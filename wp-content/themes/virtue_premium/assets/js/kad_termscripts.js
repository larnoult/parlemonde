jQuery(function($){
			$("#edittag").attr("enctype", "multipart/form-data");
		
			$("body").on("click", ".rwtm-delete-file", function(){
				$(this).parent().remove();
				return false;
			});
			$(".color").wpColorPicker();
			$('body').on('click', '.rwtm-image-upload', function(){
			var id = $(this).data('field');

			var $uploaded = $(this).siblings('.rwtm-uploaded');

			var frame = wp.media({
				multiple : true,
				title    : "Select Image",
				library  : {
					type: 'image'
				}
			});
			frame.on('select', function()
			{	
				var attachment = frame.state().get('selection').first();
                    frame.close();
                    $uploaded.append('<li><img src="'+attachment.attributes.url+'"><a class="rwtm-delete-file" href="#">Delete</a><input type="hidden" name="'+id+'[]" value="'+attachment.attributes.id+'"></li>');

				
			});
			frame.open();

			return false;
		});
		});