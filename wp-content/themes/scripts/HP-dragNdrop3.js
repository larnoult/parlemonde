// version en ligne
jQuery(document).ready(function() {
	
/*	jQuery('#dragPelico').toggle(function(){
  		jQuery(this).attr("src","http://www.parlemonde.org/wp-content/uploads/2014/05/pelico.gif");
			}, function() {
		jQuery(this).attr("src","http://www.parlemonde.org/wp-content/uploads/2014/05/pelico.gif");
	});	*/
	
	// Preload toutes les images fixes
	
	jQuery.preload("http://www.parlemonde.org/wp-content/uploads/2014/06/nuage-projet.png",
					"http://www.parlemonde.org/wp-content/uploads/2014/06/nuage-contact.png",
					"http://www.parlemonde.org/wp-content/uploads/2014/06/nuage-asso.png",
					"http://www.parlemonde.org/wp-content/uploads/2014/06/nuage-cap.png",
					"http://www.parlemonde.org/wp-content/uploads/2014/06/nuage-route.png",
					"http://www.parlemonde.org/wp-content/uploads/2014/05/pelico.gif"
	);
	
	jQuery("#home-page-container > div > div").css("visibility","visible"); // révèle toutes les descriptions d'images
	
	// pour savoir si Pélico est déjà passé sur les nuages
	var indicatorCap=0;
	var indicatorQuoi=0;
	var indicatorRoute=0;
	var indicatorContact=0;
	var indicatorAgir=0;

//	jQuery("#home-page-container div img").fadeTo(0,0.4); // masque un partie toutes les images
	jQuery("#home-page-container > div > div").hide(); // cache toutes les descriptions des nuages
	jQuery("#home-page-container div img").mousedown(function(){ // pour éviter d'avoir l'impression de pouvoir dragger les nuages 
	    return false;
	});
	
	jQuery("#dragPelico").hover(function() {
		jQuery(this).attr("src","http://www.parlemonde.org/wp-content/uploads/2014/05/pelico.gif");
			}, function() {
		jQuery(this).attr("src","http://www.parlemonde.org/wp-content/uploads/2014/05/pelico-repos.png");
	});
	
	
        jQuery('#dragPelico').draggable({
	containment : jQuery('body'),// restreint Pélico dans la zone nuageuse
 revert : "invalid",
revertDuration: 1000 
	});
	
// Définition des évenements drop // 	
	function OnPelicoDrop(nuage){
		
	}
	
	
	 jQuery('#nuage-quoi').droppable({
	    drop : function(){

		jQuery('#dragPelico').position({ //  Pour ramener Pélico sur le point d'interrogation
				"my": "center center",       
				"at": "center-4 center-41" ,  
				"of": jQuery('#nuage-quoi .question'),	
		    using: function( position ) { // pour que le repositionnement de Pélico sur le point d'interrogation soit soft
		        jQuery( this ).animate(position, function() { // et qu'il s'arrête de voler après être arrivé :-)
					jQuery("#dragPelico").attr("src","http://www.parlemonde.org/wp-content/uploads/2014/05/pelico-repos.png");
				}
			);
		    }
		});
		
		if (indicatorQuoi==0) { // si seulement c'est la première fois qu'on le pose dessus
		jQuery("#nuage-quoi img").attr('src','http://www.parlemonde.org/wp-content/uploads/2014/06/nuage-projet.png');
		jQuery("#sumup-quoi").slideDown(500);
		jQuery("#sumup-quoi").animate(
			    { opacity: 1 },
			    { queue: false, duration: 'slow' }
				);
		indicatorQuoi=1;
		}
	}
	
	
	});	
	

	 jQuery('#nuage-cap').droppable({
	    drop : function(){
		jQuery('#dragPelico').position({ //  Pour ramener Pélico sur le point d'interrogation
				"my": "center center",       
				"at": "center-4 center-41" ,  
				"of": jQuery('#nuage-cap .question'),	
		    using: function( position ) { // pour que le repositionnement de Pélico sur le point d'interrogation soit soft
		        jQuery( this ).animate(position, function() { // et qu'il s'arrête de voler après être arrivé :-)
					jQuery("#dragPelico").attr("src","http://www.parlemonde.org/wp-content/uploads/2014/05/pelico-repos.png");
				}
			);
		    }
		});
		
		if (indicatorCap==0) { // si seulement c'est la première fois qu'on le pose dessus
		jQuery("#nuage-cap img").attr('src','http://www.parlemonde.org/wp-content/uploads/2014/06/nuage-cap.png');
		jQuery("#sumup-cap").slideDown(500);
		jQuery("#sumup-cap").animate(
			    { opacity: 1 },
			    { queue: false, duration: 'slow' }
				);
		indicatorCap=1;
		}
	}
	
	});			
		



	
	 jQuery('#nuage-route').droppable({
	    drop : function(){

		jQuery('#dragPelico').position({ //  Pour ramener Pélico sur le point d'interrogation
				"my": "center center",       
				"at": "center-4 center-41" ,  
				"of": jQuery('#nuage-route .question'),	
		    using: function( position ) { // pour que le repositionnement de Pélico sur le point d'interrogation soit soft
		        jQuery( this ).animate(position, function() { // et qu'il s'arrête de voler après être arrivé :-)
					jQuery("#dragPelico").attr("src","http://www.parlemonde.org/wp-content/uploads/2014/05/pelico-repos.png");
				}
			);
		    }
		});
		
		if (indicatorRoute==0) { // si seulement c'est la première fois qu'on le pose dessus
		jQuery("#nuage-route img").attr('src','http://www.parlemonde.org/wp-content/uploads/2014/06/nuage-route.png');
		jQuery("#sumup-route").slideDown(500);
		jQuery("#sumup-route").animate(
			    { opacity: 1 },
			    { queue: false, duration: 'slow' }
				);
		indicatorRoute=1;
		}
	}

	});			
		



	 jQuery('#nuage-1901').droppable({
	    drop : function(){

		jQuery('#dragPelico').position({ //  Pour ramener Pélico sur le point d'interrogation
				"my": "center center",       
				"at": "center-4 center-41" ,  
				"of": jQuery('#nuage-1901 .question'),	
		    using: function( position ) { // pour que le repositionnement de Pélico sur le point d'interrogation soit soft
		        jQuery( this ).animate(position, function() { // et qu'il s'arrête de voler après être arrivé :-)
					jQuery("#dragPelico").attr("src","http://www.parlemonde.org/wp-content/uploads/2014/05/pelico-repos.png");
				}
			);
		    }
		});

		if (indicatorAgir==0) { // si seulement c'est la première fois qu'on le pose dessus
		jQuery("#nuage-1901 img").attr('src','http://www.parlemonde.org/wp-content/uploads/2014/06/nuage-asso.png');
		jQuery("#sumup-1901").slideDown(500);
		jQuery("#sumup-1901").animate(
			    { opacity: 1 },
			    { queue: false, duration: 'slow' }
				);
		indicatorAgir=1;
		}
	}

	});			
		
			
	
	
	 jQuery('#nuage-contact').droppable({
	    drop : function(){

		jQuery('#dragPelico').position({ //  Pour ramener Pélico sur le point d'interrogation
				"my": "center center",       
				"at": "center-4 center-41" ,  
				"of": jQuery('#nuage-contact .question'),	
		    using: function( position ) { // pour que le repositionnement de Pélico sur le point d'interrogation soit soft
		        jQuery( this ).animate(position, function() { // et qu'il s'arrête de voler après être arrivé :-)
					jQuery("#dragPelico").attr("src","http://www.parlemonde.org/wp-content/uploads/2014/05/pelico-repos.png");
				}
			);
		    }
		});
		

		if (indicatorContact==0) { // si seulement c'est la première fois qu'on le pose dessus
		jQuery("#nuage-contact img").attr('src','http://www.parlemonde.org/wp-content/uploads/2014/06/nuage-contact.png');
		jQuery("#sumup-contact").slideDown(500);
		jQuery("#sumup-contact").animate(
			    { opacity: 1 },
			    { queue: false, duration: 'slow' }
				);
		indicatorContact=1;
		}
	}

	});	
			


	

});