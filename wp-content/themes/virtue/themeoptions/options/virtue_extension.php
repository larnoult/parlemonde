<?php

if ( class_exists( 'Redux' ) ) {
    $opt_name = 'virtue';
    Redux::setExtensions( $opt_name, dirname( __FILE__ ) . '/extensions/' );
}

add_action( "redux/extension/customizer/control/includes","kt_info_customizer" );
function kt_info_customizer(){
    if ( ! class_exists( 'Redux_Customizer_Control_info' ) ) {
        class Redux_Customizer_Control_info extends Redux_Customizer_Control {
            public $type = "redux-info";
        }
    }
}
/* if(!function_exists('redux_register_custom_extension_loader')) :
    function redux_register_custom_extension_loader($ReduxFramework) {
        $path = dirname( __FILE__ ) . '/extensions/';
        $folders = scandir( $path, 1 );        
        foreach($folders as $folder) {
            if ($folder === '.' or $folder === '..' or !is_dir($path . $folder) ) {
                continue;   
            } 
            $extension_class = 'ReduxFramework_Extension_' . $folder;
            if( !class_exists( $extension_class ) ) {
                // In case you wanted override your override, hah.
                $class_file = $path . $folder . '/extension_' . $folder . '.php';
                $class_file = apply_filters( 'redux/extension/'.$ReduxFramework->args['opt_name'].'/'.$folder, $class_file );
                if( $class_file ) {
                    require_once( $class_file );
                    $extension = new $extension_class( $ReduxFramework );
                }
            }
        }
    }
    add_action("redux/extensions/virtue/before", 'redux_register_custom_extension_loader', 0);
endif;
*/

