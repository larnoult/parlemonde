<?php

/* Modification du header */

add_action('virtue_after_body', 'custom_add_topbar_after_body', 20);
function custom_add_topbar_after_body() {
    if ( !is_user_logged_in() ) {
	get_template_part('templates/header', 'topbar');
    } 
}

function my_custom_scripts() {
	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('jquery-ui-draggable');
	wp_enqueue_script('jquery-ui-position');
	wp_enqueue_script('jquery-ui-droppable');
	wp_enqueue_script( 'jquery-touch-punch' );
	wp_enqueue_script( 'jquery-effects-highlight' );
	wp_enqueue_script( 'oman-word',get_theme_root_uri().'/scripts/scripts-fr-visa/oman-word.js' );
	wp_enqueue_script( 'ressource2',get_theme_root_uri().'/scripts/scripts-fr-visa/ressource2.js' );
	wp_enqueue_script( 'enfant-correspondance',get_theme_root_uri().'/scripts/scripts-fr-visa/enfant-correspondance.js' );
	wp_enqueue_script( 'jquery.preload',get_theme_root_uri().'/scripts/scripts-fr-visa/jquery.preload.js' );
    }

add_action( 'wp_enqueue_scripts', 'my_custom_scripts' );
