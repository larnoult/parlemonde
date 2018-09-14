<?php 
/**
 * @version 1.0
 * @package Products
 * @subpackage Support for Download Functions
 * @category Products
 * 
 * @author wpdevelop
 * @link http://oplugins.com/
 * @email info@oplugins.com
 *
 * @modified 2017-03-01
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly


/** Products for downloading */
class OPSD_Products {

    protected $products = array();

    public static $settings = array( 'csv_separator' => ',' );     // unlike a const, static property values can be changed
    
    function __construct( $csv = '' ) {
       
		$opsd_csv_separator = get_opsd_option( 'opsd_csv_separator' );
		if ( ! empty( $opsd_csv_separator ) )
			self::$settings[ 'csv_separator' ] = $opsd_csv_separator;

		if ( ! empty( $csv ) ) {
            $this->define_products_from_csv( $csv );
        }
    }
    
    /** Parse CSV in specific format and Load Products.
     * 
     * @param string $csv
     */
    public function define_products_from_csv( $csv = '' ) {
        
		if ( empty( $csv ) ) {
			$csv = get_opsd_option('opsd_products_csv' ); 
		}
		
		// ID | Title | Version Number | Desciption | Path
		// 
        // $this->products = array();                                           //Do we need to  reset exist  products list
        
        $defaults = array(      'id' => ''
											, 'order'   => '0'
											, 'ip'      => '0.0.0.0'						// '0.0.0.0' or '0' - Its means any IP
											, 'expire'  => '+1 day'                         // for usage in strtotime function
                    );
                
        if ( ! empty( $csv ) ) {
            
            $csv = str_replace( array("\r\n", "\r"), "\n", $csv );
            $csv_lines = explode( "\n", $csv );
            
            foreach ( $csv_lines as $csv_line ) {
                
                //0,Prod,Description,XXXXXXXXXX/fold/product.zip,0,1440
                $product_from_csv = explode( self::$settings['csv_separator'],  $csv_line );                
				if ( count($product_from_csv) > 4 ) {	
					$product_arr = array();
					$i = 0;
					
// 137^Title OPSD^Version Number OPSD^Description^http://new/wp-content/uploads/opsd_lSJacOT1yVLFnrkqt2xR/2017/04/1-1.txt
					
					$product_arr[ 'id' ]			= trim( $product_from_csv[ $i ++ ] );
					$product_arr[ 'title' ]			= trim( $product_from_csv[ $i ++ ] );
					$product_arr[ 'version_num' ]		= trim( $product_from_csv[ $i ++ ] );
					$product_arr[ 'description' ]	= trim( $product_from_csv[ $i ++ ] );
					$product_arr[ 'path' ]			= trim( str_replace( site_url(), '',  $product_from_csv[ $i ++ ] ) );	// Get local relative path
					
					$this->products[] = wp_parse_args( $product_arr, $defaults );   // Add Product
				} else if ( count( $product_from_csv ) == 1 ){

					// Get section  title - usually  its contain only  from TEXT row without COLUMN SEPARTORs
					$this->products[] = array( 'id' => microtime( false ) + wp_rand( 100 ), 'title' => $product_from_csv[0] );
				}
            }            
        } 
		//debuge($this->products);
    }
    
	
	/** Count Real Products with  Path to  files
	 * 
	 * @return int
	 */
	function get_products_count() {
		$cnt = 0;
		foreach ( $this->products as $prod ) {
			if ( ! empty( $prod[ 'path' ] ) )
				$cnt ++;
		}
		return $cnt;
	}


	/** Save products from products array
	 * 
	 * @param array $new_products_arr
	 */
	public function save_products( $new_products_arr ) {
		
		if ( is_array( $new_products_arr )) {
		
			$csv_line = array();
			foreach ( $new_products_arr as $product_arr ) {
				
				$csv_arr = array();
				
				if ( isset( $product_arr['id'] ) ) {
					$csv_arr[] = str_replace( self::$settings['csv_separator'], '_', $product_arr['id'] );				// Fix issue of not have separator in contne of field
				}
				if ( isset( $product_arr['title'] ) ) {
					$csv_arr[] = str_replace( self::$settings['csv_separator'], '_', $product_arr['title'] );				// Fix issue of not have separator in contne of field
				}
				if ( isset( $product_arr['version_num'] ) ) {
					$csv_arr[] = str_replace( self::$settings['csv_separator'], '_', $product_arr['version_num'] );				// Fix issue of not have separator in contne of field
				}
				if ( isset( $product_arr['description'] ) ) {
					$csv_arr[] = str_replace( self::$settings['csv_separator'], '_', $product_arr['description'] );				// Fix issue of not have separator in contne of field
				}
				if ( isset( $product_arr['path'] ) ) {
					$csv_arr[] = str_replace( self::$settings['csv_separator'], '', $product_arr['path'] );				// Fix issue of not have separator in contne of field
				}
									
				$csv_line[] = implode( self::$settings['csv_separator'], $csv_arr );
			}
				
			$csv_line = implode( "\n", $csv_line );
			
			update_opsd_option( 'opsd_products_csv', $csv_line );
			
			return $csv_line;
		}
		
		return  false;
	}
    
	
    public function get_products() {
        return $this->products;
    }
    
    
    /** Get product  by ID
     * 
     * @param type $id
     * @return array | boolean
     */
    public function get_product( $id ) {
        foreach ( $this->products as $product ) {
            if ( $product['id'] == $id )
                return $product;
        }
        return false;
    }   
}


/** Get Array  of properties for specific product
 * 
 * @param int $product_id - id of resource
 * @return  array (
            [id] => 2
            [order] => 0
            [ip] => 0.0.0.0
            [ipl] => 0
            [expire] => +1440 seconds
            [title] => Pro (1 site usage)
            [size] => 3.45
            [path] => XXXXXXXXXX/pro/pro.zip
 */
function opsd_get_product( $product_id ) {
    
    $products_obj = new OPSD_Products(  get_opsd_option('opsd_products_csv' )  );
    
    return $products_obj->get_product( $product_id );
}


/** Get replace shortcode for different products.
 * 
 * @param array $product_id_arr  -  product array
 * @param array $opt - optional array with  parameters, which redefined default product values   array(             
                                                                                                        'expire' => '+1 day'            
                                                                                                        'ip' => '149.10.0.1'            
                                                                                                      )
 * @return array                                                                array ( 
                                                                                        [products_list] => Array( .... )
                                                                                        [product_id] - ID of product
                                                                                        [product_title] - title of product
                                                                                        [product_description] - description of product
                                                                                        [product_link] - secure URL for product download
                                                                                        [product_filename] - filename of the product
                                                                                        [product_size] - the download size in a friendly format such as 500 KB or 3.45 MB
                                                                                        [product_expire_after] - expiry time in friendly format (e.g. 24 hours or 2 days etc)
                                                                                        [product_expire_date] - exact expiry date and time (e.g. 2017-03-21 18:30)
                                                                                        [product_summary] - complete product details including title, size, link etc
                                                                                        ---
                                                                                        [siteurl] - inserting your site URL
                                                                                        [remote_ip] - inserting IP address of the user who made this action
                                                                                        [user_agent] - inserting contents of the User-Agent: header from the current request, if there is one
                                                                                        [request_url] - inserting address of the page (if any), where visitor make this action
                                                                                        [current_date] - inserting date of this action
                                                                                        [current_time] - inserting time of this action 
                                                                                    )
 */
function opsd_get_product_replace_shortcodes( $product_id_arr = array(), $opt = array() ) {

    $replace = array( 'products_list' => array() );
    
    // Init and load all  products
    $products_obj = new OPSD_Products(  get_opsd_option('opsd_products_csv' )  );
    $product_id_arr = (array) $product_id_arr;
    
    foreach ( $product_id_arr as $product_id ) {
        
        $products_replace = array();
        
        $product_arr = $products_obj->get_product( $product_id );                      

		if (	   ( empty( $product_arr[ 'path' ] ) ) 
				|| ( empty( $product_arr[ 'id' ]   ) ) 
			)
			continue;

		$products_replace['product_id']         = $product_arr['id'];               // 1
        $products_replace['product_title']      = apply_opsd_filter( 'opsd_check_for_active_language', $product_arr['title'] );            // Product Name
        $products_replace['product_version']    = apply_opsd_filter( 'opsd_check_for_active_language', $product_arr['version_num'] );          // May be update num		
        $products_replace['product_description']= apply_opsd_filter( 'opsd_check_for_active_language', $product_arr['description'] );      // Tra ta ta ...         
        
		if ( ! empty( $opt[ 'order' ] ) ) {
			
			$products_replace['order'] = $opt[ 'order' ];
			
			if ( strpos( $opt[ 'order' ], '@' ) !== false ) {
				$products_replace['link_sent_to'] = $opt[ 'order' ];
			}
		}
		
		$products_replace['product_link']  = opsd_get_secret_link( $product_arr, $opt );       // http://server.com/?prefix=922015&product=MSwxNDg5OTI3Njg4LDAsMC4wLjAuMCwwLGFmYTFmMW/product.zip 
        $products_replace['product_link']  = htmlspecialchars_decode( 
                                                                      //    '<a href="' . 
                                                                            esc_url( $products_replace['product_link'] ) 
                                                                      //    . '">' . __('here', 'secure-downloads') . '</a>'  
                                                                    );            
                // Get Redable Size of file
                $real_link = rtrim( get_site_url() , '/' ) . '/' . ltrim( $product_arr[ 'path' ], '/\\' );        

                $real_size = OPSD_Download::get_file_size(  OPSD_Download::get_local_path_from_real_link( $real_link )  );
                $redable_size = OPSD_Download::readable_format_file_size( $real_size );
                
        $products_replace['product_size']       = $redable_size;                                    // 2.93 MB        
        $products_replace['product_filename']   = OPSD_Download::get_file_name_from_path( $real_link );       // product.zip
        
                // Do we ovveride expire CSV parameter  from request  FORM
                if ( ! empty( $opt['expire'] ) ) $product_arr['expire'] = $opt['expire'];       
				
				if ( opsd_is_valid_timestamp( $product_arr['expire'] ) ) {
					$expire_time_stamp = $product_arr['expire'];
				} else {
					$expire_time_stamp = strtotime( $product_arr['expire'] , current_time( 'timestamp' ) );
				}
				
                $readable_expire = OPSD_Download::readable_format_seconds_to_words(  $expire_time_stamp - current_time( 'timestamp' )  );
                
		// 2 days		
        $products_replace['product_expire_after'] = $readable_expire;                               
                                                                                        
		// 2017-03-19 14:48:08
        $products_replace['product_expire_date']  = date_i18n( get_opsd_option( 'opsd_date_format' ) . ' ' . get_opsd_option( 'opsd_time_format' )
																, $expire_time_stamp );  
		
        $products_replace['product_summary'] =   '<strong>' . $products_replace['product_title'] . '</strong> '
                                               . '<a href="' . $products_replace['product_link'] . '" target="_blank">' 
                                               .    $products_replace['product_filename']
                                               . '</a>'
                                               . ' ('. $products_replace['product_size'] .')' 
                                               . ' ~ ' . __( 'expire in', 'secure-downloads' ) . ' ' . $products_replace['product_expire_after'];
        $replace['products_list'][]= $products_replace;     
    }       
    
    $replace[ 'siteurl' ]       = htmlspecialchars_decode( '<a href="' . home_url() . '">' . home_url() . '</a>' );
    $replace[ 'remote_ip'     ] = opsd_get_user_ip();          // The IP address from which the user is viewing the current page. 
    $replace[ 'user_agent'    ] = (isset($_SERVER['HTTP_USER_AGENT'])) ? $_SERVER['HTTP_USER_AGENT'] : '';  // Contents of the User-Agent: header from the current request, if there is one. 
    $replace[ 'request_url'   ] = (isset($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : '';        // The address of the page (if any) where action was occured. Because we are sending it in Ajax request, we need to use the REFERER HTTP
    $replace[ 'current_date' ]  = date_i18n( get_opsd_option( 'opsd_date_format' ) );
    $replace[ 'current_time' ]  = date_i18n( get_opsd_option( 'opsd_time_format' ) );                                                    

    // Get values for 1-st product
    if ( ! empty( $replace['products_list'] ) ){                                
        foreach ( $replace['products_list'][0] as $product_key => $product_value ) {
            $replace[ $product_key ] = $product_value;
        }
    }
    
//debuge('$replace',$replace);

    return $replace;
}