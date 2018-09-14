<?php

if ( ! defined( 'ABSPATH' ) ) exit; 

if( is_plugin_active( 'woocommerce-memberships/woocommerce-memberships.php' ) ){
	add_filter( 'acui_restricted_fields', 'acui_wm_restricted_fields', 10, 1 );
	add_action( 'acui_documentation_after_plugins_activated', 'acui_wm_documentation_after_plugins_activated' );
	add_action( 'post_acui_import_single_user', 'acui_wm_post_import_single_user', 10, 3 );
}

function acui_wm_restricted_fields( $acui_restricted_fields ){
	return array_merge( $acui_restricted_fields, array( 'member_first_name', 'member_last_name', 'member_email', 'membership_plan_id', 'membership_plan_slug', 'membership_plan', 'membership_status', 'member_since', 'membership_expiration' ) );
}

function acui_wm_documentation_after_plugins_activated(){
	?>
	<tr valign="top">
		<th scope="row"><?php _e( "WooCommerce Membership is activated", 'import-users-from-csv-with-meta' ); ?></th>
		<td><?php _e( "You can use the <strong>columns in the CSV format created by WooCommercer Membership</strong> in order to import data from this plugin.", 'import-users-from-csv-with-meta' ); ?>. <a href="https://docs.woocommerce.com/document/woocommerce-memberships-import-and-export/"><?php _e( "Read more about columns and formats", 'import-users-from-csv-with-meta' ); ?></a>
		</td>
	</tr>
	<?php
}

function acui_wm_post_import_single_user( $headers, $row, $user_id ){
	$keys = array( 'member_first_name', 'member_last_name', 'member_email', 'membership_plan_id', 'membership_plan_slug', 'membership_plan', 'membership_status', 'member_since', 'membership_expiration' );
	$columns = array();

	foreach ( $keys as $key ) {
		$columns[ $key ] = array_search ( $key, $headers );
	}

	$membership_plan_id   = isset( $columns['membership_plan_id'] )   && ! empty( $row[ $columns['membership_plan_id'] ] )   ? (int) $row[ $columns['membership_plan_id'] ] : null;
	$membership_plan_slug = isset( $columns['membership_plan_slug'] ) && ! empty( $row[ $columns['membership_plan_slug'] ] ) ? $row[ $columns['membership_plan_slug'] ]     : null;
	$membership_plan      = null;

	if ( is_int( $membership_plan_id ) ) {
		$membership_plan = wc_memberships_get_membership_plan( $membership_plan_id );
	}

	if ( ! $membership_plan && ! empty( $membership_plan_slug ) ) {
		$membership_plan = wc_memberships_get_membership_plan( $membership_plan_slug );
	}

	// try to get an existing user membership from an id
	$user_membership_id       = isset( $columns['user_membership_id'] ) && ! empty( $row[ $columns['user_membership_id'] ] ) ? (int) $row[ $columns['user_membership_id'] ] : null;
	$existing_user_membership = is_int( $user_membership_id ) ? wc_memberships_get_user_membership( $user_membership_id ) : null;

	if ( ! $membership_plan && ! $existing_user_membership ) {
		return;
	} elseif ( ! $existing_user_membership && false ) {
		return;
	}

	$import_data = array();

	$import_data['membership_plan_id']    = $membership_plan_id;
	$import_data['membership_plan_slug']  = $membership_plan_slug;
	$import_data['membership_plan_name']  = isset( $columns['membership_plan'] )       && ! empty( $row[ $columns['membership_plan'] ] )       ? $row[ $columns['membership_plan'] ]       : null;
	$import_data['membership_plan']       = $membership_plan;
	$import_data['user_membership_id']    = $user_membership_id;
	$import_data['user_membership']       = $existing_user_membership;
	$import_data['user_id']               = $user_id;
	$import_data['user_name']             = isset( $columns['user_name'] )             && ! empty( $row[ $columns['user_name'] ] )             ? $row[ $columns['user_name'] ]             : null;
	$import_data['product_id']            = isset( $columns['product_id'] )            && ! empty( $row[ $columns['product_id'] ] )            ? $row[ $columns['product_id'] ]            : null;
	$import_data['order_id']              = isset( $columns['order_id'] )              && ! empty( $row[ $columns['order_id'] ] )              ? $row[ $columns['order_id'] ]              : null;
	$import_data['member_email']          = isset( $columns['member_email'] )          && ! empty( $row[ $columns['member_email'] ] )          ? $row[ $columns['member_email'] ]          : null;
	$import_data['member_first_name']     = isset( $columns['member_first_name'] )     && ! empty( $row[ $columns['member_first_name'] ] )     ? $row[ $columns['member_first_name'] ]     : null;
	$import_data['member_last_name']      = isset( $columns['member_last_name'] )      && ! empty( $row[ $columns['member_last_name'] ] )      ? $row[ $columns['member_last_name'] ]      : null;
	$import_data['membership_status']     = isset( $columns['membership_status'] )     && ! empty( $row[ $columns['membership_status'] ] )     ? $row[ $columns['membership_status'] ]     : null;
	$import_data['member_since']          = isset( $columns['member_since'] )          && ! empty( $row[ $columns['member_since'] ] )          ? $row[ $columns['member_since'] ]          : null;
	$import_data['membership_expiration'] = isset( $columns['membership_expiration'] ) && isset( $row[ $columns['membership_expiration'] ] )   ? $row[ $columns['membership_expiration'] ] : null;

	$action = 'create';
	$import_data = (array) apply_filters( 'wc_memberships_csv_import_user_memberships_data', $import_data, $action, $columns, $row );

	$user_membership = null;

	if ( isset( $import_data['membership_plan'] ) && $import_data['membership_plan'] instanceof WC_Memberships_Membership_Plan ) {
		if ( wc_memberships_is_user_member( $user_id, $import_data['membership_plan'] ) ) {
			return false;
		}

		$user_membership = wc_memberships_create_user_membership( array(
			'user_membership_id' => 0,
			'plan_id'            => $import_data['membership_plan']->get_id(),
			'user_id'            => $user_id,
			'product_id'         => ! empty( $import_data['product_id'] ) ? (int) $import_data['product_id'] : 0,
			'order_id'           => ! empty( $import_data['order_id'] )   ? (int) $import_data['order_id']   : 0,
		), 'create' );
	}

	acui_vm_update_user_membership_meta( $user_membership, $action, $import_data );

	do_action( 'wc_memberships_csv_import_user_membership', $user_membership, $action, $import_data );
}

function acui_vm_update_user_membership_meta( WC_Memberships_User_Membership $user_membership, $action, array $data ) {
	if( !empty( $data['product_id'] ) ) {
		$user_membership->set_product_id( trim( $data['product_id'] ) );
	}

	if( !empty( $data['order_id'] ) ) {
		$user_membership->set_order_id( trim( $data['order_id'] ) );
	}

	if( !empty( $data['member_since'] ) ) {
		$user_membership->set_start_date( trim( $data['member_since'] ) );
	}

	if( !empty( $data['membership_status'] ) ){
		$user_membership->update_status( trim( $data['membership_status'] ) );
	}

	if( !empty( $data['membership_expiration'] ) ){
		$user_membership->set_end_date( trim( $data['membership_expiration'] ) );
	}
}