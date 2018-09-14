<?php if(!defined('ABSPATH')) die('Direct access denied.'); ?>

<div class="wrap">
    <div id="icon-tools" class="icon32"><br></div>
    <h2 class="nav-tab-wrapper">
        <a class="nav-tab nav-tab-active" href="<?php echo $export_page_url; ?>"><?php _e('Export', 'cycloneslider'); ?></a>
        <a class="nav-tab" href="<?php echo $import_page_url; ?>"><?php _e('Import', 'cycloneslider'); ?></a>
    </h2>
    <h2><?php _e('Cyclone Slider Export', 'cycloneslider'); ?></h2>
    <div class="intro">
        <?php if(CYCLONE_DEBUG) echo cyclone_slider_debug( $cycloneslider_export ); ?>
    </div>
    <form method="post" action="<?php echo $form_url; ?>">
        <input type="hidden" name="<?php echo $nonce_name; ?>" value="<?php echo $nonce; ?>" />
        <input type="hidden" name="cycloneslider_export_step" value="1" />
        
        <?php if($sliders): ?>
        <p><?php _e('Select sliders:', 'cycloneslider'); ?></p>
        <table class="form-table">
            <tr>
                <td>
                    <label for="cs-select-all">
                        <input type="checkbox" id="cs-select-all" name="cycloneslider_export[all]" value="1" <?php checked($cycloneslider_export['all'], 1); ?> />
                        <span><strong><?php _e('Select All', 'cycloneslider'); ?></strong></span>
                    </label> <br />
                    <hr />
                    <?php foreach($sliders as $slider): ?>
                        <label for="cs-slider-<?php echo $slider['post_name']; ?>">
                            <input class="cs-sliders" type="checkbox" id="cs-slider-<?php echo $slider['post_name']; ?>" name="cycloneslider_export[sliders][]" value="<?php echo $slider['post_name']; ?>" <?php echo ( in_array($slider['post_name'], $cycloneslider_export['sliders']) ) ? 'checked="checked"' : '' ; ?> />
                            <span><em><?php echo $slider['post_title']; ?></em></span>
                        </label> <br />
                    <?php endforeach; ?>
                </td>
            </tr>
        </table>
        <?php else: ?>
        <p><?php _e('No slider to export.', 'cycloneslider'); ?></p>
        <?php endif; ?>
        <br /><br />
        <?php submit_button( __('Next', 'cycloneslider'), 'primary', 'submit', false) ?>
    </form>
</div>