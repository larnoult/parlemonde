<header class="banner headerclass" role="banner">
<?php if (kadence_display_topbar()) : ?>
  <section id="topbar" class="topclass">
    <div class="container">
      <div class="row">
        <div class="col-md-6 col-sm-6 kad-topbar-left">
          <div class="topbarmenu clearfix">
          <?php if (has_nav_menu('topbar_navigation')) :
              wp_nav_menu(array('theme_location' => 'topbar_navigation', 'menu_class' => 'sf-menu'));
            endif;?>
            <?php if(kadence_display_topbar_icons()) : ?>
            <div class="topbar_social">
              <ul>
                <?php global $virtue; $top_icons = $virtue['topbar_icon_menu'];
                foreach ($top_icons as $top_icon) {
                  if(!empty($top_icon['target']) && $top_icon['target'] == 1) {$target = '_blank';} else {$target = '_self';}
                  echo '<li><a href="'.$top_icon['link'].'" target="'.$target.'" title="'.esc_attr($top_icon['title']).'" data-toggle="tooltip" data-placement="bottom" data-original-title="'.esc_attr($top_icon['title']).'">';
                  if($top_icon['url'] != '') echo '<img src="'.$top_icon['url'].'"/>' ; else echo '<i class="'.$top_icon['icon_o'].'"></i>';
                  echo '</a></li>';
                } ?>
              </ul>
            </div>
          <?php endif; ?>
            <?php global $virtue; if(isset($virtue['show_cartcount'])) {
               if($virtue['show_cartcount'] == '1') { 
                if (class_exists('woocommerce')) {
                  global $woocommerce; ?>
                    <ul class="kad-cart-total">
                      <li>
                      <a class="cart-contents" href="<?php echo $woocommerce->cart->get_cart_url(); ?>" title="<?php esc_attr_e('View your shopping cart', 'woocommerce'); ?>">
                          <i class="icon-shopping-cart" style="padding-right:5px;"></i> <?php _e('Your Cart', 'virtue');?> - <?php echo $woocommerce->cart->get_cart_total(); ?>
                      </a>
                    </li>
                  </ul>
                <?php } } }?>
          </div>
        </div><!-- close col-md-6 --> 
        <div class="col-md-6 col-sm-6 kad-topbar-right">
          <div id="topbar-search" class="topbar-widget">
            <?php if(kadence_display_topbar_widget()) { if(is_active_sidebar('topbarright')) { dynamic_sidebar(__('Topbar Widget', 'virtue')); } 
              } else { if(kadence_display_top_search()) {get_search_form();} 
          } ?>
        </div>
        </div> <!-- close col-md-6-->
      </div> <!-- Close Row -->
    </div> <!-- Close Container -->
  </section>
<?php endif; ?>
<?php global $virtue; if(isset($virtue['logo_layout'])) {
  if($virtue['logo_layout'] == 'logocenter') {$logocclass = 'col-md-12'; $menulclass = 'col-md-12';}
  else if($virtue['logo_layout'] == 'logohalf') {$logocclass = 'col-md-6'; $menulclass = 'col-md-6';}
  else {$logocclass = 'col-md-4'; $menulclass = 'col-md-8';} 
} else {$logocclass = 'col-md-4'; $menulclass = 'col-md-8'; }?>
  <div class="container">
    <div class="row">
          <div class="<?php echo $logocclass; ?>  clearfix kad-header-left">
            <div id="logo" class="logocase">
              <a class="brand logofont" href="<?php echo home_url(); ?>/">
                      <?php global $virtue; if (!empty($virtue['x1_virtue_logo_upload']['url'])) { ?> <div id="thelogo"><img src="<?php echo $virtue['x1_virtue_logo_upload']['url']; ?>" alt="<?php  bloginfo('name');?>" class="kad-standard-logo" />
                         <?php if(!empty($virtue['x2_virtue_logo_upload']['url'])) {?> <img src="<?php echo $virtue['x2_virtue_logo_upload']['url'];?>" class="kad-retina-logo" style="max-height:<?php echo $virtue['x1_virtue_logo_upload']['height'];?>px" /> <?php } ?>
                        </div> <?php } else { bloginfo('name'); } ?>
                        </a>
              <?php if (isset($virtue['logo_below_text'])) { ?> <p class="kad_tagline belowlogo-text"><?php echo $virtue['logo_below_text']; ?></p> <?php }?>
           </div> <!-- Close #logo -->
       </div><!-- close logo span -->
		
		<a href="https://twitter.com/share?text=Un+voyage+num%C3%A9rique+%C3%A0+la+d%C3%A9couverte+de+soi+et+des+autres%2C+par+et+pour+les+futurs+citoyens+du+monde+%21&amp;via=PelicoPLM&amp;related=PelicoPLM&amp;url=http://www.parlemonde.org" target="_blank">
		<div id="tweet-that"> 
		Un voyage numérique à la découverte de soi et des autres, par et pour les futurs citoyens du monde ! 
			<img src="http://www.parlemonde.org/wp-content/uploads/2015/01/Twitter_logo_blue.png">	
		</div>
		</a>
		
		 <?php if ( !is_user_logged_in() ) { ?><img src="http://www.parlemonde.org/wp-content/uploads/2015/02/namibie.png" id="ou-Header"> <?php } ?>  	<!--  Où sommes nous highlight-->
		
	   <div id="supportPLM"><a href="http://www.parlemonde.org/nous-soutenir/">  <!-- div support stickers -->

		<div id="Awesome" class="anim750">

		  <div class="reveal circle_wrapper">
				<div class="circle">Support us!</div>
			</div>

			<div class="sticky anim750">
				<div class="front circle_wrapper anim750">
					<div class="circle anim750"></div>
			  </div>
			</div>

		  <span id="SupportFrench">Soutenez-nous !</span>

		  <div class="sticky anim750">
				<div class="back circle_wrapper anim750">
					<div class="circle anim750"></div>
				</div>
			</div>

		</div>

	</a></div> <!-- close div support stickers -->
	
	

       <div class="<?php echo $menulclass; ?> kad-header-right">
         <nav id="nav-main" class="clearfix" role="navigation">
          <?php
            if (has_nav_menu('primary_navigation')) :
              wp_nav_menu(array('theme_location' => 'primary_navigation', 'menu_class' => 'sf-menu')); 
            endif;
           ?>
		<?php if (is_front_page()) {echo '<div class="low-width" style="padding-bottom:15px">' ; echo do_shortcode('[testimonials_slider category=\"voyage-quote\" order=asc]'); echo ' <div class="HomeIframe"> 
			<iframe src="//player.vimeo.com/video/115915382" width="600" height="337" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
			</div></div>';}?>
         </nav> 
        </div> <!-- Close span7 -->       
    </div> <!-- Close Row -->
    <?php if (has_nav_menu('mobile_navigation')) : ?>
           <div id="mobile-nav-trigger" class="nav-trigger">
              <a class="nav-trigger-case mobileclass collapsed" rel="nofollow" data-toggle="collapse" data-target=".kad-nav-collapse">
                <div class="kad-navbtn"><i class="icon-reorder"></i></div>
                <div class="kad-menu-name"><?php echo __('Menu', 'virtue'); ?></div>
              </a>
            </div>
            <div id="kad-mobile-nav" class="kad-mobile-nav">
              <div class="kad-nav-inner mobileclass">
                <div class="kad-nav-collapse">
                 <?php wp_nav_menu( array('theme_location' => 'mobile_navigation','items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>', 'menu_class' => 'kad-mnav')); ?>
               </div>
            </div>
          </div>   
          <?php  endif; ?> 
  </div> <!-- Close Container -->
  <?php
            if (has_nav_menu('secondary_navigation')) : ?>
  <section id="cat_nav" class="navclass">
    <div class="container">
     <nav id="nav-second" class="clearfix" role="navigation">
     <?php wp_nav_menu(array('theme_location' => 'secondary_navigation', 'menu_class' => 'sf-menu')); ?>
   </nav>
    </div><!--close container-->
    </section>
    <?php endif; ?> 
     <?php global $virtue; if (!empty($virtue['virtue_banner_upload']['url'])) { ?> <div class="container"><div class="virtue_banner"><img src="<?php echo $virtue['virtue_banner_upload']['url']; ?>" /></div></div> <?php } ?>
</header>