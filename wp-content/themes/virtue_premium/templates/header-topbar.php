<?php
/** 
 * Topbar Template
 */
global $virtue_premium;

?>
<div id="topbar" class="topclass">
	<div class="container">
		<div class="row">
			<div class="col-md-6 col-sm-6 kad-topbar-left">
				<div class="topbarmenu clearfix">
				<?php 
					if ( has_nav_menu( 'topbar_navigation' ) ) :
						wp_nav_menu( array( 'theme_location' => 'topbar_navigation', 'menu_class' => 'sf-menu' ) );
						if ( isset( $virtue_premium[ 'topbar_mobile' ] ) && 1 == $virtue_premium[ 'topbar_mobile' ] ) { ?>
						<div id="mobile-nav-trigger-top" class="nav-trigger mobile-nav-trigger-id">
							<a class="nav-trigger-case" data-toggle="collapse" rel="nofollow" data-target=".top_mobile_menu_collapse">
								<div class="kad-navbtn clearfix"><i class="icon-menu"></i></div>
							</a>
						</div>
						<?php }
					endif;

					if ( kadence_display_topbar_icons() ) : ?>
						<div class="topbar_social">
							<ul>
								<?php $top_icons = $virtue_premium['topbar_icon_menu'];
								$i = 1;
								foreach ( $top_icons as $top_icon ) {
									if ( ! empty( $top_icon[ 'target' ] ) && 1 == $top_icon[ 'target' ] ) {
										$target = '_blank';
									} else {
										$target = '_self';
									}
									echo '<li><a href="'.esc_attr( $top_icon[ 'link' ] ).'" data-toggle="tooltip" data-placement="bottom" target="'.esc_attr( $target ).'" class="topbar-icon-'.esc_attr( $i ).'" data-original-title="'.esc_attr( $top_icon[ 'title' ] ).'">';
										if ( ! empty( $top_icon[ 'url' ] ) ) {
											echo '<img src="'.esc_url( $top_icon[ 'url' ] ).'"/>'; 
										} else {
											echo '<i class="'.esc_attr( $top_icon[ 'icon_o' ] ).'"></i>';
										}
									echo '</a></li>';
									$i ++;
								} ?>
							</ul>
						</div>
					<?php endif;

					if ( isset( $virtue_premium[ 'show_cartcount' ] ) ) {
						if( $virtue_premium[ 'show_cartcount' ] == '1' ) { 
							if ( class_exists( 'woocommerce' ) ) { ?>
								<ul class="kad-cart-total">
									<li>
										<a class="cart-contents" href="<?php echo wc_get_cart_url(); ?>" title="<?php esc_attr_e( 'View your shopping cart', 'virtue' ); ?>">
											<i class="icon-basket" style="padding-right:5px;"></i> 
											<?php if ( ! empty( $virtue_premium[ 'cart_placeholder_text' ] ) ) {
												echo $virtue_premium[ 'cart_placeholder_text' ];
											} else {
												echo __( 'Your Cart', 'virtue' );
											}  ?> 
											<span class="kad-cart-dash">-</span> 
											<?php if ( WC()->cart->tax_display_cart == 'incl' ) {
												echo WC()->cart->get_cart_subtotal(); 
											} else {
												echo WC()->cart->get_cart_total();
											} ?>
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
					if ( kadence_display_topbar_widget() ) {
						if ( is_active_sidebar( 'topbarright' ) ) {
							dynamic_sidebar( 'topbarright' );
						}
					} else {
						if( kadence_display_top_search() ) {
							get_search_form();
						}
					}
					?>
				</div>
			</div> <!-- close col-md-6-->
		</div> <!-- Close Row -->
		<?php if ( has_nav_menu( 'topbar_navigation' ) && isset( $virtue_premium[ 'topbar_mobile' ] ) && 1 == $virtue_premium[ 'topbar_mobile' ] ) : ?>
			<div id="kad-mobile-nav-top" class="kad-mobile-nav id-kad-mobile-nav">
				<div class="kad-nav-inner mobileclass">
					<div id="mobile_menu_collapse_top" class="kad-nav-collapse collapse top_mobile_menu_collapse">
						<?php 
						get_search_form(); 
						if ( isset( $virtue_premium[ 'mobile_submenu_collapse' ] ) && 1 == $virtue_premium[ 'mobile_submenu_collapse' ] ) {
							wp_nav_menu( array( 'theme_location' => 'topbar_navigation', 'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>', 'menu_class' => 'kad-top-mnav', 'walker' => new Virtue_Mobile_Nav_Walker() ) );
						} else {
							wp_nav_menu(array('theme_location' => 'topbar_navigation','items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>', 'menu_class' => 'kad-top-mnav'));
						}
						?>
					</div>
				</div>
			</div>
		<?php endif; ?>
	</div> <!-- Close Container -->
</div>