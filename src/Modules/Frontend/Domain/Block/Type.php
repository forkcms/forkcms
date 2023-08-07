<?php

namespace ForkCMS\Modules\Frontend\Domain\Block;

use ForkCMS\Modules\Frontend\Domain\Action\ActionName;
use ForkCMS\Modules\Frontend\Domain\Widget\WidgetName;
use ForkCMS\Modules\Internationalisation\Domain\Translation\TranslationKey;
use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

enum Type: string implements TranslatableInterface
{
    case ACTION = 'action';
    case WIDGET = 'widget';

    public function getDirectoryName(): string
    {
        return match ($this) {
            self::ACTION => 'Actions',
            self::WIDGET => 'Widgets',
        };
    }

    public function trans(TranslatorInterface $translator, string $locale = null): string
    {
        return $this->getLabel()->trans($translator, $locale);
    }

    public function getLabel(): TranslationKey
    {
        return (match ($this) {
            self::ACTION => TranslationKey::label('Action'),
            self::WIDGET => TranslationKey::label('Widget'),
        });
    }

    public static function fromDirectoryName(string $directoryName): self
    {
        return match ($directoryName) {
            'Actions' => self::ACTION,
            'Widgets' => self::WIDGET,
        };
    }

    public function getBlockName(string $name): BlockName
    {
        return match ($this) {
            self::ACTION => ActionName::fromString($name),
            self::WIDGET => WidgetName::fromString($name),
        };
    }
}
