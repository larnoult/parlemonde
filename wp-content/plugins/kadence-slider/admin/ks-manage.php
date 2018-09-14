<?php 
class KadenceSliderAdmin {
	
	// Creates the menu and the admin panel

	public static function ksp_init_admin() {
		add_action('admin_menu', 'KadenceSliderAdmin::ksp_add_menu_item');
	}
	
	public static function ksp_add_menu_item() {
		global $ksp_manage_page;
		$ksp_manage_page = add_menu_page('Kadence Slider', 'Kadence Slider', 'edit_pages', 'kadenceslider', 'KadenceSliderAdmin::ksp_display_page', 'dashicons-images-alt2');
	}
	
	// Go to the correct page
	public static function ksp_display_page() {
		if(!isset($_GET['view'])) {
			$index = 'base';
		} else {
			$index = $_GET['view'];
		}
		
		
		
		?>
		<div class="wrap" style="height:0;overflow:hidden;"><h2></h2></div>
		
		<div class="wrap ksp-admin-page ksp-admin">	
		<div id="kt_ajax_overlay">
			<div class="ajaxnotice-kt"><span class="kt-notice-saving"><?php echo __( 'Saving', 'kadence-importer' ); ?></span>
			<div class="bubblingG">
			    <span id="bubblingG_1">
			    </span>
			    <span id="bubblingG_2">
			    </span>
			    <span id="bubblingG_3">
			    </span>
			</div>
			</div>
			</div>
				<div class="ksp-logo">
					<img src="<?php echo esc_url(KADENCE_SLIDER_URL .'admin/css/kproslider.png');?>" width="300" height="83" style="padding-top:4px;">
					<?php if('base' != $index) { ?>
						<a class="ksp_back_overview" href="<?php echo esc_url(admin_url('?page=kadenceslider'));?>"><?php _e('Back to sliders overview', 'kadence-slider'); ?></a>
					<?php } ?>
				</div>

			<noscript class="ksp-no-js">
				<?php _e('JavaScript must be enabled to view this page correctly.', 'kadence-slider'); ?>
			</noscript>
			
			<div class="ksp-message ksp-message-ok" style="display: none;"><?php _e('Operation completed successfully.', 'kadence-slider'); ?></div>
			<div class="ksp-message ksp-message-error" style="display: none;"><?php _e('Something went wrong.', 'kadence-slider'); ?></div>
			
			<?php
			
			switch($index) {
				case 'base':
					self::ksp_display_base();
				break;
				
				case 'layeradd':
				case 'layeredit':
					self::ksp_display_layerslider();
				break;
			}
			
			?>
		
		</div>
		<?php
	}
	
	// Display the slider base page
	public static function ksp_display_base() {		
		?>
		<div class="ksp-base">
			<?php require_once KADENCE_SLIDER_PATH . 'admin/base.php'; ?>
		</div>
		<?php
	}
	

	// Displays the slider page in wich you can add or modify sliders, slides and elements
	public static function ksp_display_layerslider() {
		global $wpdb;
		
		// Check what the user is doing: is it adding or modifying a slider? 
		if($_GET['view'] == 'layeradd') {
			$edit = false;
			$id = NULL;	//This variable will be used in other files. It contains the ID of the SLIDER that the user is editing
		} else {
			$edit = true;
			$id = isset($_GET['id']) ? $_GET['id'] : NULL;
			$slider = $wpdb->get_row('SELECT * FROM ' . $wpdb->prefix . 'ksp_sliders WHERE id = ' . $id);
			$slides = $wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . 'ksp_slides WHERE slider_parent = ' . $id . ' ORDER BY position');
			// The elements variable are updated in the foreachh() loop directly in the "slides.php" file
		}
		?>
		
		<div class="ksp-slider <?php echo $edit ? 'ksp-edit-slider' : 'ksp-add-slider' ?>">
				<?php if($edit): ?>
					<div class="ksp-tabs ksp-clearfix">
							<ul class="ksp-clearfix">
							<li class="ksp-current">
								<a class="nav-tab ksp-slider-settings" href="#ksp-slider-settings"><?php _e('Slider Settings', 'kadence-slider'); ?></a>
							</li>
							<li>
								<a class="nav-tab ksp-slide-settings" href="#ksp-slide-settings"><?php _e('Edit Slides', 'kadence-slider'); ?></a>
							</li>
							</ul>
					</div>
									
				<?php endif; ?>
				
				<?php require_once KADENCE_SLIDER_PATH . 'admin/layerslider.php'; ?>
				<?php 
				if($edit) {
					require_once KADENCE_SLIDER_PATH . 'admin/layers.php';
					require_once KADENCE_SLIDER_PATH . 'admin/layerslides.php';
				}
				?>
			
			<br />
			
			<a class="ksp-button ksp-is-primary ksp-save-settings" data-id="<?php echo $id; ?>" href="#"><?php _e('Save Settings', 'kadence-slider'); ?></a>
			
		</div>
						<script type="text/javascript">	
jQuery(document).ready(function ($) {
	if(window.location.hash) {
		var urlhash = window.location.hash.replace(/^#!/, '');
		$('.'+urlhash).parent().addClass("ksp-current");
        $('.'+urlhash).parent().siblings().removeClass("ksp-current");
		$(".ksp-tab-content").not('#'+urlhash).css("display", "none");
        $('#'+urlhash).fadeIn(0);
	}
    $(".ksp-tabs a").click(function(event) {
        event.preventDefault();
        $(this).parent().addClass("ksp-current");
        $(this).parent().siblings().removeClass("ksp-current");
        var tab = $(this).attr("href");
        var tabhash = $(this).attr("href").replace('#', '');
        window.location.hash = '#!' + tabhash;
        $(".ksp-tab-content").not(tab).css("display", "none");
        $(tab).fadeIn();
    });
    var width = parseInt($('#ksp-slider-settings .ksp-settings-table #ksp-slider-maxWidth').val());
    var height = parseInt($('#ksp-slider-settings .ksp-settings-table #ksp-slider-maxHeight').val());
            
            $('.ksp-admin #ksp-slides .ksp-slide .ksp-slide-editing-area').css({
                'width' : width,
                'height' : height,
            });
            
            $('.ksp-admin').css({
                'width' : width + 40,
            });
});
</script>

		<?php
	}
	

	// Include CSS and JavaScript
	public static function ksp_enqueues($hook) {
		global $ksp_manage_page;
		if( $hook != $ksp_manage_page ) 
    	return;

		wp_enqueue_script('jquery-ui-draggable');
		wp_enqueue_script('jquery-ui-tabs');
		wp_enqueue_script('jquery-ui-sortable');
		wp_enqueue_style('wp-color-picker');
		wp_enqueue_media();
		wp_register_script('ksp-mag_pop', KADENCE_SLIDER_URL . 'admin/js/ksp_mag_pop.js',array(), KS_VERSION, true);
		wp_enqueue_script('ksp-mag_pop');
		wp_register_script('kadencesliderpro-admin', KADENCE_SLIDER_URL . 'admin/js/ksp_admin.js', array('wp-color-picker'), KS_VERSION, true);
		self::ksp_localization();
		wp_enqueue_style('ksp-mag_pop_css', KADENCE_SLIDER_URL . 'admin/css/ksp_mag_pop.css', array(), KS_VERSION);
		wp_enqueue_style('kadencesliderpro-admin', KADENCE_SLIDER_URL . 'admin/css/ksp_admin.css', array(), KS_VERSION);
		wp_enqueue_script('kadencesliderpro-admin');
		
	}
	public static function admin_google_fonts() {
		global $ksp_manage_page;
		$data = get_option('kadence_slider');
    	$screen = get_current_screen();
    	
		if (!isset($screen) || $screen->id != $ksp_manage_page ) {
			return;
		}
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

		echo '<style type="text/css" id="ksp-admin-css">.ksp-slide-editing-area .ksp-layer {';
		if(isset($data['font_option_one']['font-family'])) { echo 'font-family:'. $data["font_option_one"]["font-family"].';';}
		if(isset($data['font_option_one']['font-weight'])) { echo 'font-weight:'. $data["font_option_one"]["font-weight"].';';}
		if(isset($data['font_option_one']['font-style'])) { echo 'font-style:'. $data["font_option_one"]["font-style"].';';}
		echo '}'; 
		echo '</style>';
		
	}
	public static function ksp_hook_admin_scripts() {
		add_action('admin_enqueue_scripts', 'KadenceSliderAdmin::ksp_enqueues');
		add_action('admin_head', 'KadenceSliderAdmin::admin_google_fonts');
	}

	public static function ksp_localization() {
		// Here the translations for the admin.js file
		$kadencesliderpro_translations = array(
			'slide' => __('Slide', 'kadence-slider'),
			'slide_delete_confirm' => __('The slide will be deleted. Are you sure?', 'kadence-slider'),
			'slide_delete_just_one' => __('You can\'t delete this. You must have at least one slide.', 'kadence-slider'),
			'slider_delete_confirm' => __('The slider will be deleted. Are you sure?', 'kadence-slider'),
			'text_layer_default_html' => __('Text layer', 'kadence-slider'),
			'button_layer_default_html' => __('Button layer', 'kadence-slider'),
			'slide_live_preview' => __('Live preview', 'kadence-slider'),
			'slide_stop_preview' => __('Stop preview', 'kadence-slider'),
		);
		wp_localize_script('kadencesliderpro-admin', 'ksp_translations', $kadencesliderpro_translations);
	}

}

?>