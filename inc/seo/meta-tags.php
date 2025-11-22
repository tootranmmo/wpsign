<?php
/**
 * SEO Meta Tags - Open Graph & Twitter Cards
 *
 * @package AdPrint_Blog
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Output Open Graph and Twitter Card meta tags
 */
function adprint_meta_tags() {
    global $post;

    // Canonical URL
    $canonical_url = get_permalink();
    if (is_home() || is_front_page()) {
        $canonical_url = home_url('/');
    } elseif (is_category()) {
        $canonical_url = get_category_link(get_queried_object_id());
    } elseif (is_tag()) {
        $canonical_url = get_tag_link(get_queried_object_id());
    } elseif (is_author()) {
        $canonical_url = get_author_posts_url(get_queried_object_id());
    }

    echo '<link rel="canonical" href="' . esc_url($canonical_url) . '">' . PHP_EOL;

    // Meta Description
    $meta_description = '';
    if (is_singular('post')) {
        $meta_description = get_the_excerpt() ? wp_trim_words(get_the_excerpt(), 25, '...') : wp_trim_words(get_the_content(), 25, '...');
    } elseif (is_home() || is_front_page()) {
        $meta_description = get_bloginfo('description');
    } elseif (is_category()) {
        $category = get_queried_object();
        $meta_description = $category->description ? wp_trim_words($category->description, 25, '...') : get_bloginfo('description');
    } elseif (is_tag()) {
        $tag = get_queried_object();
        $meta_description = $tag->description ? wp_trim_words($tag->description, 25, '...') : get_bloginfo('description');
    } elseif (is_author()) {
        $meta_description = get_the_author_meta('description', get_queried_object_id());
    }

    if ($meta_description) {
        echo '<meta name="description" content="' . esc_attr(wp_strip_all_tags($meta_description)) . '">' . PHP_EOL;
    }

    // Meta Robots
    if (is_singular()) {
        echo '<meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">' . PHP_EOL;
    } elseif (is_category() || is_tag() || is_archive()) {
        echo '<meta name="robots" content="index, follow">' . PHP_EOL;
    } elseif (is_search()) {
        echo '<meta name="robots" content="noindex, follow">' . PHP_EOL;
    }

    // Open Graph Tags
    adprint_open_graph_tags();

    // Twitter Card Tags
    adprint_twitter_card_tags();
}
add_action('wp_head', 'adprint_meta_tags', 1);

/**
 * Open Graph Tags
 */
function adprint_open_graph_tags() {
    global $post;

    $og_type = 'website';
    $og_title = get_bloginfo('name');
    $og_description = get_bloginfo('description');
    $og_url = home_url('/');
    $og_image = ADPRINT_THEME_URI . '/assets/images/og-default.jpg';
    $og_image_width = 1200;
    $og_image_height = 630;

    if (is_singular('post')) {
        $og_type = 'article';
        $og_title = get_the_title();
        $og_description = get_the_excerpt();
        $og_url = get_permalink();

        if (has_post_thumbnail()) {
            $image_id = get_post_thumbnail_id();
            $image_data = wp_get_attachment_image_src($image_id, 'adprint-large');
            $og_image = $image_data[0];
            $og_image_width = $image_data[1];
            $og_image_height = $image_data[2];
        }
    } elseif (is_category()) {
        $category = get_queried_object();
        $og_title = $category->name . ' - ' . get_bloginfo('name');
        $og_description = $category->description ? $category->description : get_bloginfo('description');
        $og_url = get_category_link($category->term_id);
    } elseif (is_tag()) {
        $tag = get_queried_object();
        $og_title = $tag->name . ' - ' . get_bloginfo('name');
        $og_description = $tag->description ? $tag->description : get_bloginfo('description');
        $og_url = get_tag_link($tag->term_id);
    } elseif (is_author()) {
        $author_id = get_queried_object_id();
        $og_title = get_the_author_meta('display_name', $author_id) . ' - ' . get_bloginfo('name');
        $og_description = get_the_author_meta('description', $author_id);
        $og_url = get_author_posts_url($author_id);
        $og_image = get_avatar_url($author_id, array('size' => 1200));
    }

    // Output Open Graph tags
    echo '<meta property="og:locale" content="' . esc_attr(get_locale()) . '">' . PHP_EOL;
    echo '<meta property="og:type" content="' . esc_attr($og_type) . '">' . PHP_EOL;
    echo '<meta property="og:title" content="' . esc_attr($og_title) . '">' . PHP_EOL;
    echo '<meta property="og:description" content="' . esc_attr(wp_strip_all_tags($og_description)) . '">' . PHP_EOL;
    echo '<meta property="og:url" content="' . esc_url($og_url) . '">' . PHP_EOL;
    echo '<meta property="og:site_name" content="' . esc_attr(get_bloginfo('name')) . '">' . PHP_EOL;
    echo '<meta property="og:image" content="' . esc_url($og_image) . '">' . PHP_EOL;
    echo '<meta property="og:image:width" content="' . esc_attr($og_image_width) . '">' . PHP_EOL;
    echo '<meta property="og:image:height" content="' . esc_attr($og_image_height) . '">' . PHP_EOL;
    echo '<meta property="og:image:type" content="image/jpeg">' . PHP_EOL;

    // Article specific tags
    if (is_singular('post')) {
        echo '<meta property="article:published_time" content="' . esc_attr(get_the_date('c')) . '">' . PHP_EOL;
        echo '<meta property="article:modified_time" content="' . esc_attr(get_the_modified_date('c')) . '">' . PHP_EOL;
        echo '<meta property="article:author" content="' . esc_attr(get_author_posts_url(get_post_field('post_author'))) . '">' . PHP_EOL;

        // Categories
        $categories = get_the_category();
        foreach ($categories as $category) {
            echo '<meta property="article:section" content="' . esc_attr($category->name) . '">' . PHP_EOL;
        }

        // Tags
        $tags = get_the_tags();
        if ($tags) {
            foreach ($tags as $tag) {
                echo '<meta property="article:tag" content="' . esc_attr($tag->name) . '">' . PHP_EOL;
            }
        }
    }

    // Facebook App ID (if set)
    $fb_app_id = get_theme_mod('adprint_facebook_app_id');
    if ($fb_app_id) {
        echo '<meta property="fb:app_id" content="' . esc_attr($fb_app_id) . '">' . PHP_EOL;
    }
}

/**
 * Twitter Card Tags
 */
function adprint_twitter_card_tags() {
    global $post;

    $twitter_card = 'summary_large_image';
    $twitter_title = get_bloginfo('name');
    $twitter_description = get_bloginfo('description');
    $twitter_image = ADPRINT_THEME_URI . '/assets/images/og-default.jpg';

    if (is_singular('post')) {
        $twitter_title = get_the_title();
        $twitter_description = get_the_excerpt();

        if (has_post_thumbnail()) {
            $image_id = get_post_thumbnail_id();
            $image_data = wp_get_attachment_image_src($image_id, 'adprint-large');
            $twitter_image = $image_data[0];
        }
    } elseif (is_category()) {
        $category = get_queried_object();
        $twitter_title = $category->name . ' - ' . get_bloginfo('name');
        $twitter_description = $category->description ? $category->description : get_bloginfo('description');
    } elseif (is_tag()) {
        $tag = get_queried_object();
        $twitter_title = $tag->name . ' - ' . get_bloginfo('name');
        $twitter_description = $tag->description ? $tag->description : get_bloginfo('description');
    } elseif (is_author()) {
        $author_id = get_queried_object_id();
        $twitter_title = get_the_author_meta('display_name', $author_id) . ' - ' . get_bloginfo('name');
        $twitter_description = get_the_author_meta('description', $author_id);
        $twitter_image = get_avatar_url($author_id, array('size' => 1200));
    }

    // Output Twitter Card tags
    echo '<meta name="twitter:card" content="' . esc_attr($twitter_card) . '">' . PHP_EOL;
    echo '<meta name="twitter:title" content="' . esc_attr($twitter_title) . '">' . PHP_EOL;
    echo '<meta name="twitter:description" content="' . esc_attr(wp_strip_all_tags($twitter_description)) . '">' . PHP_EOL;
    echo '<meta name="twitter:image" content="' . esc_url($twitter_image) . '">' . PHP_EOL;

    // Twitter username (if set)
    $twitter_username = get_theme_mod('adprint_twitter_username');
    if ($twitter_username) {
        echo '<meta name="twitter:site" content="@' . esc_attr(str_replace('@', '', $twitter_username)) . '">' . PHP_EOL;
        echo '<meta name="twitter:creator" content="@' . esc_attr(str_replace('@', '', $twitter_username)) . '">' . PHP_EOL;
    }

    // Author Twitter (if available)
    if (is_singular('post')) {
        $author_twitter = get_the_author_meta('twitter');
        if ($author_twitter) {
            echo '<meta name="twitter:creator" content="@' . esc_attr(str_replace('@', '', $author_twitter)) . '">' . PHP_EOL;
        }
    }
}
