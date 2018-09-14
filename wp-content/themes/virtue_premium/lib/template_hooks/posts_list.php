<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 *  Post list Hooks
 */
add_action( 'kadence_post_excerpt_header', 'virtue_post_excerpt_header_title', 10 );
function virtue_post_excerpt_header_title() {
	echo '<a href="'.get_the_permalink().'">';
    	echo '<h3 class="entry-title" itemprop="name headline">';
          		the_title();
    	echo '</h3>';
    echo '</a>';
}

add_action( 'kadence_single_loop_post_header', 'virtue_post_full_loop_title', 20 );
function virtue_post_full_loop_title() {
	echo '<a href="'.get_the_permalink().'">';
    	echo '<h2 class="entry-title" itemprop="name headline">';
          		the_title();
    	echo '</h2>';
    echo '</a>';
}
