<?php
/*
Plugin Name: BuddyPress NoCaptcha Register Box
Plugin URI: http://www.functionsphp.com/recaptcha
Description: This super-lightweight plugin adds a Google's No Captcha human friendly reCAPTCHA box to the BuddyPress registration form. 
It should help keep your community free from spambots and also hopefully not be too much of a inconvenience for your sites genuine users.
You can set a dark or light theme. The type of captcha can be set to image or audio and there are over 30 languages available for the NoCaptcha box. Or, set it to auto, based on the users browser language.
(Plugin code based on Buddypress ReCaptacha by Hardeep Asrani, modified for latest reCaptcha API.)
Version: 1.1.3
Author: Neil Foster
Author URI: http://www.mokummusic.com
Requires at least: WordPress 2.8, BuddyPress 1.2.9
License: GPL2
*/

// -----------------Set up the plug-in -------------------
defined('ABSPATH') or die("No script kiddies please!");

function enqueue_mokum_nocaptcha_scripts() {

	if (get_option('mmbpcapt_lang') != '' && get_option('mmbpcapt_lang') !== 'xx') $lang = '&hl='.get_option('mmbpcapt_lang');

	wp_register_script('googleRecaptchaScript', 'https://www.google.com/recaptcha/api.js?onload=onloadCaptchaCallback&render=explicit'.$lang, '','', true);
	if (function_exists('bp_is_register_page') && bp_is_register_page()) wp_enqueue_script('googleRecaptchaScript');
}
add_action( 'wp_enqueue_scripts', 'enqueue_mokum_nocaptcha_scripts');


// Make recaptcha script load asynchronously
add_filter( 'script_loader_tag', function ( $tag, $handle ) {
	if ( 'googleRecaptchaScript' !== $handle ) return $tag;
	return str_replace( ' src', ' async="async" defer="defer" src', $tag );
}, 10, 2 );


// ********** ADMIN SETTINGS FUNCTIONS **************

function mmbpcapt_init() {
	register_setting( 'mmbpcapt', 'mmbpcapt_public' );
	register_setting( 'mmbpcapt', 'mmbpcapt_private' );
	register_setting( 'mmbpcapt', 'mmbpcapt_theme' );
	register_setting( 'mmbpcapt', 'mmbpcapt_type' );
	register_setting( 'mmbpcapt', 'mmbpcapt_lang' );
	register_setting( 'mmbpcapt', 'mmbpcapt_style' );
	add_option( 'mmbpcapt_public');
	add_option( 'mmbpcapt_private');
	add_option( 'mmbpcapt_theme', 'light');
	add_option( 'mmbpcapt_type', 'image');
	add_option( 'mmbpcapt_lang', 'xx');
	add_option( 'mmbpcapt_style', 'clear:both; float:right; margin-top:30px;');
}
add_action('admin_init', 'mmbpcapt_init' );

function mmbpcapt_register_options_page() {
	add_options_page('BuddyPress noCaptcha', 'BuddyPress noCaptcha', 'manage_options', 'bp-captcha', 'mmbpcapt_options_page');
}
add_action('admin_menu', 'mmbpcapt_register_options_page');

function mmbpcapt_options_page() {
	include (plugin_dir_path( __FILE__ ).'admin-settings-template.php');
}

// ********** FRONT END **************

add_action( 'bp_before_registration_submit_buttons', 'bp_add_code' );
add_action( 'bp_signup_validate', 'bp_validate' );

function bp_add_code() {
	global $bp;
	?>

	<div class="register-section" id="security-section" style="<?php echo get_option('mmbpcapt_style'); ?>">
		<div class="editfield">
			<?php if (!empty($bp->signup->errors['recaptcha_response_field'])) : ?>
				<div class="error"><?php echo $bp->signup->errors['recaptcha_response_field']; ?></div>
			<?php endif; ?>

			<div id="mm-nocaptcha"></div>

			<?php if (get_option('mmbpcapt_public') == null || get_option('mmbpcapt_public') == '') echo "Enter your reCAPTCHA API keys in Wordpress admin settings!"; ?>

		</div>
	</div>
	<script type="text/javascript">
		var onloadCaptchaCallback = function() {
			grecaptcha.render('mm-nocaptcha', {
				'sitekey' : '<?php echo get_option('mmbpcapt_public'); ?>',
				'theme' : '<?php echo get_option('mmbpcapt_theme'); ?>',
				'type' : '<?php echo get_option('mmbpcapt_type'); ?>'
			});
		};
	</script>
	<?php
}

function bp_validate() {
	global $bp;

	if (get_option('mmbpcapt_private') == null || get_option('mmbpcapt_private') == '') {
		die ("To use reCAPTCHA you must get an API key from <a href='https://www.google.com/recaptcha/admin/create'>https://www.google.com/recaptcha/admin/create</a>");
	}

	if ($_SERVER['REMOTE_ADDR'] == null || $_SERVER['REMOTE_ADDR'] == '') {
		die ("For security reasons, you must pass the remote ip to reCAPTCHA");
	}

	$query = array(
		'secret' => get_option('mmbpcapt_private'),
		'response' => $_POST['g-recaptcha-response'],
		'remoteip' => $_SERVER['REMOTE_ADDR']
		);

	$url = 'https://www.google.com/recaptcha/api/siteverify';
	$request = new WP_Http;
	$result = $request->request( $url, array( 'method' => 'POST', 'body' => $query) );

	$response = $result['response'];
	$body = json_decode( $result['body']);

	if ($response['message'] != 'OK' || $body->success != true) {
		foreach ($body->{'error-codes'} as $error_code) {
			if ($error_code == 'missing-input-response') {
				$error_string .= 'You must prove you are human. ';
			} else {
				$error_string .= 'There was an error ('.$error_code.') in reCaptcha. ';
			}	
		}
		$bp->signup->errors['recaptcha_response_field'] = $error_string;
	}

	return;
}
