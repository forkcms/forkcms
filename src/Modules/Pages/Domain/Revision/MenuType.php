<?php

namespace ForkCMS\Modules\Pages\Domain\Revision;

use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

enum MenuType: string implements TranslatableInterface
{
    case MAIN = 'main';
    case META = 'meta';
    case FOOTER = 'footer';
    case ROOT = 'root';

    public function trans(TranslatorInterface $translator, string $locale = null): string
    {
        return $translator->trans('lbl.' . ucfirst($this->value), locale: $locale);
    }
}
