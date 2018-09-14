<?php
if(!class_exists('Cyclone_Slider_Widget')):

	/**
	* Class for Cyclone Slider widget
	*/
	class Cyclone_Slider_Widget extends WP_Widget {
		
		/**
		* Constructor
		*/
		public function __construct() {
			parent::__construct(
				'cyclone-slider-widget', // Base ID
				__( 'Cyclone Slider Widget', 'cycloneslider' ), // Name
				array( 'description' => __( 'Widget for displaying sliders.', 'cycloneslider' ), ) // Args
			);
		}
		
		/**
		* Widget output
		*/
		function widget( $args, $instance ) {
			extract($args, EXTR_SKIP);
	
			echo $before_widget;
			
			if ( !empty($instance['title']) ) {
				$title = apply_filters('widget_title', $instance['title']);
				echo $before_title . $title . $after_title;
			}
			$slideshow = '';
			if ( !empty($instance['slideshow']) ) {
				$slideshow = $instance['slideshow'];
				if( function_exists('cyclone_slider') ) cyclone_slider($slideshow);
			}
			echo $after_widget;
		}
		
		/**
		* Widget on save
		*/
		function update( $new_instance, $old_instance ) {
			$instance = $old_instance;
			
			$instance['title'] = strip_tags($new_instance['title']);
			$instance['slideshow'] = strip_tags($new_instance['slideshow']);
			
			return $instance;
	
		}
		
		/**
		* Admin form
		*/
		function form( $instance ) {
			$defaults = array(
				'title' => '',
				'slideshow'=>''
			);
			$instance = wp_parse_args( (array) $instance, $defaults );
			
			$slideshow = $instance['slideshow'];
	
	?>
			<p>
				<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'cycloneslider'); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php esc_attr_e($instance['title']); ?>" />
			</p>
			<p>
			<?php
			$my_query = new WP_Query(
				array(
					'post_type' => 'cycloneslider',
					'order'=>'ASC',
					'posts_per_page' => -1,
				)
			);
			if($my_query->have_posts()):
			?>
				<label for="<?php echo $this->get_field_id('slideshow'); ?>"><?php _e('Select a Slider:', 'cycloneslider'); ?></label>
				<select class="widefat" id="<?php echo $this->get_field_id('slideshow'); ?>" name="<?php echo $this->get_field_name('slideshow'); ?>">
					<option value=""></option>
					<?php
					global $post;
					$old_post = $post;
					while ( $my_query->have_posts() ) : $my_query->the_post(); ?>
					<option value="<?php echo $post->post_name; ?>" <?php echo $slideshow==$post->post_name ? 'selected="selected"': ''; ?>><?php the_title(); ?></option>
					<?php
					endwhile;
					wp_reset_postdata();
					$post = $old_post;
					?>
				</select>
			<?php else: ?>
				<?php _e('No sliders found.', 'cycloneslider'); ?>
			<?php endif; ?>
			</p>
	<?php
	
		}
	
	}

	
	/**
	* Register it
	*/
	function cycloneslider_widgets(){
		register_widget('Cyclone_Slider_Widget');
	}
	add_action('widgets_init', 'cycloneslider_widgets');
	
endif;
