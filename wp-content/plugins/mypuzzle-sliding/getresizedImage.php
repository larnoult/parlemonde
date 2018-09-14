<?php 
function testRange($int,$min,$max){     return ($int>=$min && $int<=$max); }

if (!isset($_GET["imageUrl"])){$url = "../jigsaw/img";} else {$url = $_GET["imageUrl"];}
if (!isset($_GET["resizePath"])){$resizePath = "../sliding/img/resize";} else {$resizePath = $_GET["resizePath"];}
if (!isset($_GET["maxWidth"])){$width = "400";} else {$width = $_GET["maxWidth"];}
if (!isset($_GET["maxHeight"])){$height = "400";} else {$height = $_GET["maxHeight"];}

//include_once("/sliding/sliding-plugin.php");

//$mySlider = new slider();

//$myPic = $mySlider->getResizedImage(imageUrl);
//$myTemplate->set('startPicture', $myPic);


$file_path = parse_url( $url );
$file_path = $_SERVER['DOCUMENT_ROOT'] . $file_path['path'];
$orig_size = getimagesize( $url );
		
$image_src[0] = $img_url;
$image_src[1] = $orig_size[0];
$image_src[2] = $orig_size[1];

$file_info = pathinfo( $url );
$extension = '.'. $file_info['extension'];

// the image path without the extension
$no_ext_path = $file_info['dirname'].'/'.$file_info['filename'];

//calculate new dimensions
$orig_width = $image_src[1];
$orig_height = $image_src[2];
$origRatio = $orig_width / $orig_height;
$maxRatio = $width / $height;
//echo('origRatio='.$origRatio.',maxRatio='.$maxRatio.'<br/>');
$bSizeHeight = false;

if ($origRatio < $maxRatio) {
    $ratio = $height / $orig_height;
    $newWidth = intval($orig_width * $ratio);
    $newHeight = $height;
    $bSizeHeight = true;
} else {
    $ratio = $width / $orig_width;
    $newHeight = intval($orig_height * $ratio);
    $newWidth = $width;
    $bSizeHeight = false;
}
//echo('Width orig:'.$orig_width.',max:'.$width.',new:'.$newWidth.'<br/>');
//echo('Height orig:'.$orig_height.',max:'.$height.',new:'.$newHeight.'<br/>');

//get resized file-name
$newfilename = $file_info['filename'].'-'.$newWidth.'x'.$newHeight.$extension;
$upload_path = $resizePath.'/'.$newfilename;

if ($newHeight == $orig_height && $newWidth = $orig_width) {
    $vt_image = array (
            'url' => $resizePathUrl.'/'.$file_info['filename'].$extension,
            'file' => $file_info['filename'].$extension,
            'width' => $newWidth,
            'height' => $newHeight
    );

    echo json_encode($vt_image);
    die();
}

if ( file_exists( $upload_path ) ) {

        $vt_image = array (
                'url' => $upload_path,
                'file' => $newfilename,
                'width' => $newWidth,
                'height' => $newHeight
        );

        echo json_encode($vt_image);
} else {
    
    //load image
    $myImage = new SimpleImage();
    $myImage->load($url);
    if ($orig_height > $orig_width) {
        $myImage->resizeToHeight($height);
    }
    
    $myImage->resize($newWidth, $newHeight);
    $myImage->save($upload_path);

    // resized output
    $vt_image = array (
            'url' => $upload_path,
            'file' => $newfilename,
            'width' => $myImage->getWidth(),
            'height' => $myImage->getHeight()
    );
    echo json_encode($vt_image);
}

class SimpleImage {
 
    var $image;
    var $image_type;

    function load($filename) {

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
    function save($filename, $image_type=IMAGETYPE_JPEG, $compression=100, $permissions=null) {

        if( $image_type == IMAGETYPE_JPEG ) {
            imagejpeg($this->image,$filename,$compression);
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