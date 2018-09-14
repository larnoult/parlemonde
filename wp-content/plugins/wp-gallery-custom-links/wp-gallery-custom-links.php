<?php
/*
Plugin Name: WP Gallery Custom Links
Text Domain: wp-gallery-custom-links
Description: Specify custom links for WordPress gallery images (instead of attachment or file only).
Version: 1.12
Author: johnogg
Author URI: http://www.fourlightsweb.com
License: GPL2

Copyright 2017 John Griffiths

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

add_action( 'init', array( 'WPGalleryCustomLinks', 'init' ) );
add_action( 'plugins_loaded', array( 'WPGalleryCustomLinks', 'loadPluginTextDomain' ) );

class WPGalleryCustomLinks {
	// We will always be "replacing" the gallery shortcode function
	// via the post_gallery filter, although usually it will be with
	// the gallery shortcode function content itself unless there's
	// an additional theme filter or something.
	// [gallery] ->
	// 		$GLOBALS['shortcode_tags']['gallery'] ->
	//			apply_filter('post_gallery') *
	//			apply_filter('post_gallery') (first call, will include second recursive call, and then does actual link replacing) ->
	//				$GLOBALS['shortcode_tags']['gallery'] ->
	//					apply_filter('post_gallery') *
	//					apply_filter('post_gallery') (second call, simply returns output passed in)
	//				return same content passed in to this second recursive call
	//			return "filter" $output with replaced links to original $GLOBALS['shortcode_tags']['gallery'] call
	private static $first_call = true;
	private static $class_name = 'WPGalleryCustomLinks';

	public static function init() {
		// Add the filter for editing the custom url field
		add_filter( 'attachment_fields_to_edit', array( self::$class_name, 'apply_filter_attachment_fields_to_edit' ), null, 2 );

		// Add the filter for saving the custom url field
		add_filter( 'attachment_fields_to_save', array( self::$class_name, 'apply_filter_attachment_fields_to_save' ), null , 2 );

		// Add the filter for when the post_gallery is written out
		add_filter( 'post_gallery', array( self::$class_name, 'apply_filter_post_gallery' ), 9999, 2 );

		// Require the javascript to disable lightbox
		add_action( 'wp_enqueue_scripts', array( self::$class_name, 'do_action_wp_enqueue_scripts' ) );

		// Load translations
		load_plugin_textdomain( 'wp-gallery-custom-links', false, basename( dirname( __FILE__ ) ) . '/languages' );
	} // End function init()

	public static function loadPluginTextDomain() {
		// The codex says this is needed to load plugin language files
		load_plugin_textdomain( 'wp-gallery-custom-links', false, basename( dirname( __FILE__ ) ) . '/languages' );
	}

	public static function do_action_wp_enqueue_scripts() {
		wp_enqueue_script(
			'wp-gallery-custom-links-js',
			plugins_url( '/wp-gallery-custom-links.js', __FILE__ ),
			array( 'jquery' ),
			'1.1',
			true
		);
	}

	public static function apply_filter_attachment_fields_to_edit( $form_fields, $post ) {
		$help_css = 'z-index:999;display:none;position:absolute;margin-top:-100px;background-color:#ffffe0;text-align:left;border:1px solid #dfdfdf;padding:10px;width:75%;font-weight:normal;border-radius:3px;';

		// Gallery Link URL field
		$form_fields['gallery_link_url'] = array(
			'label' => __( 'Gallery Link URL', 'wp-gallery-custom-links' ) .
				' <a href="#" onclick="jQuery(\'.wpgcl_gallery_link_help\').hide();jQuery(\'#wpgcl_gallery_link_url_help\').show(); return false;" onblur="jQuery(\'#wpgcl_gallery_link_url_help\').hide();">[?]</a>' .
				'<div id="wpgcl_gallery_link_url_help" style="'.$help_css.'" class="wpgcl_gallery_link_help">' .
				__( 'Will replace "Image File" or "Attachment Page" link for this image in galleries. Use [none] to remove the link from this image in galleries.', 'wp-gallery-custom-links' ) .
				' <a href="#" onclick="jQuery(\'#wpgcl_gallery_link_url_help\').hide(); return false;">[X]</a>' .
				'</div>',
			'input' => 'text',
			'value' => get_post_meta( $post->ID, '_gallery_link_url', true )
		);
		// Gallery Link Target field
		$target_value = get_post_meta( $post->ID, '_gallery_link_target', true );
		$form_fields['gallery_link_target'] = array(
			'label' => __( 'Gallery Link Target', 'wp-gallery-custom-links' ) .
				' <a href="#" onclick="jQuery(\'.wpgcl_gallery_link_help\').hide();jQuery(\'#wpgcl_gallery_link_target_help\').show(); return false;" onblur="jQuery(\'#wpgcl_gallery_link_target_help\').hide();">[?]</a>' .
				'<div id="wpgcl_gallery_link_target_help" style="'.$help_css.'" class="wpgcl_gallery_link_help">' .
				__( 'This setting will be applied to this image in galleries regardless of whether or not a Gallery Link URL has been specified.', 'wp-gallery-custom-links' ) .
				' <a href="#" onclick="jQuery(\'#wpgcl_gallery_link_target_help\').hide(); return false;">[X]</a>' .
				'</div>',
			'input'	=> 'html',
			'html'	=> '
				<select name="attachments['.$post->ID.'][gallery_link_target]" id="attachments['.$post->ID.'][gallery_link_target]">
					<option value="">'.__( 'Do Not Change', 'wp-gallery-custom-links' ).'</option>
					<option value="_self"'.($target_value == '_self' ? ' selected="selected"' : '').'>'.__( 'Same Window', 'wp-gallery-custom-links' ).'</option>
					<option value="_blank"'.($target_value == '_blank' ? ' selected="selected"' : '').'>'.__( 'New Window', 'wp-gallery-custom-links' ).'</option>
				</select>'
		);
		// Gallery Link OnClick Effect field
		$preserve_click_value = get_post_meta( $post->ID, '_gallery_link_preserve_click', true );
		$form_fields['gallery_link_preserve_click'] = array(
			'label' => __( 'Gallery Link OnClick Effect', 'wp-gallery-custom-links' ) .
				' <a href="#" onclick="jQuery(\'.wpgcl_gallery_link_help\').hide();jQuery(\'#wpgcl_gallery_link_preserve_click_help\').show(); return false;" onblur="jQuery(\'#wpgcl_gallery_link_preserve_click_help\').hide();">[?]</a>' .
				'<div id="wpgcl_gallery_link_preserve_click_help" style="'.$help_css.'" class="wpgcl_gallery_link_help">' .
				__( 'Lightbox and other OnClick events are removed by default from this image in galleries. This setting will only be applied to this image in galleries if this image has a Gallery Link URL specified.', 'wp-gallery-custom-links' ) .
				' <a href="#" onclick="jQuery(\'#wpgcl_gallery_link_preserve_click_help\').hide(); return false;">[X]</a>' .
				'</div>',
			'input'	=> 'html',
			'html'	=> '
				<select name="attachments['.$post->ID.'][gallery_link_preserve_click]" id="attachments['.$post->ID.'][gallery_link_preserve_click]">
					<option value="remove">'.__( 'Remove', 'wp-gallery-custom-links' ).'</option>
					<option value="preserve"'.($preserve_click_value == 'preserve' ? ' selected="selected"' : '').'>'.__( 'Keep', 'wp-gallery-custom-links' ).'</option>
				</select>'
		);
		// Gallery Link additional css classes field
		$form_fields['gallery_link_additional_css_classes'] = array(
			'label' => __( 'Gallery Link Additional CSS Classes', 'wp-gallery-custom-links' ) .
				' <a href="#" onclick="jQuery(\'.wpgcl_gallery_link_help\').hide();jQuery(\'#wpgcl_gallery_link_additional_css_classes_help\').show(); return false;" onblur="jQuery(\'#wpgcl_gallery_link_additional_css_classes_help\').hide();">[?]</a>' .
				'<div id="wpgcl_gallery_link_additional_css_classes_help" style="'.$help_css.'" class="wpgcl_gallery_link_help">' .
				__( 'Additional CSS classes specified here will be applied to the <strong><em>link</em></strong> around this image in galleries regardless of whether or not a Gallery Link URL has been specified.', 'wp-gallery-custom-links' ) .
				' <a href="#" onclick="jQuery(\'#wpgcl_gallery_link_additional_css_classes_help\').hide(); return false;">[X]</a>' .
				'</div>',
			'input' => 'text',
			'value' => get_post_meta( $post->ID, '_gallery_link_additional_css_classes', true )
		);
		return $form_fields;
	} // End function apply_filter_attachment_fields_to_edit()

	public static function apply_filter_attachment_fields_to_save( $post, $attachment ) {
		// Save our custom meta fields
		if( isset( $attachment['gallery_link_url'] ) ) {
			update_post_meta( $post['ID'], '_gallery_link_url', $attachment['gallery_link_url'] );
		}
		if( isset( $attachment['gallery_link_target'] ) ) {
			update_post_meta( $post['ID'], '_gallery_link_target', $attachment['gallery_link_target'] );
		}
		if( isset( $attachment['gallery_link_preserve_click'] ) ) {
			update_post_meta( $post['ID'], '_gallery_link_preserve_click', $attachment['gallery_link_preserve_click'] );
		}
		if( isset( $attachment['gallery_link_additional_css_classes'] ) ) {
			update_post_meta( $post['ID'], '_gallery_link_additional_css_classes', $attachment['gallery_link_additional_css_classes'] );
		}
		return $post;
	} // End function apply_filter_attachment_fields_to_save()

	public static function apply_filter_post_gallery( $output, $attr ) {
		global $post;

		// Determine what our postID for attachments is - either
		// from our shortcode attr or from $post->ID. If we don't
		// have one from either of those places...something weird
		// is going on, so just bail.
		if( isset( $attr['id'] ) ) {
			$post_id = intval( $attr['id'] );
		} else if( $post ) {
			$post_id = intval( $post->ID );
		} else {
			return ' ';
		}

		if( isset( $attr['ignore_gallery_link_urls'] ) && strtolower( trim( $attr['ignore_gallery_link_urls'] ) ) === 'true' ) {
			// If the user has passed in a parameter to ignore the custom link
			// URLs for this gallery, just skip over this whole plugin and
			// return what was passed in
			return $output;
		} else if( self::$first_call ) {
			// Our first run, so the gallery function thinks it's being
			// overwritten. Set the variable to prevent actual endless
			// overwriting later.
			self::$first_call = false;
		} else {
			// We're inside our dynamically called gallery function and
			// don't want to spiral into an endless loop, so return
			// whatever output we were given so the gallery function
			// will run as normal. Also reset the first call variable
			// so if there's two galleries or something it will run the
			// same next time.
			self::$first_call = true;
			return $output;
		}

		// Get the normal gallery shortcode function
		if ( isset( $GLOBALS['shortcode_tags'] ) && isset( $GLOBALS['shortcode_tags']['gallery'] ) ) {
			$gallery_shortcode_function = $GLOBALS['shortcode_tags']['gallery'];
		}

		// Run whatever gallery shortcode function has been set up,
		// default, theme-specified or whatever
		$output = call_user_func( $gallery_shortcode_function, $attr );

		$attachment_ids = array();
		if( isset( $attr['ids'] ) ) {
			// WP 3.5+:
			$attachment_ids = array_merge( $attachment_ids, explode( ',', $attr['ids'] ) );
		} else {
			// < WP 3.5:
			// Get the attachment ids for this post
			$attachments = get_children( array( 'post_parent' => $post_id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image' ) );
			if( count( $attachments ) > 0 ) {
				$attachment_ids = array_merge( $attachment_ids, array_keys( $attachments ) );
			}
		} // End if it's the "ids" attribute way of specifying images or not
		// Add in any "include"d attachmentIDs
		if( isset( $attr['include'] ) ) {
			$attachment_ids = array_merge( $attachment_ids, explode( ',', $attr['include'] ) );
		}

		// Make sure we don't replace the same one multiple times
		$attachment_ids = array_unique( $attachment_ids );

		foreach ( $attachment_ids as $attachment_id ) {
			$link = '';
			$target = '';
			$rel = '';
			$preserve_click = '';
			$remove_link = false;
			$additional_css_classes = '';
			$attachment_id = intval( $attachment_id );

			// See if we have a custom url for this attachment image
			$attachment_meta = get_post_meta( $attachment_id, '_gallery_link_url', true );
			if( $attachment_meta ) {
				$link = $attachment_meta;

				// Apply filters to the custom link (e.g. the case of prefixing internal
				// links with /en/ or /fr/ etc. for languages on the fly)
				$link = apply_filters( 'wpgcl_filter_raw_gallery_link_url', $link, $attachment_id, $post_id );
			}
			// See if we have a target for this attachment image
			$attachment_meta = get_post_meta( $attachment_id, '_gallery_link_target', true );
			if( $attachment_meta ) {
				$target = $attachment_meta;
			}
			if( isset( $attr['open_all_in_new_window'] ) && strtolower( trim( $attr['open_all_in_new_window'] ) ) === 'true' ) {
				// Override setting if the gallery shortcode says to open everything in a new window
				// This should accommodate both "3000 images in new window" guy (to some extent) and allow _blank
				// by default lady to reset her gallery to _blank at once
				$target = '_blank';
			}
			if( isset( $attr['open_all_in_same_window'] ) && strtolower( trim( $attr['open_all_in_same_window'] ) ) === 'true' ) {
				// Override setting if the gallery shortcode says to open everything in the same window
				// This should allow _blank by default lady to set her gallery to _self at once
				$target = '_self';
			}
			// See if we have a rel for this link
			if( isset( $attr['rel'] ) ) {
				// Add a rel property to this image link with the specified value
				$rel = $attr['rel']; // Note: this is escaped before being dropped in
			}
			// See if we have additional css classes for this attachment image
			$attachment_meta = get_post_meta( $attachment_id, '_gallery_link_additional_css_classes', true );
			if( $attachment_meta ) {
				$additional_css_classes = trim( $attachment_meta );
			}

			// See how to handle click events for this attachment image
			$attachment_meta = get_post_meta( $attachment_id, '_gallery_link_preserve_click', true );
			if( $attachment_meta ) {
				$preserve_click = $attachment_meta;
			}
			if( isset( $attr['preserve_click_events'] ) && strtolower( trim( $attr['preserve_click_events'] ) ) === 'true' ) {
				// Override the individual setting if the gallery shortcode says to preserve on all
				$preserve_click = 'preserve';
			}

			// See if we need to remove the link for this image or not
			if( strtolower( trim( $link ) ) === '[none]' || ( isset( $attr['remove_links'] ) && strtolower( trim( $attr['remove_links'] ) ) === 'true' ) ) {
				$remove_link = true;
			}

			if( $link != '' || $target != '' || $rel != '' || $remove_link || $additional_css_classes != '' ) {
				// Replace the attachment href
				$needle = get_attachment_link( $attachment_id );
				$output = self::replace_link( $needle, $link, $target, $rel, $preserve_click, $remove_link, $additional_css_classes, $output );

				// Replace the file href
				list( $needle ) = wp_get_attachment_image_src( $attachment_id, '' );
				$output = self::replace_link( $needle, $link, $target, $rel, $preserve_click, $remove_link, $additional_css_classes, $output );
				// Also, in case of jetpack photon with tiled galleries...
				if( function_exists( 'jetpack_photon_url' ) ) {
					// The CDN url currently is generated with "$subdomain = rand( 0, 2 );",
					// and then "$photon_url  = "http://i{$subdomain}.wp.com/$image_host_path";".
					// So just to cover our bases, loop over a slightly larger range of numbers
					// to make sure we include all possibilities for what the photon url
					// could be.  The max $j value may need to be increased someday if they
					// get a lot busier. Also the URL could change. I guess I'll cross
					// those bridges when we come to them. Blah.
					for( $j = 0; $j < 10; $j++ ) {
						$needle_parts = explode( '.wp.com', jetpack_photon_url( $needle ) );
						if( count( $needle_parts ) == 2 ) {
							$needle_part_1 = preg_replace( '/\d+$/U', '', $needle_parts[0] );
							$needle_part_2 = '.wp.com' . $needle_parts[1];
							$needle_reassembled = $needle_part_1 . $j . $needle_part_2;
							$output = self::replace_link( $needle_reassembled, $link, $target, $rel, $preserve_click, $remove_link, $additional_css_classes, $output );
						}
					}
				}

				// Replace all possible file sizes - some themes etc.
				// may use sizes other than the full version
				$attachment_metadata = wp_get_attachment_metadata( $attachment_id );
				if( $attachment_metadata !== false && isset( $attachment_metadata['sizes'] ) ) {
					$attachment_sizes = $attachment_metadata['sizes'];
					if( is_array( $attachment_sizes ) && count( $attachment_sizes ) > 0 ) {
						foreach( $attachment_sizes as $attachment_size => $attachment_info ) {
							list( $needle ) = wp_get_attachment_image_src( $attachment_id, $attachment_size );
							$output = self::replace_link( $needle, $link, $target, $rel, $preserve_click, $remove_link, $additional_css_classes, $output );
						} // End of foreach attachment size
					} // End if we have attachment sizes
				} // End if we have attachment metadata (specifically sizes)
			} // End if we have a link to swap in or a target to add
		} // End foreach post attachment

		return $output;
	} // End function apply_filter_post_gallery()

	private static function replace_link( $default_link, $custom_link, $target, $rel, $preserve_click, $remove_link, $additional_css_classes, $output ) {
		// Build the regex for matching/replacing
		$needle = preg_quote( $default_link );
		$needle = str_replace( '/', '\/', $needle );
		$needle = '/href\s*=\s*["\']' . $needle . '["\']/U';
		if( preg_match( $needle, $output ) > 0 ) {
			if( $additional_css_classes != '' ) {
				$classes_to_add = $additional_css_classes . ' ';
			} else {
				$classes_to_add = '';
			}

			// Remove Link
			if( $remove_link ) {
				// Break the output up into two parts: everything before this href
				// and everything after. Note: sometimes the image url will
				// appear multiple times in the source (e.g. "data-orig-file" in
				// jetpack), so use the regex needle to find the href first, then break
				// up the link.
				$output_parts = preg_replace( $needle, '^^^HREF^^^', $output );
				$output_parts = explode( '^^^HREF^^^', $output_parts );
				if( count( $output_parts ) == 2 ) {
					$output_part_1 = $output_parts[0];
					$output_part_2 = $output_parts[1];

					// Take out the <a> from the end of part 1, from its
					// opening angle bracket
					$pos = strrpos( $output_part_1, '<' );
					if( $pos !== false ) {
						$output_part_1 = substr( $output_part_1, 0, $pos );
					}

					// And then take out everything up through the first > that comes after that
					// (which would be the closing angle bracket of <a>)
					$pos = strpos( $output_part_2, '>' );
					if( $pos !== false ) {
						$output_part_2 = substr( $output_part_2, $pos+1 );
						// Add in a span where the link used to be, just in case
						$output_part_2 = '<span class="no-link'. ( $preserve_click != 'preserve' ? ' no-lightbox' : '' ) . '">' . $output_part_2;
					}

					// And then take out the first </a> that comes after that
					$pos = strpos( $output_part_2, '</a>' );
					if( $pos !== false ) {
						// Also close out the span where the link used to be
						$output_part_2 = substr( $output_part_2, 0, $pos ) . '</span>' . substr( $output_part_2, $pos+4 );
					}

					// And then stitch them back together again, without the link parts
					$output = $output_part_1 . $output_part_2;
				}
			}

			// Custom Target
			if( $target != '' && ! $remove_link ) {
				// Replace the link target
				$output = self::add_property( $default_link, 'target', $target, $output );

				// Add a class to the link so we can manipulate it with
				// javascript later (only if we're opening it in a new window -
				// don't want to auto-unbind lightbox if it's the same window)
				if( $target == '_blank' ) {
					$classes_to_add .= 'set-target ';
				}
			}

			// Custom Rel
			if( $rel != '' && ! $remove_link ) {
				// Replace the link rel
				$output = self::add_property( $default_link, 'rel', $rel, $output );
			}

			// Pre-custom link class
			if( $custom_link != '' && ! $remove_link  ) { // Same criteria as "custom link" block below
				// Add a class to the link so we can manipulate it with
				// javascript later.
				// Doing this first in case we have the same custom link
				// on multiple items, in which case that class would be added
				// to the first item/all items multiple times.
				if( $preserve_click != 'preserve' ) {
					$classes_to_add .= 'no-lightbox ';
				}
			} // End if we have a custom link to swap in

			// Add any classes, if needed (saves on some regexes to do it all at once)
			if( $classes_to_add != '' ) {
				$output = self::add_class( $default_link, trim( $classes_to_add ), $output );
			}

			// Custom Link
			if( $custom_link != '' && ! $remove_link  ) {
				// If we found the href to swap out, perform the href replacement,
				// and add some javascript to prevent lightboxes from kicking in
				$output = preg_replace( $needle, 'href="' . $custom_link . '"', $output );
			} // End if we have a custom link to swap in
		} // End if we found the attachment to replace in the output

		return $output;
	} // End function replace_link()

	private static function add_class( $needle, $class, $output ) {
		// Clean up our needle for regexing
		$needle = preg_quote( $needle );
		$needle = str_replace( '/', '\/', $needle );

		// Clean property values a bit
		$class = esc_attr( $class );

		// Add a class to the link so we can manipulate it with
		// javascript later
		if( preg_match( '/<a[^>]*href\s*=\s*["\']' . $needle . '["\'][^>]*class\s*=\s*["\'][^"\']*["\'][^>]*>/', $output ) > 0 ) {
			// href comes before class
			$output = preg_replace( '/(<a[^>]*href\s*=\s*["\']' . $needle . '["\'][^>]*class\s*=\s*["\'][^"\']*)(["\'][^>]*>)/U', '$1 '.$class.'$2', $output );
		} elseif( preg_match( '/<a[^>]*class\s*=\s*["\'][^"\']*["\'][^>]*href\s*=\s*["\']' . $needle . '["\'][^>]*>/', $output ) > 0 ) {
			// href comes after class
			$output = preg_replace( '/(<a[^>]*class\s*=\s*["\'][^"\']*)(["\'][^>]*href\s*=\s*["\']' . $needle . '["\'][^>]*>)/U', '$1 '.$class.'$2', $output );
		} else {
			// No previous class
			$output = preg_replace( '/(<a[^>]*href\s*=\s*["\']' . $needle . '["\'][^>]*)(>)/U', '$1 class="'.$class.'"$2', $output );
		} // End if we have a class on the a tag or not

		return $output;
	} // End function add_class()

	private static function add_property( $needle, $property_name, $property_value, $output ) {
		// Clean up our needle for regexing
		$needle = preg_quote( $needle );
		$needle = str_replace( '/', '\/', $needle );

		// Clean property values a bit
		$property_name = esc_attr( $property_name );
		$property_value = esc_attr( $property_value );

		// Add a property to the link (or overwrite what's there)
		if( preg_match( '/<a[^>]*href\s*=\s*["\']' . $needle . '["\'][^>]*'.$property_name.'\s*=\s*["\'][^"\']*["\'][^>]*>/', $output ) > 0 ) {
			// href comes before property
			$output = preg_replace( '/(<a[^>]*href\s*=\s*["\']' . $needle . '["\'][^>]*'.$property_name.'\s*=\s*["\'])[^"\']*(["\'][^>]*>)/U', '$1'.$property_value.'$2', $output );
		} elseif( preg_match( '/<a[^>]*'.$property_name.'\s*=\s*["\'][^"\']*["\'][^>]*href\s*=\s*["\']' . $needle . '["\'][^>]*>/', $output ) > 0 ) {
			// href comes after property
			$output = preg_replace( '/(<a[^>]*'.$property_name.'\s*=\s*["\'])[^"\']*(["\'][^>]*href\s*=\s*["\']' . $needle . '["\'][^>]*>)/U', '$1'.$property_value.'$2', $output );
		} else {
			// No previous property
			$output = preg_replace( '/(<a[^>]*href\s*=\s*["\']' . $needle . '["\'][^>]*)(>)/U', '$1 '.$property_name.'="'.$property_value.'"$2', $output );
		} // End if we have a class on the a tag or not

		return $output;
	} // End function add_property()

} // End class WPGalleryCustomLinks
