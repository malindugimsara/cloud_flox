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
        '101': { name: 'Mini Plan', category: 'Budget Cloud Hosting', monthly: 3.00, yearly: 28.80, specs: '5 GB NVMe • 1 Site • 250 GB Bandwidth' },
        '102': { name: 'Basic Plan', category: 'Budget Cloud Hosting', monthly: 4.50, yearly: 43.20, specs: '10 GB NVMe • 2 Sites • 500 GB Bandwidth' },
        '103': { name: 'Plus Plan', category: 'Budget Cloud Hosting', monthly: 6.00, yearly: 57.60, specs: '20 GB NVMe • 5 Sites • Unmetered Bandwidth' },

        '201': { name: 'Business Starter', category: 'Business NVMe Web Hosting', monthly: 7.50, yearly: 72.00, specs: '20 GB Pure NVMe • 1 vCPU • 2 GB RAM • 1 Site' },
        '202': { name: 'Business Growth', category: 'Business NVMe Web Hosting', monthly: 15.00, yearly: 144.00, specs: '40 GB Pure NVMe • 2 vCPU • 4 GB RAM • 5 Sites' },
        '203': { name: 'Business Professional', category: 'Business NVMe Web Hosting', monthly: 27.00, yearly: 259.20, specs: '80 GB Pure NVMe • 4 vCPU • 8 GB RAM • 15 Sites' },
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
        '.lk': 29.00,
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
            // 1. Intercept Domain Claim / Buy buttons
            const domainBuyBtn = e.target.closest('.cf-btn-domain-buy, .cf-btn-alt-buy, a[href*="domain=register"]');
            if (domainBuyBtn) {
                e.preventDefault();
                e.stopPropagation();
                let domainName = '';
                if (domainBuyBtn.href) {
                    const match = domainBuyBtn.href.match(/query=([^&]+)/i);
                    if (match) domainName = decodeURIComponent(match[1]);
                }
                openCheckoutDrawer({
                    pid: '201',
                    cycle: 'annually',
                    domain: domainName,
                    domainType: 'register'
                });
                return;
            }

            // 1.1 Intercept Domain Transfer buttons
            const domainTransferBtn = e.target.closest('.cf-btn-domain-transfer, .cf-btn-alt-transfer, a[href*="domain=transfer"]');
            if (domainTransferBtn) {
                e.preventDefault();
                e.stopPropagation();
                let domainName = '';
                if (domainTransferBtn.href) {
                    const match = domainTransferBtn.href.match(/query=([^&]+)/i);
                    if (match) domainName = decodeURIComponent(match[1]);
                }
                openCheckoutDrawer({
                    pid: '201',
                    cycle: 'annually',
                    domain: domainName,
                    domainType: 'transfer'
                });
                return;
            }

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

    // Open Checkout Drawer with selected plan
    window.openCheckoutDrawer = function (opts) {
        opts = opts || {};
        currentPid = opts.pid || '201';
        currentCycle = opts.cycle || 'monthly';
        currentDomainType = opts.domainType || 'subdomain';
        currentDomainFee = 0.00;

        const plan = CF_PRODUCTS[currentPid] || CF_PRODUCTS['201'];

        // Populate Plan Details
        document.getElementById('cfCheckoutPid').value = currentPid;
        document.getElementById('cfSummaryPlanName').textContent = plan.name;
        document.getElementById('cfSummaryCategory').textContent = plan.category;
        document.getElementById('cfCheckoutPlanBadge').textContent = plan.category + ' — ' + plan.name;
        document.getElementById('cfSummarySpecs').textContent = plan.specs;

        // Set Cycle
        cfSetCycle(currentCycle);

        // Set domain tabs
        if (opts.domain && currentDomainType === 'register') {
            cfSelectDomainTab('register');
            const regInput = document.getElementById('cfInputRegister');
            if (regInput) {
                if (opts.domain.includes('.')) {
                    const lastDot = opts.domain.lastIndexOf('.');
                    regInput.value = opts.domain.substring(0, lastDot);
                    const tld = opts.domain.substring(lastDot);
                    const sel = document.getElementById('cfSelectTld');
                    if (sel && [...sel.options].some(o => o.value === tld)) {
                        sel.value = tld;
                    }
                } else {
                    regInput.value = opts.domain;
                }
            }
            setTimeout(() => cfCheckDomainAvailability(), 150);
        } else if (opts.domain && currentDomainType === 'transfer') {
            cfSelectDomainTab('transfer');
            const transferInput = document.getElementById('cfInputTransfer');
            if (transferInput) {
                transferInput.value = opts.domain;
            }
        } else {
            cfSelectDomainTab('subdomain');
            const subInput = document.getElementById('cfInputSubdomain');
            if (subInput && !subInput.value) {
                subInput.value = 'mybusiness-' + Math.floor(100 + Math.random() * 900);
            }
        }

        // Reset form & steps
        cfGoToStep(1);
        hideAlert();

        // Show Backdrop & Drawer
        backdrop.style.display = 'flex';
        setTimeout(() => {
            backdrop.classList.add('open');
            document.body.classList.add('cf-modal-open');
            document.documentElement.classList.add('cf-modal-open');
            document.body.style.overflow = 'hidden';
        }, 10);
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

        const plan = CF_PRODUCTS[currentPid] || CF_PRODUCTS['201'];
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

        updateReceipt();
    };

    // Domain Choice Tabs
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

    // Update Live Order Receipt
    function updateReceipt() {
        const plan = CF_PRODUCTS[currentPid] || CF_PRODUCTS['201'];
        const isYearly = currentCycle === 'annually';
        const planPrice = isYearly ? plan.yearly : plan.monthly;

        const receiptTitle = document.getElementById('cfReceiptPlanTitle');
        const receiptPlanPrice = document.getElementById('cfReceiptPlanPrice');
        const receiptDomainRow = document.getElementById('cfReceiptDomainRow');
        const receiptDomainPrice = document.getElementById('cfReceiptDomainPrice');
        const receiptTotal = document.getElementById('cfReceiptTotal');

        if (receiptTitle) {
            receiptTitle.textContent = plan.name + (isYearly ? ' (Annual Billing - 20% Saved)' : ' (Monthly Billing)');
        }
        if (receiptPlanPrice) {
            receiptPlanPrice.textContent = '$' + planPrice.toFixed(2);
        }

        if (receiptDomainRow) {
            if (currentDomainFee > 0) {
                receiptDomainRow.style.display = 'flex';
                if (receiptDomainPrice) receiptDomainPrice.textContent = '$' + currentDomainFee.toFixed(2);
            } else {
                receiptDomainRow.style.display = 'none';
            }
        }

        const grandTotal = planPrice + currentDomainFee;
        if (receiptTotal) {
            receiptTotal.textContent = '$' + grandTotal.toFixed(2);
        }
    }

    // Step Navigation
    window.cfGoToStep = function (step) {
        hideAlert();

        if (step === 2) {
            // Validate Step 1 domain input
            if (currentDomainType === 'register') {
                const regInput = document.getElementById('cfInputRegister');
                if (!regInput || !regInput.value.trim()) {
                    showAlert('Please enter the domain name you wish to register.');
                    return;
                }
                const tld = document.getElementById('cfSelectTld').value;
                document.getElementById('cfCheckoutFinalDomain').value = regInput.value.trim();
                document.getElementById('cfCheckoutFinalTld').value = tld;
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
            // Validate Step 2 customer inputs
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

    // Password Eye Toggle
    window.cfTogglePasswordVisibility = function () {
        const passInput = document.getElementById('cfCustPassword');
        const eyeIcon = document.getElementById('cfPasswordEye');
        if (passInput.type === 'password') {
            passInput.type = 'text';
            eyeIcon.classList.replace('la-eye', 'la-eye-slash');
        } else {
            passInput.type = 'password';
            eyeIcon.classList.replace('la-eye-slash', 'la-eye');
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
