<?php

namespace threewp_broadcast\traits;

/**
	@brief		Handle the printing / display of admin scripts.
	@since		2014-12-03 18:56:48
**/
trait admin_scripts
{
	/**
		@brief		Add some javascript to display in the admin UI.
		@details	The identifier is "optional". If the script is not specified, it is assumed that the identifier is the script.

		The identifier can be used to perhaps remove specific scripts at will.

		@since		2014-12-03 19:02:19
	**/
	public function add_admin_script( $identifier, $script = null )
	{
		// Was the "identifier" used?
		if ( ! $script )
		{
			$script = $identifier;
			$identifier = null;
		}

		$scripts = $this->admin_scripts();
		if ( ! $identifier )
			$scripts->append( $script );
		else
			$scripts->set( $identifier, $script );

		// Have we hooked into the print action?
		if ( isset( $this->__admin_scripts_hooked ) )
			return;
		$this->add_action( 'admin_print_footer_scripts', 'admin_scripts_print' );
		$this->__admin_scripts_hooked = true;
	}

	/**
		@brief		Return the admin scripts object.
		@details	The object is simply a collection.
		@since		2014-12-03 19:04:16
	**/
	public function admin_scripts()
	{
		if ( ! isset( $this->__admin_scripts ) )
			$this->__admin_scripts = ThreeWP_Broadcast()->collection();
		return $this->__admin_scripts;
	}

	/**
		@brief		Display / print the admin javascripts.
		@since		2014-12-03 19:08:30
	**/
	public function admin_scripts_print()
	{
		foreach( $this->admin_scripts() as $key => $script )
			echo $script;
	}
}
