jQuery( document ).ready( function( $ ){
 
	/*
	 * Media uploader
	 */

	// placeholder url
	var blank_url = $( '#placeholder_url' ).val();

	// bind the media uploader at current and future ( 'live()' ) image upload buttons
    $( document ).on( 'click', '.imageupload', function( e ) {
		
        e.preventDefault();
 
		var custom_uploader;
		
		// get number of row
		var row_number = this.id.match( /[0-9]+/ );

        //Extend the wp.media object
		var selector_upload_text = '#upload_image_XX';
        custom_uploader = wp.media.frames.file_frame = wp.media( {
            title: $( selector_upload_text ).val(),
            library: {
                type: 'image'
            },
            button: {
                text: $( selector_upload_text ).val()
            },
            multiple: false
        } );
 
        //When a file is selected, grab the URL and set it as the text field's value
        custom_uploader.on( 'select', function() {
			var selector_image_element = '#selected_image_' + row_number;
            var attachment = custom_uploader.state().get( 'selection' ).first().toJSON();
            $( '#image_id_' + row_number ).val( attachment.id );
            $( selector_image_element ).attr( 'src', attachment.url );
            $( selector_image_element ).attr( 'class', 'attachment-thumbnail qfi_preset_image' );
        } );
 
        //Open the uploader dialog
        custom_uploader.open();
 
    } );

	/*
	 * Remove rule row
	 */
	// clear default image
    $( document ).on( 'click', '.remove_rule', function( e ) {
 
        e.preventDefault();
		
		if( confirm( $( '#confirmation_question' ).val() ) ) {

			// get number of row
			var row_number = this.id.match( /[0-9]+/ );
			// remove table row
			$( '#row_' + row_number ).remove();
		}
    } );
 
	/*
	 * Add rule row
	 */
 	$( '#add_rule_button' ).click( function( e ){

		e.preventDefault();
		
		// get template html
		template_row = $( '#template_row' ).clone();
		// detect new row number
		row_number = parseInt( $( 'table.widefat tbody tr' ).last().prev().attr( 'id' ).match( /[0-9]+/ )) + 1;
		if ( ! isFinite( row_number ) ) {
			row_number = 2; // assume second row if not a valid number
		}
		
		// replace placeholder with row number:
		// text replacements
		template_row.find( '#image_id_XX' ).each( function( index, el ) {
			el_val = String( $( el ).attr( 'name' ));
			$( el ).attr( 'name', el_val.replace( 'XX', row_number ));
		} );
		template_row.find( '[ for*="taxonomy_XX" ]' ).each( function( index, el ) {
			el_val = String( $( el ).attr( 'for' ));
			$( el ).attr( 'for', el_val.replace( 'XX', row_number ));
		} );
		template_row.find( '#taxonomy_XX' ).each( function( index, el ) {
			el_val = String( $( el ).attr( 'name' ));
			$( el ).attr( 'name', el_val.replace( 'XX', row_number ));
		} );
		template_row.find( '[ for*="matchterm_XX" ]' ).each( function( index, el ) {
			el_val = String( $( el ).attr( 'for' ));
			$( el ).attr( 'for', el_val.replace( 'XX', row_number ));
		} );
		template_row.find( '#matchterm_XX' ).each( function( index, el ) {
			el_val = String( $( el ).attr( 'name' ));
			$( el ).attr( 'name', el_val.replace( 'XX', row_number ));
		} );
		// attribute replacements
		template_row.attr( 'id', 'row_' + row_number );
		template_row.find( 'td.num' ).text( row_number );
		template_row.find( '#image_id_XX' ).attr( 'id', 'image_id_' + row_number );
		template_row.find( '#selected_image_XX' ).attr( 'id', 'selected_image_' + row_number );
		template_row.find( '#upload_image_XX' ).attr( 'name', 'upload_image_' + row_number );
		template_row.find( '#upload_image_XX' ).attr( 'id', 'upload_image_' + row_number );
		template_row.find( '#taxonomy_XX' ).attr( 'id', 'taxonomy_' + row_number );
		template_row.find( '#matchterm_XX' ).attr( 'id', 'matchterm_' + row_number );
		template_row.find( '#remove_rule_XX' ).attr( 'name', 'remove_rule_' + row_number );
		template_row.find( '#remove_rule_XX' ).attr( 'id', 'remove_rule_' + row_number );
		// add row color alternation if row number is odd
		if ( 0 != row_number % 2 ) {
			template_row.attr( 'class', 'alternate' );
		}

		// display new row
		template_row.insertBefore( '#template_row' );
		
	} );
	
	/*
	 * Do not submit template row
	 */
	$( '#submit' ).click( function(){
		$( '#template_row' ).remove();
		return true;
	} );
	
} );
