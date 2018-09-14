/* Allow lateral menu to shake, to show it's new */


(function(jQuery){
    jQuery.fn.shake = function(settings) {
        if(typeof settings.interval == 'undefined'){
            settings.interval = 100;
        }

        if(typeof settings.distance == 'undefined'){
            settings.distance = 10;
        }

        if(typeof settings.times == 'undefined'){
            settings.times = 4;
        }

        if(typeof settings.complete == 'undefined'){
            settings.complete = function(){};
        }

        jQuery(this).css('position','relative');

        for(var iter=0; iter<(settings.times+1); iter++){
            jQuery(this).animate({ left:((iter%2 == 0 ? settings.distance : settings.distance * -1)) }, settings.interval);
        }

        jQuery(this).animate({ left: 0}, settings.interval, settings.complete);  
    }; 
  
})(jQuery);
        console.log("home shake 1");
		
jQuery(document).ready(function(){
//	console.log("home shake 2");
        jQuery(".tdf").shake({
            interval: 400,
            distance: 10,
            times: 50
        });
    
});