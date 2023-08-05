<?php

namespace ForkCMS\Modules\Internationalisation\Domain\Locale\Event;

use ForkCMS\Modules\Internationalisation\Domain\Locale\InstalledLocale;
use Symfony\Contracts\EventDispatcher\Event;

final class InstalledLocaleChangedEvent extends Event
{
    public function __construct(public readonly InstalledLocale $installedLocale)
    {
    }
}
