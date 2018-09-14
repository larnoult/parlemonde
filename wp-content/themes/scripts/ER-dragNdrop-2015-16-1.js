// En ligne sur le site enfant

jQuery(document).ready(function(){
	

	function tilt(piece){
		
		function animatedCode() {

			if (jQuery(piece).getRotateAngle()<5){
				degree = +jQuery(piece).getRotateAngle() + 10 ;
				console.log("positif"+jQuery(piece).getRotateAngle()+degree);	
			}
			else{ 
				degree = +jQuery(piece).getRotateAngle() - 10 ;
				console.log("negatif  "+jQuery(piece).getRotateAngle()+"  "+degree);	
			}

			jQuery(piece).rotate(degree);

								}

		var interval = null;
		jQuery(piece).hover(function() {
		    interval = window.setInterval(function(){animatedCode()},120);
		}, function() {
		    window.clearInterval(interval);    
		});
		
	}
	tilt('#France');
	tilt('#Bolivie');
	tilt('#Madagascar');
	tilt('#Oman');
	tilt('#mystere-4');	
	tilt('#mystere-5');
	
	
/*		jQuery(".mini-puzzle").mousedown(function(){ // pour éviter d'avoir l'impression de pouvoir dragger les pièces du puzzle d'en bas 
	    return false;
	});
*/
		jQuery(".mini-puzzle").toggle(function(){
		   alert("Rendez-vous à la rentrée pour le début du voyage !");
				}, function() {

		});
		
	// Les pièces invalides
		  jQuery('.mini-puzzle').draggable({
			revert : "invalid",
			revertDuration: 500,
			drag: function( event, ui ) {
			   alert("Rendez-vous à la rentrée pour le début du voyage !");	
			}
		});
		
	// Les pièces de pays draggables 
	  jQuery('#France,#Bolivie, #Madagascar, #Oman, #mystere-4, #mystere-5').draggable({
		containment : jQuery('body'),// restreint Pélico dans la zone nuageuse
		revert : "invalid",
		revertDuration: 500
	});	
	// récupérer les positions initiales des pièces
	
	function getPosition (Pays){
		jQuery(Pays).data({
		    'originalLeft': jQuery(Pays).css('left'),
		    'origionalTop': jQuery(Pays).css('top')
		});
	}
	
	getPosition("#France");
	getPosition("#Bolivie");
	getPosition("#Madagascar");
	getPosition("#Oman");
	getPosition("#mystere-4");
	getPosition("#mystere-5");

	function resetPosition(Pays) { // pour remettre la pièce de puzzle-pays à leurs place initiale, si elle n'est pas glissée dans le bon 'trou'
		jQuery(Pays).animate({
	        'left': jQuery(Pays).data('originalLeft'),
	        'top': jQuery(Pays).data('origionalTop')
	    });
	}
	
	
	
	// Les trous dans lesquels les pièces sont droppables 
	jQuery('.inactive-piece').droppable({ 
	tolerance: "intersect" ,
	    drop : function(event, ui){
			var draggedPays = jQuery(ui.draggable).attr("pays");
			var toHighlight = jQuery(ui.draggable).attr("drop");
			var droppedPays = jQuery(this).attr("pays");	        
	        alert('Ici, c\'est '+droppedPays +'\nGlisse moi plutôt sur '+draggedPays); // 
			for (var i =1 ; i<4 ; i++ ){
				jQuery('#'+toHighlight).effect(	"highlight", {color:"rgba(126,101,232,0.2)"}, 200 );
			}
			resetPosition(jQuery(ui.draggable));
	    }
	});
		 
	// Faire une fonction :-) 
	function dropIt(PieceDroppable,PaysToDrop){ // 
		jQuery(PieceDroppable).droppable({
			tolerance: "intersect",
		    drop : function(event, ui){
				if ( '#'+jQuery(ui.draggable).attr("id") == PaysToDrop){
				    location.href = jQuery(PaysToDrop).attr("linkIt");	
				}
				else {
					var draggedPays = jQuery(ui.draggable).attr("pays");
					var toHighlight = jQuery(ui.draggable).attr("drop");
					var droppedPays = jQuery(this).attr("pays");	        
					alert('Ici, c\'est '+droppedPays +'\nGlisse moi plutôt sur '+draggedPays); // 
					for (var i =1 ; i<4 ; i++ ){
						jQuery('#'+toHighlight).effect(	"highlight", {color:"rgba(126,101,232,0.2)"}, 200 );
					}
					resetPosition(jQuery(ui.draggable));
				}
		    }
		});
		
	}
	dropIt('#france-drop-in','#France');
	dropIt('#bolivie-drop-in','#Bolivie');
	dropIt('#canada-drop-in','#Canada');
	dropIt('#birmanie-drop-in','#Birmanie');
	dropIt('#madagascar-drop-in','#Madagascar');
	dropIt('#oman-drop-in','#Oman');


	
	
	/*
	jQuery('#france-drop-in').droppable({ 
		tolerance: "touch",
		accept: '#France',
	    drop : function(){
			var draggableId = ui.draggable.attr("id");
	        alert('France'); // 
	    }
	});*/
		
		
	
});
