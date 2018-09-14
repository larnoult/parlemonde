jQuery(document).ready(function($){
	(function() {
		/*** hide wordpress admin stuff ***/
		$('#minor-publishing-actions').hide();
		$('#misc-publishing-actions').hide();
		
		/*** make it sortable ***/
		$('.cycloneslider-sortable').sortable({
			handle:'.cycloneslider-box-title',
			placeholder: "cycloneslider-box-placeholder",
			forcePlaceholderSize:true,
			update: function(event, ui) {
				$('.cycloneslider-sortable .cycloneslider-box').each(function(i){
					$(this).find('.cycloneslider-slide-meta-id').attr('name', 'cycloneslider_metas['+(i)+'][id]');
					$(this).find('.cycloneslider-slide-meta-link').attr('name', 'cycloneslider_metas['+(i)+'][link]');
					$(this).find('.cycloneslider-slide-meta-title').attr('name', 'cycloneslider_metas['+(i)+'][title]');
					$(this).find('.cycloneslider-slide-meta-description').attr('name', 'cycloneslider_metas['+(i)+'][description]');
				});
			}
		});
		
		/*** ID ***/
		$('.cycloneslider-upload-button').each(function(i){
			$(this).data('cycloneslider_id',i);
		});
		$('.cycloneslider-sortable .cycloneslider-box').each(function(i){
			$(this).data('cycloneslider_id',i);
			$(this).find('.cycloneslider-box-title-left').append((i+1));
		});
		
		
		
		/*** Add new slide box ***/
		$('input[name="cycloneslider_add_slide"]').on('click', function(e){
			var id = $('.cycloneslider-sortable .cycloneslider-box').length;
			var html = $('.cycloneslider-box-template').html();
			html = html.replace(/{id}/g, id);/*** replace all occurences of {id} to real id ***/
			
			$('.cycloneslider-sortable').append(html);
			$('.cycloneslider-sortable .cycloneslider-box:last').find('.cycloneslider-slide-thumb').hide().end().find('.cycloneslider-box-body').show();
			$('.cycloneslider-upload-button').each(function(i){
				$(this).data('cycloneslider_id',i);
			});
			$('.cycloneslider-sortable .cycloneslider-box').each(function(i){
				$(this).data('cycloneslider_id',i);
			});
			$('.cycloneslider-field-body').each(function(i){
				$(this).data('cycloneslider_id',i);
			});

			e.preventDefault();
		});
		
		/*** Toggle slide visiblity ***/
		$('#cyclone-slides-metabox').on('click', '.cycloneslider-box-title', function(e) {
			var box = $(this).parents('.cycloneslider-box');
			var body = box.find('.cycloneslider-box-body');
			
			if(body.is(':visible')){
				body.slideUp(100);
				if($.cookie!=undefined){
					$.cookie('cycloneslider_box_'+box.data('cycloneslider_id'), null);
				}
				
			} else {
				body.slideDown(100);
				if($.cookie!=undefined){
					$.cookie('cycloneslider_box_'+box.data('cycloneslider_id'), 'open', { expires: 7});/*** remember open section ***/
				}
			}
			e.preventDefault();
		});
		
		/*** Slide Properties ***/
		$('.cycloneslider-field-body').each(function(i){
			$(this).data('cycloneslider_id',i);
		});
		/*** Slide Properties Toggle ***/
		$('.cycloneslider-meta-field .cycloneslider-field-title').live('click',function(e){
			var body = $(this).next();
			var id = body.data('cycloneslider_id');
			if(body.is(':visible')){
				body.slideUp(100);
				if($.cookie!=undefined){
					$.cookie('cycloneslider_slide_meta_field_'+id, null);/*** delete cookie ***/
				}
			} else {
				body.slideDown(100);
				if($.cookie!=undefined){
					$.cookie('cycloneslider_slide_meta_field_'+id, 'open', { expires: 7});/*** remember open section ***/
				}
			}
		});
		/*** Slide Properties Cookie ***/
		$('.cycloneslider-field-body').each(function(i){
			body = $(this);
			var id = $(this).data('cycloneslider_id');
			if($.cookie!=undefined){
				if($.cookie('cycloneslider_slide_meta_field_'+id)!='open'){/*** do not close open section ***/
					body.hide();
				}
			}
		});
		/*** hide all thats hidden ***/
		$('.cycloneslider-sortable .cycloneslider-box').each(function(){
			var body = $(this).find('.cycloneslider-box-body');
			var id = $(this).data('cycloneslider_id');
			if($.cookie!=undefined){
				if($.cookie('cycloneslider_box_'+id)=='open'){/*** do not close open section ***/
					body.show();
				}
			}
		});
		
		/*** Delete Slide ***/
		$('.cycloneslider-box-delete').live('click',function(e) {

			var box = $(this).parents('.cycloneslider-box');
			box.fadeOut('slow', function(){ box.remove()});

			e.preventDefault();
		});
	})();
	
	
	if( !cycloneslider_admin_vars.use_new_media ){
		/*** Old Media < WP 3.5 ***/
		(function() {
			/*** Modify WP media uploader ***/
			var current_slide_box = false;/*** we use this var to determine if thickbox is being used in cycloneslider. also saves the field to be updated later. ***/
			$(document).on('click', '.cycloneslider-upload-button', function() {
				var box = $(this).parents('.cycloneslider-box');/*** get current box ***/
				
				current_slide_box = box;
				tb_show('', 'media-upload.php?referer=cycloneslider&amp;post_id=0&amp;type=image&amp;TB_iframe=true');/*** referer param needed to change button text ***/
				return false;
			});
			
			window.original_send_to_editor = window.send_to_editor;/*** backup original for other parts of admin that uses thickbox to work ***/
			window.send_to_editor = function(html) {
				if (current_slide_box) {
					var slide_thumb = current_slide_box.find('.cycloneslider-slide-thumb');/*** find the thumb ***/
					var slide_attachment_id = current_slide_box.find('.cycloneslider-slide-meta-id');/*** find the hidden field that will hold the attachment id ***/
					var slide_type = current_slide_box.find('.cycloneslider-slide-meta-type');/*** find the hidden field that will hold the type ***/
					
					var image = false;
					if(jQuery(html).get(0) != undefined){ /*** Check if its a valid html tag ***/
						if(jQuery(html).get(0).nodeName.toLowerCase()=='img'){/*** Check if html is an img tag ***/
							image = jQuery(html);
						} else { /*** If not may be it contains the img tag ***/
							if(jQuery(html).find('img').length > 0){
								image = jQuery(html).find('img');
							}
						}
					}
					if(image){
						var url = image.attr('src');
						var attachment_id = image.attr('data-id');
						if(url!=undefined && attachment_id != undefined ){
							slide_thumb.attr('src', url).show();
							slide_attachment_id.val(attachment_id);
							slide_type.val('image');
						} else {
							alert('Could not insert image. URL or attachment ID missing.');
						}
					} else {
						alert('Could not insert image.');
					}
					
					tb_remove();
					current_slide_box = false;
				} else {
					window.original_send_to_editor(html);
				}
			};
		})();
		
	} else {
		
		/*** New Media WP 3.5+ ***/
		(function() {
			if(typeof(wp) == "undefined" || typeof(wp.media) != "function"){
				return;
			}
			// Prepare the variable that holds our custom media manager.
			var cyclone_media_frame;
			var current_slide_box = false;
			
			// Bind to our click event in order to open up the new media experience.
			$(document.body).on('click', '.cycloneslider-upload-button', function(e){
				// Prevent the default action from occuring.
				e.preventDefault();
				
				current_slide_box = $(this).parents('.cycloneslider-box');/*** get current box ***/
				
				
				// If the frame already exists, re-open it.
				if ( cyclone_media_frame ) {
					cyclone_media_frame.open();
					return;
				}
		
	
				cyclone_media_frame = wp.media.frames.cyclone_media_frame = wp.media({
					className: 'media-frame cs-frame',
					frame: 'select',
					multiple: false,
					title: cycloneslider_admin_vars.title,
					library: {
						type: 'image'
					},
					button: {
						text:  cycloneslider_admin_vars.button
					}
				});
		
				cyclone_media_frame.on('select', function(){
					var media_attachment, slide_thumb, slide_attachment_id, img_url;
					
					// Grab our attachment selection and construct a JSON representation of the model.
					media_attachment = cyclone_media_frame.state().get('selection').first().toJSON();
					
					slide_thumb = current_slide_box.find('.cycloneslider-slide-thumb');/*** find the thumb ***/
					slide_attachment_id = current_slide_box.find('.cycloneslider-slide-meta-id');/*** find the hidden field that will hold the attachment id ***/
					
					if(undefined==media_attachment.sizes.medium){ /*** Account for smaller images where medium does not exist ***/
						img_url = media_attachment.url;
					} else {
						img_url = media_attachment.sizes.medium.url;
					}
					
					slide_thumb.attr('src', img_url).show();
					slide_attachment_id.val(media_attachment.id);
					
				});
		
				// Now that everything has been set, let's open up the frame.
				cyclone_media_frame.open();
			});
		})();
	}
});