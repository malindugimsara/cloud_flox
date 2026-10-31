<?php
/**
 * CloudFlox Server Solution & Quotation Request API
 * -------------------------------------------------------------------------
 * Handles incoming RFQ submissions for KVM VPS & Dedicated Bare Metal Servers.
 * 1. Validates hardware specifications & client details
 * 2. Authenticates or provisions client account in WHMCS
 * 3. Logs the formal proposal request in tblquotes & tblquoteitems
 * 4. Dispatches confirmation emails to client and alert to sales engineering
 */

header('Content-Type: application/json; charset=utf-8');

if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    @session_start();
}

require_once __DIR__ . '/../include/email-service.php';
require_once __DIR__ . '/../include/url.inc.php';
require_once __DIR__ . '/whmcs.php';

// Accept both JSON payload and $_POST
$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true);
if (!$data || !is_array($data)) {
    $data = $_POST;
}

// 1. Client Contact Details
$firstName   = trim($data['firstname'] ?? '');
$lastName    = trim($data['lastname'] ?? '');
$companyName = trim($data['companyname'] ?? '');
$email       = trim($data['email'] ?? '');
$phone       = trim($data['phone'] ?? $data['phonenumber'] ?? '');
$country     = trim($data['country'] ?? 'LK');

// If user is currently logged in, fill defaults
if (!empty($_SESSION['cf_client_logged_in'])) {
    if (empty($email)) $email = $_SESSION['cf_client_email'] ?? '';
    if (empty($firstName) && !empty($_SESSION['cf_client_name'])) {
        $parts = explode(' ', $_SESSION['cf_client_name']);
        $firstName = $parts[0];
        $lastName = count($parts) > 1 ? implode(' ', array_slice($parts, 1)) : '';
    }
}

// 2. Server Technical Requirements
$serverType     = trim($data['server_type'] ?? $data['tier'] ?? 'KVM Cloud VPS');
$basePlanName   = trim($data['base_plan'] ?? $data['plan_name'] ?? 'Custom Server Solution');
$pid            = trim($data['pid'] ?? '');
$basePrice      = (float)($data['base_price'] ?? 0.00);

$cpuCores       = trim($data['cpu'] ?? $data['cpu_cores'] ?? '2 vCPUs');
$ramMemory      = trim($data['ram'] ?? '4 GB RAM');
$storage        = trim($data['storage'] ?? '80 GB Pure NVMe');
$raidLevel      = trim($data['raid'] ?? 'RAID-1 Mirroring');
$osTemplate     = trim($data['os'] ?? 'Ubuntu 24.04 LTS');
$controlPanel   = trim($data['control_panel'] ?? 'None (Root SSH Only)');
$datacenter     = trim($data['datacenter'] ?? $data['location'] ?? 'Frankfurt, Germany (EU Central)');
$dedicatedIps   = trim($data['ips'] ?? '1 Dedicated IPv4');
$management     = trim($data['management'] ?? 'Self-Managed (Full Root Access)');
$workload       = trim($data['workload'] ?? 'Web & Application Hosting');
$budget         = trim($data['budget'] ?? 'Standard Pricing');
$customNotes    = trim($data['notes'] ?? $data['custom_requirements'] ?? '');
$serverHostname = trim($data['hostname'] ?? '');

// Basic validations
if (empty($firstName) || empty($lastName)) {
    echo json_encode(['status' => 'error', 'message' => 'Please provide both your First and Last Name.']);
    exit;
}
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'message' => 'Please provide a valid business email address.']);
    exit;
}
if (empty($phone)) {
    echo json_encode(['status' => 'error', 'message' => 'Please provide a contact phone number.']);
    exit;
}

// 3. Connect to Database & Link Client
$mysqli = null;
try {
    $mysqli = @new mysqli('127.0.0.1', 'cfwhmcs_whmcs', '+V)Rb%K3wFiK', 'cfwhmcs_whmcs3');
    if ($mysqli->connect_error) {
        throw new Exception('Database connection failed: ' . $mysqli->connect_error);
    }
    $mysqli->set_charset('utf8');
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection error. Please try again shortly.']);
    exit;
}

$clientId = (int)($_SESSION['cf_client_id'] ?? 0);

if ($clientId <= 0) {
    // Check if client exists by email
    $chkStmt = $mysqli->prepare("SELECT id, firstname, lastname FROM tblclients WHERE email = ? LIMIT 1");
    if ($chkStmt) {
        $chkStmt->bind_param('s', $email);
        $chkStmt->execute();
        $res = $chkStmt->get_result();
        if ($res && $row = $res->fetch_assoc()) {
            $clientId = (int)$row['id'];
        }
        $chkStmt->close();
    }
}

// If new customer, register client profile so they can track quote in portal
if ($clientId <= 0) {
    $tempPass = bin2hex(random_bytes(6)) . 'A1!';
    $hashedPass = password_hash($tempPass, PASSWORD_DEFAULT);
    $today = date('Y-m-d');

    $regStmt = $mysqli->prepare("INSERT INTO tblclients (
        firstname, lastname, companyname, email, phonenumber, address1, city, state, postcode, country,
        password, datecreated, status, currency, defaultgateway
    ) VALUES (
        ?, ?, ?, ?, ?, 'Commercial Address', 'Colombo', 'Western', '00100', ?,
        ?, ?, 'Active', 1, 'payheresdk'
    )");

    if ($regStmt) {
        $regStmt->bind_param('ssssssss', $firstName, $lastName, $companyName, $email, $phone, $country, $hashedPass, $today);
        $regStmt->execute();
        $clientId = $regStmt->insert_id;
        $regStmt->close();

        // Automatically log them in for instant client portal access
        $_SESSION['cf_client_logged_in'] = true;
        $_SESSION['cf_client_id']        = $clientId;
        $_SESSION['cf_client_email']     = $email;
        $_SESSION['cf_client_name']      = trim($firstName . ' ' . $lastName);
    }
}

// 4. Construct Rich Proposal Specification
$specsList = [
    'Infrastructure Category' => $serverType,
    'Base Model / Tier'       => $basePlanName,
    'Requested Hostname'      => $serverHostname ?: 'To be assigned',
    'Processor / CPU'         => $cpuCores,
    'Memory (RAM)'            => $ramMemory,
    'Storage Array'           => $storage . (!empty($raidLevel) ? " ({$raidLevel})" : ''),
    'Operating System'        => $osTemplate,
    'Control Panel Stack'     => $controlPanel,
    'Datacenter Region'       => $datacenter,
    'Network & Dedicated IPs' => $dedicatedIps . ' • 1 Gbps Unmetered Uplink',
    'SLA / Management Level'  => $management,
    'Intended Workload'       => $workload
];

$proposalText = "Proposal for CloudFlox Enterprise Infrastructure Solution\n";
$proposalText .= "---------------------------------------------------------\n";
foreach ($specsList as $k => $v) {
    $proposalText .= "{$k}: {$v}\n";
}
if (!empty($budget)) {
    $proposalText .= "Customer Target Budget: {$budget}\n";
}

$today = date('Y-m-d');
$validUntil = date('Y-m-d', strtotime('+30 days'));
$subject = "Custom Server RFQ: {$basePlanName} ({$serverType})";
$adminNotes = "Auto-generated via CloudFlox Server Solution Builder. IP: " . ($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1');

// 5. Insert Quote into tblquotes
$quoteId = 0;
$qStmt = $mysqli->prepare("INSERT INTO tblquotes (
    subject, stage, validuntil, userid, firstname, lastname, companyname, email,
    address1, address2, city, state, postcode, country, phonenumber, tax_id,
    currency, subtotal, tax1, tax2, total, proposal, customernotes, adminnotes,
    datecreated, lastmodified, datesent, dateaccepted
) VALUES (
    ?, 'Draft', ?, ?, ?, ?, ?, ?,
    '', '', '', '', '', ?, ?, '',
    1, ?, 0.00, 0.00, ?, ?, ?, ?,
    ?, ?, '0000-00-00', '0000-00-00'
)");

if ($qStmt) {
    $types = 'ssissssssddsssss';
    $qStmt->bind_param(
        $types,
        $subject, $validUntil, $clientId, $firstName, $lastName, $companyName, $email,
        $country, $phone,
        $basePrice, $basePrice, $proposalText, $customNotes, $adminNotes,
        $today, $today
    );
    $qStmt->execute();
    $quoteId = $qStmt->insert_id;
    $qStmt->close();
}

if ($quoteId <= 0) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Unable to record quotation request. Please reach out to sales directly.'
    ]);
    exit;
}

// 6. Insert Line Items in tblquoteitems
$items = [
    [
        'desc' => "{$serverType}: {$basePlanName} — {$cpuCores}, {$ramMemory}",
        'qty'  => "1",
        'price'=> $basePrice
    ],
    [
        'desc' => "Primary Storage: {$storage} ({$raidLevel})",
        'qty'  => "1",
        'price'=> 0.00
    ],
    [
        'desc' => "Operating System & Environment: {$osTemplate} with {$controlPanel}",
        'qty'  => "1",
        'price'=> 0.00
    ],
    [
        'desc' => "Datacenter Allocation: {$datacenter} with {$dedicatedIps}",
        'qty'  => "1",
        'price'=> 0.00
    ],
    [
        'desc' => "Enterprise Support & SLA: {$management}",
        'qty'  => "1",
        'price'=> 0.00
    ]
];

// 6.1 Insert To-Do Item in WHMCS so it lights up on the WHMCS Home Dashboard
$todoTitle = "Server Quotation RFQ #" . $quoteId . " - " . $basePlanName;
$todoDesc = "Customer " . trim($firstName . " " . $lastName) . " (" . $email . ", " . $phone . ") requested " . $basePlanName . " (" . $serverType . "). vCPU: " . $cpuCores . ", RAM: " . $ramMemory . ", Storage: " . $storage . ". Budget: " . ($budget ?: "Not specified") . ". Go to Billing > Quotes to review and send pricing.";
$todoStatus = "Pending";
$tStmt = $mysqli->prepare("INSERT INTO tbltodolist (date, title, description, admin, status, duedate) VALUES (?, ?, ?, 0, ?, ?)");
if ($tStmt) {
    $tStmt->bind_param("sssss", $today, $todoTitle, $todoDesc, $todoStatus, $today);
    $tStmt->execute();
    $tStmt->close();
}

// 6.2 Insert Activity Log in WHMCS so it appears in Admin Activity Log
$actDesc = "New Server Quotation Request #RFQ-" . $quoteId . " submitted by " . trim($firstName . " " . $lastName) . " (" . $basePlanName . " - " . $serverType . ")";
$actUser = "System";
$remoteIp = $_SERVER["REMOTE_ADDR"] ?? "127.0.0.1";
$actStmt = $mysqli->prepare("INSERT INTO tblactivitylog (date, description, user, userid, user_id, admin_id, ipaddr) VALUES (?, ?, ?, ?, ?, 0, ?)");
if ($actStmt) {
    $nowDt = date("Y-m-d H:i:s");
    $actStmt->bind_param("sssdds", $nowDt, $actDesc, $actUser, $clientId, $clientId, $remoteIp);
    $actStmt->execute();
    $actStmt->close();
}

$now = date('Y-m-d H:i:s');
foreach ($items as $it) {
    $itStmt = $mysqli->prepare("INSERT INTO tblquoteitems (quoteid, description, quantity, unitprice, discount, taxable, created_at, updated_at) VALUES (?, ?, ?, ?, 0.00, 0, ?, ?)");
    if ($itStmt) {
        $itStmt->bind_param('issdss', $quoteId, $it['desc'], $it['qty'], $it['price'], $now, $now);
        $itStmt->execute();
        $itStmt->close();
    }
}

// 7. Dispatch Transactional Emails
$portalLink = 'clientarea?tab=quotes';
CloudFloxEmailService::sendQuoteRequestClientConfirmation(
    $email,
    trim($firstName . ' ' . $lastName),
    $quoteId,
    $basePlanName,
    $specsList,
    $portalLink
);

CloudFloxEmailService::sendQuoteRequestAdminAlert(
    $quoteId,
    trim($firstName . ' ' . $lastName),
    $companyName,
    $email,
    $phone,
    $country,
    $basePlanName,
    $specsList,
    $customNotes,
    $budget
);

// 8. Success Response
echo json_encode([
    'status'           => 'success',
    'quote_id'         => $quoteId,
    'message'          => 'Your server requirements have been received! Our Cloud Engineering team will review your specifications and dispatch an official quotation shortly.',
    'client_id'        => $clientId,
    'client_logged_in' => !empty($_SESSION['cf_client_logged_in']),
    'redirect_url'     => $url . 'clientarea?tab=quotes&new_quote=' . $quoteId
]);
exit;
