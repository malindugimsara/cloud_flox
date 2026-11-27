<?php
require_once __DIR__ . '/env.inc.php';
cf_secure_session_start();
$host = $_SERVER['HTTP_HOST'] ?? '';
$isLocal = function_exists('cf_is_local_dev') ? cf_is_local_dev() : false;
if ($isLocal) {
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    $parts = explode('/', trim($scriptName, '/'));
    if (count($parts) > 1 && !empty($parts[0]) && !preg_match('/\.php$/i', $parts[0])) {
        $url = '/' . $parts[0] . '/';
    } else {
        $url = '/';
    }
} else {
    $safeHost = preg_replace('/[^a-zA-Z0-9\.\-\:]/', '', $host);
    if (empty($safeHost) || strpos($safeHost, 'cloudflox.com') !== false) {
        $url = 'https://cloudflox.com/';
    } else {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || ($_SERVER['SERVER_PORT'] ?? 80) == 443) ? 'https://' : 'http://';
        $url = $protocol . $safeHost . '/';
    }
}
