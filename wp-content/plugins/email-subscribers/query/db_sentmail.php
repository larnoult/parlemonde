<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

class es_cls_sentmail {
	public static function es_sentmail_select( $id = 0, $offset = 0, $limit = 0 ) {

		global $wpdb;

		$arrRes = array();

		if( $id > 0 ) {
			$query = $wpdb->prepare( "SELECT *
										FROM {$wpdb->prefix}es_sentdetails
										WHERE es_sent_id = %d", $id );
			$arrRes = $wpdb->get_row( $query, ARRAY_A );
		} else {
			$query = $wpdb->prepare( "SELECT *
										FROM {$wpdb->prefix}es_sentdetails
										WHERE 1 = %d
										ORDER BY es_sent_id DESC
										LIMIT %d, %d", 1, $offset, $limit );
			$arrRes = $wpdb->get_results( $query, ARRAY_A );
		}

		return $arrRes;

	}

	// Query to view records on Reports Dashboard and preview individual report from Reports Dashboard
	public static function es_sentmail_count( $id = 0 ) {

		global $wpdb;

		$result = '0';

		if( $id > 0 ) {
			$query = $wpdb->prepare( "SELECT COUNT(*) AS count
										FROM {$wpdb->prefix}es_sentdetails
										WHERE es_sent_id = %d", array($id) );
		} else {
			$query = $wpdb->prepare( "SELECT COUNT(*) AS count
										FROM {$wpdb->prefix}es_sentdetails
										WHERE 1 = %d", 1 );
		}
		$result = $wpdb->get_var( $query );

		return $result;

	}

	// Query to delete records from Reports Dashboard from two tables: es_deliverreport & es_sentdetails
	public static function es_sentmail_delete( $id = 0 ) {

		global $wpdb;

		$Sentdetails = array();
		$Sentdetails = es_cls_sentmail::es_sentmail_select($id, 0, 1);

		if( count( $Sentdetails ) > 0 ) {
			$es_deliver_sentguid = $Sentdetails['es_sent_guid'];	
			if( $es_deliver_sentguid != "" ) {
				$query = $wpdb->prepare( "DELETE
											FROM {$wpdb->prefix}es_deliverreport
											WHERE es_deliver_sentguid = %s", $es_deliver_sentguid );
				$wpdb->query( $query );
			}

			$query = $wpdb->prepare( "DELETE
										FROM {$wpdb->prefix}es_sentdetails
										WHERE es_sent_id = %d
										LIMIT 1", $id );
			$wpdb->query( $query );
		}

		return true;

	}

	// Query to insert sent emails (immediately) records in table: es_sentdetails - 1
	public static function es_sentmail_ins( $guid = "", $qstring = 0, $source = "", $startdt = "", $enddt = "", $count = "", $preview = "", $mailsenttype = "" ) {

		global $wpdb;

		$returnid = 0;
		$currentdate = date('Y-m-d G:i:s'); 
		
		if( $mailsenttype == "Immediately" ) {
			$es_sent_status = "Sent";
		} else {
			$es_sent_status = "In Queue";
		}

		$query = $wpdb->prepare( "INSERT INTO {$wpdb->prefix}es_sentdetails
									(es_sent_guid, es_sent_qstring, es_sent_source, es_sent_starttime, es_sent_endtime, es_sent_count, es_sent_preview, es_sent_status, es_sent_type) 
									 VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)", array( $guid, $qstring, $source, $startdt, $enddt, $count, $preview, $es_sent_status, $mailsenttype ) );

		$wpdb->query( $query );

		return true;

	}

	// Query to insert sent emails (immediately) records in table: es_sentdetails - 2
	public static function es_sentmail_ups( $guid = "", $sentsubject = "" ) {

		global $wpdb;

		$returnid = 0;
		$currentdate = date('Y-m-d G:i:s');

		$query = $wpdb->prepare( "UPDATE {$wpdb->prefix}es_sentdetails
									SET es_sent_endtime = %s, es_sent_subject = %s
									WHERE es_sent_guid = %s
									LIMIT 1", array( $currentdate, $sentsubject, $guid ) );
		$wpdb->query( $query );

		return true;
	}

	public static function es_sentmail_cronmail_inqueue() {

		global $wpdb;

		$arrRes = array();

		$cron = 'Cron';
		$in_queue = 'In Queue';

		$query = $wpdb->prepare( "SELECT *
									FROM {$wpdb->prefix}es_sentdetails
									WHERE es_sent_type = %s AND es_sent_status = %s
									ORDER BY es_sent_id
									LIMIT 0, 1", $cron, $in_queue );
		$arrRes = $wpdb->get_results( $query, ARRAY_A );

		return $arrRes;

	}

	// Query to insert sent emails (cron) records in table: es_sentdetails
	public static function es_sentmail_cronmail_ups( $guid = "" ) {

		global $wpdb;

		$returnid = 0;
		$currentdate = date('Y-m-d G:i:s');

		$query = $wpdb->prepare( "UPDATE {$wpdb->prefix}es_sentdetails
									SET es_sent_endtime = %s, es_sent_status = %s
									WHERE es_sent_guid = %s
									LIMIT 1", array( $currentdate, "Sent", $guid ) );
		$wpdb->query( $query );

		return true;
	}
}