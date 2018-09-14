// en ligne

jQuery(document).ready(function() {

	var menu1 = jQuery('.menu-le-projet > a');
	jQuery(menu1).replaceText( "Le projet", "<span id=\"menu1\">Le&nbsp;projet</span>" );

	var menu2 = jQuery('.menu-le-cap-pedagogique > a');
	jQuery(menu2).replaceText( "Le cap pédagogique", "<span id=\"menu2\">Le&nbsp;cap&nbsp;pédagogique</span>" );

	var menu3 = jQuery('.menu-en-route > a');
	jQuery(menu3).replaceText( "En route !", "<span id=\"menu3\">En&nbsp;Route&nbsp;!</span>" );

	var menu4 = jQuery('.menu-agir-avec-nous > a');
	jQuery(menu4).replaceText( "Agir avec nous", "<span id=\"menu4\">Agir&nbsp;avec&nbsp;nous</span>" );

	tiltmenu(jQuery("#menu1"),jQuery(".menu-le-projet"));
	tiltmenu(jQuery("#menu2"),jQuery(".menu-le-cap-pedagogique"));
	tiltmenu(jQuery("#menu3"),jQuery(".menu-en-route"));
	tiltmenu(jQuery("#menu4"),jQuery(".menu-agir-avec-nous"));



function tiltmenu (menu, sousmenu){ // la fonction qui tilt chaque menu individuellement

	var interval = null;
	jQuery(menu).lettering();

	function animatedCode() {
	console.log("anime");
		var degree ;
	   var signage = [1,-1]
	  jQuery(menu).find('span').each(function(){
//	  jQuery(this).css("color", colours[idx]);
		if( jQuery(this).getRotateAngle() == 0 )  {  // si l'angle de la rotation est égale à zéro, on tire au hasard
	   degree = Math.round( Math.random() * 10 ) * signage[ Math.round(Math.random() * 1) ];
		} 
		else {  // sinon, on extrait le signe de l'angle et on assigne le signe opposé à l'angle suivant
			if (jQuery(this).getRotateAngle()>0){
				degree = +jQuery(this).getRotateAngle() + Math.random()*(-10) ;
				console.log("positif"+jQuery(this).getRotateAngle()+degree);
				
			}
			else{ degree = +jQuery(this).getRotateAngle() + Math.random()*(10) ;
				console.log("negatif  "+jQuery(this).getRotateAngle()+"  "+degree);
				
				}
		}		
		console.log("angle initial "+jQuery(this).getRotateAngle()+ " nouvel angle" + degree);
		jQuery(this).rotate(degree);
	       }) 

	}

	jQuery(sousmenu).hover(function() {
	    interval = window.setInterval(function(){animatedCode()},120);
	}, function() {
	    window.clearInterval(interval);    
	});
		
}

});
//http://jsfiddle.net/q46j5/