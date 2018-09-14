<?php 

extract($args);

?>
<div id="fieldmaster-upgrade-wrap" class="wrap">
	
	<h1><?php _e("FieldMaster Database Upgrade",'fieldmaster'); ?></h1>
	
<?php if( $updates ): ?>
	
	<p><?php _e('Reading upgrade tasks...', 'fieldmaster'); ?></p>
	
	<p class="show-on-ajax"><i class="fieldmaster-loading"></i> <?php printf(__('Upgrading data to version %s', 'fieldmaster'), $plugin_version); ?></p>
	
	<p class="show-on-complete"><?php echo sprintf( __('Database Upgrade complete. <a href="%s">See what\'s new</a>', 'fieldmaster' ), admin_url('edit.php?post_type=fm-field-group&page=fieldmaster-settings-info') ); ?></p>

	<style type="text/css">
		
		/* hide show */
		.show-on-ajax,
		.show-on-complete {
			display: none;
		}		
		
	</style>
	
	<script type="text/javascript">
	(function($) {
		
		var upgrader = {
			
			init: function(){
				
				// reference
				var self = this;
				
				
				// allow user to read message for 1 second
				setTimeout(function(){
					
					self.upgrade();
					
				}, 1000);
				
				
				// return
				return this;
			},
			
			upgrade: function(){
				
				// reference
				var self = this;
				
				
				// show message
				$('.show-on-ajax').show();
				
				
				// get results
			    var xhr = $.ajax({
			    	url: '<?php echo admin_url('admin-ajax.php'); ?>',
					dataType: 'json',
					type: 'post',
					data: {
						action:	'fieldmaster/admin/db_update',
						nonce: '<?php echo wp_create_nonce('fieldmaster_db_update'); ?>'
					},
					success: function( json ){
						
						// vars
						var message = fieldmaster.get_ajax_message(json);
						
						
						// bail early if no message text
						if( !message.text ) {
							
							return;
							
						}
						
						
						// show message
						$('.show-on-ajax').html( message.text );
						
					},
					complete: function( json ){
						
						// remove spinner
						$('.fieldmaster-loading').hide();
						
						
						// show complete
						$('.show-on-complete').show();
						
					}
				});
				
				
			}
			
		}.init();
				
	})(jQuery);	
	</script>
	
<?php else: ?>

	<p><?php _e('No updates available.', 'fieldmaster'); ?></p>
	
<?php endif; ?>

</div>