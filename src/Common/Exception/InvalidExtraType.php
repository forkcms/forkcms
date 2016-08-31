<?php

namespace Common\Exception;

use Common\ExtraType;
use Exception;

final class InvalidExtraType extends Exception
{
    /**
     * @param string $type
     * @return self
     */
    public static function withType($type)
    {
        return new self(
            sprintf(
                '%s is not a valid module extra type. Possible options are %s',
                $type,
                implode(', ', ExtraType::getPossibleTypes())
            )
        );
    }
}
