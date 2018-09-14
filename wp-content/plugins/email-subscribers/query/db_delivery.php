<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

class es_cls_delivery {
	public static function es_delivery_select( $sentguid = "", $offset = 0, $limit = 0 ) {

		global $wpdb;

		$arrRes = array();

		if( $sentguid != "" ) {
			$query = $wpdb->prepare( "SELECT *
										FROM {$wpdb->prefix}es_deliverreport
										WHERE es_deliver_sentguid = %s
										ORDER BY es_deliver_id DESC 
										LIMIT $offset, $limit", $sentguid );
		} else {
			$query = $wpdb->prepare( "SELECT *
										FROM {$wpdb->prefix}es_deliverreport
										WHERE 1 = %d", 1 );
		}
		$arrRes = $wpdb->get_results( $query, ARRAY_A );

		return $arrRes;

	}

	// Query to get count of total emails sent
	public static function es_delivery_count( $sentguid = "" ) {

		global $wpdb;

		$result = '0';

		if( $sentguid != "" ) {
			$query = $wpdb->prepare( "SELECT COUNT(*) AS count
										FROM {$wpdb->prefix}es_deliverreport
										WHERE es_deliver_sentguid = %s", array( $sentguid ) );
			$result = $wpdb->get_var( $query );
		}

		return $result;

	}

	// Query to get total viewed emails per report
	public static function es_delivery_viewed_count( $sentguid = "" ) {

		global $wpdb;

		$result = '0';

		if( $sentguid != "" ) {
			$query = $wpdb->prepare("SELECT COUNT(*) AS count
									FROM {$wpdb->prefix}es_deliverreport
									WHERE es_deliver_status = 'Viewed' AND es_deliver_sentguid = %s", array( $sentguid ) );
			$result = $wpdb->get_var( $query );
		}

		return $result;

	}

	public static function es_delivery_ins( $guid = "", $dbid = 0, $email = "", $mailsenttype = "" ) {

		global $wpdb;

		$return_id = 0;

		if( $mailsenttype == "Immediately" ) {
			$es_sent_status = "Sent";
			$current_date = date('Y-m-d G:i:s'); 
		} else {
			$es_sent_status = "In Queue";
			$current_date = "0000-00-00"; 
		}

		$query = $wpdb->prepare( "INSERT INTO {$wpdb->prefix}es_deliverreport
												( es_deliver_sentguid, es_deliver_emailid, es_deliver_emailmail, es_deliver_sentdate, es_deliver_status, es_deliver_sentstatus, es_deliver_senttype )
												VALUES ( %s, %s, %s, %s, %s, %s, %s )", array( $guid, $dbid, $email, $current_date, "Nodata", $es_sent_status, $mailsenttype ) );

		$wpdb->query( $query );
		$return_id = $wpdb->insert_id;

		return $return_id;
	}

	public static function es_delivery_ups( $id = 0 ) {

		global $wpdb;

		$current_date = date('Y-m-d G:i:s'); 

		if( is_numeric( $id ) ) {
			$query = $wpdb->prepare( "UPDATE {$wpdb->prefix}es_deliverreport
										SET es_deliver_status = %s, es_deliver_viewdate = %s
										WHERE es_deliver_id = %d
										LIMIT 1", array( "Viewed", $current_date, $id ) );
			$wpdb->query( $query );
		}

		return true;

	}

	public static function es_delivery_ups_cron( $id = 0 ) {

		global $wpdb;

		$current_date = date('Y-m-d G:i:s'); 

		if( is_numeric( $id ) ) {
			$query = $wpdb->prepare( "UPDATE {$wpdb->prefix}es_deliverreport
										SET es_deliver_sentstatus = %s, es_deliver_sentdate = %s
										WHERE es_deliver_id = %d
										LIMIT 1", array( "Sent", $current_date, $id ) );
			$wpdb->query( $query );
		}

		return true;

	}

	public static function es_delivery_cronmail_inqueue( $limit = 50, $sentguid ) {

		global $wpdb;

		$arrRes = array();

		$query = $wpdb->prepare( "SELECT *
									FROM {$wpdb->prefix}es_deliverreport
									WHERE es_deliver_senttype = %s AND es_deliver_sentstatus = %s AND es_deliver_sentguid = %s
									ORDER BY es_deliver_id
									LIMIT 0, $limit", array( "Cron", "In Queue", $sentguid ) );
		$arrRes = $wpdb->get_results( $query, ARRAY_A );
		if(count($arrRes) > 0){
			foreach ($arrRes as $res) {
				$ids[] = $res['es_deliver_id']; 
			}
			$id_str = implode(",", $ids);
		    $update_sql = $wpdb->prepare( "UPDATE {$wpdb->prefix}es_deliverreport 
										SET es_deliver_sentstatus = %s 
		 									WHERE es_deliver_id IN (%s)", "Sending", $id_str );
			$wpdb->query( $update_sql );
		}
		return $arrRes;

	}

	public static function es_delivery_cronmail_count( $sentguid ) {

		global $wpdb;

		$result = '0';

		if( $sentguid != "" ) {
			$query = $wpdb->prepare( "SELECT COUNT(*) AS count
										FROM {$wpdb->prefix}es_deliverreport
										WHERE es_deliver_sentguid = %s AND es_deliver_senttype = %s AND es_deliver_sentstatus = %s", array( $sentguid, "Cron", "In Queue" ) );
			$result = $wpdb->get_var( $query );
		}

		return $result;

	}
}