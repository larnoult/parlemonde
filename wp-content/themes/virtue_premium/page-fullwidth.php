<?php
/**
 * Template Name: Fullwidth
 *
 * @package Virtue Theme
 */

/**
 * Virtue Page title area.
 *
 * @hooked virtue_page_title - 20
 */
do_action( 'kadence_page_title_container' );
?>
<div id="content" class="container <?php echo esc_attr( virtue_container_class() ); ?>">
   		<div class="row">
     		<div class="main <?php echo virtue_main_class(); ?>" id="ktmain" role="main">
                <?php 
                do_action('kadence_page_before_content'); ?>
				<div class="entry-content" itemprop="mainContentOfPage">
					<?php get_template_part('templates/content', 'page'); ?>
				</div>
				<?php 
                /**
                * @hooked virtue_page_comments - 20
                */
                do_action('kadence_page_footer');
                ?>
			</div><!-- /.main -->
