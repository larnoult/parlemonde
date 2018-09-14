<?php

class WPML_Media_Settings {

	private $wpdb;

	public function __construct( $wpdb ) {
		$this->wpdb = $wpdb;
	}

	public function add_hooks() {
		add_action( 'icl_tm_menu_mcsetup', array( $this, 'render' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_script' ) );
	}

	public function enqueue_script() {
		wp_enqueue_script( 'wpml-media-settings', ICL_PLUGIN_URL . '/res/js/media/settings.js', array(), ICL_SITEPRESS_VERSION, true );
	}

	public function render() {
		$orphan_attachments_sql = "
		SELECT COUNT(*)
		FROM {$this->wpdb->posts}
		WHERE post_type = 'attachment'
			AND ID NOT IN (
				SELECT element_id
				FROM {$this->wpdb->prefix}icl_translations
				WHERE element_type='post_attachment'
			)
		";

		$orphan_attachments     = $this->wpdb->get_var( $orphan_attachments_sql );

		include WPML_PLUGIN_PATH . '/classes/media/management.php';
	}
}