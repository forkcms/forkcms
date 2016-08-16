<?php

namespace Backend\Modules\ContentBlocks\Command;

use Backend\Modules\ContentBlocks\Entity\ContentBlock;

final class UpdateContentBlock extends ContentBlockCommand
{
    /**
     * @param ContentBlock $contentBlock
     */
    public function __construct(ContentBlock $contentBlock)
    {
        $this->contentBlock = $contentBlock;

        $this->isVisible = !$contentBlock->isHidden();
        $this->title = $contentBlock->getTitle();
        $this->text = $contentBlock->getText();
        $this->template = $contentBlock->getTemplate();
    }
}
