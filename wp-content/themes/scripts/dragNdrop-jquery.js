jQuery(document).ready(function() {
    jQuery('.draggableObject').draggable();
    
    jQuery('.droppableRightArm').droppable({
        drop: function(event, ui) {
            var jQuerythis = jQuery(this); // reuse JQuery object.
            var droppedObject = ui.draggable.data('object'); // get object type
            // css reset
            jQuerythis.removeClass();
            jQuerythis.addClass('droppableRightArm');
            jQuerythis.addClass("rpRightArm" + droppedObject);
            //
            jQuerythis.html('You dropped ' + droppedObject + ' on the arm');
        }
    });
});