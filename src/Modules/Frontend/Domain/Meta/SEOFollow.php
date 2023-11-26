<?php

namespace ForkCMS\Modules\Frontend\Domain\Meta;

use ForkCMS\Modules\Internationalisation\Domain\Translation\TranslationKey;

enum SEOFollow: string
{
    case NONE = 'none';
    case FOLLOW = 'follow';
    case NO_FOLLOW = 'nofollow';

    public function getLabel(): TranslationKey
    {
        return match ($this) {
            self::NONE => TranslationKey::label('None'),
            self::FOLLOW => TranslationKey::label('Follow'),
            self::NO_FOLLOW => TranslationKey::label('NoFollow'),
        };
    }
}
