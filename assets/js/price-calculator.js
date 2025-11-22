/**
 * Price Calculator for Printing Services
 *
 * @package AdPrint_Blog
 * @since 1.0.0
 */

(function() {
    'use strict';

    document.addEventListener('DOMContentLoaded', function() {
        const calculator = document.getElementById('price-calculator');

        if (!calculator) return;

        // Elements
        const productType = document.getElementById('product-type');
        const quantity = document.getElementById('quantity');
        const paperType = document.getElementById('paper-type');
        const size = document.getElementById('size');
        const colors = document.getElementById('colors');
        const finishing = document.getElementById('finishing');
        const totalPrice = document.getElementById('total-price');
        const unitPrice = document.getElementById('unit-price');
        const calculateBtn = document.getElementById('calculate-price');

        // Pricing data (VNĐ)
        const pricing = {
            // Base prices per unit
            'flyer': {
                base: 500,
                sizes: {
                    'a4': 1,
                    'a5': 0.7,
                    'a6': 0.5
                }
            },
            'brochure': {
                base: 2000,
                sizes: {
                    'a4': 1,
                    'a5': 0.8
                }
            },
            'poster': {
                base: 5000,
                sizes: {
                    'a3': 1,
                    'a2': 1.5,
                    'a1': 2,
                    'a0': 3
                }
            },
            'namecard': {
                base: 100,
                sizes: {
                    '90x50': 1
                }
            },
            'banner': {
                base: 150, // per m²
                sizes: {
                    '1x2': 2,
                    '2x3': 6,
                    '3x5': 15
                }
            }
        };

        // Paper multipliers
        const paperMultiplier = {
            'standard': 1,
            'glossy': 1.3,
            'matte': 1.2,
            'premium': 1.5
        };

        // Color multipliers
        const colorMultiplier = {
            'bw': 1,
            '1color': 1.2,
            '4color': 1.5
        };

        // Finishing multipliers
        const finishingMultiplier = {
            'none': 1,
            'lamination': 1.2,
            'uv': 1.4,
            'embossing': 1.6
        };

        // Quantity discounts
        function getQuantityDiscount(qty) {
            if (qty >= 10000) return 0.7;
            if (qty >= 5000) return 0.75;
            if (qty >= 1000) return 0.8;
            if (qty >= 500) return 0.85;
            if (qty >= 100) return 0.9;
            return 1;
        }

        // Calculate price
        function calculatePrice() {
            const product = productType.value;
            const qty = parseInt(quantity.value) || 1;
            const paper = paperType.value;
            const sizeValue = size.value;
            const color = colors.value;
            const finish = finishing.value;

            if (!product || !qty || !paper || !sizeValue || !color) {
                return;
            }

            // Base calculation
            const productData = pricing[product];
            const basePrice = productData.base;
            const sizeMultiplier = productData.sizes[sizeValue] || 1;

            // Calculate unit price
            let pricePerUnit = basePrice * sizeMultiplier;
            pricePerUnit *= paperMultiplier[paper];
            pricePerUnit *= colorMultiplier[color];
            pricePerUnit *= finishingMultiplier[finish];

            // Apply quantity discount
            const discount = getQuantityDiscount(qty);
            pricePerUnit *= discount;

            // Calculate total
            const total = pricePerUnit * qty;

            // Display results
            unitPrice.textContent = formatCurrency(pricePerUnit);
            totalPrice.textContent = formatCurrency(total);

            // Show discount if applicable
            if (discount < 1) {
                const discountPercent = Math.round((1 - discount) * 100);
                showNotification(`Giảm giá ${discountPercent}% cho đơn hàng từ ${qty} sản phẩm!`, 'success');
            }

            // Animate numbers
            animateValue(totalPrice, 0, total, 500);
        }

        // Format currency
        function formatCurrency(amount) {
            return new Intl.NumberFormat('vi-VN', {
                style: 'currency',
                currency: 'VND'
            }).format(amount);
        }

        // Animate number
        function animateValue(element, start, end, duration) {
            const range = end - start;
            const increment = range / (duration / 16);
            let current = start;

            const timer = setInterval(function() {
                current += increment;
                if ((increment > 0 && current >= end) || (increment < 0 && current <= end)) {
                    current = end;
                    clearInterval(timer);
                }
                element.textContent = formatCurrency(Math.round(current));
            }, 16);
        }

        // Show notification
        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.className = `calculator-notification ${type} fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg bg-green-500 text-white`;
            notification.textContent = message;

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.classList.add('fade-out');
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }

        // Event listeners
        if (calculateBtn) {
            calculateBtn.addEventListener('click', calculatePrice);
        }

        // Auto-calculate on change
        [productType, quantity, paperType, size, colors, finishing].forEach(element => {
            if (element) {
                element.addEventListener('change', calculatePrice);
            }
        });

        // Quantity input validation
        if (quantity) {
            quantity.addEventListener('input', function() {
                if (this.value < 1) this.value = 1;
                if (this.value > 100000) this.value = 100000;
            });
        }
    });

})();
