<?php
/**
 * CloudFlox Environment Variable Loader
 * Loads .env variables into $_ENV, $_SERVER, and defines global constants.
 */

if (!function_exists('cf_load_env')) {
    function cf_load_env($filePath) {
        if (!file_exists($filePath) || !is_readable($filePath)) {
            return false;
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            return false;
        }

        foreach ($lines as $line) {
            $line = trim($line);

            // Skip comments and empty lines
            if ($line === '' || strpos($line, '#') === 0) {
                continue;
            }

            // Split into key and value
            $parts = explode('=', $line, 2);
            if (count($parts) === 2) {
                $key = trim($parts[0]);
                $val = trim($parts[1]);

                // Remove surrounding quotes if present
                if ((strpos($val, '"') === 0 && substr($val, -1) === '"') ||
                    (strpos($val, "'") === 0 && substr($val, -1) === "'")) {
                    $val = substr($val, 1, -1);
                }

                if (!empty($key)) {
                    if (!isset($_SERVER[$key])) {
                        $_SERVER[$key] = $val;
                    }
                    if (!isset($_ENV[$key])) {
                        $_ENV[$key] = $val;
                    }
                    putenv("{$key}={$val}");
                }
            }
        }
        return true;
    }
}

// Automatically load .env from website root
$envPath = dirname(__DIR__) . '/.env';
cf_load_env($envPath);

// Define global constants for Turnstile
if (!defined('CF_TURNSTILE_SITE_KEY')) {
    $siteKey = $_ENV['CLOUDFLARE_TURNSTILE_SITE_KEY'] ?? $_SERVER['CLOUDFLARE_TURNSTILE_SITE_KEY'] ?? getenv('CLOUDFLARE_TURNSTILE_SITE_KEY');
    define('CF_TURNSTILE_SITE_KEY', $siteKey ?: '0x4AAAAAAFsPGuQlAMYqY37j');
}

if (!defined('CF_TURNSTILE_SECRET_KEY')) {
    $secretKey = $_ENV['CLOUDFLARE_TURNSTILE_SECRET_KEY'] ?? $_SERVER['CLOUDFLARE_TURNSTILE_SECRET_KEY'] ?? getenv('CLOUDFLARE_TURNSTILE_SECRET_KEY');
    define('CF_TURNSTILE_SECRET_KEY', $secretKey ?: '0x4AAAAAAFsPGj4XQp1O1pc9PnrNzozwjKM');
}

