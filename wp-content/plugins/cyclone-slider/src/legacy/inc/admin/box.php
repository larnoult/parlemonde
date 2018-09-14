<div class="cycloneslider-box">
	<div class="cycloneslider-box-title ui-state-default">
		<span class="cycloneslider-box-title-left">
			<?php echo $box_title; ?>
		</span>
		<span class="cycloneslider-box-title-right">
			<a href="#" class="cycloneslider-box-toggle" title="<?php _e('Toggle', 'cycloneslider'); ?>"><?php _e('Toggle', 'cycloneslider'); ?></a>
			<a href="#" class="cycloneslider-box-delete" title="<?php _e('Delete', 'cycloneslider'); ?>"><?php _e('Delete', 'cycloneslider'); ?></a>
		</span>
		<div class="clear"></div>
	</div>
	<div class="cycloneslider-box-body">
		<div class="cycloneslider-body-left">
			<img class="cycloneslider-slide-thumb" src="<?php echo esc_url($image_url); ?>" alt="" />
			<input class="cycloneslider-slide-meta-id" name="cycloneslider_metas[<?php echo $i; ?>][id]" type="hidden" value="<?php echo esc_attr($slider_metas[$i]['id']); ?>" />
			<input class="cycloneslider-slide-meta-type" name="cycloneslider_metas[<?php echo $i; ?>][type]" type="hidden" value="<?php echo esc_attr($slider_meta['type']); ?>" />
			<input class="button-secondary cycloneslider-upload-button" type="button" value="<?php _e('Get Image', 'cycloneslider'); ?>" />
		</div>
		<div class="cycloneslider-body-right">
			<p class="cycloneslider-sub-title"><?php _e('Extra slide elements:', 'cycloneslider'); ?></p>
			<div class="cycloneslider-slide-metas">
				<div class="cycloneslider-meta-field">
					<div class="cycloneslider-field-title first">
						<?php _e('Slide Link', 'cycloneslider'); ?>
					</div>
					<div class="cycloneslider-field-body">
						<label for=""><?php _e('Link:', 'cycloneslider'); ?></label>
						<input class="widefat cycloneslider-slide-meta-link" name="cycloneslider_metas[<?php echo $i; ?>][link]" type="text" value="<?php echo esc_url($slider_metas[$i]['link']); ?>" />
						<label for=""><?php _e('Open Link in:', 'cycloneslider'); ?></label>
						<select id="" name="cycloneslider_metas[<?php echo $i; ?>][link_target]">
							<option <?php echo ('_self'==$slider_metas[$i]['link_target']) ? 'selected="selected"' : ''; ?> value="_self"><?php _e('Same Window', 'cycloneslider'); ?></option>
							<option <?php echo ('_blank'==$slider_metas[$i]['link_target']) ? 'selected="selected"' : ''; ?> value="_blank"><?php _e('New Tab or Window', 'cycloneslider'); ?></option>
						</select>
					</div>
				</div>
				<div class="cycloneslider-meta-field">
					<div class="cycloneslider-field-title">
						<?php _e('Title', 'cycloneslider'); ?>
					</div>
					<div class="cycloneslider-field-body">
						<input class="widefat cycloneslider-slide-meta-title" name="cycloneslider_metas[<?php echo $i; ?>][title]" type="text" value="<?php echo esc_attr($slider_metas[$i]['title']); ?>" />
					</div>
				</div>
				<div class="cycloneslider-meta-field">
					<div class="cycloneslider-field-title">
						<?php _e('Description', 'cycloneslider'); ?>
					</div>
					<div class="cycloneslider-field-body">
						<textarea class="widefat cycloneslider-slide-meta-description" name="cycloneslider_metas[<?php echo $i; ?>][description]"><?php echo esc_html($slider_metas[$i]['description']); ?></textarea>
					</div>
				</div>
			</div>
			
			
		</div>
		<div class="clear"></div>
	</div>
</div>