<?php
/*
* Single Portfolio Content
*/

 global $post, $virtue;
?>
<div id="pageheader" class="titleclass">
		<div class="container">
			<div class="page-header">
				<div class="portfolionav clearfix">
	   				<?php 
	   				kadence_previous_post_link_plus( array('order_by' => 'menu_order', 'loop' => true, 'format' => '%link', 'link' => '<i class="icon-chevron-left"></i>') ); 
	   			 	if( !empty($virtue['portfolio_link'])){ ?>
						 <a href="<?php echo esc_url( get_page_link( $virtue[ 'portfolio_link' ] ) ); ?>">
					<?php } else {?> 
						<a href="../">
					<?php } ?>
	   				<i class="icon-th"></i></a> 
	   				<?php kadence_next_post_link_plus( array('order_by' => 'menu_order', 'loop' => true, 'format' => '%link', 'link' => '<i class="icon-chevron-right"></i>') ); ?>
	   				<span>&nbsp;</span>
   				</div>
			<h1 class="entry-title" itemprop="name headline"><?php the_title(); ?></h1>
			</div>
		</div><!--container-->
</div><!--titleclass-->
<?php do_action( 'kadence_single_portfolio_before' ); ?>
<div id="content" class="container">
    <div class="row">
      <div class="main <?php echo esc_attr( virtue_main_class() ); ?> portfolio-single" role="main" itemscope itemtype="http://schema.org/CreativeWork">
      <?php while (have_posts()) : the_post(); ?>
      <?php 
      	$layout 	= get_post_meta( $post->ID, '_kad_ppost_layout', true ); 
		$ppost_type = get_post_meta( $post->ID, '_kad_ppost_type', true );
		$imgheight 	= get_post_meta( $post->ID, '_kad_posthead_height', true );
		$imgwidth 	= get_post_meta( $post->ID, '_kad_posthead_width', true );
		$autoplay 	= get_post_meta( $post->ID, '_kad_portfolio_autoplay', true );
		if( isset( $autoplay ) && $autoplay == 'no' ) {
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
		} else {
			$slideheight = 450;
		} 
		if (!empty($imgwidth)) {
			$slidewidth = $imgwidth;
		} else {
			$slidewidth = $slidewidth_d;
		} 
		 ?>
  <article <?php post_class() ?> id="post-<?php the_ID(); ?>">
      <div class="postclass">
      	<div class="row">
      		<div class="<?php echo esc_attr( $imgclass ); ?>">
      		<?php do_action( 'kadence_single_portfolio_before_feature' );
				
				if ($ppost_type == 'flex') { ?>
					<div class="flexslider loading kt-flexslider kad-light-gallery" style="max-width:<?php echo esc_attr( $slidewidth );?>px;" data-flex-speed="7000" data-flex-anim-speed="400" data-flex-animation="fade" data-flex-auto="<?php echo esc_attr($slideauto);?>">
                       	<ul class="slides">
						<?php
                          	$image_gallery = get_post_meta( $post->ID, '_kad_image_gallery', true );
                          		if(!empty($image_gallery)) {
                    				$attachments = array_filter( explode( ',', $image_gallery ) );
                    					if ($attachments) {
											foreach ($attachments as $attachment) {
												$img = virtue_get_image_array( $slidewidth, $slideheight, true, null, null, $attachment, false );
												$caption = get_post($attachment)->post_excerpt;

												echo '<li><a href="'.esc_url( $img[ 'full' ] ).'" data-rel="lightbox" title="'.esc_attr( $caption ).'" itemprop="image" itemscope itemtype="https://schema.org/ImageObject"><img src="'.esc_url( $img[ 'src' ] ).'" width="'.esc_attr( $img[ 'width' ] ).'" height="'.esc_attr( $img[ 'height' ] ).'" '.wp_kses_post( $img[ 'srcset' ] ).' alt="'.esc_attr( $img[ 'alt' ] ).'"/>';
														echo '<meta itemprop="url" content="'.esc_url( $img['src'] ).'">';
														echo '<meta itemprop="width" content="'.esc_attr( $img['width'] ).'">';
														echo '<meta itemprop="height" content="'.esc_attr( $img['height'] ).'">';
													echo '</a></li>';
											}
										}
                    			} else {
                    				$attach_args = array('order'=> 'ASC','post_type'=> 'attachment','post_parent'=> $post->ID,'post_mime_type' => 'image','post_status'=> null,'orderby'=> 'menu_order','numberposts'=> -1);
									$attachments = get_posts($attach_args);
										if ( $attachments ) {
											foreach ( $attachments as $attachment ) {
												$caption = get_post($attachment->ID)->post_excerpt;
												$img = virtue_get_image_array( $slidewidth, $slideheight, true, null, null, $attachment->ID, false );
												echo '<li><a href="'.esc_url( $img[ 'full' ] ).'" data-rel="lightbox" title="'.esc_attr( $caption ).'"><img src="'.esc_url( $img[ 'src' ] ).'" width="'.esc_attr( $img[ 'width' ] ).'" height="'.esc_attr( $img[ 'height' ] ).'" '.wp_kses_post( $img[ 'srcset' ] ).' alt="'.esc_attr( $img[ 'alt' ] ).'"/></a></li>';
											}
                    					}	
								} ?>                                
						</ul>
              		</div> <!--Flex Slides-->
              	<?php } else if ($ppost_type == 'carousel') { ?>
					 <div id="imageslider" class="carousel_outerrim">
					    <div class="carousel_slider_outer fredcarousel" style="overflow:hidden; max-width:<?php echo esc_attr($slidewidth);?>px; height: <?php echo esc_attr($slideheight);?>px; margin-left: auto; margin-right:auto;">
					        <div class="carousel_slider kad-light-gallery slick-slider kt-slickslider kt-content-carousel kt-slider-different-image-ratio loading clearfix" data-slider-fade="false" data-slider-type="slider" data-slider-anim-speed="600" data-slider-scroll="1" data-slider-auto="<?php echo esc_attr( $slideauto );?>" data-slider-speed="9000">
					            <?php
								$image_gallery = get_post_meta( $post->ID, '_kad_image_gallery', true );
								if( ! empty( $image_gallery ) ) {
									$attachments = array_filter( explode( ',', $image_gallery ) );
									if ($attachments) {
										foreach ( $attachments as $attachment ) {
											$caption = get_post($attachment)->post_excerpt;
											$img = virtue_get_image_array( null, $slideheight, false, null, null, $attachment, false );
											echo '<div class="carousel_gallery_item" style="display: table; position: relative; text-align: center; margin: 0; width:100%; height:'.esc_attr( $img[ 'height' ] ).'px;">';
												echo '<div class="carousel_gallery_item_inner" style="vertical-align: middle; display: table-cell;">';
													echo '<a href="'.esc_url( $img[ 'full' ] ).'" data-rel="lightbox" title="'.esc_attr( $caption ).'" itemprop="image" itemscope itemtype="https://schema.org/ImageObject">';
														echo '<img src="'.esc_url( $img[ 'src' ] ).'" width="'.esc_attr( $img[ 'width' ] ).'" height="'.esc_attr( $img[ 'height' ] ).'" '.wp_kses_post( $img[ 'srcset' ] ).'  />';
														echo '<meta itemprop="url" content="'.esc_url( $img['src'] ).'">';
														echo '<meta itemprop="width" content="'.esc_attr( $img['width'] ).'">';
														echo '<meta itemprop="height" content="'.esc_attr( $img['height'] ).'">';
													echo '</a>'; 
												echo '</div>';
											echo '</div>';
										}
									}
								} ?>
					            </div>
					          </div> <!--fredcarousel-->
					  </div><!--carousel_outerrim-->
				<?php 
				} else if ($ppost_type == 'imagegrid') {
						$image_gallery = get_post_meta( $post->ID, '_kad_image_gallery', true );
						$columns = get_post_meta( $post->ID, '_kad_portfolio_img_grid_columns', true );
        				if( empty( $columns ) ) { 
        					$columns = '3';
        				}
						echo do_shortcode('[gallery ids="'.esc_attr( $image_gallery ).'" columns="'.esc_attr( $columns).'"]');
				} else if ($ppost_type == 'video') { ?>
					<div class="videofit">
                  <?php
					$allowed_tags = wp_kses_allowed_html('post');
					$allowed_tags['iframe'] = array(
						'src'             => true,
						'height'          => true,
						'width'           => true,
						'frameborder'     => true,
						'allowfullscreen' => true,
						'name' 			  => true,
						'id' 			  => true,
						'class' 		  => true,
						'style' 		  => true,
					);

					echo do_shortcode( wp_kses( get_post_meta( $post->ID, '_kad_post_video', true ), $allowed_tags ) );
					if (has_post_thumbnail( $post->ID ) ) { 
						$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' ); ?>
						<div itemprop="image" itemscope itemtype="https://schema.org/ImageObject">
							<meta itemprop="url" content="<?php echo esc_url( $image[0] ); ?>">
							<meta itemprop="width" content="<?php echo esc_attr( $image[1] )?>">
							<meta itemprop="height" content="<?php echo esc_attr( $image[2] )?>">
						</div>
					<?php }
                  	?>
                  </div>
				<?php 
				} else if ($ppost_type == 'none') {
					 $portfolio_margin = "kad_portfolio_nomargin";
				} else {
					if ( has_post_thumbnail() ) {		
						$image_id = get_post_thumbnail_id();
						$img = virtue_get_image_array( $slidewidth, $slideheight, true, null, null, $image_id, false );
						?>
						<div class="imghoverclass">
							<a href="<?php echo esc_url( $img[ 'full' ] ); ?>" data-rel="lightbox" class="lightboxhover" itemprop="image" itemscope itemtype="https://schema.org/ImageObject">
								<img src="<?php echo esc_url( $img[ 'src' ] ); ?>" width="<?php echo esc_attr( $img[ 'width' ] ); ?>" height="<?php echo esc_attr( $img[ 'height' ] ); ?>" <?php echo wp_kses_post( $img[ 'srcset' ] ); ?> alt="<?php echo esc_attr( get_post( $image_id )->post_excerpt ); ?>" />
								<?php 
								echo '<meta itemprop="url" content="'.esc_url( $img['src'] ).'">';
								echo '<meta itemprop="width" content="'.esc_attr( $img['width'] ).'">';
								echo '<meta itemprop="height" content="'.esc_attr( $img['height'] ).'">';
								?>
							</a>
						</div>
				<?php } 
				}
				do_action( 'kadence_single_portfolio_after_feature' ); ?>
        </div><!--imgclass -->
  		<div class="<?php echo esc_attr( $textclass ); ?>">
		    <div class="entry-content <?php echo esc_attr( $entryclass ); ?> <?php echo esc_attr( $portfolio_margin ); ?>" itemprop="text">
		    <?php 
		      	do_action( 'kadence_single_portfolio_before_content' );
					the_content(); 
		      	do_action( 'kadence_single_portfolio_after_content' ); ?>
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
		  	if( ! empty( $project_v1t ) || ! empty( $project_v2t ) || ! empty( $project_v3t ) || ! empty( $project_v4t ) || ! empty( $project_v5t ) ) { ?>
	    		<div class="<?php echo esc_attr( $valueclass ); ?>">
	    			<div class="pcbelow">
	    			<?php do_action( 'kadence_single_portfolio_value_before' );  ?> 
						<ul class="portfolio-content disc">
						<?php 
							if ( ! empty( $project_v1t ) ) {
								echo '<li class="pdetails"><span>'.esc_html( $project_v1t ).'</span> '.esc_html( $project_v1d ).'</li>';
							} 
							if ( ! empty( $project_v2t ) ) {
								echo '<li class="pdetails"><span>'.esc_html( $project_v2t ).'</span> '.esc_html( $project_v2d ).'</li>';
							}
							if ( ! empty( $project_v3t ) ) {
								echo '<li class="pdetails"><span>'.esc_html( $project_v3t ).'</span> '.esc_html( $project_v3d ).'</li>';
							}
							if ( ! empty( $project_v4t ) ) {
								echo '<li class="pdetails"><span>'.esc_html( $project_v4t ).'</span> '.esc_html( $project_v4d ).'</li>';
							}
							if ( ! empty( $project_v5t ) ) {
								echo '<li class="pdetails"><span>'.esc_html( $project_v5t ).'</span> <a href="'.esc_url( $project_v5d ).'" target="_new">'.esc_html( $project_v5d ).'</a></li>';
							}
						?>
				    	<?php do_action( 'kadence_single_portfolio_list_li' );  ?> 
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
</div>
<?php
      /**
      */
      do_action( 'kadence_single_portfolio_end' );
?>