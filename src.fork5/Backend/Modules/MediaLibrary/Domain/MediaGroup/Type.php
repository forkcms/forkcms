<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaGroup;

final class Type
{
    // Possible MediaGroup types
    const ALL = 'all';
    const AUDIO = 'audio';
    const FILE = 'file';
    const IMAGE = 'image';
    const IMAGE_FILE = 'image-file';
    const IMAGE_MOVIE = 'image-movie';
    const MOVIE = 'movie';
    const POSSIBLE_VALUES = [
        self::ALL,
        self::AUDIO,
        self::FILE,
        self::IMAGE,
        self::IMAGE_FILE,
        self::IMAGE_MOVIE,
        self::MOVIE,
    ];

    /** @var string */
    private $mediaGroupType;

    private function __construct(string $mediaGroupType)
    {
        if (!in_array($mediaGroupType, self::POSSIBLE_VALUES, true)) {
            throw new \InvalidArgumentException('Invalid value');
        }

        $this->mediaGroupType = $mediaGroupType;
    }

    public static function fromString(string $mediaGroupType): self
    {
        return new self($mediaGroupType);
    }

    public function __toString(): string
    {
        return $this->mediaGroupType;
    }

    public function equals(Type $mediaGroupType): bool
    {
        if (!($mediaGroupType instanceof $this)) {
            return false;
        }

        return $mediaGroupType->mediaGroupType === $this->mediaGroupType;
    }

    public static function all(): self
    {
        return new self(self::ALL);
    }

    public function isAll(): bool
    {
        return $this->equals(self::all());
    }

    public static function audio(): self
    {
        return new self(self::AUDIO);
    }

    public function isAudio(): bool
    {
        return $this->equals(self::audio());
    }

    public static function file(): self
    {
        return new self(self::FILE);
    }

    public function isFile(): bool
    {
        return $this->equals(self::file());
    }

    public static function imageFile(): self
    {
        return new self(self::IMAGE_FILE);
    }

    public static function image(): self
    {
        return new self(self::IMAGE);
    }

    public function isImage(): bool
    {
        return $this->equals(self::image());
    }

    public function isImageFile(): bool
    {
        return $this->equals(self::imageFile());
    }

    public static function imageMovie(): self
    {
        return new self(self::IMAGE_MOVIE);
    }

    public function isImageMovie(): bool
    {
        return $this->equals(self::imageMovie());
    }

    public static function movie(): self
    {
        return new self(self::MOVIE);
    }

    public function isMovie(): bool
    {
        return $this->equals(self::movie());
    }
}
