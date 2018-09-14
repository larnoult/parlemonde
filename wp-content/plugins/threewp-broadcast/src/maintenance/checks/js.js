<script type="text/javascript">
	jQuery(document).ready(function( $ )
	{
		var $check;

		$check = $( '.threewp_broadcast_check' );
		if ( $check.length < 1 )
			return;

		var $next_link = $( '.next_step_link', $check );

		if ( $next_link.length < 1 )
			return;

		$next_link.hide();

		setInterval( function()
		{
			$check.append( '.' );
		}, 1000 );

		window.location = $( 'a', $next_link ).attr( 'href' );
	})
</script>
