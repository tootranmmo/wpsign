<?php
/**
 * Tools Widget - Display tool shortcuts in sidebar
 *
 * @package AdPrint_Blog
 * @since 1.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * AdPrint Tools Widget Class
 */
class AdPrint_Tools_Widget extends WP_Widget {

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct(
            'adprint_tools_widget',
            __('Công cụ In ấn & Quảng cáo', 'adprint-blog'),
            array(
                'description' => __('Hiển thị shortcuts đến các công cụ tính toán in ấn và quảng cáo', 'adprint-blog'),
                'classname' => 'adprint-tools-widget'
            )
        );
    }

    /**
     * Front-end display of widget
     */
    public function widget($args, $instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Công cụ hữu ích', 'adprint-blog');
        $title = apply_filters('widget_title', $title, $instance, $this->id_base);

        $show_icons = !empty($instance['show_icons']) ? $instance['show_icons'] : 'yes';
        $display_style = !empty($instance['display_style']) ? $instance['display_style'] : 'list';

        echo $args['before_widget'];

        if ($title) {
            echo $args['before_title'] . $title . $args['after_title'];
        }

        // Tools data
        $tools = array(
            array(
                'name' => 'Tính giá in ấn',
                'icon' => '💰',
                'url' => home_url('/cong-cu/#price-calc'),
                'description' => 'Tính chi phí in ấn nhanh chóng'
            ),
            array(
                'name' => 'Chuyển đổi màu',
                'icon' => '🎨',
                'url' => home_url('/cong-cu/#color-conv'),
                'description' => 'RGB, CMYK, HEX converter'
            ),
            array(
                'name' => 'Tính ROI quảng cáo',
                'icon' => '📊',
                'url' => home_url('/cong-cu/#roi-calc'),
                'description' => 'Đo lường hiệu quả chiến dịch'
            ),
            array(
                'name' => 'Tính kích thước',
                'icon' => '📐',
                'url' => home_url('/cong-cu/#size-calc'),
                'description' => 'Bleed & Safe area calculator'
            ),
            array(
                'name' => 'Tính giấy',
                'icon' => '📄',
                'url' => home_url('/cong-cu/#paper-calc'),
                'description' => 'Trọng lượng & độ dày giấy'
            ),
            array(
                'name' => 'Kiểm tra file',
                'icon' => '✓',
                'url' => home_url('/cong-cu/#file-check'),
                'description' => 'Validate file in ấn'
            ),
            array(
                'name' => 'Yêu cầu báo giá',
                'icon' => '📝',
                'url' => home_url('/cong-cu/#quote-form'),
                'description' => 'Gửi yêu cầu báo giá'
            ),
        );

        if ($display_style === 'grid') {
            // Grid display
            echo '<div class="adprint-tools-grid grid grid-cols-2 gap-3">';
            foreach ($tools as $tool) {
                echo '<a href="' . esc_url($tool['url']) . '" class="block p-3 bg-gray-50 dark:bg-gray-700 hover:bg-primary-50 dark:hover:bg-primary-900 rounded-lg transition-colors text-center">';
                if ($show_icons === 'yes') {
                    echo '<span class="text-2xl mb-1 block">' . $tool['icon'] . '</span>';
                }
                echo '<span class="text-sm font-semibold block">' . esc_html($tool['name']) . '</span>';
                echo '</a>';
            }
            echo '</div>';
        } else {
            // List display
            echo '<ul class="adprint-tools-list space-y-2">';
            foreach ($tools as $tool) {
                echo '<li>';
                echo '<a href="' . esc_url($tool['url']) . '" class="flex items-start p-3 bg-gray-50 dark:bg-gray-700 hover:bg-primary-50 dark:hover:bg-primary-900 rounded-lg transition-colors group">';
                if ($show_icons === 'yes') {
                    echo '<span class="text-xl mr-3 group-hover:scale-110 transition-transform">' . $tool['icon'] . '</span>';
                }
                echo '<div class="flex-1">';
                echo '<span class="font-semibold block text-gray-900 dark:text-gray-100">' . esc_html($tool['name']) . '</span>';
                echo '<span class="text-xs text-gray-600 dark:text-gray-400">' . esc_html($tool['description']) . '</span>';
                echo '</div>';
                echo '</a>';
                echo '</li>';
            }
            echo '</ul>';
        }

        // Call to action
        $show_cta = !empty($instance['show_cta']) ? $instance['show_cta'] : 'yes';
        if ($show_cta === 'yes') {
            echo '<div class="mt-4">';
            echo '<a href="' . home_url('/cong-cu/') . '" class="block text-center px-4 py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-semibold transition-colors">';
            echo 'Xem tất cả công cụ →';
            echo '</a>';
            echo '</div>';
        }

        echo $args['after_widget'];
    }

    /**
     * Back-end widget form
     */
    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Công cụ hữu ích', 'adprint-blog');
        $show_icons = !empty($instance['show_icons']) ? $instance['show_icons'] : 'yes';
        $display_style = !empty($instance['display_style']) ? $instance['display_style'] : 'list';
        $show_cta = !empty($instance['show_cta']) ? $instance['show_cta'] : 'yes';
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">
                <?php _e('Tiêu đề:', 'adprint-blog'); ?>
            </label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text"
                   value="<?php echo esc_attr($title); ?>">
        </p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('display_style')); ?>">
                <?php _e('Kiểu hiển thị:', 'adprint-blog'); ?>
            </label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('display_style')); ?>"
                    name="<?php echo esc_attr($this->get_field_name('display_style')); ?>">
                <option value="list" <?php selected($display_style, 'list'); ?>>Danh sách (List)</option>
                <option value="grid" <?php selected($display_style, 'grid'); ?>>Lưới (Grid)</option>
            </select>
        </p>

        <p>
            <input class="checkbox" type="checkbox"
                   id="<?php echo esc_attr($this->get_field_id('show_icons')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('show_icons')); ?>"
                   value="yes" <?php checked($show_icons, 'yes'); ?>>
            <label for="<?php echo esc_attr($this->get_field_id('show_icons')); ?>">
                <?php _e('Hiển thị icon', 'adprint-blog'); ?>
            </label>
        </p>

        <p>
            <input class="checkbox" type="checkbox"
                   id="<?php echo esc_attr($this->get_field_id('show_cta')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('show_cta')); ?>"
                   value="yes" <?php checked($show_cta, 'yes'); ?>>
            <label for="<?php echo esc_attr($this->get_field_id('show_cta')); ?>">
                <?php _e('Hiển thị nút "Xem tất cả"', 'adprint-blog'); ?>
            </label>
        </p>
        <?php
    }

    /**
     * Sanitize widget form values
     */
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = !empty($new_instance['title']) ? sanitize_text_field($new_instance['title']) : '';
        $instance['show_icons'] = !empty($new_instance['show_icons']) ? 'yes' : 'no';
        $instance['display_style'] = !empty($new_instance['display_style']) ? sanitize_text_field($new_instance['display_style']) : 'list';
        $instance['show_cta'] = !empty($new_instance['show_cta']) ? 'yes' : 'no';
        return $instance;
    }
}

/**
 * Register AdPrint Tools Widget
 */
function adprint_register_tools_widget() {
    register_widget('AdPrint_Tools_Widget');
}
add_action('widgets_init', 'adprint_register_tools_widget');
