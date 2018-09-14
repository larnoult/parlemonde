<?php
/**
 * Recent portfolio posts.
 *
 * @package Virtue Theme
 */

?>
<div id="portfolio_carousel_container" class="carousel_outerrim">
	<?php
	global $post, $virtue;
	$text = get_post_meta( $post->ID, '_kad_portfolio_carousel_title', true );
	if ( ! empty( $text ) ) {
		echo '<h3 class="title">' . esc_html( $text ) . '</h3>';
	} else {
		echo '<h3 class="title">' . __( 'Recent Projects', 'virtue' ) . '</h3>';
	}
	?>
	<div class="portfolio-carouselcase fredcarousel">
		<?php
		$itemsize    = 'tcol-lg-3 tcol-md-3 tcol-sm-4 tcol-xs-6 tcol-ss-12';
		$slidewidth  = 269;
		$slideheight = 269;
		$md          = 4;
		$sm          = 3;
		$xs          = 2;
		$ss          = 1;
		if ( isset( $virtue['portfolio_type_under_title'] ) && 0 == $virtue['portfolio_type_under_title'] ) {
			$portfolio_item_types = false;
		} else {
			$portfolio_item_types = true;
		}
		?>
		<div id="carouselcontainer-portfolio" class="rowtight fadein-carousel">
			<div id="portfolio-carousel" class="slick-slider kt-slickslider kt-content-carousel loading clearfix"  data-slider-fade="false" data-slider-type="content-carousel" data-slider-anim-speed="300" data-slider-scroll="1" data-slider-auto="true" data-slider-speed="9000" data-slider-xxl="<?php echo esc_attr( $md ); ?>" data-slider-xl="<?php echo esc_attr( $md ); ?>" data-slider-md="<?php echo esc_attr( $md ); ?>" data-slider-sm="<?php echo esc_attr( $sm ); ?>" data-slider-xs="<?php echo esc_attr( $xs ); ?>" data-slider-ss="<?php echo esc_attr( $ss ); ?>">
				<?php
				$loop = new WP_Query();
				$loop->query( array(
					'orderby'        => 'date',
					'order'          => 'DESC',
					'post_type'      => 'portfolio',
					'post__not_in'   => array( $post->ID ),
					'posts_per_page' => '8',
				) );
				if ( $loop ) :
					while ( $loop->have_posts() ) :
						$loop->the_post();
						?>
						<div class="<?php echo esc_attr( $itemsize ); ?>">
							<div class="grid_item portfolio_item all postclass">
								<?php if ( has_post_thumbnail( $post->ID ) ) { ?>
									<div class="imghoverclass">
										<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
											<?php echo virtue_get_full_image_output( $slidewidth, $slideheight, true, 'lightboxhover', null, get_post_thumbnail_id( $post->ID ), true, true, false ); ?>
										</a> 
									</div>
								<?php } ?>
								<a href="<?php the_permalink(); ?>" class="portfoliolink">
									<div class="piteminfo">   
										<h5><?php the_title(); ?></h5>
										<?php
										if ( true == $portfolio_item_types ) {
											$terms = get_the_terms( $post->ID, 'portfolio-type' );
											if ( $terms ) {
												?>
												<p class="cportfoliotag">
													<?php
													$output = array();
													foreach ( $terms as $term ) {
														$output[] = $term->name;
													}
													echo wp_kses_post( implode( ', ', $output ) );
												?>
												</p>
												<?php
											}
										}
										?>
									</div>
								</a>
							</div>
						</div>
					<?php
					endwhile;
					else :
					?>
					<div class="error-not-found">
						<?php esc_html_e( 'Sorry, no portfolio entries found.', 'virtue' ); ?>
					</div>
				<?php
				endif;
					wp_reset_postdata();
				?>
			</div>									
		</div>
	</div>
</div><!-- Porfolio Container-->
