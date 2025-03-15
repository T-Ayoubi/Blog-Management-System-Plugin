<?php
if (!defined('ABSPATH')) exit;

get_header();
?>

<div class="bms-container">

    <h1>All Blogs</h1>

    <!-- SEARCH FORM + ADD BLOG BUTTON -->
    <div class="bms-blog-controls">

        <!-- SEARCH / FILTER FORM -->
        <?php
        if (file_exists(BMS_PLUGIN_PATH . 'templates/search-bms.php')) {
            include BMS_PLUGIN_PATH . 'templates/search-bms.php';
        }
        ?>

        <!-- ADD BLOG BUTTON (Admins Only) -->
        <?php if (current_user_can('manage_options')) : ?>
            <button id="bms-add-blog-btn" class="bms-btn">+ Add New Blog</button>
        <?php endif; ?>

    </div>

    <!-- BLOG LISTING GRID -->
    <?php
    // Use the default global WP query on archive pages
    $query = $wp_query;

    // Include the shared blog loop template
    include BMS_PLUGIN_PATH . 'templates/loop-bms-blogs.php';
    ?>

    <!-- ADD POST FORM MODAL (Any Logged-in User) -->
        <div id="bms-modal" class="bms-modal">
            <div class="bms-modal-content">
                <span class="bms-close">&times;</span>
                <?php include BMS_PLUGIN_PATH . 'templates/add-post-form.php'; ?>
            </div>
        </div>



</div>

<?php get_footer(); ?>
