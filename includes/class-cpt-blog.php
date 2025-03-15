<?php
if (!defined('ABSPATH')) {
    exit;
}

class BMS_CPT_Blog {

    public function __construct() {
        // Register CPT and Taxonomies
        add_action('init', [$this, 'register_blog_post_type']);
        add_action('init', [$this, 'register_blog_category_taxonomy']);

        // Custom fields (Meta box)
        add_action('add_meta_boxes', [$this, 'add_custom_fields_metabox']);
        add_action('save_post', [$this, 'save_custom_fields']);

        // Filter archive queries (for category filter)
        add_action('pre_get_posts', [$this, 'bms_filter_blogs_by_category']);
    }

    /**
     * Register the Custom Post Type: bms_blog
     */
    public function register_blog_post_type() {
        $labels = [
            'name'               => 'Blogs',
            'singular_name'      => 'Blog',
            'add_new'            => 'Add New Blog',
            'add_new_item'       => 'Add New Blog',
            'edit_item'          => 'Edit Blog',
            'new_item'           => 'New Blog',
            'all_items'          => 'All Blogs',
            'view_item'          => 'View Blog',
            'search_items'       => 'Search Blogs',
            'not_found'          => 'No blogs found',
            'not_found_in_trash' => 'No blogs found in Trash',
            'menu_name'          => 'Blogs'
        ];

        $args = [
            'labels'             => $labels,
            'public'             => true,
            'has_archive'        => true, 
            'rewrite'            => ['slug' => 'bms_blog'], 
            'show_in_rest'       => true,
            'supports'           => ['title', 'editor', 'thumbnail'],
            'menu_position'      => 5,
            'menu_icon'          => 'dashicons-admin-post',
            'taxonomies'         => ['blog_category'],
        ];

        register_post_type('bms_blog', $args);
    }

    /**
     * Register custom taxonomy: blog_category
     */
    public function register_blog_category_taxonomy() {
        $labels = [
            'name'              => 'Categories',
            'singular_name'     => 'Category',
            'search_items'      => 'Search Categories',
            'all_items'         => 'All Categories',
            'parent_item'       => 'Parent Category',
            'parent_item_colon' => 'Parent Category:',
            'edit_item'         => 'Edit Category',
            'update_item'       => 'Update Category',
            'add_new_item'      => 'Add New Category',
            'new_item_name'     => 'New Category Name',
            'menu_name'         => 'Categories',
        ];

        $args = [
            'hierarchical'      => true,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'show_in_rest'      => true,
            'rewrite'           => ['slug' => 'blog_category'],
        ];

        register_taxonomy('blog_category', 'bms_blog', $args);
    }

    /**
     * Add meta box for custom fields (Author Name)
     */
    public function add_custom_fields_metabox() {
        add_meta_box(
            'bms_blog_author_meta',
            'Author Name',
            [$this, 'display_author_metabox'],
            'bms_blog',
            'side',
            'default'
        );
    }

    /**
     * Display the Author Name field inside the meta box
     */
    public function display_author_metabox($post) {
        $author_name = get_post_meta($post->ID, 'author_name', true);
        
        wp_nonce_field('bms_blog_author_nonce', 'bms_blog_author_nonce_field');
        ?>

        <label for="author_name">Author Name:</label>
        <input type="text" 
               id="author_name" 
               name="author_name" 
               value="<?php echo esc_attr($author_name); ?>" 
               style="width: 100%;"
        >

        <?php
    }

    /**
     * Save the custom fields when the post is saved
     */
    public function save_custom_fields($post_id) {
        // Check if nonce is set and valid
        if (!isset($_POST['bms_blog_author_nonce_field']) || 
            !wp_verify_nonce($_POST['bms_blog_author_nonce_field'], 'bms_blog_author_nonce')) {
            return;
        }

        // Avoid autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // Check user permission
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Save author_name field
        if (isset($_POST['author_name'])) {
            update_post_meta($post_id, 'author_name', sanitize_text_field($_POST['author_name']));
        }
    }

    /**
     * Filter posts by blog_category on archive page
     */
    public function bms_filter_blogs_by_category($query) {
        if (!is_admin() && $query->is_main_query() && is_post_type_archive('bms_blog')) {

            if (!empty($_GET['blog_category'])) {
                $query->set('tax_query', [
                    [
                        'taxonomy' => 'blog_category',
                        'field'    => 'slug',
                        'terms'    => sanitize_text_field($_GET['blog_category']),
                    ],
                ]);
            }

            if (!empty($_GET['s'])) {
                $query->set('s', sanitize_text_field($_GET['s']));
            }
        }
    }

}
