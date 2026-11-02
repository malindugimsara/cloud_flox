/**
 * CloudFlox Inline Domain Availability Checker & Multi-Domain Cart System
 * -------------------------------------------------------------------------
 * Supports:
 * 1. Single Instant Domain Search with alternative TLD suggestions
 * 2. Multi-Domain Cart management (persistent across pages via localStorage)
 * 3. Bulk Domain Search (up to 20 domains concurrent verification)
 * 4. Floating sticky cart drawer with 1-click continuation to checkout
 */

// Global CloudFlox Multi-Domain Cart
window.cfDomainCart = {
    items: [],
    storageKey: 'cf_domain_cart',
    hiddenKey: 'cf_cart_bar_hidden',
    isCollapsed: false,
    isBarHidden: false,
    _toastTimer: null,

    init: function() {
        try {
            const raw = localStorage.getItem(this.storageKey);
            if (raw) {
                const parsed = JSON.parse(raw);
                if (Array.isArray(parsed)) {
                    this.items = parsed.filter(it => it && it.domain);
                }
            }
        } catch (e) {
            this.items = [];
        }

        try {
            this.isBarHidden = localStorage.getItem(this.hiddenKey) === '1';
        } catch (e) {
            this.isBarHidden = false;
        }

        this.renderCartBar();
        this.renderDrawer();
        this.updateHeaderBadges();
        this.syncButtons();
    },

    save: function() {
        try {
            localStorage.setItem(this.storageKey, JSON.stringify(this.items));
        } catch (e) {}
        this.renderCartBar();
        this.renderDrawer();
        this.updateHeaderBadges();
        this.syncButtons();
    },

    has: function(domain) {
        if (!domain) return false;
        const clean = domain.toLowerCase().trim();
        return this.items.some(it => it.domain.toLowerCase() === clean);
    },

    add: function(domain, price, rawPrice, type) {
        if (!domain) return;
        const clean = domain.toLowerCase().trim();
        if (this.has(clean)) return;

        let numPrice = parseFloat(rawPrice);
        if (isNaN(numPrice) || numPrice <= 0) {
            numPrice = parseFloat(String(price).replace(/[^0-9\.]/g, '')) || 14.93;
        }

        this.items.push({
            domain: clean,
            price: price || ('$' + numPrice.toFixed(2)),
            raw_price: numPrice,
            type: type || 'register',
            years: 1
        });
        this.isCollapsed = false;
        this.save();
        this.showToast(clean);
    },

    remove: function(domain) {
        if (!domain) return;
        const clean = domain.toLowerCase().trim();
        this.items = this.items.filter(it => it.domain.toLowerCase() !== clean);
        if (this.items.length === 0) {
            this.isBarHidden = false;
            try {
                localStorage.removeItem(this.hiddenKey);
            } catch (e) {}
        }
        this.save();
    },

    toggle: function(domain, price, rawPrice, type) {
        if (this.has(domain)) {
            this.remove(domain);
        } else {
            this.add(domain, price, rawPrice, type);
        }
    },

    addAll: function(itemsList) {
        if (!Array.isArray(itemsList)) return;
        let addedAny = false;
        itemsList.forEach(it => {
            if (it && it.domain && !this.has(it.domain)) {
                let numPrice = parseFloat(it.raw_price);
                if (isNaN(numPrice) || numPrice <= 0) {
                    numPrice = parseFloat(String(it.price).replace(/[^0-9\.]/g, '')) || 14.93;
                }
                this.items.push({
                    domain: it.domain.toLowerCase().trim(),
                    price: it.price || ('$' + numPrice.toFixed(2)),
                    raw_price: numPrice,
                    type: it.type || 'register',
                    years: 1
                });
                addedAny = true;
            }
        });
        if (addedAny) {
            this.isCollapsed = false;
            this.showToast(itemsList.length + ' domains');
        }
        this.save();
    },

    clear: function() {
        this.items = [];
        this.isBarHidden = false;
        try {
            localStorage.removeItem(this.hiddenKey);
        } catch (e) {}
        this.save();
    },

    getTotal: function() {
        return this.items.reduce((sum, it) => sum + (parseFloat(it.raw_price) || 0), 0);
    },

    hideBar: function() {
        this.isBarHidden = true;
        try {
            localStorage.setItem(this.hiddenKey, '1');
        } catch (e) {}
        const bar = document.getElementById('cfDomainCartBar');
        if (bar) {
            bar.classList.add('cf-cart-bar-hiding');
            setTimeout(() => {
                bar.style.display = 'none';
                bar.classList.add('cf-cart-bar-hidden');
                bar.classList.remove('cf-cart-bar-hiding');
            }, 250);
        }
    },

    showBar: function() {
        this.isBarHidden = false;
        try {
            localStorage.removeItem(this.hiddenKey);
        } catch (e) {}
        const bar = document.getElementById('cfDomainCartBar');
        if (bar) {
            bar.classList.remove('cf-cart-bar-hidden', 'cf-cart-bar-hiding');
        }
        this.renderCartBar();
    },

    toggleCollapse: function() {
        this.hideBar();
    },

    applyCollapseState: function() {
        const body = document.getElementById('cfCartItemsBody');
        const bar = document.getElementById('cfDomainCartBar');
        if (body) {
            body.style.display = this.isCollapsed ? 'none' : 'block';
        }
        if (bar) {
            bar.classList.toggle('cf-cart-collapsed', this.isCollapsed);
        }
    },

    showToast: function(domainName) {
        const toast = document.getElementById('cfCartToast');
        const domEl = document.getElementById('cfToastDomain');
        if (!toast) return;
        if (domEl) domEl.textContent = domainName;
        toast.style.display = 'block';
        toast.classList.add('show');
        if (this._toastTimer) clearTimeout(this._toastTimer);
        this._toastTimer = setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => { toast.style.display = 'none'; }, 300);
        }, 3500);
    },

    openDrawer: function() {
        const drawer = document.getElementById('cfCartDrawer');
        const backdrop = document.getElementById('cfCartDrawerBackdrop');
        this.renderDrawer();
        if (drawer) drawer.classList.add('active');
        if (backdrop) backdrop.classList.add('active');
        document.body.style.overflow = 'hidden';
    },

    closeDrawer: function() {
        const drawer = document.getElementById('cfCartDrawer');
        const backdrop = document.getElementById('cfCartDrawerBackdrop');
        if (drawer) drawer.classList.remove('active');
        if (backdrop) backdrop.classList.remove('active');
        document.body.style.overflow = '';
    },

    updateHeaderBadges: function() {
        const count = this.items.length;
        const badges = document.querySelectorAll('.cfHeaderCartBadge, #cfHeaderCartCount, #cfHeaderCartCountMobile');
        badges.forEach(b => {
            b.textContent = count;
            if (count > 0) {
                b.style.display = 'flex';
                b.classList.remove('cf-badge-pulse');
                void b.offsetWidth;
                b.classList.add('cf-badge-pulse');
            } else {
                b.style.display = 'none';
            }
        });
    },

    renderDrawer: function() {
        const body = document.getElementById('cfCartDrawerBody');
        const countText = document.getElementById('cfDrawerCountText');
        const totalText = document.getElementById('cfDrawerTotalText');
        const footer = document.getElementById('cfCartDrawerFooter');
        const count = this.items.length;
        const total = this.getTotal();
        const baseUrl = window.CF_BASE_URL || '';

        if (countText) countText.textContent = count + (count === 1 ? ' item selected' : ' items selected');
        if (totalText) totalText.textContent = '$' + total.toFixed(2);

        if (!body) return;

        if (count === 0) {
            body.innerHTML = `
                <div class="cf-drawer-empty-state text-center py-5 px-3">
                    <div class="cf-drawer-empty-icon mb-3">
                        <i class="las la-shopping-cart"></i>
                    </div>
                    <h5 class="fw-800 text-slate-900 mb-2" style="font-size: 20px;">Your Cart is Empty</h5>
                    <p class="text-muted fs-14 mb-4" style="max-width: 290px; margin-left: auto; margin-right: auto; line-height: 1.5;">You haven't added any domains yet. Search for your ideal domain or explore hosting solutions!</p>
                    <a href="${baseUrl}domain" class="btn cf-btn-drawer-search" onclick="cfDomainCart.closeDrawer()">
                        <i class="las la-search me-1.5"></i> Find a Domain
                    </a>
                </div>
            `;
            if (footer) footer.style.display = 'none';
            return;
        }

        if (footer) footer.style.display = 'block';

        body.innerHTML = `
            <div class="cf-drawer-items-list">
                ${this.items.map(it => `
                    <div class="cf-drawer-item-card">
                        <div class="cf-drawer-item-left">
                            <div class="cf-drawer-item-icon">
                                <i class="las la-globe"></i>
                            </div>
                            <div class="cf-drawer-item-details">
                                <strong class="cf-drawer-item-name" title="${cfEscapeHtml(it.domain)}">${cfEscapeHtml(it.domain)}</strong>
                                <span class="cf-drawer-item-tag">1 Year Registration</span>
                            </div>
                        </div>
                        <div class="cf-drawer-item-right">
                            <span class="cf-drawer-item-price">${cfEscapeHtml(it.price)}</span>
                            <button type="button" class="cf-drawer-item-del" onclick="cfDomainCart.remove('${cfEscapeHtml(it.domain)}')" title="Remove domain" aria-label="Remove">
                                <i class="las la-times"></i>
                            </button>
                        </div>
                    </div>
                `).join('')}
            </div>
        `;
    },

    renderCartBar: function() {
        const path = (window.location && window.location.pathname) ? window.location.pathname.toLowerCase() : '';
        const isCheckoutPage = !!(
            (document.body && document.body.classList.contains('cf-checkout-page')) ||
            path.indexOf('/checkout') !== -1 ||
            path.indexOf('checkout.php') !== -1
        );

        let bar = document.getElementById('cfDomainCartBar');
        if (isCheckoutPage) {
            if (bar) {
                bar.style.display = 'none';
                bar.classList.add('cf-cart-bar-hidden');
            }
            return;
        }

        if (!bar) {
            bar = document.createElement('div');
            bar.id = 'cfDomainCartBar';
            bar.className = 'cf-floating-cart-bar';
            bar.style.display = 'none';
            bar.innerHTML = `
                <div class="cf-cart-bar-header">
                    <div class="cf-cart-bar-header-left">
                        <div class="cf-cart-bar-badge">
                            <i class="las la-shopping-cart"></i>
                            <span id="cfCartCount" class="cf-cart-count-pill">0</span>
                        </div>
                        <div>
                            <h6 class="cf-cart-header-title mb-0">
                                Selected Domains (<span id="cfCartCountTitle">0</span>)
                            </h6>
                            <small class="cf-cart-header-subtitle">Review added domains below</small>
                        </div>
                    </div>
                    <div class="cf-cart-bar-header-right">
                        <button type="button" class="cf-btn-cart-toggle" id="cfCartToggleBtn" onclick="cfDomainCart.hideBar()" title="Hide cart drawer" aria-label="Hide cart">
                            <i class="las la-times"></i>
                            <span>Hide</span>
                        </button>
                        <button type="button" class="cf-btn-cart-clear-link" onclick="cfDomainCart.clear()" title="Clear all domains from cart">
                            <i class="las la-trash-alt me-1"></i>
                            <span>Clear</span>
                        </button>
                    </div>
                </div>
                <div class="cf-cart-items-body" id="cfCartItemsBody">
                    <div class="cf-cart-items-wrap" id="cfCartChipsWrap"></div>
                </div>
                <div class="cf-cart-bar-footer">
                    <div class="cf-cart-total-box">
                        <small class="cf-cart-total-label">Estimated Total</small>
                        <strong id="cfCartTotalText" class="cf-cart-total-amount">$0.00</strong>
                    </div>
                    <button type="button" class="btn cf-btn-checkout" onclick="cfDomainCart.checkout()">
                        <span>Continue to Checkout</span>
                        <i class="las la-arrow-right ms-1"></i>
                    </button>
                </div>
            `;
            document.body.appendChild(bar);
        }

        const count = this.items.length;
        if (count === 0 || this.isBarHidden) {
            bar.style.display = 'none';
            bar.classList.add('cf-cart-bar-hidden');
            return;
        }

        bar.classList.remove('cf-cart-bar-hidden', 'cf-cart-bar-hiding');
        bar.style.display = 'block';
        this.applyCollapseState();

        const countEl = document.getElementById('cfCartCount');
        if (countEl) countEl.textContent = count;

        const countTitleEl = document.getElementById('cfCartCountTitle');
        if (countTitleEl) countTitleEl.textContent = count;

        const totalEl = document.getElementById('cfCartTotalText');
        if (totalEl) totalEl.textContent = '$' + this.getTotal().toFixed(2);

        const listWrap = document.getElementById('cfCartChipsWrap');
        if (listWrap) {
            listWrap.innerHTML = this.items.map(it => `
                <div class="cf-cart-item-row">
                    <div class="cf-cart-item-info">
                        <div class="cf-cart-item-icon">
                            <i class="las la-globe"></i>
                        </div>
                        <div class="cf-cart-item-details">
                            <strong class="cf-cart-item-domain">${cfEscapeHtml(it.domain)}</strong>
                            <span class="cf-cart-item-term">1 Year Registration</span>
                        </div>
                    </div>
                    <div class="cf-cart-item-actions">
                        <span class="cf-cart-item-price">${cfEscapeHtml(it.price)}</span>
                        <button type="button" class="cf-cart-item-del-btn" onclick="cfDomainCart.remove('${cfEscapeHtml(it.domain)}')" title="Remove ${cfEscapeHtml(it.domain)}" aria-label="Remove domain">
                            <i class="las la-times"></i>
                        </button>
                    </div>
                </div>
            `).join('');
        }
    },

    syncButtons: function() {
        document.querySelectorAll('[data-domain-cart-btn]').forEach(btn => {
            const dom = btn.getAttribute('data-domain-cart-btn');
            const inCart = this.has(dom);
            btn.classList.toggle('in-cart', inCart);
            const textEl = btn.querySelector('.btn-cart-text') || btn.querySelector('span');
            const iconEl = btn.querySelector('i');
            if (inCart) {
                if (textEl) textEl.textContent = 'In Cart';
                if (iconEl) iconEl.className = 'las la-check me-1';
            } else {
                if (textEl) textEl.textContent = 'Add to Cart';
                if (iconEl) iconEl.className = 'las la-cart-plus me-1';
            }
        });
    },

    checkout: function() {
        if (this.items.length === 0) return;
        const domList = this.items.map(it => it.domain).join(',');
        const baseUrl = window.CF_BASE_URL || '';
        window.location.href = baseUrl + 'checkout?domains=' + encodeURIComponent(domList);
    }
};

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && window.cfDomainCart && typeof window.cfDomainCart.closeDrawer === 'function') {
        window.cfDomainCart.closeDrawer();
    }
});

function cfEscapeHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

(function() {
    'use strict';

    function initDomainChecker() {
        window.cfDomainCart.init();

        const domainForms = document.querySelectorAll('#domainForm, .domain-mega-search-box');
        if (!domainForms.length) return;

        domainForms.forEach(form => {
            if (form.getAttribute('data-checker-bound') === 'true') return;
            form.setAttribute('data-checker-bound', 'true');

            const input = form.querySelector('input[name="domain"], input[name="query"], #domainInput');
            const submitBtn = form.querySelector('button[type="submit"], #domainSearchBtn');

            let resultsContainer = (form.nextElementSibling && form.nextElementSibling.classList.contains('cf-domain-results-wrap')) 
                ? form.nextElementSibling 
                : form.parentElement.querySelector('.cf-domain-results-wrap');

            if (!resultsContainer) {
                resultsContainer = document.getElementById('domainSearchResults');
            }

            if (!resultsContainer) {
                resultsContainer = document.createElement('div');
                resultsContainer.className = 'cf-domain-results-wrap';
                resultsContainer.style.display = 'none';
                form.parentNode.insertBefore(resultsContainer, form.nextSibling);
            }

            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const query = input ? input.value.trim() : '';
                if (!query) {
                    if (input) input.focus();
                    return;
                }

                performDomainCheck(query, submitBtn, resultsContainer);
            });
        });
    }

    function performDomainCheck(domainQuery, submitBtn, container) {
        if (!container) return;

        const originalBtnHtml = submitBtn ? submitBtn.innerHTML : '<span>Search</span>';
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span><span>Checking...</span>';
        }

        container.style.display = 'block';
        container.innerHTML = `
            <div class="cf-domain-loading-card position-relative">
                <button type="button" class="btn cf-domain-close-btn" onclick="cfResetDomainSearch()" aria-label="Cancel search" title="Cancel">
                    <i class="las la-times"></i>
                </button>
                <div class="cf-domain-pulse-spinner"></div>
                <h4 class="fs-16 fw-bold text-slate-800 mb-1">Checking Registry Availability...</h4>
                <p class="text-muted fs-13 mb-0">Verifying <strong>${cfEscapeHtml(domainQuery)}</strong> across ICANN Anycast DNS &amp; global registries.</p>
            </div>
        `;

        if (window.innerWidth < 992) {
            container.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        let apiBase = 'check-domain.php';
        const scriptEl = document.querySelector('script[src*="cloudflox-domain-checker.js"]');
        if (scriptEl) {
            const scriptSrc = scriptEl.getAttribute('src');
            const rootPrefix = scriptSrc.replace(/assets\/js\/cloudflox-domain-checker\.js.*$/, '');
            apiBase = (rootPrefix ? rootPrefix : '') + 'check-domain.php';
        }

        const formData = new FormData();
        formData.append('domain', domainQuery);

        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 12000);

        fetch(apiBase, {
            method: 'POST',
            body: formData,
            signal: controller.signal
        })
        .then(response => {
            clearTimeout(timeoutId);
            if (!response.ok) throw new Error('HTTP error ' + response.status);
            return response.json();
        })
        .then(data => {
            if (data.status === 'error') {
                renderError(container, data.message || 'Unable to check domain.');
                return;
            }
            renderResults(container, data);
        })
        .catch(err => {
            clearTimeout(timeoutId);
            console.error('Domain check failed:', err);
            if (err.name === 'AbortError') {
                renderError(container, 'Connection timed out. Please try again.');
            } else {
                renderError(container, 'Unable to check domain right now. Please try again.');
            }
        })
        .finally(() => {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnHtml;
            }
        });
    }

    // Dismiss / Reset Domain Search Results
    window.cfResetDomainSearch = function() {
        const container = document.getElementById('domainSearchResults') || document.querySelector('.cf-domain-results-wrap');
        if (container) {
            container.style.display = 'none';
            container.innerHTML = '';
        }
    };

    function renderResults(container, data) {
        const primary = data.primary;
        const isAvail = primary.is_available;
        const suggestions = data.suggestions || [];
        const baseUrl = window.CF_BASE_URL || '';
        const inCart = window.cfDomainCart.has(primary.domain);

        const primaryBuyUrl = baseUrl + 'checkout?domain=' + encodeURIComponent(primary.domain) + '&price=' + encodeURIComponent(primary.price) + '&domainType=register';
        const primaryTransferUrl = baseUrl + 'checkout?domain=' + encodeURIComponent(primary.domain) + '&domainType=transfer';

        let primaryHtml = '';
        if (isAvail) {
            primaryHtml = `
                <div class="cf-domain-card cf-domain-card-available position-relative">
                    <button type="button" class="btn cf-domain-close-btn" onclick="cfResetDomainSearch()" aria-label="Close search results" title="Close results">
                        <i class="las la-times"></i>
                    </button>
                    <div class="cf-domain-card-left">
                        <div class="d-flex align-items-center gap-2 mb-1 flex-wrap pe-4">
                            <span class="cf-badge-available">
                                <i class="las la-check-circle"></i> Available Now
                            </span>
                            <span class="text-muted fs-12">• Free WHOIS Privacy &amp; Anycast DNS included</span>
                        </div>
                        <h3 class="cf-domain-name-title mb-1">${cfEscapeHtml(primary.domain)}</h3>
                        <p class="text-slate-600 fs-13 mb-0">Great choice! Add to your cart or proceed to checkout.</p>
                    </div>
                    <div class="cf-domain-card-right d-flex align-items-center gap-2 flex-wrap">
                        <div class="cf-domain-price-box me-1">
                            <span class="cf-domain-price text-success">${cfEscapeHtml(primary.price)}</span>
                            <span class="cf-domain-period">/ 1st yr</span>
                        </div>
                        <button type="button" class="btn cf-btn-domain-cart-toggle ${inCart ? 'in-cart' : ''}" data-domain-cart-btn="${cfEscapeHtml(primary.domain)}" onclick="cfDomainCart.toggle('${cfEscapeHtml(primary.domain)}', '${cfEscapeHtml(primary.price)}', ${primary.raw_price}, 'register')">
                            <i class="las ${inCart ? 'la-check' : 'la-cart-plus'} me-1"></i>
                            <span class="btn-cart-text">${inCart ? 'In Cart' : 'Add to Cart'}</span>
                        </button>
                        <a href="${cfEscapeHtml(primaryBuyUrl)}" class="btn cf-btn-domain-direct-buy">
                            <span>Checkout &rarr;</span>
                        </a>
                    </div>
                </div>
            `;
        } else {
            primaryHtml = `
                <div class="cf-domain-card cf-domain-card-taken position-relative">
                    <button type="button" class="btn cf-domain-close-btn" onclick="cfResetDomainSearch()" aria-label="Close search results" title="Close results">
                        <i class="las la-times"></i>
                    </button>
                    <div class="cf-domain-card-left">
                        <div class="d-flex align-items-center gap-2 mb-2 flex-wrap pe-4">
                            <span class="cf-badge-taken">
                                <i class="las la-times-circle"></i> Already Registered
                            </span>
                            <span class="text-muted fs-12">Unavailable for new registration</span>
                        </div>
                        <h3 class="cf-domain-name-title text-muted text-decoration-line-through mb-1">${cfEscapeHtml(primary.domain)}</h3>
                        <p class="text-slate-500 fs-13 mb-0">Already own this domain? Transfer management to CloudFlox for cheaper renewals.</p>
                    </div>
                    <div class="cf-domain-card-right">
                        <a href="${cfEscapeHtml(primaryTransferUrl)}" class="btn cf-btn-domain-transfer">
                            <i class="las la-exchange-alt me-1"></i>
                            <span>Transfer to CloudFlox</span>
                            <i class="las la-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            `;
        }

        // Suggestions List with "+ Add to Cart" buttons
        let suggestionsHtml = '';
        if (suggestions.length > 0) {
            let itemsHtml = suggestions.map(s => {
                const sInCart = window.cfDomainCart.has(s.domain);
                const availBadge = s.is_available 
                    ? `<span class="cf-badge-alt-available"><i class="las la-check me-1"></i>Available</span>`
                    : `<span class="cf-badge-alt-taken"><i class="las la-times me-1"></i>Taken</span>`;

                const dealBadge = s.badge 
                    ? `<span class="badge ${s.badge === 'Hot Deal' ? 'bg-orange-subtle text-orange' : 'bg-primary-subtle text-primary'} rounded-pill fs-11 ms-2">${cfEscapeHtml(s.badge)}</span>`
                    : '';

                const altTransferUrl = baseUrl + 'checkout?domain=' + encodeURIComponent(s.domain) + '&domainType=transfer';

                const actionControl = s.is_available
                    ? `<button type="button" class="btn btn-sm cf-btn-alt-cart-toggle ${sInCart ? 'in-cart' : ''}" data-domain-cart-btn="${cfEscapeHtml(s.domain)}" onclick="cfDomainCart.toggle('${cfEscapeHtml(s.domain)}', '${cfEscapeHtml(s.price)}', ${s.raw_price}, 'register')">
                           <i class="las ${sInCart ? 'la-check' : 'la-cart-plus'} me-1"></i>
                           <span class="btn-cart-text">${sInCart ? 'In Cart' : 'Add to Cart'}</span>
                       </button>`
                    : `<a href="${cfEscapeHtml(altTransferUrl)}" class="btn btn-sm btn-outline-secondary py-1 px-3 fs-12 rounded-pill">Transfer</a>`;

                return `
                    <div class="cf-domain-alt-row">
                        <div class="cf-domain-alt-name">
                            <strong>${cfEscapeHtml(s.sld)}<span class="text-primary">${cfEscapeHtml(s.tld)}</span></strong>
                            ${dealBadge}
                        </div>
                        <div class="cf-domain-alt-meta d-flex align-items-center gap-2">
                            ${availBadge}
                            <span class="cf-domain-alt-price">${cfEscapeHtml(s.price)}<small>/yr</small></span>
                            ${actionControl}
                        </div>
                    </div>
                `;
            }).join('');

            suggestionsHtml = `
                <div class="cf-domain-alternatives-section">
                    <div class="cf-domain-alt-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <span class="fw-bold fs-14 text-slate-800">
                            <i class="las la-sparkles text-primary me-1"></i> More Available Extensions for <strong>${cfEscapeHtml(data.sld)}</strong>:
                        </span>
                        <a href="${baseUrl}domain#domainPricing" class="fs-12 text-primary text-decoration-none fw-semibold">
                            View all 500+ TLDs <i class="las la-arrow-right"></i>
                        </a>
                    </div>
                    <div class="cf-domain-alt-list">
                        ${itemsHtml}
                    </div>
                </div>
            `;
        }

        container.innerHTML = `
            <div class="cf-domain-results-box position-relative">
                ${primaryHtml}
                ${suggestionsHtml}
            </div>
        `;
    }

    function renderError(container, msg) {
        container.innerHTML = `
            <div class="cf-domain-results-box border-danger-subtle bg-danger-subtle text-danger p-3 rounded-3 text-center position-relative">
                <button type="button" class="btn cf-domain-close-btn" onclick="cfResetDomainSearch()" aria-label="Close error" title="Close">
                    <i class="las la-times"></i>
                </button>
                <i class="las la-exclamation-triangle fs-22 mb-1"></i>
                <div class="fw-semibold fs-14">${cfEscapeHtml(msg)}</div>
            </div>
        `;
    }

    // Bulk Domain Search Runner
    window.cfPerformBulkSearch = function() {
        const textarea = document.getElementById('cfBulkSearchTextarea');
        const btn = document.getElementById('cfBtnBulkSearch');
        const container = document.getElementById('domainSearchResults');
        if (!textarea || !btn || !container) return;

        const val = textarea.value.trim();
        if (!val) {
            alert('Please enter at least one domain name to search.');
            textarea.focus();
            return;
        }

        const originalBtnHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span><span>Checking Domains...</span>';

        container.style.display = 'block';
        container.innerHTML = `
            <div class="cf-domain-loading-card position-relative">
                <button type="button" class="btn cf-domain-close-btn" onclick="cfResetDomainSearch()" aria-label="Cancel">
                    <i class="las la-times"></i>
                </button>
                <div class="cf-domain-pulse-spinner"></div>
                <h4 class="fs-16 fw-bold text-slate-800 mb-1">Checking Bulk Registry Availability...</h4>
                <p class="text-muted fs-13 mb-0">Querying domain extensions via parallel ICANN RDAP endpoints...</p>
            </div>
        `;

        if (window.innerWidth < 992) {
            container.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        fetch('check-domain.php?bulk=1&domain=' + encodeURIComponent(val))
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = originalBtnHtml;

                if (data.status === 'error') {
                    renderError(container, data.message || 'Error occurred while checking bulk domains.');
                    return;
                }

                renderBulkResults(container, data);
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = originalBtnHtml;
                renderError(container, 'Network connection error while checking bulk domains.');
            });
    };

    function renderBulkResults(container, data) {
        const results = data.results || [];
        const availableItems = results.filter(r => r.is_available);
        const availableCount = availableItems.length;
        const totalCount = results.length;
        const baseUrl = window.CF_BASE_URL || '';

        // Store available items in window for 1-click "Add All"
        window._cfLatestBulkAvailable = availableItems;

        let rowsHtml = results.map(r => {
            const isAvail = r.is_available;
            const inCart = window.cfDomainCart.has(r.domain);

            const statusBadge = isAvail
                ? `<span class="badge bg-emerald-subtle text-emerald rounded-pill px-2.5 py-1 fs-12 fw-bold"><i class="las la-check-circle me-1"></i>Available</span>`
                : `<span class="badge bg-slate-100 text-slate-500 rounded-pill px-2.5 py-1 fs-12 fw-medium"><i class="las la-times-circle me-1"></i>Taken</span>`;

            let actionCol = '';
            if (isAvail) {
                actionCol = `
                    <div class="d-flex align-items-center gap-2">
                        <strong class="text-slate-900 fs-15 me-1">${cfEscapeHtml(r.price)}</strong>
                        <button type="button" class="btn btn-sm cf-btn-alt-cart-toggle ${inCart ? 'in-cart' : ''}" data-domain-cart-btn="${cfEscapeHtml(r.domain)}" onclick="cfDomainCart.toggle('${cfEscapeHtml(r.domain)}', '${cfEscapeHtml(r.price)}', ${r.raw_price}, 'register')">
                            <i class="las ${inCart ? 'la-check' : 'la-cart-plus'} me-1"></i>
                            <span class="btn-cart-text">${inCart ? 'In Cart' : 'Add to Cart'}</span>
                        </button>
                    </div>
                `;
            } else {
                const transferUrl = baseUrl + 'checkout?domain=' + encodeURIComponent(r.domain) + '&domainType=transfer';
                actionCol = `
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted fs-13 me-1">Unavailable</span>
                        <a href="${cfEscapeHtml(transferUrl)}" class="btn btn-sm btn-outline-secondary py-1 px-3 fs-12 rounded-pill">Transfer</a>
                    </div>
                `;
            }

            return `
                <div class="cf-bulk-item-row">
                    <div class="d-flex align-items-center gap-2.5">
                        <div style="width: 32px; height: 32px; border-radius: 50%; background: ${isAvail ? '#DCFCE7' : '#F1F5F9'}; color: ${isAvail ? '#15803D' : '#64748B'}; display: flex; align-items: center; justify-content: center; font-size: 16px;">
                            <i class="las ${isAvail ? 'la-globe' : 'la-lock'}"></i>
                        </div>
                        <div>
                            <strong class="fs-15 text-slate-900 d-block">${cfEscapeHtml(r.domain)}</strong>
                            <small class="text-muted fs-11">${cfEscapeHtml(r.tld)} extension</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        ${statusBadge}
                        ${actionCol}
                    </div>
                </div>
            `;
        }).join('');

        container.innerHTML = `
            <div class="cf-bulk-results-box position-relative">
                <div class="cf-bulk-header-bar">
                    <div class="d-flex align-items-center gap-2">
                        <i class="las la-layer-group text-primary fs-20"></i>
                        <div>
                            <strong class="text-slate-900 fs-15 d-block">Bulk Availability Results</strong>
                            <span class="text-muted fs-12">${availableCount} available out of ${totalCount} domain(s) queried</span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        ${availableCount > 0 ? `
                            <button type="button" class="btn btn-sm btn-success fw-bold px-3 py-1.5 rounded-pill fs-12 shadow-sm" onclick="cfAddAllBulkAvailable()">
                                <i class="las la-cart-plus me-1"></i> Add All Available (${availableCount})
                            </button>
                        ` : ''}
                        <button type="button" class="btn cf-domain-close-btn position-static" onclick="cfResetDomainSearch()" aria-label="Close">
                            <i class="las la-times"></i>
                        </button>
                    </div>
                </div>
                <div class="cf-bulk-rows-wrap">
                    ${rowsHtml}
                </div>
            </div>
        `;
    }

    window.cfAddAllBulkAvailable = function() {
        if (window._cfLatestBulkAvailable && window._cfLatestBulkAvailable.length > 0) {
            window.cfDomainCart.addAll(window._cfLatestBulkAvailable);
        }
    };

    // Mode Switcher between Single & Bulk Search
    window.cfSwitchDomainSearchMode = function(mode) {
        const btnSingle = document.getElementById('cfTabBtnSingle');
        const btnBulk = document.getElementById('cfTabBtnBulk');
        const formSingle = document.getElementById('domainForm');
        const pillsSingle = document.getElementById('cfPopularPillsWrap');
        const wrapBulk = document.getElementById('cfBulkSearchWrap');

        if (mode === 'bulk') {
            if (btnSingle) btnSingle.classList.remove('active');
            if (btnBulk) btnBulk.classList.add('active');
            if (formSingle) formSingle.style.display = 'none';
            if (pillsSingle) pillsSingle.style.display = 'none';
            if (wrapBulk) wrapBulk.style.display = 'block';
        } else {
            if (btnBulk) btnBulk.classList.remove('active');
            if (btnSingle) btnSingle.classList.add('active');
            if (wrapBulk) wrapBulk.style.display = 'none';
            if (formSingle) formSingle.style.display = 'flex';
            if (pillsSingle) pillsSingle.style.display = 'flex';
        }
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initDomainChecker);
    } else {
        initDomainChecker();
    }
})();
