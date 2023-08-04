<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaItem;

use InvalidArgumentException;

final class StorageType
{
    // Possible MediaItem types
    const EXTERNAL = 'external';
    const LOCAL = 'local';
    const YOUTUBE = 'youtube';
    const VIMEO = 'vimeo';
    const POSSIBLE_VALUES = [
        self::EXTERNAL,
        self::LOCAL,
        self::YOUTUBE,
        self::VIMEO,
    ];
    const POSSIBLE_VALUES_FOR_MOVIE = [
        self::YOUTUBE,
        self::VIMEO,
    ];

    /** @var string */
    private $mediaItemStorageType;

    private function __construct(string $mediaItemStorageType)
    {
        if (!in_array($mediaItemStorageType, self::POSSIBLE_VALUES, true)) {
            throw new InvalidArgumentException('Invalid value');
        }

        $this->mediaItemStorageType = $mediaItemStorageType;
    }

    public static function fromString(string $mediaItemStorageType): StorageType
    {
        return new self($mediaItemStorageType);
    }

    public function __toString(): string
    {
        return $this->mediaItemStorageType;
    }

    public function equals(StorageType $mediaItemStorageType): bool
    {
        return $mediaItemStorageType->mediaItemStorageType === $this->mediaItemStorageType;
    }

    public static function local(): StorageType
    {
        return new self(self::LOCAL);
    }

    public function isLocal(): bool
    {
        return $this->equals(self::local());
    }

    public static function external(): StorageType
    {
        return new self(self::EXTERNAL);
    }

    public function isExternal(): bool
    {
        return $this->equals(self::external());
    }

    public static function youtube(): StorageType
    {
        return new self(self::YOUTUBE);
    }

    public function isYoutube(): bool
    {
        return $this->equals(self::youtube());
    }

    public static function vimeo(): StorageType
    {
        return new self(self::VIMEO);
    }

    public function isVimeo(): bool
    {
        return $this->equals(self::vimeo());
    }

    public function getStorageType(): string
    {
        return $this->mediaItemStorageType;
    }
}
