
jQuery(document).ready(function($) {
    $('.js-bpml-register-fields').click(function() {
        var button = $(this);
        button.off().after('<div class="spinner"></div>').next().show();
        $.post(ajaxurl, button.data('bpml'), function(response) {
            button.next().remove();
            if ( response != '0' ) {
                button.text(response).parents('div.updated').delay(1500).fadeOut('slow');
            } else {
                button.text('error');
            }
        });
    });
});