<h2><?php esc_html_e( 'Results of the action', 'quick-featured-images' ); ?></h2>
<?php
if ( $results ) {
	if ( 'remove_orphaned' == $this->selected_action ) {
		// WP core labels
		$text 			  = 'Number of items found: %d';
		$number_found     = esc_html__( $text );
		$text             = 'Deleted!';
		$label_deleted    = esc_html( _x( $text, 'plugin' ) );
?> 
<p><?php printf( $number_found, $results ); ?></p>
<p><?php echo $label_deleted; ?> <img src="<?php echo esc_url( admin_url() ); ?>images/yes.png" alt="" width="16" height="16"></p>
<?php 
	} else {
		// translate once for multiple usage and improve performance
		$label_details 	  = esc_html__( 'Details', 'quick-featured-images' );
		$label_current_fi = esc_html__( 'Current Featured Image', 'quick-featured-images' );
		$label_number 	  = esc_html__( 'No.', 'quick-featured-images' );
		$label_changed 	  = esc_html__( 'Changed successfully', 'quick-featured-images' );
		$label_unchanged  = sprintf( '<span class="failure">%s</span>', esc_html__( 'Unchanged', 'quick-featured-images' ) );
		// WP core labels
		$text 			  = 'No image set';
		$label_no_image   = esc_html__( $text );
		$text             = '(no title)';
		$default_title    = esc_html__( $text );
?> 
<p><?php esc_html_e( 'The list is in alphabetical order according to post title. You can edit a post in a new window by clicking on its link in the list.', 'quick-featured-images' ); ?></p>
<table class="widefat">
	<thead>
		<tr>
			<th class="num"><?php echo $label_number; ?></th>
			<th><?php echo $label_details; ?></th>
			<th class="num"><?php echo $label_current_fi; ?></th>
		</tr>
	</thead>
	<tbody>
<?php
		$c = 1;
		foreach ( $results as $result ) {
			// post title, else default title
			$post_title = $result[ 1 ] ? esc_html( $result[ 1 ] ) : $default_title;
			// check if no featured image for the post, else add default
			$img = $result[ 2 ] ? $result[ 2 ] : $label_no_image;
			// get the result message per post
			$msg = $result[ 3 ] ? $label_changed : $label_unchanged;
			// alternating row colors with error class if error
			$row_classes = $result[ 3 ] ? '' : 'qfi-failure';
			if ( 0 != $c % 2 ) { // if $c is divisible by 2 (so the modulo is 0)
				$row_classes .= $row_classes ? ' alternate' : 'alternate';
			}
			if ( $row_classes ) {
				$row_classes = ' class="' . $row_classes . '"';
			}
			// print the table row
			printf( '<tr%s>', $row_classes );
			printf( '<td class="num">%d</td>', $c );
			printf( 
				'<td><a href="%s" target="_blank">%s</a><br>%s</td>', 
				esc_url( $result[ 0 ] ), // edit post link
				$post_title,
				$msg
			);
			printf( '<td class="num">%s</td>', $img );
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
		</tr>
	</tfoot>
</table>
<?php 
	} // if ( 'remove_orphaned' == $this->selected_action )
} else { 
?>
<p><?php esc_html_e( 'No matches found.', 'quick-featured-images' ); ?></p>
<?php 
}
?>
<p><a class="button" href="<?php echo esc_url( admin_url( sprintf( 'admin.php?page=%s', $this->page_slug ) ) );?>"><?php esc_html_e( 'Start again', 'quick-featured-images' );?></a></p>
<h2><?php esc_html_e( 'Do you like the plugin?', 'quick-featured-images' ); ?></h2>
<p><a href="http://wordpress.org/support/view/plugin-reviews/quick-featured-images"><?php esc_html_e( 'Please rate it at wordpress.org!', 'quick-featured-images' ); ?></a></p>
