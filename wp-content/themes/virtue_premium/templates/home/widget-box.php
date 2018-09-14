<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div class="home-margin home-padding kad-animation" data-animation="fade-in" data-delay="0">
    <div class="home-widget-box">
        <?php  if(is_active_sidebar('homewidget')) { dynamic_sidebar('homewidget'); } ?>
    </div> <!--home widget box -->
</div>