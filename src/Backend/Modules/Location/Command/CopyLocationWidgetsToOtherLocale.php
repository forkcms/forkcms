<?php

namespace Backend\Modules\Location\Command;

use ForkCMS\Component\Module\CopyModuleToOtherLocale;

final class CopyLocationWidgetsToOtherLocale extends CopyModuleToOtherLocale
{
    public function getModuleName(): string
    {
        return 'Location';
    }
}
