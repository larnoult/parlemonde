
/* Largely from the Crelly Slider */

(function (original) {
  jQuery.fn.ksp_betterClone = function () {
    var result           = original.apply(this, arguments),
        my_textareas     = this.find('textarea').add(this.filter('textarea')),
        result_textareas = result.find('textarea').add(result.filter('textarea')),
        my_selects       = this.find('select').add(this.filter('select')),
        result_selects   = result.find('select').add(result.filter('select'));

    for (var i = 0, l = my_textareas.length; i < l; ++i) jQuery(result_textareas[i]).val(jQuery(my_textareas[i]).val());
    for (var i = 0, l = my_selects.length;   i < l; ++i) {
      for (var j = 0, m = my_selects[i].options.length; j < m; ++j) {
        if (my_selects[i].options[j].selected === true) {
          result_selects[i].options[j].selected = true;
        }
      }
    }
    return result;
  };
}) (jQuery.fn.clone);


(function($) {
    $(window).load(function() {


        // Run draggables
        ksp_draggable_layers();
        
        function ksp_showSuccess() {
            var overlay = $(document.getElementById('kt_ajax_overlay'));
            overlay.fadeOut();
            var target = $('.ksp-admin .ksp-message.ksp-message-ok');
            target.css({
                'display' : 'block',
                'opacity' : 0,
            });
            target.animate({
                'opacity' : 1,
            }, 300)
            .delay(2000)
            .animate({
                'opacity' : 0,
            }, 300, function() {
                target.css('display', 'none');
            });
        }
        
        function ksp_showError() {
            var overlay = $(document.getElementById('kt_ajax_overlay'));
            overlay.fadeOut();
            var target = $('.ksp-admin .ksp-message.ksp-message-error');
            target.css({
                'display' : 'block',
                'opacity' : 0,
            });
            target.animate({
                'opacity' : 1,
            }, 300)
            .delay(2000)
            .animate({
                'opacity' : 0,
            }, 300, function() {
                target.css('display', 'none');
            });
        }
        
        /*************/
        /** SLIDERS **/
        /*************/
        
        
        // Set the new sizes of the editing area and of the slider if changing values
        $('#ksp-slider-settings .ksp-settings-table #ksp-slider-maxWidth').keyup(function() {
            ksp_set_slides_editing_areasizes();
        });
        $('#ksp-slider-settings .ksp-settings-table #ksp-slider-maxHeight').keyup(function() {
            ksp_set_slides_editing_areasizes();
        });
        $('#ksp-slider-settings .ksp-settings-table #ksp-slider-fullHeight').change(function() {
            if( $(this).is(':checked') ) {
                $(this).closest('.ksp-settings-table').find('.ksp-full-height-offset-row').show();
            } else {
                $(this).closest('.ksp-settings-table').find('.ksp-full-height-offset-row').hide();
            }
        });
        function ksp_full_height_offset() {
            if( $('#ksp-slider-settings .ksp-settings-table #ksp-slider-fullHeight').length) {
                if( $('#ksp-slider-settings .ksp-settings-table #ksp-slider-fullHeight').is(':checked') ) {
                   $('#ksp-slider-settings .ksp-settings-table .ksp-full-height-offset-row').show();
                } else {
                    $('#ksp-slider-settings .ksp-settings-table .ksp-full-height-offset-row').hide();
                }
            }
        }
        ksp_full_height_offset();
        /************/
        /** SLIDES **/
        /************/
        
        var slides_number = $('.ksp-admin #ksp-slides .ksp-slide-tabs ul li').length - 1;
        
        // Run sortable
        var slide_before; // Contains the index before the sorting
        var slide_after; // Contains the index after the sorting
        $('.ksp-slide-tabs .ksp-sortable').sortable({
            items: 'li:not(.kt-ui-disabled)',
            cancel: '.kt-ui-disabled',
            
            // Store the actual index
            start: function(event, ui) {
                slide_before = $(ui.item).index();
            },
            
            // Change the .ksp-slide order based on the new index and rename the tabs
            update: function(event, ui) {
                // Store the new index
                slide_after = $(ui.item).index();
                
                // Change the slide position
                var slide = $('.ksp-admin #ksp-slides .ksp-slides-list .ksp-slide:eq(' + slide_before + ')');           
                var after = $('.ksp-admin #ksp-slides .ksp-slides-list .ksp-slide:eq(' + slide_after + ')');            
                if(slide_before < slide_after) {
                    slide.insertAfter(after);
                }
                else {
                    slide.insertBefore(after);
                }
                
                // Rename all the tabs
                $('.ksp-admin #ksp-slides .ksp-slide-tabs ul li').each(function() {
                    var temp = $(this);
                    if(!temp.find('a').hasClass('ksp-add-new')) {
                        temp.find('a').text(ksp_translations.slide + (temp.index() + 1));
                    }
                });
            }
        });
        $('.ksp-slide-tabs .ksp-sortable li').disableSelection();
        
        // Show the slide when clicking on the link
        $('.ksp-admin #ksp-slides .ksp-slide-tabs ul li a').live('click', function() {
            // Do only if is not click add new
            if($(this).parent().index() != slides_number) {
                // Hide all tabs
                $('.ksp-admin #ksp-slides .ksp-slides-list .ksp-slide').css('display', 'none');
                var tab = $(this).parent().index();
                $('.ksp-admin #ksp-slides .ksp-slides-list .ksp-slide:eq(' + tab + ')').css('display', 'block');
                
                // Active class
                $('.ksp-admin #ksp-slides .ksp-slide-tabs ul li').removeClass('active');
                $(this).parent().addClass('active');
            }
        });

        // Show hide video options.
        $('.ksp-slide-background_type_video').change(function() {
        	var optionSelected = $("option:selected", this);
        	var valueSelected = this.value;

            if( valueSelected == 'youtube') {
            	$(this).closest('.ksp-slide-settings-list').find('.ksp-video-options-row').show();
            	$(this).closest('.ksp-slide-settings-list').find('.ksp-video-html5-options-row').hide();
            	$(this).closest('.ksp-slide-settings-list').find('.ksp-video-youtube-options-row').show();
            } else if( valueSelected == 'html5') {
            	$(this).closest('.ksp-slide-settings-list').find('.ksp-video-options-row').show();
            	$(this).closest('.ksp-slide-settings-list').find('.ksp-video-html5-options-row').show();
            	$(this).closest('.ksp-slide-settings-list').find('.ksp-video-youtube-options-row').hide();
            } else {
                $(this).closest('.ksp-slide-settings-list').find('.ksp-video-html5-options-row').hide();
            	$(this).closest('.ksp-slide-settings-list').find('.ksp-video-youtube-options-row').hide();
            	$(this).closest('.ksp-slide-settings-list').find('.ksp-video-options-row').hide();
            }
        });
        function ksp_video_options_row() {
            if( $('#ksp-slide-settings .ksp-slide-settings-list .ksp-slide-background_type_video').length) {
            	$('#ksp-slide-settings .ksp-slide-settings-list .ksp-slide-background_type_video').each(function(){
	               var optionSelected = $("option:selected", this);
		        	var valueSelected = this.value;

		            if( valueSelected == 'youtube') {
		            	$(this).closest('.ksp-slide-settings-list').find('.ksp-video-options-row').show();
		            	$(this).closest('.ksp-slide-settings-list').find('.ksp-video-html5-options-row').hide();
		            	$(this).closest('.ksp-slide-settings-list').find('.ksp-video-youtube-options-row').show();
		            } else if( valueSelected == 'html5') {
		            	$(this).closest('.ksp-slide-settings-list').find('.ksp-video-options-row').show();
		            	$(this).closest('.ksp-slide-settings-list').find('.ksp-video-html5-options-row').show();
		            	$(this).closest('.ksp-slide-settings-list').find('.ksp-video-youtube-options-row').hide();
		            } else {
		                $(this).closest('.ksp-slide-settings-list').find('.ksp-video-html5-options-row').hide();
		            	$(this).closest('.ksp-slide-settings-list').find('.ksp-video-youtube-options-row').hide();
		            	$(this).closest('.ksp-slide-settings-list').find('.ksp-video-options-row').hide();
		            }
	            });
            }
        }
        ksp_video_options_row();
        // Add new
        function ksp_addSlide() {
            var add_btn = $('.ksp-admin #ksp-slides .ksp-add-new');
            
            var void_slide = $('.ksp-admin #ksp-slides .ksp-void-slide').html();
            // Insert the link at the end of the list
            add_btn.parent().before('<li class="kt-ui-default"><a>' + ksp_translations.slide + ' <span class="ksp-slide-index">' + (slides_number + 1) + '</span></a><span class="ksp-close"></span></li>');
            // jQuery UI tabs are not working here. For now, just use a manual created tab
            $('.ksp-admin #ksp-slides .ksp-slide-tab').tabs('refresh');
            // Create the slide
            $('.ksp-admin #ksp-slides .ksp-slides-list').append('<div class="ksp-slide">' + void_slide + '</div>');
            slides_number++;
            
            // Open the tab just created
            var tab_index = add_btn.parent().index() - 1;
            $('.ksp-admin #ksp-slides .ksp-slide-tabs ul li').eq(tab_index).find('a').click();
            
            // Active class
            $('.ksp-admin #ksp-slides .ksp-slide-tabs ul li').removeClass('active');
            $('.ksp-admin #ksp-slides .ksp-slide-tabs ul li').eq(tab_index).addClass('active');
            
            // Set editing area sizes
            ksp_set_slides_editing_areasizes();
            
            ksp_slides_color_picker();

            $('.ksp-slide-background_type_video').change(function() {
	        	var optionSelected = $("option:selected", this);
	        	var valueSelected = this.value;

	            if( valueSelected == 'youtube') {
	            	$(this).closest('.ksp-slide-settings-list').find('.ksp-video-options-row').show();
	            	$(this).closest('.ksp-slide-settings-list').find('.ksp-video-html5-options-row').hide();
	            	$(this).closest('.ksp-slide-settings-list').find('.ksp-video-youtube-options-row').show();
	            } else if( valueSelected == 'html5') {
	            	$(this).closest('.ksp-slide-settings-list').find('.ksp-video-options-row').show();
	            	$(this).closest('.ksp-slide-settings-list').find('.ksp-video-html5-options-row').show();
	            	$(this).closest('.ksp-slide-settings-list').find('.ksp-video-youtube-options-row').hide();
	            } else {
	                $(this).closest('.ksp-slide-settings-list').find('.ksp-video-html5-options-row').hide();
	            	$(this).closest('.ksp-slide-settings-list').find('.ksp-video-youtube-options-row').hide();
	            	$(this).closest('.ksp-slide-settings-list').find('.ksp-video-options-row').hide();
	            }
	        });
        }
        
        // Add new on click
        $('.ksp-admin #ksp-slides .ksp-add-new').click(function() {
            ksp_addSlide();
        }); 
        // Also add a new slide if slides_number == 0
        if(slides_number == 0) {
            ksp_addSlide();
        }
        else {
            $('.ksp-admin #ksp-slides .ksp-slide-tabs ul li').eq(0).find('a').click();
        }
        
        // Delete
        $('.ksp-admin #ksp-slides .ksp-slide-tabs ul li .ksp-close').live('click', function() {
            if($('.ksp-admin #ksp-slides .ksp-slide-tabs ul li').length <= 2) {
                alert(ksp_translations.slide_delete_just_one);
                return;
            }
            var confirm = window.confirm(ksp_translations.slide_delete_confirm);
            if(!confirm) {
                return;
            }
            
            slides_number--;
            
            var slide_index = $(this).parent().index();
            
            // If is deleting the current viewing slide, set the first as active
            if($('.ksp-admin #ksp-slides .ksp-slide-tabs ul li').eq(slide_index).hasClass('active') && slides_number != 0) {
                $('.ksp-admin #ksp-slides .ksp-slide-tabs ul li').eq(0).addClass('active');
                $('.ksp-admin #ksp-slides .ksp-slides-list .ksp-slide').css('display', 'none');
                $('.ksp-admin #ksp-slides .ksp-slides-list .ksp-slide').eq(0).css('display', 'block');          
            }
            
            // Remove the anchor
            $(this).parent().remove();
            // Remove the slide itself
            $('.ksp-admin #ksp-slides .ksp-slides-list .ksp-slide').eq(slide_index).remove();
            
            // Scale back all the slides text
            for(var i = slide_index; i < slides_number; i++) {
                var slide = $('.ksp-admin #ksp-slides .ksp-slide-tabs ul li').eq(i);
                var indx = parseInt(slide.find('.ksp-slide-index').text());
                slide.find('.ksp-slide-index').text(indx - 1);
            }
        });
        // Duplicate
        $('.ksp-admin #ksp-slides .ksp-slide-tabs > ul > li .ksp-duplicate').live('click', function() {         
            var slide_index = $(this).parent().index();
            var slide = $('.ksp-admin #ksp-slides .ksp-slides-list .ksp-slide').eq(slide_index);
            
            // Clone the slide settings table
            slide.ksp_betterClone(true).appendTo(slide.parent()).css('display', 'none');
            
           // Insert the link at the end of the list
           $(this).parent().parent().find('.ksp-add-new').parent().before('<li class="kt-ui-default"><a>' + ksp_translations.slide + ' <span class="ksp-slide-index">' + (slides_number + 1) + '</span></a><span class="ksp-duplicate"></span><span class="ksp-close"></span></li>');
          $('.ksp-admin #ksp-slides .ksp-slide-tab').tabs('refresh');
          
          slides_number++;

          ksp_saveSlider();

            $('.ksp-admin').on('savedSlide', function() {
                location.reload();
            });
        });

        // Set correct size for the editing area
        function ksp_set_slides_editing_areasizes() {
            var width = parseInt($('#ksp-slider-settings .ksp-settings-table #ksp-slider-maxWidth').val());
            var height = parseInt($('#ksp-slider-settings .ksp-settings-table #ksp-slider-maxHeight').val());
            
            $('.ksp-admin #ksp-slides .ksp-slide .ksp-slide-editing-area').css({
                'width' : width,
                'height' : height,
            });
            
            $('.ksp-admin').css({
                'width' : width + 40,
            });
        }
        
        ksp_slides_color_picker();
        
        // Run background color picker
        function ksp_slides_color_picker() {
            $('.ksp-admin #ksp-slides .ksp-slides-list .ksp-slide-settings-list .ksp-slide-background_type_color-picker-input').each(function() {
                $(this).wpColorPicker({
                    // a callback to fire whenever the color changes to a valid color
                    change: function(event, ui){
                        // Change only if the color picker is the user choice
                        var btn = $(this);
                        if(btn.closest('.ksp-content').find('input[name="ksp-slide-background_type_color"]:checked').val() == '1') {
                            var area = btn.closest('.ksp-slide').find('.ksp-layers .ksp-slide-editing-area');
                            area.css('background-color', ui.color.toString());
                        }
                    },
                    // a callback to fire when the input is emptied or an invalid color
                    clear: function() {},
                    // hide the color picker controls on load
                    hide: true,
                    // show a group of common colors beneath the square
                    // or, supply an array of colors to customize further
                    palettes: true
                });
            });
        }
        
        // Set background color (transparent or color-picker)
        $('.ksp-admin #ksp-slides').on('change', '.ksp-slides-list .ksp-slide-settings-list input[name="ksp-slide-background_type_color"]:radio', function() {
            var btn = $(this);
            var area = btn.closest('.ksp-slide').find('.ksp-layers .ksp-slide-editing-area');
            
            if(btn.val() == '0') {
                area.css('background-color', '#fff');
            }
            else {
                var color_picker_value = btn.closest('.ksp-content').find('.wp-color-result').css('background-color');
                area.css('background-color', color_picker_value);
            }
        });
        
        // Set background image (none or image)
        $('.ksp-admin #ksp-slides').on('change', '.ksp-slides-list .ksp-slide-settings-list input[name="ksp-slide-background_type_image"]:radio', function() {
            var btn = $(this);
            var area = btn.closest('.ksp-slide').find('.ksp-slide-editing-area');
            
            if(btn.val() == '0') {
                area.css('background-image', 'none');
            } else {
                var slide_parent = $(this).closest('.ksp-slide');
                ksp_add_slide_image_background(slide_parent);
            }
        });
        
        // Set Background image (the upload function)
        $('.ksp-admin #ksp-slides').on('click', '.ksp-slides-list .ksp-slide-settings-list .ksp-slide-background_type_image-upload-button', function() {
            var btn = $(this);
            if(btn.closest('.ksp-content').find('input[name="ksp-slide-background_type_image"]:checked').val() == '1') {
                var slide_parent = $(this).closest('.ksp-slide');
                ksp_add_slide_image_background(slide_parent);
            }
        });
        function ksp_add_slide_image_background(slide_parent) {
            var area = slide_parent.find('.ksp-slide-editing-area');
            
            // Upload
            var file_frame;

            // If the media frame already exists, reopen it.
            if ( file_frame ) {
              file_frame.open();
              return;
            }

            // Create the media frame.
            file_frame = wp.media.frames.file_frame = wp.media({
              title: jQuery( this ).data( 'uploader_title' ),
              button: {
                text: jQuery( this ).data( 'uploader_button_text' ),
              },
              multiple: false  // Set to true to allow multiple files to be selected
            });

            // When an image is selected, run a callback.
            file_frame.on( 'select', function() {
              // We set multiple to false so only get one image from the uploader
              attachment = file_frame.state().get('selection').first().toJSON();

              // Do something with attachment.id and/or attachment.url here
              var image_src = attachment.url;
              var image_alt = attachment.alt;
              
              // Set background
              area.css('background-image', 'url("' + image_src + '")');
              // I add a data with the src because, is not like images (when there is only the src link), the background contains the url('') string that is very annoying when we will get the content
              area.data('background-image-src', image_src);
            });

            // Finally, open the modal
            file_frame.open();  
        }
        // babckground MP4
        $('.ksp-admin #ksp-slides').on('click', '.ksp-slides-list .ksp-slide-settings-list .ksp-slide-background_type_mp4-upload-button', function() {
                var slide_parent = $(this).closest('.ksp-slide');
                ksp_add_slide_mp4_background(slide_parent);
        });
        function ksp_add_slide_mp4_background(slide_parent) {
           	var input_source = slide_parent.find('.ksp-video-html5-options-row .ksp-slide-background-mp4_source');
            // Upload
            var file_frame;

            // If the media frame already exists, reopen it.
            if ( file_frame ) {
              file_frame.open();
              return;
            }

            // Create the media frame.
            file_frame = wp.media.frames.file_frame = wp.media({
              title: jQuery( this ).data( 'uploader_title' ),
              button: {
                text: jQuery( this ).data( 'uploader_button_text' ),
              },
              multiple: false  // Set to true to allow multiple files to be selected
            });

            // When an image is selected, run a callback.
            file_frame.on( 'select', function() {
              // We set multiple to false so only get one image from the uploader
              attachment = file_frame.state().get('selection').first().toJSON();

              // Do something with attachment.id and/or attachment.url here
              var video_src = attachment.url;
              
              // Set background
              input_source.val(video_src);
            });

            // Finally, open the modal
            file_frame.open();  
        }
        // babckground webm
        $('.ksp-admin #ksp-slides').on('click', '.ksp-slides-list .ksp-slide-settings-list .ksp-slide-background_type_webm-upload-button', function() {
                var slide_parent = $(this).closest('.ksp-slide');
                ksp_add_slide_webm_background(slide_parent);
        });
        function ksp_add_slide_webm_background(slide_parent) {
           	var input_source = slide_parent.find('.ksp-video-html5-options-row .ksp-slide-background-webm_source');
            // Upload
            var file_frame;

            // If the media frame already exists, reopen it.
            if ( file_frame ) {
              file_frame.open();
              return;
            }

            // Create the media frame.
            file_frame = wp.media.frames.file_frame = wp.media({
              title: jQuery( this ).data( 'uploader_title' ),
              button: {
                text: jQuery( this ).data( 'uploader_button_text' ),
              },
              multiple: false  // Set to true to allow multiple files to be selected
            });

            // When an image is selected, run a callback.
            file_frame.on( 'select', function() {
              // We set multiple to false so only get one image from the uploader
              attachment = file_frame.state().get('selection').first().toJSON();

              // Do something with attachment.id and/or attachment.url here
              var video_src = attachment.url;
              
              // Set background
              input_source.val(video_src);
            });

            // Finally, open the modal
            file_frame.open();  
        }
        // Background propriety: repeat or no-repeat
        $('.ksp-admin #ksp-slides').on('change', '.ksp-slides-list .ksp-slide-settings-list input[name="ksp-slide-background_repeat"]:radio', function() {
            var btn = $(this);
            var area = btn.closest('.ksp-slide').find('.ksp-layers .ksp-slide-editing-area');
            if(btn.val() == '0') {
                area.css('background-repeat', 'no-repeat');
            }
            else {
                area.css('background-repeat', 'repeat');
            }
        });
        
        // Background propriety: position
        $('.ksp-admin #ksp-slides').on('change', '.ksp-slides-list .ksp-slide-settings-list .ksp-slide-background_propriety_position', function() {
            var val = $(this).val();
            var area = $(this).closest('.ksp-slide').find('.ksp-layers .ksp-slide-editing-area');

            area.css('background-position', val);     
        });
        
        // Background propriety: size
        $('.ksp-admin #ksp-slides').on('change', '.ksp-slides-list .ksp-slide-settings-list .ksp-slide-background_propriety_size', function() {
            var val = $(this).val();
            var area = $(this).closest('.ksp-slide').find('.ksp-layers .ksp-slide-editing-area');

            area.css('background-size', val);       
        });
         
        
        /**************/
        /** layers **/
        /**************/
        
        // GENERAL
        
        // Make draggable
        function ksp_draggable_layers() {
            $('.ksp-admin .ksp-layers .ksp-layer-wrap').draggable({
                'containment' : 'parent',
                
                start: function() {
                    // Select when dragging
                    ksp_select_layer($(this));
                },
                
                drag: function(){
                    // Set left and top positions on drag to the textbox
                    var position = $(this).position();
                    var left = position.left;
                    var top = position.top;
                    var index = $(this).index();
                    
                    $(this).closest('.ksp-layers').find('.ksp-layers-list .ksp-layer-settings:eq(' + index + ') .ksp-layer-data_x').val(left);
                    $(this).closest('.ksp-layers').find('.ksp-layers-list .ksp-layer-settings:eq(' + index + ') .ksp-layer-data_y').val(top);
                },
            });
        }
        
        // Selects an layer, shows its options and makes the delete layer button available
        $('.ksp-admin #ksp-slides').on('click', '.ksp-slide .ksp-layers .ksp-slide-editing-area .ksp-layer-wrap', function(e) {
            // Do not click the editing-area
            e.stopPropagation();
            
            // Do not open links
            e.preventDefault();
            
            ksp_select_layer($(this));
        });
        function ksp_select_layer(layer) {
            var index = layer.index();
            var slide = layer.closest('.ksp-slide');       
            var options = slide.find('.ksp-layers .ksp-layers-list');
            
            // Hide all options - .active class
            options.find('.ksp-layer-settings').css('display', 'none');
            options.find('.ksp-layer-settings').removeClass('active');
            
            // Show the correct options + .active class
            options.find('.ksp-layer-settings:eq(' + index + ')').css('display', 'block');
            options.find('.ksp-layer-settings:eq(' + index + ')').addClass('active');
            
            // Add .active class to the layer in the editing area
            layer.parent().children().removeClass('active');
            layer.addClass('active');
            
            // Make the delete and the duplicate buttons working
            slide.find('.ksp-layers-actions .ksp-delete-layer').removeClass('ksp-is-disabled');
            slide.find('.ksp-layers-actions .ksp-duplicate-layer').removeClass('ksp-is-disabled');
        }
        
        // Deselect layers
        $('.ksp-admin').on('click', '.ksp-slide .ksp-layers .ksp-slide-editing-area', function() {
            ksp_deselect_layer();
        });
        function ksp_deselect_layer() {
            $('.ksp-admin .ksp-slide .ksp-layers .ksp-slide-editing-area .ksp-layer-wrap').removeClass('active');
            $('.ksp-admin .ksp-slide .ksp-layers .ksp-layers-list .ksp-layer-settings').removeClass('active');     
            $('.ksp-admin .ksp-slide .ksp-layers .ksp-layers-list .ksp-layer-settings').css('display', 'none');        
            
            // Hide delete and duplicate layer btns
            $('.ksp-admin .ksp-slide .ksp-layers-actions .ksp-delete-layer').addClass('ksp-is-disabled');
            $('.ksp-admin .ksp-slide .ksp-layers-actions .ksp-duplicate-layer').addClass('ksp-is-disabled');
        }
        
        // Delete layer. Remember that the button should be enabled / disabled somewhere else
        function ksp_delete_layer(layer) {
            var index = layer.index();
            var slide_parent = layer.closest('.ksp-slide');
            
            layer.remove();
            var layer_options = slide_parent.find('.ksp-layers-list .ksp-layer-settings:eq(' + index + ')');
            layer_options.remove();
            ksp_deselect_layer();
        }
        $('.ksp-admin #ksp-slides').on('click', '.ksp-slide .ksp-layers .ksp-layers-actions .ksp-delete-layer', function() {
            // Click only if an layer is selected
            if($(this).hasClass('.ksp-is-disabled')) {
                return;
            }
            
            var slide_parent = $(this).closest('.ksp-slide');
            var layer = slide_parent.find('.ksp-layers .ksp-slide-editing-area .ksp-layer-wrap.active');
            ksp_delete_layer(layer);
        });
        
        function ksp_duplicate_layer(layer) {
            var index = layer.index();
            var slide_parent = layer.closest('.ksp-slide');
            
            layer.clone().appendTo(layer.parent());
            var layer_options = slide_parent.find('.ksp-layers-list .ksp-layer-settings').eq(index);
            layer_options.clone().insertBefore(layer_options.parent().find('.ksp-void-text-layer-settings'));
            
            ksp_deselect_layer();
            ksp_select_layer(layer.parent().find('.ksp-layer-wrap').last());
            
            // Clone fixes (Google "jQuery clone() bug")
            var cloned_options = layer.parent().find('.ksp-layer-wrap').last().closest('.ksp-slide').find('.ksp-layers-list .ksp-layer-settings.active');
            
            cloned_options.find('.ksp-layer-data_in').val(layer_options.find('.ksp-layer-data_in').val());
            cloned_options.find('.ksp-layer-data_out').val(layer_options.find('.ksp-layer-data_out').val());
            cloned_options.find('.ksp-layer-custom_css').val(layer_options.find('.ksp-layer-custom_css').val());            
            if(layer_options.hasClass('ksp-image-layer-settings')) {
                cloned_options.find('.ksp-image-layer-upload-button').data('src', layer_options.find('.ksp-image-layer-upload-button').data('src'));    
                cloned_options.find('.ksp-image-layer-upload-button').data('alt', layer_options.find('.ksp-image-layer-upload-button').data('alt'));    
            }
            
            // Make draggable
            ksp_draggable_layers();
        }
        ksp_layer_color_picker();
        
        // Run background color picker
        function ksp_layer_color_picker() {
            $('.ksp-admin #ksp-slides .ksp-slides-list .ksp-layer-settings-list .ksp-color-wp-color-picker').each(function() {
                $(this).wpColorPicker({
                    change: function(event, ui){
                        var index = $(this).closest('.ksp-layer-settings').index();
                        $(this).closest('.ksp-layers').find('.ksp-slide-editing-area .ksp-layer-wrap:eq(' + index + ') .ksp-layer').css('color', ui.color.toString());
                    }
                });
            });
        }

        $('.ksp-admin #ksp-slides').on('click', '.ksp-slide .ksp-layers .ksp-layers-actions .ksp-duplicate-layer', function() {
            // Click only if an layer is selected
            if($(this).hasClass('.ksp-is-disabled')) {
                return;
            }
            
            var slide_parent = $(this).closest('.ksp-slide');
            var layer = slide_parent.find('.ksp-layers .ksp-slide-editing-area .ksp-layer-wrap.active');
            ksp_duplicate_layer(layer);
        });
        
        // Modify left position
        $('.ksp-admin').on('keyup mouseup', '.ksp-layers .ksp-layers-list .ksp-layer-settings .ksp-layer-data_x', function() {
            var index = $(this).closest('.ksp-layer-settings').index();
            $(this).closest('.ksp-layers').find('.ksp-slide-editing-area .ksp-layer-wrap:eq(' + index + ')').css('left', parseFloat($(this).val()));
        });
        // Center horizontally
        $('.ksp-admin').on('click', '.ksp-layers .ksp-layers-list .ksp-layer-settings .ksp-layer-center-x', function() {
            var index = $(this).closest('.ksp-layer-settings').index();
            var left = parseInt(($('#ksp-slider-settings .ksp-settings-table #ksp-slider-maxWidth').val() / 2) - (parseFloat($(this).closest('.ksp-layers').find('.ksp-slide-editing-area .ksp-layer-wrap:eq(' + index + ')').width()) / 2));
            $(this).closest('.ksp-layers').find('.ksp-slide-editing-area .ksp-layer-wrap:eq(' + index + ')').css('left', left);
            $(this).closest('.ksp-layers').find('.ksp-layers-list .ksp-layer-settings:eq(' + index + ') .ksp-layer-data_x').val(left);
        });
        // Modify top position
        $('.ksp-admin').on('keyup mouseup', '.ksp-layers .ksp-layers-list .ksp-layer-settings .ksp-layer-data_y', function() {
            var index = $(this).closest('.ksp-layer-settings').index();
            $(this).closest('.ksp-layers').find('.ksp-slide-editing-area .ksp-layer-wrap:eq(' + index + ')').css('top', parseFloat($(this).val()));
        });
        // Center vertically
        $('.ksp-admin').on('click', '.ksp-layers .ksp-layers-list .ksp-layer-settings .ksp-layer-center-y', function() {
            var index = $(this).closest('.ksp-layer-settings').index();
            var top = parseInt(($('#ksp-slider-settings .ksp-settings-table #ksp-slider-maxHeight').val() / 2) - (parseFloat($(this).closest('.ksp-layers').find('.ksp-slide-editing-area .ksp-layer-wrap:eq(' + index + ')').height()) / 2));
            $(this).closest('.ksp-layers').find('.ksp-slide-editing-area .ksp-layer-wrap:eq(' + index + ')').css('top', top);
            $(this).closest('.ksp-layers').find('.ksp-layers-list .ksp-layer-settings:eq(' + index + ') .ksp-layer-data_y').val(top);
        });
        
        // Modify z-index
        $('.ksp-admin').on('keyup mouseup', '.ksp-layers .ksp-layers-list .ksp-layer-settings .ksp-layer-z_index', function() {
            var index = $(this).closest('.ksp-layer-settings').index();
            $(this).closest('.ksp-layers').find('.ksp-slide-editing-area .ksp-layer-wrap:eq(' + index + ')').css('z-index', parseFloat($(this).val()));
        });
        // Font Size
        $('.ksp-admin').on('keyup mouseup', '.ksp-layers .ksp-layers-list .ksp-layer-settings .ksp-layer-font_size', function() {
            var index = $(this).closest('.ksp-layer-settings').index();
            $(this).closest('.ksp-layers').find('.ksp-slide-editing-area .ksp-layer-wrap:eq(' + index + ') .ksp-layer').css('font-size', parseFloat($(this).val()));
        });
        // Line Height
        $('.ksp-admin').on('keyup mouseup', '.ksp-layers .ksp-layers-list .ksp-layer-settings .ksp-layer-line_height', function() {
            var index = $(this).closest('.ksp-layer-settings').index();
            $(this).closest('.ksp-layers').find('.ksp-slide-editing-area .ksp-layer-wrap:eq(' + index + ') .ksp-layer').css('line-height', parseFloat($(this).val()) + 'px');
        });
        // Letting Spacing
        $('.ksp-admin').on('keyup mouseup', '.ksp-layers .ksp-layers-list .ksp-layer-settings .ksp-layer-letter_spacing', function() {
            var index = $(this).closest('.ksp-layer-settings').index();
            $(this).closest('.ksp-layers').find('.ksp-slide-editing-area .ksp-layer-wrap:eq(' + index + ') .ksp-layer').css('letter-spacing', parseFloat($(this).val()) + 'px');
        });
        // Font Family and Weight
        $('.ksp-admin').on('change', '.ksp-layers .ksp-layers-list .ksp-layer-settings .ksp-layer-font', function() {
            var index = $(this).closest('.ksp-layer-settings').index();
            $(this).closest('.ksp-layers').find('.ksp-slide-editing-area .ksp-layer-wrap:eq(' + index + ') .ksp-layer').css('font-family', $(this).find(':selected').data('family'));
            $(this).closest('.ksp-layers').find('.ksp-slide-editing-area .ksp-layer-wrap:eq(' + index + ') .ksp-layer').css('font-weight', parseFloat($(this).find(':selected').data('weight')));
            $(this).closest('.ksp-layers').find('.ksp-slide-editing-area .ksp-layer-wrap:eq(' + index + ') .ksp-layer').css('font-style', $(this).find(':selected').data('style'));
        });
        // Modify shaddow
        $('.ksp-admin').on('change', '.ksp-layers .ksp-layers-list .ksp-layer-settings .ksp-layer-text_shadow', function() {
            var number = $(this).closest('.ksp-layer-settings').index();
            $(this).closest('.ksp-layers').find('.ksp-slide-editing-area .ksp-layer-wrap:eq(' + number + ') .ksp-layer').removeClass(function (index, css) {
		        return (css.match(/(^|\s)kt-t-shadow\S+/g) || []).join(' ');
		    });
            $(this).closest('.ksp-layers').find('.ksp-slide-editing-area .ksp-layer-wrap:eq(' + number + ') .ksp-layer').addClass('kt-t-shadow-'+$(this).val());
        });
        
        // TEXT layers
        
        // Add text click
        $('.ksp-admin #ksp-slides').on('click', '.ksp-slide .ksp-layers .ksp-layers-actions .ksp-add-text-layer', function() {
            var slide_parent = $(this).closest('.ksp-slide');
            ksp_add_text_layer(slide_parent);
        });
        
        // Add text. Receives the slide as object
        function ksp_add_text_layer(slide_parent) {
            var area = slide_parent.find('.ksp-slide-editing-area');
            var settings_div = slide_parent.find('.ksp-layers .ksp-layers-list .ksp-void-text-layer-settings');
            var settings = '<div class="ksp-layer-settings ksp-text-layer-settings">' + $('.ksp-admin .ksp-slide .ksp-layers .ksp-void-text-layer-settings').html() + '</div>';
            
            // Insert in editing area
            area.append('<div class="ksp-layer-wrap" style="z-index: 1;"><div class="ksp-layer ksp-text-layer">' + ksp_translations.text_layer_default_html + '</div></div>');
            
            // Insert the options
            settings_div.before(settings);
            
            // Make draggable
            ksp_draggable_layers();
            
            // Display settings
            ksp_select_layer(area.find('.ksp-layer-wrap').last());


            var index = slide_parent.find('.ksp-text-layer-settings.active').index();
            var $item = area.find('.ksp-layer').last();
            var top = parseInt(($('#ksp-slider-settings .ksp-settings-table #ksp-slider-maxHeight').val() / 2) - (parseFloat($item.closest('.ksp-layers').find('.ksp-slide-editing-area .ksp-layer-wrap:eq(' + index  + ')').height()) / 2));
            area.find('.ksp-layer').last().css('top', top);
            $item.closest('.ksp-layers').find('.ksp-slide-editing-area .ksp-layer-wrap:eq(' + index + ')').css('top', top);
            $item.closest('.ksp-layers').find('.ksp-layers-list .ksp-layer-settings:eq(' + index + ') .ksp-layer-data_y').val(top);
            var left = parseInt(($('#ksp-slider-settings .ksp-settings-table #ksp-slider-maxWidth').val() / 2) - (parseFloat($item.closest('.ksp-layers').find('.ksp-slide-editing-area .ksp-layer-wrap:eq(' + index  + ')').width()) / 2));
            $item.closest('.ksp-layers').find('.ksp-slide-editing-area .ksp-layer-wrap:eq(' + index  + ')').css('left', left);
            $item.closest('.ksp-layers').find('.ksp-layers-list .ksp-layer-settings:eq(' + index  + ') .ksp-layer-data_x').val(left);
            $item.closest('.ksp-layers').find('.ksp-layers-list .ksp-layer-settings.active .ksp-layer-font_color').wpColorPicker({
                    change: function(event, ui){
                        var index = $(this).closest('.ksp-layer-settings').index();
                        $(this).closest('.ksp-layers').find('.ksp-slide-editing-area .ksp-layer-wrap:eq(' + index + ') .ksp-layer').css('color', ui.color.toString());
                    }
                });
        }
        
        // Modify text
        $('.ksp-admin').on('keyup', '.ksp-layers .ksp-layers-list .ksp-layer-settings .ksp-layer-inner_html', function() {
            var index = $(this).closest('.ksp-layer-settings').index();
            var text_layer = $(this).closest('.ksp-layers').find('.ksp-slide-editing-area .ksp-layer-wrap:eq(' + index + ') .ksp-layer');
            
            if(! text_layer.is('a')) {
                text_layer.html($(this).val());
            }
            else {
                text_layer.find('> div').html($(this).val());
            }
        });

         // Button layers
        
        // Add text click
        $('.ksp-admin #ksp-slides').on('click', '.ksp-slide .ksp-layers .ksp-layers-actions .ksp-add-button-layer', function() {
            var slide_parent = $(this).closest('.ksp-slide');
            ksp_add_button_layer(slide_parent);
        });
        
        // Add text. Receives the slide as object
        function ksp_add_button_layer(slide_parent) {
            var area = slide_parent.find('.ksp-slide-editing-area');
            var settings_div = slide_parent.find('.ksp-layers .ksp-layers-list .ksp-void-text-layer-settings');
            var settings = '<div class="ksp-layer-settings ksp-button-layer-settings">' + $('.ksp-admin .ksp-slide .ksp-layers .ksp-void-button-layer-settings').html() + '</div>';
            // Insert in editing area
            area.append('<div class="ksp-layer-wrap" style="z-index: 1;"><div class="ksp-layer ksp-button-layer" data-color="#ffffff" data-border-color="#000000" data-background-color="#000000" data-hcolor="#ffffff" data-hborder-color="#444444" data-hbackground-color="#444444">' + ksp_translations.button_layer_default_html + '</div></div>');
            
            // Insert the options
            settings_div.before(settings);
            
            // Make draggable
            ksp_draggable_layers();

            
            // Display settings
            ksp_select_layer(area.find('.ksp-layer-wrap').last());


            var index = slide_parent.find('.ksp-button-layer-settings.active').index();
            var $item = area.find('.ksp-layer').last();
            var top = parseInt(($('#ksp-slider-settings .ksp-settings-table #ksp-slider-maxHeight').val() / 2) - (parseFloat($item.closest('.ksp-layers').find('.ksp-slide-editing-area .ksp-layer-wrap:eq(' + index  + ')').height()) / 2));
            area.find('.ksp-layer').last().css('top', top);
            $item.closest('.ksp-layers').find('.ksp-slide-editing-area .ksp-layer-wrap:eq(' + index + ')').css('top', top);
            $item.closest('.ksp-layers').find('.ksp-layers-list .ksp-layer-settings:eq(' + index + ') .ksp-layer-data_y').val(top);
            var left = parseInt(($('#ksp-slider-settings .ksp-settings-table #ksp-slider-maxWidth').val() / 2) - (parseFloat($item.closest('.ksp-layers').find('.ksp-slide-editing-area .ksp-layer-wrap:eq(' + index  + ')').width()) / 2));
            $item.closest('.ksp-layers').find('.ksp-slide-editing-area .ksp-layer-wrap:eq(' + index  + ')').css('left', left);
            $item.closest('.ksp-layers').find('.ksp-layers-list .ksp-layer-settings:eq(' + index  + ') .ksp-layer-data_x').val(left);
            $item.closest('.ksp-layers').find('.ksp-layers-list .ksp-layer-settings.active .ksp-layer-font_color').wpColorPicker({
                    change: function(event, ui){
                        var index = $(this).closest('.ksp-layer-settings').index();
                        var layer = $(this).closest('.ksp-layers').find('.ksp-slide-editing-area .ksp-layer-wrap:eq(' + index + ') .ksp-layer');
                        layer.css('color', ui.color.toString());
                        layer.attr('data-color', ui.color.toString());
                        layer.mouseout(function() {
                            $(this).css('color', ui.color.toString());
                        });
                    }
            });
            $item.closest('.ksp-layers').find('.ksp-layers-list .ksp-layer-settings.active .ksp-layer-background_color').wpColorPicker({
                    change: function(event, ui){
                        var index = $(this).closest('.ksp-layer-settings').index();
                        var layer = $(this).closest('.ksp-layers').find('.ksp-slide-editing-area .ksp-layer-wrap:eq(' + index + ') .ksp-layer');
                        layer.css('background', ui.color.toString());
                        layer.attr('data-background-color', ui.color.toString());
                        layer.mouseout(function() {
                            $(this).css('background', ui.color.toString());
                        });
                    }
            });
            $item.closest('.ksp-layers').find('.ksp-layers-list .ksp-layer-settings.active .ksp-layer-border_color').wpColorPicker({
                    change: function(event, ui){
                        var index = $(this).closest('.ksp-layer-settings').index();
                        var layer = $(this).closest('.ksp-layers').find('.ksp-slide-editing-area .ksp-layer-wrap:eq(' + index + ') .ksp-layer');
                        layer.css('border-color', ui.color.toString());
                        layer.attr('data-border-color', ui.color.toString());
                        layer.mouseout(function() {
                            $(this).css('border-color', ui.color.toString());
                        });
                    }
            });
            $item.closest('.ksp-layers').find('.ksp-layers-list .ksp-layer-settings.active .ksp-layer-font_hover_color').wpColorPicker({
                    change: function(event, ui){
                        var index = $(this).closest('.ksp-layer-settings').index();
                        var layer = $(this).closest('.ksp-layers').find('.ksp-slide-editing-area .ksp-layer-wrap:eq(' + index + ') .ksp-layer');
                        layer.attr('data-hcolor', ui.color.toString());
                        layer.mouseover(function() {
                            $(this).css('color', ui.color.toString());
                        });
                    }
            });
            $item.closest('.ksp-layers').find('.ksp-layers-list .ksp-layer-settings.active .ksp-layer-border_hover_color').wpColorPicker({
                    change: function(event, ui){
                        var index = $(this).closest('.ksp-layer-settings').index();
                        var layer = $(this).closest('.ksp-layers').find('.ksp-slide-editing-area .ksp-layer-wrap:eq(' + index + ') .ksp-layer');
                        layer.attr('data-hborder-color', ui.color.toString());
                        layer.mouseover(function() {
                            $(this).css('border-color', ui.color.toString());
                        });
                    }
            });
            $item.closest('.ksp-layers').find('.ksp-layers-list .ksp-layer-settings.active .ksp-layer-background_hover_color').wpColorPicker({
                    change: function(event, ui){
                        var index = $(this).closest('.ksp-layer-settings').index();
                        var layer = $(this).closest('.ksp-layers').find('.ksp-slide-editing-area .ksp-layer-wrap:eq(' + index + ') .ksp-layer');
                        layer.attr('data-hbackground-color', ui.color.toString());
                        layer.mouseover(function() {
                            $(this).css('background', ui.color.toString());
                        });
                    }
            });
            $item.mouseenter(function() {
                $(this).css('color', $(this).data('hcolor'));
                $(this).css('border-color', $(this).data('hborder-color'));
                $(this).css('background', $(this).data('hbackground-color'));
            });
             $item.mouseleave(function() {
                $(this).css('color', $(this).data('color'));
                $(this).css('border-color', $(this).data('border-color'));
                $(this).css('background', $(this).data('background-color'));
            });
        }
        ksp_button_color_picker();
        
        // Run background color picker
        function ksp_button_color_picker() {
            //color
            $('.ksp-admin #ksp-slides .ksp-slides-list .ksp-layer-settings-list .ksp-bcolor-wp-color-picker').each(function() {
                $(this).wpColorPicker({
                    change: function(event, ui){
                        var index = $(this).closest('.ksp-layer-settings').index();
                        var layer = $(this).closest('.ksp-layers').find('.ksp-slide-editing-area .ksp-layer-wrap:eq(' + index + ') .ksp-layer');
                        layer.css('color', ui.color.toString());
                        layer.mouseout(function() {
                            $(this).css('color', ui.color.toString());
                        });
                    }
                });
            });
            // color hover
            $('.ksp-admin #ksp-slides .ksp-slides-list .ksp-layer-settings-list .ksp-hcolor-wp-color-picker').each(function() {
                $(this).wpColorPicker({
                    change: function(event, ui){
                        var index = $(this).closest('.ksp-layer-settings').index();
                        var layer = $(this).closest('.ksp-layers').find('.ksp-slide-editing-area .ksp-layer-wrap:eq(' + index + ') .ksp-layer');
                        layer.attr('data-hcolor', ui.color.toString());
                        layer.mouseover(function() {
                            $(this).css('color', ui.color.toString());
                        });
                    }
                });
            });
            // background
            $('.ksp-admin #ksp-slides .ksp-slides-list .ksp-layer-settings-list .ksp-background-wp-color-picker').each(function() {
                $(this).wpColorPicker({
                    change: function(event, ui){
                        var index = $(this).closest('.ksp-layer-settings').index();
                        var layer = $(this).closest('.ksp-layers').find('.ksp-slide-editing-area .ksp-layer-wrap:eq(' + index + ') .ksp-layer');
                        layer.css('background', ui.color.toString());
                        layer.mouseout(function() {
                            $(this).css('background', ui.color.toString());
                        });
                    }
                });
            });
            // background hover
            $('.ksp-admin #ksp-slides .ksp-slides-list .ksp-layer-settings-list .ksp-hbackground-wp-color-picker').each(function() {
                $(this).wpColorPicker({
                    change: function(event, ui){
                        var index = $(this).closest('.ksp-layer-settings').index();
                        var layer = $(this).closest('.ksp-layers').find('.ksp-slide-editing-area .ksp-layer-wrap:eq(' + index + ') .ksp-layer');
                        layer.attr('data-hbackground-color', ui.color.toString());
                        layer.mouseover(function() {
                            $(this).css('background', ui.color.toString());
                        });
                    }
                });
            });
            $('.ksp-admin #ksp-slides .ksp-slides-list .ksp-layer-settings-list .ksp-border-wp-color-picker').each(function() {
                $(this).wpColorPicker({
                    change: function(event, ui){
                        var index = $(this).closest('.ksp-layer-settings').index();
                        var layer = $(this).closest('.ksp-layers').find('.ksp-slide-editing-area .ksp-layer-wrap:eq(' + index + ') .ksp-layer');
                        layer.css('border-color', ui.color.toString());
                        layer.mouseout(function() {
                            $(this).css('border-color', ui.color.toString());
                        });
                    }
                });
            });
            $('.ksp-admin #ksp-slides .ksp-slides-list .ksp-layer-settings-list .ksp-hborder-wp-color-picker').each(function() {
                $(this).wpColorPicker({
                    change: function(event, ui){
                        var index = $(this).closest('.ksp-layer-settings').index();
                        var layer = $(this).closest('.ksp-layers').find('.ksp-slide-editing-area .ksp-layer-wrap:eq(' + index + ') .ksp-layer');
                        layer.attr('data-hborder-color', ui.color.toString());
                        layer.mouseover(function() {
                            $(this).css('border-color', ui.color.toString());
                        });
                    }
                });
            });
        }

        // border width
        $('.ksp-admin').on('keyup mouseup', '.ksp-layers .ksp-layers-list .ksp-layer-settings .ksp-layer-border_width', function() {
            var index = $(this).closest('.ksp-layer-settings').index();
            $(this).closest('.ksp-layers').find('.ksp-slide-editing-area .ksp-layer-wrap:eq(' + index + ') .ksp-layer').css('border', parseFloat($(this).val()) + 'px solid');
        });
        // border radius
        $('.ksp-admin').on('keyup mouseup', '.ksp-layers .ksp-layers-list .ksp-layer-settings .ksp-layer-border_radius', function() {
            var index = $(this).closest('.ksp-layer-settings').index();
            $(this).closest('.ksp-layers').find('.ksp-slide-editing-area .ksp-layer-wrap:eq(' + index + ') .ksp-layer').css('border-radius', parseFloat($(this).val()) + 'px');
        });
        // Padding 
        $('.ksp-admin').on('keyup mouseup', '.ksp-layers .ksp-layers-list .ksp-layer-settings .ksp-layer-padding', function() {
            var index = $(this).closest('.ksp-layer-settings').index();
            $(this).closest('.ksp-layers').find('.ksp-slide-editing-area .ksp-layer-wrap:eq(' + index + ') .ksp-layer').css('padding-left', parseFloat($(this).val()) + 'px');
            $(this).closest('.ksp-layers').find('.ksp-slide-editing-area .ksp-layer-wrap:eq(' + index + ') .ksp-layer').css('padding-right', parseFloat($(this).val()) + 'px');
        });

        //button hover state 
        $( ".ksp-button-layer" ).each(function() {
            $(this).mouseover(function() {
                $(this).css('color', $(this).data('hcolor'));
                $(this).css('border-color', $(this).data('hborder-color'));
                $(this).css('background', $(this).data('hbackground-color'));
            });
            $(this).mouseout(function() {
                $(this).css('color', $(this).data('color'));
                $(this).css('border-color', $(this).data('border-color'));
                $(this).css('background', $(this).data('background-color'));
            });
        });
        
        // IMAGE layers
        
        // Add images click
        $('.ksp-admin #ksp-slides').on('click', '.ksp-slide .ksp-layers .ksp-layers-actions .ksp-add-image-layer', function() {
            var slide_parent = $(this).closest('.ksp-slide');
            ksp_add_image_layer(slide_parent);
        });
        
        // Upload click
        $('.ksp-admin').on('click', '.ksp-layers .ksp-layers-list .ksp-image-layer-settings .ksp-image-layer-upload-button', function() {
            var slide_parent = $(this).closest('.ksp-slide');
            ksp_upload_image_layer(slide_parent);
        });
        
        // Add image. Receives the slide as object
        function ksp_add_image_layer(slide_parent) {
            var area = slide_parent.find('.ksp-slide-editing-area');
            var settings_div = slide_parent.find('.ksp-layers .ksp-layers-list .ksp-void-text-layer-settings');
            var settings = '<div class="ksp-layer-settings ksp-image-layer-settings">' + $('.ksp-admin .ksp-slide .ksp-layers .ksp-void-image-layer-settings').html() + '</div>';
            
            // Temporarily insert an layer with no src and alt
            // Add the image into the editing area.
              area.append('<div class="ksp-layer-wrap" style="z-index: 1;"><img class="ksp-layer ksp-image-layer" src="" /></div>');
              
            // Insert the options
            settings_div.before(settings);
              
            // Make draggable
            ksp_draggable_layers();
                
            // Display settings
            ksp_select_layer(area.find('.ksp-layer-wrap').last());
            
            // Upload
            ksp_upload_image_layer(slide_parent);       
        }
        
        function ksp_upload_image_layer(slide_parent) {
            var area = slide_parent.find('.ksp-slide-editing-area');
            var settings_div = slide_parent.find('.ksp-layers .ksp-layers-list .ksp-void-text-layer-settings');
            var settings = '<div class="ksp-layer-settings ksp-image-layer-settings">' + $('.ksp-admin .ksp-slide .ksp-layers .ksp-void-image-layer-settings').html() + '</div>';
            
            var file_frame;

            // If the media frame already exists, reopen it.
            if ( file_frame ) {
              file_frame.open();
              return;
            }

            // Create the media frame.
            file_frame = wp.media.frames.file_frame = wp.media({
              title: jQuery( this ).data( 'uploader_title' ),
              button: {
                text: jQuery( this ).data( 'uploader_button_text' ),
              },
              multiple: false  // Set to true to allow multiple files to be selected
            });

            // When an image is selected, run a callback.
            file_frame.on( 'select', function() {
              // We set multiple to false so only get one image from the uploader
              attachment = file_frame.state().get('selection').first().toJSON();

              // Do something with attachment.id and/or attachment.url here
              var image_src = attachment.url;
              var image_alt = attachment.alt;
              var image_width = attachment.width;
              var image_height = attachment.height;
              
              // Set attributes. If is a link, do the right thing
              var image = area.find('.ksp-layer-wrap.active .ksp-image-layer').last();
              
                  image.attr('src', image_src);
                  image.attr('alt', image_alt);
                  image.css('width', image_width);
                  image.css('height', image_height);
              
              
              // Set data (will be used in the ajax call)
              settings_div.parent().find('.ksp-layer-settings.active .ksp-image-layer-upload-button').data('src', image_src);
              settings_div.parent().find('.ksp-layer-settings.active .ksp-image-layer-upload-button').data('alt', image_alt);
              settings_div.parent().find('.ksp-layer-settings.active .ksp-layer-width').val(image_width);
              settings_div.parent().find('.ksp-layer-settings.active .ksp-layer-height').val(image_height);
            });

            // Finally, open the modal
            file_frame.open();
        }
        $('.ksp-admin').on('keyup mouseup', '.ksp-layers .ksp-layers-list .ksp-layer-settings .ksp-layer-height', function() {
            var index = $(this).closest('.ksp-layer-settings').index();
            var settings = $(this).closest('.ksp-layer-settings');
            $(this).closest('.ksp-layers').find('.ksp-slide-editing-area .ksp-layer-wrap:eq(' + index + ') .ksp-layer').css('height', parseFloat($(this).val()) + 'px');
            $(this).closest('.ksp-layers').find('.ksp-slide-editing-area .ksp-layer-wrap:eq(' + index + ') .ksp-layer').css('width', 'auto');
            var new_width = $(this).closest('.ksp-layers').find('.ksp-slide-editing-area .ksp-layer-wrap:eq(' + index + ') .ksp-layer').width();
            $(this).closest('.ksp-layers').find('.ksp-slide-editing-area .ksp-layer-wrap:eq(' + index + ') .ksp-layer').css('width', new_width);
            settings.find('.ksp-layer-width').val(new_width);
        });
         $('.ksp-admin').on('keyup mouseup', '.ksp-layers .ksp-layers-list .ksp-layer-settings .ksp-layer-width', function() {
            var index = $(this).closest('.ksp-layer-settings').index();
            var settings = $(this).closest('.ksp-layer-settings');
            $(this).closest('.ksp-layers').find('.ksp-slide-editing-area .ksp-layer-wrap:eq(' + index + ') .ksp-layer').css('width', parseFloat($(this).val()) + 'px');
            $(this).closest('.ksp-layers').find('.ksp-slide-editing-area .ksp-layer-wrap:eq(' + index + ') .ksp-layer').css('height', 'auto');
            var new_height = $(this).closest('.ksp-layers').find('.ksp-slide-editing-area .ksp-layer-wrap:eq(' + index + ') .ksp-layer').height();
            $(this).closest('.ksp-layers').find('.ksp-slide-editing-area .ksp-layer-wrap:eq(' + index + ') .ksp-layer').css('height', new_height);
            settings.find('.ksp-layer-height').val(new_height);
        });

    // Save or update the new slider in the database
        $('.ksp-admin .ksp-save-settings').click(function(e) {
            ksp_saveSlider();
            e.preventDefault();
        });

        // Delete slider
        $('.ksp-admin .ksp-base .ksp-sliders-list .ksp-delete-slider').click(function() {
            var confirm = window.confirm(ksp_translations.slider_delete_confirm);
            if(!confirm) {
                return;
            }
            
            ksp_deleteSlider($(this));
        });
        // Duplicate slider
        $('.ksp-admin .ksp-base').on('click', '.ksp-sliders-list .ksp-duplicate-slider', function() {
            ksp_duplicateSlider($(this));
        });
        // Export slider
        $('.ksp-admin .ksp-base').on('click', '.ksp-sliders-list .ksp-export-slider', function() {
            ksp_exportSlider($(this));
        });
        // Import slider
        $('.ksp-admin .ksp-base').on('click', '.ksp-import-slider', function() {
            $('#ksp-import-file').trigger('click');
        });
        $('.ksp-admin .ksp-base').on('change', '#ksp-import-file', function() {
            ksp_importSlider();
        });

        // Sends an array with the new or current slider options
        function ksp_saveSlider() {
            var overlay = $(document.getElementById('kt_ajax_overlay'));
            var content = $('.ksp-slider #ksp-slider-settings');
            var options = {
                id : parseInt($('.ksp-save-settings').data('id')),
                name : content.find('#ksp-title').val(),
                maxHeight : parseInt(content.find('#ksp-slider-maxHeight').val()),
                maxWidth : parseInt(content.find('#ksp-slider-maxWidth').val()),
                fullHeight : parseInt(content.find('#ksp-slider-fullHeight').is(':checked') ? 1 : 0),
                fullWidth : parseInt(content.find('#ksp-slider-fullWidth').is(':checked') ? 1 : 0),
                full_offset : content.find('#ksp-slider-full_offset').val(),
                responsive : parseInt(content.find('#ksp-slider-responsive').is(':checked') ? 1 : 0),
                autoPlay : parseInt(content.find('#ksp-slider-autoPlay').is(':checked') ? 1 : 0),
                pauseTime : parseInt(content.find('#ksp-slider-pauseTime').val()),
                enableParallax : parseInt(content.find('#ksp-slider-enableParallax').is(':checked') ? 1 : 0),
                singleSlide : parseInt(content.find('#ksp-slider-singleSlide').is(':checked') ? 1 : 0),
                minHeight : parseInt(content.find('#ksp-slider-minHeight').val()),
                pauseonHover : parseInt(content.find('#ksp-slider-pauseonHover').is(':checked') ? 1 : 0),
            };
            overlay.fadeIn();
            // Do the ajax call
            jQuery.ajax({
                type : 'POST',
                dataType : 'json',
                url : ajaxurl,
                data : {
                    // Is it saving or updating?
                    action: $('.ksp-slider').hasClass('ksp-add-slider') ? 'ksp_addSlider' : 'ksp_editSlider',
                    datas : options,
                },
                success: function(response) {
                    //alert('Save slider response: ' + response);
                    // If adding a new slider, response will be the generated id, else will be the number of rows modified
                    if(response !== false) {
                        // If is adding a slider, redirect
                        if($('.ksp-slider').hasClass('ksp-add-slider')) {
                            window.location.href = '?page=kadenceslider&view=layeredit&id=' + response;
                            return;
                        }
                        
                        ksp_saveSlides();
                    } else {
                       ksp_showError();
                    }
                },
                
                error: function(XMLHttpRequest, textStatus, errorThrown) { 
                    alert('Error saving slider');
                    alert("Status: " + textStatus);
                    alert("Error: " + errorThrown); 
                     ksp_showError();
                }
            });
        }
        // Sends an array with all the slides options
        function ksp_saveSlides() {
            var slides = $('.ksp-slider .ksp-slide');
            var i = 0;
            var final_options = {};
            
            final_options['options'] = new Array();         
            slides.each(function() {
                var slide = $(this);
                var content = slide.find('.ksp-slide-settings-list');
                var options = {
                    slider_parent : parseInt($('.ksp-admin .ksp-save-settings').data('id')),                
                    position : i,
                    background_type_image : slide.find('.ksp-slide-editing-area').css('background-image') == 'none' ? 'none' : slide.find('.ksp-slide-editing-area').data('background-image-src') + "",
                    background_type_color : content.find('input[name="ksp-slide-background_type_color"]:checked').val() == '0' ? 'transparent' : slide.find('.ksp-slide-editing-area').css('background-color') + "",
                    background_propriety_position : content.find('.ksp-slide-background_propriety_position').val(),
                    background_repeat : content.find('input[name="ksp-slide-background_repeat"]:checked').val() == '0' ? 'no-repeat' : 'repeat',
                    background_propriety_size : content.find('.ksp-slide-background_propriety_size').val(),
                    background_type_video :  content.find('.ksp-slide-background_type_video').val(),
                    background_type_video_youtube : content.find('.ksp-slide-background_type_video_youtube').val(),
                    background_type_video_start : parseInt(content.find('.ksp-slide-background_type_video_start').val()),
                    background_type_video_ratio : content.find('.ksp-slide-background_type_video_ratio').val(),
                    background_type_video_mute : parseInt(content.find('.ksp-slider-background_type_video_mute').is(':checked') ? 1 : 0),
                    background_type_video_loop : parseInt(content.find('.ksp-slider-background_type_video_loop').is(':checked') ? 1 : 0),
                    background_type_video_playpause : parseInt(content.find('.ksp-slider-background_type_video_playpause').is(':checked') ? 1 : 0),
                    background_type_video_mp4 : content.find('.ksp-slide-background-mp4_source').val(),
                    background_type_video_webm : content.find('.ksp-slide-background-webm_source').val(),
                    background_link : content.find('.ksp-slide-background-link').val(),
                    background_link_new_tab : parseInt( content.find('.ksp-slide-link_new_tab').is(':checked') ? 1 : 0),
                };
                
                final_options['options'][i] = options;
                
                i++;
            });
            
            final_options['slider_parent'] = parseInt($('.ksp-admin .ksp-save-settings').data('id')),
            
            // Do the ajax call
            jQuery.ajax({
                type : 'POST',
                dataType : 'json',
                url : ajaxurl,
                data : {
                    action: 'ksp_editSlides',
                    datas : final_options,
                },
                success: function(response) {
                    //alert('Save slides response: ' + response);
                    if(response !== false) {
                        ksp_save_layers();
                        //ksp_showSuccess();
                    }
                    else {
                        ksp_showError();
                    }
                },
                
                error: function(XMLHttpRequest, textStatus, errorThrown) { 
                    alert('Error saving slides');
                    alert("Status: " + textStatus);
                    alert("Error: " + errorThrown); 
                    ksp_showError();
                }
            });
        }
        
        // Sends an array with all the layers options of each slide
        function ksp_save_layers() {
            var slides = $('.ksp-admin .ksp-slider #ksp-slides .ksp-slide');
            var i = 0, j = 0;
            var final_options = {};
            
            final_options['options'] = new Array();
            slides.each(function() {
                var slide = $(this);
                var layers = slide.find('.ksp-layers .ksp-layer-settings');
                
                layers.each(function() {
                    var layer = $(this);
                    
                    // Stop each loop when reach the void layer
                    if(layer.hasClass('ksp-void-layer-settings')) {
                        return;
                    }
                    var type;
                    if(layer.hasClass('ksp-text-layer-settings')) {
                        type = 'text';
                    } else if(layer.hasClass('ksp-image-layer-settings')) {
                        type = 'image';
                    } else if(layer.hasClass('ksp-button-layer-settings')) {
                        type = 'button';
                    } else {
                        type = 'undefined';
                    }
                    
                    var options = {
                        slider_parent : parseInt($('.ksp-admin .ksp-save-settings').data('id')),  
                        slide_parent : i,   
                        position : layer.index(),
                        type : type,
                        
                        inner_html : layer.hasClass('ksp-text-layer-settings') || layer.hasClass('ksp-button-layer-settings') ? layer.find('.ksp-layer-inner_html').val() : '',
                        image_src : layer.hasClass('ksp-image-layer-settings') ? layer.find('.ksp-image-layer-upload-button').data('src') : '',
                        image_alt : layer.hasClass('ksp-image-layer-settings') ? layer.find('.ksp-image-layer-upload-button').data('alt') : '',
                        width : layer.hasClass('ksp-image-layer-settings') ? parseInt(layer.find('.ksp-layer-width').val()) : '',
                        height : layer.hasClass('ksp-image-layer-settings') ? parseInt(layer.find('.ksp-layer-height').val()) : '',
                        font_color : layer.hasClass('ksp-text-layer-settings') || layer.hasClass('ksp-button-layer-settings') ? layer.find('.ksp-layer-font_color').val() : '',
                        background_color : layer.hasClass('ksp-button-layer-settings') ? layer.find('.ksp-layer-background_color').val() : '',
                        border_color : layer.hasClass('ksp-button-layer-settings') ? layer.find('.ksp-layer-border_color').val() : '',
                        font_hover_color : layer.hasClass('ksp-button-layer-settings') ? layer.find('.ksp-layer-font_hover_color').val() : '',
                        background_hover_color : layer.hasClass('ksp-button-layer-settings') ? layer.find('.ksp-layer-background_hover_color').val() : '',
                        border_hover_color : layer.hasClass('ksp-button-layer-settings') ? layer.find('.ksp-layer-border_hover_color').val() : '',
                        border_width : layer.hasClass('ksp-button-layer-settings') ? parseInt(layer.find('.ksp-layer-border_width').val()) : '',
                        border_radius : layer.hasClass('ksp-button-layer-settings') ? parseInt(layer.find('.ksp-layer-border_radius').val()) : '',
                        padding : layer.hasClass('ksp-button-layer-settings') ? parseInt(layer.find('.ksp-layer-padding').val()) : '',
                        font_size : layer.hasClass('ksp-text-layer-settings') || layer.hasClass('ksp-button-layer-settings') ? parseInt(layer.find('.ksp-layer-font_size').val()) : '',
                        line_height : layer.hasClass('ksp-text-layer-settings') || layer.hasClass('ksp-button-layer-settings') ? parseInt(layer.find('.ksp-layer-line_height').val()) : '',
                        letter_spacing : layer.hasClass('ksp-text-layer-settings') || layer.hasClass('ksp-button-layer-settings') ? parseInt(layer.find('.ksp-layer-letter_spacing').val()) : '',
                        font : layer.hasClass('ksp-text-layer-settings') || layer.hasClass('ksp-button-layer-settings') ? layer.find('.ksp-layer-font').val() : '',
                        data_x : parseInt(layer.find('.ksp-layer-data_x').val()),
                        data_y : parseInt(layer.find('.ksp-layer-data_y').val()),
                        data_delay : parseInt(layer.find('.ksp-layer-data_delay').val()),
                        data_in : layer.find('.ksp-layer-data_in').val(),
                        data_out : layer.find('.ksp-layer-data_out').val(),
                        data_ease : layer.find('.ksp-layer-data_ease').val(),
                        link : layer.find('.ksp-layer-link').val(),
                        link_new_tab : parseInt(layer.find('.ksp-layer-link_new_tab').is(':checked') ? 1 : 0),
                        z_index : parseInt(layer.find('.ksp-layer-z_index').val()),
                        text_shadow : layer.hasClass('ksp-text-layer-settings') ? layer.find('.ksp-layer-text_shadow').val() : 'none',
                    };
                    
                    final_options['options'][j] = options;
                    
                    j++;
                });
                
                i++;
            });
            
            // Proceed?
            final_options['layers'] = 1;
            if(final_options['options'].length == 0) {
                final_options['layers'] = 0;
            }
            
            final_options['slider_parent'] = parseInt($('.ksp-admin .ksp-save-settings').data('id'));
            
            final_options['options'] = JSON.stringify(final_options['options']);

            // Do the ajax call
            jQuery.ajax({
                type : 'POST',
                dataType : 'json',
                url : ajaxurl,
                data : {
                    action: 'ksp_editLayers',
                    datas : final_options,
                },
                success: function(response) {
                    if(response !== false) {
                        $('.ksp-admin').trigger( "savedSlide" );
                        ksp_showSuccess();
                    } else {
                        ksp_showError();
                    }
                },
                
                error: function(XMLHttpRequest, textStatus, errorThrown) { 
                    alert('Error saving layers');
                    console.log(XMLHttpRequest.responseText);
                    ksp_showError();
                }
            });
        }
        function ksp_deleteSlider(content) {
            // Get options
            var options = {
                id : parseInt(content.data('delete')),
            };
            
            // Do the ajax call
            jQuery.ajax({
                type : 'POST',
                dataType : 'json',
                url : ajaxurl,
                data : {
                    action: 'ksp_deleteSlider',
                    datas : options,
                },
                success: function(response) {
                    //alert('Delete slider response: ' + response);
                    if(response !== false) {
                        content.parent().parent().remove();
                        ksp_showSuccess();
                    }
                    else {
                        ksp_showError();
                    }
                },
                
                error: function(XMLHttpRequest, textStatus, errorThrown) { 
                    alert('Error deleting slider');
                    alert("Status: " + textStatus);
                    alert("Error: " + errorThrown); 
                    ksp_showError();
                },
            });
        }
        function ksp_duplicateSlider(content) {
            var overlay = $(document.getElementById('kt_ajax_overlay'));
            var options = {
                id : parseInt(content.data('duplicate')),
            };
            overlay.fadeIn();
            
            // Do the ajax call
            jQuery.ajax({
                type : 'POST',
                dataType : 'json',
                url : ajaxurl,
                data : {
                    action: 'ksp_duplicateSlider',
                    datas : options,
                },
                success: function(response) {
                    //console.log(response);
                    if(response['response'] !== false) {
                        var cloned_slider = content.parent().parent().clone().appendTo(content.parent().parent().parent());
                        cloned_slider.find('.ksp_column_02').html(response['cloned_slider_id']);
                        cloned_slider.find('.ksp_column_03 a').html(response['cloned_slider_name']);
                        cloned_slider.find('.ksp_column_03 a').attr('href', '?page=kadenceslider&view=layeredit&id=' + response['cloned_slider_id']);
                        cloned_slider.find('.ksp_column_04').html('[kadence_slider_pro id="' + response['cloned_slider_id'] + '"]');
                        cloned_slider.find('.ksp-edit-slider').attr('href', '?page=kadenceslider&view=layeredit&id=' + response['cloned_slider_id']);
                        cloned_slider.find('.ksp-duplicate-slider').data('duplicate', response['cloned_slider_id']);
                        cloned_slider.find('.ksp-delete-slider').data('delete', response['cloned_slider_id']);
                        cloned_slider.find('.ksp-export-slider').data('export', response['cloned_slider_id']);
                        ksp_showSuccess();
                    } else {
                        ksp_showError();
                    }
                },
                
                error: function(XMLHttpRequest, textStatus, errorThrown) { 
                    alert('Error duplicating slider');
                    console.log(XMLHttpRequest.responseText);
                    ksp_showError();
                },
            });
        }

        function ksp_exportSlider(content) {
            var overlay = $(document.getElementById('kt_ajax_overlay'));
            var options = {
                id : parseInt(content.data('export')),
            };
            overlay.fadeIn();
            
            // Do the ajax call
            jQuery.ajax({
                type : 'POST',
                dataType : 'json',
                url : ajaxurl,
                data : {
                    action: 'ksp_exportSlider',
                    datas : options,
                },
                success: function(response) {
                    if(response['response'] !== false) {                        
                        window.location.href = response['url'];
                        ksp_showSuccess();
                    }
                    else {
                        ksp_showError();
                    }
                },
                
                error: function(XMLHttpRequest, textStatus, errorThrown) { 
                    alert('Error while exporting the slider');
                    console.log(XMLHttpRequest.responseText);
                    ksp_showError();
                },
            });
        }
        function ksp_importSlider() {
            var overlay = $(document.getElementById('kt_ajax_overlay'));
            var file = $('#ksp-import-file')[0].files[0];
            
            if(! file) {
                return;
            }
            
            $('#ksp-import-file').val('');
            
            // Form data (for file uploads)
            var fd = new FormData();
            fd.append('file', file);
            fd.append('action', 'ksp_importSlider');  

            overlay.fadeIn();
            // Do the ajax call
            jQuery.ajax({
                type : 'POST',
                url : ajaxurl,
                contentType: false,
                processData : false,
                data : fd,
                success: function(response) {
                    response = JSON.parse(response);
                    //console.log(response);
                    if(response['response'] !== false) {
                        var content = $('.ksp-sliders-list .ksp-duplicate-slider:eq(0)');
                        if(content.length > 0) {
                            var imported_slider = content.parent().parent().clone().appendTo(content.parent().parent().parent());
                            imported_slider.find('.ksp_column_02').html(response['imported_slider_id']);
                            imported_slider.find('.ksp_column_03 a').html(response['imported_slider_name']);
                            imported_slider.find('.ksp_column_03 a').attr('href', '?page=ksp&view=layeredit&id=' + response['imported_slider_id']);
                            imported_slider.find('.ksp_column_04').html('[kadence_slider_pro id="' + response['imported_slider_id'] + '"]');
                            imported_slider.find('.ksp-edit-slider').attr('href', '?page=ksp&view=layeredit&id=' + response['imported_slider_id']);
                            imported_slider.find('.ksp-duplicate-slider').data('duplicate', response['imported_slider_id']);
                            imported_slider.find('.ksp-delete-slider').data('delete', response['imported_slider_id']);
                            imported_slider.find('.ksp-delete-slider').data('export', response['imported_slider_id']);
                        } else {
                            location.reload();
                        }
                        
                        ksp_showSuccess();
                    }
                    else {
                        ksp_showError();
                    }
                },
                
                error: function(XMLHttpRequest, textStatus, errorThrown) { 
                    alert('Error while importing the slider');
                    console.log(XMLHttpRequest.responseText);
                    ksp_showError();
                },
            });
        }

         $('.ksp-live-preview').magnificPopup({
            type: 'iframe',
            closeOnContentClick: false,
            iframe: {
              markup: '<div class="mfp-iframe-preview">'+
                        '<div class="mfp-close"></div>'+
                        '<iframe class="mfp-iframe" frameborder="0" allowfullscreen></iframe>'+
                      '</div>', // 
                  }
        });
    });
})(jQuery);

