/**
 * Enhanced Search Functionality
 *
 * @package AdPrint_Blog
 * @since 1.0.0
 */

(function() {
    'use strict';

    document.addEventListener('DOMContentLoaded', function() {
        const searchForms = document.querySelectorAll('.hero-search, .search-form');

        searchForms.forEach(function(form) {
            const searchInput = form.querySelector('input[type="search"], input[name="s"]');
            const searchButton = form.querySelector('button[type="submit"]');

            if (!searchInput) return;

            // Live search suggestions (optional)
            let searchTimeout;
            const suggestionsContainer = createSuggestionsContainer(form);

            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);

                const query = this.value.trim();

                if (query.length < 3) {
                    hideSuggestions(suggestionsContainer);
                    return;
                }

                // Debounce search requests
                searchTimeout = setTimeout(function() {
                    fetchSearchSuggestions(query, suggestionsContainer, searchInput);
                }, 300);
            });

            // Clear search
            const clearButton = createClearButton(searchInput);
            searchInput.parentNode.appendChild(clearButton);

            searchInput.addEventListener('input', function() {
                if (this.value.length > 0) {
                    clearButton.classList.remove('hidden');
                } else {
                    clearButton.classList.add('hidden');
                }
            });

            // Close suggestions on outside click
            document.addEventListener('click', function(e) {
                if (!form.contains(e.target)) {
                    hideSuggestions(suggestionsContainer);
                }
            });

            // Keyboard navigation for suggestions
            searchInput.addEventListener('keydown', function(e) {
                handleKeyboardNavigation(e, suggestionsContainer);
            });
        });

        /**
         * Create suggestions container
         */
        function createSuggestionsContainer(form) {
            const container = document.createElement('div');
            container.className = 'search-suggestions absolute w-full mt-2 bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 hidden z-50 max-h-96 overflow-y-auto';
            container.setAttribute('role', 'listbox');

            form.appendChild(container);

            return container;
        }

        /**
         * Create clear button
         */
        function createClearButton(input) {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'absolute right-24 top-1/2 -translate-y-1/2 p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hidden';
            button.setAttribute('aria-label', 'Clear search');
            button.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>';

            button.addEventListener('click', function() {
                input.value = '';
                input.focus();
                this.classList.add('hidden');
                hideSuggestions(input.parentNode.querySelector('.search-suggestions'));
            });

            return button;
        }

        /**
         * Fetch search suggestions via AJAX
         */
        function fetchSearchSuggestions(query, container, input) {
            fetch(adprintData.ajaxUrl + '?action=adprint_search_suggestions&s=' + encodeURIComponent(query) + '&nonce=' + adprintData.nonce)
                .then(function(response) {
                    return response.json();
                })
                .then(function(data) {
                    if (data.success && data.data.length > 0) {
                        displaySuggestions(data.data, container, input);
                    } else {
                        displayNoResults(container);
                    }
                })
                .catch(function(error) {
                    console.error('Search error:', error);
                });
        }

        /**
         * Display search suggestions
         */
        function displaySuggestions(suggestions, container, input) {
            container.innerHTML = '';

            suggestions.forEach(function(suggestion, index) {
                const item = document.createElement('a');
                item.href = suggestion.url;
                item.className = 'search-suggestion-item flex items-start gap-4 p-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors border-b border-gray-100 dark:border-gray-700 last:border-0';
                item.setAttribute('role', 'option');
                item.dataset.index = index;

                let thumbnail = '';
                if (suggestion.thumbnail) {
                    thumbnail = '<img src="' + suggestion.thumbnail + '" alt="" class="w-16 h-16 object-cover rounded" loading="lazy">';
                } else {
                    thumbnail = '<div class="w-16 h-16 bg-gray-200 dark:bg-gray-600 rounded flex items-center justify-center"><svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></div>';
                }

                item.innerHTML = thumbnail + '<div class="flex-1 min-w-0"><h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-1 truncate">' + highlightQuery(suggestion.title, input.value) + '</h4><p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2">' + suggestion.excerpt + '</p></div>';

                container.appendChild(item);
            });

            showSuggestions(container);
        }

        /**
         * Display no results message
         */
        function displayNoResults(container) {
            container.innerHTML = '<div class="p-6 text-center text-gray-500 dark:text-gray-400"><svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg><p class="font-medium">No results found</p><p class="text-sm">Try different keywords</p></div>';
            showSuggestions(container);
        }

        /**
         * Highlight search query in results
         */
        function highlightQuery(text, query) {
            const regex = new RegExp('(' + escapeRegex(query) + ')', 'gi');
            return text.replace(regex, '<mark class="bg-yellow-200 dark:bg-yellow-800">$1</mark>');
        }

        /**
         * Escape regex special characters
         */
        function escapeRegex(string) {
            return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        }

        /**
         * Show suggestions
         */
        function showSuggestions(container) {
            container.classList.remove('hidden');
        }

        /**
         * Hide suggestions
         */
        function hideSuggestions(container) {
            if (container) {
                container.classList.add('hidden');
            }
        }

        /**
         * Handle keyboard navigation
         */
        function handleKeyboardNavigation(e, container) {
            const items = container.querySelectorAll('.search-suggestion-item');

            if (items.length === 0) return;

            const currentIndex = Array.from(items).findIndex(function(item) {
                return item === document.activeElement;
            });

            switch (e.key) {
                case 'ArrowDown':
                    e.preventDefault();
                    if (currentIndex < items.length - 1) {
                        items[currentIndex + 1].focus();
                    } else {
                        items[0].focus();
                    }
                    break;

                case 'ArrowUp':
                    e.preventDefault();
                    if (currentIndex > 0) {
                        items[currentIndex - 1].focus();
                    } else {
                        items[items.length - 1].focus();
                    }
                    break;

                case 'Escape':
                    hideSuggestions(container);
                    e.target.blur();
                    break;
            }
        }
    });

})();
