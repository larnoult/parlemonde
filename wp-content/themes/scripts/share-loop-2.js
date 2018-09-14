jQuery(document).ready(function() {
	var whichClass = 1 ;
	var firstChild = jQuery(".jwsharethis-old");
	
    window.setInterval(function() {
        if (whichClass == 1)    {
			firstChild.css("font-family","amandine");
			firstChild.css("font-size","22px");
			whichClass = 0 ;
		}
		else if (whichClass == 2) {
			firstChild.css("font-family","littledays");	
			firstChild.css("font-size","28px");
			
			whichClass = 1 ;
			}
		else {
			firstChild.css("font-family","exception");
			firstChild.css("font-size","21px");
				
			whichClass = 2 ;			
		}
	
    },1500);

});