<?php

namespace CycloneSlider\Grafika\Imagick;

use CycloneSlider\Grafika\DrawingObjectInterface;
use CycloneSlider\Grafika\EditorInterface;
use CycloneSlider\Grafika\FilterInterface;
use CycloneSlider\Grafika\Grafika;
use CycloneSlider\Grafika\ImageInterface;
use CycloneSlider\Grafika\ImageType;
use CycloneSlider\Grafika\Color;
use CycloneSlider\Grafika\Imagick\ImageHash\DifferenceHash;

/**
 * Imagick Editor class. Uses the PHP Imagick library.
 * @package Grafika\Imagick
 */
final class Editor implements EditorInterface
{

    /**
     * @var Image Holds the image instance.
     */
    private $image;

    /**
     * Constructor.
     */
    function __construct()
    {
        $this->image = null;
    }

    /**
     * Apply a filter to the image. See Filters section for a list of available filters.
     *
     * @param FilterInterface $filter
     *
     * @return Editor
     */
    public function apply($filter)
    {
        $this->_imageCheck();

        if ($this->image->isAnimated()) { // Ignore animated GIF for now
            return $this;
        }

        $this->image = $filter->apply($this->image);

        return $this;
    }

    /**
     * Create a blank image given width and height.
     *
     * @param int $width Width of image in pixels.
     * @param int $height Height of image in pixels.
     *
     * @return self
     */
    public function blank($width, $height)
    {
        $this->image = Image::createBlank($width, $height);

        return $this;
    }

    /**
     * Compare two images and returns a hamming distance. A value of 0 indicates a likely similar picture. A value between 1 and 10 is potentially a variation. A value greater than 10 is likely a different image.
     *
     * @param ImageInterface|string $image1
     * @param ImageInterface|string $image2
     *
     * @return int Hamming distance. Note: This breaks the chain if you are doing fluent api calls as it does not return an Editor.
     * @throws \Exception
     */
    public function compare($image1, $image2)
    {

        if (is_string($image1)) { // If string passed, turn it into a Image object
            $image1 = Image::createFromFile($image1);
            $image1->flatten();
        }

        if (is_string($image2)) { // If string passed, turn it into a Image object
            $image2 = Image::createFromFile($image2);
            $image2->flatten();
        }

        $hash = new DifferenceHash();

        $bin1     = $hash->hash($image1);
        $bin2     = $hash->hash($image2);
        $str1     = str_split($bin1);
        $str2     = str_split($bin2);
        $distance = 0;
        foreach ($str1 as $i => $char) {
            if ($char !== $str2[$i]) {
                $distance++;
            }
        }

        return $distance;

    }

    /**
     * Crop the image to the given dimension and position.
     *
     * @param int $cropWidth Crop width in pixels.
     * @param int $cropHeight Crop Height in pixels.
     * @param string $position The crop position. Possible values top-left, top-center, top-right, center-left, center, center-right, bottom-left, bottom-center, bottom-right and smart. Defaults to center.
     * @param int $offsetX Number of pixels to add to the X position of the crop.
     * @param int $offsetY Number of pixels to add to the Y position of the crop.
     *
     * @return Editor
     * @throws \Exception
     */
    public function crop($cropWidth, $cropHeight, $position = 'center', $offsetX = 0, $offsetY = 0)
    {

        if ($this->image->isAnimated()) { // Ignore animated GIF for now
            return $this;
        }

        if ('top-left' === $position) {
            $x = 0;
            $y = 0;
        } else if ('top-center' === $position) {
            $x = (int)round(($this->image->getWidth() / 2) - ($cropWidth / 2));
            $y = 0;
        } else if ('top-right' === $position) {
            $x = $this->image->getWidth() - $cropWidth;
            $y = 0;
        } else if ('center-left' === $position) {
            $x = 0;
            $y = (int)round(($this->image->getHeight() / 2) - ($cropHeight / 2));
        } else if ('center-right' === $position) {
            $x = $this->image->getWidth() - $cropWidth;
            $y = (int)round(($this->image->getHeight() / 2) - ($cropHeight / 2));
        } else if ('bottom-left' === $position) {
            $x = 0;
            $y = $this->image->getHeight() - $cropHeight;
        } else if ('bottom-center' === $position) {
            $x = (int)round(($this->image->getWidth() / 2) - ($cropWidth / 2));
            $y = $this->image->getHeight() - $cropHeight;
        } else if ('bottom-right' === $position) {
            $x = $this->image->getWidth() - $cropWidth;
            $y = $this->image->getHeight() - $cropHeight;
        } else if ('smart' === $position) { // Smart crop
            list($x, $y) = $this->_smartCrop($cropWidth, $cropHeight);
        } else if ('center' === $position) {
            $x = (int)round(($this->image->getWidth() / 2) - ($cropWidth / 2));
            $y = (int)round(($this->image->getHeight() / 2) - ($cropHeight / 2));
        } else {
            throw new \Exception('Invalid parameter position.');
        }

        $x += $offsetX;
        $y += $offsetY;

        $this->image->getCore()->cropImage($cropWidth, $cropHeight, $x, $y);

        return $this;
    }

    /**
     * Draw a DrawingObject on the image. See Drawing Objects section.
     *
     * @param DrawingObjectInterface $drawingObject
     *
     * @return $this
     */
    public function draw($drawingObject)
    {
        $this->_imageCheck();

        if ($this->image->isAnimated()) { // Ignore animated GIF for now
            return $this;
        }

        $this->image = $drawingObject->draw($this->image);

        return $this;
    }

    /**
     * Compare if two images are equal. It will compare if the two images are of the same width and height. If the dimensions differ, it will return false. If the dimensions are equal, it will loop through each pixels. If one of the pixel don't match, it will return false. The pixels are compared using their RGB (Red, Green, Blue) values.
     *
     * @param string|ImageInterface $image1 Can be an instance of Image or string containing the file system path to image.
     * @param string|ImageInterface $image2 Can be an instance of Image or string containing the file system path to image.
     *
     * @return bool True if equals false if not. Note: This breaks the chain if you are doing fluent api calls as it does not return an Editor.
     * @throws \Exception
     */
    public function equal($image1, $image2)
    {

        if (is_string($image1)) { // If string passed, turn it into a Image object
            $image1 = Image::createFromFile($image1);
            $image1->flatten();
        }

        if (is_string($image2)) { // If string passed, turn it into a Image object
            $image2 = Image::createFromFile($image2);
            $image2->flatten();
        }

        // Check if image dimensions are equal
        if ($image1->getWidth() !== $image2->getWidth() or $image1->getHeight() !== $image2->getHeight()) {

            return false;

        } else {

            // Loop using image1
            $pixelIterator = $image1->getCore()->getPixelIterator();
            foreach ($pixelIterator as $row => $pixels) { /* Loop through pixel rows */
                foreach ($pixels as $column => $pixel) { /* Loop through the pixels in the row (columns) */
                    /**
                     * Get image1 pixel
                     * @var $pixel \ImagickPixel
                     */
                    $rgba1 = $pixel->getColor();

                    // Get image2 pixel
                    $rgba2 = $image2->getCore()->getImagePixelColor($column, $row)->getColor();

                    // Compare pixel value
                    if (
                        $rgba1['r'] !== $rgba2['r'] or
                        $rgba1['g'] !== $rgba2['g'] or
                        $rgba1['b'] !== $rgba2['b']
                    ) {
                        return false;
                    }
                }
                $pixelIterator->syncIterator(); /* Sync the iterator, this is important to do on each iteration */
            }
        }

        return true;
    }

    /**
     * Fill entire image with color.
     *
     * @param Color $color Color object
     * @param int $x X-coordinate of start point
     * @param int $y Y-coordinate of start point
     *
     * @return self
     */
    public function fill($color, $x = 0, $y = 0)
    {

        $this->_imageCheck();

        if ($this->image->isAnimated()) { // Ignore animated GIF for now
            return $this;
        }

        $target = $this->image->getCore()->getImagePixelColor($x, $y);
        $this->image->getCore()->floodfillPaintImage($color->getHexString(), 1, $target, $x, $y, false);

        return $this;
    }

    /**
     * Flatten if animated GIF. Do nothing otherwise.
     *
     * @return self
     */
    public function flatten(){
        $this->_imageCheck();
        $this->image->flatten();
        return $this;
    }

    /**
     * Flip or mirrors the image.
     *
     * @param string $mode The type of flip: 'h' for horizontal flip or 'v' for vertical.
     *
     * @return Editor
     * @throws \Exception
     */
    public function flip($mode){
        $this->_imageCheck();
        if ($mode === 'h') {
            $this->image->getCore()->flopImage();
        } else if ($mode === 'v') {
            $this->image->getCore()->flipImage();
        } else {
            throw new \Exception(sprintf('Unsupported mode "%s"', $mode));
        }
        return $this;
    }

    /**
     * Free the current image clearing resources associated with it.
     */
    public function free()
    {
        if (null !== $this->image) {
            if (null !== $this->image->getCore()) {
                $this->image->getCore()->clear();
            }
        }
        $this->image = null;
    }

    /**
     * Get image instance.
     *
     * @param bool $byRef True to return image by reference or false to return a copy. Defaults to copy.
     *
     * @return Image
     */
    public function getImage($byRef=false)
    {
        $this->_imageCheck();
        if($byRef){
            return $this->image;
        }
        return clone $this->image;
    }

    /**
     * Checks if the editor is available on the current PHP install.
     *
     * @return bool True if available false if not.
     */
    public function isAvailable()
    {
        // First, test Imagick's extension and classes.
        if (false === extension_loaded('imagick') ||
            false === class_exists('Imagick') ||
            false === class_exists('ImagickDraw') ||
            false === class_exists('ImagickPixel') ||
            false === class_exists('ImagickPixelIterator')
        ) {
            return false;
        }

        return true;
    }

    /**
     * Sets the image to the specified opacity level where 1.0 is fully opaque and 0.0 is fully transparent.
     *
     * @param float $opacity
     *
     * @return self
     * @throws \Exception
     */
    public function opacity($opacity)
    {

        $this->_imageCheck();

        if ($this->image->isAnimated()) { // Ignore animated GIF for now
            return $this;
        }

        // Bounds checks
        $opacity = ($opacity > 1) ? 1 : $opacity;
        $opacity = ($opacity < 0) ? 0 : $opacity;

        $this->image->getCore()->setImageOpacity($opacity);

        return $this;
    }

    /**
     * Opens an image file for manipulation specified by $target.
     *
     * @param mixed $target Can be an instance of Image or a string containing file system path to the image.
     *
     * @return Editor
     * @throws \Exception
     */
    public function open($target)
    {
        if ($target instanceof ImageInterface) {
            $this->openImage($target);
        } else if (is_string($target)) {
            $this->openFile($target);
        } else {
            throw new \Exception('Could not open image.');
        }

        return $this;
    }

    /**
     * Open an image by passing an instance of Image.
     *
     * @param ImageInterface $image
     *
     * @return $this
     */
    public function openImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Open an image by passing a file system path.
     *
     * @param string $file A full path to the image in the file system.
     *
     * @return $this
     * @throws \Exception
     */
    public function openFile($file)
    {
        $this->image = Image::createFromFile($file);

        return $this;
    }

    /**
     * Overlay an image on top of the current image.
     *
     * @param Image|string $overlay Can be a string containing a file path of the image to overlay or an Image object.
     * @param string|int $xPos Horizontal position of image. Can be 'left','center','right' or integer number. Defaults to 'center'.
     * @param string|int $yPos Vertical position of image. Can be 'top', 'center','bottom' or integer number. Defaults to 'center'.
     * @param null $width
     * @param null $height
     *
     * @return Editor
     * @throws \Exception
     */
    public function overlay($overlay, $xPos = 'center', $yPos = 'center', $width = null, $height = null)
    {

        $this->_imageCheck();

        if ($this->image->isAnimated()) { // Ignore animated GIF for now
            return $this;
        }

        if (is_string($overlay)) { // If string passed, turn it into a Image object
            $overlay = Image::createFromFile($overlay);
        }

        // Resize overlay
        if ($width and $height) {

            $overlayWidth  = $overlay->getWidth();
            $overlayHeight = $overlay->getHeight();

            if (is_numeric($width)) {
                $overlayWidth = (int)$width;
            } else {
                $percent = strpos($width, '%');
                if (false !== $percent) {
                    $overlayWidth = intval($width) / 100 * $this->image->getWidth();
                }
            }

            if (is_numeric($height)) {
                $overlayHeight = (int)$height;
            } else {
                $percent = strpos($height, '%');
                if (false !== $percent) {
                    $overlayHeight = intval($height) / 100 * $this->image->getHeight();
                }
            }

            $editor = new Editor();
            $editor->setImage($overlay);
            $editor->resizeFit($overlayWidth, $overlayHeight);
            $overlay = $editor->getImage();
        }

        //$x = $y = 0;

        if (is_string($xPos)) {
            // Compute position from string
            switch ($xPos) {
                case 'left':
                    $x = 0;
                    break;

                case 'right':
                    $x = $this->image->getWidth() - $overlay->getWidth();
                    break;

                case 'center':
                default:
                    $x = (int)round(($this->image->getWidth() / 2) - ($overlay->getWidth() / 2));
                    break;
            }
        } else {
            $x = $xPos;
        }

        if (is_string($yPos)) {
            switch ($yPos) {
                case 'top':
                    $y = 0;
                    break;

                case 'bottom':
                    $y = $this->image->getHeight() - $overlay->getHeight();
                    break;

                case 'center':
                default:
                    $y = (int)round(($this->image->getHeight() / 2) - ($overlay->getHeight() / 2));
                    break;
            }
        } else {
            $y = $yPos;
        }

        // Overlay the image on the original image
        $this->image->getCore()->compositeImage($overlay->getCore(), \Imagick::COMPOSITE_OVER, $x, $y);

        return $this;

    }

    /**
     * Wrapper function for the resizeXXX family of functions. Resize image given width, height and mode.
     *
     * @param int $newWidth Width in pixels.
     * @param int $newHeight Height in pixels.
     * @param string $mode Resize mode. Possible values: "exact", "exactHeight", "exactWidth", "fill", "fit".
     *
     * @return Editor
     * @throws \Exception
     */
    public function resize($newWidth, $newHeight, $mode = 'fit')
    {
        /*
         * Resize formula:
         * ratio = w / h
         * h = w / ratio
         * w = h * ratio
         */
        switch ($mode) {
            case 'exact':
                $this->resizeExact($newWidth, $newHeight);
                break;
            case 'fill':
                $this->resizeFill($newWidth, $newHeight);
                break;
            case 'exactWidth':
                $this->resizeExactWidth($newWidth);
                break;
            case 'exactHeight':
                $this->resizeExactHeight($newHeight);
                break;
            case 'fit':
                $this->resizeFit($newWidth, $newHeight);
                break;
            default:
                throw new \Exception(sprintf('Invalid resize mode "%s".', $mode));
        }

        return $this;
    }

    /**
     * Resize image to exact dimensions ignoring aspect ratio. Useful if you want to force exact width and height.
     *
     * @param int $newWidth Width in pixels.
     * @param int $newHeight Height in pixels.
     *
     * @return self
     */
    public function resizeExact($newWidth, $newHeight)
    {

        $this->_resize($newWidth, $newHeight);

        return $this;
    }

    /**
     * Resize image to exact height. Width is auto calculated. Useful for creating row of images with the same height.
     *
     * @param int $newHeight Height in pixels.
     *
     * @return self
     */
    public function resizeExactHeight($newHeight)
    {

        $width  = $this->image->getWidth();
        $height = $this->image->getHeight();
        $ratio  = $width / $height;

        $resizeHeight = $newHeight;
        $resizeWidth  = $newHeight * $ratio;

        $this->_resize($resizeWidth, $resizeHeight);

        return $this;
    }

    /**
     * Resize image to exact width. Height is auto calculated. Useful for creating column of images with the same width.
     *
     * @param int $newWidth Width in pixels.
     *
     * @return self
     */
    public function resizeExactWidth($newWidth)
    {

        $width  = $this->image->getWidth();
        $height = $this->image->getHeight();
        $ratio  = $width / $height;

        $resizeWidth  = $newWidth;
        $resizeHeight = round($newWidth / $ratio);

        $this->_resize($resizeWidth, $resizeHeight);

        return $this;
    }

    /**
     * Resize image to fill all the space in the given dimension. Excess parts are cropped.
     *
     * @param int $newWidth Width in pixels.
     * @param int $newHeight Height in pixels.
     *
     * @return self
     */
    public function resizeFill($newWidth, $newHeight)
    {
        $width  = $this->image->getWidth();
        $height = $this->image->getHeight();
        $ratio  = $width / $height;

        // Base optimum size on new width
        $optimumWidth  = $newWidth;
        $optimumHeight = round($newWidth / $ratio);

        if (($optimumWidth < $newWidth) or ($optimumHeight < $newHeight)) { // Oops, where trying to fill and there are blank areas
            // So base optimum size on height instead
            $optimumWidth  = $newHeight * $ratio;
            $optimumHeight = $newHeight;
        }

        $this->_resize($optimumWidth, $optimumHeight);
        $this->crop($newWidth, $newHeight); // Trim excess parts

        return $this;
    }

    /**
     * Resize image to fit inside the given dimension. No part of the image is lost.
     *
     * @param int $newWidth Width in pixels.
     * @param int $newHeight Height in pixels.
     *
     * @return self
     */
    public function resizeFit($newWidth, $newHeight)
    {

        $width  = $this->image->getWidth();
        $height = $this->image->getHeight();
        $ratio  = $width / $height;

        // Try basing it on width first
        $resizeWidth  = $newWidth;
        $resizeHeight = round($newWidth / $ratio);

        if (($resizeWidth > $newWidth) or ($resizeHeight > $newHeight)) { // Oops, either with or height does not fit
            // So base on height instead
            $resizeHeight = $newHeight;
            $resizeWidth  = $newHeight * $ratio;
        }

        $this->_resize($resizeWidth, $resizeHeight);

        return $this;
    }

    /**
     * Rotate an image counter-clockwise.
     *
     * @param int $angle The angle in degrees.
     * @param Color|null $color The Color object containing the background color.
     *
     * @return EditorInterface An instance of image editor.
     */
    public function rotate($angle, $color = null)
    {

        $this->_imageCheck();

        if ($this->image->isAnimated()) { // Ignore animated GIF for now
            return $this;
        }

        $color = ($color !== null) ? $color : new Color('#000000');
        list($r, $g, $b, $alpha) = $color->getRgba();

        $this->image->getCore()->rotateImage(new \ImagickPixel("rgba($r, $g, $b, $alpha)"), $angle * -1);

        return $this;
    }

    /**
     * Save the image to an image format.
     *
     * @param string $file File path where to save the image.
     * @param null|string $type Type of image. Can be null, "GIF", "PNG", or "JPEG".
     * @param null|string $quality Quality of image. Applies to JPEG only. Accepts number 0 - 100 where 0 is lowest and 100 is the highest quality. Or null for default.
     * @param bool|false $interlace Set to true for progressive JPEG. Applies to JPEG only.
     * @param int $permission Default permission when creating non-existing target directory.
     *
     * @return Editor
     * @throws \Exception
     */
    public function save($file, $type = null, $quality = null, $interlace = false, $permission = 0755)
    {

        $this->_imageCheck();

        if (null === $type) {

            $type = $this->_getImageTypeFromFileName($file); // Null given, guess type from file extension
            if (ImageType::UNKNOWN === $type) {
                $type = $this->image->getType(); // 0 result, use original image type
            }
        }

        $targetDir = dirname($file); // $file's directory
        if (false === is_dir($targetDir)) { // Check if $file's directory exist
            // Create and set default perms to 0755
            if ( ! mkdir($targetDir, $permission, true)) {
                throw new \Exception(sprintf('Cannot create %s', $targetDir));
            }
        }

        switch (strtoupper($type)) {
            case ImageType::GIF :
                $this->image->getCore()->writeImages($file, true); // Support animated image. Eg. GIF
                break;

            case ImageType::PNG :
                // PNG is lossless and does not need compression. Although GD allow values 0-9 (0 = no compression), we leave it alone.
                $this->image->getCore()->setImageFormat($type);
                $this->image->getCore()->writeImage($file);
                break;

            default: // Defaults to jpeg
                $quality = ($quality === null) ? 75 : $quality; // Default to 75 (GDs default) if null.
                $quality = ($quality > 100) ? 100 : $quality;
                $quality = ($quality <= 0) ? 1 : $quality; // Note: If 0 change it to 1. The lowest quality in Imagick is 1 whereas in GD its 0.

                if ($interlace) {
                    $this->image->getCore()->setImageInterlaceScheme(\Imagick::INTERLACE_JPEG);
                }
                $this->image->getCore()->setImageFormat($type);
                $this->image->getCore()->setImageCompression(\Imagick::COMPRESSION_JPEG);
                $this->image->getCore()->setImageCompressionQuality($quality);
                $this->image->getCore()->writeImage($file); // Single frame image. Eg. JPEG
        }

        return $this;
    }

    /**
     * Set image instance.
     *
     * @param Image $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

    /**
     * Write text to image.
     *
     * @param string $text The text to be written.
     * @param int $size The font size. Defaults to 12.
     * @param int $x The distance from the left edge of the image to the left of the text. Defaults to 0.
     * @param int $y The distance from the top edge of the image to the top of the text. Defaults to 12 (equal to font size) so that the text is placed within the image.
     * @param Color $color The Color object. Default text color is black.
     * @param string $font Full path to font file. If blank, will default to Liberation Sans font.
     * @param int $angle Angle of text from 0 - 359. Defaults to 0.
     *
     * @return EditorInterface
     * @throws \Exception
     */
    public function text($text, $size = 12, $x = 0, $y = 0, $color = null, $font = '', $angle = 0)
    {

        $this->_imageCheck();

        if ($this->image->isAnimated()) { // Ignore animated GIF for now
            return $this;
        }

        $y += $size;

        $color = ($color !== null) ? $color : new Color('#000000');
        $font  = ($font !== '') ? $font : Grafika::fontsDir() . DIRECTORY_SEPARATOR . 'LiberationSans-Regular.ttf';

        list($r, $g, $b, $alpha) = $color->getRgba();

        // Set up draw properties
        $draw = new \ImagickDraw();
        // Text color
        $draw->setFillColor(new \ImagickPixel("rgba($r, $g, $b, $alpha)"));
        // Font properties
        $draw->setFont($font);
        $draw->setFontSize($size);

        // Write text
        $this->image->getCore()->annotateImage(
            $draw,
            $x,
            $y,
            $angle,
            $text
        );

        return $this;
    }

    /**
     * Get histogram from an entire image or its sub-region of image.
     *
     * @param array|null $slice Array of slice information. array( array( 0,0), array(100,50)) means x,y is 0,0 and width,height is 100,50
     *
     * @return array Returns array containing RGBA bins array('r'=>array(), 'g'=>array(), 'b'=>array(), 'a'=>array())
     */
    function histogram($slice = null)
    {
        $this->_imageCheck();

        if(null === $slice){
            $sliceX = 0;
            $sliceY = 0;
            $sliceW = $this->image->getWidth();
            $sliceH = $this->image->getHeight();
        } else {
            $sliceX = $slice[0][0];
            $sliceY = $slice[0][1];
            $sliceW = $slice[1][0];
            $sliceH = $slice[1][1];
        }

        $rBin = array();
        $gBin = array();
        $bBin = array();
        $aBin = array();

        // Loop using image1
        $pixelIterator = $this->image->getCore()->getPixelIterator();
        foreach ($pixelIterator as $y => $rows) { /* Loop through pixel rows */
            if($y >= $sliceY and $y < $sliceY+$sliceH) {
                foreach ($rows as $x => $px) { /* Loop through the pixels in the row (columns) */
                    if($x >= $sliceX and $x < $sliceX+$sliceW) {
                        /**
                         * @var $px \ImagickPixel */
                        $pixel = $px->getColor();
                        $r = $pixel['r'];
                        $g = $pixel['g'];
                        $b = $pixel['b'];
                        $a = $pixel['a'];

                        if ( ! isset($rBin[$r])) {
                            $rBin[$r] = 1;
                        } else {
                            $rBin[$r]++;
                        }

                        if ( ! isset($gBin[$g])) {
                            $gBin[$g] = 1;
                        } else {
                            $gBin[$g]++;
                        }

                        if ( ! isset($bBin[$b])) {
                            $bBin[$b] = 1;
                        } else {
                            $bBin[$b]++;
                        }

                        if ( ! isset($aBin[$a])) {
                            $aBin[$a] = 1;
                        } else {
                            $aBin[$a]++;
                        }
                    }
                }
            }
        }
        return array(
            'r' => $rBin,
            'g' => $gBin,
            'b' => $bBin,
            'a' => $aBin
        );
    }

    /**
     * Calculate entropy based on histogram.
     *
     * @param $hist
     *
     * @return float|int
     */
    function entropy($hist){
        $entropy = 0;
        $hist_size = array_sum($hist['r']) + array_sum($hist['g']) + array_sum($hist['b']);
        foreach($hist['r'] as $p){
            $p = $p / $hist_size;
            $entropy += $p * log($p, 2);
        }
        foreach($hist['g'] as $p){
            $p = $p / $hist_size;
            $entropy += $p * log($p, 2);
        }
        foreach($hist['b'] as $p){
            $p = $p / $hist_size;
            $entropy += $p * log($p, 2);
        }
        return $entropy * -1;
    }

    /**
     * Crop based on entropy.
     *
     * @param $cropW
     * @param $cropH
     *
     * @return array
     */
    private function _smartCrop($cropW, $cropH){
        $image = clone $this->image;

        $editor = new Editor();
        $editor->setImage($image);
        $editor->resizeFit(30, 30);

        $origW = $this->getImage()->getWidth();
        $origH = $this->getImage()->getHeight();
        $resizeW = $editor->getImage()->getWidth();
        $resizeH = $editor->getImage()->getHeight();

        $smallCropW = round(($resizeW / $origW) * $cropW);
        $smallCropH = round(($resizeH / $origH) * $cropH);

        $step = 1;

        for($y = 0; $y < $resizeH-$smallCropH; $y+=$step){
            for($x = 0; $x < $resizeW-$smallCropW; $x+=$step){
                $hist[$x.'-'.$y] = $this->entropy($editor->histogram(array(array($x, $y), array($smallCropW, $smallCropH))));
            }
            if($resizeW-$smallCropW <= 0){
                $hist['0-'.$y] = $this->entropy($editor->histogram(array(array(0, 0), array($smallCropW, $smallCropH))));
            }
        }
        if($resizeH-$smallCropH <= 0){
            $hist['0-0'] = $this->entropy($editor->histogram(array(array(0, 0), array($smallCropW, $smallCropH))));
        }

        asort($hist);
        end($hist);
        $pos = key($hist); // last key
        list($x, $y) = explode('-', $pos);
        $x = round($x*($origW / $resizeW));
        $y = round($y*($origH / $resizeH));

        return array($x,$y);
    }

    /**
     * Resize helper function.
     *
     * @param int $newWidth
     * @param int $newHeight
     *
     * @return self
     * @throws \Exception
     */
    private function _resize($newWidth, $newHeight)
    {
        $this->_imageCheck();

        if ('GIF' == $this->image->getType()) { // Animated image. Eg. GIF

            $imagick = $this->image->getCore()->coalesceImages();

            foreach ($imagick as $frame) {
                $frame->resizeImage($newWidth, $newHeight, \Imagick::FILTER_BOX, 1, false);
                $frame->setImagePage($newWidth, $newHeight, 0, 0);
            }

            // Assign new image with frames
            $this->image = new Image($imagick->deconstructImages(), $this->image->getImageFile(), $newWidth, $newHeight,
                $this->image->getType());
        } else { // Single frame image. Eg. JPEG, PNG

            $this->image->getCore()->resizeImage($newWidth, $newHeight, \Imagick::FILTER_LANCZOS, 1, false);
            // Assign new image
            $this->image = new Image($this->image->getCore(), $this->image->getImageFile(), $newWidth, $newHeight,
                $this->image->getType());
        }

    }

    /**
     * Get image type base on file extension.
     *
     * @param int $imageFile File path to image.
     *
     * @return ImageType string Type of image.
     */
    private function _getImageTypeFromFileName($imageFile)
    {
        $ext = strtolower((string)pathinfo($imageFile, PATHINFO_EXTENSION));

        if ('jpg' == $ext or 'jpeg' == $ext) {
            return ImageType::JPEG;
        } else if ('gif' == $ext) {
            return ImageType::GIF;
        } else if ('png' == $ext) {
            return ImageType::PNG;
        } else {
            return ImageType::UNKNOWN;
        }
    }

    /**
     * Check if editor has already been assigned an image.
     *
     * @throws \Exception
     */
    private function _imageCheck()
    {
        if (null === $this->image) {
            throw new \Exception('No image to edit.');
        }
    }

}