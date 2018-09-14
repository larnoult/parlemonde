<?php
/*
* Post loop contnet
*
*
*/
global $post, $virtue_premium, $kt_post_with_sidebar;

 if($kt_post_with_sidebar){
    $kt_feat_width = apply_filters('kt_blog_image_width_sidebar', 846); 
    $kt_portraittext = 'col-md-7';
    $kt_portraitimg_size = 'col-md-5';
 } else {
    $kt_feat_width = apply_filters('kt_blog_image_width', 1140); 
    $kt_portraittext = 'col-md-8';
    $kt_portraitimg_size = 'col-md-4';
 }

  $postsummery  = get_post_meta( $post->ID, '_kad_post_summery', true );
  $height       = get_post_meta( $post->ID, '_kad_posthead_height', true ); 
  $swidth       = get_post_meta( $post->ID, '_kad_posthead_width', true ); 
  // get_width
  if (!empty($height)) {
    $slideheight = $height;
  } else {
    $slideheight = apply_filters('kt_post_excerpt_image_height', 400);
  }
  // get height
  if (!empty($swidth)) {
    $slidewidth = $swidth; 
  } else {
    $slidewidth = apply_filters('kt_post_excerpt_image_width', $kt_feat_width);
  }
  // get post summary
  if(empty($postsummery) || $postsummery == 'default') {
    if(!empty($virtue_premium['post_summery_default'])) {
      $postsummery = $virtue_premium['post_summery_default'];
    } else {
      $postsummery = 'img_portrait';
    }
  }

?>
<article id="post-<?php the_ID(); ?>" <?php post_class('kad_blog_item kad-animation'); ?> data-animation="fade-in" data-delay="0" itemscope itemtype="http://schema.org/BlogPosting">
     <div class="row">
          <?php 
          if($postsummery == 'img_landscape') { 
               $textsize = 'col-md-12'; 
               	if ( has_post_thumbnail( $post->ID ) ) {
					$image_id =  get_post_thumbnail_id( $post->ID );
				} else {
					$image_id = null;
				}
				$img_args = array(
					'width' 		=> $slidewidth,
					'height' 		=> $slideheight,
					'crop'			=> true,
					'class'			=> 'iconhover',
					'alt'			=> null,
					'id'			=> $image_id,
					'placeholder'	=> true,
					'schema'		=> true,
					'intrinsic'       => true,
					'intrinsic_max'   => true,
				);
                ?>
				<div class="col-md-12 post-land-image-container">
					<div class="imghoverclass img-margin-center">
						<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
							<?php virtue_print_full_image_output( $img_args ); ?>
						</a> 
					</div>
				</div>
               <?php
          
          } elseif( $postsummery == 'img_portrait' ) { 
               $textsize = $kt_portraittext;
               $portraitwidth = apply_filters('kt_post_excerpt_image_width_portrait', 365);
               $portraitheight = apply_filters('kt_post_excerpt_image_height_portrait', 365);
				if (has_post_thumbnail( $post->ID ) ) {
				    $image_id =  get_post_thumbnail_id( $post->ID );
				} else {
				    $image_id = null;
				}
				$img_args = array(
					'width' 		=> $portraitwidth,
					'height' 		=> $portraitheight,
					'crop'			=> true,
					'class'			=> 'iconhover',
					'alt'			=> null,
					'id'			=> $image_id,
					'placeholder'	=> true,
					'schema'		=> true,
					'intrinsic'       => true,
					'intrinsic_max'   => true,
				);
                    ?>
                    <div class="<?php echo esc_attr( $kt_portraitimg_size );?> post-image-container">
                         <div class="imghoverclass img-margin-center">
                              <a href="<?php the_permalink()  ?>" title="<?php the_title_attribute(); ?>">
									<?php virtue_print_full_image_output( $img_args ); ?>
                              </a> 
                         </div>
                    </div>
               <?php // clear
               $image = null; $thumbnailURL = null; 
          
          } elseif($postsummery == 'slider_landscape') {
               $textsize = 'col-md-12'; ?>
               <div class="col-md-12 post-land-image-container">
					<?php
						$image_gallery = get_post_meta( $post->ID, '_kad_image_gallery', true );
						virtue_build_slider($post->ID, $image_gallery, $slidewidth, $slideheight, 'image', 'kt-slider-same-image-ratio');
					?>
               </div>
               <?php 
          
          } elseif($postsummery == 'slider_portrait') { 
               $textsize = $kt_portraittext; 
               $portraitwidth = apply_filters('kt_post_excerpt_image_width_portrait', 365);
               $portraitheight = apply_filters('kt_post_excerpt_image_height_portrait', 365); ?>
               <div class="<?php echo esc_attr($kt_portraitimg_size);?> post-image-container">
               		<?php
						$image_gallery = get_post_meta( $post->ID, '_kad_image_gallery', true );
						virtue_build_slider($post->ID, $image_gallery, $portraitwidth, $portraitheight, 'image', 'kt-slider-same-image-ratio');
					?>
               </div>
               <?php 

          } elseif($postsummery == 'video') {
               $textsize = 'col-md-12'; ?>
               <div class="col-md-12 post-land-image-container">
                    <div class="videofit">
                         <?php 
                         $video = get_post_meta( $post->ID, '_kad_post_video', true ); 
                              echo do_shortcode($video); 
                         ?>
                    </div>
                    <?php if (has_post_thumbnail( $post->ID ) ) { 
                        $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' ); ?>
                        <div itemprop="image" itemscope itemtype="https://schema.org/ImageObject">
                            <meta itemprop="url" content="<?php echo esc_url($image[0]); ?>">
                            <meta itemprop="width" content="<?php echo esc_attr($image[1])?>">
                            <meta itemprop="height" content="<?php echo esc_attr($image[2])?>">
                        </div>
                    <?php } ?>
               </div>
               <?php 

          } else { 
               $textsize = 'col-md-12 kttextpost'; 
          } ?>

          <div class="<?php echo esc_attr($textsize);?> post-text-container postcontent">
               <?php 
                /**
                * @hooked virtue_post_before_header_meta_date - 20
                */
                do_action( 'kadence_post_excerpt_before_header' );
                ?>
               <header>
                    <?php 
                    /**
                    * @hooked virtue_post_excerpt_header_title - 10
                    * @hooked virtue_post_header_meta - 20
                    */
                    do_action( 'kadence_post_excerpt_header' );
                    ?>
               </header>
               <div class="entry-content" itemprop="articleBody">
                    <?php 
                         do_action( 'kadence_post_excerpt_content_before' );

                         the_excerpt();

                         do_action( 'kadence_post_excerpt_content_after' );
                    ?>
               </div>
               <footer>
                    <?php 
                    /**
                    * @hooked virtue_post_footer_tags - 10
                    */
                    do_action( 'kadence_post_excerpt_footer' );
                    ?>
               </footer>
               <?php 
               /**
               * 
               */
               do_action( 'kadence_post_excerpt_after_footer' );
               ?>
          </div><!-- Text size -->
     </div><!-- row-->
</article> <!-- Article -->