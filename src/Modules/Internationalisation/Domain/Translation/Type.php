<?php

namespace ForkCMS\Modules\Internationalisation\Domain\Translation;

use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

enum Type: string implements TranslatableInterface
{
    case msg = 'message';
    case lbl = 'label';
    case slg = 'slug';
    case err = 'error';

    public function trans(TranslatorInterface $translator, string $locale = null): string
    {
        return $translator->trans('lbl.TranslationType' . ucfirst($this->value), locale: $locale);
    }
}
