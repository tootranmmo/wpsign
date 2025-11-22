<?php
/**
 * AJAX Handlers for Tools
 *
 * @package AdPrint_Blog
 * @since 1.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Handle quote request form submission
 */
function adprint_submit_quote_request() {
    // Verify nonce
    check_ajax_referer('adprint-ajax-nonce', 'nonce');

    // Sanitize and validate inputs
    $product_type = sanitize_text_field($_POST['quote-product-type'] ?? '');
    $custom_product = sanitize_text_field($_POST['custom-product'] ?? '');
    $quantity = intval($_POST['quote-quantity'] ?? 0);
    $paper_type = sanitize_text_field($_POST['quote-paper-type'] ?? '');
    $print_size = sanitize_text_field($_POST['quote-size'] ?? '');
    $color_type = sanitize_text_field($_POST['quote-color'] ?? '');
    $finishing = sanitize_text_field($_POST['quote-finishing'] ?? '');
    $urgency = sanitize_text_field($_POST['quote-urgency'] ?? '');
    $additional_notes = sanitize_textarea_field($_POST['additional-notes'] ?? '');

    // Contact info
    $contact_name = sanitize_text_field($_POST['contact-name'] ?? '');
    $contact_email = sanitize_email($_POST['contact-email'] ?? '');
    $contact_phone = sanitize_text_field($_POST['contact-phone'] ?? '');
    $contact_company = sanitize_text_field($_POST['contact-company'] ?? '');

    // Validate required fields
    if (empty($product_type) || empty($quantity) || empty($contact_name) || empty($contact_email)) {
        wp_send_json_error([
            'message' => 'Vui lòng điền đầy đủ thông tin bắt buộc.'
        ]);
    }

    // Validate email
    if (!is_email($contact_email)) {
        wp_send_json_error([
            'message' => 'Email không hợp lệ.'
        ]);
    }

    // Handle file upload
    $uploaded_file = '';
    if (!empty($_FILES['quote-file']['name'])) {
        require_once(ABSPATH . 'wp-admin/includes/file.php');

        $upload = wp_handle_upload($_FILES['quote-file'], [
            'test_form' => false,
            'mimes' => [
                'pdf' => 'application/pdf',
                'ai' => 'application/postscript',
                'eps' => 'application/postscript',
                'psd' => 'image/vnd.adobe.photoshop',
                'jpg|jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'tiff|tif' => 'image/tiff'
            ]
        ]);

        if (!empty($upload['file'])) {
            $uploaded_file = $upload['url'];
        }
    }

    // Prepare email content
    $admin_email = get_option('admin_email');
    $site_name = get_bloginfo('name');

    $subject = sprintf('[%s] Yêu cầu báo giá mới từ %s', $site_name, $contact_name);

    $message = "Yêu cầu báo giá mới từ website:\n\n";
    $message .= "=== THÔNG TIN LIÊN HỆ ===\n";
    $message .= "Họ tên: {$contact_name}\n";
    $message .= "Email: {$contact_email}\n";
    $message .= "Số điện thoại: {$contact_phone}\n";
    $message .= "Công ty: {$contact_company}\n\n";

    $message .= "=== CHI TIẾT SẢN PHẨM ===\n";
    $message .= "Loại sản phẩm: " . ($product_type === 'custom' ? $custom_product : $product_type) . "\n";
    $message .= "Số lượng: " . number_format($quantity) . "\n";
    $message .= "Loại giấy: {$paper_type}\n";
    $message .= "Kích thước: {$print_size}\n";
    $message .= "Màu in: {$color_type}\n";
    $message .= "Gia công: {$finishing}\n";
    $message .= "Mức độ gấp: {$urgency}\n\n";

    if (!empty($additional_notes)) {
        $message .= "=== GHI CHÚ THÊM ===\n";
        $message .= $additional_notes . "\n\n";
    }

    if (!empty($uploaded_file)) {
        $message .= "=== FILE ĐÍNH KÈM ===\n";
        $message .= "File: {$uploaded_file}\n\n";
    }

    $message .= "---\n";
    $message .= "Thời gian: " . date_i18n('d/m/Y H:i:s') . "\n";

    // Send email to admin
    $headers = [
        'Content-Type: text/plain; charset=UTF-8',
        'From: ' . $site_name . ' <' . $admin_email . '>',
        'Reply-To: ' . $contact_name . ' <' . $contact_email . '>'
    ];

    $email_sent = wp_mail($admin_email, $subject, $message, $headers);

    // Send confirmation email to customer
    $customer_subject = sprintf('[%s] Xác nhận yêu cầu báo giá', $site_name);
    $customer_message = "Xin chào {$contact_name},\n\n";
    $customer_message .= "Cảm ơn bạn đã gửi yêu cầu báo giá đến {$site_name}.\n\n";
    $customer_message .= "Chúng tôi đã nhận được yêu cầu của bạn và sẽ liên hệ lại trong vòng 24 giờ.\n\n";
    $customer_message .= "Thông tin yêu cầu của bạn:\n";
    $customer_message .= "- Sản phẩm: " . ($product_type === 'custom' ? $custom_product : $product_type) . "\n";
    $customer_message .= "- Số lượng: " . number_format($quantity) . "\n\n";
    $customer_message .= "Nếu có bất kỳ thắc mắc nào, vui lòng liên hệ chúng tôi qua:\n";
    $customer_message .= "Email: {$admin_email}\n\n";
    $customer_message .= "Trân trọng,\n";
    $customer_message .= $site_name;

    wp_mail($contact_email, $customer_subject, $customer_message, $headers);

    // Save to database (optional - for quote management)
    global $wpdb;
    $table_name = $wpdb->prefix . 'quote_requests';

    // Create table if not exists
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        product_type varchar(100) NOT NULL,
        quantity int NOT NULL,
        paper_type varchar(50),
        print_size varchar(50),
        color_type varchar(50),
        finishing varchar(50),
        urgency varchar(50),
        contact_name varchar(255) NOT NULL,
        contact_email varchar(255) NOT NULL,
        contact_phone varchar(50),
        contact_company varchar(255),
        additional_notes text,
        uploaded_file varchar(500),
        submitted_at datetime DEFAULT CURRENT_TIMESTAMP,
        status varchar(50) DEFAULT 'pending',
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    // Insert quote request
    $wpdb->insert(
        $table_name,
        [
            'product_type' => $product_type === 'custom' ? $custom_product : $product_type,
            'quantity' => $quantity,
            'paper_type' => $paper_type,
            'print_size' => $print_size,
            'color_type' => $color_type,
            'finishing' => $finishing,
            'urgency' => $urgency,
            'contact_name' => $contact_name,
            'contact_email' => $contact_email,
            'contact_phone' => $contact_phone,
            'contact_company' => $contact_company,
            'additional_notes' => $additional_notes,
            'uploaded_file' => $uploaded_file,
            'status' => 'pending'
        ],
        [
            '%s', '%d', '%s', '%s', '%s', '%s', '%s',
            '%s', '%s', '%s', '%s', '%s', '%s', '%s'
        ]
    );

    if ($email_sent) {
        wp_send_json_success([
            'message' => 'Yêu cầu báo giá đã được gửi thành công!'
        ]);
    } else {
        wp_send_json_error([
            'message' => 'Đã lưu yêu cầu nhưng không thể gửi email. Chúng tôi sẽ liên hệ sớm nhất.'
        ]);
    }
}
add_action('wp_ajax_submit_quote_request', 'adprint_submit_quote_request');
add_action('wp_ajax_nopriv_submit_quote_request', 'adprint_submit_quote_request');
