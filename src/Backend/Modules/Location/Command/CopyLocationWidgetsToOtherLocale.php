<?php

namespace Backend\Modules\Location\Command;

use ForkCMS\Component\Module\CopyModuleToOtherLocaleCommand;

final class CopyLocationWidgetsToOtherLocale extends CopyModuleToOtherLocaleCommand
{
    public function getModuleName(): string
    {
        return 'Location';
    }
}
