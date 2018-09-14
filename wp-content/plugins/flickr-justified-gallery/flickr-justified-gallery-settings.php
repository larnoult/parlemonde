<?php
/* 
Flickr Justified Gallery
Version: 3.5
Author: Miro Mannino
Author URI: http://miromannino.it

Copyright 2012 Miro Mannino (miro.mannino@gmail.com)
thanks to Dan Coulter for phpFlickr Class (dan@dancoulter.com)

This file is part of Flickr Justified Gallery Wordpress Plugin.

Flickr Justified Gallery Wordpress Plugin is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by the Free Software 
Foundation, either version 3 of the License, or (at your option) any later version.

Flickr Justified Gallery Wordpress Plugin is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with Flickr Justified 
Gallery Wordpress Plugin. If not, see <http://www.gnu.org/licenses/>.
*/

//uninstall plugin, remove the options for privacy
register_uninstall_hook( __FILE__, 'fjgwpp_plugin_uninstall');
if (!function_exists( 'fjgwpp_plugin_uninstall')) {
	function fjgwpp_plugin_uninstall() {
		if (get_option('$fjgwpp_userID')) {
			delete_option('$fjgwpp_userID');
		}
		if (get_option('$fjgwpp_APIKey')) {
			delete_option('$fjgwpp_APIKey');
		}
		if (get_option('$fjgwpp_maxPhotosPP')) {
			delete_option('$fjgwpp_maxPhotosPP');
		}
		if (get_option('$fjgwpp_imagesHeight')) {
			delete_option('$fjgwpp_imagesHeight');
		}
		if (get_option('$fjgwpp_lastRow')) {
			delete_option('$fjgwpp_lastRow');
		}
		if (get_option('$fjgwpp_fixedHeight')) {
			delete_option('$fjgwpp_fixedHeight');
		}
		if (get_option('$fjgwpp_pagination')) {
			delete_option('$fjgwpp_pagination');
		}
		if (get_option('$fjgwpp_lightbox')) {
			delete_option('$fjgwpp_lightbox');
		}
		if (get_option('$fjgwpp_provideColorbox')) {
			delete_option('$fjgwpp_provideColorbox');
		}
		if (get_option('$fjgwpp_provideSwipebox')) {
			delete_option('$fjgwpp_provideSwipebox');
		}
		if (get_option('$fjgwpp_captions')) {
			delete_option('$fjgwpp_captions');
		}
		if (get_option('$fjgwpp_showDescriptions')) {
			delete_option('$fjgwpp_showDescriptions');
		}
		if (get_option('$fjgwpp_randomize')) {
			delete_option('$fjgwpp_randomize');
		}
		if (get_option('$fjgwpp_margins')) {
			delete_option('$fjgwpp_margins');
		}
		if (get_option('$fjgwpp_openOriginals')) {
			delete_option('$fjgwpp_openOriginals');
		}
		if (get_option('$fjgwpp_bcontextmenu')) {
			delete_option('$fjgwpp_bcontextmenu');
		}
		if (get_option('$fjgwpp_flickrAPIWrapperVersion')) {
			delete_option('$fjgwpp_flickrAPIWrapperVersion');
		}
	}
}

// add the admin options page
add_action('admin_menu', 'fjgwpp_admin_add_page');
function fjgwpp_admin_add_page() {
	add_options_page('FlickrJustifiedGalleryWPPSettings', 'Flickr Justified Gallery', 'activate_plugins', 'fjgwpp', 'fjgwpp_settings');
}

function fjgwpp_settings() {
	global $fjgwpp_imagesHeight_default;
	global $fjgwpp_maxPhotosPP_default;
	global $fjgwpp_lastRow_default;
	global $fjgwpp_fixedHeight_default;
	global $fjgwpp_pagination_default;
	global $fjgwpp_lightbox_default;
	global $fjgwpp_provideColorbox_default;
	global $fjgwpp_provideSwipebox_default;
	global $fjgwpp_captions_default;
	global $fjgwpp_showDescriptions_default;
	global $fjgwpp_randomize_default;
	global $fjgwpp_margins_default;
	global $fjgwpp_openOriginals_default;
	global $fjgwpp_bcontextmenu_default;
	global $fjgwpp_flickrAPIWrapperVersion_default;

	//Get Values
	$fjgwpp_userID_saved = fjgwpp_getOption('userID', '');
	$fjgwpp_APIKey_saved = fjgwpp_getOption('APIKey', '');
	$fjgwpp_imagesHeight_saved = (int)fjgwpp_getOption('imagesHeight', $fjgwpp_imagesHeight_default);
	$fjgwpp_maxPhotosPP_saved = (int)fjgwpp_getOption('maxPhotosPP', $fjgwpp_maxPhotosPP_default);
	$fjgwpp_lastRow_saved = (int)fjgwpp_getOption('lastRow', $fjgwpp_lastRow_default);
	$fjgwpp_fixedHeight_saved = (int)fjgwpp_getOption('fixedHeight', $fjgwpp_fixedHeight_default);
	$fjgwpp_pagination_saved = fjgwpp_getOption('pagination', $fjgwpp_pagination_default);
	$fjgwpp_lightbox_saved = fjgwpp_getOption('lightbox', $fjgwpp_lightbox_default);
	$fjgwpp_provideColorbox_saved = (int)fjgwpp_getOption('provideColorbox', $fjgwpp_provideColorbox_default);
	$fjgwpp_provideSwipebox_saved = (int)fjgwpp_getOption('provideSwipebox', $fjgwpp_provideSwipebox_default);
	$fjgwpp_captions_saved = (int)fjgwpp_getOption('captions', $fjgwpp_captions_default);
	$fjgwpp_showDescriptions_saved = (int)fjgwpp_getOption('showDescriptions', $fjgwpp_showDescriptions_default);
	$fjgwpp_randomize_saved = (int)fjgwpp_getOption('randomize', $fjgwpp_randomize_default);
	$fjgwpp_margins_saved = (int)fjgwpp_getOption('margins', $fjgwpp_margins_default);
	$fjgwpp_openOriginals_saved = (int)fjgwpp_getOption('openOriginals', $fjgwpp_openOriginals_default);
	$fjgwpp_bcontextmenu_saved = (int)fjgwpp_getOption('bcontextmenu', $fjgwpp_bcontextmenu_default);
	$fjgwpp_flickrAPIWrapperVersion_saved = (int)fjgwpp_getOption('flickrAPIWrapperVersion', $fjgwpp_flickrAPIWrapperVersion_default);
	
	//Save Values
	if (isset($_POST['Submit'])) {

		$error = false;
		$error_msg = "";

		$fjgwpp_flickrAPIWrapperVersion_saved = (int)$_POST["fjgwpp_flickrAPIWrapperVersion"];
		if ($fjgwpp_flickrAPIWrapperVersion_saved == 0) {
			require_once("phpFlickr/phpFlickr.php");
		} else {
			require_once("phpFlickr_a" . $fjgwpp_flickrAPIWrapperVersion_saved . "/phpFlickr.php");
		}

		//Check the API Key
		$fjgwpp_APIKey_saved = trim(htmlentities($_POST["fjgwpp_APIKey"], ENT_QUOTES));
		$f = new phpFlickr($fjgwpp_APIKey_saved);

		if ($f->test_echo() == false) {
			$error = true;
			$error_msg .=	'<li>' . __('API Key is not valid', 'fjgwpp' ) . '</li>'; 
		}

		$fjgwpp_userID_saved = trim(htmlentities($_POST["fjgwpp_userID"], ENT_QUOTES));
		if (!$error) {
			if ($f->urls_getUserProfile($fjgwpp_userID_saved) == false) {
				$error = true;
				$error_msg .=	'<li>' . __('Invalid UserID', 'fjgwpp' ) . '</li>'; 		
			}
		}

		$fjgwpp_imagesHeight_saved = (int)$_POST["fjgwpp_imagesHeight"];
		if ($fjgwpp_imagesHeight_saved < 30) {
			$error = true;
			$error_msg .= '<li>' . __('The \'Images Height\' field must have a value greater than or equal to 30', 'fjgwpp' ) . '</li>';
		}
		$fjgwpp_maxPhotosPP_saved = (int)$_POST["fjgwpp_maxPhotosPP"];
		if ($fjgwpp_maxPhotosPP_saved <= 0) {
			$error = true;
			$error_msg .= '<li>' . __('The \'Photos per page\' field must have a value greater than 0', 'fjgwpp' ) . '</li>';
		}
		$fjgwpp_lastRow_saved = htmlentities($_POST["fjgwpp_lastRow"], ENT_QUOTES);

		if (isset($_POST["fjgwpp_fixedHeight"]))
			$fjgwpp_fixedHeight_saved = ((int)$_POST["fjgwpp_fixedHeight"] != 0)? 1:0;
		else
			$fjgwpp_fixedHeight_saved = 0;

		$fjgwpp_pagination_saved = htmlentities($_POST["fjgwpp_pagination"], ENT_QUOTES);
		$fjgwpp_lightbox_saved = htmlentities($_POST["fjgwpp_lightbox"], ENT_QUOTES);
		if (isset($_POST["fjgwpp_provideColorbox"]))
			$fjgwpp_provideColorbox_saved = ((int)$_POST["fjgwpp_provideColorbox"] != 0) ? 1:0;
		else
			$fjgwpp_provideColorbox_saved = 0;

		if (isset($_POST["fjgwpp_provideSwipebox"]))
			$fjgwpp_provideSwipebox_saved = ((int)$_POST["fjgwpp_provideSwipebox"] !=0) ? 1:0;
		else
			$fjgwpp_provideSwipebox_saved = 0;

		if (isset($_POST["fjgwpp_captions"]))
			$fjgwpp_captions_saved = ((int)$_POST["fjgwpp_captions"] != 0)? 1:0;
		else
			$fjgwpp_captions_saved = 0;

		if (isset($_POST["fjgwpp_showDescriptions"]))
			$fjgwpp_showDescriptions_saved = ((int)$_POST["fjgwpp_showDescriptions"] != 0)? 1:0;
		else
			$fjgwpp_showDescriptions_saved = 0;

		if (isset($_POST["fjgwpp_randomize"]))
			$fjgwpp_randomize_saved = ((int)$_POST["fjgwpp_randomize"] != 0)? 1:0;
		else
			$fjgwpp_randomize_saved = 0;

		if (isset($_POST["fjgwpp_openOriginals"]))
			$fjgwpp_openOriginals_saved = ((int)$_POST["fjgwpp_openOriginals"] != 0)? 1:0;
		else
			$fjgwpp_openOriginals_saved = 0;

		if (isset($_POST["fjgwpp_bcontextmenu"]))
			$fjgwpp_bcontextmenu_saved = ((int)$_POST["fjgwpp_bcontextmenu"] != 0)? 1:0;
		else
			$fjgwpp_bcontextmenu_saved = 0;

		$fjgwpp_margins_saved = (int)$_POST["fjgwpp_margins"];
		if ($fjgwpp_margins_saved < 0 || $fjgwpp_margins_saved > 30) {
			$error = true;
			$error_msg .= '<li>' . __('The \'Margins\' field must have a value greater or equal than 0, and not greater than 30', 'fjgwpp' ) . '</li>';
		}

		if ($error == false) {
			update_option('$fjgwpp_APIKey', $fjgwpp_APIKey_saved);
			update_option('$fjgwpp_userID', $fjgwpp_userID_saved);
			update_option('$fjgwpp_imagesHeight', $fjgwpp_imagesHeight_saved);
			update_option('$fjgwpp_maxPhotosPP', $fjgwpp_maxPhotosPP_saved);
			update_option('$fjgwpp_lastRow', $fjgwpp_lastRow_saved);
			update_option('$fjgwpp_fixedHeight', $fjgwpp_fixedHeight_saved);
			update_option('$fjgwpp_pagination', $fjgwpp_pagination_saved);
			update_option('$fjgwpp_lightbox', $fjgwpp_lightbox_saved);
			update_option('$fjgwpp_provideColorbox', $fjgwpp_provideColorbox_saved);
			update_option('$fjgwpp_provideSwipebox', $fjgwpp_provideSwipebox_saved);
			update_option('$fjgwpp_captions', $fjgwpp_captions_saved);
			update_option('$fjgwpp_showDescriptions', $fjgwpp_showDescriptions_saved);
			update_option('$fjgwpp_randomize', $fjgwpp_randomize_saved);
			update_option('$fjgwpp_margins', $fjgwpp_margins_saved);
			update_option('$fjgwpp_openOriginals', $fjgwpp_openOriginals_saved);
			update_option('$fjgwpp_bcontextmenu', $fjgwpp_bcontextmenu_saved);
			update_option('$fjgwpp_flickrAPIWrapperVersion', $fjgwpp_flickrAPIWrapperVersion_saved);
?>
		<div class="updated">
			<p><strong><?php _e('Settings updated.', 'fjgwpp' ); ?></strong></p>
		</div>
<?php
		}else{
?>
		<div class="updated">
			<p><strong><?php _e('Invalid values, the settings have not been updated', 'fjgwpp' ); ?></strong></p>
			<ul style="color:red"><?php echo($error_msg); ?></ul>
		</div>
<?php
		}
	}
?>

	<style type="text/css">
		#poststuff h3 { cursor: auto; }
		.justified-gallery-settings .card { max-width: 1200px; }
	</style>

			 
	<div class="wrap justified-gallery-settings">
		<h1>Flickr Justified Gallery</h1>

		<div>

			<div class="card">

				<h2><?php _e('Help', 'fjgwpp' ); ?></h2>
				<div class="inside">
					<p>
						<?php _e('To display the default user\'s photostream, create a page and simply write the following shortcode where you want to display the gallery.', 'fjgwpp' ); ?>
						<div style="margin-left: 30px">
							<pre>[flickr_photostream]</pre>
						</div>
					</p>
					<p>
						<?php _e('However, you can also use the attributes to have settings that are different than the defaults. For example:', 'fjgwpp' ); ?>
						<div style="margin-left: 30px">
							<pre>[flickr_photostream max_num_photos="50" no_pages="true"]</pre>
							<?php _e('displays the latest 50 photos of the default user photostream, without any page navigation. (the other settings are the defaults)', 'fjgwpp' ); ?>
						</div>
					</p>
					<p>
						<?php _e('You can also configure it to show other photostreams. For example:', 'fjgwpp' ); ?>
						<div style="margin-left: 30px">
							<pre>[flickr_photostream user_id="67681714@N03"]</pre>
							<?php _e('displays the photostream of the specified user, no matter what is the default user ID in the settings. Remember that you can use <a href="http://idgettr.com/" target="_blank">idgettr</a> to retrieve the <code>user_id</code>.', 'fjgwpp' ); ?>
						</div>
					</p>

		
					<h4><?php _e('Sets', 'fjgwpp' ); ?></h4>
					<p>
						<?php _e('To show the photos of a particular photo set (also called "album"), you need to know its <code>photoset_id</code>.', 'fjgwpp' ); ?>
						<?php _e('For example, the <code>photoset_id</code> of the photo set located in the URL:', 'fjgwpp' ); ?>
						<code>http://www.flickr.com/photos/miro-mannino/sets/72157629228993613/</code>
						<?php _e('is: ', 'fjgwpp' ); ?>
						<code>72157629228993613</code>.
						<?php _e('You can see that it is always the number after the word \'/sets/\'.', 'fjgwpp' ); ?>
						<div>
							<?php _e('To show a particular photoset, you need to use the <code>flickr_set</code> shortcode, and specify the <code>photoset_id</code> with the attribute <code>id</code>. For example:', 'fjgwpp' ); ?>
							<div style="margin-left: 30px">
								<pre>[flickr_set id="72157629228993613"]</pre>
							</div>
						</div>
					</p>

					<h4><?php _e('Galleries', 'fjgwpp' ); ?></h4>
					<p>
						<?php _e('To show the photos of a particular gallery, you need to know the <code>user_id</code> of the user that owns it, and its <code>id</code>.', 'fjgwpp' ); ?>
						<?php _e('For example, the <code>id</code> of the gallery located in the URL:', 'fjgwpp' ); ?>
						<code>http://www.flickr.com/photos/miro-mannino/galleries/72157636382842016/</code>
						<?php _e('is: ', 'fjgwpp' ); ?>
						<code>72157636382842016</code>.
						<?php _e('You can see that it is always the number after the word \'/galleries/\'.', 'fjgwpp' ); ?>
						<div>
							<?php _e('To show a particular gallery, you need to use the <code>flickr_gallery</code> shortcode, and specify the <code>user_id</code> with the attribute <code>user_id</code>, and the <code>gallery_id</code> with the attribute <code>id</code>. For example:', 'fjgwpp' ); ?>
							<div style="margin-left: 30px">
								<pre>[flickr_gallery user_id="67681714@N03" id="72157636382842016"]</pre>
							</div>
						</div>
						<?php _e('Remember that, if the gallery is owned by the default user (specified in the settings), you don\'t need the <code>user_id</code> attribute in the shortcode.', 'fjgwpp' ); ?>
					</p>

					<h4><?php _e('Group pools', 'fjgwpp' ); ?></h4>
					<p>
						<?php _e('To show the photos of a particular group pool, you need to know the group id, that you can retrieve using <a href="http://idgettr.com/" target="_blank">idgettr</a>.', 'fjgwpp' ); ?>
						<div>
							<?php _e('To show a particular group pool, you need to use the <code>flickr_group</code> shortcode, and specify the group id with the attribute <code>id</code>. For example:', 'fjgwpp' ); ?>
							<div style="margin-left: 30px">
								<pre>[flickr_group id="1563131@N22"]</pre>
							</div>
						</div>
					</p>

					<h4><?php _e('Tags', 'fjgwpp' ); ?></h4>
					<p>
						<?php _e('To show the photos that have some particular tags, you need to use the <code>flickr_tags</code> shortcode, and specify the <code>user_id</code> and the tags with the attribute <code>tags</code>, as a comma-delimited list of words. For example:', 'fjgwpp' ); ?>
						<div style="margin-left: 30px">
							<pre>[flickr_tags user_id="67681714@N03" tags="cat, square, nikon"]</pre>
							<?php _e('Displays photos with one or more of the tags listed (the list is viewed as an OR combination, that is the default behavior).', 'fjgwpp' ); ?>
						</div>
						<p>
							<?php _e('You can also exclude results that match a term by prepending it with a <code>-</code> character.', 'fjgwpp' ); ?>
							<?php _e('Then, you can choose to use the list as a OR combination of tags (to return photos that have <b>any</b> tag), or an AND combination (to return photos that have <b>all</b> the tags).', 'fjgwpp' ); ?>
							<?php _e('To do this, you need to use the <code>tags_mode</code>, specifying "any" or "all". For example:', 'fjgwpp' ); ?>						
							<div style="margin-left: 30px">
								<pre>[flickr_tags user_id="67681714@N03" tags="cat, square, nikon" tags_mode="all"]</pre>
								<?php _e('Displays photos with all the tags listed (the list is viewed as an AND combination).', 'fjgwpp' ); ?>
							</div>
						</p>
						<?php _e('Remember that, if the photo that you want to display is owned by the default user (specified in the settings), you don\'t need the <code>user_id</code> attribute in the shortcode.', 'fjgwpp' ); ?>
					</p>

				</div>
			</div>

			<div class="card">

				<h2><?php _e('Settings', 'fjgwpp' ); ?></h2>
				<div class="inside">

					<form method="post" name="options" target="_self">
						<h4><?php _e('Global Settings', 'fjgwpp' ); ?></h4>

						<table class="form-table">
							<tr>
								<th scope="row">
									<label><?php _e('Flickr API Key', 'fjgwpp'); ?></label>
								</th>
								<td>
									<label for="fjgwpp_APIKey">
									<input type="text" name="fjgwpp_APIKey" 
										value="<?php echo($fjgwpp_APIKey_saved); ?>"
										style="margin-right:10px"
									/> 	
									<?php _e('Get your Flickr API Key from ', 'fjgwpp' ); ?><a href="http://www.flickr.com/services/api/keys/" target="_blank">Flickr API</a>
									<p><?php _e('You can\'t use an attribute to change this setting', 'fjgwpp'); ?></p>
									</label>
								</td>
							</tr>
						</table>

						<h4><?php _e('Default Settings', 'fjgwpp' ); ?></h4>

						<table class="form-table">
							<tr>
								<th scope="row"><?php _e('User ID', 'fjgwpp' ); ?></th>
								<td>
									<label for="fjgwpp_userID">
										<input type="text" name="fjgwpp_userID"
											value="<?php echo($fjgwpp_userID_saved); ?>"
											style="margin-right:10px"
										/>
										<?php _e('Get the User ID from ', 'fjgwpp' ); ?><a href="http://idgettr.com/" target="_blank">idgettr</a>
										<p><?php echo( __('You can use the <code>', 'fjgwpp') . 'user_id' . __('</code> attribute to change this default value', 'fjgwpp') ); ?></p>
									</label>
								</td>
							</tr>
							<tr>
								<th scope="row"><?php _e('Images Height (in px)', 'fjgwpp' ); ?></th>
								<td>
									<label for="fjgwpp_imagesHeight">
										<input type="text" name="fjgwpp_imagesHeight" 
											value="<?php echo($fjgwpp_imagesHeight_saved); ?>"
										/>
										<p><?php echo( __('You can use the <code>', 'fjgwpp') . 'images_height' . __('</code> attribute to change this default value', 'fjgwpp') ); ?></p>
									</label>
								</td>
							</tr>
							<tr>
								<th scope="row"><?php _e('Maximum number of photos per page', 'fjgwpp' ); ?></th>
								<td>
									<label for="fjgwpp_maxPhotosPP">
										<input type="text" name="fjgwpp_maxPhotosPP" 
											value="<?php echo($fjgwpp_maxPhotosPP_saved); ?>"
										/>
										<p><?php echo( __('You can use the <code>', 'fjgwpp') . 'max_num_photos' . __('</code> attribute to change this default value', 'fjgwpp') ); ?></p>
									</label> 	
								</td>
							</tr>
							<tr>
								<th scope="row"><?php _e('Last Row', 'fjgwpp' ); ?></th>
								<td>
									<label for="">
										<select name="fjgwpp_lastRow" style="margin-right:5px">
											<option value="justify" <?php if ($fjgwpp_lastRow_saved === 'justify') { echo('selected="selected"'); }; ?> ><?php _e('Justify', 'fjgwpp' );?></option>
											<option value="nojustify" <?php if ($fjgwpp_lastRow_saved === 'nojustify') { echo('selected="selected"'); }; ?> ><?php _e('No justify', 'fjgwpp' ); ?></option>
											<option value="hide" <?php if ($fjgwpp_lastRow_saved === 'hide') { echo('selected="selected"'); }; ?> ><?php _e('Hide if it cannot be justified', 'fjgwpp' ); ?></option>
										</select>
										<p><?php echo( __('You can use the <code>', 'fjgwpp') . 'last_row' . __('</code> attribute to change this default value (with the value <code>justify</code>, <code>nojustify</code> or <code>hide</code>)', 'fjgwpp') ); ?></p>
									</label>
								</td>
							</tr>
							<tr>
								<th scope="row"><?php _e('Fixed Height', 'fjgwpp' ); ?></th>
								<td>
									<label for="fjgwpp_fixedHeight">
										<input type="checkbox" name="fjgwpp_fixedHeight" 
											<?php if ($fjgwpp_fixedHeight_saved == 1) { echo('checked="checked"'); }; ?> 
											value="1"
											style="margin-right:5px"
										/>
										<?php _e('If enabled, each row has the same height, but the images will be cut more.', 'fjgwpp' ); ?></li>
										<p><?php echo( __('You can use the <code>', 'fjgwpp') . 'fixed_height' . __('</code> attribute to change this default value (with the value <code>true</code> or <code>false</code>)', 'fjgwpp') ); ?></p>
									</label>
								</td>
							</tr>
							<tr>
								<th scope="row"><?php _e('Pagination', 'fjgwpp' ); ?></th>
								<td>
									<label for="fjgwpp_pagination">
										<select name="fjgwpp_pagination" style="margin-right:5px">
											<option value="none" <?php if ($fjgwpp_pagination_saved === 'none') { echo('selected="selected"'); }; ?> ><?php _e('None', 'fjgwpp'); ?></option>
											<option value="prevnext" <?php if ($fjgwpp_pagination_saved === 'prevnext') { echo('selected="selected"'); }; ?> ><?php _e('Previous and Next', 'fjgwpp'); ?></option>
											<option value="numbers" <?php if ($fjgwpp_pagination_saved === 'numbers') { echo('selected="selected"'); }; ?> ><?php _e('Page Numbers', 'fjgwpp'); ?></option>
										</select>
										<?php _e('If enabled, navigation buttons will be shown, and you can see the older photos.<br/><i>Use only one instance per page with this settings enabled!</i>', 'fjgwpp' ); ?></li>
										<p><?php echo( __('You can use the <code>', 'fjgwpp') . 'pagination' . __('</code> attribute to change this default value (with the value <code>none</code>, <code>prevnext</code> or <code>numbers</code>)', 'fjgwpp') ); ?></p>
									</label>
								</td>
							</tr>
							<tr>
								<th scope="row"><?php _e('Lightbox', 'fjgwpp' ); ?></th>
								<td>
									<label for="fjgwpp_lightbox">
									<select name="fjgwpp_lightbox" style="margin-right:5px">
										<option value="none" <?php if ($fjgwpp_lightbox_saved === 'none') { echo('selected="selected"'); }; ?> ><?php _e('None', 'fjgwpp'); ?></option>
										<option value="colorbox" <?php if ($fjgwpp_lightbox_saved === 'colorbox') { echo('selected="selected"'); }; ?> >Colorbox</option>
										<option value="swipebox" <?php if ($fjgwpp_lightbox_saved === 'swipebox') { echo('selected="selected"'); }; ?> >Swipebox</option>
									</select>
									<p><?php echo( __('You can use the <code>', 'fjgwpp') . 'lightbox' . __('</code> attribute to change this default value (with the value <code>none</code>, <code>colorbox</code> or <code>swipebox</code>).', 'fjgwpp') ); ?></p>
									</label>
									<br/>
									<label for="fjgwpp_provideColorbox">
										<input type="checkbox" name="fjgwpp_provideColorbox" 
											<?php if ($fjgwpp_provideColorbox_saved == 1) { echo('checked="checked"'); }; ?> 
											value="1"
											style="margin-right:5px"
										/>
										<?php _e('Provide Colorbox', 'fjgwpp' ); ?>
									</label>
									<span>&nbsp;</span>
									<label for="fjgwpp_provideSwipebox">
										<input type="checkbox" name="fjgwpp_provideSwipebox" 
											<?php if ($fjgwpp_provideSwipebox_saved == 1) { echo('checked="checked"'); }; ?> 
											value="1"
											style="margin-right:5px"
										/>
										<?php _e('Provide Swipebox', 'fjgwpp' ); ?>
									</label>
									<p>
										<?php echo( __('Decide to include the lightbox libraries. Without them checked, make sure that you have installed the chosen lightboxes with a plugin (e.g. ', 'fjgwpp' ) 
											. '<a href="https://wordpress.org/plugins/responsive-lightbox/">Responsive Lightbox</a>, ' 
											. '<a href="http://wordpress.org/extend/plugins/jquery-colorbox/">jQuery Colorbox</a>, ' 
											. '<a href="http://wordpress.org/extend/plugins/lightbox-plus/">Lightbox Plus Colorbox</a>).'); 
										?>
									</p>
								</td>
							</tr>
							<tr>
								<th scope="row"><?php _e('Captions', 'fjgwpp' ); ?></th>
								<td>
									<label for="fjgwpp_captions">
									<input type="checkbox" name="fjgwpp_captions" 
										<?php if ($fjgwpp_captions_saved == 1) { echo('checked="checked"'); }; ?> 
										value="1" 
										style="margin-right:5px"
									/>
									<?php _e('If enabled, the title of the photo will be shown over the image when the mouse is over. Note: <i>captions, with small images, become aesthetically unpleasing</i>.', 'fjgwpp'); ?></li>
										<p><?php echo( __('You can use the <code>', 'fjgwpp') . 'captions' . __('</code> attribute to change this default value (with the value <code>true</code> or <code>false</code>)', 'fjgwpp') ); ?></p>
									</label>
								</td>
							</tr>
							<tr>
								<th scope="row"><?php _e('Show descriptions', 'fjgwpp' ); ?></th>
								<td>
									<label for="fjgwpp_showDescriptions">
									<input type="checkbox" name="fjgwpp_showDescriptions" 
										<?php if ($fjgwpp_showDescriptions_saved == 1) { echo('checked="checked"'); }; ?> 
										value="1" 
										style="margin-right:5px"
									/>
									<?php _e('If the captions are enabled, the descriptions will be shown inside the thumbnail captions. Note: <i>the descriptions is not shown inside the lightbox, because Colorbox and Lightbox
									don\'t support this feature</i>.', 'fjgwpp'); ?></li>
										<p><?php echo( __('You can use the <code>', 'fjgwpp') . 'show_descriptions' . __('</code> attribute to change this default value (with the value <code>true</code> or <code>false</code>)', 'fjgwpp') ); ?></p>
									</label>
								</td>
							</tr>
							<tr>
								<th scope="row"><?php _e('Randomize order', 'fjgwpp' ); ?></th>
								<td>
									<label for="fjgwpp_randomize">
										<input type="checkbox" name="fjgwpp_randomize" 
											<?php if ($fjgwpp_randomize_saved == 1) { echo('checked="checked"'); }; ?> 
											value="1"
											style="margin-right:5px"
										/>
										<?php _e('If enabled, the photos of the same page are randomized.', 'fjgwpp' ); ?></li>
										<p><?php echo( __('You can use the <code>', 'fjgwpp') . 'randomize' . __('</code> attribute to change this default value (with the value <code>true</code> or <code>false</code>)', 'fjgwpp') ); ?></p>
									</label>
								</td>
							</tr>
							<tr>
								<th scope="row"><?php _e('Margin between the images', 'fjgwpp' ); ?></th>
								<td>
									<label for="fjgwpp_margins">
										<input type="text" name="fjgwpp_margins" 
											value="<?php echo($fjgwpp_margins_saved); ?>"
											style="margin-right:10px"
										/>
										<p><?php echo( __('You can use the <code>', 'fjgwpp') . 'margins' . __('</code> attribute to change this default value', 'fjgwpp') ); ?></p>
									</label> 	
								</td>
							</tr>
							<tr>
								<th scope="row"><?php _e('Open original images', 'fjgwpp' ); ?></th>
								<td>
									<label for="fjgwpp_openOriginals">
										<input type="checkbox" name="fjgwpp_openOriginals" 
											<?php if ($fjgwpp_openOriginals_saved == 1) { echo('checked="checked"'); }; ?> 
											value="1"
											style="margin-right:5px"
										/>
										<?php _e('If enabled, the lightbox will show the original images if they are available. Consider to leave this option off if your original images are very large.', 'fjgwpp' ); ?></li>
										<p><?php echo( __('You can use the <code>', 'fjgwpp') . 'open_originals' . __('</code> attribute to change this default value (with the value <code>true</code> or <code>false</code>)', 'fjgwpp') ); ?></p>
									</label>
								</td>
							</tr>
							<tr>
								<th scope="row"><?php _e('Block right click', 'fjgwpp' ); ?></th>
								<td>
									<label for="fjgwpp_bcontextmenu">
										<input type="checkbox" name="fjgwpp_bcontextmenu" 
											<?php if ($fjgwpp_bcontextmenu_saved == 1) { echo('checked="checked"'); }; ?> 
											value="1"
											style="margin-right:5px"
										/>
										<?php _e('If enabled, the context menu will be blocked, so for the user is more difficult to save the images', 'fjgwpp' ); ?></li>
										<p><?php echo( __('You can use the <code>', 'fjgwpp') . 'block_contextmenu' . __('</code> attribute to change this default value (with the value <code>true</code> or <code>false</code>)', 'fjgwpp') ); ?></p>
									</label>
								</td>
							</tr>
							<tr>
								<th scope="row"><?php _e('Flickr API Wrapper Version', 'fjgwpp' ); ?></th>
								<td>
									<label for="fjgwpp_flickrAPIWrapperVersion">
										<select name="fjgwpp_flickrAPIWrapperVersion" style="margin-right:5px">
											<option value="0" <?php if ($fjgwpp_flickrAPIWrapperVersion_saved === 0) { echo('selected="selected"'); }; ?> >phpFlickr</option>
											<option value="1" <?php if ($fjgwpp_flickrAPIWrapperVersion_saved === 1) { echo('selected="selected"'); }; ?> >phpFlickr (alternative version)</option>
										</select>
										<p><?php echo( __('If you have some problems to communicate with the Flickr\'s API could be useful to change the current version of phpFlickr', 'fjgwpp') ); ?></p>
									</label> 	
								</td>
							</tr>
						</table>

						<p>
							<input type="submit" class="button-primary" name="Submit" value="<?php _e('Save Changes', 'fjgwpp' ); ?>" />
						</p>
					</form>
				</div>
			</div>

			<div class="card">
				<h2><?php _e('Help the project', 'fjgwpp' ); ?></h2>
				<div class="inside">
					<p>
						<?php _e('Help the project to grow. Donate something, or simply <a href="http://wordpress.org/plugins/flickr-justified-gallery" target="_blank">rate the plugin on Wordpress</a>.', 'fjgwpp' ); ?>
						<form action="https://www.paypal.com/<cgi-bin/webscr" method="post" target="_top">
							<input type="hidden" name="cmd" value="_s-xclick">
							<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----
							">
							<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
							<img alt="" border="0" src="https://www.paypalobjects.com/it_IT/i/scr/pixel.gif" width="1" height="1">
						</form>
					</p>
				</div>
			</div>

		</div>
	</div>

<?php 
}
?>