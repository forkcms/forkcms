<?php

namespace Backend\Modules\ContentBlocks\ContentBlock\Command;

use Backend\Modules\ContentBlocks\ContentBlock\ContentBlock;

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
