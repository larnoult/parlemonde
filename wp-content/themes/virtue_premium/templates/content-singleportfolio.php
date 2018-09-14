<?php 
global $post, $virtue_premium;

?>
	<div id="pageheader" class="titleclass">
		<div class="container">
			<div class="page-header single-portfolio-item">
				<div class="row">
					<div class="col-md-8 col-sm-8">
						<?php if(kadence_display_portfolio_breadcrumbs()) { kadence_breadcrumbs(); } ?>
									<h1 class="entry-title"><?php the_title(); ?></h1>
		   			</div>
		   			<div class="col-md-4 col-sm-4">
		   				<div class="portfolionav clearfix">
		   					<?php if(!empty($virtue_premium['portfolio_arrow_nav']) && ($virtue_premium['portfolio_arrow_nav'] == 'cat') ) {$arrownav = true;} else {$arrownav = false;}	
		   					$parent_link = get_post_meta( $post->ID, '_kad_portfolio_parent', true ); if(!empty($parent_link) && ($parent_link != 'default')) {$parent_id = $parent_link;} else {$parent_id = $virtue_premium['portfolio_link'];}
		   					previous_post_link_plus( array('order_by' => 'menu_order', 'loop' => true, 'in_same_tax' => $arrownav, 'format' => '%link', 'link' => '<i class="icon-arrow-left"></i>') ); ?>
					   			<?php if( !empty($parent_id)){ ?>
					   				<a href="<?php echo get_page_link($parent_id); ?>" title="<?php echo get_the_title($parent_id);?>" >
									<?php } else {?> 
									<a href="../">
									<?php } ?>
					   				<i class="icon-grid"></i></a> 
					   				<?php next_post_link_plus( array('order_by' => 'menu_order', 'loop' => true, 'in_same_tax' => $arrownav, 'format' => '%link', 'link' => '<i class="icon-arrow-right"></i>') ); ?>
		   				</div>
		   			</div>
		   		</div>
		</div>
		</div><!--container-->
	</div><!--titleclass-->
  <?php if ( ! post_password_required() ) { ?>
	<?php 
			$layout = get_post_meta( $post->ID, '_kad_ppost_layout', true ); 
			$ppost_type = get_post_meta( $post->ID, '_kad_ppost_type', true );
			$imgheight = get_post_meta( $post->ID, '_kad_posthead_height', true );
			$imgwidth = get_post_meta( $post->ID, '_kad_posthead_width', true );
            $autoplay = get_post_meta( $post->ID, '_kad_portfolio_autoplay', true );
            if(isset($autoplay) && $autoplay == 'no') {
            	$slideauto = 'false';
            } else {
            	$slideauto = 'true';
            }
			if($layout == 'above')  {
				$imgclass = 'col-md-12';
				$textclass = 'pcfull clearfix';
				$entryclass = 'col-md-8';
				$valueclass = 'col-md-4';
				$slidewidth_d = 1140;
			} elseif ($layout == 'three')  {
				$imgclass = 'col-md-12';
				$textclass = 'pcfull clearfix';
				$entryclass = 'col-md-12';
				$valueclass = 'col-md-12';
				$slidewidth_d = 1140;
			} else {
				$imgclass = 'col-md-7';
				$textclass = 'col-md-5 pcside';
				$entryclass = '';
				$valueclass = '';
				$slidewidth_d = 653;
			 }
			 $portfolio_margin = '';
			if (!empty($imgheight)) {
				$slideheight = $imgheight; 
				$imageheight = $imgheight;
			} else { 
				$slideheight = 450; 
				$imageheight = apply_filters('kt_single_portfolio_image_height', 450); 
			} 
			if (!empty($imgwidth)) {
				$slidewidth = $imgwidth;
			} else {
				$slidewidth = $slidewidth_d;
			}
			do_action( 'kadence_single_portfolio_before' ); 
		 ?>
		 <?php if ( $ppost_type == 'imgcarousel' ) { ?>
			<section class="postfeat carousel_outerrim">
			    <div id="portfolio-carousel-gallery" class="fredcarousel" style="overflow:hidden;">
			          <?php 
			          	$image_gallery = get_post_meta( $post->ID, '_kad_image_gallery', true );
			            if (  ! empty( $image_gallery ) ) {
							virtue_build_slider($post->ID, $image_gallery, null, $slideheight, 'image', 'kt-image-carousel kt-image-carousel-center-fade kad-wp-gallery', 'carousel', 'false', $slideauto, '7000', 'true', 'false', '300');
						}
						?>
				</div> <!--fredcarousel-->
			</section>
      	<?php } ?>
<div id="content" class="container">
    <div class="row">
      <div class="main <?php echo virtue_main_class(); ?> portfolio-single" role="main">
      <?php while (have_posts()) : the_post(); ?>		
  <article <?php post_class() ?> id="post-<?php the_ID(); ?>">
      <div class="postclass">
      	<div class="row">
      		<div class="<?php echo esc_attr($imgclass); ?>">
				<?php do_action( 'kadence_single_portfolio_before_feature' );

				if ($ppost_type == 'flex') { ?>
					<div class="flexslider loading kt-flexslider kad-light-gallery" style="max-width:<?php echo esc_attr($slidewidth);?>px;" data-flex-speed="7000" data-flex-anim-speed="400" data-flex-animation="fade" data-flex-auto="<?php echo $slideauto; ?>">
                       <ul class="slides">
                          	<?php global $post;
                          	$image_gallery = get_post_meta( $post->ID, '_kad_image_gallery', true );
                          		if(!empty($image_gallery)) {
                    				$attachments = array_filter( explode( ',', $image_gallery ) );
                    					if ($attachments) {
											foreach ($attachments as $attachment) {
												$image_src = wp_get_attachment_image_src($attachment, 'full' ); 
												$caption = get_post($attachment)->post_excerpt;
												$image = aq_resize($image_src[0], $slidewidth, $slideheight, true, false, false, $attachment);
												if(empty($image[0])) {$image = array($image_src[0], $image_src[1], $image_src[2]);}
												echo '<li>';
													echo '<a href="'.esc_url($image_src[0]).'" data-rel="lightbox" title="'.esc_attr($caption).'">';
														echo '<img src="'.esc_url($image[0]).'" width="'.esc_attr($image[1]).'" height="'.esc_attr($image[2]).'" '.kt_get_srcset_output($image[1], $image[2], $image_src[0], $attachment).' alt="'.esc_attr($caption).'" />';
													echo '</a>';
												echo '</li>';
											}
										}
                    			}  ?>                            
						</ul>
              	</div> <!--Flex Slides-->
				<?php 	
				} else if ($ppost_type == 'rev' || $ppost_type == 'cyclone' || $ppost_type == 'ktslider') {

					$shortcodeslider = get_post_meta( $post->ID, '_kad_shortcode_slider', true ); if(!empty($shortcodeslider)) echo do_shortcode( $shortcodeslider );

				} else if ($ppost_type == 'video') { ?>
					
					<div class="videofit">
                  		<?php
                  		$video_url = get_post_meta( $post->ID, '_kad_post_video_url', true );
                    if(!empty($video_url)) {
                      echo wp_oembed_get($video_url);
                    } else {
                  		$video = get_post_meta( $post->ID, '_kad_post_video', true ); echo $video;
                  	} ?>
                  	</div>
				<?php 
			} else if ($ppost_type == 'imagegrid') {
				$image_gallery = get_post_meta( $post->ID, '_kad_image_gallery', true );
				$columns = get_post_meta( $post->ID, '_kad_portfolio_img_grid_columns', true );
        		if(empty($columns)) {$columns = '4';}
				echo do_shortcode('[gallery ids="'.$image_gallery.'" columns="'.$columns.'"]');
			} else if ($ppost_type == 'carousel') { ?>
					
					 <div id="imageslider" class="carousel_outerrim">
					 	<?php 
					 	$image_gallery = get_post_meta( $post->ID, '_kad_image_gallery', true );
					 	virtue_build_slider($post->ID, $image_gallery, null, $slideheight, 'image', 'kt-slider-different-image-ratio', 'slider', 'false', 'true', '7000', 'true', 'false'); ?>
					</div><!--Container-->
				<?php 
				} else if ($ppost_type == 'imagelist') { ?>
				<div class="kad-light-gallery">
					<?php
                          	$image_gallery = get_post_meta( $post->ID, '_kad_image_gallery', true );
                          		if(!empty($image_gallery)) {
                    				$attachments = array_filter( explode( ',', $image_gallery ) );
                    					if ($attachments) {
                    						$counter = 0;
											foreach ($attachments as $attachment) {
												$image_src = wp_get_attachment_image_src($attachment, 'full' ); 
												$caption = get_post($attachment)->post_excerpt;
												$image = aq_resize($image_src[0], $slidewidth, $slideheight, true, false, false, $attachment);
												if(empty($image[0])) {$image = array($image_src[0], $image_src[1], $image_src[2]);}

												echo '<div class="portfolio_list_item pli'.$counter.'">';
													echo '<a href="'.$image_src[0].'" data-rel="lightbox" class="lightboxhover" title="'.$caption.'">';
														echo '<img src="'.$image[0].'" alt="'.$caption.'" width="'.esc_attr($image[1]).'" height="'.esc_attr($image[2]).'" '.kt_get_srcset_output($image[1], $image[2], $image_src[0], $attachment).' />';
													echo '</a>';
												echo '</div>';
												$counter ++;
											}
										}
                    			} ?>  
							</div>  
				<?php } else if ($ppost_type == 'imagelist2') { ?>
				<div class="kad-light-gallery portfolio_image_list_style2">
					<?php 
                          	$image_gallery = get_post_meta( $post->ID, '_kad_image_gallery', true );
                          		if(!empty($image_gallery)) {
                    				$attachments = array_filter( explode( ',', $image_gallery ) );
                    					if ($attachments) {
                    						$counter = 0;
											foreach ($attachments as $attachment) {
												$image_src = wp_get_attachment_image_src($attachment, 'full' ); 
												$caption = get_post($attachment)->post_excerpt;
												$image = aq_resize($image_src[0], $slidewidth, null, false, false, false, $attachment);
												if(empty($image[0])) {$image = array($image_src[0], $image_src[1], $image_src[2]);}

												echo '<div class="portfolio_list_item pli'.$counter.'">';
													echo '<a href="'.$image_src[0].'" data-rel="lightbox" class="lightboxhover" title="'.esc_attr($caption).'">';
														echo '<img src="'.$image[0].'" alt="'.$caption.'" width="'.esc_attr($image[1]).'" height="'.esc_attr($image[2]).'" '.kt_get_srcset_output($image[1], $image[2], $image_src[0], $attachment).' />';
													echo '</a>';
												echo '</div>';
												$counter ++;
											}
										}
                    			} ?>  
							</div>  
				<?php 
				} else if ($ppost_type == 'imgcarousel') {
				} else if ($ppost_type == 'none') {
					$portfolio_margin = "kad_portfolio_nomargin";
				} else {
					if (has_post_thumbnail( $post->ID ) ) { 					
						$image_id = get_post_thumbnail_id();
						$image_src = wp_get_attachment_image_src($image_id, 'full' ); 
						$image = aq_resize( $image_src[0], $slidewidth, $imageheight, true, false, false, $image_id);
                  		if(empty($image[0])) {$image = array($image_src[0], $image_src[1], $image_src[2]);}
						?>
                            <div class="imghoverclass portfolio-single-img">
                            	<a href="<?php echo esc_url($image_src[0]); ?>" data-rel="lightbox" class="lightboxhover">
                            		<img src="<?php echo esc_url($image[0]); ?>" width="<?php echo esc_attr($image[1])?>" height="<?php echo esc_attr($image[2])?>" <?php echo kt_get_srcset_output($image[1], $image[2], $image_src[0], $image_id);?> alt="<?php echo esc_attr(get_post($image_id)->post_excerpt); ?>"  />
                            	</a>
                            </div>
                            <?php 
                    }
				} 
				do_action( 'kadence_single_portfolio_after_feature' ); ?>
        	</div><!--imgclass -->
  			<div class="<?php echo esc_attr($textclass); ?>">
		    	<div class="entry-content <?php echo esc_attr($entryclass); ?> <?php echo esc_attr($portfolio_margin); ?>">
			    	<?php 
			    	do_action( 'kadence_single_portfolio_before_content' );
			      			the_content(); 
			      	do_action( 'kadence_single_portfolio_after_content' ); 
			      	?>
		  		</div>
		  		<?php 
		  			$project_v1t = get_post_meta( $post->ID, '_kad_project_val01_title', true );
				    $project_v1d = get_post_meta( $post->ID, '_kad_project_val01_description', true );
					$project_v2t = get_post_meta( $post->ID, '_kad_project_val02_title', true );
					$project_v2d = get_post_meta( $post->ID, '_kad_project_val02_description', true );
					$project_v3t = get_post_meta( $post->ID, '_kad_project_val03_title', true );
					$project_v3d = get_post_meta( $post->ID, '_kad_project_val03_description', true );
					$project_v4t = get_post_meta( $post->ID, '_kad_project_val04_title', true );
					$project_v4d = get_post_meta( $post->ID, '_kad_project_val04_description', true );
					$project_v5t = get_post_meta( $post->ID, '_kad_project_val05_title', true );
					$project_v5d = get_post_meta( $post->ID, '_kad_project_val05_description', true );
					$tag_terms = get_the_terms( $post->ID, 'portfolio-tag' );
					if(!empty($project_v1t) || !empty($project_v2t)|| !empty($project_v3t) || !empty($project_v4t) || !empty($project_v5t) || !empty($tag_terms)) { ?>
	    				<div class="<?php echo esc_attr($valueclass); ?>">
				    		<div class="pcbelow">
				    		<?php do_action( 'kadence_single_portfolio_value_before' );  ?> 
							    <ul class="portfolio-content disc">
							    <?php 
							    if (!empty($project_v1t)) echo '<li class="pdetails"><span>'.$project_v1t.'</span> '.$project_v1d.'</li>'; 
							    if (!empty($project_v2t)) echo '<li class="pdetails"><span>'.$project_v2t.'</span> '.$project_v2d.'</li>'; 
							    if (!empty($project_v3t)) echo '<li class="pdetails"><span>'.$project_v3t.'</span> '.$project_v3d.'</li>'; 
							    if (!empty($project_v4t)) echo '<li class="pdetails"><span>'.$project_v4t.'</span> '.$project_v4d.'</li>'; 
							    if (!empty($project_v5t)) echo '<li class="pdetails"><span>'.$project_v5t.'</span> <a href="'.$project_v5d.'" target="_new">'.$project_v5d.'</a></li>'; 
							     
			                        	if ($tag_terms) {?> 
			                        		<li class="kt-portfolio-tags pdetails"><span class="portfoliotags"><i class="icon-tag"></i> </span>
			                        			<?php echo get_the_term_list( $post->ID,'portfolio-tag','',', ','') ?>
			                        		</li>
			                        <?php } 
			                        	do_action( 'kadence_single_portfolio_list_li' );  ?> 
							    </ul><!--Portfolio-content-->
							    <?php do_action( 'kadence_single_portfolio_value_after' );  ?> 
							</div>
						</div>
					<?php } ?>
    	</div><!--textclass -->
    </div><!--row-->
    <div class="clearfix"></div>
    </div><!--postclass-->
    <footer>
      <?php
      /**
      * @hooked virtue_portfolio_nav - 10
      */
      do_action( 'kadence_single_portfolio_footer' ); 
      ?>
    </footer>
  </article>
<?php
      /**
      * @hooked virtue_portfolio_bottom_carousel - 30
      * @hooked virtue_portfolio_comments - 40
      */
      do_action( 'kadence_single_portfolio_after' );

      endwhile; ?>
<?php } else { ?>
<div id="content" class="container">
    <div class="row">
      <div class="main <?php echo virtue_main_class(); ?> portfolio-single" role="main">
      <?php echo get_the_password_form();
    }?>
</div>
<?php
      /**
      */
      do_action( 'kadence_single_portfolio_end' );
?>