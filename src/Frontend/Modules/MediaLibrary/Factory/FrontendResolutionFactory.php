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
     * @param string $method  "crop" or "resize"
     * @param int $width
     * @param int $height
     * @param int $quality
     * @return FrontendResolution
     */
    public function create(
        string $customKey,
        string $method,
        int $width = null,
        int $height = null,
        int $quality = 100
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
