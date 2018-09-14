jQuery(document).ready(function() {
jQuery("#insert-media-button").click(	function() {

jQuery(document).ready(function() {
		console.log("media");
	
	if (jQuery("div.media-frame-menu > div > a:nth-child(3)").text()=="Créer une liste de lecture audio"){
		console.log(jQuery("div.media-frame-menu > div > a:nth-child(3)").text());
		jQuery("div.media-frame-menu > div > a:nth-child(3)").css("display","none");
	} else {
		console.log(jQuery("div.media-frame-menu > div > a:nth-child(3)").text());

	}
	
	if (jQuery("div.media-frame-menu > div > a:nth-child(4)").text()=="Créer une liste de lecture vidéo"){
		console.log(jQuery("div.media-frame-menu > div > a:nth-child(3)").text());
		jQuery("div.media-frame-menu > div > a:nth-child(4)").css("display","none");
	} else {
		console.log(jQuery("div.media-frame-menu > div > a:nth-child(4)").text());

	}
	
	
	});
	 });
 });