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

    /** @var string */
    private $mediaGroupType;

    /**
     * @param string $mediaGroupType
     */
    private function __construct(string $mediaGroupType)
    {
        if (!in_array($mediaGroupType, self::getPossibleValues(), true)) {
            throw new \InvalidArgumentException('Invalid value');
        }

        $this->mediaGroupType = $mediaGroupType;
    }

    /**
     * @param string $mediaGroupType
     * @return Type
     */
    public static function fromString(string $mediaGroupType): Type
    {
        return new self((string) $mediaGroupType);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string) $this->mediaGroupType;
    }

    /**
     * @param Type $mediaGroupType
     *
     * @return bool
     */
    public function equals(Type $mediaGroupType): bool
    {
        if (!($mediaGroupType instanceof $this)) {
            return false;
        }

        return $mediaGroupType == $this;
    }

    /**
     * @return array
     */
    public static function getPossibleValues(): array
    {
        return [
            self::ALL,
            self::AUDIO,
            self::FILE,
            self::IMAGE,
            self::IMAGE_FILE,
            self::IMAGE_MOVIE,
            self::MOVIE,
        ];
    }

    /**
     * @return Type
     */
    public static function all(): Type
    {
        return new self(self::ALL);
    }

    /**
     * @return bool
     */
    public function isAll(): bool
    {
        return $this->equals(self::all());
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
    public static function imageFile(): Type
    {
        return new self(self::IMAGE_FILE);
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
     * @return bool
     */
    public function isImageFile(): bool
    {
        return $this->equals(self::imageFile());
    }

    /**
     * @return Type
     */
    public static function imageMovie(): Type
    {
        return new self(self::IMAGE_MOVIE);
    }

    /**
     * @return bool
     */
    public function isImageMovie(): bool
    {
        return $this->equals(self::imageMovie());
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
}
