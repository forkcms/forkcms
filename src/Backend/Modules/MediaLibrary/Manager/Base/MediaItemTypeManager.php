<?php

namespace Backend\Modules\MediaLibrary\Manager\Base;

use Backend\Modules\MediaLibrary\Domain\MediaItem\Type;

class MediaItemTypeManager
{
    /** @var array */
    protected $values = [];

    public function add(string $mediaItemType, array $values): void
    {
        /** @var Type $type */
        $type = Type::fromString($mediaItemType);

        // Add the value
        $this->values[$type->getType()] = $values;
    }

    public function get(Type $mediaItemType): array
    {
        return $this->values[(string) $mediaItemType];
    }

    /**
     * @return array
     */
    public function getAll(): array
    {
        $values = [];
        foreach ($this->values as $key => $items) {
            $values = array_merge($values, $items);
        }

        return $values;
    }
}
