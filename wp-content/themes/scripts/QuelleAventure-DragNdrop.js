	// version en ligne
jQuery(document).ready(function() {
	
	// Preload toutes les images fixes
	
/*	jQuery.preload("http://www.parlemonde.fr/wp-content/uploads/2015/04/nuage-comment.png",
					"http://www.parlemonde.fr/wp-content/uploads/2015/04/nuage-quoi.png",
					"http://www.parlemonde.fr/wp-content/uploads/2015/04/nuage-qui.png",
					"http://www.parlemonde.fr/wp-content/uploads/2015/04/nuage-quand.png",
					"http://www.parlemonde.fr/wp-content/uploads/2015/04/nuage-ou.png",
					"http://www.parlemonde.fr/wp-content/uploads/2015/04/pelico.gif"
	);*/
	
	jQuery("#home-page-container > div > div").css("visibility","visible"); // rÃ©vÃ¨le toutes les descriptions d'images
	
	// pour savoir si PÃ©lico est dÃ©jÃ  passÃ© sur les nuages
	var indicatorCap=0;
	var indicatorQuoi=0;
	var indicatorRoute=0;
	var indicatorContact=0;
	var indicatorAgir=0;

//	jQuery("#home-page-container div img").fadeTo(0,0.4); // masque un partie toutes les images
	jQuery("#home-page-container > div > div").hide(); // cache toutes les descriptions des nuages
	jQuery("#home-page-container div img").mousedown(function(){ // pour Ã©viter d'avoir l'impression de pouvoir dragger les nuages 
	    return false;
	});
	
	jQuery("#dragPelico").hover(function() {
		jQuery(this).attr("src","http://www.parlemonde.fr/wp-content/uploads/2015/04/pelico.gif");
			}, function() {
		jQuery(this).attr("src","http://www.parlemonde.fr/wp-content/uploads/2015/04/pelico-repos.png");
	});
	
	
        jQuery('#dragPelico').draggable({
	containment : jQuery('body'),// restreint PÃ©lico dans la zone nuageuse
 revert : "invalid",
revertDuration: 1000 
	});
	
// DÃ©finition des Ã©venements drop // 	
	function OnPelicoDrop(nuage){
		
	}
	
	
	 jQuery('#nuage-quoi').droppable({
	    drop : function(){

		jQuery('#dragPelico').position({ //  Pour ramener PÃ©lico sur le point d'interrogation
				"my": "center center",       
				"at": "center-4 center-41" ,  
				"of": jQuery('#nuage-quoi .question'),	
		    using: function( position ) { // pour que le repositionnement de PÃ©lico sur le point d'interrogation soit soft
		        jQuery( this ).animate(position, function() { // et qu'il s'arrÃªte de voler aprÃ¨s Ãªtre arrivÃ© :-)
					jQuery("#dragPelico").attr("src","http://www.parlemonde.fr/wp-content/uploads/2015/04/pelico-repos.png");
				}
			);
		    }
		});
		
		if (indicatorQuoi==0) { // si seulement c'est la premiÃ¨re fois qu'on le pose dessus
		jQuery("#nuage-quoi img").attr('src','http://www.parlemonde.fr/wp-content/uploads/2015/04/nuage-quoi.png');
		jQuery("#sumup-quoi").slideDown(500);
		jQuery("#sumup-quoi").animate(
			    { opacity: 1 },
			    { queue: false, duration: 'slow' }
				);
		indicatorQuoi=1;
		}
	}
	
	
	});	
	

	 jQuery('#nuage-qui').droppable({
	    drop : function(){
		jQuery('#dragPelico').position({ //  Pour ramener PÃ©lico sur le point d'interrogation
				"my": "center center",       
				"at": "center-4 center-41" ,  
				"of": jQuery('#nuage-qui .question'),	
		    using: function( position ) { // pour que le repositionnement de PÃ©lico sur le point d'interrogation soit soft
		        jQuery( this ).animate(position, function() { // et qu'il s'arrÃªte de voler aprÃ¨s Ãªtre arrivÃ© :-)
					jQuery("#dragPelico").attr("src","http://www.parlemonde.fr/wp-content/uploads/2015/04/pelico-repos.png");
				}
			);
		    }
		});
		
		if (indicatorCap==0) { // si seulement c'est la premiÃ¨re fois qu'on le pose dessus
		jQuery("#nuage-qui img").attr('src','http://www.parlemonde.fr/wp-content/uploads/2015/04/nuage-qui.png');
		jQuery("#sumup-qui").slideDown(500);
		jQuery("#sumup-qui").animate(
			    { opacity: 1 },
			    { queue: false, duration: 'slow' }
				);
		indicatorCap=1;
		}
	}
	
	});			
		



	
	 jQuery('#nuage-quand').droppable({
	    drop : function(){

		jQuery('#dragPelico').position({ //  Pour ramener PÃ©lico sur le point d'interrogation
				"my": "center center",       
				"at": "center-4 center-41" ,  
				"of": jQuery('#nuage-quand .question'),	
		    using: function( position ) { // pour que le repositionnement de PÃ©lico sur le point d'interrogation soit soft
		        jQuery( this ).animate(position, function() { // et qu'il s'arrÃªte de voler aprÃ¨s Ãªtre arrivÃ© :-)
					jQuery("#dragPelico").attr("src","http://www.parlemonde.fr/wp-content/uploads/2015/04/pelico-repos.png");
				}
			);
		    }
		});
		
		if (indicatorRoute==0) { // si seulement c'est la premiÃ¨re fois qu'on le pose dessus
		jQuery("#nuage-quand img").attr('src','http://www.parlemonde.fr/wp-content/uploads/2015/04/nuage-quand.png');
		jQuery("#sumup-quand").slideDown(500);
		jQuery("#sumup-quand").animate(
			    { opacity: 1 },
			    { queue: false, duration: 'slow' }
				);
		indicatorRoute=1;
		}
	}

	});			
		



	 jQuery('#nuage-ou').droppable({
	    drop : function(){

		jQuery('#dragPelico').position({ //  Pour ramener PÃ©lico sur le point d'interrogation
				"my": "center center",       
				"at": "center-4 center-41" ,  
				"of": jQuery('#nuage-ou .question'),	
		    using: function( position ) { // pour que le repositionnement de PÃ©lico sur le point d'interrogation soit soft
		        jQuery( this ).animate(position, function() { // et qu'il s'arrÃªte de voler aprÃ¨s Ãªtre arrivÃ© :-)
					jQuery("#dragPelico").attr("src","http://www.parlemonde.fr/wp-content/uploads/2015/04/pelico-repos.png");
				}
			);
		    }
		});

		if (indicatorAgir==0) { // si seulement c'est la premiÃ¨re fois qu'on le pose dessus
		jQuery("#nuage-ou img").attr('src','http://www.parlemonde.fr/wp-content/uploads/2015/04/nuage-ou.png');
		jQuery("#sumup-ou").slideDown(500);
		jQuery("#sumup-ou").animate(
			    { opacity: 1 },
			    { queue: false, duration: 'slow' }
				);
		indicatorAgir=1;
		}
	}

	});			
		
			
	
	
	 jQuery('#nuage-comment').droppable({
	    drop : function(){

		jQuery('#dragPelico').position({ //  Pour ramener PÃ©lico sur le point d'interrogation
				"my": "center center",       
				"at": "center-4 center-41" ,  
				"of": jQuery('#nuage-comment .question'),	
		    using: function( position ) { // pour que le repositionnement de PÃ©lico sur le point d'interrogation soit soft
		        jQuery( this ).animate(position, function() { // et qu'il s'arrÃªte de voler aprÃ¨s Ãªtre arrivÃ© :-)
					jQuery("#dragPelico").attr("src","http://www.parlemonde.fr/wp-content/uploads/2015/04/pelico-repos.png");
				}
			);
		    }
		});
		

		if (indicatorContact==0) { // si seulement c'est la premiÃ¨re fois qu'on le pose dessus
		jQuery("#nuage-comment img").attr('src','http://www.parlemonde.fr/wp-content/uploads/2015/04/nuage-comment.png');
		jQuery("#sumup-comment").slideDown(500);
		jQuery("#sumup-comment").animate(
			    { opacity: 1 },
			    { queue: false, duration: 'slow' }
				);
		indicatorContact=1;
		}
	}

	});	
			


	

});