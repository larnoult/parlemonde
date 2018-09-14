<?php
/**
 * Widget functions
 *
 * @package Virtue Theme
 */

// load required widgets.
require_once trailingslashit( get_template_directory() ) . 'lib/widgets/class-kad-tabs-content-widget.php';
require_once trailingslashit( get_template_directory() ) . 'lib/widgets/class-kad-image-widget.php';
require_once trailingslashit( get_template_directory() ) . 'lib/widgets/class-kad-call-to-action-widget.php';
require_once trailingslashit( get_template_directory() ) . 'lib/widgets/class-kad-carousel-widget.php';
require_once trailingslashit( get_template_directory() ) . 'lib/widgets/class-kad-imgmenu-widget.php';
require_once trailingslashit( get_template_directory() ) . 'lib/widgets/class-kad-split-content-widget.php';
require_once trailingslashit( get_template_directory() ) . 'lib/widgets/class-kad-gmap-widget.php';
require_once trailingslashit( get_template_directory() ) . 'lib/widgets/class-kad-gallery-widget.php';

require_once trailingslashit( get_template_directory() ) . 'lib/post-type-testimonial/class-kadence-testimonial-slider-widget.php';

/**
 * Get list of sidebars
 */
function virtue_sidebar_list() {
	$all_sidebars = array( array('name' => __( 'Primary Sidebar', 'virtue' ), 'id' => 'sidebar-primary' ) );
	global $virtue_premium;
	if ( isset( $virtue_premium['cust_sidebars'] ) ) {
		if ( is_array( $virtue_premium['cust_sidebars'] ) ) {
			$i = 1;
			foreach ( $virtue_premium['cust_sidebars'] as $sidebar ) {
				if ( empty( $sidebar ) ) {
					$sidebar = 'sidebar' . $i;
				}
				$all_sidebars[] = array('name' => $sidebar, 'id' => 'sidebar' . $i );
				$i++;
			}
		}
	}
	global $vir_sidebars;
	$vir_sidebars = $all_sidebars;
	return $all_sidebars;
}
add_action( 'init', 'virtue_sidebar_list' );

function virtue_register_sidebars(){
  $the_sidebars = virtue_sidebar_list();
  if (function_exists('register_sidebar')){
    foreach($the_sidebars as $side){
      virtue_register_sidebar($side['name'], $side['id']);    
    }

  }
}
function virtue_register_sidebar($name, $id){
  register_sidebar(array('name'=>$name,
    'id' => $id,
         'before_widget' => '<section id="%1$s" class="widget %2$s"><div class="widget-inner">',
    'after_widget' => '</div></section>',
    'before_title' => '<h3>',
    'after_title' => '</h3>',
  ));
}
add_action('widgets_init', 'virtue_register_sidebars');

function kadence_widgets_init() {
    //Topbar 
  if(kadence_display_topbar_widget()) {
  register_sidebar(array(
    'name'          => __('Topbar Widget', 'virtue'),
    'id'            => 'topbarright',
    'before_widget' => '<div class="topbar-widgetcontent topbar-widgetcontain">',
    'after_widget'  => '</div>',
    'before_title'  => '<div class="topbar-widgettitle topbar-widgettitle-wrap">',
    'after_title'   => '</div>',
  ));
}
    //header
  global $virtue_premium; if(isset($virtue_premium['logo_layout']) && $virtue_premium['logo_layout'] == 'logowidget') {
  register_sidebar(array(
    'name'          => __('Header Widget Area', 'virtue'),
    'id'            => 'headerwidget',
    'before_widget' => '<div class="header-widget-area-header %2$s">',
    'after_widget'  => '</div>',
    'before_title'  => '<span class="header-widget-title">',
    'after_title'   => '</span>',
  ));
}
  // Sidebars
  register_sidebar(array(
    'name'          => __('Primary Sidebar', 'virtue'),
    'id'            => 'sidebar-primary',
    'before_widget' => '<section id="%1$s" class="widget %2$s"><div class="widget-inner">',
    'after_widget'  => '</div></section>',
    'before_title'  => '<h3>',
    'after_title'   => '</h3>',
  ));
  register_sidebar(array(
    'name'          => __('Home Widget Area', 'virtue'),
    'id'            => 'homewidget',
    'before_widget' => '<div class="home-widget-area-widget %2$s">',
    'after_widget'  => '</div>',
    'before_title'  => '<h3>',
    'after_title'   => '</h3>',
  ));

  // Footer
  global $virtue_premium; if(isset($virtue_premium['footer_layout'])) { $footer_layout = $virtue_premium['footer_layout'];} else {$footer_layout = "fourc";}
  if ($footer_layout == "fourc" || $footer_layout == "four_single") {
    if ( function_exists('register_sidebar') )
      register_sidebar(array(
        'name' => __('Footer Column 1', 'virtue'),
        'id' => 'footer_1',
        'before_widget' => '<div class="footer-widget widget"><aside id="%1$s" class="%2$s">',
        'after_widget' => '</aside></div>',
        'before_title' => '<h3>',
        'after_title' => '</h3>',
      )
    );
    if ( function_exists('register_sidebar') )
      register_sidebar(array(
        'name' => __('Footer Column 2', 'virtue'),
        'id' => 'footer_2',
        'before_widget' => '<div class="footer-widget widget"><aside id="%1$s" class="%2$s">',
        'after_widget' => '</aside></div>',
        'before_title' => '<h3>',
        'after_title' => '</h3>',
      )
    );
    if ( function_exists('register_sidebar') )
      register_sidebar(array(
        'name' => __('Footer Column 3', 'virtue'),
        'id' => 'footer_3',
        'before_widget' => '<div class="footer-widget widget"><aside id="%1$s" class="%2$s">',
        'after_widget' => '</aside></div>',
        'before_title' => '<h3>',
        'after_title' => '</h3>',
      )
    );
    if ( function_exists('register_sidebar') )
      register_sidebar(array(
        'name' => __('Footer Column 4', 'virtue'),
        'id' => 'footer_4',
        'before_widget' => '<div class="footer-widget widget"><aside id="%1$s" class="%2$s">',
        'after_widget' => '</aside></div>',
        'before_title' => '<h3>',
        'after_title' => '</h3>',
      )
    );
  } else if ($footer_layout == "threec" || $footer_layout == "three_single") {
    if ( function_exists('register_sidebar') )
      register_sidebar(array(
        'name' => __('Footer Column 1', 'virtue'),
        'id' => 'footer_third_1',
        'before_widget' => '<div class="footer-widget widget"><aside id="%1$s" class="%2$s">',
        'after_widget' => '</aside></div>',
        'before_title' => '<h3>',
        'after_title' => '</h3>',
      )
    );
    if ( function_exists('register_sidebar') )
      register_sidebar(array(
        'name' => __('Footer Column 2', 'virtue'),
        'id' => 'footer_third_2',
        'before_widget' => '<div class="footer-widget widget"><aside id="%1$s" class="%2$s">',
        'after_widget' => '</aside></div>',
        'before_title' => '<h3>',
        'after_title' => '</h3>',
      )
    );
    if ( function_exists('register_sidebar') )
      register_sidebar(array(
        'name' => __('Footer Column 3', 'virtue'),
        'id' => 'footer_third_3',
        'before_widget' => '<div class="footer-widget widget"><aside id="%1$s" class="%2$s">',
        'after_widget' => '</aside></div>',
        'before_title' => '<h3>',
        'after_title' => '</h3>',
      )
    );
  } else {
      if ( function_exists('register_sidebar') )
        register_sidebar(array(
          'name' => __('Footer Column 1', 'virtue'),
          'id' => 'footer_double_1',
          'before_widget' => '<div class="footer-widget widget"><aside id="%1$s" class="%2$s">',
          'after_widget' => '</aside></div>',
          'before_title' => '<h3>',
          'after_title' => '</h3>',
        )
      );
      if ( function_exists('register_sidebar') )
        register_sidebar(array(
          'name' => __('Footer Column 2', 'virtue'),
          'id' => 'footer_double_2',
          'before_widget' => '<div class="footer-widget widget"><aside id="%1$s" class="%2$s">',
          'after_widget' => '</aside></div>',
          'before_title' => '<h3>',
          'after_title' => '</h3>',
        )
      );
    }

  // Widgets
	register_widget('Kadence_Contact_Widget');
	register_widget('Kadence_Social_Widget');
	register_widget('Kadence_Recent_Posts_Widget');
	register_widget('Kadence_Testimonial_Slider_Widget');
	register_widget('Kadence_Image_Grid_Widget');
	register_widget('Simple_About_With_Image');
	register_widget('kad_gallery_widget');
	register_widget('kad_carousel_widget');
	register_widget('kad_infobox_widget');
	register_widget('kad_gmap_widget');
	register_widget('kad_calltoaction_widget');
	register_widget('kad_imgmenu_widget');
	register_widget('kad_split_content_widget');
	register_widget('kad_icon_flip_box_widget');
	
  	if ( defined( 'SITEORIGIN_PANELS_VERSION' ) ) {
		register_widget('kad_tabs_content_widget');
	}
}
add_action('widgets_init', 'kadence_widgets_init');

/**
 * Contact widget
 */
class Kadence_Contact_Widget extends WP_Widget {
  private static $instance = 0;
    public function __construct() {
    $widget_ops = array('classname' => 'widget_kadence_contact', 'description' => __('Use this widget to add a Vcard to your site', 'virtue'));
    parent::__construct('widget_kadence_contact', __('Virtue: Contact/Vcard', 'virtue'), $widget_ops);
  }

  public function widget($args, $instance) {

    if (!isset($args['widget_id'])) {
      $args['widget_id'] = null;
    }

    extract($args);

    $title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
    $company = empty($instance['company']) ? ' ' : apply_filters('widget_text', $instance['company']);
    if (!isset($instance['name'])) { $instance['name'] = ''; }
    if (!isset($instance['street_address'])) { $instance['street_address'] = ''; }
    if (!isset($instance['locality'])) { $instance['locality'] = ''; }
    if (!isset($instance['region'])) { $instance['region'] = ''; }
    if (!isset($instance['postal_code'])) { $instance['postal_code'] = ''; }
    if (!isset($instance['tel'])) { $instance['tel'] = ''; }
    if (!isset($instance['fixedtel'])) { $instance['fixedtel'] = ''; }
    if (!isset($instance['email'])) { $instance['email'] = ''; }

    echo $before_widget;
    if ($title) {
      echo $before_title;
      echo $title;
      echo $after_title;
    }
  ?>
    <div class="vcard">
      
      <?php if(!empty($instance['company'])):?><h5 class="vcard-company"><i class="icon-office"></i><?php echo $company; ?></h5><?php endif;?>
      <?php if(!empty($instance['name'])):?><p class="vcard-name fn"><i class="icon-user2"></i><?php echo $instance['name']; ?></p><?php endif;?>
      <?php if(!empty($instance['street_address']) || !empty($instance['locality']) || !empty($instance['region']) ):?>
        <p class="vcard-address"><i class="icon-location"></i><?php echo $instance['street_address']; ?>
       <span><?php echo $instance['locality']; ?> <?php echo $instance['region']; ?> <?php echo $instance['postal_code']; ?></span></p>
     <?php endif;?>
      <?php if(!empty($instance['tel'])):?><p class="tel"><i class="icon-mobile"></i><?php echo $instance['tel']; ?></p><?php endif;?>
      <?php if(!empty($instance['fixedtel'])):?><p class="tel fixedtel"><i class="icon-phone"></i><?php echo $instance['fixedtel']; ?></p><?php endif;?>
      <?php if(!empty($instance['email'])):?><p><a class="email" href="mailto:<?php echo antispambot($instance['email']);?>"><i class="icon-envelope"></i><?php echo antispambot($instance['email']); ?></a></p> <?php endif;?>
    </div>
      <?php
    echo $after_widget;

  }

  public function update($new_instance, $old_instance) {
    $instance = $old_instance;
    $instance['title'] = strip_tags($new_instance['title']);
  $instance['company'] = strip_tags($new_instance['company']);
  $instance['name'] = strip_tags($new_instance['name']);
    $instance['street_address'] = strip_tags($new_instance['street_address']);
    $instance['locality'] = strip_tags($new_instance['locality']);
    $instance['region'] = strip_tags($new_instance['region']);
    $instance['postal_code'] = strip_tags($new_instance['postal_code']);
    $instance['tel'] = strip_tags($new_instance['tel']);
    $instance['fixedtel'] = strip_tags($new_instance['fixedtel']);
    $instance['email'] = strip_tags($new_instance['email']);

    return $instance;
  }

  public function form($instance) {
    $title = isset($instance['title']) ? esc_attr($instance['title']) : '';
    $company = isset($instance['company']) ? esc_attr($instance['company']) : '';
  $name = isset($instance['name']) ? esc_attr($instance['name']) : '';
  $street_address = isset($instance['street_address']) ? esc_attr($instance['street_address']) : '';
    $locality = isset($instance['locality']) ? esc_attr($instance['locality']) : '';
    $region = isset($instance['region']) ? esc_attr($instance['region']) : '';
    $postal_code = isset($instance['postal_code']) ? esc_attr($instance['postal_code']) : '';
    $tel = isset($instance['tel']) ? esc_attr($instance['tel']) : '';
    $fixedtel = isset($instance['fixedtel']) ? esc_attr($instance['fixedtel']) : '';
    $email = isset($instance['email']) ? esc_attr($instance['email']) : '';
  ?>
    <p>
      <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Title:', 'virtue'); ?></label>
      <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
    </p>
    <p>
      <label for="<?php echo esc_attr($this->get_field_id('company')); ?>"><?php _e('Company Name:', 'virtue'); ?></label>
      <input class="widefat" id="<?php echo esc_attr($this->get_field_id('company')); ?>" name="<?php echo esc_attr($this->get_field_name('company')); ?>" type="text" value="<?php echo esc_attr($company); ?>" />
    </p>
    <p>
      <label for="<?php echo esc_attr($this->get_field_id('name')); ?>"><?php _e('Name:', 'virtue'); ?></label>
      <input class="widefat" id="<?php echo esc_attr($this->get_field_id('name')); ?>" name="<?php echo esc_attr($this->get_field_name('name')); ?>" type="text" value="<?php echo esc_attr($name); ?>" />
    </p>
    <p>
      <label for="<?php echo esc_attr($this->get_field_id('street_address')); ?>"><?php _e('Street Address:', 'virtue'); ?></label>
      <input class="widefat" id="<?php echo esc_attr($this->get_field_id('street_address')); ?>" name="<?php echo esc_attr($this->get_field_name('street_address')); ?>" type="text" value="<?php echo esc_attr($street_address); ?>" />
    </p>
    <p>
      <label for="<?php echo esc_attr($this->get_field_id('locality')); ?>"><?php _e('City/Locality:', 'virtue'); ?></label>
      <input class="widefat" id="<?php echo esc_attr($this->get_field_id('locality')); ?>" name="<?php echo esc_attr($this->get_field_name('locality')); ?>" type="text" value="<?php echo esc_attr($locality); ?>" />
    </p>
    <p>
      <label for="<?php echo esc_attr($this->get_field_id('region')); ?>"><?php _e('State/Region:', 'virtue'); ?></label>
      <input class="widefat" id="<?php echo esc_attr($this->get_field_id('region')); ?>" name="<?php echo esc_attr($this->get_field_name('region')); ?>" type="text" value="<?php echo esc_attr($region); ?>" />
    </p>
    <p>
      <label for="<?php echo esc_attr($this->get_field_id('postal_code')); ?>"><?php _e('Zipcode/Postal Code:', 'virtue'); ?></label>
      <input class="widefat" id="<?php echo esc_attr($this->get_field_id('postal_code')); ?>" name="<?php echo esc_attr($this->get_field_name('postal_code')); ?>" type="text" value="<?php echo esc_attr($postal_code); ?>" />
    </p>
    <p>
      <label for="<?php echo esc_attr($this->get_field_id('tel')); ?>"><?php _e('Mobile Telephone:', 'virtue'); ?></label>
      <input class="widefat" id="<?php echo esc_attr($this->get_field_id('tel')); ?>" name="<?php echo esc_attr($this->get_field_name('tel')); ?>" type="text" value="<?php echo esc_attr($tel); ?>" />
    </p>
    <p>
      <label for="<?php echo esc_attr($this->get_field_id('fixedtel')); ?>"><?php _e('Fixed Telephone:', 'virtue'); ?></label>
      <input class="widefat" id="<?php echo esc_attr($this->get_field_id('fixedtel')); ?>" name="<?php echo esc_attr($this->get_field_name('fixedtel')); ?>" type="text" value="<?php echo esc_attr($fixedtel); ?>" />
    </p>
    <p>
      <label for="<?php echo esc_attr($this->get_field_id('email')); ?>"><?php _e('Email:', 'virtue'); ?></label>
      <input class="widefat" id="<?php echo esc_attr($this->get_field_id('email')); ?>" name="<?php echo esc_attr($this->get_field_name('email')); ?>" type="text" value="<?php echo esc_attr($email); ?>" />
    </p>
  <?php
  }
}
/**
 * Social widget
 */
class Kadence_Social_Widget extends WP_Widget {
  private static $instance = 0;
    public function __construct() {
    $widget_ops = array('classname' => 'widget_kadence_social', 'description' => __('Simple way to add Social Icons', 'virtue'));
    parent::__construct('widget_kadence_social', __('Virtue: Social Links', 'virtue'), $widget_ops);
  }

  function widget($args, $instance) {

    if (!isset($args['widget_id'])) {
      $args['widget_id'] = null;
    }
    extract($args);

    $title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
    if (!isset($instance['facebook'])) { $instance['facebook'] = ''; }
    if (!isset($instance['twitter'])) { $instance['twitter'] = ''; }
    if (!isset($instance['instagram'])) { $instance['instagram'] = ''; }
    if (!isset($instance['googleplus'])) { $instance['googleplus'] = ''; }
    if (!isset($instance['flickr'])) { $instance['flickr'] = ''; }
    if (!isset($instance['vimeo'])) { $instance['vimeo'] = ''; }
    if (!isset($instance['youtube'])) { $instance['youtube'] = ''; }
    if (!isset($instance['pinterest'])) { $instance['pinterest'] = ''; }
    if (!isset($instance['dribbble'])) { $instance['dribbble'] = ''; }
    if (!isset($instance['linkedin'])) { $instance['linkedin'] = ''; }
    if (!isset($instance['tumblr'])) { $instance['tumblr'] = ''; }
    if (!isset($instance['stumbleupon'])) { $instance['stumbleupon'] = ''; }
    if (!isset($instance['vk'])) { $instance['vk'] = ''; }
    if (!isset($instance['viadeo'])) { $instance['viadeo'] = ''; }
    if (!isset($instance['xing'])) { $instance['xing'] = ''; }
    if (!isset($instance['yelp'])) { $instance['yelp'] = ''; }
    if (!isset($instance['soundcloud'])) { $instance['soundcloud'] = ''; }
    if (!isset($instance['snapchat'])) { $instance['snapchat'] = ''; }
    if (!isset($instance['periscope'])) { $instance['periscope'] = ''; }
    if (!isset($instance['behance'])) { $instance['behance'] = ''; }

    if (!isset($instance['rss'])) { $instance['rss'] = ''; }

    echo $before_widget;
    if ($title) {
      echo $before_title;
      echo $title;
      echo $after_title;
    }
  ?>
    <div class="virtue_social_widget clearfix">
      
<?php if(!empty($instance['facebook'])):?><a href="<?php echo esc_url($instance['facebook']); ?>" class="facebook_link" title="Facebook" target="_blank" data-toggle="tooltip" data-placement="top" data-original-title="Facebook"><i class="icon-facebook"></i></a><?php endif;?>
<?php if(!empty($instance['twitter'])):?><a href="<?php echo esc_url($instance['twitter']); ?>" class="twitter_link" title="Twitter" target="_blank" data-toggle="tooltip" data-placement="top" data-original-title="Twitter"><i class="icon-twitter"></i></a><?php endif;?>
<?php if(!empty($instance['instagram'])):?><a href="<?php echo esc_url($instance['instagram']); ?>" class="instagram_link" title="Instagram" target="_blank" data-toggle="tooltip" data-placement="top" data-original-title="Instagram"><i class="icon-instagram"></i></a><?php endif;?>
<?php if(!empty($instance['googleplus'])):?><a href="<?php echo esc_url($instance['googleplus']); ?>" class="googleplus_link" title="GooglePlus" target="_blank" data-toggle="tooltip" data-placement="top" data-original-title="GooglePlus" rel="publisher"><i class="icon-google-plus4"></i></a><?php endif;?>
<?php if(!empty($instance['flickr'])):?><a href="<?php echo esc_url($instance['flickr']); ?>" class="flickr_link" title="Flickr" data-toggle="tooltip" target="_blank" data-placement="top" data-original-title="Flickr"><i class="icon-flickr"></i></a><?php endif;?>
<?php if(!empty($instance['vimeo'])):?><a href="<?php echo esc_url($instance['vimeo']); ?>" class="vimeo_link" title="Vimeo" target="_blank" data-toggle="tooltip" data-placement="top" data-original-title="Vimeo"><i class="icon-vimeo"></i></a><?php endif;?>
<?php if(!empty($instance['youtube'])):?><a href="<?php echo esc_url($instance['youtube']); ?>" class="youtube_link" title="YouTube" target="_blank" data-toggle="tooltip" data-placement="top" data-original-title="YouTube"><i class="icon-youtube"></i></a><?php endif;?>
<?php if(!empty($instance['pinterest'])):?><a href="<?php echo esc_url($instance['pinterest']); ?>" class="pinterest_link" title="Pinterest" target="_blank" data-toggle="tooltip" data-placement="top" data-original-title="Pinterest"><i class="icon-pinterest"></i></a><?php endif;?>
<?php if(!empty($instance['dribbble'])):?><a href="<?php echo esc_url($instance['dribbble']); ?>" class="dribbble_link" title="Dribbble" target="_blank" data-toggle="tooltip" data-placement="top" data-original-title="Dribbble"><i class="icon-dribbble"></i></a><?php endif;?>
<?php if(!empty($instance['linkedin'])):?><a href="<?php echo esc_url($instance['linkedin']); ?>" class="linkedin_link" title="LinkedIn" target="_blank" data-toggle="tooltip" data-placement="top" data-original-title="LinkedIn"><i class="icon-linkedin"></i></a><?php endif;?>
<?php if(!empty($instance['tumblr'])):?><a href="<?php echo esc_url($instance['tumblr']); ?>" class="tumblr_link" title="Tumblr" target="_blank" data-toggle="tooltip" data-placement="top" data-original-title="Tumblr"><i class="icon-tumblr"></i></a><?php endif;?>
<?php if(!empty($instance['stumbleupon'])):?><a href="<?php echo esc_url($instance['stumbleupon']); ?>" class="stumbleupon_link" title="StumbleUpon" target="_blank" data-toggle="tooltip" data-placement="top" data-original-title="StumbleUpon"><i class="icon-stumbleupon"></i></a><?php endif;?>
<?php if(!empty($instance['vk'])):?><a href="<?php echo esc_url($instance['vk']); ?>" class="vk_link" title="VK" target="_blank" data-toggle="tooltip" data-placement="top" data-original-title="VK"><i class="icon-vk"></i></a><?php endif;?>
<?php if(!empty($instance['viadeo'])):?><a href="<?php echo esc_url($instance['viadeo']); ?>" class="viadeo_link" title="Viadeo" target="_blank" data-toggle="tooltip" data-placement="top" data-original-title="Viadeo"><i class="icon-viadeo"></i></a><?php endif;?>
<?php if(!empty($instance['xing'])):?><a href="<?php echo esc_url($instance['xing']); ?>" class="xing_link" title="Xing" target="_blank" data-toggle="tooltip" data-placement="top" data-original-title="Xing"><i class="icon-xing"></i></a><?php endif;?>
<?php if(!empty($instance['yelp'])):?><a href="<?php echo esc_url($instance['yelp']); ?>" class="yelp_link" title="Yelp" target="_blank" data-toggle="tooltip" data-placement="top" data-original-title="Yelp"><i class="icon-yelp"></i></a><?php endif;?>
<?php if(!empty($instance['soundcloud'])):?><a href="<?php echo esc_url($instance['soundcloud']); ?>" class="soundcloud_link" title="Soundcloud" target="_blank" data-toggle="tooltip" data-placement="top" data-original-title="Soundcloud"><i class="icon-soundcloud"></i></a><?php endif;?>
<?php if(!empty($instance['snapchat'])):?><a href="<?php echo esc_url($instance['snapchat']); ?>" class="snapchat_link" title="Snapchat" target="_blank" data-toggle="tooltip" data-placement="top" data-original-title="Snapchat"><i class="icon-fa-snapchat"></i></a><?php endif;?>
<?php if(!empty($instance['periscope'])):?><a href="<?php echo esc_url($instance['periscope']); ?>" class="periscope_link" title="Periscope" target="_blank" data-toggle="tooltip" data-placement="top" data-original-title="Periscope"><i class="icon-iconPeriscope"></i></a><?php endif;?>
<?php if(!empty($instance['behance'])):?><a href="<?php echo esc_url($instance['behance']); ?>" class="behance_link" title="Behance" target="_blank" data-toggle="tooltip" data-placement="top" data-original-title="Behance"><i class="icon-behance"></i></a><?php endif;?>
<?php if(!empty($instance['rss'])):?><a href="<?php echo esc_url($instance['rss']); ?>" class="rss_link" title="RSS" target="_blank" data-toggle="tooltip" data-placement="top" data-original-title="RSS"><i class="icon-feed"></i></a><?php endif;?>
    </div>
  <?php
    echo $after_widget;

  }

  public function update($new_instance, $old_instance) {
    $instance = $old_instance;
     $instance['title'] = strip_tags($new_instance['title']);
    $instance['facebook'] = strip_tags($new_instance['facebook']);
    $instance['twitter'] = strip_tags($new_instance['twitter']);
    $instance['instagram'] = strip_tags($new_instance['instagram']);
    $instance['googleplus'] = strip_tags($new_instance['googleplus']);
    $instance['flickr'] = strip_tags($new_instance['flickr']);
    $instance['vimeo'] = strip_tags($new_instance['vimeo']);
    $instance['youtube'] = strip_tags($new_instance['youtube']);
    $instance['pinterest'] = strip_tags($new_instance['pinterest']);
    $instance['dribbble'] = strip_tags($new_instance['dribbble']);
    $instance['linkedin'] = strip_tags($new_instance['linkedin']);
    $instance['tumblr'] = strip_tags($new_instance['tumblr']);
    $instance['stumbleupon'] = strip_tags($new_instance['stumbleupon']);
    $instance['vk'] = strip_tags($new_instance['vk']);
    $instance['viadeo'] = strip_tags($new_instance['viadeo']);
    $instance['xing'] = strip_tags($new_instance['xing']);
    $instance['yelp'] = strip_tags($new_instance['yelp']);
    $instance['soundcloud'] = strip_tags($new_instance['soundcloud']);
    $instance['snapchat'] = strip_tags($new_instance['snapchat']);
    $instance['periscope'] = strip_tags($new_instance['periscope']);
    $instance['behance'] = strip_tags($new_instance['behance']);
    $instance['rss'] = strip_tags($new_instance['rss']);

    return $instance;
  }

  public function form($instance) {
    $title = isset($instance['title']) ? esc_attr($instance['title']) : '';
    $facebook = isset($instance['facebook']) ? esc_attr($instance['facebook']) : '';
    $twitter = isset($instance['twitter']) ? esc_attr($instance['twitter']) : '';
    $instagram = isset($instance['instagram']) ? esc_attr($instance['instagram']) : '';
    $googleplus = isset($instance['googleplus']) ? esc_attr($instance['googleplus']) : '';
    $flickr = isset($instance['flickr']) ? esc_attr($instance['flickr']) : '';
    $vimeo = isset($instance['vimeo']) ? esc_attr($instance['vimeo']) : '';
    $youtube = isset($instance['youtube']) ? esc_attr($instance['youtube']) : '';
    $pinterest = isset($instance['pinterest']) ? esc_attr($instance['pinterest']) : '';
    $dribbble = isset($instance['dribbble']) ? esc_attr($instance['dribbble']) : '';
    $linkedin = isset($instance['linkedin']) ? esc_attr($instance['linkedin']) : '';
    $tumblr = isset($instance['tumblr']) ? esc_attr($instance['tumblr']) : '';
    $stumbleupon = isset($instance['stumbleupon']) ? esc_attr($instance['stumbleupon']) : '';
    $vk = isset($instance['vk']) ? esc_attr($instance['vk']) : '';
    $viadeo = isset($instance['viadeo']) ? esc_attr($instance['viadeo']) : '';
    $xing = isset($instance['xing']) ? esc_attr($instance['xing']) : '';
    $yelp = isset($instance['yelp']) ? esc_attr($instance['yelp']) : '';
    $soundcloud = isset($instance['soundcloud']) ? esc_attr($instance['soundcloud']) : '';
    $snapchat = isset($instance['snapchat']) ? esc_attr($instance['snapchat']) : '';
    $periscope = isset($instance['periscope']) ? esc_attr($instance['periscope']) : '';
    $behance = isset($instance['behance']) ? esc_attr($instance['behance']) : '';
    $rss = isset($instance['rss']) ? esc_attr($instance['rss']) : '';
  ?>
  <p>
      <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Title:', 'virtue'); ?></label>
      <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
    </p>
    <p>
      <label for="<?php echo esc_attr($this->get_field_id('facebook')); ?>"><?php _e('Facebook:', 'virtue'); ?></label>
      <input class="widefat" id="<?php echo esc_attr($this->get_field_id('facebook')); ?>" name="<?php echo esc_attr($this->get_field_name('facebook')); ?>" type="text" value="<?php echo esc_attr($facebook); ?>" />
    </p>
    <p>
      <label for="<?php echo esc_attr($this->get_field_id('twitter')); ?>"><?php _e('Twitter:', 'virtue'); ?></label>
      <input class="widefat" id="<?php echo esc_attr($this->get_field_id('twitter')); ?>" name="<?php echo esc_attr($this->get_field_name('twitter')); ?>" type="text" value="<?php echo esc_attr($twitter); ?>" />
    </p>
    <p>
      <label for="<?php echo esc_attr($this->get_field_id('instagram')); ?>"><?php _e('Instagram:', 'virtue'); ?></label>
      <input class="widefat" id="<?php echo esc_attr($this->get_field_id('instagram')); ?>" name="<?php echo esc_attr($this->get_field_name('instagram')); ?>" type="text" value="<?php echo esc_attr($instagram); ?>" />
    </p>
    <p>
      <label for="<?php echo esc_attr($this->get_field_id('googleplus')); ?>"><?php _e('GooglePlus:', 'virtue'); ?></label>
      <input class="widefat" id="<?php echo esc_attr($this->get_field_id('googleplus')); ?>" name="<?php echo esc_attr($this->get_field_name('googleplus')); ?>" type="text" value="<?php echo esc_attr($googleplus); ?>" />
    </p>
    <p>
      <label for="<?php echo esc_attr($this->get_field_id('flickr')); ?>"><?php _e('Flickr:', 'virtue'); ?></label>
      <input class="widefat" id="<?php echo esc_attr($this->get_field_id('flickr')); ?>" name="<?php echo esc_attr($this->get_field_name('flickr')); ?>" type="text" value="<?php echo esc_attr($flickr); ?>" />
    </p>
    <p>
      <label for="<?php echo esc_attr($this->get_field_id('vimeo')); ?>"><?php _e('Vimeo:', 'virtue'); ?></label>
      <input class="widefat" id="<?php echo esc_attr($this->get_field_id('vimeo')); ?>" name="<?php echo esc_attr($this->get_field_name('vimeo')); ?>" type="text" value="<?php echo esc_attr($vimeo); ?>" />
    </p>
    <p>
      <label for="<?php echo esc_attr($this->get_field_id('youtube')); ?>"><?php _e('Youtube:', 'virtue'); ?></label>
      <input class="widefat" id="<?php echo esc_attr($this->get_field_id('youtube')); ?>" name="<?php echo esc_attr($this->get_field_name('youtube')); ?>" type="text" value="<?php echo esc_attr($youtube); ?>" />
    </p>
    <p>
      <label for="<?php echo esc_attr($this->get_field_id('pinterest')); ?>"><?php _e('Pinterest:', 'virtue'); ?></label>
      <input class="widefat" id="<?php echo esc_attr($this->get_field_id('pinterest')); ?>" name="<?php echo esc_attr($this->get_field_name('pinterest')); ?>" type="text" value="<?php echo esc_attr($pinterest); ?>" />
    </p>
    <p>
      <label for="<?php echo esc_attr($this->get_field_id('dribbble')); ?>"><?php _e('Dribbble:', 'virtue'); ?></label>
      <input class="widefat" id="<?php echo esc_attr($this->get_field_id('dribbble')); ?>" name="<?php echo esc_attr($this->get_field_name('dribbble')); ?>" type="text" value="<?php echo esc_attr($dribbble); ?>" />
    </p>
    <p>
      <label for="<?php echo esc_attr($this->get_field_id('linkedin')); ?>"><?php _e('Linkedin:', 'virtue'); ?></label>
      <input class="widefat" id="<?php echo esc_attr($this->get_field_id('linkedin')); ?>" name="<?php echo esc_attr($this->get_field_name('linkedin')); ?>" type="text" value="<?php echo esc_attr($linkedin); ?>" />
    </p>
    <p>
      <label for="<?php echo esc_attr($this->get_field_id('tumblr')); ?>"><?php _e('Tumblr:', 'virtue'); ?></label>
      <input class="widefat" id="<?php echo esc_attr($this->get_field_id('tumblr')); ?>" name="<?php echo esc_attr($this->get_field_name('tumblr')); ?>" type="text" value="<?php echo esc_attr($tumblr); ?>" />
    </p>
    <p>
      <label for="<?php echo esc_attr($this->get_field_id('stumbleupon')); ?>"><?php _e('Stumbleupon:', 'virtue'); ?></label>
      <input class="widefat" id="<?php echo esc_attr($this->get_field_id('stumbleupon')); ?>" name="<?php echo esc_attr($this->get_field_name('stumbleupon')); ?>" type="text" value="<?php echo esc_attr($stumbleupon); ?>" />
    </p>
    <p>
      <label for="<?php echo esc_attr($this->get_field_id('vk')); ?>"><?php _e('VK:', 'virtue'); ?></label>
      <input class="widefat" id="<?php echo esc_attr($this->get_field_id('vk')); ?>" name="<?php echo esc_attr($this->get_field_name('vk')); ?>" type="text" value="<?php echo esc_attr($vk); ?>" />
    </p>
    <p>
      <label for="<?php echo esc_attr($this->get_field_id('viadeo')); ?>"><?php _e('Viadeo:', 'virtue'); ?></label>
      <input class="widefat" id="<?php echo esc_attr($this->get_field_id('viadeo')); ?>" name="<?php echo esc_attr($this->get_field_name('viadeo')); ?>" type="text" value="<?php echo esc_attr($viadeo); ?>" />
    </p>
    <p>
      <label for="<?php echo esc_attr($this->get_field_id('xing')); ?>"><?php _e('xing:', 'virtue'); ?></label>
      <input class="widefat" id="<?php echo esc_attr($this->get_field_id('xing')); ?>" name="<?php echo esc_attr($this->get_field_name('xing')); ?>" type="text" value="<?php echo esc_attr($xing); ?>" />
    </p>
    <p>
      <label for="<?php echo esc_attr($this->get_field_id('yelp')); ?>"><?php _e('Yelp:', 'virtue'); ?></label>
      <input class="widefat" id="<?php echo esc_attr($this->get_field_id('yelp')); ?>" name="<?php echo esc_attr($this->get_field_name('yelp')); ?>" type="text" value="<?php echo esc_attr($yelp); ?>" />
    </p>
    <p>
      <label for="<?php echo esc_attr($this->get_field_id('soundcloud')); ?>"><?php _e('Soundcloud:', 'virtue'); ?></label>
      <input class="widefat" id="<?php echo esc_attr($this->get_field_id('soundcloud')); ?>" name="<?php echo esc_attr($this->get_field_name('soundcloud')); ?>" type="text" value="<?php echo esc_attr($soundcloud); ?>" />
    </p>
    <p>
      <label for="<?php echo esc_attr($this->get_field_id('snapchat')); ?>"><?php _e('Snapchat:', 'virtue'); ?></label>
      <input class="widefat" id="<?php echo esc_attr($this->get_field_id('snapchat')); ?>" name="<?php echo esc_attr($this->get_field_name('snapchat')); ?>" type="text" value="<?php echo esc_attr($snapchat); ?>" />
    </p>
    <p>
      <label for="<?php echo esc_attr($this->get_field_id('periscope')); ?>"><?php _e('Periscope:', 'virtue'); ?></label>
      <input class="widefat" id="<?php echo esc_attr($this->get_field_id('periscope')); ?>" name="<?php echo esc_attr($this->get_field_name('periscope')); ?>" type="text" value="<?php echo esc_attr($periscope); ?>" />
    </p>
    <p>
      <label for="<?php echo esc_attr($this->get_field_id('behance')); ?>"><?php _e('Behance:', 'virtue'); ?></label>
      <input class="widefat" id="<?php echo esc_attr($this->get_field_id('behance')); ?>" name="<?php echo esc_attr($this->get_field_name('behance')); ?>" type="text" value="<?php echo esc_attr($behance); ?>" />
    </p>
    <p>
      <label for="<?php echo esc_attr($this->get_field_id('rss')); ?>"><?php _e('RSS:', 'virtue'); ?></label>
      <input class="widefat" id="<?php echo esc_attr($this->get_field_id('rss')); ?>" name="<?php echo esc_attr($this->get_field_name('rss')); ?>" type="text" value="<?php echo esc_attr($rss); ?>" />
    </p>
  <?php
  }
}
/**
 * Kadence Recent_Posts widget class
 *  Just a rewite of wp recent post
 * 
 */
class Kadence_Recent_Posts_Widget extends WP_Widget {

  private static $instance = 0;
    public function __construct() {
      $widget_ops = array('classname' => 'kadence_recent_posts', 'description' => __('This shows the most recent posts on your site with a thumbnail', 'virtue'));
      parent::__construct('kadence_recent_posts', __('Virtue: Recent Posts', 'virtue'), $widget_ops);

  }

  public function widget($args, $instance) {

    if ( ! isset( $args['widget_id'] ) )
      $args['widget_id'] = $this->id;

    extract($args);

    $title = apply_filters('widget_title', empty($instance['title']) ? __('Recent Posts', 'virtue') : $instance['title'], $instance, $this->id_base);
    if ( empty( $instance['number'] ) || ! $number = absint( $instance['number'] ) ) {$number = 10; }
    if(isset($instance['orderby'])) {
      $orderby = $instance['orderby'];
    } else {
      $orderby = 'date';
    }
    if($orderby == "menu_order" || $orderby == "title") {
      $order = "ASC";
    } else {
      $order = "DESC";
    }
    $r = new WP_Query( 
      apply_filters( 'widget_posts_args', array( 
        'posts_per_page' => $number,
        'category_name' => $instance['thecate'],
        'no_found_rows' => true,
        'post_status' => 'publish',
        'orderby' => $orderby,
        'order' => $order,
        'ignore_sticky_posts' => true 
        ) 
    ) );
    if ($r->have_posts()) :
?>
    <?php echo $before_widget; ?>
    <?php if ( $title ) echo $before_title . $title . $after_title; ?>
    <ul>
    <?php  while ($r->have_posts()) : $r->the_post(); ?>
    <li class="clearfix postclass">
        <a href="<?php the_permalink() ?>" title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>" class="recentpost_featimg">
          <?php global $post; if(has_post_thumbnail( $post->ID ) ) { 
            the_post_thumbnail( 'widget-thumb' ); 
          } else { 
             $image_url = virtue_img_placeholder_small();
            $image = aq_resize($image_url, 80, 50, true);
            if(empty($image)) { $image = $image_url; }
            echo '<img width="80" height="50" src="'.esc_attr($image).'" class="attachment-widget-thumb wp-post-image" alt="">'; } ?></a>
        <a href="<?php the_permalink() ?>" title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>" class="recentpost_title"><?php if ( get_the_title() ) the_title(); else the_ID(); ?></a>
        <span class="recentpost_date color_gray"><?php echo get_the_date(get_option( 'date_format' )); ?></span>
        </li>
    <?php endwhile; ?>
    </ul>
    <?php echo $after_widget; ?>
<?php
    // Reset the global $the_post as this query will have stomped on it
    wp_reset_postdata();

    endif;

  }

  public function update( $new_instance, $old_instance ) {
    $instance = $old_instance;
    $instance['title'] = strip_tags($new_instance['title']);
    $instance['orderby'] = $new_instance['orderby'];
    $instance['number'] = (int) $new_instance['number'];
    $instance['thecate'] = $new_instance['thecate'];


    return $instance;
  }

  public function form( $instance ) {
    $title = isset($instance['title']) ? esc_attr($instance['title']) : '';
    $number = isset($instance['number']) ? absint($instance['number']) : 5;
    if (isset($instance['thecate'])) { $thecate = esc_attr($instance['thecate']); } else {$thecate = '';}
    if (isset($instance['orderby'])) { $orderby = esc_attr($instance['orderby']); } else {$orderby = 'date';}
    $orderoptions = array(array('name' => 'Date', 'slug' => 'date'), array('name' => 'Random', 'slug' => 'rand'), array('name' => 'Comment Count', 'slug' => 'comment_count'), array('name' => 'Modified', 'slug' => 'modified'));
     $categories= get_categories();
     $cate_options = array();
          $cate_options[] = '<option value="">All</option>';
 
    foreach ($categories as $cate) {
      if ($thecate==$cate->slug) { $selected=' selected="selected"';} else { $selected=""; }
      $cate_options[] = '<option value="' . $cate->slug .'"' . $selected . '>' . $cate->name . '</option>';
    }
    $order_options = array();
    foreach ($orderoptions as $ooption) {
      if ($orderby==$ooption['slug']) { $selected=' selected="selected"';} else { $selected=""; }
      $order_options[] = '<option value="' . $ooption['slug'] .'"' . $selected . '>' . $ooption['name'] . '</option>';
    }

?>
    <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'virtue'); ?></label>
    <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>

    <p><label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number of posts to show:', 'virtue'); ?></label>
    <input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" size="3" /></p>
     <p>
    <label for="<?php echo $this->get_field_id('orderby'); ?>"><?php _e('Orderby:', 'virtue'); ?></label>
    <select id="<?php echo $this->get_field_id('orderby'); ?>" name="<?php echo $this->get_field_name('orderby'); ?>"><?php echo implode('', $order_options); ?></select>
    </p>
        <p>
    <label for="<?php echo $this->get_field_id('thecate'); ?>"><?php _e('Limit to Catagory (Optional):', 'virtue'); ?></label>
    <select id="<?php echo $this->get_field_id('thecate'); ?>" name="<?php echo $this->get_field_name('thecate'); ?>"><?php echo implode('', $cate_options); ?></select>
  </p>
<?php
  }
}

/**
 * Kadence_Image_Grid_Widget widget class
 * 
 */
class Kadence_Image_Grid_Widget extends WP_Widget {

  private static $instance = 0;
    public function __construct() {
      $widget_ops = array('classname' => 'kadence_image_grid', 'description' => __('This shows a grid of featured images from recent posts or portfolio items', 'virtue'));
      parent::__construct('kadence_image_grid', __('Virtue: Post Grid', 'virtue'), $widget_ops);
  }

  public function widget($args, $instance) {

    extract($args);

    $title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
    if(isset($instance['orderby'])) {
      $orderby = $instance['orderby'];
    } else {
      $orderby = 'date';
    }
    if($orderby == "menu_order" || $orderby == "title") {
      $order = "ASC";
    } else {
      $order = "DESC";
    }

    if ( empty( $instance['number'] ) || ! $number = absint( $instance['number'] ) ) {
      $number = 8; 
    }
      echo $before_widget; ?>
        <?php if ( $title ) echo $before_title . $title . $after_title;
        
       switch ($instance['gridchoice']) {
      
        case "portfolio" :
        
	          $r = new WP_Query( apply_filters('widget_posts_args', array( 
	          'post_type' => 'portfolio', 
	          'portfolio-type' => $instance['thetype'], 
	          'no_found_rows' => true, 
	          'posts_per_page' => $number, 
	          'post_status' => 'publish', 
	          'orderby' => $orderby,
	          'order' => $order,
	          'ignore_sticky_posts' => true ) ) );
          	if ($r->have_posts()) :
          	?>        
          	<div class="imagegrid-widget">
	          	<?php  while ($r->have_posts()) : $r->the_post(); 
	          	global $post; 
	          		if(has_post_thumbnail( $post->ID ) ) { ?> 
		          		<a href="<?php the_permalink() ?>" title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>" class="imagegrid_item lightboxhover">
		          			<?php 
		          			echo virtue_get_full_image_output(80,50,true, 'attachment-widget-thumb size-widget-thumb wp-post-image', null, get_post_thumbnail_id( $post->ID ), false, false, false);
		          			 ?>
		          		</a>
	                <?php } ?>
	          	<?php endwhile; ?>
          	</div>
          	<?php wp_reset_postdata(); 
          	endif;
        break;
        case "post":          
	        $r = new WP_Query( apply_filters('widget_posts_args', array( 
	            'posts_per_page' => $number, 
	            'category_name' => $instance['thecat'], 
	            'no_found_rows' => true, 
	            'orderby' => $orderby,
	            'order' => $order,
	            'post_status' => 'publish', 
	            'ignore_sticky_posts' => true 
	            ) 
	          ) );
           	if ($r->have_posts()) : ?>
	            <div class="imagegrid-widget">
		          	<?php  
		         	while ($r->have_posts()) : $r->the_post();
		          		global $post; 
		          		if(has_post_thumbnail( $post->ID ) ) { ?>
		          			<a href="<?php the_permalink() ?>" title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>" class="imagegrid_item lightboxhover">
		          			 	<?php 
		          			 	echo virtue_get_full_image_output(80,50,true, 'attachment-widget-thumb size-widget-thumb wp-post-image', null, get_post_thumbnail_id( $post->ID ), false, false, false);
		          			 	?>
		          			</a>
		          		<?php } 
		          	endwhile; ?>
	          	</div>
          	<?php 
          	wp_reset_postdata(); 
          	endif;
        break; 
    } ?>
	<div class="clearfix"></div>
    <?php echo $after_widget; ?>
        
<?php

  }

  public function update( $new_instance, $old_instance ) {
    $instance = $old_instance;
    $instance['title'] = strip_tags($new_instance['title']);
    $instance['number'] = (int) $new_instance['number'];
    $instance['thecat'] = $new_instance['thecat'];
    $instance['orderby'] = $new_instance['orderby'];
    $instance['thetype'] = $new_instance['thetype'];
    $instance['gridchoice'] = $new_instance['gridchoice'];

    return $instance;
  }

  public function form( $instance ) {
    
    $title = isset($instance['title']) ? esc_attr($instance['title']) : '';
    $gridchoice = isset($instance['gridchoice']) ? esc_attr($instance['gridchoice']) : '';
    $number = isset($instance['number']) ? absint($instance['number']) : 6;
    if (isset($instance['thecat'])) { $thecat = esc_attr($instance['thecat']); } else {$thecat = '';}
    if (isset($instance['thetype'])) { $thetype = esc_attr($instance['thetype']); } else {$thetype = '';}
    if (isset($instance['orderby'])) { $orderby = esc_attr($instance['orderby']); } else {$orderby = 'date';}
    $orderoptions = array(array('name' => 'Date', 'slug' => 'date'), array('name' => 'Random', 'slug' => 'rand'), array('name' => 'Comment Count', 'slug' => 'comment_count'), array('name' => 'Modified', 'slug' => 'modified'), array('name' => 'Menu Order', 'slug' => 'menu_order'), array('name' => 'Title', 'slug' => 'title'));
     $types= get_terms('portfolio-type');
     $type_options = array();
          $type_options[] = '<option value="">All</option>';
    if(!empty($types) && !is_wp_error($types) ) {
      foreach ($types as $type) {
        if ($thetype==$type->slug) { $selected=' selected="selected"';} else { $selected=""; }
        $type_options[] = '<option value="' . $type->slug .'"' . $selected . '>' . $type->name . '</option>';
      }
    }
     $categories= get_categories();
     $cat_options = array();
          $cat_options[] = '<option value="">All</option>';
 
    foreach ($categories as $cat) {
      if ($thecat==$cat->slug) { $selected=' selected="selected"';} else { $selected=""; }
      $cat_options[] = '<option value="' . $cat->slug .'"' . $selected . '>' . $cat->name . '</option>';
    }
    $order_options = array();
    foreach ($orderoptions as $ooption) {
      if ($orderby==$ooption['slug']) { $selected=' selected="selected"';} else { $selected=""; }
      $order_options[] = '<option value="' . $ooption['slug'] .'"' . $selected . '>' . $ooption['name'] . '</option>';
    }


?>
    <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'virtue'); ?></label>
    <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>

    <p><label for="<?php echo $this->get_field_id('gridchoice'); ?>"><?php _e('Grid Choice:','virtue'); ?></label>
        <select id="<?php echo $this->get_field_id('gridchoice'); ?>" name="<?php echo $this->get_field_name('gridchoice'); ?>">
            <option value="post"<?php echo ($gridchoice === 'post' ? ' selected="selected"' : ''); ?>><?php _e('Blog Posts', 'virtue'); ?></option>
            <option value="portfolio"<?php echo ($gridchoice === 'portfolio' ? ' selected="selected"' : ''); ?>><?php _e('Portfolio', 'virtue'); ?></option>
        </select></p>
        
        <p><label for="<?php echo $this->get_field_id('thecat'); ?>"><?php _e('If Post - Choose Category (Optional):', 'virtue'); ?></label>
    <select id="<?php echo $this->get_field_id('thecat'); ?>" name="<?php echo $this->get_field_name('thecat'); ?>"><?php echo implode('', $cat_options); ?></select></p>
        
    <p><label for="<?php echo $this->get_field_id('thetype'); ?>"><?php _e('If Portfolio - Choose Type (Optional):', 'virtue'); ?></label>
    <select id="<?php echo $this->get_field_id('thetype'); ?>" name="<?php echo $this->get_field_name('thetype'); ?>"><?php echo implode('', $type_options); ?></select></p>
        
        <p><label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number of images to show:', 'virtue'); ?></label>
    <input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo esc_attr($number); ?>" size="3" /></p>
     <p>
    <label for="<?php echo $this->get_field_id('orderby'); ?>"><?php _e('Orderby:', 'virtue'); ?></label>
    <select id="<?php echo $this->get_field_id('orderby'); ?>" name="<?php echo $this->get_field_name('orderby'); ?>"><?php echo implode('', $order_options); ?></select>
    </p>
  
<?php
  }
}
