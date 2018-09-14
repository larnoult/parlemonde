<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

class es_cls_notification {
	// Query to view Post Notifications Dashboard and edit any Post Notification
	public static function es_notification_select( $id = 0 ) {

		global $wpdb;

		$arrRes = array();

		if( $id > 0 ) {
			$query = $wpdb->prepare( "SELECT *
										FROM {$wpdb->prefix}es_notification
										WHERE es_note_id = %d", $id );
			$arrRes = $wpdb->get_row( $query, ARRAY_A );
		} else {
			$query = $wpdb->prepare( "SELECT *
										FROM {$wpdb->prefix}es_notification
										WHERE 1 = %d", 1 );
			$arrRes = $wpdb->get_results( $query, ARRAY_A );
		}

		return $arrRes;

	}

	public static function es_notification_count( $id = 0 ) {

		global $wpdb;

		$result = '0';

		if( $id > 0 ) {
			$query = $wpdb->prepare( "SELECT COUNT(*) AS count
										FROM {$wpdb->prefix}es_notification
										WHERE es_note_id = %d", array( $id ) );
		} else {
			$query = $wpdb->prepare( "SELECT COUNT(*) AS count
										FROM {$wpdb->prefix}es_notification
										WHERE 1 = %d", 1 );
		}
		$result = $wpdb->get_var( $query );

		return $result;

	}

	public static function es_notification_delete( $id = 0 ) {

		global $wpdb;

		$es_delete_notification = $wpdb->prepare( "DELETE
													FROM {$wpdb->prefix}es_notification
													WHERE es_note_id = %d
													LIMIT 1", $id );
		$wpdb->query( $es_delete_notification );

		return true;

	}

	public static function es_notification_ins( $data = array(), $action = "insert" ) {

		global $wpdb;

		if( $action == "insert" ) {
			$query = $wpdb->prepare( "INSERT INTO {$wpdb->prefix}es_notification
										( es_note_cat, es_note_group, es_note_templ, es_note_status )
										VALUES( %s, %s, %s, %s )", array( $data["es_note_cat"], $data["es_note_group"], $data["es_note_templ"], $data["es_note_status"] ) );
		} elseif( $action == "update" ) {
			$query = $wpdb->prepare( "UPDATE {$wpdb->prefix}es_notification
										SET es_note_cat = %s, es_note_group = %s, es_note_templ = %d, es_note_status = %s
										WHERE es_note_id = %d
										LIMIT 1", array( $data["es_note_cat"], $data["es_note_group"], $data["es_note_templ"], $data["es_note_status"], $data["es_note_id"] ) );
		}
		$wpdb->query( $query );

		return true;

	}

	public static function es_notification_prepare( $post_id = 0 ) {

		global $wpdb;

		$arrNotification = array();

		if( $post_id > 0 ) {
			$post_type = get_post_type( $post_id );
			$sSql = "SELECT * FROM {$wpdb->prefix}es_notification WHERE (es_note_status = 'Enable' OR es_note_status = 'Cron') ";
			if( $post_type == "post" ) {
				$category = get_the_category( $post_id );
				$totcategory = count( $category );
				if ( $totcategory > 0 ) {
					for( $i=0; $i<$totcategory; $i++ ) {				
						if( $i == 0 ) {
							$sSql .= " and (";
						} else {
							$sSql .= " or";
						}
						$sSql .= " es_note_cat LIKE '%##" . addslashes(htmlspecialchars_decode($category[$i]->cat_name)). "##%'";	// alternative addslashes(htmlspecialchars_decode(text)) = mysqli_real_escape_string but not working all the time
						if( $i == ( $totcategory-1 ) ) {
							$sSql .= ")";
						}
					}
					$arrNotification = $wpdb->get_results( $sSql, ARRAY_A );
				}
			} else {
				$sSql .= " and es_note_cat LIKE '%##{T}" . $post_type . "{T}##%'";
				$arrNotification = $wpdb->get_results( $sSql, ARRAY_A );
			}
		}

		return $arrNotification;

	}

	public static function es_notification_subscribers( $arrNotification = array() ) {

		global $wpdb;

		$subscribers = array();
		$totnotification = count($arrNotification);

		if( $totnotification > 0 ) {
			$sSql = "SELECT * FROM {$wpdb->prefix}es_emaillist WHERE es_email_mail != ''";
			for( $i=0; $i<$totnotification; $i++ ) {
				if( $i == 0 ) {
					$sSql .= " and (";
				} else {
					$sSql .= " or";
				}
				$sSql .= " es_email_group = '" . $arrNotification[$i]['es_note_group']. "'";
				if( $i == ( $totnotification-1 ) ) {
					$sSql .= ")";
				}
			}
			$sSql .= " and (es_email_status = 'Confirmed' or es_email_status = 'Single Opt In')";
			$sSql .= " GROUP BY es_email_mail ORDER BY es_email_mail ASC";
			$subscribers = $wpdb->get_results( $sSql, ARRAY_A );
		}

		return $subscribers;

	}
}