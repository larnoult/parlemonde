(function ($) {
	$(document).ready(function() {
		$('*').each(function () {
			$.each(this.attributes, function(k, v) {
				if (v.value.indexOf('{mnp_mark}') >= 0) v.value = v.value.replace(/{\/?mnp_mark}/g, '');
			});
			var text = $(this).contents().filter(function() { return this.nodeType == 3; })[0];
			var title = (text ? text.nodeValue : '').match(/{mnp_mark}(.*){\/mnp_mark}/);
			if (title) $(text).replaceWith($('.mnp-title-wrapper').html().replace('{title}', title[1]));
		});
		$('.mnp-title-wrapper').remove();
	});
})(jQuery);