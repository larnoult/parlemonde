<?php

/**
 * bbPress Digest Cron Event Functions
 *
 * @package bbPress Digest
 * @subpackage Cron Event Functions
 */

/* Exit if accessed directly */
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Send digest emails on schedule
 *
 * @since 1.0
 *
 * @uses BBP_Digest_Event Class that handles process
 */
function bbp_digest_do_event() {
	$bbp_digest_event = new BBP_Digest_Event();
	$bbp_digest_event->do_event();
}

/**
 * Send digest emails on schedule
 *
 * @since 2.0
 */
class BBP_Digest_Event {

	/**
	 * Curent unix time
	 *
	 * @var $current_time
	 * @since 2.0
	 * @access private
	 */
	private $current_time;

	/**
	 * Minutes passed since full hour
	 *
	 * @var $current_minute
	 * @since 2.0
	 * @access private
	 */
	private $current_minute;

	/**
	 * Seconds passed since full hour
	 *
	 * @var $seconds_late
	 * @since 2.0
	 * @access private
	 */
	private $seconds_late;

	/**
	 * Unix time 24 hours ago
	 *
	 * @var $yesterday_time
	 * @since 2.0
	 * @access private
	 */
	private $yesterday_time;

	/**
	 * Unix time 7 days ago
	 *
	 * @var $week_ago_time
	 * @since 2.0
	 * @access private
	 */
	private $week_ago_time;

	/**
	 * Numeric representation of the day of the week
	 *
	 * @var $current_day_of_week
	 * @since 2.0
	 * @access private
	 */
	private $current_day_of_week;

	/**
	 * Sets class properties.
	 *
	 * @since 2.0
	 * @access public
	 */
	public function __construct() {

		/* Set variables */

		/* Get current time */
		$this->current_time = current_time( 'timestamp' );

		/* Get minutes passed since full hour */
		$this->current_minute = date( 'i', $this->current_time );

		/* Count number of minutes passed since full hour, plus seconds */
		if ( $this->current_minute > 00 )
			$this->seconds_late = ( $this->current_minute * 60 ) + date( 's', $this->current_time );
		else
			$this->seconds_late = date( 's', $this->current_time );

		/* Get yesterday's time */
		$this->yesterday_time = $this->current_time - ( ( 24 * 3600 ) + $this->seconds_late );

		/* Get week ago time */
		$this->week_ago_time = $this->current_time - ( ( 7 * 24 * 3600 ) + $this->seconds_late );

		/* Get current day of the week */
		$this->current_day_of_week = date( 'w', $this->current_time );
	}

	/**
	 * Process sending of digest emails
	 *
	 * @since 2.0
	 * @access public
	 *
	 * @uses get_users() To get users that should receive digest
	 * @uses BBP_Digest_Event::get_topics() To get topics IDs
	 * @uses apply_filters() Calls 'bbp_digest_time_border' with the time
	 *                        border for digest email title
	 * @uses bbp_get_topic_title() To get title of the topic
	 * @uses bbp_get_topic_last_reply_url() To get URL of topic's last reply
	 * @uses get_user_meta() To get user's digest settings
	 * @uses bbp_get_topic_forum_id() To get ID of topic's forum
	 * @uses BBP_Digest_Event::mail() To send digest email
	 *
	 * @param string $period Period for which digests are sent
	 */
	public function do_event( $period = 'day' ) {
		/* Setup arguments for user query */
		$user_args = array(
			'meta_key'   => 'bbp_digest_time',
			'meta_value' => date( 'H', $this->current_time ), // Only users that should receive in this hour
		);

		/* Query users */
		$users = get_users( $user_args );

		/* Only proceed further if there are users */
		if ( $users ) {

			/* Set to false are there weekly subscribers */
			$have_for_weekly = false;

			/* Query topics */
			$topic_ids = $this->get_topics( $period );

			/* Only proceed further if there are topics */
			if ( $topic_ids ) {

				/* Setup texts based on period  */
				if ( 'week' == $period ) {
					/* Set subject of email */
					$subject = sprintf( __( 'Active topics for week ending %1$s', 'bbp-digest' ), date_i18n( _x( 'd. F Y.', 'week span email title date format', 'bbp-digest' ), $this->current_time ) );

					/* Set standard message intro */
					$message = __( "This topics have been active in the last 7 days:\n\n", "bbp-digest" );
				} else {
					/* Set subject of email based on current time */
					/**
					 * Filter bordering hour.
					 *
					 * Bordering hour is used to decide should title
					 * include span of one or two days.
					 *
					 * @since 1.0
					 *
					 * @param int $hour Bordering hour.
					 */
					if ( date( 'G', $this->current_time ) < absint( apply_filters( 'bbp_digest_time_border', 8 ) ) ) { // If before 08:00, use yesterday
						$subject = sprintf( __( 'Active topics for %1$s', 'bbp-digest' ), date_i18n( _x( 'd. F Y.', 'one day span email title date format', 'bbp-digest' ), $this->yesterday_time ) );
					} else { // Otherwise, use both
						$subject = sprintf( _x( 'Active topics for %1$s / %2$s', '1. Yesterday 2. Today', 'bbp-digest' ), date_i18n( _x( 'd. F Y.', 'two day span yesterday email title date format', 'bbp-digest' ), $this->yesterday_time ), date_i18n( _x( 'd. F Y.', 'one day span today email title date format', 'bbp-digest' ), $this->current_time ) );
					}

					/* Set standard message intro */
					$message = __( "This topics have been active in the last 24 hours:\n\n", "bbp-digest" );
				}

				/* Set list item placeholder; used because of new line (\n) */
				$item_placeholder = '
%1$s : %2$s. En voilà un extrait : %3$s
';

				/* Setup list of topics */
				$all_topics_list = '';

				/* Go through all topics */
				foreach ( $topic_ids as $topic_id ) {
					$all_topics_list .= sprintf( $item_placeholder, bbp_get_topic_title( $topic_id ), bbp_get_topic_last_reply_url( $topic_id ), bbp_get_topic_excerpt ( $topic_id ) );
				}

				/* Go through all users */
				foreach ( $users as $user ) {
					/* Get weekly subscription status */
					$user_day = get_user_meta( $user->ID, 'bbp_digest_day', true );

					/* Continue if user not from this period */
					if ( 'day' == $period ) {
						/* Is weekly subscriber? Pay attention to Sunday. */
						if ( strlen( $user_day ) > 0 ) {
							/* Set that there are subscribers for weekly */
							$have_for_weekly = true;
							/* Continue to the next */
							continue;
						}
					}

					/* Continue if user not from this day */
					if ( 'week' == $period && $user_day != $this->current_day_of_week )
						continue;

					/* If user folows only selected forums, loop all topics again */
					if ( $user_forums = get_user_meta( $user->ID, 'bbp_digest_forums', true ) ) {

						/* Get string name of forum array, used for reducing duplication */
						$topic_list = md5( serialize( $user_forums ) );

						/* Check if topic list is already created & send it, otherwise create it & send it */
						if ( isset( $$topic_list ) && $$topic_list ) {
							/* Send notification email */
							$this->mail( $user->user_email, $subject, $message . $$topic_list, $user, $topic_ids, $period );
						} else {
							/* Setup list of topics */
							$$topic_list = '';
							$send_email = false;

							/* Go through all topics */
							foreach ( $topic_ids as $topic_id ) {
								/* Is topic from forum user selected? */
								if ( in_array( bbp_get_topic_forum_id( $topic_id ), $user_forums ) ) {
									$$topic_list .= sprintf( $item_placeholder, bbp_get_topic_title( $topic_id ), bbp_get_topic_last_reply_url( $topic_id ), bbp_get_topic_excerpt ( $topic_id ) );
									$send_email = true;
								}
							}

							/* Send notification email */
							if ( $send_email ) {
								$this->mail( $user->user_email, $subject, $message . $$topic_list, $user, $topic_ids, $period );
							}
						}
					/* Otherwise, send all topics */
					} else {
						/* Send notification email */
						$this->mail( $user->user_email, $subject, $message . $all_topics_list, $user, $topic_ids, $period );
					}
				}
			}

			/* If this was for day, and there are for week, process them too */
			if ( 'day' == $period && $have_for_weekly )
				$this->do_event( 'week' );
		}
	}

	/**
	 * Get active topics for period.
	 *
	 * @since 2.0
	 * @access private
	 *
	 * @uses bbp_get_topic_post_type() To get topic post type name
	 * @uses bbp_get_public_status_id() To get post public status name
	 * @uses bbp_get_closed_status_id() To get post closed status name
	 * @uses get_posts() To get topics IDs
	 *
	 * @param string $period Period for which topics are queried
	 * @return array $topics List of topics IDs
	 */
	private function get_topics( $period = 'day' ) {
		/* Setup topic IDs array */
		$topic_ids = array();

		/* Setup time that we compare to */
		if ( 'week' == $period )
			$time = $this->week_ago_time;
		else
			$time = $this->yesterday_time;

		/* Setup arguments for topic query */
		$topic_args = array(
			'post_type'      => bbp_get_topic_post_type(), // Only bbPress topic type
			'posts_per_page' => -1, // All topics
			'meta_key'       => '_bbp_last_active_time',
			'fields'         => 'ids',
			'orderby'        => 'meta_value', // Order by _bbp_last_active_time (ie. from newest to oldest)
			'post_status'    => join( ',', array( bbp_get_public_status_id(), bbp_get_closed_status_id() ) ), // All public statuses
			'meta_query'     => array(
				array(
					'key' => '_bbp_last_active_time',
					'value' => date( 'Y-m-d H:i:s', $time ), // Only active for period we are quering, last 24 hours or last 7 days, plus passed time since full hour
					'compare' => '>',
					'type' => 'DATETIME',
				)
			)
		);

		/* Query topics */
		$topics = get_posts( $topic_args );

		/* Only proceed further if there are topics */
		if ( $topics )
			return $topics;
		else
			return;
	}

	/**
	 * Send email.
	 *
	 * @since 2.1
	 * @access private
	 *
	 * @uses wp_mail() To send email.
	 *
	 * @param string  $email_address Adress of the receiver.
	 * @param string  $subject       Subject of the email.
	 * @param string  $message       Content of the email.
	 * @param WP_User $user          WP_User object of reciver.
	 * @param array   $topic_ids     IDs of topics that were active.
	 * @param string  $period        Period for which digest is sent.
	 */
	private function mail( $email_address, $subject, $message, $user, $topic_ids, $period ) {
		/**
		 * Fires before email is sent.
		 *
		 * @since 2.1
		 *
		 * @param string           $email_address Adress of the receiver.
		 * @param string           $subject       Subject of the email.
		 * @param string           $message       Content of the email.
		 * @param WP_User          $user          WP_User object of reciver.
		 * @param array            $topic_ids     IDs of topics that were active.
		 * @param string           $period        Period for which digest is sent.
		 * @param BBP_Digest_Event $this          BBP_Digest_Event instance, passed by reference.
		 */
		do_action( 'bbp_digest_before_mail', $email_address, $subject, $message, $user, $topic_ids, $period, $this );

		wp_mail( $email_address, $subject, $message );

		/**
		 * Fires after email is sent.
		 *
		 * @since 2.1
		 *
		 * @param string           $email_address Adress of the receiver.
		 * @param string           $subject       Subject of the email.
		 * @param string           $message       Content of the email.
		 * @param WP_User          $user          WP_User object of reciver.
		 * @param array            $topic_ids     IDs of topics that were active.
		 * @param string           $period        Period for which digest is sent.
		 * @param BBP_Digest_Event $this          BBP_Digest_Event instance, passed by reference.
		 */
		do_action( 'bbp_digest_after_mail', $email_address, $subject, $message, $user, $topic_ids, $period, $this );
	}
}