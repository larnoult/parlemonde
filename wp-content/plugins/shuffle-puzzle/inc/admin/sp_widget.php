<?php
class ShufflePuzzleWidget extends WP_Widget
{
  function ShufflePuzzleWidget()
  {
    $widget_ops = array('classname' => 'ShufflePuzzleWidget', 'description' => 'Displays selected shufflepuzzle' );
    $this->WP_Widget('ShufflePuzzleWidget', 'ShufflePuzzle Widget', $widget_ops);
  }
 
  function form($instance)
  {
    $instance = wp_parse_args( (array) $instance, array( 'title' => '','select_msp' =>'' ) );
    $title = $instance['title'];
	$select_msp = esc_attr($instance['select_msp']);
	?>
	<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:','msp'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></label></p>
	<p><label for="<?php echo $this->get_field_id('select_msp'); ?>"><?php _e('Select Shuffle Puzzle:','msp'); ?> 
	<?php
		global $wpdb;
		$table_name = $wpdb->prefix . "shufflepuzzle";
		$msp_data = $wpdb->get_results("SELECT * FROM $table_name WHERE active=1  ORDER BY id");
	?>
	<select id="<?php echo $this->get_field_id('select_msp'); ?>" name="<?php echo $this->get_field_name('select_msp'); ?>">
		<?php
			foreach($msp_data as $msp_item){ ?><option <?php selected($msp_item->option_name,$select_msp); ?> value="<?php echo $msp_item->option_name; ?>"><?php echo $msp_item->option_name; ?></option><?php }
		?>
	</select>
	</label>
	</p>
	
<?php
  }
 
  function update($new_instance, $old_instance)
  {
    $instance = $old_instance;
    $instance['title'] = strip_tags($new_instance['title']);
	$instance['select_msp'] = strip_tags($new_instance['select_msp']);
    return $instance;
  }
 
  function widget($args, $instance)
  {
    extract($args, EXTR_SKIP);
    echo $before_widget;
    $title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
	$select_msp = esc_attr($instance['select_msp']);
    if (!empty($title))
    echo $before_title . $title . $after_title;;
    echo shufflePuzzle($select_msp);
    echo $after_widget;
  }
 
}
add_action( 'widgets_init', create_function('', 'return register_widget("ShufflePuzzleWidget");') );
?>