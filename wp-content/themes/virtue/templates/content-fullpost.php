<?php
global $post, $virtue_sidebar; 
$headcontent = get_post_meta( $post->ID, '_kad_blog_head', true );
$height      = get_post_meta( $post->ID, '_kad_posthead_height', true ); 
$swidth      = get_post_meta( $post->ID, '_kad_posthead_width', true );
if ( !empty($height ) ) {
	$slideheight = $height; 
} else {
	$slideheight = 400;
}
if ( !empty($swidth ) ) {
	$slidewidth = $swidth; 
} else {
	if ( $virtue_sidebar ) {
		$slidewidth = 848;
	} else {
		$slidewidth =  140;
	}
} ?>
<article <?php post_class(); ?> itemscope="" itemtype="http://schema.org/BlogPosting">
	<?php 
	/**
	* @hooked virtue_single_post_headcontent - 10
	* @hooked virtue_single_post_meta_date - 20
	*/
	do_action( 'virtue_single_post_before_header' );
	?>
	<header>
		<a href="<?php the_permalink() ?>">
			<h1 class="entry-title" itemprop="name headline">
				<?php the_title(); ?>
			</h1>
		</a>
		<?php get_template_part('templates/entry', 'meta-subhead'); ?>
	</header>
	<div class="entry-content" itemprop="articleBody">
		<?php 
		global $more;

		$readmore =  __('Continued', 'virtue');
		the_content($readmore); ?>
	</div>
	<footer class="single-footer clearfix">
		<?php 
		/**
        * @hooked virtue_post_footer_tags - 20
        */
		do_action( 'kadence_single_loop_post_footer' );

		if ( comments_open() ) :
			echo '<p class="kad_comments_link">';
			comments_popup_link( 
				__( 'Leave a Reply', 'virtue' ), 
				__( '1 Comment', 'virtue' ), 
				__( '% Comments', 'virtue' ),
				'comments-link',
				__( 'Comments are Closed', 'virtue' )
			);
			echo '</p>';
		endif;
		?>
	</footer>
</article>

