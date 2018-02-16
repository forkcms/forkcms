<?php

namespace Backend\Modules\Pages\Domain\Page\Command;

use ForkCMS\Component\Module\CopyModuleToOtherLocale;

class CopyPagesToOtherLocale extends CopyModuleToOtherLocale
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
