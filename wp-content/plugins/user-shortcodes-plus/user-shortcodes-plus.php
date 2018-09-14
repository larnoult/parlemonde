<?php

/*
 * Plugin Name: User Shortcodes Plus
 * Plugin URI: http://kylebjohnson.me/plugins
 * Description: Add simple user shortcodes to WordPress for displaying information, including <strong>custom meta</strong> and <strong>avatars</strong>, for any user.
 * Version: 2.0.1
 * Author: Kyle B. Johnson
 * Author URI: http://kylebjohnson.me
 * Text Domain: user-shortcodes-plus
 *
 * Copyright 2016 Kyle B. Johnson.
 */

final class KBJ_UserShortcodesPlus
{
    const VERSION = '2.0.1';

    const PREFIX  = 'KBJ_UserShortcodesPlus';

    private static $instance;

    public static $dir = '';

    public static $url = '';

    public function __construct()
    {
        add_action( 'init', array( $this, 'init' ) );
        add_action( 'admin_init', array( $this, 'admin_init' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
    }

    public function init()
    {
        new KBJ_UserShortcodesPlus_Shortcodes_User();
        new KBJ_UserShortcodesPlus_Shortcodes_UserMeta();
        new KBJ_UserShortcodesPlus_Shortcodes_UserAvatar();
    }

    public function admin_init()
    {
        new KBJ_UserShortcodesPlus_Admin_TinyMCE_AddShortcodeButton();
    }

    public function admin_enqueue_scripts()
    {
        wp_register_style( 'kbj_user_shortcodes_plus_admin_css', self::url( 'assets/css/admin.css' ), FALSE, self::VERSION );
        wp_enqueue_style( 'kbj_user_shortcodes_plus_admin_css' );

        wp_register_script( 'kbj_user_shortcodes_plus_admin_js', self::url( 'assets/js/admin.js' ), array( 'jquery' ), self::VERSION );
        wp_enqueue_script( 'kbj_user_shortcodes_plus_admin_js' );
    }

    public static function instance()
    {
        if ( ! isset(self::$instance) && ! ( self::$instance instanceof KBJ_UserShortcodesPlus ) ) {
            self::$instance = new KBJ_UserShortcodesPlus();
            self::$dir = plugin_dir_path(__FILE__);
            self::$url = plugin_dir_url(__FILE__);
            spl_autoload_register( array( self::$instance, 'autoloader' ) );
        }
        return self::$instance;
    }

    public static function dir( $path = '' )
    {
        return self::$dir . $path;
    }

    public static function url( $url = '' )
    {
        return self::$url . $url;
    }

    public static function autoloader( $class_name )
    {
        if( class_exists( $class_name ) ) return;

        if( false === strpos( $class_name, self::PREFIX ) ) return;

        $class_name = str_replace( self::PREFIX, '', $class_name );
        $classes_dir = realpath(plugin_dir_path(__FILE__)) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR;
        $class_file = str_replace('_', DIRECTORY_SEPARATOR, $class_name) . '.php';

        if (file_exists($classes_dir . $class_file)) {
            require_once $classes_dir . $class_file;
        }
    }

    public static function config( $file_name )
    {
        return include self::dir( 'includes/Config/' . $file_name . '.php' );
    }

    public static function template( $file_name = '', array $data = array() )
    {
        if( ! $file_name ) return '';

        extract( $data );

        ob_start();
        include self::dir( 'includes/Templates/' . $file_name );
        return ob_get_clean();
    }
}

function KBJ_UserShortcodesPlus()
{
    return KBJ_UserShortcodesPlus::instance();
}

KBJ_UserShortcodesPlus();
