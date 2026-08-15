/**
 * DigitalKasur.com - Main JavaScript
 * Version: 2.0.0
 * Complete frontend functionality with dark mode, search, animations,
 * form validation, payment integration helpers, and more.
 */

(function () {
    'use strict';

    // ============================================================
    // UTILITY HELPERS
    // ============================================================

    /**
     * Debounce function - limits how often a function can fire
     */
    function debounce(func, wait) {
        let timeout;
        return function (...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), wait);
        };
    }

    /**
     * Throttle function - limits execution to once per interval
     */
    function throttle(func, limit) {
        let inThrottle;
        return function (...args) {
            if (!inThrottle) {
                func.apply(this, args);
                inThrottle = true;
                setTimeout(() => (inThrottle = false), limit);
            }
        };
    }

    /**
     * Cookie helpers
     */
    const Cookie = {
        get(name) {
            const value = `; ${document.cookie}`;
            const parts = value.split(`; ${name}=`);
            if (parts.length === 2) return parts.pop().split(';').shift();
            return null;
        },
        set(name, value, days = 365) {
            const d = new Date();
            d.setTime(d.getTime() + days * 24 * 60 * 60 * 1000);
            document.cookie = `${name}=${value};expires=${d.toUTCString()};path=/;SameSite=Lax`;
        },
        delete(name) {
            document.cookie = `${name}=;expires=Thu, 01 Jan 1970 00:00:00 UTC;path=/;`;
        }
    };

    /**
     * Safely query a single DOM element
     */
    function $(selector) {
        return document.querySelector(selector);
    }

    /**
     * Safely query multiple DOM elements
     */
    function $$(selector) {
        return document.querySelectorAll(selector);
    }

    /**
     * Check if element is in viewport
     */
    function isInViewport(el) {
        const rect = el.getBoundingClientRect();
        return (
            rect.top < (window.innerHeight || document.documentElement.clientHeight) &&
            rect.bottom > 0 &&
            rect.left < (window.innerWidth || document.documentElement.clientWidth) &&
            rect.right > 0
        );
    }

    // ============================================================
    // 1. DARK MODE TOGGLE
    // ============================================================

    const DarkMode = {
        init() {
            // Read from cookie on load
            const savedTheme = Cookie.get('theme');
            if (savedTheme) {
                document.documentElement.setAttribute('data-theme', savedTheme);
            }
            this.updateIcon();

            // Listen for the theme-toggle link clicks (the ?theme= URL approach
            // already works via the server, but we also support JS toggle)
            const toggleBtns = $$('.theme-toggle, [data-toggle="theme"]');
            toggleBtns.forEach(btn => {
                btn.addEventListener('click', (e) => {
                    // If it's a link with ?theme= query, let it work naturally
                    if (btn.tagName === 'A' && btn.href && btn.href.includes('theme=')) {
                        return; // let the server-side redirect handle it
                    }
                    e.preventDefault();
                    this.toggle();
                });
            });
        },

        toggle() {
            const current = document.documentElement.getAttribute('data-theme') || 'light';
            const next = current === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', next);
            Cookie.set('theme', next, 365);
            this.updateIcon();
        },

        updateIcon() {
            const theme = document.documentElement.getAttribute('data-theme') || 'light';
            // Update any theme-toggle icon elements
            $$('.theme-toggle .fa-moon, .theme-toggle .fa-sun').forEach(icon => {
                if (theme === 'dark') {
                    icon.classList.remove('fa-moon');
                    icon.classList.add('fa-sun');
                } else {
                    icon.classList.remove('fa-sun');
                    icon.classList.add('fa-moon');
                }
            });
        }
    };

    // ============================================================
    // 2. BACK TO TOP BUTTON
    // ============================================================

    const BackToTop = {
        init() {
            this.btn = $('#backToTop');
            if (!this.btn) return;

            window.addEventListener('scroll', throttle(() => this.toggleVisibility(), 200), { passive: true });
            this.btn.addEventListener('click', () => this.scrollToTop());
        },

        toggleVisibility() {
            if (!this.btn) return;
            if (window.scrollY > 300) {
                this.btn.classList.add('visible');
                this.btn.style.opacity = '1';
                this.btn.style.pointerEvents = 'auto';
            } else {
                this.btn.classList.remove('visible');
                this.btn.style.opacity = '0';
                this.btn.style.pointerEvents = 'none';
            }
        },

        scrollToTop() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    };

    // ============================================================
    // 3. SMOOTH SCROLL FOR ANCHOR LINKS
    // ============================================================

    const SmoothScroll = {
        init() {
            document.addEventListener('click', (e) => {
                const link = e.target.closest('a[href^="#"]');
                if (!link) return;
                const targetId = link.getAttribute('href');
                if (targetId === '#' || targetId === '#') return;
                const target = document.querySelector(targetId);
                if (target) {
                    e.preventDefault();
                    const offset = 100; // account for fixed navbar
                    const top = target.getBoundingClientRect().top + window.scrollY - offset;
                    window.scrollTo({ top, behavior: 'smooth' });
                    // Update URL hash without jump
                    history.pushState(null, null, targetId);
                }
            });
        }
    };

    // ============================================================
    // 4. NAVBAR SCROLL EFFECT
    // ============================================================

    const NavbarScroll = {
        init() {
            this.navbar = $('#mainNavbar');
            if (!this.navbar) return;

            window.addEventListener('scroll', throttle(() => this.onScroll(), 100), { passive: true });
            this.onScroll(); // run once on load
        },

        onScroll() {
            if (!this.navbar) return;
            if (window.scrollY > 50) {
                this.navbar.classList.add('scrolled', 'navbar-scrolled');
                this.navbar.style.boxShadow = '0 4px 20px rgba(0,0,0,0.12)';
                this.navbar.style.backgroundColor = 'var(--bg-card)';
            } else {
                this.navbar.classList.remove('scrolled', 'navbar-scrolled');
                this.navbar.style.boxShadow = '';
                this.navbar.style.backgroundColor = '';
            }
        }
    };

    // ============================================================
    // 5. SEARCH WITH AUTO-COMPLETE / SUGGESTIONS
    // ============================================================

    const Search = {
        suggestions: [
            { text: 'Wedding Planners in Kasur', type: 'event', url: 'pages/events.php?type=wedding' },
            { text: 'Website Development', type: 'digital', url: 'pages/digital-services.php?type=website' },
            { text: 'Restaurants in Kasur', type: 'business', url: 'pages/business-directory.php?category=restaurant' },
            { text: 'Jobs in Pattoki', type: 'job', url: 'pages/jobs.php?city=pattoki' },
            { text: 'Kasur Cultural Festival', type: 'event', url: 'pages/events.php' },
            { text: 'Graphic Designing Services', type: 'digital', url: 'pages/digital-services.php?type=design' },
            { text: 'Hotels in Kasur', type: 'business', url: 'pages/business-directory.php?category=hotel' },
            { text: 'Digital Marketing Workshop', type: 'event', url: 'pages/events.php' },
            { text: 'SEO Services', type: 'digital', url: 'pages/digital-services.php?type=seo' },
            { text: 'Social Media Marketing', type: 'digital', url: 'pages/digital-services.php?type=social' },
            { text: 'Schools in Kasur', type: 'business', url: 'pages/business-directory.php?category=school' },
            { text: 'Hospitals in Kasur', type: 'business', url: 'pages/business-directory.php?category=hospital' },
            { text: 'E-Commerce Development', type: 'digital', url: 'pages/digital-services.php?type=ecommerce' },
            { text: 'Pattoki Flower Show', type: 'event', url: 'pages/events.php' },
            { text: 'Real Estate in Kasur', type: 'business', url: 'pages/business-directory.php?category=real-estate' },
            { text: 'Content Writing Services', type: 'digital', url: 'pages/digital-services.php?type=content' },
            { text: 'Birthday Party Planning', type: 'event', url: 'pages/events.php?type=birthday' },
            { text: 'Video Editing Services', type: 'digital', url: 'pages/digital-services.php?type=video' },
            { text: 'Latest News Kasur', type: 'news', url: 'pages/news.php' },
            { text: 'Mobile App Development', type: 'digital', url: 'pages/digital-services.php?type=mobile' },
        ],

        init() {
            this.input = $('#searchInput');
            if (!this.input) return;

            // Create suggestions dropdown
            this.dropdown = document.createElement('div');
            this.dropdown.className = 'search-suggestions-dropdown';
            this.dropdown.style.cssText = 'position:absolute;top:100%;left:0;right:0;background:var(--bg-card);border:1px solid var(--border-color);border-radius:0 0 var(--radius-md) var(--radius-md);box-shadow:var(--shadow-lg);max-height:300px;overflow-y:auto;z-index:1000;display:none;';
            this.input.parentElement.style.position = 'relative';
            this.input.parentElement.appendChild(this.dropdown);

            // Debounced input handler
            this.input.addEventListener('input', debounce(() => this.onInput(), 300));
            this.input.addEventListener('focus', () => this.onInput());

            // Close dropdown on outside click
            document.addEventListener('click', (e) => {
                if (!this.input.parentElement.contains(e.target)) {
                    this.dropdown.style.display = 'none';
                }
            });

            // Search on Enter
            this.input.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    this.performSearch();
                    this.dropdown.style.display = 'none';
                }
                if (e.key === 'Escape') {
                    this.dropdown.style.display = 'none';
                }
            });
        },

        onInput() {
            const query = this.input.value.trim().toLowerCase();
            if (query.length < 2) {
                this.dropdown.style.display = 'none';
                return;
            }

            const matches = this.suggestions.filter(s =>
                s.text.toLowerCase().includes(query)
            ).slice(0, 8);

            if (matches.length === 0) {
                this.dropdown.innerHTML = '<div style="padding:12px 16px;color:var(--text-muted);font-size:0.875rem;">No suggestions found</div>';
            } else {
                this.dropdown.innerHTML = matches.map(s => {
                    const typeIcons = {
                        event: 'fa-calendar-alt text-warning',
                        digital: 'fa-laptop-code text-primary',
                        business: 'fa-store text-success',
                        job: 'fa-briefcase text-info',
                        news: 'fa-newspaper text-danger'
                    };
                    const icon = typeIcons[s.type] || 'fa-search text-muted';
                    return `<a href="${s.url}" class="search-suggestion-item" style="display:flex;align-items:center;gap:10px;padding:10px 16px;color:var(--text-color);text-decoration:none;font-size:0.875rem;transition:background 0.15s ease;" onmouseover="this.style.background='rgba(30,64,175,0.06)'" onmouseout="this.style.background='transparent'">
                        <i class="fas ${icon}" style="width:16px;text-align:center;"></i>
                        <span>${this.highlightMatch(s.text, query)}</span>
                    </a>`;
                }).join('');
            }

            this.dropdown.style.display = 'block';
        },

        highlightMatch(text, query) {
            const regex = new RegExp(`(${query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
            return text.replace(regex, '<strong style="color:var(--primary-color);">$1</strong>');
        },

        performSearch() {
            const query = this.input.value.trim();
            if (query) {
                window.location.href = 'pages/search.php?q=' + encodeURIComponent(query);
            }
        }
    };

    // ============================================================
    // 6. WHATSAPP WIDGET
    // ============================================================

    const WhatsAppWidget = {
        init() {
            this.widget = $('.whatsapp-float');
            if (!this.widget) return;

            // The pulse animation is handled by the <span class="whatsapp-pulse"> 
            // element inside the widget (see footer.php). We only toggle its visibility.
            const pulseEl = this.widget.querySelector('.whatsapp-pulse');
            
            if (pulseEl) {
                // Pause pulse on hover for cleaner interaction
                this.widget.addEventListener('mouseenter', () => {
                    pulseEl.style.animationPlayState = 'paused';
                });

                this.widget.addEventListener('mouseleave', () => {
                    pulseEl.style.animationPlayState = 'running';
                });
            }
        }
    };

    // ============================================================
    // 7. COUNTER ANIMATION
    // ============================================================

    const CounterAnimation = {
        init() {
            this.counters = $$('.stat-number, .hero-stat-number, [data-counter]');
            if (this.counters.length === 0) return;

            this.animated = new Set();

            // Use IntersectionObserver for efficient viewport detection
            if ('IntersectionObserver' in window) {
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting && !this.animated.has(entry.target)) {
                            this.animated.add(entry.target);
                            this.animateCounter(entry.target);
                            observer.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.3 });

                this.counters.forEach(counter => observer.observe(counter));
            } else {
                // Fallback: animate counters that are already in viewport
                this.counters.forEach(counter => this.animateCounter(counter));
            }
        },

        animateCounter(el) {
            const target = parseInt(el.getAttribute('data-counter') || el.textContent.replace(/[^\d]/g, ''), 10);
            if (isNaN(target) || target === 0) return;

            const suffix = el.textContent.replace(/[\d,]/g, '').trim();
            const duration = 2000;
            const startTime = performance.now();

            const step = (currentTime) => {
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / duration, 1);
                // Ease-out cubic
                const eased = 1 - Math.pow(1 - progress, 3);
                const current = Math.floor(eased * target);
                el.textContent = current.toLocaleString() + (suffix ? ' ' + suffix : '');

                if (progress < 1) {
                    requestAnimationFrame(step);
                } else {
                    el.textContent = target.toLocaleString() + (suffix ? ' ' + suffix : '');
                }
            };

            requestAnimationFrame(step);
        }
    };

    // ============================================================
    // 8. IMAGE LAZY LOADING
    // ============================================================

    const LazyLoad = {
        init() {
            const lazyImages = $$('img[data-src], img[loading="lazy"]');
            if (lazyImages.length === 0) return;

            if ('IntersectionObserver' in window) {
                const imageObserver = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const img = entry.target;
                            if (img.dataset.src) {
                                img.src = img.dataset.src;
                                img.removeAttribute('data-src');
                            }
                            img.classList.add('loaded');
                            imageObserver.unobserve(img);
                        }
                    });
                }, {
                    rootMargin: '100px 0px',
                    threshold: 0.01
                });

                lazyImages.forEach(img => imageObserver.observe(img));
            } else {
                // Fallback: load all images immediately
                lazyImages.forEach(img => {
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                        img.removeAttribute('data-src');
                    }
                });
            }
        }
    };

    // ============================================================
    // 9. FORM VALIDATION
    // ============================================================

    const FormValidation = {
        init() {
            const forms = $$('form[data-validate], form.needs-validation');
            forms.forEach(form => {
                form.setAttribute('novalidate', '');

                // Real-time validation on input
                form.addEventListener('input', (e) => {
                    this.validateField(e.target);
                });

                // Validate on submit
                form.addEventListener('submit', (e) => {
                    if (!this.validateForm(form)) {
                        e.preventDefault();
                        e.stopPropagation();
                    }
                    form.classList.add('was-validated');
                });
            });

            // Also handle forms that just have required fields but no data-validate
            $$('form').forEach(form => {
                if (form.hasAttribute('data-validate') || form.classList.contains('needs-validation')) return;
                form.addEventListener('submit', () => {
                    // Remove invalid-feedback elements that may have been auto-added
                });
            });
        },

        validateForm(form) {
            let isValid = true;
            const fields = form.querySelectorAll('input[required], textarea[required], select[required]');
            fields.forEach(field => {
                if (!this.validateField(field)) {
                    isValid = false;
                }
            });

            // Check email fields specifically
            form.querySelectorAll('input[type="email"]').forEach(field => {
                if (field.value && !this.isValidEmail(field.value)) {
                    this.showError(field, 'Please enter a valid email address');
                    isValid = false;
                }
            });

            // Check phone fields
            form.querySelectorAll('input[type="tel"], input[name="phone"], input[name="whatsapp"]').forEach(field => {
                if (field.value && !this.isValidPhone(field.value)) {
                    this.showError(field, 'Please enter a valid phone number');
                    isValid = false;
                }
            });

            return isValid;
        },

        validateField(field) {
            // Remove previous error
            this.clearError(field);

            // Skip non-required empty fields
            if (!field.hasAttribute('required') && !field.value.trim()) return true;

            // Required check
            if (field.hasAttribute('required') && !field.value.trim()) {
                this.showError(field, 'This field is required');
                return false;
            }

            // Type-specific checks
            if (field.type === 'email' && field.value && !this.isValidEmail(field.value)) {
                this.showError(field, 'Please enter a valid email address');
                return false;
            }

            if ((field.type === 'tel' || field.name === 'phone' || field.name === 'whatsapp') && field.value && !this.isValidPhone(field.value)) {
                this.showError(field, 'Please enter a valid phone number');
                return false;
            }

            if (field.type === 'url' && field.value && !this.isValidURL(field.value)) {
                this.showError(field, 'Please enter a valid URL');
                return false;
            }

            // Min/Max length
            const minLength = field.getAttribute('minlength');
            const maxLength = field.getAttribute('maxlength');
            if (minLength && field.value.length < parseInt(minLength, 10)) {
                this.showError(field, `Minimum ${minLength} characters required`);
                return false;
            }
            if (maxLength && field.value.length > parseInt(maxLength, 10)) {
                this.showError(field, `Maximum ${maxLength} characters allowed`);
                return false;
            }

            // Pattern match
            const pattern = field.getAttribute('pattern');
            if (pattern && field.value && !new RegExp(pattern).test(field.value)) {
                this.showError(field, field.getAttribute('title') || 'Invalid format');
                return false;
            }

            field.classList.add('is-valid');
            field.classList.remove('is-invalid');
            return true;
        },

        isValidEmail(email) {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
        },

        isValidPhone(phone) {
            const cleaned = phone.replace(/[\s\-\(\)\+]/g, '');
            return /^\d{10,15}$/.test(cleaned);
        },

        isValidURL(url) {
            try {
                new URL(url);
                return true;
            } catch {
                return false;
            }
        },

        showError(field, message) {
            field.classList.add('is-invalid');
            field.classList.remove('is-valid');

            // Remove existing error message
            const existing = field.parentElement.querySelector('.invalid-feedback');
            if (existing) existing.remove();

            // Add error message
            const errorDiv = document.createElement('div');
            errorDiv.className = 'invalid-feedback';
            errorDiv.textContent = message;
            field.parentElement.appendChild(errorDiv);
        },

        clearError(field) {
            field.classList.remove('is-invalid');
            const existing = field.parentElement.querySelector('.invalid-feedback');
            if (existing) existing.remove();
        }
    };

    // ============================================================
    // 10. ALERT AUTO-DISMISS
    // ============================================================

    const AlertDismiss = {
        init() {
            const alerts = $$('.alert:not(.alert-permanent)');
            alerts.forEach(alert => {
                // Auto-dismiss after 5 seconds
                setTimeout(() => {
                    if (alert && alert.parentElement) {
                        alert.style.transition = 'opacity 0.4s ease, transform 0.4s ease, max-height 0.4s ease';
                        alert.style.opacity = '0';
                        alert.style.transform = 'translateY(-10px)';
                        alert.style.maxHeight = alert.scrollHeight + 'px';
                        setTimeout(() => {
                            alert.style.maxHeight = '0';
                            alert.style.padding = '0';
                            alert.style.margin = '0';
                            alert.style.overflow = 'hidden';
                        }, 300);
                        setTimeout(() => {
                            if (alert.parentElement) {
                                alert.remove();
                            }
                        }, 800);
                    }
                }, 5000);
            });
        }
    };

    // ============================================================
    // 11. DROPDOWN ON HOVER (DESKTOP)
    // ============================================================

    const DropdownHover = {
        init() {
            // Only enable hover on desktop (>= 992px)
            if (window.innerWidth < 992) return;

            const dropdowns = $$('.navbar .dropdown, .nav-item.dropdown');
            dropdowns.forEach(dropdown => {
                const toggle = dropdown.querySelector('.dropdown-toggle');
                const menu = dropdown.querySelector('.dropdown-menu');
                if (!toggle || !menu) return;

                let timeout;

                dropdown.addEventListener('mouseenter', () => {
                    clearTimeout(timeout);
                    menu.classList.add('show');
                    toggle.setAttribute('aria-expanded', 'true');
                });

                dropdown.addEventListener('mouseleave', () => {
                    timeout = setTimeout(() => {
                        menu.classList.remove('show');
                        toggle.setAttribute('aria-expanded', 'false');
                    }, 150);
                });
            });
        }
    };

    // ============================================================
    // 12. TOOLTIP INITIALIZATION
    // ============================================================

    const Tooltips = {
        init() {
            // Initialize Bootstrap tooltips
            if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
                $$('[data-bs-toggle="tooltip"]').forEach(el => {
                    new bootstrap.Tooltip(el, {
                        trigger: 'hover',
                        delay: { show: 200, hide: 100 }
                    });
                });
            }
        }
    };

    // ============================================================
    // 13. LOADING SPINNER
    // ============================================================

    const LoadingSpinner = {
        spinnerEl: null,

        init() {
            // Create global spinner element
            this.spinnerEl = document.createElement('div');
            this.spinnerEl.id = 'globalSpinner';
            this.spinnerEl.className = 'loading-spinner-overlay';
            this.spinnerEl.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,0.3);display:none;align-items:center;justify-content:center;z-index:9999;';
            this.spinnerEl.innerHTML = `
                <div style="background:var(--bg-card);padding:2rem 2.5rem;border-radius:var(--radius-lg);box-shadow:var(--shadow-xl);text-align:center;">
                    <div class="spinner-border text-primary" role="status" style="width:3rem;height:3rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p style="margin-top:1rem;margin-bottom:0;color:var(--text-muted);font-size:0.875rem;" class="spinner-message">Please wait...</p>
                </div>`;
            document.body.appendChild(this.spinnerEl);

            // Show spinner for AJAX requests if using jQuery or fetch
            this.setupAjaxSpinner();
        },

        show(message) {
            if (this.spinnerEl) {
                const msgEl = this.spinnerEl.querySelector('.spinner-message');
                if (msgEl && message) msgEl.textContent = message;
                this.spinnerEl.style.display = 'flex';
            }
        },

        hide() {
            if (this.spinnerEl) {
                this.spinnerEl.style.display = 'none';
            }
        },

        setupAjaxSpinner() {
            // For fetch-based AJAX
            const originalFetch = window.fetch;
            window.fetch = function (...args) {
                LoadingSpinner.show();
                return originalFetch.apply(this, args)
                    .finally(() => LoadingSpinner.hide());
            };

            // For jQuery AJAX (if loaded)
            if (typeof $ !== 'undefined' && typeof $.ajaxSetup === 'function') {
                $(document).ajaxStart(() => LoadingSpinner.show());
                $(document).ajaxStop(() => LoadingSpinner.hide());
            }
        }
    };

    // ============================================================
    // 14. COPY TO CLIPBOARD
    // ============================================================

    const CopyToClipboard = {
        init() {
            $$('[data-copy]').forEach(el => {
                el.style.cursor = 'pointer';
                el.addEventListener('click', () => {
                    const text = el.getAttribute('data-copy') || el.textContent.trim();
                    this.copy(text, el);
                });
            });

            // Also add click-to-copy on phone and email links in contact info
            $$('.footer-contact a[href^="tel:"], .top-bar a[href^="tel:"]').forEach(el => {
                el.setAttribute('title', 'Click to copy');
                el.addEventListener('dblclick', (e) => {
                    e.preventDefault();
                    const phone = el.textContent.trim();
                    this.copy(phone, el);
                });
            });
        },

        async copy(text, triggerEl) {
            try {
                await navigator.clipboard.writeText(text);
                this.showTooltip(triggerEl, 'Copied!');
            } catch {
                // Fallback
                const textarea = document.createElement('textarea');
                textarea.value = text;
                textarea.style.position = 'fixed';
                textarea.style.opacity = '0';
                document.body.appendChild(textarea);
                textarea.select();
                document.execCommand('copy');
                document.body.removeChild(textarea);
                this.showTooltip(triggerEl, 'Copied!');
            }
        },

        showTooltip(el, message) {
            const original = el.getAttribute('title') || '';
            el.setAttribute('title', message);

            if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
                const tooltip = bootstrap.Tooltip.getInstance(el) || new bootstrap.Tooltip(el);
                tooltip.setContent({ '.tooltip-inner': message });
                tooltip.show();
                setTimeout(() => {
                    tooltip.hide();
                    el.setAttribute('title', original);
                }, 1500);
            } else {
                // Simple fallback tooltip
                const tip = document.createElement('span');
                tip.textContent = message;
                tip.style.cssText = 'position:absolute;background:#333;color:#fff;padding:4px 10px;border-radius:4px;font-size:12px;z-index:9999;top:-30px;left:50%;transform:translateX(-50%);white-space:nowrap;';
                el.style.position = el.style.position || 'relative';
                el.appendChild(tip);
                setTimeout(() => tip.remove(), 1500);
            }
        }
    };

    // ============================================================
    // 15. PRINT PAGE
    // ============================================================

    const PrintPage = {
        init() {
            $$('[data-print]').forEach(btn => {
                btn.addEventListener('click', () => this.print());
            });

            // Add keyboard shortcut Ctrl+P enhancement
            document.addEventListener('keydown', (e) => {
                if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
                    // Let the browser handle it, just add print-friendly class
                    document.body.classList.add('printing');
                    setTimeout(() => document.body.classList.remove('printing'), 1000);
                }
            });
        },

        print() {
            document.body.classList.add('printing');
            window.print();
            setTimeout(() => document.body.classList.remove('printing'), 500);
        }
    };

    // ============================================================
    // 16. COOKIE CONSENT
    // ============================================================

    const CookieConsent = {
        init() {
            // Don't show if already accepted
            if (Cookie.get('cookie_consent')) return;

            const banner = document.createElement('div');
            banner.id = 'cookieConsent';
            banner.style.cssText = 'position:fixed;bottom:0;left:0;right:0;background:var(--gray-800);color:var(--white);padding:16px 24px;z-index:9998;box-shadow:0 -4px 20px rgba(0,0,0,0.2);display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;';
            banner.innerHTML = `
                <div style="flex:1;min-width:280px;">
                    <p style="margin:0;font-size:0.875rem;line-height:1.5;">
                        <i class="fas fa-cookie-bite me-2" style="color:var(--secondary-color);"></i>
                        We use cookies to enhance your experience. By continuing to visit this site you agree to our use of cookies.
                        <a href="pages/privacy.php" style="color:var(--secondary-color);text-decoration:underline;">Learn more</a>
                    </p>
                </div>
                <div style="display:flex;gap:8px;">
                    <button id="cookieAccept" style="background:var(--primary-color);color:#fff;border:none;padding:8px 20px;border-radius:4px;font-size:0.875rem;font-weight:600;cursor:pointer;">Accept</button>
                    <button id="cookieDecline" style="background:transparent;color:var(--gray-300);border:1px solid var(--gray-600);padding:8px 20px;border-radius:4px;font-size:0.875rem;cursor:pointer;">Decline</button>
                </div>
            `;
            document.body.appendChild(banner);

            // Accept handler
            banner.querySelector('#cookieAccept').addEventListener('click', () => {
                Cookie.set('cookie_consent', 'accepted', 365);
                this.hideBanner(banner);
            });

            // Decline handler
            banner.querySelector('#cookieDecline').addEventListener('click', () => {
                Cookie.set('cookie_consent', 'declined', 30);
                this.hideBanner(banner);
            });
        },

        hideBanner(banner) {
            banner.style.transition = 'transform 0.4s ease, opacity 0.4s ease';
            banner.style.transform = 'translateY(100%)';
            banner.style.opacity = '0';
            setTimeout(() => banner.remove(), 500);
        }
    };

    // ============================================================
    // 17. SEARCH SUGGESTIONS (Enhanced with debounced input)
    // ============================================================
    // Already handled in the Search module above (Section 5)

    // ============================================================
    // 18. MOBILE MENU
    // ============================================================

    const MobileMenu = {
        init() {
            const navCollapse = $('#mainNav');
            if (!navCollapse) return;

            // Close mobile menu on link click
            navCollapse.querySelectorAll('.nav-link:not(.dropdown-toggle)').forEach(link => {
                link.addEventListener('click', () => {
                    if (window.innerWidth < 992) {
                        const bsCollapse = bootstrap.Collapse.getInstance(navCollapse);
                        if (bsCollapse) bsCollapse.hide();
                    }
                });
            });

            // Set active state for current page
            const currentPath = window.location.pathname;
            navCollapse.querySelectorAll('.nav-link').forEach(link => {
                const href = link.getAttribute('href');
                if (href && (currentPath.endsWith(href) || (href.includes(currentPath.split('/').pop()) && currentPath.split('/').pop() !== ''))) {
                    link.classList.add('active');
                }
            });

            // Close on outside click
            document.addEventListener('click', (e) => {
                if (window.innerWidth < 992 && navCollapse.classList.contains('show')) {
                    const navbar = $('#mainNavbar');
                    if (navbar && !navbar.contains(e.target)) {
                        const bsCollapse = bootstrap.Collapse.getInstance(navCollapse);
                        if (bsCollapse) bsCollapse.hide();
                    }
                }
            });
        }
    };

    // ============================================================
    // 19. CARD ANIMATIONS (IntersectionObserver fade-in)
    // ============================================================

    const CardAnimations = {
        init() {
            const animatedElements = $$('.card, .service-card, .business-card, .job-card, .city-card, [data-animate]');
            if (animatedElements.length === 0) return;

            // Set initial state
            animatedElements.forEach(el => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(20px)';
                el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            });

            if ('IntersectionObserver' in window) {
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach((entry, index) => {
                        if (entry.isIntersecting) {
                            // Stagger the animation
                            setTimeout(() => {
                                entry.target.style.opacity = '1';
                                entry.target.style.transform = 'translateY(0)';
                            }, index * 80);
                            observer.unobserve(entry.target);
                        }
                    });
                }, {
                    threshold: 0.1,
                    rootMargin: '0px 0px -50px 0px'
                });

                animatedElements.forEach(el => observer.observe(el));
            } else {
                // Fallback: show all immediately
                animatedElements.forEach(el => {
                    el.style.opacity = '1';
                    el.style.transform = 'translateY(0)';
                });
            }
        }
    };

    // ============================================================
    // 20. EVENT REGISTRATION (AJAX form submission)
    // ============================================================

    const EventRegistration = {
        init() {
            const form = $('#eventRegistrationForm');
            if (!form) return;

            form.addEventListener('submit', (e) => {
                e.preventDefault();

                if (!FormValidation.validateForm(form)) return;

                const submitBtn = form.querySelector('[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Registering...';

                const formData = new FormData(form);

                fetch(form.action || 'pages/register-event.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.showNotification('success', data.message || 'Registration successful!');
                        form.reset();
                        form.classList.remove('was-validated');
                    } else {
                        this.showNotification('error', data.message || 'Registration failed. Please try again.');
                    }
                })
                .catch(error => {
                    this.showNotification('error', 'Something went wrong. Please try again.');
                    console.error('Event Registration Error:', error);
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                });
            });
        },

        showNotification(type, message) {
            const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
            const icon = type === 'success' ? 'check-circle' : 'exclamation-circle';
            const alert = document.createElement('div');
            alert.className = `alert ${alertClass} alert-dismissible fade show`;
            alert.setAttribute('role', 'alert');
            alert.innerHTML = `<i class="fas fa-${icon} me-2"></i>${message}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;

            // Insert at top of content
            const container = $('.container') || document.body;
            container.insertBefore(alert, container.firstChild);

            // Auto-dismiss
            setTimeout(() => {
                if (alert.parentElement) {
                    alert.classList.remove('show');
                    setTimeout(() => alert.remove(), 300);
                }
            }, 5000);
        }
    };

    // ============================================================
    // 21. NEWSLETTER (AJAX form submission)
    // ============================================================

    const Newsletter = {
        init() {
            const form = $('.newsletter-form');
            if (!form) return;

            form.addEventListener('submit', (e) => {
                e.preventDefault();

                const emailInput = form.querySelector('input[name="email"]');
                const email = emailInput ? emailInput.value.trim() : '';

                if (!email || !FormValidation.isValidEmail(email)) {
                    if (emailInput) {
                        emailInput.classList.add('is-invalid');
                    }
                    return;
                }

                if (emailInput) {
                    emailInput.classList.remove('is-invalid');
                    emailInput.classList.add('is-valid');
                }

                const submitBtn = form.querySelector('[type="submit"]');
                const originalHTML = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

                const formData = new FormData(form);

                fetch(form.action || 'newsletter.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        EventRegistration.showNotification('success', data.message || 'Subscribed successfully!');
                        form.reset();
                    } else {
                        EventRegistration.showNotification('error', data.message || 'Subscription failed.');
                    }
                })
                .catch(() => {
                    // If the server returns a redirect (non-JSON), just submit the form normally
                    form.submit();
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalHTML;
                });
            });
        }
    };

    // ============================================================
    // ADDITIONAL: External link handler
    // ============================================================

    function handleExternalLinks() {
        $$('a[target="_blank"]').forEach(link => {
            if (!link.getAttribute('rel')) {
                link.setAttribute('rel', 'noopener noreferrer');
            }
        });
    }

    // ============================================================
    // ADDITIONAL: Scroll-based animations for sections
    // ============================================================

    function initScrollReveal() {
        const revealSections = $$('section, .stats-section, .services-grid');
        if (revealSections.length === 0) return;

        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('revealed');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.05 });

            revealSections.forEach(section => {
                section.classList.add('scroll-reveal');
                observer.observe(section);
            });
        }
    }

    // ============================================================
    // ADDITIONAL: WhatsApp pulse CSS injection
    // ============================================================

    function injectDynamicStyles() {
        const style = document.createElement('style');
        style.textContent = `
            /* WhatsApp pulse animation - subtle */
            .whatsapp-pulse {
                position: absolute;
                inset: 0;
                border-radius: 50%;
                border: 2px solid #25D366;
                animation: whatsappPulse 2s ease-out infinite;
                pointer-events: none;
            }
            @keyframes whatsappPulse {
                0% { transform: scale(1); opacity: 0.6; }
                100% { transform: scale(1.2); opacity: 0; }
            }

            /* Back to top visibility */
            #backToTop {
                opacity: 0;
                pointer-events: none;
                transition: opacity 0.3s ease, transform 0.3s ease;
            }
            #backToTop.visible {
                opacity: 1;
                pointer-events: auto;
            }
            #backToTop:hover {
                transform: translateY(-3px);
            }

            /* Print styles */
            @media print {
                .top-bar, .navbar, .whatsapp-float, #backToTop,
                .site-footer, .cookie-consent, .search-box,
                .btn, .no-print { display: none !important; }
                body { background: #fff !important; color: #000 !important; }
                .card { box-shadow: none !important; border: 1px solid #ddd !important; break-inside: avoid; }
                a { color: #000 !important; text-decoration: underline !important; }
            }

            /* Scroll reveal */
            .scroll-reveal { opacity: 0; transform: translateY(30px); transition: opacity 0.8s ease, transform 0.8s ease; }
            .scroll-reveal.revealed { opacity: 1; transform: translateY(0); }

            /* Search suggestions */
            .search-suggestions-dropdown::-webkit-scrollbar { width: 6px; }
            .search-suggestions-dropdown::-webkit-scrollbar-thumb { background: var(--gray-300); border-radius: 3px; }
            [data-theme="dark"] .search-suggestions-dropdown::-webkit-scrollbar-thumb { background: var(--gray-600); }

            /* Loading spinner */
            .loading-spinner-overlay { backdrop-filter: blur(2px); }
        `;
        document.head.appendChild(style);
    }

    // ============================================================
    // INITIALIZE ALL MODULES
    // ============================================================

    function init() {
        injectDynamicStyles();
        DarkMode.init();
        BackToTop.init();
        SmoothScroll.init();
        NavbarScroll.init();
        Search.init();
        WhatsAppWidget.init();
        CounterAnimation.init();
        LazyLoad.init();
        FormValidation.init();
        AlertDismiss.init();
        DropdownHover.init();
        Tooltips.init();
        LoadingSpinner.init();
        CopyToClipboard.init();
        PrintPage.init();
        CookieConsent.init();
        MobileMenu.init();
        CardAnimations.init();
        EventRegistration.init();
        Newsletter.init();
        handleExternalLinks();
        initScrollReveal();
    }

    // Run on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Expose public API for use in other scripts
    window.DK = {
        Cookie,
        DarkMode,
        BackToTop,
        Search,
        LoadingSpinner,
        FormValidation,
        CopyToClipboard,
        EventRegistration,
        Newsletter,
        debounce,
        throttle,
        $,
        $$
    };

})();

// ============================================================
// AI CHATBOT - Standalone (outside IIFE for template access)
// ============================================================
(function() {
    'use strict';

    const chatbotBtn = document.getElementById('aiChatbotBtn');
    const chatbotPopup = document.getElementById('aiChatbotPopup');
    const chatbotClose = document.getElementById('chatbotCloseBtn');
    const chatbotInput = document.getElementById('chatbotInput');
    const chatbotSendBtn = document.getElementById('chatbotSendBtn');
    const chatbotAnswers = document.getElementById('chatbotAnswers');

    if (!chatbotBtn || !chatbotPopup) return;

    // Pre-defined Q&A database about DigitalKasur
    const qaDatabase = {
        'kasur district ki population kya hai': 'Kasur District ki population taqreeban 35 lakh (3.5 million) hai. Yeh Punjab ka 16th largest district hai aur Kasur city iska headquarters hai.',
        'digitalkasur kon kon si services faraham karta hai': 'DigitalKasur yeh services faraham karta hai:\n• Event Management (Wedding, Birthday, Corporate, Concert)\n• Digital Services (Website, E-Commerce, SEO, Social Media)\n• Business Directory (Local businesses ki listing)\n• Jobs Portal (Kasur mein jobs)\n• News Portal (Latest Kasur news)\n• Blog (Tips aur guides)',
        'kasur mein kon kon se shehar hain': 'Kasur District ke main shehar:\n• Kasur (District HQ)\n• Pattoki (City of Flowers)\n• Phool Nagar (Flower Market)\n• Kot Radha Kishan (Tehsil Town)\n• Chunian (Tehsil - Agricultural Hub)\n• Theng More (Ellah Abad)',
        'event management ki charges kya hain': 'Event management ki charges event ki type aur size pe depend karti hain. Wedding events ki shuruati price PKR 50,000 se hai, birthday parties PKR 15,000 se, aur corporate events PKR 30,000 se. Detail ke liye hum se contact karein ya WhatsApp pe message karein!',
        'website banwani hai kasur mein kaise shuru karoon': 'Website banwane ke liye:\n1. Hum se contact karein (WhatsApp ya Phone)\n2. Apni requirements batayein\n3. Hum quote denge (starts from PKR 25,000)\n4. Design approval ke baad development shuru\n5. 7-15 din mein ready!\nAbhi WhatsApp pe message karein aur discussion shuru karein!',
        'whatsapp pe contact kaise karein': 'WhatsApp pe contact karne ke liye neeche green WhatsApp button pe click karein. Humara number +92-333-3197977 hai. Direct link: wa.me/923333197977',
        'business directory mein kaise list karoon': 'Apni business list karne ke liye:\n1. "Add Your Business" button pe click karein\n2. Apna account banayein (free!)\n3. Business details fill karein\n4. Submit karein\nAapki business Kasur District bhar ke logon tak pohanche gi!',
        'jobs kaise dhundhein kasur mein': 'Kasur mein jobs dhundhne ke liye:\n1. Jobs page pe jayein\n2. City ya type se filter lagayein\n3. Job details dekhein\n4. "Apply Now" pe click karein\nYa phir apna CV WhatsApp pe bhej dein, hum aapko suitable jobs batayein ge!'
    };

    // Toggle chatbot popup
    chatbotBtn.addEventListener('click', function(e) {
        e.preventDefault();
        const isVisible = chatbotPopup.style.display !== 'none';
        chatbotPopup.style.display = isVisible ? 'none' : 'flex';
    });

    chatbotClose.addEventListener('click', function() {
        chatbotPopup.style.display = 'none';
    });

    // Handle suggestion chips
    document.querySelectorAll('.suggestion-chip').forEach(function(chip) {
        chip.addEventListener('click', function() {
            const question = this.getAttribute('data-question');
            addUserMessage(question);
            findAndShowAnswer(question);
        });
    });

    // Handle send button
    chatbotSendBtn.addEventListener('click', function() {
        sendMessage();
    });

    chatbotInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') sendMessage();
    });

    function sendMessage() {
        const text = chatbotInput.value.trim();
        if (!text) return;
        addUserMessage(text);
        findAndShowAnswer(text);
        chatbotInput.value = '';
    }

    function addUserMessage(text) {
        const div = document.createElement('div');
        div.className = 'chatbot-message user';
        div.innerHTML = '<p>' + escapeHtml(text) + '</p>';
        chatbotAnswers.appendChild(div);
        scrollChatToBottom();
    }

    function addBotMessage(text) {
        const div = document.createElement('div');
        div.className = 'chatbot-message bot';
        div.innerHTML = '<p>' + text.replace(/\n/g, '<br>') + '</p>';
        chatbotAnswers.appendChild(div);
        scrollChatToBottom();
    }

    function findAndShowAnswer(question) {
        // Show typing indicator briefly
        const typingDiv = document.createElement('div');
        typingDiv.className = 'chatbot-message bot';
        typingDiv.innerHTML = '<p><i class="fas fa-circle-notch fa-spin me-1"></i> Soch raha hoon...</p>';
        chatbotAnswers.appendChild(typingDiv);
        scrollChatToBottom();

        setTimeout(function() {
            chatbotAnswers.removeChild(typingDiv);

            const qLower = question.toLowerCase().trim();
            let bestMatch = null;
            let bestScore = 0;

            // Find best matching answer
            for (const [key, value] of Object.entries(qaDatabase)) {
                const keyWords = key.split(' ');
                let matchCount = 0;
                keyWords.forEach(function(word) {
                    if (word.length > 2 && qLower.includes(word)) matchCount++;
                });
                const score = matchCount / keyWords.length;
                if (score > bestScore) {
                    bestScore = score;
                    bestMatch = value;
                }
            }

            if (bestScore > 0.3 && bestMatch) {
                addBotMessage(bestMatch);
            } else {
                addBotMessage('Shukriya aapke sawal ke liye! Main is sawal ka jawab abhi nahi de sakta, lekin aap hum se WhatsApp pe baat kar sakte hain aur detail mein discuss kar sakte hain. Neeche WhatsApp button pe click karein! 📱');
            }
        }, 800);
    }

    function scrollChatToBottom() {
        const body = chatbotPopup.querySelector('.chatbot-popup-body');
        if (body) body.scrollTop = body.scrollHeight;
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
})();
