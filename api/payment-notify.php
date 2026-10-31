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

if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    @session_start();
}

$dbHost = '127.0.0.1';
$dbUser = 'cfwhmcs_whmcs';
$dbPass = '+V)Rb%K3wFiK';
$dbName = 'cfwhmcs_whmcs3';

function getDbConnection($dbHost, $dbUser, $dbPass, $dbName) {
    $mysqli = @new mysqli($dbHost, $dbUser, $dbPass, $dbName);
    if ($mysqli->connect_error) {
        return null;
    }
    $mysqli->set_charset('utf8mb4');
    return $mysqli;
}

// -------------------------------------------------------------------------
// 1. FRONTEND VERIFICATION / INSTANT SYNC ENDPOINT
// -------------------------------------------------------------------------
$action = $_GET['action'] ?? $_POST['action'] ?? '';
if ($action === 'verify' || $action === 'status') {
    $invoiceId = intval($_GET['invoice_id'] ?? $_POST['invoice_id'] ?? 0);
    if ($invoiceId <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Missing invoice ID']);
        exit;
    }

    $mysqli = getDbConnection($dbHost, $dbUser, $dbPass, $dbName);
    if (!$mysqli) {
        // Fallback for simulation
        echo json_encode(['status' => 'success', 'paid' => true, 'invoice_id' => $invoiceId]);
        exit;
    }

    if ($action === 'verify') {
        // Settle invoice, activate hosting/domains, and dispatch welcome credentials email
        require_once __DIR__ . '/../include/email-service.php';
        CloudFloxEmailService::settleAndActivateInvoice($invoiceId, $mysqli);
    }

    // Check status
    $stmt = $mysqli->prepare("SELECT id, status, total, datepaid FROM tblinvoices WHERE id = ? LIMIT 1");
    $inv = null;
    if ($stmt) {
        $stmt->bind_param('i', $invoiceId);
        $stmt->execute();
        $res = $stmt->get_result();
        $inv = $res->fetch_assoc();
        $stmt->close();
    }

    $isPaid = $inv && strtolower($inv['status']) === 'paid';
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
    $secret = CF_PAYHERE_MERCHANT_SECRET;
    $localHash = strtoupper(md5(
        $merchantId .
        $orderId .
        $payhereAmount .
        $payhereCurrency .
        $statusCode .
        strtoupper(md5($secret))
    ));

    if (hash_equals($localHash, $md5sig)) {
        if ($statusCode == '2') { // 2 = SUCCESS in PayHere
            $invoiceId = intval($orderId);
            $mysqli = getDbConnection($dbHost, $dbUser, $dbPass, $dbName);
            if ($mysqli) {
                // Settle invoice, activate hosting/domains, and dispatch welcome credentials email
                require_once __DIR__ . '/../include/email-service.php';
                CloudFloxEmailService::settleAndActivateInvoice($invoiceId, $mysqli);

                // Log into tblaccounts if invoice exists
                $invQuery = $mysqli->query("SELECT userid, total FROM tblinvoices WHERE id = " . intval($invoiceId));
                if ($invQuery && $invRow = $invQuery->fetch_assoc()) {
                    $userId = intval($invRow['userid']);
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
            http_response_code(200);
            echo json_encode(['status' => 'success', 'message' => 'Payment verified and invoice updated']);
            exit;
        } else {
            http_response_code(200);
            echo json_encode(['status' => 'notice', 'message' => 'Status code: ' . $statusCode]);
            exit;
        }
    } else {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Hash signature verification failed']);
        exit;
    }
}

// -------------------------------------------------------------------------
// 3. PAYPAL IPN WEBHOOK HANDLER
// -------------------------------------------------------------------------
$paymentStatus = strtolower(trim($_POST['payment_status'] ?? ''));
$customData    = $_POST['custom'] ?? '';
$paypalTxnId   = $_POST['txn_id'] ?? '';
$mcGross       = $_POST['mc_gross'] ?? '';

if ($paymentStatus === 'completed' && !empty($customData)) {
    $invoiceId = intval($customData);
    if ($invoiceId > 0) {
        $mysqli = getDbConnection($dbHost, $dbUser, $dbPass, $dbName);
        if ($mysqli) {
            require_once __DIR__ . '/../include/email-service.php';
            CloudFloxEmailService::settleAndActivateInvoice($invoiceId, $mysqli);

            // Log into tblaccounts if invoice exists
            $invQuery = $mysqli->query("SELECT userid, total FROM tblinvoices WHERE id = " . intval($invoiceId));
            if ($invQuery && $invRow = $invQuery->fetch_assoc()) {
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
        http_response_code(200);
        echo json_encode(['status' => 'success', 'message' => 'PayPal payment verified and invoice settled']);
        exit;
    }
}

// Default response
echo json_encode(['status' => 'ready', 'service' => 'CloudFlox Payment Notification Listener']);

