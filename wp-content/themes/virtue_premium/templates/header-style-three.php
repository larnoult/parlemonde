<?php 
global $virtue_premium; 

	if( isset( $virtue_premium[ 'header_height' ] ) ) {
		$header_height = $virtue_premium[ 'header_height' ];
	} else {
		$header_height = 90;
	}
	if( isset( $virtue_premium[ 'm_sticky_header' ] ) && $virtue_premium[ 'm_sticky_header' ] == '1' ) {
		$msticky = '1';
		$mstickyclass = 'mobile-stickyheader';
	} else {
		$msticky = '0';
		$mstickyclass = 'kt-not-mobile-sticky';
	}
	if( isset( $virtue_premium[ 'logo_layout' ] ) ) {
		if( $virtue_premium[ 'logo_layout' ] == 'logohalf' ) {
			$logocclass = 'col-md-6';
			$menulclass = 'col-md-6';
		} else {
			$logocclass = 'col-md-4';
			$menulclass = 'col-md-8';
		}
	} else {
		$logocclass = 'col-md-4';
		$menulclass = 'col-md-8';
	} ?>
<header id="kad-banner" class="banner headerclass kad-header-style-three <?php echo esc_attr( $mstickyclass );?>" data-header-shrink="1" data-mobile-sticky="<?php echo esc_attr( $msticky );?>" data-header-base-height="<?php echo esc_attr( $header_height );?>">
<?php if (kadence_display_topbar()) : 
      get_template_part( 'templates/header', 'topbar' ); 
      endif; ?>
  <div id="kad-shrinkheader" class="container" style="height:<?php echo esc_attr($header_height);?>px; line-height:<?php echo esc_attr($header_height);?>px; ">
    <div class="row">
          <div class="<?php echo esc_attr($logocclass); ?> clearfix kad-header-left">
            <div id="logo" class="logocase">
              <a class="brand logofont" style="height:<?php echo esc_attr($header_height);?>px; line-height:<?php echo esc_attr($header_height);?>px; display:block;" href="<?php echo esc_url(apply_filters('kadence_logo_link', home_url( '/' ) ) ); ?>" title="<?php bloginfo('name');?>">

                       <?php if (!empty($virtue_premium['x1_virtue_logo_upload']['url'])) { ?> <div id="thelogo" style="height:<?php echo esc_attr($header_height);?>px; line-height:<?php echo esc_attr($header_height);?>px;"><img src="<?php echo esc_url($virtue_premium['x1_virtue_logo_upload']['url']); ?>" alt="<?php  bloginfo('name');?>" style="max-height:<?php echo esc_attr($header_height);?>px" class="kad-standard-logo" />
                         <?php if(!empty($virtue_premium['x2_virtue_logo_upload']['url'])) {?> <img src="<?php echo esc_url($virtue_premium['x2_virtue_logo_upload']['url']);?>" alt="<?php  bloginfo('name');?>" class="kad-retina-logo" style="max-height:<?php echo esc_attr($header_height);?>px;" /> <?php } ?>
                        </div> <?php } else { echo apply_filters('kad_site_name', get_bloginfo('name')); } ?>
              </a>
           </div> <!-- Close #logo -->
       </div><!-- close col-md-4 -->

       <div class="<?php echo esc_attr($menulclass); ?> kad-header-right">
         <nav id="nav-main" class="clearfix nav-main">
          <?php
            if (has_nav_menu('primary_navigation')) :
              wp_nav_menu(array('theme_location' => 'primary_navigation', 'menu_class' => 'sf-menu')); 
            endif;
           ?>
         </nav> 
        </div> <!-- Close span7 -->       
    </div> <!-- Close Row -->
  </div> <!-- Close Container -->
  <?php if (has_nav_menu('mobile_navigation')) : ?>
    <?php if($virtue_premium['mobile_header'] == '1' && $virtue_premium['mobile_header_tablet_show'] == '1') { 
        } else {?>
  <div class="container kad-nav-three" >
           <div id="mobile-nav-trigger" class="nav-trigger mobile-nav-trigger-id">
              <button class="nav-trigger-case collapsed" data-toggle="collapse" rel="nofollow" data-target=".mobile_menu_collapse">
                <span class="kad-navbtn mobileclass clearfix"><i class="icon-menu"></i></span>
                <?php global $virtue_premium; if(!empty($virtue_premium['mobile_menu_text'])) {$menu_text = $virtue_premium['mobile_menu_text'];} else {$menu_text = __('Menu', 'virtue');} ?>
                <span class="kad-menu-name mobileclass"><?php echo $menu_text; ?></span>
              </button>
            </div>
            <div id="kad-mobile-nav" class="kad-mobile-nav id-kad-mobile-nav">
              <div class="kad-nav-inner mobileclass">
                <div id="mobile_menu_collapse" class="kad-nav-collapse collapse mobile_menu_collapse">
                <?php if(isset($virtue_premium['menu_search']) && $virtue_premium['menu_search'] == '1') {  
                    get_search_form(); 
                  } 
                  if(isset($virtue_premium['mobile_submenu_collapse']) && $virtue_premium['mobile_submenu_collapse'] == '1') {
                    wp_nav_menu( array('theme_location' => 'mobile_navigation','items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>', 'menu_class' => 'kad-mnav', 'walker' => new Virtue_Mobile_Nav_Walker()));
                  } else {
                    wp_nav_menu( array('theme_location' => 'mobile_navigation','items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>', 'menu_class' => 'kad-mnav'));
                  } ?>
               </div>
            </div>
          </div>
          </div> <!-- Close Container -->
          <?php  }
          endif; ?> 
          <?php do_action('kt_after_header_content'); ?>
</header>