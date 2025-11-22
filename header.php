<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="profile" href="https://gmpg.org/xfn/11">

    <?php wp_head(); ?>

    <script>
        // Remove no-js class immediately
        document.documentElement.classList.remove('no-js');
        document.documentElement.classList.add('js');
    </script>
</head>

<body <?php body_class('antialiased'); ?>>
<?php wp_body_open(); ?>

<!-- Skip to main content (Accessibility) -->
<a href="#main-content" class="skip-link sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-50 focus:px-4 focus:py-2 focus:bg-primary-600 focus:text-white focus:rounded">
    <?php esc_html_e('Skip to main content', 'adprint-blog'); ?>
</a>

<!-- Header -->
<header id="masthead" class="main-nav" role="banner">
    <div class="container">
        <div class="flex items-center justify-between py-4">
            <!-- Logo -->
            <div class="site-branding">
                <?php if (has_custom_logo()) : ?>
                    <div class="site-logo">
                        <?php the_custom_logo(); ?>
                    </div>
                <?php else : ?>
                    <div class="text-branding">
                        <h1 class="site-title text-2xl font-bold font-display">
                            <a href="<?php echo esc_url(home_url('/')); ?>" class="text-gray-900 dark:text-gray-100 hover:text-primary-600 dark:hover:text-primary-400 transition-colors" rel="home">
                                <?php bloginfo('name'); ?>
                            </a>
                        </h1>
                        <?php
                        $description = get_bloginfo('description', 'display');
                        if ($description || is_customize_preview()) :
                        ?>
                            <p class="site-description text-sm text-gray-600 dark:text-gray-400 mt-1">
                                <?php echo $description; ?>
                            </p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Primary Navigation -->
            <nav id="site-navigation" class="primary-navigation hidden lg:block" aria-label="<?php esc_attr_e('Primary Menu', 'adprint-blog'); ?>">
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'menu_class'     => 'flex items-center gap-6',
                    'container'      => false,
                    'fallback_cb'    => false,
                    'depth'          => 2,
                    'link_before'    => '<span class="nav-link">',
                    'link_after'     => '</span>',
                ));
                ?>
            </nav>

            <!-- Header Right: Search, Dark Mode, Mobile Menu -->
            <div class="header-right flex items-center gap-4">
                <!-- Search Toggle -->
                <button
                    type="button"
                    id="search-toggle"
                    class="p-2 text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-500 rounded"
                    aria-label="<?php esc_attr_e('Open search', 'adprint-blog'); ?>"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </button>

                <!-- Dark Mode Toggle -->
                <button
                    type="button"
                    id="dark-mode-toggle"
                    class="dark-mode-toggle p-2"
                    aria-label="<?php esc_attr_e('Toggle dark mode', 'adprint-blog'); ?>"
                >
                    <svg class="sun-icon w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <svg class="moon-icon w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>
                </button>

                <!-- Mobile Menu Toggle -->
                <button
                    type="button"
                    id="mobile-menu-toggle"
                    class="lg:hidden p-2 text-gray-700 dark:text-gray-300 hover:text-primary-600 focus:outline-none focus:ring-2 focus:ring-primary-500 rounded"
                    aria-expanded="false"
                    aria-controls="mobile-menu"
                    aria-label="<?php esc_attr_e('Toggle menu', 'adprint-blog'); ?>"
                >
                    <svg class="menu-open w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg class="menu-close w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="lg:hidden hidden pb-4" role="navigation" aria-label="<?php esc_attr_e('Mobile Menu', 'adprint-blog'); ?>">
            <?php
            wp_nav_menu(array(
                'theme_location' => 'primary',
                'menu_class'     => 'flex flex-col gap-2',
                'container'      => false,
                'fallback_cb'    => false,
                'depth'          => 2,
            ));
            ?>
        </div>

        <!-- Search Modal -->
        <div id="search-modal" class="hidden fixed inset-0 z-50 bg-black bg-opacity-50 flex items-start justify-center pt-20" role="dialog" aria-modal="true" aria-labelledby="search-modal-title">
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 w-full max-w-2xl mx-4">
                <div class="flex items-center justify-between mb-4">
                    <h2 id="search-modal-title" class="text-xl font-bold text-gray-900 dark:text-gray-100">
                        <?php esc_html_e('Search', 'adprint-blog'); ?>
                    </h2>
                    <button
                        type="button"
                        id="search-modal-close"
                        class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
                        aria-label="<?php esc_attr_e('Close search', 'adprint-blog'); ?>"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form role="search" method="get" class="search-form relative" action="<?php echo esc_url(home_url('/')); ?>">
                    <label for="search-input" class="sr-only"><?php esc_html_e('Search for:', 'adprint-blog'); ?></label>
                    <input
                        type="search"
                        id="search-input"
                        name="s"
                        class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 focus:border-primary-500 focus:ring-4 focus:ring-primary-100 dark:focus:ring-primary-900 outline-none"
                        placeholder="<?php esc_attr_e('Search...', 'adprint-blog'); ?>"
                        value="<?php echo get_search_query(); ?>"
                        autocomplete="off"
                    >
                </form>
            </div>
        </div>

        <script>
            // Search modal toggle
            document.addEventListener('DOMContentLoaded', function() {
                const searchToggle = document.getElementById('search-toggle');
                const searchModal = document.getElementById('search-modal');
                const searchModalClose = document.getElementById('search-modal-close');
                const searchInput = document.getElementById('search-input');

                if (searchToggle && searchModal) {
                    searchToggle.addEventListener('click', function() {
                        searchModal.classList.remove('hidden');
                        searchInput.focus();
                    });

                    searchModalClose.addEventListener('click', function() {
                        searchModal.classList.add('hidden');
                    });

                    searchModal.addEventListener('click', function(e) {
                        if (e.target === searchModal) {
                            searchModal.classList.add('hidden');
                        }
                    });

                    document.addEventListener('keydown', function(e) {
                        if (e.key === 'Escape' && !searchModal.classList.contains('hidden')) {
                            searchModal.classList.add('hidden');
                        }
                    });
                }
            });
        </script>
    </div>
</header>

<div id="page" class="site">
    <div id="content" class="site-content">
