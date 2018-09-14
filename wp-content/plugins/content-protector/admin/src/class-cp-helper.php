<?php

class CP_Helper {


	/**
	 * @param string $password
	 * @param string $identifier
	 * @param string $post_id
	 * @param string $cookie_expires
	 *
	 * @return bool
	 */
	public function content_protector_is_logged_in( $password = "", $identifier = "", $post_id = "", $cookie_expires = "" ) {
		if ( trim( $password ) == "" || trim( $identifier ) == "" || trim( $post_id == "" ) ) {
			return false;
		}

		$ident          = md5( $identifier );
		$post_permalink = get_permalink( $post_id );

		$captcha        = ( strtoupper( $password ) === CONTENT_PROTECTOR_CAPTCHA_KEYWORD ? true : false );
		$password_field = ( isset( $_POST['content-protector-password'] ) ? $_POST['content-protector-password'] : "" );

		// Cookies from CAPTCHA protected content are built differently from cookies from password protected content
		if ( ! $captcha ) {
			$cookie_name    = CONTENT_PROTECTOR_COOKIE_ID . md5( $ident . $post_permalink );
			$is_live_cookie = ( ( isset( $_COOKIE[ $cookie_name ] ) ) && ( $_COOKIE[ $cookie_name ] == md5( $password . $cookie_expires . $ident . $post_permalink ) ) );
			if ( 1 == get_option( CONTENT_PROTECTOR_HANDLE . '_captcha_case_insensitive', "0" ) ) {
				$password_field == strtoupper( $password_field );
			}
		} else {
			$cookie_name    = CONTENT_PROTECTOR_COOKIE_ID . md5( $ident . $post_permalink . "_captcha" );
			$is_live_cookie = ( ( isset( $_COOKIE[ $cookie_name ] ) ) && ( $_COOKIE[ $cookie_name ] == md5( $cookie_expires . $ident . $post_permalink ) ) );
		}


		return ( $is_live_cookie ||
		         ( ( ( isset( $_POST['content-protector-password'] ) ) && ( isset( $_POST['content-protector-token'] ) ) )
		           && ( crypt( $password_field, $_POST['content-protector-token'] ) == $_POST['content-protector-token'] )
		           && ( ( isset( $_POST['content-protector-ident'] ) ) && ( $_POST['content-protector-ident'] === $ident ) ) ) );
	}

	// Credit: http://bavotasan.com/2011/convert-hex-color-to-rgb-using-php/

	/**
	 * @param $hex
	 *
	 * @return array
	 */
	function __hex2rgb( $hex ) {
		$hex = str_replace( "#", "", $hex );

		if ( strlen( $hex ) == 3 ) {
			$r = hexdec( substr( $hex, 0, 1 ) . substr( $hex, 0, 1 ) );
			$g = hexdec( substr( $hex, 1, 1 ) . substr( $hex, 1, 1 ) );
			$b = hexdec( substr( $hex, 2, 1 ) . substr( $hex, 2, 1 ) );
		} elseif ( strlen( $hex ) == 6 ) {
			$r = hexdec( substr( $hex, 0, 2 ) );
			$g = hexdec( substr( $hex, 2, 2 ) );
			$b = hexdec( substr( $hex, 4, 2 ) );
		} else {  // Set to black
			$r = 0;
			$g = 0;
			$b = 0;
		}
		$rgb = array( $r, $g, $b );

		//return implode( ", ",  $rgb ); // returns the rgb values separated by commas
		return $rgb; // returns an array with the rgb values
	}

	/**
	 * Gets the colors from the active Theme's stylesheet (style.css)
	 *
	 * @return array    Array of colors in hexadecimal notation
	 */
	function getThemeColors() {
		$colors     = array();
		$stylesheet = file_get_contents( get_stylesheet_directory() . "/style.css" );
		preg_match_all( "/\#[a-fA-F0-9]{3,6}/", $stylesheet, $matches, PREG_SET_ORDER );
		foreach ( $matches as $m ) {
			$colors[] = $m[0];
		}
		sort( $colors );

		return array_unique( $colors );
	}
}