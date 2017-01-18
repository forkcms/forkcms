<?php

namespace Frontend\Modules\ContentBlocks\Widgets;

use Backend\Modules\ContentBlocks\Entity\ContentBlock;
use Frontend\Core\Engine\Base\Widget as FrontendBaseWidget;
use Frontend\Core\Language\Locale;

/**
 * This is the detail widget.
 */
class Detail extends FrontendBaseWidget
{
    /**
     * Execute the extra
     */
    public function execute()
    {
        parent::execute();

        $contentBlock = $this->get('content_blocks.repository.content_block')->findOneByIdAndLocale(
            (int) $this->data['id'],
            Locale::frontendLanguage()
        );

        $this->tpl->assign('widgetContentBlocks', $contentBlock);
    }
}
