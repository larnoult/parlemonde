<?php
/**
 * Gallery Widget
 *
 * @package Virtue Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'kad_gallery_widget' ) ) {
	/**
	 * Gallery Widget
	 *
	 * @category class
	 */
	class kad_gallery_widget extends WP_Widget{
		/**
		 * Gallery instance
		 *
		 * @var null
		 */
		private static $instance = 0;
		/**
		 * Gallery construct
		 */
		public function __construct() {
			$widget_ops = array(
				'classname'   => 'virtue_gallery_widget',
				'description' => __( 'Adds a gallery to any widget area.', 'virtue' ),
			);
			parent::__construct( 'virtue_gallery_widget', __( 'Virtue: Gallery', 'virtue' ), $widget_ops );
		}
		/**
		 * Widget output function
		 *
		 * @param array $args the widget args.
		 * @param array $instance the widget instance.
		 */
		public function widget( $args, $instance ) {

			$title      = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
			$g_ids      = ( ! empty( $instance['ids'] ) ? $instance['ids'] : '' );
			$g_width    = ( ! empty( $instance['gallery_width'] ) ? 'width="' . $instance['gallery_width'] . '"' : '' );
			$g_height   = ( ! empty( $instance['gallery_height'] ) ? 'height="' . $instance['gallery_height'] . '"' : '' );
			$g_speed    = ( ! empty( $instance['gallery_speed'] ) ? 'speed="' . $instance['gallery_speed'] . '"' : '' );
			$l_size     = ( ! empty( $instance['lightbox_size'] ) ? 'lightboxsize="' . $instance['lightbox_size'] . '"' : '' );
			$g_type     = ( ! empty( $instance['gallery_type'] ) ? $instance['gallery_type'] : 'standard' );
			$g_columns  = ( ! empty( $instance['gallery_columns'] ) ? $instance['gallery_columns'] : '3' );
			$g_captions = ( ! empty( $instance['gallery_captions'] ) && 'on' === $instance['gallery_captions'] ? 'caption="true"' : 'caption="false"' );

			if ( 'masonry' === $g_type ) {
				$masonry = 'true';
			} else {
				$masonry = 'false';
			}

			echo $args['before_widget'];
			if ( $title ) {
				echo $args['before_title'] . $title . $args['after_title'];
			}
			echo do_shortcode( '[gallery ids="' . $g_ids . '" type="' . $g_type . '" ' . $g_captions . ' masonry="' . $masonry . '" columns="' . $g_columns . '" ' . $g_speed . ' ' . $g_height . ' ' . $l_size . ' ' . $g_width . ']' );
			echo $args['after_widget'];
		}
		/**
		 * Widget update function
		 *
		 * @param array $new_instance the widgets instance.
		 * @param array $old_instance the widgets prior instance.
		 */
		public function update( $new_instance, $old_instance ) {
			$instance                     = $old_instance;
			$instance['ids']              = sanitize_text_field( $new_instance['ids'] );
			$instance['gallery_type']     = sanitize_text_field( $new_instance['gallery_type'] );
			$instance['lightbox_size']    = sanitize_text_field( $new_instance['lightbox_size'] );
			$instance['gallery_columns']  = sanitize_text_field( $new_instance['gallery_columns'] );
			$instance['gallery_captions'] = sanitize_text_field( $new_instance['gallery_captions'] );
			$instance['gallery_width']    = (int) $new_instance['gallery_width'];
			$instance['gallery_height']   = (int) $new_instance['gallery_height'];
			$instance['gallery_speed']    = (int) $new_instance['gallery_speed'];
			$instance['title']            = sanitize_text_field( $new_instance['title'] );

			return $instance;
		}
		/**
		 * Widget form function
		 *
		 * @param array $instance the widgets instance.
		 */
		public function form( $instance ) {
			$title            = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
			$ids              = isset( $instance['ids'] ) ? esc_attr( $instance['ids'] ) : '';
			$gallery_width    = isset( $instance['gallery_width'] ) ? esc_attr( $instance['gallery_width'] ) : '';
			$gallery_height   = isset( $instance['gallery_height'] ) ? esc_attr( $instance['gallery_height'] ) : '';
			$gallery_speed    = isset( $instance['gallery_speed'] ) ? esc_attr( $instance['gallery_speed'] ) : '';
			$gallery_type     = isset( $instance['gallery_type'] ) ? esc_attr( $instance['gallery_type'] ) : 'standard';
			$lightbox_size    = isset( $instance['lightbox_size'] ) ? esc_attr( $instance['lightbox_size'] ) : 'full';
			$gallery_columns  = isset( $instance['gallery_columns'] ) ? esc_attr( $instance['gallery_columns'] ) : '3';
			$gallery_captions = isset( $instance['gallery_captions'] ) ? esc_attr( $instance['gallery_captions'] ) : 'off';

			$gallery_type_array     = array();
			$lightbox_size_array    = array();
			$gallery_columns_array  = array();
			$gallery_captions_array = array();

			$gallery_options         = array(
				array(
					'slug' => 'standard',
					'name' => __( 'Standard', 'virtue' ),
				),
				array(
					'slug' => 'masonry',
					'name' => __( 'Masonry', 'virtue' ),
				),
				array(
					'slug' => 'mosaic',
					'name' => __( 'Mosaic', 'virtue' ),
				),
				array(
					'slug' => 'carousel',
					'name' => __( 'Carousel', 'virtue' ),
				),
				array(
					'slug' => 'slider',
					'name' => __( 'Slider', 'virtue' ),
				),
				array(
					'slug' => 'imagecarousel',
					'name' => __( 'Image Carousel', 'virtue' ),
				),
			);
			$gallery_columns_options = array(
				array(
					'slug' => '1',
					'name' => __( '1 Column', 'virtue' ),
				),
				array(
					'slug' => '2',
					'name' => __( '2 Columns', 'virtue' ),
				),
				array(
					'slug' => '3',
					'name' => __( '3 Columns', 'virtue' ),
				),
				array(
					'slug' => '4',
					'name' => __( '4 Columns', 'virtue' ),
				),
				array(
					'slug' => '5',
					'name' => __( '5 Columns', 'virtue' ),
				),
				array(
					'slug' => '6',
					'name' => __( '6 Columns', 'virtue' ),
				),
			);
			$gallery_caption_options = array(
				array(
					'slug' => 'off',
					'name' => __( 'Off', 'virtue' ),
				),
				array(
					'slug' => 'on',
					'name' => __( 'On', 'virtue' ),
				),
			);
			$lightbox_size_options   = array(
				array(
					'slug' => 'full',
					'name' => __( 'Full', 'virtue' ),
				),
				array(
					'slug' => 'large',
					'name' => __( 'Large', 'virtue' ),
				),
				array(
					'slug' => 'medium',
					'name' => __( 'Medium', 'virtue' ),
				),
				array(
					'slug' => 'thumbnail',
					'name' => __( 'Thumbnail', 'virtue' ),
				),
			);

			foreach ( $gallery_caption_options as $gallery_caption_option ) {
				$selected                 = ( $gallery_caption_option['slug'] === $gallery_captions ? ' selected="selected"' : '' );
				$gallery_captions_array[] = '<option value="' . $gallery_caption_option['slug'] . '"' . $selected . '>' . $gallery_caption_option['name'] . '</option>';
			}
			foreach ( $lightbox_size_options as $lightbox_size_option ) {
				$selected              = ( $lightbox_size_option['slug'] === $lightbox_size ? ' selected="selected"' : '' );
				$lightbox_size_array[] = '<option value="' . $lightbox_size_option['slug'] . '"' . $selected . '>' . $lightbox_size_option['name'] . '</option>';
			}
			foreach ( $gallery_options as $gallery_option ) {
				$selected             = ( $gallery_option['slug'] === $gallery_type ? ' selected="selected"' : '' );
				$gallery_type_array[] = '<option value="' . $gallery_option['slug'] . '"' . $selected . '>' . $gallery_option['name'] . '</option>';
			}
			foreach ( $gallery_columns_options as $gallery_column_option ) {
				$selected                = ( $gallery_column_option['slug'] === $gallery_columns ? ' selected="selected"' : '' );
				$gallery_columns_array[] = '<option value="' . $gallery_column_option['slug'] . '"' . $selected . '>' . $gallery_column_option['name'] . '</option>';
			}
			?>

			<div id="virtue_gallery_widget<?php echo esc_attr( $this->get_field_id( 'container' ) ); ?>" class="kad_widget_image_gallery">
				<div class="gallery_images">
					<?php
					$attachments = array_filter( explode( ',', $ids ) );
					if ( $attachments ) {
						foreach ( $attachments as $attachment_id ) {
							$img     = wp_get_attachment_image_src( $attachment_id, 'thumbnail' );
							$imgfull = wp_get_attachment_image_src( $attachment_id, 'full' );
							echo '<a class="of-uploaded-image" target="_blank" rel="external" href="' . esc_url( $imgfull[0] ) . '">';
								echo '<img class="gallery-widget-image" id="gallery_widget_image_' . esc_attr( $attachment_id ) . '" src="' . esc_url( $img[0] ) . '" />';
							echo '</a>';
						}
					}
					?>
				</div>
				<?php
				echo '<a href="#" onclick="return false;" id="edit-gallery" class="gallery-attachments button button-primary">' . esc_html__( 'Add/Edit Gallery', 'virtue' ) . '</a> ';
				echo '<a href="#" onclick="return false;" id="clear-gallery" class="gallery-attachments button">' . esc_html__( 'Clear Gallery', 'virtue' ) . '</a>';
				echo '<input type="hidden" id="' . esc_attr( $this->get_field_id( 'ids' ) ) . '" class="gallery_values" value="' . esc_attr( $ids ) . '" name="' . esc_attr( $this->get_field_name( 'ids' ) ) . '" />';
				?>
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'virtue' ); ?></label>
					<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
				</p>
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'gallery_type' ) ); ?>"><?php esc_html_e( 'Gallery Type', 'virtue' ); ?></label><br />
					<select id="<?php echo esc_attr( $this->get_field_id( 'gallery_type' ) ); ?>" style="width:100%; max-width:230px" name="<?php echo esc_attr( $this->get_field_name( 'gallery_type' ) ); ?>">
						<?php echo wp_kses( implode( '', $gallery_type_array ), virtue_admin_allowed_html() ); ?>
					</select>
				</p>
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'gallery_columns' ) ); ?>"><?php esc_html_e( 'Gallery Columns', 'virtue' ); ?></label><br />
					<select id="<?php echo esc_attr( $this->get_field_id( 'gallery_columns' ) ); ?>" style="width:100%; max-width:230px;" name="<?php echo esc_attr( $this->get_field_name( 'gallery_columns' ) ); ?>">
						<?php echo wp_kses( implode( '', $gallery_columns_array ), virtue_admin_allowed_html() ); ?>	
					</select>
				</p>
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'gallery_captions' ) ); ?>"><?php esc_html_e( 'Display Captions', 'virtue' ); ?></label><br />
					<select id="<?php echo esc_attr( $this->get_field_id( 'gallery_captions' ) ); ?>" style="width:100%; max-width:230px" name="<?php echo esc_attr( $this->get_field_name( 'gallery_captions' ) ); ?>">
						<?php echo wp_kses( implode( '', $gallery_captions_array ), virtue_admin_allowed_html() ); ?>
					</select>  
				</p>
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'lightbox_size' ) ); ?>"><?php esc_html_e( 'Lightbox Image Size', 'virtue' ); ?></label><br />
					<select id="<?php echo esc_attr( $this->get_field_id( 'lightbox_size' ) ); ?>" style="width:100%; max-width:230px" name="<?php echo esc_attr( $this->get_field_name( 'lightbox_size' ) ); ?>">
						<?php echo wp_kses( implode( '', $lightbox_size_array ), virtue_admin_allowed_html() ); ?>
					</select>
				</p>
				<p style="font-weight:bold;"><?php echo esc_html__( 'If Type Slider', 'virtue' ); ?></p>
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'gallery_width' ) ); ?>"><?php esc_html_e( 'Slider Width (e.g. = 600)', 'virtue' ); ?></label><br />
					<input type="text" class="widefat kad_img_widget_link" name="<?php echo esc_attr( $this->get_field_name( 'gallery_width' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'gallery_width' ) ); ?>" value="<?php echo esc_attr( $gallery_width ); ?>">
				</p>
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'gallery_height' ) ); ?>"><?php esc_html_e( 'Slider Height (e.g. = 400)', 'virtue' ); ?></label><br />
					<input type="text" class="widefat kad_img_widget_link" name="<?php echo esc_attr( $this->get_field_name( 'gallery_height' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'gallery_height' ) ); ?>" value="<?php echo esc_attr( $gallery_height ); ?>">
				</p>
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'gallery_speed' ) ); ?>"><?php esc_html_e( 'Slider Speed (e.g. = 7000)', 'virtue' ); ?></label><br />
					<input type="text" class="widefat kad_img_widget_link" name="<?php echo esc_attr( $this->get_field_name( 'gallery_speed' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'gallery_speed' ) ); ?>" value="<?php echo esc_attr( $gallery_speed ); ?>">
				</p>
			</div>

			<style type="text/css">
			.kad_widget_image_gallery {padding-bottom: 10px;}
			.kad_widget_image_gallery .gallery_images:after { content: "."; display: block; height: 0; clear: both; visibility: hidden; }
			.kad_widget_image_gallery .gallery_images {padding: 5px 5px 0; margin: 10px 0; background: #f2f2f2;}
			.kad_widget_image_gallery .gallery_images img {max-width: 80px; height: auto; float: left; padding: 0 5px 5px 0}
			</style>

		<?php
		}
	}
}
