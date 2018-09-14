<?php
/**
 * Grade Book
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
if ( ! is_admin() ) { exit; }
?>

<div class="wrap lifterlms llms-reporting tab--<?php echo $current_tab; ?>">

	<form action="<?php echo admin_url( 'admin.php' ); ?>" class="llms-reporting-nav" method="GET">

		<nav class="llms-nav-tab-wrapper">

			<ul class="llms-nav-items">
			<?php foreach ( $tabs as $name => $label ) : ?>

				<?php $current_tab_class = ( $current_tab == $name ) ? ' llms-active' : ''; ?>
				<li class="llms-nav-item<?php echo $current_tab_class; ?>"><a class="llms-nav-link" href="<?php echo admin_url( 'admin.php?page=llms-reporting&tab=' . $name ); ?>"><?php echo $label; ?></a>

			<?php endforeach; ?>
			</ul>

		</nav>

		<?php do_action( 'llms_reporting_after_nav', $current_tab ); ?>

	</form>

	<h1 style="display:none;"></h1><!-- find a home for admin notices -->

	<div class="llms-options-page-contents">

		<?php do_action( 'llms_reporting_before_content', $current_tab ); ?>

		<?php do_action( 'llms_reporting_content_' . $current_tab ); ?>

		<?php do_action( 'llms_reporting_after_content', $current_tab ); ?>


	</div>

	<p class="alignright"><em><a style="font-size:12px;color:#555d66" target="_blank" href="https://lifterlms.com/docs/lifterlms-reporting-beta/"><?php _e( 'LifterLMS Reporting Beta', 'lifterlms' ); ?></em></a></p>

</div>
