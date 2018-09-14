/**
	@brief		Subclass for handling of post bulk actions.
	@since		2014-10-31 23:15:10
**/
;(function( $ )
{
    $.fn.extend(
    {
        broadcast_post_bulk_actions: function()
        {
            return this.each( function()
            {
                var $this = $( this );

                /**
					@brief		Mark the bulkactions section as busy.
					@since		2014-11-01 23:43:52
				**/
				$this.busy = function( busy )
				{
					if ( busy )
						$( '.bulkactions' ).fadeTo( 250, 0.5 );
					else
						$( '.bulkactions' ).fadeTo( 250, 1 );
				}

				/**
					@brief		Return a string with all of the selected post IDs.
					@since		2014-10-31 23:15:48
				**/
				$this.get_ids = function()
				{
					var post_ids = [];
					// Get all selected rows
					var $inputs = $( '#posts-filter tbody#the-list th.check-column input:checked' );
					$.each( $inputs, function( index, item )
					{
						var $item = $( item );
						var $row = $( item ).parentsUntil( 'tr' ).parent();
						// Add it
						var id = $row.prop( 'id' ).replace( 'post-', '' );
						post_ids.push( id );
					} );
					return post_ids.join( ',' );
				}

				if ( typeof broadcast_bulk_post_actions === "undefined" )
					return;

				// Don't add bulk post options several times.
				if( $this.data( 'broadcast_post_bulk_actions' ) !== undefined )
					return;
				$this.data( 'broadcast_post_bulk_actions', true )

				// Begin by adding the broadcast optgroup.
				var $select = $( '.bulkactions select' );
				var $optgroup = $( '<optgroup>' );

				$.each( broadcast_bulk_post_actions, function( index, item )
				{
					var $option = $( '<option>' );
					$option.html( item.name );
					$option.prop( 'value', index );
					$option.addClass( 'broadcast' );
					$option.appendTo( $optgroup );
				} );

				// We appendTo here because otherwise it is only put in one place.
				$optgroup.prop( 'label', broadcast_strings.broadcast );
				$optgroup.appendTo( $select );

				// Take over the apply buttons
				$( '.button.action' )
				.click( function()
				{
					// What is the current selection?
					var $container = $( this ).parent();
					var $select = $( 'select', $container );

					var $selected = $( 'option:selected', $select );

					// Not a broadcast bulk post action = allow the button to work normally.
					if ( ! $selected.hasClass( 'broadcast' ) )
						return true;

					// Has the user selected any posts?
					var post_ids = $this.get_ids();
					if ( post_ids == '' )
					{
						broadcast_popup()
							.set_title( 'No posts selected' )
							.set_content( 'Please select at least one post to use the Broadcast bulk actions.' )
							.open();
						return false;
					}

					// Retrieve the action.
					var value = $selected.prop( 'value' );
					var action = broadcast_bulk_post_actions[ value ];
					// Use the callback.
					$this.busy( true );
					action.callback( $this );
					return false;
				} );

            }); // return this.each( function()
        } // plugin: function()
    }); // $.fn.extend({
} )( jQuery );
