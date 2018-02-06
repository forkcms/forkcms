<?php

namespace App\Domain\ModuleExtra\Exception;

use Exception;

final class InvalidModuleExtraException extends Exception
{
    public static function alreadySet(): self
    {
        return new self('You can only set the extra ID once');
    }
}
