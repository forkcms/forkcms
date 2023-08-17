<?php

namespace ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\Command;

use ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\ContentBlock;
use ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\ContentBlockDataTransferObject;
use ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\Status;

final class CreateContentBlock extends ContentBlockDataTransferObject
{
    public function __construct()
    {
        parent::__construct();

        $this->status = Status::active();
    }

    public function setEntity(ContentBlock $contentBlock): void
    {
        $this->contentBlockEntity = $contentBlock;
    }
}
