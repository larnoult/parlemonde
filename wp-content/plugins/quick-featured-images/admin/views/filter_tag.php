<h3><?php echo esc_html( $this->valid_filters[ 'filter_tag' ] ); ?></h3>
<p>
<?php 
$tags = get_tags();
if ( $tags ) {
?>
	<label for="qfi_tags"><?php esc_html_e( 'Select a tag', 'quick-featured-images' ); ?></label><br />
	<select id="qfi_tags" name="tag_id">
<?php 
	echo $this->get_html_empty_option();
	foreach ( $tags as $tag ) {
?>
		<option value="<?php echo $tag->term_id; ?>" <?php selected( $this->selected_tag_id == $tag->term_id ); ?>><?php echo esc_html( $tag->name ); ?></option>
<?php 
	}
?>
	</select>
<?php 
} else {
	esc_html_e( 'There are no tags in use.', 'quick-featured-images' );
}
?>
</p>