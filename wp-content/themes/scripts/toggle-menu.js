jQuery(document).ready(function() {
	console.log("toggle");

jQuery("#toggle-menu").toggle( function() {
	console.log("on");
   jQuery("#nav-second").animate({left: '-44px'});
	jQuery("#toggle-menu").css("background-color","white");
	jQuery("#toggle-menu").css("color","#545454");
	jQuery("#toggle-menu").text("+")
	
   }, function () {
	console.log("off");
   jQuery("#nav-second").animate({left: '0px'});
	jQuery("#toggle-menu").css("background-color","#545454");
	jQuery("#toggle-menu:after").css("content","-");
	jQuery("#toggle-menu").text("-")
	jQuery("#toggle-menu").css("color","white");

});


 });