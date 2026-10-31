<?php 
if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    @session_start();
}
include 'include/url.inc.php';
require_once __DIR__ . '/api/config.php';
require_once __DIR__ . '/api/whmcs-pricing.php';

$isLoggedIn = !empty($_SESSION['cf_client_logged_in']);
$clientId   = (int)($_SESSION['cf_client_id'] ?? 0);
$clientName = $_SESSION['cf_client_name'] ?? '';
$clientEmail= $_SESSION['cf_client_email'] ?? '';

// Pre-fill query parameters
$requestedPid  = trim($_GET['pid'] ?? '');
$requestedType = strtolower(trim($_GET['type'] ?? ''));

$prefillPlanName = 'Custom Server Solution';
$prefillType     = 'vps';
$prefillCpu      = '2 vCPUs';
$prefillRam      = '4 GB RAM';
$prefillStorage  = '80 GB Pure NVMe';
$prefillPrice    = 0.00;

if (!empty($requestedPid) && isset($CF_PRODUCTS[$requestedPid])) {
    $p = $CF_PRODUCTS[$requestedPid];
    $prefillPlanName = $p['name'];
    $prefillPrice    = $p['monthly'] ?? 0.00;
    if (stripos($p['category'], 'Dedicated') !== false) {
        $prefillType = 'dedicated';
    } else {
        $prefillType = 'vps';
    }
} elseif ($requestedType === 'dedicated' || $requestedType === 'server') {
    $prefillType = 'dedicated';
    $prefillPlanName = 'Dedicated Bare Metal Server';
}

$clientProfile = [
    'firstname'   => '',
    'lastname'    => '',
    'companyname' => '',
    'email'       => $clientEmail,
    'phonenumber' => '',
    'country'     => 'LK'
];

if ($isLoggedIn && $clientId > 0) {
    try {
        $mysqli = @new mysqli('127.0.0.1', 'cfwhmcs_whmcs', '+V)Rb%K3wFiK', 'cfwhmcs_whmcs3');
        if (!$mysqli->connect_error) {
            $stmt = $mysqli->prepare("SELECT firstname, lastname, companyname, email, phonenumber, country FROM tblclients WHERE id = ? LIMIT 1");
            if ($stmt) {
                $stmt->bind_param('i', $clientId);
                $stmt->execute();
                $res = $stmt->get_result();
                if ($res && $row = $res->fetch_assoc()) {
                    $clientProfile = array_merge($clientProfile, $row);
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
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Request Custom Server Quotation (RFQ) — CloudFlox Cloud Infrastructure</title>
    <meta name="description" content="Submit your custom hardware and networking requirements for Cloud VPS and Dedicated Bare Metal servers. Receive an official tailored quotation within 1-2 hours.">
    <link rel="canonical" href="https://cloudflox.com/request-quote">

    <!-- CSS Area -->
    <?php include 'include/css.inc.php'; ?>

    <style>
        /* 1. Request Form Header Section */
        .cf-rfq-form-header {
            background: linear-gradient(180deg, #F8FAFC 0%, #FFFFFF 100%);
            padding-top: 36px;
            padding-bottom: 34px;
            border-bottom: 1.5px solid #E2E8F0;
            position: relative;
            overflow: hidden;
        }
        .cf-rfq-form-header .hero-ambient-glow {
            position: absolute;
            top: -20%;
            left: 50%;
            transform: translateX(-50%);
            width: 100%;
            max-width: 900px;
            height: 380px;
            background: radial-gradient(ellipse 65% 55% at 50% 0%, rgba(6, 182, 212, 0.12) 0%, rgba(37, 99, 235, 0.04) 50%, rgba(255, 255, 255, 0) 80%);
            pointer-events: none;
            z-index: 1;
        }

        /* 4-Step RFQ Progress Stepper */
        .cf-rfq-steps-tracker {
            background: #FFFFFF;
            border: 1.5px solid #E2E8F0;
            border-radius: 16px;
            padding: 14px 18px;
            box-shadow: 0 4px 16px -4px rgba(15, 23, 42, 0.04);
            margin-top: 24px;
        }
        .cf-rfq-step-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 10px;
            border-radius: 11px;
            transition: all 0.2s ease;
        }
        .cf-rfq-step-badge {
            width: 32px;
            height: 32px;
            border-radius: 9px;
            background: #F1F5F9;
            color: #64748B;
            font-weight: 800;
            font-size: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            border: 1px solid #CBD5E1;
            transition: all 0.2s ease;
        }
        .cf-rfq-step-item.active .cf-rfq-step-badge {
            background: #2563EB;
            color: #FFFFFF;
            border-color: #2563EB;
            box-shadow: 0 3px 10px rgba(37, 99, 235, 0.3);
        }
        .cf-rfq-step-item.active {
            background: #EFF6FF;
        }
        .cf-rfq-step-title {
            font-size: 12.5px;
            font-weight: 700;
            color: #0F172A;
            line-height: 1.25;
            display: block;
        }
        .cf-rfq-step-item.active .cf-rfq-step-title {
            color: #1D4ED8;
        }
        .cf-rfq-step-desc {
            font-size: 11px;
            color: #64748B;
            line-height: 1.2;
            display: block;
            margin-top: 2px;
        }

        /* Trust & Assurance Chips */
        .cf-rfq-chip-tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: 999px;
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            font-size: 11.5px;
            font-weight: 600;
            color: #334155;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
            white-space: nowrap;
        }

        /* Main Form Section & Spacing */
        .cf-rfq-main-section {
            background: #F8FAFC;
            padding: 44px 0 80px !important;
        }
        .cf-rfq-card {
            background: #FFFFFF;
            border: 1.5px solid #E2E8F0;
            border-radius: 20px !important;
            padding: 34px 36px !important;
            margin-bottom: 28px !important;
            box-shadow: 0 4px 20px -6px rgba(15, 23, 42, 0.04);
            transition: all 0.2s ease;
        }
        .cf-rfq-card-header {
            display: flex;
            align-items: center;
            gap: 16px !important;
            margin-bottom: 26px !important;
            padding-bottom: 18px !important;
            border-bottom: 1.5px solid #F1F5F9;
        }
        .cf-rfq-card-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: #EFF6FF;
            color: #2563EB;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            flex-shrink: 0;
        }
        .cf-rfq-card-title {
            font-size: 17px;
            font-weight: 800;
            color: #0F172A;
            margin: 0;
            letter-spacing: -0.2px;
        }
        .cf-rfq-card-subtitle {
            font-size: 12.5px;
            color: #64748B;
            margin: 4px 0 0;
        }
        .cf-rfq-pill-card {
            border: 1.5px solid #E2E8F0;
            border-radius: 13px;
            padding: 14px 12px !important;
            cursor: pointer;
            text-align: center;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            background: #FFFFFF;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            user-select: none;
        }
        .cf-rfq-pill-card:hover {
            border-color: #93C5FD;
            background: #F8FAFC;
            transform: translateY(-2px);
        }
        .cf-rfq-pill-card.active {
            border-color: #2563EB !important;
            background: #EFF6FF !important;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15) !important;
        }
        .cf-rfq-pill-title {
            font-weight: 700;
            font-size: 13px;
            color: #0F172A;
        }
        .cf-rfq-pill-desc {
            font-size: 11px;
            color: #64748B;
            margin-top: 3px;
        }

        /* Form Inputs & Controls */
        .cf-rfq-card .form-label {
            font-size: 13px !important;
            font-weight: 700 !important;
            color: #1E293B !important;
            margin-bottom: 7px !important;
        }
        .cf-rfq-card .form-control,
        .cf-rfq-card .form-select {
            padding: 11px 16px !important;
            font-size: 13.5px !important;
            border: 1.5px solid #E2E8F0 !important;
            border-radius: 11px !important;
            transition: all 0.2s ease !important;
        }
        .cf-rfq-card .form-control:focus,
        .cf-rfq-card .form-select:focus {
            border-color: #2563EB !important;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12) !important;
        }

        /* Bottom Summary Card */
        .cf-rfq-bottom-summary {
            background: #FFFFFF;
            border: 2px solid #2563EB;
            border-radius: 22px !important;
            padding: 38px 40px !important;
            margin-top: 16px !important;
            margin-bottom: 24px !important;
            box-shadow: 0 16px 40px -10px rgba(37, 99, 235, 0.15), 0 4px 18px -4px rgba(15, 23, 42, 0.05);
            position: relative;
        }
        .cf-rfq-base-banner {
            background: linear-gradient(135deg, #F8FAFC 0%, #EFF6FF 100%);
            border: 1.5px solid #DBEAFE;
            border-radius: 14px;
            padding: 18px 24px !important;
            margin-bottom: 24px !important;
        }
        .cf-rfq-tier-pill {
            background: #2563EB !important;
            color: #FFFFFF !important;
            font-weight: 700;
            padding: 7px 18px !important;
            border-radius: 999px;
            font-size: 12px;
            display: inline-flex;
            align-items: center;
            box-shadow: 0 2px 8px rgba(37, 99, 235, 0.25);
            letter-spacing: 0.3px;
        }
        .cf-rfq-spec-pill {
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 12px;
            padding: 14px 16px !important;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            transition: all 0.2s ease;
        }
        .cf-rfq-spec-pill:hover {
            background: #FFFFFF;
            border-color: #CBD5E1;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
        }
        .cf-rfq-spec-label {
            font-size: 11px;
            font-weight: 700;
            color: #64748B;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
        }
        .cf-rfq-spec-val {
            font-size: 13.5px;
            font-weight: 700;
            color: #0F172A;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .cf-rfq-reassurance-box {
            background: #F0FDF4;
            border: 1.5px solid #BBF7D0;
            border-radius: 14px;
            padding: 18px 22px !important;
        }
        .cf-rfq-btn-submit {
            background: linear-gradient(135deg, #1D4ED8 0%, #2563EB 100%);
            color: #FFFFFF;
            border: none;
            border-radius: 14px;
            padding: 18px 32px !important;
            font-size: 16.5px !important;
            font-weight: 800;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.2s ease;
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.3);
            cursor: pointer;
        }
        .cf-rfq-btn-submit:hover {
            background: linear-gradient(135deg, #1E40AF 0%, #1D4ED8 100%);
            transform: translateY(-1px);
            box-shadow: 0 8px 25px rgba(37, 99, 235, 0.4);
            color: #FFFFFF;
        }
        .cf-rfq-btn-submit:active {
            transform: translateY(0);
        }

        /* Responsive Breakpoints */
        @media (max-width: 991.98px) {
            .cf-rfq-form-header {
                padding-top: 28px;
                padding-bottom: 26px;
            }
            .cf-rfq-main-section {
                padding: 32px 0 60px !important;
            }
            .cf-rfq-card {
                padding: 26px 24px !important;
                margin-bottom: 22px !important;
                border-radius: 16px !important;
            }
            .cf-rfq-bottom-summary {
                padding: 28px 26px !important;
                border-radius: 18px !important;
            }
        }
        @media (max-width: 575.98px) {
            .cf-rfq-form-header {
                padding-top: 20px;
                padding-bottom: 22px;
            }
            .cf-rfq-steps-tracker {
                padding: 12px;
            }
            .cf-rfq-step-item {
                padding: 6px;
                gap: 8px;
            }
            .cf-rfq-step-badge {
                width: 28px;
                height: 28px;
                font-size: 12px;
            }
            .cf-rfq-main-section {
                padding: 24px 0 45px !important;
            }
            .cf-rfq-card {
                padding: 20px 16px !important;
                margin-bottom: 18px !important;
                border-radius: 14px !important;
            }
            .cf-rfq-card-header {
                gap: 12px !important;
                margin-bottom: 18px !important;
                padding-bottom: 14px !important;
            }
            .cf-rfq-card-title {
                font-size: 15.5px !important;
            }
            .cf-rfq-bottom-summary {
                padding: 22px 16px !important;
                border-radius: 16px !important;
            }
            .cf-rfq-btn-submit {
                padding: 15px 20px !important;
                font-size: 15px !important;
            }
            .cf-rfq-spec-val {
                font-size: 12.5px !important;
            }
        }
    </style>
</head>

<body class="pure-white-theme">

    <!-- Header Area -->
    <?php include 'include/header.inc.php'; ?>

    <!-- =========================================================================
         1. REQUEST FORM HEADER SECTION (Formal Enterprise RFQ Flow)
         ========================================================================= -->
    <section class="cf-rfq-form-header position-relative">
        <div class="hero-ambient-glow" aria-hidden="true"></div>

        <div class="container position-relative z-2">
            <div class="row justify-content-center">
                <div class="col-12 col-lg-10 col-xl-9">
                    <!-- Breadcrumbs -->
                    <nav aria-label="breadcrumb" class="mb-3">
                        <ol class="breadcrumb custom-breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="<?php echo $url; ?>" class="text-decoration-none text-muted">
                                    <i class="las la-home me-1"></i>Home
                                </a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="<?php echo $url; ?>vps-hosting" class="text-decoration-none text-muted">Servers</a>
                            </li>
                            <li class="breadcrumb-item active text-cyan fw-semibold" aria-current="page">Custom Quote Request</li>
                        </ol>
                    </nav>

                    <!-- Form Header Top Bar: Badge & Active Quotes Link -->
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-2.5">
                        <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill bg-cyan-subtle text-cyan fw-bold fs-12 border border-cyan-subtle">
                            <span class="cf-pulse-dot-cyan"></span>
                            <span>OFFICIAL ENTERPRISE SERVER RFQ</span>
                        </div>

                        <a href="<?php echo $url; ?>clientarea?tab=quotes" class="text-decoration-none fs-12 fw-semibold text-secondary d-inline-flex align-items-center gap-1.5 px-3 py-1.5 rounded-pill bg-white border shadow-xs hover-primary">
                            <i class="las la-file-invoice text-primary fs-14"></i>
                            <span>View My RFQ Quotes</span>
                            <i class="las la-angle-right fs-11 text-muted"></i>
                        </a>
                    </div>

                    <!-- Primary Request Form Title & Explanatory Subtitle -->
                    <h1 class="fw-extrabold text-slate-900 tracking-tight mb-2" style="font-size: clamp(24px, 3.2vw, 34px); line-height: 1.25;">
                        Request a Custom <span class="text-gradient-vps">Server Quotation</span>
                    </h1>

                    <p class="text-slate-600 mb-0 fs-14" style="line-height: 1.65; max-width: 840px;">
                        Configure your target compute, memory, storage, and networking architecture below. Our Senior Cloud Infrastructure Architects will evaluate availability, apply eligible enterprise discounts, and dispatch an official quotation within <strong>1-2 hours</strong>.
                    </p>

                    <!-- 4-Step RFQ Pipeline Stepper (Visual Request Process) -->
                    <div class="cf-rfq-steps-tracker">
                        <div class="row g-2 g-md-3">
                            <div class="col-6 col-md-3">
                                <div class="cf-rfq-step-item active">
                                    <div class="cf-rfq-step-badge">1</div>
                                    <div>
                                        <span class="cf-rfq-step-title">1. Configure Specs</span>
                                        <span class="cf-rfq-step-desc">vCPU, RAM, NVMe &amp; OS</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="cf-rfq-step-item">
                                    <div class="cf-rfq-step-badge">2</div>
                                    <div>
                                        <span class="cf-rfq-step-title">2. Submit RFQ</span>
                                        <span class="cf-rfq-step-desc">Zero upfront payment</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="cf-rfq-step-item">
                                    <div class="cf-rfq-step-badge">3</div>
                                    <div>
                                        <span class="cf-rfq-step-title">3. NOC Review</span>
                                        <span class="cf-rfq-step-desc">1-2 hour SLA turnaround</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="cf-rfq-step-item">
                                    <div class="cf-rfq-step-badge">4</div>
                                    <div>
                                        <span class="cf-rfq-step-title">4. Formal Quote</span>
                                        <span class="cf-rfq-step-desc">WHMCS Portal &amp; PDF</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Assurance Trust Badges -->
                    <div class="d-flex flex-wrap gap-2 mt-3 pt-1">
                        <span class="cf-rfq-chip-tag"><i class="las la-shield-alt text-emerald"></i> Zero Upfront Payment</span>
                        <span class="cf-rfq-chip-tag"><i class="las la-microchip text-primary"></i> 100% Dedicated Compute</span>
                        <span class="cf-rfq-chip-tag"><i class="las la-clock text-cyan"></i> 1-2 Hour Response SLA</span>
                        <span class="cf-rfq-chip-tag"><i class="las la-globe text-purple"></i> 5 Global Datacenters</span>
                        <span class="cf-rfq-chip-tag"><i class="las la-file-invoice text-indigo"></i> Formal Binding PDF</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 2. MAIN RFQ BUILDER FORM SECTION -->
    <section class="cf-rfq-main-section">
        <div class="container position-relative">
            <form id="cfRfqForm" onsubmit="submitServerQuoteRequest(event)" novalidate>
                <div class="row justify-content-center">
                    <div class="col-12 col-lg-10 col-xl-9">

                        <!-- SECTION 1: SERVER TIER & BASE MODEL -->
                        <div class="cf-rfq-card">
                            <div class="cf-rfq-card-header">
                                <div class="cf-rfq-card-icon"><i class="las la-server"></i></div>
                                <div>
                                    <h3 class="cf-rfq-card-title">1. Server Category &amp; Base Architecture</h3>
                                    <p class="cf-rfq-card-subtitle">Choose the core deployment type for your infrastructure.</p>
                                </div>
                            </div>

                            <div class="row g-2.5 mb-4" id="serverTierGrid">
                                <div class="col-6 col-md-4">
                                    <div class="cf-rfq-pill-card <?php echo $prefillType === 'vps' ? 'active' : ''; ?>" data-val="KVM Cloud VPS" onclick="selectRfqTier('KVM Cloud VPS', this)">
                                        <i class="las la-terminal fs-24 text-primary mb-1"></i>
                                        <span class="cf-rfq-pill-title">KVM Cloud VPS</span>
                                        <span class="cf-rfq-pill-desc">Virtual Private Server</span>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="cf-rfq-pill-card <?php echo $prefillType === 'dedicated' ? 'active' : ''; ?>" data-val="Dedicated Bare Metal Server" onclick="selectRfqTier('Dedicated Bare Metal Server', this)">
                                        <i class="las la-microchip fs-24 text-purple mb-1"></i>
                                        <span class="cf-rfq-pill-title">Dedicated Server</span>
                                        <span class="cf-rfq-pill-desc">100% Bare Metal Compute</span>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="cf-rfq-pill-card" data-val="Custom Enterprise Cluster" onclick="selectRfqTier('Custom Enterprise Cluster', this)">
                                        <i class="las la-network-wired fs-24 text-emerald mb-1"></i>
                                        <span class="cf-rfq-pill-title">Custom Cluster</span>
                                        <span class="cf-rfq-pill-desc">Multi-Node / High Availability</span>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold text-slate-800 fs-13 mb-1">Base Plan Reference</label>
                                    <input type="text" id="rfqBasePlan" name="base_plan" class="form-control fs-13" value="<?php echo htmlspecialchars($prefillPlanName); ?>" oninput="updateSummary()">
                                    <small class="text-muted fs-11 mt-1 d-block">e.g. Launch VPS, Scale VPS, Intel Xeon E5, AMD Ryzen 5800X, or Custom</small>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold text-slate-800 fs-13 mb-1">Requested Server Hostname (FQDN)</label>
                                    <input type="text" id="rfqHostname" name="hostname" class="form-control fs-13 font-monospace" placeholder="e.g. srv1.yourcompany.com" oninput="updateSummary()">
                                    <small class="text-muted fs-11 mt-1 d-block">Fully Qualified Domain Name for network PTR &amp; identity.</small>
                                </div>
                            </div>
                        </div>

                        <!-- SECTION 2: HARDWARE COMPUTE & STORAGE -->
                        <div class="cf-rfq-card">
                            <div class="cf-rfq-card-header">
                                <div class="cf-rfq-card-icon"><i class="las la-microchip"></i></div>
                                <div>
                                    <h3 class="cf-rfq-card-title">2. Compute, Memory &amp; Storage Sizing</h3>
                                    <p class="cf-rfq-card-subtitle">Select your desired CPU core count, dedicated RAM, and storage array.</p>
                                </div>
                            </div>

                            <!-- CPU Cores -->
                            <div class="mb-3.5">
                                <label class="form-label fw-bold text-slate-800 fs-13 mb-1.5">Processor / CPU Cores</label>
                                <div class="row g-2" id="rfqCpuGrid">
                                    <div class="col-4 col-md-2">
                                        <div class="cf-rfq-pill-card" data-val="1 vCPU" onclick="selectRfqCpu('1 vCPU', this)"><span class="cf-rfq-pill-title">1 Core</span></div>
                                    </div>
                                    <div class="col-4 col-md-2">
                                        <div class="cf-rfq-pill-card active" data-val="2 vCPUs" onclick="selectRfqCpu('2 vCPUs', this)"><span class="cf-rfq-pill-title">2 Cores</span></div>
                                    </div>
                                    <div class="col-4 col-md-2">
                                        <div class="cf-rfq-pill-card" data-val="4 vCPUs" onclick="selectRfqCpu('4 vCPUs', this)"><span class="cf-rfq-pill-title">4 Cores</span></div>
                                    </div>
                                    <div class="col-4 col-md-2">
                                        <div class="cf-rfq-pill-card" data-val="8 vCPUs" onclick="selectRfqCpu('8 vCPUs', this)"><span class="cf-rfq-pill-title">8 Cores</span></div>
                                    </div>
                                    <div class="col-4 col-md-2">
                                        <div class="cf-rfq-pill-card" data-val="16 Cores" onclick="selectRfqCpu('16 Cores', this)"><span class="cf-rfq-pill-title">16 Cores</span></div>
                                    </div>
                                    <div class="col-4 col-md-2">
                                        <div class="cf-rfq-pill-card" data-val="32+ Cores" onclick="selectRfqCpu('32+ Cores', this)"><span class="cf-rfq-pill-title">32+ Cores</span></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Dedicated Memory -->
                            <div class="mb-3.5">
                                <label class="form-label fw-bold text-slate-800 fs-13 mb-1.5">Dedicated RAM</label>
                                <div class="row g-2" id="rfqRamGrid">
                                    <div class="col-4 col-md-2">
                                        <div class="cf-rfq-pill-card" data-val="2 GB RAM" onclick="selectRfqRam('2 GB RAM', this)"><span class="cf-rfq-pill-title">2 GB</span></div>
                                    </div>
                                    <div class="col-4 col-md-2">
                                        <div class="cf-rfq-pill-card active" data-val="4 GB RAM" onclick="selectRfqRam('4 GB RAM', this)"><span class="cf-rfq-pill-title">4 GB</span></div>
                                    </div>
                                    <div class="col-4 col-md-2">
                                        <div class="cf-rfq-pill-card" data-val="8 GB RAM" onclick="selectRfqRam('8 GB RAM', this)"><span class="cf-rfq-pill-title">8 GB</span></div>
                                    </div>
                                    <div class="col-4 col-md-2">
                                        <div class="cf-rfq-pill-card" data-val="16 GB RAM" onclick="selectRfqRam('16 GB RAM', this)"><span class="cf-rfq-pill-title">16 GB</span></div>
                                    </div>
                                    <div class="col-4 col-md-2">
                                        <div class="cf-rfq-pill-card" data-val="32 GB RAM" onclick="selectRfqRam('32 GB RAM', this)"><span class="cf-rfq-pill-title">32 GB</span></div>
                                    </div>
                                    <div class="col-4 col-md-2">
                                        <div class="cf-rfq-pill-card" data-val="64+ GB RAM" onclick="selectRfqRam('64+ GB RAM', this)"><span class="cf-rfq-pill-title">64+ GB</span></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Storage & RAID Array -->
                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold text-slate-800 fs-13 mb-1">Storage Medium &amp; Size</label>
                                    <select class="form-select fs-13" id="rfqStorage" onchange="updateSummary()">
                                        <option value="40 GB Gen4 NVMe">40 GB Pure Gen4 NVMe</option>
                                        <option value="80 GB Gen4 NVMe" selected>80 GB Pure Gen4 NVMe</option>
                                        <option value="160 GB Gen4 NVMe">160 GB Pure Gen4 NVMe</option>
                                        <option value="320 GB Gen4 NVMe">320 GB Pure Gen4 NVMe</option>
                                        <option value="2x 1TB NVMe Enterprise">2x 1TB NVMe Enterprise (Bare Metal)</option>
                                        <option value="2x 2TB NVMe Enterprise">2x 2TB NVMe Enterprise (Bare Metal)</option>
                                        <option value="4x 3.84TB NVMe Datacenter">4x 3.84TB NVMe Datacenter Tier</option>
                                        <option value="Custom Storage Configuration">Custom Storage Specification</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold text-slate-800 fs-13 mb-1">RAID Redundancy</label>
                                    <select class="form-select fs-13" id="rfqRaid" onchange="updateSummary()">
                                        <option value="Standard Cloud RAID">Standard Host Node Cloud Redundancy</option>
                                        <option value="Hardware RAID-1 (Mirroring)" selected>Hardware RAID-1 (Mirroring - High Safety)</option>
                                        <option value="Hardware RAID-10 (Speed &amp; Safety)">Hardware RAID-10 (High IOPS + Maximum Safety)</option>
                                        <option value="No RAID / Individual Disks">No RAID (Raw Separate Disks)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- SECTION 3: ENVIRONMENT & NETWORK -->
                        <div class="cf-rfq-card">
                            <div class="cf-rfq-card-header">
                                <div class="cf-rfq-card-icon"><i class="las la-globe"></i></div>
                                <div>
                                    <h3 class="cf-rfq-card-title">3. Operating System, Control Panel &amp; Datacenter</h3>
                                    <p class="cf-rfq-card-subtitle">Choose your software ecosystem and deployment location.</p>
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold text-slate-800 fs-13 mb-1">Operating System</label>
                                    <select class="form-select fs-13" id="rfqOs" onchange="updateSummary()">
                                        <option value="Ubuntu 24.04 LTS" selected>Ubuntu 24.04 LTS (64-bit)</option>
                                        <option value="Ubuntu 22.04 LTS">Ubuntu 22.04 LTS (64-bit)</option>
                                        <option value="Debian 12 Bookworm">Debian 12 Bookworm</option>
                                        <option value="AlmaLinux 9 (cPanel Ready)">AlmaLinux 9 (cPanel / Enterprise Ready)</option>
                                        <option value="Rocky Linux 9">Rocky Linux 9</option>
                                        <option value="Windows Server 2022 Standard">Windows Server 2022 Standard</option>
                                        <option value="Windows Server 2019 Standard">Windows Server 2019 Standard</option>
                                        <option value="Custom ISO / Bring Your Own OS">Custom ISO / Custom Image</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold text-slate-800 fs-13 mb-1">Control Panel Stack</label>
                                    <select class="form-select fs-13" id="rfqPanel" onchange="updateSummary()">
                                        <option value="No Control Panel (Pure CLI / Root SSH)" selected>No Control Panel (Pure Root SSH)</option>
                                        <option value="cPanel &amp; WHM (Premier)">cPanel &amp; WHM</option>
                                        <option value="DirectAdmin">DirectAdmin</option>
                                        <option value="Plesk Web Pro">Plesk</option>
                                        <option value="CyberPanel (OpenLiteSpeed)">CyberPanel (OpenLiteSpeed)</option>
                                        <option value="Docker &amp; Portainer Pre-installed">Docker &amp; Portainer Ready</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold text-slate-800 fs-13 mb-1">Target Datacenter Region</label>
                                    <select class="form-select fs-13" id="rfqDatacenter" onchange="updateSummary()">
                                        <option value="Frankfurt, Germany (EU Central)" selected>🇩🇪 Frankfurt, Germany (EU Central)</option>
                                        <option value="New York, USA (US East)">🇺🇸 New York, USA (US East Coast)</option>
                                        <option value="Singapore, APAC">🇸🇬 Singapore, APAC Backbone</option>
                                        <option value="Helsinki, Finland (Nordic Tier IV)">🇫🇮 Helsinki, Finland (Nordic Tier IV)</option>
                                        <option value="London, United Kingdom">🇬🇧 London, United Kingdom</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold text-slate-800 fs-13 mb-1">Dedicated IPv4 Addresses</label>
                                    <select class="form-select fs-13" id="rfqIps" onchange="updateSummary()">
                                        <option value="1 Dedicated IPv4" selected>1 Dedicated IPv4 (Included)</option>
                                        <option value="2 Dedicated IPv4s">2 Dedicated IPv4s</option>
                                        <option value="5 Dedicated IPv4s (/29 Subnet)">5 Usable Dedicated IPs (/29 Subnet)</option>
                                        <option value="13 Dedicated IPv4s (/28 Subnet)">13 Usable Dedicated IPs (/28 Subnet)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- SECTION 4: WORKLOAD, MANAGEMENT & SLA -->
                        <div class="cf-rfq-card">
                            <div class="cf-rfq-card-header">
                                <div class="cf-rfq-card-icon"><i class="las la-tasks"></i></div>
                                <div>
                                    <h3 class="cf-rfq-card-title">4. Intended Workload, Support &amp; Special Requirements</h3>
                                    <p class="cf-rfq-card-subtitle">Help our engineers size the network and allocate the right hardware.</p>
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold text-slate-800 fs-13 mb-1">Management &amp; Support Level</label>
                                    <select class="form-select fs-13" id="rfqManagement" onchange="updateSummary()">
                                        <option value="Self-Managed (Full Root Access)" selected>Self-Managed (Full Root Access)</option>
                                        <option value="Semi-Managed (OS Security &amp; Kernel Updates)">Semi-Managed (OS &amp; Kernel Hardening)</option>
                                        <option value="Fully Managed Enterprise 24/7 SLA">Fully Managed 24/7 SLA (Hands-off Management)</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold text-slate-800 fs-13 mb-1">Target Monthly Budget (USD)</label>
                                    <select class="form-select fs-13" id="rfqBudget" onchange="updateSummary()">
                                        <option value="Under $50 / month">Under $50 / month</option>
                                        <option value="$50 – $150 / month" selected>$50 – $150 / month</option>
                                        <option value="$150 – $300 / month">$150 – $300 / month</option>
                                        <option value="$300 – $600 / month">$300 – $600 / month</option>
                                        <option value="$600+ / month (Enterprise Tier)">$600+ / month (Enterprise Tier)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold text-slate-800 fs-13 mb-1">Project Purpose / Workload Description</label>
                                <input type="text" class="form-control fs-13" id="rfqWorkload" placeholder="e.g. Enterprise ERP, High Traffic WooCommerce store, PostgreSQL Cluster, Game Server" oninput="updateSummary()">
                            </div>

                            <div>
                                <label class="form-label fw-bold text-slate-800 fs-13 mb-1">Additional Requirements / Notes</label>
                                <textarea class="form-control fs-13" id="rfqNotes" rows="3" placeholder="Tell us if you require custom port forwardings, private VLAN, BGP peering, DDoS mitigation requirements, or special backup retention..."></textarea>
                            </div>
                        </div>

                        <!-- SECTION 5: CONTACT & BUSINESS DETAILS -->
                        <div class="cf-rfq-card">
                            <div class="cf-rfq-card-header">
                                <div class="cf-rfq-card-icon"><i class="las la-user-check"></i></div>
                                <div>
                                    <h3 class="cf-rfq-card-title">5. Client &amp; Organization Details</h3>
                                    <p class="cf-rfq-card-subtitle">Where should we deliver your formal quotation?</p>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold text-slate-800 fs-13 mb-1">First Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control fs-13" id="rfqFname" required value="<?php echo htmlspecialchars($clientProfile['firstname']); ?>">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold text-slate-800 fs-13 mb-1">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control fs-13" id="rfqLname" required value="<?php echo htmlspecialchars($clientProfile['lastname']); ?>">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold text-slate-800 fs-13 mb-1">Business / Company Name</label>
                                    <input type="text" class="form-control fs-13" id="rfqCompany" placeholder="e.g. Acme Tech Solutions LLC" value="<?php echo htmlspecialchars($clientProfile['companyname']); ?>">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold text-slate-800 fs-13 mb-1">Business Email Address <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control fs-13" id="rfqEmail" required value="<?php echo htmlspecialchars($clientProfile['email']); ?>">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold text-slate-800 fs-13 mb-1">Contact Phone Number <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control fs-13" id="rfqPhone" required placeholder="+94 77 123 4567" value="<?php echo htmlspecialchars($clientProfile['phonenumber']); ?>">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold text-slate-800 fs-13 mb-1">Country</label>
                                    <select class="form-select fs-13" id="rfqCountry">
                                        <option value="LK" <?php echo ($clientProfile['country'] === 'LK') ? 'selected' : ''; ?>>Sri Lanka</option>
                                        <option value="US" <?php echo ($clientProfile['country'] === 'US') ? 'selected' : ''; ?>>United States</option>
                                        <option value="GB" <?php echo ($clientProfile['country'] === 'GB') ? 'selected' : ''; ?>>United Kingdom</option>
                                        <option value="AE" <?php echo ($clientProfile['country'] === 'AE') ? 'selected' : ''; ?>>United Arab Emirates</option>
                                        <option value="SG" <?php echo ($clientProfile['country'] === 'SG') ? 'selected' : ''; ?>>Singapore</option>
                                        <option value="AU" <?php echo ($clientProfile['country'] === 'AU') ? 'selected' : ''; ?>>Australia</option>
                                        <option value="IN" <?php echo ($clientProfile['country'] === 'IN') ? 'selected' : ''; ?>>India</option>
                                        <option value="DE" <?php echo ($clientProfile['country'] === 'DE') ? 'selected' : ''; ?>>Germany</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- SECTION 6 (BOTTOM SUMMARY & SUBMISSION): REVIEW & DISPATCH RFQ -->
                        <div class="cf-rfq-bottom-summary" id="rfqSummarySection">
                            <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-2 mb-3.5 pb-3 border-bottom">
                                <div class="d-flex align-items-center gap-2.5">
                                    <div class="cf-rfq-card-icon" style="background: #EFF6FF; color: #2563EB;">
                                        <i class="las la-file-invoice fs-22"></i>
                                    </div>
                                    <div>
                                        <h4 class="fw-800 text-slate-900 fs-17 mb-0">6. Quotation Specification Summary</h4>
                                        <p class="text-secondary fs-12 mb-0">Review your customized infrastructure specifications before submitting.</p>
                                    </div>
                                </div>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill fs-11 px-3 py-1.5 fw-bold align-self-start align-self-sm-auto">
                                    <i class="las la-shield-alt me-1"></i> Official Enterprise RFQ
                                </span>
                            </div>

                            <!-- Selected Base Model Banner -->
                            <div class="cf-rfq-base-banner d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
                                <div>
                                    <div class="fs-11 fw-bold text-slate-500 text-uppercase letter-spacing-1">Selected Deployment Model</div>
                                    <h5 class="fs-18 fw-800 text-slate-900 mb-0 mt-0.5" id="summaryModelText"><?php echo htmlspecialchars($prefillPlanName); ?></h5>
                                </div>
                                <div>
                                    <span class="cf-rfq-tier-pill" id="summaryTierBadge"><?php echo htmlspecialchars($prefillType === 'dedicated' ? 'Dedicated Bare Metal Server' : 'KVM Cloud VPS'); ?></span>
                                </div>
                            </div>

                            <!-- Spec Overview Grid (8 tiles) -->
                            <div class="row g-2.5 mb-3.5">
                                <div class="col-6 col-md-3">
                                    <div class="cf-rfq-spec-pill">
                                        <span class="cf-rfq-spec-label"><i class="las la-microchip me-1 text-primary"></i>Processor</span>
                                        <span class="cf-rfq-spec-val" id="summaryCpu"><?php echo $prefillCpu; ?></span>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="cf-rfq-spec-pill">
                                        <span class="cf-rfq-spec-label"><i class="las la-memory me-1 text-purple"></i>RAM Memory</span>
                                        <span class="cf-rfq-spec-val" id="summaryRam"><?php echo $prefillRam; ?></span>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="cf-rfq-spec-pill">
                                        <span class="cf-rfq-spec-label"><i class="las la-hdd me-1 text-emerald"></i>Storage Array</span>
                                        <span class="cf-rfq-spec-val" id="summaryStorage">80 GB Gen4 NVMe</span>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="cf-rfq-spec-pill">
                                        <span class="cf-rfq-spec-label"><i class="las la-shield-alt me-1 text-warning"></i>RAID Array</span>
                                        <span class="cf-rfq-spec-val" id="summaryRaid">Hardware RAID-1</span>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="cf-rfq-spec-pill">
                                        <span class="cf-rfq-spec-label"><i class="las la-desktop me-1 text-info"></i>Operating System</span>
                                        <span class="cf-rfq-spec-val" id="summaryOs">Ubuntu 24.04 LTS</span>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="cf-rfq-spec-pill">
                                        <span class="cf-rfq-spec-label"><i class="las la-tools me-1 text-danger"></i>Control Panel</span>
                                        <span class="cf-rfq-spec-val" id="summaryPanel">Root SSH</span>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="cf-rfq-spec-pill">
                                        <span class="cf-rfq-spec-label"><i class="las la-globe me-1 text-primary"></i>Datacenter</span>
                                        <span class="cf-rfq-spec-val" id="summaryLocation">Frankfurt, DE</span>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="cf-rfq-spec-pill">
                                        <span class="cf-rfq-spec-label"><i class="las la-network-wired me-1 text-secondary"></i>Dedicated IPs</span>
                                        <span class="cf-rfq-spec-val" id="summaryIps">1 Dedicated IPv4</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Zero Initial Payment Reassurance Box -->
                            <div class="cf-rfq-reassurance-box mb-3.5">
                                <div class="d-flex align-items-start gap-2.5">
                                    <i class="las la-info-circle text-primary fs-20 mt-0.5 flex-shrink-0"></i>
                                    <div>
                                        <div class="fw-bold text-slate-900 fs-13 mb-0.5">Zero Upfront Payment Required &bull; Free Architecture Review</div>
                                        <div class="text-secondary fs-12 lh-sm">
                                            Our senior infrastructure architects will verify datacenter availability, apply eligible volume discounts, and issue your official quotation within 1-2 hours. You can review, approve, and pay only after receiving your custom proposal.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Validation Alert Box -->
                            <div class="alert alert-danger d-none fs-13 py-2.5 px-3 mb-3 border-0 rounded-3 shadow-xs" id="rfqAlertBox"></div>

                            <!-- Submit Button -->
                            <button type="submit" class="cf-rfq-btn-submit" id="btnSubmitRfq">
                                <span class="btn-text">
                                    <i class="las la-paper-plane me-1.5 fs-18"></i> Submit Server Quotation Request &rarr;
                                </span>
                                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            </button>

                            <!-- Trust Guarantee Badges -->
                            <div class="d-flex align-items-center justify-content-center gap-3 text-secondary fs-12 mt-3 flex-wrap">
                                <span><i class="las la-lock text-success me-1 fs-14"></i>256-Bit SSL Encrypted</span>
                                <span>&bull;</span>
                                <span><i class="las la-clock text-primary me-1 fs-14"></i>1-2 Hour Engineering Review</span>
                                <span>&bull;</span>
                                <span><i class="las la-shield-alt text-success me-1 fs-14"></i>99.95% Network SLA Guarantee</span>
                            </div>
                        </div>

                    </div>
                </div>
            </form>
        </div>
    </section>

    <!-- Footer Area -->
    <?php include 'include/footer.inc.php'; ?>

    <!-- JavaScript Area -->
    <?php include 'include/js.inc.php'; ?>

    <script>
        let rfqState = {
            serverType: '<?php echo $prefillType === 'dedicated' ? 'Dedicated Bare Metal Server' : 'KVM Cloud VPS'; ?>',
            cpu: '2 vCPUs',
            ram: '4 GB RAM'
        };

        function selectRfqTier(tierName, el) {
            rfqState.serverType = tierName;
            document.querySelectorAll('#serverTierGrid .cf-rfq-pill-card').forEach(c => c.classList.remove('active'));
            if (el) el.classList.add('active');
            document.getElementById('summaryTierBadge').textContent = tierName;
            updateSummary();
        }

        function selectRfqCpu(cpuName, el) {
            rfqState.cpu = cpuName;
            document.querySelectorAll('#rfqCpuGrid .cf-rfq-pill-card').forEach(c => c.classList.remove('active'));
            if (el) el.classList.add('active');
            updateSummary();
        }

        function selectRfqRam(ramName, el) {
            rfqState.ram = ramName;
            document.querySelectorAll('#rfqRamGrid .cf-rfq-pill-card').forEach(c => c.classList.remove('active'));
            if (el) el.classList.add('active');
            updateSummary();
        }

        function updateSummary() {
            const planInput = document.getElementById('rfqBasePlan');
            const modelText = planInput ? (planInput.value.trim() || 'Custom Server Solution') : 'Custom Server Solution';
            const mEl = document.getElementById('summaryModelText');
            if (mEl) mEl.textContent = modelText;

            const cEl = document.getElementById('summaryCpu');
            if (cEl) cEl.textContent = rfqState.cpu;

            const rEl = document.getElementById('summaryRam');
            if (rEl) rEl.textContent = rfqState.ram;

            const storageEl = document.getElementById('rfqStorage');
            if (storageEl) {
                const sEl = document.getElementById('summaryStorage');
                if (sEl) sEl.textContent = storageEl.value;
            }

            const raidEl = document.getElementById('rfqRaid');
            if (raidEl) {
                const rdEl = document.getElementById('summaryRaid');
                if (rdEl) rdEl.textContent = raidEl.value.split('(')[0].trim();
            }

            const osEl = document.getElementById('rfqOs');
            if (osEl) {
                const oEl = document.getElementById('summaryOs');
                if (oEl) oEl.textContent = osEl.value.split('(')[0].trim();
            }

            const panelEl = document.getElementById('rfqPanel');
            if (panelEl) {
                const pEl = document.getElementById('summaryPanel');
                if (pEl) pEl.textContent = panelEl.value.split('(')[0].trim();
            }

            const locEl = document.getElementById('rfqDatacenter');
            if (locEl) {
                const lEl = document.getElementById('summaryLocation');
                if (lEl) lEl.textContent = locEl.value.split('(')[0].trim();
            }

            const ipsEl = document.getElementById('rfqIps');
            if (ipsEl) {
                const iEl = document.getElementById('summaryIps');
                if (iEl) iEl.textContent = ipsEl.value.split('(')[0].trim();
            }
        }

        function submitServerQuoteRequest(e) {
            e.preventDefault();

            const alertBox = document.getElementById('rfqAlertBox');
            alertBox.classList.add('d-none');
            alertBox.textContent = '';

            const btn = document.getElementById('btnSubmitRfq');
            const btnText = btn.querySelector('.btn-text');
            const spinner = btn.querySelector('.spinner-border');

            const fname = document.getElementById('rfqFname').value.trim();
            const lname = document.getElementById('rfqLname').value.trim();
            const email = document.getElementById('rfqEmail').value.trim();
            const phone = document.getElementById('rfqPhone').value.trim();

            if (!fname || !lname) {
                alertBox.textContent = 'Please enter your first and last name.';
                alertBox.classList.remove('d-none');
                return;
            }
            if (!email || !email.includes('@')) {
                alertBox.textContent = 'Please enter a valid email address.';
                alertBox.classList.remove('d-none');
                return;
            }
            if (!phone) {
                alertBox.textContent = 'Please provide your contact phone number.';
                alertBox.classList.remove('d-none');
                return;
            }

            btnText.classList.add('d-none');
            spinner.classList.remove('d-none');
            btn.disabled = true;

            const payload = {
                server_type: rfqState.serverType,
                base_plan: document.getElementById('rfqBasePlan').value.trim() || 'Custom Solution',
                hostname: document.getElementById('rfqHostname').value.trim(),
                cpu: rfqState.cpu,
                ram: rfqState.ram,
                storage: document.getElementById('rfqStorage').value,
                raid: document.getElementById('rfqRaid').value,
                os: document.getElementById('rfqOs').value,
                control_panel: document.getElementById('rfqPanel').value,
                datacenter: document.getElementById('rfqDatacenter').value,
                ips: document.getElementById('rfqIps').value,
                management: document.getElementById('rfqManagement').value,
                budget: document.getElementById('rfqBudget').value,
                workload: document.getElementById('rfqWorkload').value.trim(),
                notes: document.getElementById('rfqNotes').value.trim(),
                firstname: fname,
                lastname: lname,
                companyname: document.getElementById('rfqCompany').value.trim(),
                email: email,
                phone: phone,
                country: document.getElementById('rfqCountry').value
            };

            fetch('api/quote-request.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(data => {
                btnText.classList.remove('d-none');
                spinner.classList.add('d-none');
                btn.disabled = false;

                if (data.status === 'success') {
                    // Success state
                    document.getElementById('cfRfqForm').innerHTML = `
                        <div class="cf-rfq-card text-center py-5">
                            <div class="mb-3" style="width: 68px; height: 68px; border-radius: 50%; background: #DCFCE7; color: #16A34A; display: inline-flex; align-items: center; justify-content: center; font-size: 36px;">
                                <i class="las la-check-circle"></i>
                            </div>
                            <h3 class="fw-800 text-slate-900 fs-24 mb-2">Quotation Request #RFQ-${data.quote_id} Submitted!</h3>
                            <p class="text-secondary fs-14 mb-4 mx-auto" style="max-width: 580px;">
                                Thank you, <strong>${fname}</strong>! Your server architecture requirements have been logged in our enterprise provisioning queue. Our Infrastructure Engineering team is reviewing node availability and will dispatch your official quotation within <strong>1-2 business hours</strong> to <strong>${email}</strong>.
                            </p>
                            <div class="d-flex align-items-center justify-content-center gap-3 flex-wrap">
                                <a href="${data.redirect_url}" class="btn btn-primary rounded-pill px-4 py-2.5 fw-bold fs-13">
                                    <i class="las la-file-invoice me-1"></i> Track in Client Portal &rarr;
                                </a>
                                <a href="<?php echo $url; ?>" class="btn btn-outline-secondary rounded-pill px-4 py-2.5 fw-bold fs-13">
                                    Back to Home
                                </a>
                            </div>
                        </div>
                    `;
                } else {
                    alertBox.textContent = data.message || 'Failed to submit quotation request. Please try again.';
                    alertBox.classList.remove('d-none');
                }
            })
            .catch(err => {
                console.error(err);
                btnText.classList.remove('d-none');
                spinner.classList.add('d-none');
                btn.disabled = false;
                alertBox.textContent = 'Server connection error. Please try again.';
                alertBox.classList.remove('d-none');
            });
        }

        // Init
        document.addEventListener('DOMContentLoaded', updateSummary);
    </script>
</body>
</html>

