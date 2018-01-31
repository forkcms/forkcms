<?php

namespace Backend\Modules\Pages\Domain\Page\Command;

use ForkCMS\Component\Module\CopyModuleToOtherLocaleCommand;

class CopyPagesToOtherLocale extends CopyModuleToOtherLocaleCommand
{
    public function __construct()
    {
        $this->setPriority(50);
    }

    public function getModuleName(): string
    {
        return 'Pages';
    }
}
