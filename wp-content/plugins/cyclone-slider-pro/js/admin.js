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
    this.expandables = data;/*** data format should be object[slideshow_id][element_index] ***/
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
        var slideshow_id, cs_ui_open;
        
        slideshow_id = $('#cyclone-slides-metabox .cs-sortables').data('post-id');
        
        cs_ui_open = new CsUiOpen(cs_local_storage.get('cs_slide_toggles'));/*** Handle persistent slide data ***/
        
        /*** Init - Sortable slides ***/
        $('.cs-sortables').sortable({
            handle:'.cs-header',
            placeholder: "cs-slide-placeholder",
            forcePlaceholderSize:true,
            delay:100,
            /*** Update form field indexes when slide order changes ***/
            update: function(event, ui) {
                $('.cs-sortables .cs-slide').each(function(boxIndex, box){ /*** Loop thru each box ***/
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
        
        /*** Init - Slide ID and title ***/
        $('.cs-sortables .cs-slide').each(function(i){
            var body;
            
            body = $(this).find('.cs-body');

            $(this).data('cs_id',i);
            
            if(cs_ui_open.get(slideshow_id ,i)=='open'){
                body.slideDown(0);
            } else {
                body.slideUp(0);
            }
        });
        
        /*** Add - Slide box from a hidden html template ***/
        $('#cyclone-slides-metabox').on('click', '.cs-add-slide', function(e){
            var id = $('.cs-sortables .cs-slide').length;
            var html = $('.cs-slide-skeleton').html();
            html = html.replace(/{id}/g, id);/*** replace all occurences of {id} to real id ***/
            
            $('.cs-sortables').append(html);
            $('.cs-sortables .cs-slide:last').find('.cs-thumbnail').hide().end().find('.cs-body').show();

            $('.cs-sortables .cs-slide').each(function(i){
                $(this).data('cs_id',i);
            });
            $('.expandable-body').each(function(i){
                $(this).data('cs_id',i);
            });
            
            $(".cycloneslider_metas_enable_slide_effects").trigger('change');
            
            e.preventDefault();
        });
        
        /*** Add image to slide ***/
        $('#cyclone-slides-metabox').on('wpAddImage', '.cs-media-gallery-show', function(e, image_url, attachment_id){
            var current_slide_box, slide_thumb, slide_attachment_id;

            current_slide_box = $(this).parents('.cs-slide');/*** Get current box ***/
            slide_thumb = current_slide_box.find('.cs-image-thumb');/*** Find the thumb ***/
            slide_attachment_id = current_slide_box.find('.cs-image-id ');/*** Find the hidden field that will hold the attachment id ***/
            
            slide_thumb.html('<img src="'+image_url+'" alt="thumb">').show();
            slide_attachment_id.val(attachment_id);
 
        });
        
        /*** Add multiple images as slide ***/
        $('#cyclone-slides-metabox').on('wpAddImages', '.cs-multiple-slides', function(e, media_attachments){
            var cur_slide_count = $('.cs-sortables .cs-slide').length;
            
            for(i=0; i<media_attachments.length; ++i){
                
                $('#cyclone-slides-metabox .cs-add-slide').trigger('click');
                
                $('.cs-sortables .cs-slide').eq(cur_slide_count+i).find('.cs-media-gallery-show').trigger('wpAddImage', [media_attachments[i].url, media_attachments[i].id]);
            }
            
        });
        
        /*** Toggle - slide body visiblity ***/
        $('#cyclone-slides-metabox').on('click',  '.cs-header', function(e) {
            var id, box, body, cs_slide_toggles;
            
            box = $(this).parents('.cs-slide');
            body = box.find('.cs-body');
            
            id = box.data('cs_id');
            
            if(body.is(':visible')){
                body.slideUp(100);
                cs_ui_open.remove(slideshow_id , id);
            } else {
                body.slideDown(100);
                cs_ui_open.set(slideshow_id , id, 'open');/*** remember open section ***/ 
            }
            
            cs_local_storage.set('cs_slide_toggles', cs_ui_open.getAll());
        });
        
        /*** Delete - Remove slide box ***/
        $('#cyclone-slides-metabox').on('click',  '.cs-delete', function(e) {

            var box = $(this).parents('.cs-slide');
            box.fadeOut('slow', function(){ box.remove()});

            e.preventDefault();
            e.stopPropagation();
        });
        
        /*** Switcher - switch between slide types ***/
        $('#cyclone-slides-metabox').on('change', '.cs-slide-type-switcher', function(e){
            var box, body, image_box, video_box, custom_box, icon;
            
            box = $(this).parents('.cs-slide');
            box.attr('data-slide-type', $(this).val());
            
        });
        $('.cs-slide-type-switcher').trigger('change');
        
        /*** Enable/Disable Link URL if lightbox is selected ***/
        $('#cyclone-slides-metabox').on('change', '.cycloneslider_metas_link_target', function(e){
            var box, link_url;
            
            box = $(this).parents('.expandable-box');
            
            link_url = box.find('.cycloneslider_metas_link_url');
            
            if ($(this).val() == 'lightbox') {
                link_url.attr('disabled', 'disabled');
            } else {
                link_url.removeAttr('disabled');
            }
        });
        $('.cycloneslider_metas_link_target').trigger('change');
        
    })();
    
    /*** EXPANDABLES ***/
    (function() {
        var slideshow_id, cs_ui_open;
        
        /*** Init ***/
        slideshow_id = $('#cyclone-slides-metabox .cs-sortables').data('post-id');
        
        cs_ui_open = new CsUiOpen(cs_local_storage.get('cs_expandables'));
        
        $('#cyclone-slides-metabox .expandable-body').each(function(i){
            $(this).data('cs_id', i);
            
            if(cs_ui_open.get(slideshow_id ,i)=='open'){
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
                cs_ui_open.remove(slideshow_id , id);
                
            } else {
                body.slideDown(100);
                cs_ui_open.set(slideshow_id , id, 'open');
                
            }
            
            cs_local_storage.set('cs_expandables', cs_ui_open.getAll());
        });
    })();
    
    /*** VIDEO SLIDE ***/
    (function() {
        var slideshow_id;
        
        slideshow_id = $('#cyclone-slides-metabox .cs-sortables').data('post-id');
        
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
        $('#cyclone-slider-templates-metabox').on('click', '.cs-templates li', function(e){
            $('.cs-templates li').removeClass('active');
            $('.cs-templates li input').removeAttr('checked');
            $(this).addClass('active').find('input').attr('checked','checked');
        });
        $('#cyclone-slider-templates-metabox').on('click', '.body .cs-location a', function(e){
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
            if($(this).val()=='tileBlind' || $(this).val()=='tileSlide'){
                $(this).siblings('.cycloneslider-slide-tile-properties').slideDown('fast');
            } else {
                $(this).siblings('.cycloneslider-slide-tile-properties').slideUp('fast');
            }
        });
        $(".cycloneslider_metas_fx").trigger('change');
        
        /*** enable/disable form fields and labels ***/
        $('#cyclone-slides-metabox').on('change', '.cycloneslider_metas_enable_slide_effects', function(){
            if($(this).val()==0){
                $(this).parent().find('input,select').not(this).attr('disabled','disabled');
                $(this).parent().find('label,.note').addClass('disabled');
            } else {
                $(this).parent().find('input,select').not(this).removeAttr('disabled');
                $(this).parent().find('label,.note').removeClass('disabled');
            }
        });
        $(".cycloneslider_metas_enable_slide_effects").trigger('change');
        
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
                
                triggering_element.trigger('wpAddImage', [img_url, media_attachment.id]);
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