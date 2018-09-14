<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

class es_cls_optimize {
	public static function es_optimize_setdetails() {

		global $wpdb;

		$total = es_cls_sentmail::es_sentmail_count($id = 0);
		if ( $total > 10 ) {
			$delete = $total - 10;
			$sSql = $wpdb->prepare( "DELETE
										FROM {$wpdb->prefix}es_sentdetails
										WHERE 1 = %d
										ORDER BY es_sent_id ASC LIMIT ".$delete, 1 );
			$wpdb->query( $sSql );
		}

		$sSql = $wpdb->prepare( "DELETE
									FROM {$wpdb->prefix}es_deliverreport
									WHERE 1 = %d AND es_deliver_sentguid NOT IN (SELECT es_sent_guid FROM {$wpdb->prefix}es_sentdetails)", 1 );
		$wpdb->query( $sSql );

		return true;
	}
}