/*
 * kt variations plugin
 */

jQuery(document).ready(function ($) {

	var $vform = $('.product form.variations_form');
	var $vform_select = $('.variations td.product_value select');
	var $variations = $vform.find( '.single_variation_wrap' );
	var $use_ajax = $vform.data( 'product_variations' ) === false;


		$vform.on( 'click', '.reset_variations', function() {
			if ( $use_ajax ) {
				$('.kad-select').select2({minimumResultsForSearch: -1 });
			}
			return false;
		} );
		$vform.on( 'reset_data', function() {
			$vform.find( '.single_variation_wrap_kad' ).find('.quantity').hide();
			$vform.find( '.single_variation .price').hide();
			//$vform.find( '.single_variation_wrap' ).css("height", "auto");
		} );

		$vform.on('woocommerce_variation_has_changed', function() {
			$('.kad-select').trigger('update');
			if ( $use_ajax ) {
				if( $(window).width() > 790 && !kt_isMobile.any() ) {
					$('.kad-select').select2({minimumResultsForSearch: -1 });
				}
			}
		} );

		$variations.on('hide_variation', function() {
			$(this).css('height', 'auto');
		} );
});

