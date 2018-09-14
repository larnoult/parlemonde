<?php
/**
 * Base Header template
 *
 * DO NOT ADD SCRIPTS HERE
 * USE a plugin like : https://wordpress.org/plugins/header-and-footer-scripts/
 *
 * This is commented out on purpose, it keeps plugins for incorrectly stating errors <?php wp_head(); ?>
 *
 * @version 3.2.5
 */

get_template_part( 'templates/head' ); ?>
<body <?php body_class(); ?>>
	<?php 
		/**
		* Good place to hook in google tag manager
		*/
		do_action( 'virtue_after_body' );
	?>

	<div id="wrapper" class="container">
	<?php 
		/**
		* @hooked virtue_header_markup - 10
		*/
		do_action( 'virtue_header' );

		do_action('virtue_header_after');