<?php

// =============================================================================
// Theme Setup
// =============================================================================
function travel_verdict_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'travel-verdict'),
        'footer'  => __('Footer Menu', 'travel-verdict')
    ));
    add_theme_support('wp-block-styles');
    add_theme_support('align-wide');
    update_option('image_default_link_type', 'file');
}
add_action('after_setup_theme', 'travel_verdict_setup');

// =============================================================================
// Enqueue Scripts and Styles
// =============================================================================
function travel_verdict_scripts() {
    wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&family=Playfair+Display:wght@700&display=swap', array(), null);
    wp_enqueue_style('main-style', get_template_directory_uri() . '/assets/css/main.css', array(), '1.10');
    wp_enqueue_style('print-style', get_template_directory_uri() . '/assets/css/print.css', array(), '1.0', 'print');
    wp_enqueue_script('main-js', get_template_directory_uri() . '/assets/js/main.js', array('jquery'), '1.8', true); // Version bump

    global $wp_query;
    wp_localize_script('main-js', 'tv_loadmore_params', array(
        'ajax_url'     => admin_url('admin-ajax.php'),
        'posts'        => json_encode($wp_query->query_vars),
        'current_page' => get_query_var('paged') ? get_query_var('paged') : 1,
        'max_page'     => $wp_query->max_num_pages
    ));

    wp_localize_script('main-js', 'tv_ajax', array(
        'ajax_url'     => admin_url('admin-ajax.php'),
        'like_nonce'   => wp_create_nonce('tv_like_nonce'),
        'search_nonce' => wp_create_nonce('tv_search_nonce')
    ));
}
add_action('wp_enqueue_scripts', 'travel_verdict_scripts');

// =============================================================================
// AJAX Handlers
// =============================================================================
function travel_verdict_load_more_posts() {
    $args = json_decode(stripslashes($_POST['query']), true);
    $args['paged'] = $_POST['page'] + 1;
    $args['post_status'] = 'publish';
    $query = new WP_Query($args);
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            get_template_part('template-parts/content', 'excerpt');
        }
    }
    wp_die();
}
add_action('wp_ajax_load_more_posts', 'travel_verdict_load_more_posts');
add_action('wp_ajax_nopriv_load_more_posts', 'travel_verdict_load_more_posts');

function travel_verdict_ajax_search() {
    check_ajax_referer('tv_search_nonce', 'nonce');
    $search_query = isset($_POST['query']) ? sanitize_text_field($_POST['query']) : '';
    $results = array();
    if (strlen($search_query) >= 3) {
        $query = new WP_Query(array('post_type' => 'post', 'post_status' => 'publish', 'posts_per_page' => 5, 's' => $search_query));
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $results[] = array('title' => get_the_title(), 'permalink' => get_permalink(), 'thumbnail' => has_post_thumbnail() ? get_the_post_thumbnail_url(get_the_ID(), 'thumbnail') : 'https://via.placeholder.com/150');
            }
        }
        wp_reset_postdata();
    }
    wp_send_json_success($results);
}
add_action('wp_ajax_travel_verdict_ajax_search', 'travel_verdict_ajax_search');
add_action('wp_ajax_nopriv_travel_verdict_ajax_search', 'travel_verdict_ajax_search');

function travel_verdict_like_post() {
    check_ajax_referer('tv_like_nonce', 'nonce');
    $post_id = intval($_POST['post_id']);
    $liked_posts = isset($_COOKIE['travel_verdict_liked_posts']) ? json_decode(stripslashes($_COOKIE['travel_verdict_liked_posts']), true) : array();
    if (in_array($post_id, $liked_posts)) {
        wp_send_json_error(array('message' => 'You have already liked this post.'));
        return;
    }
    $like_count = get_post_meta($post_id, 'likes', true);
    $like_count = empty($like_count) ? 1 : (int)$like_count + 1;
    update_post_meta($post_id, 'likes', $like_count);
    $liked_posts[] = $post_id;
    setcookie('travel_verdict_liked_posts', json_encode($liked_posts), time() + (365 * 24 * 60 * 60), '/');
    wp_send_json_success(array('likes' => $like_count));
}
add_action('wp_ajax_nopriv_travel_verdict_like_post', 'travel_verdict_like_post');
add_action('wp_ajax_travel_verdict_like_post', 'travel_verdict_like_post');

// =============================================================================
// Core Theme & Helper Functions
// =============================================================================
function travel_verdict_get_breadcrumbs() {
    if (is_front_page()) return;
    $crumbs = array('<a href="' . home_url() . '">Home</a>');
    if (is_category() || is_single()) {
        $categories = get_the_category();
        if (!empty($categories)) {
            $category = $categories[0];
            $crumbs[] = '<a href="' . get_category_link($category->term_id) . '">' . $category->name . '</a>';
        }
    }
    if (is_single() || is_page()) {
        $crumbs[] = get_the_title();
    } elseif (is_category()) {
        $crumbs[] = single_cat_title('', false);
    } elseif (is_tag()) {
        $crumbs[] = single_tag_title('', false);
    } elseif (is_search()) {
        $crumbs[] = 'Search Results for "' . get_search_query() . '"';
    } elseif (is_404()) {
        $crumbs[] = '404 Not Found';
    }
    echo '<div class="breadcrumbs-container"><nav aria-label="breadcrumb">' . implode('<span class="breadcrumb-separator"> / </span>', $crumbs) . '</nav></div>';
}

function travel_verdict_get_last_updated_date() {
    $published_time = get_the_time('U');
    $modified_time = get_the_modified_time('U');
    if ($modified_time >= $published_time + 86400) {
        return '<span class="last-updated-date">(Updated: ' . get_the_modified_date() . ')</span>';
    }
    return '';
}

function travel_verdict_article_schema() {
    if (!is_single()) return;
    global $post;
    $rating = get_post_meta($post->ID, '_tv_rating', true);
    $schema = array('@context' => 'https://schema.org', 'mainEntityOfPage' => array('@type' => 'WebPage', '@id' => get_permalink()), 'headline' => get_the_title(), 'datePublished' => get_the_date('c'), 'dateModified' => get_the_modified_date('c'), 'author' => array('@type' => 'Person', 'name' => get_the_author()), 'publisher' => array('@type' => 'Organization', 'name' => get_bloginfo('name'), 'logo' => array('@type' => 'ImageObject', 'url' => get_template_directory_uri() . '/assets/images/logo-light.png')), 'description' => get_the_excerpt());
    if (has_post_thumbnail()) {
        $schema['image'] = get_the_post_thumbnail_url($post->ID, 'full');
    }
    if (!empty($rating) && $rating > 0) {
        $schema['@type'] = 'Review';
        $schema['reviewRating'] = array('@type' => 'Rating', 'ratingValue' => $rating, 'bestRating' => '5', 'worstRating' => '1');
        $schema['itemReviewed'] = array('@type' => 'Thing', 'name' => get_the_title());
    } else {
        $schema['@type'] = 'Article';
    }
    echo '<script type="application/ld+json">' . json_encode($schema) . '</script>';
}

function travel_verdict_highlight_search_term($text) {
    if (!is_search() || !in_the_loop() || !is_main_query()) return $text;
    $query = get_search_query();
    if (empty($query)) return $text;
    return preg_replace('/(' . preg_quote($query, '/') . ')/i', '<mark class="search-highlight">$1</mark>', $text);
}
add_filter('the_title', 'travel_verdict_highlight_search_term');
add_filter('get_the_excerpt', 'travel_verdict_highlight_search_term');

function travel_verdict_reading_time($post_id) {
    $content = get_post_field('post_content', $post_id);
    $word_count = str_word_count(strip_tags($content));
    $reading_time = ceil($word_count / 200);
    return $reading_time . ' min read';
}

function travel_verdict_get_post_views($post_id) {
    $count_key = 'post_views_count';
    $count = get_post_meta($post_id, $count_key, true);
    if ($count == '') {
        delete_post_meta($post_id, $count_key);
        add_post_meta($post_id, $count_key, '0');
        return "0 Views";
    }
    return number_format($count) . ' Views';
}

function travel_verdict_set_post_views() {
    if (!is_single() || is_user_logged_in()) return;
    $post_id = get_the_ID();
    $count_key = 'post_views_count';
    $count = (int) get_post_meta($post_id, $count_key, true);
    $count++;
    update_post_meta($post_id, $count_key, $count);
}
add_action('wp_head', 'travel_verdict_set_post_views');

function travel_verdict_get_likes_count($post_id) {
    $like_count = get_post_meta($post_id, 'likes', true);
    return empty($like_count) ? '0' : number_format($like_count);
}

function travel_verdict_get_post_terms($post_id) {
    $categories = get_the_category($post_id);
    $tags = get_the_tags($post_id);
    $output = '';
    if (!empty($categories)) {
        $output .= '<div class="post-terms-list post-categories">';
        foreach ($categories as $category) {
            $output .= '<a href="' . esc_url(get_category_link($category->term_id)) . '" class="term-link category-link">' . esc_html($category->name) . '</a>';
        }
        $output .= '</div>';
    }
    if (!empty($tags)) {
        $output .= '<div class="post-terms-list post-tags">';
        foreach ($tags as $tag) {
            $output .= '<a href="' . esc_url(get_tag_link($tag->term_id)) . '" class="term-link tag-link">#' . esc_html($tag->name) . '</a>';
        }
        $output .= '</div>';
    }
    return $output;
}

// =============================================================================
// Comments
// =============================================================================
function travel_verdict_comment_reply_notification($comment_ID, $comment_approved, $commentdata) {
    if ($comment_approved == 1 && !empty($commentdata['comment_parent'])) {
        $parent_comment = get_comment($commentdata['comment_parent']);
        if ($parent_comment && $parent_comment->comment_author_email != $commentdata['comment_author_email']) {
            $parent_email = $parent_comment->comment_author_email;
            $post = get_post($commentdata['comment_post_ID']);
            $subject = 'Someone replied to your comment on "' . $post->post_title . '"';
            $message = 'Hi ' . $parent_comment->comment_author . ",\n\n";
            $message .= $commentdata['comment_author'] . ' has replied to your comment on the post "' . $post->post_title . '".' . "\n\n";
            $message .= 'You can see the reply here: ' . get_comment_link($comment_ID) . "\n\n";
            $message .= "Thank you,\n";
            $message .= get_bloginfo('name') . "\n";
            $headers = array('Content-Type: text/plain; charset=UTF-8');
            wp_mail($parent_email, $subject, $message, $headers);
        }
    }
}
add_action('comment_post', 'travel_verdict_comment_reply_notification', 10, 3);

// =============================================================================
// Table of Contents
// =============================================================================
function travel_verdict_generate_toc($content) {
    if (is_single() && in_the_loop() && is_main_query()) {
        $headings = [];
        preg_match_all('/<h([2-3]).*?id="([^"]+)".*?>(.*?)<\/h[2-3]>/i', $content, $matches, PREG_SET_ORDER);

        if (empty($matches)) {
            // If no IDs are present, run a preliminary pass to add them
            $i = 1;
            $content = preg_replace_callback('/<h([2-3])(.*?)>(.*?)<\/h[2-3]>/i', function($matches) use (&$i) {
                $id = 'toc-' . $i++;
                return '<h' . $matches[1] . ' id="' . $id . '"' . $matches[2] . '>' . $matches[3] . '</h' . $matches[1] . '>';
            }, $content);
            // Now, re-run the match to capture the newly added IDs
            preg_match_all('/<h([2-3]).*?id="([^"]+)".*?>(.*?)<\/h[2-3]>/i', $content, $matches, PREG_SET_ORDER);
        }
        
        if (count($matches) < 3) {
            return $content;
        }

        $toc = '<div id="table-of-contents" class="table-of-contents"><p class="toc-title">Table of Contents</p><ul class="toc-list">';
        foreach ($matches as $match) {
            $level = $match[1];
            $id = $match[2];
            $title = strip_tags($match[3]);
            $toc .= '<li class="toc-item toc-level-' . $level . '"><a href="#' . $id . '" class="toc-link">' . $title . '</a></li>';
        }
        $toc .= '</ul></div>';

        return $toc . $content;
    }
    return $content;
}
add_filter('the_content', 'travel_verdict_generate_toc', 100);

// =============================================================================
// Admin Columns
// =============================================================================
function travel_verdict_add_reading_time_column($columns) {
    $new_columns = array();
    foreach ($columns as $key => $value) {
        $new_columns[$key] = $value;
        if ($key === 'title') {
            $new_columns['reading_time'] = __('Reading Time', 'travel-verdict');
        }
    }
    return $new_columns;
}
add_filter('manage_posts_columns', 'travel_verdict_add_reading_time_column');

function travel_verdict_reading_time_column_content($column, $post_id) {
    if ($column === 'reading_time') {
        echo esc_html(travel_verdict_reading_time($post_id));
    }
}
add_action('manage_posts_custom_column', 'travel_verdict_reading_time_column_content', 10, 2);

// =============================================================================
// Widgets & Customizer
// =============================================================================
function travel_verdict_widgets_init() {
    register_sidebar(array(
        'name'          => esc_html__('Footer Widgets', 'travel-verdict'),
        'id'            => 'footer-widgets',
        'description'   => esc_html__('Add widgets here to appear in your footer.', 'travel-verdict'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h4 class="widget-title">',
        'after_title'   => '</h4>',
    ));
}
add_action('widgets_init', 'travel_verdict_widgets_init');

class Travel_Verdict_Popular_Posts_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct('tv_popular_posts', 'Travel Verdict: Popular Posts', array('description' => 'Displays a list of the most viewed posts.'));
    }
    public function widget($args, $instance) {
        echo $args['before_widget'];
        $title = apply_filters('widget_title', $instance['title']);
        if (!empty($title)) {
            echo $args['before_title'] . $title . $args['after_title'];
        }
        $post_count = !empty($instance['post_count']) ? $instance['post_count'] : 5;
        $popular_posts_query = new WP_Query(array('posts_per_page' => $post_count, 'meta_key' => 'post_views_count', 'orderby' => 'meta_value_num', 'order' => 'DESC', 'ignore_sticky_posts' => 1));
        if ($popular_posts_query->have_posts()) {
            echo '<ul>';
            while ($popular_posts_query->have_posts()) {
                $popular_posts_query->the_post();
                echo '<li class="popular-post-item"><a href="' . get_permalink() . '">';
                if (has_post_thumbnail()) {
                    the_post_thumbnail('thumbnail');
                }
                echo '<span>' . get_the_title() . '</span></a></li>';
            }
            echo '</ul>';
        }
        wp_reset_postdata();
        echo $args['after_widget'];
    }
    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : 'Popular Posts';
        $post_count = !empty($instance['post_count']) ? $instance['post_count'] : 5;
        ?>
        <p><label for="<?php echo $this->get_field_id('title'); ?>">Title:</label><input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>"></p>
        <p><label for="<?php echo $this->get_field_id('post_count'); ?>">Number of posts to show:</label><input class="tiny-text" id="<?php echo $this->get_field_id('post_count'); ?>" name="<?php echo $this->get_field_name('post_count'); ?>" type="number" step="1" min="1" value="<?php echo esc_attr($post_count); ?>" size="3"></p>
        <?php
    }
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['post_count'] = (!empty($new_instance['post_count'])) ? absint($new_instance['post_count']) : 5;
        return $instance;
    }
}
function travel_verdict_register_widgets() {
    register_widget('Travel_Verdict_Popular_Posts_Widget');
}
add_action('widgets_init', 'travel_verdict_register_widgets');

function travel_verdict_customize_register($wp_customize) {
    $wp_customize->add_section('travel_verdict_logos', array('title' => __('Theme Logos', 'travel-verdict'), 'priority' => 25));
    $wp_customize->add_setting('logo_light', array('default' => get_template_directory_uri() . '/assets/images/logo-light.png', 'sanitize_callback' => 'esc_url_raw'));
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'logo_light_control', array('label' => 'Light Mode Logo', 'section' => 'travel_verdict_logos', 'settings' => 'logo_light')));
    $wp_customize->add_setting('logo_dark', array('default' => get_template_directory_uri() . '/assets/images/logo-dark.png', 'sanitize_callback' => 'esc_url_raw'));
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'logo_dark_control', array('label' => 'Dark Mode Logo', 'section' => 'travel_verdict_logos', 'settings' => 'logo_dark')));
    
    $wp_customize->add_section('travel_verdict_instagram', array('title' => __('Instagram Feed', 'travel-verdict'), 'priority' => 130));
    for ($i = 1; $i <= 6; $i++) {
        $wp_customize->add_setting('instagram_post_link_' . $i, array('default' => '', 'sanitize_callback' => 'esc_url_raw'));
        $wp_customize->add_control('instagram_post_link_' . $i, array('label' => __('Post Link ', 'travel-verdict') . $i, 'section' => 'travel_verdict_instagram', 'type' => 'url'));
        $wp_customize->add_setting('instagram_image_url_' . $i, array('default' => '', 'sanitize_callback' => 'esc_url_raw'));
        $wp_customize->add_control('instagram_image_url_' . $i, array('label' => __('Image URL ', 'travel-verdict') . $i, 'section' => 'travel_verdict_instagram', 'type' => 'url', 'description' => '---'));
    }
    $wp_customize->add_section('travel_verdict_social', array('title' => __('Social Media Links', 'travel-verdict'), 'priority' => 140));
    $social_networks = ['facebook', 'twitter', 'instagram', 'youtube', 'pinterest', 'linkedin'];
    foreach ($social_networks as $network) {
        $wp_customize->add_setting('social_link_' . $network, array('default' => '', 'sanitize_callback' => 'esc_url_raw'));
        $wp_customize->add_control('social_link_' . $network, array('label' => ucfirst($network) . ' URL', 'section' => 'travel_verdict_social', 'type' => 'url'));
    }
    $wp_customize->add_section('travel_verdict_copyright', array('title' => __('Footer Copyright', 'travel-verdict'), 'priority' => 150));
    $wp_customize->add_setting('copyright_text', array('default' => 'All Rights Reserved.', 'sanitize_callback' => 'sanitize_text_field', 'transport' => 'postMessage'));
    $wp_customize->add_control('copyright_text_control', array('label' => __('Copyright Text', 'travel-verdict'), 'section' => 'travel_verdict_copyright', 'settings' => 'copyright_text', 'type' => 'textarea'));
}
add_action('customize_register', 'travel_verdict_customize_register');

function travel_verdict_customize_preview_js() {
    wp_enqueue_script('travel-verdict-customizer', get_template_directory_uri() . '/assets/js/customizer.js', array('jquery', 'customize-preview'), '', true);
}
add_action('customize_preview_init', 'travel_verdict_customize_preview_js');

// =============================================================================
// Meta Boxes
// =============================================================================
function travel_verdict_add_map_meta_box() { add_meta_box('tv-map-meta-box', 'Map Embed Code', 'travel_verdict_map_meta_box_html', 'post', 'normal', 'high'); }
add_action('add_meta_boxes', 'travel_verdict_add_map_meta_box');
function travel_verdict_map_meta_box_html($post) { $value = get_post_meta($post->ID, '_tv_map_embed', true); wp_nonce_field('travel_verdict_map_nonce', 'map_nonce');?><p>Go to Google Maps, find a location, click "Share", then "Embed a map", and copy the HTML code here.</p><textarea name="tv_map_embed" id="tv_map_embed" style="width:100%;min-height:100px;"><?php echo esc_textarea($value); ?></textarea><?php }
function travel_verdict_save_map_meta($post_id) { if(!isset($_POST['map_nonce'])||!wp_verify_nonce($_POST['map_nonce'],'travel_verdict_map_nonce'))return;if(defined('DOING_AUTOSAVE')&&DOING_AUTOSAVE)return;if(!current_user_can('edit_post',$post_id))return;if(isset($_POST['tv_map_embed'])){$allowed_html=array('iframe'=>array('src'=>true,'width'=>true,'height'=>true,'style'=>true,'frameborder'=>true,'allowfullscreen'=>true,'loading'=>true,'referrerpolicy'=>true,));update_post_meta($post_id,'_tv_map_embed',wp_kses($_POST['tv_map_embed'],$allowed_html));}}
add_action('save_post', 'travel_verdict_save_map_meta');

function travel_verdict_add_rating_meta_box() { add_meta_box('tv-rating-meta-box', 'Verdict Rating', 'travel_verdict_rating_meta_box_html', 'post', 'side', 'default'); }
add_action('add_meta_boxes', 'travel_verdict_add_rating_meta_box');
function travel_verdict_rating_meta_box_html($post) { $value = get_post_meta($post->ID, '_tv_rating', true);wp_nonce_field('travel_verdict_rating_nonce', 'rating_nonce');?><p>Select a star rating (0 for none).</p><div class="rating-stars"><?php for ($i=5;$i>=0;$i--):?><input type="radio" id="star<?php echo $i;?>" name="tv_rating" value="<?php echo $i;?>" <?php checked($value,$i);?>><label for="star<?php echo $i;?>">★</label><?php endfor;?></div><style>#tv-rating-meta-box .rating-stars{display:flex;flex-direction:row-reverse;justify-content:flex-end;}#tv-rating-meta-box .rating-stars input[type="radio"]{display:none;}#tv-rating-meta-box .rating-stars label{font-size:24px;color:#ccc;cursor:pointer;transition:color .2s;}#tv-rating-meta-box .rating-stars input[type="radio"]:checked ~ label{color:#ffc700;}#tv-rating-meta-box .rating-stars label:hover,#tv-rating-meta-box .rating-stars label:hover ~ label{color:#ffc700;}</style><?php }
function travel_verdict_save_rating_meta($post_id) { if(!isset($_POST['rating_nonce'])||!wp_verify_nonce($_POST['rating_nonce'],'travel_verdict_rating_nonce'))return;if(defined('DOING_AUTOSAVE')&&DOING_AUTOSAVE)return;if(!current_user_can('edit_post',$post_id))return;if(isset($_POST['tv_rating'])){update_post_meta($post_id,'_tv_rating',sanitize_text_field($_POST['tv_rating']));}}
add_action('save_post','travel_verdict_save_rating_meta');
function travel_verdict_display_star_rating($post_id){$rating=get_post_meta($post_id,'_tv_rating',true);if(empty($rating)||$rating==0)return;$output='<span class="star-rating">';for($i=1;$i<=5;$i++){$output.=($i<=$rating)?'<span class="star filled">★</span>':'<span class="star empty">★</span>';}$output.='</span>';return $output;}

function travel_verdict_add_location_meta_box() { add_meta_box('tv-location-meta-box','Location','travel_verdict_location_meta_box_html','post','side','default');}
add_action('add_meta_boxes', 'travel_verdict_add_location_meta_box');
function travel_verdict_location_meta_box_html($post){$value=get_post_meta($post->ID,'_tv_location',true);wp_nonce_field('travel_verdict_location_nonce','location_nonce');?><p>Enter a location (e.g., "Paris, France").</p><input type="text" name="tv_location" id="tv_location" value="<?php echo esc_attr($value);?>" style="width:100%;"><?php }
function travel_verdict_save_location_meta($post_id){if(!isset($_POST['location_nonce'])||!wp_verify_nonce($_POST['location_nonce'],'travel_verdict_location_nonce'))return;if(defined('DOING_AUTOSAVE')&&DOING_AUTOSAVE)return;if(!current_user_can('edit_post',$post_id))return;if(isset($_POST['tv_location'])){update_post_meta($post_id,'_tv_location',sanitize_text_field($_POST['tv_location']));}}
add_action('save_post','travel_verdict_save_location_meta');
?>
