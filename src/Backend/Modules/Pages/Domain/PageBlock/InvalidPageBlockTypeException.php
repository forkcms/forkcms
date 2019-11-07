<?php

namespace Backend\Modules\Pages\Domain\PageBlock;

use RuntimeException;

final class InvalidPageBlockTypeException extends RuntimeException
{
    public static function withType(string $type)
    {
        return new self("$type is not a valid PageBlockType");
    }
}
