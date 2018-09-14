<h2><?php esc_html_e( 'Preview of your selection', 'quick-featured-images' ); ?></h2>
<h3><?php printf( esc_html__( '%d matches found', 'quick-featured-images' ), sizeof( $results ) ); ?></h3>
<?php 
if ( $results ) { 
	if ( 'remove_orphaned' == $this->selected_action ) {
		// WP core labels
		$text 			  = 'Delete';
		$label_apply      = esc_attr__( $text );
		$text 			  = 'Cancel';
		$label_cancel     = esc_html__( $text );
		// QFI strings
		$question = __( 'Should the entries of featured images without image files be removed from the database?', 'quick-featured-images' );
	} else {
		// translate once for multiple usage and improve performance
		$label_details 	  = esc_html__( 'Details', 'quick-featured-images' );
		$label_number 	  = esc_html__( 'No.', 'quick-featured-images' );
		$label_current_fi = esc_html__( 'Current Featured Image', 'quick-featured-images' );
		$label_future_fi  = esc_html__( 'Future Featured Image', 'quick-featured-images' );
		$label_written_on = esc_html__( 'written on', 'quick-featured-images' );
		$label_by         = esc_html__( 'by', 'quick-featured-images' );
		// WP core labels
		$text 			  = 'No image set';
		$label_no_image   = esc_html__( $text );
		$text 			  = 'Status:';
		$label_status     = esc_html__( $text );
		$text 			  = 'Apply';
		$label_apply      = esc_attr__( $text );
		$text 			  = 'Cancel';
		$label_cancel     = esc_html__( $text );
		$text             = '(no title)';
		$default_title    = esc_html__( $text );
		// QFI strings
		switch ( $this->selected_action ) {
			case 'assign':
				$question = __( 'Should the selected image be set as featured image to all listed posts?', 'quick-featured-images' );
				break;
			case 'assign_randomly':
				$question = __( 'Should the selected images be set randomly as featured images to all listed posts?', 'quick-featured-images' );
				break;
			case 'replace':
				$question = __( 'Should the current set featured image be replaced by the selected image at all listed posts?', 'quick-featured-images' );
				break;
			case 'remove':
				$question = __( 'Should the selected image be removed from all listed posts?', 'quick-featured-images' );
				break;
			case 'assign_first_img':
				$question = __( 'Should the future images be set as featured images at all listed posts?', 'quick-featured-images' );
				break;
			case 'remove_any_img':
				$question = __( 'Should the added featured images be removed from all listed posts?', 'quick-featured-images' );
				break;
		} // switch()
?>
<p><?php esc_html_e( 'The list is in alphabetical order according to post title. You can edit a post in a new window by clicking on its link in the list.', 'quick-featured-images' ); ?></p>
<table class="widefat">
	<thead>
		<tr>
			<th class="num"><?php echo $label_number; ?></th>
			<th><?php echo $label_details; ?></th>
			<th class="num"><?php echo $label_current_fi; ?></th>
			<th class="num"><?php echo $label_future_fi; ?></th>
		</tr>
	</thead>
	<tbody>
<?php
		$c = 1;
		foreach ( $results as $result ) {
			// alternating row colors: if $c is divisible by 2 (so the modulo is 0) then set 'alt'-class
			$row_classes = ( 0 != $c % 2 ) ? ' class="alternate"' : '';
			// post title, else default title
			$post_title = $result[ 1 ] ? esc_html( $result[ 1 ] ) : $default_title;
			// post date
			$post_date = sprintf( '%s %s', $label_written_on, esc_html( $result[ 2 ] ) );
			// post author
			$post_author = sprintf( '%s %s', $label_by, esc_html( $result[ 3 ] ) );
			// post type label
			$post_type = $result[ 7 ];
			$post_type_obj = get_post_type_object( $post_type );
			if ( $post_type_obj ) {
				$post_type = $post_type_obj->labels->singular_name; // readable name
			}
			// post status
			$post_status = isset( $this->valid_statuses[ $result[ 6 ] ] ) ? $this->valid_statuses[ $result[ 6 ] ] : $result[ 6 ];
			// check if no featured image for the post, else add default
			$current_img = $result[ 4 ] ? $result[ 4 ] : $label_no_image;
			$future_img = $result[ 5 ] ? $result[ 5 ] : $label_no_image;
			// print the table row
			printf( '<tr%s>', $row_classes );
			printf( '<td class="num">%d</td>', $c );
			printf( 
				'<td><a href="%s" target="_blank">%s</a><br>%s<br>%s<br>%s, %s %s</td>',
				esc_url( $result[ 0 ] ), // edit post link
				$post_title,
				$post_date,
				$post_author,
				esc_html( $post_type ),
				$label_status,
				esc_html( $post_status )
			);
			printf( '<td class="num">%s</td>', $current_img );
			printf( '<td class="num">%s</td>', $future_img );
			print "</tr>\n";
			// increase counter
			$c++;
		}
?>
	</tbody>
	<tfoot>
		<tr>
			<th class="num"><?php echo $label_number; ?></th>
			<th><?php echo $label_details; ?></th>
			<th class="num"><?php echo $label_current_fi; ?></th>
			<th class="num"><?php echo $label_future_fi; ?></th>
		</tr>
	</tfoot>
</table>
<?php
	} // if ( 'remove_orphaned' == $this->selected_action )
?>
<h2><?php esc_html_e( 'Confirm the change', 'quick-featured-images' ); ?></h2>
<p><?php echo esc_html( $question ); ?> <?php esc_html_e( 'You can not undo the operation!', 'quick-featured-images' ); ?></p>
<form method="post" action="<?php echo esc_url( admin_url( sprintf( 'admin.php?page=%s&step=perform', $this->page_slug ) ) ); ?>">
	<p>
		<input type="hidden" name="image_id" value="<?php echo $this->selected_image_id; ?>" />
		<input type="hidden" name="action" value="<?php echo $this->selected_action; ?>" />
<?php 
if ( $this->selected_multiple_image_ids ) {
	$v = implode( ',', $this->selected_multiple_image_ids );
?>
		<input type="hidden" name="multiple_image_ids" value="<?php echo $v; ?>" />
<?php
}
if ( $this->selected_filters ) {
	foreach ( $this->selected_filters as $v ) {
?>
		<input type="hidden" name="filters[]" value="<?php echo $v; ?>" />
<?php
	}
}
foreach ( $this->selected_post_types as $v ) {
?>
		<input type="hidden" name="post_types[]" value="<?php echo $v; ?>" />
<?php 
}
if ( $this->selected_options ) {
	foreach ( $this->selected_options as $v ) {
?>
		<input type="hidden" name="options[]" value="<?php echo $v; ?>" />
<?php
	}
}
if ( $this->selected_search_term ) {
?>
		<input type="hidden" name="search_term" value="<?php echo $this->selected_search_term; ?>" />
<?php 
}
if ( $this->selected_category_id ) {
?>
		<input type="hidden" name="category_id" value="<?php echo $this->selected_category_id; ?>" />
<?php 
}
if ( $this->selected_tag_id ) {
?>
		<input type="hidden" name="tag_id" value="<?php echo $this->selected_tag_id; ?>" />
<?php 
}
if ( $this->selected_old_image_ids ) {
	foreach ( $this->selected_old_image_ids as $k => $v ) {
?>
		<input type="hidden" name="replacement_image_ids[<?php echo $k; ?>]" value="<?php echo $v; ?>" />
<?php
	}
}
?>
		<?php wp_nonce_field( 'quickfi_confirm', $this->plugin_slug . '_nonce' ); ?>
		<input type="submit" class="button-primary" value="<?php echo $label_apply; ?>" /> <a class="button" href="<?php echo esc_url( admin_url( sprintf( 'admin.php?page=%s', $this->page_slug ) ) );?>"><?php echo $label_cancel;?></a>
	</p>
</form>
<?php
} else { 
?>
<p><a class="button" href="<?php echo esc_url( admin_url( sprintf( 'admin.php?page=%s', $this->page_slug ) ) );?>"><?php esc_html_e( 'Start again', 'quick-featured-images' );?></a> <?php esc_html_e( 'or refine your selection with the following form fields.', 'quick-featured-images' );?></p>
<?php
}