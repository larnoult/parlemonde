<?php 
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}
global $virtue_premium;

        if(isset($virtue_premium['slider_size'])) {
          $slideheight = $virtue_premium['slider_size'];
        } else {
          $slideheight = 400;
        }
        if(isset($virtue_premium['slider_captions'])) {
          $captions = $virtue_premium['slider_captions'];
        } else {
          $captions = '';
        }
        if(isset($virtue_premium['home_slider'])) {
          $slides = $virtue_premium['home_slider'];
        } else {
          $slides = '';
        }
        if(isset($virtue_premium['trans_type'])) {
          $transtype = $virtue_premium['trans_type'];
        } else {
          $transtype = 'slide';
        }
        if(isset($virtue_premium['slider_transtime'])) {
          $transtime = $virtue_premium['slider_transtime'];
        } else {
          $transtime = '300';
        }
        if(isset($virtue_premium['slider_autoplay'])) {
          $autoplay = $virtue_premium['slider_autoplay'];
        } else {
          $autoplay = 'true';
        }
        if(isset($virtue_premium['slider_pausetime'])) {
          $pausetime = $virtue_premium['slider_pausetime'];
        } else {
          $pausetime = '7000';
        } ?>
<div class="sliderclass kad-desktop-slider clearfix kt-full-slider-container">        
  <div id="full_imageslider" class="kt-full-slider">
    <div class="flexslider kt-flexslider loading" data-flex-speed="<?php echo esc_attr($pausetime);?>" data-flex-anim-speed="<?php echo esc_attr($transtime);?>" data-flex-animation="<?php echo esc_attr($transtype); ?>" data-flex-auto="<?php echo esc_attr($autoplay);?>">
        <ul class="slides">
            <?php foreach ($slides as $slide) : 
                    if(!empty($slide['target']) && $slide['target'] == 1) {$target = '_blank';} else {$target = '_self';}
                    ?>
                      <li> 
                        <?php if($slide['link'] != '') echo '<a href="'.esc_url($slide['link']).'" target="'.esc_attr($target).'">'; ?>
                          <div class="kt-flex-fullslide" style="background-image:url(<?php echo esc_url($slide['url']); ?>); height:<?php echo esc_attr($slideheight);?>px;">
                              <?php if ($captions == '1') { ?> 
                                <div class="flex-caption" style="height:<?php echo esc_attr($slideheight);?>px;">
                                  <div class="flex-caption-case" style="height:<?php echo esc_attr($slideheight);?>px;">
                                  <?php if (!empty($slide['title'])) {
                                    echo '<div class="captiontitle headerfont">'.$slide['title'].'</div>'; 
                                  }
                                  if (!empty($slide['description'])) {
                                    echo '<div><div class="captiontext headerfont">'.$slide['description'].'</div></div>';
                                  } ?>
                                  </div>
                                </div> 
                              <?php } ?>
                              </div>
                        <?php if($slide['link'] != '') echo '</a>'; ?>
                      </li>
                  <?php endforeach; ?>
        </ul>
      </div> <!--Flex Slides-->
  </div><!--Container-->
</div><!--sliderclass-->