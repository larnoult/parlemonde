<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if( (isset($_GET['es'])) && ($_GET['es'] == "cron") ) {

	$es_process_request = true;
	$es_process_request = apply_filters( 'es_process_request' , $es_process_request );
	if( false === $es_process_request ) return;

	$es_c_cronguid = isset($_GET['guid']) ? $_GET['guid'] : '';
	$es_c_cronguid = trim($es_c_cronguid);

	if($es_c_cronguid != "") {
		
		$security1 = strlen($es_c_cronguid);
		$es_c_cronguid_noslash = str_replace("-", "", $es_c_cronguid);
		$security2 = strlen($es_c_cronguid_noslash);
		if($security1 == 34 && $security2 == 30) {
			if ( !preg_match('/[^a-z]/', $es_c_cronguid_noslash) ) {
			   	$es_c_cronurl = get_option('ig_es_cronurl');
				$es_c_croncount = get_option('ig_es_cron_mailcount');
				$es_c_croncount = apply_filters('es_email_sending_limit', $es_c_croncount );
				parse_str($es_c_cronurl, $output);
				if($es_c_cronguid == $output['guid']) {
					if( !is_numeric($es_c_croncount) ) {	//if $es_c_croncount is coming empty, then set $es_c_croncount should be passed empty?
						$es_c_croncount = 50;
					}

					$cronmailqueue = es_cls_sentmail::es_sentmail_cronmail_inqueue();
					if(count($cronmailqueue) > 0) {
						$crondeliveryqueue = es_cls_delivery::es_delivery_cronmail_inqueue($es_c_croncount, $cronmailqueue[0]['es_sent_guid']);
						if(count($crondeliveryqueue) > 0) {
							es_cls_sendmail::es_prepare_send_cronmail($cronmailqueue, $crondeliveryqueue);
						}

						$cronmailqueuecnt = es_cls_delivery::es_delivery_cronmail_count($cronmailqueue[0]['es_sent_guid']);
						if($cronmailqueuecnt == 0) {
							es_cls_sentmail::es_sentmail_cronmail_ups($cronmailqueue[0]['es_sent_guid']);
						}
					}
					$cronmailqueuecnt = (!empty($cronmailqueuecnt)) ? $cronmailqueuecnt : 0;
					$response = array('es_remaining_email_count' => $cronmailqueuecnt );
					echo json_encode($response);
				}
			}
		}
	}
}
die();