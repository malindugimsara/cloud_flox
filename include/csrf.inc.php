<?php
/**
 * CloudFlox CSRF Token Protection Helper
 * -------------------------------------------------------------------------
 * Provides functions for generating and validating CSRF tokens per session.
 * Must be called AFTER session_start().
 */

/**
 * Generate (or return existing) CSRF token for this session.
 */
if (!function_exists('cf_csrf_token')) {
    function cf_csrf_token(): string {
        if (empty($_SESSION['cf_csrf_token'])) {
            $_SESSION['cf_csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['cf_csrf_token'];
    }
}

/**
 * Validate submitted CSRF token against the session token.
 * Returns true if valid, false otherwise.
 */
if (!function_exists('cf_csrf_verify')) {
    function cf_csrf_verify(string $submittedToken): bool {
        $sessionToken = $_SESSION['cf_csrf_token'] ?? '';
        if (empty($sessionToken) || empty($submittedToken)) {
            return false;
        }
        return hash_equals($sessionToken, $submittedToken);
    }
}

/**
 * Rotate the CSRF token (call after a successful form action).
 */
if (!function_exists('cf_csrf_rotate')) {
    function cf_csrf_rotate(): void {
        $_SESSION['cf_csrf_token'] = bin2hex(random_bytes(32));
    }
}

/**
 * Output a hidden CSRF input field.
 */
if (!function_exists('cf_csrf_field')) {
    function cf_csrf_field(): string {
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(cf_csrf_token()) . '">';
    }
}
