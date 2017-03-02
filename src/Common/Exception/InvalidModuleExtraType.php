<?php

namespace Common\Exception;

use Common\ModuleExtraType;
use Exception;

final class InvalidModuleExtraType extends Exception
{
    /**
     * @param string $type
     * @return self
     */
    public static function withType(string $type): self
    {
        return new self(
            sprintf(
                '%s is not a valid module extra type. Possible options are %s',
                $type,
                implode(', ', ModuleExtraType::getPossibleTypes())
            )
        );
    }
}
