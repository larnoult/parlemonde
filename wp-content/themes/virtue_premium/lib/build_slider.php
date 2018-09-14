<?php 

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if(!function_exists('virtue_build_slider_home')) {
    function virtue_build_slider_home($slides = null, $width = null, $height = null, $class = null, $id = 'kt_slider_options', $type = 'slider', $captions = "false", $auto = 'true', $speed = '7000', $arrows = 'true', $fade = 'true', $fade_speed = '400') {
    	$crop = true;
    	$imgwidth = $width;
    	$stype = 'slider';
        if($type == 'thumb') {
            echo '<div class="thumb-slider-container" style="max-width:'.esc_attr($width).'px;">';
            $stype = 'thumb';
        } else if($type == 'different-ratio') {
        	$crop = false;
        	$imgwidth = null;
        }else if($type == 'carousel') {
        	$crop = false;
        	$imgwidth = null;
        	$width = 'none';
        	$stype = 'carousel';
        	$fade = 'false';
        	$class .= ' kt-image-carousel';
        }
        if(!empty($slides)) :
            echo '<div id="'.esc_attr($id).'" class="slick-slider kad-light-gallery kt-slickslider titleclass loading '.esc_attr($class).'" data-slider-speed="'.esc_attr($speed).'" data-slider-anim-speed="'.esc_attr($fade_speed).'" data-slider-fade="'.esc_attr($fade).'" data-slider-type="'.esc_attr($stype).'" data-slider-auto="'.esc_attr($auto).'" data-slider-thumbid="#'.esc_attr($id).'-thumbs" data-slider-arrows="'.esc_attr($arrows).'" data-slider-thumbs-showing="'.esc_attr(ceil($width/80)).'" style="max-width:'.esc_attr($width).'px;">';
                    foreach ($slides as $slide) {
                        $alt = get_post_meta($slide['attachment_id'], '_wp_attachment_image_alt', true);
                        $img = virtue_get_image_array($imgwidth, $height, $crop, null, $alt, $slide['attachment_id'], false);
                        echo '<div class="kt-slick-slide">';
                            if(!empty($slide['link'])) {
                            	if(!empty($slide['target']) && $slide['target'] == 1) {$target = '_blank';} else {$target = '_self';} 
                                echo '<a href="'.esc_url($slide['link']).'" target="'.esc_attr($target).'" class="kt-slider-image-link">';
                            }
                                echo '<div itemprop="image" itemscope itemtype="http://schema.org/ImageObject">';
                                echo '<img src="'.esc_url($img['src']).'" width="'.esc_attr($img['width']).'" height="'.esc_attr($img['height']).'" alt="'.esc_attr($img['alt']).'" itemprop="contentUrl" '.$img['srcset'].'/>';
                                echo '<meta itemprop="url" content="'.esc_url($img['src']).'">';
                                echo '<meta itemprop="width" content="'.esc_attr($img['width']).'px">';
                                echo '<meta itemprop="height" content="'.esc_attr($img['height']).'>px">';
                                echo '</div>';
                                if ($captions == 'true') {
                                	echo '<div class="flex-caption">';
                                  	if (!empty($slide['title'])) {
                                    	echo '<div class="captiontitle headerfont">'.$slide['title'].'</div>'; 
                                  	}
                                  	if (!empty($slide['description'])) {
                                    	echo '<div><div class="captiontext headerfont"><p>'.$slide['description'].'</p></div></div>';
                                  	}
                                  	echo '</div>';
                          		}
                            if(!empty($slide['link'])) {
                            	echo '</a>';
                        	}
                        echo '</div>';
                    }                      
            echo '</div> <!--Image Slider-->';
            if($type == 'thumb') {
                echo '<div id="'.esc_attr($id).'-thumbs" class="kt-slickslider-thumbs slick-slider">';
                        foreach ($slides as $slide) {
                            $alt = get_post_meta($slide['attachment_id'], '_wp_attachment_image_alt', true);
                            $img = virtue_get_image_array(80, 80, true, null, $alt, $slide['attachment_id'], false);
                            echo '<div class="kt-slick-thumb">';
                                    echo '<img src="'.esc_url($img['src']).'" width="'.esc_attr($img['width']).'" height="'.esc_attr($img['height']).'" alt="'.esc_attr($img['alt']).'" itemprop="image" '.$img['srcset'].'/>';
                                    echo '<div class="thumb-highlight"></div>';
                            echo '</div>';
                        }                      
                echo '</div> <!--Image Slider-->';
            echo '</div> <!--Thumb Container-->';
            }
        endif;
    }
}
if(!function_exists('virtue_build_slider_home_fullwidth')) {
    function virtue_build_slider_home_fullwidth($slides = null, $width = null, $height = null, $class = null, $id = 'kt_slider_options', $type = 'slider', $captions = "false", $auto = 'true', $speed = '7000', $arrows = 'true', $fade = 'true', $fade_speed = '400', $title_max = '70', $title_min = '40', $subtitle_max = '30', $subtitle_min = '20', $align = 'center') {
    	$stype = 'slider';
        if(!empty($slides)) :
            echo '<div id="'.esc_attr($id).'" class="slick-slider kad-home-full-slider titleclass kad-light-gallery kt-slickslider loading '.esc_attr($class).'" data-slider-speed="'.esc_attr($speed).'" data-slider-anim-speed="'.esc_attr($fade_speed).'" data-slider-fade="'.esc_attr($fade).'" data-slider-type="'.esc_attr($stype).'" data-slider-auto="'.esc_attr($auto).'" data-slider-thumbid="#'.esc_attr($id).'-thumbs" data-slider-arrows="'.esc_attr($arrows).'" data-slider-thumbs-showing="0">';
                    foreach ($slides as $slide) {
                        echo '<div class="kt-slick-slide">';
                            if(!empty($slide['link'])) {
                                echo '<a href="'.esc_url($slide['link']).'" class="kt-slider-image-link">';
                            }
                                echo '<div class="kt-basic-fullslide" style="background-image:url('.esc_url($slide['url']).'); height:'.esc_attr($height).'px;"></div>';
                                if ($captions == 'true') {
                                	echo '<div class="basic-caption"><div class="flex-caption-case" style="text-align:'.esc_attr($align).'">';
                                  	if (!empty($slide['title'])) {
                                    	echo '<div class="captiontitle entry-title h1class" data-max-size="'.esc_attr($title_max).'" data-min-size="'.esc_attr($title_min).'">'.$slide['title'].'</div>'; 
                                  	}
                                  	if (!empty($slide['description'])) {
                                    	echo '<div class="captiontext subtitle" data-max-size="'.esc_attr($subtitle_max).'" data-min-size="'.esc_attr($subtitle_min).'">'.$slide['description'].'</div>';
                                  	}
                                  	echo '</div></div>';
                          		}
                            if(!empty($slide['link'])) {
                            	echo '</a>';
                        	}
                        echo '</div>';
                    }                      
            echo '</div> <!--Image Slider-->';
        endif;
    }
}

if ( ! function_exists( 'virtue_build_slider' ) ) {
    function virtue_build_slider( $id = 'post', $images = null, $width = null, $height = null, $link ='image', $class = null, $type = 'slider', $captions = "false", $auto = 'true', $speed = '7000', $arrows = 'true', $fade = 'true', $fade_speed = '400', $delay = '0' ) {
    	if(empty($images)) {
    		global $post;
            $attach_args = array('order'=> 'ASC','post_type'=> 'attachment','post_parent'=> $post->ID,'post_mime_type' => 'image','post_status'=> null,'orderby'=> 'menu_order','numberposts'=> -1);
            $attachments_posts = get_posts($attach_args);
            $images = '';
            foreach ($attachments_posts as $val) {
                $images .= $val->ID.',';
            }

        }
        $thumb_overview_width = $width;
        if( empty( $thumb_overview_width ) ) {
        	$thumb_overview_width = 400;
        }
        if($type == 'thumb') {
            echo '<div class="thumb-slider-container" style="max-width:'.esc_attr($width).'px;">';
        }
        if(!empty($images)) :
        	$attachments = array_filter( explode( ',', $images ) );
        	$items = count($attachments);
        	if( 11 < $items ) {
        		$show_slides = 10;
        	} else {
        		$show_slides = $items - 1;
        	}
            echo '<div id="kt_slider_'.esc_attr($id).'" class="slick-slider kad-light-wp-gallery kt-slickslider loading '.esc_attr($class).'" data-slider-speed="'.esc_attr($speed).'" data-slider-anim-speed="'.esc_attr($fade_speed).'" data-slider-fade="'.esc_attr($fade).'" data-slider-type="'.esc_attr($type).'" data-slider-center-mode="true" data-slider-auto="'.esc_attr($auto).'" data-slider-thumbid="#kt_slider_'.esc_attr($id).'-thumbs" data-slider-arrows="'.esc_attr($arrows).'" data-slider-initdelay="'.esc_attr($delay).'" data-slides-to-show="'.esc_attr($show_slides).'" data-slider-thumbs-showing="'.esc_attr(ceil($thumb_overview_width/80)).'" style="max-width:'.esc_attr($width).'px;">';
                $attachments = array_filter( explode( ',', $images ) );
                    if ($attachments) {
                        foreach ($attachments as $attachment) {
                            $alt = get_post_meta($attachment, '_wp_attachment_image_alt', true);
                            $img = virtue_get_image_array($width, $height, true, null, $alt, $attachment, false);
                            $item = get_post($attachment);
                            $img['schema'] = true;
                            $img['lazy'] = false;
                            $img['extras']	= 'data-caption="'.esc_attr( wptexturize($item->post_excerpt) ).'"';
                            echo '<div class="kt-slick-slide gallery_item">';
                                if($link == "post") {
                                    echo '<a href="'.esc_url(get_the_permalink()).'" class="kt-slider-image-link">';
                                } else if($link == "attachment"){
                                    echo '<a href="'.esc_url(get_permalink($attachment)).'" class="kt-slider-image-link">';
                                } else {
                                    echo '<a href="'.esc_url($img['full']).'" data-rel="lightbox" class="kt-slider-image-link">';
                                }
                                	virtue_print_image_output( $img );
                                   
                                    if ($captions == 'true') {
                                    	if(trim($item->post_excerpt) ) {
							        		echo  '<div class="caption flex-caption">';
							        			echo  '<div class="captiontitle headerfont">' . wptexturize($item->post_excerpt) . '</div>';
							        		echo  '</div>';
							        	}
						      		}
                                echo '</a>';
                            echo '</div>';
                        }
                    }                      
            echo '</div> <!--Image Slider-->';
            if($type == 'thumb') {
	                echo '<div id="kt_slider_'.esc_attr($id).'-thumbs" class="kt-slickslider-thumbs slick-slider">';
	                $attachments = array_filter( explode( ',', $images ) );
	                    if ($attachments) {
	                        foreach ($attachments as $attachment) {
	                            $alt = get_post_meta($attachment, '_wp_attachment_image_alt', true);
	                            $img = virtue_get_image_array(80, 80, true, null, $alt, $attachment, false);
	                            echo '<div class="kt-slick-thumb">';
	                                    echo '<img src="'.esc_url($img['src']).'" width="'.esc_attr($img['width']).'" height="'.esc_attr($img['height']).'" alt="'.esc_attr($img['alt']).'" itemprop="image" '.$img['srcset'].'/>';
	                                    echo '<div class="thumb-highlight"></div>';
	                            echo '</div>';
	                        }
	                    }                       
	                echo '</div> <!--Image Slider-->';
                echo '</div> <!--Image Slider-->';
            }
        endif;
    }
}
if(!function_exists('virtue_build_post_carousel')) {
    function virtue_build_post_carousel($width = null, $height = 400, $class = null, $type = 'post', $cat = null, $items = 8, $orderby = 'date', $order = 'DESC', $offset = null, $auto = 'true', $speed = '7000', $arrows = 'true', $trans_speed = '400', $featured = null) {
    	$extraargs = array();
    	if($type == 'portfolio') {
    		$tax = 'portfolio-type';
    		$qtax = 'portfolio-type';
    	} elseif($type == 'product') {
    		if($featured == 'true'){
	    		$extraargs = array(
	    			'meta_key' 		=> '_featured',
					'meta_value' 	=> 'yes',
				);
    		}
    		$tax = 'product_cat';
    		$qtax = 'product_cat';
    	} else if($type == 'staff') {
    		$tax = 'staff-group';
    		$qtax = 'staff-group';
    	} else if($type == 'testimonal') {
    		$tax = 'testimonal-group';
    		$qtax = 'testimonal-group';
    	} else {
    		$tax = 'category';
    		$qtax = 'category_name';
    	}
    	if(!empty($cat)) {
    		$cat = get_term($cat, $tax);
    		$cat = $cat->slug;
    	}
    	$args = array(
			'orderby' 			=> $orderby,
			'order' 			=> $order,
			'post_type' 		=> $type,
			'offset' 			=> $offset,
			'post_status' 		=> 'publish',
			$qtax 				=> $cat,
			'posts_per_page' 	=> $items,
		);
		$args = array_merge($args, $extraargs);
            echo '<div class="slick-slider kad-light-gallery kt-slickslider loading '.esc_attr($class).'" data-slides-to-show="'.esc_attr($items - 1).'" data-slider-speed="'.esc_attr($speed).'" data-slider-anim-speed="'.esc_attr($trans_speed).'" data-slider-fade="false" data-slider-type="carousel" data-slider-auto="'.esc_attr($auto).'" data-slider-arrows="'.esc_attr($arrows).'">';
            		if(isset($wp_query)) {
            			$temp = $wp_query; 
            		} else {
            			$temp = null;
            		}
				  	$wp_query = null; 
				  	$wp_query = new WP_Query();
				  	$wp_query->query($args);
					if ( $wp_query ) : 
						while ( $wp_query->have_posts() ) : $wp_query->the_post(); 
							global $post;
                            $img = virtue_get_image_array($width , $height, true, null, null, null, true);
                            echo '<div class="kt-slick-slide blog_photo_item">';
					                echo '<div class="carousel-image-object" itemprop="image" itemscope itemtype="http://schema.org/ImageObject">';
					                    echo '<img src="'.esc_url($img['src']).'" width="'.esc_attr($img['width']).'" height="'.esc_attr($img['height']).'" '.$img['srcset'].' class="'.esc_attr($img['class']).'" itemprop="contentUrl" alt="'.esc_attr($img['alt']).'">';
					                    echo '<meta itemprop="url" content="'.esc_url($img['src']).'">';
					                    echo '<meta itemprop="width" content="'.esc_attr($img['width']).'px">';
					                    echo '<meta itemprop="height" content="'.esc_attr($img['height']).'px">';
					                echo '</div>';
				           		echo '<div class="photo-postcontent">';
				           			echo '<div class="photo-post-bg"></div>'; 
					            	echo '<div class="photo-postcontent-inner">';
						            	echo '<header>';
	                                    	echo '<h4 class="entry-title">'.get_the_title().'</h4>'; 
	                                    echo '</header>';
                                    	echo '<div class="kt-post-photo-added-content">';
		                                  	echo '<div class="kt_post_category">';
		                                  	if($type == 'product') {
		                                  		if(function_exists('woocommerce_template_loop_price')) {
		                                  			woocommerce_template_loop_price();
		                                  		}
		                                  	} else if($type != 'post') {
		                                  		the_terms($post->ID, $tax, '', ' | ', '');
		                                  	} else {
												the_category(' | ');
		                                  	}
											echo '</div>';
										echo '</div>';
                                  	echo '</div></div>';
                                echo '<a href="'.get_the_permalink().'" class="photo-post-link"></a>';
                            echo '</div>';
						endwhile; else: 
						echo '<div class="error-not-found">'.__('Sorry, no entries found.', 'virtue').'</li>';
						
					endif; 
                    $wp_query = null; 
                    $wp_query = $temp;  // Reset
                    wp_reset_query(); 
            echo '</div> <!--Post Carousel-->';
    }
}
