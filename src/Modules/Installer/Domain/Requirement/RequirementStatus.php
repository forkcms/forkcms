<?php

namespace ForkCMS\Modules\Installer\Domain\Requirement;

enum RequirementStatus: string
{
    case SUCCESS = 'success';
    case WARNING = 'warning';
    case ERROR = 'error';

    public function isWarning(): bool
    {
        return $this === self::WARNING;
    }

    public function isError(): bool
    {
        return $this === self::ERROR;
    }
}
