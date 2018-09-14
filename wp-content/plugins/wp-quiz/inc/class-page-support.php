<?php
/**
 * Class For WP Quiz Import Export page
 */
class WP_Quiz_Page_Support {

	public static function admin_print_styles() {
		?>
		<style>
			#mts-debug-data-field { font-family: monospace; }
		</style>
		<?php
	}

	public static function load() {

		$screen = get_current_screen();
		add_meta_box( 'support-content', esc_html__( 'Technical Support', 'wp-quiz' ), array( __CLASS__, 'support_content' ), $screen->id, 'normal', 'core' );

		// Needed javascripts to allow drag/drop, expand/collapse and hide/show of boxes
		wp_enqueue_script( 'common' );
		wp_enqueue_script( 'wp-lists' );
		wp_enqueue_script( 'postbox' );

		wp_enqueue_script( 'clipboard', wp_quiz()->plugin_url() . 'assets/js/clipboard.min.js',  array('jquery'), wp_quiz()->version, true );
	}

	public static function page() {

		$screen 		= get_current_screen();
		$columns 		= absint( $screen->get_columns() );
		$columns_css 	= '';

		if ( $columns ) {
			$columns_css = " columns-$columns";
		}
		?>
			<div class="wrap" id="config-page">
				<h2><?php echo get_admin_page_title(); ?></h2>
				<input type="hidden" name="page" value="wp_quiz_config" />
				<div id="poststuff">
					<div id="post-body" class="metabox-holder <?php echo $columns_css ?>">
						<div id="postbox-container-2" class="postbox-container">
							<?php
								do_meta_boxes( $screen->id, 'normal', '' );
							?>
						</div>
					</div>
				</div>

			</div>
			<script type="text/javascript">
				//<![CDATA[
					(function ( $ ) {

					"use strict";

					$(function () {

						var mts_log_generating = false;
						var mts_log_generated = false;

						function init_support_buttons() {
							if ( ! $( '.mts-support-copy' ).length ) {
								return false;
							}

							var clipboard = new Clipboard('.mts-support-copy', {
								target: function(trigger) {
									return $( '#mts-debug-data-field' )[0];
								},
							});

							$( '#mts-debug-data-field' ).click(function(event) {
								$( this ).select();
							});
							return true;
						};

						function mts_load_support_log() {
							loading_progress();
							$.ajax({
								url: ajaxurl,
								method: 'post',
								data: {
									'action' : 'wpquiz_get_debug_log'
								},
								success: function(data) {
									$('#mts-debug-data-field').val( data ).prop( 'disabled', false );
									$('.mts-support-copy').prop( 'disabled', false );
									mts_log_generated = true;
								},
								error: function(data) {
									$('#mts-debug-data-field').val( 'Something went wrong.' );
									mts_log_generated = true;
								}
							});

							mts_log_generating = true;
						}

						function loading_progress() {
							if ( ! mts_log_generated ) {
								$('#mts-debug-data-field').val(function(index, lastval) { return lastval+' .' });
								setTimeout(loading_progress, 500);
							}
						}

						function mtsGetParameterByName(name, url) {
							if (!url) {
								url = window.location.href;
							}
							name = name.replace(/[\[\]]/g, "\\$&");
							var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
								results = regex.exec(url);
							if (!results) return null;
							if (!results[2]) return '';
							return decodeURIComponent(results[2].replace(/\+/g, " "));
						}

						init_support_buttons();

						mts_load_support_log();
					});

				}(jQuery));
				//]]>
			</script>
		<?php
	}

	public static function support_content() {
		?>
			<div>
				<?php
				echo '<p>' .
				/* Translators: %s expands to "our Support Forums" link */
				sprintf( __( 'We offer technical support through our %s. Please <strong>copy and paste the following information in your ticket</strong> when contacting support:' , 'wp-quiz' ), '<a href="https://community.mythemeshop.com/forum/11-free-plugin-support/" target="_blank">' . __( 'Support Ticket System', 'wp-quiz' ) . '</a>' )
				 . '</p>';
				?>

				<textarea class="large-text" id="mts-debug-data-field" rows="16" readonly="readonly" disabled><?php _e( 'Gathering information. Please wait . . .', 'wp-quiz' ); ?></textarea>

				<?php self::support_buttons(); ?>
			</div>
		<?php
	}

	public function get_debug_log() {
		echo $this->debug_data_output();
		die();
	}

	public static function support_buttons( $copy_button = true ) {
		?>
		<div class="mts-help-buttons">
			<?php if ( $copy_button ) { ?>
				<button type="button" class="button mts-support-copy" disabled>
					<!-- <span class="dashicons dashicons-clipboard"></span> -->
					<?php _e( 'Copy Data for Support Request', 'wp-quiz' ); ?>
				</button>
			<?php } ?>
			<a href="https://community.mythemeshop.com/forum/11-free-plugin-support/" target="_blank" class="button button-primary mts-support-link">
				<!-- <span class="dashicons dashicons-external"></span> -->
				<?php _e( 'Open Support Forum', 'wp-quiz' ); ?>
			</a>
		</div>
		<?php
	}
	public function debug_data_output() {
		$data = $this->get_debug_data();
		$output = "`\n";
		foreach ( $data as $section_id => $section_data ) {
			$output .= $this->debug_section_output( $section_data );
		}
		$output = trim( $output );
		$output .= "\n`";
		return $output;
	}
	public function debug_section_output( $debug_section ) {
		$output = '';
		$output .= '--- ' . $debug_section['title'] . ' ---' . "\n";
		$output .= $this->debug_data_prettify( $debug_section['data'] );
		$output .= "\n";
		return $output;
	}
	public function debug_data_prettify( $data, $level = 0 ) {
		$output = '';
		$pad_to = 0;
		foreach ( $data as $key => $value ) {
			$pad_to = max( $pad_to, strlen( $key ) + 2 );
		}
		foreach ( $data as $key => $value ) {
			$key = str_replace( '_', ' ', $key );
			$key = ucwords( $key );
			$key = str_replace( array( 'Wp ', 'Php ' ), array( 'WP ', 'PHP ' ), $key );
			if ( is_array( $value ) ) {
				$output .= str_repeat( ' ', $level * 2 ) . "$key: \n";
				$output .= $this->debug_data_prettify( $value, $level + 1 );
				continue;
			}
			if ( $value === true ) {
				$value = 'Yes';
			} elseif ( $value === false ) {
				$value = 'No';
			}
			$output .= str_repeat( ' ', $level * 2 );
			$output .= str_pad( $key . ': ', $pad_to );
			$output .= "{$value}\n";
		}
		return $output;
	}
	/**
	 *
	 * @return array
	 */
	public function get_debug_data() {
		$data = array();
		$data['environment'] = array(
			'title' => __( 'Environment', 'wp-quiz' ),
			'data' => $this->get_environment_info(),
		);
		$data['wp'] = array(
			'title' => __( 'WordPress', 'wp-quiz' ),
			'data' => $this->get_wp_info(),
		);
		$data['plugins'] = array(
			'title' => __( 'Plugins', 'wp-quiz' ),
			'data' => $this->get_plugins_info(),
		);
		$data['themes'] = array(
			'title' => __( 'Theme', 'wp-quiz' ),
			'data' => $this->get_theme_info(),
		);
		$data['settings'] = array(
			'title' => __( 'WP Quiz', 'wp-quiz' ),
			'data' => $this->get_plugin_settings(),
		);
		return apply_filters( 'mts_support_log', $data );
	}
	public function get_environment_info() {
		global $wpdb;
		// WP memory limit
		// Figure out cURL version, if installed.
		$curl_version = '';
		if ( function_exists( 'curl_version' ) ) {
			$curl_version = curl_version();
			$curl_version = $curl_version['version'] . ', ' . $curl_version['ssl_version'];
		}
		return array(
			'server_info'               => $_SERVER['SERVER_SOFTWARE'],
			'php_version'               => phpversion(),
			'php_post_max_size'         => $this->let_to_num( ini_get( 'post_max_size' ) ),
			'php_max_execution_time'    => ini_get( 'max_execution_time' ),
			'php_max_input_vars'        => ini_get( 'max_input_vars' ),
			'curl_version'              => $curl_version,
			'max_upload_size'           => wp_max_upload_size(),
			'mysql_version'             => ( ! empty( $wpdb->is_mysql ) ? $wpdb->db_version() : '' ),
			'default_timezone'          => date_default_timezone_get(),
			'fsockopen_or_curl_enabled' => ( function_exists( 'fsockopen' ) || function_exists( 'curl_init' ) ),
			'domdocument_enabled'       => class_exists( 'DOMDocument' ),
			'simplexml_enabled' 		=> extension_loaded( 'SimpleXML' ),
			'gd_extension'              => extension_loaded( 'gd' ) && function_exists( 'gd_info' ),
			'imagick_extension'         => extension_loaded( 'imagick' ),
			'allow_url_fopen'        	=> (bool) ini_get( 'allow_url_fopen' ),
			'allow_url_include'        	=> (bool) ini_get( 'allow_url_include' ),
		);
	}
	public function get_wp_info() {
		$wp_memory_limit = $this->let_to_num( WP_MEMORY_LIMIT );
		if ( function_exists( 'memory_get_usage' ) ) {
			$wp_memory_limit = max( $wp_memory_limit, $this->let_to_num( @ini_get( 'memory_limit' ) ) );
		}
		return array(
			'home_url'                  => get_option( 'home' ),
			'site_url'                  => get_option( 'siteurl' ),
			'secure_connection' 		=> is_ssl(),
			'hide_errors'      			=> ! ( defined( 'WP_DEBUG' ) && defined( 'WP_DEBUG_DISPLAY' ) && WP_DEBUG && WP_DEBUG_DISPLAY ) || 0 === intval( ini_get( 'display_errors' ) ),
			'wp_version'                => get_bloginfo( 'version' ),
			'wp_multisite'              => is_multisite(),
			'wp_memory_limit'           => $wp_memory_limit,
			'wp_debug_mode'             => ( defined( 'WP_DEBUG' ) && WP_DEBUG ),
			'wp_cron'                   => ! ( defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON ),
			'language'                  => get_locale(),
		);
	}
	public function get_plugins_info() {
		$out = array();
		$plugins = $this->get_active_plugins();
		foreach ( $plugins as $key => $value ) {
			$name = $value['name'];
			if ( isset( $value['known_conflict'] ) && $value['known_conflict'] ) {
				$name = '(!) ' . $name;
			}
			$out[] = $name . ' v' . $value['version'] . ' ' . ( empty( $value['author_url'] ) ? $value['author_name'] : $value['author_url'] );
		}
		return $out;
	}
	public function get_active_plugins() {
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		// Get both site plugins and network plugins
		$active_plugins = (array) get_option( 'active_plugins', array() );
		if ( is_multisite() ) {
			$network_activated_plugins = array_keys( get_site_option( 'active_sitewide_plugins', array() ) );
			$active_plugins            = array_merge( $active_plugins, $network_activated_plugins );
		}
		$active_plugins_data = array();
		foreach ( $active_plugins as $plugin ) {
			$data           = get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin );
			$dirname        = dirname( $plugin );
			$version_latest = '';
			$slug           = explode( '/', $plugin );
			$slug           = explode( '.', end( $slug ) );
			$slug           = $slug[0];
			include_once( ABSPATH . 'wp-admin/includes/plugin-install.php' );
			$api = plugins_api( 'plugin_information', array(
				'slug'     => $slug,
				'fields'   => array(
					'sections' => false,
					'tags'     => false,
				),
			) );
			if ( is_object( $api ) && ! is_wp_error( $api ) && ! empty( $api->version ) ) {
				$version_latest = $api->version;
			}
			// convert plugin data to json response format.
			$active_plugins_data[] = array(
				'plugin'            => $plugin,
				'name'              => $data['Name'],
				'version'           => $data['Version'],
				'version_latest'    => $version_latest,
				'url'               => $data['PluginURI'],
				'author_name'       => $data['AuthorName'],
				'author_url'        => esc_url_raw( $data['AuthorURI'] ),
				'network_activated' => $data['Network'],
			);
		}// End foreach().
		return $active_plugins_data;
	}
	public function get_theme_info() {
		$active_theme = wp_get_theme();
		// Get parent theme info if this theme is a child theme, otherwise
		// pass empty info in the response.
		if ( is_child_theme() ) {
			$parent_theme      = wp_get_theme( $active_theme->Template );
			$parent_theme_info = array(
				'parent_name'           => $parent_theme->Name,
				'parent_version'        => $parent_theme->Version,
				'parent_author_url'     => $parent_theme->{'Author URI'},
			);
		} else {
			// $parent_theme_info = array( 'parent_name' => '', 'parent_version' => '', 'parent_version_latest' => '', 'parent_author_url' => '' );
			$parent_theme_info = array();
		}
		$active_theme_info = array(
			'name'                    => $active_theme->Name,
			'version'                 => $active_theme->Version,
			'author_url'              => esc_url_raw( $active_theme->{'Author URI'} ),
			'is_child_theme'          => is_child_theme(),
		);
		return array_merge( $active_theme_info, $parent_theme_info );
	}
	public function get_plugin_settings() {
		$options = get_option( 'wp_quiz_default_settings' );
		$mail_service = __( 'None', 'wp-quiz' );
		if ( $options['mail_service'] == 1 ) {
			$mail_service = 'Mailchimp';
		} elseif ( $options['mail_service'] == 2 ) {
			$mail_service = 'GetResponse';
		}
		return array(
			'wp_quiz_version' => get_option( 'wp_quiz_version' ),
			'selected_mail_service' => $mail_service
		);
	}
	/**
	 * let_to_num function.
	 *
	 * This function transforms the php.ini notation for numbers (like '2M') to an integer.
	 *
	 * @param $size
	 * @return int
	 */
	public function let_to_num( $size ) {
		$l   = substr( $size, -1 );
		$ret = substr( $size, 0, -1 );
		switch ( strtoupper( $l ) ) {
			case 'P':
				$ret *= 1024;
			case 'T':
				$ret *= 1024;
			case 'G':
				$ret *= 1024;
			case 'M':
				$ret *= 1024;
			case 'K':
				$ret *= 1024;
		}
		return $ret;
	}
}
