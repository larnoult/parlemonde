<h2><?php esc_html_e( 'Your selection', 'quick-featured-images' ); ?></h2>
<?php
$two_columns = false;
if ( in_array( $this->selected_action, array_keys( $this->valid_actions ) ) && $this->selected_image_id ) {
	$two_columns = true;
?>
<div class="qfi_wrapper">
	<div class="qfi_w50percent">
		<h3><?php esc_html_e( 'Your selected thumbnail', 'quick-featured-images' ); ?></h3>
		<p>
		<?php echo wp_get_attachment_image( $this->selected_image_id, 'thumbnail' ); ?><br>
		<strong><?php esc_html_e( 'Image title', 'quick-featured-images' ); ?>:</strong> <?php echo get_the_title( $this->selected_image_id ); ?>
		</p>
		<p><a class="button" href="<?php echo esc_url( admin_url( sprintf( 'admin.php?page=%s', $this->page_slug ) ) );?>"><?php esc_html_e( 'If wrong image start again', 'quick-featured-images' );?></a></p>
	</div><!-- .qfi_w50percent -->
	<div class="qfi_w50percent">
<?php
} elseif ( in_array( $this->selected_action, array_keys( $this->valid_actions_multiple_images ) ) && $this->selected_multiple_image_ids ) {
	$two_columns = true;
?>
<div class="qfi_wrapper">
	<div class="qfi_w50percent">
		<h3><?php esc_html_e( 'Your selected thumbnails', 'quick-featured-images' ); ?></h3>
		<ul class="selected_images">
<?php
	$size = array( 60, 60 );
	$attr = array( 'class' => 'attachment-thumbnail' );
	foreach( $this->selected_multiple_image_ids as $attachment_id ) {
?>			<li><?php echo wp_get_attachment_image( $attachment_id, $size, false, $attr ); ?></li>
<?php
	} // foreach()
?>
		</ul>
<?php
?>
		<p><a class="button" href="<?php echo esc_url( admin_url( sprintf( 'admin.php?page=%s', $this->page_slug ) ) );?>"><?php esc_html_e( 'If wrong image start again', 'quick-featured-images' );?></a></p>
	</div><!-- .qfi_w50percent -->
	<div class="qfi_w50percent">
<?php
}
?>
		<h3><?php esc_html_e( 'Your selected action', 'quick-featured-images' ); ?></h3>
<?php
if ( isset( $this->valid_actions[ $this->selected_action ] ) ) {
	$selected_action = $this->valid_actions[ $this->selected_action ];
} elseif ( isset( $this->valid_actions_without_image[ $this->selected_action ] ) ) {
	$selected_action = $this->valid_actions_without_image[ $this->selected_action ];
} elseif ( isset( $this->valid_actions_multiple_images[ $this->selected_action ] ) ) {
	$selected_action = $this->valid_actions_multiple_images[ $this->selected_action ];
} else {
	$selected_action = __( 'You have not selected an action.', 'quick-featured-images' );
}
?>
		<p><?php echo esc_html( $selected_action ); ?></p>
		<p><a class="button" href="<?php echo esc_url( admin_url( sprintf( 'admin.php?page=%s', $this->page_slug ) ) );?>"><?php esc_html_e( 'If wrong action start again', 'quick-featured-images' );?></a></p>
<?php
if ( $two_columns ) {
?>
	</div><!-- .qfi_w50percent -->
</div><!-- .qfi_wrapper -->
<?php
}
// don't show on selection page
if ( 'select' != $this->selected_step ) {
?>
<div class="qfi_wrapper">
	<div class="qfi_w50percent">
		<h3><?php esc_html_e( 'Your selected options', 'quick-featured-images' ); ?></h3>
<?php 
	if ( $this->selected_options ) {
?>
		<ul>
<?php 
		foreach ( $this->selected_options as $option ) {
?>
			<li><?php echo $this->valid_options[ $option ]; ?></li>
<?php 
		}
?>
		</ul>
<?php 
	} else {
?>
		<p><?php esc_html_e( 'No selected options', 'quick-featured-images' ); ?></p>
<?php 
}
?>
	</div><!-- .qfi_w50percent -->
	<div class="qfi_w50percent">
		<h3><?php esc_html_e( 'Your selected filters', 'quick-featured-images' ); ?></h3>
<?php 
	if ( $this->selected_filters ) {
?>
		<ul>
<?php 
		foreach ( $this->selected_filters as $filter ) {
?>
			<li><?php echo $this->valid_filters[ $filter ]; ?></li>
<?php 
		}
?>
		</ul>
<?php 
	} else {
?>
		<p><?php esc_html_e( 'No selected filters', 'quick-featured-images' ); ?></p>
<?php 
	}
?>
	</div><!-- .qfi_w50percent -->
</div><!-- .qfi_wrapper -->
<?php
} // if ( 'select' != $this->selected_step )
