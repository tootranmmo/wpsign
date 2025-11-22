/**
 * Paper Weight Calculator for Print Projects
 *
 * @package AdPrint_Blog
 * @since 1.0.0
 */

(function() {
    'use strict';

    document.addEventListener('DOMContentLoaded', function() {
        const calculator = document.getElementById('paper-calculator');

        if (!calculator) return;

        // Input elements
        const paperType = document.getElementById('paper-type');
        const paperGsm = document.getElementById('paper-gsm');
        const customGsm = document.getElementById('custom-gsm');
        const paperWidth = document.getElementById('paper-width');
        const paperHeight = document.getElementById('paper-height');
        const paperUnit = document.getElementById('paper-unit');
        const quantity = document.getElementById('paper-quantity');
        const sheets = document.getElementById('sheets-per-item');

        // Output elements
        const thickness = document.getElementById('paper-thickness');
        const weightPerSheet = document.getElementById('weight-per-sheet');
        const totalWeight = document.getElementById('total-weight');
        const stackHeight = document.getElementById('stack-height');
        const shippingWeight = document.getElementById('shipping-weight');
        const paperCost = document.getElementById('paper-cost');

        const calculateBtn = document.getElementById('calculate-paper');

        // Paper types with typical GSM values
        const paperTypes = {
            'newsprint': { gsm: [45, 48, 52], thickness: 0.06, name: 'Newsprint' },
            'office': { gsm: [70, 80], thickness: 0.10, name: 'Office Paper' },
            'bond': { gsm: [90, 100], thickness: 0.12, name: 'Bond Paper' },
            'coated': { gsm: [115, 128, 157, 170], thickness: 0.11, name: 'Coated Paper' },
            'art': { gsm: [128, 157, 210, 250], thickness: 0.14, name: 'Art Paper' },
            'cardstock': { gsm: [200, 250, 300, 350], thickness: 0.30, name: 'Cardstock' },
            'duplex': { gsm: [250, 300, 350, 400], thickness: 0.40, name: 'Duplex Board' },
            'custom': { gsm: [], thickness: 0.12, name: 'Custom' }
        };

        // Unit conversions to mm
        const unitConversions = {
            'mm': 1,
            'cm': 10,
            'inch': 25.4,
            'm': 1000
        };

        // Paper cost per kg (approximate, can be customized)
        const paperCostPerKg = {
            'newsprint': 15000,
            'office': 20000,
            'bond': 25000,
            'coated': 35000,
            'art': 45000,
            'cardstock': 50000,
            'duplex': 55000,
            'custom': 30000
        };

        /**
         * Calculate paper weight and related metrics
         */
        function calculatePaper() {
            // Get GSM
            let gsm;
            if (paperType.value === 'custom' || paperGsm.value === 'custom') {
                gsm = parseFloat(customGsm.value) || 0;
                if (gsm === 0) {
                    showError('Vui lòng nhập giá trị GSM hợp lệ!');
                    return;
                }
            } else {
                gsm = parseInt(paperGsm.value) || 80;
            }

            // Get dimensions in mm
            const width = parseFloat(paperWidth.value) || 0;
            const height = parseFloat(paperHeight.value) || 0;
            const selectedUnit = paperUnit.value;

            if (width === 0 || height === 0) {
                showError('Vui lòng nhập kích thước hợp lệ!');
                return;
            }

            const widthMm = width * unitConversions[selectedUnit];
            const heightMm = height * unitConversions[selectedUnit];

            // Get quantity and sheets
            const qty = parseInt(quantity.value) || 1;
            const sheetsPerItem = parseInt(sheets.value) || 1;
            const totalSheets = qty * sheetsPerItem;

            // Calculate weight per sheet (GSM = grams per square meter)
            const areaSqM = (widthMm * heightMm) / 1000000;
            const weightPerSheetG = gsm * areaSqM;
            const weightPerSheetKg = weightPerSheetG / 1000;

            // Calculate total weight
            const totalWeightKg = weightPerSheetKg * totalSheets;

            // Calculate thickness
            // Approximate formula: thickness (mm) = GSM / density
            // Average paper density ≈ 700-800 kg/m³, we use 750
            const densityKgPerM3 = 750;
            const thicknessMm = (gsm / densityKgPerM3);

            // Calculate stack height
            const stackHeightMm = thicknessMm * totalSheets;
            const stackHeightCm = stackHeightMm / 10;

            // Calculate shipping weight (add 5% for packaging)
            const shippingWeightKg = totalWeightKg * 1.05;

            // Calculate paper cost
            const selectedPaperType = paperType.value;
            const costPerKg = paperCostPerKg[selectedPaperType] || 30000;
            const totalCost = totalWeightKg * costPerKg;

            // Display results
            displayResults({
                gsm,
                thicknessMm,
                weightPerSheetG,
                weightPerSheetKg,
                totalWeightKg,
                stackHeightMm,
                stackHeightCm,
                shippingWeightKg,
                totalCost,
                totalSheets,
                areaSqM
            });

            // Show recommendations
            showRecommendations({
                gsm,
                totalWeightKg,
                stackHeightCm,
                paperType: selectedPaperType
            });
        }

        /**
         * Display calculation results
         */
        function displayResults(calc) {
            // Thickness
            thickness.textContent = calc.thicknessMm.toFixed(3) + ' mm';

            // Weight per sheet
            if (calc.weightPerSheetG < 1) {
                weightPerSheet.textContent = (calc.weightPerSheetG * 1000).toFixed(2) + ' mg';
            } else if (calc.weightPerSheetG < 1000) {
                weightPerSheet.textContent = calc.weightPerSheetG.toFixed(2) + ' g';
            } else {
                weightPerSheet.textContent = calc.weightPerSheetKg.toFixed(3) + ' kg';
            }

            // Total weight
            if (calc.totalWeightKg < 1) {
                totalWeight.textContent = (calc.totalWeightKg * 1000).toFixed(0) + ' g';
            } else {
                totalWeight.textContent = calc.totalWeightKg.toFixed(2) + ' kg';
            }

            // Stack height
            if (calc.stackHeightCm < 1) {
                stackHeight.textContent = calc.stackHeightMm.toFixed(1) + ' mm';
            } else if (calc.stackHeightCm < 100) {
                stackHeight.textContent = calc.stackHeightCm.toFixed(1) + ' cm';
            } else {
                stackHeight.textContent = (calc.stackHeightCm / 100).toFixed(2) + ' m';
            }

            // Shipping weight
            shippingWeight.textContent = calc.shippingWeightKg.toFixed(2) + ' kg';

            // Paper cost
            paperCost.textContent = formatCurrency(calc.totalCost);

            // Show additional details
            showAdditionalDetails(calc);
        }

        /**
         * Show additional details
         */
        function showAdditionalDetails(calc) {
            const detailsContainer = document.getElementById('paper-details');
            if (!detailsContainer) return;

            // Calculate cost breakdown
            const costPerSheet = calc.totalCost / calc.totalSheets;
            const costPer100 = costPerSheet * 100;
            const costPer1000 = costPerSheet * 1000;

            detailsContainer.innerHTML = `
                <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <h4 class="font-bold mb-3">📋 Chi tiết:</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <div class="flex justify-between mb-2">
                                <span class="text-gray-600 dark:text-gray-400">Diện tích 1 tờ:</span>
                                <span class="font-semibold">${calc.areaSqM.toFixed(4)} m²</span>
                            </div>
                            <div class="flex justify-between mb-2">
                                <span class="text-gray-600 dark:text-gray-400">Tổng số tờ:</span>
                                <span class="font-semibold">${calc.totalSheets.toLocaleString()}</span>
                            </div>
                            <div class="flex justify-between mb-2">
                                <span class="text-gray-600 dark:text-gray-400">GSM:</span>
                                <span class="font-semibold">${calc.gsm} g/m²</span>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between mb-2">
                                <span class="text-gray-600 dark:text-gray-400">Giá/tờ:</span>
                                <span class="font-semibold">${formatCurrency(costPerSheet)}</span>
                            </div>
                            <div class="flex justify-between mb-2">
                                <span class="text-gray-600 dark:text-gray-400">Giá/100 tờ:</span>
                                <span class="font-semibold">${formatCurrency(costPer100)}</span>
                            </div>
                            <div class="flex justify-between mb-2">
                                <span class="text-gray-600 dark:text-gray-400">Giá/1000 tờ:</span>
                                <span class="font-semibold">${formatCurrency(costPer1000)}</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        /**
         * Show recommendations
         */
        function showRecommendations(data) {
            const recommendations = [];

            // GSM recommendations
            if (data.gsm < 70) {
                recommendations.push({
                    type: 'warning',
                    message: `GSM ${data.gsm} rất mỏng, phù hợp cho báo, tờ rơi giá rẻ. Dễ xé và không bền.`
                });
            } else if (data.gsm >= 70 && data.gsm < 100) {
                recommendations.push({
                    type: 'success',
                    message: `GSM ${data.gsm} phù hợp cho văn phòng, thư, tài liệu nội bộ.`
                });
            } else if (data.gsm >= 100 && data.gsm < 170) {
                recommendations.push({
                    type: 'success',
                    message: `GSM ${data.gsm} phù hợp cho brochure, catalogue, tài liệu chất lượng cao.`
                });
            } else if (data.gsm >= 170 && data.gsm < 250) {
                recommendations.push({
                    type: 'info',
                    message: `GSM ${data.gsm} phù hợp cho bìa sách, poster, postcard.`
                });
            } else if (data.gsm >= 250) {
                recommendations.push({
                    type: 'info',
                    message: `GSM ${data.gsm} là cardstock dày, phù hợp cho name card, thiệp, bìa cứng.`
                });
            }

            // Weight recommendations for shipping
            if (data.totalWeightKg > 30) {
                recommendations.push({
                    type: 'warning',
                    message: `Tổng trọng lượng ${data.totalWeightKg.toFixed(0)}kg khá nặng. Cân nhắc vận chuyển xe tải hoặc chia nhỏ đơn hàng.`
                });
            } else if (data.totalWeightKg > 10) {
                recommendations.push({
                    type: 'info',
                    message: `Trọng lượng ${data.totalWeightKg.toFixed(0)}kg có thể vận chuyển bằng xe máy hoặc grab.`
                });
            }

            // Stack height recommendations
            if (data.stackHeightCm > 50) {
                recommendations.push({
                    type: 'warning',
                    message: `Chiều cao chồng giấy ${data.stackHeightCm.toFixed(0)}cm khá cao. Nên chia thành nhiều chồng để tránh đổ.`
                });
            }

            // Paper type specific recommendations
            if (data.paperType === 'newsprint') {
                recommendations.push({
                    type: 'info',
                    message: 'Giấy newsprint giá rẻ nhưng dễ ố vàng theo thời gian, không phù hợp cho tài liệu lưu trữ lâu.'
                });
            } else if (data.paperType === 'art') {
                recommendations.push({
                    type: 'success',
                    message: 'Giấy art cao cấp, bề mặt láng mịn, phù hợp cho in ảnh và catalogue chất lượng cao.'
                });
            } else if (data.paperType === 'duplex') {
                recommendations.push({
                    type: 'info',
                    message: 'Giấy duplex có lớp phủ trắng một mặt, mặt sau là giấy tái chế, tiết kiệm chi phí cho hộp carton.'
                });
            }

            displayRecommendations(recommendations);
        }

        /**
         * Display recommendations
         */
        function displayRecommendations(recommendations) {
            const container = document.getElementById('paper-recommendations');

            if (!container || recommendations.length === 0) return;

            container.innerHTML = '<h4 class="font-bold mb-3">💡 Gợi ý chọn giấy:</h4>';

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
         * Format currency
         */
        function formatCurrency(amount) {
            return new Intl.NumberFormat('vi-VN', {
                style: 'currency',
                currency: 'VND'
            }).format(amount);
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
         * Update GSM options based on paper type
         */
        function updateGsmOptions() {
            const selectedType = paperType.value;
            const gsmOptions = paperTypes[selectedType].gsm;

            if (gsmOptions.length === 0) {
                // Custom type
                paperGsm.innerHTML = '<option value="custom">Tùy chỉnh</option>';
                document.getElementById('custom-gsm-input').classList.remove('hidden');
            } else {
                document.getElementById('custom-gsm-input').classList.add('hidden');
                paperGsm.innerHTML = gsmOptions.map(gsm =>
                    `<option value="${gsm}">${gsm} GSM</option>`
                ).join('');
                paperGsm.innerHTML += '<option value="custom">Tùy chỉnh</option>';
            }
        }

        /**
         * Toggle custom GSM input
         */
        function toggleCustomGsmInput() {
            if (paperGsm.value === 'custom') {
                document.getElementById('custom-gsm-input').classList.remove('hidden');
            } else {
                document.getElementById('custom-gsm-input').classList.add('hidden');
            }
        }

        // Event listeners
        if (calculateBtn) {
            calculateBtn.addEventListener('click', calculatePaper);
        }

        if (paperType) {
            paperType.addEventListener('change', function() {
                updateGsmOptions();
                calculatePaper();
            });
        }

        if (paperGsm) {
            paperGsm.addEventListener('change', function() {
                toggleCustomGsmInput();
                calculatePaper();
            });
        }

        // Auto-calculate on input (debounced)
        let calculateTimeout;
        [customGsm, paperWidth, paperHeight, paperUnit, quantity, sheets].forEach(input => {
            if (input) {
                input.addEventListener('input', function() {
                    clearTimeout(calculateTimeout);
                    calculateTimeout = setTimeout(calculatePaper, 500);
                });
            }
        });

        // Initialize
        updateGsmOptions();
    });

})();
