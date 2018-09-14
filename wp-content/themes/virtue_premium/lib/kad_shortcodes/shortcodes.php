<?php
/**
 * Shortcodes for Virtue
 *
 * @package Virtue Theme
 */

// map shortcode.
require_once trailingslashit( get_template_directory() ) . 'lib/kad_shortcodes/google-map-shortcode.php';

/**
 * Shortcode for year
 */
function virtue_year_shortcode_function() {
	$year = date( 'Y' );
	return $year;
}
/**
 * Shortcode for copy
 */
function virtue_copyright_shortcode_function() {
	return '&copy;';
}
/**
 * Shortcode for name
 */
function virtue_sitename_shortcode_function() {
	$sitename = get_bloginfo( 'name' );
	return $sitename;
}
/**
 * Shortcode for theme credit
 */
function virtue_themecredit_shortcode_function() {
	$my_theme = wp_get_theme();
	$output   = '- WordPress Theme by <a href="' . esc_url( $my_theme->{'Author URI'} ) . '">Kadence Themes</a>';
	return $output;
}
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if ( is_plugin_active('virtue-toolkit/virtue_toolkit.php') ) {
function virtue_plugin_admin_notice(){
    echo '<div class="error"><p>Please <strong>Disable</strong> the Kadence ToolKit Plugin. It is not needed with Virtue Premium.</p></div>';
}
add_action('admin_notices', 'virtue_plugin_admin_notice');
}

//Shortcode for accordion
function kadence_accordion_shortcode_function($atts, $content ) {
	extract(shortcode_atts(array(
'id' => rand(1, 99)
), $atts));
	global $kt_pane_count, $kt_panes;
		$kt_pane_count = 0;
		$kt_panes = array();
		$return = '';
		do_shortcode( $content );
			if( is_array( $kt_panes ) && !empty($kt_panes)){
				$i = 0;
				foreach( $kt_panes as $tab ){
					if ($i % 2 == 0) {
						$eo = "even";
					} else {
						$eo = "odd";
					}
					$tabs[] = '<div class="panel panel-default panel-'.$eo.'"><div class="panel-heading"><a class="accordion-toggle '.$tab['open'].'" data-toggle="collapse" data-parent="#accordionname'.$id.'" href="#collapse'.$id.$tab['link'].'"><h5><i class="icon-minus primary-color"></i><i class="icon-plus"></i>'.$tab['title'].'</h5></a></div><div id="collapse'.$id.$tab['link'].'" class="panel-collapse collapse '.$tab['in'].'"><div class="panel-body postclass">'.$tab['content'].'</div></div></div>';
					$i++;
				}
				$return = "\n".'<div class="panel-group" id="accordionname'.$id.'">'.implode( "\n", $tabs ).'</div>'."\n";
			}
		return $return;
}

function kadence_accordion_pane_function($atts, $content ) {
	extract(shortcode_atts(array(
		'title' => 'Pane %d',
		'start' => ''
	), $atts));
	if (!empty($start) || $start == 'closed') {
		$open = '';
	} else {
		$open = 'collapsed';
	}
	if (!empty($start) || $start == 'closed') {
		$in = 'in';
	} else {
		$in = '';
	}
	global $kt_pane_count, $kt_panes;
	$x = $kt_pane_count;

	$kt_panes[$x] = array( 'title' => $title, 'open' => $open, 'in' => $in, 'link' => $kt_pane_count, 'content' =>  do_shortcode( $content ) );

	$kt_pane_count++;
}
function kadence_tab_shortcode_function($atts, $content ) {
	extract(shortcode_atts(array(
		'id' => rand(1, 999)
	), $atts));
	global $kt_tab_count, $kt_tabs;
	$kt_tab_count = 0;
	$kt_tabs = array();
	$return = '';
	do_shortcode( $content );
	if( is_array( $kt_tabs ) && !empty($kt_tabs)) {	
		foreach( $kt_tabs as $nav ){
			$tabnav[] = '<li class="'.$nav['active'].'"><a href="#sctab'.$id.$nav['link'].'" rel="nofollow">'.$nav['title'].'</a></li>';
		}
		foreach( $kt_tabs as $tab ){
			$tabs[] = '<div class="tab-pane clearfix '.$tab['active'].'" id="sctab'.$id.$tab['link'].'">'.$tab['content'].'</div>';
		}
		
		$return = "\n".'<ul class="nav nav-tabs sc_tabs">'.implode( "\n", $tabnav ).'</ul> <div class="tab-content postclass">'.implode( "\n", $tabs ).'</div>'."\n";
	}
	return $return;
}
function kadence_tab_pane_function($atts, $content ) {
	extract(shortcode_atts(array(
		'title' => 'Tab %d',
		'start' => ''
	), $atts));
	if (!empty($start)) {
		$active = 'active';
	} else {
		$active = '';
	}
	global $kt_tab_count, $kt_tabs;
	$x = $kt_tab_count;
	$kt_tabs[$x] = array( 'title' => $title, 'active' => $active, 'link' => $kt_tab_count, 'content' =>  do_shortcode( $content ) );

	$kt_tab_count++;
}
//product toggle
function kadence_product_toggle_shortcode_function( $atts) {
	return '<div class="kt_product_toggle_container"><div title="'.__("Grid View", "virtue").'" class="toggle_grid toggle_active" data-toggle="product_grid"><i class="icon-grid5"></i></div> <div title="'.__("List View", "virtue").'" class="toggle_list" data-toggle="product_list"><i class="icon-menu4"></i></div></div>';
}

//Shortcode for columns
function kadence_column_shortcode_function( $atts, $content ) {
	return '<div class="row">'.do_shortcode($content).'</div>';
}
function kadence_hcolumn_shortcode_function( $atts, $content ) {
	return '<div class="row">'.do_shortcode($content).'</div>';
}
function kadence_column11_function( $atts, $content ) {
	extract(shortcode_atts(array(
			'tablet' => '',
			'phone' => ''
			), $atts));
		if(empty($tablet)) {$tclass = "";} else if ($tablet == 'span2') {$tclass = "col-sm-2";} else if ($tablet == 'span3') {$tclass = "col-sm-3";} else if ($tablet == 'span4') {$tclass = "col-sm-4";} else if ($tablet == 'span6') {$tclass = "col-sm-6";} else if ($tablet == 'span8') {$tclass = "col-sm-8";} else {$tclass = "";}
		if(empty($phone)) {$pclass = "";} else if ($phone == 'span2') {$pclass = "col-ss-2";} else if ($phone == 'span3') {$pclass = "col-ss-3";} else if ($phone == 'span4') {$pclass = "col-ss-4";} else if ($phone == 'span6') {$pclass = "col-ss-6";} else if ($phone == 'span8') {$pclass = "col-ss-8";} else {$tclass = "";}
	return '<div class="col-md-11 '.$tclass.' '.$pclass.'">'.do_shortcode($content).'</div>';
}
function kadence_column10_function( $atts, $content ) {
	extract(shortcode_atts(array(
			'tablet' => '',
			'phone' => ''
			), $atts));
		if(empty($tablet)) {$tclass = "";} else if ($tablet == 'span2') {$tclass = "col-sm-2";} else if ($tablet == 'span3') {$tclass = "col-sm-3";} else if ($tablet == 'span4') {$tclass = "col-sm-4";} else if ($tablet == 'span6') {$tclass = "col-sm-6";} else if ($tablet == 'span8') {$tclass = "col-sm-8";} else {$tclass = "";}
		if(empty($phone)) {$pclass = "";} else if ($phone == 'span2') {$pclass = "col-ss-2";} else if ($phone == 'span3') {$pclass = "col-ss-3";} else if ($phone == 'span4') {$pclass = "col-ss-4";} else if ($phone == 'span6') {$pclass = "col-ss-6";} else if ($phone == 'span8') {$pclass = "col-ss-8";} else {$tclass = "";}
	return '<div class="col-md-10 '.$tclass.' '.$pclass.'">'.do_shortcode($content).'</div>';
}
function kadence_column9_function( $atts, $content ) {
	extract(shortcode_atts(array(
			'tablet' => '',
			'phone' => ''
			), $atts));
		if(empty($tablet)) {$tclass = "";} else if ($tablet == 'span2') {$tclass = "col-sm-2";} else if ($tablet == 'span3') {$tclass = "col-sm-3";} else if ($tablet == 'span4') {$tclass = "col-sm-4";} else if ($tablet == 'span6') {$tclass = "col-sm-6";} else if ($tablet == 'span8') {$tclass = "col-sm-8";} else {$tclass = "";}
		if(empty($phone)) {$pclass = "";} else if ($phone == 'span2') {$pclass = "col-ss-2";} else if ($phone == 'span3') {$pclass = "col-ss-3";} else if ($phone == 'span4') {$pclass = "col-ss-4";} else if ($phone == 'span6') {$pclass = "col-ss-6";} else if ($phone == 'span8') {$pclass = "col-ss-8";} else {$tclass = "";}
	return '<div class="col-md-9 '.$tclass.' '.$pclass.'">'.do_shortcode($content).'</div>';
}
function kadence_column8_function( $atts, $content ) {
	extract(shortcode_atts(array(
			'tablet' => '',
			'phone' => ''
			), $atts));
		if(empty($tablet)) {$tclass = "";} else if ($tablet == 'span2') {$tclass = "col-sm-2";} else if ($tablet == 'span3') {$tclass = "col-sm-3";} else if ($tablet == 'span4') {$tclass = "col-sm-4";} else if ($tablet == 'span6') {$tclass = "col-sm-6";} else if ($tablet == 'span8') {$tclass = "col-sm-8";} else {$tclass = "";}
		if(empty($phone)) {$pclass = "";} else if ($phone == 'span2') {$pclass = "col-ss-2";} else if ($phone == 'span3') {$pclass = "col-ss-3";} else if ($phone == 'span4') {$pclass = "col-ss-4";} else if ($phone == 'span6') {$pclass = "col-ss-6";} else if ($phone == 'span8') {$pclass = "col-ss-8";} else {$tclass = "";}
	return '<div class="col-md-8 '.$tclass.' '.$pclass.'">'.do_shortcode($content).'</div>';
}
function kadence_column7_function( $atts, $content ) {
	extract(shortcode_atts(array(
			'tablet' => '',
			'phone' => ''
			), $atts));
		if(empty($tablet)) {$tclass = "";} else if ($tablet == 'span2') {$tclass = "col-sm-2";} else if ($tablet == 'span3') {$tclass = "col-sm-3";} else if ($tablet == 'span4') {$tclass = "col-sm-4";} else if ($tablet == 'span6') {$tclass = "col-sm-6";} else if ($tablet == 'span8') {$tclass = "col-sm-8";} else {$tclass = "";}
		if(empty($phone)) {$pclass = "";} else if ($phone == 'span2') {$pclass = "col-ss-2";} else if ($phone == 'span3') {$pclass = "col-ss-3";} else if ($phone == 'span4') {$pclass = "col-ss-4";} else if ($phone == 'span6') {$pclass = "col-ss-6";} else if ($phone == 'span8') {$pclass = "col-ss-8";} else {$tclass = "";}
	return '<div class="col-md-7 '.$tclass.' '.$pclass.'">'.do_shortcode($content).'</div>';
}
function kadence_column6_function( $atts, $content ) {
	extract(shortcode_atts(array(
			'tablet' => '',
			'phone' => ''
			), $atts));
		if(empty($tablet)) {$tclass = "";} else if ($tablet == 'span2') {$tclass = "col-sm-2";} else if ($tablet == 'span3') {$tclass = "col-sm-3";} else if ($tablet == 'span4') {$tclass = "col-sm-4";} else if ($tablet == 'span6') {$tclass = "col-sm-6";} else if ($tablet == 'span8') {$tclass = "col-sm-8";} else {$tclass = "";}
		if(empty($phone)) {$pclass = "";} else if ($phone == 'span2') {$pclass = "col-ss-2";} else if ($phone == 'span3') {$pclass = "col-ss-3";} else if ($phone == 'span4') {$pclass = "col-ss-4";} else if ($phone == 'span6') {$pclass = "col-ss-6";} else if ($phone == 'span8') {$pclass = "col-ss-8";} else {$tclass = "";}
	return '<div class="col-md-6 '.$tclass.' '.$pclass.'">'.do_shortcode($content).'</div>';
}
function kadence_column5_function( $atts, $content ) {
	extract(shortcode_atts(array(
			'tablet' => '',
			'phone' => ''
			), $atts));
		if(empty($tablet)) {$tclass = "";} else if ($tablet == 'span2') {$tclass = "col-sm-2";} else if ($tablet == 'span3') {$tclass = "col-sm-3";} else if ($tablet == 'span4') {$tclass = "col-sm-4";} else if ($tablet == 'span6') {$tclass = "col-sm-6";} else if ($tablet == 'span8') {$tclass = "col-sm-8";} else {$tclass = "";}
		if(empty($phone)) {$pclass = "";} else if ($phone == 'span2') {$pclass = "col-ss-2";} else if ($phone == 'span3') {$pclass = "col-ss-3";} else if ($phone == 'span4') {$pclass = "col-ss-4";} else if ($phone == 'span6') {$pclass = "col-ss-6";} else if ($phone == 'span8') {$pclass = "col-ss-8";} else {$tclass = "";}
	return '<div class="col-md-5 '.$tclass.' '.$pclass.'">'.do_shortcode($content).'</div>';
}
function kadence_column4_function( $atts, $content ) {
	extract(shortcode_atts(array(
			'tablet' => '',
			'phone' => ''
			), $atts));
		if(empty($tablet)) {$tclass = "";} else if ($tablet == 'span2') {$tclass = "col-sm-2";} else if ($tablet == 'span3') {$tclass = "col-sm-3";} else if ($tablet == 'span4') {$tclass = "col-sm-4";} else if ($tablet == 'span6') {$tclass = "col-sm-6";} else if ($tablet == 'span8') {$tclass = "col-sm-8";} else {$tclass = "";}
		if(empty($phone)) {$pclass = "";} else if ($phone == 'span2') {$pclass = "col-ss-2";} else if ($phone == 'span3') {$pclass = "col-ss-3";} else if ($phone == 'span4') {$pclass = "col-ss-4";} else if ($phone == 'span6') {$pclass = "col-ss-6";} else if ($phone == 'span8') {$pclass = "col-ss-8";} else {$tclass = "";}
	return '<div class="col-md-4 '.$tclass.' '.$pclass.'">'.do_shortcode($content).'</div>';
}
function kadence_column3_function( $atts, $content ) {
	extract(shortcode_atts(array(
			'tablet' => '',
			'phone' => ''
			), $atts));
		if(empty($tablet)) {$tclass = "";} else if ($tablet == 'span2') {$tclass = "col-sm-2";} else if ($tablet == 'span3') {$tclass = "col-sm-3";} else if ($tablet == 'span4') {$tclass = "col-sm-4";} else if ($tablet == 'span6') {$tclass = "col-sm-6";} else if ($tablet == 'span8') {$tclass = "col-sm-8";} else {$tclass = "";}
		if(empty($phone)) {$pclass = "";} else if ($phone == 'span2') {$pclass = "col-ss-2";} else if ($phone == 'span3') {$pclass = "col-ss-3";} else if ($phone == 'span4') {$pclass = "col-ss-4";} else if ($phone == 'span6') {$pclass = "col-ss-6";} else if ($phone == 'span8') {$pclass = "col-ss-8";} else {$tclass = "";}
	return '<div class="col-md-3 '.$tclass.' '.$pclass.'">'.do_shortcode($content).'</div>';
}
function kadence_column2_function( $atts, $content ) {
	extract(shortcode_atts(array(
			'tablet' => '',
			'phone' => ''
			), $atts));
		if(empty($tablet)) {$tclass = "";} else if ($tablet == 'span2') {$tclass = "col-sm-2";} else if ($tablet == 'span3') {$tclass = "col-sm-3";} else if ($tablet == 'span4') {$tclass = "col-sm-4";} else if ($tablet == 'span6') {$tclass = "col-sm-6";} else if ($tablet == 'span8') {$tclass = "col-sm-8";} else {$tclass = "";}
		if(empty($phone)) {$pclass = "";} else if ($phone == 'span2') {$pclass = "col-ss-2";} else if ($phone == 'span3') {$pclass = "col-ss-3";} else if ($phone == 'span4') {$pclass = "col-ss-4";} else if ($phone == 'span6') {$pclass = "col-ss-6";} else if ($phone == 'span8') {$pclass = "col-ss-8";} else {$tclass = "";}
	return '<div class="col-md-2 '.$tclass.' '.$pclass.'">'.do_shortcode($content).'</div>';
}
function kadence_column25_function( $atts, $content ) {
	extract(shortcode_atts(array(
			'tablet' => '',
			'phone' => ''
			), $atts));
		if(empty($tablet)) {$tclass = "";} else if ($tablet == 'span2') {$tclass = "col-sm-2";} else if ($tablet == 'span3') {$tclass = "col-sm-3";} else if ($tablet == 'span4') {$tclass = "col-sm-4";} else if ($tablet == 'span6') {$tclass = "col-sm-6";} else if ($tablet == 'span8') {$tclass = "col-sm-8";} else {$tclass = "";}
		if(empty($phone)) {$pclass = "";} else if ($phone == 'span2') {$pclass = "col-ss-2";} else if ($phone == 'span3') {$pclass = "col-ss-3";} else if ($phone == 'span4') {$pclass = "col-ss-4";} else if ($phone == 'span6') {$pclass = "col-ss-6";} else if ($phone == 'span8') {$pclass = "col-ss-8";} else {$tclass = "";}
	return '<div class="col-md-25 '.$tclass.' '.$pclass.'">'.do_shortcode($content).'</div>';
}
function kadence_column1_function( $atts, $content ) {
	extract(shortcode_atts(array(
			'tablet' => '',
			'phone' => ''
			), $atts));
		if(empty($tablet)) {$tclass = "";} else if ($tablet == 'span2') {$tclass = "col-sm-2";} else if ($tablet == 'span3') {$tclass = "col-sm-3";} else if ($tablet == 'span4') {$tclass = "col-sm-4";} else if ($tablet == 'span6') {$tclass = "col-sm-6";} else if ($tablet == 'span8') {$tclass = "col-sm-8";} else {$tclass = "";}
		if(empty($phone)) {$pclass = "";} else if ($phone == 'span2') {$pclass = "col-ss-2";} else if ($phone == 'span3') {$pclass = "col-ss-3";} else if ($phone == 'span4') {$pclass = "col-ss-4";} else if ($phone == 'span6') {$pclass = "col-ss-6";} else if ($phone == 'span8') {$pclass = "col-ss-8";} else {$tclass = "";}
	return '<div class="col-md-1 '.$tclass.' '.$pclass.'">'.do_shortcode($content).'</div>';
}
//Shortcode for Icons
function kadence_icon_shortcode_function( $atts) {
	extract(shortcode_atts(array(
		'icon' => '',
		'size' => '',
		'color' => '',
		'style' => '',
		'background' => '',
		'float'=> ''
), $atts));
	if($style == 'circle') {$stylecss = 'kad-circle-iconclass';}
	 else if($style == 'smcircle') {$stylecss = 'kad-smcircle-iconclass';}
	 else if($style == 'square') {$stylecss = 'kad-square-iconclass';}
	 else if($style == 'smsquare') {$stylecss = 'kad-smsquare-iconclass';}
	 else {$stylecss = '';}
	if(empty($background)) {$background = '#eee';}
	if(empty($icon)) {$icon = 'icon-home';}
	if(empty($size)) {$size = '20px';}
	if(empty($color)) {$color = '#444';}
	if(empty($float)) {$float = '';}
	ob_start(); ?>
			<i class="<?php echo $icon;?> <?php if(!empty($stylecss)){echo $stylecss;}?>" style="font-size:<?php echo esc_attr($size); ?>; display:inline-block; color:<?php echo $color;?>; <?php if(!empty($float)){echo 'float:'.$float.';';} if(!empty($stylecss)){echo 'background:'.$background.';';} ?>
			"></i>
			<?php if(!empty($link)) {echo '<a href="'.$link.'" class="kadinfolink">'; } ?>
	<?php  $output = ob_get_contents();
		ob_end_clean();
	return $output;
}
//Shortcode for Info Boxes
function kadence_info_boxes_shortcode_function( $atts, $content) {
	extract(shortcode_atts(array(
		'icon' => '',
		'image' => '',
		'alt' => '',
		'id' => (rand(10,100)),
		'size' => '',
		'link' => '',
		'target' => '_self',
		'iconbackground' => '',
		'style' => '',
		'color' => '',
		'tcolor' => '',
		'class' => '',
		'background' => ''
), $atts));
	ob_start(); ?>
	<?php if(!empty($link)) {
		echo '<a href="'.esc_url($link).'" target="'.esc_attr($target).'" class="kadinfolink">'; 
	} ?>
	<div class="kad-info-box kad-info-box-<?php echo esc_attr($id);?> <?php echo esc_attr($class);?> clearfix" style="<?php if(!empty($background)) echo 'background:'.esc_attr($background);?>">
		<?php if(!empty($image)){?> 
			<img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($alt); ?>">
		<?php } else if(!empty($icon)){?> 
				<i class="<?php echo esc_attr($icon);?> <?php if(!empty($style)) {echo $style;}?>" style="<?php if(!empty($iconbackground)) echo 'background:'.esc_attr($iconbackground);?>; font-size:<?php echo esc_attr($size);?>px; <?php if(!empty($color)) echo 'color:'.esc_attr($color);?>"></i>
		<?php }
			 echo wp_kses_post($content); ?>
	</div>
	<?php if(!empty($link)) {echo '</a>'; } 
	if(!empty($tcolor)) {echo '<style type="text/css" media="screen">.kad-info-box-'.esc_attr($id).' h1, .kad-info-box-'.esc_attr($id).' h2, .kad-info-box-'.esc_attr($id).' h3, .kad-info-box-'.esc_attr($id).' h4, .kad-info-box-'.esc_attr($id).' h5, .kad-info-box-'.esc_attr($id).' p {color:'.esc_attr($tcolor).';}</style>';}?>
	<?php  $output = ob_get_contents();
		ob_end_clean();
	return $output;
}
//Shortcode for Icons Boxes
function kadence_icon_boxes_shortcode_function( $atts, $content) {
	extract(shortcode_atts(array(
		'icon' => '',
		'id' => (rand(10,100)),
		'iconsize' => '',
		'color' => '',
		'image' => '',
		'background' => '',
		'hcolor' => '',
		'hbackground' => '',
		'link' => '',
		'class' => '',
		'target' => '_self'
), $atts));
	if(empty($color)) {$color = '#444';}
	if(empty($background)) {$background = 'transparent';}
	$hover_bright = '';
	if($hbackground == 'primary') {$hbackground = ''; $hover_bright = "kad-hover-bg-primary";}
	if(!empty($link)) {
		$output = '<a href="'.esc_attr($link).'" target="'.esc_attr($target).'" class="kad-icon-box-'.esc_attr($id).' '.esc_attr($class).' kad-icon-box '.esc_attr($hover_bright).'">';
	} else {
		$output = '<div class="kad-icon-box-'.esc_attr($id).' kad-icon-box '.esc_attr($hover_bright).'">';
	}
	if(!empty($image)) {
	$output .= '<img src="'.esc_url($image).'" class="kad-icon-box-img">'.do_shortcode($content);
	} else {
	$output .= '<i class="'.esc_attr($icon).'" style="font-size:'.esc_attr($iconsize).';"></i>'.do_shortcode($content);
	}
	if(!empty($link)) {
		$output .= '</a>';
	} else {
		$output .= '</div>';
	}
	$output .= '<style type="text/css" media="screen">.kad-icon-box-'.esc_attr($id).' {background:'.esc_attr($background).';} .kad-icon-box-'.esc_attr($id).', .kad-icon-box-'.esc_attr($id).' h1, .kad-icon-box-'.esc_attr($id).' h2, .kad-icon-box-'.esc_attr($id).' h3, .kad-icon-box-'.esc_attr($id).' h4, .kad-icon-box-'.esc_attr($id).' h5 {color:'.esc_attr($color).' !important;} .kad-icon-box-'.esc_attr($id).':hover {background:'.esc_attr($hbackground).';} .kad-icon-box-'.esc_attr($id).':hover, .kad-icon-box-'.esc_attr($id).':hover h1, .kad-icon-box-'.esc_attr($id).':hover h2, .kad-icon-box-'.esc_attr($id).':hover h3, .kad-icon-box-'.esc_attr($id).':hover h4, .kad-icon-box-'.esc_attr($id).':hover h5 {color:'.esc_attr($hcolor).' !important;}</style>';

	return $output;
}
//Shortcode for Flip Boxes
function kadence_flip_boxes_shortcode_function( $atts) {
	extract(shortcode_atts(array(
		'id' => (rand(10,100)),
		'icon' => 'icon-rocket',
		'iconsize' => '48px',
		'iconcolor' => '#444',
		'titlecolor' => '#444',
		'title' => '',
		'description' => '',
		'height' => '',
		'titlesize' => '20px',
		'fcolor' => '#444',
		'image' => '',
		'flip_content' => '',
		'fbtn_text' => '',
		'fbtn_link' => '#',
		'fbtn_color' => '#fff',
		'fbtn_icon' => '',
		'fbtn_background' => 'transparent',
		'fbtn_border' => '2px solid #fff',
		'fbtn_border_radius' => '0',
		'background' => '#fff',
		'bcolor' => '#fff',
		'bbackground' => '#444',
		'fbtn_target' => '_self'
), $atts));
	$icon_color = 'color:'.$iconcolor.';';
	$front_color = 'color:'.$fcolor.';';
	$title_color = 'color:'.$titlecolor.';';
	$front_background = 'background:'.$background.';';
	$back_background = 'background:'.$bbackground.';';
	$back_color = 'color:'.$bcolor.';';
	if(!empty($height)) {
		$content_height = 'style="height:'.$height.';"';
	} else {
		$content_height = '';
	}
	$f_btn_background = 'background:'.$fbtn_background.';';
	$f_btn_color = 'color:'.$fbtn_color.';';
	$f_btn_border = 'border:'.$fbtn_border.';';
	$f_btn_border_radius = 'border-radius:'.$fbtn_border_radius.';';

	$output = '<div class="kt-flip-box-contain kt-mhover-inactive kt-m-hover kt-flip-box-'.$id.'" '.$content_height.'>';
	$output .= '<div class="kt-flip-box-flipper">';
		$output .= '<div class="kt-flip-box-front" style="'.$front_color.' '.$front_background.'">';
			$output .= '<div class="kt-flip-box-front-inner">';
			if(!empty($image)) {
				$output .= '<img src="'.$image.'" class="kad-flip-box-img kt-flip-icon">';
			} else {
				$output .= '<i class="'.$icon.' kad-flip-box-icon kt-flip-icon" style="font-size:'.$iconsize.'; '.$icon_color.'"></i>';
			}
			if(!empty($title) ){
				$output .= '<h4 style="'.$title_color.' font-size:'.$titlesize.'">'.do_shortcode($title).'</h4>';
			}
			if(!empty($description) ){
				$output .= '<p style="'.$front_color.'; margin:0;">'.do_shortcode($description).'</p>';
			}
			$output .= '</div>';
		$output .= '</div>';
		$output .= '<div class="kt-flip-box-back" style="'.$back_color.' '.$back_background.'">';
			$output .= '<div class="kt-flip-box-back-inner">';
			$output .= '<div style="'.$back_color.'">'.do_shortcode($flip_content).'</div>';
			if(!empty($fbtn_text)) {
				$output .= '<a href="'.$fbtn_link.'" target="'.$fbtn_target.'" style="'.$f_btn_background.' '.$f_btn_color.' '.$f_btn_border.' '.$f_btn_border_radius.'" class="kt-flip-btn">'.$fbtn_text;
				if(!empty($fbtn_icon) ){
					$output .= ' <i class="'.$fbtn_icon.'""></i>';
				}
				$output .= '</a>';
			}
			$output .= '</div>';
		$output .= '</div>';
		$output .= '</div>';
	$output .= '</div>';

	return $output;
}
//Shortcode for modal
function kadence_modal_shortcode_function( $atts, $content) {
	extract(shortcode_atts(array(
		'title' => 'Modal Title',
		'close' => 'true',
		'btntitle' => 'Click Here',
		'id' => '',
		'btnfont' => 'body',
		'btnsize' => 'medium',
		'btncolor' => '',
		'type' => 'button',
		'btnbackground' => ''
), $atts));
	if(empty($id)) {$id = rand(1, 99);}
	if($btnsize == 'large'){$sizeclass = "lg-kad-btn";} else if ($btnsize == 'small') {$sizeclass = "sm-kad-btn";} else {$sizeclass = "";}
	if($btnfont == 'h1-family'){$fontclass = "headerfont";} else {$fontclass = "";}
	ob_start(); 
		if($type == 'link'){?>
			<a class="kt-modal-link-<?php echo esc_attr($id);?> kt-modal-link" data-toggle="modal" data-target="#kt-modal-<?php echo esc_attr($id);?>">
			 <?php echo $btntitle; ?>
			</a>
		<?php } else { ?>
			<button class="kad-btn kad-btn-primary <?php echo esc_attr($sizeclass).' '.esc_attr($fontclass);?> kt-modal-btn-<?php echo esc_attr($id);?> kt-modal-btn" style="<?php if(!empty($btnbackground)) {echo 'background-color:'.esc_attr($btnbackground).';'; } if(!empty($btncolor)) { echo 'color:'.esc_attr($btncolor).';';}?>" data-toggle="modal" data-target="#kt-modal-<?php echo esc_attr($id);?>">
			<?php echo $btntitle; ?>
			</button>
		<?php } ?>

	<!-- Modal -->
	<div class="modal fade" id="kt-modal-<?php echo esc_attr($id);?>" tabindex="-1" role="dialog" aria-labelledby="#kt-modal-label-<?php echo esc_attr($id);?>" aria-hidden="true">
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	        <h4 class="modal-title" id="kt-modal-label-<?php echo $id;?>"><?php echo $title; ?></h4>
	      </div>
	      <div class="modal-body">
	        <?php echo do_shortcode($content); ?>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="kad-btn" data-dismiss="modal"><?php echo __('Close', 'virtue');?></button>
	      </div>
	    </div>
	  </div>
	</div>

	<?php  $output = ob_get_contents();
		ob_end_clean();
	return $output;
}
// Video Shortcode
function kadence_video_shortcode_function( $atts, $content) {
	extract(shortcode_atts(array(
		'width' => '',
		'height' => '',
		'mp4' => '',
		'm4v' => ''
), $atts));
	if(!empty($mp4)) {
		 $output = '<div class="videofit-embed"><video style="max-width:'.$width.'px; width:100%;" controls><source type="video/mp4" src="'.$mp4.'"/></video></div>';
	} elseif(!empty($m4v)) {
		 $output = '<div class="videofit-embed"><video style="max-width:'.$width.'px; width:100%;" controls><source type="video/m4v" src="'.$m4v.'"/></video></div>';
	} elseif(!empty($width)) { $output = '<div style="max-width:'.$width.'px;"><div class="videofit">'.$content.'</div></div>';}
	else { $output = '<div class="videofit">'.$content.'</div>'; }
	return $output;
}
function kadence_youtube_shortcode_function( $atts, $content) {
		// Prepare data
		$return = array();
		$params = array();
		$atts = shortcode_atts(array(
				'url'  => false,
				'width' => 600,
				'height' => 400,
				'maxwidth' => '',
				'autoplay' => 'false',
				'controls' => 'true',
				'hidecontrols' => 'false',
				'fs' => 'true',
				'loop' => 'false',
				'rel' => 'false',
				'vq' => '',
				'https' => 'true',
				'modestbranding' => 'false',
				'nocookie' => 'false',
				'theme' => 'dark'
		), $atts, 'kad_youtube' );

		if ( !$atts['url'] ) return '<p class="error">YouTube: ' . __( 'please specify correct url', 'virtue' ) . '</p>';
		$id = ( preg_match( '%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $atts['url'], $match ) ) ? $match[1] : false;
		// Check that url is specified
		if ( !$id ) return '<p class="error">YouTube: ' . __( 'please specify correct url', 'virtue' ) . '</p>';
		// Prepare params
		if($atts['hidecontrols'] == 'true') {$atts['controls'] = 'false';}
		foreach ( array('autoplay', 'controls', 'fs', 'modestbranding', 'theme', 'rel', 'loop' ) as $param ) $params[$param] = str_replace( array( 'false', 'true', 'alt' ), array( '0', '1', '2' ), $atts[$param] );
		// Prepare player parameters
		if(!empty($atts['vq']) ) {$params['vq'] = $atts['vq']; }
		$params = http_build_query( $params );
		if($atts['maxwidth']) {$maxwidth = 'style="max-width:'.$atts['maxwidth'].'px;"';} else{ $maxwidth = '';}
		if(isset($atts['nocookie']) && $atts['nocookie'] == 'true') {$youtubeurl = 'youtube-nocookie.com';} else{$youtubeurl = 'youtube.com';}
		$protocol = ( $atts['https'] === 'true' ) ? 'https' : 'http';
		// Create player
		$return[] = '<div class="kad-youtube-shortcode videofit" '.$maxwidth.' >';
		$return[] = '<iframe width="' . $atts['width'] . '" height="' . $atts['height'] . '" src="'.$protocol.'://www.'.$youtubeurl.'/embed/' . $id . '?' . $params . '" frameborder="0" allowfullscreen="true"></iframe>';
		$return[] = '</div>';
		// Return result
		return implode( '', $return );
}
function kadence_vimeo_shortcode_function( $atts, $content) {
		$return = array();
		$atts = shortcode_atts( array(
				'url'        => false,
				'width'      => 600,
				'height'     => 400,
				'maxwidth' => '',
				'https' => 'true',
				'autoplay'   => 'false'
			), $atts, 'vimeo' );
		if ( !$atts['url'] ) return '<p class="error">Vimeo: ' . __( 'please specify correct url', 'virtue' ) . '</p>';
		$id = ( preg_match( '~(?:<iframe [^>]*src=")?(?:https?:\/\/(?:[\w]+\.)*vimeo\.com(?:[\/\w]*\/videos?)?\/([0-9]+)[^\s]*)"?(?:[^>]*></iframe>)?(?:<p>.*</p>)?~ix', $atts['url'], $match ) ) ? $match[1] : false;
		// Check that url is specified
		if ( !$id ) return '<p class="error">Vimeo: ' . __( 'please specify correct url', 'virtue' ) . '</p>';

		if($atts['maxwidth']) {$maxwidth = 'style="max-width:'.$atts['maxwidth'].'px;"';} else{ $maxwidth = '';}
		$autoplay = ( $atts['autoplay'] === 'yes' || $atts['autoplay'] === 'true' ) ? '&amp;autoplay=1' : '';
		$protocol = ( $atts['https'] === 'true' ) ? 'https' : 'http';
		// Create player
		$return[] = '<div class="kad-vimeo-shortcode  videofit" '.$maxwidth.'>';
		$return[] = '<iframe width="' . $atts['width'] . '" height="' . $atts['height'] .
			'" src="'.$protocol.'://player.vimeo.com/video/' . $id . '?title=0&amp;byline=0&amp;portrait=0&amp;color=ffffff' .
			$autoplay . '" frameborder="0" allowfullscreen="true"></iframe>';
		$return[] = '</div>';
		// Return result
		return implode( '', $return );
	}

//Image Split
function kadence_image_split_shortcode_function( $atts, $content ) {
	extract(shortcode_atts(array(
		'height' 				=> '500',
		'image' 				=> '',
		'image_id' 				=> '',
		'description_max_width' => null,
		'description_align' 	=> 'default',
		'image_cover' 			=> 'false',
		'img_background' 		=> '',
		'content_background' 	=> '',
		'image_link' 			=> '',
		'link_target' 			=> '_self',
		'imageside' 			=> 'left',
		'id' 					=> rand(1, 99),
	), $atts));
	if(!empty($description_max_width) && $description_max_width != '0'){
		$max_width = 'max-width:'.$description_max_width.'px;';
	} else {
		$max_width = '';
	}
	if(!empty($text_color)){
		$tcolor = 'color:'.$text_color.';';
	} else {
		$tcolor = '';
	}
	if(!empty($description_align) && $description_align != 'default'){
		$textalign = 'text-align:'.$description_align.';';
	} else {
		$textalign = '';
	}
	if(!empty($image_id)) {
		$alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
	} else {
		$alt = '';
	}
	ob_start(); ?>
	<!-- Image Split -->
	<div class="kt-image-slit" id="kt-image-split-<?php echo $id;?>">
	  <div class="row">
	    <div class="col-sm-6 kt-si-imagecol img-ktsi-<?php echo esc_attr($imageside);?> kt-animate-fade-in-<?php if($imageside == 'right') {echo 'left';} else {echo 'right';}?>" style="<?php if(!empty($img_background)) {echo 'background-color:'.esc_attr($img_background).';';} if($image_cover == 'true' && !empty($image)) {echo 'background-image:url('.esc_url($image).'); background-size:cover; background-position: center center; min-height:'.esc_attr($height / 2).'px;';}?>">
	      <div class="kt-si-table-box" style="height:<?php echo esc_attr($height);?>px">
	      	<div class="kt-si-cell-box">
	      		<?php if(!empty($image_link)) { echo '<a href="'.$image_link.'" target="'.$link_target.'" class="kt-si-image-link">';} 
	      		
	      		if($image_cover == 'true' && !empty($image)) {
	      			echo '<div class="kt-si-image kt-si-cover-image" style="max-height:'.$height.'px;"></div>'; 
	      		} else if(!empty($image)){
	      			 echo '<img src="'.esc_url($image).'" class="kt-si-image" style="max-height:'.$height.'px">'; 
	      		}

	      		if(!empty($image_link)) { echo '</a>';} ?>
	        </div>
	      </div>
	     </div>
	     <div class="col-sm-6 kt-si-imagecol content-ktsi-<?php echo $imageside;?> kt-animate-fade-in-<?php echo esc_attr($imageside);?>" <?php if(!empty($content_background)) {echo 'style="background-color:'.$content_background.'"';}?>>
	      <div class="kt-si-table-box" style="height:<?php echo esc_attr($height);?>px">
	      	<div class="kt-si-cell-box" style="<?php echo esc_attr($max_width.' '.$tcolor.' '.$textalign);?>">
 				<?php echo do_shortcode($content); ?>
	        </div>
	      </div>
	     </div>
	  </div>
	</div>

	<?php  $output = ob_get_contents();
		ob_end_clean();
	return $output;
}
//Typed Text
function kadence_typed_text_shortcode_function( $atts, $content) {
	extract(shortcode_atts(array(
		'first_sentence' => 'typed text',
		'second_sentence' => '',
		'third_sentence' => '',
		'fourth_sentence' => '',
		'loop' => 'false',
		'startdelay' => '500',
		'speed' => '40',
), $atts));
if(!empty($second_sentence) && empty($third_sentence) && empty($fourth_sentence)) {
	$count = '2';
} else if(!empty($second_sentence) && !empty($third_sentence) && empty($fourth_sentence)) {
	$count = '3';
} else if(!empty($second_sentence) && !empty($third_sentence) && !empty($fourth_sentence)){
	$count = '4';
} else {
	$count = '1';
}
$output = '<span class="kt_typed_element" data-first-sentence="'.$first_sentence.'"';
	if(!empty($second_sentence)) {
		$output .= ' data-second-sentence="'.$second_sentence.'"';
	}
	if(!empty($third_sentence)) {
		$output .= ' data-third-sentence="'.$third_sentence.'"';
	}
	if(!empty($fourth_sentence)) {
		$output .= ' data-fourth-sentence="'.$fourth_sentence.'"';
	}
	$output .= 'data-sentence-count="'.$count.'" data-loop="'.$loop.'" data-speed="'.$speed.'" data-start-delay="'.$startdelay.'"></span>';

	return $output;
}

	//Simple Box
function kadence_simple_box_shortcode_function( $atts, $content ) {
	extract(shortcode_atts(array(
		'padding_top' => '15',
		'padding_bottom' => '15',
		'padding_left' => '15',
		'padding_right' => '15',
		'min_height' => '1',
		'background' => '#ffffff',
		'style' => '',
		'valign' => 'top',
		'opacity' => '1'
), $atts));
	$bg_color_rgb = kad_hex2rgb($background);
	if(!empty($style)) {$style = $style;} else {$style = '';}
    $bcolor = 'rgba('.$bg_color_rgb[0].', '.$bg_color_rgb[1].', '.$bg_color_rgb[2].', '.$opacity.');';
    if($valign == "middle"){
    	$output = '<div class="kt-simple-box kt-valign-center" style="background-color:'.$bcolor.' min-height:'.$min_height.'px; padding-top:'.$padding_top.'px; padding-bottom:'.$padding_bottom.'px; padding-left:'.$padding_left.'px; padding-right:'.$padding_right.'px; '.$style.'">';
    	$output .='<div class="kt-simple-box-inner" style="height:'.$min_height.'px;">';
    } else {
   		$output = '<div class="kt-simple-box" style="background-color:'.$bcolor.' min-height:'.$min_height.'px; padding-top:'.$padding_top.'px; padding-bottom:'.$padding_bottom.'px; padding-left:'.$padding_left.'px; padding-right:'.$padding_right.'px; '.$style.'">';
    	$output .='<div class="kt-simple-box-inner">';
    }

    $output .= do_shortcode($content) .'</div></div>';
	return $output;
}
//Button
function kadence_button_shortcode_function( $atts) {
	extract(shortcode_atts(array(
		'id' => rand(1, 99),
		'bcolor' => '',
		'bhovercolor' => '',
		'thovercolor' => '',
		'link' => '',
		'target' => '',
		'border' => '0',
		'borderradius' => '0',
		'bordercolor' => '#000',
		'borderhovercolor' => '',
		'text' => '',
		'size' => 'medium',
		'font' => 'body',
		'icon' => '',
		'tcolor' => '',
), $atts));
	if($target == 'true' || $target == '_blank') {$target = '_blank';} else {$target = '_self';} 
	if($size == 'large'){
		$sizeclass = "lg-kad-btn";
	} else if ($size == 'small') {
		$sizeclass = "sm-kad-btn";
	} else {
		$sizeclass = "";
	}
	if($font == 'h1-family'){
		$fontclass = "headerfont";
	} else {
		$fontclass = "";
	}
	if(!empty($icon)) {
		$iconhtml = "<i class='".esc_attr($icon)."'></i>";
	} else {
		$iconhtml = "";
	}
	if(!empty($borderradius) || $borderradius != '0') {
		$borderradius = 'border-radius:'.$borderradius.';';
	} else {
		$borderradius = '';
	}

	$js_over = '';
	if( ! empty( $bhovercolor ) ) {
		$js_over .= 'this.style.background=\''.$bhovercolor.'\'';
		if( ! empty( $thovercolor ) || !empty( $borderhovercolor ) ) {
			$js_over .= ',';
		}
		if( empty( $bcolor ) ) {
			$bcolor = "initial";
		}
	}
	if( ! empty( $thovercolor ) ) { 
		$js_over .= 'this.style.color=\''.$thovercolor.'\'';
		if( ! empty( $borderhovercolor ) ) {
			$js_over .= ',';
		}
		if( empty( $tcolor ) ) {
			$tcolor = "#fff";
		}
	}
	if(!empty($borderhovercolor)) {
		$js_over .= 'this.style.borderColor=\''.$borderhovercolor.'\'';
		if( empty( $bordercolor ) ) {
			$bordercolor = "#000";
		}
	}

	$js_out ='';
	if(!empty($bhovercolor)) {
		$js_out .= 'this.style.background=\''.$bcolor.'\'';
		if(!empty($thovercolor) || !empty($borderhovercolor)) {
			$js_out .= ',';
		}
	}
	if(!empty($thovercolor)) { 
	 	$js_out .= 'this.style.color=\''.$tcolor.'\'';
	 	if(!empty($borderhovercolor)) {
			$js_out .= ',';
		}
	}
	if(!empty($borderhovercolor)) {
		$js_out .= 'this.style.borderColor=\''.$bordercolor.'\'';
	}
	$js_out .='';

	if(!empty($bcolor)) {
		$bgc = 'background-color:'.esc_attr($bcolor).';';
	} else {
		$bgc = '';
	}
	if(!empty($tcolor)) {
		$tc = 'color:'.esc_attr($tcolor).';';
	} else {
		$tc = '';
	}

	$output =  '<a href="'.esc_attr($link).'" id="kadbtn'.esc_attr($id).'" target="'.esc_attr($target).'" class="kad-btn btn-shortcode kad-btn-primary '.esc_attr($sizeclass).' '.esc_attr($fontclass).'" style="'.esc_attr($bgc).' border: '.esc_attr($border).' solid; border-color:'.esc_attr($bordercolor).'; '.esc_attr($borderradius).' '.esc_attr($tc).'" onMouseOver="'.esc_attr($js_over).'" onMouseOut="'.esc_attr($js_out).'">'.wp_kses_post($text.' '.$iconhtml).'</a>';

return $output;
}
function kadence_blockquote_shortcode_function( $atts, $content) {
	extract(shortcode_atts(array(
		'align' => 'center',
), $atts));
		switch ($align)
	{
		case "center":
		$output = '<div class="blockquote-full postclass clearfix">' . do_shortcode($content) . '</div>';
		break;
		
		case "left":
		$output = '<div class="blockquote-left postclass clearfix">' . do_shortcode($content) . '</div>';
		break;
		
		case "right":
		$output = '<div class="blockquote-right postclass clearfix">' . do_shortcode($content) . '</div>';
		break;
	}
	  return $output;
}
function kadence_pullquote_shortcode_function( $atts, $content) {
   extract( shortcode_atts( array(
	  'align' => 'center'
  ), $atts ));

	switch ($align)
	{
		case "center":
		$output = '<div class="pullquote-center">' . do_shortcode($content) . '</div>';
		break;
		
		case "right":
		$output = '<div class="pullquote-right">' . do_shortcode($content) . '</div>';
		break;
		
		case "left":
		$output = '<div class="pullquote-left">' . do_shortcode($content) . '</div>';
		break;
	}

   return $output;
}
function kadence_hrule_function($atts) {
	extract(shortcode_atts(array(
		'color' => '',
		'style' => 'line',
		'size' => ''
), $atts));
	if($style == 'dots') {
		$output = '<div class="hrule_dots clearfix" style="';
		if(!empty($color)) {$output .= 'border-color:'.$color.';';}
		if(!empty($size)) {$output .= ' border-top-width:'.$size; }
		$output .= '"></div>';
	} elseif ($style == 'gradient') {
		$output = '<div class="hrule_gradient"></div>';
	} else {
		$output = '<div class="hrule clearfix" style="';
		if(!empty($color)) {$output .= 'background:'.$color.';';}
		if(!empty($size)) {$output .= ' height:'.$size; }
		$output .= '"></div>';
	}

	return $output;
}
function kadence_popover_function($atts, $content) {
	extract(shortcode_atts(array(
		'direction' => 'top',
		'text' => '',
		'title' => ''
), $atts));
		$output = '<a class="kad_popover" data-toggle="popover" data-placement="'.$direction.'" data-content="'.$text.'" data-original-title="'.$title.'">';
		$output .= $content;
		$output .= '</a>';

	return $output;
}
function kadence_hrule_dots_function($atts) {
	extract(shortcode_atts(array(
		'color' => '',
		'size' => ''
), $atts));
	$output = '<div class="hrule_dots clearfix" style="';
	if(!empty($color)) {$output .= 'border-color:'.$color.';';}
	if(!empty($size)) {$output .= ' border-top-width:'.$size.'px;'; }
	$output .= '"></div>';

	return $output;
}
function kadence_hrule_gradient_function() {
	$output = '<div class="hrule_gradient"></div>';
	return $output;
}
function kadence_hrpadding_function($atts ) {
	extract(shortcode_atts(array(
		'size' => ''
), $atts));
	if(empty($size)) {$size = '10px';}
	return '<div class="kad-spacer clearfix" style="height:'.$size.'"></div>';
}
function kadence_hrpadding_minus_10_function( ) {
	return '<div class="space_minus_10 clearfix"></div>';
}
function kadence_hrpadding_minus_20_function( ) {
	return '<div class="space_minus_20 clearfix"></div>';
}
function kadence_hrpadding10_function( ) {
	return '<div class="space_10 clearfix"></div>';
}
function kadence_hrpadding20_function( ) {
	return '<div class="space_20 clearfix"></div>';
}
function kadence_hrpadding40_function( ) {
	return '<div class="space_40 clearfix"></div>';
}
function kadence_hrpadding30_function( ) {
	return '<div class="space_30 clearfix"></div>';
}
function kadence_hrpadding80_function( ) {
	return '<div class="space_80 clearfix"></div>';
}
function kadence_clearfix_function( ) {
	return '<div class="clearfix"></div>';
}
function kadence_columnhelper_function( ) {
	return '';
}
/**
 * Breadcrumb shortcode
 */
function virtue_bc_shortcode() {
	ob_start();
	?>
	<div class="kt_shortcode_breadcrumbs">
		<?php kadence_breadcrumbs(); ?>
	</div>
	<?php
	$output = ob_get_contents();
	ob_end_clean();
	return $output;
}
/**
 * Load all extra shortcodes
 */
function virtue_extra_shortcodes() {
	add_shortcode( 'accordion', 'kadence_accordion_shortcode_function' );
	add_shortcode( 'pane', 'kadence_accordion_pane_function' );
	add_shortcode( 'tabs', 'kadence_tab_shortcode_function' );
	add_shortcode( 'tab', 'kadence_tab_pane_function' );
	add_shortcode( 'columns', 'kadence_column_shortcode_function' );
	add_shortcode( 'hcolumns', 'kadence_hcolumn_shortcode_function' );
	add_shortcode( 'span11', 'kadence_column11_function' );
	add_shortcode( 'span10', 'kadence_column10_function' );
	add_shortcode( 'span9', 'kadence_column9_function' );
	add_shortcode( 'span8', 'kadence_column8_function' );
	add_shortcode( 'span7', 'kadence_column7_function' );
	add_shortcode( 'span6', 'kadence_column6_function' );
	add_shortcode( 'span5', 'kadence_column5_function' );
	add_shortcode( 'span4', 'kadence_column4_function' );
	add_shortcode( 'span3', 'kadence_column3_function' );
	add_shortcode( 'span25', 'kadence_column25_function' );
	add_shortcode( 'span2', 'kadence_column2_function' );
	add_shortcode( 'span1', 'kadence_column1_function' );
	add_shortcode( 'columnhelper', 'kadence_columnhelper_function' );
	add_shortcode( 'icon', 'kadence_icon_shortcode_function' );
	add_shortcode( 'pullquote', 'kadence_pullquote_shortcode_function' );
	add_shortcode( 'blockquote', 'kadence_blockquote_shortcode_function' );
	add_shortcode( 'btn', 'kadence_button_shortcode_function' );
	add_shortcode( 'hr', 'kadence_hrule_function' );
	add_shortcode( 'hr_dots', 'kadence_hrule_dots_function' );
	add_shortcode( 'hr_gradient', 'kadence_hrule_gradient_function' );
	add_shortcode( 'minus_space_10', 'kadence_hrpadding_minus_10_function' );
	add_shortcode( 'minus_space_20', 'kadence_hrpadding_minus_20_function' );
	add_shortcode( 'space_10', 'kadence_hrpadding10_function' );
	add_shortcode( 'space_20', 'kadence_hrpadding20_function' );
	add_shortcode( 'space_30', 'kadence_hrpadding30_function' );
	add_shortcode( 'space_40', 'kadence_hrpadding40_function' );
	add_shortcode( 'space_80', 'kadence_hrpadding80_function' );
	add_shortcode( 'space', 'kadence_hrpadding_function' );
	add_shortcode( 'clear', 'kadence_clearfix_function' );
	add_shortcode( 'infobox', 'kadence_info_boxes_shortcode_function' );
	add_shortcode( 'iconbox', 'kadence_icon_boxes_shortcode_function' );
	add_shortcode( 'carousel', 'kad_carousel_shortcode_function' );
	add_shortcode( 'blog_posts', 'kad_blog_shortcode_function' );
	add_shortcode( 'testimonial_posts', 'kad_testimonial_shortcode_function' );
	add_shortcode( 'custom_carousel', 'kad_custom_carousel_shortcode_function' );
	add_shortcode( 'carousel_item', 'kad_custom_carousel_item_shortcode_function' );
	add_shortcode( 'img_menu', 'kad_image_menu_shortcode_function' );
	add_shortcode( 'gmap', 'virtue_map_shortcode_function' );
	add_shortcode( 'portfolio_posts', 'kad_portfolio_shortcode_function' );
	add_shortcode( 'portfolio_types', 'kad_portfolio_type_shortcode_function' );
	add_shortcode( 'staff_posts', 'kad_staff_shortcode_function' );
	add_shortcode( 'kad_youtube', 'kadence_youtube_shortcode_function' );
	add_shortcode( 'kad_vimeo', 'kadence_vimeo_shortcode_function' );
	add_shortcode( 'kad_popover', 'kadence_popover_function' );
	add_shortcode( 'kad_modal', 'kadence_modal_shortcode_function' );
	add_shortcode( 'kad_blog', 'kad_blog_simple_shortcode_function' );
	add_shortcode( 'blog_grid', 'kad_blog_grid_shortcode_function' );
	add_shortcode( 'kt_box', 'kadence_simple_box_shortcode_function' );
	add_shortcode( 'kt_imgsplit', 'kadence_image_split_shortcode_function' );
	add_shortcode( 'kt_product_toggle', 'kadence_product_toggle_shortcode_function' );
	add_shortcode( 'kt_breadcrumbs', 'virtue_bc_shortcode' );
	add_shortcode( 'kt_typed', 'kadence_typed_text_shortcode_function' );
	add_shortcode( 'kt_flip_box', 'kadence_flip_boxes_shortcode_function' );
}
add_action( 'init', 'virtue_extra_shortcodes' );

function kadence_add_plugin( $plugin_array ) {
   $plugin_array['kadcolumns'] = get_template_directory_uri() . '/lib/shortcodes/columns/columns_shortgen.js';
   return $plugin_array;
}
function kadence_tinymce_shortcode_button() {

   if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') ) {
      return;
   }

   if ( get_user_option('rich_editing') == 'true' ) {
      add_filter( 'mce_external_plugins', 'kadence_add_plugin' );
   }

}
add_action('init', 'kadence_tinymce_shortcode_button');

/**
 * Add shortcodes
 */
function virtue_footer_register_shortcodes() {
	add_shortcode( 'the-year', 'virtue_year_shortcode_function' );
	add_shortcode( 'copyright', 'virtue_copyright_shortcode_function' );
	add_shortcode( 'site-name', 'virtue_sitename_shortcode_function' );
	add_shortcode( 'theme-credit', 'virtue_themecredit_shortcode_function' );
}
add_action( 'init', 'virtue_footer_register_shortcodes' );
//    Clean up Shortcodes

function kad_content_clean_shortcodes($content){   
    $array = array (
        '<p>[' => '[', 
        ']</p>' => ']', 
        ']<br />' => ']'
    );
    $content = strtr($content, $array);
    return $content;
}
add_filter('the_content', 'kad_content_clean_shortcodes');
function kad_widget_clean_shortcodes($text){   
    $array = array (
        '<p>[' => '[', 
        ']</p>' => ']', 
        '<p></p>' => '', 
        ']<br />' => ']',
        '<br />[' => '['
    );
    $text = strtr($text, $array);
    return $text;
}
add_filter('widget_text', 'kad_widget_clean_shortcodes', 10);
remove_filter('widget_text', 'do_shortcode');
add_filter('widget_text', 'do_shortcode', 50);
add_action( 'init', 'kt_remove_bstw_do_shortcode' );
function kt_remove_bstw_do_shortcode() {
    if ( function_exists( 'bstw' ) ) {
        remove_filter( 'widget_text', array( bstw()->text_filters(), 'do_shortcode' ), 10 );
    }
}
add_filter('siteorigin_widgets_template_variables_sow-editor', 'kt_edit_sow_editor', 10, 4);
function kt_edit_sow_editor($template_vars, $instance, $args, $object) {
		$instance = wp_parse_args(
			$instance,
			array(  'text' => '' )
		);
		
		// Run some known stuff
		if( !empty($GLOBALS['wp_embed']) ) {
			$instance['text'] = $GLOBALS['wp_embed']->autoembed( $instance['text'] );
		}
		if (function_exists('wp_make_content_images_responsive')) {
			$instance['text'] = wp_make_content_images_responsive( $instance['text'] );
		}
		if( $instance['autop'] ) {
			$instance['text'] = wpautop( $instance['text'] );
		}
		$instance['text'] = do_shortcode( shortcode_unautop( $instance['text'] ) );
		$instance['text'] = apply_filters( 'widget_text', $instance['text'] );


		$text =  array('text' => $instance['text']);
		return $text;
}
