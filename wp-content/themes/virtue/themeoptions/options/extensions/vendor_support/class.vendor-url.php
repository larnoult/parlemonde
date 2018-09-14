<?php

    if ( ! defined( 'ABSPATH' ) ) {
        exit;
    }

    if ( ! class_exists( 'Redux_VendorURL' ) ) {
        class Redux_VendorURL {
            static public $url;
            static public $dir;

            public static function get_url( $handle ) {
                if ( $handle == 'select2-js' && file_exists( self::$dir . 'vendor/select2/select2' . Redux_Functions::isMin() . '.js' ) ) {
                    return get_template_directory_uri() . '/themeoptions/options/extensions/vendor_support/vendor/select2/select2.min.js';
                } elseif ( $handle == 'select2-css' && file_exists( self::$dir . 'vendor/select2/select2.css' )  ) {
                    return get_template_directory_uri() . '/themeoptions/options/extensions/vendor_support/vendor/select2/select2.css';
                }
            }
        }
    }