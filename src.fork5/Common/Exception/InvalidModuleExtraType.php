<?php

namespace Common\Exception;

use Backend\Modules\Pages\Domain\ModuleExtra\ModuleExtraType;
use Exception;

final class InvalidModuleExtraType extends Exception
{
    public static function withType(string $type): self
    {
        return new self(
            sprintf(
                '%s is not a valid module extra type. Possible options are %s',
                $type,
                implode(', ', ModuleExtraType::POSSIBLE_TYPES)
            )
        );
    }
}
