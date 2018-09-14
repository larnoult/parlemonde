<?php
 // Prevent file from being accessed directly.
 if ( basename( $_SERVER['SCRIPT_FILENAME'] ) == 'class-mmp-latest-marker-map-widget.php' ) {
	 die ( "Please do not access this file directly. Thanks!<br/><a href='https://www.mapsmarker.com/go'>www.mapsmarker.com</a>" );
 }

/**
 * Adds MMP_Latest_Marker widget.
 */
class MMP_Latest_Marker_Map_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		$lmm_options = get_option( 'leafletmapsmarker_options' );
		if ( isset( $lmm_options['misc_whitelabel_backend'] ) && $lmm_options['misc_whitelabel_backend'] == 'enabled' ) {
			$widget_name = __( 'Maps - latest marker map', 'lmm' );
		} else {
			$widget_name = __( 'Maps Marker Pro - latest marker map', 'lmm' );
		}
		$widget_options = array(
			'classname' => 'MMP_Recent_Marker_Widget',
			'description' => __( 'Widget to show the latest marker map', 'lmm' )
		);
		$control_options = array();
		parent::__construct(
			'mmp_latest_marker', // Base ID
			'<span>' . $widget_name . '</span>',
			$widget_options,
			$control_options
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		global $wpdb, $allowedposttags;
		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}
		if ( ! empty( $instance['include'] ) || ( isset( $instance['include'] ) && is_numeric( $instance['include'] ) ) ) {
			$sql_include = MMP_Globals::sanitize_csv( $instance['include'], true, true, 'double' );
			$layers = explode( ',', $sql_include );
			$sql_include = '';
			$i = 1;
			$l = count( $layers );
			foreach ( $layers as $layer ) {
				if ( $i === $l ) {
					$sql_include .= "layer LIKE '%%{$layer}%%'";
				} else {
					$sql_include .= "layer LIKE '%%{$layer}%%' OR ";
				}
				$i++;
			}
		} else {
			$sql_include = '1';
		}
		if ( ! empty( $instance['exclude'] ) || ( isset( $instance['exclude'] ) && is_numeric( $instance['exclude'] ) ) ) {
			$sql_exclude = MMP_Globals::sanitize_csv( $instance['exclude'], true, true, 'double' );
			$layers = explode( ',', $sql_exclude );
			$sql_exclude = '';
			$i = 1;
			$l = count( $layers );
			foreach ( $layers as $layer ) {
				if ( $i === $l ) {
					$sql_exclude .= "layer NOT LIKE '%%{$layer}%%'";
				} else {
					$sql_exclude .= "layer NOT LIKE '%%{$layer}%%' AND ";
				}
				$i++;
			}
		} else {
			$sql_exclude = '1';
		}
		$sql_orderby = $instance['orderkey'] == 'updatedon' ? 'updatedon' : 'createdon';
		$query = "
			SELECT * FROM {$wpdb->prefix}leafletmapsmarker_markers
			WHERE {$sql_include} AND {$sql_exclude}
			ORDER BY {$sql_orderby} DESC
			LIMIT %d, 1
		";
		$result = $wpdb->get_row( $wpdb->prepare( $query, $instance['orderoff'] ) );
		if ( is_wp_error( $result ) ) {
			echo $result->get_error_message();
		}
		else if ( $result ) {
			if ( ! empty( $instance['textbeforemap'] ) ) {
				echo wp_kses($instance['textbeforemap'], $allowedposttags);
			}
			$shortcode_name = isset( $lmm_options['shortcode'] ) && ! empty( $lmm_options['shortcode'] ) ? $lmm_options['shortcode'] : 'mapsmarker';
			$shortcode_marker = ' marker="' . $result->id . '"';
			$shortcode_width = ! empty( $instance['width'] ) ? ' mapwidth="' . $instance['width'] . '"' : '';
			$shortcode_unit = ! empty( $instance['unit'] ) && ! empty( $instance['width'] ) ? ' mapwidthunit="' . $instance['unit'] . '"' : '';
			$shortcode_height = ! empty( $instance['height'] ) ? ' mapheight="' . $instance['height'] . '"' : '';
			echo do_shortcode( '[' . $shortcode_name . $shortcode_marker . $shortcode_width . $shortcode_unit . $shortcode_height . ']' );
			if ( ! empty( $instance['textaftermap'] ) ) {
				echo wp_kses($instance['textaftermap'], $allowedposttags);
			}
		} else {
			echo __( 'No marker found', 'lmm' );
		}
		echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : '';
		$textbeforemap = ! empty( $instance['textbeforemap'] ) ? $instance['textbeforemap'] : '';
		$textaftermap = ! empty( $instance['textaftermap'] ) ? $instance['textaftermap'] : '';
		$width = ! empty( $instance['width'] ) ? $instance['width'] : '100';
		$unit = ! empty( $instance['unit'] ) ? $instance['unit'] : '%';
		$height = ! empty( $instance['height'] ) ? $instance['height'] : '250';
		$include = ! empty( $instance['include'] ) || ( isset( $instance['include'] ) && is_numeric( $instance['include'] ) ) ? $instance['include'] : '';
		$exclude = ! empty( $instance['exclude'] ) || ( isset( $instance['exclude'] ) && is_numeric( $instance['exclude'] ) ) ? $instance['exclude'] : '';
		$orderkey = ! empty( $instance['orderkey'] ) ? $instance['orderkey'] : 'createdon';
		$orderoff = ! empty( $instance['orderoff'] ) ? $instance['orderoff'] : '0';
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'lmm' ); ?></label>
			<br />
			<input id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" class="widefat" />
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'textbeforemap' ) ); ?>"><?php esc_attr_e( 'Text before map:', 'lmm' ); ?></label>
			<br />
			<input id="<?php echo esc_attr( $this->get_field_id( 'textbeforemap' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'textbeforemap' ) ); ?>" type="text" value="<?php echo esc_attr( $textbeforemap ); ?>" class="widefat" />
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'width' ) ); ?>"><?php esc_attr_e( 'Map width:', 'lmm' ); ?></label>
			<br />
			<input id="<?php echo esc_attr( $this->get_field_id( 'width' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'width' ) ); ?>" type="text" value="<?php echo esc_attr( $width ); ?>" style="width:65px;" />
			<input <?php checked( $unit, 'px' ); ?> id="<?php echo esc_attr( $this->get_field_id( 'widthunitpx' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'unit' ) ); ?>" value="px" type="radio" /><label for="<?php echo esc_attr( $this->get_field_id( 'widthunitpx' ) ); ?>">px</label>
			<input <?php checked( $unit, '%' ); ?> id="<?php echo esc_attr( $this->get_field_id( 'widthunitpc' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'unit' ) ); ?>" value="%" type="radio" /><label for="<?php echo esc_attr( $this->get_field_id( 'widthunitpc' ) ); ?>">%</label>
			<br />
			<label for="<?php echo esc_attr( $this->get_field_id( 'height' ) ); ?>"><?php esc_attr_e( 'Map height:', 'lmm' ); ?></label>
			<br />
			<input id="<?php echo esc_attr( $this->get_field_id( 'height' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'height' ) ); ?>" type="text" value="<?php echo esc_attr( $height ); ?>" style="width:65px;" /> px
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'include' ) ); ?>"><?php esc_attr_e( 'Included markers from specific layers only', 'lmm' ); ?>: <img src="<?php echo LEAFLET_PLUGIN_URL; ?>inc/img/icon-question-mark.png" width="12" height="12" border="0" title="<?php esc_attr_e('If empty, markers from all layers are selected.','lmm') . ' ' . esc_attr_e('To select only markers from a layer, please enter the layer ID. Use commas to separate multiple layers. Use 0 for markers not assigned to a layer.','lmm'); ?>" /></label>
			<br />
			<input id="<?php echo esc_attr( $this->get_field_id( 'include' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'include' ) ); ?>" type="text" value="<?php echo esc_attr( $include ); ?>" class="widefat" />
			<br />
			<label for="<?php echo esc_attr( $this->get_field_id( 'exclude' ) ); ?>"><?php esc_attr_e( 'Exclude markers from specific layer IDs', 'lmm' ); ?>: <img src="<?php echo LEAFLET_PLUGIN_URL; ?>inc/img/icon-question-mark.png" width="12" height="12" border="0" title="<?php esc_attr_e('Please enter layer ID. Use commas to separate multiple layers. Use 0 for markers not assigned to a layer.','lmm'); ?>" /></label>
			<br />
			<input id="<?php echo esc_attr( $this->get_field_id( 'exclude' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'exclude' ) ); ?>" type="text" value="<?php echo esc_attr( $exclude ); ?>" class="widefat" />
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'orderkey' ) ); ?>"><?php esc_attr_e( 'Type:', 'lmm' ); ?></label>
			<br />
			<select id="<?php echo esc_attr( $this->get_field_id( 'orderkey' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'orderkey' ) ); ?>">;
				<option <?php selected( $orderkey, 'createdon' ); ?> value="createdon">Last created</option>
				<option <?php selected( $orderkey, 'updatedon' ); ?> value="updatedon">Last updated</option>
			</select>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'textaftermap' ) ); ?>"><?php esc_attr_e( 'Text after map:', 'lmm' ); ?></label>
			<br />
			<input id="<?php echo esc_attr( $this->get_field_id( 'textaftermap' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'textaftermap' ) ); ?>" type="text" value="<?php echo esc_attr( $textaftermap ); ?>" class="widefat" />
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'orderoff' ) ); ?>"><?php esc_attr_e( 'Offset:', 'lmm' ); ?></label>
			<br />
			<input id="<?php echo esc_attr( $this->get_field_id( 'orderoff' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'orderoff' ) ); ?>" type="text" value="<?php echo esc_attr( $orderoff ); ?>" class="widefat" />
		</p>
		<?php
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		global $allowedposttags;
		$instance['title'] = ! empty( $new_instance['title'] ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['textbeforemap'] = ! empty( $new_instance['textbeforemap'] ) ? wp_kses( $new_instance['textbeforemap'], $allowedposttags ) : '';
		$instance['textaftermap'] = ! empty( $new_instance['textaftermap'] ) ? wp_kses( $new_instance['textaftermap'], $allowedposttags ) : '';
		$instance['width'] = ! empty( $new_instance['width'] ) ? abs( intval( $new_instance['width'] ) ) : '';
		$instance['unit'] = ! empty( $new_instance['unit'] ) ? strip_tags( $new_instance['unit'] ) : 'px';
		$instance['height'] = ! empty( $new_instance['height'] ) ? abs( intval( $new_instance['height'] ) ): '';
		$instance['include'] = ! empty( $new_instance['include'] ) || ( isset( $new_instance['include'] ) && is_numeric( $new_instance['include'] ) ) ? MMP_Globals::sanitize_csv( $new_instance['include'] ) : '';
		$instance['exclude'] = ! empty( $new_instance['exclude'] ) || ( isset( $new_instance['exclude'] ) && is_numeric( $new_instance['exclude'] ) ) ? MMP_Globals::sanitize_csv( $new_instance['exclude'] ) : '';
		$instance['orderkey'] = ! empty( $new_instance['orderkey'] ) ? strip_tags( $new_instance['orderkey'] ) : 'createdon';
		$instance['orderoff'] = ! empty( $new_instance['orderoff'] ) ? abs( intval( $new_instance['orderoff'] ) ) : '0';
		return $instance;
	}
}