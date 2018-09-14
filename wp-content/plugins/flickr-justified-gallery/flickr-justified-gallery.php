<?php
/* 
Plugin Name: Flickr Justified Gallery
Plugin URI: http://miromannino.it/projects/flickr-justified-gallery/
Description: Shows the Flickr photostream, sets and galleries, with an high quality justified gallery.
Version: 3.5
Author: Miro Mannino
Author URI: http://miromannino.com/about-me/

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

//Defaults
$fjgwpp_imagesHeight_default = '120';
$fjgwpp_maxPhotosPP_default = '20';
$fjgwpp_lastRow_default = 'justify';
$fjgwpp_fixedHeight_default = '0';
$fjgwpp_pagination_default = 'none';
$fjgwpp_lightbox_default = 'none';
$fjgwpp_provideColorbox_default = '0';
$fjgwpp_provideSwipebox_default = '1';
$fjgwpp_captions_default = '1';
$fjgwpp_showDescriptions_default = '0';
$fjgwpp_randomize_default = '0';
$fjgwpp_margins_default = '1';
$fjgwpp_openOriginals_default = '0';
$fjgwpp_bcontextmenu_default = '0';
$fjgwpp_flickrAPIWrapperVersion_default = '0';

//Add the link to the plugin page
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'fjgwpp_plugin_settings_link' );
function fjgwpp_plugin_settings_link($links) { 
	$settings_link = '<a href="options-general.php?page=fjgwpp.php">Settings</a>'; 
	array_unshift($links, $settings_link); 
	return $links; 
}

//Activation hook, we check that the upload dir is writable
register_activation_hook( __FILE__ , 'fjgwpp_plugin_activate');
if (!function_exists( 'fjgwpp_plugin_uninstall')) {
	function fjgwpp_plugin_activate() {
		$upload_dir = wp_upload_dir();
		@mkdir($upload_dir['basedir'].'/phpFlickrCache');
		if (!is_writable($upload_dir['basedir'].'/phpFlickrCache')) {
			deactivate_plugins(basename(__FILE__)); // Deactivate ourself
			wp_die(__('Flickr Justified Gallery can\'t be activated: the cache Folder is not writable', 'fjgwpp') 
				. ' (' . $upload_dir['basedir'] .'/phpFlickrCache' . ')'
			);
		}
	}
}

//Add the language and the permalink
add_action('init', 'fjgwpp_init');
function fjgwpp_init() {
	/* languages */
	load_plugin_textdomain('fjgwpp', false, dirname(plugin_basename( __FILE__ )) . '/languages/');
}

//Register with hook 'wp_enqueue_scripts' which can be used for front end CSS and JavaScript
add_action('wp_enqueue_scripts', 'fjgwpp_addCSSandJS');
function fjgwpp_addCSSandJS() {

	//Register styles
	wp_register_style('justifiedGallery', plugins_url('css/justifiedGallery.min.css', __FILE__), NULL, 'v3.6');
	wp_register_style('flickrJustifiedGalleryWPPlugin', plugins_url('css/flickrJustifiedGalleryWPPlugin.css', __FILE__), NULL, 'v3.6');

	//Register scripts
	wp_register_script('justifiedGallery', plugins_url('js/jquery.justifiedGallery.min.js', __FILE__), array('jquery'), '', true);
	wp_register_script('flickrJustifiedGalleryWPPlugin', plugins_url('js/flickrJustifiedGalleryWPPlugin.js', __FILE__), array('jquery', 'justifiedGallery'), '', true);

	if (fjgwpp_getOption('provideColorbox')) {
		wp_register_style('colorbox', plugins_url('lightboxes/colorbox/colorbox.css', __FILE__));
		wp_register_script('colorbox', plugins_url('lightboxes/colorbox/jquery.colorbox-min.js', __FILE__), array('jquery'), '', true);
	}
	if (fjgwpp_getOption('provideSwipebox')) {
		wp_register_style('swipebox', plugins_url('lightboxes/swipebox/css/swipebox.min.css', __FILE__));
		wp_register_script('swipebox', plugins_url('lightboxes/swipebox/js/jquery.swipebox.min.js', __FILE__), array('jquery'), '', true);
	}

	//Enqueue styles
	wp_enqueue_style('justifiedGallery');
	wp_enqueue_style('flickrJustifiedGalleryWPPlugin');
	if (fjgwpp_getOption('provideColorbox')) wp_enqueue_style('colorbox');
	if (fjgwpp_getOption('provideSwipebox')) wp_enqueue_style('swipebox');

	//Enqueue scripts
	wp_enqueue_script('jquery');
	if (fjgwpp_getOption('provideColorbox')) wp_enqueue_script('colorbox');
	if (fjgwpp_getOption('provideSwipebox')) wp_enqueue_script('swipebox');
	wp_enqueue_script('justifiedGallery');
	wp_enqueue_script('flickrJustifiedGalleryWPPlugin');
}

function fjgwpp_formatError($errorMsg) {
	return '<div class="flickr-justified-gallery-error"><span style="color:red">' 
		. __('Flickr Justified Gallery Plugin error', 'fjgwpp') 
		. ': </span><span class="flickr-justified-gallery-error-msg">' . $errorMsg . '</span></div>';
}

function fjgwpp_formatFlickrAPIError($errorMsg) {
	return '<div class="flickr-justified-gallery-error"><span style="color:red">' 
		. __('Flickr API error', 'fjgwpp') 
		. ': </span><span class="flickr-justified-gallery-error-msg">' . $errorMsg . '</span></div>';
}

function fjgwpp_getOption($name, $default = '') {
	$key = (get_option('$fjgwpp_' . $name) === false ? '$flickr_photostream_' : '$fjgwpp_') . $name;
	return get_option($key, $default);
}

function fjgwpp_createGallery($action, $atts) {
	global $fjgwpp_imagesHeight_default;
	global $fjgwpp_maxPhotosPP_default;
	global $fjgwpp_lastRow_default;
	global $fjgwpp_fixedHeight_default;
	global $fjgwpp_pagination_default;
	global $fjgwpp_lightbox_default;
	global $fjgwpp_captions_default;
	global $fjgwpp_showDescriptions_default;
	global $fjgwpp_randomize_default;
	global $fjgwpp_margins_default;
	global $fjgwpp_openOriginals_default;
	global $fjgwpp_bcontextmenu_default;
	global $fjgwpp_flickrAPIWrapperVersion_default;
	static $shortcode_unique_id = 0;
	$ris = "";
	
	$page_num = (get_query_var('page')) ? get_query_var('page') : 1;
	$flickrGalID = 'flickrGal' . $shortcode_unique_id;

	//Options-----------------------
	extract( shortcode_atts( array(
		//left value: the variable to set (e.g. user_id option in shortcode set the variable $user_id in the function scope)
		//right value: the default value, in our case we take this values from the options where we store them.
		'user_id' => fjgwpp_getOption('userID'),
		'id' => NULL,
		'tags' => NULL,
		'tags_mode' => 'any',
		'images_height' => fjgwpp_getOption('imagesHeight', $fjgwpp_imagesHeight_default), // Flickr images size
		'max_num_photos' => fjgwpp_getOption('maxPhotosPP', $fjgwpp_maxPhotosPP_default), // Max number of Photos	
		'last_row' => fjgwpp_getOption('lastRow', $fjgwpp_lastRow_default),
		'fixed_height' => fjgwpp_getOption('fixedHeight', $fjgwpp_fixedHeight_default) == 1,
		'lightbox' => fjgwpp_getOption('lightbox', $fjgwpp_lightbox_default),
		'captions' => fjgwpp_getOption('captions', $fjgwpp_captions_default) == 1,
		'show_descriptions' => fjgwpp_getOption('showDescriptions', $fjgwpp_showDescriptions_default) == 1,
		'randomize' => fjgwpp_getOption('randomize', $fjgwpp_randomize_default) == 1,
		'pagination' => fjgwpp_getOption('pagination', $fjgwpp_pagination_default),
		'margins' => fjgwpp_getOption('margins', $fjgwpp_margins_default),
		'open_originals' => fjgwpp_getOption('openOriginals', $fjgwpp_openOriginals_default) == 1,
		'block_contextmenu' => fjgwpp_getOption('bcontextmenu', $fjgwpp_bcontextmenu_default) == 1,
		'flickrAPIWrapperVersion' => fjgwpp_getOption('flickrAPIWrapperVersion', $fjgwpp_flickrAPIWrapperVersion_default) == 0
	), $atts ) );

	//Trim string options
	$user_id = trim($user_id);
	$id = trim($id);
	$lightbox = trim($lightbox);
	$last_row = trim($last_row);

	if ($flickrAPIWrapperVersion == 0) {
		require_once("phpFlickr/phpFlickr.php");
	} else {
		require_once("phpFlickr_a" . $flickrAPIWrapperVersion . "/phpFlickr.php");
	}

	//LEGACY for the old options
	if($pagination === '1') $pagination = 'prevnext';
	else if ($pagination !== 'none' && $pagination !== 'prevnext' && $pagination !== 'numbers') $pagination = 'none';
	if($lightbox === '1') $lightbox = 'colorbox';
	if($lightbox === '0') $lightbox = 'none';

	$images_height = (int)$images_height;
	if($images_height < 30) $images_height = 30;

	$max_num_photos = (int)$max_num_photos;
	if ($max_num_photos < 1) $max_num_photos = 1;

	$margins = (int)$margins;
	if ($margins < 0) $margins = 1;
	if ($margins > 30) $margins = 30;

	if($pagination === 'none') $page_num = 1;

	//-----------------------------

	//Inizialization---------------
	$flickrAPIKey = trim(fjgwpp_getOption('APIKey')); //Flickr API Key
	
	$f = new phpFlickr($flickrAPIKey);
	$upload_dir = wp_upload_dir();
	$f->enableCache("fs", $upload_dir['basedir']."/phpFlickrCache");

	$photos_url = array();
	$photos = array();
	$photos_main_index = '';

	$maximum_pages_nums = 10; //TODO configurable?

	//Errors-----------------------
	if ($action === 'phs' || $action === 'gal' || $action === 'tag') {
		if (!isset($user_id) || strlen($user_id) == 0) 
			return(fjgwpp_formatError(__('You must specify the user_id for this action, using the "user_id" attribute', 'fjgwpp')));	
	}

	if ($action === 'gal') {
		if (!isset($id) || strlen($id) == 0) 
			return(fjgwpp_formatError(__('You must specify the id of the gallery, using the "id" attribute', 'fjgwpp')));	
	}

	if ($action === 'set') {
		if (!isset($id) || strlen($id) == 0) 
			return(fjgwpp_formatError(__('You must specify the id of the set, using the "id" attribute', 'fjgwpp')));	
	}

	if ($action === 'tag') {
		if (!isset($tags) || strlen($tags) == 0) 
			return(fjgwpp_formatError(__('You must specify the tags using the "tags" attribute', 'fjgwpp')));
		if ($tags_mode !== 'any' && $tags_mode !== 'all') 
			return(fjgwpp_formatError(__('You must specify a valid tags_mode: "any" or "all"', 'fjgwpp')));
	}

	if ($action === 'grp') {
		if (!isset($id) || strlen($id) == 0) 
			return(fjgwpp_formatError(__('You must specify the id of the group, using the "id" attribute', 'fjgwpp')));	
	}

	if ($pagination !== 'none' && $pagination !== 'prevnext' && $pagination !== 'numbers') {
		return(fjgwpp_formatError(__('The pagination attribute can be only "none", "prevnext" or "numbers".', 'fjgwpp')));		
	}

	if ($last_row !== 'hide' && $last_row !== 'justify' && $last_row !== 'nojustify') {
		return(fjgwpp_formatError(__('The last_row attribute can be only "hide", "justify" or "nojustify".', 'fjgwpp')));		
	}

	if ($lightbox !== 'none' && $lightbox !== 'colorbox' && $lightbox !== 'swipebox') {
		return(fjgwpp_formatError(__('The lightbox attribute can be only "none", "colorbox" or "swipebox".', 'fjgwpp')));		
	}

	//Photo loading----------------
	$extras = "description, original_format, url_l, url_z";
	if ($action === 'set') {
		//Show the photos of a particular photoset
		$photos = $f->photosets_getPhotos($id, $extras, 1, $max_num_photos, $page_num, NULL);	
		$photos_main_index = 'photoset';
	} else if ($action === 'gal') {
		//Show the photos of a particular gallery
		$photos_url[$user_id] = $f->urls_getUserPhotos($user_id);
		if ($f->getErrorCode() != NULL) return(fjgwpp_formatFlickrAPIError($f->getErrorMsg()));

		$gallery_info = $f->urls_lookupGallery($photos_url[$user_id] . 'galleries/' . $id);
		if ($f->getErrorCode() != NULL) return(fjgwpp_formatFlickrAPIError($f->getErrorMsg()));

		$photos = $f->galleries_getPhotos($gallery_info['gallery']['id'], $extras, $max_num_photos, $page_num);	

		$photos_main_index = 'photos';
	} else if ($action === 'tag') {
		$photos = $f->photos_search(array(
			'user_id' => $user_id,
			'tags' => $tags,
			'tag_mode' => $tags_mode,
			'extras' => $extras,
			'per_page' => $max_num_photos, 
			'page' => $page_num
		));
		$photos_main_index = 'photos';
	} else if ($action === 'grp') {
		//Show the photos of a particular group pool
		//groups_pools_getPhotos ($group_id, $tags = NULL, $user_id = NULL, $jump_to = NULL, $extras = NULL, $per_page = NULL, $page = NULL) {
		$photos = $f->groups_pools_getPhotos($id, $tags, NULL, NULL, $extras, $max_num_photos, $page_num);
		$photos_main_index = 'photos';
	} else {
		//Show the classic photostream
		$photos = $f->people_getPublicPhotos($user_id, NULL, $extras, $max_num_photos, $page_num);
			
		//Need the authentication (TODO)
		//$photos = $f->people_getPhotos($user_id, 
		//	array("privacy_filter" => "1", "extras" => "description", "per_page" => $max_num_photos, "page" => $page_num));

		$photos_main_index = 'photos';
	}

	if ($f->getErrorCode() != NULL) return(fjgwpp_formatFlickrAPIError($f->getErrorMsg()));

	$photos_pool = $photos[$photos_main_index];
	if(count((array)$photos_pool['photo']) == 0) return(__('No photos', 'fjgwpp'));

	//we calculate that the aspect ratio has an average of 4:3
	if($images_height <= 75) {
		$imgSize = "thumbnail"; //thumbnail (longest side:100)
	}else if($images_height <= 180) {
		$imgSize = "small"; //small (longest side:240)
	}else{ //if <= 240
		$imgSize = "small_320"; //small (longest side:320)
	}

	$ris .= '<!-- Flickr Justified Gallery Wordpress Plugin by Miro Mannino -->' . "\n" 
		.	'<div id="' . $flickrGalID . '" class="justified-gallery" >';

	$r = 0;

	$use_large_thumbnails = true;

	foreach ($photos_pool['photo'] as $photo) {

		if (!isset($photo['url_l'])) {
			$use_large_thumbnails = false;
		}

		fjgwpp_entryLink($id, $f, $ris, $photo, $photos_pool, $photos_url, $lightbox, $open_originals, $flickrGalID, $action);
		
		$ris .= '<img alt="' . htmlspecialchars($photo['title'], ENT_QUOTES, 'UTF-8') 
				 .	'" src="' . $f->buildPhotoURL($photo, $imgSize)
				 .	'" data-safe-src="' . $f->buildPhotoURL($photo, $imgSize) . '" />';

		if ($captions) {
			$ris .= '<div class="caption">'
				 .  '<div class="photo-title' . ($show_descriptions ? ' photo-title-with-desc' : '') . '">'
				 .  htmlspecialchars($photo['title'], ENT_QUOTES, 'UTF-8') . '</div>';
			if ($show_descriptions && isset($photo['description']) && isset($photo['description']['_content'])) {
				$ris .= '<div class="photo-desc">' . fjgwpp_filterDescription($photo['description']['_content']) . '</div>';
			}	
			$ris .= '</div>';
		}

		$ris .= '</a>'; //end link
	}

	$ris .= '</div>';



	$act_script = 'function fjgwppInit_' . $flickrGalID . '() { 
				jQuery("#' . $flickrGalID . '")';

	if ($lightbox === 'colorbox') {
		$act_script .= '.on(\'jg.rowflush jg.complete\', function() {
					jQuery(this).find("> a").colorbox({
						maxWidth : "85%",
						maxHeight : "85%",
						current : "",';

		if ($block_contextmenu) {
			$act_script .= 	'onComplete: function() {
							fjgwppDisableContextMenu(jQuery("#colorbox .cboxPhoto"));
						}';
		}

		$act_script .=		'});
				})';
	} else if ($lightbox === 'swipebox') {
		$act_script .= '.on(\'jg.complete\', function() {
					jQuery("#' . $flickrGalID . '").find("> a").swipebox(';

		if ($block_contextmenu) {
			$act_script .= '{
						afterOpen : function () { 
							setTimeout(function() {
								fjgwppDisableContextMenu(jQuery("#swipebox-overlay .slide img"));
							}, 100);
						}
					}';
		}

		$act_script .=		');
				})';
	}

	$act_script .= '.justifiedGallery({'
			 .	'\'lastRow\': \'' . $last_row . '\', '
			 .	'\'rowHeight\':' . $images_height . ', '
			 .	'\'fixedHeight\':' . ($fixed_height ? 'true' : 'false') . ', '		 
			 .	'\'captions\':' . ($captions ? 'true' : 'false') . ', '
			 .	'\'randomize\':' . ($randomize ? 'true' : 'false') . ', '
			 .	'\'margins\':' . $margins . ', '
			 .  '\'sizeRangeSuffixes\': { 
			 			\'lt100\':\'_t\', \'lt240\':\'_m\', \'lt320\':\'_n\',
						\'lt500\':\'\', \'lt640\':\'_z\','
			 .  		(($use_large_thumbnails) ? '\'lt1024\':\'_b\'' : '\'lt1024\':\'_z\'')
			 .  '}});';
	
	if ($block_contextmenu) {
		$act_script .= 'fjgwppDisableContextMenu(jQuery("#' . $flickrGalID . '").find("> a"));';
	}
	
	$act_script .= '}'
		.	'if (typeof fjgwpp_galleriesInit_functions === "undefined") fjgwpp_galleriesInit_functions = [];'
		.	'fjgwpp_galleriesInit_functions.push(fjgwppInit_' . $flickrGalID . ');';

	//Navigation---------------------
	if($pagination !== 'none') {
		
		$num_pages = $photos[$photos_main_index]['pages'];

		if ($num_pages > 1) {

			$permalink = get_permalink();
		
			if ($pagination === 'numbers') {
					
				$ris .= '<div class="page-links">'
						 .	'<span class="page-links-title">Pages:</span> ';

				$low_num = $page_num - floor($maximum_pages_nums/2);
				$high_num = $page_num + ceil($maximum_pages_nums/2) - 1;

				if ($low_num < 1) {
					$high_num += 1 - $low_num; 
					$low_num = 1;
				}

				if ($high_num > $num_pages) {
					$high_num = $num_pages;
				}

				if ($low_num > 1) {
					$ris .= '<a href="' . add_query_arg('page', ($low_num - 1), $permalink) . '"><span>...</span></a> ';
				}

				for ($i = $low_num; $i <= $high_num; $i++) {
					if ($i == $page_num) $ris .= '<span>' . $i . '</span> ';
					else {
						$ris .= '<a href="' . add_query_arg('page', $i, $permalink) . '"><span>' . $i . '</span></a> ';
					}
				}

				if ($high_num < $num_pages) {
					$ris .= '<a href="' . add_query_arg('page', ($high_num + 1), $permalink) . '"><span>...</span></a> ';
				}

				$ris .= '</div>';

			} else if ($pagination === 'prevnext') {
					
				$ris .= '<div>';

				if ($page_num < $num_pages) {
					$ris .= '<div class="nav-previous">'
					 .	'<a href="' . add_query_arg('page', ((int)$page_num + 1), $permalink) . '">' 
					 . __('<span class="meta-nav">&larr;</span> Older photos', 'fjgwpp') . '</a>'
					 .	'</div>';
				}

				if ($page_num > 1) { //a link to the newer photos
					$ris .= '<div class="nav-next">'
					 .	'<a href="' . add_query_arg('page', ((int)$page_num - 1), $permalink) . '">' 
					 . __('Newer photos <span class="meta-nav">&rarr;</span>', 'fjgwpp') . '</a>'
					 .	'</div>';	
				}

				$ris .= '</div>';

			}
		}
	}

	wp_add_inline_script('flickrJustifiedGalleryWPPlugin', $act_script, 'before');

	$shortcode_unique_id++;
	return($ris);
}

function fjgwpp_filterDescription($descriptionText) {
	$descriptionText = preg_replace('/<a[^>]*?>(.*?)<\/a>/i', htmlspecialchars('$1', ENT_QUOTES, 'UTF-8'), $descriptionText);
	return $descriptionText;
}

function fjgwpp_entryLink($id, $f, &$ris, $photo, $photos_pool, $photos_url, $lightbox, $open_originals, $flickrGalID, $action) {
	$target_blank = true; //TODO in the settings page?

	if ($lightbox !== 'none') {
		$ris .=	'<a href="';

		if($open_originals) {
			if (isset($photo['originalsecret'])) {
				$ris .= $f->buildPhotoURL($photo, "original");
			} else if (isset($photo['url_l'])) {
				$ris .= $photo['url_l'];
			} else {
				$ris .= $photo['url_z'];
			}
		} else {
			if (isset($photo['url_l'])) {
				$ris .= $photo['url_l'];
			} else {
				$ris .= $photo['url_z'];
			}
		}

		$ris .= '" rel="' . $flickrGalID . '" title="' . $photo['title'] . '">';	
	} else {

		//If it is a gallery the photo has an owner, else is the photoset owner (or the photostream owner)
		$photo_owner = isset($photo['owner']) ? $photo['owner'] : $photos_pool['owner'];

		//Save the owner url
		if (!isset($photos_url[$photo_owner])) {
			$photos_url[$photo_owner] = $f->urls_getUserPhotos($photo_owner);
			if ($f->getErrorCode() != NULL) return(fjgwpp_formatFlickrAPIError($f->getErrorMsg()));
		}

		if ($action === 'set') {
			$photos_url_in = '/in/set-' . $id . '/lightbox';
		} else {
			$photos_url_in = '/in/photostream/lightbox';
		}

		$ris .= '<a href="' . $photos_url[$photo_owner] . $photo['id'] . $photos_url_in . '" ';
		if ($target_blank) $ris .= 'target="_blank" ';
		$ris .= 'title="' . $photo['title'] . '">';
	}
}

//[flickr_photostream user_id="..." ...]
function fjgwpp_flickr_photostream($atts, $content = null) {
	return fjgwpp_createGallery('phs', $atts);
}
add_shortcode('flickr_photostream', 'fjgwpp_flickr_photostream');
add_shortcode('flickrps', 'fjgwpp_flickr_photostream'); //legacy tag

//[flickr_set id="..." ...]
function fjgwpp_flickr_set($atts, $content = null) {
	return fjgwpp_createGallery('set', $atts);	
}
add_shortcode('flickr_set', 'fjgwpp_flickr_set');

//[flickr_gallery user_id="..." id="..." ...]
function fjgwpp_flickr_gallery($atts, $content = null) {
	return fjgwpp_createGallery('gal', $atts);		
}
add_shortcode('flickr_gallery', 'fjgwpp_flickr_gallery');

//[flickr_tags user_id="..." tags="..." tags_mode="any/all" ...]
function fjgwpp_flickr_tags($atts, $content = null) {
	return fjgwpp_createGallery('tag', $atts);
}
add_shortcode('flickr_tags', 'fjgwpp_flickr_tags');

//[flickr_group id="..."]
function fjgwpp_flickr_group($atts, $content = null) {
	return fjgwpp_createGallery('grp', $atts);
}
add_shortcode('flickr_group', 'fjgwpp_flickr_group');


//Options
include("flickr-justified-gallery-settings.php");

?>