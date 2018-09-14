<?php

if ( ! defined( 'ABSPATH' ) ) exit; 

if( is_plugin_active( 'paid-memberships-pro/paid-memberships-pro.php' ) ){
	add_filter( 'acui_restricted_fields', 'acui_pmpro_restricted_fields', 10, 1 );
	add_action( 'acui_documentation_after_plugins_activated', 'acui_pmpro_documentation_after_plugins_activated' );
	add_action( 'post_acui_import_single_user', 'acui_pmpro_post_import_single_user', 10, 3 );
}

function acui_pmpro_fields() {
	$pmpro_fields = array(
		"membership_id",
		"membership_code_id",
		"membership_discount_code",
		"membership_initial_payment",
		"membership_billing_amount",
		"membership_cycle_number",
		"membership_cycle_period",
		"membership_billing_limit",
		"membership_trial_amount",
		"membership_trial_limit",
		"membership_status",
		"membership_startdate",
		"membership_enddate",
		"membership_subscription_transaction_id",
		"membership_payment_transaction_id",
		"membership_gateway",
		"membership_affiliate_id",
		"membership_timestamp"
	);
	
	return $pmpro_fields;
}

function acui_pmpro_restricted_fields( $acui_restricted_fields ){
	return array_merge( $acui_restricted_fields, acui_pmpro_fields() );
}

function acui_pmpro_documentation_after_plugins_activated(){
	?>
	<tr valign="top">
		<th scope="row"><?php _e( "Paid Mebership Pro is activated", 'import-users-from-csv-with-meta' ); ?></th>
		<td>
			<?php _e( "You can use the columns in the CSV in order to import data from Paid Membership Pro plugin.", 'import-users-from-csv-with-meta' ); ?>.
			<ul style="list-style:disc outside none; margin-left:2em;">
				<?php foreach ( acui_pmpro_fields() as $key => $value): ?>
				<li><?php echo $value; ?></li>
				<?php endforeach; ?>
			</ul>
		</td>
	</tr>
	<?php
}

function acui_pmpro_post_import_single_user( $headers, $row, $user_id ){
	global $wpdb;

	$keys = acui_pmpro_fields();
	$columns = array();

	foreach ( $keys as $key ) {
		$pos = array_search( $key, $headers );

		if( $pos !== FALSE ){
			$columns[ $key ] = $pos;
			$$key = $row[ $columns[ $key ] ];
		}
	}

	if( !empty( $membership_startdate ) )
		$membership_startdate = date( "Y-m-d", strtotime( $membership_startdate, current_time( 'timestamp' ) ) );
	
	if( !empty( $membership_enddate ) )
		$membership_enddate = date( "Y-m-d", strtotime( $membership_enddate, current_time( 'timestamp' ) ) );
	else
		$membership_enddate = "NULL";

	if( !empty( $membership_timestamp ) )	
		$membership_timestamp = date( "Y-m-d", strtotime( $membership_timestamp, current_time( 'timestamp' ) ) );
	
	if( !empty( $membership_discount_code ) && empty( $membership_code_id ) )
		$membership_code_id = $wpdb->get_var( "SELECT id FROM $wpdb->pmpro_discount_codes WHERE `code` = '" . esc_sql( $membership_discount_code ) . "' LIMIT 1" );

	if( !empty( $membership_id ) && empty( $membership_code_id ) ){
		pmpro_changeMembershipLevel( $membership_id, $user_id );
	}
	elseif( !empty( $membership_code_id ) ){
		$custom_level = array(
			'user_id' => $user_id,
			'membership_id' => $membership_id,
			'code_id' => $membership_code_id,
			'initial_payment' => $membership_initial_payment,
			'billing_amount' => $membership_billing_amount,
			'cycle_number' => $membership_cycle_number,
			'cycle_period' => $membership_cycle_period,
			'billing_limit' => $membership_billing_limit,
			'trial_amount' => $membership_trial_amount,
			'trial_limit' => $membership_trial_limit,
			'status' => $membership_status,
			'startdate' => $membership_startdate,
			'enddate' => $membership_enddate
		);
				
		pmpro_changeMembershipLevel( $custom_level, $user_id );
		
		if( $membership_status === "inactive" || ( !empty( $membership_enddate ) && $membership_enddate !== "NULL" && strtotime( $membership_enddate, current_time('timestamp') ) < current_time('timestamp') ) ){			
			$sqlQuery = "UPDATE $wpdb->pmpro_memberships_users SET status = 'inactive' WHERE user_id = '" . $user_id . "' AND membership_id = '" . $membership_id . "'";
			$wpdb->query( $sqlQuery );
			$membership_in_the_past = true;
		}
		
		if( $membership_status === "active" && ( empty( $membership_enddate ) || $membership_enddate === "NULL" || strtotime( $membership_enddate, current_time( 'timestamp' ) ) >= current_time( 'timestamp' ) ) ){			
			$sqlQuery = $wpdb->prepare( "UPDATE {$wpdb->pmpro_memberships_users} SET status = 'active' WHERE user_id = %d AND membership_id = %d", $user_id, $membership_id );
			$wpdb->query( $sqlQuery );
		}
	}
	
	if( !empty( $membership_subscription_transaction_id ) && !empty( $membership_gateway ) || !empty( $membership_timestamp ) || !empty( $membership_code_id ) ){
		$order = new MemberOrder();
		$order->user_id = $user_id;
		$order->membership_id = $membership_id;
		$order->InitialPayment = $membership_initial_payment;		
		$order->payment_transaction_id = $membership_payment_transaction_id;
		$order->subscription_transaction_id = $membership_subscription_transaction_id;
		$order->affiliate_id = $membership_affiliate_id;
		$order->gateway = $membership_gateway;

		if( !empty( $membership_in_the_past ) )
			$order->status = "cancelled";
		
		$order->saveOrder();
		
		if( !empty( $membership_timestamp ) ){
			$timestamp = strtotime( $membership_timestamp, current_time('timestamp') );
			$order->updateTimeStamp( date( "Y", $timestamp ), date( "m", $timestamp ), date( "d", $timestamp ), date( "H:i:s", $timestamp ) );
		}
	}
	
	if( !empty( $membership_code_id ) && !empty( $order ) && !empty( $order->id ) )
		$wpdb->query( "INSERT INTO $wpdb->pmpro_discount_codes_uses (code_id, user_id, order_id, timestamp) VALUES('" . esc_sql( $membership_code_id ) . "', '" . esc_sql( $user_id ) . "', '" . intval( $order->id ) . "', now())" );
}