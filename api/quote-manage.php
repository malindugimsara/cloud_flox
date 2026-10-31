<?php
/**
 * CloudFlox Quotations API Bridge (Client & Admin)
 * -------------------------------------------------------------------------
 * Handles:
 * 1. Fetching quotation details (Client & Admin)
 * 2. Admin delivering quotation with customized pricing to customer
 * 3. Client accepting approved quotation and generating payable invoice
 */

header('Content-Type: application/json; charset=utf-8');

if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    @session_start();
}

require_once __DIR__ . '/../include/url.inc.php';
require_once __DIR__ . '/../include/email-service.php';
require_once __DIR__ . '/whmcs.php';

// Parse request
$raw = file_get_contents('php://input');
$data = json_decode($raw, true) ?: $_POST;

$action  = strtolower(trim($data['action'] ?? ''));
$quoteId = (int)($data['quote_id'] ?? 0);

if ($quoteId <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid or missing Quote ID.']);
    exit;
}

// Connect to Database
$mysqli = null;
try {
    $mysqli = @new mysqli('127.0.0.1', 'cfwhmcs_whmcs', '+V)Rb%K3wFiK', 'cfwhmcs_whmcs3');
    if ($mysqli->connect_error) {
        throw new Exception($mysqli->connect_error);
    }
    $mysqli->set_charset('utf8');
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed.']);
    exit;
}

// -----------------------------------------------------------------------------
// ADMIN ACTIONS
// -----------------------------------------------------------------------------
if ($action === 'admin_get_quote') {
    $stmt = $mysqli->prepare("SELECT * FROM tblquotes WHERE id = ? LIMIT 1");
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Query error.']);
        exit;
    }
    $stmt->bind_param('i', $quoteId);
    $stmt->execute();
    $res = $stmt->get_result();
    $quote = $res ? $res->fetch_assoc() : null;
    $stmt->close();

    if (!$quote) {
        echo json_encode(['status' => 'error', 'message' => 'Quotation not found.']);
        exit;
    }

    $items = [];
    $iStmt = $mysqli->prepare("SELECT id, description, quantity, unitprice, discount, taxable FROM tblquoteitems WHERE quoteid = ? ORDER BY id ASC");
    if ($iStmt) {
        $iStmt->bind_param('i', $quoteId);
        $iStmt->execute();
        $iRes = $iStmt->get_result();
        while ($it = $iRes->fetch_assoc()) {
            $items[] = [
                'id'          => (int)$it['id'],
                'description' => $it['description'],
                'quantity'    => $it['quantity'],
                'unitprice'   => number_format((float)$it['unitprice'], 2),
                'total'       => number_format((float)$it['unitprice'] * (float)$it['quantity'], 2)
            ];
        }
        $iStmt->close();
    }

    echo json_encode([
        'status' => 'success',
        'quote'  => [
            'id'            => (int)$quote['id'],
            'subject'       => $quote['subject'],
            'stage'         => $quote['stage'],
            'client_name'   => trim($quote['firstname'] . ' ' . $quote['lastname']),
            'company'       => $quote['companyname'],
            'email'         => $quote['email'],
            'phone'         => $quote['phonenumber'],
            'country'       => $quote['country'],
            'datecreated'   => !empty($quote['datecreated']) ? date('M d, Y', strtotime($quote['datecreated'])) : '',
            'validuntil'    => !empty($quote['validuntil']) ? $quote['validuntil'] : date('Y-m-d', strtotime('+30 days')),
            'subtotal'      => number_format((float)$quote['subtotal'], 2),
            'total'         => number_format((float)$quote['total'], 2),
            'proposal'      => $quote['proposal'],
            'customernotes' => $quote['customernotes'],
            'adminnotes'    => $quote['adminnotes'],
            'items'         => $items
        ]
    ]);
    exit;
}

if ($action === 'admin_deliver_quote') {
    $price = (float)($data['price'] ?? 0);
    $validUntil = trim($data['valid_until'] ?? '');
    $adminNotes = trim($data['admin_notes'] ?? '');

    if ($price <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Please specify a valid quotation price greater than 0.']);
        exit;
    }

    if (empty($validUntil)) {
        $validUntil = date('Y-m-d', strtotime('+30 days'));
    }

    $stmt = $mysqli->prepare("SELECT * FROM tblquotes WHERE id = ? LIMIT 1");
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Database error.']);
        exit;
    }
    $stmt->bind_param('i', $quoteId);
    $stmt->execute();
    $res = $stmt->get_result();
    $quote = $res ? $res->fetch_assoc() : null;
    $stmt->close();

    if (!$quote) {
        echo json_encode(['status' => 'error', 'message' => 'Quotation not found.']);
        exit;
    }

    $today = date('Y-m-d');

    // Update Quote status to Delivered
    $upd = $mysqli->prepare("UPDATE tblquotes SET stage = 'Delivered', subtotal = ?, total = ?, validuntil = ?, datesent = ?, lastmodified = ?, adminnotes = CONCAT(adminnotes, '\n', ?) WHERE id = ?");
    if ($upd) {
        $noteAppend = "[Admin Updated " . date('Y-m-d H:i') . "] Quoted Price: $" . number_format($price, 2) . ". " . $adminNotes;
        $upd->bind_param('ddssssi', $price, $price, $validUntil, $today, $today, $noteAppend, $quoteId);
        $upd->execute();
        $upd->close();
    }

    // Update primary line item price
    $updItem = $mysqli->prepare("UPDATE tblquoteitems SET unitprice = ? WHERE quoteid = ? ORDER BY id ASC LIMIT 1");
    if ($updItem) {
        $updItem->bind_param('di', $price, $quoteId);
        $updItem->execute();
        $updItem->close();
    }

    // Send formal notification email to customer
    $clientEmail = trim($quote['email']);
    $clientName  = trim($quote['firstname'] . ' ' . $quote['lastname']);
    $planName    = $quote['subject'];
    $portalUrl   = $url . 'clientarea?tab=quotes';

    CloudFloxEmailService::sendQuoteDeliveredCustomerEmail(
        $clientEmail,
        $clientName,
        $quoteId,
        $planName,
        $price,
        $validUntil,
        $portalUrl
    );

    echo json_encode([
        'status'  => 'success',
        'message' => 'Quotation #RFQ-' . $quoteId . ' has been updated to $' . number_format($price, 2) . ' USD and formally delivered to ' . htmlspecialchars($clientEmail) . '!'
    ]);
    exit;
}

// -----------------------------------------------------------------------------
// CLIENT ACTIONS (Requires Client Session)
// -----------------------------------------------------------------------------
$clientId = (int)($_SESSION['cf_client_id'] ?? 0);
$isLoggedIn = !empty($_SESSION['cf_client_logged_in']) && $clientId > 0;

if (!$isLoggedIn) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Please sign in to view and manage quotations.']);
    exit;
}

// Security: Verify quote belongs to authenticated client
$stmt = $mysqli->prepare("SELECT * FROM tblquotes WHERE id = ? AND userid = ? LIMIT 1");
if (!$stmt) {
    echo json_encode(['status' => 'error', 'message' => 'Query error.']);
    exit;
}
$stmt->bind_param('ii', $quoteId, $clientId);
$stmt->execute();
$res = $stmt->get_result();
$quote = $res ? $res->fetch_assoc() : null;
$stmt->close();

if (!$quote) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Quotation not found or unauthorized access.']);
    exit;
}

switch ($action) {

    // =========================================================================
    // ACTION: GET QUOTATION DETAILS & LINE ITEMS
    // =========================================================================
    case 'get_quote':
        $items = [];
        $iStmt = $mysqli->prepare("SELECT id, description, quantity, unitprice, discount, taxable FROM tblquoteitems WHERE quoteid = ? ORDER BY id ASC");
        if ($iStmt) {
            $iStmt->bind_param('i', $quoteId);
            $iStmt->execute();
            $iRes = $iStmt->get_result();
            while ($it = $iRes->fetch_assoc()) {
                $items[] = [
                    'id'          => (int)$it['id'],
                    'description' => $it['description'],
                    'quantity'    => $it['quantity'],
                    'unitprice'   => number_format((float)$it['unitprice'], 2),
                    'total'       => number_format((float)$it['unitprice'] * (float)$it['quantity'], 2)
                ];
            }
            $iStmt->close();
        }

        echo json_encode([
            'status' => 'success',
            'quote'  => [
                'id'            => (int)$quote['id'],
                'subject'       => $quote['subject'],
                'stage'         => $quote['stage'],
                'datecreated'   => !empty($quote['datecreated']) ? date('M d, Y', strtotime($quote['datecreated'])) : '',
                'validuntil'    => !empty($quote['validuntil']) ? date('M d, Y', strtotime($quote['validuntil'])) : '',
                'subtotal'      => number_format((float)$quote['subtotal'], 2),
                'total'         => number_format((float)$quote['total'], 2),
                'proposal'      => $quote['proposal'],
                'customernotes' => $quote['customernotes'],
                'items'         => $items,
                'can_accept'    => (strtolower($quote['stage']) === 'delivered')
            ]
        ]);
        exit;

    // =========================================================================
    // ACTION: ACCEPT QUOTATION & CREATE PAYABLE INVOICE
    // =========================================================================
    case 'accept_quote':
        $currentStage = strtolower(trim($quote['stage']));
        if ($currentStage === 'accepted') {
            echo json_encode([
                'status'      => 'info',
                'message'     => 'This quotation has already been accepted.',
                'invoice_url' => $url . 'clientarea?tab=invoices'
            ]);
            exit;
        }

        if ($currentStage !== 'delivered') {
            echo json_encode([
                'status'  => 'error',
                'message' => 'This quotation is currently ' . htmlspecialchars($quote['stage']) . ' and cannot be accepted until formally delivered by CloudFlox engineering.'
            ]);
            exit;
        }

        $today = date('Y-m-d');
        $dueDate = date('Y-m-d', strtotime('+7 days'));
        $now = date('Y-m-d H:i:s');
        $totalAmount = (float)$quote['total'];

        // 1. Mark Quote as Accepted
        $upd = $mysqli->prepare("UPDATE tblquotes SET stage = 'Accepted', dateaccepted = ?, lastmodified = ? WHERE id = ?");
        if ($upd) {
            $upd->bind_param('ssi', $today, $today, $quoteId);
            $upd->execute();
            $upd->close();
        }

        // 2. Generate Payable Invoice in tblinvoices
        $invoiceId = 0;
        $invStmt = $mysqli->prepare("INSERT INTO tblinvoices (
            userid, invoicenum, date, duedate, datepaid, subtotal, credit, tax, tax2, total,
            taxrate, taxrate2, status, paymentmethod, notes, created_at, updated_at
        ) VALUES (
            ?, '', ?, ?, '0000-00-00 00:00:00', ?, 0.00, 0.00, 0.00, ?,
            0.000, 0.000, 'Unpaid', 'payheresdk', ?, ?, ?
        )");

        $invNotes = "Generated from accepted quotation #RFQ-" . $quoteId;
        if ($invStmt) {
            $invStmt->bind_param('isssdsss', $clientId, $today, $dueDate, $totalAmount, $totalAmount, $invNotes, $now, $now);
            $invStmt->execute();
            $invoiceId = $invStmt->insert_id;
            $invStmt->close();
        }

        // 3. Copy line items from quote to tblinvoiceitems
        if ($invoiceId > 0) {
            $iStmt = $mysqli->prepare("SELECT description, quantity, unitprice FROM tblquoteitems WHERE quoteid = ?");
            if ($iStmt) {
                $iStmt->bind_param('i', $quoteId);
                $iStmt->execute();
                $iRes = $iStmt->get_result();
                while ($it = $iRes->fetch_assoc()) {
                    $itemDesc = $it['description'];
                    $itemAmt  = (float)$it['unitprice'] * (float)$it['quantity'];
                    $nowTime  = date('Y-m-d H:i:s');
                    $ins = $mysqli->prepare("INSERT INTO tblinvoiceitems (invoiceid, userid, type, relid, description, amount, taxed, duedate, paymentmethod) VALUES (?, ?, 'Item', 0, ?, ?, 0, ?, 'payheresdk')");
                    if ($ins) {
                        $ins->bind_param('iisds', $invoiceId, $clientId, $itemDesc, $itemAmt, $dueDate);
                        $ins->execute();
                        $ins->close();
                    }
                }
                $iStmt->close();
            }
        }

        echo json_encode([
            'status'      => 'success',
            'message'     => 'Quotation #RFQ-' . $quoteId . ' accepted successfully! Invoice #' . $invoiceId . ' has been generated.',
            'invoice_id'  => $invoiceId,
            'invoice_url' => $url . 'invoice?id=' . $invoiceId . '&paynow=1'
        ]);
        exit;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Unrecognized action parameter.']);
        exit;
}
