<?php

namespace threewp_broadcast\premium_pack;

class base
	extends \plainview\sdk_broadcast\wordpress\base
{
	use \threewp_broadcast\traits\actions;		// For new_action()

	/**
		@brief		Convenience method to add a select of user writeable blogs to a forum.
		@details	The options array contains the following parameters.

		- [description]				Optional input description.
		- label						Input label.
		- form						The form2 object.
		- [multiple] = false		May the user select several blogs?
		- name						The name of the input. "blogs" is a good name.
		- [options]					An array of prepopulated options.
		- [opts]					An array of prepopulated options using the new opts() call.
		- [required] = false		Is the input required?
		- [size] = 0				Optional size, else the input is autosized.
		- value						Value or values to be selected.

		@since		2015-11-28 19:25:30
	**/
	public function add_blog_list_input( $options )
	{
		$options = (object)array_merge( [
			'description' => '',
			'label' => '',
			'form' => null,
			'required' => true,
			'multiple' => true,
			'name' => '',
			'options' => [],
			'opts' => [],
			'size' => 0,
			'value' => '',
		], $options );

		$input = $options->form->select( $options->name )
			->label( $options->label )
			->value( $options->value );

		// Prepopulate
		$input->options( $options->options );
		$input->opts( $options->opts );

		// Add all available blogs as options.
		$filter = new \threewp_broadcast\actions\get_user_writable_blogs( $this->user_id() );
		$blogs = $filter->execute()->blogs;
		foreach( $blogs as $blog )
			$input->option( $blog->get_name(), $blog->get_id() );

		// Maybe give them group functionality.
		if ( function_exists( 'BC_Blog_Groupize' ) )
			BC_Blog_Groupize( $input );

		if ( $options->description != '' )
			$input->description( $options->description );

		if ( $options->multiple )
			$input->multiple();

		if ( $options->required )
			$input->required();

		if ( $options->size > 0 )
			$input->size( $options->size );
		else
			$input->autosize();

		return $input;
	}

	/**
		@brief		Send debug info to Broadcast.
		@since		2014-02-24 00:47:19
	**/
	public function debug( $string )
	{
		$bc = ThreeWP_Broadcast();
		$args = func_get_args();
		// Get the name of the class
		$class_name = get_called_class();
		// But without the namespace
		$class_name = preg_replace( '/.*\\\/', '', $class_name );
		// And append it at the beginning of the string.
		$args[ 0 ] =  $class_name . ': ' . $args[ 0 ];
		return call_user_func_array( [ $bc, 'debug' ] , $args );
	}

	/**
		@brief		This overrides the SDK's load language in order to load all of the pack plugin translations from a single file.
		@details	A note about translations.

					Note that if you're trying to call the _ functions of, say, the meta box, the _() function that will be called will be that of Broadcast, not of this plugin.

					So instead of writing:

					$mbd->lock_post = $form->checkbox( 'lock_post' )
						->label( __( 'Lock the post', 'threewp-broadcast' ) )

					You have to ask the plugin itself to translate the string first, before it is given to the form:

					$mbd->lock_post = $form->checkbox( 'lock_post' )
						->label( $this->_( 'Lock the post' ) )


		@obsolete	Don't use since 2017-09-15.
		@since		2015-10-03 15:32:24
	**/
	public function load_language( $domain = '' )
	{
		$this->language_domain = 'Broadcast_Pack';
		$directory = ThreeWP_Broadcast()->paths( 'path_from_plugin_directory' ) . '/src/premium_pack/lang/';
		// Allow people to load their own pot files.
		$directory = apply_filters( 'Broadcast_Pack_language_directory', $directory );
		load_plugin_textdomain( $this->language_domain, false, $directory );
	}

	/**
		@brief		Maybe match this subject to a pattern.
		@details	Accepts a plaintext $pattern, or a regexp if the pattern starts and ends with a forward slash.
		@since		2017-09-15 18:27:08
	**/
	public static function maybe_preg_match( $pattern, $subject )
	{
		$is_regexp = false;
		// A straight up regexp starts and ends with a forward slash.
		if ( ( $pattern[ 0 ] == '/' ) AND ( $pattern[ strlen( $pattern ) - 1 ] == '/' ) )
			$is_regexp = true;
		else
		{
			// An asterisk is accepted as a regexp.
			if ( strpos( $pattern, '*' ) !== false )
			{
				// But it needs to be modified to be a real regexp.
				$pattern = '/' . str_replace( '*', '.*', $pattern ) . '/';
				$is_regexp = true;
			}
		}

		if ( ! $is_regexp )
			return $pattern == $subject;

		preg_match( $pattern, $subject, $matches );
		return ( count( $matches ) > 0 );
	}

	/**
		@brief		Loads and paragraphs a file.
		@since		20131207
	**/
	public function wpautop_file( $filepath )
	{
		$r = file_get_contents( $filepath );
		$r = wpautop( $r );
		return $r;
	}
}
