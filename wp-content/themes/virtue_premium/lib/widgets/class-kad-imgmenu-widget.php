<?php 
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class kad_imgmenu_widget extends WP_Widget{

	private static $instance = 0;
    public function __construct() {
        $widget_ops = array('classname' => 'virtue_imgmenu_widget', 'description' => __('Adds an image background with text, link and hover effect.', 'virtue'));
        parent::__construct('virtue_imgmenu_widget', __('Virtue: Image Menu Item', 'virtue'), $widget_ops);
    }

	public function widget($args, $instance){ 
		extract( $args ); 
		$title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
		if ( ! empty( $instance["description"])) {
			$description = $instance["description"];
		} else {
			$description = '';
		}
		if ( ! empty( $instance['image_uri'] ) ) {
			$image = esc_url( $instance['image_uri'] );
		} else {
			$image = virtue_img_placeholder();
		}
		if ( ! empty( $instance['image_id'] ) ) { 
			$image_id = $instance['image_id'];
		} else {
			$image_id = '';
		}
		if ( ! empty( $instance['height'] ) ) {
			$height = $instance['height'];
		} else {
			$height = '210';
		}
		if ( ! empty( $instance['link'] ) ) {
			$link = $instance['link'];
		} else {
			$link = '';
		}
		if ( ! empty( $instance['height_setting'] ) ) {
			$height_setting = $instance['height_setting'];
		} else {
			$height_setting = 'normal';
		}
		if ( ! empty( $instance['target'] ) && 'true' == $instance['target'] ) {
			$linktarget = '_blank';
		} else {
			$linktarget = '_self';
		}
		
		echo $before_widget;
			echo virtue_image_menu_output_builder( $image_id, $height_setting, $height, $link, '1', $linktarget, $title, $description, 'kadence_img_menu_widget', $image, $args['widget_id']);
		echo $after_widget;

	}

    public function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['image_uri'] = esc_url_raw( $new_instance['image_uri'] );
		$instance['image_id'] = (int) $new_instance['image_id'];
		$instance['description'] = sanitize_text_field( $new_instance['description'] );
		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		$instance['link'] = esc_url_raw( $new_instance['link'] );
		$instance['height'] = (int) $new_instance['height'];
		$instance['target'] = sanitize_text_field( $new_instance['target'] );
		$instance['height_setting'] = sanitize_text_field( $new_instance['height_setting'] );
		return $instance;
	}
  	public function form($instance){ 
		$title = isset($instance['title']) ? esc_attr($instance['title']) : '';
		$link = isset($instance['link']) ? esc_attr($instance['link']) : '';
		$height = isset($instance['height']) ? esc_attr($instance['height']) : '';
		$image_uri = isset($instance['image_uri']) ? esc_attr($instance['image_uri']) : '';
		$image_id = isset( $instance['image_id'] ) ? esc_attr( $instance['image_id'] ) : '';
		if ( isset( $instance['target'] ) ) {
			$target = esc_attr($instance['target']);
		} else {
			$target = 'false';
		}
		if ( isset( $instance['height_setting'] ) ) {
			$height_setting = esc_attr($instance['height_setting']);
		} else {
			$height_setting = 'normal';
		}
		$height_options = array( array("slug" => "normal", "name" => __('Height setting Above', 'virtue') ), array("slug" => "imgsize", "name" => __('Image Size', 'virtue') ) );
		$target_options = array( array("slug" => "false", "name" => __('Self', 'virtue') ), array("slug" => "true", "name" => __('New Window', 'virtue') ) );
		foreach ( $target_options as $target_option ) {
			if ( $target == $target_option['slug'] ) { 
				$selected=' selected="selected"'; 
			} else { 
				$selected="";
			}
			$target_array[] = '<option value="' . $target_option['slug'] .'"' . $selected . '>' . $target_option['name'] . '</option>';
		}
		foreach ($height_options as $height_option) {
			if ( $height_setting == $height_option['slug'] ) { 
				$selected=' selected="selected"'; 
			} else { 
				$selected= '';
			}
			$height_array[] = '<option value="' . $height_option['slug'] .'"' . $selected . '>' . $height_option['name'] . '</option>';
		}
		?>  

		<div id="virtue_imgmenu_widget<?php echo esc_attr($this->get_field_id('container')); ?>" class="kad_img_upload_widget kad_infobox_widget">
			<p>
				<img class="kad_custom_media_image" src="<?php if(!empty($instance['image_uri'])){echo $instance['image_uri'];} ?>" style="margin:0;padding:0;max-width:100px;display:block" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('image_uri'); ?>"><?php _e('Upload an image', 'virtue'); ?></label><br />
				<input type="text" class="widefat kad_custom_media_url" name="<?php echo $this->get_field_name('image_uri'); ?>" id="<?php echo $this->get_field_id('image_uri'); ?>" value="<?php echo esc_attr( $image_uri ); ?>">
				<input type="hidden" value="<?php echo esc_attr( $image_id ); ?>" class="kad_custom_media_id" name="<?php echo $this->get_field_name('image_id'); ?>" id="<?php echo $this->get_field_id('image_id'); ?>" />
				<input type="button" value="<?php _e('Upload', 'virtue'); ?>" class="button kad_custom_media_upload" id="kad_custom_image_uploader" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('height'); ?>"><?php _e('Item Height (e.g. = 220)', 'virtue'); ?></label><br />
				<input type="number" class="widefat kad_img_widget_link" name="<?php echo $this->get_field_name('height'); ?>" id="<?php echo $this->get_field_id('height'); ?>" style="width: 70px;" value="<?php echo $height; ?>">
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('height_setting'); ?>"><?php _e('Height set by:', 'virtue'); ?></label><br />
				<select id="<?php echo $this->get_field_id('height_setting'); ?>" style="width:100%; max-width:230px;" name="<?php echo $this->get_field_name('height_setting'); ?>"><?php echo implode('', $height_array);?></select>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'virtue'); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
			</p>
			 <p>
				<label for="<?php echo $this->get_field_id('description'); ?>"><?php _e('Description', 'virtue'); ?></label><br />
				<textarea name="<?php echo $this->get_field_name('description'); ?>" style="min-height: 20px;" id="<?php echo $this->get_field_id('description'); ?>" class="widefat" ><?php if(!empty($instance['description'])) echo $instance['description']; ?></textarea>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('link'); ?>"><?php _e('Link:', 'virtue'); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id('link'); ?>" name="<?php echo $this->get_field_name('link'); ?>" type="text" value="<?php echo $link; ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('target'); ?>"><?php _e('Link Target', 'virtue'); ?></label><br />
				<select id="<?php echo $this->get_field_id('target'); ?>" style="width:100%; max-width:230px;" name="<?php echo $this->get_field_name('target'); ?>"><?php echo implode('', $target_array);?></select>
			</p>
		</div>

	<?php 
	}
}
