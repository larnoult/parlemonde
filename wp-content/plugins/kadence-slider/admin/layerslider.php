<?php 

?>
<section id="ksp-slider-settings" class="ksp-tab-content" style="display:block;">
	<?php
	$slider_select_options = array(
		array("slug" => 0, "name" => __('Off', 'kadence-slider')), 
		array("slug" => 1, "name" => __('On', 'kadence-slider'))
		);
	?>
	<div class="ksp-settings-table ksp-table">
		<div class="ksp-settings-table-head ksp-tab-title">
				<h3><?php _e('Slider Settings', 'kadence-slider'); ?></h3>
		</div>
		<div class="ksp-row">
			<div class="ksp-column ksp-full">
				<strong><?php _e('Slider Name:', 'kadence-slider'); ?></strong>
				<input type="text" id="ksp-title" placeholder="<?php _e('New Slider', 'kadence-slider'); ?>" value="<?php if($edit){ echo esc_attr($slider->name);} ?>" />
			</div>
		</div>
		<div class="ksp-row">
			<div class="ksp-column ksp-odd">
				<strong><?php _e('ID:', 'kadence-slider'); ?></strong>
				<span id="ksp-id-output"><?php if($edit) { echo $slider->id; } ?></span>
			</div>
			<div class="ksp-column ksp-even">
				<strong><?php _e('Shortcode:', 'kadence-slider'); ?></strong>	
				<span id="ksp-slider-shortcode"><?php if($edit) { echo '[kadence_slider_pro id="'.$slider->id.'"]';}?></span>
			</div>
		</div>
		<div class="ksp-row">
			<div class="ksp-column ksp-odd">
				<div class="ksp-label">
					<?php _e('Slider Grid Height', 'kadence-slider'); ?>
					</div>
				<div class="ksp-content">
					<?php 
					if(!$edit) { 
						echo '<input id="ksp-slider-maxHeight" type="text" value="450" />';
					} else {
						echo '<input id="ksp-slider-maxHeight" type="text" value="' . $slider->maxHeight .'" />';
					} ?>
					px
					<span class="ksp-description">
						<?php _e('This sets a max height for your slider.', 'kadence-slider'); ?>
					</span>
				</div>
			</div>
			<div class="ksp-column ksp-even">
				<div class="ksp-label">
					<?php _e('Slider Grid Width', 'kadence-slider'); ?>
				</div>
				<div class="ksp-content">
					<?php 
					if(!$edit) { 
						echo '<input id="ksp-slider-maxWidth" type="text" value="1140" />';
					} else {
						echo '<input id="ksp-slider-maxWidth" type="text" value="' . $slider->maxWidth .'" />';
					} ?>
					px
					<span class="ksp-description">
						<?php _e('This sets a max width for your slider.', 'kadence-slider'); ?>
					</span>
				</div>
			</div>
		</div>
		<div class="ksp-row">
			<div class="ksp-column ksp-odd">
				<div class="ksp-label">
					<?php _e('Full height', 'kadence-slider'); ?>
					</div>
				<div class="ksp-content">
					<div class="onoffswitch">
							    <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="ksp-slider-fullHeight" <?php if($edit && $slider->fullHeight == 1) {
								echo 'checked';
							}?> >
							    <label class="onoffswitch-label" for="ksp-slider-fullHeight">
							        <span class="onoffswitch-inner"></span>
							        <span class="onoffswitch-switch"></span>
							    </label>
					</div>
					<span class="ksp-description">
						<?php _e('This will ignore the grid height setting.', 'kadence-slider'); ?>
					</span>
				</div>
			</div>
			<div class="ksp-column ksp-even">
				<div class="ksp-label">
					<?php _e('Full Width', 'kadence-slider'); ?>
				</div>
				<div class="ksp-content">
					<div class="onoffswitch">
							    <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="ksp-slider-fullWidth" <?php if($edit && $slider->fullWidth == 1) {
								echo 'checked';
							}?> >
							    <label class="onoffswitch-label" for="ksp-slider-fullWidth">
							        <span class="onoffswitch-inner"></span>
							        <span class="onoffswitch-switch"></span>
							    </label>
					</div>
					<span class="ksp-description">
						<?php _e('This will ignore the grid width setting.', 'kadence-slider'); ?>
					</span>
				</div>
			</div>
		</div>
		<div class="ksp-row ksp-full-height-offset-row">
			<div class="ksp-column-full">
				<div class="ksp-label">
					<?php _e('Full Height Offset', 'kadence-slider'); ?>
					</div>
				<div class="ksp-content">
					<?php 
					if(!$edit) { 
						echo '<input id="ksp-slider-full_offset" type="text" value="" />';
					} else {
						echo '<input id="ksp-slider-full_offset" type="text" value="' . $slider->full_offset .'" />';
					} ?>
					<span class="ksp-description">
						<?php _e('Add a ID that would allow you to offset the fullheight screen height by the height of the div (example: #kad-banner)', 'kadence-slider'); ?>
					</span>
				</div>
			</div>
		</div>
		<div class="ksp-row">
			<div class="ksp-column-full">
				<div class="ksp-label">
					<?php _e('Mobile Minium Height', 'kadence-slider'); ?>
					</div>
				<div class="ksp-content">
					<?php 
					if(!$edit) { 
						echo '<input id="ksp-slider-minHeight" type="text" value="0" />';
					} else {
						if(isset($slider->minHeight)) {
							echo '<input id="ksp-slider-minHeight" type="text" value="' . $slider->minHeight .'" />';
						} else {
							echo '<input id="ksp-slider-minHeight" type="text" value="0" />';
						}
					} ?>
					px
					<span class="ksp-description">
						<?php _e('This will set a minium height for you slider for small mobile screens. NOTE that this will not apply if "Respect image aspect ratio" is checked.', 'kadence-slider'); ?>
					</span>
				</div>
			</div>
		</div>
		<div class="ksp-row">
			<div class="ksp-column-full">
				<div class="ksp-label">
					<?php _e('Respect Image Aspect Ratio', 'kadence-slider'); ?>
					</div>
				<div class="ksp-content">
					<div class="onoffswitch">
							    <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="ksp-slider-responsive" <?php if($edit && $slider->responsive == 1) {
								echo 'checked';
							}?> >
							    <label class="onoffswitch-label" for="ksp-slider-responsive">
							        <span class="onoffswitch-inner"></span>
							        <span class="onoffswitch-switch"></span>
							    </label>
					</div>
					<span class="ksp-description">
						<?php _e('This will ignore the full height and display the images based on the uploaded ratio once they fall below the max height. You can not use with fullheight or parallax.', 'kadence-slider'); ?>
					</span>
				</div>
			</div>
		</div>
		<div class="ksp-row">
			<div class="ksp-column ksp-odd">
				<div class="ksp-label">
				<?php _e('Auto Play', 'kadence-slider'); ?>
				</div>
				<div class="ksp-content">
					<div class="onoffswitch">
							    <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="ksp-slider-autoPlay" <?php if($edit && $slider->autoPlay == 1) {
								echo 'checked';
							}?> >
							    <label class="onoffswitch-label" for="ksp-slider-autoPlay">
							        <span class="onoffswitch-inner"></span>
							        <span class="onoffswitch-switch"></span>
							    </label>
					</div>
				<span class="ksp-description">
						<?php _e('Scroll through slides automatically.', 'kadence-slider'); ?>
				</span>
				</div>
			</div>
			<div class="ksp-column ksp-even">
				<div class="ksp-label">
					<?php _e('Slider Pause Time', 'kadence-slider'); ?>
				</div>
				<div class="ksp-content">
					<?php 
					if(!$edit) { 
						echo '<input id="ksp-slider-pauseTime" type="text" value="9000" />';
					} else {
						echo '<input id="ksp-slider-pauseTime" type="text" value="' . $slider->pauseTime .'" />';
					} ?>
					<span class="ksp-description">
						<?php _e('The time each slide is displayed in milliseconds.', 'kadence-slider'); ?>
					</span>
				</div>
			</div>
		</div>
		<div class="ksp-row">
			<div class="ksp-column ksp-odd">
				<div class="ksp-label">
					<?php _e('Pause autoplay on hover?', 'kadence-slider'); ?>
				</div>
				<div class="ksp-content">
					<div class="onoffswitch">
							    <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="ksp-slider-pauseonHover" <?php if($edit && $slider->pauseonHover == 1) {
								echo 'checked';
							}?> >
							    <label class="onoffswitch-label" for="ksp-slider-pauseonHover">
							        <span class="onoffswitch-inner"></span>
							        <span class="onoffswitch-switch"></span>
							    </label>
					</div>
					<span class="ksp-description">
						<?php _e('Stops autoplay while mouse is over slider.', 'kadence-slider'); ?>
					</span>
				</div>
			</div>
			<div class="ksp-column ksp-even">
				<div class="ksp-label">
					<?php _e('Single Slide', 'kadence-slider'); ?>
				</div>
				<div class="ksp-content">
					<div class="onoffswitch">
							    <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="ksp-slider-singleSlide" <?php if($edit && $slider->singleSlide == 1) {
								echo 'checked';
							}?> >
							    <label class="onoffswitch-label" for="ksp-slider-singleSlide">
							        <span class="onoffswitch-inner"></span>
							        <span class="onoffswitch-switch"></span>
							    </label>
					</div>
					<span class="ksp-description">
						<?php _e('Turns off slider navigation.', 'kadence-slider'); ?>
					</span>
				</div>
			</div>
		</div>
		<div class="ksp-row">
			<div class="ksp-column-full">
				<div class="ksp-label">
				<?php _e('Enable Parallax', 'kadence-slider'); ?>
				</div>
				<div class="ksp-content">
					<div class="onoffswitch">
							    <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="ksp-slider-enableParallax" <?php if($edit && $slider->enableParallax == 1) {
								echo 'checked';
							}?> >
							    <label class="onoffswitch-label" for="ksp-slider-enableParallax">
							        <span class="onoffswitch-inner"></span>
							        <span class="onoffswitch-switch"></span>
							    </label>
					</div>
				<span class="ksp-description">
						<?php _e('Images should be 1800px by 1200px minimum with this option.', 'kadence-slider'); ?>
				</span>
				</div>
			</div>
		</div>
</div>
</section>