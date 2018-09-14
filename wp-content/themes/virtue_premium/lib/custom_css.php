<?php 

function kad_custom_css() {

global $virtue_premium; 
if(isset($virtue_premium['logo_padding_top'])) {
$logo_padding_top = '#logo {padding-top:'.$virtue_premium['logo_padding_top'].'px;}';
} else {
  $logo_padding_top = '#logo {padding-top:25px;}';
}
if(isset($virtue_premium['logo_padding_bottom'])) {
 $logo_padding_bottom = '#logo {padding-bottom:'.$virtue_premium['logo_padding_bottom'].'px;}';
 } else {
  $logo_padding_bottom = '#logo {padding-bottom:10px;}';
 } 
 if(isset($virtue_premium['logo_padding_left'])) {
 $logo_padding_left = '#logo {margin-left:'.$virtue_premium['logo_padding_left'].'px;}';
 } else {
$logo_padding_left = '#logo {margin-left:0px;}';
 }
 if(isset($virtue_premium['logo_padding_right'])) {
  $logo_padding_right = '#logo {margin-right:'.$virtue_premium['logo_padding_right'].'px;}';
} else {
  $logo_padding_right = '#logo {margin-right:0px;}';
}
if(isset($virtue_premium['menu_margin_top'])) {
 $menu_margin_top = '#nav-main, .nav-main {margin-top:'.$virtue_premium['menu_margin_top'].'px;}';
 } else {
  $menu_margin_top = '#nav-main, .nav-main {margin-top:40px;}';
 } 
 if(isset($virtue_premium['menu_margin_bottom'])) {
 $menu_margin_bottom = '#nav-main, .nav-main  {margin-bottom:'.$virtue_premium['menu_margin_bottom'].'px;}';
} else {
  $menu_margin_bottom = '#nav-main, .nav-main {margin-bottom:10px;}';
}
//Typography
if(!empty($virtue_premium['font_h1'])) {
  $font_family = '.headerfont, .tp-caption, .yith-wcan-list li, .yith-wcan .yith-wcan-reset-navigation, ul.yith-wcan-label li a, .price {font-family:'.$virtue_premium['font_h1']['font-family'].';} 
  .topbarmenu ul li {font-family:'.$virtue_premium['font_primary_menu']['font-family'].';}
  #kadbreadcrumbs {font-family:'.$virtue_premium['font_p']['font-family'].';}';
} else {
  $font_family = '';
}
if(!empty($virtue_premium['font_p']['color'])) {
$body_color = '.color_body, .sidebar a, .author-name a, .author-latestposts h5 a, .author-latestposts h5, 
.author-profile .author-occupation, .nav-tabs>li.active>a, .nav-tabs>li.active>a:hover, .nav-tabs>li.active>a:focus, .product_price, .product_details > .product_excerpt {color:'.$virtue_premium['font_p']['color'].';}';
} else {
$body_color = '';
}
  //Menus Styling
  if(!empty($virtue_premium['primarymenu_hover_color'])) {
  $color_pmenu_hover = '#nav-main ul.sf-menu a:hover, .nav-main ul.sf-menu a:hover, #nav-main ul.sf-menu li.current-menu-item > a, .nav-main ul.sf-menu li.current-menu-item > a, #nav-main ul.sf-menu ul li a:hover, .nav-main ul.sf-menu ul li a:hover {color:'.$virtue_premium['primarymenu_hover_color'].';}';
  } else {
  $color_pmenu_hover = '';
  }
  if(!empty($virtue_premium['primarymenu_hover_bg_color'])) {
    $color_pmenu_bg_hover = '#nav-main ul.sf-menu li a:hover, .nav-main ul.sf-menu a:hover, #nav-main ul.sf-menu li.current-menu-item > a, .nav-main ul.sf-menu li.current-menu-item > a, #nav-main ul.sf-menu ul li a:hover, .nav-main ul.sf-menu ul li a:hover  {background:'.$virtue_premium['primarymenu_hover_bg_color'].';}';
    } else {
    $color_pmenu_bg_hover = '';
  }
   if(!empty($virtue_premium['secondarymenu_hover_color'])) {
  $color_smenu_hover = '#nav-second ul.sf-menu > li:hover > a, #nav-second ul.sf-menu a:hover, #nav-second ul.sf-menu li.current-menu-item > a, #nav-second ul.sf-menu ul li a:hover {color:'.$virtue_premium['secondarymenu_hover_color'].';}';
  } else {
  $color_smenu_hover = '';
  }
  if(!empty($virtue_premium['secondarymenu_hover_bg_color'])) {
    $color_smenu_bg_hover = '#nav-second ul.sf-menu > li:hover, #nav-second ul.sf-menu > li.current-menu-item, #nav-second ul.sf-menu ul > li.current-menu-item, #nav-second ul.sf-menu ul li a:hover {background:'.$virtue_premium['secondarymenu_hover_bg_color'].';}';
    } else {
    $color_smenu_bg_hover = '';
  }
   if(!empty($virtue_premium['mobilemenu_hover_color'])) {
  $color_mmenu_hover = '.kad-mobile-nav .kad-nav-inner li.current-menu-item>a, .kad-mobile-nav .kad-nav-inner li a:hover, #kad-banner .mobile-nav-trigger-id a.nav-trigger-case:hover .kad-menu-name, #kad-banner .mobile-nav-trigger-id a.nav-trigger-case:hover .kad-navbtn, .nav-trigger-case:hover .kad-navbtn {color:'.$virtue_premium['mobilemenu_hover_color'].';}';
  } else {
  $color_mmenu_hover = '';
  }
  if(!empty($virtue_premium['mobilemenu_hover_bg_color'])) {
    $color_mmenu_bg_hover = '.kad-mobile-nav .kad-nav-inner li.current-menu-item>a, .kad-mobile-nav .kad-nav-inner li a:hover, #kad-banner .mobile-nav-trigger-id a.nav-trigger-case:hover .kad-menu-name, #kad-banner .mobile-nav-trigger-id a.nav-trigger-case:hover .kad-navbtn  {background:'.$virtue_premium['mobilemenu_hover_bg_color'].';}';
    } else {
    $color_mmenu_bg_hover = '';
  }
  if(!empty($virtue_premium['font_primary_menu']['color'])) {
  $color_mobile_header = '.mobile-header-container a, .mobile-header-container .mh-nav-trigger-case{color:'.$virtue_premium['font_primary_menu']['color'].';}';
  } else {
   $color_mobile_header = '';
  }
  if(!empty($virtue_premium['font_mobile_menu']) && !empty($virtue_premium['font_mobile_menu']['color']) ) {
    $color_mmenu_search_color = '.kad-mobile-nav .form-search .search-query, .kad-mobile-nav .form-search .search-icon {color:'.$virtue_premium['font_mobile_menu']['color'].';}';
    $color_mmenu_search_color_moz = '.kad-mobile-nav .form-search :-moz-placeholder {color:'.$virtue_premium['font_mobile_menu']['color'].';}';
    $color_mmenu_search_color_mozz = '.kad-mobile-nav .form-search ::-moz-placeholder {color:'.$virtue_premium['font_mobile_menu']['color'].';}';
    $color_mmenu_search_color_ms = '.kad-mobile-nav .form-search :-ms-input-placeholder {color:'.$virtue_premium['font_mobile_menu']['color'].';}';
    $color_mmenu_search_color_kit = '.kad-mobile-nav .form-search ::-webkit-input-placeholder {color:'.$virtue_premium['font_mobile_menu']['color'].';}';
    } else {
    $color_mmenu_search_color = '';
    $color_mmenu_search_color_moz = '';
    $color_mmenu_search_color_mozz = '';
    $color_mmenu_search_color_ms = '';
    $color_mmenu_search_color_kit = '';
  }

//Basic Styling

if(!empty($virtue_premium['primary_color'])) {
  $primaryrgb = kad_hex2rgb($virtue_premium['primary_color']); 
  $color_primary = '.home-message:hover {background-color:'.$virtue_premium['primary_color'].'; background-color: rgba('.$primaryrgb[0].', '.$primaryrgb[1].', '.$primaryrgb[2].', 0.6);}
  nav.woocommerce-pagination ul li a:hover, .wp-pagenavi a:hover, .panel-heading .accordion-toggle, .variations .kad_radio_variations label:hover, .variations .kad_radio_variations label.selectedValue {border-color: '.$virtue_premium['primary_color'].';}
  a, a:focus, #nav-main ul.sf-menu ul li a:hover, .product_price ins .amount, .price ins .amount, .color_primary, .primary-color, #logo a.brand, #nav-main ul.sf-menu a:hover,
  .woocommerce-message:before, .woocommerce-info:before, #nav-second ul.sf-menu a:hover, .footerclass a:hover, .posttags a:hover, .subhead a:hover, .nav-trigger-case:hover .kad-menu-name, 
  .nav-trigger-case:hover .kad-navbtn, #kadbreadcrumbs a:hover, #wp-calendar a, .testimonialbox .kadtestimoniallink:hover, .star-rating, .has-virtue-primary-color {color: '.$virtue_premium['primary_color'].';}
.widget_price_filter .ui-slider .ui-slider-handle, .product_item .kad_add_to_cart:hover, .product_item.hidetheaction:hover .kad_add_to_cart:hover, .kad-btn-primary, html .woocommerce-page .widget_layered_nav ul.yith-wcan-label li a:hover, html .woocommerce-page .widget_layered_nav ul.yith-wcan-label li.chosen a,
.product-category.grid_item a:hover h5, .woocommerce-message .button, .widget_layered_nav_filters ul li a, .widget_layered_nav ul li.chosen a, .track_order .button, .wpcf7 input.wpcf7-submit, .yith-wcan .yith-wcan-reset-navigation,.single_add_to_cart_button,
#containerfooter .menu li a:hover, .bg_primary, .portfolionav a:hover, .home-iconmenu a:hover, .home-iconmenu .home-icon-item:hover, p.demo_store, .topclass, #commentform .form-submit #submit, .kad-hover-bg-primary:hover, .widget_shopping_cart_content .checkout,
.login .form-row .button, .post-password-form input[type="submit"], .menu-cart-btn .kt-cart-total, #kad-head-cart-popup a.button.checkout, .kad-post-navigation .kad-previous-link a:hover, .kad-post-navigation .kad-next-link a:hover, .shipping-calculator-form .button, .cart_totals .checkout-button, .select2-results .select2-highlighted, .variations .kad_radio_variations label.selectedValue, #payment #place_order, .shop_table .actions input[type=submit].checkout-button, input[type="submit"].button, .order-actions .button, .productnav a:hover, .image_menu_hover_class, .select2-container--default .select2-results__option--highlighted[aria-selected], .has-virtue-primary-background-color {background: '.$virtue_premium['primary_color'].';}';
} else {
  $color_primary = '';
}
if(!empty($virtue_premium['primary20_color'])) {
  $color_primary30 =  'a:hover, .has-virtue-primary-light-color {color: '.$virtue_premium['primary20_color'].';} .kad-btn-primary:hover, .login .form-row .button:hover, #payment #place_order:hover, .yith-wcan .yith-wcan-reset-navigation:hover, .widget_shopping_cart_content .checkout:hover,.single_add_to_cart_button:hover,
.woocommerce-message .button:hover, #commentform .form-submit #submit:hover, .wpcf7 input.wpcf7-submit:hover, .track_order .button:hover, .widget_layered_nav_filters ul li a:hover, .cart_totals .checkout-button:hover,.shipping-calculator-form .button:hover,
.widget_layered_nav ul li.chosen a:hover, .shop_table .actions input[type=submit].checkout-button:hover, #kad-head-cart-popup a.button.checkout:hover, .order-actions .button:hover, input[type="submit"].button:hover, .product_item.hidetheaction:hover .kad_add_to_cart, .post-password-form input[type="submit"]:hover, .has-virtue-primary-light-background-color {background: '.$virtue_premium['primary20_color'].';}';
} else {
  $color_primary30 = '';
}
if(!empty($virtue_premium['gray_font_color'])) {
  $color_grayfont = '.color_gray, #kadbreadcrumbs a, .subhead, .subhead a, .posttags, .posttags a, .product_meta a, .kadence_recent_posts .postclass a {color:'.$virtue_premium['gray_font_color'].';}';
} else {
  $color_grayfont = '';
}
if(!empty($virtue_premium['footerfont_color'])) {
  $color_footerfont = '#containerfooter h3, #containerfooter h5, #containerfooter, .footercredits p, .footerclass a, .footernav ul li a {color:'.$virtue_premium['footerfont_color'].';}';
} else {
  $color_footerfont = '';
}
//Body Font
if(!empty($virtue_premium['font_p']['color'])) {
  $body_color = '.sidebar a, .product_price, .select2-container .select2-choice, .kt_product_toggle_container .toggle_grid, .kt_product_toggle_container .toggle_list, .kt_product_toggle_container_list .toggle_grid, .kt_product_toggle_container_list .toggle_list {color:'.$virtue_premium['font_p']['color'].';}';
} else {
  $body_color = '';
}

//Advanced Styling

if(!empty($virtue_premium['content_bg_color'])) {
$content_bg_color = $virtue_premium['content_bg_color'];
} else {
  $content_bg_color = '';
}
if(!empty($virtue_premium['bg_content_bg_img']['url'])) { 
  $content_bg_img = 'url('.$virtue_premium['bg_content_bg_img']['url'].')'; 
} else {
  $content_bg_img = '';
}
if(!empty($virtue_premium['content_bg_repeat'])) {
$content_bg_repeat = $virtue_premium['content_bg_repeat'];
} else {
  $content_bg_repeat = '';
}
if(!empty($virtue_premium['content_bg_placementx'])) {
  $content_bg_x = $virtue_premium['content_bg_placementx'];
} else {
  $content_bg_x = '';
}
if (!empty($virtue_premium['content_bg_placementy'])) {
$content_bg_y = $virtue_premium['content_bg_placementy']; 
} else {
  $content_bg_y = '';
}
if(!empty($virtue_premium['content_bg_color']) || !empty($virtue_premium['bg_content_bg_img']['url'])) {
    $contentclass = '.contentclass, .nav-tabs>li.active>a, .nav-tabs>li.active>a:hover, .nav-tabs>li.active>a:focus, .hrule_gradient:after {background:'.$content_bg_color.' '.$content_bg_img.' '.$content_bg_repeat.' '.$content_bg_x.' '.$content_bg_y.';}';
  } else {
    $contentclass = '';
  }
if(!empty($virtue_premium['header_bg_color'])) {
  $header_bg_color = $virtue_premium['header_bg_color'];
  } else {
    $header_bg_color = '';
  }
  if(!empty($virtue_premium['bg_header_bg_img']['url'])) { 
    $header_bg_img = 'url('.$virtue_premium['bg_header_bg_img']['url'].')'; 
  } else {
    $header_bg_img = '';
  }
  if(!empty($virtue_premium['header_bg_repeat'])) {
  $header_bg_repeat = $virtue_premium['header_bg_repeat'];
  } else {
    $header_bg_repeat = '';
  }
  if(!empty($virtue_premium['header_bg_placementx'])) {
  $header_bg_x = $virtue_premium['header_bg_placementx'];
  } else {
    $header_bg_x = '';
  }
  if(!empty($virtue_premium['header_bg_placementy'])) {
  $header_bg_y = $virtue_premium['header_bg_placementy'];
  } else {
    $header_bg_y = '';
  }
  if(!empty($virtue_premium['header_bg_color']) || !empty($virtue_premium['bg_header_bg_img']['url'])) {
    $headerclass = '.headerclass, .mobile-headerclass, .kt-mainnavsticky .sticky-wrapper.is-sticky #nav-main {background:'.$header_bg_color.' '.$header_bg_img.' '.$header_bg_repeat.' '.$header_bg_x.' '.$header_bg_y.';}';
  } else {
    $headerclass = '';
  }
if(!empty($virtue_premium['topbar_bg_color'])) {
$topbar_bg_color = $virtue_premium['topbar_bg_color'];
} else {
  $topbar_bg_color = '';
}
if(!empty($virtue_premium['bg_topbar_bg_img']['url'])) { 
  $topbar_bg_img = 'url('.$virtue_premium['bg_topbar_bg_img']['url'].')'; 
} else {
  $topbar_bg_img = '';
}
if(!empty($virtue_premium['topbar_bg_repeat'])) {
$topbar_bg_repeat = $virtue_premium['topbar_bg_repeat'];
} else {
  $topbar_bg_repeat = '';
}
if(!empty($virtue_premium['topbar_bg_placementx'])) {
$topbar_bg_x = $virtue_premium['topbar_bg_placementx'];
} else {
  $topbar_bg_x = '';
}
if(!empty($virtue_premium['topbar_bg_placementy'])) {
$topbar_bg_y = $virtue_premium['topbar_bg_placementy'];
} else {
  $topbar_bg_y = '';
}
if(!empty($virtue_premium['topbar_bg_color']) || !empty($virtue_premium['bg_topbar_bg_img']['url'])) {
    $topbarclass = '.topclass {background:'.$topbar_bg_color.' '.$topbar_bg_img.' '.$topbar_bg_repeat.' '.$topbar_bg_x.' '.$topbar_bg_y.';}';
  } else {
    $topbarclass = '';
  }
if(!empty($virtue_premium['menu_bg_color'])) {
$menu_bg_color = $virtue_premium['menu_bg_color'];
} else {
  $menu_bg_color = '';
}
if(!empty($virtue_premium['bg_menu_bg_img']['url'])) {
 $menu_bg_img = 'url('.$virtue_premium['bg_menu_bg_img']['url'].')'; 
} else {
  $menu_bg_img = '';
}
if(!empty($virtue_premium['menu_bg_repeat'])) {
$menu_bg_repeat = $virtue_premium['menu_bg_repeat'];
} else {
  $menu_bg_repeat = '';
}
if(!empty($virtue_premium['menu_bg_placementx'])) {
$menu_bg_x = $virtue_premium['menu_bg_placementx'];
} else {
  $menu_bg_x = '';
}
if(!empty($virtue_premium['menu_bg_placementy'])) {
$menu_bg_y = $virtue_premium['menu_bg_placementy'];
} else {
  $menu_bg_y = '';
}
if(!empty($virtue_premium['menu_bg_color']) || !empty($virtue_premium['bg_menu_bg_img']['url'])) {
    $menuclass = '.navclass {background:'.$menu_bg_color.' '.$menu_bg_img.' '.$menu_bg_repeat.' '.$menu_bg_x.' '.$menu_bg_y.';}';
  } else {
    $menuclass = '';
  }

if(!empty($virtue_premium['mobile_bg_color'])) {
$mobile_bg_color = $virtue_premium['mobile_bg_color'];
} else {
  $mobile_bg_color = '';
}
if(!empty($virtue_premium['bg_mobile_bg_img']['url'])) { 
  $mobile_bg_img = 'url('.$virtue_premium['bg_mobile_bg_img']['url'].')'; 
} else {
  $mobile_bg_img = '';
}
if(!empty($virtue_premium['mobile_bg_repeat'])) {
$mobile_bg_repeat = $virtue_premium['mobile_bg_repeat'];
} else {
  $mobile_bg_repeat = '';
}
if(!empty($virtue_premium['mobile_bg_placementx'])) {
$mobile_bg_x = $virtue_premium['mobile_bg_placementx'];
} else {
  $mobile_bg_x = '';
}
if(!empty($virtue_premium['mobile_bg_placementy'])) {
$mobile_bg_y = $virtue_premium['mobile_bg_placementy'];
} else {
  $mobile_bg_y = '';
}
if(!empty($virtue_premium['mobile_bg_color']) || !empty($virtue_premium['bg_mobile_bg_img']['url'])) {
    $mobileclass = '.mobileclass {background:'.$mobile_bg_color.' '.$mobile_bg_img.' '.$mobile_bg_repeat.' '.$mobile_bg_x.' '.$mobile_bg_y.';}';
  } else {
    $mobileclass = '';
  }
if(!empty($virtue_premium['feature_bg_color'])) {
$feature_bg_color = $virtue_premium['feature_bg_color'];
} else {
  $feature_bg_color = '';
}
if(!empty($virtue_premium['bg_feature_bg_img']['url'])) {
 $feature_bg_img = 'url('.$virtue_premium['bg_feature_bg_img']['url'].')'; 
} else {
  $feature_bg_img = '';
}
if(!empty($virtue_premium['feature_bg_repeat'])) {
$feature_bg_repeat = $virtue_premium['feature_bg_repeat'];
} else {
  $feature_bg_repeat = '';
}
if(!empty($virtue_premium['feature_bg_placementx'])) {
$feature_bg_x = $virtue_premium['feature_bg_placementx'];
} else {
  $feature_bg_x = '';
}
if(!empty($virtue_premium['feature_bg_placementy'])) {
$feature_bg_y = $virtue_premium['feature_bg_placementy'];
} else {
  $feature_bg_y = '';
}
if(!empty($virtue_premium['feature_bg_color']) || !empty($virtue_premium['bg_feature_bg_img']['url'])) {
    $featureclass = '.panel-row-style-wide-feature {background:'.$feature_bg_color.' '.$feature_bg_img.' '.$feature_bg_repeat.' '.$feature_bg_x.' '.$feature_bg_y.';}';
  } else {
    $featureclass = '';
  }
if(!empty($virtue_premium['footer_bg_color'])) {
$footer_bg_color = $virtue_premium['footer_bg_color'];
} else {
  $footer_bg_color = '';
}
if(!empty($virtue_premium['bg_footer_bg_img']['url'])) {
 $footer_bg_img = 'url('.$virtue_premium['bg_footer_bg_img']['url'].')'; 
} else {
  $footer_bg_img = '';
}
if(!empty($virtue_premium['footer_bg_repeat'])) {
$footer_bg_repeat = $virtue_premium['footer_bg_repeat'];
} else {
  $footer_bg_repeat = '';
}
if(!empty($virtue_premium['footer_bg_placementx'])) {
$footer_bg_x = $virtue_premium['footer_bg_placementx'];
} else {
  $footer_bg_x = '';
}
if(!empty($virtue_premium['footer_bg_placementy'])) {
$footer_bg_y = $virtue_premium['footer_bg_placementy'];
} else {
  $footer_bg_y = '';
}
if(!empty($virtue_premium['footer_bg_color']) || !empty($virtue_premium['bg_footer_bg_img']['url'])) {
    $footerclass = '.footerclass {background:'.$footer_bg_color.' '.$footer_bg_img.' '.$footer_bg_repeat.' '.$footer_bg_x.' '.$footer_bg_y.';}';
  } else {
    $footerclass = '';
  }
if(!empty($virtue_premium['boxed_bg_color'])) {
$boxedbg_color = $virtue_premium['boxed_bg_color'];
} else {
  $boxedbg_color = '';
}
if(!empty($virtue_premium['bg_boxed_bg_img']['url'])) { 
  $boxedbg_img = 'url('.$virtue_premium['bg_boxed_bg_img']['url'].')'; 
} else {
  $boxedbg_img = '';
}
if(!empty($virtue_premium['boxed_bg_repeat'])) {
$boxedbg_repeat = 'background-repeat:'. $virtue_premium['boxed_bg_repeat'].';';
} else {
  $boxedbg_repeat = '';
}
if(!empty($virtue_premium['boxed_bg_placementx'])) {
$boxedbg_x = $virtue_premium['boxed_bg_placementx'];
} else {
  $boxedbg_x = '0%';
}
if(!empty($virtue_premium['boxed_bg_placementy'])) {
$boxedbg_y = $virtue_premium['boxed_bg_placementy'];
} else {
  $boxedbg_y = '0%';
}
if(!empty($virtue_premium['boxed_bg_fixed'])) {
  $boxedbg_fixed = 'background-attachment: '.$virtue_premium['boxed_bg_fixed'].';'; 
} else {
  $boxedbg_fixed = '';
}
if(!empty($virtue_premium['boxed_bg_size'])) {
  $boxedbg_size = 'background-size: '.$virtue_premium['boxed_bg_size'].';'; 
} else {
  $boxedbg_size = '';
}
if(!empty($virtue_premium['boxed_bg_color']) || !empty($virtue_premium['bg_boxed_bg_img']['url'])) {
    $boxedclass = 'body {background:'.$boxedbg_color.' '.$boxedbg_img.'; background-position: '.$boxedbg_x.' '.$boxedbg_y.'; '.$boxedbg_repeat.' '.$boxedbg_fixed.' '.$boxedbg_size.'}';
  } else {
    $boxedclass = '';
  }
if(isset($virtue_premium['logo_layout']) and ($virtue_premium['logo_layout'] == 'logocenter')) {
    if (isset($virtue_premium['show_mobile_btn']) && $virtue_premium['show_mobile_btn'] == 1) { 
      $menu_layout_center = '@media (max-width: 992px) {.nav-trigger .nav-trigger-case {top: 0;} .id-kad-mobile-nav {margin-top:50px;}}';
      } else {
      $menu_layout_center = '@media (max-width: 992px) {.nav-trigger .nav-trigger-case {position: static; display: block; width: 100%;}}@media only screen and (device-width: 768px) {.nav-trigger .nav-trigger-case {position: static; display: block; width: 100%;}}';
      }
  } else {
  $menu_layout_center = '';
  } 
if(isset($virtue_premium['page_max_width']) && $virtue_premium['page_max_width'] == '1') {
    $page_max_width = '@media (min-width: 1200px) {.container {width: 970px;} aside.col-lg-3 {width: 33.33333333333333%;} .main.col-lg-9 {width: 66.66666666666666%;} .sf-menu>.kt-lgmenu>ul, .sf-menu>li.kt-lgmenu:hover>ul, .sf-menu>li.kt-lgmenu.sfHover>ul {width:940px;}}';
    } else {
      $page_max_width = '';
    } 

  if(isset($virtue_premium['shop_title_uppercase']) and $virtue_premium['shop_title_uppercase'] == 0) {
  $ptitle_uppercase = '.product_item .product_details h5 {text-transform: none;}';
} else {
  $ptitle_uppercase = '';
}
  if(!empty($virtue_premium['x2_virtue_logo_upload']['url'])) {
  $x2logo = ' @media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) { body #kad-banner #logo .kad-standard-logo {display: none;} body #kad-banner #logo .kad-retina-logo {display: block;}}';
} else {
  $x2logo = '';
}
  if(!empty($virtue_premium['shop_title_min_height'])) {
  $ptitle_minheight = '.product_item .product_details h5 {min-height:'.$virtue_premium['shop_title_min_height'].'px;}';
} else {
  $ptitle_minheight = '';
}

  if(!empty($virtue_premium['icon_font_color'])) {
  $color_iconmenu = '.home-iconmenu a h4, .home-iconmenu a i, .home-iconmenu a p, .home-iconmenu .home-icon-item h4, .home-iconmenu .home-icon-item p, .home-iconmenu .home-icon-item i {color:'.$virtue_premium['icon_font_color'].';} .home-iconmenu a:hover h4, .home-iconmenu a:hover i, .home-iconmenu a:hover p, .home-iconmenu .home-icon-item:hover h4, .home-iconmenu .home-icon-item:hover i, .home-iconmenu .home-icon-item:hover p {color:#fff;} ';
} else {
  $color_iconmenu = '';
}
  if(!empty($virtue_premium['icon_bg_color'])) {
  $color_iconmenubg = '.home-iconmenu a, .home-iconmenu .home-icon-item {background:'.$virtue_premium['icon_bg_color'].';}';
} else {
  $color_iconmenubg = '';
}
if( isset( $virtue_premium['secondary_menu_size'] ) && !empty($virtue_premium['secondary_menu_size']) ) {
	$smenu_width = '#nav-second ul.sf-menu>li {width:'.$virtue_premium['secondary_menu_size'].';}';
} else {
	$smenu_width = '';
} 
  if(isset($virtue_premium['hide_author']) and ($virtue_premium['hide_author'] == 0)) {
  $show_author = '.kad-hidepostauthortop, .postauthortop {display:none;}';
  } else {
  $show_author = '';
  } 
  if(isset($virtue_premium['hide_postedin']) and ($virtue_premium['hide_postedin'] == 0)) {
  $show_postedin = '.subhead .postedintop, .kad-hidepostedin {display:none;}';
  } else {
  $show_postedin = '';
  } 
  if(isset($virtue_premium['hide_commenticon']) and ($virtue_premium['hide_commenticon'] == 0)) {
  $show_comment = '.postcommentscount {display:none;}';
  } else {
  $show_comment = '';
  } 
  if(isset($virtue_premium['hide_postdate']) and ($virtue_premium['hide_postdate'] == 0)) {
  $show_date = '.postdate, .kad-hidedate, .postdatetooltip, .recentpost_date{display:none;}';
  } else {
  $show_date = '';
  } 
  if(isset($virtue_premium['topbar_layout']) and ($virtue_premium['topbar_layout'] == 1)) {
  $topbar_layout = '.kad-topbar-left, .kad-topbar-left .topbarmenu {float:right;} .kad-topbar-left .topbar_social, .kad-topbar-left .topbarmenu ul, .kad-topbar-left .kad-cart-total,.kad-topbar-right #topbar-search .form-search{float:left} #topbar .mobile-nav-trigger-id {float: left;}';
  } else {
  $topbar_layout = '';
  } 
  if(isset($virtue_premium['hide_image_border']) and ($virtue_premium['hide_image_border'] == 1)) {
  $wp_image_border = '[class*="wp-image"] {-webkit-box-shadow: none;-moz-box-shadow: none;box-shadow: none;border:none;}[class*="wp-image"]:hover {-webkit-box-shadow: none;-moz-box-shadow: none;box-shadow: none;border:none;} .light-dropshaddow {-moz-box-shadow: none;-webkit-box-shadow: none;box-shadow: none;}';
  } else {
  $wp_image_border = '';
  } 
if (isset($virtue_premium['show_breadcrumbs_shop']) && $virtue_premium['show_breadcrumbs_shop'] == 0) {
  $shopordering = '.woocommerce-ordering {margin: 16px 0 0;}';
} else {
  $shopordering = '';
}
if (isset($virtue_premium['show_breadcrumbs_portfolio']) && $virtue_premium['show_breadcrumbs_portfolio'] == 0) {
  $portfoliobread = '.portfolionav {padding: 10px 0 10px;}';
} else {
  $portfoliobread = '';
}
if (isset($virtue_premium['show_breadcrumbs_portfolio']) && $virtue_premium['show_breadcrumbs_portfolio'] == 0) {
  $portfoliobread = '.portfolionav {padding: 10px 0 10px;}';
} else {
  $portfoliobread = '';
}
if (isset($virtue_premium['smooth_scrolling_background']) && $virtue_premium['smooth_scrolling_background'] == 1) {
  $scrolling_background = '#ascrail2000 {background-color: transparent;}';
} else {
  $scrolling_background = '';
}
if (isset($virtue_premium['dropdown_background_color']) && !empty($virtue_premium['dropdown_background_color']) ) {
  $mdropdown_background = '#nav-main .sf-menu ul, .nav-main .sf-menu ul, #nav-second .sf-menu ul, .topbarmenu .sf-menu ul{background: '.$virtue_premium['dropdown_background_color'].';}';
} else {
  $mdropdown_background = '';
}
if (isset($virtue_premium['dropdown_font_color']) && !empty($virtue_premium['dropdown_font_color']) ) {
  $mdropdown_font = '#nav-main ul.sf-menu ul li a, .nav-main ul.sf-menu ul li a, #nav-second ul.sf-menu ul li a, .topbarmenu ul.sf-menu ul li a, #kad-head-cart-popup ul.cart_list li, #nav-main ul.sf-menu ul#kad-head-cart-popup li .quantity, #nav-main ul.sf-menu ul#kad-head-cart-popup .total, #kad-head-cart-popup p.woocommerce-mini-cart__empty-message {color: '.$virtue_premium['dropdown_font_color'].';}';
} else {
  $mdropdown_font = '';
}
if (isset($virtue_premium['dropdown_border_color']) && !empty($virtue_premium['dropdown_border_color']) ) {
  $mdropdown_border = '#nav-main .sf-menu ul li, .nav-main .sf-menu ul li, #nav-second .sf-menu ul li, .topbarmenu .sf-menu ul li,#nav-main .sf-menu ul, .nav-main .sf-menu ul, #nav-second .sf-menu ul, .topbarmenu .sf-menu ul {border-color: '.$virtue_premium['dropdown_border_color'].';}';
} else {
  $mdropdown_border = '';
}
if (isset($virtue_premium['side_header_menu_width'])) {
  $centermenuwidth = $virtue_premium['side_header_menu_width'];
} else {
  $centermenuwidth = '33.333333';
}
$centermenuwidthoutput = '.kad-header-style-two .nav-main ul.sf-menu > li {width: '.$centermenuwidth.'%;}';
if(isset($virtue_premium['virtue_animate_in']) && $virtue_premium['virtue_animate_in'] == 0) {
  $animate = '.kad-animation {opacity: 1; top:0;} .kad_portfolio_fade_in, .kad_product_fade_in, .kad_gallery_fade_in, .kad_testimonial_fade_in, .kad_staff_fade_in, .kad_blog_fade_in {opacity: 1;}';
} else {
  $animate = '';
}
if(isset($virtue_premium['topbar_mobile']) && $virtue_premium['topbar_mobile'] == '1') {
    $topbar_mobile = '@media (max-width: 991px) {.topbarmenu ul.sf-menu {display: none;} } @media only screen and (device-width: 768px) {.topbarmenu ul.sf-menu {display: none;} } @media (max-width: 768px) {#topbar-search form {display: none;}}';
} else {
    $topbar_mobile = '';
}
if(isset($virtue_premium['sidebar_side']) && $virtue_premium['sidebar_side'] == 'left') {
 $sidebar_side = '@media (min-width: 992px) {body .main.col-lg-9 {float:right;}}';
} else {
  $sidebar_side = '';
}
if(isset($virtue_premium['m_sticky_header']) && $virtue_premium['m_sticky_header'] == '1' && $virtue_premium['sticky_header'] == '1' && $virtue_premium['header_style'] == 'shrink') {
   $head_height = $virtue_premium['header_height']/2;
  $stickymobile = 'header.mobile-stickyheader .nav-trigger .nav-trigger-case {width: auto; position: absolute; top: -'.$head_height.'px; right: 0; } .nav-trigger-case .kad-navbtn, .nav-trigger-case .kad-menu-name {line-height: '.$head_height.'px;} @media (max-width: 991px) {.stickyheader #kad-banner-sticky-wrapper,  .stickyheader #kad-banner-sticky-wrapper #kad-banner {min-height:'.$head_height.'px} .stickyheader #kad-banner #logo a, .stickyheader #kad-banner #logo a #thelogo, .stickyheader #kad-banner #kad-shrinkheader {height:'.$head_height.'px !important; line-height: '.$head_height.'px !important;} .stickyheader #kad-banner #logo a img{max-height:'.$head_height.'px !important;} .kad-header-left{width:75%; float:left;} .kad-header-right {float:left; width:25%;}}';
} else {
  $stickymobile = '';
}
if(isset($virtue_premium['m_center_sticky_header']) && $virtue_premium['m_center_sticky_header'] == '1' && $virtue_premium['sticky_header'] == '1' && $virtue_premium['shrink_center_header'] == '1') {
   $head_height = $virtue_premium['shrink_center_header_height']/2;
  $centerstickymobile = 'header.mobile-stickyheader .nav-trigger .nav-trigger-case {width: auto; position: absolute; top: -'.$head_height.'px; right: 0; } .nav-trigger-case .kad-navbtn, .nav-trigger-case .kad-menu-name {line-height: '.$head_height.'px;} @media (max-width: 991px) {.stickyheader #kad-banner-sticky-wrapper,  .stickyheader #kad-banner-sticky-wrapper #kad-banner {min-height:'.$head_height.'px} .stickyheader #kad-banner #logo a, .stickyheader #kad-banner #logo a #thelogo, .stickyheader #kad-banner #kad-shrinkheader {height:'.$head_height.'px !important; line-height: '.$head_height.'px !important;} .stickyheader #kad-banner #logo a img{max-height:'.$head_height.'px !important;} .kad-header-left{width:75%; float:left;} .kad-header-right {float:left; width:25%;}}';
} else {
  $centerstickymobile = '';
}
if (isset($virtue_premium['notavailable_placeholder_text']) && !empty($virtue_premium['notavailable_placeholder_text']) ) {
  $notavailable_placeholder_text = '.variations .kad_radio_variations label.kt_disabled:after {content: '.$virtue_premium['notavailable_placeholder_text'].';}';
} else {
  $notavailable_placeholder_text = '';
}
if (isset($virtue_premium['show_mobile_btn']) && $virtue_premium['show_mobile_btn'] == 1) {
  $mobilebtn = ' header .nav-trigger .nav-trigger-case {width: auto;} .nav-trigger-case .kad-menu-name {display:none;} @media (max-width: 767px) {header .nav-trigger .nav-trigger-case {width: auto; top: 0; position: absolute;} #kad-mobile-nav {margin-top:50px;}}';
} else {
  $mobilebtn = '';
}
if (isset($virtue_premium['remove_image_padding']) && $virtue_premium['remove_image_padding'] == 1) {
  $img_padding = '[class*="wp-image"] {padding:0;}';
} else {
  $img_padding = '';
}
if (isset($virtue_premium['logo_layout']) && $virtue_premium['logo_layout'] == 'logowidget') {
  $logolayoutwidget = 'header .nav-trigger .nav-trigger-case {width: 100%; position: static; display:block;}';
} else {
  $logolayoutwidget = '';
}
if (isset($virtue_premium['sitewide_action_padding']) && $virtue_premium['sitewide_action_padding'] != '20') {
  $sitewide_action_padding = '.kt-call-sitewide-to-action {padding-top:'.$virtue_premium['sitewide_action_padding'].'px; padding-bottom:'.$virtue_premium['sitewide_action_padding'].'px;}';
} else {
  $sitewide_action_padding = '';
}
if (isset($virtue_premium['mobile_header']) && $virtue_premium['mobile_header'] == '1') {
    if (isset($virtue_premium['mobile_header_tablet_show']) && $virtue_premium['mobile_header_tablet_show'] == '1') {
      $mobile_header = '@media (max-width: 992px) {#kad-banner,#kad-banner-sticky-wrapper {display: none;}#kad-mobile-banner, #kad-mobile-banner-sticky-wrapper{display:block;}}@media only screen and (device-width: 768px) {#kad-banner,#kad-banner-sticky-wrapper {display: none;}#kad-mobile-banner, #kad-mobile-banner-sticky-wrapper {display:block;}}';
    } else {
      $mobile_header = '@media (max-width: 767px) {#kad-banner,#kad-banner-sticky-wrapper{display: none;}#kad-mobile-banner, #kad-mobile-banner-sticky-wrapper{display:block;}}';
    }
} else {
  $mobile_header = '';
}
 if(isset($virtue_premium['topbar_off_mobile']) && $virtue_premium['topbar_off_mobile'] == 1) {
 $topbar_off_mobile = '@media (max-width: 767px) {#topbar {display:none;}}';
} else {
  $topbar_off_mobile = '';
}
if (isset($virtue_premium['product_quantity_input']) && $virtue_premium['product_quantity_input'] == 1) {
  $quantity_input = 'input[type=number]::-webkit-inner-spin-button, input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; } input[type=number] {-moz-appearance: textfield;}.quantity input::-webkit-outer-spin-button,.quantity input::-webkit-inner-spin-button {display: none;}';
} else {
  $quantity_input = '';
}
if ( isset( $virtue_premium[ 'header_style' ] ) && 'shrink' == $virtue_premium[ 'header_style' ] ) {
	if( isset( $virtue_premium[ 'header_height' ] ) ) {
		$header_height = $virtue_premium[ 'header_height' ];
	} else {
		$header_height = 90;
	}
	$menu_line_height = '.kad-header-style-three #nav-main ul.sf-menu > li > a {line-height:'.esc_attr( $header_height ).'px;}';
} else if ( isset( $virtue_premium[ 'header_style' ] ) && 'center' == $virtue_premium[ 'header_style' ] && isset( $virtue_premium[ 'shrink_center_header' ] ) && 1 == $virtue_premium[ 'shrink_center_header' ] ) {
	if( isset( $virtue_premium[ 'shrink_center_header_height' ] ) ) {
		$header_height = $virtue_premium[ 'shrink_center_header_height' ];
	} else {
		$header_height = 120;
	}
	$menu_line_height = '.kad-center-head-shrink .nav-main ul.sf-menu > li > a {line-height:'.esc_attr( $header_height ).'px;}';
} else {
	$menu_line_height = '';
}
if (!empty($virtue_premium['custom_css'])) {
  $custom_css = $virtue_premium['custom_css'];
} else {
  $custom_css = '';
}

$kad_custom_css = '<style type="text/css" id="kt-custom-css">'.$logo_padding_top.$logo_padding_bottom.$logo_padding_left.$logo_padding_right.$menu_margin_top.$menu_margin_bottom.$font_family.$color_iconmenubg.$color_iconmenu.$color_primary30.$color_grayfont.$color_footerfont.$sitewide_action_padding.$notavailable_placeholder_text
.$color_primary.$color_smenu_hover.$color_smenu_bg_hover.$color_mmenu_hover.$color_pmenu_hover.$color_pmenu_bg_hover.$color_mmenu_bg_hover.$contentclass.$topbarclass.$headerclass.$menuclass.$featureclass.$mobileclass.$footerclass.$boxedclass.$logolayoutwidget.$topbar_mobile.$body_color.$quantity_input.$body_color.$sidebar_side.$mobile_header.$color_mobile_header.$img_padding.$topbar_off_mobile
.$portfoliobread.$shopordering.$ptitle_uppercase.$x2logo.$ptitle_minheight.$smenu_width.$animate.$topbar_layout.$mdropdown_background.$mdropdown_font.$mdropdown_border.$scrolling_background.$centermenuwidthoutput.$show_author.$show_postedin.$show_comment.$stickymobile.$page_max_width.$show_date.$wp_image_border.$menu_layout_center.$mobilebtn.$color_mmenu_search_color.$centerstickymobile.$color_mmenu_search_color_moz.$color_mmenu_search_color_mozz.$color_mmenu_search_color_ms.$color_mmenu_search_color_kit.$menu_line_height.$custom_css.'</style>';

  echo $kad_custom_css;
}
add_action('wp_head', 'kad_custom_css');
?>
