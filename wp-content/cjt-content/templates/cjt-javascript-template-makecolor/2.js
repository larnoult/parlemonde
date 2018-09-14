jQuery(document).ready(function() {

var colours = ["#000000", "#FF0000", "#990066", "#FF9966", "#996666", "#CC9933","#993366","#0000ff","#808000","#3366ff","#993300","#993366","#800000","#800080","#666699","#ff9900","#cc99ff","#ff9900","#ff0000","#ff00ff","#ff9900","#ff6600","#ff0000","#ff9900","#ff6600","#808000","#3366ff","#ff0000","#008000"], 
    idx;
    
    jQuery('h4').each(function(){
    //if statement here 
    // use jQuery(this) to reference the current div in the loop
    //you can try something like...
    var div = jQuery(this);
    var chars = jQuery(this).text().split('');
       div.html('');     
    for(var i=0; i<chars.length; i++) {
        idx = Math.floor(Math.random() * colours.length);
        var span = jQuery('<span>' + chars[i] + '</span>').css("color", colours[idx]);
        div.append(span);
    }
 });
    
});