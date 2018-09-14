<?php

class CP_TinyMCE {

	/**
	 * CP_TinyMCE constructor.
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'content_protector_enqueue_admin_scripts' ) );
		add_filter( 'mce_buttons', array( $this, 'content_protector_register_tinymce_button' ) );
		add_filter( 'mce_external_plugins', array( $this, 'content_protector_register_tinymce_plugin' ) );
		add_action( 'admin_head', array( $this, 'content_protector_save_tinymce_data' ) );
	}

	/**
	 *
	 */
	public function content_protector_enqueue_admin_scripts() {
		wp_enqueue_script( 'jquery' );
	}


	/**
	 * @param $button_array
	 *
	 * @return mixed
	 */
	public function content_protector_register_tinymce_button( $button_array ) {
		global $current_screen; //  WordPress contextual information about where we are.

		$type = $current_screen->post_type;

		if ( is_admin() && ( $type == 'post' || $type == 'page' ) ) {
			//  This ID must match the one in our button definition in tinymce-plugin.js
			array_push( $button_array, 'content_protector_button' );
		}

		return $button_array;
	}


	/**
	 * @param $plugin_array
	 *
	 * @return mixed
	 */
	public function content_protector_register_tinymce_plugin( $plugin_array ) {

		global $current_screen; //  WordPress contextual information about where we are.

		$type = $current_screen->post_type;

		if ( is_admin() && ( $type == 'post' || $type == 'page' ) ) {
			//  Okay, our conditions for registering the plugin have been met. Therefore,
			//  we need to tack on the new plugin file to the plugin array.  The array
			//  key in the plugin array must match the plugin name in tinymce-plugin.js
			$plugin_array['content_protector_plugin'] = CONTENT_PROTECTOR_PLUGIN_ASSET_URL . '/admin/inc/tinymce/tinymce-plugin.js';
		}

		return $plugin_array;
	}

	/**
	 *
	 */
	public function content_protector_save_tinymce_data() {
		?>
        <script type='text/javascript'>
            var content_protector_data = {
                //'php_version': '<?php echo phpversion(); ?>'
            };
        </script>
		<?php
	}
}