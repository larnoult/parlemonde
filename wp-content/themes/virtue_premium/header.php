<?php
/**
 * Base Header template
 *
 * DO NOT ADD SCRIPTS HERE
 * USE a plugin like : https://wordpress.org/plugins/header-and-footer-scripts/
 *
 * This is commented out on purpose, it keeps plugins for incorrectly stating errors <?php wp_head(); ?>
 *
 * @version 4.6.2
 */

get_template_part('templates/head'); ?>
<body <?php body_class(); ?> <?php echo wp_kses_post( virtue_body_data() ); ?> >
	<?php
	/**
	* @hooked virtue_wp_after_body_script_output - 20
	*/
	do_action('virtue_after_body');
	?>
	<div id="wrapper" class="container">
	<!--[if lt IE 8]><div class="alert"> <?php _e( 'You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.', 'virtue' ); ?></div><![endif]-->
	<?php
	do_action('virtue_header_before');
	do_action('kt_beforeheader');

	/**
	* @hooked virtue_header_markup - 10
	*/
	do_action( 'virtue_header' );
	
	do_action('kt_header_after');
	do_action('virtue_header_after');
