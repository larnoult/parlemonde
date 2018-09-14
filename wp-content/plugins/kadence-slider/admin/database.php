<?php

class KadenceSliderProDatabase {

	public static function ksp_setversion() {
		update_option('ksp_version', KS_VERSION);
	}

	// Creates or updates all the Settings
	public static function setDatabase() {
		self::setSlidersDatabase();
		self::setSlidesDatabase();
		self::setLayersDatabase();
	}

	public static function setSlidersDatabase() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'ksp_sliders';
		
		$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		name TEXT CHARACTER SET utf8,
		responsive INT,
		maxHeight INT,
		maxWidth INT,
		fullHeight INT,
		full_offset TEXT CHARACTER SET utf8,
		fullWidth INT,
		autoPlay INT,
		pauseTime INT,
		enableParallax INT,
		singleSlide INT,
		minHeight INT,
		pauseonHover INT,
		UNIQUE KEY id (id)
		);";
		
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}

	// Warning: the time variable is a string because it could contain the 'all' word
	public static function setSlidesDatabase() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'ksp_slides';
		
		$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		slider_parent mediumint(9),
		position INT,
		background_type_image TEXT CHARACTER SET utf8,
		background_type_color TEXT CHARACTER SET utf8,
		background_propriety_position TEXT CHARACTER SET utf8,
		background_repeat TEXT CHARACTER SET utf8,
		background_propriety_size TEXT CHARACTER SET utf8,
		background_type_video TEXT CHARACTER SET utf8,
		background_type_video_youtube TEXT CHARACTER SET utf8,
		background_type_video_mute INT,
		background_type_video_loop INT,
		background_type_video_playpause INT,
		background_type_video_ratio TEXT CHARACTER SET utf8,
		background_type_video_start INT,
		background_type_video_mp4 TEXT CHARACTER SET utf8,
		background_type_video_webm TEXT CHARACTER SET utf8,
		background_link TEXT CHARACTER SET utf8,
		background_link_new_tab INT DEFAULT 0,
		UNIQUE KEY id (id)
		);";
		
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}

	public static function setLayersDatabase() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'ksp_layers';
		
		$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		slider_parent mediumint(9),
		slide_parent mediumint(9),
		position INT,
		type TEXT CHARACTER SET utf8,
		inner_html TEXT CHARACTER SET utf8,
		image_src TEXT CHARACTER SET utf8,
		image_alt TEXT CHARACTER SET utf8,
		align_horizontal TEXT CHARACTER SET utf8,
		align_veritcal TEXT CHARACTER SET utf8,
		data_y INT,
		data_x INT,
		width INT,
		height INT,
		font_color TEXT CHARACTER SET utf8,
		font_hover_color TEXT CHARACTER SET utf8,
		background_color TEXT CHARACTER SET utf8,
		background_hover_color TEXT CHARACTER SET utf8,
		border_color TEXT CHARACTER SET utf8,
		border_hover_color TEXT CHARACTER SET utf8,
		border_width INT,
		border_radius INT,
		letter_spacing INT,
		padding INT,
		font_size INT,
		line_height TEXT CHARACTER SET utf8,
		font TEXT CHARACTER SET utf8,
		data_delay INT,
		data_time TEXT CHARACTER SET utf8,
		data_in TEXT CHARACTER SET utf8,
		data_out TEXT CHARACTER SET utf8,
		data_ease INT,
		link TEXT CHARACTER SET utf8 DEFAULT '',
		link_new_tab INT DEFAULT 0,
		z_index INT,
		text_shadow TEXT CHARACTER SET utf8,
		UNIQUE KEY id (id)
		);";
		
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}
}

?>