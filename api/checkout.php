<?php
/**
 * CloudFlox On-Site Checkout Processing Endpoint
 * -------------------------------------------------------------------------
 * Handles AJAX requests from the checkout modal, creates the customer account
 * in WHMCS, creates the hosting + domain order, and returns immediate invoice
 * and order confirmation.
 */

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');

error_reporting(0);
ini_set('display_errors', '0');

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/whmcs.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Accept both JSON payload and $_POST
$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true);
if (!$data || !is_array($data)) {
    $data = $_POST;
}

$action = strtolower(trim($data['action'] ?? ''));

// =========================================================================
// ACTION: SIGN IN / VALIDATE LOGIN
// =========================================================================
if ($action === 'login') {
    $email    = trim($data['email'] ?? $data['login_email'] ?? '');
    $password = trim($data['password'] ?? $data['login_password'] ?? '');

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Please enter a valid account email address.'
        ]);
        exit;
    }
    if (empty($password)) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Please enter your account password.'
        ]);
        exit;
    }

    $authResult = WHMCSClient::validateLogin($email, $password);
    if (empty($authResult['clientid'])) {
        $errMsg = $authResult['message'] ?? 'Invalid email address or password. Please check your credentials.';
        echo json_encode([
            'status'  => 'error',
            'message' => $errMsg
        ]);
        exit;
    }

    $clientId = $authResult['clientid'];
    $clientName = '';

    // Fetch user details if available
    try {
        $details = WHMCSClient::call('GetClientsDetails', ['clientid' => $clientId]);
        if (!empty($details['client'])) {
            $c = $details['client'];
            $clientName = trim(($c['firstname'] ?? '') . ' ' . ($c['lastname'] ?? ''));
        }
    } catch (Exception $e) {}

    if (empty($clientName)) {
        $clientName = explode('@', $email)[0];
    }

    // Set authenticated session
    $_SESSION['cf_client_logged_in'] = true;
    $_SESSION['cf_client_id']        = $clientId;
    $_SESSION['cf_client_email']     = $email;
    $_SESSION['cf_client_name']      = $clientName;

    echo json_encode([
        'status'       => 'success',
        'message'      => 'Signed in successfully as ' . $clientName,
        'client_id'    => $clientId,
        'client_name'  => $clientName,
        'client_email' => $email
    ]);
    exit;
}

// =========================================================================
// ACTION: REGISTER NEW ACCOUNT
// =========================================================================
if ($action === 'register') {
    $firstName   = trim($data['firstname'] ?? '');
    $lastName    = trim($data['lastname'] ?? '');
    $email       = trim($data['email'] ?? '');
    $phoneNumber = trim($data['phonenumber'] ?? '');
    $password    = trim($data['password'] ?? '');
    $confirm     = trim($data['password_confirm'] ?? '');
    $companyName = trim($data['companyname'] ?? '');
    $country     = strtoupper(trim($data['country'] ?? 'LK'));
    $address1    = trim($data['address1'] ?? '');
    $address2    = trim($data['address2'] ?? '');
    $city        = trim($data['city'] ?? '');
    $state       = trim($data['state'] ?? 'Western');
    $postcode    = trim($data['postcode'] ?? '00100');

    if (empty($firstName) || empty($lastName)) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Please provide both your First and Last Name.'
        ]);
        exit;
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Please provide a valid email address.'
        ]);
        exit;
    }
    if (empty($phoneNumber)) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Please provide a contact phone number.'
        ]);
        exit;
    }
    if (empty($address1)) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Please enter your street address.'
        ]);
        exit;
    }
    if (empty($city)) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Please enter your city.'
        ]);
        exit;
    }
    if (empty($password) || strlen($password) < 6) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Password must be at least 6 characters long.'
        ]);
        exit;
    }
    if (!empty($confirm) && $password !== $confirm) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Passwords do not match. Please re-enter your password.'
        ]);
        exit;
    }

    // 1. Check if email already registered in local database
    $accountExists = false;
    try {
        $mysqli = @new mysqli('localhost', 'cfwhmcs_whmcs', '+V)Rb%K3wFiK', 'cfwhmcs_whmcs3');
        if (!$mysqli->connect_error) {
            $checkStmt = $mysqli->prepare("SELECT id FROM tblclients WHERE email = ? LIMIT 1");
            if ($checkStmt) {
                $checkStmt->bind_param('s', $email);
                $checkStmt->execute();
                $checkStmt->store_result();
                if ($checkStmt->num_rows > 0) {
                    $accountExists = true;
                }
                $checkStmt->close();
            }
        }
    } catch (Exception $e) {}

    if ($accountExists) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'An account with this email address already exists. Please switch to Sign In.'
        ]);
        exit;
    }

    // 2. Call WHMCS AddClient API
    $clientPayload = [
        'firstname'   => $firstName,
        'lastname'    => $lastName,
        'companyname' => $companyName,
        'email'       => $email,
        'phonenumber' => $phoneNumber,
        'password'    => $password,
        'address1'    => $address1,
        'address2'    => $address2,
        'city'        => $city,
        'state'       => $state,
        'postcode'    => $postcode,
        'country'     => $country
    ];

    $clientResult = WHMCSClient::addOrGetClient($clientPayload);
    if (empty($clientResult['clientid'])) {
        $errMsg = $clientResult['message'] ?? 'Failed to register client profile in billing system.';
        echo json_encode([
            'status'  => 'error',
            'message' => $errMsg
        ]);
        exit;
    }

    $clientId = $clientResult['clientid'];

    // 3. Insert/sync to tblclients if local database is connected
    try {
        if (isset($mysqli) && !$mysqli->connect_error) {
            $hashedPass = password_hash($password, PASSWORD_DEFAULT);
            $ins = $mysqli->prepare("INSERT INTO tblclients (firstname, lastname, companyname, email, password, phonenumber, address1, address2, city, state, postcode, country, status, datecreated) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Active', NOW())");
            if ($ins) {
                $ins->bind_param('ssssssssssss', $firstName, $lastName, $companyName, $email, $hashedPass, $phoneNumber, $address1, $address2, $city, $state, $postcode, $country);
                $ins->execute();
                if ($ins->insert_id > 0) {
                    $clientId = $ins->insert_id;
                }
                $ins->close();
            }
        }
    } catch (Exception $e) {}

    $fullName = trim($firstName . ' ' . $lastName);

    // Set authenticated session
    $_SESSION['cf_client_logged_in'] = true;
    $_SESSION['cf_client_id']        = $clientId;
    $_SESSION['cf_client_email']     = $email;
    $_SESSION['cf_client_name']      = $fullName;

    echo json_encode([
        'status'       => 'success',
        'message'      => 'Account created successfully! Welcome, ' . $fullName,
        'client_id'    => $clientId,
        'client_name'  => $fullName,
        'client_email' => $email
    ]);
    exit;
}

// =========================================================================
// ACTION: LOGOUT
// =========================================================================
if ($action === 'logout') {
    unset($_SESSION['cf_client_logged_in']);
    unset($_SESSION['cf_client_id']);
    unset($_SESSION['cf_client_name']);
    unset($_SESSION['cf_client_email']);
    echo json_encode([
        'status'  => 'success',
        'message' => 'Signed out safely.'
    ]);
    exit;
}

// 1. Validate Product or Domain-Only Selection
$pid = trim($data['pid'] ?? '');
$hasHosting = (!empty($pid) && $pid !== 'none' && isset($CF_PRODUCTS[$pid]));

// Domain Details
$domainType = strtolower(trim($data['domain_type'] ?? 'subdomain'));
$domainName = strtolower(trim($data['domain'] ?? ''));
$domainTld  = strtolower(trim($data['domain_tld'] ?? ''));

if (!empty($domainTld) && !str_starts_with($domainTld, '.')) {
    $domainTld = '.' . $domainTld;
}

$fullDomain = '';
$domainFee = 0.00;

if ($domainType === 'register') {
    if (empty($domainName)) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Please enter the domain name you wish to register.'
        ]);
        exit;
    }
    // Clean domain
    $domainName = preg_replace('#^https?://#i', '', $domainName);
    $domainName = preg_replace('#^www\.#i', '', $domainName);
    if (!str_contains($domainName, '.') && !empty($domainTld)) {
        $fullDomain = $domainName . $domainTld;
    } else {
        $fullDomain = $domainName;
    }

    $tldKey = ltrim(substr($fullDomain, strrpos($fullDomain, '.')), '.');
    $domainFee = $CF_TLDS[$tldKey]['price'] ?? 13.57;
} elseif ($domainType === 'existing' || $domainType === 'transfer') {
    $fullDomain = !empty($domainName) ? $domainName : 'mybusiness.com';
} else {
    // Free Subdomain
    $subPart = !empty($domainName) ? preg_replace('/[^a-z0-9\-]/', '', $domainName) : 'client-' . rand(100, 999);
    $fullDomain = $subPart . '.cloudflox.site';
}

// Ensure either hosting or a domain is chosen
if (!$hasHosting && $domainType !== 'register' && $domainType !== 'transfer') {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Please select either a hosting plan or a domain name to register.'
    ]);
    exit;
}

// Plan & Pricing Calculations
$billingCycle = strtolower(trim($data['billingcycle'] ?? 'monthly'));
if (!in_array($billingCycle, ['monthly', 'annually', 'yearly'])) {
    $billingCycle = 'monthly';
}
$isYearly = ($billingCycle === 'annually' || $billingCycle === 'yearly');

if ($hasHosting) {
    $plan = $CF_PRODUCTS[$pid];
    $planName = $plan['name'];
    $planCategory = $plan['category'];
    $planPrice = $isYearly ? $plan['yearly'] : $plan['monthly'];
} else {
    $plan = null;
    $planName = 'Domain Registration Only';
    $planCategory = 'Global Domains';
    $planPrice = 0.00;
}

$totalPrice = $planPrice + $domainFee;

// 2. Customer Account Handling (Already Authenticated vs Log In vs Create Account)
$clientId = !empty($_SESSION['cf_client_id']) ? $_SESSION['cf_client_id'] : (!empty($data['client_id']) ? intval($data['client_id']) : null);

if (!$clientId) {
    $accountAction = strtolower(trim($data['account_action'] ?? 'register'));

    if ($accountAction === 'login') {
        $loginEmail = trim($data['login_email'] ?? $data['email'] ?? '');
        $loginPass  = trim($data['login_password'] ?? $data['password'] ?? '');

        if (empty($loginEmail) || !filter_var($loginEmail, FILTER_VALIDATE_EMAIL)) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'Please enter a valid account email address.'
            ]);
            exit;
        }
        if (empty($loginPass)) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'Please enter your account password.'
            ]);
            exit;
        }

        $authResult = WHMCSClient::validateLogin($loginEmail, $loginPass);
        if (empty($authResult['clientid'])) {
            $errMsg = $authResult['message'] ?? 'Invalid login credentials. Please check your email and password.';
            echo json_encode([
                'status'  => 'error',
                'message' => $errMsg
            ]);
            exit;
        }
        $clientId = $authResult['clientid'];
    } else {
        // New Account Registration
        $firstName   = trim($data['firstname'] ?? '');
        $lastName    = trim($data['lastname'] ?? '');
        $email       = trim($data['email'] ?? '');
        $phoneNumber = trim($data['phonenumber'] ?? '');
        $password    = trim($data['password'] ?? '');
        $country     = strtoupper(trim($data['country'] ?? 'LK'));
        $city        = trim($data['city'] ?? 'Colombo');
        $address1    = trim($data['address1'] ?? 'Main Street');

        $companyName = trim($data['companyname'] ?? '');
        $address2    = trim($data['address2'] ?? '');
        $state       = trim($data['state'] ?? 'Western');
        $postcode    = trim($data['postcode'] ?? '00100');

        if (empty($firstName) || empty($lastName)) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'Please provide both your First and Last Name.'
            ]);
            exit;
        }
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'Please enter a valid email address.'
            ]);
            exit;
        }
        if (empty($phoneNumber)) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'Please provide a contact phone number.'
            ]);
            exit;
        }
        if (empty($password) || strlen($password) < 6) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'Password must be at least 6 characters long.'
            ]);
            exit;
        }

        $clientResult = WHMCSClient::addOrGetClient([
            'firstname'   => $firstName,
            'lastname'    => $lastName,
            'companyname' => $companyName,
            'email'       => $email,
            'phonenumber' => $phoneNumber,
            'password'    => $password,
            'country'     => $country,
            'city'        => $city,
            'address1'    => $address1,
            'address2'    => $address2,
            'state'       => $state,
            'postcode'    => $postcode
        ]);

        if (empty($clientResult['clientid'])) {
            $errMsg = $clientResult['message'] ?? 'Failed to register client profile in billing system.';
            echo json_encode([
                'status'  => 'error',
                'message' => $errMsg
            ]);
            exit;
        }
        $clientId = $clientResult['clientid'];
    }
}

// Payment Method: Card, PayHere, PayPal (matching cfwhmcs gateways)
$paymentMethod = strtolower(trim($data['paymentmethod'] ?? 'card'));
$validMethods = ['card', 'payhere', 'paypal'];
if (!in_array($paymentMethod, $validMethods)) {
    $paymentMethod = 'card';
}

// Map to WHMCS gateway modules in cfwhmcs:
// - 'payheresdk' is the active gateway in cfwhmcs for PayHere / Visa / Master
// - 'paypal' is the active gateway in cfwhmcs for PayPal
$whmcsGateway = 'payheresdk';
if ($paymentMethod === 'paypal') {
    $whmcsGateway = 'paypal';
} elseif ($paymentMethod === 'payhere') {
    $whmcsGateway = 'payheresdk';
} elseif ($paymentMethod === 'card') {
    // In cfwhmcs, payheresdk handles Visa, Master, Amex
    $whmcsGateway = 'payheresdk';
}

// 3. Prepare WHMCS Order Payload
$orderPayload = [
    'clientid'      => $clientId,
    'paymentmethod' => $whmcsGateway,
    'noinvoice'     => false,
    'noemail'       => false
];

if ($hasHosting) {
    $orderPayload['pid']          = [$pid];
    $orderPayload['domain']       = [$fullDomain];
    $orderPayload['billingcycle'] = [$isYearly ? 'annually' : 'monthly'];
} else {
    $orderPayload['domain']       = [$fullDomain];
    $orderPayload['billingcycle'] = ['annually'];
}

if ($domainType === 'register') {
    $orderPayload['domaintype'] = ['register'];
    $orderPayload['regperiod']  = [1];
} elseif ($domainType === 'transfer') {
    $orderPayload['domaintype'] = ['transfer'];
    $orderPayload['regperiod']  = [1];
}

// 4. Create Order in WHMCS
$orderResult = WHMCSClient::createOrder($orderPayload);

if (empty($orderResult['orderid'])) {
    $errMsg = $orderResult['message'] ?? 'Failed to place order in billing engine.';
    echo json_encode([
        'status'  => 'error',
        'message' => $errMsg
    ]);
    exit;
}

$orderId   = $orderResult['orderid'];
$invoiceId = 0;

// If local database is connected, insert genuine invoice record into tblinvoices
try {
    $mysqli = @new mysqli('127.0.0.1', 'cfwhmcs_whmcs', '+V)Rb%K3wFiK', 'cfwhmcs_whmcs3');
    if (!$mysqli->connect_error) {
        $today   = date('Y-m-d');
        $dueDate = date('Y-m-d', strtotime('+7 days'));
        $now     = date('Y-m-d H:i:s');
        $invStmt = $mysqli->prepare("INSERT INTO tblinvoices (userid, invoicenum, date, duedate, datepaid, subtotal, credit, tax, tax2, total, taxrate, taxrate2, status, paymentmethod, notes, created_at, updated_at) VALUES (?, '', ?, ?, '0000-00-00 00:00:00', ?, 0.00, 0.00, 0.00, ?, 0.000, 0.000, 'Unpaid', ?, '', ?, ?)");
        if ($invStmt) {
            $invStmt->bind_param('isssssss', $clientId, $today, $dueDate, $totalPrice, $totalPrice, $whmcsGateway, $now, $now);
            $invStmt->execute();
            if ($invStmt->insert_id > 0) {
                $invoiceId = $invStmt->insert_id;

                $itemDesc = $planName . ($fullDomain ? ' - ' . $fullDomain : '');
                $relType  = $hasHosting ? 'Hosting' : 'Item';
                $itemStmt = $mysqli->prepare("INSERT INTO tblinvoiceitems (invoiceid, userid, type, relid, description, amount, taxed, duedate, paymentmethod) VALUES (?, ?, ?, 0, ?, ?, 0, ?, ?)");
                if ($itemStmt) {
                    $itemStmt->bind_param('iisssss', $invoiceId, $clientId, $relType, $itemDesc, $totalPrice, $dueDate, $whmcsGateway);
                    $itemStmt->execute();
                    $itemStmt->close();
                }
            }
            $invStmt->close();
        }
    }
} catch (Exception $e) {}

if (empty($invoiceId)) {
    $invoiceId = $orderResult['invoiceid'] ?? ($orderId + 100);
}

// Generate Invoice URL in CloudFlox main site
require_once __DIR__ . '/../include/url.inc.php';
$invoiceUrl = $url . "invoice?id=" . urlencode($invoiceId) . "&gateway=" . urlencode($paymentMethod) . "&paynow=1";
$clientAreaUrl = $url . "clientarea";

$proto = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || ($_SERVER['SERVER_PORT'] ?? 80) == 443) ? 'https' : 'http';
$hostHeader = $_SERVER['HTTP_HOST'] ?? 'localhost:8080';
if (substr($url, 0, 4) === 'http') {
    $fullSiteUrl = rtrim($url, '/') . '/';
} else {
    $fullSiteUrl = $proto . '://' . $hostHeader . '/' . ltrim($url, '/');
    $fullSiteUrl = rtrim($fullSiteUrl, '/') . '/';
}
$fullInvoiceUrl = $fullSiteUrl . "invoice?id=" . urlencode($invoiceId) . "&gateway=" . urlencode($paymentMethod) . "&paynow=1";

// PayHere SDK Data (synced with active gateway configuration)
$payhereData = null;
if ($paymentMethod === 'payhere' || $paymentMethod === 'card') {
    $lkrRate = defined('CF_PAYHERE_EXCHANGE_RATE') ? CF_PAYHERE_EXCHANGE_RATE : 335.00;
    $amountLkr = number_format($totalPrice * $lkrRate, 2, '.', '');
    $merchantId = defined('CF_PAYHERE_MERCHANT_ID') ? CF_PAYHERE_MERCHANT_ID : '230744';
    $merchantSecret = defined('CF_PAYHERE_MERCHANT_SECRET') ? CF_PAYHERE_MERCHANT_SECRET : 'Mjc4NjU2NTM4MDM5OTA5NTg3NDAyNzAyMzk2Mzc1MTM1ODYwMTUy';
    $payHash = strtoupper(md5($merchantId . $invoiceId . $amountLkr . 'LKR' . strtoupper(md5($merchantSecret))));

    $cName = $_SESSION['cf_client_name'] ?? 'CloudFlox Customer';
    $cEmail = $_SESSION['cf_client_email'] ?? 'billing@cloudflox.com';
    $nameParts = explode(' ', trim($cName));
    $fName = $nameParts[0] ?? 'CloudFlox';
    $lName = count($nameParts) > 1 ? implode(' ', array_slice($nameParts, 1)) : 'Customer';

    $payhereData = [
        'sandbox'     => defined('CF_PAYHERE_SANDBOX') ? CF_PAYHERE_SANDBOX : false,
        'merchant_id' => $merchantId,
        'return_url'  => $fullInvoiceUrl . '&paid=1',
        'cancel_url'  => $fullInvoiceUrl . '&cancelled=1',
        'notify_url'  => $fullSiteUrl . 'api/payment-notify.php',
        'first_name'  => $fName,
        'last_name'   => $lName,
        'email'       => $cEmail,
        'phone'       => '+94700000000',
        'address'     => 'Main Street',
        'city'        => 'Colombo',
        'country'     => 'Sri Lanka',
        'order_id'    => (string)$invoiceId,
        'items'       => $planName . ($fullDomain ? ' (' . $fullDomain . ')' : ''),
        'currency'    => 'LKR',
        'amount'      => $amountLkr,
        'hash'        => $payHash
    ];
}

echo json_encode([
    'status'          => 'success',
    'message'         => 'Order successfully created! Your cloud server is being prepared.',
    'order_id'        => $orderId,
    'invoice_id'      => $invoiceId,
    'client_id'       => $clientId,
    'plan_name'       => $planName,
    'category'        => $planCategory,
    'domain'          => $fullDomain,
    'domain_type'     => $domainType,
    'billing_cycle'   => $isYearly ? 'Annual Billing (20% Saved)' : 'Monthly Billing',
    'plan_price'      => number_format($planPrice, 2),
    'domain_fee'      => number_format($domainFee, 2),
    'total'           => number_format($totalPrice, 2),
    'currency'        => 'USD',
    'payment_method'  => strtoupper($paymentMethod),
    'whmcs_gateway'   => $whmcsGateway,
    'payhere_data'    => $payhereData,
    'invoice_url'     => $invoiceUrl,
    'client_area_url' => $clientAreaUrl,
    'demo_mode'       => !empty($orderResult['demo_mode']) || CF_CHECKOUT_DEMO_MODE,
    'notice'          => CF_CHECKOUT_DEMO_MODE ? 'Demo Mode Active: Enter your WHMCS API keys in api/config.php for live production processing.' : ''
]);
