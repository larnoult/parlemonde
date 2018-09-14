;(function ($, window, document, undefined) {
    $.wcpWPMedia = function(inputSelector, buttonSelector, callback)  {
        var clicked_button = false;
        $(document).on('click', buttonSelector, function(e) {
            e.preventDefault();
            var selected_img;
            clicked_button = $(this);

            // configuration of the media manager new instance
            wp.media.frames.gk_frame = wp.media({
                title: 'Select Media',
                multiple: false,
                library: {
                    type: 'image'
                },
                button: {
                    text: 'Use Selected Media'
                }
            });

            // Function used for the image selection and media manager closing
            var gk_media_set_image = function() {
                var selection = wp.media.frames.gk_frame.state().get('selection');

                // no selection
                if (!selection) {
                    return;
                }

                // iterate through selected elements
                selection.each(function(attachment) {
                    var url = attachment.attributes.url;
                    clicked_button.prev(inputSelector).val(url);
                    callback(url);
                });
            };

            // closing event for media manger
            wp.media.frames.gk_frame.on('close', gk_media_set_image);
            // image selection event
            wp.media.frames.gk_frame.on('select', gk_media_set_image);
            // showing media manager
            wp.media.frames.gk_frame.open();
        });
    };
})(jQuery, window, document);
