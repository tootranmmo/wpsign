/**
 * Quote Request Form Handler
 *
 * @package AdPrint_Blog
 * @since 1.0.0
 */

(function() {
    'use strict';

    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('quote-form');

        if (!form) return;

        // Form elements
        const productType = document.getElementById('quote-product-type');
        const customProduct = document.getElementById('custom-product');
        const quantity = document.getElementById('quote-quantity');
        const paperType = document.getElementById('quote-paper-type');
        const printSize = document.getElementById('quote-size');
        const colorType = document.getElementById('quote-color');
        const finishing = document.getElementById('quote-finishing');
        const urgency = document.getElementById('quote-urgency');
        const fileUpload = document.getElementById('quote-file');
        const fileName = document.getElementById('file-name');
        const additionalNotes = document.getElementById('additional-notes');

        // Contact info
        const contactName = document.getElementById('contact-name');
        const contactEmail = document.getElementById('contact-email');
        const contactPhone = document.getElementById('contact-phone');
        const contactCompany = document.getElementById('contact-company');

        // Estimated price display
        const estimatedPrice = document.getElementById('estimated-price');
        const priceBreakdown = document.getElementById('price-breakdown');

        const submitBtn = document.getElementById('submit-quote');

        // Pricing data (similar to price calculator)
        const pricing = {
            'flyer': { base: 500, name: 'Tờ rơi / Flyer' },
            'brochure': { base: 2000, name: 'Brochure' },
            'namecard': { base: 200, name: 'Name Card' },
            'poster': { base: 5000, name: 'Poster' },
            'banner': { base: 50000, name: 'Banner' },
            'catalog': { base: 10000, name: 'Catalogue' },
            'book': { base: 15000, name: 'Sách / Tạp chí' },
            'packaging': { base: 8000, name: 'Bao bì / Hộp' },
            'sticker': { base: 300, name: 'Sticker / Decal' },
            'custom': { base: 1000, name: 'Khác' }
        };

        const paperMultipliers = {
            'standard': 1,
            'glossy': 1.2,
            'matte': 1.15,
            'premium': 1.5,
            'cardstock': 2
        };

        const colorMultipliers = {
            'bw': 0.5,
            '1color': 0.7,
            '4color': 1
        };

        const finishingCosts = {
            'none': 0,
            'lamination': 500,
            'uv': 1000,
            'embossing': 2000,
            'die-cut': 1500
        };

        const urgencyMultipliers = {
            'normal': 1,
            'urgent': 1.3,
            'express': 1.6
        };

        /**
         * Calculate estimated price
         */
        function calculateEstimate() {
            const product = productType.value;
            const qty = parseInt(quantity.value) || 1;
            const paper = paperType.value;
            const color = colorType.value;
            const finish = finishing.value;
            const urgent = urgency.value;

            if (!product || product === '') {
                return;
            }

            // Base price
            let basePrice = pricing[product]?.base || 1000;

            // Quantity discount
            const qtyDiscount = getQuantityDiscount(qty);

            // Calculate total
            let unitPrice = basePrice * paperMultipliers[paper] * colorMultipliers[color];
            unitPrice += finishingCosts[finish] / Math.max(qty, 1);
            unitPrice *= qtyDiscount;

            let totalPrice = unitPrice * qty * urgencyMultipliers[urgent];

            // Display estimate
            displayEstimate(totalPrice, {
                basePrice,
                qty,
                paper,
                color,
                finish,
                urgent,
                unitPrice,
                qtyDiscount
            });
        }

        /**
         * Get quantity discount
         */
        function getQuantityDiscount(qty) {
            if (qty >= 10000) return 0.7;
            if (qty >= 5000) return 0.75;
            if (qty >= 1000) return 0.8;
            if (qty >= 500) return 0.85;
            if (qty >= 100) return 0.9;
            return 1;
        }

        /**
         * Display price estimate
         */
        function displayEstimate(total, details) {
            if (!estimatedPrice) return;

            estimatedPrice.textContent = formatCurrency(total);
            estimatedPrice.className = 'text-3xl font-bold text-primary-600 dark:text-primary-400';

            // Show breakdown
            if (priceBreakdown) {
                const discount = ((1 - details.qtyDiscount) * 100).toFixed(0);

                priceBreakdown.innerHTML = `
                    <div class="mt-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg text-sm">
                        <h5 class="font-bold mb-2">Chi tiết báo giá:</h5>
                        <div class="space-y-1">
                            <div class="flex justify-between">
                                <span>Đơn giá cơ bản:</span>
                                <span>${formatCurrency(details.basePrice)}/sp</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Loại giấy (×${paperMultipliers[details.paper]}):</span>
                                <span>${details.paper}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Màu in (×${colorMultipliers[details.color]}):</span>
                                <span>${details.color}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Gia công:</span>
                                <span>+${formatCurrency(finishingCosts[details.finish])}</span>
                            </div>
                            ${discount > 0 ? `
                            <div class="flex justify-between text-green-600 dark:text-green-400">
                                <span>Giảm giá số lượng:</span>
                                <span>-${discount}%</span>
                            </div>
                            ` : ''}
                            ${details.urgent !== 'normal' ? `
                            <div class="flex justify-between text-orange-600 dark:text-orange-400">
                                <span>Phí gấp (×${urgencyMultipliers[details.urgent]}):</span>
                                <span>${details.urgent === 'urgent' ? '30%' : '60%'}</span>
                            </div>
                            ` : ''}
                            <div class="flex justify-between border-t border-gray-300 dark:border-gray-600 pt-2 mt-2 font-bold">
                                <span>Tổng cộng:</span>
                                <span class="text-primary-600 dark:text-primary-400">${formatCurrency(total)}</span>
                            </div>
                        </div>
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">* Giá ước tính, chưa bao gồm VAT. Báo giá chính thức sẽ được gửi qua email.</p>
                    </div>
                `;
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
         * Handle file upload
         */
        function handleFileUpload(e) {
            const file = e.target.files[0];
            if (file) {
                const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);
                fileName.textContent = `${file.name} (${fileSizeMB}MB)`;
                fileName.classList.remove('hidden');
            }
        }

        /**
         * Validate form
         */
        function validateForm() {
            const errors = [];

            if (!productType.value) {
                errors.push('Vui lòng chọn loại sản phẩm');
            }

            if (!quantity.value || parseInt(quantity.value) < 1) {
                errors.push('Vui lòng nhập số lượng hợp lệ');
            }

            if (!contactName.value.trim()) {
                errors.push('Vui lòng nhập tên liên hệ');
            }

            if (!contactEmail.value.trim()) {
                errors.push('Vui lòng nhập email');
            } else if (!isValidEmail(contactEmail.value)) {
                errors.push('Email không hợp lệ');
            }

            if (!contactPhone.value.trim()) {
                errors.push('Vui lòng nhập số điện thoại');
            }

            return errors;
        }

        /**
         * Validate email
         */
        function isValidEmail(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        }

        /**
         * Submit form via AJAX
         */
        function submitForm(e) {
            e.preventDefault();

            // Validate
            const errors = validateForm();
            if (errors.length > 0) {
                showErrors(errors);
                return;
            }

            // Show loading
            submitBtn.disabled = true;
            submitBtn.textContent = 'Đang gửi...';

            // Prepare form data
            const formData = new FormData(form);
            formData.append('action', 'submit_quote_request');
            formData.append('nonce', adprintData.nonce);

            // Submit via AJAX
            fetch(adprintData.ajaxUrl, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSuccess('Yêu cầu báo giá đã được gửi thành công! Chúng tôi sẽ liên hệ với bạn trong vòng 24h.');
                    form.reset();
                    estimatedPrice.textContent = '0đ';
                    priceBreakdown.innerHTML = '';
                    fileName.classList.add('hidden');
                } else {
                    showError(data.data.message || 'Có lỗi xảy ra. Vui lòng thử lại.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showError('Có lỗi xảy ra. Vui lòng thử lại sau.');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Gửi yêu cầu báo giá';
            });
        }

        /**
         * Show validation errors
         */
        function showErrors(errors) {
            const errorContainer = document.getElementById('form-errors');
            if (!errorContainer) return;

            errorContainer.innerHTML = `
                <div class="mb-4 p-4 bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 rounded-lg">
                    <p class="font-bold mb-2">❌ Vui lòng kiểm tra lại:</p>
                    <ul class="list-disc list-inside space-y-1">
                        ${errors.map(error => `<li>${error}</li>`).join('')}
                    </ul>
                </div>
            `;

            errorContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }

        /**
         * Show success message
         */
        function showSuccess(message) {
            const notification = document.createElement('div');
            notification.className = 'fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg bg-green-500 text-white max-w-md';
            notification.innerHTML = `<strong>✅ Thành công!</strong><br>${message}`;

            document.body.appendChild(notification);

            setTimeout(() => notification.remove(), 5000);
        }

        /**
         * Show error message
         */
        function showError(message) {
            const notification = document.createElement('div');
            notification.className = 'fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg bg-red-500 text-white';
            notification.textContent = message;

            document.body.appendChild(notification);

            setTimeout(() => notification.remove(), 3000);
        }

        /**
         * Toggle custom product input
         */
        function toggleCustomProduct() {
            if (productType.value === 'custom') {
                customProduct.classList.remove('hidden');
            } else {
                customProduct.classList.add('hidden');
            }
        }

        // Event listeners
        if (form) {
            form.addEventListener('submit', submitForm);
        }

        if (fileUpload) {
            fileUpload.addEventListener('change', handleFileUpload);
        }

        if (productType) {
            productType.addEventListener('change', function() {
                toggleCustomProduct();
                calculateEstimate();
            });
        }

        // Auto-calculate estimate on input change (debounced)
        let estimateTimeout;
        [quantity, paperType, printSize, colorType, finishing, urgency].forEach(input => {
            if (input) {
                input.addEventListener('change', function() {
                    clearTimeout(estimateTimeout);
                    estimateTimeout = setTimeout(calculateEstimate, 300);
                });
            }
        });

        // Initialize
        toggleCustomProduct();
    });

})();
