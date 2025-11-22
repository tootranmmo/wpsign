/**
 * File Checker for Print-Ready Files
 *
 * @package AdPrint_Blog
 * @since 1.0.0
 */

(function() {
    'use strict';

    document.addEventListener('DOMContentLoaded', function() {
        const checker = document.getElementById('file-checker');

        if (!checker) return;

        // Input elements
        const fileInput = document.getElementById('file-input');
        const dropZone = document.getElementById('drop-zone');
        const checkBtn = document.getElementById('check-file');

        // Output elements
        const resultsContainer = document.getElementById('check-results');
        const fileInfo = document.getElementById('file-info');
        const issuesContainer = document.getElementById('issues-container');
        const checklistContainer = document.getElementById('checklist-container');

        // Accepted file types for print
        const acceptedTypes = [
            'application/pdf',
            'application/postscript', // .ai, .eps
            'image/vnd.adobe.photoshop', // .psd
            'application/x-indesign', // .indd
            'image/tiff',
            'image/jpeg',
            'image/png'
        ];

        const acceptedExtensions = [
            '.pdf', '.ai', '.eps', '.psd', '.indd', '.tiff', '.tif', '.jpg', '.jpeg', '.png'
        ];

        // File upload via drag and drop
        if (dropZone) {
            dropZone.addEventListener('dragover', function(e) {
                e.preventDefault();
                dropZone.classList.add('border-primary-500', 'bg-primary-50', 'dark:bg-primary-900');
            });

            dropZone.addEventListener('dragleave', function() {
                dropZone.classList.remove('border-primary-500', 'bg-primary-50', 'dark:bg-primary-900');
            });

            dropZone.addEventListener('drop', function(e) {
                e.preventDefault();
                dropZone.classList.remove('border-primary-500', 'bg-primary-50', 'dark:bg-primary-900');

                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    handleFileSelect(files[0]);
                }
            });

            dropZone.addEventListener('click', function() {
                fileInput.click();
            });
        }

        if (fileInput) {
            fileInput.addEventListener('change', function(e) {
                if (e.target.files.length > 0) {
                    handleFileSelect(e.target.files[0]);
                }
            });
        }

        if (checkBtn) {
            checkBtn.addEventListener('click', function() {
                if (fileInput.files.length > 0) {
                    handleFileSelect(fileInput.files[0]);
                } else {
                    showError('Vui lòng chọn file để kiểm tra!');
                }
            });
        }

        /**
         * Handle file selection
         */
        function handleFileSelect(file) {
            // Show loading state
            showLoading();

            // Validate file
            setTimeout(() => {
                validateFile(file);
            }, 500);
        }

        /**
         * Validate file for print readiness
         */
        function validateFile(file) {
            const issues = [];
            const warnings = [];
            const success = [];

            // Get file info
            const fileName = file.name;
            const fileSize = file.size;
            const fileType = file.type;
            const fileExtension = fileName.substring(fileName.lastIndexOf('.')).toLowerCase();

            // Check 1: File type
            const isAcceptedType = acceptedTypes.includes(fileType) || acceptedExtensions.includes(fileExtension);
            if (!isAcceptedType) {
                issues.push('Định dạng file không được khuyến nghị cho in ấn. Nên sử dụng PDF, AI, hoặc PSD.');
            } else {
                success.push('Định dạng file phù hợp cho in ấn.');
            }

            // Check 2: File extension
            if (fileExtension === '.pdf') {
                success.push('PDF là định dạng tốt nhất cho in ấn.');
            } else if (fileExtension === '.ai' || fileExtension === '.eps') {
                success.push('File vector (AI/EPS) đảm bảo chất lượng ở mọi kích thước.');
            } else if (fileExtension === '.psd') {
                warnings.push('File PSD nên được chuyển sang PDF hoặc TIFF trước khi gửi in.');
            } else if (fileExtension === '.jpg' || fileExtension === '.jpeg' || fileExtension === '.png') {
                warnings.push('File ảnh (JPG/PNG) cần đảm bảo độ phân giải tối thiểu 300 DPI.');
            }

            // Check 3: File size
            const fileSizeMB = fileSize / (1024 * 1024);
            if (fileSizeMB < 0.1) {
                warnings.push(`File chỉ ${fileSizeMB.toFixed(2)}MB, có thể độ phân giải thấp. Kiểm tra lại chất lượng.`);
            } else if (fileSizeMB > 500) {
                warnings.push(`File ${fileSizeMB.toFixed(0)}MB rất lớn. Cân nhắc nén hoặc giảm độ phân giải không cần thiết.`);
            } else {
                success.push(`Kích thước file ${fileSizeMB.toFixed(2)}MB phù hợp.`);
            }

            // Check 4: File name
            const hasSpecialChars = /[^a-zA-Z0-9._-]/.test(fileName);
            const hasSpaces = /\s/.test(fileName);
            if (hasSpecialChars || hasSpaces) {
                warnings.push('Tên file có ký tự đặc biệt hoặc khoảng trắng. Nên đổi tên đơn giản hơn.');
            } else {
                success.push('Tên file hợp lệ, không có ký tự đặc biệt.');
            }

            // For images, try to check dimensions (if browser supports it)
            if (fileExtension === '.jpg' || fileExtension === '.jpeg' || fileExtension === '.png') {
                checkImageDimensions(file, issues, warnings, success);
            } else {
                // Display results for non-image files
                displayResults(file, issues, warnings, success);
            }
        }

        /**
         * Check image dimensions and DPI
         */
        function checkImageDimensions(file, issues, warnings, success) {
            const reader = new FileReader();

            reader.onload = function(e) {
                const img = new Image();

                img.onload = function() {
                    const width = img.width;
                    const height = img.height;
                    const megapixels = (width * height) / 1000000;

                    // Check resolution (rough estimate)
                    // For A4 at 300 DPI: 2480 × 3508 px
                    if (width < 2480 && height < 3508) {
                        warnings.push(`Độ phân giải ${width}×${height}px có thể thấp cho in A4. Khuyến nghị tối thiểu 2480×3508px (300 DPI).`);
                    } else {
                        success.push(`Độ phân giải ${width}×${height}px (${megapixels.toFixed(1)}MP) phù hợp cho in ấn.`);
                    }

                    // Check aspect ratio
                    const aspectRatio = (width / height).toFixed(2);
                    success.push(`Tỷ lệ khung hình: ${aspectRatio}:1`);

                    displayResults(file, issues, warnings, success, { width, height, megapixels });
                };

                img.src = e.target.result;
            };

            reader.readAsDataURL(file);
        }

        /**
         * Display validation results
         */
        function displayResults(file, issues, warnings, success, imageInfo = null) {
            resultsContainer.classList.remove('hidden');

            // File info
            const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);
            const fileExtension = file.name.substring(file.name.lastIndexOf('.')).toLowerCase();

            let fileInfoHTML = `
                <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <h4 class="font-bold mb-3">📄 Thông tin file:</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Tên file:</span>
                            <span class="font-semibold">${file.name}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Định dạng:</span>
                            <span class="font-semibold">${fileExtension.toUpperCase()}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Kích thước:</span>
                            <span class="font-semibold">${fileSizeMB} MB</span>
                        </div>
            `;

            if (imageInfo) {
                fileInfoHTML += `
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Độ phân giải:</span>
                            <span class="font-semibold">${imageInfo.width} × ${imageInfo.height} px</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Megapixels:</span>
                            <span class="font-semibold">${imageInfo.megapixels.toFixed(1)} MP</span>
                        </div>
                `;
            }

            fileInfoHTML += `
                    </div>
                </div>
            `;

            fileInfo.innerHTML = fileInfoHTML;

            // Issues and warnings
            let issuesHTML = '';

            if (issues.length > 0) {
                issuesHTML += '<div class="mb-4"><h5 class="font-bold text-red-600 dark:text-red-400 mb-2">❌ Vấn đề cần khắc phục:</h5><ul class="space-y-2">';
                issues.forEach(issue => {
                    issuesHTML += `<li class="p-3 bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 rounded-lg">${issue}</li>`;
                });
                issuesHTML += '</ul></div>';
            }

            if (warnings.length > 0) {
                issuesHTML += '<div class="mb-4"><h5 class="font-bold text-yellow-600 dark:text-yellow-400 mb-2">⚠️ Cảnh báo:</h5><ul class="space-y-2">';
                warnings.forEach(warning => {
                    issuesHTML += `<li class="p-3 bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 rounded-lg">${warning}</li>`;
                });
                issuesHTML += '</ul></div>';
            }

            if (success.length > 0) {
                issuesHTML += '<div><h5 class="font-bold text-green-600 dark:text-green-400 mb-2">✅ Đạt yêu cầu:</h5><ul class="space-y-2">';
                success.forEach(item => {
                    issuesHTML += `<li class="p-3 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-lg">${item}</li>`;
                });
                issuesHTML += '</ul></div>';
            }

            issuesContainer.innerHTML = issuesHTML;

            // Print-ready checklist
            displayChecklist(file, imageInfo);

            // Overall status
            displayOverallStatus(issues.length, warnings.length);
        }

        /**
         * Display print-ready checklist
         */
        function displayChecklist(file, imageInfo) {
            const fileExtension = file.name.substring(file.name.lastIndexOf('.')).toLowerCase();

            const checklist = [
                {
                    item: 'Độ phân giải tối thiểu 300 DPI (cho ảnh)',
                    status: imageInfo && imageInfo.width >= 2480 ? 'checked' : 'unchecked',
                    applicable: ['.jpg', '.jpeg', '.png', '.tiff', '.tif'].includes(fileExtension)
                },
                {
                    item: 'Định dạng file phù hợp (PDF, AI, PSD)',
                    status: ['.pdf', '.ai', '.eps', '.psd'].includes(fileExtension) ? 'checked' : 'unchecked',
                    applicable: true
                },
                {
                    item: 'Màu sắc: CMYK cho in offset',
                    status: 'unknown',
                    applicable: true
                },
                {
                    item: 'Bleed (vùng cắt) 3mm mỗi cạnh',
                    status: 'unknown',
                    applicable: true
                },
                {
                    item: 'Fonts được nhúng hoặc outline',
                    status: 'unknown',
                    applicable: ['.pdf', '.ai', '.eps'].includes(fileExtension)
                },
                {
                    item: 'Kích thước file hợp lý (< 500MB)',
                    status: (file.size / (1024 * 1024)) < 500 ? 'checked' : 'unchecked',
                    applicable: true
                },
                {
                    item: 'Tên file không có ký tự đặc biệt',
                    status: !/[^a-zA-Z0-9._-]/.test(file.name) && !/\s/.test(file.name) ? 'checked' : 'unchecked',
                    applicable: true
                }
            ];

            let checklistHTML = '<div class="p-4 bg-blue-50 dark:bg-blue-900 rounded-lg"><h4 class="font-bold mb-3 text-blue-900 dark:text-blue-100">📋 Checklist in ấn:</h4><ul class="space-y-2">';

            checklist.forEach(item => {
                if (item.applicable) {
                    let icon, statusClass;
                    if (item.status === 'checked') {
                        icon = '✅';
                        statusClass = 'text-green-700 dark:text-green-300';
                    } else if (item.status === 'unchecked') {
                        icon = '❌';
                        statusClass = 'text-red-700 dark:text-red-300';
                    } else {
                        icon = '❓';
                        statusClass = 'text-gray-700 dark:text-gray-300';
                    }

                    checklistHTML += `<li class="${statusClass}"><strong>${icon}</strong> ${item.item}</li>`;
                }
            });

            checklistHTML += '</ul></div>';
            checklistContainer.innerHTML = checklistHTML;
        }

        /**
         * Display overall status
         */
        function displayOverallStatus(issuesCount, warningsCount) {
            const statusContainer = document.getElementById('overall-status');
            if (!statusContainer) return;

            let statusHTML, statusClass, statusIcon, statusText;

            if (issuesCount === 0 && warningsCount === 0) {
                statusClass = 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 border-green-500';
                statusIcon = '✅';
                statusText = 'File sẵn sàng in ấn! Tuy nhiên, hãy kiểm tra lại các mục "unknown" trong checklist.';
            } else if (issuesCount > 0) {
                statusClass = 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 border-red-500';
                statusIcon = '❌';
                statusText = `File có ${issuesCount} vấn đề cần khắc phục trước khi in.`;
            } else {
                statusClass = 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 border-yellow-500';
                statusIcon = '⚠️';
                statusText = `File có ${warningsCount} cảnh báo. Kiểm tra kỹ trước khi gửi in.`;
            }

            statusHTML = `
                <div class="p-4 border-2 rounded-lg ${statusClass}">
                    <h3 class="font-bold text-lg mb-2">${statusIcon} Tổng quan</h3>
                    <p>${statusText}</p>
                </div>
            `;

            statusContainer.innerHTML = statusHTML;
        }

        /**
         * Show loading state
         */
        function showLoading() {
            resultsContainer.classList.remove('hidden');
            fileInfo.innerHTML = '<div class="text-center py-8"><div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600 mx-auto"></div><p class="mt-4 text-gray-600 dark:text-gray-400">Đang kiểm tra file...</p></div>';
            issuesContainer.innerHTML = '';
            checklistContainer.innerHTML = '';
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
    });

})();
