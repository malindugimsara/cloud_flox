/**
 * CloudFlox Inline Domain Availability Checker
 * Intercepts domain searches and renders real-time availability & alternative extensions
 * directly on the page without navigating away to WHMCS.
 */
(function() {
    'use strict';

    function initDomainChecker() {
        const domainForms = document.querySelectorAll('#domainForm, .domain-mega-search-box');
        if (!domainForms.length) return;

        domainForms.forEach(form => {
            // Avoid duplicate binding
            if (form.getAttribute('data-checker-bound') === 'true') return;
            form.setAttribute('data-checker-bound', 'true');

            const input = form.querySelector('input[name="query"], #domainInput');
            const submitBtn = form.querySelector('button[type="submit"], #domainSearchBtn');

            // Find or create results container directly after form
            let resultsContainer = (form.nextElementSibling && form.nextElementSibling.classList.contains('cf-domain-results-wrap')) 
                ? form.nextElementSibling 
                : form.parentElement.querySelector('.cf-domain-results-wrap');

            if (!resultsContainer) {
                resultsContainer = document.getElementById('domainSearchResults') || document.getElementById('homeDomainSearchResults');
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

        // 1. Loading UI State
        const originalBtnHtml = submitBtn ? submitBtn.innerHTML : '<span>Search</span>';
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span><span>Checking...</span>';
        }

        container.style.display = 'block';
        container.innerHTML = `
            <div class="cf-domain-loading-card">
                <div class="cf-domain-pulse-spinner"></div>
                <h4 class="fs-16 fw-bold text-slate-800 mb-1">Checking Registry Availability...</h4>
                <p class="text-muted fs-13 mb-0">Verifying <strong>${escapeHtml(domainQuery)}</strong> across ICANN Anycast DNS &amp; global registries.</p>
            </div>
        `;

        // Smooth scroll to container if on mobile
        if (window.innerWidth < 992) {
            container.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        // 2. Fetch from check-domain.php
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

    // Global Active Domain Cart State
    let activeDomainCart = {
        domain: null,
        domainPrice: 0.00,
        hostingPid: null,
        hostingName: null,
        hostingPrice: 0.00
    };

    function renderResults(container, data) {
        const primary = data.primary;
        const isAvail = primary.is_available;
        const suggestions = data.suggestions || [];

        // Reset cart state on new search
        activeDomainCart.domain = null;
        activeDomainCart.domainPrice = 0.00;
        activeDomainCart.hostingPid = null;
        activeDomainCart.hostingName = null;
        activeDomainCart.hostingPrice = 0.00;
        hideFloatingCartBar();

        let primaryHtml = '';
        if (isAvail) {
            primaryHtml = `
                <div class="cf-domain-card cf-domain-card-available">
                    <div class="cf-domain-card-left">
                        <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                            <span class="cf-badge-available">
                                <i class="las la-check-circle"></i> Available Now
                            </span>
                            <span class="text-muted fs-12">• Free WHOIS Privacy &amp; Anycast DNS included</span>
                        </div>
                        <h3 class="cf-domain-name-title mb-1">${escapeHtml(primary.domain)}</h3>
                        <p class="text-slate-600 fs-13 mb-0">Great choice! Claim this domain instantly to add it to your cart.</p>
                    </div>
                    <div class="cf-domain-card-right">
                        <div class="cf-domain-price-box">
                            <span class="cf-domain-price text-success">${escapeHtml(primary.price)}</span>
                            <span class="cf-domain-period">/ 1st yr</span>
                        </div>
                        <button type="button" class="btn cf-btn-domain-buy" id="cfBtnClaimPrimary" onclick="cfAddDomainToCart('${escapeHtml(primary.domain)}', '${escapeHtml(primary.price)}', this)">
                            <i class="las la-cart-plus me-1"></i>
                            <span class="btn-text">Claim Domain</span>
                            <i class="las la-arrow-right ms-1"></i>
                        </button>
                    </div>
                </div>
            `;
        } else {
            primaryHtml = `
                <div class="cf-domain-card cf-domain-card-taken">
                    <div class="cf-domain-card-left">
                        <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                            <span class="cf-badge-taken">
                                <i class="las la-times-circle"></i> Already Registered
                            </span>
                            <span class="cf-domain-subtitle-taken">Unavailable for new registration</span>
                        </div>
                        <h3 class="cf-domain-name-title mb-1">${escapeHtml(primary.domain)}</h3>
                        <p class="cf-domain-desc-taken mb-0">Already own this domain? Transfer management to CloudFlox for cheaper renewals.</p>
                    </div>
                    <div class="cf-domain-card-right">
                        <button type="button" class="btn cf-btn-domain-transfer" onclick="cfOpenTransferDrawer('${escapeHtml(primary.domain)}')">
                            <i class="las la-exchange-alt me-1"></i>
                            <span>Transfer to CloudFlox</span>
                        </button>
                    </div>
                </div>
            `;
        }

        // Suggestions List (Original Clean Layout)
        let suggestionsHtml = '';
        if (suggestions.length > 0) {
            let itemsHtml = suggestions.map(s => {
                const availBadge = s.is_available 
                    ? `<span class="cf-badge-alt-available"><i class="las la-check me-1"></i>Available</span>`
                    : `<span class="cf-badge-alt-taken"><i class="las la-times me-1"></i>Taken</span>`;

                let tagClass = 'cf-tld-tag cf-tld-tag-blue';
                if (s.badge === 'Hot Deal' || s.badge === 'Sale') {
                    tagClass = 'cf-tld-tag cf-tld-tag-orange';
                } else if (s.badge === 'Tech Favorite' || s.badge === 'Tech') {
                    tagClass = 'cf-tld-tag cf-tld-tag-purple';
                } else if (s.badge === 'Special') {
                    tagClass = 'cf-tld-tag cf-tld-tag-green';
                }

                const dealBadge = s.badge 
                    ? `<span class="${tagClass}">${escapeHtml(s.badge)}</span>`
                    : '';

                const actionBtn = s.is_available
                    ? `<button type="button" class="cf-btn-alt-buy" onclick="cfAddDomainToCart('${escapeHtml(s.domain)}', '${escapeHtml(s.price)}', this)">
                           <span>Select</span>
                           <i class="las la-plus ms-1"></i>
                       </button>`
                    : `<button type="button" class="cf-btn-alt-transfer" onclick="cfOpenTransferDrawer('${escapeHtml(s.domain)}')">
                           <i class="las la-exchange-alt me-1"></i>
                           <span>Transfer</span>
                       </button>`;

                return `
                    <div class="cf-domain-alt-row">
                        <div class="cf-domain-alt-name">
                            <strong>${escapeHtml(s.sld)}<span class="cf-domain-tld-ext">${escapeHtml(s.tld)}</span></strong>
                            ${dealBadge}
                        </div>
                        <div class="cf-domain-alt-meta">
                            ${availBadge}
                            <span class="cf-domain-alt-price">${escapeHtml(s.price)}<small>/yr</small></span>
                            ${actionBtn}
                        </div>
                    </div>
                `;
            }).join('');

            suggestionsHtml = `
                <div class="cf-domain-alternatives-section">
                    <div class="cf-domain-alt-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <span class="fw-bold fs-14 cf-domain-alt-title">
                            <i class="las la-sparkles text-primary me-1"></i> More Available Extensions for <strong>${escapeHtml(data.sld)}</strong>:
                        </span>
                        <a href="domain#domainPricing" class="cf-domain-view-all-link">
                            <span>View all TLDs &amp; rates</span>
                            <i class="las la-arrow-right ms-1"></i>
                        </a>
                    </div>
                    <div class="cf-domain-alt-list">
                        ${itemsHtml}
                    </div>
                </div>
            `;
        }

        // REDESIGNED HIGH-CLARITY & SIMPLE DOMAIN CART + OPTIONAL HOSTING WORKFLOW
        const upsellSectionHtml = `
            <div id="cfDomainCartUpsellContainer" class="cf-domain-cart-upsell-wrap" style="display: none;">
                
                <!-- 1. Instant Domain Added Confirmation & 1-Click Fast Checkout Bar -->
                <div class="cf-domain-added-strip">
                    <div class="cf-domain-added-left">
                        <div class="cf-domain-added-icon">
                            <i class="las la-check"></i>
                        </div>
                        <div class="cf-domain-added-info">
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <span class="cf-domain-added-badge">In Your Cart</span>
                                <h4 class="cf-domain-added-name mb-0" id="cfReservedDomainName">${escapeHtml(primary.domain)}</h4>
                                <span class="cf-domain-added-price" id="cfReservedDomainPrice">${escapeHtml(primary.price)}<small>/yr</small></span>
                            </div>
                            <p class="cf-domain-added-subtext mb-0">
                                <span><i class="las la-shield-alt text-success me-1"></i>Free Privacy Protection</span>
                                <span class="cf-bullet-sep">•</span>
                                <span><i class="las la-bolt text-warning me-1"></i>Instant 60-Sec DNS</span>
                                <span class="cf-bullet-sep">•</span>
                                <span><i class="las la-lock text-info me-1"></i>Domain Lock Protection</span>
                            </p>
                        </div>
                    </div>
                    <div class="cf-domain-added-right">
                        <button type="button" class="btn cf-btn-quick-domain" onclick="cfProceedToCheckoutDrawer()">
                            <span>Continue to Checkout</span>
                            <i class="las la-arrow-right ms-2"></i>
                        </button>
                    </div>
                </div>

                <!-- 2. Web Hosting Optional Add-On (Clean, Modern, Intuitive Cards) -->
                <div class="cf-domain-hosting-upsell">
                    <div class="cf-hosting-upsell-header">
                        <div class="cf-hosting-header-left">
                            <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                <span class="cf-pill-optional"><i class="las la-plus-circle me-1"></i>Optional Add-On</span>
                                <h4 class="cf-hosting-title mb-0">Need Web Hosting For Your Domain?</h4>
                            </div>
                            <p class="cf-hosting-subtitle mb-0">
                                Select an ultra-fast NVMe cloud hosting plan below, or proceed with domain registration only.
                            </p>
                        </div>
                        <div class="cf-hosting-header-right">
                            <button type="button" class="btn cf-btn-domain-only-link" onclick="cfDeselectAllHosting()">
                                <i class="las la-times-circle me-1"></i>
                                <span>Domain Only (No Hosting)</span>
                            </button>
                        </div>
                    </div>

                    <!-- 3 Clean, Polished Hosting Plan Cards -->
                    <div class="row g-3 cf-upsell-cards-row">
                        
                        <!-- Mini Plan -->
                        <div class="col-12 col-md-4">
                            <div class="cf-upsell-plan-card-min" id="cfUpsellCard101" onclick="cfToggleHosting('101', 'Mini Plan', 3.00)">
                                <div class="cf-card-top-row">
                                    <div>
                                        <span class="cf-plan-type-pill">Personal &amp; Blogs</span>
                                        <h5 class="cf-plan-min-title mb-0">Mini Plan</h5>
                                    </div>
                                    <div class="cf-plan-min-price">
                                        <span class="cf-price-currency">$</span>
                                        <span class="cf-price-val">3.00</span>
                                        <span class="cf-price-cycle">/mo</span>
                                    </div>
                                </div>
                                <div class="cf-plan-min-specs">
                                    <div class="cf-spec-item"><i class="las la-check text-success"></i><span><strong>1</strong> Website Included</span></div>
                                    <div class="cf-spec-item"><i class="las la-check text-success"></i><span><strong>5 GB</strong> Pure NVMe Storage</span></div>
                                    <div class="cf-spec-item"><i class="las la-check text-success"></i><span>Free SSL &amp; Business Email</span></div>
                                    <div class="cf-spec-item"><i class="las la-check text-success"></i><span>99.99% Uptime Guarantee</span></div>
                                </div>
                                <button type="button" class="btn cf-btn-plan-toggle" id="cfBtnToggle101" onclick="event.stopPropagation(); cfToggleHosting('101', 'Mini Plan', 3.00)">
                                    <i class="las la-plus me-1"></i>
                                    <span>Add to Order</span>
                                </button>
                            </div>
                        </div>

                        <!-- Business Starter (Best Value / Recommended) -->
                        <div class="col-12 col-md-4">
                            <div class="cf-upsell-plan-card-min featured" id="cfUpsellCard201" onclick="cfToggleHosting('201', 'Business Starter', 7.50)">
                                <span class="cf-plan-badge-value"><i class="las la-crown me-1"></i>MOST POPULAR</span>
                                <div class="cf-card-top-row">
                                    <div>
                                        <span class="cf-plan-type-pill featured">Small Business &amp; E-Com</span>
                                        <h5 class="cf-plan-min-title mb-0">Business Starter</h5>
                                    </div>
                                    <div class="cf-plan-min-price">
                                        <span class="cf-price-currency">$</span>
                                        <span class="cf-price-val">7.50</span>
                                        <span class="cf-price-cycle">/mo</span>
                                    </div>
                                </div>
                                <div class="cf-plan-min-specs">
                                    <div class="cf-spec-item"><i class="las la-check text-success"></i><span><strong>1</strong> High-Speed Website</span></div>
                                    <div class="cf-spec-item"><i class="las la-check text-success"></i><span><strong>20 GB</strong> Pure NVMe Storage</span></div>
                                    <div class="cf-spec-item"><i class="las la-check text-success"></i><span><strong>1 vCPU &amp; 2 GB</strong> Dedicated RAM</span></div>
                                    <div class="cf-spec-item"><i class="las la-check text-success"></i><span>Free Daily Backups &amp; SSL</span></div>
                                </div>
                                <button type="button" class="btn cf-btn-plan-toggle primary" id="cfBtnToggle201" onclick="event.stopPropagation(); cfToggleHosting('201', 'Business Starter', 7.50)">
                                    <i class="las la-plus me-1"></i>
                                    <span>Add to Order</span>
                                </button>
                            </div>
                        </div>

                        <!-- Business Growth -->
                        <div class="col-12 col-md-4">
                            <div class="cf-upsell-plan-card-min" id="cfUpsellCard202" onclick="cfToggleHosting('202', 'Business Growth', 15.00)">
                                <div class="cf-card-top-row">
                                    <div>
                                        <span class="cf-plan-type-pill">Multi-Site &amp; Agencies</span>
                                        <h5 class="cf-plan-min-title mb-0">Business Growth</h5>
                                    </div>
                                    <div class="cf-plan-min-price">
                                        <span class="cf-price-currency">$</span>
                                        <span class="cf-price-val">15.00</span>
                                        <span class="cf-price-cycle">/mo</span>
                                    </div>
                                </div>
                                <div class="cf-plan-min-specs">
                                    <div class="cf-spec-item"><i class="las la-check text-success"></i><span><strong>5</strong> Websites Included</span></div>
                                    <div class="cf-spec-item"><i class="las la-check text-success"></i><span><strong>40 GB</strong> Pure NVMe Storage</span></div>
                                    <div class="cf-spec-item"><i class="las la-check text-success"></i><span><strong>2 vCPU &amp; 4 GB</strong> Dedicated RAM</span></div>
                                    <div class="cf-spec-item"><i class="las la-check text-success"></i><span>Priority 24/7/365 Tech Support</span></div>
                                </div>
                                <button type="button" class="btn cf-btn-plan-toggle" id="cfBtnToggle202" onclick="event.stopPropagation(); cfToggleHosting('202', 'Business Growth', 15.00)">
                                    <i class="las la-plus me-1"></i>
                                    <span>Add to Order</span>
                                </button>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- 3. High-Clarity Next-Step Checkout Bar (Summary & Final Action) -->
                <div class="cf-domain-checkout-bar" id="cfDomainCheckoutBar">
                    <div class="cf-checkout-bar-content">
                        <div class="cf-checkout-bar-left">
                            <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                                <span class="cf-checkout-step-badge">
                                    <i class="las la-shopping-cart me-1"></i>ORDER SUMMARY
                                </span>
                                <span class="cf-cart-summary-text" id="cfCartSummaryItems">
                                    <strong>${escapeHtml(primary.domain)}</strong> <span class="text-white-50 fs-12">(Domain Only)</span>
                                </span>
                            </div>
                            <div class="cf-cart-total-line">
                                <span class="cf-total-label">Total Due Today:</span>
                                <span class="cf-total-amount" id="cfCartTotalAmount">${escapeHtml(primary.price)}</span>
                                <span class="cf-badge-no-fees">
                                    <i class="las la-shield-alt text-emerald"></i> Zero Hidden Fees • Instant 60-Sec Activation
                                </span>
                            </div>
                        </div>
                        <div class="cf-checkout-bar-right">
                            <button type="button" class="btn cf-btn-checkout-highlight" id="cfBtnProceedCheckout" onclick="cfProceedToCheckoutDrawer()">
                                <span>Proceed to Checkout</span>
                                <i class="las la-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        `;

        container.innerHTML = `
            <div class="cf-domain-results-box">
                ${primaryHtml}
                ${upsellSectionHtml}
                ${suggestionsHtml}
            </div>
        `;
    }

    // Add Domain to Active Cart Action
    window.cfAddDomainToCart = function(domain, price, btn) {
        const rawPrice = String(price).replace(/[^0-9\.]/g, '');
        activeDomainCart.domain = domain;
        activeDomainCart.domainPrice = parseFloat(rawPrice) || 13.57;

        // Reset all Claim & Select buttons
        document.querySelectorAll('.cf-btn-domain-buy, .cf-btn-alt-buy').forEach(b => {
            b.classList.remove('active-in-cart');
            if (b.classList.contains('cf-btn-domain-buy')) {
                b.innerHTML = '<i class="las la-cart-plus me-1"></i><span>Claim Domain</span><i class="las la-arrow-right ms-1"></i>';
            } else {
                b.innerHTML = '<span>Select</span><i class="las la-plus ms-1"></i>';
            }
        });

        // Mark clicked button as Added
        if (btn) {
            btn.classList.add('active-in-cart');
            btn.innerHTML = '<i class="las la-check-circle me-1"></i><span>In Cart ✓</span>';
        }

        // Reveal Upsell & Highlighted Checkout Bar
        const upsellWrap = document.getElementById('cfDomainCartUpsellContainer');
        if (upsellWrap) {
            upsellWrap.style.display = 'block';
            upsellWrap.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        updateDomainCartUI();
        showFloatingCartBar();

        if (window.cfToast) {
            cfToast.success('Domain <strong>' + domain + '</strong> added to cart!');
        }
    };

    // Toggle Web Hosting Add-on in Domain Results
    window.cfToggleHosting = function(pid, name, price) {
        if (activeDomainCart.hostingPid === pid) {
            // Deselect hosting
            activeDomainCart.hostingPid = null;
            activeDomainCart.hostingName = null;
            activeDomainCart.hostingPrice = 0.00;
        } else {
            // Select this hosting plan
            activeDomainCart.hostingPid = pid;
            activeDomainCart.hostingName = name;
            activeDomainCart.hostingPrice = price;
        }

        updateDomainCartUI();
    };

    // Deselect Hosting (Domain Only option)
    window.cfDeselectAllHosting = function() {
        activeDomainCart.hostingPid = null;
        activeDomainCart.hostingName = null;
        activeDomainCart.hostingPrice = 0.00;
        updateDomainCartUI();
    };

    // Update Domain Cart UI calculations
    function updateDomainCartUI() {
        const total = activeDomainCart.domainPrice + activeDomainCart.hostingPrice;
        const totalAmountEl = document.getElementById('cfCartTotalAmount');
        const summaryItemsEl = document.getElementById('cfCartSummaryItems');
        const reservedNameEl = document.getElementById('cfReservedDomainName');
        const reservedPriceEl = document.getElementById('cfReservedDomainPrice');
        const checkoutBtnEl = document.getElementById('cfBtnProceedCheckout');

        if (totalAmountEl) {
            totalAmountEl.textContent = '$' + total.toFixed(2);
        }

        if (reservedNameEl && activeDomainCart.domain) {
            reservedNameEl.textContent = activeDomainCart.domain;
        }

        if (reservedPriceEl && activeDomainCart.domainPrice) {
            reservedPriceEl.innerHTML = '$' + activeDomainCart.domainPrice.toFixed(2) + '<small>/yr</small>';
        }

        if (checkoutBtnEl) {
            checkoutBtnEl.innerHTML = `<span>Proceed to Checkout ($${total.toFixed(2)})</span><i class="las la-arrow-right ms-2"></i>`;
        }

        if (summaryItemsEl) {
            if (activeDomainCart.hostingPid) {
                summaryItemsEl.innerHTML = `<strong>${escapeHtml(activeDomainCart.domain)}</strong> <span class="text-white-50 fs-12">(Domain)</span> + <span class="cf-summary-hosting-pill"><i class="las la-server me-1"></i>${escapeHtml(activeDomainCart.hostingName)} ($${activeDomainCart.hostingPrice.toFixed(2)}/mo)</span>`;
            } else {
                summaryItemsEl.innerHTML = `<strong>${escapeHtml(activeDomainCart.domain)}</strong> <span class="text-white-50 fs-12">(Domain Only)</span>`;
            }
        }

        // Update 3 Hosting Plan Cards button states
        ['101', '201', '202'].forEach(id => {
            const card = document.getElementById('cfUpsellCard' + id);
            const btn = document.getElementById('cfBtnToggle' + id);
            if (!card || !btn) return;

            if (activeDomainCart.hostingPid === id) {
                card.classList.add('selected-hosting');
                btn.className = 'btn cf-btn-plan-toggle selected';
                btn.innerHTML = '<i class="las la-check me-1"></i> Added to Order ✓';
            } else {
                card.classList.remove('selected-hosting');
                const isFeatured = card.classList.contains('featured');
                btn.className = isFeatured 
                    ? 'btn cf-btn-plan-toggle primary' 
                    : 'btn cf-btn-plan-toggle';
                btn.innerHTML = '<i class="las la-plus me-1"></i> Add to Order';
            }
        });

        // Update Floating Cart Bar
        updateFloatingCartBar(total);
    }

    // Launch Dedicated Checkout Page with Cart items
    window.cfProceedToCheckoutDrawer = function() {
        if (!activeDomainCart.domain) return;
        const base = window.CF_BASE_URL || '';
        const pid = activeDomainCart.hostingPid || 'none';
        const price = activeDomainCart.domainPrice || 13.57;
        const url = `${base}checkout?domain=${encodeURIComponent(activeDomainCart.domain)}&price=${price.toFixed(2)}&pid=${encodeURIComponent(pid)}&cycle=monthly`;
        window.location.href = url;
    };

    // Open Domain Transfer in Dedicated Checkout Page
    window.cfOpenTransferDrawer = function(domain) {
        const base = window.CF_BASE_URL || '';
        const url = `${base}checkout?domain=${encodeURIComponent(domain)}&domainType=transfer&pid=201&cycle=annually`;
        window.location.href = url;
    };

    // Floating Sticky Bottom Cart Bar
    function showFloatingCartBar() {
        let bar = document.getElementById('cfFloatingCartBar');
        if (!bar) {
            bar = document.createElement('div');
            bar.id = 'cfFloatingCartBar';
            bar.className = 'cf-floating-cart-bar';
            document.body.appendChild(bar);
        }

        const total = activeDomainCart.domainPrice + activeDomainCart.hostingPrice;
        updateFloatingCartBar(total);

        // Smart visibility: If inline order summary bar is already in view, don't obstruct screen with floating bar
        const checkoutBar = document.getElementById('cfDomainCheckoutBar');
        if (checkoutBar && 'IntersectionObserver' in window) {
            if (!window._cfCartBarObserver) {
                window._cfCartBarObserver = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        const b = document.getElementById('cfFloatingCartBar');
                        if (!b) return;
                        if (entry.isIntersecting) {
                            b.style.display = 'none';
                        } else if (activeDomainCart.domain) {
                            b.style.display = 'block';
                        }
                    });
                }, { threshold: 0.1 });
                window._cfCartBarObserver.observe(checkoutBar);
            }
        } else {
            bar.style.display = 'block';
        }
    }

    function hideFloatingCartBar() {
        const bar = document.getElementById('cfFloatingCartBar');
        if (bar) bar.style.display = 'none';
    }

    function updateFloatingCartBar(total) {
        const bar = document.getElementById('cfFloatingCartBar');
        if (!bar || !activeDomainCart.domain) return;

        const itemCount = activeDomainCart.hostingPid ? 2 : 1;
        const subtext = activeDomainCart.hostingPid 
            ? `${escapeHtml(activeDomainCart.domain)} + ${escapeHtml(activeDomainCart.hostingName)}`
            : `${escapeHtml(activeDomainCart.domain)} (Domain Registration)`;

        bar.innerHTML = `
            <div class="container d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-3">
                    <div class="cf-floating-icon">
                        <i class="las la-shopping-cart fs-20 text-white"></i>
                        <span class="cf-floating-count">${itemCount}</span>
                    </div>
                    <div>
                        <div class="fs-13 fw-bold text-white">${subtext}</div>
                        <div class="fs-12 text-white-50">Free Privacy Protection &amp; Anycast DNS included</div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div class="text-end text-white">
                        <span class="fs-11 text-white-50 d-block">Total Due:</span>
                        <span class="fs-18 fw-extrabold text-warning">$${total.toFixed(2)}</span>
                    </div>
                    <button type="button" class="btn btn-warning fw-extrabold px-4 py-2 rounded-pill shadow-lg text-slate-900" onclick="cfProceedToCheckoutDrawer()">
                        Checkout Now <i class="las la-arrow-right ms-1"></i>
                    </button>
                </div>
            </div>
        `;
    }

    function renderError(container, msg) {
        container.innerHTML = `
            <div class="cf-domain-results-box border-danger-subtle bg-danger-subtle text-danger p-3 rounded-3 text-center">
                <i class="las la-exclamation-triangle fs-22 mb-1"></i>
                <div class="fw-semibold fs-14">${escapeHtml(msg)}</div>
            </div>
        `;
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initDomainChecker);
    } else {
        initDomainChecker();
    }
})();
