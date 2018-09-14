<?php if(!defined('ABSPATH')) die('Direct access denied.'); ?>
<table class="cs-templates">
	<thead>
		<tr>
			<th width="25%"><?php _e('Name', 'cycloneslider'); ?></th>
			<th width="25%"><?php _e('Supported Slides', 'cycloneslider'); ?></th>
			<th width="25%"><?php _e('Location', 'cycloneslider'); ?></th>
			<th width="25%">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($templates as $id=>$template): ?>
		<tr class="<?php echo ($id==$slider_settings['template']) ? 'active': '' ;?>">
			<td>
				<label for="template-<?php echo esc_attr($id); ?>"><?php echo esc_html($template['name']); ?></label>
			</td>
			<td>
				<?php if(in_array('image', $template['supports'])): ?>
				<a class="boxxy" href="#" data-content="<?php _e('Image', 'cycloneslider'); ?>" title="<?php _e('Image', 'cycloneslider'); ?>">
					<svg viewBox="0 0 24 24"><path d="M8.5,13.5L11,16.5L14.5,12L19,18H5M21,19V5C21,3.89 20.1,3 19,3H5A2,2 0 0,0 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19Z" /></svg>
				</a>
				<?php endif; ?>
				<?php if(in_array('youtube', $template['supports'])): ?>
					<a class="boxxy" href="#" data-content="<?php _e('YouTube', 'cycloneslider'); ?>" title="<?php _e('YouTube', 'cycloneslider'); ?>">
						<svg viewBox="0 0 24 24"><path d="M10,16.5V7.5L16,12M20,4.4C19.4,4.2 15.7,4 12,4C8.3,4 4.6,4.19 4,4.38C2.44,4.9 2,8.4 2,12C2,15.59 2.44,19.1 4,19.61C4.6,19.81 8.3,20 12,20C15.7,20 19.4,19.81 20,19.61C21.56,19.1 22,15.59 22,12C22,8.4 21.56,4.91 20,4.4Z" /></svg>
					</a>
				<?php endif; ?>
				<?php if(in_array('vimeo', $template['supports'])): ?>
					<a class="boxxy" href="#" data-content="<?php _e('Vimeo', 'cycloneslider'); ?>" title="<?php _e('Vimeo', 'cycloneslider'); ?>">
						<svg viewBox="0 0 24 24"><path d="M22,7.42C21.91,9.37 20.55,12.04 17.92,15.44C15.2,19 12.9,20.75 11,20.75C9.85,20.75 8.86,19.67 8.05,17.5C7.5,15.54 7,13.56 6.44,11.58C5.84,9.42 5.2,8.34 4.5,8.34C4.36,8.34 3.84,8.66 2.94,9.29L2,8.07C3,7.2 3.96,6.33 4.92,5.46C6.24,4.32 7.23,3.72 7.88,3.66C9.44,3.5 10.4,4.58 10.76,6.86C11.15,9.33 11.42,10.86 11.57,11.46C12,13.5 12.5,14.5 13.05,14.5C13.47,14.5 14.1,13.86 14.94,12.53C15.78,11.21 16.23,10.2 16.29,9.5C16.41,8.36 15.96,7.79 14.94,7.79C14.46,7.79 13.97,7.9 13.46,8.12C14.44,4.89 16.32,3.32 19.09,3.41C21.15,3.47 22.12,4.81 22,7.42Z" /></svg>
					</a>
				<?php endif; ?>
				<?php if(in_array('custom', $template['supports'])): ?>
					<a class="boxxy" href="#" data-content="<?php _e('Custom HTML', 'cycloneslider'); ?>" title="<?php _e('Custom HTML', 'cycloneslider'); ?>">
						<svg viewBox="0 0 24 24"><path d="M14.6,16.6L19.2,12L14.6,7.4L16,6L22,12L16,18L14.6,16.6M9.4,16.6L4.8,12L9.4,7.4L8,6L2,12L8,18L9.4,16.6Z" /></svg>
					</a>
				<?php endif; ?>
				<?php if(in_array('testimonial', $template['supports'])): ?>
					<a class="boxxy" href="#" data-content="<?php _e('Testimonial', 'cycloneslider'); ?>" title="<?php _e('Testimonial', 'cycloneslider'); ?>">
						<svg viewBox="0 0 24 24"><path d="M10,7L8,11H11V17H5V11L7,7H10M18,7L16,11H19V17H13V11L15,7H18Z" /></svg>
					</a>
				<?php endif; ?>
			</td>
			<td>
				<a class="boxxy" href="#" data-content="<?php echo wp_kses_post($template['location_details']); ?>"><?php echo $template['location_name']; ?></a>
				<?php if( !empty( $template['warning'] ) ): ?>
					<a class="boxxy" href="#" data-content="<?php echo wp_kses_post($template['warning']); ?>" ><i class="icon-warning-sign"></i></a>
				<?php endif; ?>
			</td>
			<td>
				<input <?php checked( $slider_settings['template'], $id); ?> id="template-<?php echo esc_attr($id); ?>" type="radio" name="cycloneslider_settings[template]" value="<?php echo esc_attr($id); ?>" />
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<div class="cs-templates-buttons">
	<a target="_blank" href="http://docs.codefleet.net/cyclone-slider/templating/" class="button-secondary"><i class="icon-book"></i> <?php _e('Learn More About Templates', 'cycloneslider'); ?></a>
	<a target="_blank" href="https://www.codefleet.net/cyclone-slider/templates/" class="button-primary"><i class="icon-plus"></i> <?php _e('More Templates', 'cycloneslider'); ?></a>
</div>