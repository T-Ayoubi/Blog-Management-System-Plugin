<?php
if (!defined('ABSPATH')) exit;

if (empty($query)) return;
?>

<div class="bms-blog-listing">
    <?php if ($query->have_posts()) : ?>
        <div class="bms-blog-grid">
            <?php while ($query->have_posts()) : $query->the_post(); ?>
                <div class="bms-blog-card" id="bms-post-<?php echo get_the_ID(); ?>">

                    <!-- Featured Image -->
                    <?php
                    $fallback_image = BMS_PLUGIN_URL . 'assets/images/fallback.jpg';
                    $thumbnail_url = get_the_post_thumbnail_url(get_the_ID(), 'medium');
                    $image_to_show = $thumbnail_url ? $thumbnail_url : $fallback_image;
                    ?>
                    <a href="<?php the_permalink(); ?>" class="bms-blog-image-wrapper">
                        <img src="<?php echo esc_url($image_to_show); ?>" alt="<?php the_title_attribute(); ?>" class="bms-blog-image">
                    </a>

                    <!-- Category -->
                    <div class="bms-blog-category">
                        <?php
                        $terms = wp_get_post_terms(get_the_ID(), 'blog_category');
                        if (!empty($terms)) :
                            $term = $terms[0];
                            $term_link = get_term_link($term);
                        ?>
                            <a href="<?php echo esc_url($term_link); ?>" class="bms-blog-category-link">
                                <?php echo esc_html($term->name); ?>
                            </a>
                        <?php endif; ?>
                    </div>

                    <!-- Title -->
                    <h2 class="bms-blog-title">
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </h2>

                    <!-- Excerpt -->
                    <div class="bms-blog-excerpt">
                        <?php echo wp_trim_words(get_the_excerpt(), 15, '...'); ?>
                    </div>

                    <!-- Read More Button -->
                    <a href="<?php the_permalink(); ?>" class="bms-btn bms-read-more">Continue Reading</a>

                    <!-- Admin Buttons -->
                    <?php if (current_user_can('manage_options')) : ?>
                        <div class="bms-admin-buttons">
                            <a href="<?php echo admin_url('post.php?post=' . get_the_ID() . '&action=edit'); ?>" class="bms-btn bms-edit-btn">Edit</a>
                            <button class="bms-btn bms-delete-btn" data-post-id="<?php echo get_the_ID(); ?>">Delete</button>
                        </div>
                    <?php endif; ?>

                </div>
            <?php endwhile; ?>
        </div>

        <!-- PAGINATION -->
        <div class="bms-pagination">
            <?php
            echo paginate_links([
                'total' => $query->max_num_pages,
                'current' => max(1, get_query_var('paged'))
            ]);
            ?>
        </div>

    <?php else : ?>
        <p>No blog posts found.</p>
    <?php endif; ?>
</div>
