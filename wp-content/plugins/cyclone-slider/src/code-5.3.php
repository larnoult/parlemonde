<?php
// Used in ImageResizer
try {
	$editor = \CycloneSlider\Grafika\Grafika::createEditor();
	$editor->open($image_file);
	if ('fill' == $resize_option) {
		$editor->resizeFill($width, $height);
	} else if ('crop' == $resize_option) {
		$editor->crop($width, $height);
	} else if ('exact' == $resize_option) {
		$editor->resizeExact($width, $height);
	} else if ('exactHeight' == $resize_option) {
		$editor->resizeExactHeight($height);
	} else if ('exactWidth' == $resize_option) {
		$editor->resizeExactWidth($width);
	} else {
		$editor->resizeFit($width, $height);
	}
	$editor->save($image_file_dest, null, $resize_quality);
	return true;
} catch (Exception $e){
	return false;
}
