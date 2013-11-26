<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This exception is used to handle backend related exceptions.
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class BackendException extends SpoonException
{
    /**
     * @param string $message The message of the exception.
     * @param int[optional] $code The numeric code of the exception.
     */
    public function __construct($message, $code = 0)
    {
        parent::__construct((string) $message, (int) $code);
    }
}
