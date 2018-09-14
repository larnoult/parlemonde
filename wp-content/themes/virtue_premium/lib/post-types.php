<?php
/**
 * Init Custom post types
 *
 * @package Virtue Theme
 */

/**
 * Init Portfolio posts type.
 */
function virtue_portfolio_post_init() {

	$portfoliolabels = array(
		'name'               => __( 'Portfolio', 'virtue' ),
		'singular_name'      => __( 'Portfolio Item', 'virtue' ),
		'add_new'            => __( 'Add New', 'virtue' ),
		'add_new_item'       => __( 'Add New Portfolio Item', 'virtue' ),
		'edit_item'          => __( 'Edit Portfolio Item', 'virtue' ),
		'new_item'           => __( 'New Portfolio Item', 'virtue' ),
		'all_items'          => __( 'All Portfolio', 'virtue' ),
		'view_item'          => __( 'View Portfolio Item', 'virtue' ),
		'search_items'       => __( 'Search Portfolio', 'virtue' ),
		'not_found'          => __( 'No Portfolio Item found', 'virtue' ),
		'not_found_in_trash' => __( 'No Portfolio Items found in Trash', 'virtue' ),
		'parent_item_colon'  => '',
		'menu_name'          => __( 'Portfolio', 'virtue' ),
	);
	$portargs = array(
		'labels'             => $portfoliolabels,
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'has_archive'        => false,
		'capability_type'    => 'post',
		'hierarchical'       => false,
		'rewrite'            => false,
		'menu_position'      => 8,
		'show_in_rest'       => true,
		'menu_icon'          => 'dashicons-format-gallery',
		'supports'           => array( 'title', 'editor', 'excerpt', 'author', 'page-attributes', 'thumbnail', 'custom-fields', 'comments' ),
	);
	// Initialize Taxonomy Labels.
	$worklabels = array(
		'name'              => __( 'Portfolio Type', 'virtue' ),
		'singular_name'     => __( 'Type', 'virtue' ),
		'search_items'      => __( 'Search Type', 'virtue' ),
		'all_items'         => __( 'All Type', 'virtue' ),
		'parent_item'       => __( 'Parent Type', 'virtue' ),
		'parent_item_colon' => __( 'Parent Type:', 'virtue' ),
		'edit_item'         => __( 'Edit Type', 'virtue' ),
		'update_item'       => __( 'Update Type', 'virtue' ),
		'add_new_item'      => __( 'Add New Type', 'virtue' ),
		'new_item_name'     => __( 'New Type Name', 'virtue' ),
	);
	// Allow slug to be filterd.
	$portfolio_type_slug = apply_filters( 'kadence_portfolio_type_slug', 'portfolio-type' );
	// Register Custom Taxonomy.
	register_taxonomy( 'portfolio-type', array( 'portfolio' ), array(
		'hierarchical' => true, // define whether to use a system like tags or categories.
		'labels'       => $worklabels,
		'show_ui'      => true,
		'query_var'    => true,
		'rewrite'      => array( 'slug' => $portfolio_type_slug ),
	) );
	$taglabels = array(
		'name'              => __( 'Portfolio Tags', 'virtue' ),
		'singular_name'     => __( 'Tags', 'virtue' ),
		'search_items'      => __( 'Search Tags', 'virtue' ),
		'all_items'         => __( 'All Tag', 'virtue' ),
		'parent_item'       => __( 'Parent Tag', 'virtue' ),
		'parent_item_colon' => __( 'Parent Tag:', 'virtue' ),
		'edit_item'         => __( 'Edit Tag', 'virtue' ),
		'update_item'       => __( 'Update Tag', 'virtue' ),
		'add_new_item'      => __( 'Add New Tag', 'virtue' ),
		'new_item_name'     => __( 'New Tag Name', 'virtue' ),
	);
	// Allow slug to be filterd.
	$portfolio_tag_slug = apply_filters( 'kadence_portfolio_tag_slug', 'portfolio-tag' );
	// Register Custom Taxonomy.
	register_taxonomy( 'portfolio-tag', array( 'portfolio' ), array(
		'hierarchical' => false,
		'labels'       => $taglabels,
		'show_ui'      => true,
		'query_var'    => true,
		'rewrite'      => array( 'slug' => $portfolio_tag_slug ),
	) );

	register_post_type( 'portfolio', $portargs );
}
add_action( 'init', 'virtue_portfolio_post_init', 1 );

function virtue_testimonial_post_init() {
  $testlabels = array(
    'name' =>  __('Testimonials', 'virtue'),
    'singular_name' => __('Testimonial', 'virtue'),
    'add_new' => __('Add New', 'virtue'),
    'add_new_item' => __('Add New Testimonial', 'virtue'),
    'edit_item' => __('Edit Testimonial', 'virtue'),
    'new_item' => __('New Testimonial', 'virtue'),
    'all_items' => __('All Testimonials', 'virtue'),
    'view_item' => __('View Testimonial', 'virtue'),
    'search_items' => __('Search Testimonials', 'virtue'),
    'not_found' =>  __('No Testimonials found', 'virtue'),
    'not_found_in_trash' => __('No Testimonials found in Trash', 'virtue'),
    'parent_item_colon' => '',
    'menu_name' => __('Testimonials', 'virtue')
  );
  $testimonial_post_slug = apply_filters('kadence_testimonial_post_slug', 'testimonial');
  $testargs = array(
    'labels' => $testlabels,
    'public' => true,
    'publicly_queryable' => true,
    'show_ui' => true, 
    'show_in_menu' => true, 
    'query_var' => true,
    'rewrite' => array( 'slug' => $testimonial_post_slug ),
    'capability_type' => 'post',
    'has_archive' => false,  
    'hierarchical' => false,
    'menu_position' => 22,
    'show_in_rest'       => true,
    'menu_icon' => 'dashicons-id',
    'supports' => array( 'title', 'excerpt', 'editor', 'page-attributes', 'thumbnail', 'revisions')
  ); 
  // Initialize Taxonomy Labels
    $taxlabels = array(
        'name' => __( 'Testimonial Group', 'virtue' ),
        'singular_name' => __( 'Testimonials', 'virtue' ),
        'search_items' =>  __( 'Search Groups', 'virtue' ),
        'all_items' => __( 'All Groups', 'virtue' ),
        'parent_item' => __( 'Parent Groups', 'virtue' ),
        'parent_item_colon' => __( 'Parent Groups:', 'virtue' ),
        'edit_item' => __( 'Edit Group', 'virtue' ),
        'update_item' => __( 'Update Group', 'virtue' ),
        'add_new_item' => __( 'Add New Group', 'virtue' ),
        'new_item_name' => __( 'New Group Name', 'virtue' ),
    );
    $testimonial_group_slug = apply_filters('kadence_testimonial_group_slug', 'testimonial-group');
    // Register Custom Taxonomy
    register_taxonomy('testimonial-group',array('testimonial'), array(
        'hierarchical' => true, // define whether to use a system like tags or categories
        'labels' => $taxlabels,
        'show_ui' => true,
        'query_var' => true,
        'rewrite'  => array( 'slug' => $testimonial_group_slug )
    ));

  register_post_type( 'testimonial', $testargs );
}
add_action( 'init', 'virtue_testimonial_post_init' );

function virtue_staff_post_init() {
  $stafflabels = array(
    'name' =>  __('Staff', 'virtue'),
    'singular_name' => __('Staff', 'virtue'),
    'add_new' => __('Add New', 'virtue'),
    'add_new_item' => __('Add New Staff', 'virtue'),
    'edit_item' => __('Edit Staff', 'virtue'),
    'new_item' => __('New Staff', 'virtue'),
    'all_items' => __('All Staff', 'virtue'),
    'view_item' => __('View Staff', 'virtue'),
    'search_items' => __('Search Staff', 'virtue'),
    'not_found' =>  __('No Staff found', 'virtue'),
    'not_found_in_trash' => __('No Staff found in Trash', 'virtue'),
    'parent_item_colon' => '',
    'menu_name' => __('Staff', 'virtue')
  );
  $staff_post_slug = apply_filters('kadence_staff_post_slug', 'staff');
  $staffargs = array(
    'labels' => $stafflabels,
    'public' => true,
    'publicly_queryable' => true,
    'show_ui' => true, 
    'show_in_menu' => true, 
    'query_var' => true,
    'rewrite' => array( 'slug' => $staff_post_slug ),
    'capability_type' => 'post',
    'has_archive' => false, 
    'show_in_rest'       => true,
    'hierarchical' => false,
    'menu_position' => 23,
    'menu_icon' => 'dashicons-businessman',
    'supports' => array( 'title', 'excerpt', 'editor', 'page-attributes', 'thumbnail', 'revisions' )
  ); 
  // Initialize Taxonomy Labels
    $grouplabels = array(
        'name' => __( 'Staff Group', 'virtue' ),
        'singular_name' => __( 'Staff', 'virtue' ),
        'search_items' =>  __( 'Search Groups', 'virtue' ),
        'all_items' => __( 'All Groups', 'virtue' ),
        'parent_item' => __( 'Parent Groups', 'virtue' ),
        'parent_item_colon' => __( 'Parent Groups:', 'virtue' ),
        'edit_item' => __( 'Edit Group', 'virtue' ),
        'update_item' => __( 'Update Group', 'virtue' ),
        'add_new_item' => __( 'Add New Group', 'virtue' ),
        'new_item_name' => __( 'New Group Name', 'virtue' ),
    );
    $staff_group_slug = apply_filters('kadence_staff_group_slug', 'staff-group');
    // Register Custom Taxonomy
    register_taxonomy('staff-group',array('staff'), array(
        'hierarchical' => true, // define whether to use a system like tags or categories
        'labels' => $grouplabels,
        'show_ui' => true,
        'query_var' => true,
        'rewrite'  => array( 'slug' => $staff_group_slug )
    ));

  register_post_type( 'staff', $staffargs );
}
add_action( 'init', 'virtue_staff_post_init' );

