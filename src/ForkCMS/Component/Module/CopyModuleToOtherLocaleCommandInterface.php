<?php

namespace ForkCMS\Component\Module;

use Common\Locale;

interface CopyModuleToOtherLocaleCommandInterface
{
    public function copy(Locale $fromLocale, Locale $toLocale): bool;
}
