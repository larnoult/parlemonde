<?php 
global $post, $kt_portfolio_loop;

$postsummery = get_post_meta( $post->ID, '_kad_post_summery', true );
?>
		<div class="portfolio_item grid_item postclass kad-light-gallery kt_item_fade_in kad_portfolio_fade_in">
            <?php if ($postsummery == 'slider') {
            $image_gallery = get_post_meta( $post->ID, '_kad_image_gallery', true );
            if(!empty($image_gallery)) {
            	$attachments = array_filter( explode( ',', $image_gallery ) );
                if ($attachments) {
					if ( empty($kt_portfolio_loop[ 'slideheight' ] ) ) {
						$crop = false;
						$slide_height = $kt_portfolio_loop[ 'slidewidth' ];
					} else {
						$crop = true;
						$slide_height = $kt_portfolio_loop[ 'slideheight' ];
					}
                	$fimg = virtue_get_image_array($kt_portfolio_loop['slidewidth'], $slide_height, $crop, null, null, $attachments[0], true);
					echo '<div class="imghoverclass kt-intrinsic portfolio-loop-image" style="padding-bottom:'.(($fimg['height']/$fimg['width']) * 100).'%;">';
					echo '<div class="portfolio-loop-slider">';
					echo '<div id="kt_slider_'.esc_attr( $post->ID ).'" class="slick-slider kad-light-wp-gallery kt-slickslider loading kt-slider-same-image-ratio" data-slider-speed="7000" data-slider-anim-speed="400" data-slider-fade="true" data-slider-type="slider" data-slider-center-mode="true" data-slider-auto="true" data-slider-thumbid="#kt_slider_'.esc_attr( $post->ID ).'-thumbs" data-slider-arrows="true" data-slider-initdelay="'.esc_attr( rand( 1, 400 ) ).'" data-slider-thumbs-showing="'.esc_attr(ceil($fimg['width']/80)).'" style="max-width:100%">';
                	$attachments = array_filter( explode( ',', $image_gallery ) );
                    if ($attachments) {
                        foreach ($attachments as $attachment) {
                            $alt = get_post_meta($attachment, '_wp_attachment_image_alt', true);
                            $img_args = array(
								'width' 		=> $fimg['width'],
								'height' 		=> $fimg['height'],
								'crop'			=> true,
								'alt'			=> $alt,
								'id'			=> $attachment,
								'placeholder'	=> false,
							);
                            $img = virtue_get_processed_image_array( $img_args );
                            $item = get_post($attachment);
                            $img['extras'] = 'data-caption="'.esc_attr( wptexturize($item->post_excerpt) ).'" itemprop="contentUrl"';
                            echo '<div class="kt-slick-slide gallery_item">';
                                    echo '<a href="'.esc_url(get_the_permalink()).'" class="kt-slider-image-link">';
                                    echo '<div itemprop="image" itemscope itemtype="http://schema.org/ImageObject">';
                                    virtue_print_image_output( $img );
                                    echo '<meta itemprop="url" content="'.esc_url($img['src']).'">';
                                    echo '<meta itemprop="width" content="'.esc_attr($img['width']).'px">';
                                    echo '<meta itemprop="height" content="'.esc_attr($img['height']).'px">';
                                    echo '</div>';
									if($kt_portfolio_loop['lightbox'] == 'true') {
										echo '<a href="'.esc_url( $img[ 'full' ] ).'" class="kad_portfolio_lightbox_link" data-rel="lightbox">';
											echo '<i class="icon-search"></i>';
										echo '</a>';
									}
                                echo '</a>';
                            echo '</div>';
                        }
                    }                      
            echo '</div> <!--Image Slider-->';
            					echo '</div>';
            					echo '</div>';
            				}
            			}
            ?>
                
           	<?php } else if($postsummery == 'videolight') {
					if (has_post_thumbnail( $post->ID ) ) {
						$image_id = get_post_thumbnail_id( $post->ID );
						$video_string = get_post_meta( $post->ID, '_kad_post_video_url', true );
						$img_args = array(
							'width' 		=> $kt_portfolio_loop['slidewidth'],
							'height' 		=> $kt_portfolio_loop['slideheight'],
							'crop'			=> true,
							'class'			=> 'lightboxhover',
							'alt'			=> null,
							'id'			=> $image_id,
							'placeholder'	=> false,
						);
						$img = virtue_get_processed_image_array( $img_args );
						
						if ( ! empty( $video_string ) ) {
							$video_url = $video_string;
						} else {
							$video_url = $img[ 'full' ];
						}
                        ?>
							<div class="imghoverclass">
	                            <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" class="kt-intrinsic" style="padding-bottom:<?php echo ($img[ 'height' ]/$img[ 'width' ]) * 100;?>%;">
	                               <?php virtue_print_image_output( $img ); ?>
	                            </a> 
	                        </div>
                            <?php if($kt_portfolio_loop['lightbox'] == 'true') {?>
								<a href="<?php echo esc_url( $video_url ); ?>" class="kad_portfolio_lightbox_link pvideolight" title="<?php the_title_attribute();?>" data-rel="lightbox">
									<i class="icon-search"></i>
								</a>
							<?php }
                    } 
            } else {
					if (has_post_thumbnail( $post->ID ) ) {
						$image_id = get_post_thumbnail_id( $post->ID );
						$img_args = array(
							'width' 		=> $kt_portfolio_loop['slidewidth'],
							'height' 		=> $kt_portfolio_loop['slideheight'],
							'crop'			=> true,
							'class'			=> 'lightboxhover',
							'alt'			=> null,
							'id'			=> $image_id,
							'placeholder'	=> false,
						);
						$img = virtue_get_processed_image_array( $img_args );
		        		?>
							<div class="imghoverclass">
	                            <a href="<?php the_permalink();?>" title="<?php the_title_attribute(); ?>" class="kt-intrinsic" style="padding-bottom:<?php echo ($img[ 'height' ]/$img[ 'width' ]) * 100;?>%;">
	                            	<?php virtue_print_image_output( $img ); ?>
	                            </a> 
	                        </div>
                            <?php if($kt_portfolio_loop['lightbox'] == 'true') {?>
										<a href="<?php echo esc_url( $img[ 'full' ] ); ?>" class="kad_portfolio_lightbox_link" title="<?php the_title_attribute();?>" data-rel="lightbox">
											<i class="icon-search"></i>
										</a>
							<?php }?>
                        <?php $image = null; $thumbnailURL = null;?>
                    <?php } 
            } ?>
              	
              	<a href="<?php the_permalink() ?>" class="portfoliolink">
					<div class="piteminfo">   
                        <h5><?php the_title();?></h5>
                        <?php if($kt_portfolio_loop['showtypes'] == 'true') {
                        	$terms = get_the_terms( $post->ID, 'portfolio-type' ); 
                        	if ($terms && ! is_wp_error( $terms )) {?> 
                        		<p class="cportfoliotag">
                        			<?php $output = array(); foreach($terms as $term){ $output[] = $term->name;} echo implode(', ', $output); ?>
                        		</p>
                        <?php } 
                       	} 
                       	if($kt_portfolio_loop['showexcerpt'] == 'true') { 
                       		if ( apply_filters( 'virtue_portfolio_partial_excerpt', true ) ) {
								echo '<p class="p_excerpt">'.strip_tags( virtue_excerpt(16) ).'</p>'; 
							} else {
								echo '<div class="p_excerpt">'.get_the_excerpt().'</div>';
							} 
						} ?>
                    </div>
                </a>
        </div>

