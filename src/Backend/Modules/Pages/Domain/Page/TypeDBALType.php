<?php

namespace Backend\Modules\Pages\Domain\Page;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

final class TypeDBALType extends StringType
{
    public function getName(): string
    {
        return 'pages_page_type';
    }

    public function convertToPHPValue($typeDBALType, AbstractPlatform $platform): ?Type
    {
        if ($typeDBALType === null) {
            return null;
        }

        return new Type($typeDBALType);
    }

    public function convertToDatabaseValue($typeDBALType, AbstractPlatform $platform): string
    {
        return $typeDBALType->getValue();
    }
}
