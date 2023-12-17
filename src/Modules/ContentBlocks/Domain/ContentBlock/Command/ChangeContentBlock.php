<?php

namespace ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\Command;

use ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\ContentBlock;
use ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\ContentBlockDataTransferObject;

final class ChangeContentBlock extends ContentBlockDataTransferObject
{
    public function __construct(ContentBlock $contentBlock)
    {
        parent::__construct($contentBlock);
    }
}
