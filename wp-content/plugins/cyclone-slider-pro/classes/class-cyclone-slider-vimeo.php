<?php
if(!class_exists('Cyclone_Slider_Vimeo')):
    
    /**
    * Class for handling Vimeo slides
    */
    class Cyclone_Slider_Vimeo {
        
        /**
         * Get Vimeo ID
         * 
         * Return vimeo video id
         *
         * @param string $url URL of to parse
         *
         * @return int|false Vimeo ID on success and false on fail
         */
        public function get_vimeo_id($url){
            
            $parsed_url = parse_url($url);
            if ($parsed_url['host'] == 'vimeo.com'){
                $vimeo_id = ltrim( $parsed_url['path'], '/');
                if (is_numeric($vimeo_id)) {
                    return $vimeo_id;
                }
            }
            return false;
        }
        
        /**
         * Get vimeo video thumbnail image
         *
         * @param int Vimeo ID.
         * @param string Size can be: thumbnail_small, thumbnail_medium, thumbnail_large.
         *
         * @return string URL of thumbnail image.
         */
        public function get_vimeo_thumb($video_id, $size = 'small'){
            if(!empty($video_id)){
                $data = @file_get_contents('http://vimeo.com/api/v2/video/'.$video_id.'.php');
                if( $data ) {
                    $vimeo = unserialize( $data );
                    if( isset($vimeo[0]['thumbnail_'.$size]) ){
                        return $vimeo[0]['thumbnail_'.$size];
                    }
                }
            }
            return '';
        }
    } // end class
    
endif;