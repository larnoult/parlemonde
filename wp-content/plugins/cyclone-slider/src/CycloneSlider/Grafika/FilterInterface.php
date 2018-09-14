<?php
namespace CycloneSlider\Grafika;

/**
 * Interface FilterInterface
 * @package Grafika
 */
interface FilterInterface {

    /**
     * @param ImageInterface $image
     *
     * @return ImageInterface
     */
    public function apply( $image );

}