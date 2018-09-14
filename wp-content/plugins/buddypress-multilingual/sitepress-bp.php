<?php
/*
  Plugin Name: BuddyPress Multilingual
  Plugin URI: http://wpml.org/?page_id=2890
  Description: BuddyPress Multilingual. <a href="http://wpml.org/?page_id=2890">Documentation</a>.
  Author: OnTheGoSystems
  Author URI: http://www.onthegosystems.com
  Version: 1.5.6
 */

define( 'BPML_VERSION', '1.5.6' );
define( 'BPML_RELPATH', plugins_url( '', __FILE__ ) );
add_action( 'plugins_loaded', 'bpml_init', 11 );

function bpml_init() {
    require_once dirname( __FILE__ ) . '/includes/functions.php';
    if ( defined( 'BP_VERSION' ) && did_action( 'wpml_loaded' ) ) {
        if ( bpml_is_langauge_as_param() ) {
            add_action( 'admin_notices', 'bpml_admin_notice_wpml_settings' );
        } else {
            $apply_filters = false;
            /*
             * Check if frontend BP AJAX request
             * BPML attaches ?lang=[code]&bpml_filter=true to admin ajax url using:
             * add_filter('bp_core_ajax_url', 'BPML_Filters::core_ajax_url_filter');
             */
            if ( defined( 'DOING_AJAX' ) && DOING_AJAX && isset( $_GET['bpml_filter'] ) ) {
                $apply_filters = true;
            }
            /*
             * Heartbeat WP API - BP latest activity AJAX status update
             * Displayed on activity page, AJAX updated list of activities.
             * Hooks 'heartbeat_received' and 'heartbeat_nopriv_received'
             * cannot be used because filters need to be applied earlier.
             */
            if ( isset($_POST['action']) && $_POST['action'] == 'heartbeat'
                    && isset( $_POST['screen_id'] ) && $_POST['screen_id'] == 'front'
                    && !empty( $_POST['data']['bp_activity_last_recorded'] ) ) {
                $apply_filters = true;
            }
            // Allow uploading cover images from screens in other languages
            if ( defined('DOING_AJAX') && isset($_POST['action'])
                    && $_POST['action'] == 'bp_cover_image_upload' ) {
                $apply_filters = true;
            }
            // Always on frontend
            if ( !is_admin() || $apply_filters ) {
                include_once dirname( __FILE__ ) . '/includes/class.filters.php';
                // Verbose page rewrite rules
                if ( defined( 'BPML_USE_VERBOSE_PAGE_RULES' ) && BPML_USE_VERBOSE_PAGE_RULES ) {
                    add_action( 'init', 'bpml_use_verbose_rules' );
                    add_filter( 'page_rewrite_rules', 'bpml_page_rewrite_rules_filter' );
                    add_filter( 'rewrite_rules_array', 'bpml_rewrite_rules_array_filter' );
                }
            }

            // XProfile
            include_once dirname( __FILE__ ) . '/includes/class.xprofile.php';

	        include_once dirname( __FILE__ ) . '/includes/class-bpml-compatibility.php';
	        $bpml_compatibility = new BPML_Compatibility();
	        $bpml_compatibility->add_hooks();

        }
    } else if ( is_admin() ) {
        add_action( 'admin_notices', 'bpml_admin_notice_required_plugins' );
    }
}
