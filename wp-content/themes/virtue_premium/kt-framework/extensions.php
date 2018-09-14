<?php 
add_action( "after_setup_theme", 'kt_remove_sections', 1);
function kt_remove_sections() {
    if ( ! class_exists( 'Redux' ) ) {
                return;
            }
    if(ReduxFramework::$_version <= '3.5.6') {
        return;
        }
    $options_slug = get_option(OPTIONS_SLUG);
    $activation = get_option('kt_api_manager');
    if(!empty($activation['kt_api_key'])) {
      $license = substr($activation['kt_api_key'], 0, 3);
    } else {
      $license = '';
    }
    if( !class_exists('RevSlider') && is_admin() )  {
       // Home Slider
       $homeslider = Redux::getField(OPTIONS_SLUG, 'choose_slider');
       if(is_array($homeslider)){
            unset($homeslider['options']['rev']);
        }
       Redux::setField(OPTIONS_SLUG, $homeslider);
       //Mobile Slider
       $mobileslider = Redux::getField(OPTIONS_SLUG, 'choose_mobile_slider');
       if(is_array($mobileslider)){
            unset($mobileslider['options']['rev']);
        }
       Redux::setField(OPTIONS_SLUG, $mobileslider);
       // Shop Slider
       $shopslider = Redux::getField(OPTIONS_SLUG, 'choose_shop_slider');
       if(is_array($shopslider)){
        unset($shopslider['options']['rev']);
        }
       Redux::setField(OPTIONS_SLUG, $shopslider);
       Redux::removefield(OPTIONS_SLUG, 'mobile_rev_slider');
       Redux::removefield(OPTIONS_SLUG, 'shop_rev_slider');


		function virtue_update_rev_fields_properties() {
			// Retrieve a CMB2 instance
			$cmb = cmb2_get_metabox( 'pagefeature_metabox' );
			if(is_object($cmb) ){
				$field_id = $cmb->update_field_property( '_kad_page_head', 'options', array(
					'flex' 			=> __('Image Slider - (Cropped Image Ratio)', 'virtue'),
					'carousel' 		=> __('Image Slider - (Different Image Ratio)', 'virtue'),
					'imgcarousel' 	=> __('Image Carousel - (Multiple Images Showing At Once)', 'virtue'),
					'ktslider' 		=> __('Kadence Slider', 'virtue'), 
					'cyclone' 		=> __('Shortcode Slider', 'virtue'), 
					'video' 		=> __('Video', 'virtue'),
					'image' 		=> __('Image', 'virtue'),
				));
				$cmb->remove_field( '_kad_post_rev' );
			}
			$pcmb = cmb2_get_metabox( 'portfolio_post_metabox' );
			if(is_object($pcmb) ){
				$field_id = $pcmb->update_field_property( '_kad_ppost_type', 'options', array(
					'image' => __('Image', 'virtue'), 
					'flex' => __('Image Slider - (Cropped Image Ratio)', 'virtue'),
					'carousel' => __('Image Slider - (Different Image Ratio)', 'virtue'),
					'imgcarousel' => __('Image Carousel  - (Muiltiple Images Showing At Once)', 'virtue'),
					'ktslider' => __('Kadence Slider', 'virtue'), 
					'cyclone' => __('Shortcode', 'virtue'), 
					'imagegrid' => __('Image Grid', 'virtue'),
					'imagelist' => __('Image List', 'virtue'),
					'imagelist2' => __('Image List Style 2', 'virtue'), 
					'video' => __('Video', 'virtue'), 
					'none' => __('None', 'virtue'), 
				));
			}

		}
		add_action( 'cmb2_init_before_hookup', 'virtue_update_rev_fields_properties' );

    }
    if($license == 'vps' || $license == 'ktm' )  {
        Redux::removefield(OPTIONS_SLUG, 'kt_revslider_notice');
    }
    if(isset($options_slug['kadence_woo_extension']) && $options_slug['kadence_woo_extension'] == '0') {
        //sections
        Redux::removeSection(OPTIONS_SLUG, 'shop_settings');
        Redux::removeSection(OPTIONS_SLUG, 'product_settings');

        //topbar
        Redux::removefield(OPTIONS_SLUG, 'show_cartcount');

        //home layout
        Redux::removefield(OPTIONS_SLUG, 'info_product_feat_settings');
        Redux::removefield(OPTIONS_SLUG, 'product_title');
        Redux::removefield(OPTIONS_SLUG, 'home_product_feat_column');
        Redux::removefield(OPTIONS_SLUG, 'home_product_count');
        Redux::removefield(OPTIONS_SLUG, 'home_product_feat_scroll');
        Redux::removefield(OPTIONS_SLUG, 'home_product_feat_speed');
        Redux::removefield(OPTIONS_SLUG, 'info_product_sale_settings');
        Redux::removefield(OPTIONS_SLUG, 'product_sale_title');
        Redux::removefield(OPTIONS_SLUG, 'home_product_sale_count');
        Redux::removefield(OPTIONS_SLUG, 'home_product_sale_scroll');
        Redux::removefield(OPTIONS_SLUG, 'home_product_sale_speed');
        Redux::removefield(OPTIONS_SLUG, 'info_product_best_settings');
        Redux::removefield(OPTIONS_SLUG, 'product_best_title');
        Redux::removefield(OPTIONS_SLUG, 'home_product_best_column');
        Redux::removefield(OPTIONS_SLUG, 'home_product_best_count');
        Redux::removefield(OPTIONS_SLUG, 'home_product_best_scroll');
        Redux::removefield(OPTIONS_SLUG, 'home_product_best_speed');

        // Breadcrumbs
        Redux::removefield(OPTIONS_SLUG, 'show_breadcrumbs_shop');
        Redux::removefield(OPTIONS_SLUG, 'show_breadcrumbs_product');
        Redux::removefield(OPTIONS_SLUG, 'shop_breadcrumbs');

        // Menu
        Redux::removefield(OPTIONS_SLUG, 'menu_cart');
        Redux::removefield(OPTIONS_SLUG, 'menu_search_woo');

        // language
        Redux::removefield(OPTIONS_SLUG, 'cart_placeholder_text');
        Redux::removefield(OPTIONS_SLUG, 'sold_placeholder_text');
        Redux::removefield(OPTIONS_SLUG, 'sale_placeholder_text');
        Redux::removefield(OPTIONS_SLUG, 'shop_filter_text');
        Redux::removefield(OPTIONS_SLUG, 'description_tab_text');
        Redux::removefield(OPTIONS_SLUG, 'description_header_text');
        Redux::removefield(OPTIONS_SLUG, 'additional_information_tab_text');
        Redux::removefield(OPTIONS_SLUG, 'additional_information_header_text');
        Redux::removefield(OPTIONS_SLUG, 'video_tab_text');
        Redux::removefield(OPTIONS_SLUG, 'video_title_text');
        Redux::removefield(OPTIONS_SLUG, 'reviews_tab_text');
        Redux::removefield(OPTIONS_SLUG, 'related_products_text');
        

        //update feild
        $field = Redux::getField(OPTIONS_SLUG, 'homepage_layout');
        unset($field['options']['disabled']['block_three']);
        unset($field['options']['disabled']['block_nine']);
        unset($field['options']['disabled']['block_ten']);
        Redux::setField(OPTIONS_SLUG,$field);

        add_filter('kadence_widget_carousel_types', 'kadence_unset_product_from_carousel');
        function kadence_unset_product_from_carousel($types) {
            unset($types['featured-products']);
            unset($types['sale-products']);
            unset($types['best-products']);
            unset($types['cat-products']);
            return $types;
        }
    }
    if(isset($options_slug['kadence_header_footer_extension']) && $options_slug['kadence_header_footer_extension'] == '1') {
        add_action( 'wp_head', 'kt_wp_head_script_output' );
        function kt_wp_head_script_output() {
            global $virtue_premium;
            if( isset( $virtue_premium[ 'kt_header_script' ] ) && !empty( $virtue_premium[ 'kt_header_script' ] ) ){
                echo $virtue_premium['kt_header_script'];
            }
        }
        add_action('wp_footer', 'kt_wp_fotoer_script_output');
        function kt_wp_fotoer_script_output() {
            global $virtue_premium;
            if(isset($virtue_premium['kt_footer_script']) && !empty($virtue_premium['kt_footer_script']) ){
                echo $virtue_premium['kt_footer_script'];
            }
        }
    } else {
        if(isset($options_slug['kadence_header_footer_extension'])) {
            Redux::removeSection(OPTIONS_SLUG, 'header_footer_scripts');
        }
    }
    if(isset($options_slug['kadence_portfolio_extension']) && $options_slug['kadence_portfolio_extension'] == '0') {
        Redux::removeSection(OPTIONS_SLUG, 'portfolio_options');

        Redux::removefield(OPTIONS_SLUG, 'info_portfolio_settings');
        Redux::removefield(OPTIONS_SLUG, 'portfolio_title');
        Redux::removefield(OPTIONS_SLUG, 'portfolio_type');
        Redux::removefield(OPTIONS_SLUG, 'home_portfolio_carousel_column');
        Redux::removefield(OPTIONS_SLUG, 'home_port_car_layoutstyle');
        Redux::removefield(OPTIONS_SLUG, 'home_port_car_hoverstyle');
        Redux::removefield(OPTIONS_SLUG, 'home_port_car_imageratio');
        Redux::removefield(OPTIONS_SLUG, 'home_portfolio_carousel_count');
        Redux::removefield(OPTIONS_SLUG, 'home_portfolio_carousel_speed');
        Redux::removefield(OPTIONS_SLUG, 'home_portfolio_carousel_scroll');
        Redux::removefield(OPTIONS_SLUG, 'home_portfolio_order');
        Redux::removefield(OPTIONS_SLUG, 'portfolio_car_fullwidth');
        Redux::removefield(OPTIONS_SLUG, 'portfolio_car_lightbox');
        Redux::removefield(OPTIONS_SLUG, 'portfolio_show_type');
        Redux::removefield(OPTIONS_SLUG, 'portfolio_show_excerpt');
        Redux::removefield(OPTIONS_SLUG, 'info_portfolio_full_settings');
        Redux::removefield(OPTIONS_SLUG, 'portfolio_full_title');
        Redux::removefield(OPTIONS_SLUG, 'portfolio_full_type');
        Redux::removefield(OPTIONS_SLUG, 'home_port_count');
        Redux::removefield(OPTIONS_SLUG, 'home_portfolio_full_order');
        Redux::removefield(OPTIONS_SLUG, 'home_port_columns');

        Redux::removefield(OPTIONS_SLUG, 'home_port_layoutstyle');
        Redux::removefield(OPTIONS_SLUG, 'home_port_hoverstyle');
        Redux::removefield(OPTIONS_SLUG, 'home_port_imageratio');
        Redux::removefield(OPTIONS_SLUG, 'portfolio_full_masonry');
        Redux::removefield(OPTIONS_SLUG, 'home_portfolio_full_height');
        Redux::removefield(OPTIONS_SLUG, 'portfolio_full_filter');
        Redux::removefield(OPTIONS_SLUG, 'portfolio_full_show_filter');
        Redux::removefield(OPTIONS_SLUG, 'portfolio_full_fullwidth');
        Redux::removefield(OPTIONS_SLUG, 'home_portfolio_lightbox');
        Redux::removefield(OPTIONS_SLUG, 'portfolio_full_show_type');
        Redux::removefield(OPTIONS_SLUG, 'portfolio_full_show_excerpt');
        Redux::removefield(OPTIONS_SLUG, 'portfolio_full_fullwidth');
        


        remove_action( 'init', 'virtue_portfolio_post_init', 1 );

        $field = Redux::getField(OPTIONS_SLUG, 'homepage_layout');
        unset($field['options']['disabled']['block_eight']);
        unset($field['options']['disabled']['block_six']);
        Redux::setField(OPTIONS_SLUG,$field);

        add_filter('kadence_widget_carousel_types', 'kadence_unset_portfolio_from_carousel');
        function kadence_unset_portfolio_from_carousel($types) {
            unset($types['portfolio']);
            return $types;
        }
        add_filter( 'kadence_portfolio_post_removal', 'kadence_portfolio_post_removal_function');
        function kadence_portfolio_post_removal_function() { 
            return false;
        }
    }
    if(isset($options_slug['kadence_staff_extension']) && $options_slug['kadence_staff_extension'] == '0') {
      remove_action( 'init', 'virtue_staff_post_init');
    }
    if(isset($options_slug['kadence_testimonial_extension']) && $options_slug['kadence_testimonial_extension'] == '0') {
      remove_action( 'init', 'virtue_testimonial_post_init');
        add_filter( 'kadence_testimonial_post_removal', 'kadence_testimonial_post_removal_function');
        function kadence_testimonial_post_removal_function() { 
            return false;
        }
        add_filter('kadence_shortcodes', 'kadence_unset_test_from_shortcode_popup');
        function kadence_unset_test_from_shortcode_popup($virtue_shortcodes) {
            unset($virtue_shortcodes['kad_testimonial_form']);
            return $virtue_shortcodes;
        }
    }
}


function kadence_portfolio_post_on() {
    return apply_filters( 'kadence_portfolio_post_removal', true );
}
function kadence_testimonial_post_on() {
    return apply_filters( 'kadence_testimonial_post_removal', true );
}