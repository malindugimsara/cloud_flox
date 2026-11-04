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
$markPaid = !empty($_GET['paid']);

// Database credentials
$dbHost = CF_DB_HOST;
$dbUser = CF_DB_USER;
$dbPass = CF_DB_PASS;
$dbName = CF_DB_NAME;

$mysqli = @new mysqli($dbHost, $dbUser, $dbPass, $dbName, (int)CF_DB_PORT);
if (!$mysqli->connect_error) {
    $mysqli->set_charset('utf8mb4');
} else {
    $mysqli = null;
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

        // 4. Fetch Transactions from tblaccounts
        $transactions = [];
        $txStmt = $mysqli->prepare("SELECT date, gateway, transid, amountin FROM tblaccounts WHERE invoiceid = ? ORDER BY id ASC");
        if ($txStmt) {
            $txStmt->bind_param('i', $invoiceId);
            $txStmt->execute();
            $txRes = $txStmt->get_result();
            while ($tRow = $txRes->fetch_assoc()) {
                $transactions[] = $tRow;
            }
            $txStmt->close();
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

// Check if this invoice contains domain or hosting services
$hasDomainItem = false;
$hasHostingItem = false;
foreach ($invoiceItems as $it) {
    $type = strtolower($it['type'] ?? '');
    $desc = strtolower($it['description'] ?? '');

    if (in_array($type, ['domain', 'domainregister', 'domaintransfer', 'domainrenew']) || 
        str_contains($desc, 'domain registration') || 
        str_contains($desc, 'domain transfer') ||
        (str_contains($desc, 'domain') && !str_contains($desc, 'hosting') && !str_contains($desc, 'plan'))) {
        $hasDomainItem = true;
    }

    if (in_array($type, ['hosting', 'service']) || 
        str_contains($desc, 'hosting') || 
        str_contains($desc, 'cpanel') || 
        str_contains($desc, 'plan') || 
        str_contains($desc, 'server') || 
        str_contains($desc, 'nvme') ||
        str_contains($desc, 'cloud')) {
        $hasHostingItem = true;
    }
}

// Exchange Rates & Amounts
$totalUsd = (float)$invoice['total'];
$subtotalUsd = (float)$invoice['subtotal'];
$taxUsd = (float)($invoice['tax'] + $invoice['tax2']);
$creditUsd = (float)($invoice['credit'] ?? 0);
$amountUsd = number_format($totalUsd, 2, '.', '');
$amountUsdFormatted = number_format($totalUsd, 2);

// Calculate Paid Amount & Balance Due
$amountPaid = 0.00;
if (!empty($transactions)) {
    foreach ($transactions as $tx) {
        $txVal = (float)($tx['amountin'] ?? 0);
        if ($totalUsd > 0 && $txVal > ($totalUsd * 50)) {
            $txVal = $totalUsd;
        }
        $amountPaid += $txVal;
    }
}
if ($isPaid && $amountPaid <= 0) {
    $amountPaid = $totalUsd;
    if (empty($transactions)) {
        $transactions[] = [
            'date'     => (!empty($invoice['datepaid']) && $invoice['datepaid'] !== '0000-00-00 00:00:00') ? $invoice['datepaid'] : date('Y-m-d H:i:s'),
            'gateway'  => ($selectedGateway === 'paypal') ? 'PayPal' : 'Pay Now (Visa, Master, Amex)',
            'transid'  => (string)($invoice['id'] * 3182 + 10429),
            'amountin' => $totalUsd
        ];
    }
}
$balanceDueUsd = max(0.00, $totalUsd - $amountPaid);

// Reference hash code matching WHMCS PDF footer specification
$invoiceRefCode = 'CFX-INV-' . strtoupper(substr(hash('sha256', 'INV|' . $invoice['id'] . '|' . ($invoice['date'] ?? '') . '|' . ($invoice['total'] ?? '')), 0, 20));
$invoiceGeneratedTime = gmdate('d-m-Y H:i') . ' UTC';

// Country mapping
$countryNames = [
    'LK' => 'Sri Lanka',
    'US' => 'United States',
    'GB' => 'United Kingdom',
    'AU' => 'Australia',
    'CA' => 'Canada',
    'SG' => 'Singapore',
    'AE' => 'United Arab Emirates',
    'IN' => 'India',
    'DE' => 'Germany',
    'FR' => 'France',
    'JP' => 'Japan',
    'MY' => 'Malaysia',
    'NZ' => 'New Zealand'
];
$clientCountryCode = strtoupper(trim($client['country'] ?? 'LK'));
$clientCountryName = $countryNames[$clientCountryCode] ?? $clientCountryCode;

// Formatted Dates matching WHMCS quotation standard
$invoiceDateFormatted = !empty($invoice['date']) ? date('l, F jS, Y', strtotime($invoice['date'])) : date('l, F jS, Y');
$dueDateFormatted = !empty($invoice['duedate']) ? date('l, F jS, Y', strtotime($invoice['duedate'])) : date('l, F jS, Y', strtotime('+7 days'));

// Client Display Details
$person = trim(($client['firstname'] ?? '') . ' ' . ($client['lastname'] ?? ''));
$clientFullName = !empty($person) ? $person : 'CloudFlox Customer';
$clientCity = $client['city'] ?? '';
$clientState = $client['state'] ?? '';
$clientPostcode = $client['postcode'] ?? '';
$cityParts = array_filter([$clientCity, $clientState, $clientPostcode]);
$cityLine = !empty($cityParts) ? implode(', ', $cityParts) : '';
$clientEmail = $client['email'] ?? '';
$clientPhone = $client['phonenumber'] ?? '';
$clientAddress = trim(($client['address1'] ?? '') . ' ' . ($client['city'] ?? '') . ' ' . ($client['country'] ?? ''));

// PayHere Secure Gateway Payload (USD)
$merchantId = defined('CF_PAYHERE_MERCHANT_ID') ? CF_PAYHERE_MERCHANT_ID : '';
$merchantSecret = defined('CF_PAYHERE_MERCHANT_SECRET') ? CF_PAYHERE_MERCHANT_SECRET : '';
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

/**
 * Returns detailed hosting specifications matching CloudFlox standards and Image 1.
 */
function getHostingPlanSpecificationsText($planString) {
    $t = strtolower($planString);

    if (strpos($t, 'mini') !== false) {
        return "Hosting Specifications\n\n"
             . "- CPU: 1 Core (Shared Resources)\n"
             . "- RAM: 1 GB Guaranteed RAM\n"
             . "- I/O Rate: 5 MB/s\n"
             . "- Disk Space: 5 GB Pure NVMe Gen4 SSD\n"
             . "- Bandwidth: 100 GB Premium Bandwidth\n"
             . "- Hosted Websites: 1 Website\n"
             . "- Email Accounts: 5 Webmail Accounts\n"
             . "- MySQL Databases: 2 Databases\n"
             . "- Subdomains: 5 Subdomains\n"
             . "- FTP Accounts: 2 Accounts\n"
             . "- SSL Certificate: Free AutoSSL\n"
             . "- Security: CloudLinux CageFS & Imunify360\n"
             . "- Control Panel: cPanel\n"
             . "- PHP: Multiple PHP Versions (7.4 - 8.3)\n"
             . "- Cron Jobs: Supported\n"
             . "- Backups: Weekly Automated Backups\n"
             . "- Support: Standard 24/7 Ticket Support\n\n"
             . "Hosting Type: High-Speed NVMe Shared Hosting\n"
             . "Access: cPanel User Access Only (No Root Access)";
    }

    if (strpos($t, 'basic') !== false) {
        return "Hosting Specifications\n\n"
             . "- CPU: 1.5 Cores (Shared Resources)\n"
             . "- RAM: 2 GB Guaranteed RAM\n"
             . "- I/O Rate: 10 MB/s\n"
             . "- Disk Space: 15 GB Pure NVMe Gen4 SSD\n"
             . "- Bandwidth: 500 GB Premium Bandwidth\n"
             . "- Hosted Websites: 3 Websites\n"
             . "- Email Accounts: 25 Webmail Accounts\n"
             . "- MySQL Databases: 10 Databases\n"
             . "- Subdomains: 20 Subdomains\n"
             . "- FTP Accounts: 5 Accounts\n"
             . "- SSL Certificate: Free AutoSSL\n"
             . "- Security: CloudLinux CageFS & Imunify360\n"
             . "- Control Panel: cPanel\n"
             . "- PHP: Multiple PHP Versions (7.4 - 8.3)\n"
             . "- Cron Jobs: Supported\n"
             . "- Backups: Daily Automated Backups\n"
             . "- Support: 24/7 Helpdesk & Ticket Support\n\n"
             . "Hosting Type: High-Speed NVMe Shared Hosting\n"
             . "Access: cPanel User Access Only (No Root Access)";
    }

    if (strpos($t, 'plus') !== false) {
        return "Hosting Specifications\n\n"
             . "- CPU: 2 Cores (Shared Resources)\n"
             . "- RAM: 3 GB Guaranteed RAM\n"
             . "- I/O Rate: 15 MB/s\n"
             . "- Disk Space: 30 GB Pure NVMe Gen4 SSD\n"
             . "- Bandwidth: 1 TB Premium Bandwidth\n"
             . "- Hosted Websites: 10 Websites\n"
             . "- Email Accounts: 50 Webmail Accounts\n"
             . "- MySQL Databases: 25 Databases\n"
             . "- Subdomains: 50 Subdomains\n"
             . "- FTP Accounts: Unlimited\n"
             . "- SSL Certificate: Free AutoSSL\n"
             . "- Security: CloudLinux CageFS & Imunify360\n"
             . "- Control Panel: cPanel\n"
             . "- PHP: Multiple PHP Versions (7.4 - 8.3)\n"
             . "- Cron Jobs: Supported\n"
             . "- Backups: Daily Automated Backups\n"
             . "- Support: Priority 24/7 Ticket Support\n\n"
             . "Hosting Type: High-Speed NVMe Shared Hosting\n"
             . "Access: cPanel User Access Only (No Root Access)";
    }

    if (strpos($t, 'starter') !== false) {
        return "Hosting Specifications\n\n"
             . "- CPU: 1 vCPU Dedicated\n"
             . "- RAM: 2 GB Dedicated RAM\n"
             . "- I/O Rate: 15 MB/s\n"
             . "- Disk Space: 20 GB Pure NVMe Gen4 SSD\n"
             . "- Bandwidth: 1 TB Premium Bandwidth\n"
             . "- Hosted Websites: 1 Website\n"
             . "- Email Accounts: 25 Professional Email Accounts\n"
             . "- MySQL Databases: 10 Databases\n"
             . "- Subdomains: 25 Subdomains\n"
             . "- FTP Accounts: Unlimited\n"
             . "- SSL Certificate: Free AutoSSL & HTTP/3\n"
             . "- Security: Imunify360 Proactive Defense & CageFS\n"
             . "- Web Server: LiteSpeed Enterprise Web Server\n"
             . "- Control Panel: cPanel\n"
             . "- PHP: Multiple PHP Versions Supported\n"
             . "- Cron Jobs: Supported\n"
             . "- Backups: Free Daily JetBackup Snapshots\n"
             . "- Support: Priority 24/7 Tech Support\n\n"
             . "Hosting Type: Business NVMe Cloud Infrastructure\n"
             . "Access: cPanel User Access Only (No Root Access)";
    }

    if (strpos($t, 'growth') !== false || strpos($t, 'business plan') !== false) {
        return "Hosting Specifications\n\n"
             . "- CPU: 2 vCPU Dedicated\n"
             . "- RAM: 4 GB Dedicated RAM\n"
             . "- I/O Rate: 20 MB/s\n"
             . "- Disk Space: 40 GB Pure NVMe Gen4 SSD\n"
             . "- Bandwidth: 2 TB Premium Bandwidth\n"
             . "- Hosted Websites: 5 Websites\n"
             . "- Email Accounts: 50 Professional Email Accounts\n"
             . "- MySQL Databases: 25 Databases\n"
             . "- Subdomains: Unlimited\n"
             . "- FTP Accounts: Unlimited\n"
             . "- SSL Certificate: Free AutoSSL & HTTP/3\n"
             . "- Security: Imunify360 Proactive Defense & CageFS\n"
             . "- Web Server: LiteSpeed Enterprise Web Server\n"
             . "- Control Panel: cPanel\n"
             . "- PHP: Multiple PHP Versions Supported\n"
             . "- Cron Jobs: Supported\n"
             . "- Backups: Free Daily JetBackup Snapshots\n"
             . "- Support: Priority 24/7 Tech Support\n\n"
             . "Hosting Type: Business NVMe Cloud Infrastructure\n"
             . "Access: cPanel User Access Only (No Root Access)";
    }

    if (strpos($t, 'pro') !== false) {
        return "Hosting Specifications\n\n"
             . "- CPU: 4 vCPU Dedicated\n"
             . "- RAM: 8 GB Dedicated RAM\n"
             . "- I/O Rate: 25 MB/s\n"
             . "- Disk Space: 100 GB Pure NVMe Gen4 SSD\n"
             . "- Bandwidth: 5 TB Premium Bandwidth\n"
             . "- Hosted Websites: 15 Websites\n"
             . "- Email Accounts: 100 Professional Email Accounts\n"
             . "- MySQL Databases: 50 Databases\n"
             . "- Subdomains: Unlimited\n"
             . "- FTP Accounts: Unlimited\n"
             . "- Dedicated IP: 1 Free Dedicated IPv4\n"
             . "- SSL Certificate: Free AutoSSL & HTTP/3\n"
             . "- Security: Imunify360 Proactive Defense & CageFS\n"
             . "- Web Server: LiteSpeed Enterprise Web Server\n"
             . "- Control Panel: cPanel\n"
             . "- PHP: Multiple PHP Versions Supported\n"
             . "- Cron Jobs: Supported\n"
             . "- Backups: Free Daily JetBackup Snapshots\n"
             . "- Support: VIP 24/7 Tech Support\n\n"
             . "Hosting Type: High-Scale Business Cloud Infrastructure\n"
             . "Access: cPanel User Access Only (No Root Access)";
    }

    if (strpos($t, 'enterprise') !== false || strpos($t, 'managed') !== false) {
        return "Hosting Specifications\n\n"
             . "- CPU: Up to 4 Cores (Shared Resources)\n"
             . "- RAM: Up to 8 GB\n"
             . "- I/O Rate: 10 MB/s\n"
             . "- Disk Space: 300 GB NVMe SSD\n"
             . "- Bandwidth: 1 TB Premium\n"
             . "- Add-on Domains: 100\n"
             . "- Email Accounts: 100\n"
             . "- MySQL Databases: 50\n"
             . "- Subdomains: 100\n"
             . "- FTP Accounts: Unlimited\n"
             . "- SSL Certificate: Free SSL\n"
             . "- Security: Imunify360\n"
             . "- Control Panel: cPanel\n"
             . "- PHP: Multiple PHP Versions Supported\n"
             . "- Cron Jobs: Supported (Subject to Resource Limits)\n"
             . "- Migration: Free One-Time Migration\n"
             . "- Support: Managed Hosting Support\n\n"
             . "Hosting Type: Managed Shared Server Infrastructure\n"
             . "Access: cPanel User Access Only (No Root Access)";
    }

    // Default Fallback for any Cloud Hosting item
    return "Hosting Specifications\n\n"
         . "- Disk Space: High-Speed Pure NVMe Gen4 SSD Storage\n"
         . "- Bandwidth: Premium High-Speed Connectivity\n"
         . "- SSL Certificate: Free AutoSSL Certificate\n"
         . "- Security: CloudLinux CageFS & Imunify360 Proactive Defense\n"
         . "- Control Panel: cPanel & Webmail Access\n"
         . "- PHP: Multiple PHP Versions Supported\n"
         . "- Support: 24/7 Technical Support\n\n"
         . "Hosting Type: High-Speed NVMe Cloud Infrastructure\n"
         . "Access: cPanel User Access Only (No Root Access)";
}

/**
 * Splits raw description into first line (title) and specifications/details.
 * Enriches hosting items with specifications if not already present.
 */
function parseInvoiceItemDescription($rawDesc, $itemType = '', $notes = '') {
    $rawDesc = trim((string)$rawDesc);
    $lines = explode("\n", str_replace("\r", "", $rawDesc));
    $firstLine = trim($lines[0] ?? '');
    
    $remainingLines = array_slice($lines, 1);
    $specsText = trim(implode("\n", $remainingLines));

    if (empty($specsText) && !empty($notes) && $notes !== 'Standard Cloud Infrastructure Service') {
        $specsText = trim($notes);
    }

    // If description already has Hosting Specifications, return as is
    if (strpos($rawDesc, 'Hosting Specifications') !== false) {
        return [
            'title' => $firstLine ?: 'CloudFlox Cloud Infrastructure Service',
            'specs' => $specsText
        ];
    }

    // Check if this item is a hosting plan
    $checkText = strtolower($firstLine . ' ' . $itemType . ' ' . $rawDesc);
    $isHosting = (
        strtolower($itemType) === 'hosting' || 
        str_contains($checkText, 'hosting') || 
        str_contains($checkText, 'mini plan') || 
        str_contains($checkText, 'basic plan') || 
        str_contains($checkText, 'plus plan') || 
        str_contains($checkText, 'starter') || 
        str_contains($checkText, 'business') || 
        str_contains($checkText, 'growth') || 
        str_contains($checkText, 'enterprise') || 
        str_contains($checkText, 'professional') || 
        str_contains($checkText, 'cpanel')
    );

    if ($isHosting) {
        $genSpecs = getHostingPlanSpecificationsText($firstLine);
        if (!empty($genSpecs)) {
            $specsText = !empty($specsText) ? ($specsText . "\n\n" . $genSpecs) : $genSpecs;
        }
    }

    return [
        'title' => $firstLine ?: 'CloudFlox Cloud Infrastructure Service',
        'specs' => $specsText
    ];
}
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
            padding-left: clamp(16px, 3vw, 32px);
            padding-right: clamp(16px, 3vw, 32px);
            box-sizing: border-box;
        }
        .cf-invoice-container {
            max-width: 1180px;
            margin: 0 auto;
            padding-left: clamp(16px, 2.5vw, 28px);
            padding-right: clamp(16px, 2.5vw, 28px);
            box-sizing: border-box;
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

        /* Invoice Paper Sheet - Matching Official Quotation & Invoice Standard */
        .cf-invoice-sheet {
            background: #FFFFFF !important;
            border: 1px solid #E2E8F0 !important;
            border-radius: 12px !important;
            box-shadow: 0 4px 28px rgba(15, 23, 42, 0.05) !important;
            padding: 24px 30px 18px !important;
            color: #0F172A !important;
            position: relative;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            display: flex !important;
            flex-direction: column !important;
            min-height: 820px !important;
            box-sizing: border-box !important;
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }

        .cf-pdf-header-row {
            margin-bottom: 2px !important;
        }
        .cf-invoice-logo {
            max-height: 38px !important;
            width: auto !important;
            display: block;
            margin-bottom: 3px !important;
        }
        .cf-pdf-tagline {
            font-size: 10px !important;
            color: #64748B;
            letter-spacing: 0.8px;
            font-weight: 500;
        }
        .cf-pdf-company-name {
            font-size: 13.5px !important;
            font-weight: 700;
            color: #0F172A;
            display: block;
            margin-bottom: 2px;
        }
        .cf-pdf-company-address {
            font-size: 11px !important;
            color: #475569;
            line-height: 1.4 !important;
        }
        .cf-pdf-domains {
            color: #64748B;
            font-size: 10.5px !important;
        }
        .cf-pdf-header-divider {
            height: 1.5px !important;
            background: #EB7520 !important;
            margin: 10px 0 12px !important;
            width: 100%;
        }

        /* 2-Column Meta & Bill To */
        .cf-pdf-meta-row {
            margin-bottom: 6px !important;
        }
        .cf-pdf-main-heading {
            color: #0F2C5B !important;
            font-size: 14.5px !important;
            font-weight: 700 !important;
            letter-spacing: 0.5px !important;
            text-transform: uppercase !important;
            margin-bottom: 6px !important;
        }
        .cf-pdf-sub-heading {
            color: #0F2C5B !important;
            font-size: 13.5px !important;
            font-weight: 700 !important;
            letter-spacing: 0.5px !important;
            text-transform: uppercase !important;
            margin-bottom: 6px !important;
        }
        .cf-pdf-meta-table {
            width: 100%;
            max-width: 320px;
            border-collapse: collapse;
            font-size: 11.5px !important;
        }
        .cf-pdf-meta-table td {
            padding: 2px 0 !important;
            border: none !important;
        }
        .cf-pdf-meta-label {
            color: #64748B !important;
            font-weight: 500 !important;
            width: 85px !important;
        }
        .cf-pdf-meta-val {
            color: #0F172A !important;
            font-weight: 600 !important;
        }
        .cf-pdf-status-paid {
            color: #15803D !important;
            font-weight: 700 !important;
            font-size: 12px !important;
            text-transform: uppercase;
        }
        .cf-pdf-status-unpaid {
            color: #DC2626 !important;
            font-weight: 700 !important;
            font-size: 12px !important;
            text-transform: uppercase;
        }
        .cf-pdf-status-cancelled {
            color: #64748B !important;
            font-weight: 700 !important;
            font-size: 12px !important;
            text-transform: uppercase;
        }

        .cf-pdf-billto-details {
            font-size: 11.5px !important;
            color: #334155;
            line-height: 1.45 !important;
        }
        .cf-pdf-billto-company {
            font-weight: 600;
            color: #0F172A;
            font-size: 12.5px !important;
        }
        .cf-pdf-billto-name {
            font-weight: 600;
            color: #0F172A;
        }

        .cf-pdf-mid-divider {
            height: 1px !important;
            background: #DFE5EB !important;
            margin: 10px 0 8px !important;
            width: 100% !important;
        }

        /* Items Table */
        .cf-pdf-items-wrap {
            margin-top: 8px !important;
            margin-bottom: 6px !important;
        }
        .cf-pdf-items-table {
            width: 100% !important;
            border-collapse: collapse !important;
        }
        .cf-pdf-items-table thead tr {
            background: #0F2C5B !important;
            color: #FFFFFF !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        .cf-pdf-items-table th {
            padding: 6px 12px !important;
            font-size: 11.5px !important;
            font-weight: 600 !important;
            color: #FFFFFF !important;
            border: none !important;
        }
        .cf-pdf-items-table td {
            padding: 6px 12px !important;
            border-bottom: 1px solid #F1F5F9 !important;
            vertical-align: top !important;
        }
        .cf-pdf-item-title {
            font-weight: 600 !important;
            color: #0F172A !important;
            font-size: 12px !important;
            line-height: 1.35 !important;
        }
        .cf-pdf-item-specs {
            font-size: 9.5px !important;
            color: #0F2C5B !important;
            line-height: 1.32 !important;
            margin-top: 4px !important;
            white-space: pre-line !important;
            font-weight: 400 !important;
        }
        .cf-pdf-item-amount {
            font-weight: 600 !important;
            color: #0F172A !important;
            font-size: 12px !important;
            white-space: nowrap !important;
        }

        /* Totals */
        .cf-pdf-totals-wrap {
            margin-top: 4px !important;
            margin-bottom: 8px !important;
        }
        .cf-pdf-totals-table {
            width: 100% !important;
            max-width: 320px !important;
            border-collapse: collapse !important;
            font-size: 11.5px !important;
        }
        .cf-pdf-totals-table td {
            padding: 2.5px 8px !important;
            border: none !important;
        }
        .cf-pdf-tot-label {
            text-align: right !important;
            color: #475569 !important;
            font-weight: 500 !important;
        }
        .cf-pdf-tot-val {
            text-align: right !important;
            color: #0F172A !important;
            font-weight: 600 !important;
            width: 44% !important;
        }
        .cf-pdf-total-orange-bar {
            width: 100% !important;
            background: #EB7520 !important;
            color: #FFFFFF !important;
            font-weight: 700 !important;
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            padding: 4px 12px !important;
            margin: 4px 0 !important;
            box-sizing: border-box !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        .cf-pdf-total-orange-label {
            text-align: left !important;
            color: #FFFFFF !important;
            font-size: 12px !important;
            letter-spacing: 0.5px !important;
            padding: 0 !important;
            font-weight: 700 !important;
        }
        .cf-pdf-total-orange-val {
            text-align: right !important;
            color: #FFFFFF !important;
            font-size: 12px !important;
            padding: 0 !important;
            font-weight: 700 !important;
        }

        /* Transactions */
        .cf-pdf-tx-section {
            margin-top: 6px !important;
            margin-bottom: 8px !important;
        }
        .cf-pdf-section-title {
            color: #0F2C5B !important;
            font-size: 11.5px !important;
            font-weight: 700 !important;
            margin-bottom: 4px !important;
        }
        .cf-pdf-tx-table {
            width: 100% !important;
            border-collapse: collapse !important;
            font-size: 11px !important;
        }
        .cf-pdf-tx-table thead tr {
            background: #EDF1F6 !important;
            color: #0F2C5B !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        .cf-pdf-tx-table th {
            padding: 4px 8px !important;
            font-weight: 600 !important;
            color: #0F2C5B !important;
            border: none !important;
            font-size: 10.5px !important;
        }
        .cf-pdf-tx-table td {
            padding: 4px 8px !important;
            border-bottom: 1px solid #F1F5F9 !important;
            color: #334155 !important;
            font-weight: 400 !important;
        }
        .cf-pdf-mono {
            font-family: SFMono-Regular, Menlo, Monaco, Consolas, monospace !important;
            font-size: 10.5px !important;
        }

        /* Notes */
        .cf-pdf-notes-section {
            margin-top: 6px !important;
            margin-bottom: 8px !important;
        }
        .cf-pdf-notes-title {
            color: #0F2C5B !important;
            font-size: 11.5px !important;
            font-weight: 700 !important;
            margin-bottom: 3px !important;
            letter-spacing: 0.5px !important;
        }
        .cf-pdf-notes-content {
            font-size: 10.5px !important;
            color: #475569 !important;
            line-height: 1.4 !important;
            white-space: pre-line !important;
            font-weight: 400 !important;
        }

        /* Footer - Image 2 pixel-perfect styling pinned to bottom */
        .cf-pdf-footer-wrap {
            margin-top: auto !important;
            padding-top: 8px !important;
            border-top: 1px solid #DFE5EB !important;
            width: 100% !important;
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }
        .cf-pdf-footer-contacts {
            margin-bottom: 4px !important;
        }
        .cf-pdf-footer-head {
            color: #0F2C5B !important;
            font-size: 10px !important;
            font-weight: 700 !important;
            letter-spacing: 0.5px !important;
            display: block !important;
            margin-bottom: 2px !important;
        }
        .cf-pdf-footer-link {
            color: #546379 !important;
            text-decoration: none !important;
            font-size: 10px !important;
            font-weight: 400 !important;
        }
        .cf-pdf-footer-orange-bar {
            height: 2px !important;
            background: #EB7520 !important;
            width: 100% !important;
            margin: 6px 0 5px !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        .cf-pdf-footer-meta {
            text-align: center !important;
            font-size: 9px !important;
            color: #546379 !important;
            font-weight: 400 !important;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
        }

        /* Print Media Styles */
        @media print {
            @page {
                size: A4 portrait;
                margin: 6mm 8mm;
            }
            html, body {
                height: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                overflow: hidden !important;
                background: #FFFFFF !important;
            }
            body * {
                visibility: hidden !important;
            }
            .cf-invoice-sheet,
            .cf-invoice-sheet * {
                visibility: visible !important;
            }
            .cf-invoice-sheet {
                position: relative !important;
                left: 0 !important;
                top: 0 !important;
                width: 100% !important;
                height: 100% !important;
                min-height: 100% !important;
                max-height: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                border: none !important;
                box-shadow: none !important;
                display: flex !important;
                flex-direction: column !important;
                justify-content: space-between !important;
                box-sizing: border-box !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
                page-break-after: avoid !important;
                break-after: avoid !important;
            }
            .cf-pdf-footer-wrap {
                margin-top: auto !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
            .cf-invoice-page-wrap,
            .cf-header-area,
            footer,
            .cf-invoice-top-actions,
            .cf-paid-hero-card,
            .cf-pay-gateway-box,
            .preloader {
                display: none !important;
            }
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

                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="cf-btn-print" onclick="downloadInvoicePDF(this)" title="Save PDF to your computer">
                            <i class="las la-file-pdf"></i> PDF
                        </button>
                        
                    </div>
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
                            <div class="d-flex justify-content-center align-items-center gap-2 flex-wrap mb-4" style="margin-bottom: 24px !important;">
                                <?php if ($hasHostingItem): ?>
                                    <a href="<?php echo $url; ?>clientarea?tab=services" class="btn btn-primary rounded-pill fw-bold shadow-xs d-inline-flex align-items-center" style="padding: 8px 20px; font-size: 13px;">
                                        <i class="las la-server me-1"></i> Manage Hosting &amp; cPanel
                                    </a>
                                <?php endif; ?>
                                <?php if ($hasDomainItem): ?>
                                    <a href="<?php echo $url; ?>clientarea?tab=domains" class="btn <?php echo $hasHostingItem ? 'btn-outline-primary' : 'btn-primary'; ?> rounded-pill fw-bold shadow-xs d-inline-flex align-items-center" style="padding: 8px 20px; font-size: 13px;">
                                        <i class="las la-globe me-1"></i> Manage Domains &amp; Nameservers
                                    </a>
                                <?php endif; ?>
                                <?php if (!$hasHostingItem && !$hasDomainItem): ?>
                                    <a href="<?php echo $url; ?>clientarea" class="btn btn-primary rounded-pill fw-bold shadow-xs d-inline-flex align-items-center" style="padding: 8px 20px; font-size: 13px;">
                                        <i class="las la-server me-1"></i> Client Area
                                    </a>
                                <?php endif; ?>
                                <button type="button" class="cf-btn-print" id="btnDownloadPdfReceipt" onclick="downloadInvoicePDF(this)">
                                    <i class="las la-file-pdf me-1"></i> Download PDF Receipt
                                </button>
                            </div>

                            <!-- Hosting Activation & Password Notice -->
                            <?php if ($hasHostingItem): ?>
                                <div class="rounded-3 text-start border p-3" style="background: rgba(13, 110, 253, 0.05); border-color: rgba(13, 110, 253, 0.2) !important; margin-top: 18px;">
                                    <div class="d-flex align-items-center gap-2 text-primary fw-bold fs-13 mb-1">
                                        <i class="las la-server fs-18"></i> 
                                        <span>Your Cloud Hosting Server is Ready!</span>
                                    </div>
                                    <p class="fs-12 text-slate-700 mb-2">
                                        Your high-speed NVMe hosting account has been automatically provisioned. We have dispatched your initial cPanel username, server IP, and access credentials to your email (<strong><?php echo htmlspecialchars($client['email'] ?? 'your email'); ?></strong>). You can also reset your password or access server settings directly in your Client Area.
                                    </p>
                                    <div class="d-flex align-items-center gap-3 flex-wrap">
                                        <a href="<?php echo $url; ?>clientarea?tab=services" class="d-inline-flex align-items-center gap-1 fs-12 fw-bold text-primary text-decoration-none">
                                            Go to Hosting Manager <i class="las la-arrow-right"></i>
                                        </a>
                                        <span class="text-muted fs-11">•</span>
                                        <a href="https://sv.cloudflox.com:2083" target="_blank" rel="noopener" class="d-inline-flex align-items-center gap-1 fs-12 fw-bold text-slate-700 text-decoration-none">
                                            Open cPanel Directly <i class="las la-external-link-alt fs-11"></i>
                                        </a>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Domain ICANN Notice -->
                            <?php if ($hasDomainItem): ?>
                                <div class="rounded-3 text-start border p-3" style="background: rgba(245, 158, 11, 0.08); border-color: rgba(245, 158, 11, 0.25) !important; margin-top: 14px;">
                                    <div class="d-flex align-items-center gap-2 text-warning fw-bold fs-12.5 mb-1">
                                        <i class="las la-shield-alt fs-18"></i> 
                                        <span>Next Step: Verify Contact Email (ICANN Requirement)</span>
                                    </div>
                                    <p class="fs-12 text-slate-700 mb-2">
                                        As mandated by ICANN regulations, an email verification link has been dispatched to <strong><?php echo htmlspecialchars($client['email'] ?? 'your email'); ?></strong>. Please confirm the link within 15 days to activate your domain. You can configure nameservers anytime in your Client Area.
                                    </p>
                                    <a href="<?php echo $url; ?>clientarea?tab=domains" class="d-inline-flex align-items-center gap-1 fs-12 fw-bold text-primary text-decoration-none">
                                        Go to Domain Management <i class="las la-arrow-right"></i>
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <div class="cf-invoice-sheet">
                        
                        <!-- Header Row: Brand & Company Details -->
                        <div class="row align-items-center cf-pdf-header-row g-3">
                            <div class="col-6 col-sm-6">
                                <img src="<?php echo $url; ?>assets/imgs/logos/cloud-flox-main.webp" alt="CloudFlox" class="cf-invoice-logo">
                                <div class="cf-pdf-tagline">Fast | Secure | Reliable</div>
                            </div>
                            <div class="col-6 col-sm-6 text-end">
                                <div class="cf-pdf-company-name">CloudFlox LLC</div>
                                <div class="cf-pdf-company-address">2709 N Hayden Island Dr STE 444529</div>
                                <div class="cf-pdf-company-address">Portland OR 97217, USA</div>
                                <div class="cf-pdf-domains">cloudflox.com &nbsp;|&nbsp; my.cloudflox.com</div>
                            </div>
                        </div>

                        <!-- Top Orange Accent Line -->
                        <div class="cf-pdf-header-divider"></div>

                        <!-- 2-Column Meta & Bill To Information -->
                        <div class="row cf-pdf-meta-row g-4">
                            <div class="col-12 col-sm-6">
                                <div class="cf-pdf-main-heading">INVOICE</div>
                                <table class="cf-pdf-meta-table">
                                    <tr>
                                        <td class="cf-pdf-meta-label">Invoice No.</td>
                                        <td class="cf-pdf-meta-val"><?php echo htmlspecialchars($invoiceNumber); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="cf-pdf-meta-label">Date</td>
                                        <td class="cf-pdf-meta-val"><?php echo htmlspecialchars($invoiceDateFormatted); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="cf-pdf-meta-label">Due Date</td>
                                        <td class="cf-pdf-meta-val"><?php echo htmlspecialchars($dueDateFormatted); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="cf-pdf-meta-label">Status</td>
                                        <td class="cf-pdf-meta-val">
                                            <?php if ($isPaid): ?>
                                                <span class="cf-pdf-status-paid">Paid</span>
                                            <?php elseif ($isCancelled): ?>
                                                <span class="cf-pdf-status-cancelled">Cancelled</span>
                                            <?php else: ?>
                                                <span class="cf-pdf-status-unpaid">Unpaid</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="cf-pdf-sub-heading">BILL TO</div>
                                <div class="cf-pdf-billto-details">
                                    <?php if (!empty($client['companyname'])): ?>
                                        <div class="cf-pdf-billto-company"><?php echo htmlspecialchars($client['companyname']); ?></div>
                                    <?php endif; ?>
                                    <div class="cf-pdf-billto-name"><?php echo htmlspecialchars($clientFullName); ?></div>
                                    <?php if (!empty($client['address1'])): ?>
                                        <div><?php echo htmlspecialchars($client['address1']); ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($client['address2'])): ?>
                                        <div><?php echo htmlspecialchars($client['address2']); ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($cityLine)): ?>
                                        <div><?php echo htmlspecialchars($cityLine); ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($clientCountryName)): ?>
                                        <div><?php echo htmlspecialchars($clientCountryName); ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($client['tax_id'])): ?>
                                        <div>Tax ID: <?php echo htmlspecialchars($client['tax_id']); ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Divider Line -->
                        <div class="cf-pdf-mid-divider"></div>

                        <!-- Line Items Table -->
                        <div class="cf-pdf-items-wrap">
                            <table class="cf-pdf-items-table">
                                <thead>
                                    <tr>
                                        <th style="width: 78%; text-align: left;">Description</th>
                                        <th style="width: 22%; text-align: right;">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($invoiceItems)): ?>
                                        <?php foreach ($invoiceItems as $item): ?>
                                            <?php
                                                $rawDesc = $item['description'] ?? '';
                                                $parsed = parseInvoiceItemDescription($rawDesc, $item['type'] ?? '', $item['notes'] ?? '');
                                            ?>
                                            <tr>
                                                <td>
                                                    <div class="cf-pdf-item-title"><?php echo htmlspecialchars($parsed['title']); ?></div>
                                                    <?php if (!empty($parsed['specs'])): ?>
                                                        <div class="cf-pdf-item-specs"><?php echo htmlspecialchars($parsed['specs']); ?></div>
                                                    <?php endif; ?>
                                                </td>
                                                <td style="text-align: right;" class="cf-pdf-item-amount">
                                                    $<?php echo number_format((float)$item['amount'], 2); ?> USD
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td>
                                                <div class="cf-pdf-item-title">CloudFlox Cloud Infrastructure Service</div>
                                            </td>
                                            <td style="text-align: right;" class="cf-pdf-item-amount">
                                                $<?php echo number_format($totalUsd, 2); ?> USD
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Totals Breakdown -->
                        <div class="cf-pdf-totals-wrap">
                            <!-- Subtotal, Tax, and Credit on the Right -->
                            <div class="row justify-content-end">
                                <div class="col-12 col-sm-8 col-md-6 col-lg-5">
                                    <table class="cf-pdf-totals-table ms-auto">
                                        <tr>
                                            <td class="cf-pdf-tot-label">Subtotal</td>
                                            <td class="cf-pdf-tot-val">$<?php echo number_format($subtotalUsd, 2); ?> USD</td>
                                        </tr>
                                        <?php if ($taxUsd > 0): ?>
                                            <tr>
                                                <td class="cf-pdf-tot-label">Tax / Processing Fees</td>
                                                <td class="cf-pdf-tot-val">$<?php echo number_format($taxUsd, 2); ?> USD</td>
                                            </tr>
                                        <?php endif; ?>
                                        <?php if ($creditUsd > 0): ?>
                                            <tr>
                                                <td class="cf-pdf-tot-label">Credit</td>
                                                <td class="cf-pdf-tot-val">-$<?php echo number_format($creditUsd, 2); ?> USD</td>
                                            </tr>
                                        <?php endif; ?>
                                    </table>
                                </div>
                            </div>

                            <!-- Full-Width Orange Bar from Left Corner to Right Corner -->
                            <div class="cf-pdf-total-orange-bar">
                                <span class="cf-pdf-total-orange-label">TOTAL</span>
                                <span class="cf-pdf-total-orange-val">$<?php echo number_format($totalUsd, 2); ?> USD</span>
                            </div>

                            <!-- Balance Due on the Right -->
                            <div class="row justify-content-end">
                                <div class="col-12 col-sm-8 col-md-6 col-lg-5">
                                    <table class="cf-pdf-totals-table ms-auto">
                                        <tr>
                                            <td class="cf-pdf-tot-label" style="font-weight: 700; color: #0F2C5B; padding-top: 4px;">BALANCE DUE</td>
                                            <td class="cf-pdf-tot-val" style="font-weight: 700; color: #0F2C5B; padding-top: 4px;">$<?php echo number_format($balanceDueUsd, 2); ?> USD</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Transactions History Section -->
                        <div class="cf-pdf-tx-section">
                            <div class="cf-pdf-section-title">Transactions</div>
                            <table class="cf-pdf-tx-table">
                                <thead>
                                    <tr>
                                        <th style="width: 24%;">Date</th>
                                        <th style="width: 26%;">Gateway</th>
                                        <th style="width: 32%;">Transaction ID</th>
                                        <th style="width: 18%; text-align: right;">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($transactions)): ?>
                                        <?php foreach ($transactions as $trans): ?>
                                            <?php
                                                $gwRaw = strtolower(trim($trans['gateway'] ?? ''));
                                                if (strpos($gwRaw, 'payhere') !== false) {
                                                    $gwDisplay = 'Pay Now (Visa, Master, Amex)';
                                                } elseif (strpos($gwRaw, 'paypal') !== false) {
                                                    $gwDisplay = 'PayPal';
                                                } else {
                                                    $gwDisplay = !empty($trans['gateway']) ? $trans['gateway'] : 'Online Payment';
                                                }
                                                $txAmount = (float)($trans['amountin'] ?? $trans['amount'] ?? 0);
                                                if ($totalUsd > 0 && $txAmount > ($totalUsd * 50)) {
                                                    $txAmount = $totalUsd;
                                                }
                                            ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars(!empty($trans['date']) ? date('d/m/Y H:i', strtotime($trans['date'])) : '-'); ?></td>
                                                <td><?php echo htmlspecialchars($gwDisplay); ?></td>
                                                <td class="cf-pdf-mono"><?php echo htmlspecialchars($trans['transid'] ?? '-'); ?></td>
                                                <td style="text-align: right; font-weight: 700;">$<?php echo number_format($txAmount, 2); ?> USD</td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-muted" style="padding: 10px;">No Transactions Found</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Optional Invoice Notes -->
                        <?php if (!empty($invoice['notes'])): ?>
                            <div class="cf-pdf-notes-section">
                                <div class="cf-pdf-notes-title">NOTES</div>
                                <div class="cf-pdf-notes-content"><?php echo htmlspecialchars($invoice['notes']); ?></div>
                            </div>
                        <?php endif; ?>

                        <!-- Standard 3-Column Footer with Reference -->
                        <div class="cf-pdf-footer-wrap">
                            <div class="row cf-pdf-footer-contacts g-2 text-start">
                                <div class="col-4">
                                    <span class="cf-pdf-footer-head">TECHNICAL SUPPORT</span>
                                    <a href="mailto:support@cloudflox.com" class="cf-pdf-footer-link">support@cloudflox.com</a>
                                </div>
                                <div class="col-4">
                                    <span class="cf-pdf-footer-head">SALES &amp; HOSTING</span>
                                    <a href="mailto:sales@cloudflox.com" class="cf-pdf-footer-link">sales@cloudflox.com</a>
                                </div>
                                <div class="col-4">
                                    <span class="cf-pdf-footer-head">BILLING &amp; ACCOUNTS</span>
                                    <a href="mailto:billing@cloudflox.com" class="cf-pdf-footer-link">billing@cloudflox.com</a>
                                </div>
                            </div>
                            <div class="cf-pdf-footer-orange-bar"></div>
                            <div class="cf-pdf-footer-meta">
                                CloudFlox LLC &nbsp;|&nbsp; Generated: <?php echo htmlspecialchars($invoiceGeneratedTime); ?> &nbsp;|&nbsp; Ref: <?php echo htmlspecialchars($invoiceRefCode); ?>
                            </div>
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

                            <!-- Gateway Switcher Pills (Card via PayHere, PayPal) -->
                            <div class="cf-gateway-nav-pills" role="tablist">
                                <button type="button" class="cf-gateway-nav-btn <?php echo ($selectedGateway !== 'paypal') ? 'active' : ''; ?>" id="tabBtnCard" onclick="switchGateway('card')">
                                    <i class="las la-credit-card text-primary fs-15"></i> Credit / Debit Card
                                </button>
                                <button type="button" class="cf-gateway-nav-btn <?php echo ($selectedGateway === 'paypal') ? 'active' : ''; ?>" id="tabBtnPayPal" onclick="switchGateway('paypal')">
                                    <i class="lab la-paypal text-info fs-15"></i> PayPal
                                </button>
                            </div>

                            <!-- 1. Credit / Debit Card via PayHere Gateway View -->
                            <div id="paneCard" class="<?php echo ($selectedGateway !== 'paypal') ? '' : 'd-none'; ?>">
                                <div class="cf-amount-display-box">
                                    <div class="cf-amount-display-label">Total Payable with Card:</div>
                                    <div class="cf-amount-display-value">$<?php echo number_format($totalUsd, 2); ?> USD</div>
                                    <span class="cf-amount-display-note">Visa • MasterCard • American Express (via PayHere)</span>
                                </div>

                                <button type="button" class="cf-btn-payhere-cta mb-3" id="btnPayHereCta" onclick="triggerPayHereModal()">
                                    <i class="las la-credit-card fs-18"></i> Pay with Card ($<?php echo number_format($totalUsd, 2); ?> USD)
                                </button>

                                <div class="d-flex align-items-center justify-content-center gap-2 cf-text-muted fs-11.5 mb-2">
                                    <i class="las la-shield-alt text-emerald"></i>
                                    <span>PCI-DSS Level 1 Certified • PayHere 3D-Secure 2.0</span>
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
        // Automatically clear domain & hosting carts upon invoice payment
        if (isAlreadyPaid) {
            try {
                localStorage.removeItem('cf_domain_cart');
                localStorage.removeItem('cf_hosting_pid');
                localStorage.removeItem('cf_hosting_cycle');
                if (window.cfDomainCart && typeof window.cfDomainCart.clear === 'function') {
                    window.cfDomainCart.clear();
                }
            } catch (e) {}
        }

        // Switch Gateway View
        window.switchGateway = function(gw) {
            document.querySelectorAll('.cf-gateway-nav-btn').forEach(btn => btn.classList.remove('active'));
            const paneCard = document.getElementById('paneCard');
            const panePayPal = document.getElementById('panePayPal');
            if (paneCard) paneCard.classList.add('d-none');
            if (panePayPal) panePayPal.classList.add('d-none');

            if (gw === 'paypal') {
                const btn = document.getElementById('tabBtnPayPal');
                if (btn) btn.classList.add('active');
                if (panePayPal) panePayPal.classList.remove('d-none');
            } else {
                // Both 'card' and 'payhere' activate the unified Card (PayHere) gateway
                const btn = document.getElementById('tabBtnCard');
                if (btn) btn.classList.add('active');
                if (paneCard) paneCard.classList.remove('d-none');
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

        // Helper to ensure PayHere SDK is loaded with dynamic polling & retry
        function ensurePayHereSDK(onReady, onFailed) {
            if (typeof payhere !== 'undefined') {
                onReady();
                return;
            }

            const ctaBtn = document.getElementById('btnPayHereCta') || document.querySelector('.cf-btn-payhere-cta');
            const origHtml = ctaBtn ? ctaBtn.innerHTML : '';
            if (ctaBtn) {
                ctaBtn.disabled = true;
                ctaBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span> Initializing Gateway...';
            }

            // Ensure script tag is present in DOM
            let scriptTag = document.querySelector('script[src*="payhere.lk"]');
            if (!scriptTag) {
                scriptTag = document.createElement('script');
                scriptTag.type = 'text/javascript';
                scriptTag.src = 'https://www.payhere.lk/lib/payhere.js';
                document.head.appendChild(scriptTag);
            }

            let elapsed = 0;
            const intervalTime = 200;
            const maxWaitTime = 6000; // Wait up to 6 seconds for network/SDK load

            const poller = setInterval(function() {
                elapsed += intervalTime;
                if (typeof payhere !== 'undefined') {
                    clearInterval(poller);
                    if (ctaBtn) {
                        ctaBtn.disabled = false;
                        ctaBtn.innerHTML = origHtml;
                    }
                    onReady();
                } else if (elapsed >= maxWaitTime) {
                    clearInterval(poller);
                    if (ctaBtn) {
                        ctaBtn.disabled = false;
                        ctaBtn.innerHTML = origHtml;
                    }
                    if (typeof onFailed === 'function') {
                        onFailed();
                    }
                }
            }, intervalTime);
        }

        // PayHere SDK Modal Trigger
        window.triggerPayHereModal = function() {
            ensurePayHereSDK(function() {
                payhere.onCompleted = function(orderId) {
                    try {
                        localStorage.removeItem('cf_domain_cart');
                        localStorage.removeItem('cf_hosting_pid');
                        localStorage.removeItem('cf_hosting_cycle');
                        if (window.cfDomainCart && typeof window.cfDomainCart.clear === 'function') {
                            window.cfDomainCart.clear();
                        }
                    } catch (e) {}
                    // Payment completed via PayHere popup; reload invoice to view payment status
                    window.location.href = 'invoice?id=' + encodeURIComponent(orderId) + '&paid=1';
                };

                payhere.onDismissed = function() {
                    console.log('PayHere popup closed by customer.');
                    window.closePayHereModal();
                };

                payhere.onError = function(error) {
                    console.error('PayHere Error:', error);
                    if (window.cfToast) {
                        cfToast.error('PayHere Notice: ' + error, 'Payment Gateway Notice');
                    } else {
                        alert('PayHere Notice: ' + error);
                    }
                };

                // Start checkout modal
                payhere.startPayment(payherePayment);

                // Sync floating button
                setTimeout(syncPayHereCloseButton, 100);
                setTimeout(syncPayHereCloseButton, 500);
            }, function() {
                // If PayHere could not load after timeout
                const msg = 'PayHere payment gateway could not be loaded. If you are using an Ad Blocker or Brave Shields, please disable it for cloudflox.com and click "Pay with Card" again.';
                if (window.cfToast) {
                    cfToast.warning(msg, 'Payment Gateway Delayed or Blocked', 8000);
                } else {
                    alert(msg);
                }
            });
        };

        // Auto trigger PayHere if redirected directly from checkout with paynow=1
        if (isAutoPay && !isAlreadyPaid) {
            window.addEventListener('load', function() {
                setTimeout(function() {
                    triggerPayHereModal();
                }, 300);
            });
        }
    })();
    </script>

    <!-- PDF Generation Engine (Local Offline-Ready Bundle) -->
    <script src="<?php echo $url; ?>assets/js/vendors/html2pdf.bundle.min.js"></script>
    <script>
    window.downloadInvoicePDF = function(btnElement) {
        const sheet = document.querySelector('.cf-invoice-sheet');
        if (!sheet) {
            window.print();
            return;
        }

        const origHtml = btnElement ? btnElement.innerHTML : '';
        if (btnElement) {
            btnElement.disabled = true;
            btnElement.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Generating PDF...';
        }

        const origMinHeight = sheet.style.minHeight;
        const origHeight = sheet.style.height;

        // Calculate single A4 page height leaving safe 35px buffer to NEVER create an empty second page
        const sheetWidth = sheet.offsetWidth || 760;
        const maxSinglePageHeight = Math.floor(sheetWidth * (285 / 194));
        const targetHeight = maxSinglePageHeight - 35;

        // Apply target height so flexbox margin-top: auto pushes footer right to the bottom of the page
        sheet.style.setProperty('min-height', targetHeight + 'px', 'important');
        sheet.style.setProperty('height', targetHeight + 'px', 'important');

        const invoiceId = '<?php echo htmlspecialchars($invoiceNumber); ?>';
        const opt = {
            margin:       [6, 8, 6, 8],
            filename:     'CloudFlox-Invoice-' + invoiceId + '.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2, useCORS: true, letterRendering: true, logging: false },
            jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
        };

        const restoreSheet = function() {
            sheet.style.removeProperty('min-height');
            sheet.style.removeProperty('height');
            if (origMinHeight) sheet.style.minHeight = origMinHeight;
            if (origHeight) sheet.style.height = origHeight;
            if (btnElement) {
                btnElement.disabled = false;
            }
        };

        if (typeof html2pdf === 'undefined') {
            console.warn('html2pdf not loaded, falling back to print dialog');
            restoreSheet();
            if (btnElement) {
                btnElement.innerHTML = origHtml;
            }
            window.print();
            return;
        }

        html2pdf().set(opt).from(sheet).toPdf().get('pdf').then(function(pdf) {
            // Strictly enforce 1 single page by removing any trailing empty/blank page
            var totalPages = pdf.internal.getNumberOfPages();
            while (totalPages > 1) {
                pdf.deletePage(totalPages);
                totalPages = pdf.internal.getNumberOfPages();
            }
        }).save().then(function() {
            restoreSheet();
            if (btnElement) {
                btnElement.innerHTML = '<i class="las la-check text-success me-1"></i> Saved to Downloads!';
                setTimeout(function() {
                    btnElement.innerHTML = origHtml;
                }, 3000);
            }
        }).catch(function(err) {
            console.error('PDF error:', err);
            restoreSheet();
            if (btnElement) {
                btnElement.innerHTML = origHtml;
            }
            window.print();
        });
    };
    </script>
</body>
</html>
