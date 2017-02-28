<?php

namespace Frontend\Modules\MediaLibrary\Component;

use Backend\Modules\MediaLibrary\Component\ImageSettings;

/**
 * Frontend Resolution
 * We use this component to create a image thumbnail to be used in the frontend.
 */
class FrontendResolution
{
    // Methods to edit the file
    const METHOD_CROP = 'crop';
    const METHOD_RESIZE = 'resize';

    /**
     * @var string
     */
    protected $customKey;

    /**
     * @var ImageSettings
     */
    protected $imageSettings;

    /**
     * Construct
     *
     * @param string $customKey
     * @param ImageSettings $imageSettings
     */
    private function __construct(
        string $customKey,
        ImageSettings $imageSettings
    ) {
        $this->setCustomKey($customKey);
        $this->imageSettings = $imageSettings;
    }

    /**
     * Create
     *
     * @param string $customKey
     * @param ImageSettings $imageSettings
     * @return FrontendResolution
     */
    public static function create(
        string $customKey,
        ImageSettings $imageSettings
    ) : FrontendResolution{
        return new self(
            $customKey,
            $imageSettings
        );
    }

    /**
     * Gets the value of customKey.
     *
     * @return string
     */
    public function getCustomKey(): string
    {
        return $this->customKey;
    }

    /**
     * Gets the thumbnail settings
     *
     * @return ImageSettings
     */
    public function getImageSettings(): ImageSettings
    {
        return $this->imageSettings;
    }

    /**
     * Set custom key
     *
     * @param string $customKey
     * @return FrontendResolution
     * @throws \Exception
     */
    protected function setCustomKey(string $customKey): FrontendResolution
    {
        $customKey = (string) $customKey;

        // We have found spaces in the custom key
        if (preg_match('/\s/', $customKey) > 0) {
            throw new \Exception('Your frontend.resolution customKey must not contain spaces.');
        }

        $this->customKey = $customKey;
        return $this;
    }
}
