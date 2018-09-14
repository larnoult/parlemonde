<?php if(!defined('ABSPATH')) die('Direct access denied.'); ?>

<div class="wrap">
    <div id="icon-tools" class="icon32"><br></div>
    <h2 class="nav-tab-wrapper">
        <a class="nav-tab" href="<?php echo $export_page_url; ?>"><?php _e('Export', 'cycloneslider'); ?></a>
        <a class="nav-tab nav-tab-active" href="<?php echo $import_page_url; ?>"><?php _e('Import', 'cycloneslider'); ?></a>
    </h2>
	<h2><?php _e('Cyclone Slider Import', 'cycloneslider'); ?></h2>
	<?php if( empty($log_results['errors']) ): ?>
		<ul>
			<?php foreach($log_results['oks'] as $ok): ?>
				<li><?php echo $ok; ?></li>
			<?php endforeach; ?>
		</ul>
		<p><?php _e('Import operation success!', 'cycloneslider'); ?></p>
		<br /><br />
		<a class="button" href="<?php echo $import_page_url; ?>"><?php _e('Back', 'cycloneslider'); ?></a>
	<?php else: ?>
		<ul>
			<?php foreach($log_results['errors'] as $error): ?>
				<li><?php echo $error; ?></li>
			<?php endforeach; ?>
		</ul>
		<br /><br />
		<a class="button" href="<?php echo $import_page_url; ?>"><?php _e('Back', 'cycloneslider'); ?></a>
	<?php endif; ?>
</div>