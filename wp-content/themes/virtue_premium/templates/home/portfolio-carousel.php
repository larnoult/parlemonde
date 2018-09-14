<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $virtue_premium, $kt_portfolio_loop;

if ( ! empty( $virtue_premium[ 'portfolio_title' ] ) ) {
	$porttitle = $virtue_premium['portfolio_title'];
} else {
	$porttitle = __('Featured Projects', 'virtue');
}
if ( ! empty( $virtue_premium[ 'home_portfolio_order' ] ) ) {
	$hp_orderby = $virtue_premium['home_portfolio_order'];
} else {
	$hp_orderby = 'menu_order';
}
if ( $hp_orderby == 'menu_order' ) {
	$p_order = 'ASC';
} else {
	$p_order = 'DESC';
}
if ( ! empty( $virtue_premium[ 'home_portfolio_carousel_count' ] ) ) {
	$hp_pcount = $virtue_premium['home_portfolio_carousel_count'];
} else {
	$hp_pcount = '8';
}
if ( ! empty( $virtue_premium[ 'home_portfolio_carousel_speed' ] ) ) {
	$hport_speed = $virtue_premium['home_portfolio_carousel_speed'].'000';
} else {
	$hport_speed = '9000';
}
if ( isset( $virtue_premium[ 'home_portfolio_carousel_scroll' ] ) && $virtue_premium[ 'home_portfolio_carousel_scroll' ] == 'all' ) {
	$hport_scroll = 'all';
} else {
	$hport_scroll = '1';
}
if ( ! empty( $virtue_premium['portfolio_type'] ) ) {
	$port_cat = get_term_by( 'id', $virtue_premium['portfolio_type'], 'portfolio-type' );
	$portfolio_category = $port_cat->slug;
} else {
	$portfolio_category = '';
}
if ( isset( $virtue_premium[ 'portfolio_show_type' ]) && 1 == $virtue_premium[ 'portfolio_show_type' ] ) {
	$portfolio_show_types = 'true';
} else {
	$portfolio_show_types = 'false';
}
if ( isset( $virtue_premium[ 'portfolio_show_excerpt' ] ) && 1 == $virtue_premium[ 'portfolio_show_excerpt' ] ) {
	$portfolio_item_excerpt = 'true';
} else {
	$portfolio_item_excerpt = 'false';
}
if ( ! empty( $virtue_premium[ 'home_portfolio_carousel_column' ] ) ) {
	$portfolio_column = $virtue_premium[ 'home_portfolio_carousel_column' ];
} else {
	$portfolio_column = 3;
}		
?>
<div class="home-portfolio home-margin carousel_outerrim home-padding kad-animation" data-animation="fade-in" data-delay="0">
	<div class="clearfix">
		<h3 class="hometitle">
			<?php echo wp_kses_post( $porttitle ); ?>
		</h3>
	</div>
	<div class="home-margin fredcarousel">
		<div id="hport_carouselcontainer" class="home-portfolio-carousel">
			<?php virtue_build_post_content_carousel('portfolio', $portfolio_column, 'portfolio', $portfolio_category, $hp_pcount, $hp_orderby, $p_order, 'portfolio-carousel', null, 'true', $hport_speed, $hport_scroll, 'true', '500', null, 'false', $portfolio_item_excerpt, $portfolio_show_types, $virtue_premium[ 'home_portfolio_carousel_height' ]); ?>
		</div> <!-- fred Carousel-->
	</div> <!--featclass -->
</div>	