jQuery(document).ready(function ($) {
	$('.kad-panel-left .nav-tab-link').click(function (event) {
		event.preventDefault();
		var contain = $(this).closest('.kad-panel-left')
		var panel = contain.find('.nav-tab-wrapper');
		var active = panel.find('.nav-tab-active');
		var opencontent = $(this).closest('.kad-panel-contain').find('.nav-tab-content.panel_open');
		var contentid = $(this).data('tab-id');
		var tab = panel.find('a[data-tab-id="'+contentid+'"]');
		if (active.data('tab-id') == contentid ) {
			//leave
		} else {
			tab.addClass('nav-tab-active');
			active.removeClass('nav-tab-active');
			opencontent.removeClass('panel_open');
			$('#'+contentid).addClass('panel_open');	
		}

		return false;

	});
	$('.kad-panel-left .nav-tab:not(.nav-tab-link)').click(function (event) {
		event.preventDefault();
		var contain = $(this).closest('.kad-panel-left')
		var panel = contain.find('.nav-tab-wrapper');
		var active = panel.find('.nav-tab-active');
		var opencontent = $(this).closest('.kad-panel-contain').find('.nav-tab-content.panel_open');
		var contentid = $(this).data('tab-id');
		var tab = panel.find('a[data-tab-id="'+contentid+'"]');
		if (active.data('tab-id') == contentid ) {
			//leave
		} else {
			tab.addClass('nav-tab-active');
			active.removeClass('nav-tab-active');
			opencontent.removeClass('panel_open');
			$('#'+contentid).addClass('panel_open');	
		}

		return false;

	});
});
/**
 * Ajax install the Theme Plugin
 *
 */
(function($, window, document, undefined){
	"use strict";

	$(function(){
		$('.kt-trigger-plugin-install').on( 'click', function( event ) {
			var $button = $( event.target );
			event.preventDefault();
			/**
			 * Keep button from running twice
			 */
			if ( $button.hasClass( 'updating-message' ) || $button.hasClass( 'button-disabled' ) ) {
				return;
			}

			function ajax_callback(response){
	            if(typeof response === "object" && typeof response.message !== "undefined"){
	                // The plugin is done (installed, updated and activated).
	                if(typeof response.done != "undefined" && response.done){
	                	buttonStatusInstalled( $button.data('installed-label') );
	                    activatePlugin();
	                } else if( typeof response.url != "undefined" ){
	                    // we have an ajax url action to perform.
	                    jQuery.post(response.url, response, ajax_callback).fail(ajax_callback);
	                }else{
	                    // error processing this plugin
	                    buttonStatusDisabled( 'Error' );
	                }
	            } else {
	                // The TGMPA returns a whole page as response, so check, if this plugin is done.
	               activatePlugin();
	            }
	        }
			/**
			 * Install a plugin
			 *
			 * @return void
			 */
			function installPlugin(){

				if ( $button.hasClass( 'install-bundled' ) ) {
					buttonStatusInProgress( $button.data('installing-label') );
					jQuery.post(kadence_api_params.ajaxurl, {
                        action: "kadence_install_bundled",
                        wpnonce: kadence_api_params.wpnonce,
                        slug: $button.data( 'plugin-slug' ),
                    }, ajax_callback).fail( ajax_callback );

				} else {
					$.ajax({
						url : $button.data('install-url'),
						type: 'GET',
						data: {},
						beforeSend: function () {
							buttonStatusInProgress( $button.data('installing-label') );
						},
						success: function( reposnse ) {
							buttonStatusInstalled( $button.data('installed-label') );
							activatePlugin();
						},
						error: function (xhr, ajaxOptions, thrownError) {
							// Installation failed
							buttonStatusDisabled( 'Error' );
						}
					});
				}
			}

			/**
			 * Activate a plugin
			 *
			 * @return void
			 */
			function activatePlugin(){

				$.ajax({
					url : $button.data('activate-url'),
					type: 'GET',
					data: {},
					beforeSend: function () {
						buttonStatusInProgress( $button.data('activating-label') );
					},
					success: function( reposnse ) {
						buttonStatusDisabled( $button.data('activated-label') );
						if ( $button.data('redirect-url') ) {
							location.replace( $button.data('redirect-url') );
						}
					},
					error: function (xhr, ajaxOptions, thrownError) {
						 // Activation failed
						console.log( xhr.responseText );
						buttonStatusDisabled( 'Error' );
					}
				});
			}

			/**
			 * Change button status to in-progress
			 *
			 * @return void
			 */
			function buttonStatusInProgress( message ){
				$button.addClass('updating-message').removeClass('button-disabled kt-not-installed installed').text( message );
			}

			/**
			 * Change button status to disabled
			 *
			 * @return void
			 */
			function buttonStatusDisabled( message ){
				$button.removeClass('updating-message kt-not-installed installed')
				.addClass('button-disabled')
				.text( message );
			}

			/**
			 * Change button status to installed
			 *
			 * @return void
			 */
			function buttonStatusInstalled( message ){
				$button.removeClass('updating-message kt-not-installed')
					.addClass('installed')
					.text( message );
			}


			if( $button.data('action') === 'install' ){
				installPlugin();
			} else if( $button.data('action') === 'activate' ){
				activatePlugin();
			}
		});
	});
})(jQuery, window, document);