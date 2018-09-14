jQuery( document ).ready( function( $ ){
 
	/*
	 * bind the media uploader at current and future ( 'live()' ) image upload buttons
	 * single image selection
	 */	 
	$( document ).on( 'click', '.qfi_set_fi', function( e ) {
		
		e.preventDefault();

		// get number of post
		var post_id = this.id.match( /[0-9]+/ );

		// get parent jQ object of clicked link
		var origin_parent = $( this ).parent();

		// Extend the wp.media object for selection of a single image
		var text_link = $( this ).text();
		var custom_uploader = wp.media.frames.file_frame = wp.media( {
			title: text_link,
			library: {
				type: 'image'
			},
			button: {
				text: text_link
			},
			multiple: false
		} );

		// When a file is selected, grab the URL and set it as the text field's value
		custom_uploader.on( 'select', function() {
			// get selected image
			var attachment = custom_uploader.state().get( 'selection' ).first().toJSON();
			
			// set image as featured for current post via Ajax and print response
			jQuery.post( ajaxurl, {
				action:			 'qfi_set_thumbnail',
				post_id:		post_id,
				thumbnail_id:	attachment.id,
				qfi_nonce:		qfi_i18n.nonce,
				cookie:			encodeURIComponent( document.cookie )
			}, function( response ) {
				// fade in new content
				origin_parent.html( response ).hide().fadeIn();
			});

		} );
 
		//Open the uploader dialog
		custom_uploader.open();
		
		// prevent following the link href
		return false;
 
	} );

	/*
	 * remove featured image from post and
	 * display 'set image' link
	 */	 
	$( document ).on( 'click', '.qfi_delete_fi', function( e ) {
		
		e.preventDefault();

		// get number of post
		var post_id = this.id.match( /[0-9]+/ );

		// get parent jQ object of clicked link
		var origin_parent = $( this ).parent();

		// remove featured image
		jQuery.post( ajaxurl, {
			action:		'qfi_delete_thumbnail',
			post_id:	post_id,
			qfi_nonce:	qfi_i18n.nonce,
			cookie:		encodeURIComponent( document.cookie )
		}, function( response ) {
			// fade in new content
			origin_parent.html( response ).hide().fadeIn();
		});

		// prevent following the link href
		return false;
 
	} );

} );
