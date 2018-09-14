<?php if(!defined('ABSPATH')) die('Direct access denied.'); ?>
<?php if($error): ?>
	<div class="cs-sortables">
		<?php echo $error; ?>
	</div>
<?php else: ?>
	<div class="cs-slide-actions">
		<button type="button" id="cs-add-slide" class="cs-add-slide"><?php _e('Add Slide', 'cycloneslider'); ?></button>
		<button type="button" id="cs-multiple-slides" class="cs-multiple-slides"><?php _e('Add Images as Slides', 'cycloneslider'); ?></button>
		<button type="button" id="cs-sort" class="cs-sort"><?php _e('Sort', 'cycloneslider'); ?></button>
	</div>
	<input type="hidden" name="<?php echo esc_attr($nonce_name); ?>" value="<?php echo esc_attr($nonce); ?>" />
	<div id="cs-sortables" class="cs-sortables" data-post-id="<?php echo esc_attr($post_id); ?>">
		<?php echo $slides; ?>
	</div>
<?php endif; ?>