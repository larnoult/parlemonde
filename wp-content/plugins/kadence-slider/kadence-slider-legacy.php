<?php

function kadslider_post_init() {
  $slidelabels = array(
    'name' =>  __('Kadence Slider', 'kadence-slider'),
    'singular_name' => __('Kadence Slider Item', 'kadence-slider'),
    'add_new' => __('Add New Slider', 'kadence-slider'),
    'add_new_item' => __('Add New Slider Item', 'kadence-slider'),
    'edit_item' => __('Edit Slider Item', 'kadence-slider'),
    'new_item' => __('New Slider Item', 'kadence-slider'),
    'all_items' => __('All Sliders', 'kadence-slider'),
    'view_item' => __('View Slider Item', 'kadence-slider'),
    'search_items' => __('Search Slider', 'kadence-slider'),
    'not_found' =>  __('No Slider Item found', 'kadence-slider'),
    'not_found_in_trash' => __('No Slider Items found in Trash', 'kadence-slider'),
    'parent_item_colon' => '',
    'menu_name' => __('Kadence Slider', 'kadence-slider')
  );

  $sliderargs = array(
    'labels' => $slidelabels,
    'public' => false,
    'publicly_queryable' => false,
    'show_ui' => true, 
    'exclude_from_search' => true,
    'show_in_menu' => true, 
    'query_var' => true,
    'rewrite'  => false,
    'has_archive' => false, 
    'capability_type' => 'post', 
    'hierarchical' => false,
    'show_in_menu'=>false,
    'menu_position' => 82,
    'menu_icon' =>  'dashicons-images-alt2',
    'supports' => array( 'title')
  ); 

  register_post_type( 'kadslider', $sliderargs );
}
add_action( 'init', 'kadslider_post_init', 10 );



add_action( 'cmb2_render_slider_useage', 'kt_cmb_render_slider_useage', 10, 5 );
function kt_cmb_render_slider_useage($field, $meta, $object_id, $object_type, $field_type_object) {
    global $post;
	echo '<code>';
	echo '[kadence_slider id="' .$post->ID. '"]';
	echo '</code>';
}

add_action( 'cmb2_render_text_number', 'kt_slider_render_text_number', 10, 5 );
function kt_slider_render_text_number($field, $meta, $object_id, $object_type, $field_type_object) {
    echo $field_type_object->input( array( 'class' => 'cmb_text_small', 'type' => 'number' ) );
}

// validate the field
add_filter( 'cmb2_validate_text_number', 'sm_cmb_validate_text_number' );
function sm_cmb_validate_text_number( $new ) {
   $bnew = preg_replace("/[^0-9]/","",$new);

    return $new;
}

add_filter( 'cmb2_admin_init', 'kadence_slider_metaboxes');
function kadence_slider_metaboxes(){
	$prefix = '_kt_slider_';
	$kt_slider_settings = new_cmb2_box( array(
		'id'         => 'slider_settings',
		'title'      => __( "Basic Slider Settings", 'kadence-slider' ),
		'object_types'      => array( 'kadslider' ), // Post type
		'priority'   => 'high',
	) );
	$kt_slider_settings->add_field( array(
		'name' => __( "Slider Shortcode", 'kadence-slider' ),
		'desc' => __( "Subtitle will go below post title", 'kadence-slider' ),
		'id'   => $prefix . 'slider_useage',
		'type' => 'slider_useage',
	) );
	$kt_slider_settings->add_field( array(
		'name' => __( "Slider Max Height", 'kadence-slider' ),
		'desc' => __( "This sets a max height for your slider", 'kadence-slider' ),
		'id'   => $prefix . 'max_height',
		'type' => 'text_number',
		'default' => '450',
	) );
	$kt_slider_settings->add_field( array(
		'name' => __( "Slider Max Width", 'kadence-slider' ),
		'desc' => __( "This sets a max width for your slider", 'kadence-slider' ),
		'id'   => $prefix . 'max_width',
		'type' => 'text_number',
		'default' => '1140',
	) );
	$kt_slider_settings->add_field( array(
		'name' => __( "Full Width", 'kadence-slider' ),
		'desc' => __( "This will ignore the max width setting.", 'kadence-slider' ),
		'id'   => $prefix . 'fullwidth',
		'type' => 'checkbox',
	) );
	$kt_slider_settings->add_field( array(
		'name' => __( "Full Height", 'kadence-slider' ),
		'desc' => __( "This will ignore the max height setting.", 'kadence-slider' ),
		'id'   => $prefix . 'fullheight',
		'type' => 'checkbox',
	) );
	$kt_slider_settings->add_field( array(
		'name' => __( "Respect Image Aspect Ratio", 'kadence-slider' ),
		'desc' => __( "This will ignore the full height and display the images based on the uploaded ratio once they fall below the max height. You can not use with fullheight or parallax", 'kadence-slider' ),
		'id'   => $prefix . 'respect_ratio',
		'type' => 'checkbox',
	) );
	$kt_slider_settings->add_field( array(
		'name' => __( "Auto Play", 'kadence-slider' ),
		'desc' => __( "Scroll through slides automatically.", 'kadence-slider' ),
		'id'   => $prefix . 'auto_play',
		'type' => 'select',
		'options' => array(
			'true' => __("True", 'kadence-slider' ),
			'false' => __("False", 'kadence-slider' ), 
		),
	) );
	$kt_slider_settings->add_field( array(
		'name' => __( "Slide Pause Time", 'kadence-slider' ),
		'desc' => __( "This sets the time each slide is displayed in milliseconds.", 'kadence-slider' ),
		'id'   => $prefix . 'pause_time',
		'type' => 'text_number',
		'default' => '9000',
	) );
	$kt_slider_settings->add_field( array(
		'name' => __( "Enable Parallax", 'kadence-slider' ),
		'desc' => __( "This fix the slider image from scrolling.", 'kadence-slider' ),
		'id'   => $prefix . 'parallax',
		'type' => 'checkbox',
	) );
	$kt_slider_settings->add_field( array(
		'name' => __( "Hide Controls?", 'kadence-slider' ),
		'id'   => $prefix . 'hidecontrols',
		'type' => 'checkbox',
	) );
}
add_filter( 'cmb2_admin_init', 'kadence_slider_slides_metaboxes');
function kadence_slider_slides_metaboxes(){
	$prefix = '_kt_slider_';
	$kt_slider_slides = new_cmb2_box( array(
		'id'         => 'slider_slides',
		'title'      => __( "Slider Slides", 'kadence-slider' ),
		'object_types'      => array( 'kadslider' ), // Post type
		'priority'   => 'high',
	) );
	$kt_slider_slides_group = $kt_slider_slides->add_field( array(
		'id'          => $prefix . 'slides',
		'type'        => 'group',
		'options'     => array(
			'group_title'   => __( 'Slide {#}', 'kadence-slider' ), 
			'add_button'    => __( 'Add Another Slide', 'kadence-slider' ),
			'remove_button' => __( 'Remove Slide', 'kadence-slider' ),
			'sortable'      => true, 
		),
	) );
	$kt_slider_slides->add_group_field( $kt_slider_slides_group, array(
		'name' => __('Slider Caption Heading', 'kadence-slider' ),
		'id'   => 'title',
		'type' => 'text',
	) );
	$kt_slider_slides->add_group_field( $kt_slider_slides_group, array(
		'name' => __('Slider Caption Description', 'kadence-slider' ),
		'description' => __('A short description for this slide', 'kadence-slider' ),
		'id'   => 'description',
		'type' => 'textarea_small',
	) );
	$kt_slider_slides->add_group_field( $kt_slider_slides_group, array(
		'name' => __('Slide Image', 'kadence-slider' ),
		'id'   => 'image',
		'type' => 'file',
	) );
	$kt_slider_slides->add_group_field( $kt_slider_slides_group, array(
		'name' => __('Primary Button Text', 'kadence-slider' ),
		'id'   => 'button_txt',
		'type' => 'text',
	) );
	$kt_slider_slides->add_group_field( $kt_slider_slides_group, array(
		'name' => __('Primary Button Link', 'kadence-slider' ),
		'id'   => 'button_link',
		'type' => 'text',
	) );
	$kt_slider_slides->add_group_field( $kt_slider_slides_group, array(
		'name' => __('Secondary Button Text', 'kadence-slider' ),
		'id'   => 'button_txt_2',
		'type' => 'text',
	) );
	$kt_slider_slides->add_group_field( $kt_slider_slides_group, array(
		'name' => __('Secondary Button Link', 'kadence-slider' ),
		'id'   => 'button_link_2',
		'type' => 'text',
	) );
	$kt_slider_slides->add_group_field( $kt_slider_slides_group, array(
		'name' => __('Caption Placement', 'kadence-slider' ),
		'desc' => __('This positions the caption in your slide area', 'kadence-slider'),
		'id'   => 'caption_placement',
		'type'    => 'select',
		'options' => array(
			'cc' => __("Center Center", 'kadence-slider' ),
			'tc' => __("Top Center", 'kadence-slider' ), 
			'bc' => __("Bottom Center", 'kadence-slider' ), 
			'lc' => __("Left Center", 'kadence-slider' ), 
			'rc' => __("Right Center", 'kadence-slider' ), 
			'tl' => __("Top Left", 'kadence-slider' ), 
			'tr' => __("Top Right", 'kadence-slider' ), 
			'bl' => __("Bottom Left", 'kadence-slider' ), 
			'br' => __("Bottom Right", 'kadence-slider' ), 
		),
	) );
	$kt_slider_slides->add_group_field( $kt_slider_slides_group, array(
		'name' => __('Caption In Animation', 'kadence-slider' ),
		'desc' => __('This determines the caption animation as it starts the slide', 'kadence-slider'),
		'id'   => 'caption_animation_in',
		'type'    => 'select',
		'options' => array(
			'fadeIn' => __("fadeIn", 'kadence-slider' ),
			'fadeInDown' => __("fadeInDown", 'kadence-slider' ), 
			'fadeInDownBig' => __("fadeInDownBig", 'kadence-slider' ), 
			'fadeInLeft' => __("fadeInLeft", 'kadence-slider' ), 
			'fadeInLeftBig' => __("fadeInLeftBig", 'kadence-slider' ), 
			'fadeInRight' => __("fadeInRight", 'kadence-slider' ), 
			'fadeInRightBig' => __("fadeInRightBig", 'kadence-slider' ), 
			'fadeInUp' => __("fadeInUp", 'kadence-slider' ), 
			'fadeInUpBig' => __("fadeInUpBig", 'kadence-slider' ), 
			'bounceIn' => __("bounceIn", 'kadence-slider' ), 
			'bounceInDown' => __("bounceInDown", 'kadence-slider' ), 
			'bounceInLeft' => __("bounceInLeft", 'kadence-slider' ), 
			'bounceInRight' => __("bounceInRight", 'kadence-slider' ), 
			'bounceInUp' => __("bounceInUp", 'kadence-slider' ), 
			'rotateIn' => __("rotateIn", 'kadence-slider' ), 
			'rotateInDownLeft' => __("rotateInDownLeft", 'kadence-slider' ), 
			'rotateInDownRight' => __("rotateInDownRight", 'kadence-slider' ), 
			'rotateInUpLeft' => __("rotateInUpLeft", 'kadence-slider' ), 
			'rotateInUpRight' => __("rotateInUpRight", 'kadence-slider' ), 
			'slideInDown' => __("slideInDown", 'kadence-slider' ), 
			'slideInLeft' => __("slideInLeft", 'kadence-slider' ), 
			'slideInRight' => __("slideInRight", 'kadence-slider' ), 
			'rollIn' => __("rollIn", 'kadence-slider' ), 
		),
	) );
	$kt_slider_slides->add_group_field( $kt_slider_slides_group, array(
		'name' => __('Caption Out Animation', 'kadence-slider' ),
		'desc' => __('This determines the caption animation as it switches to the next slide', 'kadence-slider'),
		'id'   => 'caption_animation_out',
		'type'    => 'select',
		'options' => array(
			'fadeOut' => __("fadeOut", 'kadence-slider' ),
			'fadeOutDown' => __("fadeOutDown", 'kadence-slider' ), 
			'fadeOutDownBig' => __("fadeOutDownBig", 'kadence-slider' ), 
			'fadeOutLeft' => __("fadeOutLeft", 'kadence-slider' ), 
			'fadeOutLeftBig' => __("fadeOutLeftBig", 'kadence-slider' ), 
			'fadeOutRight' => __("fadeOutRight", 'kadence-slider' ), 
			'fadeOutRightBig' => __("fadeOutRightBig", 'kadence-slider' ), 
			'fadeOutUp' => __("fadeOutUp", 'kadence-slider' ), 
			'fadeOutUpBig' => __("fadeOutUpBig", 'kadence-slider' ), 
			'bounceOut' => __("bounceOut", 'kadence-slider' ), 
			'bounceOutDown' => __("bounceOutDown", 'kadence-slider' ), 
			'bounceOutLeft' => __("bounceOutLeft", 'kadence-slider' ), 
			'bounceOutRight' => __("bounceOutRight", 'kadence-slider' ), 
			'bounceOutUp' => __("bounceOutUp", 'kadence-slider' ), 
			'rotateOut' => __("rotateOut", 'kadence-slider' ), 
			'rotateOutDownLeft' => __("rotateOutDownLeft", 'kadence-slider' ), 
			'rotateOutDownRight' => __("rotateOutDownRight", 'kadence-slider' ), 
			'rotateOutUpLeft' => __("rotateOutUpLeft", 'kadence-slider' ), 
			'rotateOutUpRight' => __("rotateOutUpRight", 'kadence-slider' ), 
			'slideOutDown' => __("slideOutDown", 'kadence-slider' ), 
			'slideOutLeft' => __("slideOutLeft", 'kadence-slider' ), 
			'slideOutRight' => __("slideOutRight", 'kadence-slider' ), 
			'rollOut' => __("rollOut", 'kadence-slider' ), 
		),
	) );
}
add_filter( 'cmb2_admin_init', 'kadence_slider_buttons_metaboxes');
function kadence_slider_buttons_metaboxes(){
	$prefix = '_kt_slider_';
	$kt_slider_buttons = new_cmb2_box( array(
		'id'         => 'button_slider_settings',
		'title'      => __( "Button Color Settings", 'kadence-slider' ),
		'object_types'      => array( 'kadslider' ), // Post type
		'priority'   => 'high',
	) );
	$kt_slider_buttons->add_field( array(
		'name' => __( "Primary Button Text Color", 'kadence-slider' ),
		'id'   => $prefix . 'btn_txt_color',
		'type' => 'colorpicker',
	) );
	$kt_slider_buttons->add_field( array(
		'name' => __( "Primary Button Background Color", 'kadence-slider' ),
		'id'   => $prefix . 'btn_bg_color',
		'type' => 'colorpicker',
	) );
	$kt_slider_buttons->add_field( array(
		'name' => __( "Primary Button Border Color", 'kadence-slider' ),
		'id'   => $prefix . 'btn_bord_color',
		'type' => 'colorpicker',
	) );
	$kt_slider_buttons->add_field( array(
		'name' => __( "Primary Button Hover Text Color", 'kadence-slider' ),
		'id'   => $prefix . 'btn_txt_color_h',
		'type' => 'colorpicker',
	) );
	$kt_slider_buttons->add_field( array(
		'name' => __( "Primary Button Hover Background Color", 'kadence-slider' ),
		'id'   => $prefix . 'btn_bg_color_h',
		'type' => 'colorpicker',
	) );
	$kt_slider_buttons->add_field( array(
		'name' => __( "Primary Button Hover Border Color", 'kadence-slider' ),
		'id'   => $prefix . 'btn_bord_color_h',
		'type' => 'colorpicker',
	) );
	$kt_slider_buttons->add_field( array(
		'name' => __( "Secondary Button Text Color", 'kadence-slider' ),
		'id'   => $prefix . 'btn_txt_color_2',
		'type' => 'colorpicker',
	) );
	$kt_slider_buttons->add_field( array(
		'name' => __( "Secondary Button Background Color", 'kadence-slider' ),
		'id'   => $prefix . 'btn_back_color_2',
		'type' => 'colorpicker',
	) );
	$kt_slider_buttons->add_field( array(
		'name' => __( "Secondary Button Border Color", 'kadence-slider' ),
		'id'   => $prefix . 'btn_bg_color_2',
		'type' => 'colorpicker',
	) );
	$kt_slider_buttons->add_field( array(
		'name' => __( "Secondary Button Hover Text Color", 'kadence-slider' ),
		'id'   => $prefix . 'btn_txt_color_2_h',
		'type' => 'colorpicker',
	) );
	$kt_slider_buttons->add_field( array(
		'name' => __( "Secondary Button Hover Background Color", 'kadence-slider' ),
		'id'   => $prefix . 'btn_back_color_2_h',
		'type' => 'colorpicker',
	) );
	$kt_slider_buttons->add_field( array(
		'name' => __( "Secondary Button Hover Border Color", 'kadence-slider' ),
		'id'   => $prefix . 'btn_bg_color_2_h',
		'type' => 'colorpicker',
	) );
}

add_filter( 'cmb2_admin_init', 'kadence_slider_captions_metaboxes');
function kadence_slider_captions_metaboxes(){
	$prefix = '_kt_slider_';
	$kt_slider_captions = new_cmb2_box( array(
		'id'         => 'caption_slider_settings',
		'title'      => __( "Slider Caption Settings", 'kadence-slider' ),
		'object_types'      => array( 'kadslider' ), // Post type
		'priority'   => 'high',
	) );
	$kt_slider_captions->add_field( array(
		'name' => __( "Caption Header Text Color", 'kadence-slider' ),
		'id'   => $prefix . 'header_txt_color',
		'type' => 'colorpicker',
	) );
	$kt_slider_captions->add_field( array(
		'name' => __( "Caption Header Text Font Size", 'kadence-slider' ),
		'id'   => $prefix . 'head_font',
		'type' => 'text_number',
		'default' => '76',
	) );
	$kt_slider_captions->add_field( array(
		'name' => __( "Caption Header Text Font Weight", 'kadence-slider' ),
		'id'   => $prefix . 'head_font_weight',
		'default' => '400',
		'type'    => 'select',
		'options' => array(
			'100' => __("100", 'kadence-slider' ),
			'200' => __("200", 'kadence-slider' ), 
			'300' => __("300", 'kadence-slider' ), 
			'400' => __("400", 'kadence-slider' ), 
			'500' => __("500", 'kadence-slider' ), 
			'600' => __("600", 'kadence-slider' ), 
			'700' => __("700", 'kadence-slider' ), 
			'800' => __("800", 'kadence-slider' ), 
			'900' => __("900", 'kadence-slider' ),  
		),
	) );
	$kt_slider_captions->add_field( array(
		'name' => __( "Caption Header Text Letter Spacing", 'kadence-slider' ),
		'id'   => $prefix . 'head_font_spacing',
		'default' => 'normal',
		'type'    => 'select',
		'options' => array(
			'normal' => __("Normal", 'kadence-slider' ),
			'1' => __("1", 'kadence-slider' ), 
			'2' => __("2", 'kadence-slider' ), 
			'3' => __("3", 'kadence-slider' ), 
			'4' => __("4", 'kadence-slider' ), 
			'5' => __("5", 'kadence-slider' ), 
			'6' => __("6", 'kadence-slider' ), 
			'7' => __("7", 'kadence-slider' ), 
			'8' => __("8", 'kadence-slider' ), 
			'9' => __("9", 'kadence-slider' ), 
			'10' => __("10", 'kadence-slider' ), 
			'11' => __("11", 'kadence-slider' ), 
			'12' => __("12", 'kadence-slider' ),  
		),
	) );
	$kt_slider_captions->add_field( array(
		'name' => __( "Caption Header Text Style", 'kadence-slider' ),
		'id'   => $prefix . 'caption_header_style',
		'default' => 'normal',
		'type'    => 'select',
		'options' => array(
			'normal' => __("Normal", 'kadence-slider' ),
			'border' => __("Top and Bottom Border", 'kadence-slider' ), 
		),
	) );
	$kt_slider_captions->add_field( array(
		'name' => __( "Caption Description Text Color", 'kadence-slider' ),
		'id'   => $prefix . 'caption_txt_color',
		'type' => 'colorpicker',
	) );
	$kt_slider_captions->add_field( array(
		'name' => __( "Caption Description Text Font Size", 'kadence-slider' ),
		'id'   => $prefix . 'text_font',
		'type' => 'text_number',
		'default' => '24',
	) );
	$kt_slider_captions->add_field( array(
		'name' => __( "Caption Description Text Font Weight", 'kadence-slider' ),
		'id'   => $prefix . 'caption_text_font_weight',
		'default' => '400',
		'type'    => 'select',
		'options' => array(
			'100' => __("100", 'kadence-slider' ),
			'200' => __("200", 'kadence-slider' ), 
			'300' => __("300", 'kadence-slider' ), 
			'400' => __("400", 'kadence-slider' ), 
			'500' => __("500", 'kadence-slider' ), 
			'600' => __("600", 'kadence-slider' ), 
			'700' => __("700", 'kadence-slider' ), 
			'800' => __("800", 'kadence-slider' ), 
			'900' => __("900", 'kadence-slider' ),  
		),
	) );
	$kt_slider_captions->add_field( array(
		'name' => __( "Caption Description Text Letter Spacing", 'kadence-slider' ),
		'id'   => $prefix . 'caption_text_font_spacing',
		'default' => 'normal',
		'type'    => 'select',
		'options' => array(
			'normal' => __("Normal", 'kadence-slider' ),
			'1' => __("1", 'kadence-slider' ), 
			'2' => __("2", 'kadence-slider' ), 
			'3' => __("3", 'kadence-slider' ), 
			'4' => __("4", 'kadence-slider' ), 
			'5' => __("5", 'kadence-slider' ), 
			'6' => __("6", 'kadence-slider' ), 
			'7' => __("7", 'kadence-slider' ), 
			'8' => __("8", 'kadence-slider' ), 
			'9' => __("9", 'kadence-slider' ), 
			'10' => __("10", 'kadence-slider' ), 
			'11' => __("11", 'kadence-slider' ), 
			'12' => __("12", 'kadence-slider' ),  
		),
	) );
	$kt_slider_captions->add_field( array(
		'name' => __( "Caption Background Color", 'kadence-slider' ),
		'id'   => $prefix . 'caption_bg_color',
		'type' => 'colorpicker',
	) );
	$kt_slider_captions->add_field( array(
		'name' => __( "Caption Text Align", 'kadence-slider' ),
		'id'   => $prefix . 'caption_align',
		'type'    => 'select',
		'options' => array(
			'center' => __("Center", 'kadence-slider' ),
			'left' => __("Left", 'kadence-slider' ), 
			'right' => __("Right", 'kadence-slider' ), 
		),
	) );
	$kt_slider_captions->add_field( array(
		'name' => __( "Caption Text Shaddow", 'kadence-slider' ),
		'id'   => $prefix . 'caption_shaddow',
		'type'    => 'select',
		'options' => array(
			'true' => __("Show Text Shaddow", 'kadence-slider' ),
			'false' => __("Hide Text Shaddow", 'kadence-slider' ), 
		),
	) );
}

add_filter( 'cmb2_admin_init', 'kadence_slider_mcaptions_metaboxes');
function kadence_slider_mcaptions_metaboxes(){
	$prefix = '_kt_slider_';
	$kt_slider_mcaptions = new_cmb2_box( array(
		'id'         => 'mobile_caption_slider_settings',
		'title'      => __( "Mobile Slider Caption and Size Settings", 'kadence-slider' ),
		'object_types'      => array( 'kadslider' ), // Post type
		'priority'   => 'high',
	) );
	$kt_slider_mcaptions->add_field( array(
		'name' => __( "Tablet - Caption Header Text Font Size", 'kadence-slider' ),
		'id'   => $prefix . 'head_font_tb',
		'type' => 'text_number',
		'default' => '50',
	) );
	$kt_slider_mcaptions->add_field( array(
		'name' => __( "Tablet - Caption Description Text Font Size", 'kadence-slider' ),
		'id'   => $prefix . 'text_font_tb',
		'type' => 'text_number',
		'default' => '18',
	) );
	$kt_slider_mcaptions->add_field( array(
		'name' => __( "Phone - Caption Header Text Font Size", 'kadence-slider' ),
		'id'   => $prefix . 'head_font_pn',
		'type' => 'text_number',
		'default' => '26',
	) );
	$kt_slider_mcaptions->add_field( array(
		'name' => __( "Phone - Caption Description Text Font Size", 'kadence-slider' ),
		'id'   => $prefix . 'text_font_pn',
		'type' => 'text_number',
		'default' => '14',
	) );
	$kt_slider_mcaptions->add_field( array(
		'name' => __( "Tablet - Slider Max Height(overrides settings)", 'kadence-slider' ),
		'desc' => __('This sets a max height for your slider for tablet', 'kadence-slider'),
		'id'   => $prefix . 'max_height_tablet',
		'type' => 'text_number',
	) );
	$kt_slider_mcaptions->add_field( array(
		'name' => __( "Mobile - Slider Max Height(overrides settings)", 'kadence-slider' ),
		'desc' => __('This sets a max height for your slider for mobile', 'kadence-slider'),
		'id'   => $prefix . 'max_height_mobile',
		'type' => 'text_number',
	) );
}
add_action( 'init', 'initialize_slider_meta_boxes', 10 );
function initialize_slider_meta_boxes() {
	require_once( KADENCE_SLIDER_PATH . 'cmb/init.php');

}
function ktslidehex2rgb($hex) {
   $hex = str_replace("#", "", $hex);

   if(strlen($hex) == 3) {
      $r = hexdec(substr($hex,0,1).substr($hex,0,1));
      $g = hexdec(substr($hex,1,1).substr($hex,1,1));
      $b = hexdec(substr($hex,2,1).substr($hex,2,1));
   } else {
      $r = hexdec(substr($hex,0,2));
      $g = hexdec(substr($hex,2,2));
      $b = hexdec(substr($hex,4,2));
   }
   $rgb = array($r, $g, $b);
   return $rgb;
}
/**
 * Remove Rev Slider Metabox
 */
if ( is_admin() ) {
  function kt_remove_revolution_slider_meta_boxes() {
    remove_meta_box( 'mymetabox_revslider_0', 'kadslider', 'normal' );
  }
  add_action( 'do_meta_boxes', 'kt_remove_revolution_slider_meta_boxes' ); 
}

// Lagacy Admin CSS
function ksp_slider_edit_page(){
  if (!is_admin()) return false;
    if ( in_array( $GLOBALS['pagenow'], array( 'post.php', 'post-new.php', 'widgets.php', 'post.php', 'post-new.php' ) ) ) {
      return true;
    }
}


function kadence_slider_admin_scripts() {
  wp_enqueue_style('kadence_slider_admin', KADENCE_SLIDER_URL . 'admin/css/kad-slider-admin.css', false, '220');

}
function kadence_slider_lagacy_script_init() {
	if(is_admin()){ if(ksp_slider_edit_page()){add_action( 'admin_enqueue_scripts', 'kadence_slider_admin_scripts', 101);	}}
}
add_action('init', 'kadence_slider_lagacy_script_init');

function kad_slider_shortcode_function( $atts ) {
	extract(shortcode_atts(array(
		'id' => ''
), $atts));
	if(empty($id)) {
		return '<p class="error">' . __( 'Please specify a slider ID', 'kadence_slider' ) . '</p>';
	}
		$slides = array();
		$max_height = get_post_meta( $id, '_kt_slider_max_height', true );
		$max_width = get_post_meta( $id, '_kt_slider_max_width', true );
		$fullwidth = get_post_meta( $id, '_kt_slider_fullwidth', true );
		$respect_ratio = get_post_meta( $id, '_kt_slider_respect_ratio', true );
		$fullheight = get_post_meta( $id, '_kt_slider_fullheight', true );
		$pause_time = get_post_meta( $id, '_kt_slider_pause_time', true );
		$auto_play = get_post_meta( $id, '_kt_slider_auto_play', true );
		$btn_txt_color = get_post_meta( $id, '_kt_slider_btn_txt_color', true );
		$btn_bg_color = get_post_meta( $id, '_kt_slider_btn_bg_color', true );
		$btn_bord_color = get_post_meta( $id, '_kt_slider_btn_bord_color', true );
		$btn_txt_color_2 = get_post_meta( $id, '_kt_slider_btn_txt_color_2', true );
		$btn_bg_color_2 = get_post_meta( $id, '_kt_slider_btn_bg_color_2', true );
		$btn_back_color_2 = get_post_meta( $id, '_kt_slider_btn_back_color_2', true );
		$btn_txt_color_h = get_post_meta( $id, '_kt_slider_btn_txt_color_h', true );
		$btn_bg_color_h = get_post_meta( $id, '_kt_slider_btn_bg_color_h', true );
		$btn_bord_color_h = get_post_meta( $id, '_kt_slider_btn_bord_color_h', true );
		$btn_txt_color_2_h = get_post_meta( $id, '_kt_slider_btn_txt_color_2_h', true );
		$btn_bg_color_2_h = get_post_meta( $id, '_kt_slider_btn_bg_color_2_h', true );
		$btn_back_color_2_h = get_post_meta( $id, '_kt_slider_btn_back_color_2_h', true );
		$caption_head_font = get_post_meta( $id, '_kt_slider_head_font', true );
		$caption_head_font_weight = get_post_meta( $id, '_kt_slider_head_font_weight', true );
		$caption_head_font_spacing = get_post_meta( $id, '_kt_slider_head_font_spacing', true );
		$caption_header_txt_color = get_post_meta( $id, '_kt_slider_header_txt_color', true );
		$caption_text_font = get_post_meta( $id, '_kt_slider_text_font', true );
		$caption_text_font_weight = get_post_meta( $id, '_kt_slider_caption_text_font_weight', true );
		$caption_text_font_spacing = get_post_meta( $id, '_kt_slider_caption_text_font_spacing', true );
		$caption_caption_txt_color = get_post_meta( $id, '_kt_slider_caption_txt_color', true );
		$caption_caption_bg_color = get_post_meta( $id, '_kt_slider_caption_bg_color', true );
		$caption_caption_align = get_post_meta( $id, '_kt_slider_caption_align', true );
		$caption_caption_shaddow = get_post_meta( $id, '_kt_slider_caption_shaddow', true );
		$caption_caption_header_style = get_post_meta( $id, '_kt_slider_caption_header_style', true );
		$caption_head_font_tb = get_post_meta( $id, '_kt_slider_head_font_tb', true );
		$caption_text_font_tb = get_post_meta( $id, '_kt_slider_text_font_tb', true );
		$caption_head_font_pn = get_post_meta( $id, '_kt_slider_head_font_pn', true );
		$caption_text_font_pn = get_post_meta( $id, '_kt_slider_text_font_pn', true );
		$tabletmax_height = get_post_meta( $id, '_kt_slider_max_height_tablet', true );
		$mobilemax_height = get_post_meta( $id, '_kt_slider_max_height_mobile', true );

		$slider_parallax = get_post_meta( $id, '_kt_slider_parallax', true );
		$hidecontrols = get_post_meta( $id, '_kt_slider_hidecontrols', true );
		$data_height = $max_height;

		$slides = get_post_meta( $id, '_kt_slider_slides', true );
		$slidecount = count($slides);
		if(!empty($pause_time)) {$pause_data = $pause_time;} else{$pause_data = '9000';} 
		if(!empty($auto_play)) {$auto_play_data = $auto_play;} else{$auto_play_data = 'true';} 
		if($fullwidth) {$max_width = 'none';} else {$max_width = $max_width.'px';}
		if($fullheight) {$max_height = '600px'; $data_height_type = "full"; } else {$max_height = $max_height.'px'; $data_height_type = "normal";}

		if($slider_parallax) {$slider_parallax_class = 'kad-slider-parallax';} else {$slider_parallax_class = '';}
		$custom_slider_css = '';
		if($hidecontrols) {$custom_slider_css = '.kad-slider-'.$id.' .kad-slider-navigate, .kad-slider-'.$id.' .kad-slider ul.kad-slider-pagination {display:none !important;}';}
		if(!empty($btn_txt_color)) { $custom_slider_css .= '.kad-slider-'.$id.' .kad-slider .kad-slider-btn-01 {color:'.$btn_txt_color.';}'; }
		if(!empty($btn_bg_color)) { $custom_slider_css .= '.kad-slider-'.$id.' .kad-slider .kad-slider-btn-01 {background:'.$btn_bg_color.';}'; }
		if(!empty($btn_bord_color)) { $custom_slider_css .= '.kad-slider-'.$id.' .kad-slider .kad-slider-btn-01 {border-color:'.$btn_bord_color.';}'; }
		if(!empty($btn_txt_color_2)) { $custom_slider_css .= '.kad-slider-'.$id.' .kad-slider .kad-slider-btn-02 {color:'.$btn_txt_color_2.';}'; }
		if(!empty($btn_bg_color_2)) { $custom_slider_css .= '.kad-slider-'.$id.' .kad-slider .kad-slider-btn-02 {border-color:'.$btn_bg_color_2.';}'; }
		if(!empty($btn_back_color_2)) { $custom_slider_css .= '.kad-slider-'.$id.' .kad-slider .kad-slider-btn-02 {background:'.$btn_back_color_2.';}'; }
		if(!empty($btn_txt_color_h)) { $custom_slider_css .= '.kad-slider-'.$id.' .kad-slider .kad-slider-btn-01:hover {color:'.$btn_txt_color_h.';}'; }
		if(!empty($btn_bg_color_h)) { $custom_slider_css .= '.kad-slider-'.$id.' .kad-slider .kad-slider-btn-01:hover {background:'.$btn_bg_color_h.';}'; }
		if(!empty($btn_bord_color_h)) { $custom_slider_css .= '.kad-slider-'.$id.' .kad-slider .kad-slider-btn-01:hover {border-color:'.$btn_bord_color_h.';}'; }
		if(!empty($btn_txt_color_2_h)) { $custom_slider_css .= '.kad-slider-'.$id.' .kad-slider .kad-slider-btn-02:hover {color:'.$btn_txt_color_2_h.';}'; }
		if(!empty($btn_bg_color_2_h)) { $custom_slider_css .= '.kad-slider-'.$id.' .kad-slider .kad-slider-btn-02:hover {border-color:'.$btn_bg_color_2_h.';}'; }
		if(!empty($btn_back_color_2_h)) { $custom_slider_css .= '.kad-slider-'.$id.' .kad-slider .kad-slider-btn-02:hover {background:'.$btn_back_color_2_h.';}'; }
		if(!empty($caption_head_font)) { $custom_slider_css .= '.kad-slider-'.$id.' .kad-slider .kadcaptiontitle {font-size:'.$caption_head_font.'px;}'; }
		if(!empty($caption_head_font_weight)) { $custom_slider_css .= '.kad-slider-'.$id.' .kad-slider .kadcaptiontitle {font-weight:'.$caption_head_font_weight.';}'; }
		if(!empty($caption_head_font_spacing)) { $custom_slider_css .= '.kad-slider-'.$id.' .kad-slider .kadcaptiontitle {letter-spacing:'.$caption_head_font_spacing.';}'; }
		if(!empty($caption_header_txt_color)) { $custom_slider_css .= '.kad-slider-'.$id.' .kad-slider .kadcaptiontitle {color:'.$caption_header_txt_color.'; border-color: '.$caption_header_txt_color.';}'; }
		if(!empty($caption_text_font)) { $custom_slider_css .= '.kad-slider-'.$id.' .kad-slider .kadcaptiontext p {font-size:'.$caption_text_font.'px;}'; }
		if(!empty($caption_text_font_weight)) { $custom_slider_css .= '.kad-slider-'.$id.' .kad-slider .kadcaptiontext p {font-weight:'.$caption_text_font_weight.';}'; }
		if(!empty($caption_text_font_spacing)) { $custom_slider_css .= '.kad-slider-'.$id.' .kad-slider .kadcaptiontext p {letter-spacing:'.$caption_text_font_spacing.';}'; }
		if(!empty($caption_caption_txt_color)) { $custom_slider_css .= '.kad-slider-'.$id.' .kad-slider .kadcaptiontext p {color:'.$caption_caption_txt_color.';}'; }
		if(!empty($caption_caption_align)) { $custom_slider_css .= '.kad-slider-'.$id.' .kad-slider .caption-case-inner {text-align:'.$caption_caption_align.';}'; }
		if(!empty($caption_caption_shaddow) && $caption_caption_shaddow == 'false') { $textshaddowclass = 'kad-slider-no-textshaddow';} else {$textshaddowclass = ''; }
		if(!empty($caption_caption_header_style) && $caption_caption_header_style == 'border') { $headerstyleclass = 'kt-border-title-style';} else {$headerstyleclass = ''; }
		if(!empty($caption_caption_bg_color)) { $cc_bg_rgb = ktslidehex2rgb($caption_caption_bg_color);
			$custom_slider_css .= '.kad-slider-'.$id.' .kad-slider .kadcaptiontext p, .kad-slider-'.$id.' .kad-slider .kadcaptiontitle {background-color: rgba('.$cc_bg_rgb[0].', '.$cc_bg_rgb[1].', '.$cc_bg_rgb[2].', 0.3);}'; }


		/*
		// CASE SWITCH FOR SLIDER TYPE //
		*/
		if(isset($respect_ratio) && $respect_ratio == true) {

 		$custom_slider_css .= '@media (max-width: 992px) {';
 		if(!empty($caption_text_font_tb)) { $custom_slider_css .= '.kad-slider-'.$id.' .kad-slider .kadcaptiontext p {font-size:'.$caption_text_font_tb.'px;}'; }
 		if(!empty($caption_head_font_tb)) { $custom_slider_css .= '.kad-slider-'.$id.' .kad-slider .kadcaptiontitle {font-size:'.$caption_head_font_tb.'px;}'; }
 		
 		$custom_slider_css .= '} @media (max-width: 767px) {';
 		if(!empty($caption_head_font_pn)) { $custom_slider_css .= '.kad-slider-'.$id.' .kad-slider .kadcaptiontitle {font-size:'.$caption_head_font_pn.'px;}'; }
 		if(!empty($caption_text_font_pn)) { $custom_slider_css .= '.kad-slider-'.$id.' .kad-slider .kadcaptiontext p {font-size:'.$caption_text_font_pn.'px;}'; }
 		$custom_slider_css .= '}';

 		$data_height_type = 'ratio';

                	ob_start(); ?>
                	<style type="text/css">
                	<?php echo $custom_slider_css; ?>
                	</style>
			  	<div class="kad-slider-wrapper <?php if($slidecount == 1) {echo 'kt_slider_single_slide';}?> kad-slider-<?php echo esc_attr($id);?> kt-ratio-slider" data-ktslider-id="<?php echo esc_attr($id);?>" data-ktslider-auto-play="<?php echo esc_attr($auto_play_data);?>" data-ktslider-pause-time="<?php echo esc_attr($pause_data);?>" data-ktslider-count="<?php echo esc_attr($slidecount);?>" data-ktslider-pause-hover="false" data-ktslider-height="<?php echo esc_attr($data_height);?>" data-ktslider-height-type="<?php echo esc_attr($data_height_type);?>" style="max-width:<?php echo esc_attr($max_width);?>; margin-left: auto; margin-right:auto;">
			    	<div id="kad-slider-<?php echo esc_attr($id);?>" class="kad-slider kad-loading" style="margin-left: auto; margin-right:auto;">
			        	<ul class="kad-slider-canvas seq-canvas">
			        	<?php $slidenumber = 1;
			        	if(!empty($slides)) {
			            	foreach ($slides as $slide) :
			            		if( ! empty( $slide['image'] ) ) {
			            			$slide_image_id = ksp_get_image_id_by_link($slide['image']);
			            			$slide_image = wp_get_attachment_image_src ($slide_image_id, 'full');
			            		}
								?>
			                      <li> 
			                              <div class="kad-slide kad-slide-<?php echo esc_attr($slidenumber);?> <?php echo esc_attr($textshaddowclass).' '.esc_attr($headerstyleclass);?>">
			                              <img src="<?php echo esc_url($slide_image['0']);?>" width="<?php echo esc_attr($slide_image['1']);?>"  height="<?php echo esc_attr($slide_image['2']);?>" class="kt-ratio-img">
			                              	<div class="caption-case kad-placement-<?php echo $slide["caption_placement"];?>">
			                                	<div class="kad-slide-caption animated <?php echo $slide["caption_animation_in"].' '.$slide["caption_animation_out"];?>">
			                                		<div class="caption-case-inner" data-0="opacity: 1;" data-300="opacity: 0;">
					                                <?php if (!empty($slide['title'])) echo '<div class="kadcaptiontitle clearfix headerfont">'.$slide['title'].'</div><div class="clearfix"></div>'; ?>
					                                <?php if (!empty($slide['description'])) echo '<div><div class="kadcaptiontext headerfont"><p>'.$slide['description'].'</p></div></div>';?>
					                                <?php if(!empty($slide['button_link'])) echo '<a href="'.$slide['button_link'].'" class="kad-slider-btn headerfont kad-slider-btn-01">'.$slide['button_txt'].'</a>'; ?>
					                                <?php if(!empty($slide['button_link_2'])) echo '<a href="'.$slide['button_link_2'].'" class="kad-slider-btn headerfont kad-slider-btn-02">'.$slide['button_txt_2'].'</a>'; ?>
			                                		</div> 
			                                	</div> 
			                            	</div>
			                            </div>
			                      	</li> 
			                      	<?php $slidenumber ++; ?>
			            <?php endforeach; ?>
			        	</ul>
			        	<ul class="kad-slider-pagination kad-pag-<?php echo esc_attr($id);?>">
			        		<?php foreach ($slides as $slide) : ?>
						    <li class="kad-slider-dot"></li>
						    <?php endforeach; ?>
						</ul>
			        	<a class="kad-slider-next kad-slider-navigate kad-next-<?php echo esc_attr($id);?>"></a>
			        	<a class="kad-slider-prev kad-slider-navigate kad-prev-<?php echo esc_attr($id);?>"></a>
			        	<?php } ?>
			      </div> <!--kad-slides-->
			  </div> <!--kad-slider-->
            		
	<?php  $output = ob_get_contents();
		ob_end_clean();
	return $output;
	} else {

		$custom_slider_css .= '@media (max-width: 992px) {';
 		if(!empty($caption_text_font_tb)) { $custom_slider_css .= '.kad-slider-'.$id.' .kad-slider .kadcaptiontext p {font-size:'.$caption_text_font_tb.'px;}'; }
 		if(!empty($caption_head_font_tb)) { $custom_slider_css .= '.kad-slider-'.$id.' .kad-slider .kadcaptiontitle {font-size:'.$caption_head_font_tb.'px;}'; }
 		if(!empty($tabletmax_height)) { $custom_slider_css .= '.kad-slider-'.$id.' .kad-slider .kad-slider-canvas, .kad-slider-'.$id.' .kad-slider {height:'.$tabletmax_height.'px !important;}'; }
 		
 		$custom_slider_css .= '} @media (max-width: 767px) {';
 		if(!empty($caption_head_font_pn)) { $custom_slider_css .= '.kad-slider-'.$id.' .kad-slider .kadcaptiontitle {font-size:'.$caption_head_font_pn.'px;}'; }
 		if(!empty($caption_text_font_pn)) { $custom_slider_css .= '.kad-slider-'.$id.' .kad-slider .kadcaptiontext p {font-size:'.$caption_text_font_pn.'px;}'; }
 		if(!empty($mobilemax_height)) { $custom_slider_css .= '.kad-slider-'.$id.' .kad-slider .kad-slider-canvas, .kad-slider-'.$id.' .kad-slider {height:'.$mobilemax_height.'px !important;}'; }
 		$custom_slider_css .= '}';

		               	ob_start(); ?>
                	<style type="text/css">
                	<?php echo $custom_slider_css; ?>
                	</style>
			  	<div class="kad-slider-wrapper <?php echo esc_attr($slider_parallax_class);?> <?php if($slidecount == 1) {echo 'kt_slider_single_slide';}?> kad-slider-<?php echo esc_attr($id);?>" data-ktslider-id="<?php echo esc_attr($id);?>" data-ktslider-auto-play="<?php echo esc_attr($auto_play_data);?>" data-ktslider-pause-time="<?php echo esc_attr($pause_data);?>" data-ktslider-height="<?php echo esc_attr($data_height);?>" data-ktslider-height-type="<?php echo esc_attr($data_height_type);?>" style="max-width:<?php echo esc_attr($max_width);?>; margin-left: auto; margin-right:auto;">
			    	<div id="kad-slider-<?php echo esc_attr($id);?>" class="kad-slider kad-loading" style="margin-left: auto; margin-right:auto; height:<?php echo esc_attr($max_height);?>; ">
			        	<ul class="kad-slider-canvas seq-canvas" style="height:<?php echo esc_attr($max_height);?>;" >
			        	<?php $slidenumber = 1;
			        	if(!empty($slides)) {
			            	foreach ($slides as $slide) : ?>
			                      <li> 
			                              <div class="kad-slide kad-slide-<?php echo esc_attr($slidenumber);?> <?php echo esc_attr($textshaddowclass).' '.esc_attr($headerstyleclass);?>" style="background-image: url('<?php echo $slide['image'];?>'); background-size:cover; background-repeat: no-repeat;">
			                              	<div class="caption-case kad-placement-<?php echo $slide["caption_placement"];?>">
			                                	<div class="kad-slide-caption animated <?php echo $slide["caption_animation_in"].' '.$slide["caption_animation_out"];?>">
			                                		<div class="caption-case-inner" data-0="opacity: 1;" data-300="opacity: 0;">
					                                <?php if (!empty($slide['title'])) echo '<div class="kadcaptiontitle clearfix headerfont">'.$slide['title'].'</div><div class="clearfix"></div>'; ?>
					                                <?php if (!empty($slide['description'])) echo '<div><div class="kadcaptiontext headerfont"><p>'.$slide['description'].'</p></div></div>';?>
					                                <?php if(!empty($slide['button_link'])) echo '<a href="'.$slide['button_link'].'" class="kad-slider-btn headerfont kad-slider-btn-01">'.$slide['button_txt'].'</a>'; ?>
					                                <?php if(!empty($slide['button_link_2'])) echo '<a href="'.$slide['button_link_2'].'" class="kad-slider-btn headerfont kad-slider-btn-02">'.$slide['button_txt_2'].'</a>'; ?>
			                                		</div> 
			                                	</div> 
			                            	</div>
			                            </div>
			                      	</li> 
			                      	<?php $slidenumber ++; ?>
			            <?php endforeach; ?>
			        	</ul>
			        	<ul class="kad-slider-pagination kad-pag-<?php echo esc_attr($id);?>">
			        		<?php foreach ($slides as $slide) : ?>
						    <li class="kad-slider-dot"></li>
						    <?php endforeach; ?>
						</ul>
			        	<a class="kad-slider-next kad-slider-navigate kad-next-<?php echo esc_attr($id);?>"></a>
			        	<a class="kad-slider-prev kad-slider-navigate kad-prev-<?php echo esc_attr($id);?>"></a>
			        	<?php } ?>
			      </div> <!--kad-slides-->
			  </div> <!--kad-slider-->
            		
	<?php  $output = ob_get_contents();
		ob_end_clean();
	return $output;
	}
}

function kadence_slider_shortcode(){
   add_shortcode('kadence_slider', 'kad_slider_shortcode_function');
}
add_action( 'init', 'kadence_slider_shortcode');


