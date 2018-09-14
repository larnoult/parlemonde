<div class="cycloneslider-box">
	<div class="cycloneslider-box-title ui-state-default">
		<span class="cycloneslider-box-title-left">
			<?php _e('Slide', 'cycloneslider'); ?>
		</span>
		<span class="cycloneslider-box-title-right">
			<a href="#" class="cycloneslider-box-toggle" title="<?php _e('Toggle', 'cycloneslider'); ?>"><?php _e('Toggle', 'cycloneslider'); ?></a>
			<a href="#" class="cycloneslider-box-delete" title="<?php _e('Delete', 'cycloneslider'); ?>"><?php _e('Delete', 'cycloneslider'); ?></a>
		</span>
		<div class="clear"></div>
	</div>
	<div class="cycloneslider-box-body">
		<div class="cycloneslider-body-left">
			<img class="cycloneslider-slide-thumb" src="" alt="" />
			<input class="cycloneslider-slide-meta-id" name="cycloneslider_metas[{id}][id]" type="hidden" value="" />
			<input class="cycloneslider-slide-meta-type" name="cycloneslider_metas[{id}][type]" type="hidden" value="image" />
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
						<input class="widefat cycloneslider-slide-meta-link" name="cycloneslider_metas[{id}][link]" type="text" value="" />
						<label for=""><?php _e('Open Link in:', 'cycloneslider'); ?></label>
						<select id="" name="cycloneslider_metas[{id}][link_target]">
							<option value="_self"><?php _e('Same Window', 'cycloneslider'); ?></option>
							<option value="_blank"><?php _e('New Tab or Window', 'cycloneslider'); ?></option>
						</select>
					</div>
				</div>
				<div class="cycloneslider-meta-field">
					<div class="cycloneslider-field-title">
						<?php _e('Title', 'cycloneslider'); ?>
					</div>
					<div class="cycloneslider-field-body">
						<input class="widefat cycloneslider-slide-meta-title" name="cycloneslider_metas[{id}][title]" type="text" value="" />
					</div>
				</div>
				<div class="cycloneslider-meta-field">
					<div class="cycloneslider-field-title">
						<?php _e('Description', 'cycloneslider'); ?>
					</div>
					<div class="cycloneslider-field-body">
						<textarea class="widefat cycloneslider-slide-meta-description" name="cycloneslider_metas[{id}][description]"></textarea>
					</div>
				</div>
			</div>
			
			
		</div>
		<div class="clear"></div>
	</div>
</div>