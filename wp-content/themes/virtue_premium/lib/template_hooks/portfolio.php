<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
function virtue_portfolio_nav() { 
	 wp_link_pages(array('before' => '<nav class="pagination kt-pagination">', 'after' => '</nav>', 'link_before'=> '<span>','link_after'=> '</span>'));
}
add_action( 'kadence_single_portfolio_footer', 'virtue_portfolio_nav', 10 );
function virtue_portfolio_bottom_carousel() { 
	global $post; 
	$portfolio_carousel = get_post_meta( $post->ID, '_kad_portfolio_carousel_recent', true ); 
	if ($portfolio_carousel != 'no') { 
		get_template_part('templates/bottomportfolio', 'carousel'); 
	}
}

add_action( 'kadence_single_portfolio_after', 'virtue_portfolio_bottom_carousel', 30 );
function virtue_portfolio_comments() { 
	global $virtue_premium; 
	if(isset($virtue_premium['portfolio_comments']) && $virtue_premium['portfolio_comments'] == 1) { 
    	comments_template('/templates/comments.php'); 
		} 
}

add_action( 'kadence_single_portfolio_after', 'virtue_portfolio_comments', 40 );

add_filter( 'kt_single_portfolio_image_height', 'kt_portfolio_single_image_height', 10 );
function kt_portfolio_single_image_height() {
	global $virtue_premium;
	if(isset($virtue_premium['portfolio_header_single_image_height']) && $virtue_premium['portfolio_header_single_image_height'] == 1 ) {
		return null;
	} else {
		return 450;
	}
}
