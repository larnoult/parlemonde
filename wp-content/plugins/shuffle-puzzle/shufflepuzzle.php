<?php
/*
 * Plugin Name: Shuffle Puzzle
 * Plugin URI: http://codecanyon.net/item/wp-shuffle-puzzle/5738331
 * Description: Shuffle Puzzle is a nice game for developing logical skills
 * Version: 2.2
 * Author: Anatol
 * Author URI: http://codecanyon.net/user/anatolik/portfolio
 */
//----------------------------------------------------------------------------------
/*---Load Required Files------------------------------
----------------------------------------------------*/
include_once('scripts.php');
include_once('inc/admin/sp_admin.php');
include_once('inc/admin/example/puzzle_main_settings.php');
include_once('inc/admin/example/puzzle_with_message_box.php');
include_once('inc/admin/example/puzzle_with_timer.php');
include_once('inc/admin/example/puzzle_with_counter.php');
//---------------------------------------------------


add_action( 'admin_menu', 'shufflepuzzle_plugin_admin_menu', 9553);
add_action('admin_init', 'sp_options_fields');

function shufflepuzzle_plugin_admin_menu() {
	add_menu_page('Add Shuffle Puzzle', 'Shuffle Puzzle', 'publish_posts', 'shufflepuzzle', 'add_shufflepuzzle', plugins_url('inc/images/icon.png',__FILE__));
	add_submenu_page('shufflepuzzle', 'Edit Shuffle Puzzle', '', 'publish_posts', 'editpuzzle','edit_shufflepuzzle');
	add_options_page( 'Shuffle Puzzle Options', 'Shuffle Puzzle', 'manage_options', 'sp-options-page.php', 'sp_options_page' );
}


function sp_options_page() {
	echo '<div class="wrap">';
	echo '<h2>Shuffle Puzzle Options</h2>';
	echo '<form method="post" action="options.php">';
	do_settings_sections(__FILE__);
	settings_fields('sp_fields'); 
	submit_button();
	echo '</form>';
	echo '</div>';
}

function sp_options_fields() {
	register_setting('sp_fields', 'sp_load_jquery');
	register_setting('sp_fields', 'sp_load_highlighter');
	add_settings_section('sp_section_single', '', '', __FILE__);
    
	add_settings_field('sp_setting-text-id1', 'Load jQuery from:', 'sp_jQuery_callback', __FILE__, 'sp_section_single');
	add_settings_field('sp_setting-text-id2', 'Use Highlighter:', 'sp_highlighter_callback', __FILE__, 'sp_section_single');
}

function sp_jQuery_callback() {
	$checked1 = '';
	$checked2 = '';
	if(!get_option('sp_load_jquery') || get_option('sp_load_jquery') == 'yes'){
		$checked1 = 'checked="checked"';
	}else{
		$checked2 = 'checked="checked"';
	}
	
	?>
	<input type="radio" name="sp_load_jquery" value="yes" class="radio" <?php echo $checked1; ?>/> <small>Wordpress</small><br>
	<input type="radio" name="sp_load_jquery" value="no" class="radio" <?php echo $checked2; ?>/> <small>Shuffle Puzzle Plugin</small><br>
	<?php
}

function sp_highlighter_callback() {
    $checked = '';
    if(get_option('sp_load_highlighter') && get_option('sp_load_highlighter') == 'yes'){
        $checked = ' checked="checked" ';
    }
    echo "<input ".$checked." id='plugin_chk1' value='yes' name='sp_load_highlighter' type='checkbox' />";
}

function get_images(){
	return array(
		'image_1' => plugins_url('inc/images/puzzle.jpg',__FILE__),
		'image_2' => plugins_url('inc/images/puzzle1.jpg',__FILE__),
		'image_3' => plugins_url('inc/images/puzzle2.jpg',__FILE__),
		'image_4' => plugins_url('inc/images/puzzle3.jpg',__FILE__),
		'image_5' => plugins_url('inc/images/puzzle4.jpg',__FILE__),
		'image_6' => plugins_url('inc/images/puzzle5.jpg',__FILE__)
	);
}

function msp_main(){
	return array_merge(get_images(), puzzle_with_default_settings());
}
function msp_with_message_box(){
	return array_merge(get_images(), puzzle_with_message_box());
}
function msp_with_timer(){
	return array_merge(get_images(), puzzle_with_timer());
}
function msp_with_counter(){
	return array_merge(get_images(), puzzle_with_counter());
}

function sp_install(){
	global $wpdb;
	$table_name = $wpdb->prefix . "shufflepuzzle";

	$query = "CREATE TABLE " . $table_name . " (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			option_name VARCHAR(255) NOT NULL DEFAULT  'sp_main',
			active tinyint(1) NOT NULL DEFAULT  '0',
			UNIQUE KEY id (id)
	  );";

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($query);
}

// Runs when plugin is activated and creates new database field
register_activation_hook(__FILE__,'shufflepuzzle_plugin_install');
function shufflepuzzle_plugin_install() {
	global $wpdb;
    //add_option('sp_main', msp_main());
    add_option('sp_main', str_replace('%sp_name%', 'sp_main', msp_main()));
	//add_option('sp_message_box', msp_with_message_box());
    add_option('sp_message_box', str_replace('%sp_name%', 'sp_message_box', msp_with_message_box()));
	//add_option('sp_timer', msp_with_timer());
    add_option('sp_timer', str_replace('%sp_name%', 'sp_timer', msp_with_timer()));
	//add_option('sp_counter', msp_with_counter());
    add_option('sp_counter', str_replace('%sp_name%', 'sp_counter', msp_with_counter()));

	$msp_version = get_option('msp_version');
	if(!$msp_version){
		add_option('msp_version', msp_get_version());
	}

	sp_install();

	$table_name = $wpdb->prefix . "shufflepuzzle";

	$sql = "INSERT IGNORE INTO $table_name VALUES ('1','sp_main','1'), ('2','sp_message_box','1'), ('3','sp_timer','1'), ('4','sp_counter','1');";
	$wpdb->query( $sql );
}

register_uninstall_hook(__FILE__, 'shufflepuzzle_plugin_deinstall');
function shufflepuzzle_plugin_deinstall() {
    global $wpdb;
	
	$table = $wpdb->prefix . "shufflepuzzle";
	
	$msp_data = $wpdb->get_results("SELECT * FROM $table ORDER BY id");
	foreach ($msp_data as $data) {
		delete_option($data->option_name);
	}
	$wpdb->query("DROP TABLE IF EXISTS $table");
}

function msp_get_version(){
	if ( ! function_exists( 'get_plugins' ) ){
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	}
	$plugin_folder = get_plugins( '/' . plugin_basename( dirname( __FILE__ ) ) );
	$plugin_file = basename( ( __FILE__ ) );
	return $plugin_folder[$plugin_file]['Version'];
}
include_once('inc/front/sp_front.php');

//----------------------------------------------------------------------------------