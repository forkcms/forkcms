<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaItemTranslation\Exception;

use Common\Locale;

class MediaItemTranslationNotFound extends \Exception
{
    public static function forLocale(Locale $locale): \Exception
    {
        return new \Exception('The translation can\'t be found for "' . (string) $locale . '"');
    }
}
