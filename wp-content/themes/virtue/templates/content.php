    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?> itemscope="" itemtype="http://schema.org/BlogPosting">
        <div class="row">
        <?php global $post, $virtue, $virtue_sidebar; 
            $postsummery  = get_post_meta( $post->ID, '_kad_post_summery', true );
            $height       = get_post_meta( $post->ID, '_kad_posthead_height', true );
            $swidth       = get_post_meta( $post->ID, '_kad_posthead_width', true );
            if (!empty($height)){
                $slideheight = $height;
            } else {
                $slideheight = apply_filters('kt_post_excerpt_image_height', 400);
            }
            if (!empty($swidth)){
                $slidewidth = $swidth;
            } else {
            	if( $virtue_sidebar ) {
                	$slidewidth = apply_filters('kt_post_excerpt_image_width', 846);
                } else {
                	$slidewidth = apply_filters('kt_post_excerpt_full_image_width', 1140);
                }
            }
            if(empty($postsummery) || $postsummery == 'default') {
                if(!empty($virtue['post_summery_default'])) {
                    $postsummery = $virtue['post_summery_default'];
                } else {
                    $postsummery = 'img_portrait';
                }
            }
            $portraitwidth = apply_filters('kt_post_excerpt_image_width_portrait', 365);
            $portraitheight = apply_filters('kt_post_excerpt_image_height_portrait', 365);
                        
            if($postsummery == 'img_landscape') { 
                $textsize = 'col-md-12'; 
                	$image_id = get_post_thumbnail_id( $post->ID );
                	$img = virtue_get_image_array( $slidewidth, $slideheight, true, 'iconhover', null, $image_id, true );
                    ?>
                    <div class="col-md-12">
                        <div class="imghoverclass img-margin-center" itemprop="image" itemscope itemtype="https://schema.org/ImageObject">
                            <a href="<?php the_permalink()  ?>" title="<?php the_title_attribute(); ?>">
                                <img src="<?php echo esc_url( $img[ 'src' ] ); ?>" alt="<?php the_title_attribute(); ?>" width="<?php echo esc_attr( $img[ 'width' ] );?>" height="<?php echo esc_attr( $img[ 'height' ] );?>" itemprop="contentUrl"  class="<?php echo esc_attr( $img[ 'class' ] );?>" <?php echo wp_kses_post(  $img[ 'srcset' ] ); ?>>
                                    <meta itemprop="url" content="<?php echo esc_url( $img[ 'src' ] ); ?>">
                                    <meta itemprop="width" content="<?php echo esc_attr( $img[ 'width' ] )?>">
                                    <meta itemprop="height" content="<?php echo esc_attr( $img[ 'height' ] )?>">
                            </a> 
                        </div>
                    </div>
                    <?php
            } elseif($postsummery == 'img_portrait') {
				if( $virtue_sidebar ) {
					$textsize = 'col-md-7';
					$featsize = 'col-md-5';
				} else {
					$textsize = 'col-md-8';
					$featsize = 'col-md-4';
				}
                $image_id = get_post_thumbnail_id( $post->ID );
            	$img = virtue_get_image_array( $portraitwidth, $portraitheight, true, 'iconhover', null, $image_id, true );
              	?>
                <div class="<?php echo esc_attr( $featsize ); ?> post-image-container">
                    <div class="imghoverclass img-margin-center" itemprop="image" itemscope itemtype="https://schema.org/ImageObject">
                        <a href="<?php the_permalink()  ?>" title="<?php the_title_attribute(); ?>">
                            <img src="<?php echo esc_url( $img[ 'src' ] ); ?>" alt="<?php the_title_attribute(); ?>" width="<?php echo esc_attr( $img[ 'width' ] );?>" height="<?php echo esc_attr( $img[ 'height' ] );?>" itemprop="contentUrl"   class="<?php echo esc_attr( $img[ 'class' ] );?>" <?php echo wp_kses_post(  $img[ 'srcset' ] ); ?>>
								<meta itemprop="url" content="<?php echo esc_url( $img[ 'src' ] ); ?>">
								<meta itemprop="width" content="<?php echo esc_attr( $img[ 'width' ] )?>">
								<meta itemprop="height" content="<?php echo esc_attr( $img[ 'height' ] )?>">
                        </a> 
                     </div>
                 </div>
                    <?php
            } elseif($postsummery == 'slider_landscape') {
                $textsize = 'col-md-12'; ?>
                <div class="col-md-12">
                    <div class="flexslider kt-flexslider loading" style="max-width:<?php echo esc_attr($slidewidth);?>px;" data-flex-speed="7000" data-flex-anim-speed="400" data-flex-animation="fade" data-flex-auto="true">
                        <ul class="slides">
                            <?php
                            $image_gallery = get_post_meta( $post->ID, '_kad_image_gallery', true );
                            if(!empty($image_gallery)) {
                                $attachments = array_filter( explode( ',', $image_gallery ) );
                                if ($attachments) {
                                    foreach ($attachments as $attachment) {
                                    	$img = virtue_get_image_array( $slidewidth, $slideheight, true, null, null, $attachment, true );
                                        ?>
                                            <li>
                                                <a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>" itemprop="image" itemscope itemtype="https://schema.org/ImageObject">
                                                    <img src="<?php echo esc_url( $img[ 'src' ] ); ?>" itemprop="contentUrl" alt="<?php echo esc_attr( $img[ 'alt' ] );?>" width="<?php echo esc_attr( $img[ 'width' ] );?>" height="<?php echo esc_attr( $img[ 'height' ] );?>"  <?php echo wp_kses_post(  $img[ 'srcset' ] ); ?> />
													<meta itemprop="url" content="<?php echo esc_url( $img[ 'src' ] ); ?>">
													<meta itemprop="width" content="<?php echo esc_attr( $img[ 'width' ] )?>">
													<meta itemprop="height" content="<?php echo esc_attr( $img[ 'height' ] )?>">
                                            </a>
                                        </li>
                                    <?php 
                                    }
                                  }
                                } ?>                                   
                        </ul>
                    </div> <!--Flex Slides-->
                </div>
            <?php 
            } elseif($postsummery == 'slider_portrait') {
            	if( $virtue_sidebar ) {
                	$textsize = 'col-md-7';
                	$featsize = 'col-md-5';
                } else {
                	$textsize = 'col-md-8';
                	$featsize = 'col-md-4';
                }?>
                <div class="<?php echo esc_attr( $featsize ); ?> post-image-container">
                    <div class="flexslider kt-flexslider loading" data-flex-speed="7000" data-flex-anim-speed="400" data-flex-animation="fade" data-flex-auto="true">
                        <ul class="slides">
                            <?php 
                            $image_gallery = get_post_meta( $post->ID, '_kad_image_gallery', true );
                                if(!empty($image_gallery)) {
                                    $attachments = array_filter( explode( ',', $image_gallery ) );
                                    if ($attachments) {
                                        foreach ($attachments as $attachment) {
                                        	$img = virtue_get_image_array(  $portraitwidth, $portraitheight, true, null, null, $attachment, true ); ?>
                                            <li>
                                                <a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>" itemprop="image" itemscope itemtype="https://schema.org/ImageObject">
                                                    <img src="<?php echo  esc_url( $img[ 'src' ] ); ?>" alt="<?php echo esc_attr( $img[ 'alt' ] );?>" itemprop="contentUrl" width="<?php echo esc_attr( $img[ 'width' ] );?>" height="<?php echo esc_attr( $img[ 'height' ] );?>"  <?php echo wp_kses_post(  $img[ 'srcset' ] ); ?> />
													<meta itemprop="url" content="<?php echo esc_url( $img[ 'src' ] ); ?>">
													<meta itemprop="width" content="<?php echo esc_attr( $img[ 'width' ] )?>">
													<meta itemprop="height" content="<?php echo esc_attr( $img[ 'height' ] )?>">
                                                </a>
                                            </li>
                                        <?php 
                                        }
                                    }
                                } ?>           
                        </ul>
                    </div> <!--Flex Slides-->
                </div>
            <?php 
            } elseif($postsummery == 'video') {
                    $textsize = 'col-md-12'; ?>
                    <div class="col-md-12">
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
							?>
                        </div>
                    </div>
                    <?php if (has_post_thumbnail( $post->ID ) ) { 
                        $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' ); ?>
	                    <div itemprop="image" itemscope itemtype="https://schema.org/ImageObject">
	                        <meta itemprop="url" content="<?php echo esc_url( $image[0] ); ?>">
	                        <meta itemprop="width" content="<?php echo esc_attr( $image[1] )?>">
	                        <meta itemprop="height" content="<?php echo esc_attr( $image[2] )?>">
	                    </div>
                    <?php } ?>

            <?php 
            } else { 
                $textsize = 'col-md-12 kttextpost'; 
            } ?>

            <div class="<?php echo esc_attr( $textsize );?> post-text-container postcontent">
                <?php get_template_part('templates/post', 'date'); ?> 
                <header>
                    <a href="<?php the_permalink() ?>">
                        <h2 class="entry-title" itemprop="name headline">
                            <?php the_title(); ?> 
                        </h2>
                    </a>
                    <?php get_template_part('templates/entry', 'meta-subhead'); ?>    
                </header>
                <div class="entry-content" itemprop="description">
                    <?php 
                        do_action( 'kadence_post_excerpt_content_before' );
                        
                        the_excerpt(); 
                        
                        do_action( 'kadence_post_excerpt_content_after' );
                    ?>
                </div>
                <footer>
                <?php do_action( 'kadence_post_excerpt_footer' );
                    $tags = get_the_tags(); 
                    if ( $tags ) { ?>
                        <span class="posttags color_gray"><i class="icon-tag"></i> <?php the_tags('', ', ', ''); ?></span>
                    <?php } ?>
                </footer>
            </div><!-- Text size -->
        </div><!-- row-->
    </article> <!-- Article -->