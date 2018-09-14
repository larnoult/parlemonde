<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
add_filter( 'cmb2_admin_init', 'virtue_product_metaboxes');
function virtue_product_metaboxes(){
	$prefix = '_kad_';
	$kt_product_post = new_cmb2_box( array(
		'id'         => 'product_post_side_metabox',
		'title'      => __('Product Sidebar Options', 'virtue'),
		'object_types'      => array( 'product' ), // Post type
		'priority'   => 'default',
	) );
	$kt_product_post->add_field( array(
		'name' => __('Display Sidebar?', 'virtue'),
		'desc' => __('Choose if layout is fullwidth or sidebar', 'virtue'),
		'id'   => $prefix . 'post_sidebar',
		'type'    => 'select',
		'options' => array(
			'default' => __('Default', 'virtue'),
			'no' => __('No', 'virtue'),
			'yes' => __('Yes', 'virtue'),
		),
	) );
	$kt_product_post->add_field( array(
		'name'    => __('Choose Sidebar', 'virtue'),
		'desc'    => '',
		'id'      => $prefix . 'sidebar_choice',
		'type'    => 'select',
		'options' => virtue_cmb_product_sidebar_options(),
	) );
}
add_filter( 'cmb2_admin_init', 'virtue_productvideo_metaboxes');
function virtue_productvideo_metaboxes(){
	$prefix = '_kad_';
	$kt_productvideo_post = new_cmb2_box( array(
		'id'         => 'product_post_metabox',
		'title'      => __('Product Video Tab', 'virtue'),
		'object_types'      => array( 'product' ), // Post type
		'priority'   => 'default',
	) );
	$kt_productvideo_post->add_field( array(
		'name' => __('Product Video', 'virtue'),
		'desc' => __('Place Embed Code Here, works with youtube, vimeo...', 'virtue'),
		'id'   => $prefix . 'product_video',
		'type' => 'textarea_code',
	) );
} 

add_filter( 'cmb2_admin_init', 'virtue_product_tab_metaboxes');
function virtue_product_tab_metaboxes(){
	$prefix = '_kad_';
	global $virtue_premium;	
	if(isset($virtue_premium['custom_tab_01']) && $virtue_premium['custom_tab_01'] == '1') {
		$kt_custom_tab_01 = new_cmb2_box( array(
			'id'         	=> 'kad_custom_tab_01',
			'title'      	=> __("Custom Tab 01", 'virtue'),
			'object_types'  => array('product'),
			'priority'   	=> 'default',
		) );
		$kt_custom_tab_01->add_field( array(
			'name' => __( "Tab Title", 'virtue' ),
			'desc' => __( "This will show on the tab", 'virtue' ),
			'id'   => $prefix . 'tab_title_01',
			'type' => 'text',
		) );
		$kt_custom_tab_01->add_field( array(
			'name'    => __("Tab Content", 'virtue' ),
			'desc'    =>  __( "Add Tab Content", 'virtue' ),
			'id'      => $prefix . 'tab_content_01',
			'type'    => 'wysiwyg',
		) );
		$kt_custom_tab_01->add_field( array(
			'name' => __( "Tab priority", 'virtue' ),
			'desc' => __( "This will determine where the tab is shown (e.g. 20)", 'virtue' ),
			'id'   => $prefix . 'tab_priority_01',
			'type' => 'text',
		) );
	}
	if(isset($virtue_premium['custom_tab_02']) && $virtue_premium['custom_tab_02'] == '1') {
		$kt_custom_tab_02 = new_cmb2_box( array(
			'id'         	=> 'kad_custom_tab_02',
			'title'      	=> __("Custom Tab 02", 'virtue'),
			'object_types'  => array('product'),
			'priority'   	=> 'default',
		) );
		$kt_custom_tab_02->add_field( array(
			'name' => __( "Tab Title", 'virtue' ),
			'desc' => __( "This will show on the tab", 'virtue' ),
			'id'   => $prefix . 'tab_title_02',
			'type' => 'text',
		) );
		$kt_custom_tab_02->add_field( array(
			'name'    => __("Tab Content", 'virtue' ),
			'desc'    =>  __( "Add Tab Content", 'virtue' ),
			'id'      => $prefix . 'tab_content_02',
			'type'    => 'wysiwyg',
		) );
		$kt_custom_tab_02->add_field( array(
			'name' => __( "Tab priority", 'virtue' ),
			'desc' => __( "This will determine where the tab is shown (e.g. 20)", 'virtue' ),
			'id'   => $prefix . 'tab_priority_02',
			'type' => 'text',
		) );
	}
	if(isset($virtue_premium['custom_tab_03']) && $virtue_premium['custom_tab_03'] == '1') {
		$kt_custom_tab_03 = new_cmb2_box( array(
			'id'         	=> 'kad_custom_tab_03',
			'title'      	=> __("Custom Tab 03", 'virtue'),
			'object_types'  => array('product'),
			'priority'   	=> 'default',
		) );
		$kt_custom_tab_03->add_field( array(
			'name' => __( "Tab Title", 'virtue' ),
			'desc' => __( "This will show on the tab", 'virtue' ),
			'id'   => $prefix . 'tab_title_03',
			'type' => 'text',
		) );
		$kt_custom_tab_03->add_field( array(
			'name'    => __("Tab Content", 'virtue' ),
			'desc'    =>  __( "Add Tab Content", 'virtue' ),
			'id'      => $prefix . 'tab_content_03',
			'type'    => 'wysiwyg',
		) );
		$kt_custom_tab_03->add_field( array(
			'name' => __( "Tab priority", 'virtue' ),
			'desc' => __( "This will determine where the tab is shown (e.g. 20)", 'virtue' ),
			'id'   => $prefix . 'tab_priority_03',
			'type' => 'text',
		) );
	}
}
