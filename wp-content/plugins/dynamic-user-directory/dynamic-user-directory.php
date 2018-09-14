<?php
/*** Plugin Name: Dynamic User Directory 
* Plugin URI: http://sgcustomwebsolutions.com 
* Description: Creates an alphabetically sorted user directory that will format and display specified user meta data such as name, address, and email. 
* Version: 1.4.4 
* Author: Sarah Giles 
* Author URI: http://sgcustomwebsolutions.com 
* License: GPL2 
*/

define('DYNAMIC_USER_DIRECTORY_DIR_PATH', plugin_dir_path(__FILE__));
define('DYNAMIC_USER_DIRECTORY_URL', plugin_dir_url(__FILE__));

add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'my_plugin_action_links' );

function my_plugin_action_links( $links ) {
  
   $links[] = '<a href="http://sgcustomwebsolutions.com/wordpress-plugin-development/" target="_blank">Add-ons</a>';
   return $links;
}

global $wpdb;

/*** Cimy User Extra Fields Constants *********************************************/

$dud_Cimy_Table_Name1 = $wpdb->prefix . 'cimy_uef_data';	
$dud_Cimy_Table_Name2 = $wpdb->prefix . 'cimy_uef_fields';
			
$dud_Cimy_Table_1 = $wpdb->get_results("SHOW TABLES LIKE '" . $dud_Cimy_Table_Name1 . "'");	
$dud_Cimy_Table_2 = $wpdb->get_results("SHOW TABLES LIKE '" . $dud_Cimy_Table_Name2 . "'");
	
if($dud_Cimy_Table_1 && $dud_Cimy_Table_2)
{	
		if(!defined("DUD_CIMY_DATA_TABLE"))		
			define('DUD_CIMY_DATA_TABLE', $dud_Cimy_Table_Name1);
		
		if(!defined("DUD_CIMY_FIELDS_TABLE"))		
			define('DUD_CIMY_FIELDS_TABLE', $dud_Cimy_Table_Name2);	
}

/*** BuddyPress Constants *********************************************************/

$dud_BP_Table_Name1 = $wpdb->prefix . 'bp_xprofile_data';	
$dud_BP_Table_Name2 = $wpdb->prefix . 'bp_xprofile_fields';
			
$dud_BP_Table_1  = $wpdb->get_results("SHOW TABLES LIKE '" . $dud_BP_Table_Name1 . "'");	
$dud_BP_Table_2  = $wpdb->get_results("SHOW TABLES LIKE '" . $dud_BP_Table_Name2 . "'");
	
if($dud_BP_Table_1 && $dud_BP_Table_2)
{	
		if(!defined("DUD_BP_PLUGIN_DATA_TABLE"))		
			define('DUD_BP_PLUGIN_DATA_TABLE', $dud_BP_Table_Name1);	
		
		if(!defined("DUD_BP_PLUGIN_FIELDS_TABLE"))		
			define('DUD_BP_PLUGIN_FIELDS_TABLE', $dud_BP_Table_Name2);	
}
		
function DynamicUserDirectoryLoad()
{		    
			if(is_admin()) //load admin files only in admin  
				require_once(DYNAMIC_USER_DIRECTORY_DIR_PATH . 'includes/admin.php');
				
			require_once(DYNAMIC_USER_DIRECTORY_DIR_PATH . 'includes/core.php'); 
			require_once(DYNAMIC_USER_DIRECTORY_DIR_PATH . 'includes/member_plugins_compatibility.php'); 
}

DynamicUserDirectoryLoad();