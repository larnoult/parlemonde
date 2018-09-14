<?php 

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Virtue_Lazy_Load {
	private static $lazyload = null;

	public static function is_lazy() {
		if ( is_null( self::$lazyload ) ) {
			$lazy = false;
			if( ( function_exists( 'get_rocket_option' ) && get_rocket_option( 'lazyload') ) || ( function_exists( 'rocket_lazyload_get_option' ) && rocket_lazyload_get_option( 'images' ) ) ) {
				$lazy = true;
			}
			self::$lazyload = apply_filters( 'kad_lazy_load', $lazy );
		}
		return self::$lazyload;
	}
}
