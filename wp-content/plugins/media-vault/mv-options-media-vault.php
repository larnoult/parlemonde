<?php
/**
 * Media Vault Settings Functions.
 *
 * @package WordPress_Plugin
 * @package MediaVault
 *
 * @author Max G J Panas <http://maxpanas.com/>
 * @license GPL-3.0+
 */


/** Register plugin settings **/
register_setting( 'media', 'mgjp_mv_default_permission' );
register_setting( 'media', 'mgjp_mv_options', 'mgjp_mv_options_sanitize' );
register_setting( 'media', 'mgjp_mv_ir', 'mgjp_mv_ir_sanitize' );


add_settings_section(
  'mgjp_mv_general_settings',
  null,
  'mgjp_mv_render_general_settings_info_txt',
  'media'
);


add_settings_field(
  'default_permission',
  __( 'Default Protected File Permissions', 'media-vault' ),
  'mgjp_mv_render_default_permission_field',
  'media',
  'mgjp_mv_general_settings',
  array( 'label_for' => 'mgjp_mv_default_permission' )
);

add_settings_field(
  'default_upload_protection',
  __( 'Default Upload Protection', 'media-vault' ),
  'mgjp_mv_render_checkbox_field',
  'media',
  'mgjp_mv_general_settings',
  array(
    'label_for' => 'mgjp_mv_default_upload_protection',
    'option'    => 'mgjp_mv_options',
    'option_id' => 'default_upload_protection',
    'value'     => 'on',
    'desc'      => __( 'Set media file upload protection to be enabled by default when uploading new files through the Add New Media page in the WordPress Admin.', 'media-vault' )
  )
);

add_settings_field(
  'place_holder_img',
  __( 'Image Placeholder', 'media-vault' ),
  'mgjp_mv_render_place_holder_img_field',
  'media',
  'mgjp_mv_general_settings'
);


/**
 * Sanitization function for mgjp_mv_option settings
 *
 * @since 0.8
 *
 * @param $input array of options from settings page
 * @return array sanitized array of mgjp_mv_options
 */
function mgjp_mv_options_sanitize( $input ) {

  $options = get_option( 'mgjp_mv_options' );

  $options['default_upload_protection'] = isset( $input['default_upload_protection'] ) ? 'on' : 'off';

  return $options;
}


/**
 * Sanitization function for mgjp_mv_ir settings
 *
 * @since 0.8
 *
 * @param $input array of options from settings page
 * @return array sanitized array of mgjp_mv_ir options
 */
function mgjp_mv_ir_sanitize( $input ) {

  $options = get_option( 'mgjp_mv_ir' );

  $options['is_on'] = isset( $input['is_on'] ) ? ! ! $input['is_on'] : false;

  if ( isset( $input['id'] ) && wp_attachment_is_image( absint( $input['id'] ) ) )
    $options['id'] = absint( $input['id'] );
  else
    add_settings_error(
      'mgjp_mv_ir',
      'invalid-attachment-id',
      __( 'The Media Vault placeholder image ID must be the ID of an existing image attachment in your media library. Please select a different Media Vault placeholder image.', 'media-vault' )
    );

  if ( isset( $input['default'] ) && wp_attachment_is_image( absint( $input['default'] ) ) )
    $options['default'] = absint( $input['default'] );

  return $options;
}


/**
 * Render the General Settings info txt
 *
 * @since 0.4
 */
function mgjp_mv_render_general_settings_info_txt() {

  echo '<h3 class="title" id="mgjp_mv_settings_section">Media Vault</h3>';
  echo '<p>';
    esc_html_e( 'Media Vault is a plugin that allows you to protect media files in your uploads folder.', 'media-vault' );
    echo ' ';
    esc_html_e( 'Here you can set options for:', 'media-vault' );
  echo '</p>';

}


/**
 * Render a generic single checkbox field, supports options
 * saved in an array *for options saved in an array remember
 * to always manually handle sanitizing the saved settings
 *
 * @since 0.8
 *
 * @param array Array of arguments passed the specific settings field
 */
function mgjp_mv_render_checkbox_field( $args ) {

  $id = isset( $args['label_for'] ) ? $args['label_for'] : ( isset( $args['id'] ) ? $args['id'] : '' );
  $id = esc_attr( $id );

  $option = get_option( $args['option'] );
  $name   = $args['option'];
  if ( isset( $args['option_id'] ) ) {
    $option = isset( $option[$args['option_id']] ) ? $option[$args['option_id']] : null;
    $name   = $args['option'] . '[' . $args['option_id'] . ']';
  }

  $value = isset( $args['value'] ) ? $args['value'] : true;

  ?>

    <label for="<?php echo $id; ?>">

      <input type="checkbox" id="<?php echo $id; ?>" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>" <?php checked( $option, $value ); ?>>

      <?php if ( isset( $args['desc'] ) ) : ?>

        <span class="description">
          <?php echo esc_html( $args['desc'] ); ?>
        </span>

      <?php endif; ?>

    </label>

  <?php
}


/**
 * Render the default file access permission field
 *
 * @since 0.4
 *
 * @param array Array of arguments passed the specific settings field
 */
function mgjp_mv_render_default_permission_field( $args ) {

  $default_permission = get_option( 'mgjp_mv_default_permission', 'logged-in' );

  ?>

  <select id="mgjp_mv_default_permission" name="mgjp_mv_default_permission">

    <?php foreach( mgjp_mv_get_the_permissions() as $permission => $data ) : ?>

      <option value="<?php echo esc_attr( $permission ); ?>" <?php selected( $default_permission, $permission ); ?>>
        <?php echo esc_html( $data['select'] ); ?>
      </option>

    <?php endforeach; ?>

  </select>
  <span class="description">
    <?php esc_html_e( 'Select the default permissions required for accessing protected media uploads.', 'media-vault' ); ?>
  </span>

  <?php
}


/**
 * Render the placeholder image field
 *
 * @since 0.8
 *
 * @param array Array of arguments passed the specific settings field
 */
function mgjp_mv_render_place_holder_img_field( $args ) {

  $ir = get_option( 'mgjp_mv_ir' );
  
  $image_args = array(
    'alt' =>  __( 'Media Vault selected placeholder image', 'media-vault' ),
    'title' => __( 'Selected placeholder', 'media-vault' )
  );

  wp_localize_script( 'mgjp-image-selector', 'mgjp_mv_options_media', array(
    'ir_select_btn'    => __( 'Select Placeholder', 'media-vault' ),
    'ir_select_btn2'   => __( 'Change Placeholder', 'media-vault' ),
    'ir_restore_btn'   => __( 'Restore the Default', 'media-vault' ),
    'ir_modal_title'   => __( 'Select image placeholder for images that have been restricted.', 'media-vault' ),
    'ir_modal_btn'     => __( 'Use image as placeholder', 'media-vault' ),
    'ir_image_args'    => $image_args,
    'ir_default'       => isset( $ir['default'] ) ? $ir['default'] : -1,
    'ir_size'          => array( 100, 80 ),
    'ir_restore_nonce' => wp_create_nonce( 'mgjp_mv_ir_restore_default' )
  ) );

  ?>

    <style>
      .mgjp-mv-ir-container {
        margin-top: 4px;
      }

      .mgjp-mv-ir-container p {
        margin-bottom: 0;
      }

      .mgjp-mv-img-select-preview {
        float: left;
        margin-right: 8px;
        text-align: center;
      }
    </style>

    <p>

      <label for="mgjp_mv_ir">

        <input type="checkbox" id="mgjp_mv_ir" name="mgjp_mv_ir[is_on]" value="true" <?php if ( isset( $ir['is_on'] ) ) checked( $ir['is_on'] ); ?>>

        <span class="description">
          <?php esc_html_e( 'Enable showing a replacement image for protected images when the user does not have sufficient permissions to access them.', 'media-vault' ); ?>
        </span>

      </label>

    </p>

    <div class="mgjp-mv-ir-container">

      <div class="mgjp-mv-img-select-preview" id="mgjp_mv_ir_preview">

        <?php if ( isset( $ir['id'] ) ) : ?>

          <?php echo wp_get_attachment_image( $ir['id'], array( 100, 80 ), false, $image_args ); ?>

        <?php endif; ?>

      </div>

      <div id="mgjp_mv_ir_wrap">

        <label class="hide-if-js" for="mgjp_mv_ir_id">

          <input class="small-text" type="number" step="1" min="0" id="mgjp_mv_ir_id" name="mgjp_mv_ir[id]" value="<?php if ( isset( $ir['id'] ) && wp_attachment_is_image( $ir['id'] ) ) echo absint( $ir['id'] ); ?>" size="5">

          <span class="description">
            <?php esc_html_e( 'The attachment ID of the image you would like to use as a placeholder.', 'media-vault' ); ?>
          </span>

        </label>

      </div>

    </div>

  <?php
}


/**
 * Add necessary scripts and styles to the WP admin media settings page
 *
 * @since 0.8
 */
function mgjp_mv_options_media_enqueue_scripts() {

  $screen = get_current_screen();
  if ( 'options-media' !== $screen->base )
    return;

  wp_enqueue_media();
  wp_enqueue_script( 'mgjp-image-selector', plugins_url( 'js/min/mv-image-selector.min.js', __FILE__ ), array( 'jquery', 'json2' ), null, true );

}
add_action( 'admin_enqueue_scripts', 'mgjp_mv_options_media_enqueue_scripts' );

?>