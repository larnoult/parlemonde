jQuery(function($){

  var hidden = $('.mgjp-mv-permission-select');

  $('#mgjp_mv_protection_toggle').change(function(){
    if (this.checked)
      hidden.addClass('mgjp-mv-active');
    else
      hidden.removeClass('mgjp-mv-active');
  }).change();

}(jQuery));