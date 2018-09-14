<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
	/**
	* @hooked virtue_page_title - 20
	*/
	do_action( 'virtue_page_title_container' );
	global $virtue_sidebar;
	$virtue_sidebar = true;
	?>

<div id="content" class="container">
	<div class="row">
		<div class="main <?php echo esc_attr( virtue_main_class() ); ?>  postlist" role="main">

		<?php if ( !have_posts() ) : ?>
			<div class="alert">
				<?php esc_html_e( 'Sorry, no results were found.', 'virtue' ); ?>
			</div>
			<?php get_search_form(); 
		endif; 

		while (have_posts()) : the_post();
			get_template_part( 'templates/content', get_post_format() );
		endwhile; 

		/**
		* @hooked virtue_pagination - 10
		*/
		do_action( 'virtue_pagination' );
		?>

		</div><!-- /.main -->