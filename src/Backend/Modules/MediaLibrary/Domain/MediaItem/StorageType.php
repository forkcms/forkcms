<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaItem;

final class StorageType
{
    // Possible MediaItem types
    const EXTERNAL = 'external';
    const LOCAL = 'local';
    const YOUTUBE = 'youtube';
    const VIMEO = 'vimeo';

    /** @var string */
    private $mediaItemStorageType;

    /**
     * @param string $mediaItemStorageType
     */
    private function __construct(string $mediaItemStorageType)
    {
        if (!in_array($mediaItemStorageType, self::getPossibleValues(), true)) {
            throw new \InvalidArgumentException('Invalid value');
        }

        $this->mediaItemStorageType = $mediaItemStorageType;
    }

    /**
     * @param string $mediaItemStorageType
     * @return StorageType
     */
    public static function fromString(string $mediaItemStorageType): StorageType
    {
        return new self($mediaItemStorageType);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string) $this->mediaItemStorageType;
    }

    /**
     * @param StorageType $mediaItemStorageType
     *
     * @return bool
     */
    public function equals(StorageType $mediaItemStorageType): bool
    {
        if (!($mediaItemStorageType instanceof $this)) {
            return false;
        }

        return $mediaItemStorageType == $this;
    }

    /**
     * @return array
     */
    public static function getPossibleValues(): array
    {
        return [
            self::EXTERNAL,
            self::LOCAL,
            self::YOUTUBE,
            self::VIMEO,
        ];
    }

    /**
     * @return array
     */
    public static function getPossibleMovieStorageTypeValues(): array
    {
        return [
            self::YOUTUBE,
            self::VIMEO,
        ];
    }

    /**
     * @return StorageType
     */
    public static function local(): StorageType
    {
        return new self(self::LOCAL);
    }

    /**
     * @return bool
     */
    public function isLocal(): bool
    {
        return $this->equals(self::local());
    }

    /**
     * @return StorageType
     */
    public static function youtube(): StorageType
    {
        return new self(self::YOUTUBE);
    }

    /**
     * @return bool
     */
    public function isYoutube(): bool
    {
        return $this->equals(self::youtube());
    }

    /**
     * @return StorageType
     */
    public static function vimeo(): StorageType
    {
        return new self(self::VIMEO);
    }

    /**
     * @return bool
     */
    public function isVimeo(): bool
    {
        return $this->equals(self::vimeo());
    }

    /**
     * @return string
     */
    public function getStorageType(): string
    {
        return $this->mediaItemStorageType;
    }
}
