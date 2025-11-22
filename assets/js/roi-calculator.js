/**
 * ROI Calculator for Advertising Campaigns
 *
 * @package AdPrint_Blog
 * @since 1.0.0
 */

(function() {
    'use strict';

    document.addEventListener('DOMContentLoaded', function() {
        const calculator = document.getElementById('roi-calculator');

        if (!calculator) return;

        // Input elements
        const totalInvestment = document.getElementById('total-investment');
        const totalRevenue = document.getElementById('total-revenue');
        const campaignDuration = document.getElementById('campaign-duration');
        const impressions = document.getElementById('impressions');
        const clicks = document.getElementById('clicks');
        const conversions = document.getElementById('conversions');

        // Output elements
        const roiValue = document.getElementById('roi-value');
        const profitValue = document.getElementById('profit-value');
        const cpmValue = document.getElementById('cpm-value');
        const cpcValue = document.getElementById('cpc-value');
        const cpaValue = document.getElementById('cpa-value');
        const conversionRate = document.getElementById('conversion-rate');
        const ctrValue = document.getElementById('ctr-value');

        const calculateBtn = document.getElementById('calculate-roi');

        /**
         * Calculate all metrics
         */
        function calculateMetrics() {
            // Get values
            const investment = parseFloat(totalInvestment.value) || 0;
            const revenue = parseFloat(totalRevenue.value) || 0;
            const duration = parseInt(campaignDuration.value) || 1;
            const imp = parseInt(impressions.value) || 0;
            const clk = parseInt(clicks.value) || 0;
            const conv = parseInt(conversions.value) || 0;

            if (investment === 0) {
                showError('Vui lòng nhập tổng chi phí đầu tư!');
                return;
            }

            // Calculate ROI
            const profit = revenue - investment;
            const roi = ((revenue - investment) / investment) * 100;

            // Calculate CPM (Cost Per Mille - per 1000 impressions)
            const cpm = imp > 0 ? (investment / imp) * 1000 : 0;

            // Calculate CPC (Cost Per Click)
            const cpc = clk > 0 ? investment / clk : 0;

            // Calculate CPA (Cost Per Acquisition/Conversion)
            const cpa = conv > 0 ? investment / conv : 0;

            // Calculate Conversion Rate
            const convRate = clk > 0 ? (conv / clk) * 100 : 0;

            // Calculate CTR (Click-Through Rate)
            const ctr = imp > 0 ? (clk / imp) * 100 : 0;

            // Display results
            displayResults({
                roi,
                profit,
                cpm,
                cpc,
                cpa,
                convRate,
                ctr
            });

            // Show recommendations
            showRecommendations({
                roi,
                ctr,
                convRate,
                cpa,
                revenue,
                investment
            });
        }

        /**
         * Display results
         */
        function displayResults(metrics) {
            // ROI
            roiValue.textContent = metrics.roi.toFixed(2) + '%';
            roiValue.className = metrics.roi >= 0 ? 'text-green-600' : 'text-red-600';

            // Profit
            profitValue.textContent = formatCurrency(metrics.profit);
            profitValue.className = metrics.profit >= 0 ? 'text-green-600' : 'text-red-600';

            // CPM
            cpmValue.textContent = formatCurrency(metrics.cpm);

            // CPC
            cpcValue.textContent = formatCurrency(metrics.cpc);

            // CPA
            cpaValue.textContent = formatCurrency(metrics.cpa);

            // Conversion Rate
            conversionRate.textContent = metrics.convRate.toFixed(2) + '%';

            // CTR
            ctrValue.textContent = metrics.ctr.toFixed(2) + '%';

            // Animate values
            animateROI(roiValue, 0, metrics.roi, 1000);
        }

        /**
         * Show recommendations
         */
        function showRecommendations(data) {
            const recommendations = [];

            // ROI recommendations
            if (data.roi < 0) {
                recommendations.push({
                    type: 'error',
                    message: 'ROI âm! Chiến dịch đang thua lỗ. Cần xem xét lại chiến lược.'
                });
            } else if (data.roi < 50) {
                recommendations.push({
                    type: 'warning',
                    message: 'ROI thấp. Nên tối ưu targeting và creative để cải thiện hiệu quả.'
                });
            } else if (data.roi >= 100) {
                recommendations.push({
                    type: 'success',
                    message: 'ROI tuyệt vời! Chiến dịch rất hiệu quả, cân nhắc scale up.'
                });
            }

            // CTR recommendations
            if (data.ctr < 1) {
                recommendations.push({
                    type: 'warning',
                    message: 'CTR thấp (< 1%). Cần cải thiện creative và ad copy.'
                });
            } else if (data.ctr >= 3) {
                recommendations.push({
                    type: 'success',
                    message: 'CTR tốt (≥ 3%). Ad creative đang hoạt động hiệu quả.'
                });
            }

            // Conversion Rate recommendations
            if (data.convRate < 2) {
                recommendations.push({
                    type: 'warning',
                    message: 'Tỷ lệ chuyển đổi thấp. Kiểm tra landing page và CTA.'
                });
            } else if (data.convRate >= 5) {
                recommendations.push({
                    type: 'success',
                    message: 'Tỷ lệ chuyển đổi tốt! Landing page đang hoạt động hiệu quả.'
                });
            }

            // CPA recommendations
            const averageOrderValue = data.revenue / parseInt(conversions.value || 1);
            if (data.cpa > averageOrderValue) {
                recommendations.push({
                    type: 'error',
                    message: `CPA cao hơn giá trị đơn hàng trung bình. Cần giảm chi phí hoặc tăng AOV.`
                });
            }

            // Display recommendations
            displayRecommendations(recommendations);
        }

        /**
         * Display recommendations
         */
        function displayRecommendations(recommendations) {
            const container = document.getElementById('recommendations');

            if (!container || recommendations.length === 0) return;

            container.innerHTML = '<h4 class="font-bold mb-3">📊 Đề xuất tối ưu:</h4>';

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
         * Animate ROI value
         */
        function animateROI(element, start, end, duration) {
            const range = end - start;
            const increment = range / (duration / 16);
            let current = start;

            const timer = setInterval(function() {
                current += increment;
                if ((increment > 0 && current >= end) || (increment < 0 && current <= end)) {
                    current = end;
                    clearInterval(timer);
                }
                element.textContent = current.toFixed(2) + '%';
            }, 16);
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

        // Event listeners
        if (calculateBtn) {
            calculateBtn.addEventListener('click', calculateMetrics);
        }

        // Auto-calculate on input (debounced)
        let calculateTimeout;
        [totalInvestment, totalRevenue, campaignDuration, impressions, clicks, conversions].forEach(input => {
            if (input) {
                input.addEventListener('input', function() {
                    clearTimeout(calculateTimeout);
                    calculateTimeout = setTimeout(calculateMetrics, 500);
                });
            }
        });
    });

})();
