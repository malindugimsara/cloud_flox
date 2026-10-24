<?php
/**
 * CloudFlox Modern On-Site Checkout Modal
 * -------------------------------------------------------------------------
 * Slide-over / Modal checkout drawer that enables customers to purchase
 * servers and domains directly on cloudflox.com without external redirect.
 */
?>
<div class="cf-checkout-modal-backdrop" id="cfCheckoutModalBackdrop" aria-hidden="true" style="display: none;">
    <div class="cf-checkout-drawer" id="cfCheckoutDrawer" role="dialog" aria-modal="true" aria-labelledby="cfCheckoutTitle">
        
        <!-- Drawer Header -->
        <div class="cf-checkout-header">
            <div class="d-flex align-items-center gap-3">
                <div class="cf-checkout-logo-icon">
                    <i class="las la-cloud-upload-alt fs-24 text-primary"></i>
                </div>
                <div>
                    <h3 class="cf-checkout-title mb-0" id="cfCheckoutTitle">Instant Server Deployment</h3>
                    <span class="cf-checkout-subtitle" id="cfCheckoutPlanBadge">Business NVMe Cloud Hosting</span>
                </div>
            </div>
            <button type="button" class="cf-checkout-close-btn" id="cfCheckoutCloseBtn" aria-label="Close Checkout">
                <i class="las la-times"></i>
            </button>
        </div>

        <!-- Checkout Steps Indicator -->
        <div class="cf-checkout-steps-bar">
            <div class="cf-step-item active" data-step="1">
                <span class="cf-step-circle"><i class="las la-server"></i></span>
                <span class="cf-step-label">1. Plan &amp; Domain</span>
            </div>
            <div class="cf-step-connector"></div>
            <div class="cf-step-item" data-step="2">
                <span class="cf-step-circle"><i class="las la-user-check"></i></span>
                <span class="cf-step-label">2. Account</span>
            </div>
            <div class="cf-step-connector"></div>
            <div class="cf-step-item" data-step="3">
                <span class="cf-step-circle"><i class="las la-credit-card"></i></span>
                <span class="cf-step-label">3. Payment</span>
            </div>
        </div>

        <!-- Checkout Form Container -->
        <form id="cfCheckoutForm" onsubmit="return false;" novalidate>
            <input type="hidden" id="cfCheckoutPid" name="pid" value="201">
            <input type="hidden" id="cfCheckoutCycle" name="billingcycle" value="monthly">
            <input type="hidden" id="cfCheckoutDomainType" name="domain_type" value="subdomain">
            <input type="hidden" id="cfCheckoutFinalDomain" name="domain" value="">
            <input type="hidden" id="cfCheckoutFinalTld" name="domain_tld" value="">

            <!-- Drawer Body -->
            <div class="cf-checkout-body">

                <!-- Alert / Error Banner -->
                <div class="cf-checkout-alert alert alert-danger d-none" id="cfCheckoutAlert" role="alert">
                    <i class="las la-exclamation-triangle me-2"></i>
                    <span id="cfCheckoutAlertText">Please complete all required fields.</span>
                </div>

                <!-- =========================================================================
                     STEP 1: PLAN CONFIGURATION & DOMAIN SETUP
                     ========================================================================= -->
                <div class="cf-checkout-step-pane active" id="cfCheckoutStep1">
                    
                    <!-- Selected Plan Summary Card -->
                    <div class="cf-plan-summary-card">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                            <div>
                                <span class="badge bg-primary-subtle text-primary fw-bold px-2 py-1 rounded-pill mb-1" id="cfSummaryCategory">
                                    Business Hosting
                                </span>
                                <h4 class="mb-0 fw-bold text-slate-900" id="cfSummaryPlanName">Business Starter</h4>
                            </div>
                            <div class="text-end">
                                <div class="cf-summary-price-box">
                                    <span class="fs-24 fw-extrabold text-primary" id="cfSummaryPrice">$7.50</span>
                                    <span class="text-muted fs-13" id="cfSummaryCycleLabel">/mo</span>
                                </div>
                            </div>
                        </div>
                        
                        <p class="text-muted fs-13 mb-3" id="cfSummarySpecs">
                            20 GB Pure NVMe • 1 vCPU • 2 GB RAM • 1 Hosted Website
                        </p>

                        <!-- Billing Cycle Toggle -->
                        <div class="cf-cycle-selector-wrap">
                            <span class="fs-13 fw-semibold text-slate-700 me-2">Billing Term:</span>
                            <div class="btn-group btn-group-sm cf-cycle-pill-group" role="group">
                                <button type="button" class="btn btn-outline-primary active" id="cfCycleMonthlyBtn" onclick="cfSetCycle('monthly')">
                                    Monthly
                                </button>
                                <button type="button" class="btn btn-outline-primary" id="cfCycleYearlyBtn" onclick="cfSetCycle('annually')">
                                    Annually <span class="badge bg-emerald text-white rounded-pill ms-1">-20% OFF</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Domain Association Choice -->
                    <div class="cf-domain-selection-section mt-4">
                        <label class="form-label fw-bold text-slate-900 mb-2">
                            <i class="las la-globe me-1 text-primary"></i> Choose Your Domain Option:
                        </label>
                        
                        <!-- Domain Option Tabs -->
                        <div class="row g-2 mb-3">
                            <div class="col-6 col-sm-3">
                                <div class="cf-domain-tab-card active" onclick="cfSelectDomainTab('subdomain')">
                                    <input type="radio" name="domain_opt_radio" id="optSubdomain" checked>
                                    <label for="optSubdomain">
                                        <i class="las la-bolt"></i>
                                        <span>Free Subdomain</span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-6 col-sm-3">
                                <div class="cf-domain-tab-card" onclick="cfSelectDomainTab('register')">
                                    <input type="radio" name="domain_opt_radio" id="optRegister">
                                    <label for="optRegister">
                                        <i class="las la-plus-circle"></i>
                                        <span>New Domain</span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-6 col-sm-3">
                                <div class="cf-domain-tab-card" onclick="cfSelectDomainTab('existing')">
                                    <input type="radio" name="domain_opt_radio" id="optExisting">
                                    <label for="optExisting">
                                        <i class="las la-check-circle"></i>
                                        <span>Own Domain</span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-6 col-sm-3">
                                <div class="cf-domain-tab-card" onclick="cfSelectDomainTab('transfer')">
                                    <input type="radio" name="domain_opt_radio" id="optTransfer">
                                    <label for="optTransfer">
                                        <i class="las la-exchange-alt"></i>
                                        <span>Transfer In</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Tab 1: Free Subdomain Input -->
                        <div class="cf-domain-tab-content active" id="tabContentSubdomain">
                            <div class="input-group">
                                <input type="text" class="form-control" id="cfInputSubdomain" placeholder="your-business" value="" aria-label="Subdomain">
                                <span class="input-group-text bg-light text-muted fw-semibold">.cloudflox.site</span>
                            </div>
                            <small class="text-success mt-1 d-block">
                                <i class="las la-check-circle"></i> Instant 60-second activation with free SSL certificate included.
                            </small>
                        </div>

                        <!-- Tab 2: Register New Domain Input with Instant RDAP Checker -->
                        <div class="cf-domain-tab-content d-none" id="tabContentRegister">
                            <div class="input-group">
                                <input type="text" class="form-control" id="cfInputRegister" placeholder="mybrandname" aria-label="Domain name">
                                <select class="form-select flex-grow-0 w-auto" id="cfSelectTld">
                                    <option value=".com">.com ($13.57/yr)</option>
                                    <option value=".net">.net ($15.49/yr)</option>
                                    <option value=".org">.org ($17.49/yr)</option>
                                    <option value=".cloud">.cloud ($9.99/yr)</option>
                                    <option value=".lk">.lk ($29.00/yr)</option>
                                    <option value=".io">.io ($39.99/yr)</option>
                                    <option value=".co">.co ($24.99/yr)</option>
                                    <option value=".online">.online ($3.99/yr)</option>
                                    <option value=".store">.store ($4.99/yr)</option>
                                </select>
                                <button type="button" class="btn btn-primary fw-bold px-3" id="cfBtnCheckDomain" onclick="cfCheckDomainAvailability()">
                                    <span class="btn-text">Check</span>
                                    <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                                </button>
                            </div>
                            <!-- Domain Availability Badge -->
                            <div class="mt-2 d-none" id="cfDomainResultBadge">
                                <!-- Dynamic check status injected here -->
                            </div>
                        </div>

                        <!-- Tab 3: Use Existing Domain Input -->
                        <div class="cf-domain-tab-content d-none" id="tabContentExisting">
                            <div class="input-group">
                                <input type="text" class="form-control" id="cfInputExisting" placeholder="example.com" aria-label="Existing domain">
                            </div>
                            <small class="text-muted mt-1 d-block">
                                <i class="las la-info-circle"></i> Point nameservers to <code>ns1.cloudflox.com</code> &amp; <code>ns2.cloudflox.com</code>.
                            </small>
                        </div>

                        <!-- Tab 4: Transfer In Domain Input -->
                        <div class="cf-domain-tab-content d-none" id="tabContentTransfer">
                            <div class="input-group">
                                <input type="text" class="form-control" id="cfInputTransfer" placeholder="transfer-domain.com" aria-label="Transfer domain">
                            </div>
                            <small class="text-muted mt-1 d-block">
                                <i class="las la-info-circle"></i> Requires an EPP / Auth code from your current domain registrar.
                            </small>
                        </div>
                    </div>

                    <!-- Step 1 Footer -->
                    <div class="cf-step-nav-footer mt-4">
                        <button type="button" class="btn btn-primary cf-btn-next w-100 py-2 fs-15 fw-bold" onclick="cfGoToStep(2)">
                            <span>Continue to Account Setup</span>
                            <i class="las la-arrow-right ms-2"></i>
                        </button>
                    </div>
                </div>

                <!-- =========================================================================
                     STEP 2: CUSTOMER ACCOUNT DETAILS
                     ========================================================================= -->
                <div class="cf-checkout-step-pane d-none" id="cfCheckoutStep2">
                    <h5 class="fw-bold text-slate-900 mb-3">Customer Information</h5>
                    <p class="text-muted fs-13 mb-3">Your credentials will be used for WHMCS client area &amp; cPanel root credentials.</p>

                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label fs-13 fw-semibold text-slate-700">First Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="firstname" id="cfCustFirstname" placeholder="John" required>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fs-13 fw-semibold text-slate-700">Last Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="lastname" id="cfCustLastname" placeholder="Doe" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fs-13 fw-semibold text-slate-700">Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" name="email" id="cfCustEmail" placeholder="john.doe@example.com" required>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fs-13 fw-semibold text-slate-700">Phone Number <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" name="phonenumber" id="cfCustPhone" placeholder="+94 77 123 4567" required>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fs-13 fw-semibold text-slate-700">Country <span class="text-danger">*</span></label>
                            <select class="form-select" name="country" id="cfCustCountry">
                                <option value="LK" selected>Sri Lanka</option>
                                <option value="US">United States</option>
                                <option value="GB">United Kingdom</option>
                                <option value="AU">Australia</option>
                                <option value="CA">Canada</option>
                                <option value="IN">India</option>
                                <option value="SG">Singapore</option>
                                <option value="AE">United Arab Emirates</option>
                                <option value="DE">Germany</option>
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fs-13 fw-semibold text-slate-700">City</label>
                            <input type="text" class="form-control" name="city" id="cfCustCity" placeholder="Colombo" value="Colombo">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fs-13 fw-semibold text-slate-700">Address</label>
                            <input type="text" class="form-control" name="address1" id="cfCustAddress" placeholder="123 Galle Road" value="Main Street">
                        </div>
                        <div class="col-12">
                            <label class="form-label fs-13 fw-semibold text-slate-700">Account Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control" name="password" id="cfCustPassword" placeholder="Minimum 6 characters" required>
                                <button type="button" class="btn btn-outline-secondary" onclick="cfTogglePasswordVisibility()">
                                    <i class="las la-eye" id="cfPasswordEye"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2 Navigation -->
                    <div class="cf-step-nav-footer d-flex gap-2 mt-4">
                        <button type="button" class="btn btn-outline-secondary w-50 py-2 fs-14 fw-semibold" onclick="cfGoToStep(1)">
                            <i class="las la-arrow-left me-1"></i> Back
                        </button>
                        <button type="button" class="btn btn-primary cf-btn-next w-50 py-2 fs-14 fw-bold" onclick="cfGoToStep(3)">
                            <span>Review &amp; Pay</span>
                            <i class="las la-arrow-right ms-1"></i>
                        </button>
                    </div>
                </div>

                <!-- =========================================================================
                     STEP 3: PAYMENT METHOD & FINAL ORDER CONFIRMATION
                     ========================================================================= -->
                <div class="cf-checkout-step-pane d-none" id="cfCheckoutStep3">
                    <h5 class="fw-bold text-slate-900 mb-2">Select Payment Method</h5>
                    
                    <!-- Payment Method Selector -->
                    <div class="row g-2 mb-4">
                        <div class="col-6">
                            <label class="cf-payment-method-card active" onclick="cfSelectPaymentMethod('card')">
                                <input type="radio" name="paymentmethod" value="card" checked>
                                <div class="cf-pm-icon text-primary"><i class="las la-credit-card"></i></div>
                                <span class="cf-pm-title">Credit / Debit Card</span>
                                <small class="text-muted">Visa, Mastercard, Amex</small>
                            </label>
                        </div>
                        <div class="col-6">
                            <label class="cf-payment-method-card" onclick="cfSelectPaymentMethod('payhere')">
                                <input type="radio" name="paymentmethod" value="payhere">
                                <div class="cf-pm-icon text-emerald"><i class="las la-money-bill-wave"></i></div>
                                <span class="cf-pm-title">PayHere / LKR</span>
                                <small class="text-muted">Local Bank / Genie / Frimi</small>
                            </label>
                        </div>
                        <div class="col-6">
                            <label class="cf-payment-method-card" onclick="cfSelectPaymentMethod('paypal')">
                                <input type="radio" name="paymentmethod" value="paypal">
                                <div class="cf-pm-icon text-info"><i class="lab la-paypal"></i></div>
                                <span class="cf-pm-title">PayPal</span>
                                <small class="text-muted">International Checkout</small>
                            </label>
                        </div>
                        <div class="col-6">
                            <label class="cf-payment-method-card" onclick="cfSelectPaymentMethod('banktransfer')">
                                <input type="radio" name="paymentmethod" value="banktransfer">
                                <div class="cf-pm-icon text-slate-700"><i class="las la-university"></i></div>
                                <span class="cf-pm-title">Bank Transfer</span>
                                <small class="text-muted">Direct Deposit / Wire</small>
                            </label>
                        </div>
                    </div>

                    <!-- Final Order Review Receipt -->
                    <div class="cf-order-receipt-box">
                        <h6 class="fw-bold text-slate-900 mb-2 border-bottom pb-2">Order Summary</h6>
                        
                        <div class="d-flex justify-content-between fs-14 py-1">
                            <span class="text-muted" id="cfReceiptPlanTitle">Business Starter (Monthly)</span>
                            <span class="fw-bold text-slate-900" id="cfReceiptPlanPrice">$7.50</span>
                        </div>
                        <div class="d-flex justify-content-between fs-14 py-1" id="cfReceiptDomainRow" style="display: none;">
                            <span class="text-muted" id="cfReceiptDomainName">Domain Registration</span>
                            <span class="fw-bold text-slate-900" id="cfReceiptDomainPrice">$13.57</span>
                        </div>
                        <div class="d-flex justify-content-between fs-14 py-1">
                            <span class="text-muted">Setup &amp; Provisioning</span>
                            <span class="text-emerald fw-bold">FREE ($0.00)</span>
                        </div>
                        <div class="d-flex justify-content-between fs-14 py-1">
                            <span class="text-muted">24/7 Priority Support SLA</span>
                            <span class="text-emerald fw-bold">INCLUDED</span>
                        </div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between fs-16 py-1">
                            <span class="fw-bold text-slate-900">Total Due Today:</span>
                            <span class="fw-extrabold text-primary fs-20" id="cfReceiptTotal">$7.50</span>
                        </div>
                    </div>

                    <!-- Security & Money-Back Badges -->
                    <div class="d-flex align-items-center justify-content-center gap-3 mt-3 text-muted fs-12">
                        <span><i class="las la-lock text-success"></i> 256-Bit SSL Encrypted</span>
                        <span>•</span>
                        <span><i class="las la-shield-alt text-primary"></i> 30-Day Money-Back Guarantee</span>
                    </div>

                    <!-- Step 3 Navigation / Submit -->
                    <div class="cf-step-nav-footer d-flex gap-2 mt-4">
                        <button type="button" class="btn btn-outline-secondary w-40 py-2 fs-14 fw-semibold" onclick="cfGoToStep(2)">
                            <i class="las la-arrow-left me-1"></i> Back
                        </button>
                        <button type="button" class="btn btn-emerald cf-btn-submit w-60 py-2 fs-15 fw-bold text-white" id="cfBtnSubmitOrder" onclick="cfSubmitCheckoutOrder()">
                            <span class="btn-text"><i class="las la-bolt me-1"></i> Complete &amp; Deploy Server</span>
                            <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                        </button>
                    </div>
                </div>

                <!-- =========================================================================
                     STEP 4: ORDER SUCCESS / CONFIRMATION STATE
                     ========================================================================= -->
                <div class="cf-checkout-step-pane d-none" id="cfCheckoutSuccess">
                    <div class="text-center py-4">
                        <div class="cf-success-checkmark-circle mb-3">
                            <i class="las la-check"></i>
                        </div>
                        <h4 class="fw-extrabold text-slate-900 mb-1">Server Deployment Initiated!</h4>
                        <p class="text-muted fs-14 max-w-400 mx-auto mb-4" id="cfSuccessMessage">
                            Your order has been registered in our cloud infrastructure. An automated confirmation and invoice receipt has been dispatched to your email.
                        </p>

                        <!-- Order Details Badge Box -->
                        <div class="cf-success-details-card mb-4 text-start">
                            <div class="d-flex justify-content-between py-1 border-bottom">
                                <span class="text-muted">Order ID:</span>
                                <span class="fw-bold text-slate-900" id="cfSuccessOrderId">#4092</span>
                            </div>
                            <div class="d-flex justify-content-between py-1 border-bottom">
                                <span class="text-muted">Invoice ID:</span>
                                <span class="fw-bold text-slate-900" id="cfSuccessInvoiceId">#8921</span>
                            </div>
                            <div class="d-flex justify-content-between py-1 border-bottom">
                                <span class="text-muted">Selected Plan:</span>
                                <span class="fw-bold text-primary" id="cfSuccessPlan">Business Starter</span>
                            </div>
                            <div class="d-flex justify-content-between py-1">
                                <span class="text-muted">Assigned Domain:</span>
                                <span class="fw-bold text-slate-900" id="cfSuccessDomain">mybusiness.cloudflox.site</span>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex flex-column gap-2">
                            <a href="#" class="btn btn-primary fw-bold py-2 fs-15 shadow-sm" id="cfSuccessInvoiceBtn" target="_blank">
                                <i class="las la-file-invoice-dollar me-1"></i> View Invoice &amp; Pay Online
                            </a>
                            <a href="https://my.cloudflox.com/clientarea.php" class="btn btn-outline-secondary fw-semibold py-2 fs-14" target="_blank">
                                <i class="las la-user me-1"></i> Open Client Dashboard
                            </a>
                            <button type="button" class="btn btn-link text-muted fs-13 mt-2" onclick="cfCloseCheckoutModal()">
                                Close &amp; Return to Website
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </form>

    </div>
</div>
