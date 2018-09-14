<?php

// Adds all the predefined styles to the editor
add_filter( 'tiny_mce_before_init', 'wp_edit_style_formats_mce_before_init' );
function wp_edit_style_formats_mce_before_init( $settings ) {
	
	//*************************************
	// Predefined Styles - if user selected
	//************************************/
	$options = get_option('wp_edit_editor');
	$enable_predefined = (isset($options['editor_add_pre_styles']) && $options['editor_add_pre_styles'] === '1') ? '1' : '0';
	
	if($enable_predefined == 1) {
		
		$style_formats_predefined = array(
			
			//
			// Our Predefined Styles need to be added as well
			array(
				'title' => 'Defined Styles',
				'items' => array( 
				
					array(
						'title' => 'Text Styles',
						'items' => array( 
						
							array(
								'title' => 'Bold Black Text',
								'inline' => 'span',
								'styles' => array(
									'color' => '#000',
									'fontWeight' => 'bold'
								)
							),
							array(
								'title' => 'Italic Black Text',
								'inline' => 'span',
								'styles' => array(
									'color' => '#000',
									'fontStyle' => 'italic'
								)
							),
							array(
								'title' => 'Bold Italic Black Text',
								'inline' => 'span',
								'styles' => array(
									'color' => '#000',
									'fontWeight' => 'bold',
									'fontStyle' => 'italic'
								)
							),
							array(
								'title' => 'Bold Red Text',
								'inline' => 'span',
								'styles' => array(
									'color' => '#f00',
									'fontWeight' => 'bold'
								)
							),
							array(
								'title' => 'Italic Red Text',
								'inline' => 'span',
								'styles' => array(
									'color' => '#f00',
									'fontStyle' => 'italic'
								)
							),
							array(
								'title' => 'Bold Italic Red Text',
								'inline' => 'span',
								'styles' => array(
									'color' => '#f00',
									'fontWeight' => 'bold',
									'fontStyle' => 'italic'
								)
							),
							array(
								'title' => 'Bold Blue Text',
								'inline' => 'span',
								'styles' => array(
									'color' => '#0040FF',
									'fontWeight' => 'bold'
								)
							),
							array(
								'title' => 'Italic Blue Text',
								'inline' => 'span',
								'styles' => array(
									'color' => '#0040FF',
									'fontStyle' => 'italic'
								)
							),
							array(
								'title' => 'Bold Italic Blue Text',
								'inline' => 'span',
								'styles' => array(
									'color' => '#0040FF',
									'fontWeight' => 'bold',
									'fontStyle' => 'italic'
								)
							),
							array(
								'title' => 'Bold Green Text',
								'inline' => 'span',
								'styles' => array(
									'color' => '#0BEA43',
									'fontWeight' => 'bold'
								)
							),
							array(
								'title' => 'Italic Green Text',
								'inline' => 'span',
								'styles' => array(
									'color' => '#0BEA43',
									'fontStyle' => 'italic'
								)
							),
							array(
								'title' => 'Bold Italic Green Text',
								'inline' => 'span',
								'styles' => array(
									'color' => '#0BEA43',
									'fontWeight' => 'bold',
									'fontStyle' => 'italic'
								)
							)
						)
					),
					
					array(
						'title' => 'Text Outlines',
						'items' => array( 
						
							array(
								'title' => 'Text Outline Black',
								'inline' => 'span',
								'styles' => array(
									'text-shadow' => '-1px 0 #000, 0 1px #000, 1px 0 #000, 0 -1px #000'
									)
							),
							array(
								'title' => 'Text Outline Red',
								'inline' => 'span',
								'styles' => array(
									'text-shadow' => '-1px 0 #f00, 0 1px #f00, 1px 0 #f00, 0 -1px #f00'
									)
							),
							array(
								'title' => 'Text Outline Blue',
								'inline' => 'span',
								'styles' => array(
									'text-shadow' => '-1px 0 #0040FF, 0 1px #0040FF, 1px 0 #0040FF, 0 -1px #0040FF'
									)
							),
							array(
								'title' => 'Text Outline Green',
								'inline' => 'span',
								'styles' => array(
									'text-shadow' => '-1px 0 #0BEA43, 0 1px #0BEA43, 1px 0 #0BEA43, 0 -1px #0BEA43'
									)
							),
							array(
								'title' => 'Text Outline Violet',
								'selector' => 'placeholder'
							),
							// Added 7-18-12
							array(
								'title' => 'Text Outline Teal',
								'selector' => 'placeholder'
							),
							array(
								'title' => 'Text Outline Gold',
								'selector' => 'placeholder'
							),
							array(
								'title' => 'Text Outline Purple',
								'selector' => 'placeholder'
							)
						)
					),
					
					
					
					
					array(
						'title' => 'Text Decoration',
						'items' => array( 
						
							array(
								'title' => '3D Text',
								'inline' => 'span',
								'styles' => array(
									'font-size' => '28px',
									'text-shadow' => '0px 0px 0 rgb(198,198,198),1px 1px 0 rgb(163,163,163),2px 2px 0 rgb(127,127,127),3px 3px 0 rgb(91,91,91), 4px 4px 0 rgb(55,55,55),5px 5px 4px rgba(0,0,0,0.35),5px 5px 1px rgba(0,0,0,0.5),0px 0px 4px rgba(0,0,0,.2)'
									)
							),
							array(
								'title' => 'Text Shadow',
								'inline' => 'span',
								'styles' => array(
									'text-shadow' => '2px 2px 2px #000'
									)
							),
							array(
								'title' => 'Blurry Text',
								'inline' => 'span',
								'styles' => array(
									'color' => 'transparent',
									'text-shadow' => '0 0 5px rgba(0,0,0,0.8)',
									'font-size' => '28px'
									)
							),
							array(
								'title' => 'Milky Text',
								'inline' => 'span',
								'styles' => array(
									'color' => '#fff',
									'background' => '#fff',
									'text-shadow' => '1px 1px 4px#000'
									)
							),
							array(
								'title' => 'Mystery Text',
								'inline' => 'span',
								'styles' => array(
									'color' => '#000',
									'background' => '#000',
									'text-shadow' => '1px 1px 4px #fff'
									)
							),
							array(
								'title' => 'Engrave Text',
								'inline' => 'span',
								'styles' => array(
									'text-shadow' => '1px 1px white, -1px -1px #444',
									'color' => '#fff'
									)
							),
							array(
								'title' => 'Small Caps',
								'inline' => 'span',
								'styles' => array(
									'font-variant' => 'small-caps'
									)
							)
						)
					),
					
					array(
						'title' => 'Text Glows',
						'items' => array( 
						
							array(
								'title' => 'Glow Green',
								'inline' => 'span',
								'styles' => array(
									'text-shadow' => '0 0 0.2em #B8F9BB, 0 0 0.2em #B8F9BB, 0 0 0.2em #B8F9BB'
									)
							),
							array(
								'title' => 'Glow Blue',
								'inline' => 'span',
								'styles' => array(
									'text-shadow' => '0 0 0.2em #BFF8FC, 0 0 0.2em #BFF8FC, 0 0 0.2em #BFF8FC'
									)
							),
							array(
								'title' => 'Glow Yellow',
								'inline' => 'span',
								'styles' => array(
									'text-shadow' => '0 0 0.2em #FCFCBD, 0 0 0.2em #FCFCBD, 0 0 0.2em #FCFCBD'
									)
							),
							array(
								'title' => 'Glow Violet',
								'selector' => 'placeholder'
							),
							array(
								'title' => 'Glow Red',
								'selector' => 'placeholder'
							),
							// Added 7-18-12
							array(
								'title' => 'Glow Teal',
								'selector' => 'placeholder'
							),
							array(
								'title' => 'Glow Gold',
								'selector' => 'placeholder'
							),
							array(
								'title' => 'Glow Purple',
								'selector' => 'placeholder'
							)
						)
					),
					
					
							
					array(
						'title' => 'Highlights',
						'items' => array( 
						
							array(
								'title' => 'Highlight Green',
								'inline' => 'span',
								'styles' => array(
									'background-color' => '#B8F9BB',
									'border' => '1px solid #419B44',
									'padding' => '2px 5px 2px 5px',
									'border-radius' => '3px'
								)
							),
							array(
								'title' => 'Highlight Blue',
								'inline' => 'span',
								'styles' => array(
									'background-color' => '#BFF8FC',
									'border' => '1px solid #506EF4',
									'padding' => '2px 5px 2px 5px',
									'border-radius' => '3px'
								)
							),
							array(
								'title' => 'Highlight Yellow',
								'inline' => 'span',
								'styles' => array(
									'background-color' => '#FFFF35',
									'border' => '1px solid #E5D02D',
									'padding' => '2px 5px 2px 5px',
									'border-radius' => '3px'
								)
							),
							array(
								'title' => 'Highlight Violet',
								'selector' => 'placeholder'
							),
							array(
								'title' => 'Highlight Red',
								'selector' => 'placeholder'
							),
							// Added 7-18-12
							array(
								'title' => 'Highlight Teal',
								'selector' => 'placeholder'
							),
							array(
								'title' => 'Highlight Gold',
								'selector' => 'placeholder'
							),
							array(
								'title' => 'Highlight Purple',
								'selector' => 'placeholder'
							)
						)
					),
					
					array(
						'title' => 'Inset Box Styles',
						'items' => array( 
						
							array(
								'title' => 'Inset Box Green',
								'inline' => 'span',
								'styles' => array(
									'background-color' => '#B8F9BB',
									'border' => '1px solid #419B44',
									'padding' => '2px 5px 2px 5px',
									'border-radius' => '3px',
									'box-shadow' => '0 1px 10px #666 inset'
								)
							),
							array(
								'title' => 'Inset Box Blue',
								'inline' => 'span',
								'styles' => array(
									'background-color' => '#BFF8FC',
									'border' => '1px solid #506EF4',
									'padding' => '2px 5px 2px 5px',
									'border-radius' => '3px',
									'box-shadow' => '0 1px 10px #666 inset'
								)
							),
							array(
								'title' => 'Inset Box Yellow',
								'inline' => 'span',
								'styles' => array(
									'background-color' => '#FFFF35',
									'border' => '1px solid #E5D02D',
									'padding' => '2px 5px 2px 5px',
									'border-radius' => '3px',
									'box-shadow' => '0 1px 10px #666 inset'
								)
							),
							array(
								'title' => 'Inset Box Violet',
								'selector' => 'placeholder'
							),
							array(
								'title' => 'Inset Box Red',
								'selector' => 'placeholder'
							),
							array(
								'title' => 'Inset Box Steel',
								'selector' => 'placeholder'
							),
							// Added 7-18-12
							array(
								'title' => 'Inset Box Teal',
								'selector' => 'placeholder'
							),
							array(
								'title' => 'Inset Box Gold',
								'selector' => 'placeholder'
							),
							array(
								'title' => 'Inset Box Purple',
								'selector' => 'placeholder'
							)
						)
					),
					
					array(
						'title' => 'Box Shadows',
						'items' => array( 
						
							array(
								'title' => 'Box Shadow Green',
								'inline' => 'span',
								'styles' => array(
									'background-color' => '#B8F9BB',
									'border' => '1px solid #419B44',
									'padding' => '2px 5px 2px 5px',
									'border-radius' => '3px',
									'box-shadow' => '5px 5px 2px #888888'
								)
							),
							array(
								'title' => 'Box Shadow Blue',
								'inline' => 'span',
								'styles' => array(
									'background-color' => '#BFF8FC',
									'border' => '1px solid #506EF4',
									'padding' => '2px 5px 2px 5px',
									'border-radius' => '3px',
									'box-shadow' => '5px 5px 2px #888888'
								)
							),
							array(
								'title' => 'Box Shadow Yellow',
								'inline' => 'span',
								'styles' => array(
									'background-color' => '#FFFF35',
									'border' => '1px solid #E5D02D',
									'padding' => '2px 5px 2px 5px',
									'border-radius' => '3px',
									'box-shadow' => '5px 5px 2px #888888'
								)
							),
							array(
								'title' => 'Box Shadow Violet',
								'selector' => 'placeholder'
							),
							array(
								'title' => 'Box Shadow Red',
								'selector' => 'placeholder'
							),
							// Added 7-18-12
							array(
								'title' => 'Box Shadow Teal',
								'selector' => 'placeholder'
							),
							array(
								'title' => 'Box Shadow Gold',
								'selector' => 'placeholder'
							),
							array(
								'title' => 'Box Shadow Purple',
								'selector' => 'placeholder'
							)
						)
					),
					
					array(
						'title' => 'Hover Boxes',
						'items' => array( 
						
							array(
								'title' => 'Hover Box Green',
								'inline' => 'span',
								'styles' => array(
									'background-color' => '#B8F9BB',
									'border' => '1px solid #419B44',
									'padding' => '2px 5px 2px 5px',
									'border-radius' => '3px',
									'box-shadow' => '10px 10px 5px #888888'
								)
							),
							array(
								'title' => 'Hover Box Blue',
								'inline' => 'span',
								'styles' => array(
									'background-color' => '#BFF8FC',
									'border' => '1px solid #506EF4',
									'padding' => '2px 5px 2px 5px',
									'border-radius' => '3px',
									'box-shadow' => '10px 10px 5px #888888'
								)
							),
							array(
								'title' => 'Hover Box Yellow',
								'inline' => 'span',
								'styles' => array(
									'background-color' => '#FFFF35',
									'border' => '1px solid #E5D02D',
									'padding' => '2px 5px 2px 5px',
									'border-radius' => '3px',
									'box-shadow' => '10px 10px 5px #888888'
								)
							),
							array(
								'title' => 'Hover Box Violet',
								'selector' => 'placeholder'
							),
							array(
								'title' => 'Hover Box Red',
								'selector' => 'placeholder'
							),
							// Added 7-18-12
							array(
								'title' => 'Hover Box Teal',
								'selector' => 'placeholder'
							),
							array(
								'title' => 'Hover Box Gold',
								'selector' => 'placeholder'
							),
							array(
								'title' => 'Hover Box Purple',
								'selector' => 'placeholder'
							)
						)
					),
					
					array(
						'title' => 'Download Links',
						'items' => array( 
						
							array(
								'title' => 'Download Link Green',
								'selector' => 'a',
								'styles' => array(
									'color' => '#3B9634',
									'fontWeight' => 'bold',
									'background-color' => '#ACF9A7',
									'border-radius' => '5px',
									'border' => '1px solid #71C66B',
									'padding' => '5px',
									'text-shadow' => '1px 1px 6px #fff'
								)
							),
							array(
								'title' => 'Download Link Blue',
								'selector' => 'a',
								'styles' => array(
									'color' => '#46ADC4',
									'fontWeight' => 'bold',
									'background-color' => '#9FF4EA',
									'border-radius' => '5px',
									'border' => '1px solid #46ADC4',
									'padding' => '5px'
									)
							),
							array(
								'title' => 'Download Link Yellow',
								'selector' => 'a',
								'styles' => array(
									'color' => '#A5A51A',
									'fontWeight' => 'bold',
									'background-color' => '#FCFCAB',
									'border-radius' => '5px',
									'border' => '1px solid #F2F200',
									'padding' => '5px'
									)
							),
							array(
								'title' => 'Download Link Violet',
								'selector' => 'placeholder'
							),
							array(
								'title' => 'Download Link Red',
								'selector' => 'placeholder'
							),
							// Added 7-18-12
							array(
								'title' => 'Download Link Teal',
								'selector' => 'placeholder'
							),
							array(
								'title' => 'Download Link Gold',
								'selector' => 'placeholder'
							),
							array(
								'title' => 'Download Link Purple',
								'selector' => 'placeholder'
							)
						)
					),
					
					array(
						'title' => '3D Buttons',
						'items' => array( 
						
							array(
								'title' => '3D Button Green',
								'inline' => 'span',
								'styles' => array(
									'border' => '1px solid #006400',
									'background-color' => '#008000',
									'border-radius' => '4px',
									'box-shadow' => 'inset 1px 6px 12px #7CFC00, inset -1px -10px 5px #006400, 1px 2px 1px black',
									'-o-box-shadow' => 'inset 1px 6px 12px #7CFC00, inset -1px -10px 5px #006400, 1px 2px 1px black',
									'-webkit-box-shadow' => 'inset 1px 6px 12px #7CFC00, inset -1px -10px 5px #006400, 1px 2px 1px black',
									'-moz-box-shadow' => 'inset 1px 6px 12px #7CFC00, inset -1px -10px 5px #006400, 1px 2px 1px black',
									'color' => 'white',
									'text-shadow' => '1px 1px 1px black',
									'padding' => '5px 30px',
									)
							),
							array(
								'title' => '3D Button Blue',
								'inline' => 'span',
								'styles' => array(
									'border' => '1px solid #001563',
									'background-color' => '#0638AD',
									'border-radius' => '4px',
									'box-shadow' => 'inset 1px 6px 12px #5A98FC, inset -1px -10px 5px #001563, 1px 2px 1px black',
									'-o-box-shadow' => 'inset 1px 6px 12px #5A98FC, inset -1px -10px 5px #001563, 1px 2px 1px black',
									'-webkit-box-shadow' => 'inset 1px 6px 12px #5A98FC, inset -1px -10px 5px #001563, 1px 2px 1px black',
									'-moz-box-shadow' => 'inset 1px 6px 12px #5A98FC, inset -1px -10px 5px #001563, 1px 2px 1px black',
									'color' => 'white',
									'text-shadow' => '1px 1px 1px black',
									'padding' => '5px 30px',
									)
							),
							array(
								'title' => '3D Button Yellow',
								'inline' => 'span',
								'styles' => array(
									'border' => '1px solid #7F7D10',
									'background-color' => '#AAAA06',
									'border-radius' => '4px',
									'box-shadow' => 'inset 1px 6px 12px #F8FC7E, inset -1px -10px 5px #7F7D10, 1px 2px 1px black',
									'-o-box-shadow' => 'inset 1px 6px 12px #F8FC7E, inset -1px -10px 5px #7F7D10, 1px 2px 1px black',
									'-webkit-box-shadow' => 'inset 1px 6px 12px #F8FC7E, inset -1px -10px 5px #7F7D10, 1px 2px 1px black',
									'-moz-box-shadow' => 'inset 1px 6px 12px #F8FC7E, inset -1px -10px 5px #7F7D10, 1px 2px 1px black',
									'color' => 'white',
									'text-shadow' => '1px 1px 1px black',
									'padding' => '5px 30px',
									)
							),
							array(
								'title' => '3D Button Violet',
								'selector' => 'placeholder'
							),
							array(
								'title' => '3D Button Red',
								'selector' => 'placeholder'
							)
						)
					),
					
					array(
						'title' => 'Callout Boxes',
						'items' => array( 
						
							array(
								'title' => 'Callout Box Green',
								'block' => 'div',
								'styles' => array(
									'background-color' => '#B5F4CC',
									'border' => '1px solid #00C648',
									'border-radius' => '10px',
									'padding' => '10px'
									),
								'wrapper' => true
							),
							array(
								'title' => 'Callout Box Blue',
								'block' => 'div',
								'styles' => array(
									'background-color' => '#BDF9F9',
									'border' => '1px solid #4ABAF7',
									'border-radius' => '10px',
									'padding' => '10px',
									'width' => 'auto'
									),
								'wrapper' => true
							),
							array(
								'title' => 'Callout Box Yellow',
								'block' => 'div',
								'styles' => array(
									'background-color' => '#FCFCAB',
									'border' => '1px solid #F2F200',
									'border-radius' => '10px',
									'padding' => '10px'
									),
								'wrapper' => true
							),
							array(
								'title' => 'Callout Box Violet',
								'selector' => 'placeholder'
							),
							array(
								'title' => 'Callout Box Red',
								'selector' => 'placeholder'
							),
							// Added 7-18-12
							array(
								'title' => 'Callout Box Teal',
								'selector' => 'placeholder'
							),
							array(
								'title' => 'Callout Box Gold',
								'selector' => 'placeholder'
							),
							array(
								'title' => 'Callout Box Purple',
								'selector' => 'placeholder'
							)
						)
					),
					
					array(
						'title' => 'Floating Divs',
						'items' => array( 
						
							array(
								'title' => 'Floating Div Green',
								'block' => 'div',
								'styles' => array(
									'display' => 'inline-block',
									'width' => 'auto',
									'height' => 'auto',
									'margin' => '10px',
									'padding' => '0px 10px',
									'background' => '#6FE27A',
									'box-shadow' => '0 1px 5px #00A805, inset 0 10px 20px #B7FFC1',
									'-o-box-shadow' => '0 1px 5px #00A805, inset 0 10px 20px #B7FFC1',
									'-webkit-box-shadow' => '0 1px 5px #00A805, inset 0 10px 20px #B7FFC1',
									'-moz-box-shadow' => '0 1px 5px #00A805, inset 0 10px 20px #B7FFC1'
									),
								'wrapper' => true
							),
							array(
								'title' => 'Floating Div Blue',
								'block' => 'div',
								'styles' => array(
									'display' => 'inline-block',
									'width' => 'auto',
									'height' => 'auto',
									'margin' => '10px',
									'padding' => '0px 10px',
									'background' => '#6fb2e5',
									'box-shadow' => '0 1px 5px #0061aa, inset 0 10px 20px #b6f9ff',
									'-o-box-shadow' => '0 1px 5px #0061aa, inset 0 10px 20px #b6f9ff',
									'-webkit-box-shadow' => '0 1px 5px #0061aa, inset 0 10px 20px #b6f9ff',
									'-moz-box-shadow' => '0 1px 5px #0061aa, inset 0 10px 20px #b6f9ff'
									),
								'wrapper' => true
							),
							array(
								'title' => 'Floating Div Yellow',
								'block' => 'div',
								'styles' => array(
									'display' => 'inline-block',
									'width' => 'auto',
									'height' => 'auto',
									'margin' => '10px',
									'padding' => '0px 10px',
									'background' => '#E2E26F',
									'box-shadow' => '0 1px 5px #E8E409, inset 0 10px 20px #FFFFB7',
									'-o-box-shadow' => '0 1px 5px #E8E409, inset 0 10px 20px #FFFFB7',
									'-webkit-box-shadow' => '0 1px 5px #E8E409, inset 0 10px 20px #FFFFB7',
									'-moz-box-shadow' => '0 1px 5px #E8E409, inset 0 10px 20px #FFFFB7'
									),
								'wrapper' => true
							),
							array(
								'title' => 'Floating Div Violet',
								'selector' => 'placeholder'
							),
							array(
								'title' => 'Floating Div Red',
								'selector' => 'placeholder'
							),
							array(
								'title' => 'Floating Div Glass',
								'selector' => 'placeholder'
							)
						)
					),
					
					array(
						'title' => 'Borders',
						'items' => array(
						
							array(
								'title' => 'Single Border',
								'inline' => 'span',
								'styles' => array(
									'border' => '1px solid #000',
									'padding' => '2px 10px',
									'border-radius' => '2px'
									)
							),
							array(
								'title' => 'Double Border',
								'inline' => 'span',
								'styles' => array(
									'border' => 'medium double #000',
									'padding' => '2px 10px',
									'border-radius' => '2px'
									)
							),
							array(
								'title' => 'Top & Bottom Border',
								'inline' => 'span',
								'styles' => array(
									'border' => 'medium',
									'border-top-style' => 'double',
									'border-bottom-style' => 'double',
									'border-top-color' => '#000',
									'border-bottom-color' => '#000'
									)
							),
							array(
								'title' => 'Sngl Rainbow Border',
								'inline' => 'span',
								'styles' => array(
									'border' => '1px solid',
									'padding' => '2px 10px 2px 10px',
									'border-color' => 'red blue green orange'
									)
							),
							array(
								'title' => 'Dbl Rainbow Border',
								'selector' => 'placeholder'
							)
						)
					),
					
					array(
						'title' => 'Opacity',
						'items' => array(
						
							array(
								'title' => '75% Opacity',
								'inline' => 'span',
								'styles' => array(
									'opacity' => '0.75'
									)
							),
							array(
								'title' => '50% Opacity',
								'inline' => 'span',
								'styles' => array(
									'opacity' => '0.50'
									)
							),
							array(
								'title' => '25% Opacity',
								'inline' => 'span',
								'styles' => array(
									'opacity' => '0.25'
									)
							)
						)
					),
					
					array(
						'title' => 'Alignment',
						'items' => array(
						
							array(
								'title' => 'Align Left',
								'inline' => 'span',
								'styles' => array(
									'float' => 'left'
								)
							),
							array(
								'title' => 'Align Right',
								'inline' => 'span',
								'styles' => array(
									'float' => 'right'
								)
							)
						)
					)
				)
			)
		);
		//*************************************
		// END Predefined Styles
		//************************************/
		
		
		//**********************************************
		// FINALLY... let's put it all back together
		//*********************************************/
		if(isset($settings['style_formats'])) {
			
			$new_array = array();
			$json_decode_orig_settings = json_decode($settings['style_formats'], true);
			
			// Check to make sure incoming 'style_formats' is an array
			if(is_array($json_decode_orig_settings)) {
				
				$new_array = json_encode(array_merge($json_decode_orig_settings, $style_formats_predefined));
				$settings['style_formats'] = $new_array;
			}
		} else {
			
			$settings['style_formats'] = json_encode($style_formats_predefined);
		}
		
		// Merge new styles to original styles
		isset($settings['style_formats_merge']) ? $settings['style_formats_merge'] = true : $settings['style_formats_merge'] = true;
	}
	
	return $settings;
}


?>