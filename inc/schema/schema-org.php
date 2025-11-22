<?php
/**
 * Schema.org Markup
 *
 * @package AdPrint_Blog
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Output Schema.org JSON-LD markup
 */
function adprint_schema_markup() {
    $schema = array();

    // 1. Organization Schema
    $schema[] = adprint_schema_organization();

    // 2. WebSite Schema
    $schema[] = adprint_schema_website();

    if (is_singular('post')) {
        // 3. Article Schema
        $schema[] = adprint_schema_article();

        // 4. BreadcrumbList Schema
        $schema[] = adprint_schema_breadcrumb();

        // 5. Review/Rating Schema (if post has rating)
        if (get_post_meta(get_the_ID(), 'adprint_rating', true)) {
            $schema[] = adprint_schema_review();
        }
    }

    if (is_front_page()) {
        // 6. FAQPage Schema
        $schema[] = adprint_schema_faq();
    }

    // 7. Person Schema (Author)
    if (is_author() || is_singular('post')) {
        $schema[] = adprint_schema_person();
    }

    // Output Schema markup
    if (!empty($schema)) {
        echo '<script type="application/ld+json">' . PHP_EOL;
        echo wp_json_encode(array('@graph' => $schema), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        echo PHP_EOL . '</script>' . PHP_EOL;
    }
}
add_action('wp_head', 'adprint_schema_markup');

/**
 * Organization Schema
 */
function adprint_schema_organization() {
    $logo = get_theme_mod('custom_logo');
    $logo_url = $logo ? wp_get_attachment_image_url($logo, 'full') : ADPRINT_THEME_URI . '/assets/images/logo.png';

    return array(
        '@type' => 'Organization',
        '@id' => home_url() . '/#organization',
        'name' => get_bloginfo('name'),
        'url' => home_url(),
        'logo' => array(
            '@type' => 'ImageObject',
            'url' => $logo_url,
            'width' => 400,
            'height' => 100,
        ),
        'description' => get_bloginfo('description'),
        'sameAs' => adprint_get_social_profiles(),
    );
}

/**
 * WebSite Schema
 */
function adprint_schema_website() {
    return array(
        '@type' => 'WebSite',
        '@id' => home_url() . '/#website',
        'url' => home_url(),
        'name' => get_bloginfo('name'),
        'description' => get_bloginfo('description'),
        'publisher' => array(
            '@id' => home_url() . '/#organization',
        ),
        'potentialAction' => array(
            '@type' => 'SearchAction',
            'target' => array(
                '@type' => 'EntryPoint',
                'urlTemplate' => home_url() . '/?s={search_term_string}',
            ),
            'query-input' => 'required name=search_term_string',
        ),
    );
}

/**
 * Article Schema
 */
function adprint_schema_article() {
    $post_id = get_the_ID();
    $author_id = get_post_field('post_author', $post_id);

    $schema = array(
        '@type' => 'BlogPosting',
        '@id' => get_permalink() . '#article',
        'headline' => get_the_title(),
        'description' => get_the_excerpt(),
        'url' => get_permalink(),
        'datePublished' => get_the_date('c'),
        'dateModified' => get_the_modified_date('c'),
        'author' => array(
            '@type' => 'Person',
            '@id' => get_author_posts_url($author_id) . '#author',
            'name' => get_the_author_meta('display_name', $author_id),
            'url' => get_author_posts_url($author_id),
        ),
        'publisher' => array(
            '@id' => home_url() . '/#organization',
        ),
        'mainEntityOfPage' => array(
            '@type' => 'WebPage',
            '@id' => get_permalink(),
        ),
        'wordCount' => str_word_count(strip_tags(get_post_field('post_content', $post_id))),
        'commentCount' => get_comments_number(),
        'inLanguage' => get_bloginfo('language'),
    );

    // Add image if available
    if (has_post_thumbnail()) {
        $image_id = get_post_thumbnail_id();
        $image_data = wp_get_attachment_image_src($image_id, 'full');

        $schema['image'] = array(
            '@type' => 'ImageObject',
            'url' => $image_data[0],
            'width' => $image_data[1],
            'height' => $image_data[2],
        );
    }

    return $schema;
}

/**
 * BreadcrumbList Schema
 */
function adprint_schema_breadcrumb() {
    $items = array(
        array(
            '@type' => 'ListItem',
            'position' => 1,
            'name' => __('Home', 'adprint-blog'),
            'item' => home_url(),
        ),
    );

    if (is_single()) {
        $categories = get_the_category();
        if (!empty($categories)) {
            $category = $categories[0];
            $items[] = array(
                '@type' => 'ListItem',
                'position' => 2,
                'name' => $category->name,
                'item' => get_category_link($category->term_id),
            );
        }

        $items[] = array(
            '@type' => 'ListItem',
            'position' => count($items) + 1,
            'name' => get_the_title(),
            'item' => get_permalink(),
        );
    }

    return array(
        '@type' => 'BreadcrumbList',
        '@id' => get_permalink() . '#breadcrumb',
        'itemListElement' => $items,
    );
}

/**
 * Review Schema
 */
function adprint_schema_review() {
    $post_id = get_the_ID();
    $rating = get_post_meta($post_id, 'adprint_rating', true);

    if (!$rating) {
        return array();
    }

    return array(
        '@type' => 'Review',
        '@id' => get_permalink() . '#review',
        'itemReviewed' => array(
            '@type' => 'Thing',
            'name' => get_the_title(),
        ),
        'reviewRating' => array(
            '@type' => 'Rating',
            'ratingValue' => $rating,
            'bestRating' => 5,
            'worstRating' => 1,
        ),
        'author' => array(
            '@type' => 'Person',
            'name' => get_the_author(),
        ),
        'datePublished' => get_the_date('c'),
    );
}

/**
 * FAQPage Schema
 */
function adprint_schema_faq() {
    $faqs = get_theme_mod('adprint_faqs', array());

    if (empty($faqs)) {
        // Default FAQs for advertising and printing blog
        $faqs = array(
            array(
                'question' => 'Dịch vụ in ấn của bạn có những loại nào?',
                'answer' => 'Chúng tôi cung cấp đa dạng các dịch vụ in ấn bao gồm in offset, in kỹ thuật số, in UV, in lụa và nhiều loại hình in ấn chuyên nghiệp khác phục vụ cho nhu cầu quảng cáo và marketing.',
            ),
            array(
                'question' => 'Thời gian hoàn thành đơn hàng in ấn là bao lâu?',
                'answer' => 'Thời gian hoàn thành phụ thuộc vào loại sản phẩm và số lượng. Thông thường, đơn hàng nhỏ có thể hoàn thành trong 2-3 ngày, đơn hàng lớn từ 5-7 ngày làm việc.',
            ),
            array(
                'question' => 'Làm thế nào để đặt hàng dịch vụ quảng cáo?',
                'answer' => 'Bạn có thể liên hệ trực tiếp qua hotline, email hoặc sử dụng form liên hệ trên website. Đội ngũ tư vấn của chúng tôi sẽ hỗ trợ bạn 24/7.',
            ),
            array(
                'question' => 'Bạn có hỗ trợ thiết kế miễn phí không?',
                'answer' => 'Có, chúng tôi cung cấp dịch vụ tư vấn và thiết kế miễn phí cho các đơn hàng có giá trị từ 5 triệu đồng trở lên.',
            ),
        );
    }

    if (empty($faqs)) {
        return array();
    }

    $faq_items = array();
    foreach ($faqs as $faq) {
        if (isset($faq['question']) && isset($faq['answer'])) {
            $faq_items[] = array(
                '@type' => 'Question',
                'name' => $faq['question'],
                'acceptedAnswer' => array(
                    '@type' => 'Answer',
                    'text' => $faq['answer'],
                ),
            );
        }
    }

    return array(
        '@type' => 'FAQPage',
        '@id' => home_url() . '/#faq',
        'mainEntity' => $faq_items,
    );
}

/**
 * Person Schema
 */
function adprint_schema_person() {
    if (is_author()) {
        $author_id = get_queried_object_id();
    } elseif (is_singular('post')) {
        $author_id = get_post_field('post_author', get_the_ID());
    } else {
        return array();
    }

    return array(
        '@type' => 'Person',
        '@id' => get_author_posts_url($author_id) . '#author',
        'name' => get_the_author_meta('display_name', $author_id),
        'url' => get_author_posts_url($author_id),
        'description' => get_the_author_meta('description', $author_id),
        'image' => array(
            '@type' => 'ImageObject',
            'url' => get_avatar_url($author_id, array('size' => 96)),
            'width' => 96,
            'height' => 96,
        ),
    );
}

/**
 * Get social media profiles
 */
function adprint_get_social_profiles() {
    $profiles = array();

    $social_links = array(
        'facebook' => get_theme_mod('adprint_facebook_url'),
        'twitter' => get_theme_mod('adprint_twitter_url'),
        'instagram' => get_theme_mod('adprint_instagram_url'),
        'linkedin' => get_theme_mod('adprint_linkedin_url'),
        'youtube' => get_theme_mod('adprint_youtube_url'),
    );

    foreach ($social_links as $profile) {
        if (!empty($profile)) {
            $profiles[] = $profile;
        }
    }

    return $profiles;
}
