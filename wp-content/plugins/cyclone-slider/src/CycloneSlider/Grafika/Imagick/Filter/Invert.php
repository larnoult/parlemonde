<?php

namespace CycloneSlider\Grafika\Imagick\Filter;

use CycloneSlider\Grafika\FilterInterface;
use CycloneSlider\Grafika\Imagick\Image;

/**
 * Invert the image colors.
 */
class Invert implements FilterInterface{

    /**
     * @param Image $image
     *
     * @return Image
     */
    public function apply( $image ) {

        $image->getCore()->negateImage(false);
        return $image;
    }

}