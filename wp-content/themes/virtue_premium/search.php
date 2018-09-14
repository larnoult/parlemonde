<?php 
/**
 * @hooked virtue_page_title - 20
 */
do_action('kadence_page_title_container');
    
    global $virtue_premium; 
	if( virtue_display_sidebar() ) {
		$display_sidebar = true; 
		$fullclass = '';
	} else {
		$display_sidebar = false; 
		$fullclass = 'fullwidth';
	} ?>

    <div id="content" class="container">
    	<div class="row">
      		<div class="main <?php echo esc_attr(virtue_main_class()); ?>  <?php echo esc_attr($fullclass);?> postlist" id="ktmain" role="main">

			<?php if (!have_posts()) : ?>
	  			<div class="alert">
	    			<?php _e('Sorry, no results were found.', 'virtue'); ?>
	  			</div>
	  			<?php get_search_form(); ?>
			<?php endif; ?>

			<?php
			if(isset($virtue_premium['search_layout']) && $virtue_premium['search_layout'] == 'singlecolumn') {

  				if($display_sidebar){
	               while (have_posts()) : the_post();
	                    get_template_part('templates/content', get_post_format());
	               endwhile;
             	} else {
	                while (have_posts()) : the_post(); 
	                    get_template_part('templates/content', 'fullwidth');
	                endwhile;
             	}

			} else if(isset($virtue_premium['search_layout']) && $virtue_premium['search_layout'] == 'simple_grid') { 

  				if(isset($virtue_premium['virtue_animate_in']) && $virtue_premium['virtue_animate_in'] == 1) {
  					$animate = 1;
  				} else {
  					$animate = 0;
  				} ?>
  				<div id="kad-blog-grid" class="clearfix init-isotope rowtight"  data-fade-in="<?php echo esc_attr($animate);?>"  data-iso-selector=".b_item" data-iso-style="masonry">
					<?php while (have_posts()) : the_post(); ?>
  						<div class="tcol-md-4 tcol-sm-4 tcol-xs-6 tcol-ss-12 b_item search_item">
  							<?php get_template_part('templates/content', 'loop-searchresults'); ?>
 						</div>
					<?php endwhile; ?>
				</div> <!-- Blog Grid -->

			<?php 
			} else { 
   				if(isset($virtue_premium['virtue_animate_in']) && $virtue_premium['virtue_animate_in'] == 1) {
   					$animate = 1;
   				} else {
   					$animate = 0;
   				} ?>
				<div id="kad-blog-grid" class="clearfix init-isotope rowtight"  data-fade-in="<?php echo esc_attr($animate);?>"  data-iso-selector=".b_item" data-iso-style="masonry">
					<?php while (have_posts()) : the_post(); ?>
  						<div class="tcol-md-4 tcol-sm-4 tcol-xs-6 tcol-ss-12 b_item search_item">
  							<?php get_template_part('templates/content', 'searchresults'); ?>
  						</div>
					<?php endwhile; ?>
				</div> <!-- Blog Grid -->
			<?php 
			}
			/*
			* @hoooked virtue_pagination_markup - 20;
			*/
			do_action( 'virtue_pagination' );
			?>
        	
			</div><!-- /.main -->
