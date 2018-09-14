<?php
// Autoloader
function cs3_autoloader( $class_name ) {
	if ( 0 === strpos( $class_name, 'CycloneSlider' ) ) {

		$class_name = str_replace( '\\', '/', $class_name ); // for 5.3 namespaces, replace \ with / to work with linux.
		$src = dirname( __FILE__ ) . '/';
		$class  = str_replace( '_', '/', $class_name ) . '.php';

		require_once $src . $class;
	}
}

spl_autoload_register( 'cs3_autoloader' );