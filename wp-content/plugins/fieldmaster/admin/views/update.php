<?php 

extract($args);

?>
<div id="fields-upgrade-wrap" class="wrap">
	
	<h2><?php _e("FieldMaster Database Upgrade",'fields'); ?></h2>
	
<?php if( !empty($updates) ): ?>
	
	<p><?php _e('Reading upgrade tasks...', 'fields'); ?></p>
	
	<p class="show-on-ajax"><i class="fields-loading"></i> <?php printf(__('Upgrading data to version %s', 'fields'), $plugin_version); ?></p>
	
	<p class="show-on-complete"><?php _e('Database Upgrade complete', 'fields'); ?>. <a href="<?php echo admin_url('edit.php?post_type=fields-field-group&page=fields-settings-info'); ?>"><?php _e("See what's new",'fields'); ?></a>.</p>

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
						action:	'fields/admin/data_upgrade',
						nonce: '<?php echo wp_create_nonce('fieldmaster_upgrade'); ?>'
					},
					success: function( json ){
						
						// vars
						var message = fields.get_ajax_message(json);
						
						
						// bail early if no message text
						if( !message.text ) {
							
							return;
							
						}
						
						
						// show message
						$('.show-on-ajax').html( message.text );
						
					},
					complete: function( json ){
						
						// remove spinner
						$('.fields-loading').hide();
						
						
						// show complete
						$('.show-on-complete').show();
						
					}
				});
				
				
			}
			
		}.init();
				
	})(jQuery);	
	</script>
	
<?php else: ?>

	<p><?php _e('No updates available', 'fields'); ?>.</p>
	
<?php endif; ?>

</div>
