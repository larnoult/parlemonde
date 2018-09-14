<?php 

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $virtue;
if(isset($virtue['portfolio_title'])) {
	$porttitle = $virtue['portfolio_title'];
} else {
	$porttitle = __('Featured Projects', 'virtue');
}
if(!empty($virtue['home_portfolio_carousel_count'])) {
	$hp_pcount = $virtue['home_portfolio_carousel_count'];
} else {
	$hp_pcount = '6';
} 
if(!empty($virtue['home_portfolio_order'])) {
	$hp_orderby = $virtue['home_portfolio_order'];
} else {
	$hp_orderby = 'menu_order';
}
if($hp_orderby == 'menu_order' || $hp_orderby == 'title') {
	$p_order = 'ASC';
} else {
	$p_order = 'DESC';
} 
if( ! empty($virtue['portfolio_type'] ) ) {
	$port_cat = get_term_by ('id',$virtue['portfolio_type'],'portfolio-type');
	$portfolio_category = $port_cat -> slug;
} else {
	$portfolio_category = '';
}
if(isset($virtue['portfolio_show_type'])) {
	$portfolio_item_types = $virtue['portfolio_show_type'];
} else {
	$portfolio_item_types = '';
}
$itemsize = 'tcol-lg-4 tcol-md-4 tcol-sm-4 tcol-xs-6 tcol-ss-12';
$slidewidth = 366;
$slideheight = 366;
$md = 3;
$sm = 3;
$xs = 2;
$ss = 1;
?>
<div class="home-portfolio home-margin carousel_outerrim home-padding">
	<div class="clearfix">
		<h3 class="hometitle">
			<?php echo esc_html($porttitle); ?>
		</h3>
	</div>
	<div class="home-margin fredcarousel">
		<div id="carouselcontainer-portfolio" class="rowtight fadein-carousel">
			<div id="portfolio-carousel" class="portfolio-carousel slick-slider kt-slickslider kt-content-carousel loading clearfix" data-slider-fade="false" data-slider-type="content-carousel" data-slider-anim-speed="700" data-slider-scroll="1" data-slider-auto="true" data-slider-speed="9000" data-slider-xxl="<?php echo esc_attr($md);?>" data-slider-xl="<?php echo esc_attr($md);?>" data-slider-md="<?php echo esc_attr($md);?>" data-slider-sm="<?php echo esc_attr($sm);?>" data-slider-xs="<?php echo esc_attr($xs);?>" data-slider-ss="<?php echo esc_attr($ss);?>">
			<?php 
				$loop = new WP_Query();
				$loop->query(array(
					'orderby' 		=> $hp_orderby,
					'order' 		=> $p_order,
					'post_type' 	=> 'portfolio',
					'portfolio-type'=> $portfolio_category,
					'posts_per_page'=> $hp_pcount
					)
				);
				if ( $loop ) : 

					while ( $loop->have_posts() ) : $loop->the_post(); ?>
						<div class="<?php echo esc_attr( $itemsize ); ?> kad_portfolio_item">
							<div class="grid_item portfolio_item postclass">
								<?php if (has_post_thumbnail( $post->ID ) ) { ?>
									<div class="imghoverclass">
										<a href="<?php the_permalink()  ?>" title="<?php the_title_attribute(); ?>" class="kad_portfolio_link">
											<?php echo virtue_get_full_image_output($slidewidth, $slideheight, true, 'lightboxhover', null, get_post_thumbnail_id( $post->ID ), true, true, false); ?>
										</a> 
									</div>
								<?php } ?>
								<a href="<?php the_permalink() ?>" class="portfoliolink">
									<div class="piteminfo">   
										<h5><?php the_title();?></h5>
										<?php 
											if ( $portfolio_item_types == 1 ) { 
												$terms = get_the_terms( $post->ID, 'portfolio-type' ); 
												if ( $terms ) {?> 
													<p class="cportfoliotag"><?php $output = array(); foreach($terms as $term){ $output[] = $term->name;} echo wp_kses_post( implode(', ', $output) ); ?></p> 
												<?php 
												} 
											} ?>
									</div>
								</a>
							</div>
						</div>
					<?php endwhile; else: ?>
					<li class="error-not-found"><?php esc_html_e('Sorry, no portfolio entries found.', 'virtue');?></li>	
					<?php endif; 

                    $loop = null; 
                    wp_reset_query(); ?>
			</div>
		</div>
	</div> <!-- fred Carousel-->
</div> <!--home portfoliot -->