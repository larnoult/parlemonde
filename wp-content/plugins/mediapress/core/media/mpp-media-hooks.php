<?php
/**
 * Media hooks.
 *
 * @package mediapress
 */

// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Filter attachment permalink
 *
 * @param string $link attachment url.
 * @param int    $post_id post id.
 *
 * @return string
 */
function mpp_media_filter_permalink( $link, $post_id ) {

	if ( ! mpp_is_valid_media( $post_id ) ) {
		return $link;
	}

	$media = mpp_get_media( $post_id );

	if ( $media->component != 'sitewide' ) {
		return $link;
	}

	// in case of sitewide gallery, the permalink is like.
	$gallery_permalink = mpp_get_gallery_permalink( $media->gallery_id );

	return user_trailingslashit( untrailingslashit( $gallery_permalink ) . '/media/' . $media->slug );

}
add_filter( 'attachment_link', 'mpp_media_filter_permalink', 10, 2 );

// for title.
add_filter( 'mpp_get_media_title', 'wp_kses_post' );
add_filter( 'mpp_get_media_title', 'wptexturize' );
add_filter( 'mpp_get_media_title', 'convert_chars' );
add_filter( 'mpp_get_media_title', 'trim' );
// for content.
add_filter( 'mpp_get_media_description', 'wp_kses_post' );
add_filter( 'mpp_get_media_description', 'wptexturize' );
add_filter( 'mpp_get_media_description', 'convert_smilies' );
add_filter( 'mpp_get_media_description', 'convert_chars' );
add_filter( 'mpp_get_media_description', 'wpautop' );
add_filter( 'mpp_get_media_description', 'make_clickable' );
