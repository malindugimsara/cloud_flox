<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'include/url.inc.php';
require_once __DIR__ . '/api/whmcs.php';

// Auth Guard: Require login
if (empty($_SESSION['cf_client_logged_in'])) {
    header("Location: " . $url . "login?redirect=clientarea");
    exit;
}

$clientId    = (int)($_SESSION['cf_client_id'] ?? 0);
$clientEmail = $_SESSION['cf_client_email'] ?? '';
$clientName  = $_SESSION['cf_client_name'] ?? 'Client';

$supportedCountries = [
    'LK' => 'Sri Lanka',
    'US' => 'United States',
    'GB' => 'United Kingdom',
    'AU' => 'Australia',
    'CA' => 'Canada',
    'IN' => 'India',
    'SG' => 'Singapore',
    'AE' => 'United Arab Emirates',
    'DE' => 'Germany',
    'FR' => 'France',
    'MY' => 'Malaysia',
    'JP' => 'Japan',
    'NZ' => 'New Zealand',
    'NL' => 'Netherlands',
    'IT' => 'Italy',
    'ES' => 'Spain',
    'CH' => 'Switzerland',
    'SE' => 'Sweden',
    'NO' => 'Norway',
    'IE' => 'Ireland',
    'SA' => 'Saudi Arabia',
    'QA' => 'Qatar',
    'PK' => 'Pakistan',
    'BD' => 'Bangladesh',
    'MV' => 'Maldives',
    'TH' => 'Thailand',
    'ID' => 'Indonesia',
    'PH' => 'Philippines',
    'ZA' => 'South Africa',
    'BR' => 'Brazil'
];

$profileSuccess = '';
$profileError   = '';

// Handle Profile Update POST
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    $pFirstName   = trim($_POST['firstname'] ?? '');
    $pLastName    = trim($_POST['lastname'] ?? '');
    $pCompanyName = trim($_POST['companyname'] ?? '');
    $pPhoneNumber = trim($_POST['phonenumber'] ?? '');
    $pAddress1    = trim($_POST['address1'] ?? '');
    $pAddress2    = trim($_POST['address2'] ?? '');
    $pCity        = trim($_POST['city'] ?? '');
    $pState       = trim($_POST['state'] ?? '');
    $pPostcode    = trim($_POST['postcode'] ?? '');
    $pCountry     = strtoupper(trim($_POST['country'] ?? 'LK'));
    
    $pNewPassword = $_POST['new_password'] ?? '';
    $pConfirmPass = $_POST['confirm_password'] ?? '';

    if (empty($pFirstName) || empty($pLastName)) {
        $profileError = 'Please provide both your First Name and Last Name.';
    } elseif (empty($pPhoneNumber)) {
        $profileError = 'Please provide a valid contact phone number.';
    } elseif (empty($pAddress1)) {
        $profileError = 'Please enter your street address.';
    } elseif (empty($pCity)) {
        $profileError = 'Please enter your city.';
    } elseif (!empty($pNewPassword) && strlen($pNewPassword) < 6) {
        $profileError = 'New password must be at least 6 characters long.';
    } elseif (!empty($pNewPassword) && $pNewPassword !== $pConfirmPass) {
        $profileError = 'New passwords do not match. Please re-enter.';
    } else {
        try {
            $mysqli = @new mysqli('127.0.0.1', 'cfwhmcs_whmcs', '+V)Rb%K3wFiK', 'cfwhmcs_whmcs3');
            if (!$mysqli->connect_error && $clientId > 0) {
                if (!empty($pNewPassword)) {
                    $passHash = password_hash($pNewPassword, PASSWORD_DEFAULT);
                    $up = $mysqli->prepare("UPDATE tblclients SET firstname = ?, lastname = ?, companyname = ?, phonenumber = ?, address1 = ?, address2 = ?, city = ?, state = ?, postcode = ?, country = ?, password = ?, updated_at = NOW() WHERE id = ?");
                    if ($up) {
                        $up->bind_param('sssssssssssi', $pFirstName, $pLastName, $pCompanyName, $pPhoneNumber, $pAddress1, $pAddress2, $pCity, $pState, $pPostcode, $pCountry, $passHash, $clientId);
                        $up->execute();
                        $up->close();
                    }
                } else {
                    $up = $mysqli->prepare("UPDATE tblclients SET firstname = ?, lastname = ?, companyname = ?, phonenumber = ?, address1 = ?, address2 = ?, city = ?, state = ?, postcode = ?, country = ?, updated_at = NOW() WHERE id = ?");
                    if ($up) {
                        $up->bind_param('ssssssssssi', $pFirstName, $pLastName, $pCompanyName, $pPhoneNumber, $pAddress1, $pAddress2, $pCity, $pState, $pPostcode, $pCountry, $clientId);
                        $up->execute();
                        $up->close();
                    }
                }

                // Sync with WHMCS API if available
                try {
                    $whmcsUpdateData = [
                        'clientid'    => $clientId,
                        'firstname'   => $pFirstName,
                        'lastname'    => $pLastName,
                        'companyname' => $pCompanyName,
                        'phonenumber' => $pPhoneNumber,
                        'address1'    => $pAddress1,
                        'address2'    => $pAddress2,
                        'city'        => $pCity,
                        'state'       => $pState,
                        'postcode'    => $pPostcode,
                        'country'     => $pCountry
                    ];
                    if (!empty($pNewPassword)) {
                        $whmcsUpdateData['password2'] = $pNewPassword;
                    }
                    WHMCSClient::call('UpdateClient', $whmcsUpdateData);
                } catch (Exception $ex) {}

                $clientName = trim($pFirstName . ' ' . $pLastName);
                $_SESSION['cf_client_name'] = $clientName;
                $profileSuccess = 'Your personal and billing information has been updated successfully!';
            }
        } catch (Exception $e) {
            $profileError = 'An error occurred while saving your profile: ' . $e->getMessage();
        }
    }
}

// Initialize data containers
$services = [];
$domains  = [];
$invoices = [];
$tickets  = [];

$clientProfile = [
    'firstname'   => '',
    'lastname'    => '',
    'companyname' => '',
    'email'       => $clientEmail,
    'phonenumber' => '',
    'address1'    => '',
    'address2'    => '',
    'city'        => '',
    'state'       => '',
    'postcode'    => '',
    'country'     => 'LK'
];

$statServices = 0;
$statDomains  = 0;
$statInvoicesUnpaid = 0;
$statTicketsOpen = 0;

// Fetch data from local WHMCS database or API fallback
try {
    $mysqli = @new mysqli('127.0.0.1', 'cfwhmcs_whmcs', '+V)Rb%K3wFiK', 'cfwhmcs_whmcs3');
    if (!$mysqli->connect_error && $clientId > 0) {
        // 1. Fetch Services
        $stmt = $mysqli->prepare("SELECT h.id, h.domain, h.domainstatus, h.amount, h.billingcycle, h.nextduedate, p.name as product_name FROM tblhosting h LEFT JOIN tblproducts p ON p.id = h.packageid WHERE h.userid = ? ORDER BY h.id DESC");
        if ($stmt) {
            $stmt->bind_param('i', $clientId);
            $stmt->execute();
            $services = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
        }

        // 2. Fetch Domains
        $stmt = $mysqli->prepare("SELECT id, domain, status, registrationdate, nextduedate, recurringamount FROM tbldomains WHERE userid = ? ORDER BY id DESC");
        if ($stmt) {
            $stmt->bind_param('i', $clientId);
            $stmt->execute();
            $domains = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
        }

        // 3. Fetch Invoices
        $stmt = $mysqli->prepare("SELECT id, invoicenum, date, duedate, total, status FROM tblinvoices WHERE userid = ? ORDER BY id DESC LIMIT 20");
        if ($stmt) {
            $stmt->bind_param('i', $clientId);
            $stmt->execute();
            $invoices = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
        }

        // 4. Fetch Support Tickets
        $stmt = $mysqli->prepare("SELECT id, tid, title, date, lastreply, status, urgency FROM tbltickets WHERE userid = ? ORDER BY lastreply DESC LIMIT 20");
        if ($stmt) {
            $stmt->bind_param('i', $clientId);
            $stmt->execute();
            $tickets = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
        }

        // 5. Fetch Client Profile Details
        $stmt = $mysqli->prepare("SELECT id, firstname, lastname, companyname, email, phonenumber, address1, address2, city, state, postcode, country FROM tblclients WHERE id = ? LIMIT 1");
        if ($stmt) {
            $stmt->bind_param('i', $clientId);
            $stmt->execute();
            $profRes = $stmt->get_result();
            if ($profRes && $profRow = $profRes->fetch_assoc()) {
                $clientProfile = array_merge($clientProfile, $profRow);
                if (empty($clientName) || $clientName === 'Client') {
                    $clientName = trim(($profRow['firstname'] ?? '') . ' ' . ($profRow['lastname'] ?? ''));
                    $_SESSION['cf_client_name'] = $clientName;
                }
            }
            $stmt->close();
        }

        // Calculate counts
        foreach ($services as $s) {
            if (strtolower($s['domainstatus']) === 'active') $statServices++;
        }
        foreach ($domains as $d) {
            if (strtolower($d['status']) === 'active') $statDomains++;
        }
        foreach ($invoices as $inv) {
            if (strtolower($inv['status']) === 'unpaid') $statInvoicesUnpaid++;
        }
        foreach ($tickets as $t) {
            if (in_array(strtolower($t['status']), ['open', 'customer-reply', 'in progress'])) $statTicketsOpen++;
        }
    }
} catch (Exception $e) {
    // Graceful error handling
}

$isProfileActive = (!empty($profileSuccess) || !empty($profileError) || (isset($_GET['tab']) && $_GET['tab'] === 'profile'));

// Format status badge helper
function getStatusBadge($status) {
    $st = strtolower(trim($status));
    if ($st === 'active' || $st === 'paid') {
        return '<span class="cf-badge-active"><i class="las la-check-circle"></i> ' . htmlspecialchars($status) . '</span>';
    } elseif ($st === 'pending' || $st === 'in progress') {
        return '<span class="cf-badge-pending"><i class="las la-clock"></i> ' . htmlspecialchars($status) . '</span>';
    } elseif ($st === 'unpaid' || $st === 'overdue' || $st === 'cancelled') {
        return '<span class="cf-badge-unpaid"><i class="las la-exclamation-circle"></i> ' . htmlspecialchars($status) . '</span>';
    } else {
        return '<span class="cf-badge-suspended"><i class="las la-info-circle"></i> ' . htmlspecialchars($status) . '</span>';
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
    <title>Client Portal Dashboard — CloudFlox Cloud Infrastructure</title>

    <!-- SEO Meta -->
    <meta name="description" content="Manage your CloudFlox hosting services, registered domains, billing invoices, and support tickets in one integrated dashboard.">
    <meta name="robots" content="noindex, follow">

    <!-- =========== CSS Area =========== -->
    <?php include 'include/css.inc.php'; ?>
    <!-- =========== CSS Area =========== -->
</head>

<body class="pure-white-theme">

    <!-- =========== Header Area =========== -->
    <?php include 'include/header.inc.php'; ?>
    <!-- =========== Header Area =========== -->

    <!-- =========================================================================
         1. DASHBOARD HERO BANNER (Integrated Welcome & Action Center)
         ========================================================================= -->
    <section class="cf-dashboard-hero text-white position-relative">
        <div class="container cf-dashboard-container position-relative" style="z-index: 2;">
            <!-- Top sub-navigation & status indicator -->
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                <a href="<?php echo $url; ?>" class="cf-dash-back-link">
                    <i class="las la-arrow-left"></i> Return to Homepage
                </a>
                <div class="d-inline-flex align-items-center gap-2">
                    <span class="cf-hero-status-pill">
                        <span class="cf-pulse-dot-green"></span> High Availability &bull; All Systems Normal
                    </span>
                </div>
            </div>

            <!-- Main Greeting & Action Row -->
            <div class="row align-items-center justify-content-between gy-3">
                <div class="col-lg-7">
                    <div class="d-inline-flex align-items-center gap-1.5 px-2.5 py-0.5 rounded-pill cf-hero-session-pill mb-2 border" style="padding: 10px;">
                        <span class="cf-pulse-dot-blue" style="margin-right: 5px; margin-left: 5px;"></span> Active Client Session
                    </div>
                    <h1 class="h2 fw-extrabold text-white mb-2 tracking-tight">
                        Welcome back, <span class="text-gradient-cyan"><?php echo htmlspecialchars($clientName); ?></span>!
                    </h1>
                    <div class="cf-hero-meta d-flex align-items-center flex-wrap gap-2 text-white-50 fs-13">
                        <span class="d-inline-flex align-items-center gap-1">
                            <i class="las la-envelope text-white-50"></i> <?php echo htmlspecialchars($clientEmail); ?>
                        </span>
                        <span class="text-white-20 d-none d-sm-inline">&bull;</span>
                        <span class="cf-hero-meta-badge">
                            <i class="las la-id-badge" style="color: #67E8F9;"></i> Client ID #<?php echo $clientId; ?>
                        </span>
                        <span class="text-white-20 d-none d-sm-inline">&bull;</span>
                        <span class="cf-hero-meta-badge" style="color: #6EE7B7;">
                            <i class="las la-shield-alt" style="color: #34D399;"></i> Verified Account
                        </span>
                    </div>
                </div>

                <div class="col-lg-5 text-lg-end">
                    <div class="d-flex align-items-center justify-content-lg-end gap-2 flex-wrap mb-2.5">
                        <a href="<?php echo $url; ?>web-hosting" class="btn cf-btn-hero-primary">
                            <i class="las la-plus-circle"></i> Deploy Service
                        </a>
                        <a href="<?php echo $url; ?>domain" class="btn cf-btn-hero-white">
                            <i class="las la-globe text-primary"></i> New Domain
                        </a>
                        <button type="button" class="btn cf-btn-hero-white" onclick="document.getElementById('profile-tab').click(); document.getElementById('clientDashboardTabs').scrollIntoView({behavior: 'smooth'});">
                            <i class="las la-user-edit text-primary"></i> Edit Profile
                        </button>
                        <a href="<?php echo $url; ?>login?action=logout" class="btn cf-btn-hero-logout" title="Sign out of your account">
                            <i class="las la-sign-out-alt"></i> Sign Out
                        </a>
                    </div>
                    
                </div>
            </div>
        </div>
    </section>

    <!-- =========================================================================
         2. DASHBOARD STATS & CONTENT AREA (Spacious & Clean Layout)
         ========================================================================= -->
    <main class="cf-dashboard-body-wrap">
        <div class="container cf-dashboard-container">

            <!-- 4 Quick Stats Grid -->
            <div class="row g-3 g-md-3.5 mb-4">
                <!-- Services Stat -->
                <div class="col-6 col-lg-3">
                    <div class="cf-stat-card">
                        <div class="cf-stat-icon" style="background: #EFF6FF; color: #2563EB;">
                            <i class="las la-server"></i>
                        </div>
                        <div>
                            <div class="fs-24 fw-800 text-slate-900 lh-1 mb-1"><?php echo count($services); ?></div>
                            <div class="fs-12.5 text-secondary fw-semibold">Cloud Services</div>
                        </div>
                    </div>
                </div>

                <!-- Domains Stat -->
                <div class="col-6 col-lg-3">
                    <div class="cf-stat-card">
                        <div class="cf-stat-icon" style="background: #ECFDF5; color: #059669;">
                            <i class="las la-globe"></i>
                        </div>
                        <div>
                            <div class="fs-24 fw-800 text-slate-900 lh-1 mb-1"><?php echo count($domains); ?></div>
                            <div class="fs-12.5 text-secondary fw-semibold">Domains</div>
                        </div>
                    </div>
                </div>

                <!-- Invoices Stat -->
                <div class="col-6 col-lg-3">
                    <div class="cf-stat-card">
                        <div class="cf-stat-icon" style="background: <?php echo $statInvoicesUnpaid > 0 ? '#FEE2E2' : '#F1F5F9'; ?>; color: <?php echo $statInvoicesUnpaid > 0 ? '#DC2626' : '#64748B'; ?>;">
                            <i class="las la-file-invoice-dollar"></i>
                        </div>
                        <div>
                            <div class="fs-24 fw-800 text-slate-900 lh-1 mb-1"><?php echo $statInvoicesUnpaid; ?></div>
                            <div class="fs-12.5 text-secondary fw-semibold">Unpaid Invoices</div>
                        </div>
                    </div>
                </div>

                <!-- Tickets Stat -->
                <div class="col-6 col-lg-3">
                    <div class="cf-stat-card">
                        <div class="cf-stat-icon" style="background: #FAF5FF; color: #9333EA;">
                            <i class="las la-headset"></i>
                        </div>
                        <div>
                            <div class="fs-24 fw-800 text-slate-900 lh-1 mb-1"><?php echo count($tickets); ?></div>
                            <div class="fs-12.5 text-secondary fw-semibold">Support Tickets</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dashboard Main Card with Tabs -->
            <div class="cf-dashboard-card">
                <!-- Nav Tabs -->
                <ul class="nav cf-dashboard-nav-tabs mb-4" id="clientDashboardTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?php echo !$isProfileActive ? 'active' : ''; ?>" id="services-tab" data-bs-toggle="tab" data-bs-target="#services-pane" type="button" role="tab">
                            <i class="las la-server me-1"></i> My Services (<?php echo count($services); ?>)
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="domains-tab" data-bs-toggle="tab" data-bs-target="#domains-pane" type="button" role="tab">
                            <i class="las la-globe me-1"></i> My Domains (<?php echo count($domains); ?>)
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="invoices-tab" data-bs-toggle="tab" data-bs-target="#invoices-pane" type="button" role="tab">
                            <i class="las la-file-invoice-dollar me-1"></i> Invoices &amp; Billing (<?php echo count($invoices); ?>)
                            <?php if ($statInvoicesUnpaid > 0): ?>
                                <span class="badge bg-danger rounded-pill ms-1"><?php echo $statInvoicesUnpaid; ?></span>
                            <?php endif; ?>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tickets-tab" data-bs-toggle="tab" data-bs-target="#tickets-pane" type="button" role="tab">
                            <i class="las la-headset me-1"></i> Support Tickets (<?php echo count($tickets); ?>)
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?php echo $isProfileActive ? 'active' : ''; ?>" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile-pane" type="button" role="tab">
                            <i class="las la-user-edit me-1"></i> My Profile &amp; Details
                        </button>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content" id="clientDashboardContent">

                    <!-- 1. Services Tab -->
                    <div class="tab-pane fade <?php echo !$isProfileActive ? 'show active' : ''; ?>" id="services-pane" role="tabpanel">
                        <?php if (empty($services)): ?>
                            <div class="text-center py-5">
                                <div class="fs-40 text-muted mb-2"><i class="las la-server"></i></div>
                                <h5 class="fw-bold text-slate-800">No Active Cloud Services</h5>
                                <p class="text-secondary fs-13 mb-3">You do not have any cloud hosting services or servers active yet.</p>
                                <a href="<?php echo $url; ?>web-hosting" class="btn btn-primary rounded-pill btn-sm px-4 fw-bold">
                                    <i class="las la-bolt me-1"></i> Deploy High-Speed Hosting
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table cf-dash-table mb-0">
                                    <thead>
                                        <tr>
                                            <th>Product / Service</th>
                                            <th>Assigned Domain</th>
                                            <th>Pricing / Cycle</th>
                                            <th>Next Due Date</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($services as $srv): ?>
                                            <tr>
                                                <td>
                                                    <div class="fw-bold text-slate-900"><?php echo htmlspecialchars($srv['product_name'] ?? 'Cloud Hosting'); ?></div>
                                                    <span class="fs-11 text-muted">ID #<?php echo $srv['id']; ?></span>
                                                </td>
                                                <td>
                                                    <a href="http://<?php echo htmlspecialchars($srv['domain']); ?>" target="_blank" rel="noopener" class="fw-semibold text-primary text-decoration-none">
                                                        <?php echo htmlspecialchars($srv['domain']); ?>
                                                        <i class="las la-external-link-alt fs-11 ms-1"></i>
                                                    </a>
                                                </td>
                                                <td>
                                                    <div class="fw-semibold text-slate-800">$<?php echo number_format((float)$srv['amount'], 2); ?> USD</div>
                                                    <span class="fs-11 text-muted"><?php echo htmlspecialchars($srv['billingcycle']); ?></span>
                                                </td>
                                                <td>
                                                    <span class="fs-13 text-secondary"><?php echo !empty($srv['nextduedate']) && $srv['nextduedate'] !== '0000-00-00' ? htmlspecialchars($srv['nextduedate']) : 'N/A'; ?></span>
                                                </td>
                                                <td>
                                                    <?php echo getStatusBadge($srv['domainstatus']); ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- 2. Domains Tab -->
                    <div class="tab-pane fade" id="domains-pane" role="tabpanel">
                        <?php if (empty($domains)): ?>
                            <div class="text-center py-5">
                                <div class="fs-40 text-muted mb-2"><i class="las la-globe"></i></div>
                                <h5 class="fw-bold text-slate-800">No Domains Registered</h5>
                                <p class="text-secondary fs-13 mb-3">You do not have any active registered domains in this account.</p>
                                <a href="<?php echo $url; ?>domain" class="btn btn-primary rounded-pill btn-sm px-4 fw-bold">
                                    <i class="las la-search me-1"></i> Search &amp; Register Domain
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table cf-dash-table mb-0">
                                    <thead>
                                        <tr>
                                            <th>Domain Name</th>
                                            <th>Registration Date</th>
                                            <th>Next Due Date</th>
                                            <th>Renewal Amount</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($domains as $dom): ?>
                                            <tr>
                                                <td>
                                                    <div class="fw-bold text-slate-900"><?php echo htmlspecialchars($dom['domain']); ?></div>
                                                </td>
                                                <td>
                                                    <span class="fs-13 text-secondary"><?php echo htmlspecialchars($dom['registrationdate']); ?></span>
                                                </td>
                                                <td>
                                                    <span class="fs-13 text-secondary"><?php echo htmlspecialchars($dom['nextduedate']); ?></span>
                                                </td>
                                                <td>
                                                    <div class="fw-semibold text-slate-800">$<?php echo number_format((float)$dom['recurringamount'], 2); ?> USD</div>
                                                </td>
                                                <td>
                                                    <?php echo getStatusBadge($dom['status']); ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- 3. Invoices Tab -->
                    <div class="tab-pane fade" id="invoices-pane" role="tabpanel">
                        <?php if (empty($invoices)): ?>
                            <div class="text-center py-5">
                                <div class="fs-40 text-muted mb-2"><i class="las la-file-invoice"></i></div>
                                <h5 class="fw-bold text-slate-800">No Invoices Found</h5>
                                <p class="text-secondary fs-13 mb-0">You do not have any invoices generated for your account.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table cf-dash-table mb-0">
                                    <thead>
                                        <tr>
                                            <th>Invoice #</th>
                                            <th>Invoice Date</th>
                                            <th>Due Date</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($invoices as $inv): ?>
                                            <tr>
                                                <td>
                                                    <span class="fw-bold text-slate-900">#<?php echo htmlspecialchars($inv['invoicenum'] ?: $inv['id']); ?></span>
                                                </td>
                                                <td>
                                                    <span class="fs-13 text-secondary"><?php echo htmlspecialchars($inv['date']); ?></span>
                                                </td>
                                                <td>
                                                    <span class="fs-13 text-secondary"><?php echo htmlspecialchars($inv['duedate']); ?></span>
                                                </td>
                                                <td>
                                                    <div class="fw-bold text-slate-900">$<?php echo number_format((float)$inv['total'], 2); ?> USD</div>
                                                </td>
                                                <td>
                                                    <?php echo getStatusBadge($inv['status']); ?>
                                                </td>
                                                <td>
                                                    <?php if (strtolower($inv['status']) === 'unpaid'): ?>
                                                        <a href="<?php echo $url; ?>invoice?id=<?php echo $inv['id']; ?>&paynow=1" class="btn btn-danger btn-sm rounded-pill px-3 py-1 fs-12 fw-bold">
                                                            <i class="las la-credit-card me-1"></i> Pay Now
                                                        </a>
                                                    <?php else: ?>
                                                        <a href="<?php echo $url; ?>invoice?id=<?php echo $inv['id']; ?>" class="btn btn-light btn-sm rounded-pill px-3 py-1 fs-12 fw-semibold text-secondary border">
                                                            <i class="las la-file-invoice me-1"></i> View
                                                        </a>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- 4. Support Tickets Tab -->
                    <div class="tab-pane fade" id="tickets-pane" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold text-slate-900 mb-0">Recent Tickets</h6>
                            <a href="<?php echo $url; ?>contact-us" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-semibold">
                                <i class="las la-plus-circle me-1"></i> Open Support Ticket
                            </a>
                        </div>
                        <?php if (empty($tickets)): ?>
                            <div class="text-center py-5">
                                <div class="fs-40 text-muted mb-2"><i class="las la-headset"></i></div>
                                <h5 class="fw-bold text-slate-800">No Support Tickets</h5>
                                <p class="text-secondary fs-13 mb-3">Everything is running smoothly! You don't have any support tickets.</p>
                                <a href="<?php echo $url; ?>contact-us" class="btn btn-primary rounded-pill btn-sm px-4 fw-bold">
                                    <i class="las la-comment-alt me-1"></i> Contact CloudFlox Support
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table cf-dash-table mb-0">
                                    <thead>
                                        <tr>
                                            <th>Ticket #</th>
                                            <th>Subject</th>
                                            <th>Last Updated</th>
                                            <th>Urgency</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($tickets as $tkt): ?>
                                            <tr>
                                                <td>
                                                    <span class="fw-bold text-slate-900">#<?php echo htmlspecialchars($tkt['tid'] ?: $tkt['id']); ?></span>
                                                </td>
                                                <td>
                                                    <div class="fw-semibold text-slate-900"><?php echo htmlspecialchars($tkt['title']); ?></div>
                                                </td>
                                                <td>
                                                    <span class="fs-13 text-secondary"><?php echo htmlspecialchars($tkt['lastreply']); ?></span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light text-dark border"><?php echo htmlspecialchars($tkt['urgency']); ?></span>
                                                </td>
                                                <td>
                                                    <?php echo getStatusBadge($tkt['status']); ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- 5. Edit Profile & Details Tab -->
                    <div class="tab-pane fade <?php echo $isProfileActive ? 'show active' : ''; ?>" id="profile-pane" role="tabpanel">
                        <?php if (!empty($profileSuccess)): ?>
                            <div class="alert alert-success d-flex align-items-center gap-2 mb-4 border-0 rounded-3 shadow-xs" role="alert">
                                <i class="las la-check-circle fs-20 text-success"></i>
                                <div><strong>Success!</strong> <?php echo htmlspecialchars($profileSuccess); ?></div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($profileError)): ?>
                            <div class="alert alert-danger d-flex align-items-center gap-2 mb-4 border-0 rounded-3 shadow-xs" role="alert">
                                <i class="las la-exclamation-circle fs-20 text-danger"></i>
                                <div><strong>Error:</strong> <?php echo htmlspecialchars($profileError); ?></div>
                            </div>
                        <?php endif; ?>

                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4 pb-3 border-bottom">
                            <div>
                                <h4 class="fw-bold text-slate-900 mb-1">
                                    <i class="las la-user-cog text-primary me-1.5"></i> Personal &amp; Billing Profile
                                </h4>
                                <p class="text-secondary fs-13 mb-0">Update your verified contact information, company name, billing address and account password.</p>
                            </div>
                            <span class="badge bg-light text-secondary border px-3 py-2 rounded-pill fs-12">
                                <i class="las la-id-badge text-primary me-1"></i> Client ID #<?php echo $clientId; ?>
                            </span>
                        </div>

                        <form method="POST" action="<?php echo $url; ?>clientarea.php?tab=profile" class="needs-validation">
                            <input type="hidden" name="action" value="update_profile">

                            <!-- BLOCK 1: PERSONAL INFORMATION -->
                            <div class="card border mb-4 shadow-none rounded-3 overflow-hidden">
                                <div class="card-header bg-light py-3 px-3.5 border-bottom">
                                    <div class="d-flex align-items-center gap-2">
                                        <div style="width: 28px; height: 28px; border-radius: 50%; background: #EFF6FF; color: #2563EB; display: flex; align-items: center; justify-content: center; font-size: 14px;">
                                            <i class="las la-user"></i>
                                        </div>
                                        <h6 class="fw-bold text-slate-900 mb-0 fs-14">1. Personal &amp; Contact Information</h6>
                                    </div>
                                </div>
                                <div class="card-body p-3.5 p-md-4">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label fs-13 fw-bold text-slate-800">First Name <span class="text-danger">*</span></label>
                                            <div class="position-relative">
                                                <i class="las la-user position-absolute top-50 translate-middle-y text-muted fs-18 ms-3" style="pointer-events: none;"></i>
                                                <input type="text" class="form-control ps-5" name="firstname" value="<?php echo htmlspecialchars($clientProfile['firstname']); ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fs-13 fw-bold text-slate-800">Last Name <span class="text-danger">*</span></label>
                                            <div class="position-relative">
                                                <i class="las la-user position-absolute top-50 translate-middle-y text-muted fs-18 ms-3" style="pointer-events: none;"></i>
                                                <input type="text" class="form-control ps-5" name="lastname" value="<?php echo htmlspecialchars($clientProfile['lastname']); ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fs-13 fw-bold text-slate-800">Email Address</label>
                                            <div class="position-relative">
                                                <i class="las la-envelope position-absolute top-50 translate-middle-y text-muted fs-18 ms-3" style="pointer-events: none;"></i>
                                                <input type="email" class="form-control ps-5 bg-light" name="email_display" value="<?php echo htmlspecialchars($clientProfile['email']); ?>" readonly>
                                            </div>
                                            <small class="text-muted fs-11 mt-1 d-block"><i class="las la-shield-alt text-success me-1"></i>Primary CloudFlox ID. Contact billing support to change email.</small>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fs-13 fw-bold text-slate-800">Phone Number <span class="text-danger">*</span></label>
                                            <div class="position-relative">
                                                <i class="las la-phone position-absolute top-50 translate-middle-y text-muted fs-18 ms-3" style="pointer-events: none;"></i>
                                                <input type="tel" class="form-control ps-5" name="phonenumber" value="<?php echo htmlspecialchars($clientProfile['phonenumber']); ?>" placeholder="+94 77 123 4567" required>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label fs-13 fw-bold text-slate-800">Company / Organization Name <span class="text-muted fw-normal fs-11">(Optional)</span></label>
                                            <div class="position-relative">
                                                <i class="las la-building position-absolute top-50 translate-middle-y text-muted fs-18 ms-3" style="pointer-events: none;"></i>
                                                <input type="text" class="form-control ps-5" name="companyname" value="<?php echo htmlspecialchars($clientProfile['companyname']); ?>" placeholder="Company or Organization Name">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- BLOCK 2: BILLING & PHYSICAL ADDRESS -->
                            <div class="card border mb-4 shadow-none rounded-3 overflow-hidden">
                                <div class="card-header bg-light py-3 px-3.5 border-bottom">
                                    <div class="d-flex align-items-center gap-2">
                                        <div style="width: 28px; height: 28px; border-radius: 50%; background: #EFF6FF; color: #2563EB; display: flex; align-items: center; justify-content: center; font-size: 14px;">
                                            <i class="las la-map-marker-alt"></i>
                                        </div>
                                        <h6 class="fw-bold text-slate-900 mb-0 fs-14">2. Billing &amp; Physical Address</h6>
                                    </div>
                                </div>
                                <div class="card-body p-3.5 p-md-4">
                                    <div class="row g-3">
                                        <div class="col-md-8">
                                            <label class="form-label fs-13 fw-bold text-slate-800">Street Address Line 1 <span class="text-danger">*</span></label>
                                            <div class="position-relative">
                                                <i class="las la-home position-absolute top-50 translate-middle-y text-muted fs-18 ms-3" style="pointer-events: none;"></i>
                                                <input type="text" class="form-control ps-5" name="address1" value="<?php echo htmlspecialchars($clientProfile['address1']); ?>" placeholder="123 Main Street" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fs-13 fw-bold text-slate-800">Street Address Line 2 <span class="text-muted fw-normal fs-11">(Optional)</span></label>
                                            <input type="text" class="form-control" name="address2" value="<?php echo htmlspecialchars($clientProfile['address2']); ?>" placeholder="Apt, Suite, Unit">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fs-13 fw-bold text-slate-800">City <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="city" value="<?php echo htmlspecialchars($clientProfile['city']); ?>" placeholder="City" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fs-13 fw-bold text-slate-800">State / Region</label>
                                            <input type="text" class="form-control" name="state" value="<?php echo htmlspecialchars($clientProfile['state']); ?>" placeholder="Western Province">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fs-13 fw-bold text-slate-800">Postcode / ZIP</label>
                                            <input type="text" class="form-control" name="postcode" value="<?php echo htmlspecialchars($clientProfile['postcode']); ?>" placeholder="00100">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label fs-13 fw-bold text-slate-800">Country <span class="text-danger">*</span></label>
                                            <select class="form-select" name="country" required>
                                                <?php foreach ($supportedCountries as $code => $cName): ?>
                                                    <option value="<?php echo $code; ?>" <?php echo (strtoupper($clientProfile['country']) === $code) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($cName); ?> (<?php echo $code; ?>)
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- BLOCK 3: CHANGE PASSWORD (OPTIONAL) -->
                            <div class="card border mb-4 shadow-none rounded-3 overflow-hidden">
                                <div class="card-header bg-light py-3 px-3.5 border-bottom">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center gap-2">
                                            <div style="width: 28px; height: 28px; border-radius: 50%; background: #EFF6FF; color: #2563EB; display: flex; align-items: center; justify-content: center; font-size: 14px;">
                                                <i class="las la-lock"></i>
                                            </div>
                                            <h6 class="fw-bold text-slate-900 mb-0 fs-14">3. Security &amp; Password Update</h6>
                                        </div>
                                        <span class="badge bg-light text-muted border fs-11">Optional</span>
                                    </div>
                                </div>
                                <div class="card-body p-3.5 p-md-4">
                                    <p class="text-secondary fs-12 mb-3">Leave both password fields blank if you do not want to change your current account password.</p>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label fs-13 fw-bold text-slate-800">New Password</label>
                                            <div class="position-relative">
                                                <i class="las la-key position-absolute top-50 translate-middle-y text-muted fs-18 ms-3" style="pointer-events: none;"></i>
                                                <input type="password" class="form-control ps-5 pe-5" id="profileNewPass" name="new_password" placeholder="Min. 6 characters">
                                                <button type="button" class="btn btn-link position-absolute end-0 top-50 translate-middle-y text-secondary text-decoration-none pe-2.5" onclick="togglePassVisibility('profileNewPass', 'eyeNewPass')">
                                                    <i class="las la-eye fs-18" id="eyeNewPass"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fs-13 fw-bold text-slate-800">Confirm New Password</label>
                                            <div class="position-relative">
                                                <i class="las la-check-double position-absolute top-50 translate-middle-y text-muted fs-18 ms-3" style="pointer-events: none;"></i>
                                                <input type="password" class="form-control ps-5 pe-5" id="profileConfirmPass" name="confirm_password" placeholder="Re-enter new password">
                                                <button type="button" class="btn btn-link position-absolute end-0 top-50 translate-middle-y text-secondary text-decoration-none pe-2.5" onclick="togglePassVisibility('profileConfirmPass', 'eyeConfirmPass')">
                                                    <i class="las la-eye fs-18" id="eyeConfirmPass"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- ACTION BAR -->
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 pt-2">
                                <span class="text-secondary fs-12">
                                    <i class="las la-shield-alt text-success me-1"></i> Changes take effect immediately across all active services and future invoices.
                                </span>
                                <button type="submit" class="btn btn-primary rounded-pill px-4 py-2.5 fw-bold shadow-sm">
                                    <i class="las la-save me-1.5"></i> Save Profile Changes
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>

            <!-- Direct Support Help Banner (Spacious & Clean) -->
            <div class="cf-dashboard-support-card d-flex flex-column flex-md-row align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="cf-stat-icon" style="background: #EFF6FF; color: #2563EB;">
                        <i class="las la-headset fs-28"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-slate-900 mb-1">Need help managing your cloud infrastructure?</h6>
                        <p class="fs-13 text-secondary mb-0">Our 24/7 technical team is available to assist you with server setups, DNS configurations, and billing inquiries.</p>
                    </div>
                </div>
                <a href="<?php echo $url; ?>contact-us" class="btn btn-outline-primary rounded-pill px-4 py-2 fw-bold flex-shrink-0 text-decoration-none">
                    Contact Support &rarr;
                </a>
            </div>

        </div>
    </main>

    <!-- =========== Footer Area =========== -->
    <?php include 'include/footer.inc.php'; ?>
    <!-- =========== Footer Area =========== -->

    <!-- =========== Javascript Area =========== -->
    <?php include 'include/js.inc.php'; ?>
    <!-- =========== Javascript Area =========== -->

    <script>
    function togglePassVisibility(inputId, eyeId) {
        const input = document.getElementById(inputId);
        const eye = document.getElementById(eyeId);
        if (!input || !eye) return;
        if (input.type === 'password') {
            input.type = 'text';
            eye.classList.remove('la-eye');
            eye.classList.add('la-eye-slash');
        } else {
            input.type = 'password';
            eye.classList.remove('la-eye-slash');
            eye.classList.add('la-eye');
        }
    }
    </script>

</body>
</html>
