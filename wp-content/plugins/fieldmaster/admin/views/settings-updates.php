<?php

// extract
extract($args);


// vars
$active = $license ? true : false;
$nonce = $active ? 'deactivate_pro_licence' : 'activate_pro_licence';
$input = $active ? 'password' : 'text';
$button = $active ? __('Deactivate License', 'fieldmaster') : __('Activate License', 'fieldmaster');
$readonly = $active ? 1 : 0;

?>
<div class="wrap fieldmaster-settings-wrap">

	<h1><?php _e('Updates', 'fieldmaster'); ?></h1>

	<div class="fieldmaster-box" id="fieldmaster-license-information">
		<div class="title">
			<h3><?php _e('License Information', 'fieldmaster'); ?></h3>
		</div>
		<div class="inner">
			<p>Automatic updates are not available for FieldMaster currently.</p>
			<form action="" method="post">
			<div class="fieldmaster-hidden">
				<input type="hidden" name="_fieldmasternonce" value="<?php echo wp_create_nonce( $nonce ); ?>" />
			</div>
			<table class="form-table">
                <tbody>
                	<tr>
                    	<th>
                    		<label for="fieldmaster-field-fieldmaster_pro_licence"><?php _e('License Key', 'fieldmaster'); ?></label>
                    	</th>
						<td>
							<?php

							// render field
							fieldmaster_render_field(array(
								'type'		=> $input,
								'name'		=> 'fieldmaster_pro_licence',
								'value'		=> str_repeat('*', strlen($license)),
								'readonly'	=> $readonly
							));

							?>
						</td>
					</tr>
					<tr>
						<th></th>
						<td>
							<input type="submit" value="<?php echo $button; ?>" class="button button-primary">
						</td>
					</tr>
				</tbody>
			</table>
			</form>

		</div>

	</div>

	<div class="fieldmaster-box" id="fieldmaster-update-information">
		<div class="title">
			<h3><?php _e('Update Information', 'fieldmaster'); ?></h3>
		</div>
		<div class="inner">
			<table class="form-table">
                <tbody>
                	<tr>
                    	<th>
                    		<label><?php _e('Current Version', 'fieldmaster'); ?></label>
                    	</th>
						<td>
							<?php echo $current_version; ?>
						</td>
					</tr>
					<tr>
                    	<th>
                    		<label><?php _e('Latest Version', 'fieldmaster'); ?></label>
                    	</th>
						<td>
							<?php echo $remote_version; ?>
						</td>
					</tr>
					<tr>
                    	<th>
                    		<label><?php _e('Update Available', 'fieldmaster'); ?></label>
                    	</th>
						<td>
							<?php if( $update_available ): ?>

								<span style="margin-right: 5px;"><?php _e('Yes', 'fieldmaster'); ?></span>

								<?php if( $active ): ?>
									<a class="button button-primary" href="<?php echo admin_url('plugins.php?s=Advanced+Custom+Fields+Pro'); ?>"><?php _e('Update Plugin', 'fieldmaster'); ?></a>
								<?php else: ?>
									<a class="button" disabled="disabled" href="#"><?php _e('Please enter your license key above to unlock updates', 'fieldmaster'); ?></a>
								<?php endif; ?>

							<?php else: ?>

								<span style="margin-right: 5px;"><?php _e('No', 'fieldmaster'); ?></span>
								<a class="button" href="<?php echo add_query_arg('force-check', 1); ?>"><?php _e('Check Again', 'fieldmaster'); ?></a>
							<?php endif; ?>
						</td>
					</tr>
					<?php if( $changelog ): ?>
					<tr>
                    	<th>
                    		<label><?php _e('Changelog', 'fieldmaster'); ?></label>
                    	</th>
						<td>
							<?php echo $changelog; ?>
						</td>
					</tr>
					<?php endif; ?>
					<?php if( $upgrade_notice ): ?>
					<tr>
                    	<th>
                    		<label><?php _e('Upgrade Notice', 'fieldmaster'); ?></label>
                    	</th>
						<td>
							<?php echo $upgrade_notice; ?>
						</td>
					</tr>
					<?php endif; ?>
				</tbody>
			</table>
			</form>

		</div>


	</div>

</div>
<style type="text/css">
	#fieldmaster_pro_licence {
		width: 75%;
	}

	#fieldmaster-update-information td h4 {
		display: none;
	}
</style>
