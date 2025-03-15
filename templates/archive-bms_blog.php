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

        <!-- ADD BLOG BUTTON -->
        <button id="bms-add-blog-btn" class="bms-btn">+ Add New Blog</button>

    </div>

    <!-- BLOG LISTING GRID -->
    <?php
    $query = $wp_query;

    if ($query->have_posts()) :
        // Include the shared blog loop template
        include BMS_PLUGIN_PATH . 'templates/loop-bms-blogs.php';
    else :
        // Display message if no blogs exist
        ?>
        <p style="text-align: center;">No blogs found. You can <strong>add new blogs</strong> by clicking the button above.</p>
    <?php
    endif;
    ?>

    <!-- ADD POST FORM MODAL -->
    <div id="bms-modal" class="bms-modal">
        <div class="bms-modal-content">
            <span class="bms-close">&times;</span>
            <?php include BMS_PLUGIN_PATH . 'templates/add-post-form.php'; ?>
        </div>
    </div>

</div>

<?php get_footer(); ?>
