<?php
/**
 * The sidebar containing the main widget area
 *
 * @package AdPrint_Blog
 * @since 1.0.0
 */

if (!is_active_sidebar('sidebar-1')) {
    return;
}
?>

<aside id="secondary" class="widget-area lg:w-80 lg:ml-8 mt-12 lg:mt-0" role="complementary">
    <?php dynamic_sidebar('sidebar-1'); ?>
</aside>
