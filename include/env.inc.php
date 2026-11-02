<?php
/**
 * CloudFlox Environment Variable Loader & Central Configuration Bridge
 * -------------------------------------------------------------------------
 * Loads variables from root .env file into $_ENV, $_SERVER, and defines
 * secure global constants for database, mail, payments, and external APIs.
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
                    $_SERVER[$key] = $val;
                    $_ENV[$key]    = $val;
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

// Helper function to safely fetch an environment variable with default fallback
if (!function_exists('cf_env')) {
    function cf_env($key, $default = null) {
        $val = getenv($key);
        if ($val !== false && $val !== '') return $val;
        if (isset($_ENV[$key]) && $_ENV[$key] !== '') return $_ENV[$key];
        if (isset($_SERVER[$key]) && $_SERVER[$key] !== '') return $_SERVER[$key];
        return $default;
    }
}

// -------------------------------------------------------------------------
// 1. Database Credentials Constants
// -------------------------------------------------------------------------
if (!defined('CF_DB_HOST')) define('CF_DB_HOST', cf_env('DB_HOST', '127.0.0.1'));
if (!defined('CF_DB_PORT')) define('CF_DB_PORT', (int)cf_env('DB_PORT', 3306));
if (!defined('CF_DB_USER')) define('CF_DB_USER', cf_env('DB_USER', 'cfwhmcs_whmcs'));
if (!defined('CF_DB_PASS')) define('CF_DB_PASS', cf_env('DB_PASS', ''));
if (!defined('CF_DB_NAME')) define('CF_DB_NAME', cf_env('DB_NAME', 'cfwhmcs_whmcs3'));

// Central Database Connection Helper
if (!function_exists('cf_get_db')) {
    function cf_get_db() {
        static $db = null;
        if ($db !== null) {
            try {
                if (@$db->ping()) {
                    return $db;
                }
            } catch (Exception $e) {}
        }

        $host = CF_DB_HOST;
        $user = CF_DB_USER;
        $pass = CF_DB_PASS;
        $name = CF_DB_NAME;
        $port = (int)CF_DB_PORT;

        $mysqli = @new mysqli($host, $user, $pass, $name, $port);
        if ($mysqli->connect_error) {
            if ($host === '127.0.0.1') {
                $mysqli = @new mysqli('localhost', $user, $pass, $name, $port);
            } elseif ($host === 'localhost') {
                $mysqli = @new mysqli('127.0.0.1', $user, $pass, $name, $port);
            }
        }

        if ($mysqli && !$mysqli->connect_error) {
            $mysqli->set_charset('utf8mb4');
            $db = $mysqli;
            return $db;
        }

        return false;
    }
}

// -------------------------------------------------------------------------
// 2. Cloudflare Turnstile CAPTCHA Keys
// -------------------------------------------------------------------------
if (!defined('CF_TURNSTILE_SITE_KEY')) {
    define('CF_TURNSTILE_SITE_KEY', cf_env('CLOUDFLARE_TURNSTILE_SITE_KEY', ''));
}
if (!defined('CF_TURNSTILE_SECRET_KEY')) {
    define('CF_TURNSTILE_SECRET_KEY', cf_env('CLOUDFLARE_TURNSTILE_SECRET_KEY', ''));
}

// -------------------------------------------------------------------------
// 3. WHMCS External API Credentials
// -------------------------------------------------------------------------
if (!defined('WHMCS_API_URL')) {
    define('WHMCS_API_URL', cf_env('WHMCS_API_URL', 'https://my.cloudflox.com/includes/api.php'));
}
if (!defined('WHMCS_API_IDENTIFIER')) {
    define('WHMCS_API_IDENTIFIER', cf_env('WHMCS_API_IDENTIFIER', ''));
}
if (!defined('WHMCS_API_SECRET')) {
    define('WHMCS_API_SECRET', cf_env('WHMCS_API_SECRET', ''));
}
if (!defined('WHMCS_API_ACCESS_KEY')) {
    define('WHMCS_API_ACCESS_KEY', cf_env('WHMCS_API_ACCESS_KEY', ''));
}

// -------------------------------------------------------------------------
// 4. Payment Gateways Configuration
// -------------------------------------------------------------------------
if (!defined('CF_PAYHERE_MERCHANT_ID')) {
    define('CF_PAYHERE_MERCHANT_ID', cf_env('CF_PAYHERE_MERCHANT_ID', ''));
}
if (!defined('CF_PAYHERE_MERCHANT_SECRET')) {
    define('CF_PAYHERE_MERCHANT_SECRET', cf_env('CF_PAYHERE_MERCHANT_SECRET', ''));
}
if (!defined('CF_PAYHERE_CURRENCY')) {
    define('CF_PAYHERE_CURRENCY', cf_env('CF_PAYHERE_CURRENCY', 'USD'));
}
if (!defined('CF_PAYHERE_EXCHANGE_RATE')) {
    define('CF_PAYHERE_EXCHANGE_RATE', (float)cf_env('CF_PAYHERE_EXCHANGE_RATE', 1.00));
}
if (!defined('CF_PAYHERE_SANDBOX')) {
    define('CF_PAYHERE_SANDBOX', filter_var(cf_env('CF_PAYHERE_SANDBOX', 'false'), FILTER_VALIDATE_BOOLEAN));
}
if (!defined('CF_PAYPAL_EMAIL')) {
    define('CF_PAYPAL_EMAIL', cf_env('CF_PAYPAL_EMAIL', 'info@cloudflox.com'));
}

// -------------------------------------------------------------------------
// 5. SMTP & Email Notification Settings
// -------------------------------------------------------------------------
if (!defined('CF_SMTP_HOST')) define('CF_SMTP_HOST', cf_env('SMTP_HOST', 'mail.cloudflox.com'));
if (!defined('CF_SMTP_PORT')) define('CF_SMTP_PORT', (int)cf_env('SMTP_PORT', 465));
if (!defined('CF_SMTP_SECURE')) define('CF_SMTP_SECURE', cf_env('SMTP_SECURE', 'ssl'));
if (!defined('CF_SMTP_USERNAME')) define('CF_SMTP_USERNAME', cf_env('SMTP_USERNAME', 'info@cloudflox.com'));
if (!defined('CF_SMTP_PASSWORD')) define('CF_SMTP_PASSWORD', cf_env('SMTP_PASSWORD', ''));
if (!defined('CF_MAIL_FROM_ADDRESS')) define('CF_MAIL_FROM_ADDRESS', cf_env('MAIL_FROM_ADDRESS', 'info@cloudflox.com'));
if (!defined('CF_MAIL_FROM_NAME')) define('CF_MAIL_FROM_NAME', cf_env('MAIL_FROM_NAME', 'CloudFlox LLC Hosting'));
if (!defined('CF_MAIL_REPLY_TO')) define('CF_MAIL_REPLY_TO', cf_env('MAIL_REPLY_TO', 'support@cloudflox.com'));

// -------------------------------------------------------------------------
// 6. Direct Server / WHM Integration (Optional)
// -------------------------------------------------------------------------
if (!defined('CF_WHM_API_TOKEN')) {
    define('CF_WHM_API_TOKEN', cf_env('CF_WHM_API_TOKEN', ''));
}

// -------------------------------------------------------------------------
// 7. Security & Admin Console Access Keys
// -------------------------------------------------------------------------
if (!defined('CF_ADMIN_ACCESS_KEY')) {
    define('CF_ADMIN_ACCESS_KEY', cf_env('CF_ADMIN_ACCESS_KEY', 'CloudFlox_Admin_2026_SecureKey'));
}
