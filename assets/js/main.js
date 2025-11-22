/**
 * Main Theme JavaScript
 *
 * @package AdPrint_Blog
 * @since 1.0.0
 */

(function() {
    'use strict';

    document.addEventListener('DOMContentLoaded', function() {
        // Mobile menu toggle
        initMobileMenu();

        // Smooth scroll for anchor links
        initSmoothScroll();

        // Back to top button
        initBackToTop();

        // Accessibility: Focus management
        initFocusManagement();

        // FAQ Accordion
        initFAQAccordion();

        // Lazy loading fallback for older browsers
        initLazyLoadFallback();

        // Stats counter animation
        initStatsCounter();

        // External links security
        initExternalLinks();
    });

    /**
     * Mobile Menu Toggle
     */
    function initMobileMenu() {
        const menuToggle = document.getElementById('mobile-menu-toggle');
        const mobileMenu = document.getElementById('mobile-menu');

        if (!menuToggle || !mobileMenu) return;

        menuToggle.addEventListener('click', function() {
            const isExpanded = menuToggle.getAttribute('aria-expanded') === 'true';

            menuToggle.setAttribute('aria-expanded', !isExpanded);
            mobileMenu.classList.toggle('hidden');

            // Toggle icon
            const openIcon = menuToggle.querySelector('.menu-open');
            const closeIcon = menuToggle.querySelector('.menu-close');

            if (openIcon && closeIcon) {
                openIcon.classList.toggle('hidden');
                closeIcon.classList.toggle('hidden');
            }

            // Trap focus in mobile menu
            if (!isExpanded) {
                trapFocus(mobileMenu);
            }
        });

        // Close menu on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const isExpanded = menuToggle.getAttribute('aria-expanded') === 'true';
                if (isExpanded) {
                    menuToggle.click();
                }
            }
        });
    }

    /**
     * Smooth Scroll for Anchor Links
     */
    function initSmoothScroll() {
        const anchorLinks = document.querySelectorAll('a[href^="#"]');

        anchorLinks.forEach(function(link) {
            link.addEventListener('click', function(e) {
                const targetId = this.getAttribute('href');

                if (targetId === '#') return;

                const target = document.querySelector(targetId);

                if (target) {
                    e.preventDefault();

                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start',
                    });

                    // Update URL without jumping
                    if (history.pushState) {
                        history.pushState(null, null, targetId);
                    }

                    // Focus target for accessibility
                    target.focus({ preventScroll: true });

                    // Add tabindex if element is not focusable
                    if (!target.hasAttribute('tabindex')) {
                        target.setAttribute('tabindex', '-1');
                    }
                }
            });
        });
    }

    /**
     * Back to Top Button
     */
    function initBackToTop() {
        const backToTopButton = document.getElementById('back-to-top');

        if (!backToTopButton) {
            // Create button if it doesn't exist
            createBackToTopButton();
            return;
        }

        // Show/hide button based on scroll position
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                backToTopButton.classList.remove('hidden');
                backToTopButton.classList.add('flex');
            } else {
                backToTopButton.classList.add('hidden');
                backToTopButton.classList.remove('flex');
            }
        });

        backToTopButton.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth',
            });
        });
    }

    /**
     * Create Back to Top Button
     */
    function createBackToTopButton() {
        const button = document.createElement('button');
        button.id = 'back-to-top';
        button.className = 'fixed bottom-8 right-8 z-50 hidden items-center justify-center w-12 h-12 bg-primary-600 hover:bg-primary-700 text-white rounded-full shadow-lg transition-all focus:outline-none focus:ring-4 focus:ring-primary-100 dark:focus:ring-primary-900';
        button.setAttribute('aria-label', 'Back to top');

        button.innerHTML = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>';

        document.body.appendChild(button);

        initBackToTop();
    }

    /**
     * Focus Management for Accessibility
     */
    function initFocusManagement() {
        // Add focus visible class for keyboard navigation
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Tab') {
                document.body.classList.add('using-keyboard');
            }
        });

        document.addEventListener('mousedown', function() {
            document.body.classList.remove('using-keyboard');
        });

        // Skip to main content link
        const skipLink = document.querySelector('.skip-link');
        if (skipLink) {
            skipLink.addEventListener('click', function(e) {
                e.preventDefault();
                const main = document.getElementById('main-content');
                if (main) {
                    main.setAttribute('tabindex', '-1');
                    main.focus();
                }
            });
        }
    }

    /**
     * Trap Focus in Element
     */
    function trapFocus(element) {
        const focusableElements = element.querySelectorAll('a[href], button:not([disabled]), textarea, input, select');
        const firstFocusable = focusableElements[0];
        const lastFocusable = focusableElements[focusableElements.length - 1];

        element.addEventListener('keydown', function(e) {
            if (e.key === 'Tab') {
                if (e.shiftKey) {
                    if (document.activeElement === firstFocusable) {
                        e.preventDefault();
                        lastFocusable.focus();
                    }
                } else {
                    if (document.activeElement === lastFocusable) {
                        e.preventDefault();
                        firstFocusable.focus();
                    }
                }
            }
        });
    }

    /**
     * FAQ Accordion
     */
    function initFAQAccordion() {
        const faqItems = document.querySelectorAll('.faq-question');

        faqItems.forEach(function(question) {
            question.addEventListener('click', function() {
                const answer = this.nextElementSibling;
                const isExpanded = this.getAttribute('aria-expanded') === 'true';

                // Close all other FAQs (optional)
                // faqItems.forEach(function(item) {
                //     if (item !== question) {
                //         item.setAttribute('aria-expanded', 'false');
                //         item.nextElementSibling.classList.add('hidden');
                //     }
                // });

                // Toggle current FAQ
                this.setAttribute('aria-expanded', !isExpanded);
                answer.classList.toggle('hidden');

                // Rotate icon
                const icon = this.querySelector('svg');
                if (icon) {
                    icon.style.transform = isExpanded ? 'rotate(0deg)' : 'rotate(180deg)';
                }
            });
        });
    }

    /**
     * Lazy Loading Fallback for Older Browsers
     */
    function initLazyLoadFallback() {
        if ('loading' in HTMLImageElement.prototype) {
            return; // Native lazy loading is supported
        }

        // Use Intersection Observer as fallback
        if ('IntersectionObserver' in window) {
            const lazyImages = document.querySelectorAll('img[loading="lazy"]');

            const imageObserver = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src || img.src;
                        img.removeAttribute('loading');
                        imageObserver.unobserve(img);
                    }
                });
            });

            lazyImages.forEach(function(img) {
                imageObserver.observe(img);
            });
        }
    }

    /**
     * Stats Counter Animation
     */
    function initStatsCounter() {
        const statNumbers = document.querySelectorAll('.stat-number');

        if (!statNumbers.length) return;

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    animateCounter(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });

        statNumbers.forEach(function(stat) {
            observer.observe(stat);
        });
    }

    /**
     * Animate Counter
     */
    function animateCounter(element) {
        const target = parseInt(element.dataset.count || element.textContent.replace(/,/g, ''));
        const duration = 2000;
        const steps = 60;
        const increment = target / steps;
        let current = 0;

        const timer = setInterval(function() {
            current += increment;

            if (current >= target) {
                element.textContent = target.toLocaleString();
                clearInterval(timer);
            } else {
                element.textContent = Math.floor(current).toLocaleString();
            }
        }, duration / steps);
    }

    /**
     * Add rel="noopener noreferrer" to external links for security
     */
    function initExternalLinks() {
        const links = document.querySelectorAll('a[href^="http"]');

        links.forEach(function(link) {
            // Check if link is external
            if (link.hostname !== window.location.hostname) {
                // Add security attributes
                if (link.getAttribute('target') === '_blank') {
                    link.setAttribute('rel', 'noopener noreferrer');
                }

                // Add external link icon (optional)
                if (!link.querySelector('.external-icon')) {
                    const icon = document.createElement('span');
                    icon.className = 'external-icon inline-block ml-1';
                    icon.setAttribute('aria-hidden', 'true');
                    icon.innerHTML = '<svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>';
                    link.appendChild(icon);
                }
            }
        });
    }

    /**
     * Intersection Observer for Animations
     */
    if ('IntersectionObserver' in window) {
        const animatedElements = document.querySelectorAll('.animate-on-scroll');

        const animationObserver = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-fade-in-up');
                    animationObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });

        animatedElements.forEach(function(element) {
            animationObserver.observe(element);
        });
    }

})();
