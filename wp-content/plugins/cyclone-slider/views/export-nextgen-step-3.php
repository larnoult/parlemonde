<?php if(!defined('ABSPATH')) die('Direct access denied.'); ?>

<div class="wrap">
    <div id="icon-tools" class="icon32"><br></div>
    
	<?php $this->render('export-import-tabs.php', array('tabs'=>$tabs)); ?>
	
    <h2><?php _e('Cyclone Slider Nextgen Exporter', 'cycloneslider'); ?></h2>
	
	<?php $this->render('error-message.php', array('error'=>$error)); ?>
	
	<?php $this->render('ok-message.php', array('ok'=>$ok)); ?>
	
	<?php if( empty($error) ): ?>
		<ul>
			<?php foreach($log_results['oks'] as $ok): ?>
				<li><?php echo esc_attr($ok); ?></li>
			<?php endforeach; ?>
		</ul>
		<br /><br />
		<a class="button" href="<?php echo esc_url($export_page_url); ?>"><?php _e('Back', 'cycloneslider'); ?></a>
		<a class="button button-primary" href="<?php echo esc_url($zip_url); ?>"><?php _e('Download', 'cycloneslider'); ?></a>
	<?php else: ?>
		
		<br /><br />
		<a class="button" href="<?php echo esc_url($export_page_url); ?>"><?php _e('Back', 'cycloneslider'); ?></a>
	<?php endif; ?>
</div>