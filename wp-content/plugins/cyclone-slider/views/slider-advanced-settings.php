<?php if(!defined('ABSPATH')) die('Direct access denied.'); ?>
<div class="cycloneslider-cover"><p><a href="http://www.codefleet.net/cyclone-slider-pro/"><?php _e('Available in pro version.', 'cycloneslider'); ?></a></p></div>
<div class="cycloneslider-field">
	<label for="cycloneslider_settings_allow_wrap"><?php _e('Allow Wrap?', 'cycloneslider'); ?></label>
	<select id="cycloneslider_settings_allow_wrap" name="cycloneslider_settings[allow_wrap]">
		<option <?php selected($slider_settings['allow_wrap'], 'true'); ?> value="true"><?php _e('Yes', 'cycloneslider'); ?></option>
		<option <?php selected($slider_settings['allow_wrap'], 'false'); ?> value="false"><?php _e('No', 'cycloneslider'); ?></option>
	</select>
	<span class="note">
	<?php _e('Determines if slider wraps to beginning slide if it reaches the end slide.', 'cycloneslider'); ?>
	</span>
	<div class="clear"></div>
</div>
<div class="cycloneslider-field">
	<label for="cycloneslider_settings_dynamic_height"><?php _e('Dynamic Height:', 'cycloneslider'); ?></label>
	<select id="cycloneslider_settings_dynamic_height" name="cycloneslider_settings[dynamic_height]">
		<option <?php selected($slider_settings['dynamic_height'], 'off'); ?> value="off"><?php _e('Off', 'cycloneslider'); ?></option>
		<option <?php selected($slider_settings['dynamic_height'], 'on'); ?> value="on"><?php _e('On', 'cycloneslider'); ?></option>
	</select>
	<span class="note">
	<?php _e('Adjust slider height depending on current slide.', 'cycloneslider'); ?>
	</span>
	<div class="clear"></div>
</div>
<div class="cycloneslider-field">
	<label for="cycloneslider_settings_delay"><?php _e('Delay:', 'cycloneslider'); ?> </label>
	<input id="cycloneslider_settings_delay" type="number" name="cycloneslider_settings[delay]" value="<?php echo esc_attr($slider_settings['delay']); ?>" />
	<span class="note"><?php _e('Milliseconds to add or substract from the time before the first transition occurs.', 'cycloneslider'); ?></span>
	<div class="clear"></div>
</div>
<div class="cycloneslider-field">
	<label for="cycloneslider_settings_easing"><?php _e('Easing:', 'cycloneslider'); ?></label>
	<select id="cycloneslider_settings_easing" name="cycloneslider_settings[easing]">
		<?php foreach( $easing_options as $easing ): ?>
		<option <?php echo (isset($slider_settings['easing']) && $easing['value']==$slider_settings['easing']) ? 'selected="selected"' : ''; ?> value="<?php echo esc_attr( $easing['value'] ); ?>"><?php echo esc_attr( $easing['text'] ); ?></option>
		<?php endforeach; ?>
	</select>
	<span class="note"><?php _e('Easing for transition animations.', 'cycloneslider'); ?></span>
	<div class="clear"></div>
</div>
<div class="cycloneslider-field">
	<label for="cycloneslider_settings_swipe"><?php _e('Swipe:', 'cycloneslider'); ?></label>
	<select id="cycloneslider_settings_swipe" name="cycloneslider_settings[swipe]">
		<option <?php selected($slider_settings['swipe'], 'true'); ?> value="true"><?php _e('Yes', 'cycloneslider'); ?></option>
		<option <?php selected($slider_settings['swipe'], 'false'); ?> value="false"><?php _e('No', 'cycloneslider'); ?></option>
	</select>
	<span class="note">
	<?php _e('Enable swipe gesture support for touch devices.', 'cycloneslider'); ?>
	</span>
	<div class="clear"></div>
</div>
	<div class="cycloneslider-field">
		<label for="cycloneslider_settings_resize_option"><?php _e('Resize Options:', 'cycloneslider'); ?></label>
		<select id="cycloneslider_settings_resize_option" name="cycloneslider_settings[resize_option]">
			<?php foreach( $resize_options as $resize_option=>$resize_name ): ?>
				<option <?php echo (isset($slider_settings['resize_option']) && $resize_option==$slider_settings['resize_option']) ? 'selected="selected"' : ''; ?> value="<?php echo esc_attr( $resize_option ); ?>"><?php echo esc_attr( $resize_name ); ?></option>
			<?php endforeach; ?>
		</select>
	<span class="note">
		<?php if(version_compare(PHP_VERSION, '5.3', '>=')){ ?>
			<?php _e('Fit - (Default) Fit images inside the slideshow maintaining aspect ratio.', 'cycloneslider'); ?><br>
			<?php _e('Fill - Images will fill the entire slideshow with no empty space.', 'cycloneslider'); ?><br>
			<?php _e('Crop - Excess parts of images are removed.', 'cycloneslider'); ?><br>
			<?php _e('Exact - Resize images to exact dimensions ignoring aspect ratio.', 'cycloneslider'); ?><br>
			<?php _e('Exact Width - Resize to exact width.', 'cycloneslider'); ?><br>
			<?php _e('Exact Height - Resize to exact height.', 'cycloneslider'); ?><br>
			<?php _e('Note: Please clear your browser cache if you are not seeing the changes.', 'cycloneslider'); ?><br>
		<?php } else { ?>
			<?php _e('Auto - Cyclone Slider decides the resize option.', 'cycloneslider'); ?><br>
			<?php _e('Crop - Resize and remove excess parts.', 'cycloneslider'); ?><br>
			<?php _e('Exact - Resize to exact dimensions.', 'cycloneslider'); ?><br>
			<?php _e('Landscape - Resize to exact width.', 'cycloneslider'); ?><br>
			<?php _e('Portrait - Resize to exact height.', 'cycloneslider'); ?><br>
		<?php } ?>
	</span>
		<div class="clear"></div>
	</div>
<?php if(version_compare(PHP_VERSION, '5.3', '>=')){ ?>
	<div class="cycloneslider-field last">
		<label for="cycloneslider_settings_resize_quality"><?php _e('Image Quality (JPEG):', 'cycloneslider'); ?></label>
		<select id="cycloneslider_settings_resize_quality" name="cycloneslider_settings[resize_quality]">

			<option <?php selected($slider_settings['resize_quality'], 10); ?> value="10"><?php _e('Low', 'cycloneslider'); ?></option>
			<option <?php selected($slider_settings['resize_quality'], 30); ?> value="30"><?php _e('Medium', 'cycloneslider'); ?></option>
			<option <?php selected($slider_settings['resize_quality'], 60); ?> value="60"><?php _e('High', 'cycloneslider'); ?></option>
			<option <?php selected($slider_settings['resize_quality'], 80); ?> value="80"><?php _e('Very High', 'cycloneslider'); ?></option>
			<option <?php selected($slider_settings['resize_quality'], 100); ?> value="100"><?php _e('Max', 'cycloneslider'); ?></option>

		</select>
	<span class="note">
		<?php _e('The quality of the generated images. Applies to JPEG images only.', 'cycloneslider'); ?><br>
		<?php _e('Low = low quality but small file size. Max = Best quality but large file size.', 'cycloneslider'); ?><br>
	</span>
		<div class="clear"></div>
	</div>
<?php } ?>