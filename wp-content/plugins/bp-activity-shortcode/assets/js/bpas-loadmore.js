jQuery(document).ready(function( $ ){
    jQuery('div.bpas-shortcode-activities li.load-more').unbind('click');
    jQuery('div.bpas-shortcode-activities li.load-more').off('click');

    jQuery('div.bpas-shortcode-activities').on('click', 'li.load-more', function() {

        var $this = $(this);
        var $form = $this.parents('div.bpas-shortcode-activities').nextAll('form.bpas-activities-args');
        var data = $form.serialize();
        data += '&action=bpas_load_activities';
        console.log(data);
        var page = $form.find('.bps-input-current-page').val();
        $.post( ajaxurl, data, function(resp){
            if (resp.success ) {
                page++;
                $form.find('.bps-input-current-page').val(page);
                $this.hide();//prevAll('li').remove();
                $this.parents('ul.activity-list').append(resp.data);//.insertBefore( $this );
            }
        }, 'json' );

        return false;
    });

    // for group.


    $('.bpas-post-form-wrapper').each(function () {
        var $this = $(this);
        var $settingsForm =$this.nextAll('form.bpas-activities-args');

        var object = $settingsForm.find('.bpas_input_object').val();
        var primary_id = $settingsForm.find('.bpas_input_primary_id').val();
        console.log(object);
        if ('groups' === object && parseInt(primary_id) > 0) {
            // we are overriding to make sure user can post to hidden groups too if the admin allows.
            // select box only allows valid options as selected value.
            var $postIn = $this.find('#whats-new-post-in');
            $postIn.empty();
            $postIn.html("<option value='" + primary_id + "'>" + primary_id + "</option>");//make sure it is alaways valid.
            $postIn.val(primary_id);
            $this.find('#whats-new-post-in-box').hide();
        }

    });

});