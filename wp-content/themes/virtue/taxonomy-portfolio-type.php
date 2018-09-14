<?php

global $virtue;

if(isset( $virtue['portfolio_type_columns'] ) && $virtue['portfolio_type_columns'] == '4' ) {
	$itemsize = 'tcol-md-3 tcol-sm-4 tcol-xs-6 tcol-ss-12';
	$slidewidth = 269;
	$slideheight = 269;
} elseif( isset( $virtue['portfolio_type_columns'] ) && $virtue['portfolio_type_columns'] == '5' ) {
	$itemsize 		= 'tcol-md-25 tcol-sm-3 tcol-xs-4 tcol-ss-6';
	$slidewidth 	= 240;
	$slideheight 	= 240; 
} else {
	$itemsize 		= 'tcol-md-4 tcol-sm-4 tcol-xs-6 tcol-ss-12';
	$slidewidth 	= 366; 
	$slideheight 	= 366; 
}
if( isset( $virtue['portfolio_type_under_title'] ) && $virtue['portfolio_type_under_title'] == '0' ) {
	$portfolio_item_types = false;
} else {
	$portfolio_item_types = true;
}

/**
* @hooked virtue_page_title - 20
*/
do_action( 'virtue_page_title_container' );
?>

<div id="content" class="container">
	<div class="row">
		<div class="main <?php echo esc_attr( virtue_main_class() ); ?>" role="main">
			<?php echo category_description(); ?> 
				<?php if ( !have_posts() ) : ?>
					<div class="alert">
						<?php esc_html_e( 'Sorry, no results were found.', 'virtue' ); ?>
					</div>
					<?php get_search_form(); 
				endif; 
				?>
				<div id="portfoliowrapper" class="rowtight">
					<?php while ( have_posts() ) : the_post(); ?>
					<div class="<?php echo esc_attr( $itemsize );?>">
						<div class="grid_item portfolio_item postclass">
							<?php
							if (has_post_thumbnail( $post->ID ) ) {
								$img = virtue_get_image_array( $slidewidth, $slideheight, true, 'lightboxhover', null, get_post_thumbnail_id( $post->ID ) );
								?>
								<div class="imghoverclass">
									<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" class="kt-intrinsic" style="padding-bottom:<?php echo esc_attr( ( $img['height']/$img['width'] ) * 100 );?>%;">
										<?php echo '<img src="'.esc_url( $img['src'] ).'" width="'.esc_attr( $img['width'] ).'" height="'.esc_attr( $img['height'] ).'" '.wp_kses_post( $img['srcset'] ).' class="'.esc_attr( $img['class'] ).'" alt="'.esc_attr( $img['alt'] ).'">';
										?>
									</a> 
								</div>
							<?php } ?>
							<a href="<?php the_permalink() ?>" class="portfoliolink">
								<div class="piteminfo">   
									<h5><?php the_title();?></h5>
									<?php if( $portfolio_item_types == true ) {
									$terms = get_the_terms( $post->ID, 'portfolio-type' );
										if ( $terms ) {?>
											<p class="cportfoliotag">
												<?php $output = array(); 
												foreach( $terms as $term ){ 
													$output[] = $term->name;
												} 
												echo esc_html( implode( ', ', $output ) ); ?>
											</p>
										<?php } 
									} ?>
								</div>
							</a>
						</div>
					</div>
				<?php endwhile; ?>
				</div> <!--portfoliowrapper-->

				<?php 
					/**
					* @hooked virtue_pagination - 10
					*/
					do_action( 'virtue_pagination' );
				?>
			</div><!-- /.main -->