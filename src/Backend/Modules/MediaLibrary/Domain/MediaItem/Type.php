<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaItem;

final class Type
{
    // Possible MediaItem types
    const AUDIO = 'audio';
    const FILE = 'file';
    const IMAGE = 'image';
    const MOVIE = 'movie';

    /** @var string */
    private $mediaItemType;

    /**
     * @param string $mediaItemType
     */
    private function __construct($mediaItemType)
    {
        if (!in_array($mediaItemType, self::getPossibleValues(), true)) {
            throw new \InvalidArgumentException('Invalid value');
        }

        $this->mediaItemType = $mediaItemType;
    }

    /**
     * @param string $mediaItemType
     * @return Type
     */
    public static function fromString($mediaItemType)
    {
        return new self($mediaItemType);
    }

    /**
     * @param $extension
     * @return Type
     */
    public static function fromExtension($extension)
    {
        // Init extension
        $type = self::FILE;

        // Looking for image files
        if (in_array($extension, array(
            'jpg',
            'jpeg',
            'gif',
            'png',
        ))) {
            // Define extension as image
            $type = self::IMAGE;
            // Looking for audio files
        } elseif (in_array($extension, array(
            'mp3',
            'aiff',
            'wav',
        ))) {
            $type = self::AUDIO;
        } elseif (in_array($extension, array(
            'avi',
            'mov',
            'mp4',
        ))) {
            $type = self::MOVIE;
        }

        return self::fromString($type);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->mediaItemType;
    }

    /**
     * @param Type $mediaItemType
     *
     * @return bool
     */
    public function equals(Type $mediaItemType)
    {
        if (!($mediaItemType instanceof $this)) {
            return false;
        }

        return $mediaItemType == $this;
    }

    /**
     * @return array
     */
    public static function getPossibleValues()
    {
        return [
            self::IMAGE,
            self::FILE,
            self::MOVIE,
            self::AUDIO,
        ];
    }

    /**
     * @return Type
     */
    public static function audio()
    {
        return new self(self::AUDIO);
    }

    /**
     * @return bool
     */
    public function isAudio()
    {
        return $this->equals(self::audio());
    }

    /**
     * @return Type
     */
    public static function file()
    {
        return new self(self::FILE);
    }

    /**
     * @return bool
     */
    public function isFile()
    {
        return $this->equals(self::file());
    }

    /**
     * @return Type
     */
    public static function image()
    {
        return new self(self::IMAGE);
    }

    /**
     * @return bool
     */
    public function isImage()
    {
        return $this->equals(self::image());
    }

    /**
     * @return Type
     */
    public static function movie()
    {
        return new self(self::MOVIE);
    }

    /**
     * @return bool
     */
    public function isMovie()
    {
        return $this->equals(self::movie());
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->mediaItemType;
    }
}
