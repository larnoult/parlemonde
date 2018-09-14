<?php

/**
 * Add body_class() classes
 */
function virtue_body_class($classes) {
  // Add post/page slug
  if (is_single() || is_page() && !is_front_page()) {
    $classes[] = basename(get_permalink());
  }

  return $classes;
}
add_filter('body_class', 'virtue_body_class');

/**
 * Add class="thumbnail" to attachment items
 */
function virtue_attachment_link_class($html) {
  $postid = get_the_ID();
  $html = str_replace('<a', '<a class="thumbnail"', $html);
  return $html;
}
add_filter('wp_get_attachment_link', 'virtue_attachment_link_class', 10, 1);

/**
 * Wrap embedded media
 */
function virtue_embed_wrap($cache, $url, $attr = '', $post_ID = '') {
  	return '<div class="entry-content-asset videofit">' . $cache . '</div>';
}
add_filter('embed_oembed_html', 'virtue_embed_wrap', 10, 4);

/**
 * Add Bootstrap thumbnail styling to images with captions
 *
 * @link http://justintadlock.com/archives/2011/07/01/captions-in-wordpress
 */
function virtue_caption($output, $attr, $content) {
  if (is_feed()) {
    return $output;
  }

  $defaults = array(
    'id'      => '',
    'align'   => 'alignnone',
    'width'   => '',
    'caption' => ''
  );

  $attr = shortcode_atts($defaults, $attr);

  // If the width is less than 1 or there is no caption, return the content wrapped between the [caption] tags
  if ($attr['width'] < 1 || empty($attr['caption'])) {
    return $content;
  }

  // Set up the attributes for the caption <figure>
  $attributes  = (!empty($attr['id']) ? ' id="' . esc_attr($attr['id']) . '"' : '' );
  $attributes .= ' class="thumbnail wp-caption ' . esc_attr($attr['align']) . '"';
  $attributes .= ' style="width: ' . esc_attr($attr['width']) . 'px"';

  $output  = '<figure' . $attributes .'>';
  $output .= do_shortcode($content);
  $output .= '<figcaption class="caption wp-caption-text">' . $attr['caption'] . '</figcaption>';
  $output .= '</figure>';

  return $output;
}
add_filter('img_caption_shortcode', 'virtue_caption', 10, 3);

/**
 * Clean up the_excerpt()
 */
function virtue_excerpt_length($length) {
  return POST_EXCERPT_LENGTH;
}

function virtue_remove_more_link_scroll( $link ) {
  $link = preg_replace( '|#more-[0-9]+|', '', $link );
  return $link;
}
add_filter( 'the_content_more_link', 'virtue_remove_more_link_scroll' );

function virtue_excerpt_more($more) {
  return ' &hellip; <a href="' . get_permalink() . '">' . __('Continued', 'virtue') . '</a>';
}
add_filter('excerpt_length', 'virtue_excerpt_length');
add_filter('excerpt_more', 'virtue_excerpt_more');

/**
 * Add additional classes onto widgets
 *
 * @link http://wordpress.org/support/topic/how-to-first-and-last-css-classes-for-sidebar-widgets
 */
function virtue_widget_first_last_classes($params) {
  global $my_widget_num;

  $this_id = $params[0]['id'];
  $arr_registered_widgets = wp_get_sidebars_widgets();

  if (!$my_widget_num) {
    $my_widget_num = array();
  }

  if (!isset($arr_registered_widgets[$this_id]) || !is_array($arr_registered_widgets[$this_id])) {
    return $params;
  }

  if (isset($my_widget_num[$this_id])) {
    $my_widget_num[$this_id] ++;
  } else {
    $my_widget_num[$this_id] = 1;
  }

  $class = 'class="widget-' . $my_widget_num[$this_id] . ' ';

  if ($my_widget_num[$this_id] == 1) {
    $class .= 'widget-first ';
  } elseif ($my_widget_num[$this_id] == count($arr_registered_widgets[$this_id])) {
    $class .= 'widget-last ';
  }

  $params[0]['before_widget'] = preg_replace('/class=\"/', "$class", $params[0]['before_widget'], 1);

  return $params;
}
add_filter('dynamic_sidebar_params', 'virtue_widget_first_last_classes');
/**
 * Remove hentry class from portfolio posts
 */
function virtue_portfolio_remove_hentry( $classes ) {
	if ( is_singular('portfolio') ) {
		$classes = array_diff( $classes, array( 'hentry' ) );
	}
	return $classes;
}
add_filter( 'post_class', 'virtue_portfolio_remove_hentry' );
