<?php
/**
 * Register sidebars and widgets
 */
function virtue_sidebar_list() {
	$all_sidebars = array( array( 'name'=> __('Primary Sidebar', 'virtue' ), 'id'=>'sidebar-primary' ) );
	global $virtue; 
	if ( isset( $virtue['cust_sidebars'] ) ) {
		if ( is_array( $virtue['cust_sidebars'] ) ) {
			$i = 1;
			foreach( $virtue['cust_sidebars'] as $sidebar ) {
				if ( empty( $sidebar ) ) {
					$sidebar = 'sidebar'.$i;
				}
				$all_sidebars[] = array( 'name'=>$sidebar, 'id'=>'sidebar'.$i );
				$i++;
			}
		}
	}
	global $vir_sidebars;
	$vir_sidebars = $all_sidebars;
	return $all_sidebars;
}
add_action('init', 'virtue_sidebar_list');

function virtue_register_sidebars(){
	$the_sidebars = virtue_sidebar_list();
	if ( function_exists( 'register_sidebar' ) ){
		foreach( $the_sidebars as $side ){
			virtue_register_sidebar( $side['name'], $side['id'] );    
		}
	}
}

function virtue_register_sidebar( $name, $id ) {
	register_sidebar( array( 'name'=>$name,
		'id' => $id,
		'before_widget' => '<section id="%1$s" class="widget %2$s"><div class="widget-inner">',
		'after_widget' => '</div></section>',
		'before_title' => '<h3>',
		'after_title' => '</h3>',
	) );
}
add_action( 'widgets_init', 'virtue_register_sidebars' );

function kadence_widgets_init() {
	//Topbar 
	if( kadence_display_topbar_widget() ) {
		register_sidebar( array(
			'name'          => __('Topbar Widget', 'virtue'),
			'id'            => 'topbarright',
			'before_widget' => '',
			'after_widget'  => '',
			'before_title'  => '<span class="topbar-widgettitle">',
			'after_title'   => '</span>',
		));
	}
	// Sidebars
	register_sidebar( array(
		'name'          => __('Primary Sidebar', 'virtue'),
		'id'            => 'sidebar-primary',
		'before_widget' => '<section id="%1$s" class="widget %2$s"><div class="widget-inner">',
		'after_widget'  => '</div></section>',
		'before_title'  => '<h3>',
		'after_title'   => '</h3>',
	));
	// Footer
	global $virtue; 
	if ( isset( $virtue['footer_layout'] ) ) {
		$footer_layout = $virtue['footer_layout'];
	} else {
		$footer_layout = "twoc";
	}
	if ( $footer_layout == "fourc" ) {
		if ( function_exists( 'register_sidebar' ) )
			register_sidebar( array(
				'name' => __('Footer Column 1', 'virtue'),
				'id' => 'footer_1',
				'before_widget' => '<div class="footer-widget"><aside id="%1$s" class="widget %2$s">',
				'after_widget' => '</aside></div>',
				'before_title' => '<h3>',
				'after_title' => '</h3>',
			)
		);
		if ( function_exists( 'register_sidebar' ) )
			register_sidebar( array(
				'name' => __('Footer Column 2', 'virtue'),
				'id' => 'footer_2',
				'before_widget' => '<div class="footer-widget"><aside id="%1$s" class="widget %2$s">',
				'after_widget' => '</aside></div>',
				'before_title' => '<h3>',
				'after_title' => '</h3>',
			)
		);
		if ( function_exists( 'register_sidebar' ) )
			register_sidebar( array(
				'name' => __('Footer Column 3', 'virtue'),
				'id' => 'footer_3',
				'before_widget' => '<div class="footer-widget"><aside id="%1$s" class="widget %2$s">',
				'after_widget' => '</aside></div>',
				'before_title' => '<h3>',
				'after_title' => '</h3>',
			)
		);
		if ( function_exists( 'register_sidebar' ) )
			register_sidebar( array(
				'name' => __('Footer Column 4', 'virtue'),
				'id' => 'footer_4',
				'before_widget' => '<div class="footer-widget"><aside id="%1$s" class="widget %2$s">',
				'after_widget' => '</aside></div>',
				'before_title' => '<h3>',
				'after_title' => '</h3>',
			)
		);
	} else if ($footer_layout == "threec") {
		if ( function_exists( 'register_sidebar' ) )
			register_sidebar( array(
				'name' => __('Footer Column 1', 'virtue'),
				'id' => 'footer_third_1',
				'before_widget' => '<div class="footer-widget"><aside id="%1$s" class="widget %2$s">',
				'after_widget' => '</aside></div>',
				'before_title' => '<h3>',
				'after_title' => '</h3>',
			)
		);
		if ( function_exists( 'register_sidebar' ) )
			register_sidebar( array(
				'name' => __('Footer Column 2', 'virtue'),
				'id' => 'footer_third_2',
				'before_widget' => '<div class="footer-widget"><aside id="%1$s" class="widget %2$s">',
				'after_widget' => '</aside></div>',
				'before_title' => '<h3>',
				'after_title' => '</h3>',
			)
		);
		if ( function_exists( 'register_sidebar' ) )
			register_sidebar( array(
				'name' => __('Footer Column 3', 'virtue'),
				'id' => 'footer_third_3',
				'before_widget' => '<div class="footer-widget"><aside id="%1$s" class="widget %2$s">',
				'after_widget' => '</aside></div>',
				'before_title' => '<h3>',
				'after_title' => '</h3>',
			)
		);
	} else {
		if ( function_exists( 'register_sidebar' ) )
			register_sidebar( array(
				'name' => __('Footer Column 1', 'virtue'),
				'id' => 'footer_double_1',
				'before_widget' => '<div class="footer-widget"><aside id="%1$s" class="widget %2$s">',
				'after_widget' => '</aside></div>',
				'before_title' => '<h3>',
				'after_title' => '</h3>',
			)
		);
		if ( function_exists( 'register_sidebar' ) )
			register_sidebar( array(
				'name' => __('Footer Column 2', 'virtue'),
				'id' => 'footer_double_2',
				'before_widget' => '<div class="footer-widget"><aside id="%1$s" class="widget %2$s">',
				'after_widget' => '</aside></div>',
				'before_title' => '<h3>',
				'after_title' => '</h3>',
			)
		);
	}

	// Widgets
	if ( class_exists( 'Kadence_Contact_Widget' ) ) {
		register_widget('Kadence_Contact_Widget');
	}
	if ( class_exists( 'Kadence_Social_Widget' ) ) {
		register_widget('Kadence_Social_Widget');
	}
	if ( class_exists( 'Kadence_Recent_Posts_Widget' ) ) {
		register_widget('Kadence_Recent_Posts_Widget');
	}
	if ( class_exists( 'Kadence_Image_Grid_Widget' ) ) {
		register_widget('Kadence_Image_Grid_Widget');
	}
	if ( class_exists( 'Simple_About_With_Image' ) ) {
		register_widget('Simple_About_With_Image');
	}
}
add_action('widgets_init', 'kadence_widgets_init');

/**
 * Contact widget
 */
if ( ! class_exists( 'Kadence_Contact_Widget' ) ) {
	class Kadence_Contact_Widget extends WP_Widget {
		private static $instance = 0;
		public function __construct() {
			$widget_ops = array( 'classname' => 'widget_kadence_contact', 'description' => __( 'Use this widget to add a Vcard to your site', 'virtue' ) );
			parent::__construct( 'widget_kadence_contact', __('Virtue: Contact/Vcard', 'virtue'), $widget_ops);
		}

		public function widget($args, $instance) {

			if (!isset($args['widget_id'])) {
				$args['widget_id'] = null;
			}
			extract($args);

			$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? __('vCard', 'virtue') : $instance['title'], $instance, $this->id_base );
			if ( !isset( $instance['company'] ) ) { $instance['company'] = ''; }
			if ( !isset( $instance['name'] ) ) { $instance['name'] = ''; }
			if ( !isset( $instance['street_address'] ) ) { $instance['street_address'] = ''; }
			if ( !isset( $instance['locality'] ) ) { $instance['locality'] = ''; }
			if ( !isset( $instance['region'] ) ) { $instance['region'] = ''; }
			if ( !isset( $instance['postal_code'] ) ) { $instance['postal_code'] = ''; }
			if ( !isset( $instance['tel'] ) ) { $instance['tel'] = ''; }
			if ( !isset( $instance['fixedtel'] ) ) { $instance['fixedtel'] = ''; }
			if ( !isset( $instance['email'] ) ) { $instance['email'] = ''; }

			echo $before_widget;

			if ($title) {
				echo $before_title;
				echo $title;
				echo $after_title;
			}
			?>
			<div class="vcard">
				<?php 
				if ( ! empty( $instance['company'] ) ) : ?><h5 class="vcard-company"><i class="icon-building"></i><?php echo esc_html( $instance['company'] ); ?></h5>
				<?php 
				endif;
				if ( ! empty( $instance['name'] ) ) : ?><p class="vcard-name fn"><i class="icon-user"></i><?php echo esc_html( $instance['name'] ); ?></p>
				<?php 
				endif;
				if ( ! empty( $instance['street_address'] ) || ! empty( $instance['locality'] ) || ! empty( $instance['region'] ) ) :?>
				<p class="vcard-address"><i class="icon-map-marker"></i><?php echo esc_html( $instance['street_address'] ); ?>
				<span><?php echo esc_html($instance['locality']); ?> <?php echo esc_html( $instance['region'] ); ?> <?php echo esc_html( $instance['postal_code'] ); ?></span></p>
				<?php endif;?>
				<?php if(!empty($instance['tel'])):?><p class="tel"><i class="icon-tablet"></i> <?php echo esc_html( $instance['tel'] ); ?></p><?php endif;?>
				<?php if(!empty($instance['fixedtel'])):?><p class="tel fixedtel"><i class="icon-phone"></i> <?php echo esc_html( $instance['fixedtel'] ); ?></p><?php endif;?>
				<?php if(!empty($instance['email'])):?><p><a class="email" href="mailto:<?php echo esc_attr($instance['email']); ?>"><i class="icon-envelope"></i> <?php echo esc_html($instance['email']); ?></a></p> <?php endif;?>
    </div>
  <?php
    echo $after_widget;

  }

  public function update($new_instance, $old_instance) {
    $instance = $old_instance;
    $instance['title'] = sanitize_text_field( $new_instance['title']);
    $instance['company'] = sanitize_text_field( $new_instance['company']);
    $instance['name'] = sanitize_text_field( $new_instance['name']);
    $instance['street_address'] = sanitize_text_field( $new_instance['street_address']);
    $instance['locality'] = sanitize_text_field( $new_instance['locality']);
    $instance['region'] = sanitize_text_field( $new_instance['region']);
    $instance['postal_code'] = sanitize_text_field( $new_instance['postal_code']);
    $instance['tel'] = sanitize_text_field( $new_instance['tel']);
    $instance['fixedtel'] = sanitize_text_field( $new_instance['fixedtel']);
    $instance['email'] = sanitize_text_field( $new_instance['email']);

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
      <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Title:', 'virtue'); ?></label>
      <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
    </p>
    <p>
      <label for="<?php echo esc_attr($this->get_field_id('company')); ?>"><?php esc_html_e('Company Name:', 'virtue'); ?></label>
      <input class="widefat" id="<?php echo esc_attr($this->get_field_id('company')); ?>" name="<?php echo esc_attr($this->get_field_name('company')); ?>" type="text" value="<?php echo esc_attr($company); ?>" />
    </p>
    <p>
      <label for="<?php echo esc_attr($this->get_field_id('name')); ?>"><?php esc_html_e('Name:', 'virtue'); ?></label>
      <input class="widefat" id="<?php echo esc_attr($this->get_field_id('name')); ?>" name="<?php echo esc_attr($this->get_field_name('name')); ?>" type="text" value="<?php echo esc_attr($name); ?>" />
    </p>
    <p>
      <label for="<?php echo esc_attr($this->get_field_id('street_address')); ?>"><?php esc_html_e('Street Address:', 'virtue'); ?></label>
      <input class="widefat" id="<?php echo esc_attr($this->get_field_id('street_address')); ?>" name="<?php echo esc_attr($this->get_field_name('street_address')); ?>" type="text" value="<?php echo esc_attr($street_address); ?>" />
    </p>
    <p>
      <label for="<?php echo esc_attr($this->get_field_id('locality')); ?>"><?php esc_html_e('City/Locality:', 'virtue'); ?></label>
      <input class="widefat" id="<?php echo esc_attr($this->get_field_id('locality')); ?>" name="<?php echo esc_attr($this->get_field_name('locality')); ?>" type="text" value="<?php echo esc_attr($locality); ?>" />
    </p>
    <p>
      <label for="<?php echo esc_attr($this->get_field_id('region')); ?>"><?php esc_html_e('State/Region:', 'virtue'); ?></label>
      <input class="widefat" id="<?php echo esc_attr($this->get_field_id('region')); ?>" name="<?php echo esc_attr($this->get_field_name('region')); ?>" type="text" value="<?php echo esc_attr($region); ?>" />
    </p>
    <p>
      <label for="<?php echo esc_attr($this->get_field_id('postal_code')); ?>"><?php esc_html_e('Zipcode/Postal Code:', 'virtue'); ?></label>
      <input class="widefat" id="<?php echo esc_attr($this->get_field_id('postal_code')); ?>" name="<?php echo esc_attr($this->get_field_name('postal_code')); ?>" type="text" value="<?php echo esc_attr($postal_code); ?>" />
    </p>
    <p>
      <label for="<?php echo esc_attr($this->get_field_id('tel')); ?>"><?php esc_html_e('Mobile Telephone:', 'virtue'); ?></label>
      <input class="widefat" id="<?php echo esc_attr($this->get_field_id('tel')); ?>" name="<?php echo esc_attr($this->get_field_name('tel')); ?>" type="text" value="<?php echo esc_attr($tel); ?>" />
    </p>
     <p>
      <label for="<?php echo esc_attr($this->get_field_id('fixedtel')); ?>"><?php esc_html_e('Fixed Telephone:', 'virtue'); ?></label>
      <input class="widefat" id="<?php echo esc_attr($this->get_field_id('fixedtel')); ?>" name="<?php echo esc_attr($this->get_field_name('fixedtel')); ?>" type="text" value="<?php echo esc_attr($fixedtel); ?>" />
    </p>
    <p>
      <label for="<?php echo esc_attr($this->get_field_id('email')); ?>"><?php esc_html_e('Email:', 'virtue'); ?></label>
      <input class="widefat" id="<?php echo esc_attr($this->get_field_id('email')); ?>" name="<?php echo esc_attr($this->get_field_name('email')); ?>" type="text" value="<?php echo esc_attr($email); ?>" />
    </p>
  <?php
  }
}
}
/**
 * Social widget
 */
if ( ! class_exists( 'Kadence_Social_Widget' ) ) {
class Kadence_Social_Widget extends WP_Widget {
  private static $instance = 0;
    public function __construct() {
    $widget_ops = array('classname' => 'widget_kadence_social', 'description' => __('Simple way to add Social Icons', 'virtue'));
    parent::__construct('widget_kadence_social', __('Virtue: Social Links', 'virtue'), $widget_ops);
  }

  public function widget($args, $instance) {
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
    if (!isset($instance['vk'])) { $instance['vk'] = ''; }
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
<?php if(!empty($instance['googleplus'])):?><a href="<?php echo esc_url($instance['googleplus']); ?>" class="googleplus_link" title="GooglePlus" target="_blank" data-toggle="tooltip" data-placement="top" data-original-title="GooglePlus"><i class="icon-google-plus"></i></a><?php endif;?>
<?php if(!empty($instance['flickr'])):?><a href="<?php echo esc_url($instance['flickr']); ?>" class="flickr_link" title="Flickr" data-toggle="tooltip" target="_blank" data-placement="top" data-original-title="Flickr"><i class="icon-flickr"></i></a><?php endif;?>
<?php if(!empty($instance['vimeo'])):?><a href="<?php echo esc_url($instance['vimeo']); ?>" class="vimeo_link" title="Vimeo" target="_blank" data-toggle="tooltip" data-placement="top" data-original-title="Vimeo"><i class="icon-vimeo"></i></a><?php endif;?>
<?php if(!empty($instance['youtube'])):?><a href="<?php echo esc_url($instance['youtube']); ?>" class="youtube_link" title="YouTube" target="_blank" data-toggle="tooltip" data-placement="top" data-original-title="YouTube"><i class="icon-youtube"></i></a><?php endif;?>
<?php if(!empty($instance['pinterest'])):?><a href="<?php echo esc_url($instance['pinterest']); ?>" class="pinterest_link" title="Pinterest" target="_blank" data-toggle="tooltip" data-placement="top" data-original-title="Pinterest"><i class="icon-pinterest"></i></a><?php endif;?>
<?php if(!empty($instance['dribbble'])):?><a href="<?php echo esc_url($instance['dribbble']); ?>" class="dribbble_link" title="Dribbble" target="_blank" data-toggle="tooltip" data-placement="top" data-original-title="Dribbble"><i class="icon-dribbble"></i></a><?php endif;?>
<?php if(!empty($instance['linkedin'])):?><a href="<?php echo esc_url($instance['linkedin']); ?>" class="linkedin_link" title="LinkedIn" target="_blank" data-toggle="tooltip" data-placement="top" data-original-title="LinkedIn"><i class="icon-linkedin"></i></a><?php endif;?>
<?php if(!empty($instance['tumblr'])):?><a href="<?php echo esc_url($instance['tumblr']); ?>" class="tumblr_link" title="Tumblr" target="_blank" data-toggle="tooltip" data-placement="top" data-original-title="Tumblr"><i class="icon-tumblr"></i></a><?php endif;?>
<?php if(!empty($instance['vk'])):?><a href="<?php echo esc_url($instance['vk']); ?>" class="vk_link" title="VK" target="_blank" data-toggle="tooltip" data-placement="top" data-original-title="VK"><i class="icon-vk"></i></a><?php endif;?>
<?php if(!empty($instance['rss'])):?><a href="<?php echo esc_url($instance['rss']); ?>" class="rss_link" title="RSS" target="_blank" data-toggle="tooltip" data-placement="top" data-original-title="RSS"><i class="icon-rss-sign"></i></a><?php endif;?>
    </div>
  <?php
    echo $after_widget;

  }

  public function update($new_instance, $old_instance) {
    $instance = $old_instance;
     $instance['title'] = sanitize_text_field( $new_instance['title']);
    $instance['facebook'] = esc_url_raw( $new_instance['facebook']);
    $instance['twitter'] = esc_url_raw( $new_instance['twitter']);
    $instance['instagram'] = esc_url_raw( $new_instance['instagram']);
    $instance['googleplus'] = esc_url_raw( $new_instance['googleplus']);
    $instance['flickr'] = esc_url_raw( $new_instance['flickr']);
    $instance['vimeo'] = esc_url_raw( $new_instance['vimeo']);
    $instance['youtube'] = esc_url_raw( $new_instance['youtube']);
    $instance['pinterest'] = esc_url_raw( $new_instance['pinterest']);
    $instance['dribbble'] = esc_url_raw( $new_instance['dribbble']);
    $instance['linkedin'] = esc_url_raw( $new_instance['linkedin']);
    $instance['tumblr'] = esc_url_raw( $new_instance['tumblr']);
    $instance['vk'] = esc_url_raw( $new_instance['vk']);
    $instance['rss'] = esc_url_raw( $new_instance['rss']);

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
    $vk = isset($instance['vk']) ? esc_attr($instance['vk']) : '';
    $rss = isset($instance['rss']) ? esc_attr($instance['rss']) : '';
  ?>
  <p>
      <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Title:', 'virtue'); ?></label>
      <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
    </p>
    <p>
      <label for="<?php echo esc_attr($this->get_field_id('facebook')); ?>"><?php esc_html_e('Facebook:', 'virtue'); ?></label>
      <input class="widefat" id="<?php echo esc_attr($this->get_field_id('facebook')); ?>" name="<?php echo esc_attr($this->get_field_name('facebook')); ?>" type="text" value="<?php echo esc_attr($facebook); ?>" />
    </p>
    <p>
      <label for="<?php echo esc_attr($this->get_field_id('twitter')); ?>"><?php esc_html_e('Twitter:', 'virtue'); ?></label>
      <input class="widefat" id="<?php echo esc_attr($this->get_field_id('twitter')); ?>" name="<?php echo esc_attr($this->get_field_name('twitter')); ?>" type="text" value="<?php echo esc_attr($twitter); ?>" />
    </p>
    <p>
      <label for="<?php echo esc_attr($this->get_field_id('instagram')); ?>"><?php esc_html_e('Instagram:', 'virtue'); ?></label>
      <input class="widefat" id="<?php echo esc_attr($this->get_field_id('instagram')); ?>" name="<?php echo esc_attr($this->get_field_name('instagram')); ?>" type="text" value="<?php echo esc_attr($instagram); ?>" />
    </p>
    <p>
      <label for="<?php echo esc_attr($this->get_field_id('googleplus')); ?>"><?php esc_html_e('GooglePlus:', 'virtue'); ?></label>
      <input class="widefat" id="<?php echo esc_attr($this->get_field_id('googleplus')); ?>" name="<?php echo esc_attr($this->get_field_name('googleplus')); ?>" type="text" value="<?php echo esc_attr($googleplus); ?>" />
    </p>
    <p>
      <label for="<?php echo esc_attr($this->get_field_id('flickr')); ?>"><?php esc_html_e('Flickr:', 'virtue'); ?></label>
      <input class="widefat" id="<?php echo esc_attr($this->get_field_id('flickr')); ?>" name="<?php echo esc_attr($this->get_field_name('flickr')); ?>" type="text" value="<?php echo esc_attr($flickr); ?>" />
    </p>
    <p>
      <label for="<?php echo esc_attr($this->get_field_id('vimeo')); ?>"><?php esc_html_e('Vimeo:', 'virtue'); ?></label>
      <input class="widefat" id="<?php echo esc_attr($this->get_field_id('vimeo')); ?>" name="<?php echo esc_attr($this->get_field_name('vimeo')); ?>" type="text" value="<?php echo esc_attr($vimeo); ?>" />
    </p>
    <p>
      <label for="<?php echo esc_attr($this->get_field_id('youtube')); ?>"><?php esc_html_e('Youtube:', 'virtue'); ?></label>
      <input class="widefat" id="<?php echo esc_attr($this->get_field_id('youtube')); ?>" name="<?php echo esc_attr($this->get_field_name('youtube')); ?>" type="text" value="<?php echo esc_attr($youtube); ?>" />
    </p>
    <p>
      <label for="<?php echo esc_attr($this->get_field_id('pinterest')); ?>"><?php esc_html_e('Pinterest:', 'virtue'); ?></label>
      <input class="widefat" id="<?php echo esc_attr($this->get_field_id('pinterest')); ?>" name="<?php echo esc_attr($this->get_field_name('pinterest')); ?>" type="text" value="<?php echo esc_attr($pinterest); ?>" />
    </p>
    <p>
      <label for="<?php echo esc_attr($this->get_field_id('dribbble')); ?>"><?php esc_html_e('Dribbble:', 'virtue'); ?></label>
      <input class="widefat" id="<?php echo esc_attr($this->get_field_id('dribbble')); ?>" name="<?php echo esc_attr($this->get_field_name('dribbble')); ?>" type="text" value="<?php echo esc_attr($dribbble); ?>" />
    </p>
    <p>
      <label for="<?php echo esc_attr($this->get_field_id('linkedin')); ?>"><?php esc_html_e('Linkedin:', 'virtue'); ?></label>
      <input class="widefat" id="<?php echo esc_attr($this->get_field_id('linkedin')); ?>" name="<?php echo esc_attr($this->get_field_name('linkedin')); ?>" type="text" value="<?php echo esc_attr($linkedin); ?>" />
    </p>
    <p>
      <label for="<?php echo esc_attr($this->get_field_id('tumblr')); ?>"><?php esc_html_e('Tumblr:', 'virtue'); ?></label>
      <input class="widefat" id="<?php echo esc_attr($this->get_field_id('tumblr')); ?>" name="<?php echo esc_attr($this->get_field_name('tumblr')); ?>" type="text" value="<?php echo esc_attr($tumblr); ?>" />
    </p>
    <p>
      <label for="<?php echo esc_attr($this->get_field_id('vk')); ?>"><?php esc_html_e('VK:', 'virtue'); ?></label>
      <input class="widefat" id="<?php echo esc_attr($this->get_field_id('vk')); ?>" name="<?php echo esc_attr($this->get_field_name('vk')); ?>" type="text" value="<?php echo esc_attr($vk); ?>" />
    </p>
    <p>
      <label for="<?php echo esc_attr($this->get_field_id('rss')); ?>"><?php esc_html_e('RSS:', 'virtue'); ?></label>
      <input class="widefat" id="<?php echo esc_attr($this->get_field_id('rss')); ?>" name="<?php echo esc_attr($this->get_field_name('rss')); ?>" type="text" value="<?php echo esc_attr($rss); ?>" />
    </p>
  <?php
  }
}
}
/**
 * Kadence Recent_Posts widget class
 *  Just a rewite of wp recent post
 * 
 */
if ( ! class_exists( 'Kadence_Recent_Posts_Widget' ) ) {
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
    if ( empty( $instance['number'] ) || ! $number = absint( $instance['number'] ) )
      $number = 10;

    $r = new WP_Query( apply_filters( 'widget_posts_args', array( 'posts_per_page' => $number, 'category_name' => $instance['thecate'], 'no_found_rows' => true, 'post_status' => 'publish', 'ignore_sticky_posts' => true ) ) );
    if ($r->have_posts()) :
?>
    <?php echo $before_widget; ?>
    <?php if ( $title ) echo $before_title . $title . $after_title; ?>
    <ul>
    <?php  while ($r->have_posts()) : $r->the_post(); 
    global $post; ?>
    <li class="clearfix postclass">
		<a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>" class="recentpost_featimg">
		<?php
			if(has_post_thumbnail( $post->ID ) ) { 
				the_post_thumbnail( 'widget-thumb' ); 
			} else {
				// Placeholder Id
				$image_id = virtue_get_options_placeholder_image();
				if( ! empty( $image_id ) ) {
					$img = virtue_get_image_array( 80, 50, true, null, null, $image_id );
				} else {
					$image = virtue_post_widget_default_placeholder();
					$img = array(
						'src' => $image,
						'srcset' => ''
					);
				}
				echo '<img width="80" height="50" src="'.esc_attr( $img[ 'src' ] ).'" class="attachment-widget-thumb wp-post-image" '.wp_kses_post( $img[ 'srcset' ] ).' alt="'.the_title_attribute('echo=0').'">'; 
			} ?>
        </a>
        <a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>" class="recentpost_title"><?php the_title() ?></a>
        <span class="recentpost_date"><?php echo esc_html( get_the_date( get_option( 'date_format' ) ) ); ?></span>
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
    $instance['title'] = sanitize_text_field( $new_instance['title'] );
    $instance['number'] = (int) $new_instance['number'];
    $instance['thecate'] = sanitize_text_field( $new_instance['thecate'] );

    return $instance;
  }


  public function form( $instance ) {
    $title = isset($instance['title']) ? esc_attr($instance['title']) : '';
    $number = isset($instance['number']) ? absint($instance['number']) : 5;
     if (isset($instance['thecate'])) { $thecate = esc_attr($instance['thecate']); } else {$thecate = '';}
          $categories= get_categories();
     $cate_options = array();
          $cate_options[] = '<option value="">All</option>';
 
    foreach ($categories as $cate) {
      if ($thecate==$cate->slug) { $selected=' selected="selected"';} else { $selected=""; }
      $cate_options[] = '<option value="' . $cate->slug .'"' . $selected . '>' . $cate->name . '</option>';
    }

?>
    <p><label for="<?php echo esc_attr( $this->get_field_id('title') ); ?>"><?php esc_html_e('Title:', 'virtue'); ?></label>
    <input class="widefat" id="<?php echo esc_attr( $this->get_field_id('title') ); ?>" name="<?php echo esc_attr( $this->get_field_name('title') ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></p>

    <p><label for="<?php echo esc_attr( $this->get_field_id('number') ); ?>"><?php esc_html_e('Number of posts to show:', 'virtue'); ?></label>
    <input id="<?php echo esc_attr( $this->get_field_id('number') ); ?>" name="<?php echo esc_attr( $this->get_field_name('number') ); ?>" type="text" value="<?php echo esc_attr( $number ); ?>" size="3" /></p>
        <p>
    <label for="<?php echo esc_attr( $this->get_field_id('thecate') ); ?>"><?php esc_html_e('Limit to Catagory (Optional):', 'virtue'); ?></label>
    <select id="<?php echo esc_attr( $this->get_field_id('thecate') ); ?>" name="<?php echo esc_attr( $this->get_field_name('thecate') ); ?>"><?php echo wp_kses( implode('', $cate_options ), virtue_admin_allowed_html() ); ?></select>
  </p>
<?php
  }
}

}
if ( ! class_exists( 'Kadence_Image_Grid_Widget' ) ) {
class Kadence_Image_Grid_Widget extends WP_Widget {

  private static $instance = 0;
    public function __construct() {
      $widget_ops = array('classname' => 'kadence_image_grid', 'description' => __('This shows a grid of featured images from recent posts or portfolio items', 'virtue'));
      parent::__construct('kadence_image_grid', __('Virtue: Image Grid', 'virtue'), $widget_ops);
  }

  public function widget($args, $instance) {
    if ( ! isset( $args['widget_id'] ) )
      $args['widget_id'] = $this->id;

    extract($args);

    $title = apply_filters('widget_title', empty($instance['title']) ? __('Post Gallery', 'virtue') : $instance['title'], $instance, $this->id_base);
    if ( empty( $instance['number'] ) || ! $number = absint( $instance['number'] ) )
      $number = 8; 
      echo $before_widget; ?>
        <?php if ( $title ) echo $before_title . $title . $after_title;
		switch ($instance['gridchoice']) {
      
	        case "portfolio" :

	        	if ( empty( $instance['thetype'] ) || 'null' == $instance['thetype'] ) {
	        		$type = '';
	        	} else {
	        		$type = $instance['thetype'];
	        	}
	        
				$r = new WP_Query( apply_filters('widget_posts_args', array( 
				'post_type' => 'portfolio', 
				'portfolio-type' => $type, 
				'no_found_rows' => true, 
				'posts_per_page' => $number, 
				'post_status' => 'publish', 
				'ignore_sticky_posts' => true ) ) );
				if ($r->have_posts()) :
				?>        
				<div class="imagegrid-widget">
					<?php  while ($r->have_posts()) : $r->the_post(); 
						global $post; 
						if( has_post_thumbnail( $post->ID ) ) { ?>
							<a href="<?php the_permalink() ?>" title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>" class="imagegrid_item lightboxhover">
								<?php the_post_thumbnail( 'widget-thumb' ); ?>
							</a>
						<?php } 
					endwhile; ?>
					</div>
					<?php wp_reset_postdata(); 
				endif;
			break;
			case "post" :
			echo $instance['thecat'];
				$r = new WP_Query( apply_filters( 'widget_posts_args', array( 
					'posts_per_page' => $number, 
					'category_name' => $instance['thecat'], 
					'no_found_rows' => true, 
					'post_status' => 'publish', 
					'ignore_sticky_posts' => true ) 
				) );
				if ( $r->have_posts() ) : ?>
					<div class="imagegrid-widget">
					<?php  while ( $r->have_posts() ) : $r->the_post(); 
						global $post; 
						if( has_post_thumbnail( $post->ID ) ) { ?>
							<a href="<?php the_permalink() ?>" title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>" class="imagegrid_item lightboxhover">
								<?php the_post_thumbnail( 'widget-thumb' ); ?>
							</a>
						<?php } 
					endwhile; ?>
					</div>
					<?php wp_reset_postdata(); 
				endif;
			break; 
		} ?>
		<div class="clearfix"></div>
		<?php echo $after_widget; 
  }

  public function update( $new_instance, $old_instance ) {
    $instance = $old_instance;
    $instance['title'] = sanitize_text_field( $new_instance['title'] );
    $instance['number'] = (int) $new_instance['number'];
    $instance['thecat'] = sanitize_text_field( $new_instance['thecat'] );
    $instance['thetype'] = sanitize_text_field( $new_instance['thetype'] );
    $instance['gridchoice'] = sanitize_text_field( $new_instance['gridchoice'] );

    return $instance;
  }


  public function form( $instance ) {
    
    $title = isset($instance['title']) ? esc_attr($instance['title']) : '';
    $gridchoice = isset($instance['gridchoice']) ? esc_attr($instance['gridchoice']) : '';
    $number = isset($instance['number']) ? absint($instance['number']) : 6;
    if (isset($instance['thecat'])) { $thecat = esc_attr($instance['thecat']); } else {$thecat = '';}
    if (isset($instance['thetype'])) { $thetype = esc_attr($instance['thetype']); } else {$thetype = '';}
	$types = get_terms('portfolio-type');
	$type_options = array();
	$type_options[] = '<option value="">All</option>';
	if ( ! empty( $types ) && ! is_wp_error( $types ) ) {
		foreach( $types as $type ) {
			$selected = ( $thetype == $type->slug ? ' selected="selected"' : '' );
			$type_options[] = '<option value="' . esc_attr( $type->slug ) .'"' . $selected . '>' . esc_html( $type->name ) . '</option>';
		}
	}
	$categories= get_categories();
	$cat_options = array();
	$cat_options[] = '<option value="">All</option>';
 
    foreach ($categories as $cat) {
      if ($thecat==$cat->slug) { $selected=' selected="selected"';} else { $selected=""; }
      $cat_options[] = '<option value="' . $cat->slug .'"' . $selected . '>' . $cat->name . '</option>';
    }

?>
    <p><label for="<?php echo esc_attr( $this->get_field_id('title') ); ?>"><?php esc_html_e('Title:', 'virtue'); ?></label>
    <input class="widefat" id="<?php echo esc_attr( $this->get_field_id('title') ); ?>" name="<?php echo esc_attr( $this->get_field_name('title') ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></p>

    <p><label for="<?php echo esc_attr( $this->get_field_id('gridchoice') ); ?>"><?php esc_html_e('Grid Choice:','virtue'); ?></label>
        <select id="<?php echo esc_attr( $this->get_field_id('gridchoice') ); ?>" name="<?php echo esc_attr( $this->get_field_name('gridchoice') ); ?>">
            <option value="post"<?php echo ($gridchoice === 'post' ? ' selected="selected"' : ''); ?>><?php esc_html_e('Blog Posts', 'virtue'); ?></option>
            <option value="portfolio"<?php echo ($gridchoice === 'portfolio' ? ' selected="selected"' : ''); ?>><?php esc_html_e('Portfolio', 'virtue'); ?></option>
        </select></p>
        
        <p><label for="<?php echo esc_attr( $this->get_field_id('thecat') ); ?>"><?php esc_html_e('If Post - Choose Category (Optional):', 'virtue'); ?></label>
    <select id="<?php echo esc_attr( $this->get_field_id('thecat') ); ?>" name="<?php echo $this->get_field_name('thecat'); ?>"><?php echo wp_kses( implode( '', $cat_options ), virtue_admin_allowed_html() ); ?></select></p>
        
    <p><label for="<?php echo esc_attr( $this->get_field_id('thetype') ); ?>"><?php esc_html_e('If Portfolio - Choose Type (Optional):', 'virtue'); ?></label>
    <select id="<?php echo esc_attr( $this->get_field_id('thetype') ); ?>" name="<?php echo esc_attr( $this->get_field_name('thetype') ); ?>"><?php echo wp_kses( implode('', $type_options), virtue_admin_allowed_html() ); ?></select></p>
        
        <p><label for="<?php echo esc_attr( $this->get_field_id('number') ); ?>"><?php esc_html_e('Number of images to show:', 'virtue'); ?></label>
    <input id="<?php echo esc_attr( $this->get_field_id('number') ); ?>" name="<?php echo esc_attr( $this->get_field_name('number') ); ?>" type="text" value="<?php echo esc_attr( $number ); ?>" size="3" /></p>
  
<?php
  }
}
}
function kad_is_edit_page(){
  if (!is_admin()) return false;

    if ( in_array( $GLOBALS['pagenow'], array( 'post.php', 'post-new.php', 'widgets.php', 'post.php', 'post-new.php' ) ) ) {
      return true;
    }
}


if ( ! class_exists( 'Simple_About_With_Image' ) ) {
class Simple_About_With_Image extends WP_Widget {

  private static $instance = 0;
    public function __construct() {
        $widget_ops = array('classname' => 'virtue_about_with_image', 'description' => __('This allows for an image and a simple about text.', 'virtue'));
        parent::__construct('virtue_about_with_image', __('Virtue: Image', 'virtue'), $widget_ops);
    }

    public function widget($args, $instance){ 
        extract( $args );
        if (!empty($instance['image_link_open']) && $instance['image_link_open'] == "none") {
          $uselink = false;
          $link = '';
          $linktype = '';
        } else if(empty($instance['image_link_open']) || $instance['image_link_open'] == "lightbox") {
          $uselink = true;
          $link = esc_url($instance['image_uri']);
          $linktype = 'rel="lightbox"';
        } else if($instance['image_link_open'] == "_blank") {
          $uselink = true;
          if(!empty($instance['image_link'])) {$link = $instance['image_link'];} else {$link = esc_url($instance['image_uri']);}
          $linktype = 'target="_blank"';
        } else if($instance['image_link_open'] == "_self") {
          $uselink = true;
          if(!empty($instance['image_link'])) {$link = $instance['image_link'];} else {$link = esc_url($instance['image_uri']);}
          $linktype = 'target="_self"';
        }
    ?>
     <?php echo $before_widget; ?>
    <div class="kad_img_upload_widget">
        <?php if($uselink == true) {echo '<a href="'.esc_attr( $link ).'" '.wp_kses_post( $linktype ).'>';} ?>
        <img src="<?php echo esc_url($instance['image_uri']); ?>" />
        <?php if($uselink == true) {echo '</a>'; }?>
        <?php if(!empty($instance['text'])) { ?> <div class="virtue_image_widget_caption"><?php echo wp_kses_post( $instance['text'] ); ?></div><?php }?>
    </div>

    <?php echo $after_widget; ?>
    <?php }

    public function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['text'] = wp_filter_post_kses( $new_instance['text'] );
        $instance['image_uri'] = esc_url_raw( $new_instance['image_uri'] );
        $instance['image_link'] = esc_url_raw( $new_instance['image_link'] );
        $instance['image_link_open'] = sanitize_text_field( $new_instance['image_link_open'] );
        return $instance;
    }

  public function form($instance){ 
    $image_uri = isset($instance['image_uri']) ? esc_attr($instance['image_uri']) : '';
    $image_link = isset($instance['image_link']) ? esc_attr($instance['image_link']) : '';
    if (isset($instance['image_link_open'])) { $image_link_open = esc_attr($instance['image_link_open']); } else {$image_link_open = 'lightbox';}
    $link_options = array();
    $link_options_array = array();
    $link_options[] = array("slug" => "lightbox", "name" => __('Lightbox', 'virtue'));
    $link_options[] = array("slug" => "_blank", "name" => __('New Window', 'virtue'));
    $link_options[] = array("slug" => "_self", "name" => __('Same Window', 'virtue'));
    $link_options[] = array("slug" => "none", "name" => __('No Link', 'virtue'));

    foreach ($link_options as $link_option) {
      if ($image_link_open == $link_option['slug']) { $selected=' selected="selected"';} else { $selected=""; }
      $link_options_array[] = '<option value="' . $link_option['slug'] .'"' . $selected . '>' . $link_option['name'] . '</option>';
    }
    ?>
  <div class="kad_img_upload_widget">
    <p>
        <img class="kad_custom_media_image" src="<?php if(!empty($instance['image_uri'])){echo esc_url( $instance['image_uri'] );} ?>" style="margin:0;padding:0;max-width:100px;display:block" />
    </p>
    <p>
        <label for="<?php echo esc_attr( $this->get_field_id('image_uri') ); ?>"><?php esc_html_e('Image URL', 'virtue'); ?></label><br />
        <input type="text" class="widefat kad_custom_media_url" name="<?php echo esc_attr( $this->get_field_name('image_uri') ); ?>" id="<?php echo esc_attr( $this->get_field_id('image_uri') ); ?>" value="<?php echo esc_attr( $image_uri ); ?>">
        <input type="button" value="<?php esc_html_e('Upload', 'virtue'); ?>" class="button kad_custom_media_upload" id="kad_custom_image_uploader" />
    </p>
    <p>
        <label for="<?php echo esc_attr( $this->get_field_id('image_link_open') ); ?>"><?php esc_html_e('Image opens in', 'virtue'); ?></label><br />
        <select id="<?php echo esc_attr( $this->get_field_id('image_link_open') ); ?>" name="<?php echo esc_attr( $this->get_field_name('image_link_open') ); ?>"><?php echo wp_kses( implode('', $link_options_array), virtue_admin_allowed_html() );?></select>
    </p>
    <p>
        <label for="<?php echo esc_attr( $this->get_field_id('image_link') ); ?>"><?php esc_html_e('Image Link (optional)', 'virtue'); ?></label><br />
        <input type="text" class="widefat kad_img_widget_link" name="<?php echo esc_attr( $this->get_field_name('image_link') ); ?>" id="<?php echo esc_attr( $this->get_field_id('image_link') ); ?>" value="<?php echo esc_attr( $image_link ); ?>">
    </p>
    <p>
      <label for="<?php echo esc_attr( $this->get_field_id('text') ); ?>"><?php esc_html_e('Text/Caption (optional)', 'virtue'); ?></label><br />
      <textarea name="<?php echo esc_attr( $this->get_field_name('text') ); ?>" id="<?php echo esc_attr( $this->get_field_id('text') ); ?>" class="widefat" ><?php if(! empty( $instance['text'] ) ) echo esc_textarea( $instance['text'] ); ?></textarea>
    </p>
  </div>
    <?php
  }
}


}
