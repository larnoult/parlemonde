<?php

/*
 * @since      1.0
 * @author     MAHABUB <mahabub@solvease.com>
 */

class Solvease_Roles_Capabilities {

    /**
     *
     * @var type 
     */
    protected $plugin_name;

    /**
     *
     * @var type 
     */
    protected $plugin_version;

    /**
     *
     * @var type 
     */
    protected $plugin_loader;
    protected $translation_domain;

    function __construct() {
        $this->plugin_name = 'WP Roles and Capabilities';
        $this->plugin_version = '1.2.1';
        $this->translation_domain = 'solvease_wprc';
        $this->load_dependecies();
        $this->set_locale();
       
    }

    private function load_dependecies() {
        require plugin_dir_path(__FILE__) . 'class-solvease-wp-roles-capabilities-loader.php';

        require plugin_dir_path(__FILE__) . 'class-solvease-wp-roles-capabilities-table.php';

        require plugin_dir_path(__FILE__) . 'class-solvease-wp-roles-capabilities_cap_functionality.php';

        require plugin_dir_path(__FILE__) . 'class-solvease-wp-roles-admin.php';

        $this->plugin_loader = new Solvease_Roles_Capabilities_Loader();
    }

    private function define_hooks() {
        //if (is_admin()) {
            $solvease_roles_capabilities_admin = new Solvease_Roles_Capabilities_Admin($this->plugin_name, $this->plugin_version, $this->translation_domain);
            $this->plugin_loader->add_action('admin_menu', $solvease_roles_capabilities_admin, 'solvease_roles_capabilities_menu' );
            $this->plugin_loader->add_filter('user_row_actions', $solvease_roles_capabilities_admin, 'solvease_roles_capabilities_add_user_row_action', 10, 2);
            $this->plugin_loader->add_action('plugins_loaded', $solvease_roles_capabilities_admin, 'solvease_roles_add_new_caps_to_admin' );
       // }
    }

    private function set_locale() {
        
    }

    public function execute() {
         $this->define_hooks();
        $this->plugin_loader->run();
    }

    public function get_translation_domain() {
        return $this->plugin_name;
    }
    
     public function get_plugin_name() {
        return $this->translation_domain;
    }

    public function get_plugin_version() {
        return $this->plugin_version;
    }

    public function get_plugin_loader() {
        return $this->plugin_loader;
    }

}
