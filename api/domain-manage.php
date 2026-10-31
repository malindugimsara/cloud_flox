<?php
/**
 * CloudFlox On-Site Domain Management API Bridge
 * -------------------------------------------------------------------------
 * Handles AJAX requests for:
 * 1. Fetching live nameservers & domain locking status
 * 2. Updating nameservers (CloudFlox Default Anycast vs Custom)
 * 3. Toggle Registrar Transfer Lock
 * 4. Requesting EPP / Auth code
 * 5. Toggle WHOIS Privacy Protection
 * 6. Toggle Auto-Renewal
 */

header('Content-Type: application/json; charset=utf-8');

if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    @session_start();
}

require_once __DIR__ . '/whmcs.php';

// 1. Authentication Guard
$clientId = (int)($_SESSION['cf_client_id'] ?? 0);
$isLoggedIn = !empty($_SESSION['cf_client_logged_in']) && $clientId > 0;

if (!$isLoggedIn) {
    http_response_code(401);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Your session has expired. Please log in to manage your domains.'
    ]);
    exit;
}

// 2. Parse JSON or POST payload
$raw = file_get_contents('php://input');
$data = [];
if (!empty($raw)) {
    $decoded = json_decode($raw, true);
    if (is_array($decoded)) {
        $data = $decoded;
    }
}
$data = array_merge($_GET, $_POST, $data);

$action   = trim($data['action'] ?? '');
$domainId = (int)($data['domain_id'] ?? 0);

if ($domainId <= 0) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Invalid or missing Domain ID.'
    ]);
    exit;
}

// 3. Connect to Database & Validate Domain Ownership
try {
    $mysqli = @new mysqli('127.0.0.1', 'cfwhmcs_whmcs', '+V)Rb%K3wFiK', 'cfwhmcs_whmcs3');
    if ($mysqli->connect_error) {
        throw new Exception('Database connection failed: ' . $mysqli->connect_error);
    }
    $mysqli->set_charset('utf8');

    $stmt = $mysqli->prepare("
        SELECT d.id, d.userid, d.domain, d.status, d.registrationdate, d.expirydate, 
               d.nextduedate, d.recurringamount, d.registrar, d.dnsmanagement, 
               d.idprotection, d.donotrenew,
               c.email, c.firstname, c.lastname
        FROM tbldomains d
        JOIN tblclients c ON c.id = d.userid
        WHERE d.id = ? AND d.userid = ?
        LIMIT 1
    ");

    if (!$stmt) {
        throw new Exception('Database prepare failed: ' . $mysqli->error);
    }

    $stmt->bind_param('ii', $domainId, $clientId);
    $stmt->execute();
    $res = $stmt->get_result();
    $domainInfo = $res ? $res->fetch_assoc() : null;
    $stmt->close();

    if (!$domainInfo) {
        http_response_code(403);
        echo json_encode([
            'status'  => 'error',
            'message' => 'Unauthorized: You do not own this domain or it does not exist.'
        ]);
        exit;
    }

    // Helper to get extra cached values
    $getCachedExtra = function($name) use ($mysqli, $domainId) {
        $st = $mysqli->prepare("SELECT value FROM tbldomains_extra WHERE domain_id = ? AND name = ? LIMIT 1");
        if ($st) {
            $st->bind_param('is', $domainId, $name);
            $st->execute();
            $r = $st->get_result();
            if ($row = $r->fetch_assoc()) {
                $st->close();
                return $row['value'];
            }
            $st->close();
        }
        return null;
    };

    // Helper to set extra cached values
    $setCachedExtra = function($name, $value) use ($mysqli, $domainId) {
        $st = $mysqli->prepare("
            INSERT INTO tbldomains_extra (domain_id, name, value, created_at, updated_at) 
            VALUES (?, ?, ?, NOW(), NOW())
            ON DUPLICATE KEY UPDATE value = VALUES(value), updated_at = NOW()
        ");
        if ($st) {
            $st->bind_param('iss', $domainId, $name, $value);
            $st->execute();
            $st->close();
        }
    };

    // 4. Handle Actions
    switch ($action) {

        // =====================================================================
        // ACTION: GET DOMAIN DETAILS & NAMESERVERS
        // =====================================================================
        case 'get_info':
            // Check cached nameservers
            $ns1 = $getCachedExtra('nameserver1');
            $ns2 = $getCachedExtra('nameserver2');
            $ns3 = $getCachedExtra('nameserver3') ?? '';
            $ns4 = $getCachedExtra('nameserver4') ?? '';

            // If not cached, attempt to query live WHMCS API
            if (empty($ns1)) {
                $whmcsNs = WHMCSClient::call('DomainGetNameservers', ['domainid' => $domainId]);
                if (!empty($whmcsNs['ns1'])) {
                    $ns1 = $whmcsNs['ns1'];
                    $ns2 = $whmcsNs['ns2'] ?? '';
                    $ns3 = $whmcsNs['ns3'] ?? '';
                    $ns4 = $whmcsNs['ns4'] ?? '';
                    // Cache them
                    $setCachedExtra('nameserver1', $ns1);
                    $setCachedExtra('nameserver2', $ns2);
                    $setCachedExtra('nameserver3', $ns3);
                    $setCachedExtra('nameserver4', $ns4);
                } else {
                    // Default to CloudFlox Anycast DNS
                    $ns1 = 'ns1.cloudflox.com';
                    $ns2 = 'ns2.cloudflox.com';
                    $ns3 = '';
                    $ns4 = '';
                }
            }

            // Lock status
            $lockStatus = $getCachedExtra('lock_status');
            if (empty($lockStatus)) {
                $lockRes = WHMCSClient::call('DomainGetLockingStatus', ['domainid' => $domainId]);
                if (!empty($lockRes['lockstatus'])) {
                    $lockStatus = strtolower($lockRes['lockstatus']);
                } else {
                    $lockStatus = 'locked'; // Safe default
                }
                $setCachedExtra('lock_status', $lockStatus);
            }

            $isDefaultNs = (
                strtolower(trim($ns1)) === 'ns1.cloudflox.com' &&
                strtolower(trim($ns2)) === 'ns2.cloudflox.com' &&
                empty($ns3) && empty($ns4)
            );

            echo json_encode([
                'status'        => 'success',
                'domain'        => [
                    'id'               => (int)$domainInfo['id'],
                    'name'             => $domainInfo['domain'],
                    'status'           => $domainInfo['status'],
                    'registrationdate' => $domainInfo['registrationdate'],
                    'expirydate'       => $domainInfo['expirydate'] ?: $domainInfo['nextduedate'],
                    'nextduedate'      => $domainInfo['nextduedate'],
                    'recurringamount'  => number_format((float)$domainInfo['recurringamount'], 2),
                    'idprotection'     => (int)$domainInfo['idprotection'],
                    'donotrenew'       => (int)$domainInfo['donotrenew'],
                    'auto_renew'       => ((int)$domainInfo['donotrenew'] === 0) ? 1 : 0
                ],
                'nameservers'   => [
                    'ns1'        => $ns1,
                    'ns2'        => $ns2,
                    'ns3'        => $ns3,
                    'ns4'        => $ns4,
                    'is_default' => $isDefaultNs
                ],
                'lock_status'   => $lockStatus
            ]);
            exit;

        // =====================================================================
        // ACTION: SAVE NAMESERVERS
        // =====================================================================
        case 'save_nameservers':
            $nsType = strtolower(trim($data['type'] ?? 'custom'));
            $ns1 = '';
            $ns2 = '';
            $ns3 = '';
            $ns4 = '';

            if ($nsType === 'default') {
                $ns1 = 'ns1.cloudflox.com';
                $ns2 = 'ns2.cloudflox.com';
                $ns3 = '';
                $ns4 = '';
            } else {
                $ns1 = strtolower(trim($data['ns1'] ?? ''));
                $ns2 = strtolower(trim($data['ns2'] ?? ''));
                $ns3 = strtolower(trim($data['ns3'] ?? ''));
                $ns4 = strtolower(trim($data['ns4'] ?? ''));

                // Validation
                $hostRegex = '/^([a-z0-9]([a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z]{2,}$/i';
                if (empty($ns1) || !preg_match($hostRegex, $ns1)) {
                    echo json_encode([
                        'status'  => 'error',
                        'message' => 'Please provide a valid primary nameserver (e.g. ns1.example.com).'
                    ]);
                    exit;
                }
                if (empty($ns2) || !preg_match($hostRegex, $ns2)) {
                    echo json_encode([
                        'status'  => 'error',
                        'message' => 'Please provide a valid secondary nameserver (e.g. ns2.example.com).'
                    ]);
                    exit;
                }
                if (!empty($ns3) && !preg_match($hostRegex, $ns3)) {
                    echo json_encode([
                        'status'  => 'error',
                        'message' => 'Nameserver 3 is not a valid hostname.'
                    ]);
                    exit;
                }
                if (!empty($ns4) && !preg_match($hostRegex, $ns4)) {
                    echo json_encode([
                        'status'  => 'error',
                        'message' => 'Nameserver 4 is not a valid hostname.'
                    ]);
                    exit;
                }
            }

            // Call WHMCS API to update at the registry
            $apiParams = [
                'domainid' => $domainId,
                'ns1'      => $ns1,
                'ns2'      => $ns2,
                'ns3'      => $ns3,
                'ns4'      => $ns4
            ];
            $res = WHMCSClient::call('DomainUpdateNameservers', $apiParams);

            // Update cache in database
            $setCachedExtra('nameserver1', $ns1);
            $setCachedExtra('nameserver2', $ns2);
            $setCachedExtra('nameserver3', $ns3);
            $setCachedExtra('nameserver4', $ns4);

            echo json_encode([
                'status'      => 'success',
                'message'     => 'Nameservers successfully updated! DNS changes will propagate globally across root servers within 2 to 24 hours.',
                'nameservers' => [
                    'ns1'        => $ns1,
                    'ns2'        => $ns2,
                    'ns3'        => $ns3,
                    'ns4'        => $ns4,
                    'is_default' => ($nsType === 'default')
                ]
            ]);
            exit;

        // =====================================================================
        // ACTION: TOGGLE REGISTRAR LOCK
        // =====================================================================
        case 'toggle_lock':
            $lockStatus = strtolower(trim($data['lockstatus'] ?? ''));
            if (!in_array($lockStatus, ['locked', 'unlocked'])) {
                echo json_encode([
                    'status'  => 'error',
                    'message' => 'Invalid lock status. Must be "locked" or "unlocked".'
                ]);
                exit;
            }

            // Call WHMCS API
            $apiRes = WHMCSClient::call('DomainUpdateLockingStatus', [
                'domainid'   => $domainId,
                'lockstatus' => $lockStatus
            ]);

            // Save cache
            $setCachedExtra('lock_status', $lockStatus);

            $msg = ($lockStatus === 'locked')
                ? 'Registrar Transfer Lock has been ENABLED. Your domain is safely protected against unauthorized transfer requests.'
                : 'Registrar Transfer Lock has been DISABLED. Your domain can now be transferred if you provide the EPP / Auth code.';

            echo json_encode([
                'status'      => 'success',
                'message'     => $msg,
                'lock_status' => $lockStatus
            ]);
            exit;

        // =====================================================================
        // ACTION: REQUEST EPP / AUTH CODE
        // =====================================================================
        case 'get_epp':
            $res = WHMCSClient::call('DomainRequestEPP', ['domainid' => $domainId]);

            $eppCode = $res['eppcode'] ?? '';
            if (empty($eppCode)) {
                // Some registrars don't return the code synchronously via API; they email it
                echo json_encode([
                    'status'  => 'success',
                    'message' => 'The EPP / Auth-Code transfer request has been submitted to the registry. The code will be dispatched to your registered email address (' . htmlspecialchars($domainInfo['email']) . ').',
                    'eppcode' => ''
                ]);
                exit;
            }

            echo json_encode([
                'status'  => 'success',
                'message' => 'EPP / Authorization code retrieved successfully.',
                'eppcode' => $eppCode
            ]);
            exit;

        // =====================================================================
        // ACTION: TOGGLE AUTO-RENEWAL
        // =====================================================================
        case 'toggle_autorenew':
            $autoRenew = !empty($data['auto_renew']) ? 1 : 0;
            $doNotRenew = $autoRenew ? 0 : 1;

            $up = $mysqli->prepare("UPDATE tbldomains SET donotrenew = ?, updated_at = NOW() WHERE id = ? AND userid = ?");
            if ($up) {
                $up->bind_param('iii', $doNotRenew, $domainId, $clientId);
                $up->execute();
                $up->close();
            }

            echo json_encode([
                'status'     => 'success',
                'message'    => $autoRenew 
                    ? 'Auto-renewal is now ENABLED. Your domain will be automatically invoiced and renewed before expiration.' 
                    : 'Auto-renewal has been DISABLED. You will need to renew this domain manually before its due date.',
                'auto_renew' => $autoRenew
            ]);
            exit;

        // =====================================================================
        // ACTION: TOGGLE WHOIS ID PROTECTION
        // =====================================================================
        case 'toggle_idprotect':
            $protect = !empty($data['protect']) ? 1 : 0;

            // Call WHMCS API
            $apiRes = WHMCSClient::call('DomainToggleIdProtect', [
                'domainid'  => $domainId,
                'idprotect' => $protect
            ]);

            // Update database
            $up = $mysqli->prepare("UPDATE tbldomains SET idprotection = ?, updated_at = NOW() WHERE id = ? AND userid = ?");
            if ($up) {
                $up->bind_param('iii', $protect, $domainId, $clientId);
                $up->execute();
                $up->close();
            }

            echo json_encode([
                'status'       => 'success',
                'message'      => $protect 
                    ? 'WHOIS Privacy Protection is now ACTIVE. Your personal contact details are hidden on public WHOIS lookup tools.' 
                    : 'WHOIS Privacy Protection is now DISABLED. Your contact details may appear in public registry lookups.',
                'idprotection' => $protect
            ]);
            exit;

        default:
            echo json_encode([
                'status'  => 'error',
                'message' => 'Unrecognized domain management action: ' . htmlspecialchars($action)
            ]);
            exit;
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Internal server error: ' . $e->getMessage()
    ]);
    exit;
}

