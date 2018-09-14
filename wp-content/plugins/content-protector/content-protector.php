<?php
/*
Plugin Name: Passster
Text Domain: content-protector
Author URI:   https://patrickposner.de
Description: Plugin to password-protect portions of a Page or Post.
Author: patrickposner
Version: 3.0

*/

/* main constants */
define( "CONTENT_PROTECTOR_VERSION", "2.11" );
define( "CONTENT_PROTECTOR_HANDLE", "content_protector" );
define( "CONTENT_PROTECTOR_COOKIE_ID", CONTENT_PROTECTOR_HANDLE . "_" );
define( "CONTENT_PROTECTOR_PLUGIN_URL", untrailingslashit( plugin_dir_path( __FILE__ ) ) );
define( "CONTENT_PROTECTOR_PLUGIN_ASSET_URL", untrailingslashit( plugin_dir_url( __FILE__ ) ) );
define( "CONTENT_PROTECTOR_SHORTCODE", CONTENT_PROTECTOR_HANDLE );

/* default settings constants */
define( "CONTENT_PROTECTOR_DEFAULT_FORM_INSTRUCTIONS", __( "This content is protected. Please enter the password to access it.", "content-protector" ) );
define( "CONTENT_PROTECTOR_DEFAULT_AJAX_LOADING_MESSAGE", __( "Checking Password...", "content-protector" ) );
define( "CONTENT_PROTECTOR_DEFAULT_ERROR_MESSAGE", __( "Incorrect password. Try again.", "content-protector" ) );
define( "CONTENT_PROTECTOR_DEFAULT_FORM_SUBMIT_LABEL", _x( "Submit", "Access form submit label", "content-protector" ) );
define( "CONTENT_PROTECTOR_DEFAULT_ENCRYPTION_ALGORITHM", "CRYPT_STD_DES" );
define( "CONTENT_PROTECTOR_DEFAULT_SHARE_AUTH_DURATION", "3600" ); // One hour (in seconds)
define( "CONTENT_PROTECTOR_DEFAULT_FONT_SIZE_OPTION", "0" );  // Make sure default size used is from the stylesheet
define( "CONTENT_PROTECTOR_DEFAULT_FONT_WEIGHT", "400" );  // normal
define( "CONTENT_PROTECTOR_DEFAULT_PASSWORD_FIELD_TYPE", "password" );
define( "CONTENT_PROTECTOR_DEFAULT_PASSWORD_FIELD_LENGTH", "8" );
define( "CONTENT_PROTECTOR_DEFAULT_PASSWORD_FIELD_PLACEHOLDER", _x( "Enter Password", "password field placeholder", "content-protector" ) );


class contentProtectorPlugin {

	/**
	 * @var array
	 */
	private $default_content_filters = array(
		'wptexturize'        => "1",
		'convert_smilies'    => "1",
		'convert_chars'      => "1",
		'wpautop'            => "1",
		'prepend_attachment' => "1",
		'do_shortcode'       => "1"
	);

	/**
	 * @var array
	 */
	private $default_options = array(
		'form_instructions'             => CONTENT_PROTECTOR_DEFAULT_FORM_INSTRUCTIONS,
		'form_instructions_font_size'   => CONTENT_PROTECTOR_DEFAULT_FONT_SIZE_OPTION,
		'form_instructions_font_weight' => CONTENT_PROTECTOR_DEFAULT_FONT_WEIGHT,
		'form_instructions_color'       => "",
		'error_message'                 => CONTENT_PROTECTOR_DEFAULT_ERROR_MESSAGE,
		'error_message_font_size'       => CONTENT_PROTECTOR_DEFAULT_FONT_SIZE_OPTION,
		'error_message_font_weight'     => CONTENT_PROTECTOR_DEFAULT_FONT_WEIGHT,
		'error_message_color'           => "",
		'form_submit_label'             => CONTENT_PROTECTOR_DEFAULT_FORM_SUBMIT_LABEL,
		'form_submit_label_color'       => "",
		'form_submit_button_color'      => "",
		'border_style'                  => "",
		'border_color'                  => "",
		'border_radius'                 => "",
		'border_width'                  => "",
		'padding'                       => "",
		'background_color'              => "",
		'form_css'                      => "",
		'encryption_algorithm'          => CONTENT_PROTECTOR_DEFAULT_ENCRYPTION_ALGORITHM,
		'content_filters'               => array(),
		'other_content_filters'         => "",
		'share_auth'                    => array(),
		'share_auth_duration'           => CONTENT_PROTECTOR_DEFAULT_SHARE_AUTH_DURATION,
		'store_encrypted_passwords'     => "1",
		'delete_options_on_uninstall'   => "",
		'password_field_type'           => CONTENT_PROTECTOR_DEFAULT_PASSWORD_FIELD_TYPE,
		'password_field_placeholder'    => CONTENT_PROTECTOR_DEFAULT_PASSWORD_FIELD_PLACEHOLDER,
		'password_field_length'         => CONTENT_PROTECTOR_DEFAULT_PASSWORD_FIELD_LENGTH,
	);


	/**
	 * contentProtectorPlugin constructor.
	 */
	public function __construct() {

		$this->default_options['content_filters'] = $this->default_content_filters;

		add_action( "init", array( $this, "i18nInit" ), 1 );
		add_action( "wp", array( $this, "setupContentFilters" ), 1 );

		/* helper*/
		require_once( CONTENT_PROTECTOR_PLUGIN_URL . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'class-cp-helper.php' );
		$helper = new CP_Helper();

		/* utilities */
		require_once( CONTENT_PROTECTOR_PLUGIN_URL . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'class-cp-utility.php' );
		$utility = new CP_Utility();

		/* shortcode */
		require_once( CONTENT_PROTECTOR_PLUGIN_URL . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'class-cp-public.php' );
		$public = new CP_Public();

		/* tiny mce addon */
		require_once( CONTENT_PROTECTOR_PLUGIN_URL . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'class-cp-tinymce.php' );
		$mce = new CP_TinyMCE();

		/* options page */
		require_once( CONTENT_PROTECTOR_PLUGIN_URL . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'options' . DIRECTORY_SEPARATOR . 'class-cp-settingspage.php' );
		$admin = new CP_Settingspage();

	}

	/**
	 * internationalisation
	 */
	public function i18nInit() {
		$plugin_dir = basename( dirname( __FILE__ ) ) . "/lang/";
		load_plugin_textdomain( "content-protector", null, $plugin_dir );
	}

	/**
	 * additional content filter
	 */
	public function setupContentFilters() {
		$filters = get_option( CONTENT_PROTECTOR_HANDLE . '_content_filters', $this->default_content_filters );
		foreach ( $filters as $filter => $is_active ) {
			add_filter( "content_protector_unlocked_content", $filter );
		}

		$other_filters = explode( ",", get_option( CONTENT_PROTECTOR_HANDLE . '_other_content_filters', "" ) );
		foreach ( $other_filters as $other_filter ) {
			if ( ! empty( $other_filter ) ) {
				add_filter( "content_protector_unlocked_content", $other_filter );
			}
		}
	}

	/**
	 * activation
	 */
	public function activatePlugin() {
		foreach ( $this->default_options as $option => $value ) {
			update_option( CONTENT_PROTECTOR_HANDLE . '_' . $option, $value );
		}
	}
}


if ( class_exists( "contentProtectorPlugin" ) ) {
	new contentProtectorPlugin();
}
