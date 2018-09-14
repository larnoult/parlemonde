<?php 
global $post, $virtue, $virtue_sidebar; 
$headcontent = get_post_meta( $post->ID, '_kad_blog_head', true );
$height      = get_post_meta( $post->ID, '_kad_posthead_height', true ); 
$swidth      = get_post_meta( $post->ID, '_kad_posthead_width', true );
if(empty($headcontent) || $headcontent == 'default') {
	if(!empty($virtue['post_head_default'])) {
		$headcontent = $virtue['post_head_default'];
	} else {
		$headcontent = 'none';
	}
}
if($virtue_sidebar) {
	$slide_sidebar = 848;
} else {
	$slide_sidebar = 1140;
}
if ( !empty( $height ) ) {
	$slideheight = $height; 
} else {
	$slideheight = 400;
}
if ( !empty( $swidth ) ) {
	$slidewidth = $swidth; 
} else {
	$slidewidth = $slide_sidebar;
} 

if ( $headcontent == 'flex' ) { ?>
	<div class="postfeat">
		<div class="flexslider kt-flexslider" style="max-width:<?php echo esc_attr( $slidewidth );?>px;" data-flex-speed="7000" data-flex-anim-speed="400" data-flex-animation="fade" data-flex-auto="true">
			<ul class="slides">
			<?php $image_gallery = get_post_meta( $post->ID, '_kad_image_gallery', true );
				if( !empty( $image_gallery ) ) {
					$attachments = array_filter( explode( ',', $image_gallery ) );
					if ( $attachments) {
						foreach ( $attachments as $attachment ) {
							$caption = get_post( $attachment )->post_excerpt;
							$img = virtue_get_image_array( $slidewidth, $slideheight, true, 'kt-slider-image', $caption, $attachment );
							echo '<li>';
								echo '<a href="'.esc_url( $img['full'] ).'" data-rel="lightbox" itemprop="image" itemscope itemtype="https://schema.org/ImageObject">';
									echo '<img src="'.esc_url( $img['src'] ).'" width="'.esc_attr( $img['width'] ).'" height="'.esc_attr( $img['height'] ).'" '.wp_kses_post( $img['srcset'] ).' class="'.esc_attr( $img['class'] ).'" alt="'.esc_attr( $img['alt'] ).'">';
									echo '<meta itemprop="url" content="'.esc_url( $img['src'] ).'">';
									echo '<meta itemprop="width" content="'.esc_attr( $img['width'] ).'">';
									echo '<meta itemprop="height" content="'.esc_attr( $img['height'] ).'">';
								echo '</a>';
							echo '</li>';
						}
					}
				} else {
					$attach_args = array( 'order'=> 'ASC','post_type'=> 'attachment','post_parent'=> $post->ID, 'post_mime_type' => 'image','post_status'=> null,'orderby'=> 'menu_order','numberposts'=> -1 );
					$attachments = get_posts( $attach_args );
					if ( $attachments ) {
						foreach ( $attachments as $attachment ) {
							$img = virtue_get_image_array( $slidewidth, $slideheight, true, 'kt-slider-image', null, $attachment->ID );
							echo '<li>';
								echo '<a href="'.esc_url( $img['full'] ).'" data-rel="lightbox" itemprop="image" itemscope itemtype="https://schema.org/ImageObject">';
									echo '<img src="'.esc_url( $img['src'] ).'" width="'.esc_attr( $img['width'] ).'" height="'.esc_attr( $img['height'] ).'" '.wp_kses_post( $img['srcset'] ).' class="'.esc_attr( $img['class'] ).'" alt="'.esc_attr( $img['alt'] ).'">';
									echo '<meta itemprop="url" content="'.esc_url( $img['src'] ).'">';
									echo '<meta itemprop="width" content="'.esc_attr( $img['width'] ).'">';
									echo '<meta itemprop="height" content="'.esc_attr( $img['height'] ).'">';
								echo '</a>';
							echo '</li>';
						}
					} 
				} ?>
			</ul>
		</div> <!--Flex Slides-->
	</div>
<?php } else if ( $headcontent == 'video' ) { ?>
	<div class="postfeat">
		<div class="videofit">
			<?php 
				$allowed_tags = wp_kses_allowed_html('post');
				$allowed_tags['iframe'] = array(
					'src'             => true,
					'height'          => true,
					'width'           => true,
					'frameborder'     => true,
					'allowfullscreen' => true,
					'name' 			  => true,
					'id' 			  => true,
					'class' 		  => true,
					'style' 		  => true,
				);

				echo do_shortcode( wp_kses( get_post_meta( $post->ID, '_kad_post_video', true ), $allowed_tags ) ); ?>
		</div>
		<?php
		if (has_post_thumbnail( $post->ID ) ) { 
			$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' ); ?>
			<div itemprop="image" itemscope itemtype="https://schema.org/ImageObject">
				<meta itemprop="url" content="<?php echo esc_url( $image[0] ); ?>">
				<meta itemprop="width" content="<?php echo esc_attr( $image[1] )?>">
				<meta itemprop="height" content="<?php echo esc_attr( $image[2] )?>">
			</div>
		<?php } ?>
	</div>
<?php } else if ( $headcontent == 'image' ) {
	if (has_post_thumbnail( $post->ID ) ) {
		$img = virtue_get_image_array( $slidewidth, $slideheight, true, 'kt-slider-image', null, get_post_thumbnail_id( $post->ID ) );
		echo '<div class="imghoverclass">';
			echo '<a href="'.esc_url( $img['full'] ).'" data-rel="lightbox" itemprop="image" itemscope itemtype="https://schema.org/ImageObject">';
				echo '<img src="'.esc_url( $img['src'] ).'" width="'.esc_attr( $img['width'] ).'" height="'.esc_attr( $img['height'] ).'" '.wp_kses_post( $img['srcset'] ).' class="'.esc_attr( $img['class'] ).'" alt="'.esc_attr( $img['alt'] ).'">';
				echo '<meta itemprop="url" content="'.esc_url( $img['src'] ).'">';
				echo '<meta itemprop="width" content="'.esc_attr( $img['width'] ).'">';
				echo '<meta itemprop="height" content="'.esc_attr( $img['height'] ).'">';
			echo '</a>';
		echo '</div>';
	}
} else {
	if(has_post_thumbnail()) {
		$image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' ); 
		echo '<div class="meta_post_image" itemprop="image" itemscope itemtype="https://schema.org/ImageObject">';
			echo '<meta itemprop="url" content="'.esc_url($image[0]).'">';
			echo '<meta itemprop="width" content="'.esc_attr($image[1]).'">';
			echo '<meta itemprop="height" content="'.esc_attr($image[2]).'">';
		echo '</div>';
	}
} ?>