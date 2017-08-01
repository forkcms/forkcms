<?php

namespace Backend\Modules\MediaGalleries\Domain\MediaGallery;

use InvalidArgumentException;

final class Status
{
    private const ACTIVE = 'active';
    private const HIDDEN = 'hidden';
    public const POSSIBLE_VALUES = [
        self::ACTIVE,
        self::HIDDEN,
    ];

    /** @var string */
    private $mediaGalleryStatus;

    private function __construct(string $mediaGalleryStatus)
    {
        if (!in_array($mediaGalleryStatus, self::POSSIBLE_VALUES, true)) {
            throw new InvalidArgumentException('Invalid value');
        }

        $this->mediaGalleryStatus = $mediaGalleryStatus;
    }

    public static function fromString(string $mediaGalleryStatus): self
    {
        return new self($mediaGalleryStatus);
    }

    public function __toString(): string
    {
        return $this->mediaGalleryStatus;
    }

    public function equals(Status $mediaGalleryStatus): bool
    {
        if (!$mediaGalleryStatus instanceof $this) {
            return false;
        }

        return $mediaGalleryStatus->mediaGalleryStatus === $this->mediaGalleryStatus;
    }

    public static function active(): self
    {
        return new self(self::ACTIVE);
    }

    public function isActive(): bool
    {
        return $this->equals(self::active());
    }

    public static function hidden(): self
    {
        return new self(self::HIDDEN);
    }

    public function isHidden(): bool
    {
        return $this->equals(self::hidden());
    }
}
