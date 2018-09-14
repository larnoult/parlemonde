<?php 
add_action('wp_ajax_ksp_preview_slider', 'ksp_preview_slider_callback');
function ksp_preview_slider_callback() {

	echo '<!DOCTYPE html><html><head>';
	 wp_register_style('kadence_slider_css',  KADENCE_SLIDER_URL . 'css/ksp.css', false, '206');
    wp_print_styles('kadence_slider_css');

	wp_print_scripts('jquery');
	wp_register_script('kadence_slider_js', KADENCE_SLIDER_URL . 'js/min/ksp-min.js', false, 206, true);
   	wp_print_scripts('kadence_slider_js');

	$fonts = ksp_font_list();
	$gfonts = '';
	foreach ($fonts as $font) {
		if($font['info']['data-google'] == 'true'){
			$gfonts .= '"'.$font['info']['name'].':'.$font['info']['data-weight'].'",';
		}
	}
	echo '<script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.16/webfont.js"></script>
			<script>
			  WebFont.load({
			    google: {
			      families: ['.$gfonts.']
			    }
			  });
			</script>';
	echo '</head><body style="margin: 0;">';
	echo '<div class="slider_preview" style="margin: 0 auto;">';
	
	if(isset($_GET['slider_id']) ) {
		$id = $_GET['slider_id'];
	} elseif (isset($_POST['slider_id']) ){
		$id = $_POST['slider_id'];
	} else {
		$id = null;
	}
	echo do_shortcode('[kadence_slider_pro id="'.$id.'"]');

	echo '</div>';
	echo '</body></html>';
	die();
}

?>
