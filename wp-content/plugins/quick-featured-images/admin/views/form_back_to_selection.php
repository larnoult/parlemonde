<?php // print form for going back to the filter selection without loosing input data ?>
<h2><?php esc_html_e( 'Select filters and options again', 'quick-featured-images' ); ?></h2>
<p><?php esc_html_e( 'If you want to change your former selection just go back by clicking on this button.', 'quick-featured-images' ); ?></p>
<form method="post" action="<?php echo esc_url( admin_url( sprintf( 'admin.php?page=%s&step=select', $this->page_slug ) ) ); ?>">
	<p>
		<input type="hidden" name="image_id" value="<?php echo $this->selected_image_id; ?>" />
		<input type="hidden" name="action" value="<?php echo $this->selected_action; ?>" />
<?php
// remember selected filters if there are some
if ( $this->selected_filters ) {
	foreach ( $this->selected_filters as $filter ) {
?>
		<input type="hidden" name="filters[]" value="<?php echo $filter; ?>" />
<?php
	}
}
// remember selected multiple images if there are some
if ( $this->selected_multiple_image_ids ) {
	$v = implode( ',', $this->selected_multiple_image_ids );
?>
		<input type="hidden" name="multiple_image_ids" value="<?php echo $v; ?>" />
<?php
}
// remember selected replacement images if there are some
if ( $this->selected_old_image_ids ) {
	foreach ( $this->selected_old_image_ids as $v ) {
?>
		<input type="hidden" name="replacement_image_ids[]" value="<?php echo $v; ?>" />
<?php
	}
}
// remember selected options if there are some
if ( $this->selected_options ) {
	foreach ( $this->selected_options as $v ) {
?>
		<input type="hidden" name="options[]" value="<?php echo $v; ?>" />
<?php
	}
}
?>
		<?php wp_nonce_field( 'quickfi_start', $this->plugin_slug . '_nonce' ); ?>
		<input type="submit" class="button" value="<?php esc_attr_e( 'Modify selection', 'quick-featured-images' ); ?>" />
	</p>
</form>