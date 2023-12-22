<?php

namespace ForkCMS\Modules\Internationalisation\Domain\Translation;

use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

enum Type: string implements TranslatableInterface
{
    case MESSAGE = 'message';
    case LABEL = 'label';
    case SLUG = 'slug';
    case ERROR = 'error';

    public function trans(TranslatorInterface $translator, string $locale = null): string
    {
        return $translator->trans('lbl.TranslationType' . ucfirst($this->value), locale: $locale);
    }

    public function getAbbreviation(): string
    {
        return match ($this) {
            self::MESSAGE => 'msg',
            self::LABEL => 'lbl',
            self::SLUG => 'slg',
            self::ERROR => 'err',
        };
    }
}
