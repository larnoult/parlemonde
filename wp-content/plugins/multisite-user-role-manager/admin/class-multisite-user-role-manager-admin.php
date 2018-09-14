<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://ozthegreat.io
 * @since      1.0.0
 *
 * @package    Multisite_User_Role_Manager
 * @subpackage Multisite_User_Role_Manager/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Multisite_User_Role_Manager
 * @subpackage Multisite_User_Role_Manager/admin
 * @author     OzTheGreat <oz@ozthegreat.io>
 */
class Multisite_User_Role_Manager_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register all actions and hooks.
	 *
	 * @access public
	 * @return null
	 */
	public function actions() {
		global $pagenow;

		/**
		 * By default only super-admins can use this plugin.
		 * If you want to allow other user levels to use it then
		 * use this filter to enable it
		 * @param bool Whether the current user is a super admin or not
		 */
		if ( ! apply_filters( 'wpmuurm_user_allowed', is_super_admin() ) ||
			( 'user-edit.php' != $pagenow && 'profile.php' != $pagenow && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) )
		)
			return;

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// Ajax endpoints
		add_action( 'wp_ajax_get_user_blogs', array( $this, 'ajax_get_user_blogs' ) );
		add_action( 'wp_ajax_reassign_user_blog_posts', array( $this, 'ajax_reassign_user_blog_posts' ) );
		add_action( 'wp_ajax_set_user_blog_roles', array( $this, 'ajax_set_user_blog_roles' ) );
		add_action( 'wp_ajax_remove_user_from_blog', array( $this, 'ajax_remove_user_from_blog' ) );
		add_action( 'wp_ajax_get_blogs_wo_user', array( $this, 'ajax_get_blogs_wo_user' ) );
		add_action( 'wp_ajax_get_blog_roles', array( $this, 'ajax_get_blog_roles' ) );
		add_action( 'wp_ajax_add_user_to_blog', array( $this, 'ajax_add_user_to_blog' ) );

		// User profile HTML
		add_action( 'show_user_profile', array( $this, 'template_manage_user_roles' ), 1 );
		add_action( 'edit_user_profile', array( $this, 'template_manage_user_roles' ), 1 );

		// Ajax template
		add_action( 'admin_footer', array( $this, 'template_manage_user_blogs_roles_popup' ) );
		add_action( 'admin_footer', array( $this, 'template_user_blogs_roles_row' ) );
		add_action( 'admin_footer', array( $this, 'template_add_user_to_blog' ) );
		add_action( 'admin_footer', array( $this, 'template_user_blog_remove_confirm' ) );
		add_action( 'admin_footer', array( $this, 'template_blog_roles_options' ) );
		add_action( 'admin_footer', array( $this, 'template_notification' ) );
		add_action( 'admin_footer', array( $this, 'template_ajax_spinner' ) );
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/multisite-user-role-manager-admin.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'jqueryui-editable', dirname( plugin_dir_url( __FILE__ ) ) . '/assets/jqueryui-editable/css/jqueryui-editable.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		$js_filename = ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ? 'js/multisite-user-role-manager-admin.js' : 'js/multisite-user-role-manager-admin.min.js';

		// The main JS file and dependencies
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . $js_filename,
			array( 'jquery', 'jquery-ui-core', 'jquery-effects-core', 'jquery-effects-slide', 'wp-util' ),
			$this->version,
			false
		);

		wp_enqueue_script( 'jqueryui-editable', dirname( plugin_dir_url( __FILE__ ) ) . '/assets/jqueryui-editable/js/jqueryui-editable.min.js',
			array( 'jquery', 'jquery-ui-core', 'jquery-ui-button', 'jquery-ui-tooltip' ),
			$this->version,
			false
		);

		// We need thickbox
		add_thickbox();

		// The ID of the user we're editing
		global $user_id;
		$this->user = new WP_User( $user_id );

		wp_localize_script(
			$this->plugin_name,
			'wpmuurm',
			apply_filters( 'wpmuurm_localize',
				array(
					'user_id' => $user_id,
					'nonce'   => wp_create_nonce( 'wpmuurm-ajax-nonce' ),
				)
			)
		);
	}

	/**
	 * Get all blogs that the user is a member of and returns
	 * a json array of blogs and roles
	 *
	 * @access public
	 * @return string
	 */
	public function ajax_get_user_blogs() {
		if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX )
			return;

		// Verify nonce
		$this->verify_ajax_nonce();

		// Verify permissions
		$this->verify_ajax_super_admin();

		if ( empty( $_POST['user_id'] ) || ! $user_id = absint( $_POST['user_id'] ) ) {
			wp_send_json_error( array( array( 'type' => 'error', 'message' => esc_html__( 'Invalid User ID', 'multisite-user-role-manager' ) ) ) );
		}

		$output = array();

		/**
		 * Filter the list of blogs returned for a particular user
		 * @param array All the blogs the user is a member of
		 * @param int   $user_id The user ID currently being edited
		 */
		$user_blogs = apply_filters( 'wpmuurm_user_blogs', get_blogs_of_user( $user_id ), $user_id );

		// Cycle through each blog and get the user's
		// roles at each one
		foreach ( $user_blogs as $user_blog ) {

			switch_to_blog( $user_blog->userblog_id  );
			$user_at_different_blog = new WP_User( $user_id, $user_blog->userblog_id );

			// Decode the blog name
			$user_blog->blogname = wp_kses_decode_entities( $user_blog->blogname );

			// Format the blog roles so they can be used by the JS
			$formatted_blog_roles = array();
			foreach ( $blog_roles = get_editable_roles() as $role_slug => $role_details ) {
				$formatted_blog_roles[] = array(
					'text'  => $role_details['name'],
					'value' => $role_slug,
				);
			}

			$output[] = array(
				'blog'       => $user_blog,
				'blog_roles' => $formatted_blog_roles,
				'user_roles' => $user_at_different_blog->roles,
			);
			restore_current_blog();
		}

		wp_send_json_success( apply_filters( 'wpmuurm_ajax_get_user_blogs_output', $output, $user_id ) );
	}

	/**
	 * Sets a user's roles for a site
	 *
	 * @access public
	 * @return null
	 */
	public function ajax_set_user_blog_roles() {
		if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX )
			return;

		// Verify nonce
		$this->verify_ajax_nonce();

		// Verify permissions
		$this->verify_ajax_super_admin();

		$new_roles = ! empty( $_POST['value'] ) ? (array) $_POST['value'] : array();

		$errors = array();
		if ( empty( $_POST['blog_id'] ) || ! $blog_id = absint( $_POST['blog_id'] ) ) {
			$errors[] = array( 'type' => 'error', 'message' => esc_html__( 'Invalid Blog ID', 'multisite-user-role-manager' ) );
		}

		if ( empty( $_POST['user_id'] ) || ! $user_id = absint( $_POST['user_id'] ) ) {
			$errors[] = array( 'type' => 'error', 'message' => esc_html__( 'Invalid User ID', 'multisite-user-role-manager' ) );
		}

		if ( ! empty( $errors ) ) {
			wp_send_json_error( $errors );
		}

		switch_to_blog( $blog_id );

		$blog_roles = get_editable_roles();

		$roles = array_intersect( array_keys( $blog_roles ), $new_roles );

		$user = new WP_User( $user_id );

		/**
		 * The array of new roles being applied to the user
		 * @param Array   The new roles for the user
		 * @param WP_User The user on the blog
		 * @param int     The blog ID currently being edited
		 */
		$roles = apply_filters( 'wpmuurm_user_new_roles', $roles, $user, $blog_id );

		/**
		 * Do an action before the user is updated with their new roles on
		 * @param Array   The new roles for the user
		 * @param WP_User The user on the blog
		 * @param int     The blog ID currently being edited
		 */
		do_action( 'wpmuurm_pre_set_user_blog_roles', $user, $roles, $blog_id );

		// Remove all the existing roles for the user
		if ( ! empty( $user->roles ) && is_array( $user->roles ) ) {
			foreach ( $user->roles as $role ) {
				$user->remove_role( $role );
			}
		}

		// Add the new roles to the user
		if ( ! empty( $roles ) ) {
			foreach ( $roles as $role ) {
				$user->add_role( $role );
			}
		}

		/**
		 * Do an action after the user is updated with their new roles on
 		 * @param Array   The new roles for the user
 		 * @param WP_User The user on the blog
 		 * @param int     The blog ID currently being edited
 		 */
		do_action( 'wpmuurm_post_set_user_blog_roles', $user, $roles, $blog_id );

		restore_current_blog();

		wp_send_json_success();
	}
	/**
	 * Returns all the users for a blog.
	 * This is used when we're removing a user from a blog and we need to
	 * re-attribute the posts
	 *
	 * @todo Take better account of large user lists
	 *
	 * @access public
	 * @return string
	 */

	public function ajax_reassign_user_blog_posts() {
		if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX )
			return;

		// Verify nonce
		$this->verify_ajax_nonce();

		// Verify permissions
		$this->verify_ajax_super_admin();

		$errors = array();
		if ( empty( $_POST['blog_id'] ) || ! $blog_id = absint( $_POST['blog_id'] ) ) {
			$errors[] = array( 'type' => 'error', 'message' => esc_html__( 'Invalid Blog ID', 'multisite-user-role-manager' ) );
		}

		if ( empty( $_POST['user_id'] ) || ! $user_id = absint( $_POST['user_id'] ) ) {
			$errors[] = array( 'type' => 'error', 'message' => esc_html__( 'Invalid User ID', 'multisite-user-role-manager' ) );
		}

		if ( ! empty( $errors ) ) {
			wp_send_json_error( $errors );
		}

		switch_to_blog( $blog_id );

		$output_data = array(
			'blog_id'     => get_current_blog_id(),
			'posts_count' => count_user_posts( $user_id ),
		);

		if ( $output_data['posts_count'] > 0 ) {

			$args = array(
				'orderby' => 'display_name',
				'fields'  => array( 'ID', 'display_name', 'user_email' ),
			);

			$users = get_users( $args );

			// Cycle through all the users and format the data ready for output
			$output_data['users'] = array( 'id' => 'none', 'name' => esc_html__( 'None', 'multisite-user-role-manager' ) );
			foreach ( $users as $user ) {
				$output_data['users'][] = array( 'id' => $user->ID, 'name' => $user->user_email );
			}

		}

		wp_send_json_success( apply_filters( 'wpmuurm_ajax_reassign_user_blog_posts_output', $output_data ) );
	}

	/**
	 * Ajax method to remove a user from a certain blog
	 *
	 * @todo stricter user/blog validation
	 * @access public
	 * @return null
	 */
	public function ajax_remove_user_from_blog() {
		if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX )
			return;

		// Verify nonce
		$this->verify_ajax_nonce();

		// Verify permissions
		$this->verify_ajax_super_admin();

		$reassign_id = ! empty( $_POST['reassign_id'] ) ? absint( $_POST['reassign_id'] ) : null;

		$errors = array();
		if ( empty( $_POST['blog_id'] ) || ! $blog_id = absint( $_POST['blog_id'] ) ) {
			$errors[] = array( 'type' => 'error', 'message' => esc_html__( 'Invalid Blog ID', 'multisite-user-role-manager' ) );
		}

		if ( empty( $_POST['user_id'] ) || ! $user_id = absint( $_POST['user_id'] ) ) {
			$errors[] = array( 'type' => 'error', 'message' => esc_html__( 'Invalid User ID', 'multisite-user-role-manager' ) );
		}

		if ( ! empty( $errors ) ) {
			wp_send_json_error( $errors );
		}

		/**
		 * Do an action before removing a user from a blog
		 * @param int      The ID of the user to remove
		 * @param int      The ID of the blog to remove the user from
		 * @param int|null The ID of the user to re-assign posts to (if there are any)
		 */
		do_action( 'wpmuurm_pre_remove_user_from_blog', $user_id, $blog_id, $reassign_id );

		remove_user_from_blog( $user_id, $blog_id, $reassign_id );

		/**
		 * Do an action after removing a user from a blog
		 * @param int      The ID of the user to remove
		 * @param int      The ID of the blog to remove the user from
		 * @param int|null The ID of the user to re-assign posts to (if there are any)
		 */
		do_action( 'wpmuurm_post_remove_user_from_blog', $user_id, $blog_id, $reassign_id );

		wp_send_json_success();
	}

	/**
	 * Outputs an array of all the blogs that the user isn't a member of
	 *
	 * @todo Cache?
	 * @access public
	 * @return array
	 */
	public function ajax_get_blogs_wo_user() {
		if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX )
			return;

		// Verify nonce
		$this->verify_ajax_nonce();

		// Verify permissions
		$this->verify_ajax_super_admin();

		global $wpdb;

		if ( empty( $_POST['user_id'] ) || ! $user_id = absint( $_POST['user_id'] ) ) {
			wp_send_json_error( array( array( 'type' => 'error', 'message' => esc_html__( 'Invalid User ID', 'multisite-user-role-manager' ) ) ) );
		}

		$placeholders = $where = '';

		$user_blogs_ids = array_keys( get_blogs_of_user( absint( $user_id ) ) );

		if ( ! empty( $user_blogs_ids ) ) {
			$placeholders = implode( ', ', array_fill( 0, count( $user_blogs_ids ), '%d' ) );
			$where = "AND blog_id NOT IN ({$placeholders})";
		}

		// We;re doing it this way to avoid any wp_is_large_network() restrictions
		// https://codex.wordpress.org/Function_Reference/wp_is_large_network
		$output_data = $wpdb->get_results(
			$wpdb->prepare( "SELECT blog_id, domain, path FROM {$wpdb->blogs} WHERE 1 = 1 {$where} ORDER BY domain, path", $user_blogs_ids ),
			ARRAY_A
		);

		wp_send_json_success( apply_filters( 'ajax_get_blogs_wo_user_output', $output_data ) );
	}

	/**
	 * Outputs an array of all the editable roles on a blog
	 *
	 * @access public
	 * @return null
	 */
	public function ajax_get_blog_roles() {
		if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX )
			return;

		// Verify nonce
		$this->verify_ajax_nonce();

		// Verify permissions
		$this->verify_ajax_super_admin();

		if ( ! $blog_id = absint( $_POST['blog_id'] ) ) {
			wp_send_json_error( array( array( 'type' => 'error', 'message' => esc_html__( 'Invalid Blog ID', 'multisite-user-role-manager' ) ) ) );
		}

		switch_to_blog( $blog_id );

		$blog_roles = get_editable_roles();

		restore_current_blog();

		$output_data = array( 'roles' => array() );
		foreach ( $blog_roles as $role_slug => $role_details ) {
			$output_data['roles'][] = array(
				'text'  => $role_details['name'],
				'value' => $role_slug,
			);
		}

		wp_send_json_success( apply_filters( 'ajax_get_blog_roles_output', $output_data ) );
	}

	/**
	 * Adds a user to a blog with a specific role
	 *
	 * @todo Stricter validation on role
	 *
	 * @access public
	 * @return null
	 */
	public function ajax_add_user_to_blog() {
		if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX )
			return;

		// Verify nonce
		$this->verify_ajax_nonce();

		// Verify permissions
		$this->verify_ajax_super_admin();

		$errors = array();
		if ( empty( $_POST['blog_id'] ) || ! $blog_id = absint( $_POST['blog_id'] ) ) {
			$errors[] = array( 'type' => 'error', 'message' => esc_html__( 'Invalid Blog ID', 'multisite-user-role-manager' ) );
		}

		if ( empty( $_POST['user_id'] ) || ! $user_id = absint( $_POST['user_id'] ) ) {
			$errors[] = array( 'type' => 'error', 'message' => esc_html__( 'Invalid User ID', 'multisite-user-role-manager' ) );
		}

		if ( ! empty( $errors ) ) {
			wp_send_json_error( $errors );
		}

		$role = ! empty( $_POST['role'] ) ? $_POST['role'] : null;

		/**
		 * Do an action before adding a user to a blog
		 * @param int    The ID of the user to remove
		 * @param int    The ID of the blog to remove the user from
		 * @param string The role to assign to the user
		 */
		do_action( 'wpmuurm_pre_add_user_to_blog', $user_id, $blog_id, $role );

		$status = add_user_to_blog( $blog_id, $user_id, $role );

		/**
		 * Do an action before adding a user to a blog
		 * @param int    The ID of the user to remove
		 * @param int    The ID of the blog to remove the user from
		 * @param string The role to assign to the user
		 */
		do_action( 'wpmuurm_post_add_user_to_blog', $user_id, $blog_id, $role, $status );

		wp_send_json( array( 'success' => $status ) );
	}

	/**
	 * Verifies a nonce passed through ajax. Dies if fails
	 *
	 * @access public
	 * @return boolean
	 */
	public function verify_ajax_nonce() {
		check_ajax_referer( 'wpmuurm-ajax-nonce', 'nonce' );
	}

	/**
	 * Verifies the current user is a network admin.
	 * Echos a JSON error response on failure
	 *
	 * @access public
	 * @return null
	 */
	public function verify_ajax_super_admin() {
		/**
		 * By default only super-admins can use this plugin.
		 * If you want to allow other user levels to use it then
		 * use this filter to enable it
		 * @param bool Whether the current user is a super admin or not
		 */
		if ( ! apply_filters( 'wpmuurm_user_allowed', is_super_admin() ) ) {
			wp_send_json_error( array( array( 'type' => 'error', 'message' => esc_html__( 'You do not have permission to do this', 'multisite-user-role-manager' ) ) ) );
		}
	}

	/**
	 * Outputs the HTML for the link to launch the thickbox for
	 * managing all the user's roles
	 *
	 * @access public
	 * @return null
	 */
	public function template_manage_user_roles( $user ) {
		?>
		<table class="form-table">
			<tr>
				<th><label for=""><?php esc_html_e( 'User Roles', 'multisite-user-role-manager' ); ?></label></th>
				<td>
					<p>
						<a href="#TB_inline?height=auto&width=800&inlineId=wpmuurm-thickbox" class="button button-secondary thickbox wpmuurm-launch" title="<?php echo sprintf( esc_html__( '%s :: Manage Roles', 'multisite-user-role-manager' ), $user->display_name ); ?>">
							<?php esc_html_e( 'Manage Roles', 'multisite-user-role-manager' ); ?>
						</a>
					</p>
					<p class="description"><?php esc_html_e( 'Manage roles across all blogs', 'multisite-user-role-manager' ); ?></p>
				</td>
			</tr>
		</table>
		<?php
	}

	/**
	 * Outputs the HTML for the actual thickbox
	 *
	 * @access public
	 * @return null
	 */
	public function template_manage_user_blogs_roles_popup() {
		?>
		<div id="wpmuurm-thickbox" style="display:none;">
			<div class="wpmuurm-wrapper">
				<div class="notifications"></div>
				<table class="wp-list-table widefat fixed">
					<thead>
						<tr>
							<th width="40"><?php esc_html_e( '#ID', 'multisite-user-role-manager' ); ?></th>
							<th><?php esc_html_e( 'Site Name', 'multisite-user-role-manager' ); ?></th>
							<th><?php esc_html_e( 'URL', 'multisite-user-role-manager' ); ?></th>
							<th><?php esc_html_e( 'Roles', 'multisite-user-role-manager' ); ?></th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td colspan="5"><i><?php esc_html_e( 'Loading blogs...', 'multisite-user-role-manager' ); ?></i></td>
						</tr>
					</tbody>
				</table>
				<div class="add-user-to-blog-wrapper"></div>
			</div>
		</div>
		<?php
	}

	/**
	 * Outputs the HTML for the user-blogs-roles-row template
	 *
	 * @access public
	 * @return null
	 */
	public function template_user_blogs_roles_row() {
		?>
		<script type="text/html" id="tmpl-user-blog-roles-row">
			<tr class="blog-{{ data.blog.userblog_id }}" data-blog-id="{{ data.blog.userblog_id }}">
				<td width="40">{{ data.blog.userblog_id }}</td>
				<td>{{ data.blog.blogname }}</td>
				<td>{{ data.blog.domain }}{{ data.blog.path }}</td>
				<td>
					<a href="#" data-value="{{ data.user_roles }}" roles-editable></a>
				</td>
				<td align="right">
					<a href="#" class="button button-default remove-blog"><?php esc_html_e( 'Remove', 'multisite-user-role-manager' ); ?></a>
				</td>
			</tr>
		</script>
		<?php
	}

	/**
	 * Outputs the HTML for adding a user to a blog
	 *
	 * @access public
	 * @return null
	 */
	public function template_add_user_to_blog() {
		?>
		<script type="text/html" id="tmpl-add-user-to-blog">
			<select class="add-to-blog-blogs">
				<option value=""><?php esc_html_e( 'Add to blog', 'multisite-user-role-manager' ); ?></option>
				<# _.each( data, function( blog ) { #>
					<option value="{{ blog.blog_id }}">{{ blog.domain }}{{ blog.path }}</option>
				<# }) #>
			</select>

			<select class="add-to-blog-roles" disabled>
				<option><?php esc_html_e( 'Role', 'multisite-user-role-manager' ); ?></option>
			</select>
			<a href="#" class="button button-primary add-to-blog-submit"><?php esc_html_e( 'Add to blog', 'multisite-user-role-manager' ); ?></a>
		</script>
		<?php
	}

	/**
	 * Outputs the HTML for the user-blog-remove-confirm template
	 *
	 * @access public
	 * @return null
	 */
	public function template_user_blog_remove_confirm() {
		?>
		<script type="text/html" id="tmpl-user-blog-remove-confirm">
			<tr class="blog-{{ data.blog_id }} confirm-remove" data-blog-id="{{ data.blog_id }}">
				<td></td>
				<td colspan="3">
					<# if( data.posts_count > 0 ) { #>
						<strong>{{ data.posts_count  }}</strong> <?php esc_html_e( 'posts found. Reassign to:', 'multisite-user-role-manager' ); ?>
						<select name="ressaign_to" class="ressaign_to">
							<# _.each( data.users, function( user ) { #>
								<option value="{{ user.id }}">{{ user.name }}</option>
							<# }) #>
						</select>
					<# } else { #>
						<i><?php esc_html_e( 'No posts found for User', 'multisite-user-role-manager' ); ?></i>
					<# } #>
				</td>
				<td align="right">
					<a href="#" class="button button-default cancel-user-blog-removal"><?php esc_html_e( 'Cancel', 'multisite-user-role-manager' ); ?></a>
					<a href="#" class="button button-primary confirm-user-blog-removal"><?php esc_html_e( 'Confirm', 'multisite-user-role-manager' ); ?></a>
				</td>
			</tr>
		</script>
		<?php
	}

	/**
	 * Outputs the HTML for the ajax-loader template
	 *
	 * @access public
	 * @return null
	 */
	public function template_blog_roles_options() {
		?>
		<script type="text/html" id="tmpl-blog-roles-options">
			<# if( data.roles.length > 0 ) { #>
				<# _.each( data.roles, function( role ) { #>
					<option value="{{ role.value }}">{{ role.text }}</option>
				<# }) #>
			<# } else { #>
			<# } #>
		</script>
		<?php
	}

	/**
	 * Outputs the HTML for adding a notification to the thickbox
	 *
	 * @return null
	 */
	public function template_notification() {
		?>
		<script type="text/html" id="tmpl-notification">
			<div class="{{ data.type }}">
				<p>{{ data.message }}</p>
			</div>
		</script>
		<?php
	}

	/**
	 * Outputs the HTML for the ajax-loader template
	 *
	 * @access public
	 * @return null
	 */
	public function template_ajax_spinner() {
		?>
		<script type="text/html" id="tmpl-ajax-spinner">
			<span class="spinner"></span>
		</script>
		<?php
	}

}
