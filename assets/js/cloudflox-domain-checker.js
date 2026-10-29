/**
 * CloudFlox Inline Domain Availability Checker
 * -------------------------------------------------------------------------
 * Intercepts domain searches and renders real-time registry availability & alternative extensions
 * directly on the page, with 1-click continuation to on-site checkout.
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

        // 1. Loading UI State
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
                        <h3 class="cf-domain-name-title mb-1">${escapeHtml(primary.domain)}</h3>
                        <p class="text-slate-600 fs-13 mb-0">Great choice! Claim this domain now and configure your registration term.</p>
                    </div>
                    <div class="cf-domain-card-right">
                        <div class="cf-domain-price-box">
                            <span class="cf-domain-price text-success">${escapeHtml(primary.price)}</span>
                            <span class="cf-domain-period">/ 1st yr</span>
                        </div>
                        <a href="${escapeHtml(primaryBuyUrl)}" class="btn cf-btn-domain-buy">
                            <i class="las la-shopping-cart me-1"></i>
                            <span>Buy Domain</span>
                            <i class="las la-arrow-right ms-1"></i>
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
                        <h3 class="cf-domain-name-title text-muted text-decoration-line-through mb-1">${escapeHtml(primary.domain)}</h3>
                        <p class="text-slate-500 fs-13 mb-0">Already own this domain? Transfer management to CloudFlox for cheaper renewals.</p>
                    </div>
                    <div class="cf-domain-card-right">
                        <a href="${escapeHtml(primaryTransferUrl)}" class="btn cf-btn-domain-transfer">
                            <i class="las la-exchange-alt me-1"></i>
                            <span>Transfer to CloudFlox</span>
                            <i class="las la-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            `;
        }

        // Suggestions List (Clean, Direct on-site checkout links)
        let suggestionsHtml = '';
        if (suggestions.length > 0) {
            let itemsHtml = suggestions.map(s => {
                const availBadge = s.is_available 
                    ? `<span class="cf-badge-alt-available"><i class="las la-check me-1"></i>Available</span>`
                    : `<span class="cf-badge-alt-taken"><i class="las la-times me-1"></i>Taken</span>`;

                const dealBadge = s.badge 
                    ? `<span class="badge ${s.badge === 'Hot Deal' ? 'bg-orange-subtle text-orange' : 'bg-primary-subtle text-primary'} rounded-pill fs-11 ms-2">${escapeHtml(s.badge)}</span>`
                    : '';

                const altBuyUrl = baseUrl + 'checkout?domain=' + encodeURIComponent(s.domain) + '&price=' + encodeURIComponent(s.price) + '&domainType=register';
                const altTransferUrl = baseUrl + 'checkout?domain=' + encodeURIComponent(s.domain) + '&domainType=transfer';

                const actionBtn = s.is_available
                    ? `<a href="${escapeHtml(altBuyUrl)}" class="btn btn-sm cf-btn-alt-buy">
                           <span>Select</span>
                           <i class="las la-arrow-right ms-1"></i>
                       </a>`
                    : `<a href="${escapeHtml(altTransferUrl)}" class="btn btn-sm btn-outline-secondary py-1 px-3 fs-12 rounded-pill">Transfer</a>`;

                return `
                    <div class="cf-domain-alt-row">
                        <div class="cf-domain-alt-name">
                            <strong>${escapeHtml(s.sld)}<span class="text-primary">${escapeHtml(s.tld)}</span></strong>
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
                        <span class="fw-bold fs-14 text-slate-800">
                            <i class="las la-sparkles text-primary me-1"></i> More Available Extensions for <strong>${escapeHtml(data.sld)}</strong>:
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
