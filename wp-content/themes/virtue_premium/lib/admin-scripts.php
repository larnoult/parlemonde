<?php
/**
 * Admin scripts
 *
 * @package Virtue Theme
 */

/**
 * Enqueue CSS & JS
 *
 * @param string $hook is the page hook.
 */
function virtue_admin_scripts( $hook ) {
	if ( 'toplevel_page_kad_options' === $hook || 'widgets.php' === $hook ) {
		wp_enqueue_script( 'select2', get_template_directory_uri() . '/assets/js/min/select2-min.js', array( 'jquery' ), VIRTUE_VERSION, false );
		wp_dequeue_script( 'select2-js' );
	}
	if ( 'edit.php' !== $hook && 'post.php' !== $hook && 'post-new.php' !== $hook && 'widgets.php' !== $hook && 'toplevel_page_kad_options' !== $hook && 'term.php' !== $hook ) {
		return;
	}

	wp_enqueue_style( 'kad_adminstyles', get_template_directory_uri() . '/assets/css/kad_adminstyles.css', false, VIRTUE_VERSION );
	if ( class_exists( 'woocommerce' ) ) {
		if ( version_compare( WC_VERSION, '2.7', '>' ) ) {
			wp_register_script( 'select2', get_template_directory_uri() . '/assets/js/min/select2_v4-min.js', array( 'jquery' ), VIRTUE_VERSION, false );
		} else {
			wp_register_script( 'select2', get_template_directory_uri() . '/assets/js/min/select2-min.js', array( 'jquery' ), VIRTUE_VERSION, false );
		}
	} else {
		wp_register_script( 'select2', get_template_directory_uri() . '/assets/js/min/select2_v4-min.js', array( 'jquery' ), VIRTUE_VERSION, false );
	}
	wp_enqueue_script( 'select2' );
	wp_dequeue_script( 'select2-js' );
	wp_register_script( 'mustache-js', get_template_directory_uri() . '/assets/js/vendor/mustache.min.js' );
	wp_enqueue_script( 'kad_adminscripts', get_template_directory_uri() . '/assets/js/min/kad_adminscripts-min.js', array( 'wp-color-picker', 'jquery', 'underscore', 'backbone', 'jquery-ui-sortable', 'mustache-js' ), VIRTUE_VERSION, false );
}
add_action( 'admin_enqueue_scripts', 'virtue_admin_scripts' );


/**
 * Enqueue block editor style
 */
function virtue_block_editor_styles() {
	wp_enqueue_style( 'virtue-guten-editor-styles', get_template_directory_uri() . '/assets/css/guten-editor-style.css', false, VIRTUE_VERSION, 'all' );
}

add_action( 'enqueue_block_editor_assets', 'virtue_block_editor_styles' );


/**
 * Add inline css for fonts
 */
function virtue_editor_dynamic_css() {
	global $virtue_premium;
	$options_fonts = array( 'font_h1', 'font_h2', 'font_h3', 'font_h4', 'font_h5', 'font_p' );
	$load_gfonts   = array();
	foreach ( $options_fonts as $options_key ) {
		if ( isset( $virtue_premium[ $options_key ] ) && true == $virtue_premium[ $options_key ]['google'] ) {
			// check if it's in the array.
			if ( isset( $load_gfonts[ sanitize_key( $virtue_premium[ $options_key ]['font-family'] ) ] ) ) {
				if ( isset( $virtue_premium[ $options_key ]['font-weight'] ) && ! empty( $virtue_premium[ $options_key ]['font-weight'] ) ) {
					if ( isset( $virtue_premium[ $options_key ]['font-style'] ) && ! empty( $virtue_premium[ $options_key ]['font-style'] ) && ! is_numeric( $virtue_premium[ $options_key ]['font-style'] ) && 'normal' !== $virtue_premium[ $options_key ]['font-style'] ) {
						$load_gfonts[ sanitize_key( $virtue_premium[ $options_key ]['font-family'] ) ]['font-style'][ $virtue_premium[ $options_key ]['font-weight'] . $virtue_premium[ $options_key ]['font-style'] ] = $virtue_premium[ $options_key ]['font-weight'] . $virtue_premium[ $options_key ]['font-style'];
					} else {
						$load_gfonts[ sanitize_key( $virtue_premium[ $options_key ]['font-family'] ) ]['font-style'][ $virtue_premium[ $options_key ]['font-weight'] ] = $virtue_premium[ $options_key ]['font-weight'];
					}
				}
				if ( isset( $virtue_premium[ $options_key ]['subsets'] ) && ! empty( $virtue_premium[ $options_key ]['subsets'] ) ) {
					$load_gfonts[ sanitize_key( $virtue_premium[ $options_key ]['font-family'] ) ]['subsets'][ $virtue_premium[ $options_key ]['subsets'] ] = $virtue_premium[ $options_key ]['subsets'];
				}
			} else {
				$load_gfonts[ sanitize_key( $virtue_premium[ $options_key ]['font-family'] ) ] = array(
					'font-family' => $virtue_premium[ $options_key ]['font-family'],
					'font-style'  => array(),
					'subsets'     => array(),
				);
				if ( isset( $virtue_premium[ $options_key ]['font-weight'] ) && ! empty( $virtue_premium[ $options_key ]['font-weight'] ) ) {
					if ( isset( $virtue_premium[ $options_key ]['font-style'] ) && ! empty( $virtue_premium[ $options_key ]['font-style'] ) && ! is_numeric( $virtue_premium[ $options_key ]['font-style'] ) && 'normal' !== $virtue_premium[ $options_key ]['font-style'] ) {
						$load_gfonts[ sanitize_key( $virtue_premium[ $options_key ]['font-family'] ) ]['font-style'][ $virtue_premium[ $options_key ]['font-weight'] . $virtue_premium[ $options_key ]['font-style'] ] = $virtue_premium[ $options_key ]['font-weight'] . $virtue_premium[ $options_key ]['font-style'];
					} else {
						$load_gfonts[ sanitize_key( $virtue_premium[ $options_key ]['font-family'] ) ]['font-style'][ $virtue_premium[ $options_key ]['font-weight'] ] = $virtue_premium[ $options_key ]['font-weight'];
					}
				}
				if ( isset( $virtue_premium[ $options_key ]['subsets'] ) && ! empty( $virtue_premium[ $options_key ]['subsets'] ) ) {
					$load_gfonts[ sanitize_key( $virtue_premium[ $options_key ]['font-family'] ) ]['subsets'][ $virtue_premium[ $options_key ]['subsets'] ] = $virtue_premium[ $options_key ]['subsets'];
				}
			}
		}
		if ( 'font_p' === $options_key ) {
			$path      = trailingslashit( get_template_directory() ) . 'kt-framework/gfont-downloader-json.php';
			$all_fonts = include $path;
			if ( isset( $all_fonts[ $virtue_premium[ $options_key ]['font-family'] ] ) ) {
				$p_font = $all_fonts[ $virtue_premium[ $options_key ]['font-family'] ];
				if ( isset( $p_font['variants']['italic']['400'] ) ) {
					$load_gfonts[ sanitize_key( $virtue_premium[ $options_key ]['font-family'] ) ]['font-style']['400italic'] = '400italic';
				}
				if ( isset( $p_font['variants']['italic']['700'] ) ) {
					$load_gfonts[ sanitize_key( $virtue_premium[ $options_key ]['font-family'] ) ]['font-style']['700italic'] = '700italic';
				}
				if ( isset( $p_font['variants']['normal']['400'] ) ) {
					$load_gfonts[ sanitize_key( $virtue_premium[ $options_key ]['font-family'] ) ]['font-style']['400'] = '400';
				}
				if ( isset( $p_font['variants']['normal']['700'] ) ) {
					$load_gfonts[ sanitize_key( $virtue_premium[ $options_key ]['font-family'] ) ]['font-style']['700'] = '700';
				}
			}
		}
	}
	if ( ! empty( $load_gfonts ) ) {
		// Build the font family link.
		$link    = '';
		$subsets = array();
		foreach ( $load_gfonts as $gfont_values ) {
			if ( ! empty( $link ) ) {
				$link .= '%7C'; // Append a new font to the string.
			}
			$link .= $gfont_values['font-family'];
			if ( ! empty( $gfont_values['font-style'] ) ) {
				$link .= ':';
				$link .= implode( ',', $gfont_values['font-style'] );
			}
			if ( ! empty( $gfont_values['subsets'] ) ) {
				foreach ( $gfont_values['subsets'] as $subset ) {
					if ( ! in_array( $subset, $subsets ) ) {
						array_push( $subsets, $subset );
					}
				}
			}
		}
		if ( ! empty( $subsets ) ) {
			$link .= '&amp;subset=' . implode( ',', $subsets );
		}
		echo '<link href="//fonts.googleapis.com/css?family=' . esc_attr( str_replace( '|', '%7C', $link ) ) . ' " rel="stylesheet">';

	}
	echo '<style type="text/css" id="virtue-editor-font-family">';
	if ( isset( $virtue_premium['font_h1'] ) ) {
		echo 'body.gutenberg-editor-page .editor-post-title__block .editor-post-title__input, body.gutenberg-editor-page .wp-block-heading h1, body.gutenberg-editor-page .editor-block-list__block h1  {
				font-size: ' . esc_attr( $virtue_premium['font_h1']['font-size'] ) . ';
				line-height: ' . esc_attr( $virtue_premium['font_h1']['line-height'] ) . ';
				font-weight: ' . esc_attr( $virtue_premium['font_h1']['font-weight'] ) . ';
				font-family: ' . esc_attr( $virtue_premium['font_h1']['font-family'] ) . ';
				color: ' . esc_attr( $virtue_premium['font_h1']['color'] ) . ';
			}';
	}
	if ( isset( $virtue_premium['font_h2'] ) ) {
		echo 'body.gutenberg-editor-page .wp-block-heading h2, body.gutenberg-editor-page .editor-block-list__block h2 {
			font-size: ' . esc_attr( $virtue_premium['font_h2']['font-size'] ) . ';
			line-height: ' . esc_attr( $virtue_premium['font_h2']['line-height'] ) . ';
			font-weight: ' . esc_attr( $virtue_premium['font_h2']['font-weight'] ) . ';
			font-family: ' . esc_attr( $virtue_premium['font_h2']['font-family'] ) . ';
			color: ' . esc_attr( $virtue_premium['font_h2']['color'] ) . ';
		}';
	}
	if ( isset( $virtue_premium['font_h3'] ) ) {
		echo 'body.gutenberg-editor-page .wp-block-heading h3, body.gutenberg-editor-page .editor-block-list__block h3 {
			font-size: ' . esc_attr( $virtue_premium['font_h3']['font-size'] ) . ';
			line-height: ' . esc_attr( $virtue_premium['font_h3']['line-height'] ) . ';
			font-weight: ' . esc_attr( $virtue_premium['font_h3']['font-weight'] ) . ';
			font-family: ' . esc_attr( $virtue_premium['font_h3']['font-family'] ) . ';
			color: ' . esc_attr( $virtue_premium['font_h3']['color'] ) . ';
		}';
	}
	if ( isset( $virtue_premium['font_h4'] ) ) {
		echo 'body.gutenberg-editor-page .wp-block-heading h4, body.gutenberg-editor-page .editor-block-list__block h4 {
			font-size: ' . esc_attr( $virtue_premium['font_h4']['font-size'] ) . ';
			line-height: ' . esc_attr( $virtue_premium['font_h4']['line-height'] ) . ';
			font-weight: ' . esc_attr( $virtue_premium['font_h4']['font-weight'] ) . ';
			font-family: ' . esc_attr( $virtue_premium['font_h4']['font-family'] ) . ';
			color: ' . esc_attr( $virtue_premium['font_h4']['color'] ) . ';
		}body.gutenberg-editor-page .editor-block-list__block .widgets-container .so-widget h4 {font-size:inherit; letter-spacing:normal; font-family:inherit;}';
	}
	if ( isset( $virtue_premium['font_h5'] ) ) {
		echo 'body.gutenberg-editor-page .wp-block-heading h5, body.gutenberg-editor-page .editor-block-list__block h5 {
			font-size: ' . esc_attr( $virtue_premium['font_h5']['font-size'] ) . ';
			line-height: ' . esc_attr( $virtue_premium['font_h5']['line-height'] ) . ';
			font-weight: ' . esc_attr( $virtue_premium['font_h5']['font-weight'] ) . ';
			font-family: ' . esc_attr( $virtue_premium['font_h5']['font-family'] ) . ';
			color: ' . esc_attr( $virtue_premium['font_h5']['color'] ) . ';
		}';
	}
	if ( isset( $virtue_premium['font_p'] ) ) {
		echo '.edit-post-visual-editor, .edit-post-visual-editor p {
			font-size: ' . esc_attr( $virtue_premium['font_p']['font-size'] ) . ';
			font-weight: ' . esc_attr( $virtue_premium['font_p']['font-weight'] ) . ';
			font-family: ' . esc_attr( $virtue_premium['font_p']['font-family'] ) . ';
			color: ' . esc_attr( $virtue_premium['font_p']['color'] ) . ';
		}';
	}
	echo '</style>';
}
add_action( 'admin_head-post.php', 'virtue_editor_dynamic_css' );
add_action( 'admin_head-post-new.php', 'virtue_editor_dynamic_css' );
add_action( 'admin_head-edit.php', 'virtue_editor_dynamic_css' );

/**
 * Set gallery link to file.
 *
 * @param array $settings an array of gallery settings.
 */
function virtue_gallery_default_type_set_link( $settings ) {
	$settings['galleryDefaults']['link'] = 'file';
	return $settings;
}
add_filter( 'media_view_settings', 'virtue_gallery_default_type_set_link' );

/**
 * Set gallery extra options
 */
function virtue_media_gallery_extras() {
?>
<script type="text/html" id="tmpl-custom-gallery-setting">
	<hr style="clear: both;">
	<h3 style="margin-top:10px;"><?php esc_html_e( 'KT Extra Gallery Settings', 'virtue' ); ?></h3>
	<label class="setting">
		<span><?php esc_html_e( 'Type', 'virtue' ); ?></span>
		<select data-setting="type">
			<option value="default"><?php esc_html_e( 'Default', 'virtue' ); ?></option>
			<option value="slider"><?php esc_html_e( 'Slider', 'virtue' ); ?></option>
			<option value="carousel"><?php esc_html_e( 'Carousel', 'virtue' ); ?></option>
			<option value="mosaic"><?php esc_html_e( 'Mosaic', 'virtue' ); ?></option>
			<option value="grid"><?php esc_html_e( 'Custom Grid', 'virtue' ); ?></option>
			<option value="imagecarousel"><?php esc_html_e( 'Image Carousel', 'virtue' ); ?></option>
		</select>
	</label>
	<label class="setting">
		<span><?php esc_html_e( 'Show Captions', 'virtue' ); ?></span>
		<select data-setting="caption">
			<option value="default"><?php esc_html_e( 'Default', 'virtue' ); ?></option>
			<option value="false"><?php esc_html_e( 'False', 'virtue' ); ?></option>
			<option value="true"><?php esc_html_e( 'True', 'virtue' ); ?></option>
		</select>
	</label>
	<label class="setting">
		<span><?php esc_html_e( 'Masonry', 'virtue' ); ?></span>
		<select data-setting="masonry">
			<option value="default"><?php esc_html_e( 'Default', 'virtue' ); ?></option>
			<option value="false"><?php esc_html_e( 'False', 'virtue' ); ?></option>
			<option value="true"><?php esc_html_e( 'True', 'virtue' ); ?></option>
		</select>
	</label>
	<h4><?php esc_html_e( 'Slider Option - Settings', 'virtue' ); ?></h4>
	<label class="setting">
		<span style="min-width: 50px;"><?php esc_html_e( 'Width', 'virtue' ); ?></span>
		<input type="text" value="" data-setting="width" style="float:left;">
	</label>
	<label class="setting">
		<span style="min-width: 50px;"><?php esc_html_e( 'Height', 'virtue' ); ?></span>
		<input type="text" value="" data-setting="height" style="float:left;">
	</label>
	<hr style="clear: both;">
</script>

<script> 
	jQuery( window ).load(function () {
		if ( typeof wp === 'undefined' || ! wp.media || ! wp.media.gallery ) {
			return;
		}
		jQuery.extend(wp.media.gallery.defaults, {
			type: 'default',
			caption: 'default',
			masonry: 'default',
			width: '',
			height: '',
		}); 

		wp.media.view.Settings.Gallery = wp.media.view.Settings.Gallery.extend({
			template: function(view){
				return wp.media.template('gallery-settings')(view) + wp.media.template('custom-gallery-setting')(view);
			}
		});
	});
</script>
<?php
}
add_action( 'print_media_templates', 'virtue_media_gallery_extras' );
