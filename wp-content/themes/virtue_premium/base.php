<?php
/**
 * Wrapper template for displaying everything
 *
 * @version 4.6.2
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

 	get_header();
 	?>
	<div class="wrap clearfix contentclass hfeed" role="document">

			<?php do_action( 'kt_afterheader' );


			include kadence_template_path(); 

			/**
			* @hooked virtue_sidebar_markup - 10
			*/
			do_action( 'virtue_sidebar' );
			?>
			</div><!-- /.row-->
			<?php do_action( 'kt_after_content' ); ?>
		</div><!-- /.content -->
	</div><!-- /.wrap -->
<?php
get_footer();
