<?php
/*
* Standard post Archive
*
*/
	global $virtue_premium, $kt_post_with_sidebar; 

	if(virtue_display_sidebar()) {
		$display_sidebar = true; 
		$fullclass = '';
		$kt_post_with_sidebar = true;
	} else {
		$display_sidebar = false; 
		$fullclass = 'fullwidth';
		$kt_post_with_sidebar = false;
	}
	if(isset($virtue_premium['category_post_summary']) && 'full' == $virtue_premium['category_post_summary']) {
		$summary = 'full'; 
		$postclass = "single-article fullpost";
	} else if (isset($virtue_premium['category_post_summary']) && 'grid' == $virtue_premium['category_post_summary']){
		$summary = 'grid'; 
		$postclass = "grid-postlist";
	} else {
		$summary = 'normal'; 
		$postclass = 'postlist';
	} 
	if(isset($virtue_premium['blog_cat_infinitescroll']) && $virtue_premium['blog_cat_infinitescroll'] == 1) {
		wp_enqueue_script( 'virtue-infinite-scroll' );
		if($summary == 'grid') {
			$scrollclass = 'init-infinit';
		} else {
			$scrollclass = 'init-infinit-norm';
		}
	} else {
		$scrollclass = '';
	}
	if(isset($virtue_premium['blog_grid_display_height']) && $virtue_premium['blog_grid_display_height'] == 1) {
		$matchheight = 1;
	} else {
		$matchheight = 0;
	}
	if(isset($virtue_premium['category_post_grid_column'])) {
		$blog_grid_column = $virtue_premium['category_post_grid_column'];
	} else {
		$blog_grid_column = '3';
	} 
	if ($blog_grid_column == '2') {
		$itemsize = 'tcol-md-6 tcol-sm-6 tcol-xs-12 tcol-ss-12'; 
	} else if ($blog_grid_column == '3'){ 
		$itemsize = 'tcol-md-4 tcol-sm-4 tcol-xs-6 tcol-ss-12'; 
	} else {
		$itemsize = 'tcol-md-3 tcol-sm-4 tcol-xs-6 tcol-ss-12';
	}

    /**
    * @hooked virtue_page_title - 20
    */
     do_action('kadence_page_title_container');
    ?>

	<div id="content" class="container">
		<div class="row">
			<div class="main <?php echo esc_attr(virtue_main_class()); ?>  <?php echo esc_attr($postclass) .' '. esc_attr($fullclass); ?>" role="main">
			<?php do_action('kadence_page_before_content'); ?>
			<?php if (!have_posts()) : ?>
				<div class="alert">
					<?php esc_html_e('Sorry, no results were found.', 'virtue'); ?>
				</div>
				<?php get_search_form(); ?>
			<?php endif; ?>

			<?php if($summary == 'full'){ ?>
				<div class="kt_archivecontent <?php echo esc_attr($scrollclass); ?>" data-nextselector=".wp-pagenavi a.next" data-navselector=".wp-pagenavi" data-itemselector=".post" data-itemloadselector=".kad-animation" data-infiniteloader="<?php echo esc_url(get_template_directory_uri() .'/assets/img/loader.gif'); ?>"> 
					<?php 
					while (have_posts()) : the_post();
						get_template_part('templates/content', 'fullpost'); 
					endwhile;
					?>
				</div> 
				<?php 
			} else if($summary == 'grid') { ?>
				<div id="kad-blog-grid" class="rowtight archivecontent <?php echo esc_attr($scrollclass); ?> init-isotope" data-nextselector=".wp-pagenavi a.next" data-navselector=".wp-pagenavi" data-itemselector=".kad_blog_item" data-itemloadselector=".kad_blog_fade_in" data-infiniteloader="<?php echo esc_url(get_template_directory_uri() .'/assets/img/loader.gif'); ?>" data-iso-match-height="<?php echo esc_attr($matchheight);?>" data-fade-in="<?php echo esc_attr(virtue_animate());?>" data-iso-selector=".b_item" data-iso-style="masonry">
				<?php while (have_posts()) : the_post(); ?>
					<?php if($blog_grid_column == '2') { ?>
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
				<?php
			} else { ?>
				<div class="kt_archivecontent <?php echo esc_attr($scrollclass); ?>" data-nextselector=".wp-pagenavi a.next" data-navselector=".wp-pagenavi" data-itemselector=".post" data-itemloadselector=".kad-animation" data-infiniteloader="<?php echo esc_url(get_template_directory_uri() .'/assets/img/loader.gif'); ?>"> 
					<?php
					while (have_posts()) : the_post();
						get_template_part('templates/content', get_post_format());
					endwhile;
					?>
				</div> 
				<?php 
			}
			/*
			* @hoooked virtue_pagination_markup - 20;
			*/
			do_action( 'virtue_pagination' );

			?>
			</div><!-- /.main -->