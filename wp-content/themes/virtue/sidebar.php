<?php
/* 
* Sidebar Template
*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<aside class="<?php echo esc_attr( virtue_sidebar_class() ); ?> kad-sidebar" role="complementary" itemscope itemtype="http://schema.org/WPSideBar">
	<div class="sidebar">
		<?php dynamic_sidebar( virtue_sidebar_id() ); ?>
	</div><!-- /.sidebar -->
</aside><!-- /aside -->
