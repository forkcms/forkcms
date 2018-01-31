<?php

namespace ForkCMS\Component\Module;

use Common\Locale;

final class CopyModulesToOtherLocale
{
    /**
     * @var array
     */
    protected $modules = [];

    public function addModule(CopyModuleToOtherLocaleCommandInterface $command): void
    {
        $this->modules[] = $command;
    }

    public function copy(Locale $fromLocale, Locale $toLocale): void
    {
        /**
         * @var CopyModuleToOtherLocaleCommandInterface $module
         */
        foreach ($this->modules as $module) {
            $module->copy($fromLocale, $toLocale);
        }
    }
}
