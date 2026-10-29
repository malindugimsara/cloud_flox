<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'include/url.inc.php'; 
require_once __DIR__ . '/api/whmcs-pricing.php';
$whmcsDomains = WHMCSPricing::getDomainPricing();
$whmcsHosting = WHMCSPricing::getHostingProducts();

$isLoggedIn = !empty($_SESSION['cf_client_logged_in']);
$loggedInClientId = (int)($_SESSION['cf_client_id'] ?? 0);
$loggedInClientName = $_SESSION['cf_client_name'] ?? '';
$loggedInClientEmail = $_SESSION['cf_client_email'] ?? '';

$clientProfile = [
    'firstname'   => '',
    'lastname'    => '',
    'companyname' => '',
    'email'       => $loggedInClientEmail,
    'phonenumber' => '',
    'address1'    => '',
    'address2'    => '',
    'city'        => '',
    'state'       => '',
    'postcode'    => '',
    'country'     => 'LK'
];

if ($isLoggedIn && $loggedInClientId > 0) {
    try {
        $mysqli = @new mysqli('127.0.0.1', 'cfwhmcs_whmcs', '+V)Rb%K3wFiK', 'cfwhmcs_whmcs3');
        if (!$mysqli->connect_error) {
            $stmt = $mysqli->prepare("SELECT id, firstname, lastname, companyname, email, phonenumber, address1, address2, city, state, postcode, country FROM tblclients WHERE id = ? LIMIT 1");
            if ($stmt) {
                $stmt->bind_param('i', $loggedInClientId);
                $stmt->execute();
                $r = $stmt->get_result();
                if ($r && $row = $r->fetch_assoc()) {
                    $clientProfile = array_merge($clientProfile, $row);
                    if (empty($loggedInClientName) || $loggedInClientName === 'Client Account') {
                        $loggedInClientName = trim(($row['firstname'] ?? '') . ' ' . ($row['lastname'] ?? ''));
                        $_SESSION['cf_client_name'] = $loggedInClientName;
                    }
                }
                $stmt->close();
            }
        }
    } catch (Exception $e) {}
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
         CHECKOUT MAIN CONTAINER (Stepped Flow Matching cfwhmcs)
         Step 1: Domain Name Connection (configureproductdomain.tpl)
         Step 2: Plan & Billing Cycle Configuration (configureproduct.tpl)
         Step 3: Customer Account (register-user-fields.tpl / viewcart.tpl)
         Step 4: Payment Method (form-payment-gateway.tpl / viewcart.tpl)
         ========================================================================= -->
    <section class="cf-checkout-body-wrap py-5">
        <div class="container cf-checkout-container">

            <!-- 4-Steps Visual Progress Indicator -->
            <div class="cf-checkout-steps-bar" role="navigation" aria-label="Checkout Progress">
                <!-- Step 1: Domain -->
                <div class="cf-step-pill active" id="pstep1" onclick="pageGoToStep(1)">
                    <span class="cf-step-num" id="pnum1"><i class="las la-globe"></i></span>
                    <div class="cf-step-text">
                        <span class="cf-step-label">1. Domain</span>
                        <small class="cf-step-sub">Connection</small>
                    </div>
                </div>
                <div class="cf-step-connector" id="pconn1"></div>

                <!-- Step 2: Plan & Config -->
                <div class="cf-step-pill" id="pstep2" onclick="pageGoToStep(2)">
                    <span class="cf-step-num" id="pnum2"><i class="las la-sliders-h"></i></span>
                    <div class="cf-step-text">
                        <span class="cf-step-label">2. Hosting Plan</span>
                        <small class="cf-step-sub">Billing Term</small>
                    </div>
                </div>
                <div class="cf-step-connector" id="pconn2"></div>

                <!-- Step 3: Account -->
                <div class="cf-step-pill" id="pstep3" onclick="pageGoToStep(3)">
                    <span class="cf-step-num" id="pnum3"><i class="las la-user-circle"></i></span>
                    <div class="cf-step-text">
                        <span class="cf-step-label">3. Account</span>
                        <small class="cf-step-sub">Client Portal</small>
                    </div>
                </div>
                <div class="cf-step-connector" id="pconn3"></div>

                <!-- Step 4: Payment -->
                <div class="cf-step-pill" id="pstep4" onclick="pageGoToStep(4)">
                    <span class="cf-step-num" id="pnum4"><i class="las la-credit-card"></i></span>
                    <div class="cf-step-text">
                        <span class="cf-step-label">4. Payment</span>
                        <small class="cf-step-sub">USD Gateways</small>
                    </div>
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
                    <input type="hidden" id="pageEppCode" name="epp_code" value="">
                    <input type="hidden" id="pageAccountAction" name="account_action" value="register">

                    <div class="row g-4">
                        
                        <!-- LEFT COLUMN: STEP BY STEP PANES -->
                        <div class="col-12 col-lg-8">

                            <!-- =========================================================================
                                 STEP 1: DOMAIN NAME CONNECTION (configureproductdomain.tpl)
                                 ========================================================================= -->
                            <div class="cf-checkout-card active-step-card" id="stepBox1">
                                <div class="cf-card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                                    <div>
                                        <h4 class="cf-card-title"><i class="las la-globe text-primary me-2"></i>Step 1: Domain Name Connection</h4>
                                        <p class="text-secondary fs-13 mb-0">Choose how to link a domain to your hosting server: Register new, Transfer, or Use your own domain.</p>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="cf-active-step-chip"><span class="cf-pulse-dot"></span> Active Step 1 of 4</div>
                                        <span class="cf-card-badge-secure"><i class="las la-shield-alt text-emerald me-1"></i>WHMCS Standard</span>
                                    </div>
                                </div>

                                <!-- Hosting Plan Context Banner (When plan is selected) -->
                                <div class="cf-domain-plan-banner" id="pageStep1PlanBanner" style="display: none;">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="cf-item-icon-box bg-primary text-white" style="width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 22px;">
                                            <i class="las la-server"></i>
                                        </div>
                                        <div>
                                            <div class="d-flex align-items-center gap-2 mb-0.5">
                                                <span class="cf-domain-plan-badge" id="step1BannerBadge">WEB HOSTING</span>
                                                <strong class="text-slate-900 fs-15" id="step1BannerPlanName">Business Starter</strong>
                                            </div>
                                            <span class="text-secondary fs-12" id="step1BannerSpecs">20 GB Pure NVMe • 1 vCPU &amp; 2 GB RAM</span>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <span class="fs-16 fw-bold text-primary" id="step1BannerPrice">$5.99</span>
                                        <small class="text-secondary d-block fs-11" id="step1BannerCycle">/month</small>
                                    </div>
                                </div>

                                <!-- Currently Connected Domain Confirmation Strip (If already chosen) -->
                                <div class="cf-connected-domain-strip" id="pageConnectedDomainStrip" style="display: none;">
                                    <div class="d-flex align-items-center gap-3">
                                        <div style="width: 40px; height: 40px; border-radius: 50%; background: #DCFCE7; color: #15803D; display: flex; align-items: center; justify-content: center; font-size: 22px;">
                                            <i class="las la-check-circle"></i>
                                        </div>
                                        <div>
                                            <div class="d-flex align-items-center gap-2 mb-0.5">
                                                <span class="cf-connected-domain-name" id="connectedDomainName">yourdomain.com</span>
                                                <span class="badge bg-primary text-white rounded-pill fs-11" id="connectedDomainTypeBadge">New Registration</span>
                                            </div>
                                            <span class="text-secondary fs-12" id="connectedDomainNote">1 Year Registration • Free WHOIS Privacy &amp; Anycast DNS</span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-3">
                                        <strong class="fs-17 text-slate-900" id="connectedDomainFeeText">$14.93</strong>
                                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 fs-12" onclick="pageToggleDomainOptions(true)">
                                            <i class="las la-pen me-1"></i> Change
                                        </button>
                                    </div>
                                </div>

                                <!-- 3 Domain Options Segmented Selector (Matching WHMCS configureproductdomain.tpl) -->
                                <div id="pageDomainOptionsWrap">
                                    <div class="cf-domain-options-tabs">
                                        <!-- Option 1: Register New Domain -->
                                        <div class="cf-domain-opt-tab active" id="domainOptTabRegister" onclick="pageSetDomainOption('register')">
                                            <span class="cf-domain-opt-badge-pill">RECOMMENDED</span>
                                            <div class="cf-domain-opt-top">
                                                <div class="cf-domain-opt-icon">
                                                    <i class="las la-plus-circle"></i>
                                                </div>
                                                <div class="cf-domain-opt-radio">
                                                    <i class="las la-check"></i>
                                                </div>
                                            </div>
                                            <h5 class="cf-domain-opt-title">Register New Domain</h5>
                                            <p class="cf-domain-opt-desc">I want to register a brand new domain name with CloudFlox.</p>
                                            <span class="cf-domain-opt-price-tag">From $1.31/yr • Free Privacy</span>
                                        </div>

                                        <!-- Option 2: Transfer Domain -->
                                        <div class="cf-domain-opt-tab" id="domainOptTabTransfer" onclick="pageSetDomainOption('transfer')">
                                            <span class="cf-domain-opt-badge-pill transfer">TRANSFER</span>
                                            <div class="cf-domain-opt-top">
                                                <div class="cf-domain-opt-icon">
                                                    <i class="las la-exchange-alt"></i>
                                                </div>
                                                <div class="cf-domain-opt-radio">
                                                    <i class="las la-check"></i>
                                                </div>
                                            </div>
                                            <h5 class="cf-domain-opt-title">Transfer Domain</h5>
                                            <p class="cf-domain-opt-desc">I want to transfer my domain from another registrar to CloudFlox.</p>
                                            <span class="cf-domain-opt-price-tag">+1 Year Free Renewal</span>
                                        </div>

                                        <!-- Option 3: Use Existing Domain (Own Domain) -->
                                        <div class="cf-domain-opt-tab" id="domainOptTabOwn" onclick="pageSetDomainOption('owndomain')">
                                            <span class="cf-domain-opt-badge-pill free">FREE ($0.00)</span>
                                            <div class="cf-domain-opt-top">
                                                <div class="cf-domain-opt-icon">
                                                    <i class="las la-link"></i>
                                                </div>
                                                <div class="cf-domain-opt-radio">
                                                    <i class="las la-check"></i>
                                                </div>
                                            </div>
                                            <h5 class="cf-domain-opt-title">Use My Own Domain</h5>
                                            <p class="cf-domain-opt-desc">I will use my existing domain and update my DNS nameservers.</p>
                                            <span class="cf-domain-opt-price-tag free">100% FREE ($0.00 Domain Fee)</span>
                                        </div>
                                    </div>

                                    <!-- PANE 1: REGISTER A NEW DOMAIN -->
                                    <div class="cf-domain-search-card" id="domainPaneRegister">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <h5 class="fw-bold text-slate-900 fs-16 mb-0">
                                                <i class="las la-search text-primary me-1.5"></i>Search Domain Availability
                                            </h5>
                                            <span class="badge bg-blue-subtle text-primary rounded-pill fs-11">Instant ICANN Lookup</span>
                                        </div>
                                        <p class="text-secondary fs-13 mb-3">Type your brand or project name to check instant live availability.</p>

                                        <div class="cf-domain-input-group">
                                            <input type="text" class="form-control" id="pageRegisterSld" placeholder="Search your domain name (e.g. mycompany)" autocomplete="off" autocapitalize="none">
                                            <select class="form-select cf-tld-select" id="pageRegisterTld" onchange="pageSyncTldPill(this.value)">
                                                <?php foreach ($whmcsDomains as $tldKey => $tldData): ?>
                                                    <option value="<?php echo htmlspecialchars($tldData['extension']); ?>" data-price="<?php echo htmlspecialchars($tldData['price']); ?>" <?php echo ($tldKey === 'com') ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($tldData['extension']); ?> (<?php echo htmlspecialchars($tldData['price']); ?>)
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <button type="button" class="cf-btn-domain-check" id="btnCheckDomain" onclick="pageCheckDomainAvailability()">
                                                <span class="btn-check-text"><i class="las la-search me-1"></i> Check</span>
                                                <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                                            </button>
                                        </div>

                                        <!-- Popular TLD Quick Shortcut Pills -->
                                        <div class="cf-quick-tld-pills">
                                            <span class="text-secondary fs-11 fw-bold me-1">Popular TLDs:</span>
                                            <?php 
                                            $popularKeys = ['com', 'net', 'org', 'io', 'online', 'site'];
                                            foreach ($popularKeys as $pk):
                                                if (isset($whmcsDomains[$pk])):
                                                    $item = $whmcsDomains[$pk];
                                            ?>
                                                <div class="cf-quick-tld-pill <?php echo ($pk === 'com') ? 'active' : ''; ?>" data-tld="<?php echo $item['extension']; ?>" onclick="pagePickTldPill('<?php echo $item['extension']; ?>')">
                                                    <span><?php echo $item['extension']; ?></span>
                                                    <span class="tld-rate"><?php echo $item['price']; ?></span>
                                                </div>
                                            <?php 
                                                endif;
                                            endforeach; 
                                            ?>
                                        </div>

                                        <!-- Live Search Result Box -->
                                        <div class="cf-domain-result-box d-none" id="pageDomainResultBox">
                                            <div class="d-flex align-items-center gap-3">
                                                <div id="domainResultIconWrap" style="width: 44px; height: 44px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                                                    <i class="las la-check" id="domainResultIcon"></i>
                                                </div>
                                                <div>
                                                    <div class="d-flex align-items-center gap-2 mb-0.5">
                                                        <h5 class="fw-bold text-slate-900 mb-0 fs-16" id="domainResultTitle">domain.com is available!</h5>
                                                        <span class="badge bg-success text-white rounded-pill fs-11" id="domainResultBadge">Available</span>
                                                    </div>
                                                    <span class="text-secondary fs-12" id="domainResultSubtext">Includes Free WHOIS Privacy Protection &amp; Anycast DNS</span>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center gap-3">
                                                <strong class="fs-18 text-slate-900" id="domainResultPrice">$14.93</strong>
                                                <button type="button" class="btn btn-primary fw-bold px-4 py-2 rounded-pill fs-13 shadow-sm" id="btnSelectResultDomain" onclick="pageConnectRegisteredDomain()">
                                                    Connect &amp; Continue <i class="las la-arrow-right ms-1"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Domain Suggestions Grid (Shown if domain is unavailable) -->
                                        <div class="d-none mt-3 pt-3 border-top" id="domainSuggestionsWrap">
                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                <span class="fs-12 fw-bold text-slate-700">Alternative Extensions Available For You:</span>
                                                <small class="text-muted fs-11">Click to select</small>
                                            </div>
                                            <div class="cf-suggestions-grid" id="domainSuggestionsList"></div>
                                        </div>
                                    </div>

                                    <!-- PANE 2: TRANSFER YOUR DOMAIN -->
                                    <div class="cf-domain-search-card d-none" id="domainPaneTransfer">
                                        <h5 class="fw-bold text-slate-900 fs-16 mb-2">
                                            <i class="las la-exchange-alt text-primary me-1.5"></i>Transfer Domain to CloudFlox
                                        </h5>
                                        <p class="text-secondary fs-13 mb-3">Transfer your domain from your current registrar (e.g. GoDaddy, Namecheap) to manage everything under one unified CloudFlox portal.</p>

                                        <!-- 3-Step Transfer Guidance Strip -->
                                        <div class="p-3 bg-light rounded-3 border mb-3">
                                            <div class="row g-2 text-secondary fs-12">
                                                <div class="col-sm-4 d-flex align-items-center gap-2">
                                                    <span class="badge bg-primary rounded-circle" style="width:20px;height:20px;display:flex;align-items:center;justify-content:center;font-size:10px;">1</span>
                                                    <span>Unlock domain at current registrar</span>
                                                </div>
                                                <div class="col-sm-4 d-flex align-items-center gap-2">
                                                    <span class="badge bg-primary rounded-circle" style="width:20px;height:20px;display:flex;align-items:center;justify-content:center;font-size:10px;">2</span>
                                                    <span>Obtain EPP / Auth code</span>
                                                </div>
                                                <div class="col-sm-4 d-flex align-items-center gap-2">
                                                    <span class="badge bg-primary rounded-circle" style="width:20px;height:20px;display:flex;align-items:center;justify-content:center;font-size:10px;">3</span>
                                                    <span>+1-Year Extension added</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row g-3">
                                            <div class="col-12 col-md-7">
                                                <div class="cf-domain-input-group">
                                                    <input type="text" class="form-control" id="pageTransferSld" placeholder="Enter your domain (e.g. mycompany)" autocomplete="off" autocapitalize="none">
                                                    <select class="form-select cf-tld-select" id="pageTransferTld">
                                                        <?php foreach ($whmcsDomains as $tldKey => $tldData): ?>
                                                            <option value="<?php echo htmlspecialchars($tldData['extension']); ?>" data-transfer="<?php echo (float)$tldData['transfer']; ?>" <?php echo ($tldKey === 'com') ? 'selected' : ''; ?>>
                                                                <?php echo htmlspecialchars($tldData['extension']); ?> ($<?php echo number_format($tldData['transfer'], 2); ?>)
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-5">
                                                <div class="cf-domain-input-group">
                                                    <input type="text" class="form-control" id="pageTransferEpp" placeholder="EPP / Authorization Code" autocomplete="off">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="p-3 bg-light rounded-3 border mt-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="las la-info-circle text-primary fs-18"></i>
                                                <span class="fs-12 text-slate-700">Domain transfer includes a <strong>1-year renewal extension</strong> from your current expiration date.</span>
                                            </div>
                                            <div>
                                                <button type="button" class="btn btn-primary fw-bold px-4 py-2 rounded-pill fs-13 shadow-sm" onclick="pageConnectTransferDomain()">
                                                    Connect Transfer &amp; Continue <i class="las la-arrow-right ms-1"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- PANE 3: USE EXISTING DOMAIN & UPDATE NAMESERVERS (owndomain) -->
                                    <div class="cf-domain-search-card d-none" id="domainPaneOwn">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <h5 class="fw-bold text-slate-900 fs-16 mb-0">
                                                <i class="las la-link text-primary me-1.5"></i>Use Your Existing Domain
                                            </h5>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill fs-11">Zero Domain Charge</span>
                                        </div>
                                        <p class="text-secondary fs-13 mb-3">Keep your domain registered where it is (Namecheap, GoDaddy, Cloudflare, etc.) and point your nameservers to CloudFlox.</p>

                                        <!-- Domain Input -->
                                        <label class="fs-12 fw-bold text-slate-700 mb-1 d-block">1. Enter your existing domain name:</label>
                                        <div class="cf-domain-input-group mb-3">
                                            <span class="input-group-text bg-light text-secondary border-0 px-3 fw-bold">www.</span>
                                            <input type="text" class="form-control" id="pageOwnDomainInput" placeholder="yourcompany.com" autocomplete="off" autocapitalize="none">
                                            <button type="button" class="cf-btn-domain-check" onclick="pageConnectOwnDomain()">
                                                <span>Connect Domain <i class="las la-arrow-right ms-1"></i></span>
                                            </button>
                                        </div>

                                        <!-- Interactive Nameservers Box with 1-Click Copy -->
                                        <div class="cf-nameservers-box">
                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                <span class="fs-12 fw-bold text-slate-800">
                                                    <i class="las la-network-wired text-primary me-1"></i>2. Nameservers to set at your domain registrar:
                                                </span>
                                                <span class="badge bg-light text-secondary fs-10">Standard Anycast DNS</span>
                                            </div>
                                            <div class="cf-ns-item">
                                                <span>ns1.cloudflox.com</span>
                                                <button type="button" class="cf-btn-copy-ns" onclick="pageCopyText('ns1.cloudflox.com', this)">
                                                    <i class="las la-copy me-1"></i>Copy
                                                </button>
                                            </div>
                                            <div class="cf-ns-item mb-0">
                                                <span>ns2.cloudflox.com</span>
                                                <button type="button" class="cf-btn-copy-ns" onclick="pageCopyText('ns2.cloudflox.com', this)">
                                                    <i class="las la-copy me-1"></i>Copy
                                                </button>
                                            </div>
                                            <small class="text-muted fs-11 d-block mt-2">
                                                * You only pay for your web hosting package. Domain fee is strictly <strong>$0.00</strong>.
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Step 1 Next Step Action Button -->
                                <div class="mt-4 pt-3 border-top" id="step1ContinueBar" style="display: none;">
                                    <button type="button" class="cf-btn-next-step w-100" onclick="pageGoToStep(2)">
                                        <span>Continue to Plan &amp; Configuration</span>
                                        <i class="las la-arrow-right ms-2"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- =========================================================================
                                 STEP 2: PLAN & BILLING CONFIGURATION (configureproduct.tpl)
                                 ========================================================================= -->
                            <div class="cf-checkout-card d-none" id="stepBox2">
                                <div class="cf-card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                                    <div>
                                        <h4 class="cf-card-title"><i class="las la-sliders-h text-primary me-2"></i>Step 2: Configure Plan &amp; Billing Cycle</h4>
                                        <p class="text-secondary fs-13 mb-0">Select your preferred billing cycle and review server specifications.</p>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="cf-active-step-chip"><span class="cf-pulse-dot"></span> Active Step 2 of 4</div>
                                        <span class="cf-card-badge-secure"><i class="las la-check-circle text-emerald me-1"></i>Verified Rate</span>
                                    </div>
                                </div>

                                <!-- Connected Domain Summary Banner in Step 2 -->
                                <div class="p-3 rounded-3 border bg-light d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
                                    <div class="d-flex align-items-center gap-2.5">
                                        <div style="width: 34px; height: 34px; border-radius: 50%; background: #EFF6FF; color: #2563EB; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                                            <i class="las la-globe"></i>
                                        </div>
                                        <div>
                                            <span class="text-secondary fs-11 d-block text-uppercase fw-bold letter-spacing-1">Connected Domain:</span>
                                            <strong class="text-slate-900 fs-14" id="step2DomainText">yourdomain.com</strong>
                                            <span class="badge bg-primary text-white rounded-pill ms-1 fs-10" id="step2DomainBadge">Registered</span>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 fs-11" onclick="pageGoToStep(1)">
                                        <i class="las la-sync-alt me-1"></i> Change Domain
                                    </button>
                                </div>

                                <!-- Hosting Plan Showcase Card -->
                                <div class="cf-checkout-card p-4 border bg-white mb-4 shadow-xs" id="step2HostingShowcase">
                                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <div style="width: 48px; height: 48px; border-radius: 12px; background: #EEF2FF; color: #4F46E5; display: flex; align-items: center; justify-content: center; font-size: 26px;">
                                                <i class="las la-server"></i>
                                            </div>
                                            <div>
                                                <div class="d-flex align-items-center gap-2 mb-0.5">
                                                    <h5 class="fw-bold text-slate-900 mb-0 fs-17" id="step2PlanName">Business Starter</h5>
                                                    <span class="cf-badge-tag cf-badge-tag-primary" id="step2PlanBadge">WEB HOSTING</span>
                                                </div>
                                                <span class="text-secondary fs-12" id="step2PlanSpecs">20 GB Pure NVMe Storage • 1 vCPU • 2 GB Dedicated RAM</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Billing Cycle Selector (Monthly vs Annually with 20% discount) -->
                                    <label class="fs-13 fw-bold text-slate-800 d-block mb-2">
                                        <i class="las la-calendar-alt text-primary me-1"></i>Choose Billing Term:
                                    </label>
                                    <div class="cf-cycle-cards-grid">
                                        <!-- Monthly Card -->
                                        <div class="cf-cycle-card active" id="cycleCardMonthly" onclick="pageSetBillingCycle('monthly')">
                                            <div class="d-flex align-items-center justify-content-between mb-1">
                                                <strong class="fs-14 text-slate-900">Monthly Billing</strong>
                                                <span class="fs-16 fw-bold text-slate-900" id="cyclePriceMonthlyText">$5.99/mo</span>
                                            </div>
                                            <small class="text-secondary fs-12 d-block">Flexible month-to-month payment. Cancel anytime without penalty.</small>
                                        </div>

                                        <!-- Annual Card (Save 20%) -->
                                        <div class="cf-cycle-card" id="cycleCardAnnually" onclick="pageSetBillingCycle('annually')">
                                            <span class="cf-cycle-save-badge">Save 20% + 2 Mo Free</span>
                                            <div class="d-flex align-items-center justify-content-between mb-1">
                                                <strong class="fs-14 text-slate-900">Annual Billing</strong>
                                                <span class="fs-16 fw-bold text-primary" id="cyclePriceAnnuallyText">$61.10/yr</span>
                                            </div>
                                            <small class="text-secondary fs-12 d-block">Billed annually ($5.09/mo equivalent). Save 20% compared to monthly.</small>
                                        </div>
                                    </div>

                                    <!-- Included Features Checklist -->
                                    <div class="pt-3 border-top">
                                        <span class="fs-12 fw-bold text-slate-700 d-block mb-2">Included with this Package:</span>
                                        <div class="cf-plan-features-grid">
                                            <div class="cf-plan-feat-item">
                                                <i class="las la-check-circle"></i>
                                                <span>Pure NVMe Gen4 Storage</span>
                                            </div>
                                            <div class="cf-plan-feat-item">
                                                <i class="las la-check-circle"></i>
                                                <span>Free AutoSSL &amp; ImunifyAV</span>
                                            </div>
                                            <div class="cf-plan-feat-item">
                                                <i class="las la-check-circle"></i>
                                                <span>cPanel Control Panel</span>
                                            </div>
                                            <div class="cf-plan-feat-item">
                                                <i class="las la-check-circle"></i>
                                                <span>Free Daily Backups</span>
                                            </div>
                                            <div class="cf-plan-feat-item">
                                                <i class="las la-check-circle"></i>
                                                <span>Free Website Migration</span>
                                            </div>
                                            <div class="cf-plan-feat-item">
                                                <i class="las la-check-circle"></i>
                                                <span>24/7/365 Tech Support</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Switch Hosting Tier Pills -->
                                    <div class="mt-4 pt-3 border-top">
                                        <span class="fs-12 fw-bold text-slate-700 d-block mb-2">Need a different tier? Switch instantly:</span>
                                        <div class="d-flex flex-wrap gap-2">
                                            <?php foreach ($whmcsHosting as $hKey => $hItem): ?>
                                                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 fs-12" id="switchPlanBtn_<?php echo $hItem['pid']; ?>" onclick="pageSelectHostingPlan('<?php echo $hItem['pid']; ?>')">
                                                    <?php echo htmlspecialchars($hItem['name']); ?> (<?php echo $hItem['monthly_formatted']; ?>/mo)
                                                </button>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- Step 2 Navigation Buttons -->
                                <div class="d-flex justify-content-between align-items-center gap-3 pt-2 border-top flex-wrap">
                                    <button type="button" class="cf-btn-back-step" onclick="pageGoToStep(1)">
                                        <i class="las la-arrow-left me-1"></i> Back to Domain Connection
                                    </button>
                                    <button type="button" class="cf-btn-next-step" onclick="pageGoToStep(3)">
                                        <span>Continue to Account Details</span>
                                        <i class="las la-arrow-right ms-2"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- =========================================================================
                                 STEP 3: CUSTOMER ACCOUNT (register-user-fields.tpl)
                                 ========================================================================= -->
                            <div class="cf-checkout-card d-none" id="stepBox3">
                                <div class="cf-card-header d-flex align-items-center justify-content-between flex-wrap gap-3">
                                    <div>
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <h4 class="cf-card-title mb-0"><i class="las la-user-circle text-primary me-2"></i>Step 3: Customer Account</h4>
                                            <div class="cf-active-step-chip"><span class="cf-pulse-dot"></span> Active Step 3 of 4</div>
                                        </div>
                                        <p class="text-secondary fs-13 mb-0" id="step3HeaderSubtext">
                                            <?php echo $isLoggedIn ? 'You are authenticated with your CloudFlox account.' : 'Create your client portal account or sign in to an existing profile.'; ?>
                                        </p>
                                    </div>
                                    <!-- Mode Switcher Tabs -->
                                    <div class="cf-auth-tabs-wrap mb-0 <?php echo $isLoggedIn ? 'd-none' : ''; ?>" id="step3AuthTabs">
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
                                            <div class="d-flex align-items-center gap-2">
                                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 fs-12 fw-semibold" id="btnToggleEditProfileCheckout" onclick="pageToggleCheckoutEditProfile()">
                                                    <i class="las la-user-edit me-1"></i> Edit Personal &amp; Billing Info
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 fs-12" onclick="pageLogout()">
                                                    <i class="las la-sign-out-alt me-1"></i> Switch Account
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Summary of Personal & Billing Info -->
                                        <div class="mt-3 pt-3 border-top" id="checkoutProfileSummary">
                                            <div class="row g-2 text-secondary fs-12">
                                                <div class="col-sm-4">
                                                    <span class="text-muted d-block fs-11 fw-bold text-uppercase">Contact Phone</span>
                                                    <strong class="text-slate-800" id="summaryPhoneText"><?php echo htmlspecialchars($clientProfile['phonenumber'] ?: 'Not specified'); ?></strong>
                                                </div>
                                                <div class="col-sm-4">
                                                    <span class="text-muted d-block fs-11 fw-bold text-uppercase">Organization</span>
                                                    <strong class="text-slate-800" id="summaryCompanyText"><?php echo htmlspecialchars($clientProfile['companyname'] ?: 'Individual Account'); ?></strong>
                                                </div>
                                                <div class="col-sm-4">
                                                    <span class="text-muted d-block fs-11 fw-bold text-uppercase">Billing Address</span>
                                                    <strong class="text-slate-800" id="summaryAddressText"><?php 
                                                        $addrParts = array_filter([$clientProfile['address1'] ?? '', $clientProfile['city'] ?? '', $clientProfile['country'] ?? '']);
                                                        echo htmlspecialchars(!empty($addrParts) ? implode(', ', $addrParts) : 'Not specified');
                                                    ?></strong>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Expandable Edit Profile Box for Checkout -->
                                        <div id="checkoutProfileEditBox" class="d-none mt-3 pt-3 border-top">
                                            <div class="d-flex align-items-center justify-content-between mb-3">
                                                <h6 class="fw-bold text-slate-900 mb-0 fs-14">
                                                    <i class="las la-edit text-primary me-1"></i> Edit Personal &amp; Billing Details
                                                </h6>
                                                <button type="button" class="btn btn-sm btn-link text-muted p-0 text-decoration-none fs-12" onclick="pageToggleCheckoutEditProfile()">
                                                    <i class="las la-times me-1"></i> Close
                                                </button>
                                            </div>

                                            <div id="chkEditAlert" class="alert d-none py-2 px-3 fs-12 rounded-2 mb-3"></div>

                                            <div class="row g-3">
                                                <div class="col-sm-6">
                                                    <label class="form-label fs-12 fw-bold text-slate-800 mb-1">First Name <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control form-control-sm" id="chkEditFname" value="<?php echo htmlspecialchars($clientProfile['firstname']); ?>" placeholder="First Name">
                                                </div>
                                                <div class="col-sm-6">
                                                    <label class="form-label fs-12 fw-bold text-slate-800 mb-1">Last Name <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control form-control-sm" id="chkEditLname" value="<?php echo htmlspecialchars($clientProfile['lastname']); ?>" placeholder="Last Name">
                                                </div>
                                                <div class="col-sm-6">
                                                    <label class="form-label fs-12 fw-bold text-slate-800 mb-1">Phone Number <span class="text-danger">*</span></label>
                                                    <input type="tel" class="form-control form-control-sm" id="chkEditPhone" value="<?php echo htmlspecialchars($clientProfile['phonenumber']); ?>" placeholder="+94 77 123 4567">
                                                </div>
                                                <div class="col-sm-6">
                                                    <label class="form-label fs-12 fw-bold text-slate-800 mb-1">Company / Organization</label>
                                                    <input type="text" class="form-control form-control-sm" id="chkEditCompany" value="<?php echo htmlspecialchars($clientProfile['companyname']); ?>" placeholder="Company Name (Optional)">
                                                </div>
                                                <div class="col-sm-8">
                                                    <label class="form-label fs-12 fw-bold text-slate-800 mb-1">Street Address 1 <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control form-control-sm" id="chkEditAddress1" value="<?php echo htmlspecialchars($clientProfile['address1']); ?>" placeholder="123 Main Street">
                                                </div>
                                                <div class="col-sm-4">
                                                    <label class="form-label fs-12 fw-bold text-slate-800 mb-1">Street Address 2</label>
                                                    <input type="text" class="form-control form-control-sm" id="chkEditAddress2" value="<?php echo htmlspecialchars($clientProfile['address2']); ?>" placeholder="Apt, Suite">
                                                </div>
                                                <div class="col-sm-4">
                                                    <label class="form-label fs-12 fw-bold text-slate-800 mb-1">City <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control form-control-sm" id="chkEditCity" value="<?php echo htmlspecialchars($clientProfile['city']); ?>" placeholder="Colombo">
                                                </div>
                                                <div class="col-sm-4">
                                                    <label class="form-label fs-12 fw-bold text-slate-800 mb-1">State / Region</label>
                                                    <input type="text" class="form-control form-control-sm" id="chkEditState" value="<?php echo htmlspecialchars($clientProfile['state']); ?>" placeholder="Western">
                                                </div>
                                                <div class="col-sm-4">
                                                    <label class="form-label fs-12 fw-bold text-slate-800 mb-1">Postcode / ZIP</label>
                                                    <input type="text" class="form-control form-control-sm" id="chkEditPostcode" value="<?php echo htmlspecialchars($clientProfile['postcode']); ?>" placeholder="00100">
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label fs-12 fw-bold text-slate-800 mb-1">Country <span class="text-danger">*</span></label>
                                                    <select class="form-select form-select-sm" id="chkEditCountry">
                                                        <option value="LK" <?php echo ($clientProfile['country'] === 'LK') ? 'selected' : ''; ?>>Sri Lanka</option>
                                                        <option value="US" <?php echo ($clientProfile['country'] === 'US') ? 'selected' : ''; ?>>United States</option>
                                                        <option value="GB" <?php echo ($clientProfile['country'] === 'GB') ? 'selected' : ''; ?>>United Kingdom</option>
                                                        <option value="AU" <?php echo ($clientProfile['country'] === 'AU') ? 'selected' : ''; ?>>Australia</option>
                                                        <option value="CA" <?php echo ($clientProfile['country'] === 'CA') ? 'selected' : ''; ?>>Canada</option>
                                                        <option value="IN" <?php echo ($clientProfile['country'] === 'IN') ? 'selected' : ''; ?>>India</option>
                                                        <option value="SG" <?php echo ($clientProfile['country'] === 'SG') ? 'selected' : ''; ?>>Singapore</option>
                                                        <option value="AE" <?php echo ($clientProfile['country'] === 'AE') ? 'selected' : ''; ?>>United Arab Emirates</option>
                                                        <option value="DE" <?php echo ($clientProfile['country'] === 'DE') ? 'selected' : ''; ?>>Germany</option>
                                                        <option value="FR" <?php echo ($clientProfile['country'] === 'FR') ? 'selected' : ''; ?>>France</option>
                                                        <option value="MY" <?php echo ($clientProfile['country'] === 'MY') ? 'selected' : ''; ?>>Malaysia</option>
                                                        <option value="JP" <?php echo ($clientProfile['country'] === 'JP') ? 'selected' : ''; ?>>Japan</option>
                                                    </select>
                                                </div>
                                                <div class="col-12 d-flex align-items-center justify-content-end gap-2 pt-2">
                                                    <button type="button" class="btn btn-sm btn-light border rounded-pill px-3" onclick="pageToggleCheckoutEditProfile()">Cancel</button>
                                                    <button type="button" class="btn btn-sm btn-primary rounded-pill px-4 fw-bold" id="btnSaveChkProfile" onclick="pageSaveCheckoutProfile()">
                                                        <i class="las la-check me-1"></i> Save Changes
                                                    </button>
                                                </div>
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

                                <!-- Step 3 Navigation Buttons -->
                                <div class="d-flex justify-content-between align-items-center gap-3 mt-4 pt-2 border-top flex-wrap">
                                    <button type="button" class="cf-btn-back-step" onclick="pageGoToStep(2)">
                                        <i class="las la-arrow-left me-1"></i> Back to Plan &amp; Config
                                    </button>
                                    <button type="button" class="cf-btn-next-step" id="btnStep3Submit" onclick="pageProcessAccount()">
                                        <span class="btn-text">
                                            <span id="btnStep3Text"><?php echo $isLoggedIn ? 'Continue to Payment' : 'Create Account &amp; Continue'; ?></span>
                                            <i class="las la-arrow-right ms-2"></i>
                                        </span>
                                        <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                                    </button>
                                </div>
                            </div>

                            <!-- =========================================================================
                                 STEP 4: PAYMENT METHOD (form-payment-gateway.tpl)
                                 ========================================================================= -->
                            <div class="cf-checkout-card d-none" id="stepBox4">
                                <div class="cf-card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                                    <div>
                                        <h4 class="cf-card-title"><i class="las la-credit-card text-primary me-2"></i>Step 4: Select Payment Method</h4>
                                        <p class="text-secondary fs-13 mb-0">Choose your preferred gateway to complete your deployment.</p>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="cf-active-step-chip"><span class="cf-pulse-dot"></span> Active Step 4 of 4</div>
                                        <span class="text-secondary fs-12"><i class="las la-lock text-emerald me-1"></i>256-Bit SSL</span>
                                    </div>
                                </div>

                                <!-- Payment Cards Grid (Card, PayHere, PayPal in USD) -->
                                <div class="row g-3 mb-4">
                                    <div class="col-12 col-md-4">
                                        <label class="cf-pay-option-pill active w-100" onclick="pageSelectPay('card')">
                                            <input type="radio" name="paymentmethod" value="card" checked>
                                            <i class="las la-credit-card fs-30 text-primary mb-1.5"></i>
                                            <strong class="d-block fs-14">Card Payment</strong>
                                            <small class="text-muted fs-11.5">Visa / Mastercard / Amex (USD)</small>
                                        </label>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label class="cf-pay-option-pill w-100" onclick="pageSelectPay('payhere')">
                                            <input type="radio" name="paymentmethod" value="payhere">
                                            <i class="las la-money-bill-wave fs-30 text-emerald mb-1.5"></i>
                                            <strong class="d-block fs-14">PayHere</strong>
                                            <small class="text-muted fs-11.5">Cards &amp; Online Payment (USD)</small>
                                        </label>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label class="cf-pay-option-pill w-100" onclick="pageSelectPay('paypal')">
                                            <input type="radio" name="paymentmethod" value="paypal">
                                            <i class="lab la-paypal fs-30 text-info mb-1.5"></i>
                                            <strong class="d-block fs-14">PayPal</strong>
                                            <small class="text-muted fs-11.5">Instant Global Checkout (USD)</small>
                                        </label>
                                    </div>
                                </div>

                                <!-- Final Itemized Order Review in Step 4 -->
                                <div class="p-3.5 bg-light rounded-3 border mb-4">
                                    <span class="fs-12 fw-bold text-slate-700 text-uppercase letter-spacing-1 d-block mb-2">Order Breakdown:</span>
                                    <div class="d-flex justify-content-between py-1.5 border-bottom">
                                        <span class="text-secondary fs-13" id="finalPlanItemName">Hosting Plan:</span>
                                        <strong class="text-slate-900 fs-13" id="finalPlanItemPrice">$5.99</strong>
                                    </div>
                                    <div class="d-flex justify-content-between py-1.5 border-bottom">
                                        <span class="text-secondary fs-13" id="finalDomainItemName">Domain:</span>
                                        <strong class="text-slate-900 fs-13" id="finalDomainItemPrice">$14.93</strong>
                                    </div>
                                    <div class="d-flex justify-content-between py-1.5 border-bottom">
                                        <span class="text-secondary fs-13">Server Setup &amp; DNS:</span>
                                        <span class="text-emerald fw-bold fs-13">FREE ($0.00)</span>
                                    </div>
                                    <div class="d-flex justify-content-between pt-2">
                                        <span class="fs-15 fw-bold text-slate-900">Total Due Today:</span>
                                        <span class="fs-18 fw-extrabold text-primary" id="finalTotalDue">$20.92 USD</span>
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

                                <!-- Step 4 Navigation Buttons -->
                                <div class="d-flex justify-content-between align-items-center gap-3 pt-2 border-top flex-wrap">
                                    <button type="button" class="cf-btn-back-step" onclick="pageGoToStep(3)">
                                        <i class="las la-arrow-left me-1"></i> Back to Account
                                    </button>
                                    <button type="button" class="cf-btn-submit-order" id="btnPageSubmitOrder" onclick="pageSubmitOrder()">
                                        <span class="btn-text">
                                            <i class="las la-lock me-1.5"></i> Complete Order &amp; Pay (<span id="btnPayAmount">$13.57</span>)
                                        </span>
                                        <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                                    </button>
                                </div>
                            </div>

                        </div>

                        <!-- RIGHT COLUMN: STICKY ORDER SUMMARY (Always Visible Across Steps) -->
                        <div class="col-12 col-lg-4">
                            <div class="cf-sidebar-summary-card sticky-top" style="top: 80px;">
                                <h5 class="cf-summary-title mb-3 pb-2 border-bottom">Order Summary</h5>

                                <div class="cf-summary-line" id="sideHostingRow" style="display: none;">
                                    <span class="text-secondary" id="sideHostingLabel">Hosting:</span>
                                    <strong class="text-slate-900" id="sideHostingFee">None ($0.00)</strong>
                                </div>

                                <div class="cf-summary-line" id="sideDomainRow" style="display: none;">
                                    <span class="text-secondary" id="sideDomainLabel">Domain:</span>
                                    <strong class="text-slate-900" id="sideDomainFee">$14.93</strong>
                                </div>

                                <div class="cf-summary-line">
                                    <span class="text-secondary">WHOIS Privacy:</span>
                                    <span class="text-emerald fw-bold">FREE</span>
                                </div>

                                <div class="cf-summary-line">
                                    <span class="text-secondary">Anycast DNS Setup:</span>
                                    <span class="text-emerald fw-bold">FREE</span>
                                </div>

                                <hr class="my-3">

                                <div class="d-flex justify-content-between align-items-baseline mb-1">
                                    <span class="fs-14 fw-bold text-slate-800">Total Due Today:</span>
                                    <span class="cf-side-total-due text-primary" id="sideTotalDue">$13.57</span>
                                </div>
                                <small class="text-muted fs-11 d-block mb-3">All prices in USD ($) • Cancel anytime</small>

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
                        <strong class="text-primary fs-13" id="successPlanName">Web Hosting</strong>
                    </div>
                    <div class="d-flex justify-content-between py-1.5">
                        <span class="text-secondary fs-13">Domain:</span>
                        <strong class="text-slate-900 fs-13" id="successDomainName">mybrand.com</strong>
                    </div>
                </div>

                <div class="d-flex flex-column flex-sm-row justify-content-center gap-3">
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
    <!-- =========== JS Area =========== -->

    <!-- Step-by-Step Checkout Logic Script -->
    <script>
    (function() {
        'use strict';

        // Products Catalog (Live from WHMCS)
        const PRODUCTS = <?php echo json_encode($whmcsHosting, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES); ?>;

        const TLD_PRICES = {
            <?php foreach ($whmcsDomains as $tldKey => $tldData): ?>
                '<?php echo $tldData['extension']; ?>': {
                    register: <?php echo (float)$tldData['register']; ?>,
                    transfer: <?php echo (float)$tldData['transfer']; ?>
                },
            <?php endforeach; ?>
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

        // Stepped State
        let state = {
            domain: '',
            domainType: 'register', // 'register', 'transfer', 'owndomain'
            domainPrice: 0.00,
            eppCode: '',
            pid: '201', // defaults to Business Starter
            billingcycle: 'monthly',
            accountAction: userSession.isLoggedIn ? 'loggedin' : 'register',
            paymentMethod: 'card',
            step: 1
        };

        // Initialize from URL Query Params
        function initFromUrl() {
            const params = new URLSearchParams(window.location.search);
            const domainParam     = params.get('domain') || '';
            const domainTypeParam = (params.get('domainType') || params.get('domain_type') || 'register').toLowerCase();
            const priceParam      = params.get('price') || params.get('domain_price') || '';
            const pidParam        = params.get('pid') || '';
            const cycleParam      = params.get('cycle') || params.get('billingcycle') || 'monthly';

            // 1. Hosting Plan
            if (pidParam && pidParam !== 'none' && PRODUCTS[pidParam]) {
                state.pid = pidParam;
            } else if (!domainParam && !pidParam) {
                state.pid = '201'; // Default recommended
            } else if (pidParam === 'none') {
                state.pid = 'none';
            } else {
                state.pid = '201';
            }

            // 2. Billing Cycle
            state.billingcycle = (cycleParam === 'annually' || cycleParam === 'yearly') ? 'annually' : 'monthly';

            // 3. Domain Details
            if (domainParam) {
                state.domain = domainParam.toLowerCase().trim();
                state.domainType = (domainTypeParam === 'owndomain' || domainTypeParam === 'existing') ? 'owndomain' : (domainTypeParam === 'transfer' ? 'transfer' : 'register');

                if (state.domainType === 'owndomain') {
                    state.domainPrice = 0.00;
                } else if (state.domainType === 'transfer') {
                    const lastDot = state.domain.lastIndexOf('.');
                    const ext = lastDot !== -1 ? state.domain.substring(lastDot) : '.com';
                    state.domainPrice = TLD_PRICES[ext] ? TLD_PRICES[ext].transfer : 12.73;
                } else {
                    let parsedPrice = parseFloat(priceParam.replace(/[^0-9\.]/g, ''));
                    if (!parsedPrice || isNaN(parsedPrice)) {
                        const lastDot = state.domain.lastIndexOf('.');
                        const ext = lastDot !== -1 ? state.domain.substring(lastDot) : '.com';
                        parsedPrice = TLD_PRICES[ext] ? TLD_PRICES[ext].register : 14.93;
                    }
                    state.domainPrice = parsedPrice;
                }
            }

            renderState();

            // Set initial step
            pageGoToStep(1);
        }

        // Render UI based on state
        function renderState() {
            // Update hidden inputs
            document.getElementById('pagePid').value = state.pid;
            document.getElementById('pageCycle').value = state.billingcycle;
            document.getElementById('pageDomain').value = state.domain;
            document.getElementById('pageDomainType').value = state.domainType;
            document.getElementById('pageEppCode').value = state.eppCode || '';
            document.getElementById('pageAccountAction').value = state.accountAction;

            const isYearly = state.billingcycle === 'annually';
            const hasHosting = (state.pid !== 'none' && PRODUCTS[state.pid]);
            const plan = hasHosting ? PRODUCTS[state.pid] : null;

            // 1. STEP 1: Domain Connection Rendering
            const planBanner = document.getElementById('pageStep1PlanBanner');
            const domainStrip = document.getElementById('pageConnectedDomainStrip');
            const domainOptionsWrap = document.getElementById('pageDomainOptionsWrap');
            const step1ContinueBar = document.getElementById('step1ContinueBar');

            if (hasHosting) {
                planBanner.style.display = 'flex';
                document.getElementById('step1BannerPlanName').textContent = plan.name;
                document.getElementById('step1BannerBadge').textContent = plan.category.toUpperCase();
                document.getElementById('step1BannerSpecs').textContent = plan.specs;
                document.getElementById('step1BannerPrice').textContent = isYearly ? ('$' + plan.yearly.toFixed(2)) : ('$' + plan.monthly.toFixed(2));
                document.getElementById('step1BannerCycle').textContent = isYearly ? '/year' : '/month';
            } else {
                planBanner.style.display = 'none';
            }

            // If a domain is already connected
            if (state.domain) {
                domainStrip.style.display = 'flex';
                document.getElementById('connectedDomainName').textContent = state.domain;
                
                const typeBadge = document.getElementById('connectedDomainTypeBadge');
                const noteEl = document.getElementById('connectedDomainNote');
                const feeEl = document.getElementById('connectedDomainFeeText');

                if (state.domainType === 'owndomain') {
                    typeBadge.textContent = 'Existing Domain';
                    typeBadge.className = 'badge bg-success text-white rounded-pill fs-11';
                    noteEl.textContent = 'Point DNS nameservers to ns1.cloudflox.com & ns2.cloudflox.com';
                    feeEl.textContent = 'FREE ($0.00)';
                } else if (state.domainType === 'transfer') {
                    typeBadge.textContent = 'Transfer';
                    typeBadge.className = 'badge bg-info text-dark rounded-pill fs-11';
                    noteEl.textContent = 'Transfer from external registrar • 1 Year Extension';
                    feeEl.textContent = '$' + state.domainPrice.toFixed(2);
                } else {
                    typeBadge.textContent = 'New Registration';
                    typeBadge.className = 'badge bg-primary text-white rounded-pill fs-11';
                    noteEl.textContent = '1 Year • Free Privacy Protection & Anycast DNS';
                    feeEl.textContent = '$' + state.domainPrice.toFixed(2);
                }

                if (step1ContinueBar) step1ContinueBar.style.display = 'block';
            } else {
                domainStrip.style.display = 'none';
                if (step1ContinueBar) step1ContinueBar.style.display = 'none';
            }

            // 2. STEP 2: Plan & Configuration Rendering
            document.getElementById('step2DomainText').textContent = state.domain || 'Not connected yet';
            const s2Badge = document.getElementById('step2DomainBadge');
            if (state.domainType === 'owndomain') {
                s2Badge.textContent = 'Existing Domain ($0.00)';
                s2Badge.className = 'badge bg-success text-white rounded-pill ms-1 fs-10';
            } else if (state.domainType === 'transfer') {
                s2Badge.textContent = 'Domain Transfer';
                s2Badge.className = 'badge bg-info text-dark rounded-pill ms-1 fs-10';
            } else {
                s2Badge.textContent = 'Registered Domain';
                s2Badge.className = 'badge bg-primary text-white rounded-pill ms-1 fs-10';
            }

            if (hasHosting) {
                document.getElementById('step2PlanName').textContent = plan.name;
                document.getElementById('step2PlanBadge').textContent = plan.category.toUpperCase();
                document.getElementById('step2PlanSpecs').textContent = plan.specs;

                document.getElementById('cyclePriceMonthlyText').textContent = '$' + plan.monthly.toFixed(2) + '/mo';
                document.getElementById('cyclePriceAnnuallyText').textContent = '$' + plan.yearly.toFixed(2) + '/yr';

                // Active cycle card toggle
                document.getElementById('cycleCardMonthly').classList.toggle('active', !isYearly);
                document.getElementById('cycleCardAnnually').classList.toggle('active', isYearly);

                // Highlight active plan button
                Object.keys(PRODUCTS).forEach(pKey => {
                    const btn = document.getElementById('switchPlanBtn_' + pKey);
                    if (btn) {
                        btn.classList.toggle('btn-primary', pKey === state.pid);
                        btn.classList.toggle('btn-outline-secondary', pKey !== state.pid);
                    }
                });
            }

            // 3. STEP 4 & SIDEBAR: Total Calculations
            const hostingFee = hasHosting ? (isYearly ? plan.yearly : plan.monthly) : 0.00;
            const total = state.domainPrice + hostingFee;

            // Final breakdown in Step 4
            document.getElementById('finalPlanItemName').textContent = hasHosting ? (plan.name + ' (' + (isYearly ? 'Annual' : 'Monthly') + '):') : 'Hosting:';
            document.getElementById('finalPlanItemPrice').textContent = hasHosting ? ('$' + hostingFee.toFixed(2)) : 'None ($0.00)';

            const domLabel = (state.domainType === 'owndomain') ? 'Domain (Existing DNS point):' : (state.domainType === 'transfer' ? 'Domain (Transfer):' : 'Domain (Registration):');
            document.getElementById('finalDomainItemName').textContent = state.domain ? (domLabel + ' ' + state.domain) : 'Domain:';
            document.getElementById('finalDomainItemPrice').textContent = state.domain ? (state.domainPrice > 0 ? ('$' + state.domainPrice.toFixed(2)) : 'FREE ($0.00)') : '$0.00';
            document.getElementById('finalTotalDue').textContent = '$' + total.toFixed(2) + ' USD';
            document.getElementById('btnPayAmount').textContent = '$' + total.toFixed(2) + ' USD';

            // Sidebar rows
            const sideHostingRow   = document.getElementById('sideHostingRow');
            const sideHostingLabel = document.getElementById('sideHostingLabel');
            const sideHostingFee   = document.getElementById('sideHostingFee');
            const sideDomainRow    = document.getElementById('sideDomainRow');
            const sideDomainLabel  = document.getElementById('sideDomainLabel');
            const sideDomainFee    = document.getElementById('sideDomainFee');
            const sideTotalDue     = document.getElementById('sideTotalDue');

            if (sideHostingRow) {
                sideHostingRow.style.display = hasHosting ? 'flex' : 'none';
                if (hasHosting) {
                    sideHostingLabel.textContent = 'Hosting (' + plan.name + '):';
                    sideHostingFee.textContent = '$' + hostingFee.toFixed(2) + (isYearly ? '/yr' : '/mo');
                }
            }

            if (sideDomainRow) {
                sideDomainRow.style.display = state.domain ? 'flex' : 'none';
                if (state.domain) {
                    sideDomainLabel.textContent = (state.domainType === 'owndomain') ? 'Domain (Existing):' : (state.domainType === 'transfer' ? 'Domain (Transfer):' : 'Domain:');
                    sideDomainFee.textContent = state.domainPrice > 0 ? ('$' + state.domainPrice.toFixed(2)) : 'FREE ($0.00)';
                }
            }

            if (sideTotalDue) {
                sideTotalDue.textContent = '$' + total.toFixed(2) + ' USD';
            }
        }

        // Quick TLD Shortcut Pill Selection
        window.pagePickTldPill = function(tld) {
            const selectEl = document.getElementById('pageRegisterTld');
            if (selectEl) {
                selectEl.value = tld;
            }
            pageSyncTldPill(tld);
        };

        window.pageSyncTldPill = function(tld) {
            document.querySelectorAll('.cf-quick-tld-pill').forEach(pill => {
                pill.classList.toggle('active', pill.getAttribute('data-tld') === tld);
            });
        };

        // 1-Click Copy helper
        window.pageCopyText = function(text, btn) {
            if (navigator.clipboard) {
                navigator.clipboard.writeText(text).then(() => {
                    const original = btn.innerHTML;
                    btn.innerHTML = '<i class="las la-check text-emerald"></i> Copied!';
                    setTimeout(() => { btn.innerHTML = original; }, 2000);
                });
            }
        };

        // Toggle Domain Options visibility
        window.pageToggleDomainOptions = function(show) {
            const wrap = document.getElementById('pageDomainOptionsWrap');
            if (wrap) {
                wrap.style.display = show ? 'block' : 'none';
                if (show) wrap.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        };

        // Switch Domain Option Tab (Register, Transfer, Own Domain)
        window.pageSetDomainOption = function(opt) {
            ['register', 'transfer', 'owndomain'].forEach(o => {
                const tab = document.getElementById(o === 'register' ? 'domainOptTabRegister' : (o === 'transfer' ? 'domainOptTabTransfer' : 'domainOptTabOwn'));
                const pane = document.getElementById(o === 'register' ? 'domainPaneRegister' : (o === 'transfer' ? 'domainPaneTransfer' : 'domainPaneOwn'));
                if (tab) tab.classList.toggle('active', o === opt);
                if (pane) pane.classList.toggle('d-none', o !== opt);
            });
        };

        // Check Domain Availability via check-domain.php
        window.pageCheckDomainAvailability = function() {
            hideAlert();

            const sldInput = document.getElementById('pageRegisterSld');
            const tldSelect = document.getElementById('pageRegisterTld');
            const sld = sldInput.value.trim().toLowerCase().replace(/[^a-z0-9\-]/g, '');
            const tld = tldSelect.value.trim();

            if (!sld) {
                showAlert('Please enter the domain name you wish to register.');
                sldInput.focus();
                return;
            }

            const fullDomain = sld + tld;
            const btn = document.getElementById('btnCheckDomain');
            const btnText = btn.querySelector('.btn-check-text');
            const spinner = btn.querySelector('.spinner-border');

            btn.disabled = true;
            btnText.classList.add('d-none');
            spinner.classList.remove('d-none');

            const resultBox = document.getElementById('pageDomainResultBox');
            const suggestionsWrap = document.getElementById('domainSuggestionsWrap');
            const suggestionsList = document.getElementById('domainSuggestionsList');

            resultBox.classList.add('d-none');
            suggestionsWrap.classList.add('d-none');

            fetch('check-domain.php?query=' + encodeURIComponent(fullDomain))
                .then(res => res.json())
                .then(data => {
                    btn.disabled = false;
                    btnText.classList.remove('d-none');
                    spinner.classList.add('d-none');

                    if (data.status === 'success' && data.primary) {
                        const prim = data.primary;
                        resultBox.classList.remove('d-none');

                        if (prim.is_available) {
                            resultBox.className = 'cf-domain-result-box available';
                            document.getElementById('domainResultIconWrap').style.background = '#DCFCE7';
                            document.getElementById('domainResultIconWrap').style.color = '#15803D';
                            document.getElementById('domainResultIcon').className = 'las la-check-circle';
                            document.getElementById('domainResultTitle').textContent = prim.domain + ' is available!';
                            document.getElementById('domainResultBadge').textContent = 'Available Now';
                            document.getElementById('domainResultBadge').className = 'badge bg-success text-white rounded-pill fs-11';
                            document.getElementById('domainResultSubtext').textContent = 'Includes Free WHOIS Privacy Protection & Anycast DNS';
                            document.getElementById('domainResultPrice').textContent = prim.price;
                            
                            const btnSelect = document.getElementById('btnSelectResultDomain');
                            btnSelect.style.display = 'inline-flex';
                            btnSelect.setAttribute('data-domain', prim.domain);
                            btnSelect.setAttribute('data-price', prim.price);
                        } else {
                            resultBox.className = 'cf-domain-result-box unavailable';
                            document.getElementById('domainResultIconWrap').style.background = '#FEE2E2';
                            document.getElementById('domainResultIconWrap').style.color = '#B91C1C';
                            document.getElementById('domainResultIcon').className = 'las la-times-circle';
                            document.getElementById('domainResultTitle').textContent = prim.domain + ' is already taken.';
                            document.getElementById('domainResultBadge').textContent = 'Taken';
                            document.getElementById('domainResultBadge').className = 'badge bg-danger text-white rounded-pill fs-11';
                            document.getElementById('domainResultSubtext').textContent = 'Is this your domain? You can use your existing domain in option 3 above.';
                            document.getElementById('domainResultPrice').textContent = '';
                            document.getElementById('btnSelectResultDomain').style.display = 'none';

                            // Render suggestions
                            if (data.suggestions && data.suggestions.length > 0) {
                                suggestionsWrap.classList.remove('d-none');
                                suggestionsList.innerHTML = '';
                                data.suggestions.forEach(sug => {
                                    if (sug.is_available) {
                                        const card = document.createElement('div');
                                        card.className = 'cf-suggestion-card';
                                        card.innerHTML = `
                                            <div>
                                                <strong class="text-slate-900 fs-13 d-block">${escapeHtml(sug.domain)}</strong>
                                                <span class="text-primary fw-bold fs-12">${escapeHtml(sug.price)}</span>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 fs-11 fw-bold" onclick="pageSelectAltDomain('${escapeHtml(sug.domain)}', '${escapeHtml(sug.price)}')">
                                                Select
                                            </button>
                                        `;
                                        suggestionsList.appendChild(card);
                                    }
                                });
                            }
                        }
                    } else {
                        showAlert(data.message || 'Could not verify domain availability. Please try again.');
                    }
                })
                .catch(() => {
                    btn.disabled = false;
                    btnText.classList.remove('d-none');
                    spinner.classList.add('d-none');
                    showAlert('Network error: Could not verify domain availability.');
                });
        };

        // Select registered domain from check result
        window.pageConnectRegisteredDomain = function() {
            const btn = document.getElementById('btnSelectResultDomain');
            const domain = btn.getAttribute('data-domain');
            const priceStr = btn.getAttribute('data-price') || '$14.93';
            const price = parseFloat(priceStr.replace(/[^0-9\.]/g, '')) || 14.93;

            state.domain = domain;
            state.domainType = 'register';
            state.domainPrice = price;

            renderState();

            if (window.cfToast) {
                cfToast.success('Domain <strong>' + escapeHtml(domain) + '</strong> connected!');
            }

            // Advance to Step 2
            pageGoToStep(2);
        };

        // Select alternative suggested domain
        window.pageSelectAltDomain = function(domain, priceStr) {
            const price = parseFloat(priceStr.replace(/[^0-9\.]/g, '')) || 14.93;
            state.domain = domain;
            state.domainType = 'register';
            state.domainPrice = price;

            renderState();

            if (window.cfToast) {
                cfToast.success('Domain <strong>' + escapeHtml(domain) + '</strong> connected!');
            }

            pageGoToStep(2);
        };

        // Connect domain for Transfer
        window.pageConnectTransferDomain = function() {
            hideAlert();

            const sld = document.getElementById('pageTransferSld').value.trim().toLowerCase().replace(/[^a-z0-9\-]/g, '');
            const tldSelect = document.getElementById('pageTransferTld');
            const tld = tldSelect.value.trim();
            const epp = document.getElementById('pageTransferEpp').value.trim();

            if (!sld) {
                showAlert('Please enter the domain name you wish to transfer.');
                document.getElementById('pageTransferSld').focus();
                return;
            }

            const selectedOpt = tldSelect.options[tldSelect.selectedIndex];
            const transferFee = parseFloat(selectedOpt.getAttribute('data-transfer')) || 12.73;

            state.domain = sld + tld;
            state.domainType = 'transfer';
            state.domainPrice = transferFee;
            state.eppCode = epp;

            renderState();

            if (window.cfToast) {
                cfToast.success('Transfer for <strong>' + escapeHtml(state.domain) + '</strong> configured!');
            }

            pageGoToStep(2);
        };

        // Connect Own / Existing Domain
        window.pageConnectOwnDomain = function() {
            hideAlert();

            let domain = document.getElementById('pageOwnDomainInput').value.trim().toLowerCase();
            domain = domain.replace(/^https?:\/\//i, '').replace(/^www\./i, '').replace(/\/.*$/, '');

            if (!domain || !domain.includes('.')) {
                showAlert('Please enter a valid existing domain name (e.g. yourcompany.com).');
                document.getElementById('pageOwnDomainInput').focus();
                return;
            }

            state.domain = domain;
            state.domainType = 'owndomain';
            state.domainPrice = 0.00;

            renderState();

            if (window.cfToast) {
                cfToast.success('Existing domain <strong>' + escapeHtml(domain) + '</strong> connected ($0.00)!');
            }

            pageGoToStep(2);
        };

        // Set Billing Cycle
        window.pageSetBillingCycle = function(cycle) {
            state.billingcycle = cycle;
            renderState();
        };

        // Switch Hosting Plan
        window.pageSelectHostingPlan = function(pid) {
            state.pid = pid;
            renderState();
        };

        // Step Navigation Logic (1: Domain, 2: Plan & Config, 3: Account, 4: Payment)
        window.pageGoToStep = function(targetStep) {
            hideAlert();

            if (targetStep >= 2) {
                // Validate domain connection if hosting plan is selected
                if (state.pid !== 'none' && !state.domain) {
                    showAlert('Please connect a domain name for your hosting package before proceeding.');
                    pageGoToStep(1);
                    return;
                }
            }

            if (targetStep === 4) {
                // Validate user authentication
                if (!userSession.isLoggedIn) {
                    pageProcessAccount();
                    return;
                }
            }

            state.step = targetStep;

            // Switch visibility of step boxes and update Stepper Bar indicators
            [1, 2, 3, 4].forEach(s => {
                const box = document.getElementById('stepBox' + s);
                if (box) {
                    box.classList.toggle('d-none', s !== targetStep);
                    box.classList.toggle('active-step-card', s === targetStep);
                }

                const pill = document.getElementById('pstep' + s);
                const numSpan = document.getElementById('pnum' + s);
                const conn = document.getElementById('pconn' + s);

                if (pill) {
                    pill.classList.remove('active', 'completed');
                    if (s === targetStep) {
                        pill.classList.add('active');
                        if (numSpan) {
                            numSpan.innerHTML = (s === 1) ? '<i class="las la-globe"></i>' : ((s === 2) ? '<i class="las la-sliders-h"></i>' : ((s === 3) ? '<i class="las la-user-circle"></i>' : '<i class="las la-credit-card"></i>'));
                        }
                    } else if (s < targetStep) {
                        pill.classList.add('completed');
                        if (numSpan) {
                            numSpan.innerHTML = '<i class="las la-check"></i>';
                        }
                    } else {
                        if (numSpan) {
                            numSpan.innerHTML = (s === 1) ? '<i class="las la-globe"></i>' : ((s === 2) ? '<i class="las la-sliders-h"></i>' : ((s === 3) ? '<i class="las la-user-circle"></i>' : '<i class="las la-credit-card"></i>'));
                        }
                    }
                }

                if (conn) {
                    conn.classList.remove('active', 'completed');
                    if (s < targetStep) {
                        conn.classList.add('completed');
                    } else if (s === targetStep) {
                        conn.classList.add('active');
                    }
                }
            });

            window.scrollTo({ top: 120, behavior: 'smooth' });
        };

        // Process Customer Account (Verify login credentials or create account)
        window.pageProcessAccount = function() {
            hideAlert();

            // If already authenticated, advance directly to Step 4
            if (userSession.isLoggedIn) {
                pageGoToStep(4);
                return;
            }

            const btn = document.getElementById('btnStep3Submit');
            const btnText = btn.querySelector('.btn-text');
            const spinner = btn.querySelector('.spinner-border');
            const btnLabel = document.getElementById('btnStep3Text');

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

                        document.getElementById('loggedInNameText').textContent = data.client_name;
                        document.getElementById('loggedInEmailText').textContent = data.client_email;
                        document.getElementById('paneAccountLoggedIn').classList.remove('d-none');
                        document.getElementById('paneAccountLogin').classList.add('d-none');
                        document.getElementById('paneAccountRegister').classList.add('d-none');
                        document.getElementById('step3AuthTabs').classList.add('d-none');
                        document.getElementById('step3HeaderSubtext').textContent = 'You are authenticated with your CloudFlox account.';
                        if (btnLabel) btnLabel.textContent = 'Continue to Payment';

                        if (window.cfToast) {
                            cfToast.success('Signed in as <strong>' + escapeHtml(data.client_name) + '</strong>');
                        }

                        // Populate profile summary & edit fields
                        fetch('api/checkout.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ action: 'get_profile', client_id: data.client_id })
                        })
                        .then(r => r.json())
                        .then(pData => {
                            if (pData.status === 'success' && pData.profile) {
                                const p = pData.profile;
                                if (document.getElementById('chkEditFname')) document.getElementById('chkEditFname').value = p.firstname || '';
                                if (document.getElementById('chkEditLname')) document.getElementById('chkEditLname').value = p.lastname || '';
                                if (document.getElementById('chkEditPhone')) document.getElementById('chkEditPhone').value = p.phonenumber || '';
                                if (document.getElementById('chkEditCompany')) document.getElementById('chkEditCompany').value = p.companyname || '';
                                if (document.getElementById('chkEditAddress1')) document.getElementById('chkEditAddress1').value = p.address1 || '';
                                if (document.getElementById('chkEditAddress2')) document.getElementById('chkEditAddress2').value = p.address2 || '';
                                if (document.getElementById('chkEditCity')) document.getElementById('chkEditCity').value = p.city || '';
                                if (document.getElementById('chkEditState')) document.getElementById('chkEditState').value = p.state || '';
                                if (document.getElementById('chkEditPostcode')) document.getElementById('chkEditPostcode').value = p.postcode || '';
                                if (document.getElementById('chkEditCountry')) document.getElementById('chkEditCountry').value = p.country || 'LK';

                                if (document.getElementById('summaryPhoneText')) document.getElementById('summaryPhoneText').textContent = p.phonenumber || 'Not specified';
                                if (document.getElementById('summaryCompanyText')) document.getElementById('summaryCompanyText').textContent = p.companyname || 'Individual Account';
                                const addrArr = [p.address1, p.city, p.country].filter(Boolean);
                                if (document.getElementById('summaryAddressText')) document.getElementById('summaryAddressText').textContent = addrArr.join(', ') || 'Not specified';
                            }
                        })
                        .catch(() => {});

                        // Advance to Payment (Step 4)
                        pageGoToStep(4);
                    } else {
                        showAlert(data.message || 'Invalid email address or password. Please try again.');
                    }
                })
                .catch(() => {
                    setLoading(false);
                    showAlert('Network error: Could not verify login credentials.');
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

                        document.getElementById('loggedInNameText').textContent = data.client_name;
                        document.getElementById('loggedInEmailText').textContent = data.client_email;
                        document.getElementById('paneAccountLoggedIn').classList.remove('d-none');
                        document.getElementById('paneAccountLogin').classList.add('d-none');
                        document.getElementById('paneAccountRegister').classList.add('d-none');
                        document.getElementById('step3AuthTabs').classList.add('d-none');
                        document.getElementById('step3HeaderSubtext').textContent = 'You are authenticated with your CloudFlox account.';
                        if (btnLabel) btnLabel.textContent = 'Continue to Payment';

                        if (window.cfToast) {
                            cfToast.success('Account created! Welcome, <strong>' + escapeHtml(data.client_name) + '</strong>');
                        }

                        // Advance to Payment (Step 4)
                        pageGoToStep(4);
                    } else {
                        showAlert(data.message || 'Could not create account. Please check the details and try again.');
                    }
                })
                .catch(() => {
                    setLoading(false);
                    showAlert('Network error: Could not submit account registration.');
                });
            }
        };

        // Logout
        window.pageLogout = function() {
            fetch('api/checkout.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'logout' })
            }).then(() => {
                userSession.isLoggedIn = false;
                userSession.clientId = 0;
                userSession.clientName = '';
                userSession.clientEmail = '';
                state.accountAction = 'register';

                document.getElementById('paneAccountLoggedIn').classList.add('d-none');
                document.getElementById('step3AuthTabs').classList.remove('d-none');
                document.getElementById('step3HeaderSubtext').textContent = 'Create your client portal account or sign in to an existing profile.';
                pageSetAccountMode('register');
            });
        };

        // Toggle Edit Personal & Billing Info on Checkout Step 3
        window.pageToggleCheckoutEditProfile = function() {
            const editBox = document.getElementById('checkoutProfileEditBox');
            const summaryBox = document.getElementById('checkoutProfileSummary');
            if (!editBox) return;
            const isHidden = editBox.classList.contains('d-none');
            if (isHidden) {
                editBox.classList.remove('d-none');
                if (summaryBox) summaryBox.classList.add('d-none');
            } else {
                editBox.classList.add('d-none');
                if (summaryBox) summaryBox.classList.remove('d-none');
            }
        };

        // Save Updated Profile Details from Step 3
        window.pageSaveCheckoutProfile = function() {
            const fname   = document.getElementById('chkEditFname').value.trim();
            const lname   = document.getElementById('chkEditLname').value.trim();
            const phone   = document.getElementById('chkEditPhone').value.trim();
            const company = document.getElementById('chkEditCompany').value.trim();
            const addr1   = document.getElementById('chkEditAddress1').value.trim();
            const addr2   = document.getElementById('chkEditAddress2').value.trim();
            const city    = document.getElementById('chkEditCity').value.trim();
            const stateVal = document.getElementById('chkEditState').value.trim();
            const postcode = document.getElementById('chkEditPostcode').value.trim();
            const country  = document.getElementById('chkEditCountry').value;

            const alertEl = document.getElementById('chkEditAlert');
            function showEditAlert(msg, type) {
                if (!alertEl) return;
                alertEl.className = 'alert alert-' + (type || 'danger') + ' py-2 px-3 fs-12 rounded-2 mb-3';
                alertEl.innerHTML = msg;
                alertEl.classList.remove('d-none');
            }
            if (alertEl) alertEl.classList.add('d-none');

            if (!fname || !lname) {
                showEditAlert('Please provide both your First Name and Last Name.');
                return;
            }
            if (!phone) {
                showEditAlert('Please enter your contact phone number.');
                return;
            }
            if (!addr1) {
                showEditAlert('Please enter your street address.');
                return;
            }
            if (!city) {
                showEditAlert('Please enter your city.');
                return;
            }

            const btn = document.getElementById('btnSaveChkProfile');
            const origHtml = btn ? btn.innerHTML : '';
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Saving...';
            }

            fetch('api/checkout.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'update_profile',
                    client_id: userSession.clientId,
                    firstname: fname,
                    lastname: lname,
                    companyname: company,
                    phonenumber: phone,
                    address1: addr1,
                    address2: addr2,
                    city: city,
                    state: stateVal,
                    postcode: postcode,
                    country: country
                })
            })
            .then(res => res.json())
            .then(data => {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = origHtml;
                }
                if (data.status === 'success') {
                    userSession.clientName = data.client_name;
                    document.getElementById('loggedInNameText').textContent = data.client_name;
                    document.getElementById('summaryPhoneText').textContent = phone;
                    document.getElementById('summaryCompanyText').textContent = company || 'Individual Account';
                    const parts = [addr1, city, country].filter(Boolean);
                    document.getElementById('summaryAddressText').textContent = parts.join(', ');

                    if (window.cfToast) {
                        cfToast.success('Personal & Billing details updated successfully!');
                    }
                    pageToggleCheckoutEditProfile();
                } else {
                    showEditAlert(data.message || 'Could not update profile. Please try again.');
                }
            })
            .catch(() => {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = origHtml;
                }
                showEditAlert('Network error: Could not update profile.');
            });
        };

        // Switch Account Mode
        window.pageSetAccountMode = function(mode) {
            state.accountAction = mode;
            document.getElementById('pageAccountAction').value = mode;

            const btnReg = document.getElementById('btnTabRegister');
            const btnLog = document.getElementById('btnTabLogin');
            const paneReg = document.getElementById('paneAccountRegister');
            const paneLog = document.getElementById('paneAccountLogin');
            const btnLabel = document.getElementById('btnStep3Text');

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

        // Toggle Password Show/Hide
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

        // Select Payment Gateway (Card, PayHere, PayPal)
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
            payload.domain_type = state.domainType;
            payload.epp_code = state.eppCode;
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
                    // Direct redirect to the invoice / payment page
                    if (data.invoice_url) {
                        btn.querySelector('.btn-text').innerHTML = '<i class="las la-check-circle me-1.5"></i> Order Placed! Opening Payment Page...';
                        btn.querySelector('.btn-text').classList.remove('d-none');
                        btn.querySelector('.spinner-border').classList.add('d-none');
                        window.location.href = data.invoice_url;
                        return;
                    }
                } else {
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
