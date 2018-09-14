<?php
/*
Template Name: Feature
*/

global $post; 
$headoption = get_post_meta( $post->ID, '_kad_page_head', true ); 
if ( $headoption == 'flex' ) {
	get_template_part( 'templates/flex', 'slider' );
} else if ( $headoption == 'video' ) { ?>
	<section class="postfeat container">
		<?php 
		$swidth = get_post_meta( $post->ID, '_kad_posthead_width', true ); 
		if (!empty($swidth)) {
			$slidewidth = $swidth; 
		} else {
			$slidewidth = 1170;
		}	?>
		<div class="videofit" style="max-width:<?php echo esc_attr( $slidewidth );?>px; margin-left: auto; margin-right:auto;">
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

			echo do_shortcode( wp_kses( get_post_meta( $post->ID, '_kad_post_video', true ), $allowed_tags ) );
			 ?>
		</div>
	</section>
<?php } else if ( $headoption == 'image' ) {
		$height 	 = get_post_meta( $post->ID, '_kad_posthead_height', true );
		$swidth 	 = get_post_meta( $post->ID, '_kad_posthead_width', true );  
		$uselightbox = get_post_meta( $post->ID, '_kad_feature_img_lightbox', true );
		if ( !empty( $height ) ) {
			$slideheight = $height;
		} else {
			$slideheight = 400;
		}
		if ( !empty($swidth ) ) {
			$slidewidth = $swidth;
		} else {
			$slidewidth = 1140;
		}
		if ( !empty($uselightbox ) ) {
			$lightbox = $uselightbox;
		} else {
			$lightbox = 'yes';
		}
		$img = virtue_get_image_array( $slidewidth, $slideheight, true, 'kt-feature-image', null, get_post_thumbnail_id() );
		?>
		<div class="postfeat container">
			<div class="imghoverclass img-margin-center">
				<?php 
				if( $lightbox == 'yes' ) { 
					echo '<a href="'.esc_url( $img['full'] ).'" data-rel="lightbox" class="lightboxhover">';
				} 
					echo '<img src="'.esc_url( $img['src'] ).'" width="'.esc_attr( $img['width'] ).'" height="'.esc_attr( $img['height'] ).'" '.wp_kses_post( $img['srcset'] ).' class="'.esc_attr( $img['class'] ).'" alt="'.esc_attr( $img['alt'] ).'">';
				if( $lightbox == 'yes' ) {
					echo '</a>';
				} ?>
			</div>
		</div>
<?php } 

/**
* @hooked virtue_page_title - 20
*/
do_action( 'virtue_page_title_container' );
?>
	<div id="content" class="container">
		<div class="row">
			<div class="main <?php echo esc_attr( virtue_main_class() ); ?>" role="main">
				<div class="entry-content" itemprop="mainContentOfPage" itemscope itemtype="http://schema.org/WebPageElement">
					<?php get_template_part( 'templates/content', 'page' ); ?>
				</div>
				<?php 
				/**
                * @hooked virtue_page_comments - 20
                */
				do_action( 'virtue_page_footer' );
				?>
			</div><!-- /.main -->