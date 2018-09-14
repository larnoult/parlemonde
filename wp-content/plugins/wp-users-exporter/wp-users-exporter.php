<?php
/*
Plugin Name: WP Users Exporter
Plugin URI: https://wordpress.org/plugins/wp-users-exporter/
Description: Users Exporter
Author: hacklab
Version: 1.4.2
Text Domain:
*/
define('WPUE_PREFIX', 'wpue-');
define('__ROLE__','User_Role');


add_action('init', 'wpue_init');
register_activation_hook(__FILE__, 'wpue_activate');
register_deactivation_hook(__FILE__, 'wpue_deactivate');

function wpue_init(){
    
    if (!is_admin())
        return;
    
    require_once dirname(__FILE__).'/A_UserExporter.class.php';
    
    $dir = opendir(dirname(__FILE__).'/exporters/');
    while (false !== ($d = readdir($dir))){
        if(strpos($d,'class.php')){
    	    require_once dirname(__FILE__).'/exporters/'.$d;
    	}
    }
    add_action('admin_menu', 'wpue_admin_menu');
    
    if(isset($_POST[WPUE_PREFIX.'action'])){
        switch($_POST[WPUE_PREFIX.'action']){
            case 'export-users':
                if(current_user_can('use-wp-users-exporter') && isset($_POST['roles']) && isset($_POST['exporter'])){
                    
                    $requested_exporter = $_POST['exporter'];
                    if (class_exists($requested_exporter) && is_subclass_of($requested_exporter, 'A_UserExporter')) {
                        eval('$exporter = new '.$requested_exporter.'();');
                        $exporter->export();
                        die;
                    }
                }
            break;
            
            case 'save-config':
                
                if (!current_user_can('manage-wp-users-exporter'))
                    break;
                
                $wpue_options = wpue_getDefaultConfig();
                $wpue_config = new stdClass();
                global $wp_roles;
                
                foreach ($wp_roles->roles as $r) {
            		if ($r['name'] != 'Administrator') {
	            		$role = get_role(strtolower($r['name']));
                        if (!is_object($role))
                            continue;
	            		if ($_POST[$r['name']]) {
	            			$role->add_cap('use-wp-users-exporter');
	            		} else {
	            			$role->remove_cap('use-wp-users-exporter');
	            		}
            		}
            	}
                
                
                /* BUDDYPRESS EDITION - begin */
                if(wpue_isBP()){
	                foreach ($wpue_options->bp_fields as $id){
	                    if(isset($_POST['bp_fields'][$id])) 
	                        $wpue_config->bp_fields[$id] = $id;
	                }
                }
                /* BUDDYPRESS EDITION - end */
                
                foreach ($wpue_options->metadata as $k=>$n){
                    if(isset($_POST['metadata'][$k])) 
                        $wpue_config->metadata[$k] = $_POST['metadata_desc'][$k];
                }
                
                foreach ($wpue_options->userdata as $k=>$n){
                    if(isset($_POST['userdata'][$k])) 
                        $wpue_config->userdata[$k] = $_POST['userdata_desc'][$k];
                }
                
                foreach ($wpue_options->roles as $k=>$n){
                    if(isset($_POST['roles'][$k]))
                        $wpue_config->roles[$k] = $_POST['roles_desc'][$k];
                }
                
                foreach ($wpue_options->exporters as $e){
                    if(isset($_POST['exporters'][$e])){ 
                        $wpue_config->exporters[] = $e;
                        eval("{$e}::saveOptions();");
                    }
                }
                
                $wpue_config->date_format = $_POST['date_format'];
                
                wpue_setConfig($wpue_config);
            break;
        }
    }
}

function wpue_admin_menu(){

    wp_enqueue_script('jquery-ui-core');
    wp_enqueue_script('jquery-ui-sortable');
    add_submenu_page("tools.php", __('Export Users','wp-users-exporter'), __('Export Users','wp-users-exporter'), 'use-wp-users-exporter', WPUE_PREFIX.'wpue-main', 'wpue_page');
    add_options_page(__('WP Users Exporter','wp-users-exporter'), __('WP Users Exporter', 'wp-users-exporter'), 'manage-wp-users-exporter', 'wpue-config', 'wpue_config_page');    

}

function wpue_config_page(){
    include dirname(__FILE__).'/config.php';
}
    
function wpue_page(){
    include dirname(__FILE__).'/page.php';
}

function wpue_activate(){
    $wpue_config = wpue_getDefaultConfig();
    $wpue_config->metadata = array();
    if(get_option('wpue-config'))
        delete_option('wpue-config');
    
    add_option('wpue-config', $wpue_config);
    
    $admin = get_role('administrator');   
    $admin->add_cap('manage-wp-users-exporter');
    $admin->add_cap('use-wp-users-exporter');

    
    $exporters = wpue_getExporters();
    foreach ($exporters as $exporter)
        eval("{$exporter}::activate();");
}

function wpue_deactivate(){
    $exporters = wpue_getExporters();
    foreach ($exporters as $exporter)
        eval("{$exporter}::deactivate();"); 
    delete_option('wpue-config');
}

function wpue_getExporters(){
    require_once dirname(__FILE__).'/A_UserExporter.class.php';
    $exporters = array();
    $dir = opendir(dirname(__FILE__).'/exporters/');
    while (false !== ($d = readdir($dir))){
        if(strpos($d,'class.php')){
    	    require_once dirname(__FILE__).'/exporters/'.$d;
    		$exporters[] = substr($d, 0, -10);
    	}
    }	
    return $exporters;
}

function wpue_getConfig(){
    $wpue_config = get_option('wpue-config');
    $ok = true;
    foreach ($wpue_config->exporters as $exporter)
        if(!class_exists($exporter))
            $ok = false;

    if(!$ok){
        $wpue_config->exporters = wpue_getExporters();
        wpue_setConfig($wpue_config);
    }
    return $wpue_config;
}

function wpue_setConfig($config){
    if(!$config->metadata)
        $config->metadata = array();

    if(!$config->userdata)
        $config->userdata = array();
    
    if(!$config->exporters)
        $config->exporters = array();
    
    update_option('wpue-config', $config);
}

/**
 * Retorna um array com as configurações padrões
 * @return array config
 */
function wpue_getDefaultConfig(){
    global $wpdb, $wp_roles;
    $wpue_options = new stdClass();

    $metakeys = $wpdb->get_col("SELECT DISTINCT meta_key FROM $wpdb->usermeta");
    
    $wpue_options->userdata = array(
        __ROLE__ => __('Role','wp-users-exporter'),
    	'ID' => __('ID','wp-users-exporter'),
        'user_login' => __('Login','wp-users-exporter'),
        'user_nicename' => __('Nice Name','wp-users-exporter'),
        'user_email' => __('E-Mail','wp-users-exporter'),
    	'user_url' => __('URL','wp-users-exporter'),
    	'user_registered' => __('Data de Registro','wp-users-exporter'),
    	'user_status' => __('Status do Usuário','wp-users-exporter'),
    	'display_name' => __('Nome','wp-users-exporter')
    );
    foreach ($metakeys as $metakey)
        $wpue_options->metadata[$metakey] = $metakey;
    
    foreach ($wp_roles->roles as $role => $r)
        $wpue_options->roles[$role] = $r['name'];
        
    /* BUDDYPRESS EDITION - begin */
    if(wpue_isBP()){
    	$fields = wpue_bp_getProfileFields();
    	foreach ($fields as $field)
    		$wpue_options->bp_fields[$field->id] = $field->id; 
    }
    /* BUDDYPRESS EDITION - end */
        
    $wpue_options->exporters = wpue_getExporters();
    
    $wpue_options->date_format = 'Y-m-d';
    
    return $wpue_options;
}

/* Get users and save it to a temp file
 * return: string temp file path
 * We use this approach to avoid exceeding the memory limit when we got tens of thousands of users
 */ 
function wpue_getUsers_to_tmpfile(){
    global $wpdb;
    
    $tmpfile = tempnam(sys_get_temp_dir(), 'wp-users-exporter');
    
    //To increase speed and avoid memory limit, we do the querys and savings in steps of:
    $step = 1000;
    
    $roles = $_POST['roles'];
    
    foreach ($roles as $k=>$role)
        $roles[$k] = $wpdb->prepare("meta_value LIKE %s", "%\"$role\"%");
        
    $metakeys = implode(' OR ', $roles);
    $udata = $_POST['userdata'];
    if(!in_array('ID', $udata))
        $udata[] = 'ID';
        
    $DISPLAY_ROLE = in_array(__ROLE__, $udata);
    
    $roles = array();
    
    if($DISPLAY_ROLE){
        unset($udata[array_search(__ROLE__,$udata)]);
        
        $roles = new WP_Roles;
        $roles = array_keys($roles->roles);
        
    }
    
        
    
    $cols = implode(',' , $udata);
    $orderby = $_POST['order'];
    $oby = $_POST['oby'] == 'ASC' ? 'ASC' : 'DESC';
    
    // filtro
    if(isset($_POST['filter']) && trim($_POST['filter']) != ''){
        $field = $_POST['filter'];
        $value = $_POST['filter_value'];
        
        if($field[0] == '_'){
            $field = substr($field, 1);
            switch($_POST['operator']){
                case 'eq':
                    $filter = $wpdb->prepare("AND ID IN (SELECT user_id FROM $wpdb->usermeta WHERE meta_key = %s AND meta_value = %s)", $field, $value);
                break;
                case 'dif':
                    $filter = $wpdb->prepare("AND ID NOT IN (SELECT user_id FROM $wpdb->usermeta WHERE meta_key= %s AND meta_value = %s)", $field, $value);
                break;
                case 'like':
                    $filter = $wpdb->prepare("AND ID IN (SELECT user_id FROM $wpdb->usermeta WHERE meta_key= %s AND meta_value LIKE %s)", $field, '%'.$value.'%');
                break;
                case 'not-like':
                    $filter = $wpdb->prepare("AND ID NOT IN (SELECT user_id FROM $wpdb->usermeta WHERE meta_key= %s AND meta_value LIKE %s)", $field, '%'.$value.'%');
                break;
            }
        }else {
            $validFields = array('ID', 'user_login', 'user_pass', 'user_nicename', 'user_email', 'user_url', 'user_registered', 'user_activation_key', 'user_status', 'display_name');
            if (in_array($field, $validFields)) {
                switch($_POST['operator']){
                    case 'eq':
                        $filter = $wpdb->prepare("AND $field = %s", $value);
                    break;
                    case 'dif':
                        $filter = $wpdb->prepare("AND $field <> %s", $value);
                    break;
                    case 'like':
                        $filter = $wpdb->prepare("AND $field LIKE %s", '%'.$value.'%');
                    break;
                    case 'not-like':
                        $filter = $wpdb->prepare("AND $field NOT LIKE %s", '%'.$value.'%');
                    break;
                }
            }
        }
        
        
    }else{
        $filter = '';
    }
    

    // seleciona os usuários
    $base_q = "
    	FROM 
            $wpdb->users 
        WHERE 
        	ID IN (	SELECT 
        				user_id 
        			FROM 
                        $wpdb->usermeta 
                    WHERE 
                    	meta_key = '{$wpdb->prefix}capabilities' AND 
                    	($metakeys)
                   )
            $filter
        ORDER BY $orderby $oby";
    
    $count_q = "SELECT count(ID) $base_q";
    
    $count = $wpdb->get_var($count_q);
    
    for ($ii = 0; $ii <= $count; $ii += $step) {
    
        $q = "SELECT $cols $base_q LIMIT $step OFFSET $ii";
        
                  
        $users = $wpdb->get_results($q);

        $wpue_config = wpue_getConfig();
        $user_ids = array();
        // limpa o usuário, removendo as propriedades que não foram selecionadas no formulario
        // de exportação
        unset($result);
        $result = array();
        foreach($users as $index => $user){
            $user_ids[] = $user->ID;
            $result[$user->ID] = $user;
            unset($users[$index]);
        }
        
        unset($users);
        
        // seleciona os metadados to usuário
        $user_ids = implode(',', $user_ids);
        
        $user_ids = $user_ids ? $user_ids : '-1';
        
        $metakeys = array();
        $metakeys = array_keys($wpue_config->metadata);
        if(!in_array($wpdb->prefix.'capabilities', $metakeys))
                $metakeys[] = $wpdb->prefix.'capabilities';
        
        $metakeys = "'".implode("','", $metakeys)."'";
        
        $qm = "
            SELECT
                user_id,
                meta_key,
                meta_value
            FROM
                $wpdb->usermeta
            WHERE
                meta_key IN ($metakeys) AND
                user_id IN ($user_ids)";
        
        $data = $wpdb->get_results($qm);
        foreach ($data as $metadata){
            $meta_key = $metadata->meta_key;
            $meta_value = $metadata->meta_value;
            $user_id = $metadata->user_id;
            
            if(is_serialized($meta_value)){
                $meta_value = unserialize ($meta_value);
                
                if($meta_key == $wpdb->prefix.'capabilities' && $DISPLAY_ROLE){
                    $user_roles = '';
                    $capabilities = array_keys($meta_value);
                    foreach($capabilities as $i => $cap){
                        if(in_array($cap, $roles))
                            $user_roles = $user_roles ? $user_roles.', '.$cap : $cap;
                        
                        if(!$meta_value[$cap])
                            unset($capabilities[$i]);
                    }
                    $__role = __ROLE__;
                    $result[$user_id]->$__role = $user_roles;
                    
                    $meta_value = $capabilities;
                }
            }
            
            $meta_value = apply_filters('wpue_meta_value', $meta_value, $meta_key);

            if (is_array($meta_value)) {
                $meta_value = join(' ', $meta_value);
            }
            
            $result[$user_id]->$meta_key = isset($result[$user_id]->$meta_key) ? ($result[$user_id]->$meta_key).", ".$meta_value : $meta_value;
            
            if(is_object($result[$user_id]->$meta_key))
                $result[$user_id]->$meta_key = (array) $result[$user_id]->$meta_key;
                
            if(is_array($result[$user_id]->$meta_key))
                $result[$user_id]->$meta_key = implode(', ', $result[$user_id]->$meta_key);
                
        }
        
        
        /* BUDDYPRESS EDITION */
        
        if(wpue_isBP()){
            $field_ids = implode(',', $wpue_config->bp_fields);
            
            $bp_fields = wpue_bp_getProfileFields();
            
            $bp_data_query = "
            SELECT 
                * 
            FROM 
                {$wpdb->prefix}bp_xprofile_data 
            WHERE 
                user_id IN ($user_ids) AND 
                field_id IN ($field_ids)
            ORDER BY 
                user_id ASC";
            
            
            $bp_data = $wpdb->get_results($bp_data_query);
            
            
            foreach ($bp_data as $data){
                $field = 'bp_'.$data->field_id;
                if($bp_fields[$data->field_id]->type == 'datebox')
                    $data->value = date($wpue_config->date_format, $data->value);
                    
                if(is_serialized($data->value))
                    $data->value = unserialize($data->value);
                    
                if(is_object($data->value))
                    $data->value = (array) $data->value;
                
                if(is_array($data->value))
                    $data->value = implode(', ', $data->value);
                    
                $result[$data->user_id]->$field = $data->value;
            }
        }
        
        $result_string = '';
        
        foreach ($result as $id => $r) {
            foreach ($r as $key => $value) {
                $r->$key = str_replace(array("\r\n", "\n", "\r"), '||BR||', $value);
            }
            $result_string .= serialize($r) . "\n";
        }
        
        //$result_string = str_replace("\r", '', $result_string);
        file_put_contents($tmpfile, $result_string, FILE_APPEND);
        
        
        
        
    
    } // end for
    
    
    return $tmpfile;
}



/* BUDDYPRESS EDITION - begin */

/**
 * is buddypress
 */
function wpue_isBP(){
	return defined('BP_VERSION');
}

function wpue_bp_getProfileFields(){
	global $wpdb, $wpue_bp_fields;
	
	// runtime cache
	if(isset($wpue_bp_fields) && is_array($wpue_bp_fields))
		return $wpue_bp_fields;
		
	$q = "
	SELECT 
		* 
	FROM 
		{$wpdb->prefix}bp_xprofile_fields 
	WHERE 
		parent_id = 0 
	ORDER BY 
		group_id ASC, 
		name ASC";
		
	$result = $wpdb->get_results($q);
	
	foreach ($result as $field)
		$wpue_bp_fields[$field->id] = $field;
		
	return $wpue_bp_fields;
}

/* BUDDYPRESS EDITION - end */

