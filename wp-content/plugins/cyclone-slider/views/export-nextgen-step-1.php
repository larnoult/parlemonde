<?php if(!defined('ABSPATH')) die('Direct access denied.'); ?>

<div class="wrap">
    <div id="icon-tools" class="icon32"><br></div>
    
    <?php $this->render('export-import-tabs.php', array('tabs'=>$tabs)); ?>
    
    <h2><?php _e('Cyclone Slider Nextgen Exporter', 'cycloneslider'); ?></h2>
	
	<?php $this->render('error-message.php', array('error'=>$error)); ?>
	
	<form method="post" action="<?php echo esc_url( $export_page_url ); ?>">
		<input type="hidden" name="<?php echo $nonce_name; ?>" value="<?php echo $nonce; ?>" />
		<input type="hidden" name="cycloneslider_export_step" value="1" />
        <?php if($sliders): ?>
            <table class="form-table">
                <tr>
                    <th><h4><?php _e('Choose a NextGEN Gallery:', 'cycloneslider'); ?></h4></th>
                    <td>
                        <label for="cs-select-all">
                            <input type="checkbox" id="cs-select-all" name="<?php echo esc_attr( $transient_name ); ?>[all_sliders]" value="1" <?php checked($page_data['all_sliders'], 1); ?> />
                            <span><strong><?php _e('Select All', 'cycloneslider'); ?></strong></span>
                        </label> <br />
                        <hr />
                        <?php foreach($sliders as $slider): ?>
                            <label for="cs-slider-<?php echo $slider->gid; ?>">
                                <input class="cs-sliders" type="checkbox" id="cs-slider-<?php echo esc_attr( $slider->gid ); ?>" name="<?php echo esc_attr( $transient_name ); ?>[sliders][]" value="<?php echo esc_attr( $slider->title ); ?>" <?php echo ( in_array($slider->gid, $page_data['sliders']) ) ? 'checked="checked"' : '' ; ?> />
                                <span><em><?php echo esc_attr( $slider->title ); ?></em></span>
                            </label> <br />
                        <?php endforeach; ?>
                    </td>
                </tr>
				<tr>
					<th><label for="cs-file_name"><?php _e('File Name:', 'cycloneslider'); ?></label></th>
					<td>
						<input type="text" class="regular-text" id="cs-file_name" name="<?php echo esc_attr( $transient_name ); ?>[file_name]" value="<?php echo esc_attr( $page_data['file_name'] ); ?>" />
					</td>
				</tr>
            </table>
        <?php else: ?>
			<p><?php _e('No slider to export.', 'cycloneslider'); ?></p>
        <?php endif; ?>
        <br /><br />
        <?php submit_button( __('Clear', 'cycloneslider'), 'secondary', 'reset', false) ?>
        <?php submit_button( __('Next', 'cycloneslider'), 'primary', 'submit', false) ?>
    </form>
</div>