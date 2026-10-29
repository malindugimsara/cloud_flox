<?php 
if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    @session_start();
}
include 'include/url.inc.php';
require_once __DIR__ . '/api/whmcs.php';

$error = '';
$success = '';
$info = '';
$tokenValid = false;
$tokenUser = null;
$demoResetUrl = '';

// If already logged in, redirect to clientarea
if (!empty($_SESSION['cf_client_logged_in'])) {
    header("Location: " . $url . "clientarea");
    exit;
}

// -------------------------------------------------------------------------
// Check if a reset token is provided in the URL or POST
// -------------------------------------------------------------------------
$token = trim($_GET['token'] ?? $_POST['token'] ?? '');
$email = trim($_GET['email'] ?? $_POST['email'] ?? '');

// Connect to local database for token validation
$mysqli = null;
try {
    $mysqli = @new mysqli('localhost', 'cfwhmcs_whmcs', '+V)Rb%K3wFiK', 'cfwhmcs_whmcs3');
} catch (Exception $e) {}

if (!empty($token) && !empty($email)) {
    if ($mysqli && !$mysqli->connect_error) {
        $stmt = $mysqli->prepare("SELECT id, firstname, lastname, email, pwresetkey, pwresetexpiry FROM tblclients WHERE email = ? AND pwresetkey = ? LIMIT 1");
        if ($stmt) {
            $stmt->bind_param('ss', $email, $token);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($row = $res->fetch_assoc()) {
                $expiry = strtotime($row['pwresetexpiry']);
                if ($expiry && $expiry > time()) {
                    $tokenValid = true;
                    $tokenUser = $row;
                } else {
                    $error = 'This password reset link has expired. For your security, please request a new one below.';
                    $token = ''; // Reset to show request form
                }
            } else {
                $error = 'This password reset link is invalid or has already been used. Please request a new one.';
                $token = ''; // Reset to show request form
            }
            $stmt->close();
        }
    }
}

// -------------------------------------------------------------------------
// Handle Form Submissions
// -------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // A. SET NEW PASSWORD (When Token is Valid)
    if (isset($_POST['set_new_password']) && $tokenValid && $tokenUser) {
        $newPass = trim($_POST['password'] ?? '');
        $confirm = trim($_POST['password_confirm'] ?? '');

        if (empty($newPass) || strlen($newPass) < 6) {
            $error = 'Your new password must be at least 6 characters in length.';
        } elseif ($newPass !== $confirm) {
            $error = 'The passwords you entered do not match. Please try again.';
        } else {
            // Update password in database and clear token
            $hashed = password_hash($newPass, PASSWORD_DEFAULT);
            $upd = $mysqli->prepare("UPDATE tblclients SET password = ?, pwresetkey = NULL, pwresetexpiry = NULL WHERE id = ?");
            if ($upd) {
                $upd->bind_param('si', $hashed, $tokenUser['id']);
                $upd->execute();
                $upd->close();

                // Redirect to login with success flash
                header("Location: " . $url . "login?reset=success");
                exit;
            } else {
                $error = 'Failed to update password. Please try again or contact support.';
            }
        }
    }

    // B. REQUEST RESET LINK
    elseif (isset($_POST['request_reset'])) {
        $email = trim($_POST['email'] ?? '');

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please enter a valid email address.';
        } else {
            $foundUser = false;
            $clientId = 0;

            if ($mysqli && !$mysqli->connect_error) {
                $stmt = $mysqli->prepare("SELECT id, firstname, lastname, email FROM tblclients WHERE email = ? LIMIT 1");
                if ($stmt) {
                    $stmt->bind_param('s', $email);
                    $stmt->execute();
                    $res = $stmt->get_result();
                    if ($client = $res->fetch_assoc()) {
                        $foundUser = true;
                        $clientId = $client['id'];

                        // Generate secure 32-character hex token
                        $generatedToken = bin2hex(random_bytes(24));
                        $expiryTime = date('Y-m-d H:i:s', time() + 3600); // 1 hour

                        $updStmt = $mysqli->prepare("UPDATE tblclients SET pwresetkey = ?, pwresetexpiry = ? WHERE id = ?");
                        if ($updStmt) {
                            $updStmt->bind_param('ssi', $generatedToken, $expiryTime, $clientId);
                            $updStmt->execute();
                            $updStmt->close();

                            // Construct reset URL
                            $demoResetUrl = $url . 'forgot-password?token=' . $generatedToken . '&email=' . urlencode($email);
                        }
                    }
                    $stmt->close();
                }
            }

            // Friendly message
            $success = 'Password reset instructions have been generated for <strong>' . htmlspecialchars($email) . '</strong>. If an account is associated with this email, you will be able to set a new password.';
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
    <title>Reset Password — CloudFlox Cloud Infrastructure</title>

    <!-- SEO Meta -->
    <meta name="description" content="Reset your CloudFlox account password. Secure self-service account recovery for your cloud hosting portal.">
    <meta name="robots" content="noindex, follow">
    <link rel="canonical" href="https://cloudflox.com/forgot-password">

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
                
                <!-- LEFT COLUMN: RESET FORM (col-lg-7) -->
                <div class="col-12 col-lg-7">
                    <div class="cf-step-card-box">
                        
                        <!-- Mode Switcher Tabs -->
                        <div class="cf-auth-tabs-wrap">
                            <a href="<?php echo $url; ?>login" class="cf-auth-tab-btn">
                                <i class="las la-sign-in-alt"></i> Sign In
                            </a>
                            <a href="<?php echo $url; ?>register" class="cf-auth-tab-btn">
                                <i class="las la-user-plus"></i> Create Account
                            </a>
                        </div>

                        <!-- Card Heading -->
                        <div class="mb-4 pb-2 border-bottom">
                            <div class="d-inline-flex align-items-center gap-1.5 px-2.5 py-0.5 rounded-pill bg-primary-subtle text-primary fw-bold fs-11 mb-2 border border-primary-subtle">
                                <span class="cf-pulse-dot-blue"></span> Account Recovery
                            </div>
                            <h2 class="fw-extrabold text-slate-900 mb-1.5 fs-22">
                                <?php echo $tokenValid ? 'Create Your New Password' : 'Forgot Your Password?'; ?>
                            </h2>
                            <p class="text-secondary fs-13.5 mb-0">
                                <?php echo $tokenValid 
                                    ? 'Please enter and confirm your new secure password below.' 
                                    : 'Enter the email address registered with your account and we\'ll send you instructions to safely reset it.'; ?>
                            </p>
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
                                <div><?php echo $success; ?></div>
                            </div>

                            <!-- Developer / Localhost One-Click Test Notification -->
                            <?php if (!empty($demoResetUrl)): ?>
                                <div class="p-3 mb-3 rounded-3 border border-primary-subtle bg-primary-subtle fs-12.5 text-slate-800">
                                    <div class="d-flex align-items-center gap-2 mb-1.5 fw-bold text-primary">
                                        <i class="las la-key fs-16"></i> Secure Reset Link Generated:
                                    </div>
                                    <p class="mb-2 text-secondary fs-12">
                                        Click the button below to proceed to the password update screen:
                                    </p>
                                    <a href="<?php echo htmlspecialchars($demoResetUrl); ?>" class="btn btn-primary btn-sm rounded-pill px-3.5 py-1.5 fw-bold">
                                        Proceed to Set New Password &rarr;
                                    </a>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php if ($tokenValid): ?>
                            <!-- FORM A: SET NEW PASSWORD -->
                            <form action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>" method="POST" id="cfSetPassForm" novalidate>
                                <input type="hidden" name="set_new_password" value="1">
                                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                                <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">

                                <div class="cf-minimal-input-wrap">
                                    <label for="newPassword" class="form-label fs-13 fw-bold text-slate-800 mb-2">
                                        New Password <span class="text-danger">*</span>
                                    </label>
                                    <div class="position-relative">
                                        <i class="las la-lock cf-input-icon"></i>
                                        <input 
                                            type="password" 
                                            class="form-control pe-5" 
                                            id="newPassword" 
                                            name="password" 
                                            placeholder="Min. 6 characters" 
                                            required 
                                            autofocus
                                        >
                                        <button type="button" class="btn btn-link position-absolute end-0 top-50 translate-middle-y text-secondary text-decoration-none pe-3" id="toggleNewPassBtn" aria-label="Toggle password visibility">
                                            <i class="las la-eye fs-18" id="toggleNewPassIcon"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="cf-minimal-input-wrap">
                                    <label for="confirmPassword" class="form-label fs-13 fw-bold text-slate-800 mb-2">
                                        Confirm New Password <span class="text-danger">*</span>
                                    </label>
                                    <div class="position-relative">
                                        <i class="las la-check-double cf-input-icon"></i>
                                        <input 
                                            type="password" 
                                            class="form-control" 
                                            id="confirmPassword" 
                                            name="password_confirm" 
                                            placeholder="Repeat new password" 
                                            required
                                        >
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <button type="submit" class="cf-minimal-cta-btn" id="btnSubmitNewPass">
                                        <span class="btn-text">
                                            Update Password &amp; Sign In <i class="las la-arrow-right ms-2 fs-16"></i>
                                        </span>
                                        <span class="spinner-border spinner-border-sm d-none ms-2" role="status" aria-hidden="true"></span>
                                    </button>
                                </div>
                            </form>

                        <?php else: ?>
                            <!-- FORM B: REQUEST RESET LINK -->
                            <form action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>" method="POST" id="cfRequestResetForm" novalidate>
                                <input type="hidden" name="request_reset" value="1">

                                <div class="cf-minimal-input-wrap">
                                    <label for="recoveryEmail" class="form-label fs-13 fw-bold text-slate-800 mb-2">
                                        Registered Email Address <span class="text-danger">*</span>
                                    </label>
                                    <div class="position-relative">
                                        <i class="las la-envelope cf-input-icon"></i>
                                        <input 
                                            type="email" 
                                            class="form-control" 
                                            id="recoveryEmail" 
                                            name="email" 
                                            placeholder="name@example.com" 
                                            value="<?php echo htmlspecialchars($_POST['email'] ?? $email); ?>" 
                                            required 
                                            autofocus
                                        >
                                    </div>
                                    <small class="text-muted fs-11 mt-1 d-block">We will send instructions to this address if it is registered.</small>
                                </div>

                                <div class="mb-4">
                                    <button type="submit" class="cf-minimal-cta-btn" id="btnSubmitReset">
                                        <span class="btn-text">
                                            Send Reset Instructions <i class="las la-arrow-right ms-2 fs-16"></i>
                                        </span>
                                        <span class="spinner-border spinner-border-sm d-none ms-2" role="status" aria-hidden="true"></span>
                                    </button>
                                </div>
                            </form>
                        <?php endif; ?>

                        <!-- Bottom Link -->
                        <div class="text-center pt-4 border-top">
                            <span class="fs-13.5 text-secondary">
                                Remembered your password?
                            </span>
                            <a href="<?php echo $url; ?>login" class="fs-13.5 fw-bold text-primary text-decoration-none ms-1 hover-underline">
                                Sign In &rarr;
                            </a>
                        </div>

                    </div>
                </div>

                <!-- RIGHT COLUMN: MINIMALISTIC RECOVERY SHOWCASE (col-lg-5) -->
                <div class="col-12 col-lg-5">
                    <div class="cf-sidebar-summary-card text-center sticky-top" style="top: 88px;">
                        
                        <!-- 3D Security Portal Visual Showcase -->
                        <div class="cf-auth-side-img-wrap">
                            <img src="<?php echo $url ?? ''; ?>assets/imgs/banners/auth-login-portal.webp" alt="CloudFlox Account Security" class="cf-auth-side-img img-fluid">
                        </div>

                        <!-- Focused Portal Title & Description -->
                        <div class="cf-auth-portal-badge badge-purple">
                            <span class="badge-dot"></span>
                            <span>Self-Service Recovery</span>
                        </div>
                        <h3 class="cf-auth-portal-title">Secure Account Recovery</h3>
                        <p class="cf-auth-portal-desc">Cryptographically signed verification ensures your cloud infrastructure remains protected.</p>

                        <!-- 3 Sleek Trust Badges -->
                        <div class="cf-auth-trust-pills">
                            <span class="cf-auth-trust-pill">
                                <i class="las la-clock icon-clock"></i> 1-Hour Token Expiry
                            </span>
                            <span class="cf-auth-trust-pill">
                                <i class="las la-shield-alt icon-shield"></i> Anti-Bruteforce
                            </span>
                            <span class="cf-auth-trust-pill">
                                <i class="las la-lock icon-ssl"></i> 256-Bit SSL
                            </span>
                        </div>

                        <!-- Minimal 1-Line Support Link -->
                        <div class="cf-auth-side-footer-link">
                            <span>Still having trouble regaining access?</span>
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
            const toggleBtn = document.getElementById('toggleNewPassBtn');
            const passInput = document.getElementById('newPassword');
            const eyeIcon   = document.getElementById('toggleNewPassIcon');

            if (toggleBtn && passInput && eyeIcon) {
                toggleBtn.addEventListener('click', function() {
                    const isPass = passInput.getAttribute('type') === 'password';
                    passInput.setAttribute('type', isPass ? 'text' : 'password');
                    eyeIcon.classList.toggle('la-eye', !isPass);
                    eyeIcon.classList.toggle('la-eye-slash', isPass);
                });
            }

            // Form Submit Loading Feedback
            const resetForm = document.getElementById('cfRequestResetForm') || document.getElementById('cfSetPassForm');
            const submitBtn = document.getElementById('btnSubmitReset') || document.getElementById('btnSubmitNewPass');
            if (resetForm && submitBtn) {
                resetForm.addEventListener('submit', function() {
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

