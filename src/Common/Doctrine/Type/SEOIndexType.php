<?php

namespace Common\Doctrine\Type;

use Common\Doctrine\ValueObject\SEOIndex;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

final class SEOIndexType extends StringType
{
    const SEO_INDEX = 'seo_index';

    /**
     * @param SEOIndex $seoIndex
     * @param AbstractPlatform $platform
     *
     * @return string
     */
    public function convertToDatabaseValue($seoIndex, AbstractPlatform $platform): string
    {
        return (string) $seoIndex;
    }

    public function convertToPHPValue($seoIndex, AbstractPlatform $platform): SEOIndex
    {
        return SEOIndex::fromString($seoIndex);
    }

    public function getName(): string
    {
        return self::SEO_INDEX;
    }
}
