<?php

namespace Backend\Modules\MediaGalleries\Domain\MediaGallery;

final class Status
{
    const ACTIVE = 'active';
    const HIDDEN = 'hidden';
    const POSSIBLE_VALUES = [
        self::ACTIVE,
        self::HIDDEN,
    ];

    /** @var string */
    private $mediaGalleryStatus;

    /**
     * @param string $mediaGalleryStatus
     */
    private function __construct(string $mediaGalleryStatus)
    {
        if (!in_array($mediaGalleryStatus, self::POSSIBLE_VALUES, true)) {
            throw new \InvalidArgumentException('Invalid value');
        }

        $this->mediaGalleryStatus = $mediaGalleryStatus;
    }

    /**
     * @param string $mediaGalleryStatus
     * @return Status
     */
    public static function fromString(string $mediaGalleryStatus): Status
    {
        return new self($mediaGalleryStatus);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->mediaGalleryStatus;
    }

    /**
     * @param Status $mediaGalleryStatus
     * @return bool
     */
    public function equals(Status $mediaGalleryStatus): bool
    {
        if (!$mediaGalleryStatus instanceof $this) {
            return false;
        }

        return $mediaGalleryStatus == $this;
    }

    /**
     * @return Status
     */
    public static function active(): Status
    {
        return new self(self::ACTIVE);
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->equals(self::active());
    }

    /**
     * @return Status
     */
    public static function hidden(): Status
    {
        return new self(self::HIDDEN);
    }

    /**
     * @return bool
     */
    public function isHidden(): bool
    {
        return $this->equals(self::hidden());
    }
}
