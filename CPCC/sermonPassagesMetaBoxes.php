<?php

/* Fire our meta box setup function on the post editor screen. */
add_action( 'load-post.php', 'cpcc_post_meta_boxes_setup' );
add_action( 'load-post-new.php', 'cpcc_post_meta_boxes_setup' );

/* Meta box setup function. */
function cpcc_post_meta_boxes_setup() {

    /* Add meta boxes on the 'add_meta_boxes' hook. */
    add_action( 'add_meta_boxes', 'cpcc_add_post_meta_boxes' );

    /* Add meta boxes on the 'add_meta_boxes' hook. */
    add_action( 'add_meta_boxes', 'cpcc_add_post_meta_boxes' );

    /* Save post meta on the 'save_post' hook. */
    add_action( 'save_post', 'cpcc_save_post_book_meta', 10, 2 );
}

/* Create one or more meta boxes to be displayed on the post editor screen. */
function cpcc_add_post_meta_boxes() {

    add_meta_box(
        'cpcc-sermon-passages',                     // Unique ID
        esc_html__( 'Bible Passage', 'example' ),   // Title
        'cpcc_post_book_meta_box',                 // Callback function
        'sermons',                                  // Admin page (or post type)
        'normal',                                   // Context
        'default'                                   // Priority
    );
}

/* Display the post meta box. */
function cpcc_post_book_meta_box( $post ) { ?>
    <?php $bible = require_once(CHILD_THEME_DIRECTORY . '/CPCC/bibleBooks.php'); ?>
    <?php wp_nonce_field( basename( __FILE__ ), 'cpcc_post_book_nonce' ); ?>
    <p>
        <label for="cpcc-post-book"><?php _e( 'Book', 'Book' ); ?></label>
        <br />
        <select id="cpcc-post-book" name="cpcc_post_book">
            <?php foreach ($bible as $book): ?>
                <option <?= esc_attr(get_post_meta($post->ID, 'cpcc_post_book', true)) === $book ? 'selected="selected"' : ''; ?>>
                    <?= $book ?>
                </option>
            <?php endforeach; ?>
        </select>
    </p>
<?php }


/* Save the meta box's post metadata. */
function cpcc_save_post_book_meta($post_id, $post)
{
    /* Verify the nonce before proceeding. */
    if (! isset($_POST['cpcc_post_book_nonce']) || ! wp_verify_nonce($_POST['cpcc_post_book_nonce'], basename(__FILE__)))
        return $post_id;

    /* Get the post type object. */
    $post_type = get_post_type_object($post->post_type);

    /* Check if the current user has permission to edit the post. */
    if (! current_user_can($post_type->cap->edit_post, $post_id))
        return $post_id;

    /* Get the posted data and sanitize it for use as an HTML class. */
    $new_meta_value = (isset($_POST['cpcc_post_book']) ? sanitize_html_class($_POST['cpcc_post_book']) : '');

    /* Get the meta key. */
    $meta_key = 'cpcc_post_book';

    /* Get the meta value of the custom field key. */
    $meta_value = get_post_meta($post_id, $meta_key, true);

    /* If a new meta value was added and there was no previous value, add it. */
    if ($new_meta_value && '' == $meta_value)
        add_post_meta($post_id, $meta_key, $new_meta_value, true);

    /* If the new meta value does not match the old value, update it. */
    elseif ($new_meta_value && $new_meta_value != $meta_value)
        update_post_meta($post_id, $meta_key, $new_meta_value);

    /* If there is no new meta value but an old value exists, delete it. */
    elseif ('' == $new_meta_value && $meta_value)
        delete_post_meta($post_id, $meta_key, $meta_value);
}