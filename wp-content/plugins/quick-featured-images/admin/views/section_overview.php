<?php
/**
 * Options Page
 *
 * @package   Quick_Featured_Images_Admin
 * @author    Martin Stehle <m.stehle@gmx.de>
 * @license   GPL-2.0+
 * @link      http://wordpress.org/plugins/quick-featured-images/
 * @copyright 2014 
 */

$qfi_tools_instance    = Quick_Featured_Images_Tools::get_instance();
$qfi_defaults_instance = Quick_Featured_Images_Defaults::get_instance();
$qfi_settings_instance = Quick_Featured_Images_Settings::get_instance();
?>

<h2 class="no-bottom"><?php esc_html_e( 'Manage featured images in a quick way', 'quick-featured-images' ); ?></h2>
<div class="qfi_page_description">
	<p><?php echo esc_html( $this->get_page_description() ); ?></p>
</div>
<ul>
<?php
	/** 
	 * Bulk Edit Page Item
	 *
	 */
?>
	<li>
		<h3><?php echo esc_html( $qfi_tools_instance->get_page_headline() ); ?></h3>
<?php
if ( current_user_can( $qfi_tools_instance->get_required_user_cap() ) ) {
	printf( 
		'		<p><a href="%s"><span class="dashicons dashicons-admin-tools"></span><br />%s</a></p>',
		esc_url( admin_url( sprintf( 'admin.php?page=%s', $qfi_tools_instance->get_page_slug() ) ) ),
		esc_html( $qfi_tools_instance->get_page_description() )
	);
} else {
?>
		<p><span class="dashicons dashicons-admin-tools"></span><br /><?php echo esc_html( $qfi_tools_instance->get_page_description() ); ?></p>
<?php
}
?>
	</li>
<?php
	/** 
	 * Presets Page Item
	 *
	 */
?>
	<li>
		<h3><?php echo esc_html( $qfi_defaults_instance->get_page_headline() ); ?></h3>
<?php
if ( current_user_can( $qfi_defaults_instance->get_required_user_cap() ) ) {
	printf( 
		'						<p><a href="%s"><span class="dashicons dashicons-images-alt"></span><br />%s</a></p>',
		esc_url( admin_url( sprintf( 'admin.php?page=%s', $qfi_defaults_instance->get_page_slug() ) ) ),
		esc_html( $qfi_defaults_instance->get_page_description() )
	);
} else {
?>
		<p><span class="dashicons dashicons-admin-defaults"></span><br /><?php echo esc_html( $qfi_defaults_instance->get_page_description() ); ?></p>
<?php
}
?>
	</li>
<?php
	/** 
	 * Image Columns Page Item
	 *
	 */
?>
	<li>
		<h3><?php echo esc_html( $qfi_settings_instance->get_page_headline() ); ?></h3>
<?php
if ( current_user_can( $qfi_settings_instance->get_required_user_cap() ) ) {
	printf( 
		'						<p><a href="%s"><span class="dashicons dashicons-admin-settings"></span><br />%s</a></p>', 	
		esc_url( admin_url( sprintf( 'admin.php?page=%s', $qfi_settings_instance->get_page_slug() ) ) ), 
		esc_html( $qfi_settings_instance->get_page_description() )
	);
} else {
?>
		<p><span class="dashicons dashicons-admin-settings"></span><br /><?php echo esc_html( $qfi_settings_instance->get_page_description() ); ?></p>
<?php
}
?>
	</li>
	<li>
		<h3><?php esc_html_e( 'The premium version', 'quick-featured-images' ); ?></h3>
		<p><a href="https://www.quickfeaturedimages.com<?php esc_attr_e( '/', 'quick-featured-images' ); ?>"><img alt="Quick Featured Images Pro" src="<?php echo plugin_dir_url( dirname( dirname( __FILE__ ) ) ); ?>admin/assets/images/logo_qfi_pro.gif" style="width:100%;height:auto;"></a></p>
		<p><?php esc_html_e( 'Are you looking for more options and more filters?', 'quick-featured-images' ); ?> <?php esc_html_e( 'Get the premium version', 'quick-featured-images' ); ?> <a href="https://www.quickfeaturedimages.com<?php esc_attr_e( '/', 'quick-featured-images' ); ?>">Quick Featured Images Pro</a>.</p>
	</li>
</ul>

