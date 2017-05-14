<?php

namespace Backend\Core\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This exception is used to handle backend related exceptions.
 */
class Exception extends \SpoonException
{
    /**
     * @param string $message The message of the exception.
     * @param int $code The numeric code of the exception.
     */
    public function __construct(string $message, int $code = 0)
    {
        parent::__construct($message, $code);
    }
}
