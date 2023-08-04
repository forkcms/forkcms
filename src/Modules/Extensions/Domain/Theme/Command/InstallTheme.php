<?php

namespace ForkCMS\Modules\Extensions\Domain\Theme\Command;

use ForkCMS\Modules\Extensions\Domain\Theme\InstallableTheme;

final class InstallTheme
{
    public function __construct(public readonly InstallableTheme $theme)
    {
    }
}
