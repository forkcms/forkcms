<?php

namespace Frontend\Modules\ContentBlocks\Widgets;

use Backend\Modules\ContentBlocks\ContentBlock\ContentBlock;
use Backend\Modules\ContentBlocks\ContentBlock\ContentBlockRepository;
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

        /** @var ContentBlockRepository $contentBlockRepository */
        $contentBlockRepository = $this->get('doctrine.orm.entity_manager')->getRepository(ContentBlock::class);
        $contentBlock = $contentBlockRepository->findByIdAndLocale(
            (int) $this->data['id'],
            Locale::frontendLanguage()
        );

        // if the content block is not found or if it is hidden, just return an array with empty text
        // @deprecated fix this for version 5, we just shouldn't assign this instead of this hack, but we need it for BC
        if (!$contentBlock instanceof ContentBlock || $contentBlock->isHidden()) {
            $contentBlock = ['text' => ''];
        }

        $this->tpl->assign('widgetContentBlocks', $contentBlock);
        // That's all folks!
    }
}
