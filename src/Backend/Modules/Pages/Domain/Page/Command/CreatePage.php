<?php

namespace Backend\Modules\Pages\Domain\Page\Command;

use Backend\Modules\Pages\Domain\Page\PageDataTransferObject;
use Common\Locale;

final class CreatePage extends PageDataTransferObject
{
    public function __construct(Locale $locale)
    {
        $this->locale = $locale;
    }
}
