<?php

/**
 * Registering meta sections for taxonomies
 *
 * All the definitions of meta sections are listed below with comments, please read them carefully.
 * Note that each validation method of the Validation Class MUST return value.
 *
 * You also should read the changelog to know what has been changed
 *
 */

// Hook to 'admin_init' to make sure the class is loaded before
// (in case using the class in another plugin)
add_action( 'admin_init', 'kad_register_taxonomy_meta_boxes' );

/**
 * Register meta boxes
 *
 * @return void
 */
function kad_register_taxonomy_meta_boxes()
{
	// Make sure there's no errors when the plugin is deactivated or during upgrade
	if ( !class_exists( 'RW_Taxonomy_Meta_Kad' ) )
		return;

	$meta_sections = array();

	// First meta section
	$meta_sections[] = array(
		'title'      => 'Extra Product Category Options',             // section title
		'taxonomies' => array('product_cat'), 							// list of taxonomies. Default is array('category', 'post_tag'). Optional
		'id'         => 'product_cat_slider', 						// ID of each section, will be the option name

		'fields' => array(                             // List of meta fields
			// TEXT
			array(
				'name' => 'Category Slider',                      // field name
				'desc' => 'Add a slider shortcode here from the Revslider or Cyclone Slider',         // field description, optional
				'id'   => 'cat_short_slider',                      // field id, i.e. the meta key
				'type' => 'text',                      // field type
				'std'  => '',                      // default value, optional
			),
		),
	);
	$meta_sections[] = array(
		'title'      => 'Extra Product Category Options',             // section title
		'taxonomies' => array('portfolio-type'), 							// list of taxonomies. Default is array('category', 'post_tag'). Optional
		'id'         => 'portfolio_cat_image', 						// ID of each section, will be the option name

		'fields' => array(                             // List of meta fields
			// TEXT
			array(
					    'name' => __('Category Image', 'virtue' ),
					    'id' => 'category_image',
					    'type' => 'image',
					),
		),
	);
	$meta_sections[] = array(
		'title'      => __('Archive Title Setting', 'virtue'),             // section title
		'taxonomies' => array('product_cat', 'product_tag','category', 'post_tag', 'portfolio-type', 'portfolio-tag'), 							// list of taxonomies. Default is array('category', 'post_tag'). Optional
		'id'         => 'kad_cat_title', 						// ID of each section, will be the option name

		'fields' => array(                             // List of meta fields
			// TEXT
			array(
						'name'    => __("Show archive title?", 'virtue' ),
						'id'      => 'archive_show_title',
						'type'    => 'select',
						'options' => array(
							'default' => __("Default", 'virtue' ),
							'show' => __("Show", 'virtue' ),
							'hide' => __("Hide", 'virtue' ),
						),
					),
		),
	);

	foreach ( $meta_sections as $meta_section )
	{
		new RW_Taxonomy_Meta_Kad( $meta_section );
	}
}