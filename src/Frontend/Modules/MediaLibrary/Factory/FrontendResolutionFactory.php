<?php

namespace Frontend\Modules\MediaLibrary\Factory;

use Backend\Modules\MediaLibrary\Component\ImageSettings;
use Backend\Modules\MediaLibrary\Component\ImageTransformationMethod;
use Frontend\Modules\MediaLibrary\Component\FrontendResolution;

/**
 * Frontend MediaItem Resolution Factory
 */
class FrontendResolutionFactory
{
    /**
     * Create a FrontendResolution
     *
     * @param string $customKey
     * @param integer $width
     * @param integer $height
     * @param string $method  "crop" or "resize"
     * @param integer $quality
     * @return FrontendResolution
     */
    public function create(
        $customKey,
        $method,
        $width = null,
        $height = null,
        $quality = 100
    ) {
        return FrontendResolution::create(
            $customKey,
            ImageSettings::create(
                ImageTransformationMethod::fromString($method),
                $width,
                $height,
                $quality
            )
        );
    }
}
