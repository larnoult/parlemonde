<?php if(!defined('ABSPATH')) die('Direct access denied.'); ?>

<?php if( !empty($ok) ): ?>
	<div class="updated below-h2"> 
		<p><?php echo esc_attr($ok); ?></p>
	</div>
<?php endif; ?>