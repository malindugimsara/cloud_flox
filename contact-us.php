<?php include 'include/url.inc.php'; ?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">

<head>
    <!-- Basic Meta -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Page Title -->
    <title>Contact CloudFlox — Hosting Support &amp; Sales Assistance</title>
    <!-- SEO Meta -->
    <meta name="description"
        content="Contact CloudFlox for support with web hosting, VPS, dedicated servers, or domain services. Our team is ready to help with sales, technical assistance, and account questions.">
    <meta name="keywords"
        content="contact CloudFlox, hosting support, web hosting help, vps support, domain support, cloud hosting contact">
    <meta name="author" content="CloudFlox">
    <link rel="canonical" href="https://cloudflox.com/contact-us">

    <meta property="og:type" content="website">
    <meta property="og:title" content="Contact CloudFlox Support Team">
    <meta property="og:description" content="Reach CloudFlox for hosting assistance, technical support, or sales inquiries.">
    <meta property="og:url" content="https://cloudflox.com/contact-us">
    <meta property="og:image" content="https://cloudflox.com/assets/imgs/banners/cloudflox-contact-support.webp">
    <meta property="og:site_name" content="CloudFlox">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Contact CloudFlox">
    <meta name="twitter:description" content="Get help with hosting, domains, and servers from CloudFlox support.">
    <meta name="twitter:image" content="https://cloudflox.com/assets/imgs/banners/cloudflox-contact-support.webp">

    <!-- JSON-LD -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "ContactPage",
      "name": "Contact CloudFlox",
      "description": "Contact page for CloudFlox hosting support and sales",
      "url": "https://cloudflox.com/contact-us"
    }
    </script>

    <!-- =========== CSS Area =========== -->
    <?php include 'include/css.inc.php'; ?>
    <!-- =========== CSS Area =========== -->
    <!-- toast (Non-Blocking) -->
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.css" as="style">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.css" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.css"></noscript>
</head>

<body class="pure-white-theme cf-contact-page">

    <!-- =========== Header Area =========== -->
    <?php include 'include/header.inc.php'; ?>
    <!-- =========== Header Area =========== -->

    <!-- =========================================================================
         1. HERO SECTION
         ========================================================================= -->
    <section class="cf-contact-hero position-relative">
        <div class="hero-ambient-glow" aria-hidden="true"></div>

        <!-- Ambient Decorative Patterns -->
        <div class="pricing-bg-shape pricing-shape-left" aria-hidden="true" style="opacity: 0.4;">
            <img src="assets/imgs/shape/pattern-2.svg?v=2" alt="pattern-2" width="370" height="590" loading="eager" decoding="async">
        </div>
        <div class="pricing-bg-shape pricing-shape-right" aria-hidden="true" style="opacity: 0.4;">
            <img src="assets/imgs/shape/pattern-3.svg?v=2" alt="pattern-3" width="370" height="590" loading="eager" decoding="async">
        </div>

        <div class="container position-relative z-2">
            <!-- Breadcrumbs -->
            <nav aria-label="breadcrumb" class="mb-4 pt-1">
                <ol class="breadcrumb custom-breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="<?php echo $url; ?>" class="text-decoration-none text-muted">
                            <i class="las la-home me-1"></i>Home
                        </a>
                    </li>
                    <li class="breadcrumb-item active text-blue fw-semibold" aria-current="page">Contact Us</li>
                </ol>
            </nav>

            <div class="row align-items-center g-4 g-xl-5 pb-lg-3">
                <!-- Left Column: Value Proposition & Contact Actions -->
                <div class="col-12 col-lg-7">
                    <!-- Hero Badge -->
                    <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill bg-blue-subtle text-blue fw-bold fs-13 mb-3 border border-blue-subtle">
                        <span class="cf-pulse-dot-blue"></span>
                        <span>24/7 SUPPORT &amp; SALES ASSISTANCE</span>
                    </div>

                    <!-- Title -->
                    <h1 class="cf-contact-hero-title fw-extrabold text-slate-900 tracking-tight mb-3">
                        We're Here to <br class="d-none d-sm-inline">
                        <span class="text-gradient-contact">Help You</span>
                    </h1>

                    <!-- Lead -->
                    <p class="cf-contact-hero-lead text-slate-600 mb-4 lh-base">
                        Need help with hosting, domains, billing, or technical setup? Our expert team is ready to assist. Send us a message and we'll get back to you promptly.
                    </p>

                    <!-- Trust Chips -->
                    <div class="d-flex flex-wrap gap-2 mb-4 pb-2">
                        <span class="cf-feature-chip">
                            <i class="las la-clock text-blue"></i> Response within 1 hour
                        </span>
                        <span class="cf-feature-chip">
                            <i class="las la-headset text-blue"></i> 24/7 Expert Support
                        </span>
                        <span class="cf-feature-chip">
                            <i class="las la-shield-alt text-blue"></i> 100% Secure &amp; Private
                        </span>
                        <span class="cf-feature-chip">
                            <i class="las la-check-circle text-blue"></i> Free Technical Help
                        </span>
                    </div>

                    <!-- Hero Action Buttons -->
                    <div class="d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center gap-3 cf-hero-btn-group">
                        <a href="#contactForm" class="cf-btn-contact-submit text-center">
                            <span>Send Us a Message</span>
                            <i class="las la-arrow-down ms-2"></i>
                        </a>
                        <a href="https://my.cloudflox.com/submitticket.php" class="cf-btn-contact-outline text-center">
                            <i class="las la-ticket-alt me-2"></i>
                            <span>Open Support Ticket</span>
                        </a>
                    </div>
                </div>

                <!-- Right Column: Visual Hero Infrastructure Showcase -->
                <div class="col-12 col-lg-5">
                    <div class="cf-contact-hero-visual position-relative text-center">
                        <div class="cf-hero-img-wrap position-relative mx-auto">
                            <img src="assets/imgs/banners/cloudflox-contact-support.webp"
                                alt="CloudFlox Support and Sales Assistance Team"
                                class="cf-hero-illustration img-fluid"
                                width="440"
                                height="440"
                                fetchpriority="high"
                                loading="eager">

                            <!-- Floating Chip (Top Left) -->
                            <div class="cf-hero-float-chip float-chip-top shadow-sm">
                                <span class="cf-chip-pulse"></span>
                                <div class="text-start">
                                    <span class="d-block cf-chip-sub">Live Support</span>
                                    <strong class="cf-chip-val text-slate-900">24/7 Online</strong>
                                </div>
                                <span class="badge bg-success-subtle text-success fw-bold px-2 py-1 rounded-pill ms-2 fs-11">
                                    Active
                                </span>
                            </div>

                            <!-- Floating Chip (Bottom Right) -->
                            <div class="cf-hero-float-chip float-chip-bottom shadow-sm">
                                <div class="cf-chip-icon bg-blue-subtle text-blue">
                                    <i class="las la-bolt"></i>
                                </div>
                                <div class="text-start">
                                    <span class="d-block cf-chip-sub">Fast Response</span>
                                    <strong class="cf-chip-val text-slate-900">&lt; 15 Mins</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section><!-- /Hero -->

    <!-- =========================================================================
         3. CONTACT FORM SECTION (HIGH-CONVERSION HIGHLIGHTED FORM & SIDEBAR)
         ========================================================================= -->
    <section class="cf-contact-form-section" id="contactForm">
        <div class="container position-relative">

            <!-- Ambient Glow behind form -->
            <div class="cf-form-ambient-glow" aria-hidden="true"></div>

            <!-- Section Header -->
            <div class="text-center mb-5">
                <div class="section-tag mx-auto mb-3">
                    <i class="las la-paper-plane me-1"></i> Send a Message
                </div>
                <h2 class="fs-32 fw-bold text-slate-900 mb-2">Get in Touch With Our Specialists</h2>
                <p class="text-muted fs-15 mx-auto" style="max-width:540px;">
                    Submit your inquiry below. Our team triages every submission immediately and will respond to your registered email address.
                </p>
            </div>

            <div class="row g-4 g-xl-5 align-items-stretch justify-content-center">

                <!-- Highlighted Form Card -->
                <div class="col-12 col-lg-7 d-flex">
                    <div class="cf-contact-form-card cf-form-card-highlighted position-relative w-100 h-100 d-flex flex-column">

                        <!-- Form Top Gradient Accent -->
                        <div class="cf-form-top-accent"></div>

                        <!-- Form Header with Active Pulse Desk -->
                        <div class="cf-form-inner-header mb-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
                            <div>
                                <span class="cf-form-desk-badge mb-1">
                                    <i class="las la-headset me-1"></i> Direct Message Dispatch
                                </span>
                                <h3 class="fs-20 fw-bold text-slate-900 mb-0">Send Us an Inquiry</h3>
                            </div>
                            <div class="cf-form-live-status">
                                <span class="cf-chip-pulse"></span>
                                <span class="cf-live-text">Desk Online</span>
                                <span class="cf-live-sla">&lt; 15 Mins</span>
                            </div>
                        </div>

                        <!-- Active form -->
                        <form id="c-f" class="d-flex flex-column h-100">
                            <div class="row g-3">

                                <div class="col-12 col-sm-6">
                                    <div class="cf-form-group">
                                        <label class="cf-form-label" for="name">
                                            <i class="las la-user me-1 text-primary"></i> Full Name <span class="text-danger">*</span>
                                        </label>
                                        <div class="cf-input-wrapper">
                                            <input type="text" id="name"
                                                placeholder="Enter your full name"
                                                class="cf-form-input">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-sm-6">
                                    <div class="cf-form-group">
                                        <label class="cf-form-label" for="email">
                                            <i class="las la-envelope me-1 text-primary"></i> Business Email <span class="text-danger">*</span>
                                        </label>
                                        <div class="cf-input-wrapper">
                                            <input type="email" id="email"
                                                placeholder="you@company.com"
                                                class="cf-form-input">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-sm-6">
                                    <div class="cf-form-group">
                                        <label class="cf-form-label" for="phone">
                                            <i class="las la-phone me-1 text-primary"></i> Phone Number <span class="text-muted fw-normal">(Optional)</span>
                                        </label>
                                        <div class="cf-input-wrapper">
                                            <input type="tel" id="phone"
                                                placeholder="+1 (555) 000-0000"
                                                class="cf-form-input">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-sm-6">
                                    <div class="cf-form-group">
                                        <label class="cf-form-label" for="subject">
                                            <i class="las la-tag me-1 text-primary"></i> Subject / Topic
                                        </label>
                                        <div class="cf-input-wrapper">
                                            <input type="text" id="subject"
                                                placeholder="e.g. VPS Upgrade / Custom Server / Migration"
                                                class="cf-form-input">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="cf-form-group">
                                        <label class="cf-form-label" for="message">
                                            <i class="las la-comment-alt me-1 text-primary"></i> Your Message <span class="text-danger">*</span>
                                        </label>
                                        <div class="cf-input-wrapper">
                                            <textarea id="message"
                                                placeholder="Please provide details about your hosting requirements, domain names, current server setup, or specific assistance needed..."
                                                class="cf-form-input cf-form-textarea"></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="cf-turnstile-wrapper p-2 bg-slate-50 rounded-3 border d-flex justify-content-center">
                                        <div class="cf-turnstile" data-sitekey="<?php echo htmlspecialchars(defined('CF_TURNSTILE_SITE_KEY') ? CF_TURNSTILE_SITE_KEY : '0x4AAAAAAFsPGuQlAMYqY37j'); ?>" data-theme="light"></div>
                                    </div>
                                </div>

                            </div><!-- /row -->

                            <div class="mt-auto pt-4">
                                <button type="button" id="form-btn" onclick="form_submit();"
                                    class="cf-btn-contact-submit cf-btn-submit-highlighted w-100">
                                    <i class="las la-paper-plane me-2 fs-18"></i>
                                    <span>Send Priority Message</span>
                                </button>
                                <button type="button" id="form-btn2"
                                    class="cf-btn-contact-submit cf-btn-submit-highlighted w-100 d-none" disabled>
                                    <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                    <span>Transmitting Message...</span>
                                </button>

                                <!-- Trust Guarantee Footer -->
                                <div class="cf-form-trust-footer mt-3 pt-3 border-top d-flex flex-wrap align-items-center justify-content-between gap-2 text-muted fs-12">
                                    <span><i class="las la-shield-alt text-success me-1"></i> No spam guarantee</span>
                                    <span><i class="las la-lock text-primary me-1"></i> 256-bit SSL encrypted</span>
                                    <span><i class="las la-bolt text-warning me-1"></i> Direct engineer reply</span>
                                </div>
                            </div>

                        </form>

                        <!-- Thank you state -->
                        <div class="cf-contact-thankyou d-none my-auto" id="th-c-f">
                            <div class="text-center py-4">
                                <img src="assets/imgs/success.webp" alt="Thank you" style="width: 220px; margin-bottom: 16px;">
                                <h2 class="fs-24 fw-bold text-slate-900 mb-2">Message Transmitted!</h2>
                                <p class="text-muted fs-14 mb-4 mx-auto" style="max-width:440px;">
                                    Thank you for reaching out to CloudFlox. We have received your inquiry and our engineering team will get back to you shortly.
                                </p>
                                <a href="<?php echo $url; ?>" class="cf-btn-contact-outline">
                                    <i class="las la-home me-1"></i> Back to Home
                                </a>
                            </div>
                        </div>

                    </div><!-- /card -->
                </div>

                <!-- Unified Sidebar Card: Why Work With CloudFlox & Quick Support (Exact Form Size & Height) -->
                <div class="col-12 col-lg-5 col-xl-5 d-flex">
                    <div class="cf-contact-sidebar-unified w-100 h-100 d-flex flex-column position-relative">

                        <!-- Top Gradient Accent line matching form -->
                        <div class="cf-sidebar-top-accent"></div>

                        <!-- Card Header: Why Work With CloudFlox? -->
                        <div class="cf-sidebar-unified-header mb-3 pb-3 border-bottom">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="cf-form-desk-badge">
                                    <i class="las la-shield-alt me-1"></i> Enterprise SLA &amp; Support
                                </span>
                                <span class="badge bg-primary-subtle text-primary rounded-pill px-2 py-1 fs-11 fw-semibold">
                                    24/7/365 NOC
                                </span>
                            </div>
                            <h3 class="fs-20 fw-bold text-slate-900 mb-1">Why Work With CloudFlox?</h3>
                            <p class="text-muted fs-13 mb-0">
                                Direct sysadmin access, zero robotic delays, and enterprise cloud infrastructure.
                            </p>
                        </div>

                        <!-- Why CloudFlox Core Pillars -->
                        <div class="cf-sidebar-pillars-wrap mb-3">
                            <ul class="cf-contact-sidebar-list">
                                <li>
                                    <span class="cf-sidebar-icon bg-blue-subtle text-blue"><i class="las la-headset"></i></span>
                                    <div>
                                        <strong class="text-slate-900 fs-13">Direct Sysadmin Access</strong>
                                        <p class="text-muted fs-12 mb-0">No robotic scripts. Real infrastructure specialists handle and triage your inquiries.</p>
                                    </div>
                                </li>
                                <li>
                                    <span class="cf-sidebar-icon bg-purple-subtle text-purple"><i class="las la-bolt"></i></span>
                                    <div>
                                        <strong class="text-slate-900 fs-13">Sub-15 Minute Turnaround</strong>
                                        <p class="text-muted fs-12 mb-0">High-priority ticket queues ensure emergency fixes and server reboots happen fast.</p>
                                    </div>
                                </li>
                                <li>
                                    <span class="cf-sidebar-icon bg-emerald-subtle text-emerald"><i class="las la-shield-alt"></i></span>
                                    <div>
                                        <strong class="text-slate-900 fs-13">Multi-Tbps DDoS Protection</strong>
                                        <p class="text-muted fs-12 mb-0">Automated carrier-grade DDoS scrubbing shields every website, server, and DNS zone.</p>
                                    </div>
                                </li>
                            </ul>
                        </div>

                        <!-- Quick Support Portals Section -->
                        <div class="cf-sidebar-quick-section pt-3 border-top mt-auto">
                            <div class="d-flex align-items-center justify-content-between mb-2 pb-1">
                                <span class="fs-12 fw-bold text-slate-900 text-uppercase tracking-wider">
                                    <i class="las la-bolt text-primary me-1"></i> Quick Support Portals
                                </span>
                                <span class="badge bg-slate-100 text-slate-600 rounded-pill px-2 py-0.5 fs-10 fw-semibold">Instant Access</span>
                            </div>

                            <div class="cf-quick-links-group d-flex flex-column gap-2">
                                <!-- Link 1: Client Area Portal -->
                                <a href="https://my.cloudflox.com" target="_blank" rel="noopener" class="cf-quick-action-card">
                                    <div class="cf-quick-icon-wrap icon-theme-blue">
                                        <i class="las la-user-circle"></i>
                                    </div>
                                    <div class="cf-quick-content">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <strong class="cf-quick-title">Client Area Portal</strong>
                                            <span class="cf-quick-pill">my.cloudflox</span>
                                        </div>
                                        <span class="cf-quick-sub">Manage DNS, services &amp; billing</span>
                                    </div>
                                    <div class="cf-quick-arrow">
                                        <i class="las la-arrow-right"></i>
                                    </div>
                                </a>

                                <!-- Link 2: Open Support Ticket -->
                                <a href="https://my.cloudflox.com/submitticket.php" target="_blank" rel="noopener" class="cf-quick-action-card">
                                    <div class="cf-quick-icon-wrap icon-theme-purple">
                                        <i class="las la-ticket-alt"></i>
                                    </div>
                                    <div class="cf-quick-content">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <strong class="cf-quick-title">Priority Support Ticket</strong>
                                            <span class="cf-quick-pill pill-pulse">&lt; 15 Mins SLA</span>
                                        </div>
                                        <span class="cf-quick-sub">Direct NOC incident dispatch</span>
                                    </div>
                                    <div class="cf-quick-arrow">
                                        <i class="las la-arrow-right"></i>
                                    </div>
                                </a>

                                <!-- Link 3: High-Performance VPS -->
                                <a href="<?php echo $url; ?>vps-hosting" class="cf-quick-action-card">
                                    <div class="cf-quick-icon-wrap icon-theme-indigo">
                                        <i class="las la-cloud"></i>
                                    </div>
                                    <div class="cf-quick-content">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <strong class="cf-quick-title">Cloud VPS Instances</strong>
                                            <span class="cf-quick-pill">From $7.99</span>
                                        </div>
                                        <span class="cf-quick-sub">Pure NVMe storage with root access</span>
                                    </div>
                                    <div class="cf-quick-arrow">
                                        <i class="las la-arrow-right"></i>
                                    </div>
                                </a>

                                <!-- Link 4: Dedicated Servers -->
                                <a href="<?php echo $url; ?>dedicated-server" class="cf-quick-action-card">
                                    <div class="cf-quick-icon-wrap icon-theme-cyan">
                                        <i class="las la-server"></i>
                                    </div>
                                    <div class="cf-quick-content">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <strong class="cf-quick-title">Dedicated Bare-Metal</strong>
                                            <span class="cf-quick-pill">10Gbps</span>
                                        </div>
                                        <span class="cf-quick-sub">High-bandwidth raw compute</span>
                                    </div>
                                    <div class="cf-quick-arrow">
                                        <i class="las la-arrow-right"></i>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <!-- Card Footer Trust Note -->
                        <div class="cf-sidebar-unified-footer mt-3 pt-3 border-top d-flex align-items-center justify-content-between text-muted fs-12">
                            <span><i class="las la-check-circle text-success me-1"></i> 99.9% Network Uptime</span>
                            <span><i class="las la-lock text-primary me-1"></i> 256-Bit SSL Secured</span>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section><!-- /Contact Form -->

    <!-- =========================================================================
         2. DIRECT COMMUNICATION CHANNELS & SLA GUARANTEE HUB
         ========================================================================= -->
    <section class="cf-contact-channels-section">
        <div class="container">

            <!-- Section Header -->
            <div class="text-center mb-5">
                <div class="section-tag mx-auto mb-3">
                    <i class="las la-headset me-1"></i> Fast-Track Channels
                </div>
                <h2 class="fs-24 fw-bold text-slate-900 mb-2">How Would You Like to Connect?</h2>
                <p class="text-muted fs-15 mx-auto" style="max-width:560px;">
                    Choose the fastest route for your request. Our specialized technical and enterprise teams are on standby 24/7/365.
                </p>
            </div>

            <!-- 3 Modern Actionable Communication Hub Cards -->
            <div class="row g-4 justify-content-center mb-4">

                <!-- Channel 1: Technical & Incident Support -->
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="cf-channel-card cf-channel-card-support h-100">
                        <div class="cf-channel-badge-top">
                            <span class="cf-channel-chip chip-blue">
                                <span class="cf-chip-pulse-dot"></span> Fastest Resolution
                            </span>
                        </div>
                        <div class="cf-channel-icon-wrap icon-blue">
                            <i class="las la-terminal"></i>
                        </div>
                        <h3 class="cf-channel-title">Priority NOC &amp; Ticket Desk</h3>
                        <p class="cf-channel-desc">
                            Active hosting, VPS reboot, DNS configuration, or server incident? Open a priority ticket with direct NOC engineer triage.
                        </p>
                        <div class="cf-channel-sla-pill">
                            <i class="las la-stopwatch text-blue"></i>
                            <span>Guaranteed <strong>&lt; 15 Mins</strong> Response SLA</span>
                        </div>
                        <div class="cf-channel-actions">
                            <a href="https://my.cloudflox.com/submitticket.php" target="_blank" rel="noopener" class="cf-btn-channel-primary w-100">
                                <span>Open Priority Ticket</span>
                                <i class="las la-arrow-right ms-2"></i>
                            </a>
                            <div class="text-center mt-2">
                                <a href="https://my.cloudflox.com" target="_blank" rel="noopener" class="cf-channel-sublink">
                                    <i class="las la-sign-in-alt me-1"></i> Client Portal Login &rarr;
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Channel 2: Solutions & Sales -->
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="cf-channel-card cf-channel-card-sales h-100">
                        <div class="cf-channel-badge-top">
                            <span class="cf-channel-chip chip-purple">
                                <i class="las la-rocket me-1"></i> Custom Deployments
                            </span>
                        </div>
                        <div class="cf-channel-icon-wrap icon-purple">
                            <i class="las la-network-wired"></i>
                        </div>
                        <h3 class="cf-channel-title">Solutions &amp; Cloud Architects</h3>
                        <p class="cf-channel-desc">
                            Planning dedicated bare-metal, high-bandwidth clusters, or need seamless zero-downtime migration from another provider?
                        </p>
                        <div class="cf-channel-sla-pill">
                            <i class="las la-file-invoice text-purple"></i>
                            <span>Custom Quotes &amp; <strong>Free White-Glove Migration</strong></span>
                        </div>
                        <div class="cf-channel-actions">
                            <a href="#contactForm" class="cf-btn-channel-secondary w-100">
                                <span>Consult Cloud Architects</span>
                                <i class="las la-arrow-down ms-2"></i>
                            </a>
                            <div class="text-center mt-2">
                                <a href="mailto:sales@cloudflox.com" class="cf-channel-sublink">
                                    <i class="las la-envelope me-1"></i> sales@cloudflox.com
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Channel 3: Direct Email & Executive Inquiries -->
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="cf-channel-card cf-channel-card-email h-100">
                        <div class="cf-channel-badge-top">
                            <span class="cf-channel-chip chip-emerald">
                                <i class="las la-shield-alt me-1"></i> Verified Inboxes
                            </span>
                        </div>
                        <div class="cf-channel-icon-wrap icon-emerald">
                            <i class="las la-envelope-open-text"></i>
                        </div>
                        <h3 class="cf-channel-title">Direct Department Desks</h3>
                        <p class="cf-channel-desc">
                            Prefer communicating via email? Reach our specialized departments directly with failover round-the-clock monitoring.
                        </p>
                        <div class="cf-channel-emails-list">
                            <div class="cf-channel-email-row">
                                <span class="cf-email-label">Technical:</span>
                                <a href="mailto:support@cloudflox.com" class="cf-email-val">support@cloudflox.com</a>
                            </div>
                            <div class="cf-channel-email-row">
                                <span class="cf-email-label">Sales &amp; Billing:</span>
                                <a href="mailto:sales@cloudflox.com" class="cf-email-val">sales@cloudflox.com</a>
                            </div>
                        </div>
                        <div class="cf-channel-actions mt-auto">
                            <a href="#contactForm" class="cf-btn-channel-emerald w-100">
                                <span>Send Direct Message</span>
                                <i class="las la-paper-plane ms-2"></i>
                            </a>
                            <div class="text-center mt-2">
                                <span class="cf-channel-subtext">
                                    <i class="las la-clock me-1"></i> 24/7 Monitored Dispatch
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Trust & SLA Guarantee Ribbon -->
            <div class="cf-contact-sla-ribbon">
                <div class="row g-3 align-items-center">
                    <div class="col-6 col-md-3">
                        <div class="cf-sla-item">
                            <div class="cf-sla-icon text-blue">
                                <i class="las la-bolt"></i>
                            </div>
                            <div>
                                <strong class="cf-sla-title">&lt; 15 Mins SLA</strong>
                                <span class="cf-sla-sub">Guaranteed response</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="cf-sla-item">
                            <div class="cf-sla-icon text-purple">
                                <i class="las la-shield-alt"></i>
                            </div>
                            <div>
                                <strong class="cf-sla-title">Multi-Tbps DDoS</strong>
                                <span class="cf-sla-sub">Always active protection</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="cf-sla-item">
                            <div class="cf-sla-icon text-emerald">
                                <i class="las la-user-shield"></i>
                            </div>
                            <div>
                                <strong class="cf-sla-title">100% In-House</strong>
                                <span class="cf-sla-sub">Real cloud sysadmins</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="cf-sla-item">
                            <div class="cf-sla-icon text-cyan">
                                <i class="las la-lock"></i>
                            </div>
                            <div>
                                <strong class="cf-sla-title">256-Bit SSL</strong>
                                <span class="cf-sla-sub">Zero-spam privacy</span>
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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>

    <script>
        function displayToast(heading, text, icon, loaderBg) {
            $.toast({
                heading: heading,
                text: text,
                position: "bottom-right",
                loaderBg: loaderBg,
                icon: icon,
                hideAfter: 15000,
                stack: 3,
            });
        }

        function form_submit() {

            var turnstileToken = "";
            var turnstileInput = document.querySelector('[name="cf-turnstile-response"]');
            if (turnstileInput && turnstileInput.value) {
                turnstileToken = turnstileInput.value;
            } else if (typeof turnstile !== "undefined" && typeof turnstile.getResponse === "function") {
                turnstileToken = turnstile.getResponse();
            }

            if (!turnstileToken || turnstileToken.length === 0) {
                displayToast("Security Verification", "Please complete the Cloudflare security verification check.", "error", "#c46a69");
                return;
            }

            document.getElementById("form-btn").classList.add("d-none");
            document.getElementById("form-btn2").classList.remove("d-none");

            var name = document.getElementById('name').value;
            var email = document.getElementById('email').value;
            var phone = document.getElementById('phone').value;
            var subject = document.getElementById('subject').value;
            var message = document.getElementById('message').value;

            var form = new FormData();
            form.append("name", name);
            form.append("email", email);
            form.append("phone", phone);
            form.append("subject", subject);
            form.append("message", message);
            form.append("cf-turnstile-response", turnstileToken);

            var r = new XMLHttpRequest();
            r.onreadystatechange = function() {
                if (r.readyState == 4) {
                    var t = r.responseText;

                    if (t == "success") {
                        displayToast("Success", "Thank you for contacting us. We'll get back to you shortly.", "success", "#3b7f5e");
                        document.getElementById("c-f").classList.add("d-none");
                        document.getElementById("th-c-f").classList.remove("d-none");
                    } else {
                        document.getElementById("form-btn").classList.remove("d-none");
                        document.getElementById("form-btn2").classList.add("d-none");
                        displayToast("Error", t, "error", "#c46a69");
                        if (typeof turnstile !== "undefined" && typeof turnstile.reset === "function") {
                            try { turnstile.reset(); } catch(e) {}
                        }
                    }
                }
            };

            r.open("POST", "include/send-mail/contact-send-email.php", true);
            r.send(form);
        }
    </script>

</body>

</html>