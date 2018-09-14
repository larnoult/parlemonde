<?php

class QuizMaster_Helper_CopyPost {

	private $postTypes = array();

	public function __construct( $postTypes = array() ) {
		if( empty( $postTypes )) {
			$this->postTypes = false;
		} else {
			$this->postTypes = $postTypes;
		}
		$this->init();
	}

	public function init() {
		add_filter( 'post_row_actions', array( $this, 'copyPostLink' ), 10, 2 );
		add_action( 'admin_action_copy_post', array( $this, 'copyPost' ));
	}

	/*
 * Function creates post copy as a draft and redirects then to the edit post screen
 */
	public function copyPost() {

		global $wpdb;

		if (! ( isset( $_GET['post']) || isset( $_POST['post'])  || ( isset($_REQUEST['action']) && 'copyPost' == $_REQUEST['action'] ) ) ) {
			wp_die('No post to copy has been supplied!');
		}

		/*
		 * get the original post id
		 */
		$post_id = (isset($_GET['post']) ? absint( $_GET['post'] ) : absint( $_POST['post'] ) );
		/*
		 * and all the original post data then
		 */
		$post = get_post( $post_id );

		/*
		 * if you don't want current user to be the new post author,
		 * then change next couple of lines to this: $new_post_author = $post->post_author;
		 */
		$current_user = wp_get_current_user();
		$new_post_author = $current_user->ID;

		/*
		 * if post data exists, create the post copy
		 */
		if (isset( $post ) && $post != null) {

			/*
			 * new post data array
			 */
			$args = array(
				'comment_status' => $post->comment_status,
				'ping_status'    => $post->ping_status,
				'post_author'    => $new_post_author,
				'post_content'   => $post->post_content,
				'post_excerpt'   => $post->post_excerpt,
				'post_name'      => $post->post_name,
				'post_parent'    => $post->post_parent,
				'post_password'  => $post->post_password,
				'post_status'    => 'draft',
				'post_title'     => $post->post_title,
				'post_type'      => $post->post_type,
				'to_ping'        => $post->to_ping,
				'menu_order'     => $post->menu_order
			);

			/*
			 * insert the post by wp_insert_post() function
			 */
			$new_post_id = wp_insert_post( $args );

			/*
			 * get all current post terms ad set them to the new post draft
			 */
			$taxonomies = get_object_taxonomies($post->post_type); // returns array of taxonomy names for post type, ex array("category", "post_tag");
			foreach ($taxonomies as $taxonomy) {
				$post_terms = wp_get_object_terms($post_id, $taxonomy, array('fields' => 'slugs'));
				wp_set_object_terms($new_post_id, $post_terms, $taxonomy, false);
			}

			/*
			 * copy all post meta just in two SQL queries
			 */
			$post_meta_infos = $wpdb->get_results("SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$post_id");
			if (count($post_meta_infos)!=0) {
				$sql_query = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
				foreach ($post_meta_infos as $meta_info) {
					$meta_key = $meta_info->meta_key;
					$meta_value = addslashes($meta_info->meta_value);
					$sql_query_sel[]= "SELECT $new_post_id, '$meta_key', '$meta_value'";
				}
				$sql_query.= implode(" UNION ALL ", $sql_query_sel);
				$wpdb->query($sql_query);
			}

			/*
			 * finally, redirect to the edit post screen for the new draft
			 */
			wp_redirect( admin_url( 'post.php?action=edit&post=' . $new_post_id ) );
			exit;

		} else {

			wp_die('Post creation failed, could not find original post: ' . $post_id);

		}

	}


	/*
	 * Add the copy link to action list for post_row_actions
	 */
	function copyPostLink( $actions, $post ) {

		if (current_user_can('edit_posts') && $this->postTypes == false || in_array( $post->post_type, $this->postTypes )) {
			$actions['copy'] = '<a href="admin.php?action=copy_post&amp;post=' . $post->ID . '" title="Copy" rel="permalink">Copy</a>';
		}

		return $actions;

	}

}
