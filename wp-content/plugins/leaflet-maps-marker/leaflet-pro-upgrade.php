<?php
/*
    Pro Upgrade - Leaflet Maps Marker Plugin
*/
//info prevent file from being accessed directly
if (basename($_SERVER['SCRIPT_FILENAME']) == 'leaflet-pro-upgrade.php') { die ("Please do not access this file directly. Thanks!<br/><a href='https://www.mapsmarker.com/go'>www.mapsmarker.com</a>"); }

include('inc' . DIRECTORY_SEPARATOR . 'admin-header.php');
$first_run = (isset($_GET['first_run']) ? 'true' : 'false');

$lmm_pro_readme = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'leaflet-maps-marker-pro' . DIRECTORY_SEPARATOR . 'readme.txt';
$action = isset($_POST['action']) ? $_POST['action'] : '';
if ( $action == NULL ) {
	if (!file_exists($lmm_pro_readme)) {
		$override_css = ($first_run == 'true') ? 'style="margin-top:16px;"' : 'style="margin-top:20px;"';
		echo '<div class="pro-upgrade-logo-rtl" ' . $override_css . '><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/logo-mapsmarker-pro.png" alt="Pro Logo" title="Maps Marker Pro Logo" /></div>';
		echo '<h1 style="margin:6px 0 0 0;">' . __('More power: try Maps Marker Pro for free!','lmm') . '</h1>';
		echo '<form method="post"><input type="hidden" name="action" value="upgrade_to_pro_version" />';
		wp_nonce_field('pro-upgrade-nonce');
		echo '<p>' . __('Start a free 30-day-trial of Maps Marker Pro without any obligation. You can switch back to the free version anytime.','lmm') . '</p>';
		if ( current_user_can( 'install_plugins' ) ) {
			echo '<input style="font-weight:bold;" type="submit" name="submit_upgrade_to_pro_version" value="' . __('Sounds good! I will try it now','lmm') . ' &raquo;" class="submit button-primary" />';
		} else {
			echo '<div class="error" style="padding:10px;"><strong>' . sprintf(__('Warning: your user does not have the capability to install new plugins - please contact your administrator (%1s)','lmm'), '<a href="mailto:' . get_bloginfo('admin_email') . '?subject=' . esc_attr__('Please install the plugin "Maps Marker Pro"','lmm') . '">' . get_bloginfo('admin_email') . '</a>' ) . '</strong></div>';
			echo '<input style="font-weight:bold;" type="submit" name="submit_upgrade_to_pro_version" value="' . __('Sounds good! I will try it now','lmm') . ' &raquo;" class="submit button-secondary" disabled="disabled" />';
		}
		if ($first_run == 'true') {
			echo ' <a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker" style="text-decoration:none;">' . __('or thanks, maybe later','lmm') . '</a>';
		}
		echo '</form>';
		echo '<hr noshade size="1" style="margin-top:25px;"/><h2 style="margin-top:10px;">' . __('Highlights of Maps Marker Pro','lmm') . '</h2>';
		echo '<p>' . sprintf(__('For demo maps please visit %1s which also allows you to test the admin area of the pro version.','lmm'), '<a href="https://demo.mapsmarker.com/" target="_blank" style="text-decoration:none;">demo.mapsmarker.com</a>') . '</p>';
		echo '<p>' . sprintf(__('If you want to compare the free and pro version side by side, please visit %1s.','lmm'), '<a href="https://www.mapsmarker.com/comparison" target="_blank" style="text-decoration:none;">mapsmarker.com/comparison</a>') . '</p>';
		
		//info: different backgrounds for WP3.8+
		global $wp_version;
		if ( version_compare( $wp_version, '3.8-alpha', '>' ) ) { //info: for mp6 theme compatibility
			$bgcolor = '#FFFFFF';
		} else {
			$bgcolor = '#F2F2F2';
		}
		echo '<p style="clear:both;">
			<div id="pro-features">
				<span class="pro-feature-header">' . __('integration of the latest leaflet.js version','lmm') . '</span>
				<div class="pro-feature-content" style="background:' . $bgcolor . ';">
				<p style="margin:0;">
				<div style="float:right;margin:0 10px 10px 0;"><a href="http://www.leafletjs.com" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/pro-feature-leaflet.png"></a></div>' . __('Maps Marker Pro supports the latest leaflet.js version, which is the core library used for displaying maps.','lmm') . ' ' . __('Major highlights:','lmm') . '
				<ul style="list-style-type:disc;margin-left:15px;">
					<li>' . sprintf(__('over %1$s changes compared to v%2$s','lmm'), '400', '0.7.7') . '</li>
					<li>' . __('huge performance improvements in all aspects of the library and vector layers in particular','lmm') . '</li>
					<li>' . __('much better tile loading algorithm with less flickering','lmm') . '</li>
					<li>' . __('more accessibility features','lmm') . '</li>
					<li>' . __('tons of bugfixes and stability improvements','lmm') . '</li>
				</ul>
				</p>
				<p>
				<a class="pro-upgrade-external-links" href="https://www.mapsmarker.com/pro-feature-leaflet-changelog" target="_blank">' . sprintf(__('Click here to get the full changelog for leaflet.js v%1s currently integrated in the pro version','lmm'), '1.0.2 (11/2016)') . '</a> (' . sprintf(__('v%1s is used in the free version','lmm'), '0.7.7 (10/2015)') . ')
				</p>
				</div>
				<p><a href="#top" class="upgrade-top-link">' . __('back to top to start free 30-day-trial','lmm') . '</a></p>

				<span class="pro-feature-header">' . __('significantly increased performance for Google-based maps','lmm') . '</span>
				<div class="pro-feature-content" style="background:' . $bgcolor . ';">
				<div style="float:left;margin:0 10px 0 0;"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/pro-feature-speed.png"></div>
				' . __('Maps Marker Pro uses the state-of-the-art "GoogleMutant" leaflet plugin, which provides a much better user experience and performance when using Google Maps as basemaps.','lmm') . '
				<p>
				<a class="pro-upgrade-external-links" href="https://www.mapsmarker.com/v3.0p" target="_blank">' . __('Click here to get more information about this pro feature on mapsmarker.com','lmm') . '</a>
				</p>
				</div>
				<p><a href="#top" class="upgrade-top-link">' . __('back to top to start free 30-day-trial','lmm') . '</a></p>

				<span class="pro-feature-header">' . __('significantly decreased loadtimes for OpenStreetMap-based maps','lmm') . '</span>
				<div class="pro-feature-content" style="background:' . $bgcolor . ';">
				<div style="float:left;margin:0 10px 0 0;"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/pro-feature-speed.png"></div>
				' . __('Maps Marker Pro supports conditional and deferred Google Maps API loading. This saves visitor of your site up to ~370kb uncompressed data transmission for each page view with an embedded OpenStreetMap based map! If you are using Google Maps as basemap, the needed scripts are loaded deferred and on demand only.','lmm') . '
				<p>
				<a class="pro-upgrade-external-links" href="https://www.mapsmarker.com/v2.6.1p" target="_blank">' . __('Click here to get more information about this pro feature on mapsmarker.com','lmm') . '</a>
				</p>
				</div>
				<p><a href="#top" class="upgrade-top-link">' . __('back to top to start free 30-day-trial','lmm') . '</a></p>

				<span class="pro-feature-header">' . __('Marker clustering','lmm') . '</span>
				<div class="pro-feature-content" style="background:' . $bgcolor . ';">
				<p style="margin:0 0 10px 0;">
				' . __('Maps Marker Pro allows you to create beautifully animated marker clusters for layer maps:','lmm') . '
				</p>
				<img src="' . LEAFLET_PLUGIN_URL . 'inc/img/help-clustering.jpg">
				<p>
				<a class="pro-upgrade-external-links" href="https://www.mapsmarker.com/pro-feature-clustering" target="_blank">' . __('Click here to get more information about this pro feature on mapsmarker.com','lmm') . '</a>
				</p>
				</div>
				<p><a href="#top" class="upgrade-top-link">' . __('back to top to start free 30-day-trial','lmm') . '</a></p>

				<span class="pro-feature-header">' . __('Filter maps on frontend','lmm') . '</span>
				<div class="pro-feature-content" style="background:' . $bgcolor . ';">
				<div style="float:left;margin:0 10px 0 0;"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/pro-preview-filter-controlbox.png"></div>
				' . __('Maps Marker Pro allows you to organize your markers in categories and to toggle their visibility on frontend.','lmm') . '
				<p style="margin-bottom:67px;">
				<a class="pro-upgrade-external-links" href="https://www.mapsmarker.com/v2.7p" target="_blank">' . __('Click here to get more information about this pro feature on mapsmarker.com','lmm') . '</a>
				</p>
				</div>
				<p><a href="#top" class="upgrade-top-link">' . __('back to top to start free 30-day-trial','lmm') . '</a></p>

				<span class="pro-feature-header">' . __('Dynamic list of markers supporting paging, searching and sorting','lmm') . '</span>
				<div class="pro-feature-content" style="background:' . $bgcolor . ';">
				<p style="margin:0 0 10px 0;">
				' . __('The list of markers below layer maps in Maps Marker Pro allows you use dynamic paging, searching and sorting - making the list more usable for your visitors. The list can also be sorted based on the current position of the user viewing the map:','lmm') . '
				</p>
				<img src="' . LEAFLET_PLUGIN_URL . 'inc/img/pro-preview-dynamic-list-of-markers.png">
				<p>
				<a class="pro-upgrade-external-links" href="https://www.mapsmarker.com/v2.7p" target="_blank">' . __('Click here to get more information about this pro feature on mapsmarker.com','lmm') . '</a>
				</p>
				</div>
				<p><a href="#top" class="upgrade-top-link">' . __('back to top to start free 30-day-trial','lmm') . '</a></p>

				<span class="pro-feature-header">' . __('geolocation support: show and follow your location when viewing maps','lmm') . '</span>
				<div class="pro-feature-content" style="background:' . $bgcolor . ';">
				<p style="margin:0 0 10px 0;">
				<img src="' . LEAFLET_PLUGIN_URL . 'inc/img/help-geolocation	.jpg">
				</p>
				<p>
				<a class="pro-upgrade-external-links" href="https://www.mapsmarker.com/v1.9p" target="_blank">' . __('Click here to get more information about this pro feature on mapsmarker.com','lmm') . '</a>
				</p>
				</div>
				<p><a href="#top" class="upgrade-top-link">' . __('back to top to start free 30-day-trial','lmm') . '</a></p>

				<span class="pro-feature-header">' . __('GPX tracks','lmm') . '</span>
				<div class="pro-feature-content" style="background:' . $bgcolor . ';">
				<p style="margin:0 0 10px 0;">
				' . __('Maps Marker Pro allows you to also display GPX tracks with optional metadata on your maps:','lmm') . '
				</p>
				<img src="' . LEAFLET_PLUGIN_URL . 'inc/img/help-gpx.jpg">
				<p>
				<a class="pro-upgrade-external-links" href="https://www.mapsmarker.com/pro-feature-gpx" target="_blank">' . __('Click here to get more information about this pro feature on mapsmarker.com','lmm') . '</a>
				</p>
				</div>
				<p><a href="#top" class="upgrade-top-link">' . __('back to top to start free 30-day-trial','lmm') . '</a></p>

				<span class="pro-feature-header">' . __('support for assigning markers to multiple layers','lmm') . '</span>
				<div class="pro-feature-content" style="background:' . $bgcolor . ';">
				<p style="margin:0;">
				<div style="float:left;margin:0 10px 10px 0;"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/pro-feature-multi-layer-assignments.png"></div>' . __('Maps Marker Pro allows you to assign markers to multiple layers at once - helping you to better manage and organize your points of interest.','lmm') . '
				</p>
				<p style="margin-bottom:50px;">
				<a class="pro-upgrade-external-links" href="https://www.mapsmarker.com/v2.4p" target="_blank">' . __('Click here to get more information about this pro feature on mapsmarker.com','lmm') . '</a>
				</p>
				</div>
				<p><a href="#top" class="upgrade-top-link">' . __('back to top to start free 30-day-trial','lmm') . '</a></p>
				
				<span class="pro-feature-header">' . __('support for CSV/XLS/XLSX/ODS import and export for bulk additions and bulk updates','lmm') . '</span>
				<div class="pro-feature-content" style="background:' . $bgcolor . ';">
				<p style="margin:0 0 10px 0;">
				' . __('Maps Marker Pro allows you to easily perform bulk updates on markers and layers by using the integrated import feature:','lmm') . '
				</p>
				<img src="' . LEAFLET_PLUGIN_URL . 'inc/img/help-import.png">
				<p>
				<a class="pro-upgrade-external-links" href="https://www.mapsmarker.com/pro-feature-import" target="_blank">' . __('Click here to get more information about this pro feature on mapsmarker.com','lmm') . '</a>
				</p>
				</div>
				<p><a href="#top" class="upgrade-top-link">' . __('back to top to start free 30-day-trial','lmm') . '</a></p>

				<span class="pro-feature-header">' . __('HTML5 fullscreen maps','lmm') . '</span>
				<div class="pro-feature-content" style="background:' . $bgcolor . ';">
				<p style="margin:0;">
				<div style="float:left;margin:0 10px 10px 0;"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/pro-preview-html5-fullscreen.png"></div>' . __('Maps Marker Pro allows you to add a fullscreen button to maps. Clicking on this button will open an HTML5 fullscreen map without leaving the page you are currently viewing.','lmm') . '
				</p>
				<p style="margin-bottom:80px;">
				<a class="pro-upgrade-external-links" href="https://www.mapsmarker.com/pro-feature-htlm5-fullscreen-maps" target="_blank">' . __('Click here to get more information about this pro feature on mapsmarker.com','lmm') . '</a>
				</p>
				</div>
				<p><a href="#top" class="upgrade-top-link">' . __('back to top to start free 30-day-trial','lmm') . '</a></p>

				<span class="pro-feature-header">' . __('home button','lmm') . '</span>
				<div class="pro-feature-content" style="background:' . $bgcolor . ';">
				<p style="margin:0;">
				<div style="float:left;margin:0 10px 10px 0;"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/pro-preview-home-button.png"></div>' . __('With Maps Marker Pro you can add a home button to your maps which allows your visitors to reset the map to its original state.','lmm') . '
				</p>
				<p>
				<a class="pro-upgrade-external-links" href="https://www.mapsmarker.com/v2.7p" target="_blank">' . __('Click here to get more information about this pro feature on mapsmarker.com','lmm') . '</a>
				</p>
				</div>
				<p><a href="#top" class="upgrade-top-link">' . __('back to top to start free 30-day-trial','lmm') . '</a></p>

				<span class="pro-feature-header">' . __('Minimaps','lmm') . '</span>
				<div class="pro-feature-content" style="background:' . $bgcolor . ';">
				<p style="margin:0 0 10px 0;">
				' . __('Maps Marker Pro allows you to add a small map in the corner which shows the same as the main map with a set zoom offset:','lmm') . '
				</p>
				<img src="' . LEAFLET_PLUGIN_URL . 'inc/img/help-minimap.jpg">
				<p>
				<a class="pro-upgrade-external-links" href="https://www.mapsmarker.com/pro-feature-minimaps" target="_blank">' . __('Click here to get more information about this pro feature on mapsmarker.com','lmm') . '</a>
				</p>
				</div>
				<p><a href="#top" class="upgrade-top-link">' . __('back to top to start free 30-day-trial','lmm') . '</a></p>

				<span class="pro-feature-header">' . __('mobile web app support for fullscreen maps and optimized mobile viewport','lmm') . '</span>
				<div class="pro-feature-content" style="background:' . $bgcolor . ';">
				<p style="margin:0 0 10px 0;">
				' . __('Maps Marker Pro enables you to save the link to the fullscreen map to the homescreen on iOS devices and reopen the map with an optional launch image as web app – meaning the display of the map in fullscreen mode with no address bar:','lmm') . '
				</p>
				<img src="' . LEAFLET_PLUGIN_URL . 'inc/img/help-webapp.jpg">
				<p>
				' . __('Furthermore the viewport of the device used is considered, which results in optimized display of fullscreen maps especially on mobile devices:','lmm') . '
				</p>
				<img src="' . LEAFLET_PLUGIN_URL . 'inc/img/pro-feature-viewport-mobile.jpg">
				<p>
				<a class="pro-upgrade-external-links" href="https://www.mapsmarker.com/pro-feature-webapp" target="_blank">' . __('Click here to get more information about this pro feature on mapsmarker.com','lmm') . '</a>
				</p>
				</div>
				<p><a href="#top" class="upgrade-top-link">' . __('back to top to start free 30-day-trial','lmm') . '</a></p>

				<span class="pro-feature-header">' . __('custom Google Maps styling','lmm') . '</span>
				<div class="pro-feature-content" style="background:' . $bgcolor . ';">
				<p style="margin:0 0 10px 0;">
				' . __('Maps Marker Pro allow you to easily customize the presentation of the standard Google base maps, changing the visual display of such elements as roads, parks, and built-up areas:','lmm') . '
				</p>
				<img src="' . LEAFLET_PLUGIN_URL . 'inc/img/help-google-styling-preview.jpg">
				<p>
				<a class="pro-upgrade-external-links" href="https://www.mapsmarker.com/pro-feature-google-styling" target="_blank">' . __('Click here to get more information about this pro feature on mapsmarker.com','lmm') . '</a>
				</p>
				</div>
				<p><a href="#top" class="upgrade-top-link">' . __('back to top to start free 30-day-trial','lmm') . '</a></p>

				<span class="pro-feature-header">' . __('QR codes with custom backgrounds','lmm') . '</span>
				<div class="pro-feature-content" style="background:' . $bgcolor . ';">
				<p style="margin:0;">
				<div style="float:left;margin:0 10px 10px 0;"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/help-visualead.png"></div>' . __('Maps Marker Pro allows you to use custom backgrounds for QR codes.','lmm') . ' (' . __('custom visualead API key required!','lmm') . ')
				<br/><br/>
				' . __('Additionally the pro version does not display the visualead logo on the QR code output pages.','lmm') . '
				<br/><br/>
				' . __('Since pro v1.5 QR code images are also cached for a higher performance.','lmm') . '
				<br/><br/>
				<a class="pro-upgrade-external-links" href="https://www.mapsmarker.com/pro-feature-qrcode" target="_blank">' . __('Click here to get more information about this pro feature on mapsmarker.com','lmm') . '</a>
				</p>
				<p style="margin-bottom:95px;"></p>
				</div>
				<p><a href="#top" class="upgrade-top-link">' . __('back to top to start free 30-day-trial','lmm') . '</a></p>

				<span class="pro-feature-header">' . __('upload icon button & custom icon directory','lmm') . '</span>
				<div class="pro-feature-content" style="background:' . $bgcolor . ';">
				<p style="margin:0 0 10px 0;">
				' . __('Uploading new icons gets easier with Maps Marker Pro - no more need to use a FTP client, just click on the new upload button and add new icons from WordPress admin area easily:','lmm') . '
				</p>
				<img src="' . LEAFLET_PLUGIN_URL . 'inc/img/pro-feature-icon-upload.jpg">
				<p>
				<a class="pro-upgrade-external-links" href="https://www.mapsmarker.com/pro-feature-backlink-uploadbutton" target="_blank">' . __('Click here to get more information about this pro feature on mapsmarker.com','lmm') . '</a>
				</p>
				</div>
				<p><a href="#top" class="upgrade-top-link">' . __('back to top to start free 30-day-trial','lmm') . '</a></p>

				<span class="pro-feature-header">' . __('backup and restore of settings','lmm') . '</span>
				<div class="pro-feature-content" style="background:' . $bgcolor . ';">
				<p style="margin:0 0 10px 0;">
				' . __('Maps Marker Pro allows you to backup and restore your settings which makes it possible to quickly switch between different plugin profiles. This is especially useful if you want to deploy the plugin with custom configuration on multiple sites:','lmm') . '
				</p>
				<img src="' . LEAFLET_PLUGIN_URL . 'inc/img/pro-preview-backup-restore-settings.png">
				<p>
				<a class="pro-upgrade-external-links" href="https://www.mapsmarker.com/pro-feature-backup-restore" target="_blank">' . __('Click here to get more information about this pro feature on mapsmarker.com','lmm') . '</a>
				</p>
				</div>
				<p><a href="#top" class="upgrade-top-link">' . __('back to top to start free 30-day-trial','lmm') . '</a></p>

				<span class="pro-feature-header">' . __('advanced recent marker widget','lmm') . '</span>
				<div class="pro-feature-content" style="background:' . $bgcolor . ';">
				<p style="margin:0 0 10px 0;">
				' . __('Maps Marker Pro allows you to customize which markers and layers to include or exclude in the recent marker widget:','lmm') . '
				</p>
				<img src="' . LEAFLET_PLUGIN_URL . 'inc/img/pro-preview-advanced-widget.png">
				<p>
				' . __('Furthermore can also remove the attribution link from the recent marker widget:','lmm') . '
				</p>
				<img src="' . LEAFLET_PLUGIN_URL . 'inc/img/pro-preview-advanced-widget-noattribution.png">
				<p>
				<a class="pro-upgrade-external-links" href="https://www.mapsmarker.com/pro-feature-advanced-widget" target="_blank">' . __('Click here to get more information about this pro feature on mapsmarker.com','lmm') . '</a>
				</p>
				</div>
				<p><a href="#top" class="upgrade-top-link">' . __('back to top to start free 30-day-trial','lmm') . '</a></p>

				<span class="pro-feature-header">' . __('MapsMarker API','lmm') . '</span>
				<div class="pro-feature-content" style="background:' . $bgcolor . ';">
				<p style="margin:0 0 10px 0;">
				' . __('Manage your markers and layers through a highly customizable REST API, which supports GET & POST requests, JSON & XML as formats and was developed with a focus on security.','lmm') . ' ' . __('In addition, Maps Marker Pro also offers a separate MMPAPI class which you can use to developing an add-on for example.','lmm') . '
				</p>
				<img src="' . LEAFLET_PLUGIN_URL . 'inc/img/pro-preview-mapsmarker-api.png">
				<p>
				<a class="pro-upgrade-external-links" href="https://www.mapsmarker.com/pro-feature-mapsmarker-api" target="_blank">' . __('For more details please visit the MapsMarker API docs.','lmm') . '</a>
				</p>
				</div>
				<p><a href="#top" class="upgrade-top-link">' . __('back to top to start free 30-day-trial','lmm') . '</a></p>

				<span class="pro-feature-header">' . __('whitelabel backend admin pages','lmm') . '</span>
				<div class="pro-feature-content" style="background:' . $bgcolor . ';">
				<p style="margin:0 0 10px 0;">
				' . __('Maps Marker Pro allows you to remove all backlinks and logos on backend as well as making the pages and menu entries for Tools, Settings, Support, License visible to admins only.','lmm') . '
				</p>
				<img src="' . LEAFLET_PLUGIN_URL . 'inc/img/pro-preview-whitelabel-backend.png">
				<p>
				<a class="pro-upgrade-external-links" href="https://www.mapsmarker.com/pro-feature-whitelabel" target="_blank">' . __('Click here to get more information about this pro feature on mapsmarker.com','lmm') . '</a>
				</p>
				</div>
				<p><a href="#top" class="upgrade-top-link">' . __('back to top to start free 30-day-trial','lmm') . '</a></p>

				<span class="pro-feature-header">' . __('option to remove MapsMarker.com backlinks','lmm') . '</span>
				<div class="pro-feature-content" style="background:' . $bgcolor . ';">
				<p style="margin:0 0 10px 0;">
				' . __('Maps Marker Pro allows you to hide MapsMarker.com-backlinks from maps, KML files and from the Wikitude app:','lmm') . '
				</p>
				<img src="' . LEAFLET_PLUGIN_URL . 'inc/img/help-backlink.jpg"><br/><br/>
				<img src="' . LEAFLET_PLUGIN_URL . 'inc/img/help-backlink-kml.jpg"><br/><br/>
				<img src="' . LEAFLET_PLUGIN_URL . 'inc/img/pro-feature-wikitude-backlink.jpg">
				<p>
				<a class="pro-upgrade-external-links" href="https://www.mapsmarker.com/pro-feature-backlink-uploadbutton" target="_blank">' . __('Click here to get more information about this pro feature on mapsmarker.com','lmm') . '</a>
				</p>
				</div>
				<p><a href="#top" class="upgrade-top-link">' . __('back to top to start free 30-day-trial','lmm') . '</a></p>
			
				<span class="pro-feature-header">' . __('advanced permission settings','lmm') . '</span>
				<div class="pro-feature-content" style="background:' . $bgcolor . ';">
				<p style="margin:0 0 10px 0;">
				' . __('Maps Marker Pro allows you to set the user level needed for editing and deleting marker and layer maps from other users.','lmm') . '
				</p>
				<img src="' . LEAFLET_PLUGIN_URL . 'inc/img/pro-feature-advanced-permissions.png">
				<p>
				<a class="pro-upgrade-external-links" href="https://www.mapsmarker.com/pro-feature-advanced-permissions" target="_blank">' . __('Click here to get more information about this pro feature on mapsmarker.com','lmm') . '</a>
				</p>
				</div>
				<p><a href="#top" class="upgrade-top-link">' . __('back to top to start free 30-day-trial','lmm') . '</a></p>

				<span class="pro-feature-header">' . __('WPML translation support for multilingual maps','lmm') . '</span>
				<div class="pro-feature-content" style="background:' . $bgcolor . ';">
				<p style="margin:0 0 10px 0;">
				' . __('Maps Marker Pro makes it easy to build multilingual maps by fully supporting the translation solutions WPML and Polylang.','lmm') . '
				</p>
				<img src="' . LEAFLET_PLUGIN_URL . 'inc/img/pro-feature-multilingual.jpg" width="424" height="104">
				<p>
				<a class="pro-upgrade-external-links" href="https://www.mapsmarker.com/multilingual" target="_blank">' . __('Click here to get more information about this pro feature on mapsmarker.com','lmm') . '</a>
				</p>
				</div>
				<p><a href="#top" class="upgrade-top-link">' . __('back to top to start free 30-day-trial','lmm') . '</a></p>

				<span class="pro-feature-header">' . __('additional optimizations and improvements','lmm') . '</span>
				<div class="pro-feature-content" style="background:' . $bgcolor . ';">
				<ul style="list-style-type:disc;margin-left:15px;margin-top:0;">
					<li><a class="pro-upgrade-external-links" href="https://www.mapsmarker.com/v1.2.1p" target="_blank">' . __('improved performance for layer maps with a huge number of markers (parsing of GeoJSON is up to 3 times faster)','lmm') . '</a></li>
					<li><a class="pro-upgrade-external-links" href="https://www.mapsmarker.com/v1.3p" target="_blank">' . __('support for shortcodes in popup texts','lmm') . '</a></li>
					<li><a class="pro-upgrade-external-links" href="https://www.mapsmarker.com/v1.5p" target="_blank">' . __('support for setting global maximum zoom level to 21 (tiles from basemaps with lower native zoom levels will be upscaled automatically)','lmm') . '</a></li>
					<li><a class="pro-upgrade-external-links" href="https://www.mapsmarker.com/v1.5.1p" target="_blank">' . __('support for duplicating markers','lmm') . '</a></li>
					<li><a class="pro-upgrade-external-links" href="https://www.mapsmarker.com/v1.5.7p" target="_blank">' . __('support for dynamic switching between simplified and advanced editor (no more reloads needed)','lmm') . '</a></li>
					<li><a class="pro-upgrade-external-links" href="https://www.mapsmarker.com/v1.5.7p" target="_blank">' . __('support for filtering of marker icons on backend (based on filename)','lmm') . '</a></li>
					<li><a class="pro-upgrade-external-links" href="https://www.mapsmarker.com/v1.5.7p" target="_blank">' . __('support for changing marker IDs and layer IDs from the tools page','lmm') . '</a></li>
					<li><a class="pro-upgrade-external-links" href="https://www.mapsmarker.com/v1.5.7p" target="_blank">' . __('support for bulk updates of marker maps on the tools page for selected layers only','lmm') . '</a></li>
					<li><a class="pro-upgrade-external-links" href="https://www.mapsmarker.com/v1.5.8p" target="_blank">' . __('option to add markernames to popups automatically (default = false)','lmm') . '</a></li>
					<li><a class="pro-upgrade-external-links" href="https://www.mapsmarker.com/v1.5.8p" target="_blank">' . __('map moves back to initial position after popup is closed','lmm') . '</a></li>
					<li><a class="pro-upgrade-external-links" href="https://www.mapsmarker.com/v1.6p" target="_blank">' . __('option to disable loading of Google Maps API for higher performance if alternative basemaps are used only','lmm') . '</a></li>
					<li><a class="pro-upgrade-external-links" href="https://www.mapsmarker.com/v1.6p" target="_blank">' . sprintf(__('map parameters can be overwritten within shortcodes (e.g. %1s)','lmm'), '[mapsmarker marker="1" height="100"]') . '</a></li>
					<li><a class="pro-upgrade-external-links" href="https://www.mapsmarker.com/v1.8p" target="_blank">' . __('tool for monitoring "active shortcodes for already deleted maps"','lmm') . '</a></li>
					<li><a class="pro-upgrade-external-links" href="https://www.mapsmarker.com/v1.8p" target="_blank">' . __('layer maps: center map on markers and open popups by clicking on list of marker entries','lmm') . '</a></li>
					<li><a class="pro-upgrade-external-links" href="https://www.mapsmarker.com/v1.9p" target="_blank">' . __('search function for layerlist on marker edit page','lmm') . '</a></li> 
					<li><a class="pro-upgrade-external-links" href="https://www.mapsmarker.com/v1.9.2p" target="_blank">' . __('improved accessibility/screen reader support by using proper alt texts','lmm') . '</a></li> 
					<li><a class="pro-upgrade-external-links" href="https://www.mapsmarker.com/v1.9.3p" target="_blank">' . __('support for duplicating layer maps (without assigned markers)','lmm') . '</a></li> 
					<li><a class="pro-upgrade-external-links" href="https://www.mapsmarker.com/v1.9.3p" target="_blank">' . __('bulk actions for layers (duplicate, delete layer only, delete & re-assign markers)','lmm') . '</a></li> 
					<li><a class="pro-upgrade-external-links" href="https://www.mapsmarker.com/v2.1p" target="_blank">' . __('support for custom Mapbox basemaps','lmm') . '</a></li> 
					<li><a class="pro-upgrade-external-links" href="https://www.mapsmarker.com/v2.1p" target="_blank">' . __('optimized editing workflow for marker maps - no more reloads needed due to AJAX support','lmm') . '</a></li> 
					<li><a class="pro-upgrade-external-links" href="https://www.mapsmarker.com/v2.5p" target="_blank">' . __('optimized editing workflow for layer maps and list of markers-page - no more reloads needed due to AJAX support','lmm') . '</a></li> 
					<li><a class="pro-upgrade-external-links" href="https://www.mapsmarker.com/v2.5p" target="_blank">' . __('option to duplicate layer AND assigned markers (for single layers and for layer bulk actions)','lmm') . '</a></li> 
					<li><a class="pro-upgrade-external-links" href="https://www.mapsmarker.com/v2.5p" target="_blank">' . __('option to disable map dragging on touch devices only','lmm') . '</a></li> 
					<li><a class="pro-upgrade-external-links" href="https://www.mapsmarker.com/v2.5p" target="_blank">' . __('dynamic preview of all markers from assigned layer(s) on marker edit pages','lmm') . '</a></li> 
					<li><a class="pro-upgrade-external-links" href="https://www.mapsmarker.com/v2.5p" target="_blank">' . __('dynamic preview of markers from checked multi-layer-map layer(s) on layer edit pages','lmm') . '</a></li> 
					<li><a class="pro-upgrade-external-links" href="https://www.mapsmarker.com/v2.5p" target="_blank">' . __('"edit map"-link on frontend based on user-permissions for better maintainability','lmm') . '</a></li> 
					<li><a class="pro-upgrade-external-links" href="https://www.mapsmarker.com/v2.6p" target="_blank">' . __('option to sort list of markers below layer maps by distance from layer center','lmm') . '</a></li> 
					<li><a class="pro-upgrade-external-links" href="https://www.mapsmarker.com/v2.6p" target="_blank">' . __('highlight a marker on a layer map by opening its popup via shortcode attribute [mapsmarker layer="1" highlightmarker="2"] or by adding ?highlightmarker=2 to the URL where the map is embedded','lmm') . '</a></li> 
					<li><a class="pro-upgrade-external-links" href="https://www.mapsmarker.com/v2.6p" target="_blank">' . __('improved backend usability by listing all contents (posts, pages, CPTs, widgets) where each shortcode is used','lmm') . '</a></li> 
					<li><a class="pro-upgrade-external-links" href="https://www.mapsmarker.com/v2.6p" target="_blank">' . __('XML sitemaps integration: improved local SEO value by automatically adding links to KML maps to your XML sitemaps (if plugin "Google XML Sitemaps" is active)','lmm') . '</a></li> 
					<li><a class="pro-upgrade-external-links" href="https://www.mapsmarker.com/v2.6p" target="_blank">' . sprintf(__('add dynamic URL hashes to web pages with maps, allowing users to easily link to specific map views. Example: %1$s','lmm'), 'https://domain/link-to-map/#11/48.2073/16.3792') . '</a></li> 
					<li><a class="pro-upgrade-external-links" href="https://www.mapsmarker.com/v2.7p" target="_blank">' . __('Marker validity check for layer assignements','lmm') . '</a></li> 
					<li><a class="pro-upgrade-external-links" href="https://www.mapsmarker.com/restapi" target="_blank">' . __('RESTful API allowing you to access some of the common core functionalities ','lmm') . '</a></li> 
					<li><a class="pro-upgrade-external-links" href="https://www.mapsmarker.com/jseventsapi" target="_blank">' . __('Javascript Events API for LeafletJS to to attach events handlers to markers and layers','lmm') . '</a></li> 
					<li><a class="pro-upgrade-external-links" href="https://www.mapsmarker.com/filters-actions" target="_blank">' . __('support for filters and actions','lmm') . '</a></li> 
					<li><a class="pro-upgrade-external-links" href="https://www.mapsmarker.com/v3.0p" target="_blank">' . __('add pre-loading for map tiles beyond the edge of the visible map to prevent showing background behind tile images when panning a map','lmm') . '</a></li>
					<li><a class="pro-upgrade-external-links" href="https://www.mapsmarker.com/v3.0p" target="_blank">' . sprintf(__('Pretty permalinks with customizable slug for fullscreen maps and APIs (e.g. %1$s)','lmm'), get_site_url() . '/<strong>maps</strong>/fullscreen/marker/1') . '</a></li>
					<li><a class="pro-upgrade-external-links" href="https://www.mapsmarker.com/v3.0p" target="_blank">' . __('support for tooltips to display the marker name as small text on top of marker icons','lmm') . '</a></li>
					<li><a class="pro-upgrade-external-links" href="https://www.mapsmarker.com/v3.0p" target="_blank">' . __('AMP support: show placeholder image for map with link to fullscreen view on AMP enabled pages','lmm') . '</a></li>
					<li><a class="pro-upgrade-external-links" href="https://www.mapsmarker.com/v3.1p" target="_blank">' . __('new widget "show latest marker map"','lmm') . '</a></li>
					<li><a class="pro-upgrade-external-links" href="https://www.mapsmarker.com/v3.1p" target="_blank">' . __('list all markers page enhancement: dropdown added to filter markers by layer','lmm') . '</a></li>
					<li><a class="pro-upgrade-external-links" href="https://www.mapsmarker.com/v3.1p" target="_blank">' . __('loading indicator for GeoJSON download and marker clustering','lmm') . '</a></li>
					<li><a class="pro-upgrade-external-links" href="https://www.mapsmarker.com/v3.1p" target="_blank">' . __('global basemap setting "nowrap": (if set to true, tiles will not load outside the world width instead of repeating, default: false)','lmm') . '</a></li>
				</ul>
				</div>
				<p><a href="#top" class="upgrade-top-link">' . __('back to top to start free 30-day-trial','lmm') . '</a></p>

				<span class="pro-feature-header">' . __('features planned for future releases','lmm') . '</span>
				<div class="pro-feature-content" style="background:' . $bgcolor . ';">
				<p style="margin:0 0 10px 0;">
				' . __('We are working hard on delivering the best mapping solution available for WordPress - helping you to share your favorite spots. Therefore we are commited to constantly improving Maps Marker Pro.','lmm') . '<br/>' . sprintf(__('Follow <a href="%1$s" target="_blank">@MapsMarker</a> on Twitter for constant updates or use our <a href="%2$s" target="_blank">contact form</a> to submit your feature request or idea.','lmm'), 'https://twitter.com/MapsMarker', 'https://www.mapsmarker.com/contact') . '
				</p>
				</div>
				<p><a href="#top" class="upgrade-top-link">' . __('back to top to start free 30-day-trial','lmm') . '</a></p>
			</div>
			</p>
			<p>' . __('For more details, showcases and reviews please also visit <a style="text-decoration:none;" href="http://www.mapsmarker.com">www.mapsmarker.com</a>','lmm') . '</p>';
			echo '<script type="text/javascript">
					//info: toggle advanced menu items - duplicated from admin-footer.php
					jQuery("#show-advanced-menu-items-link, #hide-advanced-menu-items-link").click(function(e) {
						jQuery("#show-advanced-menu-items-link").toggle();
						jQuery("#hide-advanced-menu-items-link").toggle();
						jQuery("#advanced-menu-items").toggle();
					});
					</script>';
	} else if (file_exists($lmm_pro_readme)) {
		echo '<h1>' . __('Upgrade to pro version','lmm') . '</h1>';
		echo '<div class="error" style="padding:10px;"><strong>' . __('You already downloaded "Maps Marker Pro" to your server but did not activate the plugin yet!','lmm') . '</strong></div>';
		if ( current_user_can( 'install_plugins' ) ) {
			echo sprintf(__('Please navigate to <a href="%1$s">Plugins / Installed Plugins</a> and activate the plugin "Maps Marker Pro".','lmm'), LEAFLET_WP_ADMIN_URL . 'plugins.php');
		} else {
			echo sprintf(__('Please contact your administrator (%1s) to activate the plugin "Maps Marker Pro".','lmm'), '<a href="mailto:' . get_bloginfo('admin_email') . '?subject=' . esc_attr__('Please activate the plugin "Maps Marker Pro"','lmm') . '">' . get_bloginfo('admin_email') . '</a>' );
		}
	}
} else {
	if (!wp_verify_nonce( $_POST['_wpnonce'], 'pro-upgrade-nonce') ) { wp_die('<br/>'.__('Security check failed - please call this function from the according admin page!','lmm').''); };
	if ($action == 'upgrade_to_pro_version') {
		include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		add_filter( 'https_ssl_verify', '__return_false' ); //info: otherwise SSL error on localhost installs.
		add_filter( 'https_local_ssl_verify', '__return_false' ); //info: not sure if needed, added to be sure
		$upgrader = new Plugin_Upgrader( new Plugin_Upgrader_Skin() );
		$dl = 'https://www.mapsmarker.com/upgrade-pro';
		$upgrader->install( $dl );
		//info: check if download was successful
		$lmm_pro_readme = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'leaflet-maps-marker-pro' . DIRECTORY_SEPARATOR . 'readme.txt';
		if (file_exists($lmm_pro_readme)) {
			echo '<p>' . __('Please activate the plugin by clicking the link above','lmm') . '</p>';
		} else {
			$dl_l = 'https://www.mapsmarker.com/upgrade-pro';
			$dl_lt = 'www.mapsmarker.com/upgrade-pro';
			echo '<p>' . sprintf(__('The pro plugin package could not be downloaded automatically. Please download the plugin from <a href="%1s">%2s</a> and upload it to the directory /wp-content/plugins on your server manually','lmm'), $dl_l, $dl_lt) . '</p>';
		}
	}
}
?>
</div>
<!--wrap-->