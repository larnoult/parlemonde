/**
 * @version 1.0
 * @package Secure Downloads 
 * @subpackage BackEnd Main Script Lib
 * @category Scripts
 * 
 * @author wpdevelop
 * @link http://oplugins.com/
 * @email info@oplugins.com
 *
 * @modified 2014.09.10
 */


/** Set item listing row as   R e a d
 * 
 * @param {type} opsd_id
 * @returns {undefined}
 */
function set_opsd_row_read(opsd_id){
    if (opsd_id == 0) {
        jQuery('.new-label').addClass('hidden_items');
        jQuery('.bk-update-count').html( '0' );
    } else {
        jQuery('#opsd_mark_'+opsd_id + '').addClass('hidden_items');
        decrese_new_counter();
    }
}

/** Set item listing row as   U n R e a d
 * 
 * @param {type} opsd_id
 * @returns {undefined}
 */
function set_opsd_row_unread(opsd_id){
    jQuery('#opsd_mark_'+opsd_id + '').removeClass('hidden_items');
    increase_new_counter();
}


/** Increase counter about new items
 * 
 * @returns {undefined}
 */
function increase_new_counter () {
    var my_num = parseInt(jQuery('.bk-update-count').html());
    my_num = my_num + 1;
    jQuery('.bk-update-count').html(my_num);
}

/** Decrease counter about new items
 * 
 * @returns {undefined}
 */
function decrese_new_counter () {
    var my_num = parseInt(jQuery('.bk-update-count').html());
    if (my_num>0){
        my_num = my_num - 1;
        jQuery('.bk-update-count').html(my_num);
    }
}


// Set item listing   R O W   Approved
function set_opsd_row_approved(opsd_id){
    jQuery('#opsd_row_'+opsd_id + ' .opsd-labels .label-approved').removeClass('hidden_items');
    jQuery('#opsd_row_'+opsd_id + ' .opsd-labels .label-pending').addClass('hidden_items');

    jQuery('#opsd_row_'+opsd_id + ' .opsd-dates .field-opsd-date').addClass('approved');

    jQuery('#opsd_row_'+opsd_id + ' .opsd-actions .approve_opsd_link').addClass('hidden_items');
    jQuery('#opsd_row_'+opsd_id + ' .opsd-actions .pending_opsd_link').removeClass('hidden_items');

}

// Set item listing   R O W   Pending
function set_opsd_row_pending(opsd_id){
    jQuery('#opsd_row_'+opsd_id + ' .opsd-labels .label-approved').addClass('hidden_items');
    jQuery('#opsd_row_'+opsd_id + ' .opsd-labels .label-pending').removeClass('hidden_items');

    jQuery('#opsd_row_'+opsd_id + ' .opsd-dates .field-opsd-date').removeClass('approved');

    jQuery('#opsd_row_'+opsd_id + ' .opsd-actions .approve_opsd_link').removeClass('hidden_items');
    jQuery('#opsd_row_'+opsd_id + ' .opsd-actions .pending_opsd_link').addClass('hidden_items');

}

// Remove  item listing   R O W
function set_opsd_row_deleted(opsd_id){
    jQuery('#opsd_row_'+opsd_id).fadeOut(1000);        
    jQuery('#gcal_imported_events_id_'+opsd_id).remove();
}

// Set in item listing   R O W   Resource title
function set_opsd_row_resource_name(opsd_id, resourcename){
    jQuery('#opsd_row_'+opsd_id + ' .opsd-labels .label-resource').html(resourcename);
}

// Set in item listing   R O W   new Remark in hint
function set_opsd_row_remark_in_hint( opsd_id, new_remark ){
    
    jQuery('#opsd_row_' + opsd_id + ' .opsd-actions .remark_opsd_link').attr( 'data-original-title', new_remark );

    if ( new_remark != '' )
        jQuery('#opsd_row_' + opsd_id + ' .opsd-actions .remark_opsd_link i.glyphicon-comment').addClass('red_icon_color');
    else
        jQuery('#opsd_row_' + opsd_id + ' .opsd-actions .remark_opsd_link i.glyphicon-comment').removeClass('red_icon_color');
}

// Set in item listing   R O W   new Remark in hint
function set_opsd_row_payment_status(opsd_id, payment_status, payment_status_show){

    jQuery('#opsd_row_'+opsd_id + ' .opsd-labels .label-payment-status').removeClass('label-danger');
    jQuery('#opsd_row_'+opsd_id + ' .opsd-labels .label-payment-status').removeClass('label-success');

    jQuery('#opsd_row_'+opsd_id + ' .opsd-labels .label-payment-status').html(payment_status_show);

    if (payment_status == 'OK') {
        jQuery('#opsd_row_'+opsd_id + ' .opsd-labels .label-payment-status').addClass('label-success');
    } else if (payment_status == '') {
        jQuery('#opsd_row_'+opsd_id + ' .opsd-labels .label-payment-status').addClass('label-danger');
    } else {
        jQuery('#opsd_row_'+opsd_id + ' .opsd-labels .label-payment-status').addClass('label-danger');
    }
}



// Interface Element
function showSelectedInDropdown(selector_id, title, value){
    jQuery('#' + selector_id + '_selector .opsd_selected_in_dropdown').html( title );
    jQuery('#' + selector_id ).val( value );
    jQuery('#' + selector_id + '_container').hide();
}

//Admin function s for checking all checkbos in one time
function setCheckBoxInTable(el_stutus, el_class){
     jQuery('.'+el_class).attr('checked', el_stutus);

     if ( el_stutus ) {
         jQuery('.'+el_class).parent().parent().addClass('row_selected_color');
     } else {
         jQuery('.'+el_class).parent().parent().removeClass('row_selected_color');
     }
}


// FixIn: 5.4.5
function opsd_get_selected_locale( opsd_id, opsd_active_locale ) {
    
    var id_to_check = "" + opsd_id;
    if ( id_to_check.indexOf('|') == -1 ) {
        var selected_locale = jQuery('#locale_for_item' + opsd_id).val();

        if (  ( selected_locale != '' ) && ( typeof(selected_locale) !== 'undefined' )  ) {
            opsd_active_locale = selected_locale;
        } 
    }
    return opsd_active_locale;
}



 
//FixIn: 6.1.1.10 
// Set item listing   R O W   Trash
function set_opsd_row_trash( opsd_id ){
    jQuery('#opsd_row_'+opsd_id + ' .opsd-labels .label-trash').removeClass('hidden_items');    
    jQuery('#opsd_row_'+opsd_id + ' .opsd-actions .trash_opsd_link').addClass('hidden_items');
    jQuery('#opsd_row_'+opsd_id + ' .opsd-actions .restore_opsd_link').removeClass('hidden_items');
    jQuery('#opsd_row_'+opsd_id + ' .opsd-actions .delete_opsd_link').removeClass('hidden_items');
    
    
    jQuery('#opsd-id-'+opsd_id + ' .label-trash').removeClass('hidden_items');
}

//FixIn: 6.1.1.10 
// Set item listing   R O W   Restore
function set_opsd_row_restore( opsd_id ){    
    jQuery('#opsd_row_'+opsd_id + ' .opsd-labels .label-trash').addClass('hidden_items');    
    jQuery('#opsd_row_'+opsd_id + ' .opsd-actions .trash_opsd_link').removeClass('hidden_items');
    jQuery('#opsd_row_'+opsd_id + ' .opsd-actions .restore_opsd_link').addClass('hidden_items');
    jQuery('#opsd_row_'+opsd_id + ' .opsd-actions .delete_opsd_link').addClass('hidden_items');

    jQuery('#opsd-id-'+opsd_id + ' .label-trash').addClass('hidden_items');
}
 


// Get Selected rows in imported Events list
function get_selected_items_id_in_this_list( list_tag, skip_id_length ) {

    var checkedd = jQuery( list_tag + ":checked" );
    var id_for_approve = "";

    // get all IDs
    checkedd.each(function(){
        var id_c = jQuery(this).attr('id');
        id_c = id_c.substr(skip_id_length,id_c.length-skip_id_length);
        id_for_approve += id_c + "|";
    });

    if ( id_for_approve.length > 1 )
        id_for_approve = id_for_approve.substr(0,id_for_approve.length-1);      //delete last "|"

    return id_for_approve ;

}

// Get the list of ID in selected items from item listing
function get_selected_items_id_in_opsd_listing(){

    var checkedd = jQuery(".opsd_list_item_checkbox:checked");
    var id_for_approve = "";

    // get all IDs
    checkedd.each(function(){
        var id_c = jQuery(this).attr('id');
        id_c = id_c.substr(20,id_c.length-20);
        id_for_approve += id_c + "|";
    });

    if ( id_for_approve.length > 1 )
        id_for_approve = id_for_approve.substr(0,id_for_approve.length-1);      //delete last "|"

    return id_for_approve ;
}





/** Selections of several  checkboxes like in gMail with shift :)
 * Need to  have this structure: 
 * .opsd_selectable_table
 *      .opsd_selectable_head
 *              .check-column
 *                  :checkbox
 *      .opsd_selectable_body
 *          .opsd_row
 *              .check-column
 *                  :checkbox
 *      .opsd_selectable_foot             
 *              .check-column
 *                  :checkbox
 */
( function( $ ){            
    $( document ).ready(function(){
            
	var checks, first, last, checked, sliced, lastClicked = false;

	// check all checkboxes
        $('.opsd_selectable_body').find('.check-column').find(':checkbox').click( function(e) {
	//$('.opsd_selectable_body').children().children('.check-column').find(':checkbox').click( function(e) {
		if ( 'undefined' == e.shiftKey ) { return true; }
		if ( e.shiftKey ) {
			if ( !lastClicked ) { return true; }
			//checks = $( lastClicked ).closest( 'form' ).find( ':checkbox' ).filter( ':visible:enabled' );
                        checks = $( lastClicked ).closest( '.opsd_selectable_body' ).find( ':checkbox' ).filter( ':visible:enabled' );
			first = checks.index( lastClicked );
			last = checks.index( this );
			checked = $(this).prop('checked');
			if ( 0 < first && 0 < last && first != last ) {
				sliced = ( last > first ) ? checks.slice( first, last ) : checks.slice( last, first );
				sliced.prop( 'checked', function() {
					if ( $(this).closest('.opsd_row').is(':visible') )
						return checked;

					return false;
				});
			}
		}
		lastClicked = this;

		// toggle "check all" checkboxes
		var unchecked = $(this).closest('.opsd_selectable_body').find(':checkbox').filter(':visible:enabled').not(':checked');
		$(this).closest('.opsd_selectable_table').children('.opsd_selectable_head, .opsd_selectable_foot').find(':checkbox').prop('checked', function() {
			return ( 0 === unchecked.length );
		});

		return true;
	});

	$('.opsd_selectable_head, .opsd_selectable_foot').find('.check-column :checkbox').on( 'click.wp-toggle-checkboxes', function( event ) {
		var $this = $(this),
			$table = $this.closest( '.opsd_selectable_table' ),
			controlChecked = $this.prop('checked'),
			toggle = event.shiftKey || $this.data('wp-toggle');

		$table.children( '.opsd_selectable_body' ).filter(':visible')
                        .find('.check-column').find(':checkbox')
			//.children().children('.check-column').find(':checkbox')
			.prop('checked', function() {
				if ( $(this).is(':hidden,:disabled') ) {
					return false;
				}

				if ( toggle ) {
					return ! $(this).prop( 'checked' );
				} else if ( controlChecked ) {
					return true;
				}

				return false;
			});

		$table.children('.opsd_selectable_head,  .opsd_selectable_foot').filter(':visible')
                        .find('.check-column').find(':checkbox')
			//.children().children('.check-column').find(':checkbox')
			.prop('checked', function() {
				if ( toggle ) {
					return false;
				} else if ( controlChecked ) {
					return true;
				}

				return false;
			});
	});
    });    
}( jQuery ) );    