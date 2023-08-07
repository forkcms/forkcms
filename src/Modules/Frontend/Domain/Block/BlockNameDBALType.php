<?php

namespace ForkCMS\Modules\Frontend\Domain\Block;

use BackedEnum;
use ForkCMS\Core\Domain\Doctrine\ValueObjectDBALType;
use ForkCMS\Modules\Frontend\Domain\Action\ActionName;
use ForkCMS\Modules\Frontend\Domain\Widget\WidgetName;
use InvalidArgumentException;
use Stringable;

class BlockNameDBALType extends ValueObjectDBALType
{
    public const ACTION_PREFIX = 'action__';
    public const WIDGET_PREFIX = 'widget__';

    protected function fromString(string $value): Stringable
    {
        [$prefix, $value] = mb_str_split($value, 8);

        return match ($prefix) {
            self::ACTION_PREFIX => ActionName::fromString($value),
            self::WIDGET_PREFIX => WidgetName::fromString($value),
            default => throw new InvalidArgumentException('Type not supported'),
        };
    }

    protected function toString(BackedEnum|Stringable $value): string
    {
        if (!$value instanceof BlockName) {
            throw new InvalidArgumentException('Type not supported');
        }

        return self::prefixedString($value);
    }

    public static function prefixedString(BlockName $blockName): string
    {
        return match ($blockName->getType()) {
            Type::ACTION => self::ACTION_PREFIX,
            Type::WIDGET => self::WIDGET_PREFIX,
        } . $blockName->getName();
    }
}
