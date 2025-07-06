<article id="post-<?php the_ID(); ?>" <?php post_class('post-excerpt'); ?>>
    <?php if (has_post_thumbnail()) : ?>
        <a href="<?php the_permalink(); ?>" class="post-thumbnail-link">
            <?php the_post_thumbnail('large'); ?>
        </a>
    <?php endif; ?>

    <div class="post-excerpt-content">
        <?php echo travel_verdict_get_post_terms(get_the_ID()); ?>
        
        <header class="entry-header">
            <?php the_title(sprintf('<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url(get_permalink())), '</a></h2>'); ?>
        </header>

        <div class="entry-meta">
            <span class="post-date"><?php echo get_the_date(); ?></span>
            <span class="meta-sep">&bull;</span>
            <span class="reading-time"><?php echo travel_verdict_reading_time(get_the_ID()); ?></span>
        </div>

        <div class="entry-content">
            <?php the_excerpt(); ?>
            <a href="<?php the_permalink(); ?>" class="read-more">Read More</a>
        </div>
    </div>
</article>
