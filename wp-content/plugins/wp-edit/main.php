<?php
/**
 * Plugin Name: WP Edit
 * Plugin URI: https://wpeditpro.com
 * Description: Ultimate WordPress Content Editing.
 * Version: 4.0.3
 * Author: Josh Lobe
 * Author URI: https://wpeditpro.com
 * License: GPL2
 * Text Domain: wp-edit
 * Domain Path: /langs
*/

/*
****************************************************************
Define plugin url
****************************************************************
*/
define('WPEDIT_PLUGIN_URL', plugins_url('', __FILE__).'/');
define('WPEDIT_PLUGIN_PATH', plugin_dir_path(__FILE__));


/*
****************************************************************
Begin Plugin Class
****************************************************************
*/
class wp_edit_class {
	
	/*
	****************************************************************
	Define WP Edit Plugin Options
	****************************************************************
	*/
	public $global_options_buttons = array(
		'toolbar1' => 'bold italic strikethrough bullist numlist blockquote alignleft aligncenter alignright link unlink wp_more hr', 
		'toolbar2' => 'formatselect underline alignjustify forecolor pastetext removeformat charmap outdent indent undo redo wp_help', 
		'toolbar3' => '', 
		'toolbar4' => '',
		'tmce_container' => 'fontselect fontsizeselect styleselect backcolor media rtl ltr table anchor code emoticons inserttime wp_page preview print searchreplace visualblocks subscript superscript image_orig advlink acheck abbr columnShortcodes nonbreaking eqneditor'
	);
	public $global_options_global = array(
		'jquery_theme' => 'smoothness',
		'disable_admin_links' => '0',
		'disable_fancy_tooltips' => '0'
	);
	public $global_options_general = array(
		'linebreak_shortcode' => '0',
		'shortcodes_in_widgets' => '0',
		'shortcodes_in_excerpts' => '0',
		'post_excerpt_editor' => '0',
		'page_excerpt_editor' => '0',
		'profile_editor' => '0',
		'cpt_excerpt_editor' => array()
	);
	public $global_options_posts = array(
		'post_title_field' => 'Enter title here',
		'max_post_revisions' => '',
		'max_page_revisions' => '',
		'delete_revisions' => '0',
		'hide_admin_posts' => '',
		'hide_admin_pages' => '',
		'disable_wpautop' => '0',
		'column_shortcodes' => '0'
	);
	public $global_options_editor = array(
		'editor_add_pre_styles' => '0',
		'default_editor_fontsize_type' => 'pt',
		'default_editor_fontsize_values' => '',
		'bbpress_editor' => '0'
	);
	public $global_options_extras = array(
		'signoff_text' => 'Please enter text here...'
	);
	public $global_options_user_specific = array(
		'id_column' => '0',
		'thumbnail_column' => '0',
		'hide_text_tab' => '0',
		'default_visual_tab' => '0',
		'dashboard_widget' => '0',
		'enable_highlights' => '0',
		
		'draft_highlight' => '#FFFFFF',
		'pending_highlight' => '#FFFFFF',
		'published_highlight' => '#FFFFFF',
		'future_highlight' => '#FFFFFF',
		'private_highlight' => '#FFFFFF'
	);
	
	// Prepare global settings array (for future use)
	public $wpedit_options_array = array();
	
	public $filtered_buttons = array();
	public $new_plugin_array = array();
	public $default_buttons_array = array();
	public $filtered_plugin_buttons = array();
	
	/*
	****************************************************************
	Class construct
	****************************************************************
	*/
	public function __construct() {
		
		register_activation_hook( __FILE__, array( $this, 'plugin_activate' ) );  // Plugin activation hook
		
		add_action('plugins_loaded', array($this, 'wp_edit_load_translation'));  // Language localization
		
		add_filter('plugin_action_links_'.plugin_basename(__FILE__), array($this, 'plugin_settings_link'));  // Set plugin settings links
		
		add_action('admin_init', array($this, 'upgrade_notice'));  // Dismissable upgrade notice
		
		add_action('admin_menu', array($this, 'add_page'));  // Register main admin page
		add_action('admin_init', array($this, 'process_activation_redirect'));   // Redirect after plugin activation
		add_action('admin_init', array($this, 'process_settings_export'));  // Export db options
		add_action('admin_init', array($this, 'process_settings_import'));  // Import db options 
		
		add_action('admin_enqueue_scripts', array($this, 'admin_plugins_page_stylesheet'));
		
		add_action('before_wp_tiny_mce', array($this, 'before_wp_tiny_mce'));  // Add dashicons to tinymce
		add_filter('tiny_mce_before_init', array($this, 'wp_edit_tiny_mce_before_init'));  // Before tinymce initialization
		add_action('init', array($this, 'wp_edit_init_tinymce'));  // Tinymce initialization
		
		add_filter('format_for_editor', array($this, 'htlmedit_pre'));  // Filter html content if wpautop is disabled
		
		$plugin_file   = basename( __FILE__ );
		$plugin_folder = basename( dirname( __FILE__ ) );
		$plugin_hook   = "in_plugin_update_message-{$plugin_folder}/{$plugin_file}";
		add_action($plugin_hook, array($this, 'wpedit_plugin_update_cb'), 10, 2);  // Plugin update message
		add_action('admin_footer', array($this, 'wpedit_plugin_update_js'));  // Plugin update message javascript
		
		
		// Populate this plugin filtered buttons
		$filter_args = array();
		$get_filters = apply_filters( 'wp_edit_custom_buttons', $filter_args );
		$filters_array = array();
		
		// If the array set is not empty (filters being applied)
		if(  ! empty( $get_filters ) ) {
			foreach( $get_filters as $key => $values ) {
				
				$filters_array[] = $values;
			}
		}
		$this->filtered_buttons = $filters_array;
	}
	
	/*
	****************************************************************
	Activation hook
	****************************************************************
	*/
	public function plugin_activate() {
		
		global $current_user;
		
		// Get DB values
		$options_buttons = get_option('wp_edit_buttons');
		$options_global = get_option('wp_edit_global');
		$options_general = get_option('wp_edit_general');
		$options_posts = get_option('wp_edit_posts');
		$options_editor = get_option('wp_edit_editor');
		$options_extras = get_option('wp_edit_extras');
		$options_user_specific = get_user_meta($current_user->ID, 'aaa_wp_edit_user_meta', true);
		
		// Check if DB value exists.. if YES, then keep value.. if NO, then replace with protected defaults
		$options_buttons = $options_buttons ? $options_buttons : $this->global_options_buttons;
		$options_global = $options_global ? $options_global : $this->global_options_global;
		$options_general = $options_general ? $options_general : $this->global_options_general;
		$options_posts = $options_posts ? $options_posts : $this->global_options_posts;
		$options_editor = $options_editor ? $options_editor : $this->global_options_editor;
		$options_extras = $options_extras ? $options_extras : $this->global_options_extras;
		$options_user_specific = $options_user_specific ? $options_user_specific : $this->global_options_user_specific;
		
		// Set DB values
		update_option('wp_edit_buttons', $options_buttons);
		update_option('wp_edit_global', $options_global);
		update_option('wp_edit_general', $options_general);
		update_option('wp_edit_posts', $options_posts);
		update_option('wp_edit_editor', $options_editor);
		update_option('wp_edit_extras', $options_extras);
		update_user_meta($current_user->ID, 'aaa_wp_edit_user_meta', $options_user_specific);
		
		// Add option for redirect
		add_option('wp_edit_activation_redirect', true);
	}
	
	/*
	****************************************************************
	Language localization
	****************************************************************
	*/
	public function wp_edit_load_translation() {
		
		load_plugin_textdomain( 'wp-edit' );
	}
	
	/*
	****************************************************************
	Plugin settings links
	****************************************************************
	*/
	public function plugin_settings_link($links) {
		
		$settings_link = '<a href="admin.php?page=wp_edit_options">'.__('Settings', 'wp-edit').'</a>';
		$settings_link2 = '<a target="_blank" href="https://wpeditpro.com">'.__('Go Pro!', 'wp-edit').'</a>';
  		array_push( $links, $settings_link, $settings_link2 );
  		return $links;
	}
	
	/*
	****************************************************************
	Dismissable upgrade notice
	****************************************************************
	*/
	public function upgrade_notice() {
		
		// Define variables
		global $pagenow;
		global $current_user;
		$userid = $current_user->ID;
			
		// If we are only on plugins.php admin page...
		if($pagenow === 'plugins.php') {
		
			//******************************************************/
			// Check 30 day installation notice
			//******************************************************/
		
			// Check if plugin install date is set in database
			$opt_install = get_option('wp_edit_install');
			if($opt_install === false) {
				
				// Set install date to today
				update_option('wp_edit_install', date('Y-m-d'));
			}
			
			// Compare install date with today
			$date_install = isset($opt_install) ? $opt_install : date('Y-m-d');
			
			// If install date is more than 30 days old...
			if(strtotime($date_install) < strtotime('-30 days')){
					
				// If the user clicked to dismiss notice...
				if ( isset( $_GET['dismiss_wpedit_ug_notice'] ) && 'yes' == $_GET['dismiss_wpedit_ug_notice'] ) {
					
					// Update user meta
					add_user_meta( $userid, 'ignore_wpedit_ag_notice', 'yes', true );
				}
				
				// If user meta is not set...
				if ( !get_user_meta( $userid, 'ignore_wpedit_ag_notice' ) ) {
					
					// Alert plugin update message
					function wpedit_wordpress_version_notice() {
						
						global $pagenow;
						
						echo '<div class="updated" style="padding: 0; margin: 0; border: none; background: none;">
								<div class="wpedit_plugins_page_banner">
									<a href="'.$pagenow.'?dismiss_wpedit_ug_notice=yes"><img class="close_icon" title="" src="'. plugins_url( 'images/close_banner.png', __FILE__ ) .'" alt=""/></a>
									<div class="button_div">
										<a class="button" target="_blank" href="https://wpeditpro.com">Learn More</a>				
									</div>
									<div class="text">
										It\'s time to consider upgrading <strong>WP Edit</strong> to the <strong>PRO</strong> version.<br />
										<span>Extend standard plugin functionality with new, enhanced options.</span>
									</div>
								</div>  
							</div>';
					}
					add_action('admin_notices', 'wpedit_wordpress_version_notice');
				}
			}
		
			//******************************************************/
			// Check Custom Buttons API notice
			//******************************************************/
			// If the user clicked to dismiss notice...
			if ( isset( $_GET['dismiss_wpedit_custom_buttons_notice'] ) && 'yes' == $_GET['dismiss_wpedit_custom_buttons_notice'] ) {
				
				// Update user meta
				add_user_meta( $userid, 'ignore_wpedit_custom_buttons_notice', 'yes', true );
			}
			
			// If user meta is not set...
			if ( !get_user_meta( $userid, 'ignore_wpedit_custom_buttons_notice' ) ) {
				
				// Alert plugin update message
				function wpedit_custom_buttons_notice() {
					
					global $pagenow;
					
					echo '<div class="updated" style="padding: 0; margin: 0; border: none; background: none;">
							<div class="wpedit_plugins_page_banner">
								<a href="'.$pagenow.'?dismiss_wpedit_custom_buttons_notice=yes"><img class="close_icon" title="" src="'. plugins_url( 'images/close_banner.png', __FILE__ ) .'" alt=""/></a>
								<div class="button_div">
									<a class="button" target="_blank" href="http://learn.wpeditpro.com/custom-buttons-api/">Learn More</a>				
								</div>
								<div class="text">
									Introducing the WP Edit Custom Buttons API<br />
									<span>Tell all your favorite plugin/theme developers they can now add their editor buttons to WP Edit and WP Edit Pro.</span>
								</div>
							</div>  
						</div>';
				}
				add_action('admin_notices', 'wpedit_custom_buttons_notice');
			}
			
		}
	}
	
	public function admin_plugins_page_stylesheet( $hook ) {
		
		if( $hook == 'plugins.php' ) {
		
			wp_register_style( 'wp_edit_admin_plugins_page_styles', plugin_dir_url( __FILE__ ) . 'css/admin_plugins_page.css', array() ); // Main Admin Page Script File
			wp_enqueue_style( 'wp_edit_admin_plugins_page_styles' );
		}
	}
	
	
	/*
	****************************************************************
	Page Functions
	****************************************************************
	*/
	public function add_page() {
		
		$wp_edit_page = add_menu_page(__('WP Edit', 'wp-edit'), __('WP Edit', 'wp-edit'), 'manage_options', 'wp_edit_options', array($this, 'options_do_page'));
		add_action('admin_print_scripts-'.$wp_edit_page, array($this, 'admin_scripts'));
		add_action('admin_print_styles-'.$wp_edit_page, array($this, 'admin_styles'));
		add_action('load-'.$wp_edit_page, array($this, 'load_page'));
	}
	public function admin_scripts() {
		
		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('jquery-ui-sortable');
		wp_enqueue_script('jquery-ui-dialog');
		wp_enqueue_script('jquery-ui-tooltip');
		wp_enqueue_script('jquery-ui-tabs');
		wp_enqueue_script('wp-color-picker');
		
		wp_register_script( 'wp_edit_js', plugin_dir_url( __FILE__ ) . 'js/admin.js', array() ); // Main Admin Page Script File
		wp_enqueue_script( 'wp_edit_js' );
		
		// Pass WP variables to main JS script
        $wp_vars = array( 'jwl_plugin_url' => plugin_dir_url( __FILE__ ));
        wp_localize_script( 'wp_edit_js', 'jwlWpVars', $wp_vars);  // Set wp-content
	}
	public function admin_styles() {
		
		$options = get_option('wp_edit_global');
		$select_theme = isset($options['jquery_theme']) ? $options['jquery_theme'] : 'smoothness';
		
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style('dashicons');
		
		?><link rel="stylesheet" href="//code.jquery.com/ui/1.10.3/themes/<?php echo $select_theme; ?>/jquery-ui.css"><?php
		?><link rel="stylesheet" href="<?php echo includes_url().'js/tinymce/skins/lightgray/skin.min.css' ?>"><?php
		
		wp_register_style('wp_edit_css', plugin_dir_url( __FILE__ ) . ('css/admin.css'), array());  // css for admin panel presentation
		wp_enqueue_style('wp_edit_css');
	}
	
	
	/*
	****************************************************************
	Display Page
	****************************************************************
	*/
	public function options_do_page() {
		
		?>
        <div class="wrap">
        
        	<div id="icon-themes" class="icon32"></div>
        	<h2><?php _e('WP Edit Settings', 'wp-edit'); ?></h2>
            
            <?php 
			settings_errors(); 
			$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'buttons';
			
			
			/******************************************************************************/
			// Make filtered button comparison; alert users if counts do not match
			/******************************************************************************/
			// First; get all buttons saved in database
			$plugin_buttons = get_option( 'wp_edit_buttons', $this->global_options_buttons );
			$plugin_buttons_string = '';
			
			foreach( $plugin_buttons as $key => $button_string ) {
				
				if( !empty( $button_string ) )
					$plugin_buttons_string .= $button_string. ' ';
			}
			
			$plugin_buttons_string = rtrim( $plugin_buttons_string, ' ' );
			$explode_buttons_string = explode( ' ', $plugin_buttons_string );
			
			
			
			// Second; get all plugin default buttons
			$plugin_buttons = $this->global_options_buttons;
			
			// Merge all default plugin buttons into single array
			$all_array = '';
			foreach($plugin_buttons as $slot_array) {
				
				if(!empty($slot_array) && $slot_array != '') {  // Skip containter array if empty
					$all_array .= $slot_array.' ';  // Create single string of all default plugin buttons
				}
			}
			$all_array = rtrim($all_array, ' ');  // Remove trailing right space
			$plugin_array = explode(' ', $all_array);  // Explode at spaces to make single array (this is an array of all current plugin buttons)
			
			
			// Third; add filtered buttons to second array
			$get_filters = $this->filtered_buttons;
			
			// If the array set is not empty (filters being applied)
			if(  ! empty( $get_filters ) ) {
				foreach( $get_filters as $key => $values ) {
					
					$plugin_array[] = $values['button_id'];
				}
			}
			
			
			// Create an array of buttons that have been removed
			$array_diff = array_diff( $explode_buttons_string, $plugin_array );
			
			
			// Fourth; make comparison and alert user if filtered buttons have been removed (deactivated)
			if( count( $plugin_array ) < count( $explode_buttons_string ) ) {
				
				?>
				<div class="error wpep_info">
				
					<p>
						<?php  _e('The following buttons have been removed:', 'wp_edit_pro'); ?><br />
						<strong>
						<?php
						$buttons = '';
						foreach( $array_diff as $key => $button ) { $buttons .=  $button . ', '; }
						$buttons = rtrim( $buttons, ', ' );
						echo $buttons;
						?>
						</strong><br /><br />
						<?php  _e('These buttons came from a plugin or theme that has been deactivated.', 'wp_edit_pro'); ?><br />
						<?php  _e('To remove this message; simply visit the "Buttons" tab and save the buttons.', 'wp_edit_pro'); ?><br />
					</p>
				</div>
				<?php
			}
			?>
            
            <h2 class="nav-tab-wrapper">  
                <a href="?page=wp_edit_options&tab=buttons" class="nav-tab <?php echo $active_tab == 'buttons' ? 'nav-tab-active' : ''; ?>"><?php _e('Buttons', 'wp-edit'); ?></a>
                <a href="?page=wp_edit_options&tab=global" class="nav-tab <?php echo $active_tab == 'global' ? 'nav-tab-active' : ''; ?>"><?php _e('Global', 'wp-edit'); ?></a>
                <a href="?page=wp_edit_options&tab=general" class="nav-tab <?php echo $active_tab == 'general' ? 'nav-tab-active' : ''; ?>"><?php _e('General', 'wp-edit'); ?></a>
                <a href="?page=wp_edit_options&tab=posts" class="nav-tab <?php echo $active_tab == 'posts' ? 'nav-tab-active' : ''; ?>"><?php _e('Posts/Pages', 'wp-edit'); ?></a>
                <a href="?page=wp_edit_options&tab=editor" class="nav-tab <?php echo $active_tab == 'editor' ? 'nav-tab-active' : ''; ?>"><?php _e('Editor', 'wp-edit'); ?></a>
                <a href="?page=wp_edit_options&tab=extras" class="nav-tab <?php echo $active_tab == 'extras' ? 'nav-tab-active' : ''; ?>"><?php _e('Extras', 'wp-edit'); ?></a>
                <a href="?page=wp_edit_options&tab=user_specific" class="nav-tab <?php echo $active_tab == 'user_specific' ? 'nav-tab-active' : ''; ?>"><?php _e('User Specific', 'wp-edit'); ?></a>
                <a href="?page=wp_edit_options&tab=database" class="nav-tab <?php echo $active_tab == 'database' ? 'nav-tab-active' : ''; ?>"><?php _e('Database', 'wp-edit'); ?></a>
        		<a href="?page=wp_edit_options&tab=about" class="nav-tab <?php echo $active_tab == 'about' ? 'nav-tab-active' : ''; ?>"><?php _e('About', 'wp-edit'); ?></a>
            </h2>  
            
			<?php 
			/*
			****************************************************************
			Buttons Tab
			****************************************************************
			*/
            if($active_tab == 'buttons'){
				
				$options_buttons = get_option( 'wp_edit_buttons', $this->global_options_buttons );
	
				echo '<div class="main_container">';
				
					echo '<div id="main_buttons_container" class="main_buttons_container_float">';
					
						echo '<h3>';
							_e('WP Edit Buttons', 'wp-edit');
						echo '</h3>';
						?>
						
						<div class="metabox-holder"> 
							<div class="postbox">
								
								<div class="inside wpep_act_button_area" id="inside_button_hover">
									<h3><?php _e('Button Rows', 'wp-edit'); ?></h3>
							
									<?php
									$no_tooltips = false;
									$icons_filter = '';
									
									$options_global = get_option('wp_edit_global');
									if(isset($options_global['disable_fancy_tooltips']) && $options_global['disable_fancy_tooltips'] === '1') {
										
										$no_tooltips = true;
									}
									
									// Loop each toolbar and create array of icons (for later comparison)
									foreach( $options_buttons as $toolbar => $icons ) {
									
										if(!empty($icons)) {
										
											$icons_filter .= ' ' . $icons;
										}
									}
									
									// Loop all buttons and create sortable divs
									foreach ($options_buttons as $toolbar => $icons) {
										
										if($toolbar === 'tmce_container') {
											?><h3><?php _e('Button Container', 'wp-edit'); ?></h3><?php
										}
										
										// Disregard rows 3 and 4
										if($toolbar === 'toolbar1' || $toolbar === 'toolbar2' || $toolbar === 'tmce_container') {
										
										
											echo '<div id="'.$toolbar.'" class="ui-state-default sortable">';
												
												// Create array of icons
												if(!empty($icons)) {
													$icons = explode(' ', $icons);
												}
												
												// Loop icons (if is array)
												if(is_array($icons)) {
													foreach ($icons as $icon) {
														
														$class = ''; $title = ''; $text = ''; $style = ''; $tooltip = array('title' => '', 'content' => '');
																
														// WP Buttons included by default
														if($icon === 'bold') { 
															$class = 'dashicons dashicons-editor-bold'; 
															$title = 'Bold'; 
															$tooltip['title'] = 'Bold'; 
															$tooltip['content'] = '<p>Apply <strong>bold</strong> to editor text.</p>';
														}
														else if($icon === 'italic') { 
															$class = 'dashicons dashicons-editor-italic'; 
															$title = 'Italic'; 
															$tooltip['title'] = 'Italic'; 
															$tooltip['content'] = '<p>Apply <em>italic</em> to editor text.</p>';
														}
														else if($icon === 'strikethrough') { 
															$class = 'dashicons dashicons-editor-strikethrough'; 
															$title = 'Strikethrough'; 
															$tooltip['title'] = 'Strikethrough'; 
															$tooltip['content'] = '<p>Apply <strike>strikethrough</strike> to editor text.</p>';
														}
														else if($icon === 'bullist') { 
															$class = 'dashicons dashicons-editor-ul'; 
															$title = 'Bullet List'; 
															$tooltip['title'] = 'Bullet List'; 
															$tooltip['content'] = '<p>Create a list of bulleted items.</p>'; 
														}
														else if($icon === 'numlist') { 
															$class = 'dashicons dashicons-editor-ol'; 
															$title = 'Numbered List'; 
															$tooltip['title'] = 'Numbered List'; 
															$tooltip['content'] = '<p>Create a list of numbered items.</p>'; 
														}
														else if($icon === 'blockquote') { 
															$class = 'dashicons dashicons-editor-quote'; 
															$title = 'Blockquote'; 
															$tooltip['title'] = 'Blockquote'; 
															$tooltip['content'] = '<p>Insert a block level quotation.</p>';  
														}
														else if($icon === 'hr') { 
															$class = 'dashicons dashicons-minus'; 
															$title = 'Horizontal Rule'; 
															$tooltip['title'] = 'Horizontal Rule'; 
															$tooltip['content'] = '<p>Insert a horizontal rule.</p>';
														}
														else if($icon === 'alignleft') { 
															$class = 'dashicons dashicons-editor-alignleft'; 
															$title = 'Align Left';
															$tooltip['title'] = 'Align Left'; 
															$tooltip['content'] = '<p>Align editor content to the left side of the editor.</p>';
														}
														else if($icon === 'aligncenter') { 
															$class = 'dashicons dashicons-editor-aligncenter'; 
															$title = 'Align Center'; 
															$tooltip['title'] = 'Align Center'; 
															$tooltip['content'] = '<p>Align editor content to the center of the editor.</p>';
														}
														else if($icon === 'alignright') { 
															$class = 'dashicons dashicons-editor-alignright'; 
															$title = 'Align Right'; 
															$tooltip['title'] = 'Align Right'; 
															$tooltip['content'] = '<p>Align editor content to the right side of the editor.</p>';
														}
														else if($icon === 'link') { 
															$class = 'dashicons dashicons-admin-links'; 
															$title = 'Link'; 
															$tooltip['title'] = 'Link'; 
															$tooltip['content'] = '<p>Insert a link around currently selected content.</p>';
														}
														else if($icon === 'unlink') { 
															$class = 'dashicons dashicons-editor-unlink'; 
															$title = 'Unlink'; 
															$tooltip['title'] = 'Unlink'; 
															$tooltip['content'] = '<p>Remove the link around currently selected content.</p>';
														}
														else if($icon === 'wp_more') { 
															$class = 'dashicons dashicons-editor-insertmore'; 
															$title = 'More'; 
															$tooltip['title'] = 'More'; 
															$tooltip['content'] = '<p>Inserts the read_more() WordPress function; commonly used for excerpts.</p>';
														}
														
														else if($icon === 'formatselect') { 
															$title = 'Format Select';
															$text = 'Paragraph';
															$tooltip['title'] = 'Paragraph'; 
															$tooltip['content'] = '<p>Adds the Format Select dropdown button; used to select different styles.</p>';
														}
														else if($icon === 'underline') { 
															$class = 'dashicons dashicons-editor-underline';
															$title = 'Underline';
															$tooltip['title'] = 'Underline'; 
															$tooltip['content'] = '<p>Apply <u>underline</u> to editor text.</p>';
														}
														else if($icon === 'alignjustify') { 
															$class = 'dashicons dashicons-editor-justify';
															$title = 'Align Full';
															$tooltip['title'] = 'Align Full'; 
															$tooltip['content'] = '<p>Align selected content to full width of the page.</p>';
														}
														else if($icon === 'forecolor') { 
															$class = 'dashicons dashicons-editor-textcolor';
															$title = 'Foreground Color';
															$tooltip['title'] = 'Foreground Color'; 
															$tooltip['content'] = '<p>Change the foreground color of selected content; commonly used to change text color.</p>';
														}
														else if($icon === 'pastetext') { 
															$class = 'dashicons dashicons-editor-paste-text';
															$title = 'Paste Text';
															$tooltip['title'] = 'Paste Text'; 
															$tooltip['content'] = '<p>Paste content as plain text.</p>';
														}
														else if($icon === 'removeformat') { 
															$class = 'dashicons dashicons-editor-removeformatting';
															$title = 'Remove Format';
															$tooltip['title'] = 'Remove Format'; 
															$tooltip['content'] = '<p>Remove all current formatting from selected content.</p>';
														}
														else if($icon === 'charmap') { 
															$class = 'dashicons dashicons-editor-customchar';
															$title = 'Character Map';
															$tooltip['title'] = 'Character Map'; 
															$tooltip['content'] = '<p>Display a characted map used for inserting special characters.</p>';
														}
														else if($icon === 'outdent') { 
															$class = 'dashicons dashicons-editor-outdent';
															$title = 'Outdent';
															$tooltip['title'] = 'Outdent'; 
															$tooltip['content'] = '<p>Outdent selected content; primary used for paragraph elements.</p>';
														}
														else if($icon === 'indent') { 
															$class = 'dashicons dashicons-editor-indent';
															$title = 'Indent';
															$tooltip['title'] = 'Indent'; 
															$tooltip['content'] = '<p>Indent selected content; primary used for paragraph elements.</p>';
														}
														else if($icon === 'undo') { 
															$class = 'dashicons dashicons-undo';
															$title = 'Undo';
															$tooltip['title'] = 'Undo'; 
															$tooltip['content'] = '<p>Undo last editor action.</p>';
														}
														else if($icon === 'redo') { 
															$class = 'dashicons dashicons-redo';
															$title = 'Redo';
															$tooltip['title'] = 'Redo'; 
															$tooltip['content'] = '<p>Redo last editor action.</p>';
														}
														else if($icon === 'wp_help') { 
															$class = 'dashicons dashicons-editor-help';
															$title = 'Help';
															$tooltip['title'] = 'Help'; 
															$tooltip['content'] = '<p>Displays helpful information such as editor information and keyboard shortcuts.</p>';
														}
														
														// WP Buttons not included by default
														else if($icon === 'fontselect') { 
															$title = 'Font Select'; 
															$text = 'Font Family'; 
															$tooltip['title'] = 'Font Select'; 
															$tooltip['content'] = '<p>Apply various fonts to the editor selection.</p><p>Also displays fonts from Google Fonts options (if activated).</p>';
														}
														else if($icon === 'fontsizeselect') { 
															$title = 'Font Size Select'; 
															$text = 'Font Sizes'; 
															$tooltip['title'] = 'Font Size Select'; 
															$tooltip['content'] = '<p>Apply various font sizes to the editor selection.</p><p>Default values can be switched from "pt" to "px" via the Editor tab.</p>';
														}
														else if($icon === 'styleselect') { 
															$title = 'Formats'; 
															$text = 'Formats'; 
															$tooltip['title'] = 'Formats'; 
															$tooltip['content'] = '<p>Displays quick access to formats like "Headings", "Inline", "Blocks" and "Alignment".</p><p>Any custom styles created (Styles Tab) will also be shown here.</p>';
														}
														else if($icon === 'backcolor') { 
															$title = 'Background Color Picker'; 
															$text = '<i class="mce-ico mce-i-backcolor"></i>'; 
															$tooltip['title'] = 'Background Color Picker'; 
															$tooltip['content'] = '<p>Change the background color of selected content; commonly used for high-lighting text.</p>';
														}
														else if($icon === 'media') { 
															$class = 'dashicons dashicons-format-video'; 
															$title = 'Media'; 
															$tooltip['title'] = 'Media'; 
															$tooltip['content'] = '<p>Insert media from an external resource (by link); or embed media content into editor.</p>';
														}
														else if($icon === 'rtl') { 
															$title = 'Text Direction Right to Left'; 
															$text = '<i class="mce-ico mce-i-rtl"></i>'; 
															$tooltip['title'] = 'Text Direction Right to Left'; 
															$tooltip['content'] = '<p>Forces the text direction from right to left on selected block element.</p>';
														}
														else if($icon === 'ltr') { 
															$title = 'Text Direction Left to Right'; 
															$text = '<i class="mce-ico mce-i-ltr"></i>'; 
															$tooltip['title'] = 'Text Direction Left to Right'; 
															$tooltip['content'] = '<p>Forces the text direction from left to right on selected block element.</p>';
														}
														else if($icon === 'table') { 
															$title = 'Tables'; 
															$text = '<i class="mce-ico mce-i-table"></i>';
															$tooltip['title'] = 'Tables'; 
															$tooltip['content'] = '<p>Insert, edit and modify html tables.</p>';
														}
														else if($icon === 'anchor') { 
															$title = 'Anchor'; 
															$text = '<i class="mce-ico mce-i-anchor"></i>'; 
															$tooltip['title'] = 'Anchor'; 
															$tooltip['content'] = '<p>Create an anchor link on the page.</p>';
														}
														else if($icon === 'code') { 
															$title = 'HTML Code'; 
															$text = '<i class="mce-ico mce-i-code"></i>';
															$tooltip['title'] = 'HTML Code'; 
															$tooltip['content'] = '<p>Displays the html code of the editor content; in a popup window.</p><p>This can be helpful when editing code is necessary, but switching editor views is undesirable.</p><p>Also, the "Code Magic" button provides a much better interface.</p>';
														}
														else if($icon === 'emoticons') { 
															$title = 'Emoticons'; 
															$text = '<i class="mce-ico mce-i-emoticons"></i>'; 
															$tooltip['title'] = 'Emoticons'; 
															$tooltip['content'] = '<p>Opens an overlay window with access to common emoticons.</p>';
														}
														else if($icon === 'inserttime') { 
															$title = 'Insert Date Time'; 
															$text = '<i class="mce-ico mce-i-insertdatetime"></i>'; 
															$tooltip['title'] = 'Insert Date Time'; 
															$tooltip['content'] = '<p>Inserts the current date and time into the content editor.</p><p>The date format can be adjusted using the "Configuration" tab.</p>';
														}
														else if($icon === 'wp_page') { 
															$title = 'Page Break'; 
															$text = '<i class="mce-ico mce-i-pagebreak"></i>'; 
															$tooltip['title'] = 'Page Break'; 
															$tooltip['content'] = '<p>Inserts a page break; which can created "paged" sections of the content.</p>';
														}
														else if($icon === 'preview') { 
															$title = 'Preview'; 
															$text = '<i class="mce-ico mce-i-preview"></i>'; 
															$tooltip['title'] = 'Preview'; 
															$tooltip['content'] = '<p>A quick preview of the editor content.</p>';
														}
														else if($icon === 'print') { 
															$title = 'Print'; 
															$text = '<i class="mce-ico mce-i-print"></i>'; 
															$tooltip['title'] = 'Print'; 
															$tooltip['content'] = '<p>Print the editor content directly to a printer.</p>';
														}
														else if($icon === 'searchreplace') { 
															$title = 'Search and Replace'; 
															$text = '<i class="mce-ico mce-i-searchreplace"></i>'; 
															$tooltip['title'] = 'Search and Replace'; 
															$tooltip['content'] = '<p>Search and/or replace the editor content with specific characters.</p>';
														}
														else if($icon === 'visualblocks') { 
															$title = 'Show Blocks'; 
															$text = '<i class="mce-ico mce-i-visualblocks"></i>'; 
															$tooltip['title'] = 'Show Blocks'; 
															$tooltip['content'] = '<p>Shows all block level editor elements with a light border.</p>';
														}
														else if($icon === 'subscript') { 
															$title = 'Subscript'; 
															$text = '<i class="mce-ico mce-i-subscript"></i>'; 
															$tooltip['title'] = 'Subscript'; 
															$tooltip['content'] = '<p>Adds a <sub>subscript</sub> to selected editor content (mainly used with text).</p>';
														}
														else if($icon === 'superscript') { 
															$title = 'Superscript'; 
															$text = '<i class="mce-ico mce-i-superscript"></i>';
															$tooltip['title'] = 'Superscript'; 
															$tooltip['content'] = '<p>Adds a <sup>superscript</sup> to selected editor content (mainly used with text).</p>';
														}
														else if($icon === 'image_orig') { 
															$class = 'dashicons dashicons-format-image'; 
															$title = 'Image'; 
															$tooltip['title'] = 'Image'; 
															$tooltip['content'] = '<p>Insert images (by link).</p>';
														}
														else if($icon === 'p_tags_button') { 
															$title = 'Paragraph Tag'; 
															$style='background-image:url('.WPEDIT_PLUGIN_URL.'plugins/ptags/p_tag.png);width:20px;height:20px;'; 
															$tooltip['title'] = 'Paragraph Tag'; 
															$tooltip['content'] = '<p>Insert paragraph tags (along with attributes); which will not be removed from the editor.</p>';
														}
														else if($icon === 'line_break_button') { 
															$title = 'Line Break'; 
															$style='background-image:url('.WPEDIT_PLUGIN_URL.'plugins/linebreak/line_break.png);width:20px;height:20px;'; 
															$tooltip['title'] = 'Line Break'; 
															$tooltip['content'] = '<p>Insert line breaks; which will not be removed from the editor.</p><p>This is done by adding a class of "none" to the tag.</p>';
														}
														else if($icon === 'mailto') { 
															$title = 'MailTo Link'; 
															$style='background-image:url('.WPEDIT_PLUGIN_URL.'plugins/mailto/mailto.gif);width:20px;height:20px;'; 
															$tooltip['title'] = 'MailTo Link'; 
															$tooltip['content'] = '<p>Turns an email address into an active mail link.</p><p>When clicked, it will open the users default mail client to send a message.</p>';
														}
														else if($icon === 'loremipsum') { 
															$title = 'Lorem Ipsum'; 
															$style='background-image:url('.WPEDIT_PLUGIN_URL.'plugins/loremipsum/loremipsum.png);width:20px;height:20px;'; 
															$tooltip['title'] = 'Lorem Ipsum'; 
															$tooltip['content'] = '<p>Esaily insert placeholder text into the editor.</p><p>Select from multiple languages; and choose the number of elements to add.</p>';
														}
														else if($icon === 'shortcodes') { 
															$title = 'Shortcodes'; 
															$style='background-image:url('.WPEDIT_PLUGIN_URL.'plugins/shortcodes/shortcodes.gif);width:20px;height:20px;'; 
															$tooltip['title'] = 'Shortcodes'; 
															$tooltip['content'] = '<p>Gathers all available shortcodes and adds them to a dropdown list; for easy editor insertion.</p><p>Note: The shortcodes gathered here do not include any shortcode attributes.</p><p>If shortcode attributes are necessary, they will need to be entered into the shortcode manually.</p>';
														}
														else if($icon === 'youTube') { 
															$title = 'YouTube Video'; 
															$style='background-image:url('.WPEDIT_PLUGIN_URL.'plugins/youTube/images/youtube.png);width:20px;height:20px;'; 
															$tooltip['title'] = 'YouTube Video'; 
															$tooltip['content'] = '<p>Browse and insert YouTube videos without ever leaving the editor.</p><p>A custom interface allows browsing YouTube videos directly from the editor.</p>';
														}
														else if($icon === 'clker') { 
															$title = 'Clker Images'; 
															$style='background-image:url('.WPEDIT_PLUGIN_URL.'plugins/clker/img/clker.png);width:20px;height:20px;'; 
															$tooltip['title'] = 'Clker Images'; 
															$tooltip['content'] = '<p>Browse and insert images from the Clker.com website.</p>';
														}
														else if($icon === 'cleardiv') { 
															$title = 'Clear Div'; 
															$style='background-image:url('.WPEDIT_PLUGIN_URL.'plugins/cleardiv/images/cleardiv.png);width:20px;height:20px;';  
															$tooltip['title'] = 'Clear Div'; 
															$tooltip['content'] = '<p>Clear editor divs. Selections include "left", "right" and "both".</p>';
														}
														else if($icon === 'codemagic') { 
															$title = 'Code Magic'; 
															$style='background-image:url('.WPEDIT_PLUGIN_URL.'plugins/codemagic/images/codemagic.png);width:20px;height:20px;'; 
															$tooltip['title'] = 'Code Magic'; 
															$tooltip['content'] = '<p>An advanced html code editor; view and edit the html code from an overlay window.</p><p>Includes syntax highlighting; search and replace; and proper element spacing.</p><p>This is a great option when editing html code is necessary; but swtiching editor views is undesirable.</p>';
														}
														else if($icon === 'acheck') { 
															$title = 'Accessibility Checker'; 
															$style='background-image:url('.WPEDIT_PLUGIN_URL.'plugins/acheck/img//acheck.png);width:20px;height:20px;';
															$tooltip['title'] = 'Accessibility Checker'; 
															$tooltip['content'] = '<p>Checks the editor content for accessibility by other devices.</p>';
														}
														else if($icon === 'advlink') { 
															$title = 'Insert/Edit Advanced Link'; 
															$style='background-image:url('.WPEDIT_PLUGIN_URL.'plugins/advlink/images/advlink.png);width:20px;height:20px;';
															$tooltip['title'] = 'Insert/Edit Advanced Link'; 
															$tooltip['content'] = '<p>Insert and edit links; along with various atttributes.</p><p>Populates with all posts and pages; so linking to current content is a one-click process.</p><p>Also includes javascript attributes (onclick, onmouseover, etc.); which can be used for executing javascript functions.</p>';
														}
														else if($icon === 'advhr') { 
															$title = 'Advanced Horizontal Line'; 
															$style='background-image:url('.WPEDIT_PLUGIN_URL.'plugins/advhr/images/advhr.png);width:20px;height:20px;'; 
															$tooltip['title'] = 'Advanced Horizontal Line'; 
															$tooltip['content'] = '<p>Modify various options of the horizontal line; like shadow and width.</p>';
														}
														else if($icon === 'advimage') { 
															$title = 'Advanced Insert/Edit Image'; 
															$style='background-image:url('.WPEDIT_PLUGIN_URL.'plugins/advimage/images//advimage.png);width:20px;height:20px;'; 
															$tooltip['title'] = 'Advanced Insert/Edit Image'; 
															$tooltip['content'] = '<p>Insert/Edit images with more control.</p><p>Define image attributes, image margin, image padding and image border.</p><p>Also includes javascript attributes (onclick, onmouseover, etc.); which can be used for executing javascript functions.</p>';
														}
														else if($icon === 'formatPainter') { 
															$class = 'dashicons dashicons-admin-appearance';
															$title = 'Format Painter'; 
															$tooltip['title'] = 'Format Painter'; 
															$tooltip['content'] = '<p>Copies styling from one element; and applies the same styling to another element.</p>';
														}
														else if($icon === 'googleImages') { 
															$title = 'Google Images'; 
															$style='background-image:url('.WPEDIT_PLUGIN_URL.'plugins/googleImages/images/googleImages.png);width:20px;height:20px;'; 
															$tooltip['title'] = 'Google Images'; 
															$tooltip['content'] = '<p>Browse and insert Google images without ever leaving the content editor.</p>';
														}
														else if($icon === 'abbr') { 
															$title = 'Abbreviation';
															$style='background-image:url('.WPEDIT_PLUGIN_URL.'plugins/abbr/abbr.png);width:20px;height:20px;'; 
															$tooltip['title'] = 'Abbreviation'; 
															$tooltip['content'] = '<p>Add an abbreviation to selected editor content.</p>';
														}
														else if($icon === 'imgmap') {
															$title = 'Image Map'; 
															$style='background-image:url('.WPEDIT_PLUGIN_URL.'plugins/imgmap/images/imgmap.png);width:20px;height:20px;';  
															$tooltip['title'] = 'Image Map'; 
															$tooltip['content'] = '<p>Create an image map from an image.</p><p>Allows multiple "hot spots" on a single image.  Each "hot spot" can link to a different url.</p>';
														}
														else if($icon === 'columnShortcodes') { 
															$class = 'dashicons dashicons-schedule';
															$title = 'Column Shortcodes';
															$tooltip['title'] = 'Column Shortcodes'; 
															$tooltip['content'] = '<p>A tool for easily inserting column shortcode templates.</p>';
														}
														else if($icon === 'nonbreaking') { 
															$title = 'Nonbreaking Space';
															$style='background-image:url('.WPEDIT_PLUGIN_URL.'plugins/nonbreaking/nonbreaking.png);width:20px;height:20px;'; 
															$tooltip['title'] = 'Nonbreaking Space'; 
															$tooltip['content'] = '<p>Insert a nonbreaking space; which will not be removed from the editor.</p>';
														}
														else if($icon === 'eqneditor') { 
															$title = 'CodeCogs Equation Editor';
															$style='background-image:url('.WPEDIT_PLUGIN_URL.'plugins/eqneditor/img/eqneditor.png);width:20px;height:20px;'; 
															$tooltip['title'] = 'CodeCogs Equation Editor'; 
															$tooltip['content'] = '<p>Create complex math equations from a simple interface.</p>';
														}
														else {
						
															$get_filters = $this->filtered_buttons;
															
															// If the array set is not empty (filters being applied)
															if(  ! empty( $get_filters ) ) {
																
																$check_filter = array();
																foreach( $get_filters as $key => $values ) {
																	
																	$check_filter[$values['button_id']] = $values;
																}
																
																// If this button is in filtered array
																if( array_key_exists( $icon, $check_filter ) ) {
																	
																	$array_key = $check_filter[$icon];
																	
																	$title = isset( $array_key['tooltip_title'] ) && $array_key['tooltip_title'] !== '' ? $array_key['tooltip_title'] : '';
																	$class = isset( $array_key['dashicon'] ) && $array_key['dashicon'] !== '' ? $array_key['dashicon'] : '';
																	$text = isset( $array_key['button_text'] ) && $array_key['button_text'] !== '' ? $array_key['button_text'] : '';
																	$style = isset( $array_key['custom_icon'] ) && $array_key['custom_icon'] !== '' ? 'background-image:url(' . $array_key['custom_icon'] . ');width:20px;height:20px;' : '';
																	$tooltip['title'] = isset( $array_key['tooltip_title'] ) && $array_key['tooltip_title'] !== '' ? $array_key['tooltip_title'] : '';
																	$tooltip['content'] = isset( $array_key['tooltip_content'] ) && $array_key['tooltip_content'] !== '' ? $array_key['tooltip_content'] : '';
																}
															}
														}
														
														// Process tooltips
														$tooltip_title = isset($tooltip['title']) ? $tooltip['title'] : 'Title not found';
														$tooltip_content = isset($tooltip['content']) ? $tooltip['content'] : '<p>Content not found. Please report to the plugin developer.</p>';
														
														// Are we displaying fancy tooltips?
														$tooltip_att = ($no_tooltips === false) ? 'data-tooltip="<h4 class=\'data_tooltip_title\'>'.htmlspecialchars($tooltip_title).'</h4><hr />'.htmlspecialchars($tooltip_content).'" ' : '';
														
														
														// ARRAY CHECKING BEFORE DISPLAYING BUTTON FROM DATABASE
														// This will keep saved filtered buttons from displaying (and removes when user saves); if their parent was deactivated
					
														// Create array of default buttons (and filter buttons)
														$plugin_buttons = $this->global_options_buttons;
														$check_array = '';
														
														foreach( $plugin_buttons as $button ) {
															if( !empty( $button ) && $button != '' ) {  // Skip containter array if empty
															
																$check_array .= $button . ' ';  // Create single string of all default plugin buttons
															}
														}
														
														$get_filters = $this->filtered_buttons;
														
														// If the array set is not empty (filters being applied)
														if(  ! empty( $get_filters ) ) {
															foreach( $get_filters as $key => $values ) {
																
																$check_array .= $values['button_id'] . ' ';
															}
														}
														
														$trim_check_array = rtrim( $check_array, ' ' );
														$explode_check_array = explode( ' ', $trim_check_array );
														
														
														// If button is in active array; display div
														if( in_array( $icon, $explode_check_array ) ) {
						
															// Display button
															echo '<div '.$tooltip_att.' id="'.$icon.'" class="ui-state-default draggable '.$class.'" title="'.$title.'"><span style="'.esc_attr($style).'">'.$text.'</span></div>';
														}
													}
												}
												
												
												/**************************************/
												// Button filter for plugins/themes
												/**************************************/
												$filter_flag = false;
												
												// Create array of saved buttons
												if( $icons_filter !== '' ) {
													
													$trim_filter = trim( $icons_filter );
													$icons_filter_array = explode( ' ', $trim_filter );
												}
												
												$get_filters = $this->filtered_buttons;
												
												// If the array set is not empty (filters being applied)
												if(  ! empty( $get_filters ) ) {
													foreach( $get_filters as $key => $values ) {
														
														if( ! in_array( $values['button_id'], $icons_filter_array ) ) {
															
															$title = isset( $values['tooltip_title'] ) && $values['tooltip_title'] !== '' ? $values['tooltip_title'] : '';
															$content = isset( $values['tooltip_content'] ) && $values['tooltip_content'] !== '' ? $values['tooltip_content'] : '';
															$class = isset( $values['dashicon'] ) && $values['dashicon'] !== '' ? $values['dashicon'] : '';
															$text = isset( $values['button_text'] ) && $values['button_text'] !== '' ? $values['button_text'] : '';
															$style = isset( $values['custom_icon'] ) && $values['custom_icon'] !== '' ? 'background-image:url(' . $values['custom_icon'] . ');width:20px;height:20px;' : '';
															$span = $style !== '' ? '<span style="' . $style . '">' . $text . '</span>' : '<span>' . $text . '</span>';
															$row = isset( $values['editor_row'] ) && $values['editor_row'] !== '' ? $values['editor_row'] : 'tmce_container';
															
															/// Filter buttons by row
															if( $toolbar === $row ) {
																
																echo '<div 
																			data-tooltip="<h4 class=\'data_tooltip_title\'>'.htmlspecialchars( $title ) . '</h4>
																			<hr /><p>'.htmlspecialchars( $content ).'</p>" 
																			id="' . $values['button_id'] . '" 
																			class="ui-state-default draggable new_button ' . $class . '" 
																			title="' . $title . '">' .  $span . 
																	'</div>'
																;
															}
															
															$filter_flag = true;
														}
													}
												}
											echo '</div>';  // End foreach .sortable
										}  // End not rows 3 and 4
									}
										
									if( $filter_flag === true ) {
		
										echo '<div class="error">';
											echo '<h4>';
												_e('New buttons have been added via other plugins (or theme).', 'wp_edit_pro');
												echo '<br />';
												_e('Move them to a new location (if desired) and click "Save Buttons".', 'wp_edit_pro');
											echo '</h4>';
										echo '</div>';
									}
									?>
								</div>  <!-- End #inside_button_hover -->
							</div>  <!-- End .postbox -->
						</div>  <!-- End .metabox -->
					</div>  <!-- End .main_buttons_container_float -->
					<?php
					
					// Build input for passing button arrangements
					echo '<form method="post" action="">';
					
						echo '<input type="hidden" class="get_sorted_array" name="get_sorted_array_results" value="" />';
							
						// Submit save buttons
						echo '<input type="submit" value="'.__('Save Buttons', 'wp-edit').'" name="wpep_save_buttons" class="button-primary" />';
						
						// Submit reset buttons
						echo '<span style="margin-left:10px;"></span>';
						echo '<input type="button" value="'.__('Reset Buttons', 'wp-edit').'" class="button-primary reset_dd_buttons" />';
						echo '<input type="submit" name="wpep_reset_buttons" class="button-primary wpep_reset_buttons" style="display:none;" />';
						
						// Create nonce
						wp_nonce_field( 'wpe_save_buttons_opts' );
						
					echo '</form>';
				echo '</div>';
				
				echo '<div class="main_container">';
				
					echo '<h3>';
						_e('Buttons Tips', 'wp-edit');
					echo '</h3>';
					?>
					
					<div class="metabox-holder"> 
						<div class="postbox">
							<div class="inside">
							
								<div id="button_help_tabs">
								
									<ul>
									<li><a href="#dragdrop"><?php _e('Drag/Drop', 'wp-edit'); ?></a></li>
									<li><a href="#multiselect"><?php _e('Multi Select', 'wp-edit'); ?></a></li>
									<li><a href="#reset"><?php _e('Reset', 'wp-edit'); ?></a></li>
									<li><a href="#custom_api"><?php _e('Custom Buttons API', 'wp-edit'); ?></a></li>
									</ul>
									
									<div id="dragdrop">
										<p>
											<?php _e('Buttons can be dragged and dropped into desired button rows.', 'wp-edit'); ?><br />
											<?php _e('The "Button Container" is a placeholder for buttons not used in the editor; these buttons will not appear when editing a post or page.', 'wp-edit'); ?>
                                        </p>
									</div>
									<div id="multiselect">
										<p>
											<?php _e('Buttons may also be selected in quantities; or multiple selections, before being moved.', 'wp-edit'); ?>
                                        </p>
										<p>
											<?php _e('Clicking a button will set it as "active"; a yellowish highlight color. Multiple buttons can be clicked and set as "active".', 'wp-edit'); ?><br />
                                            <?php _e('Clicking and dragging one of the "active" buttons will move the entire "active" selection.', 'wp-edit'); ?><br />
                                            <?php _e('Clicking outside the button area will remove all currently active button selections.', 'wp-edit'); ?>
                                        </p>
									</div>
									<div id="reset">
										<p>
											<?php _e('Clicking "Reset Buttons" will restore the editor buttons to their original default values.', 'wp-edit'); ?><br />
                                            <?php _e('All button rows will get the default WordPress button arrangements; and the extra buttons will be added to the "Button Container".', 'wp-edit'); ?>
                                        </p>
									</div>
									<div id="custom_api">
										<p>
											<?php _e('WP Edit now uses a Custom Buttons API which allows other plugin/theme developers to add their editor buttons into the system.', 'wp-edit'); ?><br />
                                            <?php printf( __('Please direct all your favorite plugin/theme developers to the <a target="_blank" href="%s">Custom Buttons API</a> documentation.', 'wp-edit'), 'http://learn.wpeditpro.com/custom-buttons-api/'); ?>
                                        </p>
									</div>
								</div>
							</div>
						</div>
					</div>
				<?php
				echo '</div>';
			}
			/*
			****************************************************************
			Global Tab
			****************************************************************
			*/
            else if($active_tab == 'global') {
				
				echo '<div class="main_container">';
				
					?>
                    <h3><?php _e('Global Options', 'wp-edit'); ?></h3>
                    <form method="post" action="">
                    <div class="metabox-holder"> 
						<div class="postbox">
							<div class="inside">
                            
                                <?php
                                $options_global = get_option('wp_edit_global');
                                $jquery_theme = isset($options_global['jquery_theme']) ? $options_global['jquery_theme'] : 'smoothness';
                                $disable_admin_links = isset($options_global['disable_admin_links']) && $options_global['disable_admin_links'] === '1' ? 'checked="checked"' : '';
                                $disable_fancy_tooltips = isset($options_global['disable_fancy_tooltips']) && $options_global['disable_fancy_tooltips'] === '1' ? 'checked="checked"' : '';
                                ?>
                                
                                <table cellpadding="10">
                                <tbody>
                                <tr><td><?php _e('jQuery Theme', 'wp-edit'); ?></td>
                                    <td>
                                    
                                    <select id="jquery_theme" name="jquery_theme"/>
                                    <?php
                                    $jquery_themes = array('base','black-tie','blitzer','cupertino','dark-hive','dot-luv','eggplant','excite-bike','flick','hot-sneaks','humanity','le-frog','mint-choc','overcast','pepper-grinder','redmond','smoothness','south-street','start','sunny','swanky-purse','trontastic','ui-darkness','ui-lightness','vader');
                                                                    
                                    foreach($jquery_themes as $jquery_theme) {
                                        $selected = ($options_global['jquery_theme']==$jquery_theme) ? 'selected="selected"' : '';
                                        echo "<option value='$jquery_theme' $selected>$jquery_theme</option>";
                                    }
                                    ?>
                                    </select>
                                    <label for="jquery_theme"> <?php _e('Selects the jQuery theme for plugin alerts and notices.', 'wp-edit'); ?></label>
                                    </td>
                                </tr>
                                <tr><td><?php _e('Disable Admin Links', 'wp-edit'); ?></td>
                                    <td>
                                    <input id="disable_admin_links" type="checkbox" value="1" name="disable_admin_links" <?php echo $disable_admin_links; ?> />
                                    <label for="disable_admin_links"><?php _e('Disables the WP Edit top admin bar links.', 'wp-edit'); ?></label>
                                    </td>
                                </tr>
                                <tr><td><?php _e('Disable Fancy Tooltips', 'wp-edit'); ?></td>
                                    <td>
                                    <input id="disable_fancy_tooltips" type="checkbox" value="1" name="disable_fancy_tooltips" <?php echo $disable_fancy_tooltips; ?> />
                                    <label for="disable_fancy_tooltips"><?php _e('Disables the fancy tooltips used on button hover.', 'wp-edit'); ?></label>
                                    </td>
                                </tr>
                                </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
					<input type="submit" value="<?php _e('Save Global Options', 'wp-edit'); ?>" class="button button-primary" id="submit_global" name="submit_global">
                    <?php wp_nonce_field( 'wpe_save_global_opts' ); ?>
					</form>
					<?php
				echo '</div>';
            } 
			/*
			****************************************************************
			General Tab
			****************************************************************
			*/
            else if($active_tab == 'general'){
				
				// Get all cpt's (_builtin will exclude default post types)
				$post_types = get_post_types( array( 'public' => true, '_builtin' => false ), 'names' );
				
				echo '<div class="main_container">';
				
					?>
                    <h3><?php _e('General Options', 'wp-edit'); ?></h3>
                    <form method="post" action="">
                    <div class="metabox-holder"> 
						<div class="postbox">
							<div class="inside">
                                
                                <?php
                                $options_general = get_option('wp_edit_general');
                                $linebreak_shortcode = isset($options_general['linebreak_shortcode']) && $options_general['linebreak_shortcode'] === '1' ? 'checked="checked"' : '';
                                $shortcodes_in_widgets = isset($options_general['shortcodes_in_widgets']) && $options_general['shortcodes_in_widgets'] === '1' ? 'checked="checked"' : '';
                                $shortcodes_in_excerpts = isset($options_general['shortcodes_in_excerpts']) && $options_general['shortcodes_in_excerpts'] === '1' ? 'checked="checked"' : '';
                                $post_excerpt_editor = isset($options_general['post_excerpt_editor']) && $options_general['post_excerpt_editor'] === '1' ? 'checked="checked"' : '';
                                $page_excerpt_editor = isset($options_general['page_excerpt_editor']) && $options_general['page_excerpt_editor'] === '1' ? 'checked="checked"' : '';
                                $profile_editor = isset($options_general['profile_editor']) && $options_general['profile_editor'] === '1' ? 'checked="checked"' : '';
								$cpt_excerpts = isset($options_general['cpt_excerpt_editor']) ? $options_general['cpt_excerpt_editor'] : array();
                                ?>
                                
                                <table cellpadding="8">
                                <tbody>
                                <tr><td><?php _e('Linebreak Shortcode', 'wp-edit'); ?></td>
                                    <td>
                                    <input id="linebreak_shortcode" type="checkbox" value="1" name="linebreak_shortcode" <?php echo $linebreak_shortcode; ?> />
                                    <label for="linebreak_shortcode"><?php _e('Use the [break] shortcode to insert linebreaks in the editor.', 'wp-edit'); ?></label>
                                    </td>
                                </tr>
                                <tr><td><?php _e('Shortcodes in Widgets', 'wp-edit'); ?></td>
                                    <td>
                                    <input id="shortcodes_in_widgets" type="checkbox" value="1" name="shortcodes_in_widgets" <?php echo $shortcodes_in_widgets; ?> />
                                    <label for="shortcodes_in_widgets"><?php _e('Use shortcodes in widget areas.', 'wp-edit'); ?></label>
                                    </td>
                                </tr>
                                <tr><td><?php _e('Shortcodes in Excerpts', 'wp-edit'); ?></td>
                                    <td>
                                    <input id="shortcodes_in_excerpts" type="checkbox" value="1" name="shortcodes_in_excerpts" <?php echo $shortcodes_in_excerpts; ?> />
                                    <label for="shortcodes_in_excerpts"><?php _e('Use shortcodes in excerpt areas.', 'wp-edit'); ?></label>
                                    </td>
                                </tr>
                                <tr><td><?php _e('Profile Editor', 'wp-edit'); ?></td>
                                    <td class="jwl_user_cell">
                                        <input id="profile_editor" type="checkbox" value="1" name="profile_editor" <?php echo $profile_editor; ?> />
                                        <label for="profile_editor"><?php _e('Use modified editor in profile biography field.', 'wp-edit'); ?></label>
                                    </td>
                                </tr>
                                </tbody>
                                </table>
                            </div>
                        </div>
                        
						<div class="postbox">
							<div class="inside">
                                
                                <table cellpadding="8">
                                <tbody>
                                <tr><td><?php _e('WP Edit Post Excerpt', 'wp-edit'); ?></td>
                                    <td>
                                    <input id="post_excerpt_editor" type="checkbox" value="1" name="post_excerpt_editor" <?php echo $post_excerpt_editor; ?> />
                                    <label for="post_excerpt_editor"><?php _e('Add the WP Edit editor to the Post Excerpt area.', 'wp-edit'); ?></label>
                                    </td>
                                </tr>
                                <tr><td><?php _e('WP Edit Page Excerpt', 'wp-edit'); ?></td>
                                    <td>
                                    <input id="page_excerpt_editor" type="checkbox" value="1" name="page_excerpt_editor" <?php echo $page_excerpt_editor; ?> />
                                    <label for="page_excerpt_editor"><?php _e('Add the WP Edit editor to the Page Excerpt area.', 'wp-edit'); ?></label>
                                    </td>
                                </tr>
                                </tbody>
                                </table>
                                
                                <h3><?php _e('Custom Post Type Excerpts', 'wp-edit'); ?></h3>
                                <table cellpadding="3" style="margin-left:7px;">
                                <tbody>
                                <?php
                                if( !empty( $post_types) ) {
                                    foreach ( $post_types as $post_type ) {
                                        
                                        $selected = in_array($post_type, $cpt_excerpts) ? 'checked="checked"' : ''; 
                                        echo '<tr><td><input type="checkbox" name="cpt_excerpt_editor['.$post_type.']" '.$selected.'> '.$post_type.'</td></tr>';
                                    }
                                }
                                else {
                                    
                                    echo '<tr><td>';
                                    _e('No registered custom post types were found.', 'wp-edit');
                                    echo '</td></tr>';
                                }
                                ?>
                                </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                            
					<input type="submit" value="<?php _e('Save General Options', 'wp-edit'); ?>" class="button button-primary" id="submit_general" name="submit_general">
                    <?php wp_nonce_field( 'wpe_save_general_opts' ); ?>
					</form>
					<?php
				echo '</div>';
            } 
			/*
			****************************************************************
			Posts/Pages Tab
			****************************************************************
			*/
            else if($active_tab == 'posts'){
				
				$options_posts = get_option('wp_edit_posts');
				
				$post_title_field = isset($options_posts['post_title_field']) ? $options_posts['post_title_field'] : 'Enter title here';
				$column_shortcodes = isset($options_posts['column_shortcodes']) && $options_posts['column_shortcodes'] === '1' ? 'checked="checked"' : '';
				$disable_wpautop = isset($options_posts['disable_wpautop']) && $options_posts['disable_wpautop'] === '1' ? 'checked="checked"' : '';
				
				$max_post_revisions = isset($options_posts['max_post_revisions']) ? $options_posts['max_post_revisions'] : '';
				$max_page_revisions = isset($options_posts['max_page_revisions']) ? $options_posts['max_page_revisions'] : '';
				
				$hide_admin_posts = isset($options_posts['hide_admin_posts']) ? $options_posts['hide_admin_posts'] : '';
				$hide_admin_pages = isset($options_posts['hide_admin_pages']) ? $options_posts['hide_admin_pages'] : '';
					
				echo '<div class="main_container">';
				
					?>
                    <h3><?php _e('Posts/pages Options', 'wp-edit'); ?></h3>
                    <form method="post" action="">
                    <div class="metabox-holder"> 
						<div class="postbox">
							<div class="inside">
                                <table cellpadding="8">
                                <tbody>
                                <tr><td><?php _e('Post/Page Default Title', 'wp-edit'); ?></td>
                                    <td>
                                    <input type="text" name="post_title_field" value="<?php echo $post_title_field ?>" />
                                    <label for="post_title_field"><?php _e('Change the default "add new" post/page title field.', 'wp-edit'); ?></label>
                                    </td>
                                </tr>
                                <tr><td><?php _e('Column Shortcodes', 'wp-edit'); ?></td>
                                    <td>
                                    <input id="column_shortcodes" type="checkbox" value="1" name="column_shortcodes" <?php echo $column_shortcodes; ?> />
                                    <label for="column_shortcodes"><?php _e('Enable the column shortcodes functionality.', 'wp-edit'); ?></label>
                                    </td>
                                </tr>
                                <tr><td><?php _e('Disable wpautop()', 'wp-edit'); ?></td>
                                    <td>
                                    <input id="disable_wpautop" type="checkbox" value="1" name="disable_wpautop" <?php echo $disable_wpautop; ?> />
                                    <label for="disable_wpautop"><?php _e('Disable the filter responsible for removing p and br tags.', 'wp-edit'); ?></label>
                                    </td>
                                </tr>
                                </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
					
                    <h3><?php _e('Page Revisions', 'wp-edit'); ?></h3>
                    <div class="metabox-holder"> 
						<div class="postbox">
							<div class="inside">
                                <table cellpadding="8">
                                <tbody>
                                <tr><td><?php _e('Max Post Revisions', 'wp-edit'); ?></td>
                                    <td>
                                    <input type="text" name="max_post_revisions" value="<?php echo $max_post_revisions ?>" />
                                    <label for="max_post_revisions"><?php _e('Set max number of Post Revisions to store in database. (empty = unlimited)', 'wp-edit'); ?></label>
                                    </td>
                                </tr>
                                <tr><td><?php _e('Max Page Revisions', 'wp-edit'); ?></td>
                                    <td>
                                    <input type="text" name="max_page_revisions" value="<?php echo $max_page_revisions ?>" />
                                    <label for="max_page_revisions"><?php _e('Set max number of Page Revisions to store in database. (empty = unlimited)', 'wp-edit'); ?></label>
                                    </td>
                                </tr>
                                <tr><td><?php _e('Delete Revisions', 'wp-edit'); ?></td>
                                    <td>
                                    <input id="delete_revisions" type="checkbox" value="1" name="delete_revisions" />
                                    <label for="delete_revisions"><?php _e('Delete all database revisions.', 'wp-edit'); ?></label>
                                    </td>
                                </tr>
                                <tr><td><?php _e('Revisions DB Size', 'wp-edit'); ?></td>
                                    <td>
                                        <?php
                                        global $wpdb;
                                        $query = $wpdb->get_results( "SELECT * FROM $wpdb->posts WHERE post_type = 'revision'", ARRAY_A );
                                        $lengths = 0;
                                        foreach ($query as $row) {
                                            $lengths += strlen($row['post_content']);
                                        }
                                        _e('Current size of revisions stored in database:', 'wp-edit');
                                        echo ' <strong>'.number_format($lengths/(1024*1024),3).' mb</strong>';
                                        ?>
                                    </td>
                                </tr>
                                </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
					
                    <h3><?php _e('Hide Posts and Pages', 'wp-edit'); ?></h3>
                    <div class="metabox-holder"> 
						<div class="postbox">
							<div class="inside">
                                <table cellpadding="8">
                                <tbody>
                                <tr><td><?php _e('Hide Admin Posts', 'wp-edit'); ?></td>
                                    <td>
                                    <input type="text" name="hide_admin_posts" value="<?php echo $hide_admin_posts ?>" />
                                    <label for="hide_admin_posts"><?php _e('Hide selected posts from admin view. ID comma separated (1,5,14,256)', 'wp-edit'); ?></label>
                                    </td>
                                </tr>
                                <tr><td><?php _e('Hide Admin Pages', 'wp-edit'); ?></td>
                                    <td>
                                    <input type="text" name="hide_admin_pages" value="<?php echo $hide_admin_pages ?>" />
                                    <label for="hide_admin_pages"><?php _e('Hide selected pages from admin view. ID comma separated (1,5,14,256)', 'wp-edit'); ?></label>
                                    </td>
                                </tr>
                                </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <input type="submit" value="<?php _e('Save Posts/Pages Options', 'wp-edit'); ?>" class="button button-primary" id="submit_posts" name="submit_posts">
                    <?php wp_nonce_field( 'wpe_save_posts_pages_opts' ); ?>
					</form>
					<?php
				echo '</div>';
            }
			/*
			****************************************************************
			Editor Tab
			****************************************************************
			*/
            else if($active_tab == 'editor'){
				
                ?>
                <form method="post" action="">
                <div class="main_container">
                
                	<h3><?php _e('Styles Options', 'wp-edit'); ?></h3>
                    <div class="metabox-holder"> 
                        <div class="postbox">
                            <div class="inside">
                                <p style="margin-left:10px;"><?php _e('Adds predefined styles; which can be applied to editor content.', 'wp-edit'); ?><br />
                                <?php _e('Please be sure the "Formats" button is active in the editor.', 'wp-edit'); ?></p>
                                
                                <?php
                                $options_editor = get_option('wp_edit_editor');
                                $editor_add_pre_styles = isset($options_editor['editor_add_pre_styles']) && $options_editor['editor_add_pre_styles'] === '1' ? 'checked="checked"' : '';
                                $default_editor_fontsize_type = isset($options_editor['default_editor_fontsize_type']) ? $options_editor['default_editor_fontsize_type'] : 'pt';
								$default_editor_fontsize_values = isset($options_editor['default_editor_fontsize_values']) ? $options_editor['default_editor_fontsize_values'] : '';
                                $bbpress_editor = isset($options_editor['bbpress_editor']) && $options_editor['bbpress_editor'] === '1' ? 'checked="checked"' : '';
                                ?>
                                
                                <table cellpadding="8">
                                <tbody>
                                <tr><td><?php _e('Add Pre-defined Styles', 'wp-edit'); ?></td>
                                    <td>
                                    <input id="editor_add_pre_styles" type="checkbox" value="1" name="editor_add_pre_styles" <?php echo $editor_add_pre_styles; ?> />
                                    <label for="editor_add_pre_styles"><?php _e('Adds predefined styles to the "Formats" dropdown button.', 'wp-edit'); ?></label>
                                    </td>
                                </tr>
                                </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <h3><?php _e('TinyMCE Options', 'wp-edit'); ?></h3>
                    <div class="metabox-holder"> 
                        <div class="postbox">
                            <div class="inside">
                                <p style="margin-left:10px;"><?php _e('These options will adjust various parts of the TinyMCE initialization process.', 'wp-edit'); ?></p>
                                
                                <table cellpadding="8">
                                <tbody>
                                <tr><td><?php _e('Dropdown Editor Font-Size Type', 'wp-edit'); ?></td>
                                    <td>
                                    <input type="radio" name="default_editor_fontsize_type" value="px" <?php if($default_editor_fontsize_type === 'px') echo 'checked="checked"'; ?> /> <?php _e('px', 'wp-edit'); ?><span style="margin-left:10px;"></span>
                                    <input type="radio" name="default_editor_fontsize_type" value="pt" <?php if($default_editor_fontsize_type === 'pt') echo 'checked="checked"'; ?> /> <?php _e('pt', 'wp-edit'); ?><span style="margin-left:10px;"></span>
                                    <input type="radio" name="default_editor_fontsize_type" value="em" <?php if($default_editor_fontsize_type === 'em') echo 'checked="checked"'; ?> /> <?php _e('em', 'wp-edit'); ?><span style="margin-left:10px;"></span>
                                    <input type="radio" name="default_editor_fontsize_type" value="percent" <?php if($default_editor_fontsize_type === 'percent') echo 'checked="checked"'; ?> /> <?php _e('%', 'wp-edit'); ?><br />
                                    	
                                    <?php _e('Select the editor font size type displayed in the "Font Size" button dropdown menu.', 'wp-edit'); ?>
                                    </td>
                                </tr>
                                <tr><td style="vertical-align:top;"><?php _e('Dropdown Editor Font-Size Type Values', 'wp-edit'); ?></td>
                                    <td>
                                    <input type="text" name="default_editor_fontsize_values" value="<?php echo $default_editor_fontsize_values; ?>" /><br />
                                    <?php _e('Define available font-size values for Font Size dropdown box.', 'wp-edit'); ?><br />
                                    <?php _e('Values should be space separated; and end with the chosen font size type (selected above).', 'wp-edit'); ?><br />
                                    <?php _e('For Example: If <strong>em</strong> is selected; possible values could be <strong>1em 1.1em 1.2em</strong> etc.', 'wp-edit'); ?>
                                    </td>
                                </tr>
                                </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <h3><?php _e('BBPress Options', 'wp-edit'); ?></h3>
                    <div class="metabox-holder"> 
                        <div class="postbox">
                            <div class="inside">
                            
                            	<p style="margin-left:10px;"><?php _e('Options for the editor used in the BBPress forums.', 'wp-edit'); ?></p>
                                
                                <table cellpadding="8">
                                <tbody>
                                <tr><td><?php _e('Enable Visual BBPRess Editor', 'wp-edit'); ?></td>
                                	<td>
                                    <input id="bbpress_editor" type="checkbox" value="1" name="bbpress_editor" <?php echo $bbpress_editor; ?> />
                                    <label for="bbpress_editor"><?php _e('Replaces default textarea with modified visual editor.', 'wp-edit'); ?></label>
                                    </td>
                                </tr>
                                </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <input type="submit" value="<?php _e('Save Editor Options', 'wp-edit'); ?>" class="button button-primary" id="submit_editor" name="submit_editor">
                    <?php wp_nonce_field( 'wpe_save_editor_opts' ); ?>
                </div>
                </form>
                <?php
            }
			/*
			****************************************************************
			Extras Tab
			****************************************************************
			*/
			else if($active_tab == 'extras')  {
				
				?>
                <form method="post" action="">
                <div class="main_container">
                
                    <h3><?php _e('Extra Options', 'wp-edit'); ?></h3>
                    
                    <div class="metabox-holder"> 
                        <div class="postbox">
                            <div class="inside">
                            
                                <h3><?php _e('Signoff Text', 'wp-edit'); ?></h3>
                                <p style="margin-left:10px;"><?php _e('Use the editor below to create a content chunk that can be inserted anywhere using the', 'wp-edit'); ?> <strong>[signoff]</strong> <?php _e('shortcode.', 'wp-edit'); ?></p>
                                
                                <table cellpadding="8" width="100%">
                                <tbody>
                                <tr><td>
                                    <?php
                                    $options_extras = get_option('wp_edit_extras');
                                    $content = isset($options_extras['signoff_text']) ? $options_extras['signoff_text'] : 'Please enter text here...';
                                    $editor_id = 'wp_edit_signoff';
                                    $args = array('textarea_rows' => 10, 'width' => '100px');
                                    wp_editor( $content, $editor_id, $args );
                                    ?>
                                </td></tr>
                                </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                	<input type="submit" value="Save Extras Options" class="button button-primary" id="submit_extras" name="submit_extras">
                    <?php wp_nonce_field( 'wpe_save_extras_opts' ); ?>
                </div>
                </form>
                <?php
			}
			/*
			****************************************************************
			User Specific Tab
			****************************************************************
			*/
            else if($active_tab == 'user_specific') {
                
				global $current_user;
				$options_user_meta = get_user_meta($current_user->ID, 'aaa_wp_edit_user_meta', true);
				
                $id_column = isset($options_user_meta['id_column']) && $options_user_meta['id_column'] === '1' ? 'checked="checked"' : '';
                $thumbnail_column = isset($options_user_meta['thumbnail_column']) && $options_user_meta['thumbnail_column'] === '1' ? 'checked="checked"' : '';
                $hide_text_tab = isset($options_user_meta['hide_text_tab']) && $options_user_meta['hide_text_tab'] === '1' ? 'checked="checked"' : '';
                $default_visual_tab = isset($options_user_meta['default_visual_tab']) && $options_user_meta['default_visual_tab'] === '1' ? 'checked="checked"' : '';
                $dashboard_widget = isset($options_user_meta['dashboard_widget']) && $options_user_meta['dashboard_widget'] === '1' ? 'checked="checked"' : '';
                
                $enable_highlights = isset($options_user_meta['enable_highlights']) && $options_user_meta['enable_highlights'] === '1' ? 'checked="checked"' : '';
                $draft_highlight = isset($options_user_meta['draft_highlight']) ? $options_user_meta['draft_highlight'] : '#FFFFFF';
                $pending_highlight = isset($options_user_meta['pending_highlight'])  ? $options_user_meta['pending_highlight'] : '#FFFFFF';
                $published_highlight = isset($options_user_meta['published_highlight'])  ? $options_user_meta['published_highlight'] : '#FFFFFF';
                $future_highlight = isset($options_user_meta['future_highlight'])  ? $options_user_meta['future_highlight'] : '#FFFFFF';
                $private_highlight = isset($options_user_meta['private_highlight'])  ? $options_user_meta['private_highlight'] : '#FFFFFF';
                ?>
                
                <form method="post" action="">
                <div class="main_container">
                
                    <h3><?php _e('User Specific Options', 'wp-edit'); ?></h3>
                    <div class="metabox-holder"> 
                        <div class="postbox">
                            <div class="inside">
                                
                                <p style="margin-left:10px;"><?php _e('These options are stored in individual user meta; meaning each user can set these options independently from one another.', 'wp-edit'); ?></p>
                                
                                <table cellpadding="8">
                                <tbody>
                                <tr><td><?php _e('ID Column', 'wp-edit'); ?></td>
                                    <td>
                                    <input id="id_column" type="checkbox" value="1" name="wp_edit_user_specific[id_column]" <?php echo $id_column; ?> />
                                    <label for="id_column"><?php _e('Adds a column to post/page list view for displaying the post/page ID.', 'wp-edit'); ?></label>
                                    </td>
                                </tr>
                                <tr><td><?php _e('Thumbnail Column', 'wp-edit'); ?></td>
                                    <td>
                                    <input id="thumbnail_column" type="checkbox" value="1" name="wp_edit_user_specific[thumbnail_column]" <?php echo $thumbnail_column; ?> />
                                    <label for="thumbnail_column"><?php _e('Adds a column to post/page list view for displaying thumbnails.', 'wp-edit'); ?></label>
                                    </td>
                                </tr>
                                <tr><td><?php _e('Hide TEXT Tab', 'wp-edit'); ?></td>
                                    <td>
                                    <input id="hide_text_tab" type="checkbox" value="1" name="wp_edit_user_specific[hide_text_tab]" <?php echo $hide_text_tab; ?> />
                                    <label for="hide_text_tab"><?php _e('Hide the editor TEXT tab from view.', 'wp-edit'); ?></label>
                                    </td>
                                </tr>
                                <tr><td><?php _e('Default VISUAL Tab', 'wp-edit'); ?></td>
                                    <td>
                                    <input id="default_visual_tab" type="checkbox" value="1" name="wp_edit_user_specific[default_visual_tab]" <?php echo $default_visual_tab; ?> />
                                    <label for="default_visual_tab"><?php _e('Always display VISUAL tab when editor loads.', 'wp-edit'); ?></label>
                                    </td>
                                </tr>
                                <tr><td><?php _e('Disable Dashboard Widget', 'wp-edit'); ?></td>
                                    <td>
                                    <input id="dashboard_widget" type="checkbox" value="1" name="wp_edit_user_specific[dashboard_widget]" <?php echo $dashboard_widget; ?> />
                                    <label for="dashboard_widget"><?php _e('Disables WP Edit Pro News Feed dashboard widget.', 'wp-edit'); ?></label>
                                    </td>
                                </tr>
                                </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <h3><?php _e('Post/Page Highlight Colors', 'wp-edit'); ?></h3>
                    <div class="metabox-holder"> 
                        <div class="postbox">
                            <div class="inside">
                            
                                <p style="margin-left:10px;"><?php _e('These options will allow each user to customize highlight colors for each post/page status.', 'wp-edit'); ?><br />
                                <?php _e('Meaning.. saved posts can be yellow, published posts can be blue, etc.', 'wp-edit'); ?></p>
                                
                                <table cellpadding="8">
                                <tbody>
                                <tr><td><?php _e('Enable Highlights', 'wp-edit'); ?></td>
                                    <td>
                                    <input id="enable_highlights" type="checkbox" value="1" name="wp_edit_user_specific[enable_highlights]" <?php echo $enable_highlights; ?> />
                                    <label for="enable_highlights"><?php _e('Enable the Highlight Options below.', 'wp-edit'); ?></label>
                                    </td>
                                </tr>
                                <tr><td><?php _e('Draft Highlight', 'wp-edit'); ?></td>
                                    <td class="jwl_user_cell">
                                    <input id="draft_highlight" type="text" name="wp_edit_user_specific[draft_highlight]" class="color_field" value="<?php echo $draft_highlight; ?>" />
                                    </td>
                                </tr>
                                <tr><td><?php _e('Pending Highlight', 'wp-edit'); ?></td>
                                    <td class="jwl_user_cell">
                                    <input id="pending_highlight" type="text" name="wp_edit_user_specific[pending_highlight]" class="color_field" value="<?php echo $pending_highlight; ?>" />
                                    </td>
                                </tr>
                                <tr><td><?php _e('Published Highlight', 'wp-edit'); ?></td>
                                    <td class="jwl_user_cell">
                                    <input id="published_highlight" type="text" name="wp_edit_user_specific[published_highlight]" class="color_field" value="<?php echo $published_highlight; ?>" />
                                    </td>
                                </tr>
                                <tr><td><?php _e('Future Highlight', 'wp-edit'); ?></td>
                                    <td class="jwl_user_cell">
                                    <input id="future_highlight" type="text" name="wp_edit_user_specific[future_highlight]" class="color_field" value="<?php echo $future_highlight; ?>" />
                                    </td>
                                </tr>
                                <tr><td><?php _e('Private Highlight', 'wp-edit'); ?></td>
                                    <td class="jwl_user_cell">
                                    <input id="private_highlight" type="text" name="wp_edit_user_specific[private_highlight]" class="color_field" value="<?php echo $private_highlight; ?>" />
                                    </td>
                                </tr>
                                </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
               
                	<input type="submit" value="<?php _e('Save User Specific Options', 'wp-edit'); ?>" class="button button-primary" id="submit_user_specific" name="submit_user_specific">
                    <?php wp_nonce_field( 'wpe_save_user_specific_opts' ); ?>
                </div>
                </form><?php
            }
			/*
			****************************************************************
			Database Tab
			****************************************************************
			*/
			else if($active_tab == 'database') {
				?>
                <div class="main_container">
                
                    <h3><?php _e('Database Options', 'wp-edit'); ?></h3>
                    
                    <div class="metabox-holder">
                    
                        <div class="postbox">
                            <h3><span><?php _e('Export WP Edit Options', 'wp-edit'); ?></span></h3>
                            <div class="inside">
                                <p><?php _e('Export the plugin settings for this site as a .json file. This allows you to easily import the configuration into another site.', 'wp-edit'); ?></p>
                                <form method="post" action="">
                                    <p><input type="hidden" name="database_action" value="export_settings" /></p>
                                    <p>
                                    <?php wp_nonce_field( 'database_action_export_nonce', 'database_action_export_nonce' ); ?>
                                    <?php submit_button( __('Export', 'wp-edit'), 'primary', 'submit', false ); ?>
                                    </p>
                                </form>
                            </div><!-- .inside -->
                        </div><!-- .postbox -->
                                     
                        <div class="postbox">
                            <h3><span><?php _e('Import WP Edit Options', 'wp-edit'); ?></span></h3>
                            <div class="inside">
                                <p><?php _e('Import the plugin settings from a .json file. This file can be obtained by exporting the settings on another site using the form above.', 'wp-edit'); ?></p>
                                <form method="post" enctype="multipart/form-data">
                                    <p><input type="file" name="import_file"/></p>
                                    <p>
                                    <input type="hidden" name="database_action" value="import_settings" />
                                    <?php wp_nonce_field( 'database_action_import_nonce', 'database_action_import_nonce' ); ?>
                                    <?php submit_button( __('Import', 'wp-edit'), 'primary', 'submit', false ); ?>
                                    </p>
                                </form>
                            </div><!-- .inside -->
                        </div><!-- .postbox -->
                                     
                        <div class="postbox">
                            <h3><span><?php _e('Reset WP Edit Options', 'wp-edit'); ?></span></h3>
                            <div class="inside">
                                <p><?php _e('Reset all plugin settings to their original default states.', 'wp-edit'); ?></p>
                                <form method="post" action="">
                                    <?php wp_nonce_field( 'reset_db_values_nonce', 'reset_db_values_nonce' ); ?>
                                    <input class="button-primary reset_db_values" name="reset_db_values" type="submit" style="display:none;" />
                                    <input class="button-primary reset_db_values_confirm" name="reset_db_values_confirm" type="button" value="<?php _e('Reset', 'wp-edit'); ?>" />
                                    </p>
                                </form>
                            </div><!-- .inside -->
                        </div><!-- .postbox -->
                        
                        <div class="postbox">
                            <h3><span><?php _e('Uninstall WP Edit (Completely)', 'wp-edit'); ?></span></h3>
                            <div class="inside">
                                <p><?php _e('Designed by intention, this plugin will not delete the associated database tables when activating and deactivating.', 'wp-edit'); ?><br />
                                   <?php _e('This ensures the data is kept safe when troubleshooting other WordPress conflicts.', 'wp-edit'); ?><br />
                                   <?php _e('In order to completely uninstall the plugin, AND remove all associated database tables, please use the option below.', 'wp-edit'); ?><br />
                                </p>
                                <form method="post" action="">
                                    <?php wp_nonce_field('wp_edit_uninstall_nonce_check', 'wp_edit_uninstall_nonce'); ?>
                                    <input id="plugin" name="plugin" type="hidden" value="wp-edit/main.php" />
                                    <input name="uninstall_confirm" id="uninstall_confirm" type="checkbox" value="1" /><label for="uninstall_confirm"></label> <strong><?php _e('Please confirm before proceeding','wp-edit'); ?><br /><br /></strong>
                                    <input class="button-primary" name="uninstall" type="submit" value="<?php _e('Uninstall','wp-edit'); ?>" />
                                </form>
                            </div><!-- .inside -->
                        </div><!-- .postbox -->
                        
                    </div><!-- .metabox-holder -->	
                </div><!-- .main_container -->	
                <?php
			}
			/*
			****************************************************************
			About Tab
			****************************************************************
			*/
			else if($active_tab == 'about') {
				
				// Get mysql version number (scrape php_info module)
				ob_start();
				phpinfo(INFO_MODULES);
				$info = ob_get_contents();
				ob_end_clean();
				$info = stristr($info, 'Client API version');
				preg_match('/[1-9].[0-9].[1-9][0-9]/', $info, $match);
				$sql_version = $match[0]; 
				
				// Get plugin info
				$url = WPEDIT_PLUGIN_PATH.'main.php';
				$plugin_data = get_plugin_data( $url );
				
				global $wp_version;
				
				echo '<div class="main_container">';
					
					?>
					<h3><?php _e('Information','wp-edit'); ?></h3>
					
					<div class="metabox-holder">
						<div class="postbox">
							<div class="inside">
							
								<p><?php _e('Plugin and server version information.', 'wp-edit'); ?></p>
							
								<table class="table table-bordered" cellpadding="3" style="width:50%;">
								<tbody>
								<tr><td><?php _e('WP Edit Pro Version:','wp-edit'); ?></td>
									<td>
									<?php echo $plugin_data['Version']; ?>
									</td>
								</tr>
								<tr><td><?php _e('WordPress Version:','wp-edit'); ?></td>
									<td>
									<?php echo $wp_version; ?>
									</td>
								</tr>
								<tr><td><?php _e('PHP Version:','wp-edit'); ?></td>
									<td>
									<?php echo phpversion(); ?>
									</td>
								</tr>
								<tr><td><?php _e('HTML Version:','wp-edit'); ?></td>
									<td>
									<span class="wpep_html_version"></span>
									</td>
								</tr>
								<tr><td><?php _e('MySql Version:','wp-edit'); ?></td>
									<td>
									<?php echo $sql_version; ?>
									</td>
								</tr>
								<tr><td><?php _e('jQuery Version:','wp-edit'); ?></td>
									<td>
									<?php echo $GLOBALS['wp_scripts']->registered['jquery-core']->ver; ?>
									</td>
								</tr>
								</tbody>
								</table>
							</div>
						</div>
					</div>
					
					<h3><?php _e('Support','wp-edit'); ?></h3>
					<div class="metabox-holder">
						<div class="postbox">
							<div class="inside">
							
								<p><?php _e('Please use the following helpful links for plugin support.', 'wp-edit'); ?></p>
							
								<table class="table table-bordered" cellpadding="3" style="width:30%;">
								<tbody>
								<tr><td><?php _e('Support Forum:','wp-edit'); ?></td>
									<td>
									<?php echo '<a target="_blank" href="https://wordpress.org/support/plugin/wp-edit">'.__('Support Forum', 'wp-edit').'</a>'; ?>
									</td>
								</tr>
								<tr><td><?php _e('Knowledge Base:','wp-edit'); ?></td>
									<td>
									<?php echo '<a target="_blank" href="http://learn.wpeditpro.com">'.__('Knowledge Base', 'wp-edit').'</a>'; ?>
									</td>
								</tr>
								</tbody>
								</table>
							</div>
						</div>
					</div>
					
					<h3><?php _e('Documentation','wp-edit'); ?></h3>
					<div class="metabox-holder">
						<div class="postbox">
							<div class="inside">
							
								<p><?php _e('Remember, complete plugin documentation can be found on our <a target="_blank" href="http://learn.wpeditpro.com">Knowledge Base</a>.', 'wp-edit'); ?></p>
								<p><?php _e('Visit the <a target="_blank" href="http://learn.wpeditpro.com/category/plugin-options/">Knowledge Base Plugin Options</a> page to get started.','wp-edit'); ?></p>
							</div>
						</div>
					</div>
					<?php
				echo '</div>';
			}
			?>
        </div><!-- .wrap -->
        
        <div id="right_column_metaboxes">
        
        	<div class="main_container">
            	<h3><?php _e('WP Edit Pro', 'wp-edit'); ?></h3>
                <div class="metabox-holder"> 
                    <div class="postbox">
                        <div class="inside">
                        
                            <p><?php _e('Upgrade to WP Edit Pro today; and enjoy additional options such as:', 'wp-edit'); ?></p>
                            <ul class="wpep_pro_upgrade_list">
                                <li><span class="dashicons dashicons-yes"></span><?php _e('4 customizable button rows instead of only 2.', 'wp-edit'); ?></li>
                                <li><span class="dashicons dashicons-yes"></span><?php _e('Create multiple button arrangements.', 'wp-edit'); ?></li>
                                <li><span class="dashicons dashicons-yes"></span><?php _e('Limit users over what buttons they can access.', 'wp-edit'); ?></li>
                                <li><span class="dashicons dashicons-yes"></span><?php _e('Powerful "Snidget" Builder.', 'wp-edit'); ?></li>
                                <li><span class="dashicons dashicons-yes"></span><?php _e('Over 30 additional options and settings.', 'wp-edit'); ?></li>
                                <li><span class="dashicons dashicons-yes"></span><?php _e('Over a dozen additional editor buttons (Image maps, YouTube Videos, and many more!).', 'wp-edit'); ?></li>
                            </ul>
                            <a href="https://wpeditpro.com" target="_blank" class="button-primary"><?php _e('WP Edit Pro', 'wp-edit'); ?></a>
                        </div>
                    </div>
                </div> 
            </div>   
            
            <div class="main_container">
            	<h3><?php _e('Like this Plugin?', 'wp-edit'); ?></h3>
                <div class="metabox-holder"> 
                    <div class="postbox">
                        <div class="inside">
                        
                            <p><?php _e('Please take a moment to rate and review this plugin on the WordPress Plugin Repository.', 'wp-edit'); ?></p>
                            <p><a href="https://wordpress.org/plugins/wp-edit/" target="_blank" class="button-primary"><?php _e('Rate Plugin', 'wp-edit'); ?></a></p>
                            
                            <?php
							if ( ! function_exists( 'plugins_api' ) ) {
								require_once( ABSPATH . 'wp-admin/includes/plugin-install.php' );
							}
 
							/** Prepare our query */
							$call_api = plugins_api( 'plugin_information', array( 'slug' => 'wp-edit', 'fields' => array( 'active_installs' => true ) ) );
						 
							/** Check for Errors & Display the results */
							if ( is_wp_error( $call_api ) ) {
						 
								echo '<pre>' . print_r( $call_api->get_error_message(), true ) . '</pre>';
							} 
							else {
								
								echo '<h3>';
									_e( 'WP Edit Rating Statistics', 'wp_edit_pro' );
								echo '</h3>';
								
								// Get ratings array
								$ratings = $call_api->ratings;
						 
								echo '<table><tbody>';
									echo '<tr><td>Downloaded:</td><td>' . number_format( $call_api->downloaded ) . ' times</td></tr>';
									echo '<tr><td>Active Installs:</td><td>' . number_format( $call_api->active_installs ) . '+</td></tr>';
									echo '<tr><td>Number of Ratings:</td><td>' . $call_api->num_ratings . '</td></tr>';
								echo '</tbody></table>';
								echo '<br />';
						 		
								// Calculations
								$total_ratings = $call_api->num_ratings;
								
								$five_star = round( ( $ratings[5] / $total_ratings ) * 100 );
								$four_star = round( ( $ratings[4] / $total_ratings ) * 100 );
								$three_star = round( ( $ratings[3] / $total_ratings ) * 100 );
								$two_star = round( ( $ratings[2] / $total_ratings ) * 100 );
								$one_star = round( ( $ratings[1] / $total_ratings ) * 100 );
								
								$overall_stars = number_format( ( $call_api->rating / 20 ), 1 );
								
								// Setup plugin star container
								echo '<div class="plugin_star_container">';
									echo '<div class="empty-stars"></div>';
									echo '<div class="full-stars" style="width:' . $call_api->rating . '%"></div>';
								echo '</div>';
								
								echo '<p style="margin:0px 0px 10px;">' . $overall_stars . ' out of 5 stars</p>';
								
								// Setup plugin rating table
								echo '<table class="table table_plugin_ratings"><tbody>';
									echo '<tr><td>5 stars:</td><td><div class="plugin_rating_container"><div class="plugin_rating_percentage" style="width:' . $five_star . '%;"></div></div>' . $ratings[5] . '</td></tr>';
									echo '<tr><td>4 stars:</td><td><div class="plugin_rating_container"><div class="plugin_rating_percentage" style="width:' . $four_star . '%;"></div></div>' . $ratings[4] . '</td></tr>';
									echo '<tr><td>3 stars:</td><td><div class="plugin_rating_container"><div class="plugin_rating_percentage" style="width:' . $three_star . '%;"></div></div>' . $ratings[3] . '</td></tr>';
									echo '<tr><td>2 stars:</td><td><div class="plugin_rating_container"><div class="plugin_rating_percentage" style="width:' . $two_star . '%;"></div></div>' . $ratings[2] . '</td></tr>';
									echo '<tr><td>1 star:</td><td><div class="plugin_rating_container"><div class="plugin_rating_percentage" style="width:' . $one_star . '%;"></div></div>' . $ratings[1] . '</td></tr>';
								echo '</tbody></table>';
							}
							?>
                            
                        </div>
                    </div>
                </div> 
            </div>   
        </div>
        
        <div style="clear:both;"></div>
        <?php
	}
	
	
	/*
	****************************************************************
	Load/Save Page
	****************************************************************
	*/
    public function load_page() { 
	
		/*
		****************************************************************
		If Import Settings was successful... let's alert a message
		****************************************************************
		*/
		if(isset($_GET['import']) && $_GET['import'] === 'true') {
			
			echo '<div id="message" class="updated"><p>';
			_e('Plugin settings have been successfully imported.' ,'wp-edit');
			echo '</p></div>';
		}
	
		/*
		****************************************************************
		If Buttons Tab options are submitted
		****************************************************************
		*/
		if(isset($_POST['wpep_reset_buttons'])) {
			
			// Verify nonce
			$buttons_opts_nonce = $_REQUEST['_wpnonce'];
			if ( ! wp_verify_nonce( $buttons_opts_nonce, 'wpe_save_buttons_opts' ) ) {
				
				echo 'This request could not be verified.';
				exit; 
			}
			
			// Check if DB value exists.. if YES, then keep value.. if NO, then replace with protected defaults
			$options_buttons = $this->global_options_buttons;
			
			// Set DB values
			update_option('wp_edit_buttons', $options_buttons);
				
			// Alert user
			function wpe_reset_buttons_from_input(){
				
				echo '<div class="updated">';
					echo '<p>';
						_e('Buttons have been reset successfully.','wp-edit');
					echo '</p>';
				echo '</div>';
			}
			add_action('admin_notices', 'wpe_reset_buttons_from_input');
		}
		
		if(isset($_POST['wpep_save_buttons'])) {
			
			// Verify nonce
			$buttons_opts_nonce = $_REQUEST['_wpnonce'];
			if ( ! wp_verify_nonce( $buttons_opts_nonce, 'wpe_save_buttons_opts' ) ) {
				
				echo 'This request could not be verified.';
				exit; 
			}
			
			if(isset($_POST['get_sorted_array_results']) && ($_POST['get_sorted_array_results'] != '')) {
				
				//***************************************************
				// Get buttons from hidden div and update database
				//***************************************************
				$post_buttons = $_POST['get_sorted_array_results'];
				$final_button_array = array();
				
				// Explode first set of containers (breaks into "toolbar1:bold,italic,etc."
				$explode_containers = explode('*', $post_buttons);
				
				// Loop each container
				foreach($explode_containers as $container) {
				
					// Get rid of first container (empty)
					if($container != '') {
						
						// Explode each container
						$explode_each_container = explode(':', $container);
						// Replace commas (from js array) with spaces
						$explode_each_container = str_replace(',', ' ', $explode_each_container);
						
						// Push key (container) and value (buttons) to final array
						$final_button_array[$explode_each_container[0]] = $explode_each_container[1];
					}
				}
		
				// Update database buttons
				update_option('wp_edit_buttons', $final_button_array);
				
				// Alert user
				function wpe_save_buttons_from_input(){
					
					echo '<div class="updated">';
						echo '<p>';
							_e('Buttons have been saved successfully.','wp-edit');
						echo '</p>';
					echo '</div>';
				}
				add_action('admin_notices', 'wpe_save_buttons_from_input');
			}
			
			//***************************************************
			// Check for new buttons
			//***************************************************
			/*** Get page buttons ***/
			$buttons = '';
			$active_buttons = $_POST['get_sorted_array_results'];  // Get each button container value (string)
			$explode1 = explode('*', $active_buttons);  // Explode into button containers (toolbar1:bold,italic,etc)
			$final_buttons = '';
			
			foreach($explode1 as $value) {
				
				$explode2 = explode(':', $value);  // Explodes from (toolbar1:bold,italic,link,etc)
				$button_string = isset($explode2[1]) ? $explode2[1] : '';  // Get second array item (buttons (comma separated))
				
				if(!empty($button_string)) {  // If the buttons string is not empty
				
					$final_buttons .= $button_string.',';  // Create long string of comma separated butttons
				}
			}
			
			// Right trim comma from string
			$final_buttons = rtrim($final_buttons, ',');
			
			// Create array of all buttons on page ((bold)(italic)(etc))
			$page_array = array_filter(explode(',', $final_buttons));
			
			
			/*** Get default buttons ***/
			// Get all buttons from initialization code (including any new buttons)
			$new_wp_edit_class_buttons = new wp_edit_class();
			$options_buttons = $new_wp_edit_class_buttons->global_options_buttons;
			$buttons_option = '';
				
			// Loop each container and extract buttons
			foreach($options_buttons as $option) {
				
				$buttons_option .= ' ' . $option;  // The list of initialization buttons (as string)
			}
			
			// Trim whitespace from left of $buttons_option string (space separated)
			$buttons_option = ltrim($buttons_option);
			
			// Explode space separated string into array
			$buttons_option_array = array_filter(explode(' ', $buttons_option));
			
			/*** Compare arrays ***/
			$array_diff = array_diff($buttons_option_array, $page_array);
			
			// If new buttons were discovered
			if(!empty($array_diff)) {  
				
				// Get each button name from array difference
				global $each_button_trim;
				$each_button = '';
				foreach($array_diff as $button) {  // Loop array to get each button name
					
					$each_button .= ' '.$button;
				}
				// Remove white space from far left of string
				$each_button_trim = ltrim($each_button);
				
				
				// Get buttons option and append new buttons to tmce container
				$db_buttons = get_option('wp_edit_buttons');
				$db_buttons['tmce_container'] = $db_buttons['tmce_container'].$each_button;
				
				// Update database
				update_option('wp_edit_buttons', $db_buttons);
				
				// Alert user
				function wpe_alert_user_new_buttons() {
					
					global $each_button_trim;
					echo '<div id="message" class="updated"><p>';
					_e('New buttons were discovered. The following buttons have been added to the Button Container:','wp-edit');
					echo '<br /><strong>'.$each_button_trim.'</strong>';
					echo '</p></div>';
				}
				add_action('admin_notices', 'wpe_alert_user_new_buttons');
			}
			
			//*************************************************************************************************
			// Check saved database buttons against plugin default buttons.
			// - Will remove any buttons from rows if they are no longer supported by plugin.
			//*************************************************************************************************
			
			// Get user saved buttons
			$options_buttons = get_option('wp_edit_buttons');
			// Get default plugin buttons
			$new_wp_edit_class_buttons = new wp_edit_class();
			$plugin_buttons = $new_wp_edit_class_buttons->global_options_buttons;
			
			// Merge all default plugin buttons into single array
			$all_array = '';
			foreach($plugin_buttons as $slot_array) {
				
				if(!empty($slot_array) && $slot_array != '') {  // Skip containter array if empty
					$all_array .= $slot_array.' ';  // Create single string of all default plugin buttons
				}
			}
			$all_array = rtrim($all_array, ' ');  // Remove trailing right space
			$plugin_array = explode(' ', $all_array);  // Explode at spaces to make single array (this is an array of all current plugin buttons)
			
			
			// Get filtered plugin buttons
			$get_filters = $this->filtered_buttons;
			
			// If the array set is not empty (filters being applied)
			if(  ! empty( $get_filters ) ) {
				foreach( $get_filters as $key => $values ) {
					
					$plugin_array[] = $values['button_id'];
				}
			}
			
			
			
			// Create arrays of user saved buttons
			global $tot_array;
			$val_array = array();
			$tot_array = array();  // Used to display results to user
			foreach($options_buttons as $cont=>$val) {  // Break down array
			
				if(!empty($val) && $val !='') {  // Skip container if empty
					$val_array = explode(' ', $val);  // Explode at spaces into array (this is multiarray of each container array of user buttons)
					
					$rem_array = array();  // Setup removal array
					foreach($val_array as $item) {
						if(!in_array($item, $plugin_array)) {
							// Removed array items
							$rem_array[] = $item;
							$tot_array[] = $item;
						}
					}
					
					if($rem_array) {
						
						$old_opts = $options_buttons[$cont];  // Get option from database values
						$old_opts = explode(' ', $old_opts);  // Explode to array
						$new_opt_array = array_diff($old_opts, $rem_array);  // Compare arrays to remove non-supported buttons
						$new_opt_array = implode(' ', $new_opt_array);  // Implode back to string
						$options_buttons[$cont] = $new_opt_array;  // Set container to new string
						
						// Update buttons options
						update_option('wp_edit_buttons', $options_buttons);
						
						function wpe_remove_buttons_notice() {
					
							global $tot_array;
							echo '<div class="updated"><p>';
								$tot_array = implode(', ', $tot_array);
								_e('The following buttons have been removed from WP Edit Pro:', 'wp-edit');
								echo '<br />';
								echo '<strong>'.$tot_array.'</strong>';
							echo '</p></div>';
						}
						add_action('admin_notices', 'wpe_remove_buttons_notice');
					}
				}
			}
		}
	
	
		/*
		****************************************************************
		If Global Tab button was submitted
		****************************************************************
		*/
		if(isset($_POST['submit_global'])) {
			
			// Verify nonce
			$global_opts_nonce = $_REQUEST['_wpnonce'];
			if ( ! wp_verify_nonce( $global_opts_nonce, 'wpe_save_global_opts' ) ) {
				
				echo 'This request could not be verified.';
				exit; 
			}
		
			$options_global = get_option('wp_edit_global');
			$options_global['jquery_theme'] = isset($_POST['jquery_theme']) ? $_POST['jquery_theme'] : 'smoothness';
			$options_global['disable_admin_links'] = isset($_POST['disable_admin_links']) ? '1' : '0';
			$options_global['disable_fancy_tooltips'] = isset($_POST['disable_fancy_tooltips']) ? '1' : '0';
			
			update_option('wp_edit_global', $options_global);
			
			function global_saved_notice(){
				
				echo '<div class="updated"><p>';
				_e('Global options successfully saved.', 'wp-edit');
				echo '</p></div>';
			}
			add_action('admin_notices', 'global_saved_notice');
		}
		
		/*
		****************************************************************
		If General Tab button was submitted
		****************************************************************
		*/
		if(isset($_POST['submit_general'])) {
			
			// Verify nonce
			$general_opts_nonce = $_REQUEST['_wpnonce'];
			if ( ! wp_verify_nonce( $general_opts_nonce, 'wpe_save_general_opts' ) ) {
				
				echo 'This request could not be verified.';
				exit; 
			}
			
			$options_general = get_option('wp_edit_general');
			$options_general['linebreak_shortcode'] = isset($_POST['linebreak_shortcode']) ? '1' : '0';
			$options_general['shortcodes_in_widgets'] = isset($_POST['shortcodes_in_widgets']) ? '1' : '0';
			$options_general['shortcodes_in_excerpts'] = isset($_POST['shortcodes_in_excerpts']) ? '1' : '0';
			$options_general['post_excerpt_editor'] = isset($_POST['post_excerpt_editor']) ? '1' : '0';
			$options_general['page_excerpt_editor'] = isset($_POST['page_excerpt_editor']) ? '1' : '0';
			$options_general['profile_editor'] = isset($_POST['profile_editor']) ? '1' : '0';
			
			// Save cpt excerpts
			$cpt_excerpts = array();
			$options_general['cpt_excerpt_editor'] = array();
			
			if(isset($_POST['cpt_excerpt_editor'])) {
				
				$cpt_excerpts = $_POST['cpt_excerpt_editor'];
				
				// Loop checked cpt's and create array
				foreach($cpt_excerpts as $key => $value) {
					
					if($value === 'on')
						$options_general['cpt_excerpt_editor'][] = $key;
				}
			}
			else {
				$options_general['cpt_excerpt_editor'] = array();
			}
			
			update_option('wp_edit_general', $options_general);
			
			function general_saved_notice(){
				
				echo '<div class="updated"><p>';
				_e('General options successfully saved.', 'wp-edit');
				echo '</p></div>';
			}
			add_action('admin_notices', 'general_saved_notice');
		}
		
		/*
		****************************************************************
		If Posts Tab button was submitted
		****************************************************************
		*/
		if(isset($_POST['submit_posts'])) {
			
			// Verify nonce
			$posts_pages_opts_nonce = $_REQUEST['_wpnonce'];
			if ( ! wp_verify_nonce( $posts_pages_opts_nonce, 'wpe_save_posts_pages_opts' ) ) {
				
				echo 'This request could not be verified.';
				exit; 
			}
			
			// Delete database revisions
			if(isset($_POST['submit_posts']) && isset($_POST['delete_revisions'])) {
				
				function wp_edit_delete_revisions_admin_notice( ){	
				
					global $wpdb;
				
					// Get pre DB size
					$query = $wpdb->get_results( "SHOW TABLE STATUS", ARRAY_A );
					$size = 0;
					foreach ($query as $row) {
						$size += $row["Data_length"] + $row["Index_length"];
					}
					$decimals = 2;  
					$mbytes = number_format($size/(1024*1024),$decimals);
					
					// Delete Post Revisions from DB
					$query3_raw = "DELETE FROM wp_posts WHERE post_type = 'revision'";
					$query3 = $wpdb->query($query3_raw);
					if ($query3) {
						$deleted_rows = __('Revisions successfully deleted', 'wp-edit');
					} else {
						$deleted_rows = __('No POST revisions were found to delete', 'wp-edit');
					}
					
					// Get post DB size
					$query2 = $wpdb->get_results( "SHOW TABLE STATUS", ARRAY_A );
					$size2 = 0;
					foreach ($query2 as $row2) {
						$size2 += $row2["Data_length"] + $row2["Index_length"];
					}
					$decimals2 = 2;  
					$mbytes2 = number_format($size2/(1024*1024),$decimals2); 
					
					echo '<div class="updated"><p>';
					_e('Message: ', 'wp-edit');
					echo '<strong>'.$deleted_rows.'</strong>.</p><p>';
					_e('Database size before deletions: ', 'wp-edit');
					echo '<strong>'.$mbytes.'</strong> ';
					_e('megabytes.', 'wp-edit');
					echo '</p><p>';
					_e('Database Size after deletions: ', 'wp-edit');
					echo '<strong>'.$mbytes2.'</strong> ';
					_e('megabytes.', 'wp-edit');
					echo '</p></div>';
				}
				add_action('admin_notices', 'wp_edit_delete_revisions_admin_notice');
			}
	
			$options_posts = get_option('wp_edit_posts');
			
			$options_posts['post_title_field'] = isset($_POST['post_title_field']) ? sanitize_text_field($_POST['post_title_field']) : 'Enter title here';
			$options_posts['column_shortcodes'] = isset($_POST['column_shortcodes']) ? '1' : '0';
			$options_posts['disable_wpautop'] = isset($_POST['disable_wpautop']) ? '1' : '0';
			
			$options_posts['max_post_revisions'] = isset($_POST['max_post_revisions']) ? sanitize_text_field($_POST['max_post_revisions']) : '';
			$options_posts['max_page_revisions'] = isset($_POST['max_page_revisions']) ? sanitize_text_field($_POST['max_page_revisions']) : '';
			
			$options_posts['hide_admin_posts'] = isset($_POST['hide_admin_posts']) ? sanitize_text_field($_POST['hide_admin_posts']) : '';
			$options_posts['hide_admin_pages'] = isset($_POST['hide_admin_pages']) ? sanitize_text_field($_POST['hide_admin_pages']) : '';
			
			update_option('wp_edit_posts', $options_posts);
			
			function posts_saved_notice(){
				
				echo '<div class="updated"><p>';
				_e('Posts/Pages options successfully saved.', 'wp-edit');
				echo '</p></div>';
			}
			add_action('admin_notices', 'posts_saved_notice');
		}
		
		/*
		****************************************************************
		If Editor button was submitted
		****************************************************************
		*/
		if(isset($_POST['submit_editor'])) {
			
			// Verify nonce
			$editor_opts_nonce = $_REQUEST['_wpnonce'];
			if ( ! wp_verify_nonce( $editor_opts_nonce, 'wpe_save_editor_opts' ) ) {
				
				echo 'This request could not be verified.';
				exit; 
			}
			
			$options_editor = get_option('wp_edit_editor');
			
			$options_editor['editor_add_pre_styles'] = isset($_POST['editor_add_pre_styles']) ? '1' : '0';
			$options_editor['default_editor_fontsize_type'] = isset($_POST['default_editor_fontsize_type']) ? $_POST['default_editor_fontsize_type'] : 'pt';
			$options_editor['default_editor_fontsize_values'] = isset($_POST['default_editor_fontsize_values']) ? sanitize_text_field($_POST['default_editor_fontsize_values']) : '';
			$options_editor['bbpress_editor'] = isset($_POST['bbpress_editor']) ? '1' : '0';
			
			update_option('wp_edit_editor', $options_editor);
				
			function editor_saved_notice(){
				
				echo '<div class="updated"><p>';
				_e('Editor options successfully saved.', 'wp-edit');
				echo '</p></div>';
			}
			add_action('admin_notices', 'editor_saved_notice');
		}
	
		/*
		****************************************************************
		If Extras Tab button was submitted
		****************************************************************
		*/
		if(isset($_POST['submit_extras'])) {
			
			// Verify nonce
			$extras_opts_nonce = $_REQUEST['_wpnonce'];
			if ( ! wp_verify_nonce( $extras_opts_nonce, 'wpe_save_extras_opts' ) ) {
				
				echo 'This request could not be verified.';
				exit; 
			}
			
			$options_extras = get_option('wp_edit_extras');
			$options_extras['signoff_text'] = isset($_POST['wp_edit_signoff']) ? stripslashes($_POST['wp_edit_signoff']) : 'Please enter text here...';
			
			update_option('wp_edit_extras', $options_extras);
				
			function extras_saved_notice(){
				
				echo '<div class="updated"><p>';
				_e('Extra options saved.', 'wp-edit');
				echo '</p></div>';
			}
			add_action('admin_notices', 'extras_saved_notice');
		}
		
		/*
		****************************************************************
		If user specific was submitted
		****************************************************************
		*/
		if(isset($_POST['submit_user_specific'])) {
			
			// Verify nonce
			$user_specific_opts_nonce = $_REQUEST['_wpnonce'];
			if ( ! wp_verify_nonce( $user_specific_opts_nonce, 'wpe_save_user_specific_opts' ) ) {
				
				echo 'This request could not be verified.';
				exit; 
			}
			
			// If User Specific was submitted
			$post_vars = isset($_POST['wp_edit_user_specific']) ? $_POST['wp_edit_user_specific'] : '';
			
			global $current_user;
			$options_user_specific_user_meta = get_user_meta($current_user->ID, 'aaa_wp_edit_user_meta', true);
		
			$options_user_specific_user_meta['id_column'] = isset($post_vars['id_column']) ? '1' : '0';
			$options_user_specific_user_meta['thumbnail_column'] = isset($post_vars['thumbnail_column']) ? '1' : '0';
			$options_user_specific_user_meta['hide_text_tab'] = isset($post_vars['hide_text_tab']) ? '1' : '0';
			$options_user_specific_user_meta['default_visual_tab'] = isset($post_vars['default_visual_tab']) ? '1' : '0';
			$options_user_specific_user_meta['dashboard_widget'] = isset($post_vars['dashboard_widget']) ? '1' : '0';
			
			$options_user_specific_user_meta['enable_highlights'] = isset($post_vars['enable_highlights']) ? '1' : '0';
			$options_user_specific_user_meta['draft_highlight'] = isset($post_vars['draft_highlight']) ? $post_vars['draft_highlight'] : '';
			$options_user_specific_user_meta['pending_highlight'] = isset($post_vars['pending_highlight']) ? $post_vars['pending_highlight'] : '';
			$options_user_specific_user_meta['published_highlight'] = isset($post_vars['published_highlight']) ? $post_vars['published_highlight'] : '';
			$options_user_specific_user_meta['future_highlight'] = isset($post_vars['future_highlight']) ? $post_vars['future_highlight'] : '';
			$options_user_specific_user_meta['private_highlight'] = isset($post_vars['private_highlight']) ? $post_vars['private_highlight'] : '';
			
			update_user_meta($current_user->ID, 'aaa_wp_edit_user_meta', $options_user_specific_user_meta);
				
			function user_specific_saved_notice(){
				
				echo '<div class="updated"><p>';
				_e('User specific options saved.', 'wp-edit');
				echo '</p></div>';
			}
			add_action('admin_notices', 'user_specific_saved_notice');
		}
		
		/*
		****************************************************************
		If reset plugin options
		****************************************************************
		*/
		if (isset($_POST['reset_db_values'])) {
			
			if ( !isset($_POST['reset_db_values_nonce'])) {  // Verify nonce
					
				print __('Sorry, your nonce did not verify.', 'wp-edit');
				exit;
			}
			else {
				
				// Get current user
				global $current_user;
				
				// Set DB values (from class vars)
				update_option('wp_edit_buttons', $this->global_options_buttons);
				update_option('wp_edit_global', $this->global_options_global);
				update_option('wp_edit_general', $this->global_options_general);
				update_option('wp_edit_posts', $this->global_options_posts);
				update_option('wp_edit_editor', $this->global_options_editor);
				update_option('wp_edit_extras', $this->global_options_extras);
				update_user_meta($current_user->ID, 'aaa_wp_edit_user_meta', $this->global_options_user_specific);
		
				echo '<div id="message" class="updated"><p>';
				_e('Plugin settings have been restored to defaults.', 'wp-edit');
				echo '</p></div>';
			}
		}
		
		/*
		****************************************************************
		If uninstall plugin was submitted
		****************************************************************
		*/
		// Display notice if trying to uninstall but forget to check box
		if (isset($_POST['uninstall'] ) && !isset($_POST['uninstall_confirm'])) {
			
			echo '<div id="message" class="error"><p>';
			_e('You must also check the confirm box before options will be uninstalled and deleted.','wp-edit');
			echo '</p></div>';
		}
		// Uninstall plugin
		if (isset($_POST['uninstall'], $_POST['uninstall_confirm'] ) ) {
			
			if ( !isset($_POST['wp_edit_uninstall_nonce']) || !wp_verify_nonce($_POST['wp_edit_uninstall_nonce'],'wp_edit_uninstall_nonce_check') ) {  // Verify nonce
					
				print __('Sorry, your nonce did not verify.', 'wp-edit');
				exit;
			}
			else {
				
				global $current_user;
				delete_option('wp_edit_buttons','wp_edit_buttons');
				delete_option('wp_edit_global','wp_edit_global');
				delete_option('wp_edit_general','wp_edit_general');
				delete_option('wp_edit_posts','wp_edit_posts');
				delete_option('wp_edit_editor','wp_edit_editor');
				delete_option('wp_edit_extras','wp_edit_extras');
				delete_option('wp_edit_install','wp_edit_install');
				delete_user_meta($current_user->ID, 'aaa_wp_edit_user_meta');
				delete_user_meta($current_user->ID, 'ignore_wpedit_ag_notice');
			 
				// Deactivate the plugin
				$current = get_option('active_plugins');
				array_splice($current, array_search( $_POST['plugin'], $current), 1 );
				update_option('active_plugins', $current);
				
				// Redirect to plugins page with 'plugin deactivated' status message
				wp_redirect( admin_url('/plugins.php?deactivate=true') );
				exit;
			}
		}
	}
	
	/*
	****************************************************************
	Admin Init
	****************************************************************
	*/
	public function process_activation_redirect() {
	
		// Check for redirect option after plugin activation
		$re_url = admin_url('admin.php?page=wp_edit_options');
		if (get_option('wp_edit_activation_redirect', false)) {
			
			delete_option('wp_edit_activation_redirect');
			wp_redirect($re_url);
		}
	}
	
	/*
	****************************************************************
	Export Options
	****************************************************************
	*/
	public function process_settings_export() {
		
		if( empty( $_POST['database_action'] ) || 'export_settings' != $_POST['database_action'] )
			return;
		 
		if( ! wp_verify_nonce( $_POST['database_action_export_nonce'], 'database_action_export_nonce' ) )
			return;
		 
		if( ! current_user_can( 'manage_options' ) )
			return;
		 
		// Get DB values
		global $current_user;
		
		$options_buttons = get_option('wp_edit_buttons');
		$options_global = get_option('wp_edit_global');
		$options_general = get_option('wp_edit_general');
		$options_posts = get_option('wp_edit_posts');
		$options_editor = get_option('wp_edit_editor');
		$options_extras = get_option('wp_edit_extras');
		$options_user_specific = get_user_meta($current_user->ID, 'aaa_wp_edit_user_meta', true);
		
		$options_export_array = array(
			'wp_edit_buttons' => $options_buttons,
			'wp_edit_global' => $options_global,  
			'wp_edit_general' => $options_general, 
			'wp_edit_posts' => $options_posts, 
			'wp_edit_editor' => $options_editor,  
			'wp_edit_extras' => $options_extras, 
			'wp_edit_user_specific' => $options_user_specific
		);
		 
		ignore_user_abort( true );
		 
		nocache_headers();
		header( 'Content-Type: application/json; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=wp_edit_settings_export-' . date( 'm-d-Y' ) . '.json' );
		header( "Expires: 0" );
		 
		echo json_encode( $options_export_array );
		exit;
	}
	
	/*
	****************************************************************
	Import Options
	****************************************************************
	*/
	public function process_settings_import() {
		
		if( empty( $_POST['database_action'] ) || 'import_settings' != $_POST['database_action'] )
			return;
		 
		if( ! wp_verify_nonce( $_POST['database_action_import_nonce'], 'database_action_import_nonce' ) )
			return;
		 
		if( ! current_user_can( 'manage_options' ) )
			return;
		 
		$extension = end( explode( '.', $_FILES['import_file']['name'] ) );
		 
		if( $extension != 'json' ) {
			wp_die( __('Please upload a valid .json file', 'wp-edit' ) );
		}
		 
		$import_file = $_FILES['import_file']['tmp_name'];
		 
		if( empty( $import_file ) ) {
			wp_die( __('Please upload a file to import', 'wp-edit') );
		}
		
		global $current_user;
		 
		// Retrieve the settings from the file and convert the json object to an array.
		$settings = (array) json_decode( file_get_contents( $import_file ), true );
		foreach ($settings as $key => $value) {
			
			// First update user meta
			if($key === 'wp_edit_user_specific') {
				
				$value = (array) $value;
				update_user_meta($current_user->ID, 'aaa_wp_edit_user_meta', $value);
			}
			// Else update other options
			else {
				
				$value = (array) $value;
				update_option($key, $value);
			}
		}
		 
		// Redirect to database page with added parameter = true
		wp_safe_redirect( admin_url( 'admin.php?page=wp_edit_options&tab=database&import=true' ) ); 
		exit;
	}
	
	/*
	****************************************************************
	Before wp tinymce
	****************************************************************
	*/
	public function before_wp_tiny_mce() {
		
		// Add WP dashicons css file to editor
		echo '<link rel="stylesheet" type="text/css" href="'.plugins_url().'/wp-edit/css/tinymce_dashicons.css" />';
	}
	
	/*
	****************************************************************
	Tinymce before init
	****************************************************************
	*/
	public function wp_edit_tiny_mce_before_init($init) {
		
		// Initialize table ability
		if (isset($init['tools'])) {
			$init['tools'] = $init['tools'].',inserttable';
		} else {
			$init['tools'] = 'inserttable';
		}
		
		// Get editor default fontsize type value
		$opts_editor = get_option('wp_edit_editor');
		$default_editor_fontsize_type = isset($opts_editor['default_editor_fontsize_type']) ? $opts_editor['default_editor_fontsize_type'] : 'pt';
		
		// Pass values to editor initialization
		if($default_editor_fontsize_type === 'px') {
			
			$new_px = isset($opts_editor['default_editor_fontsize_values']) && !empty($opts_editor['default_editor_fontsize_values']) ? $opts_editor['default_editor_fontsize_values'] : '6px 8px 9px 10px 11px 12px 13px 14px 15px 16px 18px 20px 22px 24px 28px 32px 48px 72px';
			
			if(isset($init['fontsize_formats'])) {
				$init['fontsize_formats'] = $init['fontsize_formats'].' '.$new_px;
			} else {
				$init['fontsize_formats'] = $new_px;
			}
		}
		else if($default_editor_fontsize_type === 'pt') {
			
			$new_pt = isset($opts_editor['default_editor_fontsize_values']) && !empty($opts_editor['default_editor_fontsize_values']) ? $opts_editor['default_editor_fontsize_values'] : '6pt 8pt 10pt 12pt 14pt 16pt 18pt 20pt 22pt 24pt 26pt 28pt 30pt 32pt 34pt 36pt 48pt 72pt';
			
			if(isset($init['fontsize_formats'])) {
				$init['fontsize_formats'] = $init['fontsize_formats'].' '.$new_pt;
			} else {
				$init['fontsize_formats'] = $new_pt;
			}
		}
		else if($default_editor_fontsize_type === 'em') {
			
			$new_em = isset($opts_editor['default_editor_fontsize_values']) && !empty($opts_editor['default_editor_fontsize_values']) ? $opts_editor['default_editor_fontsize_values'] : '.8em 1em 1.2em 1.4em 1.6em 1.8em 2em';
			
			if(isset($init['fontsize_formats'])) {
				$init['fontsize_formats'] = $init['fontsize_formats'].' '.$new_em;
			} else {
				$init['fontsize_formats'] = $new_em;
			} 
		}
		else if($default_editor_fontsize_type === 'percent') {
			
			$new_percent = isset($opts_editor['default_editor_fontsize_values']) && !empty($opts_editor['default_editor_fontsize_values']) ? $opts_editor['default_editor_fontsize_values'] : '80% 90% 100% 110% 120%';
			
			if(isset($init['fontsize_formats'])) {
				$init['fontsize_formats'] = $init['fontsize_formats'].' '.$new_percent;
			} else {
				$init['fontsize_formats'] = $new_percent;
			}
		}
		
		/*
		****************************************************************
		Additional initalization if disable wpautop is true for the post
		****************************************************************
		*/
		// Get post id and meta
		$post_id = get_the_ID();
		$post_meta = get_post_meta($post_id);
		$dis_wpautop = isset($post_meta['_jwl_disable_wpautop']) && !empty($post_meta['_jwl_disable_wpautop']) ? $post_meta['_jwl_disable_wpautop'] : false;
		
		// Only initialize if the disable wpautop option is enabled in the post meta
		if ($dis_wpautop != false) {
			
			$init['wpautop'] = false;
			$init['indent'] = true;
			$init['wpep_noautop'] = true;
		}
		
		return $init;
	}
	
	/*
	****************************************************************
	Tinymce init
	****************************************************************
	*/
	public function wp_edit_init_tinymce() {
		
		
		$options_buttons = get_option( 'wp_edit_buttons', $this->global_options_buttons );
		$default_opts = $this->global_options_buttons;


		// Define plugin array of database options for comparison
		$new_array = '';
		foreach($options_buttons as $slot_array) {
			
			if(!empty($slot_array) && $slot_array != '') {  // Skip containter array if empty
				$new_array .= $slot_array.' ';  // Create single string of all default plugin buttons
			}
		}
		$new_array = rtrim($new_array, ' ');  // Remove trailing right space
		$new_plugin_array = explode(' ', $new_array);  // Explode at spaces to make single array (this is an array of all current plugin buttons)
		$this->new_plugin_array = $new_plugin_array;
		
		
		// Define plugin array of default buttons for comparison
		$default_array = '';
		foreach($default_opts as $slot_array) {
			
			if(!empty($slot_array) && $slot_array != '') {  // Skip containter array if empty
				$default_array .= $slot_array.' ';  // Create single string of all default plugin buttons
			}
		}
		$default_array = rtrim($default_array, ' ');  // Remove trailing right space
		$default_buttons_array = explode(' ', $default_array);  // Explode at spaces to make single array (this is an array of all current plugin buttons)
		$this->default_buttons_array = $default_buttons_array;
		
		
		// Get filtered plugin buttons array
		$filtered_plugin_buttons = array();
		$get_filters = $this->filtered_buttons;
		// If the array set is not empty (filters being applied)
		if(  ! empty( $get_filters ) ) {
			foreach( $get_filters as $key => $values ) {
				
				$filtered_plugin_buttons[] = $values['button_id'];
			}
		}
		$this->filtered_plugin_buttons = $filtered_plugin_buttons;
		
		
	
		// Build extra plugins array
		add_filter('mce_external_plugins', array($this, 'wp_edit_mce_external_plugins'));
		
		// Get options and set appropriate tinymce toolbars
		foreach ((array)$options_buttons as $key => $value) {
			
			// Magic is happening right here...
			if($key == 'tmce_container') { return; }
			if($key == 'toolbar1') { add_filter('mce_buttons', array($this, 'wp_edit_add_mce')); }
			if($key == 'toolbar2') { add_filter('mce_buttons_2', array($this, 'wp_edit_add_mce_2')); }
		}
	}
	
	/*
	****************************************************************
	Tinymce external plugins
	****************************************************************
	*/
	public function wp_edit_mce_external_plugins($plugins) {
		
		$options_buttons = get_option('wp_edit_buttons');
		
		// Build array of all button names found in active toolbars
		$final_options = array();
		$final_options = array_merge(explode(' ', $options_buttons['toolbar1']), explode(' ', $options_buttons['toolbar2']));
		
		$plugins['table'] = plugins_url() . '/wp-edit/plugins/table/plugin.min.js';
		
		if(in_array('ltr', $final_options) || in_array('rtl', $final_options)) {
			$plugins['directionality'] = plugins_url() . '/wp-edit/plugins/directionality/plugin.min.js';
		}
		if(in_array('anchor', $final_options)) {
			$plugins['anchor'] = plugins_url() . '/wp-edit/plugins/anchor/plugin.min.js';
		}
		if(in_array('code', $final_options)) {
			$plugins['code'] = plugins_url() . '/wp-edit/plugins/code/plugin.min.js';
		}
		if(in_array('emoticons', $final_options)) {
			$plugins['emoticons'] = plugins_url() . '/wp-edit/plugins/emoticons/plugin.min.js';
		}
		if(in_array('hr', $final_options)) {
			$plugins['hr'] = plugins_url() . '/wp-edit/plugins/hr/plugin.min.js';
		}
		if(in_array('inserttime', $final_options)) {
			$plugins['insertdatetime'] = plugins_url() . '/wp-edit/plugins/insertdatetime/plugin.min.js';
		}
		if(in_array('preview', $final_options)) {
			$plugins['preview'] = plugins_url() . '/wp-edit/plugins/preview/plugin.min.js';
		}
		if(in_array('print', $final_options)) {
			$plugins['print'] = plugins_url() . '/wp-edit/plugins/print/plugin.min.js';
		}
		if(in_array('searchreplace', $final_options)) {
			$plugins['searchreplace'] = plugins_url() . '/wp-edit/plugins/searchreplace/plugin.min.js';
		}
		if(in_array('visualblocks', $final_options)) {
			$plugins['visualblocks'] = plugins_url() . '/wp-edit/plugins/visualblocks/plugin.min.js';
		}
		if(in_array('image_orig', $final_options)) {
			$plugins['image_orig'] = plugins_url() . '/wp-edit/plugins/image_orig/plugin.min.js';
		}
		if(in_array('advlink', $final_options)) {
			$plugins['advlink'] = plugins_url() . '/wp-edit/plugins/advlink/plugin.js';
		}
		if(in_array('acheck', $final_options)) {
			$plugins['acheck'] = plugins_url() . '/wp-edit/plugins/acheck/plugin.js';
		}
		if(in_array('abbr', $final_options)) {
			$plugins['abbr'] = plugins_url() . '/wp-edit/plugins/abbr/plugin.js';
		}
		if(in_array('columnShortcodes', $final_options)) {
			$plugins['columnShortcodes'] = plugins_url() . '/wp-edit/plugins/columnShortcodes/plugin.js';
		}
		if(in_array('nonbreaking', $final_options)) {
			$plugins['nonbreaking'] = plugins_url() . '/wp-edit/plugins/nonbreaking/plugin.min.js';
		}
		if(in_array('eqneditor', $final_options)) {
			$plugins['eqneditor'] = plugins_url() . '/wp-edit/plugins/eqneditor/plugin.min.js';
		}
		
		//*** Tinymce filter if disable wpautop is true for the post ***//
		// Get post id and meta
		$post_id = get_the_ID();
		$post_meta = get_post_meta($post_id);
		$dis_wpautop = isset($post_meta['_jwl_disable_wpautop']) && !empty($post_meta['_jwl_disable_wpautop']) ? $post_meta['_jwl_disable_wpautop'] : false;
		
		// Only filter if the disable wpautop option is enabled in the post meta
		if ($dis_wpautop != false) {
			
			// Custom editor code to process content html
			$plugins['wpep_noautop'] = plugins_url() . '/wp-edit/plugins/wpep_noautop/plugin.js';
		}
		
		return $plugins;
	}
	
	/*
	****************************************************************
	Tinymce mce buttons
	****************************************************************
	*/
	public function wp_edit_add_mce($buttons) {
		
		$options = get_option('wp_edit_buttons');
		$options_toolbar1 = $options['toolbar1'];
		$default_wp_array_toolbar1 = array('bold','italic','strikethrough','bullist','numlist','blockquote','hr','alignleft','aligncenter','alignright','link','unlink','wp_more');
		$array_back = array();
		
		$new_plugin_array = $this->new_plugin_array;
		$default_buttons_array = $this->default_buttons_array;
		$filtered_plugin_buttons = $this->filtered_plugin_buttons;
		
		// First, we explode the toolbar in the database
		$options_toolbar1 = explode(' ', $options_toolbar1);
		
		// Next, we get the difference between ($options['toolbar1']) and ($buttons)
		$array_diff = array_diff($buttons, $options_toolbar1);
		
		// Now, we take the array and loop it to find original buttons
		if($array_diff) {
			
			foreach($array_diff as $array) {
				
				// If the button is NOT in the original array (WP buttons), we know it is another plugin or theme button..
				if( !in_array( $array, $default_wp_array_toolbar1 ) && !in_array( $array, $new_plugin_array ) ) {
					
					// Create the new array of additional buttons to pass back to end of toolbar
					$array_back[] = $array;
				}
			}
		}
		
		// Loop each saved toolbar button
		foreach( $options_toolbar1 as $key => $value ) {
			
			// If button is not a default button (it is a filtered button); and not in filtered plugin buttons (the button was removed when plugin deactivated)
			if( !in_array( $value, $default_buttons_array ) && !in_array( $value, $filtered_plugin_buttons ) ) { 
			
				unset( $options_toolbar1[$key]);
			}
		}
		
		// Merge the difference onto the end of our saved buttons
		$merge_buttons = array_merge($options_toolbar1, $array_back);
		
		return $merge_buttons;
	}
	public function wp_edit_add_mce_2($buttons) {
	
		$options = get_option('wp_edit_buttons');
		$options_toolbar2 = $options['toolbar2'];
		$default_wp_array_toolbar2 = array('formatselect','underline','alignjustify','forecolor','pastetext','removeformat','charmap','outdent','indent','undo','redo','wp_help');
		$array_back = array();
		
		$new_plugin_array = $this->new_plugin_array;
		$default_buttons_array = $this->default_buttons_array;
		$filtered_plugin_buttons = $this->filtered_plugin_buttons;
		
		// First, we explode the toolbar in the database
		$options_toolbar2 = explode(' ', $options_toolbar2);
		
		// Next, we get the difference between ($options['toolbar1']) and ($buttons)
		$array_diff = array_diff($buttons, $options_toolbar2);
		
		// Now, we take the array and loop it to find original buttons
		if($array_diff) {
			
			foreach($array_diff as $array) {
				
				// If the button is NOT in the original array (WP buttons), we know it is another plugin or theme button..
				if( !in_array( $array, $default_wp_array_toolbar2 ) && !in_array( $array, $new_plugin_array ) ) {
					
					// Create the new array of additional buttons to pass back to end of toolbar
					$array_back[] = $array;
				}
			}
		}
		
		// Loop each saved toolbar button
		foreach( $options_toolbar2 as $key => $value ) {
			
			// If button is not a default button (it is a filtered button); and not in filtered plugin buttons (the button was removed when plugin deactivated)
			if( !in_array( $value, $default_buttons_array ) && !in_array( $value, $filtered_plugin_buttons ) ) { 
			
				unset( $options_toolbar2[$key]);
			}
		}
		
		// Merge the difference onto the end of our saved buttons
		$merge_buttons = array_merge($options_toolbar2, $array_back);
		
		return $merge_buttons;
	}
	
	public function htlmedit_pre($content) {
		
		// Get post id and meta
		$post_id = get_the_ID();
		$post_meta = get_post_meta($post_id);
		$dis_wpautop = isset($post_meta['_jwl_disable_wpautop']) && !empty($post_meta['_jwl_disable_wpautop']) ? $post_meta['_jwl_disable_wpautop'] : false;
		
		// Only filter if the disable wpautop option is enabled in the post meta
		if ($dis_wpautop != false) {
			
			$content = str_replace( array('&amp;', '&lt;', '&gt;'), array('&', '<', '>'), $content );
			$content = wpautop( $content );
			$content = preg_replace( '/^<p>(https?:\/\/[^<> "]+?)<\/p>$/im', '$1', $content );
			$content = htmlspecialchars( $content, ENT_NOQUOTES, get_option( 'blog_charset' ) );
		}
		return $content;
	}
	
	/*
	****************************************************************
	Plugin update message
	****************************************************************
	*/
	public function wpedit_plugin_update_cb($plugin_data, $r) {
	
		$admin_email = get_option('admin_email');
		
		echo '<br /><br />';
		echo '<div style="border:1px solid black;border-radius:10px;">';
		
			echo '<div style="width:30%;padding:10px;float:left;">';
				echo '<h3>'; _e('Stay Informed', 'wp-edit'); echo '</h3>';
				_e('Signup to our free <a target="_blank" href="http://www.feedblitz.com/f/?Sub=950320">Feedblitz</a> service; to receive important plugin news, updates and discount offers for our Pro version.', 'wp-edit');
				echo '<br /><br />';
				echo 'Email:<br /><input id="wpedit_feedblitz_signup_email" name="EMAIL" type="text" value="'.$admin_email.'" style="width:50%;margin-right:10px;" /><input id="wpedit_feedblitz_signup" type="button" value="Subscribe me! &raquo;" class="button-primary" />';
			echo '</div>';
			
			echo '<div style="width:30%;padding:10px;float:left;margin-left:20px;">';
				echo '<h3>'; _e('Other Plugin News', 'wp-edit'); echo '</h3>';
				_e('* Plugin documentation is being added to our <a target="_blank" href="http://learn.wpeditpro.com">Knowledge Base</a>. Check back frequently for more tutorial articles.', 'wp-edit');
			echo '</div>';
			
			echo '<div style="clear:both;"></div>';
		echo '</div>';
	}
	public function wpedit_plugin_update_js() {
		
		global $pagenow;
		if($pagenow == 'plugins.php') {
			
			echo "<script language='javascript'>
					jQuery(document).ready(function($) {
						
						$('#wpedit_feedblitz_signup').click(function() {
							
							feed_email = $('#wpedit_feedblitz_signup_email').val();
							window.open('http://www.feedblitz.com/f/?Sub=950320&Email='+feed_email);
						});
					});
				</script>";
		}
	}
	
}
$wp_edit_class = new wp_edit_class();



/*
****************************************************************
Include Plugin Functions
****************************************************************
*/
include 'includes/functions.php';


/*
****************************************************************
Include functions for running predefined styles
****************************************************************
*/
include 'includes/style_formats.php';


/*
****************************************************************
Pointers Class
****************************************************************	
*/
class wpe_admin_pointers {
    
    public function __construct() {
        
        add_action('admin_enqueue_scripts', array($this, 'custom_admin_pointers_header'));
    }

    public function custom_admin_pointers_header() {
        
       if ($this->custom_admin_pointers_check()) {
           
          add_action('admin_print_footer_scripts', array($this, 'custom_admin_pointers_footer'));

          wp_enqueue_script('wp-pointer');
          wp_enqueue_style('wp-pointer');
       }
    }

    public function custom_admin_pointers_check() {
        
       $admin_pointers = $this->custom_admin_pointers();
       foreach ( $admin_pointers as $pointer => $array ) {
          if ( $array['active'] )
             return true;
       }
    }

    public function custom_admin_pointers_footer() {
        
       $admin_pointers = $this->custom_admin_pointers();
       ?>
        <script type="text/javascript">
        /* <![CDATA[ */
        ( function($) {
           <?php
           foreach ( $admin_pointers as $pointer => $array ) {
              if ( $array['active'] ) {
                 ?>
                 $('<?php echo $array['anchor_id']; ?>').pointer({
                    content: '<?php echo $array['content']; ?>',
                    position: {
                       edge: '<?php echo $array['edge']; ?>',
                       align: '<?php echo $array['align']; ?>'
                    },
                    close: function() {
                       $.post(ajaxurl, {
                          pointer: '<?php echo $pointer; ?>',
                          action: 'dismiss-wp-pointer'
                       });
                    }
                 }).pointer('open');
                 <?php
              }
           }
           ?>
        } )(jQuery);
        /* ]]> */
        </script>
       <?php
    }

    public function custom_admin_pointers() {
        
       $dismissed = explode( ',', (string) get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ) );
       $version = '1_0'; // replace all periods in 1.0 with an underscore
       $prefix = 'wpe_admin_pointers_' . $version . '_';

       $new_pointer_content = '<h3>' . __( 'WP Edit Tip' ) . '</h3>';
       $new_pointer_content .= '<p>' . __( 'If only one row of buttons is visible; try clicking the <a target="_blank" href="http://learn.wpeditpro.com/wordpress-tinymce-editor/#ipt_kb_toc_73_6">"Toolbar Toggle"</a> button to expand/collapse additional editor button rows.' ) . '</p>';

       return array(
          $prefix . 'toggle_toolbar' => array(
             'content' => $new_pointer_content,
             'anchor_id' => '#wp-content-editor-container',
             'edge' => 'bottom',
             'align' => 'top',
             'active' => ( ! in_array( $prefix . 'toggle_toolbar', $dismissed ) )
          )
       );
    }
}
//Initiate admin pointers
$wpe_admin_pointers = new wpe_admin_pointers();

?>