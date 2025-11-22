<?php
/**
 * The main template file
 *
 * @package AdPrint_Blog
 * @since 1.0.0
 */

get_header();
?>

<main id="main-content" class="site-main" role="main">

    <?php if (is_home() && is_front_page()) : ?>

        <!-- Hero Section với Search Bar -->
        <section class="hero-section relative bg-gradient-to-br from-primary-500 to-primary-700 text-white py-20 mb-12">
            <div class="container">
                <div class="max-w-4xl mx-auto text-center">
                    <h1 class="hero-title text-4xl md:text-5xl lg:text-6xl font-bold font-display mb-6 animate-fade-in-up">
                        <?php echo esc_html(get_theme_mod('adprint_hero_title', __('Khám Phá Thế Giới Quảng Cáo & In Ấn', 'adprint-blog'))); ?>
                    </h1>

                    <p class="hero-subtitle text-xl md:text-2xl mb-8 text-primary-100 animate-fade-in-up">
                        <?php echo esc_html(get_theme_mod('adprint_hero_subtitle', __('Tin tức, xu hướng và kiến thức chuyên sâu về lĩnh vực quảng cáo và in ấn', 'adprint-blog'))); ?>
                    </p>

                    <!-- Hero Search Bar -->
                    <div class="hero-search animate-fade-in-up">
                        <form role="search" method="get" class="relative" action="<?php echo esc_url(home_url('/')); ?>">
                            <label for="hero-search-input" class="sr-only"><?php esc_html_e('Search for:', 'adprint-blog'); ?></label>
                            <input
                                type="search"
                                id="hero-search-input"
                                name="s"
                                placeholder="<?php esc_attr_e('Tìm kiếm bài viết, xu hướng, kiến thức...', 'adprint-blog'); ?>"
                                value="<?php echo get_search_query(); ?>"
                                autocomplete="off"
                            >
                            <button type="submit" class="transition-colors focus:ring-4 focus:ring-primary-100">
                                <?php esc_html_e('Tìm kiếm', 'adprint-blog'); ?>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Decorative elements -->
            <div class="absolute bottom-0 left-0 right-0 h-16 bg-gradient-to-t from-white dark:from-gray-900 to-transparent"></div>
        </section>

        <!-- Dashboard Stats -->
        <section class="stats-dashboard mb-16">
            <div class="container">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Total Posts -->
                    <div class="stat-card">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-lg font-semibold opacity-90"><?php esc_html_e('Tổng bài viết', 'adprint-blog'); ?></h3>
                            <svg class="w-8 h-8 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <p class="stat-number text-4xl font-bold" data-count="<?php echo wp_count_posts()->publish; ?>">
                            <?php echo number_format_i18n(wp_count_posts()->publish); ?>
                        </p>
                    </div>

                    <!-- Total Categories -->
                    <div class="stat-card accent">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-lg font-semibold opacity-90"><?php esc_html_e('Danh mục', 'adprint-blog'); ?></h3>
                            <svg class="w-8 h-8 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                        </div>
                        <p class="stat-number text-4xl font-bold" data-count="<?php echo wp_count_terms('category'); ?>">
                            <?php echo number_format_i18n(wp_count_terms('category')); ?>
                        </p>
                    </div>

                    <!-- Total Authors -->
                    <div class="stat-card secondary">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-lg font-semibold opacity-90"><?php esc_html_e('Tác giả', 'adprint-blog'); ?></h3>
                            <svg class="w-8 h-8 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                        <p class="stat-number text-4xl font-bold" data-count="<?php echo count_users()['total_users']; ?>">
                            <?php echo number_format_i18n(count_users()['total_users']); ?>
                        </p>
                    </div>

                    <!-- Total Views -->
                    <div class="stat-card success">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-lg font-semibold opacity-90"><?php esc_html_e('Lượt xem', 'adprint-blog'); ?></h3>
                            <svg class="w-8 h-8 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </div>
                        <p class="stat-number text-4xl font-bold" data-count="<?php
                            global $wpdb;
                            $total_views = $wpdb->get_var("SELECT SUM(meta_value) FROM $wpdb->postmeta WHERE meta_key = 'adprint_post_views_count'");
                            echo number_format_i18n($total_views ? $total_views : 0);
                        ?>">
                            <?php echo number_format_i18n($total_views ? $total_views : 0); ?>
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Latest Posts Section -->
        <section class="latest-posts mb-16">
            <div class="container">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-3xl font-bold font-display text-gray-900 dark:text-gray-100">
                        <?php esc_html_e('Bài Viết Mới Nhất', 'adprint-blog'); ?>
                    </h2>
                    <a href="<?php echo esc_url(get_permalink(get_option('page_for_posts'))); ?>" class="btn-primary">
                        <?php esc_html_e('Xem tất cả', 'adprint-blog'); ?>
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <?php
                    $latest_posts = new WP_Query(array(
                        'posts_per_page' => get_theme_mod('adprint_posts_per_page', 6),
                        'post_status' => 'publish',
                    ));

                    if ($latest_posts->have_posts()) :
                        while ($latest_posts->have_posts()) : $latest_posts->the_post();
                    ?>
                        <article <?php post_class('post-card animate-on-scroll'); ?>>
                            <?php if (has_post_thumbnail()) : ?>
                                <a href="<?php the_permalink(); ?>" class="block">
                                    <?php the_post_thumbnail('adprint-medium', array('loading' => 'lazy')); ?>
                                </a>
                            <?php endif; ?>

                            <div class="post-card-content">
                                <?php adprint_post_categories(); ?>

                                <h3 class="text-xl font-bold mb-3">
                                    <a href="<?php the_permalink(); ?>" class="text-gray-900 dark:text-gray-100 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
                                        <?php the_title(); ?>
                                    </a>
                                </h3>

                                <p class="text-gray-600 dark:text-gray-400 mb-4 line-clamp-3">
                                    <?php echo wp_trim_words(get_the_excerpt(), 20); ?>
                                </p>

                                <div class="flex items-center justify-between">
                                    <?php adprint_post_meta(); ?>
                                </div>

                                <?php
                                $rating = get_post_meta(get_the_ID(), 'adprint_rating', true);
                                if ($rating) :
                                ?>
                                    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                        <?php adprint_star_rating($rating); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php
                        endwhile;
                        wp_reset_postdata();
                    else :
                    ?>
                        <p class="col-span-full text-center text-gray-600 dark:text-gray-400">
                            <?php esc_html_e('Không có bài viết nào.', 'adprint-blog'); ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- FAQ Section với Schema Markup -->
        <section class="faq-section mb-16 bg-gray-50 dark:bg-gray-800 py-16">
            <div class="container">
                <div class="max-w-3xl mx-auto">
                    <h2 class="text-3xl font-bold font-display text-center text-gray-900 dark:text-gray-100 mb-4">
                        <?php esc_html_e('Câu Hỏi Thường Gặp', 'adprint-blog'); ?>
                    </h2>
                    <p class="text-center text-gray-600 dark:text-gray-400 mb-10">
                        <?php esc_html_e('Giải đáp các thắc mắc phổ biến về dịch vụ quảng cáo và in ấn', 'adprint-blog'); ?>
                    </p>

                    <div class="faq-list bg-white dark:bg-gray-900 rounded-xl shadow-lg overflow-hidden">
                        <?php
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
                            array(
                                'question' => 'Phương thức thanh toán nào được chấp nhận?',
                                'answer' => 'Chúng tôi chấp nhận nhiều phương thức thanh toán bao gồm chuyển khoản ngân hàng, tiền mặt, ví điện tử và thanh toán qua thẻ tín dụng.',
                            ),
                        );

                        foreach ($faqs as $index => $faq) :
                        ?>
                            <div class="faq-item">
                                <button
                                    class="faq-question flex items-center justify-between w-full text-left"
                                    aria-expanded="false"
                                    aria-controls="faq-answer-<?php echo $index; ?>"
                                >
                                    <span class="text-gray-900 dark:text-gray-100"><?php echo esc_html($faq['question']); ?></span>
                                    <svg class="w-5 h-5 text-gray-500 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                                <div id="faq-answer-<?php echo $index; ?>" class="faq-answer hidden">
                                    <p><?php echo esc_html($faq['answer']); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </section>

        <!-- Call to Action Section -->
        <section class="cta-section mb-16">
            <div class="container">
                <div class="bg-gradient-to-r from-primary-600 to-accent-600 rounded-2xl p-8 md:p-12 text-center text-white shadow-2xl">
                    <h2 class="text-3xl md:text-4xl font-bold font-display mb-4">
                        <?php esc_html_e('Bắt Đầu Dự Án Của Bạn Ngay Hôm Nay', 'adprint-blog'); ?>
                    </h2>
                    <p class="text-xl mb-8 opacity-90">
                        <?php esc_html_e('Nhận tư vấn miễn phí và báo giá tốt nhất cho dự án in ấn & quảng cáo của bạn', 'adprint-blog'); ?>
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="<?php echo esc_url(home_url('/lien-he')); ?>" class="btn-secondary text-primary-600">
                            <?php esc_html_e('Liên hệ ngay', 'adprint-blog'); ?>
                        </a>
                        <a href="<?php echo esc_url(home_url('/dich-vu')); ?>" class="btn-primary bg-white text-primary-600 hover:bg-gray-100">
                            <?php esc_html_e('Xem dịch vụ', 'adprint-blog'); ?>
                        </a>
                    </div>
                </div>
            </div>
        </section>

    <?php else : ?>

        <!-- Blog Archive -->
        <div class="container py-12">
            <?php if (have_posts()) : ?>
                <header class="page-header mb-10">
                    <?php
                    the_archive_title('<h1 class="page-title text-4xl font-bold font-display text-gray-900 dark:text-gray-100 mb-4">', '</h1>');
                    the_archive_description('<div class="archive-description text-lg text-gray-600 dark:text-gray-400">', '</div>');
                    ?>
                </header>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <?php while (have_posts()) : the_post(); ?>
                        <article <?php post_class('post-card'); ?>>
                            <?php if (has_post_thumbnail()) : ?>
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail('adprint-medium'); ?>
                                </a>
                            <?php endif; ?>

                            <div class="post-card-content">
                                <?php adprint_post_categories(); ?>

                                <h2 class="text-xl font-bold mb-3">
                                    <a href="<?php the_permalink(); ?>" class="text-gray-900 dark:text-gray-100 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
                                        <?php the_title(); ?>
                                    </a>
                                </h2>

                                <p class="text-gray-600 dark:text-gray-400 mb-4">
                                    <?php echo wp_trim_words(get_the_excerpt(), 20); ?>
                                </p>

                                <?php adprint_post_meta(); ?>
                            </div>
                        </article>
                    <?php endwhile; ?>
                </div>

                <!-- Pagination -->
                <div class="pagination mt-12">
                    <?php
                    the_posts_pagination(array(
                        'mid_size' => 2,
                        'prev_text' => __('← Previous', 'adprint-blog'),
                        'next_text' => __('Next →', 'adprint-blog'),
                    ));
                    ?>
                </div>

            <?php else : ?>
                <p class="text-center text-gray-600 dark:text-gray-400">
                    <?php esc_html_e('Không tìm thấy bài viết nào.', 'adprint-blog'); ?>
                </p>
            <?php endif; ?>
        </div>

    <?php endif; ?>

</main>

<?php
get_sidebar();
get_footer();
