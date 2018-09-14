<?php
//info prevent file from being accessed directly
if (basename($_SERVER['SCRIPT_FILENAME']) == 'tinymce-plugin.php') { die ("Please do not access this file directly. Thanks!<br/><a href='https://www.mapsmarker.com/go'>www.mapsmarker.com</a>"); }
add_action('admin_print_styles-post.php', 'mm_shortcode_button');
add_action('admin_print_styles-post-new.php', 'mm_shortcode_button');

/*
Initialize
*/
function mm_shortcode_button() {
	if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') ) {
		return;
	}
	if ( get_user_option('rich_editing') == 'true' ) {
		add_filter( 'mce_external_plugins', 'lmm_button_visual' );
		add_filter( 'mce_external_plugins', 'lmm_button_text' );
	} else {
		add_action('admin_footer', 'lmm_button_text');
	}
}
/*
Map button for text tab
*/
function lmm_button_text($plugin_array) {
	if (!is_multisite()) { $adminurl = admin_url(); } else { $adminurl = get_admin_url(); }
	$text_add = __('Add Map','lmm');
	$link = LEAFLET_PLUGIN_URL . 'inc/js/tinymce_button_text.js';
	$tinymce_options = array(
		'leafletPluginUrl' => LEAFLET_PLUGIN_URL,
		'adminUrl' => $adminurl,
		'textAdd' => $text_add
	);
	wp_register_script('html-dialog', $link);
	wp_localize_script('html-dialog', 'tinymceOptions', $tinymce_options);
	wp_enqueue_script('html-dialog');
	return $plugin_array;
}
/*
Map button for visual tab
*/
function lmm_button_visual( $plugin_array ) {
	if (!is_multisite()) { $adminurl = admin_url(); } else { $adminurl = get_admin_url(); }
	$text_add = __('Add Map','lmm');
	$plugin_array['mm_shortcode'] = LEAFLET_PLUGIN_URL . 'inc/js/tinymce_button_visual.js';
	return $plugin_array;
}
add_action('wp_ajax_get_mm_list',  'get_mm_list');

function get_mm_list(){
	$get_map_search_nonce = isset($_GET['map_search_nonce']) ? $_GET['map_search_nonce'] : '';
	if (isset($_GET['q'])) {
		if (!wp_verify_nonce($get_map_search_nonce, 'map-search-nonce')) { die(__('Security check failed - please call this function from the according admin page!','lmm').''); };
	}
	global $wpdb, $wp_version;
	$table_name_markers = $wpdb->prefix.'leafletmapsmarker_markers';
	$table_name_layers = $wpdb->prefix.'leafletmapsmarker_layers';

	$l_condition = isset($_GET['q']) ? "AND l.name LIKE '%" . esc_sql($_GET['q']) . "%'" : '';
	$m_condition = isset($_GET['q']) ? "AND m.markername LIKE '%" . esc_sql($_GET['q']) . "%'" : '';

	$marklist = $wpdb->get_results("
		(SELECT l.id, 'icon-layer.png' as 'icon', l.name as 'name', l.updatedon, l.updatedby, 'layer' as 'type' FROM `$table_name_layers` as l WHERE l.id != '0' $l_condition)
		UNION
		(SELECT m.id, m.icon as 'icon', m.markername as 'name', m.updatedon, m.updatedby, 'marker' as 'type' FROM `$table_name_markers` as m WHERE  m.id != '0' $m_condition)
		order by updatedon DESC LIMIT 100", ARRAY_A);
	if (isset($_GET['q'])) {
		buildMarkersList($marklist);
		exit();
	}
	?>
	<!DOCTYPE html>
	<html>
	<head>
		<title><?php _e('Add Map','lmm') ?></title>
		<?php echo '<script type="text/javascript" src="' . site_url() . '/wp-includes/js/jquery/jquery.js"></script>'.PHP_EOL; ?>
		<?php if(!isset($_GET['mode'])): ?>
			<script type='text/javascript' src='<?php echo LEAFLET_PLUGIN_URL . 'inc/js/tinymce_button_visual.php' ?>'></script>
		<?php endif;?>
		<script type='text/javascript' src='<?php echo LEAFLET_PLUGIN_URL . 'inc/js/jquery_caret.js' ?>'></script>
		<link rel='stylesheet' href='<?php echo LEAFLET_PLUGIN_URL . 'inc/css/marker_select_box.css' ?>' type='text/css' media='all' />
	</head>
	<style>
	.list_item.active {
		background: #ebebeb ;
	}
	<?php if(!isset($_GET['mode'])): ?>
	body {
		background: #efefef;
	}
	<?php endif; ?>
	</style>
	<body>
	<table style="width:100%;margin-bottom:5px;" cellspacing="0"><tr>
	<tr>
	<td style="width:42%;"><div id="msb_searchContainer" title="<?php echo sprintf(esc_attr__('If no search term is entered, the latest %1$s updated maps will be shown.','lmm'), 50); ?>"><input type="text" name="q" id="msb_search" placeholder="<?php _e('Search','lmm'); ?>"/></div></td>
	<?php 
		global $current_user;
		$lmm_options = get_option( 'leafletmapsmarker_options' );
		if (current_user_can( $lmm_options[ 'capabilities_edit' ])) {
			echo '<td><a class="newmap" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker" target="_blank" title="' . esc_attr__('create a new marker map','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL  . '/inc/img/icon-menu-add.png"> ' . __('new marker map', 'lmm') . '</a></td>';
			echo '<td><a class="newmap" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_layer" target="_blank" title="' . esc_attr__('create a new layer map','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL  . '/inc/img/icon-menu-add.png"> ' . __('new layer map', 'lmm') . '</a></td>';
		}
	?>
	</tr>
	</table>
	<div id="msb_listContainer">
		<?php
		if ($marklist != NULL) {
			echo '<div id="msb_listHint">' . __('Please select the map you would like to add','lmm') . ':</div>';
			buildMarkersList($marklist);
		} else {
			echo '<div id="no_map_yet">' . __('No map has been created yet.','lmm') . '<br/>';
			if (current_user_can( $lmm_options[ 'capabilities_edit' ])) {
				echo sprintf(__('Please <a href="%s" target="_blank">click here</a> to add your first one!','lmm'), LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker');
			}
			echo '</div>';
		} ?>
	</div>
	<table style="width:100%;"><tr>
	<td style="padding-top:12px;"><a href="#" id="msb_cancel"><?php _e('Cancel','lmm'); ?></a></td>
	<td>
		<span id="submit-enabled" style="display:none;"><input type="button" href="#" id="msb_insertMarkerSC" value="<?php esc_attr_e('Insert map','lmm'); ?>" /></span>
		<span id="submit-disabled"><input class="disabled" type="button" href="#" id="msb_insertMarkerSC_disabled" value="<?php esc_attr_e('Insert map','lmm'); ?>" disabled="disabled" title="<?php esc_attr_e('Please select a map first!','lmm'); ?>" /></span>
		</td></tr>
	</table>
	<script type="text/javascript">
	(function($){
		var selectMarkerBox = {
			markerID : '',
			mapsmarkerType : '',

			init : function(){
				var self = selectMarkerBox;
				$('#msb_insertMarkerSC').on('click', function(e){
					e.preventDefault();
					self.insert();
					parent.tb_remove();

				})
				$('#msb_cancel').on('click', function(e){
					e.preventDefault();
					parent.tb_remove();
				})
				$(document).on('click touchstart', '.list_item', function(e){
					e.preventDefault();
					var id = $(this).find('input[name="msb_id"]').val();
					var type = $(this).find('input[name="msb_type"]').val();
					$('.list_item.active').removeClass('active');
					$(this).addClass('active');
					$('#submit-disabled').hide();
					$('#submit-enabled').show();
					self.setMarkerID(id)
					self.setMarkerType(type);
				})
				$(document).on('click touchstart', '.preview', function(e){
					e.preventDefault();
					var url = $(this).attr('href');
					var map_type = $(this).attr('alt');
					window.open(url, '_blank');
				})
				$('#msb_search').on('keyup', function(){
					<?php $noncelink_map_search = wp_create_nonce('map-search-nonce'); ?>
					$.post('<?php if (!is_multisite()) { echo admin_url(); } else { echo get_admin_url(); } ?>admin-ajax.php?action=get_mm_list&map_search_nonce=<?php echo $noncelink_map_search; ?>&q='+$(this).val(), function(data){
							$('.list_item').remove();
							$('#msb_listContainer').append(data);
					})
				})
			},
			setMarkerID : function(id) {
				selectMarkerBox.markerID = id;
			},
			setMarkerType : function(type) {
				switch (type)
				{
					case 'layer':
						selectMarkerBox.mapsmarkerType = 'layer';
						break;
					case 'marker':
						selectMarkerBox.mapsmarkerType = 'marker';
						break;
				}
			},
			getShortCode : function(){
			  return '[mapsmarker '+ selectMarkerBox.mapsmarkerType +'="'+ selectMarkerBox.markerID +'"]';
			},
			insert : function() {
				<?php
					if ( get_user_option("rich_editing") == "true" ) {
						echo "$('#content', parent.document.body).insertAtCaret(selectMarkerBox.getShortCode());";
						if ( version_compare( $wp_version, '3.9-alpha', '>=' ) ) { 
							echo "window.parent.tinyMCE.get('content').insertContent(selectMarkerBox.getShortCode());";
						} else {
							echo "window.parent.tinyMCE.activeEditor.execCommand('mceInsertContent', false, selectMarkerBox.getShortCode());";
						}
					} else {
						echo "$('#content', parent.document.body).insertAtCaret(selectMarkerBox.getShortCode());";
					} 
				?>
			},
			insertMarker : function() {
				return;
			},
			insertList : function() {
				return;
			},
			close : function() {
				parent.tb_remove();
			}
		}
		selectMarkerBox.init();
	})(jQuery)
	</script>
	</body>
	</html>
	<?php
	exit;
}
function buildMarkersList($array){
	foreach($array as $one):
		$date_prepare = strtotime($one['updatedon']);
		$date = date("Y/m/d", $date_prepare);
		if ($one['name'] == NULL) {
			$name = '(ID '. $one['id'].')';
			$name_title = '(ID '. $one['id'].')';
		} else {
			$name = '<strong>' . stripslashes(htmlspecialchars($one['name'])) . '</strong> (ID '. $one['id'].')';
			$name_title = stripslashes(htmlspecialchars($one['name'])) . ' (ID '. $one['id'].')';
		}

		if ($one['type'] == 'marker') {
			$title = __('Marker','lmm'). ' ID ' . $one['id'];
			if ($one['icon'] == NULL) {
				$list_entry = '<img src="' . LEAFLET_PLUGIN_URL  . '/leaflet-dist/images/marker.png" title="' . $title . '" />';
			} else {
				$list_entry = '<img src="' . LEAFLET_PLUGIN_ICONS_URL . '/' . $one['icon'] . '" title="' . $title . '"/>';
			}
		} else {
			$title = __('Layer','lmm'). ' ID ' . $one['id'];
			$list_entry = '<img class="layer" src="' . LEAFLET_PLUGIN_URL  . '/inc/img/icon-layer.png" title="' . $title . '" />';
		}
	?>
    <div class="list_item">
	<table style="width:100%;">
	<tr>
		<td style="width:30px;">
			<span class="name" title="<?php esc_attr_e('map type and ID','lmm');?>"><?php echo $list_entry; ?></span>
		</td>
		<td valign="top" title="<?php echo $name_title; ?>">
        		<span class="name"><?php echo $name; ?></span>
		</td>
		<td valign="top" title="<?php esc_attr_e('updated on','lmm');?> <?php echo $date; ?> <?php _e('by','lmm'); ?> <?php echo $one['updatedby']?>">
			<span class="date"><?php echo $date; ?></span>
		</td>
			<td valign="top" style="padding:0 5px;width:10px;">
			<a class="preview" alt="<?php echo $one['type']; ?>" href="<?php echo LEAFLET_WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_<?php echo $one['type']; ?>&id=<?php echo $one['id']; ?>" title="<?php esc_attr_e('open map in new window','lmm'); ?>" target="_blank"><img src="<?php echo LEAFLET_PLUGIN_URL; ?>/inc/img/icon-menu-external.png"/></a>	
        		<input type="hidden" value="<?php echo $one['type']?>" name="msb_type">
		        <input type="hidden" value="<?php echo $one['id']?>" name="msb_id">
		</td>
	</tr>
	</table>
    </div>
    <?php endforeach; 
}