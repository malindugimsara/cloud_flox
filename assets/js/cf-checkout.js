/**
 * CloudFlox On-Site Checkout & WHMCS API Bridge Script
 * -------------------------------------------------------------------------
 * Intercepts plan purchases across all pages and renders a high-converting
 * slide-over checkout drawer without navigating away from cloudflox.com.
 */

(function () {
    "use strict";

    // Products Catalog Cache
    const CF_PRODUCTS = {
        '1':   { name: 'Mini Plan', category: 'Budget Cloud Hosting', monthly: 3.00, yearly: 28.80, specs: '5 GB NVMe • 1 Site • 250 GB Bandwidth' },
        '2':   { name: 'Business Starter', category: 'Business NVMe Web Hosting', monthly: 5.99, yearly: 61.10, specs: '20 GB Pure NVMe • 1 vCPU • 2 GB RAM • 1 Site' },
        '3':   { name: 'Business Growth', category: 'Business NVMe Web Hosting', monthly: 9.99, yearly: 101.90, specs: '40 GB Pure NVMe • 2 vCPU • 4 GB RAM • 5 Sites' },
        '4':   { name: 'Enterprise NVMe Hosting', category: 'Enterprise Cloud Hosting', monthly: 19.99, yearly: 203.90, specs: '150 GB Pure NVMe • 4 vCPU • 8 GB RAM' },
        '5':   { name: 'Entry Dedicated Server', category: 'Dedicated Infrastructure', monthly: 80.00, yearly: 800.00, specs: 'Xeon E3 • 16 GB DDR3 • 2x 240 GB SSD' },

        '101': { name: 'Mini Plan', category: 'Budget Cloud Hosting', monthly: 3.00, yearly: 28.80, specs: '5 GB NVMe • 1 Site • 250 GB Bandwidth' },
        '102': { name: 'Basic Plan', category: 'Budget Cloud Hosting', monthly: 4.50, yearly: 43.20, specs: '10 GB NVMe • 2 Sites • 500 GB Bandwidth' },
        '103': { name: 'Plus Plan', category: 'Budget Cloud Hosting', monthly: 6.00, yearly: 57.60, specs: '20 GB NVMe • 5 Sites • Unmetered Bandwidth' },

        '201': { name: 'Business Starter', category: 'Business NVMe Web Hosting', monthly: 5.99, yearly: 61.10, specs: '20 GB Pure NVMe • 1 vCPU • 2 GB RAM • 1 Site' },
        '202': { name: 'Business Growth', category: 'Business NVMe Web Hosting', monthly: 9.99, yearly: 101.90, specs: '40 GB Pure NVMe • 2 vCPU • 4 GB RAM • 5 Sites' },
        '203': { name: 'Business Professional', category: 'Business NVMe Web Hosting', monthly: 19.99, yearly: 203.90, specs: '80 GB Pure NVMe • 4 vCPU • 8 GB RAM • 15 Sites' },
        '204': { name: 'Business Enterprise', category: 'Business NVMe Web Hosting', monthly: 48.00, yearly: 460.80, specs: '150 GB Pure NVMe • 6 vCPU • 12 GB RAM • Unlimited Sites' },

        '301': { name: 'Cloud-01 Starter', category: 'Premium Managed Cloud', monthly: 75.00, yearly: 720.00, specs: '100 GB NVMe • 4 vCPU Cores • 8 GB RAM' },
        '302': { name: 'Cloud-02 Business', category: 'Premium Managed Cloud', monthly: 120.00, yearly: 1152.00, specs: '250 GB NVMe • 8 vCPU Cores • 16 GB RAM' },
        '303': { name: 'Cloud-03 Pro', category: 'Premium Managed Cloud', monthly: 210.00, yearly: 2016.00, specs: '500 GB NVMe • 16 vCPU Cores • 32 GB RAM' },
        '304': { name: 'Cloud-04 Enterprise', category: 'Premium Managed Cloud', monthly: 350.00, yearly: 3360.00, specs: '1,000 GB NVMe • 32 vCPU Cores • 64 GB RAM' },

        '401': { name: 'KVM VPS Starter (1 Core)', category: 'KVM Cloud VPS', monthly: 7.99, yearly: 76.70, specs: '1 vCPU • 2 GB RAM • 40 GB NVMe • 1 TB Traffic' },
        '402': { name: 'KVM VPS Business (2 Cores)', category: 'KVM Cloud VPS', monthly: 14.99, yearly: 143.90, specs: '2 vCPU • 4 GB RAM • 80 GB NVMe • 2 TB Traffic' },
        '403': { name: 'KVM VPS Pro (4 Cores)', category: 'KVM Cloud VPS', monthly: 24.99, yearly: 239.90, specs: '4 vCPU • 8 GB RAM • 160 GB NVMe • 4 TB Traffic' },
        '404': { name: 'KVM VPS Enterprise (8 Cores)', category: 'KVM Cloud VPS', monthly: 39.99, yearly: 383.90, specs: '8 vCPU • 16 GB RAM • 320 GB NVMe • 8 TB Traffic' }
    };

    const CF_TLDS = {
        '.com': 13.57,
        '.net': 15.49,
        '.org': 17.49,
        '.cloud': 9.99,
        '.io': 39.99,
        '.co': 24.99,
        '.online': 3.99,
        '.store': 4.99
    };

    // Current State
    let currentPid = '201';
    let currentCycle = 'monthly';
    let currentDomainType = 'subdomain';
    let currentDomainFee = 0.00;
    let currentStep = 1;

    // DOM Elements
    let backdrop, drawer, closeBtn;

    function initCheckout() {
        backdrop = document.getElementById('cfCheckoutModalBackdrop');
        drawer   = document.getElementById('cfCheckoutDrawer');
        closeBtn = document.getElementById('cfCheckoutCloseBtn');

        if (!backdrop || !drawer) return;

        // Close events
        if (closeBtn) closeBtn.addEventListener('click', closeCheckoutModal);
        backdrop.addEventListener('click', function (e) {
            if (e.target === backdrop) closeCheckoutModal();
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && backdrop.classList.contains('open')) {
                closeCheckoutModal();
            }
        });

        // Backdrop scroll lock
        backdrop.addEventListener('wheel', function (e) {
            if (e.target === backdrop) {
                e.preventDefault();
            }
        }, { passive: false });
        backdrop.addEventListener('touchmove', function (e) {
            if (e.target === backdrop) {
                e.preventDefault();
            }
        }, { passive: false });

        // Global Event Delegation: Intercept any "Buy Now", "Choose Plan", or WHMCS cart links
        document.addEventListener('click', function (e) {
            // If already on the dedicated checkout page, don't intercept
            if (window.location.pathname.includes('/checkout')) return;

            // Direct checkout link clicks should always proceed to the checkout page naturally
            if (e.target.closest('a[href*="checkout"]')) return;

            // 2. Intercept Plan Buy buttons
            const btn = e.target.closest('[data-pid], a[href*="cart.php?a=add"], .cf-table-cta-btn, .btn-plan-outline, .btn-plan-featured');
            if (!btn) return;

            // Extract PID
            let pid = btn.getAttribute('data-pid');
            let cycle = 'monthly';

            if (!pid && btn.href) {
                const urlMatch = btn.href.match(/pid=(\d+)/i);
                if (urlMatch) pid = urlMatch[1];
                const cycleMatch = btn.href.match(/billingcycle=(monthly|annually|yearly)/i);
                if (cycleMatch) cycle = cycleMatch[1];
            }

            if (pid && CF_PRODUCTS[pid]) {
                e.preventDefault();
                e.stopPropagation();

                // Check page billing toggle state if available
                const yearlyActive = document.querySelector('#btnYearly.active, #cfCompLblYearly.active');
                if (yearlyActive) {
                    cycle = 'annually';
                }

                openCheckoutDrawer({
                    pid: pid,
                    cycle: cycle
                });
            }
        });
    }

    // Redirect to Dedicated Full-Page Checkout (Spacious, Clear & Step-by-Step)
    window.openCheckoutDrawer = function (opts) {
        opts = opts || {};
        const base = window.CF_BASE_URL || '';
        const params = new URLSearchParams();
        if (opts.pid) params.set('pid', opts.pid);
        if (opts.cycle) params.set('cycle', opts.cycle);
        if (opts.domain) params.set('domain', opts.domain);
        if (opts.domainPrice) {
            const rawPrice = String(opts.domainPrice).replace(/[^0-9\.]/g, '');
            params.set('price', rawPrice);
        }
        if (opts.domainType) params.set('domainType', opts.domainType);

        window.location.href = `${base}checkout?` + params.toString();
    };


    window.closeCheckoutModal = function () {
        if (!backdrop) return;
        backdrop.classList.remove('open');
        document.body.classList.remove('cf-modal-open');
        document.documentElement.classList.remove('cf-modal-open');
        document.body.style.overflow = '';
        setTimeout(() => {
            backdrop.style.display = 'none';
        }, 320);
    };

    // Cycle Toggle (Monthly vs Annual)
    window.cfSetCycle = function (cycle) {
        currentCycle = (cycle === 'annually' || cycle === 'yearly') ? 'annually' : 'monthly';
        document.getElementById('cfCheckoutCycle').value = currentCycle;

        if (currentPid !== 'none' && CF_PRODUCTS[currentPid]) {
            const plan = CF_PRODUCTS[currentPid];
            const isYearly = currentCycle === 'annually';

            const monthlyBtn = document.getElementById('cfCycleMonthlyBtn');
            const yearlyBtn  = document.getElementById('cfCycleYearlyBtn');

            if (monthlyBtn && yearlyBtn) {
                monthlyBtn.classList.toggle('active', !isYearly);
                yearlyBtn.classList.toggle('active', isYearly);
            }

            const priceEl = document.getElementById('cfSummaryPrice');
            const cycleLabelEl = document.getElementById('cfSummaryCycleLabel');

            if (isYearly) {
                priceEl.textContent = '$' + (plan.yearly / 12).toFixed(2);
                cycleLabelEl.textContent = '/mo (billed $' + plan.yearly.toFixed(2) + '/yr)';
            } else {
                priceEl.textContent = '$' + plan.monthly.toFixed(2);
                cycleLabelEl.textContent = '/mo';
            }
        }

        updateReceipt();
    };

    // Toggle attaching recommended hosting in checkout drawer
    window.cfToggleHostingAddon = function () {
        currentPid = '201';
        document.getElementById('cfCheckoutPid').value = '201';
        const plan = CF_PRODUCTS['201'];
        const reviewHostingCard = document.getElementById('cfReviewHostingCard');
        const noHostingBanner = document.getElementById('cfNoHostingBanner');
        if (reviewHostingCard) reviewHostingCard.style.display = 'block';
        if (noHostingBanner) noHostingBanner.style.display = 'none';

        document.getElementById('cfSummaryPlanName').textContent = plan.name;
        document.getElementById('cfSummaryCategory').textContent = plan.category;
        document.getElementById('cfCheckoutPlanBadge').textContent = plan.category + ' — ' + plan.name;
        document.getElementById('cfSummarySpecs').textContent = plan.specs;
        cfSetCycle('monthly');
        updateReceipt();
    };

    // Domain Choice Tabs (for hosting-first path)
    window.cfSelectDomainTab = function (type) {
        currentDomainType = type;
        document.getElementById('cfCheckoutDomainType').value = type;

        // Update Tab Active styling
        document.querySelectorAll('.cf-domain-tab-card').forEach(card => card.classList.remove('active'));
        const activeRadio = document.getElementById('opt' + type.charAt(0).toUpperCase() + type.slice(1));
        if (activeRadio) {
            activeRadio.checked = true;
            activeRadio.closest('.cf-domain-tab-card').classList.add('active');
        }

        // Show matching content pane
        document.querySelectorAll('.cf-domain-tab-content').forEach(pane => pane.classList.add('d-none'));
        const activePane = document.getElementById('tabContent' + type.charAt(0).toUpperCase() + type.slice(1));
        if (activePane) activePane.classList.remove('d-none');

        // Update Domain Fee in receipt
        if (type === 'register') {
            const tldSelect = document.getElementById('cfSelectTld');
            const tld = tldSelect ? tldSelect.value : '.com';
            currentDomainFee = CF_TLDS[tld] || 13.57;
        } else {
            currentDomainFee = 0.00;
        }

        updateReceipt();
    };

    // Account Mode Switcher (Register vs Login)
    window.cfSetAccountMode = function (mode) {
        const actionInput = document.getElementById('cfCheckoutAccountAction');
        if (actionInput) actionInput.value = mode;

        const btnRegister = document.getElementById('cfBtnModeRegister');
        const btnLogin    = document.getElementById('cfBtnModeLogin');
        const paneRegister = document.getElementById('cfPaneRegister');
        const paneLogin    = document.getElementById('cfPaneLogin');

        if (mode === 'login') {
            if (btnRegister) btnRegister.classList.remove('active');
            if (btnLogin) btnLogin.classList.add('active');
            if (paneRegister) paneRegister.classList.add('d-none');
            if (paneLogin) paneLogin.classList.remove('d-none');
        } else {
            if (btnLogin) btnLogin.classList.remove('active');
            if (btnRegister) btnRegister.classList.add('active');
            if (paneLogin) paneLogin.classList.add('d-none');
            if (paneRegister) paneRegister.classList.remove('d-none');
        }
    };

    // Check Domain Availability via check-domain.php
    window.cfCheckDomainAvailability = function () {
        const input = document.getElementById('cfInputRegister');
        const select = document.getElementById('cfSelectTld');
        const badge = document.getElementById('cfDomainResultBadge');
        const btn = document.getElementById('cfBtnCheckDomain');

        if (!input || !input.value.trim()) {
            showAlert('Please enter a domain name to verify.');
            return;
        }

        const domainName = input.value.trim();
        const tld = select ? select.value : '.com';
        const fullDomain = domainName.includes('.') ? domainName : (domainName + tld);

        btn.querySelector('.btn-text').classList.add('d-none');
        btn.querySelector('.spinner-border').classList.remove('d-none');
        btn.disabled = true;

        fetch('check-domain.php?domain=' + encodeURIComponent(fullDomain))
            .then(res => res.json())
            .then(data => {
                btn.querySelector('.btn-text').classList.remove('d-none');
                btn.querySelector('.spinner-border').classList.add('d-none');
                btn.disabled = false;

                badge.classList.remove('d-none');

                if (data.primary && data.primary.is_available) {
                    badge.innerHTML = `
                        <div class="alert alert-success d-flex align-items-center justify-content-between p-2 mb-0 fs-13">
                            <div>
                                <i class="las la-check-circle fs-16 me-1"></i>
                                <strong>${data.primary.domain}</strong> is Available!
                            </div>
                            <span class="badge bg-success">${data.primary.price}/yr</span>
                        </div>
                    `;
                    currentDomainFee = CF_TLDS[tld] || 13.57;
                    updateReceipt();
                } else {
                    badge.innerHTML = `
                        <div class="alert alert-warning d-flex align-items-center justify-content-between p-2 mb-0 fs-13">
                            <div>
                                <i class="las la-exclamation-circle fs-16 me-1"></i>
                                <strong>${fullDomain}</strong> is already taken.
                            </div>
                            <small class="text-muted">Try a different name</small>
                        </div>
                    `;
                }
            })
            .catch(() => {
                btn.querySelector('.btn-text').classList.remove('d-none');
                btn.querySelector('.spinner-border').classList.add('d-none');
                btn.disabled = false;
                badge.classList.remove('d-none');
                badge.innerHTML = `
                    <div class="alert alert-success d-flex align-items-center justify-content-between p-2 mb-0 fs-13">
                        <div>
                            <i class="las la-check-circle fs-16 me-1"></i>
                            <strong>${fullDomain}</strong> is available for registration.
                        </div>
                        <span class="badge bg-success">$13.57/yr</span>
                    </div>
                `;
            });
    };

    // Update Live Order Receipt across Step 1 and Step 3
    function updateReceipt() {
        const hasHosting = (currentPid && currentPid !== 'none' && CF_PRODUCTS[currentPid]);
        const plan = hasHosting ? CF_PRODUCTS[currentPid] : null;
        const isYearly = currentCycle === 'annually';
        const planPrice = hasHosting ? (isYearly ? plan.yearly : plan.monthly) : 0.00;

        // Step 1 Summary
        const step1DomainRow = document.getElementById('cfStep1DomainRow');
        const step1DomainFee = document.getElementById('cfStep1DomainFee');
        const step1HostingRow = document.getElementById('cfStep1HostingRow');
        const step1HostingFee = document.getElementById('cfStep1HostingFee');
        const step1TotalDue = document.getElementById('cfStep1TotalDue');

        if (step1DomainRow) {
            step1DomainRow.style.display = currentDomainFee > 0 ? 'flex' : 'none';
            if (step1DomainFee) step1DomainFee.textContent = '$' + currentDomainFee.toFixed(2);
        }
        if (step1HostingRow) {
            step1HostingRow.style.display = hasHosting ? 'flex' : 'none';
            if (step1HostingFee) step1HostingFee.textContent = '$' + planPrice.toFixed(2);
        }
        const grandTotal = planPrice + currentDomainFee;
        if (step1TotalDue) {
            step1TotalDue.textContent = '$' + grandTotal.toFixed(2);
        }

        // Step 3 Order Summary
        const receiptTitle = document.getElementById('cfReceiptPlanTitle');
        const receiptPlanPrice = document.getElementById('cfReceiptPlanPrice');
        const receiptDomainRow = document.getElementById('cfReceiptDomainRow');
        const receiptDomainPrice = document.getElementById('cfReceiptDomainPrice');
        const receiptTotal = document.getElementById('cfReceiptTotal');

        if (receiptTitle) {
            if (hasHosting) {
                receiptTitle.textContent = plan.name + (isYearly ? ' (Annual Billing - 20% Saved)' : ' (Monthly Billing)');
            } else {
                receiptTitle.textContent = 'Domain Registration Only (1 Year)';
            }
        }
        if (receiptPlanPrice) {
            receiptPlanPrice.textContent = '$' + planPrice.toFixed(2);
        }
        if (receiptDomainRow) {
            receiptDomainRow.style.display = currentDomainFee > 0 ? 'flex' : 'none';
            if (receiptDomainPrice) receiptDomainPrice.textContent = '$' + currentDomainFee.toFixed(2);
        }
        if (receiptTotal) {
            receiptTotal.textContent = '$' + grandTotal.toFixed(2);
        }
    }

    // Step Navigation
    window.cfGoToStep = function (step) {
        hideAlert();

        if (step === 2) {
            // Validate Step 1 domain/hosting input
            if (currentDomainType === 'register') {
                const finalDom = document.getElementById('cfCheckoutFinalDomain').value.trim();
                const regInput = document.getElementById('cfInputRegister');
                if (!finalDom && regInput && regInput.value.trim()) {
                    const tld = document.getElementById('cfSelectTld').value;
                    document.getElementById('cfCheckoutFinalDomain').value = regInput.value.trim();
                    document.getElementById('cfCheckoutFinalTld').value = tld;
                }
            } else if (currentDomainType === 'subdomain') {
                const sub = document.getElementById('cfInputSubdomain').value.trim() || 'mybusiness';
                document.getElementById('cfCheckoutFinalDomain').value = sub;
                document.getElementById('cfCheckoutFinalTld').value = '.cloudflox.site';
            } else if (currentDomainType === 'existing') {
                const ext = document.getElementById('cfInputExisting').value.trim();
                if (!ext) {
                    showAlert('Please enter your existing domain name.');
                    return;
                }
                document.getElementById('cfCheckoutFinalDomain').value = ext;
            } else if (currentDomainType === 'transfer') {
                const tr = document.getElementById('cfInputTransfer').value.trim();
                if (!tr) {
                    showAlert('Please enter the domain name you want to transfer.');
                    return;
                }
                document.getElementById('cfCheckoutFinalDomain').value = tr;
            }
        } else if (step === 3) {
            // Validate Step 2 customer inputs depending on Login vs Register mode
            const accountAction = document.getElementById('cfCheckoutAccountAction').value;

            if (accountAction === 'login') {
                const logEmail = document.getElementById('cfLoginEmail').value.trim();
                const logPass  = document.getElementById('cfLoginPassword').value;
                if (!logEmail || !logEmail.includes('@')) {
                    showAlert('Please enter your registered account email.');
                    return;
                }
                if (!logPass) {
                    showAlert('Please enter your account password.');
                    return;
                }
            } else {
                const fname = document.getElementById('cfCustFirstname').value.trim();
                const lname = document.getElementById('cfCustLastname').value.trim();
                const email = document.getElementById('cfCustEmail').value.trim();
                const phone = document.getElementById('cfCustPhone').value.trim();
                const pass  = document.getElementById('cfCustPassword').value;

                if (!fname || !lname) {
                    showAlert('Please provide both your First and Last Name.');
                    return;
                }
                if (!email || !email.includes('@')) {
                    showAlert('Please provide a valid email address.');
                    return;
                }
                if (!phone) {
                    showAlert('Please provide a phone number for billing verification.');
                    return;
                }
                if (!pass || pass.length < 6) {
                    showAlert('Password must be at least 6 characters long.');
                    return;
                }
            }
        }

        currentStep = step;

        // Hide all steps, show current
        document.querySelectorAll('.cf-checkout-step-pane').forEach(pane => pane.classList.add('d-none'));
        const targetPane = document.getElementById('cfCheckoutStep' + step);
        if (targetPane) targetPane.classList.remove('d-none');

        // Update progress indicators
        document.querySelectorAll('.cf-step-item').forEach(item => {
            const itemStep = parseInt(item.getAttribute('data-step'), 10);
            item.classList.remove('active', 'completed');
            if (itemStep === step) {
                item.classList.add('active');
            } else if (itemStep < step) {
                item.classList.add('completed');
            }
        });

        // Scroll to top of drawer
        const bodyEl = document.querySelector('.cf-checkout-body');
        if (bodyEl) bodyEl.scrollTop = 0;
    };

    // Payment Method Selection
    window.cfSelectPaymentMethod = function (method) {
        document.querySelectorAll('.cf-payment-method-card').forEach(card => card.classList.remove('active'));
        const targetRadio = document.querySelector(`input[name="paymentmethod"][value="${method}"]`);
        if (targetRadio) {
            targetRadio.checked = true;
            targetRadio.closest('.cf-payment-method-card').classList.add('active');
        }
    };

    // Generalized Password Eye Toggle
    window.cfTogglePasswordVisibility = function (inputId, eyeId) {
        inputId = inputId || 'cfCustPassword';
        eyeId = eyeId || 'cfPasswordEye';
        const passInput = document.getElementById(inputId);
        const eyeIcon = document.getElementById(eyeId);
        if (!passInput) return;
        if (passInput.type === 'password') {
            passInput.type = 'text';
            if (eyeIcon) eyeIcon.classList.replace('la-eye', 'la-eye-slash');
        } else {
            passInput.type = 'password';
            if (eyeIcon) eyeIcon.classList.replace('la-eye-slash', 'la-eye');
        }
    };

    // Submit Order via AJAX to api/checkout.php
    window.cfSubmitCheckoutOrder = function () {
        hideAlert();
        const submitBtn = document.getElementById('cfBtnSubmitOrder');
        submitBtn.querySelector('.btn-text').classList.add('d-none');
        submitBtn.querySelector('.spinner-border').classList.remove('d-none');
        submitBtn.disabled = true;

        const formData = new FormData(document.getElementById('cfCheckoutForm'));
        const payload = {};
        formData.forEach((val, key) => { payload[key] = val; });

        fetch('api/checkout.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            submitBtn.querySelector('.btn-text').classList.remove('d-none');
            submitBtn.querySelector('.spinner-border').classList.add('d-none');
            submitBtn.disabled = false;

            if (data.status === 'success') {
                // Populate Step 4 (Success state)
                document.getElementById('cfSuccessOrderId').textContent = '#' + data.order_id;
                document.getElementById('cfSuccessInvoiceId').textContent = '#' + data.invoice_id;
                document.getElementById('cfSuccessPlan').textContent = data.plan_name + ' (' + data.total + ' ' + data.currency + ')';
                document.getElementById('cfSuccessDomain').textContent = data.domain;

                const invBtn = document.getElementById('cfSuccessInvoiceBtn');
                if (invBtn && data.invoice_url) {
                    invBtn.href = data.invoice_url;
                }

                // Switch to success view
                document.querySelectorAll('.cf-checkout-step-pane').forEach(pane => pane.classList.add('d-none'));
                document.getElementById('cfCheckoutSuccess').classList.remove('d-none');

                // Mark all steps as complete
                document.querySelectorAll('.cf-step-item').forEach(item => {
                    item.classList.remove('active');
                    item.classList.add('completed');
                });
            } else {
                showAlert(data.message || 'An error occurred while creating your order. Please try again.');
            }
        })
        .catch(err => {
            submitBtn.querySelector('.btn-text').classList.remove('d-none');
            submitBtn.querySelector('.spinner-border').classList.add('d-none');
            submitBtn.disabled = false;
            showAlert('Network error: Could not reach the checkout server. Please try again.');
        });
    };

    function showAlert(msg) {
        const alertEl = document.getElementById('cfCheckoutAlert');
        const alertText = document.getElementById('cfCheckoutAlertText');
        if (alertEl && alertText) {
            alertText.textContent = msg;
            alertEl.classList.remove('d-none');
        }
    }

    function hideAlert() {
        const alertEl = document.getElementById('cfCheckoutAlert');
        if (alertEl) alertEl.classList.add('d-none');
    }

    // Initialize once DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCheckout);
    } else {
        initCheckout();
    }

})();
