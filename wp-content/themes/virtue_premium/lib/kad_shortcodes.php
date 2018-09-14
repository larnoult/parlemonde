<?php 
// Virtue Shortcode Generator 

// Enqueue scripts

function virtue_shortcode_button_scripts(){
	wp_enqueue_media();
	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_style('kad-shortcode-css', get_template_directory_uri() . '/lib/kad_shortcodes/css/kad-short-pop.css'); 
	wp_enqueue_script('kad_shortcode',get_template_directory_uri() . '/lib/kad_shortcodes/js/kad_shortcode_pop.js',array( 'jquery', 'wp-color-picker' ),'1.3.0 ', TRUE);
}

add_action('admin_enqueue_scripts','virtue_shortcode_button_scripts');

add_action('admin_footer','virtue_shortcode_content');

function virtue_shortcode_option( $name, $attr_option, $shortcode ){
	
	$kad_option_element = null;
	
	(isset($attr_option['desc']) && !empty($attr_option['desc'])) ? $desc = '<p class="description">'.$attr_option['desc'].'</p>' : $desc = '';
	
		
	switch( $attr_option['type'] ){
		
		case 'radio':
	    
		$kad_option_element .= '<div class="label"><strong>'.$attr_option['title'].': </strong></div><div class="content">';
	    foreach( $attr_option['values'] as $val => $title ){
	    
		(isset($attr_option['def']) && !empty($attr_option['def'])) ? $def = $attr_option['def'] : $def = '';
		
		 $kad_option_element .= '
			<label for="shortcode-option-'.$shortcode.'-'.$name.'-'.$val.'">'.$title.'</label>
		    <input class="attr" type="radio" data-attrname="'.$name.'" name="'.$shortcode.'-'.$name.'" value="'.$val.'" id="shortcode-option-'.$shortcode.'-'.$name.'-'.$val.'"'. ( $val == $def ? ' checked="checked"':'').'>';
	    }
		
		$kad_option_element .= $desc . '</div>';
		
	    break;
	    case 'checkbox':
		
		$kad_option_element .= '<div class="label"><label for="' . $name . '"><strong>' . $attr_option['title'] . ': </strong></label></div>    <div class="content"> <input type="checkbox" class="' . $name . '" data-attrname="'.$name.'" id="' . $name . '" />'. $desc. '</div> ';
		
		break;
		case 'select':

		$kad_option_element .= '
		<div class="label"><label for="'.$name.'"><strong>'.$attr_option['title'].': </strong></label></div>
		
		<div class="content"><select id="'.$name.'" class="kad-sc-select">';
			$values = $attr_option['values'];
			foreach( $values as $value => $vname ){
				if($value == $attr_option['default']) { $selected=' selected="selected"';} else { $selected=""; }
		    	$kad_option_element .= '<option value="'.$value.'" ' . $selected .'>'.$vname.'</option>';
			}
		$kad_option_element .= '</select>' . $desc . '</div>';

		break;
		case 'icon-select':

		$kad_option_element .= '
		<div class="label"><label for="'.$name.'"><strong>'.$attr_option['title'].': </strong></label></div>
		
		<div class="content"><select id="'.$name.'" class="kad-icon-select">';
			$values = $attr_option['values'];
			foreach( $values as $value ){
		    	$kad_option_element .= '<option value="'.$value.'">'.$value.'</option>';
			}
		$kad_option_element .= '</select>' . $desc . '</div>';

		break;
		case 'color':
			
	           $kad_option_element .= '
	           <div class="label"><label><strong>'.$attr_option['title'].' </strong></label></div>
			   <div class="content"><input type="text" value="'. ( isset($attr_option['default']) ? $attr_option['default'] : "" ) . '" class="kad-popup-colorpicker" data-attrname="'.$name.'" style="width: 70px;" data-default-color="'. ( isset($attr_option['default']) ? $attr_option['default'] : "" ) . '"/>';
			   $kad_option_element .= $desc . '</div>';
		break;
		case 'textarea':
		$kad_option_element .= '
		<div class="label"><label for="shortcode-option-'.$name.'"><strong>'.$attr_option['title'].': </strong></label></div>
		<div class="content"><textarea class="kad-sc-'.$name.'" data-attrname="'.$name.'"></textarea> ' . $desc . '</div>';
		break;
		case 'text':
		default:
		    $kad_option_element .= '
			<div class="label"><label for="shortcode-option-'.$name.'"><strong>'.$attr_option['title'].': </strong></label></div>
			<div class="content"><input class="attr kad-sc-textinput kad-sc-'.$name.'" type="text" data-attrname="'.$name.'" value="" />' . $desc . '</div>';
		break;
	}
	
	$kad_option_element .= '<div class="clear"></div>';
    
	
    return $kad_option_element;
}

function virtue_shortcode_content(){

	//Columns
$virtue_shortcodes['columns'] = array( 
	'title'=>__('Columns', 'virtue'), 
	'attr'=>array(
		'columns'=>array(
			'type'=>'radio', 
			'title'=>__('Columns','virtue'),
			'values' => array(
				"span6" => '<img src="'. get_template_directory_uri().'/assets/img/twocolumn.jpg" />' . __("Two Columns", "virtue"),
				"span4right" => '<img src="'. get_template_directory_uri().'/assets/img/twocolumnleft.jpg" />' . __("Two Columns offset Right", "virtue"),
				"span4left" => '<img src="'. get_template_directory_uri().'/assets/img/twocolumnright.jpg" />' . __("Two Columns offset Left", "virtue"),
				"span4" => '<img src="'. get_template_directory_uri().'/assets/img/threecolumn.jpg" />' . __("Three Columns", "virtue"),
				"span3" => '<img src="'. get_template_directory_uri().'/assets/img/fourcolumn.jpg" />' . __("Four Columns", "virtue"),
				)
		),
	) 
);

//table
$virtue_shortcodes['table'] = array( 
	'title'=>__('Table', 'virtue'), 
	'attr'=>array(
		'head'=>array(
			'type'=>'checkbox', 
			'title'=>__('Use a table head?','virtue')
		),
		'columns'=>array(
			'type'=>'text', 
			'title'=>__('Columns (just a number)', 'virtue'),
			'default' => '2',
		),
		'rows'=>array(
			'type'=>'text', 
			'title'=>__('Extra Rows (just a number)', 'virtue'),
			'default' => '2',
		),
	) 
);
	// Divider 
$virtue_shortcodes['hr'] = array( 
	'title'=>__('Divider', 'virtue'), 
	'attr'=>array(
		'style'=>array(
			'type'=>'select', 
			'title'=>__('Style', 'virtue'),
			'default' => 'line',
			'values' => array(
				"line" => __("Line", "virtue"),
				"dots" => __("Dots", "virtue"),
				"gradient" => __("Gradient", "virtue"),
				)
		),
		'size'=>array(
			'type'=>'select', 
			'title'=>__('Size','virtue'),
			'default' => '1px',
			'values' => array(
				"1px" => "1px",
				"2px" => "2px",
				"3px" => "3px",
				"4px" => "4px",
				"5px" => "5px",
				)
		),
		'color'=>array(
			'type'=>'color', 
			'title'  => __('Color','virtue'),
		)
	) 
);
// Spacer
$virtue_shortcodes['space'] = array( 
	'title'=>__('Spacing', 'virtue'), 
	'attr'=>array(
		'size'=>array(
			'type'=>'select', 
			'title'=>__('Size','virtue'),
			'default' => '10px',
			'values' => array(
				"10px" => "10px",
				"20px" => "20px",
				"30px" => "30px",
				"40px" => "40px",
				"50px" => "50px",
				)
		)
	) 
);
// Spacer
$virtue_shortcodes['tabs'] = array( 
	'title'=>__('Tabs', 'virtue'), 
);
$virtue_shortcodes['accordion'] = array( 
	'title'=>__('Accordion', 'virtue'),
);
$virtue_shortcodes['pullquote'] = array( 
	'title'=>__('Pull-Quotes', 'virtue'), 
	'attr'=>array(
		'align'=>array(
			'type'=>'select', 
			'title'=>__('Align', 'virtue'),
			'default' => 'center',
			'values' => array(
				"center" => __('Center','virtue'),
				"left" => __('Left','virtue'),
				"right" => __('Right','virtue'),
				)
		),
		'content'=>array(
			'type'=>'textarea', 
			'title'=>__('Pull-Quote Text', 'virtue')
		)
	) 
);
$virtue_shortcodes['blockquote'] = array( 
	'title'=>__('Block-Quotes', 'virtue'), 
	'attr'=>array(
		'align'=>array(
			'type'=>'select', 
			'title'=>__('Align', 'virtue'),
			'default' => 'center',
			'values' => array(
				"center" => __('Center','virtue'),
				"left" => __('Left','virtue'),
				"right" => __('Right','virtue'),
				)
		),
		'content'=>array(
			'type'=>'textarea', 
			'title'=>__('Block-Quote Text', 'virtue')
		)
	) 
);
$virtue_shortcodes['kt_box'] = array( 
	'title'=>__('Simple Box', 'virtue'), 
	'attr'=>array(
		'padding_top'=>array(
			'type'=>'text', 
			'title'=>__('Padding Top (just a number)', 'virtue'),
			'default' => '15',
		),
		'padding_bottom'=>array(
			'type'=>'text', 
			'title'=>__('Padding Bottom (just a number)', 'virtue'),
			'default' => '15',
		),
		'padding_left'=>array(
			'type'=>'text', 
			'title'=>__('Padding Left (just a number)', 'virtue'),
			'default' => '15',
		),
		'padding_right'=>array(
			'type'=>'text', 
			'title'=>__('Padding Right (just a number)', 'virtue'),
			'default' => '15',
		),
		'min_height'=>array(
			'type'=>'text', 
			'title'=>__('Min Height (just a number)', 'virtue'),
			'default' => '0',
		),
		'valign'=>array(
			'type'=>'checkbox', 
			'title'=>__('Vertical align middle?','virtue')
		),
		'background'=>array(
			'type'=>'color', 
			'title'  => __('Background Color','virtue'),
			'default' => '',
		),
		'opacity'=>array(
			'type'=>'select', 
			'title'=>__('Background Color Opacity', 'virtue'),
			'default' => '1',
			'values' => array(
				"1" => __('1.0','virtue'),
				"0.9" => __('0.9','virtue'),
				"0.8" => __('0.8','virtue'),
				"0.7" => __('0.7','virtue'),
				"0.6" => __('0.6','virtue'),
				"0.5" => __('0.5','virtue'),
				"0.4" => __('0.4','virtue'),
				"0.3" => __('0.3','virtue'),
				"0.2" => __('0.2','virtue'),
				"0.1" => __('0.1','virtue'),
				"0.0" => __('0.0','virtue'),
				)
		),
		'content'=>array(
			'type'=>'textarea', 
			'title'=>__('Content Text', 'virtue')
		)
	) 
);
$icons = kad_icon_list();

	//Button
$virtue_shortcodes['btn'] = array( 
	'title'=>__('Button', 'virtue'), 
	'attr'=>array(
		'text'=>array(
			'type'=>'text', 
			'title'=>__('Button Text', 'virtue')
		),
		'target'=>array(
			'type'=>'checkbox', 
			'title'=>__('Open Link In New Tab?','virtue')
		),
		'tcolor'=>array(
			'type'=>'color', 
			'title'  => __('Font Color','virtue'),
			'default' => '#ffffff',
		),
		'bcolor'=>array(
			'type'=>'color', 
			'title'  => __('Button Background Color','virtue'),
			'default' => '',
		),
		'border'=>array(
			'type'=>'text',
			'desc'=>__('Example = 2px', 'virtue'), 
			'title'=>__('Button Border Size', 'virtue')
		),
		'bordercolor'=>array(
			'type'=>'color', 
			'title'  => __('Button Border Color','virtue'),
			'default' => '',
		),
		'borderradius'=>array(
			'type'=>'text',
			'desc'=>__('Example = 6px', 'virtue'), 
			'title'=>__('Button Border Radius', 'virtue')
		),
		'thovercolor'=>array(
			'type'=>'color', 
			'title'  => __('Font Hover Color','virtue'),
			'default' => '#ffffff',
		),
		'bhovercolor'=>array(
			'type'=>'color', 
			'title'  => __('Button Background Hover Color','virtue'),
			'default' => '',
		),
		'borderhovercolor'=>array(
			'type'=>'color', 
			'title'  => __('Button Border Hover Color','virtue'),
			'default' => '',
		),
		'link'=>array(
			'type'=>'text', 
			'title'=>__('Link URL', 'virtue')
		),
		'size'=>array(
			'type'=>'select', 
			'title'=>__('Button Size', 'virtue'),
			'default' => '',
			'values' => array(
				"" => __('Default', 'virtue'),
				"large" => __('Large', 'virtue'),
				"small" => __('Small', 'virtue'),
				)
		),
		'font'=>array(
			'type'=>'select', 
			'title'=>__('Font Family', 'virtue'),
			'default' => '',
			'values' => array(
				"" => __('Default', 'virtue'),
				"h1-family" => __('H1 Family', 'virtue'),
				)
		),
		'icon'=>array(
			'type'=>'icon-select', 
			'title'=>__('Choose an Icon (optional)', 'virtue'),
			'values' => $icons
		),
	) 
);
$virtue_shortcodes['gmap'] = array( 
	'title'=>__('Google Map', 'virtue'), 
	'attr'=>array(
		'address'=>array(
			'type'=>'text', 
			'title'=>__('Address One', 'virtue')
		),
		'title'=>array(
			'type'=>'text', 
			'title'=>__('Address Title One','virtue'),
			'desc'=>__('Displays in Popup e.g. = Business Name', 'virtue')
		),
		'height'=>array(
			'type'=>'text', 
			'title'=>__('Map Height', 'virtue'),
			'desc'=>__('Just a number e.g. = 400', 'virtue'), 
		),
		'zoom'=>array(
			'type'=>'select', 
			'title'=>__('Map Zoom','virtue'),
			'default' => '15',
			'values' => array(
				"1" => "1",
				"2" => "2",
				"3" => "3",
				"4" => "4",
				"5" => "5",
				"6" => "6",
				"7" => "7",
				"8" => "8",
				"9" => "9",
				"10" => "10",
				"11" => "11",
				"12" => "12",
				"13" => "13",
				"14" => "14",
				"15" => "15",
				"16" => "16",
				"17" => "17",
				"18" => "18",
				"19" => "19",
				"20" => "20",
				)
		),
		'maptype'=>array(
			'type'=>'select', 
			'title'=>__('Map Type','virtue'),
			'default' => 'ROADMAP',
			'values' => array(
				"ROADMAP" => __('ROADMAP', 'virtue'),
				"HYBRID" => __('HYBRID', 'virtue'),
				"TERRAIN" => __('TERRAIN', 'virtue'),
				"SATELLITE" => __('SATELLITE', 'virtue'),
				)
		),
		'address2'=>array(
			'type'=>'text', 
			'title'=>__('Address Two', 'virtue')
		),
		'title2'=>array(
			'type'=>'text', 
			'title'=>__('Address Title Two','virtue'),
			'desc'=>__('Displays in Popup e.g. = Business Name', 'virtue')
		),
		'address3'=>array(
			'type'=>'text', 
			'title'=>__('Address Three', 'virtue')
		),
		'title3'=>array(
			'type'=>'text', 
			'title'=>__('Address Title Three','virtue'),
			'desc'=>__('Displays in Popup e.g. = Business Name', 'virtue')
		),
		'address4'=>array(
			'type'=>'text', 
			'title'=>__('Address Four', 'virtue')
		),
		'title4'=>array(
			'type'=>'text', 
			'title'=>__('Address Title Four','virtue'),
			'desc'=>__('Displays in Popup e.g. = Business Name', 'virtue')
		),
		'center'=>array(
			'type'=>'text', 
			'title'=>__('Map Center','virtue'),
			'desc'=>__('Defaults to Address One', 'virtue')
		)
	) 
);

$virtue_shortcodes['icon'] = array( 
	'title'=>__('Icon', 'virtue'), 
	'attr'=>array(
		'icon'=>array(
			'type'=>'icon-select', 
			'title'=>__('Choose an Icon', 'virtue'),
			'values' => $icons
		),
		'size'=>array(
			'type'=>'select', 
			'title'=>__('Icon Size','virtue'),
			'default' => '14px',
			'values' => array(
				"5px" => "5px",
				"6px" => "6px",
				"7px" => "7px",
				"8px" => "8px",
				"9px" => "9px",
				"10px" => "10px",
				"11px" => "11px",
				"12px" => "12px",
				"13px" => "13px",
				"14px" => "14px",
				"15px" => "15px",
				"16px" => "16px",
				"17px" => "17px",
				"18px" => "18px",
				"19px" => "19px",
				"20px" => "20px",
				"21px" => "21px",
				"22px" => "22px",
				"23px" => "23px",
				"24px" => "24px",
				"25px" => "25px",
				"26px" => "26px",
				"27px" => "27px",
				"28px" => "28px",
				"29px" => "29px",
				"30px" => "30px",
				"31px" => "31px",
				"32px" => "32px",
				"33px" => "33px",
				"34px" => "34px",
				"35px" => "35px",
				"36px" => "36px",
				"37px" => "37px",
				"38px" => "38px",
				"39px" => "39px",
				"40px" => "40px",
				"41px" => "41px",
				"42px" => "42px",
				"43px" => "43px",
				"44px" => "44px",
				"45px" => "45px",
				"46px" => "46px",
				"47px" => "47px",
				"48px" => "48px",
				"49px" => "49px",
				"50px" => "50px",
				"51px" => "51px",
				"52px" => "52px",
				"53px" => "53px",
				"54px" => "54px",
				"55px" => "55px",
				"56px" => "56px",
				"57px" => "57px",
				"58px" => "58px",
				"59px" => "59px",
				"60px" => "60px",
				"61px" => "61px",
				"62px" => "62px",
				"63px" => "63px",
				"64px" => "64px",
				"65px" => "65px",
				"66px" => "66px",
				"67px" => "67px",
				"68px" => "68px",
				"69px" => "69px",
				"70px" => "70px",
				"71px" => "71px",
				"72px" => "72px",
				"73px" => "73px",
				"74px" => "74px",
				"75px" => "75px",
				"76px" => "76px",
				"77px" => "77px",
				"78px" => "78px",
				"79px" => "79px",
				"80px" => "80px",
			)
		),
		'color'=>array(
			'type'=>'color', 
			'title'  => __('Icon Color','virtue'),
			'default' => '',
		),
		'float'=>array(
			'type'=>'select', 
			'title'=>__('Icon Float', 'virtue'),
			'default' => '',
			'values' => array(
				"" => "none",
				"left" => "Left",
				"right" => "Right",
				)
		),
		'style'=>array(
			'type'=>'select', 
			'title'=>__('Icon Style', 'virtue'),
			'default' => '',
			'values' => array(
				"" => "none",
				"circle" => __('Circle', 'virtue'),
				"smcircle" => __('Small Circle', 'virtue'),
				"square" => __('Square', 'virtue'),
				"smsquare" => __('Small Square', 'virtue'),
				)
		),
		'background'=>array(
			'type'=>'color', 
			'title'  => __('Background Color','virtue'),
			'default' => '',
		)
	) 
);
$virtue_shortcodes['iconbox'] = array( 
	'title'=>__('Icon Box', 'virtue'), 
	'attr'=>array(
		'icon'=>array(
			'type'=>'icon-select', 
			'title'=>__('Choose an Icon', 'virtue'),
			'values' => $icons
		),
		'iconsize'=>array(
			'type'=>'select', 
			'title'=>__('Icon Size','virtue'),
			'default' => '48px',
			'values' => array(
				"5px" => "5px",
				"6px" => "6px",
				"7px" => "7px",
				"8px" => "8px",
				"9px" => "9px",
				"10px" => "10px",
				"11px" => "11px",
				"12px" => "12px",
				"13px" => "13px",
				"14px" => "14px",
				"15px" => "15px",
				"16px" => "16px",
				"17px" => "17px",
				"18px" => "18px",
				"19px" => "19px",
				"20px" => "20px",
				"21px" => "21px",
				"22px" => "22px",
				"23px" => "23px",
				"24px" => "24px",
				"25px" => "25px",
				"26px" => "26px",
				"27px" => "27px",
				"28px" => "28px",
				"29px" => "29px",
				"30px" => "30px",
				"31px" => "31px",
				"32px" => "32px",
				"33px" => "33px",
				"34px" => "34px",
				"35px" => "35px",
				"36px" => "36px",
				"37px" => "37px",
				"38px" => "38px",
				"39px" => "39px",
				"40px" => "40px",
				"41px" => "41px",
				"42px" => "42px",
				"43px" => "43px",
				"44px" => "44px",
				"45px" => "45px",
				"46px" => "46px",
				"47px" => "47px",
				"48px" => "48px",
				"49px" => "49px",
				"50px" => "50px",
				"51px" => "51px",
				"52px" => "52px",
				"53px" => "53px",
				"54px" => "54px",
				"55px" => "55px",
				"56px" => "56px",
				"57px" => "57px",
				"58px" => "58px",
				"59px" => "59px",
				"60px" => "60px",
				"61px" => "61px",
				"62px" => "62px",
				"63px" => "63px",
				"64px" => "64px",
				"65px" => "65px",
				"66px" => "66px",
				"67px" => "67px",
				"68px" => "68px",
				"69px" => "69px",
				"70px" => "70px",
				"71px" => "71px",
				"72px" => "72px",
				"73px" => "73px",
				"74px" => "74px",
				"75px" => "75px",
				"76px" => "76px",
				"77px" => "77px",
				"78px" => "78px",
				"79px" => "79px",
				"80px" => "80px",
			)
		),
		'color'=>array(
			'type'=>'color', 
			'title'  => __('Icon/Font Color','virtue'),
			'default' => '#ffffff',
		),
		'background'=>array(
			'type'=>'color', 
			'title'  => __('Background Color','virtue'),
			'default' => '#dddddd',
		),
		'hcolor'=>array(
			'type'=>'color', 
			'title'  => __('Hover Icon/Font Color','virtue'),
			'default' => '#ffffff',
		),
		'hbackground'=>array(
			'type'=>'color', 
			'title'  => __('Hover Background Color','virtue'),
			'default' => '',
		),
		'link'=>array(
			'type'=>'text', 
			'title'=>__('Link URL', 'virtue')
		),
		'title'=>array(
			'type'=>'text', 
			'title'=>__('Title', 'virtue')
		),
		'description'=>array(
			'type'=>'textarea', 
			'title'=>__('Description', 'virtue')
		)

	) 
);
$virtue_shortcodes['kt_typed'] = array( 
	'title'=>__('Animated Typed Text', 'virtue'), 
	'attr'=>array(
		'first_sentence'=>array(
			'type'=>'text', 
			'title'=>__('First Sentence', 'virtue')
		),
		'second_sentence'=>array(
			'type'=>'text', 
			'title'=>__('Second Sentence (optional)', 'virtue')
		),
		'third_sentence'=>array(
			'type'=>'text', 
			'title'=>__('Third Sentence (optional)', 'virtue')
		),
		'fourth_sentence'=>array(
			'type'=>'text', 
			'title'=>__('Fourth Sentence (optional)', 'virtue')
		),
		'startdelay'=>array(
			'type'=>'text', 
			'title'=>__('Start Delay (milliseconds eg: 500)', 'virtue')
		),
		'loop'=>array(
			'type'=>'checkbox', 
			'title'=>__('Loop','virtue')
		)
	) 
);

$virtue_shortcodes['kad_youtube'] = array( 
	'title'=>__('YouTube', 'virtue'), 
	'attr'=>array(
		'url'=>array(
			'type'=>'text', 
			'title'=>__('Video URL', 'virtue')
		),
		'width'=>array(
			'type'=>'text', 
			'title'=>__('Video Width', 'virtue'),
			'desc' =>__('Just a number e.g. = 600', 'virtue'), 
		),
		'height'=>array(
			'type'=>'text', 
			'title'=>__('Video Height', 'virtue'),
			'desc'=>__('Just a number e.g. = 400', 'virtue'), 
		),
		'maxwidth'=>array(
			'type'=>'text', 
			'title'=>__('Video Max Width', 'virtue'),
			'desc'=>__('Keeps the responsive video from getting too large', 'virtue'), 
		),
		'hidecontrols'=>array(
			'type'=>'checkbox', 
			'title'=>__('Hide Controls','virtue')
		),
		'autoplay'=>array(
			'type'=>'checkbox', 
			'title'=>__('Auto Play','virtue')
		),
		'rel'=>array(
			'type'=>'checkbox', 
			'title'=>__('Show Related','virtue')
		),
		'modestbranding'=>array(
			'type'=>'checkbox', 
			'title'=>__('Modest Branding?','virtue')
		)
	) 
);
$virtue_shortcodes['kad_vimeo'] = array( 
	'title'=>__('Vimeo', 'virtue'), 
	'attr'=>array(
		'url'=>array(
			'type'=>'text', 
			'title'=>__('Video URL', 'virtue')
		),
		'width'=>array(
			'type'=>'text', 
			'title'=>__('Video Width', 'virtue'),
			'desc' =>__('Just a number e.g. = 600', 'virtue'), 
		),
		'height'=>array(
			'type'=>'text', 
			'title'=>__('Video Height', 'virtue'),
			'desc'=>__('Just a number e.g. = 400', 'virtue'), 
		),
		'maxwidth'=>array(
			'type'=>'text', 
			'title'=>__('Video Max Width', 'virtue'),
			'desc'=>__('Keeps the responsive video from getting too large', 'virtue'), 
		),
		'autoplay'=>array(
			'type'=>'checkbox', 
			'title'=>__('Auto Play','virtue')
		)
	) 
);
$postcategories = get_categories();
$cat_options = array();
$cat_options = array("" => "All");
foreach ($postcategories as $cat) {
      $cat_options[$cat->slug] = $cat->name;
}

$virtue_shortcodes['blog_posts'] = array( 
	'title'=>__('Blog Posts', 'virtue'), 
	'attr'=>array(
		'orderby'=>array(
			'type'=>'select', 
			'title'=>__('Order By', 'virtue'),
			'default' => 'date',
			'values' => array(
				"date" => __('Date','virtue'),
				"rand" => __('Random','virtue'),
				"menu_order" => __('Menu Order','virtue'),
				)
		),
		'cat'=>array(
			'type'=>'select',
			'default' => '',
			'title'=>__('Category', 'virtue'),
			'values' => $cat_options,
		),
		'items'=>array(
			'type'=>'text', 
			'title'=>__('Number of Posts', 'virtue')
		),
	) 
);
	//Button
$virtue_shortcodes['kad_modal'] = array( 
	'title'=>__('Modal', 'virtue'), 
	'attr'=>array(
		'btntitle'=>array(
			'type'=>'text', 
			'title'=>__('Button Title', 'virtue')
		),
		'btncolor'=>array(
			'type'=>'color', 
			'title'  => __('Button Font Color','virtue'),
			'default' => '#ffffff',
		),
		'btnbackground'=>array(
			'type'=>'color', 
			'title'  => __('Button Background Color','virtue'),
			'default' => '',
		),
		'btnsize'=>array(
			'type'=>'select', 
			'title'=>__('Button Size', 'virtue'),
			'default' => '',
			'values' => array(
				"" => __('Default', 'virtue'),
				"large" => __('Large', 'virtue'),
				"small" => __('Small', 'virtue'),
				)
		),
		'btnfont'=>array(
			'type'=>'select', 
			'title'=>__('Font Family', 'virtue'),
			'default' => '',
			'values' => array(
				"" => __('Default', 'virtue'),
				"h1-family" => __('H1 Family', 'virtue'),
				)
		),
		'title'=>array(
			'type'=>'text', 
			'title'=>__('Modal Title', 'virtue')
		),
		'content'=>array(
			'type'=>'textarea', 
			'title'=>__('Modal Content', 'virtue')
		)
	) 
);
$virtue_shortcodes['kad_testimonial_form'] = array( 
	'title'=>__('Testimonial Form', 'virtue'), 
	'attr'=>array(
		'location'=>array(
			'type'=>'checkbox', 
			'title'=>__('Show Location Field?','virtue')
		),
		'position'=>array(
			'type'=>'checkbox', 
			'title'=>__('Show Position Field?','virtue')
		),
		'link'=>array(
			'type'=>'checkbox', 
			'title'=>__('Show Link Field?','virtue')
		),
		'name_label'=>array(
			'type'=>'text', 
			'title'=>__('Name Field Label', 'virtue'),
			'desc'=>__('Default: Name', 'virtue')
		),
		'testimonial_label'=>array(
			'type'=>'text', 
			'title'=>__('Testimonial Field Label','virtue'),
			'desc'=>__('Default: Testimonial', 'virtue')
		),
		'location_label'=>array(
			'type'=>'text', 
			'title'=>__('Location Field Label', 'virtue'),
			'desc'=>__('Default: Location - Optional', 'virtue')
		),
		'position_label'=>array(
			'type'=>'text', 
			'title'=>__('Position Field Label', 'virtue'),
			'desc'=>__('Default: Position or Company - optional', 'virtue')
		),
		'link_label'=>array(
			'type'=>'text', 
			'title'=>__('Link Field Label','virtue'),
			'desc'=>__('Default: Link - optional', 'virtue')
		),
		'submit_label'=>array(
			'type'=>'text', 
			'title'=>__('Submit Field Label', 'virtue'),
			'desc'=>__('Default: Submit', 'virtue')
		),
		'success_message'=>array(
			'type'=>'text', 
			'title'=>__('Success Message','virtue'),
			'desc'=>__('Default: Thank you for submitting your testimonial! It is now awaiting approval from the site admnistator. Thank you!', 'virtue')
		),
	) 
);

	ob_start(); ?>
	<div id="kadence-shortcode-container">
		<div id="kadence-shortcode-innercontainer" class="mfp-hide mfp-with-anim">
		 	<div class="kadenceshortcode-content">
		 		<div class="shortcodes-header">
					<div class="kadshort-header"><h3><?php echo __('Virtue Shortcodes', 'virtue'); ?></h3></div>
					<div class="kadshort-select"><select id="kadence-shortcodes" data-placeholder="<?php _e("Choose a shortcode", 'virtue'); ?>">
				    <option></option>
					
					<?php $kad_sc_html = ''; $kad_options_html = '';
					$virtue_shortcodes = apply_filters('kadence_shortcodes', $virtue_shortcodes);
					foreach( $virtue_shortcodes as $shortcode => $options ){
						
							$kad_sc_html .= '<option value="'.$shortcode.'">'.$options['title'].'</option>';
							$kad_options_html .= '<div class="shortcode-options" id="options-'.$shortcode.'" data-name="'.$shortcode.'">';
							
								if( !empty($options['attr']) ){
									 foreach( $options['attr'] as $name => $attr_option ){
										$kad_options_html .= virtue_shortcode_option( $name, $attr_option, $shortcode );
									 }
								}
			
							$kad_options_html .= '</div>'; 
						
					} 
			
			$kad_sc_html .= '</select></div></div>'; 	
		
	
		 echo $kad_sc_html . $kad_options_html; ?>

 				
			<div class="kad_shortcode_insert">	
				<a href="javascript:void(0);" id="kad-shortcode-insert" class="kad-addshortcode-btn" style=""><?php _e("Add Shortcode", "virtue"); ?></a>
			</div>
	</div> 
	</div>
	</div>
<?php  $output = ob_get_contents();
		ob_end_clean();
	echo $output;
}