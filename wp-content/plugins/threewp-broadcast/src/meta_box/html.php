<?php

namespace threewp_broadcast\meta_box;

class html
	extends \plainview\sdk_broadcast\collections\collection
{
	/**
		@brief		The [meta box] data container.
		@since		20131027
	**/
	public $data;

	/**
		@brief		An array of keys signifying that the section should be added without any enclosing html.
		@since		2016-07-06 23:08:01
	**/
	public $raw_html = [];

	public function __toString()
	{
		$r = '';
		foreach( $this->items as $key => $value )
		{
			// Display just the html without enclosing it in a section?
			if ( isset( $this->raw_html[ $key ] ) )
				$r .= sprintf( '%s', $value );
			else
				$r .= sprintf( '<div class="%s html_section">%s</div>', $key, $value );
		}
		return $r;
	}

	/**
		@brief		Add this html section to the raw list.
		@since		2016-07-06 23:07:39
	**/
	public function raw_html_section( $key )
	{
		$this->raw_html[ $key ] = true;
	}

	/**
		@brief		Converts any item objects to strings.
		@since		20131027
	**/
	public function render()
	{
		foreach( $this->items as $item )
		{
			if ( ! item::is_a( $item ) )
				continue;
			$item->meta_box_data_prepared();
		}
		return $this->__toString();
	}
}