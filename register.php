<?php 
require_once __DIR__ . '/include/env.inc.php';
cf_secure_session_start();
include 'include/url.inc.php';
require_once __DIR__ . '/include/csrf.inc.php';
require_once __DIR__ . '/include/rate-limit.inc.php';
require_once __DIR__ . '/api/whmcs.php';

$error = '';
$success = '';

// Check if user is already authenticated
$isLoggedIn = !empty($_SESSION['cf_client_logged_in']);
$clientEmail = $_SESSION['cf_client_email'] ?? '';
$clientName  = $_SESSION['cf_client_name'] ?? '';

// If already logged in and not explicitly requesting to stay, redirect to clientarea
if ($isLoggedIn && !isset($_GET['stay'])) {
    header("Location: " . $url . "clientarea");
    exit;
}

// Default page mode: register.php defaults to 'signup'
$defaultMode = 'signup';
$initialMode = isset($_GET['mode']) ? (($_GET['mode'] === 'signin') ? 'signin' : 'signup') : $defaultMode;

// Process Login Form Submission (if user flipped to sign in and submitted)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login_submit'])) {
    $initialMode = 'signin';
    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $remember = !empty($_POST['rememberme']);

    if (!cf_csrf_verify($_POST['csrf_token'] ?? '')) {
        $error = 'Security token mismatch. Please refresh the page and try again.';
    } elseif (!cf_rate_check('login')) {
        $error = 'Too many sign-in attempts from your IP. Please wait 5 minutes and try again.';
    } elseif (empty($email) || empty($password)) {
        $error = 'Please enter both your email address and password to sign in.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        $authRes = WHMCSClient::validateLogin($email, $password);

        if (!empty($authRes['result']) && $authRes['result'] === 'success') {
            $clientId = $authRes['clientid'] ?? 1;
            $userName = '';

            $details = WHMCSClient::call('GetClientsDetails', ['clientid' => $clientId]);
            if (!empty($details['client'])) {
                $c = $details['client'];
                $userName = trim(($c['firstname'] ?? '') . ' ' . ($c['lastname'] ?? ''));
            }
            if (empty($userName)) {
                $userName = explode('@', $email)[0];
            }

            session_regenerate_id(true);
            cf_rate_clear('login');

            $_SESSION['cf_client_logged_in'] = true;
            $_SESSION['cf_client_id']        = $clientId;
            $_SESSION['cf_client_email']     = $email;
            $_SESSION['cf_client_name']      = $userName;

            $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);
            if ($remember) {
                setcookie('cf_remember_email', $email, [
                    'expires'  => time() + (86400 * 30),
                    'path'     => '/',
                    'secure'   => $isHttps,
                    'httponly' => true,
                    'samesite' => 'Lax'
                ]);
            } else {
                setcookie('cf_remember_email', '', [
                    'expires'  => time() - 3600,
                    'path'     => '/',
                    'secure'   => $isHttps,
                    'httponly' => true,
                    'samesite' => 'Lax'
                ]);
            }

            $redirect = !empty($_GET['redirect']) ? trim($_GET['redirect']) : 'clientarea';
            if (preg_match('#^https?://#i', $redirect) || strpos($redirect, '//') === 0 || strpos($redirect, '\\') !== false) {
                $redirect = 'clientarea';
            }
            header("Location: " . $url . ltrim($redirect, '/'));
            exit;
        } else {
            $error = !empty($authRes['message']) ? $authRes['message'] : 'Invalid email or password. Please verify your credentials and try again.';
            cf_rate_record_fail('login');
        }
    }
}

// Process Registration Form Submission (Matching WHMCS Lagom2 Register Schema)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register_submit'])) {
    $initialMode = 'signup';
    $firstname   = trim($_POST['firstname'] ?? '');
    $lastname    = trim($_POST['lastname'] ?? '');
    $email       = trim($_POST['email'] ?? '');
    $phonenumber = trim($_POST['phonenumber'] ?? '');

    $companyname = trim($_POST['companyname'] ?? '');
    $address1    = trim($_POST['address1'] ?? '');
    $address2    = trim($_POST['address2'] ?? '');
    $city        = trim($_POST['city'] ?? '');
    $state       = trim($_POST['state'] ?? '');
    $postcode    = trim($_POST['postcode'] ?? '');
    $country     = trim($_POST['country'] ?? 'LK');

    $password    = trim($_POST['password'] ?? '');
    $confirm     = trim($_POST['password_confirm'] ?? '');
    $agree       = !empty($_POST['agree_terms']);

    if (!cf_csrf_verify($_POST['csrf_token'] ?? '')) {
        $error = 'Security token mismatch. Please refresh the page and try again.';
    } elseif (!cf_rate_check('register', 5, 600)) {
        $error = 'Too many registration attempts from your IP. Please wait 10 minutes and try again.';
    } elseif (empty($firstname) || empty($lastname)) {
        $error = 'Please enter your first name and last name.';
    } elseif (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (empty($phonenumber)) {
        $error = 'Please enter your phone number.';
    } elseif (empty($address1)) {
        $error = 'Please enter your street address.';
    } elseif (empty($city)) {
        $error = 'Please enter your city.';
    } elseif (empty($password) || strlen($password) < 8) {
        $error = 'Password must be at least 8 characters in length.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match. Please re-enter your password.';
    } elseif (!$agree) {
        $error = 'Please agree to the CloudFlox Terms of Service to continue.';
    } else {
        $accountExists = false;
        $clientId = 0;

        // 1. Check if email already registered in local database
        try {
            $mysqli = @new mysqli(CF_DB_HOST, CF_DB_USER, CF_DB_PASS, CF_DB_NAME, (int)CF_DB_PORT);
            if (!$mysqli->connect_error) {
                $checkStmt = $mysqli->prepare("SELECT id FROM tblclients WHERE email = ? LIMIT 1");
                if ($checkStmt) {
                    $checkStmt->bind_param('s', $email);
                    $checkStmt->execute();
                    $checkStmt->store_result();
                    if ($checkStmt->num_rows > 0) {
                        $accountExists = true;
                    }
                    $checkStmt->close();
                }
            }
        } catch (Exception $e) {}

        if ($accountExists) {
            cf_rate_record_fail('register');
            $error = 'An account with this email address already exists. Please sign in with your credentials.';
        } else {
            // 2. Register via WHMCS Client Bridge
            $clientPayload = [
                'firstname'   => $firstname,
                'lastname'    => $lastname,
                'companyname' => $companyname,
                'email'       => $email,
                'phonenumber' => $phonenumber,
                'password'    => $password,
                'address1'    => $address1,
                'address2'    => $address2,
                'city'        => $city,
                'state'       => $state,
                'postcode'    => $postcode,
                'country'     => $country
            ];

            $createRes = WHMCSClient::addOrGetClient($clientPayload);

            if (!empty($createRes['result']) && $createRes['result'] === 'success') {
                $clientId = $createRes['clientid'] ?? rand(1001, 3999);

                // If local database is available, insert full client record into tblclients
                try {
                    if (isset($mysqli) && !$mysqli->connect_error) {
                        $hashedPass = password_hash($password, PASSWORD_DEFAULT);
                        $ins = $mysqli->prepare("INSERT INTO tblclients (firstname, lastname, companyname, email, password, phonenumber, address1, address2, city, state, postcode, country, status, datecreated) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Active', NOW())");
                        if ($ins) {
                            $ins->bind_param('ssssssssssss', $firstname, $lastname, $companyname, $email, $hashedPass, $phonenumber, $address1, $address2, $city, $state, $postcode, $country);
                            $ins->execute();
                            if ($ins->insert_id > 0) {
                                $clientId = $ins->insert_id;
                            }
                            $ins->close();
                        }
                    }
                } catch (Exception $e) {}

                session_regenerate_id(true);

                $_SESSION['cf_client_logged_in'] = true;
                $_SESSION['cf_client_id']        = $clientId;
                $_SESSION['cf_client_email']     = $email;
                $_SESSION['cf_client_name']      = $firstname . ' ' . $lastname;

                header("Location: " . $url . "clientarea");
                exit;
            } else {
                $error = !empty($createRes['message']) ? $createRes['message'] : 'Failed to create your account. Please try again or contact support.';
            }
        }
    }
}

// Prefill remembered email if available
$savedEmail = $_COOKIE['cf_remember_email'] ?? '';
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">

<head>
    <!-- Basic Meta -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Page Title -->
    <title>Create Your Account — CloudFlox Cloud Infrastructure</title>

    <!-- SEO Meta -->
    <meta name="description" content="Register for a CloudFlox client account. Deploy high-speed cloud instances, register domains, manage DNS zones, and access 24/7 expert support.">
    <meta name="robots" content="noindex, follow">
    <link rel="canonical" href="<?php echo $url; ?>register">

    <!-- =========== CSS Area =========== -->
    <?php include 'include/css.inc.php'; ?>
    <link rel="stylesheet" href="<?php echo $url; ?>assets/css/cf-auth-modern.css?v=<?php echo filemtime(__DIR__ . '/assets/css/cf-auth-modern.css'); ?>">
    <!-- =========== CSS Area =========== -->
</head>

<body class="cf-modern-auth-body">

    <!-- Preloader & CSRF Security Token Setup -->
    <script>
        window.CF_BASE_URL = <?php echo json_encode($url); ?>;
        window.CF_CSRF_TOKEN = <?php echo json_encode(cf_csrf_token()); ?>;
        (function() {
            if (window._cfFetchCsrfPatched || !window.fetch) return;
            window._cfFetchCsrfPatched = true;
            var origFetch = window.fetch.bind(window);
            window.fetch = function(input, init) {
                init = init || {};
                var urlStr = (typeof input === 'string') ? input : (input && input.url ? input.url : '');
                var isSameOrigin = !/^https?:\/\//i.test(urlStr) || urlStr.indexOf(window.location.origin) === 0;
                var method = (init.method || (input && input.method) || 'GET').toUpperCase();
                if (isSameOrigin && method !== 'GET' && method !== 'HEAD' && window.CF_CSRF_TOKEN) {
                    var headers = new Headers(init.headers || (input && input.headers) || {});
                    if (!headers.has('X-CSRF-Token')) {
                        headers.set('X-CSRF-Token', window.CF_CSRF_TOKEN);
                    }
                    init = Object.assign({}, init, {
                        headers: headers
                    });
                }
                return origFetch(input, init);
            };
        })();
    </script>

    <!-- Main Centered Authentication Wrapper -->
    <main class="cf-modern-auth-wrapper">

        <!-- Top Minimal Navigation -->
        <nav class="cf-modern-auth-topbar" aria-label="Auth Navigation">
            <a href="<?php echo $url; ?>" class="cf-modern-back-btn" title="Return to CloudFlox Homepage">
                <i class="las la-arrow-left"></i>
                <span>Back to Website</span>
            </a>
            <a href="<?php echo $url; ?>contact-us" class="cf-modern-help-link">
                <i class="las la-question-circle me-1"></i> Need Help?
            </a>
        </nav>

        <!-- Unified Dual-Panel Card: Authentic 3D Book with Center Spine -->
        <div class="cf-modern-auth-card <?php echo ($initialMode === 'signin') ? 'cf-mode-signin' : 'cf-mode-signup'; ?>" id="authCard">

            <!-- Center Book Spine Binding -->
            <div class="cf-book-spine" aria-hidden="true"></div>

            <!-- =========================================================================
                 PANEL 1: SIGN IN FORM (ON THE LEFT SIDE)
                 ========================================================================= -->
            <section class="cf-panel-signin" aria-labelledby="signinHeading">
                <div>
                    <!-- Brand Area -->
                    <a href="<?php echo $url; ?>" class="cf-modern-brand-wrap" title="CloudFlox Home">
                        <div class="cf-modern-brand-content">
                            <img 
                                src="<?php echo $url; ?>assets/imgs/logos/cloud-flox-main.webp" 
                                alt="Cloud Flox" 
                                class="cf-modern-brand-img"
                                height="40"
                                onerror="this.style.display='none'; document.getElementById('cfFallbackBrandIn').style.display='flex';"
                            >
                            <div id="cfFallbackBrandIn" style="display:none; align-items:center; gap:10px;">
                                <span style="font-size: 22px; font-weight: 800;"><span style="color: #ff6600;">Cloud</span> <span style="color: #0b2256;">Flox</span></span>
                            </div>
                            <span class="cf-modern-brand-slogan">Fast | Secure | Reliable</span>
                        </div>
                    </a>

                    <!-- Sign In Headings -->
                    <div class="cf-modern-form-heading">
                        <h2 class="cf-modern-title" id="signinHeading">Sign In</h2>
                        <p class="cf-modern-subtitle">Welcome back! Please login to your account.</p>
                    </div>

                    <!-- Alerts: Error / Success for Sign In -->
                    <?php if (!empty($error) && $initialMode === 'signin'): ?>
                        <div class="alert alert-danger d-flex align-items-center gap-2 mb-3 rounded-3 py-2 px-3 fs-13" role="alert">
                            <i class="las la-exclamation-circle fs-18 flex-shrink-0 text-danger"></i>
                            <div><?php echo htmlspecialchars($error); ?></div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($success) && $initialMode === 'signin'): ?>
                        <div class="alert alert-success d-flex align-items-center gap-2 mb-3 rounded-3 py-2 px-3 fs-13" role="alert">
                            <i class="las la-check-circle fs-18 flex-shrink-0 text-success"></i>
                            <div><?php echo htmlspecialchars($success); ?></div>
                        </div>
                    <?php endif; ?>

                    <!-- Sign In Form -->
                    <form action="<?php echo $url; ?>login" method="POST" id="cfLoginForm" novalidate>
                        <input type="hidden" name="login_submit" value="1">
                        <?php echo cf_csrf_field(); ?>

                        <!-- Email Input Group -->
                        <div class="cf-modern-input-group">
                            <i class="las la-envelope cf-input-icon"></i>
                            <input
                                type="email"
                                class="cf-modern-input"
                                id="loginEmail"
                                name="email"
                                placeholder="Email Address"
                                value="<?php echo htmlspecialchars($_POST['email'] ?? $savedEmail); ?>"
                                required>
                        </div>

                        <!-- Password Input Group -->
                        <div class="cf-modern-input-group">
                            <i class="las la-lock cf-input-icon"></i>
                            <input
                                type="password"
                                class="cf-modern-input has-toggle"
                                id="loginPassword"
                                name="password"
                                placeholder="Password"
                                required>
                            <button type="button" class="cf-toggle-password-btn" id="togglePasswordBtn" aria-label="Toggle password visibility">
                                <i class="las la-eye" id="togglePasswordIcon"></i>
                            </button>
                        </div>

                        <!-- Options: Forgot Password & Remember Me -->
                        <div class="cf-modern-options-row">
                            <a href="<?php echo $url; ?>forgot-password" class="cf-modern-forgot-link">
                                Forgot Password?
                            </a>
                            <label class="cf-modern-remember-label" for="rememberMeCheck">
                                <input
                                    type="checkbox"
                                    class="cf-modern-checkbox"
                                    id="rememberMeCheck"
                                    name="rememberme"
                                    <?php echo !empty($savedEmail) ? 'checked' : ''; ?>>
                                <span>Remember Me</span>
                            </label>
                        </div>

                        <!-- Primary CTA Submit Button with interactive arrow -->
                        <button type="submit" class="cf-modern-submit-btn" id="btnSubmitLogin">
                            <span class="btn-text">
                                Sign In
                                <i class="las la-arrow-right ms-2 fs-16 cf-btn-arrow"></i>
                            </span>
                            <span class="spinner-border spinner-border-sm d-none ms-2" role="status" aria-hidden="true"></span>
                        </button>

                        <!-- Trust / Encryption Badge -->
                        <div class="cf-modern-security-badge">
                            <i class="las la-shield-alt fs-15 text-success"></i>
                            <span>256-Bit SSL Encrypted &bull; Fast &amp; Secure Sign In</span>
                        </div>

                        <!-- Sign Up Switcher Link -->
                        <div class="cf-modern-signup-wrap">
                            <span>Don't have an account?</span>
                            <a href="<?php echo $url; ?>register" class="cf-modern-signup-link" id="triggerSignUpFlip">
                                Sign Up
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Footer -->
                <div class="cf-modern-left-footer">
                    <span class="cf-modern-copyright">&copy; <?php echo date('Y'); ?> CloudFlox LLC. All rights reserved. Engineered for modern cloud performance.</span>
                </div>
            </section>

            <!-- =========================================================================
                 PANEL 2: SIGN UP FORM (ON THE RIGHT SIDE - 3 ORGANIZED GROUPS)
                 ========================================================================= -->
            <section class="cf-panel-signup" aria-labelledby="registerHeading">
                <div>
                    <!-- Brand Area -->
                    <a href="<?php echo $url; ?>" class="cf-modern-brand-wrap" title="CloudFlox Home">
                        <div class="cf-modern-brand-content">
                            <img 
                                src="<?php echo $url; ?>assets/imgs/logos/cloud-flox-main.webp" 
                                alt="Cloud Flox" 
                                class="cf-modern-brand-img"
                                height="40"
                                onerror="this.style.display='none'; document.getElementById('cfFallbackBrandUp').style.display='flex';"
                            >
                            <div id="cfFallbackBrandUp" style="display:none; align-items:center; gap:10px;">
                                <span style="font-size: 20px; font-weight: 800;"><span style="color: #ff6600;">Cloud</span> <span style="color: #0b2256;">Flox</span></span>
                            </div>
                            <span class="cf-modern-brand-slogan">Fast | Secure | Reliable</span>
                        </div>
                    </a>

                    <!-- Sign Up Headings -->
                    <div class="cf-modern-form-heading">
                        <h1 class="cf-modern-title" id="registerHeading">Create Account</h1>
                        <p class="cf-modern-subtitle">Deploy high-speed cloud infrastructure in 60 seconds.</p>
                    </div>

                    <!-- Alerts: Error / Success for Sign Up -->
                    <?php if (!empty($error) && $initialMode === 'signup'): ?>
                        <div class="alert alert-danger d-flex align-items-center gap-2 mb-3 rounded-3 py-2 px-3 fs-13" role="alert">
                            <i class="las la-exclamation-circle fs-18 flex-shrink-0 text-danger"></i>
                            <div><?php echo $error; ?></div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($success) && $initialMode === 'signup'): ?>
                        <div class="alert alert-success d-flex align-items-center gap-2 mb-3 rounded-3 py-2 px-3 fs-13" role="alert">
                            <i class="las la-check-circle fs-18 flex-shrink-0 text-success"></i>
                            <div><?php echo htmlspecialchars($success); ?></div>
                        </div>
                    <?php endif; ?>

                    <!-- Registration Form -->
                    <form action="<?php echo $url; ?>register" method="POST" id="cfRegisterForm" novalidate>
                        <input type="hidden" name="register_submit" value="1">
                        <?php echo cf_csrf_field(); ?>

                        <!-- GROUP 1: PERSONAL DETAILS -->
                        <div class="cf-form-section-card">
                            <div class="cf-section-header">
                                <div class="cf-section-badge">
                                    <i class="las la-user"></i>
                                </div>
                                <div class="cf-section-header-meta">
                                    <span class="cf-section-title">1. Personal Profile</span>
                                    <span class="cf-section-subtitle">Your identity and primary contact details</span>
                                </div>
                                <span class="cf-section-step">Step 1</span>
                            </div>

                            <div class="cf-modern-grid-2">
                                <div class="cf-modern-input-group mb-compact">
                                    <i class="las la-user cf-input-icon"></i>
                                    <input
                                        type="text"
                                        class="cf-modern-input cf-input-sm"
                                        id="regFirstName"
                                        name="firstname"
                                        placeholder="First Name *"
                                        value="<?php echo htmlspecialchars($_POST['firstname'] ?? ''); ?>"
                                        required
                                        autofocus>
                                </div>
                                <div class="cf-modern-input-group mb-compact">
                                    <i class="las la-user cf-input-icon"></i>
                                    <input
                                        type="text"
                                        class="cf-modern-input cf-input-sm"
                                        id="regLastName"
                                        name="lastname"
                                        placeholder="Last Name *"
                                        value="<?php echo htmlspecialchars($_POST['lastname'] ?? ''); ?>"
                                        required>
                                </div>
                            </div>

                            <div class="cf-modern-grid-2">
                                <div class="cf-modern-input-group mb-compact">
                                    <i class="las la-envelope cf-input-icon"></i>
                                    <input
                                        type="email"
                                        class="cf-modern-input cf-input-sm"
                                        id="regEmail"
                                        name="email"
                                        placeholder="Email Address *"
                                        value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                                        required>
                                </div>
                                <div class="cf-modern-input-group mb-compact">
                                    <i class="las la-phone cf-input-icon"></i>
                                    <input
                                        type="tel"
                                        class="cf-modern-input cf-input-sm"
                                        id="regPhone"
                                        name="phonenumber"
                                        placeholder="Phone Number *"
                                        value="<?php echo htmlspecialchars($_POST['phonenumber'] ?? ''); ?>"
                                        required>
                                </div>
                            </div>
                        </div>

                        <!-- GROUP 2: BILLING & LOCATION -->
                        <div class="cf-form-section-card">
                            <div class="cf-section-header">
                                <div class="cf-section-badge">
                                    <i class="las la-map-marker-alt"></i>
                                </div>
                                <div class="cf-section-header-meta">
                                    <span class="cf-section-title">2. Billing &amp; Location</span>
                                    <span class="cf-section-subtitle">Address details for automated invoicing</span>
                                </div>
                                <span class="cf-section-step">Step 2</span>
                            </div>

                            <div class="cf-modern-grid-2">
                                <div class="cf-modern-input-group mb-compact">
                                    <i class="las la-home cf-input-icon"></i>
                                    <input
                                        type="text"
                                        class="cf-modern-input cf-input-sm"
                                        id="regAddress1"
                                        name="address1"
                                        placeholder="Street Address *"
                                        value="<?php echo htmlspecialchars($_POST['address1'] ?? ''); ?>"
                                        required>
                                </div>
                                <div class="cf-modern-input-group mb-compact">
                                    <i class="las la-city cf-input-icon"></i>
                                    <input
                                        type="text"
                                        class="cf-modern-input cf-input-sm"
                                        id="regCity"
                                        name="city"
                                        placeholder="City *"
                                        value="<?php echo htmlspecialchars($_POST['city'] ?? ''); ?>"
                                        required>
                                </div>
                            </div>

                            <div class="cf-modern-grid-2">
                                <div class="cf-modern-input-group mb-compact">
                                    <i class="las la-globe cf-input-icon"></i>
                                    <select class="cf-modern-input cf-input-sm" id="regCountry" name="country" required>
                                        <option value="LK" <?php echo (($_POST['country'] ?? 'LK') === 'LK') ? 'selected' : ''; ?>>Sri Lanka</option>
                                        <option value="US" <?php echo (($_POST['country'] ?? '') === 'US') ? 'selected' : ''; ?>>United States</option>
                                        <option value="GB" <?php echo (($_POST['country'] ?? '') === 'GB') ? 'selected' : ''; ?>>United Kingdom</option>
                                        <option value="AU" <?php echo (($_POST['country'] ?? '') === 'AU') ? 'selected' : ''; ?>>Australia</option>
                                        <option value="CA" <?php echo (($_POST['country'] ?? '') === 'CA') ? 'selected' : ''; ?>>Canada</option>
                                        <option value="IN" <?php echo (($_POST['country'] ?? '') === 'IN') ? 'selected' : ''; ?>>India</option>
                                        <option value="SG" <?php echo (($_POST['country'] ?? '') === 'SG') ? 'selected' : ''; ?>>Singapore</option>
                                        <option value="AE" <?php echo (($_POST['country'] ?? '') === 'AE') ? 'selected' : ''; ?>>United Arab Emirates</option>
                                        <option value="DE" <?php echo (($_POST['country'] ?? '') === 'DE') ? 'selected' : ''; ?>>Germany</option>
                                        <option value="FR" <?php echo (($_POST['country'] ?? '') === 'FR') ? 'selected' : ''; ?>>France</option>
                                    </select>
                                </div>
                                <div class="cf-modern-input-group mb-compact">
                                    <i class="las la-map-pin cf-input-icon"></i>
                                    <input
                                        type="text"
                                        class="cf-modern-input cf-input-sm"
                                        id="regState"
                                        name="state"
                                        placeholder="State / Region (Optional)"
                                        value="<?php echo htmlspecialchars($_POST['state'] ?? ''); ?>">
                                </div>
                            </div>
                        </div>

                        <!-- GROUP 3: SECURITY CREDENTIALS -->
                        <div class="cf-form-section-card">
                            <div class="cf-section-header">
                                <div class="cf-section-badge">
                                    <i class="las la-shield-alt"></i>
                                </div>
                                <div class="cf-section-header-meta">
                                    <span class="cf-section-title">3. Security Credentials</span>
                                    <span class="cf-section-subtitle">Create a strong password to protect your servers</span>
                                </div>
                                <span class="cf-section-step">Step 3</span>
                            </div>

                            <div class="cf-modern-grid-2">
                                <div class="cf-modern-input-group mb-compact">
                                    <i class="las la-lock cf-input-icon"></i>
                                    <input
                                        type="password"
                                        class="cf-modern-input cf-input-sm has-toggle"
                                        id="regPassword"
                                        name="password"
                                        placeholder="Password (Min. 8 chars) *"
                                        required>
                                    <button type="button" class="cf-toggle-password-btn" id="toggleRegPasswordBtn" aria-label="Toggle password visibility">
                                        <i class="las la-eye" id="toggleRegPasswordIcon"></i>
                                    </button>
                                </div>
                                <div class="cf-modern-input-group mb-compact">
                                    <i class="las la-check-double cf-input-icon"></i>
                                    <input
                                        type="password"
                                        class="cf-modern-input cf-input-sm"
                                        id="regConfirm"
                                        name="password_confirm"
                                        placeholder="Confirm Password *"
                                        required>
                                </div>
                            </div>

                            <!-- Live Password Strength Meter -->
                            <div class="cf-strength-wrap">
                                <div class="cf-strength-bar">
                                    <div class="cf-strength-fill" id="passStrengthFill"></div>
                                </div>
                                <div class="cf-strength-info">
                                    <span id="passStrengthText">Password strength</span>
                                    <span>Min. 8 characters</span>
                                </div>
                            </div>
                        </div>

                        <!-- Terms & Conditions Checkbox -->
                        <div class="cf-modern-terms-wrap">
                            <input class="cf-modern-checkbox" type="checkbox" id="agreeTermsCheck" name="agree_terms" required checked>
                            <label for="agreeTermsCheck" class="m-0">
                                I agree to the CloudFlox <a href="<?php echo $url; ?>terms-of-service" target="_blank">Terms of Service</a> &amp; <a href="<?php echo $url; ?>privacy-policy" target="_blank">Privacy Policy</a>
                            </label>
                        </div>

                        <?php if (defined('CF_TURNSTILE_SITE_KEY') && CF_TURNSTILE_SITE_KEY !== ''): ?>
                        <div class="d-flex justify-content-center mb-2">
                            <div class="cf-turnstile" data-sitekey="<?php echo htmlspecialchars(CF_TURNSTILE_SITE_KEY); ?>" data-theme="light"></div>
                        </div>
                        <?php endif; ?>

                        <!-- Submit Button -->
                        <button type="submit" class="cf-modern-submit-btn" id="btnSubmitRegister">
                            <span class="btn-text">
                                Create Account &amp; Deploy
                                <i class="las la-arrow-right ms-2 fs-16 cf-btn-arrow"></i>
                            </span>
                            <span class="spinner-border spinner-border-sm d-none ms-2" role="status" aria-hidden="true"></span>
                        </button>

                        <!-- Trust Badge -->
                        <div class="cf-modern-security-badge">
                            <i class="las la-shield-alt fs-15 text-success"></i>
                            <span>256-Bit SSL Encrypted &bull; 99.99% Uptime Guarantee</span>
                        </div>

                        <!-- Sign In Switcher Link -->
                        <div class="cf-modern-signup-wrap">
                            <span>Already have an account?</span>
                            <a href="<?php echo $url; ?>login" class="cf-modern-signin-link" id="triggerSignInFlip">
                                Sign In
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Footer -->
                <div class="cf-modern-left-footer">
                    <span class="cf-modern-copyright">&copy; <?php echo date('Y'); ?> CloudFlox LLC. All rights reserved. Engineered for modern cloud performance.</span>
                </div>
            </section>

            <!-- =========================================================================
                 THE 3D TURNING BOOK PAGE (LEAF)
                 Pivots on the center spine like a real physical hardcover book!
                 ========================================================================= -->
            <div class="cf-book-leaf" id="bookLeaf" aria-label="Cloud Infrastructure Showcase">

                <!-- FRONT FACE (Visible when page lies on the RIGHT half - Sign In Mode) -->
                <div class="cf-leaf-face cf-leaf-front">
                    <!-- Headline -->
                    <div class="cf-modern-hero-headline">
                        <div class="cf-hero-tag-badge">
                            <span class="cf-hero-tag-dot"></span>
                            <i class="las la-bolt cf-hero-tag-icon"></i>
                            <span>Enterprise Cloud Platform</span>
                        </div>
                        <h2 class="cf-hero-title">
                            <span class="cf-title-line-1">Next-Gen <span class="cf-title-gradient">Cloud Hosting</span></span>
                            <span class="cf-title-line-2">&amp; Domains<span class="cf-title-dot">.</span></span>
                        </h2>
                        <p class="cf-hero-subtitle">
                            High-performance NVMe virtual machines, instant domain provisioning, and ultra-reliable cloud networking.
                        </p>
                    </div>

                    <!-- Center Spacer -->
                    <div class="cf-modern-hero-spacer"></div>

                    <!-- Bottom 3 Frosted Glass Feature Pills -->
                    <div class="cf-modern-pills-row">
                        <div class="cf-modern-glass-pill" title="Ultra-fast NVMe enterprise solid state drives">
                            <div class="cf-modern-pill-icon">
                                <i class="las la-tachometer-alt"></i>
                            </div>
                            <div class="cf-modern-pill-text">
                                High-Speed<br>NVMe Storage
                            </div>
                        </div>

                        <div class="cf-modern-glass-pill" title="Instant registration with DNS management">
                            <div class="cf-modern-pill-icon">
                                <i class="las la-globe"></i>
                            </div>
                            <div class="cf-modern-pill-text">
                                Instant Domain<br>Registration
                            </div>
                        </div>

                        <div class="cf-modern-glass-pill" title="24/7/365 dedicated technical support engineering">
                            <div class="cf-modern-pill-icon">
                                <i class="las la-headset"></i>
                            </div>
                            <div class="cf-modern-pill-text">
                                24/7 Expert<br>Support
                            </div>
                        </div>
                    </div>

                    <!-- Dynamic Sheen Overlay -->
                    <div class="cf-leaf-lighting"></div>
                </div>

                <!-- BACK FACE (Visible when page turns and lies on the LEFT half - Sign Up Mode) -->
                <div class="cf-leaf-face cf-leaf-back">
                    <!-- Headline -->
                    <div class="cf-modern-hero-headline">
                        <div class="cf-hero-tag-badge">
                            <span class="cf-hero-tag-dot"></span>
                            <i class="las la-bolt cf-hero-tag-icon"></i>
                            <span>Enterprise Cloud Platform</span>
                        </div>
                        <h2 class="cf-hero-title">
                            <span class="cf-title-line-1">Next-Gen <span class="cf-title-gradient">Cloud Hosting</span></span>
                            <span class="cf-title-line-2">&amp; Domains<span class="cf-title-dot">.</span></span>
                        </h2>
                        <p class="cf-hero-subtitle">
                            High-performance NVMe virtual machines, instant domain provisioning, and ultra-reliable cloud networking.
                        </p>
                    </div>

                    <!-- Center Spacer -->
                    <div class="cf-modern-hero-spacer"></div>

                    <!-- Bottom 3 Frosted Glass Feature Pills -->
                    <div class="cf-modern-pills-row">
                        <div class="cf-modern-glass-pill" title="Ultra-fast NVMe enterprise solid state drives">
                            <div class="cf-modern-pill-icon">
                                <i class="las la-tachometer-alt"></i>
                            </div>
                            <div class="cf-modern-pill-text">
                                High-Speed<br>NVMe Storage
                            </div>
                        </div>

                        <div class="cf-modern-glass-pill" title="Instant registration with DNS management">
                            <div class="cf-modern-pill-icon">
                                <i class="las la-globe"></i>
                            </div>
                            <div class="cf-modern-pill-text">
                                Instant Domain<br>Registration
                            </div>
                        </div>

                        <div class="cf-modern-glass-pill" title="24/7/365 dedicated technical support engineering">
                            <div class="cf-modern-pill-icon">
                                <i class="las la-headset"></i>
                            </div>
                            <div class="cf-modern-pill-text">
                                24/7 Expert<br>Support
                            </div>
                        </div>
                    </div>

                    <!-- Dynamic Sheen Overlay -->
                    <div class="cf-leaf-lighting"></div>
                </div>

            </div>

        </div>
    </main>

    <!-- =========== Javascript Area =========== -->
    <?php include 'include/js.inc.php'; ?>
    <!-- =========== Javascript Area =========== -->

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Trigger toast notification on server-side error or success
            <?php if (!empty($error)): ?>
                if (window.cfToast) {
                    cfToast.error(<?php echo json_encode($error); ?>, 'Authentication Notice');
                }
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                if (window.cfToast) {
                    cfToast.success(<?php echo json_encode($success); ?>, 'Success');
                }
            <?php endif; ?>

            // Password Visibility Toggle for Sign In
            const toggleBtn = document.getElementById('togglePasswordBtn');
            const passInput = document.getElementById('loginPassword');
            const eyeIcon   = document.getElementById('togglePasswordIcon');

            if (toggleBtn && passInput && eyeIcon) {
                toggleBtn.addEventListener('click', function() {
                    const isPass = passInput.getAttribute('type') === 'password';
                    passInput.setAttribute('type', isPass ? 'text' : 'password');
                    eyeIcon.classList.toggle('la-eye', !isPass);
                    eyeIcon.classList.toggle('la-eye-slash', isPass);
                });
            }

            // Password Visibility Toggle for Sign Up
            const toggleRegBtn = document.getElementById('toggleRegPasswordBtn');
            const passRegInput = document.getElementById('regPassword');
            const eyeRegIcon   = document.getElementById('toggleRegPasswordIcon');

            if (toggleRegBtn && passRegInput && eyeRegIcon) {
                toggleRegBtn.addEventListener('click', function() {
                    const isPass = passRegInput.getAttribute('type') === 'password';
                    passRegInput.setAttribute('type', isPass ? 'text' : 'password');
                    eyeRegIcon.classList.toggle('la-eye', !isPass);
                    eyeRegIcon.classList.toggle('la-eye-slash', isPass);
                });
            }

            // Live Password Strength Meter
            const strengthFill = document.getElementById('passStrengthFill');
            const strengthText = document.getElementById('passStrengthText');

            if (passRegInput && strengthFill && strengthText) {
                passRegInput.addEventListener('input', function() {
                    const val = this.value;
                    let score = 0;
                    if (val.length >= 8) score++;
                    if (/[A-Z]/.test(val)) score++;
                    if (/[0-9]/.test(val)) score++;
                    if (/[^A-Za-z0-9]/.test(val)) score++;

                    if (!val) {
                        strengthFill.style.width = '0%';
                        strengthText.textContent = 'Password strength';
                        strengthText.style.color = '#94a3b8';
                    } else if (val.length < 8) {
                        strengthFill.style.width = '25%';
                        strengthFill.style.backgroundColor = '#ef4444';
                        strengthText.textContent = 'Too Short (Min. 8 chars)';
                        strengthText.style.color = '#ef4444';
                    } else if (score <= 2) {
                        strengthFill.style.width = '50%';
                        strengthFill.style.backgroundColor = '#f97316';
                        strengthText.textContent = 'Fair';
                        strengthText.style.color = '#f97316';
                    } else if (score === 3) {
                        strengthFill.style.width = '75%';
                        strengthFill.style.backgroundColor = '#eab308';
                        strengthText.textContent = 'Good';
                        strengthText.style.color = '#eab308';
                    } else {
                        strengthFill.style.width = '100%';
                        strengthFill.style.backgroundColor = '#10b981';
                        strengthText.textContent = 'Strong & Secure';
                        strengthText.style.color = '#10b981';
                    }
                });
            }

            // =================================================================
            // TRUE PHYSICAL 3D BOOK PAGE TURN (PIVOTED ON CENTER SPINE)
            // =================================================================
            const authCard      = document.getElementById('authCard');
            const triggerSignUp = document.getElementById('triggerSignUpFlip');
            const triggerSignIn = document.getElementById('triggerSignInFlip');
            let isFlipping      = false;

            function flipToSignUp(e) {
                if (e && e.preventDefault) e.preventDefault();
                if (isFlipping || authCard.classList.contains('cf-mode-signup')) return;
                isFlipping = true;

                // Turn page from Right to Left (hinged at center spine)
                authCard.classList.remove('cf-flipping-to-signin');
                authCard.classList.add('cf-flipping-to-signup');

                setTimeout(function() {
                    authCard.classList.remove('cf-mode-signin');
                    authCard.classList.add('cf-mode-signup');
                    authCard.classList.remove('cf-flipping-to-signup');
                    isFlipping = false;
                }, 750);

                if (window.history && window.history.pushState) {
                    window.history.pushState({ mode: 'signup' }, 'Create Account', '<?php echo $url; ?>register');
                }
                document.title = 'Create Your Account — CloudFlox Cloud Infrastructure';
            }

            function flipToSignIn(e) {
                if (e && e.preventDefault) e.preventDefault();
                if (isFlipping || authCard.classList.contains('cf-mode-signin')) return;
                isFlipping = true;

                // Turn page from Left to Right (hinged at center spine)
                authCard.classList.remove('cf-flipping-to-signup');
                authCard.classList.add('cf-flipping-to-signin');

                setTimeout(function() {
                    authCard.classList.remove('cf-mode-signup');
                    authCard.classList.add('cf-mode-signin');
                    authCard.classList.remove('cf-flipping-to-signin');
                    isFlipping = false;
                }, 750);

                if (window.history && window.history.pushState) {
                    window.history.pushState({ mode: 'signin' }, 'Sign In', '<?php echo $url; ?>login');
                }
                document.title = 'Sign In — CloudFlox Cloud Infrastructure';
            }

            if (triggerSignUp) triggerSignUp.addEventListener('click', flipToSignUp);
            if (triggerSignIn) triggerSignIn.addEventListener('click', flipToSignIn);

            // Handle browser Back / Forward buttons
            window.addEventListener('popstate', function(e) {
                const path = window.location.pathname;
                if (path.indexOf('register') !== -1 || (e.state && e.state.mode === 'signup')) {
                    flipToSignUp();
                } else {
                    flipToSignIn();
                }
            });

            // Client-side Validation: Sign In Form
            const loginForm      = document.getElementById('cfLoginForm');
            const submitLoginBtn = document.getElementById('btnSubmitLogin');
            const emailInput     = document.getElementById('loginEmail');

            if (loginForm && submitLoginBtn) {
                loginForm.addEventListener('submit', function(e) {
                    const emailVal = emailInput ? emailInput.value.trim() : '';
                    const passVal  = passInput ? passInput.value.trim() : '';

                    if (!emailVal) {
                        e.preventDefault();
                        if (window.cfToast) cfToast.highlightField(emailInput, 'Please enter your email address to sign in.', 'Email Required');
                        return false;
                    }

                    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailPattern.test(emailVal)) {
                        e.preventDefault();
                        if (window.cfToast) cfToast.highlightField(emailInput, 'Please enter a valid email address.', 'Invalid Email');
                        return false;
                    }

                    if (!passVal) {
                        e.preventDefault();
                        if (window.cfToast) cfToast.highlightField(passInput, 'Please enter your account password to sign in.', 'Password Required');
                        return false;
                    }

                    const spinner = submitLoginBtn.querySelector('.spinner-border');
                    if (spinner) spinner.classList.remove('d-none');
                    submitLoginBtn.classList.add('disabled');
                    submitLoginBtn.style.opacity = '0.85';
                });
            }

            // Client-side Validation: Sign Up Form
            const regForm       = document.getElementById('cfRegisterForm');
            const submitRegBtn  = document.getElementById('btnSubmitRegister');
            const fNameInput    = document.getElementById('regFirstName');
            const lNameInput    = document.getElementById('regLastName');
            const regMailInput  = document.getElementById('regEmail');
            const regPhoneInput = document.getElementById('regPhone');
            const regAddrInput  = document.getElementById('regAddress1');
            const regCityInput  = document.getElementById('regCity');
            const regConfInput  = document.getElementById('regConfirm');
            const agreeCheck    = document.getElementById('agreeTermsCheck');

            if (regForm && submitRegBtn) {
                regForm.addEventListener('submit', function(e) {
                    if (fNameInput && !fNameInput.value.trim()) {
                        e.preventDefault();
                        if (window.cfToast) cfToast.highlightField(fNameInput, 'Please enter your first name.', 'First Name Required');
                        return false;
                    }

                    if (lNameInput && !lNameInput.value.trim()) {
                        e.preventDefault();
                        if (window.cfToast) cfToast.highlightField(lNameInput, 'Please enter your last name.', 'Last Name Required');
                        return false;
                    }

                    const regEmailVal = regMailInput ? regMailInput.value.trim() : '';
                    if (!regEmailVal) {
                        e.preventDefault();
                        if (window.cfToast) cfToast.highlightField(regMailInput, 'Please enter your email address.', 'Email Required');
                        return false;
                    }

                    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailPattern.test(regEmailVal)) {
                        e.preventDefault();
                        if (window.cfToast) cfToast.highlightField(regMailInput, 'Please enter a valid email address.', 'Invalid Email');
                        return false;
                    }

                    if (regPhoneInput && !regPhoneInput.value.trim()) {
                        e.preventDefault();
                        if (window.cfToast) cfToast.highlightField(regPhoneInput, 'Please enter your phone number.', 'Phone Required');
                        return false;
                        }

                    if (regAddrInput && !regAddrInput.value.trim()) {
                        e.preventDefault();
                        if (window.cfToast) cfToast.highlightField(regAddrInput, 'Please enter your street address.', 'Address Required');
                        return false;
                    }

                    if (regCityInput && !regCityInput.value.trim()) {
                        e.preventDefault();
                        if (window.cfToast) cfToast.highlightField(regCityInput, 'Please enter your city.', 'City Required');
                        return false;
                    }

                    const passVal = passRegInput ? passRegInput.value.trim() : '';
                    if (!passVal || passVal.length < 8) {
                        e.preventDefault();
                        if (window.cfToast) cfToast.highlightField(passRegInput, 'Password must be at least 8 characters long.', 'Password Required');
                        return false;
                    }

                    const confirmVal = regConfInput ? regConfInput.value.trim() : '';
                    if (passVal !== confirmVal) {
                        e.preventDefault();
                        if (window.cfToast) cfToast.highlightField(regConfInput, 'Passwords do not match.', 'Password Mismatch');
                        return false;
                    }

                    if (agreeCheck && !agreeCheck.checked) {
                        e.preventDefault();
                        if (window.cfToast) cfToast.warning('Please accept the Terms of Service to continue.', 'Terms Required');
                        return false;
                    }

                    const spinner = submitRegBtn.querySelector('.spinner-border');
                    if (spinner) spinner.classList.remove('d-none');
                    submitRegBtn.classList.add('disabled');
                    submitRegBtn.style.opacity = '0.85';
                });
            }
        });
    </script>
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
</body>

</html>
