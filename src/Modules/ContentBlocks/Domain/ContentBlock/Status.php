<?php

namespace ForkCMS\Modules\ContentBlocks\Domain\ContentBlock;

enum Status: string
{
    case ARCHIVED = 'archived';
    case ACTIVE = 'active';

    public function isActive(): bool
    {
        return $this === self::ACTIVE;
    }
}
