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
     * @return self
     */
    public static function fromString(string $mediaGalleryStatus): self
    {
        return new self($mediaGalleryStatus);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string) $this->mediaGalleryStatus;
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
     * @return self
     */
    public static function active(): self
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
     * @return self
     */
    public static function hidden(): self
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
