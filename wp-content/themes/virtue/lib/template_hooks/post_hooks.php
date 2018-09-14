<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}



add_action( 'virtue_single_post_header', 'virtue_post_header_title', 20 );
function virtue_post_header_title() {
    echo '<h1 class="entry-title" itemprop="name headline">';
    the_title();
    echo '</h1>';
}

add_action( 'virtue_single_post_header', 'virtue_post_header_meta', 30 );
function virtue_post_header_meta() {
    get_template_part( 'templates/entry', 'meta-subhead' );
}

add_action( 'virtue_single_post_footer', 'virtue_post_footer_pagination', 10 );
function virtue_post_footer_pagination() {
    wp_link_pages(array('before' => '<nav class="pagination kt-pagination">', 'after' => '</nav>', 'link_before'=> '<span>','link_after'=> '</span>'));
}

add_action( 'kadence_single_loop_post_footer', 'virtue_post_footer_tags', 20 );
add_action( 'virtue_single_post_footer', 'virtue_post_footer_tags', 20 );
function virtue_post_footer_tags() {
  $tags = get_the_tags();
  if ($tags) {  
    echo '<span class="posttags"><i class="icon-tag"></i>';
    the_tags('', ', ', '');
    echo '</span>';
  }
}

add_action( 'virtue_single_post_footer', 'virtue_post_nav', 40 );
function virtue_post_nav() {
    global $virtue;
    if(isset($virtue['show_postlinks']) &&  $virtue['show_postlinks'] == 1) {
        get_template_part('templates/entry', 'post-links'); 
    }
}

add_action( 'virtue_single_post_after', 'virtue_post_authorbox', 20 );
function virtue_post_authorbox() {
 global $virtue, $post;
   $authorbox = get_post_meta( $post->ID, '_kad_blog_author', true );
    if(empty($authorbox) || $authorbox == 'default') {
        if(isset($virtue['post_author_default']) && ($virtue['post_author_default'] == 'yes')) {
            virtue_author_box(); 
        }
    } else if($authorbox == 'yes'){ 
        virtue_author_box(); 
    } 
}
add_action( 'virtue_single_post_after', 'virtue_post_bottom_carousel', 30 );
function virtue_post_bottom_carousel() {
 global $virtue, $post;
  $blog_carousel_recent = get_post_meta( $post->ID, '_kad_blog_carousel_similar', true ); 
    if(empty($blog_carousel_recent) || $blog_carousel_recent == 'default' ) { 
        if(isset($virtue['post_carousel_default'])) {
            $blog_carousel_recent = $virtue['post_carousel_default']; 
        } 
    }
    if ($blog_carousel_recent == 'similar') { 
        get_template_part('templates/similarblog', 'carousel');
    } else if($blog_carousel_recent == 'recent') {
        get_template_part('templates/recentblog', 'carousel');
    } 
}

add_action( 'virtue_single_post_after', 'virtue_post_comments', 40 );
function virtue_post_comments() {
  comments_template('/templates/comments.php');
}
add_action( 'kadence_post_excerpt_footer', 'virtue_post_footer_meta', 30 );
add_action( 'kadence_post_mini_excerpt_footer', 'virtue_post_footer_meta', 30 );
add_action( 'kadence_post_carousel_small_excerpt_footer', 'virtue_post_footer_meta', 30 );
add_action( 'kadence_single_loop_post_footer', 'virtue_post_footer_meta', 20 );
add_action( 'virtue_single_post_footer', 'virtue_post_footer_meta', 30 );
function virtue_post_footer_meta() {
    get_template_part('templates/entry', 'meta-footer');
}
add_action( 'kadence_post_carousel_small_excerpt_footer', 'virtue_post_footer_meta_author', 30 );
function virtue_post_footer_meta_author() {
	echo '<span class="author vcard kt-hentry-hide" itemprop="author" content="'.get_the_author().'"><span class="fn">'.get_the_author().'</span></span>';
	echo '<span class="kt-hentry-hide updated">'.get_the_date().'</span>';
}

/* 
* Single Post Layout 
*/
add_action( 'virtue_single_post_before_header', 'virtue_single_post_headcontent', 10 );
function virtue_single_post_headcontent() {
	get_template_part( 'templates/post', 'head-content' );
}
add_action( 'kadence_post_excerpt_before_header', 'virtue_single_post_meta_date', 20 );
add_action( 'virtue_single_post_before_header', 'virtue_single_post_meta_date', 20 );
function virtue_single_post_meta_date() {
    get_template_part('templates/post', 'date'); 
}