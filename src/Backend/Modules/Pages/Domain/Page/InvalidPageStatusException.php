<?php

namespace Backend\Modules\Pages\Domain\Page;

use RuntimeException;

final class InvalidPageStatusException extends RuntimeException
{
    public static function withType(string $type)
    {
        return new self("$type is not a valid Status");
    }
}
