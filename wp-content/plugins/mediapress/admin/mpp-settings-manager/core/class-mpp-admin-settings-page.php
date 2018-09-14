<?php
// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 0 );
}

/**
 * This class represents an Admin page
 *
 * It could be a newly generated page or just an existing page
 * If the page exists, It will inject the sections/fields to that page
 */
class MPP_Admin_Settings_Page {

	/**
	 * Unique page slug where you want to show this page
	 *
	 * @var string
	 */
	private $page = '';

	/**
	 * The option name to be stored in options table
	 *
	 * If using individual field name as option is not enabled, this is used to store all the options in a multidimensional array
	 *
	 * @var string
	 */
	private $option_name = '';

	/**
	 * Option group name.
	 *
	 * @var string
	 */
	private $optgroup = '';

	/**
	 * Settings Panel array
	 *
	 * @var  MPP_Admin_Settings_Panel[]
	 */
	private $panels = array();

	/**
	 * Callback stack.
	 *
	 * @var array
	 */
	private $cb_stack = array(); // field_name=>callback stack.

	/**
	 * Use unique option name for each settings? if enabled, each field will be individually stored in the options table
	 *
	 * @var boolean
	 */
	private $use_unique_option = false;

	/**
	 * Should we store the settings network wide.
	 *
	 * @var bool
	 */
	private $is_network_mode = false;

	/**
	 * Should we use bp_get_option/set_option for storing/retriving.
	 *
	 * @var bool
	 */
	private $is_bp_mode = false;

	/**
	 *  Settings Page constructor
	 *
	 * @param string $page unique page slug.
	 */
	public function __construct( $page ) {

		$this->page = $page;
		$this->set_option_name( $page );
		// by default, set optgroup same as page.
		$this->set_optgroup( $page );
	}

	/**
	 * Registers settings sections and fields
	 *
	 * This should be called at admin_init action
	 * If you are using existing page, make sure to attach your admin_init hook to low priority
	 */
	public function init() {

		$global_option_name = $this->get_option_name();

		// check if the option exists, if not, let us add it.
		if ( ! $this->using_unique_option() ) {

			if ( false == get_option( $global_option_name ) ) {
				add_option( $global_option_name );
			}
		}
		// register settings sections
		// for every section.
		foreach ( $this->panels as $panel ) {

			$sections = $panel->get_sections();

			foreach ( $sections as $section ) {

				// for individual section.
				if ( $section->get_disc() ) {

					$desc     = '<div class="inside">' . $section->get_disc() . '</div>';
					$callback = create_function( '', 'echo "' . str_replace( '"', '\"', $desc ) . '";' );

				} else {
					$callback = '__return_false';
				}

				$section_id = $panel->get_id() . '-' . $section->get_id();

				add_settings_section( $section_id, $section->get_title(), $callback, $this->get_page() );


				// register settings fields.
				foreach ( $section->get_fields() as $field ) {


					$option_name = $global_option_name . '[' . $field->get_name() . ']';
					// when using local.
					if ( $this->using_unique_option() ) {

						if ( false == get_option( $field->get_name() ) ) {
							add_option( $field->get_name() );
						}
						// override option name.
						$option_name = $field->get_name();
					}

					$args = array(
						'section'    => $section_id,
						'std'        => $field->get_default(),
						'option_key' => $option_name,
						'value'      => $this->get_option( $field ),
						'base_name'  => $global_option_name,

					);

					$this->cb_stack[ $field->get_id() ] = $field->get_sanitize_cb();

					add_settings_field( $option_name, $field->get_label(), array(
						$field,
						'render',
					), $this->get_page(), $section_id, $args );

					// when using local.
					if ( $this->using_unique_option() ) {
						register_setting( $this->get_optgroup(), $field->get_name(), array( $field, 'sanitize' ) );
					}
				}
			}

			// when using only one option to store all values.
			if ( ! $this->using_unique_option() ) {
				register_setting( $this->get_optgroup(), $this->get_option_name(), array( $this, 'sanitize_options' ) );
			}
		}
	}

	/**
	 * Add new Setting Panel
	 *
	 * @param  string $id section id.
	 * @param  string $title section title.
	 * @param  string $desc Section description.
	 *
	 * @return MPP_Admin_Settings_Panel
	 */
	public function add_panel( $id, $title, $desc = '' ) {

		$panel_id = $id;

		$this->panels[ $panel_id ] = new MPP_Admin_Settings_Panel( $id, $title, $desc );

		return $this->panels[ $panel_id ];

	}

	/**
	 * Add multiple panels
	 *
	 * @param array $panels panels array.
	 *
	 * @return MPP_Admin_Settings_Page
	 */
	public function add_panels( $panels ) {

		foreach ( $panels as $id => $title ) {
			$this->add_panel( $id, $title );
		}

		return $this;
	}

	/**
	 * Get the panel object.
	 *
	 * @param string $id panel id.
	 *
	 * @return MPP_Admin_Settings_Panel
	 */
	public function get_panel( $id ) {
		return isset( $this->panels[ $id ] ) ? $this->panels[ $id ] : false;
	}

	/**
	 * Get the page object.
	 *
	 * @return string page slug
	 */
	public function get_page() {
		return $this->page;
	}


	/**
	 * Get the value of a settings field
	 *
	 * @param MPP_Admin_Settings_Field $field field object.
	 *
	 * @return string
	 */
	public function get_option( $field ) {

		$option  = $field->get_name();
		$default = $field->get_default();

		if ( ! isset( $default ) ) {
			$default = '';
		}

		$value = null;

		$function_name = 'get_option';// use get_option function.
		// if the page is in network mode, use get_site_option.
		if ( $this->is_network_mode() ) {
			$function_name = 'get_site_option';
		} elseif ( $this->is_bp_mode() ) {
			if ( function_exists( 'bp_get_option' ) ) {
				$function_name = 'bp_get_option';
			}
		}

		if ( ! $this->using_unique_option() ) {
			$options = $function_name( $this->get_option_name() );

			if ( isset( $options[ $option ] ) ) {
				$value = $options[ $option ];
			}
		} else {
			$value = $function_name( $option, $default );
		}

		$value = $field->get_value( $value );

		if ( is_null( $value ) ) {
			$value = $default;
		}

		return $value;
	}

	/**
	 * If use unique option is enabled, each setting field is stored in the options table as individual item, so an item can be retrieved as get_option('setting_field_name');
	 * otherwise, all the setting field option is stored in a single option as array and that name of option is page_name or option_name depending on which one is set
	 *
	 * @return MPP_Admin_Settings_Page
	 */
	public function use_unique_option() {

		$this->use_unique_option = true;
		return $this;

	}

	/**
	 * Do we store each setting as individual option in the options table?
	 *
	 * @return $this
	 */
	public function use_single_option() {

		$this->use_unique_option = false;

		if ( ! isset( $this->option_name ) ) {
			$this->set_option_name( $this->page );
		}

		return $this;
	}

	/**
	 * Are we using unique options to store each field
	 *
	 * @return bool
	 */
	public function using_unique_option() {
		return $this->use_unique_option;
	}

	/**
	 * Set network mode.
	 *
	 * @return $this
	 */
	public function set_network_mode() {
		$this->is_network_mode = true;

		return $this;
	}

	/**
	 * Is it network mode?
	 *
	 * @return bool
	 */
	public function is_network_mode() {
		return $this->is_network_mode;
	}

	/**
	 * Set BuddyPress Mode.
	 *
	 * @return $this
	 */
	public function set_bp_mode() {
		$this->is_bp_mode = true;

		return $this;
	}

	/**
	 * Check if it is BuddyPress mode.
	 *
	 * @return bool
	 */
	public function is_bp_mode() {
		return $this->is_bp_mode;
	}

	/**
	 * Reset mode.
	 *
	 * @return $this
	 */
	public function reset_mode() {

		$this->is_network_mode = false;
		$this->is_bp_mode      = false;

		return $this;
	}

	/**
	 * Set an option name if you want. It is only used if using_unique_option is disabled
	 *
	 * @param string $option_name option name.
	 *
	 * @return MPP_Admin_Settings_Page
	 */
	public function set_option_name( $option_name ) {

		$this->option_name = $option_name;

		return $this;
	}

	/**
	 * Get the option name
	 *
	 * @return string
	 */
	public function get_option_name() {
		return $this->option_name;
	}

	/**
	 * Set option group.
	 *
	 * @param string $optgroup optgroup name.
	 */
	public function set_optgroup( $optgroup ) {
		$this->optgroup = $optgroup;
	}

	/**
	 * Get optgroup name.
	 *
	 * @return string
	 */
	public function get_optgroup() {
		return $this->optgroup;
	}

	/**
	 * Show navigations as tab
	 *
	 * Shows all the settings section labels as tab
	 */
	public function show_navigation() {
		// do not show nav if it is hidden.
		$html = '<h2 class="nav-tab-wrapper">';

		foreach ( $this->panels as $panel ) {

			if ( $panel->is_empty() ) {
				continue;
			}

			$html .= sprintf( '<a href="#%1$s" class="nav-tab" id="%1$s-tab">%2$s</a>', $panel->get_id(), $panel->get_title() );
		}

		$html .= '</h2>';

		echo $html;
	}

	/**
	 * Show the settings forms
	 *
	 * This function displays every sections in a different form
	 */
	public function show_form() {
		?>
        <div class="metabox-holder">
            <div class="postbox options-postbox" style="padding:10px;">
                <form method="post" action="options.php">

					<?php settings_fields( $this->get_optgroup() ); ?>

					<?php foreach ( $this->panels as $panel ) : ?>
						<?php
						if ( $panel->is_empty() ) {
							continue;
						}
						?>
                        <div id="<?php echo $panel->get_id(); ?>"
                             class="mpp-settings-panel-tab mpp-admin-options-clearfix">

							<?php $sections = $panel->get_sections(); ?>

							<?php foreach ( $sections as $section ) : ?>
								<?php $section_id = $panel->get_id() . '-' . $section->get_id(); ?>
                                <div id="<?php echo $section_id; ?>"
                                     class="mpp-settings-section-block <?php echo $section_id; ?>">

									<?php do_action( 'mpp_admin_settings_form_top_' . $section_id, $section ); ?>

									<?php $this->do_settings_sections( $this->get_page(), $section_id ); ?>
									<?php do_action( 'mpp_admin_settings_form_bottom_' . $section_id, $section ); ?>

                                </div>
							<?php endforeach; ?>

                            <div style="padding-left: 10px">
								<?php submit_button( null, 'primary', 'submit', false ); ?>
                                <input type="button" value="<?php _e( 'Reset to default', 'mediapress' ); ?>"
                                       class="button button-danger mpp-admin-button-danger"

                                       data-warning-message="<?php _e( 'Are you sure? It will reset all MediaPress settings to default.', 'mediapress' ); ?>"/>
                            </div>
                        </div>
					<?php endforeach; ?>

                </form>
                <!-- settings reset form -->
                <form method="post" action="" id="mpp-admin-settings-reset-form">
                    <input type="hidden" name="mpp-action-reset-settings" value="1">
					<?php wp_nonce_field( 'mpp-action-reset-settings' ); ?>
                </form>

                <style type="text/css">
                    .mpp-settings-section-block {
                        padding: 15px 12px;
                        background: #fff;
                    }

                    .mpp-settings-section-block:nth-child(odd) {
                        background: #f8f8f8;
                    }

                    .mpp-settings-section-block h3 {
                        padding: 0 0;
                        font-size: 20px;
                    }

                    .mpp-settings-media-size-field-label {
                        padding: 0 5px;
                    }

                    .mpp-settings-media-size-field {
                        margin-right: 15px;
                        max-width: 65px;
                    }

                    .postbox .mpp-admin-button-danger {
                        background: #DC3D39;
                        color: #fff;
                        border: 0 none;
                        float: right;
                    }

                    .postbox .mpp-admin-button-danger:hover {
                        background: #FF4742;
                        color: #fff;
                    }

                    .mpp-admin-options-clearfix::after {
                        content: "";
                        display: table;
                        clear: both;
                    }
                </style>
            </div>
        </div>
		<?php
		$this->script();
	}


	/**
	 * Render settings page.
	 */
	public function render() {

		echo '<div class="wrap">';

		$this->show_navigation();
		$this->show_form();

		echo '</div>';
	}

	public function do_settings_sections( $page, $section_id ) {

		global $wp_settings_sections, $wp_settings_fields;

		if ( ! isset( $wp_settings_sections ) || ! isset( $wp_settings_sections[ $page ] ) ) {

			return;
		}

		$section = $wp_settings_sections[ $page ][ $section_id ];


		if ( $section['title'] ) {

			echo "<h3>{$section['title']}</h3>\n";

		}

		if ( $section['callback'] && is_callable( $section['callback'] ) ) {

			call_user_func( $section['callback'], $section );
		}


		if ( ! isset( $wp_settings_fields ) || ! isset( $wp_settings_fields[ $page ] ) || ! isset( $wp_settings_fields[ $page ][ $section['id'] ] ) ) {
			return;
		}
		//print_r( $wp_settings_fields[$page][ $section['id'] ] );

		echo '<table class="form-table">';

		do_settings_fields( $page, $section['id'] );

		echo '</table>';

	}

	/**
	 * Sanitize options callback for Settings API
	 */
	public function sanitize_options( $options ) {

		foreach ( $options as $option_slug => $option_value ) {

			$sanitize_callback = $this->cb_stack[ $option_slug ];

			// If callback is set, call it
			if ( $sanitize_callback ) {
				$options[ $option_slug ] = call_user_func( $sanitize_callback, $option_value );
				continue;
			}

			// Treat everything that's not an array as a string
			if ( ! is_array( $option_value ) ) {
				$options[ $option_slug ] = sanitize_text_field( $option_value );
				continue;
			}
		}

		return $options;
	}

	/**
	 * Tab JavaScript codes
	 *
	 * This code uses localstorage for displaying active tabs
	 */
	public function script() {
		?>
        <script>
            jQuery(document).ready(function ($) {
                // Switches option sections
                $('.mpp-settings-panel-tab').hide();
                var activetab = '';
                //check for the active tab stored in the local storage
                if (typeof(localStorage) != 'undefined') {
                    activetab = localStorage.getItem('activetab');
                }
                //if active tab is set, show it
                if (activetab != '' && $(activetab).length) {
                    $(activetab).fadeIn();
                } else {
                    //otherwise show the first tab
                    $('.mpp-settings-panel-tab:first').fadeIn();
                }

                $('.group .collapsed').each(function () {
                    $(this).find('input:checked').parent().parent().parent().nextAll().each(
                        function () {
                            if ($(this).hasClass('last')) {
                                $(this).removeClass('hidden');
                                return false;
                            }
                            $(this).filter('.hidden').removeClass('hidden');
                        });
                });

                if (activetab != '' && $(activetab + '-tab').length) {
                    $(activetab + '-tab').addClass('nav-tab-active');
                }
                else {
                    $('.nav-tab-wrapper a:first').addClass('nav-tab-active');
                }

                //on click of the tab navigation
                $('.nav-tab-wrapper a').click(function (evt) {
                    $('.nav-tab-wrapper a').removeClass('nav-tab-active');
                    $(this).addClass('nav-tab-active').blur();
                    var clicked_group = $(this).attr('href');
                    if (typeof(localStorage) != 'undefined') {
                        localStorage.setItem("activetab", $(this).attr('href'));
                    }
                    $('.mpp-settings-panel-tab').hide();
                    $(clicked_group).fadeIn();
                    evt.preventDefault();
                });
                $('.mpp-admin-button-danger').on('click', function () {
                    var warning_message = $(this).data('warning-message');

                    if (confirm(warning_message)) {
                        $('#mpp-admin-settings-reset-form').submit();
                    }
                    return false;
                })
            });
        </script>
		<?php
	}

}

