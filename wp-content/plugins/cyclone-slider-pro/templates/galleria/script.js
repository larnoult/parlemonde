jQuery(document).ready(function(){
	jQuery('.cycloneslider-template-galleria').each(function(i,el){
		
		var main = jQuery(el),
			slideshow = main.children('.cycloneslider-slides'),
			carousel = main.find('.thumbnails-carousel'),
			controls = main.find('.cycloneslider-controls'),
			slide_titles = controls.data('titles').split(","),
			slide_descs = controls.data('descriptions').split(",");
		
		slideshow.on( 'cycle-initialized ', function( event, optionHash ) {
			controls.find('.cycloneslider-counter').html('1 / '+optionHash.slideCount);
			controls.find('.cycloneslider-caption-title').html(slide_titles[optionHash.currSlide]);
			controls.find('.cycloneslider-caption-description').html(slide_descs[optionHash.currSlide]);
		});
		
		slideshow.on( 'cycle-before', function( event, optionHash, outgoingSlideEl, incomingSlideEl, forwardFlag ) {
			controls.find('.cycloneslider-counter').html(optionHash.slideNum+' / '+optionHash.slideCount);
			controls.find('.cycloneslider-caption-title').html(slide_titles[optionHash.nextSlide]);
			controls.find('.cycloneslider-caption-description').html(slide_descs[optionHash.nextSlide]);
			carousel.find('.cycle-carousel-wrap').find('img').removeClass('current').eq(optionHash.nextSlide).addClass('current');
			

		});
		
		/*** Play Pause ***/
		controls.find('.cycloneslider-autoplay').click(function(){
			if(jQuery(this).hasClass('pause')){/*** pause icon showing, autoplay is on ***/
				jQuery(this).removeClass('pause');
				slideshow.cycle('pause');
			} else {
				jQuery(this).addClass('pause');
				slideshow.cycle('resume');
			}
		});
		
		/*** Toggle Thumbnails ***/
		controls.find('.cycloneslider-thumbs').click(function(){
			if(jQuery(this).hasClass('shown')){
				jQuery(this).removeClass('shown');
				main.find('.cycloneslider-thumbnails').animate({bottom:'-100px'},200);
			} else {
				jQuery(this).addClass('shown');
				main.find('.cycloneslider-thumbnails').animate({bottom:'30px'},200);
			}
		});
		
		slideshow.cycle();
		
		
		carousel.find('.cycle-slide').click(function(){
			var index = carousel.data('cycle.API').getSlideIndex(this);
			slideshow.cycle('goto', index);
		});
		
	});
});