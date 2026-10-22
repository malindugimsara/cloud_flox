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
    <meta property="og:image" content="https://cloudflox.com/assets/imgs/banners/cloudflox-contact-support.jpg">
    <meta property="og:site_name" content="CloudFlox">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Contact CloudFlox">
    <meta name="twitter:description" content="Get help with hosting, domains, and servers from CloudFlox support.">
    <meta name="twitter:image" content="https://cloudflox.com/assets/imgs/banners/cloudflox-contact-support.jpg">

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
    <!-- toast -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.css" />
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
            <img src="assets/imgs/shape/pattern-2.svg?v=2" alt="" width="370" height="590" loading="eager" decoding="async">
        </div>
        <div class="pricing-bg-shape pricing-shape-right" aria-hidden="true" style="opacity: 0.4;">
            <img src="assets/imgs/shape/pattern-3.svg?v=2" alt="" width="370" height="590" loading="eager" decoding="async">
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
                        <a href="tel:0112617664" class="cf-btn-contact-outline text-center">
                            <i class="las la-phone-alt me-2"></i>
                            <span>0112 617 664</span>
                        </a>
                    </div>
                </div>

                <!-- Right Column: Visual Hero Infrastructure Showcase -->
                <div class="col-12 col-lg-5">
                    <div class="cf-contact-hero-visual position-relative text-center">
                        <div class="cf-hero-img-wrap position-relative mx-auto">
                            <img src="assets/imgs/banners/cloudflox-contact-support.jpg"
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
         2. CONTACT INFO CARDS
         ========================================================================= -->
    <section class="cf-contact-cards-section">
        <div class="container">
            <div class="row g-4 justify-content-center">

                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="cf-contact-info-card">
                        <div class="cf-contact-info-icon bg-blue-subtle text-blue">
                            <i class="las la-phone-alt"></i>
                        </div>
                        <h3 class="fs-15 fw-bold text-slate-900 mb-1">Phone</h3>
                        <p class="fs-13 text-muted mb-0">0112 617 664</p>
                        <p class="fs-13 text-muted mb-0">070 211 59 38</p>
                        <p class="fs-13 text-muted mb-0">070 211 59 37</p>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="cf-contact-info-card">
                        <div class="cf-contact-info-icon bg-blue-subtle text-blue">
                            <i class="las la-map-marker-alt"></i>
                        </div>
                        <h3 class="fs-15 fw-bold text-slate-900 mb-1">Office</h3>
                        <p class="fs-13 text-muted mb-0">BeesPoint (Pvt) Ltd</p>
                        <p class="fs-13 text-muted mb-0">140/1, Moratuwa Rd</p>
                        <p class="fs-13 text-muted mb-0">Piliyandala, Sri Lanka</p>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="cf-contact-info-card">
                        <div class="cf-contact-info-icon bg-blue-subtle text-blue">
                            <i class="las la-envelope"></i>
                        </div>
                        <h3 class="fs-15 fw-bold text-slate-900 mb-1">Email</h3>
                        <p class="fs-13 text-muted mb-0">support@cloudflox.com</p>
                        <p class="fs-13 text-muted mb-0">sales@cloudflox.com</p>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="cf-contact-info-card">
                        <div class="cf-contact-info-icon bg-blue-subtle text-blue">
                            <i class="las la-clock"></i>
                        </div>
                        <h3 class="fs-15 fw-bold text-slate-900 mb-1">Working Hours</h3>
                        <p class="fs-13 text-muted mb-0">Mon - Fri: 9am - 6pm</p>
                        <p class="fs-13 text-muted mb-0">Sat: 9am - 1pm</p>
                        <p class="fs-13 text-muted mb-0">Support: 24/7 Online</p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- =========================================================================
         3. CONTACT FORM SECTION
         ========================================================================= -->
    <section class="cf-contact-form-section" id="contactForm">
        <div class="container">

            <!-- Section Header -->
            <div class="text-center mb-5">
                <div class="section-tag mx-auto mb-3">
                    <i class="las la-paper-plane me-1"></i> Send a Message
                </div>
                <h2 class="fs-32 fw-bold text-slate-900 mb-2">Get in Touch With Our Team</h2>
                <p class="text-muted fs-15 mx-auto" style="max-width:520px;">
                    Fill out the form below and one of our specialists will respond to your query as quickly as possible.
                </p>
            </div>

            <div class="row g-5 align-items-start justify-content-center">

                <!-- Form Card -->
                <div class="col-12 col-lg-7">
                    <div class="cf-contact-form-card">

                        <!-- Active form -->
                        <form id="c-f">
                            <div class="row g-3">

                                <div class="col-12 col-sm-6">
                                    <div class="cf-form-group">
                                        <label class="cf-form-label" for="name">
                                            Full Name <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" id="name"
                                            placeholder="Enter your full name"
                                            class="cf-form-input">
                                    </div>
                                </div>

                                <div class="col-12 col-sm-6">
                                    <div class="cf-form-group">
                                        <label class="cf-form-label" for="email">
                                            Email Address <span class="text-danger">*</span>
                                        </label>
                                        <input type="email" id="email"
                                            placeholder="you@example.com"
                                            class="cf-form-input">
                                    </div>
                                </div>

                                <div class="col-12 col-sm-6">
                                    <div class="cf-form-group">
                                        <label class="cf-form-label" for="phone">
                                            Phone <span class="text-muted fw-normal">(Optional)</span>
                                        </label>
                                        <input type="tel" id="phone"
                                            placeholder="XX XXX XXXX"
                                            class="cf-form-input">
                                    </div>
                                </div>

                                <div class="col-12 col-sm-6">
                                    <div class="cf-form-group">
                                        <label class="cf-form-label" for="subject">
                                            Subject
                                        </label>
                                        <input type="text" id="subject"
                                            placeholder="e.g. Hosting setup / VPS upgrade"
                                            class="cf-form-input">
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="cf-form-group">
                                        <label class="cf-form-label" for="message">
                                            Message <span class="text-danger">*</span>
                                        </label>
                                        <textarea id="message"
                                            placeholder="Tell us what you need help with (domain, hosting plan, error message, etc.)"
                                            class="cf-form-input cf-form-textarea"></textarea>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="g-recaptcha" data-sitekey="6Le_PGssAAAAAIXSCvbRBvKbhGpoyiLAxd3fMA_H"></div>
                                </div>

                            </div><!-- /row -->

                            <div class="d-flex gap-3 mt-4">
                                <button type="button" id="form-btn" onclick="form_submit();"
                                    class="cf-btn-contact-submit">
                                    <i class="las la-paper-plane me-2"></i>
                                    Send Message
                                </button>
                                <button type="button" id="form-btn2"
                                    class="cf-btn-contact-submit d-none" disabled>
                                    <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                    Sending...
                                </button>
                            </div>

                        </form>

                        <!-- Thank you state -->
                        <div class="cf-contact-thankyou d-none" id="th-c-f">
                            <div class="text-center py-4">
                                <img src="assets/imgs/success.gif" alt="Thank you" style="width: 220px; margin-bottom: 16px;">
                                <h2 class="fs-22 fw-bold text-slate-900 mb-2">Message Sent!</h2>
                                <p class="text-muted fs-14 mb-4">
                                    Thank you for reaching out. We have received your message and will get back to you shortly.
                                </p>
                                <a href="<?php echo $url; ?>" class="cf-btn-contact-outline">
                                    <i class="las la-home me-1"></i> Back to Home
                                </a>
                            </div>
                        </div>

                    </div><!-- /card -->
                </div>

                <!-- Sidebar -->
                <div class="col-12 col-lg-4 col-xl-4">

                    <!-- Why Contact Us? -->
                    <div class="cf-contact-sidebar-block mb-4">
                        <h3 class="fs-15 fw-bold text-slate-900 mb-3">Why Contact CloudFlox?</h3>
                        <ul class="cf-contact-sidebar-list">
                            <li>
                                <span class="cf-sidebar-icon bg-blue-subtle text-blue"><i class="las la-headset"></i></span>
                                <div>
                                    <strong>Expert Support</strong>
                                    <p class="text-muted fs-13 mb-0">Our team specializes in web hosting, VPS, and server management.</p>
                                </div>
                            </li>
                            <li>
                                <span class="cf-sidebar-icon bg-blue-subtle text-blue"><i class="las la-bolt"></i></span>
                                <div>
                                    <strong>Fast Resolutions</strong>
                                    <p class="text-muted fs-13 mb-0">Most tickets are resolved within the same business hour.</p>
                                </div>
                            </li>
                            <li>
                                <span class="cf-sidebar-icon bg-blue-subtle text-blue"><i class="las la-shield-alt"></i></span>
                                <div>
                                    <strong>Secure Communication</strong>
                                    <p class="text-muted fs-13 mb-0">Your information is always encrypted and kept confidential.</p>
                                </div>
                            </li>
                            <li>
                                <span class="cf-sidebar-icon bg-blue-subtle text-blue"><i class="las la-comment-dots"></i></span>
                                <div>
                                    <strong>24/7 Online Support</strong>
                                    <p class="text-muted fs-13 mb-0">Submit tickets anytime - our system is always open.</p>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <!-- Quick Links -->
                    <div class="cf-contact-sidebar-block">
                        <h3 class="fs-15 fw-bold text-slate-900 mb-3">Quick Support Links</h3>
                        <div class="d-flex flex-column gap-2">
                            <a href="https://my.cloudflox.com" target="_blank" rel="noopener" class="cf-contact-quick-link">
                                <i class="las la-user-circle me-2 text-blue"></i>
                                Client Area Portal
                                <i class="las la-arrow-right ms-auto text-muted"></i>
                            </a>
                            <a href="https://my.cloudflox.com/submitticket.php" target="_blank" rel="noopener" class="cf-contact-quick-link">
                                <i class="las la-ticket-alt me-2 text-blue"></i>
                                Open Support Ticket
                                <i class="las la-arrow-right ms-auto text-muted"></i>
                            </a>
                            <a href="<?php echo $url; ?>budget-hosting" class="cf-contact-quick-link">
                                <i class="las la-server me-2 text-blue"></i>
                                Budget Hosting Plans
                                <i class="las la-arrow-right ms-auto text-muted"></i>
                            </a>
                            <a href="<?php echo $url; ?>dedicated-server" class="cf-contact-quick-link">
                                <i class="las la-database me-2 text-blue"></i>
                                Dedicated Servers
                                <i class="las la-arrow-right ms-auto text-muted"></i>
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section><!-- /Contact Form -->

    <!-- =========== Footer Area =========== -->
    <?php include 'include/footer.inc.php'; ?>
    <!-- =========== Footer Area =========== -->

    <!-- =========== JS Area =========== -->
    <?php include 'include/js.inc.php'; ?>
    <!-- =========== JS Area =========== -->

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

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

            var recaptchaResponse = grecaptcha.getResponse();

            if (recaptchaResponse.length === 0) {
                displayToast("Error", "Please verify that you are not a robot.", "error", "#c46a69");
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
            form.append("g-recaptcha-response", recaptchaResponse);

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
                        grecaptcha.reset();
                    }
                }
            };

            r.open("POST", "include/send-mail/contact-send-email.php", true);
            r.send(form);
        }
    </script>

</body>

</html>