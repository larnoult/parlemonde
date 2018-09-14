<?php

if( ! defined('WP_UNINSTALL_PLUGIN') ) exit;

// multisite
if( is_multisite() ){
	foreach( function_exists('get_sites') ? get_sites() : wp_get_sites() as $site ) {
		switch_to_blog( is_array($site) ? $site['blog_id'] : $site->blog_id ); // get_sites of WP 4.6+ return objects ...

		dem_delete_plugin();
	}

	restore_current_blog();
}
else
	dem_delete_plugin();


function dem_delete_plugin(){
	global $wpdb;
	$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}democracy_q, {$wpdb->prefix}democracy_a, {$wpdb->prefix}democracy_log");

	delete_option('widget_democracy'); // wp option...
	delete_option('democracy_options');
	delete_option('democracy_version');
	delete_option('democracy_css');
	delete_option('democracy_l10n');
	delete_option('democracy_migrated');

	delete_transient('democracy_referer', '', 2 );
}


