<?php

namespace Backend\Modules\Location\Domain\Location\Command;

use ForkCMS\Utility\Module\CopyModuleContentToOtherLocale;

final class CopyLocationWidgetsToOtherLocale extends CopyModuleContentToOtherLocale
{
    public function getModuleName(): string
    {
        return 'Location';
    }
}
