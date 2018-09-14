//<script type="text/javascript" src="http://jqueryrotate.googlecode.com/svn/trunk/jQueryRotate.js"></script> 

function leafmealone() {
         var colours = ["#000000", "#FF0000", "#990066", "#FF9966", "#996666", "#CC9933","#993366","#0000ff","#808000","#3366ff","#993300","#993366","#800000","#800080","#666699","#ff9900","#cc99ff","#ff9900","#ff0000","#ff00ff","#ff9900","#ff6600","#ff0000","#ff9900","#ff6600","#808000","#3366ff","#ff0000","#008000"], 
    idx;
    
    jQuery('.leafmealonepoint').each(function(){
    //if statement here 
    // use jQuery(this) to reference the current div in the loop
    //you can try something like...
    var div = jQuery(this);
    var chars = jQuery(this).text().split('');
       div.html('');     
    for(var i=0; i<chars.length; i++) {
        idx = Math.floor(Math.random() * colours.length);
        var span = jQuery('<span class="rotate" id="unique' + i+ '">' + chars[i] + '</span>');
        div.append(span);
    }
 });
    
            jQuery('#leafmealonepoint').stop().rotate({ 
                angle:0, 
                animateTo: 2, 
                duration:300, callback: function() 
                {
                jQuery('#leafmealonepoint').stop().rotate({ 
                    animateTo: -2, duration:300});
                }
            });
    }

jQuery(function() {

    
jQuery('#leafmealonepoint').hover(function(){
    leafmealone();
    hoverInterval = setInterval(leafmealone, 600);
}, function(){
    clearInterval(hoverInterval);
});
})

//http://jsfiddle.net/YKj5D/1968/
//http://jsfiddle.net/8msde/
/* from https://code.google.com/p/jqueryrotate/wiki/Examples
var angle = 0;
setInterval(function(){
      angle+=3;
     $("#img").rotate(angle);
},50);*/
