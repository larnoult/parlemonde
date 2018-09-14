<?php
/**
 * Single Audio Gallery Playlist View
 *
 */
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="mpp-item-playlist mpp-audio-playlist mpp-u-1-1">

	<?php do_action( 'mpp_before_media_playlist' ); ?>

	<?php
	$ids = mpp_get_all_media_ids();
	echo wp_playlist_shortcode( array( 'ids' => $ids ) );

	?>

	<?php do_action( 'mpp_after_media_playlist' ); ?>

</div>
