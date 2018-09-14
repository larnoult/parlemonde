<?php
/*
Plugin Name: Secure Downloads
Plugin URI: http://oplugins.com/plugins/secure-downloads
Description: Easy generate and send secure expiring links for file downloads
Author: wpdevelop, oplugins
Author URI: http://oplugins.com/
Text Domain: secure-downloads
Domain Path: /languages/
Version: 1.0
*/

/*  Copyright 2017  www.oplugins.com  (email: info@oplugins.com),

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>
*/
    
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) die('<h3>Direct access to this file do not allow!</h3>');

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// PRIMARY URL CONSTANTS                        //////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////    

// ..\home\siteurl\www\wp-content\plugins\plugin-name\opsd-item.php
if ( ! defined( 'OPSD_FILE' ) )             define( 'OPSD_FILE', __FILE__ ); 

// opsd-item.php
if ( ! defined('OPSD_PLUGIN_FILENAME' ) )   define('OPSD_PLUGIN_FILENAME', basename( __FILE__ ) );                     

// plugin-name    
if ( ! defined('OPSD_PLUGIN_DIRNAME' ) )    define('OPSD_PLUGIN_DIRNAME',  plugin_basename( dirname( __FILE__ ) )  );  

// ..\home\siteurl\www\wp-content\plugins\plugin-name
if ( ! defined('OPSD_PLUGIN_DIR' ) )        define('OPSD_PLUGIN_DIR', untrailingslashit( plugin_dir_path( OPSD_FILE ) )  );

// http: //website.com/wp-content/plugins/plugin-name
if ( ! defined('OPSD_PLUGIN_URL' ) )        define('OPSD_PLUGIN_URL', untrailingslashit( plugins_url( '', OPSD_FILE ) )  );     

require_once OPSD_PLUGIN_DIR . '/core/opsd.php'; 

/** 
 * 1) Rename all  files in plugin directory starting from opsd -> prefix
 * 
 * 2) Replace Instruction:
 * 
, 'secure-downloads') ->  , 'pluginnamelocale')
  _opsd_     ->  _bk_ (...)       in get_opsd_option ....
   OPSD      ->  PREFIX
   opsd      ->  prefix
   securedownloads -> booking ???   
   Secure Downloads -> NEW_PLUGIN_NAME
 * 
 */
