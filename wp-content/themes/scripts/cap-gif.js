jQuery(document).ready(function(){
	
	jQuery.preload('http://www.parlemonde.org/wp-content/uploads/2014/05/CP-1281.gif');
	
	jQuery('div.imghoverclass').toggle(function(){
  		jQuery(this).children('img').attr("src","http://www.parlemonde.org/wp-content/uploads/2014/05/CP-1281.gif");
			}, function() {
		jQuery(this).children('img').attr("src","http://www.parlemonde.org/wp-content/uploads/2014/05/CP-128.png");
	});
	jQuery('div.imghoverclass').hover(function(){
		jQuery('div.imghoverclass').fadeTo("fast",0.5);
				}, function() {
					jQuery('div.imghoverclass').fadeTo("fast",1);
	});
	
	jQuery('.imghoverclass img').css("cursor","pointer");
});


