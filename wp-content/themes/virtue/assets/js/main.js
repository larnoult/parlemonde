/* Initialize
*/
var kt_isMobile = {
    Android: function() {
        return navigator.userAgent.match(/Android/i);
    },
    BlackBerry: function() {
        return navigator.userAgent.match(/BlackBerry/i);
    },
    iOS: function() {
        return navigator.userAgent.match(/iPhone|iPad|iPod/i);
    },
    Opera: function() {
        return navigator.userAgent.match(/Opera Mini/i);
    },
    Windows: function() {
        return navigator.userAgent.match(/IEMobile/i);
    },
    any: function() {
        return (kt_isMobile.Android() || kt_isMobile.BlackBerry() || kt_isMobile.iOS() || kt_isMobile.Opera() || kt_isMobile.Windows());
    }
};
(function($){
	'use strict';
	$.fn.kt_imagesLoaded = (function(){
		var kt_imageLoaded = function (img, cb, delay){
			var timer;
			var isReponsive = false;
			var $parent = $(img).parent();
			var $img = $('<img />');
			var srcset = $(img).attr('srcset');
			var sizes = $(img).attr('sizes') || '100vw';
			var src = $(img).attr('src');
			var onload = function(){
				$img.off('load error', onload);
				clearTimeout(timer);
				cb();
			};
			if(delay){
				timer = setTimeout(onload, delay);
			}
			$img.on('load error', onload);

			if($parent.is('picture')){
				$parent = $parent.clone();
				$parent.find('img').remove().end();
				$parent.append($img);
				isReponsive = true;
			}

			if(srcset){
				$img.attr('sizes', sizes);
				$img.attr('srcset', srcset);
				if(!isReponsive){
					$img.appendTo(document.createElement('div'));
				}
				isReponsive = true;
			} else if(src){
				$img.attr('src', src);
			}

			if(isReponsive && !window.HTMLPictureElement){
				if(window.respimage){
					window.respimage({elements: [$img[0]]});
				} else if(window.picturefill){
					window.picturefill({elements: [$img[0]]});
				} else if(src){
					$img.attr('src', src);
				}
			}
		};

		return function(cb){
			var i = 0;
			var $imgs = $('img', this).add(this.filter('img'));
			var ready = function(){
				i++;
				if(i >= $imgs.length){
					cb();
				}
			};
			if(!$imgs.length) {
				return cb();
			}
			$imgs.each(function(){
				kt_imageLoaded(this, ready);
			});
			return this;
		};
	})();
})(jQuery);
jQuery(document).ready(function ($) {
		$("[rel=tooltip]").tooltip();
		$('[data-toggle=tooltip]').tooltip();
		$("[rel=popover]").popover();
		//$('.collapse').collapse()
		$('#authorTab a').click(function (e) {e.preventDefault(); $(this).tab('show'); });
		$('.sc_tabs a').click(function (e) {e.preventDefault(); $(this).tab('show'); });
		
		$(".videofit").fitVids();
		$('.woocommerce-ordering .orderby').customSelect();
		$('.kad-select').customSelect();
		// Lightbox
			$.extend(true, $.magnificPopup.defaults, {
			tClose: '',
			image: {
				titleSrc: function(item) {
					return item.el.find('img').attr('alt');
					}
				}
		});
			$('.collapse-next').click(function (e) {
			//e.preventDefault();
		    var $target = $(this).siblings('.sf-dropdown-menu');
		     if($target.hasClass('in') ) {
		    	$target.collapse('toggle');
		    	$(this).removeClass('toggle-active');
		    } else {
		    	$target.collapse('toggle');
		    	$(this).addClass('toggle-active');
		    }
		});
		/**
		 * Checks href targets to see if a given anchor is linking to an image.
		 *
		 * @since  0.1.0
		 * @return mixed
		 */
		function kt_check_images( index, element ) {
			return /(png|jpg|jpeg|gif|tiff|bmp)$/.test(
				$( element ).attr( 'href' ).toLowerCase().split( '?' )[0].split( '#' )[0]
			);
		}

		function kt_find_images() {
			$( 'a[href]' ).filter( kt_check_images ).attr( 'data-rel', 'lightbox' );
		}
		kt_find_images();
		$("a[rel^='lightbox']").magnificPopup({type:'image'});
	    $("a[data-rel^='lightbox']").magnificPopup({type:'image'});
			$('.kad-light-gallery').each(function(){
				$(this).find('a[rel^="lightbox"]').magnificPopup({
					type: 'image',
					gallery: {
						enabled:true
						},
						image: {
							titleSrc: 'title'
						}
					});
			});
	    $('.kad-light-gallery').each(function(){
	      $(this).find("a[data-rel^='lightbox']").magnificPopup({
	        type: 'image',
	        gallery: {
	          enabled:true
	          },
	          image: {
	            titleSrc: 'title'
	          }
	        });
	    });
			$('.kad-light-wp-gallery').each(function(){
				$(this).find('a[rel^="lightbox"]').magnificPopup({
					type: 'image',
					gallery: {
						enabled:true
						},
						image: {
							titleSrc: function(item) {
							return item.el.find('img').attr('alt');
							}
						}
					});
			});
	    $('.kad-light-wp-gallery').each(function(){
	      $(this).find("a[data-rel^='lightbox']").magnificPopup({
	        type: 'image',
	        gallery: {
	          enabled:true
	          },
	          image: {
	            titleSrc: function(item) {
	            return item.el.find('img').attr('alt');
	            }
	          }
	        });
	    });
	    // Gutenberg Gallery
		$('.wp-block-gallery').each(function(){
			$(this).find('a[data-rel^="lightbox"]:not(".kt-no-lightbox")').magnificPopup({
				type: 'image',
				gallery: {
					enabled:true
				},
				image: {
					titleSrc: function(item) {
						if ( item.el.parents('.blocks-gallery-item').find('figcaption').length ) {
							return item.el.parents('.blocks-gallery-item').find('figcaption').html();
						} else {
							return item.el.find('img').attr('alt');
						}
					}
				},
			});
		});

		//Superfish Menu
		$('ul.sf-menu').superfish({
			delay:       200,                            // one second delay on mouseout
			animation:   {opacity:'show',height:'show'},  // fade-in and slide-down animation
			speed:       'fast'                          // faster animation speed
		});
		//init Flexslider
     	$('.kt-flexslider').each(function(){
		 	var flex_speed = $(this).data('flex-speed'),
			flex_animation = $(this).data('flex-animation'),
			flex_animation_speed = $(this).data('flex-anim-speed'),
			flex_auto = $(this).data('flex-auto');
		 	$(this).flexslider({
		 		animation:flex_animation,
				animationSpeed: flex_animation_speed,
				slideshow: flex_auto,
				slideshowSpeed: flex_speed,
				start: function ( slider ) {
					slider.removeClass( 'loading' );
				}
			});
	    });
	    //init Flexslider Thumb
     	$('.kt-flexslider-thumb').each(function(){
		 	var flex_speed = $(this).data('flex-speed'),
			flex_animation = $(this).data('flex-animation'),
			flex_animation_speed = $(this).data('flex-anim-speed'),
			flex_auto = $(this).data('flex-auto');
			$('#thumbnails').flexslider({
              	animation: "slide",
                controlNav: false,
                animationLoop: false,
                slideshow: false,       
                itemWidth: 180,
                itemMargin: 5,
                asNavFor: '#flex'
              });
              $('#flex').flexslider({
              animation: flex_animation,
              controlNav: false,
              animationLoop: false,
              animationSpeed: flex_animation_speed,
              slideshow: flex_auto,
              slideshowSpeed: flex_speed,
              sync: "#thumbnails",
              before: function(slider) {
                      slider.removeClass('loading');
                    }  
              });
	    });
		//init masonry
		$('.init-masonry').each(function(){
	    	var masonrycontainer = $(this),
	    	masonry_selector = $(this).data('masonry-selector');
		    if($('body.rtl').length){
				var iso_rtl = false;
			} else {
				var iso_rtl = true;
			}
	    	masonrycontainer.kt_imagesLoaded( function(){
				masonrycontainer.masonry({itemSelector: masonry_selector, isOriginLeft: iso_rtl});
			});
		});
		$('.kt-masonry-init').each(function(){
	    	var masonrycontainer = $(this),
	    	masonry_selector = $(this).data('masonry-selector');
	    	if($('body.rtl').length){
				var iso_rtl = false;
			} else {
				var iso_rtl = true;
			}
	    	masonrycontainer.kt_imagesLoaded( function(){
				masonrycontainer.masonry({itemSelector: masonry_selector, isOriginLeft: iso_rtl});
			});
		});
		/*
		*
		* Slick Slider
		*/
	     function kt_slick_slider_init(container) {
		 	var slider_speed = container.data('slider-speed'),
			slider_animation = container.data('slider-fade'),
			slider_animation_speed = container.data('slider-anim-speed'),
			slider_arrows = container.data('slider-arrows'),
			slider_auto = container.data('slider-auto'),
			slider_type = container.data('slider-type'),
			carousel_center_mode = container.data('slider-center-mode');
			var slick_rtl = false;
			if($('body.rtl').length >= 1){
				slick_rtl = true;
			} 
			container.on('init', function(event, slick){
				container.removeClass('loading');
			});
			if(slider_type == 'carousel') {
				var sliders_show = container.data('slides-to-show');
				if(sliders_show == null) {sliders_show = 1;}
				container.slick({
					slidesToScroll: 1,
					slidesToShow: sliders_show,
					centerMode: carousel_center_mode,
					variableWidth: true,
					arrows: slider_arrows,
					speed: slider_animation_speed,
					autoplay: slider_auto,
					autoplaySpeed: slider_speed,
					fade: slider_animation,
					pauseOnHover:false,
					rtl:slick_rtl, 
					dots: true,
				});
				
			}else if(slider_type == 'content-carousel') {
				container.on('init', function(event, slick) {
					container.closest('.fadein-carousel').animate({'opacity' : 1});
				});
				var xxl = container.data('slider-xxl'),
					xl = container.data('slider-xl'),
					md = container.data('slider-md'),
					sm = container.data('slider-sm'),
					xs = container.data('slider-xs'),
					ss = container.data('slider-ss'),
					scroll = container.data('slider-scroll');
					if(scroll !== 1){
						var scroll_xxl = xxl,
							scroll_xl  = xl,
							scroll_md  = md,
							scroll_sm  = sm,
							scroll_xs  = xs,
							scroll_ss  = ss;
					} else {
						var scroll_xxl = 1,
							scroll_xl  = 1,
							scroll_md  = 1,
							scroll_sm  = 1,
							scroll_xs  = 1,
							scroll_ss  = 1;
					}
				container.slick({
					slidesToScroll: scroll_xxl,
					slidesToShow: xxl,
					arrows: slider_arrows,
					speed: slider_animation_speed,
					autoplay: slider_auto,
					autoplaySpeed: slider_speed,
					fade: slider_animation,
					pauseOnHover:false,
					dots: false,
					rtl:slick_rtl, 
					responsive: [
							    {
							      breakpoint: 1499,
							      settings: {
							        slidesToShow: xl,
							        slidesToScroll: scroll_xl,
							      }
							    },
							    {
							      breakpoint: 1199,
							      settings: {
							        slidesToShow: md,
							        slidesToScroll: scroll_md,
							      }
							    },
							    {
							      breakpoint: 991,
							      settings: {
							        slidesToShow: sm,
							        slidesToScroll: scroll_sm,
							      }
							    },
							    {
							      breakpoint: 767,
							      settings: {
							        slidesToShow: xs,
							        slidesToScroll: scroll_xs,
							      }
							    },
							    {
							      breakpoint: 543,
							      settings: {
							        slidesToShow: ss,
							        slidesToScroll: scroll_ss,
							      }
							    }
							  ]
				});
				container.on('beforeChange', function(event, slick, currentSlide, nextSlide){
					container.find('.kt-slickslider:not(.slick-initialized)').each(function(){
						kt_slick_slider_init($(this));
					});
				});

			} else if(slider_type == 'thumb') {
				var thumbid = container.data('slider-thumbid'),
					thumbsshowing = container.data('slider-thumbs-showing'),
					sliderid = container.attr('id');
				container.slick({
					slidesToScroll: 1,
					slidesToShow: 1,
					arrows: slider_arrows,
					speed: slider_animation_speed,
					autoplay: slider_auto,
					autoplaySpeed: slider_speed,
					fade: slider_animation,
					pauseOnHover:false,
					adaptiveHeight: true,
					dots: false,
					rtl:slick_rtl, 
					asNavFor: thumbid,
				});
				$(thumbid).slick({
				  	slidesToShow:thumbsshowing,
				  	slidesToScroll: 1,
				  	asNavFor: '#'+sliderid,
				  	dots: false,
				  	rtl:slick_rtl, 
				  	centerMode: false,
				  	focusOnSelect: true
				});
			} else {
			 	container.slick({
			 		slidesToShow: 1,
					slidesToScroll: 1,
					arrows: slider_arrows,
					speed: slider_animation_speed,
					autoplay: slider_auto,
					autoplaySpeed: slider_speed,
					fade: slider_animation,
					pauseOnHover:false,
					rtl:slick_rtl, 
					adaptiveHeight: true,
					dots: true,
				});
			 }
	    }
	    $('.kt-slickslider').each(function(){
	    	var container = $(this);
	    	var slider_initdelay = container.data('slider-initdelay');
	    	if(slider_initdelay == null || slider_initdelay == '0') {
	     	 	kt_slick_slider_init(container);
	    	} else {
	    		setTimeout(function() {
	    			kt_slick_slider_init(container);
	    		}, slider_initdelay);
	    	}
	    });
	    $('html').removeClass('no-js');
    	$('html').addClass('js-running');
});
if( kt_isMobile.any() ) {
	jQuery(document).ready(function ($) {
		matchMedia('only screen and (max-width: 480px)').addListener(function(list){
			$('select.hasCustomSelect').removeAttr("style");
			$('select.hasCustomSelect').css({'width':'250px'});
	    	$('.kad-select.customSelect').remove();
	    	$('select.kad-select').customSelect();
	    	$('.customSelectInner').css('width','100%');
		});
	});
}
