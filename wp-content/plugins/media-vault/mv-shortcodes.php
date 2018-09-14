<?php
/**
 * Media Vault Shortcode Handling.
 *
 * @package WordPress_Plugin
 * @package MediaVault
 *
 * @author Max G J Panas <m@maxpanas.com>
 * @license GPL-3.0+
 */


/**
 * Add shortcode handler to print download link(s) for files
 * in the wp uploads folder
 *
 * @since 0.5
 *
 * @param $atts array Array of parameters passed to the shortcode through the editor
 *                    acceptable parameters include:
 *                    'ids' int || string list of attachment ids (int), comma separated
 *                    'sizes' string list of desired file sizes per id if attachment type is image, comma separated
 *                    'thumb' bool true if thumb is desired in link list
 *                    'thumb_size' string desired size the thumb should display as
 */
function mgjp_mv_download_links_list_shortcode_handler( $atts ) {

  extract( shortcode_atts( array(
    'ids'        => null,
    'sizes'      => 'full',
    'thumb'      => false,       // Not implemented yet
    'thumb_size' => 'thumbnail'  // Not implemented yet
  ), $atts ) );

  if ( empty( $ids ) )
    return;

  $ids = explode( ',', str_replace( ' ', '', $ids ) );
  $sizes = explode( ',', str_replace( ' ', '', $sizes ) );

  foreach ( $ids as $key => $id ) {

    if ( ! absint( $id ) || ! mgjp_mv_check_user_permitted( $id ) )
      continue;

    $size = $sizes[0];
    if ( isset( $sizes[$key] ) )
      $size = $sizes[$key];

    $file = mgjp_mv_get_attachment_download_url( $id, $size );
    if ( is_wp_error( $file ) )
      continue;

    $files[] = array( $id, $file );

  }

  if ( ! isset( $files ) )
    return;

  ob_start();

  if ( ! isset( $files[1] ) ) : ?>

    <a href="<?php echo esc_attr( $files[0][1] ) ?>" download="<?php echo esc_attr( basename( $files[0][0] ) ); ?>" title="<?php echo esc_html__( 'Click to Download:', 'media-vault' ), ' ', get_the_title( $files[0][0] ); ?>">
      <?php echo get_the_title( $files[0][0] ); ?>
    </a>

  <?php else : ?>

    <ul>
      <?php foreach ( $files as $file ) : ?>

        <li>
          <a href="<?php echo esc_attr( $file[1] ) ?>" download="<?php echo esc_attr( basename( $file[1] ) ); ?>" title="<?php echo esc_html__( 'Click to Download:', 'media-vault' ), ' ', get_the_title( $file[0] ); ?>">
            <?php echo get_the_title( $file[0] ); ?>
          </a>
        </li>

      <?php endforeach; ?>
    </ul>

  <?php endif;

  return ob_get_clean();
}

?>