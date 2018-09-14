<?php 

if ( apply_filters( 'woocommerce_show_page_title', true ) ) : 
	if ( version_compare( WC_VERSION, '3.3', '>' ) ) {
		if ( ! wc_get_loop_prop( 'columns' ) ) {
			wc_get_loop_class();
		} 
	}?>
			<div id="pageheader" class="titleclass">
			<div class="container woo-titleclass-container">
			  <div class="page-header">
			    <div class="row">
			      <div class="col-md-6 col-sm-6 woo-archive-pg-title">
			         <?php do_action( 'kt_woocommerce_page_title_left' ); ?>
			      </div>
			      <div class="col-md-6 col-sm-6 woo-archive-pg-order">
			       <?php do_action( 'kt_woocommerce_page_title_right' ); ?>
			      </div>
			    </div>
			  </div>
			</div>
			</div>
    
    <?php
endif; ?>