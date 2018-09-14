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

                    echo '<div class="redux-slides-accordion-group"><fieldset class="redux-field" data-id="' . $this->field[ 'id' ] . '"><h3><span class="redux-slides-header">' . $slide[ 'title' ] . '</span></h3><div>';

                    $hide = '';
                    if ( empty ( $slide[ 'image' ] ) ) {
                        $hide = ' hide';
                    }

                    echo '<div class="screenshot' . $hide . '">';
                    echo '<a class="of-uploaded-image" href="' . $slide[ 'image' ] . '">';
                    echo '<img class="redux-slides-image" id="image_image_id_' . $x . '" src="' . $slide[ 'thumb' ] . '" alt="" target="_blank" rel="external" />';
                    echo '</a>';
                    echo '</div>';

                    echo '<div class="redux_slides_add_remove">';

                    echo '<span class="button media_upload_button" id="add_' . $x . '">' . __ ( 'Upload', 'virtue' ) . '</span>';

                    $hide = '';
                    if ( empty ( $slide[ 'image' ] ) || $slide[ 'image' ] == '' ) {
                        $hide = ' hide';
                    }

                    echo '<span class="button remove-image' . $hide . '" id="reset_' . $x . '" rel="' . $slide[ 'attachment_id' ] . '">' . __ ( 'Remove', 'virtue' ) . '</span>';

                    echo '</div>' . "\n";

                    echo '<ul id="' . $this->field[ 'id' ] . '-ul" class="redux-slides-list">';

                    echo '<li><input type="text" id="' . $this->field['id'] . '-url_' . $x . '" name="' . $this->field['name'] . '[' . $x . '][url]" value="' . esc_attr($slide['url']) . '" class="full-text upload" placeholder="'.__('URL', 'virtue').'" /></li>';
                    echo '<li><input type="text" id="' . $this->field['id'] . '-title_' . $x . '" name="' . $this->field['name'] . '[' . $x . '][title]" value="' . esc_attr($slide['title']) . '" placeholder="'.__('Title', 'virtue').'" class="full-text slide-title" /></li>';
                    echo '<li><textarea name="' . $this->field['name'] . '[' . $x . '][description]" id="' . $this->field['id'] . '-description_' . $x . '" placeholder="'.__('Description', 'virtue').'" class="large-text" rows="6">' . esc_attr($slide['description']) . '</textarea></li>';
                    echo '<li><input type="text" id="' . $this->field['id'] . '-link_' . $x . '" name="' . $this->field['name'] . '[' . $x . '][link]" value="' . esc_attr($slide['link']) . '" placeholder="'.__('Slide Link', 'virtue').'" class="full-text" /></li>';
                    
                    echo '<li><label for="'. $this->field['id'] .  '-target_' . $x . '" class="icon-link-target">';
                    echo '<input type="checkbox" class="checkbox-slide-target" id="' . $this->field['id'] . '-target_' . $x . '" value="1" ' . checked(  $slide['target'], '1', false ) . ' name="' . $this->field['name'] . '[' . $x . '][target]" />';
                    echo ' '.__('Open Link in New Tab/Window', 'virtue'). '</label></li>';

                    echo '<li><input type="hidden" class="slide-sort" name="' . $this->field['name'] . '[' . $x . '][sort]" id="' . $this->field['id'] . '-sort_' . $x . '" value="' . $slide['sort'] . '" />';
                    echo '<li><input type="hidden" class="upload-id" name="' . $this->field['name'] . '[' . $x . '][attachment_id]" id="' . $this->field['id'] . '-image_id_' . $x . '" value="' . $slide['attachment_id'] . '" />';
                    echo '<input type="hidden" class="upload-thumbnail" name="' . $this->field['name'] . '[' . $x . '][thumb]" id="' . $this->field['id'] . '-thumb_url_' . $x . '" value="' . $slide['thumb'] . '" readonly="readonly" />';
                    echo '<input type="hidden" class="upload" name="' . $this->field['name'] . '[' . $x . '][image]" id="' . $this->field['id'] . '-image_url_' . $x . '" value="' . $slide['image'] . '" readonly="readonly" />';
                    echo '<input type="hidden" class="upload-height" name="' . $this->field['name'] . '[' . $x . '][height]" id="' . $this->field['id'] . '-image_height_' . $x . '" value="' . $slide['height'] . '" />';
                    echo '<input type="hidden" class="upload-width" name="' . $this->field['name'] . '[' . $x . '][width]" id="' . $this->field['id'] . '-image_width_' . $x . '" value="' . $slide['width'] . '" /></li>';
                    
                    echo '<li><a href="javascript:void(0);" class="button deletion redux-slides-remove">' . __('Delete Slide', 'virtue') . '</a></li>';
                    echo '</ul></div></fieldset></div>';
                    $x++;
                
                }
            }

            if ($x == 0) {
                echo '<div class="redux-slides-accordion-group"><fieldset class="redux-field" data-id="'.$this->field['id'].'"><h3><span class="redux-slides-header">New Slide</span></h3><div>';

                $hide = ' hide';

                echo '<div class="screenshot' . $hide . '">';
                echo '<a class="of-uploaded-image" href="">';
                echo '<img class="redux-slides-image" id="image_image_id_' . $x . '" src="" alt="" target="_blank" rel="external" />';
                echo '</a>';
                echo '</div>';

                //Upload controls DIV
                echo '<div class="upload_button_div">';

                //If the user has WP3.5+ show upload/remove button
                echo '<span class="button media_upload_button" id="add_' . $x . '">' . __ ( 'Upload', 'virtue' ) . '</span>';

                echo '<span class="button remove-image' . $hide . '" id="reset_' . $x . '" rel="' . $this->parent->args[ 'opt_name' ] . '[' . $this->field[ 'id' ] . '][attachment_id]">' . __ ( 'Remove', 'virtue' ) . '</span>';

                echo '</div>' . "\n";

                echo '<ul id="' . $this->field['id'] . '-ul" class="redux-slides-list">';
                $placeholder = (isset($this->field['placeholder']['url'])) ? esc_attr($this->field['placeholder']['url']) : __( 'URL', 'virtue' );
                echo '<li><input type="text" id="' . $this->field['id'] . '-url_' . $x . '" name="' . $this->field['name'] . '[' . $x . '][url]" value="" class="full-text upload" placeholder="'.$placeholder.'" /></li>';
                $placeholder = (isset($this->field['placeholder']['title'])) ? esc_attr($this->field['placeholder']['title']) : __( 'Title', 'virtue' );
                echo '<li><input type="text" id="' . $this->field['id'] . '-title_' . $x . '" name="' . $this->field['name'] . '[' . $x . '][title]" value="" placeholder="'.$placeholder.'" class="full-text slide-title" /></li>';
                $placeholder = (isset($this->field['placeholder']['description'])) ? esc_attr($this->field['placeholder']['description']) : __( 'Description', 'virtue' );
                echo '<li><textarea name="' . $this->field['name'] . '[' . $x . '][description]" id="' . $this->field['id'] . '-description_' . $x . '" placeholder="'.$placeholder.'" class="large-text" rows="6"></textarea></li>';
                $placeholder = (isset($this->field['placeholder']['link'])) ? esc_attr($this->field['placeholder']['link']) : __( 'Slide Link', 'virtue' );
                echo '<li><input type="text" id="' . $this->field['id'] . '-link_' . $x . '" name="' . $this->field['name'] . '[' . $x . '][link]" value="" placeholder="'.$placeholder.'" class="full-text" /></li>';
                
                echo '<li><label for="'. $this->field['id'] .  '-target_' . $x . '">';
                echo '<input type="checkbox" class="checkbox-slide-target" id="' . $this->field['id'] . '-target_' . $x . '" value="" ' . checked(  '', '1', false ) . ' name="' . $this->field['name'] . '[' . $this->field['id'] . '][' . $x . '][target]" />';
                echo ' '.__('Open Link in New Tab/Window', 'virtue'). '</label></li>';

                echo '<li><input type="hidden" class="slide-sort" name="' . $this->field['name'] . '[' . $x . '][sort]" id="' . $this->field['id'] . '-sort_' . $x . '" value="' . $x . '" />';
                echo '<li><input type="hidden" class="upload-id" name="' . $this->field['name'] . '[' . $x . '][attachment_id]" id="' . $this->field['id'] . '-image_id_' . $x . '" value="" />';
                echo '<input type="hidden" class="upload" name="' . $this->field['name'] . '[' . $x . '][url]" id="' . $this->field['id'] . '-image_url_' . $x . '" value="" readonly="readonly" />';
                echo '<input type="hidden" class="upload-height" name="' . $this->field['name'] . '[' . $x . '][height]" id="' . $this->field['id'] . '-image_height_' . $x . '" value="" />';
                echo '<input type="hidden" class="upload-width" name="' . $this->field['name'] . '[' . $x . '][width]" id="' . $this->field['id'] . '-image_width_' . $x . '" value="" /></li>';

                echo '<li><a href="javascript:void(0);" class="button deletion redux-slides-remove">' . __('Delete Slide', 'virtue') . '</a></li>';
                echo '</ul></div></fieldset></div>';
            }
            echo '</div><a href="javascript:void(0);" class="button redux-slides-add2 kad_redux-slides-add button-primary" rel-id="' . $this->field[ 'id' ] . '-ul" rel-name="' . $this->field[ 'name' ] . '[title][]">' . sprintf ( __ ( 'Add %s', 'virtue' ), $this->field[ 'content_title' ] ) . '</a><br/>';
        }
 
         public function enqueue () {
            if ( function_exists( 'wp_enqueue_media' ) ) {
                wp_enqueue_media();
            } else {
                wp_enqueue_script( 'media-upload' );
            }

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
