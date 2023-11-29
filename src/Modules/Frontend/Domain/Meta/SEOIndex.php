<?php

namespace ForkCMS\Modules\Frontend\Domain\Meta;

use ForkCMS\Modules\Internationalisation\Domain\Translation\TranslationKey;

enum SEOIndex: string
{
    case NONE = 'none';
    case INDEX = 'index';
    case NO_INDEX = 'noindex';
    public function getLabel(): TranslationKey
    {
        return match ($this) {
            self::NONE => TranslationKey::label('NotSpecified'),
            self::INDEX => TranslationKey::label('Index'),
            self::NO_INDEX => TranslationKey::label('DoNotIndex'),
        };
    }
}
