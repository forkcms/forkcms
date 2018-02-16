<?php

namespace Backend\Modules\ContentBlocks\Domain\ContentBlock\Command;

use ForkCMS\Component\Module\CopyModuleToOtherLocale;

final class CopyContentBlocksToOtherLocale extends CopyModuleToOtherLocale
{
    public function getModuleName(): string
    {
        return 'ContentBlocks';
    }
}
