<?php

namespace Backend\Modules\Pages\Command;

use Backend\Modules\Pages\Engine\Model as BackendPagesModel;
use Common\Locale;
use ForkCMS\Component\Module\CopyModuleToOtherLocaleCommand;

class CopyModuleToOtherLocale extends CopyModuleToOtherLocaleCommand
{
    public function copy(Locale $fromLocale, Locale $toLocale): bool
    {
        BackendPagesModel::copy((string) $fromLocale, (string) $toLocale);

        return true;
    }
}
