<?php if(!defined('ABSPATH')) die('Direct access denied.'); ?>

<div class="wrap">
    <div id="icon-tools" class="icon32"><br></div>
    
	<?php $this->render('export-import-tabs.php', array('tabs'=>$tabs)); ?>
	
    <h2><?php _e('Cyclone Slider Nextgen Exporter', 'cycloneslider'); ?></h2>
    
	<?php $this->render('error-message.php', array('error'=>$error)); ?>
	
	<form method="post" action="<?php echo esc_url( $export_page_url ); ?>">
		<input type="hidden" name="<?php echo $nonce_name; ?>" value="<?php echo $nonce; ?>" />
		<input type="hidden" name="cycloneslider_export_step" value="2" />
        <table class="form-table">
			<tr>
				<th><h4><?php _e('Selected slider(s):', 'cycloneslider'); ?></h4></th>
				<td><?php if($page_data['sliders']): ?>
					<ul class="export-page-list ">
						<?php foreach($page_data['sliders'] as $slider): ?>
							<li><?php echo $slider; ?></li>
						<?php endforeach; ?>
					</ul>
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<th><h4><?php _e('File Name:', 'cycloneslider'); ?></h4></th>
				<td>
					<?php echo esc_attr( $page_data['file_name'] ); ?>
				</td>
			</tr>
        </table>
        <br /><br />
		<a class="button" href="<?php echo esc_url( $export_page_url ); ?>"><?php _e('Back', 'cycloneslider'); ?></a>
        <?php submit_button( __('Generate Export File', 'cycloneslider'), 'primary', 'submit', false) ?>
    </form>
</div>