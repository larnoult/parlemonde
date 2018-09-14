<?php 

// vars
$button_url = '';
$button_text = '';
$confirm = true;


// extract
extract($args);


// calculate add-ons (non pro only)
$plugins = array();

if( !fieldmaster_get_setting('pro') ) {
	
	if( is_plugin_active('fieldmaster-repeater/fieldmaster-repeater.php') ) $plugins[] = __("Repeater",'fieldmaster');
	if( is_plugin_active('fieldmaster-flexible-content/fieldmaster-flexible-content.php') ) $plugins[] = __("Flexible Content",'fieldmaster');
	if( is_plugin_active('fieldmaster-gallery/fieldmaster-gallery.php') ) $plugins[] = __("Gallery",'fieldmaster');
	if( is_plugin_active('fieldmaster-options-page/fieldmaster-options-page.php') ) $plugins[] = __("Options Page",'fieldmaster');
	
}

?>
<div id="fieldmaster-upgrade-notice" class="fieldmaster-cf">
	
	<div class="inner">
		
		<div class="fieldmaster-icon logo">
			<i class="fieldmaster-sprite-logo"></i>
		</div>
		
		<div class="content">
			
			<h2><?php _e("Database Upgrade Required",'fieldmaster'); ?></h2>
			
			<p><?php printf(__("Thank you for updating to %s v%s!", 'fieldmaster'), fieldmaster_get_setting('name'), fieldmaster_get_setting('version') ); ?><br /><?php _e("Before you start using the new awesome features, please update your database to the newest version.", 'fieldmaster'); ?></p>
			
			<?php if( !empty($plugins) ): ?>
				<p><?php printf(__("Please also ensure any premium add-ons (%s) have first been updated to the latest version.", 'fieldmaster'), implode(', ', $plugins) ); ?></p>
			<?php endif; ?>
			
			<p><a id="fieldmaster-notice-action" href="<?php echo $button_url; ?>" class="button button-primary"><?php echo $button_text; ?></a></p>
			
		<?php if( $confirm ): ?>
			<script type="text/javascript">
			(function($) {
				
				$("#fieldmaster-notice-action").on("click", function(){
			
					var answer = confirm("<?php _e( 'It is strongly recommended that you backup your database before proceeding. Are you sure you wish to run the updater now?', 'fieldmaster' ); ?>");
					return answer;
			
				});
				
			})(jQuery);
			</script>
		<?php endif; ?>
		
		</div>
		
	</div>
	
</div>