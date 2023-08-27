<?php

namespace ForkCMS\Modules\Frontend\Domain\AjaxAction;

use ForkCMS\Core\Domain\Doctrine\ValueObjectDBALType;
use Stringable;

class AjaxActionSlugDBALType extends ValueObjectDBALType
{
    protected function fromString(string $value): Stringable
    {
        return AjaxActionSlug::fromSlug($value);
    }
}
