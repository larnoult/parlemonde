jQuery(function($) {
	$(document).ready(function(){
		$('#map_button_text').remove();
		$(document).on('click', '#map_button_visual', function(){
			tb_show(tinymceOptions.textAdd, tinymceOptions.adminUrl+'admin-ajax.php?action=get_mm_list&mode=html&TB_iframe');
			return false;
		});
		$('#wp-content-media-buttons').append('<a style="margin-left:5px;" class="button" title="'+tinymceOptions.textAdd+'" id="map_button_visual" href="#"><div style="float:left;"><img src="'+tinymceOptions.leafletPluginUrl+'inc/img/icon-tinymce.png" style="padding:0 5px 3px 0;"></div><div style="float:right;padding-top:0px;">'+tinymceOptions.textAdd+'</div></a>');
	});
});
