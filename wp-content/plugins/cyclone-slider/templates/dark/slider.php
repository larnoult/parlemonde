<?php if(!defined('ABSPATH')) die('Direct access denied.'); ?>

<div tabindex="0" class="cycloneslider cycloneslider-template-dark cycloneslider-width-<?php echo esc_attr( $slider_settings['width_management'] ); ?>"
    id="<?php echo esc_attr( $slider_html_id ); ?>"
    <?php echo ( 'responsive' == $slider_settings['width_management'] ) ? 'style="max-width:'.esc_attr( $slider_settings['width'] ).'px"' : ''; ?>
    <?php echo ( 'fixed' == $slider_settings['width_management'] ) ? 'style="width:'.esc_attr( $slider_settings['width'] ).'px"' : ''; ?>
    >
    <div class="cycloneslider-slides cycle-slideshow"
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
                <div class="cycloneslider-slide cycloneslider-slide-image" <?php echo $slide['slide_data_attributes']; ?>>
                    <img src="<?php echo $slide['image_url']; ?>" alt="<?php echo $slide['img_alt'];?>" title="<?php echo $slide['img_title'];?>" />
                    <?php if(!empty($slide['title']) or !empty($slide['description'])) : ?>
                        <div class="cycloneslider-caption">
                            <div class="cycloneslider-caption-title"><?php echo wp_kses_post( $slide['title'] );?></div>
                            <div class="cycloneslider-caption-description"><?php echo wp_kses_post( $slide['description'] );?></div>
                            <?php if( 'lightbox' == $slide['link_target'] ): ?>
                                <a class="cycloneslider-caption-more magnific-pop" href="<?php echo esc_url( $slide['full_image_url'] ); ?>" alt="<?php echo $slide['img_alt'];?>"><?php _e('View Larger Image', 'cycloneslider'); ?></a>
                            <?php elseif ( '' != $slide['link'] ) : ?>
                                <?php if( '_blank' == $slide['link_target'] ): ?>
                                    <a class="cycloneslider-caption-more" target="_blank" href="<?php echo esc_url( $slide['link'] );?>"><?php _e('Learn More', 'cycloneslider'); ?></a>
                                <?php else: ?>
                                    <a class="cycloneslider-caption-more" href="<?php echo esc_url( $slide['link'] );?>"><?php _e('Learn More', 'cycloneslider'); ?></a>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php elseif ( 'youtube' == $slide['type'] ) : ?>
                <div class="cycloneslider-slide cycloneslider-slide-youtube" <?php echo $slide['slide_data_attributes']; ?> style="padding-bottom:<?php echo $slider_settings['height']/$slider_settings['width']*100;?>%">
                    <?php echo $slide['youtube_embed_code']; ?>
                </div>
            <?php elseif ( 'vimeo' == $slide['type'] ) : ?>
                <div class="cycloneslider-slide cycloneslider-slide-vimeo" <?php echo $slide['slide_data_attributes']; ?> style="padding-bottom:<?php echo $slider_settings['height']/$slider_settings['width']*100;?>%">
                    <?php echo $slide['vimeo_embed_code']; ?>
                </div>
            <?php elseif ( 'video' == $slide['type'] ) : ?>
                <div class="cycloneslider-slide" <?php echo $slide['slide_data_attributes']; ?>>
                    <p><?php _e('Slide type not supported.', 'cycloneslider'); ?></p>
                </div>
            <?php elseif ( 'custom' == $slide['type'] ) : ?>
                <div class="cycloneslider-slide cycloneslider-slide-custom" <?php echo $slide['slide_data_attributes']; ?>>
                    <?php echo wp_kses_post( $slide['custom'] ); ?>
                </div>
            <?php elseif ( 'testimonial' == $slide['type'] ) : ?>
                <div class="cycloneslider-slide cycloneslider-slide-testimonial" <?php echo $slide['slide_data_attributes']; ?>>
                    <?php if ( '' != $slide['testimonial_img_url'] ) : ?>
                        <img src="<?php echo esc_attr($slide['testimonial_img_url']); ?>" alt="<?php _e('Quote Image', 'cycloneslider'); ?>">
                    <?php endif; ?>
                    <blockquote>
                        <p><?php echo $slide['testimonial']; ?></p>
                    </blockquote>
                    <?php if ( '' != $slide['testimonial_author'] ) : ?>
                        <?php if( '_blank' == $slide['testimonial_link_target'] ): ?>
                            <p class="cycloneslider-testimonial-author">
                                <a target="_blank" href="<?php echo esc_url( $slide['testimonial_link'] );?>">-<?php echo esc_attr( $slide['testimonial_author'] );?></a>
                            </p>
                        <?php else: ?>
                            <p class="cycloneslider-testimonial-author">
                                <a href="<?php echo esc_url( $slide['testimonial_link'] );?>">-<?php echo esc_attr( $slide['testimonial_author'] );?></a>
                            </p>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
    <?php if ($slider_settings['show_nav']) : ?>
    <div class="cycloneslider-pager"></div>
    <?php endif; ?>
    <?php if ($slider_settings['show_prev_next']) : ?>
    <a href="#" class="cycloneslider-prev">
        <span class="arrow"></span>
    </a>
    <a href="#" class="cycloneslider-next">
        <span class="arrow"></span>
    </a>
    <?php endif; ?>
</div>