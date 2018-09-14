<?php if(!defined('ABSPATH')) die('Direct access denied.'); ?>
<ul class="cs-templates">
	<li class="header">
		<span class="template-name"><?php _e('Name', 'cycloneslider'); ?></span>
		<span class="supported-slide-types"><?php _e('Supported Slides', 'cycloneslider'); ?></span>
		<span class="cs-location"><?php _e('Location', 'cycloneslider'); ?></span>
		<span class="selected"><?php _e('Selected', 'cycloneslider'); ?></span>
		<span class="clear"></span>
	</li>
	<?php foreach($templates as $name=>$template): ?>
	<li class="body <?php echo ($template['selected']) ? 'active' : ''; ?>">
		<input <?php checked( $slider_settings['template'], $name); ?> id="template-<?php echo esc_attr($name); ?>" type="radio" name="cycloneslider_settings[template]" value="<?php echo esc_attr($name); ?>" />
		<span class="template-name"><?php echo esc_attr(ucwords(str_replace('-',' ',$name))); ?></span>
		<span class="supported-slide-types">
			<?php if(in_array('image', $template['supports'])): ?>
			<i title="Image" class="icon-picture"></i>
			<?php endif; ?>
			<?php if(in_array('youtube', $template['supports'])): ?>
			<i title="YouTube" class="icon-youtube-play"></i>
			<?php endif; ?>
			<?php if(in_array('vimeo', $template['supports'])): ?>
			<i title="Vimeo" class="icon-play"></i>
			<?php endif; ?>
			<?php if(in_array('custom', $template['supports'])): ?>
			<i title="Custom HTML" class="icon-code"></i>
			<?php endif; ?>
		</span>
		<span class="cs-location">
			<a href="#" data-content="<?php echo wp_kses_post($template['location_details']); ?>"><?php echo $template['location_name']; ?></a>
			<?php if( !empty( $template['warning'] ) ): ?>
				<a href="#" data-content="<?php echo wp_kses_post($template['warning']); ?>" ><i class="icon-warning-sign"></i></a>
			<?php endif; ?>
		</span>
		<span class="selected"><i class="icon-ok"></i></span>
		<span class="clear"></span>
	</li>
	<?php endforeach; ?>
</ul>
<div class="clear"></div>
<?php echo $debug ?>