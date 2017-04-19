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

        // Init template
        $template = "Default.html.twig";

        // if the content block is not found or if it is hidden, just return an array with empty text
        // @deprecated fix this for version 5, we just shouldn't assign this instead of this hack, but we need it for BC
        if (!$contentBlock instanceof ContentBlock || $contentBlock->isHidden()) {
            $contentBlock = ['text' => ''];
        } else {
            $template = $contentBlock->getTemplate();
        }

        // Load template
        $this->loadTemplate('ContentBlocks/Layout/Widgets/' . $template);

        $this->tpl->assign('widgetContentBlocks', $contentBlock);
        // That's all folks!
    }
}
