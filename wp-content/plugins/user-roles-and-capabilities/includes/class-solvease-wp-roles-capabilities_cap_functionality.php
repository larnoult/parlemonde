<?php

class Solvease_Roles_Capabilities_Functionality
{

    private $translation_domain;

    private $built_in_capabilities = array(
        'plugins' => array(
            'name' => 'Plugins Related Capabilities',
            'cap' => array(
                'activate_plugins' => array(
                    'name' => 'activate_plugins',
                    'desc' => 'Activate Plugins'
                ),
                'delete_plugins' => array(
                    'name' => 'delete_plugins',
                    'desc' => 'Delete Plugins'
                ),
                'edit_plugins' => array(
                    'name' => 'edit_plugins',
                    'desc' => 'Edit Plugins'
                ),
                'install_plugins' => array(
                    'name' => 'install_plugins',
                    'desc' => 'Install Plugins'
                ),
                'update_plugins' => array(
                    'name' => 'update_plugins',
                    'desc' => 'Update Plugins'
                ),
            )
        ),
        'users' => array(
            'name' => 'Users Related Capabilities',
            'cap' => array(
                'add_users' => array(
                    'name' => 'add_users',
                    'desc' => 'Add Users'
                ),
                'create_users' => array(
                    'name' => 'create_users',
                    'desc' => 'Create Users'
                ),
                'delete_users' => array(
                    'name' => 'delete_users',
                    'desc' => 'Delete Users'
                ),
                'edit_users' => array(
                    'name' => 'edit_users',
                    'desc' => 'Edit Users'
                ),
                'list_users' => array(
                    'name' => 'list_users',
                    'desc' => 'List Users'
                ),
                'promote_users' => array(
                    'name' => 'promote_users',
                    'desc' => 'Promote Users'
                ),
                'remove_users' => array(
                    'name' => 'remove_users',
                    'desc' => 'Remove Users'
                ),
            )
        ),
        'themes' => array(
            'name' => 'Themes Related Capabilities',
            'cap' => array(
                'delete_themes' => array(
                    'name' => 'delete_themes',
                    'desc' => 'Delete Themes'
                ),
                'edit_themes' => array(
                    'name' => 'edit_themes',
                    'desc' => 'Edit Themes'
                ),
                'install_themes' => array(
                    'name' => 'install_themes',
                    'desc' => 'Install Themes'
                ),
                'switch_themes' => array(
                    'name' => 'switch_themes',
                    'desc' => 'Switch Themes'
                ),
                'update_themes' => array(
                    'name' => 'update_themes',
                    'desc' => 'Update Themes'
                ),
                'edit_theme_options' => array(
                    'name' => 'edit_theme_options',
                    'desc' => 'Edit Themes Options'
                ),
            )
        ),
        'posts' => array(
            'name' => 'Post Related Capabilities',
            'cap' => array(
                'edit_others_posts' => array(
                    'name' => 'edit_others_posts',
                    'desc' => 'Edit Others Posts'
                ),
                'delete_others_posts' => array(
                    'name' => 'delete_others_posts',
                    'desc' => 'Delete Others Posts'
                ),
                'delete_private_posts' => array(
                    'name' => 'delete_private_posts',
                    'desc' => 'Delete Private Posts'
                ),
                'edit_private_posts' => array(
                    'name' => 'edit_private_posts',
                    'desc' => 'Edit Private Posts'
                ),
                'read_private_posts' => array(
                    'name' => 'read_private_posts',
                    'desc' => 'Read Private Posts'
                ),
                'edit_published_posts' => array(
                    'name' => 'edit_published_posts',
                    'desc' => 'Edit Published Posts'
                ),
                'publish_posts' => array(
                    'name' => 'publish_posts',
                    'desc' => 'Publish Posts'
                ),
                'delete_published_posts' => array(
                    'name' => 'delete_published_posts',
                    'desc' => 'Delete Published Posts'
                ),
                'edit_posts' => array(
                    'name' => 'edit_posts',
                    'desc' => 'Edit Posts'
                ),
                'delete_posts' => array(
                    'name' => 'delete_posts',
                    'desc' => 'Delete Posts'
                ),
                'manage_categories' => array(
                    'name' => 'manage_categories',
                    'desc' => 'Manage Categories'
                ),
            )
        ),
        'pages' => array(
            'name' => 'Pages Related Capabilities',
            'cap' => array(
                'edit_pages' => array(
                    'name' => 'edit_pages',
                    'desc' => 'Edit Pages'
                ),
                'edit_others_pages' => array(
                    'name' => 'edit_others_pages',
                    'desc' => 'Edit Others Pages'
                ),
                'edit_published_pages' => array(
                    'name' => 'edit_published_pages',
                    'desc' => 'Edit Published Pages'
                ),
                'publish_pages' => array(
                    'name' => 'publish_pages',
                    'desc' => 'Publish Pages'
                ),
                'delete_pages' => array(
                    'name' => 'delete_pages',
                    'desc' => 'Delete Pages'
                ),
                'delete_others_pages' => array(
                    'name' => 'delete_others_pages',
                    'desc' => 'Delete others pages'
                ),
                'delete_published_pages' => array(
                    'name' => 'delete_published_pages',
                    'desc' => 'Delete Published Pages'
                ),
                'delete_private_pages' => array(
                    'name' => 'delete_private_pages',
                    'desc' => 'Delete Private Pages'
                ),
                'edit_private_pages' => array(
                    'name' => 'edit_private_pages',
                    'desc' => 'Edit Private Pages'
                ),
                'read_private_pages' => array(
                    'name' => 'read_private_pages',
                    'desc' => 'Read Private Pages'
                )
            )
        ),
        'media' => array(
            'name' => 'Media Related Capabilities',
            'cap' => array(
                'edit_files' => array(
                    'name' => 'edit_files',
                    'desc' => 'Edit Files'
                ),
                'upload_files' => array(
                    'name' => 'upload_files',
                    'desc' => 'Upload Files'
                ),
                'unfiltered_upload' => array(
                    'name' => 'unfiltered_upload',
                    'desc' => 'Unfiltered Upload'
                ),
            )
        ),
        'links' => array(
            'name' => 'Links Related Capabilities',
            'cap' => array(
                'manage_links' => array(
                    'name' => 'manage_links',
                    'desc' => 'Manage Links'
                ),
            )
        ),
        'import_export' => array(
            'name' => 'Import / Export Related Capabilities',
            'cap' => array(
                'export' => array(
                    'name' => 'export',
                    'desc' => 'Export'
                ),
                'import' => array(
                    'name' => 'import',
                    'desc' => 'Import'
                ),
            )
        ),
        'comments' => array(
            'name' => 'Comments Related Capabilities',
            'cap' => array(
                'moderate_comments' => array(
                    'name' => 'moderate_comments',
                    'desc' => 'Moderate Comments'
                ),
                'edit_comment' => array(
                    'name' => 'edit_comment',
                    'desc' => 'Edit Comment'
                ),
            )
        ),
        'dashboard' => array(
            'name' => 'Dashboard Related Capabilities',
            'cap' => array(
                'edit_dashboard' => array(
                    'name' => 'edit_dashboard',
                    'desc' => 'Edit Dashboard'
                ),
                'read' => array(
                    'name' => 'read',
                    'desc' => 'Read'
                ),
            )
        ),
        'admin_options' => array(
            'name' => 'Admin Options Capabilities',
            'cap' => array(
                'manage_options' => array(
                    'name' => 'manage_options',
                    'desc' => 'manage_options'
                ),
                'update_core' => array(
                    'name' => 'update_core',
                    'desc' => 'Update Core'
                ),
                'unfiltered_html' => array(
                    'name' => 'Unfiltered HTML',
                    'desc' => 'Unfiltered HTML'
                ),
            )
        ),
        'deprecated' => array(
            'name' => 'Deprecated Capabilities',
            'cap' => array(
                'level_0' => array(
                    'name' => 'level_0',
                    'desc' => 'level_0'
                ),
                'level_1' => array(
                    'name' => 'level_1',
                    'desc' => 'level_1'
                ),
                'level_2' => array(
                    'name' => 'level_2',
                    'desc' => 'level_2'
                ),
                'level_3' => array(
                    'name' => 'level_3',
                    'desc' => 'level_3'
                ),
                'level_4' => array(
                    'name' => 'level_4',
                    'desc' => 'level_4'
                ),
                'level_5' => array(
                    'name' => 'level_5',
                    'desc' => 'level_5'
                ),
                'level_6' => array(
                    'name' => 'level_6',
                    'desc' => 'level_6'
                ),
                'level_7' => array(
                    'name' => 'level_7',
                    'desc' => 'level_7'
                ),
                'level_8' => array(
                    'name' => 'level_8',
                    'desc' => 'level_8'
                ),
                'level_9' => array(
                    'name' => 'level_9',
                    'desc' => 'level_9'
                ),
                'level_10' => array(
                    'name' => 'level_10',
                    'desc' => 'level_10'
                ),
            )
        ),
    );
    private $built_in_role = array('administrator', 'editor', 'author', 'contributor', 'subscriber');

    function __construct($translation_domain)
    {
        $this->translation_domain = $translation_domain;
    }

    /**
     * get built in capabilities
     * @return array
     */
    public function solvease_roles_capabilities_get_built_in_capabilities()
    {
        return $this->built_in_capabilities;
    }

    /**
     * get built in caps array
     * @return array
     */
    private function solvease_roles_capabilities_built_in_cap_array()
    {
        $bcaps = array();
        foreach ($this->built_in_capabilities as $key => $value) {
            foreach ($value['cap'] as $key => $cdetails) {
                $bcaps[] = $key;
            }
        }
        return $bcaps;
    }

    /**
     * user capability
     * by default come from roles
     */
    public function solvease_roles_capabilities_get_user_caps_come_from_roles($user_roles)
    {
        $all_roles = $this->solvease_roles_capabilities_get_roles();
        $user_caps_from_roles = array();
        if (!empty($user_roles)) {
            foreach ($user_roles as $user_role) {
                if (isset($all_roles[$user_role])) {
                    $user_caps_from_roles = array_merge($user_caps_from_roles, array_keys($all_roles[$user_role]['capabilities']));
                }
            }
        }
        return array_unique($user_caps_from_roles);
    }

    /**
     * custom capabilities
     * @param type $user_data
     * @return array
     */
    public function solvease_roles_capabilities_get_other_custom_caps_for_user($user_data)
    {
        $roles = $this->solvease_roles_capabilities_get_roles();
        $all_cap = $roles['administrator']['capabilities'];
        $role_capabilities = array();
        if (!empty($user_data->allcaps)) {
            foreach (array_keys($user_data->allcaps) as $cap) {
                if (!isset($all_cap[$cap]) && !isset($roles[$cap]) && $cap != 'edit_comment') {
                    $role_capabilities[] = $cap;
                }
            }
        }
        return $role_capabilities;
    }

    public function solvease_roles_capabilities_check_user_cap($user_data, $cap_from_roles, $cap)
    {
        $status = '';

        if (in_array($cap, array_keys($user_data->allcaps))) {
            $status = 'checked';
        }

        if (in_array($cap, $cap_from_roles)) {
            $status .= ' disabled';
        }
        return $status;
    }

    public function solvease_roles_capabilities_get_other_capabilities()
    {
        $editable_roles = $this->solvease_roles_capabilities_get_roles();
        $total_caps = array();
        foreach ($editable_roles as $key => $value) {
            if (!empty($editable_roles[$key]['capabilities'])) {
                foreach ($editable_roles[$key]['capabilities'] as $cap_key => $cap_value) {
                    if (!in_array($cap_key, $total_caps)) {
                        $total_caps[] = $cap_key;
                    }
                }
            }
        }
        $built_in_caps = $this->solvease_roles_capabilities_built_in_cap_array();
        return array_diff($total_caps, $built_in_caps);
    }

    public function solvease_roles_capabilities_get_role_has_capability($allrole, $roleKey, $cap)
    {
        if (isset($allrole[$roleKey]['capabilities'][$cap]) && $allrole[$roleKey]['capabilities'][$cap] == 1) {
            return 'checked';
        }
    }

    /**
     * get all roles
     * @global type $wp_roles
     * @param type $except
     * @return type
     */
    public function solvease_roles_capabilities_get_roles($except = '')
    {
        global $wp_roles;
        $all_roles = $wp_roles->roles;
        //$editable_roles = apply_filters('editable_roles', $all_roles);
        $editable_roles = (array)$all_roles;
        if (isset($except) && $except != '') {
            unset($editable_roles[$except]);
        }
        return $editable_roles;
    }


    public function solvease_roles_capabilities_get_editable_roles($except = '')
    {
        global $wp_roles;
        $all_roles = $wp_roles->roles;
        $editable_roles = apply_filters('editable_roles', $all_roles);
        if (isset($except) && $except != '') {
            unset($editable_roles[$except]);
        }
        return $editable_roles;
    }

    /**
     * change default role
     * @return array
     */
    public function solvease_roles_capabilities_change_default_role()
    {
        if (!isset($_POST['solvease_change_default_role_nonce']) || !wp_verify_nonce($_POST['solvease_change_default_role_nonce'], 'solvease_change_default_role')) {
            return $this->solvease_roles_caps_default_warning();
        }
        $all_roles = $this->solvease_roles_capabilities_get_roles('administrator');
        if (isset($all_roles[$_POST['default_role']])) {
            update_option('default_role', $_POST['default_role']);
            return $this->solvease_roles_caps_default_message(__('Default role changed to ', $this->translation_domain) . $all_roles[$_POST['default_role']]['name'].".", 'success');
        }
        return $this->solvease_roles_caps_default_warning();
    }

    /**
     * save capabilities
     * This function get all the capabilities from POST var
     * @return boolean
     */
    public function solvease_roles_capabilities_save_capabilities()
    {
        if (!isset($_POST['solvease_verify_capability_nonce']) || !wp_verify_nonce($_POST['solvease_verify_capability_nonce'], 'solvease_save_capability')) {
            return $this->solvease_roles_caps_default_warning();
        } else {
            // no values in POST
            if (empty($_POST['capability'])) {
                return $this->solvease_roles_caps_default_warning();
            }

            // get all roles except administrator
            $all_roles = $this->solvease_roles_capabilities_get_roles('administrator');

            // loop through the roles and capabilities
            foreach ($_POST['capability'] as $role => $capability) {
                // roles exist and capabilities not empty
                if (isset($all_roles[$role]) && !empty($capability)) {
                    $this->solvease_roles_capabilities_update_capabilities(get_role($role), $capability);
                }
            }

            // roles for which user choose all unselect
            $all_unselect_roles = array_diff(array_keys($all_roles), array_keys($_POST['capability']));
            if (!empty($all_unselect_roles)) {
                foreach ($all_unselect_roles as $unselect_role) {
                    $this->solvease_roles_capabilities_removes_all_caps_from_role($unselect_role, $all_roles);
                }
            }
        }
        return $this->solvease_roles_caps_default_message(__('changes Saved!', $this->translation_domain), 'success');
    }


    /**
     * change role display name
     * This function will change the role display name
     * @return boolean
     */
    public function solvease_change_role_display_name()
    {
        if (!isset($_POST['change_role_display_name_nonce']) || !wp_verify_nonce($_POST['change_role_display_name_nonce'], 'change_role_display_name')) {
            return $this->solvease_roles_caps_default_warning();
        } else {

            $display_role_id = trim($_POST['display_role_id']);
            $new_role_name = trim($_POST['role_display_name']);

            return $this->change_role_name_in_db($display_role_id, $new_role_name);
        }
    }


    public function change_role_name_in_db($display_role_id, $new_role_name)
    {
        global $wp_roles;
        if (!isset($wp_roles)) {
            $wp_roles = new WP_Roles();
        }

        if ($display_role_id != '' && $new_role_name != '' && array_key_exists($display_role_id, $wp_roles->roles)) {
            // update the role names in database
            $val = get_option('wp_user_roles');
            $val[$display_role_id]['name'] = $new_role_name;
            update_option('wp_user_roles', $val);
            // change in global var to display currently
            $wp_roles->roles[$display_role_id]['name'] = $new_role_name;
            return $this->solvease_roles_caps_default_message(__('Role display name changed successfully!', $this->translation_domain), 'success');
        }

        return $this->solvease_roles_caps_default_warning();
    }

    /**
     * Remove all caps from a role
     * @param type string
     * @param type array
     * @return boolean
     */
    private function solvease_roles_capabilities_removes_all_caps_from_role($role_key, $all_roles)
    {
        // role key is not null
        // all_roles is not empty
        // the role key exists in all roles var
        if ($role_key != '' && !empty($all_roles) && isset($all_roles[$role_key])) {
            $role = get_role($role_key);
            if (!empty($role) && !empty($role->capabilities)) {
                $this->solvease_roles_capabilities_capabilities_remove_cap(array_keys($role->capabilities), $role);
            }
        }

        return true;
    }

    /**
     * Default Warning Message
     * @return array
     */
    private function solvease_roles_caps_default_warning()
    {
        $status = array();
        $status['type'] = 'warning';
        $status['message'] = __('something went Wrong!', $this->translation_domain);
        return $status;
    }


    /**
     * return message
     * @param type $message
     * @param type $type
     * @return array
     */
    private function solvease_roles_caps_default_message($message, $type)
    {
        $status = array();
        $status['type'] = $type;
        $status['message'] = $message;
        return $status;
    }

    /**
     * adding roles
     * @return array
     */
    public function solvease_roles_capabilities_add_role()
    {
        if (!isset($_POST['solvease_add_role_nonce']) || !wp_verify_nonce($_POST['solvease_add_role_nonce'], 'solvease_add_role')) {
            // some thing went wrong with post variable
            return $this->solvease_roles_caps_default_warning();
        }
        $role_id = esc_html(trim($_POST['role-id']));
        $role_name = esc_html(trim($_POST['role-name']));

        // check if its valid role ID
        if (preg_match("/^[a-zA-Z0-9-_]+$/", $role_id) != 1) {
            return $this->solvease_roles_caps_default_message(__('Role ID can contain characters, digits, hyphens or underscore only!', $this->translation_domain), 'danger');
        }

        // get all roles
        $all_roles = $this->solvease_roles_capabilities_get_roles();

        if ($role_id != '' && $role_name != '' && !isset($all_roles[$role_id])) {
            $copy_from = trim($_POST['copy-from']);
            $caps = ($copy_from != '' && isset($all_roles[$copy_from])) ? $all_roles[$copy_from]['capabilities'] : array();
            $roleObj = add_role($role_id, $role_name, $caps);
            if (!$roleObj) {
                return $this->solvease_roles_caps_default_warning();
            }
            return $this->solvease_roles_caps_default_message(__('Role created Successfully!', $this->translation_domain), 'success');
        } else {
            return $this->solvease_roles_caps_default_message(__('Role ID and Name can not be null!', $this->translation_domain), 'danger');
        }
    }

    private function solvease_roles_capabilities_check_if_cap_exists($cap_name)
    {
        $all_roles = $this->solvease_roles_capabilities_get_roles();
        foreach ($all_roles as $role) {
            if (isset($role['capabilities'][$cap_name])) {
                return true;
            }
        }
        return false;
    }

    private function solvease_roles_capabilities_remove_cap_from_all_roles($cap_name)
    {
        global $wp_roles;
        $all_roles = $wp_roles->roles;
        foreach ($all_roles as $key => $value) {
            $role = get_role($key);
            $role->remove_cap($cap_name);
            unset($role);
        }
        return true;
    }

    /**
     * Delete Capability
     * @return array()
     */
    public function solvease_roles_capabilities_delete_cap()
    {
        if (!isset($_POST['solvease_rc_delete_cap_nonce']) || !wp_verify_nonce($_POST['solvease_rc_delete_cap_nonce'], 'solvease_rc_delete_cap')) {
            return $this->solvease_roles_caps_default_warning();
        }
        $options = $this->solvease_roles_capabilities_get_custom_cap();
        $cap_to_delete = $_POST['delete_cap'];
        if (!empty($cap_to_delete)) {
            foreach ($cap_to_delete as $cap) {
                if (in_array($cap, $options)) {
                    $this->solvease_roles_capabilities_remove_cap_from_all_roles($cap);
                    unset($options[array_search($cap, $options)]);
                }
            }
        } else {
            return $this->solvease_roles_caps_default_message(__('Select Capability to Delete!', $this->translation_domain), 'danger');
        }

        $this->solvease_roles_capabilities_update_custom_cap($options);
        return $this->solvease_roles_caps_default_message(__(' Capability  Deleted!', $this->translation_domain), 'success');
    }

    /**
     * custom capabilities
     * @return array
     */
    public function solvease_roles_capabilities_get_custom_cap()
    {
        return unserialize(get_option('solvease_wp_rc_caps'));
    }

    /**
     * update custom cap list
     * @param type $options
     * @return type
     */
    private function solvease_roles_capabilities_update_custom_cap($options)
    {
        update_option('solvease_wp_rc_caps', serialize($options));
        return;
    }

    /**
     * add caps in custom list
     * @param type $cap_name
     * @return boolean
     */
    private function solvease_roles_capabilities_add_custom_cap_to_option($cap_name)
    {
        if ($this->solvease_roles_capabilities_check_if_cap_exists($cap_name) === false) {
            $options = $this->solvease_roles_capabilities_get_custom_cap();
            if (!in_array($cap_name, $options)) {
                $options[] = $cap_name;
            }
            $this->solvease_roles_capabilities_update_custom_cap($options);
        }
        return true;
    }


    /**
     * @return array
     */
    public function solvease_roles_capabilities_import_capability()
    {
        if (!isset($_POST['solvease_import_capability_nonce']) || !wp_verify_nonce($_POST['solvease_import_capability_nonce'], 'solvease_import_capability')) {
            return $this->solvease_roles_caps_default_warning();
        }

        if (!function_exists('wp_handle_upload')) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
        }


        // upload file
        $overrides = array('test_form' => false);
        $file = wp_handle_upload($_FILES['caps_file'], $overrides);
        if (!isset($file['file'])) {
            return $this->solvease_roles_caps_default_message(__('You did not select any file.'), 'warning');
        }

        $json_encode_caps = @file_get_contents($file['file']);
        $json_decode_caps = (array)@json_decode($json_encode_caps);
        if (empty($json_decode_caps)) {
            return $this->solvease_roles_caps_default_message(__('Sorry something went wrong. We found wrong file format'), 'warning');
        }
        $overwrite = (isset($_POST['import_option']) && trim($_POST['import_option']) == 'overwrite') ? true : false;

        foreach ($json_decode_caps as $role_id => $role_caps) {

            if ($role_id == 'administrator') {
                continue;
            }

            if (preg_match("/^[a-zA-Z0-9-_]+$/", $role_id) != 1) {
                //return $this->solvease_roles_caps_default_message(__('Role ID can contain characters, digits, hyphens or underscore only!', $this->translation_domain), 'danger');
                continue;
            }

            $all_roles = $this->solvease_roles_capabilities_get_roles();

            $existing_roles = isset($all_roles[$role_id]) ? true : false;

            $this->import_role_capabilities($role_id, $role_caps, $overwrite, $existing_roles, $all_roles);
        }

        return $this->solvease_roles_caps_default_message(__('Roles and Capabilites imported successfully!'), 'success');
    }


    /**
     * @param $role_id
     * @param $role_caps
     * @param $overwrite
     * @param $existing_roles
     * @param $all_roles
     * @return bool
     */
    public function import_role_capabilities($role_id, $role_caps, $overwrite, $existing_roles, $all_roles)
    {

        if ($existing_roles) {
            $this->change_role_name_in_db($role_id, $role_caps->name);
            // remove all capabilities for overwrite options
            if ($overwrite) {
                $this->solvease_roles_capabilities_removes_all_caps_from_role($role_id, $all_roles);
            }
        } else {
            add_role($role_id, $role_caps->name, array());
        }

        $capabilities = array_keys((array)$role_caps->capabilities);
        if (!empty($capabilities)) {
            $this->solvease_roles_capabilities_capabilities_add_cap($capabilities, get_role($role_id));
        }

        return true;
    }

    /**
     * add capability
     * @return array
     */
    public function solvease_roles_capabilities_change_add_capability()
    {
        if (!isset($_POST['solvease_add_capability_nonce']) || !wp_verify_nonce($_POST['solvease_add_capability_nonce'], 'solvease_add_capability')) {
            return $this->solvease_roles_caps_default_warning();
        }

        $cap_name = esc_html(trim($_POST['cap_name']));
        $cap_roles = $_POST['cap_role'];
        $admin_role = get_role('administrator');

        // cap name already exist
        if (array_key_exists($cap_name, $admin_role->capabilities)) {
            return $this->solvease_roles_caps_default_warning();
        }

        // update custom cap list
        $this->solvease_roles_capabilities_add_custom_cap_to_option($cap_name);
        // add the role admin 
        $admin_role->add_cap($cap_name);
        if (!empty($cap_roles)) {
            foreach ($cap_roles as $role_id) {
                $role = get_role($role_id);
                if ($role) {
                    $role->add_cap($cap_name);
                }
                unset($role);
            }
        }
        return $this->solvease_roles_caps_default_message(__('Capability Added!', $this->translation_domain), 'success');
    }

    private function solvease_roles_capabilities_update_capabilities($role, $capability)
    {
        $new_capabilities = array_keys($capability);
        $existing_capabilities = array_keys($role->capabilities);
        $this->solvease_roles_capabilities_capabilities_remove_cap($this->solvease_roles_capabilities_capabilities_to_remove($new_capabilities, $existing_capabilities), $role);
        $this->solvease_roles_capabilities_capabilities_add_cap($this->solvease_roles_capabilities_capabilities_to_add($new_capabilities, $existing_capabilities), $role);
        return;
    }

    private function solvease_roles_capabilities_capabilities_to_remove($new_capabilities, $existing_capabilities)
    {
        $remove_capabilities = array();
        if (!empty($existing_capabilities)) {
            foreach ($existing_capabilities as $cap) {
                if (!in_array($cap, $new_capabilities)) {
                    $remove_capabilities[] = $cap;
                }
            }
        }
        return $remove_capabilities;
    }

    private function solvease_roles_capabilities_capabilities_remove_cap($caps, $role)
    {
        if (!empty($caps)) {
            foreach ($caps as $cap) {
                $role->remove_cap($cap);
            }
        }
        return true;
    }

    private function solvease_roles_capabilities_capabilities_to_add($new_capabilities, $existing_capabilities)
    {
        $add_capabilities = array();
        if (!empty($new_capabilities)) {
            foreach ($new_capabilities as $cap) {
                if (!in_array($cap, $existing_capabilities)) {
                    $add_capabilities[] = $cap;
                }
            }
        }
        return $add_capabilities;
    }

    private function solvease_roles_capabilities_capabilities_add_cap($caps, $role)
    {
        if (!empty($caps)) {
            foreach ($caps as $cap) {
                $role->add_cap($cap);
            }
        }
    }

    /**
     * check if any use aggigned to this role
     * @param type $role_key
     * @return bolleans
     */
    private function solvease_roles_capabilities_check_if_role_has_user($role_key)
    {
        $user_in_role = get_users(array('role' => $role_key, 'number' => 1));
        return empty($user_in_role) ? false : true;
    }

    /**
     * check if the role is default role of WP
     * @param type $role_key
     * @return type
     */
    private function solvease_roles_capabilities_check_if_default_role($role_key)
    {
        return (get_option('default_role') == $role_key) ? true : false;
    }

    public function solvease_roles_capabilities_get_unused_roles()
    {
        $all_roles = $this->solvease_roles_capabilities_get_roles();
        $unused_roles = array();
        if (!empty($all_roles)) {
            foreach ($all_roles as $key => $value) {
                if (in_array($key, $this->built_in_role) || $this->solvease_roles_capabilities_check_if_role_has_user($key) === true || $this->solvease_roles_capabilities_check_if_default_role($key) === true) {
                    continue;
                }


                $unused_roles[$key] = $value['name'];
            }
        }
        return $unused_roles;
    }

    /**
     * Delete Role
     * @return array
     */
    public function solvease_roles_capabilities_delete_role()
    {
        if (!isset($_POST['solvease_un_used_role_nonce']) || !wp_verify_nonce($_POST['solvease_un_used_role_nonce'], 'solvease_un_used_role')) {
            return $this->solvease_roles_caps_default_warning();
        }
        $roles_to_delete = $_POST['delete_role'];
        if (!empty($roles_to_delete)) {
            foreach ($roles_to_delete as $role_key) {
                if (!in_array($role_key, $this->built_in_role) && $this->solvease_roles_capabilities_check_if_role_has_user($role_key) == false && $this->solvease_roles_capabilities_check_if_default_role($role_key) === false) {
                    remove_role($role_key);
                }
            }
            return $this->solvease_roles_caps_default_message(__('Roles Deleted!', $this->translation_domain), 'success');
        } else {
            return $this->solvease_roles_caps_default_message(__('No roles selected to Delete!', $this->translation_domain), 'danger');
        }
        return $this->solvease_roles_caps_default_warning();
    }

    /**
     * update user roles and capabilities
     * @param type $user
     * @return boolean
     */
    public function solvease_roles_capabilities_update_user_role_cap($user_id)
    {
        // check if its a valid POST
        if (!isset($_POST['solvease_user_role_cap_nonce']) || !wp_verify_nonce($_POST['solvease_user_role_cap_nonce'], 'solvease_user_role_cap') || $_POST['user_id'] != $user_id) {
            return;
        }

        $user = new WP_User($user_id);

        // capabilities

        $capabilities = isset($_POST['cap']) ? array_keys($_POST['cap']) : array();
        // all user roles
        $all_roles = $this->solvease_roles_capabilities_get_roles();
        // primary roles
        $primary_role = $_POST['primary_role'];
        // secondary roles
        $secondary_roles = isset($_POST['secondary_roles']) ? array_keys($_POST['secondary_roles']) : array();

        // blank user roles
        $user->roles = array();
        // remove all user roles
        $user->remove_all_caps();

        // add primary roles
        if ($primary_role != '' && isset($all_roles[$primary_role])) {
            $user->add_role($primary_role);
        } else {
            return FALSE;
        }

        // add secondary roles
        if (!empty($secondary_roles)) {
            foreach ($secondary_roles as $secondary_role) {
                if (isset($all_roles[$secondary_role]) && $primary_role != $secondary_role) {
                    $user->add_role($secondary_role);
                }
            }
        }

        // add capabilities of user roles
        $user->update_user_level_from_caps();


        // add capabilities
        if (!empty($capabilities)) {
            foreach ($capabilities as $capability) {
                $user->add_cap($capability);
            }
        }


        return $user;
    }


}

?>