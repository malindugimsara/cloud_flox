<?php
if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    @session_start();
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

$activeTab = strtolower(trim($_GET['tab'] ?? ''));
if (empty($activeTab)) {
    $activeTab = (!empty($profileSuccess) || !empty($profileError)) ? 'profile' : 'services';
}
$isProfileActive = ($activeTab === 'profile');
$isTicketsActive = ($activeTab === 'tickets' || $activeTab === 'ticket');
$isInvoicesActive = ($activeTab === 'invoices' || $activeTab === 'billing');
$isDomainsActive = ($activeTab === 'domains');
$isServicesActive = ($activeTab === 'services' || (!$isProfileActive && !$isTicketsActive && !$isInvoicesActive && !$isDomainsActive));

$clientInitials = 'CF';
if (!empty($clientName)) {
    $parts = preg_split('/\s+/', trim($clientName));
    if (count($parts) >= 2) {
        $clientInitials = strtoupper(mb_substr($parts[0], 0, 1) . mb_substr($parts[1], 0, 1));
    } else {
        $clientInitials = strtoupper(mb_substr($parts[0], 0, 2));
    }
}

// Format status badge helper
if (!function_exists('getStatusBadge')) {
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

    <style>
    /* =========================================================================
       CLOUDFLOX CLIENT PORTAL LEFT SIDEBAR NAVIGATION
       ========================================================================= */
    .cf-portal-sidebar {
        background: #FFFFFF;
        border: 1.5px solid #E2E8F0;
        border-radius: 20px;
        padding: 24px 20px;
        box-shadow: 0 4px 24px -4px rgba(15, 23, 42, 0.04);
        position: sticky;
        top: 90px;
        z-index: 10;
    }

    .cf-sidebar-profile {
        display: flex;
        align-items: center;
        gap: 13px;
        padding-bottom: 20px;
        margin-bottom: 20px;
        border-bottom: 1.5px solid #F1F5F9;
    }

    .cf-sidebar-avatar {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        background: linear-gradient(135deg, #2563EB 0%, #06B6D4 100%);
        color: #FFFFFF;
        font-weight: 800;
        font-size: 17px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
        flex-shrink: 0;
        letter-spacing: -0.5px;
    }

    .cf-sidebar-user-name {
        font-size: 15px;
        font-weight: 700;
        color: #0F172A;
        line-height: 1.25;
        margin-bottom: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 175px;
    }

    .cf-sidebar-user-email {
        font-size: 12px;
        color: #64748B;
        margin-bottom: 6px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 175px;
    }

    .cf-sidebar-nav-title {
        font-size: 10.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #94A3B8;
        margin-bottom: 10px;
        padding-left: 8px;
    }

    .cf-portal-nav .nav-link {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 11px 14px;
        border-radius: 12px;
        font-size: 13.5px;
        font-weight: 600;
        color: #475569;
        text-decoration: none;
        border: none;
        background: transparent;
        width: 100%;
        transition: all 0.2s ease;
        margin-bottom: 5px;
        text-align: left;
    }

    .cf-portal-nav .nav-link:hover {
        background: #F8FAFC;
        color: #2563EB;
        transform: translateX(3px);
    }

    .cf-portal-nav .nav-link:hover .cf-nav-icon {
        color: #2563EB;
    }

    .cf-portal-nav .nav-link.active {
        background: linear-gradient(135deg, #EFF6FF 0%, #E0E7FF 100%);
        color: #1D4ED8 !important;
        font-weight: 700;
        box-shadow: 0 2px 10px rgba(37, 99, 235, 0.08);
    }

    .cf-portal-nav .nav-link.active .cf-nav-icon {
        color: #2563EB;
    }

    .cf-nav-icon {
        font-size: 20px;
        width: 22px;
        text-align: center;
        color: #64748B;
        transition: color 0.2s ease;
        flex-shrink: 0;
    }

    .cf-nav-badge {
        font-size: 11px;
        font-weight: 700;
        background: #F1F5F9;
        color: #475569;
        padding: 3px 8px;
        border-radius: 999px;
    }

    .cf-portal-nav .nav-link.active .cf-nav-badge {
        background: #2563EB;
        color: #FFFFFF;
    }

    .cf-sidebar-divider {
        height: 1px;
        background: #F1F5F9;
        margin: 18px 0;
    }

    .cf-sidebar-help-box {
        background: #F8FAFC;
        border: 1px solid #E2E8F0;
        border-radius: 14px;
        padding: 14px;
        margin-bottom: 16px;
    }

    .cf-sidebar-logout-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 11px 14px;
        border-radius: 12px;
        font-size: 13.5px;
        font-weight: 600;
        color: #DC2626;
        background: #FEF2F2;
        text-decoration: none;
        transition: all 0.2s ease;
        border: 1px solid #FEE2E2;
        width: 100%;
    }

    .cf-sidebar-logout-btn:hover {
        background: #DC2626;
        color: #FFFFFF;
        border-color: #DC2626;
        box-shadow: 0 4px 14px rgba(220, 38, 38, 0.25);
        transform: translateY(-1px);
    }

    @media (max-width: 991.98px) {
        .cf-portal-sidebar {
            position: static;
            margin-bottom: 20px;
            padding: 16px 14px;
        }
        .cf-sidebar-profile {
            margin-bottom: 14px;
            padding-bottom: 14px;
        }
        .cf-portal-nav {
            flex-direction: row !important;
            overflow-x: auto;
            flex-wrap: nowrap !important;
            gap: 6px;
            padding-bottom: 6px;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }
        .cf-portal-nav::-webkit-scrollbar {
            display: none;
        }
        .cf-portal-nav .nav-link {
            white-space: nowrap;
            width: auto;
            padding: 8px 14px;
            margin-bottom: 0;
            flex-shrink: 0;
        }
        .cf-portal-nav .nav-link:hover {
            transform: none;
        }
        .cf-sidebar-help-box {
            display: none;
        }
    }

    @media (max-width: 575.98px) {
        .cf-profile-hero-card {
            padding: 16px 14px;
            flex-direction: column;
            align-items: flex-start;
        }
        .cf-profile-avatar-lg {
            width: 48px;
            height: 48px;
            font-size: 19px;
            border-radius: 12px;
        }
    }

    /* =========================================================================
       CLOUDFLOX PROFILE & ACCOUNT SETTINGS STYLING
       ========================================================================= */
    .cf-profile-hero-card {
        background: linear-gradient(135deg, #F8FAFD 0%, #EFF6FF 100%);
        border: 1.5px solid #DBEAFE;
        border-radius: 18px;
        padding: 22px 24px;
        margin-bottom: 26px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 16px;
    }

    .cf-profile-avatar-lg {
        width: 58px;
        height: 58px;
        border-radius: 16px;
        background: linear-gradient(135deg, #2563EB 0%, #06B6D4 100%);
        color: #FFFFFF;
        font-weight: 800;
        font-size: 22px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 14px rgba(37, 99, 235, 0.28);
        flex-shrink: 0;
        letter-spacing: -0.5px;
    }

    .cf-profile-section-card {
        background: #FFFFFF;
        border: 1.5px solid #E2E8F0;
        border-radius: 18px;
        overflow: hidden;
        margin-bottom: 24px;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .cf-profile-section-card:hover {
        border-color: #CBD5E1;
        box-shadow: 0 4px 18px -4px rgba(15, 23, 42, 0.04);
    }

    .cf-profile-section-header {
        background: #F8FAFC;
        border-bottom: 1.5px solid #E2E8F0;
        padding: 16px 22px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
    }

    .cf-profile-section-body {
        padding: 24px 22px;
    }

    .cf-form-label-modern {
        font-size: 13px;
        font-weight: 700;
        color: #1E293B;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .cf-input-icon-wrap {
        position: relative;
        display: flex;
        align-items: center;
    }

    .cf-input-icon-wrap .cf-input-icon {
        position: absolute;
        left: 14px;
        font-size: 18px;
        color: #64748B;
        pointer-events: none;
        z-index: 2;
        transition: color 0.2s ease;
    }

    .cf-input-icon-wrap .form-control,
    .cf-input-icon-wrap .form-select {
        padding-left: 42px !important;
    }

    .cf-input-modern {
        height: 48px !important;
        border-radius: 12px !important;
        border: 1.5px solid #E2E8F0 !important;
        font-size: 13.5px !important;
        color: #0F172A !important;
        background-color: #FFFFFF;
        transition: border-color 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
    }

    .cf-input-modern:focus {
        border-color: #2563EB !important;
        background-color: #FFFFFF !important;
        box-shadow: 0 0 0 3.5px rgba(37, 99, 235, 0.12) !important;
        outline: none !important;
    }

    .cf-input-icon-wrap:focus-within .cf-input-icon {
        color: #2563EB !important;
    }

    .cf-input-modern[readonly] {
        background-color: #F8FAFC !important;
        border-color: #E2E8F0 !important;
        color: #475569 !important;
        cursor: not-allowed;
    }

    .cf-pass-toggle-btn {
        position: absolute;
        right: 10px;
        background: transparent;
        border: none;
        color: #64748B;
        padding: 6px 8px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 18px;
        z-index: 3;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: color 0.2s ease;
    }

    .cf-pass-toggle-btn:hover {
        color: #2563EB;
    }

    .cf-profile-save-bar {
        background: #F8FAFC;
        border: 1.5px solid #E2E8F0;
        border-radius: 16px;
        padding: 18px 22px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 14px;
    }
    </style>
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
                        <span class="text-white-20 d-none d-sm-inline">&bull;</span>
                        <a href="#profile-pane" onclick="document.getElementById('profile-tab').click(); document.getElementById('clientDashboardTabs').scrollIntoView({behavior: 'smooth'}); return false;" class="cf-hero-meta-badge text-white text-decoration-none" style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.28); cursor: pointer;" title="Update your personal details &amp; password">
                            <i class="las la-user-edit" style="color: #67E8F9;"></i> Edit Profile
                        </a>
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

            <!-- =========================================================================
                 3. TWO-COLUMN DASHBOARD LAYOUT (Left Nav Bar + Right Content Area)
                 ========================================================================= -->
            <div class="row g-4 align-items-start">

                <!-- =========================================================
                     LEFT SIDEBAR NAVIGATION BAR
                     ========================================================= -->
                <div class="col-lg-3 col-xl-3">
                    <aside class="cf-portal-sidebar">
                        <!-- User Mini Profile Header -->
                        <div class="cf-sidebar-profile">
                            <div class="cf-sidebar-avatar">
                                <?php echo htmlspecialchars($clientInitials); ?>
                            </div>
                            <div class="overflow-hidden">
                                <div class="cf-sidebar-user-name" title="<?php echo htmlspecialchars($clientName); ?>">
                                    <?php echo htmlspecialchars($clientName); ?>
                                </div>
                                <div class="cf-sidebar-user-email" title="<?php echo htmlspecialchars($clientEmail); ?>">
                                    <?php echo htmlspecialchars($clientEmail); ?>
                                </div>
                                <span class="badge bg-emerald-subtle text-emerald border border-emerald-subtle rounded-pill fs-11 px-2 py-0.5">
                                    <i class="las la-shield-alt me-0.5"></i> Client ID #<?php echo $clientId; ?>
                                </span>
                            </div>
                        </div>

                        <!-- Sidebar Navigation Menu -->
                        <div class="cf-sidebar-nav-title">Portal Navigation</div>

                        <div class="nav flex-column nav-pills cf-portal-nav" id="clientDashboardTabs" role="tablist" aria-orientation="vertical">
                            <!-- Services Tab -->
                            <button class="nav-link <?php echo $isServicesActive ? 'active' : ''; ?>" id="services-tab" data-bs-toggle="pill" data-bs-target="#services-pane" type="button" role="tab" aria-controls="services-pane" aria-selected="<?php echo $isServicesActive ? 'true' : 'false'; ?>">
                                <i class="las la-server cf-nav-icon"></i>
                                <span class="cf-nav-label">My Services</span>
                                <span class="badge cf-nav-badge ms-auto"><?php echo count($services); ?></span>
                            </button>

                            <!-- Domains Tab -->
                            <button class="nav-link <?php echo $isDomainsActive ? 'active' : ''; ?>" id="domains-tab" data-bs-toggle="pill" data-bs-target="#domains-pane" type="button" role="tab" aria-controls="domains-pane" aria-selected="<?php echo $isDomainsActive ? 'true' : 'false'; ?>">
                                <i class="las la-globe cf-nav-icon"></i>
                                <span class="cf-nav-label">My Domains</span>
                                <span class="badge cf-nav-badge ms-auto"><?php echo count($domains); ?></span>
                            </button>

                            <!-- Invoices Tab -->
                            <button class="nav-link <?php echo $isInvoicesActive ? 'active' : ''; ?>" id="invoices-tab" data-bs-toggle="pill" data-bs-target="#invoices-pane" type="button" role="tab" aria-controls="invoices-pane" aria-selected="<?php echo $isInvoicesActive ? 'true' : 'false'; ?>">
                                <i class="las la-file-invoice-dollar cf-nav-icon"></i>
                                <span class="cf-nav-label">Invoices &amp; Billing</span>
                                <?php if ($statInvoicesUnpaid > 0): ?>
                                    <span class="badge bg-danger text-white rounded-pill ms-auto"><?php echo $statInvoicesUnpaid; ?></span>
                                <?php else: ?>
                                    <span class="badge cf-nav-badge ms-auto"><?php echo count($invoices); ?></span>
                                <?php endif; ?>
                            </button>

                            <!-- Support Tickets Tab -->
                            <button class="nav-link <?php echo $isTicketsActive ? 'active' : ''; ?>" id="tickets-tab" data-bs-toggle="pill" data-bs-target="#tickets-pane" type="button" role="tab" aria-controls="tickets-pane" aria-selected="<?php echo $isTicketsActive ? 'true' : 'false'; ?>">
                                <i class="las la-headset cf-nav-icon"></i>
                                <span class="cf-nav-label">Support Tickets</span>
                                <span class="badge cf-nav-badge ms-auto"><?php echo count($tickets); ?></span>
                            </button>

                            <!-- Profile Details Tab -->
                            <button class="nav-link <?php echo $isProfileActive ? 'active' : ''; ?>" id="profile-tab" data-bs-toggle="pill" data-bs-target="#profile-pane" type="button" role="tab" aria-controls="profile-pane" aria-selected="<?php echo $isProfileActive ? 'true' : 'false'; ?>">
                                <i class="las la-user-cog cf-nav-icon"></i>
                                <span class="cf-nav-label">Profile Details</span>
                            </button>
                        </div>

                        <div class="cf-sidebar-divider"></div>

                        <!-- 24/7 Help Box -->
                        <div class="cf-sidebar-help-box">
                            <div class="d-flex align-items-center gap-2 mb-1.5">
                                <div style="width: 26px; height: 26px; border-radius: 8px; background: #EFF6FF; color: #2563EB; display: flex; align-items: center; justify-content: center; font-size: 14px;">
                                    <i class="las la-life-ring"></i>
                                </div>
                                <span class="fs-12 fw-bold text-slate-900">Need Live Help?</span>
                            </div>
                            <p class="fs-11 text-secondary mb-2.5 lh-sm">Submit inquiries or chat directly with cloud engineers.</p>
                            <a href="<?php echo $url; ?>ticket?action=new" class="btn btn-sm btn-outline-primary rounded-pill w-100 fw-bold fs-11.5 py-1.5">
                                <i class="las la-plus-circle me-1"></i> Open Ticket
                            </a>
                        </div>

                        <!-- Sign Out Button -->
                        <a href="<?php echo $url; ?>login?action=logout" class="cf-sidebar-logout-btn" title="Sign out of your account">
                            <i class="las la-sign-out-alt fs-18"></i>
                            <span>Sign Out</span>
                        </a>
                    </aside>
                </div>

                <!-- =========================================================
                     RIGHT MAIN CONTENT (9 Cols)
                     ========================================================= -->
                <div class="col-lg-9 col-xl-9">
                    <div class="cf-dashboard-card p-4 p-md-4 mb-0">
                        <div class="tab-content" id="clientDashboardContent">

                            <!-- 1. Services Tab -->
                            <div class="tab-pane fade <?php echo $isServicesActive ? 'show active' : ''; ?>" id="services-pane" role="tabpanel">
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
                    <div class="tab-pane fade <?php echo $isDomainsActive ? 'show active' : ''; ?>" id="domains-pane" role="tabpanel">
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
                    <div class="tab-pane fade <?php echo $isInvoicesActive ? 'show active' : ''; ?>" id="invoices-pane" role="tabpanel">
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
                    <div class="tab-pane fade <?php echo $isTicketsActive ? 'show active' : ''; ?>" id="tickets-pane" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                            <h6 class="fw-bold text-slate-900 mb-0">Recent Support Inquiries</h6>
                            <div class="d-flex align-items-center gap-2">
                                <a href="<?php echo $url; ?>ticket" class="btn btn-light border btn-sm rounded-pill px-3 fw-semibold fs-12">
                                    <i class="las la-list me-1"></i> Ticket Desk
                                </a>
                                <a href="<?php echo $url; ?>ticket?action=new" class="btn btn-primary btn-sm rounded-pill px-3 fw-bold fs-12 shadow-sm">
                                    <i class="las la-plus-circle me-1"></i> Open Ticket
                                </a>
                            </div>
                        </div>
                        <?php if (empty($tickets)): ?>
                            <div class="text-center py-5">
                                <div class="fs-40 text-muted mb-2"><i class="las la-headset"></i></div>
                                <h5 class="fw-bold text-slate-800">No Support Tickets</h5>
                                <p class="text-secondary fs-13 mb-3">Everything is running smoothly! You don't have any support tickets.</p>
                                <a href="<?php echo $url; ?>ticket?action=new" class="btn btn-primary rounded-pill btn-sm px-4 fw-bold">
                                    <i class="las la-plus-circle me-1"></i> Open a Support Ticket
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
                                            <th class="text-end">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($tickets as $tkt): ?>
                                            <tr>
                                                <td>
                                                    <a href="<?php echo $url; ?>ticket?id=<?php echo $tkt['id']; ?>" class="fw-bold text-primary text-decoration-none">
                                                        #<?php echo htmlspecialchars($tkt['tid'] ?: $tkt['id']); ?>
                                                    </a>
                                                </td>
                                                <td>
                                                    <a href="<?php echo $url; ?>ticket?id=<?php echo $tkt['id']; ?>" class="fw-semibold text-slate-900 text-decoration-none hover-primary">
                                                        <?php echo htmlspecialchars($tkt['title']); ?>
                                                    </a>
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
                                                <td class="text-end">
                                                    <a href="<?php echo $url; ?>ticket?id=<?php echo $tkt['id']; ?>" class="btn btn-light btn-sm rounded-pill px-3 py-1 fs-12 fw-semibold text-primary border">
                                                        <i class="las la-comments me-1"></i> View &amp; Reply
                                                    </a>
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

                        <!-- Top Profile Overview Banner -->
                        <div class="cf-profile-hero-card">
                            <div class="d-flex align-items-center gap-3">
                                <div class="cf-profile-avatar-lg">
                                    <?php echo htmlspecialchars($clientInitials); ?>
                                </div>
                                <div>
                                    <h4 class="fw-bold text-slate-900 mb-1 fs-18">
                                        <?php echo htmlspecialchars($clientName); ?>
                                    </h4>
                                    <div class="d-flex align-items-center flex-wrap gap-2 text-secondary fs-12.5">
                                        <span><i class="las la-envelope text-primary me-1"></i><?php echo htmlspecialchars($clientProfile['email']); ?></span>
                                        <span>&bull;</span>
                                        <span><i class="las la-id-badge text-primary me-1"></i>Client ID #<?php echo $clientId; ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <span class="badge bg-emerald-subtle text-emerald border border-emerald-subtle rounded-pill px-3 py-1.5 fs-12 fw-bold">
                                    <i class="las la-check-circle me-1"></i> Verified Account
                                </span>
                                <span class="badge bg-white text-slate-700 border rounded-pill px-3 py-1.5 fs-12 fw-semibold">
                                    <i class="las la-shield-alt text-primary me-1"></i><span style="color: #000000;"> 256-bit Encrypted</span>
                                </span>
                            </div>
                        </div>

                        <form method="POST" action="<?php echo $url; ?>clientarea.php?tab=profile" class="needs-validation">
                            <input type="hidden" name="action" value="update_profile">

                            <!-- BLOCK 1: PERSONAL & CONTACT INFORMATION -->
                            <div class="cf-profile-section-card">
                                <div class="cf-profile-section-header">
                                    <div class="d-flex align-items-center gap-2.5">
                                        <div style="width: 32px; height: 32px; border-radius: 10px; background: #EFF6FF; color: #2563EB; display: flex; align-items: center; justify-content: center; font-size: 16px;">
                                            <i class="las la-user"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold text-slate-900 mb-0 fs-14.5">1. Personal &amp; Contact Information</h6>
                                            <small class="text-secondary fs-11.5">Your official account identity and primary contact channels.</small>
                                        </div>
                                    </div>
                                    <span class="badge bg-light text-secondary border rounded-pill fs-11 px-2.5 py-1">Required Fields <span style="color: #DC2626;">*</span></span>
                                </div>
                                <div class="cf-profile-section-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="cf-form-label-modern">First Name <span class="text-danger">*</span></label>
                                            <div class="cf-input-icon-wrap">
                                                <i class="las la-user cf-input-icon"></i>
                                                <input type="text" class="form-control cf-input-modern" name="firstname" value="<?php echo htmlspecialchars($clientProfile['firstname']); ?>" placeholder="First Name" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="cf-form-label-modern">Last Name <span class="text-danger">*</span></label>
                                            <div class="cf-input-icon-wrap">
                                                <i class="las la-user cf-input-icon"></i>
                                                <input type="text" class="form-control cf-input-modern" name="lastname" value="<?php echo htmlspecialchars($clientProfile['lastname']); ?>" placeholder="Last Name" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="cf-form-label-modern">Email Address <span class="badge bg-light text-muted border fs-10 ms-1">Login ID</span></label>
                                            <div class="cf-input-icon-wrap">
                                                <i class="las la-envelope cf-input-icon"></i>
                                                <input type="email" class="form-control cf-input-modern" name="email_display" value="<?php echo htmlspecialchars($clientProfile['email']); ?>" readonly title="Primary CloudFlox ID">
                                            </div>
                                            <small class="text-muted fs-11 mt-1.5 d-block"><i class="las la-shield-alt text-success me-1"></i>Primary ID. Contact billing support to change your account email.</small>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="cf-form-label-modern">Phone Number <span class="text-danger">*</span></label>
                                            <div class="cf-input-icon-wrap">
                                                <i class="las la-phone cf-input-icon"></i>
                                                <input type="tel" class="form-control cf-input-modern" name="phonenumber" value="<?php echo htmlspecialchars($clientProfile['phonenumber']); ?>" placeholder="+94 77 123 4567" required>
                                            </div>
                                            <small class="text-muted fs-11 mt-1.5 d-block">Used for critical server alert SMS and billing verification.</small>
                                        </div>
                                        <div class="col-12">
                                            <label class="cf-form-label-modern">Company / Organization Name <span class="text-muted fw-normal fs-11">(Optional)</span></label>
                                            <div class="cf-input-icon-wrap">
                                                <i class="las la-building cf-input-icon"></i>
                                                <input type="text" class="form-control cf-input-modern" name="companyname" value="<?php echo htmlspecialchars($clientProfile['companyname']); ?>" placeholder="Organization or Business Name (Leave blank if personal)">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- BLOCK 2: BILLING & PHYSICAL ADDRESS -->
                            <div class="cf-profile-section-card">
                                <div class="cf-profile-section-header">
                                    <div class="d-flex align-items-center gap-2.5">
                                        <div style="width: 32px; height: 32px; border-radius: 10px; background: #ECFDF5; color: #059669; display: flex; align-items: center; justify-content: center; font-size: 16px;">
                                            <i class="las la-map-marker-alt"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold text-slate-900 mb-0 fs-14.5">2. Billing &amp; Physical Address</h6>
                                            <small class="text-secondary fs-11.5">Official address used for tax invoices and ICANN domain registry.</small>
                                        </div>
                                    </div>
                                    <span class="badge bg-light text-secondary border rounded-pill fs-11 px-2.5 py-1">Invoicing Details</span>
                                </div>
                                <div class="cf-profile-section-body">
                                    <div class="row g-3">
                                        <div class="col-md-8">
                                            <label class="cf-form-label-modern">Street Address Line 1 <span class="text-danger">*</span></label>
                                            <div class="cf-input-icon-wrap">
                                                <i class="las la-map-pin cf-input-icon"></i>
                                                <input type="text" class="form-control cf-input-modern" name="address1" value="<?php echo htmlspecialchars($clientProfile['address1']); ?>" placeholder="123 Main Street" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="cf-form-label-modern">Street Address Line 2 <span class="text-muted fw-normal fs-11">(Optional)</span></label>
                                            <div class="cf-input-icon-wrap">
                                                <i class="las la-door-open cf-input-icon"></i>
                                                <input type="text" class="form-control cf-input-modern" name="address2" value="<?php echo htmlspecialchars($clientProfile['address2']); ?>" placeholder="Apt, Suite, Unit">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="cf-form-label-modern">City / Town <span class="text-danger">*</span></label>
                                            <div class="cf-input-icon-wrap">
                                                <i class="las la-city cf-input-icon"></i>
                                                <input type="text" class="form-control cf-input-modern" name="city" value="<?php echo htmlspecialchars($clientProfile['city']); ?>" placeholder="City" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="cf-form-label-modern">State / Province / Region</label>
                                            <div class="cf-input-icon-wrap">
                                                <i class="las la-map cf-input-icon"></i>
                                                <input type="text" class="form-control cf-input-modern" name="state" value="<?php echo htmlspecialchars($clientProfile['state']); ?>" placeholder="Western Province">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="cf-form-label-modern">Postal / ZIP Code</label>
                                            <div class="cf-input-icon-wrap">
                                                <i class="las la-mail-bulk cf-input-icon"></i>
                                                <input type="text" class="form-control cf-input-modern" name="postcode" value="<?php echo htmlspecialchars($clientProfile['postcode']); ?>" placeholder="00100">
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <label class="cf-form-label-modern">Country <span class="text-danger">*</span></label>
                                            <div class="cf-input-icon-wrap">
                                                <i class="las la-globe-americas cf-input-icon"></i>
                                                <select class="form-select cf-input-modern" name="country" required>
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
                            </div>

                            <!-- BLOCK 3: CHANGE PASSWORD (OPTIONAL) -->
                            <div class="cf-profile-section-card">
                                <div class="cf-profile-section-header">
                                    <div class="d-flex align-items-center gap-2.5">
                                        <div style="width: 32px; height: 32px; border-radius: 10px; background: #FAF5FF; color: #9333EA; display: flex; align-items: center; justify-content: center; font-size: 16px;">
                                            <i class="las la-lock"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold text-slate-900 mb-0 fs-14.5">3. Security &amp; Password Update</h6>
                                            <small class="text-secondary fs-11.5">Leave blank if you wish to keep your existing account password.</small>
                                        </div>
                                    </div>
                                    <span class="badge bg-light text-muted border fs-11 px-2.5 py-1">Optional</span>
                                </div>
                                <div class="cf-profile-section-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="cf-form-label-modern">New Password</label>
                                            <div class="cf-input-icon-wrap">
                                                <i class="las la-key cf-input-icon"></i>
                                                <input type="password" class="form-control cf-input-modern pe-5" id="profileNewPass" name="new_password" placeholder="Min. 6 characters">
                                                <button type="button" class="cf-pass-toggle-btn" onclick="togglePassVisibility('profileNewPass', 'eyeNewPass')" title="Show / Hide Password">
                                                    <i class="las la-eye" id="eyeNewPass"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="cf-form-label-modern">Confirm New Password</label>
                                            <div class="cf-input-icon-wrap">
                                                <i class="las la-check-double cf-input-icon"></i>
                                                <input type="password" class="form-control cf-input-modern pe-5" id="profileConfirmPass" name="confirm_password" placeholder="Re-enter new password">
                                                <button type="button" class="cf-pass-toggle-btn" onclick="togglePassVisibility('profileConfirmPass', 'eyeConfirmPass')" title="Show / Hide Password">
                                                    <i class="las la-eye" id="eyeConfirmPass"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <small class="text-muted fs-12 mt-2.5 d-block"><i class="las la-info-circle text-primary me-1"></i>Password must be at least 6 characters. Use letters, numbers, and symbols for best security.</small>
                                </div>
                            </div>

                            <!-- ACTION BAR -->
                            <div class="cf-profile-save-bar">
                                <div class="d-flex align-items-center gap-2 text-secondary fs-12.5">
                                    <i class="las la-shield-alt text-success fs-20"></i>
                                    <span>Changes apply immediately across active cloud services and future invoices.</span>
                                </div>
                                <button type="submit" class="btn btn-primary rounded-pill px-4 py-2.5 fw-bold shadow-sm">
                                    <i class="las la-save me-1.5"></i> Save Profile Changes
                                </button>
                            </div>
                        </form>
                    </div>

                        </div>
                    </div>
                </div>
            </div>

            <!-- Direct Support Help Banner (Executive Glassmorphic Design) -->
            <div class="cf-dashboard-support-card mt-4 d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3.5">
                <div class="d-flex align-items-start align-items-sm-center gap-3">
                    <div class="cf-support-icon-badge">
                        <i class="las la-headset"></i>
                        <span class="cf-pulse-dot"></span>
                    </div>
                    <div>
                        <div class="d-inline-flex align-items-center gap-1.5 px-2.5 py-0.5 rounded-pill bg-blue-subtle text-primary fw-bold fs-11 mb-1 border border-blue-subtle" style="padding-left: 8px; padding-right: 8px;">
                            <i class="las la-shield-alt"></i> <span>24/7/365 NOC Engineering Assistance</span> 
                        </div>
                        <h5 class="fw-extrabold text-slate-900 mb-1 fs-16">Need help managing your cloud infrastructure?</h5>
                        <p class="fs-13 text-secondary mb-0 max-w-700">Our senior cloud engineers are on standby to assist you with DNS routing, SSL deployments, server configurations, and migrations.</p>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap flex-sm-nowrap flex-shrink-0">
                    <a href="<?php echo $url; ?>ticket" class="cf-btn-support-card-secondary" title="View all your active & past support tickets">
                        <i class="las la-ticket-alt text-primary fs-15"></i>
                        <span>All Tickets</span>
                    </a>
                    <a href="<?php echo $url; ?>ticket?action=new" class="cf-btn-support-card-primary" title="Open a new priority support ticket">
                        <i class="las la-plus-circle fs-16"></i>
                        <span>Open Support Ticket</span>
                        <i class="las la-arrow-right cf-hover-arrow"></i>
                    </a>
                </div>
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
