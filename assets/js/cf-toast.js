/**
 * CloudFlox Modern Toast Notification & Form Validation System
 * High-performance, accessible, glassmorphic toast notifications
 */
(function(window, document) {
    'use strict';

    const DEFAULT_OPTIONS = {
        type: 'info',       // 'error' | 'success' | 'warning' | 'info'
        title: '',
        message: '',
        duration: 5500,     // ms, 0 for sticky
        position: 'top-right', // 'top-right' | 'bottom-right'
        dismissible: true
    };

    function getOrCreateContainer(position) {
        let containerId = 'cf-toast-container';
        let container = document.getElementById(containerId);
        if (!container) {
            container = document.createElement('div');
            container.id = containerId;
            container.className = 'cf-toast-container';
            container.setAttribute('aria-live', 'polite');
            container.setAttribute('role', 'region');
            container.setAttribute('aria-label', 'Notifications');
            document.body.appendChild(container);
        }
        if (position === 'bottom-right') {
            container.classList.add('bottom-right');
        } else {
            container.classList.remove('bottom-right');
        }
        return container;
    }

    const ICONS = {
        error: 'la-times-circle',
        warning: 'la-exclamation-triangle',
        success: 'la-check-circle',
        info: 'la-info-circle'
    };

    const TITLES = {
        error: 'Error',
        warning: 'Attention Required',
        success: 'Success',
        info: 'Notice'
    };

    function show(opts) {
        if (!opts) return null;
        if (typeof opts === 'string') {
            opts = { message: opts };
        }
        const config = Object.assign({}, DEFAULT_OPTIONS, opts);
        const type = (['error', 'success', 'warning', 'info'].indexOf(config.type) !== -1) ? config.type : 'info';
        const title = config.title || TITLES[type];
        const message = config.message || '';
        const duration = (config.duration !== undefined) ? config.duration : (type === 'error' || type === 'warning' ? 6500 : 4500);

        const container = getOrCreateContainer(config.position);

        const toast = document.createElement('div');
        toast.className = `cf-toast cf-toast-${type}`;
        toast.setAttribute('role', type === 'error' ? 'alert' : 'status');

        const iconClass = ICONS[type] || 'la-info-circle';

        let progressBarHtml = '';
        if (duration > 0) {
            progressBarHtml = `<div class="cf-toast-progress-bar" style="animation-duration: ${duration}ms;"></div>`;
        }

        toast.innerHTML = `
            <div class="cf-toast-icon-wrap">
                <i class="las ${iconClass}"></i>
            </div>
            <div class="cf-toast-body">
                <div class="cf-toast-heading">${escapeHtml(title)}</div>
                <div class="cf-toast-text">${message}</div>
            </div>
            ${config.dismissible ? '<button type="button" class="cf-toast-close" aria-label="Dismiss notification"><i class="las la-times"></i></button>' : ''}
            ${progressBarHtml}
        `;

        container.appendChild(toast);

        // Haptic feedback on mobile if error
        if (type === 'error' && window.navigator && window.navigator.vibrate) {
            try { window.navigator.vibrate([25, 40, 25]); } catch (e) {}
        }

        let isPaused = false;
        let remainingTime = duration;
        let startTime = Date.now();
        let timeoutId = null;

        const progressBar = toast.querySelector('.cf-toast-progress-bar');

        const closeToast = () => {
            if (toast.classList.contains('cf-toast-closing')) return;
            toast.classList.add('cf-toast-closing');
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 320);
        };

        const startTimer = (time) => {
            if (duration <= 0) return;
            startTime = Date.now();
            timeoutId = setTimeout(closeToast, time);
            if (progressBar) {
                progressBar.style.animationPlayState = 'running';
            }
        };

        const pauseTimer = () => {
            if (duration <= 0) return;
            clearTimeout(timeoutId);
            remainingTime -= (Date.now() - startTime);
            if (progressBar) {
                progressBar.style.animationPlayState = 'paused';
            }
        };

        // Pause on mouse hover
        toast.addEventListener('mouseenter', () => {
            isPaused = true;
            pauseTimer();
        });
        toast.addEventListener('mouseleave', () => {
            if (isPaused) {
                isPaused = false;
                startTimer(Math.max(remainingTime, 1000));
            }
        });

        // Close button click
        const closeBtn = toast.querySelector('.cf-toast-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                closeToast();
            });
        }

        startTimer(duration);

        return {
            element: toast,
            close: closeToast
        };
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

    // Helper to highlight invalid input with shake animation and show toast
    function highlightField(inputElement, message, title) {
        if (!inputElement) return;
        
        // Remove prior error state first
        inputElement.classList.remove('cf-field-error');
        void inputElement.offsetWidth; // Force DOM reflow to re-trigger animation
        inputElement.classList.add('cf-field-error');
        
        try {
            inputElement.focus({ preventScroll: false });
        } catch(e) {
            inputElement.focus();
        }

        if (message) {
            show({
                type: 'warning',
                title: title || 'Required Field Missing',
                message: message
            });
        }

        // Clean up on user interaction
        const clearHandler = function() {
            inputElement.classList.remove('cf-field-error');
            inputElement.removeEventListener('input', clearHandler);
            inputElement.removeEventListener('change', clearHandler);
        };
        inputElement.addEventListener('input', clearHandler);
        inputElement.addEventListener('change', clearHandler);
    }

    function clearAll() {
        const container = document.getElementById('cf-toast-container');
        if (container) {
            container.innerHTML = '';
        }
    }

    const cfToast = {
        show: show,
        error: (msg, title, duration) => show({ type: 'error', message: msg, title: title || 'Error', duration: duration }),
        success: (msg, title, duration) => show({ type: 'success', message: msg, title: title || 'Success', duration: duration }),
        warning: (msg, title, duration) => show({ type: 'warning', message: msg, title: title || 'Attention Required', duration: duration }),
        info: (msg, title, duration) => show({ type: 'info', message: msg, title: title || 'Notice', duration: duration }),
        highlightField: highlightField,
        clearAll: clearAll
    };

    window.cfToast = cfToast;

    // Backward-compatibility alias
    window.displayToast = function(heading, text, icon) {
        let type = 'info';
        if (icon === 'success') type = 'success';
        else if (icon === 'error') type = 'error';
        else if (icon === 'warning') type = 'warning';
        return cfToast.show({
            type: type,
            title: heading,
            message: text
        });
    };

})(window, document);

