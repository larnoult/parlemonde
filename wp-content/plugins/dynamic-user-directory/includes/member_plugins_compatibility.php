<?php

/*** CIMY USER EXTRA FIELDS ******************************************************************************************************/
function dud_load_cimy_vals($user_id, $dud_options, $user_directory_meta_flds_tmp)
{
		$dud_meta_addr_flds = array();
		$dud_meta_social_flds = array();
				
		$user_directory_addr_1_op    = !empty($dud_options['user_directory_addr_1']) ? $dud_options['user_directory_addr_1'] : "";
		$user_directory_addr_2_op    = !empty($dud_options['user_directory_addr_2']) ? $dud_options['user_directory_addr_2'] : "";
		$user_directory_city_op      = !empty($dud_options['user_directory_city']) ? $dud_options['user_directory_city'] : "";
		$user_directory_state_op     = !empty($dud_options['user_directory_state']) ? $dud_options['user_directory_state'] : "";
		$user_directory_zip_op       = !empty($dud_options['user_directory_zip']) ? $dud_options['user_directory_zip'] : "";
		$user_directory_country_op   = !empty($dud_options['user_directory_country']) ? $dud_options['user_directory_country'] : "";
		
		$ud_facebook_op    = !empty($dud_options['ud_facebook']) ? $dud_options['ud_facebook'] : "";
		$ud_twitter_op     = !empty($dud_options['ud_twitter']) ? $dud_options['ud_twitter'] : "";
		$ud_linkedin_op    = !empty($dud_options['ud_linkedin']) ? $dud_options['ud_linkedin'] : "";
		$ud_google_op      = !empty($dud_options['ud_google']) ? $dud_options['ud_google'] : "";
		$ud_instagram_op   = !empty($dud_options['ud_instagram']) ? $dud_options['ud_instagram'] : "";
		$ud_pinterest_op   = !empty($dud_options['ud_pinterest']) ? $dud_options['ud_pinterest'] : "";
		
	    $values = dynamic_ud_get_cimy_data($user_id, $user_directory_addr_1_op, $user_directory_addr_2_op, $user_directory_city_op,
	 			$user_directory_state_op, $user_directory_zip_op, $user_directory_country_op, $ud_facebook_op, $ud_twitter_op, $ud_linkedin_op, $ud_google_op, 
				$ud_instagram_op, $ud_pinterest_op, $user_directory_meta_flds_tmp);
	 					
		if($values) 
		{
			foreach ($values as $value)
			{ 
				$meta_name = strtoupper ($value->NAME);
				
				if($value->TYPE === 'avatar') 
					$cimy_avatar_loc = $value->VALUE;	
				
				else if($user_directory_addr_1_op && $meta_name === strtoupper ($user_directory_addr_1_op)) 
					$dud_meta_addr_flds[0] = $value->VALUE;	
					 
				else if($user_directory_addr_2_op && $meta_name === strtoupper ($user_directory_addr_2_op)) 
					$dud_meta_addr_flds[1] = $value->VALUE;	
					  
				else if($user_directory_city_op && $meta_name === strtoupper ($user_directory_city_op)) 
					$dud_meta_addr_flds[2] = $value->VALUE;	
					 
				else if($user_directory_state_op && $meta_name === strtoupper ($user_directory_state_op)) 
					$dud_meta_addr_flds[3] = $value->VALUE;	
					 
				else if($user_directory_zip_op && $meta_name === strtoupper ($user_directory_zip_op)) 
					$dud_meta_addr_flds[4] = $value->VALUE;	
				
				else if($user_directory_country_op && $meta_name === strtoupper ($user_directory_country_op)) 
					$dud_meta_addr_flds[5] = $value->VALUE;	
								
				else if($ud_facebook_op && $meta_name === strtoupper ($ud_facebook_op)) 
					$dud_meta_social_flds[0] = $value->VALUE;	
				
				else if($ud_twitter_op && $meta_name === strtoupper ($ud_twitter_op)) 
					$dud_meta_social_flds[1] = $value->VALUE;
				
				else if($ud_linkedin_op && $meta_name === strtoupper ($ud_linkedin_op)) 
					$dud_meta_social_flds[2] = $value->VALUE;	
				
				else if($ud_google_op && $meta_name === strtoupper ($ud_google_op)) 
					$dud_meta_social_flds[3] = $value->VALUE;	
				
				else if($ud_pinterest_op && $meta_name === strtoupper ($ud_pinterest_op)) 
					$dud_meta_social_flds[4] = $value->VALUE;	
				
				else if($ud_instagram_op && $meta_name === strtoupper ($ud_instagram_op)) 
					$dud_meta_social_flds[5] = $value->VALUE;
				
				else
				{					
					for($inc=0; $inc < sizeof($user_directory_meta_flds_tmp); $inc++ ) 
					{
						if($user_directory_meta_flds_tmp[$inc]['field'] 
							&& $meta_name === strtoupper ($user_directory_meta_flds_tmp[$inc]['field'])) 
						{
							$user_directory_meta_flds_tmp[$inc]['value'] 
								= dynamic_ud_format_meta_val($value->VALUE, $dud_options, $user_directory_meta_flds_tmp[$inc]['format']); 
								
							//currently Cimy doesn't appear to store arrays but we are ready if/when it does
							/*if(strlen($value->VALUE) > 2 && substr($value->VALUE, 0, 2) === "a:")
								$user_directory_meta_flds_tmp[$inc]['value'] =  implode(", ",(unserialize(stripslashes($value->VALUE))));	// json_decode() ?
							else
								$user_directory_meta_flds_tmp[$inc]['value'] =  dynamic_ud_parse_meta_val(stripslashes($value->VALUE));
							*/
							break;
						}						
					}
				}				 
			}			
		}

		if(!empty($dud_meta_addr_flds))
		{
			$end_of_array = sizeof($user_directory_meta_flds_tmp);
			
			$user_directory_meta_flds_tmp[$end_of_array]['field'] = "CIMY_ADDRESS";
			$user_directory_meta_flds_tmp[$end_of_array]['value'] = $dud_meta_addr_flds;	
		}

		if(!empty($dud_meta_social_flds))
		{
			$end_of_array = sizeof($user_directory_meta_flds_tmp);
			
			$user_directory_meta_flds_tmp[$end_of_array]['field'] = "CIMY_SOCIAL";
			$user_directory_meta_flds_tmp[$end_of_array]['value'] = $dud_meta_social_flds;	
		}			

		return $user_directory_meta_flds_tmp;
}

/*** Builds Cimy Tables Query Based on Dynamic User Directory Key Name Settings ***/
function dynamic_ud_get_cimy_data($id, $user_directory_addr_1_op, $user_directory_addr_2_op, $user_directory_city_op,
	 			$user_directory_state_op, $user_directory_zip_op, $user_directory_country_op, $ud_facebook_op, $ud_twitter_op, $ud_linkedin_op, $ud_google_op, 
				$ud_instagram_op, $ud_pinterest_op, $user_directory_meta_flds_tmp)
{
	global $wpdb;
	global $dynamic_ud_debug;
	
	if(defined("DUD_CIMY_DATA_TABLE") && defined("DUD_CIMY_FIELDS_TABLE"))
	{
		$ud_sql = "SELECT data.VALUE, efields.NAME, efields.TYPE FROM " . DUD_CIMY_DATA_TABLE . " as data JOIN " . DUD_CIMY_FIELDS_TABLE . 
				" as efields ON efields.id=data.field_id WHERE (";
		
		$was_prev_fld = 0;
		$values = null;
				
		if($user_directory_addr_1_op)   { $ud_sql .= "efields.NAME='". $user_directory_addr_1_op . "'"; $was_prev_fld = 1; }
		if($user_directory_addr_2_op)   { if($was_prev_fld) { $ud_sql .= " OR "; } $ud_sql .= "efields.NAME='" . $user_directory_addr_2_op . "'"; $was_prev_fld = 1; }
		if($user_directory_city_op)     { if($was_prev_fld) { $ud_sql .= " OR "; } $ud_sql .= "efields.NAME='" . $user_directory_city_op . "'"; $was_prev_fld = 1; }
		if($user_directory_state_op)    { if($was_prev_fld) { $ud_sql .= " OR "; } $ud_sql .= "efields.NAME='" . $user_directory_state_op . "'"; $was_prev_fld = 1; }
		if($user_directory_zip_op)      { if($was_prev_fld) { $ud_sql .= " OR "; } $ud_sql .= "efields.NAME='" . $user_directory_zip_op . "'"; $was_prev_fld = 1; }
		if($user_directory_country_op)  { if($was_prev_fld) { $ud_sql .= " OR "; } $ud_sql .= "efields.NAME='" . $user_directory_country_op . "'"; $was_prev_fld = 1; }
		
		if($ud_facebook_op)  { if($was_prev_fld) { $ud_sql .= " OR "; } $ud_sql .= "efields.NAME='" . $ud_facebook_op . "'"; $was_prev_fld = 1; }
		if($ud_twitter_op)   { if($was_prev_fld) { $ud_sql .= " OR "; } $ud_sql .= "efields.NAME='" . $ud_twitter_op . "'"; $was_prev_fld = 1; }
		if($ud_linkedin_op)  { if($was_prev_fld) { $ud_sql .= " OR "; } $ud_sql .= "efields.NAME='" . $ud_linkedin_op . "'"; $was_prev_fld = 1; }
		if($ud_google_op)    { if($was_prev_fld) { $ud_sql .= " OR "; } $ud_sql .= "efields.NAME='" . $ud_google_op . "'"; $was_prev_fld = 1; }
		if($ud_instagram_op) { if($was_prev_fld) { $ud_sql .= " OR "; } $ud_sql .= "efields.NAME='" . $ud_instagram_op . "'"; $was_prev_fld = 1; }
		if($ud_pinterest_op) { if($was_prev_fld) { $ud_sql .= " OR "; } $ud_sql .= "efields.NAME='" . $ud_pinterest_op . "'"; $was_prev_fld = 1; }
		
		foreach ( $user_directory_meta_flds_tmp as $ud_mflds )
		{
			if($ud_mflds['field']) { 
				if($was_prev_fld) { $ud_sql .= " OR "; } 
				$ud_sql .= "efields.NAME='" . $ud_mflds['field'] . "'"; 
				$was_prev_fld = 1; 
			}
		}
		
		if($was_prev_fld) { $ud_sql .= " OR "; } $ud_sql .= "efields.TYPE='avatar'";
		
		$ud_sql .= ") AND data.USER_ID = " . $id;
		
		if($dynamic_ud_debug) { echo "<PRE>Cimy Load Meta Flds SQL:<BR><BR>" . $ud_sql . "<BR><BR></PRE>"; }
				
		return  $wpdb->get_results($ud_sql);
	}
	
	return null;
}

/*** Builds the HTML for displaying the Cimy avatar ***/
function dynamic_ud_get_cimy_avatar($id, $user_login, $atts, $img_style, $cimy_avatar_loc )
{
	if (isset($id)) 
	{			
		$dud_avatar_file_path        = dynamic_ud_before_last ('/', $cimy_avatar_loc);
		$dud_avatar_file_abs_path    = dynamic_ud_between_last ('wp-content', '/', $cimy_avatar_loc);
		$dud_avatar_file_name        = dynamic_ud_between_last ('/', '.', $cimy_avatar_loc);
		$dud_avatar_file_ext         = dynamic_ud_after_last ('.', $cimy_avatar_loc);
		$dud_avatar_thumb_abs_path   = ABSPATH . "wp-content" . $dud_avatar_file_abs_path . "/" . $dud_avatar_file_name . "-thumbnail." . $dud_avatar_file_ext;
		$dud_avatar_thumb_path       = $dud_avatar_file_path . "/" . $dud_avatar_file_name . "-thumbnail." . $dud_avatar_file_ext;
				
		if ($cimy_avatar_loc) 
		{					
			if(file_exists($dud_avatar_thumb_abs_path)) //use the thumbnail if it exists for quicker load
				return "<img alt='' src='{$dud_avatar_thumb_path}' class='avatar " . $img_style  . "' height='96px' width='96px' />";
			else
				return "<img alt='' src='{$cimy_avatar_loc}' class='avatar " . $img_style  . "' height='96px' width='96px' />";
		}
		else 
			return get_avatar($id, '', '', '', $atts);
	}
}

/*** BUDDY PRESS FIELDS ******************************************************************************************************/

function dud_load_bp_vals($user_id, $dud_options, $user_directory_meta_flds_tmp)
{
		$dud_meta_addr_flds = array();
		$dud_meta_social_flds = array();
		
		$user_directory_addr_1_op    = !empty($dud_options['user_directory_addr_1']) ? $dud_options['user_directory_addr_1'] : "";
		$user_directory_addr_2_op    = !empty($dud_options['user_directory_addr_2']) ? $dud_options['user_directory_addr_2'] : "";
		$user_directory_city_op      = !empty($dud_options['user_directory_city']) ? $dud_options['user_directory_city'] : "";
		$user_directory_state_op     = !empty($dud_options['user_directory_state']) ? $dud_options['user_directory_state'] : "";
		$user_directory_zip_op       = !empty($dud_options['user_directory_zip']) ? $dud_options['user_directory_zip'] : "";
		$user_directory_country_op   = !empty($dud_options['user_directory_country']) ? $dud_options['user_directory_country'] : "";
		
		$ud_facebook_op    = !empty($dud_options['ud_facebook']) ? $dud_options['ud_facebook'] : "";
		$ud_twitter_op     = !empty($dud_options['ud_twitter']) ? $dud_options['ud_twitter'] : "";
		$ud_linkedin_op    = !empty($dud_options['ud_linkedin']) ? $dud_options['ud_linkedin'] : "";
		$ud_google_op      = !empty($dud_options['ud_google']) ? $dud_options['ud_google'] : "";
		$ud_instagram_op   = !empty($dud_options['ud_instagram']) ? $dud_options['ud_instagram'] : "";
		$ud_pinterest_op   = !empty($dud_options['ud_pinterest']) ? $dud_options['ud_pinterest'] : "";
		
		//echo "In BP load vals, ud facebook is " . $ud_facebook_op . "<BR>";
		
	    $values = dud_get_bp_data($user_id, $user_directory_addr_1_op, $user_directory_addr_2_op, $user_directory_city_op,
	 			$user_directory_state_op, $user_directory_zip_op, $user_directory_country_op, $ud_facebook_op, $ud_twitter_op, $ud_linkedin_op, $ud_google_op,
				$ud_instagram_op, $ud_pinterest_op, $user_directory_meta_flds_tmp);
	 					
		if($values) 
		{
			foreach ($values as $value)
			{ 
				$meta_name = strtoupper ($value->name);
								
				if($user_directory_addr_1_op && $meta_name === strtoupper ($user_directory_addr_1_op)) 
					$dud_meta_addr_flds[0] = $value->value;	
					 
				else if($user_directory_addr_2_op && $meta_name === strtoupper ($user_directory_addr_2_op)) 
					$dud_meta_addr_flds[1] = $value->value;	
					  
				else if($user_directory_city_op && $meta_name === strtoupper ($user_directory_city_op)) 
					$dud_meta_addr_flds[2] = $value->value;	
					 
				else if($user_directory_state_op && $meta_name === strtoupper ($user_directory_state_op)) 
					$dud_meta_addr_flds[3] = $value->value;	
					 
				else if($user_directory_zip_op && $meta_name === strtoupper ($user_directory_zip_op)) 
					$dud_meta_addr_flds[4] = $value->value;	
				
				else if($user_directory_country_op && $meta_name === strtoupper ($user_directory_country_op)) 
					$dud_meta_addr_flds[5] = $value->value;	
				
				else if($ud_facebook_op && $meta_name === strtoupper ($ud_facebook_op)) 
					$dud_meta_social_flds[0] = $value->value;	
				
				else if($ud_twitter_op && $meta_name === strtoupper ($ud_twitter_op)) 
					$dud_meta_social_flds[1] = $value->value;
				
				else if($ud_linkedin_op && $meta_name === strtoupper ($ud_linkedin_op)) 
					$dud_meta_social_flds[2] = $value->value;	
				
				else if($ud_google_op && $meta_name === strtoupper ($ud_google_op)) 
					$dud_meta_social_flds[3] = $value->value;	
				
				else if($ud_pinterest_op && $meta_name === strtoupper ($ud_pinterest_op)) 
					$dud_meta_social_flds[4] = $value->value;	
				
				else if($ud_instagram_op && $meta_name === strtoupper ($ud_instagram_op)) 
					$dud_meta_social_flds[5] = $value->value;	
				
				else
				{					
					for($inc=0; $inc < sizeof($user_directory_meta_flds_tmp); $inc++ ) 
					{
						if($user_directory_meta_flds_tmp[$inc]['field'] 
							&& $meta_name === strtoupper ($user_directory_meta_flds_tmp[$inc]['field'])) 
						{
							$user_directory_meta_flds_tmp[$inc]['value'] 
								= dynamic_ud_format_meta_val($value->value, $dud_options, $user_directory_meta_flds_tmp[$inc]['format']); 	
							
							/*if(strlen($value->value) > 2 && substr($value->value, 0, 2) === "a:")
								$user_directory_meta_flds_tmp[$inc]['value'] =  implode(", ",(unserialize(stripslashes($value->value))));
							else
								$user_directory_meta_flds_tmp[$inc]['value'] =  stripslashes(dynamic_ud_parse_meta_val($value->value));*/
							
							break;
						}						
					}
				}											 
			}

			if(!empty($dud_meta_addr_flds))
			{
				$end_of_array = sizeof($user_directory_meta_flds_tmp);
				
				$user_directory_meta_flds_tmp[$end_of_array]['field'] = "BP_ADDRESS";
				$user_directory_meta_flds_tmp[$end_of_array]['value'] = $dud_meta_addr_flds;	
			}	

			if(!empty($dud_meta_social_flds))
			{
				$end_of_array = sizeof($user_directory_meta_flds_tmp);
				
				$user_directory_meta_flds_tmp[$end_of_array]['field'] = "BP_SOCIAL";
				$user_directory_meta_flds_tmp[$end_of_array]['value'] = $dud_meta_social_flds;	
			}			
		}
			
		return $user_directory_meta_flds_tmp;
}

/*** Builds BuddyPress Tables Query Based on Dynamic User Directory Key Name Settings ***/
function dud_get_bp_data($id, $user_directory_addr_1_op, $user_directory_addr_2_op, $user_directory_city_op,
	 			$user_directory_state_op, $user_directory_zip_op, $user_directory_country_op, $ud_facebook_op, $ud_twitter_op, $ud_linkedin_op, $ud_google_op,
				$ud_instagram_op, $ud_pinterest_op, $user_directory_meta_flds_tmp)
{
	global $wpdb;
	global $dynamic_ud_debug;
	
	if(defined("DUD_BP_PLUGIN_DATA_TABLE") && defined("DUD_BP_PLUGIN_FIELDS_TABLE"))
	{
		$ud_sql = "SELECT data.value, efields.name, efields.type FROM " . DUD_BP_PLUGIN_DATA_TABLE . " as data JOIN " . DUD_BP_PLUGIN_FIELDS_TABLE . 
				" as efields ON efields.id=data.field_id WHERE (";
		
		$was_prev_fld = 0;
		$values = null;
				
		if($user_directory_addr_1_op)   { $ud_sql .= "efields.name='". $user_directory_addr_1_op . "'"; $was_prev_fld = 1; }
		if($user_directory_addr_2_op)   { if($was_prev_fld) { $ud_sql .= " OR "; } $ud_sql .= "efields.name='" . $user_directory_addr_2_op . "'"; $was_prev_fld = 1; }
		if($user_directory_city_op)     { if($was_prev_fld) { $ud_sql .= " OR "; } $ud_sql .= "efields.name='" . $user_directory_city_op . "'"; $was_prev_fld = 1; }
		if($user_directory_state_op)    { if($was_prev_fld) { $ud_sql .= " OR "; } $ud_sql .= "efields.name='" . $user_directory_state_op . "'"; $was_prev_fld = 1; }
		if($user_directory_zip_op)      { if($was_prev_fld) { $ud_sql .= " OR "; } $ud_sql .= "efields.name='" . $user_directory_zip_op . "'"; $was_prev_fld = 1; }
		if($user_directory_country_op)  { if($was_prev_fld) { $ud_sql .= " OR "; } $ud_sql .= "efields.name='" . $user_directory_country_op . "'"; $was_prev_fld = 1; }
		
		if($ud_facebook_op)  { if($was_prev_fld) { $ud_sql .= " OR "; } $ud_sql .= "efields.name='" . $ud_facebook_op . "'"; $was_prev_fld = 1; }
		if($ud_twitter_op)   { if($was_prev_fld) { $ud_sql .= " OR "; } $ud_sql .= "efields.name='" . $ud_twitter_op . "'"; $was_prev_fld = 1; }
		if($ud_linkedin_op)  { if($was_prev_fld) { $ud_sql .= " OR "; } $ud_sql .= "efields.name='" . $ud_linkedin_op . "'"; $was_prev_fld = 1; }
		if($ud_google_op)    { if($was_prev_fld) { $ud_sql .= " OR "; } $ud_sql .= "efields.name='" . $ud_google_op . "'"; $was_prev_fld = 1; }
		if($ud_instagram_op) { if($was_prev_fld) { $ud_sql .= " OR "; } $ud_sql .= "efields.name='" . $ud_instagram_op . "'"; $was_prev_fld = 1; }
		if($ud_pinterest_op) { if($was_prev_fld) { $ud_sql .= " OR "; } $ud_sql .= "efields.name='" . $ud_pinterest_op . "'"; $was_prev_fld = 1; }
		
		foreach ( $user_directory_meta_flds_tmp as $ud_mflds )
		{
			if($ud_mflds['field']) { 
				if($was_prev_fld) { $ud_sql .= " OR "; } 
				$ud_sql .= "efields.name='" . $ud_mflds['field'] . "'"; 
				$was_prev_fld = 1; 
			}
		}
		
		if($was_prev_fld) { $ud_sql .= " OR "; } $ud_sql .= "efields.type='avatar'";
		
		$ud_sql .= ") AND data.user_id = " . $id;
		
		if($dynamic_ud_debug) { echo "<PRE>BuddyPress Load Meta Flds SQL:<BR><BR>" . $ud_sql . "<BR><BR></PRE>"; }
		
		return  $wpdb->get_results($ud_sql);
	}
	
	return null;
}

/*** S2MEMBER FIELDS ******************************************************************************************************/

function dud_load_s2m_vals($user_id, $dud_options, $user_directory_meta_flds_tmp)
{
	global $wpdb;
	
	$dud_meta_addr_flds = array();
	$dud_meta_social_flds = array();
	
	$user_directory_addr_1_op    = !empty($dud_options['user_directory_addr_1']) ? $dud_options['user_directory_addr_1'] : "";
	$user_directory_addr_2_op    = !empty($dud_options['user_directory_addr_2']) ? $dud_options['user_directory_addr_2'] : "";
	$user_directory_city_op      = !empty($dud_options['user_directory_city']) ? $dud_options['user_directory_city'] : "";
	$user_directory_state_op     = !empty($dud_options['user_directory_state']) ? $dud_options['user_directory_state'] : "";
	$user_directory_zip_op       = !empty($dud_options['user_directory_zip']) ? $dud_options['user_directory_zip'] : "";
	$user_directory_country_op   = !empty($dud_options['user_directory_country']) ? $dud_options['user_directory_country'] : "";
	
	$ud_facebook_op    = !empty($dud_options['ud_facebook']) ? $dud_options['ud_facebook'] : "";
	$ud_twitter_op     = !empty($dud_options['ud_twitter']) ? $dud_options['ud_twitter'] : "";
	$ud_linkedin_op    = !empty($dud_options['ud_linkedin']) ? $dud_options['ud_linkedin'] : "";
	$ud_google_op      = !empty($dud_options['ud_google']) ? $dud_options['ud_google'] : "";
	$ud_instagram_op   = !empty($dud_options['ud_instagram']) ? $dud_options['ud_instagram'] : "";
	$ud_pinterest_op   = !empty($dud_options['ud_pinterest']) ? $dud_options['ud_pinterest'] : "";
	
	$s2m_custom_flds = get_user_meta($user_id, $wpdb->prefix . 's2member_custom_fields');
	$s2m_custom_flds = !empty($s2m_custom_flds[0]) ? $s2m_custom_flds[0] : null; //it will always be an array even for single values
	
	if(!empty($s2m_custom_flds)) 
	{
		foreach ($s2m_custom_flds as $key => $value) 
		{ 
			$key = strtoupper ($key);
			
			if($user_directory_addr_1_op && $key === strtoupper($user_directory_addr_1_op)) 
				$dud_meta_addr_flds[0] = $value;	
				 
			else if($user_directory_addr_2_op && $key === strtoupper($user_directory_addr_2_op)) 
				$dud_meta_addr_flds[1] = $value;	
				  
			else if($user_directory_city_op && $key === strtoupper($user_directory_city_op)) 
				$dud_meta_addr_flds[2] = $value;	
				 
			else if($user_directory_state_op && $key === strtoupper($user_directory_state_op)) 
				$dud_meta_addr_flds[3] = $value;	
				 
			else if($user_directory_zip_op && $key === strtoupper($user_directory_zip_op)) 
				$dud_meta_addr_flds[4] = $value;

			else if($user_directory_country_op && $key === strtoupper($user_directory_country_op)) 
				$dud_meta_addr_flds[5] = $value;
			
			else if($ud_facebook_op && $key === strtoupper ($ud_facebook_op)) 
				$dud_meta_social_flds[0] = $value;	
				
			else if($ud_twitter_op && $key === strtoupper ($ud_twitter_op)) 
				$dud_meta_social_flds[1] = $value;
			
			else if($ud_linkedin_op && $key === strtoupper ($ud_linkedin_op)) 
				$dud_meta_social_flds[2] = $value;	
			
			else if($ud_google_op && $key === strtoupper ($ud_google_op)) 
				$dud_meta_social_flds[3] = $value;	
			
			else if($ud_pinterest_op && $key === strtoupper ($ud_pinterest_op)) 
				$dud_meta_social_flds[4] = $value;	
			
			else if($ud_instagram_op && $key === strtoupper ($ud_instagram_op)) 
				$dud_meta_social_flds[5] = $value;
			
			else
			{					
				for($inc=0; $inc < sizeof($user_directory_meta_flds_tmp); $inc++ ) 
				{
					if($user_directory_meta_flds_tmp[$inc]['field'] 
						&& $key === strtoupper($user_directory_meta_flds_tmp[$inc]['field'])) 
					{
						$user_directory_meta_flds_tmp[$inc]['value'] 
								= dynamic_ud_format_meta_val($value, $dud_options, $user_directory_meta_flds_tmp[$inc]['format']); 
								
						/*if(is_array($value))
							$user_directory_meta_flds_tmp[$inc]['value'] =  implode(", ", $value);	
						else
							$user_directory_meta_flds_tmp[$inc]['value'] =  stripslashes($value);*/
						
						break;
					}						
				}
			}				 
		}			
	}

	if(!empty($dud_meta_addr_flds))
	{
		$end_of_array = sizeof($user_directory_meta_flds_tmp);
		
		$user_directory_meta_flds_tmp[$end_of_array]['field'] = "S2M_ADDRESS";
		$user_directory_meta_flds_tmp[$end_of_array]['value'] = $dud_meta_addr_flds;	
	}

	if(!empty($dud_meta_social_flds))
	{
		$end_of_array = sizeof($user_directory_meta_flds_tmp);
		
		$user_directory_meta_flds_tmp[$end_of_array]['field'] = "S2M_SOCIAL";
		$user_directory_meta_flds_tmp[$end_of_array]['value'] = $dud_meta_social_flds;	
	}			

	return $user_directory_meta_flds_tmp;
}
