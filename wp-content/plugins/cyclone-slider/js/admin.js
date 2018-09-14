/*** Wrapper module for js store ***/
var cs_local_storage = (function () {
    return {
        get: function (key) {
            if(store!=undefined){
                return store.get(key);
            }
            return false;
        },
        set: function (key, value) {
            if(store!=undefined){
                store.set(key, value);
            }
        },
        remove: function (key) {
            if(store!=undefined){
                store.remove(key);
            }
        },
        clear: function () {
            if(store!=undefined){
                store.clear(); /*** Clear all keys ***/
            }
        }
    };
})();

/*** Class for handling open and close expandable and slide elements. Use together with cs_local_storage ***/
function CsUiOpen(data){
    if(!data){
        data = {};
    }
    this.expandables = data;/*** data format should be object[slideshowId][element_index] ***/
}
CsUiOpen.prototype.get = function(slideshow, key){
    if(this.expandables[slideshow]!=undefined){
        if(this.expandables[slideshow][key]!=undefined){
            return this.expandables[slideshow][key];
        }
    }
    return false;
}
CsUiOpen.prototype.set = function(slideshow, key, value){
    if(typeof(this.expandables[slideshow])!=='object'){
        this.expandables[slideshow] = {};
    }
    
    this.expandables[slideshow][key] = value;
}
CsUiOpen.prototype.remove = function(slideshow, key){
    if(this.expandables[slideshow]!=undefined){
        if(this.expandables[slideshow][key]!=undefined){
            delete this.expandables[slideshow][key];
        }
    }
}
CsUiOpen.prototype.getAll = function(){
    return this.expandables;
}
CsUiOpen.prototype.clear = function(){
    this.expandables = {};
}


jQuery(document).ready(function($){
    /*** Export ***/
    (function() {
        $('#cs-select-all').click(function(){
            if( $(this).is(':checked') ) {
                $('.cs-sliders').prop('checked', true);
            } else {
                $('.cs-sliders').prop('checked', false);
            }
            
        });
    })();
    /*** SLIDE BOXES ***/
    (function() {
        var $slidesMetabox = $('#cyclone-slides-metabox'),
            $sortables = $('#cs-sortables'),
            slideshowId = $sortables.data('post-id'), 
            cs_ui_open;
        
        cs_ui_open = new CsUiOpen(cs_local_storage.get('cs_slide_toggles'));/*** Handle persistent slide data ***/
        
        /*** Init - Sortable slides ***/
        $sortables.sortable({
            handle:'.cs-header',
            placeholder: "cs-slide-placeholder",
            forcePlaceholderSize:true,
            disabled: true,
            /*** Update form field indexes when slide order changes ***/
            update: function(event, ui) {
                $sortables.find('.cs-slide').each(function(boxIndex, box){ /*** Loop thru each box ***/
                    $(box).find('input, select, textarea').each(function(i, field){ /*** Loop thru relevant form fields ***/
                        var name = $(field).attr('name');
                        if(name){
                            name = name.replace(/\[[0-9]+\]/, '['+boxIndex+']'); /*** Replace all [index] in field_key[index][name] ***/
                            $(field).attr('name',name);
                        }
                    });
                    $(box).find('.cs-changeling-id').each(function(i, field){ /*** Loop thru relevant fields ***/
                        var name = $(field).attr('id');
                        if(name){
                            name = name.replace(/[0-9]+/, boxIndex); /*** Replace all ad_asdasd-x ***/
                            $(field).attr('id',name);
                        }
                        var name = $(field).attr('for');
                        if(name){
                            name = name.replace(/[0-9]+/, boxIndex); /*** Replace all ad_asdasd-x ***/
                            $(field).attr('for',name);
                        }
                    });
                });
            }
        });
        $('#cs-sort').on('click', function(){
            var $sort = $(this),
                isDisabled = $( "#cs-sortables" ).sortable( "option", "disabled" );

            $sort.toggleClass('active');
            if(isDisabled){
                $('#cs-sortables').sortable('enable').addClass('active');
            } else {
                $('#cs-sortables').sortable('disable').removeClass('active');
            }
        });
        /*** Init - Slide ID and title ***/
        $sortables.find('.cs-slide').each(function(i){
            var $slide = $(this),
                $body = $slide.find('.cs-body');

            $slide.data('cs_id', i);
            
            if(cs_ui_open.get(slideshowId ,i)=='open'){
                $body.slideDown(0);
            } else {
                $body.slideUp(0);
            }
        });
        
        /*** Add - Slide box from a hidden html template ***/
        $slidesMetabox.on('click', '#cs-add-slide', function(e){
            var id = $sortables.find('.cs-slide').length;
            var html = $('#cs-slide-skeleton').html();
            html = html.replace(/{id}/g, id);/*** replace all occurences of {id} to real id ***/
            
            $sortables.append(html);
            $sortables.find('.cs-slide:last').find('.cs-thumbnail').hide().end().find('.cs-body').show();

            $sortables.find('.cs-slide').each(function(i){
                $(this).data('cs_id',i);
            });

            $('.expandable-body').each(function(i){
                $(this).data('cs_id',i);
            });
            
            $(".cycloneslider_metas_enable_slide_effects").trigger('change');
            
            e.preventDefault();
        })
        .on('wpAddImage', '.cs-media-gallery-show', function(e, image_url, attachment_id, media_attachment){
            
            /*** Add image to slide ***/

            var $button = $(this),
                $imageField = $button.closest('.cs-image-field'), // Current image field
                $thumb = $imageField.find('.cs-image-thumb'), // Find the thumb
                $hiddenField = $imageField.find('.cs-image-id '); // Find the hidden field that will hold the attachment id

            $thumb.html('<img src="'+image_url+'" alt="Thumbnail" />').show();
            $hiddenField.val(attachment_id);
 
        })
        .on('wpAddImages', '.cs-multiple-slides', function(e, media_attachments){ 

            /*** Add multiple images as slide ***/

            var $sortables = $('#cs-sortables'),
                slideCount = $sortables.find('.cs-slide').length,
                i;

            for(i=0; i<media_attachments.length; ++i){
                
                $('#cs-add-slide').trigger('click');
                
                $sortables.find('.cs-slide').eq(slideCount+i).find('.cs-media-gallery-show').trigger('wpAddImage', [media_attachments[i].url, media_attachments[i].id, media_attachments[i]]);
            }
            
        })
        .on('click',  '.cs-minimize', function(e) {

            /*** Toggle - slide body visiblity ***/

            var $button = $(this),
                $box = $button.closest('.cs-slide'),
                $body = $box.find('.cs-body'),
                id = $box.data('cs_id');
            
            if($body.is(':visible')){
                $body.slideUp(100);
                cs_ui_open.remove(slideshowId , id);
            } else {
                $body.slideDown(100);
                cs_ui_open.set(slideshowId , id, 'open');/*** remember open section ***/ 
            }
            
            cs_local_storage.set('cs_slide_toggles', cs_ui_open.getAll());
            e.preventDefault();

        }).on('click', '.cs-slide-type .switcher', function(e){

            /* Switcher - switch between slide types */

            var $switcher = $(this);

            $switcher.toggleClass('open');
            $('.cs-slide-type .switcher').not($switcher).removeClass('open');
            e.stopPropagation();

        }).on('click', '.cs-slide-type .switcher li', function(e){

            var $list = $(this),
                $box = $list.closest('.cs-slide'),
                $switcher = $list.closest('.switcher'),
                $hidden = $list.closest('.cs-slide-type').find('input'),
                $display = $switcher.find('.display');
            
            $display.html($list.html());
            $switcher.removeClass('open');
            $hidden.val($list.attr('data-value'));
            $box.attr('data-slide-type', $hidden.val());

            e.stopPropagation();
        })
        .on('click',  '.cs-delete', function(e) {
            
            /*** Delete - Remove slide box ***/

            var box = $(this).parents('.cs-slide');
            box.fadeOut('slow', function(){ box.remove()});

            e.preventDefault();
            e.stopPropagation();
        })
        .on('change', '.cycloneslider_metas_link_target', function(e){

            /*** Enable/Disable Link URL if lightbox is selected ***/
            
            var box, link_url;
            
            box = $(this).parents('.expandable-box');
            
            link_url = box.find('.cycloneslider_metas_link_url');
            
            if ($(this).val() == 'lightbox') {
                link_url.attr('disabled', 'disabled');
            } else {
                link_url.removeAttr('disabled');
            }
        })
        .find('.cs-slide').each(function(){
            var $slide = $(this),
                slideType = $slide.attr('data-slide-type');
            $slide.find('.cs-slide-type').find('li[data-value="'+slideType+'"]').trigger('click');
        });

        $(document).click(function(){

            /* Handle closing of dropdown on lost focus */

            $('.cs-slide-type .switcher').removeClass('open');
        });

        $('.cycloneslider_metas_link_target').trigger('change');
        
    })();
    
    /*** EXPANDABLES ***/
    (function() {
        var slideshowId, cs_ui_open;
        
        /*** Init ***/
        slideshowId = $('#cyclone-slides-metabox .cs-sortables').data('post-id');
        
        cs_ui_open = new CsUiOpen(cs_local_storage.get('cs_expandables'));
        
        $('#cyclone-slides-metabox .expandable-body').each(function(i){
            $(this).data('cs_id', i);
            
            if(cs_ui_open.get(slideshowId ,i)=='open'){
                $(this).slideDown(0);
            } else {
                $(this).slideUp(0);
            }
        });
        
        /*** Toggle - Expandable toggling ***/
        $('#cyclone-slides-metabox').on('click', '.expandable-header', function(e){
            var body, id;
            
            body = $(this).next('.expandable-body');
            id = body.data('cs_id');
            
            if(body.is(':visible')){
                body.slideUp(100);
                cs_ui_open.remove(slideshowId , id);
                
            } else {
                body.slideDown(100);
                cs_ui_open.set(slideshowId , id, 'open');
                
            }
            
            cs_local_storage.set('cs_expandables', cs_ui_open.getAll());
        });
    })();
    
    /*** VIDEO SLIDE ***/
    (function() {
        var slideshowId;
        
        slideshowId = $('#cyclone-slides-metabox .cs-sortables').data('post-id');
        
        /*** Get Video ***/
        $('#cyclone-slides-metabox').on('click', '.cs-video-get', function(e){
            var button, box, textbox_url, url, video_thumb, video_embed;
            
            button = $(this);
            box = $(this).parents('.cs-slide');
            video_thumb = box.find('.cs-video-thumb');
            textbox_url = box.find('.cs-video-url');
            url = textbox_url.val();
            if(url==''){
                return;
            }
            video_embed = box.find('.cs-video-embed');
            video_thumb.empty().show();
            textbox_url.attr('disabled','disabled');
            button.attr('disabled','disabled');
            
            $.ajax({
                type: "POST",
                url: ajaxurl, /*** Automatically added by wordpress ***/
                data: "action=cycloneslider_get_video&url="+encodeURIComponent(url),
                dataType: 'json',
                success: function(data, textStatus, XMLHttpRequest){
                    if(data.success){
                        video_thumb.html('<img src="'+data.url+'" alt="thumb">');
                        box.find('.cs-video-thumb-url').val(data.url);
                        video_embed.val(data.embed);
                        textbox_url.removeAttr('disabled');
                        button.removeAttr('disabled');
                    } else {
                        alert('Error. Make sure its a valid youtube or vimeo url.');
                        video_thumb.empty().hide();
                        textbox_url.removeAttr('disabled');
                        button.removeAttr('disabled');
                    }
                }
            });
        });
    })();

    (function() {

        /*** hide wordpress admin stuff ***/
        $('#minor-publishing-actions').hide();
        $('#misc-publishing-actions').hide();
        $('.inline-edit-date').prev().hide();
        
        /*** Post type switcher quick fix ***/
        $('#pts_post_type').html('<option value="cycloneslider">Cycloneslider</option>');
        
        /*** Template Chooser ***/
        $('#cyclone-slider-templates-metabox').on('click', '.boxxy', function(e){
            e.preventDefault();
            e.stopPropagation();
            
            var trigger = $(this),
                content = '',
                boxy = $('#cs-boxy'),
                width = 0,
                height = 0,
                x = 0,
                y = 0;
            
            boxy.html( trigger.data('content') );
            boxy.stop().show();
            
            /* Do calcs after element is shown to prevent zero values for hidden element */
            width = boxy.outerWidth(),
            height = boxy.outerHeight(),
            x = trigger.offset().left,
            y = trigger.offset().top,
                
            y = y - height;
            if ( $('body').hasClass('admin-bar') ) {
                y -= 32;
            }
            
            boxy.css({
                'left': x+'px',
                'top': y+'px'
            });
        }).on('change', '.cs-templates input[type="radio"]', function(e){
            var $radio = $(this),
                $tr = $(this).closest('tr'),
                $table = $tr.closest('table');

            $table.find('tr').removeClass('active');
            $tr.addClass('active');
        });
        $(document).on('click', '#cs-boxy', function(e){
            e.preventDefault();
            e.stopPropagation();
        })
        $(document).on('click', 'body', function(e){
            $('#cs-boxy').fadeOut();
        })
        $(window).resize(function(e){
            $('#cs-boxy').hide();
        })
        
        /*** show/Hide Tile Properties for slideshow ***/
        $('#cyclone-slider-properties-metabox').on('change', '#cycloneslider_settings_fx', function(){
            if($(this).val()=='tileBlind' || $(this).val()=='tileSlide'){
                $('.cycloneslider-field-tile-properties').slideDown('fast');
            } else {
                $('.cycloneslider-field-tile-properties').slideUp('fast');
            }
        });
        $("#cycloneslider_settings_fx").trigger('change');
        
        /*** Show/hide Tile Properties for slides ***/
        $('#cyclone-slides-metabox').on('change', '.cycloneslider_metas_fx', function(){
            var $select  = $(this),
                $field = $select.closest('.field');

            if($select.val()=='tileBlind' || $select.val()=='tileSlide'){
                $field.siblings('.cycloneslider-slide-tile-properties').slideDown('fast');
            } else {
                $field.siblings('.cycloneslider-slide-tile-properties').slideUp('fast');
            }
        });
        $(".cycloneslider_metas_fx").trigger('change');
        
    })();

    (function() {
        if(typeof(wp) == "undefined" || typeof(wp.media) != "function"){
            return;
        }
        // Prepare the variable that holds our custom media manager.
        var cyclone_media_frame;
        var triggering_element = null;
        
        // Bind to our click event in order to open up the new media experience.
        $(document.body).on('click', '.cs-media-gallery-show', function(e){
            // Prevent the default action from occuring.
            e.preventDefault();
            
            triggering_element = jQuery(this); /* Get current clicked element */
            
            
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
                var media_attachment, img_url;
                
                // Grab our attachment selection and construct a JSON representation of the model.
                media_attachment = cyclone_media_frame.state().get('selection').first().toJSON();
                
                if(undefined==media_attachment.sizes.medium){ /*** Account for smaller images where medium does not exist ***/
                    img_url = media_attachment.url;
                } else {
                    img_url = media_attachment.sizes.medium.url;
                }

                triggering_element.trigger('wpAddImage', [img_url, media_attachment.id, media_attachment]);
            });
    
            // Now that everything has been set, let's open up the frame.
            cyclone_media_frame.open();
        });
    })();
    
    
    (function() {
        if(typeof(wp) == "undefined" || typeof(wp.media) != "function"){
            return;
        }
        // Prepare the variable that holds our custom media manager.
        var cyclone_media_frame;
        var triggering_element = null;
        
        // Bind to our click event in order to open up the new media experience.
        $(document.body).on('click', '.cs-multiple-slides', function(e){
            // Prevent the default action from occuring.
            e.preventDefault();
            
            triggering_element = jQuery(this); /* Get current clicked element */
            
            
            // If the frame already exists, re-open it.
            if ( cyclone_media_frame ) {
                cyclone_media_frame.open();
                return;
            }
    

            cyclone_media_frame = wp.media.frames.cyclone_media_frame = wp.media({
                className: 'media-frame cs-frame',
                frame: 'select',
                multiple: true,
                title: cycloneslider_admin_vars.title2,
                library: {
                    type: 'image'
                },
                button: {
                    text:  cycloneslider_admin_vars.button2
                }
            });
    
            cyclone_media_frame.on('select', function(){
                var media_attachments;
                
                // Grab our attachment selection and construct a JSON representation of the model.
                media_attachments = cyclone_media_frame.state().get('selection').toJSON();
                
                triggering_element.trigger('wpAddImages', [media_attachments]);
            });
    
            // Now that everything has been set, let's open up the frame.
            cyclone_media_frame.open();
        });
    })();
});