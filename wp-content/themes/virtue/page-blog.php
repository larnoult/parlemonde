<?php
/*
Template Name: Blog
*/

global $post, $virtue_sidebar;

if( virtue_display_sidebar() ) {
	$virtue_sidebar = true;
	$fullclass 		 = '';
} else {
	$virtue_sidebar = false;
	$fullclass = 'fullwidth';
}
if( get_post_meta( $post->ID, '_kad_blog_summery', true ) == 'full' ) {
	$summery 	= 'full';
	$postclass 	= "single-article fullpost";
} else {
	$summery 	= 'normal';
	$postclass  = 'postlist';
}
$blog_category 	= get_post_meta( $post->ID, '_kad_blog_cat', true );
$blog_items 	= get_post_meta( $post->ID, '_kad_blog_items', true ); 
if( $blog_category == '-1' || $blog_category == '' ) {
	$blog_cat_slug = '';
} else {
	$blog_cat = get_term_by ( 'id',$blog_category,'category' );
	$blog_cat_slug = $blog_cat -> slug;
}
if( $blog_items == 'all' ) {
	$blog_items = '-1';
} 
	/**
	* @hooked virtue_page_title - 20
	*/
	do_action( 'virtue_page_title_container' );
	?>
	
	<div id="content" class="container <?php echo esc_attr( virtue_container_class() ); ?>">
		<div class="row">
			<div class="main <?php echo esc_attr( virtue_main_class() );?> <?php echo esc_attr( $postclass ) .' '. esc_attr( $fullclass ); ?>" role="main">
				<?php 	
				$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
				$temp = $wp_query; 
				$wp_query = null; 
				$wp_query = new WP_Query();
				$wp_query->query( array(
					'paged' 		 => $paged,
					'category_name'	 => $blog_cat_slug,
					'posts_per_page' => $blog_items
					)
				);

				if ( $wp_query ) : 
					while ( $wp_query->have_posts() ) : $wp_query->the_post();
				 		if( $summery == 'full' ) {
								get_template_part( 'templates/content', 'fullpost' ); 
						} else {
								get_template_part( 'templates/content', get_post_format() ); 
						}
					endwhile; 
				else: ?>
					<div class="error-not-found"><?php esc_html_e( 'Sorry, no blog entries found.', 'virtue' ); ?></div>
				<?php endif; 

				/**
				* @hooked virtue_pagination - 10
				*/
				do_action( 'virtue_pagination' );

				$wp_query = $temp;  // Reset 
				wp_reset_postdata();

				/**
                * @hooked virtue_page_comments - 20
                */
				do_action( 'virtue_page_footer' );
				?>
			</div><!-- /.main -->