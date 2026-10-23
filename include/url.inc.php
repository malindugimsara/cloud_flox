<?php
$host = $_SERVER['HTTP_HOST'] ?? '';
$isLocal = (strpos($host, 'localhost') !== false || strpos($host, '127.0.0.1') !== false);
if ($isLocal) {
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    $parts = explode('/', trim($scriptName, '/'));
    if (count($parts) > 1) {
        $url = '/' . $parts[0] . '/';
    } else {
        $url = '/';
    }
} else {
    $url = 'https://cloudflox.com/';
}
