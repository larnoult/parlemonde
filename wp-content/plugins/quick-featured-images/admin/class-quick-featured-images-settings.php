<?php
/**
 * Quick Featured Images
 *
 * @package   Quick_Featured_Images_Settings
 * @author    Martin Stehle <m.stehle@gmx.de>
 * @license   GPL-2.0+
 * @link      http://wordpress.org/plugins/quick-featured-images/
 * @copyright 2014 
 */

/**
 * @package Quick_Featured_Images_Settings
 * @author    Martin Stehle <m.stehle@gmx.de>
 */
class Quick_Featured_Images_Settings {

	/**
	 * Instance of this class.
	 *
	 * @since    7.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Required user capability to use this plugin
	 *
	 * @since   7.0
	 *
	 * @var     string
	 */
	protected $required_user_cap = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    7.0
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
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since    7.0
	 *
	 * @var     string
	 */
	protected $plugin_version = null;

	/**
	 * Unique identifier in the WP options table for the plugin's settings
	 *
	 *
	 * @since    7.0
	 *
	 * @var      string
	 */
	protected $settings_db_slug = null;

	/**
	 * Slug of the menu page on which to display the form sections
	 *
	 *
	 * @since    7.0
	 *
	 * @var      array
	 */
	protected $main_options_page_slug = 'quick-featured-images-optionspage';

	/**
	 * Group name of options
	 *
	 *
	 * @since    7.0
	 *
	 * @var      array
	 */
	protected $settings_fields_slug = 'quick-featured-images-options';
	
	/**
	 * Stored settings in an array
	 *
	 *
	 * @since    7.0
	 *
	 * @var      array
	 */
	protected $stored_settings = array();

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     7.0
	 */
	private function __construct() {

		// Call variables from public plugin class.
		$plugin = Quick_Featured_Images_Admin::get_instance();
		$this->plugin_name = $plugin->get_plugin_name();
		$this->plugin_slug = $plugin->get_plugin_slug();
		$this->page_slug = $this->plugin_slug . '-settings';
		$this->parent_page_slug = $plugin->get_page_slug();
		$this->plugin_version = $plugin->get_plugin_version();
		$this->settings_db_slug = $plugin->get_settings_db_slug();

		// get settings
		$settings = $this->get_stored_settings();
		if ( isset( $settings[ 'minimum_role_all_pages' ] ) ) {
			switch ( $settings[ 'minimum_role_all_pages' ] ) {
				case 'administrator':
					$this->required_user_cap = 'manage_options';
					break;
				default:
					$this->required_user_cap = 'manage_options';
			}
		} else {
			$this->required_user_cap = 'manage_options';
		}
		
		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );

		// Add the options page and menu item.
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

		// Add an action link pointing to the options page.
		$plugin_basename = plugin_basename( plugin_dir_path( __DIR__ ) . $this->plugin_slug . '.php' );
		add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );

		/*
		 * Add registering options
		 *
		 */
		add_action( 'admin_init', array( $this, 'register_options' ) );

	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    7.0
	 */
	public function main() {
		$this->display_header();
		include_once( 'views/section_settings.php' );
		$this->display_footer();
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     7.0
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
	 * @since    7.0
	 *
	 *@return    page headline variable.
	 */
	public function get_page_headline() {
		$text = 'Settings';
		return __( $text );
	}

	/**
	 * Return the page description.
	 *
	 * @since    8.0
	 *
	 *@return    page description variable.
	 */
	public function get_page_description() {
		return __( 'Set the visibility of columns of featured images in posts lists', 'quick-featured-images' );
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
	 * @since     7.0
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
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    7.0
	 */
	public function add_plugin_admin_menu() {

		// get translated string of the menu label and page headline
		$label = $this->get_page_headline();
		
		// Add a settings page for this plugin to the Settings menu.
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
	 * Add settings action link to the plugins page.
	 *
	 * @since    7.0
	 */
	public function add_action_links( $links ) {
		$url = sprintf( 'admin.php?page=%s', $this->page_slug );
		return array_merge(
			$links,
			array(
				'settings' => sprintf( '<a href="%s">%s</a>', esc_url( admin_url( $url ) ), esc_html( $this->get_page_headline() ) )
			)
		);

	}

	/**
	 * Set default settings
	 *
	 * @since    7.0
	 */
	private static function set_default_settings() {

		// check if there are already stored settings under the option's database slug
		if ( false === get_option( 'quick-featured-images-settings', false ) ) {
			// store default values in the db as a single and serialized entry
			add_option( 
				'quick-featured-images-settings', 
				array(
					'column_thumb_post' => '1',
					'column_thumb_page' => '1',
					'minimum_role_all_pages' => 'editor',
				)
			);
		} // if ( false )
		
	}

	/**
	 * Get current or default settings
	 *
	 * @since    7.0
	 */
	public function get_stored_settings() {
		// try to load current settings. If they are not in the DB return set default settings
		$stored_settings = get_option( $this->settings_db_slug, false );
		// if empty array set and store default values
		if ( false === $stored_settings ) {
			$this->set_default_settings();
		}
		// try to load current settings again. Now there should be the data
		$stored_settings = get_option( $this->settings_db_slug, false );
		
		return $stored_settings; # todo: return $this->sanitize_stored_settings( $stored_settings );
	}
	
	/**
	* Define and register the options
	* Run on admin_init()
	*
	* @since   7.0
	*/
	public function register_options () {

		$title = null;
		$html = null;
		
		// get current or default settings
		$this->stored_settings = $this->get_stored_settings();

		/*
		 *
		 * 1st section: column toggles
		 *
		 */
		 
		$section_key = '1st_section';
		// register the section
		add_settings_section(
			// 'id' attribute of tags
			$section_key, 
			// title of the section.
			__( 'Columns for featured images in posts lists', 'quick-featured-images' ),
			// callback function that fills the section with the desired content
			array( $this, 'print_section_' . $section_key ),
			// menu page on which to display this section
			$this->main_options_page_slug
		); // end add_settings_section()

		// register the options for the section
		$title = __( 'Show additional column for featured images in lists of', 'quick-featured-images' );
		add_settings_field(
			// form field name for use in the 'id' attribute of tags
			'column_toggles',
			// title of the form field
			$title . sprintf( '<br />&nbsp;<br /><img src="%s" alt="%s" width="200" height="104" />', plugins_url( 'assets/images/posts_list_w_image_column.gif' , __FILE__ ), esc_attr__( 'Posts list with image column', 'quick-featured-images' ) ),
			// callback function to print the form field
			array( $this, 'print_columns_options' ),
			// menu page on which to display this field for do_settings_section()
			$this->main_options_page_slug,
			// section where the form field appears
			$section_key,
			// arguments passed to the callback function 
			array( $title )
		); // end add_settings_field()

		/*
		 *
		 * 2nd section: menu display options
		 *
		 */
		 
		$section_key = '2nd_section';
		// register the section
		add_settings_section(
			// 'id' attribute of tags
			$section_key, 
			// title of the section.
			__( 'Visibility of the plugin', 'quick-featured-images' ),
			// callback function that fills the section with the desired content
			array( $this, 'print_section_' . $section_key ),
			// menu page on which to display this section
			$this->main_options_page_slug
		); // end add_settings_section()

		// register the options for the section
		$title = __( 'Which user role may see the plugin?', 'quick-featured-images' );
		add_settings_field(
			// form field name for use in the 'id' attribute of tags
			'allowed_roles',
			// title of the form field
			$title,
			// callback function to print the form field
			array( $this, 'print_role_control' ),
			// menu page on which to display this field for do_settings_section()
			$this->main_options_page_slug,
			// section where the form field appears
			$section_key,
			// arguments passed to the callback function 
			array( $title )
		); // end add_settings_field()

		/*
		 * Finally register all options. They will be stored in the database 
		 * in the wp_options table under the options name $this->settings_db_slug.
		 */
		register_setting( 
			// group name in settings_fields()
			$this->settings_fields_slug,
			// name of the option to sanitize and save in the db
			$this->settings_db_slug,
			// callback function that sanitizes the option's values.
			array( $this, 'sanitize_options' )
		); // end register_setting()
		
	} // end register_options()

	/**
	* Check and return correct values for the settings
	*
	* @since   7.0
	*
	* @param   array    $input    Options and their values after submitting the form
	* 
	* @return  array              Options and their sanatized values
	*/
	public function sanitize_options ( $input ) {
		// exit default array if null input
		if ( ! $input ) {
			return array(
				'column_thumb_post' => '1',
				'column_thumb_page' => '1',
				'minimum_role_all_pages' => 'editor',
			);
		}
		$sanitized_input = array();
		foreach ( $input as $key => $value ) {
			// checkboxes
			if ( preg_match( '/^column_thumb_[a-z0-9_\-]+$/', $key ) ) {
				$sanitized_input[ $key ] = isset( $input[ $key ] ) ? '1' : '0' ;
			}
			// selections
			if ( $key == 'minimum_role_all_pages' ) {
				if ( in_array( $value, array( 'administrator', 'editor', 'author' ) ) ) {
					$sanitized_input[ $key ] = $value;
				}
			}
			
		} // foreach()

		// default settinga if not defined
		if ( ! array_key_exists( 'minimum_role_all_pages', $sanitized_input ) ) {
			$sanitized_input[ 'minimum_role_all_pages' ] = 'editor';				
		}

		return $sanitized_input;
	} // end sanitize_options()

	/**
	 *
	 * Render the header of the admin page
	 *
	 * @access   private
	 * @since    7.0
	 */
	private function display_header() {
		include_once( 'views/section_header.php' );
	}
	
	/**
	 *
	 * Render the footer of the admin page
	 *
	 * @access   private
	 * @since    7.0
	 */
	private function display_footer() {
		include_once( 'views/section_footer.php' );
	}
	
	/**
	* Print the option
	*
	* @since   7.0
	*
	*/
	public function print_columns_options ( $args ) {
		// get WP core translations; little hack: put strings into variables to avoid PO editors to find them
		$label_posts = 'Posts';
		$label_pages = 'Pages';
		$post_types = array(
			'column_thumb_post' => _x( $label_posts, 'post type general name' ),
			'column_thumb_page' => _x( $label_pages, 'post type general name' ),
		);
		// get the registered custom post types as objects
        $custom_post_types = get_post_types( array( '_builtin' => false ), 'objects' );
		// add their names and labels to the standard WP post types
        foreach ( $custom_post_types as $name => $object ) {
            if ( post_type_supports( $name, 'thumbnail' ) ) {
				$key = sprintf( 'column_thumb_%s', $name );
				$post_types[ $key ] = $object->label; 
            }
		}
		$html = sprintf( '<fieldset><legend class="screen-reader-text"><span>%s</span></legend>', $args[ 0 ] );
		foreach ( $post_types as $value => $label ) {
			$stored_value = isset( $this->stored_settings[ $value ] ) ? esc_attr( $this->stored_settings[ $value ] ) : '0';
			$checked = $stored_value ? checked( '1', $stored_value, false ) : '0';
			$html .= sprintf( 
				'<label for="%s"><input name="%s[%s]" type="checkbox" id="%s" value="1"%s /> %s</label><br />',
				$value,
				$this->settings_db_slug,
				$value,
				$value,
				$checked,
				esc_html( $label )
			);
		} // foreach()
		$html .= '</fieldset>';
		$html .= sprintf( '<p class="description">%s</p>', esc_html__( 'Activate the checkboxes at each post type to show the extra columns in the post lists.', 'quick-featured-images' ) );
		echo $html;
	}

	/**
	* Print the option to set allowed roles displaying the menu items
	*
	* @since   12.0
	*
	*/
	public function print_role_control ( $args ) {
		// get translations
		$label = __( 'Minimum user role to see the plugin in the backend', 'quick-featured-images' );
		
		// get WP core translations; little hack: put strings into variables to avoid PO editors to find them
		$label_administrator = 'Administrator';
		$label_editor = 'Editor';
		//$label_author = 'Author';
		$role_names = array(
			'administrator'	=> _x( $label_administrator, 'User role' ),
			'editor'		=> _x( $label_editor, 'User role' ),
			//'author'		=> _x( $label_author, 'User role' ),
		);

		// get user role options HTML
		$options = '';
		$stored_value = isset( $this->stored_settings[ 'minimum_role_all_pages' ] ) ? esc_attr( $this->stored_settings[ 'minimum_role_all_pages' ] ) : 'editor';
		foreach ( $role_names as $role_slug => $role_label ) {
			$options .= sprintf(
				'<option value="%s"%s>%s</option>',
				$role_slug,
				selected( $stored_value, $role_slug, false ),
				esc_html( $role_label )
			);
		}
		
		// define the form sections, order by appereance, with headlines, and options
		$html = sprintf( '<fieldset><legend class="screen-reader-text"><span>%s</span></legend>', $args[ 0 ] );
		$html .= sprintf( '<div>' );
		$html .= sprintf( 
			'<label>%s<br /><select name="%s[%s]">%s</select></label>',
			esc_html( $label ),
			$this->settings_db_slug,
			'minimum_role_all_pages',
			$options
		);
		$html .= '</div>';
		$html .= '</fieldset>';
		$html .= sprintf( '<p class="description">%s</p>', esc_html__( 'Select the minimum user role a user must have to see the plugin.', 'quick-featured-images' ) );
		$text = 'Default Images';
		$html .= sprintf( 
			'<p class="description">%s</p>', 
			sprintf( 
				esc_html__( 'The rules as set in &#8220;%s&#8221; work on posts independently of this setting.', 'quick-featured-images' ),
				//esc_html__( 'Preset Featured Images', 'quick-featured-images' ) 
				esc_html__( $text )
			)
		);
		$html .= sprintf( '<p class="description">%s</p>', esc_html__( 'This setting controls as well whether a user will see in an image column the thumbnails with action links or the thumbnails only. To switch image columns on and off use the section above.', 'quick-featured-images' ) );
		$html .= sprintf( '<p class="description">%s</p>', esc_html__( 'This page is accessible for administrators only.', 'quick-featured-images' ) );
		echo $html;
	}

	/**
	* Print the explanation for section 1
	*
	* @since   7.0
	*/
	public function print_section_1st_section () {
		printf( "<div class=\"qfi_page_description\"><p>%s</p></div>\n", esc_html__( 'The additional columns give you a quick overview about all used featured images for every post. The Featured Image column is sortable.', 'quick-featured-images' ) );
	}

	/**
	* Print the explanation for section 2
	*
	* @since   12.0
	*/
	public function print_section_2nd_section () {
		printf( "<div class=\"qfi_page_description\"><p>%s</p></div>\n", esc_html__( 'Controls which minimum user role can see the plugin.', 'quick-featured-images' ) );
	}

}
