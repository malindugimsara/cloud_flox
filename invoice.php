<?php
/**
 * CloudFlox Modern Invoice & Payment Gateway Portal
 * -------------------------------------------------------------------------
 * Dedicated CloudFlox-styled invoice view and payment gateway integration.
 * Connects PayHere (Visa/Master/Amex/USD), Card, and PayPal directly.
 */

if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    @session_start();
}

require_once __DIR__ . '/include/url.inc.php';
require_once __DIR__ . '/api/config.php';

$invoiceId = intval($_GET['id'] ?? 0);
$selectedGateway = strtolower(trim($_GET['gateway'] ?? 'payhere'));
$autoPayNow = !empty($_GET['paynow']);
$markPaid = !empty($_GET['paid']) || (isset($_GET['status']) && strtolower($_GET['status']) === 'paid');

// Database credentials
$dbHost = '127.0.0.1';
$dbUser = 'cfwhmcs_whmcs';
$dbPass = '+V)Rb%K3wFiK';
$dbName = 'cfwhmcs_whmcs3';

$mysqli = @new mysqli($dbHost, $dbUser, $dbPass, $dbName);
if (!$mysqli->connect_error) {
    $mysqli->set_charset('utf8mb4');
} else {
    $mysqli = null;
}

// Handle payment status auto-update if requested
if ($markPaid && $mysqli && $invoiceId > 0) {
    $now = date('Y-m-d H:i:s');
    $stmt = $mysqli->prepare("UPDATE tblinvoices SET status = 'Paid', datepaid = ?, updated_at = ? WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param('ssi', $now, $now, $invoiceId);
        $stmt->execute();
        $stmt->close();
    }
    // Update order
    $ordStmt = $mysqli->prepare("UPDATE tblorders SET status = 'Active' WHERE invoiceid = ?");
    if ($ordStmt) {
        $ordStmt->bind_param('i', $invoiceId);
        $ordStmt->execute();
        $ordStmt->close();
    }
}

// Invoice default placeholder values
$invoice = null;
$invoiceItems = [];
$client = null;

if ($mysqli && $invoiceId > 0) {
    // 1. Fetch Invoice
    $stmt = $mysqli->prepare("SELECT * FROM tblinvoices WHERE id = ? LIMIT 1");
    if ($stmt) {
        $stmt->bind_param('i', $invoiceId);
        $stmt->execute();
        $res = $stmt->get_result();
        $invoice = $res->fetch_assoc();
        $stmt->close();
    }

    // 2. Fetch Line Items
    if ($invoice) {
        $itemStmt = $mysqli->prepare("SELECT * FROM tblinvoiceitems WHERE invoiceid = ? ORDER BY id ASC");
        if ($itemStmt) {
            $itemStmt->bind_param('i', $invoiceId);
            $itemStmt->execute();
            $itemRes = $itemStmt->get_result();
            while ($row = $itemRes->fetch_assoc()) {
                $invoiceItems[] = $row;
            }
            $itemStmt->close();
        }

        // 3. Fetch Client
        $cliStmt = $mysqli->prepare("SELECT * FROM tblclients WHERE id = ? LIMIT 1");
        if ($cliStmt) {
            $cliStmt->bind_param('i', $invoice['userid']);
            $cliStmt->execute();
            $cliRes = $cliStmt->get_result();
            $client = $cliRes->fetch_assoc();
            $cliStmt->close();
        }
    }
}

// Fallback values if not found in database (e.g. simulation or test IDs)
if (!$invoice) {
    $invoice = [
        'id'            => $invoiceId ?: 100075,
        'invoicenum'    => (string)($invoiceId ?: 100075),
        'date'          => date('Y-m-d'),
        'duedate'       => date('Y-m-d', strtotime('+7 days')),
        'datepaid'      => $markPaid ? date('Y-m-d H:i:s') : '0000-00-00 00:00:00',
        'subtotal'      => '13.57',
        'credit'        => '0.00',
        'tax'           => '0.00',
        'tax2'          => '0.00',
        'total'         => '13.57',
        'status'        => $markPaid ? 'Paid' : 'Unpaid',
        'paymentmethod' => 'payheresdk'
    ];
    $invoiceItems = [
        [
            'description' => 'CloudFlox Domain Registration & DNS Management',
            'amount'      => '13.57'
        ]
    ];
    $client = [
        'firstname'   => $_SESSION['cf_client_name'] ?? 'CloudFlox Customer',
        'lastname'    => '',
        'companyname' => '',
        'email'       => $_SESSION['cf_client_email'] ?? 'customer@example.com',
        'address1'    => 'Main Street',
        'city'        => 'Colombo',
        'country'     => 'LK',
        'phonenumber' => '+94700000000'
    ];
}

$invoiceNumber = !empty($invoice['invoicenum']) ? $invoice['invoicenum'] : $invoice['id'];
$statusRaw = strtolower(trim($invoice['status'] ?? 'unpaid'));
$isPaid = ($statusRaw === 'paid');
$isCancelled = ($statusRaw === 'cancelled');

// Exchange Rates & Amounts
$totalUsd = (float)$invoice['total'];
$subtotalUsd = (float)$invoice['subtotal'];
$taxUsd = (float)($invoice['tax'] + $invoice['tax2']);
$amountUsd = number_format($totalUsd, 2, '.', '');
$amountUsdFormatted = number_format($totalUsd, 2);

// Client Display Details
$clientFullName = trim(($client['firstname'] ?? '') . ' ' . ($client['lastname'] ?? ''));
if (empty($clientFullName)) {
    $clientFullName = 'CloudFlox Customer';
}
$clientEmail = $client['email'] ?? '';
$clientPhone = $client['phonenumber'] ?? '';
$clientAddress = trim(($client['address1'] ?? '') . ' ' . ($client['city'] ?? '') . ' ' . ($client['country'] ?? ''));

// PayHere Secure Gateway Payload (USD)
$merchantId = defined('CF_PAYHERE_MERCHANT_ID') ? CF_PAYHERE_MERCHANT_ID : '230744';
$merchantSecret = defined('CF_PAYHERE_MERCHANT_SECRET') ? CF_PAYHERE_MERCHANT_SECRET : 'Mjc4NjU2NTM4MDM5OTA5NTg3NDAyNzAyMzk2Mzc1MTM1ODYwMTUy';
$payhereHash = strtoupper(md5(
    $merchantId .
    $invoice['id'] .
    $amountUsd .
    'USD' .
    strtoupper(md5($merchantSecret))
));

// Formulate absolute site URL for PayHere external redirects & webhooks
$proto = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || ($_SERVER['SERVER_PORT'] ?? 80) == 443) ? 'https' : 'http';
$hostHeader = $_SERVER['HTTP_HOST'] ?? 'localhost:8080';
if (substr($url, 0, 4) === 'http') {
    $fullSiteUrl = rtrim($url, '/') . '/';
} else {
    $fullSiteUrl = $proto . '://' . $hostHeader . '/' . ltrim($url, '/');
    $fullSiteUrl = rtrim($fullSiteUrl, '/') . '/';
}

$firstItemDesc = !empty($invoiceItems[0]['description']) ? $invoiceItems[0]['description'] : 'CloudFlox Cloud Services';

$payhereObj = [
    'sandbox'     => defined('CF_PAYHERE_SANDBOX') ? CF_PAYHERE_SANDBOX : false,
    'merchant_id' => $merchantId,
    'return_url'  => $fullSiteUrl . 'invoice?id=' . $invoice['id'] . '&paid=1',
    'cancel_url'  => $fullSiteUrl . 'invoice?id=' . $invoice['id'] . '&cancelled=1',
    'notify_url'  => $fullSiteUrl . 'api/payment-notify.php',
    'first_name'  => $client['firstname'] ?? 'CloudFlox',
    'last_name'   => $client['lastname'] ?? 'Customer',
    'email'       => $clientEmail ?: 'billing@cloudflox.com',
    'phone'       => $clientPhone ?: '+94700000000',
    'address'     => $client['address1'] ?? 'Main Street',
    'city'        => $client['city'] ?? 'Colombo',
    'country'     => $client['country'] ?? 'Sri Lanka',
    'order_id'    => (string)$invoice['id'],
    'items'       => $firstItemDesc,
    'currency'    => 'USD',
    'amount'      => $amountUsd,
    'hash'        => $payhereHash
];
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Invoice #<?php echo htmlspecialchars($invoiceNumber); ?> — CloudFlox Billing</title>

    <meta name="description" content="View and pay invoice #<?php echo htmlspecialchars($invoiceNumber); ?> for CloudFlox services.">
    <meta name="robots" content="noindex, nofollow">

    <!-- CSS Area -->
    <?php include 'include/css.inc.php'; ?>
    <link rel="stylesheet" href="<?php echo $url; ?>assets/css/cloudflox-redesign.css">

    <!-- Official PayHere JavaScript SDK -->
    <script type="text/javascript" src="https://www.payhere.lk/lib/payhere.js"></script>

    <style>
        /* Page & Container Layout */
        .cf-invoice-page-wrap {
            padding: 44px 0 90px;
            background: #F8FAFC;
            min-height: calc(100vh - 180px);
        }
        .cf-invoice-container {
            max-width: 1180px;
            margin: 0 auto;
            padding: 0 16px;
        }

        /* Top Actions & Navigation Bar */
        .cf-invoice-top-actions {
            margin-bottom: 26px;
            padding-bottom: 4px;
        }
        .cf-btn-back-portal {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #FFFFFF !important;
            color: #1E293B !important;
            border: 1px solid #CBD5E1 !important;
            padding: 8px 18px;
            border-radius: 999px;
            font-size: 13.5px;
            font-weight: 700;
            text-decoration: none !important;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05);
            transition: all 0.2s ease;
        }
        .cf-btn-back-portal:hover {
            background: #F1F5F9 !important;
            color: #0F172A !important;
            border-color: #94A3B8 !important;
            transform: translateX(-2px);
        }
        .cf-btn-back-portal i {
            color: #2563EB;
            font-size: 15px;
        }

        .cf-invoice-page-title {
            font-size: 22px;
            font-weight: 800;
            color: #0F172A !important;
            margin: 0;
            letter-spacing: -0.02em;
        }

        .cf-invoice-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 16px;
            border-radius: 999px;
            font-size: 12.5px;
            font-weight: 800;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }
        .cf-invoice-badge.unpaid {
            background: #FEF2F2 !important;
            color: #DC2626 !important;
            border: 1px solid #FCA5A5 !important;
        }
        .cf-invoice-badge.paid {
            background: #ECFDF5 !important;
            color: #059669 !important;
            border: 1px solid #6EE7B7 !important;
        }
        .cf-invoice-badge.cancelled {
            background: #F1F5F9 !important;
            color: #475569 !important;
            border: 1px solid #CBD5E1 !important;
        }

        .cf-btn-print {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            background: #FFFFFF !important;
            color: #1E293B !important;
            border: 1px solid #CBD5E1 !important;
            padding: 8px 18px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05);
            transition: all 0.2s ease;
        }
        .cf-btn-print:hover {
            background: #F1F5F9 !important;
            color: #0F172A !important;
            border-color: #94A3B8 !important;
        }
        .cf-btn-print i {
            color: #64748B;
            font-size: 15px;
        }

        /* Invoice Paper Sheet */
        .cf-invoice-sheet {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(15, 23, 42, 0.04);
            padding: 42px;
            position: relative;
        }
        .cf-invoice-logo {
            max-height: 48px;
            width: auto;
            display: block;
            margin-bottom: 14px;
        }

        /* Custom Text Hierarchy */
        .cf-text-label {
            color: #64748B !important;
            font-size: 11.5px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-bottom: 6px;
        }
        .cf-text-title {
            color: #0F172A !important;
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -0.02em;
        }
        .cf-text-sub {
            color: #334155 !important;
            font-size: 13.5px;
            line-height: 1.6;
        }
        .cf-text-muted {
            color: #64748B !important;
            font-size: 12px;
        }

        /* Table */
        .cf-invoice-table {
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
        }
        .cf-invoice-table th {
            background: #F8FAFC !important;
            color: #334155 !important;
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 13px 18px;
            border-top: 1px solid #E2E8F0;
            border-bottom: 1px solid #E2E8F0;
        }
        .cf-invoice-table td {
            padding: 18px;
            font-size: 14px;
            color: #0F172A !important;
            border-bottom: 1px solid #F1F5F9;
            vertical-align: middle;
        }

        /* Totals */
        .cf-invoice-total-line {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            font-size: 14px;
            color: #475569 !important;
        }
        .cf-invoice-total-line strong {
            color: #0F172A !important;
            font-weight: 700;
        }
        .cf-invoice-total-line.final {
            padding-top: 16px;
            margin-top: 8px;
            border-top: 2px solid #E2E8F0;
            font-size: 18px;
            font-weight: 800;
            color: #0F172A !important;
        }
        .cf-invoice-total-line.final .total-amount {
            color: #2563EB !important;
            font-size: 22px;
            font-weight: 900;
        }

        /* Right Payment Panel */
        .cf-pay-gateway-box {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(15, 23, 42, 0.04);
            padding: 28px 24px;
            position: sticky;
            top: 95px;
        }
        .cf-pay-title {
            color: #0F172A !important;
            font-size: 17px;
            font-weight: 800;
            margin-bottom: 4px;
        }
        .cf-pay-desc {
            color: #475569 !important;
            font-size: 13px;
            line-height: 1.5;
            margin-bottom: 18px;
        }

        .cf-gateway-nav-pills {
            display: flex;
            gap: 6px;
            background: #F1F5F9;
            padding: 5px;
            border-radius: 12px;
            margin-bottom: 20px;
        }
        .cf-gateway-nav-btn {
            flex: 1;
            padding: 10px 8px;
            font-size: 13px;
            font-weight: 700;
            color: #334155 !important;
            background: transparent;
            border: none;
            border-radius: 9px;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }
        .cf-gateway-nav-btn.active {
            background: #FFFFFF !important;
            color: #2563EB !important;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.08);
        }
        .cf-gateway-nav-btn:hover:not(.active) {
            color: #0F172A !important;
            background: rgba(255, 255, 255, 0.6);
        }

        .cf-amount-display-box {
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 12px;
            padding: 18px;
            margin-bottom: 18px;
            text-align: center;
        }
        .cf-amount-display-label {
            color: #475569 !important;
            font-size: 12.5px;
            font-weight: 600;
            margin-bottom: 4px;
        }
        .cf-amount-display-value {
            color: #0F172A !important;
            font-size: 26px;
            font-weight: 900;
            letter-spacing: -0.02em;
            line-height: 1.2;
        }
        .cf-amount-display-note {
            color: #64748B !important;
            font-size: 11.5px;
            margin-top: 4px;
            display: block;
        }

        .cf-btn-payhere-cta {
            background: linear-gradient(135deg, #059669 0%, #10B981 100%);
            color: #FFFFFF !important;
            border: none;
            border-radius: 11px;
            padding: 14px 22px;
            font-size: 15px;
            font-weight: 800;
            width: 100%;
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(5, 150, 105, 0.25);
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .cf-btn-payhere-cta:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(5, 150, 105, 0.35);
            color: #FFFFFF !important;
        }

        .cf-btn-card-cta {
            background: linear-gradient(135deg, #1D4ED8 0%, #2563EB 100%);
            color: #FFFFFF !important;
            border: none;
            border-radius: 11px;
            padding: 14px 22px;
            font-size: 15px;
            font-weight: 800;
            width: 100%;
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.25);
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .cf-btn-card-cta:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.35);
            color: #FFFFFF !important;
        }

        .cf-btn-paypal-cta {
            background: linear-gradient(135deg, #003087 0%, #0079C1 100%);
            color: #FFFFFF !important;
            border: none;
            border-radius: 11px;
            padding: 14px 22px;
            font-size: 15px;
            font-weight: 800;
            width: 100%;
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(0, 121, 193, 0.25);
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .cf-btn-paypal-cta:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 121, 193, 0.35);
            color: #FFFFFF !important;
        }

        .cf-trust-item {
            display: flex;
            align-items: center;
            gap: 9px;
            color: #334155 !important;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 7px;
        }

        .cf-paid-hero-card {
            background: linear-gradient(135deg, #F0FDF4 0%, #DCFCE7 100%);
            border: 1px solid #86EFAC;
            border-radius: 16px;
            padding: 28px;
            text-align: center;
            margin-bottom: 24px;
        }
        .cf-paid-icon-circle {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: #10B981;
            color: #FFFFFF;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            margin: 0 auto 14px;
            box-shadow: 0 4px 14px rgba(16, 185, 129, 0.3);
        }

        /* Responsive Breakpoints */
        @media (max-width: 991.98px) {
            .cf-invoice-page-wrap {
                padding: 28px 0 60px;
            }
            .cf-invoice-sheet {
                padding: 28px 20px;
            }
            .cf-pay-gateway-box {
                margin-top: 10px;
                position: static;
                top: 0;
            }
        }

        /* Print Styling */
        @media print {
            body {
                background: #FFFFFF !important;
            }
            .cf-invoice-page-wrap {
                padding: 0 !important;
                background: #FFFFFF !important;
            }
            .cf-pay-gateway-box,
            .cf-invoice-top-actions,
            header,
            footer,
            .preloader {
                display: none !important;
            }
            .cf-invoice-sheet {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
            }
            .col-lg-8 {
                width: 100% !important;
            }
        }

        /* Floating Close Button for PayHere Modal */
        .cf-payhere-floating-close-btn {
            position: fixed !important;
            top: 18px !important;
            right: 22px !important;
            z-index: 2147483647 !important;
            background: #0F172A !important;
            color: #FFFFFF !important;
            border: 1.5px solid rgba(255, 255, 255, 0.3) !important;
            border-radius: 999px !important;
            padding: 8px 18px !important;
            font-size: 13.5px !important;
            font-weight: 700 !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 7px !important;
            cursor: pointer !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5) !important;
            backdrop-filter: blur(10px) !important;
            -webkit-backdrop-filter: blur(10px) !important;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
            font-family: inherit !important;
            text-decoration: none !important;
            line-height: 1 !important;
        }
        .cf-payhere-floating-close-btn:hover {
            background: #DC2626 !important;
            border-color: #EF4444 !important;
            color: #FFFFFF !important;
            transform: translateY(-2px) scale(1.03) !important;
            box-shadow: 0 12px 30px rgba(220, 38, 38, 0.45) !important;
        }
        .cf-payhere-floating-close-btn:active {
            transform: scale(0.97) !important;
        }
        .cf-payhere-floating-close-btn i {
            font-size: 16px !important;
            font-weight: 800 !important;
        }
    </style>
</head>

<body class="pure-white-theme cf-invoice-page">

    <!-- Header Area -->
    <?php include 'include/header.inc.php'; ?>

    <main class="cf-invoice-page-wrap">
        <div class="cf-invoice-container">

            <!-- Top Actions & Navigation Bar -->
            <div class="cf-invoice-top-actions d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <a href="<?php echo $url; ?>clientarea" class="cf-btn-back-portal">
                        <i class="las la-arrow-left"></i> Client Portal
                    </a>
                    <span class="text-slate-400">|</span>
                    <h1 class="cf-invoice-page-title">
                        Invoice #<?php echo htmlspecialchars($invoiceNumber); ?>
                    </h1>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <?php if ($isPaid): ?>
                        <span class="cf-invoice-badge paid">
                            <i class="las la-check-circle"></i> Paid
                        </span>
                    <?php elseif ($isCancelled): ?>
                        <span class="cf-invoice-badge cancelled">
                            <i class="las la-times-circle"></i> Cancelled
                        </span>
                    <?php else: ?>
                        <span class="cf-invoice-badge unpaid">
                            <i class="las la-clock"></i> Unpaid
                        </span>
                    <?php endif; ?>

                    <button type="button" class="cf-btn-print" onclick="window.print()">
                        <i class="las la-print"></i> Print
                    </button>
                </div>
            </div>

            <!-- Main Layout Row -->
            <div class="row g-4">
                
                <!-- Left: Full Invoice Sheet -->
                <div class="<?php echo $isPaid ? 'col-12 col-lg-8 mx-auto' : 'col-12 col-lg-8'; ?>">
                    
                    <?php if ($isPaid): ?>
                        <!-- Paid Banner -->
                        <div class="cf-paid-hero-card">
                            <div class="cf-paid-icon-circle">
                                <i class="las la-check"></i>
                            </div>
                            <h3 class="fs-20 fw-bold text-slate-900 mb-1">Payment Received &amp; Invoice Settled</h3>
                            <p class="fs-13 cf-text-sub mb-3">
                                Thank you for your payment! Your transaction has been recorded and services have been automatically activated.
                            </p>
                            <div class="d-flex justify-content-center gap-2">
                                <a href="<?php echo $url; ?>clientarea" class="btn btn-primary btn-sm rounded-pill px-4 fw-bold">
                                    <i class="las la-server me-1"></i> Manage Services
                                </a>
                                <button type="button" class="cf-btn-print" onclick="window.print()">
                                    <i class="las la-file-pdf me-1"></i> Download PDF Receipt
                                </button>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="cf-invoice-sheet">
                        
                        <!-- Header: Company & Invoice Info -->
                        <div class="row align-items-start mb-4 pb-3 border-bottom">
                            <div class="col-sm-6 mb-3 mb-sm-0">
                                <img src="<?php echo $url; ?>assets/imgs/logos/cloud-flox-main.webp" alt="CloudFlox" class="cf-invoice-logo">
                                <div class="cf-text-sub">
                                    <strong class="text-slate-900 d-block fs-14">CloudFlox LLC</strong>
                                    2709 N Hayden Island Dr STE 444529<br>
                                    Portland, Oregon, 97217, USA<br>
                                    <a href="mailto:billing@cloudflox.com" class="text-primary text-decoration-none fw-semibold">billing@cloudflox.com</a>
                                </div>
                            </div>
                            <div class="col-sm-6 text-sm-end">
                                <div class="cf-text-label">Invoice Details</div>
                                <div class="cf-text-title mb-1">#INV-<?php echo htmlspecialchars($invoiceNumber); ?></div>
                                <div class="cf-text-sub mb-1">
                                    <strong class="text-slate-900">Invoice Date:</strong> <?php echo htmlspecialchars($invoice['date'] ?? date('Y-m-d')); ?>
                                </div>
                                <div class="cf-text-sub mb-2">
                                    <strong class="text-slate-900">Due Date:</strong> <?php echo htmlspecialchars($invoice['duedate'] ?? date('Y-m-d', strtotime('+7 days'))); ?>
                                </div>
                                <?php if ($isPaid && !empty($invoice['datepaid']) && $invoice['datepaid'] !== '0000-00-00 00:00:00'): ?>
                                    <div class="fs-13 text-emerald fw-bold">
                                        <i class="las la-check-circle me-1"></i> Paid On: <?php echo date('Y-m-d H:i', strtotime($invoice['datepaid'])); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Invoiced To Details -->
                        <div class="row mb-4 pb-3 border-bottom">
                            <div class="col-12 col-md-6">
                                <div class="cf-text-label">Invoiced To:</div>
                                <div class="fs-15 fw-extrabold text-slate-900"><?php echo htmlspecialchars($clientFullName); ?></div>
                                <?php if (!empty($client['companyname'])): ?>
                                    <div class="cf-text-sub"><?php echo htmlspecialchars($client['companyname']); ?></div>
                                <?php endif; ?>
                                <div class="cf-text-sub"><?php echo htmlspecialchars($clientAddress); ?></div>
                                <div class="cf-text-sub"><?php echo htmlspecialchars($clientEmail); ?></div>
                                <?php if (!empty($clientPhone)): ?>
                                    <div class="cf-text-sub"><?php echo htmlspecialchars($clientPhone); ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="col-12 col-md-6 mt-3 mt-md-0 text-md-end">
                                <div class="cf-text-label">Payment Method:</div>
                                <div class="fs-14 fw-bold text-slate-900">
                                    <?php 
                                    if ($selectedGateway === 'paypal') {
                                        echo '<i class="lab la-paypal text-info me-1"></i> PayPal (USD)';
                                    } elseif ($selectedGateway === 'card') {
                                        echo '<i class="las la-credit-card text-primary me-1"></i> Credit / Debit Card (USD)';
                                    } else {
                                        echo '<i class="las la-money-bill-wave text-emerald me-1"></i> PayHere (USD)';
                                    }
                                    ?>
                                </div>
                                <div class="cf-text-muted mt-1">256-Bit SSL Encrypted Processing</div>
                            </div>
                        </div>

                        <!-- Line Items Table -->
                        <div class="table-responsive mb-4">
                            <table class="table cf-invoice-table mb-0 w-100">
                                <thead>
                                    <tr>
                                        <th style="width: 70%;">Item Description</th>
                                        <th class="text-end" style="width: 30%;">Amount (USD)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($invoiceItems)): ?>
                                        <?php foreach ($invoiceItems as $item): ?>
                                            <tr>
                                                <td>
                                                    <div class="fw-bold text-slate-900"><?php echo htmlspecialchars($item['description']); ?></div>
                                                    <small class="cf-text-muted"><?php echo htmlspecialchars($item['notes'] ?? 'Standard Cloud Infrastructure Service'); ?></small>
                                                </td>
                                                <td class="text-end fw-extrabold text-slate-900 fs-15">
                                                    $<?php echo number_format((float)$item['amount'], 2); ?> USD
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td>
                                                <div class="fw-bold text-slate-900">CloudFlox Cloud Services</div>
                                            </td>
                                            <td class="text-end fw-extrabold text-slate-900 fs-15">
                                                $<?php echo number_format($totalUsd, 2); ?> USD
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Totals Breakdown -->
                        <div class="row justify-content-end">
                            <div class="col-12 col-sm-7 col-md-5">
                                <div class="cf-invoice-total-line">
                                    <span>Subtotal:</span>
                                    <strong>$<?php echo number_format($subtotalUsd, 2); ?> USD</strong>
                                </div>
                                <div class="cf-invoice-total-line">
                                    <span>Tax / Processing Fees:</span>
                                    <span>$<?php echo number_format($taxUsd, 2); ?> USD</span>
                                </div>
                                <div class="cf-invoice-total-line final">
                                    <span>Total Due:</span>
                                    <span class="total-amount">$<?php echo number_format($totalUsd, 2); ?> USD</span>
                                </div>
                            </div>
                        </div>

                        <!-- Footer Note -->
                        <div class="mt-4 pt-3 border-top cf-text-sub fs-12 text-center text-sm-start">
                            <i class="las la-info-circle text-primary me-1"></i>
                            All services are provisioned in real-time immediately after payment confirmation. For questions regarding this invoice, contact our 24/7 billing support team at <a href="mailto:billing@cloudflox.com" class="text-primary fw-semibold">billing@cloudflox.com</a>.
                        </div>

                    </div>
                </div>

                <!-- Right: Payment Action Panel (Visible only when unpaid) -->
                <?php if (!$isPaid): ?>
                    <div class="col-12 col-lg-4">
                        <div class="cf-pay-gateway-box">
                            <h4 class="cf-pay-title">
                                <i class="las la-lock text-emerald me-1.5"></i> Make Secure Payment
                            </h4>
                            <p class="cf-pay-desc">
                                Select your payment gateway to complete this invoice instantly.
                            </p>

                            <!-- Gateway Switcher Pills -->
                            <div class="cf-gateway-nav-pills" role="tablist">
                                <button type="button" class="cf-gateway-nav-btn <?php echo ($selectedGateway === 'payhere') ? 'active' : ''; ?>" id="tabBtnPayHere" onclick="switchGateway('payhere')">
                                    <i class="las la-bolt text-emerald fs-15"></i> PayHere
                                </button>
                                <button type="button" class="cf-gateway-nav-btn <?php echo ($selectedGateway === 'card') ? 'active' : ''; ?>" id="tabBtnCard" onclick="switchGateway('card')">
                                    <i class="las la-credit-card text-primary fs-15"></i> Card
                                </button>
                                <button type="button" class="cf-gateway-nav-btn <?php echo ($selectedGateway === 'paypal') ? 'active' : ''; ?>" id="tabBtnPayPal" onclick="switchGateway('paypal')">
                                    <i class="lab la-paypal text-info fs-15"></i> PayPal
                                </button>
                            </div>

                            <!-- 1. PayHere Gateway View -->
                            <div id="panePayHere" class="<?php echo ($selectedGateway === 'payhere') ? '' : 'd-none'; ?>">
                                <div class="cf-amount-display-box">
                                    <div class="cf-amount-display-label">Total Payable:</div>
                                    <div class="cf-amount-display-value">$<?php echo number_format($totalUsd, 2); ?> USD</div>
                                    <span class="cf-amount-display-note">Includes Visa, Master, Amex, Internet Banking &amp; Wallets</span>
                                </div>

                                <button type="button" class="cf-btn-payhere-cta mb-3" onclick="triggerPayHereModal()">
                                    <i class="las la-shield-alt fs-18"></i> Pay with PayHere ($<?php echo number_format($totalUsd, 2); ?> USD)
                                </button>

                                <div class="d-flex align-items-center justify-content-center gap-2 cf-text-muted fs-11.5 mb-2">
                                    <i class="las la-lock text-emerald"></i>
                                    <span>PCI-DSS Level 1 Certified • PayHere Gateway</span>
                                </div>
                            </div>

                            <!-- 2. Card Direct Gateway View -->
                            <div id="paneCard" class="<?php echo ($selectedGateway === 'card') ? '' : 'd-none'; ?>">
                                <div class="cf-amount-display-box">
                                    <div class="cf-amount-display-label">Card Payment Amount:</div>
                                    <div class="cf-amount-display-value">$<?php echo number_format($totalUsd, 2); ?> USD</div>
                                    <span class="cf-amount-display-note">Visa • MasterCard • American Express</span>
                                </div>

                                <button type="button" class="cf-btn-card-cta mb-3" onclick="triggerPayHereModal()">
                                    <i class="las la-credit-card fs-18"></i> Pay with Credit/Debit Card ($<?php echo number_format($totalUsd, 2); ?> USD)
                                </button>

                                <div class="d-flex align-items-center justify-content-center gap-2 cf-text-muted fs-11.5 mb-2">
                                    <i class="las la-shield-alt text-primary"></i>
                                    <span>Secure 3D-Secure 2.0 Card Authentication</span>
                                </div>
                            </div>

                            <!-- 3. PayPal Gateway View -->
                            <div id="panePayPal" class="<?php echo ($selectedGateway === 'paypal') ? '' : 'd-none'; ?>">
                                <div class="cf-amount-display-box">
                                    <div class="cf-amount-display-label">Total Payable in USD:</div>
                                    <div class="cf-amount-display-value">$<?php echo number_format($totalUsd, 2); ?> USD</div>
                                    <span class="cf-amount-display-note">Global PayPal Balance &amp; Credit Cards</span>
                                </div>

                                <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
                                    <input type="hidden" name="cmd" value="_xclick">
                                    <input type="hidden" name="business" value="<?php echo htmlspecialchars(defined('CF_PAYPAL_EMAIL') ? CF_PAYPAL_EMAIL : 'info@cloudflox.com'); ?>">
                                    <input type="hidden" name="item_name" value="CloudFlox Invoice #<?php echo htmlspecialchars($invoiceNumber); ?>">
                                    <input type="hidden" name="amount" value="<?php echo number_format($totalUsd, 2, '.', ''); ?>">
                                    <input type="hidden" name="currency_code" value="USD">
                                    <input type="hidden" name="custom" value="<?php echo htmlspecialchars($invoice['id']); ?>">
                                    <input type="hidden" name="return" value="<?php echo $url; ?>invoice?id=<?php echo $invoice['id']; ?>&paid=1">
                                    <input type="hidden" name="cancel_return" value="<?php echo $url; ?>invoice?id=<?php echo $invoice['id']; ?>&cancelled=1">
                                    <input type="hidden" name="notify_url" value="<?php echo $url; ?>api/payment-notify.php">
                                    <button type="submit" class="cf-btn-paypal-cta mb-3">
                                        <i class="lab la-paypal fs-18"></i> Pay with PayPal ($<?php echo number_format($totalUsd, 2); ?> USD)
                                    </button>
                                </form>

                                <div class="d-flex align-items-center justify-content-center gap-2 cf-text-muted fs-11.5 mb-2">
                                    <i class="las la-lock text-info"></i>
                                    <span>Official PayPal Buyer Protection</span>
                                </div>
                            </div>

                            <!-- Trust Badges Under Payment -->
                            <div class="mt-3 pt-3 border-top">
                                <div class="cf-trust-item">
                                    <i class="las la-bolt text-warning fs-15"></i>
                                    <span>Instant Automated Provisioning</span>
                                </div>
                                <div class="cf-trust-item">
                                    <i class="las la-shield-alt text-emerald fs-15"></i>
                                    <span>30-Day Money-Back Guarantee</span>
                                </div>
                                <div class="cf-trust-item mb-0">
                                    <i class="las la-headset text-primary fs-15"></i>
                                    <span>24/7 Priority Engineer Support</span>
                                </div>
                            </div>

                        </div>
                    </div>
                <?php endif; ?>

            </div>

        </div>
    </main>

    <!-- Footer Area -->
    <?php include 'include/footer.inc.php'; ?>

    <!-- JS Area -->
    <?php include 'include/js.inc.php'; ?>

    <script>
    (function() {
        // PayHere Payment Payload
        const payherePayment = <?php echo json_encode($payhereObj, JSON_UNESCAPED_SLASHES); ?>;
        const invoiceId = <?php echo json_encode($invoice['id']); ?>;
        const isAutoPay = <?php echo $autoPayNow ? 'true' : 'false'; ?>;
        const isAlreadyPaid = <?php echo $isPaid ? 'true' : 'false'; ?>;

        // Switch Gateway View
        window.switchGateway = function(gw) {
            document.querySelectorAll('.cf-gateway-nav-btn').forEach(btn => btn.classList.remove('active'));
            document.getElementById('panePayHere').classList.add('d-none');
            document.getElementById('paneCard').classList.add('d-none');
            document.getElementById('panePayPal').classList.add('d-none');

            if (gw === 'card') {
                document.getElementById('tabBtnCard').classList.add('active');
                document.getElementById('paneCard').classList.remove('d-none');
            } else if (gw === 'paypal') {
                document.getElementById('tabBtnPayPal').classList.add('active');
                document.getElementById('panePayPal').classList.remove('d-none');
            } else {
                document.getElementById('tabBtnPayHere').classList.add('active');
                document.getElementById('panePayHere').classList.remove('d-none');
            }
        };

        // Universal PayHere Modal Closer
        window.closePayHereModal = function() {
            // 1. Remove all PayHere container and modal elements from DOM
            const selectors = [
                '#ph-container',
                '#payhere-modal',
                '.ph-backdrop',
                '#ph-iframe',
                'iframe[src*="payhere.lk"]',
                'iframe[name*="payhere"]',
                '#cfPayhereCloseBtn'
            ];
            selectors.forEach(function(sel) {
                document.querySelectorAll(sel).forEach(function(el) {
                    try { el.remove(); } catch(e) {}
                });
            });

            // 2. Restore body scrolling
            document.body.style.overflow = '';
            document.documentElement.style.overflow = '';

            // 3. Clean paynow query param from URL so page refresh doesn't pop up again
            if (window.history && window.history.replaceState) {
                try {
                    const cleanUrl = window.location.pathname + window.location.search.replace(/([&?])paynow=[^&]*(&|$)/, '$1').replace(/[&?]$/, '') + window.location.hash;
                    window.history.replaceState(null, '', cleanUrl);
                } catch(e) {}
            }

            // 4. Fire onDismissed callback
            if (typeof payhere !== 'undefined' && typeof payhere.onDismissed === 'function') {
                try { payhere.onDismissed(); } catch(e) {}
            }
        };

        // Ensure floating close button exists while modal is active in DOM
        function syncPayHereCloseButton() {
            const hasModal = !!(document.getElementById('ph-container') || document.getElementById('payhere-modal') || document.getElementById('ph-iframe') || document.querySelector('iframe[src*="payhere.lk"]'));
            let floatBtn = document.getElementById('cfPayhereCloseBtn');

            if (hasModal) {
                if (!floatBtn) {
                    floatBtn = document.createElement('button');
                    floatBtn.type = 'button';
                    floatBtn.id = 'cfPayhereCloseBtn';
                    floatBtn.className = 'cf-payhere-floating-close-btn';
                    floatBtn.setAttribute('aria-label', 'Close Payment Window');
                    floatBtn.title = 'Close payment window and return to invoice (Esc)';
                    floatBtn.innerHTML = '<i class="las la-times"></i> <span>Close Payment</span>';
                    floatBtn.onclick = function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        window.closePayHereModal();
                    };
                    document.body.appendChild(floatBtn);
                }
            } else {
                if (floatBtn) {
                    try { floatBtn.remove(); } catch(e) {}
                }
            }
        }

        // Intercept postMessage from PayHere iframe (Fixes PayHere SDK JSON.parse crash on modern postMessage objects)
        window.addEventListener('message', function(event) {
            let msg = event.data;
            if (typeof msg === 'string') {
                try { msg = JSON.parse(msg); } catch(err) {}
            }

            if (msg && typeof msg === 'object') {
                // Type 4: Customer clicked the 'X' (cut) close button inside PayHere iframe
                if (msg.type === 4 || msg.type === '4' || msg.status === 'DISMISSED' || msg.status === 'CANCELLED' || msg.status === 'CLOSED' || msg.action === 'close' || msg.event === 'close') {
                    window.closePayHereModal();
                }
                // Type 2: Payment completed successfully
                else if (msg.type === 2 || msg.type === '2') {
                    if (typeof payhere !== 'undefined' && typeof payhere.onCompleted === 'function' && msg.data) {
                        payhere.onCompleted(msg.data);
                    }
                }
            } else if (msg === 'close' || msg === 'dismiss' || msg === 'CANCELLED') {
                window.closePayHereModal();
            }
        }, true);

        // Escape Key Listener to dismiss PayHere modal
        window.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' || event.key === 'Esc' || event.keyCode === 27) {
                const hasModal = !!(document.getElementById('ph-container') || document.getElementById('payhere-modal') || document.getElementById('ph-iframe') || document.querySelector('iframe[src*="payhere.lk"]'));
                if (hasModal) {
                    window.closePayHereModal();
                }
            }
        });

        // Watch DOM mutations to auto attach/remove floating close button & attach backdrop click
        if (typeof MutationObserver !== 'undefined') {
            const observer = new MutationObserver(function() {
                syncPayHereCloseButton();

                // Attach direct click to backdrop element if present
                const backdrop = document.querySelector('.ph-backdrop');
                if (backdrop && !backdrop.dataset.cfBound) {
                    backdrop.dataset.cfBound = '1';
                    backdrop.style.cursor = 'pointer';
                    backdrop.addEventListener('click', function() {
                        window.closePayHereModal();
                    });
                }
            });
            observer.observe(document.body, { childList: true, subtree: true });
        }

        // PayHere SDK Modal Trigger
        window.triggerPayHereModal = function() {
            if (typeof payhere === 'undefined') {
                alert('PayHere payment gateway is initializing. Please refresh in a moment.');
                return;
            }

            payhere.onCompleted = function(orderId) {
                // Instantly notify local server to mark invoice as paid
                fetch('api/payment-notify.php?action=verify&invoice_id=' + encodeURIComponent(orderId))
                    .finally(() => {
                        window.location.href = 'invoice?id=' + encodeURIComponent(orderId) + '&status=paid';
                    });
            };

            payhere.onDismissed = function() {
                console.log('PayHere popup closed by customer.');
                window.closePayHereModal();
            };

            payhere.onError = function(error) {
                console.error('PayHere Error:', error);
                alert('PayHere Notice: ' + error);
            };

            // Start checkout modal
            payhere.startPayment(payherePayment);

            // Sync floating button
            setTimeout(syncPayHereCloseButton, 100);
            setTimeout(syncPayHereCloseButton, 500);
        };

        // Auto trigger PayHere if redirected directly from checkout with paynow=1
        if (isAutoPay && !isAlreadyPaid) {
            window.addEventListener('load', function() {
                setTimeout(function() {
                    triggerPayHereModal();
                }, 500);
            });
        }
    })();
    </script>
</body>
</html>
