jQuery(document).ready( function( $ )
{
	$( '#threewp_broadcast.postbox' ).broadcast_postbox();
	$( '#posts-filter' ).broadcast_post_bulk_actions();
	$( '#posts-filter td.3wp_broadcast a.broadcast.post' ).broadcast_post_actions();
	$( 'form.plainview_form_auto_tabs' ).plainview_form_auto_tabs();
} );
