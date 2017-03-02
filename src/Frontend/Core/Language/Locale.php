<?php

namespace Frontend\Core\Language;

use Common\Locale as CommonLocale;

final class Locale extends CommonLocale
{
    /**
     * @return self
     */
    public static function frontendLanguage(): self
    {
        return new self(FRONTEND_LANGUAGE);
    }

    /**
     * {@inheritdoc}
     */
    protected function getPossibleLanguages(): array
    {
        return array_flip(Language::getActiveLanguages());
    }
}
