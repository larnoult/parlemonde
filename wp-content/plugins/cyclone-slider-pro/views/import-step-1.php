<?php if(!defined('ABSPATH')) die('Direct access denied.'); ?>

<div class="wrap">
    <div id="icon-tools" class="icon32"><br></div>
    <h2 class="nav-tab-wrapper">
        <a class="nav-tab" href="<?php echo $export_page_url; ?>"><?php _e('Export', 'cycloneslider'); ?></a>
        <a class="nav-tab nav-tab-active" href="<?php echo $import_page_url; ?>"><?php _e('Import', 'cycloneslider'); ?></a>
    </h2>
	<h2><?php _e('Cyclone Slider Import', 'cycloneslider'); ?></h2>
	<div class="intro">
		<?php //echo cyclone_slider_debug( $results ); ?>
	</div>
	<form enctype="multipart/form-data" method="post" action="<?php echo $form_url; ?>">
		<input type="hidden" name="<?php echo $nonce_name; ?>" value="<?php echo $nonce; ?>" />
		<input type="hidden" name="cycloneslider_import_step" value="1" />
		<table class="form-table">
			<tr>
				<th><label for="cycloneslider_import"><?php _e('Import Zip File:', 'cycloneslider'); ?></label></th>
				<td>
					<input id="cycloneslider_import" type="file" name="cycloneslider_import" />
				</td>
			</tr>
		</table>
		<br /><br />
		<?php submit_button( __('Next', 'cycloneslider'), 'primary', 'submit', false) ?>
	</form>
</div>