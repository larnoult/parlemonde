<?php 

 /**
   * Custom Woocommerce Account Functions 2.6
   */
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

add_action('woocommerce_before_account_orders', 'kad_add_woo_endpoint_title');
add_action('woocommerce_before_account_payment_methods', 'kad_add_woo_endpoint_title');
add_action('woocommerce_before_available_downloads', 'kad_add_woo_endpoint_title');
add_action('woocommerce_before_edit_account_form', 'kad_add_woo_endpoint_title');
add_action('woocommerce_before_edit_account_address_form', 'kad_add_woo_endpoint_title');
  function kad_add_woo_endpoint_title() {
    the_title( '<h2 class="kad_endpointtitle">', '</h2>' );
  }

    function kad_get_wc_version() {
      return defined( 'WC_VERSION' ) && WC_VERSION ? WC_VERSION : null;
    }
    function kad_is_wc_version_gte_2_6() {
      return kad_get_wc_version() && version_compare(kad_get_wc_version(), '2.6', '>=' );
    }


       add_action('woocommerce_before_account_navigation', 'kad_woo_account_before_div', 5);
       function kad_woo_account_before_div() {
        echo '<div class="kt-woo-account-nav">';
       }
        add_action('woocommerce_after_account_navigation', 'kad_woo_account_after_div', 5);
       function kad_woo_account_after_div() {
        echo '</div>';
       }

        add_action('woocommerce_before_account_navigation', 'kad_woo_account_avatar', 20);
      function kad_woo_account_avatar(){
         $current_user = wp_get_current_user();
          if ( 0 == $current_user->ID ) {
              
          } else { ?> 
          <div class="kad-account-avatar">
              <div class="kad-customer-image">
                <?php echo get_avatar($current_user->ID, 120 ); ?>
                <a class="kt-link-to-gravatar" href="https://gravatar.com/" target="_blank">
                  <i class="icon-cloud-upload"></i>
                  <span class="kt-profile-photo-text"><?php echo __('Update Profile Photo', 'virtue');?>
                </a>
              </div>
              <div class="kad-customer-name">
                <h5>
                  <?php echo $current_user->display_name; ?>
                </h5>
              </div> 
          </div>

          <?php }
      }
