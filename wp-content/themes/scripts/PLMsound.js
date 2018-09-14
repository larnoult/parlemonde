//var audio = jQuery("#PLMSound")[0];
//jQuery("nav a").mouseenter(function() {
//  audio.play();
//});

var audio = jQuery("#PLMSound")[0];

jQuery(function(){
jQuery('#homeheader').toggle(
	function() {	audio.play();},
	function() {	audio.pause();}
	)
});
