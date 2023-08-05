<?php

namespace ForkCMS\Modules\Extensions\Domain\Theme\Command;

use ForkCMS\Modules\Extensions\Domain\Theme\Theme;

final class ActivateTheme
{
    public function __construct(public readonly Theme $theme)
    {
    }
}
