<?php
/**
 * Custom template tags
 *
 * @package AdPrint_Blog
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Display post meta (date, author, comments)
 */
function adprint_post_meta() {
    ?>
    <div class="post-meta flex items-center gap-4 text-sm text-gray-600 dark:text-gray-400">
        <time datetime="<?php echo esc_attr(get_the_date('c')); ?>" class="flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <?php echo esc_html(get_the_date()); ?>
        </time>

        <span class="flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            <a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>" class="hover:text-primary-600">
                <?php the_author(); ?>
            </a>
        </span>

        <?php if (comments_open() || get_comments_number()) : ?>
        <span class="flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
            </svg>
            <a href="<?php comments_link(); ?>" class="hover:text-primary-600">
                <?php printf(_n('%s Comment', '%s Comments', get_comments_number(), 'adprint-blog'), number_format_i18n(get_comments_number())); ?>
            </a>
        </span>
        <?php endif; ?>

        <span class="flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
            </svg>
            <?php echo esc_html(number_format_i18n(adprint_get_post_views(get_the_ID()))); ?> <?php esc_html_e('views', 'adprint-blog'); ?>
        </span>
    </div>
    <?php
}

/**
 * Display post categories
 */
function adprint_post_categories() {
    $categories = get_the_category();

    if (empty($categories)) {
        return;
    }

    echo '<div class="post-categories flex flex-wrap gap-2 mb-4">';

    foreach ($categories as $category) {
        printf(
            '<a href="%s" class="inline-block px-3 py-1 text-sm font-medium bg-primary-100 dark:bg-primary-900 text-primary-800 dark:text-primary-200 rounded-full hover:bg-primary-200 dark:hover:bg-primary-800 transition-colors">%s</a>',
            esc_url(get_category_link($category->term_id)),
            esc_html($category->name)
        );
    }

    echo '</div>';
}

/**
 * Display post tags
 */
function adprint_post_tags() {
    $tags = get_the_tags();

    if (empty($tags)) {
        return;
    }

    echo '<div class="post-tags flex flex-wrap gap-2 mt-6">';
    echo '<span class="text-sm font-semibold text-gray-700 dark:text-gray-300">' . esc_html__('Tags:', 'adprint-blog') . '</span>';

    foreach ($tags as $tag) {
        printf(
            '<a href="%s" class="inline-block px-3 py-1 text-sm bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">#%s</a>',
            esc_url(get_tag_link($tag->term_id)),
            esc_html($tag->name)
        );
    }

    echo '</div>';
}

/**
 * Display reading time
 */
function adprint_reading_time() {
    $content = get_post_field('post_content', get_the_ID());
    $word_count = str_word_count(strip_tags($content));
    $reading_time = ceil($word_count / 200); // Average reading speed: 200 words/minute

    printf(
        '<span class="reading-time flex items-center gap-1 text-sm text-gray-600 dark:text-gray-400">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            %s %s
        </span>',
        esc_html($reading_time),
        esc_html(_n('min read', 'mins read', $reading_time, 'adprint-blog'))
    );
}

/**
 * Display star rating
 */
function adprint_star_rating($rating = null, $post_id = null) {
    if ($post_id === null) {
        $post_id = get_the_ID();
    }

    if ($rating === null) {
        $rating = get_post_meta($post_id, 'adprint_rating', true);
    }

    if (!$rating) {
        return;
    }

    $rating = floatval($rating);
    $full_stars = floor($rating);
    $half_star = ($rating - $full_stars) >= 0.5;
    $empty_stars = 5 - $full_stars - ($half_star ? 1 : 0);

    echo '<div class="rating-stars flex items-center gap-1" role="img" aria-label="' . sprintf(esc_attr__('Rating: %s out of 5 stars', 'adprint-blog'), $rating) . '">';

    // Full stars
    for ($i = 0; $i < $full_stars; $i++) {
        echo '<svg class="w-5 h-5 text-accent-500 fill-current" viewBox="0 0 20 20" aria-hidden="true">
            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
        </svg>';
    }

    // Half star
    if ($half_star) {
        echo '<svg class="w-5 h-5 text-accent-500" viewBox="0 0 20 20" aria-hidden="true">
            <defs>
                <linearGradient id="half-star">
                    <stop offset="50%" stop-color="currentColor" stop-opacity="1"/>
                    <stop offset="50%" stop-color="currentColor" stop-opacity="0.3"/>
                </linearGradient>
            </defs>
            <path fill="url(#half-star)" d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
        </svg>';
    }

    // Empty stars
    for ($i = 0; $i < $empty_stars; $i++) {
        echo '<svg class="w-5 h-5 text-gray-300 dark:text-gray-600" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
        </svg>';
    }

    echo '<span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">' . number_format($rating, 1) . '</span>';
    echo '</div>';
}

/**
 * Display breadcrumbs
 */
function adprint_breadcrumbs() {
    if (is_front_page()) {
        return;
    }

    echo '<nav class="breadcrumbs text-sm mb-6" aria-label="' . esc_attr__('Breadcrumb', 'adprint-blog') . '">';
    echo '<ol class="flex items-center gap-2 text-gray-600 dark:text-gray-400">';

    // Home
    echo '<li><a href="' . esc_url(home_url('/')) . '" class="hover:text-primary-600">' . esc_html__('Home', 'adprint-blog') . '</a></li>';
    echo '<li><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg></li>';

    if (is_category() || is_single()) {
        $categories = get_the_category();
        if (!empty($categories)) {
            $category = $categories[0];
            echo '<li><a href="' . esc_url(get_category_link($category->term_id)) . '" class="hover:text-primary-600">' . esc_html($category->name) . '</a></li>';

            if (is_single()) {
                echo '<li><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg></li>';
                echo '<li class="text-gray-900 dark:text-gray-100 font-medium" aria-current="page">' . esc_html(get_the_title()) . '</li>';
            }
        }
    } elseif (is_page()) {
        echo '<li class="text-gray-900 dark:text-gray-100 font-medium" aria-current="page">' . esc_html(get_the_title()) . '</li>';
    } elseif (is_tag()) {
        echo '<li class="text-gray-900 dark:text-gray-100 font-medium" aria-current="page">' . esc_html(single_tag_title('', false)) . '</li>';
    } elseif (is_author()) {
        echo '<li class="text-gray-900 dark:text-gray-100 font-medium" aria-current="page">' . esc_html(get_the_author()) . '</li>';
    } elseif (is_search()) {
        echo '<li class="text-gray-900 dark:text-gray-100 font-medium" aria-current="page">' . esc_html__('Search Results', 'adprint-blog') . '</li>';
    }

    echo '</ol>';
    echo '</nav>';
}

/**
 * Display social share buttons
 */
function adprint_social_share() {
    $url = urlencode(get_permalink());
    $title = urlencode(get_the_title());

    ?>
    <div class="social-share flex items-center gap-3 mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
        <span class="text-sm font-semibold text-gray-700 dark:text-gray-300"><?php esc_html_e('Share:', 'adprint-blog'); ?></span>

        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $url; ?>" target="_blank" rel="noopener noreferrer" class="p-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors" aria-label="<?php esc_attr_e('Share on Facebook', 'adprint-blog'); ?>">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
        </a>

        <a href="https://twitter.com/intent/tweet?url=<?php echo $url; ?>&text=<?php echo $title; ?>" target="_blank" rel="noopener noreferrer" class="p-2 bg-sky-500 text-white rounded hover:bg-sky-600 transition-colors" aria-label="<?php esc_attr_e('Share on Twitter', 'adprint-blog'); ?>">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
        </a>

        <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo $url; ?>&title=<?php echo $title; ?>" target="_blank" rel="noopener noreferrer" class="p-2 bg-blue-700 text-white rounded hover:bg-blue-800 transition-colors" aria-label="<?php esc_attr_e('Share on LinkedIn', 'adprint-blog'); ?>">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
        </a>

        <button onclick="navigator.clipboard.writeText('<?php echo esc_js(get_permalink()); ?>')" class="p-2 bg-gray-600 text-white rounded hover:bg-gray-700 transition-colors" aria-label="<?php esc_attr_e('Copy link', 'adprint-blog'); ?>">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
        </button>
    </div>
    <?php
}
