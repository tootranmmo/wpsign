<?php
/**
 * AdPrint Blog Theme Functions
 *
 * @package AdPrint_Blog
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Theme version
define('ADPRINT_VERSION', '1.0.0');
define('ADPRINT_THEME_DIR', get_template_directory());
define('ADPRINT_THEME_URI', get_template_directory_uri());

/**
 * Theme Setup
 */
function adprint_setup() {
    // Add default posts and comments RSS feed links to head
    add_theme_support('automatic-feed-links');

    // Let WordPress manage the document title
    add_theme_support('title-tag');

    // Enable support for Post Thumbnails
    add_theme_support('post-thumbnails');

    // Set post thumbnail size
    set_post_thumbnail_size(1200, 630, true);

    // Add custom image sizes
    add_image_size('adprint-hero', 1920, 1080, true);
    add_image_size('adprint-large', 1200, 675, true);
    add_image_size('adprint-medium', 800, 450, true);
    add_image_size('adprint-small', 400, 225, true);

    // Register navigation menus
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'adprint-blog'),
        'footer' => __('Footer Menu', 'adprint-blog'),
    ));

    // Switch default core markup to output valid HTML5
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ));

    // Add theme support for selective refresh for widgets
    add_theme_support('customize-selective-refresh-widgets');

    // Add support for editor styles
    add_theme_support('editor-styles');

    // Add support for responsive embedded content
    add_theme_support('responsive-embeds');

    // Add support for custom logo
    add_theme_support('custom-logo', array(
        'height'      => 100,
        'width'       => 400,
        'flex-height' => true,
        'flex-width'  => true,
    ));

    // Load text domain for translations
    load_theme_textdomain('adprint-blog', ADPRINT_THEME_DIR . '/languages');
}
add_action('after_setup_theme', 'adprint_setup');

/**
 * Set the content width in pixels
 */
function adprint_content_width() {
    $GLOBALS['content_width'] = apply_filters('adprint_content_width', 1200);
}
add_action('after_setup_theme', 'adprint_content_width', 0);

/**
 * Register widget areas
 */
function adprint_widgets_init() {
    register_sidebar(array(
        'name'          => __('Sidebar', 'adprint-blog'),
        'id'            => 'sidebar-1',
        'description'   => __('Add widgets here to appear in your sidebar.', 'adprint-blog'),
        'before_widget' => '<section id="%1$s" class="widget %2$s bg-white dark:bg-gray-800 rounded-xl p-6 mb-6 shadow-lg">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title text-2xl font-bold mb-4 text-gray-900 dark:text-gray-100">',
        'after_title'   => '</h2>',
    ));

    register_sidebar(array(
        'name'          => __('Footer Widget Area 1', 'adprint-blog'),
        'id'            => 'footer-1',
        'description'   => __('Add widgets here to appear in your footer.', 'adprint-blog'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title text-lg font-bold mb-4 text-white">',
        'after_title'   => '</h3>',
    ));

    register_sidebar(array(
        'name'          => __('Footer Widget Area 2', 'adprint-blog'),
        'id'            => 'footer-2',
        'description'   => __('Add widgets here to appear in your footer.', 'adprint-blog'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title text-lg font-bold mb-4 text-white">',
        'after_title'   => '</h3>',
    ));

    register_sidebar(array(
        'name'          => __('Footer Widget Area 3', 'adprint-blog'),
        'id'            => 'footer-3',
        'description'   => __('Add widgets here to appear in your footer.', 'adprint-blog'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title text-lg font-bold mb-4 text-white">',
        'after_title'   => '</h3>',
    ));
}
add_action('widgets_init', 'adprint_widgets_init');

/**
 * Enqueue scripts and styles
 */
function adprint_scripts() {
    // Main stylesheet (Tailwind CSS output)
    wp_enqueue_style('adprint-style', ADPRINT_THEME_URI . '/assets/css/output.css', array(), ADPRINT_VERSION);

    // Theme stylesheet (required by WordPress)
    wp_enqueue_style('adprint-theme-style', get_stylesheet_uri(), array('adprint-style'), ADPRINT_VERSION);

    // Dark mode script (loaded in footer with high priority)
    wp_enqueue_script('adprint-dark-mode', ADPRINT_THEME_URI . '/assets/js/dark-mode.js', array(), ADPRINT_VERSION, true);

    // Main theme script
    wp_enqueue_script('adprint-main', ADPRINT_THEME_URI . '/assets/js/main.js', array(), ADPRINT_VERSION, true);

    // Conditional loading - Rating system script (only on single posts)
    if (is_single()) {
        wp_enqueue_script('adprint-rating', ADPRINT_THEME_URI . '/assets/js/rating.js', array(), ADPRINT_VERSION, true);
    }

    // Conditional loading - Search functionality (only on front page, search, and archive pages)
    if (is_front_page() || is_search() || is_archive()) {
        wp_enqueue_script('adprint-search', ADPRINT_THEME_URI . '/assets/js/search.js', array(), ADPRINT_VERSION, true);
    }

    // Localize script for AJAX
    wp_localize_script('adprint-main', 'adprintData', array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('adprint-nonce'),
        'homeUrl' => home_url(),
        'themeUri' => ADPRINT_THEME_URI,
    ));

    // Comment reply script
    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}
add_action('wp_enqueue_scripts', 'adprint_scripts');

/**
 * Add resource hints for performance
 */
function adprint_resource_hints($urls, $relation_type) {
    if ('preconnect' === $relation_type) {
        $urls[] = array(
            'href' => 'https://fonts.googleapis.com',
            'crossorigin',
        );
        $urls[] = array(
            'href' => 'https://fonts.gstatic.com',
            'crossorigin',
        );
    }
    return $urls;
}
add_filter('wp_resource_hints', 'adprint_resource_hints', 10, 2);

/**
 * Include theme files
 */
require_once ADPRINT_THEME_DIR . '/inc/schema/schema-org.php';
require_once ADPRINT_THEME_DIR . '/inc/seo/meta-tags.php';
require_once ADPRINT_THEME_DIR . '/inc/seo/sitemap.php';
require_once ADPRINT_THEME_DIR . '/inc/performance/webp-support.php';
require_once ADPRINT_THEME_DIR . '/inc/performance/lazy-load.php';
require_once ADPRINT_THEME_DIR . '/inc/performance/critical-css.php';
require_once ADPRINT_THEME_DIR . '/inc/performance/advanced-optimizations.php';

/**
 * Custom template tags
 */
require_once ADPRINT_THEME_DIR . '/inc/template-tags.php';

/**
 * Customizer additions
 */
require_once ADPRINT_THEME_DIR . '/inc/customizer.php';

/**
 * Add async/defer attributes to scripts
 */
function adprint_script_loader_tag($tag, $handle, $src) {
    // Defer non-critical scripts
    $defer_scripts = array('adprint-main', 'adprint-search');

    if (in_array($handle, $defer_scripts)) {
        return str_replace(' src', ' defer src', $tag);
    }

    return $tag;
}
add_filter('script_loader_tag', 'adprint_script_loader_tag', 10, 3);

/**
 * Excerpt length
 */
function adprint_excerpt_length($length) {
    return 30;
}
add_filter('excerpt_length', 'adprint_excerpt_length');

/**
 * Excerpt more
 */
function adprint_excerpt_more($more) {
    return '...';
}
add_filter('excerpt_more', 'adprint_excerpt_more');

/**
 * Add security headers
 */
function adprint_security_headers() {
    if (!is_admin()) {
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: SAMEORIGIN');
        header('X-XSS-Protection: 1; mode=block');
        header('Referrer-Policy: strict-origin-when-cross-origin');
    }
}
add_action('send_headers', 'adprint_security_headers');

/**
 * Disable WordPress emojis for performance
 */
function adprint_disable_emojis() {
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
}
add_action('init', 'adprint_disable_emojis');

/**
 * Remove query strings from static resources
 */
function adprint_remove_query_strings($src) {
    if (strpos($src, '?ver=')) {
        $src = remove_query_arg('ver', $src);
    }
    return $src;
}
add_filter('style_loader_src', 'adprint_remove_query_strings', 10, 1);
add_filter('script_loader_src', 'adprint_remove_query_strings', 10, 1);

/**
 * Get post view count
 */
function adprint_get_post_views($post_id) {
    $count = get_post_meta($post_id, 'adprint_post_views_count', true);
    return $count ? $count : 0;
}

/**
 * Set post view count
 */
function adprint_set_post_views($post_id) {
    $count = adprint_get_post_views($post_id);
    $count++;
    update_post_meta($post_id, 'adprint_post_views_count', $count);
}

/**
 * Track post views
 */
function adprint_track_post_views() {
    if (is_single()) {
        adprint_set_post_views(get_the_ID());
    }
}
add_action('wp_head', 'adprint_track_post_views');

/**
 * AJAX handler for rating submission
 */
function adprint_submit_rating_ajax() {
    check_ajax_referer('adprint-nonce', 'nonce');

    $post_id = intval($_POST['post_id']);
    $rating = floatval($_POST['rating']);

    if ($post_id <= 0 || $rating < 1 || $rating > 5) {
        wp_send_json_error('Invalid parameters');
    }

    // Get current ratings
    $ratings = get_post_meta($post_id, 'adprint_ratings', true);

    if (!is_array($ratings)) {
        $ratings = array(
            'total' => 0,
            'count' => 0,
        );
    }

    // Add new rating
    $ratings['total'] += $rating;
    $ratings['count']++;

    // Calculate average
    $average = $ratings['total'] / $ratings['count'];

    // Update meta
    update_post_meta($post_id, 'adprint_ratings', $ratings);
    update_post_meta($post_id, 'adprint_rating', $average);

    wp_send_json_success(array(
        'average_rating' => $average,
        'rating_count' => $ratings['count'],
    ));
}
add_action('wp_ajax_adprint_submit_rating', 'adprint_submit_rating_ajax');
add_action('wp_ajax_nopriv_adprint_submit_rating', 'adprint_submit_rating_ajax');

/**
 * AJAX handler for search suggestions
 */
function adprint_search_suggestions_ajax() {
    check_ajax_referer('adprint-nonce', 'nonce');

    $query = sanitize_text_field($_GET['s']);

    if (strlen($query) < 3) {
        wp_send_json_error('Query too short');
    }

    $args = array(
        'post_type' => 'post',
        'post_status' => 'publish',
        'posts_per_page' => 5,
        's' => $query,
    );

    $search_query = new WP_Query($args);

    $results = array();

    if ($search_query->have_posts()) {
        while ($search_query->have_posts()) {
            $search_query->the_post();

            $results[] = array(
                'title' => get_the_title(),
                'url' => get_permalink(),
                'excerpt' => wp_trim_words(get_the_excerpt(), 15),
                'thumbnail' => get_the_post_thumbnail_url(get_the_ID(), 'thumbnail'),
            );
        }
        wp_reset_postdata();
    }

    wp_send_json_success($results);
}
add_action('wp_ajax_adprint_search_suggestions', 'adprint_search_suggestions_ajax');
add_action('wp_ajax_nopriv_adprint_search_suggestions', 'adprint_search_suggestions_ajax');
