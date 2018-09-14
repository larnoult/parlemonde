jQuery(document).ready(function(){


jQuery.preload('http://www.parlemonde.org/wp-content/uploads/2014/05/1-bulle1-gif-violet2.gif',
					'http://www.parlemonde.org/wp-content/uploads/2014/05/1-bulle1-gif-marron2.gif',
					'http://www.parlemonde.org/wp-content/uploads/2014/05/1-bulle1-gif-vert2.gif',
					'http://www.parlemonde.org/wp-content/uploads/2014/05/1-bulle1-gif-bleu3.gif',
					'http://www.parlemonde.org/wp-content/uploads/2014/05/1-bulle1-gif-orange.gif',
					'http://www.parlemonde.org/wp-content/uploads/2014/05/1-bulle1-gif-turquoise.gif',
					'http://www.parlemonde.org/wp-content/uploads/2014/05/1-bulle1-gif-violet.gif',
					'http://www.parlemonde.org/wp-content/uploads/2014/05/bulle1-gif-marron.gif',
					'http://www.parlemonde.org/wp-content/uploads/2014/05/bulle1-gif-bleu2.gif',
					'http://www.parlemonde.org/wp-content/uploads/2014/05/bulle1-gif-jaune.gif',
					'http://www.parlemonde.org/wp-content/uploads/2014/05/bulle1-gif-vertB.gif',
					'http://www.parlemonde.org/wp-content/uploads/2014/05/bulle1-gif-bleu-T.gif',
					'http://www.parlemonde.org/wp-content/uploads/2014/05/bulle1-gif-rouge.gif'
);

	
	jQuery("#instituteur").hover(function() {
		jQuery("#instituteur").css("background-image","url('http://www.parlemonde.org/wp-content/uploads/2014/05/bulle1-gif-rouge.gif')"); // image gif
		jQuery("#instituteur").css( "background-size", "cover" );
		
			}, function() {
				jQuery("#instituteur").css("background-image","url('http://www.parlemonde.org/wp-content/uploads/2014/05/bulle1-gif-rouge.png')"); // image fixe
				jQuery("#instituteur").css( "background-size", "cover" );
				

	});

	jQuery("#benevole").hover(function() {
		jQuery("#benevole").css("background-image","url('http://www.parlemonde.org/wp-content/uploads/2014/05/bulle1-gif-bleu-T.gif')"); // image gif
			}, function() {
				jQuery("#benevole").css("background-image","url('http://www.parlemonde.org/wp-content/uploads/2014/05/bulle1-gif-bleu-T.png')"); // image fixe
	});

	jQuery("#graphiste").hover(function() {
		jQuery("#graphiste").css("background-image","url('http://www.parlemonde.org/wp-content/uploads/2014/05/bulle1-gif-vertB.gif')"); // image gif
			}, function() {
				jQuery("#graphiste").css("background-image","url('http://www.parlemonde.org/wp-content/uploads/2014/05/bulle1-gif-vert-T.png')"); // image fixe
	});

	jQuery("#programmateur").hover(function() {
		jQuery("#programmateur").css("background-image","url('http://www.parlemonde.org/wp-content/uploads/2014/05/bulle1-gif-jaune.gif')"); // image gif
			}, function() {
				jQuery("#programmateur").css("background-image","url('http://www.parlemonde.org/wp-content/uploads/2014/05/bulle1-gif-jaune.png')"); // image fixe
	});
	
	jQuery("#melomane").hover(function() {
		jQuery("#melomane").css("background-image","url('http://www.parlemonde.org/wp-content/uploads/2014/05/bulle1-gif-bleu2.gif')"); // image gif
			}, function() {
				jQuery("#melomane").css("background-image","url('http://www.parlemonde.org/wp-content/uploads/2014/05/bulle1-gif-bleu2.png')"); // image fixe
	});
	
	jQuery("#pedopsy").hover(function() {
		jQuery("#pedopsy").css("background-image","url('http://www.parlemonde.org/wp-content/uploads/2014/05/bulle1-gif-marron.gif')"); // image gif
			}, function() {
				jQuery("#pedopsy").css("background-image","url('http://www.parlemonde.org/wp-content/uploads/2014/05/bulle1-gif-marron.png')"); // image fixe
	});
	
	jQuery("#traducteur").hover(function() {
		jQuery("#traducteur").css("background-image","url('http://www.parlemonde.org/wp-content/uploads/2014/05/1-bulle1-gif-violet.gif')"); // image gif
			}, function() {
				jQuery("#traducteur").css("background-image","url('http://www.parlemonde.org/wp-content/uploads/2014/05/1-bulle1-gif-violet.png')"); // image fixe
	});

	jQuery("#interprete").hover(function() {
		jQuery("#interprete").css("background-image","url('http://www.parlemonde.org/wp-content/uploads/2014/05/1-bulle1-gif-turquoise.gif')"); // image gif
			}, function() {
				jQuery("#interprete").css("background-image","url('http://www.parlemonde.org/wp-content/uploads/2014/05/1-bulle1-gif-turquoise.png')"); // image fixe
	});
	
	jQuery("#contact").hover(function() {
		jQuery("#contact").css("background-image","url('http://www.parlemonde.org/wp-content/uploads/2014/05/1-bulle1-gif-orange.gif')"); // image gif
			}, function() {
				jQuery("#contact").css("background-image","url('http://www.parlemonde.org/wp-content/uploads/2014/05/1-bulle1-gif-orange.png')"); // image fixe
	});
	
	
	jQuery("#monteur").hover(function() {
		jQuery("#monteur").css("background-image","url('http://www.parlemonde.org/wp-content/uploads/2014/05/1-bulle1-gif-bleu3.gif')"); // image gif
			}, function() {
				jQuery("#monteur").css("background-image","url('http://www.parlemonde.org/wp-content/uploads/2014/05/1-bulle1-gif-bleu3.png')"); // image fixe
	});
	
	jQuery("#diffuseur").hover(function() {
		jQuery("#diffuseur").css("background-image","url('http://www.parlemonde.org/wp-content/uploads/2014/05/1-bulle1-gif-vert2.gif')"); // image gif
			}, function() {
				jQuery("#diffuseur").css("background-image","url('http://www.parlemonde.org/wp-content/uploads/2014/05/1-bulle1-gif-vert2.png')"); // image fixe
	});
	
	jQuery("#hebergement").hover(function() {
		jQuery("#hebergement").css("background-image","url('http://www.parlemonde.org/wp-content/uploads/2014/05/1-bulle1-gif-marron2.gif')"); // image gif
			}, function() {
				jQuery("#hebergement").css("background-image","url('http://www.parlemonde.org/wp-content/uploads/2014/05/1-bulle1-gif-marron2.png')"); // image fixe
	});
	
	jQuery("#media").hover(function() {
		jQuery("#media").css("background-image","url('http://www.parlemonde.org/wp-content/uploads/2014/05/1-bulle1-gif-violet2.gif')"); // image gif
			}, function() {
				jQuery("#media").css("background-image","url('http://www.parlemonde.org/wp-content/uploads/2014/05/1-bulle1-gif-violet2.png')"); // image fixe
	});	




});


