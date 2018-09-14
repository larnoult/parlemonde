<?php

defined('ABSPATH') or die('No script kiddies please!');

class SDPVS_Widget extends WP_Widget {

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		$widget_ops = array( 
			'classname' => 'sdpvs_widget',
			'description' => esc_html__('Add a chart from "Post Volume Stats" to your sidebar!', 'post-volume-stats'),
		);
		parent::__construct( 'sdpvs_widget', esc_html__('Post Volume Stats', 'post-volume-stats'), $widget_ops );
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		// outputs the content of the widget
		$title = apply_filters('widget_title', $instance['title']);
   		$textarea = $instance['textarea'];
   		$label_color = esc_attr( $instance['label_color'] );
		$checkbox1 = esc_attr( $instance['checkbox1'] );
		$checkbox2 = esc_attr( $instance['checkbox2'] );
		$checkbox3 = esc_attr( $instance['checkbox3'] );
		$checkbox4 = esc_attr( $instance['checkbox4'] );
		$checkbox5 = esc_attr( $instance['checkbox5'] );
		$checkbox8 = esc_attr( $instance['checkbox8'] );
		$checkbox9 = esc_attr( $instance['checkbox9'] );

		$checkbox6 = esc_attr( $instance['checkbox6'] );
		$checkbox7 = esc_attr( $instance['checkbox7'] );
		
   		echo $args['before_widget'];
   		// Display the widget
   		echo '<div class="widget-text wp_widget_plugin_box">';

   		// Check if title is set
   		if ( $title ) {
      		echo $args['before_title'] . $title . $args['after_title'];
   		}
   		// Check if textarea is set
   		if( $textarea ) {
     		echo '<p class="wp_widget_plugin_textarea">'.$textarea.'</p>';
   		}
		if($checkbox1 or $checkbox2 or $checkbox3 or $checkbox4 or $checkbox5 or $checkbox8 or $checkbox9 ){
			// Check if checkboxes are checked
			$sdpvs_bar = new sdpvsBarChart();
   			if( 'year' == $checkbox1 ) {
				// year bar chart
				echo "<div class='sdpvs_col'>";
				$sdpvs_bar -> sdpvs_draw_bar_chart_svg('year','','','n','y',$label_color);
				echo "</div>";
   			}
   			if('month' == $checkbox2){
   				// month bar chart
   				echo "<div class='sdpvs_col'>";
   				$sdpvs_bar -> sdpvs_draw_bar_chart_svg('month','','','n','y',$label_color);
				echo "</div>";
   			}
			if('dayofmonth' == $checkbox3){
   				// dayofmonth bar chart
   				echo "<div class='sdpvs_col'>";
   				$sdpvs_bar -> sdpvs_draw_bar_chart_svg('dayofmonth','','','n','y',$label_color);
				echo "</div>";
   			}
   			if('dayofweek' == $checkbox4){
   				// dayofmonth bar chart
   				echo "<div class='sdpvs_col'>";
   				$sdpvs_bar -> sdpvs_draw_bar_chart_svg('dayofweek','','','n','y',$label_color);
				echo "</div>";
   			}
			if('hour' == $checkbox5){
   				// dayofmonth bar chart
   				echo "<div class='sdpvs_col'>";
   				$sdpvs_bar -> sdpvs_draw_bar_chart_svg('hour','','','n','y',$label_color);
				echo "</div>";
   			}
   			if('words' == $checkbox8){
   				// dayofmonth bar chart
   				echo "<div class='sdpvs_col'>";
   				$sdpvs_bar -> sdpvs_draw_bar_chart_svg('words','','','n','y',$label_color);
				echo "</div>";
   			}
   			if('interval' == $checkbox9){
   				// dayofmonth bar chart
   				echo "<div class='sdpvs_col'>";
   				$sdpvs_bar -> sdpvs_draw_bar_chart_svg('interval','','','n','y',$label_color);
				echo "</div>";
   			}
			
		}

		if($checkbox6 or $checkbox7){
			$sdpvs_pie = new sdpvsPieChart();
			if('category' == $checkbox6){
   				// dayofmonth bar chart
   				echo "<div class='sdpvs_col'>";
   				echo $sdpvs_pie -> sdpvs_draw_pie_svg('category','','', 'n', 'y');
				echo "</div>";
   			}
			if('tag' == $checkbox7){
   				// dayofmonth bar chart
   				echo "<div class='sdpvs_col'>";
   				echo $sdpvs_pie -> sdpvs_draw_pie_svg('tag','','', 'n', 'y');
				echo "</div>";
   			}
		}

		
   		echo '</div>';
   		echo $args['after_widget'];
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {
		// outputs the options form on admin
		// Check values
		if( $instance) {
		     $title = esc_attr($instance['title']);
		     $textarea = esc_textarea($instance['textarea']);
		     $label_color = esc_attr($instance['label_color']);
			 $checkbox1 = esc_attr( $instance['checkbox1'] );
			 $checkbox2 = esc_attr( $instance['checkbox2'] );
			 $checkbox3 = esc_attr( $instance['checkbox3'] );
			 $checkbox4 = esc_attr( $instance['checkbox4'] );
			 $checkbox5 = esc_attr( $instance['checkbox5'] );
			 $checkbox8 = esc_attr( $instance['checkbox8'] );
			 $checkbox9 = esc_attr( $instance['checkbox9'] );
			 $checkbox6 = esc_attr( $instance['checkbox6'] );
			 $checkbox7 = esc_attr( $instance['checkbox7'] );
		} else {
		     $title = '';
		     $textarea = '';
		     $label_color='';
			 $checkbox1 = '';
			 $checkbox2 = '';
			 $checkbox3 = '';
			 $checkbox4 = '';
			 $checkbox5 = '';
			 $checkbox8 = '';
			 $checkbox9 = '';
			 $checkbox6 = '';
			 $checkbox7 = '';
		}
		?>

		<p>
		<label for="<?php echo $this -> get_field_id('title'); ?>"><?php esc_html_e('Title', 'post-volume-stats'); ?></label>
		<input class="widefat" id="<?php echo $this -> get_field_id('title'); ?>" name="<?php echo $this -> get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>
		<p>
		<label for="<?php echo esc_attr($this -> get_field_id('textarea')); ?>"><?php esc_html_e('Description', 'post-volume-stats'); ?></label>
		<textarea class="widefat" id="<?php echo esc_attr($this -> get_field_id('textarea')); ?>" name="<?php echo esc_attr($this -> get_field_name('textarea')); ?>"><?php echo esc_html($textarea); ?></textarea>
		</p>
		<p>
		<input id="<?php echo esc_attr($this -> get_field_id('label_color')); ?>" name="<?php echo esc_attr($this -> get_field_name('label_color')); ?>" type="checkbox" value="white" <?php checked('white', $label_color); ?> />
		<label for="<?php echo esc_attr($this -> get_field_id('label_color')); ?>"><?php esc_html_e('White Text (for dark backgrounds)', 'post-volume-stats'); ?></label>
		</p>
		<p>
		<?php esc_html_e('Select charts to display:', 'post-volume-stats'); ?>
		</p>
		<p>
		<input id="<?php echo esc_attr($this -> get_field_id('checkbox1')); ?>" name="<?php echo esc_attr($this -> get_field_name('checkbox1')); ?>" type="checkbox" value="year" <?php checked('year', $checkbox1); ?> />
		<label for="<?php echo esc_attr($this -> get_field_id('checkbox1')); ?>"><?php esc_html_e('Years', 'post-volume-stats'); ?></label>
		</p>
		<p>
		<input id="<?php echo esc_attr($this -> get_field_id('checkbox2')); ?>" name="<?php echo esc_attr($this -> get_field_name('checkbox2')); ?>" type="checkbox" value="month" <?php checked('month', $checkbox2); ?> />
		<label for="<?php echo esc_attr($this -> get_field_id('checkbox2')); ?>"><?php esc_html_e('Months', 'post-volume-stats'); ?></label>
		</p>
		<p>
		<input id="<?php echo esc_attr($this -> get_field_id('checkbox3')); ?>" name="<?php echo esc_attr($this -> get_field_name('checkbox3')); ?>" type="checkbox" value="dayofmonth" <?php checked('dayofmonth', $checkbox3); ?> />
		<label for="<?php echo esc_attr($this -> get_field_id('checkbox3')); ?>"><?php esc_html_e('Days of the Month', 'post-volume-stats'); ?></label>
		</p>
		<p>
		<input id="<?php echo esc_attr($this -> get_field_id('checkbox4')); ?>" name="<?php echo esc_attr($this -> get_field_name('checkbox4')); ?>" type="checkbox" value="dayofweek" <?php checked('dayofweek', $checkbox4); ?> />
		<label for="<?php echo esc_attr($this -> get_field_id('checkbox4')); ?>"><?php esc_html_e('Days of the Week', 'post-volume-stats'); ?></label>
		</p>
		<p>
		<input id="<?php echo esc_attr($this -> get_field_id('checkbox5')); ?>" name="<?php echo esc_attr($this -> get_field_name('checkbox5')); ?>" type="checkbox" value="hour" <?php checked('hour', $checkbox5); ?> />
		<label for="<?php echo esc_attr($this -> get_field_id('checkbox5')); ?>"><?php esc_html_e('Hour', 'post-volume-stats'); ?></label>
		</p>
		<p>
		<input id="<?php echo esc_attr($this -> get_field_id('checkbox8')); ?>" name="<?php echo esc_attr($this -> get_field_name('checkbox8')); ?>" type="checkbox" value="words" <?php checked('words', $checkbox8); ?> />
		<label for="<?php echo esc_attr($this -> get_field_id('checkbox8')); ?>"><?php esc_html_e('Words per Post', 'post-volume-stats'); ?></label>
		</p>
		<p>
		<input id="<?php echo esc_attr($this -> get_field_id('checkbox9')); ?>" name="<?php echo esc_attr($this -> get_field_name('checkbox9')); ?>" type="checkbox" value="interval" <?php checked('interval', $checkbox9); ?> />
		<label for="<?php echo esc_attr($this -> get_field_id('checkbox9')); ?>"><?php esc_html_e('Interval', 'post-volume-stats'); ?></label>
		</p>
		<p>
		<input id="<?php echo esc_attr($this -> get_field_id('checkbox6')); ?>" name="<?php echo esc_attr($this -> get_field_name('checkbox6')); ?>" type="checkbox" value="category" <?php checked('category', $checkbox6); ?> />
		<label for="<?php echo esc_attr($this -> get_field_id('checkbox6')); ?>"><?php esc_html_e('Categories', 'post-volume-stats'); ?></label>
		</p>
		<p>
		<input id="<?php echo esc_attr($this -> get_field_id('checkbox7')); ?>" name="<?php echo esc_attr($this -> get_field_name('checkbox7')); ?>" type="checkbox" value="tag" <?php checked('tag', $checkbox7); ?> />
		<label for="<?php echo esc_attr($this -> get_field_id('checkbox7')); ?>"><?php esc_html_e('Tags', 'post-volume-stats'); ?></label>
		</p>
		<?php
	}

		/**
		* Processing widget options on save
		*
		* @param array $new_instance The new options
		* @param array $old_instance The previous options
		*/
		public function update( $new_instance, $old_instance ) {
			// processes widget options to be saved
			$instance = $old_instance;
			// Fields
			$instance['title'] = strip_tags($new_instance['title']);
			$instance['textarea'] = strip_tags($new_instance['textarea']);
			$instance['label_color'] = strip_tags($new_instance['label_color']);
			$instance['checkbox1'] = strip_tags($new_instance['checkbox1']);
			$instance['checkbox2'] = strip_tags($new_instance['checkbox2']);
			$instance['checkbox3'] = strip_tags($new_instance['checkbox3']);
			$instance['checkbox4'] = strip_tags($new_instance['checkbox4']);
			$instance['checkbox5'] = strip_tags($new_instance['checkbox5']);
			$instance['checkbox8'] = strip_tags($new_instance['checkbox8']);
			$instance['checkbox9'] = strip_tags($new_instance['checkbox9']);
			$instance['checkbox6'] = strip_tags($new_instance['checkbox6']);
			$instance['checkbox7'] = strip_tags($new_instance['checkbox7']);
			return $instance;
		}
}

		// register Foo_Widget widget
		function sdpvs_widget_register() {
		register_widget( 'SDPVS_Widget' );
		}
		add_action( 'widgets_init', 'sdpvs_widget_register' );
	?>
