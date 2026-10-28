<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'include/url.inc.php'; 
$isLoggedIn = !empty($_SESSION['cf_client_logged_in']);
$loggedInClientId = $_SESSION['cf_client_id'] ?? 0;
$loggedInClientName = $_SESSION['cf_client_name'] ?? '';
$loggedInClientEmail = $_SESSION['cf_client_email'] ?? '';
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">

<head>
    <!-- Basic Meta -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Page Title -->
    <title>Secure Checkout — CloudFlox Cloud Infrastructure &amp; Domains</title>

    <!-- SEO Meta -->
    <meta name="description" content="Complete your CloudFlox order. Instant server deployment, high-speed NVMe cloud hosting, and authoritative domain registrations.">
    <meta name="robots" content="noindex, follow">
    <link rel="canonical" href="https://cloudflox.com/checkout">

    <!-- =========== CSS Area =========== -->
    <?php include 'include/css.inc.php'; ?>
    <!-- =========== CSS Area =========== -->
</head>

<body class="pure-white-theme cf-checkout-page">

    <!-- =========== Header Area =========== -->
    <?php include 'include/header.inc.php'; ?>
    <!-- =========== Header Area =========== -->

    <!-- =========================================================================
         1. COMPACT TOP HEADER STRIP (Clean & Minimalist)
         ========================================================================= -->
    <!-- <section class="cf-checkout-top-strip">
        <div class="container cf-checkout-container">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-3">
                    <a href="index" class="cf-back-link">
                        <i class="las la-arrow-left me-1"></i> Return
                    </a>
                    <span class="cf-pipe-divider"></span>
                    <h1 class="cf-checkout-page-title mb-0">Secure Checkout</h1>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="cf-safe-badge"><i class="las la-lock text-emerald me-1"></i> 256-Bit SSL</span>
                    <span class="cf-safe-badge"><i class="las la-shield-alt text-primary me-1"></i> 30-Day Guarantee</span>
                </div>
            </div>
        </div>
    </section> -->

    <!-- =========================================================================
         2. CHECKOUT MAIN CONTAINER (Step-by-Step Flow)
         ========================================================================= -->
    <section class="cf-checkout-body-wrap">
        <div class="container cf-checkout-container">

            <!-- Three Steps Progress Indicator -->
            <div class="cf-checkout-steps-bar" role="navigation" aria-label="Checkout Progress">
                <div class="cf-step-pill active" id="pstep1" onclick="pageGoToStep(1)">
                    <span class="cf-step-num">1</span>
                    <span class="cf-step-label">Review Order</span>
                </div>
                <div class="cf-step-connector"></div>
                <div class="cf-step-pill" id="pstep2" onclick="pageGoToStep(2)">
                    <span class="cf-step-num">2</span>
                    <span class="cf-step-label">Account Details</span>
                </div>
                <div class="cf-step-connector"></div>
                <div class="cf-step-pill" id="pstep3" onclick="pageGoToStep(3)">
                    <span class="cf-step-num">3</span>
                    <span class="cf-step-label">Payment</span>
                </div>
            </div>

            <!-- Alert Banner -->
            <div class="alert alert-danger d-none mb-4 rounded-3 shadow-sm" id="pageAlertBox" role="alert">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <i class="las la-exclamation-triangle fs-18"></i>
                        <span id="pageAlertText">Please fill in all required fields.</span>
                    </div>
                    <button type="button" class="btn-close" onclick="hideAlert()" aria-label="Close"></button>
                </div>
            </div>

            <!-- MAIN CHECKOUT GRID -->
            <div id="checkoutMainGrid">
                <form id="cfPageCheckoutForm" onsubmit="return false;" novalidate>
                    <!-- Hidden WHMCS form parameters -->
                    <input type="hidden" id="pagePid" name="pid" value="">
                    <input type="hidden" id="pageCycle" name="billingcycle" value="monthly">
                    <input type="hidden" id="pageDomainType" name="domain_type" value="register">
                    <input type="hidden" id="pageDomain" name="domain" value="">
                    <input type="hidden" id="pageDomainTld" name="domain_tld" value="">
                    <input type="hidden" id="pageAccountAction" name="account_action" value="register">

                    <div class="row g-4">
                        
                        <!-- LEFT COLUMN: STEP BY STEP PANES -->
                        <div class="col-12 col-lg-8">

                            <!-- =========================================================================
                                 STEP 1: REVIEW ORDER & CONFIGURE HOSTING
                                 ========================================================================= -->
                            <div class="cf-checkout-card" id="stepBox1">
                                <div class="cf-card-header">
                                    <div>
                                        <h4 class="cf-card-title"><i class="las la-shopping-bag text-primary me-2"></i>Step 1: Review Your Order</h4>
                                        <p class="text-secondary fs-13 mb-0">Review your selected items and configure optional services.</p>
                                    </div>
                                    <span class="cf-card-badge-secure"><i class="las la-check-circle text-emerald me-1"></i>Verified Rate</span>
                                </div>

                                <!-- Domain Item Preview Row -->
                                <div class="cf-item-preview-row" id="pageDomainItemRow" style="display: none;">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="cf-item-icon-box bg-blue-subtle text-primary">
                                            <i class="las la-globe fs-22"></i>
                                        </div>
                                        <div>
                                            <div class="d-flex align-items-center gap-2 mb-0.5">
                                                <h5 class="fw-bold text-slate-900 mb-0 fs-16" id="pageDomainNameText">yourdomain.com</h5>
                                                <span class="cf-badge-tag cf-badge-tag-primary">Domain</span>
                                            </div>
                                            <span class="text-secondary fs-12">1 Year • Free Privacy Protection &amp; Anycast DNS</span>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <strong class="fs-18 text-slate-900" id="pageDomainPriceText">$13.57</strong>
                                        <small class="text-secondary d-block fs-11">/1st yr</small>
                                    </div>
                                </div>

                                <!-- Standalone Hosting Item Preview Row (When ordered without domain) -->
                                <div class="cf-item-preview-row" id="pageHostingItemRow" style="display: none;">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="cf-item-icon-box bg-emerald-subtle text-emerald">
                                            <i class="las la-server fs-22"></i>
                                        </div>
                                        <div>
                                            <div class="d-flex align-items-center gap-2 mb-0.5">
                                                <h5 class="fw-bold text-slate-900 mb-0 fs-16" id="pageHostingNameText">Business Starter</h5>
                                                <span class="cf-badge-tag cf-badge-tag-emerald" id="pageHostingBadgeText">WEB HOSTING</span>
                                            </div>
                                            <span class="text-secondary fs-12" id="pageHostingSpecsText">20 GB Pure NVMe Storage • 1 vCPU • 2 GB RAM</span>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <strong class="fs-18 text-primary" id="pageHostingPriceText">$7.50</strong>
                                        <small class="text-secondary d-block fs-11" id="pageHostingCycleLabelText">/month</small>
                                    </div>
                                </div>

                                <!-- Optional Web Hosting Selector (When domain is present) -->
                                <div class="cf-hosting-addon-box mt-3" id="pageHostingAddonSection">
                                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                                        <span class="fs-13 fw-bold text-slate-800">
                                            <i class="las la-server text-primary me-1"></i>Add High-Speed Web Hosting (Optional):
                                        </span>
                                        <!-- Cycle Switcher (visible when hosting active) -->
                                        <div class="cf-cycle-mini-switch d-none" id="pageHostingAddonCycleWrap">
                                            <button type="button" class="cf-cycle-mini-btn active" id="pageAddonCycleMonthlyBtn" onclick="pageSetBillingCycle('monthly')">
                                                Monthly
                                            </button>
                                            <button type="button" class="cf-cycle-mini-btn" id="pageAddonCycleYearlyBtn" onclick="pageSetBillingCycle('annually')">
                                                Annual (-20% OFF)
                                            </button>
                                        </div>
                                    </div>

                                    <!-- 3 Minimalist Pills -->
                                    <div class="cf-hosting-mini-pills">
                                        <!-- Option 1: None -->
                                        <div class="cf-hosting-mini-pill active" id="addonOptNone" onclick="pageSelectHostingPlan('none')">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="cf-radio-circle"></div>
                                                    <div>
                                                        <strong class="fs-13 text-slate-900 d-block">Domain Only</strong>
                                                        <small class="text-secondary fs-11">No hosting needed</small>
                                                    </div>
                                                </div>
                                                <span class="fs-12 fw-bold text-slate-500">$0.00</span>
                                            </div>
                                        </div>

                                        <!-- Option 2: Business Starter (Recommended) -->
                                        <div class="cf-hosting-mini-pill" id="addonOpt201" onclick="pageSelectHostingPlan('201')">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="cf-radio-circle"></div>
                                                    <div>
                                                        <strong class="fs-13 text-slate-900 d-block">Business Starter <span class="badge bg-primary text-white rounded-pill ms-1" style="font-size: 9px;">POPULAR</span></strong>
                                                        <small class="text-secondary fs-11">20 GB NVMe • 1 Site</small>
                                                    </div>
                                                </div>
                                                <span class="fs-12 fw-bold text-primary" id="addonPriceText201">+$7.50/mo</span>
                                            </div>
                                        </div>

                                        <!-- Option 3: Mini Plan -->
                                        <div class="cf-hosting-mini-pill" id="addonOpt101" onclick="pageSelectHostingPlan('101')">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="cf-radio-circle"></div>
                                                    <div>
                                                        <strong class="fs-13 text-slate-900 d-block">Mini Plan</strong>
                                                        <small class="text-secondary fs-11">5 GB NVMe • Blogs</small>
                                                    </div>
                                                </div>
                                                <span class="fs-12 fw-bold text-primary" id="addonPriceText101">+$3.00/mo</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Step 1 Navigation CTA -->
                                <div class="mt-4 pt-2 border-top">
                                    <button type="button" class="cf-btn-next-step w-100" onclick="pageGoToStep(2)">
                                        <span>Continue to Account Details</span>
                                        <i class="las la-arrow-right ms-2"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- =========================================================================
                                 STEP 2: ACCOUNT INFORMATION (MATCHING register.php DESIGN)
                                 ========================================================================= -->
                            <div class="cf-checkout-card d-none" id="stepBox2">
                                <div class="cf-card-header d-flex align-items-center justify-content-between flex-wrap gap-3">
                                    <div>
                                        <h4 class="cf-card-title"><i class="las la-user-circle text-primary me-2"></i>Step 2: Customer Account</h4>
                                        <p class="text-secondary fs-13 mb-0" id="step2HeaderSubtext">
                                            <?php echo $isLoggedIn ? 'You are authenticated with your CloudFlox account.' : 'Create your client portal account or sign in to an existing profile.'; ?>
                                        </p>
                                    </div>
                                    <!-- Mode Switcher Tabs -->
                                    <div class="cf-auth-tabs-wrap mb-0 <?php echo $isLoggedIn ? 'd-none' : ''; ?>" id="step2AuthTabs">
                                        <button type="button" class="cf-auth-tab-btn active" id="btnTabRegister" onclick="pageSetAccountMode('register')">
                                            <i class="las la-user-plus"></i>
                                            <span>Create Account</span>
                                        </button>
                                        <button type="button" class="cf-auth-tab-btn" id="btnTabLogin" onclick="pageSetAccountMode('login')">
                                            <i class="las la-sign-in-alt"></i>
                                            <span>Sign In</span>
                                        </button>
                                    </div>
                                </div>

                                <!-- PANE A: ALREADY SIGNED IN -->
                                <div id="paneAccountLoggedIn" class="<?php echo $isLoggedIn ? '' : 'd-none'; ?>">
                                    <div class="p-3.5 rounded-3 border bg-white mb-3 shadow-xs">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                                            <div class="d-flex align-items-center gap-3">
                                                <div style="width: 48px; height: 48px; border-radius: 50%; background: #EFF6FF; color: #2563EB; display: flex; align-items: center; justify-content: center; font-size: 24px; flex-shrink: 0;">
                                                    <i class="las la-user-check"></i>
                                                </div>
                                                <div>
                                                    <div class="d-flex align-items-center gap-2 mb-0.5">
                                                        <h5 class="fw-bold text-slate-900 mb-0 fs-16" id="loggedInNameText"><?php echo htmlspecialchars($loggedInClientName ?: 'Client Account'); ?></h5>
                                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill fs-11">Verified</span>
                                                    </div>
                                                    <span class="text-secondary fs-13" id="loggedInEmailText"><?php echo htmlspecialchars($loggedInClientEmail); ?></span>
                                                </div>
                                            </div>
                                            <div>
                                                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 fs-12" onclick="pageLogout()">
                                                    <i class="las la-sign-out-alt me-1"></i> Switch Account
                                                </button>
                                            </div>
                                        </div>
                                        <div class="alert alert-info py-2 px-3 mt-3 mb-0 fs-12 rounded-2 border-0 bg-blue-50 text-slate-700">
                                            <i class="las la-info-circle text-primary me-1"></i> Your domains and cloud deployments will be automatically linked to this CloudFlox account.
                                        </div>
                                    </div>
                                </div>

                                <!-- PANE B: CREATE ACCOUNT (Matching register.php EXACT blocks & icons) -->
                                <div id="paneAccountRegister" class="<?php echo $isLoggedIn ? 'd-none' : ''; ?>">

                                    <!-- 1. PERSONAL INFORMATION BLOCK -->
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
                                                    <label for="pageFname" class="form-label fs-13 fw-bold text-slate-800 mb-2">
                                                        First Name <span class="text-danger">*</span>
                                                    </label>
                                                    <div class="position-relative">
                                                        <i class="las la-user cf-input-icon"></i>
                                                        <input type="text" class="form-control" id="pageFname" name="firstname" placeholder="John" autocomplete="given-name" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="cf-minimal-input-wrap mb-0">
                                                    <label for="pageLname" class="form-label fs-13 fw-bold text-slate-800 mb-2">
                                                        Last Name <span class="text-danger">*</span>
                                                    </label>
                                                    <div class="position-relative">
                                                        <i class="las la-user cf-input-icon"></i>
                                                        <input type="text" class="form-control" id="pageLname" name="lastname" placeholder="Doe" autocomplete="family-name" required>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row g-3.5">
                                            <div class="col-sm-6">
                                                <div class="cf-minimal-input-wrap mb-0">
                                                    <label for="pageEmail" class="form-label fs-13 fw-bold text-slate-800 mb-2">
                                                        Email Address <span class="text-danger">*</span>
                                                    </label>
                                                    <div class="position-relative">
                                                        <i class="las la-envelope cf-input-icon"></i>
                                                        <input type="email" class="form-control" id="pageEmail" name="email" placeholder="name@example.com" autocomplete="email" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="cf-minimal-input-wrap mb-0">
                                                    <label for="pagePhone" class="form-label fs-13 fw-bold text-slate-800 mb-2">
                                                        Phone Number <span class="text-danger">*</span>
                                                    </label>
                                                    <div class="position-relative">
                                                        <i class="las la-phone cf-input-icon"></i>
                                                        <input type="tel" class="form-control" id="pagePhone" name="phonenumber" placeholder="+94 77 123 4567" autocomplete="tel" required>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- 2. BILLING ADDRESS BLOCK -->
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
                                                    <label for="pageCompany" class="form-label fs-13 fw-bold text-slate-800 mb-2">
                                                        Company Name <span class="text-muted fw-normal fs-11">(Optional)</span>
                                                    </label>
                                                    <div class="position-relative">
                                                        <i class="las la-building cf-input-icon"></i>
                                                        <input type="text" class="form-control" id="pageCompany" name="companyname" placeholder="Company / Business Name">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="cf-minimal-input-wrap mb-0">
                                                    <label for="pageCountry" class="form-label fs-13 fw-bold text-slate-800 mb-2">
                                                        Country <span class="text-danger">*</span>
                                                    </label>
                                                    <div class="position-relative">
                                                        <i class="las la-globe cf-input-icon"></i>
                                                        <select class="form-select" id="pageCountry" name="country" required>
                                                            <option value="LK" selected>Sri Lanka</option>
                                                            <option value="US">United States</option>
                                                            <option value="GB">United Kingdom</option>
                                                            <option value="AU">Australia</option>
                                                            <option value="CA">Canada</option>
                                                            <option value="IN">India</option>
                                                            <option value="SG">Singapore</option>
                                                            <option value="AE">United Arab Emirates</option>
                                                            <option value="DE">Germany</option>
                                                            <option value="FR">France</option>
                                                            <option value="MY">Malaysia</option>
                                                            <option value="JP">Japan</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row g-3.5 mb-3.5">
                                            <div class="col-sm-6">
                                                <div class="cf-minimal-input-wrap mb-0">
                                                    <label for="pageAddress1" class="form-label fs-13 fw-bold text-slate-800 mb-2">
                                                        Street Address <span class="text-danger">*</span>
                                                    </label>
                                                    <div class="position-relative">
                                                        <i class="las la-home cf-input-icon"></i>
                                                        <input type="text" class="form-control" id="pageAddress1" name="address1" placeholder="123 Main Street" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="cf-minimal-input-wrap mb-0">
                                                    <label for="pageAddress2" class="form-label fs-13 fw-bold text-slate-800 mb-2">
                                                        Street Address 2 <span class="text-muted fw-normal fs-11">(Optional)</span>
                                                    </label>
                                                    <div class="position-relative">
                                                        <i class="las la-map-pin cf-input-icon"></i>
                                                        <input type="text" class="form-control" id="pageAddress2" name="address2" placeholder="Apt, Suite, Building">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row g-3.5">
                                            <div class="col-sm-4">
                                                <div class="cf-minimal-input-wrap mb-0">
                                                    <label for="pageCity" class="form-label fs-13 fw-bold text-slate-800 mb-2">
                                                        City <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text" class="form-control px-3" id="pageCity" name="city" placeholder="Colombo" required>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="cf-minimal-input-wrap mb-0">
                                                    <label for="pageState" class="form-label fs-13 fw-bold text-slate-800 mb-2">
                                                        State / Region
                                                    </label>
                                                    <input type="text" class="form-control px-3" id="pageState" name="state" placeholder="Western">
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="cf-minimal-input-wrap mb-0">
                                                    <label for="pagePostcode" class="form-label fs-13 fw-bold text-slate-800 mb-2">
                                                        Postcode / ZIP
                                                    </label>
                                                    <input type="text" class="form-control px-3" id="pagePostcode" name="postcode" placeholder="00100">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- 3. ACCOUNT SECURITY BLOCK -->
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
                                                    <label for="pagePassword" class="form-label fs-13 fw-bold text-slate-800 mb-2">
                                                        Password <span class="text-danger">*</span>
                                                    </label>
                                                    <div class="position-relative">
                                                        <i class="las la-lock cf-input-icon"></i>
                                                        <input type="password" class="form-control pe-5" id="pagePassword" name="password" placeholder="Min. 6 chars" required>
                                                        <button type="button" class="btn btn-link position-absolute end-0 top-50 translate-middle-y text-secondary text-decoration-none pe-2.5" onclick="pageTogglePass('pagePassword', 'pageEyeReg')">
                                                            <i class="las la-eye fs-18" id="pageEyeReg"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="cf-minimal-input-wrap mb-0">
                                                    <label for="pageConfirm" class="form-label fs-13 fw-bold text-slate-800 mb-2">
                                                        Confirm Password <span class="text-danger">*</span>
                                                    </label>
                                                    <div class="position-relative">
                                                        <i class="las la-check-double cf-input-icon"></i>
                                                        <input type="password" class="form-control pe-5" id="pageConfirm" name="password_confirm" placeholder="Repeat password" required>
                                                        <button type="button" class="btn btn-link position-absolute end-0 top-50 translate-middle-y text-secondary text-decoration-none pe-2.5" onclick="pageTogglePass('pageConfirm', 'pageEyeConfirm')">
                                                            <i class="las la-eye fs-18" id="pageEyeConfirm"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <!-- PANE B: LOG IN TO EXISTING ACCOUNT -->
                                <div id="paneAccountLogin" class="d-none">
                                    <div class="cf-form-section-block">
                                        <div class="cf-form-section-header-box">
                                            <div class="cf-form-section-step-num">
                                                <i class="las la-sign-in-alt"></i>
                                            </div>
                                            <div class="cf-form-section-title-wrap">
                                                <h4>Sign In to Your Account</h4>
                                                <p>Enter your registered CloudFlox email and password</p>
                                            </div>
                                        </div>

                                        <div class="row g-3.5">
                                            <div class="col-12">
                                                <div class="cf-minimal-input-wrap mb-0">
                                                    <label for="pageLoginEmail" class="form-label fs-13 fw-bold text-slate-800 mb-2">
                                                        Email Address <span class="text-danger">*</span>
                                                    </label>
                                                    <div class="position-relative">
                                                        <i class="las la-envelope cf-input-icon"></i>
                                                        <input type="email" class="form-control" id="pageLoginEmail" name="login_email" placeholder="name@example.com" autocomplete="email">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="cf-minimal-input-wrap mb-0">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <label for="pageLoginPassword" class="form-label fs-13 fw-bold text-slate-800 mb-0">
                                                            Password <span class="text-danger">*</span>
                                                        </label>
                                                        <a href="https://my.cloudflox.com/password/reset" target="_blank" class="fs-12 text-primary text-decoration-none hover-underline">Forgot?</a>
                                                    </div>
                                                    <div class="position-relative">
                                                        <i class="las la-lock cf-input-icon"></i>
                                                        <input type="password" class="form-control pe-5" id="pageLoginPassword" name="login_password" placeholder="Enter your account password" autocomplete="current-password">
                                                        <button type="button" class="btn btn-link position-absolute end-0 top-50 translate-middle-y text-secondary text-decoration-none pe-2.5" onclick="pageTogglePass('pageLoginPassword', 'pageEyeLogin')">
                                                            <i class="las la-eye fs-18" id="pageEyeLogin"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Step 2 Navigation Buttons -->
                                <div class="d-flex justify-content-between align-items-center gap-3 mt-4 pt-2 border-top flex-wrap">
                                    <button type="button" class="cf-btn-back-step" onclick="pageGoToStep(1)">
                                        <i class="las la-arrow-left me-1"></i> Back to Review
                                    </button>
                                    <button type="button" class="cf-btn-next-step" id="btnStep2Submit" onclick="pageProcessAccount()">
                                        <span class="btn-text">
                                            <span id="btnStep2Text"><?php echo $isLoggedIn ? 'Continue to Payment' : 'Create Account &amp; Continue'; ?></span>
                                            <i class="las la-arrow-right ms-2"></i>
                                        </span>
                                        <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                                    </button>
                                </div>
                            </div>

                            <!-- =========================================================================
                                 STEP 3: PAYMENT METHOD & FINAL CONFIRMATION
                                 ========================================================================= -->
                            <div class="cf-checkout-card d-none" id="stepBox3">
                                <div class="cf-card-header">
                                    <div>
                                        <h4 class="cf-card-title"><i class="las la-credit-card text-primary me-2"></i>Step 3: Select Payment Method</h4>
                                        <p class="text-secondary fs-13 mb-0">Choose your preferred gateway to complete your deployment.</p>
                                    </div>
                                    <span class="text-secondary fs-12"><i class="las la-lock text-emerald me-1"></i>256-Bit SSL</span>
                                </div>

                                <!-- Payment Cards Grid (Card, PayHere, PayPal) -->
                                <div class="row g-3 mb-4">
                                    <div class="col-12 col-md-4">
                                        <label class="cf-pay-option-pill active w-100" onclick="pageSelectPay('card')">
                                            <input type="radio" name="paymentmethod" value="card" checked>
                                            <i class="las la-credit-card fs-28 text-primary mb-1.5"></i>
                                            <strong class="d-block fs-14">Card Payment</strong>
                                            <small class="text-muted fs-11.5">Visa / Mastercard / Amex</small>
                                        </label>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label class="cf-pay-option-pill w-100" onclick="pageSelectPay('payhere')">
                                            <input type="radio" name="paymentmethod" value="payhere">
                                            <i class="las la-money-bill-wave fs-28 text-emerald mb-1.5"></i>
                                            <strong class="d-block fs-14">PayHere</strong>
                                            <small class="text-muted fs-11.5">LKR / Local Bank &amp; Wallets</small>
                                        </label>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label class="cf-pay-option-pill w-100" onclick="pageSelectPay('paypal')">
                                            <input type="radio" name="paymentmethod" value="paypal">
                                            <i class="lab la-paypal fs-28 text-info mb-1.5"></i>
                                            <strong class="d-block fs-14">PayPal</strong>
                                            <small class="text-muted fs-11.5">Instant Global Checkout</small>
                                        </label>
                                    </div>
                                </div>

                                <!-- Trust Guarantee Box -->
                                <div class="p-3 bg-light rounded-3 border d-flex align-items-center justify-content-center gap-3 text-secondary fs-12 mb-4 flex-wrap">
                                    <span><i class="las la-lock text-emerald me-1"></i> 256-Bit SSL Encrypted</span>
                                    <span>•</span>
                                    <span><i class="las la-shield-alt text-primary me-1"></i> 30-Day Money-Back Guarantee</span>
                                    <span>•</span>
                                    <span><i class="las la-bolt text-warning me-1"></i> 60-Sec Activation</span>
                                </div>

                                <!-- Step 3 Navigation Buttons -->
                                <div class="d-flex justify-content-between align-items-center gap-3 pt-2 border-top flex-wrap">
                                    <button type="button" class="cf-btn-back-step" onclick="pageGoToStep(2)">
                                        <i class="las la-arrow-left me-1"></i> Back to Account
                                    </button>
                                    <button type="button" class="cf-btn-submit-order" id="btnPageSubmitOrder" onclick="pageSubmitOrder()">
                                        <span class="btn-text">
                                            <i class="las la-lock me-1.5"></i> Complete &amp; Deploy Order (<span id="btnPayAmount">$13.57</span>)
                                        </span>
                                        <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                                    </button>
                                </div>
                            </div>

                        </div>

                        <!-- RIGHT COLUMN: STICKY ORDER SUMMARY (Always Visible) -->
                        <div class="col-12 col-lg-4">
                            <div class="cf-sidebar-summary-card sticky-top" style="top: 80px;">
                                <h5 class="cf-summary-title mb-3 pb-2 border-bottom">Order Summary</h5>

                                <div class="cf-summary-line" id="sideDomainRow" style="display: none;">
                                    <span class="text-secondary" id="sideDomainLabel">Domain:</span>
                                    <strong class="text-slate-900" id="sideDomainFee">$13.57</strong>
                                </div>

                                <div class="cf-summary-line" id="sideHostingRow" style="display: none;">
                                    <span class="text-secondary" id="sideHostingLabel">Hosting:</span>
                                    <strong class="text-slate-900" id="sideHostingFee">None ($0.00)</strong>
                                </div>

                                <div class="cf-summary-line">
                                    <span class="text-secondary">WHOIS Privacy:</span>
                                    <span class="text-emerald fw-bold">FREE</span>
                                </div>

                                <div class="cf-summary-line">
                                    <span class="text-secondary">DNS Setup:</span>
                                    <span class="text-emerald fw-bold">FREE</span>
                                </div>

                                <hr class="my-3">

                                <div class="d-flex justify-content-between align-items-baseline mb-1">
                                    <span class="fs-14 fw-bold text-slate-800">Total Due Today:</span>
                                    <span class="cf-side-total-due text-primary" id="sideTotalDue">$13.57</span>
                                </div>
                                <small class="text-muted fs-11 d-block mb-3">All taxes &amp; fees included • Cancel anytime</small>

                                <!-- Trust Guarantee Box -->
                                <div class="cf-summary-trust-box">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <i class="las la-shield-alt text-emerald fs-18"></i>
                                        <span><strong>30-Day Guarantee:</strong> 100% refund policy.</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <i class="las la-bolt text-warning fs-18"></i>
                                        <span><strong>Instant Setup:</strong> Cloud ready in seconds.</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="las la-headset text-primary fs-18"></i>
                                        <span><strong>24/7 Support:</strong> Human sysadmin team.</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </form>
            </div>

            <!-- SUCCESS CONFIRMATION BOX (Hidden until order completes) -->
            <div class="cf-checkout-card text-center py-5 d-none max-w-600 mx-auto" id="checkoutSuccessBox">
                <div class="cf-success-circle mx-auto mb-3">
                    <i class="las la-check fs-36 text-white"></i>
                </div>
                <h3 class="fw-extrabold text-slate-900 mb-1 fs-22">Order Placed Successfully!</h3>
                <p class="text-secondary fs-13 mb-4 max-w-500 mx-auto">
                    Your order has been registered in the CloudFlox system. An automated invoice has been generated for your account.
                </p>

                <div class="cf-success-details-card mb-4 text-start">
                    <div class="d-flex justify-content-between py-1.5 border-bottom">
                        <span class="text-secondary fs-13">Order Number:</span>
                        <strong class="text-slate-900 fs-13" id="successOrderId">#3920</strong>
                    </div>
                    <div class="d-flex justify-content-between py-1.5 border-bottom">
                        <span class="text-secondary fs-13">Invoice Number:</span>
                        <strong class="text-slate-900 fs-13" id="successInvoiceId">#8910</strong>
                    </div>
                    <div class="d-flex justify-content-between py-1.5 border-bottom">
                        <span class="text-secondary fs-13">Service:</span>
                        <strong class="text-primary fs-13" id="successPlanName">Domain Registration</strong>
                    </div>
                    <div class="d-flex justify-content-between py-1.5">
                        <span class="text-secondary fs-13">Domain:</span>
                        <strong class="text-slate-900 fs-13" id="successDomainName">mybrand.com</strong>
                    </div>
                </div>

                <div class="d-flex flex-column flex-sm-row justify-content-center gap-3">
                    <button type="button" class="btn btn-emerald fw-bold py-2.5 px-4 rounded-pill shadow-sm fs-14 d-none" id="btnSuccessPayHere" onclick="startPayHerePayment()">
                        <i class="las la-bolt me-1"></i> Pay Now with PayHere (<span id="successPayHereAmount"></span>)
                    </button>
                    <a href="#" target="_blank" class="btn btn-primary fw-bold py-2.5 px-4 rounded-pill shadow-sm fs-14" id="btnSuccessInvoice">
                        <i class="las la-file-invoice-dollar me-1"></i> View &amp; Pay Invoice
                    </a>
                    <a href="https://my.cloudflox.com/clientarea.php" target="_blank" class="btn btn-outline-secondary fw-semibold py-2.5 px-4 rounded-pill fs-14">
                        <i class="las la-user me-1"></i> Client Portal
                    </a>
                </div>
            </div>

        </div>
    </section>

    <!-- =========== Footer Area =========== -->
    <?php include 'include/footer.inc.php'; ?>
    <!-- =========== Footer Area =========== -->

    <!-- =========== JS Area =========== -->
    <?php include 'include/js.inc.php'; ?>
    <!-- PayHere JavaScript SDK (Direct integration from cfwhmcs) -->
    <script type="text/javascript" src="https://www.payhere.lk/lib/payhere.js"></script>
    <!-- =========== JS Area =========== -->

    <!-- Step-by-Step Checkout Logic Script -->
    <script>
    (function() {
        'use strict';

        // Products Catalog
        const PRODUCTS = {
            '101': { name: 'Mini Plan', category: 'Budget Cloud Hosting', monthly: 3.00, yearly: 28.80, specs: '5 GB NVMe • 1 Site • Free SSL' },
            '102': { name: 'Basic Plan', category: 'Budget Cloud Hosting', monthly: 4.50, yearly: 43.20, specs: '10 GB NVMe • 2 Sites • Free SSL' },
            '103': { name: 'Plus Plan', category: 'Budget Cloud Hosting', monthly: 6.00, yearly: 57.60, specs: '20 GB NVMe • 5 Sites • Free SSL' },
            '201': { name: 'Business Starter', category: 'Business NVMe Hosting', monthly: 7.50, yearly: 72.00, specs: '20 GB Pure NVMe • 1 vCPU • 2 GB RAM' },
            '202': { name: 'Business Growth', category: 'Business NVMe Hosting', monthly: 15.00, yearly: 144.00, specs: '40 GB Pure NVMe • 2 vCPU • 4 GB RAM' },
            '203': { name: 'Business Professional', category: 'Business NVMe Hosting', monthly: 27.00, yearly: 259.20, specs: '80 GB Pure NVMe • 4 vCPU • 8 GB RAM' },
            '204': { name: 'Business Enterprise', category: 'Business NVMe Hosting', monthly: 48.00, yearly: 460.80, specs: '150 GB Pure NVMe • 6 vCPU • 12 GB RAM' },
            '301': { name: 'Cloud-01 Starter', category: 'Managed Cloud', monthly: 75.00, yearly: 720.00, specs: '100 GB NVMe • 4 vCPU • 8 GB RAM' },
            '401': { name: 'KVM VPS Starter', category: 'KVM Cloud VPS', monthly: 7.99, yearly: 76.70, specs: '1 vCPU • 2 GB RAM • 40 GB NVMe' }
        };

        const TLD_PRICES = {
            '.com': 13.57,
            '.net': 15.49,
            '.org': 17.49,
            '.cloud': 9.99,
            '.io': 39.99,
            '.co': 24.99,
            '.online': 3.99,
            '.store': 4.99
        };

        const userSession = {
            isLoggedIn: <?php echo $isLoggedIn ? 'true' : 'false'; ?>,
            clientId: <?php echo (int)$loggedInClientId; ?>,
            clientName: <?php echo json_encode($loggedInClientName); ?>,
            clientEmail: <?php echo json_encode($loggedInClientEmail); ?>
        };

        function escapeHtml(str) {
            if (!str) return '';
            const div = document.createElement('div');
            div.textContent = str;
            return div.innerHTML;
        }

        // State
        let state = {
            domain: '',
            domainPrice: 0.00,
            pid: 'none',
            billingcycle: 'monthly',
            accountAction: userSession.isLoggedIn ? 'loggedin' : 'register',
            paymentMethod: 'card',
            step: 1
        };

        // Initialize from URL Query Params
        function initFromUrl() {
            const params = new URLSearchParams(window.location.search);
            const domainParam = params.get('domain') || '';
            const priceParam  = params.get('price') || params.get('domain_price') || '';
            const pidParam    = params.get('pid') || '';
            const cycleParam  = params.get('cycle') || params.get('billingcycle') || 'monthly';

            if (domainParam) {
                state.domain = domainParam;
                let parsedPrice = parseFloat(priceParam.replace(/[^0-9\.]/g, ''));
                if (!parsedPrice || isNaN(parsedPrice)) {
                    const lastDot = domainParam.lastIndexOf('.');
                    const ext = lastDot !== -1 ? domainParam.substring(lastDot) : '.com';
                    parsedPrice = TLD_PRICES[ext] || 13.57;
                }
                state.domainPrice = parsedPrice;
            }

            if (pidParam && pidParam !== 'none' && PRODUCTS[pidParam]) {
                state.pid = pidParam;
            } else if (!domainParam && !pidParam) {
                state.pid = '201';
            } else {
                state.pid = 'none';
            }

            state.billingcycle = (cycleParam === 'annually' || cycleParam === 'yearly') ? 'annually' : 'monthly';

            renderState();
        }

        function renderState() {
            // Update hidden inputs
            document.getElementById('pagePid').value = state.pid;
            document.getElementById('pageCycle').value = state.billingcycle;
            document.getElementById('pageDomain').value = state.domain;
            document.getElementById('pageAccountAction').value = state.accountAction;

            const domainItemRow = document.getElementById('pageDomainItemRow');
            const hostingItemRow = document.getElementById('pageHostingItemRow');
            const hostingAddonSection = document.getElementById('pageHostingAddonSection');
            const isYearly = state.billingcycle === 'annually';

            // 1. Domain Rendering
            if (state.domain) {
                domainItemRow.style.display = 'flex';
                document.getElementById('pageDomainNameText').textContent = state.domain;
                document.getElementById('pageDomainPriceText').textContent = '$' + state.domainPrice.toFixed(2);
            } else {
                domainItemRow.style.display = 'none';
            }

            // 2. Hosting Rendering
            if (state.domain) {
                // When domain is present: we use the Add-on Section selector
                hostingItemRow.style.display = 'none';
                hostingAddonSection.style.display = 'block';

                const optNone = document.getElementById('addonOptNone');
                const opt201  = document.getElementById('addonOpt201');
                const opt101  = document.getElementById('addonOpt101');
                const cycleWrap = document.getElementById('pageHostingAddonCycleWrap');
                const monthlyBtn = document.getElementById('pageAddonCycleMonthlyBtn');
                const yearlyBtn  = document.getElementById('pageAddonCycleYearlyBtn');

                // Toggle active classes on cards
                if (optNone) optNone.classList.toggle('active', state.pid === 'none');
                if (opt201)  opt201.classList.toggle('active', state.pid === '201');
                if (opt101)  opt101.classList.toggle('active', state.pid === '101');

                // Update prices on cards
                const p201 = PRODUCTS['201'];
                const p101 = PRODUCTS['101'];
                const p201Text = document.getElementById('addonPriceText201');
                const p101Text = document.getElementById('addonPriceText101');

                if (isYearly) {
                    if (p201Text) p201Text.textContent = '+$' + (p201.yearly / 12).toFixed(2) + '/mo';
                    if (p101Text) p101Text.textContent = '+$' + (p101.yearly / 12).toFixed(2) + '/mo';
                } else {
                    if (p201Text) p201Text.textContent = '+$' + p201.monthly.toFixed(2) + '/mo';
                    if (p101Text) p101Text.textContent = '+$' + p101.monthly.toFixed(2) + '/mo';
                }

                if (monthlyBtn && yearlyBtn) {
                    monthlyBtn.classList.toggle('active', !isYearly);
                    yearlyBtn.classList.toggle('active', isYearly);
                }

                // Show cycle wrap only if hosting is selected
                if (cycleWrap) {
                    cycleWrap.classList.toggle('d-none', state.pid === 'none');
                }

            } else {
                // When NO domain is present: we display the full standalone hosting card
                hostingAddonSection.style.display = 'none';

                if (state.pid !== 'none' && PRODUCTS[state.pid]) {
                    const plan = PRODUCTS[state.pid];
                    hostingItemRow.style.display = 'flex';

                    document.getElementById('pageHostingNameText').textContent = plan.name;
                    document.getElementById('pageHostingBadgeText').textContent = plan.category.toUpperCase();
                    document.getElementById('pageHostingSpecsText').textContent = plan.specs;

                    const priceEl = document.getElementById('pageHostingPriceText');
                    const cycleEl = document.getElementById('pageHostingCycleLabelText');
                    if (isYearly) {
                        priceEl.textContent = '$' + (plan.yearly / 12).toFixed(2);
                        cycleEl.textContent = '/mo ($' + plan.yearly.toFixed(2) + '/yr)';
                    } else {
                        priceEl.textContent = '$' + plan.monthly.toFixed(2);
                        cycleEl.textContent = '/month';
                    }
                } else {
                    hostingItemRow.style.display = 'none';
                }
            }

            // 3. Totals Calculation
            const hasHosting = (state.pid !== 'none' && PRODUCTS[state.pid]);
            const hostingFee = hasHosting ? (isYearly ? PRODUCTS[state.pid].yearly : PRODUCTS[state.pid].monthly) : 0.00;
            const total = state.domainPrice + hostingFee;

            // Sidebar rows & Pay button text
            const sideDomainRow    = document.getElementById('sideDomainRow');
            const sideDomainFee    = document.getElementById('sideDomainFee');
            const sideHostingRow   = document.getElementById('sideHostingRow');
            const sideHostingLabel = document.getElementById('sideHostingLabel');
            const sideHostingFee   = document.getElementById('sideHostingFee');
            const sideTotalDue     = document.getElementById('sideTotalDue');
            const btnPayAmount     = document.getElementById('btnPayAmount');

            if (sideDomainRow) {
                sideDomainRow.style.display = state.domainPrice > 0 ? 'flex' : 'none';
                if (sideDomainFee) sideDomainFee.textContent = '$' + state.domainPrice.toFixed(2);
            }

            if (sideHostingRow) {
                sideHostingRow.style.display = 'flex';
                if (hasHosting) {
                    const plan = PRODUCTS[state.pid];
                    sideHostingLabel.textContent = 'Hosting (' + plan.name + '):';
                    sideHostingFee.className = 'text-primary fw-bold';
                    sideHostingFee.textContent = '+$' + hostingFee.toFixed(2) + (isYearly ? '/yr' : '/mo');
                } else if (state.domain) {
                    sideHostingLabel.textContent = 'Web Hosting:';
                    sideHostingFee.className = 'text-secondary fw-semibold';
                    sideHostingFee.textContent = 'None ($0.00)';
                } else {
                    sideHostingRow.style.display = 'none';
                }
            }

            if (sideTotalDue) {
                sideTotalDue.textContent = '$' + total.toFixed(2);
            }

            if (btnPayAmount) {
                btnPayAmount.textContent = '$' + total.toFixed(2);
            }
        }

        // Step Navigation Logic
        window.pageGoToStep = function(targetStep) {
            hideAlert();

            if (targetStep === 2) {
                // Ensure either domain or hosting is selected
                if (!state.domain && state.pid === 'none') {
                    showAlert('Please select either a domain name or a web hosting plan.');
                    return;
                }
            } else if (targetStep === 3) {
                // Check if user is authenticated
                if (!userSession.isLoggedIn) {
                    pageProcessAccount();
                    return;
                }
            }

            state.step = targetStep;

            // Switch visibility of step boxes
            [1, 2, 3].forEach(s => {
                const box = document.getElementById('stepBox' + s);
                if (box) box.classList.toggle('d-none', s !== targetStep);

                const pill = document.getElementById('pstep' + s);
                if (pill) {
                    pill.classList.remove('active', 'completed');
                    if (s === targetStep) {
                        pill.classList.add('active');
                    } else if (s < targetStep) {
                        pill.classList.add('completed');
                    }
                }
            });

            window.scrollTo({ top: 120, behavior: 'smooth' });
        };

        // Process Customer Account (Verify login credentials or create account)
        window.pageProcessAccount = function() {
            hideAlert();

            // If already authenticated, advance directly to Step 3
            if (userSession.isLoggedIn) {
                state.step = 3;
                [1, 2, 3].forEach(s => {
                    const box = document.getElementById('stepBox' + s);
                    if (box) box.classList.toggle('d-none', s !== 3);
                    const pill = document.getElementById('pstep' + s);
                    if (pill) {
                        pill.classList.remove('active', 'completed');
                        if (s === 3) pill.classList.add('active');
                        else if (s < 3) pill.classList.add('completed');
                    }
                });
                window.scrollTo({ top: 120, behavior: 'smooth' });
                return;
            }

            const btn = document.getElementById('btnStep2Submit');
            const btnText = btn.querySelector('.btn-text');
            const spinner = btn.querySelector('.spinner-border');
            const btnLabel = document.getElementById('btnStep2Text');

            function setLoading(isLoading) {
                if (isLoading) {
                    btn.disabled = true;
                    btnText.classList.add('d-none');
                    spinner.classList.remove('d-none');
                } else {
                    btn.disabled = false;
                    btnText.classList.remove('d-none');
                    spinner.classList.add('d-none');
                }
            }

            if (state.accountAction === 'login') {
                const email = document.getElementById('pageLoginEmail').value.trim();
                const pass  = document.getElementById('pageLoginPassword').value;

                if (!email || !email.includes('@')) {
                    showAlert('Please enter your valid account email address.');
                    document.getElementById('pageLoginEmail').focus();
                    return;
                }
                if (!pass) {
                    showAlert('Please enter your account password.');
                    document.getElementById('pageLoginPassword').focus();
                    return;
                }

                setLoading(true);

                fetch('api/checkout.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        action: 'login',
                        email: email,
                        password: pass
                    })
                })
                .then(res => res.json())
                .then(data => {
                    setLoading(false);
                    if (data.status === 'success') {
                        userSession.isLoggedIn = true;
                        userSession.clientId = data.client_id;
                        userSession.clientName = data.client_name;
                        userSession.clientEmail = data.client_email;

                        // Update Step 2 authenticated card
                        document.getElementById('loggedInNameText').textContent = data.client_name;
                        document.getElementById('loggedInEmailText').textContent = data.client_email;
                        document.getElementById('paneAccountLoggedIn').classList.remove('d-none');
                        document.getElementById('paneAccountLogin').classList.add('d-none');
                        document.getElementById('paneAccountRegister').classList.add('d-none');
                        document.getElementById('step2AuthTabs').classList.add('d-none');
                        document.getElementById('step2HeaderSubtext').textContent = 'You are authenticated with your CloudFlox account.';
                        if (btnLabel) btnLabel.textContent = 'Continue to Payment';

                        if (window.cfToast) {
                            cfToast.success('Signed in as <strong>' + escapeHtml(data.client_name) + '</strong>');
                        }

                        // Advance to Payment
                        pageGoToStep(3);
                    } else {
                        showAlert(data.message || 'Invalid email address or password. Please try again.');
                    }
                })
                .catch(() => {
                    setLoading(false);
                    showAlert('Network error: Could not verify login credentials. Please check your connection.');
                });

            } else {
                // Register Mode
                const fname = document.getElementById('pageFname').value.trim();
                const lname = document.getElementById('pageLname').value.trim();
                const email = document.getElementById('pageEmail').value.trim();
                const phone = document.getElementById('pagePhone').value.trim();
                const company = document.getElementById('pageCompany').value.trim();
                const country = document.getElementById('pageCountry').value;
                const addr1 = document.getElementById('pageAddress1').value.trim();
                const addr2 = document.getElementById('pageAddress2').value.trim();
                const city  = document.getElementById('pageCity').value.trim();
                const stateName = document.getElementById('pageState').value.trim();
                const postcode = document.getElementById('pagePostcode').value.trim();
                const pass  = document.getElementById('pagePassword').value;
                const conf  = document.getElementById('pageConfirm').value;

                if (!fname || !lname) {
                    showAlert('Please enter both your First Name and Last Name.');
                    document.getElementById(!fname ? 'pageFname' : 'pageLname').focus();
                    return;
                }
                if (!email || !email.includes('@')) {
                    showAlert('Please enter a valid email address.');
                    document.getElementById('pageEmail').focus();
                    return;
                }
                if (!phone) {
                    showAlert('Please enter your phone number.');
                    document.getElementById('pagePhone').focus();
                    return;
                }
                if (!addr1) {
                    showAlert('Please enter your street address.');
                    document.getElementById('pageAddress1').focus();
                    return;
                }
                if (!city) {
                    showAlert('Please enter your city.');
                    document.getElementById('pageCity').focus();
                    return;
                }
                if (!pass || pass.length < 6) {
                    showAlert('Password must be at least 6 characters in length.');
                    document.getElementById('pagePassword').focus();
                    return;
                }
                if (pass !== conf) {
                    showAlert('Passwords do not match. Please re-enter your password.');
                    document.getElementById('pageConfirm').focus();
                    return;
                }

                setLoading(true);

                fetch('api/checkout.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        action: 'register',
                        firstname: fname,
                        lastname: lname,
                        email: email,
                        phonenumber: phone,
                        companyname: company,
                        country: country,
                        address1: addr1,
                        address2: addr2,
                        city: city,
                        state: stateName,
                        postcode: postcode,
                        password: pass,
                        password_confirm: conf
                    })
                })
                .then(res => res.json())
                .then(data => {
                    setLoading(false);
                    if (data.status === 'success') {
                        userSession.isLoggedIn = true;
                        userSession.clientId = data.client_id;
                        userSession.clientName = data.client_name;
                        userSession.clientEmail = data.client_email;

                        // Update Step 2 authenticated card
                        document.getElementById('loggedInNameText').textContent = data.client_name;
                        document.getElementById('loggedInEmailText').textContent = data.client_email;
                        document.getElementById('paneAccountLoggedIn').classList.remove('d-none');
                        document.getElementById('paneAccountLogin').classList.add('d-none');
                        document.getElementById('paneAccountRegister').classList.add('d-none');
                        document.getElementById('step2AuthTabs').classList.add('d-none');
                        document.getElementById('step2HeaderSubtext').textContent = 'You are authenticated with your CloudFlox account.';
                        if (btnLabel) btnLabel.textContent = 'Continue to Payment';

                        if (window.cfToast) {
                            cfToast.success('Account created! Welcome, <strong>' + escapeHtml(data.client_name) + '</strong>');
                        }

                        // Advance to Payment
                        pageGoToStep(3);
                    } else {
                        showAlert(data.message || 'Failed to create account. Please check your details.');
                    }
                })
                .catch(() => {
                    setLoading(false);
                    showAlert('Network error: Could not reach account registration server. Please try again.');
                });
            }
        };

        window.pageLogout = function() {
            fetch('api/checkout.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'logout' })
            })
            .finally(() => {
                userSession.isLoggedIn = false;
                userSession.clientId = 0;
                userSession.clientName = '';
                userSession.clientEmail = '';
                state.accountAction = 'register';

                document.getElementById('paneAccountLoggedIn').classList.add('d-none');
                document.getElementById('step2AuthTabs').classList.remove('d-none');
                document.getElementById('step2HeaderSubtext').textContent = 'Create your client portal account or sign in to an existing profile.';
                pageSetAccountMode('register');
            });
        };

        // Handlers
        window.pageSelectHostingPlan = function(pid) {
            state.pid = pid;
            renderState();
        };

        window.pageSetBillingCycle = function(cycle) {
            state.billingcycle = cycle;
            renderState();
        };

        window.pageSetAccountMode = function(mode) {
            state.accountAction = mode;
            document.getElementById('pageAccountAction').value = mode;

            const btnReg = document.getElementById('btnTabRegister');
            const btnLog = document.getElementById('btnTabLogin');
            const paneReg = document.getElementById('paneAccountRegister');
            const paneLog = document.getElementById('paneAccountLogin');
            const btnLabel = document.getElementById('btnStep2Text');

            if (mode === 'login') {
                btnReg.classList.remove('active');
                btnLog.classList.add('active');
                paneReg.classList.add('d-none');
                paneLog.classList.remove('d-none');
                if (btnLabel) btnLabel.textContent = 'Sign In & Continue';
            } else {
                btnLog.classList.remove('active');
                btnReg.classList.add('active');
                paneLog.classList.add('d-none');
                paneReg.classList.remove('d-none');
                if (btnLabel) btnLabel.textContent = 'Create Account & Continue';
            }
        };

        window.pageTogglePass = function(inputId, eyeId) {
            const input = document.getElementById(inputId);
            const eye = document.getElementById(eyeId);
            if (!input) return;
            if (input.type === 'password') {
                input.type = 'text';
                if (eye) eye.classList.replace('la-eye', 'la-eye-slash');
            } else {
                input.type = 'password';
                if (eye) eye.classList.replace('la-eye-slash', 'la-eye');
            }
        };

        window.pageSelectPay = function(method) {
            state.paymentMethod = method;
            document.querySelectorAll('.cf-pay-option-pill').forEach(card => card.classList.remove('active'));
            const radio = document.querySelector(`input[name="paymentmethod"][value="${method}"]`);
            if (radio) {
                radio.checked = true;
                radio.closest('.cf-pay-option-pill').classList.add('active');
            }
        };

        // Submit Final Order via AJAX
        window.pageSubmitOrder = function() {
            hideAlert();

            const btn = document.getElementById('btnPageSubmitOrder');
            btn.querySelector('.btn-text').classList.add('d-none');
            btn.querySelector('.spinner-border').classList.remove('d-none');
            btn.disabled = true;

            const formData = new FormData(document.getElementById('cfPageCheckoutForm'));
            const payload = {};
            formData.forEach((val, key) => { payload[key] = val; });

            // Ensure values are aligned
            payload.pid = state.pid;
            payload.billingcycle = state.billingcycle;
            payload.domain = state.domain;
            payload.domain_type = state.domain ? 'register' : 'subdomain';
            payload.account_action = state.accountAction;
            payload.paymentmethod = state.paymentMethod;
            payload.client_id = userSession.clientId;

            fetch('api/checkout.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(data => {
                btn.querySelector('.btn-text').classList.remove('d-none');
                btn.querySelector('.spinner-border').classList.add('d-none');
                btn.disabled = false;

                if (data.status === 'success') {
                    // Direct redirect to the invoice / payment page (bypassing the intermediate success box)
                    if (data.invoice_url) {
                        btn.querySelector('.btn-text').innerHTML = '<i class="las la-check-circle me-1.5"></i> Order Placed! Opening Payment Page...';
                        btn.querySelector('.btn-text').classList.remove('d-none');
                        btn.querySelector('.spinner-border').classList.add('d-none');
                        window.location.href = data.invoice_url;
                        return;
                    }
                } else {
                    btn.querySelector('.btn-text').classList.remove('d-none');
                    btn.querySelector('.spinner-border').classList.add('d-none');
                    btn.disabled = false;
                    showAlert(data.message || 'An error occurred while creating your order.');
                }
            })
            .catch(() => {
                btn.querySelector('.btn-text').classList.remove('d-none');
                btn.querySelector('.spinner-border').classList.add('d-none');
                btn.disabled = false;
                showAlert('Network error: Could not reach the billing server. Please try again.');
            });
        };

        window.startPayHerePayment = function() {
            if (!window.currentPayHereData || typeof payhere === 'undefined') return;

            payhere.onCompleted = function(orderId) {
                if (window.cfToast) {
                    cfToast.success('Payment completed! Your service is being deployed.');
                }
                const invBtn = document.getElementById('btnSuccessInvoice');
                if (invBtn && invBtn.href) {
                    window.location.href = invBtn.href;
                }
            };

            payhere.onDismissed = function() {
                console.log("PayHere payment modal closed.");
            };

            payhere.onError = function(error) {
                console.error("PayHere Error:", error);
                alert("PayHere payment notice: " + error);
            };

            payhere.startPayment(window.currentPayHereData);
        };

        window.showAlert = function(msg) {
            const box = document.getElementById('pageAlertBox');
            const text = document.getElementById('pageAlertText');
            if (box && text) {
                text.textContent = msg;
                box.classList.remove('d-none');
                box.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        };

        window.hideAlert = function() {
            const box = document.getElementById('pageAlertBox');
            if (box) box.classList.add('d-none');
        };

        // Initialize on DOM Ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initFromUrl);
        } else {
            initFromUrl();
        }

    })();
    </script>

</body>
</html>
