<?php get_header(); ?>

<div class="content-area">
    <div class="site-main">

        <header class="search-header">
            <h1 class="search-title">
                <?php printf(esc_html__('Search Results for: %s', 'travel-verdict'), '<span>' . get_search_query() . '</span>'); ?>
            </h1>
        </header>

        <?php if (have_posts()) : ?>

            <div class="posts-grid search-results-list">
                <?php while (have_posts()) : the_post(); ?>
                    <?php get_template_part('template-parts/content', 'excerpt'); ?>
                <?php endwhile; ?>
            </div>

            <?php if ($wp_query->max_num_pages > 1) : ?>
                <div class="load-more-container">
                    <button id="load-more-posts" class="load-more-button">Load More</button>
                </div>
            <?php endif; ?>

        <?php else : ?>
            <div class="no-results-content">
                <p><?php esc_html_e('Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'travel-verdict'); ?></p>
                <?php get_search_form(); ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php get_footer(); ?>
