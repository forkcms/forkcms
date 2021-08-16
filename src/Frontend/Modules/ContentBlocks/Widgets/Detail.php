<?php

namespace Frontend\Modules\ContentBlocks\Widgets;

use Backend\Modules\ContentBlocks\Domain\ContentBlock\ContentBlockRepository;
use Frontend\Core\Engine\Base\Widget as FrontendBaseWidget;
use Frontend\Core\Language\Locale;

/**
 * This is the detail widget.
 */
class Detail extends FrontendBaseWidget
{
    public function execute(): void
    {
        parent::execute();

        $contentBlock = $this->get(ContentBlockRepository::class)->findOneByIdAndLocale(
            (int) $this->data['id'],
            Locale::frontendLanguage()
        );

        if ($contentBlock->isHidden()) {
            return;
        }

        $this->template->assign('widgetContentBlocks', $contentBlock);
    }
}
