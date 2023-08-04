<?php

namespace Backend\Modules\Pages\Domain\Page\Command;

use ForkCMS\Utility\Module\CopyContentToOtherLocale\CopyModuleContentToOtherLocale;

final class CopyPagesToOtherLocale extends CopyModuleContentToOtherLocale
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
