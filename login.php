<?php 
if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    @session_start();
}
include 'include/url.inc.php';
require_once __DIR__ . '/api/whmcs.php';

$error = '';
$success = '';

// Handle Logout action
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    unset($_SESSION['cf_client_logged_in']);
    unset($_SESSION['cf_client_id']);
    unset($_SESSION['cf_client_name']);
    unset($_SESSION['cf_client_email']);
    $success = 'You have been signed out safely. Thank you for using CloudFlox!';
}

// Handle Password Reset Success
if (isset($_GET['reset']) && $_GET['reset'] === 'success') {
    $success = 'Your password has been reset successfully! Please sign in with your new password below.';
}

// Check if user is already authenticated
$isLoggedIn = !empty($_SESSION['cf_client_logged_in']);
$clientEmail = $_SESSION['cf_client_email'] ?? '';
$clientName  = $_SESSION['cf_client_name'] ?? '';

// If already logged in and no logout request, allow direct jump to clientarea
if ($isLoggedIn && empty($_GET['action'])) {
    if (!isset($_GET['stay'])) {
        header("Location: " . $url . "clientarea");
        exit;
    }
}

// Process Login Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login_submit'])) {
    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $remember = !empty($_POST['rememberme']);

    if (empty($email) || empty($password)) {
        $error = 'Please enter both your email address and password to sign in.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        $authRes = WHMCSClient::validateLogin($email, $password);

        if (!empty($authRes['result']) && $authRes['result'] === 'success') {
            $clientId = $authRes['clientid'] ?? 1;
            $userName = '';

            // Fetch client details if possible
            $details = WHMCSClient::call('GetClientsDetails', ['clientid' => $clientId]);
            if (!empty($details['client'])) {
                $c = $details['client'];
                $userName = trim(($c['firstname'] ?? '') . ' ' . ($c['lastname'] ?? ''));
            }
            if (empty($userName)) {
                $userName = explode('@', $email)[0];
            }

            $_SESSION['cf_client_logged_in'] = true;
            $_SESSION['cf_client_id']        = $clientId;
            $_SESSION['cf_client_email']     = $email;
            $_SESSION['cf_client_name']      = $userName;

            // Handle Remember Me Cookie
            if ($remember) {
                setcookie('cf_remember_email', $email, time() + (86400 * 30), "/");
            } else {
                setcookie('cf_remember_email', '', time() - 3600, "/");
            }

            $redirect = !empty($_GET['redirect']) ? $_GET['redirect'] : 'clientarea';
            header("Location: " . $url . $redirect);
            exit;
        } else {
            $error = !empty($authRes['message']) ? $authRes['message'] : 'Invalid email or password. Please verify your credentials and try again.';
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
    <title>Sign In to Client Portal — CloudFlox Cloud Infrastructure</title>

    <!-- SEO Meta -->
    <meta name="description" content="Sign in to your CloudFlox client portal to manage cloud servers, domains, DNS zones, automated invoices, and priority support.">
    <meta name="robots" content="noindex, follow">
    <link rel="canonical" href="https://cloudflox.com/login">

    <!-- =========== CSS Area =========== -->
    <?php include 'include/css.inc.php'; ?>
    <!-- =========== CSS Area =========== -->
</head>

<body class="pure-white-theme cf-auth-page cf-checkout-page">

    <!-- =========== Dedicated Minimalist Auth Header Area =========== -->
    <?php include 'include/auth-header.inc.php'; ?>
    <!-- =========== Dedicated Minimalist Auth Header Area =========== -->

    <!-- =========================================================================
         2. AUTH MAIN SECTION (Minimalistic, Highly Focused)
         ========================================================================= -->
    <section class="cf-auth-page-wrap py-4 py-lg-5">
        <div class="container cf-auth-container">

            <div class="row g-4 g-xl-5 justify-content-center align-items-start">
                
                <!-- LEFT COLUMN: AUTH FORM (col-lg-7 col-xl-7) -->
                <div class="col-12 col-lg-7 col-xl-7">
                    <div class="cf-step-card-box cf-auth-card-box">
                        
                        <!-- Mode Switcher Tabs (Short, Punchy, Focused) -->
                        <div class="cf-auth-tabs-wrap">
                            <a href="<?php echo $url; ?>login" class="cf-auth-tab-btn active">
                                <i class="las la-sign-in-alt"></i> Sign In
                            </a>
                            <a href="<?php echo $url; ?>register" class="cf-auth-tab-btn">
                                <i class="las la-user-plus"></i> Create Account
                            </a>
                        </div>

                        <!-- Card Heading Wrap -->
                        <div class="cf-auth-heading-wrap">
                            <div class="cf-auth-badge">
                                <span class="cf-pulse-dot-blue"></span>
                                <span>Client Portal Sign In</span>
                            </div>
                            <h2 class="cf-auth-title">Welcome Back</h2>
                            <p class="cf-auth-subtitle">Enter your registered credentials below to access your cloud hosting dashboard.</p>
                        </div>

                        <!-- Error Alert Banner -->
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger d-flex align-items-center gap-2 mb-4 rounded-3 shadow-sm py-3 px-3 fs-13" role="alert">
                                <i class="las la-exclamation-circle fs-18 flex-shrink-0 text-danger"></i>
                                <div><?php echo htmlspecialchars($error); ?></div>
                            </div>
                        <?php endif; ?>

                        <!-- Success Alert Banner -->
                        <?php if (!empty($success)): ?>
                            <div class="alert alert-success d-flex align-items-center gap-2 mb-4 rounded-3 shadow-sm py-3 px-3 fs-13" role="alert">
                                <i class="las la-check-circle fs-18 flex-shrink-0 text-success"></i>
                                <div><?php echo htmlspecialchars($success); ?></div>
                            </div>
                        <?php endif; ?>

                        <!-- If already logged in, show quick switcher -->
                        <?php if ($isLoggedIn): ?>
                            <div class="p-3 rounded-3 bg-light border mb-4 text-center">
                                <p class="mb-2 fs-13 text-secondary">
                                    You are currently signed in as <strong><?php echo htmlspecialchars($clientEmail); ?></strong>.
                                </p>
                                <div class="d-flex gap-2 justify-content-center">
                                    <a href="<?php echo $url; ?>clientarea" class="btn btn-primary btn-sm rounded-pill px-3 fw-bold">
                                        <i class="las la-tachometer-alt me-1"></i> Go to Dashboard
                                    </a>
                                    <a href="<?php echo $url; ?>login?action=logout" class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-semibold">
                                        <i class="las la-sign-out-alt me-1"></i> Sign Out
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Login Form -->
                        <form action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>" method="POST" id="cfLoginForm" novalidate>
                            <input type="hidden" name="login_submit" value="1">

                            <!-- Email Field -->
                            <div class="cf-minimal-input-wrap">
                                <label for="loginEmail" class="form-label">
                                    Email Address <span class="text-danger">*</span>
                                </label>
                                <div class="position-relative">
                                    <i class="las la-envelope cf-input-icon"></i>
                                    <input 
                                        type="email" 
                                        class="form-control" 
                                        id="loginEmail" 
                                        name="email" 
                                        placeholder="name@example.com" 
                                        value="<?php echo htmlspecialchars($_POST['email'] ?? $savedEmail); ?>" 
                                        required 
                                        autofocus
                                    >
                                </div>
                            </div>

                            <!-- Password Field -->
                            <div class="cf-minimal-input-wrap">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label for="loginPassword" class="form-label mb-0">
                                        Password <span class="text-danger">*</span>
                                    </label>
                                    <a href="<?php echo $url; ?>forgot-password" class="text-primary fw-semibold text-decoration-none hover-underline" style="font-size: 13px;">
                                        Forgot password?
                                    </a>
                                </div>
                                <div class="position-relative">
                                    <i class="las la-lock cf-input-icon"></i>
                                    <input 
                                        type="password" 
                                        class="form-control pe-5" 
                                        id="loginPassword" 
                                        name="password" 
                                        placeholder="••••••••••••" 
                                        required
                                    >
                                    <button type="button" class="btn btn-link position-absolute end-0 top-50 translate-middle-y text-secondary text-decoration-none pe-3" id="togglePasswordBtn" aria-label="Toggle password visibility">
                                        <i class="las la-eye fs-18" id="togglePasswordIcon"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Remember Me Checkbox -->
                            <div class="cf-minimal-check-wrap">
                                <input class="form-check-input" type="checkbox" id="rememberMeCheck" name="rememberme" <?php echo !empty($savedEmail) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="rememberMeCheck">
                                    Remember my email on this device
                                </label>
                            </div>

                            <!-- Primary Action CTA Button (Clearly Highlighted!) -->
                            <div class="cf-auth-btn-wrap">
                                <button type="submit" class="cf-minimal-cta-btn" id="btnSubmitLogin">
                                    <span class="btn-text">
                                        Sign In to Dashboard <i class="las la-arrow-right ms-2 fs-16"></i>
                                    </span>
                                    <span class="spinner-border spinner-border-sm d-none ms-2" role="status" aria-hidden="true"></span>
                                </button>
                            </div>

                        </form>

                        <!-- Bottom Link -->
                        <div class="cf-auth-footer-link">
                            <span>New to CloudFlox?</span>
                            <a href="<?php echo $url; ?>register">
                                Create an account &rarr;
                            </a>
                        </div>

                    </div>
                </div>

                <!-- RIGHT COLUMN: MINIMALISTIC PORTAL SHOWCASE (col-lg-5 col-xl-5) -->
                <div class="col-12 col-lg-5 col-xl-5">
                    <div class="cf-sidebar-summary-card cf-auth-showcase-card text-center sticky-top" style="top: 88px;">
                        
                        <!-- 3D Cloud Portal Visual Showcase -->
                        <div class="cf-auth-side-img-wrap">
                            <img src="<?php echo $url ?? ''; ?>assets/imgs/banners/auth-login-portal.webp" alt="CloudFlox Secure Portal Login" class="cf-auth-side-img img-fluid">
                        </div>

                        <!-- Focused Portal Title & Description -->
                        <div class="cf-auth-portal-badge badge-blue">
                            <span class="badge-dot"></span>
                            <span>Client Area</span>
                        </div>
                        <h3 class="cf-auth-portal-title">CloudFlox Portal</h3>
                        <p class="cf-auth-portal-desc">Sign in to manage your active cloud servers, domains, automated invoices &amp; support tickets.</p>

                        <!-- 3 Sleek Trust Badges -->
                        <div class="cf-auth-trust-pills">
                            <span class="cf-auth-trust-pill">
                                <i class="las la-lock icon-ssl"></i> 256-Bit SSL
                            </span>
                            <span class="cf-auth-trust-pill">
                                <i class="las la-shield-alt icon-shield"></i> Verified Sign In
                            </span>
                            <span class="cf-auth-trust-pill">
                                <i class="las la-bolt icon-speed"></i> Instant Access
                            </span>
                        </div>

                        <!-- Minimal 1-Line Support Link -->
                        <div class="cf-auth-side-footer-link">
                            <span>Having trouble signing in?</span>
                            <a href="<?php echo $url; ?>contact-us">
                                Contact Support &rarr;
                            </a>
                        </div>

                    </div>
                </div>

            </div>

        </div>
    </section>

    <!-- =========== Footer Area =========== -->
    <?php 
    $hide_footer_cta = true;
    include 'include/footer.inc.php'; 
    ?>
    <!-- =========== Footer Area =========== -->

    <!-- =========== Javascript Area =========== -->
    <?php include 'include/js.inc.php'; ?>
    <!-- =========== Javascript Area =========== -->

    <script>
        // Password Visibility Toggle
        document.addEventListener('DOMContentLoaded', function() {
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

            // Form Submit Loading Feedback
            const loginForm = document.getElementById('cfLoginForm');
            const submitBtn = document.getElementById('btnSubmitLogin');
            if (loginForm && submitBtn) {
                loginForm.addEventListener('submit', function() {
                    const spinner = submitBtn.querySelector('.spinner-border');
                    if (spinner) spinner.classList.remove('d-none');
                    submitBtn.classList.add('disabled');
                    submitBtn.style.opacity = '0.85';
                });
            }
        });
    </script>
</body>

</html>
