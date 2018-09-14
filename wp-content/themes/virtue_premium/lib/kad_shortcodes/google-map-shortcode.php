<?php
/**
 * Shortcode for Google Maps
 *
 * @package Virtue Theme
 */

/**
 * Shortcode function for Google Maps
 *
 * @param array $atts attributes for shortcode.
 */
function virtue_map_shortcode_function( $atts ) {
	$map = shortcode_atts( array(
		'height'      => '300',
		'center'      => '',
		'address'     => 'USA',
		'title'       => '',
		'address2'    => '',
		'title2'      => '',
		'address3'    => '',
		'title3'      => '',
		'address4'    => '',
		'title4'      => '',
		'zoom'        => '15',
		'id'          => wp_rand( 10, 1000 ),
		'maptype'     => 'ROADMAP',
		'scrollwheel' => '',
	), $atts );
	if ( empty( $map['center'] ) ) {
		$map['center'] = $map['address'];
	}
	if ( ! empty( $map['scrollwheel'] ) && 'true' === $map['scrollwheel'] ) {
		$map['scrollwheel'] = 'true';
	} else {
		$map['scrollwheel'] = 'false';
	}
	global $virtue_premium;
	if ( isset( $virtue_premium['google_map_api'] ) && ! empty( $virtue_premium['google_map_api'] ) ) {
		$gmap_api = $virtue_premium['google_map_api'];
	} else {
		$gmap_api = '';
	}
	ob_start();
	if ( ! empty( $gmap_api ) ) {
		wp_enqueue_script( 'virtue_google_map_api' );
		wp_enqueue_script( 'virtue_gmap' );
		?>
		<div id="map_address<?php echo esc_attr( $map['id'] ); ?>" style="height:<?php echo esc_attr( $map['height'] ); ?>px; margin-bottom:20px;" class="kad_google_map kt-gmap-js-init" data-maptype="<?php echo esc_attr( $map['maptype'] ); ?>" data-map-scrollwheel="<?php echo esc_attr( $map['scrollwheel'] ); ?>" data-mapzoom="<?php echo esc_attr( $map['zoom'] ); ?>" data-mapcenter="<?php echo esc_attr( $map['center'] ); ?>" data-address1="<?php echo esc_attr( $map['address'] ); ?>" data-address2="<?php echo esc_attr( $map['address2'] ); ?>" data-address3="<?php echo esc_attr( $map['address3'] ); ?>" data-address4="<?php echo esc_attr( $map['address4'] ); ?>" data-title1="<?php echo esc_attr( $map['title'] ); ?>" data-title2="<?php echo esc_attr( $map['title2'] ); ?>" data-title3="<?php echo esc_attr( $map['title3'] ); ?>" data-title4="<?php echo esc_attr( $map['title4'] ); ?>">
		</div>
		<?php
	} else {
		if ( 'TERRAIN' === $map['maptype'] ) {
			$map['maptype'] = 'p';
		} elseif ( 'HYBRID' === $map['maptype'] ) {
			$map['maptype'] = 'h';
		} elseif ( 'SATELLITE' === $map['maptype'] ) {
			$map['maptype'] = 'k';
		} else {
			$map['maptype'] = 'm';
		}
		$query_string = 'q=' . rawurlencode( $map['address'] ) . '&cid=&t=' . rawurlencode( $map['maptype'] ) . '&center=' . rawurlencode( $map['center'] );
		echo '<div id="map_address' . esc_attr( $map['id'] ) . '" class="kt-map"><iframe height="' . esc_attr( $map['height'] ) . '" src="https://maps.google.com/maps?&' . esc_attr( htmlentities( $query_string ) ) . '&output=embed&z=' . esc_attr( $map['zoom'] ) . '&iwloc=A&visual_refresh=true"></iframe></div>';

	}
	$output = ob_get_contents();
	ob_end_clean();
	return $output;
}
?>
