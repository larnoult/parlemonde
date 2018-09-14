(function($){
	"use strict";

	$.imgupload = $.imgupload || {};

	$(document).ready(function () {
		$.imgupload();
	});
	$.imgupload = function(){
	// When the user clicks on the Add/Edit gallery button, we need to display the gallery editing
		$('body').on({
			click: function(event){
				var current_imgupload = $(this).closest('.kad_img_upload_widget');

				// Make sure the media gallery API exists
				if ( typeof wp === 'undefined' || ! wp.media ) {
				    return;
				}
				event.preventDefault();

				var frame;
				// Activate the media editor
				var $$ = $(this);

				// If the media frame already exists, reopen it.
				if ( frame ) {
				        frame.open();
				        return;
				    }

				    // Create the media frame.
				    frame = wp.media({
				        multiple: false,
				        library: {type: 'image'}
				    });

				        // When an image is selected, run a callback.
				frame.on( 'select', function() {

				    // Grab the selected attachment.
				    var attachment = frame.state().get('selection').first();
				    frame.close();

				    current_imgupload.find('.kad_custom_media_url').val(attachment.attributes.url);
				    current_imgupload.find('.kad_custom_media_id').val(attachment.attributes.id);
				    var thumbSrc = attachment.attributes.url;
				    if (typeof attachment.attributes.sizes !== 'undefined' && typeof attachment.attributes.sizes.thumbnail !== 'undefined') {
				        thumbSrc = attachment.attributes.sizes.thumbnail.url;
				    } else {
				        thumbSrc = attachment.attributes.icon;
				    }
				    current_imgupload.find('.kad_custom_media_image').attr('src', thumbSrc);
				    current_imgupload.find('.kad_custom_media_url').trigger('change');
				});

				// Finally, open the modal.
				frame.open();
			}

		}, '.kad_custom_media_upload');
	 };
})(jQuery);

 (function($){
    "use strict";
    
    $.gallery = $.gallery || {};
    
    $(document).ready(function () {
        $.gallery();
    });

    $.gallery = function(){
        // When the user clicks on the Add/Edit gallery button, we need to display the gallery editing
        $('body').on({
            click: function(event){
                var current_gallery = $(this).closest('.kad_widget_image_gallery');

                if (event.currentTarget.id === 'clear-gallery') {
                    //remove value from input 
                    
                    var rmVal = current_gallery.find('.gallery_values').val('');

                    //remove preview images
                    current_gallery.find(".gallery_images").html("");
                    current_gallery.find( '.gallery_values' ).trigger('change');

                    return;

                }

                // Make sure the media gallery API exists
                if ( typeof wp === 'undefined' || ! wp.media || ! wp.media.gallery ) {
                    return;
                }
                event.preventDefault();

                // Activate the media editor
                var $$ = $(this);

                var val = current_gallery.find('.gallery_values').val();
                var final;
                if (!val) {
                    final = '[gallery ids="0"]';
                } else {
                    final = '[gallery ids="' + val + '"]';
                }

                var frame = wp.media.gallery.edit(final);

                    
                // When the gallery-edit state is updated, copy the attachment ids across
                frame.state('gallery-edit').on( 'update', function( selection ) {

                    //clear screenshot div so we can append new selected images
                    current_gallery.find(".gallery_images").html("");
                    
                    var element, preview_html= "", preview_img;
                    var ids = selection.models.map(function(e){
                        element = e.toJSON();
                        preview_img = typeof element.sizes.thumbnail !== 'undefined'  ? element.sizes.thumbnail.url : element.url ;
                        preview_html = "<a class='of-uploaded-image' target='_blank' rel='external' href='"+preview_img+"'><img class='gallery-widget-image' src='"+preview_img+"' /></a>";
                        current_gallery.find(".gallery_images").append(preview_html);
                        return e.id;
                    });
                    current_gallery.find('.gallery_values').val(ids.join(','));
                    current_gallery.find( '.gallery_values' ).trigger('change');
    
                });


                return false;
            }
        }, '.gallery-attachments');
    };
})(jQuery);