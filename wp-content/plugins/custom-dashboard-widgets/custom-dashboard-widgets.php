<?php
/**
 * @package custom-dashboard-widgets
 * @version 1.3
 */
/*
Plugin Name: Custom Dashboard Widgets
Plugin URI: http://wordpress.org/plugins/custom-dashboard-widgets
Description: Customize Your Dashboard Main Page, New Layouts, you can simplisity customize your dashboard links to access quickly to your dashboard pages.
You can add new row (access link), edit rows and delete row.
Version: 1.3.1
Author: AboZain,O7abeeb,UnitOne
Author URI: https://profiles.wordpress.org/abozain
tags: Dashboard, Widget, Layout, layouts, widgets, posts, links, settings, plugins, dashboard layout, dashboard widgets, custom dashboard, customize dashboard
*/


add_action( 'admin_menu', 'cdw_reg_menu' );

function cdw_reg_menu(){
	add_options_page( __('Dashboard Widgets', 'DashboardWidgets'), __('Dashboard Widgets', 'DashboardWidgets'), 'administrator', 'dashboard-widgets', 'cdw_DashboardWidgets'); 
}


# Load plugin text domain
add_action( 'init', 'cdw_plugin_textdomain' );
# Text domain for translations
function cdw_plugin_textdomain() {
    $domain = 'DashboardWidgets';
    $locale = apply_filters( 'plugin_locale', get_locale(), $domain );
    load_textdomain( $domain, WP_LANG_DIR.'/'.$domain.'/'.$domain.'-'.$locale.'.mo' );
    load_plugin_textdomain( $domain, FALSE, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
}
//////////////////////////
function cdw_DashboardWidgets(){
	echo '<link rel="stylesheet" type="text/css" href="'.plugins_url( 'dw_style.css', __FILE__ ).'" />';

	?>

	
			<div class="modalDialog">
				<div>
					<a href="#" title="Close" class="close"><i class="fa fa-times"></i></a>
					<table class="table table-bordered">
						<tr>
							<th width="30%"> <?php _e('Title', 'DashboardWidgets') ?> </th>
							<th width="30%"> <?php _e('icon', 'DashboardWidgets') ?>  </th>
							<th width="40%"> <?php _e('preview', 'DashboardWidgets') ?>  </th>
						</tr>
						<tr>
							<td><?php _e('Testimonials', 'DashboardWidgets') ?></td>
							<td><i style="font-size:22px;" class="fa fa-quote-left"></i></td>
							<td>fa-quote-left</td>
						</tr>
						<tr>
							<td><?php _e('Products', 'DashboardWidgets') ?></td>
							<td><span style="font-size:22px;" class="dashicons dashicons-cart"></span></td>
							<td>dashicons-cart</td>
						</tr>
						<tr>
							<td><?php _e('Clients', 'DashboardWidgets') ?></td>
							<td><i style="font-size:22px;" class="fa fa-users"></i></td>
							<td>fa-users</td>
						</tr>
						<tr>
							<td><?php _e('Slider', 'DashboardWidgets') ?></td>
							<td><i style="font-size:22px;" class="fa fa-sliders"></i></td>
							<td>fa-sliders</td>
						</tr>
						<tr>
							<td><?php _e('Videos', 'DashboardWidgets') ?></td>
							<td><i style="font-size:22px;" class="fa fa-caret-square-o-right"></i></td>
							<td>fa-caret-square-o-right</td>
						</tr>
						<tr>
							<td><?php _e('Gallery', 'DashboardWidgets') ?></td>
							<td><i style="font-size:22px;" class="fa fa-picture-o"></i></td>
							<td>fa-picture-o</td>
						</tr>
						<tr>
							<td><?php _e('Services', 'DashboardWidgets') ?></td>
							<td><i style="font-size:22px;" class="fa fa-usb"></i></td>
							<td>fa-usb</td>
						</tr>
						<tr>
							<td><?php _e('FAQ', 'DashboardWidgets') ?></td>
							<td><i style="font-size:22px;" class="fa fa-question-circle"></i></td>
							<td>fa-question-circle</td>
						</tr>
						<tr>
							<td><?php _e('Team', 'DashboardWidgets') ?></td>
							<td><i style="font-size:22px;" class="fa fa-users"></i></td>
							<td>fa fa-users</td>
						</tr>
					</table>
				</div>
			</div>

    <div class="modalDashIcons">

        <div class="ws_tool_tab ui-tabs-panel ui-widget-content ui-corner-bottom" id="ws_core_icons_tab" aria-labelledby="ui-id-2" role="tabpanel" aria-hidden="false">
            <a href="#" title="Close" class="closeDashIcons"><i class="fa fa-times"></i></a>
            <div class="ws_icon_option" title="Admin Generic" data-icon-url="dashicons-admin-generic">
                <div class="ws_icon_image icon16 dashicons dashicons-admin-generic"></div>
            </div><div class="ws_icon_option" title="Dashboard" data-icon-url="dashicons-dashboard">
                <div class="ws_icon_image icon16 dashicons dashicons-dashboard"></div>
            </div><div class="ws_icon_option" title="Admin Post" data-icon-url="dashicons-admin-post">
                <div class="ws_icon_image icon16 dashicons dashicons-admin-post"></div>
            </div><div class="ws_icon_option" title="Admin Media" data-icon-url="dashicons-admin-media">
                <div class="ws_icon_image icon16 dashicons dashicons-admin-media"></div>
            </div><div class="ws_icon_option" title="Admin Links" data-icon-url="dashicons-admin-links">
                <div class="ws_icon_image icon16 dashicons dashicons-admin-links"></div>
            </div><div class="ws_icon_option" title="Admin Page" data-icon-url="dashicons-admin-page">
                <div class="ws_icon_image icon16 dashicons dashicons-admin-page"></div>
            </div><div class="ws_icon_option" title="Admin Comments" data-icon-url="dashicons-admin-comments">
                <div class="ws_icon_image icon16 dashicons dashicons-admin-comments"></div>
            </div><div class="ws_icon_option" title="Admin Appearance" data-icon-url="dashicons-admin-appearance">
                <div class="ws_icon_image icon16 dashicons dashicons-admin-appearance"></div>
            </div><div class="ws_icon_option" title="Admin Plugins" data-icon-url="dashicons-admin-plugins">
                <div class="ws_icon_image icon16 dashicons dashicons-admin-plugins"></div>
            </div><div class="ws_icon_option" title="Admin Users" data-icon-url="dashicons-admin-users">
                <div class="ws_icon_image icon16 dashicons dashicons-admin-users"></div>
            </div><div class="ws_icon_option" title="Admin Tools" data-icon-url="dashicons-admin-tools">
                <div class="ws_icon_image icon16 dashicons dashicons-admin-tools"></div>
            </div><div class="ws_icon_option" title="Admin Settings" data-icon-url="dashicons-admin-settings">
                <div class="ws_icon_image icon16 dashicons dashicons-admin-settings"></div>
            </div><div class="ws_icon_option" title="Admin Network" data-icon-url="dashicons-admin-network">
                <div class="ws_icon_image icon16 dashicons dashicons-admin-network"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Admin Site" data-icon-url="dashicons-admin-site">
                <div class="ws_icon_image icon16 dashicons dashicons-admin-site"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Admin Home" data-icon-url="dashicons-admin-home">
                <div class="ws_icon_image icon16 dashicons dashicons-admin-home"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Album" data-icon-url="dashicons-album">
                <div class="ws_icon_image icon16 dashicons dashicons-album"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Align Center" data-icon-url="dashicons-align-center">
                <div class="ws_icon_image icon16 dashicons dashicons-align-center"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Align Left" data-icon-url="dashicons-align-left">
                <div class="ws_icon_image icon16 dashicons dashicons-align-left"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Align None" data-icon-url="dashicons-align-none">
                <div class="ws_icon_image icon16 dashicons dashicons-align-none"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Align Right" data-icon-url="dashicons-align-right">
                <div class="ws_icon_image icon16 dashicons dashicons-align-right"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Analytics" data-icon-url="dashicons-analytics">
                <div class="ws_icon_image icon16 dashicons dashicons-analytics"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Archive" data-icon-url="dashicons-archive">
                <div class="ws_icon_image icon16 dashicons dashicons-archive"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Art" data-icon-url="dashicons-art">
                <div class="ws_icon_image icon16 dashicons dashicons-art"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Awards" data-icon-url="dashicons-awards">
                <div class="ws_icon_image icon16 dashicons dashicons-awards"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Backup" data-icon-url="dashicons-backup">
                <div class="ws_icon_image icon16 dashicons dashicons-backup"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Book" data-icon-url="dashicons-book">
                <div class="ws_icon_image icon16 dashicons dashicons-book"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Book Alt" data-icon-url="dashicons-book-alt">
                <div class="ws_icon_image icon16 dashicons dashicons-book-alt"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Building" data-icon-url="dashicons-building">
                <div class="ws_icon_image icon16 dashicons dashicons-building"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Businessman" data-icon-url="dashicons-businessman">
                <div class="ws_icon_image icon16 dashicons dashicons-businessman"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Calendar" data-icon-url="dashicons-calendar">
                <div class="ws_icon_image icon16 dashicons dashicons-calendar"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Calendar Alt" data-icon-url="dashicons-calendar-alt">
                <div class="ws_icon_image icon16 dashicons dashicons-calendar-alt"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Camera" data-icon-url="dashicons-camera">
                <div class="ws_icon_image icon16 dashicons dashicons-camera"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Carrot" data-icon-url="dashicons-carrot">
                <div class="ws_icon_image icon16 dashicons dashicons-carrot"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Cart" data-icon-url="dashicons-cart">
                <div class="ws_icon_image icon16 dashicons dashicons-cart"></div>
            </div><div class="ws_icon_option ws_icon_extra ws_selected_icon" title="Category" data-icon-url="dashicons-category">
                <div class="ws_icon_image icon16 dashicons dashicons-category"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Chart Area" data-icon-url="dashicons-chart-area">
                <div class="ws_icon_image icon16 dashicons dashicons-chart-area"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Chart Bar" data-icon-url="dashicons-chart-bar">
                <div class="ws_icon_image icon16 dashicons dashicons-chart-bar"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Chart Line" data-icon-url="dashicons-chart-line">
                <div class="ws_icon_image icon16 dashicons dashicons-chart-line"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Chart Pie" data-icon-url="dashicons-chart-pie">
                <div class="ws_icon_image icon16 dashicons dashicons-chart-pie"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Clipboard" data-icon-url="dashicons-clipboard">
                <div class="ws_icon_image icon16 dashicons dashicons-clipboard"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Clock" data-icon-url="dashicons-clock">
                <div class="ws_icon_image icon16 dashicons dashicons-clock"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Cloud" data-icon-url="dashicons-cloud">
                <div class="ws_icon_image icon16 dashicons dashicons-cloud"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Desktop" data-icon-url="dashicons-desktop">
                <div class="ws_icon_image icon16 dashicons dashicons-desktop"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Dismiss" data-icon-url="dashicons-dismiss">
                <div class="ws_icon_image icon16 dashicons dashicons-dismiss"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Download" data-icon-url="dashicons-download">
                <div class="ws_icon_image icon16 dashicons dashicons-download"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Edit" data-icon-url="dashicons-edit">
                <div class="ws_icon_image icon16 dashicons dashicons-edit"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Editor Code" data-icon-url="dashicons-editor-code">
                <div class="ws_icon_image icon16 dashicons dashicons-editor-code"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Editor Contract" data-icon-url="dashicons-editor-contract">
                <div class="ws_icon_image icon16 dashicons dashicons-editor-contract"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Editor Customchar" data-icon-url="dashicons-editor-customchar">
                <div class="ws_icon_image icon16 dashicons dashicons-editor-customchar"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Editor Distractionfree" data-icon-url="dashicons-editor-distractionfree">
                <div class="ws_icon_image icon16 dashicons dashicons-editor-distractionfree"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Editor Help" data-icon-url="dashicons-editor-help">
                <div class="ws_icon_image icon16 dashicons dashicons-editor-help"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Editor Insertmore" data-icon-url="dashicons-editor-insertmore">
                <div class="ws_icon_image icon16 dashicons dashicons-editor-insertmore"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Editor Justify" data-icon-url="dashicons-editor-justify">
                <div class="ws_icon_image icon16 dashicons dashicons-editor-justify"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Editor Kitchensink" data-icon-url="dashicons-editor-kitchensink">
                <div class="ws_icon_image icon16 dashicons dashicons-editor-kitchensink"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Editor Ol" data-icon-url="dashicons-editor-ol">
                <div class="ws_icon_image icon16 dashicons dashicons-editor-ol"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Editor Paste Text" data-icon-url="dashicons-editor-paste-text">
                <div class="ws_icon_image icon16 dashicons dashicons-editor-paste-text"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Editor Paste Word" data-icon-url="dashicons-editor-paste-word">
                <div class="ws_icon_image icon16 dashicons dashicons-editor-paste-word"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Editor Quote" data-icon-url="dashicons-editor-quote">
                <div class="ws_icon_image icon16 dashicons dashicons-editor-quote"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Editor Removeformatting" data-icon-url="dashicons-editor-removeformatting">
                <div class="ws_icon_image icon16 dashicons dashicons-editor-removeformatting"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Editor Rtl" data-icon-url="dashicons-editor-rtl">
                <div class="ws_icon_image icon16 dashicons dashicons-editor-rtl"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Editor Spellcheck" data-icon-url="dashicons-editor-spellcheck">
                <div class="ws_icon_image icon16 dashicons dashicons-editor-spellcheck"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Editor Ul" data-icon-url="dashicons-editor-ul">
                <div class="ws_icon_image icon16 dashicons dashicons-editor-ul"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Editor Unlink" data-icon-url="dashicons-editor-unlink">
                <div class="ws_icon_image icon16 dashicons dashicons-editor-unlink"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Editor Video" data-icon-url="dashicons-editor-video">
                <div class="ws_icon_image icon16 dashicons dashicons-editor-video"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Email" data-icon-url="dashicons-email">
                <div class="ws_icon_image icon16 dashicons dashicons-email"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Email Alt" data-icon-url="dashicons-email-alt">
                <div class="ws_icon_image icon16 dashicons dashicons-email-alt"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Exerpt View" data-icon-url="dashicons-exerpt-view">
                <div class="ws_icon_image icon16 dashicons dashicons-exerpt-view"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="External" data-icon-url="dashicons-external">
                <div class="ws_icon_image icon16 dashicons dashicons-external"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Facebook" data-icon-url="dashicons-facebook">
                <div class="ws_icon_image icon16 dashicons dashicons-facebook"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Facebook Alt" data-icon-url="dashicons-facebook-alt">
                <div class="ws_icon_image icon16 dashicons dashicons-facebook-alt"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Feedback" data-icon-url="dashicons-feedback">
                <div class="ws_icon_image icon16 dashicons dashicons-feedback"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Filter" data-icon-url="dashicons-filter">
                <div class="ws_icon_image icon16 dashicons dashicons-filter"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Flag" data-icon-url="dashicons-flag">
                <div class="ws_icon_image icon16 dashicons dashicons-flag"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Format Aside" data-icon-url="dashicons-format-aside">
                <div class="ws_icon_image icon16 dashicons dashicons-format-aside"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Format Audio" data-icon-url="dashicons-format-audio">
                <div class="ws_icon_image icon16 dashicons dashicons-format-audio"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Format Chat" data-icon-url="dashicons-format-chat">
                <div class="ws_icon_image icon16 dashicons dashicons-format-chat"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Format Gallery" data-icon-url="dashicons-format-gallery">
                <div class="ws_icon_image icon16 dashicons dashicons-format-gallery"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Format Image" data-icon-url="dashicons-format-image">
                <div class="ws_icon_image icon16 dashicons dashicons-format-image"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Format Quote" data-icon-url="dashicons-format-quote">
                <div class="ws_icon_image icon16 dashicons dashicons-format-quote"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Format Status" data-icon-url="dashicons-format-status">
                <div class="ws_icon_image icon16 dashicons dashicons-format-status"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Format Video" data-icon-url="dashicons-format-video">
                <div class="ws_icon_image icon16 dashicons dashicons-format-video"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Forms" data-icon-url="dashicons-forms">
                <div class="ws_icon_image icon16 dashicons dashicons-forms"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Googleplus" data-icon-url="dashicons-googleplus">
                <div class="ws_icon_image icon16 dashicons dashicons-googleplus"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Grid View" data-icon-url="dashicons-grid-view">
                <div class="ws_icon_image icon16 dashicons dashicons-grid-view"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Groups" data-icon-url="dashicons-groups">
                <div class="ws_icon_image icon16 dashicons dashicons-groups"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Hammer" data-icon-url="dashicons-hammer">
                <div class="ws_icon_image icon16 dashicons dashicons-hammer"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Heart" data-icon-url="dashicons-heart">
                <div class="ws_icon_image icon16 dashicons dashicons-heart"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Hidden" data-icon-url="dashicons-hidden">
                <div class="ws_icon_image icon16 dashicons dashicons-hidden"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Id" data-icon-url="dashicons-id">
                <div class="ws_icon_image icon16 dashicons dashicons-id"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Id Alt" data-icon-url="dashicons-id-alt">
                <div class="ws_icon_image icon16 dashicons dashicons-id-alt"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Image Crop" data-icon-url="dashicons-image-crop">
                <div class="ws_icon_image icon16 dashicons dashicons-image-crop"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Image Filter" data-icon-url="dashicons-image-filter">
                <div class="ws_icon_image icon16 dashicons dashicons-image-filter"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Image Flip Horizontal" data-icon-url="dashicons-image-flip-horizontal">
                <div class="ws_icon_image icon16 dashicons dashicons-image-flip-horizontal"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Image Flip Vertical" data-icon-url="dashicons-image-flip-vertical">
                <div class="ws_icon_image icon16 dashicons dashicons-image-flip-vertical"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Image Rotate" data-icon-url="dashicons-image-rotate">
                <div class="ws_icon_image icon16 dashicons dashicons-image-rotate"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Image Rotate Left" data-icon-url="dashicons-image-rotate-left">
                <div class="ws_icon_image icon16 dashicons dashicons-image-rotate-left"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Image Rotate Right" data-icon-url="dashicons-image-rotate-right">
                <div class="ws_icon_image icon16 dashicons dashicons-image-rotate-right"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Images Alt" data-icon-url="dashicons-images-alt">
                <div class="ws_icon_image icon16 dashicons dashicons-images-alt"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Images Alt2" data-icon-url="dashicons-images-alt2">
                <div class="ws_icon_image icon16 dashicons dashicons-images-alt2"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Index Card" data-icon-url="dashicons-index-card">
                <div class="ws_icon_image icon16 dashicons dashicons-index-card"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Info" data-icon-url="dashicons-info">
                <div class="ws_icon_image icon16 dashicons dashicons-info"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Leftright" data-icon-url="dashicons-leftright">
                <div class="ws_icon_image icon16 dashicons dashicons-leftright"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Lightbulb" data-icon-url="dashicons-lightbulb">
                <div class="ws_icon_image icon16 dashicons dashicons-lightbulb"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="List View" data-icon-url="dashicons-list-view">
                <div class="ws_icon_image icon16 dashicons dashicons-list-view"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Location" data-icon-url="dashicons-location">
                <div class="ws_icon_image icon16 dashicons dashicons-location"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Location Alt" data-icon-url="dashicons-location-alt">
                <div class="ws_icon_image icon16 dashicons dashicons-location-alt"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Lock" data-icon-url="dashicons-lock">
                <div class="ws_icon_image icon16 dashicons dashicons-lock"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Marker" data-icon-url="dashicons-marker">
                <div class="ws_icon_image icon16 dashicons dashicons-marker"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Media Archive" data-icon-url="dashicons-media-archive">
                <div class="ws_icon_image icon16 dashicons dashicons-media-archive"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Media Audio" data-icon-url="dashicons-media-audio">
                <div class="ws_icon_image icon16 dashicons dashicons-media-audio"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Media Code" data-icon-url="dashicons-media-code">
                <div class="ws_icon_image icon16 dashicons dashicons-media-code"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Media Default" data-icon-url="dashicons-media-default">
                <div class="ws_icon_image icon16 dashicons dashicons-media-default"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Media Video" data-icon-url="dashicons-media-video">
                <div class="ws_icon_image icon16 dashicons dashicons-media-video"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Megaphone" data-icon-url="dashicons-megaphone">
                <div class="ws_icon_image icon16 dashicons dashicons-megaphone"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Menu" data-icon-url="dashicons-menu">
                <div class="ws_icon_image icon16 dashicons dashicons-menu"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Microphone" data-icon-url="dashicons-microphone">
                <div class="ws_icon_image icon16 dashicons dashicons-microphone"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Migrate" data-icon-url="dashicons-migrate">
                <div class="ws_icon_image icon16 dashicons dashicons-migrate"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Minus" data-icon-url="dashicons-minus">
                <div class="ws_icon_image icon16 dashicons dashicons-minus"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Money" data-icon-url="dashicons-money">
                <div class="ws_icon_image icon16 dashicons dashicons-money"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Nametag" data-icon-url="dashicons-nametag">
                <div class="ws_icon_image icon16 dashicons dashicons-nametag"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Networking" data-icon-url="dashicons-networking">
                <div class="ws_icon_image icon16 dashicons dashicons-networking"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="No" data-icon-url="dashicons-no">
                <div class="ws_icon_image icon16 dashicons dashicons-no"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="No Alt" data-icon-url="dashicons-no-alt">
                <div class="ws_icon_image icon16 dashicons dashicons-no-alt"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Palmtree" data-icon-url="dashicons-palmtree">
                <div class="ws_icon_image icon16 dashicons dashicons-palmtree"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Performance" data-icon-url="dashicons-performance">
                <div class="ws_icon_image icon16 dashicons dashicons-performance"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Phone" data-icon-url="dashicons-phone">
                <div class="ws_icon_image icon16 dashicons dashicons-phone"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Playlist Audio" data-icon-url="dashicons-playlist-audio">
                <div class="ws_icon_image icon16 dashicons dashicons-playlist-audio"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Playlist Video" data-icon-url="dashicons-playlist-video">
                <div class="ws_icon_image icon16 dashicons dashicons-playlist-video"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Plus" data-icon-url="dashicons-plus">
                <div class="ws_icon_image icon16 dashicons dashicons-plus"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Plus Alt" data-icon-url="dashicons-plus-alt">
                <div class="ws_icon_image icon16 dashicons dashicons-plus-alt"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Portfolio" data-icon-url="dashicons-portfolio">
                <div class="ws_icon_image icon16 dashicons dashicons-portfolio"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Post Status" data-icon-url="dashicons-post-status">
                <div class="ws_icon_image icon16 dashicons dashicons-post-status"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Post Trash" data-icon-url="dashicons-post-trash">
                <div class="ws_icon_image icon16 dashicons dashicons-post-trash"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Pressthis" data-icon-url="dashicons-pressthis">
                <div class="ws_icon_image icon16 dashicons dashicons-pressthis"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Products" data-icon-url="dashicons-products">
                <div class="ws_icon_image icon16 dashicons dashicons-products"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Redo" data-icon-url="dashicons-redo">
                <div class="ws_icon_image icon16 dashicons dashicons-redo"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Rss" data-icon-url="dashicons-rss">
                <div class="ws_icon_image icon16 dashicons dashicons-rss"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Schedule" data-icon-url="dashicons-schedule">
                <div class="ws_icon_image icon16 dashicons dashicons-schedule"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Screenoptions" data-icon-url="dashicons-screenoptions">
                <div class="ws_icon_image icon16 dashicons dashicons-screenoptions"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Search" data-icon-url="dashicons-search">
                <div class="ws_icon_image icon16 dashicons dashicons-search"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Share" data-icon-url="dashicons-share">
                <div class="ws_icon_image icon16 dashicons dashicons-share"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Share Alt" data-icon-url="dashicons-share-alt">
                <div class="ws_icon_image icon16 dashicons dashicons-share-alt"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Share Alt2" data-icon-url="dashicons-share-alt2">
                <div class="ws_icon_image icon16 dashicons dashicons-share-alt2"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Share1" data-icon-url="dashicons-share1">
                <div class="ws_icon_image icon16 dashicons dashicons-share1"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Shield" data-icon-url="dashicons-shield">
                <div class="ws_icon_image icon16 dashicons dashicons-shield"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Shield Alt" data-icon-url="dashicons-shield-alt">
                <div class="ws_icon_image icon16 dashicons dashicons-shield-alt"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Slides" data-icon-url="dashicons-slides">
                <div class="ws_icon_image icon16 dashicons dashicons-slides"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Smartphone" data-icon-url="dashicons-smartphone">
                <div class="ws_icon_image icon16 dashicons dashicons-smartphone"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Smiley" data-icon-url="dashicons-smiley">
                <div class="ws_icon_image icon16 dashicons dashicons-smiley"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Sort" data-icon-url="dashicons-sort">
                <div class="ws_icon_image icon16 dashicons dashicons-sort"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Sos" data-icon-url="dashicons-sos">
                <div class="ws_icon_image icon16 dashicons dashicons-sos"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Star Empty" data-icon-url="dashicons-star-empty">
                <div class="ws_icon_image icon16 dashicons dashicons-star-empty"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Star Filled" data-icon-url="dashicons-star-filled">
                <div class="ws_icon_image icon16 dashicons dashicons-star-filled"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Star Half" data-icon-url="dashicons-star-half">
                <div class="ws_icon_image icon16 dashicons dashicons-star-half"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Sticky" data-icon-url="dashicons-sticky">
                <div class="ws_icon_image icon16 dashicons dashicons-sticky"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Store" data-icon-url="dashicons-store">
                <div class="ws_icon_image icon16 dashicons dashicons-store"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Tablet" data-icon-url="dashicons-tablet">
                <div class="ws_icon_image icon16 dashicons dashicons-tablet"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Tag" data-icon-url="dashicons-tag">
                <div class="ws_icon_image icon16 dashicons dashicons-tag"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Tagcloud" data-icon-url="dashicons-tagcloud">
                <div class="ws_icon_image icon16 dashicons dashicons-tagcloud"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Testimonial" data-icon-url="dashicons-testimonial">
                <div class="ws_icon_image icon16 dashicons dashicons-testimonial"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Text" data-icon-url="dashicons-text">
                <div class="ws_icon_image icon16 dashicons dashicons-text"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Thumbs Down" data-icon-url="dashicons-thumbs-down">
                <div class="ws_icon_image icon16 dashicons dashicons-thumbs-down"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Thumbs Up" data-icon-url="dashicons-thumbs-up">
                <div class="ws_icon_image icon16 dashicons dashicons-thumbs-up"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Translation" data-icon-url="dashicons-translation">
                <div class="ws_icon_image icon16 dashicons dashicons-translation"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Twitter" data-icon-url="dashicons-twitter">
                <div class="ws_icon_image icon16 dashicons dashicons-twitter"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Undo" data-icon-url="dashicons-undo">
                <div class="ws_icon_image icon16 dashicons dashicons-undo"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Universal Access" data-icon-url="dashicons-universal-access">
                <div class="ws_icon_image icon16 dashicons dashicons-universal-access"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Universal Access Alt" data-icon-url="dashicons-universal-access-alt">
                <div class="ws_icon_image icon16 dashicons dashicons-universal-access-alt"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Unlock" data-icon-url="dashicons-unlock">
                <div class="ws_icon_image icon16 dashicons dashicons-unlock"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Update" data-icon-url="dashicons-update">
                <div class="ws_icon_image icon16 dashicons dashicons-update"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Upload" data-icon-url="dashicons-upload">
                <div class="ws_icon_image icon16 dashicons dashicons-upload"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Vault" data-icon-url="dashicons-vault">
                <div class="ws_icon_image icon16 dashicons dashicons-vault"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Video Alt" data-icon-url="dashicons-video-alt">
                <div class="ws_icon_image icon16 dashicons dashicons-video-alt"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Video Alt2" data-icon-url="dashicons-video-alt2">
                <div class="ws_icon_image icon16 dashicons dashicons-video-alt2"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Video Alt3" data-icon-url="dashicons-video-alt3">
                <div class="ws_icon_image icon16 dashicons dashicons-video-alt3"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Visibility" data-icon-url="dashicons-visibility">
                <div class="ws_icon_image icon16 dashicons dashicons-visibility"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Warning" data-icon-url="dashicons-warning">
                <div class="ws_icon_image icon16 dashicons dashicons-warning"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Welcome Add Page" data-icon-url="dashicons-welcome-add-page">
                <div class="ws_icon_image icon16 dashicons dashicons-welcome-add-page"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Welcome Comments" data-icon-url="dashicons-welcome-comments">
                <div class="ws_icon_image icon16 dashicons dashicons-welcome-comments"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Welcome Learn More" data-icon-url="dashicons-welcome-learn-more">
                <div class="ws_icon_image icon16 dashicons dashicons-welcome-learn-more"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Welcome View Site" data-icon-url="dashicons-welcome-view-site">
                <div class="ws_icon_image icon16 dashicons dashicons-welcome-view-site"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Welcome Widgets Menus" data-icon-url="dashicons-welcome-widgets-menus">
                <div class="ws_icon_image icon16 dashicons dashicons-welcome-widgets-menus"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Welcome Write Blog" data-icon-url="dashicons-welcome-write-blog">
                <div class="ws_icon_image icon16 dashicons dashicons-welcome-write-blog"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Wordpress" data-icon-url="dashicons-wordpress">
                <div class="ws_icon_image icon16 dashicons dashicons-wordpress"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Wordpress Alt" data-icon-url="dashicons-wordpress-alt">
                <div class="ws_icon_image icon16 dashicons dashicons-wordpress-alt"></div>
            </div><div class="ws_icon_option ws_icon_extra" title="Yes" data-icon-url="dashicons-yes">
                <div class="ws_icon_image icon16 dashicons dashicons-yes"></div>
            </div>
            <div class="ws_icon_option" data-icon-url="images/generic.png">
                <img src="images/generic.png">
            </div>	<div class="ws_icon_option ws_custom_image_icon" title="Custom image" style="display: none;">
                <img src="http://websites.unitone.ps/aghrady/wp-admin/images/loading.gif">
            </div>

            <div class="clear"></div>
        </div>
    </div>

        <div class="wrap">
            <?php screen_icon('edit-pages'); ?>
			<h2><?php _e('Dashboard Widgets', 'DashboardWidgets') ?></h2>
			<div style="background-color:#fff;border:1px solid #e1e1e1; padding:10px 20px;">
            <p style="font-weight:bold;"><?php _e('Customize Your Dashboard Main Page, New Layouts, you can simplisity customize your dashboard links to access quickly to your dashboard pages. You can add new row (access link), edit rows and delete row. ', 'DashboardWidgets') ?></p>

            <p  style="font-weight:bold;"><a target="_blank" href="https://developer.wordpress.org/resource/dashicons">
                    <?php _e('You can Choose the icons from Wordpress dashicons on this link ', 'DashboardWidgets') ?>  </a></p>
                <a href="#" class="openDashIcons"><?php _e('Or from this popup', 'DashboardWidgets') ?></a>


            <p  style="font-weight:bold;"><a target="_blank" href="http://fortawesome.github.io/Font-Awesome/icons/">
			<?php _e('Or You can Choose from Font-Awesome icons on this link ', 'DashboardWidgets') ?>  </a></p>

			<p style="font-weight:bold;"><?php _e('We have collected the most common wordpress post types and their icons to make it easy for you to choose the right icon for it,', 'DashboardWidgets') ?> <a href="#" class="open"><?php _e('Click Here', 'DashboardWidgets') ?></a> <?php _e('to open the window', 'DashboardWidgets') ?>.</p>
			<p style="font-weight:bold;"><?php _e('You Must enter the link after','DashboardWidgets'); ?> wp-admin/ <span> <?php _e(' For Example: ','DashboardWidgets'); ?> http://domain.com/wp-admin/<strong style="padding:3px;background-color:#8E8400;color:#fff;border-radius:5px;">edit.php</strong><?php _e(' Copy the highlight text ','DashboardWidgets'); ?> (edit.php).</span></p>
	
			<?php if(isset($_POST['data']) && isset($_POST['submit'])){
				
				$data = $_POST['data'];
				foreach($data as $k=>$dd){
					$res2[$k] = $dd['order'] ;
				}
				asort($res2);
				foreach($res2 as $k=>$val){
					$sorted[] = $data[$k];
				}
				$data = $sorted;
				update_option('dashboard-widgets', $data);		
				$cdw_show_another_widgets =(isset($_POST['cdw_show_another_widgets']) && $_POST['cdw_show_another_widgets']=='checked' )? 'checked': '';
				update_option('cdw_show_another_widgets', $cdw_show_another_widgets);			
				
				echo '<br> <h2 style="
				  color: green;
				  background-color:#f1f1f1;
				  height:15px;
				  margin:0 auto;
				  padding: 20px 50px;">'.__('Saved Successfully', 'DashboardWidgets').'</h2>';
			}else{
				$data =  get_option('dashboard-widgets'); 
				if(empty($data)  || isset($_POST['reset_default']) ){
					$data = cdw_get_default_data();
					if(isset($_POST['reset_default'])){
						 update_option('dashboard-widgets', $data);	
					}
				}
				$cdw_show_another_widgets =(get_option('cdw_show_another_widgets') == 'checked')? 'checked': '';
			
			}
			

			?>
			</div>
	
            <form method="post" action="">
				<?php settings_fields( 'disable-settings-group' ); ?>
            	<?php do_settings_sections( 'disable-settings-group' );  ?>
			<br/>
			
			<?php
				global $wp_roles;
				$all_roles = $wp_roles->roles;
				$all_roles = array_keys($all_roles); 
			?>
			
			<table class="cdw-table table table-bordered">
				<tr>
					<th width="30%"> <?php _e('Title', 'DashboardWidgets') ?> </th>
					<th width="20%">  <?php _e('icon', 'DashboardWidgets') ?>  </th>
					<th width="20%">  <?php _e('link', 'DashboardWidgets') ?> </th>
					<th width="5%"> <?php _e('Active', 'DashboardWidgets') ?>  </th>
					<?php foreach($all_roles as $role){
                        $role = str_replace('_',' ', $role);
					    ?>
					<th > <?php _e(ucwords ($role) , 'DashboardWidgets') ?>  </th>
					<?php } ?>	
					<th > <?php _e('Order', 'DashboardWidgets') ?>  </th>
					<th > <?php _e('Remove', 'DashboardWidgets') ?>  </th>
				</tr>		
				
			<?php foreach($data as $k=>$item){  ?>
			<tr data-id="<?= $k ?>">
				<td><input type="text" name="data[<?= $k ?>][title]"  value="<?php echo $item['title'] ?>" /></td>
				<td><input type="text" class="icon-input" name="data[<?= $k ?>][icon]" value="<?php echo $item['icon']  ?>" /></td>
				<td><input type="text" name="data[<?= $k ?>][link]" value="<?php echo $item['link']  ?>" /></td>
				<td><input type="checkbox" name="data[<?= $k ?>][status]" value="checked"  <?php echo $item['status']  ?>/></td>
				<?php foreach($all_roles as $role){ ?>
				<td><input type="checkbox" name="data[<?= $k ?>][<?= $role ?>]" value="checked"  <?php echo isset($item[$role])? $item[$role]: '' ?>/></td>
				<?php } ?>				
				
				<td><input type="number" name="data[<?= $k ?>][order]" value="<?= $k ?>"/></td>
				<td><a href="javascript:void(0);" class="remCF"><i class="fa fa-times"></i></a></td>
			</tr>
			<?php } ?>
			</table>
				<div class="add-new"><a href="javascript:void(0);" class="addCF"><i class="fa fa-plus"></i><?php _e('Add Row', 'DashboardWidgets') ?></a></div>
                
				<div style="float:left;width: 100%;margin-top: 10px">
					<input name="cdw_show_another_widgets" type="checkbox" value="checked" <?php echo $cdw_show_another_widgets ?>/>
					<?php _e('Don\'t hide another Wordpress default dashboard Widgets', 'DashboardWidgets') ?> 
				</div>
				<?php submit_button(); ?>
				<p class="submit">
					<input type="submit" name="reset_default" class="button button-danger def-button" value="<?php _e('ReSet to Defaults', 'DashboardWidgets') ?>">
				</p>
            </form>
        </div>	
		
		<br/>

<script src="//code.jquery.com/jquery-1.12.0.min.js"></script>
<script>
$(document).ready(function(){
	$(".addCF").click(function(){
		var key = $(".cdw-table tr:last-child").data('id');
		key = key+1;
		var newCol = '<tr data-id="'+key+'"><td><input type="text" name="data['+key+'][title]" value="Title" /></td>';
		newCol += '<td><input type="text" name="data['+key+'][icon]" value="fa fa-wordpress" /></td><td><input type="text" name="data['+key+'][link]" value="Link" /></td>';
		newCol += '<td><input type="checkbox" name="data['+key+'][status]" value="checked"  checked/></td>';
		
		var index, len;
		var a = [<?php echo '"'.implode('","', $all_roles).'"' ?>];
		for (index = 0, len = a.length; index < len; ++index) {
			if(a[index]=='administrator' || a[index]=='editor'){
				newCol += '<td><input type="checkbox" name="data['+key+']['+a[index]+']" value="checked"  checked/></td>';
			}else{
				newCol += '<td><input type="checkbox" name="data['+key+']['+a[index]+']" value="checked"/></td>';
			}
		}
		
		newCol += '<td><input type="number" name="data['+key+'][order]" value="'+key+'" /></td>';
		newCol += '<td><a href="javascript:void(0);" class="remCF"><i class="fa fa-times"></i></a></td></tr>';
		$(".cdw-table").append(newCol);
		});
		$(".cdw-table").on('click','.remCF',function(){
			$(this).parent().parent().remove();
		});
	$(".open").click(function(e){
		e.preventDefault();
		$('.modalDialog').fadeToggle();
	});
    $(".close").click(function(e){
        e.preventDefault();
        $('.modalDialog').fadeToggle();
    });

    ///////////
    var lastIconInput = '';

    $(".openDashIcons").click(function(e){ //".openDashIcons, .icon-input"
        e.preventDefault();
        $('.modalDashIcons').fadeToggle();
    });
	$(".closeDashIcons").click(function(e){
		e.preventDefault();
		$('.modalDashIcons').fadeToggle();
	});

	$('.modalDashIcons .ws_icon_option').click(function(e) {
	    var icon = $(this).attr('data-icon-url');
        alert(icon)
    });
		
});
</script>
				
		<?php
}

///////////////// Delete Default Widgets ////////////////
remove_action( 'welcome_panel', 'wp_welcome_panel' );
add_action('wp_dashboard_setup', 'remove_dashboard_widgets' );
function remove_dashboard_widgets() {
    global $wp_meta_boxes;
	$cdw_show_another_widgets =(get_option('cdw_show_another_widgets') == 'checked')? 'checked': '';
	if($cdw_show_another_widgets != 'checked'){
		unset($wp_meta_boxes['dashboard']);
	}
}
////////////////////////////////////////////////////////////

function cdw_get_default_data(){
	//echo '<link rel="stylesheet" type="text/css" href="'.plugins_url( 'dw_style.css', __FILE__ ).'" />';

				$items = $item = array();
				$item['title'] = __('View Site');
				$item['icon'] = 'dashicons-visibility';
				$item['link'] = 'site_url';
				$item['status'] = 'checked';
				$item['administrator'] = 'checked';
				$item['editor'] = 'checked';
				$item['author'] = 'checked';
				$item['contributor'] = 'checked';
				$item['order'] = 0;
				$items[] = $item;

				$item['title'] = __('Profile');
				$item['icon'] = 'dashicons-universal-access-alt';
				$item['link'] = 'profile.php';
				$item['status'] = 'checked';
				$item['administrator'] = 'checked';
				$item['editor'] = 'checked';
				$item['author'] = 'checked';
				$item['contributor'] = 'checked';
				$item['order'] = 0;
				$items[] = $item;
				 
				$item['title'] = __('Posts');
				$item['icon'] = 'dashicons-admin-post';
				$item['link'] = 'edit.php';
				$item['status'] = 'checked';
				$item['administrator'] = 'checked';
				$item['editor'] = 'checked';
				$item['author'] = 'checked';
				$item['contributor'] = 'checked';
				$item['order'] = 0;
				$items[] = $item;
					
				$item['title'] = __('Media');
				$item['icon'] = 'dashicons-admin-media';
				$item['link'] = 'upload.php';
				$item['status'] = 'checked';
				$item['administrator'] = 'checked';
				$item['editor'] = 'checked';
				$item['author'] = 'checked';
				$item['contributor'] = '';
				$item['order'] = 0;
				$items[] = $item;
				
				$item['title'] = __('Users');
				$item['icon'] = 'dashicons-admin-users';
				$item['link'] = 'users.php';
				$item['status'] = 'checked';
				$item['administrator'] = 'checked';
				$item['editor'] = '';
				$item['author'] = '';
				$item['contributor'] = '';
				$item['order'] = 0;
				$items[] = $item;
				
				$item['title'] = __('Pages');
				$item['icon'] = 'dashicons-admin-page';
				$item['link'] = 'edit.php?post_type=page';
				$item['status'] = 'checked';
				$item['administrator'] = 'checked';
				$item['editor'] = 'checked';
				$item['author'] = '';
				$item['contributor'] = '';
				$item['order'] = 0;
				$items[] = $item;
				
				$item['title'] = __('Plugins');
				$item['icon'] = 'dashicons-admin-plugins';
				$item['link'] = 'plugins.php';
				$item['status'] = 'checked';
				$item['administrator'] = 'checked';
				$item['editor'] = '';
				$item['author'] = '';
				$item['contributor'] = '';
				$item['order'] = 0;
				$items[] = $item;
				
				$item['title'] = __('Settings');
				$item['icon'] = 'dashicons-admin-settings';
				$item['link'] = 'options-general.php';
				$item['status'] = 'checked';
				$item['administrator'] = 'checked';
				$item['editor'] = '';
				$item['author'] = '';
				$item['contributor'] = '';
				$item['order'] = 0;
				$items[] = $item;
			return $items;
}

/////////////// Add Custome Widget //////////////////////
function custom_dashboard_widget(){
	echo '<link rel="stylesheet" type="text/css" href="'.plugins_url( 'dw_style.css', __FILE__ ).'" />';

	if(is_rtl()){
        echo '<style>#dashboard-widgets .dashicons{  margin-right: 0; margin-left: 40px; }</style>';
    }

    //echo '<h4>'.__('Welcome To your Dashboard', 'DashboardWidgets').'</h4>';

    global $current_user; // Use global
    get_currentuserinfo(); // Make sure global is set, if not set it.
    $website_url = get_bloginfo('url');
    $admin_url = site_url()."/wp-admin/";
    $widget_button_class = "main_bashboard_widget_button";
    
		$data =  get_option('dashboard-widgets'); 
		if(empty($data)){
			$data = cdw_get_default_data();
		}
		foreach($data as $item){ 
			if($item['status'] != 'checked') continue;
			$userRole = array_values($current_user->roles);
			$role = $userRole[0];
			if(!isset($item[$role]) ||  ($item[$role] != 'checked') ) continue;
						
				if(strpos($item['link'] , 'http') ===false){ //not full link
					$link = ($item['link'] != 'site_url')? $admin_url.$item['link'] : home_url();
				}else{
					$link = $item['link'];
				}

                $iconItem = $item['icon'];
                $iconItem = str_replace('dashicons ', '', $iconItem);
                $iconItem = str_replace('fa ', '', $iconItem);

				if(strpos($iconItem , 'dashicons-') !==false){
                    $icon = '<span class="dashicons '.$iconItem.'"></span>';
                }else{
                    $icon = '<i class="fa '.$iconItem.'"></i>';
                }


				echo '<div class="'.$widget_button_class.'">
					<a href="'.$link.'">
						'.$icon.'
						<h3>'.__($item['title']).'</h3>
					</a>
				</div>';
		}
    echo '</div>';
}
function add_custom_dashboard_widget(){
	
	error_reporting(0);
	//echo '<link rel="stylesheet" type="text/css" href="'.plugins_url( 'dw_style.css', __FILE__ ).'" />';

    wp_add_dashboard_widget('custom_dashboard_widget',__('Dashboard', 'DashboardWidgets'),'custom_dashboard_widget','rc_mdm_configure_my_rss_box');
}
add_action('wp_dashboard_setup', 'add_custom_dashboard_widget');

?>
