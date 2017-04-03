<?php

namespace Backend\Modules\MediaLibrary\Manager;

use Backend\Modules\MediaLibrary\Domain\MediaItem\Type;

class ExtensionManager
{
    /** @var array */
    protected $extensions = [];

    /**
     * @param string $mediaItemType
     * @param array $extensions
     * @throws \Exception
     */
    public function add(string $mediaItemType, array $extensions)
    {
        try {
            $type = Type::fromString($mediaItemType);
        } catch (\Exception $e) {
            throw $e;
        }

        $this->extensions[$type->getType()] = $extensions;
    }

    /**
     * @param Type $mediaItemType
     * @return array
     */
    public function get(Type $mediaItemType): array
    {
        return $this->extensions[(string) $mediaItemType];
    }

    /**
     * @return array
     */
    public function getAll(): array
    {
        $extensions = [];
        foreach ($this->extensions as $key => $values) {
            $extensions = array_merge($extensions, $values);
        }
        return $extensions;
    }
}
