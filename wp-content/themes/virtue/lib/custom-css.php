<?php
/**
 * Get all ascend custom css output.
 *
 * @package Virtue Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
/**
 * Get all virtue custom css output.
 */
function virtue_custom_css() {
	global $virtue;
	// Logo.
	if ( isset( $virtue['logo_padding_top'] ) ) {
		$logo_padding_top = '#logo {padding-top:' . esc_attr( $virtue['logo_padding_top'] ) . 'px;}';
	} else {
		$logo_padding_top = '#logo {padding-top:25px;}';
	}
	if ( isset( $virtue['logo_padding_bottom'] ) ) {
		$logo_padding_bottom = '#logo {padding-bottom:' . esc_attr( $virtue['logo_padding_bottom'] ) . 'px;}';
	} else {
		$logo_padding_bottom = '#logo {padding-bottom:10px;}';
	}
	if ( isset( $virtue['logo_padding_left'] ) ) {
		$logo_padding_left = '#logo {margin-left:' . esc_attr( $virtue['logo_padding_left'] ) . 'px;}';
	} else {
		$logo_padding_left = '#logo {margin-left:0px;}';
	}
	if ( isset( $virtue['logo_padding_right'] ) ) {
		$logo_padding_right = '#logo {margin-right:' . esc_attr( $virtue['logo_padding_right'] ) . 'px;}';
	} else {
		$logo_padding_right = '#logo {margin-right:0px;}';
	}
	if ( isset( $virtue['menu_margin_top'] ) ) {
		$menu_margin_top = '#nav-main {margin-top:' . esc_attr( $virtue['menu_margin_top'] ) . 'px;}';
	} else {
		$menu_margin_top = '#nav-main {margin-top:40px;}';
	}
	if ( isset( $virtue['menu_margin_bottom'] ) ) {
		$menu_margin_bottom = '#nav-main {margin-bottom:' . esc_attr( $virtue['menu_margin_bottom'] ) . 'px;}';
	} else {
		$menu_margin_bottom = '#nav-main {margin-bottom:10px;}';
	}
	// Typography.
	if ( ! empty( $virtue['font_h1'] ) ) {
		$font_family = '.headerfont, .tp-caption {font-family:' . esc_attr( $virtue['font_h1']['font-family'] ) . ';} .topbarmenu ul li {font-family:' . esc_attr( $virtue['font_primary_menu']['font-family'] ) . ';}';
	} else {
		$font_family = '';
	}

//Basic Styling

if(!empty($virtue['primary_color'])) {
	$primaryrgb = virtue_hex2rgb($virtue['primary_color']); 
	$color_primary = '.home-message:hover {background-color:'.$virtue['primary_color'].'; background-color: rgba('.$primaryrgb[0].', '.$primaryrgb[1].', '.$primaryrgb[2].', 0.6);}
  nav.woocommerce-pagination ul li a:hover, .wp-pagenavi a:hover, .panel-heading .accordion-toggle, .variations .kad_radio_variations label:hover, .variations .kad_radio_variations label.selectedValue {border-color: '.$virtue['primary_color'].';}
  a, #nav-main ul.sf-menu ul li a:hover, .product_price ins .amount, .price ins .amount, .color_primary, .primary-color, #logo a.brand, #nav-main ul.sf-menu a:hover,
  .woocommerce-message:before, .woocommerce-info:before, #nav-second ul.sf-menu a:hover, .footerclass a:hover, .posttags a:hover, .subhead a:hover, .nav-trigger-case:hover .kad-menu-name, 
  .nav-trigger-case:hover .kad-navbtn, #kadbreadcrumbs a:hover, #wp-calendar a, .star-rating, .has-virtue-primary-color {color: '.$virtue['primary_color'].';}
.widget_price_filter .ui-slider .ui-slider-handle, .product_item .kad_add_to_cart:hover, .product_item:hover a.button:hover, .product_item:hover .kad_add_to_cart:hover, .kad-btn-primary, html .woocommerce-page .widget_layered_nav ul.yith-wcan-label li a:hover, html .woocommerce-page .widget_layered_nav ul.yith-wcan-label li.chosen a,
.product-category.grid_item a:hover h5, .woocommerce-message .button, .widget_layered_nav_filters ul li a, .widget_layered_nav ul li.chosen a, .wpcf7 input.wpcf7-submit, .yith-wcan .yith-wcan-reset-navigation,
#containerfooter .menu li a:hover, .bg_primary, .portfolionav a:hover, .home-iconmenu a:hover, p.demo_store, .topclass, #commentform .form-submit #submit, .kad-hover-bg-primary:hover, .widget_shopping_cart_content .checkout,
.login .form-row .button, .variations .kad_radio_variations label.selectedValue, #payment #place_order, .wpcf7 input.wpcf7-back, .shop_table .actions input[type=submit].checkout-button, .cart_totals .checkout-button, input[type="submit"].button, .order-actions .button, .has-virtue-primary-background-color {background: '.$virtue['primary_color'].';}';
} else {
	$color_primary = '';
}
	if ( ! empty( $virtue['primary20_color'] ) ) {
		$color_primary30 = 'a:hover, .has-virtue-primary-light-color {color: ' . esc_attr( $virtue['primary20_color'] ) . ';} .kad-btn-primary:hover, .login .form-row .button:hover, #payment #place_order:hover, .yith-wcan .yith-wcan-reset-navigation:hover, .widget_shopping_cart_content .checkout:hover,
	.woocommerce-message .button:hover, #commentform .form-submit #submit:hover, .wpcf7 input.wpcf7-submit:hover, .widget_layered_nav_filters ul li a:hover, .cart_totals .checkout-button:hover,
	.widget_layered_nav ul li.chosen a:hover, .shop_table .actions input[type=submit].checkout-button:hover, .wpcf7 input.wpcf7-back:hover, .order-actions .button:hover, input[type="submit"].button:hover, .product_item:hover .kad_add_to_cart, .product_item:hover a.button, .has-virtue-primary-light-background-color {background: ' . esc_attr( $virtue['primary20_color'] ) . ';}';
	} else {
		$color_primary30 = '';
	}
if(!empty($virtue['gray_font_color'])) {
  $color_grayfont = '.color_gray, .subhead, .subhead a, .posttags, .posttags a, .product_meta a {color:'.$virtue['gray_font_color'].';}';
} else {
  $color_grayfont = '';
}
if(!empty($virtue['footerfont_color'])) {
  $color_footerfont = '#containerfooter h3, #containerfooter, .footercredits p, .footerclass a, .footernav ul li a {color:'.$virtue['footerfont_color'].';}';
} else {
  $color_footerfont = '';
}

//Advanced Styling


if(!empty($virtue['content_bg_color'])) {
$content_bg_color = $virtue['content_bg_color'];
} else {
  $content_bg_color = '';
}
if(!empty($virtue['bg_content_bg_img']['url'])) { 
  $content_bg_img = 'url('.$virtue['bg_content_bg_img']['url'].')'; 
} else {
  $content_bg_img = '';
}
if(!empty($virtue['content_bg_repeat'])) {
$content_bg_repeat = $virtue['content_bg_repeat'];
} else {
  $content_bg_repeat = '';
}
if(!empty($virtue['content_bg_placementx'])) {
  $content_bg_x = $virtue['content_bg_placementx'];
} else {
  $content_bg_x = '';
}
if (!empty($virtue['content_bg_placementy'])) {
$content_bg_y = $virtue['content_bg_placementy']; 
} else {
  $content_bg_y = '';
}
if(!empty($virtue['content_bg_color']) || !empty($virtue['bg_content_bg_img']['url'])) {
    $contentclass = '.contentclass, .nav-tabs>.active>a, .nav-tabs>.active>a:hover, .nav-tabs>.active>a:focus {background:'.$content_bg_color.' '.$content_bg_img.' '.$content_bg_repeat.' '.$content_bg_x.' '.$content_bg_y.';}';
  } else {
    $contentclass = '';
  }
if(!empty($virtue['header_bg_color'])) {
  $header_bg_color = $virtue['header_bg_color'];
  } else {
    $header_bg_color = '';
  }
  if(!empty($virtue['bg_header_bg_img']['url'])) { 
    $header_bg_img = 'url('.$virtue['bg_header_bg_img']['url'].')'; 
  } else {
    $header_bg_img = '';
  }
  if(!empty($virtue['header_bg_repeat'])) {
  $header_bg_repeat = $virtue['header_bg_repeat'];
  } else {
    $header_bg_repeat = '';
  }
  if(!empty($virtue['header_bg_placementx'])) {
  $header_bg_x = $virtue['header_bg_placementx'];
  } else {
    $header_bg_x = '';
  }
  if(!empty($virtue['header_bg_placementy'])) {
  $header_bg_y = $virtue['header_bg_placementy'];
  } else {
    $header_bg_y = '';
  }
  if(!empty($virtue['header_bg_color']) || !empty($virtue['bg_header_bg_img']['url'])) {
    $headerclass = '.headerclass {background:'.$header_bg_color.' '.$header_bg_img.' '.$header_bg_repeat.' '.$header_bg_x.' '.$header_bg_y.';}';
  } else {
    $headerclass = '';
  }
if(!empty($virtue['topbar_bg_color'])) {
$topbar_bg_color = $virtue['topbar_bg_color'];
} else {
  $topbar_bg_color = '';
}
if(!empty($virtue['bg_topbar_bg_img']['url'])) { 
  $topbar_bg_img = 'url('.$virtue['bg_topbar_bg_img']['url'].')'; 
} else {
  $topbar_bg_img = '';
}
if(!empty($virtue['topbar_bg_repeat'])) {
$topbar_bg_repeat = $virtue['topbar_bg_repeat'];
} else {
  $topbar_bg_repeat = '';
}
if(!empty($virtue['topbar_bg_placementx'])) {
$topbar_bg_x = $virtue['topbar_bg_placementx'];
} else {
  $topbar_bg_x = '';
}
if(!empty($virtue['topbar_bg_placementy'])) {
$topbar_bg_y = $virtue['topbar_bg_placementy'];
} else {
  $topbar_bg_y = '';
}
if(!empty($virtue['topbar_bg_color']) || !empty($virtue['bg_topbar_bg_img']['url'])) {
    $topbarclass = '.topclass {background:'.$topbar_bg_color.' '.$topbar_bg_img.' '.$topbar_bg_repeat.' '.$topbar_bg_x.' '.$topbar_bg_y.';}';
  } else {
    $topbarclass = '';
  }
if(!empty($virtue['menu_bg_color'])) {
$menu_bg_color = $virtue['menu_bg_color'];
} else {
  $menu_bg_color = '';
}
if(!empty($virtue['bg_menu_bg_img']['url'])) {
 $menu_bg_img = 'url('.$virtue['bg_menu_bg_img']['url'].')'; 
} else {
  $menu_bg_img = '';
}
if(!empty($virtue['menu_bg_repeat'])) {
$menu_bg_repeat = $virtue['menu_bg_repeat'];
} else {
  $menu_bg_repeat = '';
}
if(!empty($virtue['menu_bg_placementx'])) {
$menu_bg_x = $virtue['menu_bg_placementx'];
} else {
  $menu_bg_x = '';
}
if(!empty($virtue['menu_bg_placementy'])) {
$menu_bg_y = $virtue['menu_bg_placementy'];
} else {
  $menu_bg_y = '';
}
if(!empty($virtue['menu_bg_color']) || !empty($virtue['bg_menu_bg_img']['url'])) {
    $menuclass = '.navclass {background:'.$menu_bg_color.' '.$menu_bg_img.' '.$menu_bg_repeat.' '.$menu_bg_x.' '.$menu_bg_y.';}';
  } else {
    $menuclass = '';
  }

if(!empty($virtue['mobile_bg_color'])) {
$mobile_bg_color = $virtue['mobile_bg_color'];
} else {
  $mobile_bg_color = '';
}
if(!empty($virtue['bg_mobile_bg_img']['url'])) { 
  $mobile_bg_img = 'url('.$virtue['bg_mobile_bg_img']['url'].')'; 
} else {
  $mobile_bg_img = '';
}
if(!empty($virtue['mobile_bg_repeat'])) {
$mobile_bg_repeat = $virtue['mobile_bg_repeat'];
} else {
  $mobile_bg_repeat = '';
}
if(!empty($virtue['mobile_bg_placementx'])) {
$mobile_bg_x = $virtue['mobile_bg_placementx'];
} else {
  $mobile_bg_x = '';
}
if(!empty($virtue['mobile_bg_placementy'])) {
$mobile_bg_y = $virtue['mobile_bg_placementy'];
} else {
  $mobile_bg_y = '';
}
if(!empty($virtue['mobile_bg_color']) || !empty($virtue['bg_mobile_bg_img']['url'])) {
    $mobileclass = '.mobileclass {background:'.$mobile_bg_color.' '.$mobile_bg_img.' '.$mobile_bg_repeat.' '.$mobile_bg_x.' '.$mobile_bg_y.';}';
  } else {
    $mobileclass = '';
  }

if(!empty($virtue['footer_bg_color'])) {
$footer_bg_color = $virtue['footer_bg_color'];
} else {
  $footer_bg_color = '';
}
if(!empty($virtue['bg_footer_bg_img']['url'])) {
 $footer_bg_img = 'url('.$virtue['bg_footer_bg_img']['url'].')'; 
} else {
  $footer_bg_img = '';
}
if(!empty($virtue['footer_bg_repeat'])) {
$footer_bg_repeat = $virtue['footer_bg_repeat'];
} else {
  $footer_bg_repeat = '';
}
if(!empty($virtue['footer_bg_placementx'])) {
$footer_bg_x = $virtue['footer_bg_placementx'];
} else {
  $footer_bg_x = '';
}
if(!empty($virtue['footer_bg_placementy'])) {
$footer_bg_y = $virtue['footer_bg_placementy'];
} else {
  $footer_bg_y = '';
}
if(!empty($virtue['footer_bg_color']) || !empty($virtue['bg_footer_bg_img']['url'])) {
    $footerclass = '.footerclass {background:'.$footer_bg_color.' '.$footer_bg_img.' '.$footer_bg_repeat.' '.$footer_bg_x.' '.$footer_bg_y.';}';
  } else {
    $footerclass = '';
  }
if(!empty($virtue['boxed_bg_color'])) {
$boxedbg_color = $virtue['boxed_bg_color'];
} else {
  $boxedbg_color = '';
}
if(!empty($virtue['bg_boxed_bg_img']['url'])) { 
  $boxedbg_img = 'url('.$virtue['bg_boxed_bg_img']['url'].')'; 
} else {
  $boxedbg_img = '';
}
if(!empty($virtue['boxed_bg_repeat'])) {
$boxedbg_repeat = 'background-repeat:'. $virtue['boxed_bg_repeat'].';';
} else {
  $boxedbg_repeat = '';
}
if(!empty($virtue['boxed_bg_placementx'])) {
$boxedbg_x = $virtue['boxed_bg_placementx'];
} else {
  $boxedbg_x = '0%';
}
if(!empty($virtue['boxed_bg_placementy'])) {
$boxedbg_y = $virtue['boxed_bg_placementy'];
} else {
  $boxedbg_y = '0%';
}
if(!empty($virtue['boxed_bg_fixed'])) {
$boxedbg_fixed = 'background-attachment: '.$virtue['boxed_bg_fixed'].';'; 
} else {
  $boxedbg_fixed = '';
}
if(!empty($virtue['boxed_bg_size'])) {
  $boxedbg_size = 'background-size: '.$virtue['boxed_bg_size'].';'; 
} else {
  $boxedbg_size = '';
}
if(!empty($virtue['boxed_bg_color']) || !empty($virtue['bg_boxed_bg_img']['url'])) {
    $boxedclass = 'body {background:'.$boxedbg_color.' '.$boxedbg_img.'; background-position: '.$boxedbg_x.' '.$boxedbg_y.'; '.$boxedbg_repeat.' '.$boxedbg_fixed.' '.$boxedbg_size.'}';
  } else {
    $boxedclass = '';
  }

  if( isset( $virtue['shop_title_uppercase']) and $virtue['shop_title_uppercase'] == 0) {
  $ptitle_uppercase = '.product_item .product_details h5 {text-transform: none;}';
} else {
  $ptitle_uppercase = '';
}
  if(!empty($virtue['x2_virtue_logo_upload']['url'])) {
  $x2logo = ' @media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {#logo .kad-standard-logo {display: none;} #logo .kad-retina-logo {display: block;}}';
} else {
  $x2logo = '';
}
  if(!empty($virtue['shop_title_min_height'])) {
  $ptitle_minheight = '.product_item .product_details h5 {min-height:'.$virtue['shop_title_min_height'].'px;}';
} else {
  $ptitle_minheight = '';
}
if(isset($virtue['logo_layout']) and ($virtue['logo_layout'] == 'logocenter')) {
  $menu_layout_center = '@media (max-width: 979px) {.nav-trigger .nav-trigger-case {position: static; display: block; width: 100%;}}';
  } else {
  $menu_layout_center = '';
  } 

  if(isset($virtue['hide_author']) and ($virtue['hide_author'] == 0)) {
  $show_author = '.kad-hidepostauthortop, .postauthortop {display:none;}';
  } else {
  $show_author = '';
  } 
    if(isset($virtue['topbar_layout']) and ($virtue['topbar_layout'] == 1)) {
  $topbar_layout = '.kad-topbar-left, .kad-topbar-left .topbarmenu {float:right;} .kad-topbar-left .topbar_social, .kad-topbar-left .topbarmenu ul, .kad-topbar-left .kad-cart-total,.kad-topbar-right #topbar-search .form-search{float:left}';
  } else {
  $topbar_layout = '';
  } 
  if (isset($virtue['product_quantity_input']) && $virtue['product_quantity_input'] == 1) {
  $quantity_input = 'input[type=number]::-webkit-inner-spin-button, input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; } input[type=number] {-moz-appearance: textfield;}.quantity input::-webkit-outer-spin-button,.quantity input::-webkit-inner-spin-button {display: none;}';
} else {
  $quantity_input = '';
}
  if(isset($virtue['hide_image_border']) and ($virtue['hide_image_border'] == 1)) {
  $wp_image_border = '[class*="wp-image"] {-webkit-box-shadow: none;-moz-box-shadow: none;box-shadow: none;border:none;}[class*="wp-image"]:hover {-webkit-box-shadow: none;-moz-box-shadow: none;box-shadow: none;border:none;}.light-dropshaddow {-webkit-box-shadow: none;-moz-box-shadow: none;box-shadow: none;}';
  } else {
  $wp_image_border = '';
  }
  if(isset($virtue['mobile_switch']) && $virtue['mobile_switch'] == '1') {
  $mobileslider = '@media (max-width: 767px) {.kad-desktop-slider {display:none;}}';
} else {
  $mobileslider = '';
}
	if ( isset( $virtue['paragraph_margin_bottom'] ) ) {
		$paragraph_spacing = '.entry-content p { margin-bottom:' . esc_attr( $virtue['paragraph_margin_bottom'] ) . 'px;}';
	} else {
		$paragraph_spacing = '';
	}
	if ( ! empty( $virtue['custom_css'] ) ) {
		$custom_css = $virtue['custom_css'];
	} else {
		$custom_css = '';
	}

	$kad_custom_css = '<style type="text/css">' . $logo_padding_top . $logo_padding_bottom . $logo_padding_left . $logo_padding_right . $menu_margin_top . $menu_margin_bottom . $font_family . $color_primary . $color_primary30 . $color_grayfont . $quantity_input . $color_footerfont . $contentclass . $topbarclass . $headerclass . $menuclass . $mobileclass . $footerclass . $boxedclass . $show_author . $ptitle_uppercase . $menu_layout_center . $x2logo . $ptitle_minheight . $topbar_layout . $wp_image_border . $mobileslider . $paragraph_spacing . $custom_css . '</style>';
	
	echo $kad_custom_css;
}
add_action( 'wp_head', 'virtue_custom_css' );
?>
