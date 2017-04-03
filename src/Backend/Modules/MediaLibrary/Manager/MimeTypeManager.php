<?php

namespace Backend\Modules\MediaLibrary\Manager;

use Backend\Modules\MediaLibrary\Domain\MediaItem\Type;

class MimeTypeManager
{
    /** @var array */
    protected $mimeTypes = [];

    /**
     * @param string $mediaItemType
     * @param array $mimeTypes
     * @throws \Exception
     */
    public function add(string $mediaItemType, array $mimeTypes)
    {
        try {
            $type = Type::fromString($mediaItemType);
        } catch (\Exception $e) {
            throw $e;
        }

        $this->mimeTypes[$type->getType()] = $mimeTypes;
    }

    /**
     * @param Type $mediaItemType
     * @return array
     */
    public function get(Type $mediaItemType): array
    {
        return $this->mimeTypes[(string) $mediaItemType];
    }

    /**
     * @return array
     */
    public function getAll(): array
    {
        $mimeTypes = [];
        foreach ($this->mimeTypes as $key => $values) {
            $mimeTypes = array_merge($mimeTypes, $values);
        }
        return $mimeTypes;
    }
}
