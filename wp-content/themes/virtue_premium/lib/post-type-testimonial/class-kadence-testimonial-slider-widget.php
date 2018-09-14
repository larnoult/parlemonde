<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
/**
 * Kadence Testimonial_slider widget class
 * 
 */
if( ! class_exists( 'Kadence_Testimonial_Slider_Widget' ) ) {
	class Kadence_Testimonial_Slider_Widget extends WP_Widget {

		private static $instance = 0;
		public function __construct() {
			$widget_ops = array('classname' => 'kadence_testimonials_slider', 'description' => __('This shows a slider with your testimonials', 'virtue'));
			parent::__construct('kadence_testimonials_slider', __('Virtue: Testimonial Carousel', 'virtue'), $widget_ops);
		}

	  public function widget($args, $instance) {


	    if ( ! isset( $args['widget_id'] ) ) {
	      $args['widget_id'] = $this->id;
	    }

	    extract($args);
	    global $kt_testimonial_loop;
	    $carousel_rn = intval(preg_replace('/[^0-9]+/', '', $args['widget_id']), 10);
	    $title = apply_filters('widget_title', empty($instance['title']) ? __('Testimonials', 'virtue') : $instance['title'], $instance, $this->id_base);
	    if ( empty( $instance['number'] ) || ! $number = absint( $instance['number'] ) )
	      $number = 10;
	    if ( empty( $instance['wordcount'] ) || ! $wordcount = absint( $instance['wordcount'] ) )
	      $wordcount = 25;
		if(isset($instance['orderby'])) {$testorder = $instance['orderby'];} else {$testorder = 'rand';}
		if(isset($instance['columns'])) {$columns = $instance['columns'];} else {$columns = '1';}
		if(!empty($instance['speed'])) {$speed = $instance['speed'];} else {$speed = '9000';}
		if(!empty($instance['link'])) {$link = $instance['link'];} else {$link = false;}
		if(!empty($instance['conditional_readmore_link'])) {$conditional_readmore_link = $instance['conditional_readmore_link'];} else {$conditional_readmore_link = 'false';}
		if(!empty($instance['autoplay'])) {$autoplay = $instance['autoplay'];} else {$autoplay = 'true';}
		if(!empty($instance['linktext'])) {$linktext = $instance['linktext'];} else {$linktext = __( 'Read More', 'virtue' );}
		if(!empty($instance['pagelink'])) {$pagelink = $instance['pagelink'];} else {$pagelink = '';}
		if(!empty($instance['full_content'])) {$full_content = $instance['full_content'];} else {$full_content = 'excerpt';}
		if(empty($instance['scroll']) || $instance['scroll'] == 1) {$scroll = 'items:1,';} else {$scroll = '';}
	    if( $full_content == 'excerpt' ) {
	    	$full_content = 'true';
	    }
	    if( $link == 'post' ) {
	    	$link = 'true';
	    }
	    $kt_testimonial_loop = array(
			'columns' 			=> $columns,
			'limit' 			=> $full_content,
			'words' 			=> $wordcount,
			'link' 				=> $link,
			'linktext'			=> $linktext,
			'conditional_link'	=> $conditional_readmore_link,
			'pagelink'			=> $pagelink,
		);
	    $tc = virtue_carousel_column_array( $columns, virtue_display_sidebar() );
	    $t_carousel = new WP_Query(apply_filters('kt_testimonial_carousel_widget_posts_args', array( 
			'post_type' => 'testimonial', 
			'testimonial-group' => $instance['thecat'], 
			'no_found_rows' => true, 
			'posts_per_page' => $number,
			'orderby' => $testorder, 
			'post_status' => 'publish', 
			'ignore_sticky_posts' => true )) );

	    if ($t_carousel->have_posts()) :
	?>
	    <?php echo $before_widget; ?>
	    <?php if ( $title ) echo $before_title . $title . $after_title; ?>
			<div class="fredcarousel kt-testimonial-carousel-container">
				<div id="carouselcontainer-<?php echo esc_attr($carousel_rn);?>" class="rowtight">
					<div id="testimonial-carousel-<?php echo esc_attr($carousel_rn);?>" class="kad-testimonial-carousel kt-slickslider slick-slider kt-content-carousel loading clearfix" data-slider-fade="false" data-slider-dots="false" data-slider-arrows="true"  data-slider-type="content-carousel" data-slider-anim-speed="400" data-slider-scroll="<?php echo esc_attr($scroll);?>" data-slider-auto="<?php echo esc_attr($autoplay);?>" data-slider-speed="<?php echo esc_attr($speed);?>" data-slider-xxl="<?php echo esc_attr($tc['xxl']);?>" data-slider-xl="<?php echo esc_attr($tc['xl']);?>" data-slider-md="<?php echo esc_attr($tc['md']);?>" data-slider-sm="<?php echo esc_attr($tc['sm']);?>" data-slider-xs="<?php echo esc_attr($tc['xs']);?>" data-slider-ss="<?php echo esc_attr($tc['ss']);?>">
					
					<?php  while ($t_carousel->have_posts()) : $t_carousel->the_post(); ?>
						<?php get_template_part( 'templates/content', 'loop-testimonial' ); ?>
					<?php endwhile; ?>
					</div>
				</div>
			</div>
	      
	    <?php echo $after_widget; ?>
	<?php
	    // Reset the global $the_post as this query will have stomped on it

	    endif;

	    wp_reset_postdata();

	  }

	  public function update( $new_instance, $old_instance ) {
	    $instance = $old_instance;
	    $instance['title'] = strip_tags($new_instance['title']);
	    $instance['number'] = (int) $new_instance['number'];
	    $instance['wordcount'] = (int) $new_instance['wordcount'];
	    $instance['autoplay'] = $new_instance['autoplay'];
	    $instance['conditional_readmore_link'] = $new_instance['conditional_readmore_link'];
	    $instance['thecat'] = $new_instance['thecat'];
	    $instance['orderby'] = $new_instance['orderby'];
	    $instance['columns'] = $new_instance['columns'];
	    $instance['full_content'] = $new_instance['full_content'];
	    $instance['linktext'] = $new_instance['linktext'];
	    $instance['link'] = $new_instance['link'];
	    $instance['pagelink'] = $new_instance['pagelink'];
	    $instance['speed'] = (int) $new_instance['speed'];

	    return $instance;
	  }


	  public function form( $instance ) {
	    
	    $title = isset($instance['title']) ? esc_attr($instance['title']) : '';
	    $number = isset($instance['number']) ? absint($instance['number']) : 5;
	    $wordcount = isset($instance['wordcount']) ? absint($instance['wordcount']) : 25;
	    $speed = isset($instance['speed']) ? esc_attr($instance['speed']) : '';
	     $autoplay = isset($instance['autoplay']) ? esc_attr($instance['autoplay']) : 'true';
	     $conditional_readmore_link = isset($instance['conditional_readmore_link']) ? esc_attr($instance['conditional_readmore_link']) : 'false';
	    $linktext = isset($instance['linktext']) ? esc_attr($instance['linktext']) : '';
	    if (isset($instance['orderby'])) { $orderby = esc_attr($instance['orderby']); } else {$orderby = 'random';}
	    if (isset($instance['columns'])) { $columns = esc_attr($instance['columns']); } else {$columns = '1';}
	     if (isset($instance['full_content'])) { $full_content = esc_attr($instance['full_content']); } else {$full_content = 'excerpt';}
	    if (isset($instance['link'])) { $link = esc_attr($instance['link']); } else {$link = 'none';}
	    if (isset($instance['pagelink'])) { $pagelink = esc_attr($instance['pagelink']); } else {$pagelink = '';}
	    $orderoptions = array(array('name' => 'Random', 'slug' => 'rand'), array('name' => 'Menu Order', 'slug' => 'menu_order'), array('name' => 'Date', 'slug' => 'date'));
	    $conditional_readmore_link_options = array(array('name' => 'False', 'slug' => 'false'), array('name' => 'True', 'slug' => 'true'));
	    $autoplay_options = array(array('name' => 'True', 'slug' => 'true'), array('name' => 'False', 'slug' => 'false'));
	    $full_coptions = array(array('name' => 'Excerpt', 'slug' => 'excerpt'), array('name' => 'Full Content', 'slug' => 'full_content'), array('name' => 'Custom Excerpt', 'slug' => 'custom_excerpt'));
	    $linkoptions = array(array('name' => __('none', 'virtue'), 'slug' => 'false'), array('name' => __('Page Link', 'virtue'), 'slug' => 'page'), array('name' => __('Post Link', 'virtue'), 'slug' => 'post'));
	    $testimonial_columns_options = array(array("slug" => "1", "name" => __('1 Column', 'virtue')), array("slug" => "2", "name" => __('2 Columns', 'virtue')), array("slug" => "3", "name" => __('3 Columns', 'virtue')), array("slug" => "4", "name" => __('4 Columns', 'virtue')), array("slug" => "5", "name" => __('5 Columns', 'virtue')), array("slug" => "6", "name" => __('6 Columns', 'virtue')));
	     foreach ($testimonial_columns_options as $testimonial_column_option) {
	      if ($columns == $testimonial_column_option['slug']) { $selected=' selected="selected"';} else { $selected=""; }
	      $testimonial_columns_array[] = '<option value="' . $testimonial_column_option['slug'] .'"' . $selected . '>' . $testimonial_column_option['name'] . '</option>';
	    }
	    $order_options = array();
	    foreach ($orderoptions as $ooption) {
	      if ($orderby==$ooption['slug']) { $selected=' selected="selected"';} else { $selected=""; }
	      $order_options[] = '<option value="' . $ooption['slug'] .'"' . $selected . '>' . $ooption['name'] . '</option>';
	    }
	    $link_options = array();
	    foreach ($linkoptions as $loption) {
	      if ($link==$loption['slug']) { $selected=' selected="selected"';} else { $selected=""; }
	      $link_options[] = '<option value="' . $loption['slug'] .'"' . $selected . '>' . $loption['name'] . '</option>';
	    }
	    $auto_options = array();
	    foreach ($autoplay_options as $aoption) {
	      if ($autoplay==$aoption['slug']) { $selected=' selected="selected"';} else { $selected=""; }
	      $auto_options[] = '<option value="' . $aoption['slug'] .'"' . $selected . '>' . $aoption['name'] . '</option>';
	    }
	    $crlink_options = array();
	    foreach ($conditional_readmore_link_options as $cloption) {
	      if ($conditional_readmore_link==$cloption['slug']) { $selected=' selected="selected"';} else { $selected=""; }
	      $crlink_options[] = '<option value="' . $cloption['slug'] .'"' . $selected . '>' . $cloption['name'] . '</option>';
	    }
	    $full_content_options = array();
	    foreach ($full_coptions as $foption) {
	      if ($full_content==$foption['slug']) { $selected=' selected="selected"';} else { $selected=""; }
	      $full_content_options[] = '<option value="' . $foption['slug'] .'"' . $selected . '>' . $foption['name'] . '</option>';
	    }
	    $pages = get_pages();
	     $pagelink_options = array();
	     foreach ($pages as $poption) {
	      if ($pagelink == get_page_link( $poption->ID )) { $selected=' selected="selected"';} else { $selected=""; }
	      $pagelink_options[] = '<option value="' . get_page_link( $poption->ID ) .'"' . $selected . '>' . $poption->post_title . '</option>';
	    }

	    if (isset($instance['thecat'])) { 
	      $thecat = esc_attr($instance['thecat']);
	    } else {
	      $thecat = '';
	    }
	    $categories= get_terms('testimonial-group');
	    $cat_options = array();
	    $cat_options[] = '<option value="">All</option>';
	 	if(! is_wp_error($categories) && isset($categories) ) {
		    foreach ($categories as $cat) {
		      	if ($thecat==$cat->slug) { $selected=' selected="selected"';} else { $selected=""; }
		      	$cat_options[] = '<option value="' . $cat->slug .'"' . $selected . '>' . $cat->name . '</option>';
		    }
		}

	?>
	    <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'virtue'); ?></label>
	    <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
	    </p>
	    <p>
	    <label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number of posts to show:', 'virtue'); ?></label>
	    <input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" size="3" />
	    </p>
	    <p>
	    <label for="<?php echo $this->get_field_id('wordcount'); ?>"><?php _e('Number of words to show:', 'virtue'); ?></label>
	    <input id="<?php echo $this->get_field_id('wordcount'); ?>" name="<?php echo $this->get_field_name('wordcount'); ?>" type="text" value="<?php echo esc_attr($wordcount); ?>" size="3" /></p>
	    <p>
	    <label for="<?php echo $this->get_field_id('orderby'); ?>"><?php _e('Orderby:', 'virtue'); ?></label>
	    <select id="<?php echo $this->get_field_id('orderby'); ?>" name="<?php echo $this->get_field_name('orderby'); ?>"><?php echo implode('', $order_options); ?></select>
	    </p>
	    <p>
	    <label for="<?php echo $this->get_field_id('thecat'); ?>"><?php _e('Limit to Group (Optional):', 'virtue'); ?></label>
	    <select id="<?php echo $this->get_field_id('thecat'); ?>" name="<?php echo $this->get_field_name('thecat'); ?>"><?php echo implode('', $cat_options); ?></select>
	    </p>
	    <p>
	    <label for="<?php echo $this->get_field_id('columns'); ?>"><?php _e('Carousel Columns', 'virtue'); ?></label>
	    <select id="<?php echo $this->get_field_id('columns'); ?>" name="<?php echo $this->get_field_name('columns'); ?>"><?php echo implode('', $testimonial_columns_array); ?></select>
	    </p>
	    <p><label for="<?php echo $this->get_field_id('speed'); ?>"><?php _e('Carousel Speed (e.g. = 7000)', 'virtue'); ?></label>
	    <input class="widefat" id="<?php echo $this->get_field_id('speed'); ?>" name="<?php echo $this->get_field_name('speed'); ?>" type="text" value="<?php echo esc_attr($speed); ?>" />
	    </p>
	     <p>
	    <label for="<?php echo $this->get_field_id('autoplay'); ?>"><?php _e('Autoplay:', 'virtue'); ?></label>
	    <select id="<?php echo $this->get_field_id('autoplay'); ?>" name="<?php echo $this->get_field_name('autoplay'); ?>"><?php echo implode('', $auto_options); ?></select>
	    </p>
	    <p>
	    <label for="<?php echo $this->get_field_id('link'); ?>"><?php _e('Link Options:', 'virtue'); ?></label>
	    <select id="<?php echo $this->get_field_id('link'); ?>" name="<?php echo $this->get_field_name('link'); ?>"><?php echo implode('', $link_options); ?></select>
	    </p>
	    <p>
	    <label for="<?php echo $this->get_field_id('pagelink'); ?>"><?php _e('If link to page, choose page:', 'virtue'); ?></label>
	    <select id="<?php echo $this->get_field_id('pagelink'); ?>" name="<?php echo $this->get_field_name('pagelink'); ?>"><?php echo implode('', $pagelink_options); ?></select>
	    </p>
	     <p><label for="<?php echo $this->get_field_id('linktext'); ?>"><?php _e('Link text (e.g. = Read More)', 'virtue'); ?></label>
	    <input class="widefat" id="<?php echo $this->get_field_id('linktext'); ?>" name="<?php echo $this->get_field_name('linktext'); ?>" type="text" value="<?php echo $linktext; ?>" />
	    </p>
	    <p>
	    <label for="<?php echo $this->get_field_id('full_content'); ?>"><?php _e('Content:', 'virtue'); ?></label>
	    <select id="<?php echo $this->get_field_id('full_content'); ?>" name="<?php echo $this->get_field_name('full_content'); ?>"><?php echo implode('', $full_content_options); ?></select>
	    </p>
	    <p>
	    <label for="<?php echo $this->get_field_id('conditional_readmore_link'); ?>"><?php _e('Conditional read more link if more content:', 'virtue'); ?></label>
	    <select id="<?php echo $this->get_field_id('conditional_readmore_link'); ?>" name="<?php echo $this->get_field_name('conditional_readmore_link'); ?>"><?php echo implode('', $crlink_options); ?></select>
	    </p>
	  
	<?php
	  }
	}
}