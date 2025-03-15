<?php
if (!defined('ABSPATH')) {
    exit;
}

class BMS_REST_API {

    public function __construct() {
        add_action('rest_api_init', [$this, 'register_rest_routes']);
    }

    public function register_rest_routes() {
        // GET all posts (public)
        register_rest_route('blog-management/v1', '/posts', [
            'methods'  => 'GET',
            'callback' => [$this, 'get_blog_posts'],
        ]);

        // GET post by ID (public)
        register_rest_route('blog-management/v1', '/posts/(?P<id>\d+)', [
            'methods'  => 'GET',
            'callback' => [$this, 'get_post_by_id'],
        ]);

        // POST create post (JWT token required)
        register_rest_route('blog-management/v1', '/posts', [
            'methods'  => 'POST',
            'callback' => [$this, 'create_post'],
            'permission_callback' => [$this, 'jwt_auth_check'],
        ]);

        // PUT update post (JWT token required)
        register_rest_route('blog-management/v1', '/posts/(?P<id>\d+)', [
            'methods'             => 'PUT',
            'callback'            => [$this, 'update_post'],
            'permission_callback' => [$this, 'jwt_auth_check'],
        ]);

        // DELETE post (JWT token required)
        register_rest_route('blog-management/v1', '/posts/(?P<id>\d+)', [
            'methods'             => 'DELETE',
            'callback'            => [$this, 'delete_post'],
            'permission_callback' => [$this, 'jwt_auth_check'],
        ]);
    }

    /**
     * Check JWT token authentication.
     */
    public function jwt_auth_check() {
        $user = wp_get_current_user();
    
        if ($user && $user->exists() && current_user_can('edit_posts')) {
            return true;
        }
    
        return new WP_Error(
            'rest_forbidden',
            __('You do not have permissions to access this endpoint.', 'text-domain'),
            array('status' => 403)
        );
    }
    

    // GET all posts
    public function get_blog_posts() {
        $posts = get_posts(['post_type' => 'bms_blog', 'numberposts' => -1]);
        $response = [];

        foreach ($posts as $post) {
            $response[] = [
                'id'             => $post->ID,
                'title'          => get_the_title($post->ID),
                'content'        => apply_filters('the_content', $post->post_content),
                'author'         => get_post_meta($post->ID, 'author_name', true),
                'category'       => $this->get_post_category($post->ID),
                'featured_image' => get_the_post_thumbnail_url($post->ID, 'full'),
                'created_at'     => $post->post_date,
            ];
        }

        return rest_ensure_response($response);
    }

    // GET post by ID
    public function get_post_by_id($request) {
        $post_id = (int) $request['id'];
        $post = get_post($post_id);

        if (empty($post) || $post->post_type !== 'bms_blog') {
            return new WP_Error('no_post', 'Post not found', array('status' => 404));
        }

        return array(
            'id'      => $post->ID,
            'title'   => $post->post_title,
            'content' => $post->post_content,
            'author'  => get_the_author_meta('display_name', $post->post_author),
            'date'    => $post->post_date,
        );
    }

    // CREATE post (JWT protected)
public function create_post(WP_REST_Request $request) {

    if ( ! function_exists( 'download_url' ) ) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
    }

    if ( ! function_exists( 'media_handle_sideload' ) ) {
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';
    }
    
    // Get params and files
    $files  = $request->get_file_params();
    $params = $request->get_params();

    // Sanitize and insert post
    $post_data = [
        'post_title'   => sanitize_text_field($params['title']),
        'post_content' => sanitize_textarea_field($params['content']),
        'post_status'  => 'draft',
        'post_type'    => 'bms_blog',
    ];

    $post_id = wp_insert_post($post_data);

    if (is_wp_error($post_id)) {
        return new WP_Error('post_creation_failed', 'Failed to create post', ['status' => 500]);
    }

    // Update custom author meta
    if (!empty($params['author'])) {
        update_post_meta($post_id, 'author_name', sanitize_text_field($params['author']));
    }

    // Assign blog category
    if (!empty($params['category'])) {
        wp_set_post_terms($post_id, [(int)$params['category']], 'blog_category');
    }

    // Handle featured image file upload
    if (!empty($files['featured_image'])) {
        $attachment_id = $this->handle_image_upload($files['featured_image'], $post_id);

        if (is_wp_error($attachment_id)) {
            return new WP_Error('image_upload_failed', 'Image upload failed', ['status' => 500]);
        }

        set_post_thumbnail($post_id, $attachment_id);
    }

    // Handle featured image via URL
    if (!empty($params['featured_image_url'])) {
        $image_url = esc_url_raw($params['featured_image_url']);

        // Download the image temporarily
        $tmp = download_url($image_url);

        if (is_wp_error($tmp)) {
            return new WP_Error('image_download_failed', 'Failed to download image from URL', ['status' => 500]);
        }

        // Prepare the sideload array
        $desc = basename($image_url);
        $file_array = [
            'name'     => $desc,
            'tmp_name' => $tmp,
        ];

        // Sideload into media library
        $attachment_id = media_handle_sideload($file_array, $post_id);

        // Clean up temp file
        @unlink($tmp);

        if (is_wp_error($attachment_id)) {
            return new WP_Error('image_upload_failed', 'Failed to upload image from URL', ['status' => 500]);
        }

        set_post_thumbnail($post_id, $attachment_id);
    }

    return new WP_REST_Response([
        'message' => 'Post created successfully!',
        'post_id' => $post_id
    ], 200);
}


    public function update_post(WP_REST_Request $request) {

        if ( ! function_exists( 'download_url' ) ) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
        }

        if ( ! function_exists( 'media_handle_sideload' ) ) {
            require_once ABSPATH . 'wp-admin/includes/media.php';
            require_once ABSPATH . 'wp-admin/includes/image.php';
        }
        $post_id = (int) $request['id'];
    
        // Validate post
        $post = get_post($post_id);
        if (!$post || $post->post_type !== 'bms_blog') {
            return new WP_Error('no_post', 'Post not found', ['status' => 404]);
        }
    
        // Grab form data & files
        $params = $request->get_params();
        $files  = $request->get_file_params();
    
        // Update post title & content
        $update_data = [];
        if (!empty($params['title'])) {
            $update_data['post_title'] = sanitize_text_field($params['title']);
        }
        if (!empty($params['content'])) {
            $update_data['post_content'] = sanitize_textarea_field($params['content']);
        }
    
        if (!empty($update_data)) {
            $update_data['ID'] = $post_id;
            $updated_post = wp_update_post($update_data, true);
    
            if (is_wp_error($updated_post)) {
                return new WP_Error('cant_update', 'Failed to update post', ['status' => 500]);
            }
        }
    
        // Update author_name (meta field)
        if (!empty($params['author'])) {
            update_post_meta($post_id, 'author_name', sanitize_text_field($params['author']));
        }
    
        // Update category
        if (!empty($params['category'])) {
            wp_set_post_terms($post_id, [(int)$params['category']], 'blog_category');
        }
    
        // Handle featured_image via file upload
        if (!empty($files['featured_image'])) {
            $attachment_id = $this->handle_image_upload($files['featured_image'], $post_id);
    
            if (is_wp_error($attachment_id)) {
                return new WP_Error('image_upload_failed', 'Image upload failed', ['status' => 500]);
            }
    
            set_post_thumbnail($post_id, $attachment_id);
        }
    
        // Handle featured_image via URL upload
        if (!empty($params['featured_image_url'])) {
            $image_url = esc_url_raw($params['featured_image_url']);
    
            // Download the image and add it to the media library
            $tmp = download_url($image_url);
    
            if (is_wp_error($tmp)) {
                return new WP_Error('image_download_failed', 'Failed to download image from URL', ['status' => 500]);
            }
    
            // Get the file's name and type
            $desc = basename($image_url);
            $file_array = [
                'name'     => $desc,
                'tmp_name' => $tmp,
            ];
    
            // Do the sideload
            $attachment_id = media_handle_sideload($file_array, $post_id);
    
            // Clean up temporary file
            @unlink($tmp);
    
            if (is_wp_error($attachment_id)) {
                return new WP_Error('image_upload_failed', 'Failed to upload image from URL', ['status' => 500]);
            }
    
            set_post_thumbnail($post_id, $attachment_id);
        }
    
        return new WP_REST_Response([
            'message' => 'Post updated successfully!',
            'post_id' => $post_id
        ], 200);
    }
    
    // DELETE post (JWT protected)
    public function delete_post(WP_REST_Request $request) {
        $post_id = (int) $request['id'];

        $deleted = wp_delete_post($post_id, true);

        if (!$deleted) {
            return new WP_Error('cant_delete', 'Failed to delete post', ['status' => 500]);
        }

        return ['message' => 'Post deleted'];
    }

    private function handle_image_upload($file, $post_id = 0) {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');

        $overrides = ['test_form' => false];
        $file_return = wp_handle_upload($file, $overrides);

        if (isset($file_return['error']) || !isset($file_return['file'])) {
            return new WP_Error('upload_error', $file_return['error']);
        }

        $filename = $file_return['file'];
        $attachment = [
            'post_mime_type' => $file_return['type'],
            'post_title'     => sanitize_file_name($filename),
            'post_content'   => '',
            'post_status'    => 'inherit',
        ];

        $attachment_id = wp_insert_attachment($attachment, $filename, $post_id);
        if (is_wp_error($attachment_id)) {
            return $attachment_id;
        }

        $attach_data = wp_generate_attachment_metadata($attachment_id, $filename);
        wp_update_attachment_metadata($attachment_id, $attach_data);

        return $attachment_id;
    }

    private function get_post_category($post_id) {
        $terms = wp_get_post_terms($post_id, 'blog_category');
        return !empty($terms) ? $terms[0]->name : null;
    }
}
