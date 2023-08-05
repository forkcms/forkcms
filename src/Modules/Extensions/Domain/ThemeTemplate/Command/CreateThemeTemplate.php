<?php

namespace ForkCMS\Modules\Extensions\Domain\ThemeTemplate\Command;

use ForkCMS\Modules\Extensions\Domain\Theme\Theme;
use ForkCMS\Modules\Extensions\Domain\ThemeTemplate\ThemeTemplate;
use ForkCMS\Modules\Extensions\Domain\ThemeTemplate\ThemeTemplateDataTransferObject;

final class CreateThemeTemplate extends ThemeTemplateDataTransferObject
{
    public function __construct(Theme $theme)
    {
        parent::__construct();
        $this->theme = $theme;
    }

    public function setEntity(ThemeTemplate $themeTemplate): void
    {
        $this->themeTemplateEntity = $themeTemplate;
    }
}
