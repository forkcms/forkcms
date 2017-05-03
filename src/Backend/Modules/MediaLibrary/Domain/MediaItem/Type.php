<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaItem;

use Backend\Core\Engine\Model;
use Exception;
use InvalidArgumentException;

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

    private function __construct(string $mediaItemType)
    {
        if (!in_array($mediaItemType, self::POSSIBLE_VALUES, true)) {
            throw new InvalidArgumentException('Invalid value');
        }

        $this->mediaItemType = $mediaItemType;
    }

    public static function fromString(string $mediaItemType): self
    {
        return new self($mediaItemType);
    }

    public static function fromMimeType(string $mimeType): self
    {
        $mimeType = strtolower($mimeType);

        // Extension not exists, throw exception
        if (!in_array($mimeType, Model::get('media_library.manager.mime_type')->getAll())) {
            throw new Exception(
                'MimeType is not one of the allowed ones: ' . implode(
                    ', ',
                    Model::get('media_library.manager.mime_type')->getAll()
                )
            );
        }

        foreach (self::POSSIBLE_VALUES as $mediaItemType) {
            if (in_array(
                $mimeType,
                Model::get('media_library.manager.mime_type')->get(self::fromString($mediaItemType))
            )) {
                return self::fromString($mediaItemType);
            }
        }
    }

    public function __toString(): string
    {
        return $this->mediaItemType;
    }

    public function equals(Type $mediaItemType): bool
    {
        return $mediaItemType->mediaItemType === $this->mediaItemType;
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

    public static function image(): self
    {
        return new self(self::IMAGE);
    }

    public function isImage(): bool
    {
        return $this->equals(self::image());
    }

    public static function movie(): self
    {
        return new self(self::MOVIE);
    }

    public function isMovie(): bool
    {
        return $this->equals(self::movie());
    }

    public function getType(): string
    {
        return $this->mediaItemType;
    }
}
