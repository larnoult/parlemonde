<?php

/*
 * Democracy Widget
 */

add_action( 'widgets_init', 'widget_democracy_register' );
function widget_democracy_register(){
	register_widget('widget_democracy');
}

class widget_democracy extends WP_Widget {

	function __construct(){
		// Instantiate the parent object. Creates option 'widget_democracy'
		parent::__construct( 'democracy', __('Democracy Poll','democracy-poll'), array( 'description'=>__('Democracy Poll Widget','democracy-poll') ) );
	}

	// front end
	function widget( $args, $instance ){
		extract( $args );
		$title = @ $instance['title'];
		$pid   = @ $instance['show_poll'];

		if( is_singular() && ! democr()->opt('post_metabox_off') && ($_pid = get_post_poll_id()) ){
			$pid = $_pid;
		}

		if( isset( $instance['questionIsTitle'] ) ){
			echo $before_widget;

			democracy_poll( $pid, $before_title, $after_title );

			echo $after_widget;
		}
		else {
			echo $before_widget . $before_title . $title . $after_title;

			democracy_poll( $pid );

			echo $after_widget;
		}
	}

	// options
	function update( $new_instance, $old_instance ){
		foreach( $new_instance as & $val )
			$val = strip_tags( $val );

		return $new_instance;
	}

	// admin
	function form( $instance ){
		add_action('admin_footer', array( & $this, 'dem_widget_footer_js'), 11 );

		$checked   = isset( $instance['questionIsTitle'] ) ? ' checked="checked"' : '';
		$title     = isset( $instance['title'] )           ? esc_attr( $instance['title'] ) : __('Poll','democracy-poll');
		$show_poll = isset( $instance['show_poll'] )     ? $instance['show_poll'] : 0;

		$title_style = $checked ? 'style="display:none;"' : '';
		?>
		<p>
			<label>
				<input type="checkbox" name="<?php echo $this->get_field_name('questionIsTitle')?>" <?php echo $checked?> value="1" class="questionIsTitle" onchange="demHideTitle(this);">
				<small><?php _e('Poll question = widget title?','democracy-poll')  ?> </small>
			</label>
		</p>

		<p class="demTitleWrap" <?php echo $title_style ?>>
			<label><?php _e('Poll title:','democracy-poll'); ?>
				<input style="width:100%;" type="text" id="demTitle" name="<?php echo $this->get_field_name('title')?>" value="<?php echo $title?>">
			</label>
		</p>


		<?php
		global $wpdb, $table_prefix;

		$options = '
		<option value="0">'. __('- Active (random all active)','democracy-poll') .'</option>
		<option value="last" '. selected( $show_poll, 'last', 0) .'>'. __('- Last open poll','democracy-poll') .'</option>
		<option disabled></option>
		';

		$qu = $wpdb->get_results("SELECT * FROM $wpdb->democracy_q ORDER BY added DESC LIMIT 70");
		foreach( $qu as $quest ){
			$options .= '<option value="'. $quest->id .'" '. selected($show_poll, $quest->id, 0) .'>'. democr()->kses_html($quest->question) .'</option>';
		}

		echo '
		<p>
			<label>'. __('Which poll to show?','democracy-poll') .'
				<select name="'. $this->get_field_name('show_poll') .'" style="max-width:100%;">'. $options .'</select>
			</label>
		</p>';
	}

	function dem_widget_footer_js(){
		?>
		<script type="text/javascript">
			var getTitleObj = function(that){ return jQuery(that).closest('.widget-content').find('.demTitleWrap'); };

			window.demHideTitle = function(that){
				if( that.checked ) getTitleObj(that).slideUp(300);
				else               getTitleObj(that).slideDown(300);
			}
		</script>
		<?php
	}

}
