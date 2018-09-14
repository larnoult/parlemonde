<?php

class QuizMaster_Helper_Fields {

	public static function getFieldApiPrefix() {

		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

		$isAcfActive = is_plugin_active('advanced-custom-fields-pro/acf.php');
		$isFieldMasterActive = is_plugin_active('fieldmaster/fieldmaster.php');

	  if( $isFieldMasterActive ) {
			return "fieldmaster";
		}

		if( $isAcfActive ) {
			return "acf";
		}

	}

}
