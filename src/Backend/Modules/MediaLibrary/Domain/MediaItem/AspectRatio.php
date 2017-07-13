<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaItem;

final class AspectRatio
{
    private const PRECISION = 2;

    /** @var float */
    private $aspectRatio;

    public function __construct(float $aspectRatio)
    {
        $this->aspectRatio = round($aspectRatio, self::PRECISION);
    }

    public static function fromWidthAndHeight(float $width, float $height): self
    {
        return new self($width / $height);
    }

    public function equals(self $aspectRatio): bool
    {
        return $this->aspectRatio === $aspectRatio->aspectRatio;
    }

    public function asFloat(): float
    {
        return $this->aspectRatio;
    }
}
