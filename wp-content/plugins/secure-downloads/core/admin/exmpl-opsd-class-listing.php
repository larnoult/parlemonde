<?php /**
 * @version 1.1
 * @package Secure Downloads
 * @category item Listing Table in Admin Panel
 * @author wpdevelop
 *
 * @web-site http://oplugins.com/
 * @email info@oplugins.com 
 * 
 * @modified 2015-12-28
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly

class OPSD_OPSD_Listing_Table {
    
    public $items;
    public $opsd_types;
    
    private $url;                   // URL for differnt Menus
    private $user_id;               // ID of logged in users
    private $is_free;               // Version  type
    private $days_column_style;     // CSS Styles for days columns
    private $date_view_type;        // Initial Days View mode 
    
    
    public function __construct( $items, $opsd_types ) {
        
        $this->items = $items;
        $this->opsd_types = $opsd_types;
        
        $this->url = array();
        $this->days_column_style = array( 'wide' => 'color:#333;', 'short' => 'color:#333;' );
        $this->init_params();                        
    }

    
    /** Get URL  for specific menu
     * 
     * @param sting $url    - 'master' | 'add' | 'settings' | 'request'
     * @return string       - url
     */
    public function get_url( $url = 'listing') {
        if ( isset( $this->url[ $url ] ) )
            return $this->url[ $url ];
        else 
            $this->url[ 'master' ];
    }

    
    /** Check if items exist or no */
    public function is_items_exist() {
        if ( count( $this->items ) > 0 )
            return true;
        else 
            return false;
    }

    
    /** Init Paramteres */
    private function init_params() {
        
        $user = wp_get_current_user();  
        $this->user_id = $user->ID;

        $this->url['master']     = 'admin.php?page=' . opsd_get_master_url( false );
        $this->url['add']         = 'admin.php?page=' . opsd_get_new_opsd_url( false );        
        $this->url['settings']    = 'admin.php?page=' . opsd_get_settings_url( false );

        // Transform the REQESTS parameters (GET and POST) into URL
        $this->url['request'] = opsd_get_params_in_url( opsd_get_master_url( false ), array('page_num', 'wh_opsd_type') );

        
        $this->date_view_type = get_opsd_option( 'opsd_date_view_type');
        if ( $this->date_view_type == 'short' ) $this->days_column_style['wide']  .= 'display:none;';
        else                                    $this->days_column_style['short'] .= 'display:none;';

        $version = get_opsd_version();
        if ( $version == 'free' ) $this->is_free = true;
        else                      $this->is_free = false;    
    }

    
    /** Show Listing Table */
    public function show() {
        
        ?><div id="listing_visible_opsd" class="container-fluid table table-striped opsd_selectable_table"><?php 
        
        if ( $this->is_items_exist() ) {
            
            $this->header( $this->is_free  );
            
            ?><span class="opsd_selectable_body"><?php
            
                $this->rows( $this->is_free  );
            
            ?></span><?php
            
        } else {
            ?><center>
                <h4><?php _e('Nothing Found', 'secure-downloads'); ?>.</h4>
            </center><?php
        }
        
        ?></div><?php
    }
    
    
    /** Show Header */
    public function header( $is_free ) {
        
        ?>
        <div class="row opsd-listing-header opsd_selectable_head">
            <div class="opsd-listing-collumn col-sm-<?php echo $is_free ? '2' : '3'; ?> col-xs-4">
                <div class="row">
                    <div class="opsd-no-margin col-sm-1 col-xs-1 opsd_column_1 check-column" >
                        <input type="checkbox" onclick="javascript:opsd_set_checkbox_in_table( this.checked, 'opsd_list_item_checkbox' );" class="opsd-no-margin" style="vertical-align: middle;"/>
                    </div>
                    <div class="opsd-no-margin col-sm-1 col-xs-5 text-center opsd_column_2">
                        &nbsp;<?php _e('ID', 'secure-downloads'); ?>
                    </div>
                    <div class="opsd-no-margin col-sm-<?php echo $is_free ? '6' : '7'; ?> text-left hide-sm opsd_column_3">
                        <?php _e('Labels' , 'secure-downloads');  if ( ! $is_free ) { echo ' / '; _e('Actions' , 'secure-downloads'); } ?>
                    </div>
                </div>
            </div>            
            <div class="opsd-listing-collumn col-sm-6 col-xs-6 text-center opsd_column_4"><?php _e('Item Data', 'secure-downloads'); ?></div>
            <div class="opsd-listing-collumn col-sm-3 hide-sm text-center opsd_column_5"><?php _e('Item Dates', 'secure-downloads'); ?>&nbsp;&nbsp;&nbsp;
                <a  id="opsd_dates_full" 
                    onclick="javascript:jQuery('#opsd_dates_full,.opsd_dates_small').hide();jQuery('#opsd_dates_small,.opsd_dates_full').show();" href="javascript:void(0)" 
                    title="<?php _e('Show ALL dates of item' , 'secure-downloads'); ?>" 
                    style="<?php echo $this->days_column_style['short']; ?>" 
                    class="tooltip_top" 
                ><i class="glyphicon glyphicon-resize-full" style=" margin-top: 2px;"></i></a>
                <a  id="opsd_dates_small" 
                    onclick="javascript:jQuery('#opsd_dates_small,.opsd_dates_full').hide();jQuery('#opsd_dates_full,.opsd_dates_small').show();" href="javascript:void(0)" 
                    title="<?php _e('Show only check in/out dates' , 'secure-downloads'); ?>"  
                    style="<?php echo $this->days_column_style['wide']; ?>" 
                    class="tooltip_top" 
                ><i class="glyphicon glyphicon-resize-small" style=" margin-top: 2px;"></i></a>                
            </div>
            <?php if ( $is_free ) { ?>
            <div class="opsd-listing-collumn col-sm-1 hide-sm text-center opsd_column_6"><?php _e('Actions', 'secure-downloads'); ?></div>
            <?php } ?>
        </div>
        <?php
    }
    
    
    /** Show Listing Rows
     * 
     * @param boolean $is_free
     */
    public function rows( $is_free ) {

        $availbale_locales_in_system = get_available_languages();
        $print_data = apply_opsd_filter( 'opsd_print_get_header', array( array() ) );   // P        
        $bk_key = 0;
        		
//TODO: blank data		
$this->items = 'a:1:{i:121;O:8:"stdClass":19:{s:10:"booking_id";s:3:"121";s:5:"trash";s:1:"0";s:8:"sync_gid";s:0:"";s:6:"is_new";s:1:"1";s:6:"status";s:0:"";s:9:"sort_date";s:19:"2017-11-22 00:00:00";s:17:"modification_date";s:19:"2017-05-14 10:25:15";s:4:"form";s:261:"text^name4^William~text^secondname4^Wilyson~text^email4^Wilyson.example@wpbookingcalendar.com~text^address4^200 Lincoln Ave~text^city4^Glasgow~text^postcode4^92073~text^country4^UK~text^phone4^927-80-51~select-one^visitors4^1~textarea^details4^~coupon^coupon4^ ";s:4:"hash";s:32:"0020d451b4bf5a34a3e65928128d2c85";s:12:"booking_type";s:1:"4";s:6:"remark";N;s:4:"cost";s:6:"960.00";s:10:"pay_status";s:0:"";s:11:"pay_request";s:1:"0";s:5:"dates";a:5:{i:0;O:8:"stdClass":4:{s:10:"booking_id";s:3:"121";s:12:"booking_date";s:19:"2017-11-22 00:00:00";s:8:"approved";s:1:"0";s:7:"type_id";N;}i:1;O:8:"stdClass":4:{s:10:"booking_id";s:3:"121";s:12:"booking_date";s:19:"2017-11-23 00:00:00";s:8:"approved";s:1:"0";s:7:"type_id";N;}i:2;O:8:"stdClass":4:{s:10:"booking_id";s:3:"121";s:12:"booking_date";s:19:"2017-11-24 00:00:00";s:8:"approved";s:1:"0";s:7:"type_id";N;}i:3;O:8:"stdClass":4:{s:10:"booking_id";s:3:"121";s:12:"booking_date";s:19:"2017-11-25 00:00:00";s:8:"approved";s:1:"0";s:7:"type_id";N;}i:4;O:8:"stdClass":4:{s:10:"booking_id";s:3:"121";s:12:"booking_date";s:19:"2017-11-26 00:00:00";s:8:"approved";s:1:"0";s:7:"type_id";N;}}s:11:"dates_short";a:3:{i:0;s:19:"2017-11-22 00:00:00";i:1;s:1:"-";i:2;s:19:"2017-11-26 00:00:00";}s:9:"form_show";s:7:" Times:";s:9:"form_data";a:7:{s:5:"email";s:37:"Wilyson.example@wpbookingcalendar.com";s:4:"name";s:7:"William";s:10:"secondname";s:7:"Wilyson";s:8:"visitors";s:1:"1";s:6:"coupon";s:1:" ";s:5:"_all_";a:11:{s:5:"name4";s:7:"William";s:11:"secondname4";s:7:"Wilyson";s:6:"email4";s:37:"Wilyson.example@wpbookingcalendar.com";s:8:"address4";s:15:"200 Lincoln Ave";s:5:"city4";s:7:"Glasgow";s:9:"postcode4";s:5:"92073";s:8:"country4";s:2:"UK";s:6:"phone4";s:9:"927-80-51";s:9:"visitors4";s:1:"1";s:8:"details4";s:0:"";s:7:"coupon4";s:1:" ";}s:12:"_all_fields_";a:18:{s:4:"name";s:7:"William";s:10:"secondname";s:7:"Wilyson";s:5:"email";s:37:"Wilyson.example@wpbookingcalendar.com";s:7:"address";s:15:"200 Lincoln Ave";s:4:"city";s:7:"Glasgow";s:8:"postcode";s:5:"92073";s:7:"country";s:2:"UK";s:5:"phone";s:9:"927-80-51";s:8:"visitors";s:1:"1";s:7:"details";s:0:"";s:6:"coupon";s:1:" ";s:19:"booking_resource_id";s:1:"4";s:11:"resource_id";s:1:"4";s:7:"type_id";s:1:"4";s:4:"type";s:1:"4";s:8:"resource";s:1:"4";s:10:"booking_id";s:3:"121";s:14:"resource_title";O:8:"stdClass":12:{s:15:"booking_type_id";s:1:"4";s:5:"title";s:11:"Apartment#3";s:5:"users";s:1:"1";s:6:"import";N;s:4:"cost";s:2:"25";s:12:"default_form";s:8:"standard";s:9:"prioritet";s:1:"0";s:6:"parent";s:1:"0";s:8:"visitors";s:1:"1";s:2:"id";s:1:"4";s:5:"count";i:1;s:2:"ID";s:1:"4";}}}s:14:"dates_short_id";a:3:{i:0;s:0:"";i:1;s:0:"";i:2;s:0:"";}}}';
$this->items = maybe_unserialize( $this->items );
$this->items[]=$this->items[121];
$this->items[]=$this->items[121];
$this->items[]=$this->items[121];
        foreach ( $this->items as $bk ) {            
	
			$bk_key++;
//TODO: blank data			
	$bk->form_show = '<div class="standard-content-form"> 
<strong>First Name</strong>:<span class="fieldvalue">William</span>&nbsp;&nbsp; 
<strong>Last Name</strong>:<span class="fieldvalue">Wilyson</span>&nbsp;&nbsp; 
<strong>Email</strong>:<span class="fieldvalue">Wilyson.example@test.com</span>&nbsp;&nbsp; 
<strong>Phone</strong>:<span class="fieldvalue">927-80-51</span>&nbsp;&nbsp; 
<strong>Adults</strong>:<span class="fieldvalue"> 1</span>&nbsp;&nbsp; 
<strong>Children</strong>:<span class="fieldvalue"></span>&nbsp;&nbsp; 
<strong>Details</strong>:&nbsp;&nbsp;<span class="fieldvalue"> </span> 
</div>';	
$bk->opsd_id = $bk->booking_id;
			
			
            $row_data = array();                
            $row_data[ 'availbale_locales' ] = $availbale_locales_in_system;            
            $row_data[ 'css' ]  = '';
            $row_data[ 'css' ] .= $bk_key % 2 ? '' : ' row_alternative_color';
            $row_data[ 'css' ] .= ( $bk_key == ( count( $this->items ) ) ) ? ' opsd-listing-last_row' : '';        
            ////////////////////////////////////////////////////////////////////
            
            $date_format = get_opsd_option( 'opsd_date_format' );
            if ( empty( $date_format ) ) $date_format = 'm / d / Y, D';
            $time_format = get_opsd_option( 'opsd_time_format' );            
            if ( empty( $time_format ) ) $time_format = 'h:i a';
			
            $row_data['cr_date'] = date_i18n( $date_format  , mysql2date( 'U', $bk->modification_date ) );
            $row_data['cr_time'] = date_i18n( $time_format  , mysql2date( 'U', $bk->modification_date ) );
            $row_data['id']         = $bk->opsd_id;                          // 100
            $row_data['is_new']     = (isset( $bk->is_new )) ? $bk->is_new : '0';
            $row_data['modification_date']   = (isset( $bk->modification_date )) ? $bk->modification_date : '';    // 2012-02-29 16:01:58
            $row_data['form']       = $bk->form;                                // select-one^rangetime5^10:00 - 12:00~text^name5^Jonny~text^secondname5^Smith~email^ ....
            $row_data['form_show']  = $bk->form_show;                           // First Name:Jonny   Last Name:Smith   Email:email@server.com  Country:GB  ....
            $row_data['form_data']  = $bk->form_data;                           // Array ([name] => Jonny... [_all_] => Array ( [rangetime5] => 10:00 - 12:00 [name5] => Jonny ... ) .... )
            $row_data['dates']      = $bk->dates;                               // Array ( [0] => stdClass Object ( [opsd_id] => 8 [opsd_date] => 2012-04-16 10:00:01 [approved] => 0 [type_id] => )
            $row_data['dates_short'] = $bk->dates_short;                        // Array ( [0] => 2012-04-16 10:00:01 [1] => - [2] => 2012-04-20 12:00:02 [3] => , [4] => 2012-04-16 10:00:01 ....
            $row_data['is_approved'] = ( count( $bk->dates ) > 0 ) ? $bk->dates[0]->approved : 0;

            //Is item in Trash.
            $row_data['is_trash'] = $bk->trash ;                                //FixIn: 6.1.1.10     
            
            // BL **************************************************************
            $row_data['dates_short_id'] = ( ( count( $bk->dates ) > 0 ) && ( isset( $bk->dates_short_id ) ) ) ? $bk->dates_short_id : array();    // Array ([0] => [1] => .... [4] => 6... [11] => [12] => 8 )
            
            // Get SHORT Dates showing data ////////////////////////////////////
            //$row_data['short_dates_content'] = opsd_get_short_dates_formated_to_show( $row_data['dates_short'], $row_data['is_approved'], $row_data['dates_short_id'], $this->opsd_types );
//TODO: blank data			
$row_data['short_dates_content'] = '<div class="securedownloads_dates_small" style="color: rgb(51, 51, 51); display: block;"><a href="javascript:void(0)" class="field-securedownloads-date label ">November 22, 2017<sup class="field-securedownloads-time"></sup></a><span class="date_tire"> - </span><a href="javascript:void(0)" class="field-securedownloads-date label ">November 26, 2017<sup class="field-securedownloads-time"></sup></a></div>';
            // Get WIDE Dates showing data /////////////////////////////////////
            //$row_data['wide_dates_content'] = opsd_get_wide_dates_formated_to_show( $row_data['dates'], $row_data['is_approved'], $this->opsd_types );
//TODO: blank data
$row_data['wide_dates_content'] = '<div class="securedownloads_dates_full" style="color: rgb(51, 51, 51); display: block;"><a href="javascript:void(0)" class="field-securedownloads-date label  approved ">September 14, 2017<sup class="field-securedownloads-time"></sup></a><span class="date_tire">, </span><a href="javascript:void(0)" class="field-securedownloads-date label  approved ">September 15, 2017<sup class="field-securedownloads-time"></sup></a><span class="date_tire">, </span><a href="javascript:void(0)" class="field-securedownloads-date label  approved ">September 16, 2017<sup class="field-securedownloads-time"></sup></a></div>';
            // P ***************************************************************
            $row_data['resource']       = ( isset( $bk->opsd_type ) ) ? $bk->opsd_type : '1';
            $row_data['resource_name']  = '<span class="label_resource_not_exist">' . __( 'Default', 'secure-downloads') . '</span>';
            
            if ( class_exists( 'opsd_personal' ) ) {
                
                if ( isset( $this->opsd_types[ $row_data['resource'] ] ) ) {
                    
                    $row_data['resource_name'] = $this->opsd_types[$row_data['resource']]->title;
                    $row_data['resource_name'] = apply_opsd_filter('opsd_check_for_active_language', $row_data['resource_name'] );
                    if ( strlen( $row_data['resource_name'] ) > 19 ) {
                        $row_data['resource_name'] = '<span style="cursor:pointer;" class="tooltip_top" title="' . $row_data['resource_name'] . '">' 
                                                    . substr( $row_data['resource_name'], 0, 13 ) 
                                                    . ' ... ' . substr( $row_data['resource_name'], -3 ) 
                                                    . '</span>';
                    }
                } else 
                    $row_data['resource_name'] = '<span class="label_resource_not_exist">' . __( 'Resource not exist', 'secure-downloads') . '</span>';                
            }
            
            $row_data['hash']       = (isset( $bk->hash )) ? $bk->hash : '';                // 99c9c2bd4fd0207e4376bdbf5ee473bc
            $row_data['remark']     = (isset( $bk->remark )) ? $bk->remark : '';
            
            // BS **************************************************************
            $row_data['cost']        = (isset( $bk->cost )) ? $bk->cost : '';                // 150.00
            $row_data['pay_status']  = (isset( $bk->pay_status )) ? $bk->pay_status : '';    // 30800
            $row_data['pay_request'] = (isset( $bk->pay_request )) ? $bk->pay_request : '';  // 0
            $row_data['status']      = (isset( $bk->status )) ? $bk->status : '';
            $row_data['is_paid']     = 0;
            $row_data['current_payment_status_titles'] = '';
            $row_data['pay_print_status'] = '';
            
            if ( class_exists( 'opsd_biz_s' ) ) {

                if ( opsd_is_payment_status_ok( trim( $row_data['pay_status'] ) ) )  $row_data['is_paid'] = 1;

                $payment_status_titles = get_payment_status_titles();
                $row_data['current_payment_status_titles']  = array_search( $row_data['pay_status'], $payment_status_titles );
                if ( $row_data['current_payment_status_titles'] === false ) 
                     $row_data['current_payment_status_titles'] = $row_data['pay_status'];

                
                if ( $row_data['is_paid'] ) {
                    $row_data['pay_print_status'] = __( 'Paid OK', 'secure-downloads');
                    if ( $row_data['current_payment_status_titles'] == 'Completed' )
                        $row_data['pay_print_status'] = $row_data['current_payment_status_titles'];
                } else if ( ( is_numeric( $row_data['pay_status'] ) ) || ( $row_data['pay_status'] == '' ) ) {
                    $row_data['pay_print_status'] = __( 'Unknown', 'secure-downloads');
                } else {
                    $row_data['pay_print_status'] = $row_data['current_payment_status_titles'];
                }
            }
            
            // Print data  /////////////////////////////////////////////////////
            $print_data[] = apply_opsd_filter( 'opsd_print_get_row'
                                            , array() 
                                            , $row_data['id']
                                            , $row_data['is_approved']
                                            , $row_data['form_show']
                                            , $row_data['resource_name']
                                            , $row_data['is_paid']
                                            , $row_data['pay_print_status']
                                            , ( $this->date_view_type == 'short' ) ? '<div class="opsd_dates_small">' 
                                                                                       . $row_data['short_dates_content'] 
                                                                                       . '</div>' 
                                                                                     : '<div class="opsd_dates_full">' 
                                                                                       . $row_data['wide_dates_content'] 
                                                                                       . '</div>'
                                            , $row_data['cost']
                                            , $row_data['resource']
                    );

            ////////////////////////////////////////////////////////////////////
            
            $this->show_row( $row_data, $is_free );               
        }
                
        make_opsd_action( 'opsd_listing_show_change_opsd_resources', $this->opsd_types );
                
        make_opsd_action( 'opsd_print_loyout', $print_data );
    }
    
    
    /** Show 1 Listing Row
     * 
     * @param array $row_data - Array of data to  show
     * @param boolean $is_free 
     */
    public function show_row( $row_data, $is_free ) {
        
        // is New
      ?><div id="opsd_mark_<?php echo $row_data[ 'id' ]; ?>"  
            class="<?php if ( $row_data[ 'is_new'] != '1') echo ' hidden_items '; ?> opsd-listing-collumn new-label clearfix-height">
             <a href="javascript:void(0)"  
                onclick="javascript:console.log( '<?php echo $row_data[ 'id' ]; ?>', 0, <?php echo $this->user_id; ?>, '<?php echo opsd_get_locale(); ?>' );"
                class="tooltip_right approve_opsd_link"                
                title="<?php _e('New item' , 'secure-downloads'); ?>" 
                ><i class="glyphicon glyphicon-flash"></i></a>
        </div><?php 
          
        // Row start
        ?><div id="opsd_row_<?php echo $row_data[ 'id' ]; ?>" class="row opsd_row clearfix-height opsd-listing-row <?php echo $row_data[ 'css' ]; ?><?php echo $is_free ? ' opsd_free' : ''; ?>"><?php 

            ?><div class="opsd-listing-collumn col-sm-<?php echo $is_free ? '2' : '3'; ?> col-xs-12">
                <div class="row"><?php 
                
                    // Checkbox
                  ?><div class="opsd-no-margin col-sm-1 col-xs-1 field-checkbox opsd_column_1 check-column">
                        <input type="checkbox" class="opsd-no-margin opsd_list_item_checkbox opsd_list_item_checkbox"
                               onclick="javascript: if (jQuery(this).attr('checked') !== undefined ) { jQuery(this).parent().parent().parent().parent().addClass('row_selected_color'); } else {jQuery(this).parent().parent().parent().parent().removeClass('row_selected_color');}"
                               id="opsd_id_selected_<?php  echo $row_data[ 'id' ];  ?>"  
                               name="opsd_appr_<?php  $row_data[ 'id' ];  ?>"
                               />
                    </div><?php 
                    
                    // ID
                  ?><div class="opsd-no-margin col-sm-1 col-xs-1 field-id text-center opsd_column_2">                            
                        <span class="label"><?php echo $row_data[ 'id' ]; ?></span>
                    </div><?php 
                    
                    // Labels
                  ?><div class="opsd-no-margin col-sm-<?php echo $is_free ? '6' : '7'; ?> col-xs-10 text-left field-labels opsd-labels opsd_column_3" >
                        <?php make_opsd_action('opsd_listing_show_label_resource', $row_data['resource_name'], $this->url['request'] .'&wh_opsd_type='. $row_data['resource'] );  ?>
                        <span class="label label-default label-pending <?php if ($row_data['is_approved']) echo ' hidden_items '; ?> "><?php _e('Pending' , 'secure-downloads'); ?></span>
                        <span class="label label-default label-approved <?php if (! $row_data['is_approved']) echo ' hidden_items '; ?>"><?php _e('Approved' , 'secure-downloads'); ?></span>
                        <?php make_opsd_action('opsd_listing_show_payment_label', $row_data['is_paid'],  $row_data['pay_print_status'], $row_data['current_payment_status_titles']);  ?>                        
                        <span class="label label-trash label-danger <?php if (! $row_data['is_trash']) echo ' hidden_items '; ?> "><?php _e('Trash' , 'secure-downloads'); ?></span><?php //FixIn: 6.1.1.10 ?>                  
                    </div><?php 
              ?></div>
            </div><?php 
                        
            // Data
            ?><div class="opsd-listing-collumn col-sm-6 col-xs-12 opsd-text-justify field-content opsd_column_4">
                <?php  echo $row_data['form_show'];  ?>
            </div><?php 
            
            //Dates
            ?><div class="opsd-listing-collumn col-sm-3 col-xs-12 text-center field-dates opsd-dates opsd_column_5">
                <div class="opsd_dates_small" style="<?php echo $this->days_column_style['short']; ?>"><?php echo $row_data['short_dates_content']; ?></div>
                <div class="opsd_dates_full"  style="<?php echo $this->days_column_style['wide'];  ?>"><?php echo $row_data['wide_dates_content'];  ?></div>                
            </div><?php
            
            if ( ! $is_free ) {
                ?><div class="clear"></div><?php 
            }
            
            // Actions
            ?><div class="opsd-listing-collumn col-sm-<?php echo $is_free ? '1' : '10'; ?> col-xs-12 text-left field-action-buttons opsd-actions opsd_column_6"><?php  

                // Cost
                make_opsd_action( 'opsd_listing_button_cost_edit', $row_data );
                
                
                ?><div class="actions-fields-group control-group"><?php 
                
                    // Payment Status                    
                    make_opsd_action('opsd_listing_button_payment_status', $row_data );

                    ?><span class="opsd-buttons-separator"></span><?php

                    // Edit
                    $row_data['edit_opsd_url'] = $this->url['add'] . '&opsd_type=' . $row_data['resource'] . '&opsd_hash=' . $row_data['hash'] . '&parent_res=1' ;
                    make_opsd_action( 'opsd_listing_button_edit', $row_data );

                    // Change item resource
                    make_opsd_action( 'opsd_listing_button_change_resource', $row_data );

                    // Duplicate
                    make_opsd_action( 'opsd_listing_button_duplicate', $row_data );
                    
                    // Print
                    make_opsd_action( 'opsd_listing_button_print', $row_data );
                    
                    // Notes
                    make_opsd_action( 'opsd_listing_button_notes', $row_data );
                    
                    // Change Locale
                    make_opsd_action( 'opsd_listing_button_locale', $row_data );
                    
                    
                    ?><span class="opsd-buttons-separator"></span><?php
                                        
                                                                                //FixIn: 6.1.1.10             
                    // Trash
                   ?><a href="javascript:void(0)" 
                        onclick="javascript:if ( opsd_are_you_sure('<?php echo esc_js(__('Do you really want to do this ?' , 'secure-downloads')); ?>') ) console.log( 1, <?php echo $row_data[ 'id' ]; ?>, <?php echo $this->user_id; ?>, '<?php echo opsd_get_locale(); ?>' , 1   );"
                        class="tooltip_top button-secondary button trash_opsd_link <?php if ( $row_data['is_trash'] ) echo ' hidden_items '; ?>" 
                        title="<?php _e('Move to trash' , 'secure-downloads'); ?>"                        
                    ><i class="glyphicon glyphicon-trash"></i></a><?php 
                    // Restore
                   ?><a href="javascript:void(0)" 
                        onclick="javascript:if ( opsd_are_you_sure('<?php echo esc_js(__('Do you really want to do this ?' , 'secure-downloads')); ?>') ) console.log( 0, <?php echo $row_data[ 'id' ]; ?>, <?php echo $this->user_id; ?>, '<?php echo opsd_get_locale(); ?>' , 1   );"
                        class="tooltip_top button-secondary button restore_opsd_link <?php if ( ! $row_data['is_trash'] ) echo ' hidden_items '; ?>" 
                        title="<?php _e('Restore' , 'secure-downloads'); ?>"                        
                    ><i class="glyphicon glyphicon-repeat"></i></a><?php 
                    // Delete
                   ?><a href="javascript:void(0)" 
                        onclick="javascript:if ( opsd_are_you_sure('<?php echo esc_js(__('Do you really want to delete this item ?' , 'secure-downloads')); ?>') ) console.log(<?php echo $row_data[ 'id' ]; ?>, <?php echo $this->user_id; ?>, '<?php echo opsd_get_locale(); ?>' , 1   );"
                        class="tooltip_top button-secondary button delete_opsd_link <?php if ( ! $row_data['is_trash'] ) echo ' hidden_items '; ?>" 
                        title="<?php _e('Completely Delete' , 'secure-downloads'); ?>"                        
                    ><i class="glyphicon glyphicon-remove"></i></a><?php 
                                                                                //End FixIn: 6.1.1.10         
                                        
                    
                    // Approve
                   ?><a href="javascript:void(0)" 
                        onclick="javascript:console.log(<?php echo $row_data[ 'id' ]; ?>,1,<?php echo $this->user_id; ?>,'<?php echo opsd_get_locale(); ?>',1);" 
                        class="tooltip_top approve_opsd_link button-secondary button <?php if ($row_data['is_approved']) echo ' hidden_items '; ?> " 
                        title="<?php _e('Approve' , 'secure-downloads'); ?>"
                    ><i class="glyphicon glyphicon-ok-circle"></i></a><?php  

                    // Reject
                   ?><a href="javascript:void(0)"
                        onclick="javascript:if ( opsd_are_you_sure('<?php echo esc_js(__('Do you really want to set item as pending ?' , 'secure-downloads')); ?>') ) console.log(<?php echo $row_data[ 'id' ]; ?>,0, <?php echo $this->user_id; ?>, '<?php echo opsd_get_locale(); ?>' , 1  );"
                        class="tooltip_top pending_opsd_link button-secondary button <?php if (! $row_data['is_approved']) echo ' hidden_items '; ?> "
                        title="<?php _e('Reject' , 'secure-downloads'); ?>" 
                    ><i class="glyphicon glyphicon-ban-circle"></i></a><?php 
                
                ?></div><?php 
                
            ?></div><?php 

            // Created Date
            ?><div class="opsd-listing-collumn col-sm-<?php echo $is_free ? '12' : '2'; ?> col-xs-12 text-left field-system-info opsd_column_7"><?php  
                ?><span><?php _e('Created' , 'secure-downloads'); ?>:</span> <span class="field-creation-date"><?php echo $row_data['cr_date'], ' ', $row_data['cr_time']; ?></span><?php 
            ?></div><?php
            
                        
            // Notes Section
            make_opsd_action( 'opsd_listing_section_notes', $row_data );
            
            // Change Resources section
            make_opsd_action( 'opsd_listing_section_change_resource', $row_data );
            
            // Payment Status Section
            make_opsd_action( 'opsd_listing_section_payment_status', $row_data );
            
            
        ?></div><?php 
        
    }
        
}