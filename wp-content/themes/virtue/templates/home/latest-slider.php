<?php
global $virtue;
$slideheight = ( isset( $virtue['slider_size'] ) ? $virtue['slider_size'] : 400 );
$slidewidth = ( isset( $virtue['slider_size_width'] ) ? $virtue['slider_size_width'] : 1140 );
$autoplay = ( isset( $virtue['slider_autoplay'] ) && 0 == $virtue['slider_autoplay'] ? 'false' : 'true' );
$pausetime = ( isset( $virtue['slider_pausetime'] ) ? $virtue['slider_pausetime'] : '7000' );
$transtype = ( isset( $virtue['trans_type'] ) ? $virtue['trans_type'] : 'slide' );
$transtime = ( isset( $virtue['slider_transtime'] ) ? $virtue['slider_transtime'] : '300' );
?>
<div class="sliderclass kad-desktop-slider">
	<div id="imageslider" class="container">
		<div class="flexslider kt-flexslider loading" style="max-width:<?php echo esc_attr( $slidewidth );?>px; margin-left: auto; margin-right:auto;" data-flex-speed="<?php echo esc_attr( $pausetime );?>" data-flex-anim-speed="<?php echo esc_attr($transtime);?>" data-flex-animation="<?php echo esc_attr( $transtype ); ?>" data-flex-auto="<?php echo esc_attr( $autoplay );?>">
			<ul class="slides">
			<?php $temp = $wp_query; 
				$wp_query = null; 
				$wp_query = new WP_Query();
				$wp_query->query(array(
					'posts_per_page' => 4
					)
				);
              if ( $wp_query ) : while ( $wp_query->have_posts() ) : $wp_query->the_post(); 
					if (has_post_thumbnail( $post->ID ) ) { ?>
						<li> 
							<a href="<?php the_permalink(); ?>">
								<?php echo virtue_get_full_image_output($slidewidth, $slideheight, true, null, null, get_post_thumbnail_id( $post->ID ) ); ?>
								<div class="flex-caption">
									<div class="captiontitle headerfont"><?php the_title(); ?></div>
								</div> 
							</a>
						</li>
              <?php } endwhile; else: ?>
                <li class="error-not-found"><?php esc_html_e('Sorry, no blog entries found.', 'virtue'); ?></li>
              <?php endif;
              $wp_query = null;
              $wp_query = $temp; 
              wp_reset_query(); ?>
            </ul>
          </div> <!--Flex Slides-->
      </div><!--Container-->
</div><!--sliderclass-->