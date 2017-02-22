<?php

namespace Backend\Modules\MediaLibrary\Component;

/**
 * Thumbnail settings
 *
 * We use this component as a helper to create an image thumbnail.
 */
class ImageSettings
{
    /**
     * @var integer|null
     */
    protected $width;

    /**
     * @var integer|null
     */
    protected $height;

    /**
     * @var ImageTransformationMethod
     */
    protected $transformationMethod;

    /**
     * @var integer
     */
    protected $quality = 100;

    /**
     * Construct
     *
     * @param integer|null $width
     * @param integer|null $height
     * @param ImageTransformationMethod $transformationMethod "crop" or "resize"
     * @param integer $quality
     * @throws \Exception
     */
    private function __construct(
        $width = null,
        $height = null,
        ImageTransformationMethod $transformationMethod,
        $quality = 100
    ) {
        $this->setWidth($width);
        $this->setHeight($height);
        $this->transformationMethod = $transformationMethod;
        $this->setQuality($quality);
    }

    /**
     * Create
     *
     * @param integer|null $width
     * @param integer|null $height
     * @param ImageTransformationMethod $transformationMethod "crop" or "resize"
     * @param integer $quality
     * @return ImageSettings
     */
    public static function create(
        $width = null,
        $height = null,
        ImageTransformationMethod $transformationMethod,
        $quality = 100
    ) {
        return new self(
            $width,
            $height,
            $transformationMethod,
            $quality
        );
    }

    /**
     * From string
     *
     * @param string $value
     * @return ImageSettings
     * @throws \Exception
     */
    public static function fromString($value)
    {
        // Divide value into width and height
        list($width, $heightAndMore) = explode('x', $value);

        // Redefine
        $width = (int) $width;
        $height = (int) trim(strstr($heightAndMore, '-', true), '-');

        // Define method, by default we use "resize"
        $transformationMethod = ImageTransformationMethod::fromString($value);

        // Define quality
        $quality = (int) ltrim(substr($heightAndMore, -3), '-');

        if ($quality == 0) {
            throw new \Exception('String must contain quality at the end, f.e.: "-100", "-20"');
        }

        return new self(
            $width,
            $height,
            $transformationMethod,
            $quality
        );
    }

    /**
     * Gets the value of width.
     *
     * @return integer
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Gets the value of height.
     *
     * @return integer
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Get method
     *
     * @return ImageTransformationMethod
     */
    public function getTransformationMethod()
    {
        return $this->transformationMethod;
    }

    /**
     * Get resolution ("widthx", "xheight" or "widthxheight")
     *
     * @return string
     */
    public function getResolution()
    {
        $resolution = '';

        if ($this->getWidth() !== null) {
            $resolution .= $this->getWidth();
        }

        $resolution .= 'x';

        if ($this->getHeight() !== null) {
            $resolution .= $this->getHeight();
        }

        return $resolution;
    }

    /**
     * Gets the value of quality.
     *
     * @return integer
     */
    public function getQuality()
    {
        return $this->quality;
    }

    /**
     * Set height
     *
     * @param integer|null $height
     * @return ImageSettings
     * @throws \Exception
     */
    protected function setHeight($height)
    {
        $height = (int) $height;

        if ($height == 0) {
            $height = null;
        } else {
            if ($height <= 0) {
                throw new \Exception(
                    'The height should be higher then 0.'
                );
            }
        }

        $this->height = $height;
        return $this;
    }

    /**
     * Set quality
     *
     * @param integer $quality
     * @return ImageSettings
     * @throws \Exception
     */
    protected function setQuality($quality)
    {
        if ($quality < 0
            || $quality > 100
        ) {
            throw new \Exception(
                'The quality must be between 0 and 100.'
            );
        }

        $this->quality = $quality;
        return $this;
    }

    /**
     * Set width
     *
     * @param integer|null $width
     * @return ImageSettings
     * @throws \Exception
     */
    protected function setWidth($width)
    {
        $width = (int) $width;

        if ($width == 0) {
            $width = null;
        } else {
            if ($width <= 0) {
                throw new \Exception(
                    'The width should be higher then 0.'
                );
            }
        }

        $this->width = $width;
        return $this;
    }

    /**
     * To string - This will return the folder name
     *
     * @return string
     */
    public function toString()
    {
        // Add resolution (width - height)
        $value = $this->getResolution();

        // Add tranformation method
        $value .= $this->getTransformationMethod()->toString();

        if ($this->getQuality() < 100) {
            // Add quality
            $value .= '-' . $this->getQuality();
        }

        return $value;
    }
}
