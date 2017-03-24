<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaItem;

final class Type
{
    // Possible MediaItem types
    const AUDIO = 'audio';
    const FILE = 'file';
    const IMAGE = 'image';
    const MOVIE = 'movie';
    const POSSIBLE_VALUES = [
        self::IMAGE,
        self::FILE,
        self::MOVIE,
        self::AUDIO,
    ];

    /** @var string */
    private $mediaItemType;

    /**
     * @param string $mediaItemType
     */
    private function __construct(string $mediaItemType)
    {
        if (!in_array($mediaItemType, self::POSSIBLE_VALUES, true)) {
            throw new \InvalidArgumentException('Invalid value');
        }

        $this->mediaItemType = $mediaItemType;
    }

    /**
     * @param string $mediaItemType
     * @return Type
     */
    public static function fromString(string $mediaItemType): Type
    {
        return new self($mediaItemType);
    }

    /**
     * @param string $extension
     * @return Type
     */
    public static function fromExtension(string $extension): Type
    {
        $extension = strtolower($extension);

        // Looking for image files
        if (in_array($extension, [
            'jpg',
            'jpeg',
            'gif',
            'png',
        ])) {
            return self::image();
        }

        // Looking for audio files
        if (in_array($extension, [
            'mp3',
            'aiff',
            'wav',
        ])) {
            return self::audio();
        } elseif (in_array($extension, [
            'avi',
            'mov',
            'mp4',
        ])) {
            return self::movie();
        }

        return self::file();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->mediaItemType;
    }

    /**
     * @param Type $mediaItemType
     * @return bool
     */
    public function equals(Type $mediaItemType): bool
    {
        return $mediaItemType->mediaItemType === $this->mediaItemType;
    }

    /**
     * @return Type
     */
    public static function audio(): Type
    {
        return new self(self::AUDIO);
    }

    /**
     * @return bool
     */
    public function isAudio(): bool
    {
        return $this->equals(self::audio());
    }

    /**
     * @return Type
     */
    public static function file(): Type
    {
        return new self(self::FILE);
    }

    /**
     * @return bool
     */
    public function isFile(): bool
    {
        return $this->equals(self::file());
    }

    /**
     * @return Type
     */
    public static function image(): Type
    {
        return new self(self::IMAGE);
    }

    /**
     * @return bool
     */
    public function isImage(): bool
    {
        return $this->equals(self::image());
    }

    /**
     * @return Type
     */
    public static function movie(): Type
    {
        return new self(self::MOVIE);
    }

    /**
     * @return bool
     */
    public function isMovie(): bool
    {
        return $this->equals(self::movie());
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->mediaItemType;
    }
}
