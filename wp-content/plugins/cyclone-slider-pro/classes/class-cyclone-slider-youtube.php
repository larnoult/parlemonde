<?php
if(!class_exists('Cyclone_Slider_Youtube')):
    
    /**
    * Class for handling youtube slides
    */
    class Cyclone_Slider_Youtube {
    
        /**
         * Initialize
         */
        public function __construct() {
            
        
            // Add hook for ajax operations if logged in
            //add_action( 'wp_ajax_cycloneslider_check_youtube_url', array( $this, 'cycloneslider_check_youtube_url' ) );
            
            
        } // end constructor
        
        /**
         * Ajax for checking youtube url
         *
         * @return void Prints json data
         */
        public function cycloneslider_check_youtube_url(){
            $url = $_POST['url'];
            
            $retval = array(
                'success' => false
            );
            
            if (filter_var($url, FILTER_VALIDATE_URL) !== FALSE) {
                if( $id = $this->get_youtube_id($url) ){
                    $retval['success'] = true;
                    $retval['id'] = $id;
                    $retval['v_url'] = "http://www.youtube.com/v/{$id}";
                }
            }
            
            echo json_encode($retval);
            die();
        }
        
        /**
         * Get youtube ID from different url formats
         *
         * @param string $url Youtube url
         * @return string|boolean Youtube URL on success or boolean false on fail
         */
        public function get_youtube_id($url){
            if (filter_var($url, FILTER_VALIDATE_URL) === FALSE) {
                return false;
            }
            $parsed_url = parse_url($url);
           
            if(strpos($parsed_url['host'], 'youtube.com')!==false){
                if(strpos($parsed_url['path'], '/watch')!==false){ // Regular url Eg. http://www.youtube.com/watch?v=9bZkp7q19f0
                    parse_str($parsed_url['query'], $parsed_str);
                    if(isset($parsed_str['v']) and !empty($parsed_str['v'])){
                        return $parsed_str['v'];
                    }
                } else if(strpos($parsed_url['path'], '/v/')!==false){ // "v" URL http://www.youtube.com/v/9bZkp7q19f0?version=3&autohide=1
                    $id = str_replace('/v/','',$parsed_url['path']);
                    if( !empty($id) ){
                        return $id;
                    }
                } else if(strpos($parsed_url['path'], '/embed/')!==false){ // Embed URL: http://www.youtube.com/embed/9bZkp7q19f0
                    return str_replace('/embed/','',$parsed_url['path']);
                }
            } else if(strpos($parsed_url['host'], 'youtu.be')!==false){ // Shortened URL: http://youtu.be/9bZkp7q19f0
                return str_replace('/','',$parsed_url['path']);
            }
            
            return false;
        }
        
        
        /**
         * Get youtube video thumbnail image
         *
         * @param int string $video_id YouTube ID
         * @param string $size Can be: small, medium, large
         *
         * @return string URL of thumbnail image.
         */
        public function get_youtube_thumb( $video_id, $size = 'small' ){
            if( 'large' == $size ){
                return 'http://img.youtube.com/vi/'.$video_id.'/hqdefault.jpg';
            } else if( 'medium' == $size ){
                return 'http://img.youtube.com/vi/'.$video_id.'/mqdefault.jpg';
            }
            return 'http://img.youtube.com/vi/'.$video_id.'/default.jpg';
        }
    } // end class
    
endif;