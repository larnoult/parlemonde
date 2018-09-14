<?php
class CycloneSlider_Frontend {
	/**
     * @var CycloneSlider_Data
     */
    protected $data;
	/**
     * @var array
     */
    protected $image_sizes;
	/**
     * @var CycloneSlider_Youtube
     */
    protected $youtube;
	/**
     * @var CycloneSlider_Vimeo
     */
    public $vimeo;
	/**
     * @var CycloneSlider_View
     */
    protected $view;
	/**
     * @var int
     */
    public $slider_count;

	/**
     * CycloneSlider_Frontend constructor.
     *
     * @param $data
     * @param $image_sizes
     * @param $youtube
     * @param $vimeo
     * @param $view
     */
    public function __construct( $data, $image_sizes, $youtube, $vimeo, $view ){
        $this->data = $data;
        $this->image_sizes = $image_sizes;
        $this->youtube = $youtube;
        $this->vimeo = $vimeo;
        $this->view = $view;
    }

	/**
     *
     */
    public function run() {
        
        // Set defaults
        $this->slider_count = 0;
        
        // Our shortcode
        add_shortcode('cycloneslider', array( $this, 'cycloneslider_shortcode') );
        
    }

    /**
     * Cycloneslider Shortcode
     *
     * Displays shortcode on pages
     *
     * @param array $shortcode_settings Array of shortcode parameters
     * @return string Slider HTML
     */
    public function cycloneslider_shortcode( $shortcode_settings ) {
        // Apply defaults
        $shortcode_settings = shortcode_atts(
            array(
                'id'               => 0,
                'fx'               => null,
                'timeout'          => null,
                'speed'            => null,
                'width'            => null,
                'height'           => null,
                'hover_pause'      => null,
                'show_prev_next'   => null,
                'show_nav'         => null,
                'tile_count'       => null,
                'tile_delay'       => null,
                'tile_vertical'    => null,
                'random'           => null,
                'resize'           => null,
                'resize_option'    => null,
                'easing'           => null,
                'allow_wrap'       => null,
                'dynamic_height'   => null,
                'delay'            => null,
                'swipe'            => true, // TODO: Proper fix for swipe
                'width_management' => null
            ),
            $shortcode_settings,
            'cycloneslider'
        );

        $slider_slug = $shortcode_settings['id']; // Slideshow slug passed from shortcode
        $slider      = $this->data->get_slider_by_slug( $slider_slug ); // Get slider by slug

        // Abort if slider not found!
        if ( $slider === NULL ) {
            return sprintf( __( '[Slideshow "%s" not found]', 'cycloneslider' ), $slider_slug );
        }

        $slider_count   = ++ $this->slider_count; // Make each call to shortcode unique
        $slider_html_id = 'cycloneslider-' . $slider_slug . '-' . $slider_count; // UID


        // Assign important variables
        // $slider_settings = $slider['slider_settings'];
        // $slides = $slider['slides'];
        $admin_settings = $slider['slider_settings']; // Assign slider settings
        $slides         = $slider['slides']; // Assign slides

        $template_name = $admin_settings['template'];
        $view_file     = $this->data->get_view_file( $template_name );
        if ( $view_file === false ) { // Abort if template not found!
            return sprintf( __( '[Template "%s" not found]', 'cycloneslider' ), $template_name );
        }

        $slider_settings = $this->data->combine_slider_settings( $admin_settings, $shortcode_settings );

        $image_count   = 0; // Number of image slides
        $video_count   = 0; // Number of video slides
        $custom_count  = 0; // Number of custom slides
        $youtube_count = 0; // Number of youtube slides
        $vimeo_count   = 0; // Number of Vimeo slides

        // Remove hidden slides
        foreach ( $slides as $i => $slide ) {
            if ( $slides[ $i ]['hidden'] ) {
                unset( $slides[ $i ] );
            }
        }

        // Do some last minute logic
        // Translations and counters
        foreach ( $slides as $i => $slide ) {

            $slides[ $i ]['title']                 = __( $slide['title'] ); // Needed by some translation plugins to work
            $slides[ $i ]['description']           = __( $slide['description'] ); // Needed by some translation plugins to work
            $slides[ $i ]['slide_data_attributes'] = $this->data->slide_data_attributes( $slide, $slider );

            if ( $slides[ $i ]['type'] == 'image' ) {

                list( $full_image_url, $orig_width, $orig_height ) = wp_get_attachment_image_src( $slide['id'],
                    'full' );

                $slides[ $i ]['full_image_url'] = $full_image_url;
                $slides[ $i ]['image_url']      = $this->data->get_slide_image_url( $slide['id'], $slider_settings );

                $slides[ $i ]['image_thumbnails'] = array();
                $this->image_sizes = apply_filters('cycloneslider_image_sizes', $this->image_sizes);
                foreach ( $this->image_sizes as $key => $size ) {
                    $slides[ $i ]['image_thumbnails'][ $key ] = $this->data->get_slide_thumbnail_url( $slide['id'],
                        $size['width'], $size['height'], $slider_settings['resize'] );
                }

                $image_count ++;
            } else if ( $slides[ $i ]['type'] == 'video' ) {
                $video_count ++;
            } else if ( $slides[ $i ]['type'] == 'custom' ) {
                $custom_count ++;
            } else if ( $slides[ $i ]['type'] == 'youtube' ) {
                $youtube_count ++;
                $youtube_id = $this->youtube->get_youtube_id( $slides[ $i ]['youtube_url'] );

                $youtube_related = '';
                if ( 'true' == $slides[ $i ]['youtube_related'] ) {
                    $youtube_related = '&rel=0';
                }

                $slides[ $i ]['youtube_embed_code'] = '<iframe id="' . $slider_html_id . '-iframe-' . $i . '" width="' . $slider_settings['width'] . '" height="' . $slider_settings['height'] . '" src="//www.youtube.com/embed/' . $youtube_id . '?wmode=transparent' . $youtube_related . '" frameborder="0" allowfullscreen></iframe>';
                $slides[ $i ]['youtube_id']         = $youtube_id;
                $slides[ $i ]['thumbnail_small']    = $this->youtube->get_youtube_thumb( $youtube_id );

            } else if ( $slides[ $i ]['type'] == 'vimeo' ) {
                $vimeo_count ++;
                $vimeo_id = $this->vimeo->get_vimeo_id( $slides[ $i ]['vimeo_url'] );

                $slides[ $i ]['vimeo_embed_code'] = '<iframe id="' . $slider_html_id . '-iframe-' . $i . '" width="' . $slider_settings['width'] . '" height="' . $slider_settings['height'] . '" src="https://player.vimeo.com/video/' . $vimeo_id . '?wmode=transparent" frameborder="0"  webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
                $slides[ $i ]['vimeo_id']         = $vimeo_id;
                $slides[ $i ]['thumbnail_small']  = $this->vimeo->get_vimeo_thumb( $vimeo_id );
            } else if ( $slides[ $i ]['type'] == 'testimonial' ) {
                list( $full_image_url, $orig_width, $orig_height ) = wp_get_attachment_image_src( $slide['testimonial_img'],
                    'full' );

                $slides[ $i ]['testimonial_img_url'] = $slides[ $i ]['full_testimonial_img_url'] = $full_image_url;
            }
        }

        // Randomize slides
        if ( $slider_settings['random'] ) {
            shuffle( $slides ); // Randomizing happens in php not in cycle2
        }

        // Make this available in templates regardless
        $slider_settings['random'] = ( $slider_settings['random'] == 1 ) ? true : false; // Convert from int to bool

        // Hardcoded for now
        $slider_settings['hide_non_active'] = "true";
        $slider_settings['auto_height']     = "{$slider_settings['width']}:{$slider_settings['height']}"; // Use ratio for backward compat
        if ( 'on' == $slider_settings['dynamic_height'] ) {
            $slider_settings['auto_height'] = 0; // Disable autoheight when dynamic height is on. To prevent slider returning to wrong (ratio height) height when browser is resized.
        }
        if ( ( $youtube_count + $vimeo_count ) > 0 or 'on' == $slider_settings['dynamic_height'] ) {
            $slider_settings['hide_non_active'] = "false"; // Do not hide non active slides to prevent reloading of videos and for getBoundingClientRect() to not return 0.
        }
        $slider_settings['auto_height_speed']  = $slider_settings['dynamic_height_speed'];
        $slider_settings['auto_height_easing'] = "null"; // TODO: Will be editable in admin in the future

        // Pass this vars to template
        $vars                    = array();
        $vars['slider_html_id']  = $slider_html_id; // The unique HTML ID for slider
        $vars['slider_count']    = $slider_count;
        $vars['slides']          = $slides;
        $vars['image_count']     = $image_count;
        $vars['video_count']     = $video_count;
        $vars['custom_count']    = $custom_count;
        $vars['youtube_count']   = $youtube_count;
        $vars['vimeo_count']     = $vimeo_count;
        // $vars['slider_id']       = $slider_slug; //TODO: (Deprecated since 2.6.0, use $slider_html_id instead) Unique string to identify the slideshow.
        // $vars['slider_metas']    = $slides; //TODO: (Deprecated since 2.5.5, use $slides instead) An array containing slides properties.
        $vars['slider_settings'] = $slider_settings;

        $current_view_folder = $this->view->get_view_folder(); // Back it up
        
        $this->view->set_view_folder( dirname( $view_file ) ); // Set to template folder

        $vars = apply_filters('cycloneslider_view_vars', $vars, $this);

        $slider_html = $this->view->get_render( basename( $view_file ), $vars );
        
        $this->view->set_view_folder( $current_view_folder ); // Restore
        
        // Remove whitespace to prevent WP from adding rogue paragraphs
        $slider_html = $this->data->trim_white_spaces( $slider_html );
        
        // Return HTML
        return $slider_html;
    }
}
