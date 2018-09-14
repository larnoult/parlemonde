<?php 
header('Content-Type: application/json');

if (!isset($_GET["dir"]) || $_GET["dir"]==''){$dir = "gallery";} else {$dir = $_GET["dir"];}
if (!isset($_GET["cards"]) || $_GET["cards"]==0){$cards = 4;} else {$cards = $_GET["cards"];}

function listDirImages($dir, $cards) { //$dir is the name of the directory you want to list the images.
    
    $preg = "/.(jpg|gif|png|jpeg)/i"; //match the following files, can be changed to limit or extend range, ie: png,jpeg,etc.
    $images = array();
    $id = 0;
    if( $checkDir = opendir($dir) ) {
        while( $file = readdir($checkDir) ) {
            if(preg_match($preg, $file)) {
                if( !is_dir($dir . "/" . $file) ) {
                    //$images[basename($file)]= $file;
                    $images[$id]= $file;
                    $id++;
                }
            }
        }
    }
    $memoryCards = array ();
    
    for ($y = 0; $y < $cards; $y++)
    {
        $num = rand(0, count($images)-1 );
        array_push($memoryCards, $images[$num]);
        array_splice($images, $num, 1);
    }
    $x = count($memoryCards);
    for ($y = 0; $y < $x; $y++)
    {
        $memoryCards[] = $memoryCards[$y];
    }
    shuffle($memoryCards);shuffle($memoryCards);
    $data = json_encode($memoryCards);
    echo $_GET['callback'] . '(' . $data . ');';
}

listDirImages($dir, $cards); //call function ../jigsaw/img

?>
