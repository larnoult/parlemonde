<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
add_filter( 'cmb2_admin_init', 'virtue_metabox_portfolio');
function virtue_metabox_portfolio(){
	$prefix = '_kad_';

	$kt_portfolio_post = new_cmb2_box( array(
		'id'         => 'portfolio_post_metabox',
		'title'      => __( "Portfolio Post Options", 'virtue' ),
		'object_types'      => array( 'portfolio'), // Post type
		'priority'   => 'high',
	) );
	$kt_portfolio_post->add_field( array(
		'name'    => __('Project Layout', 'virtue'),
		'desc'    => '<a href="http://docs.kadencethemes.com/virtue-premium/portfolio-posts/" target="_blank" >Whats the difference?</a>',
		'id'      => $prefix . 'ppost_layout',
		'type'    => 'radio_inline',
		'options' => array(
			'beside' => __('Beside', 'virtue'), 
			'above' => __('Above', 'virtue'), 
			'three' => __('Three Rows', 'virtue'),
		),
	) );
	$kt_portfolio_post->add_field( array(
		'name'    => __('Project Options', 'virtue'),
		'desc'    => '',
		'id'      => $prefix . 'ppost_type',
		'type'    => 'select',
		'options' => array(
			'image' => __('Image', 'virtue'), 
			'flex' => __('Image Slider - (Cropped Image Ratio)', 'virtue'),
			'carousel' => __('Image Slider - (Different Image Ratio)', 'virtue'),
			'imgcarousel' => __('Image Carousel  - (Muiltiple Images Showing At Once)', 'virtue'),
			'rev' => __('Rev Slider', 'virtue'), 
			'ktslider' => __('Kadence Slider', 'virtue'), 
			'cyclone' => __('Shortcode', 'virtue'), 
			'imagegrid' => __('Image Grid', 'virtue'),
			'imagelist' => __('Image List', 'virtue'),
			'imagelist2' => __('Image List Style 2', 'virtue'), 
			'video' => __('Video', 'virtue'), 
			'none' => __('None', 'virtue'), 
		),
	) );
	$kt_portfolio_post->add_field( array(
		'name'    => __('Columns (Only for Image Grid option)', 'virtue'),
		'desc'    => '',
		'id'      => $prefix . 'portfolio_img_grid_columns',
		'type'    => 'select',
		'options' => array(
			'4' => __('Four Column', 'virtue'),
			'3' => __('Three Column', 'virtue'), 
			'2' => __('Two Column', 'virtue'), 
			'5' => __('Five Column', 'virtue'), 
			'6' => __('Six Column', 'virtue'), 
		),
	) );
	$kt_portfolio_post->add_field( array(
		'name' => __("Portfolio Slider/Images", 'virtue' ),
		'desc' => __("Add images for post here", 'virtue' ),
		'id'   => $prefix . 'image_gallery',
		'type' => 'kad_gallery',
	) );
	$kt_portfolio_post->add_field( array(
		'name' => __( "If Kadence or Shortcode Slider", 'virtue' ),
		'desc' => __( "Paste Slider Shortcode here", 'virtue' ),
		'id'   => $prefix . 'shortcode_slider',
		'type' => 'textarea_code',
	) );
	$kt_portfolio_post->add_field( array(
		'name' => __("Max Image/Slider Height", 'virtue' ),
		'desc' => __("Default is: 450 (Note: just input number, example: 350)", 'virtue' ),
		'id'   => $prefix . 'posthead_height',
		'type' => 'text_small',
	) );
	$kt_portfolio_post->add_field( array(
		'name' => __("Max Image/Slider Width", 'virtue' ),
		'desc' => __("Default is: 670 or 1140 on above or three row layouts (Note: just input number, example: 650)", 'virtue' ),
		'id'   => $prefix . 'posthead_width',
		'type' => 'text_small',
	) );
	$kt_portfolio_post->add_field( array(
		'name'    => __('Auto Play Slider?', 'virtue'),
		'desc'    => '',
		'id'      => $prefix . 'portfolio_autoplay',
		'type'    => 'select',
		'options' => array(
			'Yes' => __('Yes', 'virtue'),
			'no' => __('No', 'virtue'), 
		),
	) );
	$kt_portfolio_post->add_field( array(
		'name'    => __('Project Summary', 'virtue'),
		'desc'    => __('This determines how its displayed in the portfolio grid page', 'virtue'),
		'id'      => $prefix . 'post_summery',
		'type'    => 'select',
		'options' => array(
			'image' => __('Image', 'virtue'),
			'slider' => __('Image Slider', 'virtue'),
			'videolight' => __('Image with video lightbox (must be url)', 'virtue'),
		),
	) );
	$kt_portfolio_post->add_field( array(
		'name' => __('Value 01 Title', 'virtue'),
		'desc' => __('ex. Project Type:', 'virtue'),
		'id'   => $prefix . 'project_val01_title',
		'type' => 'text_medium',
	) );
	$kt_portfolio_post->add_field( array(
		'name' => __('Value 01 Description', 'virtue'),
		'desc' => __('ex. Character Illustration', 'virtue'),
		'id'   => $prefix . 'project_val01_description',
		'type' => 'text_medium',
	) );
	$kt_portfolio_post->add_field( array(
		'name' => __('Value 02 Title', 'virtue'),
		'desc' => __('ex. Skills Needed:', 'virtue'),
		'id'   => $prefix . 'project_val02_title',
		'type' => 'text_medium',
	) );
	$kt_portfolio_post->add_field( array(
		'name' => __('Value 02 Description', 'virtue'),
		'desc' => __('ex. Photoshop, Illustrator', 'virtue'),
		'id'   => $prefix . 'project_val02_description',
		'type' => 'text_medium',
	) );
	$kt_portfolio_post->add_field( array(
		'name' => __('Value 03 Title', 'virtue'),
		'desc' => __('ex. Customer:', 'virtue'),
		'id'   => $prefix . 'project_val03_title',
		'type' => 'text_medium',
	) );
	$kt_portfolio_post->add_field( array(
		'name' => __('Value 03 Description', 'virtue'),
		'desc' => __('ex. Example Inc', 'virtue'),
		'id'   => $prefix . 'project_val03_description',
		'type' => 'text_medium',
	) );
	$kt_portfolio_post->add_field( array(
		'name' => __('Value 04 Title', 'virtue'),
		'desc' => __('ex. Project Year:', 'virtue'),
		'id'   => $prefix . 'project_val04_title',
		'type' => 'text_medium',
	) );
	$kt_portfolio_post->add_field( array(
		'name' => __('Value 04 Description', 'virtue'),
		'desc' => __('ex. 2013', 'virtue'),
		'id'   => $prefix . 'project_val04_description',
		'type' => 'text_medium',
	) );
	$kt_portfolio_post->add_field( array(
		'name' => __('External Website', 'virtue'),
		'desc' => __('ex. Website:', 'virtue'),
		'id'   => $prefix . 'project_val05_title',
		'type' => 'text_medium',
	) );
	$kt_portfolio_post->add_field( array(
		'name' => __('Website Address', 'virtue'),
		'desc' => __('ex. http://www.example.com', 'virtue'),
		'id'   => $prefix . 'project_val05_description',
		'type' => 'text_medium',
	) );
	$kt_portfolio_post->add_field( array(
		'name' => __('If Video Project - Video URL (recomended)', 'virtue'),
		'desc' => __('Place youtube, vimeo url', 'virtue'),
		'id'   => $prefix . 'post_video_url',
		'type' => 'textarea_code',
	) );
	$kt_portfolio_post->add_field( array(
		'name' => __('If Video Project - Video Embed Code', 'virtue'),
		'desc' => __('Place Embed Code Here, works with youtube, vimeo...', 'virtue'),
		'id'   => $prefix . 'post_video',
		'type' => 'textarea_code',
	) );
	$kt_portfolio_post->add_field( array(
		'name'    => __('Choose Portfolio Parent Page', 'virtue'),
		'desc'    => '',
		'id'      => $prefix . 'portfolio_parent',
		'type'    => 'select',
		'options' => virtue_cmb_page_options(),
	) );
}

add_filter( 'cmb2_admin_init', 'virtue_portfolio_carousel_metaboxes');
function virtue_portfolio_carousel_metaboxes(){
	$prefix = '_kad_';
	$kt_portfolio_carousel = new_cmb2_box( array(
		'id'         => 'portfolio_post_carousel_metabox',
		'title'      => __('Bottom Carousel Options', 'virtue'),
		'object_types'      => array( 'portfolio' ), // Post type
		'priority'   => 'high',
	) );
	$kt_portfolio_carousel->add_field( array(
		'name' => __('Carousel Title', 'virtue'),
		'desc' => __('ex. Similar Projects', 'virtue'),
		'id'   => $prefix . 'portfolio_carousel_title',
		'type' => 'text_medium',
	) );
	$kt_portfolio_carousel->add_field( array(
		'name' => __('Bottom Portfolio Carousel', 'virtue'),
		'desc' => __('Display a carousel with portfolio items below project?', 'virtue'),
		'id'   => $prefix . 'portfolio_carousel_recent',
		'type'    => 'select',
		'options' => array(
			'no' => __('No', 'virtue'),
			'yes' => __('Yes', 'virtue'),
		),
	) );
	$kt_portfolio_carousel->add_field( array(
		'name' => __('Carousel Items', 'virtue'),
		'desc' => '',
		'id'   => $prefix . 'portfolio_carousel_group',
		'type'    => 'select',
		'options' => array(
			'all' => __('All Portfolio Posts', 'virtue'),
			'cat' => __('Only of same Portfolio Type', 'virtue'),
		),
	) );
	$kt_portfolio_carousel->add_field( array(
		'name' => __('Carousel Order', 'virtue'),
		'desc' => '',
		'id'   => $prefix . 'portfolio_carousel_order',
		'type'    => 'select',
		'options' => array(
			'menu_order' => __('Menu Order', 'virtue'),
			'title' => __('Title', 'virtue'),
			'date' => __('Date', 'virtue'), 
			'rand' => __('Random', 'virtue'),
		),
	) );

}

add_filter( 'cmb2_admin_init', 'virtue_portfolio_template_metaboxes');
function virtue_portfolio_template_metaboxes(){
	$prefix = '_kad_';
	$kt_portfolio_page = new_cmb2_box( array(
		'id'         => 'portfolio_metabox',
		'title'      => __('Portfolio Page Options', 'virtue'),
		'object_types'      => array( 'page' ), // Post type
		'show_on' => array('key' => 'page-template', 'value' => array( 'page-portfolio.php')),
		'priority'   => 'high',
	) );
	$kt_portfolio_page->add_field( array(
		'name'    => __('Columns', 'virtue'),
		'desc'    => '',
		'id'      => $prefix . 'portfolio_columns',
		'type'    => 'select',
		'options' => array(
			'4' 	=> __("Four Columns", 'virtue' ),
			'3' 	=> __("Three Columns", 'virtue' ),
			'2' 	=> __("Two Columns", 'virtue' ),
			'5' 	=> __("Five Columns", 'virtue' ),
			'6' 	=> __("Six Columns", 'virtue' ),
		),
	) );
	$kt_portfolio_page->add_field( array(
		'name'    => __('Filter?', 'virtue'),
		'desc'    => '',
		'id'      => $prefix . 'portfolio_filter',
		'type'    => 'select',
		'options' => array(
			'yes' => __('Yes', 'virtue'),
			'no' => __('No', 'virtue'),
		),
	) );
	$kt_portfolio_page->add_field( array(
        'name' => __('Portfolio Work Types', 'virtue'),
        'desc' => __('You can have filterable portfolios with one work type if has children', 'virtue'),
        'id' => $prefix .'portfolio_type',
        'type' 		=> 'kt_select_type',
		'taxonomy' 	=> 'portfolio-type',
    ) );
    $kt_portfolio_page->add_field( array(
		'name'    => __('Order Items By', 'virtue'),
		'desc'    => '',
		'id'      => $prefix . 'portfolio_order',
		'type'    => 'select',
		'options' => array(
			'menu_order' 	=> __("Menu Order", 'virtue' ),
			'date' 			=> __("Date", 'virtue' ),
			'title' 		=> __("Title", 'virtue' ),
			'rand' 			=> __("Random", 'virtue' ),
		),
	) );
	$kt_portfolio_page->add_field( array(
		'name'    => __('Items per Page', 'virtue'),
		'desc'    => __('How many portfolio items per page', 'virtue'),
		'id'      => $prefix . 'portfolio_items',
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
	$kt_portfolio_page->add_field( array(
		'name'    => __('Portfolio Layout Style:', 'virtue'),
		'desc'    => '',
		'id'      => $prefix . 'portfolio_style',
		'type'    => 'select',
		'options' => array(
			'standard' => __('Standard', 'virtue'),
			'mosaic' => __('Mosaic (limited options)', 'virtue'),
		),
	) );
	$kt_portfolio_page->add_field( array(
		'name'    => __('Crop images to equal height', 'virtue'),
		'desc'    => __('If cropped rows will be equal', 'virtue'),
		'id'      => $prefix . 'portfolio_crop',
		'type'    => 'select',
		'options' => array(
			'yes' => __('Yes', 'virtue'), 
			'no' => __('No', 'virtue'), 
		),
	) );
	$kt_portfolio_page->add_field( array(
		'name'    => __('Set image height if cropping', 'virtue'),
		'desc'    => __('Default is 1:1 ratio (Note: just input number, example: 350)', 'virtue'),
		'id'      => $prefix . 'portfolio_img_crop',
		'type'    => 'text_small',
	) );
	$kt_portfolio_page->add_field( array(
		'name' => __('Display Item Work Types', 'virtue'),
		'desc' => '',
		'id'   => $prefix . 'portfolio_item_types',
		'type' => 'checkbox',
	) );
	$kt_portfolio_page->add_field( array(
		'name' => __('Display Item Excerpt', 'virtue'),
		'desc' => '',
		'id'   => $prefix . 'portfolio_item_excerpt',
		'type' => 'checkbox',
	) );
	$kt_portfolio_page->add_field( array(
		'name'    => __('Add Lightbox link in the top right of each item', 'virtue'),
		'desc'    => '',
		'id'      => $prefix . 'portfolio_lightbox',
		'type'    => 'select',
		'options' => array(
			'no' => __('No', 'virtue'), 
			'yes' => __('Yes', 'virtue'), 
		),
	) );
}

add_filter( 'cmb2_admin_init', 'virtue_portfolio_cat_template_metaboxes');
function virtue_portfolio_cat_template_metaboxes(){
	$prefix = '_kad_';
	$kt_portfolio_page = new_cmb2_box( array(
		'id'         => 'portfolio_cat_metabox',
		'title'      => __('Portfolio Page Options', 'virtue'),
		'object_types'      => array( 'page' ), // Post type
		'show_on' => array('key' => 'page-template', 'value' => array( 'page-portfolio-category.php')),
		'priority'   => 'high',
	) );
	$kt_portfolio_page->add_field( array(
		'name'    => __('Columns', 'virtue'),
		'desc'    => '',
		'id'      => $prefix . 'portfolio_columns',
		'type'    => 'select',
		'options' => array(
			'4' 	=> __("Four Columns", 'virtue' ),
			'3' 	=> __("Three Columns", 'virtue' ),
			'2' 	=> __("Two Columns", 'virtue' ),
			'5' 	=> __("Five Columns", 'virtue' ),
			'6' 	=> __("Six Columns", 'virtue' ),
		),
	) );
	$kt_portfolio_page->add_field( array(
		'name'    => __('Items per Page', 'virtue'),
		'desc'    => __('How many portfolio items per page', 'virtue'),
		'id'      => $prefix . 'portfolio_items',
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
	$kt_portfolio_page->add_field( array(
		'name'    => __('Crop images to equal height', 'virtue'),
		'desc'    => __('If cropped rows will be equal', 'virtue'),
		'id'      => $prefix . 'portfolio_crop',
		'type'    => 'select',
		'options' => array(
			'yes' => __('Yes', 'virtue'), 
			'no' => __('No', 'virtue'), 
		),
	) );
	$kt_portfolio_page->add_field( array(
		'name'    => __('Set image height if cropping', 'virtue'),
		'desc'    => __('Default is 1:1 ratio (Note: just input number, example: 350)', 'virtue'),
		'id'      => $prefix . 'portfolio_img_crop',
		'type'    => 'text_small',
	) );
	$kt_portfolio_page->add_field( array(
		'name' => __('Display Item Excerpt', 'virtue'),
		'desc' => '',
		'id'   => $prefix . 'portfolio_item_excerpt',
		'type' => 'checkbox',
	) );

}

add_filter( 'cmb2_admin_init', 'virtue_testimonial_metaboxes');
function virtue_testimonial_metaboxes(){
	$prefix = '_kad_';
	$kt_testimonial_post = new_cmb2_box( array(
		'id'         => 'testimonial_post_metabox',
		'title'      => __('Testimonial Options', 'virtue'),
		'object_types'      => array( 'testimonial' ), // Post type
		'priority'   => 'high',
	) );
	$kt_testimonial_post->add_field( array(
		'name' => __('Text Next To Name', 'virtue'),
		'desc' => __('ex: New York, NY', 'virtue'),
		'id'   => $prefix . 'testimonial_location',
		'type' => 'text',
	) );
	$kt_testimonial_post->add_field( array(
		'name'    => __('Client Title (single post only)', 'virtue'),
		'desc'    => __('ex: CEO of Example Inc', 'virtue'),
		'id'      => $prefix . 'testimonial_occupation',
		'type' => 'text',
	) );
	$kt_testimonial_post->add_field( array(
		'name'    => __('Link', 'virtue'),
		'desc'    => __('ex: http://www.example.com', 'virtue'),
		'id'      => $prefix . 'testimonial_link',
		'type' => 'text',
	) );
} 

add_filter( 'cmb2_admin_init', 'virtue_staff_sidebar_metaboxes');
function virtue_staff_sidebar_metaboxes(){
	$prefix = '_kad_';
	$kt_page_sidebar = new_cmb2_box( array(
		'id'         => 'staff_sidebar',
		'title'      => __('Sidebar Options', 'virtue'),
		'object_types'      => array( 'staff' ), // Post type
		'priority'   => 'high',
	) );
	$kt_page_sidebar->add_field( array(
		'name' => __('Display Sidebar?', 'virtue'),
		'desc' => __('Choose if layout is fullwidth or sidebar', 'virtue'),
		'id'   => $prefix . 'post_sidebar',
		'type'    => 'select',
		'options' => array(
			'yes' => __('Yes', 'virtue'),
			'no' => __('No', 'virtue'),
		),
	) );
	$kt_page_sidebar->add_field( array(
		'name'    => __('Choose Sidebar', 'virtue'),
		'desc'    => '',
		'id'      => $prefix . 'sidebar_choice',
		'type'    => 'select',
		'options' => virtue_cmb_sidebar_options(),				
	) );
}
