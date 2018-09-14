(function( $ ) {
    $(function() {
         
        // Add Color Picker to all inputs that have 'color-field' class
        $( '.cpa-color-picker' ).wpColorPicker();
         
    });
    
	 $(document).ready(function() {
    	if($('#ud_display_listings').val() == 'horizontally') {    	 	
    		$("#ud-horizontal-settings-1").show();  
			$("#ud-horizontal-settings-2").show();  	
			$("#ud-horizontal-settings-3").show();  
			$("#ud-horizontal-settings-4").show(); 
			$("#horizontal-width-settings-1").show(); 
			$("#horizontal-width-settings-2").show(); 
			$("#horizontal-width-settings-3").show(); 
			$("#horizontal-width-settings-4").show(); 
			$("#ud_avatar_size_and_padding").show();
			$("#avatar_padding").hide();
			
			
			$("#user_directory_border option[value='surrounding_border']").remove();
			
		}
    	else
    	{
    		$("#ud-horizontal-settings-1").hide();
			$("#ud-horizontal-settings-2").hide();
			$("#ud-horizontal-settings-3").hide();  
			$("#ud-horizontal-settings-4").hide(); 
			$("#horizontal-width-settings-1").hide(); 	
            $("#horizontal-width-settings-2").hide();
			$("#horizontal-width-settings-3").hide();
			$("#horizontal-width-settings-4").hide(); 
			$("#ud_avatar_size_and_padding").show();
			$("#avatar_padding").show();
			
			var optionExists = ($("#user_directory_border option[value='surrounding_border']").length > 0);

			if(!optionExists)
			{
				$('#user_directory_border').append("<option value='surrounding_border'>Surrounding Border</option>");
			}

    	} 
    }); 	
    
    $(function() {
   	$('#ud_display_listings').change(function() {
       	if($('#ud_display_listings').val() == 'horizontally') { 

			if ($("#ud_show_heading_labels").is(':checked'))
			{
				$("#user_name_label").show();
				$("#email_label").show();
				$("#website_label").show();
				$("#address_label").show();
				$("#social_label").show();
				
				for(var i=1; i<11; i++)
				{	
					if($('#user_directory_num_meta_flds').val() >= i) 
						$("#col_meta_label_" + i).show();					
				}
			}
			else
			{
				$("#user_name_label").hide();
				$("#email_label").hide();
				$("#website_label").hide();
				$("#address_label").hide();
				$("#social_label").hide();
				
				for(var i=1; i<11; i++)
				{	
					if($('#user_directory_num_meta_flds').val() >= i) 
						$("#col_meta_label_" + i).hide();					
				}
			}
			
			for(var i=1; i<11; i++)
			{	
				if($('#user_directory_num_meta_flds').val() >= i) 
					$("#meta_label_" + i).hide();					
			}
				
    		$("#ud-horizontal-settings-1").show();  
			$("#ud-horizontal-settings-2").show();  	
			$("#ud-horizontal-settings-3").show();  
			$("#ud-horizontal-settings-4").show(); 
			$("#horizontal-width-settings-1").show();
			$("#horizontal-width-settings-2").show(); 
			$("#horizontal-width-settings-3").show(); 
			$("#horizontal-width-settings-4").show(); 
			$("#ud_avatar_size_and_padding").show();
			$("#avatar_padding").hide();
			$("#horizontal-width-settings-2").show(); 	
			
			$("#user_directory_border option[value='surrounding_border']").remove();
		}
    	else
    	{
			$("#user_name_label").hide();
			$("#email_label").hide();
			$("#website_label").hide();
			$("#address_label").hide();
			$("#social_label").hide();
			
			for(var i=1; i<11; i++)
			{	
				if($('#user_directory_num_meta_flds').val() >= i)  
				{					
					$("#meta_label_" + i).show();
					$("#col_meta_label_" + i).hide();
				}					
			}
			
    		$("#ud-horizontal-settings-1").hide();
			$("#ud-horizontal-settings-2").hide();
			$("#ud-horizontal-settings-3").hide();  
			$("#ud-horizontal-settings-4").hide(); 
            $("#horizontal-width-settings-1").hide(); 	
            $("#horizontal-width-settings-2").hide(); 	
			$("#horizontal-width-settings-3").hide(); 
			$("#horizontal-width-settings-4").hide(); 			
			$("#ud_avatar_size_and_padding").show();
			$("#avatar_padding").show();

			var optionExists = ($("#user_directory_border option[value='surrounding_border']").length > 0);

			if(!optionExists)
			{
				$('#user_directory_border').append("<option value='surrounding_border'>Surrounding Border</option>");
			}
    	} 
   	});
    });
	
	 /*$(document).ready(function() {
    	if($('#ud_set_col_widths').val() == 'custom') 
		{    	 	
			//$("#horizontal-width-settings-2").show(); 	
		}
    	else
    	{
			$("#col_width_avatar").val("");
			$("#col_width_name").val("");
			$("#col_width_email").val("");
			$("#col_width_website").val("");
			$("#col_width_address").val("");
			
			for(var i=1; i<11; i++)
			{	
				if($('#user_directory_num_meta_flds').val() >= i)     	 	
				{
					
					try {
						$("#line_break_" + i).val("");   
					}
					catch(err){;}
				}
			}
			
			//$("#horizontal-width-settings-2").hide(); 	
    	} 
    });*/ 	
    
    /*$(function() {
   	$('#ud_set_col_widths').change(function() {
       	if($('#ud_set_col_widths').val() == 'custom') 
		{    	 	
			//$("#horizontal-width-settings-2").show(); 	
		}
    	else
    	{
			$("#ud_line_break_avatar").val("");
			$("#ud_line_break_name").val("");
			$("#ud_line_break_email").val("");
			$("#ud_line_break_website").val("");
			$("#ud_line_break_address").val("");
			
			for(var i=1; i<11; i++)
			{	
				if($('#user_directory_num_meta_flds').val() >= i)     	 	
				{
					try {
						$("#ud_line_break_" + i).val("");   
					}
					catch(err){;}
				}
			}
			
			//$("#horizontal-width-settings-2").hide(); 
    	} 
   	});
    });*/
	
    $(document).ready(function() {	
    
    	if ($("#dynamic_ud_cimy_plugin").val().indexOf("INSTALLED AND ACTIVE") > -1) 
    		$("#cimy_key_names").show();
    	else
    		$("#cimy_key_names").hide();
    	
    });
    
    $(document).ready(function() {	
    	if ($("#ud_show_srch").is(':checked'))
    		$("#ud_srch_style").show();
    	else
    		$("#ud_srch_style").hide();
    });
    
    $(document).ready(function() {	
    	if ($("#user_directory_show_avatars").is(':checked'))
		{
    		$("#user_directory_avatar_style").show();
			$("#avatar_padding").show();
			
			try 
			{
				if($('#ud_display_listings').val() == 'horizontally') 
					$("#avatar_padding").hide();
			}
			catch(err){;}
		}
    	else
		{
    		$("#user_directory_avatar_style").hide();
			
			try {
				$("#ud_avatar_size_and_padding").hide();
				$("#avatar_padding").hide();
			}
			catch(err){;}
		}
		
    });
    
    $(document).ready(function() {	
    	if ($("#ud_author_page").is(':checked')) {	
    		$("#ud_target_window").show();
			$("#open_linked_page").show();
			$('#ud_auth_or_bp').show();
			
			if($('#ud_auth_or_bp').length)
			{
				if($('#ud_auth_or_bp').val() === 'auth')
					$("#show-auth-pg-lnk").show();
				else 
					$("#show-auth-pg-lnk").hide();
			}
			else
				$("#show-auth-pg-lnk").show();
		}
    	else {			
		    $("#show-auth-pg-lnk").hide();
    		$("#ud_target_window").hide();	
			$("#open_linked_page").hide();
			$('#ud_auth_or_bp').hide();
		}
    });
    
    $(function() {
   	$('#ud_author_page').change(function() {
			$("#open_linked_page").toggle(this.checked);
       		$("#ud_target_window").toggle(this.checked);
			$('#ud_auth_or_bp').toggle(this.checked);
			
			if($('#ud_auth_or_bp').length)
			{
				if($('#ud_auth_or_bp').val() === 'auth' && $('#ud_author_page').is(':checked'))
					$("#show-auth-pg-lnk").show();
				else 
					$("#show-auth-pg-lnk").hide();
			}
			else
			{
				$("#show-auth-pg-lnk").toggle(this.checked);
			}
   	});
    });
	
	$(function() {
   	$('#ud_auth_or_bp').change(function() {
       					
			if($('#ud_auth_or_bp').val() === 'bp')
			{
				$("#show-auth-pg-lnk").hide();
			}
			else 
			{
				$("#show-auth-pg-lnk").show();
			}
   	});
    });
    
    $(document).ready(function() {
    	if($('#user_directory_border').val() == 'no_border') {    	 	
    		$("#border-settings").hide(); 
			$("#border-settings-2").hide();
   	}
    	else
    	{
    		$("#border-settings").show();
			$("#border-settings-2").show();
    	} 
    }); 	
    
    $(function() {
   	$('#user_directory_border').change(function() {
       		if($('#user_directory_border').val() == 'no_border') {    	 	
    			$("#border-settings").hide(); 
				$("#border-settings-2").hide(); 
   		}
    		else
    		{
    			$("#border-settings").show();
				$("#border-settings-2").show();
    		} 
   	});
    });
    
    $(function() {
   	$('#user_directory_show_avatars').change(function() {
       		$("#user_directory_avatar_style").toggle(this.checked);
				
			if($('#user_directory_show_avatars').is(':checked'))
			{
				$("#ud_avatar_size_and_padding").show();
				$("#avatar_padding").show();
				
				try 
				{
					if($('#ud_display_listings').val() == 'horizontally') 
						$("#avatar_padding").hide();
				}
				catch(err){;}
			}
			else
			{
				$("#ud_avatar_size_and_padding").hide();
				$("#avatar_padding").hide();
			}
   	});
    });
    
    $(function() {
   	$('#ud_show_srch').change(function() {
       		$("#ud_srch_style").toggle(this.checked);
   	});
    });
    
    $(document).ready(function() {
    	if($('#ud_directory_type').val() == 'all-users') 
		{   
    		$("#one-page-dir-type-a").show();   	 	
    		$("#letter-link-dir-type").hide();
			$("#show_srch_results").hide();			
    		
    		if($('#ud_letter_divider').val() !== 'nld')
			{
				$("#one-page-dir-type-b").show();  
				
				if($('#ud_letter_divider').val() == 'ld-bb' || $('#ud_letter_divider').val() == 'ld-tb')
				{
					$("#letter-divider-border-settings").show();
					$("#letter-divider-border-settings-2").show();
					$("#divider-fill-color").hide();
					$("#divider-font-size").show();
				}
				else if($('#ud_letter_divider').val() == 'ld-lo')
				{
					$("#letter-divider-border-settings").hide();
					$("#letter-divider-border-settings-2").hide();
					$("#divider-fill-color").hide();
					$("#divider-font-size").show();
				}
				else
				{
					$("#letter-divider-border-settings").hide();
					$("#letter-divider-border-settings-2").hide();
					$("#divider-fill-color").show();
					$("#divider-font-size").hide();
				}
    		     
			}
    		else 
			{		
    		     $("#one-page-dir-type-b").hide(); 
				 $("#letter-divider-border-settings").hide();
				 $("#letter-divider-border-settings-2").hide();
			}
		}
    	else
    	{  
			$("#letter-divider-border-settings").hide();
			$("#letter-divider-border-settings-2").hide();
    		$("#one-page-dir-type-a").hide();  
    		$("#one-page-dir-type-b").hide();  	 	
    		$("#letter-link-dir-type").show(); 
			$("#show_srch_results").show();
    	} 
    }); 	
    
	
			
    $(function() {
   	$('#ud_letter_divider').change(function() {
		    
		if($('#ud_directory_type').val() == 'all-users') 
		{   
    		$("#one-page-dir-type-a").show();   	 	
    		$("#letter-link-dir-type").hide();
			$("#show_srch_results").hide();			
    		
    		if($('#ud_letter_divider').val() !== 'nld')
			{
				$("#one-page-dir-type-b").show();  
				
				if($('#ud_letter_divider').val() == 'ld-bb' || $('#ud_letter_divider').val() == 'ld-tb')
				{
					$("#letter-divider-border-settings").show();
					$("#letter-divider-border-settings-2").show();
					$("#divider-fill-color").hide();
					$("#divider-font-size").show();
				}
				else if($('#ud_letter_divider').val() == 'ld-lo')
				{
					$("#letter-divider-border-settings").hide();
					$("#letter-divider-border-settings-2").hide();
					$("#divider-fill-color").hide();
					$("#divider-font-size").show();
				}
				else
				{
					$("#letter-divider-border-settings").hide();
					$("#letter-divider-border-settings-2").hide();
					$("#divider-fill-color").show();
					$("#divider-font-size").hide();
				}
    		     
			}
    		else 
			{		
    		     $("#one-page-dir-type-b").hide(); 
				 $("#letter-divider-border-settings").hide();
				 $("#letter-divider-border-settings-2").hide();
			}
		}
    	else
    	{  
			$("#letter-divider-border-settings").hide();
			$("#letter-divider-border-settings-2").hide();
    		$("#one-page-dir-type-a").hide();  
    		$("#one-page-dir-type-b").hide();  	 	
    		$("#letter-link-dir-type").show(); 
			$("#show_srch_results").show();
    	} 
   	});
    });
      
	 $(function() {
   	$('#ud_directory_type').change(function() {
		    
		if($('#ud_directory_type').val() == 'all-users') 
		{   
    		$("#one-page-dir-type-a").show();   	 	
    		$("#letter-link-dir-type").hide();
			$("#show_srch_results").hide();			
    		
    		if($('#ud_letter_divider').val() !== 'nld')
			{
				$("#one-page-dir-type-b").show();  
				
				if($('#ud_letter_divider').val() == 'ld-bb' || $('#ud_letter_divider').val() == 'ld-tb')
				{
					$("#letter-divider-border-settings").show();
					$("#letter-divider-border-settings-2").show();
					$("#divider-fill-color").hide();
					$("#divider-font-size").show();
				}
				else if($('#ud_letter_divider').val() == 'ld-lo')
				{
					$("#letter-divider-border-settings").hide();
					$("#letter-divider-border-settings-2").hide();
					$("#divider-fill-color").hide();
					$("#divider-font-size").show();
				}
				else
				{
					$("#letter-divider-border-settings").hide();
					$("#letter-divider-border-settings-2").hide();
					$("#divider-fill-color").show();
					$("#divider-font-size").hide();
				}
    		     
			}
    		else 
			{		
    		     $("#one-page-dir-type-b").hide(); 
				 $("#letter-divider-border-settings").hide();
				 $("#letter-divider-border-settings-2").hide();
			}
		}
    	else
    	{  
			$("#letter-divider-border-settings").hide();
    		$("#one-page-dir-type-a").hide();  
    		$("#one-page-dir-type-b").hide();  	 	
    		$("#letter-link-dir-type").show(); 
			$("#show_srch_results").show();
    	} 
   	});
    });
	
    $(document).ready(function() {	
		
    	if ($("#user_directory_website").is(':checked'))
		{
			try {
				if ($("#ud_display_listings").val() == 'horizontally')
				{
					$("#col_width_website").show();
					if ($("#ud_show_heading_labels").is(':checked'))
					{
						$("#website_label").show();
					}
					else
					{
						$("#website_label").hide();
					}
				}
			}
			catch(err){;}
			
    		$("#Website").show();
		}
    	else
		{
			try {
				$("#col_width_website").hide();
				$("#website_label").hide();	
			}
			catch(err){;}
			
    		$("#Website").hide(); 
		}			
    });  
    
    $(function() {
   	$('#user_directory_website').change(function() {
		if ($("#user_directory_website").is(':checked'))
		{
			try {
				if ($("#ud_display_listings").val() == 'horizontally')
				{
					$("#col_width_website").show();
					if ($("#ud_show_heading_labels").is(':checked'))
						$("#website_label").show();
					else
						$("#website_label").hide();
				}
			}
			catch(err){;}
			
			$("#Website").show();
		}
   		else
		{
			try {
					if ($("#ud_display_listings").val() == 'horizontally')
					{
						$("#col_width_website").hide();
						$("#website_label").hide();
						
					}
			}
			catch(err){;}
				
   			$("#Website").hide(); 
    	}		
    		var Order = $("#sortable").sortable('toArray').toString();
                $('#user_directory_sort_order').val(Order);
    	   });
    });
    
    /*$(function() {
   	$('#ud_letter_divider').change(function() {
       		if ($("#ud_letter_divider").val() == 'ld-ds' || $("#ud_letter_divider").val() == 'ld-fl')
    			$("#one-page-dir-type-b").show();
   		else
   			$("#one-page-dir-type-b").hide(); 
    			
    	   });
    });
    
     $(document).ready(function() {	
    	if( ( $("#ud_letter_divider").val() == 'ld-ds' || $("#ud_letter_divider").val() == 'ld-fl' ) 
    		&& $('#ud_directory_type').val() == 'all-users')
    			$("#one-page-dir-type-b").show();
    	else
    		$("#one-page-dir-type-b").hide();  		
    });*/
   
     $(document).ready(function() {	
    	if ($("#ud_display_listings").val() == 'horizontally' && $("#ud_show_heading_labels").is(':checked'))
		{
			try 
			{	
				$("#user_name_label").show();
				$("#email_label").show();
				$("#website_label").show();
				$("#address_label").show();
				$("#social_label").show();
				
				for(var i=1; i<11; i++)
				{	
					if($('#user_directory_num_meta_flds').val() >= i) 
					{						
						$("#meta_label_" + i).hide();
						$("#col_meta_label_" + i).show();						
					}
				}
			}
			catch(err){;}
		}
    	else
		{
			try 
			{
				$("#user_name_label").hide();
				$("#email_label").hide();
				$("#website_label").hide();
				$("#address_label").hide();
				$("#social_label").hide();
				
				for(var i=1; i<11; i++)
				{	
					if($('#user_directory_num_meta_flds').val() >= i)
					{	
						if ($("#ud_display_listings").val() == 'horizontally')
							$("#meta_label_" + i).hide();
						else
							$("#meta_label_" + i).show();
						
						$("#col_meta_label_" + i).hide();
					}
				}
			}
			catch(err){;}
		}			
    });  
    
     $(function() {
   	 $('#ud_show_heading_labels').change(function() {
       	if ($("#ud_display_listings").val() == 'horizontally' && $("#ud_show_heading_labels").is(':checked'))
		{
			$("#user_name_label").show();
			$("#email_label").show();
			$("#website_label").show();
			$("#address_label").show();
			$("#social_label").show();
			
			for(var i=1; i<11; i++)
			{	
				if($('#user_directory_num_meta_flds').val() >= i) 
				{					
					$("#meta_label_" + i).hide();
					$("#col_meta_label_" + i).show();
				}					
			}
		}
    	else
		{
			$("#user_name_label").hide();
			$("#email_label").hide();
			$("#website_label").hide();
			$("#address_label").hide();
			$("#social_label").hide();
			
			for(var i=1; i<11; i++)
			{	
				if($('#user_directory_num_meta_flds').val() >= i)  
				{					
					if ($("#ud_display_listings").val() == 'horizontally')
						$("#meta_label_" + i).hide();
					else
						$("#meta_label_" + i).show();
					
					$("#col_meta_label_" + i).hide();
				}					
			}
		}

		});
    });

   
    $(document).ready(function() {	
    	if ($("#user_directory_email").is(':checked'))
		{
			try {
					if ($("#ud_display_listings").val() == 'horizontally')
					{
						$("#col_width_email").show();
						if ($("#ud_show_heading_labels").is(':checked'))
							$("#email_label").show();
						else
							$("#email_label").hide();
					}
			}
			catch(err){;}
			
    		$("#Email").show();
		}
    	else
		{
			try {
					if ($("#ud_display_listings").val() == 'horizontally')
					{
						$("#col_width_email").hide();
						$("#email_label").hide();
					}
			}
			catch(err){;}
			
    		$("#Email").hide(); 
		}			
    });  
    		
    $(function() {
   	$('#user_directory_email').change(function() {
       		if ($("#user_directory_email").is(':checked'))
			{
				try {
					if ($("#ud_display_listings").val() == 'horizontally')
					{
						$("#col_width_email").show();
						if ($("#ud_show_heading_labels").is(':checked'))
							$("#email_label").show();
						else
							$("#email_label").hide();
					}
			}
			catch(err){;}
			
    			$("#Email").show();
			}
   		else
		{
			try {
					if ($("#ud_display_listings").val() == 'horizontally')
					{
						$("#col_width_email").hide();
						$("#email_label").hide();
					}
			}
			catch(err){;}
			
   			$("#Email").hide(); 
		}
    			
    		var Order = $("#sortable").sortable('toArray').toString();
                $('#user_directory_sort_order').val(Order);
    	   });
    });
    
    $(document).ready(function() {	
    	if ($("#user_directory_address").val() == "1")
    	{
    		$("#street1").hide();
    		$("#street2").hide();
    		$("#city").hide();
    		$("#state").hide();
    		$("#zip").hide();
			$("#country").hide();
			
			$("#user_directory_addr_1").val("");
			$("#user_directory_addr_2").val("");
			$("#user_directory_city").val("");
			$("#user_directory_state").val("");
			$("#user_directory_zip").val("");
			$("#user_directory_country").val("");
    		
    		$("#Address").hide(); 
			
			try
			{
				$("#ud_line_break_address").val("");
				$("#col_width_address").hide();
			}
			catch(err) {;}
			
			$("#address-down-arrow").show();
			$("#address-up-arrow").hide();
			
    	}
    	else
    	{
    		$("#street1").show();
    		$("#street2").show();
    		$("#city").show();
    		$("#state").show();
    		$("#zip").show();
			$("#country").show();
						
			$("#Address").show(); 
			
			try
			{
				$("#col_width_address").show();
				
				if ($("#ud_show_heading_labels").is(':checked'))
					$("#addr_label").show();
				else
					$("#addr_label").hide();
			}
			catch(err) {;}
			
			$("#address-up-arrow").show();
			$("#address-down-arrow").hide();
			
    	}
    });
	
	 $(document).ready(function() {	
    	if ($("#ud_social").val() == "1")
    	{
    		$("#facebook").hide();
    		$("#twitter").hide();
    		$("#linkedin").hide();
    		$("#google").hide();
    		$("#instagram").hide();
			$("#pintrest").hide();
			
			$("#icon_size").hide();
			$("#icon_color").hide();
			$("#icon_style").hide();
			
			$("#ud_facebook").val("");
    		$("#ud_twitter").val("");
    		$("#ud_linkedin").val("");
    		$("#ud_google").val("");
    		$("#ud_instagram").val("");
			$("#ud_pinterest").val("");
			
			$("#Social").hide(); 
			
			try
			{
				$("#col_width_social").hide();
				
				if ($("#ud_show_heading_labels").is(':checked'))
					$("#social_label").show();
				else
					$("#social_label").hide();
			}
			catch(err) {;}
			
			$("#social-down-arrow").show();
			$("#social-up-arrow").hide();
			
    	}
    	else
    	{
			$("#facebook").show();
    		$("#twitter").show();
    		$("#linkedin").show();
    		$("#google").show();
    		$("#instagram").show();
			$("#pintrest").show();
			
			$("#icon_size").show();
			$("#icon_color").show();
			$("#icon_style").show();
			
			$("#Social").show();
 			
			try
			{
				$("#col_width_social").show();
				
				if ($("#ud_show_heading_labels").is(':checked'))
					$("#social_label").show();
				else 
					$("#social_label").hide();
				
			}
			catch(err) {;}
			
			$("#social-up-arrow").show();
			$("#social-down-arrow").hide();
			
    	}
    });
    
	$( "#address-down-arrow" ).click(function() {
		
		    $("#street1").show();
    		$("#street2").show();
    		$("#city").show();
    		$("#state").show();
    		$("#zip").show();
			$("#country").show();
			
		    $("#address-up-arrow").show();
			$("#address-down-arrow").hide();
			
			$("#Address").show(); 
			
			try
			{
				$("#col_width_address").show();
				
				if ($("#ud_show_heading_labels").is(':checked'))
					$("#addr_label").show();
				else
					$("#addr_label").hide();
			}
			catch(err) {;}
			
			$("#user_directory_address").val("");
			
			var Order = $("#sortable").sortable('toArray').toString();
                $('#user_directory_sort_order').val(Order);
	});
	
	$( "#social-down-arrow" ).click(function() {
		
		    $("#facebook").show();
    		$("#twitter").show();
    		$("#linkedin").show();
    		$("#google").show();
    		$("#instagram").show();
			$("#pintrest").show();
			
			$("#icon_size").show();
			$("#icon_color").show();
			$("#icon_style").show();
			
		    $("#social-up-arrow").show();
			$("#social-down-arrow").hide();
			
			$("#Social").show(); 
			
			try
			{
				$("#col_width_social").show();
				if ($("#ud_show_heading_labels").is(':checked'))
					$("#social_label").show();
				else
					$("#social_label").hide();
			}
			catch(err) {;}
			
			$("#ud_social").val("");
			
			var Order = $("#sortable").sortable('toArray').toString();
                $('#user_directory_sort_order').val(Order);
	});
	
	
	$( "#address-up-arrow" ).click(function() {
		
		    $("#street1").hide();
    		$("#street2").hide();
    		$("#city").hide();
    		$("#state").hide();
    		$("#zip").hide();
			$("#country").hide();
			
			$("#user_directory_addr_1").val("");
			$("#user_directory_addr_2").val("");
			$("#user_directory_city").val("");
			$("#user_directory_state").val("");
			$("#user_directory_zip").val("");
			$("#user_directory_country").val("");
			
			$("#Address").hide();

			try
			{
				$("#ud_line_break_address").val("");
				$("#col_width_address").hide();
				$("#addr_label").hide();
			}
			catch(err) {;}
    		
			$("#address-down-arrow").show();
			$("#address-up-arrow").hide();
			
			$("#user_directory_address").val("1");
			
			var Order = $("#sortable").sortable('toArray').toString();
                $('#user_directory_sort_order').val(Order);
	});
	
	$( "#social-up-arrow" ).click(function() {
		
		    $("#facebook").hide();
    		$("#twitter").hide();
    		$("#linkedin").hide();
    		$("#google").hide();
    		$("#instagram").hide();
			$("#pintrest").hide();
			
			$("#icon_size").hide();
			$("#icon_color").hide();
			$("#icon_style").hide();
			
			$("#ud_facebook").val("");
    		$("#ud_twitter").val("");
    		$("#ud_linkedin").val("");
    		$("#ud_google").val("");
    		$("#ud_instagram").val("");
			$("#ud_pinterest").val("");
			
			$("#Social").hide();

			try
			{
				$("#col_width_social").hide();
				$("#social_label").hide();
			}
			catch(err) {;}
    		
			$("#social-down-arrow").show();
			$("#social-up-arrow").hide();
			
			$("#ud_social").val("1");
			
			var Order = $("#sortable").sortable('toArray').toString();
                $('#user_directory_sort_order').val(Order);
	});
    
    
    $(document).ready(function() {
		for(var i=1; i<11; i++)
		{	
			if($('#user_directory_num_meta_flds').val() >= i)     	 	
			{
				$("#meta_fld_" + i).show(); 
				$("#MetaKey" + i).show();   
			    
				try {
					$("#col_width_" + i).show();  
					if ($("#ud_display_listings").val() == 'horizontally' && $("#ud_show_heading_labels").is(':checked'))
					{
						$("#meta_label_" + i).hide();
						$("#col_meta_label_" + i).show();
					}
					else if ($("#ud_display_listings").val() == 'horizontally')
					{
						$("#meta_label_" + i).hide();
						$("#col_meta_label_" + i).hide();						
					}
					
				}
				catch(err){;}
			}
			else
			{
				$("#meta_fld_" + i).hide(); 
				$("#MetaKey" + i).hide(); 
			
				try {
					$("#col_width_" + i).hide(); 
					$("#meta_label_" + i).hide();
					$("#col_meta_label_" + i).hide();
				}
				catch(err){;}
			}      
		}
		
		for(var iSrch=1; iSrch<16; iSrch++)
		{
           		
			if($('#user_directory_num_meta_srch_flds').val() >= iSrch)     	 	
			{
				$("#meta_srch_fld_" + iSrch).show(); 
			}
			else
			{
				$("#meta_srch_fld_" + iSrch).hide(); 
			}      
		}
    }); 
    
    
    $(function() {
   	$('#user_directory_num_meta_flds').change(function() {
		
       		for(var i=1; i<11; i++)
				{	
					if($('#user_directory_num_meta_flds').val() >= i)     	 	
					{
						$("#meta_fld_" + i).show(); 
						$("#MetaKey" + i).show(); 
						
						try {
							
							$("#col_width_" + i).show();  
							if ($("#ud_display_listings").val() == 'horizontally' && $("#ud_show_heading_labels").is(':checked'))
							{
								$("#meta_label_" + i).hide(); 
								$("#col_meta_label_" + i).show();
							}
							else if ($("#ud_display_listings").val() == 'horizontally')
							{
								$("#meta_label_" + i).hide(); 
								$("#col_meta_label_" + i).hide();
							}
							else 
							{
								$("#meta_label_" + i).show(); 	
							}
						}
						catch(err){;}

					}

					else
					{

						$("#meta_fld_" + i).hide(); 
						$("#MetaKey" + i).hide();
												
						try {
							
							$("#col_width_" + i).hide(); 
							$("#meta_label_" + i).hide();
							$("#col_meta_label_" + i).hide();
						}
						catch(err){;}

						$("#user_directory_meta_field_" + i).val("");
						$("#user_directory_meta_label_" + i).val("");
						$("#ud_line_break_" + i).val("");

					}      
				}	
    		
    		var Order = $("#sortable").sortable('toArray').toString();
                $('#user_directory_sort_order').val(Order); 
   	});
    });
    
	$(function() {
   	$('#user_directory_num_meta_srch_flds').change(function() {
		
       		for(var i=1; i<16; i++)
				{	
					if($('#user_directory_num_meta_srch_flds').val() >= i)     	 	
					{

						$("#meta_srch_fld_" + i).show();  		 

					}

					else
					{

						$("#meta_srch_fld_" + i).hide(); 

						$("#user_directory_meta_srch_field_" + i).val("");

						$("#user_directory_meta_srch_label_" + i).val("");

					}      
				}	
   	});
    });
   
    $(function() {
    	$( "#sortable" ).sortable();
    	$( "#sortable" ).disableSelection();
    }); 
    
    $('#Email').hover(function() {
        $(this).css('cursor','pointer');
    });
    $('#Website').hover(function() {
        $(this).css('cursor','pointer');
    });
    $('#Address').hover(function() {
        $(this).css('cursor','pointer');
    });
    $('#MetaKey1').hover(function() {
        $(this).css('cursor','pointer');
    });
    $('#MetaKey2').hover(function() {
        $(this).css('cursor','pointer');
    });
    $('#MetaKey3').hover(function() {
        $(this).css('cursor','pointer');
    });
    $('#MetaKey4').hover(function() {
        $(this).css('cursor','pointer');
    });
    $('#MetaKey5').hover(function() {
        $(this).css('cursor','pointer');
    });
    
    $(function() {
        $( "#sortable" ).sortable({
            placeholder: "ui-state-highlight",
            cursor: 'crosshair',
            update: function(event, ui) {
               var Order = $("#sortable").sortable('toArray').toString();
               $('#user_directory_sort_order').val(Order);
              
             }
         });
         
     });

	/* For Multipl Dirs Add-on */ 
	$( "#delete" ).click(function() {
		
    if ( confirm( "Delete this directory instance?" ) )
       document.LoadInstance.submit();
    else
        return false;
	});
	
	$( "#add" ).click(function() {
		
    if ( $('#dud_new_instance_name').val() ) 
       document.AddInstance.submit();
    else
	{
        alert("Please enter a new directory name.");
		return false;
	}
	});
	
	$(document).ready(function() {	
    	try 
		{
			if ($("#ud_show_pagination_top_bottom").val() == 'top' || $("#ud_show_pagination_top_bottom").val() == 'both')
				$("#pagination_above_below").show();
			else
				$("#pagination_above_below").hide();
		}
		catch(err){;}
    });
    
    $(function() {
   	$('#ud_show_pagination_top_bottom').change(function() {
			if ($("#ud_show_pagination_top_bottom").val() == 'top' || $("#ud_show_pagination_top_bottom").val() == 'both')
				$("#pagination_above_below").show();
			else
				$("#pagination_above_below").hide();
   	});
    });
	
})( jQuery );