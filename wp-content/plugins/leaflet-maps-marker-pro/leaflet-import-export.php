<?php
/*
    Import/Export marker - Maps Marker Pro
*/
//info prevent file from being accessed directly
if (basename($_SERVER['SCRIPT_FILENAME']) == 'leaflet-import-export.php') { die ("Please do not access this file directly. Thanks!<br/><a href='https://www.mapsmarker.com/go'>www.mapsmarker.com</a>"); }

include('inc' . DIRECTORY_SEPARATOR . 'admin-header.php');
global $wpdb, $current_user;
$lmm_options = get_option( 'leafletmapsmarker_options' );
$lmm_base_url = MMP_Rewrite::get_base_url();
$lmm_slug = MMP_Rewrite::get_slug();
$action = isset($_POST['action']) ? $_POST['action'] : '';

if (!empty($action)) {
	$import_export_nonce = isset($_POST['_wpnonce']) ? $_POST['_wpnonce'] : (isset($_GET['_wpnonce']) ? $_GET['_wpnonce'] : '');
	if (! wp_verify_nonce($import_export_nonce, 'import-export-nonce') ) die('<br/>'.__('Security check failed - please call this function from the according admin page!','lmm').'');
	$import_export_standalone_nonce = wp_create_nonce('import-export-standalone-nonce');
	require_once( LEAFLET_PLUGIN_DIR . 'inc' . DIRECTORY_SEPARATOR . 'class-mmp-geocoding.php' );
	if ($action == 'import') {
		echo '<iframe name="import" src="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/import-export/?action_iframe=import&_wpnonce=' . $import_export_standalone_nonce) . '" width="100%" height="850" marginwidth="0" marginheight="0"></iframe>'.PHP_EOL;
	} else if ($action == 'import-layers') {
		echo '<iframe name="import-layers" src="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/import-export/?action_iframe=import-layers&_wpnonce=' . $import_export_standalone_nonce) . '" width="100%" height="850" marginwidth="0" marginheight="0"></iframe>'.PHP_EOL;
	} elseif ($action == 'export') {
		echo '<iframe name="export" src="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/import-export/?action_iframe=export&_wpnonce=' . $import_export_standalone_nonce) . '" width="100%" height="850" marginwidth="0" marginheight="0"></iframe>'.PHP_EOL;
	} elseif ($action == 'export-layers') {
		echo '<iframe name="export-layers" src="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/import-export/?action_iframe=export-layers&_wpnonce=' . $import_export_standalone_nonce) . '" width="100%" height="850" marginwidth="0" marginheight="0"></iframe>'.PHP_EOL;
	}
} else { //info: !empty($action) 2/3
?>
	<?php //info: PHPExcel requirement checks
	if (!extension_loaded('xml')) {
		echo '<div class="notice notice-error" style="padding:10px;">' . sprintf(__('The PHP extension %1$s is not enabled on your server - this means that import and export will not work! Please contact your admin for more details.','lmm'), 'php_xml') . '</div>';
	}
	if (!function_exists('gd_info')) {
		echo '<div class="notice notice-error" style="padding:10px;">' . sprintf(__('The PHP extension %1$s is not enabled on your server - this means that import and export will not work! Please contact your admin for more details.','lmm'), 'php_gd2') . '</div>';
	}
	?>

	<h1><?php _e('Import/Export','lmm'); ?></h1>

	<p><?php echo sprintf(__('For details and tutorials about imports and exports, please visit %1s','lmm'), '<a href="https://www.mapsmarker.com/import-export" target="_blank" style="text-decoration:none;">www.mapsmarker.com/import-export</a>'); ?></p>

	<p>
	<?php
	if ( current_user_can( $lmm_options[ 'capabilities_edit_others' ]) ) {
		$submit_disabled = '';
		$submit_disabled_info = '';
		if ( (!extension_loaded('xml')) || (!function_exists('gd_info')) ) {
			$submit_disabled = 'disabled="disabled"';
		}
	} else {
		$submit_disabled = 'disabled="disabled"';
		$submit_disabled_info = '<div class="notice notice-warning" style="padding:10px;">' . sprintf(__('Your user does not have the WordPress capabilities needed (%1$s) - please contact your admin.','lmm'), $lmm_options[ 'capabilities_edit_others' ]) . '</div>';
	}
	$nonce = wp_create_nonce('import-export-nonce');
	?>

	<table>
		<tr>
			<td colspan="2"><span style="font-weight:bold;"><?php _e('Import markers','lmm'); ?></span></td>
			<td style="width:50px;"></td>
			<td colspan="2"><span style="font-weight:bold;"><?php _e('Import layers','lmm'); ?></span></td>
		</tr>
		<tr>
			<td style="width:35px;"><img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-import.png" width="32" height="32" alt="import"></td>
			<td>
				<form method="post">
				<?php wp_nonce_field('import-export-nonce'); ?>
				<input type="hidden" name="action" value="import" />
				<input style="font-weight:bold;" type="submit" name="submit" class="button button-primary" value="<?php esc_attr_e('prepare import','lmm'); ?>" <?php echo $submit_disabled; ?> />
				</form>
				<?php echo $submit_disabled_info; ?>
			</td>
			<td></td>
			<td style="width:35px;"><img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-import.png" width="32" height="32" alt="import"></td>
			<td>
				<form method="post">
				<?php wp_nonce_field('import-export-nonce'); ?>
				<input type="hidden" name="action" value="import-layers" />
				<input style="font-weight:bold;" type="submit" name="submit" class="button button-primary" value="<?php esc_attr_e('prepare import','lmm'); ?>" <?php echo $submit_disabled; ?> />
				</form>
				<?php echo $submit_disabled_info; ?>
			</td>
		</tr>
		<tr>
			<td colspan="2" style="font-weight:bold;"><?php _e('Export markers','lmm'); ?></td>
			<td></td>
			<td colspan="2"><span style="font-weight:bold;"><?php _e('Export layers','lmm'); ?></span></td>
		</tr>
		<tr>
			<td style="width:35px;"><img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-export.png" width="32" height="32" alt="export"></td>
			<td>
				<form method="post">
				<?php wp_nonce_field('import-export-nonce'); ?>
				<input type="hidden" name="action" value="export" />
				<input style="font-weight:bold;" type="submit" name="submit" class="button button-primary" value="<?php esc_attr_e('prepare export','lmm'); ?>" <?php echo $submit_disabled; ?> />
				</form>
				<?php echo $submit_disabled_info; ?>
			</td>
			<td></td>
			<td style="width:35px;"><img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-export.png" width="32" height="32" alt="export"></td>
			<td>
				<form method="post">
				<?php wp_nonce_field('import-export-nonce'); ?>
				<input type="hidden" name="action" value="export-layers" />
				<input style="font-weight:bold;" type="submit" name="submit" class="button button-primary" value="<?php esc_attr_e('prepare export','lmm'); ?>" <?php echo $submit_disabled; ?> />
				</form>
				<?php echo $submit_disabled_info; ?>
			</td>
		</tr>
	</table>
<?php
} //info: !empty($action) 3/3
include('inc' . DIRECTORY_SEPARATOR . 'admin-footer.php');
