<?php


namespace CycloneSlider\Grafika\Imagick\ImageHash;

use CycloneSlider\Grafika\Imagick\Editor;
use CycloneSlider\Grafika\Imagick\Image;

/**
 * DifferenceHash
 *
 * Algorithm:
 * Reduce size. The fastest way to remove high frequencies and detail is to shrink the image. In this case, shrink it to 9x8 so that there are 72 total pixels.
 * Reduce color. Convert the image to a grayscale picture. This changes the hash from 72 pixels to a total of 72 colors.
 * Compute the difference. The algorithm works on the difference between adjacent pixels. This identifies the relative gradient direction. In this case, the 9 pixels per row yields 8 differences between adjacent pixels. Eight rows of eight differences becomes 64 bits.
 * Assign bits. Each bit is simply set based on whether the left pixel is brighter than the right pixel.
 *
 * http://www.hackerfactor.com/blog/index.php?/archives/529-Kind-of-Like-That.html
 *
 * @package Grafika\Imagick\ImageHash
 */
class DifferenceHash
{

    /**
     * Generate and get the difference hash of image.
     * @param Image $image
     *
     * @return string
     */
    public function hash(Image $image)
    {
        $width  = 9;
        $height = 8;

        $editor = new Editor();
        $editor->setImage($image);
        $editor->resizeExact($width, $height); // Resize to exactly 9x8
        $imagick = $editor->getImage()->getCore();

        // Build hash
        $hash = '';
        for ($y = 0; $y < $height; $y++) {
            // Get the pixel value for the leftmost pixel.
            $rgba = $imagick->getImagePixelColor(0, $y)->getColor();

            $left = floor(($rgba['r'] + $rgba['g'] + $rgba['b']) / 3);
            for ($x = 1; $x < $width; $x++) {
                // Get the pixel value for each pixel starting from position 1.
                $rgba  = $imagick->getImagePixelColor($x, $y)->getColor();
                $right = floor(($rgba['r'] + $rgba['g'] + $rgba['b']) / 3);
                // Each hash bit is set based on whether the left pixel is brighter than the right pixel.
                if ($left > $right) {
                    $hash .= '1';
                } else {
                    $hash .= '0';
                }
                // Prepare the next loop.
                $left = $right;
            }
        }

        return $hash;
    }
}