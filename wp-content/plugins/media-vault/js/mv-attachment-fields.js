(function ($) {

  var vals = {},
    postId, permissionsField;

  $('body')

    .on('mgjpMvLoaded', '#mgjp_mv_attachment_fields', function (event, id) {
      postId           = id;
      permissionsField = $('#mgjp_mv_attachment_permissions_field');

      if (vals.hasOwnProperty(postId)) {
        $(this).find('.mgjp_mv_protection_toggle, .mgjp_mv_permission_select').each(function () {
          if (!vals[postId].hasOwnProperty(this.name))
            return;

          if ('checkbox' === this.type)
            this.checked = vals[postId][this.name];
          else
            this.value = vals[postId][this.name];
        });
      }

      $(this).find('.mgjp_mv_protection_toggle').trigger('change', 'mvJustLoaded');
    })

    .on('change', '.mgjp_mv_protection_toggle', function (event, justLoaded) {
      duration = 'mvJustLoaded' !== justLoaded ? 400 : 0;

      if (this.checked)
        permissionsField.slideDown(duration);
      else
        permissionsField.slideUp(duration);
    })

    .on('change', '.mgjp_mv_protection_toggle, .mgjp_mv_permission_select', function (event, justLoaded) {
      if ('mvJustLoaded' === justLoaded)
        return;

      if (!vals.hasOwnProperty(postId))
        vals[postId] = {};

      vals[postId][this.name] = 'checkbox' === this.type ? this.checked : this.value;
    });

}(jQuery));