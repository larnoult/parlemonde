<?php

function favicon_link() {
    echo '<link rel="shortcut icon" type="image/x-icon" href="http://www.parlemonde.org/wp-content/themes/PLM-virtue/favicon.ico" />' . "\n";
}
add_action( 'wp_head', 'favicon_link' );

function my_custom_scripts() {
	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('jquery-ui-draggable');
	wp_enqueue_script('jquery-ui-position');
	wp_enqueue_script('jquery-ui-droppable');
	wp_enqueue_script( 'jquery-touch-punch' );
	wp_enqueue_script( 'jquery-effects-highlight' );

    }
add_action( 'wp_enqueue_scripts', 'my_custom_scripts' );

function bbp_enable_visual_editor( $args = array() ) {
    $args['tinymce'] = true;
    return $args;
}
add_filter( 'bbp_after_get_the_content_parse_args', 'bbp_enable_visual_editor' );

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


?>