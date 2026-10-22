<?php include 'include/url.inc.php'; ?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">

<head>
    <!-- Basic Meta -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- SEO Title & Meta Description -->
    <title>Enterprise Dedicated Servers — High Performance Bare Metal Hosting | CloudFlox</title>
    <meta name="description"
        content="Deploy enterprise-grade CloudFlox Dedicated Servers with Intel Xeon & AMD Ryzen bare-metal processors, NVMe storage arrays, 1Gbps unmetered bandwidth, and IPMI/KVM root control from $79.00/mo.">
    <meta name="keywords"
        content="dedicated servers, bare metal hosting, intel xeon dedicated server, amd ryzen server, cloudflox hosting, high performance bare metal, unmanaged dedicated hosting, enterprise server hosting">
    <meta name="author" content="CloudFlox">
    <link rel="canonical" href="https://cloudflox.com/dedicated-servers">

    <!-- Open Graph / Social Meta -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="Enterprise Dedicated Servers — CloudFlox Bare Metal Hosting">
    <meta property="og:description"
        content="Maximum compute power, zero hypervisor overhead, and complete root control with Intel Xeon & AMD Ryzen dedicated servers starting at $79.00/mo.">
    <meta property="og:url" content="https://cloudflox.com/dedicated-servers">
    <meta property="og:image" content="https://cloudflox.com/assets/imgs/banners/cloudflox-dedicated-server-infrastructure.jpg">
    <meta property="og:site_name" content="CloudFlox">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="CloudFlox Enterprise Bare Metal Dedicated Servers">
    <meta name="twitter:description"
        content="Deploy 100% single-tenant Intel Xeon & AMD Ryzen bare metal servers with Gen4 NVMe, 1Gbps unmetered port, IPMI/iDRAC KVM, and 99.99% hardware SLA.">
    <meta name="twitter:image" content="https://cloudflox.com/assets/imgs/banners/cloudflox-dedicated-server-infrastructure.jpg">

    <!-- Structured Data (JSON-LD) for SEO -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@graph": [
        {
          "@type": "Product",
          "name": "CloudFlox Enterprise Dedicated Servers",
          "description": "High-performance bare-metal dedicated servers featuring Intel Xeon and AMD Ryzen hardware, enterprise NVMe storage arrays, unmetered 1Gbps bandwidth, and full IPMI out-of-band management.",
          "brand": {
            "@type": "Brand",
            "name": "CloudFlox"
          },
          "offers": {
            "@type": "AggregateOffer",
            "priceCurrency": "USD",
            "lowPrice": "79.00",
            "highPrice": "499.00",
            "offerCount": "20"
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
              "name": "Servers",
              "item": "https://cloudflox.com/dedicated-servers"
            },
            {
              "@type": "ListItem",
              "position": 3,
              "name": "Dedicated Servers",
              "item": "https://cloudflox.com/dedicated-servers"
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

<body class="pure-white-theme cf-dedicated-page">

    <!-- =========== Header Area =========== -->
    <?php include 'include/header.inc.php'; ?>
    <!-- =========== Header Area =========== -->

    <!-- =========================================================================
         1. HERO SECTION (Enterprise Bare Metal Dedicated Servers)
         ========================================================================= -->
    <section class="cf-dedicated-hero position-relative">
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
                    <li class="breadcrumb-item text-muted">Servers</li>
                    <li class="breadcrumb-item active text-crimson fw-semibold" aria-current="page">Dedicated Servers</li>
                </ol>
            </nav>

            <div class="row align-items-center g-4 g-xl-5 pb-lg-3">
                <!-- Left Column: Value Proposition -->
                <div class="col-12 col-lg-7">
                    <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill bg-crimson-subtle text-crimson fw-bold fs-13 mb-3 border border-crimson-subtle">
                        <span class="cf-pulse-dot-crimson"></span>
                        <span>100% SINGLE-TENANT BARE METAL HARDWARE</span>
                    </div>

                    <h1 class="cf-dedicated-hero-title fw-extrabold text-slate-900 tracking-tight mb-3">
                        Enterprise-Grade Bare Metal <br class="d-none d-sm-inline">
                        <span class="text-gradient-dedicated">Dedicated Servers</span>
                    </h1>

                    <p class="cf-dedicated-hero-lead text-slate-600 mb-4 lh-base">
                        Experience uncompromised physical computing power with CloudFlox bare metal dedicated servers. Featuring the latest <strong>Intel Xeon Scalable</strong> &amp; <strong>AMD Ryzen High-Clock</strong> processors, enterprise <strong>Gen4 NVMe storage arrays</strong>, <strong>1Gbps unmetered bandwidth</strong>, and complete out-of-band <strong>IPMI / iDRAC root control</strong> starting at <strong>$79.00/month</strong>.
                    </p>

                    <!-- Trust Chips -->
                    <div class="d-flex flex-wrap gap-2 mb-4 pb-2">
                        <span class="cf-feature-chip">
                            <i class="las la-server text-danger"></i> 100% Single-Tenant Bare Metal
                        </span>
                        <span class="cf-feature-chip">
                            <i class="las la-microchip text-primary"></i> Intel Xeon &amp; AMD Ryzen OC
                        </span>
                        <span class="cf-feature-chip">
                            <i class="las la-bolt text-warning"></i> Enterprise NVMe RAID Arrays
                        </span>
                        <span class="cf-feature-chip">
                            <i class="las la-network-wired text-info"></i> 1Gbps Unmetered Backbone
                        </span>
                        <span class="cf-feature-chip">
                            <i class="las la-terminal text-success"></i> Out-of-band IPMI / KVM Root
                        </span>
                        <span class="cf-feature-chip">
                            <i class="las la-shield-alt text-danger"></i> Multi-Tbps DDoS Scrubbing
                        </span>
                        <!-- <span class="cf-feature-chip">
                            <i class="las la-check-circle text-success"></i> 99.99% Hardware SLA
                        </span> -->
                    </div>

                    <!-- Hero Action Buttons -->
                    <div class="d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center gap-3 cf-hero-btn-group">
                        <a href="#serverPlans" class="cf-btn-dedicated-glow text-center">
                            <span>Explore Bare Metal Servers</span>
                            <i class="las la-arrow-down ms-1"></i>
                        </a>
                        <a href="#serverComparison" class="cf-btn-secondary-light text-center">
                            <i class="las la-sliders-h me-1"></i>
                            <span>Compare Hardware Specs</span>
                        </a>
                    </div>
                </div>

                <!-- Right Column: Visual Hero Infrastructure Showcase -->
                <div class="col-12 col-lg-5">
                    <div class="cf-budget-hero-visual position-relative text-center">
                        <div class="cf-hero-img-wrap position-relative mx-auto">
                            <picture>
                                <source srcset="assets/imgs/banners/cloudflox-dedicated-server-infrastructure.webp" type="image/webp">
                                <img src="assets/imgs/banners/cloudflox-dedicated-server-infrastructure.jpg"
                                     alt="CloudFlox Enterprise Bare Metal Dedicated Server Infrastructure with Intel Xeon and AMD Ryzen"
                                     class="cf-hero-illustration img-fluid"
                                     width="900"
                                     height="675"
                                     fetchpriority="high"
                                     loading="eager">
                            </picture>

                            <!-- Floating Price Tag (Top Left) -->
                            <div class="cf-hero-float-chip float-chip-top shadow-sm">
                                <span class="cf-pulse-dot-crimson"></span>
                                <div class="text-start">
                                    <span class="d-block cf-chip-sub">Bare Metal From</span>
                                    <strong class="cf-chip-val text-crimson">$79.00<small class="text-muted fw-normal">/mo</small></strong>
                                </div>
                                <span class="badge bg-danger-subtle text-danger fw-bold px-2 py-1 rounded-pill ms-2 fs-11">
                                    100% Dedicated
                                </span>
                            </div>

                            <!-- Floating Specs Tag (Bottom Right) -->
                            <div class="cf-hero-float-chip float-chip-bottom shadow-sm">
                                <div class="cf-chip-icon bg-crimson-subtle text-crimson">
                                    <i class="las la-network-wired"></i>
                                </div>
                                <div class="text-start">
                                    <span class="d-block cf-chip-sub">1Gbps Unmetered Port</span>
                                    <strong class="cf-chip-val text-slate-900">Enterprise NVMe RAID</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- =========================================================================
         2. PRICING & BARE METAL LINEUP SECTION (Intel Xeon & AMD Ryzen)
         ========================================================================= -->
    <section class="modern-pricing-section position-relative py-5" id="serverPlans">
        <!-- Ambient Decorative Pattern Backgrounds -->
        <div class="pricing-bg-shape pricing-shape-left" aria-hidden="true">
            <img src="assets/imgs/shape/pattern-2.svg?v=2" alt="" width="370" height="590" loading="eager" decoding="async">
        </div>
        <div class="pricing-bg-shape pricing-shape-right" aria-hidden="true">
            <img src="assets/imgs/shape/pattern-3.svg?v=2" alt="" width="370" height="590" loading="eager" decoding="async">
        </div>

        <div class="container position-relative z-2">
            <!-- Section Header -->
            <div class="text-center max-w-750 mx-auto mb-4 mb-md-5">
                <span class="badge bg-crimson-subtle text-crimson fw-bold px-3 py-1 rounded-pill mb-2">
                    ENTERPRISE BARE METAL LINEUP
                </span>
                <h2 class="display-6 fw-extrabold text-slate-900 mb-2">
                    Pick Your Dedicated Hardware Infrastructure
                </h2>
                <p class="text-slate-600 fs-16 mb-0">
                    Zero virtualization layer, zero noisy neighbors. Choose between Intel Xeon multi-core stability or AMD Ryzen high-frequency single-core performance.
                </p>
            </div>

            <!-- Architecture Switcher (Intel Xeon vs AMD Ryzen) -->
            <div class="cf-arch-switch-container mb-4 mb-md-5">
                <div class="cf-arch-switch-pill" role="tablist">
                    <button type="button" class="cf-arch-btn active intel-active" id="btnArchIntel" role="tab" aria-selected="true" aria-controls="paneIntel">
                        <i class="las la-microchip text-primary"></i>
                        <span>Intel Xeon Scalable</span>
                        <span class="cf-arch-badge-count">10 Plans</span>
                    </button>
                    <button type="button" class="cf-arch-btn" id="btnArchAmd" role="tab" aria-selected="false" aria-controls="paneAmd">
                        <i class="las la-bolt text-danger"></i>
                        <span>AMD Ryzen High-Clock</span>
                        <span class="cf-arch-badge-count">10 Plans</span>
                    </button>
                </div>
            </div>

            <!-- Tab Content Wrapper -->
            <div class="tab-content" id="serverArchitectureTabs">

                <!-- =============================================================
                     TAB 1: INTEL XEON SCALABLE LINEUP
                     ============================================================= -->
                <div class="tab-pane fade show active" id="paneIntel" role="tabpanel" aria-labelledby="btnArchIntel">

                    <!-- Featured 4 Showcase Cards for Intel -->
                    <div class="row g-4 mb-5 justify-content-center">

                        <!-- Card 1: Intel E3 Basic ($79) -->
                        <div class="col-12 col-md-6 col-xl-3">
                            <div class="modern-pricing-card h-100 d-flex flex-column">
                                <div class="plan-header mb-3">
                                    <span class="plan-kicker text-muted fw-semibold fs-12 text-uppercase">Starter Bare Metal</span>
                                    <h3 class="plan-name fw-extrabold text-slate-900 fs-20 mb-1">Intel E3 Basic</h3>
                                    <p class="plan-tagline text-muted fs-13 mb-0">Xeon E3-1230 v6 for web applications</p>
                                </div>
                                <div class="plan-price-wrap mb-4">
                                    <div class="d-flex align-items-baseline gap-1">
                                        <del class="text-muted fs-14 fw-medium">$95</del>
                                        <span class="price-currency fs-20 fw-bold text-slate-900">$</span>
                                        <span class="price-val fs-36 fw-extrabold text-slate-900">79</span>
                                        <span class="price-period text-muted fs-14">/mo</span>
                                    </div>
                                    <span class="billing-note text-slate-500 fs-12">Port 1Gbps &bull; Unmetered Traffic</span>
                                </div>
                                <div class="plan-features flex-grow-1 mb-4">
                                    <ul class="list-unstyled p-0 m-0 d-flex flex-column gap-2 fs-13 text-slate-700">
                                        <li class="d-flex align-items-center gap-2">
                                            <i class="las la-microchip text-primary fs-16"></i>
                                            <span><strong>4 Cores / 8 Threads</strong> (Xeon E3-1230)</span>
                                        </li>
                                        <li class="d-flex align-items-center gap-2">
                                            <i class="las la-memory text-primary fs-16"></i>
                                            <span><strong>32 GB DDR4</strong> RAM</span>
                                        </li>
                                        <li class="d-flex align-items-center gap-2">
                                            <i class="las la-hdd text-primary fs-16"></i>
                                            <span><strong>2 TB SSD</strong> Storage</span>
                                        </li>
                                        <li class="d-flex align-items-center gap-2">
                                            <i class="las la-network-wired text-primary fs-16"></i>
                                            <span><strong>1Gbps Port</strong> (Unmetered)</span>
                                        </li>
                                        <li class="d-flex align-items-center gap-2">
                                            <i class="las la-terminal text-success fs-16"></i>
                                            <span><strong>1 IPv4 &amp; IPMI / KVM</strong> Root Access</span>
                                        </li>
                                    </ul>
                                </div>
                                <div class="plan-cta mt-auto">
                                    <a href="https://my.cloudflox.com/index.php?rp=/store/dedicated-servers" class="cf-btn-plan-secondary">
                                        <span>Order E3 Basic</span>
                                        <i class="las la-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Card 2: Intel E5 Advanced ($169) -->
                        <div class="col-12 col-md-6 col-xl-3">
                            <div class="modern-pricing-card h-100 d-flex flex-column">
                                <div class="plan-header mb-3">
                                    <span class="plan-kicker text-muted fw-semibold fs-12 text-uppercase">High-Concurrency</span>
                                    <h3 class="plan-name fw-extrabold text-slate-900 fs-20 mb-1">Intel E5 Advanced</h3>
                                    <p class="plan-tagline text-muted fs-13 mb-0">Xeon E5-2680 v4 for multi-tenant systems</p>
                                </div>
                                <div class="plan-price-wrap mb-4">
                                    <div class="d-flex align-items-baseline gap-1">
                                        <del class="text-muted fs-14 fw-medium">$199</del>
                                        <span class="price-currency fs-20 fw-bold text-slate-900">$</span>
                                        <span class="price-val fs-36 fw-extrabold text-slate-900">169</span>
                                        <span class="price-period text-muted fs-14">/mo</span>
                                    </div>
                                    <span class="billing-note text-slate-500 fs-12">Port 1Gbps &bull; Unmetered Traffic</span>
                                </div>
                                <div class="plan-features flex-grow-1 mb-4">
                                    <ul class="list-unstyled p-0 m-0 d-flex flex-column gap-2 fs-13 text-slate-700">
                                        <li class="d-flex align-items-center gap-2">
                                            <i class="las la-microchip text-primary fs-16"></i>
                                            <span><strong>14 Cores / 28 Threads</strong> (Xeon E5)</span>
                                        </li>
                                        <li class="d-flex align-items-center gap-2">
                                            <i class="las la-memory text-primary fs-16"></i>
                                            <span><strong>128 GB DDR4</strong> ECC RAM</span>
                                        </li>
                                        <li class="d-flex align-items-center gap-2">
                                            <i class="las la-hdd text-primary fs-16"></i>
                                            <span><strong>2 TB Enterprise NVMe</strong></span>
                                        </li>
                                        <li class="d-flex align-items-center gap-2">
                                            <i class="las la-network-wired text-primary fs-16"></i>
                                            <span><strong>1Gbps Port</strong> (Unmetered)</span>
                                        </li>
                                        <li class="d-flex align-items-center gap-2">
                                            <i class="las la-terminal text-success fs-16"></i>
                                            <span><strong>1 IPv4 &amp; IPMI / KVM</strong> Root Access</span>
                                        </li>
                                    </ul>
                                </div>
                                <div class="plan-cta mt-auto">
                                    <a href="https://my.cloudflox.com/index.php?rp=/store/dedicated-servers" class="cf-btn-plan-secondary">
                                        <span>Order E5 Advanced</span>
                                        <i class="las la-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Card 3: Intel Silver 4314 (FEATURED POPULAR - $249) -->
                        <div class="col-12 col-md-6 col-xl-3">
                            <div class="modern-pricing-card featured-dedicated h-100 d-flex flex-column">
                                <div class="plan-featured-badge">MOST POPULAR FOR ENTERPRISE</div>
                                <div class="plan-header mb-3 pt-2">
                                    <span class="plan-kicker text-crimson fw-bold fs-12 text-uppercase">Enterprise Virtualization</span>
                                    <h3 class="plan-name fw-extrabold text-slate-900 fs-20 mb-1">Intel Silver 4314</h3>
                                    <p class="plan-tagline text-muted fs-13 mb-0">High-core Silver series for production VMs</p>
                                </div>
                                <div class="plan-price-wrap mb-4">
                                    <div class="d-flex align-items-baseline gap-1">
                                        <del class="text-muted fs-14 fw-medium">$279</del>
                                        <span class="price-currency fs-20 fw-bold text-crimson">$</span>
                                        <span class="price-val fs-36 fw-extrabold text-crimson">249</span>
                                        <span class="price-period text-muted fs-14">/mo</span>
                                    </div>
                                    <span class="billing-note text-slate-500 fs-12">Port 1Gbps &bull; Datacenter SLA Included</span>
                                </div>
                                <div class="plan-features flex-grow-1 mb-4">
                                    <ul class="list-unstyled p-0 m-0 d-flex flex-column gap-2 fs-13 text-slate-700">
                                        <li class="d-flex align-items-center gap-2">
                                            <i class="las la-microchip text-crimson fs-16"></i>
                                            <span><strong>16 Cores / 32 Threads</strong> (Silver 4314)</span>
                                        </li>
                                        <li class="d-flex align-items-center gap-2">
                                            <i class="las la-memory text-crimson fs-16"></i>
                                            <span><strong>128 GB DDR4</strong> Registered ECC</span>
                                        </li>
                                        <li class="d-flex align-items-center gap-2">
                                            <i class="las la-hdd text-crimson fs-16"></i>
                                            <span><strong>4 TB Enterprise NVMe</strong></span>
                                        </li>
                                        <li class="d-flex align-items-center gap-2">
                                            <i class="las la-network-wired text-crimson fs-16"></i>
                                            <span><strong>1Gbps Port</strong> (Unmetered)</span>
                                        </li>
                                        <li class="d-flex align-items-center gap-2">
                                            <i class="las la-shield-alt text-success fs-16"></i>
                                            <span><strong>Dedicated IP &amp; Datacenter SLA</strong></span>
                                        </li>
                                    </ul>
                                </div>
                                <div class="plan-cta mt-auto">
                                    <a href="https://my.cloudflox.com/index.php?rp=/store/dedicated-servers" class="btn-plan-dedicated-featured">
                                        <span>Deploy Silver 4314</span>
                                        <i class="las la-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Card 4: Intel Platinum Enterprise ($499) -->
                        <div class="col-12 col-md-6 col-xl-3">
                            <div class="modern-pricing-card h-100 d-flex flex-column">
                                <div class="plan-header mb-3">
                                    <span class="plan-kicker text-muted fw-semibold fs-12 text-uppercase">Ultimate Compute</span>
                                    <h3 class="plan-name fw-extrabold text-slate-900 fs-20 mb-1">Platinum Enterprise</h3>
                                    <p class="plan-tagline text-muted fs-13 mb-0">Xeon Platinum 8360Y for massive databases</p>
                                </div>
                                <div class="plan-price-wrap mb-4">
                                    <div class="d-flex align-items-baseline gap-1">
                                        <del class="text-muted fs-14 fw-medium">$579</del>
                                        <span class="price-currency fs-20 fw-bold text-slate-900">$</span>
                                        <span class="price-val fs-36 fw-extrabold text-slate-900">499</span>
                                        <span class="price-period text-muted fs-14">/mo</span>
                                    </div>
                                    <span class="billing-note text-slate-500 fs-12">512GB ECC &bull; 8TB NVMe &bull; Premium SLA</span>
                                </div>
                                <div class="plan-features flex-grow-1 mb-4">
                                    <ul class="list-unstyled p-0 m-0 d-flex flex-column gap-2 fs-13 text-slate-700">
                                        <li class="d-flex align-items-center gap-2">
                                            <i class="las la-microchip text-primary fs-16"></i>
                                            <span><strong>36 Cores / 72 Threads</strong> (Platinum 8360Y)</span>
                                        </li>
                                        <li class="d-flex align-items-center gap-2">
                                            <i class="las la-memory text-primary fs-16"></i>
                                            <span><strong>512 GB DDR4</strong> High-Density ECC</span>
                                        </li>
                                        <li class="d-flex align-items-center gap-2">
                                            <i class="las la-hdd text-primary fs-16"></i>
                                            <span><strong>8 TB Enterprise NVMe</strong></span>
                                        </li>
                                        <li class="d-flex align-items-center gap-2">
                                            <i class="las la-network-wired text-primary fs-16"></i>
                                            <span><strong>1Gbps Port</strong> (Unmetered)</span>
                                        </li>
                                        <li class="d-flex align-items-center gap-2">
                                            <i class="las la-star text-warning fs-16"></i>
                                            <span><strong>Premium VIP SLA Support</strong></span>
                                        </li>
                                    </ul>
                                </div>
                                <div class="plan-cta mt-auto">
                                    <a href="https://my.cloudflox.com/index.php?rp=/store/dedicated-servers" class="cf-btn-plan-secondary">
                                        <span>Order Platinum</span>
                                        <i class="las la-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Complete Intel Hardware Lineup Table (All 10 Servers) -->
                    <div class="cf-server-catalog-card mb-5">
                        <div class="cf-server-search-wrap">
                            <i class="las la-search cf-server-search-icon"></i>
                            <input type="text" id="intelSearchInput" class="cf-server-search-input" placeholder="Quick search Intel servers by model, core count, RAM, or price (e.g. 128GB, Silver, 16C, $199)...">
                        </div>

                        <div class="table-responsive">
                            <table class="table cf-server-table" id="intelServerTable">
                                <thead>
                                    <tr>
                                        <th>Processor Model</th>
                                        <th>Cores / Threads</th>
                                        <th>RAM Memory</th>
                                        <th>Storage Drive</th>
                                        <th>Port &amp; Traffic</th>
                                        <th>Dedicated IP / SLA</th>
                                        <th>Monthly Price</th>
                                        <th class="text-end">Deploy</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- 1. Intel E3 Basic -->
                                    <tr class="server-row">
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="cf-server-badge cf-server-badge-intel"><i class="las la-microchip"></i> Intel</span>
                                                <div>
                                                    <strong class="text-slate-900 d-block fs-14">Intel E3 Basic</strong>
                                                    <small class="text-muted">Xeon E3-1230 v6</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-slate-100 text-slate-800 fw-semibold px-2 py-1">4C / 8T</span></td>
                                        <td><strong class="text-slate-900">32 GB</strong> DDR4</td>
                                        <td>2 TB SSD</td>
                                        <td>1Gbps <span class="badge bg-success-subtle text-success fs-11">Unmetered</span></td>
                                        <td>1 IPv4 &bull; IPMI / KVM</td>
                                        <td>
                                            <div class="d-flex align-items-baseline gap-1">
                                                <del class="text-muted fs-12">$95</del>
                                                <strong class="text-crimson fs-16">$79</strong><small class="text-muted">/mo</small>
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <a href="https://my.cloudflox.com/index.php?rp=/store/dedicated-servers" class="btn-server-order">
                                                <span>Order</span>
                                                <i class="las la-arrow-right"></i>
                                            </a>
                                        </td>
                                    </tr>

                                    <!-- 2. Intel E3 Pro -->
                                    <tr class="server-row">
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="cf-server-badge cf-server-badge-intel"><i class="las la-microchip"></i> Intel</span>
                                                <div>
                                                    <strong class="text-slate-900 d-block fs-14">Intel E3 Pro</strong>
                                                    <small class="text-muted">Xeon E3-1270 v6</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-slate-100 text-slate-800 fw-semibold px-2 py-1">4C / 8T</span></td>
                                        <td><strong class="text-slate-900">64 GB</strong> DDR4</td>
                                        <td>2 TB SSD</td>
                                        <td>1Gbps <span class="badge bg-success-subtle text-success fs-11">Unmetered</span></td>
                                        <td>1 IPv4 &bull; IPMI / KVM</td>
                                        <td>
                                            <div class="d-flex align-items-baseline gap-1">
                                                <del class="text-muted fs-12">$119</del>
                                                <strong class="text-crimson fs-16">$99</strong><small class="text-muted">/mo</small>
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <a href="https://my.cloudflox.com/index.php?rp=/store/dedicated-servers" class="btn-server-order">
                                                <span>Order</span>
                                                <i class="las la-arrow-right"></i>
                                            </a>
                                        </td>
                                    </tr>

                                    <!-- 3. Intel E5 Standard -->
                                    <tr class="server-row">
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="cf-server-badge cf-server-badge-intel"><i class="las la-microchip"></i> Intel</span>
                                                <div>
                                                    <strong class="text-slate-900 d-block fs-14">Intel E5 Standard</strong>
                                                    <small class="text-muted">Xeon E5-2620 v4</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-slate-100 text-slate-800 fw-semibold px-2 py-1">8C / 16T</span></td>
                                        <td><strong class="text-slate-900">64 GB</strong> DDR4 ECC</td>
                                        <td>2 TB SSD</td>
                                        <td>1Gbps <span class="badge bg-success-subtle text-success fs-11">Unmetered</span></td>
                                        <td>1 IPv4 &bull; IPMI / KVM</td>
                                        <td>
                                            <div class="d-flex align-items-baseline gap-1">
                                                <del class="text-muted fs-12">$149</del>
                                                <strong class="text-crimson fs-16">$125</strong><small class="text-muted">/mo</small>
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <a href="https://my.cloudflox.com/index.php?rp=/store/dedicated-servers" class="btn-server-order">
                                                <span>Order</span>
                                                <i class="las la-arrow-right"></i>
                                            </a>
                                        </td>
                                    </tr>

                                    <!-- 4. Intel E5 Advanced -->
                                    <tr class="server-row">
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="cf-server-badge cf-server-badge-intel"><i class="las la-microchip"></i> Intel</span>
                                                <div>
                                                    <strong class="text-slate-900 d-block fs-14">Intel E5 Advanced</strong>
                                                    <small class="text-muted">Xeon E5-2680 v4</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-slate-100 text-slate-800 fw-semibold px-2 py-1">14C / 28T</span></td>
                                        <td><strong class="text-slate-900">128 GB</strong> DDR4 ECC</td>
                                        <td>2 TB NVMe</td>
                                        <td>1Gbps <span class="badge bg-success-subtle text-success fs-11">Unmetered</span></td>
                                        <td>1 IPv4 &bull; IPMI / KVM</td>
                                        <td>
                                            <div class="d-flex align-items-baseline gap-1">
                                                <del class="text-muted fs-12">$199</del>
                                                <strong class="text-crimson fs-16">$169</strong><small class="text-muted">/mo</small>
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <a href="https://my.cloudflox.com/index.php?rp=/store/dedicated-servers" class="btn-server-order">
                                                <span>Order</span>
                                                <i class="las la-arrow-right"></i>
                                            </a>
                                        </td>
                                    </tr>

                                    <!-- 5. Intel Silver 4210 -->
                                    <tr class="server-row">
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="cf-server-badge cf-server-badge-intel"><i class="las la-microchip"></i> Intel</span>
                                                <div>
                                                    <strong class="text-slate-900 d-block fs-14">Intel Silver 4210</strong>
                                                    <small class="text-muted">Xeon Silver 4210</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-slate-100 text-slate-800 fw-semibold px-2 py-1">10C / 20T</span></td>
                                        <td><strong class="text-slate-900">128 GB</strong> DDR4 ECC</td>
                                        <td>2 TB NVMe</td>
                                        <td>1Gbps <span class="badge bg-success-subtle text-success fs-11">Unmetered</span></td>
                                        <td>Dedicated IP &bull; Datacenter SLA</td>
                                        <td>
                                            <div class="d-flex align-items-baseline gap-1">
                                                <del class="text-muted fs-12">$229</del>
                                                <strong class="text-crimson fs-16">$199</strong><small class="text-muted">/mo</small>
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <a href="https://my.cloudflox.com/index.php?rp=/store/dedicated-servers" class="btn-server-order">
                                                <span>Order</span>
                                                <i class="las la-arrow-right"></i>
                                            </a>
                                        </td>
                                    </tr>

                                    <!-- 6. Intel Silver 4314 (Featured in Table) -->
                                    <tr class="server-row bg-crimson-subtle">
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="cf-server-badge cf-server-badge-intel"><i class="las la-microchip"></i> Intel</span>
                                                <div>
                                                    <strong class="text-slate-900 d-block fs-14">Intel Silver 4314</strong>
                                                    <small class="text-crimson fw-semibold">Xeon Silver 4314 &bull; Popular</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-danger text-white fw-bold px-2 py-1">16C / 32T</span></td>
                                        <td><strong class="text-slate-900">128 GB</strong> DDR4 ECC</td>
                                        <td>4 TB NVMe</td>
                                        <td>1Gbps <span class="badge bg-success text-white fs-11">Unmetered</span></td>
                                        <td>Dedicated IP &bull; Datacenter SLA</td>
                                        <td>
                                            <div class="d-flex align-items-baseline gap-1">
                                                <del class="text-muted fs-12">$279</del>
                                                <strong class="text-crimson fs-16">$249</strong><small class="text-muted">/mo</small>
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <a href="https://my.cloudflox.com/index.php?rp=/store/dedicated-servers" class="btn-server-order" style="background:#E11D48; border-color:#E11D48;">
                                                <span>Order</span>
                                                <i class="las la-arrow-right"></i>
                                            </a>
                                        </td>
                                    </tr>

                                    <!-- 7. Intel Gold 5218 -->
                                    <tr class="server-row">
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="cf-server-badge cf-server-badge-intel"><i class="las la-microchip"></i> Intel</span>
                                                <div>
                                                    <strong class="text-slate-900 d-block fs-14">Intel Gold 5218</strong>
                                                    <small class="text-muted">Xeon Gold 5218</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-slate-100 text-slate-800 fw-semibold px-2 py-1">16C / 32T</span></td>
                                        <td><strong class="text-slate-900">192 GB</strong> DDR4 ECC</td>
                                        <td>4 TB NVMe</td>
                                        <td>1Gbps <span class="badge bg-success-subtle text-success fs-11">Unmetered</span></td>
                                        <td>Dedicated IP &bull; Datacenter SLA</td>
                                        <td>
                                            <div class="d-flex align-items-baseline gap-1">
                                                <del class="text-muted fs-12">$339</del>
                                                <strong class="text-crimson fs-16">$299</strong><small class="text-muted">/mo</small>
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <a href="https://my.cloudflox.com/index.php?rp=/store/dedicated-servers" class="btn-server-order">
                                                <span>Order</span>
                                                <i class="las la-arrow-right"></i>
                                            </a>
                                        </td>
                                    </tr>

                                    <!-- 8. Intel Gold 6226R -->
                                    <tr class="server-row">
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="cf-server-badge cf-server-badge-intel"><i class="las la-microchip"></i> Intel</span>
                                                <div>
                                                    <strong class="text-slate-900 d-block fs-14">Intel Gold 6226R</strong>
                                                    <small class="text-muted">Xeon Gold 6226R</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-slate-100 text-slate-800 fw-semibold px-2 py-1">16C / 32T</span></td>
                                        <td><strong class="text-slate-900">256 GB</strong> DDR4 ECC</td>
                                        <td>4 TB NVMe</td>
                                        <td>1Gbps <span class="badge bg-success-subtle text-success fs-11">Unmetered</span></td>
                                        <td>Dedicated IP &bull; Datacenter SLA</td>
                                        <td>
                                            <div class="d-flex align-items-baseline gap-1">
                                                <del class="text-muted fs-12">$399</del>
                                                <strong class="text-crimson fs-16">$349</strong><small class="text-muted">/mo</small>
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <a href="https://my.cloudflox.com/index.php?rp=/store/dedicated-servers" class="btn-server-order">
                                                <span>Order</span>
                                                <i class="las la-arrow-right"></i>
                                            </a>
                                        </td>
                                    </tr>

                                    <!-- 9. Intel Platinum 8259CL -->
                                    <tr class="server-row">
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="cf-server-badge cf-server-badge-intel"><i class="las la-microchip"></i> Intel</span>
                                                <div>
                                                    <strong class="text-slate-900 d-block fs-14">Intel Platinum 8259CL</strong>
                                                    <small class="text-muted">Xeon Platinum 8259CL</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-slate-100 text-slate-800 fw-semibold px-2 py-1">24C / 48T</span></td>
                                        <td><strong class="text-slate-900">256 GB</strong> DDR4 ECC</td>
                                        <td>6 TB NVMe</td>
                                        <td>1Gbps <span class="badge bg-success-subtle text-success fs-11">Unmetered</span></td>
                                        <td>Dedicated IP &bull; Datacenter SLA</td>
                                        <td>
                                            <div class="d-flex align-items-baseline gap-1">
                                                <del class="text-muted fs-12">$449</del>
                                                <strong class="text-crimson fs-16">$399</strong><small class="text-muted">/mo</small>
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <a href="https://my.cloudflox.com/index.php?rp=/store/dedicated-servers" class="btn-server-order">
                                                <span>Order</span>
                                                <i class="las la-arrow-right"></i>
                                            </a>
                                        </td>
                                    </tr>

                                    <!-- 10. Intel Platinum Enterprise 8360Y -->
                                    <tr class="server-row">
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="cf-server-badge cf-server-badge-intel"><i class="las la-microchip"></i> Intel</span>
                                                <div>
                                                    <strong class="text-slate-900 d-block fs-14">Intel Platinum Enterprise</strong>
                                                    <small class="text-muted">Xeon Platinum 8360Y</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-dark text-white fw-bold px-2 py-1">36C / 72T</span></td>
                                        <td><strong class="text-slate-900">512 GB</strong> DDR4 ECC</td>
                                        <td>8 TB NVMe</td>
                                        <td>1Gbps <span class="badge bg-success-subtle text-success fs-11">Unmetered</span></td>
                                        <td>Dedicated IP &bull; Premium SLA</td>
                                        <td>
                                            <div class="d-flex align-items-baseline gap-1">
                                                <del class="text-muted fs-12">$579</del>
                                                <strong class="text-crimson fs-16">$499</strong><small class="text-muted">/mo</small>
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <a href="https://my.cloudflox.com/index.php?rp=/store/dedicated-servers" class="btn-server-order">
                                                <span>Order</span>
                                                <i class="las la-arrow-right"></i>
                                            </a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- =============================================================
                     TAB 2: AMD RYZEN HIGH-CLOCK LINEUP
                     ============================================================= -->
                <div class="tab-pane fade" id="paneAmd" role="tabpanel" aria-labelledby="btnArchAmd">

                    <!-- Featured 4 Showcase Cards for AMD -->
                    <div class="row g-4 mb-5 justify-content-center">

                        <!-- Card 1: Ryzen 3600 ($89) -->
                        <div class="col-12 col-md-6 col-xl-3">
                            <div class="modern-pricing-card h-100 d-flex flex-column">
                                <div class="plan-header mb-3">
                                    <span class="plan-kicker text-muted fw-semibold fs-12 text-uppercase">High-Clock Starter</span>
                                    <h3 class="plan-name fw-extrabold text-slate-900 fs-20 mb-1">Ryzen 3600</h3>
                                    <p class="plan-tagline text-muted fs-13 mb-0">Ryzen 5 3600 for web &amp; fast app hosting</p>
                                </div>
                                <div class="plan-price-wrap mb-4">
                                    <div class="d-flex align-items-baseline gap-1">
                                        <del class="text-muted fs-14 fw-medium">$109</del>
                                        <span class="price-currency fs-20 fw-bold text-slate-900">$</span>
                                        <span class="price-val fs-36 fw-extrabold text-slate-900">89</span>
                                        <span class="price-period text-muted fs-14">/mo</span>
                                    </div>
                                    <span class="billing-note text-slate-500 fs-12">Port 1Gbps &bull; Unmetered Traffic</span>
                                </div>
                                <div class="plan-features flex-grow-1 mb-4">
                                    <ul class="list-unstyled p-0 m-0 d-flex flex-column gap-2 fs-13 text-slate-700">
                                        <li class="d-flex align-items-center gap-2">
                                            <i class="las la-bolt text-danger fs-16"></i>
                                            <span><strong>6 Cores / 12 Threads</strong> @ 3.6GHz</span>
                                        </li>
                                        <li class="d-flex align-items-center gap-2">
                                            <i class="las la-memory text-danger fs-16"></i>
                                            <span><strong>32 GB DDR4</strong> RAM</span>
                                        </li>
                                        <li class="d-flex align-items-center gap-2">
                                            <i class="las la-hdd text-danger fs-16"></i>
                                            <span><strong>1 TB Gen4 NVMe</strong></span>
                                        </li>
                                        <li class="d-flex align-items-center gap-2">
                                            <i class="las la-network-wired text-danger fs-16"></i>
                                            <span><strong>1Gbps Port</strong> (Unmetered)</span>
                                        </li>
                                        <li class="d-flex align-items-center gap-2">
                                            <i class="las la-terminal text-success fs-16"></i>
                                            <span><strong>1 IPv4 &amp; IPMI / KVM</strong> Root Access</span>
                                        </li>
                                    </ul>
                                </div>
                                <div class="plan-cta mt-auto">
                                    <a href="https://my.cloudflox.com/index.php?rp=/store/dedicated-servers" class="cf-btn-plan-secondary">
                                        <span>Order Ryzen 3600</span>
                                        <i class="las la-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Card 2: Ryzen 5800X ($169) -->
                        <div class="col-12 col-md-6 col-xl-3">
                            <div class="modern-pricing-card h-100 d-flex flex-column">
                                <div class="plan-header mb-3">
                                    <span class="plan-kicker text-muted fw-semibold fs-12 text-uppercase">High Throughput</span>
                                    <h3 class="plan-name fw-extrabold text-slate-900 fs-20 mb-1">Ryzen 5800X</h3>
                                    <p class="plan-tagline text-muted fs-13 mb-0">Ryzen 7 5800X for heavy database loads</p>
                                </div>
                                <div class="plan-price-wrap mb-4">
                                    <div class="d-flex align-items-baseline gap-1">
                                        <del class="text-muted fs-14 fw-medium">$199</del>
                                        <span class="price-currency fs-20 fw-bold text-slate-900">$</span>
                                        <span class="price-val fs-36 fw-extrabold text-slate-900">169</span>
                                        <span class="price-period text-muted fs-14">/mo</span>
                                    </div>
                                    <span class="billing-note text-slate-500 fs-12">128GB RAM &bull; 2TB NVMe</span>
                                </div>
                                <div class="plan-features flex-grow-1 mb-4">
                                    <ul class="list-unstyled p-0 m-0 d-flex flex-column gap-2 fs-13 text-slate-700">
                                        <li class="d-flex align-items-center gap-2">
                                            <i class="las la-bolt text-danger fs-16"></i>
                                            <span><strong>8 Cores / 16 Threads</strong> @ 3.8GHz</span>
                                        </li>
                                        <li class="d-flex align-items-center gap-2">
                                            <i class="las la-memory text-danger fs-16"></i>
                                            <span><strong>128 GB DDR4</strong> RAM</span>
                                        </li>
                                        <li class="d-flex align-items-center gap-2">
                                            <i class="las la-hdd text-danger fs-16"></i>
                                            <span><strong>2 TB Gen4 NVMe</strong></span>
                                        </li>
                                        <li class="d-flex align-items-center gap-2">
                                            <i class="las la-network-wired text-danger fs-16"></i>
                                            <span><strong>1Gbps Port</strong> (Unmetered)</span>
                                        </li>
                                        <li class="d-flex align-items-center gap-2">
                                            <i class="las la-terminal text-success fs-16"></i>
                                            <span><strong>1 IPv4 &amp; IPMI / KVM</strong> Root Access</span>
                                        </li>
                                    </ul>
                                </div>
                                <div class="plan-cta mt-auto">
                                    <a href="https://my.cloudflox.com/index.php?rp=/store/dedicated-servers" class="cf-btn-plan-secondary">
                                        <span>Order Ryzen 5800X</span>
                                        <i class="las la-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Card 3: Ryzen 7950X (FEATURED POPULAR - $299) -->
                        <div class="col-12 col-md-6 col-xl-3">
                            <div class="modern-pricing-card featured-dedicated h-100 d-flex flex-column">
                                <div class="plan-featured-badge">FLAGSHIP ZEN 4 PERFORMANCE</div>
                                <div class="plan-header mb-3 pt-2">
                                    <span class="plan-kicker text-crimson fw-bold fs-12 text-uppercase">Extreme Multi-Core</span>
                                    <h3 class="plan-name fw-extrabold text-slate-900 fs-20 mb-1">Ryzen 7950X</h3>
                                    <p class="plan-tagline text-muted fs-13 mb-0">16 Cores Zen 4 for demanding production apps</p>
                                </div>
                                <div class="plan-price-wrap mb-4">
                                    <div class="d-flex align-items-baseline gap-1">
                                        <del class="text-muted fs-14 fw-medium">$339</del>
                                        <span class="price-currency fs-20 fw-bold text-crimson">$</span>
                                        <span class="price-val fs-36 fw-extrabold text-crimson">299</span>
                                        <span class="price-period text-muted fs-14">/mo</span>
                                    </div>
                                    <span class="billing-note text-slate-500 fs-12">192GB DDR5 &bull; 4TB NVMe &bull; Unmetered</span>
                                </div>
                                <div class="plan-features flex-grow-1 mb-4">
                                    <ul class="list-unstyled p-0 m-0 d-flex flex-column gap-2 fs-13 text-slate-700">
                                        <li class="d-flex align-items-center gap-2">
                                            <i class="las la-bolt text-crimson fs-16"></i>
                                            <span><strong>16 Cores / 32 Threads</strong> @ 4.5GHz</span>
                                        </li>
                                        <li class="d-flex align-items-center gap-2">
                                            <i class="las la-memory text-crimson fs-16"></i>
                                            <span><strong>192 GB High-Speed</strong> DDR5 ECC</span>
                                        </li>
                                        <li class="d-flex align-items-center gap-2">
                                            <i class="las la-hdd text-crimson fs-16"></i>
                                            <span><strong>4 TB Gen5 NVMe</strong> Array</span>
                                        </li>
                                        <li class="d-flex align-items-center gap-2">
                                            <i class="las la-network-wired text-crimson fs-16"></i>
                                            <span><strong>1Gbps Port</strong> (Unmetered)</span>
                                        </li>
                                        <li class="d-flex align-items-center gap-2">
                                            <i class="las la-shield-alt text-success fs-16"></i>
                                            <span><strong>Dedicated IP &amp; Datacenter SLA</strong></span>
                                        </li>
                                    </ul>
                                </div>
                                <div class="plan-cta mt-auto">
                                    <a href="https://my.cloudflox.com/index.php?rp=/store/dedicated-servers" class="btn-plan-dedicated-featured">
                                        <span>Deploy Ryzen 7950X</span>
                                        <i class="las la-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Card 4: Ryzen Enterprise Max ($399) -->
                        <div class="col-12 col-md-6 col-xl-3">
                            <div class="modern-pricing-card h-100 d-flex flex-column">
                                <div class="plan-header mb-3">
                                    <span class="plan-kicker text-muted fw-semibold fs-12 text-uppercase">Zen 5 Flagship OC</span>
                                    <h3 class="plan-name fw-extrabold text-slate-900 fs-20 mb-1">Enterprise Max</h3>
                                    <p class="plan-tagline text-muted fs-13 mb-0">Ryzen 9950X OC with 512GB RAM for AI &amp; big data</p>
                                </div>
                                <div class="plan-price-wrap mb-4">
                                    <div class="d-flex align-items-baseline gap-1">
                                        <del class="text-muted fs-14 fw-medium">$459</del>
                                        <span class="price-currency fs-20 fw-bold text-slate-900">$</span>
                                        <span class="price-val fs-36 fw-extrabold text-slate-900">399</span>
                                        <span class="price-period text-muted fs-14">/mo</span>
                                    </div>
                                    <span class="billing-note text-slate-500 fs-12">512GB DDR5 &bull; 8TB NVMe &bull; 9950X OC</span>
                                </div>
                                <div class="plan-features flex-grow-1 mb-4">
                                    <ul class="list-unstyled p-0 m-0 d-flex flex-column gap-2 fs-13 text-slate-700">
                                        <li class="d-flex align-items-center gap-2">
                                            <i class="las la-bolt text-danger fs-16"></i>
                                            <span><strong>16 Cores / 32 Threads</strong> (9950X OC)</span>
                                        </li>
                                        <li class="d-flex align-items-center gap-2">
                                            <i class="las la-memory text-danger fs-16"></i>
                                            <span><strong>512 GB High-Speed</strong> DDR5 ECC</span>
                                        </li>
                                        <li class="d-flex align-items-center gap-2">
                                            <i class="las la-hdd text-danger fs-16"></i>
                                            <span><strong>8 TB Gen5 NVMe</strong> Array</span>
                                        </li>
                                        <li class="d-flex align-items-center gap-2">
                                            <i class="las la-network-wired text-danger fs-16"></i>
                                            <span><strong>1Gbps Port</strong> (Unmetered)</span>
                                        </li>
                                        <li class="d-flex align-items-center gap-2">
                                            <i class="las la-star text-warning fs-16"></i>
                                            <span><strong>Premium VIP SLA Support</strong></span>
                                        </li>
                                    </ul>
                                </div>
                                <div class="plan-cta mt-auto">
                                    <a href="https://my.cloudflox.com/index.php?rp=/store/dedicated-servers" class="cf-btn-plan-secondary">
                                        <span>Order Enterprise Max</span>
                                        <i class="las la-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Complete AMD Hardware Lineup Table (All 10 Servers) -->
                    <div class="cf-server-catalog-card mb-5">
                        <div class="cf-server-search-wrap">
                            <i class="las la-search cf-server-search-icon"></i>
                            <input type="text" id="amdSearchInput" class="cf-server-search-input" placeholder="Quick search AMD servers by model, core count, RAM, or price (e.g. 7950X, 128GB, 16C, $229)...">
                        </div>

                        <div class="table-responsive">
                            <table class="table cf-server-table" id="amdServerTable">
                                <thead>
                                    <tr>
                                        <th>Processor Model</th>
                                        <th>Cores / Threads</th>
                                        <th>RAM Memory</th>
                                        <th>Storage Drive</th>
                                        <th>Port &amp; Traffic</th>
                                        <th>Dedicated IP / SLA</th>
                                        <th>Monthly Price</th>
                                        <th class="text-end">Deploy</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- 1. Ryzen 3600 -->
                                    <tr class="server-row">
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="cf-server-badge cf-server-badge-amd"><i class="las la-bolt"></i> AMD</span>
                                                <div>
                                                    <strong class="text-slate-900 d-block fs-14">Ryzen 3600</strong>
                                                    <small class="text-muted">Ryzen 5 3600</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-slate-100 text-slate-800 fw-semibold px-2 py-1">6C / 12T</span></td>
                                        <td><strong class="text-slate-900">32 GB</strong> DDR4</td>
                                        <td>1 TB NVMe</td>
                                        <td>1Gbps <span class="badge bg-success-subtle text-success fs-11">Unmetered</span></td>
                                        <td>1 IPv4 &bull; Dedicated IP</td>
                                        <td>
                                            <div class="d-flex align-items-baseline gap-1">
                                                <del class="text-muted fs-12">$109</del>
                                                <strong class="text-crimson fs-16">$89</strong><small class="text-muted">/mo</small>
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <a href="https://my.cloudflox.com/index.php?rp=/store/dedicated-servers" class="btn-server-order">
                                                <span>Order</span>
                                                <i class="las la-arrow-right"></i>
                                            </a>
                                        </td>
                                    </tr>

                                    <!-- 2. Ryzen 5600X -->
                                    <tr class="server-row">
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="cf-server-badge cf-server-badge-amd"><i class="las la-bolt"></i> AMD</span>
                                                <div>
                                                    <strong class="text-slate-900 d-block fs-14">Ryzen 5600X</strong>
                                                    <small class="text-muted">Ryzen 5 5600X</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-slate-100 text-slate-800 fw-semibold px-2 py-1">6C / 12T</span></td>
                                        <td><strong class="text-slate-900">64 GB</strong> DDR4</td>
                                        <td>2 TB NVMe</td>
                                        <td>1Gbps <span class="badge bg-success-subtle text-success fs-11">Unmetered</span></td>
                                        <td>1 IPv4 &bull; Dedicated IP</td>
                                        <td>
                                            <div class="d-flex align-items-baseline gap-1">
                                                <del class="text-muted fs-12">$139</del>
                                                <strong class="text-crimson fs-16">$119</strong><small class="text-muted">/mo</small>
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <a href="https://my.cloudflox.com/index.php?rp=/store/dedicated-servers" class="btn-server-order">
                                                <span>Order</span>
                                                <i class="las la-arrow-right"></i>
                                            </a>
                                        </td>
                                    </tr>

                                    <!-- 3. Ryzen 5700X -->
                                    <tr class="server-row">
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="cf-server-badge cf-server-badge-amd"><i class="las la-bolt"></i> AMD</span>
                                                <div>
                                                    <strong class="text-slate-900 d-block fs-14">Ryzen 5700X</strong>
                                                    <small class="text-muted">Ryzen 7 5700X</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-slate-100 text-slate-800 fw-semibold px-2 py-1">8C / 16T</span></td>
                                        <td><strong class="text-slate-900">64 GB</strong> DDR4</td>
                                        <td>2 TB NVMe</td>
                                        <td>1Gbps <span class="badge bg-success-subtle text-success fs-11">Unmetered</span></td>
                                        <td>1 IPv4 &bull; Dedicated IP</td>
                                        <td>
                                            <div class="d-flex align-items-baseline gap-1">
                                                <del class="text-muted fs-12">$159</del>
                                                <strong class="text-crimson fs-16">$139</strong><small class="text-muted">/mo</small>
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <a href="https://my.cloudflox.com/index.php?rp=/store/dedicated-servers" class="btn-server-order">
                                                <span>Order</span>
                                                <i class="las la-arrow-right"></i>
                                            </a>
                                        </td>
                                    </tr>

                                    <!-- 4. Ryzen 5800X -->
                                    <tr class="server-row">
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="cf-server-badge cf-server-badge-amd"><i class="las la-bolt"></i> AMD</span>
                                                <div>
                                                    <strong class="text-slate-900 d-block fs-14">Ryzen 5800X</strong>
                                                    <small class="text-muted">Ryzen 7 5800X</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-slate-100 text-slate-800 fw-semibold px-2 py-1">8C / 16T</span></td>
                                        <td><strong class="text-slate-900">128 GB</strong> DDR4</td>
                                        <td>2 TB NVMe</td>
                                        <td>1Gbps <span class="badge bg-success-subtle text-success fs-11">Unmetered</span></td>
                                        <td>1 IPv4 &bull; Dedicated IP</td>
                                        <td>
                                            <div class="d-flex align-items-baseline gap-1">
                                                <del class="text-muted fs-12">$199</del>
                                                <strong class="text-crimson fs-16">$169</strong><small class="text-muted">/mo</small>
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <a href="https://my.cloudflox.com/index.php?rp=/store/dedicated-servers" class="btn-server-order">
                                                <span>Order</span>
                                                <i class="las la-arrow-right"></i>
                                            </a>
                                        </td>
                                    </tr>

                                    <!-- 5. Ryzen 5900X -->
                                    <tr class="server-row">
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="cf-server-badge cf-server-badge-amd"><i class="las la-bolt"></i> AMD</span>
                                                <div>
                                                    <strong class="text-slate-900 d-block fs-14">Ryzen 5900X</strong>
                                                    <small class="text-muted">Ryzen 9 5900X</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-slate-100 text-slate-800 fw-semibold px-2 py-1">12C / 24T</span></td>
                                        <td><strong class="text-slate-900">128 GB</strong> DDR4</td>
                                        <td>4 TB NVMe</td>
                                        <td>1Gbps <span class="badge bg-success-subtle text-success fs-11">Unmetered</span></td>
                                        <td>1 IPv4 &bull; Dedicated IP</td>
                                        <td>
                                            <div class="d-flex align-items-baseline gap-1">
                                                <del class="text-muted fs-12">$229</del>
                                                <strong class="text-crimson fs-16">$199</strong><small class="text-muted">/mo</small>
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <a href="https://my.cloudflox.com/index.php?rp=/store/dedicated-servers" class="btn-server-order">
                                                <span>Order</span>
                                                <i class="las la-arrow-right"></i>
                                            </a>
                                        </td>
                                    </tr>

                                    <!-- 6. Ryzen 5950X -->
                                    <tr class="server-row">
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="cf-server-badge cf-server-badge-amd"><i class="las la-bolt"></i> AMD</span>
                                                <div>
                                                    <strong class="text-slate-900 d-block fs-14">Ryzen 5950X</strong>
                                                    <small class="text-muted">Ryzen 9 5950X</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-slate-100 text-slate-800 fw-semibold px-2 py-1">16C / 32T</span></td>
                                        <td><strong class="text-slate-900">128 GB</strong> DDR4</td>
                                        <td>4 TB NVMe</td>
                                        <td>1Gbps <span class="badge bg-success-subtle text-success fs-11">Unmetered</span></td>
                                        <td>1 IPv4 &bull; Dedicated IP</td>
                                        <td>
                                            <div class="d-flex align-items-baseline gap-1">
                                                <del class="text-muted fs-12">$259</del>
                                                <strong class="text-crimson fs-16">$229</strong><small class="text-muted">/mo</small>
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <a href="https://my.cloudflox.com/index.php?rp=/store/dedicated-servers" class="btn-server-order">
                                                <span>Order</span>
                                                <i class="las la-arrow-right"></i>
                                            </a>
                                        </td>
                                    </tr>

                                    <!-- 7. Ryzen 7900X -->
                                    <tr class="server-row">
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="cf-server-badge cf-server-badge-amd"><i class="las la-bolt"></i> AMD</span>
                                                <div>
                                                    <strong class="text-slate-900 d-block fs-14">Ryzen 7900X</strong>
                                                    <small class="text-muted">Ryzen 9 7900X</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-slate-100 text-slate-800 fw-semibold px-2 py-1">12C / 24T</span></td>
                                        <td><strong class="text-slate-900">128 GB</strong> DDR5</td>
                                        <td>4 TB NVMe</td>
                                        <td>1Gbps <span class="badge bg-success-subtle text-success fs-11">Unmetered</span></td>
                                        <td>1 IPv4 &bull; Dedicated IP</td>
                                        <td>
                                            <div class="d-flex align-items-baseline gap-1">
                                                <del class="text-muted fs-12">$299</del>
                                                <strong class="text-crimson fs-16">$259</strong><small class="text-muted">/mo</small>
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <a href="https://my.cloudflox.com/index.php?rp=/store/dedicated-servers" class="btn-server-order">
                                                <span>Order</span>
                                                <i class="las la-arrow-right"></i>
                                            </a>
                                        </td>
                                    </tr>

                                    <!-- 8. Ryzen 7950X (Featured in AMD Table) -->
                                    <tr class="server-row bg-crimson-subtle">
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="cf-server-badge cf-server-badge-amd"><i class="las la-bolt"></i> AMD</span>
                                                <div>
                                                    <strong class="text-slate-900 d-block fs-14">Ryzen 7950X</strong>
                                                    <small class="text-crimson fw-semibold">Zen 4 &bull; 16C @ 4.5GHz &bull; Popular</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-danger text-white fw-bold px-2 py-1">16C / 32T</span></td>
                                        <td><strong class="text-slate-900">192 GB</strong> DDR5 ECC</td>
                                        <td>4 TB NVMe</td>
                                        <td>1Gbps <span class="badge bg-success text-white fs-11">Unmetered</span></td>
                                        <td>Dedicated IP &bull; Datacenter SLA</td>
                                        <td>
                                            <div class="d-flex align-items-baseline gap-1">
                                                <del class="text-muted fs-12">$339</del>
                                                <strong class="text-crimson fs-16">$299</strong><small class="text-muted">/mo</small>
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <a href="https://my.cloudflox.com/index.php?rp=/store/dedicated-servers" class="btn-server-order" style="background:#E11D48; border-color:#E11D48;">
                                                <span>Order</span>
                                                <i class="las la-arrow-right"></i>
                                            </a>
                                        </td>
                                    </tr>

                                    <!-- 9. Ryzen 9950X -->
                                    <tr class="server-row">
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="cf-server-badge cf-server-badge-amd"><i class="las la-bolt"></i> AMD</span>
                                                <div>
                                                    <strong class="text-slate-900 d-block fs-14">Ryzen 9950X</strong>
                                                    <small class="text-muted">Ryzen 9 9950X Zen 5</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-slate-100 text-slate-800 fw-semibold px-2 py-1">16C / 32T</span></td>
                                        <td><strong class="text-slate-900">256 GB</strong> DDR5 ECC</td>
                                        <td>6 TB NVMe</td>
                                        <td>1Gbps <span class="badge bg-success-subtle text-success fs-11">Unmetered</span></td>
                                        <td>Dedicated IP &bull; Datacenter SLA</td>
                                        <td>
                                            <div class="d-flex align-items-baseline gap-1">
                                                <del class="text-muted fs-12">$389</del>
                                                <strong class="text-crimson fs-16">$339</strong><small class="text-muted">/mo</small>
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <a href="https://my.cloudflox.com/index.php?rp=/store/dedicated-servers" class="btn-server-order">
                                                <span>Order</span>
                                                <i class="las la-arrow-right"></i>
                                            </a>
                                        </td>
                                    </tr>

                                    <!-- 10. Ryzen Enterprise Max 9950X OC -->
                                    <tr class="server-row">
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="cf-server-badge cf-server-badge-amd"><i class="las la-bolt"></i> AMD</span>
                                                <div>
                                                    <strong class="text-slate-900 d-block fs-14">Ryzen Enterprise Max</strong>
                                                    <small class="text-muted">Ryzen 9950X OC</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-dark text-white fw-bold px-2 py-1">16C / 32T OC</span></td>
                                        <td><strong class="text-slate-900">512 GB</strong> DDR5 ECC</td>
                                        <td>8 TB NVMe</td>
                                        <td>1Gbps <span class="badge bg-success-subtle text-success fs-11">Unmetered</span></td>
                                        <td>Dedicated IP &bull; Premium SLA</td>
                                        <td>
                                            <div class="d-flex align-items-baseline gap-1">
                                                <del class="text-muted fs-12">$459</del>
                                                <strong class="text-crimson fs-16">$399</strong><small class="text-muted">/mo</small>
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <a href="https://my.cloudflox.com/index.php?rp=/store/dedicated-servers" class="btn-server-order">
                                                <span>Order</span>
                                                <i class="las la-arrow-right"></i>
                                            </a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </section>

    <!-- =========================================================================
         3. DETAILED TECHNICAL COMPARISON TABLE (Sticky Frozen Table Header)
         ========================================================================= -->
    <section class="cf-comparison-section py-5 bg-white border-top border-bottom" id="serverComparison">
        <div class="container">
            <!-- Section Header -->
            <div class="text-center max-w-750 mx-auto mb-4 mb-md-5">
                <span class="badge bg-crimson-subtle text-crimson fw-bold px-3 py-1 rounded-pill mb-2">
                    DETAILED METRICS
                </span>
                <h2 class="display-6 fw-extrabold text-slate-900 mb-2">
                    Hardware Architecture &amp; Performance Comparison
                </h2>
                <p class="text-slate-600 fs-16 mb-0">
                    Direct comparison of our Intel Xeon Scalable vs. AMD Ryzen High-Clock bare metal server lines.
                </p>
            </div>

            <!-- Mobile Swipe Hint -->
            <div class="d-md-none text-center text-muted fs-12 mb-3">
                <i class="las la-arrows-alt-h text-crimson me-1"></i> Swipe horizontally to compare all hardware metrics
            </div>

            <!-- Responsive Table Container with Sticky Frozen Headers -->
            <div class="cf-comparison-table-wrapper rounded-4 border bg-white shadow-sm">
                <div class="table-responsive">
                    <table class="table cf-comparison-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="cf-sticky-feature-th">
                                    <div class="cf-feature-heading">
                                        <span>Hardware Metrics</span>
                                        <small>Bare metal specifications</small>
                                    </div>
                                </th>
                                <th class="cf-plan-header-cell text-center">
                                    <span class="d-block fw-bold fs-16 text-slate-900">Intel Xeon Scalable</span>
                                    <span class="fs-14 text-primary fw-extrabold">From $79.00/mo</span>
                                </th>
                                <th class="cf-plan-header-cell popular-col-dedicated text-center">
                                    <span class="cf-popular-tag-dedicated">HIGH FREQUENCY</span>
                                    <span class="d-block fw-bold fs-16 text-crimson mt-2">AMD Ryzen OC</span>
                                    <span class="fs-14 text-crimson fw-extrabold">From $89.00/mo</span>
                                </th>
                                <th class="cf-plan-header-cell text-center">
                                    <span class="d-block fw-bold fs-16 text-slate-900">CloudFlox SLA</span>
                                    <span class="fs-14 text-success fw-extrabold">Included on All Tiers</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Category 1: Compute Architecture -->
                            <tr class="cf-cat-header-row">
                                <td colspan="4">
                                    <i class="las la-microchip"></i> 1. Compute &amp; Processor Architecture
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="cf-sticky-feature-cell fw-semibold text-slate-800">Processor Families Available</th>
                                <td class="text-center">Intel Xeon E3, E5, Silver, Gold, Platinum</td>
                                <td class="text-center popular-col-dedicated fw-semibold text-slate-900">AMD Ryzen 3000, 5000, 7000, 9950X OC</td>
                                <td class="text-center text-success"><i class="las la-check-circle fs-18"></i> 100% Dedicated Silicon</td>
                            </tr>
                            <tr>
                                <th scope="row" class="cf-sticky-feature-cell fw-semibold text-slate-800">CPU Core Scalability</th>
                                <td class="text-center">Up to 36 Cores / 72 Threads</td>
                                <td class="text-center popular-col-dedicated fw-semibold text-slate-900">Up to 16 Cores / 32 Threads @ 5.7GHz</td>
                                <td class="text-center">Zero Hypervisor Overhead</td>
                            </tr>
                            <tr>
                                <th scope="row" class="cf-sticky-feature-cell fw-semibold text-slate-800">Single-Tenant Physical Isolation</th>
                                <td class="text-center text-success"><i class="las la-check-circle fs-18"></i> 100% Dedicated Hardware</td>
                                <td class="text-center popular-col-dedicated text-success"><i class="las la-check-circle fs-18"></i> 100% Dedicated Hardware</td>
                                <td class="text-center text-success"><i class="las la-check-circle fs-18"></i> Zero Noisy Neighbors</td>
                            </tr>
                            <tr>
                                <th scope="row" class="cf-sticky-feature-cell fw-semibold text-slate-800">Full Root &amp; BIOS Control</th>
                                <td class="text-center text-success"><i class="las la-check fs-18"></i> Full Root Access</td>
                                <td class="text-center popular-col-dedicated text-success"><i class="las la-check fs-18"></i> Full Root Access</td>
                                <td class="text-center text-success"><i class="las la-check fs-18"></i> Included</td>
                            </tr>

                            <!-- Category 2: Storage & RAM -->
                            <tr class="cf-cat-header-row">
                                <td colspan="4">
                                    <i class="las la-hdd"></i> 2. Memory &amp; Enterprise Storage Arrays
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="cf-sticky-feature-cell fw-semibold text-slate-800">Maximum RAM Capacity</th>
                                <td class="text-center">Up to 512 GB DDR4 Registered ECC</td>
                                <td class="text-center popular-col-dedicated fw-semibold text-slate-900">Up to 512 GB High-Speed DDR5 ECC</td>
                                <td class="text-center">Hardware Error-Checking (ECC)</td>
                            </tr>
                            <tr>
                                <th scope="row" class="cf-sticky-feature-cell fw-semibold text-slate-800">Storage Technology</th>
                                <td class="text-center">Enterprise SSD &amp; Gen4 NVMe</td>
                                <td class="text-center popular-col-dedicated fw-semibold text-slate-900">Ultra-Fast Gen4 &amp; Gen5 NVMe Arrays</td>
                                <td class="text-center">Hardware RAID Ready</td>
                            </tr>
                            <tr>
                                <th scope="row" class="cf-sticky-feature-cell fw-semibold text-slate-800">Maximum NVMe Storage</th>
                                <td class="text-center">Up to 8 TB Enterprise NVMe</td>
                                <td class="text-center popular-col-dedicated fw-semibold text-slate-900">Up to 8 TB Gen5 Enterprise NVMe</td>
                                <td class="text-center">Hot-Swap Drive Bays</td>
                            </tr>

                            <!-- Category 3: Network & IPMI Control -->
                            <tr class="cf-cat-header-row">
                                <td colspan="4">
                                    <i class="las la-network-wired"></i> 3. Network, Uplink &amp; Remote Management
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="cf-sticky-feature-cell fw-semibold text-slate-800">Dedicated Port Uplink</th>
                                <td class="text-center">1Gbps Dedicated Uplink</td>
                                <td class="text-center popular-col-dedicated fw-semibold text-slate-900">1Gbps Dedicated Uplink</td>
                                <td class="text-center">10Gbps Burstable Option</td>
                            </tr>
                            <tr>
                                <th scope="row" class="cf-sticky-feature-cell fw-semibold text-slate-800">Bandwidth Policy</th>
                                <td class="text-center text-success"><i class="las la-infinity fs-18"></i> Unmetered Traffic</td>
                                <td class="text-center popular-col-dedicated text-success"><i class="las la-infinity fs-18"></i> Unmetered Traffic</td>
                                <td class="text-center text-success">Zero Over-Quota Charges</td>
                            </tr>
                            <tr>
                                <th scope="row" class="cf-sticky-feature-cell fw-semibold text-slate-800">Out-of-Band IPMI / iDRAC / KVM</th>
                                <td class="text-center text-success"><i class="las la-check-circle fs-18"></i> 24/7 Remote KVM Access</td>
                                <td class="text-center popular-col-dedicated text-success"><i class="las la-check-circle fs-18"></i> 24/7 Remote KVM Access</td>
                                <td class="text-center">Virtual ISO Mounting Ready</td>
                            </tr>
                            <tr>
                                <th scope="row" class="cf-sticky-feature-cell fw-semibold text-slate-800">Anti-DDoS Mitigation</th>
                                <td class="text-center">Multi-Tbps Always-On Shield</td>
                                <td class="text-center popular-col-dedicated fw-semibold text-slate-900">Multi-Tbps Always-On Shield</td>
                                <td class="text-center text-success">Layer 3, 4 &amp; 7 Protection</td>
                            </tr>

                            <!-- Category 4: Support & SLAs -->
                            <tr class="cf-cat-header-row">
                                <td colspan="4">
                                    <i class="las la-shield-alt"></i> 4. Operating Systems, Deployments &amp; SLAs
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="cf-sticky-feature-cell fw-semibold text-slate-800">Supported Operating Systems</th>
                                <td class="text-center">Ubuntu, Debian, AlmaLinux, Windows Server, Proxmox, VMware</td>
                                <td class="text-center popular-col-dedicated fw-semibold text-slate-900">Ubuntu, Debian, AlmaLinux, Windows Server, Proxmox, ESXi</td>
                                <td class="text-center">Custom ISO Supported</td>
                            </tr>
                            <tr>
                                <th scope="row" class="cf-sticky-feature-cell fw-semibold text-slate-800">Hardware Replacement SLA</th>
                                <td class="text-center">&lt; 4 Hours Hardware SLA</td>
                                <td class="text-center popular-col-dedicated fw-semibold text-slate-900">&lt; 4 Hours Hardware SLA</td>
                                <td class="text-center text-success">24/7 On-Site Technicians</td>
                            </tr>
                            <tr>
                                <th scope="row" class="cf-sticky-feature-cell fw-semibold text-slate-800">Uptime SLA Guarantee</th>
                                <td class="text-center fw-bold text-slate-900">99.99% Network SLA</td>
                                <td class="text-center popular-col-dedicated fw-bold text-crimson">99.99% Network SLA</td>
                                <td class="text-center fw-bold text-success">Financially Backed SLA</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <!-- =========================================================================
         4. BENTO ADVANTAGES (Why CloudFlox Dedicated Servers)
         ========================================================================= -->
    <section class="why-area-one bg-dark pt-120 pb-120 position-relative z-1">
        <div class="container">
            <div class="row align-items-center justify-content-between mb-5">
                <div class="col-xl-7">
                    <span class="badge bg-crimson-subtle text-crimson fw-bold px-3 py-1 rounded-pill mb-2">
                        BARE METAL ARCHITECTURE
                    </span>
                    <h2 class="text-white display-6 fw-extrabold mb-0">
                        Engineered for High-Concurrency &amp; Mission-Critical Workloads
                    </h2>
                </div>
                <div class="col-xl-5 mt-3 mt-xl-0">
                    <p class="text-white text-opacity-75 mb-0 fs-16">
                        Built for ultimate performance, low latency, and single-tenant isolation. Zero noisy neighbors, unmetered 1Gbps network backbone, and multi-Tbps DDoS mitigation for non-stop enterprise operations.
                    </p>
                </div>
            </div>

            <!-- Bento 6 Features Grid -->
            <div class="row g-4">
                <!-- Bento 1 -->
                <div class="col-xl-4 col-md-6">
                    <div class="why-card p-4 rounded-3 h-100 border border-light border-opacity-10" style="background: rgba(255,255,255,0.03);">
                        <div class="d-flex align-items-center justify-content-center rounded-3 mb-4"
                            style="width:56px;height:56px;background:rgba(225,29,72,0.15);color:#E11D48;">
                            <i class="las la-server fs-28"></i>
                        </div>
                        <h5 class="text-white mb-2">100% Single-Tenant Bare Metal</h5>
                        <p class="text-white text-opacity-75 mb-0 fs-14 lh-base">
                            Enjoy dedicated physical CPU, memory, and bus pipelines without hypervisor latency or noisy neighbors. Every clock cycle is completely reserved for your workload.
                        </p>
                    </div>
                </div>

                <!-- Bento 2 -->
                <div class="col-xl-4 col-md-6">
                    <div class="why-card p-4 rounded-3 h-100 border border-light border-opacity-10" style="background: rgba(255,255,255,0.03);">
                        <div class="d-flex align-items-center justify-content-center rounded-3 mb-4"
                            style="width:56px;height:56px;background:rgba(225,29,72,0.15);color:#E11D48;">
                            <i class="las la-bolt fs-28"></i>
                        </div>
                        <h5 class="text-white mb-2">Enterprise Gen4/Gen5 NVMe RAID</h5>
                        <p class="text-white text-opacity-75 mb-0 fs-14 lh-base">
                            Ultra-fast I/O throughput reaching up to 7,500 MB/s per drive. Ideal for high-transaction SQL databases, big data analytics, and intense virtualization clusters.
                        </p>
                    </div>
                </div>

                <!-- Bento 3 -->
                <div class="col-xl-4 col-md-6">
                    <div class="why-card p-4 rounded-3 h-100 border border-light border-opacity-10" style="background: rgba(255,255,255,0.03);">
                        <div class="d-flex align-items-center justify-content-center rounded-3 mb-4"
                            style="width:56px;height:56px;background:rgba(225,29,72,0.15);color:#E11D48;">
                            <i class="las la-network-wired fs-28"></i>
                        </div>
                        <h5 class="text-white mb-2">Unmetered 1Gbps Backbone</h5>
                        <p class="text-white text-opacity-75 mb-0 fs-14 lh-base">
                            Tier-1 upstream transit connections with BGP route optimization. Transfer terabytes of data without bandwidth throttling, transfer caps, or surprise overage fees.
                        </p>
                    </div>
                </div>

                <!-- Bento 4 -->
                <div class="col-xl-4 col-md-6">
                    <div class="why-card p-4 rounded-3 h-100 border border-light border-opacity-10" style="background: rgba(255,255,255,0.03);">
                        <div class="d-flex align-items-center justify-content-center rounded-3 mb-4"
                            style="width:56px;height:56px;background:rgba(225,29,72,0.15);color:#E11D48;">
                            <i class="las la-terminal fs-28"></i>
                        </div>
                        <h5 class="text-white mb-2">Out-of-Band IPMI / iDRAC KVM</h5>
                        <p class="text-white text-opacity-75 mb-0 fs-14 lh-base">
                            Hardware-level console management independent of the OS. Mount custom ISOs, configure RAID in BIOS, and perform hard power cycles remotely 24/7.
                        </p>
                    </div>
                </div>

                <!-- Bento 5 -->
                <div class="col-xl-4 col-md-6">
                    <div class="why-card p-4 rounded-3 h-100 border border-light border-opacity-10" style="background: rgba(255,255,255,0.03);">
                        <div class="d-flex align-items-center justify-content-center rounded-3 mb-4"
                            style="width:56px;height:56px;background:rgba(225,29,72,0.15);color:#E11D48;">
                            <i class="las la-shield-alt fs-28"></i>
                        </div>
                        <h5 class="text-white mb-2">Multi-Tbps DDoS Mitigation</h5>
                        <p class="text-white text-opacity-75 mb-0 fs-14 lh-base">
                            Always-on volumetric scrubbing filters malicious UDP/SYN floods and protocol-level exploits in milliseconds before they ever reach your dedicated network interface.
                        </p>
                    </div>
                </div>

                <!-- Bento 6 -->
                <div class="col-xl-4 col-md-6">
                    <div class="why-card p-4 rounded-3 h-100 border border-light border-opacity-10" style="background: rgba(255,255,255,0.03);">
                        <div class="d-flex align-items-center justify-content-center rounded-3 mb-4"
                            style="width:56px;height:56px;background:rgba(225,29,72,0.15);color:#E11D48;">
                            <i class="las la-tools fs-28"></i>
                        </div>
                        <h5 class="text-white mb-2">&lt; 4-Hour Hardware Replacement SLA</h5>
                        <p class="text-white text-opacity-75 mb-0 fs-14 lh-base">
                            Our datacenters are staffed 24/7 by certified hardware technicians with on-site spare memory, drives, and motherboards for rapid, guaranteed component swaps.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Key Infrastructure Metrics -->
            <div class="mt-5 pt-4 border-top border-light border-opacity-10">
                <div class="row g-4 text-center">
                    <div class="col-md-4">
                        <div class="p-3">
                            <h3 class="text-white fw-extrabold mb-1"><span class="text-crimson">99.99%</span> SLA</h3>
                            <p class="text-white text-opacity-75 fs-14 mb-0">Hardware &amp; Network Availability Guarantee</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3">
                            <h3 class="text-white fw-extrabold mb-1"><span class="text-crimson">1 Gbps</span> Unmetered</h3>
                            <p class="text-white text-opacity-75 fs-14 mb-0">Zero Bandwidth Throttling &bull; Tier-1 Transit</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3">
                            <h3 class="text-white fw-extrabold mb-1"><span class="text-crimson">24/7/365</span> Datacenter Ops</h3>
                            <p class="text-white text-opacity-75 fs-14 mb-0">On-Site Engineers &amp; Rapid Hardware Hot-Swap</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- =========================================================================
         5. CUSTOM BARE METAL CLUSTERS & PRIVATE RACK BANNER
         ========================================================================= -->
    <section class="py-5 bg-slate-50 border-bottom">
        <div class="container">
            <div class="p-4 p-md-5 rounded-4 bg-white border shadow-sm position-relative overflow-hidden">
                <div class="row align-items-center g-4">
                    <div class="col-12 col-lg-8">
                        <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill bg-crimson-subtle text-crimson fw-bold fs-12 mb-2">
                            <i class="las la-cubes"></i> CUSTOM ENTERPRISE DEPLOYMENTS
                        </div>
                        <h3 class="fw-extrabold text-slate-900 mb-2">
                            Need a Multi-Server Cluster, Private VLANs, or a Dedicated Rack?
                        </h3>
                        <p class="text-slate-600 fs-15 mb-0">
                            Our senior enterprise infrastructure architects design, cable, and deploy bespoke multi-node bare-metal clusters with 10Gbps/40Gbps private cross-connects, hardware firewalls, and custom BGP routing.
                        </p>
                    </div>
                    <div class="col-12 col-lg-4 text-lg-end">
                        <a href="https://my.cloudflox.com/submitticket.php" class="cf-btn-dedicated-glow d-inline-flex">
                            <span>Request Custom Quote</span>
                            <i class="las la-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- =========================================================================
         6. FAQ ACCORDION (Dedicated Server Technical Answers)
         ========================================================================= -->
    <section class="pt-60 pb-80 bg-white">
        <div class="container">

            <!-- Section Title -->
            <div class="row justify-content-center">
                <div class="col-xxl-6 col-xl-7 col-lg-8">
                    <div class="text-center mb-5">
                        <span class="badge bg-crimson-subtle text-crimson fw-bold px-3 py-1 rounded-pill mb-2">
                            FREQUENTLY ASKED QUESTIONS
                        </span>
                        <h2 class="display-6 fw-extrabold text-slate-900 mb-3">
                            Dedicated Server FAQs
                        </h2>
                        <p class="text-slate-600 fs-16 mb-0">
                            Everything you need to know about CloudFlox bare metal dedicated servers, provisioning timelines, IPMI remote KVM, and hardware SLAs.
                        </p>
                    </div>
                </div>
            </div>

            <!-- FAQ Accordion -->
            <div class="row justify-content-center">
                <div class="col-xl-8 col-lg-9">
                    <div class="accordion" id="dedicatedFaqAccordion">

                        <!-- Q1 -->
                        <div class="accordion-item border rounded-3 mb-3 overflow-hidden shadow-sm">
                            <h2 class="accordion-header" id="faqHeading1">
                                <button class="accordion-button fw-bold text-slate-900" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse1" aria-expanded="true" aria-controls="faqCollapse1">
                                    What is the difference between a Bare Metal Dedicated Server and a VPS?
                                </button>
                            </h2>
                            <div id="faqCollapse1" class="accordion-collapse collapse show" aria-labelledby="faqHeading1" data-bs-parent="#dedicatedFaqAccordion">
                                <div class="accordion-body text-slate-600 fs-15 lh-base">
                                    A Dedicated Server provides 100% exclusive physical hardware resources. Unlike shared hosting or a VPS where resources are divided among multiple tenants by a hypervisor, all physical CPU cores, RAM modules, NVMe storage controllers, and network ports are reserved exclusively for you with zero hypervisor overhead or noisy neighbor contention.
                                </div>
                            </div>
                        </div>

                        <!-- Q2 -->
                        <div class="accordion-item border rounded-3 mb-3 overflow-hidden shadow-sm">
                            <h2 class="accordion-header" id="faqHeading2">
                                <button class="accordion-button collapsed fw-bold text-slate-900" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse2" aria-expanded="false" aria-controls="faqCollapse2">
                                    What operating systems can I install on CloudFlox dedicated servers?
                                </button>
                            </h2>
                            <div id="faqCollapse2" class="accordion-collapse collapse" aria-labelledby="faqHeading2" data-bs-parent="#dedicatedFaqAccordion">
                                <div class="accordion-body text-slate-600 fs-15 lh-base">
                                    You can deploy any major 64-bit operating system including Ubuntu Server, Debian, AlmaLinux, Rocky Linux, CentOS Stream, and Windows Server (2019/2022/2025). We also fully support hypervisors like Proxmox VE, VMware ESXi, and custom ISO installations via IPMI virtual media.
                                </div>
                            </div>
                        </div>

                        <!-- Q3 -->
                        <div class="accordion-item border rounded-3 mb-3 overflow-hidden shadow-sm">
                            <h2 class="accordion-header" id="faqHeading3">
                                <button class="accordion-button collapsed fw-bold text-slate-900" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse3" aria-expanded="false" aria-controls="faqCollapse3">
                                    Do I get full root access and out-of-band IPMI / KVM control?
                                </button>
                            </h2>
                            <div id="faqCollapse3" class="accordion-collapse collapse" aria-labelledby="faqHeading3" data-bs-parent="#dedicatedFaqAccordion">
                                <div class="accordion-body text-slate-600 fs-15 lh-base">
                                    Yes! Every CloudFlox bare metal dedicated server includes root/administrator access over SSH or RDP. In addition, you get dedicated out-of-band IPMI / iDRAC remote management access, allowing you to access the BIOS, mount virtual ISO images, view boot diagnostics, and perform hard power recycles 24/7.
                                </div>
                            </div>
                        </div>

                        <!-- Q4 -->
                        <div class="accordion-item border rounded-3 mb-3 overflow-hidden shadow-sm">
                            <h2 class="accordion-header" id="faqHeading4">
                                <button class="accordion-button collapsed fw-bold text-slate-900" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse4" aria-expanded="false" aria-controls="faqCollapse4">
                                    How does the multi-Tbps DDoS protection work on dedicated servers?
                                </button>
                            </h2>
                            <div id="faqCollapse4" class="accordion-collapse collapse" aria-labelledby="faqHeading4" data-bs-parent="#dedicatedFaqAccordion">
                                <div class="accordion-body text-slate-600 fs-15 lh-base">
                                    All dedicated servers are routed through our real-time multi-Tbps automated scrubbing appliances. Volumetric attack vectors (such as UDP reflection, ICMP floods, and SYN floods) are identified and scrubbed in under one second, ensuring that your server's 1Gbps uplink remains clear and fully operational.
                                </div>
                            </div>
                        </div>

                        <!-- Q5 -->
                        <div class="accordion-item border rounded-3 mb-3 overflow-hidden shadow-sm">
                            <h2 class="accordion-header" id="faqHeading5">
                                <button class="accordion-button collapsed fw-bold text-slate-900" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse5" aria-expanded="false" aria-controls="faqCollapse5">
                                    How quickly are CloudFlox bare metal servers provisioned?
                                </button>
                            </h2>
                            <div id="faqCollapse5" class="accordion-collapse collapse" aria-labelledby="faqHeading5" data-bs-parent="#dedicatedFaqAccordion">
                                <div class="accordion-body text-slate-600 fs-15 lh-base">
                                    Most standard Intel and AMD server configurations are provisioned automatically within 2 to 4 hours following fraud verification and payment confirmation. Custom hardware configurations (such as 512GB RAM, multi-drive NVMe RAID, or private racks) are racked and cabled by our datacenter staff within 12 to 24 hours.
                                </div>
                            </div>
                        </div>

                        <!-- Q6 -->
                        <div class="accordion-item border rounded-3 mb-3 overflow-hidden shadow-sm">
                            <h2 class="accordion-header" id="faqHeading6">
                                <button class="accordion-button collapsed fw-bold text-slate-900" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse6" aria-expanded="false" aria-controls="faqCollapse6">
                                    Can I upgrade RAM, add extra NVMe drives, or order additional IP addresses?
                                </button>
                            </h2>
                            <div id="faqCollapse6" class="accordion-collapse collapse" aria-labelledby="faqHeading6" data-bs-parent="#dedicatedFaqAccordion">
                                <div class="accordion-body text-slate-600 fs-15 lh-base">
                                    Yes. All CloudFlox dedicated servers support seamless hardware expansion. You can request additional enterprise NVMe drives, RAM upgrades up to 512GB, additional routed IPv4 blocks (/29, /28, /27), and 10Gbps burstable uplinks at any time by opening a ticket with our infrastructure team.
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

    <!-- =========== Scripts Area =========== -->
    <?php include 'include/js.inc.php'; ?>
    <!-- =========== Scripts Area =========== -->

    <!-- Interactive Architecture Switcher, Table Search & Sticky Header Tracker Script -->
    <script>
    (function () {
        // Architecture Switcher (Intel vs AMD)
        const btnIntel = document.getElementById('btnArchIntel');
        const btnAmd = document.getElementById('btnArchAmd');
        const paneIntel = document.getElementById('paneIntel');
        const paneAmd = document.getElementById('paneAmd');

        if (btnIntel && btnAmd && paneIntel && paneAmd) {
            btnIntel.addEventListener('click', function () {
                btnIntel.classList.add('active', 'intel-active');
                btnAmd.classList.remove('active', 'amd-active');
                paneIntel.classList.add('show', 'active');
                paneAmd.classList.remove('show', 'active');
            });

            btnAmd.addEventListener('click', function () {
                btnAmd.classList.add('active', 'amd-active');
                btnIntel.classList.remove('active', 'intel-active');
                paneAmd.classList.add('show', 'active');
                paneIntel.classList.remove('show', 'active');
            });
        }

        // Live Table Search Filter for Intel
        const intelSearch = document.getElementById('intelSearchInput');
        if (intelSearch) {
            intelSearch.addEventListener('input', function () {
                const term = this.value.toLowerCase().trim();
                const rows = document.querySelectorAll('#intelServerTable tbody tr.server-row');
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(term) ? '' : 'none';
                });
            });
        }

        // Live Table Search Filter for AMD
        const amdSearch = document.getElementById('amdSearchInput');
        if (amdSearch) {
            amdSearch.addEventListener('input', function () {
                const term = this.value.toLowerCase().trim();
                const rows = document.querySelectorAll('#amdServerTable tbody tr.server-row');
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(term) ? '' : 'none';
                });
            });
        }

        // Dynamic Sticky Table Header Offset Tracker
        function updateStickyTableHeader() {
            const header = document.querySelector('.cf-header-wrapper');
            if (header) {
                const rect = header.getBoundingClientRect();
                const offset = Math.max(0, Math.round(rect.bottom));
                document.documentElement.style.setProperty('--cf-header-h', offset + 'px');
            }
        }
        window.addEventListener('scroll', updateStickyTableHeader, { passive: true });
        window.addEventListener('resize', updateStickyTableHeader, { passive: true });
        updateStickyTableHeader();
    })();
    </script>
</body>

</html>