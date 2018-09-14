<?php if(!defined('ABSPATH')) die('Direct access denied.'); ?>

<div class="cs-slide" data-slide-type="<?php echo esc_attr( $slide['type'] ); ?>">
	<div class="cs-header">
		<span class="cs-icon">
			<i class="icon-picture"></i>
			<i class="icon-youtube-play"></i>
			<i class="icon-play"></i>
			<i class="icon-code"></i>
			<i class="icon-film"></i>
		</span>
		<span class="cs-title">
			<?php echo $box_title; ?>
		</span>
		<span class="cs-controls">
			<span class="cs-delete" title="<?php _e('Delete', 'cycloneslider'); ?>">
				<i class="icon-remove"></i>
			</span>
		</span>
		<div class="clear"></div>
	</div>
	<div class="cs-body">
		<div class="cs-slide-type-bar">
			<select class="cs-slide-type-switcher" name="cycloneslider_metas[<?php echo $i; ?>][type]">
				<option value="image" <?php selected($slide['type'], 'image'); ?>><?php _e('Image', 'cycloneslider'); ?></option>
				<option value="youtube" <?php selected($slide['type'], 'youtube'); ?>><?php _e('YouTube', 'cycloneslider'); ?></option>
				<option value="vimeo" <?php selected($slide['type'], 'vimeo'); ?>><?php _e('Vimeo', 'cycloneslider'); ?></option>
				<option value="custom" <?php selected($slide['type'], 'custom'); ?>><?php _e('Custom HTML', 'cycloneslider'); ?></option>
			</select>	
		</div>
		<div class="clear"></div>
		<div class="cs-slide-image">
			<div class="cs-image-preview">
				<div class="cs-image-thumb" <?php echo (empty($image_url)) ? 'style="display:none"' : '';?>>
					<?php if($image_url): ?>
					<img src="<?php echo esc_url($image_url); ?>" alt="thumb">
					<?php endif; ?>
				</div>
				<input class="cs-image-id" name="cycloneslider_metas[<?php echo $i; ?>][id]" type="hidden" value="<?php echo esc_attr($slide['id']); ?>" />
				<input class="button-secondary cs-media-gallery-show" type="button" value="<?php _e('Get Image', 'cycloneslider'); ?>" />
			</div>
			<div class="cs-image-settings">
				<p class="expandable-group-title first"><?php _e('Slide Properties:', 'cycloneslider'); ?></p>
				<div class="expandable-box">
					<div class="expandable-header"><?php _e('Caption', 'cycloneslider'); ?></div>
					<div class="expandable-body">
						<div class="field">
							<label for=""><?php _e('Title:', 'cycloneslider'); ?></label> <br>
							<input class="widefat cycloneslider-slide-meta-title" name="cycloneslider_metas[<?php echo $i; ?>][title]" type="text" value="<?php echo esc_attr($slide['title']); ?>" />
						</div>
						<div class="field last">
							<label for=""><?php _e('Description:', 'cycloneslider'); ?></label> <br>
							<textarea class="widefat cycloneslider-slide-meta-description" name="cycloneslider_metas[<?php echo $i; ?>][description]"><?php echo esc_html($slide['description']); ?></textarea>
						</div>
					</div>
				</div>
				<div class="expandable-box">
					<div class="expandable-header"><?php _e('Link', 'cycloneslider'); ?></div>
					<div class="expandable-body">
						<div class="field">
							<label for=""><?php _e('Link URL:', 'cycloneslider'); ?></label> <br>
							<input class="cycloneslider_metas_link_url widefat" name="cycloneslider_metas[<?php echo $i; ?>][link]" type="text" value="<?php echo esc_url($slide['link']); ?>" />
						</div>
						<div class="field last">
							<label for=""><?php _e('Open Link in:', 'cycloneslider'); ?></label> <br>
							<select class="cycloneslider_metas_link_target" id="" name="cycloneslider_metas[<?php echo $i; ?>][link_target]">
								<option <?php selected( $slide['link_target'], '_self' ); ?> value="_self"><?php _e('Same Window', 'cycloneslider'); ?></option>
								<option <?php selected( $slide['link_target'], '_blank' ); ?> value="_blank"><?php _e('New Tab or Window', 'cycloneslider'); ?></option>
								<option <?php selected( $slide['link_target'], 'lightbox' ); ?> value="lightbox"><?php _e('Lightbox', 'cycloneslider'); ?></option>
							</select>
						</div>
					</div>
				</div>
				<div class="expandable-box">
					<div class="expandable-header"><?php _e('Image Attributes', 'cycloneslider'); ?></div>
					<div class="expandable-body">
						<div class="field">
							<label for=""><?php _e('Alternate Text:', 'cycloneslider'); ?></label> <br>
							<input class="widefat cycloneslider-slide-meta-alt" name="cycloneslider_metas[<?php echo $i; ?>][img_alt]" type="text" value="<?php echo esc_attr($slide['img_alt']); ?>" />
						</div>
						<div class="field last">
							<label for=""><?php _e('Title Text:', 'cycloneslider'); ?></label> <br>
							<input class="widefat cycloneslider-slide-meta-title" name="cycloneslider_metas[<?php echo $i; ?>][img_title]" type="text" value="<?php echo esc_attr($slide['img_title']); ?>" />
						</div>
					</div>
				</div>
				<div class="expandable-box last">
					<div class="expandable-header"><?php _e('Slide Transition Effects', 'cycloneslider'); ?></div>
					<div class="expandable-body">
						
						<select id="" class="cycloneslider_metas_enable_slide_effects" name="cycloneslider_metas[<?php echo $i; ?>][enable_slide_effects]">
							<option <?php echo (0==$slide['enable_slide_effects']) ? 'selected="selected"' : ''; ?> value="0"><?php _e('Disable', 'cycloneslider'); ?></option>
							<option <?php echo (1==$slide['enable_slide_effects']) ? 'selected="selected"' : ''; ?> value="1"><?php _e('Enable Slide Effects', 'cycloneslider'); ?></option>
						</select>
						
						<div class="clear"></div>
						
						<div class="field field-inline">
							<label for=""><?php _e('Transition Effects:', 'cycloneslider'); ?></label>
							<select id="" class="cycloneslider_metas_fx" name="cycloneslider_metas[<?php echo $i; ?>][fx]">
								<option value="default">Default</option>
								<?php foreach($effects as $value=>$name): ?>
								<option value="<?php echo $value; ?>" <?php echo ($slide['fx']==$value) ? 'selected="selected"' : ''; ?>><?php echo $name; ?></option>
								<?php endforeach; ?>
							</select>
							<div class="clear"></div>
						</div>
						
						<div class="field field-inline">
							<label for=""><?php _e('Transition Effects Speed:', 'cycloneslider'); ?></label>
							<input class="widefat cycloneslider-slide-meta-speed" name="cycloneslider_metas[<?php echo $i; ?>][speed]" type="number" value="<?php echo esc_attr(@$slide['speed']); ?>" />
							<span class="note"> <?php _e('Milliseconds', 'cycloneslider'); ?></span>
							<div class="clear"></div>
						</div>
						
						<div class="field field-inline">
							<label for=""><?php _e('Next Slide Delay:', 'cycloneslider'); ?></label>
							<input class="widefat cycloneslider-slide-meta-timeout" name="cycloneslider_metas[<?php echo $i; ?>][timeout]" type="number" value="<?php echo esc_attr(@$slide['timeout']); ?>" />
							<span class="note"> <?php _e('Milliseconds', 'cycloneslider'); ?></span>
							<div class="clear"></div>
						</div>
						
						
						<div class="cycloneslider-slide-tile-properties">
							
							<div class="field field-inline">
								<label for=""><?php _e('Tile Count:', 'cycloneslider'); ?></label>
								<input class="widefat cycloneslider-slide-meta-tile-count" name="cycloneslider_metas[<?php echo $i; ?>][tile_count]" type="number" value="<?php echo esc_attr(@$slide['tile_count']); ?>" />
								<span class="note"> <?php _e('The number of tiles to use in the transition.', 'cycloneslider'); ?></span>
								<div class="clear"></div>
							</div>
							<!--
							<label for=""><?php _e('Tile Delay:', 'cycloneslider'); ?></label>
							<input class="widefat cycloneslider-slide-meta-tile-delay" name="cycloneslider_metas[<?php echo $i; ?>][tile_delay]" type="text" value="<?php echo esc_attr(@$slide['tile_delay']); ?>" />
							<span class="note"> <?php _e('Milliseconds to delay each individual tile transition.', 'cycloneslider'); ?></span>
							<div class="cycloneslider-spacer-15"></div>
							-->
							<div class="field field-inline">
								<label for=""><?php _e('Tile Position:', 'cycloneslider'); ?></label>
								<select id="" name="cycloneslider_metas[<?php echo $i; ?>][tile_vertical]">
									<option <?php echo ('true'==$slide['tile_vertical']) ? 'selected="selected"' : ''; ?> value="true"><?php _e('Vertical', 'cycloneslider'); ?></option>
									<option <?php echo ('false'==$slide['tile_vertical']) ? 'selected="selected"' : ''; ?> value="false"><?php _e('Horizontal', 'cycloneslider'); ?></option>
								</select>
								<div class="clear"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="clear"></div>
		</div><!-- // end .cs-slide-image -->
		<div class="cs-slide-youtube">
			<div class="field">
				<label for="cs_youtube_url-<?php echo $i; ?>" class="cs-changeling-id"><?php _e('YouTube URL:', 'cycloneslider'); ?></label>
				<input id="cs_youtube_url-<?php echo $i; ?>" type="text" class="widefat cs-changeling-id cs-youtube-url" name="cycloneslider_metas[<?php echo $i; ?>][youtube_url]" value="<?php echo esc_attr($slide['youtube_url']); ?>" />
				<span class="note"><?php _e('Copy and paste a valid YouTube URL here.', 'cycloneslider'); ?></span>
			</div>
			<div class="field field-normal last">
				<input type="hidden" name="cycloneslider_metas[<?php echo $i; ?>][youtube_related]" value="false" />
				<input id="cs_youtube_related-<?php echo $i; ?>" type="checkbox" class="widefat cs-changeling-id cs-youtube-related" name="cycloneslider_metas[<?php echo $i; ?>][youtube_related]" value="true" <?php checked( $slide['youtube_related'], 'false' ); ?> />
				<label for="cs_youtube_related-<?php echo $i; ?>" class="cs-changeling-id"><?php _e('Do not show suggested videos when the video finishes.', 'cycloneslider'); ?></label>
			</div>
		</div><!-- // end .cs-slide-youtube -->
		<div class="cs-slide-vimeo">
			<div class="field last">
				<label for=""><?php _e('Vimeo URL:', 'cycloneslider'); ?></label>
				<input type="text" class="widefat cs-vimeo-url" name="cycloneslider_metas[<?php echo $i; ?>][vimeo_url]" value="<?php echo esc_attr($slide['vimeo_url']); ?>" />
				<span class="note"><?php _e('Copy and paste a valid Vimeo URL here.', 'cycloneslider'); ?></span>
			</div>
		</div><!-- // end .cs-slide-vimeo -->
		<div class="cs-slide-custom">
			<div class="field last">
				<label for=""><?php _e('Custom HTML', 'cycloneslider'); ?></label>
				<textarea class="widefat cs-custom-html" name="cycloneslider_metas[<?php echo $i; ?>][custom]"><?php echo esc_textarea($slide['custom']); ?></textarea>
			</div>
		</div><!-- // end .cs-slide-custom -->
		<div class="clear"></div>
		<?php echo $debug ?>
	</div>
</div>