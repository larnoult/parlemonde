<h2><?php esc_html_e( 'Refine your selection', 'quick-featured-images' ); ?></h2>
<?php
// display selected filters
if ( $this->selected_filters ) {
?>
<form method="post" action="<?php echo esc_url( admin_url( sprintf( 'admin.php?page=%s&amp;step=confirm', $this->page_slug ) ) ); ?>">
	<fieldset>
		<legend><span><?php esc_html_e( 'Refine filters', 'quick-featured-images' ); ?></span></legend>
		<p><?php esc_html_e( 'Now you can find posts and pages by matching parameters. Refine them here.', 'quick-featured-images' ); ?></p>
		<p><?php esc_html_e( 'Whatever you do: You can confirm your choice on the next page.', 'quick-featured-images' ); ?></p>
<?php
	foreach ( $this->selected_filters as $filter ) {
		$filename = $filter . '.php';
		if ( file_exists( dirname( __FILE__ ) . '/' . $filename ) ) {
			include_once( $filename );
		} else {
?>
		<p><?php printf( esc_html__( 'File %s is not available.', 'quick-featured-images' ), $filename ); ?></p>
<?php
		}
?>
	<input type="hidden" name="filters[]" value="<?php echo $filter; ?>" />
<?php
	}
?>
	</fieldset>
	<p>
<?php
// remember selected options if there are some
if ( $this->selected_options ) {
	foreach ( $this->selected_options as $v ) {
?>
		<input type="hidden" name="options[]" value="<?php echo $v; ?>" />
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
?>
		<input type="hidden" name="image_id" value="<?php echo $this->selected_image_id; ?>" />
		<input type="hidden" name="action" value="<?php echo $this->selected_action; ?>" />
		<?php wp_nonce_field( 'quickfi_refine', $this->plugin_slug . '_nonce' ); ?>
		<input type="submit" class="button" value="<?php esc_attr_e( 'Preview filtering', 'quick-featured-images' ); ?>" />
	</p>
</form>
<?php
} else {
?>
	<p><?php esc_html_e( 'There are no selected filters. Modify your filter selection or just go on by clicking on the next button.', 'quick-featured-images' ); ?></p>
<?php
}// if() 
?>
