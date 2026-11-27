<?php
require_once __DIR__ . '/env.inc.php';
cf_secure_session_start();
// Ensure $url and CSRF helpers are loaded
if (!isset($url)) {
    include_once __DIR__ . '/url.inc.php';
}
require_once __DIR__ . '/csrf.inc.php';

$cfIsLoggedIn = !empty($_SESSION['cf_client_logged_in']);
$cfClientName = $_SESSION['cf_client_name'] ?? 'Client Area';
?>
<!--preloader start-->
<div class="preloader bg-light-subtle" id="cfMainPreloader">
    <div class="preloader-wrap">
        <img src="<?php echo $url ?? ''; ?>assets/imgs/logos/cloud-flox-main.webp" alt="CloudFlox" class="img-fluid preloader-logo" width="240" height="56">
        <div class="loading-bar"></div>
    </div>
</div>
<script>
(function() {
    function cfDismissPreloader() {
        var p = document.getElementById('cfMainPreloader') || document.querySelector('.preloader');
        if (p && p.style.display !== 'none') {
            p.style.opacity = '0';
            p.style.transition = 'opacity 0.25s ease';
            setTimeout(function() { 
                if (p) p.style.display = 'none'; 
            }, 260);
        }
    }
    if (document.readyState === 'complete' || document.readyState === 'interactive') {
        setTimeout(cfDismissPreloader, 150);
    } else {
        window.addEventListener('DOMContentLoaded', cfDismissPreloader);
        window.addEventListener('load', cfDismissPreloader);
    }
    setTimeout(cfDismissPreloader, 1200);
})();
</script>
<!--preloader end-->

<script>
    window.CF_BASE_URL = <?php echo json_encode($url); ?>;
    window.CF_CSRF_TOKEN = <?php echo json_encode(cf_csrf_token()); ?>;
    (function() {
        if (window._cfFetchCsrfPatched || !window.fetch) return;
        window._cfFetchCsrfPatched = true;
        var origFetch = window.fetch.bind(window);
        window.fetch = function(input, init) {
            init = init || {};
            var urlStr = (typeof input === 'string') ? input : (input && input.url ? input.url : '');
            var isSameOrigin = !/^https?:\/\//i.test(urlStr) || urlStr.indexOf(window.location.origin) === 0;
            var method = (init.method || (input && input.method) || 'GET').toUpperCase();
            if (isSameOrigin && method !== 'GET' && method !== 'HEAD' && window.CF_CSRF_TOKEN) {
                var headers = new Headers(init.headers || (input && input.headers) || {});
                if (!headers.has('X-CSRF-Token')) {
                    headers.set('X-CSRF-Token', window.CF_CSRF_TOKEN);
                }
                init = Object.assign({}, init, { headers: headers });
            }
            return origFetch(input, init);
        };
    })();
</script>

<!-- =========================================================================
     DEDICATED MINIMALIST AUTH HEADER (Stripe / Vercel Style)
     ========================================================================= -->
<header class="cf-auth-minimal-header">
    <div class="container cf-auth-container">
        <!-- Brand Logo & Secure Tag -->
        <div class="cf-auth-brand-wrap">
            <a href="<?php echo $url; ?>" class="cf-auth-brand-link" title="CloudFlox Cloud Infrastructure">
                <img src="<?php echo $url; ?>assets/imgs/logos/cloud-flox-main.webp" alt="CloudFlox" class="cf-auth-brand-logo" height="34">
            </a>
            <span class="cf-auth-header-tag d-none d-sm-inline-flex">
                <span class="cf-pulse-dot-green"></span>
                <span>Client Portal</span>
            </span>
        </div>

        <!-- Navigation Actions (Distraction-Free) -->
        <div class="cf-auth-nav-actions">
            <a href="<?php echo $url; ?>" class="cf-auth-nav-btn">
                <i class="las la-arrow-left"></i>
                <span><span class="d-none d-sm-inline">Back to </span>Website</span>
            </a>

            <a href="<?php echo $url; ?>contact-us" class="cf-auth-nav-btn cf-auth-nav-btn-support">
                <i class="las la-headset"></i>
                <span class="d-none d-sm-inline">Support</span>
            </a>

            <?php if ($cfIsLoggedIn): ?>
                <a href="<?php echo $url; ?>clientarea" class="cf-auth-nav-btn cf-auth-nav-btn-primary">
                    <i class="las la-user-circle"></i>
                    <span>Dashboard</span>
                </a>
            <?php endif; ?>
        </div>
    </div>
</header>

