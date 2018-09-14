<?php

/**
 * bbPress Digest bbPress Profile Functions
 *
 * @package bbPress Digest
 * @subpackage bbPress Profile Functions
 */

/* Exit if accessed directly */
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Display settings on built-in profile page
 *
 * @param object $user Viewed user's data
 *
 * @since 1.0
 *
 * @uses get_user_meta() To get user's digest settings
 * @uses wp_enqueue_script() To load jQuery file
 * @uses bbp_digest_is_it_active() To check if feature is enabled
 * @uses checked() To display the checked attribute
 * @uses selected() To display the selected attribute
 * @uses esc_attr() To escape element's attribute value
 * @uses wp_locale::get_weekday To display days name
 * @uses date_i18n() To get localized time & date
 * @uses bbp_digest_get_dropdown() To get forums dropdown
 */
function bbp_digest_display_profile_fields( $user ) {
	/* Get user's settings */
	$bbp_digest_time   = get_user_meta( $user->ID, 'bbp_digest_time',   true );
	$bbp_digest_day    = get_user_meta( $user->ID, 'bbp_digest_day',    true );
	$bbp_digest_forums = get_user_meta( $user->ID, 'bbp_digest_forums', true );

	/* Workaround when day is Sunday */
	if ( '0' === $bbp_digest_day )
		$bbp_digest_day = 'Sunday fix';

	/* Load jQuery */
	wp_enqueue_script( 'jquery' );
	?>
	<h3><?php _e( 'bbPress Digest Emails', 'bbp-digest' ) ?></h3>

	<table class="form-table">

		<tr>
			<th scope="row"><?php _ex( 'Digest emails', 'settings field label', 'bbp-digest' ) ?></th>

			<td id="bbp-digest-subscription-cell">
				<label for="bbp-digest-subscription"><input name="bbp-digest-subscription" type="checkbox" id="bbp-digest-subscription" value="1" <?php checked( ! $bbp_digest_time, false ); ?> /> <?php _ex( 'Yes', 'checkbox label', 'bbp-digest' ) ?></label><br />
				<span class="description"><?php _e( 'Check if you want to receive daily digest with active forum topics for that day.', 'bbp-digest' ) ?></span>
			</td>
		</tr>

		<?php
		if ( bbp_digest_is_it_active( '_bbp_digest_enable_weekly' ) ) :
		?>
		<tr id="bbp-digest-day-row">
			<th scope="row"><?php _e( 'Daily or weekly', 'bbp-digest' ) ?></th>

			<td>
				<div id="bbp-digest-when-selection">
				<label for="bbp-digest-when-daily"><input name="bbp-digest-when" id="bbp-digest-when-daily" type="radio" value="daily" <?php checked( ! $bbp_digest_day, true ); ?> /><?php _ex( 'Daily', 'radio button label', 'bbp-digest' ) ?> </label>
				<label for="bbp-digest-when-weekly"><input name="bbp-digest-when" id="bbp-digest-when-weekly" type="radio" value="weekly" <?php checked( ! $bbp_digest_day, false ); ?> /><?php _ex( 'Weekly', 'radio button label', 'bbp-digest' ) ?> </label><br />
				<span class="description"><?php _e( 'Choose should you receive digest once daily or once weekly.', 'bbp-digest' ) ?></span><br />
				</div>

				<div id="bbp-digest-day-list">
				<select name="bbp-digest-day" id="bbp-digest-day">
				<?php
				/* Workaround when day is Sunday */
				if ( 'Sunday fix' == $bbp_digest_day )
					$bbp_digest_day = 0;

				global $wp_locale;
				for ( $day_index = 0; $day_index <= 6; $day_index++ ) :
					?>
					<option value="<?php echo esc_attr( $day_index ); ?>" <?php selected( $bbp_digest_day, $day_index ); ?>><?php echo $wp_locale->get_weekday( $day_index ); ?></option>
					<?php
				endfor;
				?>
				</select>
				<span class="description"><?php _e( 'Choose on which day of a week you want to receive a digest.', 'bbp-digest' ) ?></span>
				</div>
			</td>
		</tr>
		<?php
		endif;
		?>

		<tr id="bbp-digest-time-row">
			<th scope="row"><?php _e( 'Digest time', 'bbp-digest' ) ?></th>

			<td id="bbp-digest-time-cell">
				<label for="bbp-digest-time"><?php _e( 'Digests should be sent at this time:', 'bbp-digest' ) ?> </label>
				<select name="bbp-digest-time" id="bbp-digest-time">
					<?php for ( $i = 0; $i <= 23; $i++ ) : ?>
						<?php if ( $i < 10 ) $i = '0' . $i ?>
						<option value="<?php echo $i?>" <?php selected( $i, $bbp_digest_time ); ?>><?php echo $i; ?></option>
					<?php endfor; ?>
				</select><br />
				<span class="description"><?php printf( __( 'Select the hour of the day when you want to receive digest emails. Current time is <code>%1$s</code>.', 'bbp-digest' ), date_i18n( _x( 'Y-m-d G:i:s', 'current time date format', 'bbp-digest' ) ) ); ?></span>
			</td>
		</tr>

		<tr id="bbp-digest-pool-row">
			<th scope="row"><?php _ex( 'Forums', 'settings field label', 'bbp-digest' ) ?></th>

			<td>
				<div id="bbp-digest-pool-selection">
				<label for="bbp-digest-pool-all"><input name="bbp-digest-pool" id="bbp-digest-pool-all" type="radio" value="all" <?php checked( ! $bbp_digest_forums, true ); ?> /><?php _ex( 'All', 'radio button label', 'bbp-digest' ) ?> </label>
				<label for="bbp-digest-pool-selected"><input name="bbp-digest-pool" id="bbp-digest-pool-selected" type="radio" value="selected" <?php checked( ! $bbp_digest_forums, false ); ?> /><?php _e( 'Only forums I choose', 'bbp-digest' ) ?> </label><br />
				<span class="description"><?php _e( 'Choose should digest include topics from all forums or only from selected forums.', 'bbp-digest' ) ?></span><br />
				</div>

				<div id="bbp-digest-forum-list">
				<?php
				echo bbp_digest_get_dropdown( array(
					'selected_forums' => (array) $bbp_digest_forums,
					'disable_categories' => false
				) );
				?>
				<span class="description"><?php _e( 'Choose forums which you want to be included in a digest.', 'bbp-digest' ) ?></span>
				</div>
			</td>
		</tr>
	</table>

	<script type="text/javascript">
		jQuery(document).ready(function($) {
			/* If not subscribed, hide time dropdown, period & forum selections, & disable inputs */
			if ( false == $('input#bbp-digest-subscription').is(':checked') ) {
				$('#bbp-digest-day-row').hide();
				$('#bbp-digest-time-row').hide();
				$('#bbp-digest-pool-row').hide();

				$('#bbp-digest-time').attr("disabled",true);
				$('#bbp-digest-day-list #bbp-digest-day').attr("disabled",true);
				$('#bbp-digest-forum-list input').attr("disabled",true);
			}

			/* On subscription state change, show/hide dropdown & period & forum selection, and enable/disable inputs */
			$('input#bbp-digest-subscription').click(function() {
				if ( $(this).is(':checked') ) {
					$('#bbp-digest-day-row').show();
					$('#bbp-digest-time-row').show();
					$('#bbp-digest-pool-row').show();

					$('#bbp-digest-time').attr("disabled",false);
					if ( $('#bbp-digest-pool-selected').is(':checked') ) {
						$('#bbp-digest-forum-list input').attr("disabled",false);
					}
					if ( $('#bbp-digest-when-weekly').is(':checked') ) {
						$('#bbp-digest-day-list #bbp-digest-day').attr("disabled",false);
					}
				} else {
					$('#bbp-digest-day-row').hide();
					$('#bbp-digest-time-row').hide();
					$('#bbp-digest-pool-row').hide();

					$('#bbp-digest-time').attr("disabled",true);
					$('#bbp-digest-day-list #bbp-digest-day').attr("disabled",true);
					$('#bbp-digest-forum-list input').attr("disabled",true);
				}
			});

			/* If subscribed to daily, hide day list & disable it */
			if ( $('input#bbp-digest-when-daily').is(':checked') ) {
				$('#bbp-digest-day-list').hide();
				$('#bbp-digest-day-list #bbp-digest-day').attr("disabled",true);
			}

			/* On subscription when state change, show/hide day list, and enable/disable inputs */
			$('#bbp-digest-when-selection input:radio').click(function() {
				/* Get id of selected option */
				var currentId = $(this).attr('id');

				if ( 'bbp-digest-when-weekly' == currentId ) {
					$('#bbp-digest-day-list').show();
					$('#bbp-digest-day-list #bbp-digest-day').attr("disabled",false);
				} else {
					$('#bbp-digest-day-list').hide();
					$('#bbp-digest-day-list #bbp-digest-day').attr("disabled",true);
				}
			});

			/* If subscribed to all, hide forum list & disable it */
			if ( $('input#bbp-digest-pool-all').is(':checked') ) {
				$('#bbp-digest-forum-list').hide();
				$('#bbp-digest-forum-list input').attr("disabled",true);
			}

			/* On subscription pool state change, show/hide forum list, and enable/disable inputs */
			$('#bbp-digest-pool-selection input:radio').click(function() {
				/* Get id of selected option */
				var currentId = $(this).attr('id');

				if ( 'bbp-digest-pool-selected' == currentId ) {
					$('#bbp-digest-forum-list').show();
					$('#bbp-digest-forum-list input').attr("disabled",false);
				} else {
					$('#bbp-digest-forum-list').hide();
					$('#bbp-digest-forum-list input').attr("disabled",true);
				}
			});
		});
	</script>
	<?php
}