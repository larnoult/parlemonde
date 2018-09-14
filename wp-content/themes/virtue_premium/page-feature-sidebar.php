<?php
/*
Template Name: Feature - Sidebar
*/
global $post;
 	$headoption = get_post_meta( $post->ID, '_kad_page_head', true );
	if ( 'flex' == $headoption ) {
		get_template_part('templates/flex', 'slider');
	} else if ( 'carousel' == $headoption ) {
		get_template_part('templates/imagecarousel', 'slider');
	} else if ( 'carouselslider' == $headoption ) {
		get_template_part('templates/carousel', 'slider');
	} else if ( 'rev' == $headoption ) {
		$above = get_post_meta( $post->ID, '_kad_shortcode_above_header', true );
		if ( isset( $above ) && $above != 'on' ) {
			get_template_part('templates/rev', 'slider');
		}
	} else if ( 'cyclone' == $headoption || 'ktslider' == $headoption ) {
		$above = get_post_meta( $post->ID, '_kad_shortcode_above_header', true ); 
		if( isset( $above ) && $above != 'on' ) { 
			get_template_part( 'templates/cyclone', 'slider' );
		}
	} else if ( 'video' == $headoption ) {
		?>
		<div class="postfeat pageheadfeature container">
			<?php 
		 	$swidth = get_post_meta( $post->ID, '_kad_posthead_width', true ); 
			if ( ! empty( $swidth ) ) {
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
		</div>
		<?php 
	} else if ( 'image' == $headoption ) {
		$height = get_post_meta( $post->ID, '_kad_posthead_height', true );
		if ( ! empty( $height ) ) {
			$slideheight = $height;
		} else{
			$slideheight = 400; 
		}
		$swidth = get_post_meta( $post->ID, '_kad_posthead_width', true );
		if ( ! empty( $swidth ) ) {
			$slidewidth = $swidth;
		} else {
			$slidewidth = 1170;
		}
		$uselightbox = get_post_meta( $post->ID, '_kad_feature_img_lightbox', true ); 
		if ( ! empty( $uselightbox ) ) {
			$lightbox = $uselightbox;
		} else {
			$lightbox = 'yes';
		}
		$img = virtue_get_image_array( $slidewidth, $slideheight, true, 'kt-feature-image', null, get_post_thumbnail_id() );
		?>
		<div class="postfeat pageheadfeature container feature_container">
			<div class="imghoverclass img-margin-center">
				<?php 
				if( 'yes' == $lightbox ) { 
					echo '<a href="'.esc_url( $img['full'] ).'" data-rel="lightbox" class="lightboxhover">';
				} 
					echo '<img src="'.esc_url( $img['src'] ).'" width="'.esc_attr( $img['width'] ).'" height="'.esc_attr( $img['height'] ).'" '.wp_kses_post( $img[ 'srcset' ] ).' class="'.esc_attr( $img['class'] ).'" alt="'.esc_attr( $img['alt'] ).'">';
				if( 'yes' == $lightbox ) {
					echo '</a>';
				} ?>
			</div>
		</div>
        <?php
	}
	/**
    * @hooked virtue_page_title - 20
    */
     do_action('kadence_page_title_container');
     do_action('virtue_page_title_container');
    ?>
	
    <div id="content" class="container <?php echo esc_attr( virtue_container_class() ); ?>">
   		<div class="row">
     		<div class="main <?php echo virtue_main_class(); ?>" id="ktmain" role="main">
     		<?php 
                do_action('kadence_page_before_content'); ?>
				<div class="entry-content" itemprop="mainContentOfPage">
					<?php get_template_part('templates/content', 'page'); ?>
				</div>
				<?php 
                /**
                * @hooked virtue_page_comments - 20
                */
                do_action('kadence_page_footer');
                do_action('virtue_page_footer');
                ?>
				
			</div><!-- /.main -->