<?php
/**
 * Single Product Thumbnails
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.3.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post, $product, $virtue;

if ( version_compare( WC_VERSION, '3.0', '>' ) ) {
	$attachment_ids = $product->get_gallery_image_ids();
	if(isset($virtue['product_gallery_slider']) && 1 == $virtue['product_gallery_slider']) {
		$galslider = true;
		$output_size = 'shop_single';
	} else {
		$galslider = false;
		$output_size = 'shop_thumbnail';
	}
} else {
	$attachment_ids = $product->get_gallery_attachment_ids();
	$galslider = false;
	$output_size = 'shop_thumbnail';
}


if ( $attachment_ids ) {
	if(isset($virtue['product_simg_resize']) && 0 == $virtue['product_simg_resize'] || false == $galslider) {
		$presizeimage = 0;
	} else {
		$presizeimage = 1;
		$productimgwidth = 458;
		$productimgheight = 458;
	}

		foreach ( $attachment_ids as $attachment_id ) {
			$full_size_image  = wp_get_attachment_image_src( $attachment_id, 'full' );
			$thumbnail        = wp_get_attachment_image_src( $attachment_id, 'shop_thumbnail' );
			$image_title       = get_post_field( 'post_excerpt', $attachment_id );
			if(!empty($image_title)) {
				$light_title  = $image_title;
			} else {
				$light_title  = get_the_title($attachment_id );
			}
			$attributes = array(
				'title'                   => $image_title,
				'data-caption'            => get_post_field( 'post_excerpt', $attachment_id ),
				'data-src'                => $full_size_image[0],
				'data-large_image'        => $full_size_image[0],
				'data-large_image_width'  => $full_size_image[1],
				'data-large_image_height' => $full_size_image[2],
			);
			if($presizeimage == 1){
				$html  = '<div data-thumb="' .  esc_url( $thumbnail[0] ) . '" class="woocommerce-product-gallery__image"><a href="' . esc_url( $full_size_image[0] ) . '" data-rel="lightbox[product-gallery]" title="'.esc_attr($light_title).'">';
				$html .= virtue_get_full_image_output($productimgwidth, $productimgheight, true, 'attachment-shop_single shop_single wp-post-image', $light_title, $attachment_id, false, false, false, $attributes);
				$html .= '</a></div>';
			} else {
				$html  = '<div data-thumb="' . esc_url( $thumbnail[0] ) . '" class="woocommerce-product-gallery__image"><a href="' . esc_url( $full_size_image[0] ) . '" data-rel="lightbox[product-gallery]" title="'.esc_attr($light_title).'">';
				$html .= wp_get_attachment_image( $attachment_id, $output_size, false, $attributes );
		 		$html .= '</a></div>';
		 	}

			echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', $html, $attachment_id );
		}

}