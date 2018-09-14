<?php
/**
 * Custom functions
 */


function kf_reflush_rules() {
    global $wp_rewrite;
    $wp_rewrite->flush_rules();
}
add_action( 'after_switch_theme', 'kf_reflush_rules' );
 add_action('kadence_gallery_album_before', 'kad_gallery_container_open');
  add_action('kadence_gallery_post_before', 'kad_gallery_container_open');
  function kad_gallery_container_open() {
  	    echo '<div id="content" class="container"><div class="row">';
        echo '<div class="main '.virtue_main_class().' postclass" role="main">';
  }
  add_action('kadence_gallery_album_after', 'kad_gallery_container_close');
  add_action('kadence_gallery_post_after', 'kad_gallery_container_close');
  function kad_gallery_container_close() {
  	    echo '</div>';
  }
add_action( 'kt_beforeheader', 'revolutionslider_top', 1 );
function revolutionslider_top() {
    if ( is_front_page() ){
    global $virtue_premium;
    if(isset($virtue_premium['above_header_slider']) && $virtue_premium['above_header_slider'] == 1) {
        if(isset($virtue_premium['choose_slider']) && ($virtue_premium['choose_slider'] == 'ktslider' || $virtue_premium['choose_slider'] == 'cyclone' || $virtue_premium['choose_slider'] == 'rev' ||  $virtue_premium['choose_slider'] == 'ksp' ) ) {
            $mobile_detect = false;
            if(isset($virtue_premium['mobile_switch']) && $virtue_premium['mobile_switch']  == '1') {
                $mobile_slider = true;
                $detect = new Mobile_Detect_Virtue; 
                if(isset($virtue_premium['mobile_tablet_show']) && $virtue_premium['mobile_tablet_show']  == '1') {
                    if($detect->isMobile()) {
                        $mobile_detect = true;
                    } else {
                        $mobile_detect = false;
                    }
                } else {
                    if($detect->isMobile() && !$detect->isTablet()) {
                        $mobile_detect = true;
                    } else {
                        $mobile_detect = false;
                    }
                }
            } else {
                $mobile_slider = false;
            }
            if(($mobile_slider == true) && ($mobile_detect == true)){
                $slider = $virtue_premium['choose_mobile_slider'];
                echo '<div class="kad_fullslider_mobile">';
                if ($slider == "rev") {
                    get_template_part('templates/mobile_home/mobilerev', 'slider');
                } else if ($slider == "ksp") {
                    get_template_part('templates/mobile_home/mobileksp', 'slider');
                } else if ($slider == "flex") {
                    get_template_part('templates/mobile_home/mobileflex', 'slider');
                } else if ($slider == "video") {
                    get_template_part('templates/mobile_home/mobilevideo', 'block');
                } else if ($slider == "cyclone") {
                    get_template_part('templates/mobile_home/cyclone', 'slider');
                }
                echo '</div>';
            } else {
                if($virtue_premium['choose_slider'] == 'rev') {
                    echo '<div class="kad_fullslider">';
                    if( function_exists('putRevSlider') ) {
                        putRevSlider( $virtue_premium['rev_slider'] );
                    }
                } else if($virtue_premium['choose_slider'] == 'ktslider') {
                    echo '<div class="kad_fullslider">';
                    echo do_shortcode('[kadence_slider id='.$virtue_premium['kt_slider'].']');
                } else if($virtue_premium['choose_slider'] == 'ksp') {
                    echo '<div class="kad_fullslider">';
                    echo do_shortcode('[kadence_slider_pro id='.$virtue_premium['ksp_slider'].']');
                } else if($virtue_premium['choose_slider'] == 'cyclone') {
                    echo '<div class="kad_fullslider">';
                    echo do_shortcode( $virtue_premium['home_cyclone_slider'] );
                }

                    if(isset($virtue_premium['above_header_slider_arrow']) && $virtue_premium['above_header_slider_arrow'] == 1) {
                        echo '<div class="kad_fullslider_arrow"><a href="#the-top-menu"><i class="icon-arrow-down"></i></a></div>';
                        echo '<div id="the-top-menu"></div>';
                    }
                echo '</div>';
                if(isset($virtue_premium['header_style']) && $virtue_premium['header_style'] == 'shrink') {
                    $head_height = $virtue_premium['header_height']/2;
                    if( isset( $virtue_premium['topbar'] ) && 1 == $virtue_premium['topbar'] ) {
                    	echo '<style type="text/css" media="screen">@media (min-width: 992px) {.kad-header-style-three #kad-shrinkheader, .kad-header-style-three #logo a.brand, .kad-header-style-three #logo #thelogo, .kad-header-style-three #nav-main ul.sf-menu > li > a {height:'.esc_attr($head_height).'px !important;line-height: '.esc_attr($head_height).'px !important;}.kad-header-style-three #thelogo img {max-height: '.esc_attr($head_height).'px !important;} .stickyheader #kad-banner {height:auto !important;}}</style>';
                    } else {
                    	echo '<style type="text/css" media="screen">@media (min-width: 992px) {.kad-header-style-three #kad-shrinkheader, .kad-header-style-three #logo a.brand, .kad-header-style-three #logo #thelogo, .kad-header-style-three #nav-main ul.sf-menu > li > a, .stickyheader #kad-banner {height:'.esc_attr($head_height).'px !important;line-height: '.esc_attr($head_height).'px !important;}.kad-header-style-three #thelogo img {max-height: '.esc_attr($head_height).'px !important;}}</style>';
                    }
                }
            }
        }
    }
}
}
add_action( 'kt_beforeheader', 'featureslider_top', 1 );
function featureslider_top() {
  if ( is_page_template('page-feature.php') || is_page_template('page-feature-sidebar.php') ){
  global $post, $virtue_premium;
  $slider = get_post_meta( $post->ID, '_kad_page_head', true ); 
  $above = get_post_meta( $post->ID, '_kad_shortcode_above_header', true );
  $arrow = get_post_meta( $post->ID, '_kad_shortcode_above_header_arrow', true ); 
  if(isset($above) && $above == 'on') {
    if(isset($slider) && ($slider == 'ktslider' || $slider == 'cyclone' || $slider == 'rev')) {
    if($slider == 'rev') {
      echo '<div class="kad_fullslider">';
      get_template_part('templates/rev', 'slider');
    }
    else if($slider == 'ktslider') {
      echo '<div class="kad_fullslider">';
      get_template_part('templates/cyclone', 'slider');
    } 
    else if($slider == 'cyclone') {
      echo '<div class="kad_fullslider">';
      get_template_part('templates/cyclone', 'slider');
    }
      if(isset($arrow) && $arrow == "on") {
        echo '<div class="kad_fullslider_arrow"><a href="#the-top-menu"><i class="icon-arrow-down"></i></a></div>';
        echo '<div id="the-top-menu"></div>';
      }
      echo '</div>';
      if(isset($virtue_premium['header_style']) && $virtue_premium['header_style'] == 'shrink') {
        $head_height = $virtue_premium['header_height']/2;
           echo '<style type="text/css" media="screen">@media (min-width: 992px) {.kad-header-style-three #kad-shrinkheader, .kad-header-style-three #logo a.brand, .kad-header-style-three #logo #thelogo, .kad-header-style-three #nav-main ul.sf-menu > li > a {height:'.$head_height.'px !important;line-height: '.$head_height.'px !important;}.kad-header-style-three #thelogo img {max-height: '.$head_height.'px !important;}}</style>';
      }
    }
  }
}
}

  add_filter('kadence_wrap_base', 'kadence_wrap_base_kadslider'); // Add our function to the roots_wrap_base filter

  function kadence_wrap_base_kadslider($templates) {
    $cpt = get_post_type(); // Get the current post type
    if ($cpt == 'kadslider') {
       array_unshift($templates, 'base-kadslider.php'); // Shift the template to the front of the array
    }
    return $templates; // Return our modified array with base-$cpt.php at the front of the queue
  }

function virtue_template_override_init() {
	if(class_exists('EventOrganiser_Admin_Page')) {
	    add_filter('template_include', 'virtue_evento_venue_overide', 20);
	    function virtue_evento_venue_overide($template) {
	          if(is_tax( 'event-venue' ) ) {
	            remove_filter('template_include', array('Kadence_Wrapping', 'wrap'), 101);
	            add_filter('template_include', array('Kadence_Wrapping', 'wrap'), 99999);
	          }
	          return $template;
	    }
	}
}
add_action('init', 'virtue_template_override_init');

// Add support for qtranslate
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if ( is_plugin_active('qtranslate/qtranslate.php') || is_plugin_active('mqtranslate/mqtranslate.php') ) {
    add_action('portfolio-type_add_form',  'qtrans_modifyTermFormFor');
    add_action('portfolio-type_edit_form',   'qtrans_modifyTermFormFor');
    add_action('product_cat_add_form',   'qtrans_modifyTermFormFor');
    add_action('product_cat_edit_form',  'qtrans_modifyTermFormFor');
    add_action('product_tag_add_form',   'qtrans_modifyTermFormFor');
    add_action('product_tag_edit_form',  'qtrans_modifyTermFormFor');
    add_filter('woocommerce_cart_item_name', 'qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage', 0);
}

add_action( 'init', 'kt_rev_slider_as_theme');
function kt_rev_slider_as_theme() {
  if(is_admin()){ 
    if(function_exists( 'set_revslider_as_theme' )) {
      set_revslider_as_theme();
      kt_hide_revslider_notice();
    }
  }
}
function kt_hide_revslider_notice() {
  global $virtue_premium;
  if(isset($virtue_premium['hide_rev_activation_notice']) && $virtue_premium['hide_rev_activation_notice'] == 1) {
    add_action('admin_head', 'kt_hide_revslider_notice_css');
    update_option('revslider-valid-notice', 'false');
    remove_action('admin_notices', array('RevSliderAdmin', 'add_plugins_page_notices'));
      function kt_hide_revslider_notice_css() {
        echo '<style>
          .toplevel_page_revslider .rs-dashboard {
                display: none;
            }
        </style>';
      }
  }
}


add_filter('template_include', 'kad_ph_project_override', 20);
function kad_ph_project_override($template) {
      $cpt = get_post_type();
      if ($cpt == 'ph-project') {
        remove_filter('template_include', array('Kadence_Wrapping', 'wrap'), 101);
      }
      return $template;
}


add_filter('wp_nav_menu_items', 'kt_add_search_form_to_menu', 10, 2);
function kt_add_search_form_to_menu($items, $args) {
  global $virtue_premium, $woocommerce;
 
    if( !($args->theme_location == 'primary_navigation') || (isset($virtue_premium['header_style']) && $virtue_premium['header_style'] == "center" ) )
        return $items;

      ob_start();
      ?>
    <?php if (class_exists('woocommerce'))  {

        if(isset($virtue_premium['menu_account']) && $virtue_premium['menu_account'] == '1') { ?>
        <li class="menu-account-icon-kt sf-dropdown">
            <?php if ( is_user_logged_in() ) { ?>
            <a class="menu-account-btn" title="<?php echo __('My Account', 'virtue');?>" href="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ); ?>">
                <div class="kt-my-account-container"><i class="icon-user2"></i></div>
            </a>
            <?php } else { 
                if(get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes') {
                   $title =  __('Login/Signup', 'virtue');
                } else {
                    $title =  __('Login', 'virtue');
                } ?>
             <a class="menu-account-btn" title="<?php echo $title;?>" href="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ); ?>">
                <div class="kt-my-account-container"><i class="icon-user2"></i></div>
            </a>

              <?php  } ?>
        </li>
        <?php }

        if(isset($virtue_premium['menu_cart']) && $virtue_premium['menu_cart'] == '1') { ?>
        <li class="menu-cart-icon-kt sf-dropdown">
        <a class="menu-cart-btn" title="<?php echo __('Your Cart', 'virtue');?>" href="<?php echo esc_url(wc_get_cart_url()); ?>">
          <div class="kt-cart-container"><i class="icon-cart"></i><span class="kt-cart-total"><?php echo $woocommerce->cart->get_cart_contents_count(); ?></span></div>
        </a>
        <ul id="kad-head-cart-popup" class="sf-dropdown-menu kad-head-cart-popup">
            <div class="kt-header-mini-cart-refreash">
            <?php woocommerce_mini_cart(); 
            	do_action( 'kadence_cart_menu_popup_after' ); ?>
            </div>
          </ul>
        </li>
        <?php }
     }?>
    <?php if(isset($virtue_premium['menu_search']) && $virtue_premium['menu_search'] == '1') { ?>
    <li class="menu-search-icon-kt">
      <a class="kt-menu-search-btn collapsed" title="<?php echo __('Search', 'virtue');?>" data-toggle="collapse" data-target="#kad-menu-search-popup">
        <i class="icon-search"></i>
      </a>
        <div id="kad-menu-search-popup" class="search-container container collapse">
          <div class="kt-search-container">
          <?php if(class_exists('woocommerce') && isset($virtue_premium['menu_search_woo']) && $virtue_premium['menu_search_woo'] == '1') { 
            		get_product_search_form();
          		} else { 
              		get_search_form();
            	} ?>
          </div>
        </div>
    </li>
    <?php } ?>
   <?php  $output  = ob_get_contents();
        ob_end_clean();
    return $items . $output;
}
add_action('init', 'virtue_init_cart_frag');
function virtue_init_cart_frag(){
	if (class_exists('woocommerce')) {
		if ( version_compare( WC_VERSION, '2.7', '>' ) ) {
		   	add_filter('woocommerce_add_to_cart_fragments', 'kt_get_refreshed_fragments');
		} else {
		   	add_filter('add_to_cart_fragments', 'kt_get_refreshed_fragments');
		}
		function kt_get_refreshed_fragments($fragments) {
		    // Get mini cart
		    ob_start();

		    woocommerce_mini_cart();

		    $mini_cart = ob_get_clean();

		    // Fragments and mini cart are returned
		    $fragments['div.kt-header-mini-cart-refreash'] ='<div class="kt-header-mini-cart-refreash">' . $mini_cart . '</div>';

		    return $fragments;

		}
		if ( version_compare( WC_VERSION, '2.7', '>' ) ) {
		   	add_filter('woocommerce_add_to_cart_fragments', 'kt_get_refreshed_fragments_number');
		} else {
		   	add_filter('add_to_cart_fragments', 'kt_get_refreshed_fragments_number');
		}
		function kt_get_refreshed_fragments_number($fragments) {
		    global $woocommerce;
		    // Get mini cart
		    ob_start();

		    ?><span class="kt-cart-total"><?php echo WC()->cart->get_cart_contents_count(); ?></span> <?php

		    $fragments['span.kt-cart-total'] = ob_get_clean();

		    return $fragments;

		}
	}
}
  
function kad_hex2rgb($hex) {
   $hex = str_replace("#", "", $hex);

   if(strlen($hex) == 3) {
      $r = hexdec(substr($hex,0,1).substr($hex,0,1));
      $g = hexdec(substr($hex,1,1).substr($hex,1,1));
      $b = hexdec(substr($hex,2,1).substr($hex,2,1));
   } else {
      $r = hexdec(substr($hex,0,2));
      $g = hexdec(substr($hex,2,2));
      $b = hexdec(substr($hex,4,2));
   }
   $rgb = array($r, $g, $b);
   //return implode(",", $rgb); // returns the rgb values separated by commas
   return $rgb; // returns an array with the rgb values
}

//
function kt_get_srcset($width,$height,$url,$id) {
  if(empty($id) || empty($url)) {
    return;
  }
  
  $image_meta = get_post_meta( $id, '_wp_attachment_metadata', true );
  if(empty($image_meta['file'])){
    return;
  }
  	// If possible add in our images on the fly sizes
  	$ext = substr($image_meta['file'], strrpos($image_meta['file'], "."));
  	$pathflyfilename = str_replace($ext,'-'.$width.'x'.$height.'' . $ext, $image_meta['file']);
  	$retina_w = $width*2;
	$retina_h = $height*2;
  	$pathretinaflyfilename = str_replace($ext, '-'.$retina_w.'x'.$retina_h . $ext, $image_meta['file']);
  	$flyfilename = basename($image_meta['file'], $ext) . '-'.$width.'x'.$height.'' . $ext;
  	$retinaflyfilename = basename($image_meta['file'], $ext) . '-'.$retina_w.'x'.$retina_h . $ext;


  	$upload_info = wp_upload_dir();
  	$upload_dir = $upload_info['basedir'];

  	$flyfile = trailingslashit($upload_dir).$pathflyfilename;
  	$retinafile = trailingslashit($upload_dir).$pathretinaflyfilename;
  	if(empty($image_meta['sizes']) ){ $image_meta['sizes'] = array();}
    	if (file_exists($flyfile)) {
     	$kt_add_imagesize = array(
        	'kt_on_fly' => array( 
          	'file'=> $flyfilename,
          	'width' => $width,
          	'height' => $height,
          	'mime-type' => isset($image_meta['sizes']['thumbnail']) ? $image_meta['sizes']['thumbnail']['mime-type'] : '',
          )
      );
      $image_meta['sizes'] = array_merge($image_meta['sizes'], $kt_add_imagesize);
    }
    if (file_exists($retinafile)) {
        $kt_add_imagesize_retina = array(
        	'kt_on_fly_retina' => array( 
          	'file'=> $retinaflyfilename,
          	'width' => 2 * $width,
          	'height' => 2 * $height,
          	'mime-type' => isset($image_meta['sizes']['thumbnail']) ? $image_meta['sizes']['thumbnail']['mime-type'] : '',
          )
        );
        $image_meta['sizes'] = array_merge($image_meta['sizes'], $kt_add_imagesize_retina);
    }
    if(function_exists ( 'wp_calculate_image_srcset') ){
      	$output = wp_calculate_image_srcset(array( $width, $height), $url, $image_meta, $id);
    } else {
      	$output = '';
    }
    return $output;
}
function kt_get_srcset_output($width,$height,$url,$id) {
    $img_srcset = kt_get_srcset( $width, $height, $url, $id);
    if(!empty($img_srcset) ) {
      $output = 'srcset="'.esc_attr($img_srcset).'" sizes="(max-width: '.esc_attr($width).'px) 100vw, '.esc_attr($width).'px"';
    } else {
      $output = '';
    }
    return $output;
}
function virtue_get_image_id_by_link( $attachment_url ) {
	global $wpdb;
	$attachment_id = false;
 
	// If there is no url, return.
	if ( '' == $attachment_url )
		return;
 

	// Define upload path & dir.
	$upload_info = wp_upload_dir();
	$upload_dir = $upload_info['basedir'];
	$upload_url = $upload_info['baseurl'];

	$http_prefix = "http://";
	$https_prefix = "https://";
	$relative_prefix = "//"; // The protocol-relative URL

	/* if the $url scheme differs from $upload_url scheme, make them match 
	if the schemes differe, images don't show up. */
	if( ! strncmp( $attachment_url, $https_prefix, strlen( $https_prefix ) ) ) { //if url begins with https:// make $upload_url begin with https:// as well
		$upload_url = str_replace( $http_prefix, $https_prefix, $upload_url) ;
	} else if ( ! strncmp( $attachment_url, $http_prefix, strlen( $http_prefix ) ) ) { //if url begins with http:// make $upload_url begin with http:// as well
		$upload_url = str_replace( $https_prefix, $http_prefix, $upload_url );      
	} else if ( ! strncmp( $attachment_url, $relative_prefix, strlen( $relative_prefix ) ) ){ //if url begins with // make $upload_url begin with // as well
		$upload_url = str_replace( array( 0 => "$http_prefix", 1 => "$https_prefix"), $relative_prefix, $upload_url );
	}
 
	// Make sure the upload path base directory exists in the attachment URL, to verify that we're working with a media library image
	if ( false !== strpos( $attachment_url, $upload_url ) ) {
 		
 		$attachment_new_url = str_replace( $upload_url . '/', '', $attachment_url );
			
		$attachment_id = $wpdb->get_var( $wpdb->prepare( "SELECT wposts.ID FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta WHERE wposts.ID = wpostmeta.post_id AND wpostmeta.meta_key = '_wp_attached_file' AND wpostmeta.meta_value = '%s' AND wposts.post_type = 'attachment'", $attachment_new_url ) );

		if( ! $attachment_id ) {
			// If this is the URL of an auto-generated thumbnail, get the URL of the original image
			$attachment_url = preg_replace( '/-\d+x\d+(?=\.(jpg|jpeg|png|gif)$)/i', '', $attachment_url );
	 
			// Remove the upload path base directory from the attachment URL
			$attachment_url = str_replace( $upload_url . '/', '', $attachment_url );
	 
			// Finally, run a custom database query to get the attachment ID from the modified attachment URL
			$attachment_id = $wpdb->get_var( $wpdb->prepare( "SELECT wposts.ID FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta WHERE wposts.ID = wpostmeta.post_id AND wpostmeta.meta_key = '_wp_attached_file' AND wpostmeta.meta_value = '%s' AND wposts.post_type = 'attachment'", $attachment_url ) );
		}
 
	}
 
	return $attachment_id;
}

function virtue_carousel_column_array($columns, $sidebar = false) {
    if ( empty( $columns ) ) {
        $columns = 3;
    }
    $cc = array();
    if($columns == 6) {
        $cc['md'] = 6; 
        $cc['sm'] = 4; 
        $cc['xs'] = 3;
        $cc['ss'] = 2;
    } else if($columns == 5) {
        $cc['md'] = 5; 
        $cc['sm'] = 4; 
        $cc['xs'] = 3;
        $cc['ss'] = 2;
    }  else if($columns == 4) {
        $cc['md'] = 4; 
        $cc['sm'] = 3; 
        $cc['xs'] = 2;
        $cc['ss'] = 1;
    } else if($columns == 3) {
        $cc['md'] = 3; 
        $cc['sm'] = 3; 
        $cc['xs'] = 2;
        $cc['ss'] = 1;
    } else if($columns == 2) {
        $cc['md'] = 2; 
        $cc['sm'] = 2; 
        $cc['xs'] = 1;
        $cc['ss'] = 1;
    } else {
        $cc['md'] = 1; 
        $cc['sm'] = 1; 
        $cc['xs'] = 1;
        $cc['ss'] = 1;
    }
    $cc['xxl'] = $cc['md'];
	$cc['xl'] = $cc['md'];

    return apply_filters( 'kadence_carousel_columns', $cc, $columns, $sidebar );
}
///Page Navigation
function kad_wp_pagenavi() {
	error_log( "The kad_wp_pagenavi function is deprecated since version 4.6.2. Please use virtue_wp_pagenav() instead." );
	virtue_wp_pagenav();
}
function virtue_wp_pagenav() {

  global $wp_query, $wp_rewrite;
  $pages = '';
  $big = 999999999; // need an unlikely integer
  $max = $wp_query->max_num_pages;
  if (!$current = get_query_var('paged')) $current = 1;
  $args['base'] = str_replace($big, '%#%', esc_url( get_pagenum_link( $big ) ) );
  $args['total'] = $max;
  $args['current'] = $current;
  $args['add_args'] = false;

  $total = 1;
  $args['mid_size'] = 3;
  $args['end_size'] = 1;
  $args['prev_text'] = '«';
  $args['next_text'] = '»';
 
  	if ($max > 1){
  		echo '<div class="scroller-status"><div class="loader-ellips infinite-scroll-request"><span class="loader-ellips__dot"></span><span class="loader-ellips__dot"></span><span class="loader-ellips__dot"></span><span class="loader-ellips__dot"></span></div></div>';
   		echo '<div class="wp-pagenavi">';
   	}
 	if ($total == 1 && $max > 1)
 		echo paginate_links($args);
 	if ($max > 1) echo '</div>';
 	
}

/**
 * Allowed HTML for widget select box.
 */
function virtue_admin_allowed_html() {

	$allowed = wp_kses_allowed_html( 'post' );
	// form fields - input.
	$allowed['input'] = array(
		'class' => array(),
		'id'    => array(),
		'name'  => array(),
		'value' => array(),
		'type'  => array(),
	);
	// select.
	$allowed['select'] = array(
		'class' => array(),
		'id'    => array(),
		'name'  => array(),
		'value' => array(),
		'type'  => array(),
	);
	// select options.
	$allowed['option'] = array(
		'selected' => array(),
		'value'    => array(),
	);

	return $allowed;
}

/**
 * Schema type
 */
function kadence_html_tag_schema() {
    $schema = 'http://schema.org/';

    if( is_singular( 'post' ) ) {
        $type = "WebPage";
    } else if( is_page_template('page-contact.php') ) {
        $type = 'ContactPage';
    } elseif( is_author() ) {
        $type = 'ProfilePage';
    } elseif( is_search() ) {
        $type = 'SearchResultsPage';
    } else {
        $type = 'WebPage';
    }

    echo apply_filters('kadence_html_schema', 'itemscope="itemscope" itemtype="' .  esc_attr( $schema ) . esc_attr( $type ) . '"' );
}

// Ecerpt Length

function virtue_excerpt($limit) {
	global $virtue_premium;
	if(!empty($virtue_premium['post_readmore_text'])) {
		$readmore = $virtue_premium['post_readmore_text'];
	} else {
		$readmore =  __( 'Read More', 'virtue' );
	}
	$readmore = '>'.$readmore.'<';
	$excerpt = explode(' ', get_the_excerpt(), $limit);
	if (count($excerpt)>=$limit) {
		array_pop($excerpt);
		$excerpt = implode(" ",$excerpt).'...';
	} else {
		$excerpt = implode(" ",$excerpt);
	} 
	$excerpt = preg_replace('`\[[^\]]*\]`','',$excerpt);
	$excerpt = str_replace($readmore,'><',$excerpt);
	
	return $excerpt;
}

function virtue_content($limit) {
      $content = explode(' ', get_the_content(), $limit);
      if (count($content)>=$limit) {
        array_pop($content);
        $content = implode(" ",$content).'...';
      } else {
        $content = implode(" ",$content);
      } 
      $content = preg_replace('/\[.+\]/','', $content);
      $content = apply_filters('the_content', $content); 
      $content = str_replace(']]>', ']]&gt;', $content);
      return $content;
    }
// Adjacent Post Plus Plugin

function get_adjacent_post_plus($r, $previous = true ) {
  global $post, $wpdb;

  extract( $r, EXTR_SKIP );

  if ( empty( $post ) )
    return null;

//  Sanitize $order_by, since we are going to use it in the SQL query. Default to 'post_date'.
  if ( in_array($order_by, array('post_date', 'post_title', 'post_excerpt', 'post_name', 'post_modified')) ) {
    $order_format = '%s';
  } elseif ( in_array($order_by, array('ID', 'post_author', 'post_parent', 'menu_order', 'comment_count')) ) {
    $order_format = '%d';
  } elseif ( $order_by == 'custom' && !empty($meta_key) ) { // Don't allow a custom sort if meta_key is empty.
    $order_format = '%s';
  } elseif ( $order_by == 'numeric' && !empty($meta_key) ) {
    $order_format = '%d';
  } else {
    $order_by = 'post_date';
    $order_format = '%s';
  }
  
//  Sanitize $order_2nd. Only columns containing unique values are allowed here. Default to 'post_date'.
  if ( in_array($order_2nd, array('post_date', 'post_title', 'post_modified')) ) {
    $order_format2 = '%s';
  } elseif ( in_array($order_2nd, array('ID')) ) {
    $order_format2 = '%d';
  } else {
    $order_2nd = 'post_date';
    $order_format2 = '%s';
  }
  
//  Sanitize num_results (non-integer or negative values trigger SQL errors)
  $num_results = intval($num_results) < 2 ? 1 : intval($num_results);

//  Queries involving custom fields require an extra table join
  if ( $order_by == 'custom' || $order_by == 'numeric' ) {
    $current_post = get_post_meta($post->ID, $meta_key, TRUE);
    $order_by = ($order_by === 'numeric') ? 'm.meta_value+0' : 'm.meta_value';
    $meta_join = $wpdb->prepare(" INNER JOIN $wpdb->postmeta AS m ON p.ID = m.post_id AND m.meta_key = %s", $meta_key );
  } elseif ( $in_same_meta ) {
    $current_post = $post->$order_by;
    $order_by = 'p.' . $order_by;
    $meta_join = $wpdb->prepare(" INNER JOIN $wpdb->postmeta AS m ON p.ID = m.post_id AND m.meta_key = %s", $in_same_meta );
  } else {
    $current_post = $post->$order_by;
    $order_by = 'p.' . $order_by;
    $meta_join = '';
  }

//  Get the current post value for the second sort column
  $current_post2 = $post->$order_2nd;
  $order_2nd = 'p.' . $order_2nd;
  
//  Get the list of post types. Default to current post type
  if ( empty($post_type) )
    $post_type = "'$post->post_type'";

//  Put this section in a do-while loop to enable the loop-to-first-post option
  do {
    $join = $meta_join;
    $excluded_categories = $ex_cats;
    $included_categories = $in_cats;
    $excluded_posts = $ex_posts;
    $included_posts = $in_posts;
    $in_same_term_sql = $in_same_author_sql = $in_same_meta_sql = $ex_cats_sql = $in_cats_sql = $ex_posts_sql = $in_posts_sql = '';

//    Get the list of hierarchical taxonomies, including customs (don't assume taxonomy = 'category')
    $taxonomies = array_filter( get_post_taxonomies($post->ID), "is_taxonomy_hierarchical" );

    if ( ($in_same_cat || $in_same_tax || $in_same_format || !empty($excluded_categories) || !empty($included_categories)) && !empty($taxonomies) ) {
      	$cat_array = $tax_array = $format_array = array();

      if ( $in_same_cat ) {
        $cat_array = wp_get_object_terms($post->ID, $taxonomies, array('fields' => 'ids'));
      }
    if ( $in_same_tax && !$in_same_cat ) {
    	if ( $in_same_tax === true ) {
    		
          	if ( $taxonomies != array('category') )
            	$taxonomies = array_diff($taxonomies, array('category'));
	        } else {
	          	$taxonomies = (array) $in_same_tax;
	        }
	        $newtax = array();
	        foreach ($taxonomies as $key => $value) {
	        	$newtax[] = $value;
	        }
          	$taxonomies = $newtax;
        	$tax_array = wp_get_object_terms($post->ID, $taxonomies, array('fields' => 'ids'));
	    }
		if ( $in_same_format ) {
			$taxonomies[] = 'post_format';
			$format_array = wp_get_object_terms($post->ID, 'post_format', array('fields' => 'ids'));
		}

      		$join .= " INNER JOIN $wpdb->term_relationships AS tr ON p.ID = tr.object_id INNER JOIN $wpdb->term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id AND tt.taxonomy IN (\"" . implode('", "', $taxonomies) . "\")";

      	$term_array = array_unique( array_merge( $cat_array, $tax_array, $format_array ) );
      if ( !empty($term_array) )
        $in_same_term_sql = "AND tt.term_id IN (" . implode(',', $term_array) . ")";

      if ( !empty($excluded_categories) ) {
//        Support for both (1 and 5 and 15) and (1, 5, 15) delimiter styles
        $delimiter = ( strpos($excluded_categories, ',') !== false ) ? ',' : 'and';
        $excluded_categories = array_map( 'intval', explode($delimiter, $excluded_categories) );
//        Three category exclusion methods are supported: 'strong', 'diff', and 'weak'.
//        Default is 'weak'. See the plugin documentation for more information.
        if ( $ex_cats_method === 'strong' ) {
          $taxonomies = array_filter( get_post_taxonomies($post->ID), "is_taxonomy_hierarchical" );
          if ( function_exists('get_post_format') )
            $taxonomies[] = 'post_format';
          $ex_cats_posts = get_objects_in_term( $excluded_categories, $taxonomies );
          if ( !empty($ex_cats_posts) )
            $ex_cats_sql = "AND p.ID NOT IN (" . implode($ex_cats_posts, ',') . ")";
        } else {
          if ( !empty($term_array) && !in_array($ex_cats_method, array('diff', 'differential')) )
            $excluded_categories = array_diff($excluded_categories, $term_array);
          if ( !empty($excluded_categories) )
            $ex_cats_sql = "AND tt.term_id NOT IN (" . implode($excluded_categories, ',') . ')';
        }
      }

      if ( !empty($included_categories) ) {
        $in_same_term_sql = ''; // in_cats overrides in_same_cat
        $delimiter = ( strpos($included_categories, ',') !== false ) ? ',' : 'and';
        $included_categories = array_map( 'intval', explode($delimiter, $included_categories) );
        $in_cats_sql = "AND tt.term_id IN (" . implode(',', $included_categories) . ")";
      }
    }

//    Optionally restrict next/previous links to same author    
    if ( $in_same_author )
      $in_same_author_sql = $wpdb->prepare("AND p.post_author = %d", $post->post_author );

//    Optionally restrict next/previous links to same meta value
    if ( $in_same_meta && $r['order_by'] != 'custom' && $r['order_by'] != 'numeric' )
      $in_same_meta_sql = $wpdb->prepare("AND m.meta_value = %s", get_post_meta($post->ID, $in_same_meta, TRUE) );

//    Optionally exclude individual post IDs
    if ( !empty($excluded_posts) ) {
      $excluded_posts = array_map( 'intval', explode(',', $excluded_posts) );
      $ex_posts_sql = " AND p.ID NOT IN (" . implode(',', $excluded_posts) . ")";
    }
    
//    Optionally include individual post IDs
    if ( !empty($included_posts) ) {
      $included_posts = array_map( 'intval', explode(',', $included_posts) );
      $in_posts_sql = " AND p.ID IN (" . implode(',', $included_posts) . ")";
    }

    $adjacent = $previous ? 'previous' : 'next';
    $order = $previous ? 'DESC' : 'ASC';
    $op = $previous ? '<' : '>';

//    Optionally get the first/last post. Disable looping and return only one result.
    if ( $end_post ) {
      $order = $previous ? 'ASC' : 'DESC';
      $num_results = 1;
      $loop = false;
      if ( $end_post === 'fixed' ) // display the end post link even when it is the current post
        $op = $previous ? '<=' : '>=';
    }

//    If there is no next/previous post, loop back around to the first/last post.   
    if ( $loop && isset($result) ) {
      $op = $previous ? '>=' : '<=';
      $loop = false; // prevent an infinite loop if no first/last post is found
    }
    
    $join  = apply_filters( "get_{$adjacent}_post_plus_join", $join, $r );

//    In case the value in the $order_by column is not unique, select posts based on the $order_2nd column as well.
//    This prevents posts from being skipped when they have, for example, the same menu_order.
    $where = apply_filters( "get_{$adjacent}_post_plus_where", $wpdb->prepare("WHERE ( $order_by $op $order_format OR $order_2nd $op $order_format2 AND $order_by = $order_format ) AND p.post_type IN ($post_type) AND p.post_status = 'publish' $in_same_term_sql $in_same_author_sql $in_same_meta_sql $ex_cats_sql $in_cats_sql $ex_posts_sql $in_posts_sql", $current_post, $current_post2, $current_post), $r );

    $sort  = apply_filters( "get_{$adjacent}_post_plus_sort", "ORDER BY $order_by $order, $order_2nd $order LIMIT $num_results", $r );

    $query = "SELECT DISTINCT p.* FROM $wpdb->posts AS p $join $where $sort";
    $query_key = 'adjacent_post_' . md5($query);
    $result = wp_cache_get($query_key);
    if ( false !== $result )
      return $result;

//    echo $query . '<br />';

//    Use get_results instead of get_row, in order to retrieve multiple adjacent posts (when $num_results > 1)
//    Add DISTINCT keyword to prevent posts in multiple categories from appearing more than once
    $result = $wpdb->get_results("SELECT DISTINCT p.* FROM $wpdb->posts AS p $join $where $sort");
    if ( null === $result )
      $result = '';

  } while ( !$result && $loop );

  wp_cache_set($query_key, $result);
  return $result;
}

/**
 * Display previous post link that is adjacent to the current post.
 *
 * Based on previous_post_link() from wp-includes/link-template.php
 *
 * @param array|string $args Optional. Override default arguments.
 * @return bool True if previous post link is found, otherwise false.
 */
function previous_post_link_plus($args = '') {
  return adjacent_post_link_plus($args, '&laquo; %link', true);
}

/**
 * Display next post link that is adjacent to the current post.
 *
 * Based on next_post_link() from wp-includes/link-template.php
 *
 * @param array|string $args Optional. Override default arguments.
 * @return bool True if next post link is found, otherwise false.
 */
function next_post_link_plus($args = '') {
  return adjacent_post_link_plus($args, '%link &raquo;', false);
}

/**
 * Display adjacent post link.
 *
 * Can be either next post link or previous.
 *
 * Based on adjacent_post_link() from wp-includes/link-template.php
 *
 * @param array|string $args Optional. Override default arguments.
 * @param bool $previous Optional, default is true. Whether display link to previous post.
 * @return bool True if next/previous post is found, otherwise false.
 */
function adjacent_post_link_plus($args = '', $format = '%link &raquo;', $previous = true) {
  $defaults = array(
    'order_by' => 'post_date', 'order_2nd' => 'post_date', 'meta_key' => '', 'post_type' => '',
    'loop' => false, 'end_post' => false, 'thumb' => false, 'max_length' => 0,
    'format' => '', 'link' => '%title', 'date_format' => '', 'tooltip' => '%title',
    'in_same_cat' => false, 'in_same_tax' => false, 'in_same_format' => false,
    'in_same_author' => false, 'in_same_meta' => false,
    'ex_cats' => '', 'ex_cats_method' => 'weak', 'in_cats' => '', 'ex_posts' => '', 'in_posts' => '',
    'before' => '', 'after' => '', 'num_results' => 1, 'return' => false, 'echo' => true
  );

//  If Post Types Order plugin is installed, default to sorting on menu_order
  	if ( function_exists('CPTOrderPosts') ) {
    	$defaults['order_by'] = 'menu_order';
  	}
  	$r = wp_parse_args( $args, $defaults );
  	if ( empty($r['format']) ) {
    	$r['format'] = $format;
  	}
  	if ( empty($r['date_format']) ) {
    	$r['date_format'] = get_option('date_format');
    }
  	if ( !function_exists('get_post_format') ) {
    	$r['in_same_format'] = false;
    }

  	if ( $previous && is_attachment() ) {
    	$posts = array();
    	$posts[] = & get_post($GLOBALS['post']->post_parent);
  	} else {
    	$posts = get_adjacent_post_plus($r, $previous);
	}

//  If there is no next/previous post, return false so themes may conditionally display inactive link text.
  	if ( !$posts ) {
    	return false;
	}
//  If sorting by date, display posts in reverse chronological order. Otherwise display in alpha/numeric order.
  	if ( ($previous && $r['order_by'] != 'post_date') || (!$previous && $r['order_by'] == 'post_date') ) {
    	$posts = array_reverse( $posts, true );
	}
    
//  Option to return something other than the formatted link    
  if ( $r['return'] ) {
    if ( $r['num_results'] == 1 ) {
      reset($posts);
      $post = current($posts);
      if ( $r['return'] === 'id')
        return $post->ID;
      if ( $r['return'] === 'href')
        return get_permalink($post);
      if ( $r['return'] === 'object')
        return $post;
      if ( $r['return'] === 'title')
        return $post->post_title;
      if ( $r['return'] === 'date')
        return mysql2date($r['date_format'], $post->post_date);
    } elseif ( $r['return'] === 'object')
      return $posts;
  }

  $output = $r['before'];

//  When num_results > 1, multiple adjacent posts may be returned. Use foreach to display each adjacent post.
  foreach ( $posts as $post ) {
    $title = $post->post_title;
    if ( empty($post->post_title) )
      $title = $previous ? __('Previous Post', 'virtue') : __('Next Post', 'virtue');

    //$title = apply_filters('the_title', $title, $post->ID);
    $date = mysql2date($r['date_format'], $post->post_date);
    $author = get_the_author_meta('display_name', $post->post_author);
  
//    Set anchor title attribute to long post title or custom tooltip text. Supports variable replacement in custom tooltip.
    if ( $r['tooltip'] ) {
      $tooltip = str_replace('%title', $title, $r['tooltip']);
      $tooltip = str_replace('%date', $date, $tooltip);
      $tooltip = str_replace('%author', $author, $tooltip);
      $tooltip = ' title="' . esc_attr($tooltip) . '"';
    } else
      $tooltip = '';

//    Truncate the link title to nearest whole word under the length specified.
    $max_length = intval($r['max_length']) < 1 ? 9999 : intval($r['max_length']);
    if ( strlen($title) > $max_length )
      $title = substr( $title, 0, strrpos(substr($title, 0, $max_length), ' ') ) . '...';
  
    $rel = $previous ? 'prev' : 'next';

    $anchor = '<a href="'.get_permalink($post).'" rel="'.$rel.'"'.$tooltip.'>';
    $link = str_replace('%title', $title, $r['link']);
    $link = str_replace('%date', $date, $link);
    $link = $anchor . $link . '</a>';
  
    $format = str_replace('%link', $link, $r['format']);
    $format = str_replace('%title', $title, $format);
    $format = str_replace('%date', $date, $format);
    $format = str_replace('%author', $author, $format);
    if ( ($r['order_by'] == 'custom' || $r['order_by'] == 'numeric') && !empty($r['meta_key']) ) {
      $meta = get_post_meta($post->ID, $r['meta_key'], true);
      $format = str_replace('%meta', $meta, $format);
    } elseif ( $r['in_same_meta'] ) {
      $meta = get_post_meta($post->ID, $r['in_same_meta'], true);
      $format = str_replace('%meta', $meta, $format);
    }

//    Get the category list, including custom taxonomies (only if the %category variable has been used).
    if ( (strpos($format, '%category') !== false) && version_compare(PHP_VERSION, '5.0.0', '>=') ) {
      $term_list = '';
      $taxonomies = array_filter( get_post_taxonomies($post->ID), "is_taxonomy_hierarchical" );
      if ( $r['in_same_format'] && get_post_format($post->ID) )
        $taxonomies[] = 'post_format';
      foreach ( $taxonomies as &$taxonomy ) {
//        No, this is not a mistake. Yes, we are testing the result of the assignment ( = ).
//        We are doing it this way to stop it from appending a comma when there is no next term.
        if ( $next_term = get_the_term_list($post->ID, $taxonomy, '', ', ', '') ) {
          $term_list .= $next_term;
          if ( current($taxonomies) ) $term_list .= ', ';
        }
      }
      $format = str_replace('%category', $term_list, $format);
    }

//    Optionally add the post thumbnail to the link. Wrap the link in a span to aid CSS styling.
    if ( $r['thumb'] && has_post_thumbnail($post->ID) ) {
      if ( $r['thumb'] === true ) // use 'post-thumbnail' as the default size
        $r['thumb'] = 'post-thumbnail';
      $thumbnail = '<a class="post-thumbnail" href="'.get_permalink($post).'" rel="'.$rel.'"'.$tooltip.'>' . get_the_post_thumbnail( $post->ID, $r['thumb'] ) . '</a>';
      $format = $thumbnail . '<span class="post-link">' . $format . '</span>';
    }

//    If more than one link is returned, wrap them in <li> tags   
    if ( intval($r['num_results']) > 1 )
      $format = '<li>' . $format . '</li>';
    
    $output .= $format;
  }

  $output .= $r['after'];

  //  If echo is false, don't display anything. Return the link as a PHP string.
  if ( !$r['echo'] || $r['return'] === 'output' )
    return $output;

  $adjacent = $previous ? 'previous' : 'next';
  echo apply_filters( "{$adjacent}_post_link_plus", $output, $r );

  return true;
}

//User Addon
add_action( 'show_user_profile', 'kad_show_extra_profile_fields' );
add_action( 'edit_user_profile', 'kad_show_extra_profile_fields' );

function kad_show_extra_profile_fields( $user ) { 
	if ( !current_user_can( 'edit_posts', $user->ID ) ) {
		return;
	}?>

<h3>Extra profile information</h3>

<table class="form-table">
  <tr>
    <th><label for="twitter"><?php _e('Occupation', 'virtue');?></label></th>
    <td>
      <input type="text" name="occupation" id="occupation" value="<?php echo esc_attr( get_the_author_meta( 'occupation', $user->ID ) ); ?>" class="regular-text" /><br />
      <span class="description"><?php _e('Please enter your Occupation.', 'virtue');?></span>
    </td>
  </tr>
  <tr>
    <th><label for="twitter">Twitter</label></th>
    <td>
      <input type="text" name="twitter" id="twitter" value="<?php echo esc_attr( get_the_author_meta( 'twitter', $user->ID ) ); ?>" class="regular-text" /><br />
      <span class="description"><?php _e('Please enter your Twitter username.', 'virtue'); ?></span>
    </td>
  </tr>
    <tr>
    <th><label for="facebook">Facebook</label></th>
    <td>
      <input type="text" name="facebook" id="facebook" value="<?php echo esc_attr( get_the_author_meta( 'facebook', $user->ID ) ); ?>" class="regular-text" /><br />
      <span class="description"><?php _e('Please enter your Facebook url. (be sure to include http://)', 'virtue'); ?></span>
    </td>
  </tr>
    <tr>
    <th><label for="google">Google Plus</label></th>
    <td>
      <input type="text" name="google" id="google" value="<?php echo esc_attr( get_the_author_meta( 'google', $user->ID ) ); ?>" class="regular-text" /><br />
      <span class="description"><?php _e('Please enter your Google Plus url. (be sure to include http://)', 'virtue'); ?></span>
    </td>
  </tr>
   <tr>
    <th><label for="youtube">YouTube</label></th>
    <td>
      <input type="text" name="youtube" id="youtube" value="<?php echo esc_attr( get_the_author_meta( 'youtube', $user->ID ) ); ?>" class="regular-text" /><br />
      <span class="description"><?php _e('Please enter your YouTube url. (be sure to include http://)', 'virtue'); ?></span>
    </td>
  </tr>
    <tr>
    <th><label for="flickr">Flickr</label></th>
    <td>
      <input type="text" name="flickr" id="flickr" value="<?php echo esc_attr( get_the_author_meta( 'flickr', $user->ID ) ); ?>" class="regular-text" /><br />
      <span class="description"><?php _e('Please enter your Flickr url. (be sure to include http://)', 'virtue'); ?></span>
    </td>
  </tr>
    <tr>
    <th><label for="vimeo">Vimeo</label></th>
    <td>
      <input type="text" name="vimeo" id="vimeo" value="<?php echo esc_attr( get_the_author_meta( 'vimeo', $user->ID ) ); ?>" class="regular-text" /><br />
      <span class="description"><?php _e('Please enter your Vimeo url. (be sure to include http://)', 'virtue'); ?></span>
    </td>
  </tr>
    <tr>
    <th><label for="linkedin">Linkedin</label></th>
    <td>
      <input type="text" name="linkedin" id="linkedin" value="<?php echo esc_attr( get_the_author_meta( 'linkedin', $user->ID ) ); ?>" class="regular-text" /><br />
      <span class="description"><?php _e('Please enter your Linkedin url. (be sure to include http://)', 'virtue'); ?></span>
    </td>
  </tr>
    <tr>
    <th><label for="dribbble">Dribbble</label></th>
    <td>
      <input type="text" name="dribbble" id="dribbble" value="<?php echo esc_attr( get_the_author_meta( 'dribbble', $user->ID ) ); ?>" class="regular-text" /><br />
      <span class="description"><?php _e('Please enter your Dribbble url. (be sure to include http://)', 'virtue'); ?></span>
    </td>
  </tr>
    <tr>
    <th><label for="pinterest">Pinterest</label></th>
    <td>
      <input type="text" name="pinterest" id="pinterest" value="<?php echo esc_attr( get_the_author_meta( 'pinterest', $user->ID ) ); ?>" class="regular-text" /><br />
      <span class="description"><?php _e('Please enter your Pinterest url. (be sure to include http://)', 'virtue'); ?></span>
    </td>
  </tr>
  <tr>
    <th><label for="instagram">Instagram</label></th>
    <td>
      <input type="text" name="instagram" id="instagram" value="<?php echo esc_attr( get_the_author_meta( 'instagram', $user->ID ) ); ?>" class="regular-text" /><br />
      <span class="description"><?php _e('Please enter your Instagram url. (be sure to include http://)', 'virtue'); ?></span>
    </td>
  </tr>
  <tr>
    <th><label for="xing">XING</label></th>
    <td>
      <input type="text" name="xing" id="xing" value="<?php echo esc_attr( get_the_author_meta( 'xing', $user->ID ) ); ?>" class="regular-text" /><br />
      <span class="description"><?php _e('Please enter your XING url. (be sure to include http://)', 'virtue'); ?></span>
    </td>
  </tr>
</table>
<?php }
add_action( 'personal_options_update', 'kad_save_extra_profile_fields' );
add_action( 'edit_user_profile_update', 'kad_save_extra_profile_fields' );

function kad_save_extra_profile_fields( $user_id ) {
    if ( !current_user_can( 'edit_posts', $user_id ) )
        return false;
    if(isset($_POST['occupation'])){
  		update_user_meta( $user_id, 'occupation', wp_filter_post_kses($_POST['occupation']) );
  	}
  	if(isset($_POST['twitter'])) {
    	update_user_meta( $user_id, 'twitter', sanitize_title(wp_unslash($_POST['twitter']) ));
    }
    if(isset($_POST['facebook'])) {
  		update_user_meta( $user_id, 'facebook', esc_url_raw($_POST['facebook']) );
  	}
  	if(isset($_POST['google'])) {
  		update_user_meta( $user_id, 'google', esc_url_raw($_POST['google']) );
  	}
  	if(isset($_POST['youtube'])) {
  		update_user_meta( $user_id, 'youtube', esc_url_raw($_POST['youtube']) );
  	}
  	if(isset($_POST['flickr'])) {
  		update_user_meta( $user_id, 'flickr', esc_url_raw($_POST['flickr']) );
  	}
  	if(isset($_POST['vimeo'])) {
  		update_user_meta( $user_id, 'vimeo', esc_url_raw($_POST['vimeo']) );
  	}
  	if(isset($_POST['linkedin'])) {
  		update_user_meta( $user_id, 'linkedin', esc_url_raw($_POST['linkedin']) );
  	}
  	if(isset($_POST['dribbble'])) {
  		update_user_meta( $user_id, 'dribbble', esc_url_raw($_POST['dribbble']) );
  	}
  	if(isset($_POST['pinterest'])) {
  		update_user_meta( $user_id, 'pinterest', esc_url_raw($_POST['pinterest']) );
  	}
  	if(isset($_POST['instagram'])) {
  		update_user_meta( $user_id, 'instagram', esc_url_raw($_POST['instagram']) );
  	}
  	if(isset($_POST['xing'])) {
  		update_user_meta( $user_id, 'xing', esc_url_raw($_POST['xing']) );
  	}
}

