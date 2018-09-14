<?php

class CP_Public {

	/**
	 * CP_Public constructor.
	 */
	public function __construct() {
		add_shortcode( CONTENT_PROTECTOR_SHORTCODE, array( $this, "processShortcode" ), 1 );
		add_action( "wp_head", array( $this, "addHeaderCode" ), 99 );
	}


	/**
	 * @param $atts
	 * @param null $content
	 *
	 * @return string
	 */
	public function processShortcode( $atts, $content = null ) {
		// Get the ID of the current post
		global $post;
		$post_id        = $post->ID;
		$post_permalink = get_permalink( $post_id );


		extract( shortcode_atts( array(
			'password'       => "",
			'cookie_expires' => "",
			'identifier'     => "",
			'ajax'           => "false"
		), $atts ) );

		$isAuthorized           = false;
		$successMessage         = "";
		$cookie_name            = CONTENT_PROTECTOR_COOKIE_ID . md5( $atts['identifier'] . $post_permalink );
		$share_auth             = get_option( CONTENT_PROTECTOR_HANDLE . '_share_auth', array() );
		$share_auth_cookie_name = CONTENT_PROTECTOR_COOKIE_ID . "share_auth";
		if ( ! empty( $share_auth ) ) {
			if ( ( isset( $share_auth['same_identifier'] ) ) && ( $share_auth['same_identifier'] == "1" ) ) {
				$share_auth_cookie_name .= "_" . md5( $atts['identifier'] );
			}
			if ( ( isset( $share_auth['same_page'] ) ) && ( $share_auth['same_page'] == "1" ) ) {
				$share_auth_cookie_name .= "_" . md5( $post_permalink );
			}
		}
		$password_field = ( isset( $_POST['content-protector-password'] ) ? $_POST['content-protector-password'] : "" );

		// Authorization by group cookie
		if ( ( ! empty( $share_auth ) ) && ( isset( $_COOKIE[ $share_auth_cookie_name ] ) ) && ( $_COOKIE[ $share_auth_cookie_name ] == md5( $atts['password'] ) ) ) {
			$isAuthorized = true;
		} // ...or authorization by individual cookie
        elseif ( ( isset( $_COOKIE[ $cookie_name ] ) ) && ( $_COOKIE[ $cookie_name ] == md5( $atts['password'] . $atts['cookie_expires'] . $atts['identifier'] . $post_permalink ) ) ) {
			$isAuthorized = true;
		} elseif ( ( ( isset( $_POST['content-protector-password'] ) ) && ( isset( $_POST['content-protector-token'] ) ) )
		           && ( crypt( $password_field, $_POST['content-protector-token'] ) == $_POST['content-protector-token'] )
		           && ( ( isset( $_POST['content-protector-ident'] ) ) && ( $_POST['content-protector-ident'] === $atts['identifier'] ) )
		) {
			$isAuthorized = true;
			// We only want to see this on initial authorization, not whenever the cookie authorizes you
			$success_message_display = get_option( CONTENT_PROTECTOR_HANDLE . '_success_message_display', "0" );
			if ( $success_message_display == "1" ) {
				$successMessage = "<div id=\"content-protector-correct-password" . $atts['identifier'] . "\" class=\"content-protector-correct-password\">" . get_option( CONTENT_PROTECTOR_HANDLE . '_success_message', CONTENT_PROTECTOR_DEFAULT_SUCCESS_MESSAGE ) . "</div>";
			} else {
				$successMessage = "";
			}
		}
		$css_on_unlocked_content = ( 1 == get_option( CONTENT_PROTECTOR_HANDLE . '_css_on_unlocked_content', "1" ) ? true : false );

		if ( $isAuthorized ) {
			//error_log(has_filter( "content_protector_unlocked_content", "" ) );
			return "<div id=\"content-protector" . $atts['identifier'] . "\"" . ( $css_on_unlocked_content ? " class=\"content-protector-access-form\"" : "" ) . ">" . $successMessage . str_replace( ']]>', ']]&gt;', apply_filters( "content_protector_unlocked_content", $content ) ) . "</div>";
		} else {
			$password_field_type   = get_option( CONTENT_PROTECTOR_HANDLE . '_password_field_type', CONTENT_PROTECTOR_DEFAULT_PASSWORD_FIELD_TYPE );
			$password_field_length = get_option( CONTENT_PROTECTOR_HANDLE . '_password_field_length', CONTENT_PROTECTOR_DEFAULT_PASSWORD_FIELD_LENGTH );
			$placeholder           = get_option( CONTENT_PROTECTOR_HANDLE . '_password_field_placeholder', CONTENT_PROTECTOR_DEFAULT_PASSWORD_FIELD_PLACEHOLDER );
		}
		$incorrect_password_message = get_option( CONTENT_PROTECTOR_HANDLE . '_error_message', CONTENT_PROTECTOR_DEFAULT_ERROR_MESSAGE );

		$form_instructions    = str_replace( ']]>', ']]&gt;', apply_filters( "the_content", get_option( CONTENT_PROTECTOR_HANDLE . '_form_instructions', CONTENT_PROTECTOR_DEFAULT_FORM_INSTRUCTIONS ) ) );

		$form_submit_label = get_option( CONTENT_PROTECTOR_HANDLE . '_form_submit_label', CONTENT_PROTECTOR_DEFAULT_FORM_SUBMIT_LABEL );

		$utility       = new CP_Utility();
		$password_hash = $utility->hashPassword( $atts['password'] );

		ob_start();

		require_once( CONTENT_PROTECTOR_PLUGIN_URL . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . 'access_form.php' );

		$the_form = ob_get_contents();
		ob_end_clean();

		return $the_form;
	}


	/**
	 *
	 */
	public function addHeaderCode() {
		wp_enqueue_style( 'content-protector' . '_css', CONTENT_PROTECTOR_PLUGIN_ASSET_URL . '/assets/css/content-protector.css', CONTENT_PROTECTOR_VERSION );
		if ( ! is_admin() ) { ?>
            <!-- Content Protector plugin v. <?php echo CONTENT_PROTECTOR_VERSION; ?> CSS -->
            <style type="text/css">
                div.content-protector-access-form {
                <?php if ( false !== get_option( CONTENT_PROTECTOR_HANDLE . '_padding' ) ) { ?> padding: <?php echo get_option( CONTENT_PROTECTOR_HANDLE . '_padding' ); ?>px;
                <?php } ?><?php if ( false !== get_option( CONTENT_PROTECTOR_HANDLE . '_border_style' ) ) { ?> border-style: <?php echo get_option( CONTENT_PROTECTOR_HANDLE . '_border_style' ); ?>;
                <?php } ?><?php if ( ( false !== get_option( CONTENT_PROTECTOR_HANDLE . '_border_color' ) ) && ( strlen( trim( get_option( CONTENT_PROTECTOR_HANDLE . '_border_color' ) ) ) > 0 ) ) { ?> border-color: <?php echo get_option( CONTENT_PROTECTOR_HANDLE . '_border_color' ); ?>;
                <?php } ?><?php if ( false !== get_option( CONTENT_PROTECTOR_HANDLE . '_border_width' ) ) { ?> border-width: <?php echo get_option( CONTENT_PROTECTOR_HANDLE . '_border_width' ); ?>px;
                <?php } ?><?php if ( false !== get_option( CONTENT_PROTECTOR_HANDLE . '_border_radius' ) ) { ?> border-radius: <?php echo get_option( CONTENT_PROTECTOR_HANDLE . '_border_radius' ); ?>px;
                <?php } ?><?php if ( ( false !== get_option( CONTENT_PROTECTOR_HANDLE . '_background_color' ) ) && ( strlen( trim( get_option( CONTENT_PROTECTOR_HANDLE . '_background_color' ) ) ) > 0 ) ) { ?> background-color: <?php echo get_option( CONTENT_PROTECTOR_HANDLE . '_background_color' ); ?>;
                <?php } ?>
                }

                input.content-protector-form-submit {
                <?php if ( ( false !== get_option( CONTENT_PROTECTOR_HANDLE . '_form_submit_label_color' ) ) && ( strlen( trim( get_option( CONTENT_PROTECTOR_HANDLE . '_form_submit_label_color' ) ) ) > 0 ) ) { ?> color: <?php echo get_option( CONTENT_PROTECTOR_HANDLE . '_form_submit_label_color' ); ?>;
                <?php } ?><?php if ( ( false !== get_option( CONTENT_PROTECTOR_HANDLE . '_form_submit_button_color' ) ) && ( strlen( trim( get_option( CONTENT_PROTECTOR_HANDLE . '_form_submit_button_color' ) ) ) > 0 ) ) { ?> background-color: <?php echo get_option( CONTENT_PROTECTOR_HANDLE . '_form_submit_button_color' ); ?>;
                <?php } ?>
                }

                div.content-protector-correct-password {
                <?php if ( ( false !== get_option( CONTENT_PROTECTOR_HANDLE . '_success_message_color' ) ) && ( strlen( trim( get_option( CONTENT_PROTECTOR_HANDLE . '_success_message_color' ) ) ) > 0 ) ) { ?> color: <?php echo get_option( CONTENT_PROTECTOR_HANDLE . '_success_message_color' ); ?>;
                <?php } ?><?php if ( ( false !== get_option( CONTENT_PROTECTOR_HANDLE . '_success_message_font_size' ) ) && ( get_option( CONTENT_PROTECTOR_HANDLE . '_success_message_font_size' ) > 0 ) ) { ?> font-size: <?php echo get_option( CONTENT_PROTECTOR_HANDLE . '_success_message_font_size' ); ?>px;
                <?php } ?><?php if ( false !== get_option( CONTENT_PROTECTOR_HANDLE . '_success_message_font_weight' ) ) { ?> font-weight: <?php echo get_option( CONTENT_PROTECTOR_HANDLE . '_success_message_font_weight' ); ?>;
                <?php } ?>
                }

                div.content-protector-incorrect-password {
                <?php if ( ( false !== get_option( CONTENT_PROTECTOR_HANDLE . '_error_message_color' ) ) && ( strlen( trim( get_option( CONTENT_PROTECTOR_HANDLE . '_error_message_color' ) ) ) > 0 ) ) { ?> color: <?php echo get_option( CONTENT_PROTECTOR_HANDLE . '_error_message_color' ); ?>;
                <?php } ?><?php if ( ( false !== get_option( CONTENT_PROTECTOR_HANDLE . '_error_message_font_size' ) ) && ( get_option( CONTENT_PROTECTOR_HANDLE . '_error_message_font_size' ) > 0 ) ) { ?> font-size: <?php echo get_option( CONTENT_PROTECTOR_HANDLE . '_error_message_font_size' ); ?>px;
                <?php } ?><?php if ( false !== get_option( CONTENT_PROTECTOR_HANDLE . '_error_message_font_weight' ) ) { ?> font-weight: <?php echo get_option( CONTENT_PROTECTOR_HANDLE . '_error_message_font_weight' ); ?>;
                <?php } ?>
                }

                div.content-protector-ajaxLoading {
                <?php if ( ( false !== get_option( CONTENT_PROTECTOR_HANDLE . '_ajax_loading_message_color' ) ) && ( strlen( trim( get_option( CONTENT_PROTECTOR_HANDLE . '_ajax_loading_message_color' ) ) ) > 0 ) ) { ?> color: <?php echo get_option( CONTENT_PROTECTOR_HANDLE . '_ajax_loading_message_color' ); ?>;
                <?php } ?><?php if ( ( false !== get_option( CONTENT_PROTECTOR_HANDLE . '_ajax_loading_message_font_style' ) ) && ( strlen( trim( get_option( CONTENT_PROTECTOR_HANDLE . '_ajax_loading_message_font_style' ) ) ) > 0 ) ) { ?> font-style: <?php echo get_option( CONTENT_PROTECTOR_HANDLE . '_ajax_loading_message_font_style' ); ?>;
                <?php } ?><?php if ( false !== get_option( CONTENT_PROTECTOR_HANDLE . '_ajax_loading_message_font_weight' ) ) { ?> font-weight: <?php echo get_option( CONTENT_PROTECTOR_HANDLE . '_ajax_loading_message_font_weight' ); ?>;
                <?php } ?>
                }

                div.content-protector-form-instructions {
                <?php if ( ( false !== get_option( CONTENT_PROTECTOR_HANDLE . '_form_instructions_color' ) ) && ( strlen( trim( get_option( CONTENT_PROTECTOR_HANDLE . '_form_instructions_color' ) ) ) > 0 ) ) { ?> color: <?php echo get_option( CONTENT_PROTECTOR_HANDLE . '_form_instructions_color' ); ?>;
                <?php } ?><?php if ( ( false !== get_option( CONTENT_PROTECTOR_HANDLE . '_form_instructions_font_size' ) ) && ( get_option( CONTENT_PROTECTOR_HANDLE . '_form_instructions_font_size' ) > 0 ) ) { ?> font-size: <?php echo get_option( CONTENT_PROTECTOR_HANDLE . '_form_instructions_font_size' ); ?>px;
                <?php } ?><?php if ( false !== get_option( CONTENT_PROTECTOR_HANDLE . '_form_instructions_font_weight' ) ) { ?> font-weight: <?php echo get_option( CONTENT_PROTECTOR_HANDLE . '_form_instructions_font_weight' ); ?>;
                <?php } ?>
                }
            </style>
		<?php }
		$css = get_option( CONTENT_PROTECTOR_HANDLE . '_form_css', "" );
		if ( ( ! is_admin() ) && ( strlen( trim( $css ) ) > 0 ) ) { ?>
            <!-- Content Protector plugin v. <?php echo CONTENT_PROTECTOR_VERSION; ?> Additional CSS -->
            <style type="text/css">
                <?php echo $css; ?>
            </style>
		<?php }
	}


}