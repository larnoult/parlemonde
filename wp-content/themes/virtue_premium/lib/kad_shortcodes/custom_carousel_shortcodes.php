<?php 
//Shortcode for Custom Carousels
function kad_custom_carousel_shortcode_function( $atts, $content) {
	extract(shortcode_atts(array(
		'columns' => '4',
		'xxlcol' 	=> null,
		'xlcol' 	=> null,
		'mdcol' 	=> null,
		'smcol' 	=> null,
		'xscol' 	=> null,
		'sscol' 	=> null,
		'speed' => '9000',
		'scroll' => '1',
		'id' => (rand(10,1000)),
), $atts));
if ($columns == '2') {
	$ccc['md'] = 2; 
	$ccc['sm'] = 2; 
	$ccc['xs'] = 1; 
	$ccc['ss'] = 1;
} else if ($columns == '1') {
	$ccc['md'] = 1; 
	$ccc['sm'] = 1; 
	$ccc['xs'] = 1; 
	$ccc['ss'] = 1;
} else if ($columns == '3'){ 
	$ccc['md'] = 3; 
	$ccc['sm'] = 3; 
	$ccc['xs'] = 2; 
	$ccc['ss'] = 1;
} else if ($columns == '6'){
	$ccc['md'] = 6; 
	$ccc['sm'] = 4; 
	$ccc['xs'] = 3; 
	$ccc['ss'] = 2;
} else if ($columns == '5'){ 
	$ccc['md'] = 5; 
	$ccc['sm'] = 4; 
	$ccc['xs'] = 3; 
	$ccc['ss'] = 2;
} else {
	$ccc['md'] = 4;
	$ccc['sm'] = 3; 
	$ccc['xs'] = 2; 
	$ccc['ss'] = 1;
} 
$ccc = apply_filters('kadence_custom_carousel_columns', $ccc);
$ccc['xl'] = $ccc['md'];
$ccc['xxl'] = $ccc['md'];
if( !empty($xxlcol) ) {
	$ccc['xxl'] = $xxlcol;
}
if( !empty($xlcol) ) {
	$ccc['xl'] = $xlcol;
}
if( !empty($mdcol) ) {
	$ccc['md'] = $mdcol;
}
if( !empty($smcol) ) {
	$ccc['sm'] = $smcol;
}
if( !empty($xscol) ) {
	$ccc['xs'] = $xscol;
}
if( !empty($sscol) ) {
	$ccc['ss'] = $sscol;
}
ob_start(); 
			?>
			<div class="carousel_outerrim kad-animation" data-animation="fade-in" data-delay="0">
				<div class="custom-carouselcontainer rowtight">
					<div id="custom-carousel-<?php echo esc_attr($id);?>" class="slick-slider custom_carousel_shortcode kt-slickslider kt-content-carousel loading clearfix" data-slider-fade="false" data-slider-type="content-carousel" data-slider-anim-speed="400" data-slider-scroll="<?php echo esc_attr($scroll);?>" data-slider-auto="true" data-slider-speed="9000" data-slider-xxl="<?php echo esc_attr($ccc['xxl']);?>" data-slider-xl="<?php echo esc_attr($ccc['xl']);?>" data-slider-md="<?php echo esc_attr($ccc['md']);?>" data-slider-sm="<?php echo esc_attr($ccc['sm']);?>" data-slider-xs="<?php echo esc_attr($ccc['xs']);?>" data-slider-ss="<?php echo esc_attr($ccc['ss']);?>">
								<?php echo do_shortcode($content); ?>
            		</div>
				</div>
			</div>		
	<?php  $output = ob_get_contents();
		ob_end_clean();
	return $output;
}

//Shortcode for Custom Carousel Items
function kad_custom_carousel_item_shortcode_function( $atts, $content) {
	extract(shortcode_atts(array(
		'columns' => '',
), $atts));
	if(empty($columns)) {$columns = '4';}

ob_start(); 
		if ($columns == '2') {
			$itemsize = 'tcol-lg-6 tcol-md-6 tcol-sm-6 tcol-xs-12 tcol-ss-12'; 
		} else if ($columns == '1') {
			$itemsize = 'tcol-lg-12 tcol-md-12 tcol-sm-12 tcol-xs-12 tcol-ss-12';
		} else if ($columns == '3'){ 
			$itemsize = 'tcol-lg-4 tcol-md-4 tcol-sm-4 tcol-xs-6 tcol-ss-12';
		} else if ($columns == '6'){
			$itemsize = 'tcol-lg-2 tcol-md-2 tcol-sm-3 tcol-xs-4 tcol-ss-6';
		} else if ($columns == '5'){ 
			$itemsize = 'tcol-lg-25 tcol-md-25 tcol-sm-3 tcol-xs-4 tcol-ss-6';
		} else {
			$itemsize = 'tcol-lg-3 tcol-md-3 tcol-sm-4 tcol-xs-6 tcol-ss-12';
		} ?>
							<div class="<?php echo esc_attr($itemsize); ?> kad_customcarousel_item">
								<div class="carousel_item grid_item">
								<?php echo do_shortcode($content); ?>
								</div>
							</div>
	<?php  $output = ob_get_contents();
		ob_end_clean();
	return $output;
}