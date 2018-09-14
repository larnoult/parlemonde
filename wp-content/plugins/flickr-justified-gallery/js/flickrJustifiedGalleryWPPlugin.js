/* 
Flickr Justified Gallery Wordpress Plugin
Author: Miro Mannino
Author URI: http://miromannino.com
*/

function fjgwppDisableContextMenu(imgs) {
	function absorbEvent_(event) {
		var e = event || window.event;
		e.preventDefault && e.preventDefault();
		e.stopPropagation && e.stopPropagation();
		e.cancelBubble = true;
		e.returnValue = false;
		return false;
	}
	imgs.on("contextmenu ontouchstart ontouchmove ontouchend ontouchcancel", absorbEvent_);
}

jQuery(document).ready(function() {
	if (typeof fjgwpp_galleriesInit_functions !== "undefined") {
		for (var i in fjgwpp_galleriesInit_functions) {
			fjgwpp_galleriesInit_functions[i]();
		}
	}
});