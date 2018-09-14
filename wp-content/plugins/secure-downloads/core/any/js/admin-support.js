/**
 * @version 1.0
 * @package Support Functions
 * @subpackage BackEnd Main Script Lib
 * @category Scripts 
 * @author wpdevelop
 *
 * @web-site http://oplugins.com/
 * @email info@oplugins.com 
 * 
 * @modified 2015-04-09
  */


/** Scroll to  specific HTML element
 * 
 * @param {type} object_name
 * @returns {undefined}
 */
function opsd_scroll_to( object_name ) {    
    if ( jQuery( object_name ).length > 0 ) {        
        var targetOffset = jQuery( object_name ).offset().top;
        // targetOffset = targetOffset - 50;
        if (targetOffset<0) targetOffset = 0;
        if ( jQuery('#wpadminbar').length > 0 ) targetOffset = targetOffset - 50;
        else  targetOffset = targetOffset - 20;
        jQuery('html,body').animate({scrollTop: targetOffset}, 500);
    }
}

function opsd_animate_border( element, time, colors, x ) {
    
    if (x >= colors.length) {
        x = 0;
    } else {
        x++;
        var color;
        if ( colors[x] === '' ) {
            color = ''
        } else {
            color = '#'+colors[x]
        }        
        element.css('border-color', color)
        setTimeout(function() {
            opsd_animate_border( element, time, colors, x );
        }, time)
    }
}

function opsd_field_highlight( object_name ) {    
    
    if ( jQuery( object_name ).length > 0 ) { 
     
        opsd_scroll_to( object_name );
        
        opsd_animate_border( 
                                jQuery( object_name )                           // Element 
                                , 200                                           // Time in ms
                                , ['f87000', '', 'f87000', '', 'f87000', '', 'f87000', '', 'f87000', '', 'f87000', '']      // Colors Array
                                , 0
                            ); 
    }
}

/**  Show Yes/No dialog
 * 
 * @param {type} message_question
 * @returns {Boolean}
 */
function opsd_are_you_sure( message_question ){
    var answer = confirm( message_question );
    if ( answer) { return true; }
    else         { return false;}
}

function opsd_admin_show_message_processing( message_type ){
    
    var message = '' ;
    
    if ( message_type == 'saving' )
        message += opsd_message_saving;
    else if ( message_type == 'updating' )
        message += opsd_message_updating;
    else if ( message_type == 'deleting' )
        message += opsd_message_deleting;
    else 
        message += opsd_message_processing;
      
    if ( message == 'undefined' )  
        message = 'Processing'
      
    message = ' <span class="wpdevelop"><span class="glyphicon glyphicon-refresh opsd_spin opsd_ajax_icon"  aria-hidden="true"></span></span> ' + message + '...';
    
    opsd_admin_show_message( message, 'info', 10000 );
}

/** Show Alert Messages
 * 
 * @param {type} message
 * @param {type} m_type
 * @param {type} m_delay
 * @returns {undefined}
 */
function opsd_admin_show_message( message, m_type, m_delay ){

    var alert_class = 'notice ';                                                //'alert ';
    if (m_type == 'error')      alert_class += 'notice-error ';                 //'alert-danger '; 
    if (m_type == 'warning')    alert_class += 'notice-warning ';
    if (m_type == 'info')       alert_class += 'notice-info ';                  //'alert-info '; 
    if (m_type == 'success')    alert_class += 'alert-success updated '; 

    jQuery('#ajax_working').html(   '<div id="opsd_alert_message" class="opsd_alert_message">' +
                                        '<div class="opsd_inner_message '+alert_class+'"> ' +
                                            '<a class="close" href="javascript:void(0)" onclick="javascript:jQuery(this).parent().hide();">&times;</a> ' + 
                                            message + 
                                        '</div>' +
                                    '</div>' 
                                );
    jQuery('#opsd_alert_message').animate( {opacity: 1}, m_delay ).fadeOut(500);        
}


function opsd_close_dropdown_selectbox( selector_id ) {
  jQuery('#' + selector_id + '_container li input[type=checkbox],#' + selector_id + '_container li input[type=radio]').prop('checked', false);
  jQuery('#' + selector_id + '_container').hide();
}
// Show Container depend from the selected option in dropdown list
function opsd_show_selected_in_dropdown( selector_id, title, value ){
    jQuery('#' + selector_id + '_selector .opsd_selected_in_dropdown').html( title );
    jQuery('#' + selector_id ).val( value );    
}

// Show Container depend from the selected Radio Option and Selectbox value in dropdown list
// Exmaple: opsd_show_selected_in_dropdown__radio_select_option( 'wh_ ... _date', 'wh_ ... _date2', 'wh_ ... _datedays_interval_Radios' );
function opsd_show_selected_in_dropdown__radio_select_option( selector_id, selector_id2, radio_name ){
    
    // Get selected value in radio buttons
    var rad_val = jQuery('input:radio[name="' + radio_name + '"]:checked').val(); 
    
    if ( rad_val != 'undefined' ) {
        
        var select_box = jQuery('input:radio[name="' + radio_name + '"]:checked').parents('.input-group').find('select');
        // Selectbox exist
        if ( select_box.length > 0 ) {
            // Get label near selected radiobutton  and selected Tilte in selectbox
            var title = jQuery('input:radio[name="' + radio_name + '"]:checked').parent().find('label').html() + ' ' +
                        jQuery('input:radio[name="' + radio_name + '"]:checked').parents('.input-group').find('select option:selected').text();
            // Get Value of selected option in selectbox
            var value = jQuery('input:radio[name="' + radio_name + '"]:checked').parents('.input-group').find('select option:selected').val();
            // Set  Title in dropdown list
            jQuery('#' + selector_id + '_selector .opsd_selected_in_dropdown').html( title );
            // Set  value of radio button
            jQuery('#' + selector_id ).val( rad_val );
            // Set  value of selectbox
            jQuery('#' + selector_id2 ).val( value );            
        } else {
            // 2 Text Fields
            var text_box = jQuery('input:radio[name="' + radio_name + '"]:checked').parents('.text-group').find('input[type="text"]');                       
            if ( text_box.length > 0 ) {                           
               var text_divs = jQuery('input:radio[name="' + radio_name + '"]:checked').parents('.text-group').find('.dropdown-menu-text-element');
               
               // Check if we have 2 DIV elements with text fields
               if ( text_box.length > 0 ) {
                                       
                    var id_list = [ selector_id, selector_id2 ];
                    var title = '';
                    //Loop our text DIV elements
                    jQuery('input:radio[name="' + radio_name + '"]:checked').parents('.text-group').find('.dropdown-menu-text-element').each(function( i ) {
                        
                        if ( title != '' )
                            title += ' - ';                        
                        title += jQuery(this).find('input[type="text"]').val();
                        jQuery('#' + id_list[ i ] ).val(  jQuery(this).find('input[type="text"]').val() );
                    });
                    // Set  Title in dropdown list
                    jQuery('#' + selector_id + '_selector .opsd_selected_in_dropdown').html( title );
                    
               }
            }
        }
    }
    
    // Hide dropdown list
    jQuery('#' + selector_id + '_container').hide();                                                                            
}
    
//Set status of all checkbos in one time
function opsd_set_checkbox_in_table( el_stutus, el_class ){ 
     jQuery('.'+el_class).attr('checked', el_stutus);

     if ( el_stutus ) {
        jQuery('.'+el_class).parent().parent().parent().parent().addClass('row_selected_color');
        // jQuery('.'+el_class).parent().parent().addClass('warning');
     } else {
        jQuery('.'+el_class).parent().parent().parent().parent().removeClass('row_selected_color');
        // jQuery('.'+el_class).parent().parent().removeClass('warning');
     }     
}
   

/** Ajax Request
 * 
 * @param {type} us_id
 * @param {type} window_id
 * @returns {undefined}
 */
//<![CDATA[
function opsd_verify_window_opening( us_id, window_id ){

        var is_closed = 0;

        if (jQuery('#' + window_id ).hasClass('closed') == true){
            jQuery('#' + window_id ).removeClass('closed');
        } else {
            jQuery('#' + window_id ).addClass('closed');
            is_closed = 1;
        }


        jQuery.ajax({                                           // Start Ajax Sending
                url: opsd_ajaxurl,
                type:'POST',
                success: function (data, textStatus){if( textStatus == 'success')   jQuery('#ajax_respond').html( data );},
                error:function (XMLHttpRequest, textStatus, errorThrown){ window.status = 'Ajax sending Error status:'+ textStatus; alert(XMLHttpRequest.status + ' ' + XMLHttpRequest.statusText); if ( XMLHttpRequest.status == 500 ) { alert('Error: 500'); } } ,
                // beforeSend: someFunction,
                data:{
                    action:     'USER_SAVE_WINDOW_STATE',
                    user_id:    us_id ,
                    window:     window_id,
                    is_closed:  is_closed,
                    opsd_nonce: jQuery('#opsd_admin_panel_nonce').val() 
                }
        });

}
//]]>



/** Ajax Request - Saving Custom Data for User
 * 
 * @param {int} us_id
 * @param {string} data_name
 * @param {string} data_value - serialized data
 * @param {int} is_reload  -  { 0 | 1 } reload or not page
 */
//<![CDATA[
function opsd_save_custom_user_data( us_id, data_name, data_value , is_reload ){

        opsd_admin_show_message_processing( 'saving' );   

        jQuery.ajax({                                           // Start Ajax Sending
                url: opsd_ajaxurl,
                type:'POST',
                success: function (data, textStatus){if( textStatus == 'success')   jQuery('#ajax_respond').html( data );},
                error:function (XMLHttpRequest, textStatus, errorThrown){ window.status = 'Ajax sending Error status:'+ textStatus; alert(XMLHttpRequest.status + ' ' + XMLHttpRequest.statusText); if ( XMLHttpRequest.status == 500 ) { alert('Error: 500'); } } ,
                // beforeSend: someFunction,
                data:{
                    action:     'USER_SAVE_CUSTOM_DATA',
                    user_id:    us_id,
                    data_name:  data_name,
                    data_value: decodeURIComponent( data_value ),
                    is_reload:  is_reload, 
                    opsd_nonce: jQuery('#opsd_admin_panel_nonce').val() 
                }
        });

}
//]]>


////////////////////////////////////////////////////////////////////////////////
// Contact Form
////////////////////////////////////////////////////////////////////////////////
function opsd_submit_client_form( submit_form, wpdev_active_locale ){
    
    var count = submit_form.elements.length;
    var formdata = '';
    var inp_value;
    var element;
    var el_type;

    for (i=0; i<count; i++)   {
        element = submit_form.elements[i];

        if ( (element.type !=='button') && (element.type !=='hidden') ) {       

            // Get Value of Element
            if ( element.type == 'checkbox' ){

                if ( element.value == '' ) {
                    inp_value = element.checked;
                } else {
                    if ( element.checked ) 
                        inp_value = element.value;
                    else 
                        inp_value = '';
                }

            } else if ( element.type == 'radio' ) {

                if ( element.value == '' ) {
                    inp_value = element.checked;
                } else {
                    if ( element.checked ) 
                        inp_value = element.value;
                    else 
                        inp_value = '';
                }
                /*
                if ( element.checked ) 
                    inp_value = element.value; 
                else 
                    continue;
                */
                
            } else {
                inp_value = element.value;
            }                      

            // Get value in selectbox of multiple selection
            if (element.type =='select-multiple') {
                inp_value = jQuery('[name="'+element.name+'"]').val() ;
                if ( ( inp_value == null ) || ( inp_value.toString() == '' ) )
                    inp_value='';
            }
            
            /*if ( element.name == ('phone') ) {
                // we validate a phone number of 10 digits with no comma, no spaces, no punctuation and there will be no + sign in front the number - See more at: http://www.w3resource.com/javascript/form/phone-no-validation.php#sthash.U9FHwcdW.dpuf
                var reg =  /^\d{10}$/;
                var message_verif_phone = "Please enter correctly phone number";
                if ( inp_value != '' )
                    if(reg.test(inp_value) == false) {opsd_show_error_message( element , message_verif_phone);return;}
            }*/


            // Validation Check -- Requred fields
            if ( element.className.indexOf( 'opsd-validate-required' ) !== -1 ){      
                
                if  ( ( element.type =='checkbox' ) && ( element.checked === false ) ) {
                    if ( ! jQuery(':checkbox[name="'+element.name+'"]', submit_form).is(":checked") ) {
                        opsd_show_error_message( element , opsd_global1.message_verif_requred_for_check_box);
                        return;                            
                    }
                }
                if  ( element.type =='radio' ) {
                    if ( ! jQuery(':radio[name="'+element.name+'"]', submit_form).is(":checked") ) {
                        opsd_show_error_message( element , opsd_global1.message_verif_requred_for_radio_box);
                        return;                            
                    }
                }
                if  ( ( element.type !='checkbox' ) && ( element.type !='radio' ) && ( inp_value === '' ) ) {
                    opsd_show_error_message( element , opsd_global1.message_verif_requred);
                    return;
                }
            }

            // Validation Check --- Email correct filling field
            if ( element.className.indexOf( 'opsd-validate-email' ) !== -1 ){                
                var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,})$/;
                if ( ( inp_value != '' ) && ( reg.test(inp_value) == false ) ) {
                    opsd_show_error_message( element ,  opsd_global1.message_verif_email );
                    return;
                }
            }

            /*
            // Validation Check --- Same Email Field
            if ( ( element.className.indexOf('wpdev-validates-as-email') !== -1 ) && ( element.className.indexOf('same_as_') !== -1 ) ) { 

                // Get  the name of Primary Email field from the "same_as_NAME" class                    
                var primary_email_name = element.className.match(/same_as_([^\s])+/gi); 
                if (primary_email_name != null) { // We found
                    primary_email_name = primary_email_name[0].substr(8);

                    // Recehck if such primary email field exist in the  form
                    if (jQuery('[name="' + primary_email_name  + '"]').length > 0) {

                        // Recheck the values of the both emails, if they do  not equla show warning                    
                        if ( jQuery('[name="' + primary_email_name  + '"]').val() !== inp_value ) {
                            opsd_show_error_message( element , message_verif_same_emeil );return;
                        }
                    }
                }
                // Skip one loop for the email veryfication field
                continue;
            } */

            /*
            // Get Form Data
            if ( element.name !== ('captcha_input' ) ) {
                if (formdata !=='') formdata +=  '~';                                                // next field element

                el_type = element.type;
                if ( element.className.indexOf('wpdev-validates-as-email') !== -1 )  el_type='email';
                if ( element.className.indexOf('wpdev-validates-as-coupon') !== -1 ) el_type='coupon';

                inp_value = inp_value + '';
                inp_value = inp_value.replace(new RegExp("\\^",'g'), '&#94;'); // replace registered characters
                inp_value = inp_value.replace(new RegExp("~",'g'), '&#126;'); // replace registered characters

                inp_value = inp_value.replace(/"/g, '&#34;'); // replace double quot
                inp_value = inp_value.replace(/'/g, '&#39;'); // replace single quot

                formdata += el_type + '^' + element.name + '^' + inp_value ;                    // element attr
            } */
        }

    }  // End Fields Loop
    
        
    submit_form.submit();                                                       // Submit Form,  if previously  was no interuptions 
}


/**
 * Show message under specific element
 * 
 * @param {type} element - jQuery definition  of the element
 * @param {type} errorMessage - String message
 * @param {type} message_type "" | "alert-warning" | "alert-success" | "alert-info" | "alert-danger"
 */
function opsd_show_message_under_element( element , errorMessage , message_type) {
    
     opsd_scroll_to( element );
    
     if ( jQuery( element ).attr('type') == "radio" ) {
        jQuery( element ).parent().parent().parent()
                .after('<span class="opsd-near-field-message alert '+ message_type +'">'+ errorMessage +'</span>'); // Show message

    } else if (jQuery( element ).attr('type') == "checkbox") {
        jQuery( element ).parent()
                .after('<span class="opsd-near-field-message alert '+ message_type +'">'+ errorMessage +'</span>'); // Show message

    } else {
        jQuery( element )
                .after('<span class="opsd-near-field-message alert '+ message_type +'">'+ errorMessage +'</span>'); // Show message
    }
    jQuery(".widget_opsd .opsd-near-field-message")
            .css( {'vertical-align': 'sub' } ) ;
    jQuery(".opsd-near-field-message")
            .animate( {opacity: 1}, 10000 )
            .fadeOut( 2000 ); 
}


// Show Error Message in  Form  at Front End
function opsd_show_error_message( element , errorMessage) {

    // Scroll to the element
    opsd_scroll_to( element );

    jQuery("[name='"+ element.name +"']")
            .fadeOut( 350 ).fadeIn( 300 )
            .fadeOut( 350 ).fadeIn( 400 )
            .fadeOut( 350 ).fadeIn( 300 )
            .fadeOut( 350 ).fadeIn( 400 )
            .animate( {opacity: 1}, 4000 )
    ;  // mark red border
    
    if (jQuery("[name='"+ element.name +"']").attr('type') == "radio") {
        jQuery("[name='"+ element.name +"']").parent().parent()//.parent()
                .after('<span class="opsd-near-field-message alert alert-warning">'+ errorMessage +'</span>'); // Show message

    } else if (jQuery("[name='"+ element.name +"']").attr('type') == "checkbox") {
        jQuery("[name='"+ element.name +"']").parent().parent()
                .after('<span class="opsd-near-field-message alert alert-warning">'+ errorMessage +'</span>'); // Show message

    } else {
        jQuery("[name='"+ element.name +"']")
                .after('<span class="opsd-near-field-message alert alert-warning">'+ errorMessage +'</span>'); // Show message
    }
    jQuery(".opsd-near-field-message")
            .css( {'padding' : '5px 5px 4px', 'margin' : '2px', 'vertical-align': 'top', 'line-height': '32px' } );
    
    if ( element.type == 'checkbox' )
        jQuery(".opsd-near-field-message").css( { 'vertical-align': 'middle'} );
            
    jQuery(".widget_opsd .opsd-near-field-message")
            .css( {'vertical-align': 'sub' } ) ;
    jQuery(".opsd-near-field-message")
            .animate( {opacity: 1}, 10000 )
            .fadeOut( 2000 );   
    element.focus();    // make focus to elemnt
    return;

}


/**
 * Reload the page with  new parameter value.
 * 
 * @param {type} url            - full URL  of the page,  can include or exclude that parameter
 * @param {type} param          - URL parameter name
 * @param {type} value          - URL parameter value
 * @returns {undefined}
 */
function opsd_reload_page_with_paramater( url, param, value ) {
    var hash       = {};
    var parser     = document.createElement('a');

    parser.href    = url;

    var parameters = parser.search.split(/\?|&/);

    for(var i=0; i < parameters.length; i++) {
        if(!parameters[i])
            continue;

        var ary      = parameters[i].split('=');
        hash[ary[0]] = ary[1];
    }

    hash[param] = value;

    var list = [];  
    Object.keys(hash).forEach(function (key) {
        list.push(key + '=' + hash[key]);
    });

    parser.search = '?' + list.join('&');
    //return parser.href;
    window.location.href = parser.href;
}


jQuery(window).load(function(){

    // Color Text picker ///////////////////////////////////////////////////////
    if ( jQuery('.field-text-color').length > 0 ) {
        jQuery('.field-text-color').iris( {
            change: function(event, ui){
                jQuery(this).css( { backgroundColor: ui.color.toString() } );            
                jQuery(this).closest('.fields-color-group').find('.fieldvalue').css( { color: ui.color.toString() } );
            }
            , hide: true
            , border: true
            , palettes: ['#333', '#555', '#777', '#aaa', '#fff']        
        } ).each( function() {
            jQuery(this).css( { backgroundColor: jQuery(this).val() } );
        })
        .click(function(){
            jQuery('.iris-picker').hide();
            jQuery(this).closest('div').find('.iris-picker').show();
        });
    }
    // Color Background picker ///////////////////////////////////////////////// 
    if ( jQuery('.field-background-color').length > 0 ) {
        jQuery('.field-background-color').iris( {
            change: function(event, ui){
                jQuery(this).css( { backgroundColor: ui.color.toString() } );
                jQuery(this).closest('.fields-color-group').find('.fieldvalue').css( { backgroundColor: ui.color.toString() } );
            }
            , hide: true
            , border: true
            , palettes: [ '#FFEE99', '#459', '#78b', '#ab0', '#df5d5d', '#f0f']        
        } ).each( function() {
            jQuery(this).css( { backgroundColor: jQuery(this).val() } );
        })
        .click(function(){
            jQuery('.iris-picker').hide();
            jQuery(this).closest('div').find('.iris-picker').show();
        });

        jQuery('.field-text-color, .field-background-color').click(function(event){
            event.stopPropagation();
        });
    }

    ////////////////////////////////////////////////////////////////////////////
    // General Color picker in settings table //////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////
    if ( jQuery('.opsd_colorpick').length > 0 ) {
        jQuery('.opsd_colorpick').iris( {
            change: function(event, ui){
                jQuery(this).css( { backgroundColor: ui.color.toString() } );
            }
            , hide: true
            , border: true
            , palettes: ['#125', '#459', '#78b', '#ab0', '#de3', '#f0f']        
        } ).each( function() {
            jQuery(this).css( { backgroundColor: jQuery(this).val() } );
        })
        .click(function(){
            jQuery('.iris-picker').hide();
            jQuery(this).closest('td').find('.iris-picker').show();
        });

        jQuery('body').click(function() {
            jQuery('.iris-picker').hide();
        });

        jQuery('.opsd_colorpick').click(function(event){
            event.stopPropagation();
        });
    }
    
});            



////////////////////////////////////////////////////////////////////////////
// Support Functions
////////////////////////////////////////////////////////////////////////////

/**
 * Reset of WP Editor or TextArea Content
 * @param {string} editor_textarea_id - ID of element
 * @param {string} editor_textarea_content - Content
 */
function opsd_reset_wp_editor_content( editor_textarea_id, editor_textarea_content ) {
    if( typeof tinymce != "undefined" ) {
        var editor = tinymce.get( editor_textarea_id );
        if( editor && editor instanceof tinymce.Editor ) {
            editor.setContent( editor_textarea_content );
            editor.save( { no_events: true } );
        } else {
            jQuery( '#' + editor_textarea_id ).val( editor_textarea_content );
        }
    } else {
        jQuery( '#' + editor_textarea_id ).val( editor_textarea_content );
    }
}