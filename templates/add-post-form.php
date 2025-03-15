<?php
if (!is_user_logged_in()) {
    echo '<p style="margin-bottom: 0px !important; text-align: center;">You must be logged in to add a post.</p>';
    return;
}
?>

<div class="bms-add-post-wrapper">

    <!-- FORM CONTAINER -->
    <div id="bms-add-post-form-container">
        <form id="bms-add-post" class="bms-add-post-form" enctype="multipart/form-data">
            <input type="text" name="title" class="col-half-form" placeholder="Post Title" required>

            <input type="text" name="author" class="col-half-form" placeholder="Author Name" required>

            <textarea name="content" placeholder="Post Content" required></textarea>

            <select name="category" required>
                <option value="">Select Category</option>
                <?php
                $categories = get_terms([
                    'taxonomy' => 'blog_category',
                    'hide_empty' => false
                ]);
                foreach ($categories as $cat) {
                    echo '<option value="' . esc_attr($cat->term_id) . '">' . esc_html($cat->name) . '</option>';
                }
                ?>
            </select>

            <input type="file" name="featured_image" accept="image/*" required>

            <button class="bms-form-button" type="submit">Add Post</button>
        </form>

        <!-- Loader -->
        <div id="bms-form-loader" class="bms-form-loader-overlay" style="display: none;">
            <div class="bms-spinner"></div>
        </div>
    </div>

    <!-- SUCCESS MESSAGE CONTAINER -->
    <div id="bms-add-post-success-container" style="display: none;">
        <h2>Thank you!</h2>
        <p>Your blog post has been submitted successfully. It is currently pending admin review. Once approved, it will appear on the website.</p>
    </div>

</div>
