<?php if(!defined('ABSPATH')) die('Direct access denied.'); ?>

<?php if( !empty($error) ): ?>
	<div class="error below-h2"> 
		<p><strong>Error:</strong> <?php echo esc_attr($error); ?></p>
	</div>
<?php endif; ?>