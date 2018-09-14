<?php
/**
 * Template Name: Portfolio Grid
 *
 * @package Virtue Theme
 */

global $post; 
$portfolio_category 	= get_post_meta( $post->ID, '_kad_portfolio_type', true ); 
$portfolio_items 		= get_post_meta( $post->ID, '_kad_portfolio_items', true );
$portfolio_order 		= get_post_meta( $post->ID, '_kad_portfolio_order', true );
$portfolio_lightbox 	= get_post_meta( $post->ID, '_kad_portfolio_lightbox', true );
$portfolio_cropheight 	= get_post_meta( $post->ID, '_kad_portfolio_img_crop', true );
$portfolio_column 		= get_post_meta( $post->ID, '_kad_portfolio_columns', true );
$portfolio_item_excerpt = get_post_meta( $post->ID, '_kad_portfolio_item_excerpt', true );
$portfolio_item_types 	= get_post_meta( $post->ID, '_kad_portfolio_item_types', true ); 
if( isset( $portfolio_order ) ) {
	$p_orderby = $portfolio_order;
} else {
	$p_orderby = 'menu_order';
}
if( $p_orderby == 'menu_order' || $p_orderby == 'title' ) {
	$p_order = 'ASC';
} else {
	$p_order = 'DESC';
}
if( $portfolio_category == '-1' || empty( $portfolio_category ) ) {
	$portfolio_cat_slug = ''; 
	$portfolio_cat_ID 	= '';
} else {
	$portfolio_cat 		= get_term_by ( 'id',$portfolio_category,'portfolio-type' );
	$portfolio_cat_slug = $portfolio_cat -> slug;
	$portfolio_cat_ID 	= $portfolio_cat -> term_id;
}
$portfolio_category = $portfolio_cat_slug;
if( $portfolio_items == 'all' ) {
	$portfolio_items = '-1';
}
if ( $portfolio_column == '2' ) {
	$itemsize 		= 'tcol-md-6 tcol-sm-6 tcol-xs-12 tcol-ss-12';
	$slidewidth 	= 559;
	$slideheight 	= 559;
} else if ( $portfolio_column == '3' ){
	$itemsize 		= 'tcol-md-4 tcol-sm-4 tcol-xs-6 tcol-ss-12';
	$slidewidth 	= 366;
	$slideheight 	= 366;
} else if ( $portfolio_column == '6' ){
	$itemsize 		= 'tcol-md-2 tcol-sm-3 tcol-xs-4 tcol-ss-6';
	$slidewidth 	= 240;
	$slideheight 	= 240;
} else if ( $portfolio_column == '5' ){
	$itemsize 		= 'tcol-md-25 tcol-sm-3 tcol-xs-4 tcol-ss-6';
	$slidewidth 	= 240;
	$slideheight 	= 240; 
} else {
	$itemsize = 'tcol-md-3 tcol-sm-4 tcol-xs-6 tcol-ss-12';
	$slidewidth = 269;
	$slideheight = 269;
}
$crop = true;
if ( !empty( $portfolio_cropheight ) ) {
	$slideheight = $portfolio_cropheight; 
}
if ( $portfolio_lightbox == 'yes' ){ 
	$plb = true;
} else {
	$plb = false;
}
/**
* @hooked virtue_page_title - 20
*/
do_action( 'virtue_page_title_container' );
?>
<div id="content" class="container <?php echo esc_attr( virtue_container_class() ); ?>">
	<div class="row">
  		<div class="main <?php echo esc_attr( virtue_main_class() );?>" role="main">
			<div class="entry-content" itemprop="mainContentOfPage" itemscope itemtype="http://schema.org/WebPageElement">
				<?php get_template_part( 'templates/content', 'page' ); ?>
			</div>
			<div id="portfoliowrapper" class="rowtight">    
            <?php 	
            $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
			$temp = $wp_query; 
			$wp_query = null; 
			$wp_query = new WP_Query();
			$wp_query->query( array(
				'paged' 		 => $paged,
				'orderby' 		 => $p_orderby,
				'order' 	 	 => $p_order,
				'post_type' 	 => 'portfolio',
				'portfolio-type' => $portfolio_cat_slug,
				'posts_per_page' => $portfolio_items
				)
			 );

			if ( $wp_query ) : 		 
				while ( $wp_query->have_posts() ) : $wp_query->the_post(); ?>
				<div class="<?php echo esc_attr( $itemsize );?> kad_portfolio_fade_in">
					<div class="portfolio_item grid_item postclass">
					<?php if (has_post_thumbnail( $post->ID ) ) {
							$img = virtue_get_image_array( $slidewidth, $slideheight, true, 'lightboxhover', null, get_post_thumbnail_id( $post->ID ) );
							?>
							<div class="imghoverclass">
								<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" class="kt-intrinsic" style="padding-bottom:<?php echo esc_attr( ( $img['height']/$img['width'] ) * 100 );?>%;">
									<?php echo '<img src="'.esc_url( $img['src'] ).'" width="'.esc_attr( $img['width'] ).'" height="'.esc_attr( $img['height'] ).'" '.wp_kses_post( $img['srcset'] ).' class="'.esc_attr( $img['class'] ).'" alt="'.esc_attr( $img['alt'] ).'">';
									?>
								</a> 
							</div>
							<?php if($plb) { ?>
								<a href="<?php echo esc_url( $img['full'] ); ?>" class="kad_portfolio_lightbox_link" title="<?php the_title_attribute();?>" data-rel="lightbox">
									<i class="icon-search"></i>
								</a>
							<?php } 
					} ?>
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
								} 
								if( $portfolio_item_excerpt == true ) {?>
									<p><?php echo esc_html( virtue_excerpt( 16 ) ); ?></p>
								<?php } ?>
							</div>
						</a>
					</div>
				</div>
				<?php endwhile; else: ?>
					<div class="error-not-found">
						<?php esc_html_e( 'Sorry, no portfolio entries found.', 'virtue' ); ?>
					</div>
				<?php endif; ?>
			</div> <!--portfoliowrapper-->           
			<?php
			/**
			* @hooked virtue_pagination - 10
			*/
			do_action( 'virtue_pagination' );

			$wp_query = $temp;  // Reset 
			wp_reset_postdata();

			/**
            * @hooked virtue_page_comments - 20
            */
			do_action('virtue_page_footer');
			?>
		</div><!-- /.main -->