<?php 

// defaults
$args = wp_parse_args($args, array(
	'button_url'	=> '',
	'button_text'	=> '',
	'confirm'		=> true
));

extract($args);

?>
<div id="fields-upgrade-notice" class="fields-cf">
	
	<div class="inner">
		
		<div class="fields-icon logo">
			<i class="fields-sprite-logo"></i>
		</div>
		
		<div class="content">
			
			<h2><?php _e("Database Upgrade Required",'fields'); ?></h2>
			
			<p><?php printf(__("Thank you for updating to %s v%s!", 'fields'), fieldmaster_get_setting('name'), fieldmaster_get_setting('version') ); ?><br /><?php _e("Before you start using the new awesome features, please update your database to the newest version.", 'fields'); ?></p>
			
			<p><a id="fields-notice-action" href="<?php echo $button_url; ?>" class="fields-button blue"><?php echo $button_text; ?></a></p>
			
		<?php if( $confirm ): ?>
			<script type="text/javascript">
			(function($) {
				
				$("#fields-notice-action").on("click", function(){
			
					var answer = confirm("<?php _e( 'It is strongly recommended that you backup your database before proceeding. Are you sure you wish to run the updater now?', 'fields' ); ?>");
					return answer;
			
				});
				
			})(jQuery);
			</script>
		<?php endif; ?>
		
		</div>
		
	</div>
	
</div>
