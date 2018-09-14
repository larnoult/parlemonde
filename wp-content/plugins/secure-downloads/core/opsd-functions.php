<?php 
/**
 * @version 1.0
 * @package Secure Downloads 
 * @subpackage Support Functions
 * @category Functions
 * 
 * @author wpdevelop
 * @link http://oplugins.com/
 * @email info@oplugins.com
 *
 * @modified 29.09.2015
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly


////////////////////////////////////////////////////////////////////////////////
// Formatting functions
////////////////////////////////////////////////////////////////////////////////    
/**
 * Sanitize term to Slug format (no spaces, lowercase).
 * urldecode - reverse munging of UTF8 characters.
 *
 * @param mixed $value
 * @return string
 */
function opsd_get_slug_format( $value ) {
    return  urldecode( sanitize_title( $value ) );
}


/**
 * Get Slug Format Option Value for saving to  the options table.
 * Replacing - to _ and restrict length to 64 characters.
 * 
 * @param string $value
 * @return string
 */
function opsd_get_slug_format_4_option_name( $value ) {
    
    $value = opsd_get_slug_format( $value );
    $value = str_replace('-', '_', $value);
    $value = substr($value, 0, 64);
    return $value;
}


			

/** Replace shortcodes in string
 * 
 * @param string $subject - string to  manipulate
 * @param array $replace_array - array with  values to  replace                 // array( [opsd_id] => 9, [id] => 9, [dates] => July 3, 2016 14:00 - July 4, 2016 16:00, .... )
 * @param mixed $replace_unknown_shortcodes - replace unknown params, if false, then  no replace unknown params
 * @return string
 */
function opsd_replace_opsd_shortcodes( $subject, $replace_array , $replace_unknown_shortcodes = ' ' ) {

    $defaults = array(
        'ip'                => apply_opsd_filter( 'opsd_get_user_ip' )
        , 'blogname'        => wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES )
        , 'siteurl'         => get_site_url()
    );

    $replace = wp_parse_args( $replace_array, $defaults );

    foreach ( $replace as $replace_shortcode => $replace_value ) {

        $subject = str_replace( array(   '[' . $replace_shortcode . ']'
                                       , '{' . $replace_shortcode . '}' )
                                , $replace_value
                                , $subject );
    }

    // Remove all shortcodes, which is not replaced early.
    if ( $replace_unknown_shortcodes !== false )    
        $subject = preg_replace( '/[\s]{0,}[\[\{]{1}[a-zA-Z0-9.,-_]{0,}[\]\}]{1}[\s]{0,}/', $replace_unknown_shortcodes, $subject );  

    
    return $subject;        
}


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//  S u p p o r t    f u n c t i o n s        
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    // Get array of images - icons inside of this directory
    function opsd_dir_list ($directories) {

        // create an array to hold directory list
        $results = array();

        if (is_string($directories)) $directories = array($directories);
        foreach ($directories as $dir) {
            if ( is_dir($dir) )
                $directory = $dir ;
            else
                $directory = OPSD_PLUGIN_DIR . $dir ;
            
            if ( file_exists( $directory ) ) {                                  //FixIn: 5.4.5
                // create a handler for the directory
                $handler = @opendir($directory);
                if ($handler !== false) {
                    // keep going until all files in directory have been read
                    while ($file = readdir($handler)) {

                        // if $file isn't this directory or its parent,
                        // add it to the results array
                        if ($file != '.' && $file != '..' && ( strpos($file, '.css' ) !== false ) )
                            $results[] = array($file, /* OPSD_PLUGIN_URL .*/ $dir . $file,  ucfirst(strtolower( str_replace('.css', '', $file))) );
                    }

                    // tidy up: close the handler
                    closedir($handler);
                }
            }
        }
        // done!
        return $results;
    }
    
    /** Get absolute URL to  relative plugin path.
     *  Depend from the OPSD_MIN contant can  be load minified version of file,  if its exist
     * @param string $path    - path
     * @return string
     */
    function opsd_plugin_url( $path ) {
        
        if ( ( defined( 'OPSD_MIN' ) ) && ( OPSD_MIN ) ){
            $path_min = $path;
            if ( substr( $path_min , -3 ) === '.js' ) {
                $path_min = substr( $path_min , 0, -3 ) . '.min.js';
            }
            if ( substr( $path_min , -4 ) === '.css' ) {
                $path_min = substr( $path_min , 0, -4 ) . '.min.css';
            }
            if (  file_exists( trailingslashit( OPSD_PLUGIN_DIR ) . ltrim( $path_min, '/\\' ) )  )  // check if this file exist
                return trailingslashit( OPSD_PLUGIN_URL ) . ltrim( $path_min, '/\\' );
        }
        return trailingslashit( OPSD_PLUGIN_URL ) . ltrim( $path, '/\\' );
    }
   
    
    /** Check  if such file exist or not.
     * 
     * @param string $path - relative path to  file (relative to plugin folder).
     * @return boolean true | false
     */
    function opsd_is_file_exist( $path ) {
                             
        if (  file_exists( trailingslashit( OPSD_PLUGIN_DIR ) . ltrim( $path, '/\\' ) )  )  // check if this file exist
            return true;
        else 
            return false;
    }
    
    ////////////////////////////////////////////////////////////////////////////
    // Admin Menu Links
    ////////////////////////////////////////////////////////////////////////////

    /** Get URL to specific Admin Menu page
     * 
     * @param string $menu_type         -   { item | add | resources | settings }
     * @param boolean $is_absolute_url  - Absolute or relative url { default: true }
     * @return string                   - URL  to  menu
     */
    function opsd_get_menu_url( $menu_type, $is_absolute_url = true ) {
       
        switch ( $menu_type) {
            
			case 'master':														// Master
							$link = 'opsd';
							break;
                
            case 'add':                                                         // Add New
							$link = 'opsd-files';
							break;

            case 'settings':                                                    // Settings
            case 'options':
							$link = 'opsd-settings';
							break;

            default:                                                            // Master
							$link = 'opsd';
							break;
        }
        
        if ( $is_absolute_url ) {
            $link = admin_url( 'admin.php' ) . '?page=' . $link ;
        } 
        
        return $link;        
    }

    // // // // // // // // // // // // // // // // // // // // // // // // // /
    
    /** Get URL of item Listing or Calendar Overview page
     * 
     * @param boolean $is_absolute_url  - Absolute or relative url { default: true }
     * @param boolean $is_old           - { default: true } 
     * @return string                   - URL  to  menu
     */
    function opsd_get_master_url( $is_absolute_url = true ) {
        return opsd_get_menu_url( 'master', $is_absolute_url );
    }
    
    /** Get URL of item > Add item page 
     * 
     * @param boolean $is_absolute_url  - Absolute or relative url { default: true }
     * @param boolean $is_old           - { default: true } 
     * @return string                   - URL  to  menu
     */
    function opsd_get_new_opsd_url( $is_absolute_url = true ) {
        return opsd_get_menu_url( 'add', $is_absolute_url );
    }
       
    /** Get URL of item > Settings page 
     * 
     * @param boolean $is_absolute_url  - Absolute or relative url { default: true }
     * @param boolean $is_old           - { default: true } 
     * @return string                   - URL  to  menu
     */
    function opsd_get_settings_url( $is_absolute_url = true ) {
        return opsd_get_menu_url( 'settings', $is_absolute_url );
    }
    
    // // // // // // // // // // // // // // // // // // // // // // // // // /
    
    /** Check if this item Listing or Calendar Overview page
     * @param string $server_param -  'REQUEST_URI' | 'HTTP_REFERER'  Default: 'REQUEST_URI'
     * @return boolean true | false
     */
    function opsd_is_master_page( $server_param = 'REQUEST_URI' ) { 

        if (  ( is_admin() ) &&
              ( strpos($_SERVER[ $server_param ],'page=opsd') !== false ) &&
              ( strpos($_SERVER[ $server_param ],'page=opsd-') === false )
            ) {
            return true;
        } 
        return false;
    }
    
    /** Check if this item > Add item page 
     * @param string $server_param -  'REQUEST_URI' | 'HTTP_REFERER'  Default: 'REQUEST_URI'
     * @return boolean true | false
     */
    function opsd_is_new_opsd_page( $server_param = 'REQUEST_URI' ) {

        if (  ( is_admin() ) &&
              ( strpos($_SERVER[ $server_param ],'page=opsd-new') !== false )
            ) {
            return true;
        } 
        return false;
    }
    

    /** Check if this item > Settings page 
     * @param string $server_param -  'REQUEST_URI' | 'HTTP_REFERER'  Default: 'REQUEST_URI'
     * @return boolean true | false
     */    
    function opsd_is_settings_page( $server_param = 'REQUEST_URI' ) {

        if (  ( is_admin() ) &&
              ( strpos($_SERVER[ $server_param ],'page=opsd-settings') !== false )
            ) {
            return true;
        } 
        return false;
    }
    
    ////////////////////////////////////////////////////////////////////////////
    
        
    /** Insert New Line symbols after <br> tags. Usefull for the settings pages to  show in redable view
     * 
     * @param type $param
     * @return type
     */
    function opsd_nl_after_br($param) {
        
        $value = preg_replace( "@(&lt;|<)br\s*/?(&gt;|>)(\r\n)?@", "<br/>", $param );
        
        return $value;
    }
    

    /**
     * Replace ** to <strong> and * to  <em>
     * 
     * @param String $text
     * @return string
     */
    if ( ! function_exists( 'opsd_recheck_strong_symbols' ) ) { 
    function opsd_recheck_strong_symbols( $text ){
    
        $patterns =  '/(\*\*)(\s*[^\*\*]*)(\*\*)/';    
        $replacement = '<strong>${2}</strong>';
        $value_return = preg_replace($patterns, $replacement, $text);

        $patterns =  '/(\*)(\s*[^\*]*)(\*)/';    
        $replacement = '<em>${2}</em>';
        $value_return = preg_replace($patterns, $replacement, $value_return);

        return $value_return;
    }
    }
    
    
    // Set URL from absolute to relative (starting from /)                            
    function opsd_set_relative_url( $url ){

        $url = esc_url_raw($url);

        $url_path = parse_url($url,  PHP_URL_PATH);
        $url_path =  ( empty($url_path) ? $url : $url_path );

        $url =  trim($url_path, '/');
        return  '/' . $url;
    }
    
    // Get Correct Relative URL 
    function opsd_make_link_relative( $link ){

        if ( $link  == get_option('siteurl') ) 
            $link = '/';
        $link = '/' . trim( wp_make_link_relative( $link ), '/' ); 

        return $link;        
    }
    
    // Get Correct Absolute URL 
    function opsd_make_link_absolute( $link ){
    
        if ( ( $link  != get_option('siteurl') ) && ( strpos($link, 'http') !== 0 ) )
            $link  = get_option('siteurl') . '/' . trim( wp_make_link_relative( $link ), '/' ); 
        return esc_js( $link ) ;
    }
    
    //Simple hack  to  make array strings lowercase
    function opsd_arraytolower( $array ){
        return unserialize( strtolower( serialize( $array ) ) );
    }


    // Get version
    function get_opsd_version(){ 
        $version = 'free';
        return $version;
    }
    

    /** Check if user accidentially update Secure Downloads Paid version to Free
     * 
     * @return bool
     */
    function opsd_is_updated_paid_to_free() {
        
        if ( ( opsd_is_table_exists('opsd_log') ) && ( ! class_exists('opsd_personal') )  ) 
            return  true;
        else
            return false;                    
    }
    
    ////////////////////////////////////////////////////////////////////////////
    function opsd_get_ver_sufix() {               
        if( strpos( strtolower(OPSD_VERSION) , 'multisite') !== false  ) {
            $v_type = '-multi';                         
        } else if( strpos( strtolower(OPSD_VERSION) , 'develop') !== false  ) {
            $v_type = '-dev';
        } else {
            $v_type = '';
        }  
        $v = '';
        if (class_exists('opsd_personal'))  $v = 'ps'. $v_type;
        if (class_exists('opsd_pro')) $v = '';        
        return $v ;
    }
    
    
    function opsd_up_link() {
        if ( ! opsd_is_this_demo() ) 
             $v = opsd_get_ver_sufix();
        else $v = '';
        return 'http://oplugins.com/plugins/secure-downloads/' . ( ( empty($v) ) ? '' : 'upgrade-' . $v  . '/' ) ;
    }
    
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // DB - cheking if table, field or index exists
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Check if table exist
     * 
     * @global type $wpdb
     * @param string $tablename
     * @return 0|1
     */
    function opsd_is_table_exists( $tablename ) {
        
        global $wpdb;
        
        if ( (! empty($wpdb->prefix) ) && ( strpos($tablename, $wpdb->prefix) === false ) ) 
            $tablename = $wpdb->prefix . $tablename ;
        
        $sql_check_table = $wpdb->prepare("SHOW TABLES LIKE %s" , $tablename ); //FixIn 5.4.3
        
        $res = $wpdb->get_results( $sql_check_table );
        
        return count($res);                                                     //FixIn 5.4.3
        /*
        $sql_check_table = $wpdb->prepare("
            SELECT COUNT(*) AS count
            FROM information_schema.tables
            WHERE table_schema = '". DB_NAME ."'
            AND table_name = %s " , $tablename );

        $res = $wpdb->get_results( $sql_check_table );
        return $res[0]->count;*/
    }

    
    /**
     * Check if table exist
     * 
     * @global type $wpdb
     * @param string $tablename
     * @param type $fieldname
     * @return 0|1
     */
    function opsd_is_field_in_table_exists( $tablename , $fieldname) {
        global $wpdb;
        if ( (! empty($wpdb->prefix) ) && ( strpos($tablename, $wpdb->prefix) === false ) ) $tablename = $wpdb->prefix . $tablename ;
        $sql_check_table = "SHOW COLUMNS FROM {$tablename}" ;

        $res = $wpdb->get_results( $sql_check_table );

        foreach ($res as $fld) {
            if ($fld->Field == $fieldname) return 1;
        }

        return 0;
    }

    
    /**
     * Check if index exist
     * 
     * @global type $wpdb
     * @param string $tablename
     * @param type $fieldindex
     * @return 0|1
     */
    function opsd_is_index_in_table_exists( $tablename , $fieldindex) {
        global $wpdb;
        if ( (! empty($wpdb->prefix) ) && ( strpos($tablename, $wpdb->prefix) === false ) ) $tablename = $wpdb->prefix . $tablename ;
        $sql_check_table = $wpdb->prepare("SHOW INDEX FROM {$tablename} WHERE Key_name = %s", $fieldindex );       
        $res = $wpdb->get_results( $sql_check_table );
        if (count($res)>0) return 1;
        else               return 0;
    }

    
    
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    // Replace the shortcodes in the form by values from array
    function replace_opsd_shortcodes_in_form($form, $field_values=array(), $is_delete_unknown_shortcodes = false) {

        $new_form = $form;

        // Patern for searching of the shortcodes in some form
        $any_shortcodes = '[a-zA-Z][0-9a-zA-Z:._-]*';
        $regex = '%\[\s*(' . $any_shortcodes . ')\s*\]%';

        // Search  any shortcodes in the $form
        preg_match_all($regex, $form, $matches, PREG_PATTERN_ORDER);   // PREG_PATTERN_ORDER, PREG_SET_ORDER, PREG_OFFSET_CAPTURE

        // Loop  all found shortcodes
        if (isset($matches[1])) {
                foreach ($matches[1] as $key=>$field) {

                    //$field             // secondname
                    //$matches[0][$key]  // [secondname]
                    //$matches[1][$key]  // secondname

                    if (isset($field_values[$field])) $replace_value = $field_values[$field];
                    else {
                        if ($is_delete_unknown_shortcodes) $replace_value = '';
                        else $replace_value = $matches[0][$key];
                    }

                    $new_form = str_replace( $matches[0][$key] , $replace_value, $new_form);
                }
        }
        return  $new_form;
    }



        /** Get fields from item form at the settings page or return false if no fields
         * 
         * @param string $opsd_form 
         * @return mixed  false | array( $fields_count, $fields_matches )
         */
        function opsd_get_fields_from_opsd_form( $opsd_form = '' ){
            if ( empty( $opsd_form )  )
                $opsd_form  = get_opsd_option( 'opsd_form' );
            $types = 'text[*]?|email[*]?|time[*]?|textarea[*]?|select[*]?|checkbox[*]?|radio|acceptance|captchac|captchar|file[*]?|quiz';
            $regex = '%\[\s*(' . $types . ')(\s+[a-zA-Z][0-9a-zA-Z:._-]*)([-0-9a-zA-Z:#_/|\s]*)?((?:\s*(?:"[^"]*"|\'[^\']*\'))*)?\s*\]%';
            $regex2 = '%\[\s*(country[*]?|starttime[*]?|endtime[*]?)(\s*[a-zA-Z]*[0-9a-zA-Z:._-]*)([-0-9a-zA-Z:#_/|\s]*)*((?:\s*(?:"[^"]*"|\'[^\']*\'))*)?\s*\]%';
            $fields_count = preg_match_all($regex, $opsd_form, $fields_matches) ;
            $fields_count2 = preg_match_all($regex2, $opsd_form, $fields_matches2) ;

            //Gathering Together 2 arrays $fields_matches  and $fields_matches2
            foreach ($fields_matches2 as $key => $value) {
                if ($key == 2) $value = $fields_matches2[1];
                foreach ($value as $v) {
                    $fields_matches[$key][count($fields_matches[$key])]  = $v;
                }
            }
            $fields_count += $fields_count2;

            if ($fields_count>0) return array($fields_count, $fields_matches);
            else return false;
        }
    

        /** Get Get only SELECT, CHCKBOX & RADIO fields from item form at the settings page or return false if no fields
         * 
         * @param string $opsd_form 
         * @return mixed  false | array( $fields_count, $fields_matches )
         */
        function opsd_get_select_checkbox_fields_from_opsd_form( $opsd_form = '' ){
            
            if ( empty( $opsd_form )  )  
                $opsd_form  = get_opsd_option( 'opsd_form' );
            
            $types = 'select[*]?|checkbox[*]?|radio';
            $regex = '%\[\s*(' . $types . ')(\s+[a-zA-Z][0-9a-zA-Z:._-]*)([-0-9a-zA-Z:#_/|\s]*)?((?:\s*(?:"[^"]*"|\'[^\']*\'))*)?\s*\]%';
            
            $fields_count = preg_match_all($regex, $opsd_form, $fields_matches) ;
            
            if ( $fields_count > 0 ) 
                 return array( $fields_count, $fields_matches );
            else return false;
        }
    

    //   Get header info from this file, just for compatibility with WordPress 2.8 and older versions //////////////////////////////////////
    if (!function_exists ('get_file_data_wpdev')) {
    function get_file_data_wpdev( $file, $default_headers, $context = '' ) {
        // We don't need to write to the file, so just open for reading.
        $fp = fopen( $file, 'r' );

        // Pull only the first 8kiB of the file in.
        $file_data = fread( $fp, 8192 );

        // PHP will close file handle, but we are good citizens.
        fclose( $fp );

        if( $context != '' ) {
            $extra_headers = array();//apply_filters( "extra_$context".'_headers', array() );

            $extra_headers = array_flip( $extra_headers );
            foreach( $extra_headers as $key=>$value ) {
                $extra_headers[$key] = $key;
            }
            $all_headers = array_merge($extra_headers, $default_headers);
        } else {
            $all_headers = $default_headers;
        }

        foreach ( $all_headers as $field => $regex ) {
            preg_match( '/' . preg_quote( $regex, '/' ) . ':(.*)$/mi', $file_data, ${$field});
            if ( !empty( ${$field} ) )
                ${$field} =  trim(preg_replace("/\s*(?:\*\/|\?>).*/", '',  ${$field}[1] ));
            else
                ${$field} = '';
        }

        $file_data = compact( array_keys( $all_headers ) );

        return $file_data;
    }
    }

	

	/** Clean Request Parameters
	 * 
	 */
	function opsd_check_request_paramters() { 

		$clean_params = array();  

		$clean_params[ 'wh_opsd_id' ]			= 'digit_or_csd';		// '0' | '1' | ''
		$clean_params[ 'wh_opsd_date' ]			= 'digit_or_date';		// number | date 2016-07-20
		$clean_params[ 'wh_opsd_datenext' ]		= 'd';					// '1' | '2' ....
		$clean_params[ 'wh_pay_statuscustom' ]	= 's';					//string   !!! LIKE  !!!
		$clean_params[ 'wh_pay_status' ]		= array( 'all', 'group_ok', 'group_unknown', 'group_pending', 'group_failed' );

		foreach ( $clean_params as $request_key => $clean_type ) {

			// elements only listed in array::
			if (  is_array( $clean_type ) ) {                                       // check  only values from  the list  in this array

				if ( ( isset( $_REQUEST[ $request_key ] ) ) &&  ( ! in_array( $_REQUEST[ $request_key ], $clean_type ) ) )
					$clean_type = 's';    
				else 
					$clean_type = 'checked_skip_it';
			} 

			switch ( $clean_type ) {

				case 'checked_skip_it':

					break;

				case 'digit_or_date':                                            // digit or comma separated digit
					if ( isset( $_REQUEST[ $request_key ] ) ) 
						$_REQUEST[ $request_key ] = opsd_clean_digit_or_date( $_REQUEST[ $request_key ] );        // nums    

					break;

				case 'digit_or_csd':                                            // digit or comma separated digit
					if ( isset( $_REQUEST[ $request_key ] ) ) 
						$_REQUEST[ $request_key ] = opsd_clean_digit_or_csd( $_REQUEST[ $request_key ] );        // nums    

					break;

				case 's':                                                       // string
					if ( isset( $_REQUEST[ $request_key ] ) ) 
						$_REQUEST[ $request_key ] = opsd_clean_like_string_for_db( $_REQUEST[ $request_key ] );

					break;

				case 'd':                                                       // digit
					if ( isset( $_REQUEST[ $request_key ] ) ) 
						if ( $_REQUEST[ $request_key ] !== '' )
							$_REQUEST[ $request_key ] = intval( $_REQUEST[ $request_key ] );

					break;

				default:
					if ( isset( $_REQUEST[ $request_key ] ) ) {
						$_REQUEST[ $request_key ] = intval( $_REQUEST[ $request_key ] );                    
					}
					break;
			}


		}

	}


    // Security  
    function escape_any_xss($formdata){

        $formdata_array = explode('~',$formdata);
        $formdata_array_count = count($formdata_array);

        $clean_formdata = '';

        for ( $i=0 ; $i < $formdata_array_count ; $i++) {
            $elemnts = explode('^',$formdata_array[$i]);
            if ( count( $elemnts ) > 2 ) {
                $type = $elemnts[0];
                $element_name = $elemnts[1];
                $value = $elemnts[2];

                $value = opsd_clean_parameter( $value );

                // convert to new value
                $clean_formdata .= $type . '^' . $element_name . '^' . $value . '~';
            }
        }

        $clean_formdata = substr($clean_formdata, 0, -1);
        $clean_formdata = str_replace('%', '&#37;', $clean_formdata ); // clean any % from the form, because otherwise, there is problems with SQL prepare function
        
        return $clean_formdata;
    }

    
    /** Check  paramter  if it number or comma separated list  of numbers
     * 
     * @global type $wpdb
     * @param string $value
     * @return string
     * 
     * Exmaple:
                        opsd_clean_digit_or_csd( '12,a,45,9' )                  => '12,0,45,9'
     * or
                        opsd_clean_digit_or_csd( '10a' )                        => '10
     * or
                        opsd_clean_digit_or_csd( array( '12,a,45,9', '10a' ) )  => array ( '12,0,45,9',  '10' )
     */
    function opsd_clean_digit_or_csd( $value ) {                                //FixIn:6.2.1.4 
        
        if ( $value === '' ) return $value;
        
        
        if ( is_array( $value ) ) {
            foreach ( $value as $key => $check_value ) {
                $value[ $key ] = opsd_clean_digit_or_csd( $check_value ); 
            }
            return $value;
        }
        
        
        global $wpdb;
        
        $value = str_replace( ';', ',', $value );

        $array_of_nums = explode(',', $value);

        $result = array();
        foreach ($array_of_nums as $check_element) {
            $result[] = $wpdb->prepare( "%d", $check_element );
        }
        $result = implode(',', $result );
        return $result;
    }
    
    
    /** Cehck  about Valid date,  like 2016-07-20 or digit
     * 
     * @param string $value
     * @return string or int
     */
    function opsd_clean_digit_or_date( $value ) {                               //FixIn:6.2.1.4
    
        if ( $value === '' ) return $value;
        
        if ( preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $value ) ) {
            
            return $value;                                                      // Date is valid in format: 2016-07-20
        } else {
            return intval( $value );
        }
        
    }
    
    
    // check $value for injection here
    function opsd_clean_parameter( $value ) {
        
        $value = preg_replace( '/<[^>]*>/', '', $value );                       // clean any tags
        $value = str_replace( '<', ' ', $value ); 
        $value = str_replace( '>', ' ', $value ); 
        $value = strip_tags( $value );
                
        // Clean SQL injection    
        $value = esc_sql( $value );
        
        return $value; 
    }

    
    function opsd_esc_like( $value_trimmed ) {
 
        global $wpdb;
        if ( method_exists( $wpdb ,'esc_like' ) )
            return $wpdb->esc_like( $value_trimmed );                           // Its require minimum WP 4.0.0
        else
            return addcslashes( $value_trimmed, '_%\\' );                       // Direct implementation  from $wpdb->esc_like(
    }
    
    
    /** Clean user string for using in SQL LIKE statement - append to  LIKE sql
     * 
     * @param string $value - to clean
     * @return string       - escaped
     *                                  Exmaple:    
     *                                              $search_escaped_like_title = opsd_clean_like_string_for_append_in_sql_for_db( $input_var );
     * 
     *                                              $where_sql = " WHERE title LIKE ". $search_escaped_like_title ." ";
     */
    function opsd_clean_like_string_for_append_in_sql_for_db( $value ) {
        global $wpdb;
        
        $value_trimmed = trim( stripslashes( $value ) );
	$wild = '%';	
	$like = $wild . opsd_esc_like( $value_trimmed ) . $wild;
	$sql  = $wpdb->prepare( "'%s'", $like );

        return $sql;    
        
        
	/* Help:
         * First half of escaping for LIKE special characters % and _ before preparing for MySQL.
	 * Use this only before wpdb::prepare() or esc_sql().  Reversing the order is very bad for security.
	 *
	 * Example Prepared Statement:
	 *
	 *     $wild = '%';
	 *     $find = 'only 43% of planets';
	 *     $like = $wild . opsd_esc_like( $find ) . $wild;
	 *     $sql  = $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE post_content LIKE '%s'", $like );
	 *
	 * Example Escape Chain:
	 *
	 *     $sql  = esc_sql( opsd_esc_like( $input ) );
	 */        

    }
    
    
    /** Clean string for using in SQL LIKE requests inside single quotes:    WHERE title LIKE '%". $escaped_search_title ."%' 
     *  Replaced _ to \_     % to \%      \   to   \\
     * @param string $value - to clean
     * @return string       - escaped
     *                                  Exmaple:    
     *                                              $search_escaped_like_title = opsd_clean_like_string_for_db( $input_var );
     * 
     *                                              $where_sql = " WHERE title LIKE '%". $search_escaped_like_title ."%' ";
     * 
     *                                  Important! Use SINGLE quotes after in SQL query:  LIKE '%".$data."%'
     */
    function opsd_clean_like_string_for_db( $value ){

        global $wpdb;
        
        $value_trimmed = trim( stripslashes( $value ) );

        $value_trimmed =  opsd_esc_like( $value_trimmed );

        $value = trim( $wpdb->prepare( "'%s'",  $value_trimmed ) , "'" );

        return $value;
        
	/* Help:
         * First half of escaping for LIKE special characters % and _ before preparing for MySQL.
	 * Use this only before wpdb::prepare() or esc_sql().  Reversing the order is very bad for security.
	 *
	 * Example Prepared Statement:
	 *
	 *     $wild = '%';
	 *     $find = 'only 43% of planets';
	 *     $like = $wild . opsd_esc_like( $find ) . $wild;
	 *     $sql  = $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE post_content LIKE '%s'", $like );
	 *
	 * Example Escape Chain:
	 *
	 *     $sql  = esc_sql( opsd_esc_like( $input ) );
	 */        
    }
    
    
    /** Escape string from SQL for the HTML form field
     * 
     * @param string $value
     * @return string
     * 
     * Used: esc_sql function.
     * 
     * https://codex.wordpress.org/Function_Reference/esc_sql 
     * Note: Be careful to use this function correctly. It will only escape values to be used in strings in the query. 
     * That is, it only provides escaping for values that will be within quotes in the SQL (as in field = '{$escaped_value}'). 
     * If your value is not going to be within quotes, your code will still be vulnerable to SQL injection. 
     * For example, this is vulnerable, because the escaped value is not surrounded by quotes in the SQL query: 
     * ORDER BY {$escaped_value}. As such, this function does not escape unquoted numeric values, field names, or SQL keywords. 
     *         
     */
    function opsd_clean_string_for_form( $value ){
        
        global $wpdb;
        
        $value_trimmed = trim( stripslashes( $value ) );
        
        $esc_sql_value =  esc_sql(  $value_trimmed );
                
        //$value = trim( $wpdb->prepare( "'%s'",  $esc_sql_value ) , "'" );
               
        $esc_sql_value = trim( stripslashes( $esc_sql_value ) );
        
        return $esc_sql_value;
    
    }
    
    
    ////////////////////////////////////////////////////////////////////////////
    
    
    function opsd_get_number_new_items(){
		return 0;
    }



    
    /** Check if this demo website
     * 
     * @return bool
     */
    function opsd_is_this_demo() {
//return ! true;     //TODO: comment it. 2016-09-27    // Replaced!   
        if  (     
                   ( strpos( $_SERVER['SCRIPT_FILENAME'], 'oplugins.com' ) !== false )
                || ( strpos( $_SERVER['HTTP_HOST'], 'oplugins.com' ) !== false )
            )
              return true;
            else
              return false;
    }


    // Add Admin Bar
    add_action( 'admin_bar_menu', 'wp_admin_bar_items_menu', 70 );

    function wp_admin_bar_items_menu(){
		
        global $wp_admin_bar;
//debuge($wp_admin_bar);die;        
        $current_user = wp_get_current_user();

        $curr_user_role = get_opsd_option( 'opsd_user_role_master' );
        $level = 10;
        if ($curr_user_role == 'administrator')       $level = 10;
        else if ($curr_user_role == 'editor')         $level = 7;
        else if ($curr_user_role == 'author')         $level = 2;
        else if ($curr_user_role == 'contributor')    $level = 1;
        else if ($curr_user_role == 'subscriber')     $level = 0;
        
        if ( ( $current_user->user_level < $level ) || ! is_admin_bar_showing() )
			return;


		$update_count = opsd_get_number_new_items();	// 0

        $title = 'Secure Downloads';
		$update_title =  $title;
        
        
        if ( $update_count > 0 ) {
            $update_count_title = "&nbsp;<span id='ab-updates' class='opsd-count bk-update-count' >" . number_format_i18n($update_count) . "</span>" ; //id='opsd-count'
            $update_title .= $update_count_title;
        }

        $link_items	   = opsd_get_master_url();
        $link_settings = opsd_get_settings_url();
        

        $wp_admin_bar->add_menu(
                array(
                    'id' => 'bar_opsd',
                    'title' => $update_title ,
                    'href' => opsd_get_master_url()
                    )
                );
        
		// Add also  to  "+ New" bar menu  link
        $wp_admin_bar->add_menu(
                array(
                    'id' => 'bar_opsd_send',
                    'title' => __( 'Secure Link', 'secure-downloads'),
                    'href' => opsd_get_master_url(), 
                    'parent' => 'new-content',
                )
        );
        
         $curr_user_role_settings = get_opsd_option( 'opsd_user_role_settings' );
         $level = 10;
         if ($curr_user_role_settings == 'administrator')       $level = 10;
         else if ($curr_user_role_settings == 'editor')         $level = 7;
         else if ($curr_user_role_settings == 'author')         $level = 2;
         else if ($curr_user_role_settings == 'contributor')    $level = 1;
         else if ($curr_user_role_settings == 'subscriber')     $level = 0;

         if (   ( ($current_user->user_level < $level)   ) || !is_admin_bar_showing() ) return;
 
        $wp_admin_bar->add_menu(
                array(
                    'id' => 'bar_opsd_new',
                    'title' => __( 'Add New', 'secure-downloads'),
                    'href' => opsd_get_new_opsd_url(),
                    'parent' => 'bar_opsd',
                )
        );
        
        
        
        $wp_admin_bar->add_menu(
                array(
                    'id' => 'bar_opsd_settings',
                    'title' => __( 'Settings', 'secure-downloads'),
                    'href' => opsd_get_settings_url(),
                    'parent' => 'bar_opsd',
                )
        );
        
                $wp_admin_bar->add_menu(
                        array(
                            'id' => 'bar_opsd_settings_email',
                            'title' => __( 'Emails', 'secure-downloads'),
                            'href' => $link_settings . '&tab=email',
                            'parent' => 'bar_opsd_settings'
                        )
                );

    }




    function opsd_show_opsd_footer(){ 
        
        if ( ! opsd_is_this_demo() ) {
            
            $message = sprintf( __( 'If you like %s please leave us a %s rating. A huge thank you in advance!', 'secure-downloads')
                                , '<strong>Secure Downloads</strong>' . ' ' . OPSD_VERSION_NUM  
                                , '<a href="https://wordpress.org/support/plugin/secure-downloads/reviews/#new-post" target="_blank" title="' . esc_attr__( 'Thanks :)', 'secure-downloads') . '">'
                                    . '&#9733;&#9733;&#9733;&#9733;&#9733;' 
                                    . '</a>' 
                            );            
            
            echo '<div id="opsd-footer" style="position:absolute;bottom:40px;text-align:left;width:95%;font-size:0.9em;text-shadow:0 1px 0 #fff;margin:0;color:#888;">' . $message . '</div>';
            ?>
            <script type="text/javascript">
                jQuery(document).ready(function(){
                    jQuery('#wpfooter').append( jQuery('#opsd-footer') );
                });
            </script>
            <?php
        }
    }
    
    
    
    
////////////////////////////////////////////////////////////////////////////////
//  Support functions
////////////////////////////////////////////////////////////////////////////////

function get_opsd_current_user_id() {
    $user = wp_get_current_user();
    return ( isset( $user->ID ) ? (int) $user->ID : 0 );
}


/** Check  if Current User have specific Role
 * 
 * @return bool Whether the current user has the given capability. 
 */
function opsd_is_current_user_have_this_role( $user_role ) {
    
   if ( $user_role == 'administrator' )  $user_role = 'activate_plugins';
   if ( $user_role == 'editor' )         $user_role = 'publish_pages';
   if ( $user_role == 'author' )         $user_role = 'publish_posts';
   if ( $user_role == 'contributor' )    $user_role = 'edit_posts';
   if ( $user_role == 'subscriber')      $user_role = 'read';
   
   return current_user_can( $user_role );
}


function opsd_get_user_ip() {
//return '84.243.195.114'  ;                    // Test     //90.36.89.174
    if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $userIP = $_SERVER['HTTP_CLIENT_IP'] ;
    } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $userIP = $_SERVER['HTTP_X_FORWARDED_FOR'] ;
    } elseif (isset($_SERVER['HTTP_X_FORWARDED'])) {
            $userIP = $_SERVER['HTTP_X_FORWARDED'] ;
    } elseif (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
            $userIP = $_SERVER['HTTP_FORWARDED_FOR'] ; 
    } elseif (isset($_SERVER['HTTP_FORWARDED'])) {
            $userIP = $_SERVER['HTTP_FORWARDED'] ;
    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $userIP = $_SERVER['REMOTE_ADDR'] ;
    } else {
            $userIP = "" ;
    }
	
	$userIP = explode( ',', $userIP );
	$userIP = array_map( 'trim', $userIP );
		
    return $userIP[0] ;
}
add_opsd_filter('opsd_get_user_ip', 'opsd_get_user_ip');


/** Transform the REQESTS parameters (GET and POST) into URL
 * 
 * @param type $page_param
 * @param array $exclude_params
 * @param type $only_these_parameters
 * @return type
 */
function opsd_get_params_in_url( $page_param , $exclude_params = array(), $only_these_parameters = false, $is_escape_url = false, $only_get = false ){

    $exclude_params[] = 'page';
    
    if ( isset( $_GET['page'] ) ) 
        $page_param = $_GET['page'];
    
    $get_paramaters = array( 'page' => $page_param );
    
    if ( $only_get )
        $check_params = $_GET;
    else 
        $check_params = $_REQUEST;
//debuge($check_params);    
    foreach ( $check_params as $prm_key => $prm_value ) {
        
        // Skip  parameters arrays,  like $_GET['rvaluation_to'] = Array ( [0] => 6,  [1] => 14,  [2] => 14 )
        if ( 
               (  is_string( $prm_value ) )  
            || ( is_numeric( $prm_value ) ) 
            ) {    
            
            if ( strlen( $prm_value ) > 1000 ) {                                    // Check  about TOOO long parameters,  if it exist  then  reset it.
                $prm_value = '';
            }

            if ( ! in_array( $prm_key, $exclude_params ) )
                if ( ( $only_these_parameters === false ) || ( in_array( $prm_key, $only_these_parameters ) ) )
                        $get_paramaters[ $prm_key ] = $prm_value;
        }
    }
//debuge($check_params, $get_paramaters, $exclude_params );    
    $url = admin_url( add_query_arg(  $get_paramaters , 'admin.php' ) );
    
    if ( $is_escape_url )
        $url = esc_url( $url );
    
    return $url;
    
    /*      // Old variant:
            if ( isset( $_GET['page'] ) ) $page_param = $_GET['page'];

            $url_start = 'admin.php?page=' . $page_param . '&';    
            $exclude_params[] = 'page';
            foreach ( $_REQUEST as $prm_key => $prm_value ) {

                if ( !in_array( $prm_key, $exclude_params ) )
                    if ( ($only_these_parameters === false) || ( in_array( $prm_key, $only_these_parameters ) ) )

                        $url_start .= $prm_key . '=' . $prm_value . '&';

            }
            $url_start = substr( $url_start, 0, -1 );

            return $url_start;
     */     
}




////////////////////////////////////////////////////////////////////////////////    
// Mesages for Admin panel 
////////////////////////////////////////////////////////////////////////////////    

function opsd_show_fixed_message( $message, $time_to_show , $message_type = 'updated' , $notice_id = 0, $is_dismissible = false ) {

	    // Generate unique HTML ID  for the message
		if ( $notice_id == 0 )
			$notice_id =  intval( time() * rand(10, 100) );

		$notice_id = 'opsd_system_notice_' . $notice_id;

		$is_dismissible = false;
		
		if ( 
			   ( ( $is_dismissible ) && ( ! opsd_section_is_dismissed( $notice_id ) ) )
			|| ( ! $is_dismissible )
			 // || true 
		){

			?><div  id="<?php echo $notice_id; ?>" 
					class="opsd_system_notice opsd_is_dismissible opsd_is_hideable <?php echo $message_type; ?>"
					data-nonce="<?php echo wp_create_nonce( $nonce_name = $notice_id . '_opsdnonce' ); ?>"	
					data-user-id="<?php echo get_current_user_id(); ?>"
				><?php 
			
			opsd_x_dismiss_button();
			
			echo $message;
			
			?></div><?php
			
			// Get the time of message showing
			$time_to_show = intval( $time_to_show ) * 1000;
			
			 if ( $time_to_show > 0 ) { 
				?> <script type="text/javascript">                              				
						jQuery('#<?php echo $notice_id; ?>').animate({opacity: 1},<?php echo $time_to_show; ?>).fadeOut( 2000 );								
				</script> <?php
			 }			
		}       	
}

// Show Ajax message at the top of page //////////////////////////////////////////////////////////////////////////////////////////////////////

function opsd_show_ajax_message( $message, $time_to_show = 3000, $is_error = false ) {

    // Recheck  for any "lang" shortcodes for replacing to correct language
    $message =  apply_opsd_filter('opsd_check_for_active_language', $message );

    // Escape any JavaScript from  message
    $notice =   html_entity_decode( esc_js( $message ) ,ENT_QUOTES) ;
    
    ?><script type="text/javascript">
        var my_message = '<?php echo $notice; ?>';
        opsd_admin_show_message( my_message, '<?php echo ( $is_error ? 'error' : 'success' ); ?>', <?php echo $time_to_show; ?> );                                                                      
    </script><?php
}


/** Show "Saved Changes" message at  the top  of settings page.
 * 
 */    
function opsd_show_changes_saved_message() {
    opsd_show_message ( __('Changes saved.', 'secure-downloads'), 5 );
}    


/** Show Message at  Top  of Admin Pages
 * 
 * @param type $message         - mesage to  show
 * @param type $time_to_show    - number of seconds to  show, if 0 or skiped,  then unlimited time.
 * @param type $message_type    - Default: updated   { updated | error | notice }
 */
function opsd_show_message ( $message, $time_to_show , $message_type = 'updated') {
        
    // Generate unique HTML ID  for the message
    $inner_message_id =  intval( time() * rand(10, 100) );

    // Get formated HTML message
    $notice = opsd_get_formated_message( $message, $message_type, $inner_message_id );

    // Get the time of message showing
    $time_to_show = intval( $time_to_show ) * 1000;

    // Show this Message
    ?> <script type="text/javascript">                              
        if ( jQuery('.opsd_admin_message').length ) {
                jQuery('.opsd_admin_message').append( '<?php echo $notice; ?>' );
            <?php if ( $time_to_show > 0 ) { ?>
                jQuery('#opsd_inner_message_<?php echo $inner_message_id; ?>').animate({opacity: 1},<?php echo $time_to_show; ?>).fadeOut( 2000 );
            <?php } ?>
        }
    </script> <?php
}


/** Escape and prepare message to  show it
 * 
 * @param type $message                 - message
 * @param type $message_type            - Default: updated   { updated | error | notice }
 * @param string $inner_message_id      - ID of message DIV,  can  be skipped
 * @return string
 */
function opsd_get_formated_message ( $message, $message_type = 'updated', $inner_message_id = '') {
        

    // Recheck  for any "lang" shortcodes for replacing to correct language
    $message =  apply_opsd_filter('opsd_check_for_active_language', $message );

    // Escape any JavaScript from  message
    $notice =   html_entity_decode( esc_js( $message ) ,ENT_QUOTES) ;

    $notice .= '<a class="close tooltip_left" rel="tooltip" title="'. esc_js(__("Hide", 'secure-downloads')). '" data-dismiss="alert" href="javascript:void(0)" onclick="javascript:jQuery(this).parent().hide();">&times;</a>';

    if (! empty( $inner_message_id ))
        $inner_message_id = 'id="opsd_inner_message_'. $inner_message_id .'"';

    $notice = '<div '.$inner_message_id.' class="opsd_inner_message '. $message_type . '">' . $notice . '</div>';

    return  $notice;
}


/** Show system info  in settings page
 * 
 * @param string $message                     ...  
 * @param string $message_type                'info' | 'warning' | 'error'
 * @param string $title                       __('Important!' , 'secure-downloads')  |  __('Note' , 'secure-downloads')
 * 
 * Exmaple:     opsd_show_message_in_settings( __( 'Nothing Found', 'secure-downloads'), 'warning', __('Important!' , 'secure-downloads') );
 */
function opsd_show_message_in_settings( $message, $message_type = 'info', $title = '' , $is_echo = true ) {
    
    $message_content = '';
    
    $message_content .= '<div class="clear"></div>';
    
    $message_content .= '<div class="opsd-settings-notice notice-' . $message_type . '" style="text-align:left;">';
    
    if ( ! empty( $title ) )
        $message_content .=  '<strong>' . esc_js( $title ) . '</strong> ';
        
    $message_content .= html_entity_decode( esc_js( $message ) ,ENT_QUOTES) ;
            
    $message_content .= '</div>';
    
    $message_content .= '<div class="clear"></div>';
    
    if ( $is_echo )
        echo $message_content;
    else
        return $message_content;
        
}

////////////////////////////////////////////////////////////////////////////////    
// Settings Meta Boxes
////////////////////////////////////////////////////////////////////////////////    
function opsd_open_meta_box_section( $metabox_id, $title ) {
    
    $my_close_open_win_id = $metabox_id . '_metabox';
    ?>
    <div class='meta-box'>
        <div 
                id="<?php echo $my_close_open_win_id; ?>" 
                class="postbox <?php if ( '1' == get_user_option( 'opsd_win_' . $my_close_open_win_id ) ) echo 'closed'; ?>" 
            > <div  title="<?php _e('Click to toggle', 'secure-downloads'); ?>" 
                    class="handlediv" 
                    onclick="javascript:opsd_verify_window_opening(<?php echo get_opsd_current_user_id(); ?>, '<?php echo $my_close_open_win_id; ?>');"
                ><br/></div>
              <h3 class='hndle'>
                  <span><?php  echo wp_kses_post( $title ); ?></span>
              </h3>      
              <div class="inside">
    <?php        
}

function opsd_close_meta_box_section() {
    ?>
              </div> 
        </div> 
    </div>                        
    <?php
}


////////////////////////////////////////////////////////////////////////////////
//  P a g i n a t i o n    o f    T a b l e    L  i s t i n g    ///////////////
////////////////////////////////////////////////////////////////////////////////
/** Show    P a g i n a t i o n
 * 
 * @param int $summ_number_of_items     - total  number of items
 * @param int $active_page_num          - number of activated page
 * @param int $num_items_per_page       - number of items per page
 * @param array $only_these_parameters  - array of keys to exclude from links
 * @param string $url_sufix             - usefule for anchor to  HTML section  with  specific ID,  Example: '#my_section'
 */
function opsd_show_pagination( $summ_number_of_items, $active_page_num, $num_items_per_page , $only_these_parameters = false, $url_sufix = '' ) {
        
    if ( empty( $num_items_per_page ) ) {
        $num_items_per_page = '10';
    }

    $pages_number = ceil( $summ_number_of_items / $num_items_per_page );
    if ( $pages_number < 2 )
        return;

            //Fix: 5.1.4 - Just in case we are having tooo much  resources, then we need to show all resources - and its empty string
            if ( ( isset($_REQUEST['wh_opsd_type'] ) ) && ( strlen($_REQUEST['wh_opsd_type']) > 1000 ) ) {                   
                $_REQUEST['wh_opsd_type'] = '';            
            }  
        
    // First  parameter  will overwriten by $_GET['page'] parameter
    $bk_admin_url = opsd_get_params_in_url( opsd_get_master_url( false ), array('page_num'), $only_these_parameters );

    
    ?>
    <span class="wpdevelop opsd-pagination">
        <div class="container-fluid">  
            <div class="row">
                <div class="col-sm-12 text-center control-group0">
                    <nav class="btn-toolbar">
                      <div class="btn-group opsd-no-margin" style="float:none;">

                        <?php if ( $pages_number > 1 ) { ?>
                                <a class="button button-secondary <?php echo ( $active_page_num == 1 ) ? ' disabled' : ''; ?>" 
                                   href="<?php echo $bk_admin_url; ?>&page_num=<?php if ($active_page_num == 1) { echo $active_page_num; } else { echo ($active_page_num-1); } echo $url_sufix; ?>">
                                    <?php _e('Prev', 'secure-downloads'); ?>
                                </a>
                        <?php } 

                        /** Number visible pages (links) that linked to active page, other pages skipped by "..." */
                        $num_closed_steps = 3;
                        
                        for ( $pg_num = 1; $pg_num <= $pages_number; $pg_num++ ) {
                             
                                if ( ! ( 
                                           ( $pages_number > ( $num_closed_steps * 4) ) 
                                        && ( $pg_num > $num_closed_steps ) 
                                        && ( ( $pages_number - $pg_num + 1 ) > $num_closed_steps ) 
                                        && (  abs( $active_page_num - $pg_num ) > $num_closed_steps )  
                                   ) ) {
                                    ?> <a class="button button-secondary <?php if ($pg_num == $active_page_num ) echo ' active'; ?>" 
                                         href="<?php echo $bk_admin_url; ?>&page_num=<?php echo $pg_num;  echo $url_sufix; ?>">
                                        <?php echo $pg_num; ?>
                                      </a><?php 
                                      
                                    if ( ( $pages_number > ( $num_closed_steps * 4) ) 
                                            && ( ($pg_num+1) > $num_closed_steps ) 
                                            && ( ( $pages_number - ( $pg_num + 1 ) ) > $num_closed_steps ) 
                                            &&  ( abs($active_page_num - ( $pg_num + 1 ) ) > $num_closed_steps )  
                                        ) {
                                        echo ' <a class="button button-secondary disabled" href="javascript:void(0);">...</a> ';
                                    }
                                }
                        }

                        if ( $pages_number > 1 ) { ?>
                                <a class="button button-secondary <?php echo ( $active_page_num == $pages_number ) ? ' disabled' : ''; ?>" 
                                   href="<?php echo $bk_admin_url; ?>&page_num=<?php  if ($active_page_num == $pages_number) { echo $active_page_num; } else { echo ($active_page_num+1); }  echo $url_sufix; ?>">
                                    <?php _e('Next', 'secure-downloads'); ?>
                                </a>
                        <?php } ?>

                      </div>
                    </nav>
                </div>
            </div>
        </div>
    </span>
    <?php
}



////////////////////////////////////////////////////////////////////////////////
// Inline JavaScript to Footer page
////////////////////////////////////////////////////////////////////////////////
/**
 * Queue  JavaScript for later output at  footer
 *
 * @param string $code
 */
function opsd_enqueue_js( $code ) {
    global $opsd_queued_js;

    if ( empty( $opsd_queued_js ) ) {
        $opsd_queued_js = '';
    }

    $opsd_queued_js .= "\n" . $code . "\n";
}


/**
 * Output any queued javascript code in the footer.
 */
function opsd_print_js() {
    
    global $opsd_queued_js;

    if ( ! empty( $opsd_queued_js ) ) {

        echo "<!-- OPSD JavaScript -->\n<script type=\"text/javascript\">\njQuery(function($) {";

        $opsd_queued_js = wp_check_invalid_utf8( $opsd_queued_js );
        
        $opsd_queued_js = wp_specialchars_decode( $opsd_queued_js , ENT_COMPAT);            // Converts double quotes  '&quot;' => '"'
        
        $opsd_queued_js = preg_replace( '/&#(x)?0*(?(1)27|39);?/i', "'", $opsd_queued_js );
        $opsd_queued_js = str_replace( "\r", '', $opsd_queued_js );

        echo $opsd_queued_js . "});\n</script>\n<!-- End OPSD JavaScript -->\n";

        $opsd_queued_js = '';
        unset( $opsd_queued_js );
    }
}


/**
 * Reload page by using JavaScript
 * 
 * @param string $url - URL of page to  load
 */
function opsd_reload_page_by_js( $url ) {

    $redir = html_entity_decode( esc_url( $url ) );
    
    if ( ! empty( $redir ) ) {
        ?>
        <script type="text/javascript">                
            window.location.href = '<?php echo $redir ?>';                
        </script>
        <?php
    }
}


/** Redirect browser to a specific page
 * 
 * @param string $url - URL of page to redirect
 */
function opsd_redirect( $url ) {
    
    $url = opsd_make_link_absolute( $url );
    
    $url = html_entity_decode( esc_url( $url ) );
    
    echo '<script type="text/javascript">';
    echo 'window.location.href="'.$url.'";';
    echo '</script>';
    echo '<noscript>';
    echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';
    echo '</noscript>';
}



/** Get Warning Text  for Demo websites */
function opsd_get_warning_text_in_demo_mode() {
    // return '<div class="opsd-error-message opsd_demo_test_version_warning"><strong>Warning!</strong> Demo test version does not allow changes to these items.</div>'; //Old Style
    return '<div class="opsd-settings-notice notice-warning"><strong>Warning!</strong> Demo test version does not allow changes to these items.</div>';
}



 /** Show System Info (status) at item > Settings General page
  *  Link: http://server.com/wp-admin/admin.php?page=opsd-settings&system_info=show#opsd_general_settings_system_info_metabox
  */
function opsd_system_info() {

    if ( opsd_is_this_demo() ) return;
        
    if ( current_user_can( 'activate_plugins' ) ) {                                // Only for Administrator or Super admin. More here: https://codex.wordpress.org/Roles_and_Capabilities
        
        global $wpdb, $wp_version;
        
        $all_plugins = get_plugins();
        $active_plugins = get_option( 'active_plugins' );
        
        $mysql_info = $wpdb->get_results( "SHOW VARIABLES LIKE 'sql_mode'" );
        if ( is_array( $mysql_info ) )  $sql_mode = $mysql_info[0]->Value;
        if ( empty( $sql_mode ) )       $sql_mode = 'Not set';

        $safe_mode          = ( ini_get( 'safe_mode' ) ) ? 'On' : 'Off';
        $allow_url_fopen    = ( ini_get( 'allow_url_fopen' ) ) ?  'On' : 'Off';
        $upload_max_filesize = ( ini_get( 'upload_max_filesize' ) ) ? ini_get( 'upload_max_filesize' ) : 'N/A';
        $post_max_size      = ( ini_get( 'post_max_size' ) ) ? ini_get( 'post_max_size' ) : 'N/A';
        $max_execution_time = ( ini_get( 'max_execution_time' ) ) ? ini_get( 'max_execution_time' ) : 'N/A';
        $memory_limit       = ( ini_get( 'memory_limit' ) ) ? ini_get( 'memory_limit' ) : 'N/A';
        $memory_usage       = ( function_exists( 'memory_get_usage' ) ) ? round( memory_get_usage() / 1024 / 1024, 2 ) . ' Mb' : 'N/A';
        $exif_read_data     = ( is_callable( 'exif_read_data' ) ) ? 'Yes' . " ( V" . substr( phpversion( 'exif' ), 0, 4 ) . ")" : 'No';
        $iptcparse          = ( is_callable( 'iptcparse' ) ) ? 'Yes' : 'No';
        $xml_parser_create  = ( is_callable( 'xml_parser_create' ) ) ? 'Yes' : 'No';
        $theme              = ( function_exists( 'wp_get_theme' ) ) ? wp_get_theme() : get_theme( get_current_theme() );

        if ( function_exists( 'is_multisite' ) ) {
            if ( is_multisite() )   $multisite = 'Yes';
            else                    $multisite = 'No';
        } else {                    $multisite = 'N/A';
        }

        $system_info = array(
            'system_info' => '',
            'php_info' => '',
            'active_plugins' => '',
            'inactive_plugins' => ''
        );
            
        $ver_small_name = get_opsd_version();
        if ( class_exists( 'opsd_multiuser' ) ) $ver_small_name = 'multiuser';
        
        $system_info['system_info'] = array(
            'Plugin Update'         => ( defined( 'OPSD_VERSION' ) ) ? OPSD_VERSION : 'N/A',
            'Plugin Version'        => ucwords( $ver_small_name ),
            'Plugin Update Date'   => date( "Y-m-d", filemtime( OPSD_FILE ) ),
            
            'WP Version' => $wp_version,
            'WP DEBUG'   =>  ( ( defined('WP_DEBUG') ) && ( WP_DEBUG ) ) ? 'On' : 'Off',
            'WP DB Version' => get_option( 'db_version' ),
            'Operating System' => PHP_OS,
            'Server' => $_SERVER["SERVER_SOFTWARE"],
            'PHP Version' => PHP_VERSION,
            'PHP Safe Mode' => $safe_mode,
            'MYSQL Version' => $wpdb->get_var( "SELECT VERSION() AS version" ),
            'SQL Mode' => $sql_mode,
            'Memory usage' => $memory_usage,
            'Site URL' => get_option( 'siteurl' ),
            'Home URL' => home_url(),
            'SERVER[HTTP_HOST]' => $_SERVER['HTTP_HOST'],
            'SERVER[SERVER_NAME]' => $_SERVER['SERVER_NAME'],
            'Multisite' => $multisite,
            'Active Theme' => $theme['Name'] . ' ' . $theme['Version']
        );
        
        $system_info['php_info'] = array(
            'PHP Version' => PHP_VERSION,
            'PHP Safe Mode' => $safe_mode,
                'PHP Memory Limit'              => '<strong>' . $memory_limit . '</strong>',
                'PHP Max Script Execute Time'   => '<strong>' . $max_execution_time . '</strong>',
                
                'PHP Max Post Size'  => '<strong>' . $post_max_size . '</strong>',
                'PHP MAX Input Vars' => '<strong>' . ( ( ini_get( 'max_input_vars' ) ) ? ini_get( 'max_input_vars' ) : 'N/A' ) . '</strong>',           //How many input variables may be accepted (limit is applied to $_GET, $_POST and $_COOKIE superglobal separately).                 
            
            'PHP Max Upload Size'   => $upload_max_filesize,
            'PHP Allow URL fopen'   => $allow_url_fopen,
            'PHP Exif support'      => $exif_read_data,
            'PHP IPTC support'      => $iptcparse,
            'PHP XML support'       => $xml_parser_create            
        );
                
        $system_info['php_info']['PHP cURL'] =  ( function_exists('curl_init') ) ? 'On' : 'Off';   
        $system_info['php_info']['Max Nesting Level'] = ( ( ini_get( 'max_input_nesting_level' ) ) ? ini_get( 'max_input_nesting_level' ) : 'N/A' );   
        $system_info['php_info']['Max Time 4 script'] = ( ( ini_get( 'max_input_time' ) ) ? ini_get( 'max_input_time' ) : 'N/A' );                     //Maximum amount of time each script may spend parsing request data
        $system_info['php_info']['Log'] =      ( ( ini_get( 'error_log' ) ) ? ini_get( 'error_log' ) : 'N/A' );
        
        if ( ini_get( "suhosin.get.max_value_length" ) ) { 
            
            $system_info['suhosin_info'] = array();
            $system_info['suhosin_info']['POST max_array_index_length']     = ( ( ini_get( 'suhosin.post.max_array_index_length' ) ) ? ini_get( 'suhosin.post.max_array_index_length' ) : 'N/A' );
            $system_info['suhosin_info']['REQUEST max_array_index_length']  = ( ( ini_get( 'suhosin.request.max_array_index_length' ) ) ? ini_get( 'suhosin.request.max_array_index_length' ) : 'N/A' );
            
            $system_info['suhosin_info']['POST max_totalname_length']    = ( ( ini_get( 'suhosin.post.max_totalname_length' ) ) ? ini_get( 'suhosin.post.max_totalname_length' ) : 'N/A' );
            $system_info['suhosin_info']['REQUEST max_totalname_length'] = ( ( ini_get( 'suhosin.request.max_totalname_length' ) ) ? ini_get( 'suhosin.request.max_totalname_length' ) : 'N/A' );
            
            $system_info['suhosin_info']['POST max_vars']               = ( ( ini_get( 'suhosin.post.max_vars' ) ) ? ini_get( 'suhosin.post.max_vars' ) : 'N/A' );
            $system_info['suhosin_info']['REQUEST max_vars']            = ( ( ini_get( 'suhosin.request.max_vars' ) ) ? ini_get( 'suhosin.request.max_vars' ) : 'N/A' );
            
            $system_info['suhosin_info']['POST max_value_length']       = ( ( ini_get( 'suhosin.post.max_value_length' ) ) ? ini_get( 'suhosin.post.max_value_length' ) : 'N/A' );
            $system_info['suhosin_info']['REQUEST max_value_length']    = ( ( ini_get( 'suhosin.request.max_value_length' ) ) ? ini_get( 'suhosin.request.max_value_length' ) : 'N/A' );
            
            $system_info['suhosin_info']['POST max_name_length']        = ( ( ini_get( 'suhosin.post.max_name_length' ) ) ? ini_get( 'suhosin.post.max_name_length' ) : 'N/A' );
            $system_info['suhosin_info']['REQUEST max_varname_length']  = ( ( ini_get( 'suhosin.request.max_varname_length' ) ) ? ini_get( 'suhosin.request.max_varname_length' ) : 'N/A' );
            
            $system_info['suhosin_info']['POST max_array_depth']        = ( ( ini_get( 'suhosin.post.max_array_depth' ) ) ? ini_get( 'suhosin.post.max_array_depth' ) : 'N/A' );            
            $system_info['suhosin_info']['REQUEST max_array_depth']     = ( ( ini_get( 'suhosin.request.max_array_depth' ) ) ? ini_get( 'suhosin.request.max_array_depth' ) : 'N/A' );
        }

        
        if ( function_exists('gd_info') ) {
            $gd_info = gd_info();
            if ( isset( $gd_info['GD Version'] ) )
                $gd_info = $gd_info['GD Version'];
            else 
                $gd_info = json_encode( $gd_info );
        } else {
            $gd_info = 'Off';
        }
        $system_info['php_info']['PHP GD'] = $gd_info;

        // More here https://docs.woocommerce.com/document/problems-with-large-amounts-of-data-not-saving-variations-rates-etc/

        
        foreach ( $all_plugins as $path => $plugin ) {
            if ( is_plugin_active( $path ) )
                $system_info['active_plugins'][$plugin['Name']] = $plugin['Version'];
            else
                $system_info['inactive_plugins'][$plugin['Name']] = $plugin['Version'];
        }

        // Showing
        foreach ( $system_info as $section_name => $section_values ) {
            ?>
            <span class="wpdevelop">
            <table class="table table-striped table-bordered">
                <thead><tr><th colspan="2" style="border-bottom: 1px solid #eeeeee;padding: 10px;"><?php echo strtoupper( $section_name ); ?></th></tr></thead>
                <tbody>
                <?php 
                if ( !empty( $section_values ) ) {
                    foreach ( $section_values as $key => $value ) {
                        ?>
                        <tr>
                            <td scope="row" style="width:18em;padding:4px 8px;"><?php echo $key; ?></td>
                            <td scope="row" style="padding:4px 8px;"><?php echo $value; ?></td>
                        </tr>
                        <?php                 
                    }
                }
                ?>
                </tbody>
            </table>
            </span>
            <div class="clear"></div>
            <?php
        }
?>
<hr>            
<div style="color:#777;">
<h4 style="font-size:1.1em;">Commonly required configuration vars in php.ini file:</h4>            
<h4>General section:</h4>            
<pre><code>memory_limit = 256M
 max_execution_time = 120
 post_max_size = 8M
 upload_max_filesize = 8M
 max_input_vars = 20480
 post_max_size = 64M</code></pre>  
<h4>Suhosin section (if installed):</h4>
<pre><code>suhosin.post.max_array_index_length = 1024
 suhosin.post.max_totalname_length = 65535
 suhosin.post.max_vars = 2048
 suhosin.post.max_value_length = 1000000
 suhosin.post.max_name_length = 256
 suhosin.post.max_array_depth = 1000
 suhosin.request.max_array_index_length = 1024
 suhosin.request.max_totalname_length = 65535
 suhosin.request.max_vars = 2048
 suhosin.request.max_value_length = 1000000
 suhosin.request.max_varname_length = 256
 suhosin.request.max_array_depth = 1000</code></pre> 
</div>
<?php 
        // phpinfo();        
    }
}


////////////////////////////////////////////////////////////////////////////////
// Support functions for MU version
////////////////////////////////////////////////////////////////////////////////






////////////////////////////////////////////////////////////////////////////////
// Support Specials
////////////////////////////////////////////////////////////////////////////////

/** Support 'hash_equals' this function at older servers than PHP 5.6.0 */
if ( !function_exists( 'hash_equals' ) ) {
    function hash_equals( $known_string, $user_string ) {
        $ret = 0;

        if ( strlen( $known_string ) !== strlen( $user_string ) ) {
            $user_string = $known_string;
            $ret = 1;
        }

        $res = $known_string ^ $user_string;

        for ( $i = strlen( $res ) - 1; $i >= 0; --$i ) {
            $ret |= ord( $res[$i] );
        }

        return !$ret;
    }
}


/** Check if this valid timestamp
 * 
 * @param string|int $timestamp
 * @return bool
 */
function opsd_is_valid_timestamp( $timestamp ) {
    return (   ( (string) (int) $timestamp === $timestamp) 
			&& ($timestamp <= PHP_INT_MAX)
			&& ($timestamp >= ~PHP_INT_MAX) 
		   );
}


/** Create Blank  files in protected dir.
 * 
 * @return string - CSV content
 */
function opsd_create_blank_files() {
	
	// Get OPSD_Upload obj. instance
	$opsd_upload = opsd_upload();	
	
	// Protected secret name LEVEL 1
	$dir_level1 = $opsd_upload->get_protected_dir_name();
		
	// Install files and folders for uploading files and prevent hotlinking
	$upload_dir = wp_upload_dir();

	$files = array(
					array(
						'base'    => $upload_dir['basedir'] . '/' . $dir_level1,
						'baseurl' => $upload_dir['baseurl'] . '/' . $dir_level1,
						'file'    => 'test.txt',
						'content' =>  'Test File' . "\n"
					)
					, array(
						'base'    => $upload_dir['basedir'] . '/' . $dir_level1,
						'baseurl' => $upload_dir['baseurl'] . '/' . $dir_level1,
						'file'    => 'test.html',
						'content' =>  '<!DOCTYPE html>' . "\n"
									. '<html>' . "\n"
									. '    <head>' . "\n"
									. '        <title>Test HTML File</title>' . "\n"
									. '        <meta charset="UTF-8">' . "\n"
									. '        <meta name="viewport" content="width=device-width, initial-scale=1.0">' . "\n"
									. '    </head>' . "\n"
									. '    <body>' . "\n"
									. '        <div>Test HTML Content</div>' . "\n"
									. '    </body>' . "\n"
									. '</html>' . "\n"
					)
		);

	foreach ( $files as $file ) {

		if (   ( wp_mkdir_p( $file['base'] ) )												// Recursive directory creation based on full path.
			&& ( ! file_exists( trailingslashit( $file['base'] ) . $file['file'] ) )		// If file not exist
		) {

			if ( $file_handle = @fopen( trailingslashit( $file['base'] ) . $file['file'], 'w' ) ) {

				fwrite( $file_handle, $file['content'] );
				fclose( $file_handle );
			}
		}
	}

	
	
	$new_products_arr = array();
	$new_products_arr[] = array( 'title' => 'Text Files' );	
	$product_arr = array();
	$product_arr[ 'id' ] = 1;
	$product_arr[ 'title' ] = 'Text File';
	$product_arr[ 'version_num' ] = '1.0';
	$product_arr[ 'description' ] = 'Simple TXT file';
	$product_arr[ 'path' ] =  trim( str_replace( site_url(), '',    trailingslashit( $files[0]['baseurl'] ) . $files[0]['file'] )  );	
	$new_products_arr[] = $product_arr;	
	
	$new_products_arr[] = array( 'title' => 'HTML Files' );	
	$product_arr = array();
	$product_arr[ 'id' ] = 2;
	$product_arr[ 'title' ] = 'HTML File';
	$product_arr[ 'version_num' ] = '1.0';
	$product_arr[ 'description' ] = 'Test HTML file';
	$product_arr[ 'path' ] =  trim( str_replace( site_url(), '',    trailingslashit( $files[1]['baseurl'] ) . $files[1]['file'] ) );	
	$new_products_arr[] = $product_arr;
		
	$products_obj = new OPSD_Products();
	$products_csv = $products_obj->save_products( $new_products_arr );	
	
	return  $products_csv;
}