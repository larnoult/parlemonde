<?php 

 /**
   * Custom Woocommerce Account Functions 2.6
   */
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

add_action('woocommerce_before_account_orders', 'virtue_add_woo_endpoint_title');
add_action('woocommerce_before_account_payment_methods', 'virtue_add_woo_endpoint_title');
add_action('woocommerce_before_available_downloads', 'virtue_add_woo_endpoint_title');
add_action('woocommerce_before_edit_account_form', 'virtue_add_woo_endpoint_title');
add_action('woocommerce_before_edit_account_address_form', 'virtue_add_woo_endpoint_title');
function virtue_add_woo_endpoint_title() {
	the_title( '<h2 class="kad_endpointtitle">', '</h2>' );
}

function virtue_get_wc_version() {
	return defined( 'WC_VERSION' ) && WC_VERSION ? WC_VERSION : null;
}
function virtue_is_wc_version_gte_2_6() {
	return virtue_get_wc_version() && version_compare( virtue_get_wc_version(), '2.6', '>=' );
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
function kad_woo_account_avatar() {
$current_user = wp_get_current_user();
if ( 0 == $current_user->ID ) {

} else { ?> 
<div class="kad-account-avatar">
	<div class="kad-customer-image">
		<?php echo get_avatar( $current_user->ID, 120 ); ?>
	</div>
	<div class="kad-customer-name">
		<h5>
			<?php echo esc_html( $current_user->display_name ); ?>
		</h5>
	</div> 
</div>
<?php }
}
