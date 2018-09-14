<?php
/**
 * Represents the header for the admin page
 *
 * @package   Quick_Featured_Images
 * @author    Martin Stehle <m.stehle@gmx.de>
 * @license   GPL-2.0+
 * @link      http://stehle-internet.de
 * @copyright 2014 Martin Stehle
 */
 ?>

<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
	<?php include_once 'options-head.php'; // print out success or error messages from the Settings API ?>
	<div class="qfi_wrapper">
		<div id="qfi_main">
			<div class="qfi_content">
			