<div class="sliderclass kad-desktop-slider kt-full-slider-container">
  <?php  global $virtue; 
        if(isset($virtue['slider_size'])) {
          $slideheight = $virtue['slider_size'];
        } else {
          $slideheight = 400;
        }
        if(isset($virtue['slider_captions'])) {
          $captions = $virtue['slider_captions'];
        } else {
          $captions = '';
        }
        if(isset($virtue['home_slider'])) {
          $slides = $virtue['home_slider'];
        } else {
          $slides = '';
        }
        if(isset($virtue['trans_type'])) {
          $transtype = $virtue['trans_type'];
        } else {
          $transtype = 'slide';
        }
        if(isset($virtue['slider_transtime'])) {
          $transtime = $virtue['slider_transtime'];
        } else {
          $transtime = '300';
        }
        if(isset($virtue['slider_autoplay'])) {
          $autoplay = $virtue['slider_autoplay'];
        } else {
          $autoplay = 'true';
        }
        if(isset($virtue['slider_pausetime'])) {
          $pausetime = $virtue['slider_pausetime'];
        } else {
          $pausetime = '7000';
        } ?>
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
                                    echo '<div class="captiontitle headerfont">'.esc_html( $slide['title'] ).'</div>'; 
                                  }
                								  if (!empty($slide['description'])) {
                                    echo '<div><div class="captiontext headerfont">'.wp_kses_post( $slide['description'] ).'</div></div>';
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