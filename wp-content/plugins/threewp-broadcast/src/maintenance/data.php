<?php

namespace threewp_broadcast\maintenance;

class data
{
	public $checks;

	public $controller;

	public function __construct( $controller )
	{
		$this->controller = $controller;
		$this->checks = new checks\container;
		$this->checks->controller = $controller;
		$this->checks->maintenance_data = $this;
		$this->checks->add_check( new checks\broadcast_data\check );
		$this->checks->add_check( new checks\simple_broadcast_data\check );
		$this->checks->add_check( new checks\database_tools\check );
		$this->checks->add_check( new checks\view_broadcast_data\check );
		$this->checks->add_check( new checks\view_blog_access\check );
		$this->checks->add_check( new checks\view_post_info\check );
	}

	/**
		@brief		Return the Broadcast instance.
		@since		20131101
	**/
	public static function broadcast()
	{
		return \threewp_broadcast\ThreeWP_Broadcast::instance();
	}

	/**
		@brief		Load the data object from disk.
		@details	The serialized object is stored in the temp directory and is unique for each logged-in admin.
		@since		20131101
	**/
	public static function load( $controller )
	{
		$bc = self::broadcast();
		$user_id = $bc->user_id();
		$filename = self::get_filename( $user_id );
		if ( ! is_readable( $filename ) )
			file_put_contents( $filename, '' );
		$data = file_get_contents( $filename );
		$data = unserialize( $data );
		if ( ! $data )
			$data = new data( $controller );
		return $data;
	}

	public static function get_filename( $user_id )
	{
		return get_temp_dir() . sprintf( 'broadcast_maintenance_for_user_%s.tmp', $user_id );
	}

	/**
		@brief		Resets the saved check data and returns a new data object.
		@since		20131104
	**/
	public function reset()
	{
		$bc = self::broadcast();
		$user_id = $bc->user_id();
		$filename = self::get_filename( $user_id );

		unlink( $filename );

		return self::load( $this->controller );
	}

	public function save()
	{
		$bc = self::broadcast();
		$data = serialize( $this );
		$user_id = $bc->user_id();

		$filename = self::get_filename( $user_id );
		file_put_contents( $filename, $data );
	}

}
