<?php

/**
 * View Own Posts Media Only plugin main class
 * 
 * @author Vladimir Garagulya	
 * @package View Own Posts Media Only 
 */
class View_Own_Posts_Media_Only {

	// common code staff, including options data processor
	private $lib = null;

	/**
	 * class constructor
	 * 
	 */
	function __construct() {

		// activation action
		register_activation_hook(__FILE__, array(&$this, 'setup'));

		// deactivation action
		register_deactivation_hook(__FILE__, array(&$this, 'cleanup'));

		$this->lib = new View_Own_Post_Media_Only_Library('view_own_post_media_only');

		add_action('admin_init', array(&$this, 'init'), 1);

		// Add the translation function after the plugins loaded hook.
		add_action('plugins_loaded', array(&$this, 'load_translation'));

		// add own submenu 
		add_action('admin_menu', array(&$this, 'create_menu'));

		$this->init();
	}

	// end of __construct()

	/**
	 * Plugin initialization
	 * 
	 */
	function init() {

		// add a Settings link in the installed plugins page
		add_filter('plugin_action_links', array(&$this, 'plugin_action_links'), 10, 2);
		add_filter('plugin_row_meta', array(&$this, 'plugin_row_meta'), 10, 2);

		// Show only posts and media related to logged in author
		add_action('pre_get_posts', array(&$this, 'query_set_only_author'));
/*
  $hide_other_posts_comments = $this->lib->get_option('hide_other_posts_comments', 1);
  if ($hide_other_posts_comments) {
    // Show comments for posts of this author only
    add_action('pre_get_comments', array(&$this, 'author_posts_comments_only'));
  }*/

		$select_uploaded_to_this_post = $this->lib->get_option('select_uploaded_to_this_post', 1);
		if ($select_uploaded_to_this_post) {
			// preselect post Uploaded to this post attachements
			add_action('admin_footer-post-new.php', array(&$this, 'select_uploaded_to_this_post'));
			add_action('admin_footer-post.php', array(&$this, 'select_uploaded_to_this_post'));
		}

		$hide_attachments_type_menu = $this->lib->get_option('hide_attachments_type_menu', 1);
		if ($hide_attachments_type_menu) {
			add_action('admin_enqueue_scripts', array(&$this, 'admin_css'));
		}
	}

	// end of init()

	/**
	 * Load translation file according to the current locale
	 */
	function load_translation() {

		load_plugin_textdomain('vopmo', '', VOPMO_PLUGIN_DIR_NAME . DIRECTORY_SEPARATOR . 'lang');
	}

	// end of load_translation()


	function plugin_action_links($links, $file) {

		$plugin = plugin_basename(VOPMO_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'view-own-posts-media-only.php');
		if ($file == $plugin) {
			$settings_link = "<a href='options-general.php?page=view-own-posts-media-only.php'>" . __('Settings', 'vopmo') . "</a>";
			array_unshift($links, $settings_link);
		}

		return $links;
	}

	// end of plugin_action_links()


	function plugin_row_meta($links, $file) {

		if ($file == plugin_basename(VOPMO_PLUGIN_DIR . DIRECTORY_SEPARATOR . '/view-own-posts-media-only.php')) {
			$links[] = '<a target="_blank" href="http://www.shinephp.com/view-own-post-media-only-wordpress-plugin/#changelog">' . __('Changelog', 'vopmo') . '</a>';
		}

		return $links;
	}

	// end of plugin_row_meta()

	/**
	 * register plugin menu item under WordPress Settings menu
	 */
	function create_menu() {

		if (function_exists('add_menu_page')) {
			add_options_page(esc_html__('View Own Posts...', 'vopmo'), esc_html__('View Own Posts...', 'vopmo'), 'manage_options', 'view-own-posts-media-only.php', array(&$this, 'settings'));
		}
	}

	// end of create_menu()


	function settings() {
		
  $post_types = get_post_types(array(), 'names', 'and');     
		unset($post_types['post']);
		unset($post_types['page']);
		unset($post_types['attachment']);
  unset($post_types['revision']);
  unset($post_types['nav_menu_item']);
  unset($post_types['wp-types-group']);
  unset($post_types['wp-types-user-group']);
		
		if (isset($_POST['view_own_posts_media_only_update'])) {  // process update from the options form
			$nonce = $_REQUEST['_wpnonce'];
			if (!wp_verify_nonce($nonce, 'view-own-posts-media-only')) {
				wp_die('Security check');
			}
			$select_uploaded_to_this_post = $this->lib->get_request_var('select_uploaded_to_this_post', 'post', 'checkbox');
			$this->lib->put_option('select_uploaded_to_this_post', $select_uploaded_to_this_post);
			$hide_attachments_type_menu = $this->lib->get_request_var('hide_attachments_type_menu', 'post', 'checkbox');
			$this->lib->put_option('hide_attachments_type_menu', $hide_attachments_type_menu);
			$hide_other_posts_comments = $this->lib->get_request_var('hide_other_posts_comments', 'post', 'checkbox');
			$this->lib->put_option('hide_other_posts_comments', $hide_other_posts_comments);
   
			$exclude_custom_post_types = array();
			if (isset($_POST['exclude_custom_post_types']) && is_array($_POST['exclude_custom_post_types']) && count($_POST['exclude_custom_post_types'])>0) {
				// validate array content comparing with real custom post types
				foreach ($_POST['exclude_custom_post_types'] as $custom_post_type) {
					if (isset($post_types[$custom_post_type])) {
						$exclude_custom_post_types[] = $custom_post_type;
					}
				}
			}
			$this->lib->put_option('exclude_custom_post_types', $exclude_custom_post_types);
			$this->lib->flush_options();
			$this->lib->show_message('Options are updated');
		} else { // get options from the options storage
			$select_uploaded_to_this_post = $this->lib->get_option('select_uploaded_to_this_post', 1);
			$hide_attachments_type_menu = $this->lib->get_option('hide_attachments_type_menu', 1);
   $hide_other_posts_comments = $this->lib->get_option('hide_other_posts_comments', 1);
			$exclude_custom_post_types = $this->lib->get_option('exclude_custom_post_types', array());
		}
		
		require_once(VOPMO_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'settings-template.php');
	}
	// end of settings()
	 
	
	// execute on plugin activation
	function setup() {
		
	}

	// end of setup()
	// execute on plugin deactivation
	function cleanup() {
		
	}

	// end of setup()

	/**
	 * Show only posts and media related to logged in author
	 * Plugin respects the query var: suppress_filters.
	 * If you need to make a query without it being filtered, use $wp_query->set ("suppress_filters", true);
	 * 
	 * @global object $current_user
	 * @param object $query
	 */
	public function query_set_only_author($query) {

		global $current_user, $pagenow;

  if (!($pagenow == 'edit.php' || $pagenow == 'upload.php' || 
        ($pagenow=='admin-ajax.php' && !empty($_POST['action']) && $_POST['action']=='query-attachments'))) {
     return;
  }
  
		// do not limit user with Administrator role
		if (current_user_can('administrator')) {
			return;
		}    

		$exclude_custom_post_types = $this->lib->get_option('exclude_custom_post_types', array());				
		$post_type = $query->get('post_type');
		if (in_array($post_type, $exclude_custom_post_types)) {
			return;
		}

		$suppressing_filters = $query->get('suppress_filters'); // Filter suppression on?

		if (!$suppressing_filters && is_admin() && current_user_can('edit_posts') && !current_user_can('edit_others_posts')) {
			$query->set('author', $current_user->ID);

			add_filter('views_edit-post', array(&$this, 'fix_post_counts'));
			add_filter('views_upload', array(&$this, 'fix_media_counts'));
		}
	}

	// end of query_set_only_author

	/**
	 * Fix post counts after filtering by its author
	 * 
	 * @global type $current_user
	 * @global type $wp_query
	 * @param type $views
	 * @return type 
	 */
	public function fix_post_counts($views) {
		global $current_user, $wp_query;

		unset($views['mine']);
		$types = array(
			array('status' => NULL),
			array('status' => 'publish'),
			array('status' => 'draft'),
			array('status' => 'pending'),
			array('status' => 'trash')
		);
		foreach ($types as $type) {
			$query = array(
				'author' => $current_user->ID,
				'post_type' => 'post',
				'post_status' => $type['status']
			);
			$result = new WP_Query($query);
			if ($type['status'] == NULL):
				$class = (empty($wp_query->query_vars['post_status']) || $wp_query->query_vars['post_status'] == NULL) ? ' class="current"' : '';
				$views['all'] = sprintf('<a href="%s"' . $class . '>' . __('All', 'vopmo') . ' <span class="count">(%d)</span></a>', admin_url('edit.php?post_type=post'), $result->found_posts);
			elseif ($type['status'] == 'publish'):
				$class = (!empty($wp_query->query_vars['post_status']) && $wp_query->query_vars['post_status'] == 'publish') ? ' class="current"' : '';
				$views['publish'] = sprintf('<a href="%s"' . $class . '>' . __('Published', 'vopmo') . ' <span class="count">(%d)</span></a>', admin_url('edit.php?post_status=publish&post_type=post'), $result->found_posts);
			elseif ($type['status'] == 'draft'):
				$class = (!empty($wp_query->query_vars['post_status']) && $wp_query->query_vars['post_status'] == 'draft') ? ' class="current"' : '';
				$views['draft'] = sprintf('<a href="%s"' . $class . '>' . __('Drafts', 'vopmo') . ' <span class="count">(%d)</span></a>', admin_url('edit.php?post_status=draft&post_type=post'), $result->found_posts);
			elseif ($type['status'] == 'pending'):
				$class = (!empty($wp_query->query_vars['post_status']) && $wp_query->query_vars['post_status'] == 'pending') ? ' class="current"' : '';
				$views['pending'] = sprintf('<a href="%s"' . $class . '>' . __('Pending', 'vopmo') . ' <span class="count">(%d)</span></a>', admin_url('edit.php?post_status=pending&post_type=post'), $result->found_posts);
			elseif ($type['status'] == 'trash'):
				$class = (!empty($wp_query->query_vars['post_status']) && $wp_query->query_vars['post_status'] == 'trash') ? ' class="current"' : '';
				$views['trash'] = sprintf('<a href="%s"' . $class . '>' . __('Trash', 'vopmo') . ' <span class="count">(%d)</span></a>', admin_url('edit.php?post_status=trash&post_type=post'), $result->found_posts);
			endif;
		}
		return $views;
	}

	// end of fix_post_counts()
	// Fix media counts
	public function fix_media_counts($views) {
		global $wpdb, $current_user, $post_mime_types, $avail_post_mime_types;
		$views = array();
		$_num_posts = array();
		$count = $wpdb->get_results("
        SELECT post_mime_type, COUNT( * ) AS num_posts 
        FROM $wpdb->posts 
        WHERE post_type = 'attachment' 
        AND post_author = $current_user->ID 
        AND post_status != 'trash' 
        GROUP BY post_mime_type
    ", ARRAY_A);
		foreach ($count as $row)
			$_num_posts[$row['post_mime_type']] = $row['num_posts'];
		if (!empty($_num_posts)) {
			$_total_posts = array_sum($_num_posts);
		} else {
			$_total_posts = 0;
		}
		$detached = isset($_REQUEST['detached']) || isset($_REQUEST['find_detached']);
		if (!isset($total_orphans))
			$total_orphans = $wpdb->get_var("
            SELECT COUNT( * ) 
            FROM $wpdb->posts 
            WHERE post_type = 'attachment'
            AND post_author = $current_user->ID 
            AND post_status != 'trash' 
            AND post_parent < 1
        ");
		$matches = wp_match_mime_types(array_keys($post_mime_types), array_keys($_num_posts));
		foreach ($matches as $type => $reals)
			foreach ($reals as $real)
				$num_posts[$type] = ( isset($num_posts[$type]) ) ? $num_posts[$type] + $_num_posts[$real] : $_num_posts[$real];
		$class = ( empty($_GET['post_mime_type']) && !$detached && !isset($_GET['status']) ) ? ' class="current"' : '';
		$views['all'] = "<a href='upload.php'$class>" . sprintf(__('All <span class="count">(%s)</span>'), number_format_i18n($_total_posts)) . '</a>';
		foreach ($post_mime_types as $mime_type => $label) {
			$class = '';
			if (!wp_match_mime_types($mime_type, $avail_post_mime_types))
				continue;
			if (!empty($_GET['post_mime_type']) && wp_match_mime_types($mime_type, $_GET['post_mime_type']))
				$class = ' class="current"';
			if (!empty($num_posts[$mime_type]))
				$views[$mime_type] = "<a href='upload.php?post_mime_type=$mime_type'$class>" . sprintf(translate_nooped_plural($label[2], $num_posts[$mime_type]), $num_posts[$mime_type]) . '</a>';
		}
		$views['detached'] = '<a href="upload.php?detached=1"' . ( $detached ? ' class="current"' : '' ) . '>' . sprintf(__('Unattached <span class="count">(%s)</span>'), $total_orphans) . '</a>';
		return $views;
	}

	// end of media_counts()

	/**
	 * set author parameter for query to current user - it will limit comments by post belongs to its author only
	 * 
	 * @global object $current_user
	 * @param object $query
	 */
	public function author_posts_comments_only($query) {
		global $current_user;

		// do not limit user with Administrator role
		if (current_user_can('administrator')) {
			return;
		}

		$query->query_vars['post_author'] = $current_user->ID;
	}

	// end of author_posts_comments_only()

	/**
	 * Add javascript snippet to automatically select "Uploaded to this post" item from drop-down list at Insert Media - Media Library dialog
	 */
	public function select_uploaded_to_this_post() {
		?>
		<script>
			jQuery(function($) {
				var called = 0;
				$('#wpcontent').ajaxStop(function() {
					if (0 == called) {
						$('[value="uploaded"]').attr('selected', true).parent().trigger('change');
						called = 1;
					}
				});
			});
		</script>
		<?php

	}

	// select_uploaded_to_this_post

	/**
	 * Loads style to hide media scope selection drop-down box at Media Library
	 * 
	 * @param string $hook
	 * @return void 
	 */
	public function admin_css($hook) {

		if ('post.php' != $hook && 'post-new.php' != $hook) {
			return;
		}

		if (current_user_can('edit_others_posts')) {
			return;
		}

		wp_register_style('vopmo_admin_css', VOPMO_PLUGIN_URL . 'css/admin.css', array(), null, 'screen');
		wp_enqueue_style('vopmo_admin_css');
	}
	// end of admin_css()
 
}
// end of class View_Own_Posts_Media_Only
