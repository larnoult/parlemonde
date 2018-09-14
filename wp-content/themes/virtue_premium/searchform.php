<?php global $virtue_premium; 
	if(!empty($virtue_premium['search_placeholder_text'])) {
		$searchtext = $virtue_premium['search_placeholder_text'];
	} else {
		$searchtext = __('Search', 'virtue');
	} ?>
	<form role="search" method="get" class="form-search" action="<?php echo home_url('/'); ?>">
  		<label>
  			<span class="screen-reader-text"><?php _e( 'Search for:', 'virtue' ); ?></span>
  			<input type="text" value="<?php if (is_search()) { echo esc_attr(get_search_query()); } ?>" name="s" class="search-query" placeholder="<?php echo esc_attr($searchtext); ?>">
  		</label>
  		<button type="submit" class="search-icon" aria-label="<?php echo __('Submit Search', 'virtue');?>"><i class="icon-search"></i></button>
	</form>