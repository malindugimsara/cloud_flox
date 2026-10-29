<?php
if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    session_start();
}
$host = $_SERVER['HTTP_HOST'] ?? '';
$isLocal = (strpos($host, 'localhost') !== false || strpos($host, '127.0.0.1') !== false || preg_match('/^192\.168\./', $host) || preg_match('/^10\./', $host));
if ($isLocal) {
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    $parts = explode('/', trim($scriptName, '/'));
    if (count($parts) > 1 && !empty($parts[0]) && !preg_match('/\.php$/i', $parts[0])) {
        $url = '/' . $parts[0] . '/';
    } else {
        $url = '/';
    }
} else {
    if (strpos($host, 'cloudflox.com') !== false) {
        $url = 'https://cloudflox.com/';
    } else {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || ($_SERVER['SERVER_PORT'] ?? 80) == 443) ? 'https://' : 'http://';
        $url = $protocol . $host . '/';
    }
}
