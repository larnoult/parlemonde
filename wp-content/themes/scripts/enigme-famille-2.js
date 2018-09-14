jQuery(document).ready(function() {

	console.log("enigme");
	jQuery('#EF-indice1-img').hover(function() {
    jQuery('.EF-indice1').addClass('hover-EF-indice1');
  }, function() {
    jQuery('.EF-indice1').removeClass('hover-EF-indice1');
  })


	jQuery('#EF-indice2-img').hover(function() {
    jQuery('.EF-indice2').addClass('hover-EF-indice2');
  }, function() {
    jQuery('.EF-indice2').removeClass('hover-EF-indice2');
  })

	jQuery('#EF-indice3-img').hover(function() {
    jQuery('.EF-indice3').addClass('hover-EF-indice3');
  }, function() {
    jQuery('.EF-indice3').removeClass('hover-EF-indice3');
  })



 });