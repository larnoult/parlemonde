(function ($) {
  'use strict';


  var frame,
    buttons,
    container   = $('#mgjp_mv_ir_wrap'),
    preview     = $('#mgjp_mv_ir_preview'),
    input       = $('#mgjp_mv_ir_id'),
    select_btn  = $('<p><span class="button button-primary" id="mgjp_mv_ir_select_btn">' + mgjp_mv_options_media.ir_select_btn + '</span></p>'),
    restore_btn = $('<p><span class="button" id="mgjp_mv_ir_restore_default">' + mgjp_mv_options_media.ir_restore_btn + '</span></p>');



  buttons = [ select_btn ];
  if (0 < input.val())
    buttons = [ select_btn.children().text(mgjp_mv_options_media.ir_select_btn2).parent() ];
  if (mgjp_mv_options_media.ir_default !== input.val())
    buttons.push(restore_btn);



  container

    // append the controls
    .append(buttons)


    // register the media frame creator callback
    .on('click', '#mgjp_mv_ir_select_btn', function (e) {

      e.preventDefault();

      // If the media frame already exists, reopen it.
      if (frame) {
        frame.open();
        return;
      }

      // Create the media frame.
      frame = wp.media.frames.mvCustomIR = wp.media({
        'id'      : 'mgjp_mv_select_placeholder_modal',
        'title'   : mgjp_mv_options_media.ir_modal_title,
        'library' : { 'type': 'image' },
        'button'  : { 'text': mgjp_mv_options_media.ir_modal_btn }
      });

      // When an image is selected, run a callback.
      frame.on('select', function () {
        var attachment = frame.state().get('selection').toJSON();

        $.get(ajaxurl, {
          'action' : 'mgjp_mv_get_attachment_image',
          'id'     : attachment[0].id,
          'size'   : mgjp_mv_options_media.ir_size,
          'args'   : mgjp_mv_options_media.ir_preview_args
        }, function (html) {

          if (-1 !== html && 0 !== html && html) {
            input.val(attachment[0].id);
            preview.hide().html(html).fadeIn(300);

            if (1 > $('#mgjp_mv_ir_restore_default').length && mgjp_mv_options_media.ir_default !== attachment[0].id) {
              container.append(restore_btn);
            } else if (mgjp_mv_options_media.ir_default === attachment[0].id) {
              $('#mgjp_mv_ir_restore_default').parent().detach();
            }

          }
        });
      });

      // Finally, open the modal
      frame.open();

    })

    // register the restore default callback
    .on('click', '#mgjp_mv_ir_restore_default', function () {

      // attempt to restore the default
      $.post(ajaxurl, {
        'action' : 'mgjp_mv_restore_default_placeholder_image',
        'nonce'  : mgjp_mv_options_media.ir_restore_nonce,
        'size'   : mgjp_mv_options_media.ir_size,
        'args'   : mgjp_mv_options_media.ir_preview_args
      }, function (data) {

        if (-1 !== data && 0 !== data && data) {
          // use json2.js to parse json data
          data = JSON.parse(data);
          input.val(data.id);
          preview.hide().html(data.img).fadeIn(300);
          $('#mgjp_mv_ir_restore_default').parent().detach();
          mgjp_mv_options_media.ir_default = data.id;
        }
      });
    });

}(jQuery));