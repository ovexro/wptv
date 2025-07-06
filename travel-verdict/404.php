<?php get_header(); ?>

<div class="content-area">
    <div class="site-main">

        <section class="error-404 not-found">
            <header class="page-header-404">
                <h1 class="page-title-404"><?php esc_html_e('Oops! That page can&rsquo;t be found.', 'travel-verdict'); ?></h1>
            </header><div class="page-content-404">
                <p><?php esc_html_e('It looks like nothing was found at this location. Maybe try a search, or check out one of our popular articles below?', 'travel-verdict'); ?></p>

                <?php get_search_form(); ?>

                <div class="popular-posts-404">
                    <h3 class="footer-section-title"><?php esc_html_e('Popular Articles', 'travel-verdict'); ?></h3>
                    <?php
                    $popular_posts_query = new WP_Query(array(
                        'posts_per_page'      => 3,
                        'meta_key'            => 'post_views_count',
                        'orderby'             => 'meta_value_num',
                        'order'               => 'DESC',
                        'ignore_sticky_posts' => 1
                    ));

                    if ($popular_posts_query->have_posts()) :
                        echo '<div class="suggested-posts-grid">';
                        while ($popular_posts_query->have_posts()) : $popular_posts_query->the_post();
                    ?>
                            <div class="suggested-post-card">
                                <a href="<?php the_permalink(); ?>" class="suggested-post-image-link">
                                    <?php if (has_post_thumbnail()) :
                                        the_post_thumbnail('medium_large');
                                    else : ?>
                                        <img src="https://via.placeholder.com/400x300.png?text=Travel+Verdict" alt="Placeholder Image" loading="lazy">
                                    <?php endif; ?>
                                </a>
                                <div class="suggested-post-content">
                                    <h4 class="suggested-post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                                    <div class="suggested-post-meta">
                                        <span><?php echo get_the_date('M j, Y'); ?></span>
                                    </div>
                                </div>
                            </div>
                    <?php
                        endwhile;
                        echo '</div>';
                        wp_reset_postdata();
                    endif;
                    ?>
                </div>

            </div></section></div></div><?php get_footer(); ?>
