<?php
/**
 * @package   Quick_Featured_Images_Tools
 * @author    Martin Stehle <m.stehle@gmx.de>
 * @license   GPL-2.0+
 * @link      http://stehle-internet.de
 * @copyright 2014 Martin Stehle
 */

class Quick_Featured_Images_Tools { // only for debugging: extends Quick_Featured_Images_Base {

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Required user capability to use this plugin
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	protected $required_user_cap = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * Name of this plugin.
	 *
	 * @since    7.0
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
	 * @since    7.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = null;

	/**
	 * Unique identifier for the admin page of this class.
	 *
	 * @since    7.0
	 *
	 * @var      string
	 */
	protected $page_slug = null;

	/**
	 * Unique identifier for the admin parent page of this class.
	 *
	 * @since    7.0
	 *
	 * @var      string
	 */
	protected $parent_page_slug = null;

	/**
	 * Unique identifier in the WP options table
	 *
	 * @since    12.0
	 *
	 * @var      string
	 */
	private $settings_db_slug = null;

	/**
	 * Valid progress steps
	 *
	 * @since    1.0.0
	 *
	 * @var      array
	 */
	protected $valid_steps = null;

	/**
	 * User selected progress step
	 *
	 * @since    1.0.0
	 *
	 * @var      array
	 */
	protected $selected_step = null;

	/**
	 * Whether an image id is required or not (depends on the selected action)
	 *
	 * @since    2.0
	 *
	 * @var      bool
	 */
	protected $is_image_required = null;

	/**
	 * User selected ID of the new featured image
	 *
	 * @since    1.0.0
	 *
	 * @var      integer
	 */
	protected $selected_image_id = null;

	/**
	 * User selected IDs of the new featured images
	 *
	 * @since    6.0
	 *
	 * @var      array
	 */
	protected $selected_multiple_image_ids = null;

	/**
	 * User selected ID of the featured image to replace
	 *
	 * @since    1.0.0
	 *
	 * @var      integer
	 */
	protected $selected_old_image_ids = null;

	/**
	 * Whether the id of a to be replaced image is set or not
	 *
	 * @since    2.0
	 *
	 * @var      bool
	 */
	protected $is_error_no_old_image = null;

	/**
	 * Whether the user jumps from 'select' directly to 'confirm' omitting 'refine'
	 *
	 * @since    5.1
	 *
	 * @var      bool
	 */
	protected $is_skip_refine = null;

	/**
	 * Width of thumbnail images in the current WordPress settings
	 *
	 * @since    2.0
	 *
	 * @var      integer
	 */
	protected $used_thumbnail_width = null;
	
	/**
	 * Height of thumbnail images in the current WordPress settings
	 *
	 * @since    2.0
	 *
	 * @var      integer
	 */
	protected $used_thumbnail_height = null;
	
	/**
	 * Minimum length of image dimensions to search for
	 *
	 * @since    2.0
	 *
	 * @var      integer
	 */
	protected $min_image_length = null;
	
	/**
	 * Maximum length of image dimensions to search for
	 *
	 * @since    2.0
	 *
	 * @var      integer
	 */
	protected $max_image_length = null;
	
	/**
	 * User selected action the plugin should perform
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $selected_action = null;

	/**
	 * Valid names and descriptions of the actions
	 *
	 * @since    1.0.0
	 *
	 * @var      array
	 */
	protected $valid_actions = null;

	/**
	 * Valid names and descriptions of the actions without a user selected image
	 *
	 * @since    5.0
	 *
	 * @var      array
	 */
	protected $valid_actions_without_image = null;

	/**
	 * Valid names and descriptions of the actions with multiple user selected images
	 *
	 * @since    6.0
	 *
	 * @var      array
	 */
	protected $valid_actions_multiple_images = null;

	/**
	 * User selected filters the plugin should perform
	 *
	 * @since    1.0.0
	 *
	 * @var      array
	 */
	protected $selected_filters = array();

	/**
	 * Valid names and descriptions of the filters
	 *
	 * @since    1.0.0
	 *
	 * @var      array
	 */
	protected $valid_filters = null;

	/**
	 * User selected options the plugin should consider
	 *
	 * @since    5.1
	 *
	 * @var      array
	 */
	protected $selected_options = array();

	/**
	 * Valid names and descriptions of the options
	 *
	 * @since    5.1
	 *
	 * @var      array
	 */
	protected $valid_options = null;

	/**
	 * Valid names and descriptions of the post statuses
	 *
	 * @since    1.0.0
	 *
	 * @var      array
	 */
	protected $valid_statuses = null;
	
	/**
	 * User selected search term
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $selected_search_term = null;
	
	/**
	 * User selected category
	 *
	 * @since    1.0.0
	 *
	 * @var      integer
	 */
	protected $selected_category_id = null;

	/**
	 * User selected tag
	 *
	 * @since    1.0.0
	 *
	 * @var      integer
	 */
	protected $selected_tag_id = null;
	
	/**
	 * User selected names and descriptions of post types supporting featured images by default
	 *
	 * @since    1.0.0
	 *
	 * @var      array
	 */
	protected $selected_post_types = null;
	
	/**
	 * Valid names and descriptions of the post types supporting featured images by default
	 *
	 * @since    1.0.0
	 *
	 * @var      array
	 */
	protected $valid_post_types = null;
	
	/**
	 * User selected post ids
	 *
	 * @since    1.0.0
	 *
	 * @var      array
	 */
	protected $selected_post_ids = null;
	
	/**
	 * Valid names and descriptions of image sizes
	 *
	 * @since    2.0
	 *
	 * @var      array
	 */
	protected $valid_image_dimensions = null;

	/**
	 * User given image sizes
	 *
	 * @since    2.0
	 *
	 * @var      array
	 */
	protected $selected_image_dimensions = null;
	
	/**
	 * Transient reference for temporary storaging of data
	 *
	 * @since     11.0
	 *
	 * @var      string
	 */
	protected $transient_name = null;
		
	 /**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @access   private
	 * @since     1.0.0
	 */
	private function __construct() {

		// Call some values from public plugin class.
		$plugin = Quick_Featured_Images_Admin::get_instance();
		$this->plugin_name = $plugin->get_plugin_name();
		$this->plugin_slug = $plugin->get_plugin_slug();
		$this->page_slug = $this->plugin_slug . '-tools';
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
		
		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Add the admin page and menu item.
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );
		
		// Add 'Bulk set' link in rows of media library list
		add_filter( 'media_row_actions', array( &$this, 'add_media_row_action' ), 10, 2 );

	}
	
	/**
	 * Do the admin main function 
	 *
	 * @since     1.0.0
	 *
	 */
	public function main() {
		// set variables
		$this->set_server_config();
		$this->set_default_values();
		// get current step
		$this->selected_step = $this->get_sanitized_step();
		//$this->dambedei( $_REQUEST );
		//$this->dambedei( $_SERVER );
		/*
		 * print content
		 */
		// no action and image required, just the start page
		if ( 'start' == $this->selected_step ) {
			// print header
			$this->display_header();
			include_once( 'views/form_start.php' );
		} else {
			// get user selected action
			$this->selected_action = $this->get_sanitized_action();
			// check if action is defined, else print error page
			if ( ! $this->selected_action ) {
				$this->display_error( 'wrong_action', false );
			} else {
				if ( 'remove_orphaned' == $this->selected_action ) {
					// if 'perform' step
					if ( 'perform' == $this->selected_step ) {
						check_admin_referer( 'quickfi_confirm', $this->plugin_slug . '_nonce' );
						// filter posts and apply action to found posts
						$results = $this->delete_orphaned();
					} else {
						check_admin_referer( 'quickfi_start', $this->plugin_slug . '_nonce' );
						// find "file-less" thumbnail entries
						$results = $this->find_orphaned();
						if ( $results ) {
							// jump to confirmation step
							$this->selected_step = 'confirm';
						} else {
							// jump to result step directly
							$this->selected_step = 'perform';
						}
					} // if( perform )
					// print header
					$this->display_header();
					if ( 'confirm' == $this->selected_step ) {
						// print refine form again if there are no results
						include_once( 'views/form_confirm.php' );	
					} else {
						// print results
						include_once( 'views/section_results.php' );	
					} // if( confirm )
				} else {
					// check whether thumb id is not required due to selected action
					if ( in_array( $this->selected_action, array_merge( array_keys( $this->valid_actions_without_image ), array_keys( $this->valid_actions_multiple_images ) ) ) ) {
						$this->is_image_required = false;
					}
					// get selected image id, else 0
					$this->selected_image_id = $this->get_sanitized_image_id();
					// get selected image ids, else empty array
					$this->selected_multiple_image_ids = $this->get_sanitized_multiple_image_ids();
					// check whether an image id is available if required
					if ( $this->is_image_required && ! $this->selected_image_id ) {
						$this->display_error( 'no_image', false );
					// check whether selected attachment is an image if required
					} elseif ( $this->is_image_required && ! wp_attachment_is_image( $this->selected_image_id ) ) {
						$this->display_error( 'no_result', sprintf( __( 'Wrong image ID %d', 'quick-featured-images' ), $this->selected_image_id ) );
					// check whether there are selected images if required
					} elseif ( 'assign_randomly' == $this->selected_action && ! $this->selected_multiple_image_ids ) {
						$this->display_error( 'no_image', false );
					} else {
						// get user selected filters
						$this->selected_filters = $this->get_sanitized_filter_names();
						// get user selected options
						$this->selected_options = $this->get_sanitized_option_names();
						// after the old image selection page (filter_replace.php) and if no old image was selected
						if ( 'replace' == $this->selected_action && 'confirm' == $this->selected_step && ! isset( $_POST[ 'replacement_image_ids' ] ) ) {
							// stay on the selection page with a warning
							$this->selected_step = 'select';
							$this->is_error_no_old_image = true;
						// check if user comes from direct link in media page
						} elseif ( 'assign' == $this->selected_action && 'select' == $this->selected_step && $this->selected_image_id && isset( $_REQUEST[ '_wpnonce' ] ) ) {
							// go to the filter selection page directly
							$this->is_direct_access = true;
						// check if user comes from the selection page and has not select any filter
						} elseif ( 'refine' == $this->selected_step	&& empty( $this->selected_filters ) ) {
							// skip refine page, go to the confirm page directly
							$this->selected_step = 'confirm';
							$this->is_skip_refine = true;
						}

						// print header
						$this->display_header();
						// print content based of process
						switch ( $this->selected_step ) {
							case 'select':
								if ( $this->is_error_no_old_image ) {
									check_admin_referer( 'quickfi_refine', $this->plugin_slug . '_nonce' );
								} elseif ( $this->is_direct_access ) {
									// no referer check
									check_admin_referer( 'bulk-assign' );
								} else {
									check_admin_referer( 'quickfi_start', $this->plugin_slug . '_nonce' );
								}
								// print selected thumbnail if required
								include_once( 'views/section_image.php' );	
								// print form to select the posts to apply the action to
								include_once( 'views/form_select.php' );	
								break;
							case 'refine':
								check_admin_referer( 'quickfi_select', $this->plugin_slug . '_nonce' );
								// print selected thumbnail if required
								include_once( 'views/section_image.php' );	
								// print form to refine choice
								include_once( 'views/form_refine.php' );	
								// print form for going back to the filter selection without loosing input data
								include_once( 'views/form_back_to_selection.php' );	
								break;
							case 'confirm':
								if ( $this->is_skip_refine ) {
									check_admin_referer( 'quickfi_select', $this->plugin_slug . '_nonce' );
								} else {
									check_admin_referer( 'quickfi_refine', $this->plugin_slug . '_nonce' );
								}
								// filter posts
								$results = $this->find_posts();
								// print selected thumbnail if required
								include_once( 'views/section_image.php' );	
								// print refine form again if there are no results
								include_once( 'views/form_confirm.php' );	
								// print form to refine choice if filters were selected
								if ( $this->selected_filters ) {
									include_once( 'views/form_refine.php' );	
								}
								// print form for going back to the filter selection without loosing input data
								include_once( 'views/form_back_to_selection.php' );	
								break;
							case 'perform':
								check_admin_referer( 'quickfi_confirm', $this->plugin_slug . '_nonce' );
								// filter posts and apply action to found posts
								$results = $this->perform_action();
								// print results
								include_once( 'views/section_results.php' );	
								// print form for going back to the filter selection without loosing input data
								include_once( 'views/form_back_to_selection.php' );	
								break;
						} // switch( selected step )
					} // if( image available )
				} // if ( remove_orphaned )
			} // if( action available )
		} // if( is start )
		// print footer
		$this->display_footer();
	}
	
	/**
	 * Set variables
	 *
	 * @access   private
	 * @since     1.0.0
	 */
	private function set_default_values() {
		/*
		 * Note: The order of the entries affects the order in the frontend page
		 *
		 */
		// process steps
		$this->valid_steps = array(
			'start'		=> __( 'Select', 'quick-featured-images' ),
			'select'	=> __( 'Add filter', 'quick-featured-images' ),
			'refine' 	=> __( 'Refine', 'quick-featured-images' ),
			'confirm'	=> __( 'Confirm', 'quick-featured-images' ),
			'perform'	=> __( 'Perform', 'quick-featured-images' ),
		);
		// actions
		$this->valid_actions = array(
			'assign'			=> __( 'Set the selected image as new featured image', 'quick-featured-images' ),
			'replace'			=> __( 'Replace featured images by the selected image', 'quick-featured-images' ),
			'remove'			=> __( 'Remove the selected image as featured image', 'quick-featured-images' ),
		);
		$this->valid_actions_without_image = array(
			'remove_any_img'	=> __( 'Remove any image as featured image', 'quick-featured-images' ),
			'remove_orphaned'	=> __( 'Remove all featured images without existing image files', 'quick-featured-images' ),
		);
		$this->valid_actions_multiple_images = array(
			'assign_randomly'	=> __( 'Set multiple images randomly as featured images', 'quick-featured-images' ),
		);
		// process options
		$this->valid_options = array(
			'overwrite'         => __( 'Overwrite featured images', 'quick-featured-images' ),
			'orphans_only'      => __( 'Consider only posts without any featured image', 'quick-featured-images' ),
		);
		// filters
		$this->valid_filters = array(
			'filter_post_types' 		=> __( 'Post Type Filter', 'quick-featured-images' ),
			'filter_category' 			=> __( 'Category Filter', 'quick-featured-images' ),
			'filter_tag' 				=> __( 'Tag Filter', 'quick-featured-images' ),
		);
		// post types (generic)
		$label_posts = 'Posts';
		$label_pages = 'Pages';
		$this->valid_post_types = array(
			'post' => _x( $label_posts, 'post type general name' ),
			'page' => _x( $label_pages, 'post type general name' ),
		);
		// statuses
		$text             = 'Private';
		$label_private    = _x( $text, 'post status' );
		$text             = 'Published';
		$label_publish    = _x( $text, 'post status' );
		$text             = 'Scheduled';
		$label_future     = _x( $text, 'post status' );
		$text             = 'Pending';
		$label_pending    = _x( $text, 'post status' );
		$text             = 'Draft';
		$label_draft      = _x( $text, 'post status' );
		$this->valid_statuses = array(
			'publish' => $label_publish,
			'pending' => $label_pending,
			'draft'   => $label_draft,
			'future'  => $label_future,
			'private' => $label_private
		);
		// image dimensions
		$this->valid_image_dimensions = array(
			'max_width' 	=> __( 'Image width in pixels lower than', 'quick-featured-images' ),
			'max_height' 	=> __( 'Image height in pixels lower than', 'quick-featured-images' ),
		);
		// default: user selected image is required
		$this->is_image_required = true;
		// default: start form
		$this->selected_step = 'start';
		// default: no images
		$this->selected_old_image_ids = array();
		$this->selected_image_id = 0;
		$this->selected_multiple_image_ids = array();
		$this->is_error_no_old_image = false;
		$this->is_direct_access = false;
		$this->is_skip_refine = false;
		// default: no category
		$this->selected_category_id = 0;
		// default: no tag
		$this->selected_tag_id = 0;
		// default: all post types
		$this->selected_post_types = array_keys( $this->valid_post_types ); // default: posts and pages
		// default: no selected posts
		$this->selected_post_ids = array();
		// get user defined dimensions for thumbnails, else take 150 px and set maximum value if necessary
		$max_dimension = 160; // width of thumbnail column in px at 1024 px window width
		$this->used_thumbnail_width  = get_option( 'thumbnail_size_w', $max_dimension );
		$this->used_thumbnail_height = get_option( 'thumbnail_size_h', $max_dimension );
		$this->used_thumbnail_width = $this->used_thumbnail_width > $max_dimension ? $max_dimension : $this->used_thumbnail_width;
		$this->used_thumbnail_height = $this->used_thumbnail_height > $max_dimension ? $max_dimension : $this->used_thumbnail_height;
		 // default:  stored sizes for thumbnails
		$this->selected_image_dimensions = array(
			'max_width' 	=> $this->used_thumbnail_width,
			'max_height' 	=> $this->used_thumbnail_height,
		);
		// default: min 1 x 1 px, max 9999 x 9999 px images
		$this->min_image_length = 1;
		$this->max_image_length = 9999;
		// slug for cached results
		$this->transient_name = 'quick_featured_images_results';
	}
	
	/**
	 * Set server timeout for PHP scripts in seconds
	 *
	 * @access   private
	 * @since    12.2
	 */
	private function set_server_config() {
		// to prevent blank pages for this script:
		// set server timeout to 3000 seconds if lower
		$value = (int) ini_get( 'max_execution_time' );
		if ( 2999 > $value ) {
			ini_set( 'max_execution_time', '3000' );
		}
		// and set allowed memory space to 512 MB if lower
		preg_match( '/(\d+)(\w+)/', ini_get( 'memory_limit' ), $matches );
		if ( $matches ) {
			$value = (int) $matches[ 1 ];
			switch ( strtolower( $matches[ 2 ] ) ) {
				case 'g':
				case 'gb':
					$value *= 1024;
				case 'm':
				case 'mb':
					$value *= 1024;
				case 'k':
				case 'kb':
					$value *= 1024;
			}
			
			if ( 500000000 > $value ) {
				ini_set( 'memory_limit', '512M' );
			}
		}
	}
	
	/**
	 *
	 * Render the header of the admin page
	 *
	 * @access   private
	 * @since    1.0.0
	 */
	private function display_header() {
		include_once( 'views/section_header_progress.php' );
	}
	
	/**
	 *
	 * Render the footer of the admin page
	 *
	 * @access   private
	 * @since    1.0.0
	 */
	private function display_footer() {
		include_once( 'views/section_footer.php' );
	}
	
	/**
	 *
	 * Render the error page
	 *
	 * @access   private
	 * @since    1.0.0
	 */
	private function display_error( $reason, $value_name ) {	
		// print header
		$this->display_header();
		// print error message
		switch ( $reason ) {
			case 'missing_input_value':
				$msg = sprintf( __( 'The input field %s is empty.', 'quick-featured-images' ), $value_name );
				$solution = __( 'Type in a value into the input field.', 'quick-featured-images' );
				break;
			case 'missing_variable':
				$msg = sprintf( __( '%s is not defined.', 'quick-featured-images' ), $value_name );
				$solution = __ ('Check how to define the value.', 'quick-featured-images' );
				break;
			case 'no_image':
				$msg = __( 'There is no selected image.', 'quick-featured-images' );
				$solution = __( 'Select an image from the media library.', 'quick-featured-images' );
				break;
			case 'wrong_action':
				$msg = __( 'You have not selected an action.', 'quick-featured-images' );
				$solution = __( 'Start again and select which action you want to apply.', 'quick-featured-images' );
				break;
			case 'wrong_value':
				$msg = sprintf( __( 'The input field %s has an invalid value.', 'quick-featured-images' ), $value_name );
				$solution = __( 'Type in valid values in the input field.', 'quick-featured-images' );
				break;
			case 'no_result':
				$msg = $value_name;
				$solution = __( 'Type in values stored by WordPress.', 'quick-featured-images' );
				break;
		} // switch ( $reason )
		include_once( 'views/section_errormsg.php' );
		//die();
	} // display_error()

	/**
	 * Call the WP Query to find thumbnail entries without existing image files
	 * 
	 * @access   private
	 * @since     13.0
	 *
	 */
	private function find_orphaned() {
		$orphaned_ids = array();
		global $wpdb;
		// get IDs of images flagged as featured images
		$thumb_ids = $wpdb->get_col( "SELECT DISTINCT meta_value FROM $wpdb->postmeta WHERE meta_key = '_thumbnail_id';" );
		// if there are featured images
		if ( $thumb_ids ) {
			// check if the corresponding image files exist
			foreach( $thumb_ids as $thumbnail_id ) {
				$filepath = get_attached_file( $thumbnail_id );
				if ( ! file_exists( $filepath ) ) {
					// collect ID in array
					$orphaned_ids[] = $thumbnail_id;
				}
			}
		}
		// return IDs of "file-less" featured images or empty array
		return $orphaned_ids;
	}

	/**
	 * Call the WP Query to delete all thumbnail entries without existing image files
	 * 
	 * @access   private
	 * @since     13.0
	 *
	 */
	private function delete_orphaned() {
		global $wpdb;
		// look for "file-less" featured images
		$orphaned_ids = $this->find_orphaned();
		// if there are none, return false
		if ( empty( $orphaned_ids ) ) {
			return false;
		} else {
			// delete orphaned thumbnail entries in database, return number of deleted entries or false
			return $wpdb->query( sprintf( "DELETE FROM $wpdb->postmeta WHERE meta_key = '_thumbnail_id' AND meta_value IN ( %s );", implode( ',', $orphaned_ids ) ) );
		}
	}

	/**
	 * Call the WP Query and apply the selected action to found posts
	 * 
	 * Is an alias to 'find_posts( true )' for more readability
	 *
	 * @access   private
	 * @since     1.0.0
	 *
	 */
	private function perform_action() {
		return $this->find_posts( true );
	}

	/**
	 * Do the loop to find posts, change the thumbnail if param is true
	 *
	 * @access   private
	 * @since     1.0.0
	 *
	 * @return    array    affected posts
	 */
	private function find_posts( $perform = false ) {
		// initialise result array 
		$results = array();
		// define thumbnail properties
		$size = array( 
			absint( $this->used_thumbnail_width / 2 ), 
			absint( $this->used_thumbnail_height / 2 ) 
		);
		$attr = array(
			'class' => 'attachment-thumbnail',
			'style' => vsprintf( 'width: %dpx; height: %dpx;', $size ), /* for SVGs */
		);
		// define caching arrays for better performance while calculating attachment images
		$false_id = 'false_id'; // something to use as an array key
		$current_featured_images = array();
		$future_featured_images = array();
		$current_featured_images[ $false_id ] = false;
		$future_featured_images[ $false_id ] = false;
		// get selected options once
		$is_option = array();
		foreach ( array_keys( $this->valid_options ) as $key ) {
			$is_option[ $key ] = in_array( $key, $this->selected_options );
		}

		/* three types of tasks:
			if perform:
				if no transient:
					1: set thumbs via query
				else:
					2: set thumbs via transient
			else:
				3: get preview via query
		*/
		if ( $perform ) { // really make changes
			// check for cached data; use them for fast processing, else use query
			// if removal was selected use query, too
			if ( false === ( $query_results = get_transient( $this->transient_name ) ) ) {
				// they weren't there, so use the query
				$the_query = new WP_Query( $this->get_query_args() );
				//printf( '<p>%s</p>', $the_query->request ); // just for debugging
				// The Loop
				if ( $the_query->have_posts() ) {
					// do task dependent on selected action
					switch ( $this->selected_action ) {
						case 'assign':
							while ( $the_query->have_posts() ) {
								$the_query->the_post();
								// get the post id once
								$post_id = get_the_ID();
								// check if there is an existing featured image
								$thumb_id = $this->get_sanitized_post_thumbnail_id( $post_id );
								// if post with featured images should be ignored, jump to next loop
								if ( $thumb_id and $is_option[ 'orphans_only' ] ) {
									continue;
								}
								$success = false;
								// if no existing featured image or if permission to overwrite it
								if ( ! $thumb_id or $is_option[ 'overwrite' ] ) {
									// set featured image id
									$thumb_id = $this->selected_image_id;
									// do the task
									$success = set_post_thumbnail( $post_id, $thumb_id );
								}
								// get html for featured image for check
								$thumb_id = $this->get_sanitized_post_thumbnail_id( $post_id );
								// if existing featured image
								if ( $thumb_id ) {
									// get thumbnail html if not yet got
									if ( ! isset( $current_featured_images[ $thumb_id ] ) ) {
										$current_featured_images[ $thumb_id ] = wp_get_attachment_image( $thumb_id, $size, false, $attr );
									}
								} else {
									// nothing changed
									$thumb_id = $false_id; // cast from '' or 'false' to a value to use as an array key
								}
								// store edit link, post title, image html, success of action (true or false)
								$results[] = array( 
									get_edit_post_link(), 
									get_the_title(),
									$current_featured_images[ $thumb_id ],
									$success
								);
							} // while(have_posts)
							break;
						case 'assign_randomly':
							$last_index = count( $this->selected_multiple_image_ids ) - 1;
							while ( $the_query->have_posts() ) {
								$the_query->the_post();
								// get the post id once
								$post_id = get_the_ID();
								// check if there is an existing featured image
								$thumb_id = $this->get_sanitized_post_thumbnail_id( $post_id );
								// if post with featured images should be ignored, jump to next loop
								if ( $thumb_id and $is_option[ 'orphans_only' ] ) {
									continue;
								}
								$success = false;
								// if no existing featured image or if permission to overwrite it
								if ( ! $thumb_id or $is_option[ 'overwrite' ] ) {
									// set featured image id
									$thumb_id = $this->selected_multiple_image_ids[ rand( 0, $last_index ) ];
									// do the task
									$success = set_post_thumbnail( $post_id, $thumb_id );
								}
								// get html for featured image for check
								$thumb_id = $this->get_sanitized_post_thumbnail_id( $post_id );
								// if existing featured image
								if ( $thumb_id ) {
									// get thumbnail html if not yet got
									if ( ! isset( $current_featured_images[ $thumb_id ] ) ) {
										$current_featured_images[ $thumb_id ] = wp_get_attachment_image( $thumb_id, $size, false, $attr );
									}
								} else {
									// nothing changed
									$thumb_id = $false_id; // cast from '' or 'false' to a value to use as an array key
								}
								// store edit link, post title, image html, success of action (true or false)
								$results[] = array( 
									get_edit_post_link(), 
									get_the_title(),
									$current_featured_images[ $thumb_id ],
									$success
								);
							} // while(have_posts)
							break;
						case 'replace':
							while ( $the_query->have_posts() ) {
								$the_query->the_post();
								// get the post id once
								$post_id = get_the_ID();
								// do the task
								$success = set_post_thumbnail( $post_id, $this->selected_image_id );
								// get html for featured image for check
								$thumb_id = $this->get_sanitized_post_thumbnail_id( $post_id );
								if ( $thumb_id ) {
									// get thumbnail html if not yet got
									if ( ! isset( $current_featured_images[ $thumb_id ] ) ) {
										$current_featured_images[ $thumb_id ] = wp_get_attachment_image( $thumb_id, $size, false, $attr );
									}
								} else {
									// nothing changed
									$thumb_id = $false_id; // cast from '' or 'false' to a value to use as an array key
								}
								// store edit link, post title, image html, success of action (true or false)
								$results[] = array( 
									get_edit_post_link(), 
									get_the_title(),
									$current_featured_images[ $thumb_id ],
									$success
								);
							} // while(have_posts)
							break;
						case 'remove':
						case 'remove_any_img':
							while ( $the_query->have_posts() ) {
								$the_query->the_post();
								// get the post id once
								$post_id = get_the_ID();
								// do the task
								$success = delete_post_thumbnail( $post_id );
								// get html for featured image for check
								$thumb_id = $this->get_sanitized_post_thumbnail_id( $post_id );
								// if existing featured image
								if ( $thumb_id ) {
									// get thumbnail html if not yet got
									if ( ! isset( $current_featured_images[ $thumb_id ] ) ) {
										$current_featured_images[ $thumb_id ] = wp_get_attachment_image( $thumb_id, $size, false, $attr );
									}
								} else {
									// nothing changed
									$thumb_id = $false_id; // cast from '' or 'false' to a value to use as an array key
								}
								// store edit link, post title, image html, success of action (true or false)
								$results[] = array( 
									get_edit_post_link(), 
									get_the_title(),
									$current_featured_images[ $thumb_id ],
									$success
								);
							} // while(have_posts)
							break;
					} // switch(selected_action)
				} // if( have_posts )
				// Restore original post data after the query
				wp_reset_postdata();
			} else {
				// else if there are cached results
				// do task dependent on selected action
				switch ( $this->selected_action ) {
					case 'assign':
						foreach ( $query_results as $post_id => $post_data ) {
							$thumb_id = $post_data[ 0 ];
							// cast "false" value to boolean false
							if ( $thumb_id == $false_id ) {
								$thumb_id = false;
							}
							// check if there is an existing featured image
							$current_thumb_id = $this->get_sanitized_post_thumbnail_id( $post_id );
							$success = false;
							// if no existing featured image or if permission to overwrite it
							if ( ! $current_thumb_id or $is_option[ 'overwrite' ] ) {
								// do the task
								$success = set_post_thumbnail( $post_id, $thumb_id );
							}
							// get html for featured image for check
							$thumb_id = $this->get_sanitized_post_thumbnail_id( $post_id );
							// if existing featured image
							if ( $thumb_id ) {
								// get thumbnail html if not yet got
								if ( ! isset( $current_featured_images[ $thumb_id ] ) ) {
									$current_featured_images[ $thumb_id ] = wp_get_attachment_image( $thumb_id, $size, false, $attr );
								}
							} else {
								// nothing changed
								$thumb_id = $false_id; // cast from '' or 'false' to a value to use as an array key
							}
							// store edit link, post title, image html, success of action (true or false)
							$results[] = array( 
								$post_data[ 1 ], // get_edit_post_link()
								$post_data[ 2 ], // get_the_title()
								$current_featured_images[ $thumb_id ],
								$success
							);
						} // foreach()
						break;
					case 'assign_randomly':
						foreach ( $query_results as $post_id => $post_data ) {
							$thumb_id = $post_data[ 0 ];
							// cast "false" value to boolean false
							if ( $thumb_id == $false_id ) {
								$thumb_id = false;
							}
							$success = false;
							// check if there is an existing featured image
							$current_thumb_id = $this->get_sanitized_post_thumbnail_id( $post_id );
							// if existing featured image
							if ( $current_thumb_id ) {
								// if new image
								if ( $thumb_id ) {
									// if permission to overwrite existing image
									if ( $is_option[ 'overwrite' ] ) {
										// do the task
										$success = set_post_thumbnail( $post_id, $thumb_id );
									} else {
										// do nothing : keep existing image
									} // if ( overwrite )
								} // if ( new image )
							// if no existing featured image
							} else {
								// if new image
								if ( $thumb_id ) {
									// do the task
									$success = set_post_thumbnail( $post_id, $thumb_id );
								} else {
									// do nothing : no image
								} // if ( new image )
							} // if ( existing image )
							// get html for featured image for check
							$thumb_id = $this->get_sanitized_post_thumbnail_id( $post_id );
							// if existing featured image
							if ( $thumb_id ) {
								// get thumbnail html if not yet got
								if ( ! isset( $current_featured_images[ $thumb_id ] ) ) {
									$current_featured_images[ $thumb_id ] = wp_get_attachment_image( $thumb_id, $size, false, $attr );
								}
							} else {
								// nothing changed
								$thumb_id = $false_id; // cast from '' or 'false' to a value to use as an array key
							}
							// store edit link, post title, image html, success of action (true or false)
							$results[] = array( 
								$post_data[ 1 ], // get_edit_post_link()
								$post_data[ 2 ], // get_the_title()
								$current_featured_images[ $thumb_id ],
								$success
							);
						} // foreach()
						break;
					case 'replace':
						foreach ( $query_results as $post_id => $post_data ) {
							$thumb_id = $post_data[ 0 ];
							// do the task
							$success = set_post_thumbnail( $post_id, $thumb_id );
							// get html for featured image for check
							$thumb_id = $this->get_sanitized_post_thumbnail_id( $post_id );
							if ( $thumb_id ) {
								// get thumbnail html if not yet got
								if ( ! isset( $current_featured_images[ $thumb_id ] ) ) {
									$current_featured_images[ $thumb_id ] = wp_get_attachment_image( $thumb_id, $size, false, $attr );
								}
							} else {
								// nothing changed
								$thumb_id = $false_id; // cast from '' or 'false' to a value to use as an array key
							}
							// store edit link, post title, image html, success of action (true or false)
							$results[] = array( 
								$post_data[ 1 ], // get_edit_post_link()
								$post_data[ 2 ], // get_the_title()
								$current_featured_images[ $thumb_id ],
								$success
							);
						} // foreach()
						break;
					case 'remove':
					case 'remove_any_img':
						foreach ( $query_results as $post_id => $post_data ) {
							$thumb_id = $post_data[ 0 ];
							// do the task
							$success = delete_post_thumbnail( $post_id );
							// get html for featured image for check
							$thumb_id = $this->get_sanitized_post_thumbnail_id( $post_id );
							if ( $thumb_id ) {
								// get thumbnail html if not yet got
								if ( ! isset( $current_featured_images[ $thumb_id ] ) ) {
									$current_featured_images[ $thumb_id ] = wp_get_attachment_image( $thumb_id, $size, false, $attr );
								}
							} else {
								// nothing changed
								$thumb_id = $false_id; // cast from '' or 'false' to a value to use as an array key
							}
							// store edit link, post title, image html, success of action (true or false)
							$results[] = array( 
								$post_data[ 1 ], // get_edit_post_link()
								$post_data[ 2 ], // get_the_title()
								$current_featured_images[ $thumb_id ],
								$success
							);
						} // foreach()
						break;
				} // switch(selected_action)
				// delete cached results manually
				delete_transient( $this->transient_name );
			} // if transient
		} else {
			$query_results = array();
			$the_query = new WP_Query( $this->get_query_args() );
			//printf( '<p>%s</p>', esc_html( $the_query->request ) ); // just for debugging
			// The Loop
			if ( $the_query->have_posts() ) {
				// do task dependent on selected action
				switch ( $this->selected_action ) {
					case 'assign':
						while ( $the_query->have_posts() ) {
							$the_query->the_post();
							// get the post id once
							$post_id = get_the_ID();
							// check if there is an existing featured image
							$current_thumb_id = $this->get_sanitized_post_thumbnail_id( $post_id );
							// if post with featured images should be ignored, jump to next loop
							if ( $current_thumb_id and $is_option[ 'orphans_only' ] ) {
								continue;
							}
							if ( $current_thumb_id ) {
								// get thumbnail html if not yet got
								if ( ! isset( $current_featured_images[ $current_thumb_id ] ) ) {
									$current_featured_images[ $current_thumb_id ] = wp_get_attachment_image( $current_thumb_id, $size, false, $attr );
								}
								// get html of future thumbnail
								if ( $is_option[ 'overwrite' ] ) {
									// preview old thumb + new thumb
									$future_thumb_id = $this->selected_image_id;
									// get thumbnail html if not yet got
									if ( ! isset( $future_featured_images[ $future_thumb_id ] ) ) {
										$future_featured_images[ $future_thumb_id ] = wp_get_attachment_image( $future_thumb_id, $size, false, $attr );
									}
								} else {
									// preview old thumb + old thumb
									$future_thumb_id = $current_thumb_id;
									$future_featured_images[ $future_thumb_id ] = $current_featured_images[ $current_thumb_id ];
								}
							} else {
								// preview no old thumb + new thumb
								$current_thumb_id = $false_id; // cast from '' or 'false' to a value to use as an array key
								// get html of future thumbnail
								$future_thumb_id = $this->selected_image_id;
								// get thumbnail html if not yet got
								if ( ! isset( $future_featured_images[ $future_thumb_id ] ) ) {
									$future_featured_images[ $future_thumb_id ] = wp_get_attachment_image( $future_thumb_id, $size, false, $attr );
								}
							}
							// store edit link, post title, post date, post author, current image html, future image html
							$post_link = get_edit_post_link();
							$post_title = get_the_title();
							$results[] = array( 
								$post_link, 
								$post_title,
								get_the_date(),
								get_the_author(),
								$current_featured_images[ $current_thumb_id ],
								$future_featured_images[ $future_thumb_id ],
								get_post_status(),
								get_post_type(),
							);
							// notice result for cache
							$query_results[ $post_id ] = array( $future_thumb_id, $post_link, $post_title );
						} // while(have_posts)
						break;
					case 'assign_randomly':
						$last_index = count( $this->selected_multiple_image_ids ) - 1;
						/*
						 * 1. use selected images multiple times randomly and
						 * 2. overwrite existing featured images
						 */
						if ( $is_option[ 'overwrite' ] ) {
							while ( $the_query->have_posts() ) {
								$the_query->the_post();
								// get the post id once
								$post_id = get_the_ID();
								// check if there is an existing featured image
								$current_thumb_id = $this->get_sanitized_post_thumbnail_id( $post_id );
								// if post with featured images should be ignored, jump to next loop
								if ( $current_thumb_id and $is_option[ 'orphans_only' ] ) {
									continue;
								}
								// if existing featured image
								if ( $current_thumb_id ) {
									// get thumbnail html if not yet got
									if ( ! isset( $current_featured_images[ $current_thumb_id ] ) ) {
										$current_featured_images[ $current_thumb_id ] = wp_get_attachment_image( $current_thumb_id, $size, false, $attr );
									}
								} else {
									$current_thumb_id = $false_id; // cast from '' or 'false' to a value to use as an array key
								}
								// set image randomly : future image = new image
								$future_thumb_id = $this->selected_multiple_image_ids[ rand( 0, $last_index ) ]; // get thumb id randomly
								// get thumbnail html if not yet got
								if ( ! isset( $future_featured_images[ $future_thumb_id ] ) ) {
									$future_featured_images[ $future_thumb_id ] = wp_get_attachment_image( $future_thumb_id, $size, false, $attr );
								}
								// store edit link, post title, post date, post author, current image html, future image html
								$post_link = get_edit_post_link();
								$post_title = get_the_title();
								$results[] = array( 
									$post_link, 
									$post_title,
									get_the_date(),
									get_the_author(),
									$current_featured_images[ $current_thumb_id ],
									$future_featured_images[ $future_thumb_id ],
									get_post_status(),
									get_post_type(),
								);
								// notice result for cache
								$query_results[ $post_id ] = array( $future_thumb_id, $post_link, $post_title );
							} // while(have_posts)
						/* else 
						 * 1. use selected images multiple times randomly and
						 * 2. do not overwrite existing featured images
						 */
						} else {
							while ( $the_query->have_posts() ) {
								$the_query->the_post();
								// get the post id once
								$post_id = get_the_ID();
								// check if there is an existing featured image
								$current_thumb_id = $this->get_sanitized_post_thumbnail_id( $post_id );
								// if post with featured images should be ignored, jump to next loop
								if ( $current_thumb_id and $is_option[ 'orphans_only' ] ) {
									continue;
								}
								// if existing featured image
								if ( $current_thumb_id ) {
									// get thumbnail html if not yet got
									if ( ! isset( $current_featured_images[ $current_thumb_id ] ) ) {
										$current_featured_images[ $current_thumb_id ] = wp_get_attachment_image( $current_thumb_id, $size, false, $attr );
									}
									// do nothing : future image = current image
									$future_thumb_id = $current_thumb_id;
									$future_featured_images[ $future_thumb_id ] = $current_featured_images[ $current_thumb_id ];
								} else {
									$current_thumb_id = $false_id; // cast from '' or 'false' to a value to use as an array key
									// set image randomly : future image = new image
									$future_thumb_id = $this->selected_multiple_image_ids[ rand( 0, $last_index ) ]; // get thumb id randomly
									// get thumbnail html if not yet got
									if ( ! isset( $future_featured_images[ $future_thumb_id ] ) ) {
										$future_featured_images[ $future_thumb_id ] = wp_get_attachment_image( $future_thumb_id, $size, false, $attr );
									}
								}
								// store edit link, post title, post date, post author, current image html, future image html
								$post_link = get_edit_post_link();
								$post_title = get_the_title();
								$results[] = array( 
									$post_link, 
									$post_title,
									get_the_date(),
									get_the_author(),
									$current_featured_images[ $current_thumb_id ],
									$future_featured_images[ $future_thumb_id ],
									get_post_status(),
									get_post_type(),
								);
								// notice result for cache
								$query_results[ $post_id ] = array( $future_thumb_id, $post_link, $post_title );
							} // while(have_posts)
						} // if ( overwrite )
						break;
					case 'replace':
						while ( $the_query->have_posts() ) {
							$the_query->the_post();
							// get the post id once
							$post_id = get_the_ID();
							// check if there is an existing featured image
							$current_thumb_id = $this->get_sanitized_post_thumbnail_id( $post_id );
							if ( $current_thumb_id ) {
								// get thumbnail html if not yet got
								if ( ! isset( $current_featured_images[ $current_thumb_id ] ) ) {
									$current_featured_images[ $current_thumb_id ] = wp_get_attachment_image( $current_thumb_id, $size, false, $attr );
								}
							} else {
								$current_thumb_id = $false_id; // cast from '' or 'false' to a value to use as an array key
							}
							// get html of future thumbnail
							$future_thumb_id = $this->selected_image_id;
							// get thumbnail html if not yet got
							if ( ! isset( $future_featured_images[ $future_thumb_id ] ) ) {
								$future_featured_images[ $future_thumb_id ] = wp_get_attachment_image( $future_thumb_id, $size, false, $attr );
							}
							// store edit link, post title, post date, post author, current image html, future image html
							$post_link = get_edit_post_link();
							$post_title = get_the_title();
							$results[] = array( 
								$post_link, 
								$post_title,
								get_the_date(),
								get_the_author(),
								$current_featured_images[ $current_thumb_id ],
								$future_featured_images[ $future_thumb_id ],
								get_post_status(),
								get_post_type(),
							);
							// notice result for cache
							$query_results[ $post_id ] = array( $future_thumb_id, $post_link, $post_title );
						} // while(have_posts)
						break;
					case 'remove':
					case 'remove_any_img':
						$future_thumb_id = false;
						while ( $the_query->have_posts() ) {
							$the_query->the_post();
							// get the post id once
							$post_id = get_the_ID();
							// get html for featured image
							$current_thumb_id = $this->get_sanitized_post_thumbnail_id( $post_id );
							if ( $current_thumb_id ) {
								// get thumbnail html if not yet got
								if ( ! isset( $current_featured_images[ $current_thumb_id ] ) ) {
									$current_featured_images[ $current_thumb_id ] = wp_get_attachment_image( $current_thumb_id, $size, false, $attr );
								}
							} else {
								$current_thumb_id = $false_id; // cast from '' or 'false' to a value to use as an array key
							}
							// store edit link, post title, post date, post author, current image html, future image html
							$post_link = get_edit_post_link();
							$post_title = get_the_title();
							$results[] = array( 
								$post_link, 
								$post_title,
								get_the_date(),
								get_the_author(),
								$current_featured_images[ $current_thumb_id ],
								$future_thumb_id,
								get_post_status(),
								get_post_type(),
							);
							// notice result for cache
							$query_results[ $post_id ] = array( $future_thumb_id, $post_link, $post_title );
						} // while(have_posts)
						break;
				} // switch(selected_action)
			} // if( have_posts )
			// Restore original post data after the query
			wp_reset_postdata();
			// store results as transient for 1 day at the longest
			set_transient( $this->transient_name, $query_results, DAY_IN_SECONDS );
		} // if perform

		// return results
		return $results;
	}
	
	/**
	 * Check the arguments for WP_Query depended on users selection
	 *
	 * @access   private
	 * @since     1.0.0
	 *
	 * @return    array    the args
	 */
	private function get_query_args() {
		// define default params
		$args[ 'posts_per_page' ] =  -1; // do not use pagination, return whole result at once
		$args[ 'no_found_rows' ] = true; // since no pagination: tell WordPress not to run SQL_CALC_FOUND_ROWS on the SQL query; drastically speeding up the query
		$args[ 'orderby' ] = 'title';
		$args[ 'order' ] = 'ASC';
		$args[ 'ignore_sticky_posts' ] = true;
		$args[ 'post_type' ] = $this->selected_post_types;
		switch ( $this->selected_action ) {
			case 'replace':
				$this->selected_post_ids = $this->get_post_ids_of_old_thumbnails();
				$args[ 'post__in' ] = $this->get_id_array_for_query( $this->selected_post_ids );
				break;
			case 'remove':
				$this->selected_post_ids = $this->get_post_ids_of_thumbnail();
				$args[ 'post__in' ] = $this->get_id_array_for_query( $this->selected_post_ids );
				break;
		} // switch(selected_action)

		if ( $this->selected_filters ) {
			foreach ( $this->selected_filters as $filter ) {
				switch ( $filter ) {
					case 'filter_post_types':
						$this->selected_post_types = $this->get_sanitized_post_types();
						if ( $this->selected_post_types ) {
							$args[ 'post_type' ] = $this->selected_post_types;
						} else {
							// add a fictitious post type to get no result (and not to get a list of all posts)
							$args[ 'post_type' ] = 'abcdefghi'; // assume there is not and will be never a post type with this name
						}
						break;
					case 'filter_category':
						$this->selected_category_id = $this->get_sanitized_category_id();
						// if there is a selected category assign it to the query
						if ( 0 < $this->selected_category_id ) {
							$args[ 'cat' ] = $this->selected_category_id; // todo: user selects more than 1 category, 'category__in'
						}
						break;
					case 'filter_tag':
						$this->selected_tag_id = $this->get_sanitized_tag_id();
						// if there is a selected tag assign it to the query
						if ( 0 < $this->selected_tag_id ) {
							$args[ 'tag_id' ] = $this->selected_tag_id; // todo: user selects more than 1 tag, 'tag__in'
						}
						break;
				} // switch(filter)
			} // foreach(selected_filters)
		} // if(selected_filters)
		#$this->dambedei($args);
		return $args;
	}

	/**
	 *
	 * Render options of HTML selection lists with strings as values
	 *
	 * @access   private
	 * @since     1.0.0
	 */
	private function get_html_options_strings( $arr, $key, $options, $first_empty = true ) {
		$output = $first_empty ? $this->get_html_empty_option() : '';
		$is_key = isset( $arr[ $key ] );
		if ( $is_key ) { 
			foreach ( $options as $key => $label ) {
				$output .= sprintf( '<option value="%s" %s>%s</option>', $key, selected( $is_key , true, false ), esc_html( $label ) );
			}
		} else {
			foreach ( $options as $key => $label ) {
				$output .= sprintf( '<option value="%s">%s</option>', $key, esc_html( $label ) );
			}
		}
		return $output;
	}
	
	/**
	 *
	 * Return empty option for selection field
	 *
	 * @access   private
	 * @since    3.0
	 */
	private function get_html_empty_option() {
		$text = '&mdash; Select &mdash;';
		return sprintf( '<option value="">%s</option>', esc_html__( $text ) );
	}

	/**
	 * Returns the post ids which are assigned with the featured images which should be replaced
	 *
	 * @access   private
	 * @since     1.0.0
	 *
	 * @return    array    the post ids assigned with the thumbnails
	 */
	private function get_post_ids_of_old_thumbnails() {
		$key = 'replacement_image_ids';
		if ( isset( $_POST[ $key ] ) ) {
			if ( is_array( $_POST[ $key ] ) ) {
				$this->selected_old_image_ids = $this->get_sanitized_array( $key, $this->get_featured_image_ids() );
			} else {
				$this->selected_old_image_ids = explode( ',', $_POST[ $key ] );
			}
			return $this->get_post_ids_of_featured_image_ids( $this->selected_old_image_ids );
		} else {
			return array();
		}
	}

	/**
	 * Returns the post ids which are assigned with the featured image which should be removed
	 *
	 * @access   private
	 * @since     1.0.0
	 *
	 * @return    array    the post ids assigned with the thumbnail
	 */
	private function get_post_ids_of_thumbnail() {
		$post_ids = array();
		global $wpdb;
		// get a normal array all names of meta keys except the WP builtins meta keys beginning with an underscore '_'
		$results = $wpdb->get_results( $wpdb->prepare( "SELECT `post_id` FROM $wpdb->postmeta WHERE `meta_key` = '_thumbnail_id' AND `meta_value` = %d", $this->selected_image_id ), ARRAY_N );
		// flatten and sanitize results
		if ( $results ) {
			foreach ( $results as $r ) {
				$post_ids[] = absint( $r[ 0 ] );
			}
		}
		if ( empty( $post_ids ) ) {
			$post_ids[] = 0; // enter at least one element with no sense to yield 0 results with WP_QUERY()
		}
		return $post_ids;
	}
	
	/**
	 * Returns the posts ids which are assigned to given featured image ids
	 *
	 * @access   private
	 * @since     2.0
	 *
	 * @return    array    the post ids assigned to given featured images
	 */
	private function get_post_ids_of_featured_image_ids( $image_ids = array() ) {
		$post_ids = array();
		global $wpdb;
		// get a normal array with all IDs of posts assigned with the image ids
		foreach ( $image_ids as $id ) {
			$results = $wpdb->get_results( $wpdb->prepare( "SELECT `post_id` FROM $wpdb->postmeta WHERE `meta_key` = '_thumbnail_id' AND `meta_value` = %d", $id ), ARRAY_N );
			// flatten and sanitize results
			if ( $results ) {
				foreach ( $results as $r ) {
					$post_ids[] = absint( $r[ 0 ] );
				}
			}
		} // foreach()
		return $post_ids;
	}

	/**
	 * Returns the thumbnails ids which are assigned with a post
	 *
	 * @access   private
	 * @since     1.0.0
 	 *
	 * @return    array    the image ids assigned to posts as featured images
	 */
	private function get_featured_image_ids() {
		$image_ids = array();
		global $wpdb;
		// get a normal array all names of meta keys except the WP builtins meta keys beginning with an underscore '_'
		$results = $wpdb->get_results( $wpdb->prepare( "SELECT DISTINCT `meta_value` FROM $wpdb->postmeta WHERE `meta_key` LIKE '_thumbnail_id' AND `meta_value` != %d", $this->selected_image_id ), ARRAY_N );
		// flatten and sanitize results
		if ( $results ) {
			foreach ( $results as $r ) {
				$image_ids[] = absint( $r[ 0 ] );
			}
		}
		return $image_ids;
	}

	/**
	 * Check the step parameter and return safe values
	 *
	 * @access   private
	 * @since     1.0.0
	 *
	 * @return    string    the name of the step the plugin should take
	 */
	private function get_sanitized_step() {
		return $this->get_sanitized_value(
			'step',
			array_keys( $this->valid_steps ),
			'start'
		);
	}

	/**
	 * Check the action parameter and return safe values 
	 *
	 * @access   private
	 * @since     1.0.0
	 *
	 * @return    string    the name of the action the plugin should perform, else empty string
	 */
	private function get_sanitized_action() {
		return $this->get_sanitized_value(
			'action',
			array_keys( array_merge( $this->valid_actions, $this->valid_actions_without_image, $this->valid_actions_multiple_images ) )
		);
	}

	/**
	 * Check the requested filters and return safe values 
	 *
	 * @access   private
	 * @since     1.0.0
	 *
	 * @return    array    the names of the filters
	 */
	private function get_sanitized_filter_names() {
		return $this->get_sanitized_array(
			'filters',
			array_keys( $this->valid_filters )
		);
	}

	/**
	 * Check the requested options and return safe values 
	 *
	 * @access   private
	 * @since     5.1
	 *
	 * @return    array    the names of the options
	 */
	private function get_sanitized_option_names() {
		return $this->get_sanitized_array(
			'options',
			array_keys( $this->valid_options )
		);
	}

	/**
	 * Check the requested post types and return safe values 
	 *
	 * @access   private
	 * @since     1.0.0
	 *
	 * @return    array    the names of the selected post types
	 */
	private function get_sanitized_post_types() {
		return $this->get_sanitized_array(
			'post_types',
			array_keys( $this->valid_post_types )
		);
	}

	/**
	 * Check the parameter defined by key and return safe value
	 * Written to return a single value, e.g. for radio buttons
	 *
	 * @access   private
	 * @since     1.0.0
	 *
	 * @return    mixed    the user selected valid value or the default value
	 */
	private function get_sanitized_value( $key, $valid_values, $default_value = null ) {
		$value = isset( $_REQUEST[ $key ] ) ? $_REQUEST[ $key ] : $default_value;
		if ( in_array( $value, $valid_values ) ) {
			return $value;            
		} else {                       
			return $default_value;          
		}                             
	}

	/**
	 * Check the parameter and return safe values 
	 * Written to return multiple values, e.g. for checkboxes
	 *
	 * @access   private
	 * @since     1.0.0
	 *
	 * @return    array    the user selected valid values or the default values
	 */
	private function get_sanitized_array( $key, $valid_array, $default_array = array() ) {
		if ( isset( $_POST[ $key ] ) and is_array( $_POST[ $key ] ) ) {
			return $this->get_array_intersect( $_POST[ $key ], $valid_array );
		} else {
			return $default_array;
		}
	}

	/**
	 * Check the parameters and return safe values 
	 * Written to return multiple values associated with key names, e.g. for WP Query
	 * The function filters out empty strings
	 *
	 * @access   private
	 * @since     1.0.0
	 *
	 * @return    array    the user selected valid values or the default values
	 */
	private function get_sanitized_associated_array( $key, $valid_array, $default_array = array() ) {
		$queries = array();
		$arr = isset( $_POST[ $key ] ) ? $_POST[ $key ] : $default_array;
		if ( ! empty( $arr ) && is_array( $arr ) ) {
			foreach ( array_keys( $valid_array ) as $key ) {
				if ( array_key_exists( $key, $arr ) and isset( $arr[ $key ] ) ) {
					$queries[ $key ] = $arr[ $key ];
				}
			}
		}
		return $queries;
	}

	/**
	 * Return the intersection of two given arrays
	 * Runs 5 times faster than PHP's array_intersect()
	 *
	 * @access   private
	 * @since     1.0.0
	 *
	 * @return    array    the intersection of two arrays
	 */
	private function get_array_intersect( $a, $b ) { 
		$m = array(); 
		$intersection = array(); 
		// copy first array to array
		$len = sizeof( $a );
		for( $i = 0; $i < $len; $i++ ) { 
			$m[] = $a[ $i ]; 
		} 
		// append second array to array
		$len = sizeof( $b );
		for( $i = 0; $i < $len; $i++ ) { 
			$m[] = $b[ $i ]; 
		} 
		// make values sorted
		sort( $m ); 
		// compare value with the next one and append to intersection array if equal
		$len = sizeof( $m ) - 1;
		for( $i = 0; $i < $len; $i++ ) { 
			if ( $m[ $i ] == $m[ $i + 1 ] ) $intersection[] = $m[ $i ]; 
		} 
		// return intersection
		return $intersection; 
	}
	
	/**
	 * Check the integer value of a user selected value else default value
	 *
	 * @access   private
	 * @since     1.0.0
	 *
	 * @return    integer    the id or 0
	 */
	private function get_sanitized_id( $key, $default = 0 ) {
		$given_id = absint( sanitize_text_field( $_REQUEST[ $key ]  ) );
		if ( ( ! isset( $_REQUEST[ $key ] ) ) or empty( $_REQUEST[ $key ] ) or 0 > $given_id ) {
			return $default;
		} else {
			return $given_id;
		}
	}
	
	/**
	 * Check the id of selected featured image and return safe value
	 *
	 * @access   private
	 * @since     1.0.0
	 *
	 * @return    integer    the id or 0
	 */
	private function get_sanitized_image_id() {
		return $this->get_sanitized_id( 'image_id' );
	}
	
	/**
	 * Check the ids of selected featured images and return safe value
	 *
	 * @access   private
	 * @since     6.0
	 *
	 * @return    array    the ids or empty
	 */
	private function get_sanitized_multiple_image_ids() {
		if ( ! isset( $_POST[ 'multiple_image_ids' ] ) or empty( $_POST[ 'multiple_image_ids' ] ) ) {
			return array();
		} else {
			// read: sanatize string, make array out of string, convert each array value to integer, return result array
			return array_map( 'absint', explode( ',', sanitize_text_field( $_POST[ 'multiple_image_ids' ] ) ) );
		}
	}
	
	/**
	 * Check the id of selected tag and return safe value
	 *
	 * @access   private
	 * @since     1.0.0
	 *
	 * @return    integer    the id or 0
	 */
	private function get_sanitized_tag_id() {
		return $this->get_sanitized_id( 'tag_id' );
	}
	
	/**
	 * Check the id of selected category and return safe value
	 *
	 * @access   private
	 * @since     1.0.0
	 *
	 * @return    integer    the id or 0
	 */
	private function get_sanitized_category_id() {
		return $this->get_sanitized_id( 'category_id' );
	}

	/**
	 * Get the ID of a post's featured image, else 0
	 *
	 * @access   private
	 * @since     11.2
	 *
	 * @return    integer    the id or 0
	 */
	private function get_sanitized_post_thumbnail_id( $post_id ) {
		// check if an image with the given ID exists in the media library, else set id to 0
		$current_thumb_id = (int) get_post_thumbnail_id( $post_id );
		if ( $current_thumb_id and wp_attachment_is_image( $current_thumb_id ) ) {
			return $current_thumb_id;
		} else {
			return 0;
		}
	}

	/**
	 * If results in array, return them, else say query something like "no results in array"
	 *
	 * @access   private
	 * @since     2.0
	 *
	 * @return    array    Array with content or 0
	 */
	private function get_id_array_for_query( $arr ) {
		if ( empty( $arr ) ) {
			return array( 0 );
		} else {
			return $arr;
		}
	}
	
	/**
	 * Returns the url of the plugin's admin part
	 *
	 * @since    1.0.0
	 */
	public function get_plugin_admin_url() {
		return plugin_dir_url( __FILE__ );
	}

	/**
	 * Returns the url of the plugin's images folder without an trailing slash	
	 *
	 * @since    1.0.0
	 */
	public function get_admin_images_url() {
		return sprintf( '%s/assets/images', $this->get_plugin_admin_url() );
	}
	
	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		/*
		 * @TODO :
		 *
		 * - Uncomment following lines if the admin class should only be available for super admins
		 */
		/* if( ! is_super_admin() ) {
			return;
		} */

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Return the page headline.
	 *
	 * @since    7.0
	 *
	 *@return    page headline variable.
	 */
	public function get_page_headline() {
		//return __( 'Set, replace, remove', 'quick-featured-images' );
		$text = 'Bulk Edit';
		return __( $text );
		// just for the translation editor to catch this string
		$text = __( 'Set, replace, remove', 'quick-featured-images' );
	}

	/**
	 * Return the page description.
	 *
	 * @since    8.0
	 *
	 *@return    page description variable.
	 */
	public function get_page_description() {
		return __( 'Bulk set, replace and remove featured images for existing posts', 'quick-featured-images' );
	}

	/**
	 * Return the page slug.
	 *
	 * @since    7.0
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
	 * @since     1.0.0
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
			wp_enqueue_style( $this->plugin_slug .'-admin-styles', plugins_url( 'assets/css/admin.css', __FILE__ ), array(), $this->plugin_version );
		}

	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {
			// load script
			wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'assets/js/admin.js', __FILE__ ), array( 'jquery' ), $this->plugin_version );
			// Enqueue all stuff to use media API, requires at least WP 3.5
			wp_enqueue_media();
		}

	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since     1.0.0
	 */
	public function add_plugin_admin_menu() {

		// get translated string of the menu label and page headline
		$label = $this->get_page_headline();
		
		/*
		 * Add the top level menu page of this plugin
		 *
		 */
		//$this->plugin_screen_hook_suffix = add_object_page(...);
		$this->plugin_screen_hook_suffix = add_submenu_page( 
			$this->parent_page_slug, // parent_slug
			sprintf( '%s: %s', $this->plugin_name, $label ), // page_title
			$label, // menu_title
			$this->required_user_cap, // capability to use the following function
			$this->page_slug, // menu_slug
			array( $this, 'main' ) // function to execute when loading this page
		);		
	}
	
	/**
	 * Add a "Bulk set" link to the media row actions
	 *
	 * @since    4.1
	 */
	function add_media_row_action( $actions, $post ) {

		// if current media is not an image or user has not the right or thumbnails are not supported return without change
		if ( 'image/' != substr( $post->post_mime_type, 0, 6 ) || ! current_user_can( $this->required_user_cap ) || ! current_theme_supports( 'post-thumbnails' ) )
			return $actions;
		
		// else build the link with nonce
		$url = wp_nonce_url( admin_url( sprintf( 'admin.php?page=%s&step=select&action=assign&image_id=%d', $this->page_slug, $post->ID ) ), 'bulk-assign' );
		
		// add it
		$actions['quick-featured-images'] = sprintf( '<a href="%s">%s</a>', esc_url( $url ), esc_html__( 'Bulk set as featured image', 'quick-featured-images' ) );
		
		// return extended action links list
		return $actions;
	}

}

