<form role="search" method="get" class="form-search" action="<?php echo esc_url( home_url('/') ); ?>">
	<label>
		<span class="screen-reader-text"><?php esc_html_e( 'Search for:', 'virtue' ); ?></span>
		<input type="text" value="<?php if ( is_search() ) { echo esc_attr( get_search_query() ); } ?>" name="s" class="search-query" placeholder="<?php esc_html_e( 'Search', 'virtue' ); ?>">
	</label>
	<button type="submit" class="search-icon"><i class="icon-search"></i></button>
</form>