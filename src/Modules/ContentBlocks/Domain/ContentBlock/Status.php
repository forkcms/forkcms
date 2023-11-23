<?php

namespace ForkCMS\Modules\ContentBlocks\Domain\ContentBlock;

use InvalidArgumentException;

enum Status: string
{
    case Archived = 'ARCHIVED';
    case Active = 'ACTIVE';
}
