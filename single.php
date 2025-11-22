<?php
/**
 * The template for displaying single posts
 *
 * @package AdPrint_Blog
 * @since 1.0.0
 */

get_header();
?>

<main id="main-content" class="site-main py-12" role="main">
    <div class="container">
        <div class="max-w-4xl mx-auto">
            <?php
            while (have_posts()) :
                the_post();
            ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class('single-post'); ?>>
                    <!-- Breadcrumbs -->
                    <?php adprint_breadcrumbs(); ?>

                    <!-- Post Header -->
                    <header class="post-header mb-8">
                        <?php adprint_post_categories(); ?>

                        <h1 class="post-title text-4xl md:text-5xl font-bold font-display text-gray-900 dark:text-gray-100 mb-4">
                            <?php the_title(); ?>
                        </h1>

                        <div class="flex flex-wrap items-center gap-4 mb-6">
                            <?php adprint_post_meta(); ?>
                            <?php adprint_reading_time(); ?>
                        </div>

                        <?php
                        $rating = get_post_meta(get_the_ID(), 'adprint_rating', true);
                        if ($rating) :
                        ?>
                            <div class="post-rating mb-6">
                                <?php adprint_star_rating($rating); ?>
                            </div>
                        <?php endif; ?>
                    </header>

                    <!-- Featured Image -->
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="post-thumbnail mb-8 rounded-xl overflow-hidden">
                            <?php the_post_thumbnail('adprint-large', array('class' => 'w-full h-auto')); ?>
                        </div>
                    <?php endif; ?>

                    <!-- Post Content -->
                    <div class="post-content prose prose-lg dark:prose-dark max-w-none mb-8">
                        <?php
                        the_content();

                        wp_link_pages(array(
                            'before' => '<div class="page-links mt-8 pt-8 border-t border-gray-200 dark:border-gray-700">' . esc_html__('Pages:', 'adprint-blog'),
                            'after'  => '</div>',
                        ));
                        ?>
                    </div>

                    <!-- Post Tags -->
                    <?php adprint_post_tags(); ?>

                    <!-- Social Share -->
                    <?php adprint_social_share(); ?>

                    <!-- Interactive Rating (if not rated yet) -->
                    <?php if (!get_post_meta(get_the_ID(), 'adprint_rating', true)) : ?>
                        <div class="post-rating-interactive mt-12 p-6 bg-gray-50 dark:bg-gray-800 rounded-xl">
                            <h3 class="text-xl font-bold mb-4 text-gray-900 dark:text-gray-100">
                                <?php esc_html_e('Đánh giá bài viết này', 'adprint-blog'); ?>
                            </h3>
                            <div class="rating-interactive" data-post-id="<?php the_ID(); ?>">
                                <div class="flex gap-2 mb-4">
                                    <?php for ($i = 1; $i <= 5; $i++) : ?>
                                        <button
                                            class="rating-star w-10 h-10 text-gray-300 hover:text-accent-500 transition-colors focus:outline-none focus:ring-2 focus:ring-accent-500 rounded"
                                            data-rating="<?php echo $i; ?>"
                                            tabindex="0"
                                            aria-label="<?php echo esc_attr(sprintf(__('Rate %d stars', 'adprint-blog'), $i)); ?>"
                                        >
                                            <svg fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        </button>
                                    <?php endfor; ?>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    <?php esc_html_e('Click vào sao để đánh giá', 'adprint-blog'); ?>
                                </p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Author Bio -->
                    <div class="author-bio mt-12 p-6 bg-gray-50 dark:bg-gray-800 rounded-xl flex gap-6">
                        <div class="author-avatar flex-shrink-0">
                            <?php echo get_avatar(get_the_author_meta('ID'), 96, '', '', array('class' => 'rounded-full')); ?>
                        </div>
                        <div class="author-info flex-1">
                            <h3 class="text-xl font-bold mb-2 text-gray-900 dark:text-gray-100">
                                <a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>" class="hover:text-primary-600">
                                    <?php the_author(); ?>
                                </a>
                            </h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-4">
                                <?php echo get_the_author_meta('description'); ?>
                            </p>
                            <a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>" class="text-primary-600 hover:text-primary-700 font-medium">
                                <?php esc_html_e('Xem tất cả bài viết →', 'adprint-blog'); ?>
                            </a>
                        </div>
                    </div>

                    <!-- Related Posts -->
                    <?php
                    $categories = get_the_category();
                    if ($categories) {
                        $category_ids = array();
                        foreach ($categories as $category) {
                            $category_ids[] = $category->term_id;
                        }

                        $related_posts = new WP_Query(array(
                            'category__in' => $category_ids,
                            'post__not_in' => array(get_the_ID()),
                            'posts_per_page' => 3,
                            'ignore_sticky_posts' => 1,
                        ));

                        if ($related_posts->have_posts()) :
                    ?>
                        <div class="related-posts mt-16">
                            <h3 class="text-2xl font-bold font-display mb-6 text-gray-900 dark:text-gray-100">
                                <?php esc_html_e('Bài viết liên quan', 'adprint-blog'); ?>
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <?php
                                while ($related_posts->have_posts()) : $related_posts->the_post();
                                ?>
                                    <article class="post-card">
                                        <?php if (has_post_thumbnail()) : ?>
                                            <a href="<?php the_permalink(); ?>">
                                                <?php the_post_thumbnail('adprint-small'); ?>
                                            </a>
                                        <?php endif; ?>

                                        <div class="post-card-content">
                                            <h4 class="text-lg font-bold mb-2">
                                                <a href="<?php the_permalink(); ?>" class="text-gray-900 dark:text-gray-100 hover:text-primary-600">
                                                    <?php the_title(); ?>
                                                </a>
                                            </h4>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                                <?php echo wp_trim_words(get_the_excerpt(), 15); ?>
                                            </p>
                                        </div>
                                    </article>
                                <?php
                                endwhile;
                                wp_reset_postdata();
                                ?>
                            </div>
                        </div>
                    <?php
                        endif;
                    }
                    ?>
                </article>

                <!-- Comments -->
                <?php
                if (comments_open() || get_comments_number()) :
                    comments_template();
                endif;
                ?>

            <?php endwhile; ?>
        </div>
    </div>
</main>

<?php
get_footer();
