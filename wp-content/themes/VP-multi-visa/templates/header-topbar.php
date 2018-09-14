<?php global $virtue_premium; ?>
<div id="topbar" class="topclass">
    <div class="container">
      <div class="row">
        <div class="col-md-6 col-sm-6 kad-topbar-left">
          <div class="topbarmenu clearfix">
          <?php if (has_nav_menu('topbar_navigation')) :
              wp_nav_menu(array('theme_location' => 'topbar_navigation', 'menu_class' => 'sf-menu'));
              if(isset($virtue_premium['topbar_mobile']) && $virtue_premium['topbar_mobile'] == 1) { ?>
                <div id="mobile-nav-trigger" class="nav-trigger">
                  <a class="nav-trigger-case" data-toggle="collapse" rel="nofollow" data-target=".top_mobile_menu_collapse">
                    <div class="kad-navbtn clearfix"><i class="icon-menu"></i></div>
                  </a>
                </div>
                <?php }
            endif;?>
            <?php if(kadence_display_topbar_icons()) : ?>
            <div class="topbar_social">
              <ul>
                <?php $top_icons = $virtue_premium['topbar_icon_menu'];
                $i = 1;
                foreach ($top_icons as $top_icon) {
                  if(!empty($top_icon['target']) && $top_icon['target'] == 1) {$target = '_blank';} else {$target = '_self';}
                  echo '<li><a href="'.$top_icon['link'].'" data-toggle="tooltip" data-placement="bottom" target="'.$target.'" class="topbar-icon-'.$i.'" data-original-title="'.esc_attr($top_icon['title']).'">';
                  if($top_icon['url'] != '') echo '<img src="'.esc_url($top_icon['url']).'"/>' ; else echo '<i class="'.$top_icon['icon_o'].'"></i>';
                  echo '</a></li>';
                $i ++;
                } ?>
              </ul>
            </div>
          <?php endif; ?>
            <?php if(isset($virtue_premium['show_cartcount'])) {
               if($virtue_premium['show_cartcount'] == '1') { 
                if (class_exists('woocommerce')) {
                 global $virtue_premium, $woocommerce;
                  ?>
                    <ul class="kad-cart-total">
                      <li>
                      <a class="cart-contents" href="<?php echo $woocommerce->cart->get_cart_url(); ?>" title="<?php esc_attr_e('View your shopping cart', 'virtue'); ?>">
                          <i class="icon-basket" style="padding-right:5px;"></i> 
                          <?php if(!empty($virtue_premium['cart_placeholder_text'])) {
                            echo $virtue_premium['cart_placeholder_text'];
                          } else {
                            echo __('Your Cart', 'virtue');
                          }  ?> 
                          <span class="kad-cart-dash">-</span> 
                          <?php if ( WC()->cart->tax_display_cart == 'incl' ) {
                            echo WC()->cart->get_cart_subtotal(); 
                          } else {
                            echo WC()->cart->get_cart_total();
                          }?>
                      </a>
                    </li>
                  </ul>
                <?php } } }?>
          </div>
        </div><!-- close col-md-6 -->
        <div class="col-md-6 col-sm-6 kad-topbar-right">
          <div id="topbar-search" class="topbar-widget">
            <?php if(kadence_display_topbar_widget()) { if(is_active_sidebar('topbarright')) { dynamic_sidebar('topbarright'); } 
              } else { if(kadence_display_top_search()) {get_search_form();} 
          } ?>
        </div>
        </div> <!-- close col-md-6-->
		<div id="welcome-plm"> Vous visionnez les anciens reportages de Pelico !</div>

      </div> <!-- Close Row -->
      <?php if (has_nav_menu('topbar_navigation') && isset($virtue_premium['topbar_mobile']) && $virtue_premium['topbar_mobile'] == 1) :?>
     <div id="kad-mobile-nav" class="kad-mobile-nav">
              <div class="kad-nav-inner mobileclass">
                <div id="mobile_menu_collapse_top" class="kad-nav-collapse collapse top_mobile_menu_collapse">
                 <?php get_search_form(); ?>
                 <?php if(isset($virtue_premium['mobile_submenu_collapse']) && $virtue_premium['mobile_submenu_collapse'] == '1') {
                    wp_nav_menu( array('theme_location' => 'topbar_navigation','items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>', 'menu_class' => 'kad-top-mnav', 'walker' => new kadence_mobile_walker()));
                  } else {
                    wp_nav_menu(array('theme_location' => 'topbar_navigation','items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>', 'menu_class' => 'kad-top-mnav'));
                  } ?>
               </div>
            </div>
          </div>
    <?php endif;?>
    </div> <!-- Close Container -->
  </div>