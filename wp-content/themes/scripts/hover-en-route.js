jQuery(document).ready(function(){

	
	jQuery.preload('http://www.parlemonde.org/wp-content/uploads/2014/05/france-gif-ombre.gif',
						'http://www.parlemonde.org/wp-content/uploads/2014/05/salvador-gif-ombre.gif'
	);
	
	jQuery("#France").hover(function() {
		jQuery(this).attr("src","http://www.parlemonde.org/wp-content/uploads/2014/05/france-gif-ombre.gif");
			}, function() {
		jQuery(this).attr("src","http://www.parlemonde.org/wp-content/uploads/2014/05/france.png");
	});

	jQuery("#Liban").hover(function() {
		jQuery(this).attr("src","http://www.parlemonde.org/wp-content/uploads/2014/05/liban-gif-ombre.gif");
			}, function() {
		jQuery(this).attr("src","http://www.parlemonde.org/wp-content/uploads/2014/05/liban.png");
	});
	
	jQuery("#Salvador").hover(function() {
		jQuery(this).attr("src","http://www.parlemonde.org/wp-content/uploads/2014/05/salvador-gif-ombre.gif");
			}, function() {
		jQuery(this).attr("src","http://www.parlemonde.org/wp-content/uploads/2014/05/salvador.png");
	});
	
	
});
