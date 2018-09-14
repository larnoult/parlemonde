<?php if(!defined('ABSPATH')) die('Direct access denied.'); ?>

<?php
// For description of variables go to: http://www.codefleet.net/cyclone-slider-2/#template-variables
?>
<div class="cycloneslider cycloneslider-template-galleria cycloneslider-width-<?php echo esc_attr( $slider_settings['width_management'] ); ?>"
    id="<?php echo esc_attr( $slider_html_id ); ?>"
    <?php echo ( 'responsive' == $slider_settings['width_management'] ) ? 'style="max-width:'.esc_attr( $slider_settings['width'] ).'px"' : ''; ?>
    <?php echo ( 'fixed' == $slider_settings['width_management'] ) ? 'style="width:'.esc_attr( $slider_settings['width'] ).'px"' : ''; ?>
    >
	<div class="cycloneslider-slides"
        data-cycle-allow-wrap="<?php echo esc_attr( $slider_settings['allow_wrap'] ); ?>"
        data-cycle-dynamic-height="<?php echo esc_attr( $slider_settings['dynamic_height'] ); ?>"
        data-cycle-auto-height="<?php echo esc_attr( $slider_settings['auto_height'] ); ?>"
        data-cycle-auto-height-easing="<?php echo esc_attr( $slider_settings['auto_height_easing'] ); ?>"
        data-cycle-auto-height-speed="<?php echo esc_attr( $slider_settings['auto_height_speed'] ); ?>"
        data-cycle-delay="<?php echo esc_attr( $slider_settings['delay'] ); ?>"
        data-cycle-easing="<?php echo esc_attr( $slider_settings['easing'] ); ?>"
        data-cycle-fx="<?php echo esc_attr( $slider_settings['fx'] ); ?>"
        data-cycle-hide-non-active="<?php echo esc_attr( $slider_settings['hide_non_active'] ); ?>"
        data-cycle-log="false"
        data-cycle-next="#<?php echo esc_attr( $slider_html_id ); ?> .cycloneslider-next"
        data-cycle-pager="#<?php echo esc_attr( $slider_html_id ); ?> .cycloneslider-pager"
        data-cycle-pause-on-hover="<?php echo esc_attr( $slider_settings['hover_pause'] ); ?>"
        data-cycle-prev="#<?php echo esc_attr( $slider_html_id ); ?> .cycloneslider-prev"
        data-cycle-slides="&gt; div"
        data-cycle-speed="<?php echo esc_attr( $slider_settings['speed'] ); ?>"
        data-cycle-swipe="<?php echo esc_attr( $slider_settings['swipe'] ); ?>"
        data-cycle-tile-count="<?php echo esc_attr( $slider_settings['tile_count'] ); ?>"
        data-cycle-tile-delay="<?php echo esc_attr( $slider_settings['tile_delay'] ); ?>"
        data-cycle-tile-vertical="<?php echo esc_attr( $slider_settings['tile_vertical'] ); ?>"
        data-cycle-timeout="<?php echo esc_attr( $slider_settings['timeout'] ); ?>"
        >
		<?php foreach($slides as $slide): ?>
			<?php if ( 'image' == $slide['type'] ) : ?>
				<?php
				$slide_titles[] = esc_js($slide['title']);
				$slide_descs[] = esc_js($slide['description']);
				$slider_settings['resize'] = 1;
				?>
				<div class="cycloneslider-slide" <?php echo cyclone_slide_settings($slide, $slider_settings); ?>>
                    
					<?php if( 'lightbox' == $slide['link_target'] ): ?>
                        <a class="cycloneslider-caption-more magnific-pop" href="<?php echo esc_url( $slide['full_image_url'] ); ?>" alt="<?php echo $slide['img_alt'];?>">
                    <?php elseif ( '' != $slide['link'] ) : ?>
                        <?php if( '_blank' == $slide['link_target'] ): ?>
                            <a class="cycloneslider-caption-more" target="_blank" href="<?php echo esc_url( $slide['link'] ); ?>">
                        <?php else: ?>
                            <a class="cycloneslider-caption-more" href="<?php echo esc_url( $slide['link']); ?>">
                        <?php endif; ?>
                    <?php endif; ?>
					
					<img src="<?php echo cyclone_slide_image_url($slide['id'], $slider_settings['width'], $slider_settings['height'], array('current_slide_settings'=>$slide, 'slideshow_settings'=>$slider_settings) ); ?>" alt="<?php echo $slide['img_alt'];?>" title="<?php echo $slide['img_title'];?>" />
					
					<?php if( 'lightbox' == $slide['link_target'] or ('' != $slide['link']) ) : ?>
                        </a>
                    <?php endif; ?>
					
				</div>
			<?php endif; ?>
		<?php endforeach; ?>
	</div>
	<div class="cycloneslider-controls" data-titles="<?php echo (implode(",",$slide_titles)); ?>" data-descriptions="<?php echo (implode(",",$slide_descs)); ?>">
		<?php if ($slider_settings['timeout'] > 0): ?>
		<div class="cycloneslider-autoplay <?php echo ($slider_settings['timeout'] > 0) ? 'pause' : ''; ?>"></div>
		<div class="cycloneslider-sep"></div>
		<?php endif; ?>
		<div class="cycloneslider-counter">0 / 0</div>
		<div class="cycloneslider-sep"></div>
		<div class="cycloneslider-caption-title"></div>
		<div class="cycloneslider-caption-description"></div>
		<?php if ($slider_settings['show_nav']) : ?>
		<div class="cycloneslider-thumbs"></div>
		<div class="cycloneslider-sep cycloneslider-sep-right"></div>
		<?php endif; ?>
		<div class="clear"></div>
	</div>
	<?php if ($slider_settings['show_prev_next']) : ?>
	<span class="cycloneslider-prev">Prev</span>
	<span class="cycloneslider-next">Next</span>
	<?php endif; ?>
	<?php if ($slider_settings['show_nav']) : ?>
	<div class="cycloneslider-thumbnails">
		<div class="thumbnails-inner">
			<div class="thumbnails-carousel cycle-slideshow"
				data-cycle-fx="carousel"
				data-cycle-timeout="0"
				data-cycle-next="#<?php echo esc_attr( $slider_html_id ); ?> .carousel-next"
				data-cycle-prev="#<?php echo esc_attr( $slider_html_id ); ?> .carousel-prev"
				data-allow-wrap="false"
				data-cycle-carousel-fluid="true"
				data-cycle-log="false"
				>
			<?php foreach($slides as $slide): ?>
				<?php if ($slide['type']=='image') : ?>
					<img src="<?php echo cyclone_slide_image_url($slide['id'], 60, 60, array('current_slide_settings'=>$slide, 'slideshow_settings'=>$slider_settings, 'resize_option'=>'crop') ); ?>" width="60" height="60" alt="<?php echo $slide['img_alt'];?>" title="<?php echo esc_attr( $slide['img_title'] ); ?>" />
				<?php endif; ?>
			<?php endforeach; ?>
			</div>
			<div class="carousel-prev"></div>
			<div class="carousel-next"></div>	
		</div>
	</div>
	<?php endif; ?>
</div>