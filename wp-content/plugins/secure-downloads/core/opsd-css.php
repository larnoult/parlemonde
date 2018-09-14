<?php /**
 * @version 1.0
 * @package 
 * @category Core
 * @author wpdevelop
 *
 * @web-site http://oplugins.com/
 * @email info@oplugins.com 
 * 
 * @modified 2013.10.16
 */

class OPSD_CSS extends OPSD_JS_CSS{

    public function define() {
        
        $this->setType('css');
        
        /*
        // Exmaples of usage Font Avesome: http://fontawesome.io/icons/
        
        $this->add( array(
                            'handle' => 'font-awesome',
                            'src' => OPSD_PLUGIN_URL . 'assets/libs/font-awesome-4.3.0/css/font-awesome.css' ,
                            'deps' => false,
                            'version' => '4.3.0',
                            'where_to_load' => array( 'admin' ),
                            'condition' => false    
                  ) );   
        
        // Exmaples of usage Font Avesome 3.2.1 (benefits of this version - support IE7): http://fontawesome.io/3.2.1/examples/ 
        $this->add( array(
                            'handle' => 'font-awesome',
                            'src' => OPSD_PLUGIN_URL . '/assets/libs/font-awesome/css/font-awesome.css' ,
                            'deps' => false,
                            'version' => '3.2.1',
                            'where_to_load' => array( 'admin' ),
                            'condition' => false    
                  ) );            
        $this->add( array(
                            'handle' => 'font-awesome-ie7',
                            'src' => OPSD_PLUGIN_URL . '/assets/libs/font-awesome/css/font-awesome-ie7.css' ,
                            'deps' => array('font-awesome'),
                            'version' => '3.2.1',
                            'where_to_load' => array( 'admin' ),
                            'condition' => 'IE 7'                               // CSS condition. Exmaple: <!--[if IE 7]>    
                  ) );  
        */
          
    }


    public function enqueue( $where_to_load ) {        
        
        wp_enqueue_style('wpdevelop-bts',       opsd_plugin_url( '/assets/libs/bootstrap/css/bootstrap.css' ),          array(), '3.3.5.1');
        wp_enqueue_style('wpdevelop-bts-theme', opsd_plugin_url( '/assets/libs/bootstrap/css/bootstrap-theme.css' ),    array(), '3.3.5.1');
                   
        if ( $where_to_load == 'admin' ) {                                                                                                      // Admin CSS files            

            wp_enqueue_style('opsd-chosen',                 opsd_plugin_url( '/assets/libs/chosen/chosen.css' ),        array(), OPSD_VERSION_NUM);
            wp_enqueue_style( 'opsd-admin-support',         opsd_plugin_url( '/core/any/css/admin-support.css' ),       array(), OPSD_VERSION_NUM);            
            wp_enqueue_style( 'opsd-admin-menu',            opsd_plugin_url( '/core/any/css/admin-menu.css' ),          array(), OPSD_VERSION_NUM);
            wp_enqueue_style( 'opsd-admin-toolbar',         opsd_plugin_url( '/core/any/css/admin-toolbar.css' ),       array(), OPSD_VERSION_NUM);
            wp_enqueue_style( 'opsd-settings-page',         opsd_plugin_url( '/core/any/css/settings-page.css' ),       array(), OPSD_VERSION_NUM);            
            wp_enqueue_style( 'opsd-admin-listing-table',   opsd_plugin_url( '/core/any/css/admin-listing-table.css' ), array(), OPSD_VERSION_NUM);            
            wp_enqueue_style( 'opsd-br-table',              opsd_plugin_url( '/core/any/css/admin-br-table.css' ),      array(), OPSD_VERSION_NUM);                        
            wp_enqueue_style( 'opsd-admin-modal-popups',    opsd_plugin_url( '/css/modal.css' ),                        array(), OPSD_VERSION_NUM);            
            wp_enqueue_style( 'opsd-admin-pages',           opsd_plugin_url( '/css/admin.css' ),                        array(), OPSD_VERSION_NUM);            
            wp_enqueue_style( 'opsd-css-print',             opsd_plugin_url( '/css/print.css' ),                        array(), OPSD_VERSION_NUM);
        }         
        if (  ( $where_to_load != 'admin' ) || ( opsd_is_new_opsd_page() )  ){                                                               // Client or Add New item page
            wp_enqueue_style( 'opsd-client-pages',          opsd_plugin_url( '/css/client.css' ),                       array(), OPSD_VERSION_NUM);            
        }        
        if (  ( $where_to_load != 'admin' ) || ( opsd_is_master_page() )  ){                                                       
            wp_enqueue_style( 'opsd-admin-popover',        opsd_plugin_url( '/css/popover.css' ),						 array(), OPSD_VERSION_NUM);                        
        }        
        wp_enqueue_style('opsd-calendar',   opsd_plugin_url( '/css/calendar.css' ),                                     array(), OPSD_VERSION_NUM);        
                                                                                                                                                // Calendar Skins
    
        do_action( 'opsd_enqueue_css_files', $where_to_load );        
    }


    public function remove_conflicts( $where_to_load ) {        
    
        if ( opsd_is_master_page() ) {            
            if (function_exists('wp_dequeue_style')) {
                /*
                wp_dequeue_style( 'cs-alert' );
                wp_dequeue_style( 'cs-framework' );
                wp_dequeue_style( 'cs-font-awesome' );
                wp_dequeue_style( 'icomoon' );           
                */            
                wp_dequeue_style( 'chosen'); 
                wp_dequeue_style( 'toolset-font-awesome-css' );                               // Remove this script sitepress-multilingual-cms/res/css/font-awesome.min.css?ver=3.1.6, which is load by the "sitepress-multilingual-cms"
                wp_dequeue_style( 'toolset-font-awesome' );                          //FixIn: 5.4.5.8
                
            } 
        }
    }
}