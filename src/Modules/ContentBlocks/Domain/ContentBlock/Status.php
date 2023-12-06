<?php

namespace ForkCMS\Modules\ContentBlocks\Domain\ContentBlock;

enum Status: string
{
    case ARCHIVED = 'archived';
    case ACTIVE = 'active';
}
