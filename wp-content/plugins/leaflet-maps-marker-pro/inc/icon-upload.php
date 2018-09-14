<?php
//info prevent file from being accessed directly
if (basename($_SERVER['SCRIPT_FILENAME']) == 'icon-upload.php') { die ("Please do not access this file directly. Thanks!<br/><a href='https://www.mapsmarker.com/go'>www.mapsmarker.com</a>"); }
header('Content-Type: text/html; charset=UTF-8');

//info: security check
global $current_user;
$wpnonceicon = isset($_GET['_wpnonceicon']) ? $_GET['_wpnonceicon'] : '';
if ( (!wp_verify_nonce($wpnonceicon, 'icon-upload-nonce')) || (!current_user_can('upload_files')) ) { die(__('Security check failed - please call this function from the according admin page!','lmm').''); };
?>
<!DOCTYPE html>
<html>
<head>
<title><?php esc_attr_e('upload new icon','lmm'); ?></title>
<style type="text/css" media="screen">
.button-primary {
    -moz-text-blink: none;
    -moz-text-decoration-color: inherit;
    -moz-text-decoration-line: none;
    -moz-text-decoration-style: solid;
	background: #21759B linear-gradient(to bottom, #2A95C5, #21759B);
	border-bottom-color: #1E6A8D;
    border-left-color-ltr-source: physical;
    border-left-color-rtl-source: physical;
    border-left-color-value: #21759B;
    border-right-color-ltr-source: physical;
    border-right-color-rtl-source: physical;
    border-right-color-value: #21759B;
    border-top-color: #21759B;
    box-shadow: 0 1px 0 rgba(120, 200, 230, 0.5) inset;
    color: #FFFFFF;
    text-shadow: 0 1px 0 rgba(0, 0, 0, 0.1);
    -moz-box-sizing: border-box;
	border-bottom-style: solid;
    border-bottom-width: 1px;
    border-left-style-ltr-source: physical;
    border-left-style-rtl-source: physical;
    border-left-style-value: solid;
    border-left-width-ltr-source: physical;
    border-left-width-rtl-source: physical;
    border-left-width-value: 1px;
    border-right-style-ltr-source: physical;
    border-right-style-rtl-source: physical;
    border-right-style-value: solid;
    border-right-width-ltr-source: physical;
    border-right-width-rtl-source: physical;
    border-right-width-value: 1px;
	border-radius: 3px;
	border-top-style: solid;
    border-top-width: 1px;
    cursor: pointer;
    display: inline-block;
    font-size: 12px;
    height: 24px;
    line-height: 23px;
	margin: 0;
	padding-bottom: 1px;
    padding-left: 10px;
    padding-right: 10px;
    padding-top: 0;
    white-space: nowrap;
    border-bottom-color: #DFDFDF;
    border-left-color-ltr-source: physical;
    border-left-color-rtl-source: physical;
    border-left-color-value: #DFDFDF;
    border-right-color-ltr-source: physical;
    border-right-color-rtl-source: physical;
    border-right-color-value: #DFDFDF;
    border-top-color: #DFDFDF;
}
</style>
</head>
<body style="margin-left:18px;">
<?php
//info: get info for custom marker icon dir/url
$lmm_options = get_option( 'leafletmapsmarker_options' );
if ( $lmm_options['defaults_marker_custom_icon_url_dir'] == 'no' ) {
	$defaults_marker_icon_dir = LEAFLET_PLUGIN_ICONS_DIR;
	$defaults_marker_icon_url = LEAFLET_PLUGIN_ICONS_URL;

	if (is_writeable($defaults_marker_icon_dir)) {

		function lmm_set_upload_dir_icon_upload( $upload ) {
			$lmm_options = get_option( 'leafletmapsmarker_options' );
			if ( $lmm_options['defaults_marker_custom_icon_url_dir'] == 'no' ) {
				$defaults_marker_icon_dir = LEAFLET_PLUGIN_ICONS_DIR;
				$defaults_marker_icon_url = LEAFLET_PLUGIN_ICONS_URL;
			} else {
				$defaults_marker_icon_dir = htmlspecialchars($lmm_options['defaults_marker_icon_dir']);
				$defaults_marker_icon_url = esc_url($lmm_options['defaults_marker_icon_url']);
			}
			$upload['subdir'] = '';
			$upload['path'] = $defaults_marker_icon_dir;
			$upload['url'] = $defaults_marker_icon_url;
			return $upload;
		}
		add_filter( 'upload_dir', 'lmm_set_upload_dir_icon_upload' );

		echo '<p>' . sprintf(__('New icons will be uploaded to the following directory: %1$s','lmm'), '<br/>' . $defaults_marker_icon_url) . '</p>';
		echo '<p>' . sprintf(__('Please select icon to upload (allowed file types: %1$s)','lmm'), 'png, gif, jpg');
		echo '<form enctype="multipart/form-data" action="" method="post" style="margin:7px 0 16px 0;">';
		echo '<input type="hidden" name="MAX_FILE_SIZE" value="10000000" />';
		echo '<input type="file" name="uploadFile"/>';
		echo '<input type="submit" name="upload-submit" class="button-primary" value="' . esc_attr__('upload','lmm') . '"/>';
		echo '</form>';
		if ( isset($_FILES['uploadFile']['name']) && ($_FILES['uploadFile']['name'] == TRUE) ){
			if ( ! function_exists( 'wp_handle_upload' ) ) {
				require_once( ABSPATH . 'wp-admin' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'file.php' );
			}
			$uploadedfile = $_FILES['uploadFile'];
			$upload_overrides = array( 'test_form' => false, 'mimes' => array('jpg'=>'image/jpeg','gif'=>'image/gif','png'=>'image/png') );
			$movefile = wp_handle_upload( $uploadedfile, $upload_overrides );
			if (!isset($movefile['error'])) {
				echo '<span style="color:green;font-weight:bold;">' . sprintf(__('Upload successful - <a href="%1$s" target="_top">please reload page</a>','lmm'), LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker') . '</span>';
			} else {
				echo '<span style="color:red;font-weight:bold;">' . $movefile['error'] . '</span>';
			}
		}
	} else {
		echo '<span style="color:red;font-weight:bold;">' . sprintf(__('Marker icon directory %s is not writable - please set permissions via FTP with CHMOD command to 755','lmm'), '<br/>' . $defaults_marker_icon_dir) . '</span>';
	}
} else {
	$defaults_marker_icon_dir = htmlspecialchars($lmm_options['defaults_marker_icon_dir']);
	echo '<span style="color:red;font-weight:bold;">' . __('Due to security restrictions icon upload to a custom icon directory is not allowed!','lmm') . '</span><br/><br/>' . sprintf(__('Please use a FTP client to manually upload your icons to the following directory: %s','lmm'), '<br/>' . $defaults_marker_icon_dir);
}
?>
</body>
</html>
