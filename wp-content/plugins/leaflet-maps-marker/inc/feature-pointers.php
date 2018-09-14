<?php
//info prevent file from being accessed directly
if (basename($_SERVER['SCRIPT_FILENAME']) == 'feature-pointers.php') { die ("Please do not access this file directly. Thanks!<br/><a href='https://www.mapsmarker.com/go'>www.mapsmarker.com</a>"); }
$dismissed_pointers = explode( ',', (string) get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ) );
$dismissed_pointers = array_flip($dismissed_pointers);
$page = (isset($_GET['page']) ? $_GET['page'] : '');
?>
<script type="text/javascript">// <![CDATA[
jQuery(document).ready(function($) {
	if(typeof(jQuery().pointer) != 'undefined') {

	<?php if ( !isset($dismissed_pointers["lmmesw"]) && ($page == 'leafletmapsmarker_marker' || $page == 'leafletmapsmarker_layer') ) { ?>
		$('#editmodeswitch').pointer({
			content: '<h3><?php esc_attr_e('You are using the simplified editor','lmm'); ?></h3><p><?php esc_attr_e('If you want to be able to set all available options, please switch to the advanced editor.','lmm'); ?></p>',
			position: {
				edge: 'right',
				align: 'center'
			},
			close: function() {
				$.post( ajaxurl, {
					pointer: 'lmmesw',
					action: 'dismiss-wp-pointer'
				});
			}
		}).pointer('open');
	<?php } ?>

	}
});
// ]]></script>