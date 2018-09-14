<?php
/**
 * Functions for Media Vault WP Media New (wp-admin/media-new.php) additions.
 *
 * @package WordPress_Plugin
 * @package MediaVault
 *
 * @author Max G J Panas <http://maxpanas.com/>
 * @license GPL-3.0+
 */


/**
 * Add custom styles to the media upload page
 *
 * @since 0.2
 */
function mgjp_mv_media_new_options_css() {

  $screen = get_current_screen();
  if ( 'media' == $screen->base && 'add' == $screen->action )
    wp_enqueue_style( 'mgjp-mv-media-new-styles', plugins_url( 'css/mv-media-new.css', __FILE__ ), 'all', null );

}
add_action( 'admin_enqueue_scripts', 'mgjp_mv_media_new_options_css' );


/**
 * Js for uploads page updates a plupload var controlling
 * whether an upload is moved to the protected directory
 * or not.
 *
 * @since 0.2
 */
function mgjp_mv_media_new_options_js() { ?>

    <script type="text/javascript">
      (function ($) {
        'use strict';

        var input = $('input[name="mgjp_mv_protected"]'),
          ctrl = document.getElementById('mgjp_mv_protected'),
          ui = $('#plupload-upload-ui');

        function state(check) {
          return 'mgjp-mv-' + (check == 'on' ? '' : 'un') + 'checked';
        }

        input.on('change', function () {

          var check = ctrl.checked ? 'on' : 'off';

          ui.removeClass(state(check == 'on' ? 'off' : 'on'))
            .addClass(state(check));

          wpUploaderInit.multipart_params.mgjp_mv_protected = check;
        });

        setTimeout( function () {
          input.change();
        }, 200 );

      }(jQuery));
    </script>

  <?php
}
add_action( 'admin_footer-media-new.php', 'mgjp_mv_media_new_options_js' );


/**
 * Add a checkbox below the media upload form to control
 * whether a file is saved as a regular media upload or
 * a protected media upload.
 *
 * @since 0.2
 */
function mgjp_mv_render_media_new_options() {

  $screen = get_current_screen();
  if ( 'media' == $screen->base && 'add' == $screen->action ) : ?>

    <table class="form-table">
      <tbody>
        <tr>

          <th scope="row">
            <label for="mgjp_mv_protected">
              <?php esc_html_e( 'Protect Media Uploads', 'media-vault' ); ?>
            </label>
          </th>

          <td>

            <label for="mgjp_mv_protected">

              <?php $options = get_option( 'mgjp_mv_options' ); ?>

              <input type="checkbox" id="mgjp_mv_protected" name="mgjp_mv_protected" <?php if ( isset( $options['default_upload_protection'] ) ) checked( $options['default_upload_protection'], 'on' ); ?>>

              <span class="description">
                <?php esc_html_e( 'Tick this box to save media uploads in a protected folder.', 'media-vault' ); ?>
              </span>

            </label>

          </td>

        </tr>
      </tbody>
    </table>

  <?php endif;
}
add_action( 'post-upload-ui', 'mgjp_mv_render_media_new_options');


/**
 * Add a message box to the media uploader to
 * clearly show when new uploads are protected
 *
 * @since 0.2
 */
function mgjp_mv_render_media_new_options_message_box() {

  $screen = get_current_screen();
  if ( 'media' == $screen->base && 'add' == $screen->action ) : ?>

    <div class="mgjp-mv-tag">
      <span aria-role="hidden" class="mgjp-mv-tag-icon"></span>
      <?php echo esc_html_x( 'New Uploads are', 'as in: Uploads are now Protected', 'media-vault' ); ?>
      <strong><?php echo esc_html_x( 'Protected', 'as in: Uploads are now Protected', 'media-vault' ); ?></strong>
    </div>

  <?php endif;
}
add_action( 'pre-plupload-upload-ui', 'mgjp_mv_render_media_new_options_message_box' );

?>