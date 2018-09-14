<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
add_action( 'wp_enqueue_scripts', 'kadence_gfont_unhook_google', 160 );
function kadence_gfont_unhook_google() {
	global $virtue_premium;
	if ( isset( $virtue_premium['load_google_fonts_locally'] ) && 1 == $virtue_premium['load_google_fonts_locally'] ) {
		wp_dequeue_style('redux-google-fonts-virtue_premium');
	}
}
add_action( 'wp_head', 'kadence_gfont_local_css', 5);
function kadence_gfont_local_css() {
	if ( ! class_exists( 'Redux' ) ) {
		return;
	}
	if ( ReduxFramework::$_version <= '3.5.6' ) {
		return;
	}
	if( ! class_exists( 'Redux_Filesystem' ) ) {
		return;
	}
	global $virtue_premium;
	// Check if css is needed 
	if ( isset( $virtue_premium['load_google_fonts_locally'] ) && 1 == $virtue_premium['load_google_fonts_locally'] ) {
		// Get the needed font families and variants.
		$options_fonts = array('font_logo_style', 'font_tagline_style', 'font_shop_title', 'font_h1', 'font_h2', 'font_h3', 'font_h4', 'font_h5', 'font_p', 'font_primary_menu', 'font_secondary_menu', 'font_mobile_menu');
		$load_gfonts = array();
		foreach ($options_fonts as $options_key) {
			if ( isset( $virtue_premium[$options_key] ) && true == $virtue_premium[$options_key]['google'] ) {
				if( isset( $load_gfonts[ sanitize_key( $virtue_premium[$options_key]['font-family'] ) ] ) ) {
					// check for a different variation;
					if(isset($virtue_premium[$options_key]['font-weight']) && !empty($virtue_premium[$options_key]['font-weight']) ) {
						$load_gfonts[ sanitize_key( $virtue_premium[$options_key]['font-family'] ) ]['font-weight'][$virtue_premium[$options_key]['font-weight']] = $virtue_premium[$options_key]['font-weight'];
					}
					if(isset($virtue_premium[$options_key]['font-style']) && !empty($virtue_premium[$options_key]['font-style']) && !is_numeric($virtue_premium[$options_key]['font-style'])  ) {
						$load_gfonts[ sanitize_key( $virtue_premium[$options_key]['font-family'] ) ]['font-style'][$virtue_premium[$options_key]['font-style']] = $virtue_premium[$options_key]['font-style'];
					}
					if(isset($virtue_premium[$options_key]['subsets']) && !empty($virtue_premium[$options_key]['subsets']) ) {
						$load_gfonts[ sanitize_key( $virtue_premium[$options_key]['font-family'] ) ]['subsets'] = $virtue_premium[$options_key]['subsets'];
					}
				} else {
					$load_gfonts[ sanitize_key( $virtue_premium[$options_key]['font-family'] ) ] = array(
						'font-family' => $virtue_premium[$options_key]['font-family'],
						'font-weight' => array(),
						'font-style' => array(),
						'subsets' => array(),
					);
					if(isset($virtue_premium[$options_key]['font-weight']) && !empty($virtue_premium[$options_key]['font-weight']) ) {
						$load_gfonts[ sanitize_key( $virtue_premium[$options_key]['font-family'] ) ]['font-weight'][$virtue_premium[$options_key]['font-weight']] = $virtue_premium[$options_key]['font-weight'];
					}
					if(isset($virtue_premium[$options_key]['font-style']) && !empty($virtue_premium[$options_key]['font-style']) && !is_numeric($virtue_premium[$options_key]['font-style'])  ) {
						$load_gfonts[ sanitize_key( $virtue_premium[$options_key]['font-family'] ) ]['font-style'][$virtue_premium[$options_key]['font-style']] = $virtue_premium[$options_key]['font-style'];
					}
					if(isset($virtue_premium[$options_key]['subsets']) && !empty($virtue_premium[$options_key]['subsets']) ) {
						$load_gfonts[ sanitize_key( $virtue_premium[$options_key]['font-family'] ) ]['subsets'] = $virtue_premium[$options_key]['subsets'];
					}
				}
				if ( 'font_p' == $options_key ) {
					$load_gfonts[ sanitize_key( $virtue_premium[$options_key]['font-family'] ) ]['font-style']['italic'] = 'italic';
					$load_gfonts[ sanitize_key( $virtue_premium[$options_key]['font-family'] ) ]['font-style']['normal'] = 'normal';
					$load_gfonts[ sanitize_key( $virtue_premium[$options_key]['font-family'] ) ]['font-weight']['400'] = '400';
					$load_gfonts[ sanitize_key( $virtue_premium[$options_key]['font-family'] ) ]['font-weight']['700'] = '700';
				}
			}
		}
		// Check if google fonts are selected
		if( ! empty( $load_gfonts ) ) {
			$outputcss = '<style type="text/css" id="kt-local-fonts-css">';
			// Build the output css
			// Check if those are downloaded and download needed files.
			foreach ( $load_gfonts as $gfont_values ) {
				$fontloader = new Kadence_GFont_Downloader( $gfont_values );
				$outputcss .= $fontloader->output_css( $gfont_values );
			}
			$outputcss .= '</style>';
			echo $outputcss;

		}
	}
}
class Kadence_GFont_Downloader {

	private $f_family;
	private $f_folder_path;
	private $f_folder_url;
	private $f;
	private static $all_fonts 		= null;

	public function __construct( $gfont_values ) {
		$this->f_family      	= $gfont_values['font-family'];
		$this->f_folder_path 	= $this->get_path() . '/' . sanitize_key( $this->f_family );
		$this->f_folder_url  	= $this->get_url() . '/' . sanitize_key( $this->f_family );
		$this->f        		= $this->get_font_family();
	}
	private function filesystem() {
		return Redux_Filesystem::get_instance();
	}
	public function get_path() {
		// Get the upload directory for this site.
		$upload_dir = wp_upload_dir();
		$path       = untrailingslashit( wp_normalize_path( $upload_dir['basedir'] ) ) . '/kadence-gfonts';

		// If the folder doesn't exist, create it.
		if ( ! file_exists( $path ) ) {
			$this->filesystem()->execute( 'mkdir', $path, array( 'chmod' => FS_CHMOD_DIR ) );
		}

		// Return the path.
		return apply_filters( 'kadence_gfonts_path', $path );
	}
	public function get_url() {
		// Get the upload directory for this site.
		$upload_dir = wp_upload_dir();

		// The URL.
		$url = trailingslashit( $upload_dir['baseurl'] );
		// Take care of domain mapping.
		// When using domain mapping we have to make sure that the URL to the file
		// does not include the original domain but instead the mapped domain.
		if ( defined( 'DOMAIN_MAPPING' ) && DOMAIN_MAPPING ) {
			if ( function_exists( 'domain_mapping_siteurl' ) && function_exists( 'get_original_url' ) ) {
				$mapped_domain   = domain_mapping_siteurl( false );
				$original_domain = get_original_url( 'siteurl' );
				$url = str_replace( $original_domain, $mapped_domain, $url );
			}
		}
		return apply_filters( 'kadence_gfonts_url', untrailingslashit( esc_url_raw( $url ) ) . '/kadence-gfonts' );
	}
	private function get_fonts() {
		if ( is_null( self::$all_fonts ) ) {

			$path = trailingslashit( get_template_directory() ) . 'kt-framework/gfont-downloader-json.php';
			self::$all_fonts = include $path;
		}
		return self::$all_fonts;
	}
	private function get_font_family() {
		// Get the fonts array.
		$fonts = $this->get_fonts();
		if ( isset( $fonts[$this->f_family] ) ) {
			return $fonts[$this->f_family];
		}
	}
	public function download_font_family( $gfont_values ) {
		if ( empty( $gfont_values['font-style'] ) ) {
			if ( empty( $gfont_values['font-weight'] ) ) {
				if ( isset( $this->f['variants']['normal']['400']['url'] ) ) {
					$url_array = $this->f['variants']['normal']['400']['url'];
					foreach ($url_array as $file_type_key => $file_url ) {
						if( 'svg' != $file_type_key ) {
							$this->download_font_file( $file_url );
						}
					}
				}
			} else {
				foreach ($gfont_values['font-weight'] as $weight_key => $weight_value) {
					if ( isset( $this->f['variants']['normal'][$weight_key]['url'] ) ) {
						$url_array = $this->f['variants']['normal'][$weight_key]['url'];
						foreach ($url_array as $file_type_key => $file_url ) {
							if( 'svg' != $file_type_key ) {
								$this->download_font_file( $file_url );
							}
						}
					}
				}
			}
		} else {
			foreach ($gfont_values['font-style'] as $style_key => $style_value) {
				if ( empty( $gfont_values['font-weight'] ) ) {
					if( isset( $this->f['variants'][$style_key]['400']['url'] ) ) {
						$url_array = $this->f['variants'][$style_key]['400']['url'];
						foreach ($url_array as $file_type_key => $file_url ) {
							if( 'svg' != $file_type_key ) {
								$this->download_font_file( $file_url );
							}
						}
					}
				} else {
					foreach ($gfont_values['font-weight'] as $weight_key => $weight_value) {
						if( isset( $this->f['variants'][$style_key][$weight_key]['url'] ) ) {
							$url_array = $this->f['variants'][$style_key][$weight_key]['url'];
							foreach ($url_array as $file_type_key => $file_url ) {
								if( 'svg' != $file_type_key ) {
									$this->download_font_file( $file_url );
								}
							}
						}
					}
				}
			}
		}
	}
	private function get_filename_from_url( $url ) {
		$url_parts   = explode( '/', $url );
		$parts_count = count( $url_parts );
		if ( 1 < $parts_count ) {
			return $url_parts[ count( $url_parts ) - 1 ];
		}
		return $url;
	}
	private function download_font_file( $url ) {
		$path     = $this->f_folder_path . '/' . $this->get_filename_from_url( $url );

		// If the folder doesn't exist, create it.
		if ( ! file_exists( $this->f_folder_path ) ) {
			$this->filesystem()->execute( 'mkdir', $this->f_folder_path, array( 'chmod' => FS_CHMOD_DIR ) );
		}
		// If the file exists no reason to do anything.
		if ( file_exists( $path ) ) {
			return true;
		}
		$response = wp_remote_get( $url );
		if ( is_wp_error( $response ) ) {
			return false;
		}
		$html = wp_remote_retrieve_body( $response );
		if ( is_wp_error( $html ) ) {
			return false;
		}
		if ( empty( $html ) ) {
			return false;
		}

		return  $this->filesystem()->execute('put_contents', $path, array( 'content' => $html, 'chmod' => FS_CHMOD_FILE ) );

	}
	public function get_variant_fontface_css( $style, $weight ) {
		$font_face = "@font-face{font-family:'{$this->f_family}';";

		// Get the font-style.
		$font_face .= "font-style:{$style};";

		// Get the font-weight.
		$font_face  .= "font-weight:{$weight};";
		// Get the font-names.
		if ( isset( $this->f['variants'][$style][$weight]['local'][0] ) ) {
			$font_name_0 = $this->f['variants'][$style][$weight]['local'][0];
		}
		if ( isset( $this->f['variants'][$style][$weight]['local'][1] ) ) {
			$font_name_1 = $this->f['variants'][$style][$weight]['local'][1];
		}
		if( isset( $font_name_0 ) ) {
			$font_face  .= "src:local({$font_name_0})";
		}
		if( isset( $font_name_1 ) ) {
			$font_face .= ",local({$font_name_1})";
		}
		if ( isset( $this->f['variants'][$style][$weight]['url'] ) ) {
			$url_array = $this->f['variants'][$style][$weight]['url'];
			// Get the font-url.
			foreach ($url_array as $file_type_key => $file_url ) {
				if( 'svg' != $file_type_key ) {
					if( 'eot' == $file_type_key ){
						$file_type_key = 'embedded-opentype';
					}
					$font_url = $this->f_folder_url . '/' . $this->get_filename_from_url( $file_url );
					$font_face .= ",url({$font_url}) format('{$file_type_key}')";
				}
			}
		}
		$font_face .= ";}";

		return $font_face;
	}
	public function output_css( $gfont_values ) {
		if ( ! $this->f ) {
			return;
		}

		$font_css = '';

		// Download files.
		$this->download_font_family( $gfont_values );

		// Create the @font-face CSS.
		if ( empty( $gfont_values['font-style'] ) ) {
			if ( empty( $gfont_values['font-weight'] ) ) {
				$font_css .= $this->get_variant_fontface_css( 'normal', '400' );
			} else {
				foreach ($gfont_values['font-weight'] as $weight_key => $weight_value) {
					$font_css .= $this->get_variant_fontface_css( 'normal', $weight_key );
				}
			}
		} else {
			foreach ($gfont_values['font-style'] as $style_key => $style_value) {
				if ( empty( $gfont_values['font-weight'] ) ) {
					$font_css .= $this->get_variant_fontface_css( $style_key, '400' );
				} else {
					foreach ($gfont_values['font-weight'] as $weight_key => $weight_value) {
						$font_css .= $this->get_variant_fontface_css( $style_key, $weight_key );
					}
				}
			}
		}

		return $font_css;

	}
}