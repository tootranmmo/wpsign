/**
 * Theme Customizer Live Preview
 *
 * @package AdPrint_Blog
 * @since 1.0.0
 */

(function($) {
    'use strict';

    // Site title
    wp.customize('blogname', function(value) {
        value.bind(function(newval) {
            $('.site-title a').text(newval);
        });
    });

    // Site description
    wp.customize('blogdescription', function(value) {
        value.bind(function(newval) {
            $('.site-description').text(newval);
        });
    });

    // Hero title
    wp.customize('adprint_hero_title', function(value) {
        value.bind(function(newval) {
            $('.hero-title').text(newval);
        });
    });

    // Hero subtitle
    wp.customize('adprint_hero_subtitle', function(value) {
        value.bind(function(newval) {
            $('.hero-subtitle').text(newval);
        });
    });

    // Footer copyright
    wp.customize('adprint_footer_copyright', function(value) {
        value.bind(function(newval) {
            $('.footer-copyright').html(newval);
        });
    });

    // Header text color
    wp.customize('header_textcolor', function(value) {
        value.bind(function(newval) {
            if (newval === 'blank') {
                $('.site-title, .site-description').css({
                    'clip': 'rect(1px, 1px, 1px, 1px)',
                    'position': 'absolute'
                });
            } else {
                $('.site-title, .site-description').css({
                    'clip': 'auto',
                    'position': 'relative'
                });
                $('.site-title a, .site-description').css({
                    'color': '#' + newval
                });
            }
        });
    });

})(jQuery || window.jQuery);
