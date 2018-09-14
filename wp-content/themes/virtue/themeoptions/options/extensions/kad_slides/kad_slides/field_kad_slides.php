<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

// Don't duplicate me!
if (!class_exists('ReduxFramework_kad_slides')) {

    /**
     * Main ReduxFramework_slides class
     *
     * @since       1.0.0
     */
    class ReduxFramework_kad_slides {

        /**
         * Field Constructor.
         *
         * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
         *
         * @since       1.0.0
         * @access      public
         * @return      void
         */
          function __construct ( $field = array(), $value = '', $parent ) {
            $this->parent = $parent;
            $this->field = $field;
            $this->value = $value;
        }

        /**
         * Field Render Function.
         *
         * Takes the vars and outputs the HTML for the field in the settings
         *
         * @since       1.0.0
         * @access      public
         * @return      void
         */
        public function render() {

             $defaults = array(
                'show' => array(
                    'title' => true,
                    'description' => true,
                    'url' => true,
                ),
                'content_title' => __ ( 'Slide', 'virtue' )
            );

             $this->field = wp_parse_args ( $this->field, $defaults );
			/* translators: %s: slide */
           	echo '<div class="redux-slides-accordion" data-new-content-title="' . esc_attr ( sprintf ( __ ( 'New %s', 'virtue' ), $this->field[ 'content_title' ] ) ) . '">';

            $x = 0;

             $multi = ( isset ( $this->field[ 'multi' ] ) && $this->field[ 'multi' ] ) ? ' multiple="multiple"' : "";

            if ( isset ( $this->value ) && is_array ( $this->value ) && !empty ( $this->value ) ) {

                $slides = $this->value;

                foreach ( $slides as $slide ) {

                    if ( empty ( $slide ) ) {
                        continue;
                    }


                    $defaults = array(
                        'title' => '',
                        'description' => '',
                        'sort' => '',
                        'link' => '',
                        'image' => '',
                        'target' => '',
                        'url' => '',
                        'thumb' => '',
                        'attachment_id' => '',
                        'height' => '',
                        'width' => '',
                        'select' => array(),
                    );
                    $slide = wp_parse_args( $slide, $defaults );

                   if ( empty ( $slide[ 'thumb' ] ) && !empty ( $slide[ 'attachment_id' ] ) ) {
                        $img = wp_get_attachment_image_src ( $slide[ 'attachment_id' ], 'full' );
                        $slide[ 'image' ] = $img[ 0 ];
                        $slide[ 'width' ] = $img[ 1 ];
                        $slide[ 'height' ] = $img[ 2 ];
                    }

                    echo '<div class="redux-slides-accordion-group"><fieldset class="redux-field" data-id="' . esc_attr( $this->field[ 'id' ] ) . '"><h3><span class="redux-slides-header">' . esc_html( $slide[ 'title' ] ) . '</span></h3><div>';

                    $hide = '';
                    if ( empty ( $slide[ 'image' ] ) ) {
                        $hide = ' hide';
                    }

                    echo '<div class="screenshot' . esc_attr( $hide ) . '">';
                    echo '<a class="of-uploaded-image" href="' . esc_url( $slide[ 'image' ] ) . '">';
                    echo '<img class="redux-slides-image" id="image_image_id_' . esc_attr( $x ). '" src="' . esc_url( $slide[ 'thumb' ] ) . '" alt="" target="_blank" rel="external" />';
                    echo '</a>';
                    echo '</div>';

                    echo '<div class="redux_slides_add_remove">';

                    echo '<span class="button media_upload_button" id="add_' . esc_attr( $x ) . '">' . esc_html__( 'Upload', 'virtue' ) . '</span>';

                    $hide = '';
                    if ( empty ( $slide[ 'image' ] ) || $slide[ 'image' ] == '' ) {
                        $hide = ' hide';
                    }

                    echo '<span class="button remove-image' . esc_attr( $hide ) . '" id="reset_' . esc_attr( $x ) . '" rel="' . esc_attr( $slide[ 'attachment_id' ] ) . '">' . esc_html__( 'Remove', 'virtue' ) . '</span>';

                    echo '</div>' . "\n";

                    echo '<ul id="' . esc_attr( $this->field[ 'id' ] ) . '-ul" class="redux-slides-list">';

                    echo '<li><input type="text" id="' . esc_attr( $this->field['id'] ) . '-url_' . esc_attr( $x ) . '" name="' . esc_attr( $this->field['name'] ) . '[' . esc_attr( $x ) . '][url]" value="' . esc_attr($slide['url']) . '" class="full-text upload" placeholder="'.esc_html__('URL', 'virtue').'" /></li>';
                    echo '<li><input type="text" id="' . esc_attr( $this->field['id'] ) . '-title_' . esc_attr( $x ) . '" name="' . esc_attr( $this->field['name'] ) . '[' . esc_attr( $x ) . '][title]" value="' . esc_attr($slide['title']) . '" placeholder="'.esc_html__('Title', 'virtue').'" class="full-text slide-title" /></li>';
                    echo '<li><textarea name="' . esc_attr( $this->field['name'] ) . '[' . esc_attr( $x ) . '][description]" id="' . esc_attr( $this->field['id'] ) . '-description_' . esc_attr( $x ) . '" placeholder="'.esc_attr__('Description', 'virtue').'" class="large-text" rows="6">' . esc_attr($slide['description']) . '</textarea></li>';
                    echo '<li><input type="text" id="' . esc_attr( $this->field['id'] ) . '-link_' . esc_attr( $x ) . '" name="' . esc_attr( $this->field['name'] ) . '[' . esc_attr( $x ) . '][link]" value="' . esc_attr($slide['link']) . '" placeholder="'.esc_attr__('Slide Link', 'virtue').'" class="full-text" /></li>';
                    
                    echo '<li><label for="'. esc_attr( $this->field['id'] ) .  '-target_' . esc_attr( $x ) . '" class="icon-link-target">';
                    echo '<input type="checkbox" class="checkbox-slide-target" id="' . esc_attr( $this->field['id'] ) . '-target_' . esc_attr( $x ) . '" value="1" ' . checked(  $slide['target'], '1', false ) . ' name="' . esc_attr( $this->field['name'] ) . '[' . esc_attr( $x ) . '][target]" />';
                    echo ' '.esc_html__('Open Link in New Tab/Window', 'virtue'). '</label></li>';

                    echo '<li><input type="hidden" class="slide-sort" name="' . esc_attr( $this->field['name'] ) . '[' . esc_attr( $x ) . '][sort]" id="' . esc_attr( $this->field['id'] ) . '-sort_' . esc_attr( $x ) . '" value="' . esc_attr( $slide['sort'] ) . '" />';
                    echo '<li><input type="hidden" class="upload-id" name="' . esc_attr( $this->field['name'] ) . '[' . esc_attr( $x ) . '][attachment_id]" id="' . esc_attr( $this->field['id'] ) . '-image_id_' . esc_attr( $x ) . '" value="' . esc_attr( $slide['attachment_id'] ) . '" />';
                    echo '<input type="hidden" class="upload-thumbnail" name="' . esc_attr( $this->field['name'] ) . '[' . esc_attr( $x ) . '][thumb]" id="' . esc_attr( $this->field['id'] ) . '-thumb_url_' . esc_attr( $x ) . '" value="' . esc_attr( $slide['thumb'] ) . '" readonly="readonly" />';
                    echo '<input type="hidden" class="upload" name="' . esc_attr( $this->field['name'] ) . '[' . esc_attr( $x ) . '][image]" id="' . esc_attr( $this->field['id'] ) . '-image_url_' . esc_attr( $x ) . '" value="' . esc_attr( $slide['image'] ) . '" readonly="readonly" />';
                    echo '<input type="hidden" class="upload-height" name="' . esc_attr( $this->field['name'] ) . '[' . esc_attr( $x ) . '][height]" id="' . esc_attr( $this->field['id'] ) . '-image_height_' . esc_attr( $x ) . '" value="' . esc_attr( $slide['height'] ) . '" />';
                    echo '<input type="hidden" class="upload-width" name="' . esc_attr( $this->field['name'] ) . '[' . esc_attr( $x ) . '][width]" id="' . esc_attr( $this->field['id'] ) . '-image_width_' . esc_attr( $x ) . '" value="' . esc_attr( $slide['width'] ) . '" /></li>';
                    
                    echo '<li><a href="javascript:void(0);" class="button deletion redux-slides-remove">' . esc_html__('Delete Slide', 'virtue') . '</a></li>';
                    echo '</ul></div></fieldset></div>';
                    $x++;
                
                }
            }

            if ($x == 0) {
                echo '<div class="redux-slides-accordion-group"><fieldset class="redux-field" data-id="'.esc_attr( $this->field['id'] ).'"><h3><span class="redux-slides-header">New Slide</span></h3><div>';

                $hide = ' hide';

                echo '<div class="screenshot' . esc_attr( $hide ) . '">';
                echo '<a class="of-uploaded-image" href="">';
                echo '<img class="redux-slides-image" id="image_image_id_' . esc_attr( $x ) . '" src="" alt="" target="_blank" rel="external" />';
                echo '</a>';
                echo '</div>';

                //Upload controls DIV
                echo '<div class="upload_button_div">';

                //If the user has WP3.5+ show upload/remove button
                echo '<span class="button media_upload_button" id="add_' . esc_attr( $x ) . '">' . esc_html__( 'Upload', 'virtue' ) . '</span>';

                echo '<span class="button remove-image' . esc_attr( $hide ) . '" id="reset_' . esc_attr( $x ) . '" rel="' . esc_attr( $this->parent->args[ 'opt_name' ] ) . '[' . esc_attr( $this->field[ 'id' ] ) . '][attachment_id]">' . esc_html__( 'Remove', 'virtue' ) . '</span>';

                echo '</div>' . "\n";

                echo '<ul id="' . esc_attr( $this->field['id'] ) . '-ul" class="redux-slides-list">';
                $placeholder = (isset($this->field['placeholder']['url'])) ? esc_attr($this->field['placeholder']['url']) : __( 'URL', 'virtue' );
                echo '<li><input type="text" id="' . esc_attr( $this->field['id'] ) . '-url_' . esc_attr( $x ) . '" name="' . esc_attr( $this->field['name'] ) . '[' . esc_attr( $x ) . '][url]" value="" class="full-text upload" placeholder="'.esc_attr( $placeholder ).'" /></li>';
                $placeholder = (isset($this->field['placeholder']['title'])) ? esc_attr($this->field['placeholder']['title']) : __( 'Title', 'virtue' );
                echo '<li><input type="text" id="' . esc_attr( $this->field['id'] ) . '-title_' . esc_attr( $x ) . '" name="' . esc_attr( $this->field['name'] ) . '[' . esc_attr( $x ) . '][title]" value="" placeholder="'.esc_attr( $placeholder ).'" class="full-text slide-title" /></li>';
                $placeholder = (isset($this->field['placeholder']['description'])) ? esc_attr($this->field['placeholder']['description']) : __( 'Description', 'virtue' );
                echo '<li><textarea name="' . esc_attr( $this->field['name'] ) . '[' . esc_attr( $x ) . '][description]" id="' . esc_attr( $this->field['id'] ) . '-description_' . esc_attr( $x ) . '" placeholder="'.esc_attr( $placeholder ).'" class="large-text" rows="6"></textarea></li>';
                $placeholder = (isset($this->field['placeholder']['link'])) ? esc_attr($this->field['placeholder']['link']) : __( 'Slide Link', 'virtue' );
                echo '<li><input type="text" id="' . esc_attr( $this->field['id'] ) . '-link_' . esc_attr( $x ) . '" name="' . esc_attr( $this->field['name'] ) . '[' . esc_attr( $x ) . '][link]" value="" placeholder="'.esc_attr( $placeholder ).'" class="full-text" /></li>';
                
                echo '<li><label for="'. esc_attr( $this->field['id'] ) .  '-target_' . esc_attr( $x ) . '">';
                echo '<input type="checkbox" class="checkbox-slide-target" id="' . esc_attr( $this->field['id'] ) . '-target_' . esc_attr( $x ) . '" value="" ' . checked(  '', '1', false ) . ' name="' . esc_attr( $this->args['opt_name'] ) . '[' . esc_attr( $this->field['id'] ) . '][' . esc_attr( $x ) . '][target]" />';
                echo ' '.esc_html__('Open Link in New Tab/Window', 'virtue'). '</label></li>';

                echo '<li><input type="hidden" class="slide-sort" name="' . esc_attr( $this->field['name'] ) . '[' . esc_attr( $x ) . '][sort]" id="' . esc_attr( $this->field['id'] ) . '-sort_' . esc_attr( $x ) . '" value="' . esc_attr( $x ) . '" />';
                echo '<li><input type="hidden" class="upload-id" name="' . esc_attr( $this->field['name'] ) . '[' . esc_attr( $x ) . '][attachment_id]" id="' . esc_attr( $this->field['id'] ) . '-image_id_' . esc_attr( $x ) . '" value="" />';
                echo '<input type="hidden" class="upload" name="' . esc_attr( $this->field['name'] ) . '[' . esc_attr( $x ) . '][url]" id="' . esc_attr( $this->field['id'] ) . '-image_url_' . esc_attr( $x ) . '" value="" readonly="readonly" />';
                echo '<input type="hidden" class="upload-height" name="' . esc_attr( $this->field['name'] ) . '[' . esc_attr( $x ) . '][height]" id="' . esc_attr( $this->field['id'] ) . '-image_height_' . esc_attr( $x ) . '" value="" />';
                echo '<input type="hidden" class="upload-width" name="' . esc_attr( $this->field['name'] ) . '[' . esc_attr( $x ) . '][width]" id="' . esc_attr( $this->field['id'] ) . '-image_width_' . esc_attr( $x ) . '" value="" /></li>';

                echo '<li><a href="javascript:void(0);" class="button deletion redux-slides-remove">' . esc_html__('Delete Slide', 'virtue') . '</a></li>';
                echo '</ul></div></fieldset></div>';
            }
            /* translators: %s: slide */
            echo '</div><a href="javascript:void(0);" class="button redux-slides-add2 kad_redux-slides-add button-primary" rel-id="' . esc_attr( $this->field[ 'id' ] ) . '-ul" rel-name="' . esc_attr( $this->field[ 'name' ] ) . '[title][]">' . sprintf ( esc_html__( 'Add %s', 'virtue' ), esc_attr( $this->field[ 'content_title' ] ) ) . '</a><br/>';
        }
 
         public function enqueue () {


            wp_enqueue_script (
                'redux-field-media-js', 
                ReduxFramework::$_url . 'inc/fields/media/field_media' . Redux_Functions::isMin () . '.js', 
                array( 'jquery', 'redux-js' ), 
                time (), 
                true
            );

            wp_enqueue_script (
                'kad-field-slides-js', 
                get_template_directory_uri() . '/themeoptions/options/extensions/kad_slides/kad_slides/field_kad_slides' . Redux_Functions::isMin () . '.js', 
                array( 'jquery', 'jquery-ui-core', 'jquery-ui-accordion', 'wp-color-picker' ), 
                time (), 
                true
            );

            wp_enqueue_style (
                'kad-field-slides-css', 
                get_template_directory_uri() . '/themeoptions/options/extensions/kad_slides/kad_slides/field_kad_slides.css', 
                time (), 
                true
            );
        }       

    }
}
