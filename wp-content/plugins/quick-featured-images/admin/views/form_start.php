<?php 
if ( ! current_theme_supports( 'post-thumbnails' ) ) {
?>
<h2><?php esc_html_e( 'Notice', 'quick-featured-images' ); ?></h2>
<div class="qfi_content_inside">
	<p class="failure"><?php esc_html_e( 'The current theme does not support featured images. Anyway you can use this plugin. The effects are stored and will be visible in a theme which supports featured images.', 'quick-featured-images' ); ?></p>
</div>
<?php 
}
?>
<form method="post" action="<?php echo esc_url( admin_url( sprintf( 'admin.php?page=%s&step=select', $this->page_slug ) ) ); ?>">
	<h2 class="no-bottom"><?php esc_html_e( 'What do you want to do?', 'quick-featured-images' ); ?></h2>
	<div class="qfi_page_description">
		<p><?php esc_html_e( 'Here you can add, replace and delete featured images to your posts. Select one of the following actions and, if necessary, one or more images.', 'quick-featured-images' ); ?></p>
		<p><?php esc_html_e( 'Whatever you select: You can refine your choice on the next page.', 'quick-featured-images' ); ?></p>
	</div>
	<h3><?php esc_html_e( 'Important advice', 'quick-featured-images' ); ?></h3>
	<p><strong><?php esc_html_e( 'There is no undo function! It is strongly recommended that you make a backup of the WordPress database before you perform mass changes.', 'quick-featured-images' ); ?></strong></p>
	<fieldset>
		<legend class="screen-reader-text"><span><?php esc_html_e( 'Select action', 'quick-featured-images' ); ?></span></legend>
		<h3><?php esc_html_e( 'Actions with a single image', 'quick-featured-images' ); ?></h3>
		<p><?php esc_html_e( 'These actions require to select an image with the following button.', 'quick-featured-images' ); ?></p>
<?php 
foreach ( $this->valid_actions as $name => $label ) {
?>
		<p>
			<input type="radio" id="<?php echo $name; ?>" name="action" value="<?php echo $name; ?>" <?php checked( 'assign' == $name ); ?> />
			<label for="<?php echo $name; ?>"><strong><?php echo esc_html( $label ); ?></strong></label>
		</p>
<?php
} // foeach( valid_actions )
?>
		<div class="qfi_wrapper">
			<div class="qfi_w50percent">
				<p><?php esc_html_e( 'Select the image you want to add to, replace or delete from posts and pages by clicking on the following button.', 'quick-featured-images' ); ?></p>
				<p>
<?php
// default values for image element
$blank_img_url = includes_url() . 'images/blank.gif';
$img_url = $blank_img_url;
$img_class = '';
$img_style = '';
// if an image id was given
if ( $this->selected_image_id ) {
	$arr_image = wp_get_attachment_image_src( $this->selected_image_id );
	// and if there is an valid image
	if ( $arr_image ) {
		// show the image and set the id as param value
		$img_url = $arr_image[0];
		$img_class = 'attachment-thumbnail';
		$img_style = sprintf( 'width:%dpx', $this->used_thumbnail_width );
	}
}
?>
					<input type="hidden" id="image_id" name="image_id" value="<?php echo $this->selected_image_id; ?>">
					<img id="selected_image" src="<?php echo $img_url; ?>" alt="<?php $text = 'Featured Image'; echo esc_attr( _x( $text, 'post' ) ); ?>" class="<?php echo $img_class; ?>" style="<?php echo $img_style; ?>" /><br />
					<input type="button" id="upload_image_button" class="button qfi_select_image" value="<?php esc_attr_e( 'Choose Image', 'quick-featured-images' ); ?>" />
				</p>
			</div>
			<div class="qfi_w50percent">
				<p><strong><?php esc_html_e( 'If the button does not work, read this:', 'quick-featured-images' ); ?></strong></p>
				<p><?php esc_html_e( 'Some users reported that this button would not work in some WordPress installations. If this should be the case you can take another way:', 'quick-featured-images' ); ?></p>
				<p><?php esc_html_e( '1. Go to the media library. 2. Move the mouse over the desired image. Further links are appearing, among them the link &quot;Bulk set as featured image&quot;. 3. After a click on it you can move on in this plugin.', 'quick-featured-images' ); ?></p>
			</div>
		</div>

		<h3><?php esc_html_e( 'Actions with multiple images', 'quick-featured-images' ); ?></h3>
		<p><?php esc_html_e( 'These actions require at least one selected image with the following button.', 'quick-featured-images' ); ?></p>
<?php
foreach ( $this->valid_actions_multiple_images as $name => $label ) {
?>
		<p>
			<input type="radio" id="<?php echo $name; ?>" name="action" value="<?php echo $name; ?>" <?php checked( 'assign' == $name ); ?> />
			<label for="<?php echo $name; ?>"><strong><?php echo esc_html( $label ); ?></strong></label>
		</p>
<?php
} // foreach( valid_actions_multiple_images )
$img_ids = is_array( $this->selected_multiple_image_ids ) ? implode( ',', $this->selected_multiple_image_ids ) : '';
?>
<p><?php esc_html_e( 'To select multiple images click on the button and use the CTRL key while clicking on the images.', 'quick-featured-images' ); ?></p>
<p>
	<input type="hidden" id="multiple_image_ids" name="multiple_image_ids" value="<?php echo $img_ids; ?>">
	<img id="blank_image" src="<?php echo $blank_img_url; ?>" alt="" /><br />
	<input type="button" id="select_images_multiple" class="button" value="<?php esc_attr_e( 'Choose Images', 'quick-featured-images' ); ?>" />
</p>
<?php
if ( $this->selected_multiple_image_ids ) {
?>
<ul class="selected_images">
<?php
	$size = array( 60, 60 );
	foreach( $this->selected_multiple_image_ids as $attachment_id ) {
?>	<li><?php echo wp_get_attachment_image( $attachment_id, $size ); ?></li>
<?php
	} // foreach()
?>
</ul>
<?php
} // if ( $this->selected_multiple_image_ids )
?>
		<h3><?php esc_html_e( 'Actions without any selected image', 'quick-featured-images' ); ?></h3>
		<p><?php esc_html_e( 'These actions do not require a selected image.', 'quick-featured-images' ); ?></p>
<?php
foreach ( $this->valid_actions_without_image as $name => $label ) {
?>
		<p>
			<input type="radio" id="<?php echo $name; ?>" name="action" value="<?php echo $name; ?>" <?php checked( 'assign' == $name ); ?> />
			<label for="<?php echo $name; ?>"><strong><?php echo esc_html( $label ); ?></strong></label>
		</p>
<?php
}
?>
		<p class="qfi_ad_for_pro"><?php esc_html_e( 'Do you want to assign the first image of each post?', 'quick-featured-images' ); ?> <?php esc_html_e( 'Get the premium version', 'quick-featured-images' ); ?> <a href="https://www.quickfeaturedimages.com<?php esc_html_e( '/', 'quick-featured-images' ); ?>">Quick Featured Images Pro</a>.</p>
	</fieldset>
<?php 
wp_nonce_field( 'quickfi_start', $this->plugin_slug . '_nonce' );
$text = 'Next &raquo;';
submit_button( __( $text ), 'secondary' );
?>
	<input type="hidden" id="selection_advice" name="selection_advice" value="<?php esc_attr_e( 'Use CTRL for multiple choice', 'quick-featured-images' ); ?>" />
</form>
