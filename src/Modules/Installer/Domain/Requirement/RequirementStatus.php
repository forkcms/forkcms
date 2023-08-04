<?php

namespace ForkCMS\Modules\Installer\Domain\Requirement;

enum RequirementStatus
{
    case success;
    case warning;
    case error;

    public function isWarning(): bool
    {
        return $this === self::warning;
    }

    public function isError(): bool
    {
        return $this === self::error;
    }
}
