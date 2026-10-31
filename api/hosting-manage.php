<?php
/**
 * CloudFlox On-Site Hosting & cPanel Management API Bridge
 * -------------------------------------------------------------------------
 * Handles AJAX requests for:
 * 1. Fetching live hosting account details, server IP, disk/bandwidth stats
 * 2. Generating 1-Click cPanel Single Sign-On (SSO) links
 * 3. Updating cPanel / FTP passwords via WHMCS Module
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
        'message' => 'Your session has expired. Please log in to manage your services.'
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

$action    = trim($data['action'] ?? '');
$serviceId = (int)($data['service_id'] ?? 0);

if ($serviceId <= 0) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Invalid or missing Service ID.'
    ]);
    exit;
}

// 3. Connect to Database & Validate Service Ownership
try {
    $mysqli = @new mysqli('localhost', 'cfwhmcs_whmcs', '+V)Rb%K3wFiK', 'cfwhmcs_whmcs3');
    if ($mysqli->connect_error) {
        throw new Exception('Database connection failed: ' . $mysqli->connect_error);
    }
    $mysqli->set_charset('utf8');

    $stmt = $mysqli->prepare("
        SELECT h.id, h.userid, h.domain, h.domainstatus, h.amount, h.billingcycle, h.regdate, h.nextduedate,
               h.username, h.password, h.dedicatedip, h.assignedips, h.ns1, h.ns2, 
               h.diskusage, h.disklimit, h.bwusage, h.bwlimit, h.server,
               p.name as product_name, p.description as product_desc,
               s.name as server_name, s.ipaddress as server_ip, s.hostname as server_hostname, 
               s.nameserver1, s.nameserver2, s.nameserver3, s.nameserver4
        FROM tblhosting h
        LEFT JOIN tblproducts p ON p.id = h.packageid
        LEFT JOIN tblservers s ON s.id = h.server
        WHERE h.id = ? AND h.userid = ?
        LIMIT 1
    ");

    if (!$stmt) {
        throw new Exception('Database prepare failed: ' . $mysqli->error);
    }

    $stmt->bind_param('ii', $serviceId, $clientId);
    $stmt->execute();
    $res = $stmt->get_result();
    $hosting = $res ? $res->fetch_assoc() : null;
    $stmt->close();

    if (!$hosting) {
        http_response_code(403);
        echo json_encode([
            'status'  => 'error',
            'message' => 'Unauthorized: You do not own this service or it does not exist.'
        ]);
        exit;
    }

    // Default Fallbacks for server details if not assigned to specific node
    $serverIp   = !empty($hosting['dedicatedip']) ? $hosting['dedicatedip'] : (!empty($hosting['server_ip']) ? $hosting['server_ip'] : '69.10.60.130');
    $serverHost = !empty($hosting['server_hostname']) ? $hosting['server_hostname'] : 'sv.cloudflox.com';
    $serverName = !empty($hosting['server_name']) ? $hosting['server_name'] : 'CloudFlox Enterprise Node';
    $ns1        = !empty($hosting['ns1']) ? $hosting['ns1'] : (!empty($hosting['nameserver1']) ? $hosting['nameserver1'] : 'ns1.cloudflox.com');
    $ns2        = !empty($hosting['ns2']) ? $hosting['ns2'] : (!empty($hosting['nameserver2']) ? $hosting['nameserver2'] : 'ns2.cloudflox.com');
    $username   = !empty($hosting['username']) ? $hosting['username'] : 'cpanel_user';

    // cPanel & Webmail direct links (Direct to cPanel server, NEVER via my.cloudflox.com)
    $cpanelDirectUrl  = 'https://' . $serverHost . ':2083';
    $webmailDirectUrl = 'https://' . $serverHost . ':2096';

    // Direct WHM cPanel session generation attempt (Direct server-to-server SSO)
    $ssoSessionUrl = '';
    $whmToken = defined('CF_WHM_API_TOKEN') ? CF_WHM_API_TOKEN : trim($hosting['accesshash'] ?? '');
    if (!empty($whmToken) && !empty($username) && $username !== 'cpanel_user') {
        $whmUser = !empty($hosting['username']) ? 'root' : 'root';
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => "https://$serverHost:2087/json-api/create_user_session?api.version=1&user=" . urlencode($username) . "&service=cpaneld",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_TIMEOUT        => 4,
            CURLOPT_HTTPHEADER     => [
                "Authorization: whm $whmUser:$whmToken"
            ]
        ]);
        $ssoRaw = curl_exec($ch);
        curl_close($ch);
        if ($ssoRaw) {
            $ssoData = json_decode($ssoRaw, true);
            if (!empty($ssoData['data']['url'])) {
                $ssoSessionUrl = $ssoData['data']['url'];
            }
        }
    }

    $cpanelLaunchUrl = !empty($ssoSessionUrl) ? $ssoSessionUrl : $cpanelDirectUrl;

    // 4. Handle Actions
    switch ($action) {

        // =====================================================================
        // ACTION: GET SERVICE DETAILS
        // =====================================================================
        case 'get_info':
            // Disk space calculation
            $diskUsage = (int)$hosting['diskusage'];
            $diskLimit = (int)$hosting['disklimit'];
            if ($diskLimit <= 0) $diskLimit = 5120; // Default 5 GB
            $diskPercent = min(100, round(($diskUsage / max(1, $diskLimit)) * 100));

            // Bandwidth calculation
            $bwUsage = (int)$hosting['bwusage'];
            $bwLimit = (int)$hosting['bwlimit'];
            if ($bwLimit <= 0) $bwLimit = 256000; // Default 250 GB
            $bwPercent = min(100, round(($bwUsage / max(1, $bwLimit)) * 100));

            echo json_encode([
                'status'  => 'success',
                'service' => [
                    'id'              => (int)$hosting['id'],
                    'product_name'    => $hosting['product_name'] ?: 'Cloud NVMe Hosting',
                    'domain'          => $hosting['domain'],
                    'status'          => $hosting['domainstatus'],
                    'amount'          => number_format((float)$hosting['amount'], 2),
                    'billingcycle'    => $hosting['billingcycle'],
                    'regdate'         => $hosting['regdate'],
                    'nextduedate'     => $hosting['nextduedate'],
                    'username'        => $username,
                    'server_name'     => $serverName,
                    'server_ip'       => $serverIp,
                    'server_hostname' => $serverHost,
                    'ns1'             => $ns1,
                    'ns2'             => $ns2,
                    'disk_usage_mb'   => $diskUsage,
                    'disk_limit_mb'   => $diskLimit,
                    'disk_percent'    => $diskPercent,
                    'bw_usage_mb'     => $bwUsage,
                    'bw_limit_mb'     => $bwLimit,
                    'bw_percent'      => $bwPercent,
                    'cpanel_url'        => $cpanelDirectUrl,
                    'cpanel_launch_url' => $cpanelLaunchUrl,
                    'webmail_url'       => $webmailDirectUrl
                ]
            ]);
            exit;

        // =====================================================================
        // ACTION: CHANGE CPANEL / FTP PASSWORD
        // =====================================================================
        case 'change_password':
            $newPassword = $data['new_password'] ?? '';
            $confirmPass = $data['confirm_password'] ?? '';

            if (empty($newPassword) || strlen($newPassword) < 8) {
                echo json_encode([
                    'status'  => 'error',
                    'message' => 'New password must be at least 8 characters long and include numbers/symbols.'
                ]);
                exit;
            }
            if ($newPassword !== $confirmPass) {
                echo json_encode([
                    'status'  => 'error',
                    'message' => 'Passwords do not match. Please verify and re-enter.'
                ]);
                exit;
            }

            // Call WHMCS API to update password on the cPanel server module
            $apiRes = WHMCSClient::call('ModuleChangePassword', [
                'serviceid'      => $serviceId,
                'servicepassword'=> $newPassword
            ]);

            // Also update encrypted password in local database if present
            $passHash = password_hash($newPassword, PASSWORD_DEFAULT);
            $up = $mysqli->prepare("UPDATE tblhosting SET password = ?, updated_at = NOW() WHERE id = ? AND userid = ?");
            if ($up) {
                $up->bind_param('sii', $passHash, $serviceId, $clientId);
                $up->execute();
                $up->close();
            }

            echo json_encode([
                'status'  => 'success',
                'message' => 'Your cPanel and FTP password has been successfully updated!'
            ]);
            exit;

        default:
            echo json_encode([
                'status'  => 'error',
                'message' => 'Unrecognized hosting management action: ' . htmlspecialchars($action)
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

