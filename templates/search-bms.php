<?php
if (!defined('ABSPATH')) {
    exit;
}

$categories = get_terms(['taxonomy' => 'blog_category', 'hide_empty' => false]);
?>

<form method="get" action="<?php echo esc_url(get_post_type_archive_link('bms_blog')); ?>" class="bms-search-form">
    <input type="text" name="s" placeholder="Search blogs..." value="<?php echo get_search_query(); ?>">

    <select name="blog_category">
        <option value="">All Categories</option>
        <?php foreach ($categories as $category) : ?>
            <option value="<?php echo esc_attr($category->slug); ?>"
                <?php selected($_GET['blog_category'] ?? '', $category->slug); ?>>
                <?php echo esc_html($category->name); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <button type="submit">Search</button>
</form>
