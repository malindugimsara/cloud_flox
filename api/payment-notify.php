<?php
/**
 * CloudFlox Payment Gateway Notification & IPN Webhook Handler
 * -------------------------------------------------------------------------
 * Handles asynchronous IPN callbacks from PayHere, PayPal, and Card gateways,
 * verifies payment signatures, and marks invoices as 'Paid' in the database.
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/whmcs.php';

cf_secure_session_start();

$dbHost = CF_DB_HOST;
$dbUser = CF_DB_USER;
$dbPass = CF_DB_PASS;
$dbName = CF_DB_NAME;

function getDbConnection($dbHost, $dbUser, $dbPass, $dbName) {
    $mysqli = @new mysqli($dbHost, $dbUser, $dbPass, $dbName, (int)CF_DB_PORT);
    if ($mysqli->connect_error) {
        return null;
    }
    $mysqli->set_charset('utf8mb4');
    return $mysqli;
}

// -------------------------------------------------------------------------
// 1. FRONTEND PAYMENT STATUS ENDPOINT (Read-Only, Auth/Token Protected)
// -------------------------------------------------------------------------
$action = $_GET['action'] ?? $_POST['action'] ?? '';
if ($action === 'status') {
    $invoiceId = intval($_GET['invoice_id'] ?? $_POST['invoice_id'] ?? 0);
    if ($invoiceId <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Missing invoice ID']);
        exit;
    }

    $mysqli = getDbConnection($dbHost, $dbUser, $dbPass, $dbName);
    if (!$mysqli) {
        echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
        exit;
    }

    // Check status (Read-only query)
    $stmt = $mysqli->prepare("SELECT id, userid, status, total, datepaid FROM tblinvoices WHERE id = ? LIMIT 1");
    $inv = null;
    if ($stmt) {
        $stmt->bind_param('i', $invoiceId);
        $stmt->execute();
        $res = $stmt->get_result();
        $inv = $res->fetch_assoc();
        $stmt->close();
    }

    if (!$inv) {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Invoice not found']);
        exit;
    }

    $invOwnerId = (int)$inv['userid'];
    $sessionClientId = (int)($_SESSION['cf_client_id'] ?? 0);
    $isOwner = !empty($_SESSION['cf_client_logged_in']) && $sessionClientId > 0 && $sessionClientId === $invOwnerId;
    $providedToken = trim((string)($_GET['token'] ?? $_POST['token'] ?? ''));
    $hasValidToken = !empty($providedToken) && function_exists('cf_invoice_token') && hash_equals(cf_invoice_token($invoiceId, $invOwnerId), $providedToken);

    if (!$isOwner && !$hasValidToken) {
        http_response_code(403);
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized access to invoice status']);
        exit;
    }

    $isPaid = strtolower($inv['status']) === 'paid';
    echo json_encode([
        'status'     => 'success',
        'paid'       => $isPaid,
        'invoice_id' => $invoiceId,
        'total'      => $inv['total'] ?? '0.00',
        'datepaid'   => $inv['datepaid'] ?? ''
    ]);
    exit;
}

// -------------------------------------------------------------------------
// 2. PAYHERE IPN WEBHOOK HANDLER
// -------------------------------------------------------------------------
$merchantId     = $_POST['merchant_id'] ?? '';
$orderId        = $_POST['order_id'] ?? '';
$paymentId      = $_POST['payment_id'] ?? '';
$payhereAmount  = $_POST['payhere_amount'] ?? '';
$payhereCurrency= $_POST['payhere_currency'] ?? '';
$statusCode     = $_POST['status_code'] ?? '';
$md5sig         = $_POST['md5sig'] ?? '';
$method         = $_POST['method'] ?? 'PAYHERE';

if (!empty($merchantId) && !empty($orderId) && !empty($statusCode) && !empty($md5sig)) {
    $configuredMerchantId = defined('CF_PAYHERE_MERCHANT_ID') ? CF_PAYHERE_MERCHANT_ID : '';
    $secret = defined('CF_PAYHERE_MERCHANT_SECRET') ? CF_PAYHERE_MERCHANT_SECRET : '';

    if (empty($configuredMerchantId) || empty($secret) || !hash_equals($configuredMerchantId, (string)$merchantId)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Gateway configuration or merchant verification failed']);
        exit;
    }

    $localHash = strtoupper(md5(
        $merchantId .
        $orderId .
        $payhereAmount .
        $payhereCurrency .
        $statusCode .
        strtoupper(md5($secret))
    ));

    if (hash_equals($localHash, strtoupper((string)$md5sig))) {
        if ($statusCode == '2') { // 2 = SUCCESS in PayHere
            $invoiceId = intval($orderId);
            $mysqli = getDbConnection($dbHost, $dbUser, $dbPass, $dbName);
            if ($mysqli && $invoiceId > 0) {
                $invStmt = $mysqli->prepare("SELECT userid, total, status FROM tblinvoices WHERE id = ? LIMIT 1");
                $invRow = null;
                if ($invStmt) {
                    $invStmt->bind_param('i', $invoiceId);
                    $invStmt->execute();
                    $invRes = $invStmt->get_result();
                    $invRow = $invRes ? $invRes->fetch_assoc() : null;
                    $invStmt->close();
                }

                if (!$invRow) {
                    http_response_code(404);
                    echo json_encode(['status' => 'error', 'message' => 'Invoice not found']);
                    exit;
                }

                $expectedCurrency = strtoupper(defined('CF_PAYHERE_CURRENCY') ? CF_PAYHERE_CURRENCY : 'USD');
                $expectedTotal = (float)$invRow['total'];
                if (strtoupper(trim($payhereCurrency)) !== $expectedCurrency || (float)$payhereAmount + 0.02 < $expectedTotal) {
                    error_log("PayHere IPN mismatch for invoice #{$invoiceId}: expected {$expectedTotal} {$expectedCurrency}, got {$payhereAmount} {$payhereCurrency}");
                    http_response_code(400);
                    echo json_encode(['status' => 'error', 'message' => 'Payment amount or currency mismatch']);
                    exit;
                }

                if (strtolower($invRow['status']) !== 'paid') {
                    // Settle invoice, activate hosting/domains, and dispatch welcome credentials email
                    require_once __DIR__ . '/../include/email-service.php';
                    CloudFloxEmailService::settleAndActivateInvoice($invoiceId, $mysqli);

                    // Avoid duplicate transaction logs
                    $dupCheck = $mysqli->prepare("SELECT id FROM tblaccounts WHERE transid = ? AND invoiceid = ? LIMIT 1");
                    $alreadyLogged = false;
                    if ($dupCheck) {
                        $dupCheck->bind_param('si', $paymentId, $invoiceId);
                        $dupCheck->execute();
                        $dupCheck->store_result();
                        $alreadyLogged = ($dupCheck->num_rows > 0);
                        $dupCheck->close();
                    }

                    if (!$alreadyLogged) {
                        $desc = "PayHere Payment for Invoice #" . $invoiceId . " (Ref: " . $paymentId . ")";
                        $accStmt = $mysqli->prepare("INSERT INTO tblaccounts (date, description, amountin, fees, amountout, transid, invoiceid) VALUES (?, ?, ?, 0.00, 0.00, ?, ?)");
                        if ($accStmt) {
                            $today = date('Y-m-d');
                            $accStmt->bind_param('ssssi', $today, $desc, $invRow['total'], $paymentId, $invoiceId);
                            $accStmt->execute();
                            $accStmt->close();
                        }
                    }
                }
            }
            http_response_code(200);
            echo json_encode(['status' => 'success', 'message' => 'Payment verified and invoice updated']);
            exit;
        } else {
            http_response_code(200);
            echo json_encode(['status' => 'notice', 'message' => 'Status code processed']);
            exit;
        }
    } else {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Hash signature verification failed']);
        exit;
    }
}

// -------------------------------------------------------------------------
// 3. PAYPAL IPN WEBHOOK HANDLER (with PayPal IPN Verification)
// -------------------------------------------------------------------------
$paymentStatus = strtolower(trim($_POST['payment_status'] ?? ''));
$customData    = $_POST['custom'] ?? '';
$paypalTxnId   = $_POST['txn_id'] ?? '';
$mcGross       = $_POST['mc_gross'] ?? '';
$mcCurrency    = strtoupper(trim($_POST['mc_currency'] ?? ''));
$receiverEmail = strtolower(trim($_POST['receiver_email'] ?? ''));

if (!empty($_POST) && isset($_POST['payment_status'])) {
    // Step 1: Send IPN data back to PayPal for verification (NEVER trust caller's test_ipn flag in production)
    $rawPostData = file_get_contents('php://input');
    if (empty($rawPostData)) {
        // Fall back to rebuilding from $_POST
        $rawPostData = http_build_query($_POST);
    }

    $verifyPayload = 'cmd=_notify-validate&' . $rawPostData;

    $isPayPalSandbox = (defined('CF_PAYPAL_SANDBOX') && CF_PAYPAL_SANDBOX === true);
    $paypalIpnUrl = $isPayPalSandbox
        ? 'https://ipnpb.sandbox.paypal.com/cgi-bin/webscr'
        : 'https://ipnpb.paypal.com/cgi-bin/webscr';

    $chIpn = curl_init();
    curl_setopt_array($chIpn, [
        CURLOPT_URL            => $paypalIpnUrl,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $verifyPayload,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_HTTPHEADER     => [
            'User-Agent: CloudFlox-PayPal-IPN/1.0',
            'Connection: Close'
        ]
    ]);
    $ipnResponse = curl_exec($chIpn);
    curl_close($chIpn);

    // Step 2: Only proceed if PayPal confirms VERIFIED
    if ($ipnResponse !== 'VERIFIED') {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'PayPal IPN verification failed']);
        exit;
    }

    // Step 3: Process only completed payments and validate recipient, currency, and amount
    $expectedPaypalEmail = strtolower(trim(defined('CF_PAYPAL_EMAIL') ? CF_PAYPAL_EMAIL : ''));
    if (!empty($expectedPaypalEmail) && !empty($receiverEmail) && $receiverEmail !== $expectedPaypalEmail) {
        error_log("PayPal IPN receiver mismatch: expected {$expectedPaypalEmail}, got {$receiverEmail}");
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Receiver email mismatch']);
        exit;
    }

    if ($paymentStatus === 'completed' && !empty($customData)) {
        $invoiceId = intval($customData);
        if ($invoiceId > 0) {
            $mysqli = getDbConnection($dbHost, $dbUser, $dbPass, $dbName);
            if ($mysqli) {
                $invQuery = $mysqli->prepare("SELECT userid, total, status FROM tblinvoices WHERE id = ? LIMIT 1");
                $invRow = null;
                if ($invQuery) {
                    $invQuery->bind_param('i', $invoiceId);
                    $invQuery->execute();
                    $invRes = $invQuery->get_result();
                    $invRow = $invRes ? $invRes->fetch_assoc() : null;
                    $invQuery->close();
                }

                if (!$invRow) {
                    http_response_code(404);
                    echo json_encode(['status' => 'error', 'message' => 'Invoice not found']);
                    exit;
                }

                $expectedTotal = (float)$invRow['total'];
                if ($mcCurrency !== 'USD' || (float)$mcGross + 0.02 < $expectedTotal) {
                    error_log("PayPal IPN amount/currency mismatch for invoice #{$invoiceId}: expected {$expectedTotal} USD, got {$mcGross} {$mcCurrency}");
                    http_response_code(400);
                    echo json_encode(['status' => 'error', 'message' => 'Payment amount or currency mismatch']);
                    exit;
                }

                if (strtolower($invRow['status']) !== 'paid') {
                    require_once __DIR__ . '/../include/email-service.php';
                    CloudFloxEmailService::settleAndActivateInvoice($invoiceId, $mysqli);

                    $dupCheck = $mysqli->prepare("SELECT id FROM tblaccounts WHERE transid = ? AND invoiceid = ? LIMIT 1");
                    $alreadyLogged = false;
                    if ($dupCheck) {
                        $dupCheck->bind_param('si', $paypalTxnId, $invoiceId);
                        $dupCheck->execute();
                        $dupCheck->store_result();
                        $alreadyLogged = ($dupCheck->num_rows > 0);
                        $dupCheck->close();
                    }

                    if (!$alreadyLogged) {
                        $desc = "PayPal Payment for Invoice #" . $invoiceId . " (Ref: " . $paypalTxnId . ")";
                        $accStmt = $mysqli->prepare("INSERT INTO tblaccounts (date, description, amountin, fees, amountout, transid, invoiceid) VALUES (?, ?, ?, 0.00, 0.00, ?, ?)");
                        if ($accStmt) {
                            $today = date('Y-m-d');
                            $accStmt->bind_param('ssssi', $today, $desc, $invRow['total'], $paypalTxnId, $invoiceId);
                            $accStmt->execute();
                            $accStmt->close();
                        }
                    }
                }
            }
            http_response_code(200);
            echo json_encode(['status' => 'success', 'message' => 'PayPal payment verified and invoice settled']);
            exit;
        }
    }

    http_response_code(200);
    echo json_encode(['status' => 'notice', 'message' => 'IPN processed']);
    exit;
}

// Default response
echo json_encode(['status' => 'ready', 'service' => 'CloudFlox Payment Notification Listener']);

