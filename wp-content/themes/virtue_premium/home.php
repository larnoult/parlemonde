<?php 
    /**
    * @hooked virtue_page_title - 20
    */
    do_action( 'kadence_page_title_container' );
  
	$homeid = get_option( 'page_for_posts' );
	if ( get_post_meta( $homeid, '_kad_blog_summery', true ) == 'full' ) {
		$summary    = 'full'; 
		$postclass  = "single-article fullpost";
	} else {
		$summary    = 'normal';
		$postclass  = 'postlist';
	} ?>

    <div id="content" class="container">
      <div class="row">
      <div class="main <?php echo esc_attr( virtue_main_class() ); ?>  <?php echo esc_attr( $postclass ); ?>" role="main">
      <?php 
      do_action( 'kadence_page_before_content' ); ?>

<?php if ( ! have_posts() ) : ?>
  <div class="alert">
    <?php esc_html_e( 'Sorry, no results were found.', 'virtue' ); ?>
  </div>
  <?php get_search_form(); 
	endif;

		if ( $summary == 'full' ){
			while ( have_posts() ) : the_post(); 
				get_template_part( 'templates/content', 'fullpost' ); 
			endwhile; 
		} else {
			while ( have_posts() ) : the_post(); 
				get_template_part( 'templates/content', get_post_format() );
			endwhile; 
		}
		
		/*
		* @hoooked virtue_pagination_markup - 20;
		*/
		do_action( 'virtue_pagination' ); ?>
</div><!-- /.main -->
