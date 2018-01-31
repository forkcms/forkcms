<?php

namespace ForkCMS\Component\Module;

use Common\Locale;

abstract class CopyModuleToOtherLocaleCommand implements CopyModuleToOtherLocaleCommandInterface
{
    abstract public function copy(Locale $fromLocale, Locale $toLocale): bool;
}
