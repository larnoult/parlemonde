<?php
/**
 * Displays an inactive message if the API License Key has not yet been activated
 *
 * @package Virtue Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class for API getting started page.
 *
 * @category class
 */
class Kadence_API_Manager {
	/**
	 * This is where we make api calls.
	 *
	 * @var api url
	 */
	public $upgrade_url = 'https://www.kadencethemes.com/';

	/**
	 * This is fall back for where we make api calls.
	 *
	 * @var url
	 */
	private $fallback_api_url = 'https://api.kadencethemes.com/';

	/**
	 * This is the link to the account page.
	 *
	 * @var url
	 */
	private $renewal_url = 'https://www.kadencethemes.com/my-account/';

	/**
	 * This is the link to the account page.
	 *
	 * @var url
	 */
	private $kt_license_link;

	/**
	 * This is the current theme version.
	 *
	 * @var number
	 */
	public $version;

	/**
	 * This is the theme name for the kadence theme.
	 *
	 * @var theme name
	 */
	private $kt_product_name;

	/**
	 * This is the current theme data object.
	 *
	 * @var theme data
	 */
	private $my_theme;

	/**
	 * This is the data key for database.
	 *
	 * @var string
	 */
	public $kt_data_key = 'kt_api_manager';

	/**
	 * This is the settings key for api key.
	 *
	 * @var string
	 */
	public $kt_api_key = 'kt_api_key';

	/**
	 * This is the settings key for api email.
	 *
	 * @var string
	 */
	public $kt_activation_email = 'activation_email';

	/**
	 * This is the product ID key.
	 *
	 * @var string
	 */
	public $kt_product_id_key;

	/**
	 * This is the api instance key.
	 *
	 * @var string
	 */
	public $kt_instance_key;

	/**
	 * This is the api activated key.
	 *
	 * @var string
	 */
	public $kt_activated_key;

	/**
	 * This is the settings key for api checkbox.
	 *
	 * @var string
	 */
	public $kt_deactivate_checkbox = 'kt_deactivate_example_checkbox';

	/**
	 * This is the settings key for api activate tab.
	 *
	 * @var string
	 */
	public $kt_activation_tab_key = 'kt_api_manager_dashboard';

	/**
	 * This is the settings key for api deactive tab.
	 *
	 * @var string
	 */
	public $kt_deactivation_tab_key = 'kt_api_manager_dashboard_deactivation';
	/**
	 * This is the page menu title.
	 *
	 * @var string
	 */
	public $kt_settings_menu_title;
	/**
	 * This is the page title.
	 *
	 * @var string
	 */
	public $kt_settings_title;
	/**
	 * This is the activation title.
	 *
	 * @var string
	 */
	public $kt_menu_tab_activation_title;
	/**
	 * This is the deactivation title.
	 *
	 * @var string
	 */
	public $kt_menu_tab_deactivation_title;
	/**
	 * This is options array.
	 *
	 * @var array
	 */
	public $kt_options;
	/**
	 * This is the product ID.
	 *
	 * @var string
	 */
	public $kt_product_id;
	/**
	 * This is the instance ID.
	 *
	 * @var string
	 */
	public $kt_instance_id;
	/**
	 * This is the site domain.
	 *
	 * @var string
	 */
	public $kt_domain;

	/**
	 * Instance Control.
	 *
	 * @var null
	 */
	protected static $_instance = null;

	/**
	 * Instance Control.
	 *
	 * @param string $kt_product_id_key product ID key.
	 * @param string $kt_instance_key product instance key.
	 * @param string $kt_activated_key product activated key.
	 * @param string $kt_product_id the product ID.
	 * @param string $kt_product_name the product name.
	 */
	public static function instance( $kt_product_id_key, $kt_instance_key, $kt_activated_key, $kt_product_id, $kt_product_name ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $kt_product_id_key, $kt_instance_key, $kt_activated_key, $kt_product_id, $kt_product_name );
		}
		return self::$_instance;
	}
	/**
	 * Constructor function.
	 *
	 * @param string $kt_product_id_key product ID key.
	 * @param string $kt_instance_key product instance key.
	 * @param string $kt_activated_key product activated key.
	 * @param string $kt_product_id the product ID.
	 * @param string $kt_product_name the product name.
	 */
	public function __construct( $kt_product_id_key, $kt_instance_key, $kt_activated_key, $kt_product_id, $kt_product_name ) {
		// Only run in the admin.
		if ( is_admin() ) {

			add_action( 'admin_notices', array( $this, 'check_external_blocking' ) );
			add_action( 'admin_init', array( $this, 'activation' ) );

			// Repeat Check license.
			add_filter( 'pre_set_site_transient_update_themes', array( $this, 'status_check' ) );

			$this->my_theme = wp_get_theme(); // Get theme data.
			$this->version  = $this->my_theme->get( 'Version' );

			/**
			 * Set all data defaults here
			 */
			$this->kt_product_name                = apply_filters( 'kadence_whitelabel_theme_name', $kt_product_name );
			$this->kt_license_link                = apply_filters( 'kadence_whitelabel_license_link', $this->renewal_url );
			$this->kt_product_id_key              = $kt_product_id_key;
			$this->kt_instance_key                = $kt_instance_key;
			$this->kt_activated_key               = $kt_activated_key;
			$this->kt_settings_menu_title         = __( 'Getting Started', 'virtue' );
			$this->kt_menu_tab_activation_title   = __( 'API License Activation', 'virtue' );
			$this->kt_menu_tab_deactivation_title = __( 'Deactivation', 'virtue' );
			$this->kt_options                     = get_option( $this->kt_data_key );
			$this->kt_product_id                  = $kt_product_id; // Software ID.
			$this->kt_instance_id                 = get_option( $this->kt_instance_key ); // Instance ID (unique to each blog activation).
			$this->kt_domain                      = str_ireplace( array( 'http://', 'https://' ), '', home_url() );

			add_action( 'admin_menu', array( $this, 'add_menu' ) );
			add_action( 'admin_init', array( $this, 'load_settings' ) );
			if ( 'Activated' !== get_option( $this->kt_activated_key ) ) {
				add_action( 'admin_notices', array( $this, 'kt_api_m_inactive_notice' ) );
			}
		}
	}
	/**
	 * Activation function to set defaults.
	 */
	public function activation() {
		if ( false === get_option( $this->kt_data_key ) || false === get_option( $this->kt_instance_key ) ) {
			$global_options = array(
				$this->kt_api_key          => '',
				$this->kt_activation_email => '',
			);
			update_option( $this->kt_data_key, $global_options );
			$single_options = array(
				$this->kt_product_id_key      => $this->kt_product_id,
				$this->kt_instance_key        => wp_generate_password( 12, false ),
				$this->kt_deactivate_checkbox => 'on',
				$this->kt_activated_key       => 'Deactivated',
			);
			foreach ( $single_options as $key => $value ) {
				update_option( $key, $value );
			}
		}
	}
	/**
	 * Check the license status.
	 *
	 * @param string $transient_value filter to pass along.
	 */
	public function status_check( $transient_value = null ) {
		$status = get_transient( 'kt_api_status_check' );
		if ( false === $status ) {
			if ( get_option( $this->kt_activated_key ) === 'Activated' ) {
				$data    = get_option( $this->kt_data_key );
				$license = substr( $data[ $this->kt_api_key ], 0, 3 );
				if ( 'wc_' !== $license && 'ord' !== $license ) {
					$args = array(
						'email'       => $data[ $this->kt_activation_email ],
						'licence_key' => $data[ $this->kt_api_key ],
					);
					$status_results = json_decode( $this->status( $args ), true );
					if ( 'failed' === $status_results ) {
						// do nothing.
					} elseif ( isset( $status_results['activated'] ) && 'inactive' === $status_results['activated'] ) {
						$this->uninstall();
						update_option( $this->kt_activated_key, 'Deactivated' );
					} elseif ( isset( $status_results['error'] ) && ( '101' == $status_results['code'] || '104' == $status_results['code'] ) ) {
						$this->uninstall();
						update_option( $this->kt_activated_key, 'Deactivated' );
					}
				}
			}
			set_transient( 'kt_api_status_check', 1, 1200 );
		}
		return $transient_value;
	}
	/**
	 * Uninstall the product license.
	 */
	public function uninstall() {
		global $blog_id;

		$this->license_key_deactivation();

		// Remove options.
		if ( is_multisite() ) {

			switch_to_blog( $blog_id );

			foreach ( array(
				$this->kt_data_key,
				$this->kt_product_id_key,
				$this->kt_instance_key,
				$this->kt_deactivate_checkbox,
				$this->kt_activated_key,
			) as $option ) {
				delete_option( $option );
			}
			restore_current_blog();

		} else {

			foreach ( array(
				$this->kt_data_key,
				$this->kt_product_id_key,
				$this->kt_instance_key,
				$this->kt_deactivate_checkbox,
				$this->kt_activated_key,
			) as $option ) {
				delete_option( $option );
			}
		}
	}

	/**
	 * Deactivates the license on the API server
	 */
	public function license_key_deactivation() {
		$activation_status = get_option( $this->kt_activated_key );

		$api_email = $this->kt_options[ $this->kt_activation_email ];
		$api_key   = $this->kt_options[ $this->kt_api_key ];

		$args = array(
			'email'       => $api_email,
			'licence_key' => $api_key,
		);

		if ( 'Activated' === $activation_status && ! empty( $api_key ) && ! empty( $api_email ) ) {
			$this->deactivate( $args ); // reset license key activation.
		}
	}

	/**
	 * Displays an inactive notice when the software is inactive.
	 */
	public static function kt_api_m_inactive_notice() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		if ( isset( $_GET['page'] ) && 'kt_api_manager_dashboard' === $_GET['page'] ) {
			return;
		}
		?>
		<div id="message" class="error">
			<p><?php /* translators: %1$s and %2$s refer to an internal link markup */ printf( __( 'The theme update API License Key has not been activated! %1$sClick here%2$s to activate the license api key.', 'virtue' ), '<a href="' . esc_url( admin_url( 'themes.php?page=kt_api_manager_dashboard' ) ) . '">', '</a>' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Check for external blocking contstant
	 */
	public function check_external_blocking() {
		// show notice if external requests are blocked through the WP_HTTP_BLOCK_EXTERNAL constant.
		if ( defined( 'WP_HTTP_BLOCK_EXTERNAL' ) && WP_HTTP_BLOCK_EXTERNAL === true ) {

			// check if our API endpoint is in the allowed hosts
			$host = parse_url( $this->upgrade_url, PHP_URL_HOST );

			if( ! defined( 'WP_ACCESSIBLE_HOSTS' ) || stristr( WP_ACCESSIBLE_HOSTS, $host ) === false ) {
			?>
			<div class="error">
			<p><?php printf( __( '<b>Warning!</b> You\'re blocking external requests which means you won\'t be able to get %1$s updates. Please add %2$s to %3$s.', 'virtue' ), $this->kt_product_id, '<strong>' . $host . '</strong>', '<code>WP_ACCESSIBLE_HOSTS</code>'); ?></p>
			</div>
			<?php
			}

		}
	}
	/**
	 * Add menu page.
	 */
	public function add_menu() {
		$page = add_theme_page( $this->kt_settings_menu_title, $this->kt_settings_menu_title, 'manage_options', $this->kt_activation_tab_key, array( $this, 'config_page' ) );
		add_action( 'admin_print_styles-' . $page, array( $this, 'css_and_scripts' ) );
	}
	/**
	 * Build the config page.
	 */
	public function config_page() {

		$settings_tabs = array(
			$this->kt_activation_tab_key   => $this->kt_menu_tab_activation_title,
			$this->kt_deactivation_tab_key => $this->kt_menu_tab_deactivation_title,
		);
		$current_tab   = isset( $_GET['tab'] ) ? wp_unslash( $_GET['tab'] ) : $this->kt_activation_tab_key;
		settings_errors();
		?>
		<div class="wrap kt_theme_license">
		<h2 class="notices"></h2>

			<div class="kt_title_area">
				<h1>
					<?php /* translators: %s theme name */ echo apply_filters( 'kt_getting_started_page_title', __( 'Getting Started with ', 'virtue' ) . '<strong>' . esc_html( $this->kt_product_name ) . '</strong>' ); ?>
				</h1>
				<h4>
					<?php echo apply_filters( 'kt_getting_started_page_subtitle', __( 'Theme activation, recommended plugins and helpful links.', 'virtue' ) ); ?>
				</h4>
			</div>
			<div class="kad-content-wrapper kt-admin-clearfix">
				<div class="kad-panel-right kt-admin-clearfix">
					<div class="kad-panel-contain">
						<h2 class="nav-tab-wrapper">
							<?php
							foreach ( $settings_tabs as $tab_page => $tab_name ) {
								$active_tab = $current_tab == $tab_page ? 'nav-tab-active' : '';
								echo '<a class="nav-tab ' . $active_tab . '" href="?page=' . $this->kt_activation_tab_key . '&tab=' . $tab_page . '">' . $tab_name . '</a>';
							}
							?>
						</h2>
						<div class="nav-tab-content kt-admin-clearfix">
							<form action='options.php' method='post'>
							<div class="kt-main">
							<?php
							if( $current_tab == $this->kt_activation_tab_key ) {
								settings_fields( $this->kt_data_key );
								do_settings_sections( $this->kt_activation_tab_key );
								submit_button( __( 'Save Changes', 'virtue' ) );
							} else {
								settings_fields( $this->kt_deactivate_checkbox );
								do_settings_sections( $this->kt_deactivation_tab_key );
								submit_button( __( 'Save Changes', 'virtue' ) );
							}
							?>
							</div>
						</form>
						</div>
					</div>
				</div>
				<div class="kad-panel-left kt-admin-clearfix">
					<div class="kad-panel-contain kt-admin-clearfix">
						<?php
						do_action( 'kadence_getting_start_config' );
						?>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
	/**
	 * Register settings
	 */
	public function load_settings() {

		register_setting( $this->kt_data_key, $this->kt_data_key, array( $this, 'validate_options' ) );

		// API Key.
		add_settings_section( $this->kt_api_key, __( 'Update API License Activation', 'virtue' ), array( $this, 'kt_api_key_text' ), $this->kt_activation_tab_key );
		add_settings_field( $this->kt_api_key, __( 'Update API License Key', 'virtue' ), array( $this, 'kt_api_key_field' ), $this->kt_activation_tab_key, $this->kt_api_key );
		add_settings_field( $this->kt_activation_email, __( 'Update API License email', 'virtue' ), array( $this, 'kt_api_email_field' ), $this->kt_activation_tab_key, $this->kt_api_key );

		// Activation settings.
		register_setting( $this->kt_deactivate_checkbox, $this->kt_deactivate_checkbox, array( $this, 'kt_license_key_deactivation' ) );
		add_settings_section( 'deactivate_button', __( 'API License Deactivation', 'virtue' ), array( $this, 'kt_deactivate_text' ), $this->kt_deactivation_tab_key );
		add_settings_field( 'deactivate_button', __( 'Deactivate API License Key', 'virtue' ), array( $this, 'kt_deactivate_textarea' ), $this->kt_deactivation_tab_key, 'deactivate_button' );

	}

	/**
	 * Provides text for api key section
	 */
	public function kt_api_key_text() {
		/* translators: %1$s: <a>, %2$s: </a> */
		echo sprintf( __( 'Activating your license allows for updates to theme and bundled plugins. If you need your api key you will find it by %1$slogging into your account%2$s.', 'virtue' ), '<a href="' . esc_url( $this->kt_license_link ) . '" target="_blank">', '</a>' );
		echo '<input type="hidden" value="' . esc_html( $this->kt_instance_id ) . '">';
	}

	/**
	 * Outputs API License text field
	 */
	public function kt_api_key_field() {

		echo "<input id='api_key' name='" . $this->kt_data_key . "[" . $this->kt_api_key . "]' size='25' type='text' value='" . $this->kt_options[ $this->kt_api_key ] . "' />";
		if ( $this->kt_options[ $this->kt_api_key ] ) {
			echo '<span class="ktap-icon-pos"><i class="dashicons dashicons-yes" style="font-size:20px; color:green;"></i></span>';
		} else {
			echo '<span class="ktap-icon-pos"><i class="dashicons dashicons-warning" style="font-size:20px; color:orange;"></i></span>';
		}
		echo '<span class="kt-activation-input-description">' . __( 'Required', 'virtue' ) . '</span>';
	}

	/**
	 * Outputs API License email text field
	 */
	public function kt_api_email_field() {
		echo "<input id='activation_email' name='" . $this->kt_data_key . "[" . $this->kt_activation_email . "]' size='25' type='text' value='" . $this->kt_options[ $this->kt_activation_email ] . "' />";
		if ( $this->kt_options[ $this->kt_activation_email ] ) {
			echo '<span class="ktap-icon-pos"><i class="dashicons dashicons-yes" style="font-size:20px; color:green;"></i></span>';
		} else {
			echo '<span class="ktap-icon-pos"><i class="dashicons dashicons-warning" style="font-size:20px; color:orange;"></i></span>';
		}
		echo '<span class="kt-activation-input-description">' . __( 'Required', 'virtue' ) . '</span>';
	}

	/**
	 * Sanitizes and validates all input and output for Dashboard
	 */
	public function validate_options( $input ) {

		// Load existing options, validate, and update with changes from input before returning.
		$options                                = $this->kt_options;
		$options[ $this->kt_api_key ]           = trim( $input[ $this->kt_api_key ] );
		$options[ $this->kt_activation_email ]  = trim( $input[ $this->kt_activation_email ] );
		$api_email                              = trim( $input[ $this->kt_activation_email ] );
		$api_key                                = trim( $input[ $this->kt_api_key ] );
		$activation_status                      = get_option( $this->kt_activated_key );
		$checkbox_status                        = get_option( $this->kt_deactivate_checkbox );
		$current_api_key                        = $this->kt_options[ $this->kt_api_key ];

		// Should match the settings_fields() value.
		if ( $_REQUEST['option_page'] != $this->kt_deactivate_checkbox ) {
			if ( $activation_status == 'Deactivated' || $activation_status == '' || $api_key == '' || $api_email == '' || $checkbox_status == 'on' || $current_api_key != $api_key ) {
				if ( isset($current_api_key ) && ! empty( $current_api_key ) ) {
					if ( $current_api_key != $api_key ) {
						$this->replace_license_key( $current_api_key );
					}
				}

				$args = array(
					'email'         => $api_email,
					'licence_key'   => $api_key,
				);

				$activate_results = json_decode( $this->activate( $args ), true );

				if ( $activate_results['activated'] === true ) {
					add_settings_error( 'activate_text', 'activate_msg', __( 'Theme activated. ', 'virtue' ), 'updated' );
					update_option( $this->kt_activated_key, 'Activated' );
					update_option( $this->kt_deactivate_checkbox, 'off' );
					update_option( 'kt_api_active_order', $activate_results['activation_extra']['order_id']);
				}

                if ( $activate_results == false && ! empty( $this->kt_options ) && ! empty( $this->kt_activated_key )) {
					add_settings_error( 'api_key_check_text', 'api_key_check_error', __( 'Connection failed to the License Key API server. Make sure your host servers php version has the curl module installed and enabled.', 'virtue' ), 'error' );
					$options[$this->kt_api_key] = '';
					$options[$this->kt_activation_email] = '';
					update_option( $this->kt_options[$this->kt_activated_key], 'Deactivated' );
					update_option( 'kt_api_active_order', '');
                }

                if ( isset( $activate_results['code'] )  && ! empty( $this->kt_options ) && ! empty( $this->kt_activated_key ) ) {

                    switch ( $activate_results['code'] ) {
                        case '100':
                            $additional_info = ! empty( $activate_results['additional info'] ) ? esc_attr( $activate_results['additional info'] ) : '';
                            add_settings_error( 'api_email_text', 'api_email_error', "{$activate_results['error']}. {$additional_info}", 'error' );
                            $options[$this->kt_activation_email] = '';
                            $options[$this->kt_api_key] = '';
                            update_option( $this->kt_options[$this->kt_activated_key], 'Deactivated' );
                        break;
                        case '101':
                            $additional_info = ! empty( $activate_results['additional info'] ) ? esc_attr( $activate_results['additional info'] ) : '';
                            add_settings_error( 'api_key_text', 'api_key_error', "{$activate_results['error']}. {$additional_info}", 'error' );
                            $options[$this->kt_api_key] = '';
                            $options[$this->kt_activation_email] = '';
                            update_option( $this->kt_options[$this->kt_activated_key], 'Deactivated' );
                        break;
                        case '102':
                            $additional_info = ! empty( $activate_results['additional info'] ) ? esc_attr( $activate_results['additional info'] ) : '';
                            add_settings_error( 'api_key_purchase_incomplete_text', 'api_key_purchase_incomplete_error', "{$activate_results['error']}. {$additional_info}", 'error' );
                            $options[$this->kt_api_key] = '';
                            $options[$this->kt_activation_email] = '';
                            update_option( $this->kt_options[$this->kt_activated_key], 'Deactivated' );
                        break;
                        case '103':
                            $additional_info = ! empty( $activate_results['additional info'] ) ? esc_attr( $activate_results['additional info'] ) : '';
                            add_settings_error( 'api_key_exceeded_text', 'api_key_exceeded_error', "{$activate_results['error']}. {$additional_info}", 'error' );
                            $options[$this->kt_api_key] = '';
                            $options[$this->kt_activation_email] = '';
                            update_option( $this->kt_options[$this->kt_activated_key], 'Deactivated' );
                        break;
                        case '104':
                            $additional_info = ! empty( $activate_results['additional info'] ) ? esc_attr( $activate_results['additional info'] ) : '';
                            add_settings_error( 'api_key_not_activated_text', 'api_key_not_activated_error', "{$activate_results['error']}. {$additional_info}", 'error' );
                            $options[$this->kt_api_key] = '';
                            $options[$this->kt_activation_email] = '';
                            update_option( $this->kt_options[$this->kt_activated_key], 'Deactivated' );
                        break;
                        case '105':
                            $additional_info = ! empty( $activate_results['additional info'] ) ? esc_attr( $activate_results['additional info'] ) : '';
                            $additional_info = ! empty( $activate_results['additional info'] ) ? esc_attr( $activate_results['additional info'] ) : '';
                            add_settings_error( 'api_key_invalid_text', 'api_key_invalid_error', "{$activate_results['error']}. {$$additional_info}", 'error' );
                            $options[$this->kt_api_key] = '';
                            $options[$this->kt_activation_email] = '';
                            update_option( $this->kt_options[$this->kt_activated_key], 'Deactivated' );
                        break;
                        case '106':
                            $additional_info = ! empty( $activate_results['additional info'] ) ? esc_attr( $activate_results['additional info'] ) : '';
                            add_settings_error( 'sub_not_active_text', 'sub_not_active_error', "{$activate_results['error']}. {$additional_info}", 'error' );
                            $options[$this->kt_api_key] = '';
                            $options[$this->kt_activation_email] = '';
                            update_option( $this->kt_options[$this->kt_activated_key], 'Deactivated' );
                        break;
                    }

                }

            }

        }

        return $options;
    }

    // Deactivate the current license key before activating the new license key
    public function replace_license_key( $current_api_key ) {

        $args = array(
            'email'         => $this->kt_options[$this->kt_activation_email],
            'licence_key'   => $current_api_key,
            );

        $reset = $this->deactivate( $args ); // reset license key activation

        if ( $reset == true ) {
            return true;
        } 

        add_settings_error( 'not_deactivated_text', 'not_deactivated_error', __( 'The license could not be deactivated. Use the License Deactivation tab to manually deactivate the license before activating a new license.', 'virtue' ), 'updated' );

        return false;
    }

	// Deactivates the license key to allow key to be used on another blog
	public function kt_license_key_deactivation( $input ) {

		$activation_status = get_option( $this->kt_activated_key );

		$args = array(
			'email'       => $this->kt_options[$this->kt_activation_email],
			'licence_key' => $this->kt_options[$this->kt_api_key],
		);

        $options = ( $input == 'on' ? 'on' : 'off' );
        if($options == 'on' && $activation_status != 'Activated') {
            update_option( $this->kt_instance_key, wp_generate_password( 12, false ) );
            $update = array(
                    $this->kt_api_key => '',
                    $this->kt_activation_email => ''
                    );
            $merge_options = array_merge( $this->kt_options, $update );
            update_option( $this->kt_data_key, $merge_options );
        }

        if ( $options == 'on' && $activation_status == 'Activated' && $this->kt_options[$this->kt_api_key] != '' && $this->kt_options[$this->kt_activation_email] != '' ) {

            $activate_results = json_decode( $this->deactivate( $args ), true );

            if ( $activate_results['deactivated'] == true ) {
                $update = array(
                    $this->kt_api_key => '',
                    $this->kt_activation_email => ''
                    );

                $merge_options = array_merge( $this->kt_options, $update );
                if ( ! empty( $this->kt_activated_key ) ) {
                    update_option( $this->kt_data_key, $merge_options );
                    update_option( $this->kt_activated_key, 'Deactivated' );
                    update_option( 'kt_api_active_order', '');
                    add_settings_error( 'kt_deactivate_text', 'deactivate_msg', __( 'Theme license deactivated. ', 'virtue' ) . "{$activate_results['activations_remaining']}.", 'updated' );
                }

                return $options;
            }

            if ( isset( $activate_results['code'] ) && ! empty( $this->kt_options ) && ! empty( $this->kt_activated_key) ) {

                switch ( $activate_results['code'] ) {
                    case '100':
                        $additional_info = ! empty( $activate_results['additional info'] ) ? esc_attr( $activate_results['additional info'] ) : '';
                        add_settings_error( 'api_email_text', 'api_email_error', "{$activate_results['error']}. {$additional_info}", 'error' );
                        $update = array(
                            $this->kt_api_key => '',
                            $this->kt_activation_email => ''
                        );
                        $merge_options = array_merge( $this->kt_options, $update );
                        update_option( $this->kt_activated_key, 'Deactivated' );
                    break;
                    case '101':
                        $additional_info = ! empty( $activate_results['additional info'] ) ? esc_attr( $activate_results['additional info'] ) : '';
                        add_settings_error( 'api_key_text', 'api_key_error', "{$activate_results['error']}. {$additional_info}", 'error' );
                        $options[$this->kt_api_key] = '';
                        $options[$this->kt_activation_email] = '';
                        update_option( $this->kt_options[$this->kt_activated_key], 'Deactivated' );
                    break;
                    case '102':
                        $additional_info = ! empty( $activate_results['additional info'] ) ? esc_attr( $activate_results['additional info'] ) : '';
                        add_settings_error( 'api_key_purchase_incomplete_text', 'api_key_purchase_incomplete_error', "{$activate_results['error']}. {$additional_info}", 'error' );
                        $options[$this->kt_api_key] = '';
                        $options[$this->kt_activation_email] = '';
                        update_option( $this->kt_options[$this->kt_activated_key], 'Deactivated' );
                    break;
                    case '103':
                        $additional_info = ! empty( $activate_results['additional info'] ) ? esc_attr( $activate_results['additional info'] ) : '';
                        add_settings_error( 'api_key_exceeded_text', 'api_key_exceeded_error', "{$activate_results['error']}. {$additional_info}", 'error' );
                        $options[$this->kt_api_key] = '';
                        $options[$this->kt_activation_email] = '';
                        update_option( $this->kt_options[$this->kt_activated_key], 'Deactivated' );
                    break;
                    case '104':
                        $additional_info = ! empty( $activate_results['additional info'] ) ? esc_attr( $activate_results['additional info'] ) : '';
                        add_settings_error( 'api_key_not_activated_text', 'api_key_not_activated_error', "{$activate_results['error']}. {$additional_info}", 'error' );
                        $options[$this->kt_api_key] = '';
                        $options[$this->kt_activation_email] = '';
                        update_option( $this->kt_options[$this->kt_activated_key], 'Deactivated' );
                    break;
                    case '105':
                        $additional_info = ! empty( $activate_results['additional info'] ) ? esc_attr( $activate_results['additional info'] ) : '';
                        add_settings_error( 'api_key_invalid_text', 'api_key_invalid_error', "{$activate_results['error']}. {$additional_info}", 'error' );
                        $options[$this->kt_api_key] = '';
                        $options[$this->kt_activation_email] = '';
                        update_option( $this->kt_options[$this->kt_activated_key], 'Deactivated' );
                    break;
                    case '106':
                        $additional_info = ! empty( $activate_results['additional info'] ) ? esc_attr( $activate_results['additional info'] ) : '';
                        add_settings_error( 'sub_not_active_text', 'sub_not_active_error', "{$activate_results['error']}. {$additional_info}", 'error' );
                        $options[$this->kt_api_key] = '';
                        $options[$this->kt_activation_email] = '';
                        update_option( $this->kt_options[$this->kt_activated_key], 'Deactivated' );
                    break;
                }

            }

        } else {

            return $options;
        }

        return false;
	}
	/**
	 * Loads the deactivate text
	 */
	public function kt_deactivate_text() {
	}
	/**
	 *  Loads the deactivate text area
	 */
	public function kt_deactivate_textarea() {

		echo '<input type="checkbox" id="' . esc_attr( $this->kt_deactivate_checkbox ) . '" name="' . esc_attr( $this->kt_deactivate_checkbox ) . '" value="on"';
		echo checked( get_option( $this->kt_deactivate_checkbox ), 'on' );
		echo '/>';
		?>
		<span class="description"><?php esc_html_e( 'Deactivates an API License Key.', 'virtue' ); ?></span>
		<?php
	}

	/**
	 * Loads admin style sheets and scripts
	 */
	public function css_and_scripts() {
		wp_enqueue_style( 'kadence-api-manager-css', get_template_directory_uri() . '/kt-framework/kadence-api-manager/kadence-api-manager.css', array(), $this->version, 'all' );
		wp_enqueue_script( 'kadence-api-manager-js', get_template_directory_uri() . '/kt-framework/kadence-api-manager/kadence-api-manager.js', array( 'jquery' ), $this->version, true );
		wp_localize_script( 'kadence-api-manager-js', 'kadence_api_params', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'wpnonce' => wp_create_nonce( 'install-plugin_kadence-bundled' ),
		) );
	}
	/**
	 * Create Software API URL
	 *
	 * @param array $args for the url.
	 */
	public function create_software_api_url( $args ) {

		$api_url = add_query_arg( $args, $this->upgrade_url );

		return $api_url;
	}
	/**
	 * Activate the domain
	 *
	 * @param array $args for the activation.
	 */
	public function activate( $args ) {
		$license = substr( $args['licence_key'], 0, 3 );
		if ( 'vps' === $license ) {
			$productid = 'vps';
		} elseif ( 'ktm' === $license ) {
			$productid = 'ktm';
		} elseif ( 'ktl' === $license ) {
			$productid = 'ktl';
		} else {
			$productid = $this->kt_product_id;
		}
		$defaults = array(
			'wc-api'           => 'am-software-api',
			'request'          => 'activation',
			'product_id'       => $productid,
			'instance'         => $this->kt_instance_id,
			'platform'         => $this->kt_domain,
			'software_version' => $this->version,
		);
		$args = wp_parse_args( $defaults, $args );

		$target_url = esc_url_raw( $this->create_software_api_url( $args ) );

		$request = wp_safe_remote_get( $target_url, array( 'sslverify' => false ) );

		if ( is_wp_error( $request ) ) {
			// Lets try api address.
			$new_target_url = esc_url_raw( add_query_arg( $args, $this->fallback_api_url ) );
			$request        = wp_safe_remote_get( $new_target_url, array( 'sslverify' => false ) );
			if ( is_wp_error( $request ) || 200 != wp_remote_retrieve_response_code( $request ) ) {
				return false;
			}
		} elseif ( 200 != wp_remote_retrieve_response_code( $request ) ) {

			return false;
		}

		$response = wp_remote_retrieve_body( $request );

		return $response;
	}
	/**
	 * Deactivate the domain
	 *
	 * @param array $args for the deactivation.
	 */
	public function deactivate( $args ) {
		$license = substr( $args['licence_key'], 0, 3 );
		if ( 'vps' === $license ) {
			$productid = 'vps';
		} elseif ( 'ktm' === $license ) {
			$productid = 'ktm';
		} elseif ( 'ktl' === $license ) {
			$productid = 'ktl';
		} else {
			$productid = $this->kt_product_id;
		}
		$defaults = array(
			'wc-api'     => 'am-software-api',
			'request'    => 'deactivation',
			'product_id' => $productid,
			'instance'   => $this->kt_instance_id,
			'platform'   => $this->kt_domain,
		);

		$args = wp_parse_args( $defaults, $args );

		$target_url = esc_url_raw( $this->create_software_api_url( $args ) );

		$request = wp_safe_remote_get( $target_url, array( 'sslverify'  => false ) );
		if ( is_wp_error( $request ) ) {
			// Lets try api address.
			$new_target_url = esc_url_raw( add_query_arg( $args, $this->fallback_api_url ) );
			$request        = wp_safe_remote_get( $new_target_url, array( 'sslverify' => false ) );
			if ( is_wp_error( $request ) || 200 != wp_remote_retrieve_response_code( $request ) ) {
				return false;
			}
		} elseif ( 200 != wp_remote_retrieve_response_code( $request ) ) {
			return false;
		}

		$response = wp_remote_retrieve_body( $request );

		return $response;
	}

	/**
	 * Checks if the software is activated or deactivated
	 *
	 * @param array $args for the status check.
	 */
	public function status( $args ) {
		$license = substr( $args['licence_key'], 0, 3 );
		if ( 'vps' === $license ) {
			$productid = 'vps';
		} elseif ( 'ktm' === $license ) {
			$productid = 'ktm';
		} elseif ( 'ktl' === $license ) {
			$productid = 'ktl';
		} else {
			$productid = $this->kt_product_id;
		}
		$defaults = array(
			'wc-api'     => 'am-software-api',
			'request'    => 'status',
			'product_id' => $productid,
			'instance'   => $this->kt_instance_id,
			'platform'   => $this->kt_domain,
		);

		$args = wp_parse_args( $defaults, $args );

		$target_url = esc_url_raw( $this->create_software_api_url( $args ) );

		$request = wp_safe_remote_get( $target_url, array( 'sslverify'  => false ) );

		if ( is_wp_error( $request ) ) {
			// Lets try api address.
			$new_target_url = esc_url_raw( add_query_arg( $args, $this->fallback_api_url ) );
			$request        = wp_safe_remote_get( $new_target_url, array( 'sslverify' => false ) );
			if ( is_wp_error( $request ) || 200 != wp_remote_retrieve_response_code( $request ) ) {
				return 'failed';
			}
		} elseif ( 200 != wp_remote_retrieve_response_code( $request ) ) {
			return 'failed';
		}

		$response = wp_remote_retrieve_body( $request );

		return $response;
	}

}
Kadence_API_Manager::instance( 'virtue_premium_api_key', 'kt_api_manager_virtue_premium_instance', 'kt_api_manager_virtue_premium_activated', 'virtue_premium', 'Virtue Premium' );

if ( ! class_exists( 'kt_api_manager' ) ) {
	/**
	 * Class kept for compatablity.
	 *
	 * @category class
	 */
	class kt_api_manager {
	}
}

