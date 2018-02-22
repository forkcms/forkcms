<?php

namespace App\Backend\Modules\ContentBlocks\Domain\ContentBlock\Command;

use App\Backend\Modules\ContentBlocks\Domain\ContentBlock\ContentBlock;

final class DeleteContentBlock
{
    /** @var ContentBlock */
    public $contentBlock;

    public function __construct(ContentBlock $contentBlock)
    {
        $this->contentBlock = $contentBlock;
    }
}
