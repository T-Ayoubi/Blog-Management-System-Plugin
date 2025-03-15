<?php
get_header();

if (have_posts()) :
    while (have_posts()) : the_post();
        $author = get_post_meta(get_the_ID(), 'author_name', true);
        $categories = wp_get_post_terms(get_the_ID(), 'blog_category');
        $created_at = get_the_date('F j, Y');
        $image_url = get_the_post_thumbnail_url(get_the_ID(), 'full');
?>

<div class="bms-single-post-wrapper">

    <!-- LEFT COLUMN -->
    <div class="bms-single-post-left">

        <!-- Categories Badges -->
        <?php if (!empty($categories)) : ?>
            <div class="bms-categories">
                <?php foreach ($categories as $cat) : ?>
                    <span class="bms-category-badge"><?php echo esc_html($cat->name); ?></span>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Title -->
        <h1 class="bms-single-title"><?php the_title(); ?></h1>

        <!-- Author & Date -->
        <div class="bms-meta-info">
            <span>Posted by <?php echo esc_html($author); ?></span>
            <span>On <?php echo esc_html($created_at); ?></span>
        </div>

        <!-- Featured Image -->
        <?php if ($image_url): ?>
            <div class="bms-featured-image">
                <img src="<?php echo esc_url($image_url); ?>" alt="<?php the_title_attribute(); ?>">
            </div>
        <?php endif; ?>

        <!-- Content -->
        <div class="bms-post-content">
            <?php the_content(); ?>
        </div>

    </div>

    <!-- RIGHT COLUMN - Recent Blogs -->
    <aside class="bms-single-post-sidebar">

        <div class="bms-related-posts">
            <h3>Recent Blogs</h3>
            <ul>
                <?php
                $recent_blogs = new WP_Query([
                    'post_type'      => 'bms_blog',
                    'posts_per_page' => 5, 
                    'post__not_in'   => [get_the_ID()], 
                    'orderby'        => 'date',
                    'order'          => 'DESC'
                ]);

                if ($recent_blogs->have_posts()) :
                    while ($recent_blogs->have_posts()) : $recent_blogs->the_post();
                        $recent_thumb = get_the_post_thumbnail_url(get_the_ID(), 'thumbnail');
                        ?>
                        <li class="bms-related-post-item">

                            <a href="<?php the_permalink(); ?>" class="bms-related-post-link">
                                <?php if ($recent_thumb) : ?>
                                    <div class="bms-related-thumb">
                                        <img src="<?php echo esc_url($recent_thumb); ?>" alt="<?php the_title_attribute(); ?>">
                                    </div>
                                <?php endif; ?>

                                <div class="bms-related-info">
                                    <span class="bms-related-post-title"><?php the_title(); ?></span>
                                    <div class="bms-related-post-date"><?php echo get_the_date('F j, Y'); ?></div>
                                </div>
                            </a>

                        </li>
                    <?php endwhile;
                    wp_reset_postdata();
                else :
                    echo '<li>No recent blogs found.</li>';
                endif;
                ?>
            </ul>
        </div>

    </aside>

</div>

<?php
    endwhile;
endif;

get_footer();
