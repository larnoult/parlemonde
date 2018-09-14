<?php 
/**
 * Topbar Template
 *
 * @version 3.2.5
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $virtue; 
?>
<div id="topbar" class="topclass">
	<div class="container">
		<div class="row">
			<div class="col-md-6 col-sm-6 kad-topbar-left">
				<div class="topbarmenu clearfix">
				<?php 

				if ( has_nav_menu( 'topbar_navigation' ) ) :
					wp_nav_menu( array( 'theme_location' => 'topbar_navigation', 'menu_class' => 'sf-menu' ) );
				endif;
				
				if( kadence_display_topbar_icons() ) : ?>
					<div class="topbar_social">
						<ul>
						<?php 
						$top_icons = $virtue['topbar_icon_menu'];
						foreach ($top_icons as $top_icon) {
							if( ! empty( $top_icon[ 'target' ] ) && 1 == $top_icon[ 'target' ] ) {
								$target = '_blank';
							} else {
								$target = '_self';
							}
                  			echo '<li><a href="'.esc_url( $top_icon[ 'link' ] ).'" target="'.esc_attr( $target ).'" title="'.esc_attr( $top_icon[ 'title' ] ).'" data-toggle="tooltip" data-placement="bottom" data-original-title="'.esc_attr( $top_icon[ 'title' ] ).'">';
								if( ! empty( $top_icon[ 'url' ] ) ) {
									echo '<img src="'.esc_url( $top_icon[ 'url' ] ).'"/>' ;
								} else {
									echo '<i class="'.esc_attr( $top_icon[ 'icon_o' ] ).'"></i>';
								}
							echo '</a></li>';
						} ?>
						</ul>
					</div>
					<?php 
				endif;

				if( isset( $virtue[ 'show_cartcount' ] ) ) {
					if( $virtue[ 'show_cartcount' ] == '1' ) { 
						if ( class_exists( 'woocommerce' ) ) { ?>
						<ul class="kad-cart-total">
							<li>
								<a class="cart-contents" href="<?php echo esc_url( wc_get_cart_url() ); ?>" title="<?php esc_attr_e( 'View your shopping cart', 'virtue' ); ?>">
									<i class="icon-shopping-cart" style="padding-right:5px;"></i>
									<?php esc_html_e( 'Your Cart', 'virtue' );?>
									<span class="kad-cart-dash">-</span>
									<?php 
									if ( WC()->cart->tax_display_cart == 'incl' ) {
										echo wp_kses_post( WC()->cart->get_cart_subtotal() ); 
									} else {
										echo wp_kses_post( WC()->cart->get_cart_total() );
									}
									?>
								</a>
							</li>
						</ul>
						<?php 
						} 
					} 
				} ?>
				</div>
			</div><!-- close col-md-6 --> 
			<div class="col-md-6 col-sm-6 kad-topbar-right">
				<div id="topbar-search" class="topbar-widget">
					<?php 
					if( kadence_display_topbar_widget() ) {
						if( is_active_sidebar( 'topbarright' ) ) {
							dynamic_sidebar( 'topbarright' ); 
						} 
					} else { 
						if( kadence_display_top_search() ) {
							get_search_form();
						} 
					} ?>
				</div>
			</div> <!-- close col-md-6-->
		</div> <!-- Close Row -->
	</div> <!-- Close Container -->
</div>