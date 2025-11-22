/**
 * Color Converter (RGB ↔ CMYK ↔ HEX)
 *
 * @package AdPrint_Blog
 * @since 1.0.0
 */

(function() {
    'use strict';

    document.addEventListener('DOMContentLoaded', function() {
        const converter = document.getElementById('color-converter');

        if (!converter) return;

        // Input elements
        const hexInput = document.getElementById('hex-input');
        const rgbR = document.getElementById('rgb-r');
        const rgbG = document.getElementById('rgb-g');
        const rgbB = document.getElementById('rgb-b');
        const cmykC = document.getElementById('cmyk-c');
        const cmykM = document.getElementById('cmyk-m');
        const cmykY = document.getElementById('cmyk-y');
        const cmykK = document.getElementById('cmyk-k');
        const colorPreview = document.getElementById('color-preview');

        /**
         * HEX to RGB
         */
        function hexToRgb(hex) {
            hex = hex.replace('#', '');
            if (hex.length === 3) {
                hex = hex[0] + hex[0] + hex[1] + hex[1] + hex[2] + hex[2];
            }
            const r = parseInt(hex.substring(0, 2), 16);
            const g = parseInt(hex.substring(2, 4), 16);
            const b = parseInt(hex.substring(4, 6), 16);
            return { r, g, b };
        }

        /**
         * RGB to HEX
         */
        function rgbToHex(r, g, b) {
            return '#' + [r, g, b].map(x => {
                const hex = Math.round(x).toString(16);
                return hex.length === 1 ? '0' + hex : hex;
            }).join('');
        }

        /**
         * RGB to CMYK
         */
        function rgbToCmyk(r, g, b) {
            let c = 1 - (r / 255);
            let m = 1 - (g / 255);
            let y = 1 - (b / 255);
            let k = Math.min(c, m, y);

            if (k === 1) {
                c = m = y = 0;
            } else {
                c = (c - k) / (1 - k);
                m = (m - k) / (1 - k);
                y = (y - k) / (1 - k);
            }

            return {
                c: Math.round(c * 100),
                m: Math.round(m * 100),
                y: Math.round(y * 100),
                k: Math.round(k * 100)
            };
        }

        /**
         * CMYK to RGB
         */
        function cmykToRgb(c, m, y, k) {
            c = c / 100;
            m = m / 100;
            y = y / 100;
            k = k / 100;

            const r = 255 * (1 - c) * (1 - k);
            const g = 255 * (1 - m) * (1 - k);
            const b = 255 * (1 - y) * (1 - k);

            return {
                r: Math.round(r),
                g: Math.round(g),
                b: Math.round(b)
            };
        }

        /**
         * Update all color values from HEX
         */
        function updateFromHex() {
            const hex = hexInput.value;
            if (!/^#?[0-9A-F]{3,6}$/i.test(hex)) return;

            const rgb = hexToRgb(hex);
            const cmyk = rgbToCmyk(rgb.r, rgb.g, rgb.b);

            // Update RGB
            rgbR.value = rgb.r;
            rgbG.value = rgb.g;
            rgbB.value = rgb.b;

            // Update CMYK
            cmykC.value = cmyk.c;
            cmykM.value = cmyk.m;
            cmykY.value = cmyk.y;
            cmykK.value = cmyk.k;

            // Update preview
            updatePreview(rgb);
        }

        /**
         * Update all color values from RGB
         */
        function updateFromRgb() {
            const r = parseInt(rgbR.value) || 0;
            const g = parseInt(rgbG.value) || 0;
            const b = parseInt(rgbB.value) || 0;

            const hex = rgbToHex(r, g, b);
            const cmyk = rgbToCmyk(r, g, b);

            // Update HEX
            hexInput.value = hex;

            // Update CMYK
            cmykC.value = cmyk.c;
            cmykM.value = cmyk.m;
            cmykY.value = cmyk.y;
            cmykK.value = cmyk.k;

            // Update preview
            updatePreview({ r, g, b });
        }

        /**
         * Update all color values from CMYK
         */
        function updateFromCmyk() {
            const c = parseInt(cmykC.value) || 0;
            const m = parseInt(cmykM.value) || 0;
            const y = parseInt(cmykY.value) || 0;
            const k = parseInt(cmykK.value) || 0;

            const rgb = cmykToRgb(c, m, y, k);
            const hex = rgbToHex(rgb.r, rgb.g, rgb.b);

            // Update RGB
            rgbR.value = rgb.r;
            rgbG.value = rgb.g;
            rgbB.value = rgb.b;

            // Update HEX
            hexInput.value = hex;

            // Update preview
            updatePreview(rgb);
        }

        /**
         * Update color preview
         */
        function updatePreview(rgb) {
            if (colorPreview) {
                colorPreview.style.backgroundColor = `rgb(${rgb.r}, ${rgb.g}, ${rgb.b})`;
            }
        }

        /**
         * Get color name (approximate)
         */
        function getColorName(rgb) {
            const colors = {
                'Đỏ': { r: 255, g: 0, b: 0 },
                'Xanh lá': { r: 0, g: 255, b: 0 },
                'Xanh dương': { r: 0, g: 0, b: 255 },
                'Vàng': { r: 255, g: 255, b: 0 },
                'Tím': { r: 128, g: 0, b: 128 },
                'Cam': { r: 255, g: 165, b: 0 },
                'Hồng': { r: 255, g: 192, b: 203 },
                'Đen': { r: 0, g: 0, b: 0 },
                'Trắng': { r: 255, g: 255, b: 255 },
                'Xám': { r: 128, g: 128, b: 128 }
            };

            let closest = 'N/A';
            let minDistance = Infinity;

            for (const [name, color] of Object.entries(colors)) {
                const distance = Math.sqrt(
                    Math.pow(rgb.r - color.r, 2) +
                    Math.pow(rgb.g - color.g, 2) +
                    Math.pow(rgb.b - color.b, 2)
                );

                if (distance < minDistance) {
                    minDistance = distance;
                    closest = name;
                }
            }

            return closest;
        }

        // Event listeners
        if (hexInput) {
            hexInput.addEventListener('input', updateFromHex);
        }

        [rgbR, rgbG, rgbB].forEach(input => {
            if (input) {
                input.addEventListener('input', function() {
                    // Validate 0-255
                    if (this.value < 0) this.value = 0;
                    if (this.value > 255) this.value = 255;
                    updateFromRgb();
                });
            }
        });

        [cmykC, cmykM, cmykY, cmykK].forEach(input => {
            if (input) {
                input.addEventListener('input', function() {
                    // Validate 0-100
                    if (this.value < 0) this.value = 0;
                    if (this.value > 100) this.value = 100;
                    updateFromCmyk();
                });
            }
        });

        // Copy to clipboard
        const copyButtons = converter.querySelectorAll('[data-copy]');
        copyButtons.forEach(button => {
            button.addEventListener('click', function() {
                const value = this.dataset.copy;
                const input = converter.querySelector(`#${value}`);

                if (input) {
                    navigator.clipboard.writeText(input.value).then(() => {
                        showCopyNotification(this);
                    });
                }
            });
        });

        function showCopyNotification(button) {
            const originalText = button.textContent;
            button.textContent = '✓ Đã copy!';
            button.classList.add('bg-green-500');

            setTimeout(() => {
                button.textContent = originalText;
                button.classList.remove('bg-green-500');
            }, 2000);
        }

        // Initialize with default color
        if (hexInput && hexInput.value) {
            updateFromHex();
        }
    });

})();
