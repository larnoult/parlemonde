<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $virtue_premium; 

if(!empty($virtue_premium['img_menu_height'])) {
	$height = $virtue_premium['img_menu_height'];
} else {
	$height = 110;
}
if(!empty($virtue_premium['img_menu_height_setting'])) {
	$height_setting = $virtue_premium['img_menu_height_setting'];
} else {
	$height_setting = 'normal';
} 
$slides = $virtue_premium['home_image_menu'];

if(!empty($virtue_premium['home_image_menu_column'])) {
	$columns = $virtue_premium['home_image_menu_column'];
} else {
	$columns = 3;
}
	?>
<div class="home-padding home-margin">
	<div class="rowtight homepromo">
	<?php $counter = 1;
	if(!empty($slides)) {
		foreach ($slides as $slide) :
			if(!empty($slide['target']) && $slide['target'] == 1) {
				$target = '_blank';
			} else {
				$target = '_self';
			}
			$class = 'homeitemcount'.esc_attr($counter);
			echo virtue_image_menu_output_builder( $slide['attachment_id'], $height_setting, $height, $slide['link'], $columns, $target, $slide['title'], $slide['description'], $class, $slide['url'], null, $counter*150);
				      
			$counter ++;

		endforeach;
	} ?>
	</div> <!--homepromo -->
</div> <!--home padding -->