    <!--preloader start-->
    <div class="preloader bg-light-subtle">
        <div class="preloader-wrap">
            <img src="assets/imgs/logos/cloud-flox-main.webp" alt="logo" class="img-fluid" width="10%">
        </div>
    </div>
    <!--preloader end-->

    <?php
    // Active menu page detection
    $cfCurrentPage = basename($_SERVER['PHP_SELF'] ?? '');
    $isHostingActive = in_array($cfCurrentPage, [
        'budget-hosting.php', 'budget-hosting',
        'web-hosting.php', 'web-hosting',
        'premium-hosting.php', 'premium-hosting'
    ]);
    ?>

    <!-- =========================================================================
         MODERN CLOUDFLOX HEADER & ANNOUNCEMENT BAR
         ========================================================================= -->
    <header class="cf-header-wrapper">

        <!-- TOP ANNOUNCEMENT / PROMO BAR -->
        <div id="promoBar" class="cf-top-bar">
            <div class="container d-flex align-items-center justify-content-center justify-content-md-between position-relative flex-wrap">
                
                <!-- Left/Center Offer Message -->
                <div class="cf-promo-content d-flex align-items-center gap-1 gap-sm-2 flex-wrap justify-content-center text-center">
                    <span class="cf-promo-badge">
                        <span class="cf-pulse-dot"></span>
                        <span>PROMO</span>
                    </span>
                    <span class="cf-promo-text">
                        <span class="d-none d-sm-inline">Deploy NVMe VPS &amp; Dedicated Cloud Servers with </span>
                        <span class="d-sm-none">VPS &amp; Dedicated Servers — </span>
                        <span class="cf-promo-highlight">30% OFF</span>!
                    </span>
                    <a href="vps-hosting" class="cf-promo-link">
                        Claim <i class="las la-arrow-right"></i>
                    </a>
                </div>

                <!-- Right Quick Telemetry / SLA link (Desktop) -->
                <div class="cf-promo-extra d-none d-lg-flex align-items-center gap-3">
                    <span class="cf-uptime-chip">
                        <i class="las la-check-circle"></i> 99.99% Uptime SLA
                    </span>
                    <span class="cf-top-divider"></span>
                    <a href="contact-us" class="cf-top-support-link">
                        <i class="las la-headset"></i> 24/7 Human Support
                    </a>
                </div>

                <!-- Dismiss Button -->
                <button type="button" class="cf-promo-close d-none d-md-flex" onclick="document.getElementById('promoBar').style.display='none'" aria-label="Dismiss Announcement">
                    <i class="las la-times"></i>
                </button>
            </div>
        </div>

        <!-- PRIMARY STICKY NAVBAR -->
        <nav class="navbar navbar-expand-lg cf-main-navbar" id="mainNavbar">
            <div class="container">
                <!-- Brand Logo -->
                <a class="navbar-brand cf-brand-logo" href="<?php echo $url;?>">
                    <img src="assets/imgs/logos/cloud-flox-main.webp" alt="CloudFlox Cloud Hosting" class="cf-logo-img">
                </a>

                <!-- Mobile Hamburger Toggler -->
                <button class="navbar-toggler cf-nav-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#primaryMenu" aria-controls="primaryMenu" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="cf-toggler-bar"></span>
                    <span class="cf-toggler-bar"></span>
                    <span class="cf-toggler-bar"></span>
                </button>

                <!-- Navigation Menu Links & Action Button -->
                <div class="collapse navbar-collapse" id="primaryMenu">
                    <ul class="navbar-nav mx-auto align-items-lg-center cf-nav-list">
                        <li class="nav-item">
                            <a class="nav-link cf-nav-link <?php echo ($cfCurrentPage == 'index.php' || empty($cfCurrentPage)) ? 'active' : ''; ?>" href="<?php echo $url;?>">
                                <i class="las la-home nav-icon-mobile d-lg-none"></i>
                                <span>Home</span>
                            </a>
                        </li>
                        <!-- Hosting Dropdown (3 Parts: Budget, Business Web, Premium Managed) -->
                        <li class="nav-item dropdown cf-nav-dropdown">
                            <a class="nav-link cf-nav-link cf-dropdown-toggle <?php echo $isHostingActive ? 'active' : ''; ?>" 
                               href="#" 
                               id="hostingMenuDropdown" 
                               role="button" 
                               data-bs-toggle="dropdown" 
                               data-bs-auto-close="outside"
                               aria-expanded="false">
                                <span class="cf-dropdown-toggle-main d-inline-flex align-items-center gap-2">
                                    <i class="las la-server nav-icon-mobile d-lg-none"></i>
                                    <span>Hosting</span>
                                </span>
                                <i class="las la-angle-down cf-dropdown-caret" aria-hidden="true"></i>
                            </a>
                            <ul class="dropdown-menu cf-dropdown-menu" aria-labelledby="hostingMenuDropdown">
                                <!-- 1. Budget Hosting -->
                                <li>
                                    <a class="dropdown-item cf-dropdown-item <?php echo ($cfCurrentPage == 'budget-hosting.php' || $cfCurrentPage == 'budget-hosting') ? 'active' : ''; ?>" href="budget-hosting">
                                        <div class="cf-dd-icon-box cf-dd-icon-budget">
                                            <i class="las la-wallet"></i>
                                        </div>
                                        <div class="cf-dd-content">
                                            <div class="cf-dd-title-row">
                                                <span class="cf-dd-title">Budget Hosting</span>
                                                <span class="cf-dd-badge cf-dd-badge-budget">Affordable</span>
                                            </div>
                                            <p class="cf-dd-desc">Cost-effective, reliable cPanel hosting for starters &amp; personal sites</p>
                                        </div>
                                    </a>
                                </li>
                                <!-- 2. Business Web Hosting -->
                                <li>
                                    <a class="dropdown-item cf-dropdown-item <?php echo ($cfCurrentPage == 'web-hosting.php' || $cfCurrentPage == 'web-hosting') ? 'active' : ''; ?>" href="web-hosting">
                                        <div class="cf-dd-icon-box cf-dd-icon-web">
                                            <i class="las la-briefcase"></i>
                                        </div>
                                        <div class="cf-dd-content">
                                            <div class="cf-dd-title-row">
                                                <span class="cf-dd-title">Business Web Hosting</span>
                                                <span class="cf-dd-badge cf-dd-badge-popular">Popular</span>
                                            </div>
                                            <p class="cf-dd-desc">High-speed NVMe cloud hosting optimized for business growth</p>
                                        </div>
                                    </a>
                                </li>
                                <!-- 3. Premium Managed Hosting -->
                                <li>
                                    <a class="dropdown-item cf-dropdown-item <?php echo ($cfCurrentPage == 'premium-hosting.php' || $cfCurrentPage == 'premium-hosting') ? 'active' : ''; ?>" href="premium-hosting">
                                        <div class="cf-dd-icon-box cf-dd-icon-premium">
                                            <i class="las la-crown"></i>
                                        </div>
                                        <div class="cf-dd-content">
                                            <div class="cf-dd-title-row">
                                                <span class="cf-dd-title">Premium Managed Hosting</span>
                                                <span class="cf-dd-badge cf-dd-badge-vip">Turbo</span>
                                            </div>
                                            <p class="cf-dd-desc">Enterprise-grade performance with fully managed cloud &amp; VIP support</p>
                                        </div>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link cf-nav-link <?php echo ($cfCurrentPage == 'vps-hosting.php' || $cfCurrentPage == 'vps-hosting') ? 'active' : ''; ?>" href="vps-hosting">
                                <i class="las la-server nav-icon-mobile d-lg-none"></i>
                                <span>VPS Hosting</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link cf-nav-link <?php echo ($cfCurrentPage == 'dedicated-server.php' || $cfCurrentPage == 'dedicated-server') ? 'active' : ''; ?>" href="dedicated-server">
                                <i class="las la-microchip nav-icon-mobile d-lg-none"></i>
                                <span>Dedicated Server</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link cf-nav-link <?php echo ($cfCurrentPage == 'domain.php' || $cfCurrentPage == 'domain') ? 'active' : ''; ?>" href="domain">
                                <i class="las la-globe nav-icon-mobile d-lg-none"></i>
                                <span>Domain</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link cf-nav-link <?php echo ($cfCurrentPage == 'blog.php' || $cfCurrentPage == 'blog') ? 'active' : ''; ?>" href="blog/">
                                <i class="las la-newspaper nav-icon-mobile d-lg-none"></i>
                                <span>Blog</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link cf-nav-link <?php echo ($cfCurrentPage == 'contact-us.php' || $cfCurrentPage == 'contact-us') ? 'active' : ''; ?>" href="contact-us">
                                <i class="las la-envelope nav-icon-mobile d-lg-none"></i>
                                <span>Contact Us</span>
                            </a>
                        </li>
                    </ul>

                    <!-- Right Action Button -->
                    <div class="cf-nav-actions d-flex align-items-center gap-2 mt-3 mt-lg-0">
                        <a href="https://my.cloudflox.com/index.php?rp=/login" class="cf-btn-client-area">
                            <i class="las la-user-circle"></i>
                            <span>Client Portal</span>
                            <i class="las la-arrow-right cf-arrow-hover"></i>
                        </a>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <script>
    (function() {
        function checkHeaderScroll() {
            var nav = document.getElementById('mainNavbar');
            var header = document.querySelector('.cf-header-wrapper');
            if (!nav) return;
            var isScrolled = window.scrollY > 25;
            if (isScrolled) {
                nav.classList.add('scrolled');
                if (header) header.classList.add('is-scrolled');
            } else {
                nav.classList.remove('scrolled');
                if (header) header.classList.remove('is-scrolled');
            }
        }
        window.addEventListener('scroll', checkHeaderScroll, { passive: true });
        window.addEventListener('resize', checkHeaderScroll, { passive: true });
        checkHeaderScroll();
    })();
    </script>

    <!-- OFFER POPUP -->
    <div id="offerPopup" class="offer-popup d-none">
        <div class="offer-popup__backdrop" onclick="closeOfferPopup()"></div>

        <div class="offer-popup__card">
            <button class="offer-popup__close" onclick="closeOfferPopup()" aria-label="Close">✕</button>

            <div class="offer-popup__badge">Limited Offer</div>

            <a href="vps-hosting">
                <img src="assets/imgs/banners/cloudflox-cheap-web-hosting-usa.webp" alt="Offer" width="100%" style="border-radius: 0px 0px 15px 15px;">
            </a>
            <label class="offer-popup__dont text-black d-none">
                <input type="checkbox" id="dontShowOffer" checked>
                Don’t show again
            </label>
        </div>
    </div>