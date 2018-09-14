<?php

/*** Register Menu Item *************************************************/

function DynamicUserDirectoryAdmin(){
	
add_submenu_page(
 'options-general.php',
 'Dynamic User Directory Settings',
 'Dynamic User Directory',
 'manage_options',
 'user_directory',
 'DynamicUserDirectoryAdminSettings'
 );
}
add_action('admin_menu', 'DynamicUserDirectoryAdmin'); //menu setup

/**** Display Page Content *********************************************/

function DynamicUserDirectoryAdminSettings() {

global $submenu;

// access page settings 
$page_data = array();
foreach($submenu['options-general.php'] as $i => $menu_item) {
 	if($submenu['options-general.php'][$i][2] == 'user_directory')
 		$page_data = $submenu['options-general.php'][$i];
}

/*** load scripts ***/    
wp_enqueue_style( 'wp-color-picker' ); 
wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css'); 
wp_enqueue_script( 'dud_custom_js', DYNAMIC_USER_DIRECTORY_URL . '/js/jquery.user-directory.js', array( 'jquery', 'wp-color-picker' ), '', true  );
wp_enqueue_script( 'jquery-ui-sortable');
wp_enqueue_style( 'select2_css', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css' );
wp_register_script( 'select2_js', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js', array('jquery'), '4.0.3', true );
wp_enqueue_script('select2_js');

$dud_options = get_option( 'dud_plugin_settings' );
$dud_option_name = 'dud_plugin_settings'; 
$instance_name = "";

if(! $dud_options ) {
		
	$dud_options = array(
		'user_directory_sort' => 'last_name',
		'ud_format_name' => 'fl',
		'ud_directory_type' => 'alpha-links',
		'ud_letter_divider' => 'nld',
		'ud_letter_divider_font_color' => '#FFFFFF',
		'ud_letter_divider_fill_color' => '#D3D3D3',
		'ud_show_srch' => '',
		'user_directory_show_avatars' => '1',
		'user_directory_avatar_style' => '',		
		'user_directory_border' => 'dividing_border',
		'user_directory_border_thickness' => '',
		'user_directory_border_color' => '#D3D3D3',
		'user_directory_border_length' => '60%',
		'user_directory_border_style' => '',
		'user_directory_letter_fs' => '15',
		'ud_alpha_link_spacer' => '12',
		'user_directory_listing_fs' => '15',
		'user_directory_listing_spacing' => '15',
		'ud_hide_roles' => null,
		'ud_users_exclude_include' => null,
		'ud_exclude_include_radio' => 'exclude',
		'user_directory_email' => '1',
		'user_directory_website' => '1',
		'user_directory_num_meta_flds' => '5',
		'user_directory_meta_field_1' => '',
		'user_directory_meta_label_1' => '',
		'user_directory_meta_field_2' => '',
		'user_directory_meta_label_2' => '',
		'user_directory_meta_field_3' => '',
		'user_directory_meta_label_3' => '',
		'user_directory_meta_field_4' => '',
		'user_directory_meta_label_4' => '',
		'user_directory_meta_field_5' => '',
		'user_directory_meta_label_5' => '',
		'user_directory_meta_field_6' => '',
		'user_directory_meta_label_6' => '',
		'user_directory_meta_field_7' => '',
		'user_directory_meta_label_7' => '',
		'user_directory_meta_field_8' => '',
		'user_directory_meta_label_8' => '',
		'user_directory_meta_field_9' => '',
		'user_directory_meta_label_9' => '',
		'user_directory_meta_label_10' => '',
		'user_directory_meta_field_10' => '',
		'user_directory_meta_link_1' => '',
		'user_directory_meta_link_2' => '',
		'user_directory_meta_link_3' => '',
		'user_directory_meta_link_4' => '',
		'user_directory_meta_link_5' => '',
		'user_directory_meta_link_6' => '',
		'user_directory_meta_link_7' => '',
		'user_directory_meta_link_8' => '',
		'user_directory_meta_link_9' => '',
		'user_directory_meta_link_10' => '',
		'user_directory_address' => '1',
		'user_directory_addr_1' => '',
		'user_directory_addr_2' => '',
		'user_directory_city' => '',
		'user_directory_state' => '',
		'user_directory_zip' => '',
		'user_directory_country' => '',
		'user_directory_num_meta_srch_flds' => '5',
		'user_directory_meta_srch_field_1' => '',
		'user_directory_meta_srch_label_1' => '',
		'user_directory_meta_srch_field_2' => '',
		'user_directory_meta_srch_label_2' => '',
		'user_directory_meta_srch_field_3' => '',
		'user_directory_meta_srch_label_3' => '',
		'user_directory_meta_srch_field_4' => '',
		'user_directory_meta_srch_label_4' => '',
		'user_directory_meta_srch_field_5' => '',
		'user_directory_meta_srch_label_5' => '',
		'user_directory_meta_srch_field_6' => '',
		'user_directory_meta_srch_label_6' => '',
		'user_directory_meta_srch_field_7' => '',
		'user_directory_meta_srch_label_7' => '',
		'user_directory_meta_srch_field_8' => '',
		'user_directory_meta_srch_label_8' => '',
		'user_directory_meta_srch_field_9' => '',
		'user_directory_meta_srch_label_9' => '',
		'user_directory_meta_srch_field_10' => '',
		'user_directory_meta_srch_label_10' => '',
		'user_directory_meta_srch_field_11' => '',
		'user_directory_meta_srch_label_11' => '',
		'user_directory_meta_srch_field_12' => '',
		'user_directory_meta_srch_label_12' => '',
		'user_directory_meta_srch_field_13' => '',
		'user_directory_meta_srch_label_13' => '',
		'user_directory_meta_srch_field_14' => '',
		'user_directory_meta_srch_label_14' => '',
		'user_directory_meta_srch_field_15' => '',
		'user_directory_meta_srch_label_15' => '',
		'ud_show_last_name_srch_fld' => '',
		'user_directory_sort_order' => '',
		'ud_debug_mode' => 'off',	
		'ud_author_page' => '',
		'ud_show_author_link' => '',
		'ud_auth_or_bp' => '',
		'ud_clear_search' => '',
		'ud_show_srch_results' => 'alpha-links',
		'ud_srch_icon_color' => 'dimgray',
		'dud_instance_name' => 'original',
		'ud_display_listings' => '',
		'ud_table_width' => '100%',
		'ud_table_cell_padding_top' => '8',
		'ud_table_cell_padding_bottom' => '8',
		'ud_table_cell_padding_left' => '8',
		'ud_table_cell_padding_right' => '8',
		'ud_table_stripe_color' => '#F0F0F0',
		'ud_show_table_stripes' => '',
		'ud_show_heading_labels' => '',
		'ud_heading_fs' => '',
		'user_directory_avatar_size' => '96',
		'user_directory_avatar_padding' => '120',
		'ud_col_width_name' => '', 
		'ud_col_width_email' => '', 
		'ud_col_width_website' => '', 
		'ud_col_width_address' => '', 
		'ud_col_width_social' => '', 
		'ud_col_width_1' => '',
		'ud_col_width_2' => '',
		'ud_col_width_3' => '',
		'ud_col_width_4' => '',
		'ud_col_width_5' => '',
		'ud_col_width_6' => '',
		'ud_col_width_7' => '',
		'ud_col_width_8' => '',
		'ud_col_width_9' => '',
		'ud_col_width_10' => '',
		'ud_divider_border_thickness' => '',
		'ud_divider_border_color' => '',
		'ud_divider_border_length' => '',
		'ud_divider_border_style' => '',
		'ud_divider_font_size' => '',
		'ud_hide_before_srch' => '',
		'ud_facebook' => '',
		'ud_twitter' => '',
		'ud_linkedin' => '',
		'ud_google' => '',
		'ud_instagram' => '',
		'ud_pinterest' => '',
		'ud_social' => '1',
		'ud_icon_style' => '1',
		'ud_icon_size' => '22',
		'ud_icon_color' => '',
		'ud_col_label_name' => '',
		'ud_col_label_email' => '',
		'ud_col_label_website' => '',
		'ud_col_label_address' => '',
		'ud_col_label_social' => '',
		'ud_col_meta_label_1' => '',
		'ud_col_meta_label_2' => '',
		'ud_col_meta_label_3' => '',
		'ud_col_meta_label_4' => '',
		'ud_col_meta_label_5' => '',
		'ud_col_meta_label_6' => '',
		'ud_col_meta_label_7' => '',
		'ud_col_meta_label_8' => '',
		'ud_col_meta_label_9' => '',
		'ud_col_meta_label_10' => '',
		'ud_break_email' => '',
		'ud_horizontal_responsive_601_767' => 'fixed',
		'ud_horizontal_responsive_768_1024' => 'fixed',
		'dud_fld_format_1' => '',
		'dud_fld_format_2' => '',
		'dud_fld_format_3' => '',
		'dud_fld_format_4' => '',
		'dud_fld_format_5' => '',
		'dud_fld_format_6' => '',
		'dud_fld_format_7' => '',
		'dud_fld_format_8' => '',
		'dud_fld_format_9' => '',
		'dud_fld_format_10' => '',
		'ud_sort_fld_key' => '',
		'ud_sort_fld_type' => '',
		'ud_users_per_page' => '',
		'ud_show_pagination_top_bottom' => '',
		'ud_pagination_font_size' => '',
		'ud_pagination_link_color' => '',
		'ud_pagination_link_clicked_color' => '',
		'ud_alpha_link_color' => '',
		'ud_alpha_link_clicked_color' => '',
		'ud_show_pagination_above_below' => '',
		'ud_pagination_padding_top' => '',
		'ud_pagination_padding_bottom' => ''
	);

	// if old options exist, update to new system
	foreach( $dud_options as $key => $value ) {
		if( $existing = get_option( $key ) ) {
			$dud_options[$key] = $existing;
			delete_option( $key );
		}
	}
	
	add_option('dud_plugin_settings', $dud_options );
}

$dud_plugin_list = get_option('active_plugins');

if ( in_array( 'dynamic-user-directory-multiple-dirs/dynamic-user-directory-multiple-dirs.php' , $dud_plugin_list )) 
{
	$instance_name = "Original";
	$dud_option_name = "dud_plugin_settings";
	$dud_multi_instances_err = "";

	if(isset($_POST['dud_new_instance_name']))
	{
		$dud_total_instances = 0;
		$new_instance_name = $_POST['dud_new_instance_name'];
		
		if(!$new_instance_name)
			$dud_multi_instances_err = 'You must give the new directory instance a name!'; 
		else if(strlen($new_instance_name) > 20)
		{
			$dud_multi_instances_err = 'The directory instance name cannot be over 20 characters!'; 
		}
		else
		{
			$new_instance_name = sanitize_text_field($new_instance_name);
					
			if(strtoupper($new_instance_name) === "ORIGINAL")
			{
				$dud_multi_instances_err = 'The directory instance name "' . $new_instance_name . '" is reserved for your original settings. Please choose a different one.';
			}
			else
			{
				for($inc=0; $inc <= 4; $inc++) 
				{		  
					if( $dud_tmp_options = get_option( 'dud_plugin_settings_' . ($inc+1) ) )
					{
						if($dud_tmp_options['dud_instance_name'] && (strtoupper ($dud_tmp_options['dud_instance_name']) === strtoupper ($new_instance_name)) )  
						{
								$dud_multi_instances_err = 'The directory instance name "' . $new_instance_name . '" already exists. Please choose a different one.'; 
								break;
						} 
						
						if($dud_tmp_options['dud_instance_name'])
							$dud_total_instances++;
						else
						{
							unset($dud_options);
						    $dud_options = get_option( 'dud_plugin_settings' );
							$dud_options['dud_instance_name'] = $new_instance_name;
							update_option('dud_plugin_settings_' . ($inc+1), $dud_options );
							$dud_option_name = 'dud_plugin_settings_' . ($inc+1);
							$instance_name = $new_instance_name;
							
							break;
						}	
					}
					else
					{
						unset($dud_options);
						$dud_options = get_option( 'dud_plugin_settings' );
						$dud_options['dud_instance_name'] = $new_instance_name;
						add_option('dud_plugin_settings_' . ($inc+1), $dud_options );
						$dud_option_name = 'dud_plugin_settings_' . ($inc+1);
						$instance_name = $new_instance_name;
				
						break;
					}	
				}
				
				if($dud_total_instances >= 5)
					$dud_multi_instances_err = 'All available instances are taken! You must delete one before adding any more.'; 
			}
		}
	}
	else if(isset($_POST['load']) && isset($_POST['dud_load_dir_instance'])) 
	{
		$load_instance_name = $_POST['dud_load_dir_instance'];
		
		if(strtoupper($load_instance_name) === "ORIGINAL")
		{
			$instance_name = "Original";
			$dud_option_name = 'dud_plugin_settings';
		}
		else
		{
			$found_instance = false;
			
			for($inc=0; $inc <= 4; $inc++) 
			{		  
				if( $dud_tmp_options = get_option( 'dud_plugin_settings_' . ($inc+1) ) )
				{
					if($load_instance_name === $dud_tmp_options['dud_instance_name'])
					{
						unset($dud_options);
						$dud_options = $dud_tmp_options;
						$dud_option_name = 'dud_plugin_settings_' . ($inc+1);
						$instance_name = $load_instance_name;
						$found_instance = true;
						break;
					}	
				}	
			}
			
			if(!$found_instance)
				$dud_multi_instances_err = "Could not load instance " . $load_instance_name . " because it could not be found!"; 
		}
	}
	//else if(isset($_POST['delete']) && $_POST['delete'] === 'Delete')
	else if(isset($_POST['dud_load_dir_instance']))
	{
		$load_instance_name = $_POST['dud_load_dir_instance'];
		$deleted_instance = false;
		
		if(strtoupper($load_instance_name) === "ORIGINAL")
		{
			$dud_multi_instances_err = "The original settings cannot be deleted!"; 
		}
		else
		{
			for($inc=0; $inc <= 4; $inc++) 
			{		  
				if( $dud_tmp_options = get_option( 'dud_plugin_settings_' . ($inc+1) ) )
				{
					if($load_instance_name === $dud_tmp_options['dud_instance_name'])
					{
						delete_option('dud_plugin_settings_' . ($inc+1));
						$deleted_instance = true;
						$dud_multi_instances_err = 'Directory instance "' . $load_instance_name . '" has been deleted.'; 
						break;
					}	
				}
			}
			
			if(!$deleted_instance)
				$dud_multi_instances_err = 'Could not delete instance ' . $load_instance_name . ' because it could not be found!'; 
		}
	}
	else if($updated_settings = get_option('dud_updated_settings'))
	{
		//echo "DUD UPDATED SETTINGS: " . $updated_settings . "<BR>";
		unset($dud_options);
		$dud_option_name = $updated_settings;
		$dud_options = get_option( $updated_settings );
		$instance_name = !empty($dud_options['dud_instance_name']) ? $dud_options['dud_instance_name'] : "Original";
		//if(!$instance_name) $instance_name = "Original"; 
	}
	
	delete_option('dud_updated_settings');
}

//var_dump($_POST);	

/*** display settings screen ***/ 
?>
<div class="wrap">
<h2><?php echo $page_data[3];?></h2>

<?php 

if ( in_array( 'dynamic-user-directory-multiple-dirs/dynamic-user-directory-multiple-dirs.php' , $dud_plugin_list )) 
{ do_action(dud_multiple_directories_settings($dud_multi_instances_err, $instance_name)); } 
else { ?> <BR> <?php } ?>

<form id="user_directory_options" action="options.php" method="post" onSubmit="return selectAll()">

<?php 

settings_fields('user_directory_options');
do_settings_sections('user_directory_options'); 
      
if (!wp_script_is( 'user-directory-style', 'enqueued' )) {
	wp_register_style('user-directory-style',  DYNAMIC_USER_DIRECTORY_URL . '/css/user-directory-admin-min.css', false, 0.1);	
	//wp_register_style('user-directory-style',  DYNAMIC_USER_DIRECTORY_URL . '/css/user-directory-admin.css', false, 0.1);	
	wp_enqueue_style( 'user-directory-style' );
}
		
?>  
<div class="dud-settings-section-header">&nbsp; Main Directory Settings</div>
<div class="dud-settings-section">
<table class="form-table">

  <?php if ( !in_array( 'dynamic-user-directory-multiple-dirs/dynamic-user-directory-multiple-dirs.php' , $dud_plugin_list )) 
        { ?>
			 <tr>
				<td><b>Shortcode</b></td>
				<td><input class="dd-menu-no-chk-box-width" type="text" id="plugin_shortcode" name="plugin_shortcode" value="[DynamicUserDirectory]" size="32" readonly/></td>
				<td>Copy and paste this shortcode onto the page where the directory should be displayed. The <a href="http://sgcustomwebsolutions.com/wordpress-plugin-development/" target="_blank">Multiple Directories Add-on</a> will let you create and display up to five directories, each with its own settings.</td>
				<td></td>
			 </tr> 
  <?php }
        else 
        {?>	
	          <tr>
				<td><b>Loaded Instance</b></td>
				<td style="font-weight:bold;color:#008888;letter-spacing:1px;font-size:15px;"><?php echo $instance_name;?></td>
				<td></td>
				<td><input type="hidden" id="dud_instance_name" name="<?php echo $dud_option_name;?>[dud_instance_name]" value="<?php echo $instance_name;?>"></td>
			 </tr> 
  <?php } ?>     
      <tr>
        <td><b>Sort Field</b>
        </td>
        <td><select class="dd-menu-no-chk-box-width" name="<?php echo $dud_option_name;?>[user_directory_sort]" id="user_directory_sort">
            <OPTION value="display_name">Display Name</OPTION>             
            <OPTION value="last_name" <?php echo ($dud_options['user_directory_sort'] == "last_name") ? "SELECTED" : ""; ?>>Last Name</OPTION> 
            </select> 
        </td>
        <td>This field will always be shown first on each listing. You may sort by Last Name or Display Name. If Last Name is selected, it will sort by last name but still display the full name.</td>
        <td><input type="hidden" id="dynamic_ud_cimy_plugin" name="dynamic_ud_cimy_plugin" value="<?php echo $dynamic_ud_cimy_installed; ?>"></td>
     </tr>  
     <tr>
        <td><b>Directory Type</b></td>
        <td>
        	<select class="dd-menu-no-chk-box-width" name="<?php echo $dud_option_name;?>[ud_directory_type]" id="ud_directory_type">
            		<OPTION value="alpha-links">Alphabet Letter Links</OPTION> 
            		<OPTION value="all-users" <?php echo ($dud_options['ud_directory_type'] == "all-users") 
            			? "SELECTED" : ""; ?>>Single Page Directory</OPTION>             
            		
            	</select> 
        </td>
        <td>"Alphabet Letter Links" shows only the users for the selected letter. "Single Page Directory" shows all users on one page. Choose "Alphabet Letter Links" if page load time is an issue.</td>
        <td></td>
     </tr>	 
     <tr>
        <td><b>Directory Search</b></td>
        <td><input name="<?php echo $dud_option_name;?>[ud_show_srch]" id="ud_show_srch" type="checkbox" 
           value="1" <?php if(!empty($dud_options['ud_show_srch'])) { checked( '1', $dud_options['ud_show_srch'] ); } ?> />&nbsp;&nbsp;
           <select class="dd-menu-chk-box-width" name="<?php echo $dud_option_name;?>[ud_srch_style]" id="ud_srch_style">
            		<OPTION value="default">Default Background</OPTION>             
            		<OPTION value="transparent" <?php echo ($dud_options['ud_srch_style'] == "transparent") 
            			? "SELECTED" : ""; ?>>Transparent Bkg</OPTION>           		
           </select>
        </td>
        <td>Show a search box at the top of your directory to search by last name or display name depending on the Sort Field. The <a href="http://sgcustomwebsolutions.com/wordpress-plugin-development/" target="_blank">Meta Fields Search Add-on</a> lets the user search by ANY user meta field(s) you specify. The <a href="http://sgcustomwebsolutions.com/wordpress-plugin-development/" target="_blank">Hide Search Results Add-on</a> hides directory listings until a search has been run.</td>
        <td></td>
     </tr>
	 <?php if ( in_array( 'dynamic-user-directory-hide-dir-before-srch/dynamic-user-directory.php' , $dud_plugin_list )) 
     { ?>
			<tr>
			<td style="line-height:45px;"><b>Hide Dir Before Search</b></td>
			<td><input name="<?php echo $dud_option_name;?>[ud_hide_before_srch]" id="ud_show_srch" type="checkbox" 
			   value="1" <?php if(!empty($dud_options['ud_hide_before_srch'])) { checked( '1', $dud_options['ud_hide_before_srch'] ); } ?> />
			</td>
			<td>Show only the search box and hide the directory listings until a search has been run.<BR></td>
			<td></td>
		 </tr>
 
     <?php } ?>
	 <tr>
	    <td id="bot" colspan="2"><b>Hide Users With These Roles</b><div id='lb_hide_roles'><?php echo !empty($dud_options['ud_hide_roles']) ? dynamic_ud_roles_listbox($dud_options['ud_hide_roles'], $dud_option_name) : dynamic_ud_roles_listbox("", $dud_option_name); ?></div></td>
        <td style="font-size:13.5px;font-style:italic; line-height: 21px;padding-left:3%">Select any user roles that should NOT appear in the directory. Hold down the ctrl key while clicking on each role to select or deselect. If nothing is selected all users will be shown.</td>
        <td></td>
     </tr>
	  <tr>  
        <td colspan="2"><div id='lb_inc_exc'><b>Exclude or Include These Users </b>
        	<br>
        	<?php echo !empty($dud_options['ud_users_exclude_include']) ? dynamic_ud_users_listbox($dud_options['ud_users_exclude_include'], $dud_option_name) : dynamic_ud_users_listbox("", $dud_option_name); ?></div><br><br>
        	<input type="radio" name="<?php echo $dud_option_name;?>[ud_exclude_include_radio]" 
        		value="exclude" <?php if(!empty($dud_options['ud_exclude_include_radio'])) { checked( 'exclude', $dud_options['ud_exclude_include_radio'] ); } else { checked( 'exclude', 'exclude' );}?> /><b>Exclude</b>&nbsp;         	
        			<input type="radio" name="<?php echo $dud_option_name;?>[ud_exclude_include_radio]" value="include" 
        				<?php if(!empty($dud_options['ud_exclude_include_radio'])) { checked( 'include', $dud_options['ud_exclude_include_radio'] ); } ?> /><b>Include</b></td>
        <td style="font-size:13.5px;font-style:italic; line-height: 21px;padding-left:3%">"Include" creates a directory in which ONLY the selected users are shown. "Exclude" hides the selected users. If no users are selected this setting will not be applied. Note: Selected users will be included or excluded even if their user role was selected for hiding.</td>
        <td></td>
     </tr>
	  <tr>
        <td><b>Debug Mode</b></td>
        <td>
        	<input type="radio" name="<?php echo $dud_option_name;?>[ud_debug_mode]" 
        		value="off" <?php if(!empty($dud_options['ud_debug_mode'])) { checked( 'off', $dud_options['ud_debug_mode'] ); } else {checked( 'off', 'off' );} ?> /><b>Off</b>&nbsp;         	
        	<input type="radio" name="<?php echo $dud_option_name;?>[ud_debug_mode]" 
        		value="on" <?php if(!empty($dud_options['ud_debug_mode'])) { checked( 'on', $dud_options['ud_debug_mode'] ); } ?> /><b>On</b></td>
        <td>When debug mode is "on," a set of debug statements will be shown for admins *ONLY* at the top of the User Directory. Leave debug mode "off" unless instructed to turn on.</td>
        <td></td>
     </tr> 	
</table>
<br/>
</div><br/><br/>

<div class="dud-settings-section-header">&nbsp; Listing Display Settings</div>
<div class="dud-settings-section">
	<table class="form-table">
	
	<?php if ( in_array( 'dynamic-user-directory-horizontal-layout/dud_horizontal_layout.php' , $dud_plugin_list ) ) 
    { ?>
          <tr>
			<td><b>Display Listings</b></td>
			<td><select class="dd-menu-no-chk-box-width" name="<?php echo $dud_option_name;?>[ud_display_listings]" id="ud_display_listings">
						<OPTION value="vertically">Vertically</OPTION>             
						<OPTION value="horizontally" <?php echo (!empty($dud_options['ud_display_listings']) && $dud_options['ud_display_listings'] == "horizontally") ? "SELECTED" : ""; ?>>Horizontally</OPTION> 
			   </select>
			</td>
			<td>Choose between a horizontal and vertical directory layout.</td>
			<td></td>
		  </tr>  

    <?php 
	} ?>
	      <tr>
			<td><b>Show avatars</b></td>
			<td><input name="<?php echo $dud_option_name;?>[user_directory_show_avatars]" id="user_directory_show_avatars" type="checkbox" 
			   value="1" <?php if(!empty($dud_options['user_directory_show_avatars'])) { checked( '1', $dud_options['user_directory_show_avatars'] ); } ?> />&nbsp;&nbsp;
			   <select class="dd-menu-chk-box-width" name="<?php echo $dud_option_name;?>[user_directory_avatar_style]" id="user_directory_avatar_style">
						<OPTION value="standard">Standard Style</OPTION>             
						<OPTION value="rounded-edges" <?php echo ($dud_options['user_directory_avatar_style'] == "rounded-edges") 
							? "SELECTED" : ""; ?>>Rounded edges</OPTION> 
						<OPTION value="circle" <?php echo ($dud_options['user_directory_avatar_style'] == "circle") ? "SELECTED" : ""; ?>>Circle</OPTION> 
			   </select>
			</td>
			<td>Show avatars in your directory. Note: Some themes enforce a certain avatar shape. In those cases, DUD will *not* alter the site-wide avatar shape settings.</td>
			<td></td>
		 </tr>
		 
		 <tr id="ud_avatar_size_and_padding">
			<td><b>Avatar Size</b></td>
			<td><input class="dd-menu-chk-box-width" type="text" size="3" maxlength="3" id="user_directory_avatar_size" name="<?php echo $dud_option_name;?>[user_directory_avatar_size]" 
					value="<?php echo (!empty($dud_options['user_directory_avatar_size'] )) ? esc_attr( $dud_options['user_directory_avatar_size'] ) : "96"; ?>" /> px</td>
			<td>96px is the WordPress and DUD default avatar size.</td>
			<td></td>
		 </tr>
		 
		 <tr id="avatar_padding">
			<td><b>Avatar Padding</b></td>
			<td><input class="dd-menu-chk-box-width" type="text" size="3" maxlength="3" id="user_directory_avatar_padding" name="<?php echo $dud_option_name;?>[user_directory_avatar_padding]" 
					value="<?php echo (!empty($dud_options['user_directory_avatar_padding'] )) ? esc_attr( $dud_options['user_directory_avatar_padding'] ) : "120"; ?>" /> px</td>
			<td>Avatar Padding: The amount of space (in pixels) between the avatar on the left and the rest of the user information in your listing on the right. The default for DUD is 120px.</td>
			<td></td>
		 </tr>
		 	
	<?php if(function_exists('bp_is_active')) { ?>
			 <tr>
				<td><b>Link to Author Page<br>or BP Profile</b></td>
				<td><input name="<?php echo $dud_option_name;?>[ud_author_page]" id="ud_author_page" type="checkbox" 
				   value="1" <?php if(!empty($dud_options['ud_author_page'])) { checked( '1', $dud_options['ud_author_page'] ); } ?> />&nbsp;&nbsp;
				   <select class="dd-menu-chk-box-width" name="<?php echo $dud_option_name;?>[ud_auth_or_bp]" id="ud_auth_or_bp">
							<OPTION value="auth">WP Author Page</OPTION>             
							<OPTION value="bp" <?php echo (!empty($dud_options['ud_auth_or_bp']) && $dud_options['ud_auth_or_bp'] == "bp") 
								? "SELECTED" : ""; ?>>BP Member Activity Page</OPTION> 
							<OPTION value="bpp" <?php echo (!empty($dud_options['ud_auth_or_bp']) && $dud_options['ud_auth_or_bp'] == "bpp") 
								? "SELECTED" : ""; ?>>BP Member Profile Page</OPTION> 
				   </select>
				</td>
				<td>Hyperlink the user name & avatar to the user&lsquo;s WP author page or BuddyPress profile pages.</td>
				<td></td>
			 </tr>
			 <tr id="open_linked_page">
				<td><b>Open Linked Page</b></td>
				<td><select class="dd-menu-no-chk-box-width" name="<?php echo $dud_option_name;?>[ud_target_window]" id="ud_target_window">
							<OPTION value="separate">In new tab</OPTION>             
							<OPTION value="main" <?php echo ($dud_options['ud_target_window'] == "main") 
								? "SELECTED" : ""; ?>>In same window</OPTION> 
							
				   </select>
				</td>
				<td></td>
				<td></td>
			 </tr>
    <?php } else { ?> 
				 <tr>
					<td><b>Link to Author Page</b></td>
					<td><input name="<?php echo $dud_option_name;?>[ud_author_page]" id="ud_author_page" type="checkbox" 
					   value="1" <?php if(!empty($dud_options['ud_author_page'])) { checked( '1', $dud_options['ud_author_page'] ); } ?> />&nbsp;&nbsp;
					   <select class="dd-menu-chk-box-width" name="<?php echo $dud_option_name;?>[ud_target_window]" id="ud_target_window">
								<OPTION value="separate">Open in new window</OPTION>             
								<OPTION value="main" <?php echo ($dud_options['ud_target_window'] == "main") 
									? "SELECTED" : ""; ?>>Open in main window</OPTION> 
								
					   </select>
					</td>
					<td>Hyperlink the user name and avatar to the user&lsquo;s WordPress author page.</td>
					<td></td>
				 </tr>
    <?php } ?>
	
		  <tr id="show-auth-pg-lnk">
			<td><b>Show Author Page Link</b></td>
			<td>
			   <select class="dd-menu-no-chk-box-width" name="<?php echo $dud_option_name;?>[ud_show_author_link]" id="ud_show_author_link">          
						<OPTION value="posts-exist">If Posts Exist</OPTION> 
						<OPTION value="always" <?php echo ($dud_options['ud_show_author_link'] == "always") 
							? "SELECTED" : ""; ?>>Always</OPTION> 
			   </select>
			</td>
			<td>Select "Always" ONLY if you have a custom author.php page that is shown whether or not the author has posts. Otherwise you'll get a Page Not Found error for authors with no posts.</td>
			<td></td>
		 </tr>
		 
		 <tr>
			<td><b>User Name Display Format</b></td>
			<td><select class="dd-menu-no-chk-box-width" name="<?php echo $dud_option_name;?>[ud_format_name]" id="ud_format_name">
						<OPTION value="fl">First Last</OPTION> 
						<OPTION value="lf" <?php echo ($dud_options['ud_format_name'] == "lf") 
							? "SELECTED" : ""; ?>>Last, First</OPTION>            		                 		
					</select> 
			</td>
			<td> <i>First Last</i> shows the user name like "Sally Smith." <i>Last, First</i> shows it like "Smith, Sally."</td>
			<td></td>
		 </tr>
		 
		 <tr id="one-page-dir-type-a">
			<td><b>Letter Divider</b></td>
			<td>
				<select class="dd-menu-no-chk-box-width" name="<?php echo $dud_option_name;?>[ud_letter_divider]" id="ud_letter_divider">
						<OPTION value="nld">No letter divider</OPTION> 
						<OPTION value="ld-fl" <?php echo ($dud_options['ud_letter_divider'] == "ld-fl") ? "SELECTED" : ""; ?>>Letter Inside Bar</OPTION>  
						<OPTION value="ld-bb" <?php echo ($dud_options['ud_letter_divider'] == "ld-bb") ? "SELECTED" : ""; ?>>Letter w/Bottom Border</OPTION>  
						<OPTION value="ld-tb" <?php echo ($dud_options['ud_letter_divider'] == "ld-tb") ? "SELECTED" : ""; ?>>Letter w/Top & Bottom Border</OPTION> 
						<OPTION value="ld-lo" <?php echo ($dud_options['ud_letter_divider'] == "ld-lo") ? "SELECTED" : ""; ?>>Letter Only</OPTION>  							
						  
				</select> 
			</td>
			<td>You can show a divider for each alphabet letter in a Single Page Directory. The <a href="http://sgcustomwebsolutions.com/wordpress-plugin-development/" target="_blank">Alpha Links Scroll Add-on</a> displays clickable letter links at the top that will smoothly scroll to the matching letter divider.</td>
			<td></td>
		 </tr>
		 
		 <tr id="letter-divider-border-settings">
			<td><b>Border Thickness</b><br><select name="<?php echo $dud_option_name;?>[ud_divider_border_thickness]" id="ud_divider_border_thickness" style="width:65%">
					<OPTION value="1px" <?php echo !empty($dud_options['ud_divider_border_thickness']) && $dud_options['ud_divider_border_thickness'] == "1px" ? "SELECTED" : ""; ?>>1px</OPTION> 
					<OPTION value="2px" <?php echo !empty($dud_options['ud_divider_border_thickness']) && $dud_options['ud_divider_border_thickness'] == "2px" ? "SELECTED" : ""; ?>>2px</OPTION>             
					<OPTION value="3px" <?php echo !empty($dud_options['ud_divider_border_thickness']) && $dud_options['ud_divider_border_thickness'] == "3px" ? "SELECTED" : ""; ?>>3px</OPTION>  
					<OPTION value="4px" <?php echo !empty($dud_options['ud_divider_border_thickness']) && $dud_options['ud_divider_border_thickness'] == "4px" ? "SELECTED" : ""; ?>>4px</OPTION>              
					</select> </td>
			<td><b>Border Color</b><br><input type="text" name="<?php echo $dud_option_name;?>[ud_divider_border_color]" 
						value="<?php echo !empty($dud_options['ud_divider_border_color']) ? esc_attr( $dud_options['ud_divider_border_color'] ) : ""; ?>" class="cpa-color-picker"></td>
			<td></td>
			<td></td>
		 </tr>	
		  <tr id="letter-divider-border-settings-2">
			<td><b>Border Length</b><br><select name="<?php echo $dud_option_name;?>[ud_divider_border_length]" id="ud_divider_border_length" style="width:65%">
						<OPTION value="100%" <?php echo !empty($dud_options['ud_divider_border_length']) && $dud_options['ud_divider_border_length'] == "100%" ? "SELECTED" : ""; ?>>100%</OPTION> 
						<OPTION value="90%" <?php echo !empty($dud_options['ud_divider_border_length']) && $dud_options['ud_divider_border_length'] == "90%" ? "SELECTED" : "";   ?>>90%</OPTION> 
						<OPTION value="80%" <?php echo !empty($dud_options['ud_divider_border_length']) && $dud_options['ud_divider_border_length'] == "80%" ? "SELECTED" : "";   ?>>80%</OPTION> 
						<OPTION value="70%" <?php echo !empty($dud_options['ud_divider_border_length']) && $dud_options['ud_divider_border_length'] == "70%" ? "SELECTED" : "";   ?>>70%</OPTION> 
						<OPTION value="60%" <?php echo !empty($dud_options['ud_divider_border_length']) && $dud_options['ud_divider_border_length'] == "60%" ? "SELECTED" : "";   ?>>60%</OPTION> 
						<OPTION value="50%" <?php echo !empty($dud_options['ud_divider_border_length']) && $dud_options['ud_divider_border_length'] == "50%" ? "SELECTED" : "";   ?>>50%</OPTION> 
				 </select></td>
			<td><b>Border Style</b><br><select name="<?php echo $dud_option_name;?>[ud_divider_border_style]" id="ud_divider_border_style" style="width:42%">
						<OPTION value="solid" <?php echo !empty($dud_options['ud_divider_border_style']) && $dud_options['ud_divider_border_style'] == "solid"   ? "SELECTED" : ""; ?>>solid</OPTION> 
						<OPTION value="dotted" <?php echo !empty($dud_options['ud_divider_border_style']) && $dud_options['ud_divider_border_style'] == "dotted" ? "SELECTED" : ""; ?>>dotted</OPTION> 
						<OPTION value="dashed" <?php echo !empty($dud_options['ud_divider_border_style']) && $dud_options['ud_divider_border_style'] == "dashed" ? "SELECTED" : ""; ?>>dashed</OPTION> 
						<OPTION value="double" <?php echo !empty($dud_options['ud_divider_border_style']) && $dud_options['ud_divider_border_style'] == "double" ? "SELECTED" : ""; ?>>double</OPTION> 
						<OPTION value="groove" <?php echo !empty($dud_options['ud_divider_border_style']) && $dud_options['ud_divider_border_style'] == "groove" ? "SELECTED" : ""; ?>>groove</OPTION> 
						<OPTION value="ridge" <?php echo !empty($dud_options['ud_divider_border_style']) && $dud_options['ud_divider_border_style'] == "ridge"   ? "SELECTED" : ""; ?>>ridge</OPTION> 
				 </select> </td>
			<td></td>
			<td></td>
		 </tr>				
							
     	 <tr id="one-page-dir-type-b">
				<td>         
				   <b>Letter Font Color</b>
				   <input type="text" name="<?php echo $dud_option_name;?>[ud_letter_divider_font_color]" 
						value="<?php echo esc_attr( $dud_options['ud_letter_divider_font_color'] ); ?>" class="cpa-color-picker">
				</td>
				<td>
					<div id="divider-fill-color"><b>Bar Fill Color</b>
						<input type="text" name="<?php echo $dud_option_name;?>[ud_letter_divider_fill_color]" 
							value="<?php echo esc_attr( $dud_options['ud_letter_divider_fill_color'] ); ?>" class="cpa-color-picker"></div>
					<div id="divider-font-size">
						<b>Letter Font Size</b><br><input type="text" size="9" maxlength="3" id="ud_divider_font_size" name="<?php echo $dud_option_name;?>[ud_divider_font_size]" 
						value="<?php echo (!empty($dud_options['ud_divider_font_size'] )) ? esc_attr( $dud_options['ud_divider_font_size'] ) : ""; ?>" /> px</div>
				</td>
				<td></td>
				<td></td>
			 </tr>

			 <tr>
				<td><b>Listing Border</b>
				<td>
					<select class="dd-menu-no-chk-box-width" name="<?php echo $dud_option_name;?>[user_directory_border]" id="user_directory_border">
							<OPTION value="dividing_border">Dividing border</OPTION> 
							<OPTION value="surrounding_border" <?php echo ($dud_options['user_directory_border'] == "surrounding_border") 
								? "SELECTED" : ""; ?>>Surrounding border</OPTION>             
							<OPTION value="no_border" <?php echo ($dud_options['user_directory_border'] == "no_border") 
								? "SELECTED" : ""; ?>>No border</OPTION> 
						</select> 
				</td>
				<td>Show a border around or between each listing.</td>
				<td></td>
			 </tr>
			<tr id="border-settings">
				<td><b>Border Thickness</b><br><select name="<?php echo $dud_option_name;?>[user_directory_border_thickness]" id="user_directory_border_thickness" style="width:65%">
						<OPTION value="1px" <?php echo (!empty($dud_options['user_directory_border_thickness']) && $dud_options['user_directory_border_thickness'] == "1px") ? "SELECTED" : ""; ?>>1px</OPTION> 
						<OPTION value="2px" <?php echo (!empty($dud_options['user_directory_border_thickness']) && $dud_options['user_directory_border_thickness'] == "2px") ? "SELECTED" : ""; ?>>2px</OPTION>             
						<OPTION value="3px" <?php echo (!empty($dud_options['user_directory_border_thickness']) && $dud_options['user_directory_border_thickness'] == "3px") ? "SELECTED" : ""; ?>>3px</OPTION>  
						<OPTION value="4px" <?php echo (!empty($dud_options['user_directory_border_thickness']) && $dud_options['user_directory_border_thickness'] == "4px") ? "SELECTED" : ""; ?>>4px</OPTION>              
						</select> </td>
				<td><b>Border Color</b><br><input type="text" name="<?php echo $dud_option_name;?>[user_directory_border_color]" 
							value="<?php echo esc_attr( $dud_options['user_directory_border_color'] ); ?>" class="cpa-color-picker"></td>
				<td></td>
				<td></td>
			 </tr>	
			 <tr id="border-settings-2">
				<td><b>Border Length</b><br><select name="<?php echo $dud_option_name;?>[user_directory_border_length]" id="user_directory_border_length" style="width:65%">
						<OPTION value="100%" <?php echo (!empty($dud_options['user_directory_border_length']) && $dud_options['user_directory_border_length'] == "100%") ? "SELECTED" : ""; ?>>100%</OPTION> 
						<OPTION value="90%" <?php echo (!empty($dud_options['user_directory_border_length']) && $dud_options['user_directory_border_length'] == "90%") ? "SELECTED" : ""; ?>>90%</OPTION> 
						<OPTION value="80%" <?php echo (!empty($dud_options['user_directory_border_length']) && $dud_options['user_directory_border_length'] == "80%") ? "SELECTED" : ""; ?>>80%</OPTION> 
						<OPTION value="70%" <?php echo (!empty($dud_options['user_directory_border_length']) && $dud_options['user_directory_border_length'] == "70%") ? "SELECTED" : ""; ?>>70%</OPTION> 
						<OPTION value="60%" <?php echo (!empty($dud_options['user_directory_border_length']) && $dud_options['user_directory_border_length'] == "60%") ? "SELECTED" : ""; ?>>60%</OPTION> 
						<OPTION value="50%" <?php echo (!empty($dud_options['user_directory_border_length']) && $dud_options['user_directory_border_length'] == "50%") ? "SELECTED" : ""; ?>>50%</OPTION> 
				 </select></td>
				<td><b>Border Style</b><br><select name="<?php echo $dud_option_name;?>[user_directory_border_style]" id="user_directory_border_style" style="width:42%">
						<OPTION value="solid" <?php echo (!empty($dud_options['user_directory_border_style']) && $dud_options['user_directory_border_style'] == "solid") ? "SELECTED" : ""; ?>>solid</OPTION> 
						<OPTION value="dotted" <?php echo (!empty($dud_options['user_directory_border_style']) && $dud_options['user_directory_border_style'] == "dotted") ? "SELECTED" : ""; ?>>dotted</OPTION> 
						<OPTION value="dashed" <?php echo (!empty($dud_options['user_directory_border_style']) && $dud_options['user_directory_border_style'] == "dashed") ? "SELECTED" : ""; ?>>dashed</OPTION> 
						<OPTION value="double" <?php echo (!empty($dud_options['user_directory_border_style']) && $dud_options['user_directory_border_style'] == "double") ? "SELECTED" : ""; ?>>double</OPTION> 
						<OPTION value="groove" <?php echo (!empty($dud_options['user_directory_border_style']) && $dud_options['user_directory_border_style'] == "groove") ? "SELECTED" : ""; ?>>groove</OPTION> 
						<OPTION value="ridge" <?php echo (!empty($dud_options['user_directory_border_style']) && $dud_options['user_directory_border_style'] == "ridge") ? "SELECTED" : ""; ?>>ridge</OPTION> 
				 </select> </td>
				<td></td>
				<td></td>
			 </tr>								  
		 <tr>
			<td><div id="top"><b>Listing Font Size</b></div><BR>
				<input type="text" size="2" maxlength="2" id="user_directory_listing_fs" name="<?php echo $dud_option_name;?>[user_directory_listing_fs]" 
					value="<?php echo !empty($dud_options['user_directory_listing_fs']) ? esc_attr( $dud_options['user_directory_listing_fs'] ) : ""; ?>" /> px
			</td>
			<td><div id="top"><b>Space Between Listings</b></div><BR>
				<input type="text" size="2" maxlength="2" id="user_directory_listing_spacing" name="<?php echo $dud_option_name;?>[user_directory_listing_spacing]" 
					value="<?php echo !empty($dud_options['user_directory_listing_spacing']) || $dud_options['user_directory_listing_spacing'] === "0" ? esc_attr( $dud_options['user_directory_listing_spacing'] ) : ""; ?>" /> px</td>
			<td>Space Between Listings: how much space (in pixels) to insert between each directory listing.</td>
			<td></td>
		 </tr>
		 
	</table>	
<br/><br/>
</div>
<br/><br/>

<div class="dud-settings-section-header">&nbsp; Alphabet and Pagination Link Settings</div>
<div class="dud-settings-section">
	<table class="form-table">
		 <tr>
			 <td><b><span style='color:#08788c;'>LETTER LINKS</span></b></td>
			 <td></td>
			 <td>This section only applies if you have selected "Alphabet Letter Links" as the directory type.</td>
			 <td></td>
		 </tr> 	 
		 <tr>
			<td><div id="top"><b>Letter Links Font Size</b></div><br>
				<input type="text" size="2" maxlength="2" id="user_directory_letter_fs" name="<?php echo $dud_option_name;?>[user_directory_letter_fs]" 
					value="<?php echo !empty($dud_options['user_directory_letter_fs']) ? esc_attr( $dud_options['user_directory_letter_fs'] ) : ""; ?>" /> px
			</td>
			<td><div id="top"><b>Letter Links Spacing</b></div><BR>
				<input type="text" size="2" maxlength="2" id="ud_alpha_link_spacer" name="<?php echo $dud_option_name;?>[ud_alpha_link_spacer]" 
					value="<?php echo !empty($dud_options['ud_alpha_link_spacer']) ? esc_attr( $dud_options['ud_alpha_link_spacer'] ) : ""; ?>" /> px
			</td>
			<td>Letter Links Spacing: how much space (in pixels) to insert between each of the alphabetic links.</td>
			<td></td>
		 </tr>		
		 <tr>
			<td><div id="top"><b>Letter Link Color</b></div><br>
			<input type="text" name="<?php echo $dud_option_name;?>[ud_alpha_link_color]" 
							value="<?php echo !empty($dud_options['ud_alpha_link_color']) ? esc_attr( $dud_options['ud_alpha_link_color'] ) : ""; ?>" class="cpa-color-picker">
							
			</td>
			<td><div id="top"><b>Letter Link Clicked Color</b></div><BR>
				<input type="text" name="<?php echo $dud_option_name;?>[ud_alpha_link_clicked_color]" 
							value="<?php echo !empty($dud_options['ud_alpha_link_clicked_color']) ? esc_attr( $dud_options['ud_alpha_link_clicked_color'] ) : ""; ?>" class="cpa-color-picker">
			</td>
			<td><i>Letter Link Color</i>: the color of the alphabet letter links. Leave blank to use your theme's default link color.<br><BR> 
				<i>Letter Link Clicked Color</i>: the color of the alphabet letter link that is currently being viewed. Leave blank if you do not want to highlight the selected letter link.</td>
			<td></td>
		</tr>
		<tr>
			<td colspan="3" style="line-height:22px;"><b><span style='color:#08788c;'>PAGINATION</span></b><br><hr>Pagination works for both Single Page & Alphabet Letter Links directory types. 
			     On Letter Links directories, pagination will only be shown for a selected letter if that letter has more listings than the number of users per page. 
				 <strong>Please note: if using the Alpha Links Scroll add-on, pagination will REPLACE the scrollable letters.</strong><hr></td>
		</tr>
		 <tr>
			<td><b>Number of Users Per Page</b></td>
			<td><input type="text" size="3" maxlength="4" id="ud_users_per_page" class="dd-menu-no-chk-box-width" name="<?php echo $dud_option_name;?>[ud_users_per_page]" 
					value="<?php echo !empty($dud_options['ud_users_per_page']) ? esc_attr( $dud_options['ud_users_per_page'] ) : ""; ?>" /></td>
			<td>Enter a number here to activate pagination on your directory. If you do not want pagination, leave this blank.</td>
			<td></td>
		 </tr>		 
		  <tr>
			<td><b>Show Pagination Links</b></td>
			<td><select class="dd-menu-no-chk-box-width" name="<?php echo $dud_option_name;?>[ud_show_pagination_top_bottom]" id="ud_show_pagination_top_bottom">
						<OPTION value="top">Top of Directory</OPTION> 
						<OPTION value="bottom" <?php echo ($dud_options['ud_show_pagination_top_bottom'] == "bottom") 
							? "SELECTED" : ""; ?>>Bottom of Directory</OPTION> 
						<OPTION value="both" <?php echo ($dud_options['ud_show_pagination_top_bottom'] == "both") 
							? "SELECTED" : ""; ?>>Top & Bottom of Directory</OPTION>  
				</select> </td>
			<td></td>
			<td></td>
		 </tr>		 
		 <tr id="pagination_above_below">
			<td><b>Pagination Top Position</b></td>
			<td><select class="dd-menu-no-chk-box-width" name="<?php echo $dud_option_name;?>[ud_show_pagination_above_below]" id="ud_show_pagination_above_below">
						<OPTION value="above">Above Search Box</OPTION> 
						<OPTION value="below" <?php echo ($dud_options['ud_show_pagination_above_below'] === "below") 
							? "SELECTED" : ""; ?>>Below Search Box</OPTION> 
				</select> </td>
			<td>This only applies if your directory is showing the last name/display name search box, or you have installed the Meta Fields Search add-on.</td>
			<td></td>
		 </tr>		 
		 <tr>
			<td><b>Pagination Link Font Size</b></td>
			<td><input type="text" size="2" maxlength="3" id="ud_pagination_font_size" class="dd-menu-chk-box-width" name="<?php echo $dud_option_name;?>[ud_pagination_font_size]" 
					value="<?php echo !empty($dud_options['ud_pagination_font_size']) ? esc_attr( $dud_options['ud_pagination_font_size'] ) : ""; ?>"/> px</td>
			<td></td>
			<td></td>
		 </tr>
		 
		  <tr>
			<td><b>Padding Top</b></td>
			<td><input type="text" size="2" maxlength="3" id="ud_pagination_padding_top" class="dd-menu-chk-box-width" name="<?php echo $dud_option_name;?>[ud_pagination_padding_top]" 
					value="<?php echo !empty($dud_options['ud_pagination_padding_top']) ? esc_attr( $dud_options['ud_pagination_padding_top'] ) : "0"; ?>"/> px</td>
			<td>Increase the padding above the pagination links. Set to 0 or leave blank for no additional padding.</td>
			<td></td>
		 </tr>
		  <tr>
			<td><b>Padding Bottom</b></td>
			<td><input type="text" size="2" maxlength="3" id="ud_pagination_padding_bottom" class="dd-menu-chk-box-width" name="<?php echo $dud_option_name;?>[ud_pagination_padding_bottom]" 
					value="<?php echo !empty($dud_options['ud_pagination_padding_bottom']) ? esc_attr( $dud_options['ud_pagination_padding_bottom'] ) : "10"; ?>"/> px</td>
			<td>Increase the padding below the pagination links. Set to 0 or leave blank for no additional padding.</td>
			<td></td>
		 </tr>
		 <tr>
			<td><div id="top"><b>Pagination Link Color</b></div><br>
			<input type="text" name="<?php echo $dud_option_name;?>[ud_pagination_link_color]" 
							value="<?php echo !empty($dud_options['ud_pagination_link_color']) ? esc_attr( $dud_options['ud_pagination_link_color'] ) : ""; ?>" class="cpa-color-picker">
							
			</td>
			<td><div id="top"><b>Pagination Link Clicked Color</b></div><BR>
				<input type="text" name="<?php echo $dud_option_name;?>[ud_pagination_link_clicked_color]" 
							value="<?php echo !empty($dud_options['ud_pagination_link_clicked_color']) ? esc_attr( $dud_options['ud_pagination_link_clicked_color'] ) : ""; ?>" class="cpa-color-picker">
			</td>
			<td><i>Pagination Link Color</i>: the color of the numeric page links. Leave blank to use your theme's default link color.
				<i>Pagination Link Clicked Color</i>: the color of the page link that is currently being viewed. Leave blank if you do not want to highlight the selected page link.
				<br><br>It is recommended that the pagination link colors match their letter link counterparts when using an Alphabet Letter Links directory type.</td>
			<td></td>
		 </tr> 
	</table>	
<br/><br/>
</div>
<br/><br/>

<div class="dud-settings-section-header">&nbsp; Meta Fields Settings</div>
<div class="dud-settings-section">
	<table class="form-table">
		<tr>
			<td colspan="3" style="line-height:22px;"><b>Instructions</b><br><hr>This is where you will build the content of your directory. The key name listbox(es) below contain the names of the user meta fields available for use in your directory. 
			Find the meta key names corresponding to the user profile fields you want to display, then copy and paste each one into the Meta Key Name input fields. The Address and Social Meta Fields sections may be used instead if you would like DUD 
			to format them for you.<hr></td>
		</tr>
		<tr>
			<td><b>Show Email Addr</b>&nbsp;&nbsp;<input name="<?php echo $dud_option_name;?>[user_directory_email]" id="user_directory_email" type="checkbox" value="1" 
				<?php if(!empty($dud_options['user_directory_email'])) { checked( '1', $dud_options['user_directory_email'] ); } ?> /></td>
			<td><b>Show Website</b>&nbsp;&nbsp;<input name="<?php echo $dud_option_name;?>[user_directory_website]" id="user_directory_website" type="checkbox" value="1" 
				<?php if(!empty($dud_options['user_directory_website'])) { checked( '1', $dud_options['user_directory_website'] ); } ?> /></td>
			<td>Check the boxes to show these built-in WordPress user profile fields (located in the wp_users table) in the directory. If you wish to show an email or website address that is stored in a meta field instead, do *not* check these boxes, and simply add the email and/or website meta field key names below. </td>
			<td></td>
		 </tr>	 
		<tr>
			<td colspan="2"><b>WordPress Meta Key Names</b><br><?php echo dynamic_ud_load_meta_keys("wp"); ?></td> 
			<td id="list-box-instructions">A listing of the meta key fields <u>for reference only</u>. You must type or copy & paste the key name into the appropriate meta field below for the key field value to be displayed in the directory. Enter the key name using the SAME capitalization shown in the key names list.</td>
			<td></td>
		 </tr>
	 
	 <?php 
	 $dud_plugin_list = get_option('active_plugins');
	 
	 if ( in_array( 'cimy-user-extra-fields/cimy_user_extra_fields.php' , $dud_plugin_list ) || function_exists('bp_is_active') || in_array( 's2member/s2member.php' , $dud_plugin_list ) ) { ?>
		 <tr>
			<td colspan="2"><?php if ( in_array( 'cimy-user-extra-fields/cimy_user_extra_fields.php' , $dud_plugin_list ) ) { ?>
										<b>Cimy Field Names</b><br><?php echo dynamic_ud_load_meta_keys("cimy"); } 
								  else if(function_exists('bp_is_active')) { ?>
										<b>BuddyPress Extended Profile Field Names</b><BR><?php echo dynamic_ud_load_meta_keys("bp"); } 
								  else if(in_array( 's2member/s2member.php' , $dud_plugin_list ) ) { ?>
										<b>s2Member Custom Field Names</b><BR><?php echo dynamic_ud_load_meta_keys("s2m"); } ?>
			</td>
			<td id="list-box-instructions">You may also include any of these custom fields in your directory. <?php if(function_exists('bp_is_active')) { ?> Note: BuddyPress may clear the WordPress "last name" profile field in certain circumstances. Please ensure this field is not blank if sorting by last name, or the user will NOT appear in the directory.<?php } ?></td>
			<td></td>
		 </tr>
	 <?php } ?>
	 
     <tr>
        <td><b><span style='color:#08788c;'>USER META FIELDS</span></b></td>
        <td>
        	<select name="<?php echo $dud_option_name;?>[user_directory_num_meta_flds]" id="user_directory_num_meta_flds">
	            	<OPTION value="1" <?php echo (!empty($dud_options['user_directory_num_meta_flds']) && $dud_options['user_directory_num_meta_flds'] == "1") ? "SELECTED" : ""; ?>>1</OPTION> 
	            	<OPTION value="2" <?php echo (!empty($dud_options['user_directory_num_meta_flds']) && $dud_options['user_directory_num_meta_flds'] == "2") ? "SELECTED" : ""; ?>>2</OPTION> 
					<OPTION value="3" <?php echo (!empty($dud_options['user_directory_num_meta_flds']) && $dud_options['user_directory_num_meta_flds'] == "3") ? "SELECTED" : ""; ?>>3</OPTION> 
	            	<OPTION value="4" <?php echo (!empty($dud_options['user_directory_num_meta_flds']) && $dud_options['user_directory_num_meta_flds'] == "4") ? "SELECTED" : ""; ?>>4</OPTION> 
	            	<OPTION value="5" <?php echo (!empty($dud_options['user_directory_num_meta_flds']) && $dud_options['user_directory_num_meta_flds'] == "5") ? "SELECTED" : ""; ?>>5</OPTION> 
	            	<OPTION value="6" <?php echo (!empty($dud_options['user_directory_num_meta_flds']) && $dud_options['user_directory_num_meta_flds'] == "6") ? "SELECTED" : ""; ?>>6</OPTION>
	            	<OPTION value="7" <?php echo (!empty($dud_options['user_directory_num_meta_flds']) && $dud_options['user_directory_num_meta_flds'] == "7") ? "SELECTED" : ""; ?>>7</OPTION>
	            	<OPTION value="8" <?php echo (!empty($dud_options['user_directory_num_meta_flds']) && $dud_options['user_directory_num_meta_flds'] == "8") ? "SELECTED" : ""; ?>>8</OPTION>
	            	<OPTION value="9" <?php echo (!empty($dud_options['user_directory_num_meta_flds']) && $dud_options['user_directory_num_meta_flds'] == "9") ? "SELECTED" : ""; ?>>9</OPTION>
	            	<OPTION value="10" <?php echo (!empty($dud_options['user_directory_num_meta_flds']) && $dud_options['user_directory_num_meta_flds'] == "10") ? "SELECTED" : ""; ?>>10</OPTION> 
		</select> 
	</td>
        <td>Use the dropdown to show extra meta fields or hide unneeded ones. If you hide a meta key name/label field, that field will automatically be cleared.</td>
        <td></td>
     </tr>	
	 </table>
	 <table class="meta-flds">
	 <?php 
			for($inc = 1; $inc < 11; $inc++)
			{ 
				if( !empty($dud_options['user_directory_meta_link_' . $inc]) && $dud_options['user_directory_meta_link_' . $inc] === '#'
						&& empty($dud_options['dud_fld_format_' . $inc])) 
							$dud_options['dud_fld_format_' . $inc] = "2";		
		?>
				 <tr id="meta_fld_<?php echo $inc; ?>">
					<td><b>Meta Key Name <?php echo $inc; ?></b><br><input type="text" id="user_directory_meta_field_<?php echo $inc; ?>" name="<?php echo $dud_option_name;?>[user_directory_meta_field_<?php echo $inc; ?>]" 
						value="<?php echo !empty($dud_options['user_directory_meta_field_' . $inc]) ? esc_attr( $dud_options['user_directory_meta_field_' . $inc]) : ""; ?>" maxlength="75" /></td>
					
					<td><b>Format Meta Field <?php echo $inc; ?> As</b><br>
						<select name="<?php echo $dud_option_name;?>[dud_fld_format_<?php echo $inc; ?>]" id="dud_hyperlink_flds">
							<OPTION value="1" <?php echo (!empty($dud_options['dud_fld_format_' . $inc]) && $dud_options['dud_fld_format_' . $inc] == "1") ? "SELECTED" : ""; ?>>Plain Text</OPTION>
							<OPTION value="6" <?php echo (!empty($dud_options['dud_fld_format_' . $inc]) && $dud_options['dud_fld_format_' . $inc] == "6") ? "SELECTED" : ""; ?>>Phone Number</OPTION>  				
							<OPTION value="2" <?php echo (!empty($dud_options['dud_fld_format_' . $inc]) && $dud_options['dud_fld_format_' . $inc] == "2") ? "SELECTED" : ""; ?>>Hyperlink => Open in Same Window</OPTION> 
							<OPTION value="3" <?php echo (!empty($dud_options['dud_fld_format_' . $inc]) && $dud_options['dud_fld_format_' . $inc] == "3") ? "SELECTED" : ""; ?>>Hyperlink => Open in New Tab</OPTION> 
							<OPTION value="5" <?php echo (!empty($dud_options['dud_fld_format_' . $inc]) && $dud_options['dud_fld_format_' . $inc] == "5") ? "SELECTED" : ""; ?>>Multiple Values => Bulleted</OPTION>
							<OPTION value="4" <?php echo (!empty($dud_options['dud_fld_format_' . $inc]) && $dud_options['dud_fld_format_' . $inc] == "4") ? "SELECTED" : ""; ?>>Multiple Values => Comma Delimited</OPTION>
							<!--<OPTION value="10" <?php echo (!empty($dud_options['dud_fld_format_' . $inc]) && $dud_options['dud_fld_format_' . $inc] == "10") ? "SELECTED" : ""; ?>>Multiple Checkboxes => Bulleted => Label:Value</OPTION> -->
							<OPTION value="11" <?php echo (!empty($dud_options['dud_fld_format_' . $inc]) && $dud_options['dud_fld_format_' . $inc] == "11") ? "SELECTED" : ""; ?>>Multiple Checkboxes => Bulleted (Show Label Only)</OPTION>
							<!--<OPTION value="12" <?php echo (!empty($dud_options['dud_fld_format_' . $inc]) && $dud_options['dud_fld_format_' . $inc] == "12") ? "SELECTED" : ""; ?>>Multiple Checkboxes => Bulleted => Value Only</OPTION>--> 
							<!--<OPTION value="7" <?php echo (!empty($dud_options['dud_fld_format_' . $inc]) && $dud_options['dud_fld_format_' . $inc] == "7") ? "SELECTED" : ""; ?>>Multiple Checkboxes => Comma Delimited => Label:Value</OPTION>--> 							
							<OPTION value="8" <?php echo (!empty($dud_options['dud_fld_format_' . $inc]) && $dud_options['dud_fld_format_' . $inc] == "8") ? "SELECTED" : ""; ?>>Multiple Checkboxes => Comma Delimited (Show Label Only)</OPTION> 
							<!--<OPTION value="9" <?php echo (!empty($dud_options['dud_fld_format_' . $inc]) && $dud_options['dud_fld_format_' . $inc] == "9") ? "SELECTED" : ""; ?>>Multiple Checkboxes => Comma Delimited => Value Only</OPTION>-->
							<!--<OPTION value="13" <?php echo (!empty($dud_options['dud_fld_format_' . $inc]) && $dud_options['dud_fld_format_' . $inc] == "13") ? "SELECTED" : ""; ?>>Single Checkbox => Label:Value</OPTION>-->
							<OPTION value="14" <?php echo (!empty($dud_options['dud_fld_format_' . $inc]) && $dud_options['dud_fld_format_' . $inc] == "14") ? "SELECTED" : ""; ?>>Single Checkbox => Show Label Only</OPTION>
						    <!--<OPTION value="15" <?php echo (!empty($dud_options['dud_fld_format_' . $inc]) && $dud_options['dud_fld_format_' . $inc] == "15") ? "SELECTED" : ""; ?>>Single Checkbox => Value Only</OPTION>-->
							<OPTION value="16" <?php echo (!empty($dud_options['dud_fld_format_' . $inc]) && $dud_options['dud_fld_format_' . $inc] == "16") ? "SELECTED" : ""; ?>>Date => dd/mm/yyyy hh:mm:ss</OPTION>
							<OPTION value="17" <?php echo (!empty($dud_options['dud_fld_format_' . $inc]) && $dud_options['dud_fld_format_' . $inc] == "17") ? "SELECTED" : ""; ?>>Date => dd/mm/yy hh:mm:ss</OPTION>
							<OPTION value="18" <?php echo (!empty($dud_options['dud_fld_format_' . $inc]) && $dud_options['dud_fld_format_' . $inc] == "18") ? "SELECTED" : ""; ?>>Date => dd/mm/yy</OPTION>
							<OPTION value="19" <?php echo (!empty($dud_options['dud_fld_format_' . $inc]) && $dud_options['dud_fld_format_' . $inc] == "19") ? "SELECTED" : ""; ?>>Date => dd/mm/yyyy</OPTION>
							<OPTION value="20" <?php echo (!empty($dud_options['dud_fld_format_' . $inc]) && $dud_options['dud_fld_format_' . $inc] == "20") ? "SELECTED" : ""; ?>>Date => mm/dd/yyyy hh:mm:ss</OPTION>
							<OPTION value="21" <?php echo (!empty($dud_options['dud_fld_format_' . $inc]) && $dud_options['dud_fld_format_' . $inc] == "21") ? "SELECTED" : ""; ?>>Date => mm/dd/yy hh:mm:ss</OPTION>
							<OPTION value="22" <?php echo (!empty($dud_options['dud_fld_format_' . $inc]) && $dud_options['dud_fld_format_' . $inc] == "22") ? "SELECTED" : ""; ?>>Date => mm/dd/yy</OPTION>
							<OPTION value="23" <?php echo (!empty($dud_options['dud_fld_format_' . $inc]) && $dud_options['dud_fld_format_' . $inc] == "23") ? "SELECTED" : ""; ?>>Date => mm/dd/yyyy</OPTION>
						</select>
					</td>
					
					<td><div id="meta_label_<?php echo $inc; ?>"><b>Meta Field Label <?php echo $inc; ?></b><br><input type="text" id="user_directory_meta_label_<?php echo $inc; ?>" name="<?php echo $dud_option_name;?>[user_directory_meta_label_<?php echo $inc; ?>]" 
						value="<?php echo !empty($dud_options['user_directory_meta_label_' . $inc]) ? esc_attr( $dud_options['user_directory_meta_label_' . $inc] ) : ""; ?>" maxlength="75"/>  
						</div>
					</td>
                    <td></td>					
				 </tr>
			
	  <?php } ?>
			<tr><td></td></tr>
	  </table>
	  <table class="form-table">
       <tr>
        <td><b><span style='color:#08788c;'>ADDRESS META FIELDS &nbsp;&nbsp;<div id="address-down-arrow" name="address-down-arrow"><i class="fa fa-angle-down" aria-hidden="true"></i></div><div id="address-up-arrow" name="address-up-arrow"><i class="fa fa-angle-up" aria-hidden="true"></i></div>
</span></b></td>
        <td><input name="<?php echo $dud_option_name;?>[user_directory_address]" id="user_directory_address" type="hidden" value="<?php echo $dud_options['user_directory_address'];?>"/>        	
        </td>
        <td>Expand this section if a formatted mailing address is needed. Note: the address fields will be cleared automatically if this section is minimized.</td>
        <td></td>
     </tr> 
        
     <tr id="street1">
        <td><b>Street 1 Meta Key Name</b></td>
        <td><input type="text" id="user_directory_addr_1" name="<?php echo $dud_option_name;?>[user_directory_addr_1]" 
            value="<?php echo !empty($dud_options['user_directory_addr_1']) ? esc_attr( $dud_options['user_directory_addr_1'] ) : ""; ?>" maxlength="50"/></td>
        <td>Enter your address meta keys here to display a formatted mailing address. Use the Key Names list above for reference.</td>
        <td></td>
     </tr>
     
     <tr id="street2">
        <td><b>Street 2 Meta Key Name</b></td>
        <td><input type="text" id="user_directory_addr_2" name="<?php echo $dud_option_name;?>[user_directory_addr_2]" 
            value="<?php echo !empty($dud_options['user_directory_addr_2']) ? esc_attr( $dud_options['user_directory_addr_2'] ) : ""; ?>" maxlength="50"/></td>
        <td></td>
        <td></td>
     </tr>
     
     <tr id="city">
        <td><b>City Meta Key Name</b></td>
        <td><input type="text" id="user_directory_city" name="<?php echo $dud_option_name;?>[user_directory_city]" 
            value="<?php echo !empty($dud_options['user_directory_city']) ? esc_attr( $dud_options['user_directory_city'] ) : ""; ?>" maxlength="50"/></td>
        <td></td>
        <td></td>
     </tr>
          
     <tr id="state">
        <td><b>State Meta Key Name</b></td>
        <td><input type="text" id="user_directory_state" name="<?php echo $dud_option_name;?>[user_directory_state]" 
        	value="<?php echo !empty($dud_options['user_directory_state']) ? esc_attr( $dud_options['user_directory_state'] ) : ""; ?>" /></td>
        <td></td>
     </tr> 
         
     <tr id="zip">
        <td><b>Zip Meta Key Name</b></td>
        <td><input type="text" id="user_directory_zip" name="<?php echo $dud_option_name;?>[user_directory_zip]" 
            value="<?php echo !empty($dud_options['user_directory_zip']) ? esc_attr( $dud_options['user_directory_zip'] ) : ""; ?>" maxlength="50"/></td>
        <td></td>
        <td></td>
     </tr> 
	  <tr id="country">
        <td><b>Country Meta Key Name</b></td>
        <td><input type="text" id="user_directory_country" name="<?php echo $dud_option_name;?>[user_directory_country]" 
            value="<?php echo !empty($dud_options['user_directory_country']) ? esc_attr( $dud_options['user_directory_country'] ) : ""; ?>" maxlength="50"/></td>
        <td></td>
        <td></td>
     </tr> 
     <tr>
        <td><b><span style='color:#08788c;'>SOCIAL META FIELDS &nbsp;&nbsp;<div id="social-down-arrow" name="social-down-arrow"><i class="fa fa-angle-down" aria-hidden="true"></i></div><div id="social-up-arrow" name="social-up-arrow"><i class="fa fa-angle-up" aria-hidden="true"></i></div>
</span></b></td>
        <td></td>
        <td>Expand this section if you have social media meta fields to display. These will be shown as a row of icons. Note: the social media fields will be cleared automatically if this section is minimized.</td>
        <td><input name="<?php echo $dud_option_name;?>[ud_social]" id="ud_social" type="hidden" value="<?php echo $dud_options['ud_social'];?>"/> </td>
     </tr> 
        
     <tr id="facebook">
        <td><b>Facebook Meta Key Name</b></td>
        <td><input type="text" id="ud_facebook" name="<?php echo $dud_option_name;?>[ud_facebook]" 
            value="<?php echo !empty($dud_options['ud_facebook']) ? esc_attr( $dud_options['ud_facebook'] ) : ""; ?>" maxlength="50"/></td>
        <td></td>
        <td></td>
     </tr>
     
     <tr id="twitter">
        <td><b>Twitter Meta Key Name</b></td>
        <td><input type="text" id="ud_twitter" name="<?php echo $dud_option_name;?>[ud_twitter]" 
            value="<?php echo !empty($dud_options['ud_twitter']) ? esc_attr( $dud_options['ud_twitter'] ) : ""; ?>" maxlength="50"/></td>
        <td></td>
        <td></td>
     </tr>
     
     <tr id="linkedin">
        <td><b>LinkedIn Meta Key Name</b></td>
        <td><input type="text" id="ud_linkedin" name="<?php echo $dud_option_name;?>[ud_linkedin]" 
            value="<?php echo !empty($dud_options['ud_linkedin']) ? esc_attr( $dud_options['ud_linkedin'] ) : "";  ?>" maxlength="50"/></td>
        <td></td>
        <td></td>
     </tr>
                   
     <tr id="google">
        <td><b>Google+ Meta Key Name</b></td>
        <td><input type="text" id="ud_google" name="<?php echo $dud_option_name;?>[ud_google]" 
            value="<?php echo !empty($dud_options['ud_google']) ? esc_attr( $dud_options['ud_google'] ) : ""; ?>" maxlength="50"/></td>
        <td></td>
        <td></td>
     </tr> 

     <tr id="pintrest">
        <td><b>Pinterest Meta Key Name</b></td>
        <td><input type="text" id="ud_pinterest" name="<?php echo $dud_option_name;?>[ud_pinterest]" 
            value="<?php echo !empty($dud_options['ud_pinterest']) ? esc_attr( $dud_options['ud_pinterest'] ) : ""; ?>" maxlength="50"/></td>
        <td></td>
        <td></td>
     </tr> 
	 
	 <tr id="instagram">
        <td><b>Instagram Meta Key Name</b></td>
        <td><input type="text" id="ud_instagram" name="<?php echo $dud_option_name;?>[ud_instagram]" 
            value="<?php echo !empty($dud_options['ud_instagram']) ? esc_attr( $dud_options['ud_instagram'] ) : ""; ?>" maxlength="50"/></td>
        <td></td>
        <td></td>
     </tr> 	
	 
     <tr id="icon_size">
			<td><b>Icon Size</b></td>
			<td><input type="text" size="3" maxlength="3" id="ud_icon_size" name="<?php echo $dud_option_name;?>[ud_icon_size]" 
					value="<?php echo (!empty($dud_options['ud_icon_size'] )) ? esc_attr( $dud_options['ud_icon_size'] ) : "22"; ?>" /> px</td>
			<td></td>
			<td></td>
	</tr>
	
	 <tr id="icon_color">
			<td><b>Icon Link Color</b></td>
			<td><input type="text" name="<?php echo $dud_option_name;?>[ud_icon_color]" 
						value="<?php echo !empty($dud_options['ud_icon_color']) ? esc_attr( $dud_options['ud_icon_color'] ) : ""; ?>" class="cpa-color-picker"></td>
			<td></td>
			<td></td>
	</tr>
	
	<tr id="icon_style">
	    <?php if(empty($dud_options['ud_icon_style'])) $dud_options['ud_icon_style'] = '1';?>
        <td><b>Icon Style</b></td>
        <td>
        	<input type="radio" name="<?php echo $dud_option_name;?>[ud_icon_style]" 
        		value="1" <?php checked( '1', $dud_options['ud_icon_style'] ); ?> />
				    <i class='fa fa-facebook-official' aria-hidden='true'></i>&nbsp;&nbsp;    
				    <i class='fa fa-twitter-square' aria-hidden='true'></i>&nbsp;&nbsp;  
					<i class='fa fa-linkedin-square' aria-hidden='true'></i>&nbsp;&nbsp;    
					<i class="fa fa-google-plus-square" aria-hidden="true"></i>&nbsp;&nbsp;    
					<i class="fa fa-pinterest-square" aria-hidden="true"></i>&nbsp;&nbsp;    
					<i class="fa fa-instagram" aria-hidden="true"></i>&nbsp;&nbsp; <BR><BR>   
        	<input type="radio" name="<?php echo $dud_option_name;?>[ud_icon_style]" 
        		value="2" <?php checked( '2', $dud_options['ud_icon_style'] ); ?> />
				    <i class="fa fa-facebook" aria-hidden="true"></i>&nbsp;&nbsp;    
				    <i class="fa fa-twitter" aria-hidden="true"></i>&nbsp;&nbsp;  
					<i class="fa fa-linkedin" aria-hidden="true"></i>&nbsp;&nbsp;    
					<i class="fa fa-google-plus" aria-hidden="true"></i>&nbsp;&nbsp;    
					<i class="fa fa-pinterest" aria-hidden="true"></i>&nbsp;&nbsp;    
					<i class="fa fa-instagram" aria-hidden="true"></i>&nbsp;&nbsp;    	
		</td>
        <td></td>
        <td></td>
     </tr> 
     
	<?php if(in_array( 'dynamic-user-directory-custom-sort-fld/dynamic-user-directory-custom-sort-fld.php' , $dud_plugin_list )) { ?>
		<tr>
			<td><b><span style='color:#08788c;'>SORT FIELD</b></td>
			<td></td>
			<td>Select which field the directory should be sorted on.</td>
			<td><input name="<?php echo $dud_option_name;?>[ud_social]" id="ud_social" type="hidden" value="<?php echo $dud_options['ud_social'];?>"/> </td>
		</tr> 
		<tr>
			<td><b>Custom Sort Field</b></td>
			<td><input type="text" id="ud_sort_fld_key" name="<?php echo $dud_option_name;?>[ud_sort_fld_key]" 
				value="<?php echo !empty($dud_options['ud_sort_fld_key']) ? esc_attr( $dud_options['ud_sort_fld_key'] ) : ""; ?>" maxlength="50"/></td>
			<td>Enter the meta key name of the meta field you would like to sort the directory on.</td>
			<td></td>
		</tr> 	
		 
		<tr>
			<td><b>Custom Sort Field Type</b></td>
			<td>
			   <select class="dd-menu-no-chk-box-width" name="<?php echo $dud_option_name;?>[ud_sort_fld_type]" id="ud_sort_fld_type">          
						<OPTION value="">Standard Meta Field</OPTION> 
						<OPTION value="bp" <?php echo ($dud_options['ud_sort_fld_type'] == "bp") ? "SELECTED" : ""; ?>>BuddyPress Custom Field</OPTION> 
						<OPTION value="cimy" <?php echo ($dud_options['ud_sort_fld_type'] == "cimy") ? "SELECTED" : ""; ?>>Cimy Custom Field</OPTION> 
			   </select>
			</td>
			<td>Indicate whether the chosen sort field is from one of the DUD-compatible membership plugins.</td>
			<td></td>
		</tr>
	<?php } ?>
	</table>	
<br/><br/>
</div>
<br/><br/>

<div class="dud-settings-section-header">&nbsp; Layout Settings</div>
<div class="dud-settings-section">
	<table class="form-table">
		<?php if ( in_array( 'dynamic-user-directory-horizontal-layout/dud_horizontal_layout.php' , $dud_plugin_list ) ) 
		{ ?>
			<tr>
				<td colspan="3" style="line-height:22px;"><b>Instructions</b><br><hr>This is where you configure your horizontal directory layout. 
				The directory formatting may be adjusted to achieve the best fit for your data and theme in the following ways: 
				1) Adjust the listing font size (in the "Listings Display Settings" section), 2) Adjust column padding, 3) Adjust the column widths, 
				4) Check the box to split the email address into two lines 5) Adjust the overall directory width, 
				6) Change the column order. It is recommended to place the directory on a full-size page rather than one with a sidebar if you
				have a lot of columns to display. 
				<hr></td>
			</tr>
		<?php } ?>
		<tr>
			<td><b>Display Order</b></td>
			<td>
				<ul id="sortable"> 
				<?php 
				$sort_order_items = dynamic_ud_sort_order_admin( $dud_options['user_directory_sort_order'] );
				foreach ($sort_order_items as $item)
				{ ?> 
					<li class="sort-order-list-item" id="<?php echo esc_attr($item);?>">
						<div class="sort-order-text"><?php echo esc_attr($item);?></div></li>
		 <?php  } ?>
				 </ul> 
				 <input type="hidden" id="user_directory_sort_order" name="<?php echo $dud_option_name;?>[user_directory_sort_order]" 
					 value="<?php echo esc_attr( $dud_options['user_directory_sort_order'] ); ?>" />
			</td>	
			<td>Drag the list items up or down using your mouse to rearrange the display order. Note that the Sort Field (Last Name or Display Name) 
				will always be the first field shown. For horizontal directories, the order from top to bottom will be shown from left to right (you can show directory listings in a horizontal tabular format with the <a href="http://sgcustomwebsolutions.com/wordpress-plugin-development/" target="_blank">Horizontal Layout Add-on</a>).</td>
			<td></td>
	    </tr>
	</table>
	
		 <?php if ( in_array( 'dynamic-user-directory-horizontal-layout/dud_horizontal_layout.php' , $dud_plugin_list ) ) 
		{ ?>			
			<table class="form-table" id="horizontal-width-settings-3">	 
				<tr id="ud_directory_width">
					<td><b>Directory Width</b></td>
					<td><input class="dd-menu-chk-box-width" type="text" size="3" maxlength="3" id="ud_dir_width" name="<?php echo $dud_option_name;?>[ud_dir_width]" 
							value="<?php echo !empty($dud_options['ud_dir_width']) ? esc_attr( $dud_options['ud_dir_width'] ) : 100; ?>" /> %</td>
					<td>The overall width (in percentage format) of your horizontal directory.</td>
					<td></td>
				</tr>
				
				<tr>
					<td><div id="stripes-n-header-checkboxes-2"><b>Show Row Stripes</b>&nbsp;&nbsp;<input name="<?php echo $dud_option_name;?>[ud_show_table_stripes]" id="ud_show_table_stripes" type="checkbox" value="1" 
						<?php if(!empty($dud_options['ud_show_table_stripes'])) { checked( '1', $dud_options['ud_show_table_stripes'] ); } ?> /></div></td>
					<td> <div id="divider-colors"><b>Row Stripes Color</b></div>
					   <input type="text" name="<?php echo $dud_option_name;?>[ud_table_stripe_color]" 
							value="<?php echo !empty($dud_options['ud_table_stripe_color']) ? esc_attr( $dud_options['ud_table_stripe_color'] ) : ""; ?>" class="cpa-color-picker"></td>
					<td>Display alternating table row stripes of the selected color (a very light shade is recommended).</td>
					<td></td>
				</tr>	
		  </table>
		  
		  <table class="form-table" id="horizontal-width-settings-4">
			<tr>
				<td><div id="stripes-n-header-checkboxes-1"><b>Show Heading Labels</b>&nbsp;&nbsp;<input name="<?php echo $dud_option_name;?>[ud_show_heading_labels]" id="ud_show_heading_labels" type="checkbox" value="1" 
					<?php if(!empty($dud_options['ud_show_heading_labels'])) { checked( '1', $dud_options['ud_show_heading_labels'] ); } ?> /></div></td>
				<td><div id="top"><b>Heading Labels Font Size</b></div><br>
					<input type="text" size="9" maxlength="2" id="ud_heading_fs" name="<?php echo $dud_option_name;?>[ud_heading_fs]" 
						value="<?php echo !empty($dud_options['ud_heading_fs']) ? esc_attr( $dud_options['ud_heading_fs'] ) : ""; ?>" /> px</td>
				<td>Display meta field labels as column headings on your horizontal directory. Note: For single page directories with letter dividers, the column headings will be reprinted under each letter divider.</td>
				<td></td>
			 </tr>
			 
			<tr id="col_width_name">
				<td><b>User Name Col Width</b><br><input class="dd-menu-chk-box-width" type="text" id="ud_col_width_name" name="<?php echo $dud_option_name;?>[ud_col_width_name]" 
					value="<?php echo !empty($dud_options['ud_col_width_name']) ? esc_attr( $dud_options['ud_col_width_name'] ) : ""; ?>" size="2" maxlength="2"/> %</td>
				<td><div id="user_name_label"><b>User Name Heading Label</b><br><input class="dd-menu-chk-box-width" type="text" id="ud_col_label_name" name="<?php echo $dud_option_name;?>[ud_col_label_name]" 
					value="<?php echo !empty($dud_options['ud_col_label_name']) ? esc_attr( $dud_options['ud_col_label_name'] ) : ""; ?>" size="2" maxlength="40"/></div></td>
				<td><u>Col Width</u>: the width of each column in percentage format. The total sum of the column widths should not exceed 100%, regardless of the directory width. Default widths will be used for any column left blank.</td>
				<td></td>
			</tr>
			
			<tr id="col_width_email">
				<td><b>Email Col Width</b><br><input class="dd-menu-chk-box-width" type="text" id="ud_col_width_email" name="<?php echo $dud_option_name;?>[ud_col_width_email]" 
					value="<?php echo !empty($dud_options['ud_col_width_email']) ? esc_attr( $dud_options['ud_col_width_email'] ) : ""; ?>" size="2" maxlength="2"/> %</td>
				<td><div id="email_label"><b>Email Heading Label</b><br><input class="dd-menu-chk-box-width" type="text" id="ud_col_label_email" name="<?php echo $dud_option_name;?>[ud_col_label_email]" 
					value="<?php echo !empty($dud_options['ud_col_label_email']) ? esc_attr( $dud_options['ud_col_label_email'] ) : ""; ?>" size="2" maxlength="40"/></div></td>
				<td></td>
				<td></td>
			</tr>
			
			<tr id="col_width_website">
				<td><b>Website Col Width</b><br><input class="dd-menu-chk-box-width" type="text" id="ud_col_width_website" name="<?php echo $dud_option_name;?>[ud_col_width_website]" 
					value="<?php echo !empty($dud_options['ud_col_width_website']) ? esc_attr( $dud_options['ud_col_width_website'] ) : ""; ?>" size="2" maxlength="2"/> %</td>
				<td><div id="website_label"><b>Website Heading Label</b><br><input class="dd-menu-chk-box-width" type="text" id="ud_col_label_website" name="<?php echo $dud_option_name;?>[ud_col_label_website]" 
					value="<?php echo !empty($dud_options['ud_col_label_website']) ? esc_attr( $dud_options['ud_col_label_website'] ) : ""; ?>" size="2" maxlength="40"/></div></td>
				<td></td>
				<td></td>
			</tr>
			
			<tr id="col_width_address">
				<td><b>Address Col Width</b><br><input class="dd-menu-chk-box-width" type="text" id="ud_col_width_address" name="<?php echo $dud_option_name;?>[ud_col_width_address]" 
					value="<?php echo !empty($dud_options['ud_col_width_address']) ? esc_attr( $dud_options['ud_col_width_address'] ) : ""; ?>" size="2" maxlength="2"/> %</td>
				<td><div id="address_label"><b>Address Heading Label</b><br><input class="dd-menu-chk-box-width" type="text" id="ud_col_label_address" name="<?php echo $dud_option_name;?>[ud_col_label_address]" 
					value="<?php echo !empty($dud_options['ud_col_label_address']) ? esc_attr( $dud_options['ud_col_label_address'] ) : ""; ?>" size="2" maxlength="40"/></div></td>
				<td></td>
				<td></td>
			</tr>
			
			<tr id="col_width_social">
				<td><b>Social Col Width</b><br><input class="dd-menu-chk-box-width" type="text" id="ud_col_width_social" name="<?php echo $dud_option_name;?>[ud_col_width_social]" 
					value="<?php echo !empty($dud_options['ud_col_width_social']) ? esc_attr( $dud_options['ud_col_width_social'] ) : ""; ?>" size="2" maxlength="2"/> %</td>
				<td><div id="social_label"><b>Social Heading Label</b><br><input class="dd-menu-chk-box-width" type="text" id="ud_col_label_social" name="<?php echo $dud_option_name;?>[ud_col_label_social]" 
					value="<?php echo !empty($dud_options['ud_col_label_social']) ? esc_attr( $dud_options['ud_col_label_social'] ) : ""; ?>" size="2" maxlength="40"/></div></td>
				<td></td>
				<td></td>
			</tr>
			
			<?php 
				for($inc = 1; $inc < 11; $inc++)
				{ ?> 
					 <tr id="col_width_<?php echo $inc; ?>">
						<td><b>Meta Key <?php echo $inc; ?> Col Width</b><br><input class="dd-menu-chk-box-width" type="text" id="ud_col_width_<?php echo $inc; ?>" name="<?php echo $dud_option_name;?>[ud_col_width_<?php echo $inc; ?>]" 
							value="<?php echo !empty($dud_options['ud_col_width_' . $inc]) ? esc_attr( $dud_options['ud_col_width_' . $inc]) : ""; ?>" size="2" maxlength="2" /> %</td>
						<td><div id="col_meta_label_<?php echo $inc; ?>"><b>Meta Field Label <?php echo $inc; ?></b><br><input type="text" id="ud_col_meta_label_<?php echo $inc; ?>" name="<?php echo $dud_option_name;?>[ud_col_meta_label_<?php echo $inc; ?>]" 
							value="<?php echo !empty($dud_options['ud_col_meta_label_' . $inc]) ? esc_attr( $dud_options['ud_col_meta_label_' . $inc] ) : ""; ?>" maxlength="50"/>  
						</div></td>
						<td></td>
						<td></td>	
					 </tr>
		  <?php } ?> <!--end for loop-->
		    <tr>
				<td><div id="top"><b>Col Padding Top</b></div><br>
				
				<?php {
					  $col_padding_top = $dud_options['ud_table_cell_padding_top'];
					  $col_padding_bottom = $dud_options['ud_table_cell_padding_bottom'];
				} ?>
					<input type="text" size="9" maxlength="2" id="ud_table_cell_padding_top" name="<?php echo $dud_option_name;?>[ud_table_cell_padding_top]" 
						value="<?php echo !empty($col_padding_top) || $col_padding_top === "0" ? esc_attr( $dud_options['ud_table_cell_padding_top'] ) : ""; ?>" /> px
				</td>
				<td><div id="top"><b>Col Padding Bottom</b></div><BR>
					<input type="text" size="9" maxlength="2" id="ud_table_cell_padding_bottom" name="<?php echo $dud_option_name;?>[ud_table_cell_padding_bottom]" 
						value="<?php echo !empty($col_padding_bottom) || $col_padding_bottom === "0" ? esc_attr( $dud_options['ud_table_cell_padding_bottom'] ) : ""; ?>" /> px
				</td>
				<td>How much space (in pixels) to pad around the top and bottom of each column in the horizontal display.</td>
				<td></td>
			</tr>
			<?php {
				  $col_padding_left = $dud_options['ud_table_cell_padding_left'];
			      $col_padding_right = $dud_options['ud_table_cell_padding_right']; 
			} ?>
			<tr>
				<td><div id="top"><b>Col Padding Left</b></div><br>
					<input type="text" size="9" maxlength="2" id="ud_table_cell_padding_left" name="<?php echo $dud_option_name;?>[ud_table_cell_padding_left]" 
						value="<?php echo !empty($col_padding_left) || $col_padding_left === "0" ? esc_attr( $dud_options['ud_table_cell_padding_left'] ) : ""; ?>" /> px
				</td>
				<td><div id="top"><b>Col Padding Right</b></div><BR>
					<input type="text" size="9" maxlength="2" id="ud_table_cell_padding_right" name="<?php echo $dud_option_name;?>[ud_table_cell_padding_right]" 
						value="<?php echo !empty($col_padding_right) || $col_padding_right === "0" ? esc_attr( $dud_options['ud_table_cell_padding_right'] ) : ""; ?>" /> px
				</td>
				<td>How much space (in pixels) to pad around the left and right of each column in the horizontal display.</td>
				<td></td>
			</tr>
			
			<tr>
				<td><b>Split Email Into 2 Lines</b>&nbsp;&nbsp;</td>
				<td><input name="<?php echo $dud_option_name;?>[ud_break_email]" id="ud_break_email" type="checkbox" value="1" 
					<?php if(!empty($dud_options['ud_break_email'])) { checked( '1', $dud_options['ud_break_email'] ); } ?> /></td>
				<td>Check this box to conserve space in the email address column by neatly formatting it into two lines. E.g."JohnathanDoe@longwebsitename.com" will be shown as<br><br> JohnathanDoe<br>@longwebsitename.com</td>
				<td></td>
			</tr>	
			<tr>
				<td><b>Responsive Styling for 601px - 767px</b></td>
				<td>
					<input type="radio" name="<?php echo $dud_option_name;?>[ud_horizontal_responsive_601_767]" 
						value="fixed" <?php if(!empty($dud_options['ud_horizontal_responsive_601_767'])) { checked( 'fixed', $dud_options['ud_horizontal_responsive_601_767'] ); } else {checked( 'fixed', 'fixed' );} ?> />Horizontal fixed width table<br>         	
					<input type="radio" name="<?php echo $dud_option_name;?>[ud_horizontal_responsive_601_767]" 
						value="vertical" <?php if(!empty($dud_options['ud_horizontal_responsive_601_767'])) { checked( 'vertical', $dud_options['ud_horizontal_responsive_601_767'] ); } ?> />Vertical Layout</td>
				<td>The default fixed width table ensures that all columns in your horizontal directory are shown legibly at smaller screen sizes. However, this makes all column widths equal and data will be pushed downward as columns narrow. This may not be visually appealing if you have a lot of columns. In this case you can choose "vertical layout" for these screen sizes.  </td>
				<td></td>
			 </tr> 
			 <tr>
				<td><b>Responsive Styling for 768px - 1024px</b></td>
				<td>
					<input type="radio" name="<?php echo $dud_option_name;?>[ud_horizontal_responsive_768_1024]" 
						value="fixed" <?php if(!empty($dud_options['ud_horizontal_responsive_768_1024'])) { checked( 'fixed', $dud_options['ud_horizontal_responsive_768_1024'] ); } else {checked( 'fixed', 'fixed' );} ?> />Horizontal fixed width table<br>         	
					<input type="radio" name="<?php echo $dud_option_name;?>[ud_horizontal_responsive_768_1024]" 
						value="vertical" <?php if(!empty($dud_options['ud_horizontal_responsive_768_1024'])) { checked( 'vertical', $dud_options['ud_horizontal_responsive_768_1024'] ); } ?> />Vertical Layout</td>
				<td></td>
				<td></td>
			 </tr> 		
		  </table>
		 
  <?php } ?>
		
<br/><br/>
</div>

<?php if ( in_array( 'dynamic-user-directory-meta-flds-srch/dud_meta_flds_srch.php' , $dud_plugin_list ) ) 
 { ?>
<br/><br/>
<div class="dud-settings-section-header">&nbsp; Meta Fields Search Settings</div>
<div class="dud-settings-section">
	<table class="form-table">
            <tr>
				<td colspan="3" style="line-height:22px;"><b>Instructions</b><br><hr>Enter up to fifteen user meta search fields in addition to the last name/display name. If there is only one total search field, the label for that field will appear as placeholder text in the search input box at the top of your directory. 
				If there are two or more total search fields, the labels for these fields will be shown in a dropdown box next to the search input box at the top of your directory. Note: search fields will only be displayed if the "Directory Search" box is checked (located in the "Main Directory Settings" section).<hr></td>
			</tr>
						
			<tr>
				<td><b>Last Name / Display Name</b></td>
				<td>
					<select class="dd-menu-no-chk-box-width" name="<?php echo $dud_option_name;?>[ud_show_last_name_srch_fld]" id="ud_show_last_name_srch_fld">
						<OPTION value="first" <?php echo (!empty($dud_options['ud_show_last_name_srch_fld']) && $dud_options['ud_show_last_name_srch_fld'] == "first") ? "SELECTED" : ""; ?>>Show</OPTION> 
						<OPTION value="never" <?php echo (!empty($dud_options['ud_show_last_name_srch_fld']) && $dud_options['ud_show_last_name_srch_fld'] == "never") ? "SELECTED" : ""; ?>>Hide</OPTION> 
					</select> 
				</td>
				<td>Show or hide the Last Name / Display Name (based on your Sort Field setting) as a search field.</td>
				<td></td>
			</tr>
			
			<tr>
				<td><b><span style='color:#08788c;'>SEARCH META FIELDS</span></b></td>
				<td>
					<select class="dd-menu-no-chk-box-width" name="<?php echo $dud_option_name;?>[user_directory_num_meta_srch_flds]" id="user_directory_num_meta_srch_flds">
						<OPTION value="1" <?php echo (!empty($dud_options['user_directory_num_meta_srch_flds']) && $dud_options['user_directory_num_meta_srch_flds'] == "1") ? "SELECTED" : ""; ?>>1</OPTION> 
						<OPTION value="2" <?php echo (!empty($dud_options['user_directory_num_meta_srch_flds']) && $dud_options['user_directory_num_meta_srch_flds'] == "2") ? "SELECTED" : ""; ?>>2</OPTION> 
						<OPTION value="3" <?php echo (!empty($dud_options['user_directory_num_meta_srch_flds']) && $dud_options['user_directory_num_meta_srch_flds'] == "3") ? "SELECTED" : ""; ?>>3</OPTION> 
						<OPTION value="4" <?php echo (!empty($dud_options['user_directory_num_meta_srch_flds']) && $dud_options['user_directory_num_meta_srch_flds'] == "4") ? "SELECTED" : ""; ?>>4</OPTION> 
						<OPTION value="5" <?php echo (!empty($dud_options['user_directory_num_meta_srch_flds']) && $dud_options['user_directory_num_meta_srch_flds'] == "5") ? "SELECTED" : ""; ?>>5</OPTION> 
						<OPTION value="6" <?php echo (!empty($dud_options['user_directory_num_meta_srch_flds']) && $dud_options['user_directory_num_meta_srch_flds'] == "6") ? "SELECTED" : ""; ?>>6</OPTION>
						<OPTION value="7" <?php echo (!empty($dud_options['user_directory_num_meta_srch_flds']) && $dud_options['user_directory_num_meta_srch_flds'] == "7") ? "SELECTED" : ""; ?>>7</OPTION>
						<OPTION value="8" <?php echo (!empty($dud_options['user_directory_num_meta_srch_flds']) && $dud_options['user_directory_num_meta_srch_flds'] == "8") ? "SELECTED" : ""; ?>>8</OPTION>
						<OPTION value="9" <?php echo (!empty($dud_options['user_directory_num_meta_srch_flds']) && $dud_options['user_directory_num_meta_srch_flds'] == "9") ? "SELECTED" : ""; ?>>9</OPTION>
						<OPTION value="10" <?php echo (!empty($dud_options['user_directory_num_meta_srch_flds']) && $dud_options['user_directory_num_meta_srch_flds'] == "10") ? "SELECTED" : ""; ?>>10</OPTION> 
						<OPTION value="11" <?php echo (!empty($dud_options['user_directory_num_meta_srch_flds']) && $dud_options['user_directory_num_meta_srch_flds'] == "11") ? "SELECTED" : ""; ?>>11</OPTION> 
						<OPTION value="12" <?php echo (!empty($dud_options['user_directory_num_meta_srch_flds']) && $dud_options['user_directory_num_meta_srch_flds'] == "12") ? "SELECTED" : ""; ?>>12</OPTION> 
						<OPTION value="13" <?php echo (!empty($dud_options['user_directory_num_meta_srch_flds']) && $dud_options['user_directory_num_meta_srch_flds'] == "13") ? "SELECTED" : ""; ?>>13</OPTION> 
						<OPTION value="14" <?php echo (!empty($dud_options['user_directory_num_meta_srch_flds']) && $dud_options['user_directory_num_meta_srch_flds'] == "14") ? "SELECTED" : ""; ?>>14</OPTION> 
						<OPTION value="15" <?php echo (!empty($dud_options['user_directory_num_meta_srch_flds']) && $dud_options['user_directory_num_meta_srch_flds'] == "15") ? "SELECTED" : ""; ?>>15</OPTION> 
					</select> 
				</td>
				<td>Select the number of meta fields you want to permit users to search on.</td>
				<td></td>
			</tr>	
		 
			<?php 
				for($inc = 1; $inc < 16; $inc++)
				{
					$dud_srch_fld_name =   !empty($dud_options['user_directory_meta_srch_field_' . $inc]) ? $dud_options['user_directory_meta_srch_field_' . $inc] : null;
					$dud_srch_fld_label =  !empty($dud_options['user_directory_meta_srch_label_' . $inc]) ? $dud_options['user_directory_meta_srch_label_' . $inc] : null;
					$dud_cimy_flag =       !empty($dud_options['ud_meta_srch_cimy_flag_' . $inc]) ? $dud_options['ud_meta_srch_cimy_flag_' . $inc] : null;
					$dud_bp_flag =         !empty($dud_options['ud_meta_srch_bp_flag_' . $inc]) ? $dud_options['ud_meta_srch_bp_flag_' . $inc] : null;
				?> 
					<tr id="meta_srch_fld_<?php echo $inc; ?>">
						<td><b>Meta Key Name <?php echo $inc; ?></b><br><input class="meta-flds-srch-key-input" type="text" id="user_directory_meta_srch_field_<?php echo $inc; ?>" 
							name="<?php echo $dud_option_name;?>[user_directory_meta_srch_field_<?php echo $inc; ?>]" value="<?php echo esc_attr( $dud_srch_fld_name ); ?>" maxlength="75"/></td>
						<td><b>Meta Field Label <?php echo $inc; ?></b><br><input class="meta-flds-srch-label-input" type="text" id="user_directory_meta_srch_label_<?php echo $inc; ?>" name="<?php echo $dud_option_name;?>[user_directory_meta_srch_label_<?php echo $inc; ?>]" 
							value="<?php echo esc_attr( $dud_srch_fld_label ); ?>" maxlength="75"/>
							<input type="hidden" id="ud_meta_srch_cimy_flag_<?php echo $inc; ?>" name="<?php echo $dud_option_name;?>[ud_meta_srch_cimy_flag_<?php echo $inc; ?>]" 
								value="<?php echo esc_attr( $dud_cimy_flag ); ?>" maxlength="2"/>
							<input type="hidden" id="ud_meta_srch_bp_flag_<?php echo $inc; ?>" name="<?php echo $dud_option_name;?>[ud_meta_srch_bp_flag_<?php echo $inc; ?>]" 
								value="<?php echo esc_attr( $dud_bp_flag ); ?>" maxlength="2"/></td>
						
						<?php if($inc === 1) { ?>
							<td>Each Search Meta Key Name should match one in your existing directory. The Search Meta Field Labels will be displayed as options in a dropdown box in the order you entered them here.</td>
						<?php } else { ?>
							<td></td>
						<?php } ?>
						
						<td></td>
						
					</tr>
		    <?php } ?>
				
				 <tr id="show_srch_results">
					<td><b>Show Search Results</b></td>
					<td>
						<select class="dd-menu-no-chk-box-width" name="<?php echo $dud_option_name;?>[ud_show_srch_results]" id="ud_show_srch_results">
							<OPTION value="alpha-links" <?php echo (!empty($dud_options['ud_show_srch_results']) && $dud_options['ud_show_srch_results'] == "alpha-links") ? "SELECTED" : ""; ?>>Letter Links Format</OPTION>
							<OPTION value="single-page" <?php echo (!empty($dud_options['ud_show_srch_results']) && $dud_options['ud_show_srch_results'] == "single-page") ? "SELECTED" : ""; ?>>Single Page Format</OPTION> 
						</select> 
					</td>
					<td>The search results may be displayed either on a single page or by alphabet letter links. If page load time is an issue, select 'Letter Links Format' for improved performance.</td>
					<td></td>
				 </tr>
			 <tr>
				<td><b>Search Icon Color</b></td>
				<td>
					<select class="dd-menu-no-chk-box-width" name="<?php echo $dud_option_name;?>[ud_srch_icon_color]" id="ud_srch_icon_color">
						<OPTION value="dimgray" <?php echo (!empty($dud_options['ud_srch_icon_color']) && $dud_options['ud_srch_icon_color'] == "DimGray") ? "SELECTED" : ""; ?>>DimGray</OPTION> 
						<OPTION value="white" <?php echo (!empty($dud_options['ud_srch_icon_color']) && $dud_options['ud_srch_icon_color'] == "white") ? "SELECTED" : ""; ?>>White</OPTION> 
					</select> 
				</td>
				<td>Choose the color of the magnifying glass icon on the Search button.</td>
				<td></td>
			 </tr>
			 <tr>
				<td><b>Show 'Clear' link</b></td>
				<td><input name="<?php echo $dud_option_name;?>[ud_clear_search]" id="ud_clear_search" type="checkbox" 
				   value="1" <?php if(!empty($dud_options['ud_clear_search'])) { checked( '1', $dud_options['ud_clear_search'] ); } ?> />
				</td>
				<td>Check this box to show a 'Clear' link next to the search box. This provides an easy way to clear the search box and refresh the directory.</td>
				<td></td>
			 </tr>	
	</table>	
<br/><br/>
</div>
<?php } ?>
<script type="text/javascript">jQuery(document).ready(function() {jQuery('.js-example-basic-multiple').select2();});</script>    
<?php submit_button('Save options', 'primary', 'user_directory_options_submit'); ?>

 </form>
</div>
<?php
}

/*** Settings Link on Plugin Management Screen ************************************/

function user_directory_settings_link($actions, $file) {

if(false !== strpos($file, 'user-directory'))
 $actions['settings'] = '<a href="options-general.php?page=user_directory">Settings</a>';
return $actions; 
}
add_filter('plugin_action_links', 'user_directory_settings_link', 2, 2);

/*** Register Settings on Page Init ***********************************************/

function user_directory_settings_init(){
	
	$dud_plugin_list = get_option('active_plugins');

	if ( in_array( 'dynamic-user-directory-multiple-dirs/dynamic-user-directory-multiple-dirs.php' , $dud_plugin_list )) 
		do_action('dud_register_loaded_directory_setting');
	else
		register_setting( 'user_directory_options', 'dud_plugin_settings', 'dynamic_ud_validate');
	
}
add_action('admin_init', 'user_directory_settings_init');

/*** Validation Functions ***********************************************************/ 

function dynamic_ud_validate( $input ) 
{
    //var_dump($_POST);
    $dud_option_name = 'dud_plugin_settings';
	$dud_plugin_list = get_option('active_plugins');
	$found_error = false;
		
	if ( in_array( 'dynamic-user-directory-multiple-dirs/dynamic-user-directory-multiple-dirs.php' , $dud_plugin_list )) 
	{
		
		if(!empty($_POST['dud_plugin_settings_1'])) $dud_option_name = 'dud_plugin_settings_1';
		else if(!empty($_POST['dud_plugin_settings_2'])) $dud_option_name = 'dud_plugin_settings_2';
		else if(!empty($_POST['dud_plugin_settings_3'])) $dud_option_name = 'dud_plugin_settings_3';
		else if(!empty($_POST['dud_plugin_settings_4'])) $dud_option_name = 'dud_plugin_settings_4';
		else if(!empty($_POST['dud_plugin_settings_5'])) $dud_option_name = 'dud_plugin_settings_5';
				
		add_option('dud_updated_settings', $dud_option_name  );
	}
	
    $input['user_directory_border_color'] = dynamic_ud_validate_hex( $input['user_directory_border_color'], $dud_option_name );
    if($input['user_directory_border_color'] === null) return get_option( $dud_option_name );

    $input['ud_letter_divider_font_color'] = dynamic_ud_validate_hex( $input['ud_letter_divider_font_color'], $dud_option_name );
    if($input['ud_letter_divider_font_color'] === null) return get_option( $dud_option_name );
    
    $input['ud_letter_divider_fill_color'] = dynamic_ud_validate_hex( $input['ud_letter_divider_fill_color'], $dud_option_name );
    if($input['ud_letter_divider_fill_color'] === null) return get_option( $dud_option_name );
	
	if(in_array( 'dynamic-user-directory-horizontal-layout/dud_horizontal_layout.php' , $dud_plugin_list ) && $input['ud_display_listings'] === 'horizontally')
	{
		$input['ud_col_label_name']    = sanitize_text_field($input['ud_col_label_name']);
		$input['ud_col_label_address'] = sanitize_text_field($input['ud_col_label_address']);
		$input['ud_col_label_email']   = sanitize_text_field($input['ud_col_label_email']);
		$input['ud_col_label_website'] = sanitize_text_field($input['ud_col_label_website']);
		$input['ud_col_label_social']  = sanitize_text_field($input['ud_col_label_social']);
		
		for($inc = 1; $inc < 11; $inc++)
		{ 
			$input['ud_col_width_' . $inc]      = sanitize_text_field($input['ud_col_width_' . $inc]);
			$input['ud_col_meta_label_' . $inc] = sanitize_text_field($input['ud_col_meta_label_' . $inc]);
		}	
		
		$input['ud_col_width_name']    = sanitize_text_field($input['ud_col_width_name']);
		$input['ud_col_width_address'] = sanitize_text_field($input['ud_col_width_address']);
		$input['ud_col_width_email']   = sanitize_text_field($input['ud_col_width_email']);
		$input['ud_col_width_website'] = sanitize_text_field($input['ud_col_width_website']);
		$input['ud_col_width_social']  = sanitize_text_field($input['ud_col_width_social']);
		
		$input['ud_facebook']          = sanitize_text_field($input['ud_facebook']);
		$input['ud_twitter']           = sanitize_text_field($input['ud_twitter']);
		$input['ud_linkedin']          = sanitize_text_field($input['ud_linkedin']);
		$input['ud_google']            = sanitize_text_field($input['ud_google']);
		$input['ud_instagram']         = sanitize_text_field($input['ud_instagram']);
		$input['ud_pinterest']         = sanitize_text_field($input['ud_pinterest']);
		
		if(!empty($input['ud_show_table_stripes']))
		{
			$input['user_directory_listing_spacing'] = "0";
		}		
		
		if(!empty($input['ud_table_stripe_color'])) 
		{
			$input['ud_table_stripe_color'] = dynamic_ud_validate_hex( $input['ud_table_stripe_color'], $dud_option_name );
			if($input['ud_table_stripe_color'] === null) return get_option( $dud_option_name );
		}
		if(!empty($input['ud_divider_border_color'])) 
		{
			$input['ud_divider_border_color'] = dynamic_ud_validate_hex( $input['ud_divider_border_color'], $dud_option_name );
			if($input['ud_divider_border_color'] === null) return get_option( $dud_option_name );
		}
		if(!empty($input['ud_divider_font_size'])) 
		{
			$input['ud_divider_font_size'] = dynamic_ud_check_numeric( (!empty($input['ud_divider_font_size']) ? $input['ud_divider_font_size'] : ""), $dud_option_name );
			if($input['ud_divider_font_size'] === null) return get_option( $dud_option_name );
		}
		if(!empty($input['ud_table_cell_padding_top'])) 
		{
			$input['ud_table_cell_padding_top'] = dynamic_ud_check_numeric( $input['ud_table_cell_padding_top'], $dud_option_name );
			if($input['ud_table_cell_padding_top'] === null) return get_option( $dud_option_name );
		}
		if(!empty($input['ud_table_cell_padding_bottom'])) 
		{
			$input['ud_table_cell_padding_bottom'] = dynamic_ud_check_numeric( $input['ud_table_cell_padding_bottom'], $dud_option_name );
			if($input['ud_table_cell_padding_bottom'] === null) return get_option( $dud_option_name );
		}
		if(!empty($input['ud_table_cell_padding_left'])) 
		{
			$input['ud_table_cell_padding_left'] = dynamic_ud_check_numeric( $input['ud_table_cell_padding_left'], $dud_option_name );
			if($input['ud_table_cell_padding_left'] === null) return get_option( $dud_option_name );
		}
		if(!empty($input['ud_table_cell_padding_right'])) 
		{
			$input['ud_table_cell_padding_right'] = dynamic_ud_check_numeric( $input['ud_table_cell_padding_right'], $dud_option_name );
			if($input['ud_table_cell_padding_right'] === null) return get_option( $dud_option_name );
		}
		if(!empty($input['ud_heading_fs']))
		{
			$input['ud_heading_fs'] = dynamic_ud_check_numeric( $input['ud_heading_fs'], $dud_option_name );
			if($input['ud_heading_fs'] === null) return get_option( $dud_option_name );
		}
	} 
	
	if(!empty($input['ud_avatar_padding'])) 
	{
		$input['ud_avatar_padding'] = dynamic_ud_check_numeric( $input['ud_avatar_padding'], $dud_option_name );
		if($input['ud_avatar_padding'] === null) return get_option( $dud_option_name );
	}
	
	if(!empty($input['user_directory_avatar_size']))
	{	
		$input['user_directory_avatar_size'] = dynamic_ud_check_numeric( $input['user_directory_avatar_size'], $dud_option_name );
		if($input['user_directory_avatar_size'] === null) return get_option( $dud_option_name );
	}
			
	if(!empty($input['ud_pagination_font_size']))
	{	
		$input['ud_pagination_font_size'] = dynamic_ud_check_numeric( $input['ud_pagination_font_size'], $dud_option_name );
		if($input['ud_pagination_font_size'] === null) return get_option( $dud_option_name );
	}
	if(!empty($input['ud_users_per_page']))
	{	
		$input['ud_users_per_page'] = dynamic_ud_check_numeric( $input['ud_users_per_page'], $dud_option_name, 'ud_users_per_page' );
		if($input['ud_users_per_page'] === null) return get_option( $dud_option_name );
	}
	if(!empty($input['ud_pagination_link_color'])) 
	{
		$input['ud_pagination_link_color'] = dynamic_ud_validate_hex( $input['ud_pagination_link_color'], $dud_option_name );
		if($input['ud_pagination_link_color'] === null) return get_option( $dud_option_name );
	}
	if(!empty($input['ud_pagination_link_clicked_color'])) 
	{
		$input['ud_pagination_link_clicked_color'] = dynamic_ud_validate_hex( $input['ud_pagination_link_clicked_color'], $dud_option_name );
		if($input['ud_pagination_link_clicked_color'] === null) return get_option( $dud_option_name );
	}
	if(!empty($input['ud_alpha_link_color'])) 
	{
		$input['ud_alpha_link_color'] = dynamic_ud_validate_hex( $input['ud_alpha_link_color'], $dud_option_name );
		if($input['ud_alpha_link_color'] === null) return get_option( $dud_option_name );
	}
	if(!empty($input['ud_alpha_link_clicked_color'])) 
	{
		$input['ud_alpha_link_clicked_color'] = dynamic_ud_validate_hex( $input['ud_alpha_link_clicked_color'], $dud_option_name );
		if($input['ud_alpha_link_clicked_color'] === null) return get_option( $dud_option_name );
	}
		
    $input['user_directory_letter_fs'] = dynamic_ud_check_numeric( $input['user_directory_letter_fs'], $dud_option_name );
    if($input['user_directory_letter_fs'] === null) return get_option( $dud_option_name );
    
    $input['ud_alpha_link_spacer'] = dynamic_ud_check_numeric( $input['ud_alpha_link_spacer'], $dud_option_name );
    if($input['ud_alpha_link_spacer'] === null) return get_option( $dud_option_name );
    
    $input['user_directory_listing_fs'] = dynamic_ud_check_numeric( $input['user_directory_listing_fs'], $dud_option_name );
    if($input['user_directory_listing_fs'] === null) return get_option( $dud_option_name );
    
    $input['user_directory_listing_spacing'] = dynamic_ud_check_numeric( $input['user_directory_listing_spacing'], $dud_option_name );
    if($input['user_directory_listing_spacing'] === null) return get_option( $dud_option_name );
    			
    $input['user_directory_addr_1'] = sanitize_text_field($input['user_directory_addr_1']);
    $input['user_directory_addr_2'] = sanitize_text_field($input['user_directory_addr_2']);
    $input['user_directory_city']   = sanitize_text_field($input['user_directory_city']);
    $input['user_directory_state']  = sanitize_text_field($input['user_directory_state']);
    $input['user_directory_zip']    = sanitize_text_field($input['user_directory_zip']);
    
	for($inc = 1; $inc < 11; $inc++)
	{ 
		$input['user_directory_meta_field_' . $inc] = sanitize_text_field($input['user_directory_meta_field_' . $inc]);
		$input['user_directory_meta_label_' . $inc] = sanitize_text_field($input['user_directory_meta_label_' . $inc]);
		
		if($input['user_directory_meta_label_' . $inc])
		{
			if ($input['user_directory_meta_label_' . $inc][0] === '#')
			{
				if(strlen($input['user_directory_meta_label_' . $inc]) > 1)
					$input['user_directory_meta_label_' . $inc] = substr($input['user_directory_meta_label_' . $inc], 1);
				else
					$input['user_directory_meta_label_' . $inc] = "";
				
				$input['user_directory_meta_link_' . $inc] = '#';
			}
		}
	}	
   
	if ( in_array( 'dynamic-user-directory-meta-flds-srch/dud_meta_flds_srch.php' , $dud_plugin_list ) ) 
	{
		$found_srch_fld = false;
		
		//Clear out the flag fields...
		for($inc = 1; $inc < 16; $inc++)
		{ 
			$input['ud_meta_srch_cimy_flag_'. $inc] = null;
			$input['ud_meta_srch_bp_flag_'. $inc] = null;
		}
		
		//Now determine the new flag fields
		for($inc = 1; $inc < 16; $inc++)
		{ 
			$input['user_directory_meta_srch_field_' . $inc] = sanitize_text_field($input['user_directory_meta_srch_field_' . $inc]);
			$input['user_directory_meta_srch_label_'. $inc] = sanitize_text_field($input['user_directory_meta_srch_label_' . $inc]);
			$input['ud_meta_srch_cimy_flag_'. $inc] = dud_check_cimy_field($input['user_directory_meta_srch_field_' . $inc]);
			$input['ud_meta_srch_bp_flag_'. $inc] = dud_check_bp_field($input['user_directory_meta_srch_field_' . $inc]);		
			
			if($input['user_directory_meta_srch_field_' . $inc] && !$input['user_directory_meta_srch_label_'. $inc])
			{
				add_settings_error( $dud_option_name, 'user_directory_bc_error', 'Please add a label for Meta Search Field ' . $inc, 'error' ); 
				$found_error = true;
			}
			
			if($input['user_directory_meta_srch_field_' . $inc])
				$found_srch_fld = true;
		}	
		
		if(!$found_srch_fld && $input['ud_show_last_name_srch_fld'] === "never")
		{
			add_settings_error( $dud_option_name, 'user_directory_bc_error', 'Please enter at least one Meta Search Field or uncheck the Show Search Box option.', 'error' ); 
			$found_error = true;
		}
	}

    return $input;
}


function dynamic_ud_validate_txt_fld( $input ) {

    if(isset($input))
    {
    	//our text fields will never be larger than 50 characters.
		if(strlen($input) > 50)
			$input = substr( $input, 0, 50 );
		
    	return sanitize_text_field($input);
    }
    
    return $input;
}

function dynamic_ud_validate_hex( $input, $dud_option_name ) {

   if(isset($input))
   {
		if( !dynamic_ud_check_color( sanitize_text_field($input) ) ) 
		{
        	// $setting, $code, $message, $type
       		add_settings_error( $dud_option_name, 'user_directory_bc_error', 'All colors must be a valid hexadecimal value!', 'error' ); 
         
       		return null;
		} 
		else
			return sanitize_text_field($input);
   }  
}

function dynamic_ud_check_color( $value ) { 
     
    if ( preg_match( '/^#[a-f0-9]{6}$/i', $value ) ) { // if user insert a HEX color with #     
        return true;
    }
     
    return false;
}

function dynamic_ud_check_numeric($input, $dud_option_name, $dud_fld_name='') {

	if (!is_numeric($input)) {
		
			if($dud_fld_name === 'ud_users_per_page')
				add_settings_error( $dud_option_name, 'user_directory_fs_error', 'The number of users per page must be a numeric value!', 'error' ); 
		    else
				add_settings_error( $dud_option_name, 'user_directory_fs_error', 'All pixel sizes must be a numeric value!', 'error' ); 
         
        	// Return the previous valid value
       		return null;
	}
	
	//our numeric fields will never be larger than two digits.
	//if(strlen($input) > 3)
	//	$input = substr( $input, 0, 3 );
		
	return sanitize_text_field($input);
}

function dynamic_ud_sort_order_admin( $input ) {
       
     $output = "";
     
     if($input) 
     {
     	 //append the newly added Meta Flds to list
     	 if(strpos($input, 'MetaKey5') === FALSE) $input .= ',MetaKey5'; 
     	 if(strpos($input, 'MetaKey6') === FALSE) $input .= ',MetaKey6'; 
     	 if(strpos($input, 'MetaKey7') === FALSE) $input .= ',MetaKey7'; 
     	 if(strpos($input, 'MetaKey8') === FALSE) $input .= ',MetaKey8'; 
     	 if(strpos($input, 'MetaKey9') === FALSE) $input .= ',MetaKey9'; 
     	 if(strpos($input, 'MetaKey10') === FALSE)$input .= ',MetaKey10'; 
		 if(strpos($input, 'Social') === FALSE)   $input .= ',Social'; 
     	 
         $output = explode(',', $input);  
     }
     else
     {
     	$output = "Address,Social,Email,Website,MetaKey1,MetaKey2,MetaKey3,MetaKey4,MetaKey5,MetaKey6,MetaKey7,MetaKey8,MetaKey9,MetaKey10";
     	$output = explode(',', $output);
     }
     
     return $output;
}

function dynamic_ud_load_meta_keys($meta_type) {

	global $wpdb;
	$list_box = "";
	
	if($meta_type === "cimy" && defined("DUD_CIMY_FIELDS_TABLE")) 
	{
		$results = $wpdb->get_results("SELECT distinct NAME FROM " . DUD_CIMY_FIELDS_TABLE );
		
		if($results)
		{			
			$meta_key_list = "<textarea id='styled' class='dud_meta_keys' style='font-size:15px;line-height:25px;' spellcheck='false' rows='4' cols='40'>";
			
			$list_length = count($results);
			$cnt = 1;	
			
			foreach ($results as $result)
			{ 
				$meta_key_list .= $result->NAME; 
				if($cnt !== $list_length) $meta_key_list .= "\n";
   				$cnt++;
    			}
    				
    			$meta_key_list .= "</textarea>";
    			return $meta_key_list;
    		}
	}
	else if($meta_type === "bp" && defined("DUD_BP_PLUGIN_FIELDS_TABLE")) 
	{
		$results = $wpdb->get_results("SELECT distinct name FROM " . DUD_BP_PLUGIN_FIELDS_TABLE . " where type <> 'option'");
		
		if($results)
		{			
			$meta_key_list = "<textarea id='styled' class='dud_meta_keys' style='font-size:15px;line-height:25px;' spellcheck='false' rows='4' cols='40'>";
			
			$list_length = count($results);
			$cnt = 1;	
			
			foreach ($results as $result)
			{ 
				$meta_key_list .= $result->name; 
				if($cnt !== $list_length) $meta_key_list .= "\n";
   				$cnt++;
    			}
    				
    			$meta_key_list .= "</textarea>";
    			return $meta_key_list;
    		}
	}
	else if($meta_type === "s2m") 
	{		
		$meta_key_list = "<textarea id='styled' class='dud_meta_keys' style='font-size:15px;line-height:25px;' spellcheck='false' rows='4' cols='40'>";
		
		$flds_arr = get_s2member_custom_fields();
		
		if(!empty($flds_arr))
		{
			$list_length = count($flds_arr);
			$cnt = 1;
			
			foreach($flds_arr as $key => $value) {
				$meta_key_list .= $key;
				if($cnt !== $list_length) $meta_key_list .= "\n";
   				$cnt++;
			}
			
			$meta_key_list .= "</textarea>";
			return $meta_key_list;
		}
	}
	else
	{
		$user_meta_key_val_list = array();
		$user_meta_key_list = array();
		
		$results = $wpdb->get_results("SELECT user_id FROM " . $wpdb->prefix . "usermeta ORDER BY RAND() LIMIT 300");
		
		if($results)
		{
		        // Skip known WordPress meta fields that do not apply 
			$skip_me = "last_name*rich_editing*comment_shortcuts*admin_color*use_ssl*show_admin_bar_front
                        		*dismissed_wp_pointers*session_tokens*wp_user-settings*wp_user-settings-time
                        			*default_password_nag*wp_capabilities*wp_user_level*wporg_favorites
                        				*closedpostboxes_dashboard*metaboxhidden_dashboard*meta-box-order_dashboard";
                        		
			foreach ($results as $result)
			{ 		
				$all_meta_for_user = array_map( function( $a ){ return $a[0]; }, get_user_meta( $result->user_id ) );
							
				foreach ($all_meta_for_user as $key => $value) 
				{
					$key_exists = false;
					foreach ($user_meta_key_val_list as $key1 => $value1) 
					{
						if($key === $key1) $key_exists = true;
					}
					
					if(!$key_exists)
					{					 
						$pos = strpos($skip_me, $key);
   					
   						if($pos === false) 
   						{
   							if($value) $user_meta_key_val_list[$key] = $value;						
   							if($value) array_push($user_meta_key_list, $key);
    					}
    				}
				}
			}	
			
			$meta_key_list = "<textarea id='styled' class='dud_meta_keys' style='font-size:15px;line-height:25px;' spellcheck='false' rows='4' cols='40'>";
			
			$list_length = count($user_meta_key_list);
			$cnt = 1;
			
			asort($user_meta_key_list, SORT_STRING | SORT_FLAG_CASE | SORT_NATURAL);

			if($user_meta_key_list) 
			{		
				foreach ($user_meta_key_list as $key2) 
				{			
   					$meta_key_list .= $key2;
   					if($cnt !== $list_length) $meta_key_list .= "\n";
   					$cnt++;
				}
				
				$meta_key_list .= "</textarea>";
				return $meta_key_list;
			}		
		}
	}
	
	return "";
}

function dud_check_cimy_field($fld) {

	global $wpdb;
	
	$dud_plugin_list = get_option('active_plugins');
		
	if ( in_array( 'cimy-user-extra-fields/cimy_user_extra_fields.php' , $dud_plugin_list ) ) 
	{
		if(defined("DUD_CIMY_FIELDS_TABLE")) {
			
			$results = $wpdb->get_results("SELECT distinct NAME FROM " . DUD_CIMY_FIELDS_TABLE . " where NAME = '" . $fld . "'");
			
			if($results)
				return "1";
		}
	}
	
	return "";
}

function dud_check_bp_field($fld) {

	global $wpdb;
	
	$dud_plugin_list = get_option('active_plugins');
    	
	if( function_exists('bp_is_active'))
	{
		if(defined("DUD_BP_PLUGIN_FIELDS_TABLE")) {
			
			$results = $wpdb->get_results("SELECT distinct name FROM " . DUD_BP_PLUGIN_FIELDS_TABLE . " where name = '" . $fld . "'");
			
			if($results)
			{
				return "1";
			}
		}
	}
	
	return "";
}

function dud_check_s2m_field($fld, $fld_type) {

	global $wpdb;
	$dud_plugin_list = get_option('active_plugins');
    	
	if(in_array( 's2member/s2member.php' , $dud_plugin_list ) )
	{
		$s2member_custom_fields = get_s2member_custom_fields();

		foreach ($s2member_custom_fields as $key => $value) 
		{
			if($fld === $key && !$fld_type) return "1";
			else if($fld === $key && $fld_type)
			{
				if(is_array($value)) return "a";
				else return "s";
			}
		}	
	}
	
	return "";
}

function dynamic_ud_roles_listbox($selected_roles_arr, $dud_option_name) 
{
	global $wp_roles;

	$wproles = $wp_roles->get_names();

	$ud_listbox = "<SELECT class='js-example-basic-multiple' style='height:100%;width:98%;font-size:14px;letter-spacing:1px' name='" . $dud_option_name . "[ud_hide_roles][]' size='5' multiple='multiple'>";
		
	foreach($wproles as $role_name)
	{
		$ud_listbox .= "<option value='{$role_name}'";
		
		if($selected_roles_arr){
			if(in_array($role_name, $selected_roles_arr))
				$ud_listbox .= " SELECTED";
		}
				
		$ud_listbox .= ">{$role_name}</option>";
	}	
	
	$ud_listbox .= "</SELECT>";

	return $ud_listbox;
}

function dynamic_ud_users_listbox($selected_users_arr, $dud_option_name) 
{
	global $wpdb;
	$ud_listbox = "";
	$total_users = 0;
	
	$results = $wpdb->get_results("SELECT count(user_id) as total_users from " . $wpdb->prefix . "usermeta WHERE meta_key = 'last_name'");
	
	if($results)
	{
		foreach($results as $result)
		{
			$total_users = $result->total_users;
		}
	}
	
	if($total_users > 1000)
		$results = $wpdb->get_results("SELECT DISTINCT user_login, ID as user_id from " . $wpdb->prefix . "users order by user_login ASC");
	else
		$results = $wpdb->get_results("SELECT DISTINCT user_id from " . $wpdb->prefix . "usermeta WHERE meta_key = 'last_name' order by meta_value");
			
	if($results)
	{           
		$ud_listbox = "<SELECT class='js-example-basic-multiple' style='height:100%;width:98%;font-size:14px;letter-spacing:1px' name='" . $dud_option_name . "[ud_users_exclude_include][]' size='5' multiple='multiple'>";
		
		foreach($results as $result)
		{
			$ud_listbox .= "<option value='{$result->user_id}'";
		
			if($selected_users_arr){
				if(in_array($result->user_id, $selected_users_arr))
					$ud_listbox .= " SELECTED";
			}
			
			if($total_users > 1000)
			{
				$user_login = $result->user_login;
				$ud_listbox .= ">{$user_login}</option>";
			}
			else 
			{
				$user_first_name = get_user_meta($result->user_id, 'first_name', true);
				$user_last_name = get_user_meta($result->user_id, 'last_name', true);
				
				$ud_listbox .= ">{$user_last_name}, {$user_first_name}</option>";
			}
		}
	}	
	else 
		return "";
	
	$ud_listbox .= "</SELECT>";

	return $ud_listbox;
}

/*function my_plugin_notice() {
    $user_id = get_current_user_id();
    if ( !get_user_meta( $user_id, 'dud_horizontal_layout_notice_dismissed' ) && current_user_can('manage_options') )
	{		
		$current_url = esc_url( home_url( '/' ) ) . 'wp-admin/options-general.php?page=user_directory&';
		echo '<div class="notice notice-warning"><p>Dynamic User Directory has a new <a href="http://sgcustomwebsolutions.com/wordpress-plugin-development/" target="_blank">Horizontal Layout Add-on</a> available now!&nbsp;&nbsp;<a href="' . $current_url . 'my-plugin-dismissed">Dismiss</a></p></div>';
	}
}
add_action( 'admin_notices', 'my_plugin_notice' );

function my_plugin_notice_dismissed() {
    $user_id = get_current_user_id();
    if ( isset( $_GET['my-plugin-dismissed'] ) )
        add_user_meta( $user_id, 'dud_horizontal_layout_notice_dismissed', 'true', true );
}
add_action( 'admin_init', 'my_plugin_notice_dismissed' );*/