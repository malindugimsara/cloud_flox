<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'include/url.inc.php';
require_once __DIR__ . '/api/whmcs.php';

$error = '';
$success = '';

// If already logged in, jump directly to clientarea
if (!empty($_SESSION['cf_client_logged_in'])) {
    header("Location: " . $url . "clientarea");
    exit;
}

// Process Registration Form Submission (Matching WHMCS Lagom2 Register Schema)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register_submit'])) {
    // 1. Personal Information
    $firstname   = trim($_POST['firstname'] ?? '');
    $lastname    = trim($_POST['lastname'] ?? '');
    $email       = trim($_POST['email'] ?? '');
    $phonenumber = trim($_POST['phonenumber'] ?? '');

    // 2. Billing Address
    $companyname = trim($_POST['companyname'] ?? '');
    $address1    = trim($_POST['address1'] ?? '');
    $address2    = trim($_POST['address2'] ?? '');
    $city        = trim($_POST['city'] ?? '');
    $state       = trim($_POST['state'] ?? '');
    $postcode    = trim($_POST['postcode'] ?? '');
    $country     = trim($_POST['country'] ?? 'LK');

    // 3. Account Security
    $password    = trim($_POST['password'] ?? '');
    $confirm     = trim($_POST['password_confirm'] ?? '');
    $agree       = !empty($_POST['agree_terms']);

    if (empty($firstname) || empty($lastname)) {
        $error = 'Please enter your first name and last name.';
    } elseif (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (empty($phonenumber)) {
        $error = 'Please enter your phone number.';
    } elseif (empty($address1)) {
        $error = 'Please enter your street address.';
    } elseif (empty($city)) {
        $error = 'Please enter your city.';
    } elseif (empty($password) || strlen($password) < 6) {
        $error = 'Password must be at least 6 characters in length.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match. Please re-enter your password.';
    } elseif (!$agree) {
        $error = 'Please agree to the CloudFlox Terms of Service to continue.';
    } else {
        $accountExists = false;
        $clientId = 0;

        // 1. Check if email already registered in local database
        try {
            $mysqli = @new mysqli('localhost', 'cfwhmcs_whmcs', '+V)Rb%K3wFiK', 'cfwhmcs_whmcs3');
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
            $error = 'An account with this email address already exists. Please <a href="' . $url . 'login" class="alert-link text-decoration-underline fw-bold">Sign In here</a>.';
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

                // Automatically log the new user in
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
    <link rel="canonical" href="https://cloudflox.com/register">

    <!-- =========== CSS Area =========== -->
    <?php include 'include/css.inc.php'; ?>
    <!-- =========== CSS Area =========== -->
</head>

<body class="pure-white-theme cf-checkout-page">

    <!-- =========== Dedicated Minimalist Auth Header Area =========== -->
    <?php include 'include/auth-header.inc.php'; ?>
    <!-- =========== Dedicated Minimalist Auth Header Area =========== -->

    <!-- =========================================================================
         2. AUTH MAIN SECTION (Minimalistic, Highly Focused)
         ========================================================================= -->
    <section class="cf-checkout-body-wrap py-4 py-md-5">
        <div class="container cf-checkout-container">

            <div class="row g-4 justify-content-center">
                
                <!-- LEFT COLUMN: REGISTRATION FORM (col-lg-7) -->
                <div class="col-12 col-lg-7">
                    <div class="cf-step-card-box">
                        
                        <!-- Mode Switcher Tabs (Short, Punchy, Focused) -->
                        <div class="cf-auth-tabs-wrap">
                            <a href="<?php echo $url; ?>login" class="cf-auth-tab-btn">
                                <i class="las la-sign-in-alt"></i> Sign In
                            </a>
                            <a href="<?php echo $url; ?>register" class="cf-auth-tab-btn active">
                                <i class="las la-user-plus"></i> Create Account
                            </a>
                        </div>

                        <!-- Card Heading -->
                        <div class="mb-4 pb-2 border-bottom">
                            <h2 class="fw-extrabold text-slate-900 mb-1.5 fs-22">Get Started in 60 Seconds</h2>
                            <p class="text-secondary fs-13.5 mb-0">Create your CloudFlox profile to deploy high-speed cloud infrastructure.</p>
                        </div>

                        <!-- Error Alert Banner -->
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger d-flex align-items-center gap-2 mb-3 rounded-3 shadow-sm py-2.5 px-3 fs-13" role="alert">
                                <i class="las la-exclamation-circle fs-18 flex-shrink-0 text-danger"></i>
                                <div><?php echo $error; ?></div>
                            </div>
                        <?php endif; ?>

                        <!-- Success Alert Banner -->
                        <?php if (!empty($success)): ?>
                            <div class="alert alert-success d-flex align-items-center gap-2 mb-3 rounded-3 shadow-sm py-2.5 px-3 fs-13" role="alert">
                                <i class="las la-check-circle fs-18 flex-shrink-0 text-success"></i>
                                <div><?php echo htmlspecialchars($success); ?></div>
                            </div>
                        <?php endif; ?>

                        <!-- Registration Form (Full WHMCS Lagom2 Schema) -->
                        <form action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>" method="POST" id="cfRegisterForm" novalidate>
                            <input type="hidden" name="register_submit" value="1">

                            <!-- 1. PERSONAL INFORMATION -->
                            <div class="cf-form-section-block">
                                <div class="cf-form-section-header-box">
                                    <div class="cf-form-section-step-num">
                                        <i class="las la-user-circle"></i>
                                    </div>
                                    <div class="cf-form-section-title-wrap">
                                        <h4>1. Personal Information</h4>
                                        <p>Your primary identity, contact email and phone number</p>
                                    </div>
                                </div>
                                <div class="row g-3.5 mb-3.5">
                                    <div class="col-sm-6">
                                        <div class="cf-minimal-input-wrap mb-0">
                                            <label for="regFirstName" class="form-label fs-13 fw-bold text-slate-800 mb-2">
                                                First Name <span class="text-danger">*</span>
                                            </label>
                                            <div class="position-relative">
                                                <i class="las la-user cf-input-icon"></i>
                                                <input 
                                                    type="text" 
                                                    class="form-control" 
                                                    id="regFirstName" 
                                                    name="firstname" 
                                                    placeholder="John" 
                                                    value="<?php echo htmlspecialchars($_POST['firstname'] ?? ''); ?>" 
                                                    required 
                                                    autofocus
                                                >
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="cf-minimal-input-wrap mb-0">
                                            <label for="regLastName" class="form-label fs-13 fw-bold text-slate-800 mb-2">
                                                Last Name <span class="text-danger">*</span>
                                            </label>
                                            <div class="position-relative">
                                                <i class="las la-user cf-input-icon"></i>
                                                <input 
                                                    type="text" 
                                                    class="form-control" 
                                                    id="regLastName" 
                                                    name="lastname" 
                                                    placeholder="Doe" 
                                                    value="<?php echo htmlspecialchars($_POST['lastname'] ?? ''); ?>" 
                                                    required
                                                >
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-3.5">
                                    <div class="col-sm-6">
                                        <div class="cf-minimal-input-wrap mb-0">
                                            <label for="regEmail" class="form-label fs-13 fw-bold text-slate-800 mb-2">
                                                Email Address <span class="text-danger">*</span>
                                            </label>
                                            <div class="position-relative">
                                                <i class="las la-envelope cf-input-icon"></i>
                                                <input 
                                                    type="email" 
                                                    class="form-control" 
                                                    id="regEmail" 
                                                    name="email" 
                                                    placeholder="name@example.com" 
                                                    value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" 
                                                    required
                                                >
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="cf-minimal-input-wrap mb-0">
                                            <label for="regPhone" class="form-label fs-13 fw-bold text-slate-800 mb-2">
                                                Phone Number <span class="text-danger">*</span>
                                            </label>
                                            <div class="position-relative">
                                                <i class="las la-phone cf-input-icon"></i>
                                                <input 
                                                    type="tel" 
                                                    class="form-control" 
                                                    id="regPhone" 
                                                    name="phonenumber" 
                                                    placeholder="+94 77 123 4567" 
                                                    value="<?php echo htmlspecialchars($_POST['phonenumber'] ?? ''); ?>" 
                                                    required
                                                >
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 2. BILLING ADDRESS -->
                            <div class="cf-form-section-block">
                                <div class="cf-form-section-header-box">
                                    <div class="cf-form-section-step-num">
                                        <i class="las la-map-marker-alt"></i>
                                    </div>
                                    <div class="cf-form-section-title-wrap">
                                        <h4>2. Billing Address</h4>
                                        <p>Business entity and verified location for cloud services &amp; invoices</p>
                                    </div>
                                </div>

                                <div class="row g-3.5 mb-3.5">
                                    <div class="col-sm-6">
                                        <div class="cf-minimal-input-wrap mb-0">
                                            <label for="regCompany" class="form-label fs-13 fw-bold text-slate-800 mb-2">
                                                Company Name <span class="text-muted fw-normal fs-11">(Optional)</span>
                                            </label>
                                            <div class="position-relative">
                                                <i class="las la-building cf-input-icon"></i>
                                                <input 
                                                    type="text" 
                                                    class="form-control" 
                                                    id="regCompany" 
                                                    name="companyname" 
                                                    placeholder="Company / Business Name" 
                                                    value="<?php echo htmlspecialchars($_POST['companyname'] ?? ''); ?>"
                                                >
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="cf-minimal-input-wrap mb-0">
                                            <label for="regCountry" class="form-label fs-13 fw-bold text-slate-800 mb-2">
                                                Country <span class="text-danger">*</span>
                                            </label>
                                            <div class="position-relative">
                                                <i class="las la-globe cf-input-icon"></i>
                                                <select class="form-select" id="regCountry" name="country" required>
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
                                                    <option value="MY" <?php echo (($_POST['country'] ?? '') === 'MY') ? 'selected' : ''; ?>>Malaysia</option>
                                                    <option value="JP" <?php echo (($_POST['country'] ?? '') === 'JP') ? 'selected' : ''; ?>>Japan</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-3.5 mb-3.5">
                                    <div class="col-sm-6">
                                        <div class="cf-minimal-input-wrap mb-0">
                                            <label for="regAddress1" class="form-label fs-13 fw-bold text-slate-800 mb-2">
                                                Street Address <span class="text-danger">*</span>
                                            </label>
                                            <div class="position-relative">
                                                <i class="las la-home cf-input-icon"></i>
                                                <input 
                                                    type="text" 
                                                    class="form-control" 
                                                    id="regAddress1" 
                                                    name="address1" 
                                                    placeholder="123 Main Street" 
                                                    value="<?php echo htmlspecialchars($_POST['address1'] ?? ''); ?>" 
                                                    required
                                                >
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="cf-minimal-input-wrap mb-0">
                                            <label for="regAddress2" class="form-label fs-13 fw-bold text-slate-800 mb-2">
                                                Street Address 2 <span class="text-muted fw-normal fs-11">(Optional)</span>
                                            </label>
                                            <div class="position-relative">
                                                <i class="las la-map-pin cf-input-icon"></i>
                                                <input 
                                                    type="text" 
                                                    class="form-control" 
                                                    id="regAddress2" 
                                                    name="address2" 
                                                    placeholder="Apt, Suite, Building" 
                                                    value="<?php echo htmlspecialchars($_POST['address2'] ?? ''); ?>"
                                                >
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-3.5">
                                    <div class="col-sm-4">
                                        <div class="cf-minimal-input-wrap mb-0">
                                            <label for="regCity" class="form-label fs-13 fw-bold text-slate-800 mb-2">
                                                City <span class="text-danger">*</span>
                                            </label>
                                            <input 
                                                type="text" 
                                                class="form-control px-3" 
                                                id="regCity" 
                                                name="city" 
                                                placeholder="Colombo" 
                                                value="<?php echo htmlspecialchars($_POST['city'] ?? ''); ?>" 
                                                required
                                            >
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="cf-minimal-input-wrap mb-0">
                                            <label for="regState" class="form-label fs-13 fw-bold text-slate-800 mb-2">
                                                State / Region
                                            </label>
                                            <input 
                                                type="text" 
                                                class="form-control px-3" 
                                                id="regState" 
                                                name="state" 
                                                placeholder="Western" 
                                                value="<?php echo htmlspecialchars($_POST['state'] ?? ''); ?>"
                                            >
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="cf-minimal-input-wrap mb-0">
                                            <label for="regPostcode" class="form-label fs-13 fw-bold text-slate-800 mb-2">
                                                Postcode / ZIP
                                            </label>
                                            <input 
                                                type="text" 
                                                class="form-control px-3" 
                                                id="regPostcode" 
                                                name="postcode" 
                                                placeholder="00100" 
                                                value="<?php echo htmlspecialchars($_POST['postcode'] ?? ''); ?>"
                                            >
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 3. ACCOUNT SECURITY -->
                            <div class="cf-form-section-block">
                                <div class="cf-form-section-header-box">
                                    <div class="cf-form-section-step-num">
                                        <i class="las la-shield-alt"></i>
                                    </div>
                                    <div class="cf-form-section-title-wrap">
                                        <h4>3. Account Security</h4>
                                        <p>Create a secure password to protect your client portal</p>
                                    </div>
                                </div>

                                <div class="row g-3.5">
                                    <div class="col-sm-6">
                                        <div class="cf-minimal-input-wrap mb-0">
                                            <label for="regPassword" class="form-label fs-13 fw-bold text-slate-800 mb-2">
                                                Password <span class="text-danger">*</span>
                                            </label>
                                            <div class="position-relative">
                                                <i class="las la-lock cf-input-icon"></i>
                                                <input 
                                                    type="password" 
                                                    class="form-control pe-5" 
                                                    id="regPassword" 
                                                    name="password" 
                                                    placeholder="Min. 6 chars" 
                                                    required
                                                >
                                                <button type="button" class="btn btn-link position-absolute end-0 top-50 translate-middle-y text-secondary text-decoration-none pe-2.5" id="toggleRegPasswordBtn" aria-label="Toggle password visibility">
                                                    <i class="las la-eye fs-18" id="toggleRegPasswordIcon"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="cf-minimal-input-wrap mb-0">
                                            <label for="regConfirm" class="form-label fs-13 fw-bold text-slate-800 mb-2">
                                                Confirm Password <span class="text-danger">*</span>
                                            </label>
                                            <div class="position-relative">
                                                <i class="las la-check-double cf-input-icon"></i>
                                                <input 
                                                    type="password" 
                                                    class="form-control" 
                                                    id="regConfirm" 
                                                    name="password_confirm" 
                                                    placeholder="Repeat password" 
                                                    required
                                                >
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Terms & Conditions Checkbox -->
                            <div class="cf-minimal-check-wrap">
                                <input class="form-check-input" type="checkbox" id="agreeTermsCheck" name="agree_terms" required checked>
                                <label class="form-check-label fs-12.5" for="agreeTermsCheck">
                                    I agree to CloudFlox <a href="<?php echo $url; ?>terms" target="_blank" class="text-primary text-decoration-none hover-underline">Terms of Service</a> &amp; <a href="<?php echo $url; ?>privacy" target="_blank" class="text-primary text-decoration-none hover-underline">Privacy Policy</a>
                                </label>
                            </div>

                            <!-- Primary Action CTA Button (Clearly Highlighted!) -->
                            <div class="mb-4">
                                <button type="submit" class="cf-minimal-cta-btn" id="btnSubmitRegister">
                                    <span class="btn-text">
                                        Create Account &amp; Deploy <i class="las la-arrow-right ms-2 fs-16"></i>
                                    </span>
                                    <span class="spinner-border spinner-border-sm d-none ms-2" role="status" aria-hidden="true"></span>
                                </button>
                            </div>

                        </form>

                        <!-- Bottom Link -->
                        <div class="text-center pt-4 border-top">
                            <span class="fs-13.5 text-secondary">
                                Already have a CloudFlox account?
                            </span>
                            <a href="<?php echo $url; ?>login" class="fs-13.5 fw-bold text-primary text-decoration-none ms-1 hover-underline">
                                Sign In &rarr;
                            </a>
                        </div>

                    </div>
                </div>

                <!-- RIGHT COLUMN: MINIMALISTIC ONBOARDING SHOWCASE (col-lg-5) -->
                <div class="col-12 col-lg-5">
                    <div class="cf-sidebar-summary-card text-center sticky-top" style="top: 88px;">
                        
                        <!-- 3D Cloud Registration Visual Showcase -->
                        <div class="cf-auth-side-img-wrap">
                            <img src="<?php echo $url ?? ''; ?>assets/imgs/banners/auth-register-portal.webp" alt="CloudFlox Registration" class="cf-auth-side-img img-fluid">
                        </div>

                        <!-- Focused Portal Title & Description -->
                        <div class="cf-auth-portal-badge badge-green">
                            <span class="badge-dot"></span>
                            <span>Instant Activation</span>
                        </div>
                        <h3 class="cf-auth-portal-title">CloudFlox Registration</h3>
                        <p class="cf-auth-portal-desc">Create your account to deploy NVMe servers, register domains &amp; scale instantly.</p>

                        <!-- 3 Sleek Trust Badges -->
                        <div class="cf-auth-trust-pills">
                            <span class="cf-auth-trust-pill">
                                <i class="las la-bolt icon-speed"></i> 60-Sec Deploy
                            </span>
                            <span class="cf-auth-trust-pill">
                                <i class="las la-lock icon-ssl"></i> Free SSL / DNS
                            </span>
                            <span class="cf-auth-trust-pill">
                                <i class="las la-shield-alt icon-sla"></i> 99.99% SLA
                            </span>
                        </div>

                        <!-- Minimal 1-Line Support Link -->
                        <div class="cf-auth-side-footer-link">
                            <span>Questions before joining?</span>
                            <a href="<?php echo $url; ?>contact-us">
                                Contact Sales &rarr;
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
            const toggleBtn = document.getElementById('toggleRegPasswordBtn');
            const passInput = document.getElementById('regPassword');
            const eyeIcon   = document.getElementById('toggleRegPasswordIcon');

            if (toggleBtn && passInput && eyeIcon) {
                toggleBtn.addEventListener('click', function() {
                    const isPass = passInput.getAttribute('type') === 'password';
                    passInput.setAttribute('type', isPass ? 'text' : 'password');
                    eyeIcon.classList.toggle('la-eye', !isPass);
                    eyeIcon.classList.toggle('la-eye-slash', isPass);
                });
            }

            // Form Submit Loading Feedback
            const regForm = document.getElementById('cfRegisterForm');
            const submitBtn = document.getElementById('btnSubmitRegister');
            if (regForm && submitBtn) {
                regForm.addEventListener('submit', function() {
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
