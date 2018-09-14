<?php
/*
 * Created on 09.09.2012
 * @author Thomas Seidel
 */
include_once ("wordsearch-plugin.php");


$myWordsearch = new WordSearch($_GET['words'], $_GET['dim']);
//for ($i = 0; $i < count($myWordsearch->wordList); $i++) {
//    echo ($myWordsearch->wordList[$i]."<br/>");
//}
echo $myWordsearch->getGridData();

?>