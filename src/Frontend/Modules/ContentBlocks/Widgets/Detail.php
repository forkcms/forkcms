<?php

namespace Frontend\Modules\ContentBlocks\Widgets;

use Frontend\Core\Engine\Base\Widget as FrontendBaseWidget;
use App\Component\Locale\FrontendLocale;

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
            FrontendLocale::frontendLanguage()
        );

        $this->template->assign('widgetContentBlocks', $contentBlock);
    }
}
