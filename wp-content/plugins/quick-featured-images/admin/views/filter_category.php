<h3><?php echo esc_html( $this->valid_filters[ 'filter_category' ] ); ?></h3>
<p>
	<label for="category_id"><?php esc_html_e( 'Select a category', 'quick-featured-images' ); ?></label><br />
<?php 
$text = '&mdash; Select &mdash;';
$args = array(
	'name'		=> 'category_id',
	'selected'	=> $this->selected_category_id,
	'orderby'	=> 'NAME',
	'show_option_none' => __( $text ),
);
wp_dropdown_categories( $args ); 
?>
</p>
