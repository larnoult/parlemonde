/** 
 * @version 1.0
 * @desciption Dismiss Notices - Ajax handler
 * @usage Admin panel
 * @author wpdevelop
 *
 * @web-site http://oplugins.com/
 * @email info@oplugins.com 
 * 
 * @modified 2017-04-23
 */

/** Ajax Request - Dismiss */
jQuery( function ( $ ) {                                                                            // Shortcut to  jQuery(document).ready(function(){ ... });

    jQuery( '.opsd_is_dismissible' ).on( 'click', '.opsd_dismiss', function ( event ) {             // This delegated event, can be run, when DOM element added after page loaded
                                
        var jq_el = jQuery( this ).closest( '.opsd_is_dismissible' );                               // Get dismissible HTML element

        var params_obj = {};
        params_obj.id      = jq_el.attr( 'id' );
        params_obj.nonce   = jq_el.attr( 'data-nonce' );
        params_obj.user_id = jq_el.attr( 'data-user-id' );
        
        jQuery.post( opsd_ajaxurl, {
                                    action:     'OPSD_DISMISS',
                                    user_id:    params_obj.user_id ,
                                    nonce:      params_obj.nonce,
                                    element_id: params_obj.id,
                                    is_closed:  1
                                },                                            
                        function ( response_data, textStatus, jqXHR ) {                             // success
                            // console.log( response_data ); console.log( textStatus); console.log( jqXHR );        // Debug
                            // jQuery( '#ajax_respond' ).html( response_data );                                     // For ability to show response, add such  DIV element to page
                        }
                ).fail( function ( jqXHR, textStatus, errorThrown ) {    if ( window.console && window.console.log ){ console.log( 'Ajax_Error', jqXHR, textStatus, errorThrown ); }     })  
                // .done( function ( data, textStatus, jqXHR ) {   if ( window.console && window.console.log ){ console.log( 'second success', data, textStatus, jqXHR ); }    })                
                // .always( function ( data_jqXHR, textStatus, jqXHR_errorThrown ) {   if ( window.console && window.console.log ){ console.log( 'always finished', data_jqXHR, textStatus, jqXHR_errorThrown ); }     })
                ;
                        
    });

});


/* Hide */
jQuery( function ( $ ) {                                                                            // Shortcut to  jQuery(document).ready(function(){ ... });

    jQuery( '.opsd_is_hideable' ).on( 'click', '.opsd_dismiss', function ( event ) {                // This delegated event, can be run, when DOM element added after page loaded
                
        var jq_el = jQuery( this ).closest( '.opsd_is_hideable' );                                  // Get hideable HTML element
    
        jq_el.hide();
        
    });

});