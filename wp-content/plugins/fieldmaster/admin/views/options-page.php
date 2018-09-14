<?php 

// extract
extract($args);
		
?>
<div class="wrap fieldmaster-settings-wrap">
	
	<h1><?php echo $page_title; ?></h1>
	
	<form id="post" method="post" name="post">
		
		<?php 
		
		// render post data
		fieldmaster_form_data(array( 
			'post_id'	=> $post_id, 
			'nonce'		=> 'options',
		));
		
		wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
		wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
		
		?>
		
		<div id="poststuff">
			
			<div id="post-body" class="metabox-holder columns-<?php echo 1 == get_current_screen()->get_columns() ? '1' : '2'; ?>">
				
				<div id="postbox-container-1" class="postbox-container">
					
					<?php do_meta_boxes('fieldmaster_options_page', 'side', null); ?>
						
				</div>
				
				<div id="postbox-container-2" class="postbox-container">
					
					<?php do_meta_boxes('fieldmaster_options_page', 'normal', null); ?>
					
				</div>
			
			</div>
			
			<br class="clear">
		
		</div>
		
	</form>
	
</div>