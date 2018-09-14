<div class="cs-slide-settings">
    <div class="expandable-box last">
        <div class="expandable-header">
            <svg viewBox="0 0 24 24"><path d="M12,15.5A3.5,3.5 0 0,1 8.5,12A3.5,3.5 0 0,1 12,8.5A3.5,3.5 0 0,1 15.5,12A3.5,3.5 0 0,1 12,15.5M19.43,12.97C19.47,12.65 19.5,12.33 19.5,12C19.5,11.67 19.47,11.34 19.43,11L21.54,9.37C21.73,9.22 21.78,8.95 21.66,8.73L19.66,5.27C19.54,5.05 19.27,4.96 19.05,5.05L16.56,6.05C16.04,5.66 15.5,5.32 14.87,5.07L14.5,2.42C14.46,2.18 14.25,2 14,2H10C9.75,2 9.54,2.18 9.5,2.42L9.13,5.07C8.5,5.32 7.96,5.66 7.44,6.05L4.95,5.05C4.73,4.96 4.46,5.05 4.34,5.27L2.34,8.73C2.21,8.95 2.27,9.22 2.46,9.37L4.57,11C4.53,11.34 4.5,11.67 4.5,12C4.5,12.33 4.53,12.65 4.57,12.97L2.46,14.63C2.27,14.78 2.21,15.05 2.34,15.27L4.34,18.73C4.46,18.95 4.73,19.03 4.95,18.95L7.44,17.94C7.96,18.34 8.5,18.68 9.13,18.93L9.5,21.58C9.54,21.82 9.75,22 10,22H14C14.25,22 14.46,21.82 14.5,21.58L14.87,18.93C15.5,18.67 16.04,18.34 16.56,17.94L19.05,18.95C19.27,19.03 19.54,18.95 19.66,18.73L21.66,15.27C21.78,15.05 21.73,14.78 21.54,14.63L19.43,12.97Z" /></svg>
            <span><?php _e('Slide Properties', 'cycloneslider'); ?></span>
        </div>
        <div class="expandable-body">
            <div class="field field-inline">
                <label for=""><?php _e('Hidden:', 'cycloneslider'); ?></label>
                <input class="" name="cycloneslider_metas[<?php echo esc_attr($i); ?>][hidden]" type="hidden" value="0" />
                <input class="" name="cycloneslider_metas[<?php echo esc_attr($i); ?>][hidden]" type="checkbox" value="1" <?php checked( $slide['hidden'], '1' ); ?> />
                <div class="clear"></div>
            </div>
            
            <div class="clear"></div>
            
            <div class="field field-inline">
                <label for=""><?php _e('Transition Effects:', 'cycloneslider'); ?></label>
                <select id="" class="cycloneslider_metas_fx" name="cycloneslider_metas[<?php echo esc_attr($i); ?>][fx]">
                    <option value="default"><?php _e('Default', 'cycloneslider'); ?></option>
                    <?php foreach($effects as $value=>$name): ?>
                    <option value="<?php echo esc_attr($value); ?>" <?php echo ($slide['fx']==$value) ? 'selected="selected"' : ''; ?>><?php echo esc_html($name); ?></option>
                    <?php endforeach; ?>
                </select>
                <div class="clear"></div>
            </div>
            
            <div class="field field-inline">
                <label for=""><?php _e('Effects Speed:', 'cycloneslider'); ?></label>
                <input class="widefat cycloneslider-slide-meta-speed" name="cycloneslider_metas[<?php echo esc_attr($i); ?>][speed]" type="number" value="<?php echo esc_attr(@$slide['speed']); ?>" />
                <span class="note"> <?php _e('Milliseconds', 'cycloneslider'); ?></span>
                <div class="clear"></div>
            </div>
            
            <div class="field field-inline">
                <label for=""><?php _e('Next Slide Delay:', 'cycloneslider'); ?></label>
                <input class="widefat cycloneslider-slide-meta-timeout" name="cycloneslider_metas[<?php echo esc_attr($i); ?>][timeout]" type="number" value="<?php echo esc_attr(@$slide['timeout']); ?>" />
                <span class="note"> <?php _e('Milliseconds', 'cycloneslider'); ?></span>
                <div class="clear"></div>
            </div>
            
            
            <div class="cycloneslider-slide-tile-properties">
                
                <div class="field field-inline">
                    <label for=""><?php _e('Tile Count:', 'cycloneslider'); ?></label>
                    <input class="widefat cycloneslider-slide-meta-tile-count" name="cycloneslider_metas[<?php echo esc_attr($i); ?>][tile_count]" type="number" value="<?php echo esc_attr(@$slide['tile_count']); ?>" />
                    <span class="note"> <?php _e('The number of tiles to use in the transition.', 'cycloneslider'); ?></span>
                    <div class="clear"></div>
                </div>
                <!--
                <label for=""><?php _e('Tile Delay:', 'cycloneslider'); ?></label>
                <input class="widefat cycloneslider-slide-meta-tile-delay" name="cycloneslider_metas[<?php echo esc_attr($i); ?>][tile_delay]" type="text" value="<?php echo esc_attr(@$slide['tile_delay']); ?>" />
                <span class="note"> <?php _e('Milliseconds to delay each individual tile transition.', 'cycloneslider'); ?></span>
                <div class="cycloneslider-spacer-15"></div>
                -->
                <div class="field field-inline">
                    <label for=""><?php _e('Tile Position:', 'cycloneslider'); ?></label>
                    <select id="" name="cycloneslider_metas[<?php echo esc_attr($i); ?>][tile_vertical]">
                        <option <?php echo ('true'==$slide['tile_vertical']) ? 'selected="selected"' : ''; ?> value="true"><?php _e('Vertical', 'cycloneslider'); ?></option>
                        <option <?php echo ('false'==$slide['tile_vertical']) ? 'selected="selected"' : ''; ?> value="false"><?php _e('Horizontal', 'cycloneslider'); ?></option>
                    </select>
                    <div class="clear"></div>
                </div>
            </div>
        </div>
    </div>
</div>
