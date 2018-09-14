<?php

/*

  Plugin Name: BuddyPress Activity Filter
  Plugin URI: https://wbcomdesigns.com/downloads/buddypress-activity-filter/
  Description: Admin can set default and customized activities to be listed on front-end
  Version: 1.0.4
  Text Domain: bp-activity-filter
  Author: Wbcom Designs<admin@wbcomdesigns.com>
  Author URI: https://www.wbcomdesigns.com/
  License: GPL2
  License URI: https://www.gnu.org/licenses/gpl-2.0.html

 */



if (!defined('ABSPATH')) {

    wp_die('Direct Access is not Allowed');

}
define( 'BP_ACTIVITY_FILTER_PLUGIN_BASENAME',  plugin_basename( __FILE__ ) );



/**
 *  Checking for buddypress whether it is active or not
 */

function check_required_plugin_is_activated() {
   if ( ! defined( 'BP_VERSION' ) ) {
        deactivate_plugins(plugin_basename(__FILE__));
        wp_die(__('The <b>BuddyPress Activity Filter</b> plugin requires <b>BuddyPress</b> plugin to be installed and active. Return to <a href="' . admin_url('plugins.php') . '">Plugins</a>', 'bp-activity-filter'));
    }
}

register_activation_hook(__FILE__, 'check_required_plugin_is_activated');

/**
 * Defining class WbCom_BP_Activity_Filter is not exist
 */

if (!class_exists('WbCom_BP_Activity_Filter')) {



    class WbCom_BP_Activity_Filter {



        /**
         * Constructor
         */

        public function __construct() {
            global $bp;
            /**
             * Adding text domain
             */
            $this->bp_activity_filter_load_textdomain();

            /**
             * Adding setting link on plugin listing page
             */

            add_filter('plugin_action_links_' . plugin_basename(__FILE__), array(&$this, 'bp_activity_filter_plugin_actions'), 10, 2);



            /**
             * Including scripts files for admin setting
             */

            require_once plugin_dir_path(__FILE__) . 'admin/class-bp-activity-filter-admin-script-includer.php';



            /**
             * Including file for admin setting
             */

            require_once plugin_dir_path(__FILE__) . 'admin/class-bp-activity-filter-admin-setting.php';



            //require_once plugin_dir_path(__FILE__) . 'admin/bpaf-admin-options.php';

            /**
             * Including file for saving admin setting
             */

            require_once plugin_dir_path(__FILE__) . 'admin/class-bp-activity-filter-admin-setting-save.php';



            /**
             * Including file for dropdown option filter setting on front-end
             */

            require_once plugin_dir_path(__FILE__) . 'templates/class-bp-activity-filter-dropdown.php';



            /**
             * Including file for dropdown option filter setting on front-end
             */

            require_once plugin_dir_path(__FILE__) . 'admin/class-bp-activity-filter-add-post-support.php';


            /**
             * Including file for dropdown option filter setting on front-end
             */

            require_once plugin_dir_path(__FILE__) . 'templates/class-bp-activity-filter-query.php';
        }

        

        //Load plugin textdomain.

        public function bp_activity_filter_load_textdomain() {

            $domain = "bp-activity-filter";

            $locale = apply_filters('plugin_locale', get_locale(), $domain);

            load_textdomain($domain, 'languages/' . $domain . '-' . $locale . '.pot');

            $var = load_plugin_textdomain($domain, false, plugin_basename(dirname(__FILE__)) . '/languages');

        }



        /**
         * @desc Adds the Settings link to the plugin activate/deactivate page
         */

        public function bp_activity_filter_plugin_actions($links, $file) {

            $settings_link = '<a href="' . admin_url("admin.php?page=bp_activity_filter_settings") . '">' . __('Settings', 'bp-activity-filter') . '</a>';

            array_unshift($links, $settings_link); // before other links

            return $links;

        }
    }
}

function bpfilter_check_config(){
    global $bp;
    
    $config = array(
        'blog_status'    => false, 
        'network_active' => false, 
        'network_status' => true 
    );
    if ( get_current_blog_id() == bp_get_root_blog_id() ) {
        $config['blog_status'] = true;
    }
    
    $network_plugins = get_site_option( 'active_sitewide_plugins', array() );

    // No Network plugins
    if ( empty( $network_plugins ) )

    // Looking for BuddyPress and bp-activity plugin
    $check[] = $bp->basename;
    $check[] = BP_ACTIVITY_FILTER_PLUGIN_BASENAME;

    // Are they active on the network ?
    $network_active = array_diff( $check, array_keys( $network_plugins ) );
    
    // If result is 1, your plugin is network activated
    // and not BuddyPress or vice & versa. Config is not ok
    if ( count( $network_active ) == 1 )
        $config['network_status'] = false;

    // We need to know if the plugin is network activated to choose the right
    // notice ( admin or network_admin ) to display the warning message.
    $config['network_active'] = isset( $network_plugins[ BP_ACTIVITY_FILTER_PLUGIN_BASENAME ] );

    // if BuddyPress config is different than bp-activity plugin
    if ( !$config['blog_status'] || !$config['network_status'] ) {

        $warnings = array();
        if ( !bp_core_do_network_admin() && !$config['blog_status'] ) {
            add_action( 'admin_notices', 'bpfilter_same_blog' );
            $warnings[] = __( 'BuddyPress Activity Filter requires to be activated on the blog where BuddyPress is activated.', 'bp-activity-filter' );
        }

        if ( bp_core_do_network_admin() && !$config['network_status'] ) {
            add_action( 'admin_notices', 'bpfilter_same_network_config' );
            $warnings[] = __( 'BuddyPress Activity Filter and BuddyPress need to share the same network configuration.', 'bp-activity-filter' );
        }

        if ( ! empty( $warnings ) ) :
            return false;
        endif;
    } 
    return true;
}


add_action( 'bp_include', 'bp_activity_filter_init' );
function bp_activity_filter_init(){
    if (bpfilter_check_config() && class_exists('WbCom_BP_Activity_Filter')) {
        $GLOBALS['activity_filter'] = new WbCom_BP_Activity_Filter();
    }
}
function bpfilter_same_blog(){
    echo '<div class="error"><p>'
    . esc_html( __( 'BuddyPress Activity Filter requires to be activated on the blog where BuddyPress is activated.', 'bp-activity-filter' ) )
    . '</p></div>';
}

function bpfilter_same_network_config(){
    echo '<div class="error"><p>'
    . esc_html( __( 'BuddyPress Activity Filter and BuddyPress need to share the same network configuration.', 'bp-activity-filter' ) )
    . '</p></div>';
}
