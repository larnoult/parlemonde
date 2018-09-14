<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
function virtue_gallery_field( $field, $meta ) {
    echo '<div class="kad-gallery kad_widget_image_gallery">';
    echo '<div class="gallery_images">';
    $attachments = array_filter( explode( ',', $meta ) );
            if ( $attachments ) :
                foreach ( $attachments as $attachment_id ) {
                    $img = wp_get_attachment_image_src($attachment_id, 'thumbnail');
                    $imgfull = wp_get_attachment_image_src($attachment_id, 'full');
                    echo '<a class="of-uploaded-image edit-kt-meta-gal" data-attachment-id="'.esc_attr($attachment_id).'"  href="#">';
                    echo '<img class="gallery-widget-image" id="gallery_widget_image_'.esc_attr($attachment_id). '" src="' . esc_url($img[0]) . '" width="'.esc_attr($img[1]).'" height="'.esc_attr($img[2]).'" />';
                    echo '</a>';
                }
            endif;
    echo '</div>';
    echo ' <input type="hidden" id="' . esc_attr($field->args( 'id' )) . '" name="' . esc_attr($field->args( 'id' )) . '" class="gallery_values" value="' . esc_attr($meta) . '" />';
    echo '<a href="#" onclick="return false;" id="edit-gallery" class="gallery-attachments button button-primary">' . __('Add/Edit Gallery', 'virtue') . '</a>';
    echo '<a href="#" onclick="return false;" id="clear-gallery" class="gallery-attachments button">' . __('Clear Gallery', 'virtue') . '</a>';
    echo '</div>';
    $desc = $field->args('desc');
    if ( !empty( $desc)) {
        echo '<p class="cmb_metabox_description">' . $field->args( 'desc' ) . '</p>';
    }
}
add_filter( 'cmb2_render_kad_gallery', 'virtue_gallery_field', 10, 2 );

// Select Options List
function virtue_cmb_sidebar_options() {
    global $virtue_premium;
    $sidebars = array();
    $nonsidebars = array(
    	'topbar_widget',
        'homewidget',
        'footer_1',
        'footer_2',
        'footer_3',
        'footer_4',
        'footer_5',
        );
    foreach ( $GLOBALS['wp_registered_sidebars'] as $sidebar ) { 
        if(!in_array($sidebar['id'], $nonsidebars) ){
            $sidebars[ $sidebar['id'] ] = $sidebar['name'];
        }
    }
    return $sidebars;
}
function virtue_cmb_post_sidebar_options() {
    global $virtue_premium;
    $sidebars = array(
        'default' => __('Theme Options Default', 'virtue')
    );
    $nonsidebars = array(
    	'topbar_widget',
        'homewidget',
        'footer_1',
        'footer_2',
        'footer_3',
        'footer_4',
        'footer_5',
        );
    foreach ( $GLOBALS['wp_registered_sidebars'] as $sidebar ) { 
        if(!in_array($sidebar['id'], $nonsidebars) ){
            $sidebars[ $sidebar['id'] ] = $sidebar['name'];
        }
    }
    return $sidebars;
}
function virtue_cmb_product_sidebar_options() {
    global $virtue_premium;
    $sidebars = array(
        'default' => __('Default', 'virtue')
    );
    $nonsidebars = array(
    	'topbar_widget',
        'homewidget',
        'footer_1',
        'footer_2',
        'footer_3',
        'footer_4',
        'footer_5',
        );
    foreach ( $GLOBALS['wp_registered_sidebars'] as $sidebar ) { 
        if(!in_array($sidebar['id'], $nonsidebars) ){
            $sidebars[ $sidebar['id'] ] = $sidebar['name'];
        }
    }
    return $sidebars;
}

function virtue_cmb_page_options() {
    $allpages = array(
        'default' => __('Theme Options Default', 'virtue')
    );
    $pages = get_pages(); 
    foreach ( $pages as $page ) { 
            $allpages[ $page->ID ] = $page->post_title;
    }
    return $allpages;
}
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


add_filter( 'cmb_render_hide_type', 'virtue_meta_hide_type', 10, 2 );
function virtue_meta_hide_type( $field, $meta ) {
	echo '';
}
add_action( 'cmb2_render_kt_text_number', 'virtue_render_kt_text_number', 10, 5 );
function virtue_render_kt_text_number($field, $meta, $object_id, $object_type, $field_type_object) {
    echo $field_type_object->input( array( 'class' => 'cmb_text_small', 'type' => 'number' ) );
}

add_action( 'cmb2_render_kt_select_category', 'virtue_render_select_category', 10, 2 );
function virtue_render_select_category( $field, $meta ) {
    wp_dropdown_categories(array(
            'show_option_none' => __( "All Blog Posts", 'virtue' ),
            'hierarchical' => 1,
            'taxonomy' => 'category',
            'orderby' => 'name', 
            'hide_empty' => 0, 
            'name' => $field->args( 'id' ),
            'selected' => $meta  

        ));
    $desc = $field->args( 'desc' );
    if ( !empty( $desc ) ) {
    	echo '<p class="cmb_metabox_description">' . $desc . '</p>';
    }
}

add_action( 'cmb2_render_kt_select_group', 'virtue_render_select_group', 10, 2 );
function virtue_render_select_group( $field, $meta ) {
    wp_dropdown_categories(array(
            'show_option_none' => __( "All Groups", 'virtue' ),
            'hierarchical' => 1,
            'taxonomy' => $field->args( 'taxonomy'),
            'orderby' => 'name', 
            'hide_empty' => 0, 
            'name' => $field->args( 'id' ),
            'selected' => $meta  

        ));
    $desc = $field->args( 'desc' );
    if ( !empty( $desc ) ) {
    	echo '<p class="cmb_metabox_description">' . $desc . '</p>';
    }
}

add_action( 'cmb2_render_kt_select_type', 'virtue_render_select_type', 10, 2 );
function virtue_render_select_type( $field, $meta ) {
    wp_dropdown_categories(array(
            'show_option_none' => __( "All Types", 'virtue' ),
            'hierarchical' => 1,
            'taxonomy' => $field->args( 'taxonomy'),
            'orderby' => 'name', 
            'hide_empty' => 0, 
            'name' => $field->args( 'id' ),
            'selected' => $meta  

        ));
    $desc = $field->args( 'desc' );
    if ( !empty( $desc ) ) {
    	echo '<p class="cmb_metabox_description">' . $desc . '</p>';
    }
}

require_once trailingslashit( get_template_directory() ) . 'lib/metaboxes/virtue-metabox-pages.php'; // Custom metaboxes.
require_once trailingslashit( get_template_directory() ) . 'lib/metaboxes/virtue-metabox-posts.php'; // Custom metaboxes.
require_once trailingslashit( get_template_directory() ) . 'lib/metaboxes/virtue-metabox-post-types.php'; // Custom metaboxes.
require_once trailingslashit( get_template_directory() ) . 'lib/metaboxes/virtue-metabox-product.php'; // Custom metaboxes.
