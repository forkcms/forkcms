<?php

namespace App\Domain\ModuleExtra\Exception;

use App\Domain\ModuleExtra\Type;
use Exception;

final class InvalidTypeException extends Exception
{
    public static function for(string $type): self
    {
        return new self(
            sprintf(
                '%s is not a valid module extra type. Possible options are %s',
                $type,
                implode(', ', Type::POSSIBLE_TYPES)
            )
        );
    }
}
