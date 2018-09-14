<?php
/**
 * Wrapper template for displaying everything
 *
 * @version 3.2.5
 */
	get_header();
	?>
	<div class="wrap contentclass" role="document">

	<?php do_action( 'kt_afterheader' );

			include kadence_template_path();

				/**
				* @hooked virtue_sidebar_markup - 10
				*/
				do_action( 'virtue_sidebar' );
				?>
			</div><!-- /.row-->
		</div><!-- /.content -->
	</div><!-- /.wrap -->
	<?php 
	get_footer();
