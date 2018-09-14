<?php
/**
 * Enqueue scripts and stylesheets
 *
 * @package Virtue Theme
 */

/**
 * Enqueue scripts and stylesheets
 */
function virtue_scripts() {
	global $virtue_premium;

	wp_enqueue_style( 'virtue_main', get_template_directory_uri() . '/assets/css/virtue.css', false, VIRTUE_VERSION );

	if ( class_exists( 'woocommerce' ) ) {
		wp_enqueue_style( 'virtue_woo', get_template_directory_uri() . '/assets/css/virtue-woocommerce.css', false, VIRTUE_VERSION );
	}

	if ( defined( 'SITEORIGIN_PANELS_VERSION' ) ) {
		wp_enqueue_style( 'virtue_so_pb', get_template_directory_uri() . '/assets/css/virtue-so-page-builder.css', false, VIRTUE_VERSION );
	}

	if ( isset( $virtue_premium['minimal_icons'] ) && '1' == $virtue_premium['minimal_icons'] ) {
		wp_enqueue_style( 'virtue_icons', get_template_directory_uri() . '/assets/css/virtue_min_icons.css', false, VIRTUE_VERSION );
	} else {
		wp_enqueue_style( 'virtue_icons', get_template_directory_uri() . '/assets/css/virtue_icons.css', false, VIRTUE_VERSION );
	}
	if ( isset( $virtue_premium['skin_stylesheet'] ) && ! empty( $virtue_premium['skin_stylesheet'] ) ) {
		$skin = $virtue_premium['skin_stylesheet'];
	} else {
		$skin = 'default.css';
	}
	$skin_stylesheet_path = apply_filters( 'kt_skin_style_path_output', get_template_directory_uri() . '/assets/css/skins/' );
	wp_enqueue_style( 'virtue_skin', $skin_stylesheet_path . $skin, false, VIRTUE_VERSION );

	if ( is_rtl() ) {
		wp_enqueue_style( 'virtue_rtl', get_template_directory_uri() . '/assets/css/rtl.css', false, VIRTUE_VERSION );
	}
	if ( is_child_theme() ) {
		$child_theme = wp_get_theme();
		$child_version = $child_theme->get( 'Version' );
		wp_enqueue_style( 'virtue_child', get_stylesheet_uri(), false, $child_version );
	}

	if ( is_single() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
	wp_enqueue_script( 'bootstrap', get_template_directory_uri() . '/assets/js/min/bootstrap-min.js', array( 'jquery' ), VIRTUE_VERSION, true );
	wp_enqueue_script( 'virtue_plugins', get_template_directory_uri() . '/assets/js/min/plugins-min.js', array( 'jquery', 'hoverIntent' ), VIRTUE_VERSION, true );
	wp_enqueue_script( 'select2', get_template_directory_uri() . '/assets/js/min/select2-min.js', array( 'jquery' ), VIRTUE_VERSION, true );

	if ( isset( $virtue_premium['smooth_scrolling'] ) && '1' == $virtue_premium['smooth_scrolling'] ) {
		wp_enqueue_script( 'virtue_smoothscroll', get_template_directory_uri() . '/assets/js/min/nicescroll-min.js', array( 'jquery' ), VIRTUE_VERSION, false );
	} elseif ( isset( $virtue_premium['smooth_scrolling'] ) && '2' == $virtue_premium['smooth_scrolling'] ) {
		wp_enqueue_script( 'virtue_smoothscroll', get_template_directory_uri() . '/assets/js/min/smoothscroll-min.js', array( 'jquery' ), VIRTUE_VERSION, true );
	}
	if ( isset( $virtue_premium['kadence_lightbox'] ) && '1' != $virtue_premium['kadence_lightbox'] ) {
		wp_enqueue_script( 'virtue_lightbox', get_template_directory_uri() . '/assets/js/min/virtue_lightbox-min.js', array( 'jquery' ), VIRTUE_VERSION, true );
		// Localize the script with new data.
		$lightbox_translation_array = array(
			'loading' => ( isset( $virtue_premium['lightbox_loading_text'] ) && ! empty( $virtue_premium['lightbox_loading_text'] ) ? $virtue_premium['lightbox_loading_text'] : __( 'Loading...', 'virtue' ) ),
			'of'      => ( isset( $virtue_premium['lightbox_of_text'] ) && ! empty( $virtue_premium['lightbox_of_text'] ) ? '%curr% ' . $virtue_premium['lightbox_of_text'] . ' %total%' : '%curr% ' . __( 'of', 'virtue' ) . ' %total%' ),
			'error'   => ( isset( $virtue_premium['lightbox_error_text'] ) && ! empty( $virtue_premium['lightbox_error_text'] ) ? $virtue_premium['lightbox_error_text'] : __( 'The Image could not be loaded.', 'virtue' ) ),
		);
		wp_localize_script( 'virtue_lightbox', 'virtue_lightbox', $lightbox_translation_array );
	}
	if ( isset( $virtue_premium['google_map_api'] ) && ! empty( $virtue_premium['google_map_api'] ) ) {
		$gmap_api = $virtue_premium['google_map_api'];
	} else {
		$gmap_api = '';
	}
	wp_register_script( 'virtue_google_map_api', 'https://maps.googleapis.com/maps/api/js?key=' . esc_attr( $gmap_api ), array( 'jquery' ), VIRTUE_VERSION, true );
	wp_register_script( 'virtue_gmap', get_template_directory_uri() . '/assets/js/min/virtue-gmap-min.js', array( 'jquery' ), VIRTUE_VERSION, true );
	wp_register_script( 'virtue_contact_validation', get_template_directory_uri() . '/assets/js/min/virtue-contact-validation-min.js', array( 'jquery' ), VIRTUE_VERSION, true );
	// Localize the script with new data.
	$contact_translation_array = array(
		'required' => __( 'This field is required.', 'virtue' ),
		'email'    => __( 'Please enter a valid email address.', 'virtue' ),
	);
	wp_localize_script( 'virtue_contact_validation', 'virtue_contact', $contact_translation_array );

	wp_enqueue_script( 'virtue_main', get_template_directory_uri() . '/assets/js/min/main-min.js', array( 'jquery', 'hoverIntent' ), VIRTUE_VERSION, true );

	if ( ( isset( $virtue_premium['infinitescroll'] ) && '1' == $virtue_premium['infinitescroll'] ) || ( isset( $virtue_premium['blog_infinitescroll'] ) && '1' == $virtue_premium['blog_infinitescroll'] ) || ( isset( $virtue_premium['blog_cat_infinitescroll'] ) && '1' == $virtue_premium['blog_cat_infinitescroll'] ) ) {
		wp_register_script( 'virtue-infinite-scroll', get_template_directory_uri() . '/assets/js/jquery.infinitescroll.js', array( 'jquery' ), VIRTUE_VERSION, true );
	}

  	if(class_exists('woocommerce') ) {
    	if(isset($virtue_premium['product_radio']) && $virtue_premium['product_radio'] == 1) {
        	wp_enqueue_script( 'kt-add-to-cart-variation-radio', get_template_directory_uri() . '/assets/js/min/kt-add-to-cart-variation-radio-min.js' , array( 'jquery' ), false, VIRTUE_VERSION, true );
    	} else {
       		wp_enqueue_script( 'kt-wc-add-to-cart-variation', get_template_directory_uri() . '/assets/js/min/kt-add-to-cart-variation-min.js' , array( 'jquery' ), false, VIRTUE_VERSION, true );
    	}
   		if(isset($virtue_premium['product_quantity_input']) && $virtue_premium['product_quantity_input'] == 1) {
        	wp_enqueue_script( 'wcqi-js', get_template_directory_uri() . '/assets/js/min/wc-quantity-increment-min.js' , array( 'jquery' ), false, VIRTUE_VERSION, true );
      	}
  	}

}
add_action('wp_enqueue_scripts', 'virtue_scripts', 100);

/**
 * Add Respond.js for IE8 support of media queries
 */
function virtue_ie_support_header() {
    wp_enqueue_script( 'virtue-respond', get_template_directory_uri().'/assets/js/vendor/respond.min.js' );
    wp_script_add_data( 'virtue-respond', 'conditional', 'lt IE 9' );
}
add_action( 'wp_enqueue_scripts', 'virtue_ie_support_header', 15 );


function virtue_lightbox_text() {
  	global $virtue_premium; 
  	if( isset( $virtue_premium['kadence_lightbox'] ) && $virtue_premium['kadence_lightbox'] != 1 ) { 
		if(!empty($virtue_premium['lightbox_loading_text'])) {
			$loading_text = $virtue_premium['lightbox_loading_text'];
		} else {
			$loading_text = 'Loading...';
		}
		if(!empty($virtue_premium['lightbox_of_text'])) {
			$of_text = $virtue_premium['lightbox_of_text'];
		} else {
			$of_text = 'of';
		}
		if(!empty($virtue_premium['lightbox_error_text'])) {
			$error_text = $virtue_premium['lightbox_error_text'];
		} else {
			$error_text = 'The Image could not be loaded.';
		}
		echo  '<script type="text/javascript">var light_error = "'.$error_text.'", light_of = "%curr% '.$of_text.' %total%", light_load = "'.$loading_text.'";</script>';
	}
}
add_action('wp_head', 'virtue_lightbox_text');

add_action('virtue_after_body', 'virtue_wp_after_body_script_output', 10);
function virtue_wp_after_body_script_output() {
    global $virtue_premium;
    if(isset($virtue_premium['kt_after_body_open_script']) && !empty($virtue_premium['kt_after_body_open_script']) ){
        echo $virtue_premium['kt_after_body_open_script'];
    }
}


function virtue_google_analytics() { 
  global $virtue_premium; 
  if(isset($virtue_premium['google_analytics']) && !empty($virtue_premium['google_analytics'])) { ?>
  <!-- Google Analytics -->
<script>
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

ga('create', '<?php echo $virtue_premium['google_analytics']; ?>', 'auto');
ga('send', 'pageview');
<?php if ( isset( $virtue_premium['google_analytics_anony'] ) && 1 == $virtue_premium['google_analytics_anony'] ) { ?>
ga('set', 'anonymizeIp', true);
<?php } ?>
</script>
<!-- End Google Analytics -->
  <?php
  }
}
add_action('wp_head', 'virtue_google_analytics', 20);
