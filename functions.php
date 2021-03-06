<?php
/**
 *
 * Native Church child theme functions and definitions
 * 
 * @package Native Church
 * @author  imithemes <www.imithemes.com>
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU Public License
 * 
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * When using a child theme you can override certain functions (those wrapped
 * in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before
 * the parent theme's file, so the child theme functions would be used.
 *
 * @link https://codex.wordpress.org/Theme_Development
 * @link https://codex.wordpress.org/Child_Themes
 *
 * Functions that are not pluggable (not wrapped in function_exists()) are
 * instead attached to a filter or action hook.
 *
 * For more information on hooks, actions, and filters,
 * {@link https://codex.wordpress.org/Plugin_API}
 */
function nativechurch_child_scripts() {
    wp_enqueue_style( 'nativechurch-parent-style', get_template_directory_uri(). '/style.css',array('imic_bootstrap') );
}
add_action( 'wp_enqueue_scripts', 'nativechurch_child_scripts' );

if (!function_exists('imic_get_data_by_path')) {
    function imic_get_data_by_path($id, $imic_custom_read_more, $numItemsPerRow = 3) {
        $slug_data = get_post($id);
        $post_type = get_post_type($id);
        $slug_thumbnail_id = get_post_meta($id, '_thumbnail_id', 'true');
        $src = wp_get_attachment_image_src($slug_thumbnail_id, 'full');
        $read_More_text = !empty($imic_custom_read_more) ? $imic_custom_read_more : $slug_data->post_title;
        if (!empty($slug_thumbnail_id)) {
            /** Force col-xs-6 for xs sizes to avoid adding extra config or doing calculations */
            echo '<div class="col-xs-6 col-sm-' . (12/$numItemsPerRow) . ' featured-block mBottom20">';
            if ($post_type == 'event') {
                $customeventSt = strtotime(get_post_meta($id, 'imic_event_start_dt', true));
                $date_converted = date('Y-m-d', $customeventSt);
              $custom_event_url= imic_query_arg($date_converted,$slug_data->ID);
                } else {
                $custom_event_url = get_permalink($slug_data->ID);
            }
            echo'<a href="' . $custom_event_url . '" class="img-thumbnail"> <img src="' . $src[0] . '" alt="' . $slug_data->post_title . '"> <strong>' . $read_More_text . '</strong> <span class="more">' . __('read more', 'framework') . '</span> </a> </div>';
        }
    }
}

/** Override Recent Sermon Widget */
add_action('after_setup_theme', 'remove_parent_file');
function remove_parent_file()
{
    add_action('widgets_init', 'unregister_recent_sermon_widget');
}

function unregister_recent_sermon_widget()
{
    unregister_widget('recent_sermons');
}
require_once(dirname(__FILE__) . '/imic-framework/widgets/child_recent_sermons.php');

function prefooter_sidebar()
{
    register_sidebar(array(
        'name' => 'PreFooter Sidebar',
        'id' => 'prefooter-sidebar',
        'before_widget' => '<div class="prefooter-sidebar">',
        'after_widget' => '</div>',
        'before_title' => '<h2 class="prefooter-sidebar">',
        'after_title' => '</h2>',
    ));
}

add_action('widgets_init', 'prefooter_sidebar');

/** Ministry Meta Boxes */
if (!function_exists('imic_register_ministry_meta_box')) {
    add_action('admin_init', 'imic_register_ministry_meta_box');
    function imic_register_ministry_meta_box() {
        // Check if plugin is activated or included in theme
        if (!class_exists('RW_Meta_Box'))
            return;
        $prefix = 'imic_';

        /** @var array $meta_box Ministry Meta Box*/
        $meta_box =  array(
            'id' => 'template-home-ministry',
            'title' => __('Featured Ministry Area', 'framework'),
            'pages' => array('page'),
            'show' => array(
                // With all conditions below, use this logical operator to combine them. Default is 'OR'. Case insensitive. Optional.
                'relation' => 'OR',
                // List of page templates (used for page only). Array. Optional.
                'template' => array( 'template-home.php' ),
            ),
            'show_names' => true,
            'fields' => array(
                array(
                    'name'      => __('Number of items per row', 'framework'),
                    'id'        => $prefix . 'imic_featured_block_num_items_per_row_ministry',
                    'desc'      => __('Number of items per row', 'framework'),
                    'type'      => 'select',
                    'options'   => [
                        '1' => __('1', 'framework'),
                        '2' => __('2', 'framework'),
                        '3' => __('3', 'framework'),
                        '4' => __('4', 'framework'),
                    ]
                ),
                array(
                    'name' => __('Switch for featured blocks', 'framework'),
                    'id' => $prefix . 'imic_featured_blocks_ministry',
                    'desc' => __("Select enable or disable to show/hide featured blocks.", 'framework'),
                    'type' => 'select',
                    'options' => array(
                        '1' => __('Enable', 'framework'),
                        '2' => __('Disable', 'framework'),
                    ),
                    'std' => '1',
                ),
                array(
                    'name' => __('Featured Blocks to show on home page', 'framework'),
                    'id' => $prefix . 'home_featured_blocks_ministry',
                    'desc' => __("Enter the Posts/Pages comma separated ID to show featured blocks on Home page. example - 1,2,3", 'framework'),
                    'type' => 'text',
                    'std' => ''
                ),
                array(
                    'name' => __('Title for featured blocks', 'framework'),
                    'id' => $prefix . 'home_row_featured_blocks_ministry',
                    'desc' => __("Enter the title for featured blocks. Add more as per the entered page IDs", 'framework'),
                    'type' => 'text',
                    'clone' => true,
                    'std' => ''
                ),
                array(
                    'name' => __('Title for first featured block', 'framework'),
                    'id' => $prefix . 'home_featured_blocks1_ministry',
                    'desc' => __("Enter the title for first featured block area", 'framework'),
                    'type' => 'hidden',
                    'std' => ''
                ),
                array(
                    'name' => __('Title for second featured block', 'framework'),
                    'id' => $prefix . 'home_featured_blocks2_ministry',
                    'desc' => __("Enter the title for second featured block area", 'framework'),
                    'type' => 'hidden',
                    'std' => ''
                ),
                array(
                    'name' => __('Title for third featured block', 'framework'),
                    'id' => $prefix .'home_featured_blocks3_ministry',
                    'desc' => __("Enter the title for third featured block area", 'framework'),
                    'type' => 'hidden',
                    'std' => ''
                ),
            ));

        new RW_Meta_Box($meta_box);
    }
}

/** SF Worship Meta Boxes */
if (!function_exists('imic_register_sf_worship_meta_box')) {
    add_action('admin_init', 'imic_register_sf_worship_meta_box');
    function imic_register_sf_worship_meta_box() {
        // Check if plugin is activated or included in theme
        if (!class_exists('RW_Meta_Box'))
            return;
        $prefix = 'imic_';

        $meta_box =  array(
            'id' => 'template-home-sf-campus',
            'title' => __('Featured SF Worships', 'framework'),
            'pages' => array('page'),
            'show' => array(
                // With all conditions below, use this logical operator to combine them. Default is 'OR'. Case insensitive. Optional.
                'relation' => 'OR',
                // List of page templates (used for page only). Array. Optional.
                'template' => array( 'template-home.php' ),
            ),
            'show_names' => true,
            'fields' => array(
                array(
                    'name'      => __('Number of items per row', 'framework'),
                    'id'        => $prefix . 'imic_featured_block_num_items_per_row_sf_campus',
                    'desc'      => __('Number of items per row', 'framework'),
                    'type'      => 'select',
                    'options'   => [
                        '1' => __('1', 'framework'),
                        '2' => __('2', 'framework'),
                        '3' => __('3', 'framework'),
                        '4' => __('4', 'framework'),
                    ]
                ),
                array(
                    'name' => __('Switch for featured blocks', 'framework'),
                    'id' => $prefix . 'imic_featured_blocks_sf_campus',
                    'desc' => __("Select enable or disable to show/hide featured blocks.", 'framework'),
                    'type' => 'select',
                    'options' => array(
                        '1' => __('Enable', 'framework'),
                        '2' => __('Disable', 'framework'),
                    ),
                    'std' => '1',
                ),
                array(
                    'name' => __('Featured Blocks to show on home page', 'framework'),
                    'id' => $prefix . 'home_featured_blocks_sf_campus',
                    'desc' => __("Enter the Posts/Pages comma separated ID to show featured blocks on Home page. example - 1,2,3", 'framework'),
                    'type' => 'text',
                    'std' => ''
                ),
                array(
                    'name' => __('Title for featured blocks', 'framework'),
                    'id' => $prefix . 'home_row_featured_blocks_sf_campus',
                    'desc' => __("Enter the title for featured blocks. Add more as per the entered page IDs", 'framework'),
                    'type' => 'text',
                    'clone' => true,
                    'std' => ''
                ),
                array(
                    'name' => __('Title for first featured block', 'framework'),
                    'id' => $prefix . 'home_featured_blocks1_sf_campus',
                    'desc' => __("Enter the title for first featured block area", 'framework'),
                    'type' => 'hidden',
                    'std' => ''
                ),
                array(
                    'name' => __('Title for second featured block', 'framework'),
                    'id' => $prefix . 'home_featured_blocks2_sf_campus',
                    'desc' => __("Enter the title for second featured block area", 'framework'),
                    'type' => 'hidden',
                    'std' => ''
                ),
                array(
                    'name' => __('Title for third featured block', 'framework'),
                    'id' => $prefix .'home_featured_blocks3_sf_campus',
                    'desc' => __("Enter the title for third featured block area", 'framework'),
                    'type' => 'hidden',
                    'std' => ''
                ),
            ));
        new RW_Meta_Box($meta_box);
    }
}

/** DC Worship Metaboxes */
if (!function_exists('imic_register_dc_worship_meta_box')) {
    add_action('admin_init', 'imic_register_dc_worship_meta_box');
    function imic_register_dc_worship_meta_box() {
        // Check if plugin is activated or included in theme
        if (!class_exists('RW_Meta_Box'))
            return;
        $prefix = 'imic_';

        $meta_box =  array(
            'id' => 'template-home-dc-campus',
            'title' => __('Featured Daly City Worships', 'framework'),
            'pages' => array('page'),
            'show' => array(
                // With all conditions below, use this logical operator to combine them. Default is 'OR'. Case insensitive. Optional.
                'relation' => 'OR',
                // List of page templates (used for page only). Array. Optional.
                'template' => array( 'template-home.php' ),
            ),
            'show_names' => true,
            'fields' => array(
                array(
                    'name'      => __('Number of items per row', 'framework'),
                    'id'        => $prefix . 'imic_featured_block_num_items_per_row_dc_campus',
                    'desc'      => __('Number of items per row', 'framework'),
                    'type'      => 'select',
                    'options'   => [
                        '1' => __('1', 'framework'),
                        '2' => __('2', 'framework'),
                        '3' => __('3', 'framework'),
                        '4' => __('4', 'framework'),
                    ]
                ),
                array(
                    'name' => __('Switch for featured blocks', 'framework'),
                    'id' => $prefix . 'imic_featured_blocks_dc_campus',
                    'desc' => __("Select enable or disable to show/hide featured blocks.", 'framework'),
                    'type' => 'select',
                    'options' => array(
                        '1' => __('Enable', 'framework'),
                        '2' => __('Disable', 'framework'),
                    ),
                    'std' => '1',
                ),
                array(
                    'name' => __('Featured Blocks to show on home page', 'framework'),
                    'id' => $prefix . 'home_featured_blocks_dc_campus',
                    'desc' => __("Enter the Posts/Pages comma separated ID to show featured blocks on Home page. example - 1,2,3", 'framework'),
                    'type' => 'text',
                    'std' => ''
                ),
                array(
                    'name' => __('Title for featured blocks', 'framework'),
                    'id' => $prefix . 'home_row_featured_blocks_dc_campus',
                    'desc' => __("Enter the title for featured blocks. Add more as per the entered page IDs", 'framework'),
                    'type' => 'text',
                    'clone' => true,
                    'std' => ''
                ),
                array(
                    'name' => __('Title for first featured block', 'framework'),
                    'id' => $prefix . 'home_featured_blocks1_dc_campus',
                    'desc' => __("Enter the title for first featured block area", 'framework'),
                    'type' => 'hidden',
                    'std' => ''
                ),
                array(
                    'name' => __('Title for second featured block', 'framework'),
                    'id' => $prefix . 'home_featured_blocks2_dc_campus',
                    'desc' => __("Enter the title for second featured block area", 'framework'),
                    'type' => 'hidden',
                    'std' => ''
                ),
                array(
                    'name' => __('Title for third featured block', 'framework'),
                    'id' => $prefix .'home_featured_blocks3_dc_campus',
                    'desc' => __("Enter the title for third featured block area", 'framework'),
                    'type' => 'hidden',
                    'std' => ''
                ),
            ));
        new RW_Meta_Box($meta_box);
    }
}