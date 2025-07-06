<?php get_header(); ?>

<div class="content-area">
    <div class="site-main">
        <?php
        while (have_posts()) :
            the_post();
            ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <header class="entry-header-single">
                    <div class="entry-header-tools">
                        <button id="distraction-free-toggle" class="header-icon" aria-label="Toggle Distraction Free Mode">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="24" height="24"><path d="M10 4H4v6h6V4zm10 10h-6v6h6v-6zM14 4h-4v2h4V4zm0 4h-4v2h4V8zm0 4h-4v2h4v-2zM8 14H6v-2h2v2zm0 4H6v-2h2v2zm-2 2H4v-2h2v2zm14-8h-2v-2h2v2zm0 4h-2v-2h2v2zm0 4h-2v-2h2v2zm2 2h-2v-2h2v2zM16 4h2v6h-2V4zM8 4H6v6h2V4z"/></svg>
                        </button>
                    </div>
                    <?php the_title('<h1 class="entry-title">', '</h1>'); ?>
                    <div class="entry-meta-compact">
                        <?php
                        $rating_display = travel_verdict_display_star_rating(get_the_ID());
                        if ($rating_display) {
                            echo $rating_display;
                            echo '<span class="meta-sep">•</span>';
                        }

                        $location = get_post_meta(get_the_ID(), '_tv_location', true);
                        if (!empty($location)) {
                            $maps_link = 'https://maps.google.com/?q=' . urlencode($location);
                            echo '<span class="post-location"><a href="' . esc_url($maps_link) . '" target="_blank" rel="noopener noreferrer">' . esc_html($location) . '</a></span>';
                            echo '<span class="meta-sep">•</span>';
                        }
                        ?>
                        <span class="post-date"><?php echo get_the_date(); ?></span>
                        <?php
                        $last_updated = travel_verdict_get_last_updated_date();
                        if ($last_updated) {
                            echo '<span class="meta-sep">•</span>';
                            echo $last_updated;
                        }
                        ?>
                        <span class="meta-sep">•</span>
                        <span class="post-author"><?php the_author_posts_link(); ?></span>
                        <span class="meta-sep">•</span>
                        <span class="reading-time"><?php echo travel_verdict_reading_time(get_the_ID()); ?></span>
                        <span class="meta-sep">•</span>
                        <span class="post-views"><?php echo travel_verdict_get_post_views(get_the_ID()); ?></span>
                    </div>
                </header>

                <?php if (has_post_thumbnail()) : ?>
                    <figure class="post-thumbnail-single">
                        <?php the_post_thumbnail('full'); ?>
                        <?php
                        $caption = get_the_post_thumbnail_caption();
                        if (!empty($caption)) :
                        ?>
                            <figcaption class="post-thumbnail-caption"><?php echo esc_html($caption); ?></figcaption>
                        <?php endif; ?>
                    </figure>
                <?php endif; ?>

                <div class="entry-content-wrapper">
                    <div class="entry-content">
                        <?php the_content(); ?>
                    </div>

                    <?php
                    $map_embed_code = get_post_meta(get_the_ID(), '_tv_map_embed', true);
                    if (!empty($map_embed_code)) :
                    ?>
                        <div class="map-embed">
                            <h3 class="map-title">Location Map</h3>
                            <div class="map-embed-code">
                                <?php echo $map_embed_code; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <footer class="entry-footer">
                    <div class="user-engagement-features">
                        <button class="like-button" data-post-id="<?php echo get_the_ID(); ?>" aria-label="Like this post">❤️ <span class="like-count"><?php echo travel_verdict_get_likes_count(get_the_ID()); ?></span></button>
                        <button class="comments-toggle" aria-label="Toggle Comments">💬</button>
                        <button class="copy-link-button" data-url="<?php the_permalink(); ?>" aria-label="Copy Link">🔗</button>
                        <button id="print-post-button" class="print-post-button" aria-label="Print Post">🖨️</button>
                        <div class="share-feature">
                            <button class="share-button" aria-label="Share Post">📤</button>
                            <div class="share-options">
                                <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(get_permalink()); ?>&text=<?php echo urlencode(get_the_title()); ?>" target="_blank">Twitter</a>
                                <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_permalink()); ?>" target="_blank">Facebook</a>
                                <a href="whatsapp://send?text=<?php echo urlencode(get_the_title() . ' ' . get_permalink()); ?>" data-action="share/whatsapp/share">WhatsApp</a>
                            </div>
                        </div>
                    </div>
                </footer>

                <div id="comments-section" class="comments-area" style="display: none;">
                    <?php if (comments_open() || get_comments_number()) : comments_template(); endif; ?>
                </div>

            </article>

            <div class="post-components-wrapper">
                <?php
                if (get_the_author_meta('description')) : ?>
                    <div class="author-box">
                        <div class="author-avatar"><?php echo get_avatar(get_the_author_meta('user_email'), 85); ?></div>
                        <div class="author-info">
                            <h4 class="author-name"><?php echo get_the_author(); ?></h4>
                            <p class="author-description"><?php the_author_meta('description'); ?></p>
                        </div>
                    </div>
                <?php endif; ?>

                <?php
                the_post_navigation(array('prev_text' => '<span class="nav-title-meta">Previous Post</span><span class="nav-title">%title</span>', 'next_text' => '<span class="nav-title-meta">Next Post</span><span class="nav-title">%title</span>'));

                // Suggested Posts by Category
                $categories = get_the_category(get_the_ID());
                if ($categories) {
                    $category_ids = array_map(function($cat) { return $cat->term_id; }, $categories);
                    $args = array('category__in' => $category_ids, 'post__not_in' => array(get_the_ID()), 'posts_per_page' => 3, 'ignore_sticky_posts' => 1);
                    $suggested_posts_query = new wp_query($args);
                    if ($suggested_posts_query->have_posts()) {
                        echo '<div class="suggested-posts-section"><h3 class="footer-section-title">You Might Also Like</h3><div class="suggested-posts-grid">';
                        while ($suggested_posts_query->have_posts()) {
                            $suggested_posts_query->the_post();
                            get_template_part('template-parts/content', 'suggested');
                        }
                        echo '</div></div>';
                    }
                    wp_reset_postdata();
                }

                // Related Posts by Tags
                $tags = wp_get_post_tags(get_the_ID());
                if ($tags) {
                    $tag_ids = array();
                    foreach($tags as $individual_tag) $tag_ids[] = $individual_tag->term_id;
                    $args = array(
                        'tag__in' => $tag_ids,
                        'post__not_in' => array(get_the_ID()),
                        'posts_per_page' => 3,
                        'ignore_sticky_posts' => 1
                    );
                    $related_posts_query = new wp_query($args);
                    if ($related_posts_query->have_posts()) {
                        echo '<div class="related-posts-section"><h3 class="footer-section-title">Related Articles</h3><div class="suggested-posts-grid">';
                        while ($related_posts_query->have_posts()) {
                            $related_posts_query->the_post();
                            get_template_part('template-parts/content', 'suggested');
                        }
                        echo '</div></div>';
                    }
                    wp_reset_postdata();
                }
                ?>
            </div>
        <?php endwhile; ?>
    </div>
</div>
<?php get_footer(); ?>
