<?php

namespace ForkCMS\Modules\Backend\Domain\Action;

use ForkCMS\Core\Domain\Doctrine\ValueObjectDBALType;
use Stringable;

class ActionNameDBALType extends ValueObjectDBALType
{
    protected function fromString(string $value): Stringable
    {
        return ActionName::fromString($value);
    }
}
