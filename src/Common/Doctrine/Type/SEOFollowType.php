<?php

namespace Common\Doctrine\Type;

use Common\Doctrine\ValueObject\SEOFollow;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

final class SEOFollowType extends StringType
{
    const SEO_FOLLOW = 'seo_follow';

    /**
     * @param SEOFollow $seoFollow
     * @param AbstractPlatform $platform
     *
     * @return string
     */
    public function convertToDatabaseValue($seoFollow, AbstractPlatform $platform): ?string
    {
        if ($seoFollow === null) {
            return null;
        }

        return (string) $seoFollow;
    }

    public function convertToPHPValue($seoFollow, AbstractPlatform $platform): ?SEOFollow
    {
        if ($seoFollow === null) {
            return null;
        }

        return SEOFollow::fromString($seoFollow);
    }

    public function getName(): string
    {
        return self::SEO_FOLLOW;
    }
}
