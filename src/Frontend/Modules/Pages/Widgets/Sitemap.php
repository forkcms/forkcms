<?php

namespace ForkCMS\Frontend\Modules\Pages\Widgets;

use ForkCMS\Frontend\Core\Engine\Base\Widget as FrontendBaseWidget;

/**
 * This is a widget wherein the sitemap lives
 */
class Sitemap extends FrontendBaseWidget
{
    public function execute(): void
    {
        parent::execute();
        $this->loadTemplate();
    }
}
