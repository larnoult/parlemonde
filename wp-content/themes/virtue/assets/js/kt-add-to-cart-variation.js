/*
 * kt variations for woocommerce
 */

jQuery(document).ready(function ($) {

	var $vform = $('.product form.variations_form');
	var $variations = $vform.find( '.single_variation_wrap' );


		$vform.on( 'reset_data', function() {
			$vform.find( '.single_variation_wrap_kad' ).find('.quantity').hide();
			$vform.find( '.single_variation .price').hide();
		} );

		$vform.on('woocommerce_variation_has_changed', function() {
			$('.kad-select').trigger('update');
		} );

		$variations.on('hide_variation', function() {
			$(this).css('height', 'auto');
		} );
});

