<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class es_cls_dbquery {
	// Query to filter records on Subscribers Dashboard and get subscribers information for sending confirmation email and welcome email to subscribers
	public static function es_view_subscriber_search( $search = "", $id = 0 ) {

		global $wpdb;

		$arrRes = array();

		$sSql = "SELECT * FROM {$wpdb->prefix}es_emaillist WHERE es_email_mail != ''";
		if( $search != "" && $search != "ALL" ) {
			$letter = explode( ',', $search );
			$length = count( $letter );
			for( $i = 0; $i < $length; $i++ ) {
				if( $i == 0 ) {
					$sSql .= " AND";
				} else {
					$sSql .= " OR";
				}
				$sSql .= " es_email_mail LIKE '" . $letter[$i]. "%'";
			}
		}
		if($id > 0) {
			$sSql .= " AND es_email_id=".$id;

		}
		$sSql .= " ORDER BY es_email_id ASC";
		$arrRes = $wpdb->get_results( $sSql, ARRAY_A );

		return $arrRes;
	}

	// Query to re-send confirmation emails in bulk from Subscribers Dashboard
	public static function es_view_subscriber_bulk( $idlist = "" ) {

		global $wpdb;

		$arrRes = array();

		if( $idlist != "" ) {
			$sSql = $wpdb->prepare( "SELECT *
										FROM {$wpdb->prefix}es_emaillist
										WHERE es_email_mail != %s AND es_email_id IN ( ". $idlist ." )", '' );
		} else {
			$sSql = $wpdb->prepare( "SELECT *
										FROM {$wpdb->prefix}es_emaillist
										WHERE es_email_mail != %s", '' );
		}
		$arrRes = $wpdb->get_results( $sSql, ARRAY_A );

		return $arrRes;
	}

	// Query to fetch subscribers data on Subscribers Dashboard
	public static function es_view_subscribers_details( $id = 0, $search_sts = "", $offset = 0, $limit = 0, $search_group = "" ) {

		global $wpdb;

		$view_subscribers_details = array();

		$sSql = "SELECT * FROM {$wpdb->prefix}es_emaillist WHERE es_email_mail != ''";
		if( $search_sts != "" ) {
			$sSql .= " AND es_email_status='".$search_sts."'";
		}

		if( $search_group != "" && $search_group != "ALL" ) {
			$sSql .= ' AND es_email_group="'.$search_group.'"';
		}

		if( $id > 0 ) {
			$sSql .= " AND es_email_id=".$id;

		}
		$sSql .= " ORDER BY es_email_id DESC";
		$sSql .= " LIMIT $offset, $limit";
		$view_subscribers_details = $wpdb->get_results( $sSql, ARRAY_A );

		return $view_subscribers_details;
	}


	public static function es_view_subscriber_delete( $id = 0 ) {

		global $wpdb;

		$es_delete_subscriber = $wpdb->prepare( "DELETE FROM {$wpdb->prefix}es_emaillist WHERE es_email_id = %d LIMIT 1", $id );
		$wpdb->query( $es_delete_subscriber );

		return true;
	}

	public static function es_view_subscriber_ins($data = array(), $action = "insert") {

		global $wpdb;

		// Security
		if ( array_key_exists( 'es_nonce', $data ) ) {
			if ( empty ( $data['es_nonce'] ) || ! wp_verify_nonce( $data['es_nonce'], 'es-subscribe' ) ) {
				return "invalid";
			}
		} elseif ( array_key_exists( 'es_af_nonce', $data ) ) {
			if ( empty ( $data['es_af_nonce'] ) || ! wp_verify_nonce( $data['es_af_nonce'], 'es_af_form_subscribers' ) ) {
				return "invalid";
			}
		} else {
			return "invalid";
		}

		if ( !filter_var( $data["es_email_mail"], FILTER_VALIDATE_EMAIL ) ) {
			return "invalid";
		}

		$data = apply_filters('es_validate_subscribers_email', $data);

		if ( $data["es_email_mail"] === 'invalid' ) {
			return "invalid";
		}

		$result = 0;
		$data["es_email_name"] = sanitize_text_field(esc_attr($data["es_email_name"]));
		$data["es_email_status"] = sanitize_text_field(esc_attr($data["es_email_status"]));
		$data["es_email_group"] = sanitize_text_field(esc_attr($data["es_email_group"]));
		$data["es_email_mail"] = sanitize_email(esc_attr($data["es_email_mail"]));

		// santize_email sometimes discards invalid emails. Hence returning 'invalid' for the same.
		if ( empty( $data["es_email_mail"] ) ) {
			return "invalid";
		} else {
			$CurrentDate = date('Y-m-d G:i:s');
			if( $action == "insert" ) {
				$sSql = "SELECT * FROM `".$wpdb->prefix."es_emaillist` where es_email_mail='".$data["es_email_mail"]."' and es_email_group='".trim($data["es_email_group"])."'";
				$result = $wpdb->get_var($sSql);
				if ( $result > 0 ) {
					return "ext";
				} else {
					$data['guid'] = es_cls_common::es_generate_guid(60);
					$sql = $wpdb->prepare( "INSERT INTO {$wpdb->prefix}es_emaillist
											(es_email_name, es_email_mail, es_email_status, es_email_created, es_email_viewcount, es_email_group, es_email_guid) VALUES(%s, %s, %s, %s, %d, %s, %s)", 
											array( trim($data["es_email_name"]), trim($data["es_email_mail"]), trim($data["es_email_status"]), $CurrentDate, 0, trim($data["es_email_group"]), $data['guid'] ) );
					$sql = apply_filters( 'es_insert_subscribers_sql', $sql, $data );
					$wpdb->query($sql);

					/* Added from ES v3.1.5 - If subscribing via Rainmaker
					 * if double opt-in, send confirmation email to subscriber
					 * if single opt-in, send welcome email to subscriber
					 */
					$active_plugins = (array) get_option('active_plugins', array());
					if (is_multisite()) {
						$active_plugins = array_merge($active_plugins, get_site_option('active_sitewide_plugins', array()));
					}

					if (( in_array('icegram-rainmaker/icegram-rainmaker.php', $active_plugins) || array_key_exists('icegram-rainmaker/icegram-rainmaker.php', $active_plugins) )) {			// To Do- Handle via actions

						$es_c_optinoption = get_option( 'ig_es_optintype' );
						$subscribers = array();
						$subscribers = self::es_view_subscriber_one($data["es_email_mail"],$data["es_email_group"]);

						if( did_action( 'rainmaker_post_lead' ) >= 1 ) {
							if ( (!empty($es_c_optinoption)) && ($es_c_optinoption == 'Double Opt In') ) {
								es_cls_sendmail::es_sendmail("optin", $template = 0, $subscribers, "optin", 0);
							} else if ( (!empty($es_c_optinoption)) && ($es_c_optinoption == 'Single Opt In' ) ) {
								es_cls_sendmail::es_sendmail("welcome", $template = 0, $subscribers, "welcome", 0);
							}
						}
					}
					return "sus";
				}
			} elseif( $action == "update" ) {
				$sSql = $wpdb->prepare( "SELECT *
											FROM {$wpdb->prefix}es_emaillist
											WHERE es_email_mail = %s AND es_email_group = %s AND es_email_id != %d", $data["es_email_mail"], trim($data["es_email_group"]), $data["es_email_id"] );
				$result = $wpdb->get_var($sSql);
				if ( $result > 0 ) {
					return "ext";
				} else {
					$sSql = $wpdb->prepare( "UPDATE {$wpdb->prefix}es_emaillist
												SET es_email_name = %s, es_email_mail = %s, es_email_status = %s, es_email_group = %s 
												WHERE es_email_id = %d LIMIT 1", array( $data["es_email_name"], $data["es_email_mail"], $data["es_email_status"], $data["es_email_group"], $data["es_email_id"] ) );
					$wpdb->query($sSql);
					return "sus";
				}
			}
		}

	}

	public static function es_view_subscriber_group() {

		global $wpdb;

		$arrRes = array();

		$sSql = $wpdb->prepare( "SELECT DISTINCT(es_email_group)
									FROM {$wpdb->prefix}es_emaillist
									WHERE 1=%d", 1 ) ;
		$arrRes = $wpdb->get_results( $sSql, ARRAY_A );

		return $arrRes;
	}

	public static function es_view_subscriber_one( $mail = "", $group = "" ) {

		global $wpdb;

		$arrRes = array();

		$sSql = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}es_emaillist
									WHERE es_email_mail = %s AND es_email_group = %s", $mail, $group );
		$arrRes = $wpdb->get_results( $sSql, ARRAY_A );

		return $arrRes;
	}

	// Function to Bulk Update Subscribers Status
	public static function es_view_subscriber_upd_status( $status = "", $idlist = "" ) {

		global $wpdb;

		$update_subscribers_status = $wpdb->prepare( "UPDATE {$wpdb->prefix}es_emaillist
														SET es_email_status = %s
														WHERE es_email_id IN ( $idlist )", $status );
		$wpdb->query( $update_subscribers_status );

		return "sus";
	}

	// Function to Bulk Update Subscribers Group
	public static function es_view_subscriber_upd_group( $group = "", $idlist = "" ) {

		global $wpdb;

		$update_subscribers_group = $wpdb->prepare( "UPDATE {$wpdb->prefix}es_emaillist
														SET es_email_group = %s
														WHERE es_email_id IN ( $idlist )", $group );
		$wpdb->query( $update_subscribers_group );

		return "sus";
	}

	public static function es_view_subscriber_job( $status = "", $id = 0, $guid = "", $email = "" ) {

		global $wpdb;

		$get_count_of_subscriber = $wpdb->prepare( "SELECT COUNT(*) AS count
									FROM {$wpdb->prefix}es_emaillist
									WHERE es_email_id = %d AND es_email_mail = %s AND es_email_guid = %s
									LIMIT 1", array($id, $email, $guid) );
		$count_of_subscribers = $wpdb->get_var( $get_count_of_subscriber );

		if ( $count_of_subscribers > 0 ) {

			$update_subscriber_status = $wpdb->prepare( "UPDATE {$wpdb->prefix}es_emaillist
															SET es_email_status = %s
															WHERE es_email_mail = %s
															LIMIT 10", array( $status, $email ) );
			$wpdb->query( $update_subscriber_status );
			return true;
		} else {
			return false;
		}
	}

	public static function es_view_subscriber_jobstatus( $status = "", $id = 0, $guid = "", $email = "" ) {

		global $wpdb;

		$query = $wpdb->prepare( "SELECT COUNT(*) AS count
									FROM {$wpdb->prefix}es_emaillist
									WHERE es_email_id = %d AND es_email_mail = %s AND es_email_status = %s AND es_email_guid = %s
									LIMIT 1", array( $id, $email, $status, $guid ) );
		$result = $wpdb->get_var( $query );

		if ( $result > 0 ) {
			return true;
		} else {
			return false;
		}
	}

	public static function es_view_subscriber_widget( $data = array() ) {

		global $wpdb;

		$es_result = array();
		$currentdate = date('Y-m-d G:i:s');

		$check_if_subscriber_exists = $wpdb->prepare( "SELECT * 
														FROM {$wpdb->prefix}es_emaillist
														WHERE es_email_mail = %s AND es_email_group = %s", $data['es_email_mail'], trim($data['es_email_group']) );
		$es_result = $wpdb->get_results( $check_if_subscriber_exists, ARRAY_A );

		if ( !empty( $es_result ) && count( $es_result ) > 0 ) {
			if( $es_result[0]['es_email_status'] == "Confirmed" || $es_result[0]['es_email_status'] == "Single Opt In" ) {
				return "ext";
			} else {
				$action = "";
				$form['es_email_name'] 		= sanitize_text_field(esc_attr($data["es_email_name"]));
				$form['es_email_mail'] 		= sanitize_email(esc_attr($data["es_email_mail"]));
				$form['es_email_group'] 	= sanitize_text_field(esc_attr($data["es_email_group"]));
				$form['es_email_status'] 	= sanitize_text_field(esc_attr($data["es_email_status"]));
				$form['es_email_id'] 		= $es_result[0]["es_email_id"];

				if ( array_key_exists( 'es_nonce', $data ) ) {
					$form['es_nonce'] = $data['es_nonce'];
				} elseif ( array_key_exists( 'es_af_nonce', $data ) ) {
					$form['es_af_nonce'] = $data['es_af_nonce'];
				}

				$action = es_cls_dbquery::es_view_subscriber_ins($form, $action = "update");
				return $action;
			}
		} else {
			$action = es_cls_dbquery::es_view_subscriber_ins($data, $action = "insert");
			return $action;
		}
	}

	// Query to fetch count of subscribers from a particular group
	public static function es_subscriber_count_in_group( $groups = "" ) {

		global $wpdb;

		$count_of_subscribers_in_a_group = $wpdb->prepare( "SELECT COUNT(*) AS count
															FROM {$wpdb->prefix}es_emaillist
															WHERE es_email_group = %s AND ( es_email_status = 'Confirmed' OR es_email_status = 'Single Opt In' )", $groups );
		$total_subscribers = $wpdb->get_var( $count_of_subscribers_in_a_group );

		return $total_subscribers;

	}

	// Query to fetch all subscribers data from a particular group
	public static function es_subscribers_data_in_group( $group = "" ) {

		global $wpdb;

		$subscribers_in_group = array();

		$query = $wpdb->prepare( "SELECT *
									FROM  {$wpdb->prefix}es_emaillist
									WHERE es_email_group = %s AND ( es_email_status = 'Confirmed' OR es_email_status = 'Single Opt In' )", $group );
		$subscribers_in_group = $wpdb->get_results( $query, ARRAY_A );

		return $subscribers_in_group;

	}

	// Query to fetch subscribers count (all status)
	public static function es_view_subscriber_count( $id = 0 ) {

		global $wpdb;

		$result = '0';

		if( $id > 0 ) {
			$query = $wpdb->prepare( "SELECT COUNT(*) AS count
										FROM {$wpdb->prefix}es_emaillist
										WHERE es_email_id = %d", array( $id ) );
		} else {
			$query = $wpdb->prepare( "SELECT COUNT(*) AS count
										FROM {$wpdb->prefix}es_emaillist
										WHERE 1 = %d", 1 );
		}

		$result = $wpdb->get_var( $query );

		return $result;

	}

	// Query to fetch active subscribers (status = Confirmed / Single Opt In)
	public static function es_active_subscribers() {

		global $wpdb;

		$active_subscribers_count = '0';

		$query = $wpdb->prepare( "SELECT COUNT(*) AS count
									FROM {$wpdb->prefix}es_emaillist
									WHERE 1 = %d AND es_email_status IN ( 'Confirmed', 'Single Opt In' )", 1 );
		$active_subscribers_count = $wpdb->get_var( $query );

		return $active_subscribers_count;

	}

	// Query to fetch inactive subscribers (status = Unconfirmed / Unsubscribed)
	public static function es_inactive_subscribers() {

		global $wpdb;

		$inactive_subscribers_count = '0';

		$query = $wpdb->prepare( "SELECT COUNT(*) AS count
									FROM {$wpdb->prefix}es_emaillist
									WHERE 1 = %d AND es_email_status IN ( 'Unconfirmed', 'Unsubscribed' )", 1 );
		$inactive_subscribers_count = $wpdb->get_var( $query );

		return $inactive_subscribers_count;

	}

}