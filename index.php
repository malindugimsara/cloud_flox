<?php include 'include/url.inc.php'; ?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">

<head>
    <!-- Basic Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>CloudFlox — Enterprise NVMe Cloud Hosting, Dedicated Servers & Free 24/7 Support</title>

    <meta name="description"
        content="Deploy high-speed NVMe cloud hosting, scalable VPS, and dedicated enterprise servers with CloudFlox. Enjoy free SSL, free migrations, and 24/7 expert human support. 99.99% uptime SLA.">
    <meta name="keywords"
        content="nvme hosting, dedicated servers, enterprise cloud hosting, free migration, 24/7 support, vps hosting, cpanel hosting, domain registration, cloudflox">
    <meta name="author" content="CloudFlox">
    <link rel="canonical" href="https://cloudflox.com/">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="CloudFlox — Enterprise NVMe Cloud Hosting, Dedicated Servers & Free 24/7 Support">
    <meta property="og:description"
        content="Deploy high-speed NVMe cloud hosting, scalable VPS, and dedicated enterprise servers with CloudFlox. Enjoy free SSL, free migrations, and 24/7 expert human support.">
    <meta property="og:url" content="https://cloudflox.com/">
    <meta property="og:image" content="https://cloudflox.com/assets/imgs/banners/dedicated-server-components.webp">
    <meta property="og:site_name" content="CloudFlox">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="CloudFlox — Enterprise NVMe Cloud Hosting, Dedicated Servers & Free 24/7 Support">
    <meta name="twitter:description"
        content="Deploy high-speed NVMe cloud hosting, scalable VPS, and dedicated enterprise servers with CloudFlox. Enjoy free SSL, free migrations, and 24/7 expert human support.">
    <meta name="twitter:image" content="https://cloudflox.com/assets/imgs/banners/dedicated-server-components.webp">

    <!-- Preload Hero Image for LCP Optimization (Responsive Mobile & Desktop) -->
    <link rel="preload" as="image" href="assets/imgs/banners/banner-sm.webp" media="(max-width: 576px)" type="image/webp"
        fetchpriority="high">
    <link rel="preload" as="image" href="assets/imgs/banners/banner.webp" media="(min-width: 577px)" type="image/webp"
        fetchpriority="high">

    <!-- Schema.org Structured Data for SEO -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@graph": [
        {
          "@type": "Organization",
          "@id": "https://cloudflox.com/#organization",
          "name": "CloudFlox",
          "url": "https://cloudflox.com/",
          "logo": "https://cloudflox.com/assets/imgs/logos/cloud-flox-main.webp",
          "description": "Next-generation high-speed cloud hosting, scalable VPS, dedicated bare-metal servers, and domain services.",
          "sameAs": [
            "https://facebook.com/cloudflox",
            "https://twitter.com/cloudflox"
          ]
        },
        {
          "@type": "WebSite",
          "@id": "https://cloudflox.com/#website",
          "url": "https://cloudflox.com/",
          "name": "CloudFlox",
          "publisher": {
            "@id": "https://cloudflox.com/#organization"
          },
          "potentialAction": {
            "@type": "SearchAction",
            "target": "https://my.cloudflox.com/cart.php?a=add&domain=register&query={search_term_string}",
            "query-input": "required name=search_term_string"
          }
        }
      ]
    }
    </script>

    <!-- Swiper CSS (Non-Blocking) -->
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" as="style">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"></noscript>

    <!-- =========== CSS Area =========== -->
    <?php include 'include/css.inc.php'; ?>
    <!-- =========== CSS Area =========== -->
</head>

<body class="pure-white-theme cf-home-page">

    <!-- =========== Header Area =========== -->
    <?php include 'include/header.inc.php'; ?>
    <!-- =========== Header Area =========== -->

    <!-- =========================================================================
         1. HERO SECTION (Redesigned Modern 2-Column Split Layout & SEO Focus)
         ========================================================================= -->
    <section class="modern-hero-section position-relative">
        <!-- Ambient decorative glow background (Light Theme) -->
        <div class="hero-ambient-glow" aria-hidden="true"></div>

        <div class="container position-relative z-2">
            <div class="row align-items-center g-4 g-xl-5">

                <!-- Left Column: High-Impact SEO Content & CTAs -->
                <div class="col-12 col-lg-7 col-xl-7">
                    <div class="hero-content-wrap">

                        <!-- Simplified, Sleek Pill Badge -->
                        <div class="hero-pill-badge" data-sal="slide-down" data-sal-duration="600">
                            <span class="pulse-dot"></span>
                            <span class="badge-text">Next-Gen NVMe Cloud Infrastructure</span>
                            <i class="las la-angle-right badge-arrow" aria-hidden="true"></i>
                        </div>

                        <!-- Refined, Unified H1 Heading -->
                        <h1 class="hero-title" data-sal="slide-up" data-sal-duration="700" data-sal-delay="100">
                            High-Speed <span class="hero-text-accent">NVMe Cloud Hosting</span><br
                                class="d-none d-md-inline">&amp; Dedicated Enterprise Servers
                        </h1>

                        <!-- Punchy, Clear 2-Line Value Proposition -->
                        <p class="hero-desc" data-sal="slide-up" data-sal-duration="700" data-sal-delay="150">
                            Deploy ultra-fast websites and applications on next-gen NVMe cloud architecture. Guaranteed
                            99.99% uptime, free zero-downtime migration, and 24/7 expert human support.
                        </p>

                        <!-- Clean Primary & Secondary CTA Buttons Row -->
                        <div class="hero-cta-group" data-sal="slide-up" data-sal-duration="700" data-sal-delay="200">
                            <a href="#pricing" class="btn-hero-primary">
                                <span>Get Started Now</span>
                                <i class="las la-arrow-right"></i>
                            </a>
                            <a href="vps-hosting" class="btn-hero-secondary">
                                <i class="las la-server"></i>
                                <span>Explore VPS &amp; Dedicated</span>
                            </a>
                        </div>

                        <!-- Minimalist Single-Line Social Proof & Key Guarantees -->
                        <div class="hero-trust-bar" data-sal="fade" data-sal-duration="800" data-sal-delay="250">
                            <div class="trust-rating-item">
                                <div class="rating-stars" aria-label="5 out of 5 stars">
                                    <i class="las la-star"></i>
                                    <i class="las la-star"></i>
                                    <i class="las la-star"></i>
                                    <i class="las la-star"></i>
                                    <i class="las la-star"></i>
                                </div>
                                <span class="rating-label"><strong>4.9/5</strong> (1,200+ reviews)</span>
                            </div>
                            <span class="trust-dot-divider" aria-hidden="true">•</span>
                            <div class="trust-perk-badge">
                                <i class="las la-shield-alt text-primary"></i>
                                <span>Free Imunify360 &amp; SSL</span>
                            </div>
                            <span class="trust-dot-divider" aria-hidden="true">•</span>
                            <div class="trust-perk-badge">
                                <i class="las la-sync text-primary"></i>
                                <span>Free Migration</span>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Right Column: 3D Isometric Infrastructure Visual & Floating Telemetry -->
                <div class="col-12 col-lg-5 col-xl-5">
                    <div class="hero-visual-showcase" data-sal="zoom-in" data-sal-duration="850">
                        <!-- Main 3D Cloud Infrastructure Graphic -->
                        <div class="hero-image-wrapper">
                            <picture>
                                <source media="(max-width: 576px)" srcset="assets/imgs/banners/banner-sm.webp" type="image/webp">
                                <source srcset="assets/imgs/banners/banner.webp" type="image/webp">
                                <img src="assets/imgs/banners/banner.webp"
                                    alt="CloudFlox Next-Gen High-Speed Cloud Hosting Infrastructure with NVMe Gen4 Storage and Global Network"
                                    class="hero-main-img img-fluid" width="1024" height="765" fetchpriority="high"
                                    loading="eager">
                            </picture>
                        </div>

                        <!-- Floating Live Telemetry Badge 1: NVMe Speed (Top-Left) -->
                        <div class="hero-floating-card card-speed">
                            <div class="floating-icon-box bg-orange-subtle">
                                <i class="las la-tachometer-alt"></i>
                            </div>
                            <div class="floating-text">
                                <span class="floating-subtitle">NVMe Gen4 Speed</span>
                                <strong class="floating-title">7,450 MB/s Read</strong>
                            </div>
                            <span class="floating-status-pill">300% Faster</span>
                        </div>

                        <!-- Floating Live Telemetry Badge 2: DDoS Shield + 99.99% Uptime (Bottom-Right) -->
                        <div class="hero-floating-card card-security">
                            <div class="floating-icon-box bg-blue-subtle">
                                <i class="las la-shield-alt"></i>
                            </div>
                            <div class="floating-text">
                                <span class="floating-subtitle">Enterprise Shield</span>
                                <strong class="floating-title">DDoS Guard • 99.99% SLA</strong>
                            </div>
                            <div class="live-dot-wrap">
                                <span class="pulse-dot-green"></span>
                                <span class="live-label">Active</span>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- =========================================================================
         2. HIGHLIGHTED DOMAIN SEARCH SECTION (Dedicated Search & TLD Showcase)
         ========================================================================= -->
    <section class="modern-domain-section position-relative">
        <div class="container position-relative z-2">
            <div class="domain-showcase-box" data-sal="slide-up" data-sal-duration="700">
                <div class="domain-showcase-glow" aria-hidden="true"></div>

                <!-- Decorative Radar Scanning Rings -->
                <div class="domain-radar-waves" aria-hidden="true">
                    <div class="radar-wave w-1"></div>
                    <div class="radar-wave w-2"></div>
                    <div class="radar-wave w-3"></div>
                </div>

                <!-- Decorative Floating TLD Watermarks (Desktop Depth) -->
                <div class="domain-float-badge deco-left-top d-none d-xl-flex" aria-hidden="true">
                    <span class="float-tld-ext">.com</span>
                    <span class="float-tld-tag"><i class="las la-bolt text-warning"></i> Fast</span>
                </div>
                <div class="domain-float-badge deco-left-bottom d-none d-xl-flex" aria-hidden="true">
                    <span class="float-tld-icon"><i class="las la-globe-americas text-primary"></i></span>
                    <span class="float-tld-text">Anycast DNS</span>
                </div>
                <div class="domain-float-badge deco-right-top d-none d-xl-flex" aria-hidden="true">
                    <span class="float-tld-ext">.cloud</span>
                    <span class="float-tld-tag text-orange"><i class="las la-fire"></i> Best Deal</span>
                </div>
                <div class="domain-float-badge deco-right-bottom d-none d-xl-flex" aria-hidden="true">
                    <span class="float-tld-icon"><i class="las la-shield-alt text-success"></i></span>
                    <span class="float-tld-text">Free SSL & Lock</span>
                </div>

                <div class="row justify-content-center text-center position-relative z-2">
                    <div class="col-xl-9 col-lg-10">

                        <!-- Section Pill Badge -->
                        <div class="domain-highlight-pill">
                            <span class="pulse-dot-blue"></span>
                            <span>Instant Domain Availability Check</span>
                            <span class="domain-pill-save">Free WHOIS Privacy</span>
                        </div>

                        <!-- High-Impact Title -->
                        <h2 class="domain-section-title">
                            Find &amp; Register Your <span class="text-gradient-primary">Dream Domain</span> with Free WHOIS Privacy
                        </h2>

                        <!-- Subtitle -->
                        <p class="domain-section-desc">
                            Claim your unique web address before someone else does. Every domain includes instant DNS
                            setup, domain lock, and 24/7 specialist assistance.
                        </p>

                        <!-- Large Highlighted Search Form -->
                        <form action="https://my.cloudflox.com/cart.php?a=add&domain=register" method="post"
                            id="domainForm" class="domain-mega-search-box" aria-label="Domain Name Search">
                            <i class="las la-search domain-search-icon" aria-hidden="true"></i>
                            <input type="text" name="query" id="domainInput"
                                placeholder="Type your desired domain name (e.g. mybrand.com)..." required
                                autocomplete="off" aria-label="Domain name to register">
                            <button type="submit" id="domainSearchBtn" class="domain-mega-btn"
                                aria-label="Search Domain">
                                <span>Search Domain</span>
                                <i class="las la-arrow-right ms-2" aria-hidden="true"></i>
                            </button>
                        </form>

                        <!-- Real-time Live Domain Search Results Container -->
                        <div id="homeDomainSearchResults" class="cf-domain-results-wrap" style="display: none;"></div>

                        <!-- Highlighted TLD Price Cards -->
                        <div class="domain-tld-showcase" aria-label="Popular Domain Extensions">
                            <a href="javascript:void(0)" class="tld-card active-tld"
                                onclick="setDomainExt('.com', this)">
                                <div class="tld-card-header">
                                    <span class="tld-name">.com</span>
                                    <span class="tld-badge badge-popular">Popular</span>
                                </div>
                                <div class="tld-price-val">$13.57 <small>/yr</small></div>
                            </a>
                            <a href="javascript:void(0)" class="tld-card" onclick="setDomainExt('.net', this)">
                                <div class="tld-card-header">
                                    <span class="tld-name">.net</span>
                                </div>
                                <div class="tld-price-val">$15.49 <small>/yr</small></div>
                            </a>
                            <a href="javascript:void(0)" class="tld-card" onclick="setDomainExt('.org', this)">
                                <div class="tld-card-header">
                                    <span class="tld-name">.org</span>
                                </div>
                                <div class="tld-price-val">$17.49 <small>/yr</small></div>
                            </a>
                            <a href="javascript:void(0)" class="tld-card tld-card-deal"
                                onclick="setDomainExt('.cloud', this)">
                                <div class="tld-card-header">
                                    <span class="tld-name">.cloud</span>
                                    <span class="tld-badge badge-deal">Hot Deal</span>
                                </div>
                                <div class="tld-price-val">$9.99 <small>/yr</small></div>
                            </a>
                            <a href="javascript:void(0)" class="tld-card" onclick="setDomainExt('.info', this)">
                                <div class="tld-card-header">
                                    <span class="tld-name">.info</span>
                                </div>
                                <div class="tld-price-val">$15.99 <small>/yr</small></div>
                            </a>
                        </div>

                        <!-- Bottom Domain Trust Perks -->
                        <div class="domain-trust-perks">
                            <div class="trust-perk-item">
                                <i class="las la-shield-alt text-primary" aria-hidden="true"></i>
                                <span>Free WHOIS Privacy Protection</span>
                            </div>
                            <div class="trust-perk-item">
                                <i class="las la-bolt text-warning" aria-hidden="true"></i>
                                <span>Instant DNS & Nameserver Setup</span>
                            </div>
                            <div class="trust-perk-item">
                                <i class="las la-lock text-success" aria-hidden="true"></i>
                                <span>Domain Theft Protection Lock</span>
                            </div>
                            <div class="trust-perk-item">
                                <i class="las la-exchange-alt text-info" aria-hidden="true"></i>
                                <span>Hassle-Free Domain Transfer</span>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>




    <!-- =========================================================================
         3. HOSTING PACKAGES & PRICING SECTION
         ========================================================================= -->
    <section class="modern-pricing-section" id="pricing">
        <!-- Ambient Decorative Pattern Backgrounds -->
        <div class="pricing-bg-shape pricing-shape-left" aria-hidden="true">
            <img src="assets/imgs/shape/pattern-2.svg?v=2" alt="pattern-2" width="370" height="590" loading="eager"
                decoding="async">
        </div>
        <div class="pricing-bg-shape pricing-shape-right" aria-hidden="true">
            <img src="assets/imgs/shape/pattern-3.svg?v=2" alt="pattern-3" width="370" height="590" loading="eager"
                decoding="async">
        </div>

        <div class="container position-relative z-2">
            <div class="text-center mb-4">
                <!-- Minimalist Pricing Header Pill -->
                <div class="pricing-header-pill">
                    <span class="pulse-dot-blue"></span>
                    <span>Simple, Transparent Pricing</span>
                    <span class="pricing-pill-sub">USD STOREFRONT</span>
                </div>

                <h2 class="pricing-section-title">
                    Enterprise NVMe Hosting &amp; Dedicated Solutions <span class="text-accent-blue">With Free Migrations</span>
                </h2>
                <p class="pricing-section-subtitle">
                    Enterprise NVMe Gen4 storage, CloudLinux resource isolation, and automated daily backups — tailored
                    from personal blogs to demanding business platforms.
                </p>

                <!-- High-Impact Category Selector Tabs (Budget / Business / Premium) -->
                <div class="pricing-category-nav-wrapper">
                    <div class="pricing-category-tabs" role="tablist" aria-label="Hosting Category Tabs">
                        <!-- Budget Hosting Tab -->
                        <button type="button" class="category-tab-btn tab-budget-btn" id="tab-budget"
                            onclick="switchPricingCategory('budget', this)" role="tab" aria-selected="false">
                            <div class="cat-tab-icon icon-budget">
                                <i class="las la-feather-alt" aria-hidden="true"></i>
                            </div>
                            <div class="cat-tab-content">
                                <div class="cat-tab-header">
                                    <span class="cat-tab-title">Budget Hosting</span>
                                    <span class="cat-tab-pill pill-budget">3 Plans</span>
                                </div>
                                <div class="cat-tab-price">
                                    From <strong>$3.00</strong><small>/mo</small>
                                </div>
                            </div>
                        </button>

                        <!-- Business Web Hosting Tab (Active by Default) -->
                        <button type="button" class="category-tab-btn active tab-business-btn" id="tab-business"
                            onclick="switchPricingCategory('business', this)" role="tab" aria-selected="true">
                            <div class="cat-tab-icon icon-business">
                                <i class="las la-briefcase" aria-hidden="true"></i>
                            </div>
                            <div class="cat-tab-content">
                                <div class="cat-tab-header">
                                    <span class="cat-tab-title">Business Web</span>
                                    <span class="cat-tab-badge-pop"><i class="las la-fire me-1"></i>Most Popular</span>
                                </div>
                                <div class="cat-tab-price">
                                    From <strong>$7.50</strong><small>/mo</small>
                                </div>
                            </div>
                        </button>

                        <!-- Premium Managed Tab -->
                        <button type="button" class="category-tab-btn tab-premium-btn" id="tab-premium"
                            onclick="switchPricingCategory('premium', this)" role="tab" aria-selected="false">
                            <div class="cat-tab-icon icon-premium">
                                <i class="las la-crown" aria-hidden="true"></i>
                            </div>
                            <div class="cat-tab-content">
                                <div class="cat-tab-header">
                                    <span class="cat-tab-title">Premium Managed</span>
                                    <span class="cat-tab-pill pill-premium"><i
                                            class="las la-gem me-1"></i>Dedicated</span>
                                </div>
                                <div class="cat-tab-price">
                                    From <strong>$75.00</strong><small>/mo</small>
                                </div>
                            </div>
                        </button>
                    </div>
                </div>

                <!-- Modern Segmented Interactive Billing Switcher -->
                <div class="pricing-switch-container">
                    <div class="pricing-switch-pill">
                        <button type="button" class="switch-opt active" id="btnMonthly"
                            onclick="setBillingToggle(false)">
                            Monthly Billing
                        </button>
                        <button type="button" class="switch-opt" id="btnYearly" onclick="setBillingToggle(true)">
                            Annual Billing <span class="discount-pill">Save 20%</span>
                        </button>
                        <input type="checkbox" id="pricingSwitch" class="d-none" onchange="togglePricing(this.checked)">
                    </div>
                </div>
            </div>

            <!-- ==========================================
                 PANE 1: BUDGET HOSTING (3 PLANS)
                 ========================================== -->
            <div class="pricing-category-pane row g-4 align-items-stretch justify-content-center"
                id="categoryPane-budget" style="display: none;">
                <!-- Budget Mini -->
                <div class="col-lg-4 col-md-6">
                    <div class="modern-pricing-card">
                        <div class="plan-card-header">
                            <div class="plan-card-icon bg-emerald-subtle text-success">
                                <i class="las la-feather-alt" aria-hidden="true"></i>
                            </div>
                            <div>
                                <span class="card-tier-label text-success">Budget Tier</span>
                                <h3 class="plan-name">Mini</h3>
                                <p class="plan-tagline">Personal portfolios &amp; hobby sites</p>
                            </div>
                        </div>

                        <div class="price-box">
                            <div class="price-amount">
                                <span class="price-val" data-monthly="$3.00" data-yearly="$2.40">$3.00</span>
                                <span class="price-cycle" data-monthly="/mo" data-yearly="/mo">/mo</span>
                            </div>
                            <div class="price-subtext" data-monthly="Billed $3.00 monthly"
                                data-yearly="Billed $28.80 yearly (Save 20%)">Billed $3.00 monthly</div>
                        </div>

                        <a href="https://my.cloudflox.com/cart.php?a=add&pid=101&billingcycle=monthly" data-pid="101"
                            class="plan-cta-btn btn-plan-outline">
                            Choose Mini Plan
                        </a>

                        <ul class="plan-features-list">
                            <li><span class="feat-icon-circle"><i class="las la-check"></i></span><span>CPU &amp; RAM:
                                    <strong>1 Core / 1 GB RAM</strong></span></li>
                            <li><span class="feat-icon-circle"><i class="las la-check"></i></span><span>Storage:
                                    <strong>5 GB NVMe Gen4 SSD</strong></span></li>
                            <li><span class="feat-icon-circle"><i class="las la-check"></i></span><span>Capacity:
                                    <strong>1 Website • 50 GB Bandwidth</strong></span></li>
                            <li><span class="feat-icon-circle"><i class="las la-check"></i></span><span>Email Inboxes:
                                    <strong>5 Professional Accounts</strong></span></li>
                            <li><span class="feat-icon-circle"><i class="las la-check"></i></span><span>Security:
                                    <strong>Free AutoSSL &amp; ImunifyAV</strong></span></li>
                            <li><span class="feat-icon-circle"><i class="las la-check"></i></span><span>Control Panel:
                                    <strong>cPanel + 24/7 Expert Support</strong></span></li>
                        </ul>
                    </div>
                </div>

                <!-- Budget Basic (Featured in Budget) -->
                <div class="col-lg-4 col-md-6">
                    <div class="modern-pricing-card featured">
                        <div class="plan-featured-badge">
                            <i class="las la-star" aria-hidden="true"></i> Best Budget Value
                        </div>
                        <div class="plan-card-header">
                            <div class="plan-card-icon bg-orange-subtle text-orange">
                                <i class="las la-rocket" aria-hidden="true"></i>
                            </div>
                            <div>
                                <span class="card-tier-label text-orange">Budget Tier</span>
                                <h3 class="plan-name">Basic</h3>
                                <p class="plan-tagline">Active blogs &amp; freelancer websites</p>
                            </div>
                        </div>

                        <div class="price-box">
                            <div class="price-amount">
                                <span class="price-val" data-monthly="$4.50" data-yearly="$3.60">$4.50</span>
                                <span class="price-cycle" data-monthly="/mo" data-yearly="/mo">/mo</span>
                            </div>
                            <div class="price-subtext text-orange-sub" data-monthly="Billed $4.50 monthly"
                                data-yearly="Billed $43.20 yearly (Save 20%)">Billed $4.50 monthly</div>
                        </div>

                        <a href="https://my.cloudflox.com/cart.php?a=add&pid=102&billingcycle=monthly" data-pid="102"
                            class="plan-cta-btn btn-plan-featured">
                            <span>Choose Basic Plan</span>
                            <i class="las la-arrow-right ms-2"></i>
                        </a>

                        <ul class="plan-features-list">
                            <li><span class="feat-icon-circle"><i class="las la-check"></i></span><span>CPU &amp; RAM:
                                    <strong>2 Cores / 2 GB RAM</strong></span></li>
                            <li><span class="feat-icon-circle"><i class="las la-check"></i></span><span>Storage:
                                    <strong>15 GB NVMe Gen4 SSD</strong></span></li>
                            <li><span class="feat-icon-circle"><i class="las la-check"></i></span><span>Capacity:
                                    <strong>3 Websites • 150 GB Bandwidth</strong></span></li>
                            <li><span class="feat-icon-circle"><i class="las la-check"></i></span><span>Email Inboxes:
                                    <strong>15 Professional Accounts</strong></span></li>
                            <li><span class="feat-icon-circle"><i class="las la-check"></i></span><span>Security:
                                    <strong>Free AutoSSL &amp; ImunifyAV</strong></span></li>
                            <li><span class="feat-icon-circle"><i class="las la-check"></i></span><span>Backups &amp;
                                    Support: <strong>Daily Backups + 24/7 Support</strong></span></li>
                        </ul>
                    </div>
                </div>

                <!-- Budget Plus -->
                <div class="col-lg-4 col-md-6">
                    <div class="modern-pricing-card">
                        <div class="plan-card-header">
                            <div class="plan-card-icon bg-blue-subtle text-primary">
                                <i class="las la-bolt" aria-hidden="true"></i>
                            </div>
                            <div>
                                <span class="card-tier-label text-primary">Budget Tier</span>
                                <h3 class="plan-name">Plus</h3>
                                <p class="plan-tagline">Multi-site creators &amp; small publishers</p>
                            </div>
                        </div>

                        <div class="price-box">
                            <div class="price-amount">
                                <span class="price-val" data-monthly="$6.00" data-yearly="$4.80">$6.00</span>
                                <span class="price-cycle" data-monthly="/mo" data-yearly="/mo">/mo</span>
                            </div>
                            <div class="price-subtext" data-monthly="Billed $6.00 monthly"
                                data-yearly="Billed $57.60 yearly (Save 20%)">Billed $6.00 monthly</div>
                        </div>

                        <a href="https://my.cloudflox.com/cart.php?a=add&pid=103&billingcycle=monthly" data-pid="103"
                            class="plan-cta-btn btn-plan-outline">
                            Choose Plus Plan
                        </a>

                        <ul class="plan-features-list">
                            <li><span class="feat-icon-circle"><i class="las la-check"></i></span><span>CPU &amp; RAM:
                                    <strong>2 High Cores / 3 GB RAM</strong></span></li>
                            <li><span class="feat-icon-circle"><i class="las la-check"></i></span><span>Storage:
                                    <strong>30 GB NVMe Gen4 SSD</strong></span></li>
                            <li><span class="feat-icon-circle"><i class="las la-check"></i></span><span>Capacity:
                                    <strong>10 Websites • 300 GB Bandwidth</strong></span></li>
                            <li><span class="feat-icon-circle"><i class="las la-check"></i></span><span>Email Inboxes:
                                    <strong>Unlimited Professional Accounts</strong></span></li>
                            <li><span class="feat-icon-circle"><i class="las la-check"></i></span><span>Protection:
                                    <strong>ImunifyAV &amp; Automated Daily Backups</strong></span></li>
                            <li><span class="feat-icon-circle"><i class="las la-check"></i></span><span>Tools:
                                    <strong>cPanel + Staging Tools + 24/7 Support</strong></span></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- ==========================================
                 PANE 2: BUSINESS WEB HOSTING (4 PLANS) - ACTIVE DEFAULT
                 ========================================== -->
            <div class="pricing-category-pane active row g-4 align-items-stretch justify-content-center"
                id="categoryPane-business">
                <!-- Business Starter -->
                <div class="col-xl-3 col-md-6">
                    <div class="modern-pricing-card">
                        <div class="plan-card-header">
                            <div class="plan-card-icon bg-blue-subtle text-primary">
                                <i class="las la-building" aria-hidden="true"></i>
                            </div>
                            <div>
                                <span class="card-tier-label text-primary">Business Tier</span>
                                <h3 class="plan-name">Starter</h3>
                                <p class="plan-tagline">Startups &amp; corporate portals</p>
                            </div>
                        </div>

                        <div class="price-box">
                            <div class="price-amount">
                                <span class="price-val" data-monthly="$7.50" data-yearly="$6.00">$7.50</span>
                                <span class="price-cycle" data-monthly="/mo" data-yearly="/mo">/mo</span>
                            </div>
                            <div class="price-subtext" data-monthly="Billed $7.50 monthly"
                                data-yearly="Billed $72.00 yearly (Save 20%)">Billed $7.50 monthly</div>
                        </div>

                        <a href="https://my.cloudflox.com/cart.php?a=add&pid=201&billingcycle=monthly" data-pid="201"
                            class="plan-cta-btn btn-plan-outline">
                            Choose Starter
                        </a>

                        <ul class="plan-features-list">
                            <li><span class="feat-icon-circle"><i class="las la-check"></i></span><span>CPU &amp; RAM:
                                    <strong>2 Cores / 2 GB RAM</strong></span></li>
                            <li><span class="feat-icon-circle"><i class="las la-check"></i></span><span>Storage:
                                    <strong>20 GB NVMe Gen4 SSD</strong></span></li>
                            <li><span class="feat-icon-circle"><i class="las la-check"></i></span><span>Capacity:
                                    <strong>2 Websites • 500 GB Bandwidth</strong></span></li>
                            <li><span class="feat-icon-circle"><i class="las la-check"></i></span><span>Email Inboxes:
                                    <strong>25 Business Accounts</strong></span></li>
                            <li><span class="feat-icon-circle"><i class="las la-check"></i></span><span>Security:
                                    <strong>Free Wildcard SSL &amp; Imunify360</strong></span></li>
                            <li><span class="feat-icon-circle"><i class="las la-check"></i></span><span>Support:
                                    <strong>Daily Backups + 24/7 Business Support</strong></span></li>
                        </ul>
                    </div>
                </div>

                <!-- Business Business (Featured) -->
                <div class="col-xl-3 col-md-6">
                    <div class="modern-pricing-card featured">
                        <div class="plan-featured-badge">
                            <i class="las la-fire" aria-hidden="true"></i> Most Popular • Best Value
                        </div>
                        <div class="plan-card-header">
                            <div class="plan-card-icon bg-orange-subtle text-orange">
                                <i class="las la-bolt" aria-hidden="true"></i>
                            </div>
                            <div>
                                <span class="card-tier-label text-orange">Business Tier</span>
                                <h3 class="plan-name">Business</h3>
                                <p class="plan-tagline">SMEs, WooCommerce &amp; busy sites</p>
                            </div>
                        </div>

                        <div class="price-box">
                            <div class="price-amount">
                                <span class="price-val" data-monthly="$15.00" data-yearly="$12.00">$15.00</span>
                                <span class="price-cycle" data-monthly="/mo" data-yearly="/mo">/mo</span>
                            </div>
                            <div class="price-subtext text-orange-sub" data-monthly="Billed $15.00 monthly"
                                data-yearly="Billed $144.00 yearly (Save 20%)">Billed $15.00 monthly</div>
                        </div>

                        <a href="https://my.cloudflox.com/cart.php?a=add&pid=202&billingcycle=monthly" data-pid="202"
                            class="plan-cta-btn btn-plan-featured">
                            <span>Choose Business</span>
                            <i class="las la-arrow-right ms-2"></i>
                        </a>

                        <ul class="plan-features-list">
                            <li><span class="feat-icon-circle"><i class="las la-check"></i></span><span>CPU &amp; RAM:
                                    <strong>3 High-Perf Cores / 4 GB RAM</strong></span></li>
                            <li><span class="feat-icon-circle"><i class="las la-check"></i></span><span>Storage:
                                    <strong>40 GB NVMe Gen4 SSD</strong></span></li>
                            <li><span class="feat-icon-circle"><i class="las la-check"></i></span><span>Capacity:
                                    <strong>10 Websites • Unlimited Bandwidth</strong></span></li>
                            <li><span class="feat-icon-circle"><i class="las la-check"></i></span><span>Web Server:
                                    <strong>LiteSpeed Enterprise + LSCache</strong></span></li>
                            <li><span class="feat-icon-circle"><i class="las la-check"></i></span><span>Protection:
                                    <strong>Imunify360 AI Defense + AutoSSL</strong></span></li>
                            <li><span class="feat-icon-circle"><i class="las la-check"></i></span><span>Support:
                                    <strong>Daily Backups + Priority Support</strong></span></li>
                        </ul>
                    </div>
                </div>

                <!-- Business Professional -->
                <div class="col-xl-3 col-md-6">
                    <div class="modern-pricing-card">
                        <div class="plan-card-header">
                            <div class="plan-card-icon bg-indigo-subtle text-indigo">
                                <i class="las la-layer-group" aria-hidden="true"></i>
                            </div>
                            <div>
                                <span class="card-tier-label text-indigo">Business Tier</span>
                                <h3 class="plan-name">Professional</h3>
                                <p class="plan-tagline">High-traffic stores &amp; client agencies</p>
                            </div>
                        </div>

                        <div class="price-box">
                            <div class="price-amount">
                                <span class="price-val" data-monthly="$27.00" data-yearly="$21.60">$27.00</span>
                                <span class="price-cycle" data-monthly="/mo" data-yearly="/mo">/mo</span>
                            </div>
                            <div class="price-subtext" data-monthly="Billed $27.00 monthly"
                                data-yearly="Billed $259.20 yearly (Save 20%)">Billed $27.00 monthly</div>
                        </div>

                        <a href="https://my.cloudflox.com/cart.php?a=add&pid=203&billingcycle=monthly" data-pid="203"
                            class="plan-cta-btn btn-plan-outline">
                            Choose Professional
                        </a>

                        <ul class="plan-features-list">
                            <li><span class="feat-icon-circle"><i class="las la-check"></i></span><span>CPU &amp; RAM:
                                    <strong>4 High-Perf Cores / 6 GB RAM</strong></span></li>
                            <li><span class="feat-icon-circle"><i class="las la-check"></i></span><span>Storage:
                                    <strong>80 GB NVMe Gen4 SSD</strong></span></li>
                            <li><span class="feat-icon-circle"><i class="las la-check"></i></span><span>Capacity:
                                    <strong>25 Websites • Unlimited Bandwidth</strong></span></li>
                            <li><span class="feat-icon-circle"><i class="las la-check"></i></span><span>Web Server:
                                    <strong>LiteSpeed + LSCache Accelerator</strong></span></li>
                            <li><span class="feat-icon-circle"><i class="las la-check"></i></span><span>Security:
                                    <strong>Imunify360 + Web App Firewall (WAF)</strong></span></li>
                            <li><span class="feat-icon-circle"><i class="las la-check"></i></span><span>Support:
                                    <strong>Priority Queue Support + Staging Tools</strong></span></li>
                        </ul>
                    </div>
                </div>

                <!-- Business Enterprise -->
                <div class="col-xl-3 col-md-6">
                    <div class="modern-pricing-card">
                        <div class="plan-card-header">
                            <div class="plan-card-icon bg-emerald-subtle text-success">
                                <i class="las la-crown" aria-hidden="true"></i>
                            </div>
                            <div>
                                <span class="card-tier-label text-success">Business Tier</span>
                                <h3 class="plan-name">Enterprise</h3>
                                <p class="plan-tagline">Mission-critical &amp; intensive web apps</p>
                            </div>
                        </div>

                        <div class="price-box">
                            <div class="price-amount">
                                <span class="price-val" data-monthly="$48.00" data-yearly="$38.40">$48.00</span>
                                <span class="price-cycle" data-monthly="/mo" data-yearly="/mo">/mo</span>
                            </div>
                            <div class="price-subtext" data-monthly="Billed $48.00 monthly"
                                data-yearly="Billed $460.80 yearly (Save 20%)">Billed $48.00 monthly</div>
                        </div>

                        <a href="https://my.cloudflox.com/cart.php?a=add&pid=204&billingcycle=monthly" data-pid="204"
                            class="plan-cta-btn btn-plan-outline">
                            Choose Enterprise
                        </a>

                        <ul class="plan-features-list">
                            <li><span class="feat-icon-circle"><i class="las la-check"></i></span><span>CPU &amp; RAM:
                                    <strong>6 VIP Cores / 8 GB Dedicated RAM</strong></span></li>
                            <li><span class="feat-icon-circle"><i class="las la-check"></i></span><span>Storage:
                                    <strong>150 GB NVMe Gen4 SSD</strong></span></li>
                            <li><span class="feat-icon-circle"><i class="las la-check"></i></span><span>Capacity:
                                    <strong>Unlimited Websites &amp; Bandwidth</strong></span></li>
                            <li><span class="feat-icon-circle"><i class="las la-check"></i></span><span>Developer:
                                    <strong>Isolated SSH &amp; WP-CLI Enabled</strong></span></li>
                            <li><span class="feat-icon-circle"><i class="las la-check"></i></span><span>Protection:
                                    <strong>Imunify360 WAF + Twice-Daily Backups</strong></span></li>
                            <li><span class="feat-icon-circle"><i class="las la-check"></i></span><span>Support:
                                    <strong>VIP Senior SysAdmin Direct Support</strong></span></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- ==========================================
                 PANE 3: PREMIUM MANAGED HOSTING (4 PLANS)
                 ========================================== -->
            <div class="pricing-category-pane row g-4 align-items-stretch justify-content-center"
                id="categoryPane-premium" style="display: none;">
                <!-- Premium Starter -->
                <div class="col-xl-3 col-md-6">
                    <div class="modern-pricing-card">
                        <div class="plan-card-header">
                            <div class="plan-card-icon bg-indigo-subtle text-indigo">
                                <i class="las la-server" aria-hidden="true"></i>
                            </div>
                            <div>
                                <span class="card-tier-label text-indigo">Premium Managed</span>
                                <h3 class="plan-name">Starter</h3>
                                <p class="plan-tagline">High-resource web apps &amp; APIs</p>
                            </div>
                        </div>

                        <div class="price-box">
                            <div class="price-amount">
                                <span class="price-val" data-monthly="$75.00" data-yearly="$60.00">$75.00</span>
                                <span class="price-cycle" data-monthly="/mo" data-yearly="/mo">/mo</span>
                            </div>
                            <div class="price-subtext" data-monthly="Billed $75.00 monthly"
                                data-yearly="Billed $720.00 yearly (Save 20%)">Billed $75.00 monthly</div>
                        </div>

                        <a href="https://my.cloudflox.com/cart.php?a=add&pid=301&billingcycle=monthly" data-pid="301"
                            class="plan-cta-btn btn-plan-outline">
                            Choose Starter
                        </a>

                        <ul class="plan-features-list">
                            <li><span class="feat-icon-circle"><i class="las la-check"></i></span><span>Dedicated vCPU
                                    &amp; RAM: <strong>4 Cores / 16 GB RAM</strong></span></li>
                            <li><span class="feat-icon-circle"><i class="las la-check"></i></span><span>Storage:
                                    <strong>100 GB Gen4 NVMe</strong></span></li>
                            <li><span class="feat-icon-circle"><i class="las la-check"></i></span><span>Traffic:
                                    <strong>5 TB Monthly (1 Gbps Port)</strong></span></li>
                            <li><span class="feat-icon-circle"><i class="las la-check"></i></span><span>Management:
                                    <strong>100% Fully Managed</strong></span></li>
                            <li><span class="feat-icon-circle"><i class="las la-check"></i></span><span>Snapshots:
                                    <strong>Hourly &amp; Daily Backups</strong></span></li>
                            <li><span class="feat-icon-circle"><i class="las la-check"></i></span><span>Support SLA:
                                    <strong>24/7 Priority L3 (99.95% SLA)</strong></span></li>
                        </ul>
                    </div>
                </div>

                <!-- Premium Business -->
                <div class="col-xl-3 col-md-6">
                    <div class="modern-pricing-card featured">
                        <div class="plan-featured-badge">
                            <i class="las la-fire" aria-hidden="true"></i> Best Managed Performance
                        </div>
                        <div class="plan-card-header">
                            <div class="plan-card-icon bg-orange-subtle text-orange">
                                <i class="las la-gem" aria-hidden="true"></i>
                            </div>
                            <div>
                                <span class="card-tier-label text-orange">Premium Managed</span>
                                <h3 class="plan-name">Business</h3>
                                <p class="plan-tagline">High-volume e-commerce &amp; enterprise</p>
                            </div>
                        </div>

                        <div class="price-box">
                            <div class="price-amount">
                                <span class="price-val" data-monthly="$120.00" data-yearly="$96.00">$120.00</span>
                                <span class="price-cycle" data-monthly="/mo" data-yearly="/mo">/mo</span>
                            </div>
                            <div class="price-subtext text-orange-sub" data-monthly="Billed $120.00 monthly"
                                data-yearly="Billed $1,152.00 yearly (Save 20%)">Billed $120.00 monthly</div>
                        </div>

                        <a href="https://my.cloudflox.com/cart.php?a=add&pid=302&billingcycle=monthly" data-pid="302"
                            class="plan-cta-btn btn-plan-featured">
                            <span>Choose Business</span>
                            <i class="las la-arrow-right ms-2"></i>
                        </a>

                        <ul class="plan-features-list">
                            <li><span class="feat-icon-circle"><i class="las la-check"></i></span><span>Dedicated vCPU
                                    &amp; RAM: <strong>6 Cores / 24 GB RAM</strong></span></li>
                            <li><span class="feat-icon-circle"><i class="las la-check"></i></span><span>Storage:
                                    <strong>200 GB Gen4 NVMe</strong></span></li>
                            <li><span class="feat-icon-circle"><i class="las la-check"></i></span><span>Traffic:
                                    <strong>10 TB Monthly (1 Gbps Port)</strong></span></li>
                            <li><span class="feat-icon-circle"><i class="las la-check"></i></span><span>Specialist:
                                    <strong>Assigned L3 Tech Specialist</strong></span></li>
                            <li><span class="feat-icon-circle"><i class="las la-check"></i></span><span>Hardening:
                                    <strong>Kernel &amp; Server Optimization</strong></span></li>
                            <li><span class="feat-icon-circle"><i class="las la-check"></i></span><span>Guarantee:
                                    <strong>15-Min Response • 99.99% SLA</strong></span></li>
                        </ul>
                    </div>
                </div>

                <!-- Premium Pro -->
                <div class="col-xl-3 col-md-6">
                    <div class="modern-pricing-card">
                        <div class="plan-card-header">
                            <div class="plan-card-icon bg-blue-subtle text-primary">
                                <i class="las la-tachometer-alt" aria-hidden="true"></i>
                            </div>
                            <div>
                                <span class="card-tier-label text-primary">Premium Managed</span>
                                <h3 class="plan-name">Pro</h3>
                                <p class="plan-tagline">Multi-region apps &amp; media platforms</p>
                            </div>
                        </div>

                        <div class="price-box">
                            <div class="price-amount">
                                <span class="price-val" data-monthly="$210.00" data-yearly="$168.00">$210.00</span>
                                <span class="price-cycle" data-monthly="/mo" data-yearly="/mo">/mo</span>
                            </div>
                            <div class="price-subtext" data-monthly="Billed $210.00 monthly"
                                data-yearly="Billed $2,016.00 yearly (Save 20%)">Billed $210.00 monthly</div>
                        </div>

                        <a href="https://my.cloudflox.com/cart.php?a=add&pid=303&billingcycle=monthly" data-pid="303"
                            class="plan-cta-btn btn-plan-outline">
                            Choose Pro
                        </a>

                        <ul class="plan-features-list">
                            <li><span class="feat-icon-circle"><i class="las la-check"></i></span><span>Dedicated vCPU
                                    &amp; RAM: <strong>8 Cores / 32 GB RAM</strong></span></li>
                            <li><span class="feat-icon-circle"><i class="las la-check"></i></span><span>Storage:
                                    <strong>400 GB Gen4 NVMe</strong></span></li>
                            <li><span class="feat-icon-circle"><i class="las la-check"></i></span><span>Traffic:
                                    <strong>20 TB Monthly (1 Gbps Port)</strong></span></li>
                            <li><span class="feat-icon-circle"><i class="las la-check"></i></span><span>VIP Channels:
                                    <strong>Private Slack &amp; Direct Phone</strong></span></li>
                            <li><span class="feat-icon-circle"><i class="las la-check"></i></span><span>Architecture:
                                    <strong>Senior SysAdmin Assigned Lead</strong></span></li>
                            <li><span class="feat-icon-circle"><i class="las la-check"></i></span><span>Security:
                                    <strong>Custom DDoS Rules • 99.99% SLA</strong></span></li>
                        </ul>
                    </div>
                </div>

                <!-- Premium Enterprise -->
                <div class="col-xl-3 col-md-6">
                    <div class="modern-pricing-card">
                        <div class="plan-card-header">
                            <div class="plan-card-icon bg-emerald-subtle text-success">
                                <i class="las la-shield-alt" aria-hidden="true"></i>
                            </div>
                            <div>
                                <span class="card-tier-label text-success">Premium Managed</span>
                                <h3 class="plan-name">Enterprise</h3>
                                <p class="plan-tagline">Fin-tech clusters &amp; high scale</p>
                            </div>
                        </div>

                        <div class="price-box">
                            <div class="price-amount">
                                <span class="price-val" data-monthly="$350.00" data-yearly="$280.00">$350.00</span>
                                <span class="price-cycle" data-monthly="/mo" data-yearly="/mo">/mo</span>
                            </div>
                            <div class="price-subtext" data-monthly="Billed $350.00 monthly"
                                data-yearly="Billed $3,360.00 yearly (Save 20%)">Billed $350.00 monthly</div>
                        </div>

                        <a href="https://my.cloudflox.com/cart.php?a=add&pid=304&billingcycle=monthly" data-pid="304"
                            class="plan-cta-btn btn-plan-outline">
                            Choose Enterprise
                        </a>

                        <ul class="plan-features-list">
                            <li><span class="feat-icon-circle"><i class="las la-check"></i></span><span>Dedicated vCPU
                                    &amp; RAM: <strong>16 Cores / 64 GB RAM</strong></span></li>
                            <li><span class="feat-icon-circle"><i class="las la-check"></i></span><span>Storage:
                                    <strong>800 GB Gen4 NVMe</strong></span></li>
                            <li><span class="feat-icon-circle"><i class="las la-check"></i></span><span>Traffic:
                                    <strong>Unmetered 1 Gbps Dedicated Port</strong></span></li>
                            <li><span class="feat-icon-circle"><i class="las la-check"></i></span><span>DevOps:
                                    <strong>Dedicated 24/7 Lead Engineer</strong></span></li>
                            <li><span class="feat-icon-circle"><i class="las la-check"></i></span><span>Resilience:
                                    <strong>Multi-DC Instant Failover</strong></span></li>
                            <li><span class="feat-icon-circle"><i class="las la-check"></i></span><span>Guarantee:
                                    <strong>99.99% Financially-Backed SLA</strong></span></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- VPS & Dedicated Quick Discovery Banner -->
            <div class="cat-quick-discovery text-center mt-4" data-sal="fade" data-sal-duration="600">
                <span class="text-muted" style="color: 
                ;">Need root access, bare metal, or custom compute?</span>
                <a href="/vps-hosting/" class="cat-sublink ms-2">
                    <i class="las la-server"></i> Linux VPS from $7.99/mo &rarr;
                </a>
                <span class="mx-2 text-muted opacity-50">•</span>
                <a href="/dedicated-servers/" class="cat-sublink">
                    <i class="las la-hdd"></i> Dedicated Servers (Custom Quote) &rarr;
                </a>
            </div>

            <!-- Bottom Guarantees Strip -->
            <div class="pricing-guarantees-wrapper mt-4" data-sal="fade" data-sal-duration="600">
                <div class="guarantee-badge">
                    <i class="las la-undo-alt text-primary" aria-hidden="true"></i>
                    <span><strong>30-Day</strong> Money-Back Guarantee</span>
                </div>
                <div class="guarantee-badge">
                    <i class="las la-truck-moving text-success" aria-hidden="true"></i>
                    <span><strong>Free</strong> White-Glove Migration</span>
                </div>
                <div class="guarantee-badge">
                    <i class="las la-bolt text-warning" aria-hidden="true"></i>
                    <span><strong>Instant</strong> Automated Activation</span>
                </div>
                <div class="guarantee-badge">
                    <i class="las la-headset text-info" aria-hidden="true"></i>
                    <span><strong>24/7/365</strong> Level-3 Expert Assistance</span>
                </div>
            </div>

            <!-- Compare All CTA -->
            <div class="text-center mt-4">
                <a href="#compare-plans" class="btn-compare-all">
                    <span>Compare All Hosting Plan Specs</span>
                    <i class="las la-arrow-right ms-2" aria-hidden="true"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- =========================================================================
     3. LIVE NETWORK & GLOBAL METRICS STATS BAR (COMPACT VIDEO HUD)
     ========================================================================= -->
    <section class="modern-stats-section" id="network-metrics">
        <!-- Background Video -->
        <video autoplay loop muted playsinline class="stats-bg-video" preload="auto">
            <source src="assets/video/countup-bg.mp4" type="video/mp4">
        </video>

        <!-- Deep Ambient Glow Overlay -->
        <div class="stats-video-overlay"></div>

        <!-- Live Telemetry Content -->
        <div class="container position-relative stats-content-wrapper">
            <div class="row g-3 g-lg-4 justify-content-center">

                <!-- Stat 1: Uptime -->
                <div class="col-6 col-lg-3">
                    <div class="modern-stat-card">
                        <div class="stat-icon-wrap icon-green">
                            <i class="las la-shield-alt"></i>
                        </div>
                        <div class="stat-details">
                            <div class="stat-number">99.99%</div>
                            <div class="stat-label">Uptime SLA</div>
                            <div class="stat-meta">
                                <span class="live-pulse-dot pulse-green"></span> High Availability
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stat 2: Datacenters -->
                <div class="col-6 col-lg-3">
                    <div class="modern-stat-card">
                        <div class="stat-icon-wrap icon-blue">
                            <i class="las la-globe-americas"></i>
                        </div>
                        <div class="stat-details">
                            <div class="stat-number">6 Regions</div>
                            <div class="stat-label">Global Datacenters</div>
                            <div class="stat-meta">
                                <span class="live-pulse-dot pulse-blue"></span> Tier-3+ Facilities
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stat 3: Deployments -->
                <div class="col-6 col-lg-3">
                    <div class="modern-stat-card">
                        <div class="stat-icon-wrap icon-cyan">
                            <i class="las la-server"></i>
                        </div>
                        <div class="stat-details">
                            <div class="stat-number">250k+</div>
                            <div class="stat-label">Active Workloads</div>
                            <div class="stat-meta">
                                <span class="live-pulse-dot pulse-cyan"></span> Enterprise NVMe
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stat 4: Support Response -->
                <div class="col-6 col-lg-3">
                    <div class="modern-stat-card">
                        <div class="stat-icon-wrap icon-orange">
                            <i class="las la-bolt"></i>
                        </div>
                        <div class="stat-details">
                            <div class="stat-number">&lt; 15 min</div>
                            <div class="stat-label">Response Time</div>
                            <div class="stat-meta">
                                <span class="live-pulse-dot pulse-orange"></span> 24/7/365 SysAdmins
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>


    <!-- ==========================================
     START: HOSTING PACKAGE COMPARISON TABLE SECTION
     Place this directly below the High-Speed NVMe cPanel Web Hosting section
     ========================================== -->
    <section class="cf-comparison-section" id="compare-plans">
        <div class="container">

            <!-- Section Header & Controls -->
            <div class="cf-comp-header text-center">
                <span class="cf-comp-badge"><i class="feather-sliders"></i> Full Specifications</span>
                <h2 class="cf-comp-title">Compare Shared &amp; Managed <span>Hosting Plans</span></h2>
                <p class="cf-comp-subtitle">
                    Transparent CloudLinux LVE limits, NVMe storage allocations, and technical capabilities
                    side-by-side. No hidden throttles or surprise renewal fees.
                </p>

                <!-- Interactive Controls: Category Tabs & Billing Switcher -->
                <div class="cf-comp-controls-bar">
                    <!-- Tier Filter Tabs -->
                    <div class="cf-tier-tabs" role="tablist" aria-label="Hosting Tier Selector">
                        <button type="button" class="cf-tier-btn active" data-target="business"
                            onclick="switchComparisonCategory('business', this)">
                            <i class="feather-briefcase"></i> Business Web <span class="tab-count-pill">4 Plans</span>
                        </button>
                        <button type="button" class="cf-tier-btn" data-target="budget"
                            onclick="switchComparisonCategory('budget', this)">
                            <i class="las la-feather-alt"></i> Budget Hosting <span class="tab-count-pill">3
                                Plans</span>
                        </button>
                        <button type="button" class="cf-tier-btn" data-target="premium"
                            onclick="switchComparisonCategory('premium', this)">
                            <i class="feather-server"></i> Premium Managed <span class="tab-count-pill">4 Plans</span>
                        </button>
                        <button type="button" class="cf-tier-btn" data-target="all"
                            onclick="switchComparisonCategory('all', this)">
                            <i class="feather-grid"></i> All 11 Plans Matrix
                        </button>
                    </div>

                    <!-- Monthly / Yearly Billing Toggle -->
                    <div class="cf-billing-switch-wrap">
                        <span class="cf-bill-label active" id="cfCompLblMonthly">Monthly</span>
                        <label class="cf-toggle-switch" for="cfCompBillingToggle"
                            aria-label="Toggle Monthly or Yearly Billing">
                            <input type="checkbox" id="cfCompBillingToggle">
                            <span class="cf-toggle-slider"></span>
                        </label>
                        <span class="cf-bill-label" id="cfCompLblYearly">
                            Yearly <small class="cf-save-pill">Save 20%</small>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Mobile Horizontal Swipe Helper Hint -->
            <div class="cf-table-swipe-hint-wrapper text-center d-md-none mb-3">
                <span class="cf-table-swipe-hint">
                    <i class="las la-arrows-alt-h"></i> Swipe table horizontally to compare
                </span>
            </div>

            <!-- ==========================================
                 PANE 1: BUSINESS WEB HOSTING (DEFAULT)
                 ========================================== -->
            <div class="cf-comp-pane" id="compPane-business">
                <div class="cf-comparison-table-wrapper rounded-4 border bg-white shadow-sm mb-4">
                    <div class="table-responsive">
                        <table class="table cf-comparison-table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="cf-sticky-feature-th">
                                        <div class="cf-feature-heading">
                                            <span>Plan Specifications</span>
                                            <small>Detailed limits &amp; features</small>
                                        </div>
                                    </th>
                                    <th class="cf-plan-header-cell text-center">
                                        <span class="d-block fw-bold fs-16 text-slate-900">Starter</span>
                                        <span class="fs-14 text-indigo fw-extrabold cf-comp-price-val" data-monthly="$7.50/mo"
                                            data-yearly="$6.00/mo">$7.50/mo</span>
                                    </th>
                                    <th class="cf-plan-header-cell popular-col-business text-center">
                                        <span class="cf-popular-tag-business">MOST POPULAR</span>
                                        <span class="d-block fw-bold fs-16 text-indigo mt-2">Business</span>
                                        <span class="fs-14 text-indigo fw-extrabold cf-comp-price-val" data-monthly="$15.00/mo"
                                            data-yearly="$12.00/mo">$15.00/mo</span>
                                    </th>
                                    <th class="cf-plan-header-cell text-center">
                                        <span class="d-block fw-bold fs-16 text-slate-900">Professional</span>
                                        <span class="fs-14 text-indigo fw-extrabold cf-comp-price-val" data-monthly="$27.00/mo"
                                            data-yearly="$21.60/mo">$27.00/mo</span>
                                    </th>
                                    <th class="cf-plan-header-cell text-center">
                                        <span class="d-block fw-bold fs-16 text-slate-900">Enterprise</span>
                                        <span class="fs-14 text-indigo fw-extrabold cf-comp-price-val" data-monthly="$48.00/mo"
                                            data-yearly="$38.40/mo">$48.00/mo</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Group 1: Compute & LVE -->
                                <tr class="cf-cat-header-row">
                                    <td colspan="5"><i class="feather-cpu"></i> Compute &amp; CloudLinux LVE Resources
                                    </td>
                                </tr>
                                <tr>
                                    <th class="cf-sticky-feature-cell">CPU Cores<span class="feat-subtext">Isolated
                                            CloudLinux LVE</span></th>
                                    <td><strong>1 Core</strong> (100%)</td>
                                    <td class="popular-col"><strong>2 Cores</strong> (200%)</td>
                                    <td><strong>3 Cores</strong> (300%)</td>
                                    <td><strong>4 Cores</strong> (400%)</td>
                                </tr>
                                <tr>
                                    <th class="cf-sticky-feature-cell">Physical RAM<span class="feat-subtext">Dedicated
                                            memory pool</span></th>
                                    <td>2 GB DDR4/5</td>
                                    <td class="popular-col"><strong>4 GB DDR4/5</strong></td>
                                    <td>6 GB DDR4/5</td>
                                    <td>8 GB DDR4/5</td>
                                </tr>
                                <tr>
                                    <th class="cf-sticky-feature-cell">I/O Speed &amp; IOPS<span
                                            class="feat-subtext">Disk read/write limits</span></th>
                                    <td>30 MB/s &bull; 2,048 IOPS</td>
                                    <td class="popular-col"><strong>50 MB/s &bull; 4,096 IOPS</strong></td>
                                    <td>80 MB/s &bull; 8,192 IOPS</td>
                                    <td>100 MB/s &bull; 10,240 IOPS</td>
                                </tr>
                                <tr>
                                    <th class="cf-sticky-feature-cell">Entry Processes (EP)<span
                                            class="feat-subtext">Concurrent PHP workers</span></th>
                                    <td>30 EP (100 NPROC)</td>
                                    <td class="popular-col"><strong>50 EP (150 NPROC)</strong></td>
                                    <td>80 EP (250 NPROC)</td>
                                    <td>120 EP (400 NPROC)</td>
                                </tr>
                                <tr>
                                    <th class="cf-sticky-feature-cell">Inode Limit<span class="feat-subtext">Total file
                                            &amp; folder count</span></th>
                                    <td>300,000 Inodes</td>
                                    <td class="popular-col"><strong>600,000 Inodes</strong></td>
                                    <td>1,000,000 Inodes</td>
                                    <td>2,000,000 Inodes</td>
                                </tr>

                                <!-- Group 2: Storage & Domains -->
                                <tr class="cf-cat-header-row">
                                    <td colspan="5"><i class="feather-hard-drive"></i> Storage, Domains &amp; Bandwidth
                                    </td>
                                </tr>
                                <tr>
                                    <th class="cf-sticky-feature-cell">NVMe SSD Storage<span
                                            class="feat-subtext">Enterprise Gen4 NVMe</span></th>
                                    <td><strong>15 GB</strong> NVMe</td>
                                    <td class="popular-col"><strong>35 GB</strong> NVMe</td>
                                    <td><strong>75 GB</strong> NVMe</td>
                                    <td><strong>150 GB</strong> NVMe</td>
                                </tr>
                                <tr>
                                    <th class="cf-sticky-feature-cell">Hosted Websites<span class="feat-subtext">Domain
                                            names allowed</span></th>
                                    <td>1 Website</td>
                                    <td class="popular-col"><strong>5 Websites</strong></td>
                                    <td>15 Websites</td>
                                    <td><span class="cf-badge-tag badge-tag-green">Unlimited</span></td>
                                </tr>
                                <tr>
                                    <th class="cf-sticky-feature-cell">Monthly Bandwidth<span class="feat-subtext">1Gbps
                                            Redundant Uplink</span></th>
                                    <td>Unmetered</td>
                                    <td class="popular-col">Unmetered</td>
                                    <td>Unmetered</td>
                                    <td>Unmetered</td>
                                </tr>
                                <tr>
                                    <th class="cf-sticky-feature-cell">Dedicated IPv4<span class="feat-subtext">Isolated
                                            IP reputation</span></th>
                                    <td>Add-on ($3/mo)</td>
                                    <td class="popular-col">Add-on ($3/mo)</td>
                                    <td>Add-on ($3/mo)</td>
                                    <td><span class="cf-badge-tag badge-tag-blue">1 Free Dedicated IP</span></td>
                                </tr>

                                <!-- Group 3: Features & Acceleration -->
                                <tr class="cf-cat-header-row">
                                    <td colspan="5"><i class="las la-shield-alt"></i> Platform Stack, Security &amp;
                                        Backups</td>
                                </tr>
                                <tr>
                                    <th class="cf-sticky-feature-cell">Web Server &amp; Caching<span
                                            class="feat-subtext">Ultra-low TTFB</span></th>
                                    <td>LiteSpeed + LSCache</td>
                                    <td class="popular-col">LiteSpeed + LSCache</td>
                                    <td>LiteSpeed + LSCache</td>
                                    <td>LiteSpeed + LSCache</td>
                                </tr>
                                <tr>
                                    <th class="cf-sticky-feature-cell">cPanel Control Panel<span
                                            class="feat-subtext">Official cPanel License</span></th>
                                    <td><span class="cf-check-icon"><i class="feather-check"></i></span></td>
                                    <td class="popular-col"><span class="cf-check-icon"><i
                                                class="feather-check"></i></span></td>
                                    <td><span class="cf-check-icon"><i class="feather-check"></i></span></td>
                                    <td><span class="cf-check-icon"><i class="feather-check"></i></span></td>
                                </tr>
                                <tr>
                                    <th class="cf-sticky-feature-cell">Automated Backups<span
                                            class="feat-subtext">JetBackup snapshot recovery</span></th>
                                    <td>Daily (30-day retention)</td>
                                    <td class="popular-col"><strong>Daily (30-day retention)</strong></td>
                                    <td>Daily (30-day retention)</td>
                                    <td>2x Daily + Off-site S3</td>
                                </tr>
                                <tr>
                                    <th class="cf-sticky-feature-cell">Imunify360 + AI WAF<span
                                            class="feat-subtext">Proactive malware defense</span></th>
                                    <td><span class="cf-check-icon"><i class="feather-check"></i></span></td>
                                    <td class="popular-col"><span class="cf-check-icon"><i
                                                class="feather-check"></i></span></td>
                                    <td><span class="cf-check-icon"><i class="feather-check"></i></span></td>
                                    <td><span class="cf-check-icon"><i class="feather-check"></i></span></td>
                                </tr>
                                <tr>
                                    <th class="cf-sticky-feature-cell">Free SSL &amp; HTTP/3<span
                                            class="feat-subtext">Automated Let's Encrypt</span></th>
                                    <td><span class="cf-check-icon"><i class="feather-check"></i></span></td>
                                    <td class="popular-col"><span class="cf-check-icon"><i
                                                class="feather-check"></i></span></td>
                                    <td><span class="cf-check-icon"><i class="feather-check"></i></span></td>
                                    <td><span class="cf-check-icon"><i class="feather-check"></i></span></td>
                                </tr>
                                <tr>
                                    <th class="cf-sticky-feature-cell">Business Mailboxes<span
                                            class="feat-subtext">Anti-spam protection</span></th>
                                    <td>5 Accounts</td>
                                    <td class="popular-col"><strong>25 Accounts</strong></td>
                                    <td>Unlimited</td>
                                    <td>Unlimited</td>
                                </tr>
                                <tr>
                                    <th class="cf-sticky-feature-cell">Free Site Migration<span
                                            class="feat-subtext">Zero-downtime transfer</span></th>
                                    <td>1 Website</td>
                                    <td class="popular-col">Up to 3 Websites</td>
                                    <td>Up to 5 Websites</td>
                                    <td>Unlimited Websites</td>
                                </tr>
                                <tr>
                                    <th class="cf-sticky-feature-cell">Support &amp; SLA Guarantee<span
                                            class="feat-subtext">Response time commitment</span></th>
                                    <td>Standard 24/7 Ticket</td>
                                    <td class="popular-col"><strong>24/7 Priority Support</strong></td>
                                    <td>24/7 Priority Support</td>
                                    <td><span class="cf-badge-tag badge-tag-blue">Dedicated 24/7 SLA</span></td>
                                </tr>
                                <tr class="cf-table-action-row">
                                    <th class="cf-sticky-feature-cell bg-light">
                                        <div class="py-1">
                                            <span class="d-block fw-bold text-slate-900 fs-14">Get Started</span>
                                            <small class="text-muted fs-11">Instant Setup • 30-Day Guarantee</small>
                                        </div>
                                    </th>
                                    <td class="text-center p-3">
                                        <a href="https://my.cloudflox.com/cart.php?a=add&pid=201&billingcycle=monthly" data-pid="201"
                                            class="cf-table-cta-btn btn-cta-subtle">
                                            <span>Buy Starter</span>
                                            <i class="las la-arrow-right ms-1"></i>
                                        </a>
                                    </td>
                                    <td class="text-center popular-col-business p-3">
                                        <a href="https://my.cloudflox.com/cart.php?a=add&pid=202&billingcycle=monthly" data-pid="202"
                                            class="cf-table-cta-btn btn-cta-featured">
                                            <span>Buy Business</span>
                                            <i class="las la-arrow-right ms-1"></i>
                                        </a>
                                    </td>
                                    <td class="text-center p-3">
                                        <a href="https://my.cloudflox.com/cart.php?a=add&pid=203&billingcycle=monthly" data-pid="203"
                                            class="cf-table-cta-btn btn-cta-subtle">
                                            <span>Buy Professional</span>
                                            <i class="las la-arrow-right ms-1"></i>
                                        </a>
                                    </td>
                                    <td class="text-center p-3">
                                        <a href="https://my.cloudflox.com/cart.php?a=add&pid=204&billingcycle=monthly" data-pid="204"
                                            class="cf-table-cta-btn btn-cta-primary">
                                            <span>Buy Enterprise</span>
                                            <i class="las la-arrow-right ms-1"></i>
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ==========================================
                 PANE 2: BUDGET HOSTING
                 ========================================== -->
            <div class="cf-comp-pane" id="compPane-budget" style="display: none;">
                <div class="cf-comparison-table-wrapper rounded-4 border bg-white shadow-sm mb-4">
                    <div class="table-responsive">
                        <table class="table cf-comparison-table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="cf-sticky-feature-th">
                                        <div class="cf-feature-heading">
                                            <span>Plan Specifications</span>
                                            <small>Detailed limits &amp; features</small>
                                        </div>
                                    </th>
                                    <th class="cf-plan-header-cell text-center">
                                        <span class="d-block fw-bold fs-16 text-slate-900">Mini</span>
                                        <span class="fs-14 text-emerald fw-extrabold cf-comp-price-val" data-monthly="$3.00/mo"
                                            data-yearly="$2.40/mo">$3.00/mo</span>
                                    </th>
                                    <th class="cf-plan-header-cell popular-col-budget text-center">
                                        <span class="cf-popular-tag-budget">MOST POPULAR</span>
                                        <span class="d-block fw-bold fs-16 text-emerald mt-2">Basic</span>
                                        <span class="fs-14 text-emerald fw-extrabold cf-comp-price-val" data-monthly="$4.50/mo"
                                            data-yearly="$3.60/mo">$4.50/mo</span>
                                    </th>
                                    <th class="cf-plan-header-cell text-center">
                                        <span class="d-block fw-bold fs-16 text-slate-900">Plus</span>
                                        <span class="fs-14 text-emerald fw-extrabold cf-comp-price-val" data-monthly="$6.00/mo"
                                            data-yearly="$4.80/mo">$6.00/mo</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Group 1: Compute & Limits -->
                                <tr class="cf-cat-header-row">
                                    <td colspan="4"><i class="feather-cpu"></i> Hardware &amp; Performance Limits</td>
                                </tr>
                                <tr>
                                    <th class="cf-sticky-feature-cell">CPU Allocation<span
                                            class="feat-subtext">CloudLinux Virtual Cores</span></th>
                                    <td>1 Core (100%)</td>
                                    <td class="popular-col"><strong>1 Core</strong> (100%)</td>
                                    <td><strong>1.5 Cores</strong> (150%)</td>
                                </tr>
                                <tr>
                                    <th class="cf-sticky-feature-cell">Physical RAM<span class="feat-subtext">Dedicated
                                            memory pool</span></th>
                                    <td>1 GB RAM</td>
                                    <td class="popular-col"><strong>1.5 GB RAM</strong></td>
                                    <td>2 GB RAM</td>
                                </tr>
                                <tr>
                                    <th class="cf-sticky-feature-cell">I/O Speed &amp; IOPS<span
                                            class="feat-subtext">Disk read/write</span></th>
                                    <td>15 MB/s &bull; 1,024 IOPS</td>
                                    <td class="popular-col"><strong>20 MB/s &bull; 1,024 IOPS</strong></td>
                                    <td>30 MB/s &bull; 2,048 IOPS</td>
                                </tr>
                                <tr>
                                    <th class="cf-sticky-feature-cell">Entry Processes (EP)<span
                                            class="feat-subtext">Concurrent PHP workers</span></th>
                                    <td>20 EP (50 NPROC)</td>
                                    <td class="popular-col"><strong>25 EP (75 NPROC)</strong></td>
                                    <td>30 EP (100 NPROC)</td>
                                </tr>
                                <tr>
                                    <th class="cf-sticky-feature-cell">Inode Limit<span class="feat-subtext">Total file
                                            count</span></th>
                                    <td>150,000 Inodes</td>
                                    <td class="popular-col"><strong>250,000 Inodes</strong></td>
                                    <td>350,000 Inodes</td>
                                </tr>

                                <!-- Group 2: Storage & Domains -->
                                <tr class="cf-cat-header-row">
                                    <td colspan="4"><i class="feather-hard-drive"></i> Storage, Domains &amp; Bandwidth
                                    </td>
                                </tr>
                                <tr>
                                    <th class="cf-sticky-feature-cell">NVMe SSD Storage<span
                                            class="feat-subtext">Enterprise NVMe</span></th>
                                    <td><strong>5 GB</strong> NVMe</td>
                                    <td class="popular-col"><strong>10 GB</strong> NVMe</td>
                                    <td><strong>20 GB</strong> NVMe</td>
                                </tr>
                                <tr>
                                    <th class="cf-sticky-feature-cell">Websites Allowed<span class="feat-subtext">Hosted
                                            domains</span></th>
                                    <td>1 Website</td>
                                    <td class="popular-col"><strong>2 Websites</strong></td>
                                    <td>5 Websites</td>
                                </tr>
                                <tr>
                                    <th class="cf-sticky-feature-cell">Bandwidth<span class="feat-subtext">Monthly
                                            traffic</span></th>
                                    <td>250 GB</td>
                                    <td class="popular-col"><strong>500 GB</strong></td>
                                    <td>Unmetered</td>
                                </tr>
                                <tr>
                                    <th class="cf-sticky-feature-cell">Email Accounts<span class="feat-subtext">Custom
                                            domain mail</span></th>
                                    <td>2 Accounts</td>
                                    <td class="popular-col"><strong>5 Accounts</strong></td>
                                    <td>10 Accounts</td>
                                </tr>
                                <tr>
                                    <th class="cf-sticky-feature-cell">MySQL Databases<span class="feat-subtext">MariaDB
                                            databases</span></th>
                                    <td>1 Database</td>
                                    <td class="popular-col"><strong>3 Databases</strong></td>
                                    <td>5 Databases</td>
                                </tr>

                                <!-- Group 3: Features & Stack -->
                                <tr class="cf-cat-header-row">
                                    <td colspan="4"><i class="las la-shield-alt"></i> Control Panel &amp; Security</td>
                                </tr>
                                <tr>
                                    <th class="cf-sticky-feature-cell">cPanel Control Panel<span
                                            class="feat-subtext">Standard cPanel GUI</span></th>
                                    <td><span class="cf-check-icon"><i class="feather-check"></i></span></td>
                                    <td class="popular-col"><span class="cf-check-icon"><i
                                                class="feather-check"></i></span></td>
                                    <td><span class="cf-check-icon"><i class="feather-check"></i></span></td>
                                </tr>
                                <tr>
                                    <th class="cf-sticky-feature-cell">LiteSpeed Web Server<span
                                            class="feat-subtext">LSCache WordPress plugin</span></th>
                                    <td><span class="cf-check-icon"><i class="feather-check"></i></span></td>
                                    <td class="popular-col"><span class="cf-check-icon"><i
                                                class="feather-check"></i></span></td>
                                    <td><span class="cf-check-icon"><i class="feather-check"></i></span></td>
                                </tr>
                                <tr>
                                    <th class="cf-sticky-feature-cell">Free SSL Certificate<span
                                            class="feat-subtext">AutoSSL Let's Encrypt</span></th>
                                    <td><span class="cf-check-icon"><i class="feather-check"></i></span></td>
                                    <td class="popular-col"><span class="cf-check-icon"><i
                                                class="feather-check"></i></span></td>
                                    <td><span class="cf-check-icon"><i class="feather-check"></i></span></td>
                                </tr>
                                <tr>
                                    <th class="cf-sticky-feature-cell">Automated Backups<span
                                            class="feat-subtext">JetBackup snapshots</span></th>
                                    <td>Weekly</td>
                                    <td class="popular-col"><strong>Weekly</strong></td>
                                    <td>Weekly</td>
                                </tr>
                                <tr>
                                    <th class="cf-sticky-feature-cell">Imunify360 Malware Scan<span
                                            class="feat-subtext">Automated cleanup</span></th>
                                    <td><span class="cf-check-icon"><i class="feather-check"></i></span></td>
                                    <td class="popular-col"><span class="cf-check-icon"><i
                                                class="feather-check"></i></span></td>
                                    <td><span class="cf-check-icon"><i class="feather-check"></i></span></td>
                                </tr>
                                <tr>
                                    <th class="cf-sticky-feature-cell">Support Scope<span
                                            class="feat-subtext">Assistance level</span></th>
                                    <td>Standard 24/7 Ticket</td>
                                    <td class="popular-col">Standard 24/7 Ticket</td>
                                    <td>Standard 24/7 Ticket</td>
                                </tr>
                                <tr class="cf-table-action-row">
                                    <th class="cf-sticky-feature-cell bg-light">
                                        <div class="py-1">
                                            <span class="d-block fw-bold text-slate-900 fs-14">Get Started</span>
                                            <small class="text-muted fs-11">Instant Setup • 30-Day Guarantee</small>
                                        </div>
                                    </th>
                                    <td class="text-center p-3">
                                        <a href="https://my.cloudflox.com/cart.php?a=add&pid=101&billingcycle=monthly" data-pid="101"
                                            class="cf-table-cta-btn btn-cta-subtle">
                                            <span>Buy Mini</span>
                                            <i class="las la-arrow-right ms-1"></i>
                                        </a>
                                    </td>
                                    <td class="text-center popular-col-budget p-3">
                                        <a href="https://my.cloudflox.com/cart.php?a=add&pid=102&billingcycle=monthly" data-pid="102"
                                            class="cf-table-cta-btn btn-cta-featured">
                                            <span>Buy Basic</span>
                                            <i class="las la-arrow-right ms-1"></i>
                                        </a>
                                    </td>
                                    <td class="text-center p-3">
                                        <a href="https://my.cloudflox.com/cart.php?a=add&pid=103&billingcycle=monthly" data-pid="103"
                                            class="cf-table-cta-btn btn-cta-subtle">
                                            <span>Buy Plus</span>
                                            <i class="las la-arrow-right ms-1"></i>
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ==========================================
                 PANE 3: PREMIUM MANAGED HOSTING
                 ========================================== -->
            <div class="cf-comp-pane" id="compPane-premium" style="display: none;">
                <div class="cf-comparison-table-wrapper rounded-4 border bg-white shadow-sm mb-4">
                    <div class="table-responsive">
                        <table class="table cf-comparison-table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="cf-sticky-feature-th">
                                        <div class="cf-feature-heading">
                                            <span>Plan Specifications</span>
                                            <small>Detailed limits &amp; features</small>
                                        </div>
                                    </th>
                                    <th class="cf-plan-header-cell text-center">
                                        <span class="d-block fw-bold fs-16 text-slate-900">Starter</span>
                                        <span class="fs-14 text-orange fw-extrabold cf-comp-price-val" data-monthly="$75.00/mo"
                                            data-yearly="$60.00/mo">$75.00/mo</span>
                                    </th>
                                    <th class="cf-plan-header-cell popular-col-premium text-center">
                                        <span class="cf-popular-tag-premium">MOST POPULAR</span>
                                        <span class="d-block fw-bold fs-16 text-orange mt-2">Business</span>
                                        <span class="fs-14 text-orange fw-extrabold cf-comp-price-val" data-monthly="$120.00/mo"
                                            data-yearly="$96.00/mo">$120.00/mo</span>
                                    </th>
                                    <th class="cf-plan-header-cell text-center">
                                        <span class="d-block fw-bold fs-16 text-slate-900">Pro</span>
                                        <span class="fs-14 text-orange fw-extrabold cf-comp-price-val" data-monthly="$210.00/mo"
                                            data-yearly="$168.00/mo">$210.00/mo</span>
                                    </th>
                                    <th class="cf-plan-header-cell text-center">
                                        <span class="d-block fw-bold fs-16 text-slate-900">Enterprise</span>
                                        <span class="fs-14 text-orange fw-extrabold cf-comp-price-val" data-monthly="$350.00/mo"
                                            data-yearly="$280.00/mo">$350.00/mo</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Group 1: Dedicated Compute -->
                                <tr class="cf-cat-header-row">
                                    <td colspan="5"><i class="feather-server"></i> Dedicated Cloud Infrastructure &amp;
                                        RAM</td>
                                </tr>
                                <tr>
                                    <th class="cf-sticky-feature-cell">Dedicated vCPU Cores<span
                                            class="feat-subtext">Guaranteed isolated compute</span></th>
                                    <td><strong>4 vCPU</strong></td>
                                    <td class="popular-col"><strong>8 vCPU</strong></td>
                                    <td><strong>12 vCPU</strong></td>
                                    <td><strong>16 vCPU</strong></td>
                                </tr>
                                <tr>
                                    <th class="cf-sticky-feature-cell">Dedicated ECC RAM<span class="feat-subtext">No
                                            resource contention</span></th>
                                    <td><strong>16 GB</strong> RAM</td>
                                    <td class="popular-col"><strong>32 GB</strong> RAM</td>
                                    <td><strong>64 GB</strong> RAM</td>
                                    <td><strong>128 GB</strong> RAM</td>
                                </tr>
                                <tr>
                                    <th class="cf-sticky-feature-cell">I/O Speed &amp; IOPS<span
                                            class="feat-subtext">Enterprise NVMe throughput</span></th>
                                    <td>250 MB/s &bull; 25K IOPS</td>
                                    <td class="popular-col"><strong>500 MB/s &bull; 50K IOPS</strong></td>
                                    <td>1,000 MB/s &bull; 100K IOPS</td>
                                    <td>1,500 MB/s &bull; Uncapped IOPS</td>
                                </tr>
                                <tr>
                                    <th class="cf-sticky-feature-cell">Entry Processes (EP)<span
                                            class="feat-subtext">Concurrent heavy app tasks</span></th>
                                    <td>250 EP (800 NPROC)</td>
                                    <td class="popular-col"><strong>500 EP (1,500 NPROC)</strong></td>
                                    <td>1,000 EP (3,000 NPROC)</td>
                                    <td>2,000 EP (5,000 NPROC)</td>
                                </tr>
                                <tr>
                                    <th class="cf-sticky-feature-cell">Inode Limits<span class="feat-subtext">Enterprise
                                            file volumes</span></th>
                                    <td>5,000,000</td>
                                    <td class="popular-col"><strong>10,000,000</strong></td>
                                    <td><span class="cf-badge-tag badge-tag-green">Unlimited</span></td>
                                    <td><span class="cf-badge-tag badge-tag-green">Unlimited</span></td>
                                </tr>

                                <!-- Group 2: Storage & Networking -->
                                <tr class="cf-cat-header-row">
                                    <td colspan="5"><i class="feather-hard-drive"></i> NVMe Allocations, IPs &amp;
                                        Network</td>
                                </tr>
                                <tr>
                                    <th class="cf-sticky-feature-cell">Enterprise NVMe<span
                                            class="feat-subtext">High-redundancy RAID-10</span></th>
                                    <td><strong>100 GB</strong> NVMe</td>
                                    <td class="popular-col"><strong>250 GB</strong> NVMe</td>
                                    <td><strong>500 GB</strong> NVMe</td>
                                    <td><strong>1,000 GB (1TB)</strong> NVMe</td>
                                </tr>
                                <tr>
                                    <th class="cf-sticky-feature-cell">Dedicated IPv4<span class="feat-subtext">Clean
                                            dedicated IPs</span></th>
                                    <td>1 Dedicated IP</td>
                                    <td class="popular-col"><strong>1 Dedicated IP</strong></td>
                                    <td>2 Dedicated IPs</td>
                                    <td>4 Dedicated IPs</td>
                                </tr>
                                <tr>
                                    <th class="cf-sticky-feature-cell">Websites &amp; Databases<span
                                            class="feat-subtext">Multi-domain hosting</span></th>
                                    <td><span class="cf-badge-tag badge-tag-green">Unlimited</span></td>
                                    <td class="popular-col"><span class="cf-badge-tag badge-tag-green">Unlimited</span>
                                    </td>
                                    <td><span class="cf-badge-tag badge-tag-green">Unlimited</span></td>
                                    <td><span class="cf-badge-tag badge-tag-green">Unlimited</span></td>
                                </tr>
                                <tr>
                                    <th class="cf-sticky-feature-cell">Network Backbone<span
                                            class="feat-subtext">Anti-DDoS protected</span></th>
                                    <td>10Gbps Redundant Uplink</td>
                                    <td class="popular-col">10Gbps Redundant Uplink</td>
                                    <td>10Gbps Redundant Uplink</td>
                                    <td>10Gbps Redundant Uplink</td>
                                </tr>

                                <!-- Group 3: Managed Services & SLA -->
                                <tr class="cf-cat-header-row">
                                    <td colspan="5"><i class="feather-settings"></i> Fully Managed Operations &amp;
                                        Support</td>
                                </tr>
                                <tr>
                                    <th class="cf-sticky-feature-cell">Control Panel Stack<span class="feat-subtext">WHM
                                            + cPanel Licensing</span></th>
                                    <td>cPanel / WHM Included</td>
                                    <td class="popular-col"><strong>cPanel / WHM Included</strong></td>
                                    <td>cPanel / WHM Included</td>
                                    <td>cPanel / WHM Included</td>
                                </tr>
                                <tr>
                                    <th class="cf-sticky-feature-cell">Dedicated Redis Cache<span
                                            class="feat-subtext">In-memory database accelerator</span></th>
                                    <td><span class="cf-check-icon"><i class="feather-check"></i></span></td>
                                    <td class="popular-col"><span class="cf-check-icon"><i
                                                class="feather-check"></i></span></td>
                                    <td><span class="cf-check-icon"><i class="feather-check"></i></span></td>
                                    <td><span class="cf-check-icon"><i class="feather-check"></i></span></td>
                                </tr>
                                <tr>
                                    <th class="cf-sticky-feature-cell">Automated Backups<span
                                            class="feat-subtext">Real-time snapshots</span></th>
                                    <td>2x Daily + AWS S3</td>
                                    <td class="popular-col"><strong>2x Daily + AWS S3</strong></td>
                                    <td>2x Daily + AWS S3</td>
                                    <td>Continuous + AWS S3</td>
                                </tr>
                                <tr>
                                    <th class="cf-sticky-feature-cell">Proactive System Tuning<span
                                            class="feat-subtext">DB, PHP-FPM &amp; MySQL optimization</span></th>
                                    <td>Monthly Audit</td>
                                    <td class="popular-col"><strong>Bi-weekly Audit</strong></td>
                                    <td>Continuous Tuning</td>
                                    <td>Continuous Tuning</td>
                                </tr>
                                <tr>
                                    <th class="cf-sticky-feature-cell">Support SLA Guarantee<span
                                            class="feat-subtext">Dedicated response time</span></th>
                                    <td><span class="cf-badge-tag badge-tag-blue">&lt; 15 Min SLA</span></td>
                                    <td class="popular-col"><span class="cf-badge-tag badge-tag-blue">&lt; 10 Min
                                            SLA</span></td>
                                    <td><span class="cf-badge-tag badge-tag-orange">Senior SysAdmin SLA</span></td>
                                    <td><span class="cf-badge-tag badge-tag-orange">Dedicated DevOps SLA</span></td>
                                </tr>
                                <tr class="cf-table-action-row">
                                    <th class="cf-sticky-feature-cell bg-light">
                                        <div class="py-1">
                                            <span class="d-block fw-bold text-slate-900 fs-14">Get Started</span>
                                            <small class="text-muted fs-11">Dedicated Compute • VIP SLA</small>
                                        </div>
                                    </th>
                                    <td class="text-center p-3">
                                        <a href="https://my.cloudflox.com/cart.php?a=add&pid=301&billingcycle=monthly" data-pid="301"
                                            class="cf-table-cta-btn btn-cta-subtle">
                                            <span>Buy Starter</span>
                                            <i class="las la-arrow-right ms-1"></i>
                                        </a>
                                    </td>
                                    <td class="text-center popular-col-premium p-3">
                                        <a href="https://my.cloudflox.com/cart.php?a=add&pid=302&billingcycle=monthly" data-pid="302"
                                            class="cf-table-cta-btn btn-cta-featured">
                                            <span>Buy Business</span>
                                            <i class="las la-arrow-right ms-1"></i>
                                        </a>
                                    </td>
                                    <td class="text-center p-3">
                                        <a href="https://my.cloudflox.com/cart.php?a=add&pid=303&billingcycle=monthly" data-pid="303"
                                            class="cf-table-cta-btn btn-cta-subtle">
                                            <span>Buy Pro</span>
                                            <i class="las la-arrow-right ms-1"></i>
                                        </a>
                                    </td>
                                    <td class="text-center p-3">
                                        <a href="https://my.cloudflox.com/cart.php?a=add&pid=304&billingcycle=monthly" data-pid="304"
                                            class="cf-table-cta-btn btn-cta-primary">
                                            <span>Buy Enterprise</span>
                                            <i class="las la-arrow-right ms-1"></i>
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ==========================================
                 PANE 4: ALL 11 PLANS MATRIX (DEEP DIVE)
                 ========================================== -->
            <div class="cf-comp-pane" id="compPane-all" style="display: none;">
                <!-- Matrix Horizontal Scroll Navigation Helper -->
                <div class="d-flex align-items-center justify-content-between mb-2 px-1">
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1 fs-12 fw-semibold">
                        <i class="las la-arrows-alt-h me-1"></i> Scroll horizontally to compare all 11 plans
                    </span>
                    <div class="d-none d-sm-flex align-items-center gap-1">
                        <button type="button" class="btn btn-sm btn-light border bg-white rounded-circle shadow-xs" onclick="scrollMatrixTable('left')" aria-label="Scroll left" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;">
                            <i class="las la-angle-left"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-light border bg-white rounded-circle shadow-xs" onclick="scrollMatrixTable('right')" aria-label="Scroll right" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;">
                            <i class="las la-angle-right"></i>
                        </button>
                    </div>
                </div>

                <div class="cf-comparison-table-wrapper rounded-4 border bg-white shadow-sm mb-4">
                    <div class="table-responsive">
                        <table class="table cf-comparison-table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="cf-sticky-feature-th">
                                        <div class="cf-feature-heading">
                                            <span>Full 11-Plan Matrix</span>
                                            <small>Side-by-Side Comparison</small>
                                        </div>
                                    </th>
                                    <!-- Budget 3 -->
                                    <th class="cf-plan-header-cell text-center">
                                        <span class="d-block fw-bold fs-15 text-slate-900">Mini</span>
                                        <span class="fs-13 text-emerald fw-extrabold cf-comp-price-val" data-monthly="$3.00/mo"
                                            data-yearly="$2.40/mo">$3.00/mo</span>
                                    </th>
                                    <th class="cf-plan-header-cell text-center">
                                        <span class="d-block fw-bold fs-15 text-slate-900">Basic</span>
                                        <span class="fs-13 text-emerald fw-extrabold cf-comp-price-val" data-monthly="$4.50/mo"
                                            data-yearly="$3.60/mo">$4.50/mo</span>
                                    </th>
                                    <th class="cf-plan-header-cell text-center">
                                        <span class="d-block fw-bold fs-15 text-slate-900">Plus</span>
                                        <span class="fs-13 text-emerald fw-extrabold cf-comp-price-val" data-monthly="$6.00/mo"
                                            data-yearly="$4.80/mo">$6.00/mo</span>
                                    </th>
                                    <!-- Business 4 -->
                                    <th class="cf-plan-header-cell text-center">
                                        <span class="d-block fw-bold fs-15 text-slate-900">Starter</span>
                                        <span class="fs-13 text-indigo fw-extrabold cf-comp-price-val" data-monthly="$7.50/mo"
                                            data-yearly="$6.00/mo">$7.50/mo</span>
                                    </th>
                                    <th class="cf-plan-header-cell popular-col-business text-center">
                                        <span class="cf-popular-tag-business">FEATURED</span>
                                        <span class="d-block fw-bold fs-15 text-indigo mt-2">Business</span>
                                        <span class="fs-13 text-indigo fw-extrabold cf-comp-price-val" data-monthly="$15.00/mo"
                                            data-yearly="$12.00/mo">$15.00/mo</span>
                                    </th>
                                    <th class="cf-plan-header-cell text-center">
                                        <span class="d-block fw-bold fs-15 text-slate-900">Pro</span>
                                        <span class="fs-13 text-indigo fw-extrabold cf-comp-price-val" data-monthly="$27.00/mo"
                                            data-yearly="$21.60/mo">$27.00/mo</span>
                                    </th>
                                    <th class="cf-plan-header-cell text-center">
                                        <span class="d-block fw-bold fs-15 text-slate-900">Enterprise</span>
                                        <span class="fs-13 text-indigo fw-extrabold cf-comp-price-val" data-monthly="$48.00/mo"
                                            data-yearly="$38.40/mo">$48.00/mo</span>
                                    </th>
                                    <!-- Premium 4 -->
                                    <th class="cf-plan-header-cell text-center">
                                        <span class="d-block fw-bold fs-15 text-slate-900">Starter</span>
                                        <span class="fs-13 text-orange fw-extrabold cf-comp-price-val" data-monthly="$75.00/mo"
                                            data-yearly="$60.00/mo">$75.00/mo</span>
                                    </th>
                                    <th class="cf-plan-header-cell text-center">
                                        <span class="d-block fw-bold fs-15 text-slate-900">Business</span>
                                        <span class="fs-13 text-orange fw-extrabold cf-comp-price-val" data-monthly="$120.00/mo"
                                            data-yearly="$96.00/mo">$120.00/mo</span>
                                    </th>
                                    <th class="cf-plan-header-cell text-center">
                                        <span class="d-block fw-bold fs-15 text-slate-900">Pro</span>
                                        <span class="fs-13 text-orange fw-extrabold cf-comp-price-val" data-monthly="$210.00/mo"
                                            data-yearly="$168.00/mo">$210.00/mo</span>
                                    </th>
                                    <th class="cf-plan-header-cell text-center">
                                        <span class="d-block fw-bold fs-15 text-slate-900">Enterprise</span>
                                        <span class="fs-13 text-orange fw-extrabold cf-comp-price-val" data-monthly="$350.00/mo"
                                            data-yearly="$280.00/mo">$350.00/mo</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="cf-cat-header-row">
                                    <td colspan="12"><i class="feather-cpu"></i> Compute &amp; RAM Allocation</td>
                                </tr>
                                <tr>
                                    <th class="cf-sticky-feature-cell">CPU Cores</th>
                                    <td>1 Core</td>
                                    <td>1 Core</td>
                                    <td>1.5 Cores</td>
                                    <td>1 Core</td>
                                    <td class="popular-col">2 Cores</td>
                                    <td>3 Cores</td>
                                    <td>4 Cores</td>
                                    <td>4 vCPU</td>
                                    <td>8 vCPU</td>
                                    <td>12 vCPU</td>
                                    <td>16 vCPU</td>
                                </tr>
                                <tr>
                                    <th class="cf-sticky-feature-cell">RAM</th>
                                    <td>1 GB</td>
                                    <td>1.5 GB</td>
                                    <td>2 GB</td>
                                    <td>2 GB</td>
                                    <td class="popular-col">4 GB</td>
                                    <td>6 GB</td>
                                    <td>8 GB</td>
                                    <td>16 GB</td>
                                    <td>32 GB</td>
                                    <td>64 GB</td>
                                    <td>128 GB</td>
                                </tr>
                                <tr>
                                    <th class="cf-sticky-feature-cell">NVMe Storage</th>
                                    <td>5 GB</td>
                                    <td>10 GB</td>
                                    <td>20 GB</td>
                                    <td>15 GB</td>
                                    <td class="popular-col">35 GB</td>
                                    <td>75 GB</td>
                                    <td>150 GB</td>
                                    <td>100 GB</td>
                                    <td>250 GB</td>
                                    <td>500 GB</td>
                                    <td>1,000 GB</td>
                                </tr>
                                <tr>
                                    <th class="cf-sticky-feature-cell">Websites</th>
                                    <td>1</td>
                                    <td>2</td>
                                    <td>5</td>
                                    <td>1</td>
                                    <td class="popular-col">5</td>
                                    <td>15</td>
                                    <td>Unlimited</td>
                                    <td>Unlimited</td>
                                    <td>Unlimited</td>
                                    <td>Unlimited</td>
                                    <td>Unlimited</td>
                                </tr>
                                <tr>
                                    <th class="cf-sticky-feature-cell">Bandwidth</th>
                                    <td>250 GB</td>
                                    <td>500 GB</td>
                                    <td>Unmetered</td>
                                    <td>Unmetered</td>
                                    <td class="popular-col">Unmetered</td>
                                    <td>Unmetered</td>
                                    <td>Unmetered</td>
                                    <td>10Gbps</td>
                                    <td>10Gbps</td>
                                    <td>10Gbps</td>
                                    <td>10Gbps</td>
                                </tr>
                                <tr>
                                    <th class="cf-sticky-feature-cell">Dedicated IP</th>
                                    <td>&mdash;</td>
                                    <td>&mdash;</td>
                                    <td>&mdash;</td>
                                    <td>$3/mo</td>
                                    <td class="popular-col">$3/mo</td>
                                    <td>$3/mo</td>
                                    <td>1 Free</td>
                                    <td>1 Free</td>
                                    <td>1 Free</td>
                                    <td>2 Free</td>
                                    <td>4 Free</td>
                                </tr>
                                <tr>
                                    <th class="cf-sticky-feature-cell">Backups</th>
                                    <td>Weekly</td>
                                    <td>Weekly</td>
                                    <td>Weekly</td>
                                    <td>Daily</td>
                                    <td class="popular-col">Daily</td>
                                    <td>Daily</td>
                                    <td>2x Daily</td>
                                    <td>2x Daily</td>
                                    <td>2x Daily</td>
                                    <td>2x Daily</td>
                                    <td>Continuous</td>
                                </tr>
                                <tr class="cf-table-action-row">
                                    <th class="cf-sticky-feature-cell bg-light">
                                        <div class="py-1">
                                            <span class="d-block fw-bold text-slate-900 fs-14">Get Started</span>
                                            <small class="text-muted fs-11">Instant Setup</small>
                                        </div>
                                    </th>
                                    <td class="text-center p-2"><a href="https://my.cloudflox.com/cart.php?a=add&pid=101&billingcycle=monthly" data-pid="101" class="cf-table-cta-btn btn-cta-subtle py-1 px-2 fs-12 text-nowrap">Buy Mini</a></td>
                                    <td class="text-center p-2"><a href="https://my.cloudflox.com/cart.php?a=add&pid=102&billingcycle=monthly" data-pid="102" class="cf-table-cta-btn btn-cta-subtle py-1 px-2 fs-12 text-nowrap">Buy Basic</a></td>
                                    <td class="text-center p-2"><a href="https://my.cloudflox.com/cart.php?a=add&pid=103&billingcycle=monthly" data-pid="103" class="cf-table-cta-btn btn-cta-subtle py-1 px-2 fs-12 text-nowrap">Buy Plus</a></td>
                                    <td class="text-center p-2"><a href="https://my.cloudflox.com/cart.php?a=add&pid=201&billingcycle=monthly" data-pid="201" class="cf-table-cta-btn btn-cta-subtle py-1 px-2 fs-12 text-nowrap">Buy Starter</a></td>
                                    <td class="text-center popular-col-business p-2"><a href="https://my.cloudflox.com/cart.php?a=add&pid=202&billingcycle=monthly" data-pid="202" class="cf-table-cta-btn btn-cta-featured py-1 px-2 fs-12 text-nowrap">Buy Business</a></td>
                                    <td class="text-center p-2"><a href="https://my.cloudflox.com/cart.php?a=add&pid=203&billingcycle=monthly" data-pid="203" class="cf-table-cta-btn btn-cta-subtle py-1 px-2 fs-12 text-nowrap">Buy Pro</a></td>
                                    <td class="text-center p-2"><a href="https://my.cloudflox.com/cart.php?a=add&pid=204&billingcycle=monthly" data-pid="204" class="cf-table-cta-btn btn-cta-primary py-1 px-2 fs-12 text-nowrap">Buy Ent</a></td>
                                    <td class="text-center p-2"><a href="https://my.cloudflox.com/cart.php?a=add&pid=301&billingcycle=monthly" data-pid="301" class="cf-table-cta-btn btn-cta-subtle py-1 px-2 fs-12 text-nowrap">Buy Starter</a></td>
                                    <td class="text-center popular-col-premium p-2"><a href="https://my.cloudflox.com/cart.php?a=add&pid=302&billingcycle=monthly" data-pid="302" class="cf-table-cta-btn btn-cta-featured py-1 px-2 fs-12 text-nowrap">Buy Business</a></td>
                                    <td class="text-center p-2"><a href="https://my.cloudflox.com/cart.php?a=add&pid=303&billingcycle=monthly" data-pid="303" class="cf-table-cta-btn btn-cta-subtle py-1 px-2 fs-12 text-nowrap">Buy Pro</a></td>
                                    <td class="text-center p-2"><a href="https://my.cloudflox.com/cart.php?a=add&pid=304&billingcycle=monthly" data-pid="304" class="cf-table-cta-btn btn-cta-primary py-1 px-2 fs-12 text-nowrap">Buy Ent</a></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Custom Solution Banner -->
            <div class="cf-custom-solution-bar">
                <div class="cf-custom-bar-text">
                    <h4 style="color: #ffffff !important;">Need dedicated CPU cores, root VPS access, or custom server
                        clustering?</h4>
                    <p>Explore our KVM Linux/Windows VPS plans (starting from $7.99/mo) or request a tailored
                        infrastructure quote from our engineering team.</p>
                </div>
                <div class="cf-custom-bar-actions">
                    <a href="vps-hosting" class="cf-bar-btn btn-secondary-light">Explore VPS Plans</a>
                    <a href="contact-us" class="cf-bar-btn btn-accent-orange">Request Custom Solution</a>
                </div>
            </div>  

        </div>
    </section>

    <!-- ==========================================
     END: HOSTING PACKAGE COMPARISON TABLE SECTION
     ========================================== -->

    <!-- =========================================================================
     INTERACTIVE HOSTING RECOMMENDATION WIZARD (Customer-Fit Matchmaker)
     Placed Directly Below Hosting Package Comparison Table
     ========================================================================= -->
    <section class="cf-matchmaker-section position-relative" id="hosting-matchmaker">
        <span id="hosting-guide" class="position-absolute" style="top: -85px;"></span>
        <div class="container position-relative z-2">

            <!-- Section Header -->
            <div class="matchmaker-header">
                <span class="matchmaker-badge"><i class="las la-magic"></i> Interactive Plan Matchmaker</span>
                <h2 class="matchmaker-title">
                    Not Sure Which Plan Fits? <span>Let's Match Your Workload</span>
                </h2>
                <p class="matchmaker-subtitle">
                    Answer 3 quick questions about your project scale, visitor volume, and technical preferences. We'll
                    instantly recommend the optimal infrastructure tier and explain exactly why it fits your workload.
                </p>

                <!-- Fast-Track Presets -->
                <div class="matchmaker-preset-bar">
                    <span class="preset-bar-label"><i class="las la-bolt text-warning"></i> Fast-Track Presets:</span>
                    <button type="button" class="preset-pill-btn" onclick="applyMatchmakerPreset('personal')">
                        <i class="las la-user-astronaut"></i> Personal / Portfolio
                    </button>
                    <button type="button" class="preset-pill-btn" onclick="applyMatchmakerPreset('business')">
                        <i class="las la-briefcase"></i> Business / Corporate
                    </button>
                    <button type="button" class="preset-pill-btn" onclick="applyMatchmakerPreset('ecommerce')">
                        <i class="las la-shopping-cart"></i> E-Commerce / Stores
                    </button>
                    <button type="button" class="preset-pill-btn" onclick="applyMatchmakerPreset('vps')">
                        <i class="las la-terminal"></i> Root SSH / Devs
                    </button>
                    <button type="button" class="preset-pill-btn" onclick="applyMatchmakerPreset('dedicated')">
                        <i class="las la-microchip"></i> Dedicated Enterprise
                    </button>
                </div>
            </div>

            <!-- Wizard Card Container -->
            <div class="matchmaker-wizard-card" id="matchmaker-card">

                <!-- Stepper Progress Header -->
                <div class="wizard-stepper-header">
                    <div class="wizard-steps-track">
                        <div class="wizard-progress-line" id="wizard-progress-line" style="width: 0%;"></div>
                        <div class="wizard-step-node active" id="step-node-1" onclick="jumpToMatchmakerStep(1)">
                            <div class="step-node-bubble"><i class="las la-globe"></i></div>
                            <span class="step-node-label">1. Project</span>
                        </div>
                        <div class="wizard-step-node" id="step-node-2" onclick="jumpToMatchmakerStep(2)">
                            <div class="step-node-bubble"><i class="las la-chart-line"></i></div>
                            <span class="step-node-label">2. Traffic</span>
                        </div>
                        <div class="wizard-step-node" id="step-node-3" onclick="jumpToMatchmakerStep(3)">
                            <div class="step-node-bubble"><i class="las la-sliders-h"></i></div>
                            <span class="step-node-label">3. Control</span>
                        </div>
                    </div>
                </div>

                <!-- STEP 1: Project Type -->
                <div class="wizard-step-panel" id="step-panel-1">
                    <div class="wizard-question-box">
                        <span class="question-step-badge">Question 1 of 3</span>
                        <h3 class="question-heading">What type of website or platform are you hosting?</h3>
                        <p class="question-subtext">Select the category that best describes your project's architecture
                            and nature.</p>
                    </div>

                    <div class="wizard-options-grid">
                        <!-- Option 1: Personal -->
                        <div class="wizard-option-card" onclick="selectMatchmakerOption('project', 'personal', this)">
                            <div class="option-card-top">
                                <div class="option-icon-circle"><i class="las la-user-astronaut"></i></div>
                                <span class="option-tag-pill">Starter</span>
                            </div>
                            <h4 class="option-title">Personal Website / Blog / Portfolio</h4>
                            <p class="option-desc">Portfolios, personal blogs, resumes, hobby projects, student
                                showcases.</p>
                        </div>

                        <!-- Option 2: Business -->
                        <div class="wizard-option-card selected"
                            onclick="selectMatchmakerOption('project', 'business', this)">
                            <div class="option-card-top">
                                <div class="option-icon-circle"><i class="las la-briefcase"></i></div>
                                <span class="option-tag-pill">Popular</span>
                            </div>
                            <h4 class="option-title">Growing Business / Corporate Site</h4>
                            <p class="option-desc">SMEs, corporate platforms, agency client sites, lead-gen and
                                marketing portals.</p>
                        </div>

                        <!-- Option 3: E-Commerce -->
                        <div class="wizard-option-card" onclick="selectMatchmakerOption('project', 'ecommerce', this)">
                            <div class="option-card-top">
                                <div class="option-icon-circle"><i class="las la-shopping-cart"></i></div>
                                <span class="option-tag-pill">High Traffic</span>
                            </div>
                            <h4 class="option-title">Demanding Apps / E-Commerce Store</h4>
                            <p class="option-desc">WooCommerce, high-transaction shops, SaaS apps, LMS, and heavy media
                                sites.</p>
                        </div>

                        <!-- Option 4: VPS / Dev -->
                        <div class="wizard-option-card" onclick="selectMatchmakerOption('project', 'vps', this)">
                            <div class="option-card-top">
                                <div class="option-icon-circle"><i class="las la-terminal"></i></div>
                                <span class="option-tag-pill">Developer</span>
                            </div>
                            <h4 class="option-title">Developer Stacks / APIs / Microservices</h4>
                            <p class="option-desc">Node.js, Python, Go, Docker containers, cron daemons, and custom
                                Linux stacks.</p>
                        </div>

                        <!-- Option 5: Dedicated -->
                        <div class="wizard-option-card" onclick="selectMatchmakerOption('project', 'dedicated', this)">
                            <div class="option-card-top">
                                <div class="option-icon-circle"><i class="las la-microchip"></i></div>
                                <span class="option-tag-pill">Enterprise</span>
                            </div>
                            <h4 class="option-title">Mission-Critical Enterprise / Big DB</h4>
                            <p class="option-desc">Massive unshared databases, strict regulatory compliance, private
                                bare-metal.</p>
                        </div>
                    </div>

                    <div class="wizard-footer-nav">
                        <span class="wizard-step-hint"><i class="las la-mouse-pointer"></i> Click an option to continue
                            to Step 2</span>
                        <button type="button" class="btn btn-sm btn-primary px-3 py-2 fw-bold"
                            onclick="goToMatchmakerStep(2)">
                            Next Question <i class="las la-arrow-right"></i>
                        </button>
                    </div>
                </div>

                <!-- STEP 2: Monthly Traffic -->
                <div class="wizard-step-panel" id="step-panel-2" style="display: none;">
                    <div class="wizard-question-box">
                        <span class="question-step-badge">Question 2 of 3</span>
                        <h3 class="question-heading">What is your estimated monthly traffic volume?</h3>
                        <p class="question-subtext">This helps calculate the required memory, CPU thread isolation, and
                            bandwidth capacity.</p>
                    </div>

                    <div class="wizard-options-grid">
                        <!-- Option 1: Starter -->
                        <div class="wizard-option-card" onclick="selectMatchmakerOption('traffic', 'starter', this)">
                            <div class="option-card-top">
                                <div class="option-icon-circle"><i class="las la-seedling"></i></div>
                                <span class="option-tag-pill">&lt; 10k /mo</span>
                            </div>
                            <h4 class="option-title">Starter Traffic (&lt; 10,000 visits)</h4>
                            <p class="option-desc">Baseline traffic with steady, predictable visitors and minimal
                                concurrent load.</p>
                        </div>

                        <!-- Option 2: Moderate -->
                        <div class="wizard-option-card selected"
                            onclick="selectMatchmakerOption('traffic', 'moderate', this)">
                            <div class="option-card-top">
                                <div class="option-icon-circle"><i class="las la-users"></i></div>
                                <span class="option-tag-pill">10k &ndash; 100k</span>
                            </div>
                            <h4 class="option-title">Moderate Scale (10,000 &ndash; 100,000 visits)</h4>
                            <p class="option-desc">Consistent commercial visitors, active daily inquiries, and content
                                consumption.</p>
                        </div>

                        <!-- Option 3: Heavy -->
                        <div class="wizard-option-card" onclick="selectMatchmakerOption('traffic', 'heavy', this)">
                            <div class="option-card-top">
                                <div class="option-icon-circle"><i class="las la-fire"></i></div>
                                <span class="option-tag-pill">100k &ndash; 500k+</span>
                            </div>
                            <h4 class="option-title">High Traffic &amp; Surges (100,000+)</h4>
                            <p class="option-desc">Flash promotions, checkout concurrency, database intensive browsing
                                spikes.</p>
                        </div>

                        <!-- Option 4: API / Dev -->
                        <div class="wizard-option-card"
                            onclick="selectMatchmakerOption('traffic', 'api_traffic', this)">
                            <div class="option-card-top">
                                <div class="option-icon-circle"><i class="las la-network-wired"></i></div>
                                <span class="option-tag-pill">APIs &amp; Cron</span>
                            </div>
                            <h4 class="option-title">Continuous API Calls &amp; Dev Workloads</h4>
                            <p class="option-desc">High-frequency REST/GraphQL requests, background jobs, worker queues.
                            </p>
                        </div>

                        <!-- Option 5: Massive -->
                        <div class="wizard-option-card" onclick="selectMatchmakerOption('traffic', 'massive', this)">
                            <div class="option-card-top">
                                <div class="option-icon-circle"><i class="las la-server"></i></div>
                                <span class="option-tag-pill">Multi-TB</span>
                            </div>
                            <h4 class="option-title">Massive Scale &amp; Unmetered Throughput</h4>
                            <p class="option-desc">Huge global audience, heavy video/media streaming, unmetered gigabit
                                channels.</p>
                        </div>
                    </div>

                    <div class="wizard-footer-nav">
                        <button type="button" class="btn-wizard-back" onclick="goToMatchmakerStep(1)">
                            <i class="las la-arrow-left"></i> Previous Question
                        </button>
                        <button type="button" class="btn btn-sm btn-primary px-3 py-2 fw-bold"
                            onclick="goToMatchmakerStep(3)">
                            Next Question <i class="las la-arrow-right"></i>
                        </button>
                    </div>
                </div>

                <!-- STEP 3: Control Level & Backups -->
                <div class="wizard-step-panel" id="step-panel-3" style="display: none;">
                    <div class="wizard-question-box">
                        <span class="question-step-badge">Question 3 of 3</span>
                        <h3 class="question-heading">What level of control, management &amp; backups do you need?</h3>
                        <p class="question-subtext">Choose between zero-hassle automated managed hosting or full root
                            terminal sovereignty.</p>
                    </div>

                    <div class="wizard-options-grid">
                        <!-- Option 1: cPanel Basic -->
                        <div class="wizard-option-card"
                            onclick="selectMatchmakerOption('control', 'cpanel_basic', this)">
                            <div class="option-card-top">
                                <div class="option-icon-circle"><i class="las la-sliders-h"></i></div>
                                <span class="option-tag-pill">Easy cPanel</span>
                            </div>
                            <h4 class="option-title">Hassle-Free cPanel &amp; 1-Click Installs</h4>
                            <p class="option-desc">Zero server maintenance. Free AutoSSL, Webmail, Softaculous 1-click
                                WordPress.</p>
                        </div>

                        <!-- Option 2: Business Backup -->
                        <div class="wizard-option-card selected"
                            onclick="selectMatchmakerOption('control', 'cpanel_backup', this)">
                            <div class="option-card-top">
                                <div class="option-icon-circle"><i class="las la-shield-alt"></i></div>
                                <span class="option-tag-pill">Daily Backups</span>
                            </div>
                            <h4 class="option-title">Business Managed + Daily JetBackup</h4>
                            <p class="option-desc">Automated 30-day snapshot restore, CloudLinux LVE isolation, priority
                                support.</p>
                        </div>

                        <!-- Option 3: Redis / Isolated -->
                        <div class="wizard-option-card"
                            onclick="selectMatchmakerOption('control', 'cpanel_redis', this)">
                            <div class="option-card-top">
                                <div class="option-icon-circle"><i class="las la-tachometer-alt"></i></div>
                                <span class="option-tag-pill">Isolated + Redis</span>
                            </div>
                            <h4 class="option-title">Dedicated Redis + 15m SysAdmin SLA</h4>
                            <p class="option-desc">In-memory caching for sub-second database queries and proactive
                                server management.</p>
                        </div>

                        <!-- Option 4: Root SSH -->
                        <div class="wizard-option-card" onclick="selectMatchmakerOption('control', 'root_ssh', this)">
                            <div class="option-card-top">
                                <div class="option-icon-circle"><i class="las la-terminal"></i></div>
                                <span class="option-tag-pill">Root Access</span>
                            </div>
                            <h4 class="option-title">100% Root Access &amp; SSH Terminal</h4>
                            <p class="option-desc">Total Linux administrative freedom. Install custom software,
                                firewalls, and OS reloads.</p>
                        </div>

                        <!-- Option 5: Bare-Metal -->
                        <div class="wizard-option-card" onclick="selectMatchmakerOption('control', 'bare_metal', this)">
                            <div class="option-card-top">
                                <div class="option-icon-circle"><i class="las la-hdd"></i></div>
                                <span class="option-tag-pill">100% Private</span>
                            </div>
                            <h4 class="option-title">Bare-Metal Hardware &amp; Hardware RAID</h4>
                            <p class="option-desc">Single-tenant physical server. Zero virtualization overhead and
                                dedicated unshared CPU/RAM.</p>
                        </div>
                    </div>

                    <div class="wizard-footer-nav">
                        <button type="button" class="btn-wizard-back" onclick="goToMatchmakerStep(2)">
                            <i class="las la-arrow-left"></i> Previous Question
                        </button>
                        <button type="button" class="btn btn-sm btn-success px-4 py-2 fw-bold"
                            onclick="calculateAndShowMatch()">
                            Calculate My Match <i class="las la-magic"></i>
                        </button>
                    </div>
                </div>

                <!-- RESULT PANEL: Tailored Recommendation & Why It's Suitable -->
                <div class="matchmaker-result-wrapper" id="matchmaker-result-panel" style="display: none;">

                    <!-- Result Top Banner -->
                    <div class="result-header-banner">
                        <div class="result-banner-left">
                            <div class="result-match-circle">
                                <i class="las la-check-circle"></i>
                            </div>
                            <div>
                                <h3 class="result-banner-title">Here is Your Recommended Hosting Solution</h3>
                                <p class="result-banner-sub">Calculated specifically from your workload profile and
                                    infrastructure requirements.</p>
                            </div>
                        </div>
                        <div class="result-score-badge">
                            <i class="las la-award"></i> 99% Compatibility Match
                        </div>
                    </div>

                    <!-- Selected Answers Quick Summary (Clickable to edit) -->
                    <div class="result-selection-pills">
                        <span class="result-pills-label"><i class="las la-sliders-h"></i> Your Workload Profile:</span>
                        <span class="selection-crumb-badge" onclick="jumpToMatchmakerStep(1)"
                            title="Click to change project type">
                            Project: <strong id="crumb-project-text">Growing Business</strong> <i
                                class="las la-edit"></i>
                        </span>
                        <span class="selection-crumb-badge" onclick="jumpToMatchmakerStep(2)"
                            title="Click to change traffic volume">
                            Traffic: <strong id="crumb-traffic-text">10k &ndash; 100k /mo</strong> <i
                                class="las la-edit"></i>
                        </span>
                        <span class="selection-crumb-badge" onclick="jumpToMatchmakerStep(3)"
                            title="Click to change management control">
                            Control: <strong id="crumb-control-text">Business Managed</strong> <i
                                class="las la-edit"></i>
                        </span>
                    </div>

                    <!-- Two-Column Result Grid -->
                    <div class="result-grid-columns">

                        <!-- Left Column: Matched Plan Card -->
                        <div class="result-plan-card">
                            <div>
                                <span class="result-plan-tag tag-orange" id="res-plan-badge">Businesses &amp;
                                    Corporate</span>
                                <h3 class="result-plan-name" id="res-plan-name">Business Web Hosting</h3>

                                <div class="result-plan-price-row">
                                    <span class="result-plan-price-label">Recommended Starting Tier:</span>
                                    <span class="result-plan-price-val" id="res-plan-price">$7.50</span>
                                    <span class="result-plan-price-cycle">/month</span>
                                </div>

                                <ul class="result-specs-checklist" id="res-specs-list">
                                    <li><i class="las la-check-circle"></i> <span id="res-spec-storage">15 &ndash; 150
                                            GB Enterprise NVMe Gen4</span></li>
                                    <li><i class="las la-check-circle"></i> <span id="res-spec-cpu">1 &ndash; 4 Cores
                                            &amp; 2 &ndash; 8 GB RAM</span></li>
                                    <li><i class="las la-check-circle"></i> <span id="res-spec-backup">Automated Daily
                                            JetBackup (30-day restore)</span></li>
                                    <li><i class="las la-check-circle"></i> <span id="res-spec-panel">Official cPanel +
                                            Priority 24/7 Support</span></li>
                                </ul>
                            </div>

                            <div class="result-plan-actions">
                                <a href="web-hosting" id="res-primary-btn" onclick="handleMatchmakerCta()"
                                    class="btn-result-cta">
                                    <span id="res-btn-label">View Business Plans</span> <i
                                        class="las la-arrow-right"></i>
                                </a>
                                <button type="button" class="btn-result-retake" onclick="restartMatchmaker()">
                                    <i class="las la-redo"></i> Retake Questionnaire
                                </button>
                            </div>
                        </div>

                        <!-- Right Column: Why This Plan is Suitable For You -->
                        <div class="result-why-box">
                            <div class="why-box-title-wrap">
                                <div class="why-title-icon">
                                    <i class="las la-check-double"></i>
                                </div>
                                <div>
                                    <h4 class="why-main-heading">Why This Plan Is Your Ideal Match</h4>
                                    <p class="why-sub-heading">Detailed infrastructure justification based on your exact
                                        answers</p>
                                </div>
                            </div>

                            <div class="why-reasons-list">
                                <!-- Reason 1: Workload Alignment -->
                                <div class="why-reason-item">
                                    <div class="why-item-icon">
                                        <i class="las la-laptop-code"></i>
                                    </div>
                                    <div class="why-item-content">
                                        <div class="why-item-title" id="why-title-1">Workload &amp; Architecture Match
                                        </div>
                                        <p class="why-item-text" id="why-text-1">
                                            Engineered specifically for corporate websites, SMEs, and digital agencies
                                            that require steady uptime, professional webmail, and zero maintenance
                                            headaches.
                                        </p>
                                    </div>
                                </div>

                                <!-- Reason 2: Traffic Scalability -->
                                <div class="why-reason-item">
                                    <div class="why-item-icon">
                                        <i class="las la-tachometer-alt"></i>
                                    </div>
                                    <div class="why-item-content">
                                        <div class="why-item-title" id="why-title-2">Traffic &amp; Concurrency Capacity
                                        </div>
                                        <p class="why-item-text" id="why-text-2">
                                            Easily accommodates your expected 10,000 to 100,000 monthly visitors.
                                            CloudLinux LVE ensures your CPU and RAM allocations remain 100% isolated and
                                            unaffected by neighbors.
                                        </p>
                                    </div>
                                </div>

                                <!-- Reason 3: Management & Security -->
                                <div class="why-reason-item">
                                    <div class="why-item-icon">
                                        <i class="las la-shield-alt"></i>
                                    </div>
                                    <div class="why-item-content">
                                        <div class="why-item-title" id="why-title-3">Management, Backups &amp; Peace of
                                            Mind</div>
                                        <p class="why-item-text" id="why-text-3">
                                            Includes automated off-site daily JetBackup with a 30-day historical
                                            snapshot archive, free AutoSSL certificates, and priority 24/7 technical
                                            assistance.
                                        </p>
                                    </div>
                                </div>

                                <!-- Reason 4: Cost & Value -->
                                <div class="why-reason-item">
                                    <div class="why-item-icon">
                                        <i class="las la-coins"></i>
                                    </div>
                                    <div class="why-item-content">
                                        <div class="why-item-title" id="why-title-4">Optimal Cost-to-Performance Ratio
                                        </div>
                                        <p class="why-item-text" id="why-text-4">
                                            At just $7.50/mo, this provides the perfect commercial balance &mdash;
                                            giving your business high-availability cloud infrastructure without paying
                                            for costly unneeded dedicated server capacity.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- =========================================================================
         3. PARTNERS & TECH ECOSYSTEM
         ========================================================================= -->
    <section class="modern-partners-section" id="techEcosystem">
        <div class="container text-center">
            <!-- Ecosystem Tag Pill -->
            <div class="ecosystem-tag-pill">
                <span class="pulse-dot-blue"></span>
                <span style="color: #ffffff;">Global Infrastructure & Technology Stack</span>
                <span class="ecosystem-pill-sub">Tier-1 Network</span>
            </div>

            <!-- Title & Subtitle -->
            <h2 class="ecosystem-title">
                Technologies We Work With &amp; <span class="text-accent-blue">Global Infrastructure</span>
            </h2>
            <p class="ecosystem-subtitle">
                Built on world-leading cloud datacenters, Tier-1 optical transit networks, and industry-standard
                software stacks to
                guarantee ultra-low latency, 99.9% uptime, and transparent hosting.
            </p>
        </div>

        <!-- Infinite Animated Dual Marquee -->
        <div class="partners-marquee-container" aria-label="Partner & Technology Showcase">
            <!-- Row 1: Software & Control Platforms (Marquee Leftward) -->
            <div class="marquee-track-wrapper">
                <div class="marquee-track marquee-scroll-left">
                    <!-- Partner 1 -->
                    <div class="ecosystem-partner-card">
                        <div class="partner-card-logo">
                            <img src="assets/imgs/providers/cpanel-hosting.webp" alt="cPanel" loading="lazy">
                        </div>
                        <div class="partner-info">
                            <span class="partner-name">cPanel</span>
                            <span class="partner-role">Control Panel</span>
                        </div>
                    </div>
                    <!-- Partner 2 -->
                    <div class="ecosystem-partner-card">
                        <div class="partner-card-logo">
                            <img src="assets/imgs/providers/shared-hosting.webp" alt="CloudLinux OS" loading="lazy">
                        </div>
                        <div class="partner-info">
                            <span class="partner-name">CloudLinux</span>
                            <span class="partner-role">Kernel Isolation</span>
                        </div>
                    </div>
                    <!-- Partner 3 -->
                    <div class="ecosystem-partner-card">
                        <div class="partner-card-logo">
                            <img src="assets/imgs/providers/directadmin.webp" alt="DirectAdmin" loading="lazy">
                        </div>
                        <div class="partner-info">
                            <span class="partner-name">DirectAdmin</span>
                            <span class="partner-role">Lightweight Panel</span>
                        </div>
                    </div>
                    <!-- Partner 4 -->
                    <div class="ecosystem-partner-card">
                        <div class="partner-card-logo">
                            <img src="assets/imgs/providers/wordpress-hosting.webp" alt="WordPress" loading="lazy">
                        </div>
                        <div class="partner-info">
                            <span class="partner-name">WordPress</span>
                            <span class="partner-role">CMS Acceleration</span>
                        </div>
                    </div>
                    <!-- Partner 5 -->
                    <div class="ecosystem-partner-card">
                        <div class="partner-card-logo">
                            <img src="assets/imgs/providers/mysql.webp" alt="MySQL" loading="lazy">
                        </div>
                        <div class="partner-info">
                            <span class="partner-name">MySQL DB</span>
                            <span class="partner-role">Relational Database</span>
                        </div>
                    </div>
                    <!-- Partner 6 -->
                    <div class="ecosystem-partner-card">
                        <div class="partner-card-logo">
                            <img src="assets/imgs/providers/whm-cpanel.webp" alt="WHM" loading="lazy">
                        </div>
                        <div class="partner-info">
                            <span class="partner-name">WHM</span>
                            <span class="partner-role">Server Management</span>
                        </div>
                    </div>

                    <!-- Partner 7 -->
                    <div class="ecosystem-partner-card">
                        <div class="partner-card-logo">
                            <img src="assets/imgs/providers/backblaze-B2-Buckets.webp" alt="backblaze-B2-Buckets"
                                loading="lazy">
                        </div>
                        <div class="partner-info">
                            <span class="partner-name">Backblaze B2 Buckets</span>
                            <span class="partner-role">Cloud Storage</span>
                        </div>
                    </div>

                    <!-- Partner 8 -->
                    <div class="ecosystem-partner-card">
                        <div class="partner-card-logo">
                            <img src="assets/imgs/providers/Imunify360.webp" alt="Imunify360" loading="lazy">
                        </div>
                        <div class="partner-info">
                            <span class="partner-name">Imunify360</span>
                            <span class="partner-role">Server Security</span>
                        </div>
                    </div>

                    <!-- Partner 9 -->
                    <div class="ecosystem-partner-card">
                        <div class="partner-card-logo">
                            <img src="assets/imgs/providers/Jetbackup.webp" alt="Jetbackup" loading="lazy">
                        </div>
                        <div class="partner-info">
                            <span class="partner-name">Jetbackup</span>
                            <span class="partner-role">Backup Management</span>
                        </div>
                    </div>
                </div>

                <!-- Duplicate Track 1 for seamless infinite looping -->
                <div class="marquee-track marquee-scroll-left" aria-hidden="true">
                    <div class="ecosystem-partner-card">
                        <div class="partner-card-logo">
                            <img src="assets/imgs/providers/cpanel-hosting.webp" alt="cPanel" loading="lazy">
                        </div>
                        <div class="partner-info">
                            <span class="partner-name">cPanel</span>
                            <span class="partner-role">Control Panel</span>
                        </div>
                    </div>
                    <div class="ecosystem-partner-card">
                        <div class="partner-card-logo">
                            <img src="assets/imgs/providers/shared-hosting.webp" alt="CloudLinux OS" loading="lazy">
                        </div>
                        <div class="partner-info">
                            <span class="partner-name">CloudLinux</span>
                            <span class="partner-role">Kernel Isolation</span>
                        </div>
                    </div>
                    <div class="ecosystem-partner-card">
                        <div class="partner-card-logo">
                            <img src="assets/imgs/providers/directadmin.webp" alt="DirectAdmin" loading="lazy">
                        </div>
                        <div class="partner-info">
                            <span class="partner-name">DirectAdmin</span>
                            <span class="partner-role">Lightweight Panel</span>
                        </div>
                    </div>
                    <div class="ecosystem-partner-card">
                        <div class="partner-card-logo">
                            <img src="assets/imgs/providers/wordpress-hosting.webp" alt="WordPress" loading="lazy">
                        </div>
                        <div class="partner-info">
                            <span class="partner-name">WordPress</span>
                            <span class="partner-role">CMS Acceleration</span>
                        </div>
                    </div>
                    <div class="ecosystem-partner-card">
                        <div class="partner-card-logo">
                            <img src="assets/imgs/providers/mysql.webp" alt="MySQL" loading="lazy">
                        </div>
                        <div class="partner-info">
                            <span class="partner-name">MySQL DB</span>
                            <span class="partner-role">Relational Database</span>
                        </div>
                    </div>
                    <div class="ecosystem-partner-card">
                        <div class="partner-card-logo">
                            <img src="assets/imgs/providers/whm-cpanel.webp" alt="WHM" loading="lazy">
                        </div>
                        <div class="partner-info">
                            <span class="partner-name">WHM</span>
                            <span class="partner-role">Server Management</span>
                        </div>
                    </div>
                    <div class="ecosystem-partner-card">
                        <div class="partner-card-logo">
                            <img src="assets/imgs/providers/backblaze-B2-Buckets.webp" alt="Backblaze B2 Buckets"
                                loading="lazy">
                        </div>
                        <div class="partner-info">
                            <span class="partner-name">Backblaze B2 Buckets</span>
                            <span class="partner-role">Cloud Storage</span>
                        </div>
                    </div>
                    <div class="ecosystem-partner-card">
                        <div class="partner-card-logo">
                            <img src="assets/imgs/providers/Imunify360.webp" alt="Imunify360" loading="lazy">
                        </div>
                        <div class="partner-info">
                            <span class="partner-name">Imunify360</span>
                            <span class="partner-role">Server Security</span>
                        </div>
                    </div>
                    <div class="ecosystem-partner-card">
                        <div class="partner-card-logo">
                            <img src="assets/imgs/providers/Jetbackup.webp" alt="Jetbackup" loading="lazy">
                        </div>
                        <div class="partner-info">
                            <span class="partner-name">Jetbackup</span>
                            <span class="partner-role">Backup Management</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Row 2: Infrastructure & Tier-1 Transit (Marquee Rightward / Reverse) -->
            <div class="marquee-track-wrapper mt-3">
                <div class="marquee-track marquee-scroll-right">
                    <!-- Partner 7 -->
                    <div class="ecosystem-partner-card">
                        <div class="partner-card-logo">
                            <img src="assets/imgs/providers/ovhcloud.webp" alt="OVHcloud" loading="lazy">
                        </div>
                        <div class="partner-info">
                            <span class="partner-name">OVHcloud</span>
                            <span class="partner-role">Tier-3 Datacenter</span>
                        </div>
                    </div>
                    <!-- Partner 8 -->
                    <div class="ecosystem-partner-card">
                        <div class="partner-card-logo">
                            <img src="assets/imgs/providers/cloudflox-hosting.webp" alt="Hetzner" loading="lazy">
                        </div>
                        <div class="partner-info">
                            <span class="partner-name">Hetzner Online</span>
                            <span class="partner-role">Enterprise Bare-Metal</span>
                        </div>
                    </div>
                    <!-- Partner 9 -->
                    <div class="ecosystem-partner-card">
                        <div class="partner-card-logo">
                            <img src="assets/imgs/providers/cloudflox-hosting-domain.webp" alt="Hivelocity"
                                loading="lazy">
                        </div>
                        <div class="partner-info">
                            <span class="partner-name">Hivelocity</span>
                            <span class="partner-role">High-Capacity DC</span>
                        </div>
                    </div>
                    <!-- Partner 10 -->
                    <div class="ecosystem-partner-card">
                        <div class="partner-card-logo">
                            <img src="assets/imgs/providers/usa-hosting.webp" alt="Tata Communications" loading="lazy">
                        </div>
                        <div class="partner-info">
                            <span class="partner-name">Tata Comm</span>
                            <span class="partner-role">Tier-1 IP Transit</span>
                        </div>
                    </div>
                    <!-- Partner 11 -->
                    <div class="ecosystem-partner-card">
                        <div class="partner-card-logo">
                            <img src="assets/imgs/providers/india-hosting.webp" alt="Telia / Arelion" loading="lazy">
                        </div>
                        <div class="partner-info">
                            <span class="partner-name">Telia / Arelion</span>
                            <span class="partner-role">Global Fiber Backbone</span>
                        </div>
                    </div>
                    <!-- Partner 12 -->
                    <div class="ecosystem-partner-card">
                        <div class="partner-card-logo">
                            <img src="assets/imgs/providers/cloudflox-main.webp" alt="Cogent" loading="lazy">
                        </div>
                        <div class="partner-info">
                            <span class="partner-name">Cogent</span>
                            <span class="partner-role">Tier-1 Optical Network</span>
                        </div>
                    </div>
                </div>

                <!-- Duplicate Track 2 for seamless infinite looping -->
                <div class="marquee-track marquee-scroll-right" aria-hidden="true">
                    <div class="ecosystem-partner-card">
                        <div class="partner-card-logo">
                            <img src="assets/imgs/providers/ovhcloud.webp" alt="OVHcloud" loading="lazy">
                        </div>
                        <div class="partner-info">
                            <span class="partner-name">OVHcloud</span>
                            <span class="partner-role">Tier-3 Datacenter</span>
                        </div>
                    </div>
                    <div class="ecosystem-partner-card">
                        <div class="partner-card-logo">
                            <img src="assets/imgs/providers/cloudflox-hosting.webp" alt="Hetzner" loading="lazy">
                        </div>
                        <div class="partner-info">
                            <span class="partner-name">Hetzner Online</span>
                            <span class="partner-role">Enterprise Bare-Metal</span>
                        </div>
                    </div>
                    <div class="ecosystem-partner-card">
                        <div class="partner-card-logo">
                            <img src="assets/imgs/providers/cloudflox-hosting-domain.webp" alt="Hivelocity"
                                loading="lazy">
                        </div>
                        <div class="partner-info">
                            <span class="partner-name">Hivelocity</span>
                            <span class="partner-role">High-Capacity DC</span>
                        </div>
                    </div>
                    <div class="ecosystem-partner-card">
                        <div class="partner-card-logo">
                            <img src="assets/imgs/providers/usa-hosting.webp" alt="Tata Communications" loading="lazy">
                        </div>
                        <div class="partner-info">
                            <span class="partner-name">Tata Comm</span>
                            <span class="partner-role">Tier-1 IP Transit</span>
                        </div>
                    </div>
                    <div class="ecosystem-partner-card">
                        <div class="partner-card-logo">
                            <img src="assets/imgs/providers/india-hosting.webp" alt="Telia / Arelion" loading="lazy">
                        </div>
                        <div class="partner-info">
                            <span class="partner-name">Telia / Arelion</span>
                            <span class="partner-role">Global Fiber Backbone</span>
                        </div>
                    </div>
                    <div class="ecosystem-partner-card">
                        <div class="partner-card-logo">
                            <img src="assets/imgs/providers/cloudflox-main.webp" alt="Cogent" loading="lazy">
                        </div>
                        <div class="partner-info">
                            <span class="partner-name">Cogent</span>
                            <span class="partner-role">Tier-1 Optical Network</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Ecosystem Highlights Bar -->
        <div class="container mt-4">
            <div class="ecosystem-trust-bar">
                <div class="eco-trust-item">
                    <div class="eco-trust-icon-box text-primary">
                        <i class="las la-globe-americas"></i>
                    </div>
                    <div class="eco-trust-text">
                        <strong>Tier-1 Global Transit</strong>
                        <small>Direct Tata, Telia & Cogent Uplinks</small>
                    </div>
                </div>
                <div class="eco-trust-item">
                    <div class="eco-trust-icon-box text-warning">
                        <i class="las la-microchip"></i>
                    </div>
                    <div class="eco-trust-text">
                        <strong>Enterprise Hardware</strong>
                        <small>Intel Xeon & AMD EPYC NVMe Gen4</small>
                    </div>
                </div>
                <div class="eco-trust-item">
                    <div class="eco-trust-icon-box text-success">
                        <i class="las la-shield-alt"></i>
                    </div>
                    <div class="eco-trust-text">
                        <strong>Tbps Anti-DDoS Shield</strong>
                        <small>Always-On Imunify360 & Hardware Firewalls</small>
                    </div>
                </div>
                <div class="eco-trust-item">
                    <div class="eco-trust-icon-box text-info">
                        <i class="las la-clock"></i>
                    </div>
                    <div class="eco-trust-text">
                        <strong>99.9% Uptime Guarantee</strong>
                        <small>Financially-Backed Enterprise SLA</small>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- =========================================================================
         4. WHY CHOOSE CLOUDFLOX: ARCHITECTURE & ACTUAL FEATURES (SEO ENHANCED)
         ========================================================================= -->
    <section class="modern-why-section" id="why-cloudflox">
        <div class="container">
            <div class="why-header-box text-center">
                <span class="why-badge-pill" data-sal="slide-down"><i class="las la-award"></i> The CloudFlox
                    Advantage</span>
                <h2 class="why-main-title" data-sal="slide-up">
                    Engineered for Speed, Isolation &amp; <span>Proven Reliability</span>
                </h2>
                <p class="why-subtitle" data-sal="slide-up">
                    We present the actual enterprise hardware, CloudLinux kernel resource isolation, official cPanel
                    management,
                    and proactive AI defenses powering your websites. No unverified partnerships or hidden throttles.
                </p>
            </div>

            <!-- Bento Grid: The 5 Core Pillars (Actual Features Only) -->
            <div class="row g-4">

                <!-- Bento 1: NVMe Enterprise Storage -->
                <div class="col-lg-7" data-sal="slide-up" data-sal-duration="500">
                    <div class="bento-card-redesign card-nvme">
                        <div class="bento-card-top">
                            <div class="bento-header-row">
                                <div class="bento-icon-wrapper icon-orange">
                                    <i class="las la-tachometer-alt"></i>
                                </div>
                                <span class="bento-status-pill pill-orange-soft">14x Faster I/O</span>
                            </div>
                            <h3 class="bento-seo-title">Ultra PCIe Gen4 NVMe Enterprise Storage</h3>
                            <p class="bento-seo-desc">
                                Equipped with pure PCIe 4.0 NVMe solid state drives delivering sustained read/write
                                speeds over <strong>7,000 MB/s</strong>.
                                Instant database queries, lightning-fast WordPress execution, and maximum scores on
                                Google Core Web Vitals (LCP &amp; INP).
                            </p>

                            <!-- Speed Benchmark Visual -->
                            <div class="speed-meter-box">
                                <div class="speed-bar-row">
                                    <div class="speed-bar-label">
                                        <span>CloudFlox Enterprise Gen4 NVMe</span>
                                        <strong class="speed-highlight" style="color: #EA580C;">~7,000 MB/s</strong>
                                    </div>
                                    <div class="speed-progress-track">
                                        <div class="speed-progress-fill fill-nvme" style="width: 100%;"></div>
                                    </div>
                                </div>
                                <div class="speed-bar-row">
                                    <div class="speed-bar-label">
                                        <span>Standard SATA SSD Hosting</span>
                                        <span class="text-muted">~500 MB/s (14x Slower)</span>
                                    </div>
                                    <div class="speed-progress-track">
                                        <div class="speed-progress-fill fill-standard" style="width: 12%;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <ul class="bento-feature-checklist">
                            <li><i class="las la-check-circle"></i> <span><strong>Hardware RAID-10 Redundancy:</strong>
                                    Continuous zero-loss disk mirroring and fail-safe parity.</span></li>
                            <li><i class="las la-check-circle"></i> <span><strong>Low Latency MySQL I/O:</strong>
                                    Eliminates disk queue delays during high-concurrency checkout traffic.</span></li>
                        </ul>
                    </div>
                </div>

                <!-- Bento 2: CloudLinux LVE Limits & Isolation -->
                <div class="col-lg-5" data-sal="slide-up" data-sal-duration="500" data-sal-delay="100">
                    <div class="bento-card-redesign card-cloudlinux">
                        <div class="bento-card-top">
                            <div class="bento-header-row">
                                <div class="bento-icon-wrapper icon-blue">
                                    <i class="las la-shield-alt"></i>
                                </div>
                                <span class="bento-status-pill pill-blue-soft">Zero Noisy Neighbors</span>
                            </div>
                            <h3 class="bento-seo-title">CloudLinux Kernel Isolation &amp; Dedicated Resources</h3>
                            <p class="bento-seo-desc">
                                Every shared cPanel hosting account runs within a dedicated Lightweight Virtualized
                                Environment (LVE).
                                Fixed CPU, physical RAM, and IOPS allocations guarantee that other accounts never steal
                                your server power.
                            </p>

                            <!-- LVE Resource Grid -->
                            <div class="lve-resource-grid">
                                <div class="lve-item">
                                    <span>Dedicated CPU</span>
                                    <strong>100% &ndash; 400% LVE</strong>
                                </div>
                                <div class="lve-item">
                                    <span>Physical RAM</span>
                                    <strong>1 GB &ndash; 8 GB DDR5</strong>
                                </div>
                                <div class="lve-item">
                                    <span>Disk IOPS</span>
                                    <strong>Up to 10,240 IOPS</strong>
                                </div>
                                <div class="lve-item">
                                    <span>Entry Processes</span>
                                    <strong>Up to 120 Workers</strong>
                                </div>
                            </div>
                        </div>

                        <ul class="bento-feature-checklist">
                            <li><i class="las la-check-circle"></i> <span><strong>CageFS Sandboxing:</strong>
                                    Virtualized file system prevents cross-tenant script snooping.</span></li>
                            <li><i class="las la-check-circle"></i> <span><strong>MySQL Governor:</strong> Automatically
                                    throttles runaway database spikes in real time.</span></li>
                        </ul>
                    </div>
                </div>

                <!-- Bento 3: Official cPanel & WHM Control -->
                <div class="col-lg-4" data-sal="slide-up" data-sal-duration="500" data-sal-delay="150">
                    <div class="bento-card-redesign card-cpanel">
                        <div class="bento-card-top">
                            <div class="bento-header-row">
                                <div class="bento-icon-wrapper icon-orange">
                                    <i class="las la-layer-group"></i>
                                </div>
                                <span class="bento-status-pill pill-orange-soft">Industry Standard</span>
                            </div>
                            <h3 class="bento-seo-title">Official cPanel &amp; WHM Control Interface</h3>
                            <p class="bento-seo-desc">
                                The world’s leading web hosting control panel. Enjoy full graphical management over DNS
                                zones, domains, email accounts, and MariaDB databases without complex terminal commands.
                            </p>
                        </div>

                        <ul class="bento-feature-checklist">
                            <li><i class="las la-check-circle"></i> <span><strong>1-Click Softaculous Apps:</strong>
                                    Deploy WordPress, Laravel, or WooCommerce in seconds.</span></li>
                            <li><i class="las la-check-circle"></i> <span><strong>Multi-PHP Selector:</strong>
                                    Seamlessly toggle between PHP 7.4, 8.1, 8.2 &amp; 8.3+.</span></li>
                            <li><i class="las la-check-circle"></i> <span><strong>WHM Management:</strong> Full
                                    multi-account server management on managed nodes.</span></li>
                        </ul>
                    </div>
                </div>

                <!-- Bento 4: Multi-Layer Security & Imunify360 -->
                <div class="col-lg-4" data-sal="slide-up" data-sal-duration="500" data-sal-delay="200">
                    <div class="bento-card-redesign card-security">
                        <div class="bento-card-top">
                            <div class="bento-header-row">
                                <div class="bento-icon-wrapper icon-green">
                                    <i class="las la-lock"></i>
                                </div>
                                <span class="bento-status-pill pill-green-soft">Proactive AI WAF</span>
                            </div>
                            <h3 class="bento-seo-title">Multi-Layer Enterprise Security &amp; Free DDoS Defense</h3>
                            <p class="bento-seo-desc">
                                Enterprise cybersecurity active 24/7. Automated machine-learning Web Application
                                Firewall (WAF) inspects incoming requests, filters malicious botnets, and neutralizes
                                exploits.
                            </p>
                        </div>

                        <ul class="bento-feature-checklist">
                            <li><i class="las la-check-circle"></i> <span><strong>Automated Real-Time Cleanup:</strong>
                                    Neutralizes infected files the instant they are uploaded.</span></li>
                            <li><i class="las la-check-circle"></i> <span><strong>Free AutoSSL (Let's Encrypt):</strong>
                                    Instant automated HTTPS encryption on all domains.</span></li>
                            <li><i class="las la-check-circle"></i> <span><strong>Edge DDoS Mitigation:</strong> Absorbs
                                    high-volume volumetric attacks before reaching your site.</span></li>
                        </ul>
                    </div>
                </div>

                <!-- Bento 5: 24/7/365 Human Cloud Support -->
                <div class="col-lg-4" data-sal="slide-up" data-sal-duration="500" data-sal-delay="250">
                    <div class="bento-card-redesign card-support">
                        <div class="bento-card-top">
                            <div class="bento-header-row">
                                <div class="bento-icon-wrapper icon-purple">
                                    <i class="las la-headset"></i>
                                </div>
                                <span class="bento-status-pill pill-purple-soft">&lt; 15m Response SLA</span>
                            </div>
                            <h3 class="bento-seo-title">24/7/365 Expert Human Support &amp; SysAdmin Care</h3>
                            <p class="bento-seo-desc">
                                Zero automated robot runarounds. Real Linux sysadmins and certified hosting engineers
                                ready to troubleshoot code, database queries, and server configurations immediately.
                            </p>
                        </div>

                        <ul class="bento-feature-checklist">
                            <li><i class="las la-check-circle"></i> <span><strong>Free Zero-Downtime Migration:</strong>
                                    Complete white-glove site, database, and email transfer.</span></li>
                            <li><i class="las la-check-circle"></i> <span><strong>Multi-Channel Assistance:</strong>
                                    Fast help via ticket, live chat, and support portal.</span></li>
                            <li><i class="las la-check-circle"></i> <span><strong>99.9% Uptime SLA:</strong> Backed by
                                    enforceable uptime service level commitments.</span></li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </section>



    <!-- =========================================================================
         6. OPERATING SYSTEMS & CONTROL PANELS MATRIX (Modern Redesign)
         ========================================================================= -->
    <section class="modern-tech-section" id="osPanelsSection">
        <div class="container">
            <div class="tech-matrix-island" data-sal="slide-up" data-sal-duration="600">
                <!-- Header -->
                <div class="text-center mb-4">
                    <div class="ecosystem-tag-pill">
                        <span class="pulse-dot-blue"></span>
                        <span>Total Freedom &amp; Zero Lock-in</span>
                        <span class="ecosystem-pill-sub">1-Click Automated</span>
                    </div>
                    <h2 class="tech-matrix-title">
                        Supported <span class="text-accent-blue">Operating Systems</span> &amp; Control Panels
                    </h2>
                    <p class="tech-matrix-subtitle">
                        Deploy your cloud instance with your preferred Linux distribution, Windows Server, or turnkey
                        management panel in seconds.
                    </p>

                    <!-- Segmented Tab Switcher -->
                    <ul class="nav modern-tech-tabs" id="techTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="os-tab" data-bs-toggle="tab"
                                data-bs-target="#os-content" type="button" role="tab" aria-controls="os-content"
                                aria-selected="true">
                                <i class="las la-server"></i>
                                <span>Operating Systems</span>
                                <span class="nav-tab-badge">8 OS Options</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="panel-tab" data-bs-toggle="tab" data-bs-target="#panel-content"
                                type="button" role="tab" aria-controls="panel-content" aria-selected="false">
                                <i class="las la-sliders-h"></i>
                                <span>Panels &amp; DevOps</span>
                                <span class="nav-tab-badge">6 Stacks</span>
                            </button>
                        </li>
                    </ul>
                </div>

                <!-- Tab Content -->
                <div class="tab-content" id="techTabContent">

                    <!-- Tab 1: Operating Systems (4x2 Grid) -->
                    <div class="tab-pane fade show active" id="os-content" role="tabpanel" aria-labelledby="os-tab">
                        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-3">
                            <!-- OS 1: Ubuntu -->
                            <div class="col">
                                <div class="tech-matrix-card">
                                    <div class="tech-card-logo-box">
                                        <img src="assets/imgs/icons/ubuntu.svg" alt="Ubuntu Linux" loading="lazy">
                                    </div>
                                    <div class="tech-card-details">
                                        <div class="tech-card-top-row">
                                            <span class="tech-card-name">Ubuntu Linux</span>
                                            <span class="tech-card-tag">Popular</span>
                                        </div>
                                        <span class="tech-card-version">24.04 &amp; 22.04 LTS</span>
                                        <span class="tech-card-feature"><i class="las la-check text-success"></i> Kernel
                                            6.8+ • Daily Patches</span>
                                    </div>
                                </div>
                            </div>

                            <!-- OS 2: Debian -->
                            <div class="col">
                                <div class="tech-matrix-card">
                                    <div class="tech-card-logo-box">
                                        <img src="assets/imgs/icons/debian.svg" alt="Debian Linux" loading="lazy">
                                    </div>
                                    <div class="tech-card-details">
                                        <div class="tech-card-top-row">
                                            <span class="tech-card-name">Debian</span>
                                            <span class="tech-card-tag">Stable</span>
                                        </div>
                                        <span class="tech-card-version">12 Bookworm / 11</span>
                                        <span class="tech-card-feature"><i class="las la-check text-success"></i>
                                            Rock-Solid Reliability</span>
                                    </div>
                                </div>
                            </div>

                            <!-- OS 3: AlmaLinux -->
                            <div class="col">
                                <div class="tech-matrix-card">
                                    <div class="tech-card-logo-box">
                                        <img src="assets/imgs/icons/almalinux.svg" alt="AlmaLinux" loading="lazy">
                                    </div>
                                    <div class="tech-card-details">
                                        <div class="tech-card-top-row">
                                            <span class="tech-card-name">AlmaLinux</span>
                                            <span class="tech-card-tag">RHEL 1:1</span>
                                        </div>
                                        <span class="tech-card-version">9.x &amp; 8.x Enterprise</span>
                                        <span class="tech-card-feature"><i class="las la-check text-success"></i> 100%
                                            Binary Compatible</span>
                                    </div>
                                </div>
                            </div>

                            <!-- OS 4: Rocky Linux -->
                            <div class="col">
                                <div class="tech-matrix-card">
                                    <div class="tech-card-logo-box">
                                        <img src="assets/imgs/icons/rockylinux.svg" alt="Rocky Linux" loading="lazy">
                                    </div>
                                    <div class="tech-card-details">
                                        <div class="tech-card-top-row">
                                            <span class="tech-card-name">Rocky Linux</span>
                                            <span class="tech-card-tag">Enterprise</span>
                                        </div>
                                        <span class="tech-card-version">9.x Cloud Ready</span>
                                        <span class="tech-card-feature"><i class="las la-check text-success"></i>
                                            Community Enterprise</span>
                                    </div>
                                </div>
                            </div>

                            <!-- OS 5: Windows Server -->
                            <div class="col">
                                <div class="tech-matrix-card">
                                    <div class="tech-card-logo-box">
                                        <img src="assets/imgs/windows.webp" alt="Windows Server" loading="lazy">
                                    </div>
                                    <div class="tech-card-details">
                                        <div class="tech-card-top-row">
                                            <span class="tech-card-name">Windows Server</span>
                                            <span class="tech-card-tag">Standard</span>
                                        </div>
                                        <span class="tech-card-version">2025 / 2022 / 2019</span>
                                        <span class="tech-card-feature"><i class="las la-check text-success"></i> Full
                                            RDP &amp; Admin Rights</span>
                                    </div>
                                </div>
                            </div>

                            <!-- OS 6: openSUSE -->
                            <div class="col">
                                <div class="tech-matrix-card">
                                    <div class="tech-card-logo-box">
                                        <img src="assets/imgs/icons/opensuse.svg" alt="openSUSE" loading="lazy">
                                    </div>
                                    <div class="tech-card-details">
                                        <div class="tech-card-top-row">
                                            <span class="tech-card-name">openSUSE</span>
                                            <span class="tech-card-tag">DevOps</span>
                                        </div>
                                        <span class="tech-card-version">Leap 15.6 / Tumbleweed</span>
                                        <span class="tech-card-feature"><i class="las la-check text-success"></i> YaST
                                            System Configurator</span>
                                    </div>
                                </div>
                            </div>

                            <!-- OS 7: FreeBSD -->
                            <div class="col">
                                <div class="tech-matrix-card">
                                    <div class="tech-card-logo-box">
                                        <img src="assets/imgs/icons/freebsd.svg" alt="FreeBSD" loading="lazy">
                                    </div>
                                    <div class="tech-card-details">
                                        <div class="tech-card-top-row">
                                            <span class="tech-card-name">FreeBSD</span>
                                            <span class="tech-card-tag">BSD Unix</span>
                                        </div>
                                        <span class="tech-card-version">14.1 &amp; 13.3 Release</span>
                                        <span class="tech-card-feature"><i class="las la-check text-success"></i>
                                            High-Speed ZFS Storage</span>
                                    </div>
                                </div>
                            </div>

                            <!-- OS 8: Custom ISO -->
                            <div class="col">
                                <div class="tech-matrix-card tech-card-custom-iso">
                                    <div class="tech-card-logo-box icon-box-iso">
                                        <i class="las la-compact-disc"></i>
                                    </div>
                                    <div class="tech-card-details">
                                        <div class="tech-card-top-row">
                                            <span class="tech-card-name">Custom ISO</span>
                                            <span class="tech-card-tag text-primary">BYO ISO</span>
                                        </div>
                                        <span class="tech-card-version">Upload Any x86_64 Image</span>
                                        <span class="tech-card-feature"><i class="las la-check text-success"></i>
                                            Instant Mount via Console</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 2: Panels & DevOps (3x2 Grid) -->
                    <div class="tab-pane fade" id="panel-content" role="tabpanel" aria-labelledby="panel-tab">
                        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-3">
                            <!-- Panel 1: cPanel -->
                            <div class="col">
                                <div class="tech-matrix-card">
                                    <div class="tech-card-logo-box">
                                        <img src="assets/imgs/icons/cpanel.svg" alt="cPanel &amp; WHM" loading="lazy">
                                    </div>
                                    <div class="tech-card-details">
                                        <div class="tech-card-top-row">
                                            <span class="tech-card-name">cPanel &amp; WHM</span>
                                            <span class="tech-card-tag">Official Partner</span>
                                        </div>
                                        <span class="tech-card-version">1-Click Auto-Provisioning</span>
                                        <span class="tech-card-feature"><i class="las la-check text-success"></i> Full
                                            Multi-Domain Management</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Panel 2: Plesk -->
                            <div class="col">
                                <div class="tech-matrix-card">
                                    <div class="tech-card-logo-box">
                                        <img src="assets/imgs/icons/plesk.svg" alt="Plesk" loading="lazy">
                                    </div>
                                    <div class="tech-card-details">
                                        <div class="tech-card-top-row">
                                            <span class="tech-card-name">Plesk Obsidian</span>
                                            <span class="tech-card-tag">WP Toolkit</span>
                                        </div>
                                        <span class="tech-card-version">Linux &amp; Windows Ready</span>
                                        <span class="tech-card-feature"><i class="las la-check text-success"></i>
                                            Automated Staging &amp; Git Sync</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Panel 3: Docker -->
                            <div class="col">
                                <div class="tech-matrix-card">
                                    <div class="tech-card-logo-box">
                                        <img src="assets/imgs/icons/docker.svg" alt="Docker Engine" loading="lazy">
                                    </div>
                                    <div class="tech-card-details">
                                        <div class="tech-card-top-row">
                                            <span class="tech-card-name">Docker &amp; Compose</span>
                                            <span class="tech-card-tag">DevOps</span>
                                        </div>
                                        <span class="tech-card-version">Containerization Stack</span>
                                        <span class="tech-card-feature"><i class="las la-check text-success"></i>
                                            Microservices Ready Out of Box</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Panel 4: DirectAdmin -->
                            <div class="col">
                                <div class="tech-matrix-card">
                                    <div class="tech-card-logo-box">
                                        <img src="assets/imgs/providers/directadmin.webp" alt="DirectAdmin"
                                            loading="lazy">
                                    </div>
                                    <div class="tech-card-details">
                                        <div class="tech-card-top-row">
                                            <span class="tech-card-name">DirectAdmin</span>
                                            <span class="tech-card-tag">Lightweight</span>
                                        </div>
                                        <span class="tech-card-version">Low Resource Footprint</span>
                                        <span class="tech-card-feature"><i class="las la-check text-success"></i>
                                            Blazing Fast Server Admin</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Panel 5: Webmin -->
                            <div class="col">
                                <div class="tech-matrix-card">
                                    <div class="tech-card-logo-box">
                                        <img src="assets/imgs/icons/webmin.svg" alt="Webmin / Virtualmin" loading="lazy">
                                    </div>
                                    <div class="tech-card-details">
                                        <div class="tech-card-top-row">
                                            <span class="tech-card-name">Webmin / Virtualmin</span>
                                            <span class="tech-card-tag">Open Source</span>
                                        </div>
                                        <span class="tech-card-version">Free Web SysAdmin Console</span>
                                        <span class="tech-card-feature"><i class="las la-check text-success"></i> Zero
                                            License Costs</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Panel 6: Proxmox -->
                            <div class="col">
                                <div class="tech-matrix-card">
                                    <div class="tech-card-logo-box">
                                        <img src="assets/imgs/icons/proxmox.svg" alt="Proxmox VE" loading="lazy">
                                    </div>
                                    <div class="tech-card-details">
                                        <div class="tech-card-top-row">
                                            <span class="tech-card-name">Proxmox VE</span>
                                            <span class="tech-card-tag">Hypervisor</span>
                                        </div>
                                        <span class="tech-card-version">Bare-Metal Virtualization</span>
                                        <span class="tech-card-feature"><i class="las la-check text-success"></i> KVM
                                            &amp; LXC Cluster Support</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Bottom Telemetry / Capability Highlights Strip -->
                <div class="tech-matrix-features-strip">
                    <div class="tech-strip-item">
                        <div class="tech-strip-icon">
                            <i class="las la-history"></i>
                        </div>
                        <div class="tech-strip-content">
                            <strong>1-Click Instant Reinstall</strong>
                            <small>Switch distributions anytime in under 60 seconds with automated root credential
                                delivery.</small>
                        </div>
                    </div>
                    <div class="tech-strip-item">
                        <div class="tech-strip-icon">
                            <i class="las la-terminal"></i>
                        </div>
                        <div class="tech-strip-content">
                            <strong>HTML5 Out-of-Band VNC</strong>
                            <small>Emergency rescue console accessible directly from browser if SSH configuration
                                breaks.</small>
                        </div>
                    </div>
                    <div class="tech-strip-item">
                        <div class="tech-strip-icon">
                            <i class="las la-key"></i>
                        </div>
                        <div class="tech-strip-content">
                            <strong>Automated SSH Key Injection</strong>
                            <small>Deploy with pre-hardened public keys and customized cloud-init automation
                                scripts.</small>
                        </div>
                    </div>
                    <div class="tech-strip-item">
                        <div class="tech-strip-icon">
                            <i class="las la-shield-alt"></i>
                        </div>
                        <div class="tech-strip-content">
                            <strong>Kernel-Level Security</strong>
                            <small>Hardware virtualization isolation with always-on Tbps Anti-DDoS network
                                filtering.</small>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>



    <!-- =========================================================================
         8. CUSTOMER REVIEWS & SOCIAL PROOF (Reference-Matched Layout)
         ========================================================================= -->
    <section class="modern-reviews-section" id="customerReviews">
        <div class="container">
            <!-- Section Header -->
            <div class="text-center mb-5">
                <div class="review-endorsement-pill" data-sal="slide-down" data-sal-duration="500">
                    <i class="las la-award"></i>
                    <span>Client Endorsements</span>
                </div>
                <h2 class="review-section-title" data-sal="slide-up" data-sal-duration="600">
                    Proven Results. <span class="text-highlight-gold">Real Impact.</span>
                </h2>
                <p class="review-section-subtitle" data-sal="slide-up" data-sal-duration="600" data-sal-delay="100">
                    Real feedback from CTOs, SaaS founders, and dev leads who transformed their cloud operations with
                    CloudFlox.
                </p>
            </div>

            <!-- Main Showcase Grid (Visual Showcase Left + Slider Card Right) -->
            <div class="row g-4 align-items-stretch">

                <!-- Left Column: Impact Visual Card with Floating Stats Island -->
                <div class="col-lg-5 col-xl-5" data-sal="slide-up" data-sal-duration="600">
                    <div class="review-visual-card">
                        <!-- Parallax Background Image Wrapper -->
                        <div class="review-visual-bg-wrapper">
                            <img src="assets/imgs/testimonials-team.webp" alt="CloudFlox Infrastructure Team"
                                class="review-parallax-img" id="reviewParallaxImg" width="600" height="804" loading="lazy">
                        </div>

                        <!-- Top Tag on Image -->
                        <div class="visual-top-tag">
                            <i class="las la-shield-alt"></i>
                            <span>Mission-Critical Infrastructure</span>
                        </div>

                        <!-- Bottom Content (Headline + Floating Stats Box) -->
                        <div class="visual-content-bottom">
                            <h3 class="visual-headline">
                                Empowering Global Platforms <br><span>Into Market Leaders</span>
                            </h3>

                            <!-- Floating Glassmorphic Stats Island -->
                            <div class="visual-stats-island">
                                <div class="visual-rating-row">
                                    <div class="rating-huge-wrap">
                                        <span class="rating-huge">4.9</span>
                                        <span class="rating-max">/ 5.0</span>
                                    </div>
                                    <div class="rating-stars-col">
                                        <div class="rating-stars">
                                            <i class="las la-star"></i><i class="las la-star"></i><i
                                                class="las la-star"></i><i class="las la-star"></i><i
                                                class="las la-star"></i>
                                        </div>
                                        <div class="rating-label">Over 98% Client Satisfaction Rate</div>
                                    </div>
                                </div>

                                <div class="visual-divider"></div>

                                <div class="visual-metrics-grid">
                                    <div class="metric-item">
                                        <strong>99.99%</strong>
                                        <span>Uptime SLA</span>
                                    </div>
                                    <div class="metric-item">
                                        <strong>10,000+</strong>
                                        <span>Hosted Sites</span>
                                    </div>
                                    <div class="metric-item">
                                        <strong>15 Min</strong>
                                        <span>Avg. Response</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Interactive Testimonial Slider Card -->
                <div class="col-lg-7 col-xl-7" data-sal="slide-up" data-sal-duration="600" data-sal-delay="100">
                    <div class="review-slider-col">
                        <!-- Swiper Container -->
                        <div class="swiper reviewSwiper">
                            <div class="swiper-wrapper">

                                <!-- Slide 1: eCommerce & Retail -->
                                <div class="swiper-slide">
                                    <div class="review-feature-card">
                                        <div>
                                            <div class="review-card-header-row">
                                                <div class="review-industry-pill">
                                                    <i class="las la-shopping-cart"></i>
                                                    <span>FMCG &amp; Retail eCommerce</span>
                                                </div>
                                                <div class="review-card-stars">
                                                    <i class="las la-star"></i><i class="las la-star"></i><i
                                                        class="las la-star"></i><i class="las la-star"></i><i
                                                        class="las la-star"></i>
                                                </div>
                                            </div>

                                            <div class="review-quote-mark">
                                                <i class="las la-quote-left"></i>
                                            </div>

                                            <p class="review-quote-text">
                                                &ldquo;The NVMe storage speed on CloudFlox made an immediate difference.
                                                Our WooCommerce site load times dropped from 3.2s down to 420ms after
                                                migrating from our previous host. Their automated cPanel migration was
                                                completely seamless without a minute of downtime during our peak sales
                                                promotion.&rdquo;
                                            </p>

                                            <div class="review-impact-strip">
                                                <div class="impact-icon"><i class="las la-chart-line"></i></div>
                                                <div class="impact-text">
                                                    <strong>Key Impact:</strong> <span>Page Load Dropped to 420ms &amp;
                                                        100% Black Friday Uptime</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="review-author-row">
                                            <div class="review-author-profile">
                                                <div class="author-avatar-badge">
                                                    <span>MR</span>
                                                </div>
                                                <div>
                                                    <h4 class="author-name">Marcus Reynolds</h4>
                                                    <p class="author-meta">Head of Digital Infrastructure &bull; Apex
                                                        Retail Tech</p>
                                                </div>
                                            </div>
                                            <div class="verified-client-pill">
                                                <i class="las la-check-circle"></i>
                                                <span>Verified Client</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Slide 2: SaaS & High-Concurrency VPS -->
                                <div class="swiper-slide">
                                    <div class="review-feature-card">
                                        <div>
                                            <div class="review-card-header-row">
                                                <div class="review-industry-pill">
                                                    <i class="las la-server"></i>
                                                    <span>SaaS &amp; FinTech Platforms</span>
                                                </div>
                                                <div class="review-card-stars">
                                                    <i class="las la-star"></i><i class="las la-star"></i><i
                                                        class="las la-star"></i><i class="las la-star"></i><i
                                                        class="las la-star"></i>
                                                </div>
                                            </div>

                                            <div class="review-quote-mark">
                                                <i class="las la-quote-left"></i>
                                            </div>

                                            <p class="review-quote-text">
                                                &ldquo;We run 16 high-concurrency client VPS instances on CloudFlox
                                                across their US and Frankfurt datacenters. Uptime has been a rock-solid
                                                100% over the last 11 months. Whenever we have a complex routing or
                                                firewall query, their senior Linux team answers on live chat in under 4
                                                minutes.&rdquo;
                                            </p>

                                            <div class="review-impact-strip">
                                                <div class="impact-icon"><i class="las la-chart-line"></i></div>
                                                <div class="impact-text">
                                                    <strong>Key Impact:</strong> <span>100% Measured Uptime Across 16
                                                        Production VPS Clusters</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="review-author-row">
                                            <div class="review-author-profile">
                                                <div class="author-avatar-badge">
                                                    <span>SL</span>
                                                </div>
                                                <div>
                                                    <h4 class="author-name">Sanjay Lakshman</h4>
                                                    <p class="author-meta">Lead DevOps Engineer &bull; CloudLab
                                                        Analytics</p>
                                                </div>
                                            </div>
                                            <div class="verified-client-pill">
                                                <i class="las la-check-circle"></i>
                                                <span>Verified Client</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Slide 3: Enterprise Dedicated Bare Metal -->
                                <div class="swiper-slide">
                                    <div class="review-feature-card">
                                        <div>
                                            <div class="review-card-header-row">
                                                <div class="review-industry-pill">
                                                    <i class="las la-microchip"></i>
                                                    <span>Enterprise Media &amp; Big Data</span>
                                                </div>
                                                <div class="review-card-stars">
                                                    <i class="las la-star"></i><i class="las la-star"></i><i
                                                        class="las la-star"></i><i class="las la-star"></i><i
                                                        class="las la-star"></i>
                                                </div>
                                            </div>

                                            <div class="review-quote-mark">
                                                <i class="las la-quote-left"></i>
                                            </div>

                                            <p class="review-quote-text">
                                                &ldquo;Our dedicated bare-metal servers were provisioned with custom
                                                hardware RAID-10 NVMe and AlmaLinux in less than 3 hours. The IPMI
                                                interface gives complete out-of-band management. CloudFlox delivers
                                                enterprise-tier reliability and network throughput at very competitive
                                                pricing.&rdquo;
                                            </p>

                                            <div class="review-impact-strip">
                                                <div class="impact-icon"><i class="las la-chart-line"></i></div>
                                                <div class="impact-text">
                                                    <strong>Key Impact:</strong> <span>Sub-3-Hour Hardware Provisioning
                                                        &amp; 10Gbps Uplink SLA</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="review-author-row">
                                            <div class="review-author-profile">
                                                <div class="author-avatar-badge">
                                                    <span>DP</span>
                                                </div>
                                                <div>
                                                    <h4 class="author-name">David Peterson</h4>
                                                    <p class="author-meta">Chief Technology Officer &bull; Apex Media
                                                        Networks</p>
                                                </div>
                                            </div>
                                            <div class="verified-client-pill">
                                                <i class="las la-check-circle"></i>
                                                <span>Verified Client</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Slide 4: Digital Agency & Multi-Tenant -->
                                <div class="swiper-slide">
                                    <div class="review-feature-card">
                                        <div>
                                            <div class="review-card-header-row">
                                                <div class="review-industry-pill">
                                                    <i class="las la-layer-group"></i>
                                                    <span>Digital Agency &amp; Multi-Client</span>
                                                </div>
                                                <div class="review-card-stars">
                                                    <i class="las la-star"></i><i class="las la-star"></i><i
                                                        class="las la-star"></i><i class="las la-star"></i><i
                                                        class="las la-star"></i>
                                                </div>
                                            </div>

                                            <div class="review-quote-mark">
                                                <i class="las la-quote-left"></i>
                                            </div>

                                            <p class="review-quote-text">
                                                &ldquo;Managing over 60 client WordPress websites was a nightmare on our
                                                previous host with constant PHP memory timeouts. Moving to CloudFlox
                                                LiteSpeed caching and CloudLinux account isolation completely eliminated
                                                resource bottlenecks. Our client satisfaction score is at an all-time
                                                high.&rdquo;
                                            </p>

                                            <div class="review-impact-strip">
                                                <div class="impact-icon"><i class="las la-chart-line"></i></div>
                                                <div class="impact-text">
                                                    <strong>Key Impact:</strong> <span>60+ Agency Sites Migrated with
                                                        Zero Bottlenecks</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="review-author-row">
                                            <div class="review-author-profile">
                                                <div class="author-avatar-badge">
                                                    <span>ER</span>
                                                </div>
                                                <div>
                                                    <h4 class="author-name">Elena Rostova</h4>
                                                    <p class="author-meta">Managing Director &bull; HyperPixel Creative
                                                        Agency</p>
                                                </div>
                                            </div>
                                            <div class="verified-client-pill">
                                                <i class="las la-check-circle"></i>
                                                <span>Verified Client</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- Slider Controls (Pagination & Navigation) -->
                        <div class="review-slider-controls">
                            <!-- Swiper Dots -->
                            <div class="review-swiper-pagination"></div>

                            <!-- Swiper Arrows -->
                            <div class="review-nav-arrows">
                                <button class="review-nav-btn review-prev-btn" aria-label="Previous Review"
                                    type="button">
                                    <i class="las la-arrow-left"></i>
                                </button>
                                <button class="review-nav-btn review-next-btn" aria-label="Next Review" type="button">
                                    <i class="las la-arrow-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- =========================================================================
         CUSTOM INFRASTRUCTURE CTA & SHORT FORM (Enterprise Lead Generation)
         ========================================================================= -->
    <section class="modern-custom-quote-section" id="custom-quote">
        <div class="container position-relative z-2">
            <div class="row align-items-center g-5">

                <!-- Left Column: Value Proposition & Enterprise SLA -->
                <div class="col-lg-6" data-sal="slide-up" data-sal-duration="600">
                    <span class="custom-cta-badge"><i class="las la-project-diagram"></i> Tailored Solutions</span>
                    <h2 class="custom-cta-title">
                        Need Dedicated Infrastructure? <span>Get 24/7 Expert Support &amp; Custom Architecture</span>
                    </h2>
                    <p class="custom-cta-desc">
                        Standard packages don't always fit enterprise workloads. Whether you require multi-server
                        high-availability clusters, dedicated bare-metal nodes, private virtualization, or strict
                        regulatory compliance &mdash; our senior systems engineers will architect the exact solution
                        your platform demands.
                    </p>

                    <!-- Highlights List -->
                    <div class="cq-highlights-list">
                        <div class="cq-highlight-item">
                            <div class="cq-highlight-icon">
                                <i class="las la-server"></i>
                            </div>
                            <div>
                                <div class="cq-highlight-title">Custom Hardware Provisioning</div>
                                <p class="cq-highlight-text">Tailored AMD EPYC / Intel Xeon configurations, NVMe RAID-10
                                    arrays, and dedicated 10Gbps unmetered uplinks.</p>
                            </div>
                        </div>

                        <div class="cq-highlight-item">
                            <div class="cq-highlight-icon">
                                <i class="las la-shield-alt"></i>
                            </div>
                            <div>
                                <div class="cq-highlight-title">Zero-Downtime White-Glove Migration</div>
                                <p class="cq-highlight-text">Full hands-off migration of databases, containers, and web
                                    assets orchestrated by certified Linux sysadmins.</p>
                            </div>
                        </div>

                        <div class="cq-highlight-item">
                            <div class="cq-highlight-icon">
                                <i class="las la-clock"></i>
                            </div>
                            <div>
                                <div class="cq-highlight-title">Guaranteed 2 &ndash; 4 Hour Response SLA</div>
                                <p class="cq-highlight-text">Zero automated robot runarounds. A human Enterprise
                                    Solutions Architect directly reviews your specifications.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Direct Sales Contact Strip -->
                    <div class="cq-sales-contact-strip">
                        <a href="mailto:info@cloudflox.com" class="cq-contact-pill">
                            <i class="las la-envelope"></i> info@cloudflox.com
                        </a>
                        <span class="cq-contact-pill">
                            <i class="las la-headset"></i> 24/7/365 SysAdmin On-Call
                        </span>
                        <span class="cq-contact-pill">
                            <i class="las la-check-circle text-success"></i> 99.99% Uptime SLA
                        </span>
                    </div>
                </div>

                <!-- Right Column: Interactive Short Form Card -->
                <div class="col-lg-6" data-sal="slide-up" data-sal-duration="600" data-sal-delay="100">
                    <div class="custom-quote-card">

                        <!-- Quote Form -->
                        <form id="customQuoteForm" onsubmit="submitCustomQuote(event)">
                            <div class="cq-card-header">
                                <h3 class="cq-card-title">Request Infrastructure Quote</h3>
                                <p class="cq-card-sub">Share your workload specifications for a tailored topology and
                                    pricing.</p>
                            </div>

                            <!-- Name -->
                            <div class="cq-form-group">
                                <label class="cq-label" for="cq_name">Full Name <span
                                        class="text-danger">*</span></label>
                                <div class="cq-input-wrap">
                                    <i class="las la-user"></i>
                                    <input type="text" id="cq_name" name="name" class="cq-input"
                                        placeholder="e.g. Alex Henderson" required>
                                </div>
                            </div>

                            <!-- Business Email -->
                            <div class="cq-form-group">
                                <label class="cq-label" for="cq_email">Business Email <span
                                        class="text-danger">*</span></label>
                                <div class="cq-input-wrap">
                                    <i class="las la-envelope"></i>
                                    <input type="email" id="cq_email" name="email" class="cq-input"
                                        placeholder="alex@company.com" required>
                                </div>
                            </div>

                            <!-- Row: Service & Budget -->
                            <div class="row g-3">
                                <div class="col-md-6 cq-form-group">
                                    <label class="cq-label" for="cq_service">Service Needed <span
                                            class="text-danger">*</span></label>
                                    <div class="cq-input-wrap">
                                        <i class="las la-layer-group"></i>
                                        <select id="cq_service" name="service" class="cq-select" required>
                                            <option value="" disabled selected>Select service type...</option>
                                            <option value="Custom Bare-Metal Dedicated">Custom Bare-Metal Server
                                            </option>
                                            <option value="High-Availability Multi-Server Cluster">High-Availability
                                                Cluster</option>
                                            <option value="Private Cloud / Virtual Datacenter">Private Cloud / Virtual
                                                DC</option>
                                            <option value="High-Traffic E-Commerce Cluster">High-Traffic E-Commerce
                                            </option>
                                            <option value="Disaster Recovery & Replication">Disaster Recovery Cluster
                                            </option>
                                            <option value="Enterprise Hybrid & Migration">Hybrid Cloud & Migration
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6 cq-form-group">
                                    <label class="cq-label" for="cq_budget">Estimated Monthly Budget <span
                                            class="text-danger">*</span></label>
                                    <div class="cq-input-wrap">
                                        <i class="las la-coins"></i>
                                        <select id="cq_budget" name="budget" class="cq-select" required>
                                            <option value="" disabled selected>Select budget scope...</option>
                                            <option value="$100 - $300 /mo">$100 &ndash; $300 /mo</option>
                                            <option value="$300 - $750 /mo">$300 &ndash; $750 /mo</option>
                                            <option value="$750 - $2,000 /mo">$750 &ndash; $2,000 /mo</option>
                                            <option value="$2,000+ /mo (Enterprise)">$2,000+ /mo (Enterprise)</option>
                                            <option value="Flexible / Need Guidance">Flexible / Need Guidance</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Requirements -->
                            <div class="cq-form-group">
                                <label class="cq-label" for="cq_requirements">Workload &amp; Server Requirements <span
                                        class="text-danger">*</span></label>
                                <textarea id="cq_requirements" name="requirements" class="cq-textarea" rows="3"
                                    placeholder="Specify required CPU cores, RAM, NVMe storage, traffic volume, software stack, or compliance needs..."
                                    required></textarea>
                            </div>

                            <!-- Anti-spam Honeypot (Hidden) -->
                            <input type="text" name="cf_hp_field" id="cq_hp" style="display:none;" tabindex="-1"
                                autocomplete="off">

                            <!-- Submit Buttons -->
                            <button type="submit" id="cq_submit_btn" class="btn-cq-submit">
                                <i class="las la-paper-plane"></i>
                                <span>Request Custom Architecture Quote</span>
                                <i class="las la-arrow-right"></i>
                            </button>
                            <button type="button" id="cq_loading_btn" class="btn-cq-submit d-none" disabled>
                                <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                                <span>Delivering to Sales Mailbox...</span>
                            </button>

                            <div class="cq-security-note">
                                <i class="las la-shield-alt"></i> 256-bit encrypted delivery directly to senior
                                enterprise sales mailbox.
                            </div>
                        </form>

                        <!-- Success Confirmation Card (Hidden by default) -->
                        <div class="cq-success-box" id="cq_success_card" style="display: none;">
                            <div class="cq-success-icon-wrap">
                                <i class="las la-check"></i>
                            </div>
                            <h3 class="cq-success-title">Quote Request Received!</h3>
                            <p class="cq-success-desc">
                                Thank you, <strong id="cq_res_name" class="text-white"></strong>. Your enterprise
                                requirements have been securely delivered to our sales engineering mailbox.
                            </p>

                            <div class="cq-ref-pill">
                                Reference ID: <strong id="cq_res_ref">CFX-REQ-84920</strong>
                            </div>

                            <div class="cq-sla-box">
                                <div class="cq-sla-title">
                                    <i class="las la-bolt"></i> 2 &ndash; 4 Hour Response Commitment
                                </div>
                                <p class="cq-sla-text">
                                    A Senior Infrastructure Architect is currently reviewing your workload topology. A
                                    formal architecture proposal will be sent to <strong id="cq_res_email"
                                        class="text-white"></strong> shortly.
                                </p>
                            </div>

                            <button type="button" class="btn-cq-reset" onclick="resetCustomQuoteForm()">
                                <i class="las la-redo me-1"></i> Submit Another Request
                            </button>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- =========================================================================
         9. FREQUENTLY ASKED QUESTIONS (Knowledge & Support Hub)
         ========================================================================= -->
    <section class="modern-faq-section" id="faqSection">
        <div class="container">
            <!-- Section Header -->
            <div class="text-center mb-4">
                <div class="faq-badge-pill" data-sal="slide-down" data-sal-duration="500">
                    <span class="pulse-dot-blue"></span>
                    <span>Knowledge &amp; Support Hub</span>
                </div>
                <h2 class="faq-section-title" data-sal="slide-up" data-sal-duration="600">
                    Clear Answers. <span class="text-accent-blue">Zero Guesswork.</span>
                </h2>
                <p class="faq-section-subtitle" data-sal="slide-up" data-sal-duration="600" data-sal-delay="100">
                    Everything you need to know about instant activation, free migrations, NVMe performance, security,
                    and global datacenter operations.
                </p>

                <!-- Category Filter Switcher -->
                <div class="faq-category-nav" data-sal="slide-up" data-sal-duration="600" data-sal-delay="150">
                    <button class="faq-cat-btn active" onclick="filterFaq('all', this)" type="button">
                        <i class="las la-th-large"></i> All FAQs
                    </button>
                    <button class="faq-cat-btn" onclick="filterFaq('setup', this)" type="button">
                        <i class="las la-rocket"></i> Setup &amp; Plans
                    </button>
                    <button class="faq-cat-btn" onclick="filterFaq('migration', this)" type="button">
                        <i class="las la-exchange-alt"></i> Migration &amp; Backups
                    </button>
                    <button class="faq-cat-btn" onclick="filterFaq('security', this)" type="button">
                        <i class="las la-shield-alt"></i> Security &amp; Network
                    </button>
                </div>
            </div>

            <!-- Main Layout Grid (Support Island Left + Accordion Right) -->
            <div class="row g-4 align-items-start">

                <!-- Left Column: Sticky Support & Assistance Card -->
                <div class="col-lg-4" data-sal="slide-up" data-sal-duration="600">
                    <div class="faq-help-card">
                        <div class="faq-help-icon-wrap">
                            <i class="las la-headset"></i>
                        </div>
                        <h3 class="faq-help-title">Have a Different Question?</h3>
                        <p class="faq-help-desc">
                            Can't find the exact technical specifications or plan configuration you're looking for? Our
                            certified tier-3 systems engineers are online 24/7/365 to guide your migration and
                            deployment.
                        </p>

                        <div class="faq-help-actions">
                            <a href="javascript:void(0)" onclick="openLiveChat(event)" class="btn-faq-chat" aria-label="Start Live Chat">
                                <i class="las la-comments"></i> Start Live Chat
                            </a>
                            <a href="mailto:support@cloudflox.com" class="btn-faq-email">
                                <i class="las la-envelope"></i> Email Support
                            </a>
                        </div>

                        <div class="faq-help-telemetry">
                            <div class="faq-telemetry-item">
                                <i class="las la-check-circle"></i>
                                <span>Average Response: <strong>&lt; 4 Minutes</strong></span>
                            </div>
                            <div class="faq-telemetry-item">
                                <i class="las la-check-circle"></i>
                                <span>100% In-House Linux SysAdmins</span>
                            </div>
                            <div class="faq-telemetry-item">
                                <i class="las la-check-circle"></i>
                                <span>30-Day Money-Back Guarantee</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Modern FAQ Accordion -->
                <div class="col-lg-8" data-sal="slide-up" data-sal-duration="600" data-sal-delay="100">
                    <div class="accordion faq-modern-accordion" id="faqAccordion">

                        <!-- FAQ 1 -->
                        <div class="faq-modern-item" data-category="setup">
                            <h3 class="faq-modern-header" id="headingOne">
                                <button class="faq-modern-btn" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                    <div class="faq-btn-left">
                                        <div class="faq-q-icon icon-blue-soft">
                                            <i class="las la-bolt"></i>
                                        </div>
                                        <span class="faq-q-text">How fast is my hosting account activated after
                                            purchase?</span>
                                    </div>
                                    <div class="faq-chevron-pill">
                                        <i class="las la-angle-down"></i>
                                    </div>
                                </button>
                            </h3>
                            <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne"
                                data-bs-parent="#faqAccordion">
                                <div class="faq-modern-body">
                                    All shared hosting and cloud VPS accounts are activated <strong>automatically within
                                        60 seconds</strong> upon payment confirmation. You will immediately receive your
                                    cPanel, DirectAdmin, or root VPS credentials via your welcome email and inside your
                                    client portal, allowing you to deploy applications without waiting.
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 2 -->
                        <div class="faq-modern-item" data-category="migration">
                            <h3 class="faq-modern-header" id="headingTwo">
                                <button class="faq-modern-btn collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                    <div class="faq-btn-left">
                                        <div class="faq-q-icon icon-green-soft">
                                            <i class="las la-exchange-alt"></i>
                                        </div>
                                        <span class="faq-q-text">Do you offer free website &amp; database migration from
                                            my current host?</span>
                                    </div>
                                    <div class="faq-chevron-pill">
                                        <i class="las la-angle-down"></i>
                                    </div>
                                </button>
                            </h3>
                            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo"
                                data-bs-parent="#faqAccordion">
                                <div class="faq-modern-body">
                                    <strong>Yes, 100% free white-glove migration!</strong> We transfer all cPanel,
                                    DirectAdmin, Plesk, and WordPress websites. Simply open a migration ticket after
                                    signup with your old host details, and our senior systems engineers will transfer
                                    your files, databases, SSL certificates, and email inboxes with <strong>zero
                                        downtime</strong>.
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 3 -->
                        <div class="faq-modern-item" data-category="setup">
                            <h3 class="faq-modern-header" id="headingThree">
                                <button class="faq-modern-btn collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                    <div class="faq-btn-left">
                                        <div class="faq-q-icon icon-orange-soft">
                                            <i class="las la-level-up-alt"></i>
                                        </div>
                                        <span class="faq-q-text">Can I upgrade my plan seamlessly as my website traffic
                                            surges?</span>
                                    </div>
                                    <div class="faq-chevron-pill">
                                        <i class="las la-angle-down"></i>
                                    </div>
                                </button>
                            </h3>
                            <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree"
                                data-bs-parent="#faqAccordion">
                                <div class="faq-modern-body">
                                    Seamlessly! You can upgrade from Starter to Turbo, Business, Enterprise, or
                                    transition directly to an isolated Cloud VPS or Dedicated Server from your client
                                    portal. All upgrades are <strong>automated and prorated instantly</strong> with zero
                                    downtime and no manual data migration required.
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 4 -->
                        <div class="faq-modern-item" data-category="security">
                            <h3 class="faq-modern-header" id="headingFour">
                                <button class="faq-modern-btn collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                    <div class="faq-btn-left">
                                        <div class="faq-q-icon icon-purple-soft">
                                            <i class="las la-shield-alt"></i>
                                        </div>
                                        <span class="faq-q-text">What security features, firewall, and DDoS defense are
                                            included?</span>
                                    </div>
                                    <div class="faq-chevron-pill">
                                        <i class="las la-angle-down"></i>
                                    </div>
                                </button>
                            </h3>
                            <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour"
                                data-bs-parent="#faqAccordion">
                                <div class="faq-modern-body">
                                    Every CloudFlox account is fortified with <strong>Imunify360 AI-driven malware
                                        detection</strong>, Web Application Firewall (WAF), proactive brute-force
                                    shielding, free auto-renewing Let's Encrypt SSL certificates, and always-on
                                    <strong>multi-terabit Anti-DDoS mitigation</strong> that scrub malicious traffic
                                    before it reaches your server.
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 5 -->
                        <div class="faq-modern-item" data-category="security">
                            <h3 class="faq-modern-header" id="headingFive">
                                <button class="faq-modern-btn collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                                    <div class="faq-btn-left">
                                        <div class="faq-q-icon icon-teal-soft">
                                            <i class="las la-globe-americas"></i>
                                        </div>
                                        <span class="faq-q-text">Where are CloudFlox servers located, and can I choose
                                            my region?</span>
                                    </div>
                                    <div class="faq-chevron-pill">
                                        <i class="las la-angle-down"></i>
                                    </div>
                                </button>
                            </h3>
                            <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive"
                                data-bs-parent="#faqAccordion">
                                <div class="faq-modern-body">
                                    We operate enterprise Tier-4 datacenter facilities in key geographical hubs:
                                    <strong>United States (East &amp; West Coast), Germany (Frankfurt), United Kingdom
                                        (London), France, and Singapore</strong>. You can choose your preferred
                                    deployment region during checkout to guarantee lowest ping and latency for your
                                    audience.
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 6 -->
                        <div class="faq-modern-item" data-category="migration">
                            <h3 class="faq-modern-header" id="headingSix">
                                <button class="faq-modern-btn collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseSix" aria-expanded="false" aria-controls="collapseSix">
                                    <div class="faq-btn-left">
                                        <div class="faq-q-icon icon-blue-soft">
                                            <i class="las la-history"></i>
                                        </div>
                                        <span class="faq-q-text">How are automated backups handled, and how quickly can
                                            I restore?</span>
                                    </div>
                                    <div class="faq-chevron-pill">
                                        <i class="las la-angle-down"></i>
                                    </div>
                                </button>
                            </h3>
                            <div id="collapseSix" class="accordion-collapse collapse" aria-labelledby="headingSix"
                                data-bs-parent="#faqAccordion">
                                <div class="faq-modern-body">
                                    We maintain automated daily point-in-time snapshots powered by
                                    <strong>JetBackup</strong>, replicated to dedicated off-site backup storage. You can
                                    restore your entire account, single files, emails, or specific MySQL databases
                                    independently with <strong>1-click self-service</strong> right from your cPanel
                                    control panel.
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 7 -->
                        <div class="faq-modern-item" data-category="setup">
                            <h3 class="faq-modern-header" id="headingSeven">
                                <button class="faq-modern-btn collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseSeven" aria-expanded="false" aria-controls="collapseSeven">
                                    <div class="faq-btn-left">
                                        <div class="faq-q-icon icon-green-soft">
                                            <i class="las la-award"></i>
                                        </div>
                                        <span class="faq-q-text">Do you offer a money-back guarantee if I am not
                                            completely satisfied?</span>
                                    </div>
                                    <div class="faq-chevron-pill">
                                        <i class="las la-angle-down"></i>
                                    </div>
                                </button>
                            </h3>
                            <div id="collapseSeven" class="accordion-collapse collapse" aria-labelledby="headingSeven"
                                data-bs-parent="#faqAccordion">
                                <div class="faq-modern-body">
                                    <strong>Absolutely!</strong> All CloudFlox shared hosting and WordPress cloud plans
                                    include an unconditional <strong>30-Day Money-Back Guarantee</strong>. If for any
                                    reason you are not completely satisfied with our performance or support, request a
                                    cancellation within 30 days for a full, prompt refund.
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 8 -->
                        <div class="faq-modern-item" data-category="security">
                            <h3 class="faq-modern-header" id="headingEight">
                                <button class="faq-modern-btn collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseEight" aria-expanded="false" aria-controls="collapseEight">
                                    <div class="faq-btn-left">
                                        <div class="faq-q-icon icon-orange-soft">
                                            <i class="las la-sliders-h"></i>
                                        </div>
                                        <span class="faq-q-text">Which control panels and developer environments are
                                            supported?</span>
                                    </div>
                                    <div class="faq-chevron-pill">
                                        <i class="las la-angle-down"></i>
                                    </div>
                                </button>
                            </h3>
                            <div id="collapseEight" class="accordion-collapse collapse" aria-labelledby="headingEight"
                                data-bs-parent="#faqAccordion">
                                <div class="faq-modern-body">
                                    We support official <strong>cPanel &amp; WHM, Plesk Obsidian, DirectAdmin, and
                                        Webmin</strong>. Full root SSH access, Git version control, multi-PHP selector
                                    (7.4 to 8.4), Node.js, Python app managers, and Docker container support are
                                    pre-integrated for modern DevOps workflows.
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- =========================================================================
         10. HIGH-IMPACT BOTTOM CALL TO ACTION BANNER (Cloud Launchpad)
         ========================================================================= -->
    <section class="modern-cta-section" id="ctaSection">
        <div class="container">
            <div class="cta-banner-card">
                <!-- Ambient Aurora Glows & Cyber-Grid Mask -->
                <div class="cta-glow-mesh cta-glow-top-right" aria-hidden="true"></div>
                <div class="cta-glow-mesh cta-glow-bottom-left" aria-hidden="true"></div>
                <div class="cta-grid-pattern" aria-hidden="true"></div>

                <!-- Floating Desktop Telemetry Badges -->
                <div class="cta-float-badge cta-float-left d-none d-xl-flex" aria-hidden="true">
                    <span class="cta-float-icon"><i class="las la-server"></i></span>
                    <div class="cta-float-info">
                        <strong>AMD EPYC™ Gen4</strong>
                        <span>Enterprise Bare-Metal</span>
                    </div>
                </div>

                <div class="cta-float-badge cta-float-right d-none d-xl-flex" aria-hidden="true">
                    <span class="cta-float-icon icon-green"><i class="las la-shield-alt"></i></span>
                    <div class="cta-float-info">
                        <strong>99.99% Uptime SLA</strong>
                        <span>Live Global Fleet</span>
                    </div>
                </div>

                <!-- Center High-Impact Content -->
                <div class="cta-inner-content text-center">
                    <!-- Live Status Pill -->
                    <div class="cta-status-pill">
                        <span class="cta-pulse-dot"></span>
                        <span class="cta-status-text">Instant Cloud Deployment • Ready in 60s</span>
                    </div>

                    <!-- Main Catchy Title with Gradient Accent -->
                    <h2 class="cta-banner-title">
                        Deploy Enterprise NVMe &amp; Dedicated Cloud Today <span class="cta-gradient-text">With Free Setup &amp; 24/7 Support</span>
                    </h2>

                    <!-- Compelling Subtitle -->
                    <p class="cta-banner-desc">
                        Deploy your ultra-fast NVMe cloud hosting or VPS server today. Backed by 24/7 expert human
                        support,
                        free zero-downtime migrations, and an ironclad 99.99% uptime guarantee.
                    </p>

                    <!-- Primary & Secondary Buttons -->
                    <div class="cta-buttons-wrapper">
                        <a href="#pricing" class="btn-cta-primary-glow">
                            <span>Get Started Now</span>
                            <i class="las la-arrow-right"></i>
                        </a>
                        <a href="contact-us" class="btn-cta-glass">
                            <i class="las la-comments"></i>
                            <span>Talk to Cloud Specialist</span>
                        </a>
                    </div>

                    <!-- Trust Telemetry Bar -->
                    <div class="cta-trust-bar">
                        <div class="cta-trust-item">
                            <i class="las la-check-circle"></i>
                            <span>30-Day Money-Back Guarantee</span>
                        </div>
                        <div class="cta-trust-divider d-none d-md-block"></div>
                        <div class="cta-trust-item">
                            <i class="las la-bolt"></i>
                            <span>Instant 60-Sec Activation</span>
                        </div>
                        <div class="cta-trust-divider d-none d-md-block"></div>
                        <div class="cta-trust-item">
                            <i class="las la-exchange-alt"></i>
                            <span>Free Zero-Downtime Migration</span>
                        </div>
                        <div class="cta-trust-divider d-none d-md-block"></div>
                        <div class="cta-trust-item">
                            <i class="las la-lock"></i>
                            <span>Free SSL &amp; DDoS Guard</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- =========== Footer Area =========== -->
    <?php
    $hide_footer_cta = true;
    include 'include/footer.inc.php';
    ?>
    <!-- =========== Footer Area =========== -->

    <!-- =========== JS Area =========== -->
    <?php include 'include/js.inc.php'; ?>
    <!-- =========== JS Area =========== -->

    <!-- CloudFlox Homepage Interactive Scripts (Pricing, Comparison, Matchmaker, Quotes) -->
    <script src="assets/js/cloudflox-home.js?v=7"></script>

</body>

</html>