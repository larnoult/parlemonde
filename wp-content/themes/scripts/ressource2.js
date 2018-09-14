jQuery(document).ready(function(){
	
	jQuery.preload('http://www.parlemonde.fr/wp-content/uploads/2015/05/sri-lanka-ressources-coco.jpg',
						'http://www.parlemonde.fr/wp-content/uploads/2015/05/sri-lanka-ressources-tourisme.jpg',
						'http://www.parlemonde.fr/wp-content/uploads/2015/05/sri-lanka-ressources-peche.jpg',
						'http://www.parlemonde.fr/wp-content/uploads/2015/05/sri-lanka-ressources-eoles.jpg'
	);
	
	jQuery(".coco").hover(function() {
		jQuery("#ressource").attr("src","http://www.parlemonde.fr/wp-content/uploads/2015/05/sri-lanka-ressources-coco.jpg");
			}, function() {
		jQuery("#ressource").attr("src","http://www.parlemonde.fr/wp-content/uploads/2015/05/sri-lanka-ressources.jpg");
	});

	jQuery("#tourisme").hover(function() {
		jQuery("#ressource").attr("src","http://www.parlemonde.fr/wp-content/uploads/2015/05/sri-lanka-ressources-tourisme.jpg");
			}, function() {
		jQuery("#ressource").attr("src","http://www.parlemonde.fr/wp-content/uploads/2015/05/sri-lanka-ressources.jpg");
	});
	
	jQuery("#peche").hover(function() {
		jQuery("#ressource").attr("src","http://www.parlemonde.fr/wp-content/uploads/2015/05/sri-lanka-ressources-peche.jpg");
			}, function() {
		jQuery("#ressource").attr("src","http://www.parlemonde.fr/wp-content/uploads/2015/05/sri-lanka-ressources.jpg");
	});
	
	jQuery(".eole").hover(function() {
		jQuery("#ressource").attr("src","http://www.parlemonde.fr/wp-content/uploads/2015/05/sri-lanka-ressources-eoles.jpg");
			}, function() {
		jQuery("#ressource").attr("src","http://www.parlemonde.fr/wp-content/uploads/2015/05/sri-lanka-ressources.jpg");
	});
});
