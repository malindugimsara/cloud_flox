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
            } catch (Throwable $e) {
                $db = null;
            }
        }

        $host = CF_DB_HOST;
        $user = CF_DB_USER;
        $pass = CF_DB_PASS;
        $name = CF_DB_NAME;
        $port = (int)CF_DB_PORT;

        $hostsToTry = [$host];
        if ($host === '127.0.0.1') {
            $hostsToTry[] = 'localhost';
        } elseif ($host === 'localhost') {
            $hostsToTry[] = '127.0.0.1';
        }

        foreach ($hostsToTry as $tryHost) {
            try {
                $mysqli = @new mysqli($tryHost, $user, $pass, $name, $port);
                if ($mysqli && !$mysqli->connect_error) {
                    $mysqli->set_charset('utf8mb4');
                    $db = $mysqli;
                    return $db;
                }
            } catch (Throwable $e) {
                error_log('CloudFlox DB connection failed (' . $tryHost . '): ' . $e->getMessage());
            }
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
if (!defined('CF_PAYPAL_SANDBOX')) {
    define('CF_PAYPAL_SANDBOX', filter_var(cf_env('CF_PAYPAL_SANDBOX', 'false'), FILTER_VALIDATE_BOOLEAN));
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
    define('CF_ADMIN_ACCESS_KEY', cf_env('CF_ADMIN_ACCESS_KEY', ''));
}
if (!defined('CF_APP_ENV')) {
    define('CF_APP_ENV', cf_env('APP_ENV', ''));
}

/**
 * Start a PHP session with hardened cookie security flags (HttpOnly, SameSite=Lax, Strict Mode, Secure on HTTPS).
 */
if (!function_exists('cf_secure_session_start')) {
    function cf_secure_session_start(): void {
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            @ini_set('session.use_strict_mode', '1');
            @ini_set('session.use_only_cookies', '1');
            $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['SERVER_PORT'] ?? 80) == 443);
            @session_set_cookie_params([
                'lifetime' => 0,
                'path'     => '/',
                'secure'   => $isHttps,
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
            @session_start();
        }
    }
}

/**
 * Determine whether the current request is genuinely running in local dev.
 * Requires loopback client/server addresses so public HTTP Host spoofing cannot bypass security.
 */
if (!function_exists('cf_is_local_dev')) {
    function cf_is_local_dev(): bool {
        if (defined('CF_APP_ENV') && strtolower((string)CF_APP_ENV) === 'production') {
            return false;
        }
        if (php_sapi_name() === 'cli') {
            return true;
        }
        $remoteAddr = $_SERVER['REMOTE_ADDR'] ?? '';
        $serverAddr = $_SERVER['SERVER_ADDR'] ?? '';
        $isPrivateOrLoopback = function ($ip) {
            if ($ip === '') return false;
            if (in_array($ip, ['127.0.0.1', '::1'], true)) return true;
            return (bool)preg_match('/^(192\.168\.|10\.|172\.(1[6-9]|2[0-9]|3[0-1])\.)/', $ip);
        };
        if (!$isPrivateOrLoopback($remoteAddr)) {
            return false;
        }
        if ($serverAddr !== '' && !$isPrivateOrLoopback($serverAddr)) {
            return false;
        }
        $host = $_SERVER['HTTP_HOST'] ?? '';
        return (
            strpos($host, 'localhost') !== false ||
            strpos($host, '127.0.0.1') !== false ||
            strpos($host, '::1') !== false ||
            preg_match('/^(192\.168\.|10\.)/', $host)
        );
    }
}

/**
 * Generate a per-invoice HMAC access token bound to invoice ID and owning client ID.
 */
if (!function_exists('cf_invoice_token')) {
    function cf_invoice_token(int $invoiceId, int $userId): string {
        $keyMaterial = implode('|', [
            defined('CF_ADMIN_ACCESS_KEY') ? CF_ADMIN_ACCESS_KEY : '',
            defined('CF_PAYHERE_MERCHANT_SECRET') ? CF_PAYHERE_MERCHANT_SECRET : '',
            defined('WHMCS_API_SECRET') ? WHMCS_API_SECRET : '',
            defined('CF_DB_PASS') ? CF_DB_PASS : '',
            'cf_inv_hmac_v1'
        ]);
        return substr(hash_hmac('sha256', "inv|{$invoiceId}|{$userId}", $keyMaterial), 0, 32);
    }
}

/**
 * Verify a Cloudflare Turnstile response token via siteverify API.
 * Returns true if CF_TURNSTILE_SECRET_KEY is not configured.
 */
if (!function_exists('cf_verify_turnstile')) {
    function cf_verify_turnstile(?string $token): bool {
        $secret = defined('CF_TURNSTILE_SECRET_KEY') ? trim((string)CF_TURNSTILE_SECRET_KEY) : '';
        if ($secret === '') {
            return true;
        }
        $token = trim((string)$token);
        if ($token === '') {
            return false;
        }

        $verifyUrl = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';
        $payload = [
            'secret'   => $secret,
            'response' => $token,
            'remoteip' => $_SERVER['REMOTE_ADDR'] ?? ''
        ];

        $raw = false;
        if (function_exists('curl_init')) {
            $ch = curl_init($verifyUrl);
            if ($ch) {
                curl_setopt_array($ch, [
                    CURLOPT_POST           => true,
                    CURLOPT_POSTFIELDS     => http_build_query($payload),
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_TIMEOUT        => 10,
                    CURLOPT_SSL_VERIFYPEER => true
                ]);
                $raw = curl_exec($ch);
                curl_close($ch);
            }
        } else {
            $ctx = stream_context_create([
                'http' => [
                    'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                    'method'  => 'POST',
                    'content' => http_build_query($payload),
                    'timeout' => 10
                ]
            ]);
            $raw = @file_get_contents($verifyUrl, false, $ctx);
        }

        if (!$raw) {
            return false;
        }
        $decoded = json_decode($raw, true);
        return is_array($decoded) && !empty($decoded['success']);
    }
}

/**
 * Check whether an email belongs to a common disposable/temporary email provider.
 */
if (!function_exists('cf_is_disposable_email')) {
    function cf_is_disposable_email(string $email): bool {
        $at = strrpos($email, '@');
        if ($at === false) {
            return false;
        }
        $domain = strtolower(trim(substr($email, $at + 1)));
        static $blocked = [
            'mailinator.com', 'tempmail.com', 'temp-mail.org', 'guerrillamail.com',
            '10minutemail.com', 'yopmail.com', 'throwawaymail.com', 'trashmail.com',
            'sharklasers.com', 'getnada.com', 'dispostable.com', 'maildrop.cc',
            'fakeinbox.com', 'mintemail.com', 'tempr.email', 'burnermail.io'
        ];
        return in_array($domain, $blocked, true);
    }
}
