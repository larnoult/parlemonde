<?php
/*
    Admin footer for APIS page - Maps Marker Pro
*/
?>

<?php
//info prevent file from being accessed directly
if (basename($_SERVER['SCRIPT_FILENAME']) == 'admin-header-apis.php') { die ("Please do not access this file directly. Thanks!<br/><a href='https://www.mapsmarker.com/go'>www.mapsmarker.com</a>"); }
$pro_feature_banner_inline = ' <a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_pro_upgrade" title="' . esc_attr__('This feature is available in the pro version only! Click here to find out how you can start a free 30-day-trial easily','lmm') . '"><img src="'. LEAFLET_PLUGIN_URL .'inc/img/pro-feature-banner.png" width="75" height="15" style="display:inline;" /></a>';
?>

<h1><?php _e('Maps Marker APIs', 'lmm'); ?></h1>

<h2><?php _e('Javascript Events API for LeafletJS', 'lmm'); ?> <?php echo $pro_feature_banner_inline; ?></h2>
<p>
<?php _e('Maps Marker Pro includes a javascript API which can be utilized by developers to attach events handlers to markers and layers.','lmm'); ?>
<br/>
<?php echo sprintf(__('The JS Events API has three main methods which can be used to access the maps by javascript code. Basically, you are free to apply any capability on the <span>Map</span> object existed in the <a href="%1$s" target="_blank">Leaflet library reference</a>.','lmm'), 'http://leafletjs.com/reference.html'); ?>
<br/>
<?php echo sprintf(__('For full documentation and usage examples please visit %1$s','lmm'), '<a href="https://www.mapsmarker.com/jseventsapi" target="_blank">https://www.mapsmarker.com/jseventsapi</a>'); ?>
</p>
<hr/>
<h2><?php _e('Filters and actions', 'lmm'); ?> <?php echo $pro_feature_banner_inline; ?></h2>
<p>
<?php _e('Maps Marker Pro supports selected filters and actions, the default WordPress approach for changing the behaviour of the plugin without having to change its files.', 'lmm'); ?>
<br/>
<?php echo sprintf(__('For full documentation and usage examples please visit %1$s','lmm'), '<a href="https://www.mapsmarker.com/filters-actions" target="_blank">https://www.mapsmarker.com/filters-actions</a>'); ?>
</p>
<hr/>
<h2><?php _e('Shortcode API reference', 'lmm'); ?> <?php echo $pro_feature_banner_inline; ?></h2>
<p>
<?php _e('Use the Shortcode API to create (marker) maps dynamically without adding them on backend first.','lmm'); ?><br/>
<?php _e('In addition you can use the Shortcode API to override selected global settings for selected maps.','lmm'); ?>
<br/>
<?php echo sprintf(__('For full documentation and usage examples please visit %1$s','lmm'), '<a href="https://www.mapsmarker.com/shortcodes" target="_blank">https://www.mapsmarker.com/shortcodes</a>'); ?>
</p>
<hr/>
<h2><?php _e('MMPAPI class', 'lmm'); ?> <?php echo $pro_feature_banner_inline; ?></h2>
<p>
<?php _e('The MMPAPI class provides developers with a future-proof way to access some of the common core functionalities in Maps Marker Pro.','lmm'); ?>
<br/>
<?php _e('The API functions are automatically included when Maps Marker Pro loads and they will be available by the time add-ons are loaded.','lmm'); ?>
<br/>
<?php echo sprintf(__('For full documentation and usage examples please visit %1$s','lmm'), '<a href="https://www.mapsmarker.com/mmpapi" target="_blank">https://www.mapsmarker.com/mmpapi</a>'); ?>
</p>
<hr/>
<h2><?php _e('Web API', 'lmm'); ?> <?php echo $pro_feature_banner_inline; ?></h2>
<p>
<?php _e('Use the Web API to access some of the common core functionalities either from JavaScript in a plugin or theme, or from an external client such as a desktop, mobile or web app.','lmm'); ?>
<br/>
<?php echo sprintf(__('For full documentation and usage examples please visit %1$s','lmm'), '<a href="https://www.mapsmarker.com/webapi" target="_blank">https://www.mapsmarker.com/webapi</a>'); ?>
<br/>
<?php echo sprintf(__('For Web API settings please <a href="%1$s">click here</a>.','lmm'), LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-misc-web_api'); ?>
</p>
<hr/>
<h2><?php _e('REST API', 'lmm'); ?> <?php echo $pro_feature_banner_inline; ?></h2>
<p>
<?php _e('Maps Marker Pro also offers a complete RESTful API allowing you to access some of the common core functionalities in Maps Marker Pro.','lmm'); ?>
<br/>
<?php echo sprintf(__('For full documentation and usage examples please visit %1$s','lmm'), '<a href="https://www.mapsmarker.com/restapi" target="_blank">https://www.mapsmarker.com/restapi</a>'); ?>
</p>