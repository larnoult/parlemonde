<?php

defined( 'ABSPATH' ) or die();

class test_ForceAdminColorScheme extends WP_UnitTestCase {

	public static function setUpBeforeClass() {
		// Make all requests as if in the admin, which is the only place the plugin
		// affects.
		define( 'WP_ADMIN', true );

		// Re-initialize plugin now that WP_ADMIN is true.
		c2c_ForceAdminColorScheme::init();

		// Re-fire init handler as admin_init action would've done.
		c2c_ForceAdminColorScheme::do_init();
	}

	public function tearDown() {
		parent::tearDown();
		$this->unset_current_user();
		// Ensure the filter gets removed
//		remove_filter( 'c2c_always_allow_admin_comments_disable', array( $this, 'disable_admin_commenting_on_specified_post' ), 10, 2 );
//		remove_filter( 'c2c_always_allow_admin_comments_disable', array( $this, 'enable_admin_commenting_on_specified_post' ), 10, 2 );
	}


	//
	//
	// HELPER FUNCTIONS
	//
	//


	private function create_user( $role, $set_as_current = true ) {
		$user_id = $this->factory->user->create( array( 'role' => $role ) );
		if ( $set_as_current ) {
			wp_set_current_user( $user_id );
		}
		return $user_id;
	}

	// helper function, unsets current user globally. Taken from post.php test.
	private function unset_current_user() {
		global $current_user, $user_ID;

		$current_user = $user_ID = null;
	}


	//
	//
	// FUNCTIONS FOR HOOKING ACTIONS/FILTERS
	//
	//




	//
	//
	// TESTS
	//
	//


	public function test_class_exists() {
		$this->assertTrue( class_exists( 'c2c_ForceAdminColorScheme' ) );
	}

	public function test_version() {
		$this->assertEquals( '1.1.1', c2c_ForceAdminColorScheme::version() );
	}

	public function test_setting_name_does_not_change() {
		$this->assertEquals( 'c2c_forced_admin_color', c2c_ForceAdminColorScheme::get_setting_name() );
	}

	public function test_hooks_admin_init() {
		$this->assertEquals( 10, has_filter( 'admin_init', array( 'c2c_ForceAdminColorScheme', 'do_init' ) ) );
	}

	public function test_registers_hooks() {
		$this->assertEquals( 10, has_filter( 'get_user_option_admin_color', array( 'c2c_ForceAdminColorScheme', 'force_admin_color'           ) ) );
		$this->assertEquals( 20, has_action( 'admin_color_scheme_picker',   array( 'c2c_ForceAdminColorScheme', 'add_checkbox'                ) ) );
		$this->assertEquals( 10, has_action( 'personal_options_update',     array( 'c2c_ForceAdminColorScheme', 'save_setting'                ) ) );
		$this->assertEquals( 8,  has_action( 'admin_color_scheme_picker',   array( 'c2c_ForceAdminColorScheme', 'hide_admin_color_input'      ), 8 ) );
		$this->assertEquals( 10, has_action( 'load-profile.php',            array( 'c2c_ForceAdminColorScheme', 'register_css'                ) ) );
	}

	public function test_no_default_forced_admin_color() {
		$this->assertFalse( c2c_ForceAdminColorScheme::get_forced_admin_color() );
	}

	public function test_get_forced_admin_color() {
		c2c_ForceAdminColorScheme::set_forced_admin_color( 'ocean' );

		$this->assertEquals( 'ocean', c2c_ForceAdminColorScheme::get_forced_admin_color() );
	}

	public function test_set_forced_admin_color_sets_setting_value() {
		c2c_ForceAdminColorScheme::set_forced_admin_color( 'ocean' );

		$this->assertEquals( 'ocean', get_option( c2c_ForceAdminColorScheme::get_setting_name() ) );
	}

	public function test_user_color_scheme_is_the_forced_color_scheme() {
		$this->create_user( 'editor' );

		$this->assertEquals( 'fresh', get_user_option( 'admin_color' ) );

		c2c_ForceAdminColorScheme::set_forced_admin_color( 'ocean' );

		$this->assertEquals( 'ocean', get_user_option( 'admin_color' ) );
	}

	public function test_setting_forced_color_scheme_to_empty_string_deletes_option() {
		$this->test_user_color_scheme_is_the_forced_color_scheme();

		c2c_ForceAdminColorScheme::set_forced_admin_color( '' );

		$this->assertEquals( 'fresh', get_user_option( 'admin_color' ) );
		$this->assertFalse( get_option( c2c_ForceAdminColorScheme::get_setting_name() ) );
	}

	public function test_uninstall_deletes_option() {
		$option = c2c_ForceAdminColorScheme::get_setting_name();

		c2c_ForceAdminColorScheme::set_forced_admin_color( 'ocean' );

		$this->assertEquals( 'ocean', get_option( $option ) );

		c2c_ForceAdminColorScheme::uninstall();

		$this->assertFalse( get_option( $option ) );
	}

}
