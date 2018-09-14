<?php if(!defined('ABSPATH')) die('Direct access denied.'); ?>
<div class="cs-slide" data-slide-type="<?php echo esc_attr( $slide['type'] ); ?>" data-slide-hidden="<?php echo esc_attr( $slide['hidden'] ); ?>">
	<div class="cs-header">
		<div class="cs-slide-type">
			<input type="hidden" name="cycloneslider_metas[<?php echo esc_attr($i); ?>][type]" value="<?php echo esc_attr($slide['type']); ?>">
			<div class="switcher">
				<div class="display">
					<svg viewBox="0 0 24 24"><path d="M8.5,13.5L11,16.5L14.5,12L19,18H5M21,19V5C21,3.89 20.1,3 19,3H5A2,2 0 0,0 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19Z" /></svg>
					<span><?php _e('Image', 'cycloneslider'); ?></span>
				</div>
				<ul>
					<li data-value="image">
						<svg viewBox="0 0 24 24"><path d="M20,5A2,2 0 0,1 22,7V17A2,2 0 0,1 20,19H4C2.89,19 2,18.1 2,17V7C2,5.89 2.89,5 4,5H20M5,16H19L14.5,10L11,14.5L8.5,11.5L5,16Z" /></svg>
						<span><?php _e('Image', 'cycloneslider'); ?></span>
					</li>
					<li data-value="youtube">
						<svg viewBox="0 0 24 24"><path d="M10,16.5V7.5L16,12M20,4.4C19.4,4.2 15.7,4 12,4C8.3,4 4.6,4.19 4,4.38C2.44,4.9 2,8.4 2,12C2,15.59 2.44,19.1 4,19.61C4.6,19.81 8.3,20 12,20C15.7,20 19.4,19.81 20,19.61C21.56,19.1 22,15.59 22,12C22,8.4 21.56,4.91 20,4.4Z" /></svg>
						<span><?php _e('YouTube', 'cycloneslider'); ?></span>
					</li>
					<li data-value="vimeo">
						<svg viewBox="0 0 24 24"><path d="M22,7.42C21.91,9.37 20.55,12.04 17.92,15.44C15.2,19 12.9,20.75 11,20.75C9.85,20.75 8.86,19.67 8.05,17.5C7.5,15.54 7,13.56 6.44,11.58C5.84,9.42 5.2,8.34 4.5,8.34C4.36,8.34 3.84,8.66 2.94,9.29L2,8.07C3,7.2 3.96,6.33 4.92,5.46C6.24,4.32 7.23,3.72 7.88,3.66C9.44,3.5 10.4,4.58 10.76,6.86C11.15,9.33 11.42,10.86 11.57,11.46C12,13.5 12.5,14.5 13.05,14.5C13.47,14.5 14.1,13.86 14.94,12.53C15.78,11.21 16.23,10.2 16.29,9.5C16.41,8.36 15.96,7.79 14.94,7.79C14.46,7.79 13.97,7.9 13.46,8.12C14.44,4.89 16.32,3.32 19.09,3.41C21.15,3.47 22.12,4.81 22,7.42Z" /></svg>
						<span><?php _e('Vimeo', 'cycloneslider'); ?></span>
					</li>
					<li data-value="custom">
						<svg viewBox="0 0 24 24"><path d="M14.6,16.6L19.2,12L14.6,7.4L16,6L22,12L16,18L14.6,16.6M9.4,16.6L4.8,12L9.4,7.4L8,6L2,12L8,18L9.4,16.6Z" /></svg>
						<span><?php _e('Custom', 'cycloneslider'); ?></span>
					</li>
					<li data-value="testimonial">
						<svg viewBox="0 0 24 24"><path d="M10,7L8,11H11V17H5V11L7,7H10M18,7L16,11H19V17H13V11L15,7H18Z" /></svg>
						<span><?php _e('Testimonial', 'cycloneslider'); ?></span>
					</li>
				</ul>
				<svg viewBox="0 0 24 24"><path d="M7,10L12,15L17,10H7Z" /></svg>
			</div>
		</div>
		<span class="cs-title">
			<?php echo esc_html($box_title); ?>
		</span>
		<span class="cs-controls">
			<button class="cs-minimize" type="button" title="<?php _e('Toggle', 'cycloneslider'); ?>">
				<svg width="16" height="16" viewBox="0 0 24 24"><path d="M20,14H4V10H20" /></svg>
			</button>
			<button class="cs-delete" type="button" title="<?php _e('Delete', 'cycloneslider'); ?>">
				<svg width="16" height="16" viewBox="0 0 24 24"><path d="M13.46,12L19,17.54V19H17.54L12,13.46L6.46,19H5V17.54L10.54,12L5,6.46V5H6.46L12,10.54L17.54,5H19V6.46L13.46,12Z" /></svg>
			</button>
		</span>
		<div class="clear"></div>
	</div>
	<div class="cs-body">
		<?php 
		$this->render('slide-settings.php',
			array(
				'i' => $i,
				'slide' => $slide,
				'effects' => $effects
			)
		);
		?>
		<?php $this->render(
			'slide-edit-image.php',
			array(
				'i' => $i,
                'slide' => $slide,
                'image_url' => $image_url,
                'full_image_url' => $full_image_url,
                'box_title' => $box_title,
                'effects' => $effects
			)
		); ?>
		<div class="cs-slide-youtube">
			<div class="field">
				<label for="cs_youtube_url-<?php echo esc_attr($i); ?>" class="cs-changeling-id"><?php _e('YouTube URL:', 'cycloneslider'); ?></label>
				<input id="cs_youtube_url-<?php echo esc_attr($i); ?>" type="text" class="widefat cs-changeling-id cs-youtube-url" name="cycloneslider_metas[<?php echo esc_attr($i); ?>][youtube_url]" value="<?php echo esc_attr($slide['youtube_url']); ?>" />
				<span class="note"><?php _e('Copy and paste a valid YouTube URL here.', 'cycloneslider'); ?></span>
			</div>
			<div class="field field-normal last">
				<input type="hidden" name="cycloneslider_metas[<?php echo esc_attr($i); ?>][youtube_related]" value="false" />
				<input id="cs_youtube_related-<?php echo esc_attr($i); ?>" type="checkbox" class="widefat cs-changeling-id cs-youtube-related" name="cycloneslider_metas[<?php echo esc_attr($i); ?>][youtube_related]" value="true" <?php checked( $slide['youtube_related'], 'false' ); ?> />
				<label for="cs_youtube_related-<?php echo esc_attr($i); ?>" class="cs-changeling-id"><?php _e('Do not show suggested videos when the video finishes.', 'cycloneslider'); ?></label>
			</div>
		</div><!-- // end .cs-slide-youtube -->
		<div class="cs-slide-vimeo">
			<div class="field last">
				<label for=""><?php _e('Vimeo URL:', 'cycloneslider'); ?></label>
				<input type="text" class="widefat cs-vimeo-url" name="cycloneslider_metas[<?php echo esc_attr($i); ?>][vimeo_url]" value="<?php echo esc_attr($slide['vimeo_url']); ?>" />
				<span class="note"><?php _e('Copy and paste a valid Vimeo URL here.', 'cycloneslider'); ?></span>
			</div>
		</div><!-- // end .cs-slide-vimeo -->
		<div class="cs-slide-custom">
			<div class="field last">
				<label for=""><?php _e('Custom HTML', 'cycloneslider'); ?></label>
				<textarea class="widefat cs-custom-html" name="cycloneslider_metas[<?php echo esc_attr($i); ?>][custom]"><?php echo esc_textarea($slide['custom']); ?></textarea>
			</div>
		</div><!-- // end .cs-slide-custom -->
		
		<div class="cs-slide-testimonial">
			<div class="cs-testimonial-quote">
				<div class="field">
					<label for=""><?php _e('Quote', 'cycloneslider'); ?></label>
					<textarea class="widefat cs-testimonial" name="cycloneslider_metas[<?php echo esc_attr($i); ?>][testimonial]"><?php echo esc_textarea($slide['testimonial']); ?></textarea>
				</div>
				<div class="field last">
					<label for=""><?php _e('Image', 'cycloneslider'); ?></label>
					<div class="cs-image-field">
						<div class="cs-image-thumb">
							<?php if($testimonial_img_url): ?>
								<img src="<?php echo esc_url($testimonial_img_url); ?>" alt="<?php _e('Thumbnail', 'cycloneslider'); ?>">
							<?php endif; ?>
						</div>
						<input class="cs-image-id" name="cycloneslider_metas[<?php echo esc_attr($i); ?>][testimonial_img]" type="hidden" value="<?php echo esc_attr($slide['testimonial_img']); ?>" />
						<input class="button-secondary cs-media-gallery-show" type="button" value="<?php _e('Get Image', 'cycloneslider'); ?>" />
						<?php if($testimonial_img_url): ?>
							<a target="_blank" class="button-secondary" href="<?php echo esc_url($full_testimonial_img_url); ?>"><?php _e('View Image', 'cycloneslider'); ?></a>
						<?php endif; ?>
					</div>
				</div>
			</div><!-- // end .cs-testimonial-quote -->
			<div class="cs-quote-properties">
				<div class="expandable-box">
					<div class="expandable-header first"><?php _e('Author', 'cycloneslider'); ?></div>
					<div class="expandable-body">
						<div class="field last">
							<label for=""><?php _e('Name:', 'cycloneslider'); ?></label> <br>
							<input class="widefat" name="cycloneslider_metas[<?php echo esc_attr($i); ?>][testimonial_author]" type="text" value="<?php echo esc_attr($slide['testimonial_author']); ?>" />
						</div>
					</div>
				</div>
				<div class="expandable-box last">
					<div class="expandable-header"><?php _e('Link', 'cycloneslider'); ?></div>
					<div class="expandable-body">
						<div class="field">
							<label for=""><?php _e('Link URL:', 'cycloneslider'); ?></label> <br>
							<input class="widefat" name="cycloneslider_metas[<?php echo esc_attr($i); ?>][testimonial_link]" type="text" value="<?php echo esc_url($slide['testimonial_link']); ?>" />
						</div>
						<div class="field last">
							<label for=""><?php _e('Open Link in:', 'cycloneslider'); ?></label> <br>
							<select class="" id="" name="cycloneslider_metas[<?php echo esc_attr($i); ?>][testimonial_link_target]">
								<option <?php selected( $slide['testimonial_link_target'], '_self' ); ?> value="_self"><?php _e('Same Window', 'cycloneslider'); ?></option>
								<option <?php selected( $slide['testimonial_link_target'], '_blank' ); ?> value="_blank"><?php _e('New Tab or Window', 'cycloneslider'); ?></option>
							</select>
						</div>
					</div>
				</div>
			</div>
		</div><!-- // end .cs-slide-testimonial -->
	</div><!-- // end .cs-body -->
</div><!-- // end .cs-slide -->