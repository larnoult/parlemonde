jQuery(document).ready(function($) {

  jQuery('#bpaf_setting_error_settings_updated').hide();
  jQuery( "#bp_activity_filter_display_setting_form_submit" ).on( 'click', function(){
    var form_data = jQuery( '#bp_activity_filter_display_setting_form' ).serialize();
    jQuery("#bp_activity_filter_display_setting_form .spinner").css('visibility', 'visible');
    jQuery.post(
                ajaxurl,
                {
                'action'         : 'bp_activity_filter_save_display_settings',
                'form_data'      : form_data
                },
                function () {
                  jQuery("#bp_activity_filter_display_setting_form .spinner").css('visibility', 'hidden');
                  jQuery('#bpaf_setting_error_settings_updated').show();
                }
            );
  });

  jQuery( "#bp_activity_filter_hide_setting_form_submit" ).on( 'click', function(){
    var form_data = jQuery( '#bp_activity_filter_hide_setting_form' ).serialize();
    jQuery("#bp_activity_filter_hide_setting_form .spinner").css('visibility', 'visible');
    jQuery.post(
                ajaxurl,
                {
                'action'         : 'bp_activity_filter_save_hide_settings',
                'form_data'      : form_data
                },
                function () {
                  jQuery("#bp_activity_filter_hide_setting_form .spinner").css('visibility', 'hidden');
                  jQuery('#bpaf_setting_error_settings_updated').show();
                }
            );
  });

  jQuery( "#bp_activity_filter_cpt_setting_form_submit" ).on( 'click', function(){
    var form_data = jQuery( '#bp_activity_filter_cpt_setting_form' ).serialize();
    jQuery("#bp_activity_filter_cpt_setting_form .spinner").css('visibility', 'visible');
    jQuery.post(
                ajaxurl,
                {
                'action'         : 'bp_activity_filter_save_cpt_settings',
                'form_data'      : form_data
                },
                function () {
                  jQuery("#bp_activity_filter_cpt_setting_form .spinner").css('visibility', 'hidden');
                  jQuery('#bpaf_setting_error_settings_updated').show();
                }
            );
  });

  jQuery( "#bpaf_faq_accordion" ).accordion({
    collapsible: true,
    heightStyle: "content"
  });

});