<?php

namespace Backend\Modules\ContentBlocks\Domain\ContentBlock\Command;

use ForkCMS\Component\Module\CopyModuleToOtherLocaleCommand;

final class CopyContentBlocksToOtherLocale extends CopyModuleToOtherLocaleCommand
{
    public function getModuleName(): string
    {
        return 'ContentBlocks';
    }
}
