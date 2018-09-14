<?php
/**
 * Quick Featured Images
 *
 * @package   Quick_Featured_Images_Defaults
 * @author    Martin Stehle <m.stehle@gmx.de>
 * @license   GPL-2.0+
 * @link      http://stehle-internet.de/downloads/quick-featured-images/
 * @copyright 2014 
 */

/**
 * @package Quick_Featured_Images_Defaults
 * @author    Martin Stehle <m.stehle@gmx.de>
 */
class Quick_Featured_Images_Defaults {

	/**
	 * Instance of this class.
	 *
	 * @since    8.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Required user capability to use this plugin
	 *
	 * @since   8.0
	 *
	 * @var     string
	 */
	protected $required_user_cap = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    8.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * Name of this plugin.
	 *
	 * @since    8.0
	 *
	 * @var      string
	 */
	protected $plugin_name = null;

	/**
	 * Unique identifier for this plugin.
	 *
	 * It is the same as in class Quick_Featured_Images_Admin
	 * Has to be set here to be used in non-object context, e.g. callback functions
	 *
	 * @since    8.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = null;

	/**
	 * Unique identifier for the admin page of this class.
	 *
	 * @since    8.0
	 *
	 * @var      string
	 */
	protected $page_slug = null;

	/**
	 * Unique identifier for the admin parent page of this class.
	 *
	 * @since    8.0
	 *
	 * @var      string
	 */
	protected $parent_page_slug = null;

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since    8.0
	 *
	 * @var     string
	 */
	protected $plugin_version = null;

	/**
	 * Unique identifier in the WP options table
	 *
	 *
	 * @since    8.0
	 *
	 * @var      string
	 */
	protected $defaults_db_slug = 'quick-featured-images-defaults';

	/**
	 * Slug of the menu page on which to display the form sections
	 *
	 *
	 * @since    8.0
	 *
	 * @var      array
	 */
	protected $main_options_page_slug = 'quick-featured-images-defaultspage';

	/**
	 * Unique identifier in the WP options table for the plugin's settings
	 *
	 *
	 * @since    5.0
	 *
	 * @var      string
	 */
	protected $settings_db_slug = null;

	/**
	 * Stored settings in an array
	 *
	 *
	 * @since    8.0
	 *
	 * @var      array
	 */
	protected $stored_settings = array();

	/**
	 * User selected rules
	 *
	 * @since    8.0
	 *
	 * @var     array
	 */
	protected $selected_rules = null;

	/**
	 * Value of the nonce
	 *
	 * @since     13.4
	 *
	 * @var      string
	 */
	protected $nonce = 'kds94kek93ks90';
	
	/**
	 * Name of the main function
	 *
	 * @since     13.4
	 *
	 * @var      string
	 */
	protected $main_function_name = 'qfi_main';
	
	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     8.0
	 */
	private function __construct() {

		// Call variables from public plugin class.
		$plugin = Quick_Featured_Images_Admin::get_instance();
		$this->plugin_name = $plugin->get_plugin_name();
		$this->plugin_slug = $plugin->get_plugin_slug();
		$this->page_slug = $this->plugin_slug . '-defaults';
		$this->parent_page_slug = $plugin->get_page_slug();
		$this->plugin_version = $plugin->get_plugin_version();
		$this->settings_db_slug = $plugin->get_settings_db_slug();

		// get settings
		$settings = get_option( $this->settings_db_slug, array() );
		if ( isset( $settings[ 'minimum_role_all_pages' ] ) ) {
			switch ( $settings[ 'minimum_role_all_pages' ] ) {
				case 'administrator':
					$this->required_user_cap = 'manage_options';
					break;
				default:
					$this->required_user_cap = 'edit_others_posts';
			}
		} else {
			$this->required_user_cap = 'edit_others_posts';
		}
		
		// Domain name of WP site
		$parsed_url = parse_url( home_url() );
		$this->site_domain = $parsed_url[ 'host' ];

		// existence of the exif_imagetype()
		$this->is_exif_imagetype = function_exists( 'exif_imagetype' );

		// time in seconds to wait for a response
		$this->timeout_seconds = 60;
		
		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Add the options page and menu item.
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

		// Auto set featured image on saving a post
		add_action( 'save_post', array( $this, 'add_featured_image' ), 10 , 3 );
		
		// Auto delete rule if an image is deleted in the media library
		add_action( 'delete_attachment', array( $this, 'delete_rules_by_thumb_id' ) );
		
	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    8.0
	 */
	public function qfi_main() {
		$this->display_header();
		// store user selections
		if ( ! empty( $_POST ) ) {
			// verify allowed submission
			check_admin_referer( $this->main_function_name, $this->nonce );
			// sanitze user input
			$settings = $this->sanitize_options( $_POST );
			// store in db
			if ( update_option( $this->defaults_db_slug, $settings ) ) {
				$msg = 'Changes saved.';
				$class = 'updated';
			} else {
				$msg = 'Error while saving the changes.';
				$class = 'error';
			}
			printf ( '<div class="%s"><p><strong>%s</strong></p></div>', $class, esc_html__( $msg ) );
		} // if $_POST
		// get rules
		$this->selected_rules = $this->get_stored_settings();
		// print rest of page
		$this->display_page_content();
		$this->display_footer();
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     8.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Return the page headline.
	 *
	 * @since    8.0
	 *
	 *@return    page headline variable.
	 */
	public function get_page_headline() {
		//return __( 'Preset Featured Images', 'quick-featured-images' );
		$text = 'Default Images';
		return __( $text );
		// just for the translation editor to catch this string
		$text = __( 'Preset Featured Images', 'quick-featured-images' );
	}

	/**
	 * Return the page description.
	 *
	 * @since    8.0
	 *
	 *@return    page description variable.
	 */
	public function get_page_description() {
		return __( 'Set default featured images for future posts', 'quick-featured-images' );
	}

	/**
	 * Return the page slug.
	 *
	 * @since    8.0
	 *
	 *@return    page slug variable.
	 */
	public function get_page_slug() {
		return $this->page_slug;
	}

	/**
	 * Return the required user capability.
	 *
	 * @since    7.0
	 *
	 *@return    required user capability variable.
	 */
	public function get_required_user_cap() {
		return $this->required_user_cap;
	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @since     8.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_styles() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		// request css only if this plugin was called
		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {
			wp_enqueue_style( $this->plugin_slug .'-admin-styles', plugins_url( 'assets/css/admin.css', __FILE__ ), array( ), $this->plugin_version );
		}

 	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * @since     8.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		/* collect js for the color picker */
		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {
			// load script
			wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'assets/js/admin-defaults.js', __FILE__ ), array( 'jquery' ), $this->plugin_version );
			// Enqueue all stuff to use media API, requires at least WP 3.5
			wp_enqueue_media();
		}
	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    8.0
	 */
	public function add_plugin_admin_menu() {

		// get translated string of the menu label and page headline
		$label = $this->get_page_headline();
		
		// Add a defaults page for this plugin to the Settings menu.
		$this->plugin_screen_hook_suffix = add_submenu_page( 
			$this->parent_page_slug, // parent_slug
			sprintf( '%s: %s', $this->plugin_name, $label ), // page_title
			$label, // menu_title
			$this->required_user_cap, // capability to use the following function
			$this->page_slug, // menu_slug
			array( $this, $this->main_function_name ) // function to execute when loading this page
		);

	}

	/**
	 * Add defaults action link to the plugins page.
	 *
	 * @since    8.0
	 */
	public function add_action_links( $links ) {
		$url = sprintf( 'admin.php?page=%s', $this->page_slug );
		return array_merge(
			$links,
			array(
				'defaults' => sprintf( '<a href="%s">%s</a>', esc_url( admin_url( $url ) ), esc_html( $this->get_page_headline() ) )
			)
		);

	}

	/**
	* Check and return correct values for the settings
	*
	* @since   8.0
	*
	* @param   array    $input    Options and their values after submitting the form
	* 
	* @return  array              Options and their sanatized values
	*/
	public function sanitize_options ( $input ) {
		$sanitized_input = array();
		foreach ( $input as $key => $value ) {
			// ignore values with 'false' value, ie. 'null', zero, empty string, empty array
			if ( ! $value ) {
				continue;
			}

			switch ( $key ) {
				// checkboxes
				case 'overwrite_automatically':
				case 'use_first_image_as_default':
					$sanitized_input[ $key ] = isset( $input[ $key ] ) ? '1' : '0';
					break;
				// radio buttons
				case 'first_image_handling':
					$sanitized_input[ $key ] = ( in_array( $input[ $key ], array( 'always', 'use_if_no_img' ) ) ? $input[ $key ] : 'always' );
					break;
				// positive integers
				case 'default_image_id':
					$sanitized_input[ $key ] = absint( $value );
					break;
				// considered post types for 1st image
				case 'post_types_1st_image':
					foreach ( $value as $post_type ) {
						$sanitized_input[ $key ][] = $post_type;
					}
					break;
				// rules
				case 'rules':
					if ( is_array( $value ) ) {
						$c = 1;
						foreach ( $value as $rule ) {
							// ignored only partially defined rule
							if ( ! $rule[ 'id' ] or ! $rule[ 'taxonomy' ] or ! $rule[ 'matchterm' ] ) {
								continue;
							}
							// clean complete rule
							foreach ( $rule as $name => $setting ) {
								switch ( $name ) {
									case 'id':
										$sanitized_input[ $key ][ $c ][ $name ] = absint( $setting );
										break;
									case 'taxonomy':
										$sanitized_input[ $key ][ $c ][ $name ] = sanitize_text_field( $setting );
										break;
									case 'matchterm':
										if ( 'post_type' == $rule[ 'taxonomy' ] ) {
											$sanitized_input[ $key ][ $c ][ $name ] = sanitize_text_field( $setting );
										} else {
											$sanitized_input[ $key ][ $c ][ $name ] = absint( $setting );
										}
										break;
								} // switch()
							} // foreach
							$c = $c + 1;
						} // foreach
					} // if ( is_array( $value ))
			} // switch( $key )
		} // foreach()
		return $sanitized_input;
	} // end sanitize_options()

	/**
	 *
	 * Auto set featured image at saving a post
	 *
	 * @access   private
	 * @since    8.0
	 */
	public function add_featured_image( $post_id, $post, $is_update ) {
		// get out if post is autosave type
		if ( wp_is_post_autosave( $post_id ) ) return;
		// get out if post is revision
		if ( wp_is_post_revision( $post_id ) ) return;
		// get out if post is a newly created post, with no content
		if ( 'auto-draft' == get_post_status( $post_id ) ) return;
		// get post object if not valid
		if ( ! $post ) {
			$post = get_post( $post_id );
		}
		// get out if post does not support featured images
		if ( ! post_type_supports( $post->post_type, 'thumbnail' ) ) return;
		// else go on

		// load all rules
		$settings = $this->get_stored_settings();

		// get out if user wishes not to overwrite existing featured images and post has already a featured image
		if ( has_post_thumbnail( $post_id ) and ( ! isset( $settings[ 'overwrite_automatically' ] ) ) ) return;
		
		// set the thumbnail if a rule matches
		/*
		 * Rule cascade order:
		 * 1. first embedded content image
		 * 2. matched custom taxonomy
		 * 3. matched tag
		 * 4. matched category
		 * 5. matched user
		 * 6. matched post type
		 */
		$thumb_id = 0;
		// 1. Image by first embedded content image
		if ( isset( $settings[ 'use_first_image_as_default' ] ) ) {
			// only if post type should be considered or if no post type at all (backward compatible)
			if ( empty( $settings[ 'post_types_1st_image' ] ) or in_array( $post->post_type, $settings[ 'post_types_1st_image' ] ) ) {
				// get first content image
				$thumb_id = $this->get_first_content_image_id( $post->post_content );
			}
		} // if(use_first_image_as_default)
		// determine post's properties matched with specified rules
		if ( ! $thumb_id and isset( $settings[ 'rules' ] ) ) {
			$args = array( 'fields' => 'ids' );
			// 2. Image by matched custom taxonomy
			$skipped_taxonomies = array( 'post_tag', 'category', 'user', 'post_type' );
			foreach ( $settings[ 'rules' ] as $rule ) {
				if ( in_array( $rule[ 'taxonomy' ], $skipped_taxonomies ) ) {
					continue;
				}
				$thumb_id = $this->get_thumb_id( $post_id, $rule );
				if ( $thumb_id ) {
					break;
				}
			} // foreach()
			// 3. Image by matched tag
			if ( ! $thumb_id ) {
				foreach ( $settings[ 'rules' ] as $rule ) {
					if ( 'post_tag' != $rule[ 'taxonomy' ] ) {
						continue; // ommit non-post-tag rules here
					}
					$thumb_id = $this->get_thumb_id( $post_id, $rule );
					if ( $thumb_id ) {
						break;
					}
				} // foreach()
			} // if(no thumb)
			// 4. Image by matched category
			if ( ! $thumb_id ) {
				foreach ( $settings[ 'rules' ] as $rule ) {
					if ( 'category' != $rule[ 'taxonomy' ] ) {
						continue; // ommit non-post-category rules here
					}
					$thumb_id = $this->get_thumb_id( $post_id, $rule );
					if ( $thumb_id ) {
						break;
					}
				} // foreach()
			} // if(no thumb)
			// 5. Image by matched user
			if ( ! $thumb_id ) {
				foreach ( $settings[ 'rules' ] as $rule ) {
					if ( 'user' != $rule[ 'taxonomy' ] ) {
						continue; // ommit non-post-author rules here
					}
					if ( $post->post_author != $rule[ 'matchterm' ] ) {
						continue;
					}
					if ( wp_attachment_is_image( $rule[ 'id' ] ) ) {
						$thumb_id = $rule[ 'id' ];
						break;
					}
				} // foreach()
			} // if(no thumb)
			// 6. Image by post type
			if ( ! $thumb_id ) {
				foreach ( $settings[ 'rules' ] as $rule ) {
					if ( 'post_type' != $rule[ 'taxonomy' ] ) {
						continue; // ommit non-post-type rules here
					}
					if ( $post->post_type != $rule[ 'matchterm' ] ) {
						continue;
					}
					if ( wp_attachment_is_image( $rule[ 'id' ] ) ) {
						$thumb_id = $rule[ 'id' ];
						break;
					}
				} // foreach()
			} // if(no thumb)
		} // if(rules)
		// set image as featured image to post
		$success = false;
		if ( $thumb_id ) {
			$success = set_post_thumbnail( $post_id, $thumb_id );
		}

	}

	/**
	 *
	 * Delete the rules assigned to an image; is called after an image was deleted in the media library
	 *
	 * @access   private
	 * @since    11.6
	 */
	public function delete_rules_by_thumb_id( $thumb_id ) {
		// initialize flag
		$changed = false;
		// load all rules
		$settings = $this->get_stored_settings();

		// if rules are available look for rules with the given thumb_id and delete them
		if ( isset( $settings[ 'rules' ] ) ) {
			foreach ( $settings[ 'rules' ] as $key => $rule ) {
				// delete rule with single image
				if ( $thumb_id == $rule[ 'id' ] ) {
					unset( $settings[ 'rules' ][ $key ] );
					$changed = true;
				}
			} // foreach()
			// if no rules anymore: delete 'rules' item
			if ( empty( $settings[ 'rules' ] ) ) {
				unset( $settings[ 'rules' ] );
			} else {
				// reindex array	
				$settings[ 'rules' ] = array_values( $settings[ 'rules' ] );
			}
		} // if ( rules )
		
		// store
		if ( $changed ) {
			// store in db
			update_option( $this->defaults_db_slug, $settings );
		} // if ( changed )
	}
	
	/**
	 *
	 * Render the header of the admin page
	 *
	 * @access   private
	 * @since    8.0
	 */
	private function display_header() {
		include_once( 'views/section_header.php' );
	}
	
	/**
	 *
	 * Render the footer of the admin page
	 *
	 * @access   private
	 * @since    8.0
	 */
	private function display_footer() {
		include_once( 'views/section_footer.php' );
	}
	
	/**
	 *
	 * Render the the admin page
	 *
	 * @access   private
	 * @since    8.0
	 */
	private function display_page_content() {
		include_once( 'views/section_defaults.php' );
	}
	
	/**
	 * Set default settings
	 *
	 * @since    8.0
	 */
	private function set_default_settings() {

		// check if there are already stored settings under the option's database slug
		if ( false === get_option( $this->defaults_db_slug ) ) {
			// store default values in the db as a single and serialized entry
			add_option( 
				$this->defaults_db_slug, 
				array()
			);
		} // if ( false )
		
	}

	/**
	 * Define parameters and return thumbnail supporting custom post types
	 *
	 * @access   private
	 * @since     7.0
	 *
	 * @return    array    the names and labels of the registered and thumbnail supporting custom post types
	 */
	private function get_custom_post_types_labels() {
		$args = array(
			   '_builtin' => false # only custom post types
		);
        $name_labels = array();
		// get the registered custom post types as objects
        $objects = get_post_types( $args, 'objects' );
		// store their names and labels
        foreach ( $objects as $name => $object ) {
            if ( post_type_supports( $name, 'thumbnail' ) ) {
                $name_labels[ $name ] = $object->label;
            }
        }
		// return the result
		return $name_labels;
	}

	/**
	 * Return registered custom taxonomies with their labels
	 *
	 * @access   private
	 * @since    8.0
	 *
	 * @return    array    the names of the registered custom taxonomies
	 */
	private function get_custom_taxonomies_labels() {
		$args = array(
			   '_builtin' => false # only custon post types
		);
        $name_labels = array();
		// get the registered custom post types as objects
        $objects = get_taxonomies( $args, 'objects' );
		// store their names and labels
        foreach ( $objects as $name => $object ) {
            $name_labels[ $name ] = $object->label;
        }
		// return the result
		return $name_labels;
	}

	/**
	 * Get current or default settings
	 *
	 * @since    8.0
	 *
	 * @return    array    Return settings for default featured images
	 */
	private function get_stored_settings() {
		// try to load current settings. If they are not in the DB return set default settings
		$stored_settings = get_option( $this->defaults_db_slug, array() );
		// if empty array set and store default values
		if ( 0 == sizeof( $stored_settings ) ) {
			$this->set_default_settings();
			// try to load current settings again. Now there should be the data
			$stored_settings = get_option( $this->defaults_db_slug, array() );
		}

		return $this->sanitize_options( $stored_settings );
	}
	
	/**
	 *
	 * Test term and image id
	 *
	 * @access   private
	 * @since    8.0
	 */
	private function get_thumb_id ( $post_id, $rule ) {

		$terms = wp_get_post_terms( $post_id, $rule[ 'taxonomy' ], array( 'fields' => 'ids' ) );
		
		if ( is_wp_error( $terms ) ) {
			return 0;
		} 
		if ( ! in_array( $rule[ 'matchterm' ], $terms ) ) {
			return 0;
		}
		if ( ! wp_attachment_is_image( $rule[ 'id' ] ) ) {
			return 0;
		}
		return $rule[ 'id' ];
	}

	/**
	 * Returns the id of the first image in the content, else 0
	 *
	 * @access   private
	 * @since     5.0
	 *
	 * @return    integer    the post id of the image
	 */
	private function get_first_content_image_id ( $content ) {
		// set variables
		global $wpdb;
		// look for images in HTML code
		preg_match_all( '/<[iI][mM][gG][^>]+>/', $content, $all_img_tags );
		if ( $all_img_tags ) {
			foreach ( $all_img_tags[ 0 ] as $img_tag ) {
				// find class attribute and catch its value
				preg_match( '/<img[^>]*class\s*=\s*[\'"]([^\'"]+)[\'"][^>]*>/i', $img_tag, $img_class );
				if ( $img_class ) {
					// Look for the WP image id
					preg_match( '/wp-image-([\d]+)/i', $img_class[ 1 ], $found_id );
					// if first image id found: check whether is image
					if ( $found_id ) {
						$img_id = absint( $found_id[ 1 ] );
						// if is image: return its id
						if ( wp_attachment_is_image( $img_id ) ) {
							return $img_id;
						}
					} // if(found_id)
				} // if(img_class)
				
				// else: try to catch content image id by its url as stored in the database
				// find src attribute and catch its value
				preg_match( '/<img[^>]*src\s*=\s*[\'"]([^\'"]+)[\'"][^>]*>/i', $img_tag, $img_src );
				if ( $img_src ) {
					// delete optional query string in img src
					$url = preg_replace( '/([^?]+).*/', '\1', $img_src[ 1 ] );
					// delete image dimensions data in img file name, just take base name and extension
					$guid = preg_replace( '/(.+)-\d+x\d+\.(\w+)/', '\1.\2', $url );
					// if path is set relatively make it absolute
					if ( 0 === strpos( $guid, '/' ) ) {
						$guid = home_url() . $guid;
					}
					// look up its ID in the db
					$found_id = $wpdb->get_var( $wpdb->prepare( "SELECT `ID` FROM $wpdb->posts WHERE `guid` = '%s'", $guid ) );
					// if first image id found: return it
					if ( $found_id ) {
						return absint( $found_id );
					} // if(found_id)

				} // if(img_src)
			} // foreach(img_tag)
		} // if(all_img_tags)
		
		// if nothing found: return 0
		return 0;
	}

}
