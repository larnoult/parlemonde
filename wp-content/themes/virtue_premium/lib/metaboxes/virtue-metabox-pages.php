<?php
/**
 * Virtue Page Meta Boxes
 *
 * @package Virtue Page
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Virtue Page boxes
 */
function virtue_metabox_pages() {
	$prefix = '_kad_';

	$kt_pageheader = new_cmb2_box( array(
		'id'           => 'subtitle_metabox',
		'title'        => __( 'Page Title Settings', 'virtue' ),
		'object_types' => array( 'page' ),
		'priority'     => 'high',
	) );
	$kt_pageheader->add_field( array(
		'name' => __( 'Subtitle', 'virtue' ),
		'desc' => __( 'Subtitle will go below post title', 'virtue' ),
		'id'   => $prefix . 'subtitle',
		'type' => 'textarea_code',
	) );
	$kt_pageheader->add_field( array(
		'name'    => __( 'Show Page Title', 'virtue' ),
		'id'      => $prefix . 'show_page_title',
		'type'    => 'select',
		'options' => array(
			'default' => __( 'Site Default', 'virtue' ),
			'show'    => __( 'Show', 'virtue' ),
			'hide'    => __( 'Hide', 'virtue' ),
		),
	) );
	// Default Page.
	$virtue_default_page = new_cmb2_box( array(
		'id'           => 'default_page_sidebar',
		'title'        => __( 'Page Options', 'virtue' ),
		'object_types' => array( 'page' ),
		'show_on'      => array(
			'key'   => 'default-template',
			'value' => array( 'page.php' ),
		),
		'priority'     => 'low',
		'context'      => 'side',
	) );
	$virtue_default_page->add_field( array(
		'name'    => __( 'Display Sidebar?', 'virtue' ),
		'desc'    => '',
		'id'      => $prefix . 'page_sidebar',
		'type'    => 'select',
		'options' => array(
			'default' => __( 'Default', 'virtue' ),
			'yes'     => __( 'Yes', 'virtue' ),
			'no'      => __( 'No', 'virtue' ),
		),
	) );
	$virtue_default_page->add_field( array(
		'name'    => __( 'Choose Sidebar', 'virtue' ),
		'desc'    => '',
		'id'      => $prefix . 'sidebar_choice',
		'type'    => 'select',
		'options' => virtue_cmb_sidebar_options(),
	) );
	$virtue_default_page->add_field( array(
		'name'    => __( 'Page Content Width', 'virtue' ),
		'desc'    => '',
		'id'      => $prefix . 'page_content_width',
		'type'    => 'select',
		'options' => array(
			'default'   => __( 'Default', 'virtue' ),
			'contained' => __( 'Contained', 'virtue' ),
			'full'      => __( 'Fullwidth', 'virtue' ),
		),
	) );
	// Page templates.
	$virtue_template_pages = new_cmb2_box( array(
		'id'           => 'template_page_settings',
		'title'        => __( 'Page Options', 'virtue' ),
		'object_types' => array( 'page' ),
		'show_on'      => array(
			'key'   => 'page-template',
			'value' => array( 'page-sidebar.php', 'page-feature-sidebar.php', 'page-blog.php', 'page-blog-grid.php', 'page-contact.php', 'page-fullwidth.php', 'page-feature.php', 'page-portfolio.php' ),
		),
		'priority'     => 'low',
		'context'      => 'side',
	) );
	$virtue_template_pages->add_field( array(
		'name'    => __( 'Page Content Width', 'virtue' ),
		'desc'    => '',
		'id'      => $prefix . 'page_content_width',
		'type'    => 'select',
		'options' => array(
			'default'   => __( 'Default', 'virtue' ),
			'contained' => __( 'Contained', 'virtue' ),
			'full'      => __( 'Fullwidth', 'virtue' ),
		),
	) );
}
add_filter( 'cmb2_admin_init', 'virtue_metabox_pages' );

/**
 * Virtue feature page metaboxes
 */
function virtue_feature_page_metaboxes() {
	$prefix = '_kad_';

	$kt_feature_page = new_cmb2_box( array(
		'id'           => 'pagefeature_metabox',
		'title'        => __( 'Feature Page Options', 'virtue' ),
		'object_types' => array( 'page' ),
		'show_on'      => array(
			'key'   => 'page-template',
			'value' => array(
				'page-feature.php',
				'page-feature-sidebar.php',
			),
		),
		'priority'     => 'high',
	) );
	$kt_feature_page->add_field( array(
		'name'    => __( 'Feature Options', 'virtue' ),
		'desc'    => __( 'If image slider make sure images uploaded are at-least 1170px wide.', 'virtue' ),
		'id'      => $prefix . 'page_head',
		'type'    => 'select',
		'options' => array(
			'flex'        => __( 'Image Slider - (Cropped Image Ratio)', 'virtue' ),
			'carousel'    => __( 'Image Slider - (Different Image Ratio)', 'virtue' ),
			'imgcarousel' => __( 'Image Carousel - (Multiple Images Showing At Once)', 'virtue' ),
			'rev'         => __( 'Revolution Slider', 'virtue' ),
			'ktslider'    => __( 'Kadence Slider', 'virtue' ),
			'cyclone'     => __( 'Shortcode Slider', 'virtue' ),
			'video'       => __( 'Video', 'virtue' ),
			'image'       => __( 'Image', 'virtue' ),
		),
	) );
	$kt_feature_page->add_field( array(
		'name' => __('Slider Images', 'virtue' ),
		'desc' => __("Add for flex, carousel, and image carousel.", 'virtue' ),
		'id'   => $prefix . 'image_gallery',
		'type' => 'kad_gallery',
	) );
	$kt_feature_page->add_field( array(
		'name' => __('If Revolution Slider', 'virtue'),
		'desc' => __('Paste Revolution slider alias here (example: slider1)', 'virtue'),
		'id'   => $prefix . 'post_rev',
		'type' => 'textarea_code',
	) );
	$kt_feature_page->add_field( array(
		'name' => __('If Kadence or Shortcode Slider', 'virtue'),
		'desc' => __('Paste slider shortcode here (example: [kadence_slider_pro id="4"])', 'virtue'),
		'id'   => $prefix . 'post_cyclone',
		'type' => 'textarea_code',
	) );
	$kt_feature_page->add_field( array(
		'name' => __('Display Shortcode Slider above Header', 'virtue'),
		'desc' => '',
		'id'   => $prefix . 'shortcode_above_header',
		'type' => 'checkbox',
	) );
	$kt_feature_page->add_field( array(
		'name' => __('If above Header use arrow', 'virtue'),
		'desc' => '',
		'id'   => $prefix . 'shortcode_above_header_arrow',
		'type' => 'checkbox',
	) );
	$kt_feature_page->add_field( array(
		'name' => __('Max Image/Slider Height', 'virtue'),
		'desc' => __('Default is: 400 (Note: just input number, example: 350)', 'virtue'),
		'id'   => $prefix . 'posthead_height',
		'type' => 'text_small',
	) );
	$kt_feature_page->add_field( array(
		'name' => __("Max Image/Slider Width", 'virtue' ),
		'desc' => __("Default is: 1140 on full-width posts (Note: just input number, example: 650, does not apply to Carousel slider)", 'virtue' ),
		'id'   => $prefix . 'posthead_width',
		'type' => 'text_small',
	) );
	$kt_feature_page->add_field( array(
		'name'    => __('Use Lightbox for Feature Image', 'virtue'),
		'desc'    => __('If feature option is set to image, choose to use lightbox link with image.', 'virtue'),
		'id'      => $prefix . 'feature_img_lightbox',
		'type'    => 'select',
		'options' => array(
			'yes' 	=> __('Yes', 'virtue'),
			'no'	=> __('No', 'virtue'),
		),
	) );
	$kt_feature_page->add_field( array(
		'name' => __('If Video', 'virtue'),
		'desc' => __('Place Embed Code Here, works with youtube, vimeo...', 'virtue'),
		'id'   => $prefix . 'post_video',
		'type' => 'textarea_code',
	) );
}
add_filter( 'cmb2_admin_init', 'virtue_feature_page_metaboxes' );

add_filter( 'cmb2_admin_init', 'virtue_page_sidebar_metaboxes');
function virtue_page_sidebar_metaboxes(){
	$prefix = '_kad_';
	$kt_page_sidebar = new_cmb2_box( array(
		'id'         => 'page_sidebar',
		'title'      => __('Sidebar Options', 'virtue'),
		'object_types'      => array( 'page' ), // Post type
		'show_on' => array( 'key' => 'page-template', 'value' => array('page-sidebar.php','page-feature-sidebar.php')),
		'priority'   => 'high',
	) );
	$kt_page_sidebar->add_field( array(
		'name'    => __('Choose Sidebar', 'virtue'),
		'desc'    => '',
		'id'      => $prefix . 'sidebar_choice',
		'type'    => 'select',
		'options' => virtue_cmb_sidebar_options(),				
	) );
}

add_filter( 'cmb2_admin_init', 'virtue_contact_page_metaboxes');
function virtue_contact_page_metaboxes(){
	$prefix = '_kad_';
	$kt_contact_page = new_cmb2_box( array(
		'id'         => 'contact_metabox',
		'title'      => __('Contact Page Options', 'virtue'),
		'object_types'      => array( 'page' ), // Post type
		'show_on' => array('key' => 'page-template', 'value' => array( 'page-contact.php')),
		'priority'   => 'high',
	) );
	$kt_contact_page->add_field( array(
        'name' => __('Use Contact Form', 'virtue'),
        'desc' => '',
        'id' => $prefix .'contact_form',
        'type'    => 'select',
		'options' => array(
			'yes' => __('Yes', 'virtue'),
			'noe' => __('No', 'virtue'),
		),
	) );
	$kt_contact_page->add_field( array(
		'name' => __('Contact Form Title', 'virtue'),
		'desc' => __('ex. Send us an Email', 'virtue'),
		'id'   => $prefix . 'contact_form_title',
		'type' => 'text',
	) );
	$kt_contact_page->add_field( array(
        'name' => __('Name Field Required?', 'virtue'),
        'desc' => __('You can make the name field optional', 'virtue'),
        'id' => $prefix .'contact_name_required',
        'type'    => 'select',
		'options' => array(
			'true' => __('Yes', 'virtue'),
			'false' => __('No', 'virtue'), 
		),
	) );
	$kt_contact_page->add_field( array(
        'name' => __('Use Simple Math Question', 'virtue'),
        'desc' => __('Adds a simple math question to form.', 'virtue'),
        'id' => $prefix .'contact_form_math',
        'type'    => 'select',
		'options' => array(
			'yes' => __('Yes', 'virtue'),
			'no' => __('No', 'virtue'), 
		),
	) );
	$kt_contact_page->add_field( array(
        'name' => __('Use Consent to Privacy Policy', 'virtue'),
        'desc' => __('Adds a consent box to form.', 'virtue'),
        'id' => $prefix .'contact_consent',
        'type'    => 'select',
		'options' => array(
			'false' => __('No', 'virtue'), 
			'true' => __('Yes', 'virtue'),
		),
	) );
	$kt_contact_page->add_field( array(
        'name' => __('Use Map', 'virtue'),
        'desc' => __('NOTE: you must add a google maps API key into the theme options > misc settings.', 'virtue'),
        'id' => $prefix .'contact_map',
        'type'    => 'select',
		'options' => array(
			'no' => __('No', 'virtue'),
			'yes' => __('Yes', 'virtue'),
		),
	) );
	$kt_contact_page->add_field( array(
		'name' => __('Address', 'virtue'),
		'desc' => __('Enter your Location', 'virtue'),
		'id'   => $prefix . 'contact_address',
		'type' => 'text',
	) );
	$kt_contact_page->add_field( array(
		'name'    => __('Map Type', 'virtue'),
		'desc'    => '',
		'id'      => $prefix . 'contact_maptype',
		'type'    => 'select',
		'options' => array(
			'ROADMAP' => __('ROADMAP', 'virtue'),
			'HYBRID' => __('HYBRID', 'virtue'),
			'TERRAIN' => __('TERRAIN', 'virtue'),
			'SATELLITE' => __('SATELLITE', 'virtue'),
		),
	) );
	$kt_contact_page->add_field( array(
		'name' 		=> __('Map Zoom Level', 'virtue'),
		'desc' 		=> __('A good place to start is 15', 'virtue'),
		'id'   		=> $prefix . 'contact_zoom',
		'default'  	=> '15',
		'type'    	=> 'select',
		'options'	=> array(
			'1' => __('1 (World View)', 'virtue'),
			'2' => '2', 
			'3' => '3', 
			'4' => '4', 
			'5' => '5', 
			'6' => '6', 
			'7' => '7', 
			'8' => '8', 
			'9' => '9', 
			'10' => '10', 
			'11' => '11', 
			'12' => '12', 
			'13' => '13', 
			'14' => '14', 
			'15' => '15', 
			'16' => '16', 
			'17' => '17', 
			'18' => '18', 
			'19' => '19', 
			'20' => '20', 
			'21' => __('21 (Street View)', 'virtue'),
			),
	) );
	$kt_contact_page->add_field( array(
		'name' => __('Map Height', 'virtue'),
		'desc' => __('Default is 300', 'virtue'),
		'id'   => $prefix . 'contact_mapheight',
		'type' => 'text_small',
	) );
	$kt_contact_page->add_field( array(
		'name' => __('Address Two', 'virtue'),
		'desc' => __('Enter your Location', 'virtue'),
		'id'   => $prefix . 'contact_address2',
		'type' => 'text',
	) );
	$kt_contact_page->add_field( array(
		'name' => __('Address Three', 'virtue'),
		'desc' => __('Enter a Location', 'virtue'),
		'id'   => $prefix . 'contact_address3',
		'type' => 'text',
	) );
	$kt_contact_page->add_field( array(
		'name' => __('Address Four', 'virtue'),
		'desc' => __('Enter a Location', 'virtue'),
		'id'   => $prefix . 'contact_address4',
		'type' => 'text',
	) );
	$kt_contact_page->add_field( array(
		'name' => __('Map Center', 'virtue'),
		'desc' => __('Enter a Location', 'virtue'),
		'id'   => $prefix . 'contact_map_center',
		'type' => 'text',	
	));
}


add_filter( 'cmb2_admin_init', 'virtue_staff_template_metaboxes');
function virtue_staff_template_metaboxes(){
	$prefix = '_kad_';
	$kt_staff_page = new_cmb2_box( array(
		'id'         => 'staff_page_metabox',
		'title'      => __('Staff Page Options', 'virtue'),
		'object_types'      => array( 'page' ), // Post type
		'show_on' => array('key' => 'page-template', 'value' => array( 'page-staff-grid.php' )),
		'priority'   => 'high',
	) );
	$kt_staff_page->add_field( array(
		'name'    => __('Columns', 'virtue'),
		'desc'    => '',
		'id'      => $prefix . 'staff_columns',
		'type'    => 'select',
		'options' => array(
			'4' => __('Four Column', 'virtue'),
			'3' => __('Three Column', 'virtue'),
			'2' => __('Two Column', 'virtue'),
		),
	) );
	$kt_staff_page->add_field( array(
		'name'    => __('Order Items By', 'virtue'),
		'desc'    => '',
		'id'      => $prefix . 'staff_orderby',
		'type'    => 'select',
		'options' => array(
			'menu_order' 	=> __("Menu Order", 'virtue' ),
			'date' 			=> __("Date", 'virtue' ),
			'title' 		=> __("Title", 'virtue' ),
			'rand' 			=> __("Random", 'virtue' ),
		),
	) );
	$kt_staff_page->add_field( array(
		'name'    => __('Order', 'virtue'),
		'desc'    => '',
		'id'      => $prefix . 'staff_order',
		'type'    => 'select',
		'options' => array(
			'ASC' 	=> __("ASC", 'virtue' ),
			'DESC' 	=> __("DESC", 'virtue' ),
		),
	) );
	$kt_staff_page->add_field( array(
        'name' => __('Limit to Group', 'virtue'),
        'desc' => '',
        'id' => $prefix .'staff_type',
        'type' 		=> 'kt_select_group',
		'taxonomy' 	=> 'staff-group',
   	) );
	$kt_staff_page->add_field( array(
		'name'    => __('Filter?', 'virtue'),
		'desc'    => '',
		'id'      => $prefix . 'staff_filter',
		'type'    => 'select',
		'options' => array(
			'no' => __('No', 'virtue'),
			'yes' => __('Yes', 'virtue'),
		),
	) );
	$kt_staff_page->add_field( array(
		'name'    => __('Items per Page', 'virtue'),
		'desc'    => __('How many staff items per page', 'virtue'),
		'id'      => $prefix . 'staff_items',
		'type'    => 'select',
		'options' => array(
			'all' 	=> __("All", 'virtue' ),
			'2' 	=> __("2", 'virtue' ),
			'3' 	=> __("3", 'virtue' ),
			'4' 	=> __("4", 'virtue' ),
			'5' 	=> __("5", 'virtue' ),
			'6' 	=> __("6", 'virtue' ),
			'7' 	=> __("7", 'virtue' ),
			'8' 	=> __("8", 'virtue' ),
			'9' 	=> __("9", 'virtue' ),
			'10' 	=> __("10", 'virtue' ),
			'11' 	=> __("11", 'virtue' ),
			'12' 	=> __("12", 'virtue' ),
			'13' 	=> __("13", 'virtue' ),
			'14' 	=> __("14", 'virtue' ),
			'15' 	=> __("15", 'virtue' ),
			'16' 	=> __("16", 'virtue' ),
			'17' 	=> __("17", 'virtue' ),
			'18' 	=> __("18", 'virtue' ),
		),
	) );
	$kt_staff_page->add_field( array(
		'name'    => __('Crop images to equal height', 'virtue'),
		'desc'    => __('If cropped rows will be equal', 'virtue'),
		'id'      => $prefix . 'staff_crop',
		'type'    => 'select',
		'options' => array(
			'yes' => __('Yes', 'virtue'), 
			'no' => __('No', 'virtue'), 
		),
	) );
	$kt_staff_page->add_field( array(
		'name'    => __('Set image height if cropping', 'virtue'),
		'desc'    => __('Default is 16:9 ratio (Note: just input number, example: 350)', 'virtue'),
		'id'      => $prefix . 'staff_img_crop',
		'type'    => 'text_small',
	) );
	$kt_staff_page->add_field( array(
		'name' => __('Use Staff Excerpt or Content?', 'virtue'),
		'id'   => $prefix . 'staff_wordlimit',
		'type'    => 'select',
		'options' => array(
			'false' => __('Excerpt', 'virtue'),
			'true' => __('Content', 'virtue'),
		),
	) );
	$kt_staff_page->add_field( array(
		'name' => __('Make images and title link to single post?', 'virtue'),
		'id'   => $prefix . 'staff_single_link',
		'type'    => 'select',
		'options' => array(
			'false' => __('No, no link', 'virtue'),
			'true' => __('Yes, link to single post', 'virtue'),
			'lightbox' => __('Image link to lightbox', 'virtue'),
		),		
	) );
}


add_filter( 'cmb2_admin_init', 'virtue_testimonial_template_metaboxes');
function virtue_testimonial_template_metaboxes(){
	$prefix = '_kad_';
	$kt_testimonial_page = new_cmb2_box( array(
		'id'         => 'testimonial_page_metabox',
		'title'      => __('Testimonial Page Options', 'virtue'),
		'object_types'      => array( 'page' ), // Post type
		'show_on' => array('key' => 'page-template', 'value' => array( 'page-testimonial-grid.php' )),
		'priority'   => 'high',
	) );
	$kt_testimonial_page->add_field( array(
		'name'    => __('Columns', 'virtue'),
		'desc'    => '',
		'id'      => $prefix . 'testimonial_columns',
		'type'    => 'select',
		'options' => array(
			'4' => __('Four Column', 'virtue'), 
			'3' => __('Three Column', 'virtue'), 
			'2' => __('Two Column', 'virtue'), 
			'1' => __('One Column', 'virtue'), 
		),
	) );
	$kt_testimonial_page->add_field( array(
		'name'    => __('Orderby', 'virtue'),
		'desc'    => '',
		'id'      => $prefix . 'testimonial_orderby',
		'type'    => 'select',
		'options' => array(
			'menu_order' 	=> __("Menu Order", 'virtue' ),
			'date' 			=> __("Date", 'virtue' ),
			'title' 		=> __("Title", 'virtue' ),
			'rand' 			=> __("Random", 'virtue' ),
		),
	) );
	$kt_testimonial_page->add_field( array(
        'name' => __('Testimonial Group', 'virtue'),
        'desc' => '',
        'id' => $prefix .'testimonial_type',
        'type' 		=> 'kt_select_group',
		'taxonomy' 	=> 'testimonial-group',
    ) );
	$kt_testimonial_page->add_field( array(
		'name'    => __('Items per Page', 'virtue'),
		'desc'    => __('How many testimonial items per page', 'virtue'),
		'id'      => $prefix . 'testimonial_items',
		'type'    => 'select',
		'options' => array(
			'all' 	=> __("All", 'virtue' ),
			'3' 	=> __("3", 'virtue' ),
			'4' 	=> __("4", 'virtue' ),
			'5' 	=> __("5", 'virtue' ),
			'6' 	=> __("6", 'virtue' ),
			'7' 	=> __("7", 'virtue' ),
			'8' 	=> __("8", 'virtue' ),
			'9' 	=> __("9", 'virtue' ),
			'10' 	=> __("10", 'virtue' ),
			'11' 	=> __("11", 'virtue' ),
			'12' 	=> __("12", 'virtue' ),
			'13' 	=> __("13", 'virtue' ),
			'14' 	=> __("14", 'virtue' ),
			'15' 	=> __("15", 'virtue' ),
			'16' 	=> __("16", 'virtue' ),
			'17' 	=> __("17", 'virtue' ),
			'18' 	=> __("18", 'virtue' ),
		),
	) );
	$kt_testimonial_page->add_field( array(
		'name' => __('Limit Testimonial Text', 'virtue'),
		'desc' => '',
		'id'   => $prefix . 'limit_testimonial',
		'type' => 'checkbox',
		'default'  => '0'
	) );
	$kt_testimonial_page->add_field( array(
		'name' => __('Word Count Text', 'virtue'),
		'desc' => __('eg: 25', 'virtue'),
		'id'   => $prefix . 'testimonial_word_count',
		'type' => 'text_small',
	) );
	$kt_testimonial_page->add_field( array(
		'name' => __('Add link to single post', 'virtue'),
		'desc' => '',
		'id'   => $prefix . 'single_testimonial_link',
		'type' => 'checkbox',
		'default'  => '0'
	) );
	$kt_testimonial_page->add_field( array(
		'name' => __('Link Text', 'virtue'),
		'desc' => __('eg: Read More', 'virtue'),
		'id'   => $prefix . 'testimonial_link_text',
		'type' => 'text_small',							
	) );
}

add_filter( 'cmb2_admin_init', 'virtue_blog_page_metaboxes');
function virtue_blog_page_metaboxes(){
	$prefix = '_kad_';
	$kt_blog_page = new_cmb2_box( array(
		'id'         => 'bloglist_metabox',
		'title'      => __('Blog List Options', 'virtue'),
		'object_types'      => array( 'page' ), // Post type
		'show_on' => array('key' => 'page-template', 'value' => array( 'page-blog.php')),
		'priority'   => 'high',
	) );
	$kt_blog_page->add_field( array(
        'name' => __('Blog Category', 'virtue'),
        'desc' => __('Select all blog posts or a specific category to show', 'virtue'),
        'id' => $prefix .'blog_cat',
        'type' => 'kt_select_category',
   	) );
   	$kt_blog_page->add_field( array(
		'name'    => __('Order Items By', 'virtue'),
		'desc'    => '',
		'id'      => $prefix . 'blog_order',
		'type'    => 'select',
		'options' => array(
			'date' 			=> __("Date", 'virtue' ),
			'menu_order' 	=> __("Menu Order", 'virtue' ),
			'title' 		=> __("Title", 'virtue' ),
			'rand' 			=> __("Random", 'virtue' ),
		),
	) );
	$kt_blog_page->add_field( array(
		'name'    => __('How Many Posts Per Page', 'virtue'),
		'desc'    => '',
		'id'      => $prefix . 'blog_items',
		'type'    => 'select',
		'options' => array(
			'all' 	=> __("All", 'virtue' ),
			'3' 	=> __("3", 'virtue' ),
			'4' 	=> __("4", 'virtue' ),
			'5' 	=> __("5", 'virtue' ),
			'6' 	=> __("6", 'virtue' ),
			'7' 	=> __("7", 'virtue' ),
			'8' 	=> __("8", 'virtue' ),
			'9' 	=> __("9", 'virtue' ),
			'10' 	=> __("10", 'virtue' ),
			'11' 	=> __("11", 'virtue' ),
			'12' 	=> __("12", 'virtue' ),
			'13' 	=> __("13", 'virtue' ),
			'14' 	=> __("14", 'virtue' ),
			'15' 	=> __("15", 'virtue' ),
			'16' 	=> __("16", 'virtue' ),
			'17' 	=> __("17", 'virtue' ),
			'18' 	=> __("18", 'virtue' ),
		),
	) );
	$kt_blog_page->add_field( array(
		'name'    => __('Display Post Content as:', 'virtue'),
		'desc'    => '',
		'id'      => $prefix . 'blog_summery',
		'type'    => 'select',
		'options' => array(
			'summery' => __('Summary', 'virtue'),
			'full' => __('Full', 'virtue'),
		),		
	) );
	$kt_blog_page->add_field( array(
		'name' => __('Display Sidebar?', 'virtue'),
		'desc' => __('Choose if layout is fullwidth or sidebar', 'virtue'),
		'id'   => $prefix . 'page_sidebar',
		'type'    => 'select',
		'options' => array(
			'no' => __('No', 'virtue'),
			'yes' => __('Yes', 'virtue'),
		),
	) );
	$kt_blog_page->add_field( array(
		'name'    => __('Choose Sidebar', 'virtue'),
		'desc'    => '',
		'id'      => $prefix . 'sidebar_choice',
		'type'    => 'select',
		'options' => virtue_cmb_sidebar_options(),				
	) );
}

add_filter( 'cmb2_admin_init', 'virtue_bloggrid_page_metaboxes');
function virtue_bloggrid_page_metaboxes(){
	$prefix = '_kad_';
	$kt_bloggrid_page = new_cmb2_box( array(
		'id'         => 'bloggrid_metabox',
		'title'      => __('Blog Grid Options', 'virtue'),
		'object_types'      => array( 'page' ), // Post type
		'show_on' => array('key' => 'page-template', 'value' => array( 'page-blog-grid.php')),
		'priority'   => 'high',
	) );
	$kt_bloggrid_page->add_field( array(
        'name' => __('Blog Category', 'virtue'),
        'desc' => __('Select all blog posts or a specific category to show', 'virtue'),
        'id' => $prefix .'blog_cat',
        'type' => 'kt_select_category',
    ) );
   	$kt_bloggrid_page->add_field( array(
		'name'    => __('Order Items By', 'virtue'),
		'desc'    => '',
		'id'      => $prefix . 'blog_order',
		'type'    => 'select',
		'options' => array(
			'date' 			=> __("Date", 'virtue' ),
			'menu_order' 	=> __("Menu Order", 'virtue' ),
			'title' 		=> __("Title", 'virtue' ),
			'rand' 			=> __("Random", 'virtue' ),
		),
	) );
	$kt_bloggrid_page->add_field( array(
		'name'    => __('How Many Posts Per Page', 'virtue'),
		'desc'    => '',
		'id'      => $prefix . 'blog_items',
		'type'    => 'select',
		'options' => array(
			'all' 	=> __("All", 'virtue' ),
			'3' 	=> __("3", 'virtue' ),
			'4' 	=> __("4", 'virtue' ),
			'5' 	=> __("5", 'virtue' ),
			'6' 	=> __("6", 'virtue' ),
			'7' 	=> __("7", 'virtue' ),
			'8' 	=> __("8", 'virtue' ),
			'9' 	=> __("9", 'virtue' ),
			'10' 	=> __("10", 'virtue' ),
			'11' 	=> __("11", 'virtue' ),
			'12' 	=> __("12", 'virtue' ),
			'13' 	=> __("13", 'virtue' ),
			'14' 	=> __("14", 'virtue' ),
			'15' 	=> __("15", 'virtue' ),
			'16' 	=> __("16", 'virtue' ),
			'17' 	=> __("17", 'virtue' ),
			'18' 	=> __("18", 'virtue' ),
		),
	) );
	$kt_bloggrid_page->add_field( array(
		'name'    => __('Choose Column Layout:', 'virtue'),
		'desc'    => '',
		'id'      => $prefix . 'blog_columns',
		'type'    => 'select',
		'options' => array(
			'fourcolumn' => __('Four Column', 'virtue'),
			'threecolumn' => __('Three Column', 'virtue'),
			'twocolumn' => __('Two Column', 'virtue'),
		),		
	) );
	$kt_bloggrid_page->add_field( array(
		'name' => __('Display Sidebar?', 'virtue'),
		'desc' => __('Choose if layout is fullwidth or sidebar', 'virtue'),
		'id'   => $prefix . 'page_sidebar',
		'type'    => 'select',
		'options' => array(
			'no' => __('No', 'virtue'),
			'yes' => __('Yes', 'virtue'),
		),
	) );
	$kt_bloggrid_page->add_field( array(
		'name'    => __('Choose Sidebar', 'virtue'),
		'desc'    => '',
		'id'      => $prefix . 'sidebar_choice',
		'type'    => 'select',
		'options' => virtue_cmb_sidebar_options(),				
	) );
}


add_filter( 'cmb2_admin_init', 'virtue_seo_metaboxes');
function virtue_seo_metaboxes(){
	$prefix = '_kad_';
	global $virtue_premium;	
	if(isset($virtue_premium['seo_switch']) && $virtue_premium['seo_switch'] == '1') {
		$kt_seo = new_cmb2_box( array(
			'id'         	=> 'seo_metabox',
			'title'      	=> __("SEO Options", 'virtue'),
			'object_types'  => array('page', 'post' ),
			'priority'   	=> 'core',
		) );
		$kt_seo->add_field( array(
			'name' => __( "Page Title", 'virtue' ),
			'desc' => __( "Optimal Format: Brand Name | Primary Keyword and Secondary Keyword", 'virtue' ),
			'id'   => $prefix . 'seo_title',
			'type' => 'text',
		) );
		$kt_seo->add_field( array(
			'name' => __( "Page Description", 'virtue' ),
			'desc' => __( "Optimal Length: Roughly 155 Characters", 'virtue' ),
			'id'   => $prefix . 'seo_description',
			'type' => 'textarea_small',
		) );
	}
}
