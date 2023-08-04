<?php

namespace ForkCMS\Core\Domain\Header\Asset;

use ForkCMS\Modules\Extensions\Domain\Module\ModuleName;

enum Priority: int
{
    case CORE = 0;
    case STANDARD = 1;
    case MODULE = 2;
    case WIDGET = 3;
    case DEBUG = 99;

    public function compare(self $priority): int
    {
        return $this->value <=> $priority->value;
    }

    public static function forModuleName(ModuleName $module): self
    {
        return $module === ModuleName::CORE() ? self::CORE : self::MODULE;
    }
}
