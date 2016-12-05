<?php

namespace Backend\Modules\ContentBlocks\Command;

use Backend\Modules\ContentBlocks\Entity\ContentBlock;

final class DeleteContentBlock
{
    /**
     * @var ContentBlock
     */
    public $contentBlock;

    /**
     * @param ContentBlock $contentBlock
     */
    public function __construct(ContentBlock $contentBlock)
    {
        $this->contentBlock = $contentBlock;
    }
}
