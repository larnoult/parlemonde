<?php if(!defined('ABSPATH')) die('Direct access denied.'); ?>
<h2 class="nav-tab-wrapper">
	<?php foreach($tabs as $tab): ?>
	<a class="<?php echo esc_attr( $tab['classes'] ); ?>" href="<?php echo esc_url( $tab['url'] ); ?>"><?php echo esc_attr( $tab['title'] ); ?></a>
	<?php endforeach; ?>
</h2>