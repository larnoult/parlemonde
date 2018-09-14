/**
	@brief		Subclass for handling of post bulk actions.
	@since		2014-10-31 23:15:10
**/
;(function( $ )
{
    $.fn.extend(
    {
        broadcast_post_actions: function()
        {
            return this.each( function()
            {
                var $this = $( this );

				// Don't add bulk post options several times.
				if( $this.data( 'broadcast_post_actions' ) !== undefined )
					return;
				$this.data( 'broadcast_post_actions', true )

                $this.submitted = false;

                $this.unbind( 'click' );

                $this.click( function()
                {
                	// Get the post ID.
                	$tr = $this.parentsUntil( 'tbody#the-list' ).last();
                	var id = $tr.prop( 'id' ).replace( 'post-', '' );

                	$this.$popup = broadcast_popup({
                			'callbacks' : {
                				'close' : function()
                				{
                					if ( ! $this.submitted )
                						return;
                					// Reload the page by submitting the filter.
									$( '#post-query-submit' ).click();
                				}
                			},
                		})
						.set_title( broadcast_strings.post_actions )
						.open();

					$this.fetch_form( {
						'action' : 'broadcast_post_action_form',
						'nonce' : $this.data( 'nonce' ),
						'post_id' : id,
					} );
                } );

                $this.display_form = function( json )
                {
					$this.$popup.set_content( json.html );

					// Take over the submit button.
					var $form = $( '#broadcast_post_action_form' );
					$( 'input.submit', $form ).click( function()
 					{
 						$this.submitted = true;
						// Assemble the form.
						$this.fetch_form( $form.serialize() + '&submit=submit' );
						return false;
					} );
                }

                /**
                	@brief		Fetch the form via ajax.
                	@since		2014-11-02 22:24:07
                **/
                $this.fetch_form = function( data )
                {
					$this.$popup.set_content( 'Loading...' );

                	// Fetch the post link editor.
                	$.ajax( {
                		'data' : data,
                		"dataType" : "json",
                		'type' : 'post',
                		'url' : ajaxurl,
                	} )
                	.done( function( data )
                	{
                		$this.display_form( data );
                	} )
					.fail( function( jqXHR )
					{
						$this.$popup
							.set_content( jqXHR.responseText )
							.set_title( 'Ajax error' );
					} );
                }
            }); // return this.each( function()
        } // plugin: function()
    }); // $.fn.extend({
} )( jQuery );
