<?php
/**
 * Represents the header for the admin page
 *
 * @package   Quick_Featured_Images
 * @author    Martin Stehle <m.stehle@gmx.de>
 * @license   GPL-2.0+
 * @link      http://stehle-internet.de
 * @copyright 2013 Martin Stehle
 */
 ?>

<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
	<h2><?php esc_html_e( 'Progress bar', 'quick-featured-images' ); ?></h2>
	<p id="progress">
		<em class="screen-reader-text"><?php esc_html_e( 'You are here', 'quick-featured-images' ); ?>:</em>
		<span id="bar" class="wp-ui-primary">
<?php 
$count = 1;
$max = sizeof( $this->valid_steps );
foreach ( $this->valid_steps as $key => $label ) {
	if ( $this->selected_step == $key ) {
		$elem = 'strong';
		$class = 'wp-ui-highlight';
	} else {
		$elem = 'span'; 
		$class = 'wp-ui-notification';
	}
	printf( '<%s class="%s">%s</%s>', $elem, $class, esc_html( $label ), $elem );
	if ( $count < $max ) {
		echo '<span class="sep"> &gt; </span>';
	}
	$count++;
}
?>
	</span>
</p>

<div class="qfi_wrapper">
	<div id="qfi_main">
		<div class="qfi_content">
