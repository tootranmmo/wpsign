<?php
/**
 * Quote Request Custom Post Type & Admin Interface
 *
 * @package AdPrint_Blog
 * @since 1.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register Quote Request Custom Post Type
 */
function adprint_register_quote_cpt() {
    $labels = array(
        'name'                  => _x('Báo giá', 'Post Type General Name', 'adprint-blog'),
        'singular_name'         => _x('Yêu cầu báo giá', 'Post Type Singular Name', 'adprint-blog'),
        'menu_name'             => __('Yêu cầu báo giá', 'adprint-blog'),
        'name_admin_bar'        => __('Báo giá', 'adprint-blog'),
        'archives'              => __('Danh sách báo giá', 'adprint-blog'),
        'attributes'            => __('Thuộc tính', 'adprint-blog'),
        'parent_item_colon'     => __('Báo giá cha:', 'adprint-blog'),
        'all_items'             => __('Tất cả báo giá', 'adprint-blog'),
        'add_new_item'          => __('Thêm báo giá mới', 'adprint-blog'),
        'add_new'               => __('Thêm mới', 'adprint-blog'),
        'new_item'              => __('Báo giá mới', 'adprint-blog'),
        'edit_item'             => __('Sửa báo giá', 'adprint-blog'),
        'update_item'           => __('Cập nhật báo giá', 'adprint-blog'),
        'view_item'             => __('Xem báo giá', 'adprint-blog'),
        'view_items'            => __('Xem báo giá', 'adprint-blog'),
        'search_items'          => __('Tìm kiếm', 'adprint-blog'),
        'not_found'             => __('Không tìm thấy', 'adprint-blog'),
        'not_found_in_trash'    => __('Không có trong thùng rác', 'adprint-blog'),
    );

    $args = array(
        'label'                 => __('Yêu cầu báo giá', 'adprint-blog'),
        'description'           => __('Quản lý yêu cầu báo giá từ khách hàng', 'adprint-blog'),
        'labels'                => $labels,
        'supports'              => array('title', 'editor', 'custom-fields'),
        'hierarchical'          => false,
        'public'                => false,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 25,
        'menu_icon'             => 'dashicons-media-document',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => false,
        'can_export'            => true,
        'has_archive'           => false,
        'exclude_from_search'   => true,
        'publicly_queryable'    => false,
        'capability_type'       => 'post',
        'show_in_rest'          => false,
    );

    register_post_type('quote_request', $args);
}
add_action('init', 'adprint_register_quote_cpt', 0);

/**
 * Add custom columns to Quote Request list
 */
function adprint_quote_columns($columns) {
    $new_columns = array(
        'cb' => $columns['cb'],
        'title' => __('Tên khách hàng', 'adprint-blog'),
        'product' => __('Sản phẩm', 'adprint-blog'),
        'quantity' => __('Số lượng', 'adprint-blog'),
        'contact' => __('Liên hệ', 'adprint-blog'),
        'status' => __('Trạng thái', 'adprint-blog'),
        'date' => __('Ngày gửi', 'adprint-blog'),
    );
    return $new_columns;
}
add_filter('manage_quote_request_posts_columns', 'adprint_quote_columns');

/**
 * Populate custom columns
 */
function adprint_quote_column_content($column, $post_id) {
    switch ($column) {
        case 'product':
            $product = get_post_meta($post_id, '_quote_product_type', true);
            echo esc_html($product ?: '—');
            break;

        case 'quantity':
            $quantity = get_post_meta($post_id, '_quote_quantity', true);
            echo esc_html($quantity ? number_format($quantity) : '—');
            break;

        case 'contact':
            $email = get_post_meta($post_id, '_quote_contact_email', true);
            $phone = get_post_meta($post_id, '_quote_contact_phone', true);
            if ($email) {
                echo '<a href="mailto:' . esc_attr($email) . '">' . esc_html($email) . '</a><br>';
            }
            if ($phone) {
                echo '<a href="tel:' . esc_attr($phone) . '">' . esc_html($phone) . '</a>';
            }
            if (!$email && !$phone) {
                echo '—';
            }
            break;

        case 'status':
            $status = get_post_meta($post_id, '_quote_status', true);
            $status = $status ?: 'pending';

            $status_labels = array(
                'pending' => array('label' => 'Chờ xử lý', 'color' => 'orange'),
                'processing' => array('label' => 'Đang xử lý', 'color' => 'blue'),
                'quoted' => array('label' => 'Đã báo giá', 'color' => 'green'),
                'completed' => array('label' => 'Hoàn thành', 'color' => 'gray'),
                'cancelled' => array('label' => 'Đã hủy', 'color' => 'red'),
            );

            $label_data = $status_labels[$status] ?? $status_labels['pending'];
            echo '<span class="quote-status-badge" style="display: inline-block; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600; background-color: ' . $label_data['color'] . '; color: white;">';
            echo esc_html($label_data['label']);
            echo '</span>';
            break;
    }
}
add_action('manage_quote_request_posts_custom_column', 'adprint_quote_column_content', 10, 2);

/**
 * Make columns sortable
 */
function adprint_quote_sortable_columns($columns) {
    $columns['quantity'] = 'quantity';
    $columns['status'] = 'status';
    return $columns;
}
add_filter('manage_edit-quote_request_sortable_columns', 'adprint_quote_sortable_columns');

/**
 * Add meta boxes for Quote Request
 */
function adprint_quote_meta_boxes() {
    add_meta_box(
        'quote_details',
        __('Chi tiết yêu cầu', 'adprint-blog'),
        'adprint_quote_details_callback',
        'quote_request',
        'normal',
        'high'
    );

    add_meta_box(
        'quote_status_box',
        __('Trạng thái & Hành động', 'adprint-blog'),
        'adprint_quote_status_callback',
        'quote_request',
        'side',
        'high'
    );
}
add_action('add_meta_boxes', 'adprint_quote_meta_boxes');

/**
 * Quote details meta box callback
 */
function adprint_quote_details_callback($post) {
    wp_nonce_field('adprint_quote_meta_box', 'adprint_quote_meta_box_nonce');

    $product_type = get_post_meta($post->ID, '_quote_product_type', true);
    $quantity = get_post_meta($post->ID, '_quote_quantity', true);
    $paper_type = get_post_meta($post->ID, '_quote_paper_type', true);
    $print_size = get_post_meta($post->ID, '_quote_print_size', true);
    $color_type = get_post_meta($post->ID, '_quote_color_type', true);
    $finishing = get_post_meta($post->ID, '_quote_finishing', true);
    $urgency = get_post_meta($post->ID, '_quote_urgency', true);

    $contact_name = get_post_meta($post->ID, '_quote_contact_name', true);
    $contact_email = get_post_meta($post->ID, '_quote_contact_email', true);
    $contact_phone = get_post_meta($post->ID, '_quote_contact_phone', true);
    $contact_company = get_post_meta($post->ID, '_quote_contact_company', true);

    $additional_notes = get_post_meta($post->ID, '_quote_additional_notes', true);
    $uploaded_file = get_post_meta($post->ID, '_quote_uploaded_file', true);
    ?>

    <style>
        .quote-info-table { width: 100%; border-collapse: collapse; }
        .quote-info-table th { text-align: left; padding: 12px; background: #f0f0f1; font-weight: 600; width: 200px; }
        .quote-info-table td { padding: 12px; border-bottom: 1px solid #ddd; }
        .quote-info-section { margin-bottom: 20px; }
        .quote-info-section h3 { margin-bottom: 10px; padding-bottom: 5px; border-bottom: 2px solid #2271b1; }
    </style>

    <div class="quote-info-section">
        <h3>📋 Thông tin sản phẩm</h3>
        <table class="quote-info-table">
            <tr>
                <th>Loại sản phẩm:</th>
                <td><strong><?php echo esc_html($product_type ?: '—'); ?></strong></td>
            </tr>
            <tr>
                <th>Số lượng:</th>
                <td><strong><?php echo esc_html($quantity ? number_format($quantity) : '—'); ?></strong></td>
            </tr>
            <tr>
                <th>Loại giấy:</th>
                <td><?php echo esc_html($paper_type ?: '—'); ?></td>
            </tr>
            <tr>
                <th>Kích thước:</th>
                <td><?php echo esc_html($print_size ?: '—'); ?></td>
            </tr>
            <tr>
                <th>Màu in:</th>
                <td><?php echo esc_html($color_type ?: '—'); ?></td>
            </tr>
            <tr>
                <th>Gia công:</th>
                <td><?php echo esc_html($finishing ?: '—'); ?></td>
            </tr>
            <tr>
                <th>Mức độ gấp:</th>
                <td><?php echo esc_html($urgency ?: '—'); ?></td>
            </tr>
        </table>
    </div>

    <div class="quote-info-section">
        <h3>👤 Thông tin liên hệ</h3>
        <table class="quote-info-table">
            <tr>
                <th>Họ tên:</th>
                <td><strong><?php echo esc_html($contact_name ?: '—'); ?></strong></td>
            </tr>
            <tr>
                <th>Email:</th>
                <td><a href="mailto:<?php echo esc_attr($contact_email); ?>"><?php echo esc_html($contact_email ?: '—'); ?></a></td>
            </tr>
            <tr>
                <th>Số điện thoại:</th>
                <td><a href="tel:<?php echo esc_attr($contact_phone); ?>"><?php echo esc_html($contact_phone ?: '—'); ?></a></td>
            </tr>
            <tr>
                <th>Công ty:</th>
                <td><?php echo esc_html($contact_company ?: '—'); ?></td>
            </tr>
        </table>
    </div>

    <?php if ($additional_notes): ?>
    <div class="quote-info-section">
        <h3>📝 Ghi chú thêm</h3>
        <div style="padding: 12px; background: #f9f9f9; border-left: 4px solid #2271b1;">
            <?php echo nl2br(esc_html($additional_notes)); ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($uploaded_file): ?>
    <div class="quote-info-section">
        <h3>📎 File đính kèm</h3>
        <p>
            <a href="<?php echo esc_url($uploaded_file); ?>" target="_blank" class="button button-secondary">
                <span class="dashicons dashicons-download" style="vertical-align: middle;"></span>
                Tải file xuống
            </a>
            <a href="<?php echo esc_url($uploaded_file); ?>" target="_blank" style="margin-left: 10px;">
                <?php echo esc_html(basename($uploaded_file)); ?>
            </a>
        </p>
    </div>
    <?php endif; ?>

    <?php
}

/**
 * Quote status meta box callback
 */
function adprint_quote_status_callback($post) {
    $status = get_post_meta($post->ID, '_quote_status', true);
    $status = $status ?: 'pending';
    ?>

    <div style="margin-bottom: 15px;">
        <label for="quote_status" style="font-weight: 600;">Trạng thái:</label>
        <select name="quote_status" id="quote_status" style="width: 100%; margin-top: 5px;">
            <option value="pending" <?php selected($status, 'pending'); ?>>⏳ Chờ xử lý</option>
            <option value="processing" <?php selected($status, 'processing'); ?>>🔄 Đang xử lý</option>
            <option value="quoted" <?php selected($status, 'quoted'); ?>>✅ Đã báo giá</option>
            <option value="completed" <?php selected($status, 'completed'); ?>>✔️ Hoàn thành</option>
            <option value="cancelled" <?php selected($status, 'cancelled'); ?>>❌ Đã hủy</option>
        </select>
    </div>

    <div style="border-top: 1px solid #ddd; padding-top: 15px;">
        <p><strong>Hành động nhanh:</strong></p>
        <p>
            <a href="mailto:<?php echo esc_attr(get_post_meta($post->ID, '_quote_contact_email', true)); ?>" class="button button-secondary" style="width: 100%; text-align: center; margin-bottom: 5px;">
                <span class="dashicons dashicons-email" style="vertical-align: middle;"></span>
                Gửi email
            </a>
        </p>
        <p>
            <a href="tel:<?php echo esc_attr(get_post_meta($post->ID, '_quote_contact_phone', true)); ?>" class="button button-secondary" style="width: 100%; text-align: center;">
                <span class="dashicons dashicons-phone" style="vertical-align: middle;"></span>
                Gọi điện
            </a>
        </p>
    </div>

    <div style="border-top: 1px solid #ddd; padding-top: 15px; margin-top: 15px;">
        <p style="font-size: 12px; color: #666;">
            <strong>Ngày gửi:</strong><br>
            <?php echo get_the_date('d/m/Y H:i', $post->ID); ?>
        </p>
    </div>
    <?php
}

/**
 * Save quote meta data
 */
function adprint_save_quote_meta($post_id) {
    // Check nonce
    if (!isset($_POST['adprint_quote_meta_box_nonce']) ||
        !wp_verify_nonce($_POST['adprint_quote_meta_box_nonce'], 'adprint_quote_meta_box')) {
        return;
    }

    // Check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Check permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Save status
    if (isset($_POST['quote_status'])) {
        update_post_meta($post_id, '_quote_status', sanitize_text_field($_POST['quote_status']));
    }
}
add_action('save_post_quote_request', 'adprint_save_quote_meta');

/**
 * Sync quote requests from database table to CPT
 * Run this once to migrate existing quotes
 */
function adprint_sync_quotes_from_db() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'quote_requests';

    // Check if table exists
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        return;
    }

    // Get all quotes from database
    $quotes = $wpdb->get_results("SELECT * FROM $table_name ORDER BY submitted_at DESC");

    foreach ($quotes as $quote) {
        // Check if already imported
        $existing = get_posts(array(
            'post_type' => 'quote_request',
            'meta_key' => '_quote_db_id',
            'meta_value' => $quote->id,
            'posts_per_page' => 1
        ));

        if (!empty($existing)) {
            continue; // Already imported
        }

        // Create new post
        $post_id = wp_insert_post(array(
            'post_title' => $quote->contact_name . ' - ' . $quote->product_type,
            'post_type' => 'quote_request',
            'post_status' => 'publish',
            'post_date' => $quote->submitted_at,
        ));

        if ($post_id) {
            // Save all meta data
            update_post_meta($post_id, '_quote_db_id', $quote->id);
            update_post_meta($post_id, '_quote_product_type', $quote->product_type);
            update_post_meta($post_id, '_quote_quantity', $quote->quantity);
            update_post_meta($post_id, '_quote_paper_type', $quote->paper_type);
            update_post_meta($post_id, '_quote_print_size', $quote->print_size);
            update_post_meta($post_id, '_quote_color_type', $quote->color_type);
            update_post_meta($post_id, '_quote_finishing', $quote->finishing);
            update_post_meta($post_id, '_quote_urgency', $quote->urgency);
            update_post_meta($post_id, '_quote_contact_name', $quote->contact_name);
            update_post_meta($post_id, '_quote_contact_email', $quote->contact_email);
            update_post_meta($post_id, '_quote_contact_phone', $quote->contact_phone);
            update_post_meta($post_id, '_quote_contact_company', $quote->contact_company);
            update_post_meta($post_id, '_quote_additional_notes', $quote->additional_notes);
            update_post_meta($post_id, '_quote_uploaded_file', $quote->uploaded_file);
            update_post_meta($post_id, '_quote_status', $quote->status);
        }
    }
}

// You can call this function manually or via WP-CLI to migrate data
// adprint_sync_quotes_from_db();

/**
 * Modify AJAX handler to also save to CPT
 */
function adprint_save_quote_to_cpt($quote_data) {
    $post_id = wp_insert_post(array(
        'post_title' => $quote_data['contact_name'] . ' - ' . $quote_data['product_type'],
        'post_type' => 'quote_request',
        'post_status' => 'publish',
    ));

    if ($post_id) {
        foreach ($quote_data as $key => $value) {
            update_post_meta($post_id, '_quote_' . $key, $value);
        }
    }

    return $post_id;
}
