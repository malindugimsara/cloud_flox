<?php include 'include/url.inc.php'; ?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">

<head>
    <!-- Basic Meta -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Title -->
    <title>High-Performance Linux Cloud VPS Hosting | CloudFlox</title>

    <!-- SEO Meta -->
    <meta name="description"
        content="Deploy scalable, isolated Linux Cloud VPS hosting with CloudFlox starting at just $7.99/mo. High-speed NVMe/SSD storage, full root access, 1Gbps network, and 99.95% uptime SLA.">
    <meta name="keywords"
        content="linux cloud vps, kvm vps hosting, linux vps hosting, affordable vps, cloudflox vps, launch scale pro power vps, root access vps">
    <meta name="author" content="CloudFlox">
    <link rel="canonical" href="https://cloudflox.com/vps-hosting">

    <!-- Open Graph (Facebook / LinkedIn) -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="High-Performance Linux Cloud VPS Hosting | CloudFlox">
    <meta property="og:description"
        content="Launch scalable Linux Cloud VPS servers with KVM isolation, NVMe/SSD speed, 1Gbps network, and full root access starting at $7.99/mo.">
    <meta property="og:url" content="https://cloudflox.com/vps-hosting">
    <meta property="og:image" content="https://cloudflox.com/assets/imgs/banners/cloudflox-vps-infrastructure.webp">
    <meta property="og:site_name" content="CloudFlox">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="High-Performance Linux Cloud VPS Hosting | CloudFlox">
    <meta name="twitter:description"
        content="Deploy scalable KVM Linux virtual private servers with 1Gbps uplink, full root access, and 99.95% SLA starting at $7.99/mo.">
    <meta name="twitter:image" content="https://cloudflox.com/assets/imgs/banners/cloudflox-vps-infrastructure.webp">

    <!-- Structured Data (JSON-LD) for SEO -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@graph": [
        {
          "@type": "Product",
          "name": "CloudFlox Linux Cloud VPS Hosting",
          "description": "High-performance Linux virtual private servers with KVM isolation, NVMe/SSD storage, full root access, and 1Gbps network.",
          "brand": {
            "@type": "Brand",
            "name": "CloudFlox"
          },
          "offers": {
            "@type": "AggregateOffer",
            "priceCurrency": "USD",
            "lowPrice": "7.99",
            "highPrice": "44.99",
            "offerCount": "4"
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
              "name": "VPS Hosting",
              "item": "https://cloudflox.com/vps-hosting"
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

<body class="pure-white-theme cf-vps-page">

    <!-- =========== Header Area =========== -->
    <?php include 'include/header.inc.php'; ?>
    <!-- =========== Header Area =========== -->

    <!-- =========================================================================
         1. HERO SECTION (High-Performance KVM Cloud VPS)
         ========================================================================= -->
    <section class="cf-vps-hero position-relative">
        <div class="hero-ambient-glow" aria-hidden="true"></div>

        <!-- Ambient Decorative Pattern Backgrounds -->
        <!-- <div class="pricing-bg-shape pricing-shape-left" aria-hidden="true" style="opacity: 0.45;">
            <img src="assets/imgs/shape/pattern-2.svg?v=2" alt="" width="370" height="590" loading="eager" decoding="async">
        </div>
        <div class="pricing-bg-shape pricing-shape-right" aria-hidden="true" style="opacity: 0.45;">
            <img src="assets/imgs/shape/pattern-3.svg?v=2" alt="" width="370" height="590" loading="eager" decoding="async">
        </div> -->

        <div class="container position-relative z-2">
            <!-- Breadcrumbs -->
            <nav aria-label="breadcrumb" class="mb-3 pt-2">
                <ol class="breadcrumb custom-breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="<?php echo $url; ?>" class="text-decoration-none text-muted">
                            <i class="las la-home me-1"></i>Home
                        </a>
                    </li>
                    <li class="breadcrumb-item active text-cyan fw-semibold" aria-current="page">VPS Hosting</li>
                </ol>
            </nav>

            <div class="row align-items-center g-4 g-xl-5 pb-lg-3">
                <!-- Left Column: Copy & Value Proposition -->
                <div class="col-12 col-lg-7">
                    <!-- Hero Badge -->
                    <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill bg-cyan-subtle text-cyan fw-bold fs-13 mb-3 border border-cyan-subtle">
                        <span class="cf-pulse-dot-cyan"></span>
                        <span>High-Speed KVM Cloud VPS</span>
                    </div>

                    <h1 class="cf-vps-hero-title fw-extrabold text-slate-900 tracking-tight mb-3">
                        High-Speed KVM <br class="d-none d-sm-inline">
                        <span class="text-gradient-vps">Cloud VPS Hosting</span>
                    </h1>

                    <p class="cf-vps-hero-lead text-slate-600 mb-4 lh-base">
                        Deploy scalable virtual private servers with dedicated NVMe storage, full root access, and instant cloud provisioning.
                    </p>

                    <!-- Trust Chips -->
                    <div class="d-flex flex-wrap gap-2 mb-4 pb-1">
                        <span class="cf-feature-chip"><i class="las la-microchip text-primary"></i> High-Speed vCPU</span>
                        <span class="cf-feature-chip"><i class="las la-bolt text-warning"></i> Pure NVMe Storage</span>
                        <span class="cf-feature-chip"><i class="las la-terminal text-success"></i> Full Root Access</span>
                        <span class="cf-feature-chip"><i class="las la-shield-alt text-danger"></i> 1Gbps Anti-DDoS</span>
                    </div>

                    <!-- Hero Action Buttons -->
                    <div class="d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center gap-3 cf-hero-btn-group">
                        <a href="#vpsPlans" class="cf-btn-vps-glow text-center">
                            <span>Explore Plans</span>
                            <i class="las la-arrow-down ms-1"></i>
                        </a>
                        <a href="#vpsConfigurator" class="cf-btn-secondary-light text-center">
                            <i class="las la-sliders-h me-1"></i>
                            <span>Custom Configurator</span>
                        </a>
                    </div>
                </div>

                <!-- Right Column: Visual Hero Infrastructure Showcase -->
                <div class="col-12 col-lg-5">
                    <div class="cf-budget-hero-visual position-relative text-center">
                        <div class="cf-hero-img-wrap position-relative mx-auto">
                            <img src="assets/imgs/banners/cloudflox-vps-infrastructure.webp"
                                 alt="CloudFlox High-Performance KVM Cloud VPS Hosting Infrastructure"
                                 class="cf-hero-illustration img-fluid"
                                 width="900"
                                 height="675"
                                 fetchpriority="high"
                                 loading="eager">

                            <!-- Floating Price Tag (Top Left) -->
                            <div class="cf-hero-float-chip float-chip-top shadow-sm">
                                <span class="cf-pulse-dot-cyan"></span>
                                <div class="text-start">
                                    <span class="d-block cf-chip-sub">Linux Cloud VPS From</span>
                                    <strong class="cf-chip-val text-cyan">$7.99<small class="text-muted fw-normal">/mo</small></strong>
                                </div>
                                <span class="badge bg-cyan-subtle text-cyan fw-bold px-2 py-1 rounded-pill ms-2 fs-11">
                                    Root Access
                                </span>
                            </div>

                            <!-- Floating Specs Tag (Bottom Right) -->
                            <div class="cf-hero-float-chip float-chip-bottom shadow-sm">
                                <div class="cf-chip-icon bg-cyan-subtle text-cyan">
                                    <i class="las la-network-wired"></i>
                                </div>
                                <div class="text-start">
                                    <span class="d-block cf-chip-sub">1Gbps Port &amp; DDoS Shield</span>
                                    <strong class="cf-chip-val text-slate-900">Dedicated IPv4 + Full Root</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- =========================================================================
         2. PRICING & PLANS SECTION (Linux & Windows VPS with Monthly/Annual Toggle)
         ========================================================================= -->
    <section class="modern-pricing-section position-relative py-5" id="vpsPlans">
        <!-- Ambient Decorative Pattern Backgrounds -->
        <div class="pricing-bg-shape pricing-shape-left" aria-hidden="true">
            <img src="assets/imgs/shape/pattern-2.svg?v=2" alt="pattern-2" width="370" height="590" loading="eager" decoding="async">
        </div>
        <div class="pricing-bg-shape pricing-shape-right" aria-hidden="true">
            <img src="assets/imgs/shape/pattern-3.svg?v=2" alt="pattern-3" width="370" height="590" loading="eager" decoding="async">
        </div>

        <div class="container position-relative z-2">
            <!-- Section Header -->
            <div class="text-center max-w-750 mx-auto mb-4 mb-md-5">
                <span class="badge bg-cyan-subtle text-cyan fw-bold px-3 py-1 rounded-pill mb-2 border border-cyan-subtle">
                    PROVISIONAL LINUX VPS PRICING
                </span>
                <h2 class="display-6 fw-extrabold text-slate-900 mb-2">
                    Scalable Cloud VPS Plans Tailored to Your Workload
                </h2>
                <p class="text-slate-600 fs-16 mb-0">
                    Launch, Scale, Pro, and Power tiers engineered for developers and growing businesses. Transparent monthly USD pricing with full root control and KVM isolation.
                </p>
            </div>

            <!-- OS Architecture Switcher (Linux VPS vs Windows VPS) -->
            <div class="d-flex justify-content-center mb-3">
                <div class="cf-vps-os-switch" role="tablist">
                    <button type="button" class="cf-vps-os-btn active linux-active" id="btnOsLinux" role="tab" aria-selected="true" aria-controls="paneLinux">
                        <i class="lab la-linux fs-18"></i>
                        <span class="cf-os-tag">Linux VPS</span>
                        <span class="cf-os-badge-count">4 Plans</span>
                    </button>
                    <button type="button" class="cf-vps-os-btn" id="btnOsWindows" role="tab" aria-selected="false" aria-controls="paneWindows">
                        <i class="lab la-windows fs-18"></i>
                        <span class="cf-os-tag">Windows VPS</span>
                        <span class="cf-os-badge-count">On Demand</span>
                    </button>
                </div>
            </div>

            <!-- Provisional Status Notice Pill -->
            <div class="d-flex justify-content-center mb-4 mb-md-5">
                <div class="d-inline-flex align-items-center gap-2 px-3 py-1-5 rounded-pill bg-light border border-slate-200 text-slate-700 fs-13">
                    <i class="las la-tag text-cyan fs-15"></i>
                    <span><strong>Proposed Monthly Rates:</strong> Billed in USD &bull; WHMCS Provisioning Verification in Progress</span>
                </div>
            </div>

            <!-- TAB PANE: LINUX VPS (4 TIERS) -->
            <div id="paneLinux">
                <div class="row g-4 align-items-stretch justify-content-center">

                    <!-- Plan 1: Launch VPS ($7.99) -->
                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="cf-vps-card">
                            <div class="cf-vps-card-header">
                                <div class="cf-vps-icon-wrap bg-cyan-subtle text-cyan">
                                    <i class="las la-rocket"></i>
                                </div>
                                <div>
                                    <span class="badge bg-light text-slate-700 fw-bold fs-11 px-2 py-1 rounded-pill mb-1">Entry Tier</span>
                                    <h3 class="cf-vps-card-title">Launch VPS</h3>
                                    <p class="cf-vps-card-sub">Dev, Testing &amp; Microservices</p>
                                </div>
                            </div>

                            <div class="cf-vps-price-wrap">
                                <div class="d-flex align-items-baseline justify-content-center gap-1">
                                    <span class="cf-vps-amount" data-monthly="7.99">$7.99</span>
                                    <span class="cf-vps-period">/ month</span>
                                </div>
                                <div class="fs-12 text-muted mt-1 billing-note">Proposed monthly rate &bull; ex VAT</div>
                            </div>

                            <ul class="cf-vps-specs-list">
                                <li>
                                    <span class="cf-vps-spec-icon"><i class="las la-microchip"></i></span>
                                    <span><strong>1 vCPU</strong> High-Speed Core</span>
                                </li>
                                <li>
                                    <span class="cf-vps-spec-icon"><i class="las la-memory"></i></span>
                                    <span><strong>2 GB</strong> High-Performance RAM</span>
                                </li>
                                <li>
                                    <span class="cf-vps-spec-icon"><i class="las la-hdd"></i></span>
                                    <span><strong>30 GB</strong> Pure NVMe / SSD</span>
                                </li>
                                <li>
                                    <span class="cf-vps-spec-icon"><i class="las la-network-wired"></i></span>
                                    <span><strong>1 Gbit</strong> Port Uplink</span>
                                </li>
                                <li>
                                    <span class="cf-vps-spec-icon"><i class="las la-exchange-alt"></i></span>
                                    <span><strong>1 TB</strong> Monthly Data Transfer</span>
                                </li>
                                <li>
                                    <span class="cf-vps-spec-icon"><i class="las la-globe"></i></span>
                                    <span><strong>1 Dedicated</strong> IPv4 Address</span>
                                </li>
                                <li>
                                    <span class="cf-vps-spec-icon"><i class="las la-shield-alt"></i></span>
                                    <span><strong>Multi-Tbps</strong> DDoS Defense</span>
                                </li>
                                <li>
                                    <span class="cf-vps-spec-icon"><i class="las la-terminal"></i></span>
                                    <span><strong>Full Root</strong> Access &amp; Web KVM</span>
                                </li>
                                <li>
                                    <span class="cf-vps-spec-icon"><i class="las la-check-circle"></i></span>
                                    <span><strong>99.95%</strong> Network SLA</span>
                                </li>
                            </ul>

                            <a href="<?php echo $url; ?>request-quote?pid=401" class="cf-btn-vps-order" data-pid="401">
                                <span>Request Quote &amp; Deploy</span>
                                <i class="las la-arrow-right"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Plan 2: Scale VPS ($14.99) -->
                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="cf-vps-card">
                            <div class="cf-vps-card-header">
                                <div class="cf-vps-icon-wrap bg-cyan-subtle text-cyan">
                                    <i class="las la-server"></i>
                                </div>
                                <div>
                                    <span class="badge bg-light text-slate-700 fw-bold fs-11 px-2 py-1 rounded-pill mb-1">Growth Tier</span>
                                    <h3 class="cf-vps-card-title">Scale VPS</h3>
                                    <p class="cf-vps-card-sub">Fast-Growing Apps &amp; APIs</p>
                                </div>
                            </div>

                            <div class="cf-vps-price-wrap">
                                <div class="d-flex align-items-baseline justify-content-center gap-1">
                                    <span class="cf-vps-amount" data-monthly="14.99">$14.99</span>
                                    <span class="cf-vps-period">/ month</span>
                                </div>
                                <div class="fs-12 text-muted mt-1 billing-note">Proposed monthly rate &bull; ex VAT</div>
                            </div>

                            <ul class="cf-vps-specs-list">
                                <li>
                                    <span class="cf-vps-spec-icon"><i class="las la-microchip"></i></span>
                                    <span><strong>2 vCPU</strong> High-Speed Cores</span>
                                </li>
                                <li>
                                    <span class="cf-vps-spec-icon"><i class="las la-memory"></i></span>
                                    <span><strong>4 GB</strong> High-Performance RAM</span>
                                </li>
                                <li>
                                    <span class="cf-vps-spec-icon"><i class="las la-hdd"></i></span>
                                    <span><strong>60 GB</strong> Pure NVMe / SSD</span>
                                </li>
                                <li>
                                    <span class="cf-vps-spec-icon"><i class="las la-network-wired"></i></span>
                                    <span><strong>1 Gbit</strong> Port Uplink</span>
                                </li>
                                <li>
                                    <span class="cf-vps-spec-icon"><i class="las la-exchange-alt"></i></span>
                                    <span><strong>2 TB</strong> Monthly Data Transfer</span>
                                </li>
                                <li>
                                    <span class="cf-vps-spec-icon"><i class="las la-globe"></i></span>
                                    <span><strong>1 Dedicated</strong> IPv4 Address</span>
                                </li>
                                <li>
                                    <span class="cf-vps-spec-icon"><i class="las la-shield-alt"></i></span>
                                    <span><strong>Multi-Tbps</strong> DDoS Defense</span>
                                </li>
                                <li>
                                    <span class="cf-vps-spec-icon"><i class="las la-terminal"></i></span>
                                    <span><strong>Full Root</strong> Access &amp; Web KVM</span>
                                </li>
                                <li>
                                    <span class="cf-vps-spec-icon"><i class="las la-check-circle"></i></span>
                                    <span><strong>99.95%</strong> Network SLA</span>
                                </li>
                            </ul>

                            <a href="<?php echo $url; ?>request-quote?pid=402" class="cf-btn-vps-order" data-pid="402">
                                <span>Request Quote &amp; Deploy</span>
                                <i class="las la-arrow-right"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Plan 3: Pro VPS ($29.99 - Popular / Best Value) -->
                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="cf-vps-card popular">
                            <div class="cf-vps-popular-badge">
                                <i class="las la-fire me-1"></i> Most Popular Tier
                            </div>

                            <div class="cf-vps-card-header">
                                <div class="cf-vps-icon-wrap bg-cyan-subtle text-cyan">
                                    <i class="las la-bolt"></i>
                                </div>
                                <div>
                                    <span class="badge bg-cyan-subtle text-cyan fw-bold fs-11 px-2 py-1 rounded-pill mb-1">Production Ready</span>
                                    <h3 class="cf-vps-card-title">Pro VPS</h3>
                                    <p class="cf-vps-card-sub">Websites, SaaS &amp; Databases</p>
                                </div>
                            </div>

                            <div class="cf-vps-price-wrap" style="background: rgba(6, 182, 212, 0.05); border-color: rgba(6, 182, 212, 0.2);">
                                <div class="d-flex align-items-baseline justify-content-center gap-1">
                                    <span class="cf-vps-amount text-cyan" data-monthly="29.99">$29.99</span>
                                    <span class="cf-vps-period">/ month</span>
                                </div>
                                <div class="fs-12 text-muted mt-1 billing-note">
                                    Best performance value &bull; ex VAT
                                </div>
                            </div>

                            <ul class="cf-vps-specs-list">
                                <li>
                                    <span class="cf-vps-spec-icon"><i class="las la-microchip"></i></span>
                                    <span><strong>4 vCPU</strong> High-Speed Cores</span>
                                </li>
                                <li>
                                    <span class="cf-vps-spec-icon"><i class="las la-memory"></i></span>
                                    <span><strong>8 GB</strong> High-Performance RAM</span>
                                </li>
                                <li>
                                    <span class="cf-vps-spec-icon"><i class="las la-hdd"></i></span>
                                    <span><strong>120 GB</strong> Pure NVMe / SSD</span>
                                </li>
                                <li>
                                    <span class="cf-vps-spec-icon"><i class="las la-network-wired"></i></span>
                                    <span><strong>1 Gbit</strong> Priority Port Uplink</span>
                                </li>
                                <li>
                                    <span class="cf-vps-spec-icon"><i class="las la-exchange-alt"></i></span>
                                    <span><strong>4 TB</strong> Monthly Data Transfer</span>
                                </li>
                                <li>
                                    <span class="cf-vps-spec-icon"><i class="las la-globe"></i></span>
                                    <span><strong>1 Dedicated</strong> IPv4 Address</span>
                                </li>
                                <li>
                                    <span class="cf-vps-spec-icon"><i class="las la-shield-alt"></i></span>
                                    <span><strong>Advanced</strong> DDoS Packet Scrubbing</span>
                                </li>
                                <li>
                                    <span class="cf-vps-spec-icon"><i class="las la-terminal"></i></span>
                                    <span><strong>Full Root</strong> Access &amp; Web KVM</span>
                                </li>
                                <li>
                                    <span class="cf-vps-spec-icon"><i class="las la-headset"></i></span>
                                    <span><strong>24/7 Priority</strong> Human Support</span>
                                </li>
                            </ul>

                            <a href="<?php echo $url; ?>request-quote?pid=403" class="cf-btn-vps-order" data-pid="403">
                                <span>Request Quote &amp; Deploy</span>
                                <i class="las la-arrow-right"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Plan 4: Power VPS ($44.99) -->
                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="cf-vps-card">
                            <div class="cf-vps-card-header">
                                <div class="cf-vps-icon-wrap bg-cyan-subtle text-cyan">
                                    <i class="las la-cubes"></i>
                                </div>
                                <div>
                                    <span class="badge bg-light text-slate-700 fw-bold fs-11 px-2 py-1 rounded-pill mb-1">High Performance</span>
                                    <h3 class="cf-vps-card-title">Power VPS</h3>
                                    <p class="cf-vps-card-sub">Heavy Stacks &amp; Clusters</p>
                                </div>
                            </div>

                            <div class="cf-vps-price-wrap">
                                <div class="d-flex align-items-baseline justify-content-center gap-1">
                                    <span class="cf-vps-amount" data-monthly="44.99">$44.99</span>
                                    <span class="cf-vps-period">/ month</span>
                                </div>
                                <div class="fs-12 text-muted mt-1 billing-note">Proposed monthly rate &bull; ex VAT</div>
                            </div>

                            <ul class="cf-vps-specs-list">
                                <li>
                                    <span class="cf-vps-spec-icon"><i class="las la-microchip"></i></span>
                                    <span><strong>8 vCPU</strong> High-Speed Cores</span>
                                </li>
                                <li>
                                    <span class="cf-vps-spec-icon"><i class="las la-memory"></i></span>
                                    <span><strong>16 GB</strong> High-Performance RAM</span>
                                </li>
                                <li>
                                    <span class="cf-vps-spec-icon"><i class="las la-hdd"></i></span>
                                    <span><strong>240 GB</strong> Pure NVMe / SSD</span>
                                </li>
                                <li>
                                    <span class="cf-vps-spec-icon"><i class="las la-network-wired"></i></span>
                                    <span><strong>1 Gbit</strong> Redundant Port Uplink</span>
                                </li>
                                <li>
                                    <span class="cf-vps-spec-icon"><i class="las la-exchange-alt"></i></span>
                                    <span><strong>8 TB</strong> Monthly Data Transfer</span>
                                </li>
                                <li>
                                    <span class="cf-vps-spec-icon"><i class="las la-globe"></i></span>
                                    <span><strong>1 Dedicated</strong> IPv4 Address</span>
                                </li>
                                <li>
                                    <span class="cf-vps-spec-icon"><i class="las la-shield-alt"></i></span>
                                    <span><strong>Advanced</strong> AI DDoS Mitigation</span>
                                </li>
                                <li>
                                    <span class="cf-vps-spec-icon"><i class="las la-terminal"></i></span>
                                    <span><strong>Full Root</strong> Access &amp; Web KVM</span>
                                </li>
                                <li>
                                    <span class="cf-vps-spec-icon"><i class="las la-user-tie"></i></span>
                                    <span><strong>24/7 Dedicated</strong> Engineering Support</span>
                                </li>
                            </ul>

                            <a href="<?php echo $url; ?>request-quote?pid=404" class="cf-btn-vps-order" data-pid="404">
                                <span>Request Quote &amp; Deploy</span>
                                <i class="las la-arrow-right"></i>
                            </a>
                        </div>
                    </div>

                </div>
            </div>

            <!-- TAB PANE: WINDOWS VPS (On-Demand / Quote based without publishing unverified prices) -->
            <div id="paneWindows" class="d-none">
                <div class="row justify-content-center">
                    <div class="col-12 col-lg-8">
                        <div class="cf-vps-card text-center p-4 p-md-5">
                            <div class="cf-vps-icon-wrap bg-primary-subtle text-primary mx-auto mb-3" style="width: 64px; height: 64px; font-size: 32px;">
                                <i class="lab la-windows"></i>
                            </div>
                            <span class="badge bg-primary-subtle text-primary fw-bold fs-12 px-3 py-1 rounded-pill mb-2">
                                ENTERPRISE WINDOWS SERVER
                            </span>
                            <h3 class="display-6 fw-bold text-slate-900 mb-2">
                                Windows VPS Environments On Demand
                            </h3>
                            <p class="text-slate-600 fs-16 mb-4 max-w-650 mx-auto lh-base">
                                Windows Server 2022 and 2019 editions with full Remote Desktop (RDP) Administrator access, MSSQL, and IIS readiness are available via custom order while automated provisioning integration is verified. Contact our engineering team for instant quotes and custom resource sizing.
                            </p>

                            <div class="row g-3 justify-content-center mb-4 text-start max-w-600 mx-auto">
                                <div class="col-12 col-sm-6">
                                    <div class="d-flex align-items-center gap-2 text-slate-700 fs-14">
                                        <i class="las la-check-circle text-primary fs-18"></i>
                                        <span>Windows Server 2022 / 2019</span>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="d-flex align-items-center gap-2 text-slate-700 fs-14">
                                        <i class="las la-check-circle text-primary fs-18"></i>
                                        <span>Full Remote Desktop (RDP) Admin</span>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="d-flex align-items-center gap-2 text-slate-700 fs-14">
                                        <i class="las la-check-circle text-primary fs-18"></i>
                                        <span>MSSQL &amp; ASP.NET Optimized</span>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="d-flex align-items-center gap-2 text-slate-700 fs-14">
                                        <i class="las la-check-circle text-primary fs-18"></i>
                                        <span>Dedicated IPv4 &amp; 1Gbps Uplink</span>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <a href="contact-us" class="btn btn-primary px-4 py-3 rounded-3 fw-bold fs-15 shadow-sm">
                                    <i class="las la-envelope me-1"></i> Request Windows Server VPS Quote
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Guarantees Ribbon -->
            <div class="pricing-guarantees-wrapper mt-4 mt-md-5">
                <div class="guarantee-badge">
                    <i class="las la-undo text-cyan"></i>
                    <span><strong>30-Day</strong> Money-Back Guarantee</span>
                </div>
                <div class="guarantee-badge">
                    <i class="las la-bolt text-cyan"></i>
                    <span><strong>Automated</strong> Cloud Provisioning</span>
                </div>
                <div class="guarantee-badge">
                    <i class="las la-shield-alt text-cyan"></i>
                    <span><strong>Multi-Tbps</strong> DDoS Mitigation</span>
                </div>
                <div class="guarantee-badge">
                    <i class="las la-headset text-cyan"></i>
                    <span><strong>24/7/365</strong> Human Engineering Support</span>
                </div>
            </div>
        </div>
    </section>

    <!-- =========================================================================
         3. INTERACTIVE VPS RESOURCE CONFIGURATOR (Processor Slider)
         ========================================================================= -->
    <section class="cf-vps-config-section position-relative" id="vpsConfigurator">
        <div class="container position-relative z-2">
            <!-- Header -->
            <div class="text-center max-w-750 mx-auto mb-4 mb-md-5">
                <span class="badge bg-cyan-subtle text-cyan fw-bold px-3 py-1 rounded-pill mb-2 border border-cyan-subtle">
                    CUSTOM RESOURCE SLIDER
                </span>
                <h2 class="display-6 fw-extrabold text-slate-900 mb-2">
                    Build Your Custom VPS Configuration
                </h2>
                <p class="text-slate-600 fs-16 mb-0">
                    Need tailored specifications for your specific application? Slide the processor cores controller to calculate your dynamic resource allocation and estimated monthly cost.
                </p>
            </div>

            <div class="cf-vps-config-card">
                <div class="row g-4 align-items-center">
                    <!-- Left: Slider & Dynamic Specs -->
                    <div class="col-12 col-lg-7">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <h4 class="fw-bold text-slate-900 fs-18 mb-0">Select Processor Cores:</h4>
                            <span class="badge bg-cyan-subtle text-cyan fw-bold px-3 py-1 rounded-pill fs-13">
                                1 to 6 Dedicated Cores
                            </span>
                        </div>
                        <p class="text-slate-600 fs-14 mb-4">
                            Drag the handle below to scale CPU cores, RAM memory, and SSD storage in real-time.
                        </p>

                        <!-- jQuery UI Processor Slider Markup (Preserves app.js integration) -->
                        <div class="vps-slider vps-slider--processor mb-4">
                            <input type="text" class="vps-slider-amount d-none" readonly="">
                            <div class="vps-slider__progress vps-slider__progress-1"></div>
                            <div class="ui-slider-handle vps-slider__controller" tabindex="0">
                                <div id="custom-handle" class="vps-slider__tooltip">1 Core</div>
                            </div>
                        </div>

                        <!-- Readout Specification Grid -->
                        <div class="cf-config-readout-grid">
                            <div class="cf-config-readout-item">
                                <div class="cf-config-readout-icon">
                                    <i class="las la-microchip"></i>
                                </div>
                                <div>
                                    <span class="text-muted fs-12 d-block">Allocated Compute</span>
                                    <strong class="fs-15 text-slate-900">
                                        <span class="config-info-1">11</span> vCPU Cores
                                    </strong>
                                </div>
                            </div>

                            <div class="cf-config-readout-item">
                                <div class="cf-config-readout-icon">
                                    <i class="las la-memory"></i>
                                </div>
                                <div>
                                    <span class="text-muted fs-12 d-block">Dedicated RAM</span>
                                    <strong class="fs-15 text-slate-900">
                                        <span class="config-info-2">6</span> GB ECC RAM
                                    </strong>
                                </div>
                            </div>

                            <div class="cf-config-readout-item">
                                <div class="cf-config-readout-icon">
                                    <i class="las la-hdd"></i>
                                </div>
                                <div>
                                    <span class="text-muted fs-12 d-block">Pure SSD Storage</span>
                                    <strong class="fs-15 text-slate-900">
                                        <span class="config-info-3">26</span> GB High-Speed SSD
                                    </strong>
                                </div>
                            </div>

                            <div class="cf-config-readout-item">
                                <div class="cf-config-readout-icon">
                                    <i class="las la-network-wired"></i>
                                </div>
                                <div>
                                    <span class="text-muted fs-12 d-block">Port &amp; Security</span>
                                    <strong class="fs-15 text-slate-900">
                                        1 Gbit + Anti-DDoS
                                    </strong>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 pt-2 d-flex align-items-center gap-2 text-slate-600 fs-13">
                            <i class="las la-check-circle text-success fs-16"></i>
                            <span>All custom configurations include 1 Dedicated IPv4, full root access, and 99.95% uptime SLA.</span>
                        </div>
                    </div>

                    <!-- Right: Dynamic Pricing Preview Box -->
                    <div class="col-12 col-lg-5">
                        <div class="cf-config-price-box">
                            <span class="badge bg-white bg-opacity-10 text-cyan fw-bold px-3 py-1 rounded-pill mb-3 d-inline-block mx-auto border border-white border-opacity-20 fs-12">
                                ESTIMATED MONTHLY INVESTMENT
                            </span>

                            <div class="mb-2">
                                <span class="fs-20 text-slate-300 align-top">$</span>
                                <span class="display-4 fw-extrabold text-white tracking-tight config-info-4">26</span>
                                <span class="fs-16 text-slate-400">/ mo</span>
                            </div>

                            <p class="text-slate-300 fs-14 mb-4 lh-base px-2">
                                Transparent billing with no hidden fees or setup charges. Scale or downsize anytime.
                            </p>

                            <a href="<?php echo $url; ?>request-quote?type=vps" class="cf-btn-vps-glow w-100 justify-content-center mb-3" data-pid="401">
                                <span>Configure &amp; Request Quote</span>
                                <i class="las la-arrow-right ms-2"></i>
                            </a>

                            <div class="text-center">
                                <a href="contact-us" class="text-slate-300 text-decoration-none fs-13 hover-underline">
                                    <i class="las la-comments me-1"></i> Need a larger custom node? Talk to an Architect
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- =========================================================================
         4. DETAILED SPECIFICATIONS & COMPARISON TABLE
         ========================================================================= -->
    <section class="py-5 bg-white position-relative" id="vpsComparison">
        <div class="container position-relative z-2">
            <!-- Section Header -->
            <div class="text-center max-w-750 mx-auto mb-4 mb-md-5">
                <span class="badge bg-cyan-subtle text-cyan fw-bold px-3 py-1 rounded-pill mb-2 border border-cyan-subtle">
                    DETAILED TECHNICAL MATRIX
                </span>
                <h2 class="display-6 fw-extrabold text-slate-900 mb-2">
                    Compare VPS Specifications &amp; Limits
                </h2>
                <p class="text-slate-600 fs-16 mb-0">
                    Comprehensive breakdown of dedicated hardware components, network quotas, OS support, and management options across all tiers.
                </p>
            </div>

            <!-- Table Card -->
            <div class="cf-vps-table-card">
                <div class="cf-vps-table-wrap">
                    <table class="cf-vps-table">
                        <thead>
                            <tr>
                                <th scope="col" style="min-width: 240px;">Specifications</th>
                                <th scope="col" class="plan-col">Launch VPS</th>
                                <th scope="col" class="plan-col">Scale VPS</th>
                                <th scope="col" class="plan-col popular-vps-th">
                                    Pro VPS <br>
                                    <span class="badge bg-cyan-subtle text-cyan fw-bold fs-11 mt-1">Popular</span>
                                </th>
                                <th scope="col" class="plan-col">Power VPS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Category: Compute & Processing -->
                            <tr class="cat-header">
                                <td colspan="5"><i class="las la-microchip me-1 text-cyan"></i> Compute &amp; Processing Power</td>
                            </tr>
                            <tr>
                                <td>CPU Architecture</td>
                                <td class="plan-val">High-Frequency vCPU</td>
                                <td class="plan-val">High-Frequency vCPU</td>
                                <td class="plan-val fw-bold text-cyan">High-Frequency vCPU</td>
                                <td class="plan-val">High-Frequency vCPU</td>
                            </tr>
                            <tr>
                                <td>CPU Cores</td>
                                <td class="plan-val">1 vCPU Core</td>
                                <td class="plan-val">2 vCPU Cores</td>
                                <td class="plan-val fw-bold text-slate-900">4 vCPU Cores</td>
                                <td class="plan-val">8 vCPU Cores</td>
                            </tr>
                            <tr>
                                <td>Virtualization Hypervisor</td>
                                <td class="plan-val"><span class="badge bg-light text-slate-700">100% KVM</span></td>
                                <td class="plan-val"><span class="badge bg-light text-slate-700">100% KVM</span></td>
                                <td class="plan-val"><span class="badge bg-cyan-subtle text-cyan">100% KVM</span></td>
                                <td class="plan-val"><span class="badge bg-light text-slate-700">100% KVM</span></td>
                            </tr>

                            <!-- Category: Memory & Storage -->
                            <tr class="cat-header">
                                <td colspan="5"><i class="las la-hdd me-1 text-cyan"></i> Memory &amp; Storage Architecture</td>
                            </tr>
                            <tr>
                                <td>High-Performance Memory</td>
                                <td class="plan-val">2 GB RAM</td>
                                <td class="plan-val">4 GB RAM</td>
                                <td class="plan-val fw-bold text-slate-900">8 GB High-Speed RAM</td>
                                <td class="plan-val">16 GB High-Speed RAM</td>
                            </tr>
                            <tr>
                                <td>Storage Capacity</td>
                                <td class="plan-val">30 GB NVMe / SSD</td>
                                <td class="plan-val">60 GB NVMe / SSD</td>
                                <td class="plan-val fw-bold text-slate-900">120 GB NVMe / SSD</td>
                                <td class="plan-val">240 GB NVMe / SSD</td>
                            </tr>
                            <tr>
                                <td>Storage Technology</td>
                                <td class="plan-val">Pure NVMe / SSD</td>
                                <td class="plan-val">Pure NVMe / SSD</td>
                                <td class="plan-val">Pure NVMe / SSD</td>
                                <td class="plan-val">Pure NVMe / SSD</td>
                            </tr>

                            <!-- Category: Network & Bandwidth -->
                            <tr class="cat-header">
                                <td colspan="5"><i class="las la-network-wired me-1 text-cyan"></i> Network &amp; Connectivity</td>
                            </tr>
                            <tr>
                                <td>Port Uplink Speed</td>
                                <td class="plan-val">1 Gbit Port</td>
                                <td class="plan-val">1 Gbit Port</td>
                                <td class="plan-val fw-bold text-slate-900">1 Gbit Priority</td>
                                <td class="plan-val">1 Gbit Redundant</td>
                            </tr>
                            <tr>
                                <td>Monthly Data Transfer</td>
                                <td class="plan-val">1 TB Transfer</td>
                                <td class="plan-val">2 TB Transfer</td>
                                <td class="plan-val fw-bold text-slate-900">4 TB Transfer</td>
                                <td class="plan-val">8 TB Transfer</td>
                            </tr>
                            <tr>
                                <td>Dedicated IPv4 Addresses</td>
                                <td class="plan-val">1 Dedicated IP</td>
                                <td class="plan-val">1 Dedicated IP</td>
                                <td class="plan-val fw-bold text-slate-900">1 Dedicated IP</td>
                                <td class="plan-val">1 Dedicated IP (+Extra)</td>
                            </tr>
                            <tr>
                                <td>DDoS Defense Capacity</td>
                                <td class="plan-val">Multi-Tbps Filtering</td>
                                <td class="plan-val">Multi-Tbps Filtering</td>
                                <td class="plan-val fw-bold text-slate-900">Advanced Packet Scrubbing</td>
                                <td class="plan-val">Advanced AI Mitigation</td>
                            </tr>

                            <!-- Category: Control & Management -->
                            <tr class="cat-header">
                                <td colspan="5"><i class="las la-terminal me-1 text-cyan"></i> Control, Access &amp; Support</td>
                            </tr>
                            <tr>
                                <td>Root / Admin Access</td>
                                <td class="plan-val"><i class="las la-check text-success fs-18"></i> Full Root Access</td>
                                <td class="plan-val"><i class="las la-check text-success fs-18"></i> Full Root Access</td>
                                <td class="plan-val"><i class="las la-check text-success fs-18"></i> Full Root Access</td>
                                <td class="plan-val"><i class="las la-check text-success fs-18"></i> Full Root Access</td>
                            </tr>
                            <tr>
                                <td>Out-of-Band KVM / VNC</td>
                                <td class="plan-val"><i class="las la-check text-success fs-18"></i> Web VNC Console</td>
                                <td class="plan-val"><i class="las la-check text-success fs-18"></i> Web VNC Console</td>
                                <td class="plan-val"><i class="las la-check text-success fs-18"></i> Web VNC Console</td>
                                <td class="plan-val"><i class="las la-check text-success fs-18"></i> Web VNC Console</td>
                            </tr>
                            <tr>
                                <td>1-Click OS Reinstalls</td>
                                <td class="plan-val"><i class="las la-check text-success fs-18"></i> Unlimited</td>
                                <td class="plan-val"><i class="las la-check text-success fs-18"></i> Unlimited</td>
                                <td class="plan-val"><i class="las la-check text-success fs-18"></i> Unlimited</td>
                                <td class="plan-val"><i class="las la-check text-success fs-18"></i> Unlimited</td>
                            </tr>
                            <tr>
                                <td>Network SLA</td>
                                <td class="plan-val">99.95% Network SLA</td>
                                <td class="plan-val">99.95% Network SLA</td>
                                <td class="plan-val fw-bold text-slate-900">99.95% Network SLA</td>
                                <td class="plan-val">99.95% Network SLA</td>
                            </tr>
                            <tr>
                                <td>Human Engineering Support</td>
                                <td class="plan-val">24/7 Standard Helpdesk</td>
                                <td class="plan-val">24/7 Standard Helpdesk</td>
                                <td class="plan-val fw-bold text-cyan">24/7 Priority Support</td>
                                <td class="plan-val">24/7 Dedicated Support</td>
                            </tr>
                            <!-- Pricing Row -->
                            <tr class="cf-vps-price-row">
                                <td class="cf-vps-row-label">
                                    <div class="d-flex align-items-center gap-3">
                                        <span class="cf-vps-row-icon"><i class="las la-tag"></i></span>
                                        <div>
                                            <span class="d-block fw-bold text-slate-900 fs-15">Monthly Pricing</span>
                                            <span class="d-block text-slate-500 fs-12">Cancel or scale anytime</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="plan-val">
                                    <div class="cf-vps-price-box-mini">
                                        <div class="cf-vps-price-figure">
                                            <span class="cf-vps-price-currency">$</span>
                                            <span class="cf-vps-price-val">7.99</span>
                                            <span class="cf-vps-price-cycle">/mo</span>
                                        </div>
                                        <span class="cf-vps-price-hint">Launch Tier</span>
                                    </div>
                                </td>
                                <td class="plan-val">
                                    <div class="cf-vps-price-box-mini">
                                        <div class="cf-vps-price-figure">
                                            <span class="cf-vps-price-currency">$</span>
                                            <span class="cf-vps-price-val">14.99</span>
                                            <span class="cf-vps-price-cycle">/mo</span>
                                        </div>
                                        <span class="cf-vps-price-hint">Scale Tier</span>
                                    </div>
                                </td>
                                <td class="plan-val popular-vps-td">
                                    <div class="cf-vps-price-box-mini is-featured">
                                        <span class="badge bg-cyan-subtle text-cyan fw-bold fs-11 px-2 py-1 rounded-pill mb-1">
                                            <i class="las la-fire me-1"></i> Best Value
                                        </span>
                                        <div class="cf-vps-price-figure text-cyan">
                                            <span class="cf-vps-price-currency">$</span>
                                            <span class="cf-vps-price-val">29.99</span>
                                            <span class="cf-vps-price-cycle">/mo</span>
                                        </div>
                                        <span class="cf-vps-price-hint text-cyan fw-semibold">Pro Tier</span>
                                    </div>
                                </td>
                                <td class="plan-val">
                                    <div class="cf-vps-price-box-mini">
                                        <div class="cf-vps-price-figure">
                                            <span class="cf-vps-price-currency">$</span>
                                            <span class="cf-vps-price-val">44.99</span>
                                            <span class="cf-vps-price-cycle">/mo</span>
                                        </div>
                                        <span class="cf-vps-price-hint">Power Tier</span>
                                    </div>
                                </td>
                            </tr>

                            <!-- Action / Deploy Row -->
                            <tr class="cf-vps-action-row">
                                <td class="cf-vps-row-label">
                                    <div class="d-flex align-items-center gap-3">
                                        <span class="cf-vps-row-icon is-rocket"><i class="las la-rocket"></i></span>
                                        <div>
                                            <span class="d-block fw-bold text-slate-900 fs-15">Deploy Server</span>
                                            <span class="d-block text-slate-500 fs-12">Automated cloud provisioning</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="plan-val">
                                    <a href="<?php echo $url; ?>request-quote?pid=401" class="cf-vps-action-btn btn-starter" data-pid="401" aria-label="Deploy Launch VPS">
                                        <span>Request Quote</span>
                                        <i class="las la-arrow-right"></i>
                                    </a>
                                </td>
                                <td class="plan-val">
                                    <a href="<?php echo $url; ?>request-quote?pid=402" class="cf-vps-action-btn btn-starter" data-pid="402" aria-label="Deploy Scale VPS">
                                        <span>Request Quote</span>
                                        <i class="las la-arrow-right"></i>
                                    </a>
                                </td>
                                <td class="plan-val popular-vps-td">
                                    <a href="<?php echo $url; ?>request-quote?pid=403" class="cf-vps-action-btn btn-popular" data-pid="403" aria-label="Deploy Pro VPS">
                                        <span>Request Quote</span>
                                        <i class="las la-bolt"></i>
                                    </a>
                                </td>
                                <td class="plan-val">
                                    <a href="<?php echo $url; ?>request-quote?pid=404" class="cf-vps-action-btn btn-business" data-pid="404" aria-label="Deploy Power VPS">
                                        <span>Request Quote</span>
                                        <i class="las la-sliders-h"></i>
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Bottom Ambient Enterprise Action Bar -->
                <div class="cf-vps-table-bottom-bar">
                    <div class="cf-vps-bottom-content">
                        <div class="cf-vps-bottom-icon">
                            <i class="las la-network-wired"></i>
                        </div>
                        <div class="cf-vps-bottom-text">
                            <div class="cf-vps-bottom-title">
                                Need a Custom Enterprise Infrastructure?
                                <span class="badge bg-cyan-subtle text-cyan fw-bold px-2 py-1 rounded-pill">Custom SLA</span>
                            </div>
                            <p class="cf-vps-bottom-desc mb-0">
                                Dedicated /24 IPv4 subnets, custom BGP session peering, NVMe storage arrays, and private VLAN interconnects ready on request.
                            </p>
                        </div>
                    </div>
                    <div class="cf-vps-bottom-actions">
                        <a href="contact-us" class="cf-vps-bottom-btn cf-vps-bottom-btn-quote">
                            <i class="las la-file-invoice text-cyan fs-16"></i>
                            <span>Request Custom Quote</span>
                        </a>
                        <button type="button" onclick="openLiveChat(event)" class="cf-vps-bottom-btn cf-vps-bottom-btn-chat">
                            <i class="las la-comments fs-16"></i>
                            <span>Chat with Architect</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- =========================================================================
         5. WHY CHOOSE CLOUDFLOX VPS INFRASTRUCTURE (Bento Grid)
         ========================================================================= -->
    <section class="py-5 bg-light position-relative">
        <div class="container position-relative z-2">
            <!-- Section Header -->
            <div class="text-center max-w-750 mx-auto mb-4 mb-md-5">
                <span class="badge bg-cyan-subtle text-cyan fw-bold px-3 py-1 rounded-pill mb-2 border border-cyan-subtle">
                    ENTERPRISE GRADE INFRASTRUCTURE
                </span>
                <h2 class="display-6 fw-extrabold text-slate-900 mb-2">
                    Why Choose CloudFlox VPS Servers?
                </h2>
                <p class="text-slate-600 fs-16 mb-0">
                    Engineered from the silicon up for mission-critical reliability, unthrottled throughput, and developer autonomy.
                </p>
            </div>

            <!-- Bento Grid (6 Feature Cards) -->
            <div class="row g-4">
                <!-- Card 1 -->
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="cf-feature-box h-100 p-4 rounded-4 border bg-white shadow-sm hover-shadow">
                        <div class="cf-card-icon-bubble bg-cyan-subtle text-cyan mb-3">
                            <i class="las la-microchip"></i>
                        </div>
                        <h4 class="fw-bold text-slate-900 fs-18 mb-2">100% Dedicated Resources</h4>
                        <p class="text-slate-600 fs-14 mb-0 lh-base">
                            Guaranteed CPU cycles and reserved ECC RAM with zero oversubscription. KVM hypervisor isolation ensures your performance remains rock-solid at all times.
                        </p>
                    </div>
                </div>

                <!-- Card 2 -->
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="cf-feature-box h-100 p-4 rounded-4 border bg-white shadow-sm hover-shadow">
                        <div class="cf-card-icon-bubble bg-blue-subtle text-blue mb-3">
                            <i class="las la-hdd"></i>
                        </div>
                        <h4 class="fw-bold text-slate-900 fs-18 mb-2">Enterprise SSD Storage</h4>
                        <p class="text-slate-600 fs-14 mb-0 lh-base">
                            High-IOPS Solid State Drive arrays configured in redundant enterprise RAID. Experience lightning-fast database read/write speeds and zero latency spikes.
                        </p>
                    </div>
                </div>

                <!-- Card 3 -->
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="cf-feature-box h-100 p-4 rounded-4 border bg-white shadow-sm hover-shadow">
                        <div class="cf-card-icon-bubble bg-emerald-subtle text-emerald mb-3">
                            <i class="las la-network-wired"></i>
                        </div>
                        <h4 class="fw-bold text-slate-900 fs-18 mb-2">1Gbps Redundant Network</h4>
                        <p class="text-slate-600 fs-14 mb-0 lh-base">
                            Direct peering with Tier-1 global transit providers. Generous 10TB+ monthly data transfer quotas ensure your users enjoy instant page load speeds worldwide.
                        </p>
                    </div>
                </div>

                <!-- Card 4 -->
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="cf-feature-box h-100 p-4 rounded-4 border bg-white shadow-sm hover-shadow">
                        <div class="cf-card-icon-bubble bg-danger-subtle text-danger mb-3">
                            <i class="las la-shield-alt"></i>
                        </div>
                        <h4 class="fw-bold text-slate-900 fs-18 mb-2">Multi-Tbps DDoS Mitigation</h4>
                        <p class="text-slate-600 fs-14 mb-0 lh-base">
                            Automated volumetric packet scrubbing detects and neutralizes Layer 3, 4, and 7 attacks in real time without causing routing delays or dropped requests.
                        </p>
                    </div>
                </div>

                <!-- Card 5 -->
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="cf-feature-box h-100 p-4 rounded-4 border bg-white shadow-sm hover-shadow">
                        <div class="cf-card-icon-bubble bg-purple-subtle text-purple mb-3">
                            <i class="las la-terminal"></i>
                        </div>
                        <h4 class="fw-bold text-slate-900 fs-18 mb-2">Root Access &amp; Web KVM</h4>
                        <p class="text-slate-600 fs-14 mb-0 lh-base">
                            Complete administrative command line control. Access your server out-of-band via our built-in HTML5 KVM console, even if SSH networking is misconfigured.
                        </p>
                    </div>
                </div>

                <!-- Card 6 -->
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="cf-feature-box h-100 p-4 rounded-4 border bg-white shadow-sm hover-shadow">
                        <div class="cf-card-icon-bubble bg-warning-subtle text-warning mb-3">
                            <i class="las la-bolt"></i>
                        </div>
                        <h4 class="fw-bold text-slate-900 fs-18 mb-2">60-Second Instant Deploy</h4>
                        <p class="text-slate-600 fs-14 mb-0 lh-base">
                            Zero provisioning queues. As soon as your order completes, our automated orchestrator builds your virtual machine and delivers login credentials via email.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- =========================================================================
         6. OPERATING SYSTEMS & 1-CLICK APPS SHOWCASE
         ========================================================================= -->
    <section class="py-5 bg-white position-relative">
        <div class="container position-relative z-2">
            <div class="text-center max-w-750 mx-auto mb-4 mb-md-5">
                <span class="badge bg-cyan-subtle text-cyan fw-bold px-3 py-1 rounded-pill mb-2 border border-cyan-subtle">
                    ENDLESS FLEXIBILITY
                </span>
                <h2 class="display-6 fw-extrabold text-slate-900 mb-2">
                    Deploy Your Favorite OS &amp; Control Panels
                </h2>
                <p class="text-slate-600 fs-16 mb-0">
                    Pre-configured 1-click operating system templates and popular server software suites ready to launch.
                </p>
            </div>

            <div class="row g-3 row-cols-2 row-cols-sm-3 row-cols-md-6 text-center">
                <!-- Ubuntu -->
                <div class="col">
                    <div class="cf-os-logo-card">
                        <div class="cf-os-logo-icon text-warning">
                            <i class="lab la-ubuntu"></i>
                        </div>
                        <h5 class="fw-bold fs-15 text-slate-900 mb-1">Ubuntu</h5>
                        <span class="text-muted fs-12">24.04 / 22.04 LTS</span>
                    </div>
                </div>

                <!-- Debian -->
                <div class="col">
                    <div class="cf-os-logo-card">
                        <div class="cf-os-logo-icon text-danger">
                            <i class="lab la-linux"></i>
                        </div>
                        <h5 class="fw-bold fs-15 text-slate-900 mb-1">Debian</h5>
                        <span class="text-muted fs-12">12 Bookworm</span>
                    </div>
                </div>

                <!-- AlmaLinux -->
                <div class="col">
                    <div class="cf-os-logo-card">
                        <div class="cf-os-logo-icon text-primary">
                            <i class="las la-shield-alt"></i>
                        </div>
                        <h5 class="fw-bold fs-15 text-slate-900 mb-1">AlmaLinux</h5>
                        <span class="text-muted fs-12">9.x / 8.x RHEL</span>
                    </div>
                </div>

                <!-- Windows -->
                <div class="col">
                    <div class="cf-os-logo-card">
                        <div class="cf-os-logo-icon text-info">
                            <i class="lab la-windows"></i>
                        </div>
                        <h5 class="fw-bold fs-15 text-slate-900 mb-1">Windows Server</h5>
                        <span class="text-muted fs-12">2022 / 2019</span>
                    </div>
                </div>

                <!-- Docker -->
                <div class="col">
                    <div class="cf-os-logo-card">
                        <div class="cf-os-logo-icon text-primary">
                            <i class="lab la-docker"></i>
                        </div>
                        <h5 class="fw-bold fs-15 text-slate-900 mb-1">Docker</h5>
                        <span class="text-muted fs-12">Containers &amp; Compose</span>
                    </div>
                </div>

                <!-- cPanel -->
                <div class="col">
                    <div class="cf-os-logo-card">
                        <div class="cf-os-logo-icon text-warning">
                            <i class="las la-cog"></i>
                        </div>
                        <h5 class="fw-bold fs-15 text-slate-900 mb-1">cPanel / WHM</h5>
                        <span class="text-muted fs-12">Plesk &amp; CyberPanel</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- =========================================================================
         7. CALLOUT BANNER (Deploy in 60 Seconds)
         ========================================================================= -->
    <section class="py-4 py-md-5">
        <div class="container">
            <div class="cf-migration-card text-white p-4 p-md-5 rounded-4 position-relative overflow-hidden shadow-lg">
                <div class="row align-items-center g-4 position-relative z-2">
                    <div class="col-12 col-lg-8">
                        <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill bg-white bg-opacity-10 text-white fs-13 mb-3 border border-white border-opacity-20">
                            <i class="las la-rocket text-warning"></i>
                            <span>RAPID CLOUD DEPLOYMENT</span>
                        </div>
                        <h3 class="display-6 fw-bold mb-3" style="color: #ffffff !important;">
                            Launch Your High-Performance Linux Cloud VPS Server Today.
                        </h3>
                        <p class="text-slate-300 fs-16 mb-0 lh-base max-w-750">
                            Experience the speed and autonomy of pure KVM cloud servers with high-frequency compute, NVMe/SSD storage, and 1Gbps uplink backed by 24/7 human engineering support starting at just <strong>$7.99/month</strong>.
                        </p>
                    </div>
                    <div class="col-12 col-lg-4 text-lg-end">
                        <div class="d-flex flex-column flex-sm-row flex-lg-column gap-2 justify-content-lg-end">
                            <a href="#vpsPlans" class="cf-btn-vps-glow text-center">
                                <i class="las la-server me-1"></i> Deploy VPS Server
                            </a>
                            <a href="contact-us" class="btn btn-outline-light rounded-3 py-2 px-3 fs-14 fw-semibold text-center">
                                Talk to Solutions Architect
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- =========================================================================
         8. FREQUENTLY ASKED QUESTIONS (FAQ Accordion)
         ========================================================================= -->
    <section class="py-5 bg-white position-relative">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-lg-8 text-center mb-4 mb-md-5">
                    <span class="badge bg-cyan-subtle text-cyan fw-bold px-3 py-1 rounded-pill mb-2 border border-cyan-subtle">
                        FREQUENTLY ASKED QUESTIONS
                    </span>
                    <h2 class="display-6 fw-extrabold text-slate-900 mb-2">
                        Common Questions About Cloud VPS Hosting
                    </h2>
                    <p class="text-slate-600 fs-16 mb-0">
                        Everything you need to know about KVM virtualization, OS environments, resource scaling, and root control.
                    </p>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-12 col-xl-9">
                    <div class="accordion cf-modern-accordion d-flex flex-column gap-3" id="accordionVpsFaq">

                        <!-- FAQ 1 -->
                        <div class="accordion-item rounded-3 border">
                            <h3 class="accordion-header" id="headingV1">
                                <button class="accordion-button bg-white text-slate-900 fw-bold fs-16" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#collapseV1" aria-expanded="true"
                                    aria-controls="collapseV1">
                                    What is KVM VPS hosting and how does it differ from shared hosting?
                                </button>
                            </h3>
                            <div id="collapseV1" class="accordion-collapse collapse show" aria-labelledby="headingV1"
                                data-bs-parent="#accordionVpsFaq">
                                <div class="accordion-body text-slate-600 fs-15 lh-base pt-0">
                                    A KVM (Kernel-based Virtual Machine) VPS provides a dedicated, isolated server slice with strictly reserved CPU cores, RAM memory, and SSD storage. Unlike shared hosting where hundreds of accounts compete for common server resources, your VPS has completely separate operating system kernel space and dedicated resources. No other tenant on the node can affect your performance or security.
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 2 -->
                        <div class="accordion-item rounded-3 border">
                            <h3 class="accordion-header" id="headingV2">
                                <button class="accordion-button collapsed bg-white text-slate-900 fw-bold fs-16" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#collapseV2" aria-expanded="false"
                                    aria-controls="collapseV2">
                                    Linux VPS vs Windows VPS — which operating system should I choose?
                                </button>
                            </h3>
                            <div id="collapseV2" class="accordion-collapse collapse" aria-labelledby="headingV2"
                                data-bs-parent="#accordionVpsFaq">
                                <div class="accordion-body text-slate-600 fs-15 lh-base pt-0">
                                    Choose <strong>Linux VPS</strong> if you plan to host standard web applications using PHP, WordPress, Laravel, Node.js, Python, Docker, or control panels like cPanel/WHM. Choose <strong>Windows VPS</strong> if you require Microsoft technologies such as ASP.NET, MSSQL Server, Remote Desktop Protocol (RDP) for administrative teams, or Windows-only proprietary desktop applications.
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 3 -->
                        <div class="accordion-item rounded-3 border">
                            <h3 class="accordion-header" id="headingV3">
                                <button class="accordion-button collapsed bg-white text-slate-900 fw-bold fs-16" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#collapseV3" aria-expanded="false"
                                    aria-controls="collapseV3">
                                    Do I get full root / administrator access and an out-of-band console?
                                </button>
                            </h3>
                            <div id="collapseV3" class="accordion-collapse collapse" aria-labelledby="headingV3"
                                data-bs-parent="#accordionVpsFaq">
                                <div class="accordion-body text-slate-600 fs-15 lh-base pt-0">
                                    Yes, you receive 100% unrestricted <strong>root access</strong> (Linux via SSH) or <strong>Administrator access</strong> (Windows via RDP). You have full authority to install custom libraries, adjust kernel sysctl settings, configure firewalls, and run any software stack. In addition, our client portal includes an out-of-band HTML5 VNC console for emergency recovery.
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 4 -->
                        <div class="accordion-item rounded-3 border">
                            <h3 class="accordion-header" id="headingV4">
                                <button class="accordion-button collapsed bg-white text-slate-900 fw-bold fs-16" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#collapseV4" aria-expanded="false"
                                    aria-controls="collapseV4">
                                    Can I upgrade or resize my VPS resources later without data loss?
                                </button>
                            </h3>
                            <div id="collapseV4" class="accordion-collapse collapse" aria-labelledby="headingV4"
                                data-bs-parent="#accordionVpsFaq">
                                <div class="accordion-body text-slate-600 fs-15 lh-base pt-0">
                                    Absolutely. Scaling CPU cores, RAM memory, and SSD storage takes less than 2 minutes from your CloudFlox client area. Simply select your new resource tier and reboot the virtual machine; all files, databases, configurations, and IP addresses remain completely intact.
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 5 -->
                        <div class="accordion-item rounded-3 border">
                            <h3 class="accordion-header" id="headingV5">
                                <button class="accordion-button collapsed bg-white text-slate-900 fw-bold fs-16" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#collapseV5" aria-expanded="false"
                                    aria-controls="collapseV5">
                                    How is DDoS protection managed on CloudFlox VPS servers?
                                </button>
                            </h3>
                            <div id="collapseV5" class="accordion-collapse collapse" aria-labelledby="headingV5"
                                data-bs-parent="#accordionVpsFaq">
                                <div class="accordion-body text-slate-600 fs-15 lh-base pt-0">
                                    Every CloudFlox VPS includes multi-Tbps automated DDoS defense at no extra cost. Incoming network traffic is continuously analyzed upstream by intelligent edge scrubbers that filter out UDP floods, SYN floods, amplification attacks, and Layer 7 anomalies, passing only clean legitimate traffic to your virtual machine.
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 6 -->
                        <div class="accordion-item rounded-3 border">
                            <h3 class="accordion-header" id="headingV6">
                                <button class="accordion-button collapsed bg-white text-slate-900 fw-bold fs-16" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#collapseV6" aria-expanded="false"
                                    aria-controls="collapseV6">
                                    What can I host on a CloudFlox VPS?
                                </button>
                            </h3>
                            <div id="collapseV6" class="accordion-collapse collapse" aria-labelledby="headingV6"
                                data-bs-parent="#accordionVpsFaq">
                                <div class="accordion-body text-slate-600 fs-15 lh-base pt-0">
                                    You can host virtually any lawful software workload, including high-traffic eCommerce stores, corporate websites, SaaS web applications, game servers, CI/CD runners, Docker container swarms, PostgreSQL / MySQL database clusters, mail servers, and VPN endpoints.
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

    <!-- VPS Interactive JS (OS Switcher & Billing Toggle) -->
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        // 1. OS Switcher (Linux vs Windows)
        const btnLinux = document.getElementById("btnOsLinux");
        const btnWindows = document.getElementById("btnOsWindows");
        const paneLinux = document.getElementById("paneLinux");
        const paneWindows = document.getElementById("paneWindows");

        if (btnLinux && btnWindows && paneLinux && paneWindows) {
            btnLinux.addEventListener("click", function () {
                btnLinux.classList.add("active", "linux-active");
                btnWindows.classList.remove("active", "windows-active");
                btnLinux.setAttribute("aria-selected", "true");
                btnWindows.setAttribute("aria-selected", "false");
                paneLinux.classList.remove("d-none");
                paneWindows.classList.add("d-none");
            });

            btnWindows.addEventListener("click", function () {
                btnWindows.classList.add("active", "windows-active");
                btnLinux.classList.remove("active", "linux-active");
                btnWindows.setAttribute("aria-selected", "true");
                btnLinux.setAttribute("aria-selected", "false");
                paneWindows.classList.remove("d-none");
                paneLinux.classList.add("d-none");
            });
        }

        // 2. Billing Cycle Toggle (Monthly vs Annual - 15% discount)
        const btnMonthly = document.getElementById("btnMonthly");
        const btnYearly = document.getElementById("btnYearly");
        const priceElements = document.querySelectorAll(".cf-vps-amount");
        const billingNotes = document.querySelectorAll(".billing-note");

        if (btnMonthly && btnYearly) {
            btnMonthly.addEventListener("click", function () {
                btnMonthly.classList.add("active");
                btnYearly.classList.remove("active");

                priceElements.forEach(function (el) {
                    const monthlyVal = el.getAttribute("data-monthly");
                    if (monthlyVal) {
                        el.textContent = "$" + monthlyVal;
                    }
                });

                billingNotes.forEach(function (el) {
                    el.innerHTML = "Billed monthly &bull; ex VAT";
                });
                document.querySelectorAll('.cf-btn-vps-order, .cf-vps-action-btn, .cf-btn-vps-glow').forEach(function (btn) {
                    const pid = btn.getAttribute('data-pid') || '401';
                    btn.href = '<?php echo $url; ?>request-quote?pid=' + pid;
                });
            });

            btnYearly.addEventListener("click", function () {
                btnYearly.classList.add("active");
                btnMonthly.classList.remove("active");

                priceElements.forEach(function (el) {
                    const yearlyVal = el.getAttribute("data-yearly");
                    if (yearlyVal) {
                        el.textContent = "$" + yearlyVal;
                    }
                });

                billingNotes.forEach(function (el) {
                    el.innerHTML = "Billed annually (Save 15%) &bull; ex VAT";
                });
                document.querySelectorAll('.cf-btn-vps-order, .cf-vps-action-btn, .cf-btn-vps-glow').forEach(function (btn) {
                    const pid = btn.getAttribute('data-pid') || '401';
                    btn.href = '<?php echo $url; ?>request-quote?pid=' + pid;
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
    });
    </script>
</body>

</html>