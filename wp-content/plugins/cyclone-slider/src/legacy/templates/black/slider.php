<?php if($slider_count>0) $slider_id = $slider_id.'-'.$slider_count; ?>
<div class="cycloneslider cycloneslider-template-black" id="cycloneslider-<?php echo $slider_id; ?>">
	<div class="cycloneslider-slides">
		<?php foreach($slider_metas as $i=>$slider_meta): ?>
			<div class="cycloneslider-slide">
				<?php if ($slider_meta['link']!='') : ?><a target="<?php echo ('_blank'==$slider_meta['link_target']) ? '_blank' : '_self'; ?>" href="<?php echo $slider_meta['link'];?>"><?php endif; ?>
				<img src="<?php echo cycloneslider_thumb($slider_meta['id'], $slider_settings['width'], $slider_settings['height'], false, $slider_meta); ?>" width="<?php echo $slider_settings['width'];?>" height="<?php echo $slider_settings['height'];?>" alt="slide" />
				<?php if ($slider_meta['link']!='') : ?></a><?php endif; ?>
				<?php if(!empty($slider_meta['title']) or !empty($slider_meta['description'])) : ?>
				<div class="cycloneslider-caption">
					<div class="cycloneslider-caption-title"><?php echo $slider_meta['title'];?></div>
					<div class="cycloneslider-caption-description"><?php echo $slider_meta['description'];?></div>
				</div>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
	</div>
	<?php if ($slider_settings['show_nav']) : ?>
	<div class="cycloneslider-pager"></div>
	<?php endif; ?>
	<?php if ($slider_settings['show_prev_next']) : ?>
	<div class="cycloneslider-prev">Prev</div>
	<div class="cycloneslider-next">Next</div>
	<?php endif; ?>
</div>
<div>
<script type="text/javascript">
jQuery(document).ready(function(){
	(function() {
		var slider = '#cycloneslider-<?php echo $slider_id; ?>';
		jQuery(slider).width(<?php echo $slider_settings['width']; ?>);
		jQuery(slider+' .cycloneslider-slides').height(<?php echo $slider_settings['height']; ?>);
		jQuery(slider+' .cycloneslider-slides').cycle({
			fx: "<?php echo $slider_settings['fx']; ?>",
			speed: <?php echo $slider_settings['speed']; ?>,
			timeout: <?php echo $slider_settings['timeout']; ?>,
			pager:jQuery(slider+' .cycloneslider-pager'),
			prev:jQuery(slider+' .cycloneslider-prev'),
			next:jQuery(slider+' .cycloneslider-next'),
			pause:<?php echo $slider_settings['hover_pause']; ?>
		});
	})();
});
</script>
</div>