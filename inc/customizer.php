<?php
/**
 * Theme Customizer
 *
 * @package AdPrint_Blog
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add postMessage support for site title and description
 */
function adprint_customize_register($wp_customize) {
    $wp_customize->get_setting('blogname')->transport = 'postMessage';
    $wp_customize->get_setting('blogdescription')->transport = 'postMessage';

    // Add Social Media Section
    $wp_customize->add_section('adprint_social_media', array(
        'title' => __('Social Media', 'adprint-blog'),
        'priority' => 30,
    ));

    // Facebook URL
    $wp_customize->add_setting('adprint_facebook_url', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));

    $wp_customize->add_control('adprint_facebook_url', array(
        'label' => __('Facebook URL', 'adprint-blog'),
        'section' => 'adprint_social_media',
        'type' => 'url',
    ));

    // Twitter URL
    $wp_customize->add_setting('adprint_twitter_url', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));

    $wp_customize->add_control('adprint_twitter_url', array(
        'label' => __('Twitter URL', 'adprint-blog'),
        'section' => 'adprint_social_media',
        'type' => 'url',
    ));

    // Twitter Username
    $wp_customize->add_setting('adprint_twitter_username', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('adprint_twitter_username', array(
        'label' => __('Twitter Username', 'adprint-blog'),
        'section' => 'adprint_social_media',
        'type' => 'text',
    ));

    // Instagram URL
    $wp_customize->add_setting('adprint_instagram_url', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));

    $wp_customize->add_control('adprint_instagram_url', array(
        'label' => __('Instagram URL', 'adprint-blog'),
        'section' => 'adprint_social_media',
        'type' => 'url',
    ));

    // LinkedIn URL
    $wp_customize->add_setting('adprint_linkedin_url', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));

    $wp_customize->add_control('adprint_linkedin_url', array(
        'label' => __('LinkedIn URL', 'adprint-blog'),
        'section' => 'adprint_social_media',
        'type' => 'url',
    ));

    // YouTube URL
    $wp_customize->add_setting('adprint_youtube_url', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));

    $wp_customize->add_control('adprint_youtube_url', array(
        'label' => __('YouTube URL', 'adprint-blog'),
        'section' => 'adprint_social_media',
        'type' => 'url',
    ));

    // Facebook App ID
    $wp_customize->add_setting('adprint_facebook_app_id', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('adprint_facebook_app_id', array(
        'label' => __('Facebook App ID', 'adprint-blog'),
        'description' => __('For Open Graph integration', 'adprint-blog'),
        'section' => 'adprint_social_media',
        'type' => 'text',
    ));

    // Homepage Settings Section
    $wp_customize->add_section('adprint_homepage', array(
        'title' => __('Homepage Settings', 'adprint-blog'),
        'priority' => 35,
    ));

    // Hero Title
    $wp_customize->add_setting('adprint_hero_title', array(
        'default' => __('Khám Phá Thế Giới Quảng Cáo & In Ấn', 'adprint-blog'),
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('adprint_hero_title', array(
        'label' => __('Hero Title', 'adprint-blog'),
        'section' => 'adprint_homepage',
        'type' => 'text',
    ));

    // Hero Subtitle
    $wp_customize->add_setting('adprint_hero_subtitle', array(
        'default' => __('Tin tức, xu hướng và kiến thức chuyên sâu về lĩnh vực quảng cáo và in ấn', 'adprint-blog'),
        'sanitize_callback' => 'sanitize_textarea_field',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('adprint_hero_subtitle', array(
        'label' => __('Hero Subtitle', 'adprint-blog'),
        'section' => 'adprint_homepage',
        'type' => 'textarea',
    ));

    // Number of posts to show
    $wp_customize->add_setting('adprint_posts_per_page', array(
        'default' => 6,
        'sanitize_callback' => 'absint',
    ));

    $wp_customize->add_control('adprint_posts_per_page', array(
        'label' => __('Number of posts to display', 'adprint-blog'),
        'section' => 'adprint_homepage',
        'type' => 'number',
        'input_attrs' => array(
            'min' => 3,
            'max' => 12,
            'step' => 1,
        ),
    ));

    // Footer Settings Section
    $wp_customize->add_section('adprint_footer', array(
        'title' => __('Footer Settings', 'adprint-blog'),
        'priority' => 40,
    ));

    // Footer Copyright Text
    $wp_customize->add_setting('adprint_footer_copyright', array(
        'default' => sprintf(__('© %d %s. All rights reserved.', 'adprint-blog'), date('Y'), get_bloginfo('name')),
        'sanitize_callback' => 'wp_kses_post',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('adprint_footer_copyright', array(
        'label' => __('Copyright Text', 'adprint-blog'),
        'section' => 'adprint_footer',
        'type' => 'textarea',
    ));
}
add_action('customize_register', 'adprint_customize_register');

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously
 */
function adprint_customize_preview_js() {
    wp_enqueue_script(
        'adprint-customizer',
        ADPRINT_THEME_URI . '/assets/js/customizer.js',
        array('customize-preview'),
        ADPRINT_VERSION,
        true
    );
}
add_action('customize_preview_init', 'adprint_customize_preview_js');
