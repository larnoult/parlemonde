<?php
define( 'LAYOUT_PATH', get_template_directory() . '/assets/css/skins/' );
define( 'OPTIONS_PATH', get_template_directory_uri() . '/themeoptions/options/' );
load_theme_textdomain('virtue', get_template_directory() . '/languages');
$alt_stylesheet_path = apply_filters('kt_skin_style_path', LAYOUT_PATH);
$alt_stylesheets = array(); 
if ( is_dir($alt_stylesheet_path) ) {
    if ($alt_stylesheet_dir = opendir($alt_stylesheet_path) ) {
        while ( ($alt_stylesheet_file = readdir($alt_stylesheet_dir)) !== false ) {
            if(stristr($alt_stylesheet_file, ".css") !== false) {
                $alt_stylesheets[$alt_stylesheet_file] = $alt_stylesheet_file;
            }
        }
        closedir($alt_stylesheet_dir);
    }
}


// BEGIN Config

if ( ! class_exists( 'Redux' ) ) {
        return;
    }
// This is your option name where all the Redux data is stored.
    $opt_name = "virtue";

    // If Redux is running as a plugin, this will remove the demo notice and links
    add_action( 'redux/loaded', 'virtue_remove_demo' );

    $theme = wp_get_theme(); // For use with some settings. Not necessary.
    $args = array(
        'opt_name'             => $opt_name,
        'display_name'         => $theme->get( 'Name' ),
        'display_version'      => $theme->get( 'Version' ),
        'page_type'            => 'submenu',
        'allow_sub_menu'       => false,
        'menu_title'           => __('Theme Options', 'virtue'),
        'page_title'           => __('Theme Options', 'virtue'),
        'google_api_key'       => 'AIzaSyALkgUvb8LFAmrsczX56ZGJx-PPPpwMid0',
        'google_update_weekly' => false,
        'async_typography'     => false,
        'admin_bar'            => true,
        'admin_bar_icon'       => 'dashicons-admin-generic',
        'admin_bar_priority'   => 50,
        'use_cdn'              => false,
        'dev_mode'             => false,
        'forced_dev_mode_off'  => true,
        'update_notice'        => false,
        'customizer'           => true,
        'page_priority'        => 50,
        'page_permissions'     => 'manage_options',
        'menu_icon'            => '',
        'page_icon'            => 'kad_logo_header',
        'page_slug'            => 'ktoptions',
        'ajax_save'            => true,
        'default_show'         => false,
        'default_mark'         => '',
        'disable_tracking'     => true,
        'customizer_only'      => true,
        'save_defaults'        => false,
        'intro_text'           => 'Upgrade to <a href="https://www.kadencethemes.com/product/virtue-premium-theme/?utm_source=themeoptions&utm_medium=banner&utm_campaign=virtue_premium" target="_blank" >Virtue Premium</a> for more great features. Over 50 more theme options, premium sliders and carousels, breadcrumbs, custom post types and much much more!',           
        'footer_credit'        => __('Thank you for using the Virtue Theme by <a href="https://www.kadencethemes.com/" target="_blank">Kadence Themes</a>.', 'virtue'),
        'hints'                => array(
            'icon'          => 'icon-question',
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
    // SOCIAL ICONS -> Setup custom links in the footer for quick links in your panel footer icons.
        $args['share_icons'][] = array(
            'url' => 'https://www.facebook.com/KadenceThemes',
            'title' => 'Follow Kadence Themes on Facebook', 
            'icon' => 'icon-facebook',
        );
        $args['share_icons'][] = array(
            'url' => 'https://www.twitter.com/KadenceThemes',
            'title' => 'Follow Kadence Themes on Twitter', 
            'icon' => 'icon-twitter',
        );
        $args['share_icons'][] = array(
            'url' => 'https://www.instagram.com/KadenceThemes',
            'title' => 'Follow Kadence Themes on Instagram', 
            'icon' => 'icon-instagram',
        );
        $args = apply_filters('kadence_theme_options_args', $args);
   Redux::setArgs( $opt_name, $args );

   // -> START Basic Fields                

    Redux::setSection( $opt_name, array(
    'title' => __('Main Settings', 'virtue'),
    'id' => 'main_settings',
    'header' => '',
    'desc' => "<div class='redux-info-field'><h3>".__('Welcome to Virtue Theme Options', 'virtue')."</h3>
        <p>".__('This theme was developed by', 'virtue')." <a href=\"https://kadencethemes.com/\" target=\"_blank\">Kadence Themes</a></p>
        <p>".__('For theme documentation visit', 'virtue').": <a href=\"http://docs.kadencethemes.com/virtue-free/\" target=\"_blank\">docs.kadencethemes.com/virtue-free/</a>
        <br />
        ".__('For support please visit', 'virtue').": <a href=\"http://wordpress.org/support/theme/virtue\" target=\"_blank\">wordpress.org/support/theme/virtue</a></p></div>",
    'icon_class' => 'icon-large',
    'icon' => 'icon-dashboard',
    'customizer' => true,
    'fields' => array(
        array(
            'id'=>'boxed_layout',
            'type' => 'image_select',
            'compiler'=> false,
            'customizer' => true,
            'title' => __('Site Layout Style', 'virtue'), 
            'subtitle' => __('Select Boxed or Wide Site Layout Style', 'virtue'),
            'options' => array(
                    'wide' => array('alt' => 'Wide Layout', 'img' => OPTIONS_PATH.'img/1c.png'),
                    'boxed' => array('alt' => 'Boxed Layout', 'img' => OPTIONS_PATH.'img/3cm.png'),
                ),
            'default' => 'wide',
            ),
        array(
            'id'=>'footer_layout',
            'type' => 'image_select',
            'compiler' => false,
            'customizer' => true,
            'title' => __('Footer Widget Layout', 'virtue'), 
            'subtitle' => __('Select how many columns for footer widgets', 'virtue'),
            'options' => array(
                    'fourc' => array('alt' => 'Four Column Layout', 'img' => OPTIONS_PATH.'img/footer-widgets-4.png'),
                    'threec' => array('alt' => 'Three Column Layout', 'img' => OPTIONS_PATH.'img/footer-widgets-3.png'),
                    'twoc' => array('alt' => 'Two Column Layout', 'img' => OPTIONS_PATH.'img/footer-widgets-2.png'),
                ),
            'default' => 'fourc',
            ),
        array(
            'id'=>'logo_options',
            'type' => 'info',
            'customizer' => true,
            'desc' => __('Logo Options', 'virtue'),
            ),
         array(
            'id'=>'logo_layout',
            'type' => 'image_select',
            'compiler' => false,
            'customizer' => true,
            'title' => __('Logo Layout', 'virtue'), 
            'subtitle' => __('Choose how you want your logo to be laid out', 'virtue'),
            'options' => array(
                    'logoleft' => array('alt' => 'Logo Left Layout', 'img' => OPTIONS_PATH.'img/logo_layout_01.png'),
                    'logohalf' => array('alt' => 'Logo Half Layout', 'img' => OPTIONS_PATH.'img/logo_layout_03.png'),
                    'logocenter' => array('alt' => 'Logo Center Layout', 'img' => OPTIONS_PATH.'img/logo_layout_02.png'),
                ),
            'default' => 'logoleft',
            ),
        array(
            'id'=>'x1_virtue_logo_upload',
            'type' => 'media', 
            'url'=> true,
            'customizer' => true,
            'title' => __('Logo', 'virtue'),
            'subtitle' => __('Upload your Logo. If left blank theme will use site name.', 'virtue'),
            ),
        array(
            'id'=>'x2_virtue_logo_upload',
            'type' => 'media', 
            'url'=> true,
            'customizer' => true,
            'title' => __('Upload Your @2x Logo for Retina Screens', 'virtue'),
            'compiler' => 'true',
            'subtitle' => __('Should be twice the pixel size of your normal logo.', 'virtue'),
            ),
        array(
            'id'=>'font_logo_style',
            'type' => 'typography', 
            'title' => __('Sitename Logo Font', 'virtue'),
            //'compiler'=>true, // Use if you want to hook in your own CSS compiler
            'font-family'=>true, 
            'google'=>true, // Disable google fonts. Won't work if you haven't defined your google api key
            'font-backup'=>false, // Select a backup non-google font in addition to a google font
            'font-style'=>true, // Includes font-style and weight. Can use font-style or font-weight to declare
            'subsets'=>true, // Only appears if google is true and subsets not set to false
            'font-size'=>true,
            'line-height'=>true,
            'text-align' => false,
            'customizer' => false,
            //'word-spacing'=>false, // Defaults to false
            //'all_styles' => true,
            'color'=>true,
            'preview'=>true, // Disable the previewer
            'output' => array('header #logo a.brand', ".logofont"),
            'subtitle'=> __("Choose size and style your sitename, if you don't use an image logo.", 'virtue'),
            'default'=> array(
                'font-family'=>'Lato',
                'color'=>"", 
                'font-style'=>'400',
                'font-size'=>'32px', 
                'line-height'=>'40px', ),
            ),
        array(
            'id'=>'logo_below_text',
            'type' => 'textarea',
            'customizer' => true,
            'title' => __('Site Tagline - Below Logo', 'virtue'), 
            'subtitle' => __('An optional line of text below your logo', 'virtue'),
            //'desc' => __('This is the description field, again good for additional info.', 'virtue'),
            'validate' => 'html', //see http://codex.wordpress.org/Function_Reference/wp_kses_post
            'default' => '',
            ),
        array(
            'id'=>'font_tagline_style',
            'type' => 'typography', 
            'title' => __('Site Tagline Font', 'virtue'),
            //'compiler'=>true, // Use if you want to hook in your own CSS compiler
            'font-family'=>true, 
            'google'=>true, // Disable google fonts. Won't work if you haven't defined your google api key
            'font-backup'=>false, // Select a backup non-google font in addition to a google font
            'font-style'=>true, // Includes font-style and weight. Can use font-style or font-weight to declare
            'subsets'=>true, // Only appears if google is true and subsets not set to false
            'font-size'=>true,
            'customizer' => false,
            'line-height'=>true,
            'text-align' => false,
            'color'=>true,
            'preview'=>true,
            'output' => array('.kad_tagline'), // An array of CSS selectors to apply this font style to dynamically
            'subtitle'=> __("Choose size and style your site tagline", 'virtue'),
            'default'=> array(
                'font-family'=>'Lato',
                'color'=>"#444444", 
                'font-style'=>'400',
                'font-size'=>'14px', 
                'line-height'=>'20px', ),
            ),
        array(
            'id'=>'logo_padding_top',
            'type' => 'slider', 
            'title' => __('Logo Spacing', 'virtue'),
            'desc'=> __('Top Spacing', 'virtue'),
            "default"       => "25",
            "min"       => "0",
            "step"      => "1",
            'customizer' => true,
            "max"       => "80",
            ), 
        array(
            'id'=>'logo_padding_bottom',
            'type' => 'slider', 
            'title' => __('Logo Spacing', 'virtue'),
            'desc'=> __('Bottom Spacing', 'virtue'),
            "default"       => "10",
            "min"       => "0",
            'customizer' => true,
            "step"      => "1",
            "max"       => "80",
            ),
            array(
            'id'=>'logo_padding_left',
            'type' => 'slider', 
            'title' => __('Logo Spacing', 'virtue'),
            'desc'=> __('Left Spacing', 'virtue'),
            "default"       => "0",
            "min"       => "0",
            'customizer' => true,
            "step"      => "1",
            "max"       => "80",
            ), 
            array(
            'id'=>'logo_padding_right',
            'type' => 'slider', 
            'title' => __('Logo Spacing', 'virtue'),
            'desc'=> __('Right Spacing', 'virtue'),
            "default"       => "0",
            "min"       => "0",
            'customizer' => true,
            "step"      => "1",
            "max"       => "80",
            ),
        array(
            'id'=>'menu_margin_top',
            'type' => 'slider', 
            'title' => __('Primary Menu Spacing', 'virtue'),
            'desc'=> __('Top Spacing', 'virtue'),
            "default"       => "40",
            "min"       => "0",
            "step"      => "1",
            'customizer' => true,
            "max"       => "80",
            ), 
         array(
            'id'=>'menu_margin_bottom',
            'type' => 'slider', 
            'title' => __('Primary Menu Spacing', 'virtue'),
            'desc'=> __('Bottom Spacing', 'virtue'),
            "default"       => "10",
            "min"       => "0",
            'customizer' => true,
            "step"      => "1",
            "max"       => "80",
            ), 
         array(
            'id'=>'virtue_banner_upload',
            'type' => 'media', 
            'url'=> true,
            'customizer' => true,
            'title' => __('Sitewide Banner', 'virtue'),
            'compiler' => 'true',
            'subtitle' => __('Upload a banner for bottom of header.', 'virtue'),
            ),
         ),
    )
);
Redux::setSection( $opt_name, array(
    'icon' => 'icon-cogs',
    'icon_class' => 'icon-large',
    'id' => 'topbar_settings',
    'title' => __('Topbar Settings', 'virtue'),
    'fields' => array(
        array(
            'id'=>'topbar',
            'type' => 'switch', 
            'customizer' => true,
            'title' => __('Use Topbar?', 'virtue'),
            'subtitle'=> __('Choose to show or hide topbar', 'virtue'),
            "default"       => 1,
            ), 
        array(
            'id'=>'topbar_icons',
            'type' => 'switch',
            'customizer' => false,
            'title' => __('Use Topbar Icon Menu?', 'virtue'),
            'subtitle'=> __('Choose to show or hide topbar icon Menu', 'virtue'),
            "default"       => 0,
            ),
        array(
            'id'=>'topbar_icon_menu',
            'type' => 'kad_icons',
            'customizer' => false,
            'title' => __('Topbar Icon Menu', 'virtue'),
            'subtitle'=> __('Choose your icons for the topbar icon menu.', 'virtue'),
            //'desc' => __('This field will store all slides values into a multidimensional array to use into a foreach loop.', 'virtue')
        ), 
        array(
            'id'=>'show_cartcount',
            'type' => 'switch', 
            'customizer' => true,
            'title' => __('Show Cart total in topbar?', 'virtue'),
            'subtitle'=> __('This only works if using woocommerce', 'virtue'),
            "default"       => 1,
            ), 
        array(
            'id'=>'topbar_search',
            'type' => 'switch', 
            'customizer' => true,
            'title' => __('Display Search in Topbar?', 'virtue'),
            'subtitle'=> __('Choose to show or hide search in topbar', 'virtue'),
            "default"       => 1,
            ),
        array(
            'id'=>'topbar_widget',
            'type' => 'switch', 
            'customizer' => true,
            'title' => __('Enable widget area in right of Topbar?', 'virtue'),
            'subtitle'=> __('Note this will hide remove search (you can re-enable it by adding it to the widget area)', 'virtue'),
            "default"       => 0,
            ),
        array(
            'id'=>'topbar_layout',
            'type' => 'switch', 
            'customizer' => true,
            'title' => __('Topbar Layout Switch', 'virtue'),
            'subtitle'=> __('This moves the left items to the right and right items to the left.', 'virtue'),
            "default"       => 0,
            ),
        ),
    )
);
Redux::setSection( $opt_name, array(
    'icon' => 'icon-picture',
    'id' => 'home_slider_settings',
    'icon_class' => 'icon-large',
    'title' => __('Home Slider Settings', 'virtue'),
    'desc' => "<div class='redux-info-field'><h3>".__('Home Page Slider Options', 'virtue')."</h3></div>",
    'fields' => array(
        array(
            'id'=>'info_home_slider_settings_notice',
            'type' => 'info',
            'customizer' => true,
            'desc' => __('*NOTE: Make sure Virtue/Pinnacle Toolkit plugin is activated* <br>Go to Apperance > Theme Options > Home Slider for all Home slider settings', 'virtue'),
            ),
        array(
            'id'=>'choose_slider',
            'type' => 'select',
            'title' => __('Choose a Home Image Slider', 'virtue'), 
            'subtitle' => __("If you don't want an image slider on your home page choose none.", 'virtue'),
            //'desc' => __('This is the description field, again good for additional info.', 'virtue'),
            'options' => array('none' => 'None','flex' => 'Flex Slider', 'fullwidth' => 'Fullwidth Slider','thumbs' => 'Thumb Slider', 'carousel' => 'Carousel Slider','latest' => 'Latest Posts', 'video' => 'Video'),
            'default' => '',
            'width' => 'width:60%',
            'customizer' => false,
            ),
        array(
            'id'=>'home_slider',
            'type' => 'kad_slides',
            'customizer' => false,
            'title' => __('Slider Images', 'virtue'),
            'subtitle'=> __('Use large images for best results.', 'virtue'),
            //'desc' => __('This field will store all slides values into a multidimensional array to use into a foreach loop.', 'virtue')
        ),  
        array(
            'id'=>'slider_size',
            'type' => 'slider', 
            'title' => __('Slider Max Height', 'virtue'),
            'desc'=> __('Note: does not work if images are smaller than max.', 'virtue'),
            "default"       => "400",
            "min"       => "100",
            "step"      => "5",
            'customizer' => false,
            "max"       => "600",
            ), 
        array(
            'id'=>'slider_size_width',
            'type' => 'slider', 
            'title' => __('Slider Max Width', 'virtue'),
            'desc'=> __('Note: does not work if images are smaller than max.', 'virtue'),
            "default"       => "1170",
            "min"       => "600",
            "step"      => "5",
            'customizer' => false,
            "max"       => "1170",
            ), 
        array(
            'id'=>'slider_autoplay',
            'type' => 'switch', 
            'title' => __('Auto Play?', 'virtue'),
            'subtitle'=> __('This determines if a slider automatically scrolls', 'virtue'),
            "default"       => 1,
            'customizer' => false,
            ),
        array(
            'id'=>'slider_pausetime',
            'type' => 'slider', 
            'title' => __('Slider Pause Time', 'virtue'),
            'desc'=> __('How long to pause on each slide, in milliseconds.', 'virtue'),
            "default"       => "7000",
            "min"       => "3000",
            "step"      => "1000",
            'customizer' => false,
            "max"       => "12000",
            ), 
        array(
            'id'=>'trans_type',
            'type' => 'select',
            'title' => __('Transition Type', 'virtue'), 
            'subtitle' => __("Choose a transition type", 'virtue'),
            'options' => array('fade' => 'Fade','slide' => 'Slide'),
            'customizer' => false,
            'default' => 'fade'
            ),
        array(
            'id'=>'slider_transtime',
            'type' => 'slider', 
            'title' => __('Slider Transition Time', 'virtue'),
            'desc'=> __('How long for slide transitions, in milliseconds.', 'virtue'),
            "default"       => "600",
            "min"       => "200",
            'customizer' => false,
            "step"      => "100",
            "max"       => "1200",
            ), 
        array(
            'id'=>'slider_captions',
            'type' => 'switch', 
            'title' => __('Show Captions?', 'virtue'),
            'subtitle'=> __('Choose to show or hide captions', 'virtue'),
            "default"       => 0,
            'customizer' => false,
            ),
        array(
            'id'=>'video_embed',
            'type' => 'textarea',
            'customizer' => false,
            'title' => __('Video Embed Code', 'virtue'), 
            'subtitle' => __('If your using a video on the home page place video embed code here.', 'virtue'),
            'default' => ''
            ),
         ),
    )
);

Redux::setSection( $opt_name, array(
    'icon' => 'icon-tablet',
    'icon_class' => 'icon-large',
    'id' => 'home_mobile_slider_settings',
    'title' => __('Home Mobile Slider', 'virtue'),
    'desc' => "<div class='redux-info-field'><h3>".__('Create a more lightweight home slider for your mobile visitors.', 'virtue')."</h3></div>",
    'fields' => array(
    	array(
            'id'=>'mobile_switch',
            'type' => 'switch',
            'customizer' => false,
            'title' => __('Would you like to use this feature?', 'virtue'),
            'subtitle'=> __('Choose if you would like to show a different slider on your home page for your mobile visitors.', 'virtue'),
            "default"       => 0,
            ),
        array(
            'id'=>'choose_mobile_slider',
            'type' => 'select',
            'customizer' => false,
            'title' => __('Choose a Slider for Mobile', 'virtue'), 
            'subtitle' => __("Choose which slider you would like to show for mobile viewers.", 'virtue'),
            //'desc' => __('This is the description field, again good for additional info.', 'virtue'),
            'options' => array('none' => 'None','flex' => 'Flex Slider', 'video' => 'Video'),
            'default' => 'none',
            'width' => 'width:60%',
            'required' => array('mobile_switch','=','1'),
            ),
        array(
            'id'=>'home_mobile_slider',
            'type' => 'kad_slides',
            'customizer' => false,
            'title' => __('Slider Images', 'virtue'),
            'subtitle'=> __('Use large images for best results.', 'virtue'),
            'required' => array('mobile_switch','=','1'),
            //'desc' => __('This field will store all slides values into a multidimensional array to use into a foreach loop.', 'virtue')
        ),  
        array(
            'id'=>'mobile_slider_size',
            'type' => 'slider', 
            'customizer' => false,
            'title' => __('Slider Max Height', 'virtue'),
            'desc'=> __('Note: does not work if images are smaller than max.', 'virtue'),
            "default"       => "300",
            "min"       => "100",
            "step"      => "5",
            "max"       => "800",
            'required' => array('mobile_switch','=','1'),
            ), 
        array(
            'id'=>'mobile_slider_size_width',
            'type' => 'slider', 
            'customizer' => false,
            'title' => __('Slider Max Width', 'virtue'),
            'desc'=> __('Note: does not work if images are smaller than max.', 'virtue'),
            "default"       => "480",
            "min"       => "200",
            "step"      => "5",
            "max"       => "800",
            'required' => array('mobile_switch','=','1'),
            ), 
        array(
            'id'=>'mobile_slider_autoplay',
            'type' => 'switch', 
            'customizer' => false,
            'title' => __('Auto Play?', 'virtue'),
            'subtitle'=> __('This determines if a slider automatically scrolls', 'virtue'),
            "default"       => 1,
            'required' => array('mobile_switch','=','1'),
            ),
        array(
            'id'=>'mobile_slider_pausetime',
            'type' => 'slider',
            'customizer' => false, 
            'title' => __('Slider Pause Time', 'virtue'),
            'desc'=> __('How long to pause on each slide, in milliseconds.', 'virtue'),
            "default"       => "7000",
            "min"       => "3000",
            "step"      => "1000",
            "max"       => "12000",
            'required' => array('mobile_switch','=','1'),
            ), 
        array(
            'id'=>'mobile_trans_type',
            'type' => 'select',
            'customizer' => false,
            'title' => __('Transition Type', 'virtue'), 
            'subtitle' => __("Choose a transition type", 'virtue'),
            'options' => array('fade' => 'Fade','slide' => 'Slide'),
            'default' => 'fade',
            'required' => array('mobile_switch','=','1'),
            ),
        array(
            'id'=>'mobile_slider_transtime',
            'type' => 'slider', 
            'customizer' => false,
            'title' => __('Slider Transition Time', 'virtue'),
            'desc'=> __('How long for slide transitions, in milliseconds.', 'virtue'),
            "default"       => "600",
            "min"       => "200",
            "step"      => "100",
            "max"       => "1200",
            'required' => array('mobile_switch','=','1'),
            ), 
        array(
            'id'=>'mobile_slider_captions',
            'type' => 'switch', 
            'customizer' => false,
            'title' => __('Show Captions?', 'virtue'),
            'subtitle'=> __('Choose to show or hide captions', 'virtue'),
            "default"       => 0,
            'required' => array('mobile_switch','=','1'),
            ),
        array(
            'id'=>'mobile_video_embed',
            'type' => 'textarea',
            'customizer' => false,
            'title' => __('Video Embed Code', 'virtue'), 
            'subtitle' => __('If your using a video on the home page place video embed code here.', 'virtue'),
            'default' => '',
            'required' => array('mobile_switch','=','1'),
            ),
         ),
    )
);
Redux::setSection( $opt_name, array(
    'icon' => 'icon-home',
    'icon_class' => 'icon-large',
    'id' => 'home_layout',
    'title' => __('Home Layout', 'virtue'),
    'desc' => "",
    'fields' => array(
    	 array(
            'id'=>'home_sidebar_layout',
            'type' => 'image_select',
            'compiler'=> false,
            'customizer' => true,
            'title' => __('Display a sidebar on the Home Page?', 'virtue'), 
            'subtitle' => __('This determines if there is a sidebar on the home page.', 'virtue'),
            'options' => array(
                    'full' => array('alt' => 'Full Layout', 'img' => OPTIONS_PATH.'img/1col.png'),
                    'sidebar' => array('alt' => 'Sidebar Layout', 'img' => OPTIONS_PATH.'img/2cr.png'),
                ),
            'default' => 'full',
            ),
    	 array(
            'id'=>'home_sidebar',
            'type' => 'select',
            'customizer' => true,
            'title' => __('Choose a Sidebar for your Home Page', 'virtue'), 
            //'subtitle' => __("Choose your Revolution Slider Here", 'virtue'),
            //'desc' => __('This is the description field, again good for additional info.', 'virtue'),
            'data' => 'sidebars',
            'default' => 'sidebar-primary',
            'width' => 'width:60%',
            ),
    	 array(
            "id" => "homepage_layout",
            "type" => "sorter",
            'customizer' => false,
            "title" => __('Homepage Layout Manager', 'virtue'),
            "subtitle" => __('Organize how you want the layout to appear on the homepage', 'virtue'),
            //"compiler"=>'true',    
            'options' => array(
            	"disabled" => array(
                    "block_five"  => __("Latest Blog Posts", 'virtue'),
                    "block_six"   => __("Portfolio Carousel", 'virtue'),
                    "block_seven" => __("Icon Menu", 'virtue'),
                ),
                "enabled" => array(
                    "block_one"   => __("Page Title", 'virtue'),
                    "block_four"  => __("Page Content", 'virtue'),
                ),
            ),
        ),

         array(
            'id'=>'info_blog_settings',
            'type' => 'info',
            'customizer' => false,
            'desc' => __('Home Blog Settings', 'virtue'),
            ),
         array(
            'id'=>'blog_title',
            'type' => 'text',
            'customizer' => false,
            'title' => __('Home Blog Title', 'virtue'),
            'subtitle' => __('ex: Latest from the blog', 'virtue'),
            ),
         array(
            'id'=>'home_post_count',
            'type' => 'slider', 
            'customizer' => false,
            'title' => __('Choose How many posts on Homepage', 'virtue'),
            //'desc'=> __('Note: does not work if images are smaller than max.', 'virtue'),
            "default"       => "4",
            "min"       => "2",
            "step"      => "2",
            "max"       => "8",
            ),
         array(
            'id'=>'home_post_type',
            'type' => 'select',
            'customizer' => false,
            'data' => 'categories',
            'title' => __('Limit posts to a Category', 'virtue'), 
            'subtitle' => __('Leave blank to select all', 'virtue'),
            'width' => 'width:60%',
            //'desc' => __('This is the description field, again good for additional info.', 'virtue'),
            ),
         array(
            'id'=>'info_portfolio_settings',
            'type' => 'info',
            'customizer' => false,
            'desc' => __('Home Portfolio Carousel Settings', 'virtue'),
            ),
         array(
            'id'=>'portfolio_title',
            'type' => 'text',
            'customizer' => false,
            'title' => __('Home Portfolio Carousel title', 'virtue'),
            'subtitle' => __('ex: Portfolio Carousel title', 'virtue'),
            ),
         array(
            'id'=>'portfolio_type',
            'type' => 'select',
            'customizer' => false,
            'data' => 'terms',
            'args' => array('taxonomies'=>'portfolio-type', 'args'=>array()),
            'title' => __('Portfolio Carousel Category Type', 'virtue'), 
            'subtitle' => __('Leave blank to select all types', 'virtue'),
            'width' => 'width:60%',
            //'desc' => __('This is the description field, again good for additional info.', 'virtue'),
            ),
         array(
            'id'=>'home_portfolio_carousel_count',
            'type' => 'slider', 
            'customizer' => false,
            'title' => __('Choose how many portfolio items are in carousel', 'virtue'),
            "default"       => "6",
            "min"       => "4",
            "step"      => "1",
            "max"       => "12",
            ),
         array(
            'id'=>'home_portfolio_order',
            'type' => 'select',
            'customizer' => false,
            'title' => __('Portfolio Carousel Order by', 'virtue'), 
            'subtitle' => __("Choose how the portfolio items should be ordered in the carousel.", 'virtue'),
            'options' => array('menu_order' => 'Menu Order','title' => 'Title','date' => 'Date','rand' => 'Random'),
            'default' => 'menu_order',
            'width' => 'width:60%',
            ),
         array(
            'id'=>'portfolio_show_type',
            'type' => 'switch', 
            'customizer' => false,
            'title' => __('Display Portfolio Types under Title', 'virtue'),
            "default"       => 0,
            ),
           array(
            'id'=>'info_iconmenu_settings',
            'type' => 'info',
            'customizer' => false,
            'desc' => __('Home Icon Menu', 'virtue'),
            ),
           array(
            'id'=>'icon_menu',
            'type' => 'kad_icons',
            'customizer' => false,
            'title' => __('Icon Menu', 'virtue'),
            'subtitle'=> __('Choose your icons for the icon menu.', 'virtue'),
            //'desc' => __('This field will store all slides values into a multidimensional array to use into a foreach loop.', 'virtue')
        ), 
           array(
            'id'=>'home_icon_menu_column',
            'type' => 'slider', 
            'customizer' => false,
            'title' => __('Choose how many columns in each row', 'virtue'),
            "default"       => "3",
            "min"       => "2",
            "step"      => "1",
            "max"       => "6",
            ),
           array(
            'id'=>'info_page_content',
            'type' => 'info',
            'customizer' => false,
            'desc' => __('Page Content Options', 'virtue'),
            ),
           array(
            'id'=>'home_post_summery',
            'type' => 'select',
            'customizer' => true,
            'title' => __('Latest Post Display', 'virtue'), 
            'subtitle' => __("If Latest Post page is font page. Choose to show full post or post excerpt.", 'virtue'),
            //'desc' => __('This is the description field, again good for additional info.', 'virtue'),
            'options' => array('summery' => 'Post Excerpt','full' => 'Full'),
            'default' => 'summery',
            'width' => 'width:60%',
            ),
           array(
            'id'=>'info_home_layout_settings_notice',
            'type' => 'info',
            'customizer' => true,
            'desc' => __('*NOTE: Make sure Virtue/Pinnacle Toolkit plugin is activated* <br>Go to Apperance > Theme Options > Home Layout for all home layout settings', 'virtue'),
            ),
        ),

    )
);
Redux::setSection( $opt_name, array(
    'icon' => 'icon-shopping-cart',
    'id' => 'shop_settings',
    'icon_class' => 'icon-large',
    'title' => __('Shop Settings', 'virtue'),
    'desc' => "<div class='redux-info-field'><h3>".__('Shop Archive Page Settings (Woocommerce plugin required)', 'virtue')."</h3></div>",
    'fields' => array(
    	array(
            'id'=>'shop_layout',
            'type' => 'image_select',
            'compiler'=> false,
            'customizer' => true,
            'title' => __('Display the sidebar on shop archives?', 'virtue'), 
            'subtitle' => __('This determines if there is a sidebar on the shop and category pages.', 'virtue'),
            'options' => array(
                    'full' => array('alt' => 'Full Layout', 'img' => OPTIONS_PATH.'img/1col.png'),
                    'sidebar' => array('alt' => 'Sidebar Layout', 'img' => OPTIONS_PATH.'img/2cr.png'),
                ),
            'default' => 'full',
            ),
    	array(
            'id'=>'shop_sidebar',
            'type' => 'select',
            'title' => __('Choose a Sidebar for your shop page', 'virtue'), 
            'data' => 'sidebars',
            'customizer' => true,
            'default' => 'sidebar-primary',
            'width' => 'width:60%',
            ),            
    	array(
            'id'=>'products_per_page',
            'type' => 'slider', 
            'customizer' => true,
            'title' => __('How many products per page', 'virtue'),
            "default"       => "12",
            "min"       => "2",
            "step"      => "1",
            "max"       => "40",
            ),
    	array(
            'id'=>'shop_rating',
            'type' => 'switch', 
            'customizer' => true,
            'title' => __('Show Ratings in Shop and Category Pages', 'virtue'),
            'subtitle' => __('This determines if the rating is displayed in the product archive pages', 'virtue'),
            "default"=> 1,
            ),
        array(
            'id'=>'product_quantity_input',
            'type' => 'switch', 
            'customizer' => true,
            'title' => __('Quantity Box plus and minus', 'virtue'),
            'subtitle' => __('Turn this off if you would like to use browser added plus and minus for number boxes', 'virtue'),
            "default"=> 1,
            ),
        array(
            'id'=>'info_cat_product_size',
            'type' => 'info',
            'customizer' => true,
            'desc' => __('Shop Category Image Size', 'virtue'),
            ),
        array(
            'id'=>'product_cat_img_ratio',
            'type' => 'select',
            'title' => __('Category Image Aspect Ratio', 'virtue'), 
            'subtitle' => __('This sets how you want your category images to be cropped.', 'virtue'),
            'options' => array('square' => __('Square 1:1', 'virtue'), 'portrait' => __('Portrait 3:4', 'virtue'), 'landscape' => __('Landscape 4:3', 'virtue'), 'widelandscape' => __('Wide Landscape 4:2', 'virtue'), 'off' => __('Turn Off', 'virtue')),
            'default' => 'widelandscape',
            'customizer' => true,
            'width' => 'width:60%',
            ),
        array(
            'id'=>'info_shop_product_title',
            'type' => 'info',
            'customizer' => true,
            'desc' => __('Shop Product Title Settings', 'virtue'),
            ),
        array(
            'id'=>'font_shop_title',
            'type' => 'typography', 
            'title' => __('Shop & archive Product title Font', 'virtue'),
            //'compiler'=>true, // Use if you want to hook in your own CSS compiler
            'font-family'=>true, 
            'google'=>true, // Disable google fonts. Won't work if you haven't defined your google api key
            'font-backup'=>false, // Select a backup non-google font in addition to a google font
            'font-style'=>true, // Includes font-style and weight. Can use font-style or font-weight to declare
            'subsets'=>true, // Only appears if google is true and subsets not set to false
            'font-size'=>true,
            'line-height'=>true,
            'text-align' => false,
            'customizer' => false,
            //'word-spacing'=>false, // Defaults to false
            //'all_styles' => true,
            'color'=>true,
            'preview'=>true, // Disable the previewer
            'output' => array('.product_item .product_details h5'),
            'subtitle'=> __("Choose Size and Style for product titles on category and archive pages.", 'virtue'),
            'default'=> array(
                'font-family'=>'Lato',
                'color'=>"", 
                'font-style'=>'700',
                'font-size'=>'16px', 
                'line-height'=>'20px', ),
            ),
        array(
            'id'=>'shop_title_uppercase',
            'type' => 'switch', 
            'customizer' => true,
            'title' => __('Set Product Title to Uppercase?', 'virtue'),
            'subtitle' => __('This makes your product titles uppercase on Category pages', 'virtue'),
            "default"=> 0,
            ),
        array(
            'id'=>'shop_title_min_height',
            'type' => 'slider', 
            'title' => __('Product title Min Height', 'virtue'),
            'desc'=> __('If your titles are long increase this to help align your products height.', 'virtue'),
            "default"       => "40",
            "min"       => "20",
            "step"      => "5",
            'customizer' => true,
            "max"       => "120",
            ), 
         array(
            'id'=>'info_shop_img_size',
            'type' => 'info',
            'customizer' => true,
            'desc' => __('Product Image Sizes', 'virtue'),
            ),
    	array(
            'id'=>'product_img_resize',
            'type' => 'switch', 
            'customizer' => true,
            'title' => __('Enable Product Image Crop on Catalog pages', 'virtue'),
            'subtitle' => __('If turned off image dimensions are set by woocommerce settings - recommended width: 270px for Catalog Images', 'virtue'),
            "default"=> 1,
            ),
    	array(
            'id'=>'product_simg_resize',
            'type' => 'switch', 
            'customizer' => true,
            'title' => __('Enable Product Image Crop on product Page', 'virtue'),
            'subtitle' => __('If turned off image dimensions are set by woocommerce settings - recommended width: 468px for Single Product Image', 'virtue'),
            "default"=> 1,
            ),
        array(
            'id'=>'info_product_settings',
            'type' => 'info',
            'customizer' => true,
            'desc' => __('Product Page Settings', 'virtue'),
            ),
        array(
            'id'=>'product_gallery_slider',
            'type' => 'switch', 
            'title' => __('Enable woocommerce slider for product gallery? (must be woocommerce 3.0+)', 'virtue'),
            "default" => 0,
        ),
        array(
            'id'=>'product_gallery_zoom',
            'type' => 'switch', 
            'title' => __('Enable woocommerce hover zoom for product gallery? (must be woocommerce 3.0+)', 'virtue'),
            "default" => 0,
        ), 
        array(
            'id'=>'product_tabs',
            'type' => 'switch', 
            'title' => __('Display product tabs?', 'virtue'),
            'subtitle'=> __('This determines if product tabs are displayed', 'virtue'),
            "default"       => 1,
            'customizer' => true,
            'customizer' => true,
            ),
        array(
            'id'=>'related_products',
            'type' => 'switch', 
            'customizer' => true,
            'title' => __('Display related products?', 'virtue'),
            'subtitle'=> __('This determines related products are displayed', 'virtue'),
            "default"       => 1,
            ),
        ),
    )
);
Redux::setSection( $opt_name, array(
    'icon' => 'icon-pencil',
    'id' => 'basic_styling',
    'icon_class' => 'icon-large',
    'title' => __('Basic Styling', 'virtue'),
    'desc' => "<div class='redux-info-field'><h3>".__('Basic Stylng', 'virtue')."</h3></div>",
    'fields' => array(
    	  array(
            'id'=>'skin_stylesheet',
            'type' => 'select',
            'title' => __('Theme Skin Stylesheet', 'virtue'), 
            'subtitle' => __("Note* changes made in options panel will override this stylesheet. Example: Colors set in typography.", 'virtue'),
            //'desc' => __('This is the description field, again good for additional info.', 'virtue'),
            'options' => $alt_stylesheets,
            'default' => 'default.css',
            'width' => 'width:60%',
            'customizer' => true,
            ),
    	  array(
            'id'=>'primary_color',
            'type' => 'color',
            'title' => __('Primary Color', 'virtue'), 
            'subtitle' => __('Choose the default Highlight color for your site.', 'virtue'),
            'default' => '',
            'transparent'=>false,
            'validate' => 'color',
            'customizer' => true,
            ),
    	  array(
            'id'=>'primary20_color',
            'type' => 'color',
            'title' => __('20% lighter than Primary Color', 'virtue'), 
            'subtitle' => __('This is used for hover effects', 'virtue'),
            'default' => '',
            'transparent'=>false,
            'validate' => 'color',
            'customizer' => true,
            ),
    	  array(
            'id'=>'gray_font_color',
            'type' => 'color',
            'title' => __('Sitewide Gray Fonts', 'virtue'), 
            //'subtitle' => __('This is used for hover effects', 'virtue'),
            'default' => '',
            'transparent'=>false,
            'validate' => 'color',
            'customizer' => true,
            ),
    	  array(
            'id'=>'footerfont_color',
            'type' => 'color',
            'title' => __('Footer Font Color', 'virtue'), 
            //'subtitle' => __('This is used for hover effects', 'virtue'),
            'default' => '',
            'transparent'=>false,
            'validate' => 'color',
            'customizer' => true,
            ),
        ),
    )
);
Redux::setSection( $opt_name, array(
    'icon' => 'icon-edit',
    'icon_class' => 'icon-large',
    'id' => 'advanced_styling',
    'title' => __('Advanced Styling', 'virtue'),
    'desc' => "<div class='redux-info-field'><h3>".__('Main Content Background', 'virtue')."</h3></div>",
    'fields' => array(
    	array(
            'id'=>'content_bg_color',
            'type' => 'color',
            'title' => __('Content Background Color', 'virtue'), 
            'default' => '',
            'validate' => 'color',
            'customizer' => true,
            ),
    	array(
            'id'=>'bg_content_bg_img',
            'type' => 'media', 
            'url'=> true,
            'customizer' => true,
            'title' => __('Upload background image or texture', 'virtue'),
            ), 
    	array(
            'id'=>'content_bg_repeat',
            'type' => 'select',
            'customizer' => true,
            'title' => __('Image repeat options', 'virtue'), 
            'options' => array('no-repeat' => 'no-repeat', 'repeat' => 'repeat', 'repeat-x' => 'repeat-x', 'repeat-y' => 'repeat-y'),
            'width' => 'width:60%',
            ),
    	array(
            'id'=>'content_bg_placementx',
            'type' => 'select',
            'customizer' => true,
            'title' => __('X image placement options', 'virtue'), 
            'options' => array('left' => 'left', 'center' => 'center', 'right' => 'right'),
            'width' => 'width:60%',
            ),
    	array(
            'id'=>'content_bg_placementy',
            'type' => 'select',
            'customizer' => true,
            'title' => __('Y image placement options', 'virtue'), 
            'options' => array('top' => 'top', 'center' => 'center', 'bottom' => 'bottom',),
            'width' => 'width:60%',
            ),
    	array(
            'id'=>'info_topbar_background',
            'type' => 'info',
            'customizer' => true,
            'desc' => __('Topbar Background', 'virtue'),
            ),
    	array(
            'id'=>'topbar_bg_color',
            'type' => 'color',
            'customizer' => true,
            'title' => __('Topbar Background Color', 'virtue'), 
            'default' => '',
            'validate' => 'color',
            ),
    	array(
            'id'=>'bg_topbar_bg_img',
            'type' => 'media', 
            'url'=> true,
            'customizer' => true,
            'title' => __('Upload background image or texture', 'virtue'),
            ), 
    	array(
            'id'=>'topbar_bg_repeat',
            'type' => 'select',
            'customizer' => true,
            'title' => __('Image repeat options', 'virtue'), 
            'options' => array('no-repeat' => 'no-repeat', 'repeat' => 'repeat', 'repeat-x' => 'repeat-x', 'repeat-y' => 'repeat-y'),
            'width' => 'width:60%',
            ),
    	array(
            'id'=>'topbar_bg_placementx',
            'type' => 'select',
            'customizer' => true,
            'title' => __('X image placement options', 'virtue'), 
            'options' => array('left' => 'left', 'center' => 'center', 'right' => 'right'),
            'width' => 'width:60%',
            ),
    	array(
            'id'=>'topbar_bg_placementy',
            'type' => 'select',
            'customizer' => true,
            'title' => __('Y image placement options', 'virtue'), 
            'options' => array('top' => 'top', 'center' => 'center', 'bottom' => 'bottom',),
            'width' => 'width:60%',
            ),
    	array(
            'id'=>'info_header_background',
            'type' => 'info',
            'customizer' => true,
            'desc' => __('Header Background', 'virtue'),
            ),
    	array(
            'id'=>'header_bg_color',
            'type' => 'color',
            'title' => __('Header Background Color', 'virtue'), 
            'default' => '',
            'validate' => 'color',
            'customizer' => true,
            ),
    	array(
            'id'=>'bg_header_bg_img',
            'type' => 'media', 
            'url'=> true,
            'customizer' => true,
            'title' => __('Upload background image or texture', 'virtue'),
            ), 
    	array(
            'id'=>'header_bg_repeat',
            'type' => 'select',
            'customizer' => true,
            'title' => __('Image repeat options', 'virtue'), 
            'options' => array('no-repeat' => 'no-repeat', 'repeat' => 'repeat', 'repeat-x' => 'repeat-x', 'repeat-y' => 'repeat-y'),
            'width' => 'width:60%',
            ),
    	array(
            'id'=>'header_bg_placementx',
            'type' => 'select',
            'customizer' => true,
            'title' => __('X image placement options', 'virtue'), 
            'options' => array('left' => 'left', 'center' => 'center', 'right' => 'right'),
            'width' => 'width:60%',
            ),
    	array(
            'id'=>'header_bg_placementy',
            'type' => 'select',
            'customizer' => true,
            'title' => __('Y image placement options', 'virtue'), 
            'options' => array('top' => 'top', 'center' => 'center', 'bottom' => 'bottom',),
            'width' => 'width:60%',
            ),
    	array(
            'id'=>'info_menu_background',
            'type' => 'info',
            'customizer' => true,
            'desc' => __('Secondary Menu Background', 'virtue'),
            ),
    	array(
            'id'=>'menu_bg_color',
            'type' => 'color',
            'title' => __('Secondary menu Background Color', 'virtue'), 
            'default' => '',
            'validate' => 'color',
            'customizer' => true,
            ),
    	array(
            'id'=>'bg_menu_bg_img',
            'type' => 'media', 
            'customizer' => true,
            'url'=> true,
            'title' => __('Upload background image or texture', 'virtue'),
            ), 
    	array(
            'id'=>'menu_bg_repeat',
            'type' => 'select',
            'customizer' => true,
            'title' => __('Image repeat options', 'virtue'), 
            'options' => array('no-repeat' => 'no-repeat', 'repeat' => 'repeat', 'repeat-x' => 'repeat-x', 'repeat-y' => 'repeat-y'),
            'width' => 'width:60%',
            ),
    	array(
            'id'=>'menu_bg_placementx',
            'type' => 'select',
            'customizer' => true,
            'title' => __('X image placement options', 'virtue'), 
            'options' => array('left' => 'left', 'center' => 'center', 'right' => 'right'),
            'width' => 'width:60%',
            ),
    	array(
            'id'=>'menu_bg_placementy',
            'type' => 'select',
            'customizer' => true,
            'title' => __('Y image placement options', 'virtue'), 
            'options' => array('top' => 'top', 'center' => 'center', 'bottom' => 'bottom',),
            'width' => 'width:60%',
            ),
    	array(
            'id'=>'info_mobile_background',
            'type' => 'info',
            'customizer' => true,
            'desc' => __('Mobile Menu Background', 'virtue'),
            ),
    	array(
            'id'=>'mobile_bg_color',
            'type' => 'color',
            'title' => __('Mobile Background Color', 'virtue'), 
            'default' => '',
            'customizer' => true,
            'validate' => 'color',
            ),
    	array(
            'id'=>'bg_mobile_bg_img',
            'type' => 'media', 
            'url'=> true,
            'customizer' => true,
            'title' => __('Upload background image or texture', 'virtue'),
            ), 
    	array(
            'id'=>'mobile_bg_repeat',
            'type' => 'select',
            'customizer' => true,
            'title' => __('Image repeat options', 'virtue'), 
            'options' => array('no-repeat' => 'no-repeat', 'repeat' => 'repeat', 'repeat-x' => 'repeat-x', 'repeat-y' => 'repeat-y'),
            'width' => 'width:60%',
            ),
    	array(
            'id'=>'mobile_bg_placementx',
            'type' => 'select',
            'customizer' => true,
            'title' => __('X image placement options', 'virtue'), 
            'options' => array('left' => 'left', 'center' => 'center', 'right' => 'right'),
            'width' => 'width:60%',
            ),
    	array(
            'id'=>'mobile_bg_placementy',
            'type' => 'select',
            'customizer' => true,
            'title' => __('Y image placement options', 'virtue'), 
            'options' => array('top' => 'top', 'center' => 'center', 'bottom' => 'bottom',),
            'width' => 'width:60%',
            ),
    	array(
            'id'=>'info_footer_background',
            'type' => 'info',
            'customizer' => true,
            'desc' => __('Footer Background', 'virtue'),
            ),
    	array(
            'id'=>'footer_bg_color',
            'type' => 'color',
            'title' => __('Footer Background Color', 'virtue'), 
            'default' => '',
            'customizer' => true,
            'validate' => 'color',
            ),
    	array(
            'id'=>'bg_footer_bg_img',
            'type' => 'media', 
            'url'=> true,
            'customizer' => true,
            'title' => __('Upload background image or texture', 'virtue'),
            ), 
    	array(
            'id'=>'footer_bg_repeat',
            'type' => 'select',
            'customizer' => true,
            'title' => __('Image repeat options', 'virtue'), 
            'options' => array('no-repeat' => 'no-repeat', 'repeat' => 'repeat', 'repeat-x' => 'repeat-x', 'repeat-y' => 'repeat-y'),
            'width' => 'width:60%',
            ),
    	array(
            'id'=>'footer_bg_placementx',
            'type' => 'select',
            'customizer' => true,
            'title' => __('X image placement options', 'virtue'), 
            'options' => array('left' => 'left', 'center' => 'center', 'right' => 'right'),
            'width' => 'width:60%',
            ),
    	array(
            'id'=>'footer_bg_placementy',
            'type' => 'select',
            'customizer' => true,
            'title' => __('Y image placement options', 'virtue'), 
            'options' => array('top' => 'top', 'center' => 'center', 'bottom' => 'bottom',),
            'width' => 'width:60%',
            ),
    	array(
            'id'=>'info_body_background',
            'type' => 'info',
            'customizer' => true,
            'desc' => __('Body Background', 'virtue'),
            ),
    	array(
            'id'=>'boxed_bg_color',
            'type' => 'color',
            'title' => __('Body Background Color', 'virtue'), 
            'default' => '',
            'customizer' => true,
            'validate' => 'color',
            ),
    	array(
            'id'=>'bg_boxed_bg_img',
            'type' => 'media', 
            'url'=> true,
            'customizer' => true,
            'title' => __('Upload background image or texture', 'virtue'),
            ), 
    	array(
            'id'=>'boxed_bg_repeat',
            'type' => 'select',
            'customizer' => true,
            'title' => __('Image repeat options', 'virtue'), 
            'options' => array('no-repeat' => 'no-repeat', 'repeat' => 'repeat', 'repeat-x' => 'repeat-x', 'repeat-y' => 'repeat-y'),
            'width' => 'width:60%',
            ),
    	array(
            'id'=>'boxed_bg_placementx',
            'type' => 'select',
            'customizer' => true,
            'title' => __('X image placement options', 'virtue'), 
            'options' => array('left' => 'left', 'center' => 'center', 'right' => 'right'),
            'width' => 'width:60%',
            ),
    	array(
            'id'=>'boxed_bg_placementy',
            'type' => 'select',
            'customizer' => true,
            'title' => __('Y image placement options', 'virtue'), 
            'options' => array('top' => 'top', 'center' => 'center', 'bottom' => 'bottom',),
            'width' => 'width:60%',
            ),
    	array(
            'id'=>'boxed_bg_fixed',
            'type' => 'select',
            'customizer' => true,
            'title' => __('Fixed or Scroll', 'virtue'), 
            'options' => array('fixed' => 'Fixed', 'scroll'=>'Scroll'),
            'width' => 'width:60%',
            ),
        ),
    )
);
Redux::setSection( $opt_name, array(
    'icon' => 'icon-text-width',
    'id' => 'typography',
    'icon_class' => 'icon-large',
    'title' => __('Typography', 'virtue'),
    'desc' => "<div class='redux-info-field'><h3>".__('Header Font Options', 'virtue')."</h3></div>",
    'fields' => array(
        array(
            'id'=>'info_typography_settings_notice',
            'type' => 'info',
            'customizer' => true,
            'desc' => __('*NOTE: Make sure Virtue/Pinnacle Toolkit plugin is activated* <br>Go to Apperance > Theme Options > Typography settings for all Typography settings', 'virtue'),
            ),
    	array(
            'id'=>'font_h1',
            'type' => 'typography', 
            'title' => __('H1 Headings', 'virtue'),
            //'compiler'=>true, // Use if you want to hook in your own CSS compiler
            'font-family'=>true, 
            'google'=>true, // Disable google fonts. Won't work if you haven't defined your google api key
            'font-backup'=>false, // Select a backup non-google font in addition to a google font
            'font-style'=>true, // Includes font-style and weight. Can use font-style or font-weight to declare
            'subsets'=>true, // Only appears if google is true and subsets not set to false
            'font-size'=>true,
            'line-height'=>true,
            'text-align' => false,
            'customizer' => false,
            //'word-spacing'=>false, // Defaults to false
            //'all_styles' => true,
            'color'=>true,
            'preview'=>true, // Disable the previewer
            'output' => array('h1'),
            'subtitle'=> __("Choose Size and Style for h1 (This Styles Your Page Titles)", 'virtue'),
            'default'=> array(
                'font-family'=>'Lato',
                'color'=>"", 
                'font-style'=>'400',
                'font-size'=>'38px', 
                'line-height'=>'40px', ),
            ),
		array(
            'id'=>'font_h2',
            'type' => 'typography', 
            'title' => __('H2 Headings', 'virtue'),
            //'compiler'=>true, // Use if you want to hook in your own CSS compiler
              'font-family'=>true, 
            'google'=>true, // Disable google fonts. Won't work if you haven't defined your google api key
            'font-backup'=>false, // Select a backup non-google font in addition to a google font
            'font-style'=>true, // Includes font-style and weight. Can use font-style or font-weight to declare
            'subsets'=>true, // Only appears if google is true and subsets not set to false
            'font-size'=>true,
            'line-height'=>true,
            'customizer' => false,
            'text-align' => false,
            //'word-spacing'=>false, // Defaults to false
            //'all_styles' => true,
            'color'=>true,
            'preview'=>true, // Disable the previewer
            'output' => array('h2'),
            'subtitle'=> __("Choose Size and Style for h2", 'virtue'),
            'default'=> array(
                'font-family'=>'Lato',
                'color'=>"", 
                'font-style'=>'normal',
                'font-size'=>'32px', 
                'line-height'=>'40px', ),
            ),
		array(
            'id'=>'font_h3',
            'type' => 'typography', 
            'title' => __('H3 Headings', 'virtue'),
            //'compiler'=>true, // Use if you want to hook in your own CSS compiler
            'font-family'=>true, 
            'google'=>true, // Disable google fonts. Won't work if you haven't defined your google api key
            'font-backup'=>false, // Select a backup non-google font in addition to a google font
            'font-style'=>true, // Includes font-style and weight. Can use font-style or font-weight to declare
            'subsets'=>true, // Only appears if google is true and subsets not set to false
            'font-size'=>true,
            'customizer' => false,
            'line-height'=>true,
            'text-align' => false,
            //'word-spacing'=>false, // Defaults to false
            //'all_styles' => true,
            'color'=>true,
            'preview'=>true, // Disable the previewer
            'output' => array('h3'),
            'subtitle'=> __("Choose Size and Style for h3", 'virtue'),
            'default'=> array(
                'font-family'=>'Lato',
                'color'=>"", 
                'font-style'=>'400',
                'font-size'=>'28px', 
                'line-height'=>'40px', ),
            ),
		array(
            'id'=>'font_h4',
            'type' => 'typography', 
            'title' => __('H4 Headings', 'virtue'),
            //'compiler'=>true, // Use if you want to hook in your own CSS compiler
            'font-family'=>true, 
            'google'=>true, // Disable google fonts. Won't work if you haven't defined your google api key
            'font-backup'=>false, // Select a backup non-google font in addition to a google font
            'font-style'=>true, // Includes font-style and weight. Can use font-style or font-weight to declare
            'subsets'=>true, // Only appears if google is true and subsets not set to false
            'font-size'=>true,
            'line-height'=>true,
            'customizer' => false,
            'text-align' => false,
            //'word-spacing'=>false, // Defaults to false
            //'all_styles' => true,
            'color'=>true,
            'preview'=>true, // Disable the previewer
            'output' => array('h4'),
            'subtitle'=> __("Choose Size and Style for h4", 'virtue'),
            'default'=> array(
                'font-family'=>'Lato',
                'color'=>"", 
                'font-style'=>'400',
                'font-size'=>'24px', 
                'line-height'=>'40px', ),
            ),
		array(
            'id'=>'font_h5',
            'type' => 'typography', 
            'title' => __('H5 Headings', 'virtue'),
            //'compiler'=>true, // Use if you want to hook in your own CSS compiler
            'font-family'=>true, 
            'google'=>true, // Disable google fonts. Won't work if you haven't defined your google api key
            'font-backup'=>false, // Select a backup non-google font in addition to a google font
            'font-style'=>true, // Includes font-style and weight. Can use font-style or font-weight to declare
            'subsets'=>true, // Only appears if google is true and subsets not set to false
            'font-size'=>true,
            'customizer' => false,
            'line-height'=>true,
            'text-align' => false,
            //'word-spacing'=>false, // Defaults to false
            //'all_styles' => true,
            'color'=>true,
            'preview'=>true, // Disable the previewer
            'output' => array('h5'),
            'subtitle'=> __("Choose Size and Style for h5", 'virtue'),
            'default'=> array(
                'font-family'=>'Lato',
                'color'=>"", 
                'font-style'=>'700',
                'font-size'=>'18px', 
                'line-height'=>'24px', ),
            ),
		array(
            'id'=>'info_body_font',
            'type' => 'info',
            'customizer' => false,
            'desc' => __('Body Font Options', 'virtue'),
            ),
		array(
            'id'=>'font_p',
            'type' => 'typography', 
            'title' => __('Body Font', 'virtue'),
            //'compiler'=>true, // Use if you want to hook in your own CSS compiler
            'font-family'=>true, 
            'google'=>true, // Disable google fonts. Won't work if you haven't defined your google api key
            'font-backup'=>false, // Select a backup non-google font in addition to a google font
            'font-style'=>true, // Includes font-style and weight. Can use font-style or font-weight to declare
            'subsets'=>true, // Only appears if google is true and subsets not set to false
            'font-size'=>true,
            'customizer' => false,
            'line-height'=>true,
            'text-align' => false,
            //'word-spacing'=>false, // Defaults to false
            'all_styles' => true,
            'color'=>true,
            'preview'=>true, // Disable the previewer
            'output' => array('body'),
            'subtitle'=> __("Choose Size and Style for paragraphs", 'virtue'),
            'default'=> array(
                'font-family'=>'Verdana, Geneva, sans-serif',
                'color'=>"", 
                'font-style'=>'400',
                'font-size'=>'14px', 
                'line-height'=>'20px', ),
            ),
        ),
	)
);
Redux::setSection( $opt_name, array(
    'icon' => 'icon-reorder',
    'icon_class' => 'icon-large',
    'id' => 'menu_settings',
    'title' => __('Menu Settings', 'virtue'),
    'desc' => "<div class='redux-info-field'><h3>".__('Primary Menu Options', 'virtue')."</h3></div>",
    'fields' => array(
        array(
            'id'=>'info_menu_settings_notice',
            'type' => 'info',
            'customizer' => true,
            'desc' => __('*NOTE: Make sure Virtue/Pinnacle Toolkit plugin is activated* <br>Go to Apperance > Theme Options > Menu settings for all menu settings', 'virtue'),
            ),
    	array(
            'id'=>'font_primary_menu',
            'type' => 'typography', 
            'customizer' => false,
            'title' => __('Primary Menu Font', 'virtue'),
            //'compiler'=>true, // Use if you want to hook in your own CSS compiler
            'font-family'=>true, 
            'google'=>true, // Disable google fonts. Won't work if you haven't defined your google api key
            'font-backup'=>false, // Select a backup non-google font in addition to a google font
            'font-style'=>true, // Includes font-style and weight. Can use font-style or font-weight to declare
            'subsets'=>true, // Only appears if google is true and subsets not set to false
            'font-size'=>true,
            'line-height'=>true,
            'text-align' => false,
            //'word-spacing'=>false, // Defaults to false
            //'all_styles' => true,
            'color'=>true,
            'preview'=>true, // Disable the previewer
            'output' => array('#nav-main ul.sf-menu a'),
            'subtitle'=> __("Choose Size and Style for primary menu", 'virtue'),
            'default'=> array(
                'font-family'=>'Lato',
                'color'=>"", 
                'font-style'=>'400',
                'font-size'=>'12px', 
                'line-height'=>'18px', ),
            ),
		array(
            'id'=>'info_menu_secondary_font',
            'type' => 'info',
            'customizer' => false,
            'desc' => __('Secondary Menu Options', 'virtue'),
            ),
		array(
            'id'=>'font_secondary_menu',
            'type' => 'typography', 
            'customizer' => false,
            'title' => __('Secondary Menu Font', 'virtue'),
            //'compiler'=>true, // Use if you want to hook in your own CSS compiler
            'font-family'=>true, 
            'google'=>true, // Disable google fonts. Won't work if you haven't defined your google api key
            'font-backup'=>false, // Select a backup non-google font in addition to a google font
            'font-style'=>true, // Includes font-style and weight. Can use font-style or font-weight to declare
            'subsets'=>true, // Only appears if google is true and subsets not set to false
            'font-size'=>true,
            'line-height'=>true,
            'text-align' => false,
            //'word-spacing'=>false, // Defaults to false
            //'all_styles' => true,
            'color'=>true,
            'preview'=>true, // Disable the previewer
            'output' => array('#nav-second ul.sf-menu a'),
            'subtitle'=> __("Choose Size and Style for secondary menu", 'virtue'),
            'default'=> array(
                'font-family'=>'Lato',
                'color'=>"", 
                'font-style'=>'400',
                'font-size'=>'18px', 
                'line-height'=>'22px', ),
            ),
		array(
            'id'=>'info_menu_mobile_font',
            'type' => 'info',
            'customizer' => false,
            'desc' => __('Mobile Menu Options', 'virtue'),
            ),
        array(
            'id'=>'mobile_submenu_collapse',
            'type' => 'switch', 
            'customizer' => false,
            'title' => __('Submenu items collapse until opened', 'virtue'),
            "default" => 0,
            ),
		array(
            'id'=>'font_mobile_menu',
            'customizer' => false,
            'type' => 'typography', 
            'title' => __('Mobile Menu Font', 'virtue'),
            //'compiler'=>true, // Use if you want to hook in your own CSS compiler
            'font-family'=>true, 
            'google'=>true, // Disable google fonts. Won't work if you haven't defined your google api key
            'font-backup'=>false, // Select a backup non-google font in addition to a google font
            'font-style'=>true, // Includes font-style and weight. Can use font-style or font-weight to declare
            'subsets'=>true, // Only appears if google is true and subsets not set to false
            'font-size'=>true,
            'line-height'=>true,
            'text-align' => false,
            //'word-spacing'=>false, // Defaults to false
            //'all_styles' => true,
            'color'=>true,
            'preview'=>true, // Disable the previewer
            'output' => array('.kad-nav-inner .kad-mnav, .kad-mobile-nav .kad-nav-inner li a', '.nav-trigger-case'),
            'subtitle'=> __("Choose Size and Style for Mobile Menu", 'virtue'),
            'default'=> array(
                'font-family'=>'Lato',
                'color'=>"", 
                'font-style'=>'400',
                'font-size'=>'16px', 
                'line-height'=>'20px', ),
            ),
		),
    )
);
Redux::setSection( $opt_name, array(
    'icon' => 'icon-list-alt',
    'icon_class' => 'icon-large',
    'id' => 'pagepost_settings',
    'title' => __('Page/Post Settings', 'virtue'),
    'desc' => "<div class='redux-info-field'><h3>".__('Page and Post Comment Settings', 'virtue')."</h3></div>",
    'fields' => array(
		array(
			'id'         => 'paragraph_margin_bottom',
			'type'       => 'slider',
			'title'      => __( 'Paragraph bottom spacing', 'virtue' ),
			'default'    => '16',
			'min'        => '0',
			'customizer' => true,
			'step'       => '1',
			'max'        => '60',
		),
		array(
			'id'       => 'page_title_show',
			'type'     => 'switch', 
			'title'    => __( 'Show page title by default', 'virtue' ),
			'subtitle' => __( 'Turn off to hide page titles by default', 'virtue' ),
			'default'  => 1,
		),
		array(
			'id'      => 'default_page_content_width',
			'type'    => 'select',
			'title'   => __( 'Single Page Content Width Default', 'virtue' ),
			'options' => array(
				'contained' => __( 'Contained', 'virtue' ),
				'full'      => __( 'Fullwidth', 'virtue' ),
			),
			'width'   => 'width:60%',
			'default' => 'contained',
		),
		array(
			'id'      => 'default_page_show_sidebar',
			'type'    => 'select',
			'title'   => __( 'Default Page Template - Sidebar Default', 'virtue' ),
			'options' => array(
				'yes' => __('Yes sidebar', 'virtue'),
				'no'  => __('No sidebar', 'virtue'),
			),
			'width'   => 'width:60%',
			'default' => 'yes',
		),
        array(
            'id'=>'close_comments',
            'type' => 'switch',
            'customizer' => true, 
            'title' => __('Show Comments Closed Text?', 'virtue'),
            'subtitle' => __('Choose to show or hide comments closed alert below posts.', 'virtue'),
            "default" => 0,
            ),
        array(
            'id'=>'page_comments',
            'type' => 'switch',
            'customizer' => true,
            'title' => __('Allow Comments on Pages', 'virtue'),
            'subtitle' => __('Turn on to allow comments on pages', 'virtue'),
            "default" => 0,
            ),
        array(
            'id'=>'portfolio_comments',
            'type' => 'switch',
            'customizer' => true,
            'title' => __('Allow Comments on Portfolio Posts', 'virtue'),
            'subtitle' => __('Turn on to allow comments on Portfolio posts', 'virtue'),
            "default" => 0,
            ),
        array(
            'id'=>'info_portfolio_post_defaults',
            'type' => 'info',
            'customizer' => true,
            'desc' => __('Portfolio Post', 'virtue'),
            ),
        array(
            'id'=>'portfolio_link',
            'type' => 'select',
            'data' => 'pages',
            'customizer' => true,
            'width' => 'width:60%',
            'title' => __('All Projects Portfolio Page', 'virtue'), 
            'subtitle' => __('This sets the link in every single portfolio page. *note: You still have to set the page template to portfolio.', 'virtue'),
            ),
        array(
            'id'=>'info_portfolio_typepage_defaults',
            'type' => 'info',
            'customizer' => true,
            'desc' => __('Portfolio Type Page Defaults', 'virtue'),
            ),
        array(
            'id'=>'portfolio_type_columns',
            'type' => 'select',
            'customizer' => true,
            'title' => __('Portfolio Type - Post Columns', 'virtue'), 
            'subtitle' => __("Choose how many columns for portfolio type pages", 'virtue'),
            'options' => array('3' => 'Three Columns','4' => 'Four Column', '5' => 'Five Column'),
            'default' => '3',
            'width' => 'width:60%',
            ),
        array(
            'id'=>'portfolio_type_under_title',
            'type' => 'switch',
            'customizer' => true,
            'title' => __('Show Types under Title', 'virtue'),
            'subtitle' => __('Choose to show or hide portfolio type under title.', 'virtue'),
            "default" => 1,
            ),
        array(
            'id'=>'info_blog_defaults',
            'type' => 'info',
            'customizer' => true,
            'desc' => __('Blog Post Defaults', 'virtue'),
            ),
        array(
            'id'=>'post_summery_default',
            'type' => 'select',
            'customizer' => true,
            'title' => __('Blog Post Summary Default', 'virtue'), 
            'options' => array('text' => 'Text', 'img_portrait' => 'Portrait Image', 'img_landscape' => 'Landscape Image', 'slider_portrait' => 'Portrait Image Slider' , 'slider_landscape' => 'Landscape Image Slider'),
            'width' => 'width:60%',
            'default' => 'img_portrait',
            ),
         array(
            'id'=>'post_summery_default_image',
            'type' => 'media', 
            'customizer' => true,
            'url'=> true,
            'title' => __('Default post summary feature Image', 'virtue'),
            'subtitle' => __('Replace theme default feature image for posts without a featured image', 'virtue'),
            ),
        array(
            'id'=>'post_head_default',
            'type' => 'select',
            'customizer' => true,
            'title' => __('Blog Post Head Content Default', 'virtue'), 
            'options' => array('none' => __('None', 'virtue'), 'flex' => __('Image Slider', 'virtue'), 'image' => __('Image', 'virtue'), 'video' => __('Video', 'virtue')),
            'width' => 'width:60%',
            'default' => 'none',
            ),
        array(
            'id'=>'show_postlinks',
            'type' => 'switch', 
            'customizer' => true,
            'title' => __('Show Previous and Next posts links?', 'virtue'),
            'subtitle' => __('Choose to show or hide previous and next post links in the footer of a single post.', 'virtue'),
            "default" => 0,
            ),
        array(
            'id'=>'hide_author',
            'type' => 'switch', 
            'customizer' => true,
            'title' => __('Show Author Icon with posts?', 'virtue'),
            'subtitle' => __('Choose to show or hide author icon under post title.', 'virtue'),
            "default" => 1,
            ),
        array(
            'id'=>'post_author_default',
            'type' => 'select',
            'customizer' => true,
            'title' => __('Blog Post Author Box Default', 'virtue'), 
            'options' => array('no' => __('No, Do not Show', 'virtue'), 'yes' => __('Yes, Show', 'virtue')),
            'width' => 'width:60%',
            'default' => 'no',
            ),
        array(
            'id'=>'post_carousel_default',
            'type' => 'select',
            'customizer' => true,
            'title' => __('Blog Post Bottom Carousel Default', 'virtue'), 
            'options' => array('no' => __('No, Do not Show', 'virtue'), 'recent' => __('Yes - Display Recent Posts', 'virtue'), 'similar' => __('Yes - Display Similar Posts', 'virtue')),
            'width' => 'width:60%',
            'default' => 'no',
            ),
        array(
            'id'=>'info_blog_category',
            'type' => 'info',
            'customizer' => true,
            'desc' => __('Blog Category/Archive Defaults', 'virtue'),
            ),
        array(
            'id'=>'blog_archive_full',
            'type' => 'select',
            'customizer' => true,
            'title' => __('Blog Archive', 'virtue'), 
            'subtitle' => __("Choose to show full post or post excerpt.", 'virtue'),
            'options' => array('summery' => 'Post Excerpt','full' => 'Full'),
            'default' => 'summery',
            'width' => 'width:60%',
            ),
        ),
    )
);
Redux::setSection( $opt_name, array(
    'icon' => 'icon-wrench',
    'icon_class' => 'icon-large',
    'id' => 'misc_settings',
    'title' => __('Misc Settings', 'virtue'),
    'desc' => "<div class='redux-info-field'><h3>".__('Misc Settings', 'virtue')."</h3></div>",
    'fields' => array(
    	array(
            'id'=>'hide_image_border',
            'type' => 'switch', 
            'customizer' => true,
            'title' => __('Hide Image Border', 'virtue'),
            'subtitle' => __('Choose to show or hide image border for images added in pages or posts', 'virtue'),
            "default" => 0,
            ),
        array(
            'id'=>'contact_email',
            'type' => 'text',
            'customizer' => true,
            'title' => __('Contact Form Email', 'virtue'),
            'subtitle' => __('Sets the email for the contact page email form.', 'virtue'),
            'default' => 'test@test.com'
            ),
        array(
            'id'=>'footer_text',
            'customizer' => true,
            'type' => 'textarea',
            'title' => __('Footer Copyright Text', 'virtue'), 
            'subtitle' => __('Write your own copyright text here. You can use the following shortcodes in your footer text: [copyright] [site-name] [the-year]', 'virtue'),
            'default' => '[copyright] [the-year] [site-name] [theme-credit]',
            ),
        array(
            'id'=>'info_sidebars',
            'type' => 'info',
            'customizer' => true,
            'desc' => __('Create Sidebars', 'virtue'),
            ),
        array(
            'id'=>'cust_sidebars',
            'type' => 'multi_text',
            'customizer' => true,
            'title' => __('Create Custom Sidebars', 'virtue'),
            'subtitle' => __('Type new sidebar name into textbox', 'virtue'),
            'default' =>__('Extra Sidebar', 'virtue'),
            ),
        array(
            'id'=>'info_wpgallerys',
            'type' => 'info',
            'customizer' => true,
            'desc' => __('Wordpress Galleries', 'virtue'),
            ),
        array(
            'id'=>'virtue_gallery',
            'type' => 'switch', 
            'customizer' => true,
            'title' => __('Enable Virtue Galleries to override Wordpress', 'virtue'),
            'subtitle' => __('Disable this if using a plugin to customize galleries, for example jetpack tiled gallery.', 'virtue'),
            "default" => 1,
            ),
        array(
            'id'=>'info_lightbox',
            'type' => 'info',
            'customizer' => true,
            'desc' => __('Theme Lightbox', 'virtue'),
            ),
        array(
            'id'=>'kadence_lightbox',
            'type' => 'switch', 
            'on' => __('Lightbox Off', 'virtue'),
            'off' => __('Lightbox On', 'virtue'),
            'customizer' => true,
            'title' => __('Turn Off Theme Lightbox?', 'virtue'),
            "default" => 0,
            ),
        array(
            'id'=>'info_gmaps',
            'type' => 'info',
            'desc' => __('Theme Google Maps', 'virtue'),
            ),
        array(
            'id'=>'google_map_api',
            'type' => 'text',
            'title' => __('Google Map API', 'virtue'),
            'subtitle' => __('For best performance add your own API for google maps.', 'virtue'),
            'description' =>'<a target="_blank" href="https://developers.google.com/maps/documentation/javascript/get-api-key">Get an API code Here</a>',
            'default' => ''
            ),  
        ),
    )
);
Redux::setSection( $opt_name, array(
    'icon' => 'icon-code',
    'icon_class' => 'icon-large',
    'id' => 'advanced_settings',
    'title' => __('Advanced Settings', 'virtue'),
    'desc' => "<div class='redux-info-field'><h3>".__('Custom CSS Box', 'virtue')."</h3></div>",
    'fields' => array(
             array(
            'id'=>'custom_css',
            'type' => 'textarea',
            'customizer' => true,
            'title' => __('Custom CSS', 'virtue'), 
            'subtitle' => __('Quickly add some CSS to your theme by adding it to this block.', 'virtue'),
            //'validate' => 'css',
            ),
        ),
    )
);
Redux::setSection( $opt_name, array(
    'id' => 'inportexport_settings',
                    'title'  => __( 'Import / Export', 'virtue' ),
                    'desc'   => __( 'Import and Export your Theme Options from text or URL.', 'virtue' ),
                    'icon'   => 'icon-large icon-hdd',
                    'fields' => array(
                        array(
                            'id'         => 'opt-import-export',
                            'type'       => 'import_export',
                            'title'      => '',
                            'customizer' => false,
                            'subtitle'   => '',
                            'full_width' => true,
                        ),
                    ),
                ) );

   /*
    * <--- END SECTIONS
    */



function virtue_override_panel() {
    wp_dequeue_style( 'redux-admin-css' );
    wp_register_style('virtue-redux-custom-css', get_template_directory_uri() . '/themeoptions/options/css/style.css', false, 258);    
    wp_enqueue_style( 'virtue-redux-custom-css' );
    wp_dequeue_style( 'select2-css' );
    wp_dequeue_style( 'redux-elusive-icon' );
    wp_dequeue_style( 'redux-elusive-icon-ie7' );
}
// This example assumes your opt_name is set to redux_demo, replace with your opt_name value
add_action('redux-enqueue-virtue', 'virtue_override_panel');

function virtue_remove_demo() {

        // Used to hide the demo mode link from the plugin page. Only used when Redux is a plugin.
        if ( class_exists( 'ReduxFrameworkPlugin' ) ) {
            remove_filter( 'plugin_row_meta', array(
                ReduxFrameworkPlugin::instance(),
                'plugin_metalinks'
            ), null, 2 );

            // Used to hide the activation notice informing users of the demo panel. Only used when Redux is a plugin.
            remove_action( 'admin_notices', array( ReduxFrameworkPlugin::instance(), 'admin_notices' ) );
        }
    }

