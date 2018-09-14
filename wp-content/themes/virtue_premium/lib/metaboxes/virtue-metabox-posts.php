<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
add_filter( 'cmb2_admin_init', 'virtue_metabox_posts');
function virtue_metabox_posts(){
	$prefix = '_kad_';

	$kt_post = new_cmb2_box( array(
		'id'         => 'standard_post_metabox',
		'title'      => __( "Standard Post Options", 'virtue' ),
		'object_types'      => array( 'post'), // Post type
		'priority'   => 'high',
	) );
	$kt_post->add_field( array(
		'name' => __( "Head Content", 'virtue' ),
		'desc' => '',
		'id'   => $prefix . 'blog_head',
		'type' => 'select',
		'options' => array(
			'default' => __("Site Default", 'virtue' ),
			'none' => __("None", 'virtue' ),
			'flex' => __("Image Slider - (Cropped Image Ratio)", 'virtue' ),
			'carouselslider' => __("Image Slider - (Different Image Ratio)", 'virtue' ),
			'carousel' => __("Image Carousel - (Muiltiple Images Showing At Once)", 'virtue' ),
			'video' => __("Video", 'virtue' ),
			'image' => __("Image", 'virtue' ),
			'shortcode' => __("Shortcode", 'virtue' ),
		),
	) );
	$kt_post->add_field( array(
		'name' => __("Post Slider/Gallery Images", 'virtue' ),
		'desc' => __("Add images for slider/gallery here", 'virtue' ),
		'id'   => $prefix . 'image_gallery',
		'type' => 'kad_gallery',
	) );
	$kt_post->add_field( array(
		'name' => __("Max Image/Slider Height", 'virtue' ),
		'desc' => __("Default is: 400 (Note: just input number, example: 350)", 'virtue' ),
		'id'   => $prefix . 'posthead_height',
		'type' => 'text_small',
	) );
	$kt_post->add_field( array(
		'name' => __("Max Image/Slider Width", 'virtue' ),
		'desc' => __("Default is: 848 or 1140 on fullwidth posts (Note: just input number, example: 650, only applys to Image Slider)", 'virtue' ),
		'id'   => $prefix . 'posthead_width',
		'type' => 'text_small',
	) );
	$kt_post->add_field( array(
		'name'    => __("Post Summary", 'virtue' ),
		'desc'    => '',
		'id'      => $prefix . 'post_summery',
		'type'    => 'select',
		'options' => array(
			'default' => __('Site Default', 'virtue' ),
			'text' => __('Text', 'virtue'), 
			'img_portrait' => __('Portrait Image (feature image)', 'virtue'),
			'img_landscape' => __('Landscape Image (feature image)', 'virtue'), 
			'slider_portrait' => __('Portrait Image Slider', 'virtue'), 
			'slider_landscape' => __('Landscape Image Slider', 'virtue'),
			'video' => __('Video', 'virtue'), 
		),
	) );
	$kt_post->add_field( array(
		'name' => __('If Video Post', 'virtue'),
		'desc' => __('Place Embed Code Here, works with youtube, vimeo...', 'virtue'),
		'id'   => $prefix . 'post_video',
		'type' => 'textarea_code',
	) );
	$kt_post->add_field( array(
		'name' => __('If Shortcode Head Content', 'virtue'),
		'desc' => __('Place Shortcode Here', 'virtue'),
		'id'   => $prefix . 'post_shortcode',
		'type' => 'textarea_code',
	) );
	
}
add_filter( 'cmb2_admin_init', 'virtue_metabox_posts_options');
function virtue_metabox_posts_options(){
	$prefix = '_kad_';

	$kt_post_options = new_cmb2_box( array(
		'id'         => 'post_metabox',
		'title'      => __( "Post Options", 'virtue' ),
		'object_types'      => array( 'post'), // Post type
		'priority'   => 'high',
	) );
	$kt_post_options->add_field( array(
		'name' => __('Display Sidebar?', 'virtue'),
		'desc' => __('Choose if layout is fullwidth or sidebar', 'virtue'),
		'id'   => $prefix . 'post_sidebar',
		'type'    => 'select',
		'options' => array(
			'default' => __('Default', 'virtue'),
			'yes' => __('Yes', 'virtue'),
			'no' => __('No', 'virtue'),
		),
	));
	$kt_post_options->add_field( array(
		'name'    => __('Choose Sidebar', 'virtue'),
		'desc'    => '',
		'id'      => $prefix . 'sidebar_choice',
		'type'    => 'select',
		'options' => virtue_cmb_post_sidebar_options(),	
	));
	$kt_post_options->add_field( array(
		'name' => __('Author Info', 'virtue'),
		'desc' => __('Display an author info box?', 'virtue'),
		'id'   => $prefix . 'blog_author',
		'type'    => 'select',
		'options' => array(
			'default' => __('Default', 'virtue'),
			'no' => __('No', 'virtue'),
			'yes' => __('Yes', 'virtue'),
		),
	));
	$kt_post_options->add_field( array(
		'name' => __('Posts Carousel', 'virtue'),
		'desc' => __('Display a carousel with similar or recent posts?', 'virtue'),
		'id'   => $prefix . 'blog_carousel_similar',
		'type'    => 'select',
		'options' => array(
			'default' => __('Default', 'virtue'),
			'no' => __('No', 'virtue'), 
			'recent' => __('Yes - Display Recent Posts', 'virtue'),
			'similar' => __('Yes - Display Similar Posts', 'virtue'),
		),
		
	));
	$kt_post_options->add_field( array(
		'name' => __('Carousel Title', 'virtue'),
		'desc' => __('ex. Similar Posts', 'virtue'),
		'id'   => $prefix . 'blog_carousel_title',
		'type' => 'text_medium',
	));
}
add_filter( 'cmb2_admin_init', 'virtue_metabox_custom_posttype_options');
function virtue_metabox_custom_posttype_options(){
	$prefix = '_kad_';

	$kt_post_options = new_cmb2_box( array(
		'id'         => 'post_sidebar_metabox',
		'title'      => __( "Sidebar Options", 'virtue' ),
		'object_types'      => virtue_all_custom_posts(), // Post type
		'priority'   => 'high',
	) );
	$kt_post_options->add_field( array(
		'name' => __('Display Sidebar?', 'virtue'),
		'desc' => __('Choose if layout is fullwidth or sidebar', 'virtue'),
		'id'   => $prefix . 'post_sidebar',
		'type'    => 'select',
		'options' => array(
			'default' => __('Default', 'virtue'),
			'yes' => __('Yes', 'virtue'),
			'no' => __('No', 'virtue'),
		),
	));
	$kt_post_options->add_field( array(
		'name'    => __('Choose Sidebar', 'virtue'),
		'desc'    => '',
		'id'      => $prefix . 'sidebar_choice',
		'type'    => 'select',
		'options' => virtue_cmb_post_sidebar_options(),	
	));
}
function virtue_all_custom_posts() {
    $args = array(
       'public'   => true,
    );

    $output = 'names'; // names or objects, note names is the default
    $operator = 'and'; // 'and' or 'or'
    $post_types = get_post_types( $args, $output, $operator ); 
    $all_post_types = array();
    foreach ( $post_types  as $post_type ) {
    	if('kt_gallery' == $post_type || 'kad_slider' == $post_type || 'portfolio' == $post_type || 'post' == $post_type || 'page' == $post_type || 'staff' == $post_type || 'testimonial' == $post_type) {
    	} else {
        	array_push($all_post_types, $post_type);
    	}
    }
    return $all_post_types;
}
