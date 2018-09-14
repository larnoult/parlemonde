<?php
/**
 * Meta Boxes
 *
 * @package Virtue Theme
 */

/**
 * Virtue Sidebar Options
 */
function virtue_cmb_sidebar_options() {
	$nonsidebars = array(
		'topbarright',
		'footer_1',
		'footer_2',
		'footer_3',
		'footer_4',
		'footer_third_1',
		'footer_third_2',
		'footer_third_3',
		'footer_double_1',
		'footer_double_2',
	);
	foreach ( $GLOBALS['wp_registered_sidebars'] as $sidebar ) {
		if ( ! in_array( $sidebar['id'], $nonsidebars ) ) {
			$sidebars[ $sidebar['id'] ] = $sidebar['name'];
		}
	}
	return $sidebars;
}

/**
 * Virtue post category select
 *
 * @param object $field meta feild.
 * @param string $meta selected value.
 */
function virtue_render_select_category( $field, $meta ) {
	wp_dropdown_categories( array(
		'show_option_none' => __( 'All Blog Posts', 'virtue' ),
		'hierarchical'     => 1,
		'taxonomy'         => 'category',
		'orderby'          => 'name',
		'hide_empty'       => 0,
		'name'             => $field->args( 'id' ),
		'selected'         => $meta,
	) );
	$desc = $field->args( 'desc' );
	if ( ! empty( $desc ) ) {
		echo '<p class="cmb_metabox_description">' . esc_html( $desc ) . '</p>';
	}
}
add_action( 'cmb2_render_virtue_select_category', 'virtue_render_select_category', 10, 2 );

/**
 * Virtue post category select
 *
 * @param bool  $display is shown.
 * @param array $meta_box meta info.
 */
function virtue_metabox_display_default_page_template( $display, $meta_box ) {
	if ( ! isset( $meta_box['show_on']['key'] ) || 'default-template' !== $meta_box['show_on']['key'] ) {
		return $display;
	}
	if ( basename( get_page_template() ) === 'page.php' ) {
		return true;
	}

	return false;
}
add_filter( 'cmb2_show_on', 'virtue_metabox_display_default_page_template', 10, 2 );

/**
 * Virtue metaboxes
 */
function virtue_metaboxes() {

	// Start with an underscore to hide fields from custom fields list.
	$prefix = '_kad_';

	// Blog Posts.
	$virtue_blog_post = new_cmb2_box( array(
		'id'           => 'post_metabox',
		'title'        => __( 'Post Options', 'virtue' ),
		'object_types' => array( 'post' ),
		'priority'     => 'high',
	) );
	$virtue_blog_post->add_field( array(
		'name'    => __( 'Head Content', 'virtue' ),
		'desc'    => '',
		'id'      => $prefix . 'blog_head',
		'type'    => 'select',
		'options' => array(
			'default' => __( 'Default', 'virtue' ),
			'none'    => __( 'None', 'virtue' ),
			'flex'    => __( 'Image Slider', 'virtue' ),
			'video'   => __( 'Video', 'virtue' ),
			'image'   => __( 'Image', 'virtue' ),
		),
	) );
	$virtue_blog_post->add_field( array(
		'name' => __( 'Max Image/Slider Height', 'virtue' ),
		'desc' => __( 'Default is: 400 <b>(Note: just input number, example: 350)</b>', 'virtue' ),
		'id'   => $prefix . 'posthead_height',
		'type' => 'text_small',
	) );
	$virtue_blog_post->add_field( array(
		'name' => __( 'Max Image/Slider Width', 'virtue' ),
		'desc' => __( 'Default is: 770 or 1140 on fullwidth posts <b>(Note: just input number, example: 650, does not apply to carousel slider)</b>', 'virtue' ),
		'id'   => $prefix . 'posthead_width',
		'type' => 'text_small',
	) );
	$virtue_blog_post->add_field( array(
		'name'    => __( 'Post Summary', 'virtue' ),
		'desc'    => '',
		'id'      => $prefix . 'post_summery',
		'type'    => 'select',
		'options' => array(
			'default'          => __( 'Default', 'virtue' ),
			'text'             => __( 'Text', 'virtue' ),
			'img_portrait'     => __( 'Portrait Image', 'virtue' ),
			'img_landscape'    => __( 'Landscape Image', 'virtue' ),
			'slider_portrait'  => __( 'Portrait Image Slider', 'virtue' ),
			'slider_landscape' => __( 'Landscape Image Slider', 'virtue' ),
			'video'            => __( 'Video', 'virtue' ),
		),
	) );
	$virtue_blog_post->add_field( array(
		'name'    => __( 'Display Sidebar?', 'virtue' ),
		'desc'    => __( 'Choose if layout is fullwidth or sidebar', 'virtue' ),
		'id'      => $prefix . 'post_sidebar',
		'type'    => 'select',
		'options' => array(
			'yes' => __( 'Yes', 'virtue' ),
			'no'  => __( 'No', 'virtue' ),
		),
	) );
	$virtue_blog_post->add_field( array(
		'name'    => __( 'Choose Sidebar', 'virtue' ),
		'desc'    => '',
		'id'      => $prefix . 'sidebar_choice',
		'type'    => 'select',
		'options' => virtue_cmb_sidebar_options(),
	) );
	$virtue_blog_post->add_field( array(
		'name'    => __( 'Author Info', 'virtue' ),
		'desc'    => __( 'Display an author info box?', 'virtue' ),
		'id'      => $prefix . 'blog_author',
		'type'    => 'select',
		'options' => array(
			'default' => __( 'Default', 'virtue' ),
			'no'      => __( 'No', 'virtue' ),
			'yes'     => __( 'Yes', 'virtue' ),
		),
	) );
	$virtue_blog_post->add_field( array(
		'name'    => __( 'Posts Carousel', 'virtue' ),
		'desc'    => __( 'Display a carousel with similar or recent posts?', 'virtue' ),
		'id'      => $prefix . 'blog_carousel_similar',
		'type'    => 'select',
		'options' => array(
			'default' => __( 'Default', 'virtue' ),
			'no'      => __( 'No', 'virtue' ),
			'recent'  => __( 'Yes - Display Recent Posts', 'virtue' ),
			'similar' => __( 'Yes - Display Similar Posts', 'virtue' ),
		),
	) );
	$virtue_blog_post->add_field( array(
		'name' => __( 'Carousel Title', 'virtue' ),
		'desc' => __( 'ex. Similar Posts', 'virtue' ),
		'id'   => $prefix . 'blog_carousel_title',
		'type' => 'text_medium',
	) );

	// Blog List Page.
	$virtue_blog_page = new_cmb2_box( array(
		'id'           => 'bloglist_metabox',
		'title'        => __( 'Blog List Options', 'virtue' ),
		'object_types' => array( 'page' ),
		'show_on'      => array(
			'key'   => 'page-template',
			'value' => 'page-blog.php',
		),
		'priority'     => 'high',
	) );
	$virtue_blog_page->add_field( array(
		'name' => __( 'Blog Category', 'virtue' ),
		'desc' => __( 'Select all blog posts or a specific category to show', 'virtue' ),
		'id'   => $prefix . 'blog_cat',
		'type' => 'virtue_select_category',
	) );
	$virtue_blog_page->add_field( array(
		'name'    => __( 'How Many Posts Per Page', 'virtue' ),
		'desc'    => '',
		'id'      => $prefix . 'blog_items',
		'type'    => 'select',
		'options' => array(
			'all' => __( 'All', 'virtue' ),
			'2'   => __( '2', 'virtue' ),
			'3'   => __( '3', 'virtue' ),
			'4'   => __( '4', 'virtue' ),
			'5'   => __( '5', 'virtue' ),
			'6'   => __( '6', 'virtue' ),
			'7'   => __( '7', 'virtue' ),
			'8'   => __( '8', 'virtue' ),
			'9'   => __( '9', 'virtue' ),
			'10'  => __( '10', 'virtue' ),
			'11'  => __( '11', 'virtue' ),
			'12'  => __( '12', 'virtue' ),
			'13'  => __( '13', 'virtue' ),
			'14'  => __( '14', 'virtue' ),
			'15'  => __( '15', 'virtue' ),
			'16'  => __( '16', 'virtue' ),
		),
	) );
	$virtue_blog_page->add_field( array(
		'name'    => __( 'Display Post Content as:', 'virtue' ),
		'desc'    => '',
		'id'      => $prefix . 'blog_summery',
		'type'    => 'select',
		'options' => array(
			'summery' => __( 'Summary', 'virtue' ),
			'full'    => __( 'Full', 'virtue' ),
		),
	) );
	$virtue_blog_page->add_field( array(
		'name'    => __( 'Display Sidebar?', 'virtue' ),
		'desc'    => '',
		'id'      => $prefix . 'page_sidebar',
		'type'    => 'select',
		'options' => array(
			'yes' => __( 'Yes', 'virtue' ),
			'no'  => __( 'No', 'virtue' ),
		),
	) );
	$virtue_blog_page->add_field( array(
		'name'    => __( 'Choose Sidebar', 'virtue' ),
		'desc'    => '',
		'id'      => $prefix . 'sidebar_choice',
		'type'    => 'select',
		'options' => virtue_cmb_sidebar_options(),
	) );

	// Sidebar Page.
	$virtue_sidebar_page = new_cmb2_box( array(
		'id'           => 'page_sidebar',
		'title'        => __( 'Sidebar Options', 'virtue' ),
		'object_types' => array( 'page' ),
		'show_on'      => array(
			'key'   => 'page-template',
			'value' => array( 'page-sidebar.php', 'page-feature-sidebar.php' ),
		),
		'priority'     => 'low',
		'context'      => 'side',
	) );
	$virtue_sidebar_page->add_field( array(
		'name'    => __( 'Choose Sidebar', 'virtue' ),
		'desc'    => '',
		'id'      => $prefix . 'sidebar_choice',
		'type'    => 'select',
		'options' => virtue_cmb_sidebar_options(),
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
	$virtue_default_page->add_field( array(
		'name'    => __( 'Show Page Title', 'virtue' ),
		'id'      => $prefix . 'show_page_title',
		'type'    => 'select',
		'options' => array(
			'default' => __( 'Site Default', 'virtue' ),
			'show'    => __( 'Show', 'virtue' ),
			'hide'    => __( 'Hide', 'virtue' ),
		),
	) );
	// Page templates.
	$virtue_template_pages = new_cmb2_box( array(
		'id'           => 'template_page_settings',
		'title'        => __( 'Page Options', 'virtue' ),
		'object_types' => array( 'page' ),
		'show_on'      => array(
			'key'   => 'page-template',
			'value' => array( 'page-sidebar.php', 'page-feature-sidebar.php', 'page-blog.php', 'page-contact.php', 'page-fullwidth.php', 'page-feature.php', 'page-portfolio.php' ),
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
	$virtue_template_pages->add_field( array(
		'name'    => __( 'Show Page Title', 'virtue' ),
		'id'      => $prefix . 'show_page_title',
		'type'    => 'select',
		'options' => array(
			'default' => __( 'Site Default', 'virtue' ),
			'show'    => __( 'Show', 'virtue' ),
			'hide'    => __( 'Hide', 'virtue' ),
		),
	) );

}
add_filter( 'cmb2_admin_init', 'virtue_metaboxes' );
