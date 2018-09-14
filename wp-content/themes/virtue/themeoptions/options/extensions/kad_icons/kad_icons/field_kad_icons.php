<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

// Don't duplicate me!
if (!class_exists('ReduxFramework_kad_icons')) {

    /**
     * Main ReduxFramework_icons class
     *
     * @since       1.0.0
     */
    class ReduxFramework_kad_icons {

        /**
         * Field Constructor.
         *
         * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
         *
         * @since       1.0.0
         * @access      public
         * @return      void
         */
        function __construct( $field = array(), $value ='', $parent ) {
        
            //parent::__construct( $parent->sections, $parent->args );
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
                'content_title' => __ ( 'Icon', 'virtue' )
            );

             $this->field = wp_parse_args ( $this->field, $defaults );

           echo '<div class="redux-slides-accordion" data-new-content-title="' . esc_attr ( sprintf ( __ ( 'New %s', 'virtue' ), $this->field[ 'content_title' ] ) ) . '">';

            $x = 0;

            $multi = (isset($this->field['multi']) && $this->field['multi']) ? ' multiple="multiple"' : "";

            if (isset($this->value) && is_array($this->value)) {

                $slides = $this->value;

                foreach ($slides as $slide) {
                    
                    if ( empty( $slide ) ) {
                        continue;
                    }

                    $defaults = array(
                        'icon_o' => '',
                        'title' => '',
                        'description' => '',
                        'sort' => '',
                        'target' => '',
                        'link' => '',
                        'url' => '',
                        'thumb' => '',
                        'image' => '',
                        'attachment_id' => '',
                        'height' => '',
                        'width' => '',
                        'select' => array(),
                    );
                    $slide = wp_parse_args( $slide, $defaults );

                    if (empty($slide['url']) && !empty($slide['attachment_id'])) {
                        $img = wp_get_attachment_image_src($slide['attachment_id'], 'full');
                        $slide['url'] = $img[0];
                        $slide['width'] = $img[1];
                        $slide['height'] = $img[2];
                    }

                    echo '<div class="redux-slides-accordion-group"><fieldset class="redux-field" data-id="'.$this->field['id'].'"><h3><span class="redux-slides-header">' . $slide['title'] . '</span></h3><div>';

                    $hide = '';
                    if ( empty( $slide['url'] ) ) {
                        $hide = ' hide';
                    }

                    echo '<div class="screenshot' . $hide . '">';
                    echo '<a class="of-uploaded-image" href="' . $slide['url'] . '">';
                    echo '<img class="redux-slides-image" id="image_image_id_' . $x . '" src="' . $slide['thumb'] . '" alt="" target="_blank" rel="external" />';
                    echo '</a>';
                    echo '</div>';

                    echo '<div class="redux_slides_add_remove">';

                    echo '<span class="button media_upload_button" id="add_' . $x . '">' . __('Upload Icon', 'virtue') . '</span>';

                    $hide = '';
                    if (empty($slide['url']) || $slide['url'] == '')
                        $hide = ' hide';

                    echo '<span class="button remove-image' . $hide . '" id="reset_' . $x . '" rel="' . $slide['attachment_id'] . '">' . __('Remove', 'virtue') . '</span>';

                    echo '</div>' . "\n";
                   $icon_option = array("icon-glass " => "icon-glass ", "icon-music " => "icon-music ", "icon-search " => "icon-search ", "icon-envelope-alt " => "icon-envelope-alt ", "icon-heart " => "icon-heart ", "icon-star " => "icon-star ", "icon-star-empty " => "icon-star-empty ", "icon-user " => "icon-user ", "icon-film " => "icon-film ", "icon-th-large " => "icon-th-large ", "icon-th " => "icon-th ", "icon-th-list " => "icon-th-list ", "icon-ok " => "icon-ok ", "icon-remove " => "icon-remove ", "icon-zoom-in " => "icon-zoom-in ", "icon-zoom-out " => "icon-zoom-out ", "icon-power-off" => "icon-power-off", "icon-off " => "icon-off ", "icon-signal " => "icon-signal ", "icon-gear" => "icon-gear", "icon-cog " => "icon-cog ", "icon-trash " => "icon-trash ", "icon-home " => "icon-home ", "icon-file-alt " => "icon-file-alt ", "icon-time " => "icon-time ", "icon-road " => "icon-road ", "icon-download-alt " => "icon-download-alt ", "icon-download " => "icon-download ", "icon-upload " => "icon-upload ", "icon-inbox " => "icon-inbox ", "icon-play-circle " => "icon-play-circle ", "icon-rotate-right" => "icon-rotate-right", "icon-repeat " => "icon-repeat ", "icon-refresh " => "icon-refresh ", "icon-list-alt " => "icon-list-alt ", "icon-lock " => "icon-lock ", "icon-flag " => "icon-flag ", "icon-headphones " => "icon-headphones ", "icon-volume-off " => "icon-volume-off ", "icon-volume-down " => "icon-volume-down ", "icon-volume-up " => "icon-volume-up ", "icon-qrcode " => "icon-qrcode ", "icon-barcode " => "icon-barcode ", "icon-tag " => "icon-tag ", "icon-tags " => "icon-tags ", "icon-book " => "icon-book ", "icon-bookmark " => "icon-bookmark ", "icon-print " => "icon-print ", "icon-camera " => "icon-camera ", "icon-font " => "icon-font ", "icon-bold " => "icon-bold ", "icon-italic " => "icon-italic ", "icon-text-height " => "icon-text-height ", "icon-text-width " => "icon-text-width ", "icon-align-left " => "icon-align-left ", "icon-align-center " => "icon-align-center ", "icon-align-right " => "icon-align-right ", "icon-align-justify " => "icon-align-justify ", "icon-list " => "icon-list ", "icon-indent-left " => "icon-indent-left ", "icon-indent-right " => "icon-indent-right ", "icon-facetime-video " => "icon-facetime-video ", "icon-picture " => "icon-picture ", "icon-pencil " => "icon-pencil ", "icon-map-marker " => "icon-map-marker ", "icon-adjust " => "icon-adjust ", "icon-tint " => "icon-tint ", "icon-edit " => "icon-edit ", "icon-share " => "icon-share ", "icon-check " => "icon-check ", "icon-move " => "icon-move ", "icon-step-backward " => "icon-step-backward ", "icon-fast-backward " => "icon-fast-backward ", "icon-backward " => "icon-backward ", "icon-play " => "icon-play ", "icon-pause " => "icon-pause ", "icon-stop " => "icon-stop ", "icon-forward " => "icon-forward ", "icon-fast-forward " => "icon-fast-forward ", "icon-step-forward " => "icon-step-forward ", "icon-eject " => "icon-eject ", "icon-chevron-left " => "icon-chevron-left ", "icon-chevron-right " => "icon-chevron-right ", "icon-plus-sign " => "icon-plus-sign ", "icon-minus-sign " => "icon-minus-sign ", "icon-remove-sign " => "icon-remove-sign ", "icon-ok-sign " => "icon-ok-sign ", "icon-question-sign " => "icon-question-sign ", "icon-info-sign " => "icon-info-sign ", "icon-screenshot " => "icon-screenshot ", "icon-remove-circle " => "icon-remove-circle ", "icon-ok-circle " => "icon-ok-circle ", "icon-ban-circle " => "icon-ban-circle ", "icon-arrow-left " => "icon-arrow-left ", "icon-arrow-right " => "icon-arrow-right ", "icon-arrow-up " => "icon-arrow-up ", "icon-arrow-down " => "icon-arrow-down ", "icon-mail-forward:before" => "icon-mail-forward:before", "icon-share-alt " => "icon-share-alt ", "icon-resize-full " => "icon-resize-full ", "icon-resize-small " => "icon-resize-small ", "icon-plus " => "icon-plus ", "icon-minus " => "icon-minus ", "icon-asterisk " => "icon-asterisk ", "icon-exclamation-sign " => "icon-exclamation-sign ", "icon-gift " => "icon-gift ", "icon-leaf " => "icon-leaf ", "icon-fire " => "icon-fire ", "icon-eye-open " => "icon-eye-open ", "icon-eye-close " => "icon-eye-close ", "icon-warning-sign " => "icon-warning-sign ", "icon-plane " => "icon-plane ", "icon-calendar " => "icon-calendar ", "icon-random " => "icon-random ", "icon-comment " => "icon-comment ", "icon-magnet " => "icon-magnet ", "icon-chevron-up " => "icon-chevron-up ", "icon-chevron-down " => "icon-chevron-down ", "icon-retweet " => "icon-retweet ", "icon-shopping-cart " => "icon-shopping-cart ", "icon-folder-close " => "icon-folder-close ", "icon-folder-open " => "icon-folder-open ", "icon-resize-vertical " => "icon-resize-vertical ", "icon-resize-horizontal " => "icon-resize-horizontal ", "icon-bar-chart " => "icon-bar-chart ", "icon-twitter-sign " => "icon-twitter-sign ", "icon-facebook-sign " => "icon-facebook-sign ", "icon-camera-retro " => "icon-camera-retro ", "icon-key " => "icon-key ", "icon-gears" => "icon-gears", "icon-cogs " => "icon-cogs ", "icon-comments " => "icon-comments ", "icon-thumbs-up-alt " => "icon-thumbs-up-alt ", "icon-thumbs-down-alt " => "icon-thumbs-down-alt ", "icon-star-half " => "icon-star-half ", "icon-heart-empty " => "icon-heart-empty ", "icon-signout " => "icon-signout ", "icon-linkedin-sign " => "icon-linkedin-sign ", "icon-pushpin " => "icon-pushpin ", "icon-external-link " => "icon-external-link ", "icon-signin " => "icon-signin ", "icon-trophy " => "icon-trophy ", "icon-github-sign " => "icon-github-sign ", "icon-upload-alt " => "icon-upload-alt ", "icon-lemon " => "icon-lemon ", "icon-phone " => "icon-phone ", "icon-unchecked" => "icon-unchecked", "icon-check-empty " => "icon-check-empty ", "icon-bookmark-empty " => "icon-bookmark-empty ", "icon-phone-sign " => "icon-phone-sign ", "icon-twitter " => "icon-twitter ", "icon-facebook " => "icon-facebook ", "icon-github " => "icon-github ", "icon-unlock " => "icon-unlock ", "icon-credit-card " => "icon-credit-card ", "icon-rss " => "icon-rss ", "icon-hdd " => "icon-hdd ", "icon-bullhorn " => "icon-bullhorn ", "icon-bell " => "icon-bell ", "icon-certificate " => "icon-certificate ", "icon-hand-right " => "icon-hand-right ", "icon-hand-left " => "icon-hand-left ", "icon-hand-up " => "icon-hand-up ", "icon-hand-down " => "icon-hand-down ", "icon-circle-arrow-left " => "icon-circle-arrow-left ", "icon-circle-arrow-right " => "icon-circle-arrow-right ", "icon-circle-arrow-up " => "icon-circle-arrow-up ", "icon-circle-arrow-down " => "icon-circle-arrow-down ", "icon-globe " => "icon-globe ", "icon-wrench " => "icon-wrench ", "icon-tasks " => "icon-tasks ", "icon-filter " => "icon-filter ", "icon-briefcase " => "icon-briefcase ", "icon-fullscreen " => "icon-fullscreen ", "icon-group " => "icon-group ", "icon-link " => "icon-link ", "icon-cloud " => "icon-cloud ", "icon-beaker " => "icon-beaker ", "icon-cut " => "icon-cut ", "icon-copy " => "icon-copy ", "icon-paperclip" => "icon-paperclip", "icon-paper-clip " => "icon-paper-clip ", "icon-save " => "icon-save ", "icon-sign-blank " => "icon-sign-blank ", "icon-reorder " => "icon-reorder ", "icon-list-ul " => "icon-list-ul ", "icon-list-ol " => "icon-list-ol ", "icon-strikethrough " => "icon-strikethrough ", "icon-underline " => "icon-underline ", "icon-table " => "icon-table ", "icon-magic " => "icon-magic ", "icon-truck " => "icon-truck ", "icon-pinterest " => "icon-pinterest ", "icon-pinterest-sign " => "icon-pinterest-sign ", "icon-google-plus-sign " => "icon-google-plus-sign ", "icon-google-plus " => "icon-google-plus ", "icon-google-plus2 " => "icon-google-plus2 ", "icon-money " => "icon-money ", "icon-caret-down " => "icon-caret-down ", "icon-caret-up " => "icon-caret-up ", "icon-caret-left " => "icon-caret-left ", "icon-caret-right " => "icon-caret-right ", "icon-columns " => "icon-columns ", "icon-sort " => "icon-sort ", "icon-sort-down " => "icon-sort-down ", "icon-sort-up " => "icon-sort-up ", "icon-envelope " => "icon-envelope ", "icon-linkedin " => "icon-linkedin ", "icon-rotate-left" => "icon-rotate-left", "icon-undo " => "icon-undo ", "icon-legal " => "icon-legal ", "icon-dashboard " => "icon-dashboard ", "icon-comment-alt " => "icon-comment-alt ", "icon-comments-alt " => "icon-comments-alt ", "icon-bolt " => "icon-bolt ", "icon-sitemap " => "icon-sitemap ", "icon-umbrella " => "icon-umbrella ", "icon-paste " => "icon-paste ", "icon-lightbulb " => "icon-lightbulb ", "icon-exchange " => "icon-exchange ", "icon-cloud-download " => "icon-cloud-download ", "icon-cloud-upload " => "icon-cloud-upload ", "icon-user-md " => "icon-user-md ", "icon-stethoscope " => "icon-stethoscope ", "icon-suitcase " => "icon-suitcase ", "icon-bell-alt " => "icon-bell-alt ", "icon-coffee " => "icon-coffee ", "icon-food " => "icon-food ", "icon-file-text-alt " => "icon-file-text-alt ", "icon-building " => "icon-building ", "icon-hospital " => "icon-hospital ", "icon-ambulance " => "icon-ambulance ", "icon-medkit " => "icon-medkit ", "icon-fighter-jet " => "icon-fighter-jet ", "icon-beer " => "icon-beer ", "icon-h-sign " => "icon-h-sign ", "icon-plus-sign-alt " => "icon-plus-sign-alt ", "icon-double-angle-left " => "icon-double-angle-left ", "icon-double-angle-right " => "icon-double-angle-right ", "icon-double-angle-up " => "icon-double-angle-up ", "icon-double-angle-down " => "icon-double-angle-down ", "icon-angle-left " => "icon-angle-left ", "icon-angle-right " => "icon-angle-right ", "icon-angle-up " => "icon-angle-up ", "icon-angle-down " => "icon-angle-down ", "icon-desktop " => "icon-desktop ", "icon-laptop " => "icon-laptop ", "icon-tablet " => "icon-tablet ", "icon-mobile-phone " => "icon-mobile-phone ", "icon-circle-blank " => "icon-circle-blank ", "icon-quote-left " => "icon-quote-left ", "icon-quote-right " => "icon-quote-right ", "icon-spinner " => "icon-spinner ", "icon-circle " => "icon-circle ", "icon-mail-reply" => "icon-mail-reply", "icon-reply " => "icon-reply ", "icon-github-alt " => "icon-github-alt ", "icon-folder-close-alt " => "icon-folder-close-alt ", "icon-folder-open-alt " => "icon-folder-open-alt ", "icon-expand-alt " => "icon-expand-alt ", "icon-collapse-alt " => "icon-collapse-alt ", "icon-smile " => "icon-smile ", "icon-frown " => "icon-frown ", "icon-meh " => "icon-meh ", "icon-gamepad " => "icon-gamepad ", "icon-keyboard " => "icon-keyboard ", "icon-flag-alt " => "icon-flag-alt ", "icon-flag-checkered " => "icon-flag-checkered ", "icon-terminal " => "icon-terminal ", "icon-code " => "icon-code ", "icon-reply-all " => "icon-reply-all ", "icon-mail-reply-all " => "icon-mail-reply-all ", "icon-star-half-full" => "icon-star-half-full", "icon-star-half-empty " => "icon-star-half-empty ", "icon-location-arrow " => "icon-location-arrow ", "icon-crop " => "icon-crop ", "icon-code-fork " => "icon-code-fork ", "icon-unlink " => "icon-unlink ", "icon-question " => "icon-question ", "icon-info " => "icon-info ", "icon-exclamation " => "icon-exclamation ", "icon-superscript " => "icon-superscript ", "icon-subscript " => "icon-subscript ", "icon-eraser " => "icon-eraser ", "icon-puzzle-piece " => "icon-puzzle-piece ", "icon-microphone " => "icon-microphone ", "icon-microphone-off " => "icon-microphone-off ", "icon-shield " => "icon-shield ", "icon-calendar-empty " => "icon-calendar-empty ", "icon-fire-extinguisher " => "icon-fire-extinguisher ", "icon-rocket " => "icon-rocket ", "icon-maxcdn " => "icon-maxcdn ", "icon-chevron-sign-left " => "icon-chevron-sign-left ", "icon-chevron-sign-right " => "icon-chevron-sign-right ", "icon-chevron-sign-up " => "icon-chevron-sign-up ", "icon-chevron-sign-down " => "icon-chevron-sign-down ", "icon-html5 " => "icon-html5 ", "icon-css3 " => "icon-css3 ", "icon-anchor " => "icon-anchor ", "icon-unlock-alt " => "icon-unlock-alt ", "icon-bullseye " => "icon-bullseye ", "icon-ellipsis-horizontal " => "icon-ellipsis-horizontal ", "icon-ellipsis-vertical " => "icon-ellipsis-vertical ", "icon-rss-sign " => "icon-rss-sign ", "icon-play-sign " => "icon-play-sign ", "icon-ticket " => "icon-ticket ", "icon-minus-sign-alt " => "icon-minus-sign-alt ", "icon-check-minus " => "icon-check-minus ", "icon-level-up " => "icon-level-up ", "icon-level-down " => "icon-level-down ", "icon-check-sign " => "icon-check-sign ", "icon-edit-sign " => "icon-edit-sign ", "icon-external-link-sign " => "icon-external-link-sign ", "icon-share-sign " => "icon-share-sign ", "icon-compass " => "icon-compass ", "icon-collapse " => "icon-collapse ", "icon-collapse-top " => "icon-collapse-top ", "icon-expand " => "icon-expand ", "icon-euro" => "icon-euro", "icon-eur " => "icon-eur ", "icon-gbp " => "icon-gbp ", "icon-dollar" => "icon-dollar", "icon-usd " => "icon-usd ", "icon-rupee" => "icon-rupee", "icon-inr " => "icon-inr ", "icon-yen" => "icon-yen", "icon-jpy " => "icon-jpy ", "icon-renminbi" => "icon-renminbi", "icon-cny " => "icon-cny ", "icon-won" => "icon-won", "icon-krw " => "icon-krw ", "icon-bitcoin" => "icon-bitcoin", "icon-btc " => "icon-btc ", "icon-file " => "icon-file ", "icon-file-text " => "icon-file-text ", "icon-sort-by-alphabet " => "icon-sort-by-alphabet ", "icon-sort-by-alphabet-alt " => "icon-sort-by-alphabet-alt ", "icon-sort-by-attributes " => "icon-sort-by-attributes ", "icon-sort-by-attributes-alt " => "icon-sort-by-attributes-alt ", "icon-sort-by-order " => "icon-sort-by-order ", "icon-sort-by-order-alt " => "icon-sort-by-order-alt ", "icon-thumbs-up " => "icon-thumbs-up ", "icon-thumbs-down " => "icon-thumbs-down ", "icon-youtube-sign " => "icon-youtube-sign ", "icon-youtube " => "icon-youtube ", "icon-xing " => "icon-xing ", "icon-xing-sign " => "icon-xing-sign ", "icon-youtube-play " => "icon-youtube-play ", "icon-dropbox " => "icon-dropbox ", "icon-stackexchange " => "icon-stackexchange ", "icon-instagram " => "icon-instagram ", "icon-flickr " => "icon-flickr ", "icon-adn " => "icon-adn ", "icon-bitbucket " => "icon-bitbucket ", "icon-bitbucket-sign " => "icon-bitbucket-sign ", "icon-tumblr " => "icon-tumblr ", "icon-tumblr-sign " => "icon-tumblr-sign ", "icon-long-arrow-down " => "icon-long-arrow-down ", "icon-long-arrow-up " => "icon-long-arrow-up ", "icon-long-arrow-left " => "icon-long-arrow-left ", "icon-long-arrow-right " => "icon-long-arrow-right ", "icon-apple " => "icon-apple ", "icon-windows " => "icon-windows ", "icon-android " => "icon-android ", "icon-linux " => "icon-linux ", "icon-dribbble " => "icon-dribbble ", "icon-skype " => "icon-skype ", "icon-foursquare " => "icon-foursquare ", "icon-trello " => "icon-trello ", "icon-female " => "icon-female ", "icon-male " => "icon-male ", "icon-gittip " => "icon-gittip ", "icon-sun " => "icon-sun ", "icon-moon " => "icon-moon ", "icon-archive " => "icon-archive ", "icon-bug " => "icon-bug ", "icon-vk " => "icon-vk ", "icon-weibo " => "icon-weibo ", "icon-renren " => "icon-renren ",);
                    
                        $placeholder = (isset($this->field['placeholder']['icon_o'])) ? esc_attr($this->field['placeholder']['icon_o']) : __( 'Select an Icon', 'virtue' );
                        if ( isset( $this->field['select2'] ) ) { // if there are any let's pass them to js
                            $select2_params = json_encode( esc_attr( $this->field['select2'] ) );
                            $select2_params = htmlspecialchars( $select2_params , ENT_QUOTES);
                            echo '<input type="hidden" class="select2_params" value="'. $select2_params .'">';
                        }

                        echo '<select id="'.$this->field['id'].'-icon_o" data-placeholder="'.$placeholder.'" name="' . $this->field['name'] . '[' . $x . '][icon_o]" class="redux-select-item font-icons '.$this->field['class'].'" rows="6" style="width:93%;">';
                            echo '<option></option>';
                            foreach($icon_option as $k => $v){
                                if (is_array($slide['icon_o'])) {
                                    $selected = (is_array($slide['icon_o']) && in_array($k, $slide['icon_o']))?' selected="selected"':'';                   
                                } else {
                                    $selected = selected($slide['icon_o'], $k, false);
                                }
                                echo '<option value="'.$k.'"'.$selected.'>'.$v.'</option>';
                            }//foreach
                        echo '</select>'; 
   
                    echo '<ul id="' . $this->field['id'] . '-ul" class="redux-slides-list">';
                    echo '<li><input type="hidden" id="' . $this->field['id'] . '-url_' . $x . '" name="' . $this->field['name'] . '[' . $x . '][url]" value="' . esc_attr($slide['url']) . '" class="full-text upload" placeholder="'.__('URL', 'virtue').'" /></li>';
                    echo '<li><input type="text" id="' . $this->field['id'] . '-title_' . $x . '" name="' . $this->field['name'] . '[' . $x . '][title]" value="' . esc_attr($slide['title']) . '" placeholder="'.__('Title', 'virtue').'" class="full-text slide-title" /></li>';
                    echo '<li><textarea name="' . $this->field['name'] . '[' . $x . '][description]" id="' . $this->field['id'] . '-description_' . $x . '" placeholder="'.__('Description', 'virtue').'" class="large-text" rows="6">' . esc_attr($slide['description']) . '</textarea></li>';
                    echo '<li><input type="text" id="' . $this->field['id'] . '-link_' . $x . '" name="' . $this->field['name'] . '[' . $x . '][link]" value="' . esc_attr($slide['link']) . '" placeholder="'.__('Icon Link', 'virtue').'" class="full-text" /></li>';
                
                    echo '<li><label for="'. $this->field['id'] .  '-target_' . $x . '" class="icon-link-target">';
                    echo '<input type="checkbox" class="checkbox-slide-target" id="' . $this->field['id'] . '-target_' . $x . '" value="1" ' . checked(  $slide['target'], '1', false ) . ' name="' . $this->field['name'] . '[' . $x . '][target]" />';
                    echo ' '.__('Open Link in New Tab/Window', 'virtue'). '</label></li>';

                    echo '<li><input type="hidden" class="slide-sort" name="' . $this->field['name'] . '[' . $x . '][sort]" id="' . $this->field['id'] . '-sort_' . $x . '" value="' . $slide['sort'] . '" />';
                    echo '<li><input type="hidden" class="upload-id" name="' . $this->field['name'] . '[' . $x . '][attachment_id]" id="' . $this->field['id'] . '-image_id_' . $x . '" value="' . $slide['attachment_id'] . '" />';
                    echo '<input type="hidden" class="upload-thumbnail" name="' . $this->field['name'] . '[' . $x . '][thumb]" id="' . $this->field['id'] . '-thumb_url_' . $x . '" value="' . $slide['thumb'] . '" readonly="readonly" />';
                    echo '<input type="hidden" class="upload" name="' . $this->field['name'] . '[' . $x . '][image]" id="' . $this->field['id'] . '-image_url_' . $x . '" value="' . $slide['image'] . '" readonly="readonly" />';
                    echo '<input type="hidden" class="upload-height" name="' . $this->field['name'] . '[' . $x . '][height]" id="' . $this->field['id'] . '-image_height_' . $x . '" value="' . $slide['height'] . '" />';
                    echo '<input type="hidden" class="upload-width" name="' . $this->field['name'] . '[' . $x . '][width]" id="' . $this->field['id'] . '-image_width_' . $x . '" value="' . $slide['width'] . '" /></li>';


                    echo '<li><a href="javascript:void(0);" class="button deletion redux-slides-remove">' . __('Delete Icon', 'virtue') . '</a></li>';
                    echo '</ul></div></fieldset></div>';
                    $x++;
                
                }
            }

            if ($x == 0) {
                echo '<div class="redux-slides-accordion-group"><fieldset class="redux-field" data-id="'.$this->field['id'].'"><h3><span class="redux-slides-header">New Icon</span></h3><div>';

                $hide = ' hide';

                echo '<div class="screenshot' . $hide . '">';
                echo '<a class="of-uploaded-image" href="">';
                echo '<img class="redux-slides-image" id="image_image_id_' . $x . '" src="" alt="" target="_blank" rel="external" />';
                echo '</a>';
                echo '</div>';

                //Upload controls DIV
                echo '<div class="upload_button_div">';

                //If the user has WP3.5+ show upload/remove button
                echo '<span class="button media_upload_button" id="add_' . $x . '">' . __('Upload Icon', 'virtue') . '</span>';

                echo '<span class="button remove-image' . $hide . '" id="reset_' . $x . '" rel="' . $this->parent->args['opt_name'] . '[' . $this->field['id'] . '][attachment_id]">' . __('Remove', 'virtue') . '</span>';

                echo '</div>' . "\n";
                    $icon_option = array("icon-glass " => "icon-glass ", "icon-music " => "icon-music ", "icon-search " => "icon-search ", "icon-envelope-alt " => "icon-envelope-alt ", "icon-heart " => "icon-heart ", "icon-star " => "icon-star ", "icon-star-empty " => "icon-star-empty ", "icon-user " => "icon-user ", "icon-film " => "icon-film ", "icon-th-large " => "icon-th-large ", "icon-th " => "icon-th ", "icon-th-list " => "icon-th-list ", "icon-ok " => "icon-ok ", "icon-remove " => "icon-remove ", "icon-zoom-in " => "icon-zoom-in ", "icon-zoom-out " => "icon-zoom-out ", "icon-power-off" => "icon-power-off", "icon-off " => "icon-off ", "icon-signal " => "icon-signal ", "icon-gear" => "icon-gear", "icon-cog " => "icon-cog ", "icon-trash " => "icon-trash ", "icon-home " => "icon-home ", "icon-file-alt " => "icon-file-alt ", "icon-time " => "icon-time ", "icon-road " => "icon-road ", "icon-download-alt " => "icon-download-alt ", "icon-download " => "icon-download ", "icon-upload " => "icon-upload ", "icon-inbox " => "icon-inbox ", "icon-play-circle " => "icon-play-circle ", "icon-rotate-right" => "icon-rotate-right", "icon-repeat " => "icon-repeat ", "icon-refresh " => "icon-refresh ", "icon-list-alt " => "icon-list-alt ", "icon-lock " => "icon-lock ", "icon-flag " => "icon-flag ", "icon-headphones " => "icon-headphones ", "icon-volume-off " => "icon-volume-off ", "icon-volume-down " => "icon-volume-down ", "icon-volume-up " => "icon-volume-up ", "icon-qrcode " => "icon-qrcode ", "icon-barcode " => "icon-barcode ", "icon-tag " => "icon-tag ", "icon-tags " => "icon-tags ", "icon-book " => "icon-book ", "icon-bookmark " => "icon-bookmark ", "icon-print " => "icon-print ", "icon-camera " => "icon-camera ", "icon-font " => "icon-font ", "icon-bold " => "icon-bold ", "icon-italic " => "icon-italic ", "icon-text-height " => "icon-text-height ", "icon-text-width " => "icon-text-width ", "icon-align-left " => "icon-align-left ", "icon-align-center " => "icon-align-center ", "icon-align-right " => "icon-align-right ", "icon-align-justify " => "icon-align-justify ", "icon-list " => "icon-list ", "icon-indent-left " => "icon-indent-left ", "icon-indent-right " => "icon-indent-right ", "icon-facetime-video " => "icon-facetime-video ", "icon-picture " => "icon-picture ", "icon-pencil " => "icon-pencil ", "icon-map-marker " => "icon-map-marker ", "icon-adjust " => "icon-adjust ", "icon-tint " => "icon-tint ", "icon-edit " => "icon-edit ", "icon-share " => "icon-share ", "icon-check " => "icon-check ", "icon-move " => "icon-move ", "icon-step-backward " => "icon-step-backward ", "icon-fast-backward " => "icon-fast-backward ", "icon-backward " => "icon-backward ", "icon-play " => "icon-play ", "icon-pause " => "icon-pause ", "icon-stop " => "icon-stop ", "icon-forward " => "icon-forward ", "icon-fast-forward " => "icon-fast-forward ", "icon-step-forward " => "icon-step-forward ", "icon-eject " => "icon-eject ", "icon-chevron-left " => "icon-chevron-left ", "icon-chevron-right " => "icon-chevron-right ", "icon-plus-sign " => "icon-plus-sign ", "icon-minus-sign " => "icon-minus-sign ", "icon-remove-sign " => "icon-remove-sign ", "icon-ok-sign " => "icon-ok-sign ", "icon-question-sign " => "icon-question-sign ", "icon-info-sign " => "icon-info-sign ", "icon-screenshot " => "icon-screenshot ", "icon-remove-circle " => "icon-remove-circle ", "icon-ok-circle " => "icon-ok-circle ", "icon-ban-circle " => "icon-ban-circle ", "icon-arrow-left " => "icon-arrow-left ", "icon-arrow-right " => "icon-arrow-right ", "icon-arrow-up " => "icon-arrow-up ", "icon-arrow-down " => "icon-arrow-down ", "icon-mail-forward:before" => "icon-mail-forward:before", "icon-share-alt " => "icon-share-alt ", "icon-resize-full " => "icon-resize-full ", "icon-resize-small " => "icon-resize-small ", "icon-plus " => "icon-plus ", "icon-minus " => "icon-minus ", "icon-asterisk " => "icon-asterisk ", "icon-exclamation-sign " => "icon-exclamation-sign ", "icon-gift " => "icon-gift ", "icon-leaf " => "icon-leaf ", "icon-fire " => "icon-fire ", "icon-eye-open " => "icon-eye-open ", "icon-eye-close " => "icon-eye-close ", "icon-warning-sign " => "icon-warning-sign ", "icon-plane " => "icon-plane ", "icon-calendar " => "icon-calendar ", "icon-random " => "icon-random ", "icon-comment " => "icon-comment ", "icon-magnet " => "icon-magnet ", "icon-chevron-up " => "icon-chevron-up ", "icon-chevron-down " => "icon-chevron-down ", "icon-retweet " => "icon-retweet ", "icon-shopping-cart " => "icon-shopping-cart ", "icon-folder-close " => "icon-folder-close ", "icon-folder-open " => "icon-folder-open ", "icon-resize-vertical " => "icon-resize-vertical ", "icon-resize-horizontal " => "icon-resize-horizontal ", "icon-bar-chart " => "icon-bar-chart ", "icon-twitter-sign " => "icon-twitter-sign ", "icon-facebook-sign " => "icon-facebook-sign ", "icon-camera-retro " => "icon-camera-retro ", "icon-key " => "icon-key ", "icon-gears" => "icon-gears", "icon-cogs " => "icon-cogs ", "icon-comments " => "icon-comments ", "icon-thumbs-up-alt " => "icon-thumbs-up-alt ", "icon-thumbs-down-alt " => "icon-thumbs-down-alt ", "icon-star-half " => "icon-star-half ", "icon-heart-empty " => "icon-heart-empty ", "icon-signout " => "icon-signout ", "icon-linkedin-sign " => "icon-linkedin-sign ", "icon-pushpin " => "icon-pushpin ", "icon-external-link " => "icon-external-link ", "icon-signin " => "icon-signin ", "icon-trophy " => "icon-trophy ", "icon-github-sign " => "icon-github-sign ", "icon-upload-alt " => "icon-upload-alt ", "icon-lemon " => "icon-lemon ", "icon-phone " => "icon-phone ", "icon-unchecked" => "icon-unchecked", "icon-check-empty " => "icon-check-empty ", "icon-bookmark-empty " => "icon-bookmark-empty ", "icon-phone-sign " => "icon-phone-sign ", "icon-twitter " => "icon-twitter ", "icon-facebook " => "icon-facebook ", "icon-github " => "icon-github ", "icon-unlock " => "icon-unlock ", "icon-credit-card " => "icon-credit-card ", "icon-rss " => "icon-rss ", "icon-hdd " => "icon-hdd ", "icon-bullhorn " => "icon-bullhorn ", "icon-bell " => "icon-bell ", "icon-certificate " => "icon-certificate ", "icon-hand-right " => "icon-hand-right ", "icon-hand-left " => "icon-hand-left ", "icon-hand-up " => "icon-hand-up ", "icon-hand-down " => "icon-hand-down ", "icon-circle-arrow-left " => "icon-circle-arrow-left ", "icon-circle-arrow-right " => "icon-circle-arrow-right ", "icon-circle-arrow-up " => "icon-circle-arrow-up ", "icon-circle-arrow-down " => "icon-circle-arrow-down ", "icon-globe " => "icon-globe ", "icon-wrench " => "icon-wrench ", "icon-tasks " => "icon-tasks ", "icon-filter " => "icon-filter ", "icon-briefcase " => "icon-briefcase ", "icon-fullscreen " => "icon-fullscreen ", "icon-group " => "icon-group ", "icon-link " => "icon-link ", "icon-cloud " => "icon-cloud ", "icon-beaker " => "icon-beaker ", "icon-cut " => "icon-cut ", "icon-copy " => "icon-copy ", "icon-paperclip" => "icon-paperclip", "icon-paper-clip " => "icon-paper-clip ", "icon-save " => "icon-save ", "icon-sign-blank " => "icon-sign-blank ", "icon-reorder " => "icon-reorder ", "icon-list-ul " => "icon-list-ul ", "icon-list-ol " => "icon-list-ol ", "icon-strikethrough " => "icon-strikethrough ", "icon-underline " => "icon-underline ", "icon-table " => "icon-table ", "icon-magic " => "icon-magic ", "icon-truck " => "icon-truck ", "icon-pinterest " => "icon-pinterest ", "icon-pinterest-sign " => "icon-pinterest-sign ", "icon-google-plus-sign " => "icon-google-plus-sign ", "icon-google-plus " => "icon-google-plus ",  "icon-google-plus2 " => "icon-google-plus2 ", "icon-money " => "icon-money ", "icon-caret-down " => "icon-caret-down ", "icon-caret-up " => "icon-caret-up ", "icon-caret-left " => "icon-caret-left ", "icon-caret-right " => "icon-caret-right ", "icon-columns " => "icon-columns ", "icon-sort " => "icon-sort ", "icon-sort-down " => "icon-sort-down ", "icon-sort-up " => "icon-sort-up ", "icon-envelope " => "icon-envelope ", "icon-linkedin " => "icon-linkedin ", "icon-rotate-left" => "icon-rotate-left", "icon-undo " => "icon-undo ", "icon-legal " => "icon-legal ", "icon-dashboard " => "icon-dashboard ", "icon-comment-alt " => "icon-comment-alt ", "icon-comments-alt " => "icon-comments-alt ", "icon-bolt " => "icon-bolt ", "icon-sitemap " => "icon-sitemap ", "icon-umbrella " => "icon-umbrella ", "icon-paste " => "icon-paste ", "icon-lightbulb " => "icon-lightbulb ", "icon-exchange " => "icon-exchange ", "icon-cloud-download " => "icon-cloud-download ", "icon-cloud-upload " => "icon-cloud-upload ", "icon-user-md " => "icon-user-md ", "icon-stethoscope " => "icon-stethoscope ", "icon-suitcase " => "icon-suitcase ", "icon-bell-alt " => "icon-bell-alt ", "icon-coffee " => "icon-coffee ", "icon-food " => "icon-food ", "icon-file-text-alt " => "icon-file-text-alt ", "icon-building " => "icon-building ", "icon-hospital " => "icon-hospital ", "icon-ambulance " => "icon-ambulance ", "icon-medkit " => "icon-medkit ", "icon-fighter-jet " => "icon-fighter-jet ", "icon-beer " => "icon-beer ", "icon-h-sign " => "icon-h-sign ", "icon-plus-sign-alt " => "icon-plus-sign-alt ", "icon-double-angle-left " => "icon-double-angle-left ", "icon-double-angle-right " => "icon-double-angle-right ", "icon-double-angle-up " => "icon-double-angle-up ", "icon-double-angle-down " => "icon-double-angle-down ", "icon-angle-left " => "icon-angle-left ", "icon-angle-right " => "icon-angle-right ", "icon-angle-up " => "icon-angle-up ", "icon-angle-down " => "icon-angle-down ", "icon-desktop " => "icon-desktop ", "icon-laptop " => "icon-laptop ", "icon-tablet " => "icon-tablet ", "icon-mobile-phone " => "icon-mobile-phone ", "icon-circle-blank " => "icon-circle-blank ", "icon-quote-left " => "icon-quote-left ", "icon-quote-right " => "icon-quote-right ", "icon-spinner " => "icon-spinner ", "icon-circle " => "icon-circle ", "icon-mail-reply" => "icon-mail-reply", "icon-reply " => "icon-reply ", "icon-github-alt " => "icon-github-alt ", "icon-folder-close-alt " => "icon-folder-close-alt ", "icon-folder-open-alt " => "icon-folder-open-alt ", "icon-expand-alt " => "icon-expand-alt ", "icon-collapse-alt " => "icon-collapse-alt ", "icon-smile " => "icon-smile ", "icon-frown " => "icon-frown ", "icon-meh " => "icon-meh ", "icon-gamepad " => "icon-gamepad ", "icon-keyboard " => "icon-keyboard ", "icon-flag-alt " => "icon-flag-alt ", "icon-flag-checkered " => "icon-flag-checkered ", "icon-terminal " => "icon-terminal ", "icon-code " => "icon-code ", "icon-reply-all " => "icon-reply-all ", "icon-mail-reply-all " => "icon-mail-reply-all ", "icon-star-half-full" => "icon-star-half-full", "icon-star-half-empty " => "icon-star-half-empty ", "icon-location-arrow " => "icon-location-arrow ", "icon-crop " => "icon-crop ", "icon-code-fork " => "icon-code-fork ", "icon-unlink " => "icon-unlink ", "icon-question " => "icon-question ", "icon-info " => "icon-info ", "icon-exclamation " => "icon-exclamation ", "icon-superscript " => "icon-superscript ", "icon-subscript " => "icon-subscript ", "icon-eraser " => "icon-eraser ", "icon-puzzle-piece " => "icon-puzzle-piece ", "icon-microphone " => "icon-microphone ", "icon-microphone-off " => "icon-microphone-off ", "icon-shield " => "icon-shield ", "icon-calendar-empty " => "icon-calendar-empty ", "icon-fire-extinguisher " => "icon-fire-extinguisher ", "icon-rocket " => "icon-rocket ", "icon-maxcdn " => "icon-maxcdn ", "icon-chevron-sign-left " => "icon-chevron-sign-left ", "icon-chevron-sign-right " => "icon-chevron-sign-right ", "icon-chevron-sign-up " => "icon-chevron-sign-up ", "icon-chevron-sign-down " => "icon-chevron-sign-down ", "icon-html5 " => "icon-html5 ", "icon-css3 " => "icon-css3 ", "icon-anchor " => "icon-anchor ", "icon-unlock-alt " => "icon-unlock-alt ", "icon-bullseye " => "icon-bullseye ", "icon-ellipsis-horizontal " => "icon-ellipsis-horizontal ", "icon-ellipsis-vertical " => "icon-ellipsis-vertical ", "icon-rss-sign " => "icon-rss-sign ", "icon-play-sign " => "icon-play-sign ", "icon-ticket " => "icon-ticket ", "icon-minus-sign-alt " => "icon-minus-sign-alt ", "icon-check-minus " => "icon-check-minus ", "icon-level-up " => "icon-level-up ", "icon-level-down " => "icon-level-down ", "icon-check-sign " => "icon-check-sign ", "icon-edit-sign " => "icon-edit-sign ", "icon-external-link-sign " => "icon-external-link-sign ", "icon-share-sign " => "icon-share-sign ", "icon-compass " => "icon-compass ", "icon-collapse " => "icon-collapse ", "icon-collapse-top " => "icon-collapse-top ", "icon-expand " => "icon-expand ", "icon-euro" => "icon-euro", "icon-eur " => "icon-eur ", "icon-gbp " => "icon-gbp ", "icon-dollar" => "icon-dollar", "icon-usd " => "icon-usd ", "icon-rupee" => "icon-rupee", "icon-inr " => "icon-inr ", "icon-yen" => "icon-yen", "icon-jpy " => "icon-jpy ", "icon-renminbi" => "icon-renminbi", "icon-cny " => "icon-cny ", "icon-won" => "icon-won", "icon-krw " => "icon-krw ", "icon-bitcoin" => "icon-bitcoin", "icon-btc " => "icon-btc ", "icon-file " => "icon-file ", "icon-file-text " => "icon-file-text ", "icon-sort-by-alphabet " => "icon-sort-by-alphabet ", "icon-sort-by-alphabet-alt " => "icon-sort-by-alphabet-alt ", "icon-sort-by-attributes " => "icon-sort-by-attributes ", "icon-sort-by-attributes-alt " => "icon-sort-by-attributes-alt ", "icon-sort-by-order " => "icon-sort-by-order ", "icon-sort-by-order-alt " => "icon-sort-by-order-alt ", "icon-thumbs-up " => "icon-thumbs-up ", "icon-thumbs-down " => "icon-thumbs-down ", "icon-youtube-sign " => "icon-youtube-sign ", "icon-youtube " => "icon-youtube ", "icon-xing " => "icon-xing ", "icon-xing-sign " => "icon-xing-sign ", "icon-youtube-play " => "icon-youtube-play ", "icon-dropbox " => "icon-dropbox ", "icon-stackexchange " => "icon-stackexchange ", "icon-instagram " => "icon-instagram ", "icon-flickr " => "icon-flickr ", "icon-adn " => "icon-adn ", "icon-bitbucket " => "icon-bitbucket ", "icon-bitbucket-sign " => "icon-bitbucket-sign ", "icon-tumblr " => "icon-tumblr ", "icon-tumblr-sign " => "icon-tumblr-sign ", "icon-long-arrow-down " => "icon-long-arrow-down ", "icon-long-arrow-up " => "icon-long-arrow-up ", "icon-long-arrow-left " => "icon-long-arrow-left ", "icon-long-arrow-right " => "icon-long-arrow-right ", "icon-apple " => "icon-apple ", "icon-windows " => "icon-windows ", "icon-android " => "icon-android ", "icon-linux " => "icon-linux ", "icon-dribbble " => "icon-dribbble ", "icon-skype " => "icon-skype ", "icon-foursquare " => "icon-foursquare ", "icon-trello " => "icon-trello ", "icon-female " => "icon-female ", "icon-male " => "icon-male ", "icon-gittip " => "icon-gittip ", "icon-sun " => "icon-sun ", "icon-moon " => "icon-moon ", "icon-archive " => "icon-archive ", "icon-bug " => "icon-bug ", "icon-vk " => "icon-vk ", "icon-weibo " => "icon-weibo ", "icon-renren " => "icon-renren ",);
                    
                        $placeholder = (isset($this->field['placeholder']['icon_o'])) ? esc_attr($this->field['placeholder']['icon_o']) : __( 'Select an Icon', 'virtue' );
                        if ( isset( $this->field['select2'] ) ) { // if there are any let's pass them to js
                            $select2_params = json_encode( esc_attr( $this->field['select2'] ) );
                            $select2_params = htmlspecialchars( $select2_params , ENT_QUOTES);
                            echo '<input type="hidden" class="select2_params" value="'. $select2_params .'">';
                        }

                        echo '<select '.$multi.' id="'.$this->field['id'].'-select" data-placeholder="'.$placeholder.'" name="' . $this->field['name'] . '[' . $x . '][icon_o]" class="redux-select-item font-icons '.$this->field['class'].'" rows="6" style="width:93%;">';
                            echo '<option></option>';
                            foreach($icon_option as $k => $v){
                                if (is_array($this->value)) {
                                    $selected = (is_array($this->value) && in_array($k, $this->value))?' selected="selected"':'';                   
                                } else {
                                    $selected = selected($this->value, $k, false);
                                }
                                echo '<option value="'.$k.'"'.$selected.'>'.$v.'</option>';
                            }//foreach
                        echo '</select>';                           
                echo '<ul id="' . $this->field['id'] . '-ul" class="redux-slides-list">';
                $placeholder = (isset($this->field['placeholder']['url'])) ? esc_attr($this->field['placeholder']['url']) : __( 'URL', 'virtue' );
                echo '<li><input type="hidden" id="' . $this->field['id'] . '-url_' . $x . '" name="' . $this->field['name'] . '[' . $x . '][url]" value="" class="full-text upload" placeholder="'.$placeholder.'" /></li>';
                $placeholder = (isset($this->field['placeholder']['title'])) ? esc_attr($this->field['placeholder']['title']) : __( 'Title', 'virtue' );
                echo '<li><input type="text" id="' . $this->field['id'] . '-title_' . $x . '" name="' . $this->field['name'] . '[' . $x . '][title]" value="" placeholder="'.$placeholder.'" class="full-text slide-title" /></li>';
                $placeholder = (isset($this->field['placeholder']['description'])) ? esc_attr($this->field['placeholder']['description']) : __( 'Description', 'virtue' );
                echo '<li><textarea name="' . $this->field['name'] . '[' . $x . '][description]" id="' . $this->field['id'] . '-description_' . $x . '" placeholder="'.$placeholder.'" class="large-text" rows="6"></textarea></li>';
                $placeholder = (isset($this->field['placeholder']['link'])) ? esc_attr($this->field['placeholder']['link']) : __( 'Icon Link', 'virtue' );
                echo '<li><input type="text" id="' . $this->field['id'] . '-link_' . $x . '" name="' . $this->field['name'] . '[' . $x . '][link]" value="" placeholder="'.$placeholder.'" class="full-text" /></li>';
                
                echo '<li><label for="'. $this->field['id'] .  '-target_' . $x . '">';
                echo '<input type="checkbox" class="checkbox-slide-target" id="' . $this->field['id'] . '-target_' . $x . '" value="" ' . checked(  '', '1', false ) . ' name="' . $this->field['name'] . '[' . $x . '][target]" />';
                echo ' '.__('Open Link in New Tab/Window', 'virtue'). '</label></li>';

                echo '<li><input type="hidden" class="slide-sort" name="' . $this->field['name'] . '[' . $x . '][sort]" id="' . $this->field['id'] . '-sort_' . $x . '" value="' . $x . '" />';
                echo '<li><input type="hidden" class="upload-id" name="' . $this->field['name'] . '[' . $x . '][attachment_id]" id="' . $this->field['id'] . '-image_id_' . $x . '" value="" />';
                echo '<input type="hidden" class="upload" name="' . $this->field['name'] . '[' . $x . '][url]" id="' . $this->field['id'] . '-image_url_' . $x . '" value="" readonly="readonly" />';
                echo '<input type="hidden" class="upload-height" name="' . $this->field['name'] . '[' . $x . '][height]" id="' . $this->field['id'] . '-image_height_' . $x . '" value="" />';
                echo '<input type="hidden" class="upload-width" name="' . $this->field['name'] . '[' . $x . '][width]" id="' . $this->field['id'] . '-image_width_' . $x . '" value="" /></li>';


                echo '<li><a href="javascript:void(0);" class="button deletion redux-slides-remove">' . __('Delete Icon', 'virtue') . '</a></li>';
                echo '</ul></div></fieldset></div>';
            }
            echo '</div><a href="javascript:void(0);" class="button redux-slides-add2 kad_redux-icon-add button-primary" rel-id="' . $this->field['id'] . '-ul" rel-name="' . $this->field['name'] . '[title][]">' . __('Add Icon', 'virtue') . '</a><br/>';
            
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
                'kad-field-icons-js', 
                get_template_directory_uri() . '/themeoptions/options/extensions/kad_icons/kad_icons/field_kad_icons-min.js', 
                array( 'jquery', 'jquery-ui-core', 'jquery-ui-accordion', 'wp-color-picker' ), 
                time (), 
                true
            );

            wp_enqueue_style (
                'kad-field-icons-css', 
                get_template_directory_uri() . '/themeoptions/options/extensions/kad_icons/kad_icons/field_kad_icons.css', 
                time (), 
                true
            );
        }              

    }
}
