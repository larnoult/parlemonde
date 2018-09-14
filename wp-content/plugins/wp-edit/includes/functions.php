<?php

/*
****************************************************************
Global Functions
****************************************************************
*/
$plugin_options_global = get_option('wp_edit_global');

// Disable admin bar links
if($plugin_options_global['disable_admin_links'] != 1) {
	
	function wp_edit_admin_bar_init() {
	
		// Is the user sufficiently leveled, or has the bar been disabled?
		if (!is_admin() || !is_admin_bar_showing() ) {
			return;
		}
		// Good to go, lets do this!
		add_action('admin_bar_menu', 'wp_edit_admin_bar_links', 500);
	}
	add_action('admin_bar_init', 'wp_edit_admin_bar_init');
	
	function wp_edit_admin_bar_links() {
		
		global $wp_admin_bar;
		$path = admin_url();
		$wp_admin_bar->add_menu( array( 
			'title' => __('WP Edit','wp-edit'), 
			'id' => 'jwl_links', 
			'href' => $path . 'admin.php?page=wp_edit_options&tab=buttons' 
		));
		/** * Add the submenu links. */
		$wp_admin_bar->add_menu( array(
			'id'    => 'jwl_admin_buttons',
			'parent' => 'jwl_links',
			'title' => __('Buttons','wp-edit'),
			'href'  => $path.'admin.php?page=wp_edit_options&tab=buttons',
			'meta'  => array(
				'title' => __('Buttons','wp-edit')
			),
		));
		$wp_admin_bar->add_menu( array(
			'id'    => 'jwl_admin_global',
			'parent' => 'jwl_links',
			'title' => __('Global','wp-edit'),
			'href'  => $path.'admin.php?page=wp_edit_options&tab=global',
			'meta'  => array(
				'title' => __('Global','wp-edit')
			),
		));
		$wp_admin_bar->add_menu( array(
			'id'    => 'jwl_admin_general',
			'parent' => 'jwl_links',
			'title' => __('General','wp-edit'),
			'href'  => $path.'admin.php?page=wp_edit_options&tab=general',
			'meta'  => array(
				'title' => __('General','wp-edit')
			),
		));
		$wp_admin_bar->add_menu( array(
			'id'    => 'jwl_admin_posts',
			'parent' => 'jwl_links',
			'title' => __('Posts/Pages','wp-edit'),
			'href'  => $path.'admin.php?page=wp_edit_options&tab=posts',
			'meta'  => array(
				'title' => __('Posts/Pages','wp-edit')
			),
		));
		$wp_admin_bar->add_menu( array(
			'id'    => 'jwl_admin_editor',
			'parent' => 'jwl_links',
			'title' => __('Editor','wp-edit'),
			'href'  => $path.'admin.php?page=wp_edit_options&tab=editor',
			'meta'  => array(
				'title' => __('Editor','wp-edit')
			),
		));
		$wp_admin_bar->add_menu( array(
			'id'    => 'jwl_admin_extras',
			'parent' => 'jwl_links',
			'title' => __('Extras','wp-edit'),
			'href'  => $path.'admin.php?page=wp_edit_options&tab=extras',
			'meta'  => array(
				'title' => __('Extras','wp-edit')
			),
		));
		$wp_admin_bar->add_menu( array(
			'id'    => 'jwl_admin_user_specific',
			'parent' => 'jwl_links',
			'title' => __('User Specific','wp-edit'),
			'href'  => $path.'admin.php?page=wp_edit_options&tab=user_specific',
			'meta'  => array(
				'title' => __('User Specific','wp-edit')
			),
		));
		$wp_admin_bar->add_menu( array(
			'id'    => 'jwl_admin_database',
			'parent' => 'jwl_links',
			'title' => __('Database','wp-edit'),
			'href'  => $path.'admin.php?page=wp_edit_options&tab=database',
			'meta'  => array(
				'title' => __('Database','wp-edit')
			),
		));
		$wp_admin_bar->add_menu( array(
			'id'    => 'jwl_admin_about',
			'parent' => 'jwl_links',
			'title' => __('About','wp-edit'),
			'href'  => $path.'admin.php?page=wp_edit_options&tab=about',
			'meta'  => array(
				'title' => __('About','wp-edit')
			),
		));
	}
}

/*
****************************************************************
General Functions
****************************************************************
*/
$plugin_options_general = get_option('wp_edit_general');

// Enable LineBreak Shortcode
if($plugin_options_general['linebreak_shortcode'] == 1) {
	
	function wp_edit_insert_linebreak($atts){
 		return '<br clear="none" />';
	}
	add_shortcode( 'break', 'wp_edit_insert_linebreak' );
}

// Enable Shortcodes in Widgets
if($plugin_options_general['shortcodes_in_widgets'] == 1) {
	
	add_filter( 'widget_text', 'do_shortcode');
}

// Enable Shortcodes in Excerpts
if($plugin_options_general['shortcodes_in_excerpts'] == 1) {
	
	add_filter( 'the_excerpt', 'do_shortcode');
}

// Add Editor to Post Excerpts
if($plugin_options_general['post_excerpt_editor'] == 1) {
	
	function wp_edit_change_post_excerpt() {
		remove_meta_box('postexcerpt', 'post', 'normal');
		add_meta_box('postexcerpt', __('WP Edit Excerpt', 'wp-edit'), 'wp_edit_post_excerpt_meta_box', 'post', 'normal');
	}
	add_action( 'admin_init', 'wp_edit_change_post_excerpt' );
	
	function wp_edit_post_excerpt_meta_box() {
		global $wpdb,$post;
		$tinymce_summary = $wpdb->get_row("SELECT post_excerpt FROM $wpdb->posts WHERE id = '$post->ID'");
		$post_tinymce_excerpt = $tinymce_summary->post_excerpt;
	
		$id = 'excerpt';
		$settings = array(
						'quicktags' 	=> array('buttons' => 'em,strong,link',),
						'text_area_name'=> 'excerpt',
						'quicktags' 	=> true,
						'tinymce' 		=> true,
						'editor_css'	=> '<style>#wp-excerpt-editor-container .wp-editor-area{height:250px; width:100%;}</style>'
						);
		wp_editor($post_tinymce_excerpt,$id,$settings);
	}
}

// Add Editor to Page Excerpts
if($plugin_options_general['page_excerpt_editor'] == 1) {
	
	add_action('init', 'wp_edit_page_excerpts_init');
	function wp_edit_page_excerpts_init() {
	  add_post_type_support('page', array('excerpt'));
	}
	
	add_action( 'admin_init', 'wp_edit_change_page_excerpt' );
	function wp_edit_change_page_excerpt() {
		remove_meta_box('postexcerpt', 'page', 'normal');
		add_meta_box('postexcerpt', __('Wp Edit Excerpt', 'wp-edit'), 'wp_edit_page_excerpt_meta_box', 'page', 'normal');
	}
	
	function wp_edit_page_excerpt_meta_box() {
		global $wpdb,$post;
		$tinymce_summary_page = $wpdb->get_row("SELECT post_excerpt FROM $wpdb->posts WHERE id = '$post->ID'");
		$post_tinymce_excerpt_page 	 = $tinymce_summary_page->post_excerpt;
	
		$id = 'excerpt';
		$settings = array(
						'quicktags' 	=> array('buttons' => 'em,strong,link',),
						'text_area_name'=> 'excerpt',
						'quicktags' 	=> true,
						'tinymce' 		=> true,
						'editor_css'	=> '<style>#wp-excerpt-editor-container .wp-editor-area{height:250px; width:100%;}</style>'
						);
		wp_editor($post_tinymce_excerpt_page,$id,$settings);
	}
}

// Add Editor to CPT's
if(isset($plugin_options_general['cpt_excerpt_editor']) && !empty($plugin_options_general['cpt_excerpt_editor'])) {
	
	add_action('admin_init', 'wp_edit_change_cpt_excerpt');
}
function wp_edit_change_cpt_excerpt() {
	
	$plugin_options_general = get_option('wp_edit_general');
	$cpt_excerpts = $plugin_options_general['cpt_excerpt_editor'];
	
	foreach($cpt_excerpts as $key => $cpt) {
		
		remove_meta_box('postexcerpt', $cpt, 'normal');
		add_meta_box('postexcerpt', __('Wp Edit (' . $cpt . ') Excerpt','wp-edit'), 'wp_edit_cpt_excerpt_meta_box', $cpt, 'normal');
	}
}
function wp_edit_cpt_excerpt_meta_box() {
	
	global $wpdb, $post;
	$get_cpt_excerpt = $wpdb->get_row("SELECT post_excerpt FROM $wpdb->posts WHERE id = '$post->ID'");
	$cpt_excerpt = $get_cpt_excerpt->post_excerpt;
	$id = 'excerpt';
	$settings = array('quicktags' => array('buttons' => 'em,strong,link',), 'text_area_name' => 'excerpt', 'quicktags' => true, 'tinymce' => true, 'editor_css'	=> '<style>#wp-excerpt-editor-container .wp-editor-area{height:250px; width:100%;}</style>');
	
	wp_editor($cpt_excerpt, $id, $settings);
}

// Extend editor to profile biography
if($plugin_options_general['profile_editor'] == 1) {
	
	function wp_edit_visual_editor($user) {
		
		// Contributor level user or higher required
		if ( !current_user_can('edit_posts') )
			return;
		?>
		<table class="form-table">
			<tr id="wp_edit_biographical_editor" class="user-description-wrap">
				<th><label for="description"><?php _e('Biographical Info', 'wp-edit'); ?></label></th>
				<td>
					<?php 
					$description = get_user_meta( $user->ID, 'description', true);
					$args = array('textarea_rows' => 5);
					wp_editor( $description, 'description', $args ); 
					?>
					<p class="description"><?php _e('Share a little biographical information to fill out your profile. This may be shown publicly.', 'wp-edit'); ?></p>
				</td>
			</tr>
		</table>
		<?php
	}
	add_action('show_user_profile','wp_edit_visual_editor');
	add_action('edit_user_profile','wp_edit_visual_editor');
	
	function wp_edit_editor_biography_js($hook) {
		
	global $current_screen;
	if($current_screen->id === 'profile' || $current_screen->id === 'edit-profile') {
			
			?>
            <script type="text/javascript">
			
				jQuery(document).ready(function($) {
					
					// Remove the textarea before displaying visual editor
					$('.user-description-wrap').first().replaceWith($('#wp_edit_biographical_editor'));
					// Expand text editor width
					$('.wp-editor-area').css('width', '100%');
				});
			</script>
			<?php
		}
	}
	add_action( 'admin_head', 'wp_edit_editor_biography_js', 10, 1 );
}

/*
****************************************************************
Posts/Pages Functions
****************************************************************
*/
$plugin_options_posts = get_option('wp_edit_posts');

// Post title field
if(isset($plugin_options_posts['post_title_field']) && $plugin_options_posts['post_title_field'] != 'Enter title here') {
	
	function wp_edit_title_text_input( $title ){
		
		$plugin_options_posts = get_option('wp_edit_posts');
		$title = $plugin_options_posts['post_title_field'];
		return $title;
	}
	add_filter( 'enter_title_here', 'wp_edit_title_text_input' );
}

// Column Shortcodes
if($plugin_options_posts['column_shortcodes'] == 1) {
	
	function wp_edit_one_third( $atts, $content = null ) { return '<div class="jwl_one_third">' . do_shortcode($content) . '</div>'; }
	add_shortcode('one_third', 'wp_edit_one_third');
	function wp_edit_one_third_last( $atts, $content = null ) { return '<div class="jwl_one_third last">' . do_shortcode($content) . '</div><div class="clearboth"></div>'; }
	add_shortcode('one_third_last', 'wp_edit_one_third_last');
	function wp_edit_two_third( $atts, $content = null ) { return '<div class="jwl_two_third">' . do_shortcode($content) . '</div>'; }
	add_shortcode('two_third', 'wp_edit_two_third');
	function wp_edit_two_third_last( $atts, $content = null ) { return '<div class="jwl_two_third last">' . do_shortcode($content) . '</div><div class="clearboth"></div>'; }
	add_shortcode('two_third_last', 'wp_edit_two_third_last');
	function wp_edit_one_half( $atts, $content = null ) { return '<div class="jwl_one_half">' . do_shortcode($content) . '</div>'; }
	add_shortcode('one_half', 'wp_edit_one_half');
	function wp_edit_one_half_last( $atts, $content = null ) { return '<div class="jwl_one_half last">' . do_shortcode($content) . '</div><div class="clearboth"></div>'; }
	add_shortcode('one_half_last', 'wp_edit_one_half_last');
	function wp_edit_one_fourth( $atts, $content = null ) { return '<div class="jwl_one_fourth">' . do_shortcode($content) . '</div>'; }
	add_shortcode('one_fourth', 'wp_edit_one_fourth');
	function wp_edit_one_fourth_last( $atts, $content = null ) { return '<div class="jwl_one_fourth last">' . do_shortcode($content) . '</div><div class="clearboth"></div>'; }
	add_shortcode('one_fourth_last', 'wp_edit_one_fourth_last');
	function wp_edit_three_fourth( $atts, $content = null ) { return '<div class="jwl_three_fourth">' . do_shortcode($content) . '</div>'; }
	add_shortcode('three_fourth', 'wp_edit_three_fourth');
	function wp_edit_three_fourth_last( $atts, $content = null ) { return '<div class="jwl_three_fourth last">' . do_shortcode($content) . '</div><div class="clearboth"></div>'; }
	add_shortcode('three_fourth_last', 'wp_edit_three_fourth_last');
	function wp_edit_one_fifth( $atts, $content = null ) { return '<div class="jwl_one_fifth">' . do_shortcode($content) . '</div>'; }
	add_shortcode('one_fifth', 'wp_edit_one_fifth');
	function wp_edit_one_fifth_last( $atts, $content = null ) { return '<div class="jwl_one_fifth last">' . do_shortcode($content) . '</div><div class="clearboth"></div>'; }
	add_shortcode('one_fifth_last', 'wp_edit_one_fifth_last');
	function wp_edit_two_fifth( $atts, $content = null ) { return '<div class="jwl_two_fifth">' . do_shortcode($content) . '</div>'; }
	add_shortcode('two_fifth', 'wp_edit_two_fifth');
	function wp_edit_two_fifth_last( $atts, $content = null ) { return '<div class="jwl_two_fifth last">' . do_shortcode($content) . '</div><div class="clearboth"></div>'; }
	add_shortcode('two_fifth_last', 'wp_edit_two_fifth_last');
	function wp_edit_three_fifth( $atts, $content = null ) { return '<div class="jwl_three_fifth">' . do_shortcode($content) . '</div>'; }
	add_shortcode('three_fifth', 'wp_edit_three_fifth');
	function wp_edit_three_fifth_last( $atts, $content = null ) { return '<div class="jwl_three_fifth last">' . do_shortcode($content) . '</div><div class="clearboth"></div>'; }
	add_shortcode('three_fifth_last', 'wp_edit_three_fifth_last');
	function wp_edit_four_fifth( $atts, $content = null ) { return '<div class="jwl_four_fifth">' . do_shortcode($content) . '</div>'; }
	add_shortcode('four_fifth', 'wp_edit_four_fifth');
	function wp_edit_four_fifth_last( $atts, $content = null ) { return '<div class="jwl_four_fifth last">' . do_shortcode($content) . '</div><div class="clearboth"></div>'; }
	add_shortcode('four_fifth_last', 'wp_edit_four_fifth_last');
	function wp_edit_one_sixth( $atts, $content = null ) { return '<div class="jwl_one_sixth">' . do_shortcode($content) . '</div>'; }
	add_shortcode('one_sixth', 'wp_edit_one_sixth');
	function wp_edit_one_sixth_last( $atts, $content = null ) { return '<div class="jwl_one_sixth last">' . do_shortcode($content) . '</div><div class="clearboth"></div>'; }
	add_shortcode('one_sixth_last', 'wp_edit_one_sixth_last');
	function wp_edit_five_sixth( $atts, $content = null ) { return '<div class="jwl_five_sixth">' . do_shortcode($content) . '</div>'; }
	add_shortcode('five_sixth', 'wp_edit_five_sixth');
	function wp_edit_five_sixth_last( $atts, $content = null ) { return '<div class="jwl_five_sixth last">' . do_shortcode($content) . '</div><div class="clearboth"></div>'; }
	add_shortcode('five_sixth_last', 'wp_edit_five_sixth_last');

	function wp_edit_column_stylesheet() {
	
		wp_register_style('wp_edit_column-styles', plugins_url().'/wp-edit/css/column-style.css');
		wp_enqueue_style('wp_edit_column-styles');
	}
	add_action('wp_print_styles', 'wp_edit_column_stylesheet');
}

// Disable wpautop
if(!empty($plugin_options_posts['disable_wpautop']) && $plugin_options_posts['disable_wpautop'] == '1') {
	
	if ( ! class_exists( 'JWL_Toggle_wpautop' ) ) {
	
		/*** JWL_Toggle_wpautop class. */
		class JWL_Toggle_wpautop {
	
			/*** Add our hooks and filters */
			function __construct() {
				
				add_action( 'admin_init', array( $this, 'activation' ) );
				add_action( 'admin_init', array( $this, 'admin_init' ) );
				add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
				add_action( 'save_post', array( $this, 'save_post' ) );
				add_action( 'the_post', array( $this, 'the_post' ) );
				add_action( 'loop_end', array( $this, 'loop_end' ) );
	
				add_filter( 'post_class', array( $this, 'post_class' ), 10, 3 );
			}
	
			/*** By default, add the ability to disable wpautop on all registered post types */
			function activation() {
				
				if ( $settings = get_option( 'jwl_toggle_wpautop_settings' ))
					return;
	
				$post_types = get_post_types();
	
				if ( empty( $post_types ) )
					return;
	
				$default_post_types = array();
	
				foreach ( $post_types as $post_type ) {
					$pt = get_post_type_object( $post_type );
	
					if ( in_array( $post_type, array( 'revision', 'nav_menu_item', 'attachment' ) ) || ! $pt->public )
						continue;
	
					$default_post_types[] = $post_type;
				}
	
				if ( ! empty( $default_post_types ) )
					add_option( 'jwl_toggle_wpautop_settings', $default_post_types );
			}
	
			/*** Add our settings fields to the writing page */
			function admin_init() {
				
				register_setting( 'jwl_toggle_wpautop_settings', 'jwl_toggle_wpautop_settings', array( $this, 'sanitize_settings' ) );
	
				//add a section for the plugin's settings on the writing page
				add_settings_section( 'jwl_toggle_wpautop_settings_section', __('Toggle wpautop', 'wp-edit'), array( $this, 'settings_section_text' ), 'writing' );
	
				//For each post type add a settings field, excluding revisions and nav menu items
				if ( $post_types = get_post_types() ) {
					foreach ( $post_types as $post_type ) {
						$pt = get_post_type_object( $post_type );
	
						if ( in_array( $post_type, array( 'revision', 'nav_menu_item', 'attachment' ) ) || ! $pt->public )
							continue;
	
						add_settings_field( 'jwl_toggle_wpautop_post_types' . $post_type, $pt->labels->name, array( $this,'toggle_wpautop_field' ), 'writing', 'jwl_toggle_wpautop_settings_section', array( 'slug' => $pt->name, 'name' => $pt->labels->name ) );
					}
				}
			}
	
			/*** Display our settings section */
			function settings_section_text() {
				echo '<p>';
				_e('Select which post types have the option to disable the wpautop filter.','wp-edit');
				echo '</p>';
				settings_fields( 'jwl_toggle_wpautop_settings' );
			}
	
			/*** Display the actual settings field */
			function toggle_wpautop_field( $args ) {
				
				$settings = get_option( 'jwl_toggle_wpautop_settings', array() );
	
				if ( $post_types = get_post_types() ) { ?>
					<input type="checkbox" name="jwl_toggle_wpautop_post_types[]" id="jwl_toggle_wpautop_post_types_<?php echo $args['slug']; ?>" value="<?php echo $args['slug']; ?>" <?php in_array( $args['slug'], $settings ) ? checked( true ) : checked( false ); ?>/>
					<?php
				}
			}
	
			/*** Sanitize our settings fields */
			function sanitize_settings( $input ) {
				
				$input = wp_parse_args( $_POST['jwl_toggle_wpautop_post_types'], array() );
	
				$new_input = array();
	
				foreach ( $input as $pt ) {
					if ( post_type_exists( sanitize_text_field( $pt ) ) )
						$new_input[] = sanitize_text_field( $pt );
				}
	
				return $new_input;
			}
	
			/*** Add meta boxes to the selected post types */
			function add_meta_boxes( $post_type ) {
				
				$settings = get_option( 'jwl_toggle_wpautop_settings', array() );
	
				if ( empty( $settings ) )
					return;
	
				if ( in_array( $post_type, $settings ) )
					add_action( 'post_submitbox_misc_actions', array( $this, 'post_submitbox_misc_actions' ), 5 );
			}
	
			/*** Display a checkbox to disable the wpautop filter */
			function post_submitbox_misc_actions() {
				
				global $post;
	
				wp_nonce_field( '_jwl_wpautop_nonce', '_jwl_wpautop_noncename' );
				?>
				<div class="misc-pub-section jwl-wpautop">
					<span>Disable wpautop:</span> <input type="checkbox" name="_jwl_disable_wpautop" id="_jwl_disable_wpautop" <?php checked( get_post_meta( $post->ID, '_jwl_disable_wpautop', true ) ); ?> /> <span style="float:right; display: block;"><a href="http://codex.wordpress.org/Function_Reference/wpautop" target="_blank">?</a>
				</div>
				<?php
			}
	
			/*** Process the wpautop checkbox */
			function save_post( $post_id ) {
				//Skip revisions and autosaves
				if ( wp_is_post_revision( $post_id ) || ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) )
					return;
	
				//Users should have the ability to edit listings.
				if ( ! current_user_can( 'edit_post', $post_id ) )
					return;
	
				if ( isset( $_POST['_jwl_wpautop_noncename'] ) && wp_verify_nonce( $_POST['_jwl_wpautop_noncename'], '_jwl_wpautop_nonce' ) ) {
	
					if ( isset( $_POST['_jwl_disable_wpautop'] ) && ! empty( $_POST['_jwl_disable_wpautop'] ) )
						update_post_meta( $post_id, '_jwl_disable_wpautop', 1 );
					else
						delete_post_meta( $post_id, '_jwl_disable_wpautop' );
				}
			}
	
			/*** Add or remove the wpautop filter */
			function the_post( $post ) {
				if ( get_post_meta( $post->ID, '_jwl_disable_wpautop', true ) ) {
					remove_filter( 'the_content', 'wpautop' );
					remove_filter( 'the_excerpt', 'wpautop' );
				} else {
					if ( ! has_filter( 'the_content', 'wpautop' ) )
						add_filter( 'the_content', 'wpautop' );
	
					if ( ! has_filter( 'the_excerpt', 'wpautop' ) )
						add_filter( 'the_excerpt', 'wpautop' );
				}
			}
	
			/*** loop_end function.  * After we run our loop, everything should be set back to normal */
			function loop_end() {
				if ( ! has_filter( 'the_content', 'wpautop' ) )
					add_filter( 'the_content', 'wpautop' );
	
				if ( ! has_filter( 'the_excerpt', 'wpautop' ) )
					add_filter( 'the_excerpt', 'wpautop' );
			}
	
			/*** Add a class to posts noting whether they were passed through the wpautop filter */
			function post_class( $classes, $class, $post_id ) {
				if ( get_post_meta( $post_id, '_jwl_disable_wpautop', true ) )
					$classes[] = 'no-wpautop';
				else
					$classes[] = 'wpautop';
	
				return $classes;
			}
		}
	}
	$jwl_toggle_wpautop = new JWL_Toggle_wpautop();
}

// Max post revisions
if(isset($plugin_options_posts['max_post_revisions']) && $plugin_options_posts['max_post_revisions'] != '') {
	
	function wp_edit_max_post_revisions( $num, $post ) {  
		$options_post_revisions = get_option('wp_edit_posts'); 
		if( 'post' == $post->post_type ) {
			$num = $options_post_revisions['max_post_revisions'];
		}
		return $num;
	}
	add_filter( 'wp_revisions_to_keep', 'wp_edit_max_post_revisions', 10, 2 );
}

// Max page revisions
if(isset($plugin_options_posts['max_page_revisions']) && $plugin_options_posts['max_page_revisions'] != '') {
	
	function wp_edit_max_page_revisions( $num, $post ) {  
		$options_post_revisions = get_option('wp_edit_posts'); 
		if( 'page' == $post->post_type ) {
			$num = $options_post_revisions['max_page_revisions'];
		}
		return $num;
	}
	add_filter( 'wp_revisions_to_keep', 'wp_edit_max_page_revisions', 10, 2 );
}

// Hide admin posts
if(!empty($plugin_options_posts['hide_admin_posts']) && $plugin_options_posts['hide_admin_posts'] != '') {

	function wp_edit_hide_admin_posts( $query ) {
			if( !is_admin() ) return $query;
			global $pagenow;
			$options_hide_posts = get_option('wp_edit_posts');
			$jwl_hide_posts = $options_hide_posts['hide_admin_posts'];
			$jwl_hide_posts_array = explode(",",$jwl_hide_posts);
			
			if( 'edit.php' == $pagenow && ( get_query_var('post_type') && 'post' == get_query_var('post_type') ) )
					$query->set( 'post__not_in', $jwl_hide_posts_array ); // page id
			return $query;
	}
	add_action( 'pre_get_posts' ,'wp_edit_hide_admin_posts' );
}

// Hide admin pages
if(!empty($plugin_options_posts['hide_admin_pages']) && $plugin_options_posts['hide_admin_pages'] != '') {

	function wp_edit_hide_admin_pages( $query ) {
			if( !is_admin() ) return $query;
			global $pagenow;
			$options_hide_pages = get_option('wp_edit_posts');
			$jwl_hide_pages = $options_hide_pages['hide_admin_pages'];
			$jwl_hide_pages_array = explode(",",$jwl_hide_pages);
			
			if( 'edit.php' == $pagenow && ( get_query_var('post_type') && 'page' == get_query_var('post_type') ) )
					$query->set( 'post__not_in', $jwl_hide_pages_array ); // page id
			return $query;
	}
	add_action( 'pre_get_posts' ,'wp_edit_hide_admin_pages' );
}

/*
****************************************************************
Editor Functions
****************************************************************
*/
$plugin_options_editor = get_option('wp_edit_editor');

// BBPress editor
if(isset($plugin_options_editor['bbpress_editor']) && $plugin_options_editor['bbpress_editor'] === '1') {
	
	// Add visual editor
	function wp_edit_enable_bbpress_visual_editor( $args = array() ) {
		
		$args['tinymce'] = true;
		$args['teeny'] = false;
		return $args;
	}
	add_filter( 'bbp_after_get_the_content_parse_args', 'wp_edit_enable_bbpress_visual_editor' );
	
	// Replace kses funtion (to allow more tags)
	function wp_edit_enable_bbpress_custom_kses_allowed_tags() {
		
		return array(
		
			// Links
			'a' 			=> array( 'class'    => true, 'href' => true, 'title' => true, 'rel' => true, 'class' => true, 'target' => true ),
			// Quotes
			'blockquote' 	=> array( 'cite' => true ),
			// Div
			'div' 			=> array( 'class' => true ),
			// Span
			'span'			=> array( 'class' => true ),
			// Code
			'code' 			=> array(),
			'pre' 			=> array( 'class'  => true ),
			// Formatting
			'em' 			=> array(),
			'strong' 		=> array(),
			'del' 			=> array( 'datetime' => true ),
			// Lists
			'ul'     	    => array(),
			'ol'     	    => array( 'start' => true ),
			'li'     	    => array(),
			// Images
			'img'        	=> array( 'class' => true, 'src' => true, 'border' => true, 'alt' => true, 'height' => true, 'width' => true ),
			// Tables
			'table'      	=> array( 'align' => true, 'bgcolor' => true, 'border' => true ),
			'tbody'      	=> array( 'align' => true, 'valign' => true ),
			'td'        	=> array( 'align' => true, 'valign' => true ),
			'tfoot'     	=> array( 'align' => true, 'valign' => true ),
			'th'        	=> array( 'align' => true, 'valign' => true ),
			'thead'     	=> array( 'align' => true, 'valign' => true ),
			'tr'        	=> array( 'align' => true, 'valign' => true )
		);
	}
	add_filter( 'bbp_kses_allowed_tags', 'wp_edit_enable_bbpress_custom_kses_allowed_tags' );
}

/*
****************************************************************
Extras Functions
****************************************************************
*/
$plugin_options_extras = get_option('wp_edit_extras');

// Signoff text
if(isset($plugin_options_extras['signoff_text']) && $plugin_options_extras['signoff_text'] != '') {
	
	function wp_edit_sign_off_text() {
		
		$options = get_option('wp_edit_extras');
		$jwl_signoff = isset($options['signoff_text']) ? $options['signoff_text'] : 'Please enter text here...';
			
		return $jwl_signoff;  
	} 
	add_shortcode('signoff', 'wp_edit_sign_off_text');
}


/*
****************************************************************
User Specific Functions
****************************************************************
*/
function wp_edit_user_specific_init() {
	
	global $current_user;
	$opts_user_meta = get_user_meta($current_user->ID, 'aaa_wp_edit_user_meta', true);
	
	// Add ID Column
	if(isset($opts_user_meta['id_column']) && $opts_user_meta['id_column'] === '1') {
			
		function wp_edit_column_id($defaults){
			$defaults['wps_post_id'] = __('ID');
			return $defaults;
		}
		add_filter('manage_posts_columns', 'wp_edit_column_id', 5);
		add_filter('manage_pages_columns', 'wp_edit_column_id', 5);
		function wp_edit_custom_column_id($column_name, $id){
			if($column_name === 'wps_post_id'){
				echo $id;
			}
		}
		add_action('manage_posts_custom_column', 'wp_edit_custom_column_id', 5, 2);
		add_action('manage_pages_custom_column', 'wp_edit_custom_column_id', 5, 2);
	}
	
	// Add Tumbnail Column
	if(isset($opts_user_meta['thumbnail_column']) && $opts_user_meta['thumbnail_column'] === '1') {
		
		if ( !function_exists('wp_edit_AddThumbColumn') && function_exists('add_theme_support') ) {
			
			
			// First, check if current theme support post thumbnails
			function wpep_check_post_thumbnails() {
				
				// If current theme does not support post thumbnails
				if(!current_theme_supports('post-thumbnails')) {
					
					// Add post thumbnail support
					add_theme_support('post-thumbnails', array( 'post', 'page' ) );
				}
			}
			add_action('after_theme_setup', 'wpep_check_post_thumbnails');
			 
			function wp_edit_AddThumbColumn($cols) {
				  
				$cols['thumbnail'] = __('Thumbnail', 'wp-edit');  
				return $cols;  
			}  
		  
		function wp_edit_AddThumbValue($column_name, $post_id) {  
		 
			if ( 'thumbnail' == $column_name ) {
				  
				$thumb = get_the_post_thumbnail($post_id, array(100,70));
				  
				if ( isset($thumb) && $thumb ) { echo $thumb; }
				else { echo __('None','wp-edit'); }
			}
		}  
		  
		// for posts  
		add_filter( 'manage_posts_columns', 'wp_edit_AddThumbColumn' );  
		add_action( 'manage_posts_custom_column', 'wp_edit_AddThumbValue', 10, 2 );  
		  
		// for pages  
		add_filter( 'manage_pages_columns', 'wp_edit_AddThumbColumn' );  
		add_action( 'manage_pages_custom_column', 'wp_edit_AddThumbValue', 10, 2 );  
		}
	}
	
	// Hide Text Tab
	if(isset($opts_user_meta['hide_text_tab']) && $opts_user_meta['hide_text_tab'] === '1') {
		
		global $pagenow;
		if ($pagenow=='post.php' || $pagenow == 'post-new.php' || ($pagenow == "admin.php" && (isset($_GET['page'])) == 'cleverness-to-do-list') || ($pagenow == "options-general.php" && (isset($_GET['page'])) == 'ultimate-tinymce')) {
			function wp_edit_user_hide_on_todo() {
				?><style type="text/css"> #excerpt-html { display: none !important; } #content-id-html { display: none !important; }  #content-html { display: none !important; } #clevernesstododescription-html { display: none !important; }</style><?php
			}
			add_filter('admin_head','wp_edit_user_hide_on_todo');
		}
	}
	
	// Default Visual Tab
	if(isset($opts_user_meta['default_visual_tab']) && $opts_user_meta['default_visual_tab'] === '1') {
		
		add_filter( 'wp_default_editor', create_function('', 'return "tmce";') );
	}
	
	// Disable Dashboard Widget
	if(isset($opts_user_meta['dashboard_widget']) && $opts_user_meta['dashboard_widget'] != '1') {
		
		add_action('wp_dashboard_setup', 'wp_edit_user_custom_dashboard_widgets');
		function wp_edit_user_custom_dashboard_widgets() {
			
			global $wp_meta_boxes;
			wp_add_dashboard_widget('jwl_user_tinymce_dashboard_widget', __('WP Edit Pro RSS Feed', 'wp-edit'), 'wp_edit_user_tinymce_widget', 'wp_edit_user_configure_widget');
		}	
		function wp_edit_user_tinymce_widget() {
			
			$jwl_widgets = get_option( 'wp_edit_user_dashboard_options' ); // Get the dashboard widget options
			$jwl_widget_id = 'jwl_user_tinymce_dashboard_widget'; // This must be the same ID we set in wp_add_dashboard_widget
			
			/* Check whether we have set the post count through the controls. If we didn't, set the default to 5 */
			$jwl_total_items = isset( $jwl_widgets[$jwl_widget_id] ) && isset( $jwl_widgets[$jwl_widget_id]['items'] ) ? absint( $jwl_widgets[$jwl_widget_id]['items'] ) : 5;
			
			$protocol = is_ssl() === true ? 'https:' : 'http:';
			
			// Echo the output of the RSS Feed.
			echo '<p><a href="//www.feedblitz.com/f/?Sub=950320"><img title="Subscribe to get updates by email and more!" border="0" src="//assets.feedblitz.com/chicklets/email/i/25/950320.bmp"></a><br />News updates for WP Edit Pro and Stable versions.</p>';
			echo '<p style="border-bottom:#000 1px solid;">Showing ('.$jwl_total_items.') Posts</p>';
			echo '<div class="rss-widget">';
				wp_widget_rss_output( $protocol . '//feeds.feedblitz.com/wpeditpro&x=1', array(
					'title' => '',
					'items' => $jwl_total_items,
					'show_author' => 0,
					'show_date' => 1
				));
			echo "</div>";
			echo '<p style="text-align:center;border-top: #000 1px solid;padding:5px;"><a target="_blank" href="https://wpeditpro.com/">WP Edit Pro</a> - Visual Wordpress Editor</p>';
		}
		function wp_edit_user_configure_widget() {
			
			$jwl_widget_id = 'jwl_user_tinymce_dashboard_widget'; // This must be the same ID we set in wp_add_dashboard_widget
			$jwl_form_id = 'jwl-user-dashboard-control'; // Set this to whatever you want
			
			// Checks whether there are already dashboard widget options in the database
			if ( !$jwl_widget_options = get_option( 'wp_edit_user_dashboard_options' ) ) {
				$jwl_widget_options = array(); // If not, we create a new array
			}
			
			// Check whether we have information for this form
			if ( !isset($jwl_widget_options[$jwl_widget_id]) ) {
				$jwl_widget_options[$jwl_widget_id] = array(); // If not, we create a new array
			}
			
			// Check whether our form was just submitted
			if ( 'POST' == $_SERVER['REQUEST_METHOD'] && isset($_POST[$jwl_form_id]) ) {
				
				/* Get the value. In this case ['items'] is from the input field with the name of '.$form_id.'[items] */
				$jwl_number = absint( $_POST[$jwl_form_id]['items'] );
				$jwl_widget_options[$jwl_widget_id]['items'] = $jwl_number; // Set the number of items
				update_option( 'wp_edit_user_dashboard_options', $jwl_widget_options ); // Update our dashboard widget options so we can access later
			}
			
			// Check if we have set the number of posts previously. If we didn't, then we just set it as empty. This value is used when we create the input field
			$jwl_number = isset( $jwl_widget_options[$jwl_widget_id]['items'] ) ? (int) $jwl_widget_options[$jwl_widget_id]['items'] : '';
			
			// Create our form fields. Pay very close attention to the name part of the input field.
			echo '<p><label for="jwl_user_tinymce_dashboard_widget-number">' . __('Number of posts to show:', 'wp-edit') . '</label>';
			echo '<input id="jwl_user_tinymce_dashboard_widget-number" name="'.$jwl_form_id.'[items]" type="text" value="' . $jwl_number . '" size="3" /></p>';
		}
	}
	
	// Enable Post/Page Highlights
	if(isset($opts_user_meta['enable_highlights']) && $opts_user_meta['enable_highlights'] === '1') {
	
		function wp_edit_highlight_posts_status_colors(){
			
			global $current_user;
			$opts_user_meta = get_user_meta($current_user->ID, 'aaa_wp_edit_user_meta', true);
			?>
			<style type="text/css">
			.status-draft{background-color: <?php (isset($opts_user_meta['draft_highlight']) ? print $opts_user_meta['draft_highlight'] : print '#FFFFFF'); ?> !important;}
			.status-pending{background-color: <?php (isset($opts_user_meta['pending_highlight']) ? print $opts_user_meta['pending_highlight'] : print '#FFFFFF'); ?> !important;}
			.status-publish{background-color: <?php (isset($opts_user_meta['published_highlight']) ? print $opts_user_meta['published_highlight'] : print '#FFFFFF'); ?> !important;}
			.status-future{background-color: <?php (isset($opts_user_meta['future_highlight']) ? print $opts_user_meta['future_highlight'] : print '#FFFFFF'); ?> !important;}
			.status-private{background-color: <?php (isset($opts_user_meta['private_highlight']) ? print $opts_user_meta['private_highlight'] : print '#FFFFFF'); ?> !important;}
			</style>
			<?php
		}
		add_action('admin_head','wp_edit_highlight_posts_status_colors');
	}
	
}
add_action('init', 'wp_edit_user_specific_init');

?>