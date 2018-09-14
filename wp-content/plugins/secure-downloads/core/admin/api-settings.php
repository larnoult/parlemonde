<?php
/**
 * @version     1.0
 * @package     General Settings API - Saving different options
 * @category    Settings API
 * @author      wpdevelop
 *
 * @web-site    http://oplugins.com/
 * @email       info@oplugins.com 
 * @modified    2016-02-24
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly


// General Settings API - Saving different options
class  OPSD_Settings_API_General extends OPSD_Settings_API {
    

    /**  Override Settings API Constructor
     *   During creation,  system try to load values from DB, if exist.
     * 
     *  @param type $id - of Settings
     */
    public function __construct( $id = '' ){
          
        $options = array( 
                        'db_prefix_option' => ''                                // 'opsd_' 
                      , 'db_saving_type'   => 'separate' 
                      , 'id'               => 'set_gen'
            ); 
        
        $id = empty($id) ? $options['id'] : $id;
                
        parent::__construct( $id, $options );                                   // Define ID of Setting page and options
                
        add_action( 'opsd_after_settings_content', array($this, 'enqueue_js'), 10, 3 );
    }

    
    /** Init all fields rows for settings page */
    public function init_settings_fields() {
        
        $this->fields = array();

        $default_options_values = opsd_get_default_options();

        
        //                                                                              <editor-fold   defaultstate="collapsed"   desc=" G e n e r a l " >
        /*        
         wp_redirect:
                        /wrong-hash
                        /download-expired
                        /ip-not-valied
                        /no-such-product
                        /product-not-exist
        */ 
 
        // Redirection Path
        $this->fields['opsd_download_url_path'] = array(  
                                'type'          => 'text'
                                , 'default'     => ''
                                , 'group'       => 'general'
                                , 'placeholder' => '/file-download/'
                                , 'css'         => 'width:100%;'
                                , 'title'       => __('URL Path', 'secure-downloads')
                                , 'description' => '(' . __('Optional' , 'secure-downloads') . ') ' . __('Enter URL path, that will exist in secret URL' , 'secure-downloads')
            , 'description_tag' => ''

            );    
        
        // Redirection Path
        $this->fields['opsd_protected_directory_name_level1'] = array(  
                                'type'          => 'text'
                                , 'default'     => ''
                                , 'group'       => 'general'
                                , 'placeholder' => 'opsd_XXXXXXXXXX'
                                , 'css'         => 'width:100%;'
                                , 'title'       => __('Upload Folder', 'secure-downloads')
                                , 'description' => __('Path to upload folder' , 'secure-downloads')
            , 'description_tag' => ''

            );    
        
        // Secret Key
        $this->fields['opsd_secret_key'] = array(  
                                'type'          => 'text'
                                , 'default'     => wp_generate_password( 30, false, false )
                                , 'group'       => 'general'
                                , 'placeholder' => wp_generate_password( 30, false, false )
                                , 'css'         => 'width:100%;'
                                , 'title'       => __('Secret Key', 'secure-downloads')
                                , 'description' => __('Enter your secret key. Secure link hash will be generated from it. Please keep it secure.' , 'secure-downloads')
            , 'description_tag' => ''

            );    
        
        
        // Default Expiration

        $options = array( 
                          '+5 minutes' => '5 ' . __('minutes', 'secure-downloads')     
                        , '+15 minutes' => '15 ' . __('minutes', 'secure-downloads')     
                        , '+30 minutes' => '30 ' . __('minutes', 'secure-downloads')     
                        , '+45 minutes' => '45 ' . __('minutes', 'secure-downloads')     
                        , '+1 hour' => '1 ' . __('hour', 'secure-downloads')     
                        , '+6 hours' => '6 ' . __('hours', 'secure-downloads')     
                        , '+12 hours' => '12 ' . __('hours', 'secure-downloads')     
                        , '+24 hours' => '24 ' . __('hours', 'secure-downloads')     
                        , '+3 days' => '3 ' . __('days', 'secure-downloads')     
                        , '+5 days' => '5 ' . __('days', 'secure-downloads')     
                        , '+7 days' => '7 ' . __('days', 'secure-downloads')     
                        , '+30 days' => '30 ' . __('days', 'secure-downloads')     
                        , '+90 days' => '90 ' . __('days', 'secure-downloads')     
                        , '+365 days' => '1 ' . __('year', 'secure-downloads')                                             
                );
        $this->fields['opsd_defualt_expiration'] = array(   
                                    'type'          => 'select'
                                    , 'default'     => '+24 hours'
                                    //, 'value' => '/css/skins/standard.css'    //This will override value loaded from DB
                                    , 'title'       => __('Default Expiration', 'secure-downloads')
                                    , 'description' => __('Select default expiration time of link' , 'secure-downloads')
                                    , 'options'     => $options
                                    , 'group'       => 'general'
                            );
        
        // Default IP Lock
        $this->fields['opsd_defualt_iplock'] = array(  
                                'type'          => 'text'
                                , 'default'     => ''
                                , 'group'       => 'general'
                                , 'placeholder' => '0.0.0.0'
                                , 'css'         => 'width:17em;'
                                , 'title'       => __('Default IP address loc', 'secure-downloads')
                                , 'description' => __('Enter default IP address for grant access to download file only for specific IP. Or leave empty for having no lock.' , 'secure-downloads')
                                                    . '<div class="opsd-settings-notice notice-info" style="text-align:left">'
                                                            . '<strong>'. __('Note', '') . '!</strong> ' 
                                                            . __('To specify an IP range for grant access, use configuration like', 'secure-downloads') . '  '
                                                            . '<br/>' . __('Example', 'secure-downloads') . ' 1: <strong>195.47.89</strong>'
                                                            . '<br/>' . __('Example', 'secure-downloads') . ' 2: <strong>195.47</strong>'
                                                            . '<br/>' . __('Example', 'secure-downloads') . ' 3: <strong>195</strong>' 
                                                    . '</div>'

            );    
        
		//                                                                              </editor-fold>

		
		//                                                                              <editor-fold   defaultstate="collapsed"   desc=" E r r o r   U R L s " >    
		
		// get_opsd_option( 'opsd_url_wrong_hash' )
		// get_opsd_option( 'opsd_url_download_expired' )
		// get_opsd_option( 'opsd_url_ip_not_valied' )
		// get_opsd_option( 'opsd_url_file_not_exist' )
		// get_opsd_option( 'opsd_url_error_opening_file' )
		
        $name_of_field = 'opsd_url_wrong_hash';
		$this->fields[ $name_of_field . '_prefix' ] = array(
			  'type'	 => 'pure_html'
			, 'group'	 => 'warning_url'
			, 'html'	 => '<tr valign="top" class="opsd_sub_settings_grayed0 opsd_tr_set_gen_' . $name_of_field . ' ">
								<th scope="row">'
									. OPSD_Settings_API::label_static(	  'set_gen_' . $name_of_field
																		, array( 'title' => __( 'Wrong hash', 'secure-downloads' ), 'label_css' => '' )
									)
								. '</th>
								<td><fieldset>' . '<code style="font-size:14px;">' . site_url() . '</code>'
		);
		$this->fields[ $name_of_field ] = array(
								'type'			 => 'text'
							  , 'default'		 => ( isset( $default_options_values[ $name_of_field ] ) ) ? $default_options_values[ $name_of_field ] : ''
							  , 'placeholder'	 => '/error-download'
							  , 'css'			 => 'width:75%'
							  , 'group'			 => 'warning_url'
							  , 'only_field'	 => true
		);
		$this->fields[ $name_of_field . '_sufix' ] = array(
			  'type'	 => 'pure_html'
			, 'group'	 => 'warning_url'
			, 'html'	 =>				  '<p class="description" style="line-height: 1.7em;margin: 0;">'
											. __( 'Type URL of page with warning about wrong hash in link', 'secure-downloads' )
										. '</p>
									</fieldset>
								</td>
							</tr>'
		);
		
        $name_of_field = 'opsd_url_download_expired';
		$this->fields[ $name_of_field . '_prefix' ] = array(
			  'type'	 => 'pure_html'
			, 'group'	 => 'warning_url'
			, 'html'	 => '<tr valign="top" class="opsd_sub_settings_grayed0 opsd_tr_set_gen_' . $name_of_field . ' ">
								<th scope="row">'
									. OPSD_Settings_API::label_static(	  'set_gen_' . $name_of_field
																		, array( 'title' => __( 'Link Expired', 'secure-downloads' ), 'label_css' => '' )
									)
								. '</th>
								<td><fieldset>' . '<code style="font-size:14px;">' . site_url() . '</code>'
		);
		$this->fields[ $name_of_field ] = array(
								'type'			 => 'text'
							  , 'default'		 => ( isset( $default_options_values[ $name_of_field ] ) ) ? $default_options_values[ $name_of_field ] : ''
							  , 'placeholder'	 => '/download-expired'
							  , 'css'			 => 'width:75%'
							  , 'group'			 => 'warning_url'
							  , 'only_field'	 => true
		);
		$this->fields[ $name_of_field . '_sufix' ] = array(
			  'type'	 => 'pure_html'
			, 'group'	 => 'warning_url'
			, 'html'	 =>				  '<p class="description" style="line-height: 1.7em;margin: 0;">'
											. __( 'Type URL of page with warning about expiration of download link', 'secure-downloads' )
										. '</p>
									</fieldset>
								</td>
							</tr>'
		);
		
        $name_of_field = 'opsd_url_ip_not_valied';
		$this->fields[ $name_of_field . '_prefix' ] = array(
			  'type'	 => 'pure_html'
			, 'group'	 => 'warning_url'
			, 'html'	 => '<tr valign="top" class="opsd_sub_settings_grayed0 opsd_tr_set_gen_' . $name_of_field . ' ">
								<th scope="row">'
									. OPSD_Settings_API::label_static(	  'set_gen_' . $name_of_field
																		, array( 'title' => __( 'IP Not Valid', 'secure-downloads' ), 'label_css' => '' )
									)
								. '</th>
								<td><fieldset>' . '<code style="font-size:14px;">' . site_url() . '</code>'
		);
		$this->fields[ $name_of_field ] = array(
								'type'			 => 'text'
							  , 'default'		 => ( isset( $default_options_values[ $name_of_field ] ) ) ? $default_options_values[ $name_of_field ] : ''
							  , 'placeholder'	 => '/error-download'
							  , 'css'			 => 'width:75%'
							  , 'group'			 => 'warning_url'
							  , 'only_field'	 => true
		);
		$this->fields[ $name_of_field . '_sufix' ] = array(
			  'type'	 => 'pure_html'
			, 'group'	 => 'warning_url'
			, 'html'	 =>				  '<p class="description" style="line-height: 1.7em;margin: 0;">'
											. __( 'Type URL of page with warning about not valid IP for downloading', 'secure-downloads' )
										. '</p>
									</fieldset>
								</td>
							</tr>'
		);
		
        $name_of_field = 'opsd_url_file_not_exist';
		$this->fields[ $name_of_field . '_prefix' ] = array(
			  'type'	 => 'pure_html'
			, 'group'	 => 'warning_url'
			, 'html'	 => '<tr valign="top" class="opsd_sub_settings_grayed0 opsd_tr_set_gen_' . $name_of_field . ' ">
								<th scope="row">'
									. OPSD_Settings_API::label_static(	  'set_gen_' . $name_of_field
																		, array( 'title' => __( 'File not exist', 'secure-downloads' ), 'label_css' => '' )
									)
								. '</th>
								<td><fieldset>' . '<code style="font-size:14px;">' . site_url() . '</code>'
		);
		$this->fields[ $name_of_field ] = array(
								'type'			 => 'text'
							  , 'default'		 => ( isset( $default_options_values[ $name_of_field ] ) ) ? $default_options_values[ $name_of_field ] : ''
							  , 'placeholder'	 => '/error-download'
							  , 'css'			 => 'width:75%'
							  , 'group'			 => 'warning_url'
							  , 'only_field'	 => true
		);
		$this->fields[ $name_of_field . '_sufix' ] = array(
			  'type'	 => 'pure_html'
			, 'group'	 => 'warning_url'
			, 'html'	 =>				  '<p class="description" style="line-height: 1.7em;margin: 0;">'
											. __( 'Type URL of page with warning that requested file not exist', 'secure-downloads' )
										. '</p>
									</fieldset>
								</td>
							</tr>'
		);
		
        $name_of_field = 'opsd_url_error_opening_file';
		$this->fields[ $name_of_field . '_prefix' ] = array(
			  'type'	 => 'pure_html'
			, 'group'	 => 'warning_url'
			, 'html'	 => '<tr valign="top" class="opsd_sub_settings_grayed0 opsd_tr_set_gen_' . $name_of_field . ' ">
								<th scope="row">'
									. OPSD_Settings_API::label_static(	  'set_gen_' . $name_of_field
																		, array( 'title' => __( 'Error File Opening', 'secure-downloads' ), 'label_css' => '' )
									)
								. '</th>
								<td><fieldset>' . '<code style="font-size:14px;">' . site_url() . '</code>'
		);
		$this->fields[ $name_of_field ] = array(
								'type'			 => 'text'
							  , 'default'		 => ( isset( $default_options_values[ $name_of_field ] ) ) ? $default_options_values[ $name_of_field ] : ''
							  , 'placeholder'	 => '/error-download'
							  , 'css'			 => 'width:75%'
							  , 'group'			 => 'warning_url'
							  , 'only_field'	 => true
		);
		$this->fields[ $name_of_field . '_sufix' ] = array(
			  'type'	 => 'pure_html'
			, 'group'	 => 'warning_url'
			, 'html'	 =>				  '<p class="description" style="line-height: 1.7em;margin: 0;">'
											. __( 'Type URL of page with warning about error of file opening', 'secure-downloads' )
										. '</p>
									</fieldset>
								</td>
							</tr>'
		);
		
		
		
		//                                                                              </editor-fold>
        
                
        // <editor-fold     defaultstate="collapsed"                        desc=" Miscellaneous "  >
        
        
        

		// CSV Separator ////////////////////////////////////////////////////////
		$field_options = array(
							     ',' => ', - ' . __( 'comma', 'secure-downloads' )
							   , ';' => '; - ' . __( 'semicolon', 'secure-downloads' )
							   , '|' => '| - ' . __( 'vertical bar', 'secure-downloads' )
							   , '^' => '^ - ' . __( 'caret', 'secure-downloads' )
						   );       
		$this->fields['opsd_csv_separator'] = array(  
								   'type'          => 'select'
								   , 'default'     => $default_options_values['opsd_csv_separator']   //';'            
								   , 'title'       => __('CSV field separator', 'secure-downloads')
								   , 'description' => sprintf(__('Select CSV separator of data field.' ,'secure-downloads'),'<b>','</b>')
								   , 'options'     => $field_options
								   , 'group'       => 'opsd_listing'
						   );
		//  Divider  ///////////////////////////////////////////////////////////////       
		$this->fields['hr_opsd_csv_separator_separator'] = array( 'type' => 'hr', 'group' => 'opsd_listing' );

    		
		// Dates Format ////////////////////////////////////////////////////////
        $this->fields['opsd_date_format_html_prefix'] = array(   
                                    'type'          => 'pure_html'
                                    , 'group'       => 'opsd_listing'
                                    , 'html'        => '<tr valign="top" class="opsd_tr_set_gen_opsd_date_format">
                                                            <th scope="row">'.
                                                                OPSD_Settings_API::label_static( 'set_gen_opsd_date_format'
                                                                    , array(   'title'=> __('Date Format' , 'secure-downloads'), 'label_css' => 'margin: 0.25em 0 !important;vertical-align: middle;' ) )
                                                            .'</th>
                                                            <td><fieldset>'
                            );          
        $field_options = array();
        foreach ( array( __('F j, Y'), 'Y/m/d', 'm/d/Y', 'd/m/Y' ) as $format ) {
            $field_options[ esc_attr($format) ] = array( 'title' => date_i18n( $format ) );
        }
        $field_options['custom'] =  array( 'title' =>  __('Custom' , 'secure-downloads') . ':', 'attr' =>  array( 'id' => 'date_format_selection_custom' ) );

        $this->fields['opsd_date_format_selection'] = array(   
                                    'type'          => 'radio'
                                    , 'default'     => get_option('date_format')
                                    , 'options'     => $field_options
                                    , 'group'       => 'opsd_listing'
                                    , 'only_field'  => true
                            );

        $opsd_date_format = get_opsd_option( 'opsd_date_format');       
        $this->fields['opsd_date_format'] = array(  
                                'type'          => 'text'
                                , 'default'     => $default_options_values['opsd_date_format']         //get_option('date_format')
                                , 'value'       => htmlentities( $opsd_date_format )      // Display value of this field in specific way
                                , 'group'       => 'opsd_listing'
                                , 'placeholder' => get_option('date_format')
                                , 'css'         => 'width:10em;'
                                , 'only_field'  => true
            );    

        $this->fields['opsd_date_format_html_sufix'] = array(   
                                    'type'          => 'pure_html'
                                    , 'group'       => 'opsd_listing'
                                    , 'html'        => '          <span class="description"><code>' . date_i18n( $opsd_date_format ) . '</code></span>'
                                                                . '<p class="description">' 
                                                                    . sprintf(__('Type your date format for emails and the item table. %sDocumentation on date formatting%s' , 'secure-downloads'),'<br/><a href="http://codex.wordpress.org/Formatting_Date_and_Time" target="_blank">','</a>')
                                                            . '   </p>
                                                               </fieldset>
                                                            </td>
                                                        </tr>'            
                            );        
        
        // Time Format
        // $this->fields = apply_filters( 'opsd_settings_opsd_time_format', $this->fields, $default_options_values ); 
    
        // Time Format /////////////////////////////////////////////////////////////
        $this->fields['opsd_time_format_html_prefix'] = array(   
                                    'type'          => 'pure_html'
                                    , 'group'       => 'opsd_listing'
                                    , 'html'        => '<tr valign="top" class="wpbc_tr_set_gen_opsd_time_format">
                                                            <th scope="row">'.
                                                                OPSD_Settings_API::label_static( 'set_gen_opsd_time_format'
                                                                    , array(   'title'=> __('Time Format' ,'secure-downloads'), 'label_css' => 'margin: 0.25em 0 !important;vertical-align: middle;' ) )
                                                            .'</th>
                                                            <td><fieldset>'
                            );          
        $field_options = array();
        foreach ( array( 'g:i a', 'g:i A', 'H:i' ) as $format ) {
            $field_options[ esc_attr($format) ] = array( 'title' => date_i18n( $format ) );
        }
        $field_options['custom'] =  array( 'title' =>  __('Custom' ,'secure-downloads') . ':', 'attr' =>  array( 'id' => 'time_format_selection_custom' ) );

        $this->fields['opsd_time_format_selection'] = array(   
                                    'type'          => 'radio'
                                    , 'default'     => 'H:i'
                                    , 'options'     => $field_options
                                    , 'group'       => 'opsd_listing'
                                    , 'only_field'  => true
                            );

        $opsd_time_format = get_opsd_option( 'opsd_time_format');              
        $this->fields['opsd_time_format'] = array(  
                                'type'          => 'text'
                                , 'default'     => $default_options_values['opsd_time_format']   //'H:i'
                                , 'value'       => htmlentities( $opsd_time_format )      // Display value of this field in specific way
                                , 'group'       => 'opsd_listing'
                                , 'placeholder' => 'H:i'
                                , 'css'         => 'width:5em;' 
                                , 'only_field'  => true
            );    

        $this->fields['opsd_time_format_html_sufix'] = array(   
                                    'type'          => 'pure_html'
                                    , 'group'       => 'opsd_listing'
                                    , 'html'        => '          <span class="description"><code>' . date_i18n( $opsd_time_format ) . '</code></span>'
                                                                . '<p class="description">' 
                                                                    . sprintf(__('Type your time format for emails and the opsd table. %sDocumentation on time formatting%s' ,'secure-downloads'),'<br/><a href="http://php.net/manual/en/function.date.php" target="_blank">','</a>')
                                                            . '   </p>
                                                               </fieldset>
                                                            </td>
                                                        </tr>'            
                            );        

        // </editor-fold>
                
        
        // <editor-fold     defaultstate="collapsed"                        desc=" Advanced "  >
        
        
        //Show | Hide links for Advanced JavaScript section 
        $this->fields['opsd_advanced_js_loading_settings'] = array(    
                                  'type' => 'html'
                                , 'html'  =>  
                                          '<a id="opsd_show_advanced_section_link_show" class="opsd_expand_section_link" href="javascript:void(0)">+ ' . __('Show advanced settings of JavaScript loading' , 'secure-downloads') . '</a>'
                                        . '<a id="opsd_show_advanced_section_link_hide" class="opsd_expand_section_link" href="javascript:void(0)" style="display:none;">- ' . __('Hide advanced settings of JavaScript loading' , 'secure-downloads') . '</a>'
                                , 'cols'  => 2
                                , 'group' => 'advanced'
            );
		/*
        $this->fields['opsd_is_not_load_bs_script_in_client'] = array(   
                                'type'          => 'checkbox'
                                , 'default'     => $default_options_values['opsd_is_not_load_bs_script_in_client']         //'Off'            
                                , 'title'       => __('Disable Bootstrap loading on Front-End' , 'secure-downloads')
                                , 'label'       => __(' If your theme or some other plugin is load the BootStrap JavaScripts, you can disable  loading of this script by this plugin.' , 'secure-downloads')
                                , 'description' => ''
                                , 'group'       => 'advanced'
                                , 'tr_class'    => 'opsd_advanced_js_loading_settings opsd_sub_settings_grayed hidden_items'
            );       
		 */
        $this->fields['opsd_is_not_load_bs_script_in_admin'] = array(   
                                'type'          => 'checkbox'
                                , 'default'     => $default_options_values['opsd_is_not_load_bs_script_in_admin']         //'Off'            
                                , 'title'       => __('Disable Bootstrap loading on Back-End' , 'secure-downloads')
                                , 'label'       => __(' If your theme or some other plugin is load the BootStrap JavaScripts, you can disable  loading of this script by this plugin.' , 'secure-downloads')
                                , 'description' => ''
                                , 'group'       => 'advanced'
                                , 'tr_class'    => 'opsd_advanced_js_loading_settings opsd_sub_settings_grayed hidden_items'
            );       
		/*
        $this->fields['hr_calendar_before_is_load_js_css_on_specific_pages'] = array( 'type' => 'hr', 'group' => 'advanced', 'tr_class' => 'opsd_advanced_js_loading_settings opsd_sub_settings_grayed hidden_items' );
        $this->fields['opsd_is_load_js_css_on_specific_pages'] = array(   
                                'type'          => 'checkbox'
                                , 'default'     => $default_options_values['opsd_is_load_js_css_on_specific_pages']         //'Off'            
                                , 'title'       => __('Load JS and CSS files only on specific pages' , 'secure-downloads')
                                , 'label'       => __('Activate loading of CSS and JavaScript files of plugin only at specific pages.' , 'secure-downloads')
                                , 'description' => ''
                                , 'group'       => 'advanced'
                                , 'tr_class'    => 'opsd_advanced_js_loading_settings opsd_sub_settings_grayed hidden_items'
                                , 'is_demo_safe' => opsd_is_this_demo()
            );       
        $this->fields['opsd_pages_for_load_js_css'] = array(   
                                'type'          => 'textarea'
                                , 'default'     => $default_options_values['opsd_pages_for_load_js_css']         //''
                                , 'placeholder' => '/opsd-form/'
                                , 'title'       => __('Relative URLs of pages, where to load plugin CSS and JS files' , 'secure-downloads')
                                , 'description' => sprintf(__('Enter relative URLs of pages, where you have Secure Downloads elements (item forms or availability calendars). Please enter one URL per line. Example: %s' , 'secure-downloads'),'<code>/opsd-form/</code>')
                                ,'description_tag' => 'p'
                                , 'css'         => 'width:100%'
                                , 'rows'        => 5
                                , 'group'       => 'advanced'
                                , 'tr_class'    => 'opsd_advanced_js_loading_settings opsd_is_load_js_css_on_specific_pages opsd_sub_settings_grayed hidden_items'
                                , 'is_demo_safe' => opsd_is_this_demo()
                        );        
		 */
        if ( opsd_is_this_demo() ) 
            $this->fields['opsd_pages_for_load_js_css_demo'] = array( 'group' => 'advanced', 'type' => 'html', 'html' => opsd_get_warning_text_in_demo_mode(), 'cols' => 2 , 'tr_class' => 'opsd_advanced_js_loading_settings opsd_sub_settings_grayed hidden_items' ); 
        
		
        /*
        // Show settings of powered by notice
        $this->fields['opsd_advanced_powered_by_notice_settings'] = array(    
                                  'type' => 'html'
                                , 'html'  =>  
                                          '<a id="opsd_powered_by_link_show" class="opsd_expand_section_link" href="javascript:void(0)">+ ' . __('Show settings of powered by notice' , 'secure-downloads') . '</a>'
                                        . '<a id="opsd_powered_by_link_hide" class="opsd_expand_section_link" href="javascript:void(0)" style="display:none;">- ' . __('Hide settings of powered by notice' , 'secure-downloads') . '</a>'
                                , 'cols'  => 2
                                , 'group' => 'advanced'
            );
		
        $this->fields['opsd_is_show_powered_by_notice'] = array(   
                                'type'          => 'checkbox'
                                , 'default'     => $default_options_values['opsd_is_show_powered_by_notice']         //'On'            
                                , 'title'       => __('Powered by notice' , 'secure-downloads')
                                , 'label'       => sprintf(__(' Turn On/Off powered by "Item Calendar" notice under the calendar.' , 'secure-downloads'),'oplugins.com')
                                , 'description' => ''
                                , 'group'       => 'advanced'
                                , 'tr_class'    => 'opsd_is_show_powered_by_notice opsd_sub_settings_grayed hidden_items'
            );   
		     
		
        $this->fields['opsd_opsd_copyright_adminpanel'] = array(   
                                'type'          => 'checkbox'
                                , 'default'     => $default_options_values['opsd_opsd_copyright_adminpanel']         //'On'            
                                , 'title'       => __('Help and info notices' , 'secure-downloads')
                                , 'label'       => sprintf(__(' Turn On/Off version notice and help link to rate plugin at admin panel.' , 'secure-downloads'),'oplugins.com')
                                , 'description' => ''
                                , 'group'       => 'advanced'
                                , 'tr_class'    => 'opsd_is_show_powered_by_notice opsd_sub_settings_grayed hidden_items'
            );       
        */
        if ( ( ! opsd_is_this_demo() ) && ( current_user_can( 'activate_plugins' ) ) ) {         
        
			
			$this->fields['help_plugin_system_info'] = array(   
							   'type'              => 'help'
							 , 'value'             => '' //opsd_get_help_rows_about_config_in_several_languges()
							 , 'class'             => ''
							 , 'css'               => 'margin:0;padding:0;border:0;'
							 , 'description'       => ''
							 , 'cols'              => 2 
							 , 'group'             => 'advanced'
							 , 'tr_class'          => ''
							 , 'description_tag'   => 'p'
					 ); 
			
            $this->fields['help_plugin_system_info']['value'][] = 
                '<div class="clear"></div><hr/><center><a class="button button" href="' 
                                                                        . opsd_get_settings_url() 
                                                                        . '&system_info=show#opsd_general_settings_system_info_metabox">' 
                                                                                . __('Plugin System Info' , 'secure-downloads') 
                                                        . '</a></center>';
        }
		
        // </editor-fold>
                                 
        
        // <editor-fold     defaultstate="collapsed"                        desc=" Information "  >
        if (  function_exists( 'opsd_get_dashboard_info' ) ) {
            $this->fields['opsd_information'] = array(   
                               'type'              => 'html'
                             , 'html'              => opsd_get_dashboard_info()
                             , 'cols'              => 2
                             , 'group'             => 'information'
                     ); 
        }
        // </editor-fold>

        
        // <editor-fold     defaultstate="collapsed"                        desc=" User permissions for plugin menu pages "  >
        
        
        $this->fields['opsd_menu_position'] = array(   
                                'type'          => 'select'
                                , 'default'     => 'top'
                                , 'title'       => __('Plugin menu position', 'secure-downloads')
                                , 'description' => ''
                                , 'options'     => array(
                                                              'top'     => __('Top', 'secure-downloads')
                                                            , 'middle'  => __('Middle', 'secure-downloads')
                                                            , 'bottom'  => __('Bottom', 'secure-downloads')
                                                        )
                                , 'group'       => 'permissions'
                                , 'is_demo_safe' => opsd_is_this_demo()
                        );
        
        $this->fields['opsd_user_role_opsd_header'] = array(   
                                    'type'          => 'pure_html'
                                    , 'group'       => 'permissions'
                                    , 'html'        => '<tr valign="top">
                                                            <th scope="row" colspan="2">
                                                                <hr/><p><strong>' . wp_kses_post(  __('User permissions for plugin menu pages' , 'secure-downloads') )  . ':</strong></p>
                                                            </th>
                                                        </tr'
                            );        
        
        $field_options = array();
        $field_options['subscriber']    = translate_user_role('Subscriber');
        $field_options['contributor']   = translate_user_role('Contributor');
        $field_options['author']        = translate_user_role('Author');
        $field_options['editor']        = translate_user_role('Editor');
        $field_options['administrator'] = translate_user_role('Administrator');
        
        $this->fields['opsd_user_role_master'] = array(   
                                'type'          => 'select'
                                , 'default'     => $default_options_values['opsd_user_role_master']         //'editor'            
                                , 'title'       => __('Secure Links', 'secure-downloads')
                                , 'description' => ''
                                , 'options'     => $field_options
                                , 'group'       => 'permissions'
                                , 'is_demo_safe' => opsd_is_this_demo()
                        );
        $this->fields['opsd_user_role_addnew'] = array(   
                                'type'          => 'select'
                                , 'default'     => $default_options_values['opsd_user_role_addnew']         //'editor'            
                                , 'title'       => __('Files', 'secure-downloads')
                                , 'description' => ''
                                , 'options'     => $field_options
                                , 'group'       => 'permissions'
                                , 'is_demo_safe' => opsd_is_this_demo()
                        );
        $this->fields['opsd_user_role_settings'] = array(   
                                'type'          => 'select'
                                , 'default'     => $default_options_values['opsd_user_role_settings']         //'administrator'            
                                , 'title'       => __('Settings', 'secure-downloads')
                                , 'description' => __('Select user access level for the menu pages of plugin' , 'secure-downloads')
                                , 'description_tag' => 'p'
                                , 'options'     => $field_options
                                , 'group'       => 'permissions'
                                , 'is_demo_safe' => opsd_is_this_demo()
                        );
        
        if ( opsd_is_this_demo() ) 
            $this->fields['opsd_user_role_settings_demo'] = array( 'group' => 'permissions', 'type' => 'html', 'html' => opsd_get_warning_text_in_demo_mode(), 'cols' => 2 ); 
        
        
        // </editor-fold>
        
                
        // <editor-fold     defaultstate="collapsed"                        desc=" Uninstall "  >
        $this->fields['opsd_is_delete_if_deactive'] = array(   
                                'type'          => 'checkbox'
                                , 'default'     => $default_options_values['opsd_is_delete_if_deactive']         //'Off'            
                                , 'title'       => __('Delete plugin settings, when plugin deactivated' , 'secure-downloads')
                                , 'label'       => __('Check this box to delete plugin settings options, when you uninstal this plugin.' , 'secure-downloads')
                                , 'description' => ''
                                , 'group'       => 'uninstall'
            );       
        // </editor-fold>
        
                
//debuge($this->fields);die;                
    }      
    

    /**     Add Custon JavaScript - for some specific settings options
     *      Need to executes after showing of entire settings page (on hook: opsd_after_settings_content).
     *      After initial definition of settings,  and possible definition after POST request.
     * 
     * @param type $menu_slug
     * 
     */
    public function enqueue_js( $menu_slug, $active_page_tab, $active_page_subtab ) {

        $js_script = '';
        
        // Hide Legend items 
        $js_script .= " 
                        if ( ! jQuery('#set_gen_opsd_is_show_legend').is(':checked') ) {   
                            jQuery('.opsd_calendar_legend_items').addClass('hidden_items'); 
                        }
                      ";        
        // Hide or Show Legend items on click checkbox
        $js_script .= " jQuery('#set_gen_opsd_is_show_legend').on( 'change', function(){    
                                if ( this.checked ) { 
                                    jQuery('.opsd_calendar_legend_items').removeClass('hidden_items');
                                } else {
                                    jQuery('.opsd_calendar_legend_items').addClass('hidden_items');
                                }
                            } ); ";        
        // Thank you Message or Page
        $js_script .= " 
                        if ( jQuery('#type_of_thank_you_message_message').is(':checked') ) {   
                            jQuery('.opsd_calendar_thank_you_page').addClass('hidden_items'); 
                        }
                        if ( jQuery('#type_of_thank_you_message_page').is(':checked') ) {   
                            jQuery('.opsd_calendar_thank_you_message').addClass('hidden_items'); 
                        }
                      ";        
        $js_script .= " jQuery('input[name=\"set_gen_opsd_type_of_thank_you_message\"]').on( 'change', function(){    
                                if ( jQuery('#type_of_thank_you_message_message').is(':checked') ) {   
                                    jQuery('.opsd_calendar_thank_you_message').removeClass('hidden_items');
                                    jQuery('.opsd_calendar_thank_you_page').addClass('hidden_items'); 
                                } else {
                                    jQuery('.opsd_calendar_thank_you_message').addClass('hidden_items');
                                    jQuery('.opsd_calendar_thank_you_page').removeClass('hidden_items'); 
                                }
                            } ); ";    
        
        // Default calendar view mode (Item Listing) - set  active / inctive options depend from  resource selection.
        $js_script .= " jQuery('#set_gen_opsd_view_days_num').on( 'focus', function(){    
                            if ( jQuery('#set_gen_opsd_default_opsd_resource').length > 0 ) {
                                jQuery('#set_gen_opsd_default_opsd_resource').bind('change', function() {
                                    jQuery('#set_gen_opsd_view_days_num option:eq(2)').prop('selected', true);
                                });
                                if ( jQuery('#set_gen_opsd_default_opsd_resource').val() == '' ) { 
                                    jQuery('#set_gen_opsd_view_days_num option:eq(0)').prop('disabled', false);
                                    jQuery('#set_gen_opsd_view_days_num option:eq(1)').prop('disabled', false);
                                    jQuery('#set_gen_opsd_view_days_num option:eq(2)').prop('disabled', false);
                                    jQuery('#set_gen_opsd_view_days_num option:eq(3)').prop('disabled', false);
                                    jQuery('#set_gen_opsd_view_days_num option:eq(4)').prop('disabled', true);
                                    jQuery('#set_gen_opsd_view_days_num option:eq(5)').prop('disabled', true);
                                } else {
                                    jQuery('#set_gen_opsd_view_days_num option:eq(0)').prop('disabled', true);
                                    jQuery('#set_gen_opsd_view_days_num option:eq(1)').prop('disabled', true);
                                    jQuery('#set_gen_opsd_view_days_num option:eq(2)').prop('disabled', false);
                                    jQuery('#set_gen_opsd_view_days_num option:eq(3)').prop('disabled', true);
                                    jQuery('#set_gen_opsd_view_days_num option:eq(4)').prop('disabled', false);
                                    jQuery('#set_gen_opsd_view_days_num option:eq(5)').prop('disabled', false);                                                                
                                }
                            }
                        } ); ";        
        
        ////////////////////////////////////////////////////////////////////////
        // Set  correct  value for dates format,  depend from selection of radio buttons
        $opsd_date_format = get_opsd_option( 'opsd_date_format');       
        // On initial Load set correct text value and correct radio button
        $js_script .= " 
                        // Select by  default Custom  value, later  check all other predefined values
                        jQuery( '#date_format_selection_custom' ).prop('checked', true);

                        jQuery('input[name=\"set_gen_opsd_date_format_selection\"]').each(function() {
                           var radio_button_value = jQuery( this ).val()
                           var encodedStr = radio_button_value.replace(/[\u00A0-\u9999<>\&]/gim, function(i) {
                                                                                        return '&#'+i.charCodeAt(0)+';';
                                                                                    });
                           if ( encodedStr == '". $opsd_date_format ."' ) {
                                jQuery( this ).prop('checked', true);                     
                           }
                        });
                        
                        jQuery('#set_gen_opsd_date_format').val('". $opsd_date_format ."');
                        ";
        // On click Radio button "Date Format", - set value in custom Text field
        $js_script .= " jQuery('input[name=\"set_gen_opsd_date_format_selection\"]').on( 'change', function(){    
                                if (  ( this.checked ) && ( jQuery(this).val() != 'custom' )  ){ 

                                    jQuery('#set_gen_opsd_date_format').val( jQuery(this).val().replace(/[\u00A0-\u9999<>\&]/gim, 
                                        function(i) {
                                            return '&#'+i.charCodeAt(0)+';';
                                        }) 
                                    );
                                }                            
                            } ); "; 
        // If we edit custom "Date Format" Text  field - select Custom Radio button.                                 
        $js_script .= " jQuery('#set_gen_opsd_date_format').on( 'change', function(){                                              
                                jQuery( '#date_format_selection_custom' ).prop('checked', true);
                            } ); ";        
        
        
        

        ////////////////////////////////////////////////////////////////////////
        // Set  correct  value for Time Format,  depend from selection of radio buttons
        $opsd_time_format = get_opsd_option( 'opsd_time_format');       
        // Function  to  load on initial stage of page loading, set correct value of text and select correct radio button.
        $js_script .= " 
                        // Select by  default Custom  value, later  check all other predefined values
                        jQuery( '#time_format_selection_custom' ).prop('checked', true);

                        jQuery('input[name=\"set_gen_opsd_time_format_selection\"]').each(function() {
                           var radio_button_value = jQuery( this ).val()
                           var encodedStr = radio_button_value.replace(/[\u00A0-\u9999<>\&]/gim, function(i) {
                                                                                        return '&#'+i.charCodeAt(0)+';';
                                                                                    });
                           if ( encodedStr == '". $opsd_time_format ."' ) {
                                jQuery( this ).prop('checked', true);                     
                           }
                        });

                        jQuery('#set_gen_opsd_time_format').val('". $opsd_time_format ."');
                        ";
        // On click Radio button "Time Format", - set value in custom Text field
        $js_script .= " jQuery('input[name=\"set_gen_opsd_time_format_selection\"]').on( 'change', function(){    
                                if (  ( this.checked ) && ( jQuery(this).val() != 'custom' )  ){ 

                                    jQuery('#set_gen_opsd_time_format').val( jQuery(this).val().replace(/[\u00A0-\u9999<>\&]/gim, 
                                        function(i) {
                                            return '&#'+i.charCodeAt(0)+';';
                                        }) 
                                    );
                                }                            
                            } ); "; 
        // If we edit custom "Time Format" Text  field - select Custom Radio button.                                 
        $js_script .= " jQuery('#set_gen_opsd_time_format').on( 'change', function(){                                              
                                jQuery( '#time_format_selection_custom' ).prop('checked', true);
                            } ); ";        

        
        
        
        ////////////////////////////////////////////////////////////////////////
        // Advanced section
        ////////////////////////////////////////////////////////////////////////
        
        // Click on "Allow unlimited items per same day(s)"
        $js_script .= " jQuery('#set_gen_opsd_is_days_always_available').on( 'change', function(){    
                            if ( this.checked ) { 
                                var answer = confirm('"                 
                                              . esc_js( __( 'Warning', 'secure-downloads') ) . '! '
                                              . esc_js( __( 'You allow unlimited number of items per same dates, its can be a reason of double items on the same date. Do you really want to do this?', 'secure-downloads') ) 
                                      .  "' );  
                                if ( answer) { 
                                    this.checked = true;   
                                    jQuery('#set_gen_opsd_check_on_server_if_dates_free').prop('checked', false );                                    
                                    jQuery('#set_gen_opsd_is_show_pending_days_as_available').prop('checked', false );            
                                    jQuery('.opsd_pending_days_as_available_sub_settings').addClass('hidden_items'); 
                                } else { 
                                    this.checked = false; 
                                } 
                            }                            
                        } ); ";   
        // Click on "Checking to prevent double item, during submitting item"
        $js_script .= " jQuery('#set_gen_opsd_check_on_server_if_dates_free').on( 'change', function(){    
                            if ( this.checked ) { 
                                var answer = confirm('"                 
                                              . esc_js( __( 'Warning', 'secure-downloads') ) . '! '
                                              . esc_js( __( 'This feature can impact to speed of submitting item. Do you really want to do this?', 'secure-downloads') ) 
                                      .  "' );  
                                if ( answer) { 
                                    this.checked = true;   
                                    jQuery('#set_gen_opsd_is_days_always_available').prop('checked', false );
                                } else { 
                                    this.checked = false; 
                                } 
                            }                            
                        } ); ";   
        
        // Click  on Show Advanced JavaScript section  link
        $js_script .= " jQuery('#opsd_show_advanced_section_link_show').on( 'click', function(){                                 
                            jQuery('#opsd_show_advanced_section_link_show').slideToggle(200);                            
                            jQuery('#opsd_show_advanced_section_link_hide').animate( {opacity: 1}, 200 ).slideToggle(200);     
                            jQuery('.opsd_advanced_js_loading_settings').removeClass('hidden_items'); 
                            
                            if ( ! jQuery('#set_gen_opsd_is_load_js_css_on_specific_pages').is(':checked') ) {   
                                jQuery('.opsd_is_load_js_css_on_specific_pages').addClass('hidden_items'); 
                            }
                        } ); ";   
        $js_script .= " jQuery('#opsd_show_advanced_section_link_hide').on( 'click', function(){    
                            jQuery('#opsd_show_advanced_section_link_hide').slideToggle(200);                            
                            jQuery('#opsd_show_advanced_section_link_show').animate( {opacity: 1}, 200 ).slideToggle(200);                        
                            jQuery('.opsd_advanced_js_loading_settings').addClass('hidden_items'); 
                        } ); ";   
        // Click on "is_not_load_bs_script_in_client"
        $js_script .= " jQuery('#set_gen_opsd_is_not_load_bs_script_in_client, #set_gen_opsd_is_not_load_bs_script_in_admin').on( 'change', function(){    
                            if ( this.checked ) { 
                                var answer = confirm('"                 
                                              . esc_js( __( 'Warning', 'secure-downloads') ) . '! '
                                              . esc_js( __( 'You are need to be sure what you are doing. You are disable of loading some JavaScripts Do you really want to do this?', 'secure-downloads') )                                                              
                                      .  "' );  
                                if ( answer) {
                                    this.checked = true;                                       
                                } else { 
                                    this.checked = false; 
                                } 
                            }                            
                        } ); ";       
        $js_script .= " jQuery('#set_gen_opsd_is_load_js_css_on_specific_pages').on( 'change', function(){    
                            if ( this.checked ) { 
                                var answer = confirm('"                 
                                              . esc_js( __( 'Warning', 'secure-downloads') ) . '! '
                                              . esc_js( __( 'You are need to be sure what you are doing. You are disable of loading some JavaScripts Do you really want to do this?', 'secure-downloads') )                                                                                                                           
                                      .  "' );  
                                if ( answer) {
                                    this.checked = true;                                       
                                    jQuery('.opsd_is_load_js_css_on_specific_pages').removeClass('hidden_items'); 
                                } else { 
                                    this.checked = false; 
                                } 
                            } else {
                                jQuery('.opsd_is_load_js_css_on_specific_pages').addClass('hidden_items'); 
                            }
                        } );                         
                        ";         
        
        
        // Click  on Powered by  links
        $js_script .= " jQuery('#opsd_powered_by_link_show').on( 'click', function(){                                 
                            jQuery('#opsd_powered_by_link_show').slideToggle(200);                            
                            jQuery('#opsd_powered_by_link_hide').animate( {opacity: 1}, 200 ).slideToggle(200);  
                            jQuery('.opsd_is_show_powered_by_notice').removeClass('hidden_items');                             
                        } ); ";   
        $js_script .= " jQuery('#opsd_powered_by_link_hide').on( 'click', function(){    
                            jQuery('#opsd_powered_by_link_hide').slideToggle(200);                            
                            jQuery('#opsd_powered_by_link_show').animate( {opacity: 1}, 200 ).slideToggle(200);   
                            jQuery('.opsd_is_show_powered_by_notice').addClass('hidden_items'); 
                        } ); ";   

        
        // Show confirmation window,  if user activate this checkbox
        $js_script .= " jQuery('#set_gen_opsd_is_delete_if_deactive').on( 'change', function(){    
                            if ( this.checked ) { 
                                var answer = confirm('"                 
                                              . esc_js( __( 'Warning', 'secure-downloads') ) . '! '
                                              . esc_js( __( 'If you check this option, all data will be deleted when you uninstall this plugin. Do you really want to do this?', 'secure-downloads') )                                                        
                                      .  "' );  
                                if ( answer) {
                                    this.checked = true;                                                                           
                                } else { 
                                    this.checked = false; 
                                } 
                            }
                        } );                         
                        ";         

        
        // Eneque JS to  the footer of the page
        opsd_enqueue_js( $js_script );
    }
    
}


/** Override VALIDATED fields BEFORE saving to DB 
 * Description:
 * Check "Thank you page" URL
 * 
 * @param array $validated_fields
 */
function opsd_settings_validate_fields_before_saving__all( $validated_fields ) {


    $validated_fields['opsd_url_wrong_hash'] = opsd_make_link_relative( $validated_fields['opsd_url_wrong_hash'] );
    $validated_fields['opsd_url_download_expired'] = opsd_make_link_relative( $validated_fields['opsd_url_download_expired'] );
    $validated_fields['opsd_url_ip_not_valied'] = opsd_make_link_relative( $validated_fields['opsd_url_ip_not_valied'] );
    $validated_fields['opsd_url_file_not_exist'] = opsd_make_link_relative( $validated_fields['opsd_url_file_not_exist'] );
    $validated_fields['opsd_url_error_opening_file'] = opsd_make_link_relative( $validated_fields['opsd_url_error_opening_file'] );
    
    unset( $validated_fields[ 'opsd_date_format_selection' ] );                      // We do not need to this field,  because saving to DB only: "date_format" field
	unset( $validated_fields[ 'opsd_time_format_selection' ] );                      // We do not need to this field,  because saving to DB only: "time_format" field
    
    return $validated_fields;
}
add_filter( 'opsd_settings_validate_fields_before_saving', 'opsd_settings_validate_fields_before_saving__all', 10, 1 );   // Hook for validated fields.