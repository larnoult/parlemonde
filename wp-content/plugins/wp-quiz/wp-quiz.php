<?php
/*
 * Plugin Name: WP Quiz
 * Plugin URI:  https://mythemeshop.com/plugins/wp-quiz/
 * Description: WP Quiz makes it incredibly easy to add professional, multimedia quizzes to any website! Fully feature rich and optimized for social sharing. Make your site more interactive!
 * Version:     1.1.8
 * Author:      MyThemeShop
 * Author URI:  https://mythemeshop.com/
 *
 * Text Domain: wp-quiz
 * Domain Path: /languages/
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'WP_Quiz_Plugin' ) ) :


	/**
	 * Register the plugin.
	 *
	 * Display the administration panel, insert JavaScript etc.
	 */
	class WP_Quiz_Plugin {

		/**
		 * Hold plugin version
		 * @var string
		 */
		public $version = '1.1.8';

		/**
		 * Hold an instance of WP_Quiz_Plugin class.
		 *
		 * @var WP_Quiz_Plugin
		 */
		protected static $instance = null;

		/**
		 * @var WP Quiz
		 */
		public $quiz = null;

		/**
		 * Plugin url.
		 * @var string
		 */
		private $plugin_url = null;

		/**
		 * Plugin path.
		 * @var string
		 */
		private $plugin_dir = null;

		/**
		 * Main WP_Quiz_Plugin instance.
		 * @return WP_Quiz_Plugin - Main instance.
		 */
		public static function get_instance() {

			if ( is_null( self::$instance ) ) {
				self::$instance = new WP_Quiz_Plugin;
			}

			return self::$instance;
		}

		/**
		 * You cannot clone this class.
		 */
		public function __clone() {
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'wp-quiz' ), $this->version );
		}

		/**
		 * You cannot unserialize instances of this class.
		 */
		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'wp-quiz' ), $this->version );
		}

		/**
		 * Constructor
		 */
		public function __construct() {
			$this->includes();
			$this->hooks();
			$this->setup_shortcode();
		}

		/**
		 * Load required classes
		 */
		private function includes() {

			//auto loader
			spl_autoload_register( array( $this, 'autoloader' ) );

			new WP_Quiz_Admin;
		}

		/**
		 * Autoload classes
		 */
		public function autoloader( $class ) {

			$dir             = $this->plugin_dir() . 'inc' . DIRECTORY_SEPARATOR;
			$class_file_name = 'class-' . str_replace( array( 'wp_quiz_', '_' ), array( '', '-' ), strtolower( $class ) ) . '.php';

			if ( file_exists( $dir . $class_file_name ) ) {
				require $dir . $class_file_name;
			}
		}

		/**
		 * Register the [wp_quiz] shortcode.
		 */
		private function setup_shortcode() {
			add_shortcode( 'wp_quiz', array( $this, 'register_shortcode' ) );
		}

		/**
		 * Hook WP Quiz into WordPress
		 */
		private function hooks() {

			// Common
			add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
			add_action( 'init', array( $this, 'register_post_type' ) );

			// Frontend
			add_action( 'wp_head', array( $this, 'inline_script' ), 1 );
			add_filter( 'the_content', array( $this, 'create_quiz_page' ) );

			// Ajax
			add_action( 'wp_ajax_check_image_file', array( $this, 'check_image_file' ) );
			add_action( 'wp_ajax_check_video_file', array( $this, 'check_video_file' ) );

			add_action( 'wp_ajax_wpquiz_get_debug_log', array( $this, 'get_debug_log' ) );

			/* Display a notice */
			add_action('admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts_callback' ) );
			add_action('admin_notices', array( $this, 'wp_quiz_admin_notice' ) );
			add_action('wp_ajax_mts_dismiss_wpquiz_notice', array( $this, 'wp_quiz_admin_notice_ignore' ));

			// FB SDK version 2.9 fix
			if ( isset( $_GET['fbs'] ) && ! empty( $_GET['fbs'] ) ) {
				add_action( 'template_redirect', array( $this, 'fb_share_fix' ) );
			}
		}

		public function admin_enqueue_scripts_callback() {
			wp_enqueue_script( 'mts_wp_quiz_admin', wp_quiz()->plugin_url() . 'assets/js/admin.js', array( 'jquery' ), wp_quiz()->version, true );
		}

		public function wp_quiz_admin_notice() {
			global $current_user ;
			$user_id = $current_user->ID;
			/* Check that the user hasn't already clicked to ignore the message */
			/* Only show the notice 2 days after plugin activation */
			if ( ! get_user_meta($user_id, 'wp_quiz_ignore_notice') && time() >= (get_option( 'wp_quiz_activated', 0 ) + (2 * 24 * 60 * 60)) ) {
				echo '<div class="updated notice-info wp-quiz-notice" id="wpquiz-notice" style="position:relative;">';
				echo __('<p>Like WP Quiz plugin? You will LOVE <a target="_blank" href="https://mythemeshop.com/plugins/wp-quiz-pro/?utm_source=WP+Quiz&utm_medium=Notification+Link&utm_content=WP+Quiz+Pro+LP&utm_campaign=WordPressOrg"><strong>WP Quiz Pro!</strong></a></p><a class="notice-dismiss wpquiz-dismiss-notice" data-ignore="0" href="#"></a>', 'wp-quiz');
				echo "</div>";
			}
			/* Other notice appears right after activating */
			/* And it gets hidden after showing 3 times */
			if ( ! get_user_meta($user_id, 'wp_quiz_ignore_notice_2') && get_option('wp_quiz_notice_views', 0) < 3 && get_option( 'wp_quiz_activated', 0 ) ) {
				$views = get_option('wp_quiz_notice_views', 0);
				update_option( 'wp_quiz_notice_views', ($views + 1) );
				echo '<div class="updated notice-info wp-quiz-notice" id="wpquiz-notice2" style="position:relative;">';
				echo '<p>';
				_e('Thank you for trying WP Quiz. We hope you will like it.', 'wp-quiz');
				echo '</p>';
				echo '<a class="notice-dismiss wpquiz-dismiss-notice" data-ignore="1" href="#"></a>';
				echo "</div>";
			}
		}

		public function wp_quiz_admin_notice_ignore() {
			global $current_user;
			$user_id = $current_user->ID;
			/* If user clicks to ignore the notice, add that to their user meta */
			if ( isset($_POST['dismiss']) ) {
				if ( '0' == $_POST['dismiss'] ) {
					add_user_meta($user_id, 'wp_quiz_ignore_notice', '1', true);
				} elseif ( '1' == $_POST['dismiss'] ) {
					add_user_meta($user_id, 'wp_quiz_ignore_notice_2', '1', true);
				}
			}
		}

		/**
		 * Initialise translations
		 */
		public function load_plugin_textdomain() {
			load_plugin_textdomain( 'wp-quiz', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		}

		/**
		 * Register Quiz post type
		 */
		public static function register_post_type() {

			$labels = array(
				'name'               => __( 'WP Quiz', 'wp-quiz' ),
				'menu_name'          => __( 'WP Quiz', 'wp-quiz' ),
				'singular_name'      => __( 'WP Quiz', 'wp-quiz' ),
				'name_admin_bar'     => _x( 'WP Quiz', 'name admin bar', 'wp-quiz' ),
				'all_items'          => __( 'All Quizzes', 'wp-quiz' ),
				'search_items'       => __( 'Search Quizzes', 'wp-quiz' ),
				'add_new'            => _x( 'Add New', 'quiz', 'wp-quiz' ),
				'add_new_item'       => __( 'Add New WP Quiz', 'wp-quiz' ),
				'new_item'           => __( 'New Quiz', 'wp-quiz' ),
				'view_item'          => __( 'View Quiz', 'wp-quiz' ),
				'edit_item'          => __( 'Edit Quiz', 'wp-quiz' ),
				'not_found'          => __( 'No Quizzes Found.', 'wp-quiz' ),
				'not_found_in_trash' => __( 'WP Quiz not found in Trash.', 'wp-quiz' ),
				'parent_item_colon'  => __( 'Parent Quiz', 'wp-quiz' ),
			);

			$args = array(
				'labels'             => $labels,
				'description'        => __( 'Holds the quizzes and their data.', 'wp-quiz' ),
				'menu_position'      => 5,
				'menu_icon'          => 'dashicons-editor-help',
				'public'             => true,
				'publicly_queryable' => true,
				'show_ui'            => true,
				'show_in_menu'       => true,
				'query_var'          => true,
				'capability_type'    => 'post',
				'has_archive'        => true,
				'hierarchical'       => false,
				'supports'           => array( 'title', 'author', 'thumbnail', 'excerpt' ),
			);

			register_post_type( 'wp_quiz', $args );
		}

		/**
		 * Shortcode used to display quiz
		 *
		 * @return string HTML output of the shortcode
		 */
		public function register_shortcode( $atts ) {

			if ( ! isset( $atts['id'] ) ) {
				return false;
			}

			// we have an ID to work with
			$quiz = get_post( $atts['id'] );

			// check if ID is correct
			if ( ! $quiz || 'wp_quiz' !== $quiz->post_type ) {
				return "<!-- wp_quiz {$atts['id']} not found -->";
			}

			// lets go
			$this->set_quiz( $atts['id'] );
			$this->quiz->enqueue_scripts();

			return $this->quiz->render_public_quiz();
		}

		/**
		 * Set the current quiz
		 */
		public function set_quiz( $id ) {

			$quiz_type  = get_post_meta( $id, 'quiz_type', true );
			$quiz_type  = str_replace( '_quiz', '', $quiz_type );
			$quiz_type  = 'WP_Quiz_' . ucwords( $quiz_type ) . '_Quiz';
			$this->quiz = new $quiz_type( $id );
		}

		/**
		 * [create_quiz_page description]
		 * @param  [type] $content [description]
		 * @return [type]          [description]
		 */
		public function create_quiz_page( $content ) {

			global $post;

			if ( 'wp_quiz' !== $post->post_type ) {
				return $content;
			}

			if ( ! is_single() ) {
				return $content;
			}

			$quiz_html = $this->register_shortcode( array( 'id' => $post->ID ) );

			return $quiz_html . $content;
		}

		public function check_image_file() {

			$output = array( 'status' => 1 );
			$check  = false;
			if ( @getimagesize( $_POST['url'] ) ) {
				$check = true;
			}

			$output['check'] = $check;
			wp_send_json( $output );
		}

		public function check_video_file() {

			$output  = array( 'status' => 1 );
			$check   = false;
			$id      = $_POST['video_id'];
			$url     = "//www.youtube.com/oembed?url=http://www.youtube.com/watch?v=$id&format=json";
			$headers = get_headers( $url );

			if ( '404' !== substr( $headers[0], 9, 3 ) ) {
				$check = true;
			}

			$output['check'] = $check;
			wp_send_json( $output );
		}

		public function get_debug_log() {
			$page = new WP_Quiz_Page_Support();
			$page->get_debug_log();
		}

		public static function activate_plugin() {

			// Don't activate on anything less than PHP 5.4.0 or WordPress 3.4
			if ( version_compare( PHP_VERSION, '5.4.0', '<' ) || version_compare( get_bloginfo( 'version' ), '3.4', '<' ) || ! function_exists( 'spl_autoload_register' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
				deactivate_plugins( basename( __FILE__ ) );
				wp_die( __( 'WP Quiz requires PHP version 5.4.0 with spl extension or greater and WordPress 3.4 or greater.', 'wp-quiz' ) );
			}

			// Dont't activate if wp quiz pro is active
			if ( defined( 'WP_QUIZ_PRO_VERSION' ) ) {
				deactivate_plugins( basename( __FILE__ ) );
				wp_die( __( 'Please deactivate WP Quiz Pro plugin', 'wp-quiz' ) );
			}

			update_option('wp_quiz_activated', time());

			include( 'inc/activate-plugin.php' );
		}

		public function fb_share_fix() {

			$data   = array_map( 'urldecode', $_GET );
			$result = get_post_meta( $data['id'], 'results', true );
			$result = isset( $result[ $data['rid'] ] ) ? $result[ $data['rid'] ] : array();

			// Picture
			if ( 'r' === $data['pic'] ) {
				$data['source'] = $result['image'];
			} elseif ( 'f' === $data['pic'] ) {
				$data['source'] = wp_get_attachment_url( get_post_thumbnail_id( $data['id'] ) );
			} elseif ( ( substr( $data['pic'], 0, 6 ) === 'image-' ) ) {
				$upload_dir            = wp_upload_dir();
				$upload_dir['baseurl'] = $upload_dir['baseurl'] . '/wp_quiz-result-images';
				$data['source']        = $upload_dir['baseurl'] . '/' . $data['pic'] . '.png';
			} else {
				$data['source'] = false;
			}

			// Description
			if ( 'r' === $data['desc'] ) {
				$data['description'] = $result['desc'];
			} elseif ( 'e' === $data['desc'] ) {
				$data['description'] = get_post_field( 'post_excerpt', $data['id'] );
			} else {
				$data['description'] = false;
			}

			$settings = get_option( 'wp_quiz_default_settings' );
			$url      = ( is_ssl() ? 'https' : 'http' ) . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

			global $post;
			$pid          = $post ? $post->ID : $data['id'];
			$original_url = get_permalink( $pid );
			?>
			<html>
				<head>
					<title><?php wp_title( '' ); ?></title>
					<meta property="fb:app_id" content="<?php echo $settings['defaults']['fb_app_id']; ?>">
					<meta property="og:type" content="website">
					<meta property="og:url" content="<?php echo esc_url( $url ); ?>">
					<meta name="twitter:card" content="summary_large_image">
					<?php
					if ( ! empty( $data['text'] ) ) :
						$title = get_the_title( $pid );
						$text  = esc_attr( $data['text'] );

						$title = $title === $text ? $title : $title . ' - ' . $text;
					?>
					<meta property="og:title" content="<?php echo $title; ?>">
					<meta property="twitter:title" content="<?php echo $title; ?>">
					<?php endif; ?>
					<?php if ( ! empty( $data['source'] ) ) : ?>
					<meta property="og:image" content="<?php echo esc_url( $data['source'] ); ?>">
					<meta property="twitter:image" content="<?php echo esc_url( $data['source'] ); ?>">
						<?php list( $img_width, $img_height ) = getimagesize( $data['source'] ); ?>
						<?php if ( isset( $img_width ) && $img_width ) : ?>
						<meta property="og:image:width" content="<?php echo $img_width; ?>">
						<?php endif; ?>
						<?php if ( isset( $img_height ) && $img_height ) : ?>
						<meta property="og:image:height" content="<?php echo $img_height; ?>">
						<?php endif; ?>
					<?php endif; ?>
					<?php if ( ! empty( $data['description'] ) ) : ?>
					<meta property="og:description" content="<?php echo esc_attr( $data['description'] ); ?>">
					<meta property="twitter:description" content="<?php echo esc_attr( $data['description'] ); ?>">
					<?php endif; ?>
					<meta http-equiv="refresh" content="0;url=<?php echo esc_url( $original_url ); ?>">
				</head>
			<body>
				Redirecting please wait....
			</body>
			</html>
			<?php
			exit;
		}

		public function inline_script() {
			$settings = get_option( 'wp_quiz_default_settings' );
			?>
				<script>
				<?php if ( ! empty( $settings['defaults']['fb_app_id'] ) ) { ?>
					window.fbAsyncInit = function() {
						FB.init({
							appId    : '<?php echo $settings['defaults']['fb_app_id']; ?>',
							xfbml    : true,
							version  : 'v2.9'
						});
					};

					(function(d, s, id){
						var js, fjs = d.getElementsByTagName(s)[0];
						if (d.getElementById(id)) {return;}
						js = d.createElement(s); js.id = id;
						js.src = "//connect.facebook.net/en_US/sdk.js";
						fjs.parentNode.insertBefore(js, fjs);
					}(document, 'script', 'facebook-jssdk'));
				<?php } ?>
				</script>
			<?php
			if ( is_singular( array( 'wp_quiz' ) ) && isset( $settings['defaults']['share_meta'] ) && 1 === $settings['defaults']['share_meta'] ) {
				global $post, $wpseo_og;
				$twitter_desc = $og_desc = str_replace( array( "\r", "\n" ), '', strip_tags( $post->post_excerpt ) );
				if ( defined( 'WPSEO_VERSION' ) ) {
					remove_action( 'wpseo_head', array( $wpseo_og, 'opengraph' ), 30 );
					remove_action( 'wpseo_head', array( 'WPSEO_Twitter', 'get_instance' ), 40 );
					//use description from yoast
					$twitter_desc = get_post_meta( $post->ID, '_yoast_wpseo_twitter-description', true );
					$og_desc      = get_post_meta( $post->ID, '_yoast_wpseo_opengraph-description', true );
				}
				?>
				<meta name="twitter:title" content="<?php echo get_the_title(); ?>">
				<meta name="twitter:description" content="<?php echo $twitter_desc; ?>">
				<meta name="twitter:domain" content="<?php echo esc_url( site_url() ); ?>">
				<meta property="og:url" content="<?php the_permalink(); ?>" />
				<meta property="og:title" content="<?php echo get_the_title(); ?>" />
				<meta property="og:description" content="<?php echo $og_desc; ?>" />
				<?php
				if ( has_post_thumbnail() ) {
					$thumb_id        = get_post_thumbnail_id();
					$thumb_url_array = wp_get_attachment_image_src( $thumb_id, 'full', true );
					$thumb_url       = $thumb_url_array[0];
					?>
					<meta name="twitter:card" content="summary_large_image">
					<meta name="twitter:image:src" content="<?php echo $thumb_url; ?>">
					<meta property="og:image" content="<?php echo $thumb_url; ?>" />
					<meta itemprop="image" content="<?php echo $thumb_url; ?>">
				<?php
				}
			}
		}

		/**
		 * Get plugin directory.
		 * @return string
		 */
		public function plugin_dir() {
			if ( is_null( $this->plugin_dir ) ) {
				$this->plugin_dir = untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/';
			}
			return $this->plugin_dir;
		}

		/**
		 * Get plugin uri.
		 * @return string
		 */
		public function plugin_url() {
			if ( is_null( $this->plugin_url ) ) {
				$this->plugin_url = untrailingslashit( plugin_dir_url( __FILE__ ) ) . '/';
			}
			return $this->plugin_url;
		}
	}

	/**
	 * Main instance of WP_Quiz_Plugin.
	 *
	 * Returns the main instance of WP_Quiz_Plugin to prevent the need to use globals.
	 *
	 * @return WP_Quiz_Plugin
	 */

	function wp_quiz() {
		return WP_Quiz_Plugin::get_instance();
	}

endif;

add_action( 'plugins_loaded', 'wp_quiz', 10 );
register_activation_hook( __FILE__, array( 'WP_Quiz_Plugin', 'activate_plugin' ) );
