<?php

namespace CycloneSlider\Grafika\Imagick\Filter;

use CycloneSlider\Grafika\FilterInterface;
use CycloneSlider\Grafika\Imagick\Image;

/**
 * Turn image into grayscale.
 */
class Grayscale implements FilterInterface{

    /**
     * @param Image $image
     *
     * @return Image
     */
    public function apply( $image ) {
        $image->getCore()->modulateImage(100, 0, 100);
        return $image;
    }

}