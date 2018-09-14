<?php
/**
 * Including CSS  for addmin setting
 */

if (!class_exists('WbCom_BP_Activity_Filter_Add_Post_Type_Support')) {
	class WbCom_BP_Activity_Filter_Add_Post_Type_Support {
		/**
		 * Constructor
		 */
		public function __construct() {
			//transition_post_status
			add_action( 'transition_post_status', array(&$this, 'bpaf_customize_page_tracking_args'), 999, 3 );
		}

		public function bpaf_customize_page_tracking_args( $new_status, $old_status, $post ) {
			global $bp;
			$post_id = $post->ID;

            // bail out if not published
            if ( 'publish' === $old_status || 'publish' !== $new_status ) return;

            $get_post_type = get_post_type($post_id);
            if( !isset($get_post_type) || empty($get_post_type) ) return;

			$cpt_filter_setting = bp_get_option('bp-cpt-filters-settings');
			$all_posts = $cpt_filter_setting['bpaf_admin_settings'];
			if( isset( $all_posts ) && is_array( $all_posts ) ) {
			foreach( $all_posts as $post_type=>$details ) {
				if( $get_post_type == $post_type ) {
					$post_details = get_post_type_object( $post_type );
					$filter_type = $details['display_type'];
					$post_type_rename = $details['new_label'];
	    			if(empty($post_type_rename)){
		            	$post_type_rename =  $post_details->labels->singular_name;
		            }
					if( 'groups' == $filter_type ) {

						$group_args = array(
							'order' => 'DESC',
							'orderby' => 'date_created'
						);
	                    $allgroups  = groups_get_groups( $group_args );
	                    $groupids = array();
	                    foreach ($allgroups['groups'] as $key => $value) {
	                    	array_push( $groupids, $value->id );
	                    }
						if(!empty($groupids)){
			        		foreach ($groupids as $key => $value) {
					            // post detail for use
					            $post_author = get_the_author_meta( 'nicename', $post->post_author);
					            $post_author = '<a href="'.bp_get_loggedin_user_link().'">'.$post_author.'</a>';
					            $post_title = '<a href="'.get_the_permalink($post_id).'">'.get_the_title($post_id).'</a>';

					            $post_excerpt = wp_trim_words($post->post_content);
					            $post_thumb = get_the_post_thumbnail($post_id);
					            $post_excerpt = $post_thumb.'<br/>'. $post_excerpt;
					            $post_link = get_the_permalink($post_id);
					            $group = groups_get_group( array( 'group_id' => $value) );
					            $group_permalink = trailingslashit( bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/' . $group->slug . '/' ) ;
					            // add activity

					            $action = '<a id="group-' . esc_attr( $group->id ) . '" class="new-group" href="' . bp_get_group_permalink( $group ) . '">' .$group->name . '</a>';
					            $post_action = "";
					            $post_action = apply_filters('bpaf_groups_content_override', $post_author, $post_type_rename, $post_title, $action );
					            if (isset($post_action)) {
					            	$post_action = $post_author.' added a new '.$post_type_rename.', '.$post_title.' in the group '.$action.' about';
					            }

					            $prep_args = array(
					                'id'                => false,                  // Pass an existing activity ID to update an existing entry.
					                'action'            => $post_action,                     // The activity action - e.g. "Jon Doe posted an update"
					                'content'           => $post_excerpt,                     // Optional: The content of the activity item e.g. "BuddyPress is awesome guys!"
					                'component'         => 'groups',                  // The name/ID of the component e.g. groups, profile, mycomponent.
					                'type'              => 'activity_update',                  // The activity type e.g. activity_update, profile_updated.
					                'primary_link'      => $post_link,                     // Optional: The primary URL for this item in RSS feeds (defaults to activity permalink).
					                'user_id'           => bp_loggedin_user_id(),  // Optional: The user to record the activity for, can be false if this activity is not for a user.
					                'item_id'           => $value,                  // Optional: The ID of the specific item being recorded, e.g. a blog_id.
					                'secondary_item_id' => false,                  // Optional: A second ID used to further filter e.g. a comment_id.
					                'recorded_time'     => bp_core_current_time(), // The GMT time that this activity was recorded.
					                'hide_sitewide'     => true,                  // Should this be hidden on the sitewide activity stream?
					                'is_spam'           => false,                  // Is this activity item to be marked as spam?
					                'error_type'        => 'bool'
					            );
					            bp_activity_add($prep_args);
			        		}
			        	}
					} else if( 'main_activity' == $filter_type ) {
			            // post detail for use

			            $post_author = get_the_author_meta( 'nicename', $post->post_author);
			            $post_author = '<a href="'.bp_get_loggedin_user_link().'">'.$post_author.'</a>';
			            $post_title = '<a href="'.get_the_permalink($post_id).'">'.get_the_title($post_id).'</a>';

			            $post_excerpt = wp_trim_words($post->post_content);
			            $post_thumb = get_the_post_thumbnail($post_id);
			            $post_excerpt = $post_thumb.'<br/>'. $post_excerpt;
			            $post_link = get_the_permalink($post_id);
			            $post_action = "";
			            $post_action = apply_filters('bpaf_main_activity_content_override', $post_author, $post_type_rename, $post_title);
			            if (isset($post_action)) {
			            	$post_action = $post_author.' added a new '.$post_type_rename.', '.$post_title.' about';
			            }
			            // add activity
			            $prep_args = array(
			                'id'                => false,                  // Pass an existing activity ID to update an existing entry.
			                'action'            => $post_action,                     // The activity action - e.g. "Jon Doe posted an update"
			                'content'           => $post_excerpt,                     // Optional: The content of the activity item e.g. "BuddyPress is awesome guys!"
			                'component'         => 'activity',                  // The name/ID of the component e.g. groups, profile, mycomponent.
			                'type'              => 'new_blog_post',                  // The activity type e.g. activity_update, profile_updated.
			                'primary_link'      => $post_link,                     // Optional: The primary URL for this item in RSS feeds (defaults to activity permalink).
			                'user_id'           => bp_loggedin_user_id(),  // Optional: The user to record the activity for, can be false if this activity is not for a user.
			                'item_id'           => $post_id,                  // Optional: The ID of the specific item being recorded, e.g. a blog_id.
			                'secondary_item_id' => false,                  // Optional: A second ID used to further filter e.g. a comment_id.
			                'recorded_time'     => bp_core_current_time(), // The GMT time that this activity was recorded.
			                'hide_sitewide'     => false,                  // Should this be hidden on the sitewide activity stream?
			                'is_spam'           => false,                  // Is this activity item to be marked as spam?
			                'error_type'        => 'bool'
			            );
			            bp_activity_add($prep_args);
					} else {

					}

				}
			}
		}

		}

	}
}
if (class_exists('WbCom_BP_Activity_Filter_Add_Post_Type_Support')) {
	$support_includer = new WbCom_BP_Activity_Filter_Add_Post_Type_Support();
}
