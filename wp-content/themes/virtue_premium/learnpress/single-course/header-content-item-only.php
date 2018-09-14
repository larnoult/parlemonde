<?php
/**
 * Add signal <!-- LEARN-PRESS-REMOVE-UNWANTED-PARTS --> into footer
 * before calling footer in order to remove unwanted sections
 */
function learn_press_footer_content_item_only() {
	echo '<!-- LEARN-PRESS-REMOVE-UNWANTED-PARTS -->';
	/**
	 * Added in 2.0.5 to fix issue with some server does not
	 * output the header
	 */
	remove_action( 'wp_footer', 'learn_press_footer_content_item_only', - 1000 );
}
add_action( 'wp_footer', 'learn_press_footer_content_item_only', - 1000 );

/**
 * Add 'content-item-only' to body's classes
 *
 * @param $classes
 *
 * @return array
 */
function learn_press_footer_content_item_only_body_class( $classes ) {
	$classes[] = 'content-item-only';
	return $classes;
}
add_filter( 'body_class', 'learn_press_footer_content_item_only_body_class' );

ob_start();
do_action('get_header');
  get_template_part('templates/head');

  global $virtue_premium; 
  if(isset($virtue_premium["smooth_scrolling"]) && $virtue_premium["smooth_scrolling"] == '1') {$scrolling = '1';}  else if(isset($virtue_premium["smooth_scrolling"]) && $virtue_premium["smooth_scrolling"] == '2') { $scrolling = '2';} else {$scrolling = '0';}
  if(isset($virtue_premium["smooth_scrolling_hide"]) && $virtue_premium["smooth_scrolling_hide"] == '1') {$scrolling_hide = '1';} else {$scrolling_hide = '0';} 
  if(isset($virtue_premium['virtue_animate_in']) && $virtue_premium['virtue_animate_in'] == '1') {$animate = '1';} else {$animate = '0';}
  if(isset($virtue_premium['sticky_header']) && $virtue_premium['sticky_header'] == '1') {$sticky = '1';} else {$sticky = '0';}
  if(isset($virtue_premium['product_tabs_scroll']) && $virtue_premium['product_tabs_scroll'] == '1') {$pscroll = '1';} else {$pscroll = '0';}
  if(isset($virtue_premium['header_style'])) {$header_style = $virtue_premium['header_style'];} else {$header_style = 'standard';}
  if(isset($virtue_premium['select2_select'])) {$select2_select = $virtue_premium['select2_select'];} else {$select2_select = '1';}
  if((isset($virtue_premium['infinitescroll']) && $virtue_premium['infinitescroll'] == 1) || (isset($virtue_premium['blog_infinitescroll']) && $virtue_premium['blog_infinitescroll'] == 1) || (isset($virtue_premium['blog_cat_infinitescroll']) && $virtue_premium['blog_cat_infinitescroll'] == 1)) { $infinitescroll = 1; } else {$infinitescroll = 0;}
  ?>
<body <?php body_class(); ?> data-smooth-scrolling="<?php echo esc_attr($scrolling);?>" data-smooth-scrolling-hide="<?php echo esc_attr($scrolling_hide);?>" data-jsselect="<?php echo esc_attr($select2_select);?>" data-product-tab-scroll="<?php echo esc_attr($pscroll); ?>" data-animate="<?php echo esc_attr($animate);?>" data-sticky="<?php echo esc_attr($sticky);?>">
<?php 
$header = ob_get_clean();

// Get start tag of <body .*>
preg_match( '!(<body.*>)!', $header, $matches );

// Split and remove all section after <body />
$header_parts = preg_split( '!(<body.*>)!', $header );

// Output header with unwanted sections has removed
echo $header_parts[0] . $matches[0];