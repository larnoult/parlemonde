
<div id="content" class="container">
    <div class="row single-article">
      <div class="main <?php echo virtue_main_class(); ?>" id="ktmain" role="main">
		<?php while (have_posts()) : the_post(); ?>
		    <article <?php post_class(); ?>>
		    	<div class="clearfix">
		    	<div class="staff-img thumbnail alignleft clearfix">
		    		<?php if (has_post_thumbnail( $post->ID ) ) {
				 	  the_post_thumbnail( 'thumbnail' ); } else { ?> 
				 	  <i class="icon-user" style="font-size:150px;"></i>
				 	  <?php } ?>
				</div>
			  	<header>
			  	<?php global $post, $virtue_premium; if(isset($virtue_premium['testimonial_single_nav']) && $virtue_premium['testimonial_single_nav'] == 1) {?>
			  		<div class="portfolionav clearfix">
		   					<?php $arrownav = false;	
		   					if(isset($virtue_premium['testimonial_page'])){ $parent_id = $virtue_premium['testimonial_page'];} else {$parent_id = '';}
		   					previous_post_link_plus( array('order_by' => 'menu_order', 'loop' => true, 'in_same_tax' => $arrownav, 'format' => '%link', 'link' => '<i class="icon-arrow-left"></i>') ); ?>
					   			<?php if( !empty($parent_id)){ ?>
					   				<a href="<?php echo get_page_link($parent_id); ?>">
									<?php } else {?> 
									<a href="../">
									<?php } ?>
					   				<i class="icon-grid"></i></a> 
					   		<?php next_post_link_plus( array('order_by' => 'menu_order', 'loop' => true, 'in_same_tax' => $arrownav, 'format' => '%link', 'link' => '<i class="icon-arrow-right"></i>') ); ?>
		   				</div>
		   				<?php } ?>
	      			<h1 class="entry-title"><?php the_title(); ?></h1>
	      			<div class="subhead">
	      			<?php global $post; $occupation = get_post_meta( $post->ID, '_kad_testimonial_occupation', true ); 
	      								$clientlink = get_post_meta( $post->ID, '_kad_testimonial_link', true ); 
	      								$location = get_post_meta( $post->ID, '_kad_testimonial_location', true ); 
	      						if(!empty($location)) { echo $location . ' | ';}
	      						if(!empty($occupation)) { echo $occupation . ' | '; }
	      						if(!empty($clientlink)) { echo '<a href="'.$clientlink.'" target="_blank">'.$clientlink.'</a>';} 
	      				?>
	      			</div>
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
