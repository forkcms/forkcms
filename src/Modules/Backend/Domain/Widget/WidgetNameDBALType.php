<?php

namespace ForkCMS\Modules\Backend\Domain\Widget;

use ForkCMS\Core\Domain\Doctrine\ValueObjectDBALType;
use Stringable;

class WidgetNameDBALType extends ValueObjectDBALType
{
    protected function fromString(string $value): Stringable
    {
        return WidgetName::fromString($value);
    }
}
