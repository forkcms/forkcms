<?php

namespace ForkCMS\Modules\Frontend\Domain\AjaxAction;

use ForkCMS\Core\Domain\Doctrine\ValueObjectDBALType;
use Stringable;

class AjaxActionNameDBALType extends ValueObjectDBALType
{
    protected function fromString(string $value): Stringable
    {
        return AjaxActionName::fromString($value);
    }
}
