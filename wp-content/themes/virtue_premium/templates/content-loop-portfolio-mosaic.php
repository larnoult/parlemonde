<?php 
global $post, $kt_portfolio_loop, $kt_portfolio_loop_mosaic;
$postsummery = get_post_meta( $post->ID, '_kad_post_summery', true );
$terms = get_the_terms( $post->ID, 'portfolio-type' );
						if ( $terms && ! is_wp_error( $terms ) ) : 
							$links = array();
								foreach ( $terms as $term ) { $links[] = $term->slug;}
							$links = preg_replace("/[^a-zA-Z 0-9]+/", " ", $links);
							$links = str_replace(' ', '-', $links);	
							$tax = join( " ", $links );		
						else :	
							$tax = '';	
						endif;

	if($kt_portfolio_loop_mosaic['item_count'] == 31){$kt_portfolio_loop_mosaic['item_count'] = 0;}
    if(in_array($kt_portfolio_loop_mosaic['item_count'], explode(',', $kt_portfolio_loop_mosaic['wide_string']))){
      $mosaic_xsize = $kt_portfolio_loop_mosaic['ximgsize_wide'];
      $mosaic_ysize = $kt_portfolio_loop_mosaic['yimgsize_wide'];
      $mosaic_itemsize = $kt_portfolio_loop_mosaic['itemsize_wide'];
    } else if(in_array($kt_portfolio_loop_mosaic['item_count'], explode(',', $kt_portfolio_loop_mosaic['large_string']))){
      $mosaic_xsize = $kt_portfolio_loop_mosaic['ximgsize_large'];
      $mosaic_ysize = $kt_portfolio_loop_mosaic['yimgsize_large'];
      $mosaic_itemsize = $kt_portfolio_loop_mosaic['itemsize_large'];
    } elseif(in_array($kt_portfolio_loop_mosaic['item_count'], explode(',', $kt_portfolio_loop_mosaic['tall_string']))){
      $mosaic_xsize = $kt_portfolio_loop_mosaic['ximgsize_tall'];
      $mosaic_ysize = $kt_portfolio_loop_mosaic['yimgsize_tall'];
      $mosaic_itemsize = $kt_portfolio_loop_mosaic['itemsize_tall'];
    } else {
      $mosaic_xsize = $kt_portfolio_loop_mosaic['ximgsize_normal'];
      $mosaic_ysize = $kt_portfolio_loop_mosaic['yimgsize_normal'];
      $mosaic_itemsize = $kt_portfolio_loop_mosaic['itemsize_normal'];
    }
?>		<div class="<?php echo esc_attr($mosaic_itemsize); ?> <?php echo strtolower($tax); ?> all p-item">
		<div class="portfolio_item grid_item postclass g_mosiac_item kad-light-gallery kt_item_fade_in kad_portfolio_fade_in">
            <?php if ($postsummery == 'slider') { ?>
                <div class="flexslider mosaic_item_wrap kt-flexslider loading imghoverclass clearfix" data-flex-speed="7000" data-flex-initdelay="<?php echo (rand(10,2000));?>" data-flex-anim-speed="400" data-flex-animation="fade" data-flex-auto="true">
                    <ul class="slides kad-light-gallery">
                        <?php $image_gallery = get_post_meta( $post->ID, '_kad_image_gallery', true );
	                        if(!empty($image_gallery)) {
	                        	$i = 1;
	                        	$attachments = array_filter( explode( ',', $image_gallery ) );
	                            if ($attachments) {
	                            	foreach ($attachments as $attachment) {
		                                $attachment_url = wp_get_attachment_url($attachment , 'full');
		                                if($i == 1){$firstattachment = $attachment_url;}
		                                $image = aq_resize($attachment_url, $mosaic_xsize,$mosaic_ysize, true, false);
		                                if(empty($image[0])) {$image = array($attachment_url,$mosaic_xsize,$mosaic_ysize);} 
											// Get srcset
							        		$img_srcset = kt_get_srcset( $image[1], $image[2], $attachment_url, $attachment);
							        		if(!empty($img_srcset) ) {
									        	$img_srcset_output = 'srcset="'.esc_attr($img_srcset).'" sizes="(max-width: '.esc_attr($image[1]).'px) 100vw, '.esc_attr($image[1]).'px"';
									        } else {
									        	$img_srcset_output = '';
									        }?>
	                                  	<li>
	                                  			<img src="<?php echo esc_url($image[0]); ?>" alt="<?php the_title_attribute(); ?>" width="<?php echo esc_attr($image[1]);?>" height="<?php echo esc_attr($image[2]);?>" <?php echo $img_srcset_output;?> class="" />
	                                  			<?php if($kt_portfolio_loop['lightbox'] == 'true') {
	                                  				if($i == 1){
	                                  					} else {?>
												<a href="<?php echo esc_url($attachment_url); ?>" class="kad_portfolio_lightbox_link" title="<?php the_title_attribute();?>" data-rel="lightbox">
													<i class="icon-search"></i>
												</a>
											<?php } 
													}
											$i++;?>
	                                  </li>
	                                <?php }
	                            }
	                        }?>                            
					</ul>
								<?php if($kt_portfolio_loop['lightbox'] == 'true') {?>
												<a href="<?php echo esc_url($firstattachment); ?>" class="kad_portfolio_lightbox_link" title="<?php the_title_attribute();?>" data-rel="lightbox">
													<i class="icon-search"></i>
												</a>
											<?php }?>
              	</div> <!--Flex Slides-->
											
              	
           	<?php } else if($postsummery == 'videolight') {
					if (has_post_thumbnail( $post->ID ) ) {
						$image_id = get_post_thumbnail_id( $post->ID );
						$image_url = wp_get_attachment_image_src($image_id, 'full' ); 
						$thumbnailURL = $image_url[0]; 
						$image = aq_resize($thumbnailURL, $mosaic_xsize, $mosaic_ysize, true, false);
						$video_string = get_post_meta( $post->ID, '_kad_post_video_url', true );
                  		if(!empty($video_string)) {$video_url = $video_string;} else {$video_url = $thumbnailURL;}
						if(empty($image[0])) {$image = array($thumbnailURL,$mosaic_xsize,$mosaic_ysize);} 
						// Get srcset
		        		$img_srcset = kt_get_srcset( $image[1], $image[2], $thumbnailURL, $image_id);
		        		if(!empty($img_srcset) ) {
				        	$img_srcset_output = 'srcset="'.esc_attr($img_srcset).'" sizes="(max-width: '.esc_attr($image[1]).'px) 100vw, '.esc_attr($image[1]).'px"';
				        } else {
				        	$img_srcset_output = '';
				        }?>
							<div class="imghoverclass mosaic_item_wrap">
	                                <img src="<?php echo esc_url($image[0]); ?>" alt="<?php tthe_title_attribute(); ?>" width="<?php echo esc_attr($image[1]);?>" height="<?php echo esc_attr($image[2]);?>" <?php echo $img_srcset_output;?> class="lightboxhover" style="display: block;">
	                        </div>
	                                <?php if($kt_portfolio_loop['lightbox'] == 'true') {?>
												<a href="<?php echo esc_url($video_url); ?>" class="kad_portfolio_lightbox_link pvideolight" title="<?php the_title_attribute();?>" data-rel="lightbox">
													<i class="icon-search"></i>
												</a>
									<?php }?>
                        <?php $image = null; $thumbnailURL = null;?>
                    <?php } 
            } else {
					if (has_post_thumbnail( $post->ID ) ) {
						$image_id = get_post_thumbnail_id( $post->ID );
						$image_url = wp_get_attachment_image_src($image_id, 'full' ); 
						$thumbnailURL = $image_url[0]; 
						$image = aq_resize($thumbnailURL, $mosaic_xsize, $mosaic_ysize, true, false);
						if(empty($image[0])) {$image = array($thumbnailURL,$mosaic_xsize,$mosaic_ysize);}
						// Get srcset
		        		$img_srcset = kt_get_srcset( $image[1], $image[2], $thumbnailURL, $image_id);
		        		if(!empty($img_srcset) ) {
				        	$img_srcset_output = 'srcset="'.esc_attr($img_srcset).'" sizes="(max-width: '.esc_attr($image[1]).'px) 100vw, '.esc_attr($image[1]).'px"';
				        } else {
				        	$img_srcset_output = '';
				        }
		        		?>
							<div class="imghoverclass mosaic_item_wrap">
	                                <img src="<?php echo esc_url($image[0]); ?>" alt="<?php the_title_attribute(); ?>" width="<?php echo esc_attr($image[1]);?>" height="<?php echo esc_attr($image[2]);?>" <?php echo $img_srcset_output;?> class="lightboxhover" style="display: block;">
	                        </div>
	                                <?php if($kt_portfolio_loop['lightbox'] == 'true') {?>
												<a href="<?php echo esc_url($thumbnailURL); ?>" class="kad_portfolio_lightbox_link" title="<?php the_title_attribute();?>" data-rel="lightbox">
													<i class="icon-search"></i>
												</a>
									<?php }?>
                        <?php $image = null; $thumbnailURL = null;?>
                    <?php } 
            } ?>
            <a href="<?php the_permalink();?>" class="portfoliomosaiclink" title="<?php the_title_attribute(); ?>">
              						</a>
              	<a href="<?php the_permalink() ?>" class="portfoliolink">
					<div class="piteminfo">   
                        <h5><?php the_title();?></h5>
                        <?php if($kt_portfolio_loop['showtypes'] == 'true') {
                        	$terms = get_the_terms( $post->ID, 'portfolio-type' ); 
                        	if ($terms) {?> 
                        		<p class="cportfoliotag">
                        			<?php $output = array(); foreach($terms as $term){ $output[] = $term->name;} echo implode(', ', $output); ?>
                        		</p>
                        <?php } 
                       	} 
                       	if($kt_portfolio_loop['showexcerpt'] == 'true') { ?> 
                       		<p><?php echo virtue_excerpt(16); ?></p> 
                       	<?php } ?>
                    </div>
                </a>
        </div>
        </div>
        <?php $kt_portfolio_loop_mosaic['item_count'] ++;

