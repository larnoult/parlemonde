<?php
/**
 * Google Map Widget
 *
 * @package Virtue Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'kad_gmap_widget' ) ) {
	/**
	 * Google Map Widget Class
	 *
	 * @category class
	 */
	class kad_gmap_widget extends WP_Widget {

		private static $instance = 0;
		public function __construct() {
			$widget_ops = array( 'classname' => 'virtue_gmap_widget', 'description' => __( 'Adds a google map to a widget area', 'virtue' ) );
			parent::__construct( 'virtue_gmap_widget', __( 'Virtue: Google Map', 'virtue' ), $widget_ops );
		}

		public function widget( $args, $instance ){
			extract( $args );
			if(!empty($instance["location"])) {$location = $instance["location"];} else {$location = '';}
			if(!empty($instance["locationtitle"])) {$locationtitle = $instance["locationtitle"];} else {$locationtitle = '';}
			if(!empty($instance["location2"])) {$location2 = 'address2="'.$instance["location2"].'"';} else {$location2 = '';}
			if(!empty($instance["locationtitle2"])) {$locationtitle2 = 'title2="'.$instance["locationtitle2"].'"';} else {$locationtitle2 = '';}
			if(!empty($instance["location3"])) {$location3 = 'address3="'.$instance["location3"].'"';} else {$location3 = '';}
			if(!empty($instance["locationtitle3"])) {$locationtitle3 = 'title3="'.$instance["locationtitle3"].'"';} else {$locationtitle3 = '';}
			if(!empty($instance["location4"])) {$location4 = 'address4="'.$instance["location4"].'"';} else {$location4 = '';}
			if(!empty($instance["locationtitle4"])) {$locationtitle4 = 'title4="'.$instance["locationtitle4"].'"';} else {$locationtitle4 = '';}
			if(!empty($instance["center"])) {$center = 'center="'.$instance["center"].'"';} else {$center = '';}
			if(!empty($instance['height'])) {$height = 'height="'.esc_attr($instance['height']).'"';} else {$height = '';}
			if(!empty($instance["maptype"])) {$maptype = 'maptype='.$instance["maptype"];} else {$maptype = '';}
			if(!empty($instance["zoom"])) {$zoom = 'zoom='.$instance["zoom"];} else {$zoom = '';}
			if(!empty($instance["scrollwheel"])) {$scrollwheel = 'scrollwheel='.$instance["scrollwheel"];} else {$scrollwheel = '';}

			echo $before_widget;
			echo do_shortcode('[gmap address="' . $location . '" title="' . $locationtitle . '" ' . $height . ' ' . $maptype . ' ' . $zoom . ' ' . $scrollwheel . ' ' . $location2 . ' ' . $location3 . ' ' . $location4 . ' ' . $center . ' ' . $locationtitle2 . ' ' . $locationtitle3 . ' ' . $locationtitle4 . ']');
			echo $after_widget;
		}

	    public function update( $new_instance, $old_instance ) {
			$instance = $old_instance;
			$instance['locationtitle'] = strip_tags( $new_instance['locationtitle'] );
			$instance['location'] = $new_instance['location'];
			$instance['locationtitle2'] = strip_tags( $new_instance['locationtitle2'] );
			$instance['location2'] = $new_instance['location2'];
			$instance['locationtitle3'] = strip_tags( $new_instance['locationtitle3'] );
			$instance['location3'] = $new_instance['location3'];
			$instance['locationtitle4'] = strip_tags( $new_instance['locationtitle4'] );
			$instance['location4'] = $new_instance['location4'];
			$instance['center'] = $new_instance['center'];
			$instance['height'] = (int) $new_instance['height'];
			$instance['maptype'] = $new_instance['maptype']; 
			$instance['zoom'] = $new_instance['zoom'];
			$instance['scrollwheel'] = $new_instance['scrollwheel'];
			return $instance;
		}

		public function form($instance){
			$locationtitle = isset($instance['locationtitle']) ? esc_attr($instance['locationtitle']) : '';
			$locationtitle2 = isset($instance['locationtitle2']) ? esc_attr($instance['locationtitle2']) : '';
			$locationtitle3 = isset($instance['locationtitle3']) ? esc_attr($instance['locationtitle3']) : '';
			$locationtitle4 = isset($instance['locationtitle4']) ? esc_attr($instance['locationtitle4']) : '';
			$height = isset($instance['height']) ? esc_attr($instance['height']) : '';
			if (isset($instance['zoom'])) { $zoom = esc_attr($instance['zoom']); } else {$zoom = '15';}
			if (isset($instance['scrollwheel'])) { $scrollwheel = esc_attr($instance['scrollwheel']); } else {$scrollwheel = 'false';}
			if (isset($instance['loadscripts'])) { $loadscripts = esc_attr($instance['loadscripts']); } else {$loadscripts = "true";}
			if (isset($instance['maptype'])) { $maptype = esc_attr($instance['maptype']); } else {$maptype = 'ROADMAP';}
			$map_type_array = array();
			$zoom_array = array();
			$loadscripts_array = array();
			$scrollwheel_array = array();
			$loadscripts_options = array(array("slug" => "true", "name" => __('True', 'virtue')), array("slug" => "false", "name" => __('False', 'virtue')));
			$scrollwheel_options = array(array("slug" => "false", "name" => __('False', 'virtue')), array("slug" => "true", "name" => __('True', 'virtue')));
			$map_type_options = array(array("slug" => "ROADMAP", "name" => __('ROADMAP', 'virtue')), array("slug" => "HYBRID", "name" => __('HYBRID', 'virtue')), array("slug" => "TERRAIN", "name" => __('TERRAIN', 'virtue')), array("slug" => "SATELLITE", "name" => __('SATELLITE', 'virtue')));
			$zoom_options = array(array("slug" => "1"), array("slug" => "2"), array("slug" => "3"), array("slug" => "4"), array("slug" => "5"), array("slug" => "6"), array("slug" => "7"), array("slug" => "8"), array("slug" => "9"), array("slug" => "10"), array("slug" => "11"), array("slug" => "12"), array("slug" => "13"), array("slug" => "14"), array("slug" => "15"), array("slug" => "16"), array("slug" => "17"), array("slug" => "18"), array("slug" => "19"), array("slug" => "20"), array("slug" => "21"));
			foreach ($zoom_options as $zoom_option) {
			  if ($zoom == $zoom_option['slug']) { $selected=' selected="selected"';} else { $selected=""; }
			  $zoom_array[] = '<option value="' . $zoom_option['slug'] .'"' . $selected . '>' . $zoom_option['slug'] . '</option>';
			}
			foreach ($scrollwheel_options as $scrollwheel_option) {
			  if ($scrollwheel == $scrollwheel_option['slug']) { $selected=' selected="selected"';} else { $selected=""; }
			  $scrollwheel_array[] = '<option value="' . $scrollwheel_option['slug'] .'"' . $selected . '>' . $scrollwheel_option['name'] . '</option>';
			}
			foreach ($map_type_options as $map_type_option) {
			  if ($maptype == $map_type_option['slug']) { $selected=' selected="selected"';} else { $selected=""; }
			  $map_type_array[] = '<option value="' . $map_type_option['slug'] .'"' . $selected . '>' . $map_type_option['name'] . '</option>';
			}
			foreach ($loadscripts_options as $loadscripts_option) {
			  if ($loadscripts == $loadscripts_option['slug']) { $selected=' selected="selected"';} else { $selected=""; }
			  $loadscripts_array[] = '<option value="' . $loadscripts_option['slug'] .'"' . $selected . '>' . $loadscripts_option['name'] . '</option>';
		}
		?>  

    <div id="virtue_gmap_widget<?php echo esc_attr($this->get_field_id('container')); ?>" class="kad_gmap_widget">
            <p>
            <label for="<?php echo $this->get_field_id('locationtitle'); ?>"><?php _e('Marker Title:', 'virtue'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('locationtitle'); ?>" name="<?php echo $this->get_field_name('locationtitle'); ?>" type="text" value="<?php echo $locationtitle; ?>" />
            </p>
            <p>
              <label for="<?php echo $this->get_field_id('location'); ?>"><?php _e('Marker Address', 'virtue'); ?></label><br />
              <textarea name="<?php echo $this->get_field_name('location'); ?>" style="min-height: 50px;" id="<?php echo $this->get_field_id('location'); ?>" class="widefat" ><?php if(!empty($instance['location'])) echo $instance['location']; ?></textarea>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('maptype'); ?>"><?php _e('Map Type', 'virtue'); ?></label><br />
                <select id="<?php echo $this->get_field_id('maptype'); ?>" name="<?php echo $this->get_field_name('maptype'); ?>"><?php echo implode('', $map_type_array);?></select>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('zoom'); ?>"><?php _e('Map Zoom', 'virtue'); ?></label><br />
                <select id="<?php echo $this->get_field_id('zoom'); ?>" style="width:100%; max-width:230px;" name="<?php echo $this->get_field_name('zoom'); ?>"><?php echo implode('', $zoom_array);?></select>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('height'); ?>"><?php _e('Map Height (e.g. = 300)', 'virtue'); ?></label><br />
                <input type="text" class="widefat kad_map_widget_height" name="<?php echo $this->get_field_name('height'); ?>" id="<?php echo $this->get_field_id('height'); ?>" value="<?php echo $height; ?>">
            </p>
            <p>
            <label for="<?php echo $this->get_field_id('locationtitle2'); ?>"><?php _e('Marker Title Two:', 'virtue'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('locationtitle2'); ?>" name="<?php echo $this->get_field_name('locationtitle2'); ?>" type="text" value="<?php echo $locationtitle2; ?>" />
            </p>
            <p>
              <label for="<?php echo $this->get_field_id('location2'); ?>"><?php _e('Marker Address Two', 'virtue'); ?></label><br />
              <textarea name="<?php echo $this->get_field_name('location2'); ?>" style="min-height: 50px;" id="<?php echo $this->get_field_id('location2'); ?>" class="widefat" ><?php if(!empty($instance['location2'])) echo $instance['location2']; ?></textarea>
            </p>
            <p>
            <label for="<?php echo $this->get_field_id('locationtitle3'); ?>"><?php _e('Marker Title Three:', 'virtue'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('locationtitle3'); ?>" name="<?php echo $this->get_field_name('locationtitle3'); ?>" type="text" value="<?php echo $locationtitle3; ?>" />
            </p>
            <p>
              <label for="<?php echo $this->get_field_id('location3'); ?>"><?php _e('Marker Address Three', 'virtue'); ?></label><br />
              <textarea name="<?php echo $this->get_field_name('location3'); ?>" style="min-height: 50px;" id="<?php echo $this->get_field_id('location3'); ?>" class="widefat" ><?php if(!empty($instance['location3'])) echo $instance['location3']; ?></textarea>
            </p>
            <p>
            <label for="<?php echo $this->get_field_id('locationtitle4'); ?>"><?php _e('Marker Title Four:', 'virtue'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('locationtitle4'); ?>" name="<?php echo $this->get_field_name('locationtitle4'); ?>" type="text" value="<?php echo $locationtitle4; ?>" />
            </p>
            <p>
              <label for="<?php echo $this->get_field_id('location4'); ?>"><?php _e('Marker Address Four', 'virtue'); ?></label><br />
              <textarea name="<?php echo $this->get_field_name('location4'); ?>" style="min-height: 50px;" id="<?php echo $this->get_field_id('location4'); ?>" class="widefat" ><?php if(!empty($instance['location4'])) echo $instance['location4']; ?></textarea>
            </p>
            <p>
              <label for="<?php echo $this->get_field_id('center'); ?>"><?php _e('Map Center (defauts to first address)', 'virtue'); ?></label><br />
              <textarea name="<?php echo $this->get_field_name('center'); ?>" style="min-height: 50px;" id="<?php echo $this->get_field_id('center'); ?>" class="widefat" ><?php if(!empty($instance['center'])) echo $instance['center']; ?></textarea>
            </p>
				<p>
					<label for="<?php echo $this->get_field_id('scrollwheel'); ?>"><?php _e('Enable Scroll Zoom on Map', 'virtue'); ?></label><br />
					<select id="<?php echo $this->get_field_id('scrollwheel'); ?>" style="width:100%; max-width:230px;" name="<?php echo $this->get_field_name('scrollwheel'); ?>"><?php echo implode('', $scrollwheel_array);?></select>
				</p>
			</div>

		<?php
		}
	}
}