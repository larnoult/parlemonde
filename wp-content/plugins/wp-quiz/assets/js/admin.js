jQuery(document).on('click', '.wpquiz-dismiss-notice', function(e){
	e.preventDefault();
	jQuery(this).parent().remove();
	jQuery.ajax({
		type: "POST",
		url: ajaxurl,
		data: {
			action: 'mts_dismiss_wpquiz_notice',
			dismiss: jQuery(this).data('ignore')
		}
	});
	return false;
});