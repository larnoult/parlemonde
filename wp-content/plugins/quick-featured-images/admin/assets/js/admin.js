jQuery( document ).ready( function( $ ){


	// single image selection
	var selector_single_image_button = '#upload_image_button';
	$( selector_single_image_button ).click( function( e ) {

		e.preventDefault();

		var custom_uploader;
		
		//Extend the wp.media object
		custom_uploader = wp.media.frames.file_frame = wp.media( {
			title: $( selector_single_image_button ).val(),
			library: {
				type: 'image'
			},
			button: {
				text: $( selector_single_image_button ).val()
			},
			multiple: false
		} );

		//When a file is selected, grab the URL and set it as the text field's value
		custom_uploader.on( 'select', function() {
			var selector_image = '#selected_image';
			var attachment = custom_uploader.state().get( 'selection' ).first().toJSON();
			$( '#image_id' ).val( attachment.id );
			$( selector_image ).attr( 'src', attachment.url );
			$( selector_image ).attr( 'class', 'attachment-thumbnail' );
			$( selector_image ).attr( 'style', 'width:95%' );
		} );

		//Open the uploader dialog
		custom_uploader.open();

	} );

	// multiple images selection
	var selector_multiple_images_button = '#select_images_multiple';
	$( selector_multiple_images_button ).click( function( e ) {

		e.preventDefault();

		var custom_uploader;

		//Extend the wp.media object
		var selector_advice = '#selection_advice';
		
		custom_uploader = wp.media.frames.file_frame = wp.media( {
			title: $( selector_multiple_images_button ).val() + ': ' + $( selector_advice ).val(),
			library: {
				type: 'image'
			},
			button: {
				text: $( selector_multiple_images_button ).val()
			},
			multiple: true
		} );

		//When a file is selected, grab the URL and set it as the text field's value
		custom_uploader.on( 'select', function() {
			var selector_blank_img = '#blank_image';
			var img_set_classname = 'qfi_random_img';
			// remove existing multiple images of rule
			$( '.' + img_set_classname ).remove();
			// build new list
			var attachments = custom_uploader.state().get( 'selection' ).toJSON();
			var attachments_ids = [];
			for ( i = 0; i < attachments.length; i++ ) {
				// put id into array
				attachments_ids[ i ] = attachments[ i ].id
				// add image
				$( '<img src="' + attachments[ i ].url + '" alt="" class="attachment-thumbnail ' + img_set_classname + '">' ).insertBefore( selector_blank_img );
			}
			$( '#multiple_image_ids' ).val( attachments_ids.toString() );
		} );

		//Open the uploader dialog
		custom_uploader.open();

	} );

} );
