<?php
/**
 * Critical CSS Inline
 *
 * @package AdPrint_Blog
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Inline critical CSS in head
 */
function adprint_inline_critical_css() {
    // Get critical CSS based on page type
    $critical_css = adprint_get_critical_css();

    if (empty($critical_css)) {
        return;
    }

    echo '<style id="adprint-critical-css">' . PHP_EOL;
    echo $critical_css;
    echo PHP_EOL . '</style>' . PHP_EOL;
}
add_action('wp_head', 'adprint_inline_critical_css', 1);

/**
 * Get critical CSS based on page type
 */
function adprint_get_critical_css() {
    $cache_key = 'adprint_critical_css_' . adprint_get_page_type();
    $cached = wp_cache_get($cache_key);

    if ($cached !== false) {
        return $cached;
    }

    $css = '';

    // Base critical CSS (always loaded)
    $css .= adprint_get_base_critical_css();

    // Page-specific critical CSS
    if (is_front_page()) {
        $css .= adprint_get_homepage_critical_css();
    } elseif (is_singular('post')) {
        $css .= adprint_get_single_critical_css();
    } elseif (is_archive() || is_category() || is_tag()) {
        $css .= adprint_get_archive_critical_css();
    }

    // Cache for 24 hours
    wp_cache_set($cache_key, $css, '', DAY_IN_SECONDS);

    return $css;
}

/**
 * Get page type for caching
 */
function adprint_get_page_type() {
    if (is_front_page()) {
        return 'home';
    } elseif (is_singular('post')) {
        return 'single';
    } elseif (is_archive()) {
        return 'archive';
    } elseif (is_search()) {
        return 'search';
    } elseif (is_404()) {
        return '404';
    }
    return 'default';
}

/**
 * Base critical CSS
 */
function adprint_get_base_critical_css() {
    return '
/* Base Critical CSS */
*,::before,::after{box-sizing:border-box;border-width:0;border-style:solid;border-color:#e5e7eb}
html{line-height:1.5;-webkit-text-size-adjust:100%;-moz-tab-size:4;tab-size:4;font-family:Inter,system-ui,-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,sans-serif}
body{margin:0;line-height:inherit;color:#111827;background-color:#fff}
h1,h2,h3,h4,h5,h6{font-size:inherit;font-weight:inherit}
a{color:inherit;text-decoration:inherit}
img,svg,video,canvas,audio,iframe,embed,object{display:block;vertical-align:middle}
img,video{max-width:100%;height:auto}

/* Dark Mode Base */
.dark body{background-color:#111827;color:#f9fafb}

/* Header */
.main-nav{position:sticky;top:0;z-index:40;background-color:rgba(255,255,255,0.95);border-bottom:1px solid #e5e7eb;backdrop-filter:blur(8px)}
.dark .main-nav{background-color:rgba(17,24,39,0.95);border-bottom-color:#1f2937}

/* Skip to content (Accessibility) */
.skip-link{position:absolute;left:-999px;width:1px;height:1px;overflow:hidden}
.skip-link:focus{position:absolute;top:1rem;left:1rem;z-index:50;padding:1rem;background-color:#0284c7;color:#fff;border-radius:0.375rem;width:auto;height:auto}

/* Container */
.container{width:100%;max-width:1200px;margin-left:auto;margin-right:auto;padding-left:1rem;padding-right:1rem}

/* Buttons */
.btn-primary{display:inline-flex;align-items:center;padding:0.75rem 1.5rem;background-color:#0284c7;color:#fff;font-weight:600;border-radius:0.5rem;transition:background-color 0.2s}
.btn-primary:hover{background-color:#0369a1}
.btn-primary:focus{outline:none;box-shadow:0 0 0 4px rgba(2,132,199,0.1)}
';
}

/**
 * Homepage critical CSS
 */
function adprint_get_homepage_critical_css() {
    return '
/* Hero Section */
.hero-section{position:relative;padding:4rem 0;background:linear-gradient(135deg,#0ea5e9 0%,#0284c7 100%)}
.hero-search{position:relative;width:100%;max-width:48rem;margin:0 auto}
.hero-search input{width:100%;padding:1rem 1.5rem;font-size:1.125rem;border-radius:9999px;border:2px solid transparent;background-color:#fff;outline:none}
.hero-search input:focus{border-color:#0ea5e9;box-shadow:0 0 0 4px rgba(14,165,233,0.1)}
.hero-search button{position:absolute;right:0.5rem;top:50%;transform:translateY(-50%);padding:0.5rem 1.5rem;background-color:#0284c7;color:#fff;border-radius:9999px;font-weight:600}

/* Stats Dashboard */
.stat-card{padding:1.5rem;border-radius:0.75rem;background:linear-gradient(135deg,#0ea5e9 0%,#0284c7 100%);color:#fff;box-shadow:0 10px 15px -3px rgba(0,0,0,0.1)}

/* Post Cards */
.post-card{background-color:#fff;border-radius:0.75rem;overflow:hidden;box-shadow:0 10px 15px -3px rgba(0,0,0,0.1);border:1px solid #e5e7eb;transition:box-shadow 0.3s}
.dark .post-card{background-color:#1f2937;border-color:#374151}
.post-card:hover{box-shadow:0 20px 25px -5px rgba(0,0,0,0.1)}
.post-card img{width:100%;height:12rem;object-fit:cover}
';
}

/**
 * Single post critical CSS
 */
function adprint_get_single_critical_css() {
    return '
/* Single Post */
.post-header{margin-bottom:2rem}
.post-title{font-size:2.25rem;font-weight:700;line-height:1.2;margin-bottom:1rem}
.post-meta{display:flex;align-items:center;gap:1rem;color:#6b7280;font-size:0.875rem}
.post-content{line-height:1.75;color:#374151}
.dark .post-content{color:#d1d5db}

/* Rating Stars */
.rating-stars{display:flex;align-items:center;gap:0.25rem}
.star{font-size:1.25rem;color:#fbbf24}
';
}

/**
 * Archive critical CSS
 */
function adprint_get_archive_critical_css() {
    return '
/* Archive */
.archive-header{margin-bottom:2rem;padding-bottom:1rem;border-bottom:2px solid #e5e7eb}
.dark .archive-header{border-bottom-color:#374151}
.archive-title{font-size:2rem;font-weight:700}
.archive-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:2rem}
';
}

/**
 * Preload critical resources
 */
function adprint_preload_resources() {
    // Preload fonts
    echo '<link rel="preload" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@600;700;800&display=swap" as="style" onload="this.onload=null;this.rel=\'stylesheet\'">' . PHP_EOL;
    echo '<noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@600;700;800&display=swap"></noscript>' . PHP_EOL;

    // Preload main CSS
    echo '<link rel="preload" href="' . ADPRINT_THEME_URI . '/assets/css/output.css" as="style">' . PHP_EOL;
}
add_action('wp_head', 'adprint_preload_resources', 2);

/**
 * Defer non-critical CSS
 */
function adprint_defer_css($html, $handle, $href, $media) {
    // Only defer main stylesheet
    if ($handle === 'adprint-style') {
        $html = '<link rel="stylesheet" href="' . $href . '" media="print" onload="this.media=\'all\'; this.onload=null;">';
        $html .= '<noscript><link rel="stylesheet" href="' . $href . '"></noscript>';
    }

    return $html;
}
// Uncomment to enable CSS deferring (use with caution)
// add_filter('style_loader_tag', 'adprint_defer_css', 10, 4);

/**
 * Remove unused CSS (if using plugin)
 */
function adprint_remove_unused_css() {
    // This is a placeholder for integration with CSS optimization plugins
    // or custom unused CSS removal logic

    do_action('adprint_before_remove_unused_css');

    // Custom logic here

    do_action('adprint_after_remove_unused_css');
}

/**
 * Generate critical CSS file (CLI command or admin action)
 */
function adprint_generate_critical_css_file() {
    // This would be triggered by a CLI command or admin action
    // to generate critical CSS files for different page types

    $page_types = array('home', 'single', 'archive');
    $output_dir = ADPRINT_THEME_DIR . '/assets/css/critical/';

    if (!file_exists($output_dir)) {
        wp_mkdir_p($output_dir);
    }

    foreach ($page_types as $type) {
        $css = '';

        switch ($type) {
            case 'home':
                $css = adprint_get_base_critical_css() . adprint_get_homepage_critical_css();
                break;
            case 'single':
                $css = adprint_get_base_critical_css() . adprint_get_single_critical_css();
                break;
            case 'archive':
                $css = adprint_get_base_critical_css() . adprint_get_archive_critical_css();
                break;
        }

        file_put_contents($output_dir . $type . '.css', $css);
    }

    return true;
}
