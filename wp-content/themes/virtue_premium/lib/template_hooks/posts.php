<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
function kadence_post_featured_image_output($id, $width, $height) { 
	if (has_post_thumbnail( $id ) ) {
		$image_id = get_post_thumbnail_id( $id );
		$image_array = wp_get_attachment_image_src( $image_id, 'full' ); 
		$image_url = $image_array[0];
								
		$image = aq_resize($image_url, $width, $height, true);
		if(empty($image)) {$image = $image_url;}

		// Get srcset
		$img_srcset_output = kt_get_srcset_output($width, 270, $image_url, $image_id);

	} else {
		$image_url = virtue_post_default_placeholder();
		$image = aq_resize($image_url, $width, $height, true);
		if(empty($image)) {$image = $image_url;}
		$img_srcset_output = '';
	} ?>
	<img 
	src="<?php echo esc_url($image); ?>" 
	alt="<?php the_title_attribute(); ?>" 
	width="<?php echo esc_attr($width);?>"
	height="<?php echo esc_attr($height);?>" 
	<?php echo $img_srcset_output; ?>
	class="iconhover post-excerpt-image" 
	>
	<meta itemprop="url" content="<?php echo esc_url($image); ?>">
   	<meta itemprop="width" content="<?php echo esc_attr($width);?>">
    <meta itemprop="height" content="<?php echo esc_attr($height);?>">
	<?php
}

add_action( 'kadence_post_grid_small_excerpt_header', 'virtue_post_mini_excerpt_header_title', 10 );
add_action( 'kadence_post_mini_excerpt_header', 'virtue_post_mini_excerpt_header_title', 10 );
function virtue_post_mini_excerpt_header_title() {
	echo '<a href="'.get_the_permalink().'">';
    	echo '<h5 class="entry-title" itemprop="name headline">';
          		the_title();
    	echo '</h5>';
    echo '</a>';
}

add_action( 'kadence_post_mini_excerpt_header', 'virtue_post_meta_tooltip_subhead', 20 );
function virtue_post_meta_tooltip_subhead() {
get_template_part('templates/entry', 'meta-tooltip-subhead');
}

add_action( 'kadence_post_mini_excerpt_before_header', 'virtue_post_meta_date', 10 );
function virtue_post_meta_date() {
get_template_part('templates/entry', 'meta-date');
}


add_filter( 'kt_single_post_image_height', 'kt_post_header_single_image_height', 10 );
function kt_post_header_single_image_height() {
	global $virtue_premium;
	if(isset($virtue_premium['post_header_single_image_height']) && $virtue_premium['post_header_single_image_height'] == 1 ) {
		return null;
	} else {
		return 400;
	}
}

/* Single Post Layout */
add_action( 'kadence_single_post_begin', 'virtue_single_post_upper_headcontent', 10 );
function virtue_single_post_upper_headcontent() {
	get_template_part('templates/post', 'head-upper-content');
}

add_action( 'kadence_single_post_before_header', 'virtue_single_post_headcontent', 10 );
function virtue_single_post_headcontent() {
	get_template_part('templates/post', 'head-content');
}
add_action( 'kadence_post_excerpt_before_header', 'virtue_single_post_meta_date', 20 );
add_action( 'kadence_single_post_before_header', 'virtue_single_post_meta_date', 20 );
function virtue_single_post_meta_date() {
	get_template_part('templates/entry', 'meta-date');
}
add_action( 'kadence_single_post_header', 'virtue_post_header_breadcrumbs', 10 );
function virtue_post_header_breadcrumbs() {
	if(kadence_display_post_breadcrumbs()) { 
		kadence_breadcrumbs(); 
	}
}
add_action( 'kadence_single_post_header', 'virtue_post_header_title', 20 );
function virtue_post_header_title() {
	echo '<h1 class="entry-title" itemprop="name headline">';
	the_title();
	echo '</h1>';
}
add_action( 'kadence_post_excerpt_header', 'virtue_post_header_meta', 20 );
add_action( 'kadence_single_loop_post_header', 'virtue_post_header_meta', 30 );
add_action( 'kadence_single_post_header', 'virtue_post_header_meta', 30 );
function virtue_post_header_meta() {
	get_template_part('templates/entry', 'meta-subhead');
}

add_action( 'kadence_single_post_footer', 'virtue_post_footer_pagination', 10 );
function virtue_post_footer_pagination() {
	wp_link_pages(array('before' => '<nav class="pagination kt-pagination">', 'after' => '</nav>', 'link_before'=> '<span>','link_after'=> '</span>'));
}

add_action( 'kadence_post_grid_excerpt_footer', 'virtue_post_footer_tags', 10 );
add_action( 'kadence_post_excerpt_footer', 'virtue_post_footer_tags', 10 );
add_action( 'kadence_single_loop_post_footer', 'virtue_post_footer_tags', 20 );
add_action( 'kadence_single_post_footer', 'virtue_post_footer_tags', 20 );
function virtue_post_footer_tags() {
	$tags = get_the_tags();
	if ($tags) {  
		echo '<span class="posttags"><i class="icon-tag"></i>';
		the_tags('', ', ', '');
		echo '</span>';
	}
}
add_action( 'kadence_post_excerpt_footer', 'virtue_post_footer_meta', 30 );
add_action( 'kadence_post_mini_excerpt_footer', 'virtue_post_footer_meta', 30 );
add_action( 'kadence_post_carousel_small_excerpt_footer', 'virtue_post_footer_meta', 30 );
add_action( 'kadence_single_loop_post_footer', 'virtue_post_footer_meta', 30 );
add_action( 'kadence_single_post_footer', 'virtue_post_footer_meta', 30 );
function virtue_post_footer_meta() {
	get_template_part('templates/entry', 'meta-footer');
}

add_action( 'kadence_single_post_footer', 'virtue_post_nav', 40 );
function virtue_post_nav() {
 global $virtue_premium;
 if(isset($virtue_premium['show_postlinks']) &&  $virtue_premium['show_postlinks'] == 1) {
 	get_template_part('templates/entry', 'post-links');
 }
}

add_action( 'kadence_single_post_after', 'virtue_post_authorbox', 20 );
function virtue_post_authorbox() {
 global $virtue_premium, $post;
	 $authorbox = get_post_meta( $post->ID, '_kad_blog_author', true );
	 if(empty($authorbox) || $authorbox == 'default') { 
	 	if(isset($virtue_premium['post_author_default']) && ($virtue_premium['post_author_default'] == 'yes')) {
	 	 virtue_author_box(); 
	 	}
	 } else if($authorbox == 'yes'){ 
	 	virtue_author_box(); 
	 } 
}
add_action( 'kadence_single_post_after', 'virtue_post_bottom_carousel', 30 );
function virtue_post_bottom_carousel() {
 global $virtue_premium, $post;
	$blog_carousel_recent = get_post_meta( $post->ID, '_kad_blog_carousel_similar', true ); 
	      if ( empty( $blog_carousel_recent ) || $blog_carousel_recent == 'default' ) { 
	      	if( isset( $virtue_premium['post_carousel_default'] ) ) {
	      		$blog_carousel_recent = $virtue_premium['post_carousel_default']; 
	      	}
	      }
	      
	      if ($blog_carousel_recent == 'similar') { 
	      		get_template_part('templates/similarblog', 'carousel'); 
	      } else if($blog_carousel_recent == 'recent') {
	      		get_template_part('templates/recentblog', 'carousel');
	      } 
}

add_action( 'kadence_single_post_after', 'virtue_post_comments', 40 );
function virtue_post_comments() {
	comments_template('/templates/comments.php');
}



// POST GRID 


add_action( 'kadence_post_grid_excerpt_header', 'virtue_post_grid_excerpt_header_title', 10 );
function virtue_post_grid_excerpt_header_title() {
	echo '<a href="'.get_the_permalink().'">';
    	echo '<h4 class="entry-title" itemprop="name headline">';
          		the_title();
    	echo '</h4>';
    echo '</a>';
}

add_action( 'kadence_post_grid_small_excerpt_header', 'virtue_post_grid_header_meta', 20 );
add_action( 'kadence_post_grid_excerpt_header', 'virtue_post_grid_header_meta', 20 );
function virtue_post_grid_header_meta() {
	get_template_part('templates/entry', 'meta-grid-tooltip-subhead');
}

// Carousel

add_action( 'kadence_post_carousel_small_excerpt_header', 'virtue_post_carousel_title', 10 );
function virtue_post_carousel_title() {
	echo '<h5 class="entry-title" itemprop="name headline">';
		the_title();
	echo '</h5>';
}

add_action( 'kadence_post_carousel_small_excerpt_header', 'virtue_post_carousel_date', 20 );
function virtue_post_carousel_date() {
	echo '<div class="subhead">';
		echo '<span class="postday published kad-hidedate">'.get_the_date('j M Y').'</span>';
		echo '<meta itemprop="datePublished" content="'.esc_attr(get_the_modified_date('c')).'">';
	echo '</div>';
}

add_action( 'kadence_post_carousel_small_excerpt_footer', 'virtue_post_footer_meta_author', 30 );

/**
 * Post Meta Author
 */
function virtue_post_footer_meta_author() {
	echo '<span class="author vcard kt-hentry-hide" itemprop="author" content="'.get_the_author().'"><span class="fn">'.get_the_author().'</span></span>';
	echo '<span class="kt-hentry-hide updated">'.get_the_date().'</span>';
}
/**
 * Allow child to overide author box function.
 */
if ( ! function_exists( 'virtue_author_box' ) ) {
	/**
	 * Load Author Box
	 */
	function virtue_author_box() {
		get_template_part( 'templates/author', 'box' );
	}
}


