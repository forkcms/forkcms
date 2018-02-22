<?php

namespace App\Frontend\Modules\ContentBlocks\Widgets;

use App\Frontend\Core\Engine\Base\Widget as FrontendBaseWidget;
use App\Frontend\Core\Language\Locale;

/**
 * This is the detail widget.
 */
class Detail extends FrontendBaseWidget
{
    public function execute(): void
    {
        parent::execute();

        $contentBlock = $this->get('content_blocks.repository.content_block')->findOneByIdAndLocale(
            (int) $this->data['id'],
            Locale::frontendLanguage()
        );

        $this->template->assign('widgetContentBlocks', $contentBlock);
    }
}
