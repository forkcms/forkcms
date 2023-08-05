<?php

namespace ForkCMS\Modules\Extensions\Domain\ThemeTemplate\Command;

use ForkCMS\Modules\Extensions\Domain\ThemeTemplate\ThemeTemplate;
use ForkCMS\Modules\Extensions\Domain\ThemeTemplate\ThemeTemplateDataTransferObject;

final class ChangeThemeTemplate extends ThemeTemplateDataTransferObject
{
    public function __construct(ThemeTemplate $themeTemplate)
    {
        parent::__construct($themeTemplate);
    }
}
