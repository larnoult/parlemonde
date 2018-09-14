<?php   

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class sliding_mp_slider
{
    function getResizedImage($inputImage, $resizePath, $resizeUrl)
    {
        $extension = end(explode('.', $inputImage));
        $myimage_filename = end(explode('/', $inputImage));
        
        $image = new sliding_mp_simpleImage();
        $image->load($inputImage);
        
        //get sizes
        $height = $image->getHeight();
        $width = $image->getWidth();
        if ($height >= $width)
            $image->resizeToHeight(400);
        else           
            $image->resizeToWidth(400);
        
        $newFileUrl = $resizeUrl.'/'.$myimage_filename;
        $image->save($resizePath.'/'.$myimage_filename);
        return($newFileUrl);

    }

    function getUniqueCode($length = "") 
    {
        $code = md5(uniqid(rand(), true));
        if ($length != "")
            return substr($code, 0, $length);
        else return $code;
    }

}

class sliding_mp_simpleImage {
 
   var $image;
   var $image_type;
 
   function load($filename) {
      $filename = str_replace(' ', '%20', $filename);
      $image_info = getimagesize($filename);
      $this->image_type = $image_info[2];
      if( $this->image_type == IMAGETYPE_JPEG ) {
 
         $this->image = imagecreatefromjpeg($filename);
      } elseif( $this->image_type == IMAGETYPE_GIF ) {
 
         $this->image = imagecreatefromgif($filename);
      } elseif( $this->image_type == IMAGETYPE_PNG ) {
 
         $this->image = imagecreatefrompng($filename);
      }
   }
   function isImage($url) {
        $params = array('http' => array('method' => 'HEAD'));
        $ctx = stream_context_create($params);
        $fp = @fopen($url, 'rb', false, $ctx);
        if (!$fp)
            return false;  // Problem with url      
        $meta = stream_get_meta_data($fp);     
        if ($meta === false) {         
            fclose($fp);         
            return false;  // Problem reading data from url     
        }      
        $wrapper_data = $meta["wrapper_data"];     
        if(is_array($wrapper_data)) {       
            foreach(array_keys($wrapper_data) as $hh) {           
                if (substr($wrapper_data[$hh], 0, 19) == "Content-Type: image") // strlen("Content-Type: image") == 19            
                {             
                    fclose($fp);             
                    return true;           
                }       
            }     
        }      
        fclose($fp);     
        return false;
   }
   function save($filename, $image_type=IMAGETYPE_JPEG, $compression=100, $permissions=null) {
 
      if( $image_type == IMAGETYPE_JPEG ) {
         $saved = imagejpeg($this->image,$filename,$compression);
      } elseif( $image_type == IMAGETYPE_GIF ) {
         imagegif($this->image,$filename);
      } elseif( $image_type == IMAGETYPE_PNG ) {
         imagepng($this->image,$filename);
      }
      if( $permissions != null) {
 
         chmod($filename,$permissions);
      }
      
   }
   function output($image_type=IMAGETYPE_JPEG) {
 
      if( $image_type == IMAGETYPE_JPEG ) {
         imagejpeg($this->image);
      } elseif( $image_type == IMAGETYPE_GIF ) {
 
         imagegif($this->image);
      } elseif( $image_type == IMAGETYPE_PNG ) {
 
         imagepng($this->image);
      }
   }
   function getWidth() {
 
      return imagesx($this->image);
   }
   function getHeight() {
 
      return imagesy($this->image);
   }
   function resizeToHeight($height) {
 
      $ratio = $height / $this->getHeight();
      $width = $this->getWidth() * $ratio;
      $this->resize($width,$height);
   }
 
   function resizeToWidth($width) {
      $ratio = $width / $this->getWidth();
      $height = $this->getheight() * $ratio;
      $this->resize($width,$height);
   }
 
   function scale($scale) {
      $width = $this->getWidth() * $scale/100;
      $height = $this->getheight() * $scale/100;
      $this->resize($width,$height);
   }
 
   function resize($width,$height) {
      $new_image = imagecreatetruecolor($width, $height);
      imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
      $this->image = $new_image;
   }      
 
}

?>
