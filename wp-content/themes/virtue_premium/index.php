<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
} 
    /**
    * @hooked virtue_page_title - 20
    */
	do_action( 'kadence_page_title_container' );
    ?>
  
<div id="content" class="container">
  <div class="row">
    <div class="main <?php echo virtue_main_class(); ?>  postlist" role="main">
    <?php 
      do_action('kadence_page_before_content'); ?>
      <div class="entry-content" itemprop="mainContentOfPage">
        <?php 
        if (!have_posts()) : ?>
          <div class="alert">
            <?php _e('Sorry, no results were found.', 'virtue'); ?>
          </div>
          <?php 
          get_search_form(); 
        endif;

        while (have_posts()) : the_post(); 
			get_template_part('templates/content', get_post_format()); 
        endwhile; 

       	/*
		* @hoooked virtue_pagination_markup - 20;
		*/
		do_action( 'virtue_pagination' ); ?>
        </div>
      </div><!-- /.main -->
