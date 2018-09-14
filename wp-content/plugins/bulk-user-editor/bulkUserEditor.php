<?php

/**
 * Plugin Name: Bulk User/Posts/CPT/GravityForms Fields Editor
 * Plugin URI: http://termel.fr/
 * Description: Bulk Edit User/Posts/CPT/GravityForms Fields
 * Version: 1.7.2
 * Author: munger41
 * Author URI: http://www.termel.fr
 */
if (! defined('ABSPATH')) {
    die();
}

function bulkusereditor_log($message)
{
    if (WP_DEBUG === true) {
        if (is_array($message) || is_object($message)) {
            error_log(print_r($message, true));
        } else {
            error_log($message);
        }
    }
}
if (! class_exists('BulkUserEditor')) {

    class BulkUserEditor
    {

        private $extraUserFields = array('user_url');
        function __construct()
        {
            if (is_multisite()) {
                add_action('network_admin_menu', array(
                    $this,
                    'add_user_admin_menu'
                ));
            } else {
                add_action('admin_menu', array(
                    $this,
                    'add_user_admin_menu'
                ));
            }
            
            add_action('admin_menu', array(
                $this,
                'add_post_admin_menu'
            ));
            
            add_action("admin_enqueue_scripts", array(
                $this,
                "bulkusereditor_load_scripts"
            ));
            
            add_action('wp_ajax_bue_modify_meta', array(
                $this,
                'bue_modify_meta'
            ));
            
            add_action('wp_ajax_bue_modify_categories', array(
                $this,
                'bue_modify_categories'
            ));
            
            add_action('wp_ajax_bue_get_gf_form_fields', array(
                $this,
                'bue_get_gf_form_fields'
            ));
            
            add_action('wp_ajax_bue_modify_gf_fields', array(
                $this,
                'bue_modify_gf_fields'
            ));
            
            add_filter('gform_addon_navigation', array(
                $this,
                'add_gf_menu_item'
            ));
        }

        function add_gf_menu_item($menu_items)
        {
            $menu_items[] = array(
                "name" => "bulk_edit_fields",
                "label" => "Bulk Edit Fields",
                "callback" => array(
                    $this,
                    "bulk_edit_fields_submenu_handler"
                ),
                "permission" => "edit_posts"
            );
            return $menu_items;
        }

        function bue_modify_gf_fields()
        {
            bulkusereditor_log($_POST);
            /*
             * [action] => bue_modify_gf_fields
             * [src_key] => text
             * [new_value] => ghjk
             * [gf_form_key] => 6
             * [gf_form_field_key_target] => 9
             * [gf_form_field_key_cond] => 20
             * [condition_value] => ghjkhgk
             * [start_date] =>
             * [end_date] =>
             * [bue_dry_run] => 1
             */
            
            $gf_form_key = sanitize_text_field($_POST['gf_form_key']);
            $new_value = sanitize_text_field($_POST['new_value']);
            $gf_form_field_key_target = sanitize_text_field($_POST['gf_form_field_key_target']);
            $gf_form_field_key_cond = sanitize_text_field($_POST['gf_form_field_key_cond']);
            $condition_value = sanitize_text_field($_POST['condition_value']);
            $custom_search_criteria = array (
					/*'status' => 'active',*/
					'field_filters' => array (
							/*array (
									'key' => 'id',
									'value' => strval($gf_form_key)
							),*/
							array(
                        'key' => strval($gf_form_field_key_cond),
                        'value' => strval($condition_value)
                    )
                )
            );
            
            bulkusereditor_log("Search crit : ");
            bulkusereditor_log($custom_search_criteria);
            $sorting = null;
            $maxentries = (isset($_POST['gf_max_entries']) && $_POST['gf_max_entries'] > 0) ? $_POST['gf_max_entries'] : 1500;
            
            $paging = array(
                'offset' => 0,
                'page_size' => $maxentries
            );
            // bulkusereditor_log(var_export($paging, true));
            // bulkusereditor_log($gf_form_key. ' - ' . $custom_search_criteria. ' - ' . $sorting . ' - ' . $paging);
            // $search_criteria = null;
            $entries = GFAPI::get_entries($gf_form_key, $custom_search_criteria, $sorting, $paging);
            // $entries = GFAPI::get_entries ( $form_id );
            $nbOfEntries = count($entries);
            bulkusereditor_log("# " . $nbOfEntries . "entries found");
            
            foreach ($entries as $entry) {
                // $result = GFAPI::update_entry_field( $entry_id, $input_id, $value );
                $entry_id = rgar($entry, 'id');
                $input_id = $gf_form_field_key_target; // rgar($entry,'id');
                $value = $new_value; // rgar($entry,'id');
                
                if (! $_POST['bue_dry_run']) {
                    bulkusereditor_log("UPDATE FIELD $entry_id, $input_id, $value");
                    $result = GFAPI::update_entry_field($entry_id, $input_id, $value);
                } else {
                    bulkusereditor_log("# DRY RUN $entry_id, $input_id, $value");
                }
            }
            
            $changeResults = array(
                "added" => $nbOfEntries,
                'updated' => $nbOfEntries,
                'removed' => $nbOfEntries,
                'message' => 'GF forms'
            );
            if ($changeResults['added'] > 0 || $changeResults['updated'] > 0 || $changeResults['removed'] > 0) {
                $result = 'Updated: ' . $changeResults['updated'];
                $result .= '  ';
                $result .= $changeResults['message'];
                bulkusereditor_log($result);
            }
            echo $result;
            wp_die();
        }

        function bue_modify_categories()
        {
            bulkusereditor_log($_POST);
            $action = sanitize_text_field($_POST['catAction']);
            $dest = sanitize_text_field($_POST['catDest']);
            $src = sanitize_text_field($_POST['catSrc']);
            $postType = sanitize_text_field($_POST['postType']);
            
            bulkusereditor_log($_POST['start_date']);
            $dateFormat = 'Y-m-d';
            $start = isset($_POST['start_date']) && ! empty($_POST['start_date']) ? sanitize_text_field($_POST['start_date']) : date($dateFormat, strtotime("1 September 1970"));
            // $startOfTime = date('Ymd', 0);//strtotime('1970-01-01');// date('Ymd','19700101');
            bulkusereditor_log($start);
            
            $end = isset($_POST['end_date']) && ! empty($_POST['end_date']) ? sanitize_text_field($_POST['end_date']) : date($dateFormat);
            ;
            bulkusereditor_log($_POST['end_date']);
            bulkusereditor_log($end);
            $dates = array(
                'START' => $start,
                'END' => $end
            );
            
            bulkusereditor_log($action . ' | ' . $dest . ' | ' . $src . ' | ' . implode('/', $dates));
            // $result = DOFF_Utils::doff_add_or_update_test_to_csv($id, $pattern, $da, $dat, $df, $ia, $if);
            $changeResults = $this->handle_bulk_category_change($action, $postType, $dest, $src, $dates);
            $result = '';
            
            if ($changeResults['added'] > 0 || $changeResults['updated'] > 0 || $changeResults['removed'] > 0) {
                $result = 'Added: ' . $changeResults['added'] . ' / Updated: ' . $changeResults['updated'] . ' / Removed: ' . $changeResults['removed'];
                $result = '<br/>';
                $result = $changeResults ['message'];
				bulkusereditor_log ( $result );
			}
			echo $result;
			wp_die ();
		}
		function bue_modify_meta() {
			bulkusereditor_log ( $_POST );
			// action: "bue_modify_meta", src_key: "gf", new_value: "", gf_form_key: "2", gf_form_field_key: "1", meta_key: "first_name", user_role: "", user_id: "", start_date: "", end_date: ""
			$source = sanitize_text_field ( $_POST ['src_key'] );
			$gf_form_id = sanitize_text_field ( $_POST ['gf_form_key'] );
			$gf_field_id = sanitize_text_field ( $_POST ['gf_form_field_key'] );
			$gf_max_entries = sanitize_text_field ( $_POST ['gf_form_max_entries'] );
			$val = sanitize_text_field ( $_POST ['new_value'] );
			$meta = sanitize_text_field ( $_POST ['meta_key'] );
			$role = sanitize_text_field ( $_POST ['user_role'] );
			$user = sanitize_text_field ( $_POST ['user_id'] );
			$blank_only = ('blank_only' == sanitize_text_field ( $_POST ['bue_check_updateifblank'] ));
			$dry_run = isset ( $_POST ['bue_dry_run'] ) ? boolval ( sanitize_text_field ( $_POST ['bue_dry_run'] ) ) : false;
			bulkusereditor_log ( $dry_run ? '###### DRY RUN ######' : '++++++ REAL RUN ++++++' );
			bulkusereditor_log ( $_POST ['start_date'] );
			$dateFormat = 'Y-m-d';
			$start = isset ( $_POST ['start_date'] ) && ! empty ( $_POST ['start_date'] ) ? sanitize_text_field ( $_POST ['start_date'] ) : date ( $dateFormat, strtotime ( "1 September 1970" ) );
			// $startOfTime = date('Ymd', 0);//strtotime('1970-01-01');// date('Ymd','19700101');
			bulkusereditor_log ( $start );
			
			$end = isset ( $_POST ['end_date'] ) && ! empty ( $_POST ['end_date'] ) ? sanitize_text_field ( $_POST ['end_date'] ) : date ( $dateFormat );
			;
			bulkusereditor_log ( $_POST ['end_date'] );
			bulkusereditor_log ( $end );
			$dates = array (
					'START' => $start,
					'END' => $end
			);
			bulkusereditor_log ( $val . ' | ' . $meta . ' | ' . $role . ' | ' . $user );
			// $result = DOFF_Utils::doff_add_or_update_test_to_csv($id, $pattern, $da, $dat, $df, $ia, $if);
			$result = '';
			if ($source === 'gf' && $gf_form_id && $gf_field_id) {
				ini_set ( 'max_execution_time', 300 );
				bulkusereditor_log ( 'change meta from gf value dynamically ' + $gf_form_id + ' / ' + $gf_field_id );
				$val = array (
						'gf_form_id' => $gf_form_id,
						'gf_field_id' => $gf_field_id,
						'gf_max_entries' => $gf_max_entries
				);
				
				$changeResults = $this->handle_bulk_change ( $val, $meta, $role, $user, $dates, $blank_only, $dry_run );
			} else {
				$changeResults = $this->handle_bulk_change ( $val, $meta, $role, $user, $dates, $blank_only, $dry_run );
			}
			
			bulkusereditor_log ( "Change done : " . $changeResults ['added'] . ' / ' . $changeResults ['updated'] );
			// if ($changeResults['added'] > 0 || $changeResults['updated'] > 0) {
			$result .= $changeResults ['type'];
			$result .= '<br/>';
			$result .= 'Added: ' . $changeResults ['added'] . ' / Updated: ' . $changeResults ['updated'];
			$result .= '<br/>';
			$result .= $changeResults ['message'];
			bulkusereditor_log ( $result );
			// }
			echo $result;
			wp_die ();
		}
		function bulkusereditor_load_scripts($force = false) {
			$useSwal2 = false;
			if ($useSwal2) {
				$swalVersion = "6.9.1";
				$jsEnd = '2.min.js';
				$cssEnd = '2.min.css';
			} else {
				// <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
				/*$swalPath = 'https://unpkg.com/sweetalert/dist/sweetalert.min.js';
				bulkusereditor_log ( $swalPath);
				wp_register_script ( 'swal', $swalPath, null, null );
				wp_enqueue_script ( 'swal' );*/
				
				$swalVersion = "2.0.3";
				$jsEnd = '.min.js';
				//$cssEnd = '.css';
			
			}
			
			
			$swalPath = plugins_url ( "libs/swal/" . $swalVersion . "/sweetalert".$jsEnd, __FILE__ );
			bulkusereditor_log ( $swalPath);
			wp_register_script ( 'swal', $swalPath, null, null );
			wp_enqueue_script ( 'swal' );
			if ($useSwal2) {
			$swalCssPath = plugins_url ( "libs/swal/" . $swalVersion . "/sweetalert".$cssEnd, __FILE__ );
			bulkusereditor_log ( $swalCssPath);
			wp_register_style ( 'swal-css', $swalCssPath );
			wp_enqueue_style ( 'swal-css' );
			}
			$buePath = plugins_url ( "js/bulkusereditor.js", __FILE__ );
			wp_register_script ( 'bulk-user-js', $buePath, null, null );
			wp_enqueue_script ( 'bulk-user-js' );
			
			$jqueryuiVersion = "1.12.1";
			$jqueryuiPath = plugins_url ( "libs/jqueryui/" . $jqueryuiVersion . "/jquery-ui.min.js", __FILE__ );
			wp_register_script ( 'jqueryui', $jqueryuiPath, null, null );
			wp_enqueue_script ( 'jqueryui' );
			
			$jqueryuiCssPath = plugins_url ( "libs/jqueryui/" . $jqueryuiVersion . "/jquery-ui.min.css", __FILE__ );
			wp_register_style ( 'jquery-ui-css', $jqueryuiCssPath );
			wp_enqueue_style ( 'jquery-ui-css' );
			
			$bueCssPath = plugins_url ( "css/bue.css", __FILE__ );
			wp_register_style ( 'bue-css', $bueCssPath );
			wp_enqueue_style ( 'bue-css' );
		}
		function add_user_admin_menu() {
			add_submenu_page ( 'users.php', 'Bulk Edit Users', 'Bulk Edit Users', 'manage_options', 'bulk-edit-users', array (
					$this,
					'bulkusereditor_page'
			) );
		}
		function add_post_admin_menu() {
			add_submenu_page ( 'edit.php', 'Bulk Edit Posts', 'Bulk Edit Posts', 'manage_options', 'bulk-edit-posts', array (
					$this,
					'bulkpostseditor_page'
			) );
		}
		function getRolesSelectFormField($name) {
			$content = '<select name="' . $name . '" id="' . $name . '">';
			$content .= '<option value="" selected="selected">Any role</option>';
			
			foreach ( get_editable_roles () as $role_name => $role_info ) {
				
				$content .= '<option value="' . $role_name . '">' . $role_name . '</option>';
			}
			
			$content .= '</select>';
			
			return $content;
		}
		function getUsersSelectFormField($role, $name, $selected = '', $extra = '') {
			global $wpdb;
			
			$args = array (
					'blog_id' => $GLOBALS ['blog_id'],
					'role' => '',
					'role__in' => array (),
					'role__not_in' => array (),
					'meta_key' => '',
					'meta_value' => '',
					'meta_compare' => '',
					'meta_query' => array (),
					'date_query' => array (),
					'include' => array (),
					'exclude' => array (),
					'orderby' => 'ID',
					'order' => 'ASC',
					'offset' => '',
					'search' => '',
					'number' => '',
					'count_total' => false,
					'fields' => 'all',
					'who' => ''
			);
			
			$allUsers = get_users ( $args );
			
			$content = '<select name="' . $name . '" id="' . $name . '" ' . $extra . '>';
			
			if ($selected == '') {
				$content .= '<option value="" selected="selected">Any user</option>';
			} else {
				$r_user = $wpdb->get_results ( "SELECT *   from " . $wpdb->prefix . "users where ID = " . $selected . "", ARRAY_A );
				$content .= '<option value="' . $selected . '" selected="selected">' . stripslashes ( $r_user [0] ['display_name'] ) . '</option>';
			}
			
			foreach ( $allUsers as $user ) {
				$userID = $user->ID;
				$firstname = $user->first_name;
				$lastname = $user->last_name;
				$displayname = $user->display_name;
				$email = $user->user_email;
				
				$displayedPart = 'ID' . $userID . ') ' . $firstname . ' ' . $lastname . ' (' . stripslashes ( $displayname ) . ') : ' . $email;
				$content .= '<option value="' . $userID . '">' . $displayedPart . '</option>';
			}
			$content .= '</select>';
			
			return $content;
		}
		
		/**
		 * Returns all unique meta key from user meta database
		 *
		 * @param
		 *        	no parameter right now
		 *        	@retun std Class
		 * @todo do what you do for each meta key.
		 */
		function get_user_meta_key() {

			global $wpdb;
			
			$select = "SELECT distinct $wpdb->usermeta.meta_key FROM $wpdb->usermeta";
			
			$usermeta = $wpdb->get_results ( $select );
			
			/*$genericObject = new stdClass();
			$genericObject['meta_key'] = 'user_url';*/
			foreach ($this->extraUserFields as $meta_key){
			    
			   
			    $obj = (object) array(
			        'meta_key'=> $meta_key
			    );
			    if (!in_array($obj,$usermeta)){
			     $usermeta[] = $obj;
			    }
			}
			
			//bulkusereditor_log ($usermeta);
			return $usermeta;

		}
		function bulkpostseditor_page() {
			echo '<h1 class="bue_title">Bulk edit posts</h1>';
			echo '<form method="post">';
			
			bulkusereditor_log ( "Meta keys = " . count ( $allMetaKeys ) );
			// $allUsers = get_users();
			bulkusereditor_log ( "All users = " . count ( $allUsers ) );
			
			$argsDest = array (
					'show_option_all' => 'All',
					'show_option_none' => '',
					'option_none_value' => '-1',
					'orderby' => 'ID',
					'order' => 'ASC',
					'show_count' => 1,
					'hide_empty' => 0,
					'child_of' => 0,
					'exclude' => '',
					'include' => '',
					'echo' => 1,
					'selected' => 0,
					'hierarchical' => 0,
					'name' => 'catDest',
					'id' => '',
					'class' => 'postform',
					'depth' => 0,
					'tab_index' => 0,
					'taxonomy' => 'category',
					'hide_if_empty' => false,
					'value_field' => 'term_id'
			);
			
			$argsSrc = array (
					'show_option_all' => 'All',
					'show_option_none' => '',
					'option_none_value' => '-1',
					'orderby' => 'ID',
					'order' => 'ASC',
					'show_count' => 1,
					'hide_empty' => 0,
					'child_of' => 0,
					'exclude' => '',
					'include' => '',
					'echo' => 1,
					'selected' => 0,
					'hierarchical' => 0,
					'name' => 'catSrc',
					'id' => '',
					'class' => 'postform',
					'depth' => 0,
					'tab_index' => 0,
					'taxonomy' => 'category',
					'hide_if_empty' => false,
					'value_field' => 'term_id'
			);
			
			$fieldSetStyle = 'border:solid #C4C4C4 1px;padding:1em;margin:5px 0;';
			
			echo '<fieldset style="' . $fieldSetStyle . '"><legend class="bue_legend">Do:</legend>';
			
			echo '<p><select name="meta_key" id="meta_key_id" >';
			printf ( '<option value="%s" style="margin-bottom:3px;">%s</option>', 'add', 'Add' );
			printf ( '<option value="%s" style="margin-bottom:3px;">%s</option>', 'replace', 'Replace with' );
			printf ( '<option value="%s" style="margin-bottom:3px;">%s</option>', 'remove', 'Remove' );
			echo '</select></p>';
			echo '<p>category ';
			wp_dropdown_categories ( $argsDest );
			echo '</p>';
			echo '</fieldset>';
			// echo '<h3>to meta key (' . count($allMetaKeys) . ' available):</h3>';
			echo '<fieldset style="' . $fieldSetStyle . '"><legend class="bue_legend">Target</legend>';
			echo '<p>to all</p>';
			
			$post_types = get_post_types();
			?>
<select id="bme_post_type" name="">

    <?php foreach ($post_types as $post_type ) {      
        $label_obj = get_post_type_object($post_type); 
        $labels = $label_obj->labels->name;
    ?>

        <option <?php selected( $instance['posttype'], $post_type ); ?> value="<?php echo $post_type; ?>"><?php echo $labels; ?></option>

    <?php } ?>

</select>
			<?php 
			echo '<p>in category :';
			wp_dropdown_categories ( $argsSrc );
			echo '</p></fieldset>';
			echo '<fieldset style="<?php echo $fieldSetStyle; ?>">';
			echo '<legend class="bue_legend">with filter:</legend>';
			echo '<p>';
			echo 'Created between: Start Date: <input type="text" class="datepicker" id="start_date" name="start_date"> End Date: <input type="text" class="datepicker" id="end_date" name="end_date">';
			echo '</p></fieldset>';
			echo submit_button ( 'Add/Update', 'primary', 'bue-submit-post' );
			echo '</form>';
		}
		function bue_get_gf_form_fields() {
			bulkusereditor_log ( $_POST );
			$form_id = sanitize_text_field ( $_POST ['form_id'] );
			
			$result = $this->getFormFields ( $form_id );
			
			echo $result;
			wp_die ();
		}
		function getGFForms() {
			$gf_list = array (
					" " => " " 
			);
			if (is_plugin_active ( 'gravityforms/gravityforms.php' ) || class_exists ( 'GFCommon' )) {
				$forms = GFAPI::get_forms ();
				foreach ( $forms as $form ) {
					$selected = '';
					$post_id = $form ['id'];
					$post_name = $form ['title'];
					$gf_list [strval ( $post_id )] = $post_name;
				}
			}
			
			return $gf_list;
		}
		public function getFormFields($form_id) {
			$list = array ();
			$form = GFAPI::get_form ( $form_id );
			$form_fields = $form ['fields'];
			
			foreach ( $form_fields as $field ) {
				
				if ($field ['type'] == 'page'){
				continue;	
				}
				
				$selected = '';
				$field_id = $field ['id'];
				$field_label = !empty($field ['label']) ? $field ['label'] : 'no label';
				$list [$field_id] = $field_label;
				if (empty($field ['label'])){
					bulkusereditor_log ($field);
				}
			}
			
			wp_send_json ( $list );
			// echo $list;
			die ();
		}
		
		function bulk_edit_fields_submenu_handler() {
			echo '<h1 class="bue_title">Bulk Edit Gravity Forms Fields</h1>';			
			echo '<form method="post">';
						
			$fieldSetStyle = 'border:solid #C4C4C4 1px;padding:1em;margin:5px 0;';
			
			echo '<fieldset style="' . $fieldSetStyle . '"><legend class="bue_legend">choose data source:</legend>';
			echo '<p>Select data source used to update meta fields : <select name="src_key" id="bue_src_key_id" >';
			$sources = array (
					' ' => ' ',
					'text' => "Plain text",
					/*'gf' => 'Gravity Forms'*/
			);
			foreach ( $sources as $key => $val ) {				
				printf ( '<option value="%s" style="margin-bottom:3px;">%s</option>', $key, $val );
			}
			echo '</select></p></fieldset>';
			
			$fieldSetStyle = 'border:solid #C4C4C4 1px;padding:1em;margin:5px 0;';
			echo '<fieldset id="bue_text_data_source" class="bue_data_source" style="' . $fieldSetStyle . '"><legend class="bue_legend">set plain text value:</legend>';
			echo '<p>The custom value that will be set to all fields : <input type="text" name="new_value"></p>';
			echo '</fieldset>';
			
			$fieldSetStyle = 'border:solid #C4C4C4 1px;padding:1em;margin:5px 0;';
			$allGFForms = $this->getGFForms ();
			$formsCount = count ( $allGFForms );
		
			echo '<fieldset style="' . $fieldSetStyle . '"><legend class="bue_legend">to form (' . count ( $allGFForms) . ' available):</legend>';
			echo '<p> Select form (among ' . $formsCount . ') : <select name="gf_form_key" id="gf_form_key_id" >';
			
			foreach ( $allGFForms as $key => $val ) {
				printf ( '<option value="%s" style="margin-bottom:3px;">%s</option>', $key, $val );
			}
			echo '</select></p>';
			echo '<p> Select field  : <select name="gf_form_field_key_target" id="gf_form_field_id_target" class="gf_form_field_key_id" >';
			echo '</select></p>';
			
			echo '</fieldset>';
			echo '<fieldset style="' . $fieldSetStyle . '">';
			echo '<legend class="bue_legend">for following filtered users:</legend>';
			echo '<p>where field:<select name="gf_form_field_key_cond" id="gf_form_field_id_cond" class="gf_form_field_key_id" >';
			echo '</select></p>';
			echo '<p>is:';
			echo '<input type="text" name="condition_value"></p>';
			echo '</p>';
			echo '<p>Registered between (<b>english only dates here</b>): Start Date: <input type="text" class="datepicker"	id="start_date" name="start_date"> End Date: <input type="text"	class="datepicker" id="end_date" name="end_date"></p>';
			echo '</fieldset>';
			echo '<label> <input type="checkbox" name="bue_check_updateifblank"	value="blank_only">Update only blank values<br> <input type="checkbox" name="bue_dry_run" checked="true" value="1">Dry run (won\'t change anything), leave checked at first run (recommended).<br>';
			echo submit_button ( 'Add/Update GF Fields', 'primary', 'bue-submit-gf' );
			echo '</form>';
		}
		
		function bulkusereditor_page() {
			echo '<h1 class="bue_title">Bulk edit users</h1>';
			echo '<form method="post">';
			$allMetaKeys = $this->get_user_meta_key ();
			bulkusereditor_log ( "Meta keys = " . count ( $allMetaKeys ) );
			$allUsers = get_users ();
			bulkusereditor_log ( "All users = " . count ( $allUsers ) );
			
			$argsUsersList = array (
					'show_option_all' => null, // string
					'show_option_none' => ' ', // string
					'hide_if_only_one_author' => null, // string
					'orderby' => 'display_name',
					'order' => 'ASC',
					'include' => null, // string
					'exclude' => null, // string
					'multi' => false,
					'show' => 'display_name',
					'echo' => true,
					'selected' => false,
					'include_selected' => false,
					'name' => 'user', // string
					'id' => null, // integer
					'class' => null, // string
					'blog_id' => $GLOBALS ['blog_id'],
					'who' => null 
			); // string
			
			$fieldSetStyle = 'border:solid #C4C4C4 1px;padding:1em;margin:5px 0;';
			
			echo '<fieldset style="' . $fieldSetStyle . '"><legend class="bue_legend">choose source:</legend>';
			echo '<p>Select data source used to update meta fields : <select name="src_key" id="bue_src_key_id" >';
			$sources = array (
					' ' => ' ',
					'text' => "Plain text",
					'gf' => 'Gravity Forms' 
			);
			foreach ( $sources as $key => $val ) {
				
				printf ( '<option value="%s" style="margin-bottom:3px;">%s</option>', $key, $val );
			}
			echo '</select></p></fieldset>';
			
			$fieldSetStyle = 'border:solid #C4C4C4 1px;padding:1em;margin:5px 0;';
			echo '<fieldset id="bue_text_data_source" class="bue_data_source" style="' . $fieldSetStyle . '"><legend class="bue_legend">set plain text value:</legend>';
			echo '<p>The custom value that will be set to all meta fields : <input type="text" name="new_value"></p>';
			echo '</fieldset>';
			
			$fieldSetStyle = 'border:solid #C4C4C4 1px;padding:1em;margin:5px 0;';
			$allGFForms = $this->getGFForms ();
			$formsCount = count ( $allGFForms );
			// $allGFForms[' '] = ' ';
			echo '<fieldset id="bue_gf_data_source" class="bue_data_source" style="' . $fieldSetStyle . '"><legend class="bue_legend">set value from Gravity Forms:</legend>';
			echo '<p>The custom value from GF that will be set to all meta fields (if user IDs match)</p>';
			echo '<p> Select form (among ' . $formsCount . ') : <select name="gf_form_key" id="gf_form_key_id" >';
			
			foreach ( $allGFForms as $key => $val ) {
				printf ( '<option value="%s" style="margin-bottom:3px;">%s</option>', $key, $val );
			}
			echo '</select></p>';
			
			echo '<p> Select field  : <select name="gf_form_field_key" id="gf_form_field_2" class="gf_form_field_key_id" >';
			echo '</select></p>';
			echo '<p>The max entries to retrieve from GF API query: <input type="text" name="gf_form_max_entries">';
			echo '</p></fieldset>';
			
			echo '<fieldset style="' . $fieldSetStyle . '"><legend class="bue_legend">to meta key (' . count ( $allMetaKeys ) . ' available):</legend>';
			echo '<p>Select the meta field to update : <select name="meta_key" id="meta_key_id" >';
			
			foreach ( $allMetaKeys as $key => $metaObj ) {
				$meta_key_str = $metaObj->meta_key;
				printf ( '<option value="%s" style="margin-bottom:3px;">%s</option>', $meta_key_str, $meta_key_str );
			}
			echo '</select></p></fieldset>';
			echo '<fieldset style="' . $fieldSetStyle . '">';
			echo '<legend class="bue_legend">for following filtered users:</legend>';
			echo '<p>with role:' . $this->getRolesSelectFormField ( 'user_role' ) . '</p>';
			echo '<p>or only for specific user (' . count ( $allUsers ) . ' available) with user name:';
			echo $this->getUsersSelectFormField ( '', 'user_id' );
			echo '</p>';
			echo '<p>Registered between (<b>english only dates here</b>): Start Date: <input type="text" class="datepicker"	id="start_date" name="start_date"> End Date: <input type="text"	class="datepicker" id="end_date" name="end_date"></p>';
			echo '</fieldset>';
			echo '<label> <input type="checkbox" name="bue_check_updateifblank"	value="blank_only">Update only blank values<br> <input type="checkbox" name="bue_dry_run" checked="true" value="1">Dry run (won\'t change anything), leave checked at first run (recommended).<br>';
			echo submit_button ( 'Add/Update', 'primary', 'bue-submit' );
			echo '</form>';
		}
		function handle_bulk_category_change($action, $postType, $dest, $src, $dates) {
			bulkusereditor_log ( $_POST );
			$added = 0;
			$updated = 0;
			$removed = 0;
			if (isset ( $action ) && isset ( $dest ) && isset ( $src )) {
				// go change categories
				bulkusereditor_log ( "Get all posts in category " . $src );
				$destPostsArg = array (
						'numberposts' => - 1,
						'category' => $src,
						'orderby' => 'date',
						'order' => 'DESC',
						'include' => array (),
						'exclude' => array (),
						'meta_key' => '',
						'meta_value' => '',
						'post_type' => empty($postType) ? 'post' : $postType,
						'post_status' => array (
								'publish',
								'pending',
								'draft',
								'auto-draft',
								'future',
								'private',
								'inherit',
								'trash' 
						),
						'suppress_filters' => true 
				);
				bulkusereditor_log ( $destPostsArg );
				/*$args = array(
						'numberposts' => -1,
						'post_type'   => $postType
				);*/
				$postsToUpdate = get_posts ( $destPostsArg ); // $postType
				bulkusereditor_log ( count ( $postsToUpdate ) . ' posts to update' );
				foreach ( $postsToUpdate as $post ) {
					
					$post_ID = $post->ID;
					bulkusereditor_log ( $action . " post " . $post_ID . " categories" );
				
					$created = $post->post_date;
					$dteRegistered = new DateTime ( $created );
					$dteStart = new DateTime ( isset ( $dates ['START'] ) ? $dates ['START'] : '' );
					$dteEnd = new DateTime ( isset ( $dates ['END'] ) ? $dates ['END'] : '' );
					
					bulkusereditor_log ( $dteStart );
					bulkusereditor_log ( $dteRegistered );
					bulkusereditor_log ( $dteEnd );
					
					// bulkusereditor_log(strval($dteStart).' < '.strval($dteRegistered) . ' < '.strval($dteEnd));
					if ($dteStart > $dteRegistered || $dteEnd < $dteRegistered) {
						bulkusereditor_log ( "Post " . $post_ID . ' creation : outside dates' );
						continue;
					}
					
					$currentCats = wp_get_post_categories ( $post_ID );
					
					// bulkusereditor_log($post_ID);
					bulkusereditor_log ( $currentCats );
					if ($action != 'remove') {
						if ($action == 'add') {
							// $returnVal = array_push($currentCats, $dest);
							$ret = array (
									$dest 
							);
							$appendCat = true;
						} else {
							$ret = array (
									$dest 
							);
							$appendCat = false;
						}
					
					} else {
						$ret = array_diff ( $currentCats, array (
								$dest 
						) );
						$appendCat = false;
					}
					
					bulkusereditor_log ( "AFTER " . $action );
					bulkusereditor_log ( "appendCat " . $appendCat );
					
					bulkusereditor_log ( $ret );
					
					wp_set_post_categories ( $post_ID, $ret, $appendCat );
					
					switch ($action) {
						case 'add' :
							$added ++;
							break;
						case 'update' :
							$updated ++;
							break;
						case 'remove' :
							$removed ++;
							break;
					}
				}
			}
			
			return array (
					"added" => $added,
					'updated' => $updated,
					'removed' => $removed 
			);
		}
		function getNewValue($user_id, $val) {
			$newValue = null;
			// $user_email = get_user_meta($user_id, 'user_email', true);
			$user = get_user_by ( 'ID', $user_id );
			$user_email = $user->user_email;
			// bulkusereditor_log($val);
			bulkusereditor_log ( "---> Looking for one of " . $user_id . " / " . $user_email );
			if (isset ( $val ['gf_form_id'] ) && isset ( $val ['gf_field_id'] )) {
				$form_id = $val ['gf_form_id'];
				$field_id = $val ['gf_field_id'];
				bulkusereditor_log ( "---> Lookup on form " . $form_id . " for field " . $field_id );
				// check if creator
				$form = GFAPI::get_form ( $form_id );
				$fields = $form ['fields'];
				bulkusereditor_log ( "---> Form fields " . count ( $fields ) );
				$email_fields = array ();
				foreach ( $fields as $field ) {
					if ($field ['type'] == 'email') {
						$email_fields [] = $field ['id'];
					}
				}
				bulkusereditor_log ( "---> Email fields " . implode ( "/", $email_fields ) );
				$maxentries = (isset ( $val ['gf_max_entries'] ) && $val ['gf_max_entries'] > 0) ? $val ['gf_max_entries'] : 1500;
				$paging = array (
						'offset' => 0,
						'page_size' => $maxentries 
				);
				

				$search_criteria = array ();
				$sorting = array ();
				
				$entries = GFAPI::get_entries ( $form_id, $search_criteria, $sorting, $paging );
				
				bulkusereditor_log ( "---> Entries " . count ( $entries ) );
				foreach ( $entries as $entry ) {
					
					$creator = rgar ( $entry, 'created_by' );
					// bulkusereditor_log("---> Creator : ".$creator);
					$emails_list = array ();
					foreach ( $email_fields as $email_field_id ) {
						// bulkusereditor_log("email field id : ".$email_field_id);
						$new_email = rgar ( $entry, $email_field_id );
						$emails_list [] = $new_email;
					}
					// bulkusereditor_log("Email list ".implode("/",$emails_list));
					$matches = ($creator == $user_id) || (in_array ( $user_email, $emails_list ));
					if ($matches) {
						$newValue = rgar ( $entry, strval ( $field_id ) );
						// bulkusereditor_log ($entry);
						bulkusereditor_log ( "++++ match : $creator == $user_id || $user_email inside " . implode ( ',', $emails_list ) );
						bulkusereditor_log ( "Will set new value " . $newValue . ' (field ' . $field_id . ') to user id ' . $user_id );
						break;
					}
				}
				
			}
			
			return $newValue;
		}
		function handle_bulk_change($val, $meta, $role, $userID, $dates, $blank_only = false, $dry_run = true) {
	
			bulkusereditor_log ( $val . ' / ' . $meta . ' / ' . $role . ' / ' . $userID . ' / ' . strval($dates) . ' / ' . $blank_only . ' / ' . $dry_run );
			$added = 0;
			$updated = 0;
			$returnType = 'ok';
			if (isset ( $val ) && isset ( $meta ) && (isset ( $role ) || isset ( $userID ))) {
				$userWP = get_user_by ( 'ID', $userID );
				
				if (isset ( $userID ) && $userWP !== false) {
					bulkusereditor_log ( "--> Add/Update " . $meta . " with " . $val . " for " . $userID );
					
					$blogusers = array (
							$userWP 
					);
				} else {
					bulkusereditor_log ( "--> Add/Update " . $meta . " with " . $val . " for " . $role . " users" );
					
					$blogusers = get_users ( array (
							'role' => $role 
					) );
				}
				// Array of WP_User objects.
				$logText = '';
				foreach ( $blogusers as $user ) {
					$user_id = $user->ID;
					$msg = $user_id . ' -> ' . $user->user_email;
				
					bulkusereditor_log ( $msg );
					
					$udata = get_userdata ( $user_id );
					
					$registered = $udata->user_registered;
					try {
						$dteRegistered = new DateTime ( $registered );
						$dteStart = new DateTime ( $dates ['START'] );
						$dteEnd = new DateTime ( $dates ['END'] );
						$now = $dteStart->format ( 'Ymd' );
						$next = $dteEnd->format ( 'Ymd' );
						
						if ($dteStart > $dteRegistered || $dteEnd < $dteRegistered) {
							$msg = "User " . $user_id . ' registration ' . $registered . ' : outside dates';
						
							bulkusereditor_log ( $msg );
							continue;
						}
					} catch ( Exception $e ) {
						$returnType = 'error';
						$logText .= "Error converting dates " . $registered . ' ' . $e->getMessage ();
						$logText .= $udata;
						bulkusereditor_log ( $msg );
						
						break;
					}
					
					$currentUserMeta = get_user_meta ( $user_id, $meta, true );
					
					$newValue = is_array ( $val ) ? $this->getNewValue ( $user_id, $val ) : $val;
					if ($newValue == null) {
						$msg = 'cannot find new value for user ' . $user_id;
						bulkusereditor_log ( $msg );
						continue;
					}
					if ($currentUserMeta != null && ! empty ( $currentUserMeta ) && $blank_only) {
						$msg = "BLANKONLY:: " . $meta . " already set to " . $currentUserMeta . " for user " . $user_id;
						bulkusereditor_log ( $msg );
						continue;
					}
					
					$updateMsg = $user_id . ': "' . $meta . '" updated from "'.$currentUserMeta.'" to "' . $newValue.'"';
					if (! $dry_run) {
					    // (int|bool) Meta ID if the key didn't exist, true on successful update, false on failure.
					    // strange bug : http://lists.automattic.com/pipermail/wp-hackers/2012-August/044226.html
					    if (in_array($meta, $this->extraUserFields)){
					       // $user_id = 6;
					       // $website = 'http://example.com';
					        $toLog = "wp_update_user( array( 'ID' => $user_id, $meta => $newValue ) )";
					        bulkusereditor_log ( $toLog );
					        
					        $user_data = wp_update_user( array( 'ID' => $user_id, $meta => $newValue ) );
					        
					        if ( is_wp_error( $user_data ) ) {
					            // There was an error; possibly this user doesn't exist.
					            $msg = 'Error.';
					        } else {
					            // Success!
					            $msg = 'User profile updated.';
					        }
					    } else {
					    bulkusereditor_log ( "update_user_meta ( $user_id, $meta, $newValue );" );
					    
						$updateResult = update_user_meta ( intval($user_id), strval($meta), $newValue );
						if (true === $updateResult){
						  					
						  $msg = $updateMsg . __('successful update');
						} elseif (is_numeric($updateResult)) {
						    $msg = $updateMsg . __('key didn\'t exist');
						} elseif (false === $updateResult) {
						 $msg = __('failure to update');
						}
					    }
					} else {
						$msg = 'DRY::' . $updateMsg;
					}
					$updated ++;
					
					$logText .= $msg;
					bulkusereditor_log ( $msg );
					
					$logText .= ' | ';
				}
			}
			return array (
					'type' => $returnType,
					"added" => $added,
					'updated' => $updated,
					"message" => $logText 
			);
		}
	}
}
$obj = new BulkUserEditor ();