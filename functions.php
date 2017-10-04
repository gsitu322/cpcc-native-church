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
    function imic_get_data_by_path($id, $imic_custom_read_more) {
        $slug_data = get_post($id);
        $post_type = get_post_type($id);
        $slug_thumbnail_id = get_post_meta($id, '_thumbnail_id', 'true');
        $src = wp_get_attachment_image_src($slug_thumbnail_id, 'full');
        $read_More_text = !empty($imic_custom_read_more) ? $imic_custom_read_more : $slug_data->post_title;
        if (!empty($slug_thumbnail_id)) {
            echo '<div class="col-md-3 col-sm-3 featured-block">';
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