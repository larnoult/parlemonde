<?php
	/**
	* @hooked virtue_page_title - 20
	*/
	do_action( 'virtue_page_title_container' );
	?>

<div id="content" class="container">
	<div class="row">
		<div class="main <?php echo esc_attr( virtue_main_class() ); ?>" role="main">
			<div class="alert">
				<?php esc_html_e( 'Sorry, but the page you were trying to view does not exist.', 'virtue' ); ?>
			</div>

			<p><?php esc_html_e( 'It looks like this was the result of either:', 'virtue' ); ?></p>
				<ul>
					<li><?php esc_html_e( 'a mistyped address', 'virtue' ); ?></li>
					<li><?php esc_html_e( 'an out-of-date link', 'virtue' ); ?></li>
				</ul>
			<?php get_search_form(); ?>
		</div><!--main-->
