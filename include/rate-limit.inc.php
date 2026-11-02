<?php
/**
 * CloudFlox Simple Rate Limiter
 * -------------------------------------------------------------------------
 * Tracks failed attempts by IP using session + flat file cache.
 * Blocks an IP after exceeding max attempts within a time window.
 */

if (!function_exists('cf_rate_check')) {
    /**
     * Check if the current IP is rate-limited for a given action key.
     *
     * @param string $key       Action key (e.g. 'login', 'admin_login')
     * @param int    $maxTries  Max allowed attempts before lockout
     * @param int    $window    Time window in seconds (default 300 = 5 min)
     * @return bool  true = allowed, false = blocked
     */
    function cf_rate_check(string $key, int $maxTries = 5, int $window = 300): bool {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $cacheDir = sys_get_temp_dir() . '/cf_ratelimit';
        if (!is_dir($cacheDir)) {
            @mkdir($cacheDir, 0700, true);
        }
        $file = $cacheDir . '/' . md5($key . '_' . $ip) . '.json';

        $data = [];
        if (file_exists($file)) {
            $raw = @file_get_contents($file);
            $data = $raw ? (json_decode($raw, true) ?: []) : [];
        }

        $now = time();
        // Remove attempts older than the window
        $data['attempts'] = array_filter($data['attempts'] ?? [], fn($t) => ($now - $t) < $window);

        if (count($data['attempts']) >= $maxTries) {
            return false; // Blocked
        }

        return true; // Allowed
    }

    /**
     * Record a failed attempt for the current IP.
     *
     * @param string $key Action key
     */
    function cf_rate_record_fail(string $key): void {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $cacheDir = sys_get_temp_dir() . '/cf_ratelimit';
        if (!is_dir($cacheDir)) {
            @mkdir($cacheDir, 0700, true);
        }
        $file = $cacheDir . '/' . md5($key . '_' . $ip) . '.json';

        $data = [];
        if (file_exists($file)) {
            $raw = @file_get_contents($file);
            $data = $raw ? (json_decode($raw, true) ?: []) : [];
        }

        $now = time();
        $window = 300;
        $data['attempts'] = array_values(array_filter($data['attempts'] ?? [], fn($t) => ($now - $t) < $window));
        $data['attempts'][] = $now;
        @file_put_contents($file, json_encode($data), LOCK_EX);
    }

    /**
     * Clear rate limit record after successful login.
     *
     * @param string $key Action key
     */
    function cf_rate_clear(string $key): void {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $cacheDir = sys_get_temp_dir() . '/cf_ratelimit';
        $file = $cacheDir . '/' . md5($key . '_' . $ip) . '.json';
        if (file_exists($file)) {
            @unlink($file);
        }
    }
}
