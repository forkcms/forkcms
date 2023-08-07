<?php

namespace ForkCMS\Modules\Frontend\Domain\Meta;

use ForkCMS\Core\Domain\Doctrine\ValueObjectDBALType;

final class SEOIndexDBALType extends ValueObjectDBALType
{
    public const NAME = 'modules__frontend__meta__seo_index';

    protected function fromString(string $value): SEOIndex
    {
        return SEOIndex::from($value);
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
