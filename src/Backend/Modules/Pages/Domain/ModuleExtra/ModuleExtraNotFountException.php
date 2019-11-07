<?php

namespace Backend\Modules\Pages\Domain\ModuleExtra;

use RuntimeException;

final class ModuleExtraNotFountException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('ModuleExtra not found');
    }
}
