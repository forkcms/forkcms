<?php

namespace Common\Exception;

use InvalidArgumentException;

/**
 * This exception is thrown when we try to initialize a Fork application using
 * an incorrect type name
 */
final class InvalidInitTypeException extends InvalidArgumentException
{
    /**
     * @param $type
     * @param $allowedTypes
     *
     * @return InvalidInitTypeException
     */
    public static function withType($type, $allowedTypes)
    {
        return new self(
            'The type "' . $type . '" is not within the allowed types "'
            . implode('", "', $allowedTypes) . '"'
        );
    }
}
