/**
 * Dark Mode Toggle with localStorage
 *
 * @package AdPrint_Blog
 * @since 1.0.0
 */

(function() {
    'use strict';

    // Check for saved theme preference or default to 'light'
    const currentTheme = localStorage.getItem('theme') || 'light';

    // Apply theme immediately (before page renders) to prevent flash
    if (currentTheme === 'dark') {
        document.documentElement.classList.add('dark');
    }

    // Wait for DOM to be ready
    document.addEventListener('DOMContentLoaded', function() {
        const darkModeToggle = document.getElementById('dark-mode-toggle');

        if (!darkModeToggle) {
            console.warn('Dark mode toggle button not found');
            return;
        }

        // Set initial state
        updateToggleButton(currentTheme === 'dark');

        // Add event listener
        darkModeToggle.addEventListener('click', function() {
            const isDark = document.documentElement.classList.contains('dark');

            if (isDark) {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('theme', 'light');
                updateToggleButton(false);
                announceThemeChange('light');
            } else {
                document.documentElement.classList.add('dark');
                localStorage.setItem('theme', 'dark');
                updateToggleButton(true);
                announceThemeChange('dark');
            }
        });

        // Listen for system theme changes
        if (window.matchMedia) {
            const darkModeMediaQuery = window.matchMedia('(prefers-color-scheme: dark)');

            darkModeMediaQuery.addEventListener('change', function(e) {
                // Only auto-switch if user hasn't set a preference
                if (!localStorage.getItem('theme')) {
                    if (e.matches) {
                        document.documentElement.classList.add('dark');
                        updateToggleButton(true);
                    } else {
                        document.documentElement.classList.remove('dark');
                        updateToggleButton(false);
                    }
                }
            });
        }

        /**
         * Update toggle button appearance
         */
        function updateToggleButton(isDark) {
            const sunIcon = darkModeToggle.querySelector('.sun-icon');
            const moonIcon = darkModeToggle.querySelector('.moon-icon');

            if (isDark) {
                if (sunIcon) sunIcon.classList.remove('hidden');
                if (moonIcon) moonIcon.classList.add('hidden');
                darkModeToggle.setAttribute('aria-label', 'Switch to light mode');
                darkModeToggle.setAttribute('title', 'Switch to light mode');
            } else {
                if (sunIcon) sunIcon.classList.add('hidden');
                if (moonIcon) moonIcon.classList.remove('hidden');
                darkModeToggle.setAttribute('aria-label', 'Switch to dark mode');
                darkModeToggle.setAttribute('title', 'Switch to dark mode');
            }
        }

        /**
         * Announce theme change to screen readers
         */
        function announceThemeChange(theme) {
            const announcement = document.createElement('div');
            announcement.setAttribute('role', 'status');
            announcement.setAttribute('aria-live', 'polite');
            announcement.className = 'sr-only';
            announcement.textContent = theme === 'dark' ? 'Dark mode enabled' : 'Light mode enabled';

            document.body.appendChild(announcement);

            setTimeout(function() {
                document.body.removeChild(announcement);
            }, 1000);
        }
    });

    // Keyboard shortcut: Ctrl/Cmd + Shift + D to toggle dark mode
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'D') {
            e.preventDefault();
            const darkModeToggle = document.getElementById('dark-mode-toggle');
            if (darkModeToggle) {
                darkModeToggle.click();
            }
        }
    });

})();
