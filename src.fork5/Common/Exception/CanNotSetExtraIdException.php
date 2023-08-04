<?php

namespace Common\Exception;

use Exception;

final class CanNotSetExtraIdException extends Exception
{
    public function __construct()
    {
        parent::__construct('You can only set the extra ID once');
    }
}
