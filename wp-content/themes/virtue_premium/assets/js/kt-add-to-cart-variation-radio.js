/*
 * kt variations plugin
 */

jQuery(document).ready(function ($) {

	var $vform = $('.product form.variations_form');
	var $vform_select = $('.variations td.product_value select');
	var $variations = $vform.find( '.single_variation_wrap' );
	var $use_ajax = $vform.data( 'product_variations' ) === false;
	var $product_variations = $vform.data( 'product_variations' );
	var count_variations = $('.variations td.product_value').length;

		
		
		$vform.on( 'click', '.reset_variations', function() {
			$vform.find('.kad_radio_variations .selectedValue').removeClass('selectedValue');
			$vform.find('.kad_radio_variations label' ).removeClass( 'kt_disabled ');
			$vform.find('.kad_radio_variations input[type="radio"]:checked' ).prop('checked', false);
			return false;
		} );
		$vform.on( 'reset_data', function() {
			$vform.find( '.single_variation_wrap_kad' ).find('.quantity').hide();
			$vform.find( '.single_variation .price').hide();
			//$vform.find( '.single_variation_wrap' ).css("height", "auto");
		} );
		$vform.on('click', '.select-option', function (e) {
				e.preventDefault();
		});
		$vform.on('change', '.variations input[type="radio"]', function (e) {

				var $this = $(this);

				//Get the wrapper select div
				var $option_wrapper = $this.closest('.kt-radio-variation-container');

				//Select the option.
				var $wc_select_box = $option_wrapper.find('select').first();

				// Decode entities
				var attr_val = $this.val();
				//$wc_select_box.trigger('focusin').children("[value='" + attr_val + "']").prop("selected", "selected").change();
				$wc_select_box.trigger('focusin');
				if($wc_select_box.find("option[value='"+attr_val+"']").length) {
					$wc_select_box.trigger('focusin').val(attr_val).trigger('change');
				} else {
					$vform.find( '.variations select' ).val( '' ).change();
					$vform.find('.kad_radio_variations .selectedValue').removeClass('selectedValue');
					$vform.find('.kad_radio_variations label' ).removeClass( 'kt_disabled ');
					$('.variations .select2-container').select2("val", "");
					$vform.find('.kad_radio_variations input[type="radio"]:checked' ).prop('checked', false);
					$vform.trigger( 'reset_data' );
					// add in the selection
					$vform.find(".kad_radio_variations input[type='radio'][value='"+attr_val+"']" ).prop('checked', true);
					$wc_select_box.trigger('focusin').val(attr_val).trigger('change');
				}
				$vform.find('.kad_radio_variations .selectedValue').removeClass('selectedValue');
				$vform.find('.kad_radio_variations input[type="radio"]:checked').parent().addClass('selectedValue');

			});
		// Disable option fields that are unavaiable for current set of attributes
		$vform.on('woocommerce_variation_has_changed', function () {
			if ( $use_ajax ) {
				return;
			}
			var kt_current_settings = {};
			var variations = [];
			$vform.find( '.variations select' ).each( function() {
				$(this).trigger('focusin');
				$(this).find( 'option.enabled' ).each( function(){
					variations.push($( this ).val());
				});
			});
			$vform.find( '.variations .kad_radio_variations' ).each( function( index, el ) {

				var current_attr_radio = $( el );
				var current_attr_name = current_attr_radio.data( 'attribute_name' );
				current_attr_radio.find( 'input' ).removeClass( 'attached' );
				current_attr_radio.find( 'input' ).removeClass( 'enabled' );
				current_attr_radio.find( 'label' ).removeClass( 'kt_disabled ');

				// Loop through variations
				var i;
				for (i = 0; i < variations.length; ++i) {
										current_attr_radio.find("input[value='" + variations[i] + "']").addClass( 'attached ' + 'enabled');

				}
				current_attr_radio.find( 'input:not(.attached)' ).parent('label').addClass('kt_disabled');
				});
		} );

		$vform.on('woocommerce_variation_has_changed', function() {
			$('.kad-select').trigger('update');
		} );

		$variations.on('hide_variation', function() {
			$(this).css('height', 'auto');
		} );
		$(function() {
    	if ( typeof wc_add_to_cart_variation_params !== 'undefined' ) {
    		$( '.variations_form' ).each( function() {
				$( this ).find('.variations input[type="radio"]:checked').change();
			});
    	}
    });
});
