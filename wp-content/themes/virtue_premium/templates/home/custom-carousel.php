<?php 
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}
  global $virtue_premium; 

$cc_items = $virtue_premium['home_custom_carousel_items'];
if(!empty($virtue_premium['custom_carousel_title'])) {
  $cctitle = $virtue_premium['custom_carousel_title']; 
} else { 
  $cctitle = __('Featured News', 'virtue'); 
}
if(!empty($virtue_premium['home_custom_speed'])) {
  $hc_speed = $virtue_premium['home_custom_speed'].'000';
} else {
  $hc_speed = '9000';
}
if(isset($virtue_premium['home_custom_carousel_scroll']) && $virtue_premium['home_custom_carousel_scroll'] == 'all' ) {
  $hc_scroll = 'all';
} else {
  $hc_scroll = '1';
}
if(!empty($virtue_premium['home_custom_carousel_column'])) {
  $custom_column = $virtue_premium['home_custom_carousel_column'];
} else {
  $custom_column = 4;
} 
$cc = array();
if ($custom_column == '2') {
  $itemsize = 'tcol-lg-6 tcol-md-6 tcol-sm-6 tcol-xs-12 tcol-ss-12'; 
  $slidewidth = 560;  
  $cc['md'] = 2; 
  $cc['sm'] = 2; 
  $cc['xs'] = 1;
  $cc['ss'] = 1;
} else if ($custom_column == '3'){
  $itemsize = 'tcol-lg-4 tcol-md-4 tcol-sm-4 tcol-xs-6 tcol-ss-12'; 
  $slidewidth = 380; 
  $cc['md'] = 3; 
  $cc['sm'] = 3; 
  $cc['xs'] = 2;
  $cc['ss'] = 1; 
} else if ($custom_column == '6'){
  $itemsize = 'tcol-lg-2 tcol-md-2 tcol-sm-3 tcol-xs-4 tcol-ss-6'; 
  $slidewidth = 260; 
  $cc['md'] = 6; 
  $cc['sm'] = 4; 
  $cc['xs'] = 3;
  $cc['ss'] = 2;
} else if ($custom_column == '5'){
  $itemsize = 'tcol-lg-25 tcol-md-25 tcol-sm-3 tcol-xs-4 tcol-ss-6'; 
  $slidewidth = 260; 
  $cc['md'] = 5; 
  $cc['sm'] = 4; 
  $cc['xs'] = 3;
  $cc['ss'] = 2;
} else {
  $itemsize = 'tcol-lg-3 tcol-md-3 tcol-sm-4 tcol-xs-6 tcol-ss-12';
  $slidewidth = 300; 
  $cc['md'] = 4; 
  $cc['sm'] = 3; 
  $cc['xs'] = 2;
  $cc['ss'] = 1; 
}
$cc['xl'] = $cc['md'];
$cc['xxl'] = $cc['md'];
	if(isset($virtue_premium['home_custom_carousel_imageratio']) && $virtue_premium['home_custom_carousel_imageratio'] == '1' ) {
		$slideheight = null;
	} else {
		$slideheight = $slidewidth;
	}
$cc = apply_filters('kt_home_custom_carousel_columns', $cc);

?>
<div class="home-custom-carousel-wrap home-margin carousel_outerrim home-padding kad-animation" data-animation="fade-in" data-delay="0">
	<div class="clearfix">
		<h3 class="hometitle">
		    <?php echo $cctitle; ?>
		 </h3>
	</div>
  	<div class="custom-home-carousel">
		<div id="carouselcontainer-hcustom_carousel" class="rowtight">
    		<div id="home-custom-carousel" class="slick-slider custom_carousel kt-slickslider kt-content-carousel loading clearfix" data-slider-fade="false" data-slider-type="content-carousel" data-slider-anim-speed="400" data-slider-scroll="<?php echo esc_attr($hc_scroll);?>" data-slider-auto="true" data-slider-speed="<?php echo esc_attr($hc_speed);?>" data-slider-xxl="<?php echo esc_attr($cc['xxl']);?>" data-slider-xl="<?php echo esc_attr($cc['xl']);?>" data-slider-md="<?php echo esc_attr($cc['md']);?>" data-slider-sm="<?php echo esc_attr($cc['sm']);?>" data-slider-xs="<?php echo esc_attr($cc['xs']);?>" data-slider-ss="<?php echo esc_attr($cc['ss']);?>">
 			<?php 
        	if(!empty($cc_items)) {

          		foreach ($cc_items as $c_item) :

			        if(!empty($c_item['target']) && $c_item['target'] == 1) {
			          $target = '_blank';
			        } else {
			          $target = '_self';
			        }
			     
		            $img = virtue_get_image_array($slidewidth, $slideheight, false, null, null,$c_item['attachment_id'], false);
	            
		            echo '<div class="'.esc_attr($itemsize).' kad_customcarousel_item">';
		              	echo '<div class="grid_item custom_carousel_item product_item all postclass">';
		              		if(!empty($c_item['link'])){
		                		echo '<a href="'.esc_url($c_item['link']).'" class="custom_carousel_item_link" target="'.esc_attr($target).'">';
		                	}
		                	if(!empty($img['src'])) {
			 					echo '<div class="kt-intrinsic" style="padding-bottom:'.(($img['height']/$img['width']) * 100).'%;">';
			 						virtue_print_image_output( $img );
			 					echo '</div>';
		                	}
		                	if(!empty($c_item['link'])){
		                		echo '</a>';
		                	}
		                
		                	echo '<div class="custom_carousel_details">';
			                	if(!empty($c_item['link'])){
			                		echo '<a href="'.esc_url($c_item['link']).'" class="custom_carousel_content_link" target="'.esc_attr($target).'">';
			                	}
			                    	if (!empty($c_item['title'])){
			                    		echo '<h5>'.esc_html($c_item['title']).'</h5>'; 
			                    	}
			                   	if(!empty($c_item['link'])){
			                		echo '</a>';
			                	}
			                    echo '<div class="ccarousel_excerpt">';
				                    if(!empty($c_item['description'])){
				                    	echo $c_item['description'];
				                    }
				                echo '</div>';
		                	echo '</div>';
		            	echo '</div>';
		            echo '</div>';
            	endforeach; 
        	} ?>
      		</div>
  		</div>
	</div>
</div>