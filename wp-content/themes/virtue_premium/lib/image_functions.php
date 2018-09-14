<?php 

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
function virtue_basic_image_sizes() {
	$sizes = array('full' => 'Full Size');

	foreach ( get_intermediate_image_sizes() as $_size ) {
		if ( in_array( $_size, array('thumbnail', 'medium', 'medium_large', 'large') ) ) {
			$sizes[$_size]  = $_size .' - '. get_option( "{$_size}_size_w" ).'x'.get_option( "{$_size}_size_h" );
		} 
	}
	$sizes['custom'] = 'Custom';

	return $sizes;
}

add_filter( 'max_srcset_image_width','virtue_srcset_max');
function virtue_srcset_max($string) {
	return 2400;
}
function kad_lazy_load_filter() {
	error_log( "The kad_lazy_load_filter() function is deprecated since version 4.3.5. Please use Virtue_Lazy_Load::is_lazy() instead." );
	return Virtue_Lazy_Load::is_lazy();
}

function virtue_img_placeholder_filter_init() {
	global $virtue_premium;
	function virtue_img_placeholder() {
		return apply_filters('kadence_placeholder_image', get_template_directory_uri() . '/assets/img/post_standard.jpg');
	}
	function virtue_img_placeholder_cat() {
		return apply_filters('kadence_placeholder_image_cat', get_template_directory_uri() . '/assets/img/placement.jpg');
	}
	function virtue_img_placeholder_small() {
		return apply_filters('kadence_placeholder_image_small', get_template_directory_uri() . '/assets/img/post_standard-80x50.jpg');
	}
	function virtue_post_default_placeholder() {
		return apply_filters('kadence_post_default_placeholder_image', get_template_directory_uri() . '/assets/img/post_standard.jpg');
	}

	function virtue_post_default_placeholder_override() {
		global $virtue_premium;
		$custom_image = $virtue_premium['post_summery_default_image']['url'];
		return $custom_image;
	}

	if ( isset( $virtue_premium['post_summery_default_image'] ) && ! empty( $virtue_premium['post_summery_default_image']['url'] ) ) {
		add_filter('kadence_placeholder_image_small', 'virtue_post_default_placeholder_override');
		add_filter('kadence_post_default_placeholder_image', 'virtue_post_default_placeholder_override');
	}
}
add_action('init', 'virtue_img_placeholder_filter_init');

function virtue_default_placeholder_image() {
	return apply_filters('virtue_default_placeholder_image', 'http://placehold.it/');
}
function virtue_get_options_placeholder_image() {
	global $virtue_premium;
	if(isset($virtue_premium['post_summery_default_image']) && isset($virtue_premium['post_summery_default_image']['id']) && !empty($virtue_premium['post_summery_default_image']['id'])){
		return $virtue_premium['post_summery_default_image']['id'];
	} else {
		return '';
	}
}
function virtue_get_processed_image_array( $args = array() ) {
	$defaults = array(
		'width' 		=> null,
		'height' 		=> null,
		'crop'			=> true,
		'class'			=> null,
		'alt'			=> null,
		'id'			=> null,
		'placeholder'	=> false,
	);
	$args = wp_parse_args( $args, $defaults );
	extract($args);
	if ( empty( $id ) ) {
		$id = get_post_thumbnail_id();
	}
	if ( empty( $id ) ) {
		if( $placeholder == true ) {
			$id = virtue_get_options_placeholder_image();
		}
	}
	if( ! empty( $id ) ) {
		$virtue_get_image = Virtue_Get_Image::getInstance();
		$image = $virtue_get_image->process( $id, $width, $height);
		if( empty( $alt ) ) {
			$alt = get_post_meta( $id, '_wp_attachment_image_alt', true );
		}
		$return_array = array(
			'src' => $image[0],
			'width' => $image[1],
			'height' => $image[2],
			'srcset' => $image[3],
			'src_set' => $image[4],
			'sizes' => $image[5],
			'class' => $class,
			'alt' => $alt,
			'full' => $image[6],
		);
	} else if( empty( $id ) && $placeholder == true ) {
		if ( empty( $height ) ){
			$height = $width;
		}
		if ( empty( $width ) ){
			$width = $height;
		}
		$return_array = array(
			'src' => virtue_default_placeholder_image().$width.'x'.$height.'?text=Image+Placeholder',
			'width' => $width,
			'height' => $height,
			'srcset' => '',
			'src_set' => '',
			'sizes' => '',
			'class' => $class,
			'alt' => $alt,
			'full' => virtue_default_placeholder_image().$width.'x'.$height.'?text=Image+Placeholder',
		);
	} else {
		$return_array = array(
			'src' => '',
			'width' => '',
			'height' => '',
			'srcset' => '',
			'src_set' => '',
			'sizes' => '',
			'class' => '',
			'alt' => '',
			'full' => '',
		);
	}

	return $return_array;
}
function virtue_get_image_array($width = null, $height = null, $crop = true, $class = null, $alt = null, $id = null, $placeholder = false) {
    if(empty($id)) {
        $id = get_post_thumbnail_id();
    }
    if(empty($id)){
        if($placeholder == true) {
            $id = virtue_get_options_placeholder_image();
        }
    }
    if( ! empty( $id ) ) {
        $virtue_get_image = Virtue_Get_Image::getInstance();
        $image = $virtue_get_image->process( $id, $width, $height);
        if( empty( $alt ) ) {
            $alt = get_post_meta($id, '_wp_attachment_image_alt', true);
        }
        $return_array = array(
            'src' => $image[0],
            'width' => $image[1],
            'height' => $image[2],
            'srcset' => $image[3],
            'src_set' => $image[4],
            'sizes' => $image[5],
            'class' => $class,
            'alt' => $alt,
            'full' => $image[6],
        );
    } else if( empty( $id ) && $placeholder == true ) {
    	if ( empty( $height ) ){
    		$height = $width;
    	}
    	if ( empty( $width ) ){
    		$width = $height;
    	}
        $return_array = array(
            'src' => virtue_default_placeholder_image().$width.'x'.$height.'?text=Image+Placeholder',
            'width' => $width,
            'height' => $height,
            'srcset' => '',
            'src_set' => '',
            'sizes' => '',
            'class' => $class,
            'alt' => $alt,
            'full' => virtue_default_placeholder_image().$width.'x'.$height.'?text=Image+Placeholder',
        );
    } else {
        $return_array = array(
			'src' => '',
			'width' => '',
			'height' => '',
			'srcset' => '',
			'src_set' => '',
			'sizes' => '',
			'class' => '',
			'alt' => '',
			'full' => '',
        );
    }

    return $return_array;
}
function virtue_print_image_output( $img ) {
	echo virtue_get_image_output( $img );
}
function virtue_get_image_output( $img ) {
	$defaults = array(
		'src' => null,
		'width' => null,
		'height' => null,
		'srcset' => null,
		'src_set' => null,
		'sizes' => null,
		'class' => null,
		'alt' => null,
		'full' => null,
		'extras' => null,
		'lazy' => true,
		'schema' => false,
	);
	$img = wp_parse_args( $img, $defaults );
	$output = '';
	if( $img['lazy'] && Virtue_Lazy_Load::is_lazy() ) {
		$image_src_output = 'data-lazy-src="'.esc_url($img['src']).'" ';
		$image_src_output = apply_filters('virtue_lazy_src_output', $image_src_output );
		$image_src_set_output = ( ! empty( $img['src_set'] ) ? 'data-lazy-srcset="'.esc_attr( $img['src_set'] ).'"' : ''); 
		$image_sizes_output = ( ! empty( $img['src_set'] ) ? 'sizes="'.esc_attr($img['sizes']).'"' : ''); 
	} else {
		$image_src_output = 'src="'.esc_url($img['src']).'"'; 
		$image_src_set_output = ( ! empty( $img['src_set'] ) ? 'srcset="'.esc_attr( $img['src_set']).'"' : '');
		$image_sizes_output =  ( ! empty( $img['src_set'] ) ? 'sizes="'.esc_attr($img['sizes']).'"' : '');
	}
	if( true == $img['schema'] ) {
		$output .= '<div itemprop="image" itemscope itemtype="http://schema.org/ImageObject">';
		$output .='<img width="'.esc_attr($img['width']).'" height="'.esc_attr($img['height']).'" '.wp_kses_post( $image_src_output .' '. $image_src_set_output .' '.$image_sizes_output.' '.$img['extras'] ).' class="'.esc_attr($img['class']).'" itemprop="contentUrl" alt="'.esc_attr($img['alt']).'">';
		$output .= '<meta itemprop="url" content="'.esc_url($img['src']).'">';
		$output .= '<meta itemprop="width" content="'.esc_attr($img['width']).'px">';
		$output .= '<meta itemprop="height" content="'.esc_attr($img['height']).'px">';
		$output .= '</div>';
	} else {
		$output .= '<img '.wp_kses_post( $image_src_output .' '. $image_src_set_output .' '.$image_sizes_output.' '.$img['extras'] ).' alt="'.esc_attr( $img[ 'alt' ] ).'" width="'.esc_attr( $img[ 'width' ] ).'" height="'.esc_attr( $img[ 'height' ] ).'" class="'.esc_attr( $img[ 'class' ] ).'">';
	}
	return $output;
}
function virtue_get_full_image_output( $width = null, $height = null, $crop = true, $class = null, $alt = null, $id = null, $placeholder = false, $lazy = false, $schema = true, $extra = null, $intrinsic = false ) {
    $img = virtue_get_image_array( $width, $height, $crop, $class, $alt, $id, $placeholder );
	if ( $lazy && virtue_Lazy_Load::is_lazy() ) {
		$image_src_output     = 'data-lazy-src="' . esc_url( $img['src'] ) . '" ';
		$image_src_output     = apply_filters( 'virtue_lazy_src_output', $image_src_output );
		$image_src_set_output = ( ! empty( $img['src_set'] ) ? 'data-lazy-srcset="' . esc_attr( $img['src_set'] ) . '"' : '' );
		$image_sizes_output   = ( ! empty( $img['src_set'] ) ? 'sizes="' . esc_attr( $img['sizes'] ) . '"' : '' );
	} else {
		$image_src_output     = 'src="' . esc_url( $img['src'] ) . '"';
		$image_src_set_output = ( ! empty( $img['src_set'] ) ? 'srcset="' . esc_attr( $img['src_set'] ) . '"' : '' );
		$image_sizes_output   = ( ! empty( $img['src_set'] ) ? 'sizes="' . esc_attr( $img['sizes'] ) . '"' : '' );
	}
    $extras = '';
    if(is_array($extra)) {
    	foreach ($extra as $key => $value) {
    		$extras .= esc_attr($key).'="'.esc_attr($value).'" ';
    	}
    } else {
    	$extras = $extra;	
    }
    if(!empty($img['src']) && $schema == true) {
    	$output = '';
    	if($intrinsic == true) {
    		$output .= '<div class="kt-intrinsic" style="padding-bottom:'.esc_attr(($img['height']/$img['width']) * 100).'%;">';
    	}
		$output .= '<div itemprop="image" itemscope itemtype="http://schema.org/ImageObject">';
		$output .='<img '.$image_src_output.' width="'.esc_attr($img['width']).'" height="'.esc_attr($img['height']).'" '.$image_src_set_output.' '.$image_sizes_output.' class="'.esc_attr($img['class']).'" itemprop="contentUrl" alt="'.esc_attr($img['alt']).'" '.$extras.'>';
		$output .= '<meta itemprop="url" content="'.esc_url($img['src']).'">';
		$output .= '<meta itemprop="width" content="'.esc_attr($img['width']).'px">';
		$output .= '<meta itemprop="height" content="'.esc_attr($img['height']).'px">';
		$output .= '</div>';

	    if($intrinsic == true) {
    		$output .= '</div>';
    	}
      	return $output;

    } elseif(!empty($img['src'])) {
    	$output = '';
    	if($intrinsic == true) {
    		$output .= '<div class="kt-intrinsic" style="padding-bottom:'.esc_attr(($img['height']/$img['width']) * 100).'%;">';
    	}
        	$output .= '<img '.$image_src_output.' width="'.esc_attr($img['width']).'" height="'.esc_attr($img['height']).'" '.$image_src_set_output.' '.$image_sizes_output.' class="'.esc_attr($img['class']).'" alt="'.esc_attr($img['alt']).'" '.$extras.'>';
        if($intrinsic == true) {
    		$output .= '</div>';
    	}
      	return $output;
    } else {
        return null;
    }
}

function virtue_print_full_image_output( $args ) {
	$defaults = array(
		'width' 		=> null,
		'height' 		=> null,
		'crop'			=> true,
		'class'			=> null,
		'alt'			=> null,
		'id'			=> null,
		'placeholder'	=> false,
		'lazy'			=> true,
		'schema'		=> true,
		'extra'           => null,
		'intrinsic'       => false,
		'intrinsic_max'   => false,
	);
	$args = wp_parse_args( $args, $defaults );
    $img = virtue_get_processed_image_array($args);
    extract( $args );
	if( $lazy && Virtue_Lazy_Load::is_lazy() ) {
		$image_src_output = 'data-lazy-src="'.esc_url($img['src']).'" ';
		$image_src_output = apply_filters('virtue_lazy_src_output', $image_src_output );
		$image_src_set_output = ( ! empty( $img['src_set'] ) ? 'data-lazy-srcset="'.esc_attr( $img['src_set'] ).'"' : ''); 
		$image_sizes_output = ( ! empty( $img['src_set'] ) ? 'sizes="'.esc_attr($img['sizes']).'"' : ''); 
	} else {
		$image_src_output = 'src="'.esc_url($img['src']).'"'; 
		$image_src_set_output = ( ! empty( $img['src_set'] ) ? 'srcset="'.esc_attr( $img['src_set']).'"' : '');
		$image_sizes_output =  ( ! empty( $img['src_set'] ) ? 'sizes="'.esc_attr($img['sizes']).'"' : '');
	}
    $extras = '';
    if( is_array( $extra ) ) {
    	foreach ($extra as $key => $value) {
    		$extras .= esc_attr($key).'="'.esc_attr($value).'" ';
    	}
    } else {
    	$extras = $extra;	
    }
    if ( ! empty($img['src'] ) && $schema == true ) {
    	$output = '';
    	if($intrinsic == true) {
			if ( true == $intrinsic_max ) {
				$output .= '<div class="kt-intrinsic-container kt-intrinsic-container-center" style="max-width:' . esc_attr( $img['width'] ) . 'px">';
			}
    		$output .= '<div class="kt-intrinsic" style="padding-bottom:'.esc_attr(($img['height']/$img['width']) * 100).'%;">';
    	}
		$output .= '<div itemprop="image" itemscope itemtype="http://schema.org/ImageObject">';
		$output .='<img '.$image_src_output.' width="'.esc_attr($img['width']).'" height="'.esc_attr($img['height']).'" '.$image_src_set_output.' '.$image_sizes_output.' class="'.esc_attr($img['class']).'" itemprop="contentUrl" alt="'.esc_attr($img['alt']).'" '.$extras.'>';
		$output .= '<meta itemprop="url" content="'.esc_url($img['src']).'">';
		$output .= '<meta itemprop="width" content="'.esc_attr($img['width']).'px">';
		$output .= '<meta itemprop="height" content="'.esc_attr($img['height']).'px">';
		$output .= '</div>';

	    if($intrinsic == true) {
    		$output .= '</div>';
			if ( true == $intrinsic_max ) {
				$output .= '</div>';
			}
    	}
      	echo $output;

    } elseif( ! empty($img['src'] ) ) {
    	$output = '';
    	if($intrinsic == true) {
    		$output .= '<div class="kt-intrinsic" style="padding-bottom:'.esc_attr(($img['height']/$img['width']) * 100).'%;">';
    	}
        	$output .= '<img '.$image_src_output.' width="'.esc_attr($img['width']).'" height="'.esc_attr($img['height']).'" '.$image_src_set_output.' '.$image_sizes_output.' class="'.esc_attr($img['class']).'" alt="'.esc_attr($img['alt']).'" '.$extras.'>';
        if($intrinsic == true) {
    		$output .= '</div>';
    	}
      	echo $output;
    } else {
        echo null;
    }
}