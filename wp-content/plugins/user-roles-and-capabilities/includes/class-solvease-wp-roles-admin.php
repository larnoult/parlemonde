<?php

/*
 * @since      1.0
 * @author     MAHABUB <mahabub@solvease.com>
 */

class Solvease_Roles_Capabilities_Admin
{

    /**
     * plugin name
     * @var string
     */
    private $plugin_name;

    /**
     * plugin version
     * @var string
     */
    private $plugin_version;

    /**
     * maintain the capabilities
     * @var object
     */
    protected $capability_table;

    /**
     * translation domain
     * @var string
     */
    protected $translation_domain;
    protected $plugin_caps;

    /**
     *  class construction
     * @param string $plugin_name
     * @param string $plugin_version
     * @param string $translation_domain
     */
    function __construct($plugin_name, $plugin_version, $translation_domain)
    {

        $this->plugin_name = $plugin_name;
        $this->plugin_version = $plugin_version;
        $this->translation_domain = $translation_domain;
        $this->plugin_caps = Solvease_Roles_Capabilities_User_Caps::solvease_roles_capabilities_caps();

        // reguster script
        add_action('admin_enqueue_scripts', array($this, 'solvease_roles_capabilities_register_script'));

        // regester styles
        add_action('admin_enqueue_scripts', array($this, 'solvease_roles_capabilities_register_styles'));


        add_action('wp_ajax_export_role_cap', array($this, 'export_roles_capabilities'));

        $this->capability_table = new Solvease_Roles_Capabilities_Table($translation_domain, $this->plugin_caps);
    }


    private function prepare_export_roles()
    {
        $prepared_roles = array();
        $roles_to_export = $_POST['roles_to_export'];

        $editable_roles = $this->capability_table->solvese_roles_capabilities_get_roles();

        if (empty($roles_to_export) || empty($editable_roles)) {
            return $prepared_roles;
        }
        foreach ($roles_to_export as $post_role) {
            if (isset($editable_roles[$post_role])) {
                $prepared_roles[$post_role] = $editable_roles[$post_role];
            }
        }
        return $prepared_roles;

    }

    public function export_roles_capabilities()
    {

        $prepared_roles = (array)$this->prepare_export_roles();
        header('Content-type: application/json');
        header("Content-Disposition: attachment; filename=export-roles-capabilities.txt");
        header("Pragma: no-cache");
        header("Expires: 0");
        echo json_encode($prepared_roles);
        exit();
    }

    private function solvease_roles_capabilities_menu_title()
    {
        return __('Roles & Capabilities', $this->translation_domain);
    }

    /**
     * generate plugin Menu
     */
    public function solvease_roles_capabilities_menu()
    {
        //$menu_title = __('Roles & Capabilities', $this->translation_domain);
        //$plugin_caps = Solvease_Roles_Capabilities_User_Caps::solvease_roles_capabilities_caps();

        add_submenu_page(
            'users.php', $this->solvease_roles_capabilities_menu_title(), $this->solvease_roles_capabilities_menu_title(), $this->plugin_caps['manage_all_capabilities'], 'solvease-roles-capablities', array($this->capability_table, 'solvease_roles_capabilities_action')
        );

        add_submenu_page(
            null, null, null, $this->plugin_caps['manage_user_capabilities'], 'solvease-def', array($this->capability_table, 'solvese_roles_capabilities_users_roles_caps')
        );
    }

    /**
     *  add cpabilities link in user list table
     * @param array $actions
     * @param object $user_object
     * @return string
     */
    public function solvease_roles_capabilities_add_user_row_action($actions, $user_object)
    {
        if (current_user_can($this->plugin_caps['manage_user_capabilities'])) {
            $actions['edit_user_cap'] = "<a class='solvease-edit-user-cap' href='" . wp_nonce_url("users.php?page=solvease-def&amp;user=$user_object->ID", "solvease_rnc_save_user_rc_" . $user_object->ID, "solvease_rnc_save_user_rc") . "'>" . $this->solvease_roles_capabilities_menu_title() . "</a>";
        }
        return $actions;
    }

    /**
     * add new caps to admin
     */
    public function solvease_roles_add_new_caps_to_admin()
    {
        $current_version = (int)str_replace('.', '', $this->plugin_version);
        $prev_version = get_site_option('solvease_wprc_plugin_version', '111', false);
        if ($prev_version < $current_version) {
            if ($prev_version == 111) {
                add_option('solvease_wprc_plugin_version', $current_version, '', 'no');
            }
            update_option('solvease_wprc_plugin_version', $current_version);
            $plugin_caps = Solvease_Roles_Capabilities_User_Caps::solvease_roles_capabilities_caps();
            $role = get_role('administrator');
            if (!empty($role)) {
                foreach ($plugin_caps as $cap) {
                    $role->add_cap($cap);
                }
            }
        }
    }

    /**
     * register scripts
     */
    public function solvease_roles_capabilities_register_script()
    {

        wp_register_script(
            'solvease-roles-capabilities-validator-js', plugins_url('/js/jquery.validate.min.js', dirname(__FILE__)), array('jquery')
        );

        wp_register_script(
            'solvease-roles-capabilities-custom-js', plugins_url('/js/custom_script.js?t='.time(), dirname(__FILE__)), array('jquery')
        );

        wp_register_script(
            'solvease-roles-capabilities-sticky-js', plugins_url('/js/sticky.js', dirname(__FILE__)), array('jquery')
        );

        wp_register_script(
            'solvease-roles-capabilities-uniform-js', plugins_url('/js/jquery.uniform.js', dirname(__FILE__)), array('jquery')
        );


        wp_register_script(
            'solvease-roles-capabilities-bootstrap-js', plugins_url('/js/bootstrap.min.js', dirname(__FILE__)), array('jquery')
        );
    }

    /**
     * register Stylesheet
     */
    public function solvease_roles_capabilities_register_styles()
    {
        wp_register_style('solvease-roles-capabilities-bootstrap-css', plugins_url('/css/bs-modal.css', dirname(__FILE__)));
        wp_register_style('solvease-roles-capabilities-custom-css', plugins_url('/css/custom.css', dirname(__FILE__)));

        wp_register_style('solvease-roles-capabilities-font-awesome', plugins_url('/css/font-awesome.css', dirname(__FILE__)));
        wp_register_style('solvease-roles-capabilities-uniform', plugins_url('/css/uniform.default.css', dirname(__FILE__)));
    }

}
