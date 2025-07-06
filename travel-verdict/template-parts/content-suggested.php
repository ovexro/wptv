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
