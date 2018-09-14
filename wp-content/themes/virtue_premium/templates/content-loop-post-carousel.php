<?php 
global $post, $kt_blog_carousel_loop, $virtue_premium;
if ($kt_blog_carousel_loop[ 'columns' ] == '2') {
	$itemsize = 'tcol-lg-6 tcol-md-6 tcol-sm-6 tcol-xs-12 tcol-ss-12';
	$slidewidth = 560;
	$slideheight = 560;
} else if ($kt_blog_carousel_loop[ 'columns' ] == '1') {
	$itemsize = 'tcol-lg-12 tcol-md-12 tcol-sm-12 tcol-xs-12 tcol-ss-12';
	$slidewidth = 560;
	$slideheight = 560;
} else if ($kt_blog_carousel_loop[ 'columns' ] == '3'){
	$itemsize = 'tcol-lg-4 tcol-md-4 tcol-sm-4 tcol-xs-6 tcol-ss-12';
	$slidewidth = 400;
	$slideheight = 400;
} else if ($kt_blog_carousel_loop[ 'columns' ] == '8'){
	$itemsize = 'tcol-lg-2 tcol-md-2 tcol-sm-3 tcol-xs-4 tcol-ss-6';
	$slidewidth = 200;
	$slideheight = 200;
} else if ($kt_blog_carousel_loop[ 'columns' ] == '6'){
	$itemsize = 'tcol-lg-2 tcol-md-2 tcol-sm-3 tcol-xs-4 tcol-ss-6';
	$slidewidth = 240;
	$slideheight = 240;
} else if ($kt_blog_carousel_loop[ 'columns' ] == '5'){
	$itemsize = 'tcol-lg-25 tcol-md-25 tcol-sm-3 tcol-xs-4 tcol-ss-6';
	$slidewidth = 240;
	$slideheight = 240;
} else {
	$itemsize = 'tcol-lg-3 tcol-md-3 tcol-sm-4 tcol-xs-6 tcol-ss-12';
	$slidewidth = 300;
	$slideheight = 300;
} 
$crop = true; 
if ( ! empty(  $kt_blog_carousel_loop[ 'imgheight' ] ) ) {
	$slideheight = $kt_blog_carousel_loop[ 'imgheight' ];
}
$slidewidth = apply_filters( 'kt_blog_carousel_image_width', $slidewidth );
$slideheight = apply_filters( 'kt_blog_carousel_image_height', $slideheight );
?>
<div class="<?php echo esc_attr( $itemsize );?> kad_product">
	<div <?php post_class('blog_item grid_item'); ?>>
		<div class="imghoverclass">
			<a href="<?php the_permalink()  ?>" title="<?php the_title_attribute(); ?>">
				<?php echo virtue_get_full_image_output( $slidewidth, $slideheight, true, 'iconhover', null, get_post_thumbnail_id(), true, true, true, null, true); ?>
			</a> 
		</div>
		<a href="<?php the_permalink() ?>" class="bcarousellink">
			<header>
				<?php
				/**
				* @hooked virtue_post_carousel_title - 10
				* @hooked virtue_post_carousel_date - 20
				*/
				do_action( 'kadence_post_carousel_small_excerpt_header' );
				?>
			</header>
			<div class="entry-content color_body">
				<p>
				<?php echo strip_tags( virtue_excerpt( 16 ) ); ?>
				</p>
			</div>
		</a>
	</div>
</div>