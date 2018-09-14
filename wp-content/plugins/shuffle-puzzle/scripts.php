<?php
if (isset($_GET['page']) && $_GET['page'] == 'editpuzzle') {
	add_action('admin_print_scripts', 'msp_upload_admin_scripts');
	add_action('admin_print_styles', 'msp_upload_admin_styles');
}
add_action('admin_init', 'sp_admin_scripts');
add_action('wp_enqueue_scripts', 'sp_front_scripts');
function sp_admin_scripts(){
	if(is_admin()){
		if(isset($_REQUEST['page']) && ($_REQUEST['page']=="shufflepuzzle" || $_REQUEST['page']=="editpuzzle")){
			if(get_option('sp_load_jquery')=='no'){
				wp_register_script('sp_jquery',plugins_url('inc/admin/js/jquery-1.8.3.min.js', __FILE__));
				wp_enqueue_script('sp_jquery');				
				wp_enqueue_script('sp_jquery_sort', plugins_url('inc/admin/js/jquery-ui-1.10.3.custom.min.js',__FILE__),array('sp_jquery'));
				wp_enqueue_script('sp_farbtastic', plugins_url('inc/admin/js/farbtastic.js',__FILE__),array('sp_jquery'));
				wp_enqueue_style('sp_farbtastic', plugins_url('inc/admin/css/farbtastic.css',__FILE__),false, '1.0.0');
				wp_register_script('sp_admin-js',plugins_url('inc/admin/js/sp_admin.js',__FILE__),array('sp_jquery'));		
			}else{
				wp_enqueue_script('jquery');
				wp_enqueue_script('jquery_sort', plugins_url('inc/admin/js/jquery-ui-1.10.3.custom.min.js',__FILE__),array('jquery'));
				wp_enqueue_script('farbtastic');
				wp_enqueue_style('farbtastic');
				wp_register_script('sp_admin-js',plugins_url('inc/admin/js/sp_admin.js',__FILE__),array('jquery'));
			}

            if(get_option('sp_load_highlighter') && get_option('sp_load_highlighter') == 'yes'){
                wp_enqueue_script('sp_highlighter', plugins_url('inc/admin/js/shCore.js',__FILE__),array('jquery'));
                wp_enqueue_script('sp_highlighter1', plugins_url('inc/admin/js/shBrushJScript.js',__FILE__),array('jquery'));
                wp_enqueue_script('sp_highlighter2', plugins_url('inc/admin/js/shBrushCss.js',__FILE__),false, '1.0.0');
                wp_enqueue_style('sp_highlighter', plugins_url('inc/admin/css/shCoreEmacs.css',__FILE__),false, '1.0.0');
            }


			wp_enqueue_script('sp_admin-js');
			wp_register_style('sp_admin-css',plugins_url('inc/admin/css/sp_admin.css',__FILE__),false, '2.2');
			wp_enqueue_style('sp_admin-css');
		}
	}
}
function sp_front_scripts() {	
	if(!is_admin()){
		if(get_option('sp_load_jquery')=='no'){
			wp_deregister_script('jquery');
			wp_enqueue_script('jquery',plugins_url('inc/admin/js/jquery-1.8.3.min.js', __FILE__));
        }else{
			wp_enqueue_script('jquery');
		}
		wp_register_style('sp_front-css',plugins_url('inc/front/css/style.css',__FILE__), false, '2.2');
		wp_enqueue_style('sp_front-css');
        wp_register_script('sp_shufflepuzzle-js',plugins_url('inc/front/js/jquery.shufflepuzzle.pack.js',__FILE__), array('jquery'), false, '2.2');
        wp_enqueue_script('sp_shufflepuzzle-js');
	}
}
function msp_upload_admin_scripts() {
	wp_enqueue_script('media-upload');
	wp_enqueue_script('thickbox');
}
function msp_upload_admin_styles() {
	wp_enqueue_style('thickbox');
}
?>