<?php
/**
 * Remove the WordPress version from RSS feeds
 */
add_filter('the_generator', '__return_false');

/**
 * Clean up language_attributes() used in <html> tag
 *
 * Change lang="en-US" to lang="en"
 * Remove dir="ltr"
 */
function kadence_language_attributes() {
  $attributes = array();
  $output = '';

  if (function_exists('is_rtl')) {
    if (is_rtl() == 'rtl') {
      $attributes[] = 'dir="rtl"';
    }
  }

  $lang = get_bloginfo('language');

  if ($lang && $lang !== 'en-US') {
    $attributes[] = "lang=\"$lang\"";
  } else {
    $attributes[] = 'lang="en"';
  }

  $output = implode(' ', $attributes);
  $output = apply_filters('kadence_language_attributes', $output);

  return $output;
}
add_filter('language_attributes', 'kadence_language_attributes');


/**
 * Add and remove body_class() classes
 */
function kadence_body_class($classes) {
  // Add post/page slug
  if (is_single() || is_page() && !is_front_page()) {
    $classes[] = basename(get_permalink());
  }

  // Remove unnecessary classes
  $home_id_class = 'page-id-' . get_option('page_on_front');
  $remove_classes = array(
    'page-template-default',
    $home_id_class
  );
  $classes = array_diff($classes, $remove_classes);

  return $classes;
}
add_filter('body_class', 'kadence_body_class');

/**
 * Wrap embedded media as suggested by Readability
 *
 * @link https://gist.github.com/965956
 * @link http://www.readability.com/publishers/guidelines#publisher
 */
function kadence_embed_wrap($cache, $url, $attr = '', $post_ID = '') {
  return '<div class="entry-content-asset videofit">' . $cache . '</div>';
}
add_filter('embed_oembed_html', 'kadence_embed_wrap', 10, 4);

/**
 * Add class="thumbnail" to attachment items
 */
function kadence_attachment_link_class($html) {
  $postid = get_the_ID();
  $html = str_replace('<a', '<a class="thumbnail"', $html);
  return $html;
}
add_filter('wp_get_attachment_link', 'kadence_attachment_link_class', 10, 1);


/**
 * Add Bootstrap thumbnail styling to images with captions
 * Use <figure> and <figcaption>
 *
 * @link http://justintadlock.com/archives/2011/07/01/captions-in-wordpress
 */
function kadence_caption($output, $attr, $content) {
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
add_filter('img_caption_shortcode', 'kadence_caption', 10, 3);



/**
 * Clean up the_excerpt()
 */
function kadence_excerpt_length($length) {
  return POST_EXCERPT_LENGTH;
}
function kad_remove_more_link_scroll( $link ) {
  $link = preg_replace( '|#more-[0-9]+|', '', $link );
  return $link;
}
add_filter( 'the_content_more_link', 'kad_remove_more_link_scroll' );
function kadence_excerpt_more($more) {
  global $virtue_premium; if(!empty($virtue_premium['post_readmore_text'])) {$readmore = $virtue_premium['post_readmore_text'];} else { $readmore =  __('Read More', 'virtue') ;}
  return ' &hellip; <a class="kt-excerpt-readmore" href="' . get_permalink() . '" aria-label="' . esc_attr(get_the_title()) . '">'. $readmore . '</a>';
}
add_filter('excerpt_length', 'kadence_excerpt_length', 999);
add_filter('excerpt_more', 'kadence_excerpt_more');

function kadence_custom_excerpt_more( $excerpt ) {
  $excerpt_more = '';
  if( has_excerpt() ) {
      global $virtue_premium; 
      if(isset($virtue_premium['custom_excerpt_readmore']) && $virtue_premium['custom_excerpt_readmore'] == 1) {
        if(!empty($virtue_premium['post_readmore_text'])) {$readmore = $virtue_premium['post_readmore_text'];} else { $readmore =  __('Read More', 'virtue') ;}
        $excerpt_more = '&hellip; <a class="kt-excerpt-readmore" href="' . get_permalink() . '" aria-label="' . esc_attr(get_the_title()) . '">'. $readmore . '</a>';
      }
  }
  return $excerpt . $excerpt_more;
}
add_filter( 'get_the_excerpt', 'kadence_custom_excerpt_more' );
/**
 * Remove unnecessary self-closing tags
 */
function kadence_remove_self_closing_tags($input) {
  return str_replace(' />', '>', $input);
}
add_filter('get_avatar',          'kadence_remove_self_closing_tags'); // <img />
add_filter('comment_id_fields',   'kadence_remove_self_closing_tags'); // <input />
add_filter('post_thumbnail_html', 'kadence_remove_self_closing_tags'); // <img />

/**
 * Don't return the default description in the RSS feed if it hasn't been changed
 */
function kadence_remove_default_description($bloginfo) {
  $default_tagline = 'Just another WordPress site';
  return ($bloginfo === $default_tagline) ? '' : $bloginfo;
}
add_filter('get_bloginfo_rss', 'kadence_remove_default_description');


/**
 * Add additional classes onto widgets
 *
 */
function kadence_widget_first_last_classes($params) {
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
add_filter('dynamic_sidebar_params', 'kadence_widget_first_last_classes');

add_action( 'media_buttons', 'kad_shortcode_button', 800 );
function kad_shortcode_button() {
	$button = '<a href="javascript:void(0);" class="virtue-generator-button button" title="'.__('Insert Shortcode','virtue').'" data-target="content">';
	$button .= '<i class="dash-icon-generic"></i> '.__('Virtue Shortcodes', 'virtue').' </a>';
	echo $button;
}
function kad_is_edit_page(){
	if ( ! is_admin() ) {
		return false;
	}
	if ( in_array( $GLOBALS['pagenow'], array( 'post.php', 'post-new.php', 'widgets.php', 'customize.php', 'post-new.php', 'edit-tags.php', 'term.php' ) ) ) {
		return true;
	} else {
		return false;
	}
}
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

