<?php

namespace Backend\Modules\ContentBlocks\Domain\ContentBlock\Command;

use ForkCMS\Utility\Module\CopyModuleContentToOtherLocale;

final class CopyContentBlocksToOtherLocale extends CopyModuleContentToOtherLocale
{
    public function getModuleName(): string
    {
        return 'ContentBlocks';
    }
}
