/**
 * CloudFlox - Homepage Interactive Features & Dynamic Logic
 * -------------------------------------------------------------
 * 1. Hosting Category & Pricing Switchers (Budget, Business, Premium)
 * 2. Monthly / Annual Billing Toggle with WHMCS Cart URL Mapping
 * 3. Domain TLD Quick-Select Helper
 * 4. Comparison Table Category Switching & Billing Toggle
 * 5. Interactive Customer-Fit Matchmaker Wizard
 * 6. Custom Infrastructure Quote AJAX Submission & SLA Confirmation
 */

// =============================================================================
// 1. HOSTING PRICING CATEGORY SWITCHER (Budget, Business, Premium)
// =============================================================================
function switchPricingCategory(categoryKey, btnElement) {
    const tabs = document.querySelectorAll(".category-tab-btn");
    tabs.forEach(function (t) {
        t.classList.remove("active");
        t.setAttribute("aria-selected", "false");
    });

    const activeTab = document.getElementById("tab-" + categoryKey);
    if (activeTab) {
        activeTab.classList.add("active");
        activeTab.setAttribute("aria-selected", "true");
    }

    const panes = document.querySelectorAll(".pricing-category-pane");
    panes.forEach(function (p) {
        p.classList.remove("active");
        p.style.display = "none";
    });

    const targetPane = document.getElementById("categoryPane-" + categoryKey);
    if (targetPane) {
        targetPane.style.display = "flex";
        setTimeout(function () {
            targetPane.classList.add("active");
        }, 20);
    }
}

// =============================================================================
// 2. MONTHLY / ANNUAL BILLING SWITCHER WITH WHMCS CART MAPPING
// =============================================================================
function togglePricing(isYearly) {
    const priceVals = document.querySelectorAll(".price-val");
    const priceCycles = document.querySelectorAll(".price-cycle");
    const priceSubtexts = document.querySelectorAll(".price-subtext");
    const btnMonthly = document.getElementById("btnMonthly");
    const btnYearly = document.getElementById("btnYearly");
    const pricingSwitch = document.getElementById("pricingSwitch");
    const planCtaBtns = document.querySelectorAll(".plan-cta-btn");

    if (pricingSwitch) {
        pricingSwitch.checked = isYearly;
    }

    if (btnMonthly && btnYearly) {
        if (isYearly) {
            btnMonthly.classList.remove("active");
            btnYearly.classList.add("active");
        } else {
            btnMonthly.classList.add("active");
            btnYearly.classList.remove("active");
        }
    }

    priceVals.forEach(function (el) {
        el.style.opacity = '0';
        setTimeout(function () {
            el.textContent = isYearly ? el.getAttribute("data-yearly") : el.getAttribute("data-monthly");
            el.style.opacity = '1';
        }, 120);
    });

    priceCycles.forEach(function (el) {
        el.textContent = isYearly ? el.getAttribute("data-yearly") : el.getAttribute("data-monthly");
    });

    priceSubtexts.forEach(function (el) {
        el.textContent = isYearly ? el.getAttribute("data-yearly") : el.getAttribute("data-monthly");
    });

    // Update WHMCS URLs with appropriate billing cycle
    const cycleParam = isYearly ? "annually" : "monthly";
    planCtaBtns.forEach(function (btn) {
        const pid = btn.getAttribute("data-pid");
        if (pid) {
            btn.setAttribute("href", "https://my.cloudflox.com/cart.php?a=add&pid=" + pid + "&billingcycle=" + cycleParam);
        }
    });
}

function setBillingToggle(isYearly) {
    togglePricing(isYearly);
}

// =============================================================================
// 3. DOMAIN TLD QUICK-SELECT HELPER
// =============================================================================
function setDomainExt(ext, el) {
    const input = document.getElementById("domainInput");
    if (!input) return;

    let val = input.value.trim();
    if (val === '') {
        input.value = "mybrand" + ext;
    } else {
        val = val.replace(/\.[a-zA-Z]{2,}$/, "");
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

// =============================================================================
// 4. COMPARISON TABLE CATEGORY SWITCHING & BILLING TOGGLE
// =============================================================================
function switchComparisonCategory(catKey, btn) {
    // Update active button state
    const allBtns = document.querySelectorAll('.cf-comp-controls-bar .cf-tier-btn');
    allBtns.forEach(b => b.classList.remove('active'));
    if (btn) btn.classList.add('active');

    // Toggle panes
    const panes = ['business', 'budget', 'premium', 'all'];
    panes.forEach(p => {
        const paneEl = document.getElementById('compPane-' + p);
        if (paneEl) {
            if (p === catKey) {
                paneEl.style.display = 'block';
                paneEl.style.opacity = '0';
                setTimeout(() => { paneEl.style.opacity = '1'; }, 20);
            } else {
                paneEl.style.display = 'none';
            }
        }
    });
}

function scrollMatrixTable(direction) {
    const tableResp = document.querySelector('#compPane-all .table-responsive');
    if (!tableResp) return;
    const scrollAmount = 350;
    tableResp.scrollBy({
        left: direction === 'left' ? -scrollAmount : scrollAmount,
        behavior: 'smooth'
    });
}

// Comparison Table Billing Listener
document.addEventListener('DOMContentLoaded', function () {
    const billingToggle = document.getElementById('cfCompBillingToggle');
    const lblMonthly = document.getElementById('cfCompLblMonthly');
    const lblYearly = document.getElementById('cfCompLblYearly');

    if (billingToggle) {
        billingToggle.addEventListener('change', function () {
            const isYearly = this.checked;
            const cycleParam = isYearly ? 'annually' : 'monthly';

            if (lblMonthly) lblMonthly.classList.toggle('active', !isYearly);
            if (lblYearly) lblYearly.classList.toggle('active', isYearly);

            // Update prices across all comparison tables
            document.querySelectorAll('.cf-comp-price-val').forEach(el => {
                const price = isYearly ? el.getAttribute('data-yearly') : el.getAttribute('data-monthly');
                if (price) el.textContent = price;
            });

            // Update sub-notes
            document.querySelectorAll('.cf-comp-billing-note').forEach(el => {
                const note = isYearly ? el.getAttribute('data-yearly-note') : el.getAttribute('data-monthly-note');
                if (note) el.textContent = note;
            });

            // Update WHMCS buttons
            document.querySelectorAll('.cf-comparison-section .cf-table-cta-btn').forEach(btn => {
                const pid = btn.getAttribute('data-pid');
                if (pid) {
                    btn.setAttribute('href', 'https://my.cloudflox.com/cart.php?a=add&pid=' + pid + '&billingcycle=' + cycleParam);
                }
            });
        });
    }
});

// =============================================================================
// 5. INTERACTIVE CUSTOMER-FIT MATCHMAKER WIZARD
// =============================================================================
const matchmakerData = {
    budget: {
        key: 'budget',
        name: 'Budget Hosting',
        badgeText: 'Personal & Starter',
        badgeClass: 'tag-green',
        price: '$3.00',
        specs: [
            '5 &ndash; 20 GB Pure NVMe Storage',
            '1 &ndash; 1.5 Cores CloudLinux LVE Isolation',
            'cPanel + Free AutoSSL + 1-Click WordPress',
            'LiteSpeed Web Server + LSCache'
        ],
        btnLabel: 'View Budget Plans',
        actionType: 'link',
        actionUrl: 'budget-hosting',
        reasons: {
            title1: 'Perfect For Personal Projects & Starters',
            text1: 'Ideal for portfolios, blogs, student showcases, and hobby websites where simplicity and cost-effectiveness matter most.',
            title2: 'Built for Starter Traffic (< 10k visits)',
            text2: 'Optimized for up to 10,000 monthly visitors. LiteSpeed server caching delivers lightning-fast page loading with minimal resource consumption.',
            title3: '100% Hassle-Free Maintenance',
            text3: 'Zero server administration needed. Intuitive cPanel, automatic SSL encryption, and automated updates keep your website secure without effort.',
            title4: 'Exceptional Starter Value ($3.00/mo)',
            text4: 'Get pure enterprise NVMe storage and isolated CloudLinux resources at an accessible entry price, without overpaying for enterprise features.'
        }
    },
    business: {
        key: 'business',
        name: 'Business Web Hosting',
        badgeText: 'Businesses & Corporate',
        badgeClass: 'tag-orange',
        price: '$7.50',
        specs: [
            '15 &ndash; 150 GB Enterprise NVMe Gen4',
            '1 &ndash; 4 Cores &amp; 2 &ndash; 8 GB RAM',
            'Automated Daily JetBackup (30-day restore)',
            'Official cPanel + Priority 24/7 Support'
        ],
        btnLabel: 'View Business Plans',
        actionType: 'link',
        actionUrl: 'web-hosting',
        reasons: {
            title1: 'Tailored for SMEs & Business Sites',
            text1: 'Engineered specifically for corporate websites, SMEs, and digital agencies that require steady uptime, professional webmail, and zero maintenance headaches.',
            title2: 'Handles 10,000 to 100,000 Monthly Visits',
            text2: 'Easily accommodates growing business traffic. Dedicated CloudLinux LVE limits guarantee your CPU and RAM are never throttled by neighboring accounts.',
            title3: 'Automated Daily JetBackup (30-Day History)',
            text3: 'Every day your databases, emails, and files are backed up automatically off-site, giving you 1-click historical rollbacks and enterprise peace of mind.',
            title4: 'Highest ROI for Commercial Sites ($7.50/mo)',
            text4: 'Provides the optimal commercial balance &mdash; giving your business high-availability cloud infrastructure and 24/7 priority support at just $7.50/mo.'
        }
    },
    premium: {
        key: 'premium',
        name: 'Premium Managed Hosting',
        badgeText: 'Demanding Apps & Stores',
        badgeClass: 'tag-blue',
        price: '$75.00',
        specs: [
            '4 &ndash; 16 vCPU Isolated High-Clock Nodes',
            '16 &ndash; 128 GB Dedicated Memory',
            'Dedicated In-Memory Redis Cache Instance',
            '&lt; 15-Minute Response SLA SysAdmin Support'
        ],
        btnLabel: 'View Premium Plans',
        actionType: 'link',
        actionUrl: 'premium-hosting',
        reasons: {
            title1: 'Engineered for High-Transaction E-Commerce & Apps',
            text1: 'Built for high-volume WooCommerce, SaaS platforms, database-heavy LMS, and busy media portals that cannot afford sub-second checkout delays.',
            title2: 'Conquers Traffic Surges (100k &ndash; 500k+ Visits)',
            text2: 'Absorbs promotional campaigns, flash sales, and heavy checkout spikes without slowdowns, thanks to isolated high-clock vCPU nodes.',
            title3: 'Dedicated Redis Object Caching',
            text3: 'Sub-second database queries powered by a private Redis memory instance, paired with a guaranteed < 15-minute response SLA from senior sysadmins.',
            title4: 'Managed Alternative to Costly Dedicated Servers',
            text4: 'Delivers raw multi-core compute power and white-glove server management at $75.00/mo without the maintenance burden of bare-metal.'
        }
    },
    vps: {
        key: 'vps',
        name: 'KVM Cloud VPS',
        badgeText: 'Root Access & Developers',
        badgeClass: 'tag-purple',
        price: '$7.99',
        specs: [
            '100% Root Access &amp; SSH Terminal',
            'Hardware KVM Virtualization Architecture',
            'Instant OS Reloads (Ubuntu, Debian, AlmaLinux)',
            'Dedicated Clean Static IPv4 Address'
        ],
        btnLabel: 'Explore Cloud VPS',
        actionType: 'link',
        actionUrl: 'vps-hosting',
        reasons: {
            title1: '100% Administrative Freedom & Custom Stacks',
            text1: 'Tailored for developers, DevOps teams, and custom web applications requiring custom packages, Docker containers, Python/Node.js, or Go.',
            title2: 'Dedicated Compute & API Workloads',
            text2: 'Guaranteed KVM virtual cores and dedicated RAM with high IOPS NVMe and dedicated clean static IPv4 for continuous background microservices.',
            title3: 'Complete OS & Firewall Control',
            text3: 'Total control over your operating system, custom firewall iptables, port forwarding, and background cron daemons with zero hosting restrictions.',
            title4: 'On-Demand Scalability Starting at $7.99/mo',
            text4: 'Scale up your CPU, memory, and storage instantly as your platform expands, paying only for the exact resources your code utilizes.'
        }
    },
    dedicated: {
        key: 'dedicated',
        name: 'Bare-Metal Dedicated Servers',
        badgeText: 'Dedicated Capacity',
        badgeClass: 'tag-dark',
        price: '$89.00',
        specs: [
            'AMD EPYC &amp; Intel Xeon Physical Hardware',
            'Zero Noisy Neighbors (100% Private Hardware)',
            'Enterprise Hardware RAID-10 NVMe Storage',
            'Gigabit / 10G Unmetered Network Uplink'
        ],
        btnLabel: 'View Dedicated Servers',
        actionType: 'link',
        actionUrl: 'dedicated-server',
        reasons: {
            title1: 'Single-Tenant Physical Hardware Sovereignty',
            text1: 'Pure bare-metal physical performance designed for enterprise corporations, large database clusters, and strict data compliance needs.',
            title2: 'Unrivaled Throughput & Continuous I/O',
            text2: 'Zero virtualization layer overhead. 100% of the raw physical CPU clock cycles and multi-gigabit bandwidth belong exclusively to your application.',
            title3: 'Hardware RAID-10 & Complete Isolation',
            text3: 'Enterprise hardware RAID-10 array with automated drive failure failover and zero noisy neighbors sharing your hardware or bandwidth pipe.',
            title4: 'Predictable Enterprise Power ($89.00/mo)',
            text4: 'Full hardware ownership and unmetered gigabit uplink starting at $89.00/mo, providing the lowest cost-per-core for massive workloads.'
        }
    }
};

let currentMatchmakerState = {
    step: 1,
    project: 'business',
    traffic: 'moderate',
    control: 'cpanel_backup',
    recommendedKey: 'business'
};

function selectMatchmakerOption(category, value, cardElement) {
    currentMatchmakerState[category] = value;
    
    // Highlight selected card in the current panel
    const panel = cardElement.closest('.wizard-step-panel');
    if (panel) {
        panel.querySelectorAll('.wizard-option-card').forEach(c => c.classList.remove('selected'));
        cardElement.classList.add('selected');
    }

    // Auto-advance to next step with smooth transition
    if (category === 'project') {
        setTimeout(() => goToMatchmakerStep(2), 220);
    } else if (category === 'traffic') {
        setTimeout(() => goToMatchmakerStep(3), 220);
    } else if (category === 'control') {
        setTimeout(() => calculateAndShowMatch(), 220);
    }
}

function goToMatchmakerStep(stepNum) {
    currentMatchmakerState.step = stepNum;

    // Hide all panels, show target
    document.querySelectorAll('.wizard-step-panel').forEach(p => p.style.display = 'none');
    const targetPanel = document.getElementById('step-panel-' + stepNum);
    if (targetPanel) {
        targetPanel.style.display = 'block';
    }

    // Hide result panel if visible
    const resultPanel = document.getElementById('matchmaker-result-panel');
    if (resultPanel) resultPanel.style.display = 'none';

    // Update Progress Bar
    const progressLine = document.getElementById('wizard-progress-line');
    if (progressLine) {
        if (stepNum === 1) progressLine.style.width = '0%';
        if (stepNum === 2) progressLine.style.width = '50%';
        if (stepNum === 3) progressLine.style.width = '100%';
    }

    // Update Step Nodes
    for (let i = 1; i <= 3; i++) {
        const node = document.getElementById('step-node-' + i);
        if (node) {
            node.classList.remove('active', 'completed');
            if (i === stepNum) {
                node.classList.add('active');
            } else if (i < stepNum) {
                node.classList.add('completed');
            }
        }
    }
}

function jumpToMatchmakerStep(stepNum) {
    goToMatchmakerStep(stepNum);
}

function calculateAndShowMatch() {
    const { project, traffic, control } = currentMatchmakerState;
    let recommendedKey = 'business'; // default

    // Decision Matrix Algorithm
    if (project === 'dedicated' || traffic === 'massive' || control === 'bare_metal') {
        recommendedKey = 'dedicated';
    } else if (project === 'vps' || control === 'root_ssh' || traffic === 'api_traffic') {
        recommendedKey = 'vps';
    } else if (project === 'ecommerce' || traffic === 'heavy' || control === 'cpanel_redis') {
        recommendedKey = 'premium';
    } else if (project === 'personal' && traffic === 'starter' && control === 'cpanel_basic') {
        recommendedKey = 'budget';
    } else {
        recommendedKey = 'business';
    }

    currentMatchmakerState.recommendedKey = recommendedKey;
    renderMatchmakerResult(recommendedKey);
}

function renderMatchmakerResult(key) {
    const plan = matchmakerData[key] || matchmakerData.business;

    // Update Left Card
    const badgeEl = document.getElementById('res-plan-badge');
    if (badgeEl) {
        badgeEl.textContent = plan.badgeText;
        badgeEl.className = 'result-plan-tag ' + plan.badgeClass;
    }

    const nameEl = document.getElementById('res-plan-name');
    if (nameEl) nameEl.textContent = plan.name;

    const priceEl = document.getElementById('res-plan-price');
    if (priceEl) priceEl.textContent = plan.price;

    const specsListEl = document.getElementById('res-specs-list');
    if (specsListEl && plan.specs) {
        specsListEl.innerHTML = plan.specs.map(spec => `<li><i class="las la-check-circle"></i> <span>${spec}</span></li>`).join('');
    }

    const btnLabelEl = document.getElementById('res-btn-label');
    if (btnLabelEl) btnLabelEl.textContent = plan.btnLabel;

    const primaryBtnEl = document.getElementById('res-primary-btn');
    if (primaryBtnEl && plan.actionUrl) {
        primaryBtnEl.setAttribute('href', plan.actionUrl);
    }

    // Update Right Why-Box
    document.getElementById('why-title-1').textContent = plan.reasons.title1;
    document.getElementById('why-text-1').textContent = plan.reasons.text1;

    document.getElementById('why-title-2').textContent = plan.reasons.title2;
    document.getElementById('why-text-2').textContent = plan.reasons.text2;

    document.getElementById('why-title-3').textContent = plan.reasons.title3;
    document.getElementById('why-text-3').textContent = plan.reasons.text3;

    document.getElementById('why-title-4').textContent = plan.reasons.title4;
    document.getElementById('why-text-4').textContent = plan.reasons.text4;

    // Update Crumb badges
    const projectNames = {
        personal: 'Personal / Portfolio',
        business: 'Growing Business',
        ecommerce: 'Demanding App / Store',
        vps: 'Developer / APIs',
        dedicated: 'Dedicated Enterprise'
    };
    const trafficNames = {
        starter: '< 10k /mo',
        moderate: '10k - 100k /mo',
        heavy: '100k - 500k+ /mo',
        api_traffic: 'APIs & Microservices',
        massive: 'Massive Multi-TB'
    };
    const controlNames = {
        cpanel_basic: 'Hassle-Free cPanel',
        cpanel_backup: 'Daily JetBackup',
        cpanel_redis: 'Isolated + Redis',
        root_ssh: '100% Root SSH',
        bare_metal: 'Bare-Metal Hardware'
    };

    const crumbProj = document.getElementById('crumb-project-text');
    if (crumbProj) crumbProj.textContent = projectNames[currentMatchmakerState.project] || currentMatchmakerState.project;

    const crumbTraf = document.getElementById('crumb-traffic-text');
    if (crumbTraf) crumbTraf.textContent = trafficNames[currentMatchmakerState.traffic] || currentMatchmakerState.traffic;

    const crumbCtrl = document.getElementById('crumb-control-text');
    if (crumbCtrl) crumbCtrl.textContent = controlNames[currentMatchmakerState.control] || currentMatchmakerState.control;

    // Hide step panels and show result panel
    document.querySelectorAll('.wizard-step-panel').forEach(p => p.style.display = 'none');
    const resultPanel = document.getElementById('matchmaker-result-panel');
    if (resultPanel) {
        resultPanel.style.display = 'block';
    }

    // Update Stepper to 100%
    const progressLine = document.getElementById('wizard-progress-line');
    if (progressLine) progressLine.style.width = '100%';
    for (let i = 1; i <= 3; i++) {
        const node = document.getElementById('step-node-' + i);
        if (node) {
            node.classList.remove('active');
            node.classList.add('completed');
        }
    }
}

function handleMatchmakerCta() {
    const plan = matchmakerData[currentMatchmakerState.recommendedKey] || matchmakerData.business;
    if (plan.actionUrl) {
        window.location.href = plan.actionUrl;
    } else if (plan.actionType === 'tab') {
        const pricingSection = document.getElementById('pricing');
        if (pricingSection) {
            pricingSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
        setTimeout(() => {
            const targetTab = document.getElementById('tab-' + plan.key);
            if (targetTab) {
                targetTab.click();
            } else if (typeof switchPricingCategory === 'function') {
                switchPricingCategory(plan.key, targetTab);
            }
        }, 350);
    }
}

function restartMatchmaker() {
    goToMatchmakerStep(1);
}

function applyMatchmakerPreset(presetKey) {
    if (presetKey === 'personal') {
        currentMatchmakerState.project = 'personal';
        currentMatchmakerState.traffic = 'starter';
        currentMatchmakerState.control = 'cpanel_basic';
    } else if (presetKey === 'business') {
        currentMatchmakerState.project = 'business';
        currentMatchmakerState.traffic = 'moderate';
        currentMatchmakerState.control = 'cpanel_backup';
    } else if (presetKey === 'ecommerce') {
        currentMatchmakerState.project = 'ecommerce';
        currentMatchmakerState.traffic = 'heavy';
        currentMatchmakerState.control = 'cpanel_redis';
    } else if (presetKey === 'vps') {
        currentMatchmakerState.project = 'vps';
        currentMatchmakerState.traffic = 'api_traffic';
        currentMatchmakerState.control = 'root_ssh';
    } else if (presetKey === 'dedicated') {
        currentMatchmakerState.project = 'dedicated';
        currentMatchmakerState.traffic = 'massive';
        currentMatchmakerState.control = 'bare_metal';
    }

    calculateAndShowMatch();

    // Smooth scroll to card
    const card = document.getElementById('matchmaker-card');
    if (card) {
        card.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
}

// =============================================================================
// GLOBAL TOAST NOTIFICATION SYSTEM
// =============================================================================
function displayToast(heading, text, icon, loaderBg) {
    if (window.jQuery && typeof jQuery.toast === 'function') {
        jQuery.toast({
            heading: heading || 'Notification',
            text: text || '',
            position: 'bottom-right',
            loaderBg: loaderBg || (icon === 'success' ? '#10b981' : '#ef4444'),
            icon: icon || 'info',
            hideAfter: 8000,
            stack: 4
        });
        return;
    }

    // Modern glassmorphism toast fallback
    let container = document.getElementById('cf-toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'cf-toast-container';
        container.className = 'cf-toast-container';
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    const toastType = (icon === 'success') ? 'success' : ((icon === 'warning') ? 'warning' : ((icon === 'info') ? 'info' : 'error'));
    toast.className = `cf-toast cf-toast-${toastType}`;

    let iconClass = 'la-exclamation-circle';
    if (toastType === 'success') iconClass = 'la-check-circle';
    else if (toastType === 'warning') iconClass = 'la-exclamation-triangle';
    else if (toastType === 'info') iconClass = 'la-info-circle';

    toast.innerHTML = `
        <div class="cf-toast-icon-wrap">
            <i class="las ${iconClass}"></i>
        </div>
        <div class="cf-toast-body">
            <div class="cf-toast-heading">${heading || (toastType === 'success' ? 'Success' : 'Notice')}</div>
            <div class="cf-toast-text">${text || ''}</div>
        </div>
        <button type="button" class="cf-toast-close" aria-label="Close">&times;</button>
        <div class="cf-toast-progress-bar"></div>
    `;

    container.appendChild(toast);

    const closeBtn = toast.querySelector('.cf-toast-close');
    const removeToast = () => {
        if (toast.classList.contains('cf-toast-closing')) return;
        toast.classList.add('cf-toast-closing');
        setTimeout(() => {
            if (toast.parentNode) toast.parentNode.removeChild(toast);
        }, 320);
    };

    if (closeBtn) closeBtn.addEventListener('click', removeToast);

    // Auto dismiss after 7 seconds
    setTimeout(removeToast, 7000);
}

// =============================================================================
// 6. CUSTOM INFRASTRUCTURE QUOTE AJAX SUBMISSION & SLA CONFIRMATION
// =============================================================================
function submitCustomQuote(event) {
    event.preventDefault();

    const name = document.getElementById('cq_name') ? document.getElementById('cq_name').value.trim() : '';
    const email = document.getElementById('cq_email') ? document.getElementById('cq_email').value.trim() : '';
    const service = document.getElementById('cq_service') ? document.getElementById('cq_service').value : '';
    const budget = document.getElementById('cq_budget') ? document.getElementById('cq_budget').value : '';
    const requirements = document.getElementById('cq_requirements') ? document.getElementById('cq_requirements').value.trim() : '';
    const hp = document.getElementById('cq_hp') ? document.getElementById('cq_hp').value : '';

    if (!name || !email || !service || !budget || !requirements) {
        displayToast('Validation Error', 'Please complete all required fields before submitting your quote request.', 'error', '#c46a69');
        return;
    }

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        displayToast('Invalid Email', 'Please enter a valid business email address (e.g. name@company.com).', 'warning', '#f59e0b');
        return;
    }

    const submitBtn = document.getElementById('cq_submit_btn');
    const loadingBtn = document.getElementById('cq_loading_btn');

    if (submitBtn) submitBtn.classList.add('d-none');
    if (loadingBtn) loadingBtn.classList.remove('d-none');

    const formData = new FormData();
    formData.append('name', name);
    formData.append('email', email);
    formData.append('service', service);
    formData.append('budget', budget);
    formData.append('requirements', requirements);
    formData.append('cf_hp_field', hp);

    // Dynamic root prefix resolution so fetch works across localhost, subfolders, and root domain
    let targetUrl = 'custom-quote-send.php';
    const scriptEl = document.querySelector('script[src*="cloudflox-home.js"]');
    if (scriptEl) {
        const scriptSrc = scriptEl.getAttribute('src');
        const rootPrefix = scriptSrc.replace(/assets\/js\/cloudflox-home\.js.*$/, '');
        targetUrl = (rootPrefix ? rootPrefix : '') + 'custom-quote-send.php';
    }

    fetch(targetUrl, {
        method: 'POST',
        body: formData
    })
    .then(async res => {
        let data;
        try {
            data = await res.json();
        } catch (jsonErr) {
            throw new Error('Server returned an invalid response (HTTP ' + res.status + '). Please try again later.');
        }

        if (!res.ok && (!data || !data.message)) {
            throw new Error(`Server returned HTTP ${res.status}`);
        }
        return data;
    })
    .then(data => {
        if (data && data.status === 'success') {
            document.getElementById('cq_res_name').textContent = data.name || name;
            document.getElementById('cq_res_email').textContent = data.email || email;
            document.getElementById('cq_res_ref').textContent = data.ref || 'CFX-REQ-PENDING';

            document.getElementById('customQuoteForm').style.display = 'none';
            document.getElementById('cq_success_card').style.display = 'block';

            displayToast('Quote Request Received', 'Your inquiry has been successfully delivered to our Solutions Architecture team.', 'success', '#10b981');
        } else {
            const errorMsg = (data && data.message) ? data.message : 'Unable to submit your quote inquiry at this time. Please try again.';
            displayToast('Submission Failed', errorMsg, 'error', '#c46a69');
            if (submitBtn) submitBtn.classList.remove('d-none');
            if (loadingBtn) loadingBtn.classList.add('d-none');
        }
    })
    .catch(err => {
        console.error('Custom quote submission error:', err);
        const failMessage = err.message || 'Network connection issue. Please check your internet connection and try again.';
        displayToast('Submission Failed', failMessage, 'error', '#c46a69');
        if (submitBtn) submitBtn.classList.remove('d-none');
        if (loadingBtn) loadingBtn.classList.add('d-none');
    });
}

function resetCustomQuoteForm() {
    document.getElementById('customQuoteForm').reset();
    document.getElementById('customQuoteForm').style.display = 'block';
    document.getElementById('cq_success_card').style.display = 'none';

    const submitBtn = document.getElementById('cq_submit_btn');
    const loadingBtn = document.getElementById('cq_loading_btn');
    if (submitBtn) submitBtn.classList.remove('d-none');
    if (loadingBtn) loadingBtn.classList.add('d-none');
}

// =============================================================================
// 7. CLIENT TESTIMONIALS & REVIEWS SWIPER SLIDER (Slower, Smooth Transitions)
// =============================================================================
document.addEventListener("DOMContentLoaded", function () {
    if (document.querySelector(".reviewSwiper")) {
        new Swiper(".reviewSwiper", {
            slidesPerView: 1,
            spaceBetween: 24,
            loop: true,
            speed: 1100, // Slower, relaxed transition (slowed down from 600ms)
            autoplay: {
                delay: 7500, // 7.5s comfortable reading time before transitioning
                disableOnInteraction: false,
                pauseOnMouseEnter: true
            },
            pagination: {
                el: ".review-swiper-pagination",
                clickable: true
            },
            navigation: {
                nextEl: ".review-next-btn",
                prevEl: ".review-prev-btn"
            }
        });
    }

    // Initialize Scroll Parallax for Testimonial Visual Card
    initReviewParallax();
});

// =============================================================================
// 8. TESTIMONIAL VISUAL CARD SCROLL PARALLAX EFFECT
// =============================================================================
function initReviewParallax() {
    const parallaxImg = document.getElementById("reviewParallaxImg");
    const visualCard = document.querySelector(".review-visual-card");
    if (!parallaxImg || !visualCard) return;

    let ticking = false;

    function updateParallax() {
        const rect = visualCard.getBoundingClientRect();
        const windowHeight = window.innerHeight || document.documentElement.clientHeight;

        // Active only when card is visible in or around the viewport
        if (rect.top <= windowHeight && rect.bottom >= 0) {
            // progress: 0 when card top enters bottom of window, 1 when card bottom leaves top of window
            const progress = (windowHeight - rect.top) / (windowHeight + rect.height);
            // Smoothly move the image along the Y axis (-45px to +45px)
            const translateY = (progress - 0.5) * 90;
            parallaxImg.style.transform = `translate3d(0, ${translateY.toFixed(1)}px, 0) scale(1.1)`;
        }
        ticking = false;
    }

    window.addEventListener("scroll", function () {
        if (!ticking) {
            window.requestAnimationFrame(updateParallax);
            ticking = true;
        }
    }, { passive: true });

    // Initial calculation on load
    updateParallax();
}

// =============================================================================
// 9. FAQ CATEGORY FILTER
// =============================================================================
function filterFaq(category, btnElement) {
    const buttons = document.querySelectorAll(".faq-cat-btn");
    buttons.forEach(btn => btn.classList.remove("active"));
    if (btnElement) btnElement.classList.add("active");

    const items = document.querySelectorAll(".faq-modern-item");
    items.forEach(item => {
        const itemCat = item.getAttribute("data-category");
        if (category === "all" || itemCat === category) {
            item.style.display = "block";
            item.style.opacity = "0";
            setTimeout(() => {
                item.style.transition = "opacity 0.25s ease";
                item.style.opacity = "1";
            }, 10);
        } else {
            item.style.display = "none";
        }
    });
}


