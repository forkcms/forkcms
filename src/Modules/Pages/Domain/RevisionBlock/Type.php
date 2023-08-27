<?php

namespace ForkCMS\Modules\Pages\Domain\RevisionBlock;

use ForkCMS\Modules\Frontend\Domain\Block\Type as BlockType;
use ForkCMS\Modules\Internationalisation\Domain\Translation\TranslationKey;

enum Type: string
{
    case EDITOR = 'editor';
    case ACTION = 'action';
    case WIDGET = 'widget';

    public function getLabel(): TranslationKey
    {
        return TranslationKey::label(ucfirst($this->value));
    }

    public static function fromBlockType(?BlockType $type): self
    {
        return self::tryFrom($type?->value) ?? self::EDITOR;
    }

    /** @return array<value-of<self>, self> */
    public static function formTypeChoices(): array
    {
        return array_combine(array_column(self::cases(), 'value'), self::cases());
    }

    public function isEditor(): bool
    {
        return $this === self::EDITOR;
    }
}
