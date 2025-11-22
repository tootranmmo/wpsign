<?php
/**
 * Template Name: Tools Demo Page
 * Template Post Type: page
 *
 * @package AdPrint_Blog
 * @since 1.0.0
 */

get_header();
?>

<main id="main" class="site-main py-12" role="main">
    <div class="container mx-auto px-4">

        <!-- Page Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl md:text-5xl font-bold mb-4 text-gray-900 dark:text-gray-100">
                Công cụ chuyên nghiệp cho In ấn & Quảng cáo
            </h1>
            <p class="text-xl text-gray-600 dark:text-gray-400 max-w-3xl mx-auto">
                Bộ công cụ toàn diện giúp bạn tính toán, kiểm tra và tối ưu dự án in ấn & quảng cáo của mình
            </p>
        </div>

        <!-- Tools Navigation -->
        <div class="mb-12">
            <div class="flex flex-wrap gap-3 justify-center">
                <a href="#price-calc" class="px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-semibold transition-colors">
                    💰 Tính giá in
                </a>
                <a href="#color-conv" class="px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-semibold transition-colors">
                    🎨 Chuyển đổi màu
                </a>
                <a href="#roi-calc" class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold transition-colors">
                    📊 Tính ROI
                </a>
                <a href="#size-calc" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition-colors">
                    📐 Tính kích thước
                </a>
                <a href="#paper-calc" class="px-6 py-3 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg font-semibold transition-colors">
                    📄 Tính giấy
                </a>
                <a href="#file-check" class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold transition-colors">
                    ✓ Kiểm tra file
                </a>
                <a href="#quote-form" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold transition-colors">
                    📝 Yêu cầu báo giá
                </a>
            </div>
        </div>

        <!-- Tool Sections -->
        <div class="space-y-16">

            <!-- Price Calculator -->
            <section id="price-calc" class="scroll-mt-24">
                <div class="mb-6">
                    <h2 class="text-3xl font-bold mb-2 text-gray-900 dark:text-gray-100">💰 Công cụ tính giá in ấn</h2>
                    <p class="text-gray-600 dark:text-gray-400">Tính toán chi phí in ấn cho các sản phẩm: tờ rơi, brochure, poster, name card, banner</p>
                </div>
                <?php echo do_shortcode('[adprint_price_calculator]'); ?>
            </section>

            <hr class="border-gray-300 dark:border-gray-600">

            <!-- Color Converter -->
            <section id="color-conv" class="scroll-mt-24">
                <div class="mb-6">
                    <h2 class="text-3xl font-bold mb-2 text-gray-900 dark:text-gray-100">🎨 Công cụ chuyển đổi màu sắc</h2>
                    <p class="text-gray-600 dark:text-gray-400">Chuyển đổi màu giữa RGB, CMYK và HEX - Thiết yếu cho thiết kế in ấn</p>
                </div>
                <?php echo do_shortcode('[adprint_color_converter]'); ?>
            </section>

            <hr class="border-gray-300 dark:border-gray-600">

            <!-- ROI Calculator -->
            <section id="roi-calc" class="scroll-mt-24">
                <div class="mb-6">
                    <h2 class="text-3xl font-bold mb-2 text-gray-900 dark:text-gray-100">📊 Công cụ tính ROI quảng cáo</h2>
                    <p class="text-gray-600 dark:text-gray-400">Đo lường hiệu quả chiến dịch quảng cáo với các chỉ số: ROI, CPM, CPC, CPA, CTR</p>
                </div>
                <?php echo do_shortcode('[adprint_roi_calculator]'); ?>
            </section>

            <hr class="border-gray-300 dark:border-gray-600">

            <!-- Size Calculator -->
            <section id="size-calc" class="scroll-mt-24">
                <div class="mb-6">
                    <h2 class="text-3xl font-bold mb-2 text-gray-900 dark:text-gray-100">📐 Công cụ tính kích thước & Bleed</h2>
                    <p class="text-gray-600 dark:text-gray-400">Tính toán kích thước in với vùng bleed, safe area và độ phân giải đề xuất</p>
                </div>
                <?php echo do_shortcode('[adprint_size_calculator]'); ?>
            </section>

            <hr class="border-gray-300 dark:border-gray-600">

            <!-- Paper Calculator -->
            <section id="paper-calc" class="scroll-mt-24">
                <div class="mb-6">
                    <h2 class="text-3xl font-bold mb-2 text-gray-900 dark:text-gray-100">📄 Công cụ tính trọng lượng giấy</h2>
                    <p class="text-gray-600 dark:text-gray-400">Tính trọng lượng, độ dày và chi phí giấy dựa trên GSM và số lượng</p>
                </div>
                <?php echo do_shortcode('[adprint_paper_calculator]'); ?>
            </section>

            <hr class="border-gray-300 dark:border-gray-600">

            <!-- File Checker -->
            <section id="file-check" class="scroll-mt-24">
                <div class="mb-6">
                    <h2 class="text-3xl font-bold mb-2 text-gray-900 dark:text-gray-100">✓ Công cụ kiểm tra file in</h2>
                    <p class="text-gray-600 dark:text-gray-400">Kiểm tra file thiết kế có đạt chuẩn in ấn chưa: định dạng, độ phân giải, bleed</p>
                </div>
                <?php echo do_shortcode('[adprint_file_checker]'); ?>
            </section>

            <hr class="border-gray-300 dark:border-gray-600">

            <!-- Quote Form -->
            <section id="quote-form" class="scroll-mt-24">
                <div class="mb-6">
                    <h2 class="text-3xl font-bold mb-2 text-gray-900 dark:text-gray-100">📝 Form yêu cầu báo giá</h2>
                    <p class="text-gray-600 dark:text-gray-400">Gửi yêu cầu báo giá với file thiết kế - Nhận phản hồi trong 24h</p>
                </div>
                <?php echo do_shortcode('[adprint_quote_form]'); ?>
            </section>

        </div>

        <!-- Back to Top Button -->
        <div class="mt-12 text-center">
            <a href="#main" class="inline-block px-8 py-4 bg-gray-800 dark:bg-gray-700 hover:bg-gray-900 dark:hover:bg-gray-600 text-white rounded-lg font-semibold transition-colors">
                ↑ Về đầu trang
            </a>
        </div>

        <!-- Help Section -->
        <div class="mt-16 p-8 bg-blue-50 dark:bg-blue-900 rounded-xl">
            <h3 class="text-2xl font-bold mb-4 text-blue-900 dark:text-blue-100">💡 Cần hỗ trợ?</h3>
            <p class="text-blue-800 dark:text-blue-200 mb-4">
                Các công cụ này được thiết kế để hỗ trợ bạn tính toán nhanh chóng. Tuy nhiên, đây chỉ là giá ước tính.
                Để nhận báo giá chính xác, vui lòng sử dụng form yêu cầu báo giá bên trên.
            </p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
                <div class="p-4 bg-white dark:bg-blue-800 rounded-lg">
                    <h4 class="font-bold mb-2">📞 Hotline</h4>
                    <p class="text-sm">0123-456-789</p>
                </div>
                <div class="p-4 bg-white dark:bg-blue-800 rounded-lg">
                    <h4 class="font-bold mb-2">📧 Email</h4>
                    <p class="text-sm"><?php echo get_option('admin_email'); ?></p>
                </div>
                <div class="p-4 bg-white dark:bg-blue-800 rounded-lg">
                    <h4 class="font-bold mb-2">⏰ Giờ làm việc</h4>
                    <p class="text-sm">T2-T6: 8:00 - 17:30</p>
                </div>
            </div>
        </div>

    </div>
</main>

<?php
get_footer();
?>
