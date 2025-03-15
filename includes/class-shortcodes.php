<?php
if (!defined('ABSPATH')) exit;

class BMS_Shortcodes {

    public function __construct() {
        add_shortcode('bms_blog_list', [$this, 'display_blog_posts_shortcode']);
    }

    /**
     * Shortcode handler: [bms_blog_list]
     */
    public function display_blog_posts_shortcode($atts) {
        ob_start();

        // Query Args (customizable if needed)
        $args = [
            'post_type'      => 'bms_blog',
            'posts_per_page' => get_option('posts_per_page'),
            'paged'          => get_query_var('paged') ? get_query_var('paged') : 1,
        ];

        // Custom query
        $query = new WP_Query($args);

        if ($query->have_posts()) :

            echo '<div class="bms-container">';

            echo '<h1>All Blogs</h1>';

            // SEARCH / FILTER FORM
            ?>
                <div class="bms-blog-controls">
                    <?php
                    if (file_exists(BMS_PLUGIN_PATH . 'templates/search-bms.php')) {
                        include BMS_PLUGIN_PATH . 'templates/search-bms.php';
                    }

                    // ADD BLOG BUTTON
                        echo '<button id="bms-add-blog-btn" class="bms-btn">+ Add New Blog</button>';

                    ?>
                </div>
            <?php
            // BLOG LISTING GRID
            include BMS_PLUGIN_PATH . 'templates/loop-bms-blogs.php';

            // ADD POST FORM MODAL
                echo '<div id="bms-modal" class="bms-modal">
                        <div class="bms-modal-content">
                            <span class="bms-close">&times;</span>';
                include BMS_PLUGIN_PATH . 'templates/add-post-form.php';
                echo '</div></div>';


            echo '</div>';

        else :
            echo '<p>No blog posts found.</p>';
        endif;

        wp_reset_postdata();

        return ob_get_clean();
    }
}
