</main><footer id="colophon" class="site-footer">

    <div class="footer-primary-content">
        <div class="footer-container">
            <div id="footer-instagram">
                <h4 class="widget-title">Follow on Instagram</h4>
                <div class="grid-images">
                    <?php
                    for ($i = 1; $i <= 6; $i++) {
                        $post_link = get_theme_mod('instagram_post_link_' . $i);
                        $image_url = get_theme_mod('instagram_image_url_' . $i);
                        if (!empty($post_link) && !empty($image_url)) {
                            echo '<a href="' . esc_url($post_link) . '" class="instagram-image-link" target="_blank" rel="noopener noreferrer">';
                            echo '<img src="' . esc_url($image_url) . '" alt="Instagram Post ' . $i . '" loading="lazy">';
                            echo '<div class="instagram-overlay"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12,9.5A2.5,2.5,0,1,0,14.5,12,2.5,2.5,0,0,0,12,9.5Zm7.5-5H4.5A2.5,2.5,0,0,0,2,7V17a2.5,2.5,0,0,0,2.5,2.5h15A2.5,2.5,0,0,0,22,17V7A2.5,2.5,0,0,0,19.5,4.5Zm-7.5,11A4.5,4.5,0,1,1,16.5,12,4.5,4.5,0,0,1,12,15.5Zm4.75-8.25a1,1,0,1,1-1-1A1,1,0,0,1,16.75,7.25Z"/></svg></div>';
                            echo '</a>';
                        }
                    }
                    ?>
                </div>
            </div>
            <div class="footer-widget-area">
                <?php if (is_active_sidebar('footer-widgets')) : ?>
                    <?php dynamic_sidebar('footer-widgets'); ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="site-info">
        <div class="footer-container">
            <div class="footer-social-links">
                <?php
                $social_networks = ['facebook', 'twitter', 'instagram', 'youtube', 'pinterest', 'linkedin'];
                foreach ($social_networks as $network) {
                    $url = get_theme_mod('social_link_' . $network);
                    if (!empty($url)) {
                        echo '<a href="' . esc_url($url) . '" class="social-icon social-' . $network . '" target="_blank" rel="noopener noreferrer" aria-label="' . ucfirst($network) . '">';
                        echo '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="22" height="22" role="img"><title>' . ucfirst($network) . '</title>';
                        if($network == 'twitter') echo '<path d="M22.46,6.52a8.38,8.38,0,0,1-2.4,0.66,4.2,4.2,0,0,0,1.84-2.31,8.41,8.41,0,0,1-2.65,1,4.19,4.19,0,0,0-7.14,3.82,11.89,11.89,0,0,1-8.62-4.37,4.19,4.19,0,0,0,1.3,5.6,4.16,4.16,0,0,1-1.9-.52v0.05a4.19,4.19,0,0,0,3.36,4.11,4.21,4.21,0,0,1-1.89,0.07,4.19,4.19,0,0,0,3.91,2.9,8.4,8.4,0,0,1-5.22,1.8,8.45,8.45,0,0,1-1,0,11.83,11.83,0,0,0,6.4,1.88,11.85,11.85,0,0,0,11.85-11.85c0-0.18,0-0.36-0.01-0.54A8.46,8.46,0,0,0,2.08-2.16Z"/>';
                        if($network == 'facebook') echo '<path d="M21,3H3A1,1,0,0,0,2,4V20a1,1,0,0,0,1,1h9.52V14.39H10.15V11.52h2.37V9.31c0-2.35,1.4-3.64,3.53-3.64a18.73,18.73,0,0,1,2.1.11v2.43H16.8c-1.14,0-1.36,0.54-1.36,1.33v1.75h2.7l-0.35,2.87H15.44V21H21a1,1,0,0,0,1-1V4A1,1,0,0,0,21,3Z"/>';
                        if($network == 'instagram') echo '<path d="M12,9.5A2.5,2.5,0,1,0,14.5,12,2.5,2.5,0,0,0,12,9.5Zm7.5-5H4.5A2.5,2.5,0,0,0,2,7V17a2.5,2.5,0,0,0,2.5,2.5h15A2.5,2.5,0,0,0,22,17V7A2.5,2.5,0,0,0,19.5,4.5Zm-7.5,11A4.5,4.5,0,1,1,16.5,12,4.5,4.5,0,0,1,12,15.5Zm4.75-8.25a1,1,0,1,1-1-1A1,1,0,0,1,16.75,7.25Z"/>';
                        if($network == 'youtube') echo '<path d="M21.58,7.93a2,2,0,0,0-1.42-1.42C18.4,6,12,6,12,6s-6.4,0-8.16,0.51a2,2,0,0,0-1.42,1.42A20.78,20.78,0,0,0,2,12a20.78,20.78,0,0,0,0.42,4.07,2,2,0,0,0,1.42,1.42C5.6,18,12,18,12,18s6.4,0,8.16-0.51a2,2,0,0,0,1.42-1.42A20.78,20.78,0,0,0,22,12,20.78,20.78,0,0,0,21.58,7.93ZM10,14.5V9.5l4.33,2.5Z"/>';
                        if($network == 'pinterest') echo '<path d="M12,2A10,10,0,0,0,2,12c0,4.42,2.87,8.17,6.84,9.5.08,0,.16-0.08.2-0.16l0.25-1c0.06-.25,0-0.5-.16-0.7l-1.39-2.77s-0.34-.67-0.34-1.65c0-1.55.9-2.71,2-2.71a1.42,1.42,0,0,1,1.5,1.06c0,0.64-0.4,1.6-0.6,2.5a1.41,1.41,0,0,0,1.4,1.72c1.65,0,2.92-1.75,2.92-4.25,0-2.29-1.65-3.92-4-3.92a4.41,4.41,0,0,0-4.58,4.33,2.68,2.68,0,0,0,.83,2.05,0.3,0.3,0,0,1,.09.32l-0.27.92c-0.06,0.25,0,0.5,0.16,0.7l0.25,1c0.06,0.25,0,0.5-0.16,0.7l-0.25,1A10,10,0,1,0,12,2Z"/>';
                        if($network == 'linkedin') echo '<path d="M21,3H3a1,1,0,0,0-1,1V20a1,1,0,0,0,1,1H21a1,1,0,0,0,1-1V4A1,1,0,0,0,21,3ZM8.34,18.34H5.67V9.7h2.67Zm-1.33-9.9A1.5,1.5,0,1,1,8.34,7,1.5,1.5,0,0,1,7,8.44ZM18.34,18.34h-2.6V14.6c0-.89-.06-2-1.33-2s-1.5,1-1.5,2v3.7h-2.6V9.7h2.5v1.12h0.07a2.8,2.8,0,0,1,2.53-1.4C17.61,9.42,18.34,11,18.34,14Z"/>';
                        echo '</svg></a>';
                    }
                }
                ?>
            </div>
            <div class="footer-tools">
                <div class="font-size-controls">
                    <span>Text Size:</span>
                    <button class="font-size-button" data-size="small" aria-label="Small text">A</button>
                    <button class="font-size-button active" data-size="medium" aria-label="Medium text">A</button>
                    <button class="font-size-button" data-size="large" aria-label="Large text">A</button>
                </div>
            </div>
            <nav class="footer-navigation">
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'footer',
                    'menu_class'     => 'footer-menu',
                    'fallback_cb'    => false
                ));
                ?>
            </nav>
            <div class="copyright">
                © <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. <?php echo get_theme_mod('copyright_text', 'All Rights Reserved.'); ?>
            </div>
        </div>
    </div>
</footer>

<div id="tv-lightbox-overlay" class="tv-lightbox-overlay">
    <span id="tv-lightbox-close" class="tv-lightbox-close">×</span>
    <img id="tv-lightbox-image" class="tv-lightbox-image" src="" alt="Lightbox image">
</div>

<a href="#" id="back-to-top" class="back-to-top" aria-label="Back to top">↑</a>

<?php get_template_part('template-parts/cookie-consent'); ?>

<?php wp_footer(); ?>

</body>
</html>
