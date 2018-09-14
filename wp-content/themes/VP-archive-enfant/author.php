  <div id="pageheader" class="titleclass">
    <div class="container"> 

	<div class="page-header">
	  <h1>
	    <?php 
		$author = get_user_by( 'slug', get_query_var( 'author_name' ) ); 
	echo "Les articles de la "; echo $author->display_name;
	echo " (@"; echo $author->user_login; echo ")"
		?>
	    <?php if(kadence_display_page_breadcrumbs()) { kadence_breadcrumbs(); } ?>
	  </h1>
		<span class="identite-buddypress"> 
		<a href="<?php echo home_url().'/members/'.$author->user_login; ?>">Voir leur profil </a>
		</span>
	</div>

    </div><!--container-->
  </div><!--titleclass-->
  
    <div id="content" class="container">
      <div class="row">
      <div class="main <?php echo kadence_main_class(); ?>  postlist" role="main">
	
<?php if (!have_posts()) : ?>
  <div class="alert">
    <?php _e('Sorry, no results were found.', 'virtue'); ?>
  </div>
  <?php get_search_form(); ?>
<?php endif; ?>

<?php while (have_posts()) : the_post(); ?>
  <?php get_template_part('templates/content', get_post_format()); ?>
<?php endwhile; ?>

<?php if ($wp_query->max_num_pages > 1) : ?>
        <?php if(function_exists('kad_wp_pagenavi')) { ?>
              <?php kad_wp_pagenavi(); ?>   
            <?php } else { ?>      
              <nav class="post-nav">
                <ul class="pager">
                  <li class="previous"><?php next_posts_link(__('&larr; Older posts', 'virtue')); ?></li>
                  <li class="next"><?php previous_posts_link(__('Newer posts &rarr;', 'virtue')); ?></li>
                </ul>
              </nav>
            <?php } ?> 
        <?php endif; ?>

</div><!-- /.main -->
