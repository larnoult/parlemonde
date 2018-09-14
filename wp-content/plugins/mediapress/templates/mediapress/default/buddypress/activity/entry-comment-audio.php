<?php
// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<?php

$mppq = new MPP_Cached_Media_Query( array( 'in' => (array) mpp_activity_get_media_id( $activity_id ) ) );
?>
<?php if ( $mppq->have_media() ) : ?>

	<?php while ( $mppq->have_media() ) : $mppq->the_media(); ?>

		<?php if ( mpp_user_can_view_media( mpp_get_media_id() ) ) : ?>

			<div class="<?php mpp_media_class( 'mpp-activity-comment-media-entry mpp-activity-comment-media-entry-audio' ); ?>" id="mpp-activity-comment-media-entry-<?php mpp_media_id(); ?>">

				<div class="mpp-activity-comment-media-content mpp-activity-comment-media-audio-content mpp-activity-comment-media-audio-player">
					<?php mpp_media_content(); ?>
				</div>

				<script type='text/javascript'>
					mpp_mejs_activate(<?php echo $activity_id;?>);
				</script>
			</div>

		<?php else : ?>

			<div class="mpp-notice mpp-gallery-prohibited">
				<p><?php printf( __( 'The privacy policy does not allow you to view this.', 'mediapress' ) ); ?></p>
			</div>

		<?php endif; ?>

	<?php endwhile; ?>

<?php else : ?>

	<div class="mpp-notice mpp-gallery-prohibited">
		<p><?php printf( __( 'The privacy policy does not allow you to view this.', 'mediapress' ) ); ?></p>
	</div>

<?php endif; ?>
<?php mpp_reset_media_data(); ?>