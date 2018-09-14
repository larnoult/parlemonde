<?php

class Dem_Tinymce {

	function __construct(){
		self::tinymce_button();
	}

	## tinymce кнопка
	static function tinymce_button(){
		add_filter('mce_external_plugins', array('Dem_Tinymce', 'tinymce_plugin') ) ;
		add_filter('mce_buttons',          array('Dem_Tinymce', 'tinymce_register_button') );
		add_filter('wp_mce_translation',   array('Dem_Tinymce', 'tinymce_l10n') );
	}

	static function tinymce_register_button( $buttons ) {
		array_push( $buttons, 'separator', 'demTiny');
		return $buttons;
	}

	static function tinymce_plugin( $plugin_array ) {
		$plugin_array['demTiny'] = DEMOC_URL .'js/tinymce.js';
		return $plugin_array;
	}

	static function tinymce_l10n( $mce_l10n ) {
		$l10n = array_map('esc_js', array(
			'Insert Poll of Democracy' => __('Insert Poll of Democracy', 'democracy-poll'),
			'Insert Poll ID' => __('Insert Poll ID', 'democracy-poll'),
			'Error: ID is a integer. Enter ID again, please.' => __('Error: ID is a integer. Enter ID again, please.', 'democracy-poll'),
		) );

		return $mce_l10n + $l10n;
	}
}
