<div class="cs-slide-image">
	<div class="cs-image-preview">
		<div class="cs-image-field">
			<div class="cs-image-thumb">
				<?php if($image_url): ?>
					<img src="<?php echo esc_url($image_url); ?>" alt="<?php _e('Thumbnail', 'cycloneslider'); ?>">
				<?php endif; ?>
			</div>
			<input class="cs-image-id" name="cycloneslider_metas[<?php echo esc_attr($i); ?>][id]" type="hidden" value="<?php echo esc_attr($slide['id']); ?>" />
			<input class="button-secondary cs-media-gallery-show" type="button" value="<?php _e('Get Image', 'cycloneslider'); ?>" />
			<?php if($image_url): ?>
				<a target="_blank" class="button-secondary" href="<?php echo esc_url($full_image_url); ?>"><?php _e('View Image', 'cycloneslider'); ?></a>
			<?php endif; ?>
		</div>
	</div>
	<div class="cs-image-edit">
		<div class="expandable-box">
			<div class="expandable-header first"><?php _e('Caption', 'cycloneslider'); ?></div>
			<div class="expandable-body">
				<div class="field">
					<label for=""><?php _e('Title:', 'cycloneslider'); ?></label> <br>
					<input class="widefat cycloneslider-slide-meta-title" name="cycloneslider_metas[<?php echo esc_attr($i); ?>][title]" type="text" value="<?php echo esc_attr($slide['title']); ?>" />
				</div>
				<div class="field last">
					<label for=""><?php _e('Description:', 'cycloneslider'); ?></label> <br>
					<textarea class="widefat cycloneslider-slide-meta-description" name="cycloneslider_metas[<?php echo esc_attr($i); ?>][description]"><?php echo esc_textarea($slide['description']); ?></textarea>
				</div>
			</div>
		</div>
		<div class="expandable-box">
			<div class="expandable-header"><?php _e('Link', 'cycloneslider'); ?></div>
			<div class="expandable-body">
				<div class="field">
					<label for=""><?php _e('Link URL:', 'cycloneslider'); ?></label> <br>
					<input class="cycloneslider_metas_link_url widefat" name="cycloneslider_metas[<?php echo esc_attr($i); ?>][link]" type="text" value="<?php echo esc_url($slide['link']); ?>" />
				</div>
				<div class="field last">
					<label for=""><?php _e('Open Link in:', 'cycloneslider'); ?></label> <br>
					<select class="cycloneslider_metas_link_target" id="" name="cycloneslider_metas[<?php echo esc_attr($i); ?>][link_target]">
						<option <?php selected( $slide['link_target'], '_self' ); ?> value="_self"><?php _e('Same Window', 'cycloneslider'); ?></option>
						<option <?php selected( $slide['link_target'], '_blank' ); ?> value="_blank"><?php _e('New Tab or Window', 'cycloneslider'); ?></option>
					</select>
				</div>
			</div>
		</div>
		<div class="expandable-box last">
			<div class="expandable-header"><?php _e('Image Attributes', 'cycloneslider'); ?></div>
			<div class="expandable-body">
				<div class="field">
					<label for=""><?php _e('Alternate Text:', 'cycloneslider'); ?></label> <br>
					<input class="widefat cycloneslider-slide-meta-alt" name="cycloneslider_metas[<?php echo esc_attr($i); ?>][img_alt]" type="text" value="<?php echo esc_attr($slide['img_alt']); ?>" />
				</div>
				<div class="field last">
					<label for=""><?php _e('Title Text:', 'cycloneslider'); ?></label> <br>
					<input class="widefat cycloneslider-slide-meta-title" name="cycloneslider_metas[<?php echo esc_attr($i); ?>][img_title]" type="text" value="<?php echo esc_attr($slide['img_title']); ?>" />
				</div>
			</div>
		</div>
	</div>
</div><!-- // end .cs-slide-image -->