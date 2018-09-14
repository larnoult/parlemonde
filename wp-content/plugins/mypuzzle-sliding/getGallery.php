<?php 
header('Content-Type: application/json');

if (!isset($_GET["dir"]) || $_GET["dir"]==''){$dir = "gallery";} else {$dir = $_GET["dir"];}

function listDirImages($dir) { //$dir is the name of the directory you want to list the images.
    
    $preg = "/.(jpg|gif|png|jpeg)/i"; //match the following files, can be changed to limit or extend range, ie: png,jpeg,etc.
    $images = array();
    $id = 0;
    if( $checkDir = opendir($dir) ) {
        while( $file = readdir($checkDir) ) {
            if(preg_match($preg, $file)) {
                if( !is_dir($dir . "/" . $file) ) {
                    $images[basename($file)]= $file;
                    $id++;
                }
            }
        }
    }
    $data = json_encode($images);
    echo $_GET['callback'] . '(' . $data . ');';
}

listDirImages($dir); //call function ../jigsaw/img

?>
