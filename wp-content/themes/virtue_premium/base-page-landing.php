<?php 
/**
 * Landing Base - No header or footer output
 */

get_template_part('templates/head');
?>
	<body <?php body_class(); ?> <?php echo wp_kses_post( virtue_body_data() ); ?>>
		<div id="wrapper" class="container">
		<!--[if lt IE 8]><div class="alert"> <?php esc_html_e( 'You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.', 'virtue' ); ?></div><![endif]-->
			<div class="wrap clearfix contentclass hfeed" style="padding:0px;" role="document">
			
					<?php do_action( 'kt_afterheader' );
						include kadence_template_path(); 

						if (virtue_display_sidebar()) : ?>
							<aside id="ktsidebar" class="<?php echo esc_attr( virtue_sidebar_class() ); ?> kad-sidebar" role="complementary">
								<div class="sidebar">
									<?php include kadence_sidebar_path(); ?>
								</div><!-- /.sidebar -->
							</aside><!-- /aside -->
						<?php endif; ?>
					</div><!-- /.row-->
				</div><!-- /.content -->
			</div><!-- /.wrap -->

			<?php wp_footer(); ?>
		</div><!--Wrapper-->
	</body>
</html>
