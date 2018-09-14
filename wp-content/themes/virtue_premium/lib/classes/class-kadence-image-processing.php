<?php 
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
if ( ! class_exists( 'Kadence_Image_Processing' ) ) {
	class Kadence_Image_Processing_Exception extends Exception {}
	class Kadence_Image_Processing {
		protected static $instance = null;
        private $is_updating_backup_sizes = false;
        public $throwOnError = false;
        /**
         * No cloning allowed
         */
        private function __clone() {}
		/**
		 * Singleton
		 */
        public static function getInstance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}
		public function __construct() {
			add_filter( 'update_post_metadata', array( $this, 'filter_update_post_metadata' ), 10, 5 );
		}
        /**
		 * Filter the post meta data for backup sizes
		 *
		 * Unfortunately WordPress core is lacking hooks in its image resizing functions so we are reduced
		 * to this hackery to detect when images are resized and previous versions are relegated to backup sizes.
		 *
		 */
		public function filter_update_post_metadata( $check, $object_id, $meta_key, $meta_value, $prev_value ) {
			if ( '_wp_attachment_backup_sizes' !== $meta_key ) {
				return $check;
			}

			$current_value = get_post_meta( $object_id, $meta_key, true );

			if ( ! $current_value ) {
				$current_value = array();
			}

			$diff = array_diff_key( $meta_value, $current_value );

			if ( ! $diff ) {
				return $check;
			}

			$key = key( $diff );
			$suffix = substr( $key, strrpos( $key, '-' ) + 1 );

			$image_meta = self::kt_get_image_meta( $object_id );

			foreach ( $image_meta['sizes'] as $size_name => $size ) {
				if ( 0 !== strpos( $size_name, 'kip-' ) ) {
					continue;
				}

				$meta_value[ $size_name . '-' . $suffix ] = $size;
				unset( $image_meta['sizes'][ $size_name ] );
			}

			if ( ! $this->is_updating_backup_sizes ) {
				$this->is_updating_backup_sizes = true;
				update_post_meta( $object_id, '_wp_attachment_backup_sizes', $meta_value );
				wp_update_attachment_metadata( $object_id, $image_meta );
				return true;
			}

			$this->is_updating_backup_sizes = false;

			return $check;
		}
		public function process( $id = null, $width, $height) {
			$sizes = array(array($width, $height));
			$retina = array();
			if ( apply_filters( 'kadence_retina_support', true ) ) {
                    $retina_w = $width*2;
                    $retina_h = $height*2;
                    $retina = array(array($retina_w,  $retina_h));
            }
            $sizes = array_reverse(array_merge($sizes,$retina));
			// If we haven't created the image yet, lets do that now.
			$created = false;
			foreach ( $sizes as $size ) {
				if ( self::kt_does_size_already_exist_for_image( $id,  $size[0], $size[1] ) ) {
					continue;
				}

				if ( self::kt_is_size_larger_than_original( $id,  $size[0], $size[1] ) ) {
					continue;
				}
				$item = array(
					'id' => $id,
					'width' => $size[0],
					'height' => $size[1],
					'crop' => true,
				);
				$created = $this->create_image_size( $item );
			}
			return $created;
		}
		public function create_image_size( $item ) {
			try {
				$defaults = array(
					'id' => 0,
					'width' => 0,
					'height' => 0,
					'crop' => false,
				);
				$item = wp_parse_args( $item, $defaults );

				$id = $item['id'];
				$width = $item['width'];
				$height = $item['height'];
				$crop = $item['crop'];

				if ( ! $width && ! $height ) {
					throw new Kadence_Image_Processing_Exception( "Invalid image dimensions" );
				}
				$suffix = "{$width}x{$height}";
				$image_meta = self::kt_get_image_meta($id );
				if ( ! $image_meta ) {
					return false;
				}
				$img_path = get_attached_file( $id );

				if ( ! $img_path ) {
					return false;
				}

				$ext = substr($image_meta['file'], strrpos($image_meta['file'], "."));

				$dst_rel_path = str_replace( '.' . $ext, '', $img_path );
                $destfilename = "{$dst_rel_path}-{$suffix}.{$ext}";


				if(file_exists( $destfilename )) {
					$filename = basename($image_meta['file'], $ext) . '-'.$width.'x'.$height.'' . $ext;
					$image_meta['sizes'][ $size_name ] = array(
						'file'      => $filename,
						'width'     => $width,
						'height'    => $height,
						'mime-type' => isset($image_meta['sizes']['thumbnail']) ? $image_meta['sizes']['thumbnail']['mime-type'] : '',
					);
					wp_update_attachment_metadata( $id, $image_meta );
					
					return true;
				}

				$editor = wp_get_image_editor( $img_path );

				if ( is_wp_error( $editor ) ) {
					throw new Kadence_Image_Processing_Exception( 'Unable to get WP_Image_Editor for file (is GD or ImageMagick installed?)' );
				}

				if ( is_wp_error( $editor->resize( $width, $height, $crop ) ) ) {
					throw new Kadence_Image_Processing_Exception( 'Error resizing image');
				}

				$resized_file = $editor->save();
				if ( is_wp_error( $resized_file ) ) {
					throw new Kadence_Image_Processing_Exception( 'Unable to save resized image file' );
				}

				$size_name = self::get_size_name( array( $width, $height) );
				$image_meta['sizes'][ $size_name ] = array(
					'file'      => $resized_file['file'],
					'width'     => $resized_file['width'],
					'height'    => $resized_file['height'],
					'mime-type' => $resized_file['mime-type'],
				);
				wp_update_attachment_metadata( $id, $image_meta );

				return true;
			}
	        catch (Kadence_Image_Processing_Exception $ex) {
            	// Only for debugging
                if ($this->throwOnError) {
                    // Bubble up exception.
                    throw $ex;
                } else {
                    // Return false, so that this patch is backwards-compatible.
                    return false;
                }
            }
		}
		public static function kt_get_image_meta( $id ) {
			return wp_get_attachment_metadata( $id );
		}
		public static function kt_is_size_larger_than_original($id, $width, $height) {
				$image_meta = self::kt_get_image_meta( $id );
				// no size? then lets try
				if ( ! isset( $image_meta['width'] ) || ! isset( $image_meta['height'] ) ) {
					return true;
				}
				if ( $width > $image_meta['width'] || $height > $image_meta['height'] ) {
					return true;
				}

				return false;
		}
		public static function kt_does_size_already_exist_for_image( $id, $width, $height ) {
			$image_meta = self::kt_get_image_meta( $id );
			$size_name = self::get_size_name( array($width, $height));
			return isset( $image_meta['sizes'][ $size_name ] );
		}
		public static function get_size_name( $size ) {
			return 'kip-' . $size[0] . 'x' . $size[1];
		}
	}
	Kadence_Image_Processing::getInstance();
}