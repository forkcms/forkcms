<?php

namespace ForkCMS\Modules\Extensions\Domain\Module;

use ForkCMS\Core\Domain\Doctrine\ValueObjectDBALType;
use Stringable;

class ModuleNameDBALType extends ValueObjectDBALType
{
    protected function fromString(string $value): Stringable
    {
        return ModuleName::fromString($value);
    }
}
