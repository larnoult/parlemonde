<?php
/*
  Plugin Name: BP Limit Activity Length
  Plugin URI: http://trenvo.com
  Description: Limit the maximum length of activities like Twitter
  Version: 0.4
  Author: Mike Martel
  Author URI: http://trenvo.com
 */

// Exit if accessed directly
if (!defined('ABSPATH'))
    exit;

/**
 * Version number
 *
 * @since 0.1
 */
define('BP_LAL_VERSION', '0.4');

/**
 * PATHs and URLs
 *
 * @since 0.1
 */
define('BP_LAL_DIR', plugin_dir_path(__FILE__));
define('BP_LAL_URL', plugin_dir_url(__FILE__));
define('BP_LAL_INC_URL', BP_LAL_URL . '_inc/');

if (!class_exists('BP_LimitActivityLength')) :

    class BP_LimitActivityLength    {

        private $limit;
        private $type;

        /**
         * Creates an instance of the BP_LimitActivityLength class
         *
         * @return BP_LimitActivityLength object
         * @since 0.1
         * @static
        */
        public static function &init() {
            static $instance = false;

            if (!$instance) {
                load_plugin_textdomain('bp-lal', false, basename(BP_LAL_DIR) . '/languages/');
                $instance = new BP_LimitActivityLength;
            }

            return $instance;
        }

        /**
         * Constructor
         *
         * @since 0.1
         */
        public function __construct() {
            $options = bp_get_option('bp-limit-activity-length', array(
                'limit' => 140,
                'type'  => 'char'
            ));
            $this->limit = $options['limit'];
            $this->type  = $options['type'];

            add_action( 'init', array ( &$this, '_maybe_load_scripts' ) );
            add_filter( "bp_activity_type_before_save", array ( &$this, '_maybe_verify_activity_length' ) );

            // Admin
            add_action( 'bp_register_admin_settings', array ( &$this, 'register_settings' ) );
        }

        public function _maybe_load_scripts() {
            global $bp;

            if ( // Load the scripts on Activity pages
                (defined('BP_ACTIVITY_SLUG') && bp_is_activity_component())
                ||
                // Load the scripts when Activity page is the Home page
                (defined('BP_ACTIVITY_SLUG') && 'page' == get_option('show_on_front') && is_front_page() && BP_ACTIVITY_SLUG == get_option('page_on_front'))
                ||
                // Load the script on Group home page
                (defined('BP_GROUPS_SLUG') && bp_is_groups_component() && 'home' == $bp->current_action)
                ) {
                add_action( "wp_enqueue_scripts", array (&$this, 'enqueue_scripts') );
                add_action( "wp_print_scripts", array ( &$this, 'print_style' ) );
            }
        }

        public function register_settings() {
            add_settings_field( 'bp-limit-activity-length', __( 'Activity Length', 'bp-lal' ), array ( &$this, 'display_limit_setting'), 'buddypress', 'bp_activity' );
			register_setting( 'buddypress', 'bp-limit-activity-length', array ( &$this, 'sanitize_limit' ) );
        }

        public function display_limit_setting() {
            ?>
                <input id="bp-limit-activity-length-limit" size=4 name="bp-limit-activity-length[limit]" type="text" value="<?php echo $this->limit ?>" />
                <select name="bp-limit-activity-length[type]">
                    <option value='char' <?php selected('char',$this->type) ?>><?php _e("Characters",'bp-lal') ?></option>
                    <option value='word' <?php selected('word',$this->type) ?>><?php _e("Words",'bp-lal') ?></option>
                </select>
                <label for="bp-limit-activity-length"><?php _e( 'Allowed length for activity updates. Only applies to new updates.', 'bp-lal' ); ?></label></p>
            <?php
        }

        public function sanitize_limit( $setting ) {
            if ( ! is_array ( $setting ) ) $setting = array();
            if ( ! isset ( $setting['limit'] ) || ! is_numeric ( $setting['limit'] ) )
                $setting['limit'] = $this->limit;
            if ( ! isset ( $setting['type'] ) || ! in_array ( $setting['type'], array ( 'char', 'word' ) ) )
                $setting['type'] = $this->type;

            return $setting;
        }


        public function enqueue_scripts() {
            wp_enqueue_script( 'bplal', BP_LAL_INC_URL . 'bp-lal.js', array('jquery'), BP_LAL_VERSION, true );
            wp_localize_script('bplal', 'BPLal', array(
                'limit'     => $this->limit,
                'type'      => $this->type
            ));
        }

        /**
         * Because it's just one declaration, let's put it in the header
         */
        public function print_style() {
            ?>
            <style>div.activity-limit{margin:12px 10px 0 0;line-height:28px;} #whats-new-form div.activity-limit {float:right;} .ac-form div.activity-limit {display:inline;} </style>
            <?php
        }

        /**
         * Make sure to only enforce activity length for activity updates and comments
         *
         * @param string $type
         * @return string $type
         * @since 0.4
         */
        public function _maybe_verify_activity_length( $type ) {
            $whitelist = array( 'activity_update', 'activity_comment' );
            $whitelist = apply_filters( 'bp_lal_activity_types', $whitelist );

            if ( in_array( $type, $whitelist ) ) {
                add_filter( "bp_activity_content_before_save", array ( &$this, 'verify_activity_length' ) );
            }

            return $type;
        }

        /**
         * @since 0.2
        **/
        public function verify_activity_length ( $content ) {
            remove_filter( "bp_activity_content_before_save", array ( &$this, 'verify_activity_length' ) );

            $stripped_content = strip_tags( $content );

            if ( 'word' == $this->type ) {
                $words = str_word_count( $stripped_content, 2 );
                if ( count ( $words ) > $this->limit ) {
                    $word_positions = array_keys( $words );
                    $last_word_position = $word_positions[$this->limit];
                    $content = mb_substr( $stripped_content, 0, $last_word_position );
                }
            } else {
                $chars = mb_strlen( $stripped_content );
                $diff = $this->limit - $chars;

                if ( $diff < 0 ) {
                    $content = mb_substr( $stripped_content, 0, $this->limit );
                }
            }
            return $content;
        }

            }

    add_action('bp_include', array('BP_LimitActivityLength', 'init'));
endif;