<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class that provides compatibility code with other plugins
 *
 * @package Black_Studio_TinyMCE_Widget
 * @since 2.0.0
 */

if ( ! class_exists( 'Black_Studio_TinyMCE_Compatibility_Plugins' ) ) {

	final class Black_Studio_TinyMCE_Compatibility_Plugins {

		/**
		 * The single instance of the class
		 *
		 * @var object
		 * @since 2.0.0
		 */
		protected static $_instance = null;

		/**
		 * Flag to keep track of removed WPML filter on widget title
		 *
		 * @var boolean
		 * @since 2.6.1
		 */
		private $wpml_removed_widget_title_filter = false;

		/**
		 * Flag to keep track of removed WPML filter on widget text
		 *
		 * @var boolean
		 * @since 2.6.1
		 */
		private $wpml_removed_widget_text_filter = false;

		/**
		 * Return the single class instance
		 *
		 * @param string[] $plugins
		 * @return object
		 * @since 2.0.0
		 */
		public static function instance( $plugins = array() ) {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self( $plugins );
			}
			return self::$_instance;
		}

		/**
		 * Class constructor
		 *
		 * @param string[] $plugins
		 * @since 2.0.0
		 */
		protected function __construct( $plugins ) {
			foreach ( $plugins as $plugin ) {
				if ( is_callable( array( $this, $plugin ), false ) ) {
					$this->$plugin();
				}
			}
			if ( ! function_exists( 'is_plugin_active' ) ) {
				include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			}
		}

		/**
		 * Prevent the class from being cloned
		 *
		 * @return void
		 * @since 2.0.0
		 */
		protected function __clone() {
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; uh?' ), '2.0' );
		}

		/**
		 * Compatibility with WPML
		 *
		 * @uses add_filter()
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function wpml() {
			add_action( 'plugins_loaded', array( $this, 'wpml_init', 20 ) );
			add_action( 'black_studio_tinymce_before_widget', array( $this, 'wpml_widget_before' ), 10, 2 );
			add_action( 'black_studio_tinymce_after_widget', array( $this, 'wpml_widget_after' ), 10, 2 );
			add_filter( 'black_studio_tinymce_widget_update', array( $this, 'wpml_widget_update' ), 10, 2 );
			add_action( 'black_studio_tinymce_before_editor', array( $this, 'wpml_check_deprecated_translations' ), 5, 2 );
			add_filter( 'widget_text', array( $this, 'wpml_widget_text' ), 2, 3 );
		}

		/**
		 * Helper function to get WPML version
		 *
		 * @uses get_plugin_data()
		 *
		 * @return string
		 * @since 2.6.0
		 */
		public function wpml_get_version() {
			$wpml_data = get_plugin_data( WP_PLUGIN_DIR . '/sitepress-multilingual-cms/sitepress.php', false, false );
			return $wpml_data['Version'];
		}

		/**
		 * Initialize compatibility with WPML and WPML Widgets plugins
		 *
		 * @uses is_plugin_active()
		 * @uses has_action()
		 * @uses remove_action()
		 *
		 * @return void
		 * @since 2.3.1
		 */
		public function wpml_init() {
			if ( is_plugin_active( 'sitepress-multilingual-cms/sitepress.php' ) && is_plugin_active( 'wpml-widgets/wpml-widgets.php' ) ) {
				if ( false !== has_action( 'update_option_widget_black-studio-tinymce', 'icl_st_update_widget_title_actions' ) ) {
					remove_action( 'update_option_widget_black-studio-tinymce', 'icl_st_update_widget_title_actions', 5 );
				}
			}
		}

		/**
		 * Disable WPML String translation native behavior
		 *
		 * @uses remove_filter()
		 *
		 * @param mixed[] $args
		 * @param mixed[] $instance
		 * @return void
		 * @since 2.3.0
		 */
		public function wpml_widget_before( $args, $instance ) {
			if ( is_plugin_active( 'sitepress-multilingual-cms/sitepress.php' ) ) {
				// Avoid native WPML string translation of widget titles
				// for widgets inserted in pages built with Page Builder (SiteOrigin panels)
				// and also when WPML Widgets is active and for WPML versions from 3.8.0 on
				if ( false !== has_filter( 'widget_title', 'icl_sw_filters_widget_title' ) ) {
					if ( isset( $instance['panels_info'] ) || isset( $instance['wp_page_widget'] ) || is_plugin_active( 'wpml-widgets/wpml-widgets.php' ) || version_compare( $this->wpml_get_version(), '3.8.0' ) >= 0 ) {
						remove_filter( 'widget_title', 'icl_sw_filters_widget_title', 0 );
						$this->wpml_removed_widget_title_filter = true;
					}
				}
				// Avoid native WPML string translation of widget texts (for all widgets)
				// Note: Black Studio TinyMCE Widget already supports WPML string translation,
				// so this is needed to prevent duplicate translations
				if ( false !== has_filter( 'widget_text', 'icl_sw_filters_widget_text' ) ) {
					remove_filter( 'widget_text', 'icl_sw_filters_widget_text', 0 );
					$this->wpml_removed_widget_text_filter = true;
				}
			}

		}

		/**
		 * Re-Enable WPML String translation native behavior
		 *
		 * @uses add_filter()
		 *
		 * @param mixed[] $args
		 * @param mixed[] $instance
		 * @return void
		 * @since 2.3.0
		 */
		public function wpml_widget_after( $args, $instance ) {
			if ( is_plugin_active( 'sitepress-multilingual-cms/sitepress.php' ) ) {
				// Restore widget title's native WPML string translation filter if it was removed
				if ( $this->wpml_removed_widget_title_filter ) {
					if ( false === has_filter( 'widget_title', 'icl_sw_filters_widget_title' ) && function_exists( 'icl_sw_filters_widget_title' ) ) {
						add_filter( 'widget_title', 'icl_sw_filters_widget_title', 0 );
						$this->wpml_removed_widget_title_filter = false;
					}
				}
				// Restore widget text's native WPML string translation filter if it was removed
				if ( $this->wpml_removed_widget_text_filter ) {
					if ( false === has_filter( 'widget_text', 'icl_sw_filters_widget_text' ) && function_exists( 'icl_sw_filters_widget_text' ) ) {
						add_filter( 'widget_text', 'icl_sw_filters_widget_text', 0 );
						$this->wpml_removed_widget_text_filter = false;
					}
				}
			}
		}

		/**
		 * Add widget text to WPML String translation
		 *
		 * @uses is_plugin_active()
		 * @uses icl_register_string() Part of WPML
		 *
		 * @param mixed[] $instance
		 * @param object $widget
		 * @return mixed[]
		 * @since 2.0.0
		 */
		public function wpml_widget_update( $instance, $widget ) {
			if ( is_plugin_active( 'sitepress-multilingual-cms/sitepress.php' ) &&
				 version_compare( $this->wpml_get_version(), '3.8.0' ) < 0 &&
				 ! is_plugin_active( 'wpml-widgets/wpml-widgets.php' )
			) {
				if ( function_exists( 'icl_register_string' ) && ! empty( $widget->number ) ) {
					// Avoid translation of Page Builder (SiteOrigin panels) and WP Page Widget widgets
					if ( ! isset( $instance['panels_info'] ) && ! isset( $instance['wp_page_widget'] ) ) {
						icl_register_string( 'Widgets', 'widget body - ' . $widget->id_base . '-' . $widget->number, $instance['text'] );
					}
				}
			}
			return $instance;
		}

		/**
		 * Translate widget text
		 *
		 * @uses is_plugin_active()
		 * @uses icl_t() Part of WPML
		 * @uses icl_st_is_registered_string() Part of WPML
		 *
		 * @param string $text
		 * @param mixed[]|null $instance
		 * @param object|null $widget
		 * @return string
		 * @since 2.0.0
		 */
		public function wpml_widget_text( $text, $instance = null, $widget = null ) {
			if ( is_plugin_active( 'sitepress-multilingual-cms/sitepress.php' ) && ! is_plugin_active( 'wpml-widgets/wpml-widgets.php' ) ) {
				if ( bstw()->check_widget( $widget ) && ! empty( $instance ) ) {
					if ( function_exists( 'icl_t' ) && function_exists( 'icl_st_is_registered_string' ) ) {
						// Avoid translation of Page Builder (SiteOrigin panels) and WP Page Widget widgets
						if ( ! isset( $instance['panels_info'] ) && ! isset( $instance['wp_page_widget'] ) ) {
							if ( icl_st_is_registered_string( 'Widgets', 'widget body - ' . $widget->id_base . '-' . $widget->number ) ) {
								$text = icl_t( 'Widgets', 'widget body - ' . $widget->id_base . '-' . $widget->number, $text );
							}
						}
					}
				}
			}
			return $text;
		}

		/**
		 * Check for existing deprecated translations (made with WPML String Translations plugin) and display warning
		 *
		 * @uses is_plugin_active()
		 * @uses icl_st_is_registered_string() Part of WPML
		 * @uses admin_url()
		 *
		 * @param mixed[]|null $instance
		 * @param object|null $widget
		 * @return void
		 * @since 2.6.0
		 */
		public function wpml_check_deprecated_translations( $instance, $widget ) {
			if ( is_plugin_active( 'sitepress-multilingual-cms/sitepress.php' ) && version_compare( $this->wpml_get_version(), '3.8.0' ) >= 0 ) {
				if ( function_exists( 'icl_st_is_registered_string' ) ) {
					if ( icl_st_is_registered_string( 'Widgets', 'widget body - ' . $widget->id_base . '-' . $widget->number ) ) {
						$wpml_st_url = admin_url( 'admin.php?page=wpml-string-translation%2Fmenu%2Fstring-translation.php&context=Widgets' );
						echo '<div class="notice notice-warning inline"><p>';
						/* translators: Warning displayed when deprecated translations of the current widget are detected */
						echo sprintf( __( 'WARNING: This widget has one or more translations made using WPML String Translation plugin, which is now a deprecated method of translating widgets, in favor of the "Display on language" dropdown introduced with WPML 3.8. Please migrate your existing translations by creating new widgets and selecting the language of this widget and the new ones accordingly. Finally delete the existing translations from <a href="%s">WPML String Translation interface</a>.', 'black-studio-tinymce-widget' ), esc_url( $wpml_st_url ) );
						echo '</p></div>';
					}
				}
			}
		}

		/**
		 * Compatibility for WP Page Widget plugin
		 *
		 * @uses add_action()
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function wp_page_widget() {
			add_action( 'init', array( $this, 'wp_page_widget_init' ), 0 );
		}

		/**
		 * Initialize compatibility for WP Page Widget plugin (only for WordPress 3.3+)
		 *
		 * @uses add_filter()
		 * @uses add_action()
		 * @uses is_plugin_active()
		 * @uses get_bloginfo()
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function wp_page_widget_init() {
			if ( is_admin() && is_plugin_active( 'wp-page-widget/wp-page-widgets.php' ) && version_compare( get_bloginfo( 'version' ), '3.3', '>=' ) ) {
				add_filter( 'black_studio_tinymce_enable_pages', array( $this, 'wp_page_widget_enable_pages' ) );
				add_action( 'admin_print_scripts', array( $this, 'wp_page_widget_enqueue_script' ) );
				add_filter( 'black_studio_tinymce_widget_update', array( $this, 'wp_page_widget_add_data' ), 10, 2 );
			}
		}

		/**
		 * Enable filter for WP Page Widget plugin
		 *
		 * @param string[] $pages
		 * @return string[]
		 * @since 2.0.0
		 */
		public function wp_page_widget_enable_pages( $pages ) {
			$pages[] = 'post-new.php';
			$pages[] = 'post.php';
			if ( isset( $_GET['action'] ) && 'edit' == $_GET['action'] ) {
				$pages[] = 'edit-tags.php';
			}
			if ( isset( $_GET['page'] ) && in_array( $_GET['page'], array( 'pw-front-page', 'pw-search-page' ) ) ) {
				$pages[] = 'admin.php';
			}
			return $pages;
		}

		/**
		 * Add WP Page Widget marker
		 *
		 * @param mixed[] $instance
		 * @param object $widget
		 * @return mixed[]
		 * @since 2.5.0
		 */
		public function wp_page_widget_add_data( $instance, $widget ) {
			if ( bstw()->check_widget( $widget ) && ! empty( $instance ) ) {
				if ( isset( $_POST['action'] ) && 'pw-save-widget' == $_POST['action'] ) {
					$instance['wp_page_widget'] = true;
				}
			}
			return $instance;
		}

		/**
		 * Enqueue script for WP Page Widget plugin
		 *
		 * @uses apply_filters()
		 * @uses wp_enqueue_script()
		 * @uses plugins_url()
		 * @uses SCRIPT_DEBUG
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function wp_page_widget_enqueue_script() {
			$main_script = apply_filters( 'black-studio-tinymce-widget-script', 'black-studio-tinymce-widget' );
			$compat_script = 'wp-page-widget';
			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			wp_enqueue_script(
				$compat_script,
				plugins_url( 'js/' . $compat_script . $suffix . '.js', dirname( __FILE__ ) ),
				array( 'jquery', 'editor', 'quicktags', $main_script ),
				bstw()->get_version(),
				true
			);
		}

		/**
		 * Compatibility with Page Builder (SiteOrigin Panels)
		 *
		 * @uses add_action()
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function siteorigin_panels() {
			add_action( 'admin_init', array( $this, 'siteorigin_panels_disable_compat' ), 7 );
			add_action( 'admin_init', array( $this, 'siteorigin_panels_admin_init' ) );
		}

		/**
		 * Initialize compatibility for Page Builder (SiteOrigin Panels)
		 *
		 * @uses add_filter()
		 * @uses add_action()
		 * @uses remove_filter()
		 * @uses add_action()
		 * @uses is_plugin_active()
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function siteorigin_panels_admin_init() {
			if ( is_admin() && is_plugin_active( 'siteorigin-panels/siteorigin-panels.php' ) ) {
				add_filter( 'siteorigin_panels_widget_object', array( $this, 'siteorigin_panels_widget_object' ), 10 );
				add_filter( 'black_studio_tinymce_container_selectors', array( $this, 'siteorigin_panels_container_selectors' ) );
				add_filter( 'black_studio_tinymce_activate_events', array( $this, 'siteorigin_panels_activate_events' ) );
				add_filter( 'black_studio_tinymce_deactivate_events', array( $this, 'siteorigin_panels_deactivate_events' ) );
				add_filter( 'black_studio_tinymce_enable_pages', array( $this, 'siteorigin_panels_enable_pages' ) );
				add_filter( 'black_studio_tinymce_widget_additional_fields', array( $this, 'siteorigin_panels_additional_fields' ) );
				remove_filter( 'widget_text', array( bstw()->text_filters(), 'wpautop' ), 8 );
			}
		}

		/**
		 * Remove widget number to prevent translation when using Page Builder (SiteOrigin Panels) + WPML String Translation
		 *
		 * @param object $widget
		 * @return object
		 * @since 2.0.0
		 */
		public function siteorigin_panels_widget_object( $widget ) {
			if ( isset( $widget->id_base ) && 'black-studio-tinymce' == $widget->id_base ) {
				$widget->number = '';
			}
			return $widget;
		}

		/**
		 * Add selector for widget detection for Page Builder (SiteOrigin Panels)
		 *
		 * @param string[] $selectors
		 * @return string[]
		 * @since 2.0.0
		 */
		public function siteorigin_panels_container_selectors( $selectors ) {
			$selectors[] = 'div.panel-dialog';
			return $selectors;
		}

		/**
		 * Add activate events for Page Builder (SiteOrigin Panels)
		 *
		 * @param string[] $events
		 * @return string[]
		 * @since 2.0.0
		 */
		public function siteorigin_panels_activate_events( $events ) {
			$events[] = 'panelsopen';
			return $events;
		}

		/**
		 * Add deactivate events for Page Builder (SiteOrigin Panels)
		 *
		 * @param string[] $events
		 * @return string[]
		 * @since 2.0.0
		 */
		public function siteorigin_panels_deactivate_events( $events ) {
			$events[] = 'panelsdone';
			return $events;
		}

		/**
		 * Add pages filter to enable editor for Page Builder (SiteOrigin Panels)
		 *
		 * @param string[] $pages
		 * @return string[]
		 * @since 2.0.0
		 */
		public function siteorigin_panels_enable_pages( $pages ) {
			$pages[] = 'post-new.php';
			$pages[] = 'post.php';
			if ( isset( $_GET['page'] ) && 'so_panels_home_page' == $_GET['page'] ) {
				$pages[] = 'themes.php';
			}
			return $pages;
		}

		/**
		 * Add widget field for Page Builder (SiteOrigin Panels)
		 *
		 * @param string[] fields
		 * @return string[]
		 * @since 2.6.0
		 */
		public function siteorigin_panels_additional_fields( $fields ) {
			$fields[] = 'panels_info';
			return $fields;
		}

		/**
		 * Disable old compatibility code provided by Page Builder (SiteOrigin Panels)
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function siteorigin_panels_disable_compat( ) {
			remove_action( 'admin_init', 'siteorigin_panels_black_studio_tinymce_admin_init' );
			remove_action( 'admin_enqueue_scripts', 'siteorigin_panels_black_studio_tinymce_admin_enqueue', 15 );
		}

		/**
		 * Compatibility with Jetpack After the deadline
		 *
		 * @uses add_action()
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function jetpack_after_the_deadline() {
			add_action( 'black_studio_tinymce_load', array( $this, 'jetpack_after_the_deadline_load' ) );
		}

		/**
		 * Load Jetpack After the deadline scripts
		 *
		 * @uses add_filter()
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function jetpack_after_the_deadline_load() {
			add_filter( 'atd_load_scripts', '__return_true' );
		}

		/**
		 * Compatibility for Elementor plugin
		 *
		 * @uses add_filter()
		 *
		 * @return void
		 * @since 2.5.0
		 */
		public function elementor() {
			if ( is_admin() && isset( $_GET['action'] ) && 'elementor' == $_GET['action'] ) {
				add_filter( 'black_studio_tinymce_enable', '__return_false', 100 );
				add_action( 'widgets_init', array( $this, 'elementor_unregister_widget' ), 20 );
			}
		}

		/**
		 * Unregister Widget for Elementor plugin
		 *
		 * @uses unregister_widget()
		 *
		 * @return void
		 * @since 2.5.1
		 */
		public function elementor_unregister_widget() {
			unregister_widget( 'WP_Widget_Black_Studio_TinyMCE' );
		}

	} // END class Black_Studio_TinyMCE_Compatibility_Plugins

} // END class_exists check
