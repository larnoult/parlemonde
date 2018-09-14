var $ = jQuery;
var data = %s;
data[ "post_ids" ] = broadcast_post_bulk_actions.get_ids();
$.ajax( {
	"data" : data,
	"dataType" : "json",
	"type" : "post",
	"url" : ajaxurl,
} )
.done( function( data )
{
	// DEBUG
	// broadcast_post_bulk_actions.busy( false ); return;
	// Click the filter button to reload the page
	$( "#post-query-submit" ).click();
} )
.fail( function( jqXHR )
{
	broadcast_popup()
		.set_content( jqXHR.responseText )
		.set_title( 'Ajax error' )
		.open();
	broadcast_post_bulk_actions.busy( false );
} );
