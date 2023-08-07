<?php

namespace ForkCMS\Modules\Frontend\Domain\Meta;

use ForkCMS\Core\Domain\Doctrine\ValueObjectDBALType;

final class SEOFollowDBALType extends ValueObjectDBALType
{
    public const NAME = 'modules__frontend__meta__seo_follow';

    protected function fromString(string $value): SEOFollow
    {
        return SEOFollow::from($value);
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
