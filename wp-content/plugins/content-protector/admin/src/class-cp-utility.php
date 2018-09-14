<?php

class CP_Utility {

	/**
	 * CP_Utility constructor.
	 */
	public function __construct() {
		add_action( "wp", array( $this, "setCookie" ), 1 );
	}

	/**
	 * Generate a randomized salt for use in contentProtectorPlugin::__hashPassword()
	 *
	 * @param int $length Length of the salt requested
	 *
	 * @return string       The salt
	 */
	public function generateRandomSalt( $length = 2 ) {
		$valid_chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789./";
		$salt        = "";
		for ( $i = 0; $i < $length; $i ++ ) {
			$salt .= substr( str_shuffle( $valid_chars ), mt_rand( 0, strlen( $valid_chars ) - 1 ), 1 );
		}

		return $salt;
	}

	/**
	 * Creates a hash of the password from the [content_protector] shortcode, using the encryption
	 * algorithm set in the "content_protector_encryption_algorithm" option
	 *
	 * @param string $pw Password
	 *
	 * @return string       The hashed password
	 */
	public function hashPassword( $pw = "" ) {
		$encryption_algorithm = get_option( CONTENT_PROTECTOR_HANDLE . 'encryption_algorithm', CONTENT_PROTECTOR_DEFAULT_ENCRYPTION_ALGORITHM );
		$salt                 = "";

		$store_encrypted_passwords = ( ( "1" == get_option( CONTENT_PROTECTOR_HANDLE . '_store_encrypted_passwords', "1" ) ) ? true : false );
		if ( $store_encrypted_passwords ) {
			if ( false === ( $password_hashes = get_transient( 'content_protector_password_hashes' ) ) ) {
				// It wasn't there, so regenerate the data and save the transient
				$password_hashes = array();
			}
			$password_hashes_idx = $pw . "|" . $encryption_algorithm;
			if ( isset( $password_hashes[ $password_hashes_idx ] ) ) {
				return $password_hashes[ $password_hashes_idx ];
			}
		}
		switch ( $encryption_algorithm ) {
			case "CRYPT_STD_DES" :
				$salt = $this->generateRandomSalt( 2 );
				break;
			case "CRYPT_EXT_DES" :
				$salt = '_' . $this->generateRandomSalt( 8 );
				break;
			case "CRYPT_MD5" :
				$salt = '$1$' . $this->generateRandomSalt( 8 ) . '$';
				break;
			case "CRYPT_BLOWFISH" :
				$prefix = ( version_compare( PHP_VERSION, '5.3.7', '>=' ) ? '$2y$' : '$2a$' );
				$cost   = sprintf( "%02d", mt_rand( 12, 15 ) );
				$salt   = $prefix . $cost . '$' . $this->generateRandomSalt( 22 ) . '$';
				break;
			case "CRYPT_SHA256" :
				$cost = mt_rand( 5000, 20000 );
				$salt = '$5$rounds=' . $cost . '$' . $this->generateRandomSalt( 16 ) . '$';
				break;
			case "CRYPT_SHA512" :
				$cost = mt_rand( 5000, 20000 );
				$salt = '$6$rounds=' . $cost . '$' . $this->generateRandomSalt( 16 ) . '$';
				break;
			default :
				$salt = "";
		}
		$password_hash = crypt( $pw, $salt );
		if ( $store_encrypted_passwords ) {
			$password_hashes[ $password_hashes_idx ] = $password_hash;
			set_transient( 'content_protector_password_hashes', $password_hashes, 1 * HOUR_IN_SECONDS );
		}

		return $password_hash;
	}

	/**
	 * Creates a cookie on the user's computer so they won't necessarily need to re-enter the password on every visit.
	 */
	public function setCookie() {
		global $post;

		if ( isset( $post ) ) {
			$the_post_id = $post->ID;
		} elseif ( isset( $_POST['post_id'] ) ) {
			$the_post_id = $_POST['post_id'];
		} else {
			return;
		}

		$is_captcha = ( ( isset( $_POST['content-protector-captcha'] ) ) && ( (int) $_POST['content-protector-captcha'] == 1 ) );

		$post_permalink = get_permalink( $the_post_id );
		$password_field = ( isset( $_POST['content-protector-password'] ) ? $_POST['content-protector-password'] : "" );
		if ( $is_captcha && ( 1 == get_option( CONTENT_PROTECTOR_HANDLE . '_captcha_case_insensitive', "0" ) ) ) {
			$password_field = strtoupper( $password_field );
		}


		if ( ( isset( $_POST['content-protector-submit'] ) )
		     && ( isset( $_POST['content-protector-expires'] ) )
		     && ( crypt( $password_field, $_POST['content-protector-token'] ) == $_POST['content-protector-token'] )
		) {
			if ( ! is_int( $_POST['content-protector-expires'] ) ) {
				$expires = strtotime( $_POST['content-protector-expires'] );
			} elseif ( (int) $_POST['content-protector-expires'] <> 0 ) {
				$expires = time() + (int) $_POST['content-protector-expires'];
			} else {
				$expires = 0;
			}

			if ( $is_captcha ) {
				$cookie_name = CONTENT_PROTECTOR_COOKIE_ID . md5( $_POST['content-protector-ident'] . $post_permalink . "_captcha" );
				$cookie_val  = md5( $_POST['content-protector-expires'] . $_POST['content-protector-ident'] . $post_permalink );
				setcookie( $cookie_name, $cookie_val, $expires, COOKIEPATH, COOKIE_DOMAIN );
			} else {
				$cookie_name = CONTENT_PROTECTOR_COOKIE_ID . md5( $_POST['content-protector-ident'] . $post_permalink );
				$cookie_val  = md5( $password_field . $_POST['content-protector-expires'] . $_POST['content-protector-ident'] . $post_permalink );
				setcookie( $cookie_name, $cookie_val, $expires, COOKIEPATH, COOKIE_DOMAIN );
				$share_auth = get_option( CONTENT_PROTECTOR_HANDLE . '_share_auth', array() );
				if ( ! empty( $share_auth ) ) {
					$share_auth_cookie_name = CONTENT_PROTECTOR_COOKIE_ID . "share_auth";
					if ( ( isset( $share_auth['same_identifier'] ) ) && ( $share_auth['same_identifier'] == "1" ) ) {
						$share_auth_cookie_name .= "_" . md5( $_POST['content-protector-ident'] );
					}
					if ( ( isset( $share_auth['same_page'] ) ) && ( $share_auth['same_page'] == "1" ) ) {
						$share_auth_cookie_name .= "_" . md5( $post_permalink );
					}
					$share_auth_cookie_expires = time() + get_option( CONTENT_PROTECTOR_HANDLE . '_share_auth_duration', CONTENT_PROTECTOR_DEFAULT_SHARE_AUTH_DURATION );
					setcookie( $share_auth_cookie_name, md5( $password_field ), $share_auth_cookie_expires, COOKIEPATH, COOKIE_DOMAIN );
				}
			}

		}
	}


}