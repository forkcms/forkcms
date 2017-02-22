<?php

namespace Backend\Modules\MediaLibrary\Component;

/**
 * Thumbnail Transformation Method
 */
class ImageTransformationMethod
{
    // Methods to edit the image
    const CROP = 'crop';
    const RESIZE = 'resize';

    // Horizontal crop positions
    const LEFT = 'left';
    const CENTER = 'center';
    const RIGHT = 'right';

    // Vertical crop positions
    const TOP = 'top';
    const MIDDLE = 'middle';
    const BOTTOM = 'bottom';

    /**
     * @var string
     */
    protected $method;

    /**
     * @var string
     */
    protected static $defaultMethod = self::RESIZE;

    /**
     * @var string
     */
    protected $horizontalCropPosition;

    /**
     * @var string
     */
    protected $verticalCropPosition;

    /**
     * @var string
     */
    protected static $defaultHorizontalCropPosition = self::CENTER;

    /**
     * @var string
     */
    protected static $defaultVerticalCropPosition = self::MIDDLE;

    /**
     * ImageTransformationMethod constructor.
     *
     * @param $method
     * @param string|null $horizontalCropPosition
     * @param string|null $verticalCropPosition
     */
    private function __construct(
        $method,
        $horizontalCropPosition = null,
        $verticalCropPosition = null
    ) {
        $this->method = $method;
        $this->horizontalCropPosition = ($horizontalCropPosition !== null)
            ? $horizontalCropPosition : self::$defaultHorizontalCropPosition;
        $this->verticalCropPosition = ($verticalCropPosition !== null)
            ? $verticalCropPosition : self::$defaultVerticalCropPosition;
    }

    /**
     * Create default
     *
     * @return ImageTransformationMethod
     */
    public function createDefault()
    {
        return new self(self::$defaultMethod);
    }

    /**
     * Create a new crop
     *
     * @return ImageTransformationMethod
     */
    public static function crop()
    {
        return new self(self::CROP);
    }

    /**
     * From string
     *
     * @param string $value
     * @return ImageTransformationMethod
     * @throws \Exception
     */
    public static function fromString($value)
    {
        // Define method, by default we use "resize"
        $method = self::$defaultMethod;
        $horizontalCropPosition = self::$defaultHorizontalCropPosition;
        $verticalCropPosition = self::$defaultVerticalCropPosition;

        foreach (self::getPossibleMethods() as $possibleMethod) {
            if (strpos($value, $possibleMethod) !== false) {
                $method = $possibleMethod;
                break;
            }
        }

        foreach (self::getPossibleHorizontalCropPositions() as $position) {
            if (strpos($value, $position) !== false) {
                $horizontalCropPosition = $position;
                break;
            }
        }

        foreach (self::getPossibleVerticalCropPositions() as $position) {
            if (strpos($value, $position) !== false) {
                $verticalCropPosition = $position;
                break;
            }
        }

        return new self(
            $method,
            $horizontalCropPosition,
            $verticalCropPosition
        );
    }

    /**
     * Get crop position horizontal
     *
     * @return string
     */
    public function getHorizontalCropPosition()
    {
        return $this->horizontalCropPosition;
    }

    /**
     * Get method
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Get crop position vertical
     *
     * @return string
     */
    public function getVerticalCropPosition()
    {
        return $this->verticalCropPosition;
    }

    /**
     * Get possible horizontal crop positions
     *
     * @return array
     */
    public static function getPossibleHorizontalCropPositions()
    {
        return array(
            self::LEFT,
            self::CENTER,
            self::RIGHT,
        );
    }

    /**
     * Get possible methods
     *
     * @return array
     */
    public static function getPossibleMethods()
    {
        return array(
            self::CROP,
            self::RESIZE
        );
    }

    /**
     * Get possible vertical crop positions
     *
     * @return array
     */
    public static function getPossibleVerticalCropPositions()
    {
        return array(
            self::TOP,
            self::MIDDLE,
            self::BOTTOM,
        );
    }

    /**
     * Is crop
     *
     * @return bool
     */
    public function isCrop()
    {
        return $this->method == self::CROP;
    }

    /**
     * Is resize
     *
     * @return bool
     */
    public function isResize()
    {
        return $this->method == self::RESIZE;
    }

    /**
     * Create a new resize
     *
     * @return ImageTransformationMethod
     */
    public static function resize()
    {
        return new self(self::RESIZE);
    }

    /**
     * Set horizontal crop position
     *
     * @param $position
     * @return ImageTransformationMethod
     * @throws \Exception
     */
    public function setHorizontalCropPosition($position)
    {
        if ($this->method != self::CROP) {
            throw new \Exception('You must also set the method to "crop", otherwise setting "horizontal crop position" will have no effect.');
        }

        if (!in_array($position, self::getPossibleHorizontalCropPositions())) {
            throw new \Exception('The horizontal crop-position "' . $position . '" isn\'t valid.');
        }

        $this->horizontalCropPosition = $position;
        return $this;
    }

    /**
     * Set vertical crop position
     *
     * @param $position
     * @return ImageTransformationMethod
     * @throws \Exception
     */
    public function setVerticalCropPosition($position)
    {
        if ($this->method != self::CROP) {
            throw new \Exception('You must also set the method to "crop", otherwise setting "vertical crop position" will have no effect.');
        }

        if (!in_array($position, self::getPossibleVerticalCropPositions())) {
            throw new \Exception('The vertical crop-position "' . $position . '" isn\'t valid.');
        }

        $this->verticalCropPosition = $position;
        return $this;
    }

    /**
     * To string
     *
     * @return string
     */
    public function toString()
    {
        $value = '';

        if ($this->method != self::$defaultMethod) {
            $value .= '-' . $this->method;
        }

        if ($this->horizontalCropPosition != self::$defaultHorizontalCropPosition) {
            $value .= '-' . $this->horizontalCropPosition;
        }

        if ($this->verticalCropPosition != self::$defaultVerticalCropPosition) {
            $value .= '-' . $this->verticalCropPosition;
        }

        return $value;
    }
}
