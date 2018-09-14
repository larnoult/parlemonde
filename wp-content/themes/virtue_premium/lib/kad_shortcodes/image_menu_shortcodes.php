<?php 
//Shortcode for Blog Posts
function kad_image_menu_shortcode_function( $atts, $content) {
	extract(shortcode_atts(array(
		'id' => null,
		'height' => '110',
		'height_setting' => 'normal',
		'image' => null,
		'image_id' => null,
		'title' => null,
		'columns' => '3',
		'link' => null,
		'target' => null,
		'description' => null,
		'class' => null,
		'animation_delay' => '150'
), $atts));
	if ( 'true' == $target ) {
		$target = '_blank';
	}
	$output = virtue_image_menu_output_builder( $image_id, $height_setting, $height, $link, $columns, $target, $title, $description, $class, $image, $id, $animation_delay);

	return $output;
}
function virtue_image_menu_output_builder( $image_id = null, $height_setting = 'normal', $height = '110', $link = null, $columns = '3', $target = '_self', $title = null, $description = null, $class = null, $image_url = null, $id = null, $animation_delay = '150') {
	// Make sure we have an image
	if( empty( $image_id ) && empty( $image_url ) ) {
		return;
	}
	if ($columns == '2') {
		$itemsize = 'tcol-lg-6 tcol-md-6 tcol-sm-6 tcol-xs-12 tcol-ss-12'; $width = 559;
	} else if ($columns == '1'){
		$itemsize = '';  $width = null;
	} else if ($columns == '3'){
		$itemsize = 'tcol-lg-4 tcol-md-4 tcol-sm-4 tcol-xs-6 tcol-ss-12';  $width = 367;
	} else if ($columns == '6'){
		$itemsize = 'tcol-lg-2 tcol-md-2 tcol-sm-3 tcol-xs-4 tcol-ss-6'; $width = 240;
	} else if ($columns == '5'){
		$itemsize = 'tcol-lg-25 tcol-md-25 tcol-sm-3 tcol-xs-4 tcol-ss-6'; $width = 240;
	} else {
		$itemsize = 'tcol-lg-3 tcol-md-3 tcol-sm-4 tcol-xs-6 tcol-ss-12'; $width = 270;
	}
	ob_start();
		if ( ! empty( $target ) ) {
			$target = $target;
		} else {
			$target = '_self';
		}
		if ( empty( $image_id ) ) {
			$image_id = virtue_get_image_id_by_link( $image_url );
		}
		if(	'imgsize' == $height_setting ) {
			if ( ! empty( $width ) ) {
				if ( ! empty( $image_id ) ) {
					$img = virtue_get_image_array( $width, null, false, null, null, $image_id, false );
				} else {

					$img = array(
						'src' => $image_url,
						'width' => null,
						'height' => null,
						'srcset' => null,
						'class' => null,
						'alt' => $title,
					);
				}
			} else {
				if ( ! empty( $image_id ) ) {
					$image = wp_get_attachment_image_src( $image_id, 'full' );
					$srcset = wp_get_attachment_image_srcset( $image_id, 'full' );
					$srcset = 'srcset="'.$srcset.'"';
					$alt = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
				} else {
					$image = array( $image_url, null, null );
					$srcset = '';
					$alt = $title;
				}
				$img = array(
					'src' => $image[0],
					'width' => $image[1],
					'height' => $image[2],
					'srcset' => $srcset,
					'class' => null,
					'alt' => $alt,
				);
			} 
			echo '<div class="'.esc_attr( $itemsize ).' '.esc_attr( $class ).' kt_image_menu_'.esc_attr( $id ).' kad-animation image-menu-image-size" data-animation="fade-in" data-delay="'.esc_attr( $animation_delay ).'">';
				if ( ! empty( $link ) ) {
					echo '<a href="'.esc_url( $link ).'" class="homepromolink" target="'.esc_attr( $target ).'">';
				} else {
					echo '<div class="image_menu_wrap">';
				}
				echo '<div class="image_menu_hover_class"></div>';
				echo '<div class="kt-intrinsic-container" style="max-width:' . esc_attr( $img[ 'width' ] ) . 'px">';
					echo '<div class="kt-intrinsic" style="padding-bottom:' . esc_attr( ( $img['height'] / $img['width'] ) * 100 ) . '%;">'; 
						echo virtue_get_image_output( $img );
					echo '</div>';
				echo '</div>';
				echo '<div class="image_menu_content">';
					echo '<div class="image_menu_message">';
					if ( ! empty( $title ) ) { 
						echo '<h4>'.wp_kses_post( $title ).'</h4>';
					}
					if ( ! empty( $description ) ) {
						echo '<h5>'.wp_kses_post( $description ).'</h5>';
					}
					echo '</div>';
				echo '</div>';
				if ( ! empty( $link ) ) {
					echo '</a>';
				} else {
					echo '</div>';
				}
			echo '</div>';
		} else {
			if ( ! empty( $width ) ) {
				$x_image_width = $width * 2;
				$x_image_height = $height * 2;
				if ( ! empty( $image_id ) ) {
					$img = virtue_get_image_array( $x_image_width, $x_image_height, true, null, null, $image_id, false );
				} else {
					$img = array(
						'src' => $image_url,
					);
				}
			} else {
				if ( ! empty( $image_id ) ) {
					$image = wp_get_attachment_image_src( $image_id, 'full' );
				} else {
					$image = array( $image_url );
				}
				$img = array(
					'src' => $image[0],
				);
			} 
			echo '<div class="'.esc_attr( $itemsize ).' '.esc_attr( $class ).' kt_image_menu_'.esc_attr( $id ).' kad-animation" data-animation="fade-in" data-delay="'.esc_attr( $animation_delay ).'">';
				if ( ! empty( $link ) ) {
					echo '<a href="'.esc_url( $link ).'" class="homepromolink" target="'.esc_attr( $target ).'">';
				}
				echo '<div class="infobanner" style="background: url('.esc_url( $img['src'] ).') center center no-repeat; height:'.esc_attr( $height ).'px; background-size: cover;">';
					echo '<div class="home-message" style="height:'.esc_attr( $height ).'px;">';
						if ( ! empty( $title ) ) { 
							echo '<h4>'.wp_kses_post( $title ).'</h4>';
						}
						if ( ! empty( $description ) ) {
							echo '<h5>'.wp_kses_post( $description ).'</h5>';
						}
					echo '</div>';
				echo '</div>';
				if ( ! empty( $link ) ) {
					echo '</a>';
				}
			echo '</div>';
		}
	$output = ob_get_contents();
	ob_end_clean();

	return $output;
}


