<?php if(!defined('ABSPATH')) die('Direct access denied.'); ?>
<div class="cycloneslider-field">
	<label for="cycloneslider_settings_fx"><?php _e('Transition Effects to Use:', 'cycloneslider'); ?></label>
	<select id="cycloneslider_settings_fx" name="cycloneslider_settings[fx]">
	<?php foreach($effects as $key=>$fx): ?>
	<option <?php selected( $slider_settings['fx'], $key ); ?> value="<?php echo esc_attr($key); ?>"><?php echo esc_attr($fx); ?></option>
	<?php endforeach; ?>
	</select>
	<div class="clear"></div>
</div>
<div class="cycloneslider-field cycloneslider-field-tile-properties">
	<label for="cycloneslider_settings_tile_count"><?php _e('Tile Count:', 'cycloneslider'); ?> </label>
	<input id="cycloneslider_settings_tile_count" type="number" name="cycloneslider_settings[tile_count]" value="<?php echo esc_attr($slider_settings['tile_count']); ?>" />
	<span class="note"><?php _e('The number of tiles to use in the transition.', 'cycloneslider'); ?></span>
	<div class="clear"></div>
	<br />
	<label for="cycloneslider_settings_tile_vertical"><?php _e('Tile Position:', 'cycloneslider'); ?> </label>
	<select id="cycloneslider_settings_tile_vertical" name="cycloneslider_settings[tile_vertical]">
		<option <?php selected( $slider_settings['tile_vertical'], 'true' ); ?> value="true"><?php _e('Vertical', 'cycloneslider'); ?></option>
		<option <?php selected( $slider_settings['tile_vertical'], 'false' ); ?> value="false"><?php _e('Horizontal', 'cycloneslider'); ?></option>
	</select>
	<div class="clear"></div>
</div>
<div class="cycloneslider-field">
	<label for="cycloneslider_settings_timeout"><?php _e('Next Slide Delay:', 'cycloneslider'); ?> </label>
	<input id="cycloneslider_settings_timeout" type="number" name="cycloneslider_settings[timeout]" value="<?php echo esc_attr($slider_settings['timeout']); ?>" />
	<span class="note"><?php _e('Milliseconds. 0 to disable auto advance.', 'cycloneslider'); ?></span>
	<div class="clear"></div>
</div>
<div class="cycloneslider-field">
	<label for="cycloneslider_settings_speed"><?php _e('Transition Effects Speed:', 'cycloneslider'); ?></label>
	<input id="cycloneslider_settings_speed" type="number" name="cycloneslider_settings[speed]" value="<?php echo esc_attr($slider_settings['speed']); ?>" />
	<span class="note"><?php _e('Milliseconds', 'cycloneslider'); ?></span>
	<div class="clear"></div>
</div>
<div class="cycloneslider-field">
	<label for="cycloneslider_settings_width"><?php _e('Width:', 'cycloneslider'); ?> </label>
	<input id="cycloneslider_settings_width" type="number" name="cycloneslider_settings[width]" value="<?php echo esc_attr($slider_settings['width']); ?>" />
	<span class="note"><?php _e('pixels.', 'cycloneslider'); ?></span>
	<div class="clear"></div>
</div>
<div class="cycloneslider-field">
	<label for="cycloneslider_settings_height"><?php _e('Height:', 'cycloneslider'); ?> </label>
	<input id="cycloneslider_settings_height" type="number" name="cycloneslider_settings[height]" value="<?php echo esc_attr($slider_settings['height']); ?>" />
	<span class="note"><?php _e('pixels.', 'cycloneslider'); ?></span>
	<div class="clear"></div>
</div>
<div class="cycloneslider-field">
	<label for="cycloneslider_settings_width_management"><?php _e('Width Management:', 'cycloneslider'); ?></label>
	<select id="cycloneslider_settings_width_management" name="cycloneslider_settings[width_management]">
		<option <?php selected($slider_settings['width_management'], 'responsive'); ?> value="responsive"><?php _e('Responsive', 'cycloneslider'); ?></option>
		<option <?php selected($slider_settings['width_management'], 'full'); ?> value="full"><?php _e('Full', 'cycloneslider'); ?></option>
		<option <?php selected($slider_settings['width_management'], 'fixed'); ?> value="fixed"><?php _e('Fixed', 'cycloneslider'); ?></option>
	</select>
	<span class="note">
	<?php _e('Responsive - resizes to smaller size but maximum width will be equal to the provided width.', 'cycloneslider'); ?><br />
	<?php _e('Full - the same as responsive but maximum width will be equal to its container ignoring the provided width.', 'cycloneslider'); ?><br />
	<?php _e('Fixed - width and height are not resized.', 'cycloneslider'); ?>
	</span>
	<div class="clear"></div>
</div>
<div class="cycloneslider-field">
	<label for="cycloneslider_settings_resize"><?php _e('Resize Images?', 'cycloneslider'); ?></label>
	<select id="cycloneslider_settings_resize" name="cycloneslider_settings[resize]">
		<option <?php selected($slider_settings['resize'], 0); ?> value="0"><?php _e('No', 'cycloneslider'); ?></option>
		<option <?php selected($slider_settings['resize'], 1); ?> value="1"><?php _e('Yes', 'cycloneslider'); ?></option>
	</select>
	<input type="hidden" name="cycloneslider_settings[force_resize]" value="0" />
	<input type="checkbox" name="cycloneslider_settings[force_resize]" id="force_resize" value="1" />
	<label for="force_resize"><?php _e('Force Resize', 'cycloneslider'); ?></label> <br>
	<span class="note">
		<?php _e('Yes - resize images to slideshow dimension.', 'cycloneslider'); ?><br>
		<?php _e('No - use the original uploaded image.', 'cycloneslider'); ?><br>
		<?php _e('Force Resize - Regenerate all images and thumbnails.', 'cycloneslider'); ?>
	</span>
	<div class="clear"></div>
</div>
<div class="cycloneslider-field">
	<label for="cycloneslider_settings_hover_pause"><?php _e('Pause on Hover?', 'cycloneslider'); ?></label>
	<select id="cycloneslider_settings_hover_pause" name="cycloneslider_settings[hover_pause]">
		<option <?php selected($slider_settings['hover_pause'], 'true'); ?> value="true"><?php _e('Yes', 'cycloneslider'); ?></option>
		<option <?php selected($slider_settings['hover_pause'], 'false'); ?> value="false"><?php _e('No', 'cycloneslider'); ?></option>
	</select>
	<div class="clear"></div>
</div>
<div class="cycloneslider-field">
	<label for="cycloneslider_settings_show_prev_next"><?php _e('Show Prev/Next Buttons?', 'cycloneslider'); ?></label>
	<select id="cycloneslider_settings_show_prev_next" name="cycloneslider_settings[show_prev_next]">
		<option <?php selected($slider_settings['show_prev_next'], 1); ?> value="1"><?php _e('Yes', 'cycloneslider'); ?></option>
		<option <?php selected($slider_settings['show_prev_next'], 0); ?> value="0"><?php _e('No', 'cycloneslider'); ?></option>
	</select>
	<div class="clear"></div>
</div>
<div class="cycloneslider-field">
	<label for="cycloneslider_settings_show_nav"><?php _e('Show Navigation?', 'cycloneslider'); ?></label>
	<select id="cycloneslider_settings_show_nav" name="cycloneslider_settings[show_nav]">
		<option <?php selected($slider_settings['show_nav'], 1); ?> value="1"><?php _e('Yes', 'cycloneslider'); ?></option>
		<option <?php selected($slider_settings['show_nav'], 0); ?> value="0"><?php _e('No', 'cycloneslider'); ?></option>
	</select>
	<span class="note"><?php _e('The thumbnails or dots depending on template.', 'cycloneslider'); ?></span>
	<div class="clear"></div>
</div>
<div class="cycloneslider-field last">
	<label for="cycloneslider_settings_randomize"><?php _e('Random Slide Order?', 'cycloneslider'); ?></label>
	<select id="cycloneslider_settings_randomize" name="cycloneslider_settings[random]">
		<option <?php echo (0==$slider_settings['random']) ? 'selected="selected"' : ''; ?> value="0"><?php _e('No', 'cycloneslider'); ?></option>
		<option <?php echo (1==$slider_settings['random']) ? 'selected="selected"' : ''; ?> value="1"><?php _e('Yes', 'cycloneslider'); ?></option>
	</select>
	<span class="note"><?php _e('Randomize order of slides on every page visit.', 'cycloneslider'); ?></span>
	<div class="clear"></div>
</div>