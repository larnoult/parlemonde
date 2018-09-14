<div id="content" class="container">
    <div class="row single-article">
      <div class="main <?php echo virtue_main_class(); ?>" id="ktmain" role="main">
		<?php while (have_posts()) : the_post(); ?>
		    <article <?php post_class(); ?>>
		    	<div class="clearfix">
		    	<div class="staff-img thumbnail alignleft clearfix">
				 		<?php the_post_thumbnail( 'medium' ); ?>
				</div>
			  	<header>
			  		<?php if(kadence_display_staff_breadcrumbs()) { kadence_breadcrumbs(); } ?>
	      			<h1 class="entry-title"><?php the_title(); ?></h1>
				</header>
				<div class="entry-content">
				 	
      				<?php the_content(); ?>
    			</div>
    			</div>
    			<footer class="single-footer">
      				<?php wp_link_pages(array('before' => '<nav class="pagination kt-pagination">', 'after' => '</nav>', 'link_before'=> '<span>','link_after'=> '</span>')); ?>
			    </footer>

			    <?php comments_template('/templates/comments.php'); ?>
			</article>
		<?php endwhile; ?>
	</div>