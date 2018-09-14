<?php

class DLM_Settings_Page {

	/**
	 * Setup hooks
	 */
	public function setup() {

		// menu item
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ), 12 );

		// catch setting actions
		add_action( 'admin_init', array( $this, 'catch_admin_actions' ) );
	}

	/**
	 * Add settings menu item
	 */
	public function add_admin_menu() {
		// Settings page
		add_submenu_page( 'edit.php?post_type=dlm_download', __( 'Settings', 'download-monitor' ), __( 'Settings', 'download-monitor' ), 'manage_options', 'download-monitor-settings', array(
			$this,
			'settings_page'
		) );
	}

	/**
	 * Print global notices
	 */
	private function print_global_notices() {

		// check for nginx
		if ( isset( $_SERVER['SERVER_SOFTWARE'] ) && stristr( $_SERVER['SERVER_SOFTWARE'], 'nginx' ) !== false && 1 != get_option( 'dlm_hide_notice-nginx_rules', 0 ) ) {

			// get upload dir
			$upload_dir = wp_upload_dir();

			// replace document root because nginx uses path from document root
			$upload_path = str_replace( $_SERVER['DOCUMENT_ROOT'], '', $upload_dir['basedir'] );

			// form nginx rules
			$nginx_rules = "location " . $upload_path . "/dlm_uploads {<br/>deny all;<br/>return 403;<br/>}";
			echo '<div class="error notice is-dismissible dlm-notice" id="nginx_rules" data-nonce="' . wp_create_nonce( 'dlm_hide_notice-nginx_rules' ) . '">';
			echo '<p>' . __( "Because your server is running on nginx, our .htaccess file can't protect your downloads.", 'download-monitor' );
			echo '<br/>' . sprintf( __( "Please add the following rules to your nginx config to disable direct file access: %s", 'download-monitor' ), '<br/><br/><code class="dlm-code-nginx-rules">' . $nginx_rules . '</code>' ) . '</p>';
			echo '</div>';
		}

	}


	/**
	 * Catch and trigger admin actions
	 */
	public function catch_admin_actions() {

		if ( isset( $_GET['dlm_action'] ) && isset( $_GET['dlm_nonce'] ) ) {
			$action = $_GET['dlm_action'];
			$nonce  = $_GET['dlm_nonce'];

			// check nonce
			if ( ! wp_verify_nonce( $nonce, $action ) ) {
				wp_die( "Download Monitor action nonce failed." );
			}

			switch ( $action ) {
				case 'dlm_clear_transients':
					$result = download_monitor()->service( 'transient_manager' )->clear_all_version_transients();
					if ( $result ) {
						wp_redirect( add_query_arg( array( 'dlm_action_done' => $action ), DLM_Admin_Settings::get_url() ) );
						exit;
					}
					break;
			}
		}

		if ( isset( $_GET['dlm_action_done'] ) ) {
			add_action( 'admin_notices', array( $this, 'display_admin_action_message' ) );
		}
	}

	/**
	 * Display the admin action success mesage
	 */
	public function display_admin_action_message() {
		?>
        <div class="notice notice-success">
			<?php
			switch ( $_GET['dlm_action_done'] ) {
				case 'dlm_clear_transients':
					echo "<p>" . __( 'Download Monitor Transients successfully cleared!', 'download-monitor' ) . "</p>";
					break;
			}
			?>
        </div>
		<?php
	}


	/**
	 * settings_page function.
	 *
	 * @access public
	 * @return void
	 */
	public function settings_page() {

		// initialize settings
		$admin_settings = new DLM_Admin_Settings();
		$settings       = $admin_settings->get_settings();

		// print global notices
		$this->print_global_notices();
		?>
        <div class="wrap">
            <form method="post" action="options.php">

				<?php settings_fields( 'download-monitor' ); ?>

                <h2 class="nav-tab-wrapper">
					<?php
					foreach ( $settings as $key => $section ) {
						echo '<a href="' . DLM_Admin_Settings::get_url() . '#settings-' . sanitize_title( $key ) . '" id="dlm-tab-settings-' . sanitize_title( $key ) . '" class="nav-tab">' . esc_html( $section[0] ) . '</a>';
					}
					?>
                </h2><br/>

                <input type="hidden" id="setting-dlm_settings_tab_saved" name="dlm_settings_tab_saved" value="general"/>

				<?php

				if ( ! empty( $_GET['settings-updated'] ) ) {
					$this->need_rewrite_flush = true;
					echo '<div class="updated notice is-dismissible"><p>' . __( 'Settings successfully saved', 'download-monitor' ) . '</p></div>';

					$dlm_settings_tab_saved = get_option( 'dlm_settings_tab_saved', 'general' );

					echo '<script type="text/javascript">var dlm_settings_tab_saved = "' . $dlm_settings_tab_saved . '";</script>';
				}

				foreach ( $settings as $key => $section ) {

					echo '<div id="settings-' . sanitize_title( $key ) . '" class="settings_panel">';

					echo '<table class="form-table">';

					foreach ( $section[1] as $option ) {

						$cs = 1;

						echo '<tr valign="top">';
						if ( isset( $option['label'] ) && '' !== $option['label'] ) {
							echo '<th scope="row"><label for="setting-' . $option['name'] . '">' . $option['label'] . '</a></th>';
						} else {
							$cs ++;
						}


						echo '<td colspan="' . $cs . '">';

						if ( ! isset( $option['type'] ) ) {
							$option['type'] = '';
						}

						// make new field object
						$field = DLM_Admin_Fields_Field_Factory::make( $option );

						// check if factory made a field
						if ( null !== $field ) {
							// render field
							$field->render();

							if ( isset( $option['desc'] ) && '' !== $option['desc'] ) {
								echo ' <p class="dlm-description">' . $option['desc'] . '</p>';
							}
						}

						echo '</td></tr>';
					}

					echo '</table></div>';

				}
				?>
                <p class="submit">
                    <input type="submit" class="button-primary"
                           value="<?php _e( 'Save Changes', 'download-monitor' ); ?>"/>
                </p>
            </form>
        </div>
		<?php
	}

}