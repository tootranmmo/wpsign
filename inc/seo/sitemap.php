<?php
/**
 * XML Sitemap Generator with Images
 *
 * @package AdPrint_Blog
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Generate XML Sitemap
 */
function adprint_generate_sitemap() {
    // Check if request is for sitemap
    if (isset($_GET['adprint_sitemap'])) {
        $type = sanitize_text_field($_GET['adprint_sitemap']);

        header('Content-Type: application/xml; charset=utf-8');

        switch ($type) {
            case 'index':
                adprint_sitemap_index();
                break;
            case 'posts':
                adprint_sitemap_posts();
                break;
            case 'pages':
                adprint_sitemap_pages();
                break;
            case 'categories':
                adprint_sitemap_categories();
                break;
            default:
                adprint_sitemap_index();
                break;
        }

        exit;
    }
}
add_action('init', 'adprint_generate_sitemap');

/**
 * Sitemap Index
 */
function adprint_sitemap_index() {
    echo '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
    echo '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

    // Posts sitemap
    echo '  <sitemap>' . PHP_EOL;
    echo '    <loc>' . home_url('/?adprint_sitemap=posts') . '</loc>' . PHP_EOL;
    echo '    <lastmod>' . get_lastpostmodified('GMT') . '</lastmod>' . PHP_EOL;
    echo '  </sitemap>' . PHP_EOL;

    // Pages sitemap
    echo '  <sitemap>' . PHP_EOL;
    echo '    <loc>' . home_url('/?adprint_sitemap=pages') . '</loc>' . PHP_EOL;
    echo '    <lastmod>' . current_time('mysql') . '</lastmod>' . PHP_EOL;
    echo '  </sitemap>' . PHP_EOL;

    // Categories sitemap
    echo '  <sitemap>' . PHP_EOL;
    echo '    <loc>' . home_url('/?adprint_sitemap=categories') . '</loc>' . PHP_EOL;
    echo '    <lastmod>' . current_time('mysql') . '</lastmod>' . PHP_EOL;
    echo '  </sitemap>' . PHP_EOL;

    echo '</sitemapindex>';
}

/**
 * Posts Sitemap with Images
 */
function adprint_sitemap_posts() {
    $posts = get_posts(array(
        'post_type' => 'post',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'orderby' => 'modified',
        'order' => 'DESC',
    ));

    echo '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" ';
    echo 'xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . PHP_EOL;

    foreach ($posts as $post) {
        echo '  <url>' . PHP_EOL;
        echo '    <loc>' . get_permalink($post->ID) . '</loc>' . PHP_EOL;
        echo '    <lastmod>' . get_the_modified_date('c', $post->ID) . '</lastmod>' . PHP_EOL;
        echo '    <changefreq>weekly</changefreq>' . PHP_EOL;
        echo '    <priority>0.8</priority>' . PHP_EOL;

        // Add featured image
        if (has_post_thumbnail($post->ID)) {
            $image_id = get_post_thumbnail_id($post->ID);
            $image_url = wp_get_attachment_image_url($image_id, 'full');
            $image_title = get_the_title($image_id);

            echo '    <image:image>' . PHP_EOL;
            echo '      <image:loc>' . esc_url($image_url) . '</image:loc>' . PHP_EOL;
            echo '      <image:title>' . esc_html($image_title) . '</image:title>' . PHP_EOL;
            echo '      <image:caption>' . esc_html(get_the_title($post->ID)) . '</image:caption>' . PHP_EOL;
            echo '    </image:image>' . PHP_EOL;
        }

        // Add content images
        $content = $post->post_content;
        preg_match_all('/<img[^>]+src=[\'"]([^\'"]+)[\'"][^>]*>/i', $content, $matches);

        if (!empty($matches[1])) {
            $image_urls = array_slice(array_unique($matches[1]), 0, 5); // Max 5 images per post

            foreach ($image_urls as $img_url) {
                // Skip if it's the featured image
                if (has_post_thumbnail($post->ID)) {
                    $featured_url = wp_get_attachment_image_url(get_post_thumbnail_id($post->ID), 'full');
                    if ($img_url === $featured_url) {
                        continue;
                    }
                }

                echo '    <image:image>' . PHP_EOL;
                echo '      <image:loc>' . esc_url($img_url) . '</image:loc>' . PHP_EOL;
                echo '      <image:title>' . esc_html(get_the_title($post->ID)) . '</image:title>' . PHP_EOL;
                echo '    </image:image>' . PHP_EOL;
            }
        }

        echo '  </url>' . PHP_EOL;
    }

    echo '</urlset>';
}

/**
 * Pages Sitemap
 */
function adprint_sitemap_pages() {
    $pages = get_posts(array(
        'post_type' => 'page',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'orderby' => 'modified',
        'order' => 'DESC',
    ));

    echo '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" ';
    echo 'xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . PHP_EOL;

    // Homepage
    echo '  <url>' . PHP_EOL;
    echo '    <loc>' . home_url('/') . '</loc>' . PHP_EOL;
    echo '    <lastmod>' . current_time('c') . '</lastmod>' . PHP_EOL;
    echo '    <changefreq>daily</changefreq>' . PHP_EOL;
    echo '    <priority>1.0</priority>' . PHP_EOL;
    echo '  </url>' . PHP_EOL;

    foreach ($pages as $page) {
        echo '  <url>' . PHP_EOL;
        echo '    <loc>' . get_permalink($page->ID) . '</loc>' . PHP_EOL;
        echo '    <lastmod>' . get_the_modified_date('c', $page->ID) . '</lastmod>' . PHP_EOL;
        echo '    <changefreq>monthly</changefreq>' . PHP_EOL;
        echo '    <priority>0.6</priority>' . PHP_EOL;

        // Add featured image if available
        if (has_post_thumbnail($page->ID)) {
            $image_id = get_post_thumbnail_id($page->ID);
            $image_url = wp_get_attachment_image_url($image_id, 'full');
            $image_title = get_the_title($image_id);

            echo '    <image:image>' . PHP_EOL;
            echo '      <image:loc>' . esc_url($image_url) . '</image:loc>' . PHP_EOL;
            echo '      <image:title>' . esc_html($image_title) . '</image:title>' . PHP_EOL;
            echo '    </image:image>' . PHP_EOL;
        }

        echo '  </url>' . PHP_EOL;
    }

    echo '</urlset>';
}

/**
 * Categories Sitemap
 */
function adprint_sitemap_categories() {
    $categories = get_categories(array(
        'hide_empty' => true,
    ));

    echo '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

    foreach ($categories as $category) {
        echo '  <url>' . PHP_EOL;
        echo '    <loc>' . get_category_link($category->term_id) . '</loc>' . PHP_EOL;
        echo '    <changefreq>weekly</changefreq>' . PHP_EOL;
        echo '    <priority>0.7</priority>' . PHP_EOL;
        echo '  </url>' . PHP_EOL;
    }

    echo '</urlset>';
}

/**
 * Add sitemap to robots.txt
 */
function adprint_robots_txt($output) {
    $sitemap_url = home_url('/?adprint_sitemap=index');
    $output .= "Sitemap: {$sitemap_url}\n";
    return $output;
}
add_filter('robots_txt', 'adprint_robots_txt');
