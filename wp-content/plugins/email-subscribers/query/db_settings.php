<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class es_cls_settings {
	public static function es_setting_select( $id = 1 ) {

		global $wpdb;

		$arrRes = array();

		$es_get_settings_data = $wpdb->prepare( "SELECT *
												FROM {$wpdb->prefix}es_pluginconfig
												WHERE es_c_id = %d", $id );
		$es_settings_data = $wpdb->get_row( $es_get_settings_data, ARRAY_A );

		return $es_settings_data;

	}

	public static function es_get_all_settings() {

		global $wpdb;

	 	$condition = 'ig_es';
	 	$get_all_es_settings_from_options = $wpdb->prepare( "SELECT option_name, option_value
	 														FROM {$wpdb->prefix}options
	 														WHERE option_name LIKE %s", $wpdb->esc_like( $condition ) . '%' );
	 	$result = $wpdb->get_results( $get_all_es_settings_from_options, ARRAY_A );

	 	$settings = array();

	 	if ( ! empty( $result ) ) {
	 		foreach ($result as $index => $data ) {
	 			$settings[ $data['option_name'] ] = $data['option_value'];
	 		}
	 	}

	 	return $settings;

	}
}