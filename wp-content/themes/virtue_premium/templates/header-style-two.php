<header id="kad-banner" class="banner headerclass kad-header-style-two" data-header-shrink="0" data-mobile-sticky="0">
<?php if (kadence_display_topbar()) : ?>
  <?php get_template_part('templates/header', 'topbar'); ?>
<?php endif; ?>
<?php global $virtue_premium; 
      if(isset($virtue_premium['logo_layout'])) {
        if($virtue_premium['logo_layout'] == 'logocenter') {
            $logocclass = 'col-md-4 col-lg-2'; $menulclass = 'col-md-4 col-lg-5';
        } else if($virtue_premium['logo_layout'] == 'logohalf') {
          $logocclass = 'col-md-4'; $menulclass = 'col-md-4';
        } else {
          $logocclass = 'col-md-4'; $menulclass = 'col-md-4';
        }
      } else {
        $logocclass = 'col-md-4'; $menulclass = 'col-md-4';
      } ?>
  <div class="container">
    <div class="row">
      <div class="<?php echo esc_attr($menulclass); ?> kad-header-left">
       <?php do_action('kt_before_left_header_content'); ?>
           <nav class="nav-main" class="clearfix">
            <?php
              if (has_nav_menu('primary_navigation')) :
                wp_nav_menu(array('theme_location' => 'primary_navigation', 'menu_class' => 'sf-menu')); 
              endif;
             ?>
           </nav>
          <?php do_action('kt_after_left_header_content'); ?> 
        </div> <!-- Close left_menu -->  
          <div class="<?php echo esc_attr($logocclass); ?> clearfix kad-header-center">
            <div id="logo" class="logocase">
              <a class="brand logofont" href="<?php echo esc_url( apply_filters( 'kadence_logo_link', home_url( '/' ) ) ); ?>" title="<?php bloginfo('name');?>">
                       <?php global $virtue_premium; if (!empty($virtue_premium['x1_virtue_logo_upload']['url'])) { ?> <div id="thelogo"><img src="<?php echo $virtue_premium['x1_virtue_logo_upload']['url']; ?>" alt="<?php  bloginfo('name');?>" class="kad-standard-logo" />
                         <?php if(!empty($virtue_premium['x2_virtue_logo_upload']['url'])) {?> <img src="<?php echo $virtue_premium['x2_virtue_logo_upload']['url'];?>" alt="<?php  bloginfo('name');?>" class="kad-retina-logo" style="max-height:<?php echo $virtue_premium['x1_virtue_logo_upload']['height'];?>px" /> <?php } ?>
                        </div> <?php } else { echo apply_filters('kad_site_name', get_bloginfo('name')); } ?>
              </a>
              <?php global $virtue_premium; if ($virtue_premium['logo_below_text']) { ?> <p class="kad_tagline belowlogo-text"><?php echo $virtue_premium['logo_below_text']; ?></p> <?php }?>
           </div> <!-- Close #logo -->
       </div><!-- close logo container -->

       <div class="<?php echo esc_attr($menulclass); ?> kad-header-right">
        <?php do_action('kt_before_right_header_content'); ?>
           <nav class="nav-main clearfix">
            <?php
              if (has_nav_menu('secondary_navigation')) :
                wp_nav_menu(array('theme_location' => 'secondary_navigation', 'menu_class' => 'sf-menu')); 
              endif;
             ?>
         </nav> 
          <?php do_action('kt_after_right_header_content'); ?>
        </div> <!-- Close right_menu -->       
    </div> <!-- Close Row -->
    <?php if (has_nav_menu('mobile_navigation')) : ?>
      <?php if($virtue_premium['mobile_header'] == '1' && $virtue_premium['mobile_header_tablet_show'] == '1') { 
        } else {?>
           <div id="mobile-nav-trigger" class="nav-trigger mobile-nav-trigger-id">
              <button class="nav-trigger-case mobileclass collapsed" data-toggle="collapse" rel="nofollow" data-target=".mobile_menu_collapse">
                <span class="kad-navbtn clearfix"><i class="icon-menu"></i></span>
                <?php global $virtue_premium; if(!empty($virtue_premium['mobile_menu_text'])) {$menu_text = $virtue_premium['mobile_menu_text'];} else {$menu_text = __('Menu', 'virtue');} ?>
                <span class="kad-menu-name"><?php echo $menu_text; ?></span>
              </button>
            </div>
            <div id="kad-mobile-nav" class="kad-mobile-nav id-kad-mobile-nav">
              <div class="kad-nav-inner mobileclass">
                <div id="mobile_menu_collapse" class="kad-nav-collapse collapse mobile_menu_collapse">
                <?php do_action('kt_mobile_menu_before'); ?>
                 <?php if(isset($virtue_premium['mobile_submenu_collapse']) && $virtue_premium['mobile_submenu_collapse'] == '1') {
                    wp_nav_menu( array('theme_location' => 'mobile_navigation','items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>', 'menu_class' => 'kad-mnav', 'walker' => new Virtue_Mobile_Nav_Walker()));
                  } else {
                    wp_nav_menu( array('theme_location' => 'mobile_navigation','items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>', 'menu_class' => 'kad-mnav'));
                  } ?>
                  <?php do_action('kt_mobile_menu_after'); ?>
               </div>
            </div>
          </div>   
          <?php }
           endif; ?> 
  </div> <!-- Close Container -->
     <?php global $virtue_premium; if (!empty($virtue_premium['virtue_banner_upload']['url'])) {  ?> 
        <div class="container virtue_sitewide_banner"><div class="virtue_banner">
          <?php if (!empty($virtue_premium['virtue_banner_link'])) { ?> <a href="<?php echo $virtue_premium['virtue_banner_link'];?>"> <?php }?>
          <img src="<?php echo $virtue_premium['virtue_banner_upload']['url']; ?>" /></div>
          <?php if (!empty($virtue_premium['virtue_banner_link'])) { ?> </a> <?php }?>
        </div> <?php } ?>
        <?php do_action('kt_after_header_content'); ?>
</header>