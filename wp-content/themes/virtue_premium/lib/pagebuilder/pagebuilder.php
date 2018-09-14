<?php 

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Add support for page builder
function kadence_siteoriginpanels_row_attributes($attr, $row) {
  if(!empty($row['style']['class'])) {
    if(empty($attr['style'])) $attr['style'] = '';
    $attr['style'] .= 'margin-bottom: 0px;';
    $attr['style'] .= 'margin-left: 0px;';
    $attr['style'] .= 'margin-right: 0px;';
  }

  return $attr;
}
add_filter('siteorigin_panels_row_attributes', 'kadence_siteoriginpanels_row_attributes', 10, 2);

function kad_panels_row_background_styles($fields) {
	$fields['vertical_gutter'] = array(
        'name'      => __('Vertical Gutter', 'virtue'),
        'description' => __('Default matches row bottom margin settings.'),
        'type'      => 'select',
        'options'   => array(
               "default"       => __("Default", "virtue"),
               "no-margin"    => __("No Margin", "virtue"),
        ),
        'group'     => 'layout',
        'priority'  => 6,
  );
	  $fields['padding_top'] = array(
	        'name'      => __('Padding Top', 'virtue'),
	        'type'      => 'measurement',
	        'group'     => 'layout',
	        'priority'  => 8,
	  );
	  $fields['padding_bottom'] = array(
	        'name'      => __('Padding Bottom', 'virtue'),
	        'type'      => 'measurement',
	        'group'     => 'layout',
	        'priority'  => 8.5,
	  );
  	$fields['padding_left'] = array(
        'name'      => __('Padding Left', 'virtue'),
        'type'      => 'measurement',
        'group'     => 'layout',
        'priority'  => 9,
     );
  $fields['padding_right'] = array(
        'name'      => __('Padding Right', 'virtue'),
        'type'      => 'measurement',
        'group'     => 'layout',
        'priority'  => 9,
      );

    $fields['background_image_url'] = array(
        'name'      => __('Background image external url', 'virtue'),
        'group'     => 'design',
        'description' => 'optional, overridden by button below.',
        'type'      => 'url',
        'priority'  => 4,
      );
  $fields['background_image'] = array(
        'name'      => __('Background Image', 'virtue'),
        'group'     => 'design',
        'type'      => 'image',
        'priority'  => 4,
      );
  $fields['background_image_position'] = array(
        'name'      => __('Background Image Position', 'virtue'),
        'type'      => 'select',
        'group'     => 'design',
        'default'   => 'center top',
        'priority'  => 6,
        'options'   => array(
               "left top"       => __("Left Top", "virtue"),
               "left center"    => __("Left Center", "virtue"),
               "left bottom"    => __("Left Bottom", "virtue"),
               "center top"     => __("Center Top", "virtue"),
               "center center"  => __("Center Center", "virtue"),
               "center bottom"  => __("Center Bottom", "virtue"),
               "right top"      => __("Right Top", "virtue"),
               "right center"   => __("Right Center", "virtue"),
               "right bottom"   => __("Right Bottom", "virtue")
                ),
      );
  $fields['background_image_style'] = array(
        'name'      => __('Background Image Style', 'virtue'),
        'type'      => 'select',
        'group'     => 'design',
        'default'   => 'center top',
        'priority'  => 6,
        'options'   => array(
             "cover"      => __("Cover", "virtue"),
             "parallax"   => __("Parallax", "virtue"),
             "no-repeat"  => __("No Repeat", "virtue"),
             "repeat"     => __("Repeat", "virtue"),
             "repeat-x"   => __("Repeat-X", "virtue"),
             "repeat-y"   => __("Repeat-y", "virtue"),
              ),
        );
  $fields['border_top'] = array(
        'name'      => __('Border Top Size', 'virtue'),
        'type'      => 'measurement',
        'group'     => 'design',
        'priority'  => 8,
  );
  $fields['border_top_color'] = array(
        'name'      => __('Border Top Color', 'virtue'),
        'type'      => 'color',
        'group'     => 'design',
        'priority'  => 8.5,
      );
  $fields['border_bottom'] = array(
        'name'      => __('Border Bottom Size', 'virtue'),
        'type'      => 'measurement',
        'group'     => 'design',
        'priority'  => 9,
  );
  $fields['border_bottom_color'] = array(
        'name' => __('Border Bottom Color', 'virtue'),
        'type' => 'color',
        'group' => 'design',
        'priority' => 9.5,
  );
  $fields['row_separator'] = array(
        'name'      => __('Row Separator', 'virtue'),
        'type'      => 'select',
        'group'     => 'design',
        'default'   => 'none',
        'priority'  => 10,
        'options'   => array(
               "none"       				=> __("None", "virtue"),
               "center_triangle"    		=> __("Center Triangle", "virtue"),
               "center_triangle_double"    	=> __("Center Triangle Double", "virtue"),
               "left_triangle"  			=> __("Left Triangle", "virtue"),
               "right_triangle"  			=> __("Right Triangle", "virtue"),
               "tilt_left"     				=> __("Tilt Left", "virtue"),
               "tilt_right"  				=> __("Tilt Right", "virtue"),
               "center_small_triangle"  	=> __("Center Small Triangle", "virtue"),
               "three_small_triangle"  	=> __("Three Small Triangle", "virtue"),
                ),
      );
  $fields['next_row_background_color'] = array(
        'name'      => __('Next Row Background Color', 'virtue'),
        'type'      => 'color',
        'group'     => 'design',
        'default'   => 'none',
        'priority'  => 10.5,
      );
  return $fields;
}
add_filter('siteorigin_panels_row_style_fields', 'kad_panels_row_background_styles');
function kad_panels_remove_row_background_styles($fields) {
 unset( $fields['background_image_attachment'] );
 unset( $fields['background_display'] );
 unset( $fields['padding'] );
 unset( $fields['border_color'] );
 return $fields;
}



function virtue_panels_row_background_styles_sep($attributes, $args) {
	$attributes['style'] = '';
	$attributes['class'] = 'panel-row-style kt-row-style-no-padding';
	if(!empty($args['background_image']) || !empty($args['background_image_url'] )) {
	   	if(!empty($args['background_image'])){
	    	$url = wp_get_attachment_image_src( $args['background_image'], 'full' );
	    } else {
	    	$url = false;
	    }
	    if($url == false ) {
	        $attributes['style'] .= 'background-image: url(' . $args['background_image_url'] . ');';
	    } else {
	        $attributes['style'] .= 'background-image: url(' . $url[0] . ');';
	    }
      	if(!empty($args['background_image_style'])) {
            switch( $args['background_image_style'] ) {
              	case 'no-repeat':
                	$attributes['style'] .= 'background-repeat: no-repeat;';
                break;
              	case 'repeat':
                	$attributes['style'] .= 'background-repeat: repeat;';
                break;
              	case 'repeat-x':
                	$attributes['style'] .= 'background-repeat: repeat-x;';
                break;
              	case 'repeat-y':
                	$attributes['style'] .= 'background-repeat: repeat-y;';
                break;
                case 'contain':
                	$attributes['style'] .= 'background-repeat: no-repeat;';
                	$attributes['style'] .= 'background-size: contain;';
                break;
              	case 'cover':
                	$attributes['style'] .= 'background-size: cover;';
                break;
              	case 'parallax':
                	$attributes['class'] .= ' kt-parallax-stellar';
                	$attributes['data-ktstellar-background-ratio'] = '0.5';
                break;
            }
        }
        if(!empty($args['background_image_position'])) {
            $attributes['style'] .= 'background-position: '.$args['background_image_position'].';';
        }
  	}
	if( !empty( $args['background'] ) ) {
		$attributes['style'] .= 'background-color:' . $args['background']. ';';
	}
	if( !empty( $args['bottom_margin'] ) ) {
		$attributes['style'] .= 'margin-bottom:' . $args['bottom_margin']. ';';
	}
	if( isset( $args['vertical_gutter'] ) && 'no-margin' == $args['vertical_gutter'] ) {
		$attributes['class'] .= ' kt-no-vertical-gutter';
	}
	if( !empty( $args['id'] ) ) {
		$attributes['id'] = $args['id'];
	}
	if( (!empty( $args['row_stretch']) && $args['row_stretch'] == 'full') || (!empty( $args['row_stretch']) && $args['row_stretch'] == 'full-stretched' )  ) {
	    	$attributes['style'] .= 'visibility: hidden;';
	  	}
  	if( !empty( $args['row_stretch'] ) ) {
  		$attributes['class'] .= ' siteorigin-panels-stretch';
    	$attributes['data-stretch-type'] = $args['row_stretch'];
    	wp_enqueue_script('siteorigin-panels-front-styles');
  	}
  	if(!empty( $args['row_stretch']) && $args['row_stretch'] == 'full') {
    	$attributes['class'] .= ' kt-panel-row-stretch';
  	}
  	if(!empty( $args['row_stretch']) && $args['row_stretch'] == 'full-stretched') {
    	$attributes['class'] .= ' kt-panel-row-full-stretch';
  	}
  	if(!empty($args['padding_top']) || !empty($args['padding_bottom']) || !empty($args['padding_left']) || !empty($args['padding_right'])) {
		$attributes['class'] .= ' panel-widget-style';
	}
 	if(!empty($args['border_top'])){
   		$attributes['style'] .= 'border-top: '.esc_attr($args['border_top']).' solid; ';
 	}
	if(!empty($args['border_top_color'])){
   		$attributes['style'] .= 'border-top-color: '.$args['border_top_color'].'; ';
 	}
 	if(!empty($args['border_bottom'])){
   		$attributes['style'] .= 'border-bottom: '.esc_attr($args['border_bottom']).' solid; ';
 	}
  	if(!empty($args['border_bottom_color'])){
   		$attributes['style'] .= 'border-bottom-color: '.$args['border_bottom_color'].'; ';
 	}

  	return $attributes;
}

function virtue_panels_row_background_styles_attributes($attributes, $args) {
	if(isset($args['row_separator']) && !empty($args['row_separator']) && $args['row_separator'] != 'none') {
		$attributes = array(
			'style' => '',
			'class' => array(),
			);
		if( !empty($args['row_css']) ){
		preg_match_all('/^(.+?):(.+?);?$/m', $args['row_css'], $matches);

		if(!empty($matches[0])){
				for($i = 0; $i < count($matches[0]); $i++) {
					$attributes['style'] .= $matches[1][$i] . ':' . $matches[2][$i] . ';';
				}
			}
		}
		if( !empty( $args['class'] ) ) {
			$attributes['class'] = array_merge( $attributes['class'], explode(' ', $args['class']) );
		}

	  	if( ! empty( $args[ 'padding' ] ) || ! empty( $args[ 'padding_top' ] ) || ! empty( $args[ 'padding_bottom' ] ) || ! empty( $args[ 'mobile_padding' ] ) ) {
			$attributes['class'][] = 'panel-row-style';
		}

		return $attributes;
	} else {
	  	if(!empty($args['background_image']) || !empty($args['background_image_url'] )) {
	  		if(!empty($args['background_image'])){
		    	$url = wp_get_attachment_image_src( $args['background_image'], 'full' );
		    } else {
		    	$url = false;
		    }
		    if($url == false ) {
		        $attributes['style'] .= 'background-image: url(' . $args['background_image_url'] . ');';
		    } else {
		        $attributes['style'] .= 'background-image: url(' . $url[0] . ');';
		    }
	      	if(!empty($args['background_image_style'])) {
	            switch( $args['background_image_style'] ) {
	              	case 'no-repeat':
	                	$attributes['style'] .= 'background-repeat: no-repeat;';
	                break;
	              	case 'repeat':
	                	$attributes['style'] .= 'background-repeat: repeat;';
	                break;
	              	case 'repeat-x':
	                	$attributes['style'] .= 'background-repeat: repeat-x;';
	                break;
	              	case 'repeat-y':
	                	$attributes['style'] .= 'background-repeat: repeat-y;';
	                break;
	                case 'contain':
	                	$attributes['style'] .= 'background-repeat: no-repeat;';
	                	$attributes['style'] .= 'background-size: contain;';
	                break;
	              	case 'cover':
	                	$attributes['style'] .= 'background-size: cover;';
	                break;
	              	case 'parallax':
	                	$attributes['class'][] .= 'kt-parallax-stellar';
	                	$attributes['data-ktstellar-background-ratio'] = '0.5';
	                break;
	            }
	        }
	        if(!empty($args['background_image_position'])) {
	            $attributes['style'] .= 'background-position: '.$args['background_image_position'].';';
	        }
	  	}
	  	if( (!empty( $args['row_stretch']) && $args['row_stretch'] == 'full') || (!empty( $args['row_stretch']) && $args['row_stretch'] == 'full-stretched' )  ) {
	    	$attributes['style'] .= 'visibility: hidden;';
	  	}
	  	if( isset( $args['vertical_gutter'] ) && 'no-margin' == $args['vertical_gutter'] ) {
			$attributes['class'][] .= 'kt-no-vertical-gutter';
		}
	  	if(!empty( $args['row_stretch']) && $args['row_stretch'] == 'full') {
	    	$attributes['class'][] .= 'kt-panel-row-stretch';
	  	}
	  	if(!empty( $args['row_stretch']) && $args['row_stretch'] == 'full-stretched') {
	    	$attributes['class'][] .= 'kt-panel-row-full-stretch';
	  	}
	  	if(!empty($args['padding_top']) || !empty($args['padding_bottom']) || !empty($args['padding_left']) || !empty($args['padding_right'])) {
			$attributes['class'][] .= 'panel-widget-style';
		}
	 	if(!empty($args['border_top'])){
	   		$attributes['style'] .= 'border-top: '.esc_attr($args['border_top']).' solid; ';
	 	}
		if(!empty($args['border_top_color'])){
	   		$attributes['style'] .= 'border-top-color: '.$args['border_top_color'].'; ';
	 	}
	 	if(!empty($args['border_bottom'])){
	   		$attributes['style'] .= 'border-bottom: '.esc_attr($args['border_bottom']).' solid; ';
	 	}
	  	if(!empty($args['border_bottom_color'])){
	   		$attributes['style'] .= 'border-bottom-color: '.$args['border_bottom_color'].'; ';
	 	}

	  	return $attributes;
	}
}
add_filter('siteorigin_panels_row_style_attributes', 'virtue_panels_row_background_styles_attributes', 10, 2);

add_filter('siteorigin_panels_css_row_margin_bottom', 'virtue_panels_row_bottom_margin', 20, 2);
function virtue_panels_row_bottom_margin($margin, $panelsdata) {
	if(isset($panelsdata['style']['row_separator']) && !empty($panelsdata['style']['row_separator']) && $panelsdata['style']['row_separator'] != 'none') {
		$margin = '0';
	}
	return $margin;
}


add_filter('siteorigin_panels_row_style_fields', 'kad_panels_remove_row_background_styles');
function kad_add_padding_css($css, $panels_data, $post_id ) {
		// Add in the row padding styling
		foreach( $panels_data[ 'grids' ] as $i => $row ) {
			if( empty( $row[ 'style' ] ) ) continue;

			if( ! empty( $row['style']['padding_top'] ) ) {
				$css->add_row_css( $post_id, $i, '> .panel-row-style', array(
					'padding-top' => $row['style']['padding_top']
				) );
			}
			if( ! empty( $row['style']['padding_bottom'] ) ) {
				$css->add_row_css( $post_id, $i, '> .panel-row-style', array(
					'padding-bottom' => $row['style']['padding_bottom']
				) );
			}
			if( ! empty( $row['style']['padding_left'] ) ) {
				$css->add_row_css( $post_id, $i, '> .panel-row-style', array(
					'padding-left' => $row['style']['padding_left']
				) );
			}
			if( ! empty( $row['style']['padding_right'] ) ) {
				$css->add_row_css( $post_id, $i, '> .panel-row-style', array(
					'padding-right' => $row['style']['padding_right']
				) );
			}
			if( ! empty( $row['style']['background'] ) ) {
				if(isset($row['style']['row_separator']) && !empty($row['style']['row_separator']) && $row['style']['row_separator'] != 'none') {
					$css->add_row_css( $post_id, $i, '> .panel-row-style', array(
						'background' => 'transparent'
				) );
				}
			}
		}

		return $css;
}
add_filter('siteorigin_panels_css_object', 'kad_add_padding_css', 10, 3);

add_filter('siteorigin_panels_before_row', 'virtue_panels_separator', 10, 3);
function virtue_panels_separator($content, $panelsdata, $attributes) {
	if(isset($panelsdata['style']['row_separator']) && !empty($panelsdata['style']['row_separator']) && $panelsdata['style']['row_separator'] != 'none') {
		$att = virtue_panels_row_background_styles_sep(null, $panelsdata['style']);
		$content =  '<div ';
		foreach ( $attributes as $name => $value ) {
			if($name == 'id') {
				$content .= $name.'="'.esc_attr('sep-'.$attributes['id']).'" ';
			} else {
				$content .= $name.'="'.esc_attr($value).'" ';
			}
		}
		$content .= '>';
		$content .=  '<div ';
		foreach ( $att as $name => $value ) {
				$content .= $name.'="'.esc_attr($value).'" ';
		}
		$content .= '>';
		$content .=  '<div class="inner-sep-content-wrap">';
	}
	return $content;
}
add_filter('siteorigin_panels_after_row', 'virtue_panels_separator_after', 10, 3);
function virtue_panels_separator_after($content, $panelsdata, $attributes) {
	if(isset($panelsdata['style']['row_separator']) && !empty($panelsdata['style']['row_separator']) && $panelsdata['style']['row_separator'] != 'none') {
		if(isset($panelsdata['style']['next_row_background_color']) && !empty($panelsdata['style']['next_row_background_color']) ) {
			$fill = $panelsdata['style']['next_row_background_color'];
		} else {
			$fill = '#fff';
		}
		if(isset($panelsdata['style']['row_stretch']) && !empty($panelsdata['style']['row_stretch']) ) {
			$class = 'siteorigin-panels-stretch';
		} else {
			$class = '';
		}
		if($panelsdata['style']['row_separator'] == 'center_triangle') {
			$svg = '<svg style="fill:'.esc_attr($fill).';" viewBox="0 0 100 100" preserveAspectRatio="none"><path class="large-center-triangle" d="M0 0 L50 90 L100 0 V100 H0"/></svg>';
		} else if($panelsdata['style']['row_separator'] == 'center_triangle_double') {
			$svg = '<svg style="fill:'.esc_attr($fill).';" viewBox="0 0 100 100" preserveAspectRatio="none"><path class="large-center-triangle" d="M0 0 L50 90 L100 0 V100 H0"/><path class="second-large-center-triangle" d="M0 40 L50 90 L100 40 V100 H0"/></svg>';
		} else if($panelsdata['style']['row_separator'] == 'center_small_triangle') {
			if(isset($panelsdata['style']['background']) && !empty($panelsdata['style']['background']) ) {
				$background = $panelsdata['style']['background'];
			} else {
				$background = '#fff';
			}
			$svg = '<div class="sep-triangle-bottom" style="border-top-color:'.$background.'"></div>';
		} else if($panelsdata['style']['row_separator'] == 'three_small_triangle') {
			if(isset($panelsdata['style']['background']) && !empty($panelsdata['style']['background']) ) {
				$background = $panelsdata['style']['background'];
			} else {
				$background = '#fff';
			}
			$svg = '<div class="sep-triangle-bottom left-small" style="border-top-color:'.$background.'"></div><div class="sep-triangle-bottom" style="border-top-color:'.$background.'"></div><div class="sep-triangle-bottom right-small" style="border-top-color:'.$background.'"></div>';
		} else if($panelsdata['style']['row_separator'] == 'left_triangle') {
			$svg = '<svg style="fill:'.esc_attr($fill).';" viewBox="0 0 2000 100" preserveAspectRatio="none"><polygon xmlns="http://www.w3.org/2000/svg" points="600,90 0,0 0,100 2000,100 2000,0 "></polygon></svg>';
		} else if($panelsdata['style']['row_separator'] == 'right_triangle') {
			$svg = '<svg style="fill:'.esc_attr($fill).';" viewBox="0 0 2000 100" preserveAspectRatio="none"><polygon xmlns="http://www.w3.org/2000/svg" points="600,90 0,0 0,100 2000,100 2000,0 "></polygon></svg>';
		} else if($panelsdata['style']['row_separator'] == 'tilt_right' || $panelsdata['style']['row_separator'] == 'tilt_left') {
			$svg = '<svg style="fill:'.esc_attr($fill).';" viewBox="0 0 100 100" preserveAspectRatio="none"><path class="large-angle" d="M0 0 L100 90 L100 0 V100 H0"/></svg>';
		}
		
			$content .= '<div class="panel-row-style kt-row-style-no-padding kt_sep_panel sep_'.esc_attr($panelsdata['style']['row_separator']).' '.esc_attr($class).'" data-stretch-type="full-stretched">';
				$content .= $svg;
			$content .= '</div>';
		$content .= '</div>';
		$content .= '</div>';
		$content .= '</div>';
	}
	return $content;
}
/*
function kad_panels_widget_styles($fields) {
  $fields['margin_bottom'] = array(
        'name'      => __('Margin Bottom', 'virtue'),
        'description' => sprintf( __('Space below the widget if two widgets in column. Default is %spx.', 'virtue'), siteorigin_panels_setting( 'margin-bottom' ) ),
        'type'      => 'measurement',
        'group'     => 'layout',
        'priority'  => 8.5,
  );
  return $fields;
}
add_filter('siteorigin_panels_widget_style_fields', 'kad_panels_widget_styles' );
function kad_panels_widget_style_attributes($attributes, $args) {
    if( !empty( $args['margin_bottom'] ) ) {
        $str = preg_replace('/[^0-9.]+/', '', $args['margin_bottom']);
        $default = preg_replace('/[^0-9.]+/', '', siteorigin_panels_setting( 'margin-bottom' ));
        $finalmargin = floor($str - $default);
        $attributes['style'] .= 'margin-bottom: ' . esc_attr($finalmargin) . 'px;';
    }
    return $attributes;
}
add_filter('siteorigin_panels_widget_style_attributes', 'kad_panels_widget_style_attributes', 10, 2);
*/

remove_action( 'siteorigin_panels_before_interface', 'siteorigin_panels_update_notice'); 
add_filter( 'siteorigin_premium_upgrade_teaser', 'kt_siteorigin_panels_remove_update_notice');
function kt_siteorigin_panels_remove_update_notice() { 
	return false; 
}
function kadence_siteorigin_panels_add_recommended_widgets($widgets){
	$kt_widgets = array(
		'Kadence_Contact_Widget',
	  	'Kadence_Social_Widget',
	  	'Kadence_Recent_Posts_Widget',
	  	'Kadence_Testimonial_Slider_Widget',
	  	'Kadence_Image_Grid_Widget',
	  	'Simple_About_With_Image',
	  	'kad_gallery_widget',
	  	'kad_carousel_widget',
	  	'kad_infobox_widget',
	  	'kad_gmap_widget',
	  	'kad_calltoaction_widget',
	  	'kad_imgmenu_widget',
	  	'kad_split_content_widget',
	  	'kad_icon_flip_box_widget',
	  	'kad_tabs_content_widget',
  	);

	foreach($kt_widgets as $kt_widget) {
		if( isset( $widgets[$kt_widget] ) ) {
			$widgets[$kt_widget]['groups'] = array('kt_themes');
			$widgets[$kt_widget]['icon'] = 'dashicons dashicons-star-empty';
		}
	}
	return $widgets;
}
add_filter('siteorigin_panels_widgets', 'kadence_siteorigin_panels_add_recommended_widgets');
function kadence_siteorigin_panels_add_widgets_dialog_tabs($tabs){

	$tabs['kt_themes'] = array(
		'title' => __('Theme Widgets', 'virtue'),
		'filter' => array(
			'groups' => array('kt_themes')
		)
	);
	
	return $tabs;
}
add_filter('siteorigin_panels_widget_dialog_tabs', 'kadence_siteorigin_panels_add_widgets_dialog_tabs', 30);