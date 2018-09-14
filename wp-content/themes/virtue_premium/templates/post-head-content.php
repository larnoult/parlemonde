<?php 
global $post, $virtue_premium, $kt_feat_width;
$height = get_post_meta( $post->ID, '_kad_posthead_height', true ); 
$swidth = get_post_meta( $post->ID, '_kad_posthead_width', true );
if (!empty($height)) {
	$slideheight = $height;
	$imageheight = $height;
} else {
	$slideheight = 400;
	$imageheight = apply_filters('kt_single_post_image_height', 400); 
}
if (!empty($swidth)) {
	$slidewidth = $swidth;
} else {
	$slidewidth = $kt_feat_width;
}
$kt_headcontent = get_post_meta( $post->ID, '_kad_blog_head', true );
if(empty($kt_headcontent) || $kt_headcontent == 'default') {
	if(!empty($virtue_premium['post_head_default'])) {
		$kt_headcontent = $virtue_premium['post_head_default'];
	} else {
		$kt_headcontent = 'none';
	}
}

if ( 'flex' == $kt_headcontent ) {

	$image_gallery = get_post_meta( $post->ID, '_kad_image_gallery', true );
	echo '<div class="postfeat">';
		virtue_build_slider($post->ID, $image_gallery, $slidewidth, $slideheight, 'image', 'kt-slider-same-image-ratio');
	echo '</div>';

} else if ($kt_headcontent == 'carouselslider') {
	$image_gallery = get_post_meta( $post->ID, '_kad_image_gallery', true );
	echo '<div class="postfeat">';
		virtue_build_slider($post->ID, $image_gallery, null, $slideheight, 'image', 'kt-slider-different-image-ratio', 'slider', 'false', 'true', '7000', 'true', 'false');
	echo '</div>';
} else if ($kt_headcontent == 'video') { ?>
	<div class="postfeat">
		<div class="videofit" style="max-width:<?php echo esc_attr($slidewidth);?>px; margin:0 auto;">
			<?php $video = get_post_meta( $post->ID, '_kad_post_video', true ); echo do_shortcode( $video ); ?>
		</div>
		<?php if (has_post_thumbnail( $post->ID ) ) { 
			$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' ); ?>
			<div itemprop="image" itemscope itemtype="https://schema.org/ImageObject">
				<meta itemprop="url" content="<?php echo esc_url($image[0]); ?>">
				<meta itemprop="width" content="<?php echo esc_attr($image[1]); ?>px">
				<meta itemprop="height" content="<?php echo esc_attr($image[2]); ?>px">
			</div>
		<?php } ?>
		</div>
<?php 
} else if($kt_headcontent == 'carousel' || $kt_headcontent == 'shortcode') {
	if (has_post_thumbnail( $post->ID ) ) { 
		$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' ); ?>
		<div itemprop="image" itemscope itemtype="https://schema.org/ImageObject">
			<meta itemprop="url" content="<?php echo esc_url( $image[0] ); ?>">
			<meta itemprop="width" content="<?php echo esc_attr( $image[1] );?>px">
			<meta itemprop="height" content="<?php echo esc_attr( $image[2] );?>px">
		</div>
	<?php } 
} else if ($kt_headcontent == 'image') {
	if (has_post_thumbnail( $post->ID ) ) {
		$img = virtue_get_image_array($slidewidth, $imageheight, true, null, null, get_post_thumbnail_id(), false);
		$img['schema'] = true;   
		?>
		<div class="imghoverclass postfeat post-single-img">
			<a href="<?php echo esc_url( $img['full'] ); ?>" rel-data="lightbox">
				<?php virtue_print_image_output( $img ); ?>
			</a>
		</div>
		<?php
	} 
}