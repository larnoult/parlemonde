jQuery(document).ready(function(){
	
	jQuery("#comment-EH").click(function(){
		console.log("clickEH");

		jQuery( '#bp-nouveau-activity-form' ).addClass( 'hide' );
		jQuery( '#activites-de-publication-list' ).addClass( 'hide' );
		jQuery( '#comments' ).show();
		jQuery( '#comments' ).removeClass( 'hide' );
		jQuery( '#respond-container' ).show();
	
		jQuery( '#activites-de-publication-nav > ul > li:first-child' ).removeClass('current');
		jQuery( '#activites-de-publication-nav > ul > li:last-child' ).addClass('current');
		
		
		jQuery('html, body').animate({
		    scrollTop: (jQuery('#activites-de-publication-nav').offset().top - 100)
		},500);
	});
	
	
	jQuery("#question-GE").click(function(){
		jQuery( '#bp-nouveau-activity-form' ).removeClass( 'hide' );
		jQuery( '#activites-de-publication-list' ).removeClass( 'hide' );
		jQuery( '#comments' ).hide();
		jQuery( '#respond-container' ).hide();			
		
		jQuery( '#activites-de-publication-nav > ul > li:first-child' ).addClass('current');
		jQuery( '#activites-de-publication-nav > ul > li:last-child' ).removeClass('current');
		
		
		jQuery('html, body').animate({
		    scrollTop: (jQuery('#activites-de-publication-nav').offset().top - 100)
		},500);
	});
	
	
	// To deal with the character limit 

    
	
});


