<?php 
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class Simple_About_With_Image extends WP_Widget{
    private static $instance = 0;
    public function __construct() {
        $widget_ops = array('classname' => 'virtue_about_with_image', 'description' => __('This allows for an image and a simple about text.', 'virtue'));
        parent::__construct('virtue_about_with_image', __('Virtue: Image', 'virtue'), $widget_ops);
    }

    public function widget($args, $instance){ 
    	if ( ! isset( $args['widget_id'] ) ) {
	      $args['widget_id'] = $this->id;
	    }
        extract( $args );
        if (!empty($instance['image_link_open']) && $instance['image_link_open'] == "none") {
          $uselink = false;
          $link = '';
          $linktype = '';
        } else if(empty($instance['image_link_open']) || $instance['image_link_open'] == "lightbox") {
          $uselink = true;
          $link = ( isset( $instance['image_uri'] ) ? esc_url( $instance['image_uri'] ) : '' );
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
        if(!empty($instance['alttext'])) {
          	$alt = $instance['alttext'];
        } else if(!empty($instance['image_id'])) {
          	$alt = esc_attr( get_post_meta($instance['image_id'], '_wp_attachment_image_alt', true) );
        } else {
          	$alt = '';
        }
        if(!empty($instance['box_shadow'])) {
          $shadow = 'kt-image-shadow-'.$instance['box_shadow'];
        } else {
          $shadow = '';
        }
        if(!empty($instance['align'])) {
          $align = 'kt-image-align-'.$instance['align'];
        } else {
          $align = 'kt-image-align-center';
        }
        if(!empty($instance['image_shape'])) {
          $shape = 'kt-image-shape-'.$instance['image_shape'];
        } else {
          $shape = '';
        }
        if(isset($instance['image_size']) && !empty($instance['image_size'])) {
        	$size = $instance['image_size'];
        } else {
        	$size = 'full';
        }
        if('custom' == $size ) {
			$maxwidth = $instance['width'];
		} else {
			$maxwidth = get_option( "{$size}_size_w" );
		}

        echo $before_widget; 
            echo '<div class="kad_img_upload_widget kt-shape-type-'.esc_attr($shape).' '.esc_attr($align).' kt-image-widget-'.esc_attr($args['widget_id']).'">';
            	if( isset( $instance['image_shape'] ) && 'standard' != $instance['image_shape'] ) {
            		if('center' == $instance['align']) {
            			$margin = '0 auto';
            		} else if( 'right' == $instance['align'] ) {
            			$margin = '0 0 0 auto';
            		} else {
            			$margin = '0';
            		}
            		echo '<div style="max-width:'.esc_attr( $maxwidth ).'px; margin:'.esc_attr( $margin ).'">';
            	}
               	if($uselink == true) {
               		echo '<a href="'.esc_url($link).'" '.$linktype.'>';
               	} 
               	echo '<div class="kt-image-contain '.esc_attr($shadow).' '.esc_attr($shape).'">';
               	echo '<div class="kt-image-inner-contain">';
               		if(empty($instance['image_id'])){
               			if(isset($instance['image_uri'])) {
               				echo '<img src="'.esc_url($instance['image_uri']).'">';
               			}
               		} else if($size == 'custom') {
	                	$img = virtue_get_image_array( $instance['width'], $instance['height'], true, null, null, $instance['image_id'], true );
	                	echo '<img src="'.esc_url($img['src']).'" width="'.esc_attr($img['width']).'" height="'.esc_attr($img['height']).'" '.$img['srcset'].' class="'.esc_attr($img['class']).'" itemprop="contentUrl" alt="'.esc_attr($img['alt']).'">';
	                } else {
	                	echo wp_get_attachment_image( $instance['image_id'], $size ); 
	                }
	                echo '</div>';
                echo '</div>';
                if($uselink == true) {
                	echo '</a>'; 
                }
               	if ( ! empty($instance['text'] ) ) {
               		echo '<div class="virtue_image_widget_caption kadence_image_widget_caption">'.wp_kses_post($instance['text']).'</div>';
               	}
				if ( isset( $instance['image_shape'] ) && 'standard' != $instance['image_shape'] ) {
              		echo '</div>';
              	}
            echo '</div>';
        echo $after_widget; 
    }

    public function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['text'] = wp_kses_post($new_instance['text']);
        $instance['image_id'] = (int) $new_instance['image_id'];
        $instance['alttext'] = wp_kses_post($new_instance['alttext']);
        $instance['image_shape'] = sanitize_text_field($new_instance['image_shape']);
        $instance['box_shadow'] = sanitize_text_field($new_instance['box_shadow']);
        $instance['image_uri'] = esc_url_raw( $new_instance['image_uri'] );
        $instance['image_link'] = esc_url_raw( $new_instance['image_link'] );
        $instance['image_link_open'] = sanitize_text_field($new_instance['image_link_open']);
        $instance['image_size'] = sanitize_text_field($new_instance['image_size']);
        $instance['align'] = sanitize_text_field($new_instance['align']);
        $instance['width'] = (int) $new_instance['width'];
        $instance['height'] = (int) $new_instance['height'];
        return $instance;
    }

  public function form($instance){ 
    $image_uri = isset($instance['image_uri']) ? esc_attr($instance['image_uri']) : '';
    $image_link = isset($instance['image_link']) ? esc_attr($instance['image_link']) : '';
    $width = isset($instance['width']) ? esc_attr($instance['width']) : '';
    $height = isset($instance['height']) ? esc_attr($instance['height']) : '';
    $image_id = isset($instance['image_id']) ? esc_attr($instance['image_id']) : '';
    $box_shadow = isset($instance['box_shadow']) ? esc_attr($instance['box_shadow']) : 'none';
    $image_shape = isset($instance['image_shape']) ? esc_attr($instance['image_shape']) : 'standard';
    $align = isset($instance['align']) ? esc_attr($instance['align']) : 'center';
    if (isset($instance['image_link_open'])) { $image_link_open = esc_attr($instance['image_link_open']); } else {$image_link_open = 'lightbox';}
    if (isset($instance['image_size'])) { $image_size = esc_attr($instance['image_size']); } else {$image_size = 'full';}
    $link_options = array();
    $link_options_array = array();
    $shadow_options_array = array();
    $shape_options_array = array();
    $align_options_array = array();
    $sizes = virtue_basic_image_sizes();
    $link_options[] = array("slug" => "lightbox", "name" => __('Lightbox', 'virtue'));
    $link_options[] = array("slug" => "_blank", "name" => __('New Window', 'virtue'));
    $link_options[] = array("slug" => "_self", "name" => __('Same Window', 'virtue'));
    $link_options[] = array("slug" => "none", "name" => __('No Link', 'virtue'));
    $shadow_options = array("none" => __('None', 'virtue'), "small" => __('Small', 'virtue'), "medium" => __('Medium', 'virtue'), "large" => __('Large', 'virtue'), "small_below" => __('Small Below', 'virtue'), "medium_below" => __('Medium Below', 'virtue'), "large_below" => __('Large Below', 'virtue'));
    $shape_options = array("standard" => __('Standard', 'virtue'), "square" => __('Square', 'virtue'), "circle" => __('Circle', 'virtue'), "diamond" => __('Diamond', 'virtue'), "hexagon" => __('Hexagon', 'virtue'), "octogon" => __('Octogon', 'virtue'));
    $align_options = array("center" => __('Center', 'virtue'), "left" => __('Left', 'virtue'), "right" => __('Right', 'virtue'));
    foreach ($align_options as $align_slug => $align_value) {
      if ($align == $align_slug) { $selected=' selected="selected"';} else { $selected=""; }
      $align_options_array[] = '<option value="' . $align_slug .'"' . $selected . '>' . $align_value . '</option>';
    }
    foreach ($shape_options as $shape_slug => $shape_value) {
      if ($image_shape == $shape_slug) { $selected=' selected="selected"';} else { $selected=""; }
      $shape_options_array[] = '<option value="' . $shape_slug .'"' . $selected . '>' . $shape_value . '</option>';
    }
    foreach ($shadow_options as $shadow_slug => $shadow_value) {
      if ($box_shadow == $shadow_slug) { $selected=' selected="selected"';} else { $selected=""; }
      $shadow_options_array[] = '<option value="' . $shadow_slug .'"' . $selected . '>' . $shadow_value . '</option>';
    }
    foreach ($link_options as $link_option) {
      if ($image_link_open == $link_option['slug']) { $selected=' selected="selected"';} else { $selected=""; }
      $link_options_array[] = '<option value="' . $link_option['slug'] .'"' . $selected . '>' . $link_option['name'] . '</option>';
    }
    foreach ($sizes as $size => $size_info) {
      	if ($image_size == $size) { $selected=' selected="selected"';} else { $selected=""; }
      		$sizes_array[] = '<option value="' . $size .'"' . $selected . '>' . $size_info .'</option>';
    }
    ?>
  <div class="kad_img_upload_widget">
    <p>
        <img class="kad_custom_media_image" src="<?php if(!empty($instance['image_uri'])){echo esc_attr($instance['image_uri']);} ?>" style="margin:0;padding:0;max-width:100px;display:block" />
    </p>
    <p>
        <label for="<?php echo $this->get_field_id('image_uri'); ?>"><?php _e('Image URL', 'virtue'); ?></label><br />
        <input type="text" class="widefat kad_custom_media_url" name="<?php echo $this->get_field_name('image_uri'); ?>" id="<?php echo $this->get_field_id('image_uri'); ?>" value="<?php echo esc_attr($image_uri); ?>">
        <input type="hidden" value="<?php echo esc_attr($image_id); ?>" class="kad_custom_media_id" name="<?php echo $this->get_field_name('image_id'); ?>" id="<?php echo $this->get_field_id('image_id'); ?>" />
        <input type="button" value="<?php _e('Upload', 'virtue'); ?>" class="button kad_custom_media_upload" id="kad_custom_image_uploader" />
    </p>
    <p>
        <label for="<?php echo $this->get_field_id('align'); ?>"><?php _e('Align Image', 'virtue'); ?></label><br />
        <select id="<?php echo $this->get_field_id('align'); ?>" name="<?php echo $this->get_field_name('align'); ?>"><?php echo implode('', $align_options_array);?></select>
    </p>
    <p>
        <label for="<?php echo $this->get_field_id('image_shape'); ?>"><?php _e('Image Shape', 'virtue'); ?></label><br />
        <select id="<?php echo $this->get_field_id('image_shape'); ?>" name="<?php echo $this->get_field_name('image_shape'); ?>"><?php echo implode('', $shape_options_array);?></select>
    </p>
    <p>
        <label for="<?php echo $this->get_field_id('image_size'); ?>"><?php _e('Image size', 'virtue'); ?></label><br />
        <select id="<?php echo $this->get_field_id('image_size'); ?>" name="<?php echo $this->get_field_name('image_size'); ?>"><?php echo implode('', $sizes_array);?></select>
    </p>
    <p>
        <label for="<?php echo $this->get_field_id('width'); ?>"><?php _e('Custom Width', 'virtue'); ?></label><br />
        <input type="text" class="widefat kad_img_widget_link" name="<?php echo $this->get_field_name('width'); ?>" id="<?php echo $this->get_field_id('width'); ?>" value="<?php echo esc_attr($width); ?>">
    </p>
    <p>
        <label for="<?php echo $this->get_field_id('height'); ?>"><?php _e('Custom Height', 'virtue'); ?></label><br />
        <input type="text" class="widefat kad_img_widget_link" name="<?php echo $this->get_field_name('height'); ?>" id="<?php echo $this->get_field_id('height'); ?>" value="<?php echo esc_attr($height); ?>">
    </p>
    <p>
        <label for="<?php echo $this->get_field_id('image_link_open'); ?>"><?php _e('Image opens in', 'virtue'); ?></label><br />
        <select id="<?php echo $this->get_field_id('image_link_open'); ?>" name="<?php echo $this->get_field_name('image_link_open'); ?>"><?php echo implode('', $link_options_array);?></select>
    </p>
    <p>
        <label for="<?php echo $this->get_field_id('image_link'); ?>"><?php _e('Image Link (optional)', 'virtue'); ?></label><br />
        <input type="text" class="widefat kad_img_widget_link" name="<?php echo $this->get_field_name('image_link'); ?>" id="<?php echo $this->get_field_id('image_link'); ?>" value="<?php echo esc_attr($image_link); ?>">
    </p>
    <p>
        <label for="<?php echo $this->get_field_id('box_shadow'); ?>"><?php _e('Image Shadow (only works with basic shapes)', 'virtue'); ?></label><br />
        <select id="<?php echo $this->get_field_id('box_shadow'); ?>" name="<?php echo $this->get_field_name('box_shadow'); ?>"><?php echo implode('', $shadow_options_array);?></select>
    </p>
     <p>
      <label for="<?php echo $this->get_field_id('alttext'); ?>"><?php _e('Image Alt Text (optional)', 'virtue'); ?></label><br />
      <textarea name="<?php echo $this->get_field_name('alttext'); ?>" id="<?php echo $this->get_field_id('alttext'); ?>" class="widefat" ><?php if(!empty($instance['alttext'])) echo $instance['alttext']; ?></textarea>
    </p>
    <p>
      <label for="<?php echo $this->get_field_id('text'); ?>"><?php _e('Text/Caption (optional)', 'virtue'); ?></label><br />
      <textarea name="<?php echo $this->get_field_name('text'); ?>" id="<?php echo $this->get_field_id('text'); ?>" class="widefat" ><?php if(!empty($instance['text'])) echo esc_textarea($instance['text']); ?></textarea>
    </p>
  </div>
    <?php
  }

}
