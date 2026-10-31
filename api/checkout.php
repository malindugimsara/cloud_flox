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

if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    @session_start();
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

// =========================================================================
// ACTION: GET PROFILE DETAILS (For logged-in client editing)
// =========================================================================
if ($action === 'get_profile') {
    $clientId = !empty($_SESSION['cf_client_id']) ? (int)$_SESSION['cf_client_id'] : (int)($data['client_id'] ?? 0);
    if (!$clientId) {
        echo json_encode(['status' => 'error', 'message' => 'Not authenticated.']);
        exit;
    }
    $profile = [];
    try {
        $mysqli = @new mysqli('127.0.0.1', 'cfwhmcs_whmcs', '+V)Rb%K3wFiK', 'cfwhmcs_whmcs3');
        if (!$mysqli->connect_error) {
            $s = $mysqli->prepare("SELECT id, firstname, lastname, companyname, email, phonenumber, address1, address2, city, state, postcode, country FROM tblclients WHERE id = ? LIMIT 1");
            if ($s) {
                $s->bind_param('i', $clientId);
                $s->execute();
                $r = $s->get_result();
                if ($r && $row = $r->fetch_assoc()) {
                    $profile = $row;
                }
                $s->close();
            }
        }
    } catch (Exception $e) {}

    echo json_encode([
        'status'  => 'success',
        'profile' => $profile
    ]);
    exit;
}

// =========================================================================
// ACTION: UPDATE PROFILE (Edit personal and billing data)
// =========================================================================
if ($action === 'update_profile') {
    $clientId = !empty($_SESSION['cf_client_id']) ? (int)$_SESSION['cf_client_id'] : (int)($data['client_id'] ?? 0);
    if (!$clientId) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Please sign in to update your profile details.'
        ]);
        exit;
    }

    $firstName   = trim($data['firstname'] ?? '');
    $lastName    = trim($data['lastname'] ?? '');
    $companyName = trim($data['companyname'] ?? '');
    $phoneNumber = trim($data['phonenumber'] ?? '');
    $address1    = trim($data['address1'] ?? '');
    $address2    = trim($data['address2'] ?? '');
    $city        = trim($data['city'] ?? '');
    $state       = trim($data['state'] ?? '');
    $postcode    = trim($data['postcode'] ?? '');
    $country     = strtoupper(trim($data['country'] ?? 'LK'));

    if (empty($firstName) || empty($lastName)) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Please enter both your First Name and Last Name.'
        ]);
        exit;
    }
    if (empty($phoneNumber)) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Please enter a contact phone number.'
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

    // Update in MySQL tblclients
    try {
        $mysqli = @new mysqli('127.0.0.1', 'cfwhmcs_whmcs', '+V)Rb%K3wFiK', 'cfwhmcs_whmcs3');
        if (!$mysqli->connect_error) {
            $up = $mysqli->prepare("UPDATE tblclients SET firstname = ?, lastname = ?, companyname = ?, phonenumber = ?, address1 = ?, address2 = ?, city = ?, state = ?, postcode = ?, country = ?, updated_at = NOW() WHERE id = ?");
            if ($up) {
                $up->bind_param('ssssssssssi', $firstName, $lastName, $companyName, $phoneNumber, $address1, $address2, $city, $state, $postcode, $country, $clientId);
                $up->execute();
                $up->close();
            }

            // Optional password update
            if (!empty($data['password'])) {
                $newPass = $data['password'];
                if (strlen($newPass) >= 6) {
                    $hp = password_hash($newPass, PASSWORD_DEFAULT);
                    $upPass = $mysqli->prepare("UPDATE tblclients SET password = ? WHERE id = ?");
                    if ($upPass) {
                        $upPass->bind_param('si', $hp, $clientId);
                        $upPass->execute();
                        $upPass->close();
                    }
                }
            }
        }
    } catch (Exception $e) {}

    $fullName = trim($firstName . ' ' . $lastName);
    $_SESSION['cf_client_name'] = $fullName;

    echo json_encode([
        'status'       => 'success',
        'message'      => 'Your personal and billing details have been updated successfully!',
        'client_id'    => $clientId,
        'client_name'  => $fullName
    ]);
    exit;
}

// Fetch live WHMCS Domain and Hosting pricing
require_once __DIR__ . '/whmcs-pricing.php';
$liveDomains = WHMCSPricing::getDomainPricing();
$liveHosting = WHMCSPricing::getHostingProducts();

// 1. Validate Product or Domain-Only Selection
$pid = trim($data['pid'] ?? '');
$hasHosting = (!empty($pid) && $pid !== 'none' && (isset($liveHosting[$pid]) || is_numeric($pid)));

// Domain Details (Single or Multi-Domain Support)
$rawDomainsList = $data['domains'] ?? [];
$parsedDomains = [];

// Helper function to extract and sanitize domain
function sanitizeDomainEntry($rawName) {
    $rawName = preg_replace('#^https?://#i', '', $rawName);
    $rawName = preg_replace('#^www\.#i', '', $rawName);
    $rawName = preg_replace('#/.*$#', '', $rawName);
    $rawName = strtolower(trim($rawName));
    return preg_replace('/[^a-z0-9\.\-]/i', '', $rawName);
}

// 1. Process Multi-Domain Array if provided
if (is_array($rawDomainsList) && !empty($rawDomainsList)) {
    foreach ($rawDomainsList as $dItem) {
        if (is_string($dItem)) {
            $dName = sanitizeDomainEntry($dItem);
            $dType = 'register';
            $dYears = 1;
        } elseif (is_array($dItem)) {
            $dName = sanitizeDomainEntry($dItem['name'] ?? $dItem['domain'] ?? '');
            $dType = strtolower(trim($dItem['type'] ?? 'register'));
            if ($dType === 'existing') $dType = 'owndomain';
            $dYears = max(1, min(5, (int)($dItem['years'] ?? 1)));
        } else {
            continue;
        }

        if (empty($dName) || !str_contains($dName, '.')) continue;

        $tld = ltrim(substr($dName, strrpos($dName, '.')), '.');
        if ($dType === 'transfer') {
            $dYears = 1;
            $fee = (float)($liveDomains[$tld]['transfer'] ?? 12.73);
        } elseif ($dType === 'owndomain') {
            $fee = 0.00;
        } else {
            $yearlyRate = (float)($liveDomains[$tld]['register'] ?? $CF_TLDS[$tld]['price'] ?? 14.93);
            $fee = round($yearlyRate * $dYears, 2);
        }

        $parsedDomains[] = [
            'domain' => $dName,
            'type'   => $dType,
            'years'  => $dYears,
            'fee'    => $fee,
            'tld'    => $tld
        ];
    }
}

// 2. Fallback to Single Domain fields if parsedDomains is empty
if (empty($parsedDomains)) {
    $singleType = strtolower(trim($data['domain_type'] ?? 'register'));
    if ($singleType === 'existing') $singleType = 'owndomain';
    $singleName = sanitizeDomainEntry($data['domain'] ?? '');
    $singleTld  = strtolower(trim($data['domain_tld'] ?? ''));
    if (!empty($singleTld) && !str_starts_with($singleTld, '.')) {
        $singleTld = '.' . $singleTld;
    }

    if (!str_contains($singleName, '.') && !empty($singleTld)) {
        $singleFull = $singleName . $singleTld;
    } else {
        $singleFull = $singleName;
    }

    if (!empty($singleFull) && str_contains($singleFull, '.')) {
        $tld = ltrim(substr($singleFull, strrpos($singleFull, '.')), '.');
        $sYears = max(1, min(5, (int)($data['domain_years'] ?? 1)));

        if ($singleType === 'transfer') {
            $sYears = 1;
            $fee = (float)($liveDomains[$tld]['transfer'] ?? 12.73);
        } elseif ($singleType === 'owndomain') {
            $fee = 0.00;
        } else {
            $yearlyRate = (float)($liveDomains[$tld]['register'] ?? $CF_TLDS[$tld]['price'] ?? 14.93);
            $fee = round($yearlyRate * $sYears, 2);
        }

        $parsedDomains[] = [
            'domain' => $singleFull,
            'type'   => $singleType,
            'years'  => $sYears,
            'fee'    => $fee,
            'tld'    => $tld
        ];
    }
}

// Calculate total domain fees across all selected domains
$totalDomainFee = 0.00;
foreach ($parsedDomains as $pd) {
    $totalDomainFee += (float)$pd['fee'];
}
$domainFee = $totalDomainFee;

// Primary domain representation for hosting attachment and backward compatibility
$fullDomain = !empty($parsedDomains) ? $parsedDomains[0]['domain'] : '';
$domainType = !empty($parsedDomains) ? $parsedDomains[0]['type'] : 'register';
$domainYears = !empty($parsedDomains) ? $parsedDomains[0]['years'] : 1;
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
    $plan = $liveHosting[$pid] ?? null;
    $planName = $plan ? $plan['name'] : "Hosting Plan #{$pid}";
    $planCategory = $plan ? $plan['category'] : 'Cloud Hosting';
    $planPrice = $plan ? ($isYearly ? (float)$plan['yearly'] : (float)$plan['monthly']) : 0.00;
} else {
    $plan = null;
    $planName = ($domainType === 'transfer') ? 'Domain Transfer' : 'Domain Registration Only';
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

// Payment Method: Card (via PayHere), PayPal (matching cfwhmcs gateways)
$paymentMethod = strtolower(trim($data['paymentmethod'] ?? 'card'));
$validMethods = ['card', 'payhere', 'paypal'];
if (!in_array($paymentMethod, $validMethods)) {
    $paymentMethod = 'card';
}

// Map to WHMCS gateway modules in cfwhmcs:
// - 'payheresdk' is the active gateway in cfwhmcs for PayHere / Visa / Master
// - 'paypal' is the active gateway in cfwhmcs for PayPal
if ($paymentMethod === 'paypal') {
    $whmcsGateway = 'paypal';
} else {
    // In cfwhmcs, payheresdk handles all Card payments (Visa, Master, Amex)
    $whmcsGateway = 'payheresdk';
    $paymentMethod = 'card';
}

// 3. Prepare WHMCS Order Payload
$orderPayload = [
    'clientid'      => $clientId,
    'paymentmethod' => $whmcsGateway,
    'noinvoice'     => false,
    'noemail'       => false
];

if ($hasHosting) {
    // Map alias to WHMCS product ID (101->1, 201->2, 202->3, etc.)
    $whmcsPid = $pid;
    if ($pid === '101') $whmcsPid = '1';
    elseif ($pid === '201') $whmcsPid = '2';
    elseif ($pid === '202') $whmcsPid = '3';
    elseif ($pid === '203') $whmcsPid = '4';
    elseif ($pid === '301') $whmcsPid = '5';

    $orderPayload['pid']          = [$whmcsPid];
    $orderPayload['domain']       = [$fullDomain];
    $orderPayload['billingcycle'] = [$isYearly ? 'annually' : 'monthly'];
} else {
    $orderPayload['domain']       = [$fullDomain];
    $orderPayload['billingcycle'] = ['annually'];
}

if ($domainType === 'register') {
    $orderPayload['domaintype'] = ['register'];
    $orderPayload['regperiod']  = [$domainYears];
} elseif ($domainType === 'transfer') {
    $orderPayload['domaintype'] = ['transfer'];
    $orderPayload['regperiod']  = [1];
    if (!empty($eppCode)) {
        $orderPayload['eppcode'] = [$eppCode];
    }
} elseif ($domainType === 'owndomain') {
    $orderPayload['domaintype'] = ['owndomain'];
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

// If local database is connected, insert genuine order, hosting, domain, and invoice records
try {
    $mysqli = @new mysqli('127.0.0.1', 'cfwhmcs_whmcs', '+V)Rb%K3wFiK', 'cfwhmcs_whmcs3');
    if (!$mysqli->connect_error) {
        $today    = date('Y-m-d');
        $dueDate  = date('Y-m-d', strtotime('+7 days'));
        $now      = date('Y-m-d H:i:s');
        $clientIp = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

        // 1. Create Invoice
        $invStmt = $mysqli->prepare("INSERT INTO tblinvoices (userid, invoicenum, date, duedate, datepaid, subtotal, credit, tax, tax2, total, taxrate, taxrate2, status, paymentmethod, notes, created_at, updated_at) VALUES (?, '', ?, ?, '0000-00-00 00:00:00', ?, 0.00, 0.00, 0.00, ?, 0.000, 0.000, 'Unpaid', ?, '', ?, ?)");
        if ($invStmt) {
            $invStmt->bind_param('isssssss', $clientId, $today, $dueDate, $totalPrice, $totalPrice, $whmcsGateway, $now, $now);
            $invStmt->execute();
            if ($invStmt->insert_id > 0) {
                $invoiceId = $invStmt->insert_id;
            }
            $invStmt->close();
        }

        // 2. Create Order in tblorders
        $orderNum = rand(1000000000, 9999999999);
        $ordNotes = "Order via CloudFlox On-Site Checkout";
        $ordStmt  = $mysqli->prepare("INSERT INTO tblorders (ordernum, userid, contactid, date, nameservers, transfersecret, renewals, promocode, promotype, promovalue, orderdata, amount, paymentmethod, invoiceid, status, ipaddress, fraudmodule, fraudoutput, notes) VALUES (?, ?, 0, ?, '', '', '', '', '', '', '', ?, ?, ?, 'Pending', ?, '', '', ?)");
        if ($ordStmt) {
            $ordStmt->bind_param('sisdssis', $orderNum, $clientId, $now, $totalPrice, $whmcsGateway, $invoiceId, $clientIp, $ordNotes);
            $ordStmt->execute();
            if ($ordStmt->insert_id > 0) {
                $orderId = $ordStmt->insert_id;
            }
            $ordStmt->close();
        }

        $hostingServiceId = 0;
        $domainServiceId  = 0;

        // 3. Create Hosting Record in tblhosting
        if ($hasHosting) {
            // Clean username generation
            $domainPart = explode('.', $fullDomain)[0];
            $cleanUser  = preg_replace('/[^a-z0-9]/', '', strtolower($domainPart));
            if (empty($cleanUser) || is_numeric($cleanUser[0])) {
                $cpanelUser = 'cf' . substr($cleanUser . rand(10, 99), 0, 6);
            } else {
                $cpanelUser = substr($cleanUser, 0, 7) . rand(1, 9);
            }

            // High-entropy 12-char temporary password
            $charsUpper = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
            $charsLower = 'abcdefghjkmnpqrstuvwxyz';
            $charsNum   = '23456789';
            $charsSym   = '!@#$%&*';
            $tempPass = 'Cf' . $charsSym[rand(0, strlen($charsSym)-1)] . 
                        $charsUpper[rand(0, strlen($charsUpper)-1)] . 
                        $charsLower[rand(0, strlen($charsLower)-1)] . 
                        $charsNum[rand(0, strlen($charsNum)-1)] . 
                        $charsLower[rand(0, strlen($charsLower)-1)] . 
                        $charsNum[rand(0, strlen($charsNum)-1)] . 
                        $charsUpper[rand(0, strlen($charsUpper)-1)] . 
                        $charsSym[rand(0, strlen($charsSym)-1)] . 
                        $charsNum[rand(0, strlen($charsNum)-1)];
            $hashedPass = password_hash($tempPass, PASSWORD_DEFAULT);

            $serverId       = 1; // SV-CF1
            $cycle          = $isYearly ? 'Annually' : 'Monthly';
            $hostingNextDue = $isYearly ? date('Y-m-d', strtotime('+1 year')) : date('Y-m-d', strtotime('+1 month'));
            $hostingNotes   = "Auto-provisioned via CloudFlox Checkout. InitialPass: " . $tempPass;

            $hostStmt = $mysqli->prepare("INSERT INTO tblhosting (
                userid, orderid, packageid, server, regdate, domain, paymentmethod,
                firstpaymentamount, amount, billingcycle, nextduedate, nextinvoicedate,
                domainstatus, username, password, notes, subscriptionid, promoid,
                suspendreason, overideautosuspend, overidesuspenduntil, dedicatedip,
                assignedips, ns1, ns2, created_at, updated_at
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?,
                ?, ?, ?, ?, ?,
                'Pending', ?, ?, ?, '', 0,
                '', 0, '0000-00-00', '',
                '', 'ns1.cloudflox.com', 'ns2.cloudflox.com', ?, ?
            )");
            if ($hostStmt) {
                $hostStmt->bind_param(
                    'iiiisssddssssssss',
                    $clientId, $orderId, $whmcsPid, $serverId, $today, $fullDomain, $whmcsGateway,
                    $planPrice, $planPrice, $cycle, $hostingNextDue, $hostingNextDue,
                    $cpanelUser, $hashedPass, $hostingNotes, $now, $now
                );
                $hostStmt->execute();
                if ($hostStmt->insert_id > 0) {
                    $hostingServiceId = $hostStmt->insert_id;
                }
                $hostStmt->close();
            }
        }

        // 4. Create Domain Records in tbldomains for ALL selected domains
        $domainServiceIds = [];
        foreach ($parsedDomains as $pDom) {
            $curDomName  = $pDom['domain'];
            $curDomType  = $pDom['type'];
            $curDomYears = $pDom['years'];
            $curDomFee   = (float)$pDom['fee'];

            if ($curDomType === 'register' || $curDomType === 'transfer') {
                $domTypeFormatted = ($curDomType === 'transfer') ? 'Transfer' : 'Register';
                $domainExpiry     = date('Y-m-d', strtotime('+' . $curDomYears . ' years'));
                $domainNotes      = "Ordered via CloudFlox Checkout (" . $curDomYears . " yr)";

                $domStmt = $mysqli->prepare("INSERT INTO tbldomains (
                    userid, orderid, type, registrationdate, domain, firstpaymentamount,
                    recurringamount, registrar, registrationperiod, expirydate, subscriptionid,
                    promoid, status, nextduedate, nextinvoicedate, additionalnotes,
                    paymentmethod, dnsmanagement, emailforwarding, idprotection, donotrenew,
                    reminders, synced, created_at, updated_at
                ) VALUES (
                    ?, ?, ?, ?, ?, ?,
                    ?, 'namecheap', ?, ?, '',
                    0, 'Pending', ?, ?, ?,
                    ?, 1, 0, 1, 0,
                    '', 0, ?, ?
                )");
                if ($domStmt) {
                    $domStmt->bind_param(
                        'iisssddisssssss',
                        $clientId, $orderId, $domTypeFormatted, $today, $curDomName, $curDomFee,
                        $curDomFee, $curDomYears, $domainExpiry, $domainExpiry, $domainExpiry, $domainNotes,
                        $whmcsGateway, $now, $now
                    );
                    $domStmt->execute();
                    if ($domStmt->insert_id > 0) {
                        $domainServiceIds[$curDomName] = $domStmt->insert_id;
                    }
                    $domStmt->close();
                }
            }
        }

        // 5. Create Line Items in tblinvoiceitems linked with service IDs
        if ($invoiceId > 0) {
            if ($hasHosting) {
                $itemDesc = $planName . ($fullDomain ? ' - ' . $fullDomain : '');
                $itemStmt = $mysqli->prepare("INSERT INTO tblinvoiceitems (invoiceid, userid, type, relid, description, amount, taxed, duedate, paymentmethod) VALUES (?, ?, 'Hosting', ?, ?, ?, 0, ?, ?)");
                if ($itemStmt) {
                    $itemStmt->bind_param('iiisdss', $invoiceId, $clientId, $hostingServiceId, $itemDesc, $planPrice, $dueDate, $whmcsGateway);
                    $itemStmt->execute();
                    $itemStmt->close();
                }
            }

            // Create individual line item for EACH domain in tblinvoiceitems
            foreach ($parsedDomains as $pDom) {
                $curDomName  = $pDom['domain'];
                $curDomType  = $pDom['type'];
                $curDomYears = $pDom['years'];
                $curDomFee   = (float)$pDom['fee'];
                $curServiceId = $domainServiceIds[$curDomName] ?? 0;

                if ($curDomFee > 0 || $curDomType === 'register' || $curDomType === 'transfer') {
                    $domItemDesc = ($curDomType === 'transfer' ? 'Domain Transfer - ' : 'Domain Registration - ') . $curDomName . " ({$curDomYears} Yr" . ($curDomYears > 1 ? 's' : '') . ")";
                    $domRelType  = ($curDomType === 'transfer') ? 'DomainTransfer' : 'DomainRegister';
                    $dItemStmt   = $mysqli->prepare("INSERT INTO tblinvoiceitems (invoiceid, userid, type, relid, description, amount, taxed, duedate, paymentmethod) VALUES (?, ?, ?, ?, ?, ?, 0, ?, ?)");
                    if ($dItemStmt) {
                        $dItemStmt->bind_param('iisisdss', $invoiceId, $clientId, $domRelType, $curServiceId, $domItemDesc, $curDomFee, $dueDate, $whmcsGateway);
                        $dItemStmt->execute();
                        $dItemStmt->close();
                    }
                }
            }
        }    }
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
    $amountUsd = number_format($totalPrice, 2, '.', '');
    $merchantId = defined('CF_PAYHERE_MERCHANT_ID') ? CF_PAYHERE_MERCHANT_ID : '230744';
    $merchantSecret = defined('CF_PAYHERE_MERCHANT_SECRET') ? CF_PAYHERE_MERCHANT_SECRET : 'Mjc4NjU2NTM4MDM5OTA5NTg3NDAyNzAyMzk2Mzc1MTM1ODYwMTUy';
    $payHash = strtoupper(md5($merchantId . $invoiceId . $amountUsd . 'USD' . strtoupper(md5($merchantSecret))));

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
        'currency'    => 'USD',
        'amount'      => $amountUsd,
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
