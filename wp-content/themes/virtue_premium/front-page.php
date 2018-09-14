<?php
/** 
 * Template for the front page
 * 
 */
global $virtue_premium;

$mobile_detect = false;
if ( isset( $virtue_premium[ 'mobile_switch' ] ) && 1 == $virtue_premium[ 'mobile_switch' ] ) {
	$mobile_slider = true;
	$detect = new Mobile_Detect_Virtue; 
	if ( isset( $virtue_premium[ 'mobile_tablet_show' ] ) && 1 == $virtue_premium[ 'mobile_tablet_show' ] ) {
		if( $detect->isMobile() ) {
			$mobile_detect = true;
		} else {
			$mobile_detect = false;
		}
	} else {
		if( $detect->isMobile() && ! $detect->isTablet() ) {
			$mobile_detect = true;
		} else {
			$mobile_detect = false;
		}
	}
} else {
	$mobile_slider = false;
}
if ( true == $mobile_slider && true == $mobile_detect ) {
	if( ( isset( $virtue_premium[ 'above_header_slider' ] ) && $virtue_premium[ 'above_header_slider' ] == 1 ) && isset( $virtue_premium[ 'choose_slider' ] ) && ( $virtue_premium[ 'choose_slider' ] == 'ktslider' || $virtue_premium[ 'choose_slider' ] == 'cyclone' || $virtue_premium[ 'choose_slider' ] == 'rev' ||  $virtue_premium[ 'choose_slider' ] == 'ksp' ) )  {
		// do nothing
	} else {
 		$slider = $virtue_premium[ 'choose_mobile_slider' ];
		if ($slider == "rev") {
			get_template_part('templates/mobile_home/mobilerev', 'slider');
		} else if ($slider == "ksp") {
			get_template_part('templates/mobile_home/mobileksp', 'slider');
		} else if ($slider == "flex") {
			get_template_part('templates/mobile_home/mobileflex', 'slider');
		} else if ($slider == "video") {
			get_template_part('templates/mobile_home/mobilevideo', 'block');
		} else if ($slider == "cyclone") {
			get_template_part('templates/mobile_home/cyclone', 'slider');
		}
	}
} else { 
  	if(isset($virtue_premium['choose_slider'])) { 
  		$slider = $virtue_premium['choose_slider'];
  	} else {
  		$slider = 'none';
  	}
	if ($slider == "rev") {
			if($virtue_premium['above_header_slider'] != 1) {
				get_template_part('templates/home/rev', 'slider');
			}
	} else if ($slider == "ktslider") {
			if($virtue_premium['above_header_slider'] != 1) {
				get_template_part('templates/home/kt', 'slider');
			}
	} else if ($slider == "ksp") {
			if($virtue_premium['above_header_slider'] != 1) {
				get_template_part('templates/home/ksp', 'slider');
			}
	} else if ($slider == "flex") {
		get_template_part('templates/home/flex', 'slider');
	} else if ($slider == "carousel") {
		get_template_part('templates/home/carousel', 'slider');
	} else if ($slider == "fullwidth") {
		get_template_part('templates/home/flex', 'slider-fullwidth');
	} else if ($slider == "imgcarousel") {
		get_template_part('templates/home/image', 'carousel');
	} else if ($slider == "latest") {
		get_template_part('templates/home/latest', 'slider');
	} else if ($slider == "thumbs") {
		get_template_part('templates/home/thumb', 'slider');
	} else if ($slider == "cyclone") {
		if($virtue_premium['above_header_slider'] != 1) {
			get_template_part('templates/home/cyclone', 'slider');
		}
	} else if ($slider == "fullwidth") {
		get_template_part('templates/home/fullwidth', 'slider');
	} else if ($slider == "video") {
		get_template_part('templates/home/video', 'block');
	}
}
$show_pagetitle = false;
if ( isset( $virtue_premium[ 'homepage_layout' ][ 'enabled' ] ) ) {
	$i = 0;
	foreach ( $virtue_premium[ 'homepage_layout' ][ 'enabled' ] as $key=>$value ) {
		if( 'block_one' == $key ) {
			$show_pagetitle = true;
		}
		$i++;
		if( $i==2 ) break;
	}
}
if($show_pagetitle == true) { ?>
	<div id="homeheader" class="welcomeclass">
			<?php get_template_part('templates/page', 'header'); ?>
	</div><!--titleclass-->
<?php 
}
?>

<div id="content" class="container homepagecontent <?php echo esc_attr( virtue_container_class() ); ?>">
	<div class="row">
		<div class="main <?php echo esc_attr( virtue_main_class() ); ?>" role="main">
			<div class="entry-content" itemprop="mainContentOfPage">
				<?php if ( isset( $virtue_premium[ 'homepage_layout' ][ 'enabled' ] ) ) {
					$layout = $virtue_premium[ 'homepage_layout' ][ 'enabled' ];
				} else {
					$layout = array( "block_one" => "block_one", "block_four" => "block_four" );
				}

				if ( $layout ):

				foreach ( $layout as $key=>$value ) {

				    switch( $key ) {

				    	case 'block_one':?>
				    	<?php if( $show_pagetitle == false ) {?>
							<div id="homeheader" class="welcomeclass">
								<?php get_template_part('templates/page', 'header'); ?>
							</div><!--titleclass-->
							<?php }?>
					    <?php 
					    break;
					    case 'block_two': 
				    		get_template_part('templates/home/image', 'menu');
					    break;
						case 'block_three':
							if (class_exists('woocommerce'))  {
								get_template_part('templates/home/product', 'carousel');
							}
						break;
						case 'block_four': ?>
							<?php if ( is_home() ) {
								if ( virtue_display_sidebar() ) {
								 $display_sidebar = true; 
								      $fullclass = '';
								      global $kt_post_with_sidebar; 
								      $kt_post_with_sidebar = true;
								   } else {
								      $display_sidebar = false; 
								      $fullclass = 'fullwidth';
								      global $kt_post_with_sidebar; 
								      $kt_post_with_sidebar = false;
								   }
								if ( isset( $virtue_premium[ 'home_post_summery' ] ) and ( $virtue_premium[ 'home_post_summery' ] == 'full' ) ) {
									$summery = "full";
									$postclass = "single-article fullpost";
								} else {
									$summery = "summery";
									$postclass = "postlist";
								} 
								if( isset( $virtue_premium[ 'home_post_grid' ] ) && $virtue_premium[ 'home_post_grid' ] == '1' ) {
									$grid = true;
									$postclass = "postlist";
								} else {
									$grid = false;
								}
								if( isset( $virtue_premium[ 'virtue_animate_in' ]) && $virtue_premium[ 'virtue_animate_in' ] == 1 ) {
									$animate = 1;
								} else {
									$animate = 0;
								}  
								if( $grid == true ) {
									$blog_grid_column = $virtue_premium[ 'home_post_grid_columns' ];
									if ( $blog_grid_column == 'twocolumn' ) {
										$itemsize = 'tcol-md-6 tcol-sm-6 tcol-xs-12 tcol-ss-12';
									} else if ( $blog_grid_column == 'threecolumn' ){
										$itemsize = 'tcol-md-4 tcol-sm-4 tcol-xs-6 tcol-ss-12';
									} else {
										$itemsize = 'tcol-md-3 tcol-sm-4 tcol-xs-6 tcol-ss-12';
									}
									if ( isset( $virtue_premium[ 'blog_grid_display_height' ] ) && 1 == $virtue_premium[ 'blog_grid_display_height' ] ) {
						            	$matchheight = 1;
							        } else {
							            $matchheight = 0;
							        }
								}
								if( isset( $virtue_premium[ 'blog_infinitescroll' ] ) && '1' == $virtue_premium[ 'blog_infinitescroll' ] ) {
									wp_enqueue_script( 'virtue-infinite-scroll' );
									if ( $grid == true ) {
										$infinit = 'data-nextselector=".wp-pagenavi a.next" data-navselector=".wp-pagenavi" data-itemselector=".kad_blog_item" data-itemloadselector=".kad_blog_fade_in"';
										$scrollclass = 'init-infinit';
									} else {
										$infinit = 'data-nextselector=".wp-pagenavi a.next" data-navselector=".wp-pagenavi" data-itemselector=".post" data-itemloadselector=".kad-animation"';
										$scrollclass = 'init-infinit-norm';
							    	}
								} else {
									$infinit = '';
									$scrollclass = '';
							    }?>
								<div id="homelatestposts" class="homecontent <?php echo esc_attr( $fullclass ); ?>  <?php echo esc_attr( $postclass ); ?> clearfix home-margin"  data-fade-in="<?php echo esc_attr( $animate );?>"> 
							    	<?php if($summery == 'full') { ?>
							    		<div class="kt_home_archivecontent <?php echo esc_attr( $scrollclass ); ?>" <?php echo wp_kses_post( $infinit ); ?>> 
							    		<?php while (have_posts()) : the_post(); 
											get_template_part('templates/content', 'fullpost'); 
											endwhile; ?>
										</div> 
									<?php 
									} else {
										if($grid == true) { ?>
											<div id="kad-blog-grid" class="rowtight kt_home_archivecontent <?php echo esc_attr($scrollclass); ?> init-isotope" data-iso-match-height="<?php echo esc_attr($matchheight);?>" <?php echo $infinit; ?> data-fade-in="<?php echo esc_attr($animate);?>"  data-iso-selector=".b_item" data-iso-style="masonry">
													<?php while (have_posts()) : the_post(); ?>
														<?php if($blog_grid_column == 'twocolumn') { ?>
															<div class="<?php echo esc_attr($itemsize);?> b_item kad_blog_item">
															<?php get_template_part('templates/content', 'twogrid'); ?>
															</div>
														<?php } else {?>
															<div class="<?php echo esc_attr($itemsize);?> b_item kad_blog_item">
															<?php get_template_part('templates/content', 'fourgrid');?>
															</div>
														<?php } ?>
													<?php endwhile; ?>
											</div>

										<?php } else {?>
											<div class="kt_home_archivecontent <?php echo esc_attr( $scrollclass ); ?>" <?php echo wp_kses_post( $infinit ); ?>> 
												<?php while (have_posts()) : the_post(); ?>
													<?php get_template_part('templates/content', get_post_format()); ?>
												<?php endwhile; ?>
											</div>
										<?php
										}
									}?>
							</div> 
							<?php 	/*
									* @hoooked virtue_pagination_markup - 20;
									*/
									do_action( 'virtue_pagination' );
							
							} else { ?>
								<div class="homecontent clearfix home-margin"> 
									<?php get_template_part('templates/content', 'page'); ?>
								</div>
						<?php 	}
						break;
						case 'block_five':
							 	get_template_part('templates/home/blog', 'home'); 
						break;
						case 'block_six':
								get_template_part('templates/home/portfolio', 'carousel');		 
						break; 
						case 'block_seven':
								get_template_part('templates/home/icon', 'menu');		 
						break;
						case 'block_eight':
								get_template_part('templates/home/portfolio', 'full');		 
						break; 
						case 'block_nine':
							if (class_exists('woocommerce'))  {
								get_template_part('templates/home/product-sale', 'carousel');
							}	 
						break; 
						case 'block_ten':
							if (class_exists('woocommerce'))  {
								get_template_part('templates/home/product-best', 'carousel');
							}	 
						break; 
						case 'block_eleven':
								get_template_part('templates/home/custom', 'carousel');		 
						break; 
						case 'block_twelve':
								get_template_part('templates/home/widget', 'box');		 
						break;  
					    }

}
endif; ?>   
</div>

</div><!-- /.main -->