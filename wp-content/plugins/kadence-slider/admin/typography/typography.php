<?php 
/**
* 
*/
add_action( "after_setup_theme", 'ksp_run_redux', 30);
function ksp_run_redux() {
   if ( class_exists( 'Redux' ) ) {
      return;
    }
    require_once( KADENCE_SLIDER_PATH . '/redux/framework.php');
}
add_action( "after_setup_theme", 'ksp_add_sections', 35);
function ksp_add_sections() {
    if ( ! class_exists( 'Redux' ) ) {
      return;
    }

    $opt_name = "kadence_slider";

    $theme = wp_get_theme();
    $args = array(
        'opt_name'             => $opt_name,
        'display_name'         => 'Kadence Slider Fonts',
        'display_version'      => '',
        'menu_type'            => 'submenu',
        'page_parent'          => 'kadenceslider',
        'allow_sub_menu'       => true,
        'menu_title'           => __('Kadence Slider Fonts', 'kadence-slider'),
        'page_title'           => __('Kadence Slider Fonts', 'kadence-slider'),
        'google_api_key'       => 'AIzaSyALkgUvb8LFAmrsczX56ZGJx-PPPpwMid0',
        'google_update_weekly' => false,
        'async_typography'     => false,
        'admin_bar'            => false,
        'dev_mode'             => false,
        'use_cdn'              => false,
        'update_notice'        => false,
        'customizer'           => false,
        'forced_dev_mode_off'  => true,
        'page_permissions'     => 'edit_pages',
        'menu_icon'            => '',
        'show_import_export'   => false,
        'save_defaults'        => true,
        'page_slug'            => 'kspoptions',
        'ajax_save'            => true,
        'default_show'         => false,
        'default_mark'         => '',
        'footer_credit' => __('Thank you for using the Kadence Slider by <a href="http://kadencethemes.com/" target="_blank">Kadence Themes</a>.', 'kadence-slider'),
        'hints'                => array(
            'icon'          => 'kt-icon-question',
            'icon_position' => 'right',
            'icon_color'    => '#444',
            'icon_size'     => 'normal',
            'tip_style'     => array(
                'color'   => 'dark',
                'shadow'  => true,
                'rounded' => false,
                'style'   => '',
            ),
            'tip_position'  => array(
                'my' => 'top left',
                'at' => 'bottom right',
            ),
            'tip_effect'    => array(
                'show' => array(
                    'effect'   => 'slide',
                    'duration' => '500',
                    'event'    => 'mouseover',
                ),
                'hide' => array(
                    'effect'   => 'slide',
                    'duration' => '500',
                    'event'    => 'click mouseleave',
                ),
            ),
        ),
    );

   $args['share_icons'][] = array(
        'url' => 'https://www.facebook.com/KadenceThemes',
        'title' => 'Follow Kadence Themes on Facebook', 
        'icon' => 'dashicons dashicons-facebook',
    );
    $args['share_icons'][] = array(
        'url' => 'https://www.twitter.com/KadenceThemes',
        'title' => 'Follow Kadence Themes on Twitter', 
        'icon' => 'dashicons dashicons-twitter',
    );
    $args['share_icons'][] = array(
        'url' => 'https://www.instagram.com/KadenceThemes',
        'title' => 'Follow Kadence Themes on Instagram', 
        'icon' => 'dashicons dashicons-format-image',
    );


    // Add content after the form.
    //$args['footer_text'] = '';

    Redux::setArgs( $opt_name, $args );
    Redux::setSection( $opt_name, array(
    'icon' => 'kt-icon-font-size',
    'icon_class' => 'icon-large',
    'id' => 'ksp_typography',
    'title' => __('Kadence Slider Font Options', 'kadence-slider'),
    'desc' => "",
    'fields' => array(
      array(
            'id'=>'font_option_one',
            'type' => 'typography', 
            'title' => __('Font Option 1', 'kadence-slider'),
            'font-family'=>true, 
            'google'=>true, // Disable google fonts. Won't work if you haven't defined your google api key
            'font-backup'=>false, // Select a backup non-google font in addition to a google font
            'font-weight'=>true, // Includes font-style and weight. Can use font-style or font-weight to declare
            'subsets'=>true, // Only appears if google is true and subsets not set to false
            'font-size'=>false,
            'line-height'=>false,
            'text-align' => false,
            'all_styles' => false,
            'customizer' => false,
            'color'=>false,
            'preview'=>true, // Disable the previewer
            'output' => array(''),
            'subtitle'=> __("Choose a Font Family for the Kadence Slider", 'kadence-slider'),
            'default'=> array(
                'font-family'=>'Raleway',
                'font-weight'=>'800'
                 ),
            'preview' => array(
                'font-size' => '34px'
                ),
            ),
      array(
            'id'=>'font_option_two',
            'type' => 'typography', 
            'title' => __('Font Option 2', 'kadence-slider'),
            'font-family'=>true, 
            'google'=>true, // Disable google fonts. Won't work if you haven't defined your google api key
            'font-backup'=>false, // Select a backup non-google font in addition to a google font
            'font-weight'=>true, // Includes font-style and weight. Can use font-style or font-weight to declare
            'subsets'=>true, // Only appears if google is true and subsets not set to false
            'font-size'=>false,
            'line-height'=>false,
            'text-align' => false,
            'all_styles' => false,
            'customizer' => false,
            'color'=>false,
            'preview'=>true, // Disable the previewer
            'output' => array(''),
            'subtitle'=> __("Choose a Font Family for the Kadence Slider", 'kadence-slider'),
            'default'=> array(
                'font-family'=>'Raleway',
                'font-weight'=>'600'
                 ),
            'preview' => array(
                'font-size' => '34px'
                ),
            ),
      array(
            'id'=>'font_option_three',
            'type' => 'typography', 
            'title' => __('Font Option 3', 'kadence-slider'),
            'font-family'=>true, 
            'google'=>true, // Disable google fonts. Won't work if you haven't defined your google api key
            'font-backup'=>false, // Select a backup non-google font in addition to a google font
            'font-weight'=>true, // Includes font-style and weight. Can use font-style or font-weight to declare
            'subsets'=>true, // Only appears if google is true and subsets not set to false
            'font-size'=>false,
            'line-height'=>false,
            'text-align' => false,
            'all_styles' => false,
            'customizer' => false,
            'color'=>false,
            'preview'=>true, // Disable the previewer
            'output' => array(''),
            'subtitle'=> __("Choose a Font Family for the Kadence Slider", 'kadence-slider'),
            'default'=> array(
                'font-family'=>'Raleway',
                'font-weight'=>'400'
                 ),
            'preview' => array(
                'font-size' => '34px'
                ),
            ),
      array(
            'id'=>'font_option_four',
            'type' => 'typography', 
            'title' => __('Font Option 4', 'kadence-slider'),
            'font-family'=>true, 
            'google'=>true, // Disable google fonts. Won't work if you haven't defined your google api key
            'font-backup'=>false, // Select a backup non-google font in addition to a google font
            'font-weight'=>true, // Includes font-style and weight. Can use font-style or font-weight to declare
            'subsets'=>true, // Only appears if google is true and subsets not set to false
            'font-size'=>false,
            'line-height'=>false,
            'text-align' => false,
            'all_styles' => false,
            'customizer' => false,
            'color'=>false,
            'preview'=>true, // Disable the previewer
            'output' => array(''),
            'subtitle'=> __("Choose a Font Family for the Kadence Slider", 'kadence-slider'),
            'default'=> array(
                'font-family'=>'Raleway',
                'font-weight'=>'300'
                 ),
            'preview' => array(
                'font-size' => '34px'
                ),
            ),
      array(
            'id'=>'font_option_five',
            'type' => 'typography', 
            'title' => __('Font Option 5', 'kadence-slider'),
            'font-family'=>true, 
            'google'=>true, // Disable google fonts. Won't work if you haven't defined your google api key
            'font-backup'=>false, // Select a backup non-google font in addition to a google font
            'font-weight'=>true, // Includes font-style and weight. Can use font-style or font-weight to declare
            'subsets'=>true, // Only appears if google is true and subsets not set to false
            'font-size'=>false,
            'line-height'=>false,
            'text-align' => false,
            'all_styles' => false,
            'customizer' => false,
            'color'=>false,
            'preview'=>true, // Disable the previewer
            'output' => array(''),
            'subtitle'=> __("Choose a Font Family for the Kadence Slider", 'kadence-slider'),
            'default'=> array(
                'font-family'=>'Raleway',
                'font-weight'=>'200'
                 ),
            'preview' => array(
                'font-size' => '34px'
                ),
            ),
       array(
            'id'=>'ksp_load_fonts',
            'type' => 'switch', 
            'title' => __('Load fonts from Google Fonts?', 'kadence-slider'),
            'subtitle'=> __('Turn this off if you are loading font families through theme or other method.', 'kadence-slider'),
            "default"       => 1,
            ),
          ),
      ) );

    Redux::setExtensions( 'kadence_slider', KADENCE_SLIDER_PATH . '/extensions/' );
  }
function ksp_override_redux_css() {
  wp_dequeue_style( 'redux-admin-css' );
  wp_register_style('ksp-redux-custom-css', KADENCE_SLIDER_URL . 'admin/css/ksp_font_options.css', false, 152);    
  wp_enqueue_style('ksp-redux-custom-css');
  //wp_dequeue_style( 'select2-css');
  wp_dequeue_style( 'redux-elusive-icon' );
  wp_dequeue_style( 'redux-elusive-icon-ie7' );
}

add_action('redux-enqueue-kadence_slider', 'ksp_override_redux_css');


function ksp_font_list() {
    $data = get_option('kadence_slider');
    $fonts = array();
    if(isset($data['font_option_one']) && !empty($data['font_option_one'])) {
        if(isset($data['font_option_one']['google']) && $data['font_option_one']['google']) {$google = 'true';} else {$google = 'false';}
        if(isset($data['font_option_one']['font-style'])) {$style = $data['font_option_one']['font-style'];} else {$style = null;}
        $fonts[] = array("id" => 'font_option_one', "info" => array("name" => $data['font_option_one']['font-family'], "data-weight" => $data['font_option_one']['font-weight'], "data-style" => $style, "data-google" => $google ));
    }
    if(isset($data['font_option_two']) && !empty($data['font_option_two'])) {
        if(isset($data['font_option_two']['google']) && $data['font_option_two']['google']) {$google = 'true';} else {$google = 'false';}
        if(isset($data['font_option_two']['font-style'])) {$style = $data['font_option_two']['font-style'];} else {$style = null;}
        $fonts[] = array("id" => 'font_option_two', "info" => array("name" => $data['font_option_two']['font-family'], "data-weight" => $data['font_option_two']['font-weight'], "data-style" => $style, "data-google" => $google ));
    }
    if(isset($data['font_option_three']) && !empty($data['font_option_three'])) {
        if(isset($data['font_option_three']['google']) && $data['font_option_three']['google']) {$google = 'true';} else {$google = 'false';}
        if(isset($data['font_option_three']['font-style'])) {$style = $data['font_option_three']['font-style'];} else {$style = null;}
        $fonts[] = array("id" => 'font_option_three', "info" => array("name" => $data['font_option_three']['font-family'], "data-weight" => $data['font_option_three']['font-weight'], "data-style" => $style, "data-google" => $google ));
    }
    if(isset($data['font_option_four']) && !empty($data['font_option_four'])) {
        if(isset($data['font_option_four']['google']) && $data['font_option_four']['google']) {$google = 'true';} else {$google = 'false';}
        if(isset($data['font_option_four']['font-style'])) {$style = $data['font_option_four']['font-style'];} else {$style = null;}
        $fonts[] = array("id" => 'font_option_four', "info" => array("name" => $data['font_option_four']['font-family'], "data-weight" => $data['font_option_four']['font-weight'], "data-style" => $style, "data-google" => $google ));
    }
    if(isset($data['font_option_five']) && !empty($data['font_option_five'])) {
        if(isset($data['font_option_five']['google']) && $data['font_option_five']['google']) {$google = 'true';} else {$google = 'false';}
        if(isset($data['font_option_five']['font-style'])) {$style = $data['font_option_five']['font-style'];} else {$style = null;}
        $fonts[] = array("id" => 'font_option_five', "info" => array("name" => $data['font_option_five']['font-family'], "data-weight" => $data['font_option_five']['font-weight'], "data-style" => $style, "data-google" => $google ));
    }

 return apply_filters('ksp_font_family', $fonts);
}
function ksp_font_list_select($selected_font) {
    $data = get_option('kadence_slider');
    $fonts = ksp_font_list();
    $output = '<select class="ksp-layer-font">';
    foreach ($fonts as $font) {
        if($font['id'] == $selected_font) {$selected = 'selected';} else {$selected = '';}
       $output .= '<option '.$selected.' data-weight="'.$font['info']['data-weight'].'" data-style="'.$font['info']['data-style'].'" data-google="'.$font['info']['data-google'].'" data-family="'.$font['info']['name'].'" value="'.$font['id'].'">'.$font['info']['name'].' '.$font['info']['data-weight'].'</option>';
    }
    $output .= '</select>';

 return $output;
}

