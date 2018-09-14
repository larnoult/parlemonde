<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class kad_split_content_widget extends WP_Widget {
	private static $instance = 0;
	public function __construct() {
		$widget_ops = array('classname' => 'virtue_split_content_widget', 'description' => __('Adds an column with an image beside a content field.', 'virtue'));
		parent::__construct('virtue_split_content_widget', __('Virtue: Split Content', 'virtue'), $widget_ops);
	}

	public function widget($args, $instance){ 
		extract( $args ); 

		$title 						= ( ! empty( $instance["title"] ) ? $instance["title"] : '' );
		$description 				= ( ! empty( $instance["description"] ) ? $instance["description"] : '' );
		$description_max_width 		= ( ! empty( $instance['description_max_width'] ) ? 'description_max_width="'.$instance['description_max_width'].'" ' : '' );
		$description_align 			= ( ! empty( $instance['description_align'] ) ? 'description_align="'.$instance['description_align'].'" ' : '' );
		$description 				= ( ! empty( $instance['filter'] ) ?  wpautop( $description ) : $description );
		$image 						= ( ! empty( $instance['image_url'] ) ? $instance['image_url'] : '' );
		$image_id 					= ( ! empty( $instance['image_id'] ) ? 'image_id="'.$instance['image_id'].'"' : '' );
		$img_link 					= ( ! empty( $instance["img_link"] ) ? 'image_link="'.$instance["img_link"].'" ' : '' );
		$img_align 					= ( ! empty( $instance["img_align"] ) ? 'imageside="'.$instance["img_align"].'" ' : '' );
		$img_background_color 		= ( ! empty( $instance['img_background_color'] ) ? 'img_background="'.$instance['img_background_color'].'" ' : '' );
		$content_background_color 	= ( ! empty( $instance['content_background_color'] ) ? 'content_background="'.$instance['content_background_color'].'" ' : '' );
		$height 					= ( ! empty( $instance['height'] ) ? $instance['height'] : '500' );
		$btn_text 					= ( ! empty( $instance['btn_text'] ) ? $instance['btn_text'] : '' );
		$btn_link 					= ( ! empty( $instance['btn_link'] ) ? $instance['btn_link'] : '#' );
		$linktarget 				= ( ! empty( $instance['link_target'] ) ? 'link_target="'.$instance['link_target'].'"' : '' );
		$btn_link_target 			= ( ! empty( $instance['link_target'] ) ? 'link_target="'.$instance['link_target'].'"' : '' );
		$cover 						= ( ! empty( $instance['img_cover'] ) ? 'image_cover="true" ' : '' );
		if ( !empty( $instance['content_text_color'] ) ) {
			$content_text_color = 'text_color="'.$instance['content_text_color'].'"';
			$text_color = 'color:'.$instance['content_text_color'].';';
		} else {
			$content_text_color = '';
			$text_color = '';
		}
	
		echo $before_widget;
			$output = '[kt_imgsplit image="'.$image.'" height="'.$height.'" '.$img_align.' '.$cover.' '.$description_max_width.' '.$image_id.' id="'.$args['widget_id'].'" '.$img_background_color.' '.$img_link.' '.$linktarget.' '.$content_text_color .' '.$description_align.' '.$content_background_color.']';
				if ( ! empty( $title ) ) { $output .= '<h2 class="kt_imgsplit_title" style="'.esc_attr($text_color).'">'.$title.'</h2>'; }
				if ( ! empty( $description ) ) {$output .= '<div class="kt_imgsplit_content" style="'.esc_attr($text_color).'">'.$description.'</div>'; }
				if ( ! empty( $btn_text ) ) { $output .= '<a href="'.$btn_link.'" '.$btn_link_target.' class="kt_imgsplit_btn kad-btn kad-btn-primary">'.$btn_text.'</a>'; }
			$output .= '[/kt_imgsplit]'; 
			echo do_shortcode( $output );

		echo $after_widget;

	}

	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['image_url'] 					= esc_url_raw( $new_instance['image_url'] );
		$instance['image_id'] 					= (int) $new_instance['image_id'];
		$instance['description'] 				= wp_kses_post( $new_instance['description'] );
		$instance['description_max_width'] 		= (int) $new_instance['description_max_width'];
		$instance['description_align'] 			= sanitize_text_field( $new_instance['description_align'] );
		$instance['title'] 						= sanitize_text_field( $new_instance['title'] );
		$instance['btn_link'] 					= sanitize_text_field( $new_instance['btn_link'] );
		$instance['btn_text'] 					= sanitize_text_field( $new_instance['btn_text'] );
		$instance['img_link'] 					= sanitize_text_field( $new_instance['img_link'] );
		$instance['height'] 					= (int) $new_instance['height'];
		$instance['link_target'] 				= sanitize_text_field( $new_instance['link_target'] );
		$instance['img_align'] 					= sanitize_text_field( $new_instance['img_align'] );
		$instance['filter'] 					= ! empty( $new_instance['filter'] );
		$instance['img_cover'] 					= ! empty( $new_instance['img_cover'] );
		$instance['img_background_color']	 	= sanitize_text_field( $new_instance['img_background_color'] );
		$instance['content_background_color'] 	= sanitize_text_field( $new_instance['content_background_color'] );
		$instance['content_text_color'] 		= sanitize_text_field( $new_instance['content_text_color'] );

        return $instance;
    }

	public function form( $instance ) { 
		$title 						= isset( $instance['title'] ) ? $instance['title'] : '';
		$description 				= isset( $instance['description'] ) ? $instance['description'] : '';
		$description_max_width 		= isset( $instance['description_max_width'] ) ? $instance['description_max_width'] : '';
    	$description_align 			= isset( $instance['description_align'] ) ? $instance['description_align'] : 'default';
		$filter 					= isset( $instance['filter'] ) ? $instance['filter'] : 0;
		$btn_text 					= isset( $instance['btn_text'] ) ? $instance['btn_text'] : '';
		$btn_link 					= isset( $instance['btn_link'] ) ? $instance['btn_link'] : '';
		$img_link 					= isset( $instance['img_link'] ) ? $instance['img_link'] : '';
		$height 					= isset( $instance['height'] ) ? $instance['height'] : '500';
		$cover 						= isset( $instance['img_cover'] ) ? $instance['img_cover'] : 0;
		$image_url 					= isset( $instance['image_url'] ) ? $instance['image_url'] : '';
		$image_id 					= isset( $instance['image_id'] ) ? $instance['image_id'] : '';
		$img_background_color 		= isset( $instance['img_background_color'] ) ? $instance['img_background_color'] : '';
		$content_background_color 	= isset( $instance['content_background_color'] ) ? $instance['content_background_color'] : '';
		$content_text_color 		= isset( $instance['content_text_color']) ? $instance['content_text_color'] : '';
		$img_align 					= isset( $instance['img_align'] ) ? $instance['img_align'] : 'left';
		$link_target 				= isset( $instance['link_target']) ? $instance['link_target'] : '_self';

		// Set up arrays
		$target_options = array( array( "slug" => "_self", "name" => __('Self', 'virtue') ), array("slug" => "_blank", "name" => __('New Window', 'virtue')));
		$align_options = array(array("slug" => "left", "name" => __('Left', 'virtue')), array("slug" => "right", "name" => __('Right', 'virtue')));
		$text_align_options = array(array("slug" => "default", "name" => __('Default', 'virtue')), array("slug" => "left", "name" => __('Left', 'virtue')), array("slug" => "right", "name" => __('Right', 'virtue')), array("slug" => "center", "name" => __('Center', 'virtue')), array("slug" => "justify", "name" => __('Justify', 'virtue')));
		foreach ($target_options as $target_option) {
			if ($link_target == $target_option['slug']) { $selected=' selected="selected"';} else { $selected=""; }
			$target_array[] = '<option value="' . $target_option['slug'] .'"' . $selected . '>' . $target_option['name'] . '</option>';
		}
		foreach ($text_align_options as $talign_option) {
			if ($description_align == $talign_option['slug']) { $selected=' selected="selected"';} else { $selected=""; }
			$talign_array[] = '<option value="' . $talign_option['slug'] .'"' . $selected . '>' . $talign_option['name'] . '</option>';
		}
		foreach ($align_options as $align_option) {
			if ($img_align == $align_option['slug']) { $selected=' selected="selected"';} else { $selected=""; }
			$align_array[] = '<option value="' . $align_option['slug'] .'"' . $selected . '>' . $align_option['name'] . '</option>';
		}
		?>  

		<div id="virtue_split_content_widget<?php echo esc_attr($this->get_field_id('container')); ?>" class="kad_img_upload_widget kad_infobox_widget">
			<p>
				<label for="<?php echo $this->get_field_id('height'); ?>"><?php _e('Height', 'virtue'); ?></label><br />
				<input type="number" class="widefat kad_img_widget_link" name="<?php echo $this->get_field_name('height'); ?>" id="<?php echo $this->get_field_id('height'); ?>" style="width: 70px;" value="<?php echo esc_attr( $height ); ?>">
			</p>
			<h4><?php _e('Image content', 'virtue');?></h4>
			<p>
				<img class="kad_custom_media_image" src="<?php echo esc_url( $image_url ); ?>" style="margin:0;padding:0;max-width:100px;display:block" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('image_url'); ?>"><?php _e('Upload an image', 'virtue'); ?></label><br />
				<input type="text" class="widefat kad_custom_media_url" name="<?php echo $this->get_field_name('image_url'); ?>" id="<?php echo $this->get_field_id('image_url'); ?>" value="<?php echo $image_url; ?>">
				<input type="hidden" class="widefat kad_custom_media_id" name="<?php echo $this->get_field_name('image_id'); ?>" id="<?php echo $this->get_field_id('image_id'); ?>" value="<?php echo esc_attr( $image_id ); ?>">
				<input type="button" value="<?php _e('Upload', 'virtue'); ?>" class="button kad_custom_media_upload" id="kad_custom_image_uploader" />
			</p>
			<p>
				<input id="<?php echo $this->get_field_id('img_cover'); ?>" name="<?php echo $this->get_field_name('img_cover'); ?>" type="checkbox"<?php checked( $cover ); ?> />&nbsp;<label for="<?php echo $this->get_field_id('img_cover'); ?>"><?php _e('Force image to cover whole area', 'virtue'); ?></label></p>

			<p>
				<label for="<?php echo $this->get_field_id('img_link'); ?>"><?php _e('Image Link (optional)', 'virtue'); ?></label><br />
				<input type="text" class="widefat" name="<?php echo $this->get_field_name('img_link'); ?>" id="<?php echo $this->get_field_id('img_link'); ?>"value="<?php echo esc_attr( $img_link); ?>">
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('img_background_color'); ?>"><?php _e('Image Background Color (optional)', 'virtue'); ?></label><br />
				<input type="text" class="widefat kad-widget-colorpicker" name="<?php echo $this->get_field_name('img_background_color'); ?>" id="<?php echo $this->get_field_id('img_background_color'); ?>" value="<?php echo esc_attr( $img_background_color ); ?>">
			</p>
			<p>
			<label for="<?php echo $this->get_field_id('img_align'); ?>"><?php _e('Image align:', 'virtue'); ?></label><br />
			<select id="<?php echo $this->get_field_id('img_align'); ?>" style="width:100%; max-width:230px;" name="<?php echo $this->get_field_name('img_align'); ?>"><?php echo implode('', $align_array);?></select>
			</p>
			<h4><?php _e('Text content', 'virtue');?></h4>
			<p>
				<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'virtue'); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('description'); ?>"><?php _e('Description', 'virtue'); ?></label><br />
				<textarea class="widefat" rows="16" cols="20" id="<?php echo $this->get_field_id('description'); ?>" name="<?php echo $this->get_field_name('description'); ?>" ><?php echo esc_textarea( $description ); ?></textarea>
			</p>
			<p><input id="<?php echo $this->get_field_id('filter'); ?>" name="<?php echo $this->get_field_name('filter'); ?>" type="checkbox"<?php checked( $filter ); ?> />&nbsp;<label for="<?php echo $this->get_field_id('filter'); ?>"><?php _e('Automatically add paragraphs', 'virtue'); ?></label></p>
			<p>
				<label for="<?php echo $this->get_field_id('description_max_width'); ?>"><?php _e('Text max width', 'virtue'); ?></label><br />
				<input type="number" class="widefat" name="<?php echo $this->get_field_name('description_max_width'); ?>" id="<?php echo $this->get_field_id('description_max_width'); ?>" style="width: 70px;" value="<?php echo esc_attr( $description_max_width ); ?>">
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('description_align'); ?>"><?php _e('Text align:', 'virtue'); ?></label><br />
				<select id="<?php echo $this->get_field_id('description_align'); ?>" style="width:100%; max-width:230px;" name="<?php echo $this->get_field_name('description_align'); ?>"><?php echo implode('', $talign_array);?></select>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('btn_text'); ?>"><?php _e('Button Text (optional)', 'virtue'); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id('btn_text'); ?>" name="<?php echo $this->get_field_name('btn_text'); ?>" type="text" value="<?php echo esc_attr( $btn_text ); ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('btn_link'); ?>"><?php _e('Button Link (optional)', 'virtue'); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id('btn_link'); ?>" name="<?php echo $this->get_field_name('btn_link'); ?>" type="text" value="<?php echo esc_attr( $btn_link ); ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('link_target'); ?>"><?php _e('Link link_Target (optional)', 'virtue'); ?></label><br />
				<select id="<?php echo $this->get_field_id('link_target'); ?>" style="width:100%; max-width:230px;" name="<?php echo $this->get_field_name('link_target'); ?>"><?php echo implode('', $target_array);?></select>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('content_background_color'); ?>"><?php _e('Text Content Background Color (optional)', 'virtue'); ?></label><br />
				<input type="text" class="widefat kad-widget-colorpicker" name="<?php echo $this->get_field_name('content_background_color'); ?>" id="<?php echo $this->get_field_id('content_background_color'); ?>" value="<?php echo esc_attr( $content_background_color); ?>">
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('content_text_color'); ?>"><?php _e('Text Content Color (optional)', 'virtue'); ?></label><br />
				<input type="text" class="widefat kad-widget-colorpicker" name="<?php echo $this->get_field_name('content_text_color'); ?>" id="<?php echo $this->get_field_id('content_text_color'); ?>" value="<?php echo esc_attr( $content_text_color ); ?>">
			</p>
		</div>

<?php } }