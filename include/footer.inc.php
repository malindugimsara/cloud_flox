<?php if (empty($hide_footer_cta)): ?>
    <!-- Subpage Bottom CTA Banner (Shown on subpages when not hidden) -->
    <section class="cf-subpage-cta-banner">
        <div class="container position-relative z-2">
            <div class="cf-subpage-cta-card">
                <div class="row align-items-center g-4">
                    <div class="col-lg-8 text-center text-lg-start">
                        <span class="cf-promo-badge mb-3">
                            <span class="cf-pulse-dot"></span>
                            <span>Enterprise Infrastructure</span>
                        </span>
                        <h2 class="cf-subpage-cta-title">
                            
                        </h2>
                        <p class="cf-subpage-cta-desc mb-0">
                            From high-performance NVMe VPS and Dedicated Bare-Metal Servers to secure Cloud Hosting and domain registration — CloudFlox delivers scalable, ultra-reliable infrastructure engineered for speed.
                        </p>
                        <div class="cf-subpage-cta-features d-flex flex-wrap gap-3 mt-3 justify-content-center justify-content-lg-start">
                            <span><i class="las la-check-circle"></i> NVMe Gen4 Storage</span>
                            <span><i class="las la-check-circle"></i> 99.99% Uptime SLA</span>
                            <span><i class="las la-check-circle"></i> Instant Deployment</span>
                            <span><i class="las la-check-circle"></i> 24/7 Expert Support</span>
                        </div>
                    </div>
                    <div class="col-lg-4 text-center text-lg-end">
                        <div class="d-flex flex-column flex-sm-row flex-lg-column gap-3 justify-content-center">
                            <a href="https://my.cloudflox.com/cart.php?a=view" class="cf-btn-primary-glow">
                                <span>Get Started Today</span>
                                <i class="las la-arrow-right"></i>
                            </a>
                            <a href="contact-us" class="cf-btn-outline-glow">
                                <i class="las la-headset"></i>
                                <span>Talk to an Expert</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>

    <!-- =========================================================================
         MODERN CLOUDFLOX ENTERPRISE FOOTER
         ========================================================================= -->
    <footer class="cf-modern-footer position-relative z-1">
        
        <!-- Top Infrastructure Status & Telemetry Ribbon -->
        <div class="cf-footer-status-ribbon">
            <div class="container">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 py-3">
                    <!-- Live Status Beacon -->
                    <div class="cf-footer-status d-flex align-items-center gap-2">
                        <span class="cf-status-beacon">
                            <span class="cf-status-ping"></span>
                            <span class="cf-status-dot"></span>
                        </span>
                        <span class="cf-status-text">
                            <strong>All Cloud Systems &amp; Datacenters Operational</strong>
                        </span>
                        <span class="cf-sla-pill">
                            <i class="las la-shield-alt"></i> 99.99% SLA
                        </span>
                    </div>

                    <!-- Trust Telemetry Badges -->
                    <div class="cf-footer-telemetry d-none d-md-flex align-items-center gap-4">
                        <span class="cf-telemetry-item">
                            <i class="las la-bolt text-warning"></i> Tier-3+ Global DC
                        </span>
                        <span class="cf-telemetry-item">
                            <i class="las la-shield-alt text-primary"></i> Always-On Anti-DDoS
                        </span>
                        <span class="cf-telemetry-item">
                            <i class="las la-microchip text-info"></i> NVMe Gen4 Cloud Fabric
                        </span>
                        <span class="cf-telemetry-item">
                            <i class="las la-clock text-success"></i> 24/7/365 Human NOC
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Footer Navigation & Columns -->
        <div class="container pt-5 pb-4 footer-responsive">
            <div class="row g-4 g-lg-5">
                
                <!-- Brand Column -->
                <div class="col-lg-4 col-md-12">
                    <div class="cf-footer-brand-wrap pe-lg-4">
                        <a href="<?php echo $url ?? ''; ?>" class="d-inline-block mb-3">
                            <img src="assets/imgs/logos/cloud-flox-main.png" alt="CloudFlox Cloud Hosting" class="cf-footer-logo-img">
                        </a>
                        <p class="cf-footer-brand-desc">
                            Enterprise-grade cloud infrastructure engineered for velocity. Deploy ultra-fast NVMe VPS, dedicated bare-metal servers, and secure cloud hosting with 99.99% guaranteed uptime and 24/7 human engineering support.
                        </p>
                        
                        <!-- Social Communities -->
                        <div class="cf-footer-socials d-flex align-items-center gap-2 mb-4">
                            <a href="#" class="cf-social-btn" aria-label="CloudFlox on X (Twitter)">
                                <i class="lab la-twitter"></i>
                            </a>
                            <a href="#" class="cf-social-btn" aria-label="CloudFlox on Facebook">
                                <i class="lab la-facebook-f"></i>
                            </a>
                            <a href="#" class="cf-social-btn" aria-label="CloudFlox on LinkedIn">
                                <i class="lab la-linkedin-in"></i>
                            </a>
                            <a href="#" class="cf-social-btn" aria-label="CloudFlox on YouTube">
                                <i class="lab la-youtube"></i>
                            </a>
                            <a href="contact-us" class="cf-social-btn" aria-label="CloudFlox Support Desk">
                                <i class="las la-headset"></i>
                            </a>
                        </div>

                        <!-- Direct Portal Quick Link -->
                        <a href="https://my.cloudflox.com/index.php?rp=/login" class="cf-footer-portal-chip">
                            <i class="las la-user-circle"></i>
                            <span>Access Client Portal</span>
                            <i class="las la-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Column 1: Hosting Solutions -->
                <div class="col-lg-2 col-md-4 col-6">
                    <div class="cf-footer-col">
                        <h6 class="cf-footer-col-title">
                            <span>Hosting Solutions</span>
                        </h6>
                        <ul class="cf-footer-links list-unstyled">
                            <li><a href="shared-hosting" class="cf-footer-link">Shared Hosting</a></li>
                            <li>
                                <a href="vps-hosting" class="cf-footer-link d-inline-flex align-items-center gap-2">
                                    <span>VPS Hosting</span>
                                    <span class="cf-nav-hot-pill">HOT</span>
                                </a>
                            </li>
                            <li><a href="dedicated-server" class="cf-footer-link">Dedicated Servers</a></li>
                            <li><a href="domain" class="cf-footer-link">Domain Names</a></li>
                            <li><a href="vps-hosting#storage-vps" class="cf-footer-link">Storage VPS</a></li>
                            <li><a href="shared-hosting#wordpress" class="cf-footer-link">WordPress Cloud</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Column 2: Platform & Features -->
                <div class="col-lg-3 col-md-4 col-6">
                    <div class="cf-footer-col ps-lg-3">
                        <h6 class="cf-footer-col-title">
                            <span>Platform &amp; Tech</span>
                        </h6>
                        <ul class="cf-footer-links list-unstyled">
                            <li><a href="#!" class="cf-footer-link">Tier-3 Data Centers</a></li>
                            <li><a href="#!" class="cf-footer-link">cPanel &amp; DirectAdmin</a></li>
                            <li><a href="#!" class="cf-footer-link">Linux &amp; Windows OS</a></li>
                            <li><a href="#!" class="cf-footer-link">AMD EPYC™ Hardware</a></li>
                            <li><a href="#!" class="cf-footer-link">NVMe Gen4 Storage</a></li>
                            <li><a href="#!" class="cf-footer-link">DDoS Guard &amp; Firewall</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Column 3: Support & Company -->
                <div class="col-lg-3 col-md-4 col-12">
                    <div class="cf-footer-col">
                        <h6 class="cf-footer-col-title">
                            <span>Support &amp; Portal</span>
                        </h6>
                        <ul class="cf-footer-links list-unstyled">
                            <li><a href="contact-us" class="cf-footer-link">Contact Specialist Team</a></li>
                            <li><a href="https://my.cloudflox.com/submitticket.php" class="cf-footer-link">Submit Support Ticket</a></li>
                            <li><a href="https://my.cloudflox.com/index.php?rp=/knowledgebase" class="cf-footer-link">Knowledgebase &amp; FAQs</a></li>
                            <li><a href="https://my.cloudflox.com/index.php?rp=/announcements" class="cf-footer-link">Network Announcements</a></li>
                            <li><a href="https://my.cloudflox.com/index.php?rp=/login" class="cf-footer-link">Server Status &amp; Metrics</a></li>
                            <li><a href="https://my.cloudflox.com/cart.php?a=view" class="cf-footer-link">Order &amp; Billing Cart</a></li>
                        </ul>
                    </div>
                </div>

            </div>

            <!-- Department Direct Support Hub (3 Glass Cards) -->
            <div class="cf-footer-support-hub mt-5 pt-4">
                <div class="row g-3">
                    
                    <!-- Card 1: Technical Support -->
                    <div class="col-lg-4 col-md-6">
                        <div class="cf-dept-card">
                            <div class="cf-dept-card-header d-flex align-items-center justify-content-between mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="cf-dept-icon cf-icon-cyan">
                                        <i class="las la-server"></i>
                                    </div>
                                    <h6 class="cf-dept-name mb-0">Technical Support &amp; NOC</h6>
                                </div>
                                <span class="cf-dept-pill">24/7/365</span>
                            </div>
                            <p class="cf-dept-desc">
                                Emergency assistance for server downtime, migrations, DNS routing, and technical troubleshooting.
                            </p>
                            <a href="mailto:support@cloudflox.com" class="cf-dept-email-btn">
                                <i class="las la-envelope"></i>
                                <span>support@cloudflox.com</span>
                                <i class="las la-arrow-right ms-auto"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Card 2: Sales & Hosting -->
                    <div class="col-lg-4 col-md-6">
                        <div class="cf-dept-card">
                            <div class="cf-dept-card-header d-flex align-items-center justify-content-between mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="cf-dept-icon cf-icon-emerald">
                                        <i class="las la-rocket"></i>
                                    </div>
                                    <h6 class="cf-dept-name mb-0">Sales &amp; Solutions</h6>
                                </div>
                                <span class="cf-dept-pill">Solutions Desk</span>
                            </div>
                            <p class="cf-dept-desc">
                                Custom bare-metal configurations, enterprise migrations, hybrid clusters, and volume discounts.
                            </p>
                            <a href="mailto:sales@cloudflox.com" class="cf-dept-email-btn">
                                <i class="las la-envelope"></i>
                                <span>sales@cloudflox.com</span>
                                <i class="las la-arrow-right ms-auto"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Card 3: Billing & Accounts -->
                    <div class="col-lg-4 col-md-12">
                        <div class="cf-dept-card">
                            <div class="cf-dept-card-header d-flex align-items-center justify-content-between mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="cf-dept-icon cf-icon-amber">
                                        <i class="las la-credit-card"></i>
                                    </div>
                                    <h6 class="cf-dept-name mb-0">Billing &amp; Accounts</h6>
                                </div>
                                <span class="cf-dept-pill">Fast Handling</span>
                            </div>
                            <p class="cf-dept-desc">
                                Invoices, renewals, refunds, crypto payment verification, and corporate billing account assistance.
                            </p>
                            <a href="mailto:billing@cloudflox.com" class="cf-dept-email-btn">
                                <i class="las la-envelope"></i>
                                <span>billing@cloudflox.com</span>
                                <i class="las la-arrow-right ms-auto"></i>
                            </a>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Payment Security & Official Registration Bar -->
            <div class="cf-footer-compliance-box mt-4">
                <div class="row align-items-center g-3 text-center text-md-start">
                    
                    <!-- Accepted Payments Badge Wrap -->
                    <div class="col-lg-6 col-md-12 d-flex flex-column flex-sm-row align-items-center gap-3 justify-content-center justify-content-lg-start">
                        <span class="cf-compliance-label d-flex align-items-center gap-2">
                            <i class="las la-lock text-success"></i>
                            <span>Accepted Payment Methods:</span>
                        </span>
                        <div class="cf-payment-pill-card">
                            <img src="assets/imgs/payments.png" alt="Visa, Mastercard, American Express, PayPal, USDT Payments" class="cf-payment-img">
                        </div>
                    </div>

                    <!-- Registered Legal Entity -->
                    <div class="col-lg-6 col-md-12 text-center text-lg-end">
                        <div class="cf-company-address">
                            <span class="cf-entity-flag">🇺🇸</span>
                            <strong>CloudFlox LLC</strong> &middot; Registered in Oregon, USA
                            <div class="cf-address-line">
                                2709 N Hayden Island Dr STE 444529, Portland OR 97217, USA
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Bottom Legal & Copyright Bar -->
            <div class="cf-footer-bottom-bar mt-4 pt-4">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                    
                    <!-- Copyright -->
                    <p class="cf-footer-copy mb-0">
                        &copy; <?php echo date('Y'); ?> <strong>CloudFlox LLC</strong>. All rights reserved. Engineered for modern cloud performance.
                    </p>

                    <!-- Trust Markers & Legal Links -->
                    <div class="d-flex align-items-center gap-4 flex-wrap">
                        <span class="cf-ssl-guarantee d-none d-sm-inline-flex align-items-center gap-1">
                            <i class="las la-shield-alt text-success"></i> 256-Bit SSL Encrypted
                        </span>
                        <div class="cf-legal-links d-inline-flex align-items-center gap-3">
                            <a href="terms-of-service" class="cf-legal-link">Terms of Service</a>
                            <span class="cf-legal-sep">&middot;</span>
                            <a href="privacy-policy" class="cf-legal-link">Privacy Policy</a>
                            <span class="cf-legal-sep">&middot;</span>
                            <a href="refund-policy" class="cf-legal-link">Refund Policy</a>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </footer>

    <!-- Modern Elevated Scroll to Top Button -->
    <div class="scroll-top scroll-to-target cf-scroll-top-btn" data-target="html" aria-label="Back to top">
        <i class="las la-arrow-up"></i>
    </div>

    <!-- Start of Tawk.to Script with on-demand openLiveChat helper -->
    <script type="text/javascript">
        var tawkLoaded = false;
        window.Tawk_API = window.Tawk_API || {};
        window.Tawk_LoadStart = new Date();

        function loadTawkTo(callback) {
            if (tawkLoaded) {
                if (callback) callback();
                return;
            }
            tawkLoaded = true;
            if (callback) {
                var prevOnLoad = window.Tawk_API.onLoad;
                window.Tawk_API.onLoad = function() {
                    if (typeof prevOnLoad === 'function') prevOnLoad();
                    callback();
                };
            }
            var s1 = document.createElement("script"),
                s0 = document.getElementsByTagName("script")[0];
            s1.async = true;
            s1.src = 'https://embed.tawk.to/675a684749e2fd8dfef6be84/1iesiemke';
            s1.charset = 'UTF-8';
            s1.setAttribute('crossorigin', '*');
            s0.parentNode.insertBefore(s1, s0);
        }

        function openLiveChat(e) {
            if (e && e.preventDefault) e.preventDefault();
            if (window.Tawk_API && typeof window.Tawk_API.maximize === 'function') {
                window.Tawk_API.maximize();
                return false;
            }
            loadTawkTo(function() {
                if (window.Tawk_API && typeof window.Tawk_API.maximize === 'function') {
                    window.Tawk_API.maximize();
                }
            });
            return false;
        }

        window.addEventListener("load", function() {
            setTimeout(function() {
                if (!tawkLoaded) {
                    loadTawkTo();
                }
            }, 4000); // 4000ms delay for optimal initial page load performance
        });
    </script>
    <!-- End of Tawk.to Script -->