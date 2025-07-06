<?php get_header(); ?>

<div class="content-area">
    <div class="site-main">

        <header class="archive-header">
            <?php
            the_archive_title('<h1 class="archive-title">', '</h1>');
            the_archive_description('<div class="archive-description">', '</div>');
            ?>
        </header>

        <?php if (have_posts()) : ?>

            <div class="posts-grid">
                <?php while (have_posts()) : the_post(); ?>
                    <?php get_template_part('template-parts/content', 'excerpt'); ?>
                <?php endwhile; ?>
            </div>

            <?php if ($wp_query->max_num_pages > 1) : ?>
                <div class="load-more-container">
                    <button id="load-more-posts" class="load-more-button">Load More</button>
                </div>
            <?php endif; ?>

        <?php else : get_template_part('template-parts/content', 'none'); endif; ?>
    </div>
</div>

<?php get_footer(); ?>
