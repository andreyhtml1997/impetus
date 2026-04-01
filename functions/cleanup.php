<?php

add_action( 'init', 'e_cleanup_head' );
function e_cleanup_head() {
    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'index_rel_link');
    remove_action('wp_head', 'feed_links', 2);
    remove_action('wp_head', 'feed_links_extra', 3);
	remove_action('wp_head', 'start_post_rel_link', 10, 0 );
	remove_action('wp_head', 'parent_post_rel_link', 10, 0 );
	remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );
    remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
    remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
}

add_filter( 'login_errors', 'show_less_login_info' );
function show_less_login_info() {
   return "<strong>ERROR</strong>: ????";
}

add_action( 'wp_print_styles', 'wps_deregister_styles', 100 );
function wps_deregister_styles() {
    wp_deregister_style('wp-block-library-theme');
    wp_deregister_style('wp-block-library');
}

add_action( 'pre_ping', 'wpsites_disable_self_pingbacks' );
function wpsites_disable_self_pingbacks( &$links ) {
  foreach ( $links as $l => $link )
        if ( 0 === strpos( $link, get_option( 'home' ) ) )
            unset($links[$l]);
}

add_action( 'init', 'disable_embeds_code_init', 9999 );
function disable_embeds_code_init() {
    
	remove_action( 'rest_api_init', 'wp_oembed_register_route' );
    add_filter( 'embed_oembed_discover', '__return_false' );
    remove_filter( 'oembed_dataparse', 'wp_filter_oembed_result', 10 );
    remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
    remove_action( 'wp_head', 'wp_oembed_add_host_js' );
    add_filter( 'tiny_mce_plugins', 'disable_embeds_tiny_mce_plugin' );
    add_filter( 'rewrite_rules_array', 'disable_embeds_rewrites' );
    remove_filter( 'pre_oembed_result', 'wp_filter_pre_oembed_result', 10 );
}

function disable_embeds_tiny_mce_plugin( $plugins ) {
    return array_diff( $plugins, array( 'wpembed' ) );
}

function disable_embeds_rewrites( $rules ) {
    foreach ( $rules as $rule => $rewrite ) {
        if ( false !== strpos($rewrite, 'embed=true') )
            unset($rules[$rule]);
    }

    return $rules;
}

remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );

add_action( 'after_theme_support', 'remove_feed' );
function remove_feed() {
   remove_theme_support( 'automatic-feed-links' );
}

add_action( 'admin_menu', 'my_remove_admin_menus' );
function my_remove_admin_menus() {
    remove_menu_page( 'edit-comments.php' );
}

add_action('init', 'remove_comment_support', 100);
function remove_comment_support() {
    remove_post_type_support( 'post', 'comments' );
    remove_post_type_support( 'page', 'comments' );
}

add_action( 'wp_before_admin_bar_render', 'mytheme_admin_bar_render' );
function mytheme_admin_bar_render() {
    
	global $wp_admin_bar;
    
	$wp_admin_bar->remove_menu('comments');
}

add_action( 'admin_init', 'remove_welcome_panel' );
function remove_welcome_panel() {
	remove_action( 'welcome_panel', 'wp_welcome_panel' );
}

add_action('admin_head', 'my_custom_fonts');
function my_custom_fonts() {
	echo '<style>
		.acf-clone-fields .acf-field-group > .acf-label{
			display: none !important;
		} 
	</style>';
}

// Remove support for post labels (tags and categories)
add_action('init', 'remove_post_taxonomies_support');
function remove_post_taxonomies_support() {
    // Remove tags support
    unregister_taxonomy_for_object_type('post_tag', 'post');
    // Remove categories support
    unregister_taxonomy_for_object_type('category', 'post');
}

// Hide tag and category metaboxes in post editor
add_action('admin_menu', 'remove_post_taxonomies_metabox');
function remove_post_taxonomies_metabox() {
    // Hide tag metabox
    remove_meta_box('tagsdiv-post_tag', 'post', 'side');
    // Hide category metabox
    remove_meta_box('categorydiv', 'post', 'side');
}

// Remove labels from the admin menu (tags and categories)
add_action('admin_menu', 'remove_post_taxonomies_menu');
function remove_post_taxonomies_menu() {
    // Remove tags from the menu
    remove_submenu_page('edit.php', 'edit-tags.php?taxonomy=post_tag');
    // Remove categories from the menu
    remove_submenu_page('edit.php', 'edit-tags.php?taxonomy=category');
}

// Remove REST API endpoints for labels (tags and categories)
add_filter('rest_endpoints', 'disable_post_taxonomies_rest_api');
function disable_post_taxonomies_rest_api($endpoints) {
    // Remove tags REST API endpoint
    if (isset($endpoints['/wp/v2/tags'])) {
        unset($endpoints['/wp/v2/tags']);
    }
    // Remove categories REST API endpoint
    if (isset($endpoints['/wp/v2/categories'])) {
        unset($endpoints['/wp/v2/categories']);
    }
    return $endpoints;
}

// Remove tags and categories from widgets and archive pages
add_action('widgets_init', 'remove_post_taxonomies_widget_archives');
function remove_post_taxonomies_widget_archives() {
    // Remove tag cloud widget
    unregister_widget('WP_Widget_Tag_Cloud');
    // Remove RSS widget
    unregister_widget('WP_Widget_RSS');
    // Optionally, remove category-related widgets as well
    unregister_widget('WP_Widget_Categories');
}

// Redirect tag and category archives
add_action('template_redirect', 'redirect_post_taxonomies_archives');
function redirect_post_taxonomies_archives() {
    // Redirect tag archives
    if (is_tag()||is_category()) {
        wp_redirect(home_url());
        exit();
    }
}

// Remove tags and categories from the menu editor
add_action('admin_head-nav-menus.php', 'remove_menu_page_taxonomy_metaboxes');
function remove_menu_page_taxonomy_metaboxes() {
    // Remove tag metabox from menus
    remove_meta_box('add-post_tag', 'nav-menus', 'side');
    // Remove category metabox from menus
    remove_meta_box('add-category', 'nav-menus', 'side');
}

