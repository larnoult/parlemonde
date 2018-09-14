// En ligne sur le site enfant

jQuery(document).ready(function(){
	
	
	jQuery(".notDraggable").mousedown(function(){
	    return false;
	});

	function tilt(piece){
		
		function animatedCode() {

			if (jQuery(piece).getRotateAngle()<5){
				degree = +jQuery(piece).getRotateAngle() + 10 ;
//				console.log("positif"+jQuery(piece).getRotateAngle()+degree);	
			}
			else{ 
				degree = +jQuery(piece).getRotateAngle() - 10 ;
//				console.log("negatif  "+jQuery(piece).getRotateAngle()+"  "+degree);	
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
	tilt('#enfant-1');
	tilt('#enfant-2');
	tilt('#enfant-3');
	tilt('#enfant-4');
	tilt('#enfant-5');	
	tilt('#enfant-6');
	tilt('#enfant-7');
	tilt('#enfant-8');
	tilt('#enfant-9');
	
	
/*		jQuery(".mini-puzzle").mousedown(function(){ // pour Ã©viter d'avoir l'impression de pouvoir dragger les piÃ¨ces du puzzle d'en bas 
	    return false;
	});
*/
		jQuery(".mini-puzzle").toggle(function(){
		   alert("Rendez-vous Ã  la rentrÃ©e pour le dÃ©but du voyage !");
				}, function() {

		});
		
	// Les piÃ¨ces invalides
		  jQuery('.mini-puzzle').draggable({
			revert : "invalid",
			revertDuration: 500,
			drag: function( event, ui ) {
			   alert("Rendez-vous Ã  la rentrÃ©e pour le dÃ©but du voyage !");	
			}
		});
		
	// Les noms sont tous déplacables. 
	  jQuery('.noms').draggable({
		containment : jQuery('body'),// dans la zone de texte
		revert : "invalid",
		revertDuration: 500
	});	
	
	// rÃ©cupÃ©rer les positions initiales des noms
	
	function getPosition (noms){
		jQuery(noms).data({
		    'originalLeft': jQuery(noms).css('left'),
		    'origionalTop': jQuery(noms).css('top')
		});
	}
	
	getPosition("#enfant-1");
	getPosition("#enfant-2");
	getPosition("#enfant-3");
	getPosition("#enfant-4");
	getPosition("#enfant-5");
	getPosition("#enfant-6");
	getPosition("#enfant-7");
	getPosition("#enfant-8");
	getPosition("#enfant-9");

	function resetPosition(noms) { // pour remettre la piÃ¨ce de puzzle-pays Ã  leurs place initiale, si elle n'est pas glissÃ©e dans le bon 'trou'
		jQuery(noms).animate({
	        'left': jQuery(noms).data('originalLeft'),
	        'top': jQuery(noms).data('origionalTop')
	    });
	}
	
	// Faire une fonction :-) 
	function dropIt(EnfantDroppable,NameToDrop){ // 
		jQuery(EnfantDroppable).droppable({
			tolerance: "intersect",
		    drop : function(event, ui){
				if ( '#'+jQuery(ui.draggable).attr("id") == NameToDrop){ // si le nom écrit correspond à l'enfant 
					// change le visage de l'enfant vers l'enfant qui sourrit.  -> utiliser cut avec "-" data.split('/');
					var source=jQuery(EnfantDroppable).attr("src").split('.png')[0];
					console.log(source);
					console.log(source.split('-')[0]);
					console.log(source.split('-')[1]);
					var newSource=source.split('-')[0]+"-"+source.split('-')[1]+"-"+source.split('-')[2]+'-success.png';
				//	var newSource=source.substr(0, source.length-4)+'-success.png'; 
					jQuery(EnfantDroppable).attr("src",newSource);
					// Ramène le nom écrit à sa place, et l'empêche de bouger en changeant sa classe.
					resetPosition(jQuery(ui.draggable));
		//				jQuery(ui.draggable).addClass("notDraggable");
					// rendre quasi transparent le nom écrit 
				    jQuery(NameToDrop).css('opacity', '0.2');
					jQuery(NameToDrop).css('border','2px solid rgba(144,177,48,1)');
					// O yes
					jQuery("#yes-ouganda")[0].play();
					console.log(jQuery("#yes-ouganda"));
					
					// Colore en vert le fond pour dire que c'est bon
					jQuery(EnfantDroppable).css('background-color','rgba(144,177,48,0.5)');
					for (var i =1 ; i<4 ; i++ ){
						jQuery(EnfantDroppable).effect(	"highlight", {color:"rgba(144,177,48,0.2)"}, 200 );
					}
					// affiche en légende le nom de l'enfant sur l'image
					jQuery(EnfantDroppable).next().css('opacity', '1');
					// empêche de redropper un nom sur la face -> si tu fais ça l'enfant n'est plus droppable du tout :(
		//			jQuery(EnfantDroppable).removeAttr('id');
					
		/*			jQuery(EnfantDroppable).captionjs({
					//	'mode':'stacked'
					});*/
				}
				else {
//					console.log(jQuery(ui.draggable));
					alert('Non je ne suis pas '+jQuery(ui.draggable).attr("alt")+' !');
					resetPosition(jQuery(ui.draggable));
					// O no
					jQuery("#no-ouganda")[0].play();
					
					//Colore en rouge le fond pour dire que c'est bon
					
					jQuery(EnfantDroppable).css('background-color','rgba(206,73,53,0.5)');
					for (var i =1 ; i<4 ; i++ ){
						jQuery(EnfantDroppable).effect(	"highlight", {color:"rgba(206,73,53,0.2)"}, 200 );
					}
					
					// change le visage de l'enfant en enfant qui est triste
					var source=jQuery(EnfantDroppable).attr("src").split('.png')[0];
					var newSource=source.split('-')[0]+"-"+source.split('-')[1]+"-"+source.split('-')[2]+'-failure.png';
					console.log(newSource);
					jQuery(EnfantDroppable).attr("src",newSource);
					
			/*		var draggedName = jQuery(ui.draggable).attr("name");
					var toHighlight = jQuery(ui.draggable).attr("drop");
					var droppedPays = jQuery(this).attr("pays");	        
					alert('Ici, c\'est '+droppedPays +'\nGlisse moi plutÃ´t sur '+draggedPays); // 
					for (var i =1 ; i<4 ; i++ ){
						jQuery('#'+toHighlight).effect(	"highlight", {color:"rgba(126,101,232,0.2)"}, 200 );
					}
					resetPosition(jQuery(ui.draggable));*/
				}
		    }
		});
	}

	
	

		 

	dropIt('#enfant-1-in','#enfant-1'); 
	dropIt('#enfant-2-in','#enfant-2'); 
	dropIt('#enfant-3-in','#enfant-3'); 
	dropIt('#enfant-4-in','#enfant-4'); 
	dropIt('#enfant-5-in','#enfant-5'); 
	dropIt('#enfant-6-in','#enfant-6'); 
	dropIt('#enfant-7-in','#enfant-7'); 
	dropIt('#enfant-8-in','#enfant-8'); 
	dropIt('#enfant-9-in','#enfant-9');


		
		
	
});