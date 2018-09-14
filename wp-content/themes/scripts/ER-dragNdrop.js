// En ligne

jQuery(document).ready(function(){
	
	jQuery("#France").hover(function() {
		jQuery(this).attr("src","http://www.parlemonde.org/wp-content/uploads/2014/05/france-gif-ombre.gif");
			}, function() {
		jQuery(this).attr("src","http://www.parlemonde.org/wp-content/uploads/2014/05/france.png");
	});

	
	jQuery("#Salvador").hover(function() {
		jQuery(this).attr("src","http://www.parlemonde.org/wp-content/uploads/2014/05/salvador-gif-ombre.gif");
			}, function() {
		jQuery(this).attr("src","http://www.parlemonde.org/wp-content/uploads/2014/05/salvador.png");
	});
	
	
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
	  jQuery('#France, #Salvador').draggable({
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
	getPosition("#Salvador");

	function resetPosition(Pays) { // pour remettre le pays à sa place initiale
	    
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
	dropIt('#salvador-drop-in','#Salvador');


	
	
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
