<?php include 'include/url.inc.php'; ?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">

<head>
    <!-- Basic Meta -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Title -->
    <title>Domain Registration &amp; Transfer | Cheap Domains &amp; Free Privacy - CloudFlox</title>

    <!-- SEO -->
    <meta name="description"
        content="Register, transfer, and manage 500+ domain extensions with CloudFlox. Enjoy instant activation, free WHOIS privacy protection, high-speed Anycast DNS, and 24/7 specialist support.">
    <meta name="keywords"
        content="domain registration, buy domain, transfer domain, cheap domains, cloudflox domains, dns management, whois privacy, register domain online, tld pricing">
    <meta name="author" content="CloudFlox">

    <!-- Canonical -->
    <link rel="canonical" href="https://cloudflox.com/domain">

    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="Domain Registration &amp; Transfer | CloudFlox">
    <meta property="og:description"
        content="Search and register your domain name with CloudFlox. Free WHOIS privacy, Anycast DNS management, and seamless 0-downtime transfers.">
    <meta property="og:url" content="https://cloudflox.com/domain">
    <meta property="og:image" content="https://cloudflox.com/assets/imgs/banners/dedicated-server-components.jpg">
    <meta property="og:site_name" content="CloudFlox">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Domain Registration &amp; Transfer | CloudFlox">
    <meta name="twitter:description"
        content="Secure your domain name today with CloudFlox. Fast registration, free privacy protection, and global DNS management.">
    <meta name="twitter:image" content="https://cloudflox.com/assets/imgs/banners/dedicated-server-components.jpg">

    <!-- JSON-LD Structured Data -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@graph": [
        {
          "@type": "Product",
          "name": "CloudFlox Domain Registration & Management",
          "description": "Register, transfer, and manage top-level domain extensions with free WHOIS privacy and global Anycast DNS management.",
          "brand": {
            "@type": "Brand",
            "name": "CloudFlox"
          },
          "offers": {
            "@type": "AggregateOffer",
            "priceCurrency": "USD",
            "lowPrice": "3.99",
            "highPrice": "39.99",
            "offerCount": "500"
          }
        },
        {
          "@type": "BreadcrumbList",
          "itemListElement": [
            {
              "@type": "ListItem",
              "position": 1,
              "name": "Home",
              "item": "https://cloudflox.com/"
            },
            {
              "@type": "ListItem",
              "position": 2,
              "name": "Domains",
              "item": "https://cloudflox.com/domain"
            },
            {
              "@type": "ListItem",
              "position": 3,
              "name": "Domain Registration",
              "item": "https://cloudflox.com/domain"
            }
          ]
        }
      ]
    }
    </script>

    <!-- =========== CSS Area =========== -->
    <?php include 'include/css.inc.php'; ?>
    <!-- =========== CSS Area =========== -->
</head>

<body class="pure-white-theme cf-domain-page">

    <!-- =========== Header Area =========== -->
    <?php include 'include/header.inc.php'; ?>
    <!-- =========== Header Area =========== -->

    <!-- =========================================================================
         1. HERO SECTION (Global Domain Registration & Search)
         ========================================================================= -->
    <section class="cf-domain-hero position-relative">
        <div class="hero-ambient-glow" aria-hidden="true"></div>

        <!-- Ambient Decorative Pattern Backgrounds -->
        <div class="pricing-bg-shape pricing-shape-left" aria-hidden="true" style="opacity: 0.45;">
            <img src="assets/imgs/shape/pattern-2.svg?v=2" alt="" width="370" height="590" loading="eager" decoding="async">
        </div>
        <div class="pricing-bg-shape pricing-shape-right" aria-hidden="true" style="opacity: 0.45;">
            <img src="assets/imgs/shape/pattern-3.svg?v=2" alt="" width="370" height="590" loading="eager" decoding="async">
        </div>

        <div class="container position-relative z-2">
            <!-- Breadcrumbs -->
            <nav aria-label="breadcrumb" class="mb-3 pt-2">
                <ol class="breadcrumb custom-breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="<?php echo $url; ?>" class="text-decoration-none text-muted">
                            <i class="las la-home me-1"></i>Home
                        </a>
                    </li>
                    <li class="breadcrumb-item text-muted">Domains</li>
                    <li class="breadcrumb-item active text-blue fw-semibold" aria-current="page">Domain Registration</li>
                </ol>
            </nav>

            <div class="row align-items-center g-4 g-xl-5 pb-lg-3">
                <!-- Left Column: Copy, Search & Value Proposition -->
                <div class="col-12 col-lg-7">
                    <!-- Hero Badge -->
                    <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill bg-blue-subtle text-blue fw-bold fs-13 mb-3 border border-blue-subtle">
                        <span class="cf-pulse-dot-blue"></span>
                        <span>ICANN ACCREDITED REGISTRAR &amp; ANYCAST DNS</span>
                    </div>

                    <!-- Hero Title -->
                    <h1 class="cf-domain-hero-title fw-extrabold text-slate-900 tracking-tight mb-3">
                        Find &amp; Secure Your <br class="d-none d-sm-inline">
                        <span class="text-gradient-domain">Perfect Domain Name</span>
                    </h1>

                    <!-- Hero Lead -->
                    <p class="cf-domain-hero-lead text-slate-600 mb-4 lh-base">
                        Search over 500+ top-level extensions with instant automated setup. Every CloudFlox domain includes free WHOIS privacy protection, high-speed Anycast DNS, domain theft lock, and 24/7 technical support.
                    </p>

                    <!-- Streamlined Domain Search Box -->
                    <form action="https://my.cloudflox.com/cart.php?a=add&domain=register" method="post"
                        id="domainForm" class="domain-mega-search-box mx-0 mb-3" style="max-width: 100%;" aria-label="Domain Name Search">
                        <i class="las la-search domain-search-icon" aria-hidden="true"></i>
                        <input type="text" name="query" id="domainInput"
                            placeholder="Type your domain name (e.g. mybrand.com)..." required
                            autocomplete="off" aria-label="Domain name to register">
                        <button type="submit" id="domainSearchBtn" class="domain-mega-btn"
                            aria-label="Search Domain">
                            <span>Search</span>
                            <i class="las la-arrow-right ms-2" aria-hidden="true"></i>
                        </button>
                    </form>

                    <!-- Real-time Live Domain Search Results Container -->
                    <div id="domainSearchResults" class="cf-domain-results-wrap" style="display: none;"></div>

                    <!-- Compact Popular TLD Pills -->
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                        <span class="fs-13 text-muted fw-semibold me-1">Popular:</span>
                        <button type="button" class="btn btn-sm btn-light border py-1 px-3 rounded-pill fs-13 fw-bold text-slate-800 d-inline-flex align-items-center gap-1 hover-shadow transition" onclick="setDomainExt('.com', this)">
                            <span class="text-blue">.com</span> <span class="text-muted fw-normal">$13.57</span>
                        </button>
                        <button type="button" class="btn btn-sm btn-light border py-1 px-3 rounded-pill fs-13 fw-bold text-slate-800 d-inline-flex align-items-center gap-1 hover-shadow transition" onclick="setDomainExt('.net', this)">
                            <span class="text-blue">.net</span> <span class="text-muted fw-normal">$15.49</span>
                        </button>
                        <button type="button" class="btn btn-sm btn-light border py-1 px-3 rounded-pill fs-13 fw-bold text-slate-800 d-inline-flex align-items-center gap-1 hover-shadow transition" onclick="setDomainExt('.org', this)">
                            <span class="text-blue">.org</span> <span class="text-muted fw-normal">$17.49</span>
                        </button>
                        <button type="button" class="btn btn-sm btn-light border py-1 px-3 rounded-pill fs-13 fw-bold text-slate-800 d-inline-flex align-items-center gap-1 hover-shadow transition" onclick="setDomainExt('.cloud', this)">
                            <span class="text-orange">.cloud</span> <span class="badge bg-orange-subtle text-orange ms-1 fs-10">DEAL $9.99</span>
                        </button>
                        <button type="button" class="btn btn-sm btn-light border py-1 px-3 rounded-pill fs-13 fw-bold text-slate-800 d-inline-flex align-items-center gap-1 hover-shadow transition" onclick="setDomainExt('.lk', this)">
                            <span class="text-emerald">.lk</span> <span class="text-muted fw-normal">$29.00</span>
                        </button>
                    </div>

                    <!-- Understated Trust Perks & Transfer Link -->
                    <div class="d-flex flex-wrap align-items-center gap-3 text-slate-600 fs-13 pt-1">
                        <span class="d-inline-flex align-items-center gap-1">
                            <i class="las la-shield-alt text-primary fs-15"></i> Free Privacy
                        </span>
                        <span class="text-muted d-none d-sm-inline">•</span>
                        <span class="d-inline-flex align-items-center gap-1">
                            <i class="las la-bolt text-warning fs-15"></i> Instant DNS
                        </span>
                        <span class="text-muted d-none d-sm-inline">•</span>
                        <span class="d-inline-flex align-items-center gap-1">
                            <i class="las la-lock text-success fs-15"></i> Theft Lock
                        </span>
                        <span class="text-muted d-none d-sm-inline">•</span>
                        <a href="#domainTransfer" class="text-decoration-none text-blue fw-semibold">
                            Transfer Domain <i class="las la-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>

                <!-- Right Column: Visual Hero Infrastructure Showcase -->
                <div class="col-12 col-lg-5">
                    <div class="cf-budget-hero-visual position-relative text-center">
                        <div class="cf-hero-img-wrap position-relative mx-auto" style="max-width: 480px;">
                            <img src="assets/imgs/banners/cloudflox-domain-infrastructure.jpg"
                                 alt="CloudFlox Global Domain Registration & Anycast DNS Network"
                                 class="cf-hero-illustration img-fluid rounded-4 shadow-sm"
                                 width="600"
                                 height="450"
                                 fetchpriority="high"
                                 loading="eager">

                            <!-- Floating Chip (Top Left) -->
                            <div class="cf-hero-float-chip float-chip-top shadow-sm">
                                <span class="cf-pulse-dot-blue"></span>
                                <div class="text-start">
                                    <span class="d-block cf-chip-sub">Anycast DNS</span>
                                    <strong class="cf-chip-val text-blue">&lt; 5ms Latency</strong>
                                </div>
                                <span class="badge bg-blue-subtle text-blue fw-bold px-2 py-1 rounded-pill ms-2 fs-11">
                                    100% Uptime
                                </span>
                            </div>

                            <!-- Floating Chip (Bottom Right) -->
                            <div class="cf-hero-float-chip float-chip-bottom shadow-sm">
                                <div class="cf-chip-icon bg-emerald-subtle text-emerald">
                                    <i class="las la-user-shield"></i>
                                </div>
                                <div class="text-start">
                                    <span class="d-block cf-chip-sub">Identity Protection</span>
                                    <strong class="cf-chip-val text-slate-900">100% Free Privacy</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- =========================================================================
         2. DOMAIN PRICING CATALOG & FILTERABLE EXTENSIONS TABLE
         ========================================================================= -->
    <section class="modern-pricing-section position-relative py-5" id="domainPricing">
        <div class="container position-relative z-2">
            <!-- Section Header -->
            <div class="text-center max-w-750 mx-auto mb-4 mb-md-5">
                <span class="badge bg-blue-subtle text-blue fw-bold px-3 py-1 rounded-pill mb-2 border border-blue-subtle">
                    TRANSPARENT TLD PRICING
                </span>
                <h2 class="display-6 fw-extrabold text-slate-900 mb-2">
                    Explore Popular Extensions &amp; Rates
                </h2>
                <p class="text-slate-600 fs-16 mb-0">
                    Transparent pricing with zero hidden renewal markups. Free privacy protection, Anycast DNS, and domain lock included on all supported TLDs.
                </p>
            </div>

            <!-- TLD Catalog Card -->
            <div class="cf-tld-catalog-card">
                <!-- Search & Filter Controls -->
                <div class="cf-tld-search-bar">
                    <div class="cf-tld-filter-nav" id="tldFilterNav" role="tablist">
                        <button type="button" class="cf-tld-filter-btn active" data-filter="all">All TLDs</button>
                        <button type="button" class="cf-tld-filter-btn" data-filter="popular">Popular</button>
                        <button type="button" class="cf-tld-filter-btn" data-filter="tech">Tech &amp; Cloud</button>
                        <button type="button" class="cf-tld-filter-btn" data-filter="business">Business &amp; Commerce</button>
                        <button type="button" class="cf-tld-filter-btn" data-filter="regional">Regional</button>
                    </div>
                    <div class="cf-tld-search-input-wrap">
                        <i class="las la-search"></i>
                        <input type="text" id="tldSearchInput" placeholder="Filter extension (e.g. .org)..." aria-label="Filter extensions">
                    </div>
                </div>

                <!-- Responsive Table Wrapper -->
                <div class="table-responsive">
                    <table class="cf-tld-table" id="tldTable">
                        <thead>
                            <tr>
                                <th>TLD Extension</th>
                                <th>Category</th>
                                <th>Register (1 Yr)</th>
                                <th>Renewal (1 Yr)</th>
                                <th>Transfer (1 Yr)</th>
                                <th>Free Features</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- .com -->
                            <tr data-cat="popular">
                                <td>
                                    <div class="tld-ext-pill">
                                        <span>.com</span>
                                        <span class="tld-tag tld-tag-popular">Popular</span>
                                    </div>
                                </td>
                                <td><span class="text-muted fs-13">Commercial / Global</span></td>
                                <td><strong class="text-blue fs-15">$13.57</strong></td>
                                <td><span class="text-slate-700">$15.99</span></td>
                                <td><span class="text-slate-700">$13.57</span></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2 fs-12 text-slate-600">
                                        <span class="badge bg-emerald-subtle text-emerald"><i class="las la-check me-1"></i>Privacy</span>
                                        <span class="badge bg-blue-subtle text-blue"><i class="las la-check me-1"></i>DNS</span>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <a href="https://my.cloudflox.com/cart.php?a=add&domain=register&query=.com" class="btn-tld-register">
                                        <span>Register</span> <i class="las la-arrow-right"></i>
                                    </a>
                                </td>
                            </tr>

                            <!-- .net -->
                            <tr data-cat="popular tech">
                                <td>
                                    <div class="tld-ext-pill">
                                        <span>.net</span>
                                        <span class="tld-tag tld-tag-popular">Popular</span>
                                    </div>
                                </td>
                                <td><span class="text-muted fs-13">Network &amp; Tech</span></td>
                                <td><strong class="text-blue fs-15">$15.49</strong></td>
                                <td><span class="text-slate-700">$17.99</span></td>
                                <td><span class="text-slate-700">$15.49</span></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2 fs-12 text-slate-600">
                                        <span class="badge bg-emerald-subtle text-emerald"><i class="las la-check me-1"></i>Privacy</span>
                                        <span class="badge bg-blue-subtle text-blue"><i class="las la-check me-1"></i>DNS</span>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <a href="https://my.cloudflox.com/cart.php?a=add&domain=register&query=.net" class="btn-tld-register">
                                        <span>Register</span> <i class="las la-arrow-right"></i>
                                    </a>
                                </td>
                            </tr>

                            <!-- .org -->
                            <tr data-cat="popular">
                                <td>
                                    <div class="tld-ext-pill">
                                        <span>.org</span>
                                        <span class="tld-tag tld-tag-popular">Popular</span>
                                    </div>
                                </td>
                                <td><span class="text-muted fs-13">Organizations / Non-profit</span></td>
                                <td><strong class="text-blue fs-15">$17.49</strong></td>
                                <td><span class="text-slate-700">$18.99</span></td>
                                <td><span class="text-slate-700">$17.49</span></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2 fs-12 text-slate-600">
                                        <span class="badge bg-emerald-subtle text-emerald"><i class="las la-check me-1"></i>Privacy</span>
                                        <span class="badge bg-blue-subtle text-blue"><i class="las la-check me-1"></i>DNS</span>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <a href="https://my.cloudflox.com/cart.php?a=add&domain=register&query=.org" class="btn-tld-register">
                                        <span>Register</span> <i class="las la-arrow-right"></i>
                                    </a>
                                </td>
                            </tr>

                            <!-- .cloud -->
                            <tr data-cat="tech">
                                <td>
                                    <div class="tld-ext-pill">
                                        <span>.cloud</span>
                                        <span class="tld-tag tld-tag-deal">Hot Deal</span>
                                    </div>
                                </td>
                                <td><span class="text-muted fs-13">Cloud &amp; DevOps</span></td>
                                <td><strong class="text-orange fs-15">$9.99</strong></td>
                                <td><span class="text-slate-700">$21.99</span></td>
                                <td><span class="text-slate-700">$9.99</span></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2 fs-12 text-slate-600">
                                        <span class="badge bg-emerald-subtle text-emerald"><i class="las la-check me-1"></i>Privacy</span>
                                        <span class="badge bg-blue-subtle text-blue"><i class="las la-check me-1"></i>DNS</span>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <a href="https://my.cloudflox.com/cart.php?a=add&domain=register&query=.cloud" class="btn-tld-register">
                                        <span>Register</span> <i class="las la-arrow-right"></i>
                                    </a>
                                </td>
                            </tr>

                            <!-- .io -->
                            <tr data-cat="tech business">
                                <td>
                                    <div class="tld-ext-pill">
                                        <span>.io</span>
                                        <span class="tld-tag tld-tag-popular">Tech Favorite</span>
                                    </div>
                                </td>
                                <td><span class="text-muted fs-13">Startups &amp; Developer APIs</span></td>
                                <td><strong class="text-blue fs-15">$39.99</strong></td>
                                <td><span class="text-slate-700">$44.99</span></td>
                                <td><span class="text-slate-700">$39.99</span></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2 fs-12 text-slate-600">
                                        <span class="badge bg-emerald-subtle text-emerald"><i class="las la-check me-1"></i>Privacy</span>
                                        <span class="badge bg-blue-subtle text-blue"><i class="las la-check me-1"></i>DNS</span>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <a href="https://my.cloudflox.com/cart.php?a=add&domain=register&query=.io" class="btn-tld-register">
                                        <span>Register</span> <i class="las la-arrow-right"></i>
                                    </a>
                                </td>
                            </tr>

                            <!-- .co -->
                            <tr data-cat="business popular">
                                <td>
                                    <div class="tld-ext-pill">
                                        <span>.co</span>
                                        <span class="tld-tag tld-tag-popular">Business</span>
                                    </div>
                                </td>
                                <td><span class="text-muted fs-13">Company / Innovation</span></td>
                                <td><strong class="text-blue fs-15">$24.99</strong></td>
                                <td><span class="text-slate-700">$29.99</span></td>
                                <td><span class="text-slate-700">$24.99</span></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2 fs-12 text-slate-600">
                                        <span class="badge bg-emerald-subtle text-emerald"><i class="las la-check me-1"></i>Privacy</span>
                                        <span class="badge bg-blue-subtle text-blue"><i class="las la-check me-1"></i>DNS</span>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <a href="https://my.cloudflox.com/cart.php?a=add&domain=register&query=.co" class="btn-tld-register">
                                        <span>Register</span> <i class="las la-arrow-right"></i>
                                    </a>
                                </td>
                            </tr>

                            <!-- .info -->
                            <tr data-cat="popular">
                                <td>
                                    <div class="tld-ext-pill">
                                        <span>.info</span>
                                    </div>
                                </td>
                                <td><span class="text-muted fs-13">Information &amp; Media</span></td>
                                <td><strong class="text-blue fs-15">$15.99</strong></td>
                                <td><span class="text-slate-700">$18.99</span></td>
                                <td><span class="text-slate-700">$15.99</span></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2 fs-12 text-slate-600">
                                        <span class="badge bg-emerald-subtle text-emerald"><i class="las la-check me-1"></i>Privacy</span>
                                        <span class="badge bg-blue-subtle text-blue"><i class="las la-check me-1"></i>DNS</span>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <a href="https://my.cloudflox.com/cart.php?a=add&domain=register&query=.info" class="btn-tld-register">
                                        <span>Register</span> <i class="las la-arrow-right"></i>
                                    </a>
                                </td>
                            </tr>

                            <!-- .store -->
                            <tr data-cat="business">
                                <td>
                                    <div class="tld-ext-pill">
                                        <span>.store</span>
                                        <span class="tld-tag tld-tag-deal">eCommerce</span>
                                    </div>
                                </td>
                                <td><span class="text-muted fs-13">Online Shops &amp; Retail</span></td>
                                <td><strong class="text-orange fs-15">$4.99</strong></td>
                                <td><span class="text-slate-700">$34.99</span></td>
                                <td><span class="text-slate-700">$4.99</span></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2 fs-12 text-slate-600">
                                        <span class="badge bg-emerald-subtle text-emerald"><i class="las la-check me-1"></i>Privacy</span>
                                        <span class="badge bg-blue-subtle text-blue"><i class="las la-check me-1"></i>DNS</span>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <a href="https://my.cloudflox.com/cart.php?a=add&domain=register&query=.store" class="btn-tld-register">
                                        <span>Register</span> <i class="las la-arrow-right"></i>
                                    </a>
                                </td>
                            </tr>

                            <!-- .online -->
                            <tr data-cat="business popular">
                                <td>
                                    <div class="tld-ext-pill">
                                        <span>.online</span>
                                        <span class="tld-tag tld-tag-deal">Top Value</span>
                                    </div>
                                </td>
                                <td><span class="text-muted fs-13">General / Web Presence</span></td>
                                <td><strong class="text-orange fs-15">$3.99</strong></td>
                                <td><span class="text-slate-700">$32.99</span></td>
                                <td><span class="text-slate-700">$3.99</span></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2 fs-12 text-slate-600">
                                        <span class="badge bg-emerald-subtle text-emerald"><i class="las la-check me-1"></i>Privacy</span>
                                        <span class="badge bg-blue-subtle text-blue"><i class="las la-check me-1"></i>DNS</span>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <a href="https://my.cloudflox.com/cart.php?a=add&domain=register&query=.online" class="btn-tld-register">
                                        <span>Register</span> <i class="las la-arrow-right"></i>
                                    </a>
                                </td>
                            </tr>

                            <!-- .tech -->
                            <tr data-cat="tech">
                                <td>
                                    <div class="tld-ext-pill">
                                        <span>.tech</span>
                                        <span class="tld-tag tld-tag-popular">Developers</span>
                                    </div>
                                </td>
                                <td><span class="text-muted fs-13">Software &amp; Hardware</span></td>
                                <td><strong class="text-blue fs-15">$7.99</strong></td>
                                <td><span class="text-slate-700">$36.99</span></td>
                                <td><span class="text-slate-700">$7.99</span></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2 fs-12 text-slate-600">
                                        <span class="badge bg-emerald-subtle text-emerald"><i class="las la-check me-1"></i>Privacy</span>
                                        <span class="badge bg-blue-subtle text-blue"><i class="las la-check me-1"></i>DNS</span>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <a href="https://my.cloudflox.com/cart.php?a=add&domain=register&query=.tech" class="btn-tld-register">
                                        <span>Register</span> <i class="las la-arrow-right"></i>
                                    </a>
                                </td>
                            </tr>

                            <!-- .biz -->
                            <tr data-cat="business">
                                <td>
                                    <div class="tld-ext-pill">
                                        <span>.biz</span>
                                    </div>
                                </td>
                                <td><span class="text-muted fs-13">Small Business</span></td>
                                <td><strong class="text-blue fs-15">$14.99</strong></td>
                                <td><span class="text-slate-700">$18.99</span></td>
                                <td><span class="text-slate-700">$14.99</span></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2 fs-12 text-slate-600">
                                        <span class="badge bg-emerald-subtle text-emerald"><i class="las la-check me-1"></i>Privacy</span>
                                        <span class="badge bg-blue-subtle text-blue"><i class="las la-check me-1"></i>DNS</span>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <a href="https://my.cloudflox.com/cart.php?a=add&domain=register&query=.biz" class="btn-tld-register">
                                        <span>Register</span> <i class="las la-arrow-right"></i>
                                    </a>
                                </td>
                            </tr>

                            <!-- .us -->
                            <tr data-cat="regional">
                                <td>
                                    <div class="tld-ext-pill">
                                        <span>.us</span>
                                        <span class="tld-tag tld-tag-regional">United States</span>
                                    </div>
                                </td>
                                <td><span class="text-muted fs-13">Official US Domain</span></td>
                                <td><strong class="text-blue fs-15">$9.99</strong></td>
                                <td><span class="text-slate-700">$12.99</span></td>
                                <td><span class="text-slate-700">$9.99</span></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2 fs-12 text-slate-600">
                                        <span class="badge bg-emerald-subtle text-emerald"><i class="las la-check me-1"></i>Privacy</span>
                                        <span class="badge bg-blue-subtle text-blue"><i class="las la-check me-1"></i>DNS</span>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <a href="https://my.cloudflox.com/cart.php?a=add&domain=register&query=.us" class="btn-tld-register">
                                        <span>Register</span> <i class="las la-arrow-right"></i>
                                    </a>
                                </td>
                            </tr>

                            <!-- .io -->
                            <tr data-cat="tech">
                                <td>
                                    <div class="tld-ext-pill">
                                        <span>.io</span>
                                        <span class="tld-tag tld-tag-popular">Tech &amp; AI</span>
                                    </div>
                                </td>
                                <td><span class="text-muted fs-13">Startups &amp; Developers</span></td>
                                <td><strong class="text-blue fs-15">$38.99</strong></td>
                                <td><span class="text-slate-700">$42.99</span></td>
                                <td><span class="text-slate-700">$38.99</span></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2 fs-12 text-slate-600">
                                        <span class="badge bg-emerald-subtle text-emerald"><i class="las la-check me-1"></i>Privacy</span>
                                        <span class="badge bg-blue-subtle text-blue"><i class="las la-check me-1"></i>DNS</span>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <a href="https://my.cloudflox.com/cart.php?a=add&domain=register&query=.io" class="btn-tld-register">
                                        <span>Register</span> <i class="las la-arrow-right"></i>
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Bottom Table Footer Note -->
                <div class="p-3 bg-light text-center text-muted fs-12 border-top">
                    Need a custom or specialized TLD extension? CloudFlox supports over 500+ global gTLDs and ccTLDs. 
                    <a href="contact" class="text-blue text-decoration-underline fw-semibold ms-1">Contact our Domain Desk</a>.
                </div>
            </div>
        </div>
    </section>

    <!-- =========================================================================
         3. DOMAIN TRANSFER SECTION (Keep Prepaid Time + Free Renewal)
         ========================================================================= -->
    <section class="py-5 bg-white position-relative" id="domainTransfer">
        <div class="container position-relative z-2">
            <div class="row align-items-center g-4">
                <!-- Left: Transfer Illustration -->
                <div class="col-12 col-lg-5 text-center">
                    <div class="position-relative p-3 rounded-4 bg-light border border-slate-200 shadow-sm d-inline-block">
                        <img src="assets/imgs/domain-trancfer.avif" alt="Transfer domain to CloudFlox"
                            class="img-fluid rounded-3" width="460" height="340" loading="lazy">
                        <!-- Floating Verification Pill -->
                        <div class="position-absolute bottom-0 start-50 translate-middle-x mb-n3 px-3 py-2 bg-white rounded-pill border shadow-sm d-flex align-items-center gap-2 fs-12 text-slate-900 fw-bold">
                            <i class="las la-check-circle text-success fs-18"></i>
                            <span>Zero Downtime Migration Guarantee</span>
                        </div>
                    </div>
                </div>

                <!-- Right: Transfer Content & 3 Steps -->
                <div class="col-12 col-lg-7">
                    <span class="badge bg-blue-subtle text-blue fw-bold px-3 py-1 rounded-pill mb-2 border border-blue-subtle">
                        SEAMLESS MIGRATION
                    </span>
                    <h2 class="display-6 fw-extrabold text-slate-900 mb-3">
                        Transfer Your Existing Domain to CloudFlox
                    </h2>
                    <p class="text-slate-600 fs-16 mb-4 lh-base">
                        Already have a domain registered elsewhere? Move it to CloudFlox in minutes. You won't lose a single day of your prepaid time, and supported extensions receive a complimentary 1-year registration extension upon completion!
                    </p>

                    <!-- 3 Process Steps -->
                    <div class="row g-3 mb-4">
                        <div class="col-12 col-md-4">
                            <div class="cf-transfer-step-card">
                                <span class="cf-step-num">1</span>
                                <h4 class="fw-bold fs-15 text-slate-900 mb-2">Unlock &amp; Get Code</h4>
                                <p class="text-slate-600 fs-13 mb-0 lh-base">
                                    Unlock your domain at your current registrar and copy the EPP / Authorization code.
                                </p>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="cf-transfer-step-card">
                                <span class="cf-step-num">2</span>
                                <h4 class="fw-bold fs-15 text-slate-900 mb-2">Submit Order</h4>
                                <p class="text-slate-600 fs-13 mb-0 lh-base">
                                    Enter your domain and EPP key into our secure transfer wizard to start automated transfer.
                                </p>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="cf-transfer-step-card">
                                <span class="cf-step-num">3</span>
                                <h4 class="fw-bold fs-15 text-slate-900 mb-2">Confirm &amp; Relax</h4>
                                <p class="text-slate-600 fs-13 mb-0 lh-base">
                                    Approve the verification email. Your website and email records remain 100% active.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Transfer CTA Buttons -->
                    <div class="d-flex flex-wrap gap-3">
                        <a href="https://my.cloudflox.com/cart.php?a=add&domain=transfer" class="btn btn-primary rounded-3 px-4 py-2 fw-bold fs-15 shadow-sm">
                            <i class="las la-exchange-alt me-1"></i> Start Domain Transfer
                        </a>
                        <a href="contact" class="btn btn-outline-secondary rounded-3 px-4 py-2 fw-semibold fs-15" style="color: #2563EB;">
                            <i class="las la-headset me-1"></i> Need Transfer Assistance?
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- =========================================================================
         4. WHY CLOUDFLOX DOMAINS (Bento Grid)
         ========================================================================= -->
    <section class="py-5 bg-light position-relative">
        <div class="container position-relative z-2">
            <!-- Section Header -->
            <div class="text-center max-w-750 mx-auto mb-4 mb-md-5">
                <span class="badge bg-blue-subtle text-blue fw-bold px-3 py-1 rounded-pill mb-2 border border-blue-subtle">
                    ENTERPRISE ESSENTIALS INCLUDED
                </span>
                <h2 class="display-6 fw-extrabold text-slate-900 mb-2">
                    Why Register Your Domain with CloudFlox?
                </h2>
                <p class="text-slate-600 fs-16 mb-0">
                    Unlike ordinary registrars that charge extra for privacy and basic DNS, CloudFlox bundles enterprise security and management into every domain at no additional cost.
                </p>
            </div>

            <!-- Bento Grid (6 Feature Boxes) -->
            <div class="row g-4">
                <!-- Feature 1: Free WHOIS Privacy -->
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="cf-feature-box h-100 p-4 rounded-4 border bg-white shadow-sm hover-shadow">
                        <div class="cf-card-icon-bubble bg-blue-subtle text-blue mb-3">
                            <i class="las la-user-shield"></i>
                        </div>
                        <h4 class="fw-bold text-slate-900 fs-18 mb-2">Free WHOIS Privacy Protection</h4>
                        <p class="text-slate-600 fs-14 mb-0 lh-base">
                            Keep your personal address, phone number, and email shielded from public databases, identity thieves, spammers, and telemarketers forever.
                        </p>
                    </div>
                </div>

                <!-- Feature 2: Anycast DNS Management -->
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="cf-feature-box h-100 p-4 rounded-4 border bg-white shadow-sm hover-shadow">
                        <div class="cf-card-icon-bubble bg-purple-subtle text-purple mb-3">
                            <i class="las la-globe-americas"></i>
                        </div>
                        <h4 class="fw-bold text-slate-900 fs-18 mb-2">High-Speed Anycast DNS</h4>
                        <p class="text-slate-600 fs-14 mb-0 lh-base">
                            Our globally distributed Anycast nameserver network resolves DNS queries at sub-millisecond speeds, reducing initial TTFB for visitors worldwide.
                        </p>
                    </div>
                </div>

                <!-- Feature 3: Domain Theft Protection Lock -->
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="cf-feature-box h-100 p-4 rounded-4 border bg-white shadow-sm hover-shadow">
                        <div class="cf-card-icon-bubble bg-emerald-subtle text-emerald mb-3">
                            <i class="las la-lock"></i>
                        </div>
                        <h4 class="fw-bold text-slate-900 fs-18 mb-2">Domain Theft Protection Lock</h4>
                        <p class="text-slate-600 fs-14 mb-0 lh-base">
                            Lock your domain securely to prevent unauthorized transfers, accidental hijacking, or DNS tampering without two-factor administrative sign-off.
                        </p>
                    </div>
                </div>

                <!-- Feature 4: Auto Renewal Alerts -->
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="cf-feature-box h-100 p-4 rounded-4 border bg-white shadow-sm hover-shadow">
                        <div class="cf-card-icon-bubble bg-orange-subtle text-orange mb-3">
                            <i class="las la-history"></i>
                        </div>
                        <h4 class="fw-bold text-slate-900 fs-18 mb-2">Automated Renewal Protection</h4>
                        <p class="text-slate-600 fs-14 mb-0 lh-base">
                            Never lose a mission-critical domain asset to an oversight. Smart auto-renewal and multi-channel email alerts keep your domains securely active.
                        </p>
                    </div>
                </div>

                <!-- Feature 5: Comprehensive DNS Suite -->
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="cf-feature-box h-100 p-4 rounded-4 border bg-white shadow-sm hover-shadow">
                        <div class="cf-card-icon-bubble bg-cyan-subtle text-cyan mb-3">
                            <i class="las la-sliders-h"></i>
                        </div>
                        <h4 class="fw-bold text-slate-900 fs-18 mb-2">Full DNS Records &amp; Forwarding</h4>
                        <p class="text-slate-600 fs-14 mb-0 lh-base">
                            Effortlessly manage A, AAAA, CNAME, MX, TXT, and SRV records with fast TTL, alongside seamless URL redirection and email forwarding rules.
                        </p>
                    </div>
                </div>

                <!-- Feature 6: 1-Click Hosting Integration -->
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="cf-feature-box h-100 p-4 rounded-4 border bg-white shadow-sm hover-shadow">
                        <div class="cf-card-icon-bubble bg-blue-subtle text-blue mb-3">
                            <i class="las la-server"></i>
                        </div>
                        <h4 class="fw-bold text-slate-900 fs-18 mb-2">1-Click Hosting Integration</h4>
                        <p class="text-slate-600 fs-14 mb-0 lh-base">
                            Connect your domain directly to CloudFlox Web Hosting, cPanel accounts, or Dedicated Cloud instances instantly with automatic DNS mapping.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- =========================================================================
         5. DOMAIN MANAGEMENT DASHBOARD SHOWCASE
         ========================================================================= -->
    <section class="py-5 bg-white position-relative">
        <div class="container position-relative z-2">
            <div class="row align-items-center g-4">
                <!-- Left: Content & Bullet Highlights -->
                <div class="col-12 col-lg-6">
                    <span class="badge bg-blue-subtle text-blue fw-bold px-3 py-1 rounded-pill mb-2 border border-blue-subtle">
                        INTUITIVE CONTROL PANEL
                    </span>
                    <h2 class="display-6 fw-extrabold text-slate-900 mb-3">
                        Centralized Management for Your Entire Domain Portfolio
                    </h2>
                    <p class="text-slate-600 fs-16 mb-4 lh-base">
                        Whether you hold a single brand domain or manage hundreds of client addresses, our streamlined client dashboard provides complete control without technical clutter.
                    </p>

                    <!-- Feature Bullet List -->
                    <ul class="list-unstyled d-flex flex-column gap-3 mb-4">
                        <li class="d-flex align-items-start gap-3">
                            <div class="w-4 h-4 bg-primary flex-shrink-0 rounded-circle fs-11 text-white d-flex align-items-center justify-content-center mt-1">
                                <i class="las la-check"></i>
                            </div>
                            <div>
                                <strong class="text-slate-900 d-block fs-15">Real-Time DNS Zone Editor</strong>
                                <span class="text-slate-600 fs-14">Update A, CNAME, MX, and TXT records instantly with custom TTL settings.</span>
                            </div>
                        </li>
                        <li class="d-flex align-items-start gap-3">
                            <div class="w-4 h-4 bg-primary flex-shrink-0 rounded-circle fs-11 text-white d-flex align-items-center justify-content-center mt-1">
                                <i class="las la-check"></i>
                            </div>
                            <div>
                                <strong class="text-slate-900 d-block fs-15">Custom Nameservers &amp; Glue Records</strong>
                                <span class="text-slate-600 fs-14">Configure private brand nameservers (ns1.yourbrand.com) linked to dedicated server IPs.</span>
                            </div>
                        </li>
                        <li class="d-flex align-items-start gap-3">
                            <div class="w-4 h-4 bg-primary flex-shrink-0 rounded-circle fs-11 text-white d-flex align-items-center justify-content-center mt-1">
                                <i class="las la-check"></i>
                            </div>
                            <div>
                                <strong class="text-slate-900 d-block fs-15">Registrant &amp; Contact Information Editor</strong>
                                <span class="text-slate-600 fs-14">Update registrant, administrative, technical, and billing details anytime with ICANN compliance.</span>
                            </div>
                        </li>
                        <li class="d-flex align-items-start gap-3">
                            <div class="w-4 h-4 bg-primary flex-shrink-0 rounded-circle fs-11 text-white d-flex align-items-center justify-content-center mt-1">
                                <i class="las la-check"></i>
                            </div>
                            <div>
                                <strong class="text-slate-900 d-block fs-15">Bulk Renewal &amp; Portfolio Tools</strong>
                                <span class="text-slate-600 fs-14">Renew multiple domains simultaneously with unified invoicing and consolidated expirations.</span>
                            </div>
                        </li>
                    </ul>

                    <div class="pt-2">
                        <a href="https://my.cloudflox.com/clientarea.php?action=domains" class="btn btn-primary rounded-3 px-4 py-2 fw-bold fs-15 shadow-sm">
                            <i class="las la-external-link-alt me-1"></i> Access Domain Manager
                        </a>
                    </div>
                </div>

                <!-- Right: Dashboard Image Showcase -->
                <div class="col-12 col-lg-6 text-center">
                    <div class="position-relative p-3 rounded-4 bg-light border border-slate-200 shadow-sm d-inline-block">
                        <img src="assets/imgs/domain-management.avif" alt="Manage domains with CloudFlox"
                            class="img-fluid rounded-3" width="520" height="380" loading="lazy">
                        <!-- Floating DNS Status Badge -->
                        <div class="position-absolute bottom-0 end-0 mb-3 me-3 px-3 py-2 bg-white rounded-3 border shadow-sm d-flex align-items-center gap-2 fs-12 text-slate-900 fw-bold">
                            <span class="cf-pulse-dot-green"></span>
                            <span>Anycast DNS Propagation: &lt; 5 mins</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- =========================================================================
         6. CALLOUT BANNER (Pair with High-Speed NVMe Hosting)
         ========================================================================= -->
    <section class="py-4 py-md-5">
        <div class="container">
            <div class="cf-migration-card text-white p-4 p-md-5 rounded-4 position-relative overflow-hidden shadow-lg">
                <div class="row align-items-center g-4 position-relative z-2">
                    <div class="col-12 col-lg-8">
                        <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill bg-white bg-opacity-10 text-white fs-13 mb-3 border border-white border-opacity-20">
                            <i class="las la-bolt text-warning"></i>
                            <span>PERFECT HOSTING COMPANION</span>
                        </div>
                        <h3 class="display-6 fw-bold mb-3" style="color: #ffffff !important;">
                            Pair Your Domain with High-Speed NVMe Web Hosting.
                        </h3>
                        <p class="text-slate-300 fs-16 mb-0 lh-base max-w-750">
                            Deploy your website on blazing-fast Pure Gen4 NVMe storage with cPanel, free automated SSL certificates, daily backups, and 99.99% uptime SLA starting at just <strong>$3.00/month</strong>.
                        </p>
                    </div>
                    <div class="col-12 col-lg-4 text-lg-end">
                        <div class="d-flex flex-column flex-sm-row flex-lg-column gap-2 justify-content-lg-end">
                            <a href="budget-hosting" class="cf-btn-business-glow text-center">
                                <i class="las la-rocket me-1"></i> Explore Web Hosting
                            </a>
                            <a href="premium-hosting" class="btn btn-outline-light rounded-3 py-2 px-3 fs-14 fw-semibold text-center">
                                View Managed Cloud
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- =========================================================================
         7. FREQUENTLY ASKED QUESTIONS (FAQ Accordion)
         ========================================================================= -->
    <section class="py-5 bg-white position-relative">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-lg-8 text-center mb-4 mb-md-5">
                    <span class="badge bg-blue-subtle text-blue fw-bold px-3 py-1 rounded-pill mb-2 border border-blue-subtle">
                        FREQUENTLY ASKED QUESTIONS
                    </span>
                    <h2 class="display-6 fw-extrabold text-slate-900 mb-2">
                        Everything You Need to Know About Domains
                    </h2>
                    <p class="text-slate-600 fs-16 mb-0">
                        Clear, transparent answers to common domain registration, transfer, and DNS questions.
                    </p>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-12 col-xl-9">
                    <div class="accordion cf-modern-accordion d-flex flex-column gap-3" id="domainFaq">

                        <!-- FAQ 1 -->
                        <div class="accordion-item rounded-3 border">
                            <h3 class="accordion-header" id="headingD1">
                                <button class="accordion-button bg-white text-slate-900 fw-bold fs-16" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#collapseD1" aria-expanded="true"
                                    aria-controls="collapseD1">
                                    What is a domain name and why do I need one?
                                </button>
                            </h3>
                            <div id="collapseD1" class="accordion-collapse collapse show" aria-labelledby="headingD1"
                                data-bs-parent="#domainFaq">
                                <div class="accordion-body text-slate-600 fs-15 lh-base pt-0">
                                    A domain name is your unique digital address on the internet (e.g. <code>yourbrand.com</code>). Instead of forcing visitors to remember technical IP addresses, your domain routes web browsers and incoming emails directly to your web hosting server. Securing your domain name establishes brand authority and protects your digital identity online.
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 2 -->
                        <div class="accordion-item rounded-3 border">
                            <h3 class="accordion-header" id="headingD2">
                                <button class="accordion-button collapsed bg-white text-slate-900 fw-bold fs-16" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#collapseD2" aria-expanded="false"
                                    aria-controls="collapseD2">
                                    How fast is domain registration activated after purchase?
                                </button>
                            </h3>
                            <div id="collapseD2" class="accordion-collapse collapse" aria-labelledby="headingD2"
                                data-bs-parent="#domainFaq">
                                <div class="accordion-body text-slate-600 fs-15 lh-base pt-0">
                                    Domain registration is completely automated and instant. Once checkout is completed, the domain is immediately registered with the central registry and locked under your CloudFlox account. You can immediately assign nameservers, configure DNS records, or link it to your web hosting.
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 3 -->
                        <div class="accordion-item rounded-3 border">
                            <h3 class="accordion-header" id="headingD3">
                                <button class="accordion-button collapsed bg-white text-slate-900 fw-bold fs-16" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#collapseD3" aria-expanded="false"
                                    aria-controls="collapseD3">
                                    Is WHOIS privacy protection included for free?
                                </button>
                            </h3>
                            <div id="collapseD3" class="accordion-collapse collapse" aria-labelledby="headingD3"
                                data-bs-parent="#domainFaq">
                                <div class="accordion-body text-slate-600 fs-15 lh-base pt-0">
                                    Yes! For all supported gTLDs (such as <code>.com</code>, <code>.net</code>, <code>.org</code>, <code>.cloud</code>, <code>.io</code>), CloudFlox provides 100% free WHOIS privacy protection. This masks your personal phone number, home address, and personal email address with proxy contact details to stop marketing spam, scammers, and identity scraping.
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 4 -->
                        <div class="accordion-item rounded-3 border">
                            <h3 class="accordion-header" id="headingD4">
                                <button class="accordion-button collapsed bg-white text-slate-900 fw-bold fs-16" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#collapseD4" aria-expanded="false"
                                    aria-controls="collapseD4">
                                    Will I lose remaining registration time if I transfer my domain to CloudFlox?
                                </button>
                            </h3>
                            <div id="collapseD4" class="accordion-collapse collapse" aria-labelledby="headingD4"
                                data-bs-parent="#domainFaq">
                                <div class="accordion-body text-slate-600 fs-15 lh-base pt-0">
                                    No, you will never lose any paid time! In accordance with standard ICANN transfer rules, all remaining months on your current domain registration are carried over entirely, and supported extensions will have an additional full 1-year registration period added upon transfer completion.
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 5 -->
                        <div class="accordion-item rounded-3 border">
                            <h3 class="accordion-header" id="headingD5">
                                <button class="accordion-button collapsed bg-white text-slate-900 fw-bold fs-16" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#collapseD5" aria-expanded="false"
                                    aria-controls="collapseD5">
                                    How long do DNS records and nameserver changes take to propagate?
                                </button>
                            </h3>
                            <div id="collapseD5" class="accordion-collapse collapse" aria-labelledby="headingD5"
                                data-bs-parent="#domainFaq">
                                <div class="accordion-body text-slate-600 fs-15 lh-base pt-0">
                                    Because CloudFlox utilizes high-performance Anycast DNS nameservers, internal DNS updates take effect in seconds. Global propagation across third-party ISPs generally finishes within 15 to 45 minutes, with full worldwide TTL convergence completing within a few hours.
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 6 -->
                        <div class="accordion-item rounded-3 border">
                            <h3 class="accordion-header" id="headingD6">
                                <button class="accordion-button collapsed bg-white text-slate-900 fw-bold fs-16" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#collapseD6" aria-expanded="false"
                                    aria-controls="collapseD6">
                                    Can I point my CloudFlox domain to external services like Shopify, GitHub, or AWS?
                                </button>
                            </h3>
                            <div id="collapseD6" class="accordion-collapse collapse" aria-labelledby="headingD6"
                                data-bs-parent="#domainFaq">
                                <div class="accordion-body text-slate-600 fs-15 lh-base pt-0">
                                    Absolutely. You maintain 100% full administrative control of your DNS zone. You can point your root domain and subdomains to Shopify, Google Workspace, Microsoft 365, GitHub Pages, AWS, Vercel, or any other third-party cloud infrastructure using simple CNAME and A records.
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- =========== Footer Area =========== -->
    <?php include 'include/footer.inc.php'; ?>
    <!-- =========== Footer Area =========== -->

    <!-- =========== JS Area =========== -->
    <?php include 'include/js.inc.php'; ?>
    <!-- =========== JS Area =========== -->

    <!-- Domain Search & TLD Table Interactive Script -->
    <script>
    function setDomainExt(ext, el) {
        const input = document.getElementById("domainInput");
        if (!input) return;

        let val = input.value.trim();
        if (val === '') {
            input.value = "mybrand" + ext;
        } else {
            val = val.replace(/\.[a-zA-Z0-9\.]+$/, "");
            input.value = val + ext;
        }
        if (el) {
            document.querySelectorAll(".tld-card").forEach(function (c) {
                c.classList.remove("active-tld");
            });
            el.classList.add("active-tld");
        }
        input.focus();
    }

    // TLD Table Filtering
    document.addEventListener("DOMContentLoaded", function () {
        const filterBtns = document.querySelectorAll(".cf-tld-filter-btn");
        const searchInput = document.getElementById("tldSearchInput");
        const rows = document.querySelectorAll("#tldTable tbody tr");

        function filterRows() {
            const activeBtn = document.querySelector(".cf-tld-filter-btn.active");
            const cat = activeBtn ? activeBtn.getAttribute("data-filter") : "all";
            const searchVal = searchInput ? searchInput.value.toLowerCase().trim() : "";

            rows.forEach(function (row) {
                const rowCat = row.getAttribute("data-cat") || "";
                const rowText = row.innerText.toLowerCase();

                const matchesCat = (cat === "all") || rowCat.split(" ").includes(cat);
                const matchesSearch = (searchVal === "") || rowText.includes(searchVal);

                if (matchesCat && matchesSearch) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            });
        }

        filterBtns.forEach(function (btn) {
            btn.addEventListener("click", function () {
                filterBtns.forEach(function (b) { b.classList.remove("active"); });
                this.classList.add("active");
                filterRows();
            });
        });

        if (searchInput) {
            searchInput.addEventListener("input", filterRows);
        }
    });
    </script>
</body>

</html>