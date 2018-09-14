<?php
if(!class_exists('Cyclone_Slider')):

class Cyclone_Slider {
	var $plugin_path;
	var $plugin_url;
	var $template_slide_box;
	var $template_slide_box_js;
	var $template_slider_dir;
	var $slider_count;
	var $effects;
	/*--------------------------------------------*
	 * Constructor
	 *--------------------------------------------*/
	
	/**
	 * Initializes the plugin by setting localization, filters, and administration functions.
	 */
	function __construct() {
		// Paths and urls init
		$ds = DIRECTORY_SEPARATOR;
		$this->plugin_path = CYCLONE_PATH;
		$this->plugin_url = CYCLONE_URL;//url to plugin
		$this->template_slide_box = $this->plugin_path . 'inc'.$ds.'admin'.$ds.'box.php';
		$this->template_slide_box_js = $this->plugin_path . 'inc'.$ds.'admin'.$ds.'box-js.php';
		$this->template_slider_dir = $this->plugin_path . 'templates'.$ds;
		$this->slider_count = 0;
		$this->effects = array(
			'fade'=>'Fade',
			'blindX'=>'Blind X',
			'blindY'=>'Blind Y',
			'blindZ'=>'Blind Z',
			'cover'=>'Cover',
			'curtainX'=>'Curtain X',
			'curtainY'=>'Curtain Y',
			'fadeZoom'=>'Fade Zoom',
			'growX'=>'Grow X',
			'growY'=>'Grow Y',
			'none'=>'None',
			'scrollUp'=>'Scroll Up',
			'scrollDown'=>'Scroll Down',
			'scrollLeft'=>'Scroll Left',
			'scrollRight'=>'Scroll Right',
			'scrollHorz'=>'Scroll Horizontally',
			'scrollVert'=>'Scroll Vertically',
			'shuffle'=>'Shuffle',
			'slideX'=>'Slide X',
			'slideY'=>'Slide Y',
			'toss'=>'Toss',
			'turnUp'=>'Turn Up',
			'turnDown'=>'Turn Down',
			'turnLeft'=>'Turn Left',
			'turnRight'=>'Turn Right',
			'uncover'=>'Uncover',
			'wipe'=>'Wipe',
			'zoom'=>'Zoom'
		);
		
		load_plugin_textdomain( 'cycloneslider', false, '/lang' );
		
		// Register admin styles and scripts
		add_action( 'admin_enqueue_scripts', array( &$this, 'register_admin_scripts' ), 10);
	
		// Register frontend styles and scripts
		add_action( 'wp_enqueue_scripts', array( &$this, 'register_plugin_scripts' ), 100 );
		
		
		// Add admin menus
		add_action( 'init', array( &$this, 'create_post_types' ) );
		
		// Update the messages for our custom post make it appropriate for slideshow
		add_filter('post_updated_messages', array( &$this, 'post_updated_messages' ) );
		
		// Add slider metaboxes
		add_action( 'add_meta_boxes', array( &$this, 'add_meta_boxes' ) );
		
		// Save slides
		add_action( 'save_post', array( &$this, 'save_post' ) );
		
		// Hacky way to change text in thickbox
		add_filter( 'gettext', array( $this, 'replace_text_in_thickbox' ), 10, 3 );
		
		// Modify html of image
		add_filter( 'image_send_to_editor', array( $this, 'image_send_to_editor'), 1, 8 );
		
		// Custom columns
		add_action( 'manage_cycloneslider_posts_custom_column', array( $this, 'custom_column' ), 10, 2);
		add_filter( 'manage_edit-cycloneslider_columns', array( $this, 'slideshow_columns') );
		
		// Add hook for admin footer
		add_action('admin_footer', array( $this, 'admin_footer') );
		
		// Our shortcode
		add_shortcode('cycloneslider', array( $this, 'cycloneslider_shortcode') );
		
		// Add query var for so we can access our css via www.mysite.com/?cyclone_templates_css=1
		add_filter('query_vars', array( $this, 'modify_query_vars'));

		// The magic that shows our css
		add_action('template_redirect', array( $this, 'cyclone_css_hook'));
		
	} // end constructor

	/**
	 * Registers and enqueues admin-specific JavaScript.
	 */	
	public function register_admin_scripts() {
		global $wp_version;
		
		$use_new_media = false;
		if ( version_compare( $wp_version, '3.5', '>=' ) ) { // Use new media manager
			$use_new_media = true;
		}
			
		if('cycloneslider' == get_post_type()){ /* Load only scripts here and not on all admin pages */
			// Styles
			if ( $use_new_media == false ) { // Use old media manager < 3.5
				wp_enqueue_style('thickbox');
			}
			
			wp_register_style( 'cycloneslider-admin-styles', $this->plugin_url.'css/admin.css', array(), CYCLONE_VERSION  );
			wp_enqueue_style( 'cycloneslider-admin-styles' );
			
			// Required media files for new media manager. Since WP 3.5+
			if ( $use_new_media ) {
				wp_enqueue_media();
			}
			
			// Scripts
			wp_dequeue_script( 'autosave' );//disable autosave
			
			wp_enqueue_script('jquery');
			wp_enqueue_script('jquery-ui-sortable');
			
			if ( $use_new_media == false ) { // Use old media manager < 3.5
				wp_enqueue_script('media-upload');
				wp_enqueue_script('thickbox');
			}
			
			wp_register_script( 'jquery-cookie', $this->plugin_url.'js/jquery-cookie.js', array('jquery'), CYCLONE_VERSION );
			wp_enqueue_script( 'jquery-cookie' );

			// Allow translation to script texts
			
			wp_register_script( 'cycloneslider-admin-script', $this->plugin_url.'js/admin.js', array('jquery'), CYCLONE_VERSION  );
			wp_localize_script( 'cycloneslider-admin-script', 'cycloneslider_admin_vars',
				array(
					'title'     => __( 'Select an image', 'cycloneslider' ), // This will be used as the default title
					'button'    => __( 'Add to Slide', 'cycloneslider' ),
					'use_new_media' => $use_new_media
				)
			);
			wp_enqueue_script( 'cycloneslider-admin-script');
			
		}
	}
	
	/**
	 * Registers and enqueues frontend-specific scripts.
	 */
	public function register_plugin_scripts() {
		/*** Styles ***/
		$cyclone_css = add_query_arg(array('cyclone_templates_css' => 1), home_url( '/' ));
		wp_register_style( 'cyclone-slider-plugin-styles', $cyclone_css );//contains our combined css from ALL templates
		wp_enqueue_style( 'cyclone-slider-plugin-styles' );
		
		/*** Scripts ***/
		wp_register_script( 'cycle', $this->plugin_url.'js/jquery.cycle.all.min.js', array('jquery') );
		wp_enqueue_script( 'cycle' );
		
		
	}
	
	/*--------------------------------------------*
	 * Core Functions
	 *---------------------------------------------*/
	// Create custom post for slideshows
	function create_post_types() {
		register_post_type( 'cycloneslider',
			array(
				'labels' => array(
					'name' => __('Cyclone Slider', 'cycloneslider'),
					'singular_name' => __('Slideshow', 'cycloneslider'),
					'add_new' => __('Add Slideshow', 'cycloneslider'),
					'add_new_item' => __('Add New Slideshow', 'cycloneslider'),
					'edit_item' => __('Edit Slideshow', 'cycloneslider'),
					'new_item' => __('New Slideshow', 'cycloneslider'),
					'view_item' => __('View Slideshow', 'cycloneslider'),
					'search_items' => __('Search Slideshows', 'cycloneslider'),
					'not_found' => __('No slideshows found', 'cycloneslider'),
					'not_found_in_trash' => __('No slideshows found in Trash', 'cycloneslider')
				),
				'supports' => array('title'),
				'public' => false,
				'exclude_from_search' => true,
				'show_ui' => true,
				'menu_position' => 100
			)
		);
	}
	
	// Messages
	function post_updated_messages($messages){
		global $post, $post_ID;
		$messages['cycloneslider'] = array(
			0  => '',
			1  => sprintf( __( 'Slideshow updated. Shortcode is [cycloneslider id="%s"]', 'cycloneslider' ), $post->post_name),
			2  => __( 'Custom field updated.', 'cycloneslider' ),
			3  => __( 'Custom field deleted.', 'cycloneslider' ),
			4  => __( 'Slideshow updated.', 'cycloneslider' ),
			5  => __( 'Slideshow updated.', 'cycloneslider' ),
			6  => sprintf( __( 'Slideshow published. Shortcode is [cycloneslider id="%s"]', 'cycloneslider' ), $post->post_name),
			7  => __( 'Slideshow saved.', 'cycloneslider' ),
			8  => __( 'Slideshow updated.', 'cycloneslider' ),
			9  => __( 'Slideshow updated.', 'cycloneslider' ),
			10 => __( 'Slideshow updated.', 'cycloneslider' )
		);
		return $messages;
	}
	
	// Slides metabox init
	function add_meta_boxes(){
		add_meta_box(
			'cyclone-slides-metabox',
			__('Slides', 'cycloneslider'),
			array( &$this, 'render_slides_meta_box' ),
			'cycloneslider' ,
			'normal',
			'high'
		);
		add_meta_box(
			'cyclone-slider-properties-metabox',
			__('Slider Settings', 'cycloneslider'),
			array( &$this, 'render_slider_properties_meta_box' ),
			'cycloneslider' ,
			'side',
			'low'
		);
		add_meta_box(
			'cyclone-slider-templates-metabox',
			__('Slider Templates', 'cycloneslider'),
			array( &$this, 'render_slider_templates_meta_box' ),
			'cycloneslider' ,
			'normal',
			'low'
		);
	}
	
	// Get Image mime type. @param $image - full path to image
	function get_mime_type( $image ){
		if($properties = getimagesize( $image )){
			return $properties['mime'];
		}
		return false;
	}
	
	// Slides metabox render
	function render_slides_meta_box($post){
		
		// Use nonce for verification
		echo '<input type="hidden" name="cycloneslider_metabox_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';
		
		$meta = get_post_custom($post->ID);

		$slider_settings = $this->get_slider_admin_settings($meta);
		$slider_metas = $this->get_slider_metas($meta);

		$debug = 0;
		if($debug){
		echo '<pre>';
		print_r($meta);
		print_r($slider_settings);
		print_r($slider_metas);
		echo '</pre>';
		}
		?>
		<div class="cycloneslider-sortable">
			<?php
			if(is_array($slider_metas) and count($slider_metas)>0):
			$slider_metas = apply_filters('cycloneslider_metas', $slider_metas);
			$defaults = array(
				'id' => 0,
				'link' =>  '',
				'title' => '',
				'description' => '',
				'link_target' => '_self',
				'fx' => 'default',
				'speed' => '',
				'timeout' => '',
				'type' => 'image'
			);
			foreach($slider_metas as $i=>$slider_meta):
				$slider_meta = wp_parse_args($slider_meta, $defaults);
				$attachment_id = (int) $slider_meta['id'];
				$image_url = wp_get_attachment_image_src( $attachment_id, 'medium', true );
				$image_url = (is_array($image_url)) ? $image_url[0] : '';
				$image_url = apply_filters('cycloneslider_preview_url', $image_url, $slider_meta);
				$box_title = apply_filters('cycloneslider_box_title', __('Slide', 'cycloneslider'), $slider_meta);
				
				include($this->template_slide_box);
			endforeach;
			endif;
			?>
		</div><!-- end .cycloneslider-sortable -->
		
		<input type="button" value="<?php _e('Add Slide', 'cycloneslider'); ?>" class="button-secondary" name="cycloneslider_add_slide" />
		<?php
	}
	
	function render_slider_properties_meta_box($post){
		// Use nonce for verification
		echo '<input type="hidden" name="cycloneslider_metabox_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';

		$meta = get_post_custom($post->ID);
		$slider_settings = $this->get_slider_admin_settings($meta);
		//default values
		$slider_settings['timeout'] = (!isset($slider_settings['timeout']) || $slider_settings['timeout']===null) ? 4000 : $slider_settings['timeout'];
		$slider_settings['speed'] = (!isset($slider_settings['speed']) || $slider_settings['speed']===null) ? 1000 : $slider_settings['speed'];
		$slider_settings['width'] = (!isset($slider_settings['width']) || $slider_settings['width']===null) ? 960 : $slider_settings['width'];
		$slider_settings['height'] = (!isset($slider_settings['height']) || $slider_settings['height']===null) ? 300 : $slider_settings['height'];
		$slider_settings['show_prev_next'] = (!isset($slider_settings['show_prev_next']) || $slider_settings['show_prev_next']===null) ? 1 : $slider_settings['show_prev_next'];
		$slider_settings['show_nav'] = (!isset($slider_settings['show_nav']) || $slider_settings['show_nav']===null) ? 1 : $slider_settings['show_nav'];
		$slider_settings['hover_pause'] = (!isset($slider_settings['hover_pause']) || $slider_settings['hover_pause']===null) ? 1 : $slider_settings['hover_pause'];
		
		?>
		<div class="cycloneslider-field">
			<label for="cycloneslider_settings_fx"><?php _e('Transition Effects to Use:', 'cycloneslider'); ?></label>
			<select id="cycloneslider_settings_fx" name="cycloneslider_settings[fx]">
			<?php foreach($this->effects as $key=>$fx): ?>
			<option <?php echo (isset($slider_settings['fx']) && $key==$slider_settings['fx']) ? 'selected="selected"' : ''; ?> value="<?php echo esc_attr($key); ?>"><?php echo esc_attr($fx); ?></option>
			<?php endforeach; ?>
			</select>
			<div class="clear"></div>
		</div>
		<?php
		?>
		<div class="cycloneslider-field">
			<label for="cycloneslider_settings_timeout"><?php _e('Next Slide Delay:', 'cycloneslider'); ?> </label>
			<input id="cycloneslider_settings_timeout" type="text" name="cycloneslider_settings[timeout]" value="<?php echo esc_attr($slider_settings['timeout']); ?>" />
			<span class="note"><?php _e('Milliseconds. 0 to disable auto advance.', 'cycloneslider'); ?></span>
			<div class="clear"></div>
		</div>
		<div class="cycloneslider-field">
			<label for="cycloneslider_settings_speed"><?php _e('Transition Effects Speed:', 'cycloneslider'); ?></label>
			<input id="cycloneslider_settings_speed" type="text" name="cycloneslider_settings[speed]" value="<?php echo esc_attr($slider_settings['speed']); ?>" />
			<span class="note"><?php _e('Milliseconds', 'cycloneslider'); ?></span>
			<div class="clear"></div>
		</div>
		<div class="cycloneslider-field">
			<label for="cycloneslider_settings_width"><?php _e('Width:', 'cycloneslider'); ?> </label>
			<input id="cycloneslider_settings_width" type="text" name="cycloneslider_settings[width]" value="<?php echo esc_attr($slider_settings['width']); ?>" />
			<span class="note"><?php _e('pixels.', 'cycloneslider'); ?></span>
			<div class="clear"></div>
		</div>
		<div class="cycloneslider-field">
			<label for="cycloneslider_settings_height"><?php _e('Height:', 'cycloneslider'); ?> </label>
			<input id="cycloneslider_settings_height" type="text" name="cycloneslider_settings[height]" value="<?php echo esc_attr($slider_settings['height']); ?>" />
			<span class="note"><?php _e('pixels.', 'cycloneslider'); ?></span>
			<div class="clear"></div>
		</div>
		<div class="cycloneslider-field">
			<label for="cycloneslider_settings_hover_pause"><?php _e('Pause on Hover?', 'cycloneslider'); ?></label>
			<select id="cycloneslider_settings_hover_pause" name="cycloneslider_settings[hover_pause]">
				<option <?php echo ('true'==$slider_settings['hover_pause']) ? 'selected="selected"' : ''; ?> value="true"><?php _e('Yes', 'cycloneslider'); ?></option>
				<option <?php echo ('false'==$slider_settings['hover_pause']) ? 'selected="selected"' : ''; ?> value="false"><?php _e('No', 'cycloneslider'); ?></option>
			</select>
			<div class="clear"></div>
		</div>
		<div class="cycloneslider-field">
			<label for="cycloneslider_settings_show_prev_next"><?php _e('Show Prev/Next Buttons?', 'cycloneslider'); ?></label>
			<select id="cycloneslider_settings_show_prev_next" name="cycloneslider_settings[show_prev_next]">
				<option <?php echo (1==$slider_settings['show_prev_next']) ? 'selected="selected"' : ''; ?> value="1"><?php _e('Yes', 'cycloneslider'); ?></option>
				<option <?php echo (0==$slider_settings['show_prev_next']) ? 'selected="selected"' : ''; ?> value="0"><?php _e('No', 'cycloneslider'); ?></option>
			</select>
			<div class="clear"></div>
		</div>
		<div class="cycloneslider-field last">
			<label for="cycloneslider_settings_show_nav"><?php _e('Show Navigation?', 'cycloneslider'); ?></label>
			<select id="cycloneslider_settings_show_nav" name="cycloneslider_settings[show_nav]">
				<option <?php echo (1==$slider_settings['show_nav']) ? 'selected="selected"' : ''; ?> value="1"><?php _e('Yes', 'cycloneslider'); ?></option>
				<option <?php echo (0==$slider_settings['show_nav']) ? 'selected="selected"' : ''; ?> value="0"><?php _e('No', 'cycloneslider'); ?></option>
			</select>
			<div class="clear"></div>
		</div>
		<?php
	}
	
	function render_slider_templates_meta_box($post){
		// Use nonce for verification
		echo '<input type="hidden" name="cycloneslider_metabox_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';

		$meta = get_post_custom($post->ID);
		$slider_settings = $this->get_slider_admin_settings($meta);
		//default values
		$slider_settings['template'] = ($slider_settings['template']===null) ? 'default' : $slider_settings['template'];
		
		$templates = $this->get_all_templates();
		$template_options = array();
		$template_options['default'] = 'Default';
		foreach($templates as $name=>$template){
			if($name!='default'){
				$template_options[$name]= ucwords(str_replace('-',' ',$name));
			}
		}
		?>
		<div class="cycloneslider-field last">
			<label for="cycloneslider_settings_template"><?php _e('Select Template to Use:', 'cycloneslider'); ?></label>
			<select id="cycloneslider_settings_template" name="cycloneslider_settings[template]">
			<?php foreach($template_options as $key=>$name): ?>
			<option <?php echo ($key==$slider_settings['template']) ? 'selected="selected"' : ''; ?> value="<?php echo esc_attr($key); ?>"><?php echo esc_attr($name); ?></option>
			<?php endforeach; ?>
			</select>
			<div class="clear"></div>
		</div>
		<?php
	}
	
	function save_post($post_id){

		// Verify nonce
		$nonce_name = 'cycloneslider_metabox_nonce';
		if (!empty($_POST[$nonce_name])) {
			if (!wp_verify_nonce($_POST[$nonce_name], basename(__FILE__))) {
				return $post_id;
			}
		} else {
			return $post_id; // Make sure we cancel on missing nonce!
		}
		
		// check autosave
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		    return $post_id;
		}
		
		do_action('cycloneslider_before_save', $post_id);
		
		//slide metas
		$this->save_metas($post_id);
		
		//settings
		$this->save_settings($post_id);
		
		
		remove_action( 'save_post', array( &$this, 'save_post' ) );
	}
	
	//sanitize and save
	function save_metas($post_id){
		
		if(isset($_POST['cycloneslider_metas'])){
			
			$slides = array();
			$i=0;//always start from 0
			foreach($_POST['cycloneslider_metas'] as $slide){
				
				$slides[$i]['id'] = (int) $slide['id'];
				$slides[$i]['link'] = esc_url_raw($slide['link']);
				$slides[$i]['title'] = sanitize_text_field($slide['title']);
				$slides[$i]['description'] = wp_kses_post($slide['description']);
				$slides[$i]['link_target'] = wp_kses_post($slide['link_target']);
				$slides[$i]['fx'] = wp_kses_post($slide['fx']);
				$slides[$i]['speed'] = sanitize_text_field($slide['speed']);
				$slides[$i]['timeout'] = sanitize_text_field($slide['timeout']);
				$slides[$i]['type'] = sanitize_text_field($slide['type']);
				$i++;
			}
			
			$slides = apply_filters('cycloneslider_slides', $slides); //do filter before saving
			
			delete_post_meta($post_id, '_cycloneslider_metas');
			update_post_meta($post_id, '_cycloneslider_metas', $slides);
		}
	}
	
	//sanitize and save 
	function save_settings($post_id){
		if(isset($_POST['cycloneslider_settings'])){
			$settings = array();
			$settings['template'] = wp_filter_nohtml_kses($_POST['cycloneslider_settings']['template']);
			$settings['fx'] = wp_filter_nohtml_kses($_POST['cycloneslider_settings']['fx']);
			$settings['timeout'] = (int) wp_filter_nohtml_kses($_POST['cycloneslider_settings']['timeout']);
			$settings['speed'] = (int) wp_filter_nohtml_kses($_POST['cycloneslider_settings']['speed']);
			$settings['width'] = (int) wp_filter_nohtml_kses($_POST['cycloneslider_settings']['width']);
			$settings['height'] = (int) wp_filter_nohtml_kses($_POST['cycloneslider_settings']['height']);
			$settings['hover_pause'] = wp_filter_nohtml_kses($_POST['cycloneslider_settings']['hover_pause']);
			$settings['show_prev_next'] = (int) wp_filter_nohtml_kses($_POST['cycloneslider_settings']['show_prev_next']);
			$settings['show_nav'] = (int) wp_filter_nohtml_kses($_POST['cycloneslider_settings']['show_nav']);
			
			$settings = apply_filters('cycloneslider_settings', $settings); //do filter before saving
			
			delete_post_meta($post_id, '_cycloneslider_settings');
			update_post_meta($post_id, '_cycloneslider_settings', $settings);
		}
	}
	
	function replace_text_in_thickbox($translation, $text, $domain ) {
		$http_referrer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
		$req_referrer = isset($_REQUEST['referer']) ? $_REQUEST['referer'] : '';
		if(strpos($http_referrer, 'cycloneslider')!==false or $req_referrer=='cycloneslider') {
			if ( 'default' == $domain and 'Insert into Post' == $text )
			{
				return 'Add to Slide';
			}
		}
		return $translation;
	}
	
	// Add attachment ID as html5 data attr in thickbox
	function image_send_to_editor( $html, $id, $caption, $title, $align, $url, $size, $alt = '' ){
		if(strpos($html, '<img data-id="')===false){
			$html = str_replace('<img', '<img data-id="'.$id.'" ', $html);
		}
		return $html;
	}
	
	// Modify columns
	function slideshow_columns($columns) {
		$columns = array();
		$columns['title']= __('Slideshow Name', 'cycloneslider');
		$columns['id']= __('Slideshow ID', 'cycloneslider');
		$columns['shortcode']= __('Shortcode', 'cycloneslider');
		return $columns;
	}
	
	// Add content to custom columns
	function custom_column( $column_name, $post_id ){
		if ($column_name == 'id') {
			$post = get_post($post_id);
			echo $post->post_name;
		}
		if ($column_name == 'shortcode') {  
			$post = get_post($post_id);
			echo '[cycloneslider id="'.$post->post_name.'"]';
		}  
	}
	
	// For js adding of box
	function admin_footer() {
		if(get_post_type()=='cycloneslider'){
			?>
			<div class="cycloneslider-box-template">
				<?php
				include_once($this->template_slide_box_js);
				?>
			</div><!-- end .cycloneslider-box-template -->
		<?php
		}
	}
	
	// Compare the value from admin and shortcode. If shortcode value is present and not empty, use it, otherwise return admin value
	function get_comp_slider_setting($admin_val, $shortcode_val){
		if($shortcode_val!==null){//make sure its really null and not just int zero 0
			return $shortcode_val;
		}
		return $admin_val;
	}
	
	/* Our shortcode function.
	  Slider settings comes from both admin settings and shortcode attributes.
	  Shortcode attributes, if present, will override the admin settings.
	*/
	function cycloneslider_shortcode($shortcode_settings) {
		// Process shortcode settings and return only allowed vars
		$shortcode_settings = shortcode_atts(
			array(
				'id' => 0,
				'template' => null,
				'fx' => null,
				'speed' => null,
				'timeout' => null,
				'width' => null,
				'height' => null,
				'hover_pause' => null,
				'show_prev_next' => null,
				'show_nav' => null
			),
			$shortcode_settings
		);
		$slider_id = esc_attr($shortcode_settings['id']);

		$cycle_options = array();
		$this->slider_count++;//make each call to shortcode unique
		// Get slideshow by id
		$my_query = new WP_Query(
			array(
				'post_type' => 'cycloneslider',
				'order'=>'ASC',
				'posts_per_page' => 1,
				'name'=> $slider_id
			)
		);
		if($my_query->have_posts()):
			while ( $my_query->have_posts() ) : $my_query->the_post();
				
				$meta = get_post_custom();
				
				$admin_settings = $this->get_slider_admin_settings($meta);
				$slider_metas = $this->get_slider_metas($meta);
				foreach($slider_metas as $i=>$slider_meta){
					$slider_metas[$i]['title'] = __($slider_meta['title']);
					$slider_metas[$i]['description'] = __($slider_meta['description']);
				}
				$slides = $this->get_slides_from_meta($slider_metas);
				
				$template = $this->get_comp_slider_setting($admin_settings['template'], $shortcode_settings['template']);
				$template = esc_attr($template===null ? 'default' : $template);//fallback to default
				$slider_settings['fx'] = esc_attr($this->get_comp_slider_setting($admin_settings['fx'], $shortcode_settings['fx']));
				$slider_settings['speed'] = (int) $this->get_comp_slider_setting($admin_settings['speed'], $shortcode_settings['speed']);
				$slider_settings['timeout'] = (int) $this->get_comp_slider_setting($admin_settings['timeout'], $shortcode_settings['timeout']);
				$slider_settings['width'] = (int) $this->get_comp_slider_setting($admin_settings['width'], $shortcode_settings['width']);
				$slider_settings['height'] = (int) $this->get_comp_slider_setting($admin_settings['height'], $shortcode_settings['height']);
				$slider_settings['hover_pause'] = $this->get_comp_slider_setting($admin_settings['hover_pause'], $shortcode_settings['hover_pause']);
				$slider_settings['show_prev_next'] = (int) $this->get_comp_slider_setting($admin_settings['show_prev_next'], $shortcode_settings['show_prev_next']);
				$slider_settings['show_nav'] = (int) $this->get_comp_slider_setting($admin_settings['show_nav'], $shortcode_settings['show_nav']);
				
				$slider = $this->get_slider_template($slider_id, $template, $slides, $slider_metas, $slider_settings, $this->slider_count);
				
			endwhile;
			
			wp_reset_postdata();

		else:
			$slider = __('[Slideshow not found]', 'cycloneslider');
		endif;
		
		return $slider;
	}
	
	// Get slideshow template
	function get_slider_template($slider_id, $template_name, $slides, $slider_metas, $slider_settings, $slider_count){

		$template = get_stylesheet_directory()."/cycloneslider/{$template_name}/slider.php";
		if(@is_file($template)){
			ob_start();
			include($template);
			$html = ob_get_clean();
			return $html = $this->trim_white_spaces($html);
		}
		
		$template = $this->template_slider_dir."{$template_name}/slider.php";
		if(@is_file($template)) {
			ob_start();
			include($template);
			$html = ob_get_clean();
			return $html = $this->trim_white_spaces($html);
		}
		
		return sprintf(__('[Template "%s" not found]', 'cycloneslider'), $template_name);
	}
	
	// Process the post meta and return the settings
	function get_slider_admin_settings($meta){
		if(isset($meta['_cycloneslider_settings'][0]) and !empty($meta['_cycloneslider_settings'][0])){
			return maybe_unserialize($meta['_cycloneslider_settings'][0]);
		}
		return false;
	}
	
	// Process the post meta and return the settings
	function get_slider_metas($meta){
		if(isset($meta['_cycloneslider_metas'][0]) and !empty($meta['_cycloneslider_metas'][0])){
			return maybe_unserialize($meta['_cycloneslider_metas'][0]);
		}
		return false;
	}
	
	
	
	function trim_white_spaces($buffer){
		$search = array(
			'/\>[^\S ]+/s', //strip whitespaces after tags, except space
			'/[^\S ]+\</s', //strip whitespaces before tags, except space
			'/(\s)+/s'  // shorten multiple whitespace sequences
        );
		$replace = array(
			'>',
			'<',
			'\\1'
		);
		return preg_replace($search, $replace, $buffer);
	}

	// Return array of slide urls from meta
	function get_slides_from_meta($slider_metas){
		$slides = array();
		if(is_array($slider_metas)){
			foreach($slider_metas as $slider_meta){
				$attachment_id = (int) $slider_meta['id'];
				$image_url = wp_get_attachment_url($attachment_id);
				$image_url = ($image_url===false) ? '' : $image_url;
				$slides[] = $image_url;
			}
		}
		return $slides;
	}
	
	// Add custom query var
	function modify_query_vars($vars) {
		$vars[] = 'cyclone_templates_css';//add our own
		return $vars;
	}
	
	// Hook to template redirect
	function cyclone_css_hook() {
		if(intval(get_query_var('cyclone_templates_css')) == 1) {
			$ds = DIRECTORY_SEPARATOR;
			header("Content-type: text/css");
			
			if(file_exists($this->plugin_path."css{$ds}common.css")){
				echo file_get_contents($this->plugin_path."css{$ds}common.css");
			}
			
			$template_folders = $this->get_all_templates();
			foreach($template_folders as $name=>$folder){
				$style = $folder['path']."{$ds}style.css";
				if(file_exists($style)){
					echo "\n".str_replace('$tpl', $folder['url'], file_get_contents($style));//apply url and print css
				}
			}
			die();
		}
	}
	
	// Get all template locations. Returns array of locations containing path and url 
	function get_all_locations(){
		$ds = DIRECTORY_SEPARATOR;
		$template_locations = array();
		$template_locations[0] = array(
			'path'=>$this->template_slider_dir, //this resides in the plugin
			'url'=>$this->plugin_url.'templates/'
		);
		$template_locations[1] = array(
			'path'=> realpath(get_stylesheet_directory())."{$ds}cycloneslider{$ds}",//this resides in the current theme or child theme
			'url'=> get_stylesheet_directory_uri()."/cycloneslider/"
		);
		return $template_locations;
	}
	
	// Get all templates from all locations. Returns array of templates with keys as name containing array of path and url
	function get_all_templates(){
		$template_locations = $this->get_all_locations();
		$template_folders = array();
		foreach($template_locations as $location){
			if($files = @scandir($location['path'])){
				$c = 0;
				foreach($files as $name){
					if($name!='.' and $name!='..' and is_dir($location['path'].$name)){
						$name = sanitize_title($name);//change space to dash and all lowercase
						$template_folders[$name] = array( //here we override template of the same names. templates inside themes take precedence
							'path'=>$location['path'].$name,
							'url'=>$location['url'].$name,
						);
					}
				}
			}
		}
		return $template_folders;
	}
	
} // end class

endif;
