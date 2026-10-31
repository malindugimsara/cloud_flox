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
        $stmt = $mysqli->prepare("SELECT id, domain, status, registrationdate, nextduedate, expirydate, recurringamount, registrar, dnsmanagement, idprotection, donotrenew FROM tbldomains WHERE userid = ? ORDER BY id DESC");
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

        // 5. Fetch Server Quotes & Enterprise RFQs
        $quotes = [];
        $stmt = $mysqli->prepare("SELECT id, subject, stage, validuntil, subtotal, total, proposal, customernotes, adminnotes, datecreated, lastmodified, datesent, dateaccepted FROM tblquotes WHERE userid = ? ORDER BY id DESC LIMIT 30");
        if ($stmt) {
            $stmt->bind_param('i', $clientId);
            $stmt->execute();
            $quotes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
        }

        // 6. Fetch Client Profile Details
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
        $statQuotesPending = 0;
        $statQuotesDelivered = 0;
        foreach ($quotes as $q) {
            $st = strtolower($q['stage'] ?? '');
            if ($st === 'draft' || $st === 'on hold') $statQuotesPending++;
            if ($st === 'delivered') $statQuotesDelivered++;
        }
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
$isQuotesActive = ($activeTab === 'quotes' || $activeTab === 'quote' || $activeTab === 'rfq');
$isServicesActive = ($activeTab === 'services' || (!$isProfileActive && !$isTicketsActive && !$isInvoicesActive && !$isDomainsActive && !$isQuotesActive));

$clientInitials = 'CF';
if (!empty($clientName)) {
    $parts = preg_split('/\s+/', trim($clientName));
    if (count($parts) >= 2) {
        $clientInitials = strtoupper(mb_substr($parts[0], 0, 1) . mb_substr($parts[1], 0, 1));
    } else {
        $clientInitials = strtoupper(mb_substr($parts[0], 0, 2));
    }
}

// Format status badge helpers
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

if (!function_exists('getQuoteStageBadge')) {
    function getQuoteStageBadge($stage) {
        $st = strtolower(trim($stage));
        if ($st === 'delivered') {
            return '<span class="badge bg-emerald-subtle text-emerald border border-emerald-subtle rounded-pill px-2.5 py-1 fs-11.5 fw-bold"><i class="las la-bell me-1"></i>Delivered (Action Required)</span>';
        } elseif ($st === 'accepted') {
            return '<span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1 fs-11.5 fw-bold"><i class="las la-check-circle me-1"></i>Accepted &amp; Invoiced</span>';
        } elseif ($st === 'draft' || $st === 'on hold') {
            return '<span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2.5 py-1 fs-11.5 fw-bold"><i class="las la-clock me-1"></i>Under Engineering Review</span>';
        } else {
            return '<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-2.5 py-1 fs-11.5 fw-semibold"><i class="las la-times-circle me-1"></i>' . htmlspecialchars(ucfirst($stage)) . '</span>';
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

                            <!-- Server Quotes & RFQs Tab -->
                            <button class="nav-link <?php echo $isQuotesActive ? 'active' : ''; ?>" id="quotes-tab" data-bs-toggle="pill" data-bs-target="#quotes-pane" type="button" role="tab" aria-controls="quotes-pane" aria-selected="<?php echo $isQuotesActive ? 'true' : 'false'; ?>">
                                <i class="las la-file-signature cf-nav-icon"></i>
                                <span class="cf-nav-label">Server Quotes &amp; RFQs</span>
                                <?php if ($statQuotesDelivered > 0): ?>
                                    <span class="badge bg-success text-white rounded-pill ms-auto" title="<?php echo $statQuotesDelivered; ?> quote(s) ready to review & accept"><?php echo $statQuotesDelivered; ?> New</span>
                                <?php elseif (count($quotes) > 0): ?>
                                    <span class="badge cf-nav-badge ms-auto"><?php echo count($quotes); ?></span>
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
                                <table class="table cf-dash-table mb-0 align-middle">
                                    <thead>
                                        <tr>
                                            <th>Product / Service</th>
                                            <th>Assigned Domain</th>
                                            <th>Pricing / Cycle</th>
                                            <th>Next Due Date</th>
                                            <th>Status</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($services as $srv): ?>
                                            <tr>
                                                <td>
                                                    <div class="fw-bold text-slate-900 fs-14"><?php echo htmlspecialchars($srv['product_name'] ?? 'Cloud Hosting'); ?></div>
                                                    <span class="fs-11 text-muted font-monospace">Service ID #<?php echo $srv['id']; ?></span>
                                                </td>
                                                <td>
                                                    <?php if (!empty($srv['domain'])): ?>
                                                        <a href="http://<?php echo htmlspecialchars($srv['domain']); ?>" target="_blank" rel="noopener" class="fw-semibold text-primary text-decoration-none">
                                                            <?php echo htmlspecialchars($srv['domain']); ?>
                                                            <i class="las la-external-link-alt fs-11 ms-1"></i>
                                                        </a>
                                                    <?php else: ?>
                                                        <span class="text-muted fs-12">No domain attached</span>
                                                    <?php endif; ?>
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
                                                <td class="text-end">
                                                    <div class="d-inline-flex align-items-center gap-1.5 justify-content-end">
                                                        <?php if (strtolower($srv['domainstatus']) === 'active'): ?>
                                                            <a href="https://sv.cloudflox.com:2083" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 py-1 fs-11 fw-bold" title="Open cPanel Direct Access">
                                                                <i class="las la-external-link-alt me-0.5"></i> cPanel
                                                            </a>
                                                        <?php endif; ?>
                                                        <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold d-inline-flex align-items-center gap-1 shadow-xs" onclick="openHostingManageModal(<?php echo (int)$srv['id']; ?>, '<?php echo htmlspecialchars($srv['domain'] ?? '', ENT_QUOTES); ?>')">
                                                            <i class="las la-server fs-14"></i> Manage
                                                        </button>
                                                    </div>
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
                            <!-- ICANN Notice & Quick Action Banner -->
                            <div class="p-3 mb-3.5 rounded-3 d-flex align-items-center justify-content-between gap-3 flex-wrap border" style="background: #fbfdff; border-color: #e2e8f0 !important;">
                                <div class="d-flex align-items-center gap-2.5">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px; background: rgba(245, 158, 11, 0.12); color: #d97706;">
                                        <i class="las la-shield-alt fs-18"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-slate-900 fs-13 mb-0">Domain Security &amp; Nameserver Management</div>
                                        <div class="text-secondary fs-12">Point your domains to any server, toggle registrar transfer lock, manage WHOIS privacy, or view your transfer EPP codes below.</div>
                                    </div>
                                </div>
                                <a href="<?php echo $url; ?>domain" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-bold flex-shrink-0">
                                    <i class="las la-plus-circle me-1"></i> Register Another Domain
                                </a>
                            </div>

                            <div class="table-responsive">
                                <table class="table cf-dash-table mb-0 align-middle">
                                    <thead>
                                        <tr>
                                            <th>Domain Name</th>
                                            <th>Registration Date</th>
                                            <th>Next Due Date</th>
                                            <th>Auto-Renew</th>
                                            <th>Renewal Amount</th>
                                            <th>Status</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($domains as $dom): ?>
                                            <tr>
                                                <td>
                                                    <div class="fw-bold text-slate-900 fs-14"><?php echo htmlspecialchars($dom['domain']); ?></div>
                                                    <a href="http://<?php echo htmlspecialchars($dom['domain']); ?>" target="_blank" rel="noopener" class="fs-11 text-muted text-decoration-none">
                                                        Visit Website <i class="las la-external-link-alt fs-10"></i>
                                                    </a>
                                                </td>
                                                <td>
                                                    <span class="fs-13 text-secondary"><?php echo htmlspecialchars($dom['registrationdate']); ?></span>
                                                </td>
                                                <td>
                                                    <span class="fs-13 text-secondary"><?php echo htmlspecialchars($dom['nextduedate']); ?></span>
                                                </td>
                                                <td>
                                                    <?php if (empty($dom['donotrenew'])): ?>
                                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill fs-11 px-2.5 py-1">
                                                            <i class="las la-check-circle me-1"></i>Active
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill fs-11 px-2.5 py-1">
                                                            <i class="las la-times-circle me-1"></i>Disabled
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="fw-semibold text-slate-800">$<?php echo number_format((float)$dom['recurringamount'], 2); ?> USD</div>
                                                </td>
                                                <td>
                                                    <?php echo getStatusBadge($dom['status']); ?>
                                                </td>
                                                <td class="text-end">
                                                    <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold d-inline-flex align-items-center gap-1 shadow-xs" 
                                                            onclick="openDomainManageModal(<?php echo (int)$dom['id']; ?>, '<?php echo htmlspecialchars($dom['domain'], ENT_QUOTES); ?>')">
                                                        <i class="las la-sliders-h fs-14"></i> Manage
                                                    </button>
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

                    <!-- 3.5. Server Quotes & Enterprise RFQs Tab -->
                    <div class="tab-pane fade <?php echo $isQuotesActive ? 'show active' : ''; ?>" id="quotes-pane" role="tabpanel">
                        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
                            <div>
                                <h5 class="fw-bold text-slate-900 mb-1 fs-17 d-flex align-items-center gap-2">
                                    <i class="las la-file-signature text-primary fs-20"></i> Server Quotes &amp; Enterprise RFQs
                                </h5>
                                <p class="text-secondary fs-13 mb-0">Custom Bare Metal &amp; KVM VPS configurations reviewed and tailored by CloudFlox senior infrastructure engineers.</p>
                            </div>
                            <div class="flex-shrink-0">
                                <a href="<?php echo $url; ?>request-quote" class="btn btn-primary rounded-pill px-3.5 py-2 fs-12.5 fw-bold shadow-sm d-inline-flex align-items-center gap-1.5">
                                    <i class="las la-plus-circle fs-16"></i> Request New Server Quote
                                </a>
                            </div>
                        </div>

                        <?php if (empty($quotes)): ?>
                            <div class="text-center py-5 px-3">
                                <div style="width: 72px; height: 72px; border-radius: 20px; background: #EFF6FF; color: #2563EB; display: inline-flex; align-items: center; justify-content: center; font-size: 34px; margin-bottom: 16px;">
                                    <i class="las la-server"></i>
                                </div>
                                <h5 class="fw-bold text-slate-800 mb-2">No Server Quotations Found</h5>
                                <p class="text-secondary fs-13 mx-auto mb-4" style="max-width: 480px;">
                                    Need dedicated bare-metal hardware, high-bandwidth streaming pipes, or specialized Cloud VPS specs? Submit your technical requirements and our engineering team will tailor an official quotation for you.
                                </p>
                                <a href="<?php echo $url; ?>request-quote" class="btn btn-outline-primary rounded-pill px-4 py-2 fs-13 fw-bold">
                                    <i class="las la-sliders-h me-1.5"></i> Configure &amp; Request Server Quote
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table cf-dash-table mb-0 align-middle">
                                    <thead>
                                        <tr>
                                            <th>Quote #</th>
                                            <th>Server Specification / Subject</th>
                                            <th>Date Requested</th>
                                            <th>Valid Until</th>
                                            <th>Total Price</th>
                                            <th>Status</th>
                                            <th class="text-end">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($quotes as $q): 
                                            $qStage = strtolower($q['stage'] ?? 'draft');
                                            $isDelivered = ($qStage === 'delivered');
                                            $isAccepted = ($qStage === 'accepted');
                                            $isDraft = ($qStage === 'draft' || $qStage === 'on hold');
                                            $totalNum = (float)($q['total'] ?? 0);
                                        ?>
                                            <tr>
                                                <td>
                                                    <span class="fw-bold text-slate-900">#RFQ-<?php echo htmlspecialchars($q['id']); ?></span>
                                                </td>
                                                <td>
                                                    <div class="fw-bold text-slate-800 fs-13"><?php echo htmlspecialchars($q['subject'] ?: 'Custom Server Specification'); ?></div>
                                                    <div class="text-muted fs-11 mt-0.5"><i class="las la-shield-alt text-primary me-1"></i>Dedicated SLA &amp; Direct Datacenter Provisioning</div>
                                                </td>
                                                <td>
                                                    <span class="fs-12.5 text-secondary"><?php echo !empty($q['datecreated']) ? date('M d, Y', strtotime($q['datecreated'])) : '--'; ?></span>
                                                </td>
                                                <td>
                                                    <span class="fs-12.5 text-secondary"><?php echo !empty($q['validuntil']) ? date('M d, Y', strtotime($q['validuntil'])) : '30 Days'; ?></span>
                                                </td>
                                                <td>
                                                    <?php if ($totalNum > 0): ?>
                                                        <div class="fw-800 text-slate-900 fs-14">$<?php echo number_format($totalNum, 2); ?> <span class="fs-11 text-muted fw-normal">USD</span></div>
                                                    <?php else: ?>
                                                        <span class="badge bg-light text-secondary border rounded-pill fs-11">Under Pricing</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php echo getQuoteStageBadge($q['stage']); ?>
                                                </td>
                                                <td class="text-end">
                                                    <?php if ($isDelivered): ?>
                                                        <button type="button" class="btn btn-success btn-sm rounded-pill px-3 py-1 fs-12 fw-bold shadow-xs text-white" onclick="openQuoteModal(<?php echo (int)$q['id']; ?>)">
                                                            <i class="las la-check-circle me-1"></i> Review &amp; Accept
                                                        </button>
                                                    <?php elseif ($isAccepted): ?>
                                                        <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3 py-1 fs-12 fw-semibold" onclick="openQuoteModal(<?php echo (int)$q['id']; ?>)">
                                                            <i class="las la-eye me-1"></i> View Specs
                                                        </button>
                                                    <?php else: ?>
                                                        <button type="button" class="btn btn-light btn-sm rounded-pill px-3 py-1 fs-12 fw-semibold text-secondary border" onclick="openQuoteModal(<?php echo (int)$q['id']; ?>)">
                                                            <i class="las la-info-circle me-1"></i> Details
                                                        </button>
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

    <!-- =====================================================================
         DOMAIN MANAGEMENT MODAL
         ===================================================================== -->
    <div class="modal fade cf-dm-modal" id="domainManageModal" tabindex="-1" aria-labelledby="dmModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0">
                <div class="modal-header border-bottom py-3 px-4 bg-white align-items-center">
                    <div class="d-flex align-items-center gap-2.5">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: rgba(13, 110, 253, 0.1); color: #0d6efd;">
                            <i class="las la-globe fs-22"></i>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <h5 class="modal-title fw-bold text-slate-900 mb-0" id="dmModalTitle">Domain Management</h5>
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill fs-11" id="dmModalStatusBadge">Active</span>
                            </div>
                            <span class="fs-12 text-primary font-monospace fw-semibold" id="dmModalDomainName">example.com</span>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4 bg-white">
                    <!-- Loading Skeleton -->
                    <div id="dmLoadingState" class="text-center py-5">
                        <div class="spinner-border text-primary mb-3" role="status" style="width: 2.5rem; height: 2.5rem;">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <div class="fw-bold text-slate-800 fs-15">Connecting to Registry Engine...</div>
                        <div class="text-secondary fs-12">Retrieving live nameservers and security status</div>
                    </div>

                    <!-- Main Content (revealed after loading) -->
                    <div id="dmLoadedContent" style="display: none;">
                        <!-- Nav Tabs inside Modal -->
                        <ul class="nav cf-dm-nav-pills mb-4" id="dmTabs" role="tablist">
                            <li class="nav-item flex-fill text-center" role="presentation">
                                <button class="nav-link active w-100" id="dm-tab-ns" data-bs-toggle="pill" data-bs-target="#dm-pane-ns" type="button" role="tab">
                                    <i class="las la-server me-1"></i> Nameservers
                                </button>
                            </li>
                            <li class="nav-item flex-fill text-center" role="presentation">
                                <button class="nav-link w-100" id="dm-tab-security" data-bs-toggle="pill" data-bs-target="#dm-pane-security" type="button" role="tab">
                                    <i class="las la-lock me-1"></i> Security &amp; Transfer
                                </button>
                            </li>
                            <li class="nav-item flex-fill text-center" role="presentation">
                                <button class="nav-link w-100" id="dm-tab-settings" data-bs-toggle="pill" data-bs-target="#dm-pane-settings" type="button" role="tab">
                                    <i class="las la-shield-alt me-1"></i> Privacy &amp; Renew
                                </button>
                            </li>
                            <li class="nav-item flex-fill text-center" role="presentation">
                                <button class="nav-link w-100" id="dm-tab-guide" data-bs-toggle="pill" data-bs-target="#dm-pane-guide" type="button" role="tab">
                                    <i class="las la-compass me-1"></i> DNS Setup Guide
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content" id="dmTabsContent">
                            
                            <!-- TAB 1: NAMESERVERS -->
                            <div class="tab-pane fade show active" id="dm-pane-ns" role="tabpanel">
                                <div class="alert alert-info py-2.5 px-3 fs-12 mb-3 border-0 bg-blue-subtle text-primary rounded-3">
                                    <i class="las la-info-circle me-1"></i> Point your domain to CloudFlox hosting or external platforms (Cloudflare, Shopify, AWS, etc.).
                                </div>

                                <!-- NS Feedback Alert -->
                                <div id="dmNsAlert" class="alert d-none mb-3 py-2 px-3 fs-13" role="alert"></div>

                                <form id="dmNameserversForm" onsubmit="saveDomainNameservers(event)">
                                    <!-- Option 1: Default Nameservers -->
                                    <div class="cf-dm-card mb-3 cursor-pointer" onclick="document.getElementById('dmNsRadioDefault').checked = true; toggleNsMode();">
                                        <div class="form-check d-flex align-items-start gap-2 mb-0">
                                            <input class="form-check-input mt-1" type="radio" name="dmNsType" id="dmNsRadioDefault" value="default" onchange="toggleNsMode()">
                                            <label class="form-check-label flex-grow-1 cursor-pointer" for="dmNsRadioDefault">
                                                <div class="fw-bold text-slate-900 fs-14">Use CloudFlox Anycast DNS <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill fs-10 ms-1">Recommended</span></div>
                                                <div class="text-secondary fs-12">Global distributed Anycast network with built-in DDoS mitigation, zero routing hops, and 100% uptime SLA.</div>
                                                <div class="mt-2 font-monospace fs-12 text-slate-700 bg-light p-2.5 rounded-2 border">
                                                    <div><strong>NS1:</strong> ns1.cloudflox.com</div>
                                                    <div><strong>NS2:</strong> ns2.cloudflox.com</div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Option 2: Custom Nameservers -->
                                    <div class="cf-dm-card mb-3">
                                        <div class="form-check d-flex align-items-start gap-2 mb-2 cursor-pointer" onclick="document.getElementById('dmNsRadioCustom').checked = true; toggleNsMode();">
                                            <input class="form-check-input mt-1" type="radio" name="dmNsType" id="dmNsRadioCustom" value="custom" onchange="toggleNsMode()">
                                            <label class="form-check-label flex-grow-1 cursor-pointer" for="dmNsRadioCustom">
                                                <div class="fw-bold text-slate-900 fs-14">Use Custom Nameservers</div>
                                                <div class="text-secondary fs-12">Point your domain to external providers such as Cloudflare, Shopify, Google Cloud, or custom private nameservers.</div>
                                            </label>
                                        </div>

                                        <!-- Custom NS Inputs -->
                                        <div id="dmCustomNsContainer" style="display: none;" class="pt-2">
                                            <div class="row g-2.5 mb-2.5">
                                                <div class="col-md-6">
                                                    <label class="form-label fs-12 fw-bold text-slate-700 mb-1">Nameserver 1 <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control form-control-sm font-monospace" id="dmNs1" placeholder="e.g. ns1.example.com">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fs-12 fw-bold text-slate-700 mb-1">Nameserver 2 <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control form-control-sm font-monospace" id="dmNs2" placeholder="e.g. ns2.example.com">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fs-12 fw-bold text-slate-700 mb-1">Nameserver 3 <span class="text-muted fw-normal">(Optional)</span></label>
                                                    <input type="text" class="form-control form-control-sm font-monospace" id="dmNs3" placeholder="e.g. ns3.example.com">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fs-12 fw-bold text-slate-700 mb-1">Nameserver 4 <span class="text-muted fw-normal">(Optional)</span></label>
                                                    <input type="text" class="form-control form-control-sm font-monospace" id="dmNs4" placeholder="e.g. ns4.example.com">
                                                </div>
                                            </div>

                                            <!-- Presets -->
                                            <div class="d-flex align-items-center gap-1.5 flex-wrap pt-1">
                                                <span class="fs-11 text-muted me-1">Quick Presets:</span>
                                                <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill fs-11 py-0.5 px-2" onclick="setNsPreset('cloudflare')">Cloudflare</button>
                                                <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill fs-11 py-0.5 px-2" onclick="setNsPreset('shopify')">Shopify</button>
                                                <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill fs-11 py-0.5 px-2" onclick="setNsPreset('google')">Google Cloud</button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-center justify-content-between pt-2">
                                        <span class="fs-11 text-muted"><i class="las la-clock me-1"></i>Changes propagate globally within 2 to 24 hours</span>
                                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold fs-13" id="dmBtnSaveNs">
                                            <span id="dmSaveNsSpinner" class="spinner-border spinner-border-sm me-1 d-none" role="status"></span>
                                            <i class="las la-save me-1" id="dmSaveNsIcon"></i> Save Nameservers
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <!-- TAB 2: TRANSFER & SECURITY -->
                            <div class="tab-pane fade" id="dm-pane-security" role="tabpanel">
                                <!-- Registrar Lock -->
                                <div class="cf-dm-card mb-3">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                <h6 class="fw-bold text-slate-900 mb-0">Registrar Transfer Lock</h6>
                                                <span class="badge" id="dmLockBadge">LOCKED</span>
                                            </div>
                                            <p class="text-secondary fs-12 mb-0">Locks the domain at the registry level to prevent unauthorized transfer attempts. Keep enabled unless moving to another registrar.</p>
                                        </div>
                                        <div class="form-check form-switch fs-22 ms-3">
                                            <input class="form-check-input cursor-pointer" type="checkbox" role="switch" id="dmLockSwitch" onchange="toggleDomainLock(this.checked)">
                                        </div>
                                    </div>
                                </div>

                                <!-- EPP / Auth Code -->
                                <div class="cf-dm-card">
                                    <div class="d-flex align-items-start justify-content-between gap-3 mb-2">
                                        <div>
                                            <h6 class="fw-bold text-slate-900 mb-1">EPP / Authorization Code</h6>
                                            <p class="text-secondary fs-12 mb-0">The secret verification code required if you choose to transfer this domain to an external registrar.</p>
                                        </div>
                                        <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-bold flex-shrink-0" id="dmBtnGetEpp" onclick="fetchEppCode()">
                                            <span id="dmEppSpinner" class="spinner-border spinner-border-sm me-1 d-none" role="status"></span>
                                            <i class="las la-key me-1" id="dmEppIcon"></i> Request EPP Code
                                        </button>
                                    </div>
                                    
                                    <div id="dmEppResultContainer" class="d-none mt-2 p-3 bg-light rounded-3 border">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                            <div>
                                                <span class="fs-11 text-muted d-block">Your Transfer Authorization Code:</span>
                                                <code class="fs-15 fw-bold text-primary font-monospace" id="dmEppCodeDisplay">--</code>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-white border rounded-pill px-3 py-1 fs-12 fw-bold" onclick="copyEppCode()">
                                                <i class="las la-copy me-1"></i> <span id="dmCopyText">Copy Code</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- TAB 3: PRIVACY & AUTO-RENEW -->
                            <div class="tab-pane fade" id="dm-pane-settings" role="tabpanel">
                                <!-- WHOIS ID Protection -->
                                <div class="cf-dm-card mb-3">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 42px; height: 42px; background: rgba(16, 185, 129, 0.12); color: #10b981;">
                                                <i class="las la-user-shield fs-22"></i>
                                            </div>
                                            <div>
                                                <h6 class="fw-bold text-slate-900 mb-0.5">WHOIS ID Privacy Protection</h6>
                                                <p class="text-secondary fs-12 mb-0">Shields your contact details (name, address, email, phone) from public WHOIS directory queries to prevent spam and identity theft.</p>
                                            </div>
                                        </div>
                                        <div class="form-check form-switch fs-22 ms-3">
                                            <input class="form-check-input cursor-pointer" type="checkbox" role="switch" id="dmIdProtectSwitch" onchange="toggleIdProtection(this.checked)">
                                        </div>
                                    </div>
                                </div>

                                <!-- Auto-Renew Setting -->
                                <div class="cf-dm-card mb-3">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 42px; height: 42px; background: rgba(13, 110, 253, 0.1); color: #0d6efd;">
                                                <i class="las la-sync-alt fs-22"></i>
                                            </div>
                                            <div>
                                                <h6 class="fw-bold text-slate-900 mb-0.5">Domain Auto-Renewal</h6>
                                                <p class="text-secondary fs-12 mb-0">Automatically issues renewal invoices 30 days before expiration so your domain name and cloud services never experience downtime.</p>
                                            </div>
                                        </div>
                                        <div class="form-check form-switch fs-22 ms-3">
                                            <input class="form-check-input cursor-pointer" type="checkbox" role="switch" id="dmAutoRenewSwitch" onchange="toggleAutoRenew(this.checked)">
                                        </div>
                                    </div>
                                </div>

                                <!-- Lifecycle Meta Box -->
                                <div class="p-3 bg-light rounded-3 border">
                                    <div class="row g-2 text-center">
                                        <div class="col-4 border-end">
                                            <span class="fs-11 text-muted d-block">Registered Date</span>
                                            <strong class="fs-12 text-slate-800" id="dmMetaRegDate">--</strong>
                                        </div>
                                        <div class="col-4 border-end">
                                            <span class="fs-11 text-muted d-block">Next Due / Expiry</span>
                                            <strong class="fs-12 text-slate-800" id="dmMetaDueDate">--</strong>
                                        </div>
                                        <div class="col-4">
                                            <span class="fs-11 text-muted d-block">Annual Renewal Rate</span>
                                            <strong class="fs-12 text-primary" id="dmMetaPrice">--</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- TAB 4: QUICK DNS SETUP GUIDE -->
                            <div class="tab-pane fade" id="dm-pane-guide" role="tabpanel">
                                <div class="cf-dm-card mb-3">
                                    <div class="fw-bold text-slate-900 fs-13 mb-2 d-flex align-items-center gap-1.5">
                                        <i class="las la-server text-primary fs-16"></i> Point Domain to Cloud VPS / Dedicated Server
                                    </div>
                                    <p class="text-secondary fs-12 mb-2">If you have a VPS or server, configure an <strong>A Record</strong> pointing to your server's public IPv4 address:</p>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered fs-12 font-monospace mb-0">
                                            <thead class="table-light"><tr><th>Type</th><th>Host / Name</th><th>Value / Target</th><th>TTL</th></tr></thead>
                                            <tbody>
                                                <tr><td>A</td><td>@</td><td>YOUR.SERVER.IP.ADDRESS</td><td>Automatic (3600)</td></tr>
                                                <tr><td>CNAME</td><td>www</td><td><span class="dmGuideDomainName">yourdomain.com</span></td><td>Automatic (3600)</td></tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="cf-dm-card mb-3">
                                    <div class="fw-bold text-slate-900 fs-13 mb-2 d-flex align-items-center gap-1.5">
                                        <i class="las la-shopping-bag text-success fs-16"></i> Point Domain to Shopify Store
                                    </div>
                                    <p class="text-secondary fs-12 mb-2">Inside your DNS Zone or DNS manager, configure these standard Shopify records:</p>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered fs-12 font-monospace mb-0">
                                            <thead class="table-light"><tr><th>Type</th><th>Host / Name</th><th>Points To</th><th>TTL</th></tr></thead>
                                            <tbody>
                                                <tr><td>A</td><td>@</td><td>23.227.38.65</td><td>Automatic</td></tr>
                                                <tr><td>CNAME</td><td>www</td><td>shops.myshopify.com</td><td>Automatic</td></tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="cf-dm-card">
                                    <div class="fw-bold text-slate-900 fs-13 mb-2 d-flex align-items-center gap-1.5">
                                        <i class="las la-envelope text-info fs-16"></i> Google Workspace &amp; Custom Mail
                                    </div>
                                    <p class="text-secondary fs-12 mb-0">To connect Google Workspace or custom mailboxes, add Google's MX records (`ASPMX.L.GOOGLE.COM`, priority 1). If you require custom DNS assistance, our NOC team is available 24/7 via ticket support to configure it for you.</p>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="modal-footer border-top py-2.5 px-4 bg-light d-flex align-items-center justify-content-between">
                    <span class="fs-11 text-muted"><i class="las la-shield-alt text-success me-1"></i>Protected by CloudFlox Anycast DNS Engine</span>
                    <button type="button" class="btn btn-secondary rounded-pill px-4 fw-bold fs-13" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- =====================================================================
         HOSTING & CPANEL MANAGEMENT MODAL
         ===================================================================== -->
    <div class="modal fade cf-dm-modal" id="hostingManageModal" tabindex="-1" aria-labelledby="hmModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0">
                <div class="modal-header border-bottom py-3 px-4 bg-white align-items-center">
                    <div class="d-flex align-items-center gap-2.5">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: rgba(13, 110, 253, 0.1); color: #0d6efd;">
                            <i class="las la-server fs-22"></i>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <h5 class="modal-title fw-bold text-slate-900 mb-0" id="hmModalTitle">Hosting Management</h5>
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill fs-11" id="hmModalStatusBadge">Active</span>
                            </div>
                            <span class="fs-12 text-primary font-monospace fw-semibold" id="hmModalDomainName">example.com</span>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4 bg-white">
                    <!-- Loading State -->
                    <div id="hmLoadingState" class="text-center py-5">
                        <div class="spinner-border text-primary mb-3" role="status" style="width: 2.5rem; height: 2.5rem;">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <div class="fw-bold text-slate-800 fs-15">Connecting to Cloud Server Node...</div>
                        <div class="text-secondary fs-12">Retrieving live cPanel server status and resource quotas</div>
                    </div>

                    <!-- Main Content -->
                    <div id="hmLoadedContent" style="display: none;">
                        <!-- Nav Tabs inside Modal -->
                        <ul class="nav cf-dm-nav-pills mb-4" id="hmTabs" role="tablist">
                            <li class="nav-item flex-fill text-center" role="presentation">
                                <button class="nav-link active w-100" id="hm-tab-overview" data-bs-toggle="pill" data-bs-target="#hm-pane-overview" type="button" role="tab">
                                    <i class="las la-tachometer-alt me-1"></i> Overview &amp; cPanel
                                </button>
                            </li>
                            <li class="nav-item flex-fill text-center" role="presentation">
                                <button class="nav-link w-100" id="hm-tab-password" data-bs-toggle="pill" data-bs-target="#hm-pane-password" type="button" role="tab">
                                    <i class="las la-key me-1"></i> Change Password
                                </button>
                            </li>
                            <li class="nav-item flex-fill text-center" role="presentation">
                                <button class="nav-link w-100" id="hm-tab-ftp" data-bs-toggle="pill" data-bs-target="#hm-pane-ftp" type="button" role="tab">
                                    <i class="las la-cog me-1"></i> FTP &amp; Mail Settings
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content" id="hmTabsContent">
                            
                            <!-- TAB 1: OVERVIEW & CPANEL -->
                            <div class="tab-pane fade show active" id="hm-pane-overview" role="tabpanel">
                                <!-- 1-Click Launch Action Card -->
                                <div class="p-3.5 mb-3.5 rounded-3 d-flex align-items-center justify-content-between gap-3 flex-wrap border" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: #fff;">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px; background: rgba(255,255,255,0.15); color: #38bdf8;">
                                            <i class="las la-terminal fs-24"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold fs-14 text-white">Direct Control Panel Access</div>
                                            <div class="fs-12 text-slate-300">Open your cPanel dashboard directly on our high-speed NVMe cloud node.</div>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <a href="#" target="_blank" rel="noopener" id="hmBtnCpanelSso" class="btn btn-primary rounded-pill px-3.5 py-2 fw-bold fs-12.5 d-inline-flex align-items-center gap-1.5 shadow-sm">
                                            <i class="las la-external-link-alt fs-14"></i> Log in to cPanel
                                        </a>
                                        <a href="#" target="_blank" rel="noopener" id="hmBtnWebmail" class="btn btn-outline-light rounded-pill px-3 py-2 fw-bold fs-12.5 d-inline-flex align-items-center gap-1">
                                            <i class="las la-envelope fs-14"></i> Webmail
                                        </a>
                                    </div>
                                </div>

                                <!-- Credentials & Connection Details -->
                                <div class="cf-dm-card mb-3.5">
                                    <div class="fw-bold text-slate-900 fs-13 mb-3 d-flex align-items-center gap-1.5">
                                        <i class="las la-network-wired text-primary fs-16"></i> Server &amp; Connection Details
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-sm-6">
                                            <span class="fs-11 text-muted d-block mb-0.5">cPanel Username</span>
                                            <div class="d-flex align-items-center gap-2">
                                                <code class="fs-13 fw-bold text-slate-900 font-monospace bg-light px-2 py-1 rounded border" id="hmUsername">--</code>
                                                <button type="button" class="btn btn-xs btn-light border rounded-pill px-2 py-0.5 fs-11" onclick="copyText('hmUsername', this)" title="Copy Username"><i class="las la-copy"></i></button>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <span class="fs-11 text-muted d-block mb-0.5">Server IP Address</span>
                                            <div class="d-flex align-items-center gap-2">
                                                <code class="fs-13 fw-bold text-slate-900 font-monospace bg-light px-2 py-1 rounded border" id="hmServerIp">--</code>
                                                <button type="button" class="btn btn-xs btn-light border rounded-pill px-2 py-0.5 fs-11" onclick="copyText('hmServerIp', this)" title="Copy IP"><i class="las la-copy"></i></button>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <span class="fs-11 text-muted d-block mb-0.5">Server Hostname</span>
                                            <span class="fs-13 fw-bold text-slate-800 font-monospace" id="hmServerHost">--</span>
                                        </div>
                                        <div class="col-sm-6">
                                            <span class="fs-11 text-muted d-block mb-0.5">Assigned Nameservers</span>
                                            <span class="fs-12 font-monospace text-slate-700" id="hmNameservers">ns1.cloudflox.com, ns2.cloudflox.com</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Resource Quotas -->
                                <div class="cf-dm-card">
                                    <div class="fw-bold text-slate-900 fs-13 mb-3 d-flex align-items-center gap-1.5">
                                        <i class="las la-chart-pie text-success fs-16"></i> Resource Allocations
                                    </div>
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between fs-12 mb-1">
                                            <span class="fw-semibold text-slate-700">Disk Space Quota</span>
                                            <span class="text-secondary" id="hmDiskUsageText">0 MB / 5,120 MB (0%)</span>
                                        </div>
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-primary" id="hmDiskProgressBar" role="progressbar" style="width: 0%;"></div>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="d-flex justify-content-between fs-12 mb-1">
                                            <span class="fw-semibold text-slate-700">Monthly Bandwidth</span>
                                            <span class="text-secondary" id="hmBwUsageText">0 MB / 250,000 MB (0%)</span>
                                        </div>
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-success" id="hmBwProgressBar" role="progressbar" style="width: 0%;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- TAB 2: CHANGE PASSWORD -->
                            <div class="tab-pane fade" id="hm-pane-password" role="tabpanel">
                                <div class="alert alert-info py-2.5 px-3 fs-12 mb-3 border-0 bg-blue-subtle text-primary rounded-3">
                                    <i class="las la-info-circle me-1"></i> Changing this password updates both your cPanel and primary FTP account access credentials immediately.
                                </div>

                                <div id="hmPassAlert" class="alert d-none mb-3 py-2 px-3 fs-13" role="alert"></div>

                                <form id="hmPasswordForm" onsubmit="changeHostingPassword(event)">
                                    <div class="cf-dm-card mb-3">
                                        <div class="mb-3">
                                            <label class="form-label fs-12 fw-bold text-slate-700 mb-1">New cPanel Password <span class="text-danger">*</span></label>
                                            <div class="position-relative">
                                                <input type="password" class="form-control form-control-sm pe-5" id="hmNewPassword" placeholder="Minimum 8 characters (letters, numbers, symbols)" required>
                                                <button type="button" class="btn btn-sm text-secondary position-absolute end-0 top-50 translate-middle-y border-0 bg-transparent" onclick="togglePassVisibility('hmNewPassword', 'hmEyeIcon1')">
                                                    <i class="las la-eye fs-16" id="hmEyeIcon1"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="form-label fs-12 fw-bold text-slate-700 mb-1">Confirm New Password <span class="text-danger">*</span></label>
                                            <div class="position-relative">
                                                <input type="password" class="form-control form-control-sm pe-5" id="hmConfirmPassword" placeholder="Re-enter new password" required>
                                                <button type="button" class="btn btn-sm text-secondary position-absolute end-0 top-50 translate-middle-y border-0 bg-transparent" onclick="togglePassVisibility('hmConfirmPassword', 'hmEyeIcon2')">
                                                    <i class="las la-eye fs-16" id="hmEyeIcon2"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-center justify-content-between pt-1">
                                        <span class="fs-11 text-muted"><i class="las la-shield-alt text-success me-1"></i>256-bit Encrypted Update</span>
                                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold fs-13" id="hmBtnSavePass">
                                            <span id="hmSavePassSpinner" class="spinner-border spinner-border-sm me-1 d-none" role="status"></span>
                                            <i class="las la-save me-1" id="hmSavePassIcon"></i> Update Password
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <!-- TAB 3: FTP & EMAIL SETTINGS -->
                            <div class="tab-pane fade" id="hm-pane-ftp" role="tabpanel">
                                <div class="cf-dm-card mb-3">
                                    <div class="fw-bold text-slate-900 fs-13 mb-2 d-flex align-items-center gap-1.5">
                                        <i class="las la-folder text-warning fs-16"></i> File Transfer Protocol (FTP) Access
                                    </div>
                                    <p class="text-secondary fs-12 mb-2">Connect via FileZilla, Cyberduck, or WinSCP to upload your website source files:</p>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered fs-12 font-monospace mb-0">
                                            <tbody>
                                                <tr><td class="fw-bold text-slate-700" style="width: 140px;">FTP Host:</td><td id="hmFtpHost">ftp.yourdomain.com (or Server IP)</td></tr>
                                                <tr><td class="fw-bold text-slate-700">Port:</td><td>21</td></tr>
                                                <tr><td class="fw-bold text-slate-700">Username:</td><td id="hmFtpUser">cpanel_username</td></tr>
                                                <tr><td class="fw-bold text-slate-700">Directory:</td><td>/public_html</td></tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="cf-dm-card">
                                    <div class="fw-bold text-slate-900 fs-13 mb-2 d-flex align-items-center gap-1.5">
                                        <i class="las la-envelope text-info fs-16"></i> Mail Client Manual Configuration (Outlook, iOS, Android)
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered fs-12 font-monospace mb-0">
                                            <tbody>
                                                <tr><td class="fw-bold text-slate-700" style="width: 140px;">Incoming Server:</td><td id="hmMailIncoming">sv.cloudflox.com (Port 993 IMAP SSL)</td></tr>
                                                <tr><td class="fw-bold text-slate-700">Outgoing Server:</td><td id="hmMailOutgoing">sv.cloudflox.com (Port 465 SMTP SSL)</td></tr>
                                                <tr><td class="fw-bold text-slate-700">Webmail Access:</td><td><a href="#" target="_blank" rel="noopener" id="hmWebmailDirectLink" class="text-primary text-decoration-none">https://sv.cloudflox.com:2096</a></td></tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="modal-footer border-top py-2.5 px-4 bg-light d-flex align-items-center justify-content-between">
                    <span class="fs-11 text-muted"><i class="las la-lock text-success me-1"></i>Protected by CloudFlox SSO Gateway</span>
                    <button type="button" class="btn btn-secondary rounded-pill px-4 fw-bold fs-13" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- =====================================================================
         SERVER QUOTATION & ACCEPTANCE MODAL
         ===================================================================== -->
    <div class="modal fade" id="quoteDetailsModal" tabindex="-1" aria-labelledby="quoteModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 18px; overflow: hidden;">
                <div class="modal-header border-bottom py-3 px-4 bg-white align-items-center">
                    <div class="d-flex align-items-center gap-2.5">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; background: rgba(37, 99, 235, 0.1); color: #2563EB;">
                            <i class="las la-file-invoice fs-24"></i>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <h5 class="modal-title fw-bold text-slate-900 fs-16 mb-0" id="quoteModalTitle">Quotation #RFQ-XXXX</h5>
                                <span id="quoteModalBadge"></span>
                            </div>
                            <small class="text-secondary fs-12" id="quoteModalDates">Created: -- &bull; Valid Until: --</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4 bg-white">
                    <!-- Loading State -->
                    <div id="quoteLoadingState" class="text-center py-5">
                        <div class="spinner-border text-primary mb-3" role="status" style="width: 2.5rem; height: 2.5rem;">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="text-secondary fs-13 mb-0">Retrieving official server quotation details...</p>
                    </div>

                    <!-- Loaded Content -->
                    <div id="quoteLoadedContent" style="display: none;">
                        <!-- Alert Box for Accept Action -->
                        <div id="quoteAcceptAlert" class="alert d-none mb-3" role="alert"></div>

                        <!-- Quote Subject & Proposal Overview -->
                        <div class="card border border-light-subtle rounded-3 p-3 mb-3" style="background: #F8FAFC;">
                            <h6 class="fw-bold text-slate-900 fs-14 mb-1" id="quoteModalSubject">Custom Server Architecture</h6>
                            <p class="text-secondary fs-12.5 mb-2" id="quoteModalProposal">CloudFlox tailored server proposal.</p>
                            <div id="quoteModalCustNotesWrap" class="fs-12 text-muted border-top pt-2 mt-2 d-none">
                                <strong>Your Technical Requirements:</strong> <span id="quoteModalCustNotes"></span>
                            </div>
                        </div>

                        <!-- Line Items Table -->
                        <h6 class="fw-bold text-slate-900 fs-13 mb-2 text-uppercase letter-spacing-1">Allocated Hardware &amp; Services</h6>
                        <div class="table-responsive mb-3 border rounded-3 overflow-hidden">
                            <table class="table mb-0 fs-13">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3 py-2">Item / Specification</th>
                                        <th class="text-center py-2" style="width: 70px;">Qty</th>
                                        <th class="text-end py-2" style="width: 110px;">Unit Price</th>
                                        <th class="text-end pe-3 py-2" style="width: 120px;">Total</th>
                                    </tr>
                                </thead>
                                <tbody id="quoteModalItemsBody">
                                    <!-- Populated via JS -->
                                </tbody>
                                <tfoot class="border-top" style="background: #F8FAFC;">
                                    <tr>
                                        <td colspan="3" class="text-end fw-bold py-2.5 ps-3">Total Quoted Amount:</td>
                                        <td class="text-end fw-800 text-slate-900 pe-3 py-2.5 fs-15" id="quoteModalGrandTotal">$0.00 USD</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <!-- Under Review Notice (If Draft) -->
                        <div id="quoteDraftNotice" class="alert alert-info py-2.5 px-3 fs-12.5 mb-0 border-0 rounded-3 d-none">
                            <i class="las la-clock me-1.5 fs-15"></i>
                            <strong>Under Review:</strong> Our senior infrastructure team is confirming hardware availability and network routing. Once finalized, pricing will be posted and this quotation will be delivered for your acceptance.
                        </div>

                        <!-- Ready To Accept Notice (If Delivered) -->
                        <div id="quoteDeliveredNotice" class="alert alert-success py-2.5 px-3 fs-12.5 mb-0 border-0 rounded-3 d-none">
                            <i class="las la-check-circle me-1.5 fs-15"></i>
                            <strong>Quotation Ready:</strong> You can accept this quotation below. Upon acceptance, an official payable invoice will be generated and you will be routed to complete payment.
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-top py-2.5 px-4 bg-light d-flex align-items-center justify-content-between">
                    <span class="fs-11 text-muted"><i class="las la-shield-alt text-success me-1"></i>Backed by CloudFlox 99.95% Network SLA</span>
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-secondary rounded-pill px-3 py-1.5 fw-bold fs-12" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-success rounded-pill px-4 py-1.5 fw-bold fs-12 text-white shadow-sm d-none" id="btnAcceptQuote" onclick="submitQuoteAcceptance()">
                            <span class="btn-text"><i class="las la-check-circle me-1"></i> Accept Quotation &amp; Pay Invoice</span>
                            <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

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

    // =========================================================================
    // DOMAIN MANAGEMENT ENGINE
    // =========================================================================
    let currentDmDomainId = null;
    let currentDmDomainName = '';

    function openDomainManageModal(domainId, domainName) {
        currentDmDomainId = domainId;
        currentDmDomainName = domainName;

        // Reset modal fields
        document.getElementById('dmModalTitle').innerText = 'Domain Management';
        document.getElementById('dmModalDomainName').innerText = domainName;
        document.querySelectorAll('.dmGuideDomainName').forEach(el => el.innerText = domainName);

        document.getElementById('dmLoadingState').style.display = 'block';
        document.getElementById('dmLoadedContent').style.display = 'none';
        
        const alertBox = document.getElementById('dmNsAlert');
        alertBox.className = 'alert d-none';
        alertBox.innerText = '';

        document.getElementById('dmEppResultContainer').classList.add('d-none');
        document.getElementById('dmEppCodeDisplay').innerText = '--';

        // Activate first tab (Nameservers)
        const firstTab = document.getElementById('dm-tab-ns');
        if (firstTab && window.bootstrap) {
            bootstrap.Tab.getOrCreateInstance(firstTab).show();
        }

        // Show Modal
        const modalEl = document.getElementById('domainManageModal');
        const bsModal = bootstrap.Modal.getOrCreateInstance(modalEl);
        bsModal.show();

        // Query Domain Data from API
        fetch('api/domain-manage.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'get_info',
                domain_id: domainId
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status !== 'success') {
                alert(data.message || 'Unable to retrieve domain data.');
                bsModal.hide();
                return;
            }

            document.getElementById('dmLoadingState').style.display = 'none';
            document.getElementById('dmLoadedContent').style.display = 'block';

            // Status Badge
            const badge = document.getElementById('dmModalStatusBadge');
            badge.innerText = data.domain.status;
            if (data.domain.status.toLowerCase() === 'active') {
                badge.className = 'badge bg-success-subtle text-success border border-success-subtle rounded-pill fs-11';
            } else {
                badge.className = 'badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill fs-11';
            }

            // Nameservers
            const isDefault = data.nameservers.is_default;
            if (isDefault) {
                document.getElementById('dmNsRadioDefault').checked = true;
                document.getElementById('dmCustomNsContainer').style.display = 'none';
            } else {
                document.getElementById('dmNsRadioCustom').checked = true;
                document.getElementById('dmCustomNsContainer').style.display = 'block';
            }
            document.getElementById('dmNs1').value = data.nameservers.ns1 || '';
            document.getElementById('dmNs2').value = data.nameservers.ns2 || '';
            document.getElementById('dmNs3').value = data.nameservers.ns3 || '';
            document.getElementById('dmNs4').value = data.nameservers.ns4 || '';

            // Lock Status
            const isLocked = (data.lock_status === 'locked');
            document.getElementById('dmLockSwitch').checked = isLocked;
            updateLockBadge(isLocked);

            // WHOIS Privacy
            document.getElementById('dmIdProtectSwitch').checked = (data.domain.idprotection === 1);

            // Auto-Renew
            document.getElementById('dmAutoRenewSwitch').checked = (data.domain.auto_renew === 1);

            // Meta Info
            document.getElementById('dmMetaRegDate').innerText = data.domain.registrationdate || 'N/A';
            document.getElementById('dmMetaDueDate').innerText = data.domain.nextduedate || 'N/A';
            document.getElementById('dmMetaPrice').innerText = '$' + data.domain.recurringamount + ' USD';
        })
        .catch(err => {
            console.error('Error loading domain:', err);
            alert('Failed to connect to the domain registry engine.');
            bsModal.hide();
        });
    }

    function updateLockBadge(isLocked) {
        const badge = document.getElementById('dmLockBadge');
        if (isLocked) {
            badge.innerText = 'LOCKED (Protected)';
            badge.className = 'badge bg-success-subtle text-success border border-success-subtle rounded-pill fs-11';
        } else {
            badge.innerText = 'UNLOCKED (Transfer Ready)';
            badge.className = 'badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill fs-11';
        }
    }

    function toggleNsMode() {
        const isCustom = document.getElementById('dmNsRadioCustom').checked;
        const container = document.getElementById('dmCustomNsContainer');
        if (isCustom) {
            container.style.display = 'block';
        } else {
            container.style.display = 'none';
        }
    }

    function setNsPreset(preset) {
        document.getElementById('dmNsRadioCustom').checked = true;
        document.getElementById('dmCustomNsContainer').style.display = 'block';

        if (preset === 'cloudflare') {
            document.getElementById('dmNs1').value = 'elena.ns.cloudflare.com';
            document.getElementById('dmNs2').value = 'dane.ns.cloudflare.com';
            document.getElementById('dmNs3').value = '';
            document.getElementById('dmNs4').value = '';
        } else if (preset === 'shopify') {
            document.getElementById('dmNs1').value = 'ns1.shops.myshopify.com';
            document.getElementById('dmNs2').value = 'ns2.shops.myshopify.com';
            document.getElementById('dmNs3').value = '';
            document.getElementById('dmNs4').value = '';
        } else if (preset === 'google') {
            document.getElementById('dmNs1').value = 'ns-cloud-a1.googledomains.com';
            document.getElementById('dmNs2').value = 'ns-cloud-a2.googledomains.com';
            document.getElementById('dmNs3').value = 'ns-cloud-a3.googledomains.com';
            document.getElementById('dmNs4').value = 'ns-cloud-a4.googledomains.com';
        }
    }

    function saveDomainNameservers(e) {
        e.preventDefault();
        if (!currentDmDomainId) return;

        const isDefault = document.getElementById('dmNsRadioDefault').checked;
        const nsType = isDefault ? 'default' : 'custom';
        const ns1 = document.getElementById('dmNs1').value.trim();
        const ns2 = document.getElementById('dmNs2').value.trim();
        const ns3 = document.getElementById('dmNs3').value.trim();
        const ns4 = document.getElementById('dmNs4').value.trim();

        const btn = document.getElementById('dmBtnSaveNs');
        const spinner = document.getElementById('dmSaveNsSpinner');
        const icon = document.getElementById('dmSaveNsIcon');
        const alertBox = document.getElementById('dmNsAlert');

        btn.disabled = true;
        spinner.classList.remove('d-none');
        icon.classList.add('d-none');
        alertBox.className = 'alert d-none';

        fetch('api/domain-manage.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'save_nameservers',
                domain_id: currentDmDomainId,
                type: nsType,
                ns1: ns1,
                ns2: ns2,
                ns3: ns3,
                ns4: ns4
            })
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            spinner.classList.add('d-none');
            icon.classList.remove('d-none');

            alertBox.classList.remove('d-none');
            if (data.status === 'success') {
                alertBox.className = 'alert alert-success py-2.5 px-3 fs-13 mb-3 border-0 bg-success-subtle text-success rounded-3';
                alertBox.innerHTML = '<i class="las la-check-circle me-1 fs-15"></i> ' + data.message;
            } else {
                alertBox.className = 'alert alert-danger py-2.5 px-3 fs-13 mb-3 border-0 bg-danger-subtle text-danger rounded-3';
                alertBox.innerHTML = '<i class="las la-exclamation-circle me-1 fs-15"></i> ' + (data.message || 'Failed to update nameservers.');
            }
        })
        .catch(err => {
            btn.disabled = false;
            spinner.classList.add('d-none');
            icon.classList.remove('d-none');
            alertBox.className = 'alert alert-danger py-2.5 px-3 fs-13 mb-3 border-0 bg-danger-subtle text-danger rounded-3';
            alertBox.innerHTML = '<i class="las la-times-circle me-1 fs-15"></i> An unexpected network error occurred.';
        });
    }

    function toggleDomainLock(isLocked) {
        if (!currentDmDomainId) return;
        const toggle = document.getElementById('dmLockSwitch');
        toggle.disabled = true;

        fetch('api/domain-manage.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'toggle_lock',
                domain_id: currentDmDomainId,
                lockstatus: isLocked ? 'locked' : 'unlocked'
            })
        })
        .then(res => res.json())
        .then(data => {
            toggle.disabled = false;
            if (data.status === 'success') {
                updateLockBadge(data.lock_status === 'locked');
            } else {
                toggle.checked = !isLocked;
                alert(data.message || 'Failed to update transfer lock status.');
            }
        })
        .catch(err => {
            toggle.disabled = false;
            toggle.checked = !isLocked;
            alert('Communication error updating transfer lock.');
        });
    }

    function fetchEppCode() {
        if (!currentDmDomainId) return;
        const btn = document.getElementById('dmBtnGetEpp');
        const spinner = document.getElementById('dmEppSpinner');
        const icon = document.getElementById('dmEppIcon');
        const container = document.getElementById('dmEppResultContainer');
        const display = document.getElementById('dmEppCodeDisplay');

        btn.disabled = true;
        spinner.classList.remove('d-none');
        icon.classList.add('d-none');

        fetch('api/domain-manage.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'get_epp',
                domain_id: currentDmDomainId
            })
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            spinner.classList.add('d-none');
            icon.classList.remove('d-none');

            if (data.status === 'success') {
                container.classList.remove('d-none');
                display.innerText = data.eppcode || 'Sent via email';
                if (!data.eppcode) {
                    display.innerText = 'Dispatched to registered contact email';
                }
            } else {
                alert(data.message || 'Unable to retrieve EPP authorization code.');
            }
        })
        .catch(err => {
            btn.disabled = false;
            spinner.classList.add('d-none');
            icon.classList.remove('d-none');
            alert('Communication error requesting EPP code.');
        });
    }

    function copyEppCode() {
        const codeText = document.getElementById('dmEppCodeDisplay').innerText;
        if (!codeText || codeText === '--') return;

        navigator.clipboard.writeText(codeText).then(() => {
            const copyBtnText = document.getElementById('dmCopyText');
            copyBtnText.innerText = 'Copied!';
            setTimeout(() => {
                copyBtnText.innerText = 'Copy Code';
            }, 2000);
        }).catch(() => {
            alert('Unable to copy code to clipboard.');
        });
    }

    function toggleIdProtection(isChecked) {
        if (!currentDmDomainId) return;
        const toggle = document.getElementById('dmIdProtectSwitch');
        toggle.disabled = true;

        fetch('api/domain-manage.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'toggle_idprotect',
                domain_id: currentDmDomainId,
                protect: isChecked ? 1 : 0
            })
        })
        .then(res => res.json())
        .then(data => {
            toggle.disabled = false;
            if (data.status !== 'success') {
                toggle.checked = !isChecked;
                alert(data.message || 'Failed to update WHOIS privacy protection.');
            }
        })
        .catch(err => {
            toggle.disabled = false;
            toggle.checked = !isChecked;
            alert('Communication error updating WHOIS privacy protection.');
        });
    }

    function toggleAutoRenew(isChecked) {
        if (!currentDmDomainId) return;
        const toggle = document.getElementById('dmAutoRenewSwitch');
        toggle.disabled = true;

        fetch('api/domain-manage.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'toggle_autorenew',
                domain_id: currentDmDomainId,
                auto_renew: isChecked ? 1 : 0
            })
        })
        .then(res => res.json())
        .then(data => {
            toggle.disabled = false;
            if (data.status !== 'success') {
                toggle.checked = !isChecked;
                alert(data.message || 'Failed to update auto-renewal preference.');
            }
        })
        .catch(err => {
            toggle.disabled = false;
            toggle.checked = !isChecked;
            alert('Communication error updating auto-renewal preference.');
        });
    }

    // =========================================================================
    // HOSTING & CPANEL MANAGEMENT ENGINE
    // =========================================================================
    let currentHmServiceId = null;
    let currentHmDomain = '';

    function openHostingManageModal(serviceId, domainName) {
        currentHmServiceId = serviceId;
        currentHmDomain = domainName;

        document.getElementById('hmModalTitle').innerText = 'Hosting Management';
        document.getElementById('hmModalDomainName').innerText = domainName || 'Cloud Server';

        document.getElementById('hmLoadingState').style.display = 'block';
        document.getElementById('hmLoadedContent').style.display = 'none';

        const alertBox = document.getElementById('hmPassAlert');
        alertBox.className = 'alert d-none';
        alertBox.innerText = '';

        // Activate first tab (Overview)
        const firstTab = document.getElementById('hm-tab-overview');
        if (firstTab && window.bootstrap) {
            bootstrap.Tab.getOrCreateInstance(firstTab).show();
        }

        // Show Modal
        const modalEl = document.getElementById('hostingManageModal');
        const bsModal = bootstrap.Modal.getOrCreateInstance(modalEl);
        bsModal.show();

        // Query Hosting Data from API
        fetch('api/hosting-manage.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'get_info',
                service_id: serviceId
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status !== 'success') {
                alert(data.message || 'Unable to retrieve hosting service data.');
                bsModal.hide();
                return;
            }

            document.getElementById('hmLoadingState').style.display = 'none';
            document.getElementById('hmLoadedContent').style.display = 'block';

            const s = data.service;
            document.getElementById('hmModalTitle').innerText = s.product_name;

            // Status badge
            const badge = document.getElementById('hmModalStatusBadge');
            badge.innerText = s.status;
            if (s.status.toLowerCase() === 'active') {
                badge.className = 'badge bg-success-subtle text-success border border-success-subtle rounded-pill fs-11';
            } else {
                badge.className = 'badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill fs-11';
            }

            // Credentials
            document.getElementById('hmUsername').innerText = s.username;
            document.getElementById('hmServerIp').innerText = s.server_ip;
            document.getElementById('hmServerHost').innerText = s.server_hostname;
            document.getElementById('hmNameservers').innerText = s.ns1 + ', ' + s.ns2;

            // Links (Direct to cPanel, completely bypassing external WHMCS)
            document.getElementById('hmBtnCpanelSso').href = s.cpanel_launch_url || s.cpanel_url;
            document.getElementById('hmBtnWebmail').href = s.webmail_url;

            // Quotas
            document.getElementById('hmDiskUsageText').innerText = s.disk_usage_mb + ' MB / ' + s.disk_limit_mb + ' MB (' + s.disk_percent + '%)';
            document.getElementById('hmDiskProgressBar').style.width = s.disk_percent + '%';

            document.getElementById('hmBwUsageText').innerText = s.bw_usage_mb + ' MB / ' + s.bw_limit_mb + ' MB (' + s.bw_percent + '%)';
            document.getElementById('hmBwProgressBar').style.width = s.bw_percent + '%';

            // FTP & Mail
            document.getElementById('hmFtpHost').innerText = (s.domain ? 'ftp.' + s.domain + ' (or ' + s.server_ip + ')' : s.server_ip);
            document.getElementById('hmFtpUser').innerText = s.username;
            document.getElementById('hmMailIncoming').innerText = s.server_hostname + ' (Port 993 IMAP SSL)';
            document.getElementById('hmMailOutgoing').innerText = s.server_hostname + ' (Port 465 SMTP SSL)';
            document.getElementById('hmWebmailDirectLink').href = s.webmail_url;
            document.getElementById('hmWebmailDirectLink').innerText = s.webmail_url;
        })
        .catch(err => {
            console.error('Error loading hosting:', err);
            alert('Failed to connect to cloud server manager.');
            bsModal.hide();
        });
    }

    function changeHostingPassword(e) {
        e.preventDefault();
        if (!currentHmServiceId) return;

        const newPass = document.getElementById('hmNewPassword').value.trim();
        const confPass = document.getElementById('hmConfirmPassword').value.trim();
        const btn = document.getElementById('hmBtnSavePass');
        const spinner = document.getElementById('hmSavePassSpinner');
        const icon = document.getElementById('hmSavePassIcon');
        const alertBox = document.getElementById('hmPassAlert');

        if (newPass.length < 8) {
            alertBox.className = 'alert alert-danger py-2.5 px-3 fs-13 mb-3 border-0 bg-danger-subtle text-danger rounded-3';
            alertBox.innerHTML = '<i class="las la-exclamation-circle me-1 fs-15"></i> Password must be at least 8 characters long.';
            alertBox.classList.remove('d-none');
            return;
        }
        if (newPass !== confPass) {
            alertBox.className = 'alert alert-danger py-2.5 px-3 fs-13 mb-3 border-0 bg-danger-subtle text-danger rounded-3';
            alertBox.innerHTML = '<i class="las la-exclamation-circle me-1 fs-15"></i> Passwords do not match.';
            alertBox.classList.remove('d-none');
            return;
        }

        btn.disabled = true;
        spinner.classList.remove('d-none');
        icon.classList.add('d-none');
        alertBox.className = 'alert d-none';

        fetch('api/hosting-manage.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'change_password',
                service_id: currentHmServiceId,
                new_password: newPass,
                confirm_password: confPass
            })
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            spinner.classList.add('d-none');
            icon.classList.remove('d-none');

            alertBox.classList.remove('d-none');
            if (data.status === 'success') {
                alertBox.className = 'alert alert-success py-2.5 px-3 fs-13 mb-3 border-0 bg-success-subtle text-success rounded-3';
                alertBox.innerHTML = '<i class="las la-check-circle me-1 fs-15"></i> ' + data.message;
                document.getElementById('hmNewPassword').value = '';
                document.getElementById('hmConfirmPassword').value = '';
            } else {
                alertBox.className = 'alert alert-danger py-2.5 px-3 fs-13 mb-3 border-0 bg-danger-subtle text-danger rounded-3';
                alertBox.innerHTML = '<i class="las la-exclamation-circle me-1 fs-15"></i> ' + (data.message || 'Failed to update password.');
            }
        })
        .catch(err => {
            btn.disabled = false;
            spinner.classList.add('d-none');
            icon.classList.remove('d-none');
            alertBox.className = 'alert alert-danger py-2.5 px-3 fs-13 mb-3 border-0 bg-danger-subtle text-danger rounded-3';
            alertBox.innerHTML = '<i class="las la-times-circle me-1 fs-15"></i> Network error communicating with server.';
        });
    }

    function copyText(elementId, btn) {
        const el = document.getElementById(elementId);
        if (!el) return;
        const txt = el.innerText.trim();
        if (!txt || txt === '--') return;

        navigator.clipboard.writeText(txt).then(() => {
            const orig = btn.innerHTML;
            btn.innerHTML = '<i class="las la-check text-success"></i>';
            setTimeout(() => { btn.innerHTML = orig; }, 2000);
        });
    }

    // =========================================================================
    // SERVER QUOTATIONS & RFQ WORKFLOW ENGINE
    // =========================================================================
    let currentActiveQuoteId = null;

    function openQuoteModal(quoteId) {
        currentActiveQuoteId = quoteId;
        const modalEl = document.getElementById('quoteDetailsModal');
        const bsModal = bootstrap.Modal.getOrCreateInstance(modalEl);
        
        document.getElementById('quoteLoadingState').style.display = 'block';
        document.getElementById('quoteLoadedContent').style.display = 'none';
        document.getElementById('btnAcceptQuote').classList.add('d-none');
        document.getElementById('quoteAcceptAlert').className = 'alert d-none';
        document.getElementById('quoteAcceptAlert').innerHTML = '';
        
        document.getElementById('quoteModalTitle').innerText = 'Quotation #RFQ-' + quoteId;
        
        bsModal.show();

        fetch('api/quote-manage.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'get_quote', quote_id: quoteId })
        })
        .then(res => res.json())
        .then(data => {
            document.getElementById('quoteLoadingState').style.display = 'none';
            if (data.status === 'success' && data.quote) {
                const q = data.quote;
                document.getElementById('quoteLoadedContent').style.display = 'block';
                document.getElementById('quoteModalSubject').innerText = q.subject || 'Custom Server Architecture';
                document.getElementById('quoteModalProposal').innerText = q.proposal || 'Tailored enterprise server specification prepared by CloudFlox engineering.';
                document.getElementById('quoteModalDates').innerText = 'Created: ' + (q.datecreated || '--') + ' • Valid Until: ' + (q.validuntil || '30 Days');
                
                // Customer notes
                const notesWrap = document.getElementById('quoteModalCustNotesWrap');
                if (q.customernotes && q.customernotes.trim()) {
                    document.getElementById('quoteModalCustNotes').innerText = q.customernotes;
                    notesWrap.classList.remove('d-none');
                } else {
                    notesWrap.classList.add('d-none');
                }

                // Stage badge
                const stage = (q.stage || 'Draft').toLowerCase();
                const badgeEl = document.getElementById('quoteModalBadge');
                if (stage === 'delivered') {
                    badgeEl.innerHTML = '<span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 fs-11 fw-bold"><i class="las la-bell me-1"></i>Delivered</span>';
                } else if (stage === 'accepted') {
                    badgeEl.innerHTML = '<span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1 fs-11 fw-bold"><i class="las la-check-circle me-1"></i>Accepted</span>';
                } else {
                    badgeEl.innerHTML = '<span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2.5 py-1 fs-11 fw-bold"><i class="las la-clock me-1"></i>Under Review</span>';
                }

                // Notices & Accept Button
                const draftNotice = document.getElementById('quoteDraftNotice');
                const deliveredNotice = document.getElementById('quoteDeliveredNotice');
                const btnAccept = document.getElementById('btnAcceptQuote');

                if (stage === 'delivered') {
                    draftNotice.classList.add('d-none');
                    deliveredNotice.classList.remove('d-none');
                    btnAccept.classList.remove('d-none');
                } else if (stage === 'draft' || stage === 'on hold') {
                    draftNotice.classList.remove('d-none');
                    deliveredNotice.classList.add('d-none');
                    btnAccept.classList.add('d-none');
                } else {
                    draftNotice.classList.add('d-none');
                    deliveredNotice.classList.add('d-none');
                    btnAccept.classList.add('d-none');
                }

                // Line items
                const tbody = document.getElementById('quoteModalItemsBody');
                tbody.innerHTML = '';
                if (q.items && q.items.length > 0) {
                    q.items.forEach(it => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td class="ps-3 py-2 fw-semibold text-slate-800">${escapeHtml(it.description)}</td>
                            <td class="text-center py-2 text-muted">${escapeHtml(it.quantity)}</td>
                            <td class="text-end py-2 text-secondary">$${it.unitprice}</td>
                            <td class="text-end pe-3 py-2 fw-bold text-slate-900">$${it.total}</td>
                        `;
                        tbody.appendChild(tr);
                    });
                } else {
                    tbody.innerHTML = '<tr><td colspan="4" class="text-center py-3 text-muted">Awaiting engineering line item pricing.</td></tr>';
                }

                document.getElementById('quoteModalGrandTotal').innerText = '$' + q.total + ' USD';
            } else {
                alert('Error loading quotation: ' + (data.message || 'Unknown error'));
                bsModal.hide();
            }
        })
        .catch(err => {
            console.error(err);
            document.getElementById('quoteLoadingState').style.display = 'none';
            alert('Network error while retrieving quote.');
            bsModal.hide();
        });
    }

    function submitQuoteAcceptance() {
        if (!currentActiveQuoteId) return;
        const btn = document.getElementById('btnAcceptQuote');
        const btnText = btn.querySelector('.btn-text');
        const spinner = btn.querySelector('.spinner-border');
        const alertBox = document.getElementById('quoteAcceptAlert');

        btn.disabled = true;
        btnText.classList.add('d-none');
        spinner.classList.remove('d-none');

        fetch('api/quote-manage.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'accept_quote', quote_id: currentActiveQuoteId })
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btnText.classList.remove('d-none');
            spinner.classList.add('d-none');

            alertBox.classList.remove('d-none');
            if (data.status === 'success') {
                alertBox.className = 'alert alert-success py-2.5 px-3 fs-13 mb-3 border-0 bg-success-subtle text-success rounded-3';
                alertBox.innerHTML = '<i class="las la-check-circle me-1 fs-15"></i> ' + data.message + '<br><small class="text-secondary">Redirecting to payable invoice...</small>';
                btn.classList.add('d-none');
                setTimeout(() => {
                    if (data.invoice_url) {
                        window.location.href = data.invoice_url;
                    } else {
                        window.location.reload();
                    }
                }, 1500);
            } else {
                alertBox.className = 'alert alert-danger py-2.5 px-3 fs-13 mb-3 border-0 bg-danger-subtle text-danger rounded-3';
                alertBox.innerHTML = '<i class="las la-exclamation-circle me-1 fs-15"></i> ' + (data.message || 'Failed to accept quote.');
            }
        })
        .catch(err => {
            btn.disabled = false;
            btnText.classList.remove('d-none');
            spinner.classList.add('d-none');
            alertBox.classList.remove('d-none');
            alertBox.className = 'alert alert-danger py-2.5 px-3 fs-13 mb-3 border-0 bg-danger-subtle text-danger rounded-3';
            alertBox.innerHTML = '<i class="las la-times-circle me-1 fs-15"></i> Network error communicating with server.';
        });
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    // Auto-open quote if query parameter exists
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const autoQuoteId = urlParams.get('new_quote') || urlParams.get('quote_id');
        if (autoQuoteId) {
            const quotesTabBtn = document.getElementById('quotes-tab');
            if (quotesTabBtn && window.bootstrap) {
                bootstrap.Tab.getOrCreateInstance(quotesTabBtn).show();
            }
            setTimeout(() => {
                openQuoteModal(parseInt(autoQuoteId, 10));
            }, 300);
        }
    });
    </script>

</body>
</html>
