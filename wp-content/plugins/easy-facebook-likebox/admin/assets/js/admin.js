(function ( $ ) {
	$(function () {
		
		if ( $( '#efbl_enabe_if_login' ).is(":checked") ) {
			$('#efbl_enabe_if_not_login').removeAttr("checked"); 
			$('#efbl_enabe_if_not_login').attr("disabled", true);
		}else if ( $( '#efbl_enabe_if_login' ).is(":checked") ) {
			$('#efbl_enabe_if_login').removeAttr("checked"); 
			$('#efbl_enabe_if_login').attr("disabled", true);
		}
		
		$('#efbl_enabe_if_login').click(function (){
		 
			 if ( $( this ).is(":checked")) {
					$('#efbl_enabe_if_not_login').removeAttr("checked"); 
					$('#efbl_enabe_if_not_login').attr("disabled", true);
					
 			  } else {
 				   $('#efbl_enabe_if_not_login').removeAttr("disabled"); 
 					
			}
			 
		});
		
		$('#efbl_enabe_if_not_login').click(function (){
		 
			 if ( $( this ).is(":checked")) {
					$('#efbl_enabe_if_login').removeAttr("checked"); 
					$('#efbl_enabe_if_login').attr("disabled", true);
					
 			  } else {
 				   $('#efbl_enabe_if_login').removeAttr("disabled"); 
 					
			}
			 
		});

		$('.efbl_del_trans').click(function (){
		 
			/*
			* Getting clicked option value.
			*/	
			var efbl_option = jQuery(this).data('efbl_trans');
			

			var data = { action : 'efbl_del_trans',
				efbl_option : efbl_option
				}
				

			jQuery.ajax({
			url : efbl.ajax_url,
			type : 'POST',
			dataType: 'json',
			data : data,
			success : function( response ) {
			
					if(response.success){
						jQuery('.form-table .'+response.data).slideUp('slow');
						
						
					}
					
			}

			});/* Ajax func ends here. */			

			 
		});	
		

	});

}(jQuery));	