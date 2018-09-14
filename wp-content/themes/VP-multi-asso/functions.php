<?php



function my_custom_scripts() {
	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('jquery-ui-draggable');
	wp_enqueue_script('jquery-ui-position');
	wp_enqueue_script('jquery-ui-droppable');
	wp_enqueue_script( 'jquery-touch-punch' );
	wp_enqueue_script( 'jquery-effects-highlight' );
	wp_enqueue_script( 'jquery.preload',get_theme_root_uri().'/scripts/oman-word.js' );
	wp_enqueue_script( 'jquery.preload.min',get_theme_root_uri().'/scripts/jquery.preload.min.js' );
	wp_enqueue_script( 'HP-dragNdrop3',get_theme_root_uri().'/scripts/HP-dragNdrop3.js' );
		if (  is_page(3410) || is_page(3497)) {
			wp_enqueue_script('sozi',get_theme_root_uri().'/scripts/sozi-pelico.js' );
	}
    }
add_action( 'wp_enqueue_scripts', 'my_custom_scripts' );



/*
function bbp_enable_visual_editor( $args = array() ) {
    $args['tinymce'] = true;
    return $args;
}
add_filter( 'bbp_after_get_the_content_parse_args', 'bbp_enable_visual_editor' );
*/
function my_login_stylesheet() {
    wp_enqueue_style( 'custom-login', get_stylesheet_directory_uri() . '/login/login-style.css' );
}
add_action( 'login_enqueue_scripts', 'my_login_stylesheet' );

function my_login_logo_url() {
return get_bloginfo( 'url' );
}
add_filter( 'login_headerurl', 'my_login_logo_url' );

function my_login_logo_url_title() {
return 'Your Site Name and Info';
}
add_filter( 'login_headertitle', 'my_login_logo_url_title' );


function add_news( $items, $args ) {
	$postsInCat = get_term_by('name','news','category');
	$postsInCat = $postsInCat->count;
	 if( $args->theme_location == 'primary' ){
		 $items .= '<li class="PLMune"><a href="http://www.parlemonde.org/a-la-une/"> <span id="Une-title">A la une ! </span><span id="Une-counter"> '. $postsInCat .' </span><span class="sf-description">Offres, évenements …</span></a></li>';
	}
     return $items;
}
add_filter( 'wp_nav_menu_items', 'add_news', 10, 2 );

/* Remove stuff from the admin bar */
function update_adminbar($wp_adminbar) {

  // remove unnecessary items
  $wp_adminbar->remove_node('my-account-activity');
  $wp_adminbar->remove_node('my-account-friends');
  $wp_adminbar->remove_node('my-account-groups');
  $wp_adminbar->remove_node('my-account-settings');
  $wp_adminbar->remove_node('my-account-xprofile');
  $wp_adminbar->remove_node('edit-profile');
  $wp_adminbar->remove_node('customize');
  $wp_adminbar->remove_node('kad_options');
  $wp_adminbar->remove_node('WPML_ALS');
  $wp_adminbar->remove_node('bar_opsd');
  $wp_adminbar->remove_node('jwl_links');



}
// admin_bar_menu hook
add_action('admin_bar_menu', 'update_adminbar', 999);


add_action('virtue_after_body', 'custom_add_topbar_after_body', 20);
function custom_add_topbar_after_body() {
    if ( !is_user_logged_in() ) {
	get_template_part('templates/header', 'topbar');
    } 
}


/*
 * Create a menu for Logged Out Users

function loggedout_menu( $meta = TRUE ) {
	global $wp_admin_bar;
		if ( is_user_logged_in() ) { return false; }
	$wp_admin_bar->add_menu( array(
		'id' => 'custom_menu',
		'title' => __( 'Menu Name' ),
		'href' => 'http://google.com/',

	'meta' 	=> array( target => '_blank' ) )
	);
}
add_action( 'admin_bar_menu', 'loggedout_menu', 15 );
?> */

/**
 * Register Sidebar
 */
function FB_register_sidebars() {
 
    /* Register the primary sidebar. */
    register_sidebar(
        array(
            'id' => 'sidebar-facebook',
            'name' => __( 'Sidebar Facebook', 'textdomain' ),
            'description' => __( 'Sidebar pour Facebook.', 'textdomain' ),
            'before_widget' => '<aside id="%1$s" class="widget %2$s">',
            'after_widget' => '</aside>',
            'before_title' => '<h3 class="widget-title">',
            'after_title' => '</h3>'
        )
    );
 
    /* Repeat register_sidebar() code for additional sidebars. */
}
add_action( 'widgets_init', 'FB_register_sidebars' );