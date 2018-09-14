/**
	@brief		Offer a popup SDK, based on Magnific.
	@since		2014-11-02 10:25:38
**/
broadcast_popup = function( options )
{
	$ = jQuery;

	this.$popup = undefined;
	this.$content = $( '<div>' );
	this.$title = $( '<h1>' );
	this.options = options;

	/**
		@brief		Clear the content.
		@since		2015-07-07 21:33:35
	**/
	this.clear_content = function()
	{
		this.$content.empty();
	}

	/**
		@brief		Close the popup.
		@since		2014-11-02 11:06:07
	**/
	this.close = function()
	{
		$.magnificPopup.instance.close();
		return this;
	}

	/**
		@brief		Create the div.
		@since		2014-11-02 11:03:43
	**/
	this.create_div = function()
	{
		$( '.broadcast_popup' ).remove();
		this.$popup = $( '<div>' )
			.addClass( 'mfp-hide broadcast_popup' )
			.appendTo( $( 'body' ) );
		this.$title.appendTo( this.$popup );
		this.$content.appendTo( this.$popup );
		return this;
	}

	/**
		@brief		Open the popup.
		@since		2014-11-02 11:03:33
	**/
	this.open = function()
	{
		options = $.extend( this.options,
		{
			'items' :
			{
				'overflowY' : 'scroll',
				'src' : this.$popup,
				'type' : 'inline'
			}
		}
		);

		$.magnificPopup.open( options );
		return this;
	}

	/**
		@brief		Convenience function to set the popup's HTML content.
		@since		2014-11-02 11:10:15
	**/
	this.set_content = function( html )
	{
		this.$content.html( html );
		return this;
	}

	/**
		@brief		Obsolete: Sets the popup's HTML content.
		@since		2015-07-09 14:33:40
	**/
	this.set_html = function( html )
	{
		this.$content.html( html );
		return this;
	}


	/**
		@brief		Set a header 1 for the popup.
		@since		2014-11-02 14:52:37
	**/
	this.set_title = function( title )
	{
		this.$title.html( title );
		return this;
	}

	this.create_div();
	return this;
}
