<header id="kad-banner" class="banner headerclass" role="banner" data-header-shrink="0" data-mobile-sticky="0">
<?php if (kadence_display_topbar()) : ?>
  <?php get_template_part('templates/header', 'topbar'); ?>
<?php endif; ?>
<?php global $virtue_premium; if(isset($virtue_premium['logo_layout'])) {
            if($virtue_premium['logo_layout'] == 'logocenter') {$logocclass = 'col-md-12'; $menulclass = 'col-md-12';} 
            else if($virtue_premium['logo_layout'] == 'logohalf') {$logocclass = 'col-md-6'; $menulclass = 'col-md-6';}
            else if($virtue_premium['logo_layout'] == 'logowidget') {$logocclass = 'col-md-4'; $menulclass = 'col-md-12';}
            else {$logocclass = 'col-md-4'; $menulclass = 'col-md-8';}
          }
          else {$logocclass = 'col-md-4'; $menulclass = 'col-md-8';} ?>
  <div class="container">
    <div class="row">
          <div class="<?php echo $logocclass; ?> clearfix kad-header-left">
            <div id="logo" class="logocase">

				<!-- div support stickers -->	 	<!--  <div id="supportPLM"><a href="http://www.parlemonde.org/jinscris-ma-classe/" target="_blank">  

						<div id="Awesome" class="anim750">

						  <div class="reveal circle_wrapper">
								<div class="circle">Pour l'année <br> 
								2015-16</div>
							</div>

							<div class="sticky anim750">
								<div class="front circle_wrapper anim750">
									<div class="circle anim750"></div>
							  </div>
							</div>

						  <span id="SupportFrench">Inscrivez-vous !</span>

						  <div class="sticky anim750">
								<div class="back circle_wrapper anim750">
									<div class="circle anim750"></div>
								</div>
							</div>

						</div>

					</a></div>   --> <!-- close div support stickers -->



					<!-- EDF vote 
 				<div id="wrapEDF">
<iframe id="EDF" src="http://tropheesfondation.edf.com/domains-assets/tda/WidgetVote.aspx?domain=tropheesfondation&assoId=53166" width="330" height="235" style="border:none;overflow:hidden;"></iframe>
				</div>   -->
				
              <a class="brand logofont" href="<?php echo home_url(); ?>/">
                       <?php if (!empty($virtue_premium['x1_virtue_logo_upload']['url'])) { ?> 
                       <div id="thelogo"><img src="<?php echo $virtue_premium['x1_virtue_logo_upload']['url']; ?>" alt="<?php  bloginfo('name');?>" class="kad-standard-logo" />
                         <?php if(!empty($virtue_premium['x2_virtue_logo_upload']['url'])) {?>
                          <img src="<?php echo $virtue_premium['x2_virtue_logo_upload']['url'];?>" class="kad-retina-logo" alt="<?php  bloginfo('name');?>" style="max-height:<?php echo $virtue_premium['x1_virtue_logo_upload']['height'];?>px" /> <?php } ?>
                        </div> <?php } else { bloginfo('name'); } ?>
              </a>
              <?php if ($virtue_premium['logo_below_text']) { ?> <p class="kad_tagline belowlogo-text"><?php echo $virtue_premium['logo_below_text']; ?></p> <?php }?>

			</div> <!-- Close #logo -->
			
       </div><!-- close col-md-4 -->

		 <?php if ( !is_user_logged_in() ) { ?>	<img src="http://www.parlemonde.fr/wp-content/uploads/2015/01/pour-les-grands.png" id="grand-Header"> <?php } ?>   	<!--  Pour les grands !-->

	 
									
        <?php if(isset($virtue_premium['logo_layout']) && $virtue_premium['logo_layout'] == 'logowidget') {
          ?> <div class="col-md-8 kad-header-widget"> <?php 
                if(is_active_sidebar('headerwidget')) { dynamic_sidebar('headerwidget'); } 
                ?> </div></div><div class="row"> <?php
             }?>
       <div class="<?php echo $menulclass; ?> kad-header-right">
        <?php do_action( 'virtue_above_primarymenu' ); ?>
         <nav id="nav-main" class="clearfix" role="navigation">
          <?php
            if (has_nav_menu('primary_navigation')) :
              wp_nav_menu(array('theme_location' => 'primary_navigation', 'menu_class' => 'sf-menu')); 
            endif;
           ?>
<!--
		<?php if (is_front_page()) {echo '<img id=\'pelicoHomeFr\' src=\'http://www.parlemonde.fr/wp-content/uploads/2014/08/Pelico-ou2.png\'> 
		<div id=\'pelicoTravels\'>
		<p><strong>Dans le pays mystère n°4 </strong> <br> du 3 juin aux vacances d\'été 2016 </p>
		<a href=http://www.parlemonde.fr/category/le-journal-de-pelico/le-journal-de-pelico-dans-les-pays-mysteres/le-journal-de-pelico-dans-le-pays-mystere-n4/><img style="width: 80%" src=http://www.parlemonde.fr/wp-content/uploads/2015/08/pays-mystere-4.png> </a>
		<p>Il est <strong> ... mystère pour le moment !</strong></p>
		</div>
		<div class="low-width-2" style="padding-bottom:15px"> ' ; echo do_shortcode('[mapsmarker layer="31"]');  echo ' </div> '; 

		}?> -->
		
		<?php if (is_front_page()) {echo '<img id=\'pelicoHomeFr\' src=\'http://www.parlemonde.fr/wp-content/uploads/2014/08/Pelico-ou2.png\'> 
			<div id=\'pelicoTravels\'>
			<p style=\'margin-top:70px;\'><strong>Découvrez en vidéo le dernier mot de Pelico, Laurent et Marylène ! </strong> <br> 
			<p style=\'margin-top:40px;\'> À bientôt pour de nouvelles aventures <span class=\'plm\'>Par Le Monde</span> !!</p>
			</div>
			<div class="low-width-2" style="padding-bottom:15px"> 
			<iframe src="https://player.vimeo.com/video/173285477" width="672" height="378" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
			</div> '; 

			}?> 
		
         </nav> 
        </div> <!-- Close span7 -->       
    </div> <!-- Close Row -->
    <?php if (has_nav_menu('mobile_navigation')) : ?>
           <div id="mobile-nav-trigger" class="nav-trigger">
              <a class="nav-trigger-case mobileclass" data-toggle="collapse" rel="nofollow" data-target=".kad-nav-collapse">
                <div class="kad-navbtn clearfix"><i class="icon-menu"></i></div>
                <?php if(!empty($virtue_premium['mobile_menu_text'])) {$menu_text = $virtue_premium['mobile_menu_text'];} else {$menu_text = __('Menu', 'virtue');} ?>
                <div class="kad-menu-name"><?php echo $menu_text; ?></div>
              </a>
            </div>
            <div id="kad-mobile-nav" class="kad-mobile-nav">
              <div class="kad-nav-inner mobileclass">
                <div id="mobile_menu_collapse" class="kad-nav-collapse collapse mobile_menu_collapse">
                 <?php wp_nav_menu( array('theme_location' => 'mobile_navigation','items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>', 'menu_class' => 'kad-mnav')); ?>
               </div>
            </div>
          </div>   
          <?php  endif; ?> 
  </div> <!-- Close Container -->
  <?php if (has_nav_menu('secondary_navigation')) : ?>
  <section id="cat_nav" class="navclass">
    <div class="container">
     <nav id="nav-second" class="clearfix" role="navigation">
     <?php wp_nav_menu(array('theme_location' => 'secondary_navigation', 'menu_class' => 'sf-menu')); ?>
   </nav>
    </div><!--close container-->
    </section>
    <?php endif; ?> 
      <?php if (!empty($virtue_premium['virtue_banner_upload']['url'])) {  ?> 
        <div class="container virtue_sitewide_banner"><div class="virtue_banner">
          <?php if (!empty($virtue_premium['virtue_banner_link'])) { ?> <a href="<?php echo $virtue_premium['virtue_banner_link'];?>"> <?php }?>
          <img src="<?php echo $virtue_premium['virtue_banner_upload']['url']; ?>" /></div>
          <?php if (!empty($virtue_premium['virtue_banner_link'])) { ?> </a> <?php }?>
        </div> <?php } ?>


				<div id="menu-fixed">
				    <ul>
				      <li class="blog" style="right: 0px;"><a href="http://www.parlemonde.fr/category/les-enigmes-de-pelico/"><div class="icone" id="img_enigme"></div><div>Énigme</div></a></li>
				      <li class="work" style="right: 0px;"><a href="http://www.parlemonde.fr/tous-les-reportages-des-classes-2015-16/"><div class="icone" id="img_france"></div><div>France</div></a></li>
			      	<li class="signin" style="right: 0px;"><a href="http://www.parlemonde.fr/bolivie/"><div class="icone" id="img_bolivie"></div><div>Bolivie</div></a></li>
 		      		<li class="donation" style="right: 0px;"><a href="http://www.parlemonde.fr/madagascar/"><div class="icone" id="img_madagascar"></div><div>Madagascar</div></a></li>
				  <li class="" style="right: 0px;"><a href="http://www.parlemonde.fr/oman/"><div class="icone" id="img_oman"></div><div>Oman</div></a></li>
	    		<li class="" style="right: 0px;"><a href="http://www.parlemonde.fr/espagne/"><div class="icone" id="img_espagne"></div><div>Espagne</div></a></li> 
			      <li class="journalPelico" style="right: 0px;"><a href="http://www.parlemonde.fr/category/le-journal-de-pelico/"><div class="icone" id="img_journal"></div><div>Journal</div></a></li>  
		     	      <li class="contact" style="right: 0px;"><a href="http://www.parlemonde.fr/wp-admin/"><div class="icone" id="img_prof"></div><div>Coin des profs</div></a></li>  
		<!-- 	      <li class="contact" style="right: 0px;"><a href="http://www.actioncontrelafaim.org/fr/content/nous-contacter"><div class="icone" id="img_contact"></div><div></div></a></li>-->  
				    </ul>
				  </div>
</header>