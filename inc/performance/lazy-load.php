<?php
/**
 * Lazy Loading for Images and Iframes
 *
 * @package AdPrint_Blog
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add loading="lazy" to images
 */
function adprint_add_lazy_loading($attr, $attachment, $size) {
    // Don't add lazy loading to images in admin
    if (is_admin()) {
        return $attr;
    }

    // Don't add lazy loading to logo
    if ($attachment->ID === get_theme_mod('custom_logo')) {
        return $attr;
    }

    // Add loading attribute
    $attr['loading'] = 'lazy';

    // Add decoding attribute for better performance
    $attr['decoding'] = 'async';

    return $attr;
}
add_filter('wp_get_attachment_image_attributes', 'adprint_add_lazy_loading', 10, 3);

/**
 * Add lazy loading to content images
 */
function adprint_lazy_load_content_images($content) {
    // Don't process in admin or feeds
    if (is_admin() || is_feed()) {
        return $content;
    }

    // Add loading="lazy" to images that don't have it
    $content = preg_replace_callback(
        '/<img([^>]+?)\/?>/',
        function ($matches) {
            $img = $matches[0];

            // Skip if loading attribute already exists
            if (strpos($img, 'loading=') !== false) {
                return $img;
            }

            // Skip if it's marked as eager loading
            if (strpos($img, 'data-no-lazy') !== false) {
                return $img;
            }

            // Add loading="lazy" and decoding="async"
            $img = str_replace('<img', '<img loading="lazy" decoding="async"', $img);

            return $img;
        },
        $content
    );

    return $content;
}
add_filter('the_content', 'adprint_lazy_load_content_images', 20);

/**
 * Add lazy loading to iframes (videos, embeds)
 */
function adprint_lazy_load_iframes($content) {
    // Don't process in admin or feeds
    if (is_admin() || is_feed()) {
        return $content;
    }

    // Add loading="lazy" to iframes
    $content = preg_replace_callback(
        '/<iframe([^>]+?)\/?>/',
        function ($matches) {
            $iframe = $matches[0];

            // Skip if loading attribute already exists
            if (strpos($iframe, 'loading=') !== false) {
                return $iframe;
            }

            // Add loading="lazy"
            $iframe = str_replace('<iframe', '<iframe loading="lazy"', $iframe);

            return $iframe;
        },
        $content
    );

    return $content;
}
add_filter('the_content', 'adprint_lazy_load_iframes', 20);

/**
 * Exclude first image from lazy loading (for LCP optimization)
 */
function adprint_exclude_first_image_lazy_load($content) {
    static $first_image_processed = false;

    if (!$first_image_processed && !is_admin() && !is_feed()) {
        // Find first image and remove lazy loading
        $content = preg_replace(
            '/<img([^>]+?)loading=["\']lazy["\']/',
            '<img$1',
            $content,
            1 // Only replace first occurrence
        );

        // Add fetchpriority="high" to first image for better LCP
        $content = preg_replace(
            '/<img/',
            '<img fetchpriority="high"',
            $content,
            1
        );

        $first_image_processed = true;
    }

    return $content;
}
add_filter('the_content', 'adprint_exclude_first_image_lazy_load', 5);

/**
 * Add blur placeholder for better UX
 */
function adprint_add_blur_placeholder($attr, $attachment, $size) {
    // Don't add in admin
    if (is_admin()) {
        return $attr;
    }

    // Generate tiny blur placeholder (20x20px base64 encoded)
    $placeholder = adprint_get_blur_placeholder($attachment->ID);

    if ($placeholder) {
        $attr['data-src'] = $attr['src'];
        $attr['src'] = $placeholder;
        $attr['class'] = isset($attr['class']) ? $attr['class'] . ' blur-up' : 'blur-up';
    }

    return $attr;
}
// Uncomment to enable blur placeholder
// add_filter('wp_get_attachment_image_attributes', 'adprint_add_blur_placeholder', 15, 3);

/**
 * Generate blur placeholder
 */
function adprint_get_blur_placeholder($attachment_id) {
    $cache_key = 'adprint_blur_' . $attachment_id;
    $cached = wp_cache_get($cache_key);

    if ($cached) {
        return $cached;
    }

    $file = get_attached_file($attachment_id);

    if (!file_exists($file)) {
        return false;
    }

    // Create 20x20 thumbnail
    $image = wp_get_image_editor($file);

    if (is_wp_error($image)) {
        return false;
    }

    $image->resize(20, 20, true);

    // Get image data
    $quality = 50;
    $image->set_quality($quality);

    // Save to temp file
    $temp_file = $image->generate_filename('blur', sys_get_temp_dir());
    $image->save($temp_file);

    // Convert to base64
    $image_data = file_get_contents($temp_file);
    $base64 = base64_encode($image_data);
    $mime_type = mime_content_type($temp_file);

    // Clean up
    unlink($temp_file);

    $placeholder = 'data:' . $mime_type . ';base64,' . $base64;

    // Cache for 24 hours
    wp_cache_set($cache_key, $placeholder, '', DAY_IN_SECONDS);

    return $placeholder;
}

/**
 * Lazy load background images
 */
function adprint_lazy_load_background_images() {
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Lazy load elements with data-bg attribute
        const lazyBackgrounds = document.querySelectorAll('[data-bg]');

        if ('IntersectionObserver' in window) {
            const lazyBackgroundObserver = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        entry.target.style.backgroundImage = 'url(' + entry.target.dataset.bg + ')';
                        entry.target.removeAttribute('data-bg');
                        lazyBackgroundObserver.unobserve(entry.target);
                    }
                });
            });

            lazyBackgrounds.forEach(function(lazyBackground) {
                lazyBackgroundObserver.observe(lazyBackground);
            });
        } else {
            // Fallback for browsers that don't support IntersectionObserver
            lazyBackgrounds.forEach(function(lazyBackground) {
                lazyBackground.style.backgroundImage = 'url(' + lazyBackground.dataset.bg + ')';
                lazyBackground.removeAttribute('data-bg');
            });
        }
    });
    </script>
    <?php
}
add_action('wp_footer', 'adprint_lazy_load_background_images');
