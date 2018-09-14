<?php

class KadenceSliderPro_Output {

	// Shortcode
	public static function shortcode($atts) {
		extract(shortcode_atts(array(
		'id' => ''
		), $atts));

		if(empty($id)) {
			return '<p class="error">' . __( 'Please specify a slider ID', 'kadence_slider' ) . '</p>';
		} else {
			return KadenceSliderPro_Output::output($id);
		}
	}

	public static function addShortcode() {
		add_shortcode('kadence_slider_pro', array( __CLASS__, 'shortcode'));
	}

	public static function output( $id ) {
			global $wpdb, $kadence_slider;
			$slider = $wpdb->get_row('SELECT * FROM ' . $wpdb->prefix . 'ksp_sliders WHERE id = \'' . $id . '\'');

			if(! $slider) {
				return '<p class="error">' . __( 'Could not find slider', 'kadence_slider' ) . '</p>';
			}
			$slider_id = $slider->id;
			$slides = $wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . 'ksp_slides WHERE slider_parent = ' . $slider_id . ' ORDER BY position');

				$max_height = $slider->maxHeight;
				$min_height = $slider->minHeight;
				$max_width = $slider->maxWidth;
				$fullwidth = $slider->fullWidth;
				$respect_ratio = $slider->responsive;
				$fullheight = $slider->fullHeight;
				$full_offset = $slider->full_offset;
				$pause_time = $slider->pauseTime;
				$auto_play = $slider->autoPlay;
				$slider_parallax = $slider->enableParallax;
				$hidecontrols = $slider->singleSlide;
				$pauseonhover = $slider->pauseonHover;
				$data_height = $max_height;
				$grid_width = $max_width;

				$slidecount = count($slides);
				if(!empty($pause_time)) {$pause_data = $pause_time;} else{$pause_data = '9000';} 
				if($auto_play == '1') {$auto_play_data = 'true';} else{$auto_play_data = 'false';} 
				if(isset($pauseonhover) && $pauseonhover == 1)  {$pause_on_hover = 'true';} else{$pause_on_hover = 'false';} 
				if($fullwidth) {$max_width = 'none';} else {$max_width = $max_width.'px';}
				if($fullheight) {$max_height = '600px'; $data_height_type = "full"; } else {$max_height = $max_height.'px'; $data_height_type = "normal";}
				if($slider_parallax) {$slider_parallax_class = 'kad-slider-parallax';} else {$slider_parallax_class = '';}
				$ratioclass = "";
				// ratio
				if(isset($respect_ratio) && $respect_ratio == 1) {
		 			$data_height_type = 'ratio';
		 			$ratioclass = "kt-ratio-slider";
		 			$min_height = '0';
		 		}

		            ob_start(); ?>
					  	<div class="ksp-slider-wrapper <?php if($slidecount == 1) {echo 'kt_slider_single_slide';}?> <?php if($hidecontrols == 1) {echo 'kt_slider_hide_controls';}?> kad-slider-<?php echo esc_attr($id);?> <?php echo esc_attr($ratioclass);?> <?php echo esc_attr($slider_parallax_class);?>" data-ktslider-id="<?php echo esc_attr($id);?>" data-ktslider-auto-play="<?php echo esc_attr($auto_play_data);?>" data-ktslider-pause-time="<?php echo esc_attr($pause_data);?>" data-ktslider-count="<?php echo esc_attr($slidecount);?>" data-ktslider-height="<?php echo esc_attr($data_height);?>" data-ktslider-pause-hover="<?php echo esc_attr($pause_on_hover);?>" data-ktslider-height-type="<?php echo esc_attr($data_height_type);?>" data-ktslider-height-offset="<?php echo esc_attr($full_offset);?>" data-ktslider-width="<?php echo esc_attr($slider->maxWidth);?>" style="max-width:<?php echo esc_attr($max_width);?>; margin-left: auto; margin-right:auto;">
					    	<div id="kad-slider-<?php echo esc_attr($id);?>" class="kad-slider kad-loading" style="margin-left: auto; margin-right:auto; height:<?php echo esc_attr($max_height);?>; min-height:<?php echo esc_attr($min_height);?>px">
					        	<ul class="kad-slider-canvas seq-canvas" style="height:<?php echo esc_attr($max_height);?>; min-height:<?php echo esc_attr($min_height);?>px">
					        	<?php $slidenumber = 1;
					        	if(!empty($slides)) {
					            	foreach ($slides as $slide) :
					            		$video = 'off';
					            		$video_id = '0';
					            		$video_mp4 = '';
					            		$video_webm = '';
					            		$video_poster = '';
					            		$video_sound = '0';
					            		$video_loop = '0';
					            		$video_start = '0';
					            		$video_ratio = '1.777777778';
					            		$video_playpause = '0';
					            		$video_css = '';
					            		if(isset($slide->background_type_video) && 'youtube' == $slide->background_type_video){
					            			$video = 'youtube';
					            			$video_css = 'video_slide';
					            			if(isset($slide->background_type_video_youtube)){
					            				$video_id = $slide->background_type_video_youtube;
					            			}
					            			if(isset($slide->background_type_video_start)) {
					            				$video_start = $slide->background_type_video_start;
					            			}
					            			if(isset($slide->background_type_video_ratio)) {
					            				if ( '3 / 2' == $slide->background_type_video_ratio ) {
					            					$video_ratio = '1.5';
					            				} else if ( '4 / 3' == $slide->background_type_video_ratio ) {
					            					$video_ratio = '1.333333333';
					            				} else {
					            					$video_ratio = '1.777777778';
					            				}
					            			}
					            			if(isset($slide->background_type_video_mute) && 1 == $slide->background_type_video_mute){
					            				$video_sound = '1';
					            			}
					            			if(isset($slide->background_type_video_loop) && 1 == $slide->background_type_video_loop){
					            				$video_loop = '1';
					            			}
					            			if(isset($slide->background_type_video_playpause) && 1 == $slide->background_type_video_playpause){
					            				$video_playpause = '1';
					            			}
					            		} else if(isset($slide->background_type_video) && 'html5' == $slide->background_type_video){
					            			$video = 'html5';
					            			$video_css = 'video_slide';
					            			if(isset($slide->background_type_video_mp4)){
					            				$video_mp4 = $slide->background_type_video_mp4;
					            			}
					            			if(isset($slide->background_type_video_webm)){
					            				$video_webm = $slide->background_type_video_webm;
					            			}
					            			if(isset($slide->background_type_image) && 'none' != $slide->background_type_image){
					            				$video_poster = $slide->background_type_image;
					            			}
					            			if(isset($slide->background_type_video_mute) && 1 == $slide->background_type_video_mute){
					            				$video_sound = '1';
					            			}
					            			if(isset($slide->background_type_video_loop) && 1 == $slide->background_type_video_loop){
					            				$video_loop = '1';
					            			}
					            			if(isset($slide->background_type_video_playpause) && 1 == $slide->background_type_video_playpause){
					            				$video_playpause = '1';
					            			}
					            		}
					            	// Aspect Ratio
					            	if(isset($respect_ratio) && $respect_ratio == 1) {
						            	if($slide->background_type_image == 'undefined' || $slide->background_type_image == 'none'){
						            		$slide_image = 'none';
						            	} else {
						            		$slide_image_id = ksp_get_image_id_by_link($slide->background_type_image);
						           			$slide_image = wp_get_attachment_image_src($slide_image_id, 'full');
						           			$alt = get_post_meta($slide_image_id, '_wp_attachment_image_alt', true);
						           		}
						           		$background_type_image = '';
						           		$background_repeat = '';
						           		$background_propriety_position = '';
						           		$background_propriety_size = '';
						           		if(isset($slide->background_type_color)) {$background_type_color = 'background-color:'.$slide->background_type_color.';';} else {$background_type_color = '';}
						           	} else {
						           		if(isset($slide->background_repeat)) {$background_repeat = 'background-repeat:'.$slide->background_repeat.';';} else {$background_repeat = '';}
						           		if( isset( $slide->background_propriety_position ) ) {
						           			$background_propriety_position = 'background-position:'.$slide->background_propriety_position.';';
						           		} else {
						           			$background_propriety_position = '';
						           		}
						           		if(isset($slide->background_type_color)) {$background_type_color = 'background-color:'.$slide->background_type_color.';';} else {$background_type_color = '';}
						           		if ( isset( $slide->background_propriety_size ) && 1 != $slider_parallax ) {
						           			$background_propriety_size = 'background-size:'.$slide->background_propriety_size.';';
						           		} else {
						           			$background_propriety_size = '';
						           		}
						           		$background_type_image = $slide->background_type_image == "undefined" || $slide->background_type_image == "none" ? "background-image:none;" : "background-image:url(" . esc_attr($slide->background_type_image) . ");";

						           	}
					            	 	?>
					                      <li> 
					                      	<div class="kad-slide kad-slide-<?php echo esc_attr($slidenumber);?> <?php echo esc_attr($video_css);?>" style="<?php echo esc_attr($background_type_color); ?> <?php echo esc_attr( $background_type_image ); ?> <?php echo esc_attr($background_repeat); ?> <?php echo esc_attr($background_propriety_size); ?> <?php echo esc_attr($background_propriety_position);?>" data-video-slide="<?php echo esc_attr($video);?>" data-video-id="<?php echo esc_attr($video_id);?>" data-video-mp4="<?php echo esc_attr($video_mp4);?>" data-video-webm="<?php echo esc_attr($video_webm);?>" data-video-poster="<?php echo esc_attr($video_poster);?>" data-video-start="<?php echo esc_attr($video_start);?>" data-video-ratio="<?php echo esc_attr($video_ratio);?>" data-video-sound="<?php echo esc_attr($video_sound);?>" data-video-loop="<?php echo esc_attr($video_loop);?>" data-video-playpause="<?php echo esc_attr($video_playpause);?>">
					                			<?php if(isset($respect_ratio) && $respect_ratio == 1) {
					                            echo '<img src="'.esc_url($slide->background_type_image).'" alt="'.esc_attr($alt).'" width="'.esc_attr($slide_image['1']).'"  height="'.esc_attr($slide_image['2']).'" class="kt-ratio-img">';
							                	} 
							                	?>
					                              
					                		<?php 

					                			if('youtube' == $video) {
					                				echo '<div class="ksp-background-video-overlay"></div>';
					                				echo '<div class="ksp-background-video-buttons-wrapper ksp-background-video-buttons-youtube">';
														echo '<a class="ksp-background-video-play ksp-toggle-play" style="display: none;">Play</a>';
                                						echo '<a class="ksp-background-video-pause ksp-toggle-play" style="display: none;">Pause</a>';
														echo '<a class="ksp-background-video-unmute ksp-toggle-sound" style="display: none;">Unmute</a>';
                                						echo '<a class="ksp-background-video-mute ksp-toggle-sound" style="display: none;">Mute</a>';
													echo '</div>';
					                			} else if('html5' == $video) {
					                				echo '<div class="ksp-background-video-overlay"></div>';
					                				echo '<div class="ksp-background-video-buttons-wrapper ksp-background-video-buttons-html5">';
														echo '<a class="ksp-background-video-play ksp-toggle-play" style="display: none;">Play</a>';
                                						echo '<a class="ksp-background-video-pause ksp-toggle-play" style="display: none;">Pause</a>';
														echo '<a class="ksp-background-video-unmute ksp-toggle-sound" style="display: none;">Unmute</a>';
                                						echo '<a class="ksp-background-video-mute ksp-toggle-sound" style="display: none;">Mute</a>';
													echo '</div>';
					                			} 
					                			if(isset($slide->background_link) && !empty($slide->background_link)) {
					                					$target = $slide->background_link_new_tab == 1 ? 'target="_blank"' : '';
					                					echo '<a href="'.esc_url($slide->background_link).'" class="ksp-background-link-overlay" '.esc_attr($target).'></a>';
					                			}?>
					                           	<div class="kad-slider-layers-case" style="max-width:<?php echo esc_attr($grid_width);?>px">
					                              <?php 
					                              	if(isset($slide->background_link) && !empty($slide->background_link)) {
					                              		$target = $slide->background_link_new_tab == 1 ? 'target="_blank"' : '';
					                					echo '<a href="'.esc_url($slide->background_link).'" class="ksp-background-link-overlay" '.esc_attr($target).'></a>';
					                				}
					                				$slide_parent = $slide->position;
													$layers = $wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . 'ksp_layers WHERE slider_parent = ' . $slider_id . ' AND slide_parent = ' . $slide_parent);	
													$output = '';
													$l = 1;
													foreach($layers as $layer) {
														// Layer Wrapper 
														$output .= '<div class="ksp-layer-wrap" style="top:' . esc_attr($layer->data_y) . 'px; left: ' . esc_attr($layer->data_x) . 'px;"';
																	$output .= ' data-top="' . esc_attr($layer->data_y) . '"' . "\n" .
																		'data-left="' . esc_attr($layer->data_x) . '"' . "\n" .
																		'>';
														
														// Type of layer
														switch($layer->type) {

															case 'text':
																// if Link 
																if(!empty($layer->link)) {
																	$target = $layer->link_new_tab == 1 ? 'target="_blank"' : '';
																	$output .= '<a href="' . stripslashes($layer->link) . '"' . $target . ' class="ksp-layer-link">';
																}
																if(isset($kadence_slider[$layer->font]['font-style'])) {
																	$fontstyleoutput = $kadence_slider[$layer->font]['font-style'];
																} else {
																	$fontstyleoutput = '';
																}
																if(!empty($layer->text_shadow)) {
																	$shadow = 'kt-t-shadow-'.$layer->text_shadow;
																} else {
																	$shadow = 'kt-t-shadow-none';
																}
																$output .= '<div class="ksp-layer ksp-text-layer delay_'.esc_attr($layer->data_delay).'ms animated_'.esc_attr($layer->data_ease).'ms ksp-layer-'.esc_attr($l).' '.esc_attr($layer->data_out).' '.esc_attr($shadow).'" ';
																	$output .= 'style="
																	z-index: ' . esc_attr($layer->z_index) . ';
																	font-size: ' . esc_attr($layer->font_size) . 'px;
																	line-height: ' . esc_attr($layer->line_height) . 'px;
																	font-family: ' . esc_attr($kadence_slider[$layer->font]['font-family']) . ';
																	font-weight: ' . esc_attr($kadence_slider[$layer->font]['font-weight']) . ';
																	font-style: ' . esc_attr($fontstyleoutput) . ';
																	color: ' . esc_attr($layer->font_color). ';
																	letter-spacing: ' . esc_attr($layer->letter_spacing). 'px;
																	
																	" ';

																	$output .= 'data-delay="' . esc_attr($layer->data_delay) . '"' . "\n" .
																	'data-ease="' . esc_attr($layer->data_ease) . '"' . "\n" .
																	'data-in="' . esc_attr($layer->data_in) . '"' . "\n" .
																	'data-out="' . esc_attr($layer->data_out) . '"' . "\n" .
																	'data-top="' . esc_attr($layer->data_y) . '"' . "\n" .
																	'data-left="' . esc_attr($layer->data_x) . '"' . "\n" .
																	'data-font-size="' . esc_attr($layer->font_size) . '"' . "\n" .
																	'data-line-height="' . esc_attr($layer->line_height) . '"' . "\n" .
																	'data-letter-spacing="' . esc_attr($layer->letter_spacing). '"' . "\n";
																$output .= '>'.wp_kses_post($layer->inner_html).'</div>' . "\n";
																// If link
																if(!empty($layer->link)) {
																	$output .='</a>';
																}
															break;
															case 'button':
																// if Link 
																if(!empty($layer->link)) {
																	$target = $layer->link_new_tab == 1 ? 'target="_blank"' : '';
																	$output .= '<a href="' . stripslashes($layer->link) . '"' . $target;
																} else {
																	$output .= '<a href="#"';
																}
																$output .= ' class="ksp-layer ksp-btn-layer delay_'.$layer->data_delay.'ms animated_'.$layer->data_ease.'ms ksp-layer-'.$l.' '.$layer->data_out.'" ';
																$output .= 'style="
																	z-index: ' . $layer->z_index . '; font-size: ' . $layer->font_size . 'px; line-height: ' . $layer->line_height . 'px; font-family: ' . $kadence_slider[$layer->font]['font-family'] . '; font-weight: ' . $kadence_slider[$layer->font]['font-weight'] . ';';
																	if(isset($kadence_slider[$layer->font]['font-style']) && !empty($kadence_slider[$layer->font]['font-style']) ) {
																		$output .= 'font-style: ' . $kadence_slider[$layer->font]['font-style'] . ';';
																	}
																	$output .= 'padding-left: ' . esc_attr($layer->padding) . 'px; padding-right: ' . esc_attr($layer->padding) . 'px; letter-spacing: ' . esc_attr($layer->letter_spacing). 'px;background: ' . esc_attr($layer->background_color). '; color: ' . esc_attr($layer->font_color). '; border-style: solid; border-width: ' . esc_attr($layer->border_width). 'px; border-color: ' . esc_attr($layer->border_color). '; border-radius: ' . esc_attr($layer->border_radius). 'px;" ';
																	$output .= 'data-delay="' . esc_attr($layer->data_delay) . '"' . "\n" .
																	'data-ease="' . esc_attr($layer->data_ease) . '"' . "\n" .
																	'data-in="' . esc_attr($layer->data_in) . '"' . "\n" .
																	'data-out="' . esc_attr($layer->data_out) . '"' . "\n" .
																	'data-top="' . esc_attr($layer->data_y) . '"' . "\n" .
																	'data-left="' . esc_attr($layer->data_x) . '"' . "\n" .
																	'data-font-size="' . esc_attr($layer->font_size) . '"' . "\n" .
																	'data-padding="' . esc_attr($layer->padding) . '"' . "\n" .
																	'data-color="' . esc_attr($layer->font_color) . '"' . "\n" .
																	'data-border-color="' . esc_attr($layer->border_color) . '"' . "\n" .
																	'data-background-color="' . esc_attr($layer->background_color) . '"' . "\n" .
																	'data-hcolor="' . esc_attr($layer->font_hover_color) . '"' . "\n" .
																	'data-hborder-color="' . esc_attr($layer->border_hover_color) . '"' . "\n" .
																	'data-hbackground-color="' . esc_attr($layer->background_hover_color) . '"' . "\n" .
																	'data-letter-spacing="' . esc_attr($layer->letter_spacing). '"' . "\n" .
																	'data-border-width="' . esc_attr($layer->border_width) . '"' . "\n" .
																	'data-line-height="' . esc_attr($layer->line_height) . '"' . "\n";

																$output .= '>';
																$output .= wp_kses_post($layer->inner_html);
																$output .='</a>';

															break;
															case 'image':
																// if Link 
																// if Link 
																if(!empty($layer->link)) {
																	$target = $layer->link_new_tab == 1 ? 'target="_blank"' : '';
																	$output .= '<a href="' . esc_url($layer->link) . '"' . $target . ' class="ksp-layer-link">';
																}
																$output .= '<img class="ksp-layer ksp-image-layer delay_'.esc_attr($layer->data_delay).'ms animated_'.esc_attr($layer->data_ease).'ms ksp-layer-'.esc_attr($l).' '.esc_attr($layer->data_out).'" ';
																$output .= 'style="
																	z-index: ' . esc_attr($layer->z_index) . ';	
																	width: ' . esc_attr($layer->width) . 'px;
																	height: ' . esc_attr($layer->height) . 'px;															
																	" ';
																	$output .= 'data-delay="' . esc_attr($layer->data_delay) . '"' . "\n" .
																	'data-ease="' . esc_attr($layer->data_ease) . '"' . "\n" .
																	'data-in="' . esc_attr($layer->data_in) . '"' . "\n" .
																	'data-out="' . esc_attr($layer->data_out) . '"' . "\n" .
																	'data-top="' . esc_attr($layer->data_y) . '"' . "\n" .
																	'data-left="' . esc_attr($layer->data_x) . '"' . "\n".
																	'data-width="' . esc_attr($layer->width) . '"' . "\n" .
																	'data-height="' . esc_attr($layer->height) . '"' . "\n" ;

																$output .= 'src="'.esc_url($layer->image_src).'" alt="'.esc_attr($layer->image_alt).'">';
																if(!empty($layer->link)) {
																	$output .='</a>';
																}
															break;
														}
														
														$output .='</div>';
														$l ++;
													}
													echo $output;
													
													?>
													</div>
					                            </div>
					                      	</li> 
					                      	<?php $slidenumber ++; ?>
					            <?php endforeach; ?>
					        	</ul>
					        	<ul class="kad-slider-pagination ksp-pag-<?php echo esc_attr($id);?>">
					        		<?php foreach ($slides as $slide) : ?>
								    <li class="kad-slider-dot"></li>
								    <?php endforeach; ?>
								</ul>
					        	<a class="kad-slider-next kad-slider-navigate ksp-next-<?php echo esc_attr($id);?>"></a>
					        	<a class="kad-slider-prev kad-slider-navigate ksp-prev-<?php echo esc_attr($id);?>"></a>
					        	<?php } ?>
					      </div> <!--kad-slides-->
					  </div> <!--kad-slider-->
		            		
			<?php  $output = ob_get_contents();
				ob_end_clean();
			return $output;
		}
	}


