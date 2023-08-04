<?php

namespace ForkCMS\Modules\Internationalisation\Domain\Locale;

use Doctrine\ORM\Mapping as ORM;

trait EntityWithLocaleTrait
{
    #[ORM\Column(type: 'string', length: 5, enumType: Locale::class)]
    private Locale $locale;

    public function getLocale(): Locale
    {
        return $this->locale;
    }
}
