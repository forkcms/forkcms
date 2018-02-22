<?php

namespace App\Backend\Modules\ContentBlocks\Domain\ContentBlock\Command;

use App\Backend\Modules\ContentBlocks\Domain\ContentBlock\ContentBlock;
use App\Backend\Modules\ContentBlocks\Domain\ContentBlock\ContentBlockDataTransferObject;

final class UpdateContentBlock extends ContentBlockDataTransferObject
{
    public function __construct(ContentBlock $contentBlock)
    {
        parent::__construct($contentBlock);
    }

    public function setContentBlockEntity(ContentBlock $contentBlockEntity): void
    {
        $this->contentBlockEntity = $contentBlockEntity;
    }
}
