<?php

class Allow_Multiple_Accounts_Test extends WP_UnitTestCase {

	private $user_id;

	function setUp() {
		parent::setUp();
		$this->set_option();

		$this->user_id = wp_create_user( 'aaa', 'password', 'user@example.com' );
	}

	function tearDown() {
		parent::tearDown();

	}



	/*
	 *
	 * DATA PROVIDERS
	 *
	 */



	/*
	 *
	 * HELPER FUNCTIONS
	 *
	 */


	private function set_option( $settings = array() ) {
		$defaults = array(
			'allow_for_everyone' => true,
			'account_limit'      => '',
			'emails'             => false,
		);
		$settings = wp_parse_args( $settings, $defaults );
		c2c_AllowMultipleAccounts::get_instance()->update_option( $settings, true );
	}


	/*
	 *
	 * TESTS
	 *
	 */


	function test_class_exists() {
		$this->assertTrue( class_exists( 'c2c_AllowMultipleAccounts' ) );
	}

	function test_plugin_framework_class_name() {
		$this->assertTrue( class_exists( 'C2C_Plugin_039' ) );
	}

	function test_version() {
		$this->assertEquals( '3.0.4', c2c_AllowMultipleAccounts::get_instance()->version() );
	}

	function test_instance_object_is_returned() {
		$this->assertTrue( is_a( c2c_AllowMultipleAccounts::get_instance(), 'c2c_AllowMultipleAccounts' ) );
	}

	/*
	 * c2c_count_multiple_accounts() / $obj->count_multiple_accounts()
	 */

	function test_count_multiple_accounts_with_single_use_email() {
		$this->assertEquals( 1, c2c_count_multiple_accounts( 'user@example.com' ) );
		$this->assertEquals( 1, c2c_AllowMultipleAccounts::get_instance()->count_multiple_accounts( 'user@example.com' ) );
	}

	function test_count_multiple_accounts_with_single_use_email_and_user_specified() {
		wp_create_user( 'bbb', 'password', 'user@example.com' );
		wp_create_user( 'ccc', 'password', 'user@example.com' );

		$this->assertEquals( 2, c2c_AllowMultipleAccounts::get_instance()->count_multiple_accounts( 'user@example.com', $this->user_id ) );
	}

	function test_count_multiple_accounts_with_unused_email() {
		$this->assertEquals( 0, c2c_count_multiple_accounts( 'unused@example.com' ) );
		$this->assertEquals( 0, c2c_AllowMultipleAccounts::get_instance()->count_multiple_accounts( 'unused@example.com' ) );
	}

	function test_count_multiple_accounts_with_unused_email_and_user_id_specified() {
		$this->assertEquals( 0, c2c_AllowMultipleAccounts::get_instance()->count_multiple_accounts( 'unused@example.com', $this->user_id ) );
	}

	function test_count_multiple_accounts_with_multiple_use_email() {
		wp_create_user( 'bbb', 'password', 'user@example.com' );
		$this->assertEquals( 2, c2c_count_multiple_accounts( 'user@example.com' ) );
		$this->assertEquals( 2, c2c_AllowMultipleAccounts::get_instance()->count_multiple_accounts( 'user@example.com' ) );
	}

	function test_count_multiple_accounts_with_multiple_use_email_and_user_id_specified() {
		wp_create_user( 'bbb', 'password', 'user@example.com' );

		$this->assertEquals( 1, c2c_AllowMultipleAccounts::get_instance()->count_multiple_accounts( 'user@example.com', $this->user_id ) );
	}

	/*
	 * c2c_has_multiple_accounts() / $obj->has_multiple_accounts()
	 */

	function test_c2c_has_multiple_accounts_with_single_use_email() {
		$this->assertFalse( c2c_has_multiple_accounts( 'user@example.com' ) );
		$this->assertFalse( c2c_AllowMultipleAccounts::get_instance()->has_multiple_accounts( 'user@example.com' ) );
	}

	function test_c2c_has_multiple_accounts_with_unused_email() {
		$this->assertFalse( c2c_has_multiple_accounts( 'unused@example.com' ) );
		$this->assertFalse( c2c_AllowMultipleAccounts::get_instance()->has_multiple_accounts( 'unused@example.com' ) );
	}

	function test_c2c_has_multiple_accounts_with_multiple_use_email() {
		wp_create_user( 'bbb', 'password', 'user@example.com' );

		$this->assertTrue( c2c_has_multiple_accounts( 'user@example.com' ) );
		$this->assertTrue( c2c_AllowMultipleAccounts::get_instance()->has_multiple_accounts( 'user@example.com' ) );
	}


	/*
	 * $obj->has_exceeded_limit()
	 */

	function test_has_exceeded_limit_for_unused_email() {
		$this->assertFalse( c2c_AllowMultipleAccounts::get_instance()->has_exceeded_limit( 'user@example.com' ) );
	}

	function test_has_exceeded_limit_for_used_email_at_limit() {
		$this->set_option( array( 'account_limit' => 1 ) );

		$this->assertTrue( c2c_AllowMultipleAccounts::get_instance()->has_exceeded_limit( 'user@example.com' ) );
	}

	function test_has_exceeded_limit_for_used_email_past_limit() {
		$this->set_option( array( 'account_limit' => 1 ) );
		wp_create_user( 'bbb', 'password', 'user@example.com' );

		$this->assertTrue( c2c_AllowMultipleAccounts::get_instance()->has_exceeded_limit( 'user@example.com' ) );
	}

	function test_has_exceeded_limit_for_used_email_below_limit() {
		$this->set_option( array( 'account_limit' => 3 ) );
		wp_create_user( 'bbb', 'password', 'user@example.com' );

		$this->assertFalse( c2c_AllowMultipleAccounts::get_instance()->has_exceeded_limit( 'user@example.com' ) );
	}

	function test_has_exceeded_limit_with_no_account_limit_and_allow_for_everyone() {
		$this->set_option( array( 'account_limit' => '', 'allow_for_everyone' => true ) );
		wp_create_user( 'bbb', 'password', 'user@example.com' );

		$this->assertFalse( c2c_AllowMultipleAccounts::get_instance()->has_exceeded_limit( 'user@example.com' ) );
	}

	function test_has_exceeded_limit_with_no_account_limit_and_not_allow_for_everyone() {
		$this->set_option( array( 'account_limit' => '', 'allow_for_everyone' => false, 'emails' => '' ) );

		$this->assertTrue( c2c_AllowMultipleAccounts::get_instance()->has_exceeded_limit( 'user@example.com' ) );
	}

	function test_has_exceeded_limit_with_no_account_limit_and_not_allow_for_everyone_and_whitelisted() {
		$this->set_option( array( 'account_limit' => '', 'allow_for_everyone' => false, 'emails' => array( 'user@example.com' ) ) );

		$this->assertFalse( c2c_AllowMultipleAccounts::get_instance()->has_exceeded_limit( 'user@example.com' ) );
	}

	function test_has_exceeded_limit_with_account_limit_and_not_allow_for_everyone() {
		$this->set_option( array( 'account_limit' => 2, 'allow_for_everyone' => false, 'emails' => array() ) );
		wp_create_user( 'bbb', 'password', 'user@example.com' );

		$this->assertTrue( c2c_AllowMultipleAccounts::get_instance()->has_exceeded_limit( 'user@example.com' ) );
	}

	function test_has_exceeded_limit_with_account_limit_and_not_allow_for_everyone_and_whitelisted() {
		$this->set_option( array( 'account_limit' => 3, 'allow_for_everyone' => false, 'emails' => array( 'user@example.com' ) ) );
		wp_create_user( 'bbb', 'password', 'user@example.com' );

		$this->assertFalse( c2c_AllowMultipleAccounts::get_instance()->has_exceeded_limit( 'user@example.com' ) );
	}

	/*
	 * c2c_get_users_by_email() / $obj->get_users_by_email()
	 */

	function test_get_users_by_email_with_no_matches() {
		$this->assertEmpty( c2c_get_users_by_email( 'unused@example.com' ) );
		$this->assertEmpty( c2c_AllowMultipleAccounts::get_instance()->get_users_by_email( 'unused@example.com' ) );
	}

	function test_get_users_by_email_with_single_match() {
		$expected = array( new WP_User( $this->user_id ) );

		$this->assertEquals( $expected, c2c_get_users_by_email( 'user@example.com' ) );
		$this->assertEquals( $expected, c2c_AllowMultipleAccounts::get_instance()->get_users_by_email( 'user@example.com' ) );
	}

	function test_get_users_by_email_with_multiple_matches() {
		$user1    = new WP_User( $this->user_id );
		$user2_id = wp_create_user( 'bbb', 'password', 'user@example.com' );
		$user2    = get_user_by( 'id', $user2_id );
		$user3_id = wp_create_user( 'ccc', 'password', 'user@example.com' );
		$user3    = new WP_User( $user3_id );

		$expected = array( $user1, $user2, $user3 );

		$this->assertEquals( $expected, c2c_get_users_by_email( 'user@example.com' ) );
		$this->assertEquals( $expected, c2c_AllowMultipleAccounts::get_instance()->get_users_by_email( 'user@example.com' ) );
	}

	/*
	 *  User creation via register_new_user()
	 */

	function test_verify_multiple_accounts_not_allowed_by_default_for_register_new_user() {
		// Pseudo-disable the plugin.
		$this->set_option( array( 'allow_for_everyone' => false ) );

		$user1_id = register_new_user( 'user1', 'user1@example.com' );
		$this->assertFalse( is_wp_error( $user1_id ) );

		$user2_id = register_new_user( 'user2', 'user1@example.com' );
		$this->assertTrue( is_wp_error( $user2_id ) );
	}

	function test_multiple_accounts_via_register_new_user_if_allow_for_everyone() {
		$this->set_option( array( 'allow_for_everyone' => true ) );

		$users = array();
		for ( $i = 0; $i < 4; $i++ ) {
			$users[] = register_new_user( "user{$i}", 'user@example.com' );
		}

		$this->assertTrue( c2c_has_multiple_accounts( 'user@example.com' ) );
		$this->assertEquals( 5, c2c_count_multiple_accounts( 'user@example.com' ) );
	}

	function test_multiple_accounts_via_register_new_user_that_exceed_limit() {
		$this->set_option( array( 'account_limit' => 3, 'allow_for_everyone' => true ) );

		$users = array();
		for ( $i = 0; $i < 3; $i++ ) {
			$users[] = register_new_user( "user{$i}", 'user@example.com' );
		}

		$this->assertFalse( is_wp_error( $users[0] ) );
		$this->assertFalse( is_wp_error( $users[1] ) );
		$this->assertTrue( is_wp_error( $users[2] ) );

		$this->assertEquals( array( 'exceeded_limit' ), $users[2]->get_error_codes() );
	}

	/*
	 * User creation via wp_create_user()
	 */

	function test_verify_multiple_accounts_not_allowed_by_default_for_wp_create_user() {
		// Pseudo-disable the plugin.
		$this->set_option( array( 'allow_for_everyone' => false ) );

		$user1_id = wp_create_user( 'user1', 'abc', 'user1@example.com' );
		$this->assertFalse( is_wp_error( $user1_id ) );

		$user2_id = wp_create_user( 'user2', 'abc', 'user1@example.com' );
		$this->assertTrue( is_wp_error( $user2_id ) );
	}

	/*
	 * Misc
	 */

	function test_uninstall_deletes_option() {
		$option = 'c2c_allow_multiple_accounts';
		c2c_AllowMultipleAccounts::get_instance()->get_options();

		$this->assertNotFalse( get_option( $option ) );

		c2c_AllowMultipleAccounts::uninstall();

		$this->assertFalse( get_option( $option ) );
	}

}
