<?php

namespace threewp_broadcast\meta_box;

use \plainview\sdk_broadcast\collections\collection;

/**
	@brief		The data class that is passed around when creating the broadcast meta box.
	@since		20130928
**/
class data
{
	/**
		@brief		INPUT: ID of the blog.
		@since		20131005
		@var		$blog_id
	**/
	public $blog_id;

	/**
		@brief		OUTPUT: Collection of CSS files that should be loaded.
		@since		20131010
		@var		$css
	**/
	public $css;

	/**
		@brief		INPUT: The Form2 object from the Plainview SDK.
		@since		20131005
		@var		$form
	**/
	public $form;

	/**
		@brief		OUTPUT: HTML object containing data to be displayed.
		@since		20130928
		@var		$html
	**/
	public $html;

	/**
		@brief		OUTPUT: Collection of JS files that should be loaded.
		@since		20131010
		@var		$js
	**/
	public $js;

	/**
		@brief		INPUT: The Wordpress Post object for this meta box.
		@since		20130928
		@var		$post
	**/
	public $post;

	/**
		@brief		INPUT: ID of the post. Convenience property.
		@since		20131005
		@var		$post_id
	**/
	public $post_id;

	public function __construct()
	{
		$this->css = new collection;
		$this->html = new html;
		$this->html->data = $this;
		$this->js = new collection;
		$this->inputs_to_convert_later = new collection;
	}

	/**
		@brief		convert_form_input_later
		@since		2014-07-01 10:05:10
	**/
	public function convert_form_input_later( $input_name )
	{
		if ( ! $this->html->has( $input_name ) )
			$this->html->set( $input_name, '' );
		$this->inputs_to_convert_later->set( $input_name, $input_name );
	}

	/**
		@brief		Convert the form inputs to strings.
		@since		2014-07-01 10:06:01
	**/
	public function convert_form_inputs_now()
	{
		foreach( $this->inputs_to_convert_later as $input_name )
		{
			// Check that the html box still wants this input converted.
			if ( ! $this->html->has( $input_name ) )
				continue;
			// Check that the input exists.
			if ( ! $this->form->input( $input_name ) )
				continue;
			// Convert the input to a string.
			$this->html->set( $input_name, $this->form->input( $input_name ) . '' );
		}
	}
}
