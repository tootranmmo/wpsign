<?php
/**
 * WebP Image Support (28% smaller)
 *
 * @package AdPrint_Blog
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enable WebP upload support
 */
function adprint_webp_upload_mimes($mimes) {
    $mimes['webp'] = 'image/webp';
    return $mimes;
}
add_filter('mime_types', 'adprint_webp_upload_mimes');

/**
 * Add WebP to allowed file types
 */
function adprint_webp_file_types($types, $file, $filename, $mimes) {
    if (false !== strpos($filename, '.webp')) {
        $types['ext'] = 'webp';
        $types['type'] = 'image/webp';
    }
    return $types;
}
add_filter('wp_check_filetype_and_ext', 'adprint_webp_file_types', 10, 4);

/**
 * Generate WebP version when image is uploaded
 */
function adprint_generate_webp_on_upload($metadata, $attachment_id) {
    $file = get_attached_file($attachment_id);

    if (!file_exists($file)) {
        return $metadata;
    }

    // Check if file is an image
    $file_type = wp_check_filetype($file);
    $supported_types = array('image/jpeg', 'image/jpg', 'image/png');

    if (!in_array($file_type['type'], $supported_types)) {
        return $metadata;
    }

    // Generate WebP for main image
    adprint_create_webp_image($file);

    // Generate WebP for all image sizes
    if (isset($metadata['sizes']) && is_array($metadata['sizes'])) {
        foreach ($metadata['sizes'] as $size => $size_data) {
            $size_file = path_join(dirname($file), $size_data['file']);
            if (file_exists($size_file)) {
                adprint_create_webp_image($size_file);
            }
        }
    }

    return $metadata;
}
add_filter('wp_generate_attachment_metadata', 'adprint_generate_webp_on_upload', 10, 2);

/**
 * Create WebP version of image
 */
function adprint_create_webp_image($file) {
    if (!file_exists($file)) {
        return false;
    }

    $file_type = wp_check_filetype($file);
    $output = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $file);

    // Check if WebP already exists
    if (file_exists($output)) {
        return true;
    }

    // Load image based on type
    $image = null;

    switch ($file_type['type']) {
        case 'image/jpeg':
        case 'image/jpg':
            $image = @imagecreatefromjpeg($file);
            break;
        case 'image/png':
            $image = @imagecreatefrompng($file);
            // Preserve transparency
            if ($image) {
                imagealphablending($image, true);
                imagesavealpha($image, true);
            }
            break;
    }

    if (!$image) {
        return false;
    }

    // Create WebP image with 85% quality (optimal balance)
    $success = imagewebp($image, $output, 85);

    // Free memory
    imagedestroy($image);

    return $success;
}

/**
 * Serve WebP images when available
 */
function adprint_serve_webp_images($image, $attachment_id, $size, $icon) {
    // Check if browser supports WebP
    if (!adprint_browser_supports_webp()) {
        return $image;
    }

    if (!is_array($image)) {
        return $image;
    }

    $webp_url = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $image[0]);
    $webp_file = str_replace(wp_get_upload_dir()['baseurl'], wp_get_upload_dir()['basedir'], $webp_url);

    // Check if WebP file exists
    if (file_exists($webp_file)) {
        $image[0] = $webp_url;
        $image['mime-type'] = 'image/webp';
    }

    return $image;
}
add_filter('wp_get_attachment_image_src', 'adprint_serve_webp_images', 10, 4);

/**
 * Check if browser supports WebP
 */
function adprint_browser_supports_webp() {
    if (!isset($_SERVER['HTTP_ACCEPT'])) {
        return false;
    }

    return strpos($_SERVER['HTTP_ACCEPT'], 'image/webp') !== false;
}

/**
 * Add picture element with WebP fallback
 */
function adprint_picture_element($html, $attachment_id, $size, $icon, $attr) {
    // Check if we should use picture element
    if (!apply_filters('adprint_use_picture_element', true)) {
        return $html;
    }

    $image = wp_get_attachment_image_src($attachment_id, $size);

    if (!$image) {
        return $html;
    }

    $webp_url = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $image[0]);
    $webp_file = str_replace(wp_get_upload_dir()['baseurl'], wp_get_upload_dir()['basedir'], $webp_url);

    // Only use picture element if WebP exists
    if (!file_exists($webp_file)) {
        return $html;
    }

    // Get image attributes
    $alt = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);
    $title = get_the_title($attachment_id);
    $class = isset($attr['class']) ? $attr['class'] : '';
    $loading = isset($attr['loading']) ? $attr['loading'] : 'lazy';

    // Build picture element
    $picture = '<picture>';
    $picture .= '<source srcset="' . esc_url($webp_url) . '" type="image/webp">';
    $picture .= '<img src="' . esc_url($image[0]) . '" ';
    $picture .= 'alt="' . esc_attr($alt) . '" ';
    $picture .= 'title="' . esc_attr($title) . '" ';
    $picture .= 'class="' . esc_attr($class) . '" ';
    $picture .= 'width="' . esc_attr($image[1]) . '" ';
    $picture .= 'height="' . esc_attr($image[2]) . '" ';
    $picture .= 'loading="' . esc_attr($loading) . '">';
    $picture .= '</picture>';

    return $picture;
}
// Uncomment to enable picture element with WebP fallback
// add_filter('wp_get_attachment_image', 'adprint_picture_element', 10, 5);

/**
 * Display WebP file size savings
 */
function adprint_webp_savings($attachment_id) {
    $file = get_attached_file($attachment_id);
    $webp_file = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $file);

    if (!file_exists($file) || !file_exists($webp_file)) {
        return null;
    }

    $original_size = filesize($file);
    $webp_size = filesize($webp_file);
    $savings = round((($original_size - $webp_size) / $original_size) * 100, 1);

    return array(
        'original_size' => $original_size,
        'webp_size' => $webp_size,
        'savings' => $savings,
        'savings_bytes' => $original_size - $webp_size,
    );
}
