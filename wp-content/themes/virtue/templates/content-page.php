<?php
	do_action( 'virtue_page_content_before' );
	while ( have_posts() ) : the_post(); 
	the_content();
	wp_link_pages(array('before' => '<nav class="pagination">', 'after' => '</nav>'));
	endwhile;
	do_action( 'virtue_page_content_after' );