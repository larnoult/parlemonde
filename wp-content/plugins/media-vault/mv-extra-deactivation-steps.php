<?php
/**
 * Handles extra steps necessary to fully deactivate
 * the Media Vault plugin on certain set-ups such as
 * multisite, non-apache servers, non-writable htaccess
 * and others
 *
 * @package WordPress_Plugins
 * @package MediaVault
 *
 * @author Max G J Panas <http://maxpanas.com/>
 * @license GPL-3.0+
 */



/**
 * Display a notice in WP admin to the Admins to prompt
 * them to do the extra steps to fully deactivate the
 * Media Vault plugin
 *
 * @since 0.8.5
 */
function mgjp_mv_extra_deactivation_steps_notice() {

  // only show to admins on single site install or
  // network admins on multisite install
  if ( ! current_user_can( 'install_plugins' ) )
    return;

  $screen = get_current_screen();
  if ( is_multisite() ) {
    if ( 'plugins-network' !== $screen->id )
      return;
  } else {
    if ( 'plugins' !== $screen->id )
      return;
  }

  if ( 'disallowed' !== get_site_option( 'mgjp_mv_deactivation' ) )
    return;

  mgjp_mv_admin_notice(
    '<strong>' . __( 'Almost done!', 'media-vault' ) . '</strong> - ' . __( 'Because of your setup, Media Vault requires some extra steps before it is deactivated. If you really want to deactivate, just click the button and follow the instructions!', 'media-vault' ),
    array(
      array(
        'link' => network_admin_url( 'plugins.php?page=mgjp-mv-eds' ),
        'text' => __( 'Fully Deactivate Media Vault', 'media-vault' )
      ),
      array(
        'link' => network_admin_url( 'plugins.php?page=mgjp-mv-eds&cancel_deactivation=1&_wpnonce=' . wp_create_nonce( 'mgjp_mv_deactivation' ) ),
        'text' => __( 'Cancel Deactivation', 'media-vault' )
      )
    )
  );

}
add_action( 'admin_notices', 'mgjp_mv_extra_deactivation_steps_notice' );
add_action( 'network_admin_notices', 'mgjp_mv_extra_deactivation_steps_notice' );


/**
 * Adds an admin page to the plugins admin menu dropdown.
 * The page displays instructions to admins to fully deactivate
 * the Media Vault plugin
 *
 * @since 0.8.5
 */
function mgjp_mv_extra_deactivation_steps_page() {

  if ( is_multisite() && ! is_network_admin() )
    return;

  add_submenu_page(
    'plugins.php',
    __( 'Media Vault Deactivation Helper', 'media-vault' ),
    __( 'Media Vault Deactivation', 'media-vault' ),
    'install_plugins',
    'mgjp-mv-eds',
    'mgjp_mv_render_extra_deactivation_steps_page'
  );

}
add_action( 'admin_menu', 'mgjp_mv_extra_deactivation_steps_page' );
add_action( 'network_admin_menu', 'mgjp_mv_extra_deactivation_steps_page' );


/**
 * Render the page
 *
 * @since 0.8.5
 */
function mgjp_mv_render_extra_deactivation_steps_page() {

  wp_enqueue_style( 'mgjp-mv-eas-page', plugins_url( 'css/mv-eas-page.css', __FILE__ ), 'all', null );

  global $is_apache;

  $home_path = get_home_path();
  $rewrite_rules_enabled = mgjp_mv_check_rewrite_rules( true );
  $eds_supported = $is_apache;

  if ( isset( $_POST['true_deactivation'] ) ) {
    if ( $rewrite_rules_enabled ) {
      ?>
        <div class="error">
          <p>
            <?php echo wp_kses( __( '<em>At least one Media Vault rewrite rule is still functional.</em> Media Vault will not be properly deactivated if the rewrite rules are not removed because they <strong>will</strong> cause problems when attempting to access files that are in the Media Vault protected folders.', 'media-vault' ), array( 'em' => array(), 'strong' => array() ) ); ?>
          </p>
          <p>
            <?php esc_html_e( 'Please either verify that you have gone through and correctly completed each of the steps below and try again, or click the "Temporarily Deactivate Media Vault" button if you do not want to remove the rules and will re-activate the plugin again soon.', 'media-vault' ); ?>
          </p>
        </div>
      <?php
    } else {
      check_admin_referer( 'mgjp_mv_deactivation' );

      update_site_option( 'mgjp_mv_deactivation', 'allowed' );
    }
  } else if ( isset( $_POST['temp_deactivation'] ) ) {
    check_admin_referer( 'mgjp_mv_deactivation' );

    update_site_option( 'mgjp_mv_deactivation', 'temp' );
  }

  if ( in_array( get_site_option( 'mgjp_mv_deactivation' ), array( 'allowed', 'temp' ) ) ) {

    $plugin   = mgjp_mv_get_dirfile();
    $nonce    = wp_create_nonce( "deactivate-plugin_$plugin" );
    $location = 'plugins.php?action=deactivate&plugin=' . urlencode( $plugin ) . "&_wpnonce=$nonce";

    wp_redirect( network_admin_url( $location ) );
    exit;
  }

  if ( isset( $_REQUEST['cancel_deactivation'] ) ) {
    check_admin_referer( 'mgjp_mv_deactivation' );

    delete_site_option( 'mgjp_mv_deactivation' );

    wp_redirect( network_admin_url( 'plugins.php' ) );
    exit;
  }

  ?>

    <div class="wrap">

      <h2>
        <img class="mgjp-mv-icon" alt="Media Vault Logo" src="<?php echo plugins_url( 'imgs/media-vault-logo.png', __FILE__ ); ?>">
        <?php _e( 'Media Vault Deactivation Helper', 'media-vault' ); ?>
      </h2>

      <p>
        <?php 
          esc_html_e( 'Normally, the Media Vault rewrite rules would be automatically removed when the plugin is deactivated.', 'media-vault' );

          echo ' ';

          if ( ! $is_apache )
            $errors['Notapache'] = sprintf( esc_html_x( 'you are not on an %s', 'as in: "you are not on an Apache webserver"', 'media-vault' ), '<b>Apache Server</b>' );

          if ( is_multisite() )
            $errors['Multisite'] = sprintf( esc_html_x( 'you are running a %s installation', 'as in: "you are on a WordPress Multisite installation"', 'media-vault' ), '<b>WordPress MultiSite</b>' );

          if ( ! isset( $errors ) && ! get_option( 'permalink_structure' ) )
            $errors['Nopretty'] = sprintf( esc_html_x( 'you do not have %s enabled', 'as in: "you do not have Pretty Permalinks enabled"', 'media-vault' ), '<a href="http://codex.wordpress.org/Introduction_to_Blogging#Pretty_Permalinks" target="_blank">' . esc_html__( 'Pretty Permalinks' ) . '</a>' );

          if ( ! isset( $errors ) && ! is_writable( $home_path . '.htaccess' ) )
            $errors['Nonwritable'] = sprintf( esc_html__( 'the site\'s %s file is not writable', 'as in: "the site\'s .htaccess file is not writable "', 'media-vault' ), '<code>.htaccess</code>' );

          if ( isset( $errors ) ) {

            $error_txt = '';
            $last_error = array_pop( $errors );
            if ( count( $errors ) > 0 )
              $error_txt .= implode( ', ', $errors ) . ' ' . esc_html__( 'and', 'media-vault' ) . ' ';
            $error_txt .= $last_error . ',';

            printf( esc_html_x( 'However, because %s the plugin was unable to successfully update the rewrite rules for this site programmatically.', 'as in: "Because *you are running WordPress MultiSite* Media Vault cannot do it automatically"', 'media-vault' ), $error_txt );

          } else {

            esc_html_e( 'However for some reason the plugin was unable to successfully update the rewrite rules for this site.', 'media-vault' );

          }

          printf( wp_kses( __( 'In order to manually fully %s Media Vault on your setup please <strong>carefully</strong> follow the instructions below:', 'media-vault' ), array( 'strong' => array() ), false ), 'deactivate' );
        ?>
      </p>
      <p>
        <em>
          <?php echo wp_kses( __( 'If you want to <strong>temporarily</strong> deactivate Media Vault and do not mind leaving the rewrite rules functioning, simply click the "Temporarily Deactivate Media Vault" button. <strong>However, if you are planning on permanently deactivating Media Vault make sure to follow the steps below, otherwise you may experience problems when trying to access attachment files still in the Media Vault protected folders.</strong>', 'media-vault' ), array( 'strong' => array() ) ); ?>
        </em>
      </p>

      <ol>

        <li>
          <?php if ( $eds_supported ) : ?>
            <p>
              <?php
                $rewrite_file_type = '<code>.htaccess</code>';
                $rewrite_file_loc  = '<code>' . $home_path . '</code>';

                printf( wp_kses( __( 'From your %s file in %s, remove <strong>all</strong> the code between the lines starting with %s and ending with %s.', 'media-vault' ), array( 'strong' => array() ) ), $rewrite_file_type, $rewrite_file_loc, '<code># Media Vault Rewrite Rules</code>', '<code># Media Vault Rewrite Rules End</code>' );
              ?>
            </p>
          <?php else : ?>
            <p>
              <?php printf( esc_html__( 'In order to fully deactivate Media Vault you are going to need to remove (or comment out) all the rewrite rules you had set up to enable the plugin when you activated it. Sorry these instructions are not more specific but the Media Vault Deactivation Helper does not yet support your setup. If you have additional questions you can try asking them on the plugin\'s %s', 'media-vault' ), '<a href="http://wordpress.org/plugins/media-vault/support/" target="_blank">' . esc_html__( 'support forum', 'media-vault' ) . '</a>' ); ?>
            </p>
          <?php endif; ?>
        </li>

        <li>
          <p>
            <?php _e( 'Once you have completed the above steps, press the "Deactivate Media Vault" button below to deactivate Media Vault.', 'media-vault' ); ?>
          </p>
          <form method="post" action="plugins.php?page=mgjp-mv-eds">
            <?php wp_nonce_field( 'mgjp_mv_deactivation' ); ?>
            <?php submit_button( __( 'Deactivate Media Vault', 'media-vault' ), 'primary', 'true_deactivation', false ); ?>
            <?php submit_button( __( 'Temporarily Deactivate Media Vault', 'media-vault' ), '', 'temp_deactivation', false ); ?>
            <?php submit_button( __( 'Cancel Deactivation', 'media-vault' ), 'primary', 'cancel_deactivation', false ); ?>
          </form>
        </li>

      </ol>

    </div>

  <?php
}