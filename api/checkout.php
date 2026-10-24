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

// Accept both JSON payload and $_POST
$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true);
if (!$data || !is_array($data)) {
    $data = $_POST;
}

// 1. Validate Required Fields
$pid = trim($data['pid'] ?? '');
if (empty($pid) || !isset($CF_PRODUCTS[$pid])) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Please select a valid hosting plan.'
    ]);
    exit;
}

$plan = $CF_PRODUCTS[$pid];
$billingCycle = strtolower(trim($data['billingcycle'] ?? 'monthly'));
if (!in_array($billingCycle, ['monthly', 'annually', 'yearly'])) {
    $billingCycle = 'monthly';
}
$isYearly = ($billingCycle === 'annually' || $billingCycle === 'yearly');

// Customer Details
$firstName   = trim($data['firstname'] ?? '');
$lastName    = trim($data['lastname'] ?? '');
$email       = trim($data['email'] ?? '');
$phoneNumber = trim($data['phonenumber'] ?? '');
$password    = trim($data['password'] ?? '');
$country     = strtoupper(trim($data['country'] ?? 'LK'));
$city        = trim($data['city'] ?? 'Colombo');
$address1    = trim($data['address1'] ?? 'Main Street');

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

if (empty($password) || strlen($password) < 6) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Password must be at least 6 characters long.'
    ]);
    exit;
}

// Domain Details
$domainType = strtolower(trim($data['domain_type'] ?? 'subdomain'));
$domainName = strtolower(trim($data['domain'] ?? ''));
$domainTld  = strtolower(trim($data['domain_tld'] ?? ''));

// Normalize domain
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

// Payment Method
$paymentMethod = strtolower(trim($data['paymentmethod'] ?? 'card'));
$validMethods = ['card', 'stripe', 'payhere', 'paypal', 'banktransfer'];
if (!in_array($paymentMethod, $validMethods)) {
    $paymentMethod = 'card';
}

// WHMCS gateway mapping
$whmcsGateway = 'stripe';
if ($paymentMethod === 'payhere') $whmcsGateway = 'payhere';
elseif ($paymentMethod === 'paypal') $whmcsGateway = 'paypal';
elseif ($paymentMethod === 'banktransfer') $whmcsGateway = 'banktransfer';

// Calculate Totals
$planPrice = $isYearly ? $plan['yearly'] : $plan['monthly'];
$totalPrice = $planPrice + $domainFee;

// 2. Call WHMCS Client API: Create or Lookup Client
$clientResult = WHMCSClient::addOrGetClient([
    'firstname'   => $firstName,
    'lastname'    => $lastName,
    'email'       => $email,
    'phonenumber' => $phoneNumber,
    'password'    => $password,
    'country'     => $country,
    'city'        => $city,
    'address1'    => $address1
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

// 3. Prepare WHMCS Order Payload
$orderPayload = [
    'clientid'      => $clientId,
    'pid'           => [$pid],
    'domain'        => [$fullDomain],
    'billingcycle'  => [$isYearly ? 'annually' : 'monthly'],
    'paymentmethod' => $whmcsGateway,
    'noinvoice'     => false,
    'noemail'       => false
];

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
$invoiceId = $orderResult['invoiceid'] ?? ($orderId + 100);

// Generate URLs
$invoiceUrl = "https://my.cloudflox.com/viewinvoice.php?id=" . urlencode($invoiceId);
$clientAreaUrl = "https://my.cloudflox.com/clientarea.php";

echo json_encode([
    'status'          => 'success',
    'message'         => 'Order successfully created! Your cloud server is being prepared.',
    'order_id'        => $orderId,
    'invoice_id'      => $invoiceId,
    'client_id'       => $clientId,
    'plan_name'       => $plan['name'],
    'category'        => $plan['category'],
    'domain'          => $fullDomain,
    'domain_type'     => $domainType,
    'billing_cycle'   => $isYearly ? 'Annual Billing (20% Saved)' : 'Monthly Billing',
    'plan_price'      => number_format($planPrice, 2),
    'domain_fee'      => number_format($domainFee, 2),
    'total'           => number_format($totalPrice, 2),
    'currency'        => 'USD',
    'payment_method'  => strtoupper($paymentMethod),
    'invoice_url'     => $invoiceUrl,
    'client_area_url' => $clientAreaUrl,
    'demo_mode'       => !empty($orderResult['demo_mode']) || CF_CHECKOUT_DEMO_MODE,
    'notice'          => CF_CHECKOUT_DEMO_MODE ? 'Demo Mode Active: Enter your WHMCS API keys in api/config.php for live production processing.' : ''
]);
