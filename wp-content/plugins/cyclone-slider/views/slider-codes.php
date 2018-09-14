<?php if(!defined('ABSPATH')) die('Direct access denied.'); ?>

<div class="cycloneslider-field">
	<label for="cycloneslider_get_shortcode"><?php _e('Your Shortcode:', 'cycloneslider'); ?> </label>
	<input readonly="true" id="cycloneslider_get_shortcode" type="text" class="widefat" name="" value="<?php echo esc_attr($shortcode); ?>" />
	<span class="note"><?php _e('Copy and paste this shortcode into your Post, Page or Custom Post editor.', 'cycloneslider'); ?></span>
	<div class="clear"></div>
</div>
<div class="cycloneslider-field last">
	<label for="cycloneslider_get_code"><?php _e('Your PHP Code:', 'cycloneslider'); ?> </label>
	<input readonly="true" id="cycloneslider_get_code" type="text" class="widefat" name="" value="<?php echo esc_attr($template_code); ?>" />
	<span class="note"><?php _e('Copy and paste this code when you need to display the slider in template files (header.php, front-page.php, etc.).', 'cycloneslider'); ?></span>
	<div class="clear"></div>
</div>