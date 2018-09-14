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
	wp_register_script( 'kadence-toolkit-install', get_template_directory_uri() . '/assets/js/admin-activate.js', false, VIRTUE_VERSION );
	wp_enqueue_style( 'virtue_admin_styles', get_template_directory_uri() . '/assets/css/kad_adminstyles.css', false, VIRTUE_VERSION );

	if ( 'edit.php' !== $hook && 'post.php' !== $hook && 'post-new.php' !== $hook && 'widgets.php' !== $hook ) {
		return;
	}
	wp_enqueue_media();
	wp_enqueue_script( 'virtue_admin_script', get_template_directory_uri() . '/assets/js/virtue_admin.js', false, VIRTUE_VERSION );

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
	global $virtue;
	$options_fonts = array( 'font_h1', 'font_h2', 'font_h3', 'font_h4', 'font_h5', 'font_p' );
	$load_gfonts   = array();
	foreach ( $options_fonts as $options_key ) {
		if ( isset( $virtue[ $options_key ] ) && isset( $virtue[ $options_key ]['google'] ) && 'false' !== $virtue[ $options_key ]['google'] ) {
			// check if it's in the array.
			if ( isset( $load_gfonts[ sanitize_key( $virtue[ $options_key ]['font-family'] ) ] ) ) {
				if ( isset( $virtue[ $options_key ]['font-weight'] ) && ! empty( $virtue[ $options_key ]['font-weight'] ) ) {
					if ( isset( $virtue[ $options_key ]['font-style'] ) && ! empty( $virtue[ $options_key ]['font-style'] ) && ! is_numeric( $virtue[ $options_key ]['font-style'] ) && 'normal' !== $virtue[ $options_key ]['font-style'] ) {
						$load_gfonts[ sanitize_key( $virtue[ $options_key ]['font-family'] ) ]['font-style'][ $virtue[ $options_key ]['font-weight'] . $virtue[ $options_key ]['font-style'] ] = $virtue[ $options_key ]['font-weight'] . $virtue[ $options_key ]['font-style'];
					} else {
						$load_gfonts[ sanitize_key( $virtue[ $options_key ]['font-family'] ) ]['font-style'][ $virtue[ $options_key ]['font-weight'] ] = $virtue[ $options_key ]['font-weight'];
					}
				}
				if ( isset( $virtue[ $options_key ]['subsets'] ) && ! empty( $virtue[ $options_key ]['subsets'] ) ) {
					$load_gfonts[ sanitize_key( $virtue[ $options_key ]['font-family'] ) ]['subsets'][ $virtue[ $options_key ]['subsets'] ] = $virtue[ $options_key ]['subsets'];
				}
			} else {
				$load_gfonts[ sanitize_key( $virtue[ $options_key ]['font-family'] ) ] = array(
					'font-family' => $virtue[ $options_key ]['font-family'],
					'font-style'  => array(),
					'subsets'     => array(),
				);
				if ( isset( $virtue[ $options_key ]['font-weight'] ) && ! empty( $virtue[ $options_key ]['font-weight'] ) ) {
					if ( isset( $virtue[ $options_key ]['font-style'] ) && ! empty( $virtue[ $options_key ]['font-style'] ) && ! is_numeric( $virtue[ $options_key ]['font-style'] ) && 'normal' !== $virtue[ $options_key ]['font-style'] ) {
						$load_gfonts[ sanitize_key( $virtue[ $options_key ]['font-family'] ) ]['font-style'][ $virtue[ $options_key ]['font-weight'] . $virtue[ $options_key ]['font-style'] ] = $virtue[ $options_key ]['font-weight'] . $virtue[ $options_key ]['font-style'];
					} else {
						$load_gfonts[ sanitize_key( $virtue[ $options_key ]['font-family'] ) ]['font-style'][ $virtue[ $options_key ]['font-weight'] ] = $virtue[ $options_key ]['font-weight'];
					}
				}
				if ( isset( $virtue[ $options_key ]['subsets'] ) && ! empty( $virtue[ $options_key ]['subsets'] ) ) {
					$load_gfonts[ sanitize_key( $virtue[ $options_key ]['font-family'] ) ]['subsets'][ $virtue[ $options_key ]['subsets'] ] = $virtue[ $options_key ]['subsets'];
				}
			}
		}
		if ( 'font_p' === $options_key ) {
			$path      = trailingslashit( get_template_directory() ) . 'lib/gfont-json.php';
			$all_fonts = include $path;
			if ( isset( $all_fonts[ $virtue[ $options_key ]['font-family'] ] ) ) {
				$p_font = $all_fonts[ $virtue[ $options_key ]['font-family'] ];
				if ( isset( $p_font['variants']['italic']['400'] ) ) {
					$load_gfonts[ sanitize_key( $virtue[ $options_key ]['font-family'] ) ]['font-style']['400italic'] = '400italic';
				}
				if ( isset( $p_font['variants']['italic']['700'] ) ) {
					$load_gfonts[ sanitize_key( $virtue[ $options_key ]['font-family'] ) ]['font-style']['700italic'] = '700italic';
				}
				if ( isset( $p_font['variants']['normal']['400'] ) ) {
					$load_gfonts[ sanitize_key( $virtue[ $options_key ]['font-family'] ) ]['font-style']['400'] = '400';
				}
				if ( isset( $p_font['variants']['normal']['700'] ) ) {
					$load_gfonts[ sanitize_key( $virtue[ $options_key ]['font-family'] ) ]['font-style']['700'] = '700';
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
	if ( isset( $virtue['font_h1'] ) ) {
		echo 'body.gutenberg-editor-page .editor-post-title__block .editor-post-title__input, body.gutenberg-editor-page .wp-block-heading h1, body.gutenberg-editor-page .editor-block-list__block h1 {
				font-size: ' . esc_attr( $virtue['font_h1']['font-size'] ) . ';
				line-height: ' . esc_attr( $virtue['font_h1']['line-height'] ) . ';
				font-weight: ' . esc_attr( $virtue['font_h1']['font-weight'] ) . ';
				font-family: ' . esc_attr( $virtue['font_h1']['font-family'] ) . ';
				color: ' . esc_attr( $virtue['font_h1']['color'] ) . ';
			}';
	}
	if ( isset( $virtue['font_h2'] ) ) {
		echo 'body.gutenberg-editor-page .wp-block-heading h2, body.gutenberg-editor-page .editor-block-list__block h2 {
			font-size: ' . esc_attr( $virtue['font_h2']['font-size'] ) . ';
			line-height: ' . esc_attr( $virtue['font_h2']['line-height'] ) . ';
			font-weight: ' . esc_attr( $virtue['font_h2']['font-weight'] ) . ';
			font-family: ' . esc_attr( $virtue['font_h2']['font-family'] ) . ';
			color: ' . esc_attr( $virtue['font_h2']['color'] ) . ';
		}';
	}
	if ( isset( $virtue['font_h3'] ) ) {
		echo 'body.gutenberg-editor-page .wp-block-heading h3, body.gutenberg-editor-page .editor-block-list__block h3 {
			font-size: ' . esc_attr( $virtue['font_h3']['font-size'] ) . ';
			line-height: ' . esc_attr( $virtue['font_h3']['line-height'] ) . ';
			font-weight: ' . esc_attr( $virtue['font_h3']['font-weight'] ) . ';
			font-family: ' . esc_attr( $virtue['font_h3']['font-family'] ) . ';
			color: ' . esc_attr( $virtue['font_h3']['color'] ) . ';
		}';
	}
	if ( isset( $virtue['font_h4'] ) ) {
		echo 'body.gutenberg-editor-page .wp-block-heading h4, body.gutenberg-editor-page .editor-block-list__block h4 {
			font-size: ' . esc_attr( $virtue['font_h4']['font-size'] ) . ';
			line-height: ' . esc_attr( $virtue['font_h4']['line-height'] ) . ';
			font-weight: ' . esc_attr( $virtue['font_h4']['font-weight'] ) . ';
			font-family: ' . esc_attr( $virtue['font_h4']['font-family'] ) . ';
			color: ' . esc_attr( $virtue['font_h4']['color'] ) . ';
		} body.gutenberg-editor-page .editor-block-list__block .widgets-container .so-widget h4 {font-size:inherit; letter-spacing:normal; font-family:inherit;}';
	}
	if ( isset( $virtue['font_h5'] ) ) {
		echo 'body.gutenberg-editor-page .wp-block-heading h5, body.gutenberg-editor-page .editor-block-list__block h5 {
			font-size: ' . esc_attr( $virtue['font_h5']['font-size'] ) . ';
			line-height: ' . esc_attr( $virtue['font_h5']['line-height'] ) . ';
			font-weight: ' . esc_attr( $virtue['font_h5']['font-weight'] ) . ';
			font-family: ' . esc_attr( $virtue['font_h5']['font-family'] ) . ';
			color: ' . esc_attr( $virtue['font_h5']['color'] ) . ';
		}';
	}
	if ( isset( $virtue['font_p'] ) ) {
		echo '.edit-post-visual-editor, .edit-post-visual-editor p {
			font-size: ' . esc_attr( $virtue['font_p']['font-size'] ) . ';
			font-weight: ' . esc_attr( $virtue['font_p']['font-weight'] ) . ';
			font-family: ' . esc_attr( $virtue['font_p']['font-family'] ) . ';
			color: ' . esc_attr( $virtue['font_p']['color'] ) . ';
		}';
	}
	echo '</style>';
}
add_action( 'admin_head-post.php', 'virtue_editor_dynamic_css' );
add_action( 'admin_head-post-new.php', 'virtue_editor_dynamic_css' );
add_action( 'admin_head-edit.php', 'virtue_editor_dynamic_css' );
