<?php
/**
 * Single Product Image
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.3.2
 */


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post, $woocommerce, $product, $virtue;

$columns           = apply_filters( 'woocommerce_product_thumbnails_columns', 5 );
$post_thumbnail_id = get_post_thumbnail_id( $post->ID );
$full_size_image   = wp_get_attachment_image_src( $post_thumbnail_id, 'full' );
$image_title       = get_post_field( 'post_excerpt', $post_thumbnail_id );
if(!empty($image_title)) {
	$light_title  = $image_title;
} else {
	$light_title  = get_the_title($post_thumbnail_id);
}
$placeholder       = has_post_thumbnail() ? 'with-images' : 'without-images';
$wrapper_classes   = apply_filters( 'woocommerce_single_product_image_gallery_classes', array(
	'woocommerce-product-gallery',
	'woocommerce-product-gallery--' . $placeholder,
	'woocommerce-product-gallery--columns-' . absint( $columns ),
	'images',
	'kad-light-gallery',
) );
if ( version_compare( WC_VERSION, '3.0', '>' ) ) {
	if(isset($virtue['product_gallery_slider']) && 1 == $virtue['product_gallery_slider']) {
		$galleryslider = 'woo_product_slider_enabled';
		$galslider = true;
	} else {
		$galleryslider = 'woo_product_slider_disabled';
		$galslider = false;
	}
	if(isset($virtue['product_gallery_zoom']) && 1 == $virtue['product_gallery_zoom']) {
		$galleryzoom = 'woo_product_zoom_enabled';
		$galzoom = true;
	} else {
		$galleryzoom= 'woo_product_zoom_disabled';
		$galzoom = false;
	}
} else {
	$galleryslider = 'woo_product_slider_disabled';
	$galslider = false;
	$galleryzoom = 'woo_product_zoom_disabled';
	$galzoom = false;
}
if(isset($virtue['product_simg_resize']) && $virtue['product_simg_resize'] == 0) {
	$presizeimage = 0;
} else {
	$presizeimage = 1;
	$productimgwidth = 458;
	$productimgheight = 458;
}

?>
<div class="<?php echo esc_attr( implode( ' ', array_map( 'sanitize_html_class', $wrapper_classes ) ) ); ?>" data-columns="<?php echo esc_attr( $columns ); ?>">
	<figure class="woocommerce-product-gallery__wrapper <?php echo esc_attr($galleryslider.' '.$galleryzoom);?>">
	<?php
		if(! $galslider) {
			echo '<div class="product_image">';
		}

		$attributes = array(
			'title'                   => $image_title,
			'data-caption'            => get_post_field( 'post_excerpt', $post_thumbnail_id ),
			'data-src'                => $full_size_image[0],
			'data-large_image'        => $full_size_image[0],
			'data-large_image_width'  => $full_size_image[1],
			'data-large_image_height' => $full_size_image[2],
		);
		if ( has_post_thumbnail() ) {
			if($presizeimage == 1){
				$alt = esc_attr( get_post_meta(get_post_thumbnail_id(), '_wp_attachment_image_alt', true) );
				if( !empty($alt) ) {
					$alttag	= $alt;
				} else {
					$alttag	= $light_title;
				}
		        $html  = '<div data-thumb="' . get_the_post_thumbnail_url( $post->ID, 'shop_thumbnail' ) . '" class="woocommerce-product-gallery__image"><a href="' . esc_url( $full_size_image[0] ) . '" title="'.esc_attr($light_title).'">';
				$html .= virtue_get_full_image_output($productimgwidth, $productimgheight, true, 'attachment-shop_single shop_single wp-post-image', $alttag, $post_thumbnail_id, false, false, false, $attributes);
				$html .= '</a></div>';
			} else {
				$html  = '<div data-thumb="' . get_the_post_thumbnail_url( $post->ID, 'shop_thumbnail' ) . '" class="woocommerce-product-gallery__image"><a href="' . esc_url( $full_size_image[0] ) . '">';
				$html .= get_the_post_thumbnail( $post->ID, 'shop_single', $attributes );
				$html .= '</a></div>';
			}
		} else {
			$html  = '<div class="woocommerce-product-gallery__image--placeholder">';
			$html .= sprintf( '<img src="%s" alt="%s" class="wp-post-image" />', esc_url( wc_placeholder_img_src() ), esc_html__( 'Awaiting product image', 'virtue' ) );
			$html .= '</div>';
		}

		echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', $html, get_post_thumbnail_id( $post->ID ) );
		
		if(! $galslider) {
			echo '</div>';
		}
		if(! $galslider) {
			echo '<div class="product_thumbnails thumbnails">';
		}

		do_action( 'woocommerce_product_thumbnails' ); 

		if(! $galslider) {
			echo '</div>';
		}?>		
	</figure>
</div>

