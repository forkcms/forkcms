<?php

namespace ForkCMS\Modules\Extensions\Domain\Theme\Event;

use ForkCMS\Modules\Extensions\Domain\Theme\Theme;
use Symfony\Contracts\EventDispatcher\Event;

final class ThemeInstalledEvent extends Event
{
    public function __construct(public readonly Theme $theme)
    {
    }
}
