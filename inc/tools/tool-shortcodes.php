<?php
/**
 * Tool Shortcodes for Print & Advertising Calculators
 *
 * @package AdPrint_Blog
 * @since 1.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register all tool shortcodes
 */
function adprint_register_tool_shortcodes() {
    add_shortcode('adprint_price_calculator', 'adprint_price_calculator_shortcode');
    add_shortcode('adprint_color_converter', 'adprint_color_converter_shortcode');
    add_shortcode('adprint_roi_calculator', 'adprint_roi_calculator_shortcode');
    add_shortcode('adprint_size_calculator', 'adprint_size_calculator_shortcode');
    add_shortcode('adprint_paper_calculator', 'adprint_paper_calculator_shortcode');
    add_shortcode('adprint_file_checker', 'adprint_file_checker_shortcode');
    add_shortcode('adprint_quote_form', 'adprint_quote_form_shortcode');
}
add_action('init', 'adprint_register_tool_shortcodes');

/**
 * Price Calculator Shortcode
 * Usage: [adprint_price_calculator]
 */
function adprint_price_calculator_shortcode($atts) {
    wp_enqueue_script('adprint-price-calculator');

    ob_start();
    ?>
    <div id="price-calculator" class="adprint-tool bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 md:p-8">
        <h2 class="text-2xl md:text-3xl font-bold mb-6 text-gray-900 dark:text-gray-100">🧮 Tính giá in ấn</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Inputs -->
            <div class="space-y-4">
                <div>
                    <label for="product-type" class="block text-sm font-medium mb-2">Loại sản phẩm</label>
                    <select id="product-type" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 focus:ring-2 focus:ring-primary-500">
                        <option value="flyer">Tờ rơi / Flyer</option>
                        <option value="brochure">Brochure</option>
                        <option value="poster">Poster</option>
                        <option value="namecard">Name Card</option>
                        <option value="banner">Banner / Phông nền</option>
                    </select>
                </div>

                <div>
                    <label for="print-quantity" class="block text-sm font-medium mb-2">Số lượng</label>
                    <input type="number" id="print-quantity" min="1" value="100" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 focus:ring-2 focus:ring-primary-500">
                </div>

                <div>
                    <label for="print-paper" class="block text-sm font-medium mb-2">Loại giấy</label>
                    <select id="print-paper" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 focus:ring-2 focus:ring-primary-500">
                        <option value="standard">Giấy thường (80-100 GSM)</option>
                        <option value="glossy">Giấy bóng (Glossy)</option>
                        <option value="matte">Giấy mờ (Matte)</option>
                        <option value="premium">Giấy cao cấp (Art Paper)</option>
                    </select>
                </div>

                <div>
                    <label for="print-size" class="block text-sm font-medium mb-2">Kích thước</label>
                    <select id="print-size" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 focus:ring-2 focus:ring-primary-500">
                        <option value="a4">A4 (210 × 297mm)</option>
                        <option value="a5">A5 (148 × 210mm)</option>
                        <option value="a6">A6 (105 × 148mm)</option>
                        <option value="a3">A3 (297 × 420mm)</option>
                    </select>
                </div>

                <div>
                    <label for="print-colors" class="block text-sm font-medium mb-2">Màu in</label>
                    <select id="print-colors" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 focus:ring-2 focus:ring-primary-500">
                        <option value="bw">Đen trắng</option>
                        <option value="1color">1 màu</option>
                        <option value="4color">4 màu (CMYK)</option>
                    </select>
                </div>

                <div>
                    <label for="print-finishing" class="block text-sm font-medium mb-2">Gia công</label>
                    <select id="print-finishing" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 focus:ring-2 focus:ring-primary-500">
                        <option value="none">Không</option>
                        <option value="lamination">Cán màng (Lamination)</option>
                        <option value="uv">UV Spot</option>
                        <option value="embossing">Dập nổi (Embossing)</option>
                    </select>
                </div>

                <button id="calculate-price" class="w-full btn-primary">
                    Tính giá
                </button>
            </div>

            <!-- Results -->
            <div class="space-y-4">
                <div class="p-6 bg-gradient-to-br from-primary-500 to-primary-700 text-white rounded-xl">
                    <h3 class="text-lg font-semibold mb-2">Tổng chi phí dự kiến</h3>
                    <p id="total-price" class="text-4xl font-bold">0đ</p>
                    <p class="text-sm mt-2 opacity-90">Chưa bao gồm VAT</p>
                </div>

                <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <h4 class="font-semibold mb-2">Chi tiết</h4>
                    <div id="price-details" class="space-y-2 text-sm"></div>
                </div>

                <div id="price-recommendations" class="space-y-2"></div>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Color Converter Shortcode
 * Usage: [adprint_color_converter]
 */
function adprint_color_converter_shortcode($atts) {
    wp_enqueue_script('adprint-color-converter');

    ob_start();
    ?>
    <div id="color-converter" class="adprint-tool bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 md:p-8">
        <h2 class="text-2xl md:text-3xl font-bold mb-6 text-gray-900 dark:text-gray-100">🎨 Chuyển đổi màu sắc</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Input -->
            <div class="space-y-6">
                <!-- HEX -->
                <div>
                    <label for="hex-input" class="block text-sm font-medium mb-2">HEX</label>
                    <div class="flex gap-2">
                        <input type="text" id="hex-input" placeholder="#FF5733" class="flex-1 px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 focus:ring-2 focus:ring-primary-500">
                        <button id="convert-from-hex" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg">
                            Chuyển đổi
                        </button>
                    </div>
                </div>

                <!-- RGB -->
                <div>
                    <label class="block text-sm font-medium mb-2">RGB</label>
                    <div class="grid grid-cols-3 gap-2">
                        <input type="number" id="rgb-r" placeholder="R" min="0" max="255" class="px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 focus:ring-2 focus:ring-primary-500">
                        <input type="number" id="rgb-g" placeholder="G" min="0" max="255" class="px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 focus:ring-2 focus:ring-primary-500">
                        <input type="number" id="rgb-b" placeholder="B" min="0" max="255" class="px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 focus:ring-2 focus:ring-primary-500">
                    </div>
                    <button id="convert-from-rgb" class="mt-2 w-full px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg">
                        Chuyển đổi
                    </button>
                </div>

                <!-- CMYK -->
                <div>
                    <label class="block text-sm font-medium mb-2">CMYK (%)</label>
                    <div class="grid grid-cols-4 gap-2">
                        <input type="number" id="cmyk-c" placeholder="C" min="0" max="100" class="px-2 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 focus:ring-2 focus:ring-primary-500 text-sm">
                        <input type="number" id="cmyk-m" placeholder="M" min="0" max="100" class="px-2 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 focus:ring-2 focus:ring-primary-500 text-sm">
                        <input type="number" id="cmyk-y" placeholder="Y" min="0" max="100" class="px-2 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 focus:ring-2 focus:ring-primary-500 text-sm">
                        <input type="number" id="cmyk-k" placeholder="K" min="0" max="100" class="px-2 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 focus:ring-2 focus:ring-primary-500 text-sm">
                    </div>
                    <button id="convert-from-cmyk" class="mt-2 w-full px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg">
                        Chuyển đổi
                    </button>
                </div>
            </div>

            <!-- Output -->
            <div class="space-y-4">
                <div id="color-preview" class="h-48 rounded-xl border-4 border-gray-200 dark:border-gray-600" style="background-color: #ffffff;"></div>

                <div id="color-results" class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <h4 class="font-semibold mb-3">Kết quả chuyển đổi</h4>
                    <div class="space-y-2 text-sm"></div>
                </div>

                <div id="color-info" class="p-4 bg-blue-50 dark:bg-blue-900 rounded-lg text-sm">
                    <p class="text-blue-800 dark:text-blue-200">
                        💡 <strong>Lưu ý:</strong> Chuyển đổi CMYK ↔ RGB không hoàn toàn chính xác do khác biệt về không gian màu. Luôn sử dụng CMYK cho in ấn offset.
                    </p>
                </div>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * ROI Calculator Shortcode
 * Usage: [adprint_roi_calculator]
 */
function adprint_roi_calculator_shortcode($atts) {
    wp_enqueue_script('adprint-roi-calculator');

    ob_start();
    ?>
    <div id="roi-calculator" class="adprint-tool bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 md:p-8">
        <h2 class="text-2xl md:text-3xl font-bold mb-6 text-gray-900 dark:text-gray-100">📊 Tính ROI quảng cáo</h2>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Inputs -->
            <div class="space-y-4">
                <h3 class="font-semibold text-lg">Thông tin chiến dịch</h3>

                <div>
                    <label for="total-investment" class="block text-sm font-medium mb-2">Tổng chi phí đầu tư (VNĐ)</label>
                    <input type="number" id="total-investment" min="0" step="100000" placeholder="10000000" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 focus:ring-2 focus:ring-primary-500">
                </div>

                <div>
                    <label for="total-revenue" class="block text-sm font-medium mb-2">Tổng doanh thu (VNĐ)</label>
                    <input type="number" id="total-revenue" min="0" step="100000" placeholder="15000000" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 focus:ring-2 focus:ring-primary-500">
                </div>

                <div>
                    <label for="campaign-duration" class="block text-sm font-medium mb-2">Thời gian chiến dịch (ngày)</label>
                    <input type="number" id="campaign-duration" min="1" value="30" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 focus:ring-2 focus:ring-primary-500">
                </div>

                <h4 class="font-semibold mt-6">Chỉ số hiệu suất</h4>

                <div>
                    <label for="impressions" class="block text-sm font-medium mb-2">Lượt hiển thị (Impressions)</label>
                    <input type="number" id="impressions" min="0" placeholder="100000" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 focus:ring-2 focus:ring-primary-500">
                </div>

                <div>
                    <label for="clicks" class="block text-sm font-medium mb-2">Lượt click (Clicks)</label>
                    <input type="number" id="clicks" min="0" placeholder="2000" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 focus:ring-2 focus:ring-primary-500">
                </div>

                <div>
                    <label for="conversions" class="block text-sm font-medium mb-2">Chuyển đổi (Conversions)</label>
                    <input type="number" id="conversions" min="0" placeholder="50" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 focus:ring-2 focus:ring-primary-500">
                </div>

                <button id="calculate-roi" class="w-full btn-primary mt-4">
                    Tính toán ROI
                </button>
            </div>

            <!-- Results -->
            <div class="space-y-4">
                <div class="p-6 bg-gradient-to-br from-green-500 to-green-700 text-white rounded-xl">
                    <h3 class="text-lg font-semibold mb-2">ROI (Return on Investment)</h3>
                    <p id="roi-value" class="text-5xl font-bold">0%</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="p-4 bg-blue-50 dark:bg-blue-900 rounded-lg">
                        <h4 class="text-sm text-gray-600 dark:text-gray-300">Lợi nhuận</h4>
                        <p id="profit-value" class="text-2xl font-bold">0đ</p>
                    </div>

                    <div class="p-4 bg-purple-50 dark:bg-purple-900 rounded-lg">
                        <h4 class="text-sm text-gray-600 dark:text-gray-300">CPM</h4>
                        <p id="cpm-value" class="text-2xl font-bold">0đ</p>
                    </div>

                    <div class="p-4 bg-yellow-50 dark:bg-yellow-900 rounded-lg">
                        <h4 class="text-sm text-gray-600 dark:text-gray-300">CPC</h4>
                        <p id="cpc-value" class="text-2xl font-bold">0đ</p>
                    </div>

                    <div class="p-4 bg-red-50 dark:bg-red-900 rounded-lg">
                        <h4 class="text-sm text-gray-600 dark:text-gray-300">CPA</h4>
                        <p id="cpa-value" class="text-2xl font-bold">0đ</p>
                    </div>

                    <div class="p-4 bg-green-50 dark:bg-green-900 rounded-lg">
                        <h4 class="text-sm text-gray-600 dark:text-gray-300">Conversion Rate</h4>
                        <p id="conversion-rate" class="text-2xl font-bold">0%</p>
                    </div>

                    <div class="p-4 bg-indigo-50 dark:bg-indigo-900 rounded-lg">
                        <h4 class="text-sm text-gray-600 dark:text-gray-300">CTR</h4>
                        <p id="ctr-value" class="text-2xl font-bold">0%</p>
                    </div>
                </div>

                <div id="recommendations"></div>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Size Calculator Shortcode
 * Usage: [adprint_size_calculator]
 */
function adprint_size_calculator_shortcode($atts) {
    wp_enqueue_script('adprint-size-calculator');

    ob_start();
    // Shortened for brevity - similar structure to above
    echo '<div id="size-calculator" class="adprint-tool">Size Calculator HTML...</div>';
    return ob_get_clean();
}

/**
 * Paper Calculator Shortcode
 * Usage: [adprint_paper_calculator]
 */
function adprint_paper_calculator_shortcode($atts) {
    wp_enqueue_script('adprint-paper-calculator');

    ob_start();
    echo '<div id="paper-calculator" class="adprint-tool">Paper Calculator HTML...</div>';
    return ob_get_clean();
}

/**
 * File Checker Shortcode
 * Usage: [adprint_file_checker]
 */
function adprint_file_checker_shortcode($atts) {
    wp_enqueue_script('adprint-file-checker');

    ob_start();
    echo '<div id="file-checker" class="adprint-tool">File Checker HTML...</div>';
    return ob_get_clean();
}

/**
 * Quote Form Shortcode
 * Usage: [adprint_quote_form]
 */
function adprint_quote_form_shortcode($atts) {
    wp_enqueue_script('adprint-quote-form');

    ob_start();
    echo '<div id="quote-form" class="adprint-tool">Quote Form HTML...</div>';
    return ob_get_clean();
}
