<?php
/**
 * Including CSS  for addmin setting
 */

if (!class_exists('WbCom_BP_Activity_Filter_Script_Includer')) {
	class WbCom_BP_Activity_Filter_Script_Includer {
		/**
		 * Constructor
		 */
		public function __construct() {
			/**
			 * Adding style for admin settings
			 */
			add_action('admin_enqueue_scripts', array(&$this,'include_admin_css_function'));

		}

		/**
		 * Adding css files
		 */
		public function include_admin_css_function () {
			// Register and enqueue style
			wp_register_style( 'custom_wp_admin_css', plugins_url('css/bp-activity-filter.css', __FILE__), false, '1.0.0' );
			wp_register_style( 'font_awesome_css', plugins_url('css/font-awesome.min.css', __FILE__), false, '1.0.0' );

			wp_enqueue_style( 'custom_wp_admin_css' );
			wp_enqueue_style( 'font_awesome_css' );
			if ( !wp_script_is( 'jquery-ui-accordion', 'enqueued' ) ) {
				wp_enqueue_script( 'jquery-ui-accordion' );
			}
			wp_enqueue_script( 'custom_wp_admin_js', plugin_dir_url( __FILE__ ) . 'js/bp-activity-filter.js', array( 'jquery' ), '1.0.0', false );
		}

	}
}
if (class_exists('WbCom_BP_Activity_Filter_Script_Includer')) {
	$script_includer = new WbCom_BP_Activity_Filter_Script_Includer();
}