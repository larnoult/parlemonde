<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

if ( preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']) ) {
	die('You are not allowed to call this page directly.');
}

global $current_user;
if ( !( $current_user instanceof WP_User ) || !current_user_can( 'manage_options' ) ) {
	die();
}

if ( !empty($_SERVER) && !empty($_GET) && !empty($_GET['es']) && $_GET['es'] == 'export' ) {
	global $wpdb;
	$option = isset($_REQUEST['option']) ? $_REQUEST['option'] : '';
	switch ($option) {
		case "view_all_subscribers":
			$es_get_all_subscribers = $wpdb->prepare( "SELECT es_email_mail AS Email, es_email_name AS Name, es_email_status AS Status, es_email_created AS Created, es_email_group AS EmailGroup
														FROM {$wpdb->prefix}es_emaillist
														WHERE 1 = %d
														ORDER BY es_email_created", 1 );
			$es_all_subscribers = $wpdb->get_results( $es_get_all_subscribers );

			es_cls_common::download( $es_all_subscribers, 's', '' );
			break;
		case "view_active_subscribers":
			$es_get_active_subscribers = $wpdb->prepare( "SELECT es_email_mail AS Email, es_email_name AS Name, es_email_status AS Status, es_email_created AS Created, es_email_group AS EmailGroup
															FROM {$wpdb->prefix}es_emaillist
															WHERE 1 = %d AND es_email_status IN ( 'Confirmed', 'Single Opt In' )
															ORDER BY es_email_created", 1 );
			$es_active_subscribers = $wpdb->get_results( $es_get_active_subscribers );

			es_cls_common::download( $es_active_subscribers, 's', '' );
			break;
		case "view_inactive_subscribers":
			$es_get_inactive_subscribers = $wpdb->prepare( "SELECT es_email_mail AS Email, es_email_name AS Name, es_email_status AS Status, es_email_created AS Created, es_email_group AS EmailGroup
															FROM {$wpdb->prefix}es_emaillist
															WHERE 1 = %d AND es_email_status IN ( 'Unconfirmed', 'Unsubscribed' )
															ORDER BY es_email_created", 1 );
			$es_inactive_subscribers = $wpdb->get_results( $es_get_inactive_subscribers );

			es_cls_common::download( $es_inactive_subscribers, 's', '' );
			break;
		case "registered_user":
			$get_wp_registered_users = $wpdb->prepare( "SELECT user_email AS Email, user_nicename as Name
														FROM {$wpdb->prefix}users
														WHERE 1 = %d
														ORDER BY user_nicename", 1 );
			$wp_registered_users = $wpdb->get_results( $get_wp_registered_users );

			es_cls_common::download( $wp_registered_users, 'r', '' );
			break;
		case "commentposed_user":
			$get_wp_comment_posted_users = $wpdb->prepare( "SELECT DISTINCT(comment_author_email) AS Email, comment_author AS Name
															FROM {$wpdb->prefix}comments
															WHERE comment_author_email != %s
															ORDER BY comment_author_email", '' );
			$wp_comment_posted_users = $wpdb->get_results( $get_wp_comment_posted_users );

			es_cls_common::download( $wp_comment_posted_users, 'c', '' );
			break;
		default:
			echo __( 'Unexpected url submit has been detected!', ES_TDOMAIN );
			break;
	}
} else {
	echo __( 'Unexpected url submit has been detected!', ES_TDOMAIN );
}
die();