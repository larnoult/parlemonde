<?php
/**
 * Handles extra steps necessary to fully activate
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
 * Required to verify rewrite rules are functioning
 * correctly when the plugin is not fully enabled
 *
 * @since 0.8.5
 */
function mgjp_mv_check_rewrite_rules_answer() {
   if ( isset( $_GET['mgjp_mv_file'] ) && ! empty( $_GET['mgjp_mv_file'] )
     && isset( $_GET['mgjp_mv_rewrite_test'] ) && $_GET['mgjp_mv_rewrite_test'] )
      die( 'pass' );
}
add_action( 'init', 'mgjp_mv_check_rewrite_rules_answer', 0 );


/**
 * Display a notice in WP admin to the Admins to prompt
 * them to do the extra steps to fully activate the
 * Media Vault plugin
 *
 * @since 0.8.5
 */
function mgjp_mv_extra_activation_steps_notice() {

  // only show to admins on single site install or
  // network admins on multisite install
  if ( ! current_user_can( 'install_plugins' ) )
    return;

  $screen = get_current_screen();
  if ( ! in_array( $screen->id, array( 'plugins-network', 'plugins', 'options-media', 'upload', 'media', 'attachment' ) ) )
    return;

  mgjp_mv_admin_notice(
    '<strong>' . __( 'Almost there!', 'media-vault' ) . '</strong> - ' . __( 'Because of your setup, Media Vault requires some extra steps before it is enabled. Follow the instructions and then go protect some files!', 'media-vault' ),
    array(
      'link' => network_admin_url( 'plugins.php?page=mgjp-mv-eas' ),
      'text' => __( 'Fully Activate Media Vault', 'media-vault' )
    )
  );

}
add_action( 'admin_notices', 'mgjp_mv_extra_activation_steps_notice' );
add_action( 'network_admin_notices', 'mgjp_mv_extra_activation_steps_notice' );


/**
 * Adds an admin page to the plugins admin menu dropdown.
 * The page displays instructions to admins to fully activate
 * the Media Vault plugin
 *
 * @since 0.8.5
 */
function mgjp_mv_extra_activation_steps_page() {

  if ( is_multisite() && ! is_network_admin() )
    return;

  add_submenu_page(
    'plugins.php',
    __( 'Media Vault Activation Helper', 'media-vault' ),
    __( 'Media Vault Activation', 'media-vault' ),
    'install_plugins',
    'mgjp-mv-eas',
    'mgjp_mv_render_extra_activation_steps_page'
  );

}
add_action( 'admin_menu', 'mgjp_mv_extra_activation_steps_page' );
add_action( 'network_admin_menu', 'mgjp_mv_extra_activation_steps_page' );


/**
 * Render the page
 *
 * @since 0.8.5
 */
function mgjp_mv_render_extra_activation_steps_page() {

  wp_enqueue_style( 'mgjp-mv-eas-page', plugins_url( 'css/mv-eas-page.css', __FILE__ ), 'all', null );

  global $is_apache;

  $home_path = get_home_path();
  $rewrite_rules_enabled = mgjp_mv_check_rewrite_rules();
  $eas_supported = $is_apache;

  if ( isset( $_POST['enable_mediavault'] ) ) {
    if ( $rewrite_rules_enabled ) {
      check_admin_referer( 'mgjp_mv_enable_media_vault' );
      ?>
        <div class="updated">
          <p>
            <?php printf( esc_html__( 'Media Vault was successfully enabled! Congrats! Now go protect some %s!', 'media-vault' ), '<a href="upload.php">' . esc_html__( 'files', 'media-vault' ) . '</a>' ); ?>
          </p>
        </div>
      <?php
      update_site_option( 'mgjp_mv_enabled', true );
    } else {
      ?>
        <div class="error">
          <p>
            <?php esc_html_e( 'Media Vault could not be enabled because the rewrite rules have not been set up correctly. Please verify that you have gone through and correctly completed each of the steps below and try again.', 'media-vault' ); ?>
          </p>
        </div>
      <?php
    }
  }

  ?>

    <div class="wrap">

      <h2>
        <img class="mgjp-mv-icon" alt="Media Vault Logo" src="<?php echo plugins_url( 'imgs/media-vault-logo.png', __FILE__ ); ?>">
        <?php _e( 'Media Vault Activation Helper', 'media-vault' ); ?>
      </h2>

      <?php if ( $rewrite_rules_enabled && get_site_option( 'mgjp_mv_enabled' ) ) : ?>

        <p>
          <?php printf( esc_html__( 'This page and the Media Vault Activation Helper will now no longer appear in your WordPress admin as you don\'t need them anymore! Congratulations, your setup is currently fully configured for Media Vault to function. You can now go and protect any of your %s or try %s to the Media Vault protected folder. Also don\'t forget to check out the plugin\'s %s. Thank you for using Media Vault.', 'media-vault' ), '<a href="upload.php">' . esc_html__( 'Media files', 'media-vault' ) . '</a>', '<a href="media-new.php">' . esc_html__( 'uploading new files', 'media-vault' ) . '</a>', '<a href="options-media.php#mgjp_mv_settings_section">' . esc_html__( 'settings', 'media-vault' ) . '</a>' ); ?>
        </p>

        <?php if ( ! $eas_supported ) : ?>
          <p>
            <em>
              <?php printf( esc_html__( 'Note: You have clearly successfully ported the Apache rewrite rules to run on your server technology, which was not supported by the Media Vault Activation Helper. If you are certain about your configuration, please consider posting the full method you used on the Media Vault %s. If such a post does not already exist of course! Thank you!', 'media-vault' ), '<a href="http://wordpress.org/support/plugin/media-vault" target="_blank">' . esc_html__( 'support forum', 'media-vault' ) . '</a>' ); ?>
            </em>
          </p>
        <?php endif; ?>

      <?php else : ?>

        <p>
          <?php 
            esc_html_e( 'Normally, Media Vault would automatically set up the necessary rewrite rules for the plugin to protect your files.', 'media-vault' );

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

            echo ' ';

            echo wp_kses( __( 'In order to manually fully activate Media Vault on your setup please <strong>carefully</strong> follow the instructions below:', 'media-vault' ), array( 'strong' => array() ), false ); ?>
        </p>

        <ol>

          <?php if ( $is_apache && ! got_mod_rewrite() ) : ?>
            <li>
              <p>
                <strong><?php esc_html_e( 'Important', 'media-vault' ) ?>!:</strong> <?php printf( esc_html__( 'Media Vault %s the %s module to be installed and enabled on your %s server.', 'media-vault' ), '<strong>' . esc_html__( 'requires' ) . '</strong>', '<code>mod_rewrite</code>', '<strong>Apache</strong>' ); ?>
              </p>
            </li>
          <?php endif; ?>

          <li>

            <?php if ( $eas_supported ) : ?>
              <p>
                <?php
                  $rewrite_file_type = '<code>.htaccess</code>';
                  $rewrite_file_loc  = '<code>' . $home_path . '</code>';
                  $rewrite_rule_loc  = sprintf( wp_kses( __( '<strong>in the WordPress rewrite block</strong> (the WordPress block usually starts with %s and ends with %s), <strong>just below</strong> the line reading %s', 'media-vault' ), array( 'strong' => array() ), false ), '<code># BEGIN WordPress</code>', '<code># END WordPress</code>', '<code>RewriteRule ^index\.php$ - [L]</code>' );

                  if ( ! is_multisite() && ! get_option( 'permalink_structure' ) ) {

                    $rewrite_rule_loc = __( '<strong>above</strong> any other rewrite rules in the file.', 'media-vault' );

                    printf( wp_kses( __( 'Media Vault works best with %s enabled, so it is strongly recommended that you %s! If, however, you really <i>really</i> want to use ugly permalinks, then...', 'media-vault' ), array( 'i' => array() ), false ), '<a href="http://codex.wordpress.org/Introduction_to_Blogging#Pretty_Permalinks" target="_blank">' . esc_html__( 'Pretty Permalinks', 'media-vault' ) . '</a>', '<a href="http://codex.wordpress.org/Using_Permalinks" target="_blank">' . esc_html__( 'enable them', 'media-vault' ) . '</a>' );
                    echo "\n";
                  }

                  printf( esc_html__( 'Add the following to your %s file in %s', 'media-vault' ), $rewrite_file_type, $rewrite_file_loc );
                  echo ' ', $rewrite_rule_loc;
                ?>
              </p>
            <?php else : ?>
              <p>
                <?php esc_html_e( 'Sorry, the Media Vault Activation Helper does not currently support your server setup. This does not necessarily mean that Media Vault cannot work with your setup, just that, for now, there are no simple steps to follow. Currently Apache is the only server software that is supported by the Activation Helper. Support for more is being added in future updates of the plugin. You can try the plugin\'s support forum for more help.', 'media-vault' ); ?>
              </p>
              <p>
                <?php esc_html_e( 'Below are what the rewrite rules would look like if you were running WordPress on an Apache server. Feel free to try and port these to your own server technology. If you believe you have correctly set up the necessary rewrites to emulate the behaviour of the below rules, then click on the "Enable Media Vault" button below. If Media Vault verifies that the rules you implemented are functioning correctly it will enable the rest of the plugin.', 'media-vault' ); ?>
              </p>
            <?php endif; ?>

            <?php $rewrite_rules = mgjp_mv_get_the_rewrite_rules() ?>
            <textarea class="code" readonly="readonly" cols="125" rows="<?php echo count( $rewrite_rules ); ?>"><?php echo esc_textarea( implode( "\n", $rewrite_rules ) ); ?></textarea>

          </li>

          <li>
            <p>
              <?php esc_html_e( 'Once you have completed the above steps, press the "Enable Media Vault" button below.', 'media-vault' ); ?>
            </p>
            <form method="post" action="plugins.php?page=mgjp-mv-eas">
              <?php wp_nonce_field( 'mgjp_mv_enable_media_vault' ); ?>
              <?php submit_button( __( 'Enable Media Vault', 'media-vault' ), 'primary', 'enable_mediavault', false ); ?>
            </form>
          </li>

        </ol>

      <?php endif; ?>

    </div>

  <?php
}

?>