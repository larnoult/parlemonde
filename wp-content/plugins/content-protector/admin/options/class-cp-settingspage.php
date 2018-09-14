<?php

class CP_Settingspage {

	private $default_content_filters = array(
		'wptexturize'        => "1",
		'convert_smilies'    => "1",
		'convert_chars'      => "1",
		'wpautop'            => "1",
		'prepend_attachment' => "1",
		'do_shortcode'       => "1"
	);


	public function __construct() {
		add_action( "admin_menu", array( $this, "initSettingsPage" ), 1 );
		add_action( 'update_option', array( $this, "updateEncryptedPasswordsTransients" ), 10, 3 );
	}


	/**
	 * Prints out the Settings page.
	 *
	 */
	function drawSettingsPage() {
		ob_start();
		// Optional: you can display the admin screen as an accordion. Uncomment the next line of PHP code,
		// comment out the line following it, and follow the instructions in /js/content-protector-admin.js.
		//include( "screens/admin_screen_accordion.php" );
		require_once( CONTENT_PROTECTOR_PLUGIN_URL . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'options' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'admin_screen_tabs.php' );

		$content = ob_get_contents();
		ob_end_clean();

		echo $content;
	}

	/**
	 * Adds back-facing Javascript for form fields on the Settings page.
	 *
	 */
	function addAdminHeaderCode() {
		wp_enqueue_style( 'content-protector-jquery-ui-css', CONTENT_PROTECTOR_PLUGIN_ASSET_URL . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'jqueryui' . DIRECTORY_SEPARATOR . 'jquery-ui.css', false, CONTENT_PROTECTOR_VERSION );
		wp_enqueue_style( 'content-protector-jquery-ui-theme-css', CONTENT_PROTECTOR_PLUGIN_ASSET_URL . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'jqueryui' . DIRECTORY_SEPARATOR . 'theme.css', false, CONTENT_PROTECTOR_VERSION );

		wp_enqueue_style( 'wp-color-picker' );

		$css_all_default   = "/* " . __( "These styles will be applied to all Content Protector access forms.", "content-protector" ) . " */\n" .
		                     "form.content-protector-access-form {\n" .
		                     "\t/* " . __( "CSS styles for the entire form", "content-protector" ) . " */\n}\n" .
		                     "div.content-protector-form-instructions {\n" .
		                     "\t/* " . __( "CSS styles for the form instructions", "content-protector" ) . " */\n}\n" .
		                     "div.content-protector-correct-password {\n" .
		                     "\t/* " . __( "CSS styles for the message when the correct password is entered", "content-protector" ) . " */\n}\n" .
		                     "div.content-protector-incorrect-password {\n" .
		                     "\t/* " . __( "CSS styles for the error message for an incorrect password", "content-protector" ) . " */\n}\n" .
		                     "input.content-protector-form-submit {\n" .
		                     "\t/* " . __( "CSS styles for the Submit button", "content-protector" ) . " */\n}\n" .
		                     "input.content-protector-captcha-img {\n" .
		                     "\t/* " . __( "CSS styles for the CAPTCHA box", "content-protector" ) . " */\n}\n" .
		                     "div.content-protector-ajaxLoading {\n" .
		                     "\t/* " . __( "CSS styles for the AJAX loading message", "content-protector" ) . " */\n}\n";
		$css_ident_default = "/* " . __( "These styles will be applied to the Content Protector access form whose identifier is &quot;{id}&quot;.", "content-protector" ) . " */\n" .
		                     "#content-protector-access-form-{id} {\n" .
		                     "\t/* " . __( "CSS styles for the entire form", "content-protector" ) . " */\n}\n" .
		                     "#content-protector-form-instructions-{id} {\n" .
		                     "\t/* " . __( "CSS styles for the form instructions", "content-protector" ) . " */\n}\n" .
		                     "#content-protector-correct-password-{id} {\n" .
		                     "\t/* " . __( "CSS styles for the message when the correct password is entered", "content-protector" ) . " */\n}\n" .
		                     "#content-protector-incorrect-password-{id} {\n" .
		                     "\t/* " . __( "CSS styles for the error message for an incorrect password", "content-protector" ) . " */\n}\n" .
		                     "#content-protector-form-submit-{id} {\n" .
		                     "\t/* " . __( "CSS styles for the Submit button", "content-protector" ) . " */\n}\n" .
		                     "#content-protector-captcha-img-{id} {\n" .
		                     "\t/* " . __( "CSS styles for the CAPTCHA box", "content-protector" ) . " */\n}\n" .
		                     "#content-protector-ajaxLoading-{id} {\n" .
		                     "\t/* " . __( "CSS styles for the AJAX loading message", "content-protector" ) . " */\n}\n";
		$color_controls    = array(
			"#" . CONTENT_PROTECTOR_HANDLE . "_border_color",
			"#" . CONTENT_PROTECTOR_HANDLE . "_background_color",
			"#" . CONTENT_PROTECTOR_HANDLE . "_form_instructions_color",
			"#" . CONTENT_PROTECTOR_HANDLE . "_ajax_loading_message_color",
			"#" . CONTENT_PROTECTOR_HANDLE . "_form_submit_label_color",
			"#" . CONTENT_PROTECTOR_HANDLE . "_form_submit_button_color",
			"#" . CONTENT_PROTECTOR_HANDLE . "_error_message_color"
		);

		wp_enqueue_script( 'content-protector' . '-admin-js', CONTENT_PROTECTOR_PLUGIN_ASSET_URL . '/assets/js/content-protector-admin.js', array(
			'jquery',
			'jquery-ui-tabs',
			'jquery-ui-accordion',
			'wp-color-picker'
		), CONTENT_PROTECTOR_VERSION );


		$helper = new CP_Helper();

		wp_localize_script( 'content-protector' . '-admin-js',
			'contentProtectorAdminOptions',
			array(
				'theme_colors'              => "['" . join( "','", $helper->getThemeColors() ) . "']",
				'color_controls'            => join( ",", $color_controls ),
				'form_instructions_default' => CONTENT_PROTECTOR_DEFAULT_FORM_INSTRUCTIONS,
				'form_instructions_id'      => '#' . CONTENT_PROTECTOR_HANDLE . '_form_instructions',
				'error_message_default'     => CONTENT_PROTECTOR_DEFAULT_ERROR_MESSAGE,
				'error_message_id'          => '#' . CONTENT_PROTECTOR_HANDLE . '_error_message',
				'form_submit_label_default' => CONTENT_PROTECTOR_DEFAULT_FORM_SUBMIT_LABEL,
				'form_submit_label_id'      => '#' . CONTENT_PROTECTOR_HANDLE . '_form_submit_label',
				'form_css_all_default'      => $css_all_default,
				'form_css_ident_default'    => $css_ident_default,
				'form_css_ident_dialog'     => __( "Enter the Content Protector identifier \nwhose form you want to customize:", "content-protector" ),
				'form_css_id'               => '#' . CONTENT_PROTECTOR_HANDLE . '_form_css'
			) );
	}


	/**
	 * Initialize the Settings page and associated fields.
	 *
	 */
	function initSettingsPage() {

		$plugin_page = add_options_page( __( 'Passster', "content-protector" ), __( 'Content Protector', "content-protector" ), 'manage_options', CONTENT_PROTECTOR_HANDLE, array(
			&$this,
			'drawSettingsPage'
		) );
		add_action( "admin_print_styles-" . $plugin_page, array( &$this, "addAdminHeaderCode" ) );

		add_settings_section( CONTENT_PROTECTOR_HANDLE . '_general_settings_section', __( 'General Settings', "content-protector" ), array(
			&$this,
			'__generalSettingsSectionFieldCallback'
		), CONTENT_PROTECTOR_HANDLE . '_general_settings_subpage' );
		// Add the fields for the General Settings section
		add_settings_field( CONTENT_PROTECTOR_HANDLE . '_encryption_algorithm', __( 'Encryption Algorithm', "content-protector" ), array(
			&$this,
			'__encryptionAlgorithmFieldCallback'
		), CONTENT_PROTECTOR_HANDLE . '_general_settings_subpage', CONTENT_PROTECTOR_HANDLE . '_general_settings_section' );
		add_settings_field( CONTENT_PROTECTOR_HANDLE . '_content_filters', __( 'Default Protected Content Filters', "content-protector" ), array(
			&$this,
			'__contentFiltersFieldCallback'
		), CONTENT_PROTECTOR_HANDLE . '_general_settings_subpage', CONTENT_PROTECTOR_HANDLE . '_general_settings_section' );
		add_settings_field( CONTENT_PROTECTOR_HANDLE . '_other_content_filters', __( 'Other Protected Content Filters', "content-protector" ), array(
			&$this,
			'__otherContentFiltersFieldCallback'
		), CONTENT_PROTECTOR_HANDLE . '_general_settings_subpage', CONTENT_PROTECTOR_HANDLE . '_general_settings_section' );
		add_settings_field( CONTENT_PROTECTOR_HANDLE . '_share_auth', __( 'Shared Authorization', "content-protector" ), array(
			&$this,
			'__shareAuthFieldCallback'
		), CONTENT_PROTECTOR_HANDLE . '_general_settings_subpage', CONTENT_PROTECTOR_HANDLE . '_general_settings_section' );
		add_settings_field( CONTENT_PROTECTOR_HANDLE . '_share_auth_duration', __( 'Shared Authorization Cookie Duration', "content-protector" ), array(
			&$this,
			'__shareAuthDurationFieldCallback'
		), CONTENT_PROTECTOR_HANDLE . '_general_settings_subpage', CONTENT_PROTECTOR_HANDLE . '_general_settings_section' );
		add_settings_field( CONTENT_PROTECTOR_HANDLE . '_store_encrypted_passwords', __( 'Store Encrypted Passwords ', "content-protector" ), array(
			&$this,
			'__storeEncryptedPasswordsFieldCallback'
		), CONTENT_PROTECTOR_HANDLE . '_general_settings_subpage', CONTENT_PROTECTOR_HANDLE . '_general_settings_section' );
		add_settings_field( CONTENT_PROTECTOR_HANDLE . '_delete_options_on_uninstall', __( 'Delete Plugin Options On Uninstall ', "content-protector" ), array(
			&$this,
			'__deleteOptionsOnUninstallFieldCallback'
		), CONTENT_PROTECTOR_HANDLE . '_general_settings_subpage', CONTENT_PROTECTOR_HANDLE . '_general_settings_section' );
		// Register our setting so that $_POST handling is done for us and our callback function just has to echo the HTML
		register_setting( CONTENT_PROTECTOR_HANDLE . '_general_settings_group', CONTENT_PROTECTOR_HANDLE . '_encryption_algorithm', 'esc_attr' );
		register_setting( CONTENT_PROTECTOR_HANDLE . '_general_settings_group', CONTENT_PROTECTOR_HANDLE . '_content_filters', '' );
		register_setting( CONTENT_PROTECTOR_HANDLE . '_general_settings_group', CONTENT_PROTECTOR_HANDLE . '_other_content_filters', '' );
		register_setting( CONTENT_PROTECTOR_HANDLE . '_general_settings_group', CONTENT_PROTECTOR_HANDLE . '_share_auth', '' );
		register_setting( CONTENT_PROTECTOR_HANDLE . '_general_settings_group', CONTENT_PROTECTOR_HANDLE . '_share_auth_duration', 'intval' );
		register_setting( CONTENT_PROTECTOR_HANDLE . '_general_settings_group', CONTENT_PROTECTOR_HANDLE . '_store_encrypted_passwords', '' );
		register_setting( CONTENT_PROTECTOR_HANDLE . '_general_settings_group', CONTENT_PROTECTOR_HANDLE . '_delete_options_on_uninstall', '' );

		add_settings_section( CONTENT_PROTECTOR_HANDLE . '_form_instructions_settings_section', __( 'Form Instructions', "content-protector" ), array(
			&$this,
			'__formInstructionsSettingsSectionFieldCallback'
		), CONTENT_PROTECTOR_HANDLE . '_form_instructions_settings_subpage' );
		// Add the fields for the Form Instructions Settings section
		add_settings_field( CONTENT_PROTECTOR_HANDLE . '_form_instructions', __( 'Instructions Text', "content-protector" ), array(
			&$this,
			'__formInstructionsFieldCallback'
		), CONTENT_PROTECTOR_HANDLE . '_form_instructions_settings_subpage', CONTENT_PROTECTOR_HANDLE . '_form_instructions_settings_section' );
		add_settings_field( CONTENT_PROTECTOR_HANDLE . '_form_instructions_font_weight', __( 'Font Weight', "content-protector" ), array(
			&$this,
			'__formInstructionsFontWeightFieldCallback'
		), CONTENT_PROTECTOR_HANDLE . '_form_instructions_settings_subpage', CONTENT_PROTECTOR_HANDLE . '_form_instructions_settings_section' );
		add_settings_field( CONTENT_PROTECTOR_HANDLE . '_form_instructions_font_size', __( 'Font Size', "content-protector" ), array(
			&$this,
			'__formInstructionsFontSizeFieldCallback'
		), CONTENT_PROTECTOR_HANDLE . '_form_instructions_settings_subpage', CONTENT_PROTECTOR_HANDLE . '_form_instructions_settings_section' );
		add_settings_field( CONTENT_PROTECTOR_HANDLE . '_form_instructions_color', __( 'Text Color', "content-protector" ), array(
			&$this,
			'__formInstructionsColorFieldCallback'
		), CONTENT_PROTECTOR_HANDLE . '_form_instructions_settings_subpage', CONTENT_PROTECTOR_HANDLE . '_form_instructions_settings_section' );
		// Register our setting so that $_POST handling is done for us and our callback function just has to echo the HTML
		register_setting( CONTENT_PROTECTOR_HANDLE . '_form_instructions_settings_group', CONTENT_PROTECTOR_HANDLE . '_form_instructions', '' );
		register_setting( CONTENT_PROTECTOR_HANDLE . '_form_instructions_settings_group', CONTENT_PROTECTOR_HANDLE . '_form_instructions_font_weight', 'esc_attr' );
		register_setting( CONTENT_PROTECTOR_HANDLE . '_form_instructions_settings_group', CONTENT_PROTECTOR_HANDLE . '_form_instructions_font_size', 'esc_attr' );
		register_setting( CONTENT_PROTECTOR_HANDLE . '_form_instructions_settings_group', CONTENT_PROTECTOR_HANDLE . '_form_instructions_color', 'esc_attr' );

		add_settings_section( CONTENT_PROTECTOR_HANDLE . '_error_message_settings_section', __( 'Error Message', "content-protector" ), array(
			&$this,
			'__errorMessageSettingsSectionFieldCallback'
		), CONTENT_PROTECTOR_HANDLE . '_error_message_settings_subpage' );
		// Add the fields for the Error Message Settings section
		add_settings_field( CONTENT_PROTECTOR_HANDLE . '_error_message', __( 'Message Text', "content-protector" ), array(
			&$this,
			'__errorMessageFieldCallback'
		), CONTENT_PROTECTOR_HANDLE . '_error_message_settings_subpage', CONTENT_PROTECTOR_HANDLE . '_error_message_settings_section' );
		add_settings_field( CONTENT_PROTECTOR_HANDLE . '_error_message_font_weight', __( 'Font Weight', "content-protector" ), array(
			&$this,
			'__errorMessageFontWeightFieldCallback'
		), CONTENT_PROTECTOR_HANDLE . '_error_message_settings_subpage', CONTENT_PROTECTOR_HANDLE . '_error_message_settings_section' );
		add_settings_field( CONTENT_PROTECTOR_HANDLE . '_error_message_font_size', __( 'Font Size', "content-protector" ), array(
			&$this,
			'__errorMessageFontSizeFieldCallback'
		), CONTENT_PROTECTOR_HANDLE . '_error_message_settings_subpage', CONTENT_PROTECTOR_HANDLE . '_error_message_settings_section' );
		add_settings_field( CONTENT_PROTECTOR_HANDLE . '_error_message_color', __( 'Text Color', "content-protector" ), array(
			&$this,
			'__errorMessageColorFieldCallback'
		), CONTENT_PROTECTOR_HANDLE . '_error_message_settings_subpage', CONTENT_PROTECTOR_HANDLE . '_error_message_settings_section' );
		// Register our setting so that $_POST handling is done for us and our callback function just has to echo the HTML
		register_setting( CONTENT_PROTECTOR_HANDLE . '_error_message_settings_group', CONTENT_PROTECTOR_HANDLE . '_error_message', 'esc_attr' );
		register_setting( CONTENT_PROTECTOR_HANDLE . '_error_message_settings_group', CONTENT_PROTECTOR_HANDLE . '_error_message_font_weight', 'esc_attr' );
		register_setting( CONTENT_PROTECTOR_HANDLE . '_error_message_settings_group', CONTENT_PROTECTOR_HANDLE . '_error_message_font_size', 'esc_attr' );
		register_setting( CONTENT_PROTECTOR_HANDLE . '_error_message_settings_group', CONTENT_PROTECTOR_HANDLE . '_error_message_color', 'esc_attr' );

		add_settings_section( CONTENT_PROTECTOR_HANDLE . '_form_submit_label_settings_section', __( 'Form Submit Button', "content-protector" ), array(
			&$this,
			'__formSubmitLabelSettingsSectionFieldCallback'
		), CONTENT_PROTECTOR_HANDLE . '_form_submit_label_settings_subpage' );
		// Add the fields for the Form Submit Button Settings section
		add_settings_field( CONTENT_PROTECTOR_HANDLE . '_form_submit_label', __( 'Label Text', "content-protector" ), array(
			&$this,
			'__formSubmitLabelFieldCallback'
		), CONTENT_PROTECTOR_HANDLE . '_form_submit_label_settings_subpage', CONTENT_PROTECTOR_HANDLE . '_form_submit_label_settings_section' );
		add_settings_field( CONTENT_PROTECTOR_HANDLE . '_form_submit_label_color', __( 'Text Color', "content-protector" ), array(
			&$this,
			'__formSubmitLabelColorFieldCallback'
		), CONTENT_PROTECTOR_HANDLE . '_form_submit_label_settings_subpage', CONTENT_PROTECTOR_HANDLE . '_form_submit_label_settings_section' );
		add_settings_field( CONTENT_PROTECTOR_HANDLE . '_form_submit_button_color', __( 'Button Color', "content-protector" ), array(
			&$this,
			'__formSubmitButtonColorFieldCallback'
		), CONTENT_PROTECTOR_HANDLE . '_form_submit_label_settings_subpage', CONTENT_PROTECTOR_HANDLE . '_form_submit_label_settings_section' );
		// Register our setting so that $_POST handling is done for us and our callback function just has to echo the HTML
		register_setting( CONTENT_PROTECTOR_HANDLE . '_form_submit_label_settings_group', CONTENT_PROTECTOR_HANDLE . '_form_submit_label', 'esc_attr' );
		register_setting( CONTENT_PROTECTOR_HANDLE . '_form_submit_label_settings_group', CONTENT_PROTECTOR_HANDLE . '_form_submit_label_color', 'esc_attr' );
		register_setting( CONTENT_PROTECTOR_HANDLE . '_form_submit_label_settings_group', CONTENT_PROTECTOR_HANDLE . '_form_submit_button_color', 'esc_attr' );

		add_settings_section( CONTENT_PROTECTOR_HANDLE . '_form_css_settings_section', __( 'Form CSS', "content-protector" ), array(
			&$this,
			'__formCSSSettingsSectionFieldCallback'
		), CONTENT_PROTECTOR_HANDLE . '_form_css_settings_subpage' );
		// Add the fields for the Form CSS Settings section
		add_settings_field( CONTENT_PROTECTOR_HANDLE . '_css_on_unlocked_content', __( 'Display Form CSS On Unlocked Content', "content-protector" ), array(
			&$this,
			'__formCSSOnUnlockedContentCallback'
		), CONTENT_PROTECTOR_HANDLE . '_form_css_settings_subpage', CONTENT_PROTECTOR_HANDLE . '_form_css_settings_section' );
		add_settings_field( CONTENT_PROTECTOR_HANDLE . '_border_style', __( 'Border Style', "content-protector" ), array(
			&$this,
			'__formBorderStyleFieldCallback'
		), CONTENT_PROTECTOR_HANDLE . '_form_css_settings_subpage', CONTENT_PROTECTOR_HANDLE . '_form_css_settings_section' );
		add_settings_field( CONTENT_PROTECTOR_HANDLE . '_border_color', __( 'Border Color', "content-protector" ), array(
			&$this,
			'__formBorderColorFieldCallback'
		), CONTENT_PROTECTOR_HANDLE . '_form_css_settings_subpage', CONTENT_PROTECTOR_HANDLE . '_form_css_settings_section' );
		add_settings_field( CONTENT_PROTECTOR_HANDLE . '_border_width', __( 'Border Width', "content-protector" ), array(
			&$this,
			'__formBorderWidthFieldCallback'
		), CONTENT_PROTECTOR_HANDLE . '_form_css_settings_subpage', CONTENT_PROTECTOR_HANDLE . '_form_css_settings_section' );
		add_settings_field( CONTENT_PROTECTOR_HANDLE . '_border_radius', __( 'Border Radius', "content-protector" ), array(
			&$this,
			'__formBorderRadiusFieldCallback'
		), CONTENT_PROTECTOR_HANDLE . '_form_css_settings_subpage', CONTENT_PROTECTOR_HANDLE . '_form_css_settings_section' );
		add_settings_field( CONTENT_PROTECTOR_HANDLE . '_padding', __( 'Padding', "content-protector" ), array(
			&$this,
			'__formPaddingFieldCallback'
		), CONTENT_PROTECTOR_HANDLE . '_form_css_settings_subpage', CONTENT_PROTECTOR_HANDLE . '_form_css_settings_section' );
		add_settings_field( CONTENT_PROTECTOR_HANDLE . '_background_color', __( 'Background Color', "content-protector" ), array(
			&$this,
			'__formBackgroundColorFieldCallback'
		), CONTENT_PROTECTOR_HANDLE . '_form_css_settings_subpage', CONTENT_PROTECTOR_HANDLE . '_form_css_settings_section' );
		add_settings_field( CONTENT_PROTECTOR_HANDLE . '_form_css', __( 'Additional CSS', "content-protector" ), array(
			&$this,
			'__formCSSFieldCallback'
		), CONTENT_PROTECTOR_HANDLE . '_form_css_settings_subpage', CONTENT_PROTECTOR_HANDLE . '_form_css_settings_section' );
		// Register our setting so that $_POST handling is done for us and our callback function just has to echo the HTML
		register_setting( CONTENT_PROTECTOR_HANDLE . '_form_css_settings_group', CONTENT_PROTECTOR_HANDLE . '_css_on_unlocked_content', 'esc_attr' );
		register_setting( CONTENT_PROTECTOR_HANDLE . '_form_css_settings_group', CONTENT_PROTECTOR_HANDLE . '_border_style', 'esc_attr' );
		register_setting( CONTENT_PROTECTOR_HANDLE . '_form_css_settings_group', CONTENT_PROTECTOR_HANDLE . '_border_color', 'esc_attr' );
		register_setting( CONTENT_PROTECTOR_HANDLE . '_form_css_settings_group', CONTENT_PROTECTOR_HANDLE . '_border_width', 'esc_attr' );
		register_setting( CONTENT_PROTECTOR_HANDLE . '_form_css_settings_group', CONTENT_PROTECTOR_HANDLE . '_border_radius', 'esc_attr' );
		register_setting( CONTENT_PROTECTOR_HANDLE . '_form_css_settings_group', CONTENT_PROTECTOR_HANDLE . '_padding', 'esc_attr' );
		register_setting( CONTENT_PROTECTOR_HANDLE . '_form_css_settings_group', CONTENT_PROTECTOR_HANDLE . '_background_color', 'esc_attr' );
		register_setting( CONTENT_PROTECTOR_HANDLE . '_form_css_settings_group', CONTENT_PROTECTOR_HANDLE . '_form_css', 'esc_attr' );


		// Register our setting so that $_POST handling is done for us and our callback function just has to echo the HTML
		register_setting( CONTENT_PROTECTOR_HANDLE . '_password_settings_group', CONTENT_PROTECTOR_HANDLE . '_password_field_type', 'esc_attr' );
		register_setting( CONTENT_PROTECTOR_HANDLE . '_password_settings_group', CONTENT_PROTECTOR_HANDLE . '_password_field_placeholder', 'esc_attr' );
		register_setting( CONTENT_PROTECTOR_HANDLE . '_password_settings_group', CONTENT_PROTECTOR_HANDLE . '_password_field_length', 'intval' );
	}


	function __formInstructionsSettingsSectionFieldCallback() {
		_e( "Customize the form instructions on the access forms.", "content-protector" );
	}

	function __formInstructionsFieldCallback() {
		$editor_settings = array( "textarea_rows" => "4" );
		$current_value   = get_option( CONTENT_PROTECTOR_HANDLE . '_form_instructions', CONTENT_PROTECTOR_DEFAULT_FORM_INSTRUCTIONS );

		wp_editor( $current_value, CONTENT_PROTECTOR_HANDLE . '_form_instructions', $editor_settings );
		echo "&nbsp;<a href=\"javascript:;\" id=\"form-instructions-reset\">" . __( "Reset To Default", "content-protector" ) . "</a>";
		echo "<div style=\"clear: both;\"></div>";
		echo __( "Instructions for your access form.", "content-protector" );
		/* translators: %s refers to a CSS class on the access form. */
		echo "<br /><em>" . sprintf( __( "You can manually style this on all access forms using the %s CSS class.", "content-protector" ), "</em><code>div.content-protector-form-instructions</code><em>" ) . "</em>";
	}

	function __formInstructionsFontSizeFieldCallback() {
		$option_values = array_combine( range( 8, 20 ), range( 8, 20 ) );
		$current_value = get_option( CONTENT_PROTECTOR_HANDLE . '_form_instructions_font_size', CONTENT_PROTECTOR_DEFAULT_FONT_SIZE_OPTION );

		echo '<select name="' . CONTENT_PROTECTOR_HANDLE . '_form_instructions_font_size' . '" id="' . CONTENT_PROTECTOR_HANDLE . '_form_instructions_font_size' . '">';
		echo '<option value="0" ' . selected( '0', $current_value, false ) . ' >Default</option>';
		foreach ( $option_values as $value => $label ) {
			echo '<option value="' . $value . '" ' . selected( $value, $current_value, false ) . '>' . $label . ' px</option>';
		}
		echo '</select>';
		echo "<br />" . __( "Font size of the form instructions text.", "content-protector" );
	}

	function __formInstructionsFontWeightFieldCallback() {
		$option_values = array_combine( range( 100, 900, 100 ), range( 100, 900, 100 ) );
		$current_value = get_option( CONTENT_PROTECTOR_HANDLE . '_form_instructions_font_weight', CONTENT_PROTECTOR_DEFAULT_FONT_WEIGHT );

		echo '<select name="' . CONTENT_PROTECTOR_HANDLE . '_form_instructions_font_weight' . '" id="' . CONTENT_PROTECTOR_HANDLE . '_form_instructions_font_weight' . '">';
		foreach ( $option_values as $value => $label ) {
			echo '<option value="' . $value . '" ' . selected( $value, $current_value, false ) . '>' . $label . '</option>';
		}
		echo '</select>';
		echo "<br />" . __( "Font weight of the form instructions text (400 is normal, 700 is bold).", "content-protector" );
	}

	function __formInstructionsColorFieldCallback() {
		$current_value = get_option( CONTENT_PROTECTOR_HANDLE . '_form_instructions_color', "" );
		echo '<input type="text" name="' . CONTENT_PROTECTOR_HANDLE . '_form_instructions_color' . '" id="' . CONTENT_PROTECTOR_HANDLE . '_form_instructions_color' . '" value="' . $current_value . '" size="7" maxlength="7" style="width: 100px;" />';
		echo "<br />" . __( "Color of the form instructions text.", "content-protector" );
	}
	function __errorMessageSettingsSectionFieldCallback() {
		_e( "Customize the message displayed when an incorrect password is entered.", "content-protector" );
	}

	function __errorMessageFieldCallback() {
		$current_value = get_option( CONTENT_PROTECTOR_HANDLE . '_error_message', CONTENT_PROTECTOR_DEFAULT_ERROR_MESSAGE );
		echo '<input type="text" class="regular-text" name="' . CONTENT_PROTECTOR_HANDLE . '_error_message' . '" id="' . CONTENT_PROTECTOR_HANDLE . '_error_message' . '" value="' . $current_value . '" />';
		echo "&nbsp;<a href=\"javascript:;\" id=\"error-message-reset\">" . __( "Reset To Default", "content-protector" ) . "</a>";
		echo "<div style=\"clear: both;\"></div>";
		echo __( "Error message when your users enter an incorrect password.", "content-protector" );
		/* translators: %s refers to a CSS class on the access form. */
		echo "<br /><em>" . sprintf( __( "You can manually style this on all access forms using the %s CSS class.", "content-protector" ), "</em><code>div.content-protector-incorrect-password</code><em>" ) . "</em>";
	}

	function __errorMessageFontSizeFieldCallback() {
		$option_values = array_combine( range( 8, 20 ), range( 8, 20 ) );
		$current_value = get_option( CONTENT_PROTECTOR_HANDLE . '_error_message_font_size', CONTENT_PROTECTOR_DEFAULT_FONT_SIZE_OPTION );

		echo '<select name="' . CONTENT_PROTECTOR_HANDLE . '_error_message_font_size' . '" id="' . CONTENT_PROTECTOR_HANDLE . '_error_message_font_size' . '">';
		echo '<option value="0" ' . selected( '0', $current_value, false ) . ' >Default</option>';
		foreach ( $option_values as $value => $label ) {
			echo '<option value="' . $value . '" ' . selected( $value, $current_value, false ) . '>' . $label . ' px</option>';
		}
		echo '</select>';
		echo "<br />" . __( "Font size of the error message text.", "content-protector" );
	}

	function __errorMessageFontWeightFieldCallback() {
		$option_values = array_combine( range( 100, 900, 100 ), range( 100, 900, 100 ) );
		$current_value = get_option( CONTENT_PROTECTOR_HANDLE . '_error_message_font_weight', CONTENT_PROTECTOR_DEFAULT_FONT_WEIGHT );

		echo '<select name="' . CONTENT_PROTECTOR_HANDLE . '_error_message_font_weight' . '" id="' . CONTENT_PROTECTOR_HANDLE . '_error_message_font_weight' . '">';
		foreach ( $option_values as $value => $label ) {
			echo '<option value="' . $value . '" ' . selected( $value, $current_value, false ) . '>' . $label . '</option>';
		}
		echo '</select>';
		echo "<br />" . __( "Font weight of the error message text (400 is normal, 700 is bold).", "content-protector" );
	}

	function __errorMessageColorFieldCallback() {
		$current_value = get_option( CONTENT_PROTECTOR_HANDLE . '_error_message_color', "" );
		echo '<input type="text" name="' . CONTENT_PROTECTOR_HANDLE . '_error_message_color' . '" id="' . CONTENT_PROTECTOR_HANDLE . '_error_message_color' . '" value="' . $current_value . '" size="7" maxlength="7" style="width: 100px;" />';
		echo "<br />" . __( "Color of the error message text.", "content-protector" );
	}

	function __formSubmitLabelSettingsSectionFieldCallback() {
		_e( "Customize the submit button on the access forms.", "content-protector" );
	}

	function __formSubmitLabelFieldCallback() {
		$current_value = get_option( CONTENT_PROTECTOR_HANDLE . '_form_submit_label', CONTENT_PROTECTOR_DEFAULT_FORM_SUBMIT_LABEL );
		echo '<input type="text" class="regular-text" name="' . CONTENT_PROTECTOR_HANDLE . '_form_submit_label' . '" id="' . CONTENT_PROTECTOR_HANDLE . '_form_submit_label' . '" value="' . $current_value . '" />';
		echo "&nbsp;<a href=\"javascript:;\" id=\"form-submit-reset\">" . __( "Reset To Default", "content-protector" ) . "</a>";
		echo "<div style=\"clear: both;\"></div>";
		echo __( "Customize the submit button label on the form.", "content-protector" );
		/* translators: %s refers to a CSS class on the access form. */
		echo "<br /><em>" . sprintf( __( "You can manually style this on all access forms using the %s CSS class.", "content-protector" ), "</em><code>input.content-protector-form-submit</code><em>" ) . "</em>";
	}

	function __formSubmitLabelColorFieldCallback() {
		$current_value = get_option( CONTENT_PROTECTOR_HANDLE . '_form_submit_label_color', "" );
		echo '<input type="text" name="' . CONTENT_PROTECTOR_HANDLE . '_form_submit_label_color' . '" id="' . CONTENT_PROTECTOR_HANDLE . '_form_submit_label_color' . '" value="' . $current_value . '" size="7" maxlength="7" style="width: 100px;" />';
		echo "<br />" . __( "Color of the form submit label text.", "content-protector" );
	}

	function __formSubmitButtonColorFieldCallback() {
		$current_value = get_option( CONTENT_PROTECTOR_HANDLE . '_form_submit_button_color', "" );
		echo '<input type="text" name="' . CONTENT_PROTECTOR_HANDLE . '_form_submit_button_color' . '" id="' . CONTENT_PROTECTOR_HANDLE . '_form_submit_button_color' . '" value="' . $current_value . '" size="7" maxlength="7" style="width: 100px;" />';
		echo "<br />" . __( "Color of the form submit button.", "content-protector" );
	}


	function __formCSSSettingsSectionFieldCallback() {
		_e( "Customize the overall look-and-feel of your access forms.", "content-protector" );
		/* translators: %s refers to the 'form.content-protector-access-form' CSS class. */
		echo "<br /><em>" . sprintf( __( "You can manually style the overall look of all access forms using the %s CSS class.", "content-protector" ), "</em><code>form.content-protector-access-form</code><em>" ) . "</em>";
	}

	function __formCSSOnUnlockedContentCallback() {
		$current_value = get_option( CONTENT_PROTECTOR_HANDLE . '_css_on_unlocked_content', "1" );
		echo '<input type="checkbox" name="' . CONTENT_PROTECTOR_HANDLE . '_css_on_unlocked_content" id="' . CONTENT_PROTECTOR_HANDLE . '_css_on_unlocked_content" value="1"' . ( ( ( isset( $current_value ) ) && ( $current_value == "1" ) ) ? ' checked="checked"' : '' ) . ' />';
		echo '&nbsp;<label for="' . CONTENT_PROTECTOR_HANDLE . '_css_on_unlocked_content">' . __( "Wrap the form's CSS style around protected content when it's unlocked?.", "content-protector" ) . '</label>';
	}

	function __formBorderStyleFieldCallback() {
		$options       = array( "dotted", "dashed", "solid", "double", "groove", "ridge", "inset", "outset" );
		$option_values = array_combine( $options, $options );
		$current_value = get_option( CONTENT_PROTECTOR_HANDLE . "_border_style", "" );

		echo "<select name=\"" . CONTENT_PROTECTOR_HANDLE . "_border_style\" id=\"" . CONTENT_PROTECTOR_HANDLE . "_border_style\">";
		foreach ( $option_values as $value => $label ) {
			echo '<option value="' . $value . '" ' . selected( $value, $current_value, false ) . ' >' . $label . '</option>';
		}
		echo '</select>';
		echo "<br />" . __( "Border style of the access form.", "content-protector" );
	}

	function __formBorderColorFieldCallback() {
		$current_value = get_option( CONTENT_PROTECTOR_HANDLE . '_border_color', "" );
		echo '<input type="text" name="' . CONTENT_PROTECTOR_HANDLE . '_border_color' . '" id="' . CONTENT_PROTECTOR_HANDLE . '_border_color' . '" value="' . $current_value . '" size="7" maxlength="7" style="width: 100px;" />';
		echo "<br />" . __( "Border color of the access form.", "content-protector" );
	}

	function __formBorderRadiusFieldCallback() {
		$option_values = array_combine( range( 0, 45, 5 ), range( 0, 45, 5 ) );
		$current_value = get_option( CONTENT_PROTECTOR_HANDLE . '_border_radius', "" );

		echo '<select name="' . CONTENT_PROTECTOR_HANDLE . '_border_radius' . '" id="' . CONTENT_PROTECTOR_HANDLE . '_border_radius' . '">';
		foreach ( $option_values as $value => $label ) {
			echo '<option value="' . $value . '" ' . selected( $value, $current_value, false ) . ' >' . $label . ' px</option>';
		}
		echo '</select>';
		echo "<br />" . __( "Border radius (curvature of the corners) of the access form.", "content-protector" );
	}

	function __formBorderWidthFieldCallback() {
		$option_values = array_combine( range( 0, 5 ), range( 0, 5 ) );
		$current_value = get_option( CONTENT_PROTECTOR_HANDLE . '_border_width', "" );

		echo '<select name="' . CONTENT_PROTECTOR_HANDLE . '_border_width' . '" id="' . CONTENT_PROTECTOR_HANDLE . '_border_width' . '">';
		foreach ( $option_values as $value => $label ) {
			echo '<option value="' . $value . '" ' . selected( $value, $current_value, false ) . ' >' . $label . ' px</option>';
		}
		echo '</select>';
		echo "<br />" . __( "Border width of the access form.", "content-protector" );
	}

	function __formPaddingFieldCallback() {
		$option_values = array_combine( range( 0, 25, 5 ), range( 0, 25, 5 ) );
		$current_value = get_option( CONTENT_PROTECTOR_HANDLE . '_padding', "" );

		echo '<select name="' . CONTENT_PROTECTOR_HANDLE . '_padding' . '" id="' . CONTENT_PROTECTOR_HANDLE . '_padding' . '">';
		foreach ( $option_values as $value => $label ) {
			echo '<option value="' . $value . '" ' . selected( $value, $current_value, false ) . ' >' . $label . ' px</option>';
		}
		echo '</select>';
		echo "<br />" . __( "Padding inside the border of the access form.", "content-protector" );
	}

	function __formBackgroundColorFieldCallback() {
		$current_value = get_option( CONTENT_PROTECTOR_HANDLE . '_background_color', "" );
		echo '<input type="text" name="' . CONTENT_PROTECTOR_HANDLE . '_background_color' . '" id="' . CONTENT_PROTECTOR_HANDLE . '_background_color' . '" value="' . $current_value . '" size="7" maxlength="7" style="width: 100px;" />';
		echo "<br />" . __( "Background color of the access form.", "content-protector" );
	}

	function __formCSSFieldCallback() {
		$current_value = get_option( CONTENT_PROTECTOR_HANDLE . '_form_css', "" );
		echo '<textarea style="vertical-align: top; float: left;" rows="12" cols="70" class="regular-text" name="' . CONTENT_PROTECTOR_HANDLE . '_form_css' . '" id="' . CONTENT_PROTECTOR_HANDLE . '_form_css' . '">' . $current_value . '</textarea>';
		echo "&nbsp;<a href=\"javascript:;\" id=\"form-css-all\">" . __( "Add CSS scaffolding for all access forms", "content-protector" ) . "</a>";
		echo "<br />&nbsp;<a href=\"javascript:;\" id=\"form-css-ident\">" . __( "Add CSS scaffolding for a specific access form", "content-protector" ) . "</a>";
		echo "<br />&nbsp;<a href=\"javascript:;\" id=\"form-css-reset\">" . _x( "Clear", "Clear the textarea", "content-protector" ) . "</a>";
		echo "<div style=\"clear: both;\"></div>";
		echo __( "Apply custom CSS to your access form.", "content-protector" );
		echo " <strong>" . __( "Knowledge of CSS required.", "content-protector" ) . "</strong>";
	}

	function __generalSettingsSectionFieldCallback() {
		echo __( "Control how your content is protected.", "content-protector" );
	}

	function __encryptionAlgorithmFieldCallback() {
		$option_values = array(
			"CRYPT_STD_DES"  => _x( "Standard DES", "Encryption algorithm", "content-protector" ),
			"CRYPT_EXT_DES"  => _x( "Extended DES", "Encryption algorithm", "content-protector" ),
			"CRYPT_MD5"      => _x( "MD5", "Encryption algorithm", "content-protector" ),
			"CRYPT_BLOWFISH" => _x( "Blowfish", "Encryption algorithm", "content-protector" ),
			"CRYPT_SHA256"   => _x( "SHA-256", "Encryption algorithm", "content-protector" ),
			"CRYPT_SHA512"   => _x( "SHA-512", "Encryption algorithm", "content-protector" )
		);
		$current_value = get_option( CONTENT_PROTECTOR_HANDLE . "_encryption_algorithm", CONTENT_PROTECTOR_DEFAULT_ENCRYPTION_ALGORITHM );

		echo "<select name=\"" . CONTENT_PROTECTOR_HANDLE . "_encryption_algorithm\" id=\"" . CONTENT_PROTECTOR_HANDLE . "_encryption_algorithm\">";
		foreach ( $option_values as $value => $label ) {
			if ( ( defined( $value ) ) && ( constant( $value ) === 1 ) ) {
				echo '<option value="' . $value . '" ' . selected( $value, $current_value, false ) . '>' . $label . '</option>';
			}
		}
		echo '</select>';
		echo "<p>" . __( "Select the encryption algorithm to encrypt the password for your protected content. Only those algorithms supported by your server are listed.", "content-protector" ) . "</p>";
		/* translators: %1$s refers to 'URL for PHP's crypt() man page (if available, please change this to link to the URL for your translated language).'; %2$s refers to 'Link text for PHP's crypt() man page (language-specific)'. */
		echo "<p>" . sprintf( __( 'More info at <a href="%1$s">%2$s</a>.', "content-protector" ),
				_x( "http://www.php.net/manual/en/function.crypt.php", "URL for PHP's crypt() man page (if available, please change this to link to the URL for your translated language).", "content-protector" ),
				_x( "PHP's crypt() man page", "Link text for PHP's crypt() man page (language-specific)", "content-protector" ) ) . "</p>";
	}

	function __contentFiltersFieldCallback() {
		echo "<p>" . __( "Select which default filters to use on protected content that has been unlocked.", "content-protector" ) . "</p>";
		$current_values = get_option( CONTENT_PROTECTOR_HANDLE . '_content_filters', $this->default_content_filters );
		echo '<input type="checkbox" name="' . CONTENT_PROTECTOR_HANDLE . '_content_filters[wptexturize]" id="' . CONTENT_PROTECTOR_HANDLE . '_content_filters_wptexturize" value="1"' . ( ( ( isset( $current_values['wptexturize'] ) ) && ( $current_values['wptexturize'] == "1" ) ) ? ' checked="checked"' : '' ) . ' />';
		echo '<label for="' . CONTENT_PROTECTOR_HANDLE . '_content_filters_wptexturize"><code>wptexturize</code></label><br />';
		echo '<input type="checkbox" name="' . CONTENT_PROTECTOR_HANDLE . '_content_filters[convert_smilies]" id="' . CONTENT_PROTECTOR_HANDLE . '_content_filters_convert_smilies" value="1"' . ( ( ( isset( $current_values['convert_smilies'] ) ) && ( $current_values['convert_smilies'] == "1" ) ) ? ' checked="checked"' : '' ) . ' />';
		echo '<label for="' . CONTENT_PROTECTOR_HANDLE . '_content_filters_convert_smilies"><code>convert_smilies</code></label><br />';
		echo '<input type="checkbox" name="' . CONTENT_PROTECTOR_HANDLE . '_content_filters[convert_chars]" id="' . CONTENT_PROTECTOR_HANDLE . '_content_filters_convert_chars" value="1"' . ( ( ( isset( $current_values['convert_chars'] ) ) && ( $current_values['convert_chars'] == "1" ) ) ? ' checked="checked"' : '' ) . ' />';
		echo '<label for="' . CONTENT_PROTECTOR_HANDLE . '_content_filters_convert_chars"><code>convert_chars</code></label><br />';
		echo '<input type="checkbox" name="' . CONTENT_PROTECTOR_HANDLE . '_content_filters[wpautop]" id="' . CONTENT_PROTECTOR_HANDLE . '_content_filters_wpautop" value="1"' . ( ( ( isset( $current_values['wpautop'] ) ) && ( $current_values['wpautop'] == "1" ) ) ? ' checked="checked"' : '' ) . ' />';
		echo '<label for="' . CONTENT_PROTECTOR_HANDLE . '_content_filters_wpautop"><code>wpautop</code></label><br />';
		echo '<input type="checkbox" name="' . CONTENT_PROTECTOR_HANDLE . '_content_filters[prepend_attachment]" id="' . CONTENT_PROTECTOR_HANDLE . '_content_filters_prepend_attachment" value="1"' . ( ( ( isset( $current_values['prepend_attachment'] ) ) && ( $current_values['prepend_attachment'] == "1" ) ) ? ' checked="checked"' : '' ) . ' />';
		echo '<label for="' . CONTENT_PROTECTOR_HANDLE . '_content_filters_prepend_attachment"><code>prepend_attachment</code></label><br />';
		echo '<input type="checkbox" name="' . CONTENT_PROTECTOR_HANDLE . '_content_filters[do_shortcode]" id="' . CONTENT_PROTECTOR_HANDLE . '_content_filters_do_shortcode" value="1"' . ( ( ( isset( $current_values['do_shortcode'] ) ) && ( $current_values['do_shortcode'] == "1" ) ) ? ' checked="checked"' : '' ) . ' />';
		echo '<label for="' . CONTENT_PROTECTOR_HANDLE . '_content_filters_do_shortcode"><code>do_shortcode</code></label><br />';
		echo "<p>" . sprintf( __( "These filters will be added to the %s filter, applied when content is unlocked.", "content-protector" ), "<code>content_protector_unlocked_content</code>" ) . "</p>";
	}

	function __otherContentFiltersFieldCallback() {
		$current_value = get_option( CONTENT_PROTECTOR_HANDLE . '_other_content_filters', "" );
		$placeholder   = __( "Example: filter_1,filter_2,filter_3", "content-protector" );
		echo "<p>" . sprintf( __( "Specify any other filters to use to use on protected content that has been unlocked. Separate each filter name with %s.", "content-protector" ), "<code>,</code>" ) . "</p>";
		echo '<input type="text" class="regular-text" name="' . CONTENT_PROTECTOR_HANDLE . '_other_content_filters' . '" id="' . CONTENT_PROTECTOR_HANDLE . '_other_content_filters' . '" value="' . $current_value . '" placeholder="' . $placeholder . '" />';
		echo "<p>" . sprintf( __( "These filters will be added to the %s filter, applied when content is unlocked.", "content-protector" ), "<code>content_protector_unlocked_content</code>" ) . "</p>";
	}

	function __shareAuthFieldCallback() {
		echo "<p>" . __( "If checked, sets a cookie to share authorization among protected content sections if the sections share specific properties.", "content-protector" ) . "</p>";
		$current_values = get_option( CONTENT_PROTECTOR_HANDLE . '_share_auth', array() );
		echo '<input type="checkbox" name="' . CONTENT_PROTECTOR_HANDLE . '_share_auth[same_page]" id="' . CONTENT_PROTECTOR_HANDLE . '_share_auth_same_page" value="1"' . ( ( ( isset( $current_values['same_page'] ) ) && ( $current_values['same_page'] == "1" ) ) ? ' checked="checked"' : '' ) . ' />';
		echo '<label for="' . CONTENT_PROTECTOR_HANDLE . '_share_auth_same_page">' . __( "Share authorization for protected content that share the same Post/Page and Password", "content-protector" ) . '</label><br />';
		echo '<input type="checkbox" name="' . CONTENT_PROTECTOR_HANDLE . '_share_auth[same_identifier]" id="' . CONTENT_PROTECTOR_HANDLE . '_share_auth_same_identifier" value="1"' . ( ( ( isset( $current_values['same_identifier'] ) ) && ( $current_values['same_identifier'] == "1" ) ) ? ' checked="checked"' : '' ) . ' />';
		echo '<label for="' . CONTENT_PROTECTOR_HANDLE . '_share_auth_same_identifier">' . __( "Share authorization for protected content that share the same Identifier and Password", "content-protector" ) . '</label><br />';
		echo "<p>" . __( "NOTE: Visitors must successfully log into one matching protected content section in order to automatically access the others.", "content-protector" ) . "</p>";
	}

	function __shareAuthDurationFieldCallback() {
		$current_value = get_option( CONTENT_PROTECTOR_HANDLE . '_share_auth_duration', CONTENT_PROTECTOR_DEFAULT_SHARE_AUTH_DURATION );
		echo '<input type="text" name="' . CONTENT_PROTECTOR_HANDLE . '_share_auth_duration' . '" id="' . CONTENT_PROTECTOR_HANDLE . '_share_auth_duration' . '" value="' . $current_value . '" size="7" style="width: 100px;" />';
		echo "<p>" . __( "Duration (in seconds) for any shared authorization cookies.  Once a shared authorization cookie expires, any cookies previously set for individual protected content sections in the group will be referenced instead.", "content-protector" ) . "</p>";
	}

	function __storeEncryptedPasswordsFieldCallback() {
		$current_value = get_option( CONTENT_PROTECTOR_HANDLE . '_store_encrypted_passwords', "1" );
		echo '<input type="checkbox" name="' . CONTENT_PROTECTOR_HANDLE . '_store_encrypted_passwords" id="' . CONTENT_PROTECTOR_HANDLE . '_store_encrypted_passwords" value="1" ' . checked( 1, get_option( CONTENT_PROTECTOR_HANDLE . '_store_encrypted_passwords' ), false ) . ' />';
		echo "&nbsp;<label for='" . CONTENT_PROTECTOR_HANDLE . "_store_encrypted_passwords'>" . __( "If checked, encrypted passwords are kept in Transient storage for quicker lookup.", "content-protector" ) . "</label><br />";
		if ( $current_value == "1" ) {
			$password_hashes = get_transient( 'content_protector_password_hashes' );
			if ( is_array( $password_hashes ) ) {
				$text = sprintf( _n( '%d password is stored in the database.', '%d passwords are stored in the database.', count( $password_hashes ), "content-protector" ), count( $password_hashes ) );
				echo "<p><em>" . $text . "</em></p>";
			}
		}
	}


// Function to clear or initialize passwords transients if "Store Encrypted Passwords" is unchecked
	function updateEncryptedPasswordsTransients( $option, $old_value, $value ) {
		if ( $option == CONTENT_PROTECTOR_HANDLE . '_store_encrypted_passwords' ) {
			if ( $old_value != $value ) {
				if ( "1" == $value ) {
					set_transient( 'content_protector_password_hashes', array(), 1 * HOUR_IN_SECONDS );
					$message = __( "Encrypted Passwords Storage enabled", "content-protector" );
				} else {
					delete_transient( 'content_protector_password_hashes' );
					$message = __( "Encrypted Passwords Storage disabled", "content-protector" );
				}
				add_settings_error(
					CONTENT_PROTECTOR_HANDLE . '_store_encrypted_passwords',
					'updateEncryptedPasswordsTransients',
					$message,
					"updated" );
			}
		}
	}

	function __deleteOptionsOnUninstallFieldCallback() {
		$current_value = get_option( CONTENT_PROTECTOR_HANDLE . '_delete_options_on_uninstall', "" );
		echo '<input type="checkbox" name="' . CONTENT_PROTECTOR_HANDLE . '_delete_options_on_uninstall" id="' . CONTENT_PROTECTOR_HANDLE . '_delete_options_on_uninstall" value="1" ' . checked( 1, get_option( CONTENT_PROTECTOR_HANDLE . '_delete_options_on_uninstall' ), false ) . ' />';
		echo "&nbsp;<label for='" . CONTENT_PROTECTOR_HANDLE . "_delete_options_on_uninstall'>" . __( "If checked, all plugin options will be deleted if the plugin is unstalled.", "content-protector" ) . "</label><br />";
	}


	function __passwordFieldTypeFieldCallback() {
		echo "<p>" . __( "Select the HTML form element to use when you use a regular password for your forms.", "content-protector" ) . "</p>";
		$current_value = get_option( CONTENT_PROTECTOR_HANDLE . '_password_field_type', CONTENT_PROTECTOR_DEFAULT_PASSWORD_FIELD_TYPE );
		echo '<input type="radio" name="' . CONTENT_PROTECTOR_HANDLE . '_password_field_type" id="' . CONTENT_PROTECTOR_HANDLE . '_password_field_type_password" value="password"' . ( ( ( isset( $current_value ) ) && ( $current_value == "password" ) ) ? ' checked="checked"' : '' ) . ' />';
		echo '<label for="' . CONTENT_PROTECTOR_HANDLE . '_password_field_type_password">' . _x( "Password", "HTML element", "content-protector" ) . '</label><br />';
		echo '<input type="radio" name="' . CONTENT_PROTECTOR_HANDLE . '_password_field_type" id="' . CONTENT_PROTECTOR_HANDLE . '_password_field_type_text" value="text"' . ( ( ( isset( $current_value ) ) && ( $current_value == "text" ) ) ? ' checked="checked"' : '' ) . ' />';
		echo '<label for="' . CONTENT_PROTECTOR_HANDLE . '_password_field_type_text">' . _x( "Text", "HTML element", "content-protector" ) . '</label><br />';
	}


	function __passwordFieldPlaceholderFieldCallback() {
		$current_value = get_option( CONTENT_PROTECTOR_HANDLE . '_password_field_placeholder', CONTENT_PROTECTOR_DEFAULT_PASSWORD_FIELD_PLACEHOLDER );
		echo '<input type="text" name="' . CONTENT_PROTECTOR_HANDLE . '_password_field_placeholder' . '" id="' . CONTENT_PROTECTOR_HANDLE . '_password_field_placeholder' . '" value="' . $current_value . '" />';
		echo "<p>" . __( "Set a placeholder to use when you use a regular password for your forms.", "content-protector" ) . "</p>";
	}

	function __passwordFieldLengthFieldCallback() {
		$current_value = get_option( CONTENT_PROTECTOR_HANDLE . '_password_field_length', CONTENT_PROTECTOR_DEFAULT_PASSWORD_FIELD_LENGTH );
		echo '<input type="text" name="' . CONTENT_PROTECTOR_HANDLE . '_password_field_length' . '" id="' . CONTENT_PROTECTOR_HANDLE . '_password_field_length' . '" value="' . $current_value . '" size="2" maxlength="2" />';
		echo "<p>" . __( "Set the character length for the HTML password/text element when you use a regular password for your forms.", "content-protector" ) . "</p>";
	}


}