/**
 * Size & Bleed Calculator for Print Projects
 *
 * @package AdPrint_Blog
 * @since 1.0.0
 */

(function() {
    'use strict';

    document.addEventListener('DOMContentLoaded', function() {
        const calculator = document.getElementById('size-calculator');

        if (!calculator) return;

        // Input elements
        const printType = document.getElementById('print-type');
        const customWidth = document.getElementById('custom-width');
        const customHeight = document.getElementById('custom-height');
        const unit = document.getElementById('unit');
        const bleedAmount = document.getElementById('bleed-amount');
        const customSizeInputs = document.getElementById('custom-size-inputs');

        // Output elements
        const finalWidth = document.getElementById('final-width');
        const finalHeight = document.getElementById('final-height');
        const trimSize = document.getElementById('trim-size');
        const bleedSize = document.getElementById('bleed-size');
        const safeArea = document.getElementById('safe-area');
        const totalArea = document.getElementById('total-area');
        const visualPreview = document.getElementById('visual-preview');

        const calculateBtn = document.getElementById('calculate-size');

        // Standard print sizes in mm
        const standardSizes = {
            'a4': { width: 210, height: 297, name: 'A4' },
            'a5': { width: 148, height: 210, name: 'A5' },
            'a6': { width: 105, height: 148, name: 'A6' },
            'a3': { width: 297, height: 420, name: 'A3' },
            'business-card': { width: 90, height: 54, name: 'Business Card (90x54mm)' },
            'business-card-us': { width: 88.9, height: 50.8, name: 'Business Card US (3.5x2in)' },
            'flyer': { width: 210, height: 297, name: 'Flyer (A4)' },
            'postcard': { width: 148, height: 105, name: 'Postcard (A6)' },
            'poster-a2': { width: 420, height: 594, name: 'Poster A2' },
            'poster-a1': { width: 594, height: 841, name: 'Poster A1' },
            'poster-a0': { width: 841, height: 1189, name: 'Poster A0' },
            'banner-2x1': { width: 2000, height: 1000, name: 'Banner 2x1m' },
            'banner-3x1': { width: 3000, height: 1000, name: 'Banner 3x1m' },
            'banner-5x2': { width: 5000, height: 2000, name: 'Banner 5x2m' },
            'custom': { width: 0, height: 0, name: 'Custom Size' }
        };

        // Unit conversions to mm
        const unitConversions = {
            'mm': 1,
            'cm': 10,
            'inch': 25.4,
            'px-72dpi': 25.4 / 72,
            'px-300dpi': 25.4 / 300
        };

        /**
         * Calculate sizes with bleed
         */
        function calculateSizes() {
            // Get base size
            let baseWidth, baseHeight;
            const selectedType = printType.value;

            if (selectedType === 'custom') {
                const width = parseFloat(customWidth.value) || 0;
                const height = parseFloat(customHeight.value) || 0;
                const selectedUnit = unit.value;

                if (width === 0 || height === 0) {
                    showError('Vui lòng nhập kích thước hợp lệ!');
                    return;
                }

                // Convert to mm
                baseWidth = width * unitConversions[selectedUnit];
                baseHeight = height * unitConversions[selectedUnit];
            } else {
                baseWidth = standardSizes[selectedType].width;
                baseHeight = standardSizes[selectedType].height;
            }

            // Get bleed in mm (default 3mm for print)
            const bleed = parseFloat(bleedAmount.value) || 3;

            // Calculate dimensions
            const calculations = {
                trimWidth: baseWidth,
                trimHeight: baseHeight,
                bleedWidth: baseWidth + (bleed * 2),
                bleedHeight: baseHeight + (bleed * 2),
                safeWidth: baseWidth - (bleed * 2),
                safeHeight: baseHeight - (bleed * 2),
                bleedAmount: bleed
            };

            // Display results
            displayResults(calculations);

            // Show visual preview
            showVisualPreview(calculations);

            // Show recommendations
            showRecommendations(calculations);
        }

        /**
         * Display calculation results
         */
        function displayResults(calc) {
            // Trim size (actual size)
            trimSize.textContent = `${calc.trimWidth.toFixed(1)}mm × ${calc.trimHeight.toFixed(1)}mm`;

            // Final size with bleed
            finalWidth.textContent = calc.bleedWidth.toFixed(1) + 'mm';
            finalHeight.textContent = calc.bleedHeight.toFixed(1) + 'mm';
            bleedSize.textContent = `${calc.bleedWidth.toFixed(1)}mm × ${calc.bleedHeight.toFixed(1)}mm`;

            // Safe area (content area)
            safeArea.textContent = `${calc.safeWidth.toFixed(1)}mm × ${calc.safeHeight.toFixed(1)}mm`;

            // Total area in square meters
            const areaM2 = (calc.bleedWidth * calc.bleedHeight) / 1000000;
            totalArea.textContent = areaM2.toFixed(4) + ' m²';

            // Show additional info
            showAdditionalInfo(calc);
        }

        /**
         * Show additional information
         */
        function showAdditionalInfo(calc) {
            const infoContainer = document.getElementById('additional-info');
            if (!infoContainer) return;

            // Calculate various useful metrics
            const trimWidthInch = (calc.trimWidth / 25.4).toFixed(2);
            const trimHeightInch = (calc.trimHeight / 25.4).toFixed(2);
            const bleedWidthInch = (calc.bleedWidth / 25.4).toFixed(2);
            const bleedHeightInch = (calc.bleedHeight / 25.4).toFixed(2);

            // Recommended resolution (300 DPI)
            const widthPx = Math.ceil((calc.bleedWidth / 25.4) * 300);
            const heightPx = Math.ceil((calc.bleedHeight / 25.4) * 300);

            infoContainer.innerHTML = `
                <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900 rounded-lg">
                    <h4 class="font-bold mb-3 text-blue-900 dark:text-blue-100">📐 Thông tin bổ sung:</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-700 dark:text-gray-300">Kích thước Trim (inch):</span>
                            <span class="font-semibold">${trimWidthInch}" × ${trimHeightInch}"</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-700 dark:text-gray-300">Kích thước Bleed (inch):</span>
                            <span class="font-semibold">${bleedWidthInch}" × ${bleedHeightInch}"</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-700 dark:text-gray-300">Độ phân giải đề xuất (300 DPI):</span>
                            <span class="font-semibold">${widthPx} × ${heightPx} px</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-700 dark:text-gray-300">Bleed margin:</span>
                            <span class="font-semibold">${calc.bleedAmount}mm mỗi cạnh</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-700 dark:text-gray-300">Safe area margin:</span>
                            <span class="font-semibold">${calc.bleedAmount}mm từ mép trim</span>
                        </div>
                    </div>
                </div>
            `;
        }

        /**
         * Show visual preview
         */
        function showVisualPreview(calc) {
            if (!visualPreview) return;

            // Calculate scale to fit in preview box (max 400px)
            const maxSize = 400;
            const scale = Math.min(maxSize / calc.bleedWidth, maxSize / calc.bleedHeight);

            const previewBleedWidth = calc.bleedWidth * scale;
            const previewBleedHeight = calc.bleedHeight * scale;
            const previewTrimWidth = calc.trimWidth * scale;
            const previewTrimHeight = calc.trimHeight * scale;
            const previewSafeWidth = calc.safeWidth * scale;
            const previewSafeHeight = calc.safeHeight * scale;
            const previewBleed = calc.bleedAmount * scale;

            visualPreview.innerHTML = `
                <div class="relative mx-auto" style="width: ${previewBleedWidth}px; height: ${previewBleedHeight}px;">
                    <!-- Bleed area -->
                    <div class="absolute inset-0 bg-red-100 dark:bg-red-900 border-2 border-red-400 dark:border-red-600">
                        <span class="text-xs text-red-600 dark:text-red-300 absolute top-1 left-1">Bleed Area</span>
                    </div>

                    <!-- Trim area -->
                    <div class="absolute bg-blue-100 dark:bg-blue-900 border-2 border-blue-500 dark:border-blue-400"
                         style="top: ${previewBleed}px; left: ${previewBleed}px; width: ${previewTrimWidth}px; height: ${previewTrimHeight}px;">
                        <span class="text-xs text-blue-600 dark:text-blue-300 absolute top-1 left-1">Trim Area</span>
                    </div>

                    <!-- Safe area -->
                    <div class="absolute bg-green-100 dark:bg-green-900 border-2 border-green-500 dark:border-green-400"
                         style="top: ${previewBleed * 2}px; left: ${previewBleed * 2}px; width: ${previewSafeWidth}px; height: ${previewSafeHeight}px;">
                        <span class="text-xs text-green-600 dark:text-green-300 absolute top-1 left-1">Safe Area</span>
                    </div>
                </div>

                <div class="mt-4 space-y-2 text-xs">
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 bg-red-100 dark:bg-red-900 border border-red-400"></div>
                        <span>Bleed Area - Vùng sẽ bị cắt</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 bg-blue-100 dark:bg-blue-900 border border-blue-500"></div>
                        <span>Trim Area - Kích thước thực tế sau khi cắt</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 bg-green-100 dark:bg-green-900 border border-green-500"></div>
                        <span>Safe Area - Vùng an toàn cho nội dung quan trọng</span>
                    </div>
                </div>
            `;
        }

        /**
         * Show recommendations
         */
        function showRecommendations(calc) {
            const recommendations = [];

            // Bleed recommendations
            if (calc.bleedAmount < 3) {
                recommendations.push({
                    type: 'warning',
                    message: `Bleed ${calc.bleedAmount}mm có thể chưa đủ. Khuyến nghị tối thiểu 3mm để đảm bảo không bị viền trắng khi cắt.`
                });
            } else if (calc.bleedAmount >= 3 && calc.bleedAmount <= 5) {
                recommendations.push({
                    type: 'success',
                    message: `Bleed ${calc.bleedAmount}mm là phù hợp cho hầu hết các dự án in ấn.`
                });
            } else if (calc.bleedAmount > 5) {
                recommendations.push({
                    type: 'info',
                    message: `Bleed ${calc.bleedAmount}mm khá rộng, phù hợp cho in offset hoặc dự án yêu cầu cao.`
                });
            }

            // Safe area recommendations
            if (calc.safeWidth <= 0 || calc.safeHeight <= 0) {
                recommendations.push({
                    type: 'error',
                    message: 'Safe area âm! Bleed quá lớn so với kích thước in. Giảm bleed hoặc tăng kích thước.'
                });
            } else if (calc.safeWidth < 50 || calc.safeHeight < 50) {
                recommendations.push({
                    type: 'warning',
                    message: 'Safe area nhỏ. Hãy đặt text và logo quan trọng gần trung tâm để tránh bị cắt.'
                });
            }

            // Size recommendations
            if (calc.trimWidth >= 1000 || calc.trimHeight >= 1000) {
                recommendations.push({
                    type: 'info',
                    message: 'Dự án in khổ lớn (banner/poster). Hãy kiểm tra độ phân giải tối thiểu 150 DPI.'
                });
            }

            // Resolution warning for large prints
            const widthPx = Math.ceil((calc.bleedWidth / 25.4) * 300);
            const heightPx = Math.ceil((calc.bleedHeight / 25.4) * 300);
            const megapixels = (widthPx * heightPx) / 1000000;

            if (megapixels > 100) {
                recommendations.push({
                    type: 'warning',
                    message: `File sẽ rất lớn (~${megapixels.toFixed(0)}MP). Với banner khổ lớn, 150 DPI có thể đủ thay vì 300 DPI.`
                });
            }

            displayRecommendations(recommendations);
        }

        /**
         * Display recommendations
         */
        function displayRecommendations(recommendations) {
            const container = document.getElementById('size-recommendations');

            if (!container || recommendations.length === 0) return;

            container.innerHTML = '<h4 class="font-bold mb-3">💡 Gợi ý thiết kế:</h4>';

            const list = document.createElement('ul');
            list.className = 'space-y-2';

            recommendations.forEach(rec => {
                const li = document.createElement('li');
                li.className = `p-3 rounded-lg ${getRecommendationClass(rec.type)}`;
                li.innerHTML = `<strong>${getRecommendationIcon(rec.type)}</strong> ${rec.message}`;
                list.appendChild(li);
            });

            container.appendChild(list);
        }

        /**
         * Get recommendation class
         */
        function getRecommendationClass(type) {
            switch (type) {
                case 'success': return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
                case 'warning': return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200';
                case 'error': return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200';
                default: return 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200';
            }
        }

        /**
         * Get recommendation icon
         */
        function getRecommendationIcon(type) {
            switch (type) {
                case 'success': return '✅';
                case 'warning': return '⚠️';
                case 'error': return '❌';
                default: return 'ℹ️';
            }
        }

        /**
         * Show error
         */
        function showError(message) {
            const notification = document.createElement('div');
            notification.className = 'fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg bg-red-500 text-white';
            notification.textContent = message;

            document.body.appendChild(notification);

            setTimeout(() => notification.remove(), 3000);
        }

        /**
         * Toggle custom size inputs
         */
        function toggleCustomSizeInputs() {
            if (printType.value === 'custom') {
                customSizeInputs.classList.remove('hidden');
            } else {
                customSizeInputs.classList.add('hidden');
            }
        }

        // Event listeners
        if (calculateBtn) {
            calculateBtn.addEventListener('click', calculateSizes);
        }

        if (printType) {
            printType.addEventListener('change', function() {
                toggleCustomSizeInputs();
                calculateSizes();
            });
        }

        // Auto-calculate on input (debounced)
        let calculateTimeout;
        [customWidth, customHeight, unit, bleedAmount].forEach(input => {
            if (input) {
                input.addEventListener('input', function() {
                    clearTimeout(calculateTimeout);
                    calculateTimeout = setTimeout(calculateSizes, 500);
                });
            }
        });

        // Initialize
        toggleCustomSizeInputs();
    });

})();
