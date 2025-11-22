<?php
/**
 * Advanced Performance Optimizations
 *
 * @package AdPrint_Blog
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * 5. Critical CSS Inline (High Priority)
 */
function adprint_inline_critical_above_fold() {
    $critical_css = "
    html{scroll-behavior:smooth;overflow-x:hidden}
    body{font-family:Inter,system-ui,sans-serif;overflow-x:hidden;margin:0}
    .container{width:100%;max-width:1200px;margin:0 auto;padding:0 1rem}
    .main-nav{position:sticky;top:0;z-index:40;background:#fff;border-bottom:1px solid #e5e7eb}
    @media (prefers-color-scheme:dark){body{background:#111827;color:#f9fafb}.main-nav{background:#111827;border-color:#1f2937}}
    ";

    echo '<style id="critical-css">' . $critical_css . '</style>';
}
add_action('wp_head', 'adprint_inline_critical_above_fold', 1);

/**
 * 6. Defer main CSS (Medium Priority)
 */
function adprint_defer_non_critical_css($html, $handle) {
    if ($handle === 'adprint-style') {
        $html = str_replace("rel='stylesheet'", "rel='preload' as='style' onload=\"this.onload=null;this.rel='stylesheet'\"", $html);
        $html .= '<noscript><link rel="stylesheet" href="' . wp_styles()->registered[$handle]->src . '"></noscript>';
    }
    return $html;
}
add_filter('style_loader_tag', 'adprint_defer_non_critical_css', 10, 2);

/**
 * 7. Preload key resources (Medium Priority)
 */
function adprint_preload_key_assets() {
    // Preload critical CSS
    echo '<link rel="preload" as="style" href="' . ADPRINT_THEME_URI . '/assets/css/output.css">' . PHP_EOL;

    // Preload fonts
    echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . PHP_EOL;
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . PHP_EOL;
}
add_action('wp_head', 'adprint_preload_key_assets', 2);

/**
 * 8. Cache post views (Medium Priority)
 */
function adprint_track_post_views_cached() {
    if (!is_single() || is_user_logged_in()) {
        return;
    }

    $post_id = get_the_ID();
    $cache_key = 'post_view_' . $post_id . '_' . md5($_SERVER['REMOTE_ADDR']);

    // Only update every 5 minutes per user/IP
    if (!get_transient($cache_key)) {
        adprint_set_post_views($post_id);
        set_transient($cache_key, true, 300); // 5 minutes
    }
}
// Replace old tracking function
remove_action('wp_head', 'adprint_track_post_views');
add_action('wp', 'adprint_track_post_views_cached');

/**
 * 9. Optimize queries (Medium Priority)
 */
function adprint_optimize_queries($query) {
    if (!is_admin() && $query->is_main_query()) {
        // Disable unnecessary query calculations
        $query->set('no_found_rows', true);
        $query->set('update_post_meta_cache', true);
        $query->set('update_post_term_cache', true);
    }
}
add_action('pre_get_posts', 'adprint_optimize_queries');

/**
 * 10. Responsive images srcset (Medium Priority)
 */
function adprint_responsive_image_sizes($attr, $attachment, $size) {
    if (!isset($attr['sizes'])) {
        $attr['sizes'] = '(max-width: 640px) 100vw, (max-width: 1024px) 50vw, 33vw';
    }
    return $attr;
}
add_filter('wp_get_attachment_image_attributes', 'adprint_responsive_image_sizes', 10, 3);

/**
 * 11. Enhanced security headers (Medium Priority)
 */
function adprint_enhanced_security_headers() {
    if (!is_admin()) {
        // Existing headers
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: SAMEORIGIN');
        header('X-XSS-Protection: 1; mode=block');
        header('Referrer-Policy: strict-origin-when-cross-origin');

        // Additional security
        header('Permissions-Policy: geolocation=(), microphone=(), camera=()');

        // Content Security Policy (permissive for compatibility)
        if (!defined('WP_DEBUG') || !WP_DEBUG) {
            header("Content-Security-Policy: default-src 'self' 'unsafe-inline' https:; script-src 'self' 'unsafe-inline' 'unsafe-eval' https:; style-src 'self' 'unsafe-inline' https:;");
        }
    }
}
remove_action('send_headers', 'adprint_security_headers');
add_action('send_headers', 'adprint_enhanced_security_headers');

/**
 * 12. AJAX nonce verification enhancement (Medium Priority)
 */
function adprint_verify_ajax_nonce() {
    if (defined('DOING_AJAX') && DOING_AJAX) {
        if (!isset($_REQUEST['nonce']) || !wp_verify_nonce($_REQUEST['nonce'], 'adprint-nonce')) {
            wp_send_json_error('Invalid security token', 403);
            wp_die();
        }
    }
}
// Applied per AJAX handler instead

/**
 * 13. Object caching for expensive queries (Low Priority)
 */
function adprint_get_popular_posts($count = 5) {
    $cache_key = 'adprint_popular_posts_' . $count;
    $posts = wp_cache_get($cache_key);

    if (false === $posts) {
        global $wpdb;
        $posts = $wpdb->get_results($wpdb->prepare("
            SELECT p.ID, p.post_title, pm.meta_value as views
            FROM {$wpdb->posts} p
            INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
            WHERE pm.meta_key = 'adprint_post_views_count'
            AND p.post_status = 'publish'
            AND p.post_type = 'post'
            ORDER BY CAST(pm.meta_value AS UNSIGNED) DESC
            LIMIT %d
        ", $count));

        wp_cache_set($cache_key, $posts, '', 3600); // 1 hour
    }

    return $posts;
}

/**
 * 14. Analytics Integration (Low Priority)
 */
function adprint_google_analytics() {
    $ga_id = get_theme_mod('adprint_analytics_id');

    if (!$ga_id || is_user_logged_in()) {
        return;
    }
    ?>
    <!-- Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo esc_attr($ga_id); ?>"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '<?php echo esc_js($ga_id); ?>', {
            'anonymize_ip': true,
            'cookie_flags': 'SameSite=None;Secure'
        });
    </script>
    <?php
}
add_action('wp_head', 'adprint_google_analytics', 99);

/**
 * 15. System preference dark mode (Enhancement)
 */
function adprint_system_dark_mode_support() {
    ?>
    <script>
    // Apply system preference before page load
    (function() {
        const theme = localStorage.getItem('theme');
        if (!theme && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            document.documentElement.classList.add('dark');
        }
    })();
    </script>
    <?php
}
add_action('wp_head', 'adprint_system_dark_mode_support', 0);

/**
 * 16. DNS Prefetch for external resources (Low Priority)
 */
function adprint_dns_prefetch() {
    echo '<link rel="dns-prefetch" href="//fonts.googleapis.com">' . PHP_EOL;
    echo '<link rel="dns-prefetch" href="//fonts.gstatic.com">' . PHP_EOL;
}
add_action('wp_head', 'adprint_dns_prefetch', 1);

/**
 * 17. Disable unused WordPress features (Performance)
 */
// Already implemented in functions.php (emojis, etc.)

/**
 * 18. Minify HTML output (Optional - can cause issues)
 */
function adprint_minify_html($buffer) {
    if (!is_admin() && !defined('DOING_AJAX')) {
        $buffer = preg_replace('/<!--(?!\s*(?:\[if [^\]]+]|<!|>))(?:(?!-->).)*-->/s', '', $buffer);
        $buffer = str_replace(array("\r\n", "\r", "\n", "\t"), '', $buffer);
        $buffer = preg_replace('/ {2,}/', ' ', $buffer);
    }
    return $buffer;
}
// Uncomment to enable HTML minification (test thoroughly first)
// add_action('template_redirect', function() {
//     ob_start('adprint_minify_html');
// });
