<?php
//info: die if uninstall not called from Wordpress exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) )
	exit ();
$current_version = "v3121"; //2do: change on each update to current version!
if (is_multisite()) {
	global $wpdb;
	$blogs = $wpdb->get_results("SELECT `blog_id` FROM {$wpdb->blogs}", ARRAY_A);
	$lmm_pro_readme = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'leaflet-maps-marker-pro' . DIRECTORY_SEPARATOR . 'readme.txt';
		//info: delete transients (needed for reinstalls within validity of transients)
		$schedule_transient = 'leafletmapsmarker_install_update_cache_' . $current_version;
		delete_transient( $schedule_transient );

		//info: delete WordPress pointer IDs in user_meta (dismissed_wp_pointers) for current user
		$current_dismissed_wp_pointers = get_user_meta(get_current_user_id(), "dismissed_wp_pointers");
		$replace_lmmv = preg_replace('/(lmmv(p)?(\\d)+(,)?)/',NULL,$current_dismissed_wp_pointers['0']);
		$replace_lmmesw = preg_replace('/(lmmesw)(,)?/',NULL,$replace_lmmv);
		$replace_without_end_comma = preg_replace('/(,)$/',NULL,$replace_lmmesw);
		update_user_meta( get_current_user_id(), 'dismissed_wp_pointers', $replace_without_end_comma);

		//info: dont remove files if pro version exists
		if (!file_exists($lmm_pro_readme)) {
			delete_transient( 'leafletmapsmarker_version_before_update' );
			delete_transient('leafletmapsmarker_tinymce_custom_css');
			delete_option('leafletmapsmarker_options');
			delete_option('leafletmapsmarker_version');
			delete_option('leafletmapsmarker_version_before_update');
			delete_option('leafletmapsmarker_redirect');
			delete_option('leafletmapsmarker_update_info');
			delete_option('leafletmapsmarker_editor');
			//info: unschedule session garbage collector.
			$timestamp = wp_next_scheduled( 'lmm_wp_session_garbage_collection' );
			wp_unschedule_event( $timestamp, 'lmm_wp_session_garbage_collection' );
			//info: delete _wp_session-entries in wp_options
			$GLOBALS['wpdb']->query("DELETE FROM `".$GLOBALS['wpdb']->prefix."options` WHERE `option_name` LIKE '_wp_session_%';");
			$GLOBALS['wpdb']->query("OPTIMIZE TABLE `".$GLOBALS['wpdb']->prefix."options`;");
			/*remove map icons directory for main site */
			$lmm_upload_dir = wp_upload_dir();
			$icons_directory = $lmm_upload_dir['basedir'] . DIRECTORY_SEPARATOR . "leaflet-maps-marker-icons" . DIRECTORY_SEPARATOR;
			if (is_dir($icons_directory)) {
				foreach(glob($icons_directory.'*.*') as $v){
					unlink($v);
				}
				rmdir($icons_directory);
			}
		}
	if ($blogs) {
		foreach($blogs as $blog) {
			switch_to_blog($blog['blog_id']);
			//info: delete transients (needed for reinstalls within validity of transients)
			$schedule_transient = 'leafletmapsmarker_install_update_cache_' . $current_version;
			delete_transient( $schedule_transient );

			//info: delete WordPress pointer IDs in user_meta (dismissed_wp_pointers) for current user
			$current_dismissed_wp_pointers = get_user_meta(get_current_user_id(), "dismissed_wp_pointers");
			$replace_lmmv = preg_replace('/(lmmv(p)?(\\d)+(,)?)/',NULL,$current_dismissed_wp_pointers['0']);
			$replace_lmmesw = preg_replace('/(lmmesw)(,)?/',NULL,$replace_lmmv);
			$replace_without_end_comma = preg_replace('/(,)$/',NULL,$replace_lmmesw);
			update_user_meta( get_current_user_id(), 'dismissed_wp_pointers', $replace_without_end_comma);

			//info: dont remove files if pro version exists
			if (!file_exists($lmm_pro_readme)) {
				delete_transient( 'leafletmapsmarker_version_before_update' );
				delete_transient('leafletmapsmarker_tinymce_custom_css');
				delete_option('leafletmapsmarker_options');
				delete_option('leafletmapsmarker_version');
				delete_option('leafletmapsmarker_version_before_update');
				delete_option('leafletmapsmarker_redirect');
				delete_option('leafletmapsmarker_update_info');
				delete_option('leafletmapsmarker_editor');
				//info: unschedule session garbage collector.
				$timestamp = wp_next_scheduled( 'lmm_wp_session_garbage_collection' );
				wp_unschedule_event( $timestamp, 'lmm_wp_session_garbage_collection' );
				/* Remove and clean tables */
				$GLOBALS['wpdb']->query("DROP TABLE `".$GLOBALS['wpdb']->prefix."leafletmapsmarker_layers`");
				$GLOBALS['wpdb']->query("DROP TABLE `".$GLOBALS['wpdb']->prefix."leafletmapsmarker_markers`");

				/*remove map icons directory for subsites*/
				$lmm_upload_dir = wp_upload_dir();
				$icons_directory = $lmm_upload_dir['basedir'] . DIRECTORY_SEPARATOR . "leaflet-maps-marker-icons" . DIRECTORY_SEPARATOR;
				if (is_dir($icons_directory)) {
					foreach(glob($icons_directory.'*.*') as $v) {
						unlink($v);
					}
					rmdir($icons_directory);
				}
				//info: delete _wp_session-entries in wp_options
				$GLOBALS['wpdb']->query("DELETE FROM `".$GLOBALS['wpdb']->prefix."options` WHERE `option_name` LIKE '_wp_session_%';");
				$GLOBALS['wpdb']->query("OPTIMIZE TABLE `".$GLOBALS['wpdb']->prefix."options`;");
			}
			restore_current_blog();
		}
	}
}
else
{
	//info: delete transients (needed for reinstalls within validity of transients)
	$schedule_transient = 'leafletmapsmarker_install_update_cache_' . $current_version;
	delete_transient( $schedule_transient );

	//info: delete WordPress pointer IDs in user_meta (dismissed_wp_pointers) for current user
	$current_dismissed_wp_pointers = get_user_meta(get_current_user_id(), "dismissed_wp_pointers");
	$replace_lmmv = preg_replace('/(lmmv(p)?(\\d)+(,)?)/',NULL,$current_dismissed_wp_pointers['0']);
	$replace_lmmesw = preg_replace('/(lmmesw)(,)?/',NULL,$replace_lmmv);
	$replace_without_end_comma = preg_replace('/(,)$/',NULL,$replace_lmmesw);
	update_user_meta( get_current_user_id(), 'dismissed_wp_pointers', $replace_without_end_comma);

	//info: dont remove files if pro version exists
	$lmm_pro_readme = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'leaflet-maps-marker-pro' . DIRECTORY_SEPARATOR . 'readme.txt';
	if (!file_exists($lmm_pro_readme)) {
		delete_transient( 'leafletmapsmarker_version_before_update' );
		delete_transient('leafletmapsmarker_tinymce_custom_css');
		delete_option('leafletmapsmarker_options');
		delete_option('leafletmapsmarker_version');
		delete_option('leafletmapsmarker_version_before_update');
		delete_option('leafletmapsmarker_redirect');
		delete_option('leafletmapsmarker_update_info');
		delete_option('leafletmapsmarker_editor');
		/* Remove and clean tables */
		$GLOBALS['wpdb']->query("DROP TABLE `".$GLOBALS['wpdb']->prefix."leafletmapsmarker_layers`");
		$GLOBALS['wpdb']->query("DROP TABLE `".$GLOBALS['wpdb']->prefix."leafletmapsmarker_markers`");
		$GLOBALS['wpdb']->query("OPTIMIZE TABLE `" .$GLOBALS['wpdb']->prefix."options`");
		/*remove map icons directory*/
		$lmm_upload_dir = wp_upload_dir();
		$icons_directory = $lmm_upload_dir['basedir'] . DIRECTORY_SEPARATOR . "leaflet-maps-marker-icons" . DIRECTORY_SEPARATOR;
		if (is_dir($icons_directory)) {
			foreach(glob($icons_directory.'*.*') as $v) {
				unlink($v);
			}
		rmdir($icons_directory);
		}
		//info: unschedule session garbage collector.
		$timestamp = wp_next_scheduled( 'lmm_wp_session_garbage_collection' );
		wp_unschedule_event( $timestamp, 'lmm_wp_session_garbage_collection' );
		//info: delete _wp_session-entries in wp_options
		$GLOBALS['wpdb']->query("DELETE FROM `".$GLOBALS['wpdb']->prefix."options` WHERE `option_name` LIKE '_wp_session_%';");
		$GLOBALS['wpdb']->query("OPTIMIZE TABLE `".$GLOBALS['wpdb']->prefix."options`;");
	}
}