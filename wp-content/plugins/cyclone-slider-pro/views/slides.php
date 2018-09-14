<?php if(!defined('ABSPATH')) die('Direct access denied.'); ?>

<input type="hidden" name="<?php echo $nonce_name; ?>" value="<?php echo $nonce; ?>" />
<div class="cs-sortables" data-post-id="<?php echo $post_id; ?>">
	<?php echo $slides; ?>
</div><!-- end .cycloneslider-sortable -->

<input type="button" value="<?php _e('Add Slide', 'cycloneslider'); ?>" class="cs-add-slide button-secondary" />
<input type="button" value="<?php _e('Add Images as Slides', 'cycloneslider'); ?>" class="cs-multiple-slides button-secondary" />