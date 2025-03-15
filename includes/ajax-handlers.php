<?php
if (!defined('ABSPATH')) {
    exit;
}

// Register AJAX handler for logged-in users
add_action('wp_ajax_bms_add_post', 'bms_handle_add_post');

function bms_handle_add_post() {
    if (!is_user_logged_in()) {
        wp_send_json_error('You must be logged in.');
    }

    $title    = isset($_POST['title']) ? sanitize_text_field($_POST['title']) : '';
    $author   = isset($_POST['author']) ? sanitize_text_field($_POST['author']) : '';
    $content  = isset($_POST['content']) ? wp_kses_post($_POST['content']) : '';
    $category = isset($_POST['category']) ? intval($_POST['category']) : 0;

    if (empty($title) || empty($content) || empty($category)) {
        wp_send_json_error('Please fill out all required fields.');
    }

    $post_data = array(
        'post_title'    => $title,
        'post_content'  => $content,
        'post_status'   => 'pending',
        'post_author'   => get_current_user_id(),
        'post_type'     => 'bms_blog',
        'tax_input'     => array(
            'blog_category' => array($category),
        ),
    );

    $post_id = wp_insert_post($post_data);

    if (is_wp_error($post_id)) {
        wp_send_json_error('Failed to create post.');
    }

    // Handle featured image upload
    if (!empty($_FILES['featured_image']['name'])) {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');

        $attachment_id = media_handle_upload('featured_image', $post_id);

        if (is_wp_error($attachment_id)) {
            wp_send_json_error('Image upload failed.');
        }

        set_post_thumbnail($post_id, $attachment_id);
    }

    wp_send_json_success(['post_id' => $post_id]);
}
