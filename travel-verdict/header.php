<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0073aa">
    <link rel="manifest" href="<?php echo get_template_directory_uri(); ?>/manifest.json">
    <link rel="apple-touch-icon" href="<?php echo get_template_directory_uri(); ?>/assets/images/icons/icon-192x192.png">
    <?php wp_head(); ?>
    <?php if (is_single()) { travel_verdict_article_schema(); } ?>
</head>
<body <?php body_class(); ?>>
    <?php if (is_single()) { echo '<div class="reading-progress-bar"></div>'; } ?>
    <a class="skip-link screen-reader-text" href="#content"><?php esc_html_e('Skip to content', 'travel-verdict'); ?></a>

    <header id="masthead" class="site-header">
        <div class="header-container">
            <div class="site-branding">
                <a href="<?php echo esc_url(home_url('/')); ?>" rel="home">
                    <img src="<?php echo esc_url(get_theme_mod('logo_light', get_template_directory_uri() . '/assets/images/logo-light.png')); ?>" alt="<?php bloginfo('name'); ?> Logo" class="logo-light">
                    <img src="<?php echo esc_url(get_theme_mod('logo_dark', get_template_directory_uri() . '/assets/images/logo-dark.png')); ?>" alt="<?php bloginfo('name'); ?> Logo" class="logo-dark">
                </a>
            </div>
            <nav id="site-navigation" class="main-navigation">
                <?php wp_nav_menu(array('theme_location' => 'primary', 'menu_id' => 'primary-menu')); ?>
            </nav>
            <div class="header-tools">
                <div class="social-links-container">
                    <?php
                    $social_networks = ['facebook', 'twitter', 'instagram', 'youtube', 'pinterest', 'linkedin'];
                    foreach($social_networks as $network) {
                        $url = get_theme_mod('social_link_' . $network);
                        if (!empty($url)) {
                            echo '<a href="' . esc_url($url) . '" class="social-icon social-' . $network . '" target="_blank" rel="noopener noreferrer" aria-label="' . ucfirst($network) . '">';
                            echo '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="20" height="20" role="img"><title>' . ucfirst($network) . '</title>';
                            if($network == 'twitter') echo '<path d="M22.46,6.52a8.38,8.38,0,0,1-2.4,0.66,4.2,4.2,0,0,0,1.84-2.31,8.41,8.41,0,0,1-2.65,1,4.19,4.19,0,0,0-7.14,3.82,11.89,11.89,0,0,1-8.62-4.37,4.19,4.19,0,0,0,1.3,5.6,4.16,4.16,0,0,1-1.9-.52v0.05a4.19,4.19,0,0,0,3.36,4.11,4.21,4.21,0,0,1-1.89,0.07,4.19,4.19,0,0,0,3.91,2.9,8.4,8.4,0,0,1-5.22,1.8,8.45,8.45,0,0,1-1,0,11.83,11.83,0,0,0,6.4,1.88,11.85,11.85,0,0,0,11.85-11.85c0-0.18,0-0.36-0.01-0.54a8.46,8.46,0,0,0,2.08-2.16Z"/>';
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
                <button id="search-toggle" class="header-icon" aria-label="Search"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M10,18a8,8,0,1,1,8-8A8,8,0,0,1,10,18ZM10,4a6,6,0,1,0,6,6A6,6,0,0,0,10,4Z"/><path d="M21,21a1,1,0,0,1-.71-.29l-4-4a1,1,0,0,1,1.42-1.42l4,4a1,1,0,0,1,0,1.42A1,1,0,0,1,21,21Z"/></svg></button>
                <button id="dark-mode-toggle" class="header-icon" aria-label="Toggle Dark Mode"><svg class="sun-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12,18a6,6,0,1,1,6-6A6,6,0,0,1,12,18ZM12,8a4,4,0,1,0,4,4A4,4,0,0,0,12,8Z"/><path d="M12,4a1,1,0,0,1-1-1V1a1,1,0,0,1,2,0V3A1,1,0,0,1,12,4Z"/><path d="M12,24a1,1,0,0,1-1-1V21a1,1,0,0,1,2,0v2A1,1,0,0,1,12,24Z"/><path d="M21,13H19a1,1,0,0,1,0-2h2a1,1,0,0,1,0,2Z"/><path d="M5,13H3a1,1,0,0,1,0-2H5a1,1,0,0,1,0,2Z"/><path d="M19.07,6.34a1,1,0,0,1-.71-.29l-1.41-1.42a1,1,0,0,1,1.41-1.41l1.42,1.41a1,1,0,0,1,0,1.42A1,1,0,0,1,19.07,6.34Z"/><path d="M6.34,19.07a1,1,0,0,1-.7-.29A1,1,0,0,1,5.63,17.66l1.42-1.41a1,1,0,0,1,1.41,1.41L7.05,19.07A1,1,0,0,1,6.34,19.07Z"/><path d="M19.07,20.49a1,1,0,0,1-.71-.29l-1.41-1.42a1,1,0,0,1,1.41-1.41l1.42,1.41a1,1,0,0,1-.71,1.71Z"/><path d="M4.93,6.34a1,1,0,0,1-.71-1.71L5.63,3.22a1,1,0,1,1,1.41,1.41L5.63,6.05A1,1,0,0,1,4.93,6.34Z"/></svg><svg class="moon-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12.3,22.33a10.42,10.42,0,0,1-5.12-1.33A10.15,10.15,0,0,1,3,12.42,10.25,10.25,0,0,1,12.28,2a.75.75,0,0,1,.63.65.73.73,0,0,1-.2,0.72,8.68,8.68,0,0,0,2.37,11.52,8.5,8.5,0,0,0,11.1,1.48.73.73,0,0,1,.73.2.75.75,0,0,1,.09,1A10.3,10.3,0,0,1,12.3,22.33Zm-1.57-2.3a8.85,8.85,0,0,0,3.58-.88,10,10,0,0,1-11-12.72,8.76,8.76,0,0,0,7.84,14.07A8.44,8.44,0,0,0,10.73,20Z"/></svg></button>
                <button id="mobile-menu-toggle" class="header-icon mobile-only" aria-label="Open Menu"><svg class="menu-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M3,8H21a1,1,0,0,0,0-2H3A1,1,0,0,0,3,8Zm18,8H3a1,1,0,0,0,0,2H21a1,1,0,0,0,0-2Zm0-5H3a1,1,0,0,0,0,2H21a1,1,0,0,0,0-2Z"/></svg><svg class="close-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M13.41,12l6.3-6.29a1,1,0,1,0-1.42-1.42L12,10.59,5.71,4.29A1,1,0,0,0,4.29,5.71L10.59,12l-6.3,6.29a1,1,0,0,0,0,1.42,1,1,0,0,0,1.42,0L12,13.41l6.29,6.3a1,1,0,0,0,1.42,0,1,1,0,0,0,0-1.42Z"/></svg></button>
            </div>
        </div>
        <div class="search-form-container">
            <form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
                <label>
                    <span class="screen-reader-text"><?php _e('Search for:', 'travel-verdict'); ?></span>
                    <input type="search" class="search-field" placeholder="<?php esc_attr_e('Type to search...', 'travel-verdict'); ?>" value="<?php echo get_search_query(); ?>" name="s" autocomplete="off" />
                </label>
                <button type="submit" class="search-submit"><span class="screen-reader-text"><?php _e('Search', 'travel-verdict'); ?></span></button>
            </form>
            <div id="live-search-results" class="live-search-results"></div>
        </div>
    </header>

    <div id="mobile-menu-container" class="mobile-menu-container">
        <nav id="mobile-navigation" class="mobile-navigation">
            <?php wp_nav_menu(array('theme_location' => 'primary', 'menu_id' => 'mobile-primary-menu')); ?>
        </nav>
    </div>

    <?php if (!is_front_page()) { travel_verdict_get_breadcrumbs(); } ?>

    <main id="content" class="site-content">
